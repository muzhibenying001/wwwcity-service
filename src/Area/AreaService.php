<?php

namespace Cy\WWWCityService\Area;

use Cy\WWWCityService\Libs\MicroService\AGRequest;

class AreaService
{
    private $host;

    public function __construct($host)
    {
        $this->host = $host;
        if (!$this->host) {
            abort(5003, '缺少请求host');
        }
    }

    # 查询 楼栋所属小区
    public function getNeighborhoodByBuilding($code)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/getNeighborhoodByBuilding',
            [
                'code' => $code,
            ]
        );
    }

    // 应用查询
    public function search($pid = -1, $code = '', $keyword = '', $skip = 0, $limit = 100)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/search',
            [
                'keyword' => $keyword,
                'pid' => $pid,
                'skip' => $skip,
                'limit' => $limit,
                'code' => $code,
            ]
        );
    }


    // 应用查询
    public function getByName($appName = '')
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/getByName',
            [
                'name' => $appName
            ]
        );
    }

    // 应用查询
    public function getByKeyword($keyword = '')
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/search',
            [
                'keyword' => $keyword
            ]
        );
    }

    // 应用查询
    public function getByCode($code = '')
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/getByCode',
            [
                'code' => $code,
            ]
        );
    }
}
