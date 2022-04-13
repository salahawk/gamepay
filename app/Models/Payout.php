<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
	public $timestamps = true;

	// public function doer()
    // {
    //     return $this->belongsTo('App\Models\User', 'doer_id');
    // }

    public function user() {
    	return $this->belongsTo('App\Models\User', 'user_id');
    }
	
}