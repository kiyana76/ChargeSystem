<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function payment(Request $request) {
        $array = ['fail', 'cancel', 'success'];

        $random_status = $array[array_rand($array)];
        //$random_status = 'success';

        if ($random_status == 'success')
            return response()->json(['message' => 'payment successful', 'body' => ['status' => 'success'], 'error' => false], 200);
        return response()->json(['message' => 'payment ' . $random_status, 'body' => ['status' => $random_status], 'error' => true], 200);
    }
}
