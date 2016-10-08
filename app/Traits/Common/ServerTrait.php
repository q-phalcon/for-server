<?php
declare(strict_types = 1);

namespace App\Traits\Common;

use App\Constants\Common\Constant;
use App\Exception\QPException;
use Qp\Kernel\Request;
use Qp\Kernel\Response;

trait ServerTrait
{
    /**
     * 请求参数数组
     *
     * @var array
     */
    public $param = [];

    /**
     * 响应数据
     *
     * @var array
     */
    private $re_data = [
        'result' => Constant::CODE_FAILURE,
        'msg' => Constant::MSG_SYS_ERROR,
        'code' => Constant::ERR_DEFAULT,
        'total' => 0,
        'data' => []
    ];

    /**
     * 初始化请求
     * 1.选择是否校验POST请求
     * 2.初始化请求参数
     *
     * @param   bool        $must_post
     * @throws  QPException
     */
    public function request_init(bool $must_post = false)
    {
        if ($must_post) {
            $method = Request::method();
            if ($method != 'POST') {
                throw new QPException("此接口请求方式必须为'Post'", Constant::CODE_FAILURE);
            }
        }

        $this->param = Request::param();
    }

    /**
     * 获取分页SQL的相关数据
     *
     * @param   bool        $format_str 是否返回字符串形式的SQL数据
     * @return  array|string
     */
    public function getLimit(bool $format_str = false)
    {
        $take = intval(array_get($this->param, 'pagesize', Constant::PAGESIZE));
        $page = intval(array_get($this->param, 'page', Constant::PAGE));
        $skip = ($page - 1) * $take;

        if ($format_str) {
            return $skip . "," . $take;
        }

        return [
            'skip' => $skip,
            'take' => $take
        ];
    }

    /**
     * 获取排序SQL的相关数据
     *
     * @param   array       $stdArr         标准字段，key为参数名，value为SQL字段(防止SQL注入)
     * @param   mixed       $default_sort   默认排序的数据
     * @param   bool        $format_str     是否返回字符串形式的SQL数据
     * @return  array|string
     * @throws  QPException
     */
    public function getSort(array $stdArr, $default_sort, bool $format_str = false)
    {
        if ($format_str) {
            $sort = "";
        } else {
            $sort = [];
        }
        $sort_datum = json_decode(array_get($this->param, 'sort', '[]'), true);

        // 'sort'字段为空，表示使用默认排序
        if ($sort_datum == []) {
            return $default_sort;
        }

        $direction_allow = ['DESC','ASC'];

        // 自定义排序
        foreach ($sort_datum as $sort_data) {
            if (! is_array($sort_data)) {
                throw new QPException("排序字段中每个元素必须是对象", Constant::CODE_FAILURE);
            }
            if (! isset($sort_data['field']) || ! isset($sort_data['direction'])) {
                throw new QPException("排序字段中每个元素都必须包含'field'和'direction'", Constant::CODE_FAILURE);
            }
            if (! isset($stdArr[$sort_data['field']])) {
                continue;
            }
            $sort_field = $stdArr[$sort_data['field']];
            if (! in_array(strtoupper($sort_data['direction']), $direction_allow)) {
                throw new QPException("排序方向只能是desc或asc", Constant::CODE_FAILURE);
            }
            $direction = $sort_data['direction'];

            if ($format_str) {
                $sort .= ",{$sort_field} {$direction}";
            } else {
                $sort[] = [
                    'field' => $sort_field,
                    'direction' => $direction
                ];
            }
        }

        if ($format_str) {
            $sort = substr($sort, 1);
        }

        return $sort;
    }

    /**
     * 获取响应JSON字符串 (响应类型为系统错误/系统异常)
     *
     * @param   string  $msg    响应消息
     * @param   int     $code   响应码
     * @return  string
     */
    public function toJson_error(string $msg = '', int $code = Constant::ERR_DEFAULT)
    {
        if ($msg != "") {
            $this->re_data['msg'] = $msg;
        }

        if ($code != Constant::ERR_DEFAULT) {
            $this->re_data['error_code'] = $code;
        }

        $this->re_data['result'] = Constant::CODE_FAILURE;
        $this->re_data['total'] = 0;
        $this->re_data['data'] = [];

        return json_encode($this->re_data);
    }

    /**
     * 获取响应JSON字符串 (响应类型为业务错误/警告)
     *
     * @param   string  $msg    响应消息
     * @param   int     $code   响应码
     * @return  string
     */
    public function toJson_warning(string $msg = '', int $code = Constant::ERR_DEFAULT)
    {
        if ($msg != "") {
            $this->re_data['msg'] = $msg;
        } else {
            $this->re_data['msg'] = Constant::MSG_SYS_WARNING;
        }

        if ($code != Constant::ERR_DEFAULT) {
            $this->re_data['error_code'] = $code;
        }

        $this->re_data['result'] = Constant::CODE_WARNING;
        $this->re_data['total'] = 0;
        $this->re_data['data'] = [];

        return json_encode($this->re_data);
    }

    /**
     * 获取响应JSON字符串 (响应类型为成功)
     *
     * @param   string  $msg    响应消息
     * @param   int     $code   响应码
     * @return  string
     */
    public function toJson_success(string $msg = '', int $code = Constant::ERR_DEFAULT)
    {
        if ($msg != "") {
            $this->re_data['msg'] = $msg;
        } else {
            $this->re_data['msg'] = Constant::MSG_SUCCESS;
        }

        if ($code != Constant::ERR_DEFAULT) {
            $this->re_data['error_code'] = $code;
        } else {
            $this->re_data['error_code'] = Constant::CODE_SUCCESS;
        }

        $this->re_data['result'] = Constant::CODE_SUCCESS;

        return json_encode($this->re_data);
    }

    /**
     * 响应JSON，并结束服务 (响应类型为系统错误/系统异常)
     *
     * @param   string  $msg    响应消息
     * @param   int     $code   响应码
     * @return  string
     */
    public function toJson_errorAndExit(string $msg = '', int $code = Constant::ERR_DEFAULT)
    {
        $this->responseJson($this->toJson_error($msg, $code));
    }

    /**
     * 响应JSON，并结束服务 (响应类型为业务错误/警告)
     *
     * @param   string  $msg    响应消息
     * @param   int     $code   响应码
     * @return  string
     */
    public function toJson_warningAndExit(string $msg = '', int $code = Constant::ERR_DEFAULT)
    {
        $this->responseJson($this->toJson_warning($msg, $code));
    }

    /**
     * 响应JSON，并结束服务 (响应类型为成功)
     *
     * @param   string  $msg    响应消息
     * @param   int     $code   响应码
     * @return  string
     */
    public function toJson_successAndExit(string $msg = '', int $code = Constant::ERR_DEFAULT)
    {
        $this->responseJson($this->toJson_success($msg, $code));
    }

    /**
     * 设置响应的total值
     *
     * @param   int $total
     */
    public function setTotal(int $total)
    {
        $this->re_data['total'] = $total;
    }

    /**
     * 设置响应的data数据
     *
     * @param   mixed   $total
     */
    public function setData($data)
    {
        $this->re_data['data'] = $data;
    }

    /**
     * 响应JSON并结束服务
     *
     * @param   string  $json
     */
    public function responseJson(string $json, int $http_code = 200)
    {
        $response = Response::response();
        $response->setContentType('application/json');
        Response::send($json, $http_code);
        exit;
    }
}
