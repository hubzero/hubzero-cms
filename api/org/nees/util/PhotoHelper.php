<?php 
  
  class PhotoHelper{
  	
    const TYPE_GIF = 1;
    const TYPE_JPG = 2;
    const TYPE_PNG = 3;

    const DEFAULT_DISPLAY_WIDTH = 800;
    const DEFAULT_DISPLAY_HEIGHT = 600;

    const DEFAULT_THUMB_WIDTH = 90;
    const DEFAULT_THUMB_HEIGHT = 60;

    const EXPERIMENT_THUMB_WIDTH = 120;
    const EXPERIMENT_THUMB_HEIGHT = 90;

    const PROJECT_THUMB_WIDTH = 210;
    const PROJECT_THUMB_HEIGHT = 158;

    const SCALE_BY_WIDTH = 1;
    const SCALE_BY_HEIGHT = 2;
    
    public static function resize($src, $size_w, $size_h, $save_file=null){
      if(empty($src)) return false;

      if(!file_exists($src))return false;

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
        if ($old_w > $old_h) {  //landscape
      	  $dRatio = $old_w / $old_h;
  	
  	  $thumb_w = $new_w;
          $thumb_h = $thumb_w / $dRatio;
        }else if ($old_w < $old_h) {  //portrait
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

      ob_clean();

      if($dst_img){
        if(is_resource($dst_img)){
          imagedestroy($dst_img);
        }
      }
      
      if($src_img){
        if(is_resource($src_img)){
          imagedestroy($src_img);
        }
      }


      $oThumbArray = getimagesize($save_file);
    
    
      if($img_save) return true;
      return false;
    }

    public static function resizeByWidth($src, $size_w, $save_file=null){
      if(empty($src)) return false;

      if(!file_exists($src))return false;

      if(is_null($size_w)) return false;
      if(!is_numeric($size_w)) return false;

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

      if($old_w > $old_h){  //landscape
        $thumb_w = $size_w;

        //compute new height
        $thumb_h = ($old_h * $thumb_w) / $old_w;
      }else{  //portrait
        //$thumb_h = $size_h;
        $thumb_w = $size_w;

        //compute new width
        $ratio = ($old_h / $old_w);

        //$thumb_w = ($old_w * $thumb_h) / $old_h;
        $thumb_h = $thumb_w / $ratio;
      }
      
      if(($old_w > $thumb_w) || ($old_h > $thumb_h)) {
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

      ob_clean();

      if($dst_img){
        if(is_resource($dst_img)){
          imagedestroy($dst_img);
        }
      }

      if($src_img){
        if(is_resource($src_img)){
          imagedestroy($src_img);
        }
      }
      

      $oThumbArray = getimagesize($save_file);


      if($img_save) return true;
      return false;
    }

    /*********************************************/
    /* Fonction: ImageCreateFromBMP              */
    /* Author:   DHKold                          */
    /* Contact:  admin@dhkold.com                */
    /* Date:     The 15th of June 2005           */
    /* Version:  2.0B                            */
    /*********************************************/

    function ImageCreateFromBMP($filename)
    {
     //Ouverture du fichier en mode binaire
       if (! $f1 = fopen($filename,"rb")) return FALSE;

     //1 : Chargement des ent�tes FICHIER
       $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
       if ($FILE['file_type'] != 19778) return FALSE;

     //2 : Chargement des ent�tes BMP
       $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                     '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                     '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
       $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
       if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
       $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
       $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
       $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
       $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
       $BMP['decal'] = 4-(4*$BMP['decal']);
       if ($BMP['decal'] == 4) $BMP['decal'] = 0;

     //3 : Chargement des couleurs de la palette
       $PALETTE = array();
       if ($BMP['colors'] < 16777216)
       {
        $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
       }

     //4 : Cr�ation de l'image
       $IMG = fread($f1,$BMP['size_bitmap']);
       $VIDE = chr(0);

       $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
       $P = 0;
       $Y = $BMP['height']-1;
       while ($Y >= 0)
       {
        $X=0;
        while ($X < $BMP['width'])
        {
         if ($BMP['bits_per_pixel'] == 24)
            $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
         elseif ($BMP['bits_per_pixel'] == 16)
         {
            $COLOR = unpack("n",substr($IMG,$P,2));
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         elseif ($BMP['bits_per_pixel'] == 8)
         {
            $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         elseif ($BMP['bits_per_pixel'] == 4)
         {
            $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
            if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         elseif ($BMP['bits_per_pixel'] == 1)
         {
            $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
            if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
            elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
            elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
            elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
            elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
            elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
            elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
            elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         else
            return FALSE;
         imagesetpixel($res,$X,$Y,$COLOR[1]);
         $X++;
         $P += $BMP['bytes_per_pixel'];
        }
        $Y--;
        $P+=$BMP['decal'];
       }

     //Fermeture du fichier
       fclose($f1);

     return $res;
    }
  }
  
  
?>