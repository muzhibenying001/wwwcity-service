<?php

namespace Cy\WWWCityService\Libs\MicroService;

class BaseMicroService
{
    protected $host;
    protected $config_name;

    public function __construct($host, $config_name = '')
    {
        $this->host = $host;
        $this->config_name = $config_name;
        if (!$this->host) abort(5003, '缺少请求host');
    }

    /**
     * 判断值在参数中是否存在
     * @param $value
     * @param $key
     */
    protected function isSet($value, $key)
    {
        if (is_array($key)) {
            foreach ($key as $item) {
                if (!isset($value[$item]) && empty($value[$item])) abort(422, '【' . $this->config_name . '-微服务】' . $item . '不能为空');
            }
        } else {
            if (!isset($value[$key]) && empty($value[$key])) abort(422, '【' . $this->config_name . '-微服务】' . $key . '不能为空');
        }

    }
}
