@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="d-flex justify-content-center">
        <div class="card">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                @if ($product)
                    <p>Anda akan melakukan pembelian produk <strong>{{ $product['name'] }}</strong> dengan harga
                        <strong>Rp{{ number_format($product['price'], 0, ',', '.') }}</strong></p>
                    <button type="button" class="btn btn-primary mt-3" id="pay-button">
                        Bayar Sekarang
                    </button>
                @else
                    <p>Produk tidak ditemukan.</p>
                    <p>Debug Info:</p>
                    <pre>{{ print_r($products, true) }}</pre>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Add your scripts here if needed -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{env('MIDTRANS_CLIENT_KEY')}}"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
          // SnapToken acquired from previous step
          snap.pay('{{$transaction->snap_token}}', {
            // Optional
            onSuccess: function(result){
                window.location.href = '{{ route('checkout-success', ['transaction' => $transaction->id]) }}';

            },
            // Optional
            onPending: function(result){
              /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
            },
            // Optional
            onError: function(result){
              /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
            }
          });
        };
      </script>
@endsection
