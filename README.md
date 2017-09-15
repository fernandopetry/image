# Thumbnail

### Como instalar via composer

```SHELL
composer require fernandopetry/image
```

### Usando em um arquivo para gerar thumbs

```PHP
// thumb.php

<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';
use \Petry\Image\Thumbnail\Thumbnail;

// path completo da imagem
$img = dirname(__FILE__) . '/cao.jpg';
// path onde será gravado as thumbs
$path = dirname(__FILE__) . '/thumb';

// inicializa o thumbnail
$thumb = new Thumbnail($img,$path,200,150);
$thumb->generate();

// exibi a imagem
$thumb->show();
```

### Usando 2

```PHP
<?php
require_once 'vendor/autoload.php';
use \Petry\Image\Thumbnail\Thumbnail;

// path completo da imagem
$img = dirname(__FILE__) . '/cao.jpg';
// path onde será gravado as thumbs
$path = dirname(__FILE__) . '/thumb';

// inicializa o thumbnail
$thumb = new Thumbnail($img,$path,200,150,'opcional nome da imagem');
$thumb->generate();

// Path completo do thumbnail
$thumb->getPathThumbnail();

// Imagem no formato base64
$thumb->getBase64();

// Nome do thumbnail
$thumb->getThumbnailName();
```