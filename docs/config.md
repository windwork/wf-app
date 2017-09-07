配置
==================

```
// 初始化配置实例
$conf = new \wf\core\Config('./config', 'develop');
$conf->load('my.php);

```

## 配置的格式
windwork框架配置仅支持数组格式的配置，配置下标使用驼峰命名规则，首字母小写，只允许包含字母和数字。

```
// config/session.php
return [
    //
    'cfgFirst'  => 'cfg Value',
    'cfgSecond' => 'cfg Value',
        
    // 缓存组件设置
    'cache' => [
        // 缓存模式，File|Memcache|Memcached|Redis
        'class'            => '\\wf\\cache\\adapter\\File',
        'enabled'          => 1,  
        'dir'              => dirname(__DIR__) . '/data/cache', 
        'expire'           => 7200, 
    
        // redis
        'redis' => [
            'host'         => '127.0.0.1',   
            'port'         => 6379, 
            'pconnect'     => 1,
            'timeout'      => 0, 
        ],
        // memcache
        'memcache' => [
            'host'         => '127.0.0.1',     
            'port'         => 11211,          
            'pconnect'     => 1,            
            'timeout'      => 1,     
        ],
    ],
];
```

## 使用配置
wf-web组件定义cfg()函数读取配置参数的值。
读取配置变量，配置参数下标可用.隔开访问多层级数组。
例如：
```

// 访问 $configs['url']
cfg('url');

// 访问 $configs['url']['rewrite']
cfg('url.rewrite');

// 访问 $configs['url']['alias']['login']
cfg('url.alias.login');


```

### 配置目录
所有配置文件放在 ./config 文件夹中。

### 加载配置

### 读取配置项

### 设置配置项

### 多环境


## 配置类参考

