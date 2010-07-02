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
  	
  }//end class

?>
