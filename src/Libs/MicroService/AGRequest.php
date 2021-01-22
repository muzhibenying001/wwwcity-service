<?php

namespace Cy\WWWCityService\Libs\MicroService;

// AG请求基类
use Cy\WWWCityService\Libs\api_gateway\Constant\ContentType;
use Cy\WWWCityService\Libs\api_gateway\Constant\HttpHeader;
use Cy\WWWCityService\Libs\api_gateway\Constant\HttpMethod;
use Cy\WWWCityService\Libs\api_gateway\Http\HttpRequest;
use Cy\WWWCityService\Libs\api_gateway\Constant\SystemHeader;
use Cy\WWWCityService\Libs\api_gateway\Http\HttpClient;

class AGRequest
{

    private $ACCESS_KEY;
    private $ACCESS_SECRET;

    private $requestTime;

    protected static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    private function __construct()
    {
        $this->ACCESS_KEY = config('wwwcityservice')['APP_ACCESS_KEY'];
        $this->ACCESS_SECRET = config('wwwcityservice')['APP_ACCESS_SECRET'];
        if (!$this->ACCESS_KEY || !$this->ACCESS_SECRET) {
            abort(1226, '读取应用配置失败！请检查配置信息');
        }
    }

    public function post($host, $url, $bodys = [], $querys = [], $headers = [])
    {

        $this->requestTime = $this->getMsecTime();

        $request = new HttpRequest($host, $url, HttpMethod::POST, $this->ACCESS_KEY, $this->ACCESS_SECRET);
        //设定Content-Type，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_FORM);
        //设定Accept，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_JSON);
        // set header
        foreach ($headers as $key => $value) {
            $request->setHeader($key, $value);
            $request->setSignHeader($key);
        }
        // set query string
        foreach ($querys as $key => $value) {
            $request->setQuery($key, $value);
        }
        // set form data
        foreach ($bodys as $key => $value) {
            $request->setBody($key, $value);
        }
        // set auth field
        $request->setSignHeader(SystemHeader::X_CA_TIMESTAMP);
        //$request->setSignHeader("a-header1");
        $response = HttpClient::execute($request);
        return $this->parseResponseAndDoLog($response, $request);
    }

    public function get($host, $url, $querys = [], $headers = [])
    {

        $this->requestTime = $this->getMsecTime();

        $request = new HttpRequest($host, $url, HttpMethod::GET, $this->ACCESS_KEY, $this->ACCESS_SECRET);
        //设定Content-Type，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_FORM);

        //设定Accept，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_JSON);
        // set header
        foreach ($headers as $key => $value) {
            $request->setHeader($key, $value);
            $request->setSignHeader($key);
        }
        // set query string
        foreach ($querys as $key => $value) {
            $request->setQuery($key, $value);
        }
        // set auth field
        $request->setSignHeader(SystemHeader::X_CA_TIMESTAMP);
        //$request->setSignHeader("a-header1");
        $response = HttpClient::execute($request);
        return $this->parseResponseAndDoLog($response, $request);
    }

    // 格式化返回数据并打日志
    private function parseResponseAndDoLog($response, $request)
    {
        // 声明返回数据变量
        $content = $response->getContent();
        $body = $response->getBody();
        $header = $response->getHeader();
        $requestId = $response->getRequestId();
        $errorMessage = $response->getErrorMessage();
        $contentType = $response->getContentType();
        $httpStatusCode = $response->getHttpStatusCode();

        // 声明请求数据变量
        $host = $request->getHost();
        $path = $request->getPath();
        $method = $request->getMethod();
        $headers = $request->getHeaders();
        $querys = $request->getQuerys();
        $bodys = $request->getBodys();
        $signHeaders = $request->getSignHeaders();

        // 因为errorMessage又臭又长 所以只有当报错的时候才完成返回errorMessage
        $LogErrorMessage = '';
        if ($httpStatusCode != 200) {
            $LogErrorMessage = $errorMessage;
        }

        $responseTime = $this->getMsecTime();
        $elapsedTime = $responseTime - $this->requestTime;
        // 分别记录请求信息和返回信息
        app('log')->info(
            '####AGRequest#### requestTime:' . $this->requestTime . ' responseTime:' . $responseTime . ' elapsedTime ' . $elapsedTime .
            ' ####request#### path:' . $path . ' querys:' . json_encode($querys) . ' bodys' . json_encode($bodys) .
            PHP_EOL .
            ' ####response#### requestId:' . $requestId . ' httpStatusCode:' . $httpStatusCode . ' errorMessage:' . $LogErrorMessage . ' body:' . $body
        );

        // 1 验证阿里云请求
        if ($httpStatusCode != 200) {
            throw new \Exception($errorMessage . ' #### requestId:' . $requestId, $httpStatusCode);
        }

        // 2 验证系统自身请求
        $body = json_decode($body, true);
        if (!$body) {
            abort(500, 'AG请求失败,无结果返回');
        }
        if (!key_exists('code', $body) || !key_exists('message', $body) || !key_exists('content', $body)) {
            abort(500, 'AG请求失败,错误的数据返回格式');
        }
        if ($body['code'] != 0) {
            throw new \Exception('[微服务]' . $body['message'], $body['code']);
        }

        // 返回数据
        return $body['content'];
    }

    // 获取当前毫秒时间
    private function getMsecTime()
    {
        list($msec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }

// 	'object(HttpResponse)#72 (7) {
//   ["content":"HttpResponse":private]=>
//   string(752) "HTTP/1.1 403 Forbidden
// Server: Tengine
// Date: Mon, 11 Mar 2019 07:30:59 GMT
// Content-Type: text/plain;charset=UTF-8
// Content-Length: 0
// Connection: keep-alive
// Access-Control-Allow-Origin: *
// Access-Control-Allow-Methods: GET,POST,PUT,DELETE,HEAD,OPTIONS,PATCH
// Access-Control-Allow-Headers: X-Requested-With,X-Sequence,X-Ca-Key,X-Ca-Secret,X-Ca-Version,X-Ca-Timestamp,X-Ca-Nonce,X-Ca-API-Key,X-Ca-Stage,X-Ca-Client-DeviceId,X-Ca-Client-AppId,X-Ca-Signature,X-Ca-Signature-Headers,X-Ca-Signature-Method,X-Forwarded-For,X-Ca-Date,X-Ca-Request-Mode,Authorization,Content-Type,Accept,Accept-Ranges,Cache-Control,Range,Content-MD5
// Access-Control-Max-Age: 172800
// X-Ca-Request-Id: 6D13BEB2-C37A-4CCA-92FA-4095DDD5B52F
// X-Ca-Error-Message: Unauthorized
//
// "
//   ["body":"HttpResponse":private]=>
//   NULL
//   ["header":"HttpResponse":private]=>
//   string(752) "HTTP/1.1 403 Forbidden
// Server: Tengine
// Date: Mon, 11 Mar 2019 07:30:59 GMT
// Content-Type: text/plain;charset=UTF-8
// Content-Length: 0
// Connection: keep-alive
// Access-Control-Allow-Origin: *
// Access-Control-Allow-Methods: GET,POST,PUT,DELETE,HEAD,OPTIONS,PATCH
// Access-Control-Allow-Headers: X-Requested-With,X-Sequence,X-Ca-Key,X-Ca-Secret,X-Ca-Version,X-Ca-Timestamp,X-Ca-Nonce,X-Ca-API-Key,X-Ca-Stage,X-Ca-Client-DeviceId,X-Ca-Client-AppId,X-Ca-Signature,X-Ca-Signature-Headers,X-Ca-Signature-Method,X-Forwarded-For,X-Ca-Date,X-Ca-Request-Mode,Authorization,Content-Type,Accept,Accept-Ranges,Cache-Control,Range,Content-MD5
// Access-Control-Max-Age: 172800
// X-Ca-Request-Id: 6D13BEB2-C37A-4CCA-92FA-4095DDD5B52F
// X-Ca-Error-Message: Unauthorized
//
// "
//   ["requestId":"HttpResponse":private]=>
//   string(36) "6D13BEB2-C37A-4CCA-92FA-4095DDD5B52F"
//   ["errorMessage":"HttpResponse":private]=>
//   string(12) "Unauthorized"
//   ["contentType":"HttpResponse":private]=>
//   string(24) "text/plain;charset=UTF-8"
//   ["httpStatusCode":"HttpResponse":private]=>
//   int(403)
// }'
}

