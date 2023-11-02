@php($currency=\App\Model\BusinessSetting::where(['key'=>'currency'])->first()->value)

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>
        @yield('title')
    </title>
    <!-- SEO Meta Tags-->
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <!-- Viewport-->
    {{--<meta name="_token" content="{{csrf_token()}}">--}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon and Touch Icons-->
    <link rel="shortcut icon" href="favicon.ico">
    <!-- Font -->
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/theme.minc619.css?v=1.0">
    <script
        src="{{asset('public/assets/admin')}}/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js"></script>
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/toastr.css">

    {{--stripe--}}
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
    {{--stripe--}}

    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/bootstrap.css">

</head>
<!-- Body-->
<body class="toolbar-enabled">
{{--loader--}}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" style="display: none;">
                <div style="position: fixed;z-index: 9999; left: 40%;top: 37% ;width: 100%">
                    <img width="200" src="{{asset('public/assets/admin/img/loader.gif')}}">
                </div>
            </div>
        </div>
    </div>
</div>
{{--loader--}}
<!-- Page Content-->
<div class="container pb-5 mb-2 mb-md-4" style="display: block;">
    <div class="row">
        <div class="col-md-12 mb-5 pt-5">
            <center class="">
                <h1>{{ "Please wait while redirecting" }}</h1>
            </center>
        </div>
        <section class="col-lg-12">
            <div class="checkout_details mt-3">
                <div class="row">
                    @php($order_amount = session('order_amount'))
                    @php($customer = \App\User::find(session('customer_id')))
                    @php($callback = session('callback'))
                    
                    
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('payfast'))
                    
                    @if(isset($config) && $config['status'])
                    
                    <?php
                  
                    function generateSignature($data, $passPhrase = null) {
                    // Create parameter string
                    $pfOutput = '';
                    foreach( $data as $key => $val ) {
                    if($val !== '') {
                    $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
                    }
                    }
                    // Remove last ampersand
                    $getString = substr( $pfOutput, 0, -1 );
                    if( $passPhrase !== null ) {
                    $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
                    }
                    return md5( $getString );
                    } 
                  
                    $cartTotal = $order_amount; 
                    $data = array(
                    'merchant_id' => '22731413',
                    'merchant_key' => 'lvqypohheex0y',
                    // 'return_url' => 'https://employeyarena.com/sa6060/payment-success',
                    // 'cancel_url' => 'https://employeyarena.com/sa6060/payment-fail',
                    // 'notify_url' => 'https://employeyarena.com/sa6060/payment-notify',
                    'return_url' => 'https://pricestar.co.za/eiot/pricestar/app/payment-success',
                    'cancel_url' => 'https://pricestar.co.za/eiot/pricestar/app/payment-fail',
                    'notify_url' => 'https://pricestar.co.za/eiot/pricestar/app/payment-notify',
                    
                    'name_first' => "xyz",
                    'name_last'  => 'xyz',
                    'email_address'=> $customer['email'],
                    'm_payment_id' => "sa6060_".rand(1000,999999),
                    'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
                    'item_name' => 'Order#123'
                    );
                    
                    $signature = generateSignature($data,"jt7NOE43FZPn");
                    $data['signature'] = $signature;
                    
                    // If in testing mode make use of either sandbox.payfast.co.za or www.payfast.co.za
                    $testingMode = false;
                    $pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
                    $htmlForm = '<form action="https://'.$pfHost.'/eng/process" method="post">';
                    foreach($data as $name=> $value)
                    {
                    $htmlForm .= '<input name="'.$name.'" type="hidden" value=\''.$value.'\' />';
                    }
                    $htmlForm .= '<input name="passphrase" type="hidden" value="jt7NOE43FZPn" />';
                    $htmlForm .= '<input type="submit" value="Pay Now" id="payfast-button" style="display:none;" /></form>';
                    echo $htmlForm;
                    
                    ?>

                    @endif
                    
                    
                    
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('ssl_commerz_payment'))
                    @if(isset($config) && $config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card" onclick="$('#ssl-form').submit()">
                                <div class="card-body" style="height: 70px">
                                    <form action="{!! route('pay-ssl',['order_amount'=>$order_amount,'customer_id'=>$customer['id'],'callback'=>$callback]) !!}" method="POST" class="needs-validation"
                                          id="ssl-form">
                                        <input type="hidden" value="{{ csrf_token() }}" name="_token"/>
                                        <button class="btn btn-block click-if-alone" type="submit" id="sslcomz-button">
                                            <img width="100"
                                                 src="{{asset('public/assets/admin/img/sslcomz.png')}}"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('razor_pay'))
                    @if(isset($config) && $config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body" style="height: 70px">
                                    <form action="{!!route('payment-razor',['order_amount'=>$order_amount,'customer_id'=>$customer['id'],'callback'=>$callback])!!}" method="POST">
                                    @csrf
                                    <!-- Note that the amount is in paise = 50 INR -->
                                        <!--amount need to be in paisa-->
                                        <script src="https://checkout.razorpay.com/v1/checkout.js"
                                                data-key="{{ $config['razor_key'] }}"
                                                data-amount="{{$order_amount*100}}"
                                                data-buttontext="Pay {{$order_amount}} {{\App\CentralLogics\Helpers::currency_code()}}"
                                                data-name="{{\App\Model\BusinessSetting::where(['key'=>'restaurant_name'])->first()->value}}"
                                                data-description="{{$order_amount}}"
                                                data-image="{{asset('storage/app/public/restaurant/'.\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)}}"
                                                data-prefill.name="{{$customer->f_name}}"
                                                data-prefill.email="{{$customer->email}}"
                                                data-theme.color="#ff7529">
                                        </script>
                                    </form>
                                    <button class="btn btn-block click-if-alone" type="button" id="razorpay-button"
                                            onclick="{{\App\CentralLogics\Helpers::currency_code()=='INR'?"$('.razorpay-payment-button').click()":"toastr.error('Your currency is not supported by Razor Pay.')"}}">
                                        <img width="100"
                                             src="{{asset('public/assets/admin/img/razorpay.png')}}"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif


                    @php($config=\App\CentralLogics\Helpers::get_business_settings('paypal'))
                    @if(isset($config) && $config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body" style="height: 70px">
                                    <form class="needs-validation" method="POST" id="payment-form"
                                          action="{!! route('pay-paypal',['order_amount'=>$order_amount,'customer_id'=>$customer['id'],'callback'=>$callback]) !!}">
                                        {{ csrf_field() }}
                                        <button class="btn btn-block click-if-alone" type="submit" id="paypal-button">
                                            <img width="100"
                                                 src="{{asset('public/assets/admin/img/paypal.png')}}"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif


                    @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                    @if(isset($config) && $config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body" style="height: 70px">
                                    @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                                    <button class="btn btn-block click-if-alone" type="button" id="checkout-button">
                                        <img width="100" src="{{asset('public/assets/admin/img/stripe.png')}}"/>
                                    </button>
                                    <script type="text/javascript">
                                        // Create an instance of the Stripe object with your publishable API key
                                        var stripe = Stripe('{{$config['published_key']}}');
                                        var checkoutButton = document.getElementById("checkout-button");
                                        checkoutButton.addEventListener("click", function () {
                                            fetch("{!! route('pay-stripe',['order_amount'=>$order_amount,'customer_id'=>$customer['id'],'callback'=>$callback]) !!}", {
                                                method: "GET",
                                            }).then(function (response) {
                                                console.log(response)
                                                return response.text();
                                            }).then(function (session) {
                                                console.log(JSON.parse(session).id)
                                                return stripe.redirectToCheckout({sessionId: JSON.parse(session).id});
                                            }).then(function (result) {
                                                if (result.error) {
                                                    alert(result.error.message);
                                                }
                                            }).catch(function (error) {
                                                console.error("Error:", error);
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    @endif


                    @php($config=\App\CentralLogics\Helpers::get_business_settings('paystack'))
                    @if(isset($config) && $config['status'])
                        <?php
                            $Paystack = new App\Http\Controllers\PaystackController();
                            $reff = $Paystack::generate_transaction_Referance();
                        ?>
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body" style="height: 70px">
                                    <form method="POST" action="{!! route('paystack-pay',['order_amount'=>$order_amount,'customer_id'=>$customer['id'],'callback'=>$callback]) !!}" accept-charset="UTF-8"
                                          class="form-horizontal"
                                          role="form">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-8 col-md-offset-2">
                                                <input type="hidden" name="email"
                                                       value="{{$customer->email!=null?$customer->email:'required@email.com'}}"> {{-- required --}}
                                                <input type="hidden" name="orderID" value="">
                                                <input type="hidden" name="amount"
                                                       value="{{$order_amount*100}}"> {{-- required in kobo --}}
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="currency"
                                                       value="{{$currency}}">
                                                <input type="hidden" name="metadata"
                                                       value="{{ json_encode($array = ['key_name' => 'value',]) }}"> {{-- For other necessary things you want to add to your payload. it is optional though --}}
                                                <input type="hidden" name="reference"
                                                       value="{{ $reff }}"> {{-- required --}}
                                                <p>
                                                    <button class="paystack-payment-button click-if-alone" style="display: none"
                                                            type="submit" id="paystack-payment-button"
                                                            value="Pay Now!"></button>
                                                </p>
                                            </div>
                                        </div>
                                    </form>
                                    <button class="btn btn-block" type="button"
                                            onclick="$('.paystack-payment-button').click()">
                                        <img width="100"
                                             src="{{asset('public/assets/admin/img/paystack.png')}}"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('senang_pay'))
                    @if(isset($config) && $config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body" style="height: 70px">
                                    @php($secretkey = $config['secret_key'])
                                    @php($data = new \stdClass())
                                    @php($data->merchantId = $config['merchant_id'])
                                    @php($data->detail = 'payment')
                                    @php($data->order_id = null)
                                    @php($data->amount = $order_amount)
                                    @php($data->name = $customer->f_name.' '.$customer->l_name)
                                    @php($data->email = $customer->email)
                                    @php($data->phone = $customer->phone)
                                    @php($data->hashed_string = md5($secretkey . urldecode($data->detail) . urldecode($data->amount) . urldecode($data->order_id)))

                                    <form name="order" method="post"
                                          action="https://{{env('APP_MODE')=='live'?'app.senangpay.my':'sandbox.senangpay.my'}}/payment/{{$config['merchant_id']}}">
                                        <input type="hidden" name="detail" value="{{$data->detail}}">
                                        <input type="hidden" name="amount" value="{{$data->amount}}">
                                        <input type="hidden" name="order_id" value="{{$data->order_id}}">
                                        <input type="hidden" name="name" value="{{$data->name}}">
                                        <input type="hidden" name="email" value="{{$data->email}}">
                                        <input type="hidden" name="phone" value="{{$data->phone}}">
                                        <input type="hidden" name="hash" value="{{$data->hashed_string}}">
                                    </form>

                                    <button class="btn btn-block click-if-alone" type="button"  id="senangpay-button"
                                            onclick="{{\App\CentralLogics\Helpers::currency_code()=='MYR'?"document.order.submit()":"toastr.error('Your currency is not supported by Senang Pay.')"}}">
                                        <img width="100"
                                             src="{{asset('public/assets/admin/img/senangpay.png')}}"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('bkash'))
                    @if(isset($config) && $config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body" style="height: 70px">
                                    <button class="btn btn-block click-if-alone" id="bKash_button" onclick="BkashPayment()">
                                        <img width="100" src="{{asset('public/assets/admin/img/bkash.png')}}"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('paymob'))
                    @if(isset($config) && $config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body" style="height: 70px">
                                    <form class="needs-validation" method="POST" id="payment-form-paymob"
                                          action="{!! route('paymob-credit',['order_amount'=>$order_amount,'customer_id'=>$customer['id'],'callback'=>$callback]) !!}">
                                        {{ csrf_field() }}
                                        <button class="btn btn-block click-if-alone" id="paymob-button">>
                                            <img width="100" src="{{asset('public/assets/admin/img/paymob.png')}}"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('mercadopago'))
                    @if(isset($config) && $config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body pt-2" style="height: 70px">
                                    <button class="btn btn-block click-if-alone" id="mercadopago-button"
                                            onclick="location.href='{!! route('mercadopago.index',['order_amount'=>$order_amount,'customer_id'=>$customer['id'],'callback'=>$callback]) !!}'">
                                        <img width="150"
                                             src="{{asset('public/assets/admin/img/MercadoPago_(Horizontal).svg')}}"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('flutterwave'))
                    @if(isset($config) && $config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body pt-2" style="height: 70px">
                                    <form method="POST" action="{!! route('flutterwave_pay',['order_amount'=>$order_amount,'customer_id'=>$customer['id'],'callback'=>$callback]) !!}">
                                        {{ csrf_field() }}

                                        <button class="btn btn-block click-if-alone" type="submit" id="fluterwave-button">
                                            <img width="200"
                                                 src="{{asset('public/assets/admin/img/fluterwave.png')}}"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('6cash'))
                    @if(isset($config) && $config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body pt-2" style="height: 70px">
                                    <form method="POST" action="{!! route('6cash.make-payment',['order_amount'=>$order_amount,'customer_id'=>$customer['id'],'callback'=>$callback]) !!}">
                                        {{ csrf_field() }}
                                        <button class="btn btn-block click-if-alone" type="submit" id="6cash-button">
                                            <img width="200"
                                                 src="{{asset('public/assets/admin/img/6cash.png')}}"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('internal_point'))
                    @if(isset($config) && $config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body" style="height: 70px">
                                    <button class="btn btn-block" type="button" data-toggle="modal"
                                            data-target="#exampleModal">
                                        <i class="czi-card"></i> {{ translate('Wallet Point') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title" id="exampleModalLabel">{{ translate('Payment by Wallet Point') }}</h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <hr>
                                        @php($value=\App\Model\BusinessSetting::where(['key'=>'point_per_currency'])->first()->value)

                                        {{--                                        @php($order=\App\Model\Order::find(session('order_id')))--}}
                                        @php($point = $customer['point'])
                                        <span>{{ translate('Order Amount') }} : {{ \App\CentralLogics\Helpers::set_symbol($order_amount) }}</span><br>
                                        <span>{{ translate('Order Amount in Wallet Point') }} : {{$value*$order_amount}} {{ translate('Points') }}</span><br>
                                        <span>{{ translate('Your Available Points') }} : {{$point}} {{ translate('Points') }}</span><br>
                                        <hr>
                                        <center>
                                            @if(($value*$order_amount)<=$point)
                                                <label class="badge badge-soft-success">{{ translate('You have sufficient balance to proceed!') }}</label>
                                            @else
                                                <label class="badge badge-soft-danger">{{ translate('Your balance is insufficient!') }}</label>
                                            @endif
                                        </center>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Close') }}
                                        </button>
                                        @if(($value*$order_amount)<=$point)
                                            <form action="{!! route('internal-point-pay',['order_amount'=>$order_amount,'customer_id'=>$customer['id'],'callback'=>$callback]) !!}" method="POST">
                                                @csrf
                                                <input name="order_id" value="" style="display: none">
                                                <button type="submit" class="btn btn-primary" id="internal-point-pay-button">{{ translate('Proceed') }}</button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-primary">{{ translate('Sorry! Next time.') }}</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>

<!-- JS Front -->
<script src="{{asset('public/assets/admin')}}/js/jquery.js"></script>
<script src="{{asset('public/assets/admin')}}/js/bootstrap.js"></script>
<script src="{{asset('public/assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('public/assets/admin')}}/js/toastr.js"></script>
{!! Toastr::message() !!}

<script>
    // setTimeout(function () {
    //     $('.stripe-button-el').hide();
    //     $('.razorpay-payment-button').hide();
    // }, 10)
</script>

<script>
    $(document).ready(function (){
        
        $('#payfast-button').click();
        
        // let payment_method = "{{$payment_method}}"

        // if (payment_method ==='ssl_commerz_payment') {
        //     $('#sslcomz-button').click();
        // } else if (payment_method === 'razor_pay') {
        //     $('#razorpay-button').click();
        // } else if (payment_method === 'payfast') {
        //     $('#payfast-button').click();
        // } else if (payment_method === 'paypal') {
        //     $('#paypal-button').click();
        // } else if (payment_method === 'stripe') {
        //     $('#checkout-button').click();
        // } else if (payment_method === 'senang_pay') {
        //     $('#senangpay-button').click();
        // } else if (payment_method === 'paystack') {
        //     $('#paystack-payment-button').click();
        // } else if (payment_method === 'bkash') {
        //     $('#bKash_button').click();
        // } else if (payment_method === 'paymob') {
        //     $('#paymob-button').click();
        // } else if (payment_method === 'flutterwave') {
        //     $('#fluterwave-button').click();
        // } else if (payment_method === 'mercadopago-button') {
        //     $('#mercadopago-button').click();
        // } else if (payment_method === 'digital_payment') {
        //     $('#internal-point-pay-button').click();
        // } else if (payment_method === '6cash') {
        //     $('#6cash-button').click();
        // }
    });
</script>




</body>
</html>
