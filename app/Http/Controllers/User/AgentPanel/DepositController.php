<?php

namespace App\Http\Controllers\User\AgentPanel;

use App\Http\Controllers\Controller;
use App\Models\AgentGateway;
use App\Models\Deposit;
use App\Models\GeneralSetting;
use App\Models\Trx;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class DepositController extends Controller
{

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('check.agent.banned', only: ['approve', 'reject']),
            new Middleware('check.agent.status'),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->agent) {
            return abort(404);
        }

        $page_title = 'Agent Panel Deposit List';

        $user = Auth::user();

        $empty_message = 'No deposit history available.';
        $deposits = Deposit::where('status', '!=', 0)
            ->where('agent_id', $user->agent->id)
            ->whereBetween('created_at', [Carbon::now()->subMonth(1), Carbon::now()])
            ->with(['user', 'gateway'])
            ->latest()
            ->paginate(config('constants.table.default'));

        return view(activeTemplate() . 'user.agent-panel.deposits.index', compact('page_title', 'deposits', 'empty_message'));
    }


    public function search(Request $request, $scope)
    {
        $search = $request->q;
        if (empty($search)) return back();
        $page_title = '';
        $empty_message = 'No search result was found.';

        $deposits = Deposit::where('agent_id', Auth::user()->agent->id)
            ->with(['user', 'gateway'])
            ->where(function ($q) use ($search) {
                $q->where('trx', $search)->orWhereHas('user', function ($user) use ($search) {
                    $user->where('username', $search)
                        ->orWhere('trx', $search)
                        ->orWhereJsonContains('detail', ['transaction-id' => $search]);
                });
            });
        switch ($scope) {
            case 'pending':
                $page_title .= 'Pending Deposits Search';
                $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 2);
                break;
            case 'approved':
                $page_title .= 'Approved Deposits Search';
                $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 1);
                break;
            case 'rejected':
                $page_title .= 'Rejected Deposits Search';
                $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 3);
                break;
            case 'list':
                $page_title .= 'Deposits History Search';
                break;
        }
        $deposits = $deposits->paginate(config('constants.table.default'));
        $page_title .= ' - ' . $search;

        return view(activeTemplate() . 'user.agent-panel.deposits.index', compact('page_title', 'search', 'scope', 'empty_message', 'deposits'));
    }

    public function filter(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|required_with:end_date',
            'end_date' => 'nullable|date|required_with:start_date|after_or_equal:start_date',
            'status' => 'nullable|in:list,approved,pending,rejected'
        ]);

        $status = $request->status;
        $from = $request->start_date;
        $to = $request->end_date;

        if (empty($status) && empty($from) && empty($to)) {
            return redirect()->back();
        }

        $page_title = '';
        $empty_message = 'No search result was found.';

        $deposits = Deposit::where('agent_id', Auth::user()->agent->id)
            ->with(['user', 'gateway']);

        switch ($request->status) {
            case 'pending':
                $page_title .= 'Pending Deposit Filter';
                $deposits = $deposits->where('status', 2);
                break;
            case 'approved':
                $page_title .= 'Approved Deposit Filter';
                $deposits = $deposits->where('status', 1);
                break;
            case 'rejected':
                $page_title .= 'Rejected Deposit Filter';
                $deposits = $deposits->where('status', 3);
                break;
            case 'list':
                $deposits = $deposits->where('status', '!=', 0);
                $page_title .= 'All Withdrawal Filter';
                break;
        }

        if ($from && $to) {
            $startDate = Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();

            $deposits = $deposits->whereBetween('created_at', [$startDate, $endDate]);
            $page_title .= ' - ' . $from . ' to ' . $to;
        }

        $total_deposits = $deposits->count();
        $total_receivable_amount = $deposits->sum('amount');
        // $total_deposits_charge = $deposits->sum('charge');
        // $total_receivable_amount = $total_deposits_amount-$total_deposits_charge;

        $deposits = $deposits->paginate(config('constants.table.default'));

        return view(activeTemplate() . 'user.agent-panel.deposits.index', compact('page_title', 'empty_message', 'total_deposits', 'total_receivable_amount', 'deposits'));
    }

    public function pending()
    {
        $page_title = 'Pending Deposits';
        $empty_message = 'No pending deposits.';
        $deposits = Deposit::where('agent_id', Auth::user()->agent->id)->where('method_code', '>=', 1000)->where('status', 2)->with(['user', 'gateway'])->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.agent-panel.deposits.index', compact('page_title', 'empty_message', 'deposits'));
    }

    public function approved()
    {
        $page_title = 'Approved Deposits';
        $empty_message = 'No approved deposits.';
        $deposits = Deposit::where('agent_id', Auth::user()->agent->id)->where('method_code', '>=', 1000)->where('status', 1)->with(['user', 'gateway'])->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.agent-panel.deposits.index', compact('page_title', 'empty_message', 'deposits'));
    }

    public function rejected()
    {
        $page_title = 'Rejected Deposits';
        $empty_message = 'No rejected deposits.';
        $deposits = Deposit::where('agent_id', Auth::user()->agent->id)->where('method_code', '>=', 1000)->where('status', 3)->with(['user', 'gateway'])->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.agent-panel.deposits.index', compact('page_title', 'empty_message', 'deposits'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|string']);

        try {
            $decrypted = Crypt::decryptString($request->id);
        } catch (DecryptException $e) {
            return abort(404, "page not found");
        }

        $deposit = Deposit::where('agent_id', Auth::user()->agent->id)->where('method_code', '>=', 1000)->findOrFail($decrypted);

        if ($deposit->status == 1) {
            $notify[] = ['success', 'Deposit has been approved.'];
            return back()->withNotify($notify);
        }

        $agent = $deposit->agent;
        $wallet = $deposit->wallet;

        $agentUser = $agent->user;
        $agentUser->balance->update([
            $wallet => $agentUser->balance->$wallet - $deposit->amount
        ]);
        $agentUser->save();

        $user = User::find($deposit->user_id);
        $user->balance->update([
            $wallet => $user->balance->$wallet + $deposit->amount
        ]);
        $user->save();

        $deposit->update(['status' => 1]);

        $parent_trx = Trx::create([
            'trx' => getTrx(),
            'user_id' => null,
            'agent_id' => $agent->id,
            'wallet' => $wallet,
            'symbol' => '-',
            'amount' => $deposit->amount,
            'main_amo' => totalUserBalance($agentUser->id),
            'balance' => $agentUser->balance->$wallet,
            'title' => 'Deposited via ' . $deposit->gateway->name . ' in <b>' . Str::headline($wallet) . '</b> to <b>' . $user->username . '</b>',
            'charge' => $deposit->charge,
            'type' => 22, // 2 = Deposit;
        ]);

        $deposit->user->transactions()->save(new Trx([
            'parent_id' => $parent_trx->id,
            'wallet' => $wallet,
            'symbol' => '+',
            'amount' => $deposit->amount,
            'agent_id' => null,
            'main_amo' => totalUserBalance($user->id),
            'charge' => $deposit->charge,
            'type' => 2, // 2 = Deposit;
            'title' => 'Deposited via ' . $deposit->gateway->name . ' in <b>' . Str::headline($wallet) . '</b> by agent (<b>' . $agent->code . '</b>)',
            'trx' => $deposit->trx,
            'balance' => $user->balance->$wallet,
        ]));

        $general = GeneralSetting::first(['cur_sym', 'cur_text', 'agent_deposit_commission']);

        $commission = (($deposit->amount * $general->agent_deposit_commission) / 100);

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
                'parent_id' => $parent_trx->id,
                'trx' => getTrx(),
                'user_id' => null,
                'agent_id' => $agent->id,
                'type' => 24, // 24 = Agent deposit commission Charge;
                'title' => 'Balance deposit commission ' . $general->cur_sym . formatter_money($commission) . ' Form (<b>' . $deposit->user->username . '</b>)',
                'wallet' => 'office_wallet',
                'symbol' => '+',
                'amount' => $commission,
                'main_amo' => totalUserBalance($agentUser->id),
                'balance' => $agentUser->balance->office_wallet,
                'charge' => 0,
            ]);
        }

        if ($deposit->amount > 0) {
            send_email($deposit->user, 'DEPOSIT_APPROVE', [
                'trx' => $deposit->trx,
                'amount' => $general->cur_sym . formatter_money($deposit->amount),
                'receive_amount' => $general->cur_sym . formatter_money($deposit->amount),
                'charge' => $general->cur_sym . formatter_money($deposit->charge),
                'method' => $deposit->gateway->name,
            ]);

            send_sms($deposit->user, 'DEPOSIT_APPROVE', [
                'trx' => $deposit->trx,
                'amount' => $general->cur_text . formatter_money($deposit->amount),
                'receive_amount' => $general->cur_text . formatter_money($deposit->amount),
                'charge' => $general->cur_text . formatter_money($deposit->charge),
                'method' => $deposit->gateway->name,
            ]);
        }

        $notify[] = ['success', 'Deposit has been approved.'];
        return back()->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'message' => 'nullable|string'
        ]);

        try {
            $decrypted = Crypt::decryptString($request->id);
        } catch (DecryptException $e) {
            return abort(404, "page not found");
        }

        $deposit = Deposit::where('agent_id', Auth::user()->agent->id)->where('method_code', '>=', 1000)->findOrFail($decrypted);

        $deposit->update(['status' => 3]);

        $user = User::find($deposit->user_id);

        $general = GeneralSetting::first(['cur_sym', 'cur_text']);

        $agent = $deposit->agent;
        $agentUser = $agent->user;
        Trx::create([
            'trx' => getTrx(),
            'user_id' => null,
            'agent_id' => $agent->id,
            'title' => 'Deposited via ' . $deposit->gateway->name . ' in <b>Office Wallet</b> to <b>' . $user->username . '</b> has <b style="color: red;">cancel</b>',
            'amount' => $deposit->amount,
            'main_amo' => totalUserBalance($agentUser->id),
            'balance' => $agentUser->balance->office_wallet,
            'charge' => $deposit->charge,
            'type' => 23, //  23 = Agent Deposit cancel;
        ]);

        $title = 'Deposit rejected Via  ' . $deposit->gateway->name;

        if ($request->message) {
            $title .= '<br>Message: ' . $request->message;
        }

        $deposit->user->transactions()->save(new Trx([
            'amount' => $deposit->amount,
            'main_amo' => totalUserBalance($user->id),
            'charge' => 0,
            'type' => 23, // 2 = Deposit;
            'title' => $title,
            'trx' => $deposit->trx,
            'balance' => $user->balance->self_wallet,
        ]));

        if ($deposit->amount > 0) {
            send_email($deposit->user, 'DEPOSIT_REJECT', [
                'trx' => $deposit->trx,
                'amount' => $general->cur_text . formatter_money($deposit->amount),
                'method' => $deposit->gateway->name,
            ]);

            send_sms($deposit->user, 'DEPOSIT_REJECT', [
                'trx' => $deposit->trx,
                'amount' => $general->cur_text . formatter_money($deposit->amount),
                'method' => $deposit->gateway->name,
            ]);
        }

        $notify[] = ['success', 'Deposit has been rejected.'];
        return back()->withNotify($notify);
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
