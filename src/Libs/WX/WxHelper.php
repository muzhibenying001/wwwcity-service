<?php
/**
 * Created by PhpStorm.
 * User: zyw
 * Date: 2020/5/6
 * Time: 11:59 AM
 */

namespace App\Libs\WX;


class WxHelper
{

	public $OK = 0;
	public $IllegalAesKey = -41001;
	public $IllegalIv = -41002;
	public $IllegalBuffer = -41003;
	public $DecodeBase64Error = -41004;
	public $wx_app_id;
	public $wx_app_secret;
	
	public function __construct()
	{
		$this->wx_app_id = env('WX_APPID');
		$this->wx_app_secret = env('WX_APPSECRET');
	}


	/**
	 * 获取微信加密数据
	 * @param $code
	 * @param $encryptedData
	 * @param $iv
	 * @param $grantType
	 * @return array|string
	 */
	public function getWxData($code, $encryptedData, $iv, $grantType){
		$url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $this->wx_app_id . '&secret=' . $this->wx_app_secret . '&js_code=' . $code . '&grant_type=' . $grantType;
		$data = file_get_contents($url);
		$access = json_decode($data, true);

		if (isset($access['session_key'])){
			return $this->decryptData($access['session_key'], $encryptedData, $iv);
		}else{
			return [];
		}
	}


	/**
	 * 检验数据的真实性，并且获取解密后的明文.
	 * @param $encryptedData string 加密的用户数据
	 * @param $iv string 与用户数据一同返回的初始向量
	 * @param $data string 解密后的原文
	 *
	 * @return
	 */
	private function decryptData($sessionKey, $encryptedData, $iv)
	{
		if (strlen($sessionKey) != 24) {
			// return $this->IllegalAesKey;
			return [];
		}
		$aesKey=base64_decode($sessionKey);

		if (strlen($iv) != 24) {
			// return $this->IllegalIv;
			return [];
		}
		$aesIV=base64_decode($iv);

		$aesCipher=base64_decode($encryptedData);

		$result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

		$dataObj=json_decode( $result );
		if( $dataObj  == NULL )
		{
			// return $this->IllegalBuffer;
			return [];
		}
		if( $dataObj->watermark->appid != $this->wx_app_id )
		{
			// return $this->IllegalBuffer;
			return [];
		}
		return $dataObj;
	}

}