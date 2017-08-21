<?php
/**
 * Created by shellvon.
 *
 * @author    : fengxingchao<fengxingchao@camera360.com>
 * @date      : 2017/8/19
 * @time      : 上午10:00
 * @version   1.0
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace DocMan\Model;

class Request extends BaseModel
{
    /**
     * @var mixed 可以唯一标志此请求的某样东西吧.
     */
    public $id;

    /**
     * @var string API名字.
     */
    public $name;

    /**
     * @var string 描述.
     */
    public $description;

    /**
     * @var string API请求方式.
     */
    public $method;

    /**
     * @var string API请求地址.
     */
    public $endpoint;


    /**
     * @var APIGroups API请求所属组.
     */
    public $group;


    /**
     * @var string 请求样例.
     */
    public $examples;

    /**
     * @var Response[] 返回数组.
     */
    public $responses;

    /**
     * @var [] Request header.
     */
    public $headers;

    /**
     * @var [] (Reset API用的)
     */
    public $pathVariables;

    /**
     * @var [] 请求参数.
     */
    public $payloadParams;
}
