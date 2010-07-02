<?php

################################################################################
## thumbnail.php
##   by Minh Phan (c) 2007
##
##   NEEScentral Thumbnail class
################################################################################

class ImageThumbnail {

//  private $default_h = 60;
//  private $default_w = 60;
  
  private $default_h = 90;
  private $default_w = 75;

  const TYPE_GIF = 1;
  const TYPE_JPG = 2;
  const TYPE_PNG = 3;

  public function __construct() {
  }


  public function createThumbnail( $src, $size, $save_dir, $save_name=null ) {

    if(empty($src)) return;
    if(!is_dir($save_dir)) return;

    $save_dir .= ( substr($save_dir,-1) != "/") ? "/" : "";

    if(empty($save_name)) {
      $pathinfo = pathinfo($src);
      $save_name = "thumb_" . time() . "_" . $pathinfo['basename'];
    }

    if(($imageFormat = $this->getImageFormat($pathinfo['extension'])) !== null) {
      $func = "imagecreatefrom".$imageFormat;
      $src_img = $func($src);

      $old_w = imageSX($src_img);
      $old_h = imageSY($src_img);

      if(!is_null($size) && is_numeric($size)) {
        $new_h = $size;
        $new_w = $size;
      }
      else {
        $new_h = $this->default_h;
        $new_w = $this->default_w;
      }

      if(($old_w > $new_w) || ($old_h > $new_h)) {
        if ($old_w > $old_h) {
          $thumb_w = $new_w;
          $thumb_h = $old_h*($new_h/$old_w);
        }
        else if ($old_w < $old_h) {
          $thumb_w = $old_w*($new_w/$old_h);
          $thumb_h = $new_h;
        }
        else {
          $thumb_w = $new_w;
          $thumb_h = $new_h;
        }

        $dst_img =ImageCreateTrueColor($thumb_w,$thumb_h);

        // Retain transparent for gif and png image
        if (($imageFormat == "gif") || ($imageFormat == "png")) {
          ImageColorTransparent($dst_img, ImageColorAllocate($dst_img, 0, 0, 0));
          ImageAlphaBlending($dst_img, false);
        }

        imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_w,$old_h);
        ob_end_clean();
      }
      else {
        $dst_img = $src_img;
        $thumb_w = $old_w;
        $thumb_h = $old_h;

        ob_clean();
      }

      header("Content-type: image/$imageFormat");
      $imageType = "image".$imageFormat;
      $imageType($dst_img);

      if($dst_img) imagedestroy($dst_img);
      if($src_img) imagedestroy($src_img);
    }
  }


  /**
   * Resize a image and save it to a new location
   *
   * @param String $src: Image Source ("/path/name" or url
   * @param int $size: the max size in both width and height
   * @param String $save_dir: path to save
   * @param String $save_name: name to save
   * @return boolean true or false
   */
  //function smart_resize_image( $file, $width = 0, $height = 0, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false )

  function img_resize( $src, $size, $save_file=null )
  {
    if(empty($src)) return;

    $imageInfo = GetImageSize($src);

    // The provided $src is not a valid image, get out from here.
    if(!is_array($imageInfo)) return false;

    $type = $imageInfo[2];

    switch($type)
    {
      case self::TYPE_GIF: $src_img = imagecreatefromgif($src); break;
      case self::TYPE_JPG: $src_img = imagecreatefromjpeg($src); break;
      case self::TYPE_PNG: $src_img = imagecreatefrompng($src); break;
      default:  $src_img = imagecreatefromjpeg($src);
    }

    $old_w = $imageInfo[0];
    $old_h = $imageInfo[1];

    if(!is_null($size) && is_numeric($size)) {
      $new_h = $new_w = $size;
    }
    else {
      $new_h = $this->default_h;
      $new_w = $this->default_w;
    }

    if(($old_w > $new_w) || ($old_h > $new_h)) {
      if ($old_w > $old_h) {
        $thumb_w = $new_w;
        $thumb_h = $old_h*($new_h/$old_w);
      }
      else if ($old_w < $old_h) {
        $thumb_w = $old_w*($new_w/$old_h);
        $thumb_h = $new_h;
      }
      else {
        $thumb_w = $new_w;
        $thumb_h = $new_h;
      }

      $dst_img = imagecreate($thumb_w, $thumb_h);
      $dst_img = imagecreatetruecolor($thumb_w,$thumb_h);

      // Retain transparent for gif and png image
      if (($type == self::TYPE_GIF) || ($type == self::TYPE_PNG)) {
        $trnprt_indx = ImageColorTransparent($src_img);

        if ($trnprt_indx >= 0) {
          // Get the original image's transparent color's RGB values
          $trnprt_color = imagecolorsforindex($src_img, $trnprt_indx);

          // Allocate the same color in the new image resource
          $trnprt_indx = imagecolorallocate($dst_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

          // Completely fill the background of the new image with allocated color.
          imagefill($dst_img, 0, 0, $trnprt_indx);

          // Set the background color for new image to transparent
          imagecolortransparent($dst_img, $trnprt_indx);
        }
        elseif($type == self::TYPE_PNG) {
          // Turn off transparency blending (temporarily)
          imagealphablending($dst_img, false);

          // Create a new transparent color for image
          $color = imagecolorallocatealpha($dst_img, 0, 0, 0, 127);

          // Completely fill the background of the new image with allocated color.
          imagefill($dst_img, 0, 0, $color);

          // Restore transparency blending
          imagesavealpha($dst_img, true);
        }

        ImageAlphaBlending($dst_img, false);
      }

      imagecopyresampled($dst_img, $src_img , 0, 0, 0, 0, $thumb_w, $thumb_h, $old_w, $old_h);
    }
    else {
      $dst_img = $src_img;
      ob_clean();
    }

    if(empty($save_file)) {
      $mime = image_type_to_mime_type($imageInfo[2]);
      header("Content-type: $mime");
      return true;
    }

    switch($type)
    {
      case self::TYPE_GIF: $img_save = imagegif($dst_img, $save_file); break;
      case self::TYPE_JPG: $img_save = imagejpeg($dst_img, $save_file); break;
      case self::TYPE_PNG: $img_save = imagepng($dst_img, $save_file); break;
      default:  $img_save = imagejpeg($dst_img, $save_file); break;
    }

    if($dst_img) imagedestroy($dst_img);
    if($src_img) imagedestroy($src_img);

    if($img_save) return true;
    return false;
  }
  
  /**
   * 
   *
   */
  function img_resizeByDimensions( $src, $size_w, $size_h, $save_file=null )
  {
    if(empty($src)) return;

    $imageInfo = GetImageSize($src);

    // The provided $src is not a valid image, get out from here.
    if(!is_array($imageInfo)) return false;

    $type = $imageInfo[2];

    switch($type)
    {
      case self::TYPE_GIF: $src_img = imagecreatefromgif($src); break;
      case self::TYPE_JPG: $src_img = imagecreatefromjpeg($src); break;
      case self::TYPE_PNG: $src_img = imagecreatefrompng($src); break;
      default:  $src_img = imagecreatefromjpeg($src);
    }

    $old_w = $imageInfo[0];
    $old_h = $imageInfo[1];

    if( (!is_null($size_w) && is_numeric($size_w)) && 
        (!is_null($size_h) && is_numeric($size_h)) ) {
      $new_w = $size_w;
      $new_h = $size_h;
    }
    else {
      $new_h = $this->default_h;
      $new_w = $this->default_w;
    }

    if(($old_w > $new_w) || ($old_h > $new_h)) {
      if ($old_w > $old_h) {
//      $thumb_w = $new_w;
//      $thumb_h = $old_h*($new_h/$old_w);
        
        $dRatio = $old_w / $old_h;
  	
  	    $thumb_w = $new_w;
        $thumb_h = $thumb_w / $dRatio;
      }
      else if ($old_w < $old_h) {
//      $thumb_w = $old_w*($new_w/$old_h);
//      $thumb_h = $new_h;
        
        $dRatio = $old_h / $old_w;
	    	
        $thumb_h = $new_h;
        $thumb_w = $thumb_h / $dRatio;
      }
      else {
        $thumb_w = $new_w;
        $thumb_h = $new_h;
      }

      $dst_img = imagecreate($thumb_w, $thumb_h);
      $dst_img = imagecreatetruecolor($thumb_w,$thumb_h);

      // Retain transparent for gif and png image
      if (($type == self::TYPE_GIF) || ($type == self::TYPE_PNG)) {
        $trnprt_indx = ImageColorTransparent($src_img);

        if ($trnprt_indx >= 0) {
          // Get the original image's transparent color's RGB values
          $trnprt_color = imagecolorsforindex($src_img, $trnprt_indx);

          // Allocate the same color in the new image resource
          $trnprt_indx = imagecolorallocate($dst_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

          // Completely fill the background of the new image with allocated color.
          imagefill($dst_img, 0, 0, $trnprt_indx);

          // Set the background color for new image to transparent
          imagecolortransparent($dst_img, $trnprt_indx);
        }
        elseif($type == self::TYPE_PNG) {
          // Turn off transparency blending (temporarily)
          imagealphablending($dst_img, false);

          // Create a new transparent color for image
          $color = imagecolorallocatealpha($dst_img, 0, 0, 0, 127);

          // Completely fill the background of the new image with allocated color.
          imagefill($dst_img, 0, 0, $color);

          // Restore transparency blending
          imagesavealpha($dst_img, true);
        }

        ImageAlphaBlending($dst_img, false);
      }

      imagecopyresampled($dst_img, $src_img , 0, 0, 0, 0, $thumb_w, $thumb_h, $old_w, $old_h);
    }
    else {
      $dst_img = $src_img;
      ob_clean();
    }

    if(empty($save_file)) {
      $mime = image_type_to_mime_type($imageInfo[2]);
      header("Content-type: $mime");
      return true;
    }

    switch($type)
    {
      case self::TYPE_GIF: $img_save = imagegif($dst_img, $save_file); break;
      case self::TYPE_JPG: $img_save = imagejpeg($dst_img, $save_file); break;
      case self::TYPE_PNG: $img_save = imagepng($dst_img, $save_file); break;
      default:  $img_save = imagejpeg($dst_img, $save_file); break;
    }

    if($dst_img) imagedestroy($dst_img);
    if($src_img) imagedestroy($src_img);

    if($img_save) return true;
    return false;
  }


  function getImageFormat($extension) {
    $extension = strtolower($extension);

    $png_array = array("png");
    $jpg_array = array("jpg", "jpeg", "jpe");
    $gif_array = array("gif");
    $bmp_array = array("bmp");

    if (in_array($extension, $png_array)) {
      return "png";
    }elseif (in_array($extension, $jpg_array)){
      return "jpeg";
    }elseif (in_array($extension, $gif_array)){
      return "gif";
    }else{
      return null;
    }
  }


  /**
   * Get the Video thumbnail by running ffmpeg from shell
   *
   * @param String $inputVideo: '/nees/home/.../video.mpg'
   * @param String $outputVideo: '/nees/home/Public.groups/output.png
   * @param int $numOfThumb: How many thumbnail will be created
   * @param String $imgFormat: the string format of the output thumbnail: jpg, gif, png (and maybe more ?)
   * @param int $thumb_width: width of thumbnail
   * @param int $thumb_height: height of thumbnail
   * @return boolean
   */
  function createVideoThumbnail($inputVideo, $outputVideo, $numOfThumb=1, $imgFormat="png", $thumb_width=60, $thumb_height=40) {

    $size = $thumb_width . "x" . $thumb_height;
    $ffmpegCmd = "ffmpeg -i '".$inputVideo."' -vframes ". $numOfThumb ." -ss 00:00:05 -an -vcodec ". $imgFormat." -f rawvideo -s " . $size . " '" . $outputVideo . "'";

    $ret = shell_exec($ffmpegCmd);

    return file_exists($outputVideo);
  }
}

?>