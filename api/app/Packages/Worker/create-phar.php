<?php
ini_set("display_errors","On");
error_reporting(E_ALL);
$srcRoot = "./src";

$buildRoot = "./build";

$phar = new Phar($buildRoot . "/myphar.phar", FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, "myphar.phar");

$phar["index.php"] = file_get_contents($srcRoot . "/index.php");

$phar["common.php"] = file_get_contents($srcRoot . "/common.php");

$phar->setStub($phar->createDefaultStub("index.php"));



copy($srcRoot . "/config.ini", $buildRoot . "/config.ini");