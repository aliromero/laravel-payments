![alt text](https://github.com/abolfazlrastegar/laravel-payments/blob/main/laravel-payment.png?raw=true)

<p align="center">
<a href="https://packagist.org/packages/abolfazlrastegar/laravel-payments"><img src="https://img.shields.io/packagist/dt/abolfazlrastegar/laravel-payments" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/abolfazlrastegar/laravel-payments"><img src="https://img.shields.io/packagist/v/abolfazlrastegar/laravel-payments" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/abolfazlrastegar/laravel-payments"><img src="https://img.shields.io/github/license/abolfazlrastegar/laravel-payments" alt="License"></a>
</p>

### Laravel-payments
package laravel multi payment support form  (Zarinpal, Zibal, Idpay, Payir)

### Install package for laravel ^7
```bash
composer require aliromero/laravel-payments
```
### Publish config
```bash
  php artisan vendor:publish --tag="config"
```
### Docs drivers

<a href="https://docs.zarinpal.com/paymentGateway/guide/#%D8%A7%D8%B1%D8%B3%D8%A7%D9%84-%D8%A7%D8%B7%D9%84%D8%A7%D8%B9%D8%A7%D8%AA">zarinpal</a>

<a href="https://docs.zibal.ir/IPG/API">zibal</a>

<a href="https://idpay.ir/web-service/v1.1/#8614460e98">idpay</a>

<a href="https://docs.pay.ir/gateway/">payir</a>


### Use methode `request`
```bash
    Payment::create('IdPay')
        ->amount(10000)
        ->api(true) // time use form api add methode api()
        ->callbackUrl('http://127.0.0.1:8000/verify')
         ->infoUser([
            'name' => $user->name .' '. $user->family,
            'mobile' => $user->mobile,
            'email' => $user->email,
        ])
        ->request();
```
### or
```bash
    Payment::create('IdPay')
    ->amount(10000)
    ->callbackUrl('http://127.0.0.1:8000/verify')
     ->infoUser([
        'name' => $user->name .' '. $user->family,
        'mobile' => $user->mobile,
        'email' => $user->email,
    ])
    ->request();
```
### or
```bash
    Payment::create()
    ->defaultBank() // set name bank to payments/config
    ->api(true) // time use form api add methode api()
    ->amount(10000)
    ->callbackUrl('http://127.0.0.1:8000/verify')
     ->infoUser([
        'name' => $user->name .' '. $user->family,
        'mobile' => $user->mobile,
        'email' => $user->email,
    ])
    ->request();
```

### infoUser method `request`
| zarinpal | zibal | idpay     | payir |                                
|---------|-------|-----------|------|
|[<br/>'mobile' => $user->mobile,<br/>'email' => $user->email<br/>]  | [<br/>'mobile' => $user->mobile<br/>]  | [<br/>'name' => $user->name .' '. $user->family,<br/>'mobile' => $user->mobile,<br/>'email' => $user->email<br/>] | [<br/>'mobile' => $user->mobile,<br/> 'name' => $user->name .' '. $user->family<br/>] |

### Use methode `verify`
```bash
     Payment::create('IdPay')
        ->params(['id' => '612218274d3f1e755e1684fe7db63e35','order_id' => '1655381732'])
        ->verfiy();
```
### or 
```bash
     Payment::create()
        ->defaultBank() // set name bank to payments/config
        ->params(['id' => '612218274d3f1e755e1684fe7db63e35','order_id' => '1655381732']) 
        ->verify();
```
### Params method `verify`
| zarinpal | zibal | idpay     | payir |                                
|----------|-------|-----------|-------|
 |   [<br/>'amount' => 10000,<br/> 'authority' => 'A00000000000000000000000000202690354'<br/>]| $trackId = 15966442233311 |[<br/>'id' => "d2e353189823079e1e4181772cff5292",<br/>'order_id' => '101'<br/>] |    $token = "توکن پرداخت"   |

### Use methods zarinpal 
To read more go to the Docs <a href="https://docs.zarinpal.com/paymentGateway/setshare.html">zarinpal</a>
```bash
    // method checkout for Shared settlement
    
    Payment::create('Zarinpal')
       ->amount(10000)
       ->callbackUrl('http://127.0.0.1:8000/verify')
       ->api(true) // time use form api add methode api()
        ->params([
           [
               "iban" => "IR130570028780010957775103",
               "amount" => 1000,
               "description" => "....تسهیم سود فروش از محصول به "
           ],
           [
               "iban" => "IR670170000000352965862009",
               "amount" => 5000,
               "description" => "....تسهیم سود فروش از محصول به "
           ]
       ])
       ->checkout()
```
#### Method refund zarinpal
```bash
        // method refund for return amount to user
        
       Payment::create('Zarinpal')
       ->refund('A00000000000000000000000000243676791')
```
#### Method unVerified zarinpal
```bash
        // method unVerified for show payments unVerified On behalf of the user
        
       Payment::create('Zarinpal')
       ->unVerified()
```
### config 
```bash
    /*
    |----------------------------------------------
    | set type payment [ریال = rtr] [ تومان = rtt]
    |-----------------------------------------------
    */
    'currency' => 'rtt',

    /*
    |--------------------------------------------
    | set default payment
    |--------------------------------------------
    | from 'Zibal', 'PayIr', 'IdPay', 'Zarinpal'
    */

    'Default_payment' => 'IdPay',

    /*
    |-------------------------------------------
    | set description payment
    |-------------------------------------------
    */
    'Description_payment' => 'شارژ کیف پول',

    /*
    |-------------------------------------------
    | set test payment 
    |-------------------------------------------
    */
    'Test_payment' => false,

    /*
     |------------------------------------------
     | set setting drivers
     |------------------------------------------
     | active = 'true'  and inactive = 'false'
     */
    'drivers' => [
        'Zarinpal' => [
            'key' => '',
            'access_Token' => '',
            'status' => true,
            'api_test_request' => 'https://sandbox.zarinpal.com/pg/v4/payment/request.json',
            'api_test_py' => 'https://sandbox.zarinpal.com/pg/StartPay/',
            'api_test_verify' => 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json',
        ],

        'Zibal' =>  [
            'key' => '',
            'status' => true
        ],

        'PayIr' =>  [
            'key' => '',
            'status' => true
        ],

        'IdPay' =>  [
            'key' => '',
            'status' => true
        ],
    ]
```
