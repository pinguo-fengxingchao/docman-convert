<?php

/**
 * Created by shellvon.
 *
 * @author    : fengxingchao<fengxingchao@camera360.com>
 * @date      : 2017/8/18
 * @time      : 上午10:25
 * @version   1.0
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace DocMan\Parser;

use DocMan\Model\API;

interface ParserInterface
{
    /**
     * parse 逻辑.
     *
     * @return API
     */
    public function parse();

    /**
     * @return bool 判断当前数据是否可以被parse.
     */
    public function isParsable();
}
