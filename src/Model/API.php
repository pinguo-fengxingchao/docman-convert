<?php
/**
 * Created by shellvon.
 *
 * @author    : fengxingchao<fengxingchao@camera360.com>
 * @date      : 2017/8/18
 * @time      : 下午7:24
 * @version   1.0
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace DocMan\Model;

class API
{

    /**
     * @var string API请求名字.
     */
    public $name;

    /**
     * @var string 版本.
     */
    public $version;

    /**
     * @var string 描述.
     */
    public $description;

    /**
     * @var string API地址.
     */
    public $url;

    /**
     * @var string 测试地址.
     */
    public $sampleUrl = null;

    /**
     * @var APIGroups[] API请求组.
     */
    public $apiGroups;
}
