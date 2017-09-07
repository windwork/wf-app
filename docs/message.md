消息约定
=====================
我们使用消息管理器来管理控制器中的业务执行结果消息。
消息类型分有：正确、错误。
在程序执行流程中，控制器执行模型，将执行结果（成功或失败）设置到消息对象，消息最终传递到html视图或json数据响应到客户端。

1、模型传递消息到控制器
-----------------
模型只将错误消息传递到控制器。
传递方式为：
模型里面有错误的时候，使用$this->setError(错误信息)设置错误信息并返回false，在控制器中使用 $modelObj->getErrs() 获取模型错误信息。

**消息传递案例**
```
namespace app\user\controller;

use \wf\app\Message;

/**
 * 典型的控制器中消息传递的实例
 * 控制器从模型取得错误信息并通过Message::setErr()传到视图
 * 如果没有错误，则传递成功的提示信息到视图
 */
class AccountController extends \wf\app\web\Controller {
    public function loginAction() {
        if($this->isPost()) {
            $account  = $this->getInput('account');
            $password = $this->getInput('password');

            // 处理用户登录
            $userObj = new \app\user\model\UserModel();

            // 执行业务逻辑
            if(false !== $userObj->login($account, $password)) {
                $this->message()->setSuccess('登录成功'); // 将登录成功的信息传递到视图
            } else {
                $this->message()->setError($userObj->getError()); // 将模型返回的错误信息传递到视图以响应请求
            }

            //输出消息
            // 如果是ajax请求则返回JSON数据格式数据，否则返回HTML提示信息页面
            $this->showMessage();
            return;
        }

        // 显示视图，视图自动去获取消息
        $this->view()->render();
    }
}
```

2、视图中显示消息
--------------------
```
  <!-- {if $_msg = $message->getMessage()} -->
    <!-- {if $message->isSuccess()} -->
    <div class="section prompt success">
        <div class="success-msg">
            <span>{{$_msg}}</span>
        </div>
    </div>
    <!-- {else} -->
    <div class="section prompt error">
        <div class="error-msg">
            <span>{{$_msg}}</span>
        </div>
    </div>
    <!-- {/if} -->
    
```

3、控制器返回JSON消息
-------------------
控制器调用$this->showMessage();的时候，如果$_GET['ajax'] == true 或 $_GET['ajax_cb'] == true 或客户端使用XmlHttpRequest则自动响应json格式数据。


4、模型中的消息
-----------------
模型中通过\wf\model\Error错误类来管理模型业务错误消息，一旦业务逻辑出现错误，则将错误消息设置到模型的error属性并返回false，调用者判断返回的是false的时候，再主动去获取模型错误信息类实例属性获得错误信息。
