<?php

namespace Cy\WWWCityService\MicroService;

use Cy\WWWCityService\Libs\MicroService\AGRequest;
use Cy\WWWCityService\Libs\MicroService\BaseMicroService;

class ShellMicro extends BaseMicroService
{
    /**
     * 根据uuid查询居民出入卡
     * @param $uuid
     * @return mixed
     */
    public function doorUserGet($idcard = '', $uuid = '')
    {
        return AGRequest::getInstance()->post($this->host, '/door/user/get', [
            'uuid' => $uuid,
            'idcard' => $idcard,
        ]);
    }

    /**
     * 出入卡用户列表
     * @return mixed
     */
    public function doorUserList($neighborhood, $startTime, $endTime, $page = 0, $size = 20)
    {
        return AGRequest::getInstance()->post($this->host, '/door/user/list', [
            'neighborhood' => $neighborhood,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'page' => $page,
            'size' => $size,
        ]);
    }
}
