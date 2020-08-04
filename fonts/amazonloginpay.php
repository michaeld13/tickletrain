<?php


namespace PayWithAmazon;

session_start();

if(isset($_COOKIE['orderReferenceId'])){

require_once 'PayWithAmazon/Client.php';

$config = array('merchant_id'   => 'A1L458EVZY3PWM',
                'access_key'    => 'AKIAJ5QIPTG4ESZRAYRA',
                'secret_key'    => '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',
                'client_id'     => 'amzn1.application-oa2-client.96fce878f1094fd8977c7beb5c3370ed',
                'region'        => 'us',
                'sandbox'       => true );


$client = new Client($config);
$requestParameters = array();

// Create the parameters array to set the order
$requestParameters['amazon_order_reference_id'] = $_COOKIE['orderReferenceId'];
$requestParameters['amount']            = '175.00';
$requestParameters['currency_code']     = 'USD';
$requestParameters['seller_note']   = 'Love this sample';
$requestParameters['seller_order_id']   = '123456-TestOrder-123456';
$requestParameters['store_name']        = 'Saurons collectibles in Mordor';

// Set the Order details by making the SetOrderReferenceDetails API call
$response = $client->SetOrderReferenceDetails($requestParameters);

if($client->success)
{
    $requestParameters = array();
    $requestParameters['amazon_order_reference_id'] = $_COOKIE['orderReferenceId'];
    $response = $client->confirmOrderReference($requestParameters);
    $responsearray['confirm'] = json_decode($response->toJson());
    if($client->success)
{
    $requestParameters['authorization_amount'] = '175.00';
    $requestParameters['currency_code'] = 'USD';
    $requestParameters['authorization_reference_id'] = uniqid('A01_REF_');
    $requestParameters['seller_Authorization_Note'] = 'Authorizing and capturing the payment';
    $requestParameters['transaction_timeout'] = 0;
    $requestParameters['capture_now'] = true;

    $response = $client->authorize($requestParameters);
    $responsearray['authorize'] = json_decode($response->toJson());
    setcookie('orderReferenceId',$_COOKIE['orderReferenceId'], time() -3600);
    
}
}
echo "<pre>";
print_r($responsearray);
print_r(json_decode(json_encode($responsearray)));
die();
}
?>

<div id="AmazonPayButton"></div>

<script type='text/javascript'>
    window.onAmazonLoginReady = function () {
        amazon.Login.setClientId('amzn1.application-oa2-client.96fce878f1094fd8977c7beb5c3370ed');
        amazon.Login.setUseCookie(true);
    };
</script>

<script type='text/javascript' src='https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js'></script>

<script type='text/javascript'>
    var authRequest;
    OffAmazonPayments.Button("AmazonPayButton", "A1L458EVZY3PWM", {
        type: "PwA",
        authorization: function () {
            loginOptions = { scope: "profile postal_code payments:widget payments:shipping_address", popup: true };
            authRequest = amazon.Login.authorize(loginOptions, "https://client.tickletrain.com/fonts/amazonloginpay.php");
        },
        onError: function (error) {
            // something bad happened
        }
    });
</script>



<div id="addressBookWidgetDiv" style="width:400px; height:240px;"></div>
<div id="walletWidgetDiv" style="width:400px; height:240px;"></div>

<script type='text/javascript'>
    window.onAmazonLoginReady = function () {
        amazon.Login.setClientId('amzn1.application-oa2-client.96fce878f1094fd8977c7beb5c3370ed');
        amazon.Login.setUseCookie(true);
    };
</script>

<script type='text/javascript' src='https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js'></script>

<script type="text/javascript">
    
    function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
    
    
    
    new OffAmazonPayments.Widgets.AddressBook({
        sellerId: 'A1L458EVZY3PWM',
        onOrderReferenceCreate: function (orderReference) {
           orderReferenceId = orderReference.getAmazonOrderReferenceId();
           setCookie('orderReferenceId', orderReferenceId, 1);
           location.reload();
        },
        onAddressSelect: function () {
            // do stuff here like recalculate tax and/or shipping
        },
        design: {
            designMode: 'responsive'
        },
        onError: function (error) {
            // your error handling code
        }
    }).bind("addressBookWidgetDiv");

    new OffAmazonPayments.Widgets.Wallet({
        sellerId: 'A1L458EVZY3PWM',
        onPaymentSelect: function () {
        },
        design: {
            designMode: 'responsive'
        },
        onError: function (error) {
            // your error handling code
        }
    }).bind("walletWidgetDiv");
</script>