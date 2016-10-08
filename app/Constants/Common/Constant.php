<?php

namespace App\Constants\Common;

class Constant
{
    // 响应的返回值
    const CODE_SUCCESS = 0;     // 成功
    const CODE_FAILURE = 1;     // 失败
    const CODE_WARNING = -1;    // 警告

    // 错误码
    const ERR_DEFAULT = -1;     // 默认的错误码
    const ERR_EXTERNAL_SERVICE = 33500; // HTTP请求外部服务错误

    // 通用消息(文案)
    const MSG_SYS_ERROR = "系统异常，如有疑问请联系我们的客服";      // 系统错误
    const MSG_SYS_WARNING = "操作失败或系统繁忙，请稍后再试";      // 系统默认警告信息
    const MSG_SUCCESS = "操作成功";                             // 操作成功，默认文案

    // 默认日志模块 logs/XXX，空字符串表示在logs目录下
    const LOG_DIR = "";

    // 系统用户ID
    const SYS_USER_ID = 0;

    // 默认列表参数
    const PAGE = 1; // 默认查询第一页
    const PAGESIZE = 10; // 默认每页大小
}
