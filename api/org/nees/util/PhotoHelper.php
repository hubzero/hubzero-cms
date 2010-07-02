<?php 
  
  class PhotoHelper{
  	
  	const TYPE_GIF = 1;
    const TYPE_JPG = 2;
    const TYPE_PNG = 3;
    
    public static function resize($src, $size_w, $size_h, $save_file=null){
      if(empty($src)) return;

      $imageInfo = GetImageSize($src);

      // The provided $src is not a valid image, get out from here.
      if(!is_array($imageInfo)) return false;

      $type = $imageInfo[2];

      switch($type){
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
      } else {
        $new_h = $size_h;
        $new_w = $size_w;
      }

      if(($old_w > $new_w) || ($old_h > $new_h)) {
        if ($old_w > $old_h) {

      	  $dRatio = $old_w / $old_h;
  	
  	      $thumb_w = $new_w;
          $thumb_h = $thumb_w / $dRatio;
        }else if ($old_w < $old_h) {
        
          $dRatio = $old_h / $old_w;
	    	
          $thumb_h = $new_h;
          $thumb_w = $thumb_h / $dRatio;
        }else {
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
          }elseif($type == self::TYPE_PNG) {
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
      }else {
        $dst_img = $src_img;
        ob_clean();
      }

      if(empty($save_file)) {
        $mime = image_type_to_mime_type($imageInfo[2]);
        header("Content-type: $mime");
        return true;
      }

      switch($type){
        case self::TYPE_GIF: $img_save = imagegif($dst_img, $save_file); break;
        case self::TYPE_JPG: $img_save = imagejpeg($dst_img, $save_file); break;
        case self::TYPE_PNG: $img_save = imagepng($dst_img, $save_file); break;
        default:  $img_save = imagejpeg($dst_img, $save_file); break;
      }

      if($dst_img) imagedestroy($dst_img);
      if($src_img) imagedestroy($src_img);

      $oThumbArray = getimagesize($save_file);
    
    
      if($img_save) return true;
      return false;
    }
  }
  
  
?>