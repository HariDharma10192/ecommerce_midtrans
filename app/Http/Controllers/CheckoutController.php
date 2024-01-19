<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product; // Import the Product model


class CheckoutController extends Controller
{


    public function process(Request $request)
    {
        $data = $request->all();

        $transaction = Transaction::create([
            'user_id' => Auth::user()->id,
            'product_id' => $data['product_id'],
            'price' => $data['price'],
            'status' => 'pending',
        ]);

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;


        $params = array(
            'transaction_details' => array(
                'order_id' => rand(),
                'gross_amount' => $data['price'],
            ),
            'customer_details' => array(
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,

            ),

        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);


        $transaction->snap_token = $snapToken; // Menetapkan nilai snap_token dengan nilai snapToken
        $transaction->save();


        return redirect()->route('checkout', $transaction->id);
    }

    public function checkout(Transaction $transaction)
    {
        $products = config('products');
        $product = collect($products)->firstWhere('id', $transaction->product_id);

        return view('checkout',  compact('transaction', 'product'));
    }
    // public function checkout($id)
    // {
    //     $products = config('products');
    //     $product = collect($products)->firstWhere('id', $id);

    //     if (!$product) {
    //         return abort(404); // Product not found
    //     }

    //     return view('checkout', compact('product'));
    // }

    public function success($transaction)
    {
        $transaction = Transaction::findOrFail($transaction);

        $transaction->status = 'success';
        $transaction->save();

        return view('success');
    }
}
