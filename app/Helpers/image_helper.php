<?php

function resize($source, $width, $height)
{
    $imgdata = file_get_contents($source);
    $srcImg  = imagecreatefromstring($imgdata);
    $dstImg  = imagecreatetruecolor($width, $height);
    list($src_width, $src_height) = getimagesize($source);
    // crop image
    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $width, $height, $src_width, $src_height);
    // save image
    imagejpeg($dstImg, $source, 90);
    // clean memory
    imagedestroy($dstImg);
    imagedestroy($srcImg);
    
    return true;
}

function crop($source, $x, $y, $w, $h)
{
    $imgdata = file_get_contents($source);
    $srcImg  = imagecreatefromstring($imgdata);
    $dstImg  = imagecreatetruecolor($w, $h);
    // crop image
    imagecopyresampled($dstImg, $srcImg, 0, 0, $x, $y, $w, $h, $w, $h);
    // save image
    imagejpeg($dstImg, $source, 90);
    // clean memory
    imagedestroy($dstImg);
    imagedestroy($srcImg);
    
    return true;
}

function fit_social_image($source) 
{
    $w = $h = 128;
    $imgdata = file_get_contents($source);
    $srcImg  = imagecreatefromstring($imgdata);
    $dstImg  = imagecreatetruecolor($w, $h);
    // Resize and crop
    list($src_width, $src_height) = getimagesize($source);
    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $w, $h, $src_width, $src_width); // Take a square with sides equal to width
    // Save image
    imagejpeg($dstImg, $source, 100);
    // Clean memory
    imagedestroy($srcImg);
    imagedestroy($dstImg);
    
    return true;
}

/* End of file helpers/image_helper.php */