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
             ->where('from',$id)
             ->orWhere('to',$id)
             ->get();

        return $account;
        
    }

    /**
     * Send money to an account
     */

    public function send(Request $request, $id){

        $status['status'] = false;

        $to = $request->input('to');
        $amount = $request->input('amount');
        $details = $request->input('details');

        // Validate the sent amount
        //$this->validateAmount($id, $amount);

        $sender = $this->updateBalance($id, $amount, 'debit');
        $sender = $this->updateBalance($to, $amount, 'credit');


        DB::table('transactions')->insert(
            [
                'from' => $id,
                'to' => $to,
                'amount' => $amount,
                'details' => $details
            ]
        );

    }

    /**
     * Retrieve an account's balance
     */

    private function getBalance($id){

        $balance = DB::table('accounts')
                 ->select('balance')
                 ->where('id', $id)
                 ->get()[0];

        return $balance->balance;

    }

    /**
     * Update an account's balance
     */

    private function updateBalance($id, $amount, $operator){

        $balance = $this->getBalance($id);
        $newAmount = $operator === 'credit' ? $balance + $amount : $balance - $amount;

        $account = DB::table('accounts')
                 ->where('id', $id)
                 ->update(['balance' => $newAmount]);

        return $account;

    }

    /**
     * Verify if the amount sent is less than or equal to 
     * the remaining remaining balance in the users account
     */

    private function validateAmount($id, $amount){
        
        $balance = $this->getBalance($id);

        return $amount <= $balance;

    }


    /**
     *
     */

}
