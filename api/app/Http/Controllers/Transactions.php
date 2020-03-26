<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class Transactions extends Controller{

    /**
     * List an account's transactions    
     */

    public function list($id){

        $account = DB::table('transactions')
             ->whereRaw("`from`=$id OR `to`=$id")
             ->get();

        return $account;
        
    }

    /**
     * Send money to an account
     */

    public function send(Request $request, $id){

        $to = $request->input('to');
        $amount = $request->input('amount');
        $details = $request->input('details');

        $account = DB::table('accounts')
                 ->whereRaw("id=$id")
                 ->update(['balance' => DB::raw('balance-' . $amount)]);

        $account = DB::table('accounts')
                 ->whereRaw("id=$to")
                 ->update(['balance' => DB::raw('balance+' . $amount)]);

        DB::table('transactions')->insert(
            [
                'from' => $id,
                'to' => $to,
                'amount' => $amount,
                'details' => $details
            ]
        );

    }
}
