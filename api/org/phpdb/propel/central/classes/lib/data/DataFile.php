<?php

require_once 'lib/data/om/BaseDataFile.php';
require_once 'lib/data/FacilityPeer.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/Experiment.php';
require_once 'lib/filesystem/FileCommandAPI.php';
require_once "lib/interface/Data.php";
require_once 'lib/common/ImageThumbnail.php';
require_once 'util/PhotoHelper.php';

/**
 * DataFile
 *
 *  encapsulates the notion of a file.
 *
 * @package    lib.data
 */
class DataFile extends BaseDataFile {

  public static $ownerList = array();

  /**
   * Initializes internal state of DataFile object.
   */
  function __construct($name = '',
                       $path = '',
                       $created = 0,
                       $isDirectory = 0,
                       $isDeleted = 0,
                       $view = 'MEMBERS',
                       $curation_status = 'Uncurated',
                       $authors = null,
                       $authorEmails = null,
                       $description = null,
                       $howToCite = null,
                       $title = null,
                       $pageCount = null,
                       $checksum = null,
                       $filesize = null,
                       $thumbId = null,
                       $mimeType = null) {

    $this->setName($name);
    $this->setPath($path);
    $this->setCreated($created ? $created : date('Y-m-d H:i:s'));
    $this->setDirectory($isDirectory);
    $this->setDeleted($isDeleted);
    $this->setView($view);
    $this->setCurationStatus($curation_status);
    $this->setAuthors($authors);
    $this->setAuthorEmails($authorEmails);
    $this->setDescription($description);
    $this->setHowToCite($howToCite);
    $this->setTitle($title);
    $this->setPageCount($pageCount);
    $this->setChecksum($checksum);
    $this->setFilesize($filesize);
    $this->setThumbId($thumbId);
//    $this->setMimeType($mimeType);
  }


  /**
   * Check if the current datafile can be viewable or not
   *
   * @todo Not sure this correct! Do we need to check if the $owner->isVisibleToCurrentUser()
   * @return boolean
   */
  public function isVisibleToCurrentUser() {
    if( ! $this->existsInFilesystem()) return false;

    $owner = $this->getOwner($this->getFullPath(), true);
    return (is_null($owner)) ? false : true;
  }


  /**
   * Handle delete datafile in both places: disk and database
   * overwrite the parent::delete(), to handle the delete file system as well
   */
  public function fullDeleteSingleFile() {
    // If this is a directory, return now !
    if($this->getDirectory()) return;

    $filePath = $this->getFullPath();

    // First, delete it from database
    $this->delete();

    // Then, delete it from file system if existed.
    if(file_exists($filePath)) {
      unlink($filePath);
    }
  }


  /**
   * Pick the facility name out of the path.
   *
   * @param string $path
   * @return string $facility_name
   */

  function getFacilityName($path) {

    if (preg_match("/\/nees\/home\/(f|F)acilit(y|ies).groups\/([^\/]*)/", $path, $matches)) {
      return $matches[3];
    }

    return "";
  }


  // $path = "/nees/home/NEES-2006-0225.groups/Experiment-1/Trial-2/Rep-1"

  function getReturnPath() {
    //Default return path should be index page
    $indexPage = "/";

    $path = $this->getPath();
    $name = $this->getName();

    // Illegal path for a data file. A path must be started with '/nees/home/'
    if(substr($path, 0, 11) != '/nees/home/') return $indexPage;

    // Take out /nees/home from the path
    $path = substr($path,11);

    $pathArr = explode("/", $path);

    if( ! isset($pathArr[0])) return $indexPage;

    // $pathArr[0] must be a Project group or Facility group or Member group
    // so it must be ended with '.groups'
    if(substr($pathArr[0], -7, 7) != ".groups") return $indexPage;


    # Member image thumbnail, we do not have any page associated with this datafile, return index page
    if($pathArr[0] == "member.groups") {
      return $indexPage;
    }

    # Facility DataFiles
    elseif($pathArr[0] == "facility.groups") {
      // No file allowed to be at this level, return the Facilities main page
      if( ! isset($pathArr[1])) {
        return "/?action=DisplayFacilities";
      }
      else {
        // More here for Facility
      }
    }

    # Project dataFiles
    else {
      $projectName = substr($pathArr[0], 0, -7);
      $proj = ProjectPeer::findByName($projectName);

      if(is_null($proj)) return $indexPage;

      if( ! isset($pathArr[1])) {
        return "/?projid={$proj->getId()}&action=DisplayProjectMain";
      }
      else {
        $path1 = $pathArr[1];

        if($path1 == "Analysis" || $path1 == "Documentation" || $path1 == "Public") {
          return "/?projid={$proj->getId()}&action=DisplayProject$path1&basepath=/$projectName/$path1&path=" . substr($path, strlen("$projectName.groups/$path1")) . "&file=$name&floc=ViewFileDetails";
        }
        elseif(substr($path1, 0, 11) == "Experiment-" || substr($path1, 0, 11) == "Simulation-") {
          $expName = $path1;
          $expType = substr($path1, 0, 11) == "Experiment-" ? "Experiment" : "Simulation";

          $exp = ExperimentPeer::findByNameProject($expName, $proj->getId());
          if(is_null($exp)) return "/?projid={$proj->getId()}&action=DisplayProjectMain";

          if( ! isset($pathArr[2])) {
            return "/?projid={$proj->getId()}&expid={$exp->getId()}&action=Display{$expType}Main";
          }
          else {
            $path2 = $pathArr[2];
            $query = "/?projid={$proj->getId()}&expid={$exp->getId()}&basepath=/$projectName/$expName/$path2&path=" . substr($path, strlen("$projectName.groups/$path1/$path2")) . "&file=$name&floc=ViewFileDetails";

            if($path2 == "Analysis" || $path2 == "Documentation" || $path2 == "Setup") {
              return "$query&action=Display{$expType}{$path2}";
            }
            elseif($path2 == "N3DV") {
              return "$query&action=DisplayDataViewers";
            }
            elseif($path2 == "Models" || $path2 == "DAQChannels" || $path2 == "CoordinateSpaces") {
              return "$query&action=DisplayExperimentSetup";
            }
            elseif(substr($path2, 0, 6) == "Trial-" || substr($path2, 0, 4) == "Run-") {
              $trialName = $path2;
              $trialType = substr($path2, 0, 6) == "Trial-" ? "Trial" : "SimulationRun";

              $trial = TrialPeer::findByNameExperiment($trialName, $exp->getId());
              if(is_null($trial)) return "/?projid={$proj->getId()}&expid={$exp->getId()}&action=Display{$expType}Main";

              if( ! isset($pathArr[3])) {
                return "/?projid={$proj->getId()}&expid={$exp->getId()}&trialid={$trial->getId()}&action=Display{$trialType}Main";
              }
              else {
                $trial_query = "/?projid={$proj->getId()}&expid={$exp->getId()}&trialid={$trial->getId()}";

                if($trialType == "Trial") {
                  if(substr($pathArr[3], 0, 4) == "Rep-") {
                    $repName = $pathArr[3];
                    $rep = RepetitionPeer::findByNameTrial($repName, $trial->getId());
                    if(is_null($rep)) return "$trial_query&action=DisplayTrialMain";
                    return "$trial_query&Rep=$repName&action=DisplayTrialData";
                  }
                  elseif($pathArr[3] == "Analysis" || $pathArr[3] == "Documentation") {
                    $path3 = $pathArr[3];
                    return "$trial_query&action=DisplayTrial{$path3}&basepath=/$projectName/$expName/$trialName/$path3&path=" . substr($path, strlen("$projectName.groups/$expName/$trialName/$path3")) . "&file=$name&floc=ViewFileDetails";
                  }
                }
                else {

                }
                return "$trial_query&action=Display{$trialType}Main";
              }
            }

          }
        }

        //else exit("<br/>path1 = " . substr($path1, 0, 11));

        else return $indexPage;
      }
    }

    return $indexPage;
  }

  /**
   * The Project or Experiment or Facility that datafile $path belong to.
   * This is now check for the Experiment level access control.
   * If the entity was found but was marked with deleted, then it return null
   *
   * @param string $path
   * @return $entity Object, that either Project or Experiment or Facility or null
   */
  function getOwner($path = "", $checkVisible = false) {

    if(empty($path)) {
      $path = $this->getPath();
    }

    // Illegal path for a data file. A path must be started with '/nees/home/'
    if(substr($path, 0, 11) != '/nees/home/') return null;

    // Take out /nees/home from the path
    $path = substr($path,11);

    $pathArr = explode("/", $path);

    if( ! isset($pathArr[0])) return null;

    // $pathArr[0] must be a Project group or Facility group or Member group
    // so it must be ended with '.groups'
    if(substr($pathArr[0], -7, 7) != ".groups") return null;

    $pathEntity = ( ! isset($pathArr[1])) ? $pathArr[0] : $pathArr[0] . "/" . $pathArr[1];

    # Member image thumbnail, check thumbnail table to get owner entity
    if($pathArr[0] == "member.groups") return null;

    # Facility DataFiles
    elseif($pathArr[0] == "facility.groups") {
      // No file allowed to be at this Facility.group level
      if( ! isset($pathArr[1])) {
        return null;
      }
      else {
        $facShortName = $pathArr[1];

        if(array_key_exists($pathEntity, self::$ownerList)) {
          return self::$ownerList[$pathEntity];
        }

        $facility = FacilityPeer::find($facShortName);

        // Couldn't find the Facility by this short name
        if(is_null($facility)) {
          self::$ownerList[$pathEntity] = null;
          return null;
        }
        else {
          self::$ownerList[$pathEntity] = $facility;
          return $facility;
        }
      }
    }

    // It must be a group of Project
    $projGroup = $pathArr[0];

    $projName = substr($projGroup, 0, -7);

    if(array_key_exists($pathEntity, self::$ownerList)) {
      return self::$ownerList[$pathEntity];
    }

    $project = ProjectPeer::find($projName);

    if(is_null($project)) {
      self::$ownerList[$pathEntity] = null;
      return null;
    }
    // Experiment | Simulation Entity
    if(isset($pathArr[1]) && ((substr($pathArr[1], 0, 11) == "Experiment-") || (substr($pathArr[1], 0, 11) == "Simulation-"))) {
      $expName = $pathArr[1];

      if(array_key_exists($pathEntity, self::$ownerList)) {
        return self::$ownerList[$pathEntity];
      }
      $exp = ExperimentPeer::findByNameProject($expName, $project->getId());

      if(is_null($exp)) {
        self::$ownerList[$pathEntity] = null;
        return null;
      }

      if($checkVisible) {
        if( ! $exp->isVisibleToCurrentUser()) {
          self::$ownerList[$pathEntity] = null;
          return null;
        }
      }
      // This is Experiment owner, save path and return Experiment
      self::$ownerList[$pathEntity] = $exp;
      return $exp;
    }
    // Experiment does not exist -> the owner is the Project owner
    if($checkVisible) {
      if( ! $project->isVisibleToCurrentUser()) {
        self::$ownerList[$pathEntity] = null;
        return null;
      }
    }
    self::$ownerList[$pathEntity] = $project;
    return $project;
  }

  /**
   * Check if the file or directory is in a Public directory, somewhere under the top-level
   *    project directory /Public.
   * @return boolean
   */
  function isInPublicDir($path = "") {
    if(empty($path)) {
      $path = $this->getPath();
    }

    if (preg_match("/\/nees\/home\/[^\/]+\.groups\/Public/", $path)) {
      return true;
    }
    elseif (preg_match("/\/nees\/home\/[^\/]+\.groups\/(Experiment-|Simulation-)[\d]+\/Public/", $path)) {
      return true;
    }
    else if (preg_match("/\/nees\/home\/(facility|Facilities|Public)/", $path)) {
      return true;
    }

    return false;
  }


  /**
   * This function is to insert a new data file using the upload applet
   * or upload single file using php upload
   *
   * @param FileHandleUpload $_FILES
   * @param string $destination
   * @return DataFile if successed or false if failed
   */
  function newDataFileByUpload($file, $destination) {

    // Call lame helper function to hack up dest path.
    $destination = FileCommandAPI::set_directory($destination);

    // Was the upload successful?
    if( !$file['size'] || $file['error'] != UPLOAD_ERR_OK ) {
      return false;
    }

    $file_name = basename($file['name']);

    // Make our destination directory if it doesn't already exist.
    $directory = FileCommandAPI::create($destination);
    if( ! $directory->exists($destination) ) {
      if (!$directory->mkdir(true)) {
        return false;
      }
    }

    // Put the uploaded file in the right place.
    $move_file = FileCommandAPI::create($destination . "/" . $file_name);
    $move_file->ingest( $file['tmp_name'] );

    $fullname = $destination . "/" . $file_name;

    return DataFilePeer::insertOrUpdateIfDuplicate(
      $file_name,            // name
      $destination,          // path
      date('Y-m-d H:i:s'),   // created
      md5_file($fullname),   // checksum
      0,                     // directory
      filesize($fullname));  // filesize
  }


  /**
   * Insert a new data file that not loaded by upload function
   *
   * @param string $filename
   * @param string $path
   * @param boolean $isDir
   * @return DataFile if successed or false if failed
   */
  function newDataFileByFilesystem($filename, $path, $isDir=false) {

    $destination = FileCommandAPI::set_directory($path);

    // Make a FileCommandAPI object from the path.
    $directory = FileCommandAPI::create($destination);
    if( ! $directory->exists($destination) ) {
      return false;
    }

    $fullname = $destination . "/" . $filename;

    return DataFilePeer::insertOrUpdateIfDuplicate(
      $filename,             // name
      $destination,          // path
      date('Y-m-d H:i:s'),   // created
      md5_file($fullname),   // checksum
      0,                     // directory
      filesize($fullname));  // filesize
  }


  /**
   * Set path and name for a data file using a full path
   *
   * @param string $path
   */

  function setFullPath($path) {
    $this->setName(basename($path));
    $this->setPath(dirname($path));
  }


  /**
   * get full path from a datafile
   *
   * @return string $path
   */
  function getFullPath() {
    return $this->getPath() . "/" . $this->getName();
  }



  /**
   * FileCommandAPI and links on the website depend on a friendly
   * path with /nees/home and .groups removed.
   *
   * @return string $path
   */

  function getFriendlyPath() {
    return get_friendlyPath($this->getFullPath());
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  public function getRESTURI() {
    $path = $this->getFriendlyPath();
    $path = '/File/' . $path;
    return $path;
  }


  /**
   * Get the contents Link URI
   *
   * @return string $path
   */
  public function getContentsLink() {
    $path = $this->getRESTURI();
    $path .= '/content';
    return $path;
  }


  /**
   * Get files in a directory
   *
   * @param boolean $include_directories the flag that you want to include directories or not
   * @return array $files
   */
  public function getFilesInDir($include_directories = true) {
    $dir = FileCommandAPI::set_directory($this->getPath() . '/' . $this->getName());

    if($include_directories) {
      return DataFilePeer::findByDirectory($dir);
    }
    else {
      return DataFilePeer::findFilesByDirectory($dir);
    }
  }


  /**
   * Check if a data file in database exist in the disk or not
   *
   * @return boolean
   */
  public function existsInFilesystem() {
    return file_exists($this->getFullPath());
  }


  /**
   * Print out information of datafile
   *
   * @return string datafile information
   */
  public function toString() {
    return "ID: "             . $this->getId() .
    ", Name: "         . $this->getName() .
    ", Path: "         . $this->getPath() .
    ", Created: "      . ($this->getCreated() ? $this->getCreated() : "") .
    ", Is deleted: "   . ($this->getDeleted() ? "Yes" : "No") .
    ", Is directory: " . ($this->getDirectory() ? "Yes" : "No") .
    ", Filesize: "     . ($this->getDirectory() ? 0 : $this->getFilesize());
  }



  /**
   * @desc
   *   Get URL file link for a data file or directory
   *
   * @return
   *   If data file is a directory, then return the new URL address, based on current
   *     $baselink, $basepath, $path
   *   If data file is a file, return the web service call: /data/get/...
   */
  public function get_url() {

    if( $this->getDirectory() ) {
      return null;
    }
    return "/data/get" . get_friendlyPath($this->getPath()) . "/" . rawurlencode($this->getName());
  }


  /**
   * Get the friendly display size of a file
   *
   * @return String
   */
  public function get_friendlySize() {
    if($this->directory) return;

    if(is_null($this->getFilesize())) {
      $df_fullpath = $this->getFullPath();

      if(file_exists($df_fullpath)) {
        $df_filesize = filesize($df_fullpath);
        return cleanSize($df_filesize);
      }
      return "unknown";
    }
    else {
      return cleanSize($this->getFilesize());
    }
  }


  /**
   * Make a Database Copy or Full Copy (File System Copy + Database Copy)
   *
   * @param String $new_path Destination Dir
   * @param boolean $copySystemFile check if we need to copy file system (full copy)
   *
   * @return DataFile
   */
  public function copyTo($new_path, $copySystemFile = false) {

    // If destination file exists, do not overwrite it, quit now
    if(DataFilePeer::findByFullPath($new_path . "/" . $this->getName())) return null;

    if(! file_exists($this->getFullPath())) return null;

    if( $copySystemFile) {
      $newdir = FileCommandAPI::create($new_path);
      $newdir->mkdir(TRUE);

      if( ! copy($this->getFullPath(), $new_path . "/" . $this->getName())) return null;
    }

    $desc_df = $this->copy();
    $desc_df->setPath($new_path);
    $desc_df->save();
    return $desc_df;
  }


## Begin - TSUNAMI REPOSITORY MOD

  /**
   * Pick the Tsunami project id out of the path.
   */

  static function getTsunamiProjectId($path){
    if (preg_match("/\/nees\/home\/([^\/]*)\.groups/", $path, $matches)) {
      list($junk1,$junk2,$junk3,$proj)=split("-",$matches[1],4);
      return (substr($proj,0,4));
    }
    return null;
  }


  /**
   * Check if the file or directory belongs to a Public team
   * @return boolean
   */
  static function isInTsunamiPublicDir($path) {
    require_once("lib/data/tsunami/TsunamiBase.php");

    $pId = DataFile::getTsunamiProjectId($path);
    if ($pId) {
      $pDAO = TsunamiDAOFactory::newDAO("TsunamiProject");
      $p=$pDAO->getTsunamiProject($pId);
      return($p->isPublic());
    }
    return false;
  }


  /**
   * Get the thumbnail of a data file if it is an image
   * If the thumbnail is not created, create it now
   *
   * @return int $thumbId
   */
  function getImageThumbnailId() {
    $thumbId = parent::getThumbId();
    if($thumbId) {
      return $thumbId;
    }
    elseif(self::isImage()) {
      $thumb = new ImageThumbnail();

      $thumbpath = "/nees/home/Public.groups";
      $thumbname = "thumb_" . time() . "_" . $this->getName();

      //if($thumb->img_resize(self::getFullPath(), 60, $thumbpath . "/" . $thumbname)) {
      if(PhotoHelper::resize(self::getFullPath(), 120, 90, $thumbpath . "/" . $thumbname)) {
        $fullName = $thumbpath . "/" . $thumbname;
        $thumb_df = DataFilePeer::insertOrUpdateIfDuplicate($thumbname, $thumbpath, date('Y-m-d H:i:s'), md5_file($fullName), 0, filesize($fullName));

        if($thumb_df) {
          $thumb_df->setView('PUBLIC');
          $thumb_df->save();

          $this->setThumbId($thumb_df->getId());
          $this->save();
          return $thumb_df->getId();
        }
      }
    }
    return null;
  }


  /**
   * Check a data file to see if this is an image or not by its extension
   *
   * @return boolean value
   */
  function isImage() {
    $path_info = pathinfo(self::getName());
    $ext = $path_info['extension'];

    return in_array(strtolower($ext), array("gif", "jpeg", "jpg", "png"));
  }


  /**
   * Get the icon for mimetype
   *
   * @return String
   */
  function getMimeIcon() {
    return get_mimeIcon($this->getName(), $this->getDirectory());
  }


  /**
   * Alias function name for getDirectory
   *
   * @return boolean
   */
  function isDirectory() {
    return $this->getDirectory();
  }



  function isAsciiFile() {
    $fullpath = $this->getFullPath();
    if(!file_exists($fullpath) || is_dir($fullpath)) return false;

    $mime = exec("file -b '" . $fullpath . "'");

    return strpos(strtolower($mime), "ascii") !== false;
  }

} // DataFile
?>
