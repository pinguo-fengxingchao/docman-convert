<?php

/**
 * Created by shellvon.
 *
 * @author    : fengxingchao<fengxingchao@camera360.com>
 * @date      : 2017/8/19
 * @time      : 下午12:29
 * @version   1.0
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace DocMan\Utils;

final class Utils
{
    public static function isAssoc(array $arr)
    {
        if (array() === $arr) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
