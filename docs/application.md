应用程序类
==================
负责初始化Web运行环境并执行应用程序，类自动加载实现，获取请求、响应对象，协调处理用户请求与处理,跳转分发等工作。

1、执行应用程序
---------------
我们再index.php中通过调用\wf\mvc\Application类来执行应用程序。
```
// ./public/index.php
define('WF_IN', true);

// 应用运行环境名，根据该参数加载配置文件
// dev）开发（默认）
// test）测试； 
// product）正式上线；
// 其他）其他正式上线应用
define('WF_ENV', 'dev');

// 项目文件夹完整路径
define('ROOT_DIR', dirname(__DIR__));

// 引入Windwork类加载器
require_once ROOT_DIR . '/wf/core/lib/Loader.php';

// 入口文件创建应用实例
$app = \wf\app\web\Application::app();

// 执行应用程序
$app->run(); 
```

2、程序执行流程
----------
![程序执行流程](res/images/appflow.jpg)  

3、应用程序跳转分发
----------------
\wf\mvc\Dispatcher类提供接口支持站内页面请求转移到其它的控制器Acton（调用站内其他控制器的动作）
```
// 在控制器中
$this->getDispatcher()->dispatch("$mod.$ctl.$act/$id/$other");
// 不在控制器中
\wf\mvc\Application::app()->getDispatcher()->dispatch("$mod.$ctl.$act/$id/$other");
// 或
app()->getDispatcher()->dispatch("$mod.$ctl.$act/$id/$other");
// 或
dsp()->dispatch("$mod.$ctl.$act/$id/$other");

```

4、获取请求对象
----------------------
```
\wf\mvc\Application::app()->getDispatcher()->getRequest();
// 或
app()->getDispatcher()->getRequest();
// 或
dsp()->getRequest();
```


5、获取响应对象
-----------------
```
\wf\mvc\Application::app()->getDispatcher()->getResponse();
// 或
app()->getDispatcher()->getResponse();
// 或
dsp()->getResponse();
```

