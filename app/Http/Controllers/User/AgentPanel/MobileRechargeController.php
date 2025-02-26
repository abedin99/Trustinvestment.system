<?php

namespace App\Http\Controllers\User\AgentPanel;

use Carbon\Carbon;
use App\Models\Trx;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\MobileRecharge;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MobileRechargeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('check.agent.banned', ['only' => ['approve', 'reject']]);
        $this->middleware('check.agent.status');
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

        $page_title = 'Agent Panel Mobile Recharge';

        $user = Auth::user();

        $empty_message = 'No mobile recharge history available.';

        $topUps = MobileRecharge::where('status', '!=', 0)
            ->where('agent_id', $user->agent->id)
            ->whereBetween('created_at', [Carbon::now()->subMonth(1), Carbon::now()])
            ->with(['user', 'method'])
            ->latest()
            ->paginate(config('constants.table.default'));

        return view(activeTemplate() . 'user.agent-panel.mobile-recharge.index', compact('page_title', 'empty_message', 'topUps'));
    }


    public function search(Request $request, $scope)
    {
        $search = $request->q;
        if (empty($search)) return back();
        $page_title = '';
        $empty_message = 'No search result was found.';

        $deposits = MobileRecharge::where('agent_id', Auth::user()->agent->id)
            ->with(['user', 'method'])
            ->where(function ($q) use ($search) {
                $q->where('trx', $search)->orWhereHas('user', function ($user) use ($search) {
                    $user->where('username', $search)
                        ->orWhere('trx', $search)
                        ->orWhereJsonContains('detail', ['transaction-id' => $search]);
                });
            });

        switch ($scope) {
            case 'pending':
                $page_title .= 'Pending Mobile Recharge Search';
                $deposits = $deposits->where('status', 2);
                break;
            case 'approved':
                $page_title .= 'Approved Mobile Recharge Search';
                $deposits = $deposits->where('status', 1);
                break;
            case 'rejected':
                $page_title .= 'Rejected Mobile Recharge Search';
                $deposits = $deposits->where('status', 3);
                break;
            case 'list':
                $page_title .= 'Mobile Recharge History Search';
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

        $topUps = MobileRecharge::where('agent_id', Auth::user()->agent->id)
            ->with(['user', 'method']);

        switch ($request->status) {
            case 'pending':
                $page_title .= 'Pending MobileRecharge Filter';
                $topUps = $topUps->where('status', 2);
                break;
            case 'approved':
                $page_title .= 'Approved MobileRecharge Filter';
                $topUps = $topUps->where('status', 1);
                break;
            case 'rejected':
                $page_title .= 'Rejected MobileRecharge Filter';
                $topUps = $topUps->where('status', 3);
                break;
            case 'list':
                $topUps = $topUps->where('status', '!=', 0);
                $page_title .= 'All Withdrawal Filter';
                break;
        }

        if ($from && $to) {
            $startDate = Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();

            $topUps = $topUps->whereBetween('created_at', [$startDate, $endDate]);
            $page_title .= ' - ' . $from . ' to ' . $to;
        }

        $total_topUps = $topUps->count();
        $total_receivable_amount = $topUps->sum('amount');

        $topUps = $topUps->paginate(config('constants.table.default'));

        return view(activeTemplate() . 'user.agent-panel.mobile-recharge.index', compact('page_title', 'empty_message', 'total_deposits', 'total_receivable_amount', 'topUps'));
    }

    public function pending()
    {
        $page_title = 'Pending Mobile Recharge';
        $empty_message = 'No pending mobile recharge.';
        $topUps = MobileRecharge::where('agent_id', Auth::user()->agent->id)->where('status', 2)->with(['user', 'method'])->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.agent-panel.mobile-recharge.index', compact('page_title', 'empty_message', 'topUps'));
    }

    public function approved()
    {
        $page_title = 'Approved Mobile Recharge';
        $empty_message = 'No approved mobile recharge.';
        $topUps = MobileRecharge::where('agent_id', Auth::user()->agent->id)->where('status', 1)->with(['user', 'method'])->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.agent-panel.mobile-recharge.index', compact('page_title', 'empty_message', 'topUps'));
    }

    public function rejected()
    {
        $page_title = 'Rejected Mobile Recharge';
        $empty_message = 'No rejected mobile recharge.';
        $topUps = MobileRecharge::where('agent_id', Auth::user()->agent->id)->where('status', 3)->with(['user', 'method'])->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.agent-panel.mobile-recharge.index', compact('page_title', 'empty_message', 'topUps'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|string']);

        try {
            $request['id'] = Crypt::decryptString($request->id);
        } catch (DecryptException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }

        $topUp = MobileRecharge::where('agent_id', Auth::user()->agent->id)->findOrFail($request->id);

        if ($topUp->status == 1) {
            $notify[] = ['warning', 'Mobile Recharge has been already approved.'];
            return back()->withNotify($notify);
        }

        $agent = $topUp->agent;
        $charge = $topUp->charge;
        $payable_amount = $topUp->amount - $charge;

        // Deposit charge of admin with dollar rate
        $method = $topUp->method;
        $final_payable_amount = $payable_amount * $method->rate;
        $charge = formatter_money($method->fixed_charge + ($payable_amount * $method->percent_charge / 100));

        $payable_currency_amount = ($final_payable_amount - ($charge * $method->rate));
        // $final_agent_amount = $payable_currency_amount/$method->rate; 
        $final_agent_amount =  ($topUp->amount / 120) * $method->rate;
        // End deposit charge of admin with dollar rate 

        $agentUser = $agent->user;
        $agentUser->balance->update([
            'office_wallet' => $agentUser->balance->office_wallet + $final_agent_amount
        ]);
        $agentUser->save();

        $user = User::find($topUp->user_id);
        $user->balance->update([
            'self_wallet' => $user->balance->self_wallet + $topUp->amount
        ]);
        $user->save();

        $topUp->update(['status' => 1]);

        $plan = $user->user_plans->first();

        $this->give_mobile_recharge_commission($user->id, $plan->plan_id, $topUp->amount);

        $parent_trx = Trx::create([
            'trx' => getTrx(),
            'user_id' => null,
            'agent_id' => $agent->id,
            'wallet' => 'office_wallet',
            'symbol' => '-',
            'amount' => $final_agent_amount,
            'main_amo' => totalUserBalance($agentUser->id),
            'balance' => $agentUser->balance->office_wallet,
            'title' => 'Mobile recharge via ' . $topUp->method->name . ' to <b>' . $user->username . '</b>',
            'charge' => $topUp->charge,
            'type' => 80, // 80  = Mobile recharge via Agent;
        ]);

        $general = GeneralSetting::first(['cur_sym', 'cur_text', 'agent_topup_commission']);

        $commission = (($final_agent_amount * $general->agent_topup_commission) / 100);

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
                'title' => 'Mobile recharge commission ' . $general->cur_sym . formatter_money($commission) . ' Form (<b>' . $topUp->user->username . '</b>)',
                'wallet' => 'office_wallet',
                'symbol' => '+',
                'amount' => $commission,
                'main_amo' => totalUserBalance($agentUser->id),
                'balance' => $agentUser->balance->office_wallet,
                'charge' => 0,
            ]);
        }

        if ($topUp->amount > 0) {
            send_email($topUp->user, 'DEPOSIT_APPROVE', [
                'trx' => $topUp->trx,
                'amount' => $general->cur_sym . formatter_money($topUp->amount),
                'receive_amount' => $general->cur_sym . formatter_money($topUp->amount),
                'charge' => $general->cur_sym . formatter_money($topUp->charge),
                'method' => $topUp->method->name,
            ]);

            send_sms($topUp->user, 'DEPOSIT_APPROVE', [
                'trx' => $topUp->trx,
                'amount' => $general->cur_text . formatter_money($topUp->amount),
                'receive_amount' => $general->cur_text . formatter_money($topUp->amount),
                'charge' => $general->cur_text . formatter_money($topUp->charge),
                'method' => $topUp->method->name,
            ]);
        }

        $notify[] = ['success', 'MobileRecharge has been approved.'];
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

        $topUp = MobileRecharge::where('agent_id', Auth::user()->agent->id)->findOrFail($decrypted);

        $topUp->update(['status' => 3]);

        $user = User::find($topUp->user_id);

        $user->balance->update([
            'self_wallet' => $user->balance->self_wallet + $topUp->amount,
        ]);
        $user->save();

        $title = 'Rejected Via  ' . $topUp->method->name;

        if ($request->message) {
            $title .= '<br>Message: ' . $request->message;
        }

        $topUp->user->transactions()->save(new Trx([
            'amount' => $topUp->amount,
            'main_amo' => totalUserBalance($user->id),
            'charge' => 0,
            'type' => 81, // 81 = Agent mobile recharge cancel;
            'title' => $title,
            'trx' => getTrx(),
            'balance' => $user->balance->self_wallet,
        ]));

        $notify[] = ['success', 'MobileRecharge has been rejected.'];
        return back()->withNotify($notify);
    }
}
