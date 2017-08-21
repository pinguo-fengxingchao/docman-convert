<?php
/**
 * Created by shellvon.
 *
 * @author    : fengxingchao<fengxingchao@camera360.com>
 * @date      : 2017/8/21
 * @time      : 下午1:59
 * @version   1.0
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

$extensions = ['php'];
$app = 'docman.phar';
$dir = __DIR__;
$phar = new Phar($dir.'/'.$app, FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, $app);
$phar->startBuffering();
foreach ($extensions as $ext) {
    $phar->buildFromDirectory($dir.'/../', '/\.'.$ext.'$/');
}
$phar->addFile('bin/docman');
$phar->delete('bin/build.php');
$phar->setDefaultStub('bin/docman');
$phar->stopBuffering();
echo 'finished'.PHP_EOL;
