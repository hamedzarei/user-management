<?php

namespace App\Http\Controllers;

use App\Helpers\IdentityHelper;
use App\Http\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends Controller
{
    use Common;

    public $update_attributes = [
        'name',
    ];

    public function createItem(Request $request)
    {
//        validation
        $validate = Validator::make($request->all(), [
            'email'       => [
                'required_without:mobile',
                'regex:'.IdentityHelper::$TYPE_REGEX[1]
            ],
            'password'    => 'required|min:8',
            'mobile'      => [
                'required_without:email',
                'regex:'.IdentityHelper::$TYPE_REGEX[0]
            ]
        ]);

        if ($validate->fails()) {
            throw new
                BadRequestHttpException('msg_'.json_encode($validate->failed()));
        }
//        get required args
        $data = $request->all();
//        create user
        $user = UserService::createItem($data);

        return new JsonResponse([
            'data' => $user
        ]);
    }

    public function getItems(Request $request)
    {
        $items = UserService::getItems();

        return JsonResponse::create($items);
    }

    public function getItem(Request $request, $id)
    {
        $item = UserService::getItemBy('id', $id);

        return JsonResponse::create([
            'data' => $item
        ]);
    }

    public function updateItem(Request $request, $id)
    {
        $user_id = $request->header('x-user-id');

        $data = self::getRequestItems($request, $this->update_attributes);

        $item = UserService::updateItem($user_id, $id, $data);

        return JsonResponse::create($item);
    }
}
