<?php

namespace Cy\WWWCityService\MicroService;

use Cy\WWWCityService\Libs\MicroService\AGRequest;
use Cy\WWWCityService\Libs\MicroService\BaseMicroService;

class AGMicro extends BaseMicroService
{
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
