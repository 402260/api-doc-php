<?php

namespace phpwdk\apidoc;

use phpwdk\apidoc\lib\Tools;

/**
 * BootstrapAPI文档生成
 * Class BootstrapApiDoc
 *
 * @package phpwdk\apidoc
 */
class BootstrapApiDoc extends ApiDoc
{
    /**
     * @var string - Bootstrap CSS文件路径
     */
    private $bootstrapCss = __DIR__ . '/../assets/css/bootstrap.min.css';

    /**
     * @var string - Bootstrap JS文件路径
     */
    private $bootstrapJs = __DIR__ . '/../assets/js/bootstrap.min.js';

    /**
     * @var string - jQuery Js文件路径
     */
    private $jQueryJs = __DIR__ . '/../assets/js/jquery.min.js';

    /**
     * @var string - 自定义CSS
     */
    private $customCss = '<style>
        #list-tab-left-nav{display: none;}
        .doc-content{margin-top: 75px;}
        .class-item .class-title {text-indent: 0.6em;border-left: 5px solid lightseagreen;font-size: 21px;margin: 7px 0;font-weight:bold}
        .action-item .action-title {text-indent: 0.6em;border-left: 3px solid #F0AD4E;font-size: 17px;margin: 8px 0;font-weight:bold}
        .table-item {background-color:#FFFFFF;padding-top: 10px;margin-bottom:8px;border: solid 1px #ccc;border-radius: 5px;}
        .list-group-item{padding: .5rem 1.25rem;}
        .list-group-item-sub{padding: .2rem 1rem;}
        .copyright-content{margin: 10px 0;}
        .table{table-layout: fixed;margin-bottom: .2rem;}
        .table th{padding: 0.4rem;font-size: 14px;background-color: #ddd;}
        .table td{word-break: break-all;word-wrap: break-word;overflow: hidden;width: 20%;padding: 0.4rem;font-size: 14px;}
        body{background-color: #d3d3d3;}
        p{margin-bottom: .3rem;}
        .btn-sm{padding: 0.1rem .5rem;font-size: .675rem;}
        .topright{display: flex;justify-content: center;flex-direction: column;}
        .trightout{height: 50%;font-size: 6px;}
        .outside{height: 50px;}
    </style>';

    /**
     * @var string - 自定义JS
     */
    private $customJs = '<script>
         $(\'a[href*="#"]:not([href="#"])\').click(function() {
            if (location.pathname.replace(/^\//, \'\') == this.pathname.replace(/^\//, \'\') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $("[name=\' + this.hash.slice(1) +\']");
                if (target.length) {
                    var topOffset = target.offset().top - 60;
                    $("html, body").animate({
                        scrollTop: topOffset
                    }, 800);
                    return false;
                }
            }
        });
    </script>';

    /**
     * Bootstrap 构造函数.
     *
     * @param array $config - 配置信息
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        // bootstrapJs文件路径
        $this->bootstrapJs = Tools::getSubValue('bootstrap_js', $config, $this->bootstrapJs);
        // jQueryJs文件路径
        $this->jQueryJs = Tools::getSubValue('jquery_js', $config, $this->jQueryJs);
        // 自定义js
        $this->customJs .= Tools::getSubValue('custom_js', $config, '');
        // bootstrapCSS文件路径
        $this->bootstrapCss = Tools::getSubValue('bootstrap_css', $config, $this->bootstrapCss);
        // 自定义CSS
        $this->customCss .= Tools::getSubValue('custom_css', $config, '');
        // 合并CSS
        $this->_getCss();
        // 合并JS
        $this->_getJs();
    }

    /**
     * 输出HTML
     *
     * @param int $type - 方法过滤，默认只获取 public类型 方法
     *                  ReflectionMethod::IS_STATIC
     *                  ReflectionMethod::IS_PUBLIC
     *                  ReflectionMethod::IS_PROTECTED
     *                  ReflectionMethod::IS_PRIVATE
     *                  ReflectionMethod::IS_ABSTRACT
     *                  ReflectionMethod::IS_FINAL
     * @return string
     */
    public function getHtml(int $type = \ReflectionMethod::IS_PUBLIC): string
    {
        $data = $this->getApiDoc($type);
        $html = <<<EXT
        <!DOCTYPE html>
        <html lang="zh-CN">
        <head>
            <meta charset="utf-8">
            <meta name="renderer" content="webkit">
            <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
            <!-- 禁止浏览器初始缩放 -->
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=0">
            <title>API文档 By Api-Doc-PHP</title>
            {$this->customCss}
        </head>
        <body>
        <div class="container-fluid">
             <nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top">
                   <a class="navbar-brand" href="#">API文档</a>
                   <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" >
                       <span class="navbar-toggler-icon"></span>
                   </button>
                   <div class="collapse navbar-collapse" id="navbarColor01">
                        {$this->_getTopNavList($data)}
                   </div>
             </nav>
             <div class="row">
                  <div class="col-lg-12">{$this->_getDocList($data)}</div>
             </div>
        </div>
        {$this->customJs}
        </body>
        </html>
EXT;

        if (isset($_GET['download']) && $_GET['download'] === 'api_doc_php') {
            Tools::downloadFile($html);
            return true;
        }
        return $html;
    }

    /**
     * 解析return 并生成HTML
     *
     * @param array $data
     * @return string
     */
    private function _getReturnData(array $data = []): string
    {
        if (!is_array($data) || count($data) < 1) {
            return '';
        }
        $html = '<div class="table-item col-md-12"><p class="table-title"><span class="btn  btn-sm btn-success">返回参数</span></p>';
        $html .= '<table class="table text-center"><tr><td>字段</td><td>类型</td><td>必须</td><td>名称</td><td>上级节点</td><td>默认值</td><td>描述</td></tr>';
        foreach ($data as $v) {
            $html .= '<tr>
                        <td>' . Tools::getSubValue('return_name', $v, '') . '</td>
                        <td>' . Tools::getSubValue('return_type', $v, '') . '</td>
                        <td>' . Tools::getSubValue('return_required', $v, '是') . '</td>
                        <td>' . Tools::getSubValue('return_title', $v, '') . '</td>
                        <td>' . Tools::getSubValue('return_superior', $v, '') . '</td>
                        <td>' . Tools::getSubValue('return_default', $v, '无默认值') . '</td>
                        <td>' . Tools::getSubValue('return_desc', $v, '') . '</td>
                      </tr>';
        }
        $html .= '</table></div>';
        return $html;
    }

    /**
     * 解析desc_return 并生成HTML
     *
     * @param array $data
     * @return string
     */
    private function _getDescReturnData(array $data = []): string
    {
        if (!is_array($data) || count($data) < 1) {
            return '';
        }
        $html = '<div class="table-item col-md-12"><p class="table-title"><span class="btn  btn-sm btn-success">返回参数</span></p>';
        $html .= '<table class="table text-center"><tr><td>字段</td><td>类型</td><td>必须</td><td>名称</td><td>上级节点</td><td>默认值</td><td>描述</td></tr>';
        foreach ($data as $v) {
            $html .= '<tr>
                        <td>' . Tools::getSubValue('return_name', $v, '') . '</td>
                        <td>' . Tools::getSubValue('return_type', $v, '') . '</td>
                        <td>' . Tools::getSubValue('return_required', $v, '是') . '</td>
                        <td>' . Tools::getSubValue('return_title', $v, '') . '</td>
                        <td>' . Tools::getSubValue('return_superior', $v, '') . '</td>
                        <td>' . Tools::getSubValue('return_default', $v, '无默认值') . '</td>
                        <td>' . Tools::getSubValue('return_desc', $v, '') . '</td>
                      </tr>';
        }
        $html .= '</table></div>';
        return $html;
    }

    /**
     * 解析param 并生成HTML
     *
     * @param array $data
     * @return string
     */
    private function _getParamData(array $data = []): string
    {
        $html = '';
        if (!is_array($data) || count($data) < 1) {
            return $html;
        }
        $html .= '<div class="table-item col-md-12"><p class="table-title"><span class="btn  btn-sm btn-danger">请求参数</span></p>';
        $html .= '<table class="table text-center"><tr><td>字段</td><td>类型</td><td>必须</td><td>名称</td><td>上级节点</td><td>默认值</td><td>描述</td></tr>';
        foreach ($data as $v) {
            $html .= '<tr>
                        <td>' . Tools::getSubValue('param_name', $v, '') . '</td>
                        <td>' . Tools::getSubValue('param_type', $v, '') . '</td>
                        <td>' . Tools::getSubValue('param_require', $v, '是') . '</td>
                        <td>' . Tools::getSubValue('param_title', $v, '') . '</td>
                        <td>' . Tools::getSubValue('param_superior', $v, '') . '</td>
                        <td>' . Tools::getSubValue('param_default', $v, '无默认值') . '</td>
                        <td>' . Tools::getSubValue('param_desc', $v, '') . '</td>
                      </tr>';
        }
        $html .= '</table></div>';
        return $html;
    }

    /**
     * 解析code 并生成HTML
     *
     * @param array $data
     * @return string
     */
    private function _getCodeData(array $data = []): string
    {
        $html = '';
        if (!is_array($data) || count($data) < 1) {
            return $html;
        }
        $html .= '<div class="table-item col-md-12"><p class="table-title"><span class="btn  btn-sm btn-warning">状态码说明</span></p>';
        $html .= '<table class="table text-center"><tr><td>状态码</td><td>描述</td></tr>';
        foreach ($data as $v) {
            $html .= '<tr>
                        <td>' . Tools::getSubValue('code', $v, '') . '</td>
                        <td>' . Tools::getSubValue('content', $v, '暂无说明') . '</td>
                      </tr>';
        }
        $html .= '</table></div>';
        return $html;
    }

    /**
     * 获取指定接口操作下的文档信息
     *
     * @param string $className  - 类名
     * @param string $actionName - 操作名
     * @param array  $actionItem - 接口数据
     * @return string
     */
    private function _getActionItem(string $className, string $actionName, array $actionItem): string
    {
        $actionItem['title']  = $actionItem['title'] ?? '--';
        $actionItem['method'] = $actionItem['method'] ?? '--';
        $actionItem['url']    = $actionItem['url'] ?? '--';
        return <<<EXT
                <div class="list-group-item list-group-item-action action-item  col-md-12" id="{$className}_{$actionName}">
                    <div class="d-flex justify-content-between align-items-center outside">
                        <h4 class="action-title">API - {$actionItem['title']}</h4>
                        <div class="topright">
                            <div class="trightout">请求方式：{$actionItem['method']}</div>
                            <div class="trightout">请求地址：<a href="{$actionItem['url']}">{$actionItem['url']}</a></div>
                        </div>
                    </div>
                    {$this->_getParamData(Tools::getSubValue('param', $actionItem, []))}
                    {$this->_getReturnData(Tools::getSubValue('return', $actionItem, []))}
                    {$this->_getCodeData(Tools::getSubValue('code', $actionItem, []))}
                    {$this->_getDescReturnData(Tools::getSubValue('desc_return', $actionItem, []))}
                </div>
EXT;
    }

    /**
     * 获取指定API类的文档HTML
     *
     * @param $className - 类名称
     * @param $classItem - 类数据
     * @return string
     */
    private function _getClassItem(string $className, array $classItem): string
    {
        $title      = Tools::getSubValue('title', $classItem, '未命名');
        $actionHtml = '';
        if (isset($classItem['action']) && is_array($classItem['action']) && count($classItem['action']) >= 1) {
            foreach ($classItem['action'] as $actionName => $actionItem) {
                $actionHtml .= $this->_getActionItem($className, $actionName, $actionItem);
            }
        }
        return <<<EXT
                    <div class="class-item" id="{$className}">
                        <h3 class="class-title">{$title}</h3>
                        <div class="list-group">{$actionHtml}</div>
                    </div>
EXT;
    }

    /**
     * 获取API文档HTML
     *
     * @param array $data - 文档数据
     * @return string
     */
    private function _getDocList(array $data = []): string
    {
        $html = '';
        if (count($data) < 1) {
            return $html;
        }
        $html .= '<div class="doc-content">';
        foreach ($data as $className => $classItem) {
            $html .= $this->_getClassItem($className, $classItem);
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * 获取顶部导航HTML
     *
     * @param array $data -API文档数据
     * @return string
     */
    private function _getTopNavList(array $data): string
    {
        $html = '<ul class="navbar-nav" id="navbar-nav-top-nav">';
        foreach ($data as $className => $classItem) {
            $title = Tools::getSubValue('title', $classItem, '未命名');
            $html  .= '<li class="nav-item dropdown">';
            $html  .= '<a class="nav-link dropdown-toggle" href="#" id="' . $className . '-nav" data-toggle="dropdown">' . $title . '</a>';
            $html  .= '<div class="dropdown-menu" aria-labelledby="' . $className . '-nav">';
            foreach ($classItem['action'] as $actionName => $actionItem) {
                $title = Tools::getSubValue('title', $actionItem, '未命名');
                $id    = $className . '_' . $actionName;
                $html  .= '<a class="dropdown-item" href="#' . $id . '">' . $title . '</a>';
            }
            $html .= '</div></li>';
        }
        $html .= ' <li class="nav-item"><a class="nav-link" href="?download=api_doc_php">下载文档</a></li>';
        $html .= ' <li class="nav-item"><a class="nav-link" href="/static/theme/img/doc.png" target="_blank">接口流程图</a></li>';
        $html .= '</ul>';
        return $html;
    }

    /**
     * 获取文档CSS
     *
     * @return string
     */
    private function _getCss(): string
    {
        $path = realpath($this->bootstrapCss);
        if (!$path || !is_file($path)) {
            return $this->customCss;
        }
        $bootstrapCss = file_get_contents($path);
        if (empty($bootstrapCss)) {
            return $this->customCss;
        }
        $this->customCss = '<style type="text/css">' . $bootstrapCss . '</style>' . $this->customCss;
        // $this->customCss = ' <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet">' . $this->customCss;
        return $this->customCss;
    }

    /**
     * 获取文档JS
     *
     * @return string
     */
    private function _getJs(): string
    {
        //  $js = '<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>';
        //  $js .= '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" type="text/javascript"></script>';
        //  $this->customJs = $js . $this->customJs;
        //  return $this->customJs;
        $bootstrapJs = realpath($this->bootstrapJs);
        $jQueryJs    = realpath($this->jQueryJs);
        if (!$bootstrapJs || !$jQueryJs || !is_file($bootstrapJs) || !is_file($jQueryJs)) {
            $this->customJs = '';
            return $this->customCss;
        }
        $bootstrapJs = file_get_contents($bootstrapJs);
        $jQueryJs    = file_get_contents($jQueryJs);
        if (empty($bootstrapJs) || empty($jQueryJs)) {
            $this->customJs = '';
            return $this->customJs;
        }
        $js             = '<script type="text/javascript">' . $jQueryJs . '</script>' . '<script type="text/javascript">' . $bootstrapJs . '</script>';
        $this->customJs = $js . $this->customJs;
        return $this->customJs;
    }
}
