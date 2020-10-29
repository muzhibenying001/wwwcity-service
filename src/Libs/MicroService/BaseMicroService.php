<?php

namespace Cy\WWWCityService\Libs\MicroService;

class BaseMicroService
{
    protected $host;

    public function __construct($host)
    {
        $this->host = $host;
        if (!$this->host) {
            abort(5003, '缺少请求host');
        }
    }
}
