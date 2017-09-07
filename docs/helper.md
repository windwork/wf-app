Web开发帮助函数
============
函数 | 说明
-- | --
app() | 获取应用实例
avatar($uid, $type = 'small', $reload = false) | 获取会员头像的url，一般在模板中使用
cfg($name = null) | 读取配置变量
checkToken() | 检查页面请求令牌
dsp() | 调度器实例（前端控制器）
exceptionHandler | 默认异常处理
in($key) | 获取用户通过web提交数据
lang($key) | 获取语言包中的字符串
logging($level, $message) |  写入日志，可以在config/config.php中启用日志，所有日志按类别保存
modelPager(\wf\model\Model $m, $cdt = [], $rows = 10, $countField = '*') | 获取记录列表
msg()  |  消息对象
pager($totals, $rows = 10, $tpl = 'simple') | 创建记录查询分页导航对象
paramDecode($arg)  | 对请求URL进行解码
paramEncode()      |  对请求URL进行编码
setSrv($name, $definition, array $params = [], $share = true) | 服务定位器注入对象
srv($name)        | 使用服务定位器获取服务实例
storagePath($url) | 根据上传文件的URL获取path
storageUrl($path) | 根据上传文件的Path获取完整URL
table($table)     | 数据表数据查询
thumb($path, $width = 100, $height = 0) | 获取缩略图的URL，一般在模板中使用
url($uri, $fullUrl = false) | 生成URL，通过路由实例生成符合路由解析规则的URL
wfCache()   | 获取缓存组件实例
wfCaptcha() | 获取验证码组件实例
wfCrypt()   | 获取加密解密组件实例
wfDb($id = 'default') | 获取数据库操作组件实例
wfImage()   | 图片处理组件
wfMailer()  | 邮件发送组件实例
wfStorage() | 获取存贮组件实例
