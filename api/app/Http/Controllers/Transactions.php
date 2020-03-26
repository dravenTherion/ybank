<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class Transactions extends Controller{

    protected $INSUFFICIENT_BALANCE = -1;
    protected $INVALID_ACCOUNT = -2;
    protected $INVALID_AMOUNT = -3;

    /**
     * List an account's transactions
     *
     * $id: id of the account related to the transactions
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
     *
     * $request: POST request details of the transaction
     * $id: id of the account sending the money
     */

    public function send(Request $request, $id){

        $response['status'] = false;
        
        $to = $request->input('to');
        $amount = floatval($request->input('amount'));
        $details = $request->input('details');

        if(
           $id !== $to // Verify if the source account is not the destination account
           && $this->accountExists($id) // Verify if the sender account exists
           && $this->accountExists($to) // Verify if the receiver account exists
           ){

            // Check if the amount is valid
            if($amount <= 0)
                $response['error'] = $this->INVALID_AMOUNT;

            // Check if the amount sent is within the remaining balance
            else if($this->sufficientBalance($id, $amount)){

                $debitStatus = $this->updateBalance($id, $amount, 'debit');
                $creditStatus = $this->updateBalance($to, $amount, 'credit');

                // Record successful transaction
                $this->recordTransaction($id, $to, $amount, $details);

                $response['status'] = true;

            }
            else{
                $response['error'] = $this->INSUFFICIENT_BALANCE;
            }
        }
        else{
            $response['error'] = $this->INVALID_ACCOUNT;
        }

        return $response;
    }

    /**
     * Retrieve an account's balance
     *
     * $id: id of the account
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
     *
     * $id: id of the account to be updated
     * $amount: amount credited or debited to an account
     * $operator: credit or debit to an account
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
     *
     * $id: id of the account
     * $amount: amount to be debited from the account
     */

    private function sufficientBalance($id, $amount){
        
        $balance = floatval($this->getBalance($id));

        return ($amount <= $balance);

    }

    /**
     * Verify if the receiving account exists
     *
     * $id: id of the account
     */

    private function accountExists($id){
        
        $account = DB::table('accounts')
                 ->where('id', $id)
                 ->get();

        return $account->count() > 0;

    }

    /**
     * Record a transaction made with an account
     *
     *  $id: sender of the money
     *  $to: receiver of the money
     *  $amount: sent amount
     *  $details: transaction details
     */

    private function recordTransaction($id, $to, $amount, $details){

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
