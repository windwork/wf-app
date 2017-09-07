Response
======================
Response对象用于动态响应客户端请示，控制发送给用户的信息，并将动态生成响应。

Response类定义了常用的响应状态码常量。

常用响应对象的方法
----------------

### 1、响应跳转方法
使用header('Location: url')方式跳转，特点是对url进行检查，如果使用简写的url，可自动补全。

```
// 在控制器中使用Response对象
$url = 'http://www.qq.com';
$this->response->sendRedirect($url);

// 站内跳转简写链接
$this->response->sendRedirect('user.account.login'); // 跳转到 index.php?user.account.login
```

### 2、设置响应状态码
```
$this->response->setStatus($code);
```

### 3、设置响应头
对响应头进行封装，设置响应头后，执行发送响应头后才会发送。
```
$this->response->setHeader('content-type', 'text/html;charset=utf-8');
// 等同于 header('content-type:text/html;charset=utf-8');
// 框架中执行 $this->response->sendHeaders()后发送响应头。
```
### 4、获取响应头
获取响应对象中设置的响应头
```
$this->response->getHeaders();
```
