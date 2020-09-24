<?php
/**
 * Created by PhpStorm.
 * User: zrhm7232
 * Date: 2/5/19
 * Time: 10:03 AM
 */

namespace App\Http\Controllers;

use App\Exceptions\NoItemInRequestException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait Common
{
    public static function getRequestItems(Request $request, $keys)
    {
        $keys = array_intersect($request->keys(), $keys);
        if ($keys) {
            $data = $request->all($keys);
        } else {
            throw new BadRequestHttpException('msg_'.trans('error.no_data'));
        }

        return $data;
    }
}