<?php
/**
 * Created by shellvon.
 *
 * @author    : fengxingchao<fengxingchao@camera360.com>
 * @date      : 2017/8/18
 * @time      : 下午7:17
 * @version   1.0
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace DocMan\Model;

use DocMan\Utils\Utils;

class ApiDoc
{

    /**
     * @var API
     */
    protected $api;

    public function __construct($api = null)
    {
        $this->api = $api;
    }

    public function setAPI($api)
    {
        $this->api = $api;
        return $this;
    }

    public function export($filename, $configPersist = false)
    {
        if ($configPersist) {
            $this->exportConfig($filename);
        }
        $this->exportComment($filename);
    }

    public function exportConfig($filename)
    {
    }

    /**
     * 生成API注释文档.
     *
     * @param $filename
     */
    public function exportComment($filename)
    {
        $apiBlocks = [];
        foreach ($this->api->apiGroups as $group) {
            foreach ($group->requests as $request) {
                $apiMetaBlocks = [
                    sprintf("@api {%s} %s %s", $request->method, $request->endpoint, $request->name),
                    sprintf("@apiDescription %s", $request->description ? $request->description : '此接口暂无描述'),
                    sprintf("@apiName %s", $request->name),
                    sprintf("@apiGroup %s", $request->group->name),
                    "",
                ];
                $paramsBlock = $this->getAPIParamsBlocks($request);

                $responseBlock = $this->getResponseBlocks($request->responses);

                $blocks = array_merge($apiMetaBlocks, $paramsBlock, $responseBlock);

                $apiBlocks[] = sprintf("/**\n *\t%s\n */", join("\n *\t", $blocks));
            }
        }
        $content = join(PHP_EOL.PHP_EOL, $apiBlocks).PHP_EOL;
        if ($filename == 'STDOUT') {
            echo $content;
        } else {
            file_put_contents($filename, $content);
        }
    }

    /**
     * @param Response[] $responses
     *
     * @return array
     */
    protected function getResponseBlocks($responses)
    {
        $blocks = [""];
        foreach ($responses as $response) {
            $statusCode = $response->statusCode;
            $exampleType = 'Error';
            if ($statusCode >= 200 && $statusCode < 400) {
                $exampleType = 'Success';
                $fields = $this->getResponseFields($response->body, $response->type, 'data');
                $blocks = array_merge($blocks, $fields);
            }
            $blocks[] = sprintf("@api%sExample {%s} %s:\n%s", $exampleType, $response->type, $response->name, $this->compressResponseBody($response->body, $response->type));
        }
        return $blocks;
    }

    protected function getResponseFields($body, $type = 'json', $dataField = 'data')
    {
        $blocks = [];
        if ($type != 'json') {
            return $blocks;
        }
        $arr = json_decode($body, true);
        if (!isset($arr[$dataField]) && $dataField) {
            return $blocks;
        }
        if ($dataField) {
            $arr = $arr[$dataField];
        }
        // apidoc只支持俩层
        return $this->getResponseFieldRecursion($arr, '', 2);
    }

    protected function getResponseFieldRecursion($data, $parent = '', $maxDepth = 3)
    {
        $sep = '.';
        $blocks = [];
        if (!is_array($data) || $maxDepth < 0) {
            if (is_array($data)) {
                $type = Utils::isAssoc($data) ? 'object' : 'array';
            } else {
                $type = gettype($data);
            }
            $blocks[] = sprintf("@apiSuccess {%s} %s %s", $type, $parent, '暂无字段描述');
            return $blocks;
        }
        if (Utils::isAssoc($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $blocks = array_merge($blocks, $this->getResponseFieldRecursion($value, $parent ? $parent.$sep.$key : $key, $maxDepth-1));
                } else {
                    $blocks[] = sprintf("@apiSuccess {%s} %s %s", gettype($value), $parent ? $parent.$sep.$key : $key, '暂无字段描述');
                }
            }
        } elseif (isset($data[0])) {
            $blocks[] = sprintf('@apiSuccess {%s} %s %s', 'array', $parent, '暂无字段描述');
            // apidoc不能出现[]
            $blocks = array_merge($blocks, $this->getResponseFieldRecursion($data[0], $parent, $maxDepth -1));
        }
        return $blocks;
    }

    /**
     * 压缩原有返回值.
     *
     * @param string $body
     * @param string $type The response type.
     *
     * @return string
     */
    protected function compressResponseBody($body, $type = 'json')
    {
        if ($type != 'json') {
            return $body;
        }
        $arr = $this->compressArr(json_decode($body, true));
        if (empty($arr)) {
            return $body;
        }
        return json_encode($arr);
    }

    /**
     * 压缩数组,如果是关联数组，则递归操作,普通数组则取第一个元素.
     *
     * @param mixed $arr
     *
     * @return array|mixed
     */
    protected function compressArr($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }
        if (!Utils::isAssoc($arr)) {
            return $arr[0];
        }
        foreach ($arr as $k => $v) {
            $arr[$k] = $this->compressArr($v);
        }
        return $arr;
    }


    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getAPIParamsBlocks($request)
    {
        $blocks = [];
        foreach ($request->payloadParams as $type => $params) {
            /**
             * @var Parameter $parameter
             */
            foreach ((array)$params as $parameter) {
                if ($parameter->required) {
                    $blocks[] = sprintf("@apiParam {%s} %s %s", $parameter->type, $parameter->key, $parameter->description ? $parameter->description : '暂无字段描述');
                } else {
                    $blocks[] = sprintf("@apiParam {%s} [%s=%s] %s", $parameter->type, $parameter->key, $parameter->default, $parameter->description ? $parameter->description : '暂无字段描述');
                }
            }
        }
        return $blocks;
    }
}
