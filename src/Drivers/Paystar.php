<?php

namespace Romero\LaravelPayments\Drivers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class Paystar implements Bank
{
    /**
     * send request Payment towards zibal
     * @param $amount
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function request($api, $amount, $callbackURL, $info_user)
    {
        $request = Http::withHeaders($this->setHeaders())
            ->post('https://core.paystar.ir/api/pardakht/create', $this->setParams($amount, $callbackURL, $info_user));
        $response = json_decode($request->getBody()->getContents(), true);
        if ($response['status'] !== 1) {
            return ['message' => $response['message'], 'code'   => $response['status']];
        }
        if ($api)
        {
            $response['payment_url'] = 'https://core.paystar.ir/api/pardakht/payment' . $response['data']['token'];
            return $response;
        }
        header('Location:https://core.paystar.ir/api/pardakht/payment' . $response['data']['token']);exit();
    }

    /**
     * @param $Authority
     * @param $amount
     * @return mixed
     * @throws \Exception
     */
    public function verify($params)
    {
        $request = Http::withHeaders($this->setHeaders())->post('https://core.paystar.ir/api/pardakht/verify', [
                "ref_num" => $params['ref_num'],
                "amount" => $params['amount'],
            'sign' =>
                hash_hmac(
                    'SHA512',
                    $params['amount'] . '#' . $params['ref_num'] . '#' . $params['card_number'] . "#" . $params['tracking_code'],
                    config('payments.drivers.Paystar.gatewayId')
                ),
            ]);
        $response = json_decode($request->getBody()->getContents(), true);

        return [
            "data" => $response,
            "result" => $response['result'],
            "msg" => $this->resultCodes($response['result']),
        ];

    }

    /**
     * @param $amount
     * @param $callbackURL
     * @return array
     */
    private function setParams ($amount, $callbackURL, $info_user) {
        return [
            "callback"=> $callbackURL,
            "amount"=> config('payments.currency') == 'rtt' ? $amount * 10 : $amount,
            "order_id"=> $info_user['orderId'],
            "card_number"=> $info_user['allowedCard'],
            'sign' =>
                hash_hmac(
                    'SHA512',
                    $amount . '#' . $info_user['orderId'] . '#' . $callbackURL,config('payments.drivers.Paystar.gatewayId')
                ),
        ];
    }

    /**
     * @return string[]
     */
    private function setHeaders () {
        return [
            'Accept: application/json',
            'charset: utf-8',
            'Content-Type: application/json',
            'Authorization: Bearer ' . config('payments.drivers.Paystar.gatewayId'),
        ];
    }

    protected function resultCodes($code)
    {
       return "خطای شماره " . $code;

    }
    protected function statusCodes($code)
    {
        switch ($code)
        {
            case -1:
                return "در انتظار پردخت";

            case -2:
                return "خطای داخلی";

            case 1:
                return "پرداخت شده - تاییدشده";

            case 2:
                return "پرداخت شده - تاییدنشده";

            case 3:
                return "لغوشده توسط کاربر";

            case 4:
                return "‌شماره کارت نامعتبر می‌باشد";

            case 5:
                return "‌موجودی حساب کافی نمی‌باشد";

            case 6:
                return "رمز واردشده اشتباه می‌باشد";

            case 7:
                return "‌تعداد درخواست‌ها بیش از حد مجاز می‌باشد";

            case 8:
                return "‌تعداد پرداخت اینترنتی روزانه بیش از حد مجاز می‌باشد";

            case 9:
                return "مبلغ پرداخت اینترنتی روزانه بیش از حد مجاز می‌باشد";

            case 10:
                return "‌صادرکننده‌ی کارت نامعتبر می‌باشد";

            case 11:
                return "خطای سوییچ";

            case 12:
                return "کارت قابل دسترسی نمی‌باشد";

            default:
                return "وضعیت مشخص شده معتبر نیست";
        }
    }
}
