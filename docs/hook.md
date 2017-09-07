钩子（Hook）
========================
提供一种机制在不需要修改框架代码的情况下来扩展核心框架，改变或增加框架的核心运行功能。

## 启用钩子
在 src/config/config.php 中启用钩子
```
hookEnabled => 1
```

## 设置钩子
在src/config/hooks.php设置钩子


## 钩子业务逻辑实现
钩子类保存在 "app/{$mod}/hook/" 文件夹；
钩子必须实现wf\core\HookInterface类，钩子管理器将执行钩子实现类中的execute方法。
```
// app/user/hook/AclHook.php
namespance app\user\hook;
class AclHook implements \wf\core\HookInterface {
    public function execute($params = array()) {
        // to do sth.
    }
}
```

## 配置规则
 * **方式1：**钩子类名或钩子类的实例。如：
```
    'dspNewControllerFore' => [
        '\\app\\user\\hook\\AclHook',  // 钩子类名（推荐）
         new \app\user\hook\AclHook2(), // 钩子类的实例
    ],
```

 * **方式2：**钩子类名或钩子类的实例+数组参数。如：
```
    'dspNewControllerFore' => [
        // 钩子类名 + 参数（推荐）
        ['\\app\\user\\hook\\AclHook', ['param 1', 'param 2', ....]], 
        // 钩子类的实例 + 参数
        [new \app\user\hook\AclHook2(), ['param 1', 'param 2', ....]], 
    ],
```

挂钩点
------------------
挂钩点是系统在框架中触发的位置。

1、appRuntimeAft
2、dspNewControllerFore
3、dspRunActionFore
4、dspOutputFore
5、dspResponseAft

 
 * **1、appRuntimeAft**   
   加载完系统配置后,初始化运行时触发的钩子，目的是增加修改运行时环境选项。只在创建Application单例时执行一次，框架仅初始化了request、response、自动加载、默认异常处理，其他库不可用；
   注意：这里的钩子在框架初始化前执行，因此不能调用框架的各种组件功能。
   

 * **2、dspNewControllerFore**   
   初始化控制器实例前触发的钩子

 * **3、dspRunActionFore**   
   创建控制器实例后，action执行前触发的钩子

 * **4、dspOutputFore**   
   内容输出前触发的钩子，可对输出内容进行处理过滤

 * **5、dspResponseAft**   
   发送响应内容（程序执行完）后触发的钩


自定义挂钩点
--------------
你也可以在自己开发的模块控制器中加入挂钩点
1、先在你的业务代码中加入挂钩点
```
app()->getHook()->call('my_hook_call_id'); // my_hook_call_id 为挂载点id
```

2、然后在配置文件中加入钩子调用类
```
  'my_hook_call_id' => [
       '\\app\\mymod\\hook\\MyHook',  // 钩子类名
       new \app\mymod\hook\MyHook2(), // 钩子类的实例
  ]
```