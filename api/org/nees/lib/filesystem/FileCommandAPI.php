<?php

require_once "lib/data/DataFile.php";

class FileCommandAPIError extends Exception {
    public function __construct($error,$errorcode=-1) {
        parent::__construct($error,$errorcode);
    }
}

/** ****************************************************************************
 * @title
 *   FileCommandAPI Class
 *
 * @author
 *   ???, Greg Crawford
 *
 * @abstract
 *   Abstract base class for the File Command API.
 *
 * @description
 *   The File class encapsulates the standard file system commands such as
 *   delete, copy, rename, cat, etc.
 *   Note that the FileCommandAPI class is abstract and thus cannot be instantiated.  Use the
 *   static 'create' method to get an instance of a child class.
 *
 ******************************************************************************/

class FileCommandAPI {

    const TRACE_STATUS = 0;

    protected $SOURCE;
    protected $dataFile;

    public function __construct( $src = "", $dataFile = null ) {
        $this->setPath($src);

        $this->dataFile = $dataFile;
    }

    public function getDataFile() {
        return $this->dataFile;
    }

    static function trace($str)
    {
        if (self::TRACE_STATUS  <> 1)
        return;

        $dbg = fopen("trace_FileCommandAPI.log", "at");
        fprintf($dbg, "File - %s\n", $str);
        fclose($dbg);
    }

    public function isDirectory() {
        return FileCommandAPI::isDir($this->getPath());
    }

    static public function isDir($path) {
        if ( file_exists($path) && ((fileperms($path) & 0x4000) == 0x4000) )
        return true;
        return false;
        //    return is_dir($path);
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::create
     *
     * @abstract
     *   Class factory for File descendants - Deprecated at the moment, since we're
     *   only supporting POSIX filesystems for the forseeable future.
     *
     * @description
     *   This is a static method which creates an instance of the required descendant class.
     *   The desired class is indicated by the $fileSystemType parameter, either FILESYS_POSIX
     *   or FILESYS_SRB.  If an invalid type is specified an exception is thrown.
     *
     * @param String $source
     * @return FileCommandAPI
     ******************************************************************************/
    static public function create($source) {
        return new FileCommandAPI($source);
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::setPath
     *
     * @abstract
     *   Set the path for the File
     *
     * @description
     *   Sets the path for the File object.
     *
     * @return
     *   None
     *
     ******************************************************************************/
    public function setPath( $src ) {
        $source = FileCommandAPI::set_directory( $src );
        $this->SOURCE = $source;
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::getPath
     *
     * @abstract
     *   Get the path of the File
     *
     * @description
     *   Gets the path for the File object.
     *
     * @return
     *   String - path for the File
     *
     ******************************************************************************/
    public function getPath() {
        return $this->SOURCE;
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::replicate
     *
     * @abstract
     *  DEPRECATED 02/28/2006
     *
     * @description
     *   Deprecated method does nothing.
     *
     * @return
     *   Deprecated method always returns FALSE.
     *
     ******************************************************************************/
    public function replicate ( $resource ) {
        throw new Exception("FileCommandAPI::replicate is unimplemented");
        return FALSE;
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::remove
     *
     * @abstract
     *   Alias of data_delete (/data/delete)
     *
     * @description
     *   Please see FileCommandAPI::delete for details
     *
     ******************************************************************************/
    public function remove () {
        return delete ();
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::exists.
     *
     * @abstract
     *   Check to see if file exists.
     *
     * @description
     *   Checks to see if file actually exists on the file system.
     *
     * @return
     *   TRUE IFF file exists.
     *
     ******************************************************************************/

    public function exists( $test = null )  {

        if (isset($test)) {
            $test = FileCommandAPI::set_directory($test);
        }
        else {
            $test = $this->SOURCE;
        }

        return file_exists($test);
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::set_accs
     *
     * @abstract
     *   Change access permissions on File object
     *
     * @description
     *   Change access permissions on File object, if File object
     *   is a directory, permission change is assumed recursive.
     *   Examples:
     *     $file->set_accs("READ", "public")
     *     $file->set_accs("STICKY")
     *     $file->set_accs("ALL", "NEES-0")
     *     $file->set_accs("WRITE", "NEES-2005-0003")
     *
     *  Note: This only operates on group permissions. (GPC 3/9/2006)
     *
     * @return
     *   TRUE IFF permissions were changed without error
     *
     ******************************************************************************/
    public function set_accs ( $accs = "READ", $group = "" ) {

        $CMD_MAP = array(
        "READ" => "-R g+r '$group'",
        "WRITE" => "-R g+w '$group'",
        "ALL" => "-R g+a '$group'",
        "STICKY" => "-R g+s '$group'"
        );

        $command = "chmod ". $CMD_MAP["$accs"] ." '$this->SOURCE'";
        $output = `$command 2>&1`;

        if ( strlen($output) == 0) {
            return TRUE;
        }
        return FALSE;
    }


    /** ****************************************************************************
     * @title
     *   FileCommandAPI::copydir
     *
     * @abstract
     *   Copy the entire contents, subdirectories and files, of a directory to the specified target
     *
     * @description
     *   Copy the contents of a directory from one location in the hierarchy to another
     *
     * @param source
     *    The source path (string not an object)
     *
     * @param target
     *    The target path (string, not an object)
     *
     * @return
     *   TRUE IFF copy was successful
     *
     ******************************************************************************/


    private function copydir($source, $target ) {

        // First step is to make the new directory in the target path.
        // We also need to create the DataFile object for the directory.
        if (!$this->exists($target))
        if (!mkdir($target,0755,true))
        throw new FileCommandAPIError("Could not create directory $target");

        $dfObj = DataFile::newDataFileByFilesystem( basename($target), dirname($target), true );

        if ($hDir = opendir($source) ) {
            while (($file = readdir($hDir)) !== false) {
                if (($file != '.') && ($file != '..') && ($file!='.svn')) {

                    $fullPath = $source."/".$file;

                    if ($this->isDir($fullPath) ) {
                        // Recursive call... copy the subdirectory
                        $this->copydir( $fullPath, $target."/".$file );
                    }
                    else {  // This is a file
                      // Create a FileCommandAPI object so that we can use the copy method.
                      $fcTarget = FileCommandAPI::create($target);
                      $fcSourceFile = FileCommandAPI::create($fullPath);
                      $fcSourceFile->copy($fcTarget);
                    }
                }
            }

            closedir($hDir);

            return true;
        }

        return false;
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::copy
     *
     * @abstract
     *   Copy files and directories
     *
     * @description
     *   Copy files or directories from one location in the hierarchy to another
     *
     * @param Destination
     *   The DIRECTORY destination File object
     *
     * @return
     *   TRUE IFF copy was successful
     *
     ******************************************************************************/
    public function copy( FileCommandAPI $destination ) {

        $target = $destination->getPath();

        // Is this a directory?  If so use copydir()...
        if ( $this->isDir($this->SOURCE)) {
            $result = $this->copydir($this->SOURCE, $target);
        }
        else {
          $target .= "/".basename($this->SOURCE);
          if(basename($this->SOURCE) != '.svn') {
            $result = copy($this->SOURCE, $target);  // Use PHP's 'copy' function.

            // If we were successful, create the new DataFile object.
            // The method will handle the insert or update.
            if($result) {
              $dfObj = DataFile::newDataFileByFilesystem( basename($this->SOURCE),
                                                          $destination->getPath() );
            }
          }
        }

        return $result;

    }

    private function recursiveRmDir($dir) {

        $handle = opendir($dir);

        while (false!==($item = readdir($handle)))  {

            if($item != '.' && $item != '..') {

                // 	    if( is_dir($dir.'/'.$item) ) {
                if( $this->isDir($dir.'/'.$item) ) {
                    $this->recursiveRmDir($dir.'/'.$item);
                }
                else {
                    unlink($dir.'/'.$item);
                }
            }
        }
        closedir($handle);

        return rmdir($dir);
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::delete
     *
     * @abstract
     *   Delete files and directories
     *
     * @description
     *   Delete files or directories from the hierarchy, the user calling the
     *   operation must have sufficient access for this operation to be
     *   successful.
     *
     * @return
     *   TRUE IFF delete was successful
     *
     ******************************************************************************/
    public function delete () {

        // If this is a file, go ahead and delete it.
        if ( is_file($this->SOURCE) ) {
            return unlink($this->SOURCE);
        }
        else {    // Else, this is a directory. Need to perform a recursive deletion.
            $result = FileCommandAPI::recursiveRmDir($this->SOURCE);
            return $result;
        }
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::printFile
     *
     * @abstract
     *   Print a file to the output buffer
     *
     * @return
     *   TRUE IFF file was printed without errors
     *
     ******************************************************************************/
    public function printFile () {

        $mime = FileCommandAPI::getmime( $this->SOURCE );

        if ($this->exists($this->SOURCE))
        {
            // Get length of file
            $filesize = filesize($this->SOURCE);

            $handle = fopen($this->SOURCE, "r" );

            // Turn off "no cache" style headers for SSL file downloads
            // to prevent MSIE from displaying strange errors.
            // http://support.microsoft.com/default.aspx?scid=kb;en-us;316431

            header("Pragma:");
            header("Cache-Control:");
            header("Content-Type: $mime");
            header("Content-Length: $filesize");
            ob_clean();

            //flush();


            while (!feof($handle))
            {
                $buffer = fgets($handle, 4096);
                echo $buffer;
            }

            fclose($handle);

            return TRUE;
        }
        return FALSE;
    }
    /** ****************************************************************************
     * @title
     *   FileCommandAPI::printFileIESafe
     *
     * @abstract
     *   Print a file to the output buffer with special care
     *   to make it compatible with IE
     *
     *  @return
     *   TRUE IFF file was printed without errors
     ******************************************************************************/
    public function printFileIESafe() {

        if($this->exists($this->SOURCE)) {

            // Get length of file
            $filesize = filesize($this->SOURCE);

            $handle = fopen($this->SOURCE, "r" );
            header('HTTP/1.1 200 OK');
            header('Status: 200 OK');
            header('Accept-Ranges: bytes');
            header('Content-Transfer-Encoding: Binary');
            header('Content-Type: application/octet-stream');

            while (!feof($handle))
            {
                $buffer = fgets($handle, 4096);
                echo $buffer;
            }
            fclose($handle);
        }
    }

    /**
     * Helper method which will recursively create directories.  In theory, PHP5 supports
     * recursive directory creation, but under windows it doesn't work properly.
     *
     * @param string $dir
     * @param int $mode
     * @return boolean
     * @access private
     */

    private function makeDirectory($dir, $mode = 0755, $recursive=false)
    {
        if ($this->isDir($dir)) {
            return true;
        }

        if ( mkdir($dir,$mode,$recursive) ) {
            return true;
        }
        if (!$this->makeDirectory(dirname($dir), $mode, $recursive)) {
            return FALSE;
        }

        return mkdir($dir,$mode,$recursive);
    }


    /** ****************************************************************************
     * @title
     *   FileCommandAPI::mkdir
     *
     * @abstract
     *   Make a new directory
     *
     * @description
     *   Make a new directory in the hierarchy
     *
     * @return
     *   TRUE IFF directory was created without error
     *
     * @since July 12, 2010 - changed bits from 0770 to 0750 (gemezm@purdue.edu)
     *
     ******************************************************************************/
    public function mkdir ($recursive=false) {

        if(substr($this->SOURCE, 0, 11) != "/nees/home/") return;

        umask(0000);

        if ( $this->makeDirectory($this->SOURCE, 0750, $recursive) )
        {
            // This will need to be fixed. We're just trying to avoid whitescreens.
            try {
                $tmp_dirs = explode("/", $this->SOURCE);

                for( $i = 0; $i < count($tmp_dirs); $i++) {
                  if($tmp_dirs[$i] == "." || empty($tmp_dirs[$i])) continue;
                  $tmp_path = implode('/', array_slice($tmp_dirs, 0, $i + 1));

                  if(strpos(dirname($tmp_path), "/nees/home") !== 0) continue;

                  $df = DataFilePeer::insertOrUpdateIfDuplicate(
                    basename($tmp_path),        // name
                    dirname($tmp_path),         // path
                    date('Y-m-d H:i:s'),               // created
                    null,                              // checksum
                    1,                                 // Directory
                    null                               // filesize
                  ) ;
                }
            }
            catch(Exception $e) {
              return false;
            }
        }

        return true;
    }

    private function mv($src, $dest) {
        $escSrc = escapeshellarg($src);
        $escDest = escapeshellarg($dest);
        exec("cp -rp $escSrc $escDest", $output, $returncode);
        if (!$returncode) {
            exec("rm -rf $escSrc", $output, $returncode);
        } else {
            throw new Exception("Could not move $escSrc to $escDest");
        }
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::move
     *
     * @abstract
     *   Move files and directories
     *
     * @description
     *   Move files or directories from one location in the hierarchy to another
     *
     * @return
     *   TRUE IFF move operation was successful
     *
     ******************************************************************************/
    public function move( $destination ) {

        $source = $this->SOURCE;
        $target = $destination->getPath();

        $this->mv($source, $target);

        $this->setPath($target."/".basename($source));

        return TRUE;

    }


    /** ****************************************************************************
     * @title
     *   FileCommandAPI::ingest
     *
     * @abstract
     *   Upload files and directories
     *
     * @description
     *   The purpose of this service is to upload data to the NEES Central Repository.
     *
     * @return
     *   TRUE IFF file ingestion was successful
     *
     ******************************************************************************/
    public function ingest ( $tempName ) {
        if ( copy($tempName, $this->SOURCE) ) {
            if ( !chmod($this->SOURCE, 0770)) {
                return false;
            }

            return true;
        }

        return false;

        /*
         $output = shell_exec("cp $tempName \"$this->SOURCE\"");

         if (strlen($output == 0))
         {
         if (!chmod($this->SOURCE, 0770))
         return FALSE;

         return TRUE;
         }

         return FALSE;
         */
    }


    /** ****************************************************************************
     * @title
     *   FileCommandAPI::rename
     *
     * @abstract
     *   Rename a file or directory
     *
     * @description
     *   Rename a file or directory in the hierarchy
     *
     * @return
     *   TRUE IFF rename was successful
     *
     ******************************************************************************/
    public function rename ( $newname ) {

        $newDir = FileCommandAPI::create($newname);
        $newPath = $newDir->getPath();

        if(file_exists($newPath)) return false;

        $retval = rename($this->SOURCE, $newPath);
        $this->setPath($newPath);
        return $retval;
    }

    /** ****************************************************************************
     * @title
     *   FileCommandAPI::ls
     *
     * @abstract
     *   List the contents of a directory
     *
     * @description
     *   The purpose of this service is to list the contents of a directory
     *
     * @return
     *   Returns the directory contents
     *
     ******************************************************************************/
    public function ls($recursive = false) {

        $results = array();

        if(!is_dir($this->SOURCE)) return null;

        $datafiles = DataFilePeer::findDataFileBrowserRS($this->SOURCE);
        $datafiles->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while($datafiles->next()) {
          //name, directory, created, filesize
          $df_name = $datafiles->get('NAME');
          $df_directory = $datafiles->get('DIRECTORY');
          $df_created = $datafiles->get('CREATED');
          $df_filesize = $datafiles->get('FILESIZE');

          $df_fullpath = $this->SOURCE . "/" . $df_name;

          if(!file_exists($df_fullpath)) continue;

          if($df_directory) {
            if(!$recursive) {
              $results[$df_name] = "directory";
            }
            else {
              $subdirSrc = FileCommandAPI::create($df_fullpath);
              $results[$df_name] = $subdirSrc->ls(true);
            }
          }
          else {
            #$entryName = $df_name . "\t" . $df_created ."\t" . $df_filesize;
            $entryName = $df_name;
            $results[$entryName] = "file";
          }
        }
/*

        if ($handle = opendir($this->SOURCE)) {
            while (false !== ($file = readdir($handle))) {
                $fullPath = $this->SOURCE."/".$file;

                // Ignore . and .. entries
                if ($file == '.' || $file == '..')
                continue;
                if ($this->isDir($fullPath)) {
                  if(!$recursive) {
                    $results[$file] = "directory";
                  } else {
                    $subdirSrc = FileCommandAPI::create($fullPath);
                    $results[$file] = $subdirSrc->ls(true);
                  }
                }
                else
                {
                    $entryName = $file."\t".date("Y-m-d-H.i", filemtime($fullPath))."\t".filesize($fullPath);

                    $results[$entryName] = "file";
                }

            }
            closedir($handle);
        }
*/
        return $results;
    }


    /** ****************************************************************************
     * @title
     *   FileCommandAPI::search
     *
     * @abstract
     *   Search data repository
     *
     * @description
     *   Search all data and metadata in the repository
     *   <pre>
     *   &lt;?php
     *     data_search ( "is seismos a goat" );
     *   ?&gt;</pre>
     *
     * @param GAsession
     *   (GET/POST/COOKIE): The session identifier needed for authentication
     *   and authorization, and is required for all non-public files in the repository
     *
     * @param Query
     *   (GET/POST): The query string
     *
     * @return
     *   Returns the directory listing for the specified source location... TBD
     *
     ******************************************************************************/
    public function search ( $query = "" ) {
    }

    /**
     * @title
     *   FileCommandAPI::set_directory
     *
     * @abstract
     *   mangle directory names for SRB
     *
     * @description
     *
     * This used to be in lib/interface/helpers.inc
     *
     **/
    static public function set_directory( $relpath ) {
        $rootdir = "/nees/home"; // Assuming we're in /nees/home, but we might not be.

        $source = ltrim ($relpath, "/");
        $dirs = preg_split('/\//', $source, 0, PREG_SPLIT_NO_EMPTY);

        if( $dirs[0] == "nees" && $dirs[1] == "home" ) {
          $rootdir = "";
          if( (strpos($dirs[2], ".groups") === false) &&
              isset( $dirs[2] ) &&
              (strpos($dirs[2], ".nees") === false) )
          {
              $dirs[2] .= ".groups"; // Appending .groups if needed
          }

        } elseif($dirs[0] == "opt" && $dirs[1] == "central"){
          // We're not at /nees/home - we're in /opt/central
          $rootdir = '';
        } else {
          if( (strpos($dirs[0], ".groups") === false) &&
              isset( $dirs[0] ) &&
              (strpos($dirs[0], ".nees") === false) )
          {
              $dirs[0] .= ".groups"; // Appending .groups if needed
          }
        }

        $dir="";

        foreach( $dirs as $key => $d ) {
            $dir .= "/".$d;
        }

        $dir = $rootdir.$dir;

        return $dir;
    }


    private static function getmime( $ext = "unknown" ) {
        $mime = array(
        "avi"   => "video/avi",
        "bmp"   => "image/bmp",
        "css"   => "text/css",
        "doc"   => "application/doc",
        "gif"   => "image/gif",
        "gz"    => "application/x-gzip",
        "html"  => "text/html",
        "htm"   => "text/html",
        "jar"   => "application/x-jar",
        "jnlp"  => "application/x-java-jnlp-file",
        "jpeg"  => "image/jpeg",
        "jpg"   => "image/jpeg",
        "midi"  => "audio/midi",
        "mov"   => "video/quicktime",
        "mpeg"  => "video/mpeg",
        "mpg"   => "video/mpeg",
        "pdf"   => "application/pdf",
        "png"   => "image/png",
        "ppt"   => "application/vnd.ms-powerpoint",
        "ps"    => "application/postscript",
        "qt"    => "video/quicktime",
        "rm"    => "application/vnd.rn-realmedia",
        "shtml" => "text/html",
        "tif"   => "image/tiff",
        "txt"   => "text/plain",
        "wav"   => "audio/wav",
        "xls"   => "application/vnd-ms-excel",
        "xml"   => "application/xml",
        "z"     => "application/x-compressed",
        "zip"   => "application/x-zip-compressed"
  );
        $pathinfo = pathinfo( $ext );
        $extension = strtolower( $pathinfo['extension'] );
        if (isset($mime[ $extension ])) {
            return $mime[$extension];
        } else {
            return "nees/data";
        }
    }

    /**
     *
     * @param string $p_strProjectName
     * @return string
     */
    public static function getProjectDirectoryGroup($p_strProjectName){
      $strGroupCn = str_replace("-",  "_",  $p_strProjectName);
      return strtolower(trim($strGroupCn));
    }
} //end class
?>
