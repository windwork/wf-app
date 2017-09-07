控制器
===========
路由对象根据用户请求URL映射到控制器的动作来处理用户请求并相应。
控制器可以被看作是一个中介，只负责做视图和模型/服务的中转，调用服务或模型对请求进行处理，通过视图响应内容。

1、控制器命名及文件约定
-----------------------
### 1.1、控制器文件命名约定
所有类（包括控制器）文件命名约定为：   
类文件都放在app文件夹子目录中，命名空间对应文件夹路径，文件名为类名后面加上".php"，每个控制器类单独放一个文件，这样为了自动加载类以及方便找到类。
```
$file = str_replace('\\', '/', "app/{$namespace}/{$className}.php");
```

### 1.2、命名空间约定
控制器命名空间为
```
// 启用模块
namespace app\{$mod}\controller;

// 不启用模块
namespace app\controller;
```
支持子命名空间
```
namespace app\{$mod}\controller\子命名空间;
namespace app\controller\子命名空间;
```

### 1.3、控制器类命名约定
类名以大写开头，以Controller结尾，只允许首字母及Controller的C字母大写，其他全部为小写。例如
```
AccountController
UploadController
MydemoController
```
### 1.4、控制器类必须继承 \wf\app\web\Controller类

### 1.5、控制器方法名
我们把控制器方法名叫动作（action）。
使用驼峰命名法，操作名首字母小写。
控制器方法名后面带Action字符串。

管理操作方法名约定：
管理后台控制器一般使用： list、create、update、delete作为action名。
前台常用action名约定： indexAction、categoryAction、itemAction


### 1.6、其他
每个类需要有文档注释，以文件注释开头，在类声明前添加类文档注释。

### 1.7、控制器类完整案例
```
<?php
/**
 * 文件注释
 */
namespace app\user\controller;

/**
 * 用户账号基本功能控制器
 * 
 * 用户登录、注册、忘记密码等功能实现
 * 
 * @package     app.user.controller
 * @author      cm <cm@windwork.org>
 * @since       1.0
 */
class AccountController extends \wf\app\web\Controller {
    public function loginAction() {
        // to do sth.
    }
}
```


2、请求URL映射到控制器
-----------------------
详细文档见[Router](https://ghthub.com/windwork/wf-router)
在查询字符串开头部分为路由映射参数，参数为：模块.控制器.动作
例如：
```
index.php?$mod.$ctl.$act
index.php?user.account.login
```

控制器可以有子命名空间，用“.”隔开，如 
```
$namespace = str_replace(‘.’, ‘\\’, “\\app\\{$mod}\\{$ctl}”);

index.php?article.admin.category.list 
打开文件
app/article/controller/admin/CategoryController.php
执行
\app\article\controller\admin\CategoryController::listAction()

index.php?article.admin.biz.history.list 
打开文件
app/article/controller/admin/biz/HistoryController.php
执行
\app\article\controller\admin\biz\HistoryController::listAction()
```

3、在控制器动作中获取外部变量
-------------------------
我们把从客户端通过传递到服务器端的$_GET/$_POST/$_REQUEST/$_COOKIE变量称为外部变量。
我们使用\wf\app\web\Request类的实例获取外部变量。
```
class AccountController extends \wf\app\web\Controller {
    public function loginAction() {
        //读取$_REQUEST变量，通用方式
        $this->getInput('name'); // 控制器父类封装调用$this->request->getRequest('name');
        $this->request->getRequest('name'); // $_REQUEST['name'] || null
        $this->request->getRequest(); // $_REQUEST

        //读取$_GET变量
        $this->request->getGet('name'); // $_GET['name'] || null
        $this->request->getGet(); // $_GET

        //读取$_POST变量
        $this->request->getPost('name'); // $_POST['name'] || null
        $this->request->getPost(); // $_POST

        //读取$_COOKIE变量
        $this->request->getCookie('name'); // $_COOKIE['name'] || null
        $this->request->getCookie(); // $_COOKIE
    }
}
```

4、在控制器中使用模板视图
------------------
模板引擎的初始化在控制器中使用晚绑定的模式，即调用$this->getView()获取视图实例时才会创建模板视图对象。

**模板变量赋值：** $this->getView()->assign('变量', "值"); 变量名为字符串，值为任意数据类型。   
**显示模板：** $this->getView()->render($tpl = '模板文件');  
模板文件夹按模块放在模块下的view文件夹中，如果render()参数为空，则自动查找路径为 app/{$mod}/view/{$ctl}.{$act}.html
模板的路径可自定义，详见[模板引擎](https://ghthub.com/windwork/wf-template)组件文档
```
class AccountController extends \wf\app\web\Controller {
    public function loginAction() {
        // 模板变量赋值
        $this->getView()->assign('time', time());

        // 显示模板 app/user/view/account.login.html
        $this->getView()->render();

        // 显示模板 user/view/account.login.m.html
        $this->getView()->render('user/account.login.m.html');
    }
}
```

5、在控制器中使用模型
-----------------------
模型类放在模块文件夹下的model文件夹中，以Model为后缀，如：\app\user\model\UserModel
详细模型帮助文档见[模型](https://ghthub.com/windwork/wf-model)组件文档
```
class AccountController extends \wf\app\web\Controller {
    public function loginAction() {
        // 使用模型
        $user = new \app\user\model\UserModel();

        // 执行模型业务逻辑
        if(false !== $user->doLogin($params)) {
            $this->message()->setSuccess('登录成功！');
        } else {
            $this->message()->setError($user->getError());
        }
        $this->showMessage();
    }
}
```

6、消息传递
--------------------
在web程序执行流程中，消息从模型传递到控制器，然后再从控制器设置到消息管理器，视图显示的时候自动从消息管理器获取信息。详见[消息约定](https://github.com/windwork/wf-web/blob/master/docs/message.md)
```
class AccountController extends \wf\app\web\Controller {
    public function loginAction() {
        $user = new \app\user\model\UserModel();
        
        // 执行模型业务逻辑
        if(false !== $user->doLogin($params)) {
            // 执行成功则将成功信息设置到消息对象
            $this->message()->setSuccess('登录成功！');
        } else {
            // 出错则将错误信息设置到消息对象
            $this->message()->setError($user->getError());
        }
        $this->showMessage();
    }
}
```
