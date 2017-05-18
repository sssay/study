### ci框架
* 模板
1. 建立模板 Demo_model
2. 创建Demo_model类 继承CI模板 extends CI_Model
3. 构造方法 加载数据库 内部调用(parent::__construct();)
4. 函数方法

* 控制器
1. 建立控制器 demo.php
2. 创建Demo类 继承CI控制器 extends CI_Controller
3. 构造方法 加载demo_model 内部调用(parent::__construct();)
4. 函数方法

* 视图
1. 创建demo文件夹
2. 创建视图

* 数据库配置
1. config/database.php

* 访问限制
1. defined('BASEPATH') OR exit('No direct script access allowed');
   防止跨站攻击，直接通过访问文件路径

* 数据库操作
1. $this->db->where('user_name', $username); 查询条件
	$this->db->select('*'); 查询所有数据
	$query = $this->db->get('fog_node_owner'); 获取表名
    if ($query->num_rows() > 0) 返回数据条数
    {
        return $query->result()[0];获取数据
    }
    else
    {
        return (bool) FALSE;
    }

* config配置
1. 定义内部路由 $config['base_url'] = 'http://localhost/owner/';
2. $config['url_suffix'] = '.html'; 路由美化 添加 .html
3. $config['language']	= 'english'; 语言
4. $config['charset'] = 'UTF-8'; 编码格式
5. $config['log_date_format'] = 'Y-m-d H:i:s'; 时间格式
6. $config['time_reference'] = 'Asia/Hong_Kong'; 时区格式

* 邮件群发
1. view页面添加预发模板
2. controller也面通过htmlspecialchars_decode() 函数把一些预定义的 HTML 实体转换为字符。（标题和内容）
3. 更新插入数据库 update_config
4. 把模板插入数据库

* 邮件发送步骤
* 模板
1. 获取邮件模板主题
2. 获取邮件模板内容
3. 获取用户邮箱
* 控制器
1. 渲染一个页面（提交用户邮箱）
2. 发送邮箱 建立私有方法（send_email）
3. 获取用户邮箱 调用发送邮件方法(send_email)
