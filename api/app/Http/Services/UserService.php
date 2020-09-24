<?php

/**
 * Created by PhpStorm.
 * User: zrhm7232
 * Date: 12/2/19
 * Time: 6:52 AM
 */
namespace App\Http\Services;

use App\User;
use App\UserRef;
use App\Wallet;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserService
{
    public static $msg_mobile_exist = 'شماره موبایل وارد شده موجود است';
    public static $msg_email_exist = 'ایمیل وارد شده موجود است';

    public static function createItem($data)
    {
//        checking existing user
        if (array_key_exists('mobile', $data)) {
            $user = User::getItemBy('mobile', $data['mobile'], false);
            if ($user) {
                throw new
                BadRequestHttpException('msg_'.trans('error.user_exist_mobile'));
            }
        }

        if (array_key_exists('email', $data)) {
            $user = User::getItemBy('email', $data['email'], false);
            if ($user) {
                throw new
                BadRequestHttpException('msg_'.trans('error.user_exist_mobile'));
            }
        }

//        create item
        $user = User::createItem($data);

        return $user;
    }

    public static function getItems()
    {
        $items = User::getItems();

        return [
            'total' => count($items['data']),
            'data' => $items['data']
        ];
    }

    public static function getItemBy($by, $value)
    {
        $item = User::getItem($by, $value);

        return $item;
    }

    public static function updateItem($user_id, $id, $data)
    {
        if ($user_id != $id) throw new UnauthorizedException();

        $item = User::updateItem($id, $data);

        return [
            'data' => $item
        ];
    }
}