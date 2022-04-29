<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
	public $timestamps = true;

	// public function doer()
    // {
    //     return $this->belongsTo('App\Models\User', 'doer_id');
    // }

    public function user() {
        if ($this->is_client == 1)
    	    return $this->belongsTo('App\Models\User', 'user_id');
        else {
            return $this->belongsTo('App\Models\External', 'user_id');
        }
    }

    public function psp() {
        return $this->belongsTo('App\Models\Psp', 'psp_id');
    }
	
}