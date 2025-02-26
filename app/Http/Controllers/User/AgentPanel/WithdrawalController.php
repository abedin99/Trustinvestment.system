<?php

namespace App\Http\Controllers\User\AgentPanel;

use Carbon\Carbon;
use App\Models\Trx;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\UserBalance;
use App\Models\AgentGateway;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('check.agent.banned', ['only' => ['approve', 'reject', 'makeHold']]);
        $this->middleware('check.agent.status');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->agent) {
            return abort(404);
        }

        $page_title = 'Agent Panel Withdrawal List';


        $empty_message = 'No withdrawal history available.';
        $withdrawals = Withdrawal::where('status', '!=', 0)->where('agent_id', $user->agent->id)
            ->whereBetween('created_at', [Carbon::now()->subMonth(1), Carbon::now()])
            ->with(['user'])->latest()->paginate(config('constants.table.default'));

        return view(activeTemplate() . 'user.agent-panel.withdrawal.index', compact('page_title', 'withdrawals', 'empty_message'));
    }


    public function pending()
    {
        $page_title = 'Pending Withdrawals';
        $empty_message = 'No pending withdrawals.';
        $withdrawals = Withdrawal::where('agent_id', Auth::user()->agent->id)->latest()->where('status', 2)->with('user')->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.agent-panel.withdrawal.index', compact('page_title', 'empty_message', 'withdrawals'));
    }

    public function approved()
    {
        $page_title = 'Approved Withdrawals';
        $empty_message = 'No approved withdrawals.';
        $withdrawals = Withdrawal::where('agent_id', Auth::user()->agent->id)->latest()->where('status', 1)->with('user')->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.agent-panel.withdrawal.index', compact('page_title', 'empty_message', 'withdrawals'));
    }

    public function rejected()
    {
        $page_title = 'Rejected Withdrawals';
        $empty_message = 'No rejected withdrawals.';
        $withdrawals = Withdrawal::where('agent_id', Auth::user()->agent->id)->latest()->where('status', 3)->with('user')->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.agent-panel.withdrawal.index', compact('page_title', 'empty_message', 'withdrawals'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $withdraw = Withdrawal::findOrFail($request->id);

        $user = User::find($withdraw->user_id);

        if ($withdraw->status == 1) {
            $notify[] = ['success', 'Deposit has been approved.'];
            return back()->withNotify($notify);
        }

        $withdraw->update(['status' => 1]);

        $agent = $withdraw->agent;

        $agentUser = $agent->user;
        $charge = $withdraw->charge;
        $payable_amount = $withdraw->amount - $charge;


        // Deposit charge of admin with dollar rate
        $method_name = $withdraw->method->name;
        $gate = GatewayCurrency::where('name', 'LIKE', "%{$method_name}%")->where('currency', $withdraw->currency)->first();

        $final_payable_amount = $payable_amount * $withdraw->rate;
        $charge = formatter_money($gate->fixed_charge + ($payable_amount * $gate->percent_charge / 100));

        $payable_currency_amount = ($final_payable_amount - ($charge * $gate->rate));
        $final_agent_amount = $payable_currency_amount / $gate->rate;
        // End deposit charge of admin with dollar rate 


        $agentUser->balance->update([
            'office_wallet' => $agentUser->balance->office_wallet + $final_agent_amount
        ]);
        $agentUser->save();

        $general = GeneralSetting::first(['cur_sym', 'cur_text', 'agent_withdrawal_commission']);

        if ($final_agent_amount > 0) {
            Trx::create([
                'trx' => getTrx(),
                'user_id' => null,
                'agent_id' => $withdraw->agent_id,
                'title' => 'Withdrawal accepted by ' . $withdraw->method->name . ' to <b>' . str_replace('_', ' ', $withdraw->wallet) . '</b> from <b>' . $withdraw->user->username . '</b>',
                'amount' => $final_agent_amount,
                'main_amo' => totalUserBalance($agentUser->id),
                'balance' => $agentUser->balance->office_wallet,
                'charge' => $charge,
                'type' => 25, // 25 = Withdrawal via Agent;
            ]);
        }

        $commission = (($payable_amount * $general->agent_withdrawal_commission) / 100);

        $agentUser->balance->update([
            'office_wallet' => $agentUser->balance->office_wallet + $commission
        ]);
        $agentUser->save();

        if ($commission > 0) {
            $today_earning = $agentUser->user_statistic->today_earning + $commission;
            $last_month_earning = $agentUser->user_statistic->last_month_earning + $commission;
            $total_earning = $agentUser->user_statistic->total_earning + $commission;

            $statistic = $agentUser->user_statistic;

            $statistic->update([
                'today_earning' => $today_earning,
                'last_month_earning' => $last_month_earning,
                'total_earning' => $total_earning
            ]);

            Trx::create([
                'trx' => getTrx(),
                'user_id' => null,
                'agent_id' => $agent->id,
                'type' => 26, // 26 = Agent withdrawal commission Charge;
                'title' => 'Balance withdrawal commission ' . $general->cur_sym . formatter_money($commission) . ' Form (<b>' . $withdraw->user->username . '</b>)',
                'amount' => $commission,
                'main_amo' => totalUserBalance($agentUser->id),
                'balance' => $agentUser->balance->office_wallet,
                'charge' => 0,
            ]);
        }

        if ($final_agent_amount > 0) {
            send_email($withdraw->user, 'WITHDRAW_APPROVE', [
                'name' => $user->username,
                'trx' => $withdraw->trx,
                'amount' => $general->cur_text . formatter_money($withdraw->amount),
                'bdt_amount' => formatter_money($withdraw->final_amo),
                'receive_amount' => $general->cur_sym . formatter_money($withdraw->amount - $withdraw->charge),
                'charge' => $general->cur_sym . formatter_money($withdraw->charge),
                'method' => $withdraw->method->name,
            ]);

            send_sms($withdraw->user, 'WITHDRAW_APPROVE', [
                'name' => $user->username,
                'trx' => $withdraw->trx,
                'amount' => $general->cur_text . formatter_money($withdraw->amount),
                'bdt_amount' => formatter_money($withdraw->final_amo),
                'receive_amount' => $general->cur_sym . formatter_money($withdraw->amount - $withdraw->charge),
                'charge' => $general->cur_sym . formatter_money($withdraw->charge),
                'method' => $withdraw->method->name,
            ]);
        }

        $notify[] = ['success', 'Withdrawal has been approved.'];
        return redirect()->route('user.withdrawal.pending')->withNotify($notify);
    }


    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'message' => 'nullable|string'
        ]);
        $withdraw = Withdrawal::findOrFail($request->id);

        $user = User::find($withdraw->user_id);

        $balance = UserBalance::where('user_id', $user->id)->first();
        $balance->{$withdraw->wallet} = $user->balance->{$withdraw->wallet} + $withdraw->amount;
        $balance->saveQuietly();

        $withdraw->update(['status' => 3]);

        $title = 'Withdraw rejected Via  ' . $withdraw->method->name;

        if ($request->message) {
            $title .= '<br>Message: ' . $request->message;
        }

        $agent = $withdraw->agent;
        $agentUser = $agent->user;

        Trx::create([
            'trx' => getTrx(),
            'user_id' => null,
            'agent_id' => $agent->id,
            'title' => $title,
            'amount' => $withdraw->amount,
            'main_amo' => totalUserBalance($agentUser->id),
            'balance' => $agentUser->balance->office_wallet,
            'charge' => 0,
            'type' => 27, //  23 = Withdrawal via Agent rejected;
        ]);

        $withdraw->user->transactions()->save(new Trx([
            'amount' => $withdraw->amount,
            'main_amo' => totalUserBalance($user->id),
            'charge' => 0,
            'type' => 27, // 5=Withdrawal via Agent rejected;
            'title' => $title,
            'trx' => getTrx(),
            'balance' => $user->balance->income_balance,
        ]));


        $general = GeneralSetting::first(['cur_sym', 'cur_text']);

        if ($withdraw->amount > 0) {
            send_email($withdraw->user, 'WITHDRAW_REJECT', [
                'trx' => $withdraw->trx,
                'amount' => $general->cur_sym . formatter_money($withdraw->amount),
                'method' => $withdraw->method->name,
            ]);

            send_sms($withdraw->user, 'WITHDRAW_REJECT', [
                'trx' => $withdraw->trx,
                'amount' => $general->cur_text . formatter_money($withdraw->amount),
                'method' => $withdraw->method->name,
            ]);
        }

        $notify[] = ['success', 'Withdrawal has been rejected.'];
        return redirect()->route('user.withdrawal.pending')->withNotify($notify);
    }


    public function makeHold(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'message' => 'nullable|string'
        ]);
        $withdraw = Withdrawal::findOrFail($request->id);

        $user = User::find($withdraw->user_id);

        // $user->balance->update([
        //     $withdraw->wallet => $user->balance->{$withdraw->wallet} + $withdraw->amount,
        // ]);
        // $user->save();

        $withdraw->update(['status' => 4]);

        // $title = 'Withdraw Hold Via  ' . $withdraw->method->name;

        // if ($request->message) {
        //     $title .= '<br>Message: '.$request->message;
        // }

        // $withdraw->user->transactions()->save(new Trx([
        //     'amount' => $withdraw->amount,
        //     'main_amo' => totalUserBalance($user->id),
        //     'charge' => 0,
        //     'type' => 5, // 5=Withdraw;
        //     'title' => $title,
        //     'trx' => getTrx(),
        //     'balance' => $user->balance->income_balance,
        // ]));


        $general = GeneralSetting::first(['cur_sym', 'cur_text']);

        if ($withdraw->amount > 0) {
            send_email($withdraw->user, 'WITHDRAW_HOLD', [
                'trx' => $withdraw->trx,
                'amount' => $general->cur_sym . formatter_money($withdraw->amount),
                'method' => $withdraw->method->name,
            ]);

            send_sms($withdraw->user, 'WITHDRAW_HOLD', [
                'trx' => $withdraw->trx,
                'amount' => $general->cur_text . formatter_money($withdraw->amount),
                'method' => $withdraw->method->name,
            ]);
        }

        $notify[] = ['success', 'Withdrawal has been hold.'];
        return redirect()->back()->withNotify($notify);
    }

    public function search(Request $request, $scope)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'start_date' => 'nullable|date|required_with:end_date',
            'end_date' => 'nullable|date|required_with:start_date|after_or_equal:start_date',
            // 'status' => 'nullable|in:log,approved,pending,rejected,hold'
        ]);

        $search = $request->search;
        $from = $request->start_date;
        $to = $request->end_date;
        $status = $request->status;

        if (empty($search) && empty($from) && empty($to)) {
            return redirect()->route('admin.withdraw.log');
        }

        $page_title = '';
        $empty_message = 'No search result found.';

        $withdrawals = Withdrawal::with(['user', 'method']);

        // if($status){
        //     $scope = $status;
        // }

        switch ($scope) {
            case 'pending':
                $page_title .= 'Pending Withdrawal Search';
                $withdrawals = $withdrawals->where('status', 2);
                break;
            case 'approved':
                $page_title .= 'Approved Withdrawal Search';
                $withdrawals = $withdrawals->where('status', 1);
                break;
            case 'rejected':
                $page_title .= 'Rejected Withdrawal Search';
                $withdrawals = $withdrawals->where('status', 3);
                break;
            case 'log':
                $page_title .= 'Withdrawal History Search';
                break;
            default:
                $page_title .= 'Withdrawal History Search';
                break;
        }

        if ($from && $to) {
            $startDate = Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();

            $withdrawals = $withdrawals->whereBetween('created_at', [$startDate, $endDate]);
            $page_title .= ' - ' . $from . ' to ' . $to;
        }

        if ($search) {
            $withdrawals = $withdrawals->where(function ($q) use ($search) {
                $q->where('trx', $search)->orWhereHas('user', function ($user) use ($search) {
                    $user->where('username', $search);
                });
            });
            $page_title .= ' - ' . $search;
        }


        $total_withdrawals = $withdrawals->count();
        $total_amount = $withdrawals->sum('amount');
        $total_charge = $withdrawals->sum('charge');
        $total_withdrawals_amount = $total_amount - $total_charge;

        $withdrawals = $withdrawals->paginate(config('constants.table.default'));


        return view(activeTemplate() . 'user.agent-panel.withdrawal.index', compact('page_title', 'empty_message', 'search', 'scope', 'withdrawals', 'total_withdrawals', 'total_withdrawals_amount'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'gateway' => 'required|max:191',
            'gateway.*' => 'nullable|max:191',
        ]);

        $hasGateway = AgentGateway::where(['agent_id' => Auth::user()->agent->id])->exists();
        if ($hasGateway) {
            AgentGateway::where(['agent_id' => Auth::user()->agent->id])->delete();
        }

        foreach ($request->gateway as $key => $gateway) {
            if (!empty($gateway)) {
                $agentGateways = new AgentGateway;
                $agentGateways->agent_id         = Auth::user()->agent->id;
                $agentGateways->gateway_code     = $key;
                $agentGateways->account_number   = $gateway;
                $agentGateways->status           = true;
                $agentGateways->save();
            }
        }

        $notify[] = ['success', 'Agent Gateway has been updated!'];
        return back()->withNotify($notify);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return abort(404);
    }
}
