<?php

namespace Cy\WWWCityService;

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

        $class = 'Cy\WWWCityService\MicroService\\' . ucwords($config_name) . 'Micro';

        if (class_exists($class)) {
            return new $class($host, $config_name);
        }

        abort(5000, '微服务' . $config_name . '未开发');
    }
}
