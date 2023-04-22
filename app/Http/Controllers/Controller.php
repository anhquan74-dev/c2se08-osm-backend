<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	private array $_messages;

	public function __construct(){
		$this->_messsage = config('validate.messages');
	}

	protected function responseWithError(int $errorCode, int $statusCode = 400)
	{
		return response()->json([
			'status' => 'NG',
			'error' => [
				[
					'code' => $errorCode,
					'message' => $this->_messages[$errorCode] ?? ''
				]
			],
		], $statusCode);
	}

	public function getMsgError(array $errors = []): array
	{
		$errorArray = [];
		$seenCodes = [];
		foreach ($errors as $field => $error) {
			$code = (int)($error[0] ?? '');
			if (!in_array($code, $seenCodes)) {
				$errorArray[] = [
					'code' => $code,
					'message' => $this->_messages[$code] ?? ''
				];
				$seenCodes[] = $code;
			}
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

	protected function responseWithSuccess(int $successCode, int $statusCode = 200)
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

	protected function getStrPadAttribute($id, $length = 8, $pad_string = '0', $pad_stype = STR_PAD_LEFT)
	{
		return str_pad($id, $length, $pad_string, $pad_stype);
	}
	
	

}
