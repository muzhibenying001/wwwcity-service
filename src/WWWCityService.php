<?php

namespace Cy\WWWCityService;

use Cy\WWWCityService\Area\AreaService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

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
                return new AreaService($host);
            case 'user':
                break;
            case 'sms':
                break;
        }
    }

    private function request($host)
    {
        return HTTP::post($host);
    }


}
