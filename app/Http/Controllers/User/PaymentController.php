<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AgentGateway;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\GatewayCurrency;
use App\Models\GeneralSetting;
use App\Models\Trx;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('Check.Freeze', only: ['depositInsert', 'depositPreview', 'depositConfirm', 'manualDepositConfirm', 'manualDepositUpdate', 'agentDepositInsert', 'agentDepositPreview', 'agentDepositConfirm', 'agentDepositUpdate']),
        ];
    }
    
    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderBy('serial_id', 'asc')->get();
        $get_agent = Agent::whereNull('banned_at')->orderBy('id', 'desc')->with('user')->get();
        $page_title = 'Deposit Methods';
        return view('deposit.index', compact('gatewayCurrency', 'page_title', 'get_agent'));
    }

    public function getDepositAgentListByCode(Request $request)
    {
        $agentGateway = AgentGateway::where('gateway_code', $request->method_code)->get();

        $agentList = [];
        foreach ($agentGateway as $agent) {
            $agentList[] = $agent->agent;
        }

        $html = '<option value="" hidden>-- select one --</option>';
        foreach ($agentList as $agent) {
            if ($agent->status == true) {
                if ($agent->banned_at == null && $agent->user->balance->office_wallet > 4) {
                    $html .= "<option value=\"$agent->code\">{$agent->code}</option>";
                }
            }
        }
        return ['data' => $html];
    }

    public function depositInsert(Request $request)
    {
        if (Deposit::where(['user_id' => auth()->id(), 'status' => '2'])->count() == true) {
            $notify[] = ['error', 'New Deposit will be accepted after the previous Deposit.'];
            return back()->withNotify($notify);
        }

        try {
            $decrypted = Crypt::decryptString($request->wallet);
            $request['wallet'] = $decrypted;
        } catch (DecryptException $e) {
            $notify[] = ['error', 'The selected wallet is invalid.'];
            return back()->withNotify($notify);
        }

        $this->validate($request, [
            'amount' => 'required|numeric|min:1',
            'method_code' => 'required',
            'currency' => 'required',
            'wallet' => 'required|in:income_balance,self_wallet,ecommerce_wallet,office_wallet,earning_wallet,savings_wallet',
        ]);

        $gate = GatewayCurrency::where('method_code', $request->method_code)->where('currency', $request->currency)->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid Gateway'];
            return back()->withNotify($notify);
        }
        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please Follow Deposit Limit'];
            return back()->withNotify($notify);
        }

        // calculate wallet rate
        if (isset($gate->wallet_rates) && isset($gate->wallet_rates[$request->wallet . '_rate'])) {
            $rate = $gate->wallet_rates[$request->wallet . '_rate'];
        } else {
            $rate = $gate->rate;
        }


        $charge = formatter_money($gate->fixed_charge + ($request->amount * $gate->percent_charge / 100));
        $withCharge = $request->amount + $charge;
        $final_amo = formatter_money($withCharge * $rate);

        $deposit['user_id'] = Auth::id();
        $deposit['wallet'] = $request->wallet;
        $deposit['method_code'] = $gate->method_code;
        $deposit['method_currency'] = strtoupper($gate->currency);
        $deposit['amount'] = formatter_money($request->amount);
        $deposit['charge'] = $charge;
        $deposit['rate'] = $rate;
        $deposit['final_amo'] = $final_amo;
        $deposit['btc_amo'] = 0;
        $deposit['btc_wallet'] = "";
        $deposit['trx'] = getTrx();
        $deposit['try'] = 0;
        $deposit['status'] = 0;
        // Deposit::create($deposit);
        Session::forget('deposit');
        Session::put('deposit', $deposit);
        // Session::put('Track', $deposit['trx']);
        return redirect()->route('user.deposit.preview');
    }


    public function depositPreview()
    {
        $deposit = Session::get('deposit');

        // $data = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$deposit) {
            return redirect()->route('user.deposit');
        }
        $gateway = Gateway::where('code', $deposit['method_code'])->firstOrFail();

        $data = $deposit;
        $page_title = "Deposit Preview";
        return view(activeTemplate() . 'payment.preview', compact('data', 'page_title', 'gateway'));
    }


    public function depositConfirm()
    {
        $deposit = Session::get('deposit');
        // $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        if (is_null($deposit)) {
            $notify[] = ['error', 'Invalid Deposit Request'];
            return redirect()->route('user.deposit')->withNotify($notify);
        }
        if ($deposit['status'] != 0) {
            $notify[] = ['error', 'Invalid Deposit Request'];
            return redirect()->route('user.deposit')->withNotify($notify);
        }

        if ($deposit['method_code'] >= 1000) {
            return redirect()->route('user.manualDeposit.confirm');
        }

        $xx = 'g' . $deposit['method_code'];
        $new = __NAMESPACE__ . '\\' . $xx . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return redirect()->route('user.deposit')->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }
        $page_title = 'Payment Confirm';

        return view(activeTemplate() . $data->view, compact('data', 'deposit', 'page_title'));
    }


    public static function userDataUpdate($data)
    {

        if ($data->status == 0) {
            $data['status'] = 1;
            $data->update();

            $user = User::find($data->user_id);
            $user->balance->update([
                'income_balance' => $user->balance->income_balance + $data->amount,
            ]);
            $user->save();
            $gateway = $data->gateway;
            Trx::create([
                'user_id' => $data->user_id,
                'amount' => $data->amount,
                'main_amo' => totalUserBalance($user->id),
                'charge' => $data->charge,
                'type' => 2, // 2 = Deposit;
                'title' => 'Deposit Via ' . $gateway->name,
                'trx' => $data->trx,
                'balance' => $user->balance->income_balance,
            ]);

            $general = GeneralSetting::first(['cur_sym']);

            $amount = $general->cur_sym . ' ' . formatter_money($data->amount, $gateway->crypto());

            if ($amount > 0) {
                send_email($user, 'DEPOSIT_SUCCESS', [
                    'amount' => $amount,
                    'method' => $gateway->name,
                ]);
                send_sms($user, 'DEPOSIT_SUCCESS', [
                    'amount' => $amount,
                    'method' => $gateway->name,
                ]);
            }

            $deposit = $data;

            if ($deposit->amount > 0) {
                send_email($user, 'DEPOSIT_APPROVE', [
                    'trx' => $deposit->trx,
                    'amount' => $general->cur_sym . formatter_money($deposit->amount),
                    'receive_amount' => $amount,
                    'charge' => $general->cur_sym . formatter_money($deposit->charge),
                    'method' => $deposit->gateway->name,
                ]);

                send_sms($user, 'DEPOSIT_APPROVE', [
                    'trx' => $deposit->trx,
                    'amount' => $general->cur_sym . formatter_money($deposit->amount),
                    'receive_amount' => $amount,
                    'charge' => $general->cur_sym . formatter_money($deposit->charge),
                    'method' => $deposit->gateway->name,
                ]);
            }
        }
    }


    public function manualDepositConfirm(Request $request)
    {
        $deposit = Session::get('deposit');
        $gateway = Gateway::where('code', $deposit['method_code'])->firstOrFail();
        $gateway_currency = GatewayCurrency::where('method_code', $deposit['method_code'])->firstOrFail();
        $data = $deposit;
        // dd($deposit, $gateway);
        $page_title = 'Deposit Confirm';
        return view(activeTemplate() . 'payment.manual_confirm', compact('page_title', 'data', 'gateway', 'gateway_currency'));
    }

    public function manualDepositUpdate(Request $request)
    {
        if (Deposit::where(['user_id' => auth()->id(), 'status' => '2'])->count() == true) {
            $notify[] = ['error', 'New Deposit will be accepted after the previous Deposit.'];
            return redirect()->route('user.deposit')->withNotify($notify);
        }

        $request->validate([
            'ud' => 'required',
        ]);

        $deposit = Session::get('deposit');
        $gateway = Gateway::where('code', $deposit['method_code'])->firstOrFail();

        if ($deposit['status'] != 0 || !$deposit) {
            $notify[] = ['error', 'Invalid Deposit Request'];
            return redirect()->route('user.deposit')->withNotify($notify);
        }

        if ($request->hasFile('verify_image')) {
            try {
                $filename = upload_image($request->verify_image, config('constants.deposit.verify.path'));
                $verify_image = $filename;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Could not upload your verification image'];
                return back()->withNotify($notify);
            }
        } else {
            $verify_image = null;
        }
        Session::forget('deposit.status');

        $data = new Deposit;
        $data->verify_image = $verify_image;
        foreach ($deposit as $index => $value) {
            $data->$index = $value;
        }
        $data->detail = $request->ud;
        $data->status = 2;
        $data->save();

        $general = GeneralSetting::first();


        if ($request->amount > 0) {
            send_email(auth()->user(), 'DEPOSIT_PENDING', [
                'trx' => $data->trx,
                'amount' => $general->cur_sym . ' ' . formatter_money($request->amount),
                'method' => $data->gateway_currency()->name,
                'charge' => $general->cur_sym . ' ' . $data->charge,
            ]);

            send_sms(auth()->user(), 'DEPOSIT_PENDING', [
                'trx' => $data->trx,
                'amount' => $general->cur_sym . ' ' . formatter_money($request->amount),
                'method' => $data->gateway_currency()->name,
                'charge' => $general->cur_sym . ' ' . $data->charge,
            ]);
        }

        $notify[] = ['success', 'You have deposit request has been taken.'];
        return redirect()->route('user.deposit.history')->withNotify($notify);
    }


    /**
     ***
     *** Agent Deposit Section
     ***
     **/


    public function agentDepositInsert(Request $request)
    {
        if (Deposit::where(['user_id' => auth()->id(), 'status' => '2'])->count() == true) {
            $notify[] = ['error', 'New Deposit will be accepted after the previous Deposit.'];
            return back()->withNotify($notify);
        }

        try {
            $decrypted = Crypt::decryptString($request->wallet);
            $request['wallet'] = $decrypted;
        } catch (DecryptException $e) {
            $notify[] = ['error', 'The selected wallet is invalid.'];
            return back()->withNotify($notify);
        }

        $this->validate($request, [
            'amount' => 'required|numeric|min:1',
            'agent_code' => 'required|exists:agents,code',
            'currency' => 'required',
            'wallet' => 'required|in:income_balance,self_wallet,ecommerce_wallet,office_wallet,earning_wallet,savings_wallet',
        ]);

        $agent = Agent::whereCode($request->agent_code)->firstOrFail();

        if (Auth::user()->agent) {
            if ($agent->id != 1) {
                $notify[] = ['warning', 'Agent transaction is not allowed!'];
                return back()->withNotify($notify);
            }
        }

        if (is_course_active($agent->user->id) == false) {
            $notify[] = ['error', 'This agent account is inactive!'];
            return back()->withNotify($notify);
        }

        $agentUser = User::find($agent->user_id);
        $oldPendingAmount = Deposit::where(['agent_id' => $agent->id, 'status' => '2'])->sum('amount');

        if (($oldPendingAmount + $request->amount) > $agentUser->balance->{$request->wallet}) {
            $notify[] = ['error', 'Agent Balance Extended!'];
            return back()->withNotify($notify);
        }

        $gate = GatewayCurrency::where('method_code', $request->agent_gateway)->where('currency', $request->currency)->first();

        if (isset($gate->wallet_rates[$request->wallet . '_rate']) && $gate->wallet_rates[$request->wallet . '_rate']) {
            $rate = $gate->wallet_rates[$request->wallet . '_rate'];
        } else {
            $rate = $gate->rate;
        }

        if (!$gate) {
            $notify[] = ['error', 'Invalid Gateway'];
            return back()->withNotify($notify);
        }
        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please Follow Deposit Limit'];
            return back()->withNotify($notify);
        }

        $charge = formatter_money($gate->fixed_charge + ($request->amount * $gate->percent_charge / 100));
        $withCharge = $request->amount + $charge;
        $final_amo = formatter_money($withCharge * $rate);

        $depo['user_id'] = Auth::id();
        $depo['wallet'] = $request->wallet;
        $depo['agent_code'] = $request->agent_code;
        $depo['method_code'] = $gate->method_code;
        $depo['method_currency'] = strtoupper($gate->currency);
        $depo['amount'] = formatter_money($request->amount);
        $depo['charge'] = $charge;
        $depo['rate'] = $rate;
        $depo['final_amo'] = $final_amo;
        $depo['btc_amo'] = 0;
        $depo['btc_wallet'] = "";
        $depo['trx'] = getTrx();
        $depo['try'] = 0;
        $depo['status'] = 0;

        // delete old agent deposit session
        Session::forget('agent_deposit');
        // regenerate agent deposit session
        Session::put('agent_deposit', $depo);

        return redirect()->route('user.agent.deposit.preview');
    }


    public function agentDepositPreview()
    {
        // get agent deposit session
        $deposit = Session::get('agent_deposit');

        if (!$deposit) {
            return redirect()->route('user.deposit');
        }
        $gateway = Gateway::where('code', $deposit['method_code'])->firstOrFail();

        $data = $deposit;
        $page_title = "Agent Deposit Preview";
        return view(activeTemplate() . 'payment.agent.preview', compact('data', 'page_title', 'gateway'));
    }

    public function agentDepositConfirm(Request $request)
    {
        $deposit = Session::get('agent_deposit');

        $gateway = Gateway::where('code', $deposit['method_code'])->firstOrFail();
        $gateway_currency = GatewayCurrency::where('method_code', $deposit['method_code'])->firstOrFail();
        $data = $deposit;

        $agent = Agent::where('code', $deposit['agent_code'])->first();
        $agent_gateway = AgentGateway::where(['agent_id' => $agent->id, 'gateway_code' => $deposit['method_code']])->firstOrFail();

        $page_title = 'Agent Deposit Confirm';
        return view(activeTemplate() . 'payment.agent.confirm', compact('page_title', 'data', 'gateway', 'gateway_currency', 'agent_gateway'));
    }

    public function agentDepositUpdate(Request $request)
    {
        if (Deposit::where(['user_id' => auth()->id(), 'status' => '2'])->count() == true) {
            $notify[] = ['error', 'New Deposit will be accepted after the previous Deposit.'];
            return redirect()->route('user.deposit')->withNotify($notify);
        }

        $request->validate([
            'ud' => 'required',
        ]);

        $search = $request->ud['transaction-id'];
        if ($search) {
            $depositsExist = Deposit::whereNotIn('status', [3])
                ->where(function ($q) use ($search) {
                    $q->orWhereJsonContains('detail', ['transaction-id' => $search]);
                })
                ->exists();
            if ($depositsExist) {
                $notify[] = ['error', 'The transaction ID already exists.'];
                return back()->withNotify($notify);
            }
        } else {
            $notify[] = ['error', 'The transaction ID could not be found.'];
            return back()->withNotify($notify);
        }

        $deposit = Session::get('agent_deposit');
        $gateway = Gateway::where('code', $deposit['method_code'])->firstOrFail();

        $agent = Agent::where('code', $deposit['agent_code'])->first();
        $deposit['agent_id'] = $agent->id;
        $deposit = Arr::except($deposit, ['agent_code']);
        if ($deposit['status'] != 0 || !$deposit) {
            $notify[] = ['error', 'Invalid Deposit Request'];
            return redirect()->route('user.deposit')->withNotify($notify);
        }

        if ($request->hasFile('verify_image')) {
            try {
                $filename = upload_image($request->verify_image, config('constants.deposit.verify.path'));
                $verify_image = $filename;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Could not upload your verification image'];
                return back()->withNotify($notify);
            }
        } else {
            $verify_image = null;
        }
        Session::forget('deposit.status');

        $data = new Deposit;
        $data->verify_image = $verify_image;

        foreach ($deposit as $index => $value) {
            $data->$index = $value;
        }
        $data->detail = $request->ud;
        $data->status = 2;
        $data->save();

        $general = GeneralSetting::first();

        if ($request->amount > 0) {
            send_email(auth()->user(), 'DEPOSIT_PENDING', [
                'trx' => $data->trx,
                'amount' => $general->cur_sym . ' ' . formatter_money($request->amount),
                'method' => $data->gateway_currency()->name,
                'charge' => $general->cur_sym . ' ' . $data->charge,
            ]);

            send_sms(auth()->user(), 'DEPOSIT_PENDING', [
                'trx' => $data->trx,
                'amount' => $general->cur_sym . ' ' . formatter_money($request->amount),
                'method' => $data->gateway_currency()->name,
                'charge' => $general->cur_sym . ' ' . $data->charge,
            ]);
        }

        $notify[] = ['success', 'You have deposit request has been taken.'];
        return redirect()->route('user.deposit.history')->withNotify($notify);
    }
}
