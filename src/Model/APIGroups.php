<?php
/**
 * Created by shellvon.
 *
 * @author    : fengxingchao<fengxingchao@camera360.com>
 * @date      : 2017/8/19
 * @time      : 上午10:33
 * @version   1.0
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace DocMan\Model;

class APIGroups
{
    public function __construct($name, $description = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->id = uniqid('api.group');
    }

    /**
     * @var string the unique id.
     */
    public $id;


    /**
     * @var string 组所在名.
     */
    public $name;

    /**
     * @var string 描述.
     */
    public $description;

    /**
     * @var Request[] 请求数组.
     */
    public $requests;
}
