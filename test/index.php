<?php

use phpwdk\apidoc\BootstrapApiDoc;

require_once __DIR__ . '/../vendor/autoload.php'; // 加载插件
require_once __DIR__ . '/Api.php';                // 加载测试API类1
$config = [
    'class'         => ['Api'], // 要生成文档的类
    'filter_method' => ['__construct'], // 要过滤的方法名称，默认已过滤TP系统方法
];
$api    = new BootstrapApiDoc($config);
$doc    = $api->getHtml();
exit($doc);

