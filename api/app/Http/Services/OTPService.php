<?php
/**
 * Created by PhpStorm.
 * User: zrhm7232
 * Date: 9/26/20
 * Time: 7:05 AM
 */

namespace App\Http\Services;


use App\Modules\Redis;

class OTPService
{
    public static function createItem($identity)
    {
        $otp = random_int(env('OTP_MIN', 10000), env('OTP_MAX', 99999));

        Redis::setUserOTP($identity, $otp);

        return $otp;
    }

    public static function checkItem($identity, $claimedOtp)
    {
        $otp = Redis::getUserOTP($identity);

        if ($otp == (string)$claimedOtp) return true;
        return false;
    }
}