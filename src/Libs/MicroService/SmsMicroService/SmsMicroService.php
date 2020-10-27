<?php

namespace App\Libs\MicroService\SmsMicroService;


use App\Libs\MicroService\AGRequest;
use App\Libs\MicroService\BaseMicroService;


// 短信微服务专用类
class SmsMicroService extends BaseMicroService
{
	private $host;

	public function __construct()
	{
		$this->host = env('SmsMicroService_host');
		if (!$this->host) {
			throw new \Exception('缺少请求host', 5003);
		}
	}

	public function send($mobile, $content, $sign, $type = 0)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/send',
			[
				'mobile' => $mobile,
				'content' => $content,
				'sign' => $sign,
				'type' => $type,
			]
		);
	}
}