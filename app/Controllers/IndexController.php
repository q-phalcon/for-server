<?php

namespace App\Controllers;

use App\Traits\Common\ServerTrait;
use App\Exception\QPException;
use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    use ServerTrait;

    public function indexAction()
    {
        // 这里访问视图文件：~/app_view/index.phtml << ~/app_view/Index/index.phtml
    }

    public function notFoundAction()
    {
        header('HTTP/1.1 404 not found');
        return "404 not found!";
    }

    public function helloWorldAction()
    {
        try {
            throw new QPException("欢迎使用QP自定义异常...");
        } catch (QPException $ex) {
            $this->toJson_errorAndExit($ex->getMessage());
        }
    }
}
