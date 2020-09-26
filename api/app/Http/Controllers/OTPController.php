<?php
/**
 * Created by PhpStorm.
 * User: zrhm7232
 * Date: 9/26/20
 * Time: 6:57 AM
 */

namespace App\Http\Controllers;


use App\Http\Services\OTPService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OTPController extends Controller
{
    use Common;

    public function createItem(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'identity' => 'required'
        ]);

        if ($validate->fails()) {
            throw new
            BadRequestHttpException('msg_'.json_encode($validate->failed()));
        }

        $identity = $request->input('identity');
        // create new otp
        $otp = OTPService::createItem($identity);

        return new JsonResponse([
            'data' => [
                'otp' => $otp
            ]
        ]);
    }
}