<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class Accounts extends Controller{

    /**
     * get user account based on id    
     */

    public function get($id){

        $account = DB::table('accounts')
             ->where('id', $id)
             ->get();

        return $account;
        
    }

}
