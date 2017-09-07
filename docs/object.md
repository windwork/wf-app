Object基础类
==================
框架基础类，支持动态增删读属性，错误信息管理（setError/getLastError/getErrors）。

1. 实现了\__set,\__get,\__isset,\__unset,\__call魔术方法。
2. 错误信息管理（setError/getLastError/getErrors）让对象业务逻辑封装及对象消息传递变得很简单。


Example #1 使用Object类
```
class MyClass extends \wf\Object {
    /**
     * 对象错误消息传递实例
     * 业务逻辑中设置错误消息后返回false，外部调用时可通getErrors()/getLastError()获取错误信息。
     */
    public function demo() {
        $this->setError('error 1');
        $this->setError('error 2');

        return false;
    }
}

$my = new MyClass();

// 1、自动创建属性并保存
$my->a1 = 123; // $my->attrs['a1'] = 123;

// 2、判断属性是否存在
true === isset($my->a1); // 通过isset($my->attrs['a1'])实现

// 3、属性位创建则返回null
null === $my->a2; 

// 4、智能set/get
$my->setA2(456); // 等价于 $my->a2 = 456;
$my->getA2(); // 等价于 $my->a2;

// 5、错误信息set/get/gets
if(false === $my->demo()) {
    $err = $my->getLastError();  // 获取最后一条错误信息
    $errs = $my->getErrors(); // 获取全部错误信息
}

```