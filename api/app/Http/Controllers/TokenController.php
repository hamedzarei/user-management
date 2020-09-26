<?php
/**
 * Created by PhpStorm.
 * User: zrhm7232
 * Date: 12/2/19
 * Time: 8:55 PM
 */

namespace App\Http\Controllers;


use App\Http\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;

class TokenController extends Controller
{
    public function issue(Request $request)
    {
//        validation
        $identity = $request->input('identity');
        $password = $request->input('password');
        $otp = $request->input('otp');

        $data = TokenService::issue($identity, $password, $otp);

        if ($data !== false) {
            return JsonResponse::create(
                [
                    'token' => $data['token']
                ]
            );
        }

        throw new UnauthorizedException();
    }

    public function check(Request $request)
    {
        $raw_token = $request->input('token');

        $validate = TokenService::validate($raw_token);

        if (!$validate) {
            return JsonResponse::create(
                [
                    'message' => 'not valid'
                ], Response::HTTP_UNAUTHORIZED
            );
        }

        return JsonResponse::create(
            [
                'message' => 'valid',
                'data' => TokenService::parse($raw_token)
            ]
        );
    }
}