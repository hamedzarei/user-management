<?php


namespace App\Modules;


use Predis\Client;

class Redis
{
    /**
     * @var null
     */
    private static $redis = null;

    // OTP for user
    /**
     * @param $identity
     * @param $otp
     */
    public static function setUserOTP($identity, $otp)
    {
        $redis = self::createRedisClient();
        $redis->set("$identity:otp", $otp);
    }

    public static function getUserOTP($identity)
    {
        $redis = self::createRedisClient();

        $item = $redis->get("$identity:otp");

        return $item;
    }

    /*
     * redis open connection
     */
    /**
     * @return null|Client
     */
    private static function createRedisClient()
    {
        if (self::$redis) {
            return self::$redis;
        } else {
            self::$redis = new Client(
                [
                    'scheme' => 'tcp',
                    'host'   => env('REDIS_HOST', '127.0.0.1'),
                    'port'   => env('REDIS_PORT', 6379)
                ]
            );

            return self::$redis;
        }
    }

    /*
     * redis close connection
     */
    /**
     *
     */
    private static function quitRedisClient()
    {
        $redis = self::$redis;

        $redis->quit();

        self::$redis = null;
    }
}
