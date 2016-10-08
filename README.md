# 基于QP开发的纯服务PHP框架 (QPS)


[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]


**Note:** ```PHP``` ```Phalcon``` ```Framework```



# 欢迎使用QP for Server 框架

　　该框架只做一件事情：纯后端服务，JSON响应

　　该框架是在q-phalcon/q-phalcon框架(QP框架)的基础上，继续面向服务开发：

 * 可以在代码的任何位置返回JSON响应：result,msg,code,data,total 5个常用的JSON响应结构，满足大部分业务需求，并且规范开发
 * 可以在代码的任何位置抛出适应JSON响应的异常信息，使代码更加清晰
 * 规范了常量定义的目录和文件
 * 规范化日志目录
 * 默认加入HTTP第三方组件，并且针对JSON响应的服务做了常规封装

# Install

### 1. 使用Composer下载
　　使用Composer工具安装项目，关于composer的用法，请自行学习！

```php
   composer create-project q-phalcon/for-server
```

### 3. 需要的环境

　　需要 php >= 7.0

　　需要 phalcon扩展：[install phalcon][link-Download_Phalcon]

　　强烈建议使用redis作为会话驱动方式！安装 redis-server：[Download Redis for linux][link-Download_Redis]

　　因此，同时需要redis扩展

### 3. 配置

　　修改php.ini文件中：session.serialize\_handler = php\_serialize (否则无法使用Session::getAll方法)

　　当然别忘记在php.ini中添加 phalcon和redis扩展哦

# License

　　The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


[ico-version]: https://img.shields.io/packagist/v/q-phalcon/for-server?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/q-phalcon/for-server.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/q-phalcon/for-server
[link-downloads]: https://packagist.org/packages/q-phalcon/for-server
[link-Download_Phalcon]: https://phalconphp.com/en/download
[link-Download_Redis]: http://redis.io/download
