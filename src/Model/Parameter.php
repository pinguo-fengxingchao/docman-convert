<?php
/**
 * Created by shellvon.
 *
 * @author    : fengxingchao<fengxingchao@camera360.com>
 * @date      : 2017/8/19
 * @time      : 上午10:55
 * @version   1.0
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace DocMan\Model;

class Parameter extends BaseModel
{
    /**
     * 支持的类型.
     */
    const TYPE_INTEGER = 'integer';
    const TYPE_NUMBER = 'number';
    const TYPE_ARRAY = 'array';
    const TYPE_STRING = 'string';
    const TYPE_BOOLEAN = 'boolean';


    /**
     * Parameter constructor.
     *
     * @param        $key
     * @param        $value
     * @param string $type
     * @param string $desc
     * @param null   $default
     * @param bool   $required
     */
    public function __construct($key, $value, $type = 'string', $desc = '', $default = null, $required = true)
    {
        $this->key = $key;
        $this->value = $value;
        $this->type = $type;
        $this->description = $desc;
        $this->default = $default;
        $this->required = $required;
    }

    /**
     * @var string 参数名.
     */
    public $key;

    /**
     * @var string 参数值.
     */
    public $value;

    /**
     * @var string 参数类型.
     */
    public $type;

    /**
     * @var string 默认值.
     */
    public $default;

    /**
     * @var string 参数说明.
     */
    public $description;


    /**
     * @var bool 参数是否可选.
     */
    public $required = true;
}
