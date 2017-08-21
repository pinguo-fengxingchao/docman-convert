<?php
/**
 * Created by shellvon.
 *
 * @author    : fengxingchao<fengxingchao@camera360.com>
 * @date      : 2017/8/19
 * @time      : 上午10:08
 * @version   1.0
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace DocMan\Model;

class Response extends BaseModel
{
    /**
     * @var mixed 随便什么可以唯一标志的
     */
    public $id;

    /**
     * @var string 名字.
     */
    public $name;

    /**
     * @var string 状态说明.
     */
    public $status;

    /**
     * @var int  HTTP Status Code.
     */
    public $statusCode;

    /**
     * @var string 返回数据.
     */
    public $body;

    /**
     * @var [] 返回的请求头.
     */
    public $headers;

    public $type = 'json';
}
