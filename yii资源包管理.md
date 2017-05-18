# web样式资源包管理

## 资源内部整合

1. 在common中建立theme,存放web样式(css,js)

2. 在common/assets中建立ThemeAsset.php主题资源包

3. 引用yii\web\AssetBundle的类

4. 继承AssetBundle的类

5. 自定义 

	public $sourcePath = '@common/theme';

	public $css = [];

	public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];

6. 完成资源管理内部整合

## 资源内部引用

1. 引用资源包 在frontend/assets中建立MainAsset.php

2. 引用yii\web\AssetBundle的类

3. 定义
	public $basePath = '@webroot';$basePath 指定资源从哪个可网络访问的目录提供服务，这里的 @webroot 是指向应用 web 目录的别名。

    public $baseUrl = '@web';$baseUrl 用来指定刚才的 $css 和 $js 相对的根 URL，@web对应你的网站根 URL。

    public $css = [
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'common\assets\ThemeAsset',
    ];

4. 完成资源内部引用

## 资源web引用

1. 在layout中引用资源包

	use frontend\assets\MainAsset;

	MainAsset::register($this);

2. 完成资源web引用
# js底部引用

1. 在MainAsset.php中创建方法
	
	public function addJS ($view, $js)
    {
        return $view->registerJsFile('@web/js/'.$js, ['depends' => 'frontend\assets\MainAsset']);
    }

2. 页面操作

	引用use backend\assets\MainAsset;

	添加<?php MainAsset::addJs($this, 'bandwidth.js')?>
