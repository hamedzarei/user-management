<?php
/**
 * Created by PhpStorm.
 * User: zrhm7232
 * Date: 12/2/19
 * Time: 9:01 PM
 */

namespace App\Http\Services;


use App\Helpers\IdentityHelper;
use App\User;
use Illuminate\Validation\UnauthorizedException;
use Lcobucci\Clock\Clock;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Illuminate\Support\Facades\Hash;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\JWT\Validation\InvalidToken;

class TokenService
{
    public static function issue($identity, $password)
    {
        $type = IdentityHelper::getType($identity);

        $user = User::getItem($type, $identity, ['password']);

        if (Hash::check($password, $user['password'])) {
//            return true;
            return [
                'user' => $user,
                'token' => self::issueToken($user)
            ];
        }

        return false;
    }

    public static function validate($raw_token)
    {
        $config = self::configuration();
        $token = $config->getParser()->parse($raw_token);
        $constraints = $config->getValidationConstraints();
//        dd($constraints);

//        $token = $config->getParser()->parse(
//            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.'
//            . 'eyJzdWIiOiIxMjM0NTY3ODkwIn0.'
//            . '2gSBz9EOsQRN9I-3iSxJoFt7NtgV6Rm0IL6a8CAwl3Q'
//        );
//        dd($token);
        try {
            $config->getValidator()->assert($token, ...$constraints);
        } catch (InvalidToken $e) {
            // list of constraints violation exceptions:
            return false;
        }

        return true;
    }

    public static function parse($raw_token)
    {
        $config = self::configuration();
        $token = $config->getParser()->parse($raw_token);
        if (!($token instanceof Plain)) {
            throw new UnauthorizedException();
        }

        return [
            'headers' => $token->headers()->all(),
            'claims' => $token->claims()->all()
        ];
    }

    private static function issueToken($user)
    {

//        $config = $container->get(Configuration::class);
//        $config = Configuration::class;

        $config = self::configuration();
        assert($config instanceof Configuration);

        $clock   = new SystemClock();
        $now = $clock->now();
        $token = $config->createBuilder()
//            // Configures the issuer (iss claim)
//            ->issuedBy('http://example.com')
//            // Configures the audience (aud claim)
//            ->permittedFor('http://example.org')
            // Configures the id (jti claim)
            ->identifiedBy(uniqid())
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the time that the token can be used (nbf claim)
            ->canOnlyBeUsedAfter($now->modify('+1 second'))
            // Configures the expiration time of the token (exp claim)
//            ->expiresAt($now->modify('+1 hour'))
            // Configures a new claim, called "uid"
            ->withClaim('uid', $user['id'])
            // Builds a new token
            ->getToken($config->getSigner(), $config->getSigningKey());

        return (string)$token;
    }

    private static function configuration()
    {
        $now   = new \DateTimeImmutable();
        $clock = new SystemClock();
        $private_key = 'file:///'.dirname(dirname(dirname(__DIR__))).'/keys/private.key';
        $configuration = Configuration::forSymmetricSigner(
        // You may use any HMAC variations (256, 384, and 512)
            new Sha256(),
            // replace the value below with a key of your own!
//            new Key($private_key),
            new Key('mBC5v1sOKVvbdEitdSBenu59nfNfhwkedkJVNabosTw=')
        );
        $configuration->setValidationConstraints(new ValidAt($clock));
        return $configuration;
    }
}