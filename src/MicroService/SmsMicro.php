<?php

namespace Cy\WWWCityService\MicroService;

use Cy\WWWCityService\Libs\MicroService\AGRequest;
use Cy\WWWCityService\Libs\MicroService\BaseMicroService;

class SmsMicro extends BaseMicroService
{
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
