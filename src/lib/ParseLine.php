<?php

namespace phpwdk\apidoc\lib;

/**
 * 按行解析注释参数
 * Class ParseLine
 *
 * @package phpwdk\apidoc\lib
 */
class ParseLine
{
    /**
     * 解析 title|url
     *
     * @param array $line
     *
     * @return array
     */
    public function parseLineTitle(array $line): array
    {
        return ['type' => $line[0] ?? '', 'content' => $line[1] ?? ''];
    }

    /**
     * 解析 param
     *
     * @param array $line
     *
     * @return array
     */
    public function parseLineParam(array $line): array
    {
        return [
            'type'          => $line[0] ?? '',
            'param_type'    => $line[1] ?? '',
            'param_name'    => $line[2] ?? '',
            'param_title'   => $line[3] ?? '',
            'param_default' => $line[4] ?? '',
            'param_require' => $line[5] ?? '',
        ];
    }

    /**
     * 解析 code
     *
     * @param array $line
     *
     * @return array
     */
    public function parseLineCode(array $line): array
    {
        return [
            'type'    => $line[0] ?? '',
            'code'    => $line[1] ?? '',
            'content' => $line[2] ?? '',
        ];
    }

    /**
     * 解析 return
     *
     * @param array $line
     *
     * @return array
     */
    public function parseLineReturn(array $line): array
    {
        return [
            'type'            => $line[0] ?? '',
            'return_type'     => $line[1] ?? '',
            'return_name'     => $line[2] ?? '',
            'return_required' => $line[3] ?? '',
            'return_title'    => $line[4] ?? '',
            'return_superior' => $line[5] ?? '',
            'return_default'  => $line[6] ?? '',
            'return_desc'     => $line[7] ?? '',
        ];
    }

    /**
     * 解析 desc_return
     *
     * @param array $line
     *
     * @return array
     */
    public function parseLinedescReturn(array $line): array
    {
        return [
            'type'            => $line[0] ?? '',
            'return_type'     => $line[1] ?? '',
            'return_name'     => $line[2] ?? '',
            'return_required' => $line[3] ?? '',
            'return_title'    => $line[4] ?? '',
            'return_superior' => $line[5] ?? '',
            'return_default'  => $line[6] ?? '',
            'return_desc'     => $line[7] ?? '',
        ];
    }
}
