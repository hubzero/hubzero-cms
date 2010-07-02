<?php

/** ****************************************************************************
 * @title
 *   Data Web Services
 *
 * @author
 *   Daniel Frysinger, Tim Warnock, Greg Crawford
 **
 * @abstract
 *   Web Services for the NEES central data archive
 *
 * @description
 *   These functions allow you to manage the central data repository.  The
 *   repository is represented as a single logical file hierarchy with
 *   metadata associated with files and directories.
 *
 ******************************************************************************/
require_once "lib/interface/helpers.inc";
require_once "lib/filesystem/FileCommandAPI.php";
require_once "lib/data/DataFile.php";


/** ****************************************************************************
 * @title
 *   /data/get
 *
 * @abstract
 *   Retrieve a file from the archive
 *
 * @description
 *   The purpose of this service is to retrieve data files from the NEES Central
 *   Repository. In practice it emulates the exact behavior of an HTTPS download
 *   of a file from the web server; however, this service can access any file
 *   in the distributed NEES Central Repository.
 *   <pre>
 *   Service call:
 *   https://central.nees.org/data/get/home/image/hello.jpg
 *
 *   Functional call:
 *   &lt;?php
 *     data_get ( "/home/image/hello.jpg" );
 *   ?&gt;</pre>
 *
 * @param GAsession
 *   (GET/POST/COOKIE): The session identifier needed for authentication
 *   and authorization, and is required for this service.
 *
 * @param File
 *   (GET/POST/PATH_INFO): The absolute path of the file that should be returned.
 *
 * @param Mode
 *   (GET/POST): Optional calling mode, possible values include BROWSER or API.
 *   Both modes will retrieve the file, but in BROWSER mode errors will be
 *   handled by redirecting to NEEScentral, otherwise in API mode errors
 *   will be handled by an XML result.  The default mode is BROWSER.
 *
 * @return
 *   The service handler will return the return status of the copy operation,
 *   that is, for a successful copy a single "0" will be expected with no error
 *   ouput.
 *
 ******************************************************************************/
function data_get ( $src = "" ) {

  ## check if functional or service
  $FUNCT = 0;
  if (strlen($src) > 0) {
    $FUNCT = 1;
  }

  $src = set_request("File", $src);

  $src = set_from_path( $src );

  set_time_limit(21600);

  ## verify dirname
  if (! isset($src) ) {
    return error("no filename", $FUNCT);
  }

  $path = FileCommandAPI::set_directory($src);

  // Datafile is not found on disk
  if(! file_exists($path)) {
    return error("Data file not found", $FUNCT);
  }

  $file = DataFilePeer::findByFullPath($path);

  // Datafile is not found on database
  if(is_null($file)) {
    return error("Data file not found", $FUNCT);
  }

  $canAccessFile = false;

  if($file->getView() == "PUBLIC") {
    $canAccessFile = true;
  }
  elseif(preg_match("/\/nees\/home\/(facility.groups|Facilities.groups)/", $path)){
    $canAccessFile = true;
  }
  elseif($file->isInPublicDir()) {
    $canAccessFile = true;
  }
  else {
    $entity = $file->getOwner($path);
    if($entity) {
      if($entity->isPublished()) {
        $canAccessFile = true;
      }
      else {
        $userManager = UserManager::getInstance();

        if ($userManager->isMember($entity)) {
          $canAccessFile = true;
        }
      }
    }
  }

  // See if they can view the page
  if($canAccessFile) {
    // print file if it exists
    $file = FileCommandAPI::create($src);
    $status = $file->printFile();

    // Something bad happened when we tried to print the file
    if(!$status) {
      if ( $_REQUEST["Mode"] == "API" ) {
        return error("cannot get the file", $FUNCT);
      }
      else {
        header("Location: /datafile_error.php?file=$src&accsdata={$_SERVER['PATH_INFO']}");
      }
    }
  }
  else {
    //  Not allowed to view the file - display an error message for API or for gui
    if ( $_REQUEST["Mode"] == "API" ) {
      return error("You do not have permission to access the file", $FUNCT);
    }
    else {
      header("Location: /error.php?accsdata={$_SERVER['PATH_INFO']}");
    }
  }

  exit;
}



/**
 * Check if an Project or Experiment is Published or not
 *
 * @param $entity
 * @return boolean yes if is published, else if not
 */
function isPublished($entity){
  if (!$entity) {
    return false;
  }
  $entityId = $entity->getId();
  $entityTypeId = DomainEntityType::getEntityTypeId($entity);

  $published = false;
  if($entityTypeId == 1 || $entityTypeId == 3){
    $published = $entity->getView() == "PUBLIC";
  }
  return $published;
}


/** ****************************************************************************
 * @title
 *   /data/view
 *
 * @abstract
 *   Alias of data_get (/data/get)
 *
 * @description
 *   The purpose of this service is to retrieve data files from the NEES Central
 *   Repository. In practice it emulates the exact behavior of an HTTPS download
 *   of a file from the web server; however, this service can access any file
 *   in the distributed NEES Central Repository.
 *   <pre>
 *   Service call:
 *   https://central.nees.org/data/view/home/image/hello.jpg
 *
 *   Functional call:
 *   &lt;?php
 *     data_view ( "/home/image/hello.jpg" );
 *   ?&gt;</pre>
 *
 *   Please see data_get for details
 *
 ******************************************************************************/
function data_view ( $file = "" ) {
  return data_get( $file );
}



/** ****************************************************************************
 * @title
 *   /data/put
 *
 * @abstract
 *   Upload files and directories
 *
 * @description
 *   The purpose of this service is to upload data to the NEES Central Repository.
 *   In practice it emulates the exact behavior of an HTTPS POST file upload to
 *   a web server; however, this service can upload multiple files and directories
 *   into the distributed NEES Central Repository.
 *   <br /><br />
 *   The following HTML is for example purposes to demonstrate the usage of
 *   /data/put to upload over standard HTTP POST.
 *   <pre>
 *
 *   &lt;form enctype="multipart/form-data"
 *     action="https://hostname/data/put/?GAsession=id&base=/NEES-0/Documentation"
 *     method="post"&gt;
 *
 *   Select file to upload
 *   &lt;input type="file" name="File0" size="20"/&gt;
 *
 *   Select another file to upload
 *   &lt;input type="file" name="File1" size="20"/&gt;
 *   &lt;input type="hidden" name="Dir0"/&gt;
 *
 *   &lt;input type="submit" value="Send File(s)"/&gt;
 *
 *   &lt;/form&gt;
 *   </pre>
 *
 *
 *
 * @param GAsession
 *   (GET/POST/COOKIE): The session identifier needed for authentication
 *   and authorization, and is required for this service.
 *
 * @param File0..N
 *   (POST): The absolute path of the local file, multiple files can be
 *   specified for upload but must be named starting with File0 and
 *   incrementing to File1, File2, etc.
 *
 * @param Dir0..N
 *   (GET/POST): The relative path that should be created in the destination
 *   directory of the file hierarchy, for every file uploaded a corresponding
 *   relative path should be specified.  That is, File0 should have a corresponding
 *   Dir0, File1 should have a corresponding Dir1, etc.
 *
 * @param Destination
 *   (GET/POST): The absolute path for the target destination of the upload
 *   operation.
 *
 * @return
 *   The service handler will return the return status of the upload operation in XML.
 *
 *   For a successful upload the following XML will be returned
 *   <pre>
 *    &lt;NEES&gt;
 *     &lt;NEESTime&gt;Time&lt;/NEESTIME&gt;
 *     &lt;Output name="Upload Success"&gt;
 *          &lt;key name="path to uploaded file"&gt;0&lt;/key&gt;
 *          &lt;key name="Files Uploaded"&gt;Number of Files&lt;key/&gt;
 *     &lt;/Output&gt;
 *    &lt;/NEES&gt;
 *
 *   </pre>
 *    On failure, the following XML will be returned
 *   <pre>
 *    &lt;NEES&gt;
 *     &lt;NEESTime&gt;Time&lt;/NEESTIME&gt;
 *     &lt;Errors&gt;
 *       	&lt;Error&gt;Error Message&lt;/Error&gt;
 *     &lt;/Errors&gt;
 *    &lt;/NEES&gt;
 *  </pre>
 *
 ******************************************************************************/
function data_put () {

  global $ini_array;

  ## verify login
  $authenticator = Authenticator::getInstance();
  if (! $authenticator->isLoggedIn() ) {
    return error("Sorry, your login has expired.", FALSE);
  }

  ## bail if the base directory is not set
  if (! (isset($_REQUEST['base']) || isset($_REQUEST['Destination'])) ) {
    return error("Unknown upload destination", FALSE);
  }

  $base = array_key_exists("base",$_REQUEST)?$_REQUEST["base"]:$_REQUEST["Destination"];

  $base_obj = FileCommandAPI::create($base);

  $base = $base_obj->getPath();

  // Update new feature, create full directory if it not yet existed
  $base_obj->mkdir(true);


  ## Upload all files

  $filecount = 0;

  foreach($_FILES as $tagname=>$file) {

    $tempName = $file['tmp_name'];
    $realName = urldecode($file['name']);

    // if no file, skip it
    if ( strlen($realName) <= 0 ) continue;

    ## get the relative path
    sscanf($tagname,"File%d",$dir_key);
    $dir_key = "Dir" . $dir_key;
    $relPathName = urldecode($_REQUEST[$dir_key]);

    ## seperate path and name
    preg_match( '/^(.*)\/[^\/]+$/', $relPathName, $matches);
    $path_request = dirname( $relPathName );

    if($path_request == ".") {
      $stripped_filename = stripslashes( "$base/$realName" );
    }
    else {
      $stripped_filename = stripslashes( "$base/$path_request/$realName" );
    }

    $stripped_filename = preg_replace('/[`]/', '', $stripped_filename);

    ##########################################################################
    ## upload $tempName to $srbobject preserving existing $srbpath
    ##########################################################################
    $time_start = time();
    ##
    # If we're uploading a dir tree, make sure to create all subdirs.
    $tmp_dirs = preg_split('/\//', $path_request);
    for( $i = 0; $i < count($tmp_dirs); $i++) {
      if($tmp_dirs[$i] == "." || empty($tmp_dirs[$i])) continue;
      $tmp_fullpath = $base;
      $tmp_path = join('/', array_slice($tmp_dirs, 0, $i));
      if( $tmp_path ) {
        $tmp_fullpath .= "/$tmp_path";
      }
      // tmp_basepath is just for 'new DataFile'
      $tmp_basepath = $tmp_fullpath;
      // tmp_fullpath is used for file operations.
      $tmp_fullpath .= "/" . $tmp_dirs[$i];

      // $_EXISTS is just to cache results so we don't
      // keep asking the filesystem the same question.
      if ( !isset($_EXISTS[$tmp_fullpath]) ) {
        $path_obj = FileCommandAPI::create($tmp_fullpath);

        if ( !$path_obj->exists( $tmp_fullpath ) ) {
          $path_obj->mkdir();
        }
/*
        DataFilePeer::insertOrUpdateIfDuplicate(
        $tmp_dirs[$i],        // name
        $tmp_basepath,        // path
        date('Y-m-d H:i:s'),  // created
        null,                 // checksum
        1,                    // Directory
        null                  // filesize
        );
*/

        $_EXISTS[$tmp_fullpath] = 1;
      }
    }

    ## Upload if $abs_path $_EXISTS
    //$newfile = FileCommandAPI::create($stripped_filename);
    $status = move_uploaded_file($tempName,$stripped_filename);

    if ($status === true) {
      $filecount++;
      $path = preg_replace('/\/\.$/', '', dirname($stripped_filename));
      $name = basename($stripped_filename);

      DataFilePeer::insertOrUpdateIfDuplicate(
      $name,                         // name
      $path,                         // path
      date('Y-m-d H:i:s'),           // created
      md5_file($stripped_filename),  // checksum
      0,                             // directory
      filesize($stripped_filename)   // filesize
      );


      ## Begin - TSUNAMI REPOSITORY MOD
      if (isset($_REQUEST['tsunami']))
      {
        require_once "lib/interface/Tsunami.php";
        processTsunamiFile(basename($stripped_filename),dirname($stripped_filename));
      }
      ## End   - TSUNAMI REPOSITORY MOD
    }

    $time_end = time();
    $myout["$stripped_filename"] = $time_end - $time_start;
  }



  if ($filecount == 0) {
    return error("No files to upload", FALSE);
  }

  $myout["Files Uploaded"] = $filecount;
  return output("Upload Success", $myout, "XML");
}



/** ****************************************************************************
 * @title
 *   /data/list
 *
 * @abstract
 *   List the contents of a directory
 *
 * @description
 *   The purpose of this service is to list the contents of a directory
 *   in the NEES Central Repository.
 *   <pre>
 *   Service Call:
 *   https://central.nees.org/data/list/NEES-0/Experiment-1?Return=XML
 *
 *   Functional:
 *   &lt;?php
 *     data_list ( "/home/source" );
 *   ?&gt;</pre>
 *
 * @param GAsession
 *   (GET/POST/COOKIE): The session identifier needed for authentication
 *   and authorization, and is required for this service.
 *
 * @param Source
 *   (GET/POST/PATH_INFO): The absolute path of the directory in the central
 *   repository that should be listed.
 *
 * @param Return
 *   (GET/POST): Format of results, either XML or TEXT
 *                         (default is XML)
 *
 * @return
 *   Returns the directory listing for the specified source location... TBD
 *
 ******************************************************************************/
function data_list($src = "") {
  ## check if functional or service
  $FUNCT = 0;
  if (isset($src) && strlen($src) > 0) {
    $FUNCT = 1;
  }

  $src = set_request("File", $src);
  $src = set_from_path( $src );

  ## verify dirname
  if (! isset($src) ) {
    return error("no filename", $FUNCT);
  }

  $path = FileCommandAPI::set_directory($src);

  ## set return format
  $return = "XML";
  if ( isset($_REQUEST['Return']) ) {
    $return = $_REQUEST['Return'];
  }
  if (strlen($return) > 255) {
    return error("buffer overflow detected, bailing out", $FUNCT);
  }

  // Datafile is not found on disk
  if(! file_exists($path)) {
    return error("Data file not found", $FUNCT);
  }

  // Datafile is not a directory
  if(! is_dir($path)) {
    return error("Data file is not a directory", $FUNCT);
  }

  $file = DataFilePeer::findByFullPath($path);

  // Datafile is not found on database
  if(is_null($file)) {
    return error("Data file not found on database", $FUNCT);
  }

  $canAccessFile = false;

  if($file->isInPublicDir()) {
    $canAccessFile = true;
  }
  else {
    $entity = $file->getOwner($path);
    if($entity) {
      if($entity->isPublished()) {
        $canAccessFile = true;
      }
      else {
        $userManager = UserManager::getInstance();

        if ($userManager->isMember($entity)) {
          $canAccessFile = true;
        }
      }
    }
  }

  // See if they can view directory
  if($canAccessFile) {
    // print file if it exists
    $dir = FileCommandAPI::create($src);
    $results = $dir->ls();

    if (is_null($results)) {
      return error("invalid directory $path", $FUNCT);
    }

    if ($FUNCT) {
      return $results;
    }
    else {
      return output("$path", $results, $return);
    }
  }
  else {
    return error("You don't have permission to list the content of this directory $path", $FUNCT);
  }
}


function meta_get ( $src = "" ) {

  ## check if functional or service
  $FUNCT = 0;
  if (isset($source) && strlen($src) > 0) {
    $FUNCT = 1;
  }

  $src = set_request("File", $src);
  $src = set_from_path( $src );

  ## verify name
  if (! isset($src) ) {
    return error("no filename", $FUNCT);
  }

  $path = FileCommandAPI::set_directory($src);

    // Datafile is not found on disk
  if(! file_exists($path)) {
    return error("Data file not found", $FUNCT);
  }

  $datafile = DataFilePeer::findByFullPath($path);

  if( !$datafile ) {
    return error("Data file not found on database", $FUNCT);
  }

  $res = array();
  $ok_metas = array('Authors', 'AuthorEmails', 'Description', 'HowToCite', 'Title');

  foreach( $ok_metas as $key ) {
    $get = 'get' . $key;
    $res[$key] = $datafile->$get();
  }
  if( $datafile->getDirectory() ) {
    $res['directory'] = 1;
  }
  else {
    $res['file'] = 1;
  }

  if ($FUNCT) {
    return $res;
  }
  else {
    $return = "XML";
    if ( $_REQUEST['Return'] ) {
      $return = $_REQUEST['Return'];
    }
    return output($source, $res, $return);
  }
}


/**
 * Function to handle copy data files from a directory to another directory
 *
 * @param String $oldPath: must be a directory
 * @param String $newPath: must be a directory
 * @return boolean: false if failed.
 */
function handleCopyDataFiles($oldPath, $newPath) {

  if( ! file_exists($oldPath) || ! is_dir($oldPath)) return false;
  if( file_exists($newPath) && ! is_dir($newPath)) return false;

  if( !file_exists($newPath)) {
    $newDir_obj = FileCommandAPI::create($newPath);
    $newDir_obj->mkdir(true);
  }

  if(is_empty_folder($oldPath)) return false;

  $escSrc = escapeshellarg($oldPath) . "/*";
  $escDest = escapeshellarg($newPath);

  try {
    exec("cp -rp $escSrc $escDest", $output, $returncode);

    if (!$returncode) {
      $sql =
       "INSERT INTO DATA_FILE (
          ID, AUTHOR_EMAILS, AUTHORS, CHECKSUM, CREATED, CURATION_STATUS, DELETED, DESCRIPTION, DIRECTORY, FILESIZE, HOW_TO_CITE, NAME, PAGE_COUNT, PATH, TITLE, VIEWABLE, THUMB_ID, MIME_TYPE)
        SELECT
          DATA_FILE_SEQ.NEXTVAL, AUTHOR_EMAILS, AUTHORS, CHECKSUM, CREATED, CURATION_STATUS, DELETED, DESCRIPTION, DIRECTORY, FILESIZE, HOW_TO_CITE, NAME, PAGE_COUNT, REPLACE(path, ?, ?), TITLE, VIEWABLE, THUMB_ID, MIME_TYPE
        FROM
          DATA_FILE
        WHERE
          (PATH = ? OR PATH LIKE ?) AND DELETED = 0";

      $conn = Propel::getConnection();
      $stmt = $conn->prepareStatement($sql);
      $stmt->setString(1, $oldPath);
      $stmt->setString(2, $newPath);
      $stmt->setString(3, $oldPath);
      $stmt->setString(4, $oldPath . '/%');
      $stmt->executeUpdate();
    }
  }
  catch (Exception $e) {
    // Do some thing ???
    return false;
  }
}


/**
 * Check if a folder is empty or not
 *
 * @param String $folder
 * @return boolean
 */
function is_empty_folder($folder) {

  if (! is_dir($folder)) return false; // not a dir
  $files = opendir($folder);

  while ($file = readdir($files)) {
    if ($file != '.' && $file != '..') {
      return false; // not empty
    }
  }

  return true;
}



//**********************************************************************************************
/**
 * @desc
 *   Convert a friendly path in UI (i.e. /NEES-2006-0225/Experiment-1 )
 *   to a system path (i.e. /nees/home/NEES-2006-0225.groups/Experiment-1 )
 *
 * @param $relpath: the friendly path to be converted
 *
 * @return
 *   the system path after convertion
 *
 */
//**********************************************************************************************
function get_systemPath($relpath)
{
  $rootdir = "/nees/home";
  $source = ltrim ($relpath, "/");
  $dirs = preg_split('/\//', $source, 0, PREG_SPLIT_NO_EMPTY);

  ## Verify .groups or .nees as needed
  if($dirs[0] == "nees" && $dirs[1] == "home"){
    $rootdir = "";
    $key = 2;
  } else {
    $key = 0;
  }
  if((strpos($dirs[$key], ".groups") === false) && isset( $dirs[$key] ) && (strpos($dirs[$key], ".nees") === false)) {
    $dirs[$key] = $dirs[$key].".groups";
  }

  $dir="";
  foreach( $dirs as $key => $d ) {
    $dir .= "/".$d;
  }
  $dir = $rootdir.$dir;
  return $dir;
}


//**********************************************************************************************
/**
 * @desc
 *   Convert a system path (i.e. /nees/home/NEES-2006-0225.groups/Experiment-1 )
 *   to a friendly path in UI (i.e. /NEES-2006-0225/Experiment-1 )
 *
 * @param $apath: the system path to be converted
 *
 * @return
 *   the friendly path after convertion
 */
function get_friendlyPath($apath) {

  if(strpos($apath, "/nees/home/") == 0)
  {
    return preg_replace("/.groups/","",substr($apath, 10), 1);
  }
  else {
    return $apath;
  }
}


/**
 * Get mime icon from file name
 *
 * @param String $file_name
 * @param boolean $isdir
 * @return String html
 */
function get_mimeIcon($file_name, $isdir = false) {

  $document_root = $_SERVER['DOCUMENT_ROOT'];
  if($isdir) return "/images/icons/folder.gif";

  $mime_icon = "/images/icons/file.gif";
  $pathinfo = pathinfo ( $file_name );
  $extension = isset($pathinfo['extension']) ? strtolower( $pathinfo['extension'] ) : "";

  if ($extension != "" && file_exists("$document_root/images/icons/$extension.gif")) {
    $mime_icon = "/images/icons/$extension.gif";
  }
  return $mime_icon;
}

?>
