<?php
echo phpinfo();
$source = 'img/Jellyfish.jpeg';
$destination = 'img/resize.jpg';
$width  = 187;
$height = 311;

$imagine   = new Imagine\Gd\Imagine();
$size      = new Imagine\Image\Box($width, $height);
$mode      = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
$resizeimg = $imagine->open($source)
                ->thumbnail($size, $mode);
$sizeR     = $resizeimg->getSize();
$widthR    = $sizeR->getWidth();
$heightR   = $sizeR->getHeight();

$preserve  = $imagine->create($size);
$startX = $startY = 0;
if ( $widthR < $width ) {
    $startX = ( $width - $widthR ) / 2;
}
if ( $heightR < $height ) {
    $startY = ( $height - $heightR ) / 2;
}
$preserve->paste($resizeimg, new Imagine\Image\Point($startX, $startY))
    ->save($destination);
