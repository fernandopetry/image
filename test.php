<?php

ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';
use \Petry\Image\Thumbnail\Thumbnail;

$path = dirname(__FILE__) . '/thumb';
$img = dirname(__FILE__) . '/cao.jpg';

$thumb = new Thumbnail($img,$path,200,150);
$thumb->generate();

echo '<h1>Teste Thumbnail</h1>';
echo '<hr>';

echo 'Path Thumb: ' . $path . '<br>';
echo 'Path Img: ' . $img . '<br>';
echo '<hr>';

var_dump($thumb,$thumb->getPathThumbnail());
?>

<img src="<?= $thumb->getBase64() ?>" alt="">
<img src="thumb.php" alt="">
