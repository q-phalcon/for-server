<?php

namespace App\Services\Http;

use App\Constants\Common\Constant;
use App\Exception\QpException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Qp\Kernel\Log;

class HttpBaseService
{
    protected $header;
    protected $cookie;
    protected $timeout = 30;
    protected $connect_timeout;

    protected $url;
    protected $param;
    protected $response;

    protected function __construct(array $header = null, array $cookie = null)
    {
        $this->header = $header;
        $this->cookie = $cookie;
        $this->timeout = config('http.timeout');
        $this->connect_timeout = config('http.connect_timeout');
    }

    protected function post($base_uri, $router_uri, array $data = [])
    {
        try {
            $this->url = $base_uri . $router_uri;
            $this->param = $data;
            $client = new Client(['base_uri' => $base_uri]);
            $res = $client->request('POST', $router_uri, [
                'headers' => $this->header,
                'cookies' => $this->cookie,
                RequestOptions::FORM_PARAMS => $data,
                'timeout' => $this->timeout,
                'connect_timeout' => $this->connect_timeout,
                'debug' => false
            ]);
            $this->response = $res->getBody()->getContents();
            return $this->response;

        } catch (\Exception $ex) {

            $log_msg = "\r\n使用GuzzleHttp组件请求Web服务失败\r\nHost地址:{Host}\r\n请求参数：{Param}\r\n异常信息：{Exception}\r\n";
            $log_data = [
                'Host' => $base_uri,
                'Param' => json_encode($data),
                'Exception' => $ex->getMessage()
            ];
            Log::error($log_msg, $log_data, true, Constant::LOG_DIR);
            throw new QpException(Constant::MSG_SYS_ERROR, Constant::CODE_FAILURE, false);
        }
    }

    protected function postBody($base_uri, $router_uri, $data = "")
    {
        try {

            $this->url = $base_uri . $router_uri;
            $this->param = $data;
            $client = new Client(['base_uri' => $base_uri]);
            $res = $client->request('POST', $router_uri, [
                'headers' => $this->header,
                'cookies' => $this->cookie,
                RequestOptions::BODY => $data,
                'timeout' => $this->timeout,
                'connect_timeout' => $this->connect_timeout,
                'debug' => false
            ]);
            $this->response = $res->getBody()->getContents();
            return $this->response;

        } catch (\Exception $ex) {

            $log_msg = "\r\n使用GuzzleHttp组件请求Web服务失败\r\nHost地址:{Host}\r\n请求参数：{Param}\r\n异常信息：{Exception}\r\n";
            $log_data = [
                'Host' => $base_uri,
                'Param' => json_encode($data),
                'Exception' => $ex->getMessage()
            ];
            Log::error($log_msg, $log_data, true, Constant::LOG_DIR);
            throw new QpException(Constant::MSG_SYS_ERROR, Constant::CODE_FAILURE, false);
        }
    }

    protected function get($base_uri, $router_uri, array $data = [])
    {
        try {

            $this->url = $base_uri . $router_uri;
            $this->param = $data;
            $client = new Client(['base_uri' => $base_uri]);
            $res = $client->request('GET', $router_uri, [
                'headers' => $this->header,
                'cookies' => $this->cookie,
                RequestOptions::QUERY => $data,
                'timeout' => $this->timeout,
                'connect_timeout' => $this->connect_timeout,
                'debug' => false
            ]);

            $this->response = $res->getBody()->getContents();
            return $this->response;

        } catch (\Exception $ex) {
            $log_msg = "\r\n使用GuzzleHttp组件请求Web服务失败\r\nHost地址:{Host}\r\n请求参数：{Param}\r\n异常信息：{Exception}\r\n";
            $log_data = [
                'Host' => $base_uri,
                'Param' => json_encode($data),
                'Exception' => $ex->getMessage()
            ];
            Log::error($log_msg, $log_data, true, Constant::LOG_DIR);
            throw new QpException(Constant::MSG_SYS_ERROR, Constant::CODE_FAILURE, false);
        }
    }

    protected function _missCheck(array $data, array $format)
    {
        foreach ($format as $value) {
            if (! isset($data[$value])) {
                return $value;
            }
        }
        return '';
    }

    protected function logHttpError($msg, $log_data = [])
    {
        $log_msg = "\r\n" . $msg . "\r\n请求地址：{URL}\r\n请求参数：{Param}\r\n响应数据：{Response}\r\n";
        $log_data = array_merge($log_data, [
            'URL' => $this->url,
            'Param' => json_encode($this->param),
            'Response' => $this->response
        ]);
        Log::error($log_msg, $log_data, true, Constant::LOG_DIR);
    }

    protected function _logAndThrow($msg, $log_data = [])
    {
        $this->logHttpError($msg, $log_data);
        throw new QpException(Constant::MSG_SYS_ERROR, Constant::CODE_FAILURE,false, Constant::ERR_EXTERNAL_SERVICE);
    }
}
