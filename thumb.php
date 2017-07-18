<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';
use \Petry\Image\Thumbnail\Thumbnail;

$img = dirname(__FILE__) . '/cao.jpg';
$path = dirname(__FILE__) . '/thumb';
$thumb = new Thumbnail($img,$path,200,150);
$thumb->generate();

$thumb->show();