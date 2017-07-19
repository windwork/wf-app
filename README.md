Windwork
===============
Windwork是一个用于快速开发高并发Web应用的轻量级PHP框架， 专注于解决易学易用、高效健壮、松耦合问题的快速开发方案。

## 设计原则
 * 高效（开发效率最大化；性能出色，适合开发高并发网站）
 * KISS（易上手、易维护、易扩展）
 * CCH（内核+组件+钩子）架构
 * OOP
 * MVC
 * 松耦合（组件职责单一）
 * 支持模块化开发应用
 * 遵循规范：遵循 [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)（[PSR-1 中文](https://github.com/PizzaLiu/PHP-FIG/blob/master/PSR-1-basic-coding-standard-cn.md)）、[PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)（[PSR-2 中文](https://github.com/PizzaLiu/PHP-FIG/blob/master/PSR-2-coding-style-guide-cn.md)）、[PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)（[PSR-4 中文](https://github.com/PizzaLiu/PHP-FIG/blob/master/PSR-4-autoloader-cn.md)）、Composer、PHPUnit单元测试。


## 环境要求
* 兼容 Nginx/Apache/IIS，建议Web服务器启用 URLRewrite
* PHP 5.5+
  - pdo_mysql/mysqli
  - gd2
  - mbstring
  - allow_url_fopen
* MySQL 5.0+（要求启用InnoDB引擎）

## 安装
使用composer安装
```
composer require windwork/app
```