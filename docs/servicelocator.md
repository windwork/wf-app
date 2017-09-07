# 服务定位器
Windwork服务定位器是控制反转（IoC）模式的一种实现，目的是解耦服务提供者和用户，用户无需直接访问具体的服务提供者类。
服务定位器是一种反模式，存在这么个问题：我们把对象设置到服务定位器中时，事先没法知道这个对象提供哪些属性/方法，因此我们在wf/web/lib/helper.php组件中定义一些函数来访问服务定位器中注册的windwork组件。


## usage

```
// 创建实例
$locator = new \wf\core\ServiceLocator();

// 注入对象，类型只允许为：字符串（类名）、类实例、闭包函数。
$obj = function() {
    echo 'ServiceLocator 闭包函数实例';
};
$this->locator->set('closure_sample', $obj, $params = []);

// 获取对象实例，如果注入的是类名、闭包函数，只有获取对象实例时才创建对象实例。
echo $this->locator->set('closure_sample');

```

## 服务定位器使用
我们通过在 config/app.php 设置组件配置信息，应用运行时通过服务定位器自动加载组件。


## 内置定位器对象访问函数表：

 内置函数                | 调用组件                                 | 组件说明
------------- | -------------------- | ---------------
wfCache()     | srv('cache')         | 缓存
wfCaptcha()   | srv('captcha')       | 验证码组件
wfCrypt()     | srv('crypt')         | 可逆加密解密组件
wfDb($id)     | srv('db.' . $id)     | 数据库访问组件
wfImage()     | srv('image')         | 图片处理组件
wfMailer      | srv('mailer')        | 邮件发送组件
wfStorage()   | srv('storage')       | 附件存储组件
logging()     | srv('logger')->log() | 日志组件写入日志
