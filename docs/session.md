# Windwork Session类
PHP session支持用户访问我们的程序时，我们在服务器端保存下一些个性化的数据，用户下次再访问的时候保存的session数据还存在。

Windwork框架中，我们提供通过配置文件设置session参数，然后通过\wf\core\Session::start()方法来启动session。
为了不增加你额外学习的时间，我们不对session操作进行封装，而是直接使用$_SESSION变量存取session数据。

## 1、使用session
### 启用session
使用session前，需要先启用session。
在Windwork框架中，为了具备灵活选择session存储方式的特点，我们不直接用session_start()启用session，而是对session的启用方法进行封装。
使用如下方法启用 session
```
\wf\core\Session::start(cfg('session'));

// 在页面A中保存session值
$_SESSION['aa'] = 123;

// 在页面B中读取session值
echo $_SESSION['aa'];

```
## 2、清除session
当用户退出登录状态时，我们需要清除该用户的session数据。

```
\wf\core\Session::destroy();
```


## 3、session存储方式设置
通过修改session配置来修改Windwork session的存储方式。
session配置保存在 config/app.php 配置文件中。

### 默认session存储
配置如下即可
```
$cfg = [    
    'saveHandler'     => 'files',    
    'savePath'        => 'data/session',    
    // 是否允许通过url传递session ID，当客户端不支持cookie时启用
    'useTransSid'     => 0, 
    'cookiePath'      => '/',
    'cookieDomain'    => '',     
    'cookieLifetime'  => 43200, // 43200=12个小时
];

```

### 使用redis存储session
redis扩展使用 https://github.com/phpredis/phpredis
配置文件设置如下即可
```
// 单个redis服务
$cfg = [ 
    'saveHandler'     => 'redis',    
    'savePath'        => 'tcp://host1:6379',
    'useTransSid'     => 0, 
    'cookiePath'      => '/',
    'cookieDomain'    => '',     
    'cookieLifetime'  => 43200,
];

// 多个redis服务
$cfg['savePath'] = 'tcp://host1:6379?weight=1,tcp://host2:6379?weight=2&timeout=2.5,tcp://host3:6379?weight=2';

```

### 使用memcache存储session
配置文件设置如下即可
```
$cfg = [  
    'saveHandler'     => 'memcache',    
    'savePath'        => 'tcp://mmc_host:mmc_port', // 多个服务器用逗号隔开
    'useTransSid'     => 0, 
    'cookiePath'      => '/',
    'cookieDomain'    => '',     
    'cookieLifetime'  => 43200,
];
```

### 使用memcached存储session
配置文件设置如下即可
```
$cfg = [   
    'saveHandler'     => 'memcached',    
    'savePath'        => 'mmc_host:mmc_port', // 多个服务器用逗号隔开
    'useTransSid'     => 0, 
    'cookiePath'      => '/',
    'cookieDomain'    => '',     
    'cookieLifetime'  => 43200,
];
```

### 自定义session存储方式
PHP引擎给我们提供了自定义session存储的方案。
实现PHP内置接口SessionHandlerInterface，然后将实现类的实例设置为session处理方式。
```
$cfg = [   
    'saveHandler'     => new class implements SessionHandlerInterface
    {
        // 在这里实现接口
    },    
    'savePath'        => 'xxx', 
    'useTransSid'     => 0, 
    'cookiePath'      => '/',
    'cookieDomain'    => '',     
    'cookieLifetime'  => 43200,
];

```

SessionHandlerInterface接口参考文档：http://cn2.php.net/manual/zh/class.sessionhandlerinterface.php

