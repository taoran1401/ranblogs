<?php
/**
 * phar打包程序
 */

//开启错误提示
ini_set("display_errors","On");
error_reporting(E_ALL);

//设置路径
$src = "../src/";
$build = "../build/";
$filename = 'PharDemo.phar';

//创建phar对象
$phar = new Phar($build . $filename, FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, $filename);

//设置需要构建的文件目录归档
$phar->buildFromDirectory($src);

//压缩当前归档的目录中所有文件
$phar->compressFiles(\Phar::GZ);

//setStub: 创建stub文件，stub文件用来告诉Phar在被加载时干什么
//createDefaultStub: 创建默认stub
$phar->setStub($phar->createDefaultStub());