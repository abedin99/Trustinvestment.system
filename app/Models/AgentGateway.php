<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentGateway extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agent_id',
        'gateway_code',
        'account_number',
        'status'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'status' => 'boolean'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the gateway that owns the gatewayCurrency.
     */
    // public function gatewayCurrency()
    // {
    //     return $this->belongsTo(GatewayCurrency::class, 'gateway_code', 'gateway_code');
    // }
}
