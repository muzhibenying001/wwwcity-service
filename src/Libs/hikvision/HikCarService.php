<?php

namespace App\Libs\hikvision;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class HikCarService
{
	/**
	 * 对接海康车辆管理
	 */
	private $http;
	private $appKey = '25045342';
	private $appSecret = 'LnpVX7qVQzkLr2eRlDQb';

	public function __construct()
	{
		$this->http = new Client(['base_uri' => 'https://59.108.66.163:443', 'verify' => false]);
	}

	//停车库编码 -- 燕和园-海康
	private $parkCode = 'e95ad6e8-f493-11ea-9481-9785dd46c108';
	// 出入口
	private $entrances = [
		[
			'name' => '',
			'code' => ''
		],
		[
			'name' => '',
			'code' => ''
		]
	];

	/**
	 * 登记车辆
	 * https://open.hikvision.com/docs/39e158fce3564a768cae097bde704dbe#bb06a569
	 * @param $cars
	 * @return mixed
	 * @throws Exception
	 */
	public function addCars($cars)
	{
		$result = self::requestHik('/api/resource/v1/vehicle/batch/add', ['object' => $cars]);
		$car = $result['successes'];
		if (!empty($car)) {
			return $car[0];
		} else {
			throw new \Exception('添加车辆失败！', 2201);
		}
	}

	/**
	 * 删除车辆
	 * https://open.hikvision.com/docs/39e158fce3564a768cae097bde704dbe#b250bd27
	 * @param $ids
	 * @return mixed
	 */
	public function deleteCar($ids)
	{
		$result = self::requestHik('/api/resource/v1/vehicle/batch/delete', ['vehicleIds' => $ids]);
		return $result;
	}


	/**
	 * 停车位剩余量
	 * https://open.hikvision.com/docs/2b8e9d7976da45c09ec66362a86f30f7#c0be693e
	 * @return mixed
	 */
	public function usableTruckSpace()
	{
		// 车库
		// https://open.hikvision.com/docs/39e158fce3564a768cae097bde704dbe#d93e4991
		// $result = self::requestHik('/api/resource/v1/park/parkList', []);
		// dd($result);
		$result = self::requestHik('/api/resource/v1/vehicle/batch/delete', ['parkSyscode' => $this->parkCode]);
		return $result;
	}

	/**
	 * 车辆出入记录
	 * https://open.hikvision.com/docs/2b8e9d7976da45c09ec66362a86f30f7#e6fad46f
	 * @param $startTime
	 * @param $endTime
	 * @param $pageNo
	 * @param $limit
	 * @return mixed
	 */
	public function inOutLog($startTime, $endTime, $pageNo, $limit)
	{
		$data = [
			'startTime' => gmdate(DATE_ATOM, $startTime),
			'endTime' => gmdate(DATE_ATOM, $endTime),
			'pageNo' => $pageNo,
			'pageSize' => $limit
		];
		$result = self::requestHik('/api/pms/v1/crossRecords/page', $data);
		return $result;
	}

	/**
	 * 获取车辆出入抓拍的图片
	 * https://open.hikvision.com/docs/2b8e9d7976da45c09ec66362a86f30f7#a47fc46d
	 * @param $aswSyscode
	 * @param $picUri
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getLogImage($aswSyscode, $picUri)
	{
		$data = [
			'aswSyscode' => $aswSyscode,
			'picUri' => $picUri
		];
		return self::requestHik('/api/pms/v1/image', $data);
	}


	/**
	 * 请求海康
	 * @param $api
	 * @param $data
	 * @param string $method
	 * @return mixed
	 * @throws Exception
	 */
	public function requestHik2($api, $data, $method = 'POST')
	{
		// $str = "POST\n*/*\ntext/plain;charset=UTF-8\nheader-a:A\nheader-b:b\nx-ca-key:29666671\nx-ca-timestamp:1479968678000\n/artemis/api/example?a-body=a&qa=a&qb=B&x-body=x";
		// $hash = hash_hmac('sha256', utf8_encode($str), utf8_encode('empsl21ds3'), true);
		// // dd($hash);
		// $sign = base64_encode($hash);
		// dd($str, $sign);


		dd($this->getToken());

		try {
			$api = '/artemis' . $api;
			// dd($api, $data);
			$md = md5(json_encode($data), true);
			$content_MD5 = base64_encode($md);

			// dd($md, $content_MD5);

			$date_str = date('D, d M Y H:i:s T', time());

			$headers = [
				'X-Ca-Key' => $this->appKey
			];

			$str = $method . "\n*/*\n"  . $content_MD5 . "\n" . 'application/json' . "\n" . $date_str . "\n";
			// $str = $method . '\n*/*\n' . '\n' . 'application/json' . '\n' . $date_str . '\n';
			// $str = $method . '\n';
			foreach ($headers as $key=>$value){
				$str = $str . strtolower($key) . ':' . $value . "\n";
			}
			if (empty($data)) {
				$str = $str . $api;
			} else {
				$temp = [];
				foreach ($data as $key => $value) {
					$temp[] = strtolower($key) . '=' . $value;
				}
				$str = $str . $api . '?' . join('&', $temp);
			}
			// dd($str);

			// $hash = hash_hmac('sha256', $str, $this->appSecret);
			$hash = hash_hmac('sha256', utf8_encode($str), utf8_encode($this->appSecret), true);
			// dd($hash);
			$sign = base64_encode($hash);
			dd($str, $sign);

			$headers['Content-MD5'] = $content_MD5;
			$headers['Date'] = $date_str;
			$headers['X-Ca-Signature'] = $sign;
			$headers['X-Ca-Signature-Headers'] = 'user-agent,x-ca-key';
			$headers['Connection'] = 'keep-alive';
			$headers['Content-Type'] = 'application/json';
			$headers['Accept'] = '*/*';
			// $headers['secret'] = $this->appSecret;

			// dd(json_encode($headers));

			$http = new Client(['base_uri' => 'https://59.108.66.163:443', 'headers' => $headers, 'verify' => false]);
			$configs = $http->getConfig();
			// dd(json_encode($configs['headers']));
			// dd($configs['headers']);

			app('log')->info('请求海康威视接口：' . $api);

			$response = $http->request($method, $api, ['json' => $data]);
		}catch (\Exception $e){
			app('log')->info($e->getMessage() . $e->getTraceAsString());
			throw new \Exception(' 【海康威视】 请求错误：' . $e->getMessage(), 2222);
		}

		dd(json_decode((string)$response->getBody()), true);

		app('log')->info('返回结果：' . json_encode($response));
		if ($response['code'] == 0) {
			return $response['data'];
		} else {
			throw new \Exception(time() . ' 【海康威视】 请求错误：' . $response['message'], 2201);
		}
	}

	/**
	 * 请求海康
	 * @param $api
	 * @param $data
	 * @param string $method
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function requestHik($api, $data, $method = 'POST')
	{
		$token = $this->getToken();
		try {
			$api = '/artemis' . $api;
			$headers = [
				'access_token' => $token,
				'Content-Type' => 'application/json',
				'Accept' => '*/*',
				'Content-Length' => 0
			];
			$http = new Client(['base_uri' => 'https://59.108.66.163:443', 'headers' => $headers, 'verify' => false]);
			$configs = $http->getConfig();
			// dd(json_encode($configs['headers']));
			app('log')->info('请求海康威视接口：' . $api);
			// dd($method, $api, $data, json_encode($configs['headers']));
			$response = $http->request($method, $api, ['json' => $data]);
		}catch (\Exception $e){
			app('log')->info($e->getMessage() . $e->getTraceAsString());
			dd($e->getMessage() . $e->getTraceAsString());
			throw new \Exception(' 【海康威视】 请求错误：' . $e->getMessage(), 2222);
		}
		$body = json_decode((string)$response->getBody(), true);
		// dd($body);
		app('log')->info('返回结果：' . json_encode($response));
		if ($body['code'] == 0) {
			return $body['data'];
		} else {
			throw new \Exception(time() . ' 【海康威视】 请求错误：' . $response['message'], 2202);
		}
	}

	/**
	 * 海康请求token
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getToken()
	{
		$token = Cache::get('HIK_ACCESS_TOKEN');
		if (!empty($token)){
			return $token;
		}
		try {
			$api = '/artemis/api/v1/oauth/token';
			$date_str = date('D, d M Y H:i:s T', time());
			$headers = [
				'X-Ca-Key' => $this->appKey
			];
			$str = 'POST\n';
			foreach ($headers as $key=>$value){
				$str = $str . strtolower($key) . ':' . $value . '\n';
			}
			$str = $str . $api;
			// dd($str);
			$hash = hash_hmac('sha256', utf8_encode($str), utf8_encode($this->appSecret), true);
			$sign = base64_encode($hash);
			$headers['Date'] = $date_str;
			$headers['X-Ca-Signature'] = $sign;
			$headers['X-Ca-Signature-Headers'] = 'x-ca-key';
			$headers['Content-Type'] = 'application/json';
			$headers['Accept'] = '*/*';
			$headers['secret'] = $this->appSecret;
			$http = new Client(['base_uri' => 'https://59.108.66.163:443', 'headers' => $headers, 'verify' => false]);
			$configs = $http->getConfig();
			// dd(json_encode($configs['headers']));
			// dd($configs['headers']);
			app('log')->info('请求海康威视接口：' . $api);
			$response = $http->request('POST', $api, []);
		}catch (\Exception $e){
			app('log')->info($e->getMessage() . $e->getTraceAsString());
			throw new \Exception(' 【海康威视】 请求错误：' . $e->getMessage(), 2222);
		}
		$body = json_decode((string)$response->getBody(), true);
		// dd(\GuzzleHttp\json_encode($body));
		app('log')->info('返回结果：' . json_encode($response));
		if ($body['code'] == 0) {
			$access_token = $body['data']['access_token'];
			// 缓存token 12小时内有效
			Cache::put('HIK_ACCESS_TOKEN', $access_token, 715);
			return $access_token;
		} else {
			throw new \Exception(time() . ' 【海康威视】 请求错误：' . $response['message'], 2201);
		}
	}



}