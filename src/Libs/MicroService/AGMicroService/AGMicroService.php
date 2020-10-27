<?php

namespace App\Libs\MicroService\AGMicroService;


use App\Libs\MicroService\AGRequest;
use App\Libs\MicroService\BaseMicroService;


// AG微服务专用类
class AGMicroService extends BaseMicroService
{

	private $host;

	public function __construct()
	{
		$this->host = env('AGMicroService_host');
		if (!$this->host) {
			throw new \Exception('缺少请求host', 5003);
		}

	}

	// 应用查询
	public function appSearch($appName = '', $status = 1, $skip = 0, $limit = 10)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/app/search',
			[
				'appName' => $appName,
				'status ' => $status,
				'skip' => $skip,
				'limit' => $limit,
			]
		);
	}

	// 应用查询
	public function appGet($appId)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/app/get',
			[
				'appId' => $appId,
			]
		);
	}
}