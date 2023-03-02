<?php

namespace Romero\LaravelPayments\Drivers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class Zibal implements Bank
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
            ->post('https://gateway.zibal.ir/v1/request', $this->setParams($amount, $callbackURL, $info_user));
        $response = json_decode($request->getBody()->getContents(), true);
        if ($response['result'] !== 100) {
            return ['message' => $response['message'], 'code'   => $response['result']];
        }
        if ($api)
        {
            $response['payment_url'] = 'https://gateway.zibal.ir/start/' . $response['trackId'];
            return $response;
        }
        header('Location:https://gateway.zibal.ir/start/' . $response['trackId']);exit();
    }

    /**
     * @param $Authority
     * @param $amount
     * @return mixed
     * @throws \Exception
     */
    public function verify($params)
    {
        $request = Http::withHeaders($this->setHeaders())->post('https://gateway.zibal.ir/v1/verify', [
                "merchant" => config('payments.drivers.Zibal.key'),
                "trackId" => $params['trackId']
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
            "merchant"=> config('payments.Test_payment') == false ? config('payments.drivers.Zibal.key') : 'zibal',
            "callbackUrl"=> $callbackURL,
            "amount"=> config('payments.currency') == 'rtt' ? $amount * 10 : $amount,
            "orderId"=> $info_user['orderId'],
            "allowedCards"=> $info_user['allowedCards'],
            "mobile"=> $info_user['mobile']
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
        ];
    }

    protected function resultCodes($code)
    {
        switch ($code)
        {
            case 100:
                return "با موفقیت تایید شد";

            case 102:
                return "merchant یافت نشد";

            case 103:
                return "merchant غیرفعال";

            case 104:
                return "merchant نامعتبر";

            case 201:
                return "قبلا تایید شده";

            case 105:
                return "amount بایستی بزرگتر از 1,000 ریال باشد";

            case 106:
                return "callbackUrl نامعتبر می‌باشد. (شروع با http و یا https)";

            case 113:
                return "amount مبلغ تراکنش از سقف میزان تراکنش بیشتر است.";

            case 202:
                return "سفارش پرداخت نشده یا ناموفق بوده است";

            case 203:
                return "trackId نامعتبر می‌باشد";

            default:
                return "وضعیت مشخص شده معتبر نیست";
        }
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
