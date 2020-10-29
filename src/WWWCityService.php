<?php

namespace Cy\WWWCityService;

use Cy\WWWCityService\MicroService\AGMicro;
use Cy\WWWCityService\MicroService\AreaMicro;
use Cy\WWWCityService\MicroService\FinanceMicro;
use Cy\WWWCityService\MicroService\OrgMicro;
use Cy\WWWCityService\MicroService\SmsMicro;
use Cy\WWWCityService\MicroService\UserMicro;

class WWWCityService
{
    private $config;

    public function __construct()
    {
        $this->config = config('wwwcityservice');
    }

    public function service(string $config_name)
    {
        $host = $this->config[$config_name]['HOST'];

        switch ($config_name) {
            case 'area':
                return new AreaMicro($host);
            case 'user':
                return new UserMicro($host);
            case 'sms':
                return new SmsMicro($host);
            case 'ag':
                return new AGMicro($host);
            case 'finance':
                return new FinanceMicro($host);
            case 'org':
                return new OrgMicro($host);
        }
    }
}
