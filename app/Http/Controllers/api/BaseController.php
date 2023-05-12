<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected $_messages = [];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_messages = config('validate.messages');
    }

    protected function responseWithToken($token, $refreshToken = null, $refreshTTL = null)
    {
        $result = [
            'access_token' => $token,
            'refresh_token' => $refreshToken ?? null,
            'refresh_ttl' => $refreshTTL ? $refreshTTL * 60 : null
        ];
        return response()->json([
            'status' => 'OK',
            'result' => $result
        ]);
    }

    protected function responseWithAccessTokenWhenRefresh($token)
    {
        $result = [
            'access_token' => $token,
        ];
        return response()->json([
            'status' => 'OK',
            'result' => $result
        ]);
    }

    protected function responseWithAccessToken($token, $expires)
    {
        return response()->json([
            'status' => 'OK',
            'result' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $expires,
            ]
        ]);
    }

    protected function responseWithError(int $errorCode, int $statusCode = 400)
    {
        return response()->json([
            'status' => 'NG',
            'error' => [
                [
                    'code' => $errorCode,
                    'message' => $this->_messages[$errorCode] ?? 'Unknown error.'
                ]
            ],
        ], $statusCode);
    }

    public function getMsgError(array $errors = []): array
    {
        $errorArray = [];
        foreach ($errors as $field => $error) {
            $code = (int)($error[0] ?? '');
            $errorArray[] = [
                'code' => $code,
                'message' => $this->_messages[$code] ?? 'Unknown error.'
            ];
        }
        return $errorArray;
    }

    protected function responseWithSuccessByCard(array $data)
    {
        return response()->json([
            'status' => 'OK',
            'result' => $data
        ]);
    }


    protected function responseWithSuccessCode(int $successCode, int $statusCode = 200)
    {
        return response()->json([
            'status' => 'OK',
            'result' => [
                [
                    'code' => $successCode,
                    'message' => $this->_messages[$successCode] ?? ''
                ]
            ],
        ], $statusCode);
    }
}
