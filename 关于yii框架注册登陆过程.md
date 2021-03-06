# yii框架的注册登陆原理

## ActiveForm组件
1. 表单生成
2. 表单方法（ActiveForm::begin()方法）
3. 表单参数及其属性（$model,$validateOnSubmit,$errorSummary,$attributes）
4. ajax验证
5. 前端js事件

## 用户注册模块
* 在数据库建立用户表
* 创建SignupForm.php模板，创建公共用户名邮箱密码和重复密码（引用ActiveForm组件）
* 在view页面创建表单，post/get提交数据
* 控制器创建注册方法，实例化注册模板，如果不提交数据，渲染注册页面，如果提交数据，则SignupForm.php模板接收数据
* 创建注册验证rules
* 验证是否为空，是否符合邮箱规则等
* 验证通过后对密码进行加密，添加AuthKey值，把所有数据存入数据库，成功进入首页

### 加密方法 password_hash($password, $alog, $options) 三个参数; 
* $password 用户密码
* $alog 一个密码算法常数，指示算法使用时散列 （不能等于1）
* $options【数组】 随机的盐和默认cost（不能小于4或者大于31，yii取13）
* 算法 crypt($password, sprintf('$2y$%02d$%s', $options['cost'], $options['salt']))) === 60
* 返回的哈希包含了算法、cost 和盐值
* Yii 2提供了一个Security类，将上面的密码加密和验证函数封装起来，并且增加了对于低版本PHP的支持。

## 用户登陆模块
* 建立LoginForm.php模板，创建公共用户名密码和记住密码（需要验证AuthKey值）
* 在view页面创建表单，post/get提交数据
* 控制器创建登陆方法，实例化登陆模板，如果不提交数据，渲染登陆页面，如果提交数据，则LoginForm.php模板接收数据
* 创建验证rules（required），yii预定义规则，通过提交的用户名查询数据库，判断用户名是否存在或者密码是否错误。
* 用户提供所需的信息进行身份验证.
* 一个 identity instance 是用用户提供 的信息创建的
* 调用 IUserIdentity::authenticate 来检查identity是否有效
* 如果有效, 调用 CWebUser::login 登陆用户, 然后用户浏览器重定向到 returnUrl
* 如果无效, 从identity instance中检索错误代码或错误信息然后显示




