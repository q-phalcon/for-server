<?php
declare(strict_types = 1);

namespace App\Exception;

use App\Constants\Common\Constant;
use App\Traits\Common\ServerTrait;
use Qp\Kernel\Log;

class QPException extends \Exception
{
    use ServerTrait;

    private $result;
    private $error_code;
    private $is_log;

    public function __toString()
    {
        return parent::__toString();
    }

    /**
     * QPException 构造函数
     *
     * @param   string      $msg    异常信息
     * @param   int         $result 响应类型
     * @param   bool        $is_log 是否记录日志
     * @param   int         $code   响应码
     */
    public function __construct(string $msg = Constant::MSG_SYS_ERROR, int $result = Constant::CODE_WARNING, bool $is_log = false, int $error_code = Constant::ERR_DEFAULT)
    {
        parent::__construct($msg, $error_code);
        $this->result = $result;
        $this->error_code = $error_code;
        $this->is_log = $is_log;
    }

    /**
     * 返回异常对象定义的响应JSON，并根据定义记录日志
     *
     * @param   string  $msg    响应消息
     */
    public function response($msg = Constant::MSG_SYS_ERROR)
    {
        switch ($this->result) {
            case Constant::CODE_FAILURE :
                $this->response_error($msg);
                break;
            case Constant::CODE_WARNING :
                $this->response_warning($msg);
                break;
        }
    }

    private function response_error($msg)
    {
        if ($this->is_log) {
            $out_data = $this->toJson_error($msg, $this->error_code);
            $log_msg = "\r\n请求参数:{in_data}\r\n响应数据:{out_data}\r\n异常位置:{file}:{line}\r\n异常信息:{msg}\r\n";
            $log_data = [
                'file' => $this->file,
                'line' => $this->line,
                'msg' => $this->message,
                'in_data' => json_encode(\Qp\Kernel\Request::param()),
                'out_data' => $out_data
            ];
            Log::error($log_msg, $log_data, true, Constant::LOG_DIR);
        }
        $this->toJson_errorAndExit($msg, $this->error_code);
    }

    private function response_warning($msg)
    {
        if ($this->is_log) {
            $out_data = $this->toJson_warning($msg, $this->error_code);
            $log_msg = "\r\n请求参数:{in_data}\r\n响应数据:{out_data}\r\n异常位置:{file}:{line}\r\n异常信息:{msg}\r\n";
            $log_data = [
                'file' => $this->file,
                'line' => $this->line,
                'msg' => $this->message,
                'in_data' => json_encode(\Qp\Kernel\Request::param()),
                'out_data' => $out_data
            ];
            Log::warning($log_msg, $log_data, true, Constant::LOG_DIR);
        }
        $this->toJson_warningAndExit($msg, $this->error_code);
    }
}