# Api-Doc-PHP

### 主要功能：

+ 根据接口注释自动生成接口文档

### 开源地址：

[https://github.com/phpwdk/api-doc-php](https://github.com/phpwdk/api-doc-php)
    
### 扩展安装：

+ 方法一：composer命令 `composer require phpwdk/api-doc-php`

+ 方法二：直接下载压缩包，然后进入项目中执行 composer命令 `composer update` 来生成自动加载文件

### 引用扩展：

+ 当你的项目不支持composer自动加载时，可以使用以下方式来引用该扩展包

```
// 引入扩展（具体路径请根据你的目录结构自行修改）
require_once __DIR__ . '/vendor/autoload.php';
```

### 使用扩展：

```
// 引入扩展（具体路径请根据你的目录结构自行修改）
require_once __DIR__ . '/../vendor/autoload.php';
// 加载测试API类
require_once __DIR__ . '/Api.php';
$config = [
    'class'         => ['Api'], // 要生成文档的类
    'filter_method' => ['__construct'], // 要过滤的方法名称
];
$api = new \phpwdk\apidoc\BootstrapApiDoc($config);
$doc = $api->getHtml();
exit($doc);
```

``` 使用方法：
/**
 * @title 用户登录API
 * @url https://wwww.baidu.com/login
 * @method POST
 * @param string username 是 账号 无 无 账户登陆信息
 * @param string password 是 密码 无 无 账户登陆信息
 * @code 1 成功
 * @code 2 失败
 * @desc_return int code 是 状态码（具体参见状态码说明） 无 无
 * @desc_return string msg 是 提示信息 无 无
 */
 
 行说明：
 1、操作类型
 2、字段类型
 3、字段名称
 4、是否必填
 5、字段标题
 6、上级字段
 7、默 认 值
 8、文本介绍
```

### 具体效果可运行test目录下的`index.php`查看
