<?php

namespace Petry\Image\Thumbnail;

use Petry\Image\Canvas;


class Thumbnail
{
    /**
     * Path completo da imagem
     * @var string
     */
    private $pathSourceImage;

    /**
     * Path completo do local onde ficará salva a thumb
     * @var string
     */
    private $pathDestination;

    /**
     * Informação de erro
     * @var string
     */
    private $erroInfo;

    /**
     * Largura da Thumb
     * @var integer
     */
    private $width;

    /**
     * Altura da Thumb
     * @var integer
     */
    private $height;

    /**
     * Nome do thumbnail
     * @var string
     */
    private $thumbnailName;

    /**
     * Nome customizado
     * @var string
     */
    private $customName = false;

    /**
     * Thumbnail constructor.
     * @param $pathSourceImage Path completo da imagem
     * @param $pathDestination Path completo de onde a thumb ficará salva
     * @param $width           Largura do Thumb
     * @param $height          Altura do Thumb
     */
    public function __construct($pathSourceImage, $pathDestination, $width, $height, $customName = false)
    {
        $this->pathSourceImage = $pathSourceImage;
        $this->pathDestination = $pathDestination;
        $this->width = $width;
        $this->height = $height;
        $this->customName = ($customName) ? $this->removeAccents($customName) : $customName;
        $this->setNameThumb();
    }

    private function removeAccents($string, $slug = "-")
    {
        $Format = array();
        $Format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        $Format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';
        $Data = strtr(utf8_decode($string), utf8_decode($Format['a']), $Format['b']);
        $Data = strip_tags(trim($Data));
        $Data = str_replace(' ', $slug, $Data);
        $Data = str_replace(array(
            str_repeat($slug, 6),
            str_repeat($slug, 5),
            str_repeat($slug, 4),
            str_repeat($slug, 3),
            str_repeat($slug, 2)), $slug, $Data);
        return strtolower(utf8_encode($Data));
    }

    /**
     * Verifica se a imagem é econtrada no servidor
     * @return bool
     */
    private function verifyIsExists()
    {
        if (!file_exists($this->pathSourceImage)) {
            $this->erroInfo = 'Imagem não localizada no servidor.';
            return false;
        }
        return true;
    }

    /**
     * Verifica se o diretorio tem permissão de escrita
     * @return bool
     */
    private function verifyWritable()
    {
        if (!is_writable($this->pathDestination)) {
            $this->erroInfo = 'Diretorio não tem permissão de escrita.';
            return false;
        }
        return true;
    }

    /**
     * Tenta criar a pasta de thumbnail caso não exista
     * @return bool
     */
    private function createDirectory()
    {
        if (!is_dir($this->pathDestination)) {
            if (!mkdir($this->pathDestination, 0775, true)) {
                $this->erroInfo = 'A pasta de thumb não localizado e não foi possível criar.';
                return false;
            }
        }
        return true;
    }

    /**
     * Extensão da imagem
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->pathSourceImage, PATHINFO_EXTENSION);
    }

    /**
     * Gera o destino da imagem
     * @return string
     */
    public function getPathThumbnail()
    {
        return $this->pathDestination . DIRECTORY_SEPARATOR . $this->getThumbnailName();
    }

    /**
     * Gerar o nome do thumbnail
     */
    private function setNameThumb()
    {
        $extension = $this->getExtension();
        if ($this->customName) {
            $this->thumbnailName = $this->customName . '.' . $extension;
        } else {
            $this->thumbnailName = md5($this->pathSourceImage . $this->width . $this->height) . '.' . $extension;
        }
    }

    /**
     * @return string
     */
    public function getThumbnailName()
    {
        return $this->thumbnailName;
    }


    /**
     * Tenta gerar a imagem Thumbnail
     * @return bool
     */
    private function generateThumb()
    {
        $save = $this->getPathThumbnail();

        // não gerar o thumbnail caso já exista
        if (file_exists($save)) return $save;

        $thumb = Canvas::Instance();
        $thumb->carrega($this->pathSourceImage);
        $thumb->redimensiona($this->width, $this->height, 'crop');
        $thumb->grava($save);

        if (file_exists($save)) {
            return true;
        } else {
            $this->erroInfo = 'Não possível gerar o thumb (canvas)';
            return false;
        }
    }

    /**
     * Exibe a imagem
     * @return bool
     */
    public function generate()
    {
        if (!$this->createDirectory())
            return false;

        if (!$this->verifyIsExists())
            return false;

        if (!$this->verifyWritable())
            return false;


        $pathImage = $this->getPathThumbnail();

        if (file_exists($pathImage)) {
            return $pathImage;
        } else {
            if (!$this->generateThumb())
                return false;

            return $pathImage;
        }
    }

    /**
     * Converte a imagem para o formato base64 que pode ser jogado diretamente em src da imagem
     * @return string
     */
    public function getBase64()
    {
        $path = $this->getPathThumbnail();
        $type = $this->getExtension();
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    /**
     * Exibi a imagem, para este metodo o php não pode ter enviado nenhum header antes
     */
    public function show()
    {
//        $extension = $this->getExtension();
//        $image = $this->getPathThumbnail();
//        header('Content-type:image/'.$extension);
//        readfile($image);
        $this->showDirect($this->getPathThumbnail());
    }

    /**
     * Exibi uma imagem diretamente sem redimensionar
     * @param $image
     * @throws \Exception
     */
    public function showDirect($image)
    {
        if (!file_exists($image)) {
            throw new \Exception("Arquivo de imagem não encontrada!", E_USER_ERROR);
        }

        $extension = pathinfo($image, PATHINFO_EXTENSION);
        header('Content-type:image/' . $extension);
        readfile($image);
    }

}