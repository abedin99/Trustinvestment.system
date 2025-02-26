<?php

namespace App\Http\Controllers\User\AgentPanel;

use App\Models\Trx;
use App\Models\Agent;
use App\Models\Deposit;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AgentPanelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('check.agent.status');
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

        $page_title = 'Agent Panel';

        $agent = Auth::user()->agent;

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderBy('serial_id', 'asc')->get();

        $deposits = Deposit::where('status', 2)->where('agent_id', $user->agent->id)->with(['user', 'gateway'])->latest()->count();
        $withdrawals = Withdrawal::where('status', 2)->where('agent_id', $user->agent->id)->latest()->count();
        return view(activeTemplate() . 'user.agent-panel.index', compact('page_title', 'gatewayCurrency', 'deposits', 'withdrawals', 'agent'));
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
        return abort(404);
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
     * @param  string  $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $code)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $agent = Agent::where('code', $code)->first();

        if (!$agent && $agent->id != auth()->user()->agent->id) {
            $notify[] = ['error', 'Agent does not exist.'];
            return back()->withNotify($notify);
        }

        $agent->status = ($request->status == 'active') ? true : false;

        if ($agent->save()) {
            $notify[] = ['success', 'Agent status updated.'];
            return back()->withNotify($notify);
        }
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function transactions()
    {
        if (!auth()->user()->agent) {
            return abort(404);
        }

        $user = Auth::user();
        $page_title = 'Agent Transaction Logs';
        $transactions = Trx::where('agent_id', $user->agent->id)->with('user')->orderBy('id', 'DESC')->paginate(config('constants.table.default'));
        $empty_message = 'No transactions.';
        return view(activeTemplate() . 'user.agent-panel.transactions', compact('page_title', 'transactions', 'empty_message'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function commissions()
    {
        if (!auth()->user()->agent) {
            return abort(404);
        }

        $user = Auth::user();
        $page_title = 'Agent Commission Logs';
        $commissions = Trx::where('agent_id', $user->agent->id)->whereNotIn('type', [23, 22, 25, 27])->with('user')->orderBy('id', 'DESC')->paginate(config('constants.table.default'));
        $empty_message = 'No transactions.';
        return view(activeTemplate() . 'user.agent-panel.commissions', compact('page_title', 'commissions', 'empty_message'));
    }
}
