<?php

/**
 * Created by shellvon.
 *
 * @author    : fengxingchao<fengxingchao@camera360.com>
 * @date      : 2017/8/18
 * @time      : 上午10:19
 * @version   1.0
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace DocMan\Parser;

use DocMan\Model\API;
use DocMan\Model\APIGroups;
use DocMan\Model\Parameter;
use DocMan\Model\Request;
use DocMan\Model\Response;

class PostmanParser implements ParserInterface
{
    protected $rawData = null;



    public function __construct($content = null)
    {
        $this->rawData = $content;
    }

    /**
     * 瞎处理.
     *
     * @return API
     */
    public function parse()
    {
        if (!$this->isParsable()) {
            return;
        }
        $data = json_decode($this->rawData, true);
        $api = new API();
        $api->name = $data['info']['name'];
        $api->description = $data['info']['description'];
        $api->version = '1.0.0';
        foreach ($data['item'] as $group) {
            $apiGroup = new APIGroups($group['name'], $group['description']);
            foreach ($group['item'] as $item) {
                $request = $this->extractRequest($item, $apiGroup);
                $apiGroup->requests[] = $request;
            }
            $api->apiGroups[] = $apiGroup;
        }
        return $api;
    }

    /**
     * 提取请求对象.
     *
     * @param array $data
     * @param APIGroups $currentGroup 当前所属组.
     *
     * @return Request
     */
    protected function extractRequest($data, $currentGroup)
    {
        $request = new Request();
        $request->name = $data['name'];
        $request->description = $data['request']['description'];
        $request->method = $data['request']['method'];

        $request->endpoint = isset($data['request']['name']) ? $data['request']['name'] : $data['name'];
        $request->headers = $data['request']['header'];
        $request->responses = $this->extractResponses($data['response']);
        $request->payloadParams = $this->extractParameters($data['request']);
        $request->group = $currentGroup;
        // 唯一标志此请求.
        $request->id = $currentGroup->name.'|'.$request->name;
        return $request;
    }

    /**
     * 提取响应对象,一个请求可能有多个响应.
     *
     * @param array $responses
     *
     * @return Response[]
     */
    protected function extractResponses($responses)
    {
        return array_map(function ($el) {
            $response =  new Response();
            $response->status = $el['status'];
            $response->name = $el['name'];
            $response->id = $el['id'];
            $response->statusCode = $el['code'];
            $response->body = $el['body'];
            $response->headers = $el['header'];
            $response->type = isset($el['_postman_previewlanguage']) ? $el['_postman_previewlanguage'] : 'text';
            return $response;
        }, $responses);
    }

    /**
     * 提取参数.
     *
     * @param array $item
     *
     * @return array
     */
    protected function extractParameters($item)
    {
        $query = isset($item['url']['query']) ? $item['url']['query'] : null;
        $body = isset($item['body']) ? $item['body'] : null;
        return [
            'qs' => $this->extractQueryParamsFromQuery($query),
            'body' => $this->extractBodyParams($body)
        ];
    }

    /**
     * 从消息提中提取参数.通常用于POST／PUT之类.
     *
     * @param array $body
     *
     * @return array
     * @throws \Exception
     */
    protected function extractBodyParams($body)
    {
        if (!$body) {
            return null;
        }
        $mode = $body['mode'];
        if ($mode === 'urlencoded' || $mode === 'formdata') {
            return $this->extractBodyFromFormDataOrUrlEncoded($body);
        }
        if ($mode === 'file') {
            throw new \Exception(sprintf('unsupported body mode:[%s]', $mode));
        }
        if ($mode === 'raw') {
            return $this->extractBodyFromRawBody($body);
        }
        return null;
    }

    /**
     * @param array $body
     *
     * @return Parameter[]
     */
    protected function extractBodyFromFormDataOrUrlEncoded($body)
    {
        $data = isset($body['urlencoded']) ? $body['urlencoded'] : $body['formdata'];
        return $data ? array_map(function ($el) {
            return new Parameter($el['key'], $el['value'], isset($el['type']) ? $el['type']: 'string', $el['description'], $el['value'], true);
        }, $data) : null;
    }

    /**
     * 从rawBody中提取请求参数.
     *
     * @param array $body
     *
     * @return Parameter[]
     */
    protected function extractBodyFromRawBody($body)
    {
        $payloads = json_decode($body['raw'], true);
        if (!is_array($payloads)) {
            return;
        }
        $params = [];
        foreach ($payloads as $k => $v) {
            $params[] = new Parameter($k, $v, 'string', '该参数没有描述');
        }
        return $params;
    }


    /**
     * @param array $query GET中的请求数组.
     *
     * @return  Parameter[] $query
     */
    protected function extractQueryParamsFromQuery($query)
    {
        return $query ? array_map(function ($el) {
            return new Parameter($el['key'], $el['value'], isset($el['type']) ? $el['type']: 'string', $el['description'], $el['value'], true);
        }, $query) : null;
    }

    /**
     * 瞎几把猜能否被parsed.
     *
     * @return bool
     */
    public function isParsable()
    {
        $parsedData = json_decode($this->rawData, true);

        if (!is_array($parsedData)) {
            return false;
        }
        $score = isset($parsedData['info']) ? 0.2 : 0;

        $schema = isset($parsedData['info']['schema']) ? $parsedData['info']['schema'] : null;
        if ($schema === 'https://schema.getpostman.com/json/collection/v2.0.0/collection.json') {
            $score += 0.3;
        }
        if (isset($parsedData['item']) && is_array($parsedData['item'])) {
            $score += 0.5;
        }
        return $score > 0.8;
    }
}
