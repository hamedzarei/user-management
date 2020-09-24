<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'mobile', 'password', 'status'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    public static function createItem($data)
    {
        $item = self::create($data);

        return $item;
    }

    public static function getItem($by, $value, $visible = [])
    {
        $item = self::where($by, $value)->firstOrFail();

        return $item->makeVisible($visible)->toArray();
    }

    public static function getItems($take = 10, $skip = 0)
    {
        $items = self::take($take)->skip($skip)
            ->get();

        if (count($items) > 0) {

            $items = $items->toArray();
            return [
                'data' => $items
            ];
        }

        throw new ModelNotFoundException();
    }

    //    for example: get last item for each person
    public static function getItemBy($by, $value, $throw_exception = true)
    {
        if ($throw_exception) {
            $item = self::where($by, $value)->firstOrFail();
        } else {
            $item = self::where($by, $value)->first();
        }

        if ($item) {
            return $item->toArray();
        }

        return [];
    }

    public static function updateItem($id, $data)
    {
        $item = self::findOrFail($id);
        $item->update($data);

        return $item;
    }


    /*
 * accessors, mutators
 */

    /**
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
