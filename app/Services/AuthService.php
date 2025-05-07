<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{

    private const ACCESS_TOKEN_EXPIRY = 1; // 1 Day
    private const REFRESH_TOKEN_SHORT_EXPIRY = 7; // 7 Days
    private const REFRESH_TOKEN_LONG_EXPIRY = 30; // 30 days

    private $accessTokenExpiry;
    private $refreshTokenShortExpiry;
    private $refreshTokenLongExpiry;

    public function __construct()
    {
        $this->accessTokenExpiry = Carbon::now()->addDays(self::ACCESS_TOKEN_EXPIRY)->timestamp;
        $this->refreshTokenShortExpiry = Carbon::now()->addDays(self::REFRESH_TOKEN_SHORT_EXPIRY)->timestamp;
        $this->refreshTokenLongExpiry = Carbon::now()->addDays(self::REFRESH_TOKEN_LONG_EXPIRY)->timestamp;
    }


    public function login($request)
    {

        $result = $this->authenticateAndGetTokens($request);

        if (isset($result['status_code']) && $result['status_code'] !== 200) {
            return $result;
        }

        return $this->userWithTokens(
            $result['access_token'],
            $result['refresh_token'],
            $result['refresh_token_expires_in']
        );
    }
    private function authenticateAndGetTokens($request)
    {
        $credentials = $request->only('email', 'password');
        $accessToken = Auth::attempt($credentials);

        if (!$accessToken) {
            return [
                'message' => 'Unauthorized',
                'status_code' => 401,
            ];
        }

        $user = Auth::user();

        if (!$user) {
            return [
                'message' => 'User not found',
                'status_code' => 404,
            ];
        }

        $refreshTokenExpiry = $request->remember ? $this->refreshTokenLongExpiry : $this->refreshTokenShortExpiry;
        $refreshToken = $this->generateRefreshToken($user, $refreshTokenExpiry);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'refresh_token_expires_in' => $refreshTokenExpiry,
        ];
    }

    private function generateRefreshToken($user, $expiry)
    {
        $expireIn = Carbon::now()->addDays($expiry)->timestamp;
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc')); // initialization vector (IV) adds extra security
        $dataToEncrypt = $user->id . '|' . $user->email . '|' . $expireIn . '|' . $iv;

        return Crypt::encrypt($dataToEncrypt);
    }

    private function userWithTokens($accessToken, $refreshToken, $refreshTokenExpiry)
    {

        return [
            'access_token' => $accessToken,
            'expires_in' => $this->accessTokenExpiry,
            'refresh_token' => $refreshToken,
            'refresh_token_expires_in' => $refreshTokenExpiry,
            'user' => Auth::user(),
        ];
    }
}
