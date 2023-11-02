<?php

namespace App\Http\Controllers;

use App\Model\Order;
use App\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        if (session()->has('payment_method') == false) {
            session()->put('payment_method', 'payfast');
        }

        $params = explode('&&', base64_decode($request['token']));
         //dd($params);
        foreach ($params as $param) {
            $data = explode('=', $param);
            if ($data[0] == 'customer_id') {
                session()->put('customer_id', $data[1]);
            } elseif ($data[0] == 'callback') {
                session()->put('callback', $data[1]);
            } elseif ($data[0] == 'order_amount') {
                session()->put('order_amount', $data[1]);
            } elseif ($data[0] == 'product_ids') {
                session()->put('product_ids', $data[1]);
            }
        }

        $customer = User::find(session('customer_id'));
        $order_amount = session('order_amount');

        if (isset($customer) && isset($order_amount)) {
            $data = [
                'name' => $customer['f_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ];
            session()->put('data', $data);
            return view('payment-view', ['payment_method' => $request['payment_method']]);
        }

        if (!isset($customer))
            return response()->json(['errors' => ['message' => 'Customer not found']], 403);
        elseif (!isset($order_amount))
            return response()->json(['errors' => ['message' => 'Amount not found']], 403);
        else
            return response()->json(['errors' => ['message' => '']], 403);

    }
    
    
       public function success(Request $request)
    {
        //dd($request);
        //$token_string = 'payment_method=ssl_commerz_payment&&transaction_reference=' . $request['tran_id'];
        $token_string = 'payment_method=payfast&&transaction_reference=#'.rand(9999,100000);
        //success
        // $callback = "https://employeyarena.com/sa6060/order-successful";
          $callback = "https://pricestar.co.za/eiot/pricestar/app/order-successful";
        if ($callback != null) {
            
            return redirect($callback . '/success' . '?token=' . base64_encode($token_string));
        } else {
            return redirect()->route('payment-success', ['token' => base64_encode($token_string)]);
        }
    }
    
    
    
    // public function success()
    // {
    //     if (session()->has('callback')) {
    //         return redirect(session('callback') . '/success');
    //     }
    //     return response()->json(['message' => 'Payment succeeded'], 200);
    // }
    
    
    // public function success()
    // {
    //     if (session()->has('callback')) {
    //         return redirect(session('callback') . '/success');
    //     }
    //     return response()->json(['message' => 'Payment succeeded'], 200);
    // }

    public function fail()
    {
        $callback = "https://pricestar.co.za/eiot/pricestar/app/order-successful";
        //  $callback = "https://employeyarena.com/sa6060/order-successful";
        return redirect($callback . '/cancel?');
    }
}
