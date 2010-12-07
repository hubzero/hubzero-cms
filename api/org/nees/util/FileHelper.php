<?php 

  class FileHelper{
  	
  	/**
  	 * Downloads the content of a file.
  	 * @param $p_strPathToFile - absolute path to the file.
  	 * @see http://www.finalwebsites.com/forums/topic/php-file-download
  	 */
    public static function download($p_strPathToFile){
  	  $fullPath = $p_strPathToFile;
	  if ($fd = fopen ($fullPath, "r")) {
        $fsize = filesize($fullPath);
        $path_parts = pathinfo($fullPath);
        $ext = strtolower($path_parts["extension"]);
        switch ($ext) {
          case "pdf":
          header("Content-type: application/pdf"); // add here more headers for diff. extensions
          header("Content-Disposition: inline; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
          break;
          default:
          header("Content-type: application/octet-stream");
          header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
          break;
        }//end switch
      
        header("Content-length: $fsize");
        header("Cache-control: private"); //use this to open files directly
        while(!feof($fd)) {
          $buffer = fread($fd, 2048);
          echo $buffer;
        }//end while feof
	  }//end fd
	  fclose ($fd);
	  exit;
    }//end download
    
    /**
  	 * Downloads the content of an archive (gz) file.
  	 * @param $p_strPathToFile - absolute path to the file.
  	 * @see http://www.finalwebsites.com/forums/topic/php-file-download
  	 */
    public static function downloadTarBall($p_strPathToFile){
      $fullPath = $p_strPathToFile;
      if ($fd = fopen ($fullPath, "r")) {
        $fsize = filesize($fullPath);
        $path_parts = pathinfo($fullPath);
        header("Content-type: application/x-gzip"); // add here more headers for diff. extensions
        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
        header("Content-Transfer-Encoding: binary\n");
        header("Pragma: cache");
        header("Cache-Control: cache, must-revalidate");
        header("Content-length: $fsize");
        ob_clean();
       
        while(!feof($fd)) {
          $buffer = fread($fd, 8192);
          echo $buffer;
        }//end while feof
      }//end fd
      fclose ($fd);
      exit;
    }//end download

    public static function fixPermissions($p_strDirectory){
      exec("/nees/home/bin/fix_permissions $p_strDirectory", $output);
      return $output;
    }

    public static function fixPermissionsOneFileOrDir($p_strAbsolutePath){
      exec("/nees/home/bin/fix_onefileordir $p_strAbsolutePath", $output);
      return $output;
    }

    public static function downloadCleanup($p_strDirectory="/tmp"){
      if ($handle = opendir($p_strDirectory)) {
        /* This is the correct way to loop over the directory. */
        while (false !== ($file = readdir($handle))) {
          $strAbsoluteFilePath = $p_strDirectory."/".$file;
          if(self::canDeleteDownloadFile($strAbsoluteFilePath)){
            self::deleteDownload($strAbsoluteFilePath);
          }
        }

        closedir($handle);
      }
    }

    public static function canDeleteDownloadFile($p_strPath){
      $bDelete = false;
      if (file_exists($p_strPath)) {
        $strFileModifiedDate = date ("Y-m-d", filemtime($p_strPath));
        $strTodayDate = date("Y-m-d");

        $oTodayDate = strtotime($strTodayDate);
        $oFileModifiedDate = strtotime($strFileModifiedDate);

        if ($oFileModifiedDate < $oTodayDate) {
          $bNeeshub = StringHelper::contains($p_strPath, "[neeshub]");
          $bDigit = StringHelper::contains($p_strPath, "[(d)+]");
          $bDownload = StringHelper::contains($p_strPath, "[download.tar.gz]");
          if($bNeeshub && $bDigit && $bDownload){
            $bDelete = true;
          }
        }
      }
      return $bDelete;
    }

    public static function deleteDownload($p_strFilePath){
      if(self::canDeleteDownloadFile($p_strFilePath)){
        unlink($p_strFilePath);
      }
    }
  	
  }//end class

?>
