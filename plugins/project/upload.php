<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Import library dependencies
jimport('joomla.event.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once 'lib/data/DataFile.php';
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/EntityType.php';
require_once 'lib/data/EntityTypePeer.php';
require_once 'lib/data/Thumbnail.php';
require_once 'lib/data/DataFileLinkPeer.php';
require_once 'lib/data/DataFileLink.php';
require_once 'api/org/nees/util/PhotoHelper.php';
require_once 'api/org/nees/static/Files.php';
require_once 'api/org/nees/lib/filesystem/FileCommandAPI.php';
 
class plgProjectUpload extends JPlugin{
	
   /**
    * Constructor
    *
    * 
    */
  function plgProjectUpload( &$subject ){
    parent::__construct( $subject );
 
    // load plugin parameters
    $this->_plugin = JPluginHelper::getPlugin( 'project', 'upload' );
    $this->_params = new JParameter( $this->_plugin->params );
  }

  /**
   * @see http://docs.joomla.org/Creating_a_file_uploader_in_your_component
   * @param JUser $p_oHubUser
   * @return array
   */
  private function doUploadImage($p_oHubUser){
    //this is the name of the field in the html form, filedata is the default name for swfupload
    //so we will leave it as that
    $fieldName = 'upload';
    //echo "fieldName=$fieldName<br>";
    
    //any errors the server registered on uploading
    //print_r($_FILES);
    $fileError = $_FILES[$fieldName]['error'];
    //echo "fileError=$fileError<br>";
    if ($fileError > 0) {
      switch ($fileError){
        case 1:
        echo JText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );
        return null;

        case 2:
        echo JText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );
        return null;

        case 3:
        echo JText::_( 'ERROR PARTIAL UPLOAD' );
        return null;

        case 4:
        echo JText::_( 'ERROR NO FILE' );
        return null;
      }
    }

    //check for filesize (We're going to have files larger than 2MB.)
    $fileSize = $_FILES[$fieldName]['size'];
//    if($fileSize > 2000000){
//      echo JText::_( 'FILE BIGGER THAN 2MB' );
//    }

    //check the file extension is ok
    $fileName = $_FILES[$fieldName]['name'];
    $uploadedFileNameParts = explode('.',$fileName);
    $uploadedFileExtension = array_pop($uploadedFileNameParts);

    $validFileExts = explode(',', 'jpeg,jpg,png,gif');

    //assume the extension is false until we know its ok
    $extOk = false;

    //go through every ok extension, if the ok extension matches the file extension (case insensitive)
    //then the file extension is ok
    foreach($validFileExts as $key => $value){
      if( preg_match("/$value/i", $uploadedFileExtension ) ){
        $extOk = true;
      }
    }

    if ($extOk == false) {
      echo JText::_( 'INVALID EXTENSION' );
      return null;
    }

    //the name of the file in PHP's temp directory that we are going to move to our folder
    $fileTemp = $_FILES[$fieldName]['tmp_name'];

    //for security purposes, we will also do a getimagesize on the temp file (before we have moved it
    //to the folder) to check the MIME type of the file, and whether it has a width and height
    $imageinfo = getimagesize($fileTemp);

    //we are going to define what file extensions/MIMEs are ok, and only let these ones in (whitelisting), rather than try to scan for bad
    //types, where we might miss one (whitelisting is always better than blacklisting)
    $okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif';
    $validFileTypes = explode(",", $okMIMETypes);

    //if the temp file does not have a width or a height, or it has a non ok MIME, return
    if( !is_int($imageinfo[0]) || !is_int($imageinfo[1]) ||  !in_array($imageinfo['mime'], $validFileTypes) ){
      echo JText::_( 'INVALID FILETYPE' );
      return null;
    }

    //lose any special characters in the filename
    $fileName = ereg_replace("[^A-Za-z0-9.]", "-", $fileName);

    //always use constants when making file paths, to avoid the possibilty of remote file inclusion
    $uploadPath = JRequest::getVar('path').DS.$fileName;
    //$uploadPath = JPATH_COMPONENT.DS.'uploads'.DS.'members'.DS.$p_oHubUser->username.DS.$fileName;

    if(!JFile::upload($fileTemp, $uploadPath)) {
      echo JText::_( 'ERROR MOVING FILE' );
      return null;
    }else{
      // success, exit with code 0 for Mac users, otherwise they receive an IO Error
      //exit(0);
    }

    // success, exit with imageinfo array

    return $fileName;
  }

  private function createImageDataFile($p_strName, $p_strWarehousePath, $p_strTitle, $p_strDescription, $p_strUsageId=null){
    $oDataFile = new DataFile();
    $oDataFile = $oDataFile->newDataFileByFilesystem($p_strName, $p_strWarehousePath, false, $p_strTitle, $p_strDescription, $p_strUsageId);

    // input file
    $strSource = $p_strWarehousePath."/".$p_strName;

    // output files
    $p_strWarehousePath .= "/".Files::GENERATED_PICS;
    if(!is_dir($p_strWarehousePath)){
      $oFileCommand = FileCommandAPI::create($p_strWarehousePath);
      $oFileCommand->mkdir();
    }

    $strThumbName = "thumb_".$oDataFile->getId()."_".$p_strName;
    $strThumbnailFile = $p_strWarehousePath."/".$strThumbName;
    $bThumbnail = $this->scaleImage($strSource, PhotoHelper::DEFAULT_THUMB_WIDTH, PhotoHelper::DEFAULT_THUMB_HEIGHT, $strThumbnailFile);
    if($bThumbnail){
      /* @var $oEntityType EntityType */
      $oEntityType = EntityTypePeer::findByTableName("Thumbnail");

      $oThumbDataFile = new DataFile();
      $oThumbDataFile = $oThumbDataFile->newDataFileByFilesystem($strThumbName, $p_strWarehousePath, false, $p_strTitle, $p_strDescription, $oEntityType->getId());
    }

    $strDisplayName = "display_".$oDataFile->getId()."_".$p_strName;
    $strDisplayFile = $p_strWarehousePath."/".$strDisplayName;
    $bDisplay = $this->scaleImage($strSource, PhotoHelper::DEFAULT_DISPLAY_WIDTH, PhotoHelper::DEFAULT_DISPLAY_HEIGHT, $strDisplayFile);
    if($bDisplay){
      /* @var $oEntityType EntityType */
      $oEntityType = EntityTypePeer::findByTableName("Data Photo");

      $oDisplayDataFile = new DataFile();
      $oDisplayDataFile = $oDisplayDataFile->newDataFileByFilesystem($strDisplayName, $p_strWarehousePath, false, $p_strTitle, $p_strDescription, $oEntityType->getId());
    }

    $escSource = escapeshellarg($p_strWarehousePath);
    exec("/nees/home/bin/fix_permissions $escSource", $output);  
  }

  /**
   *
   * @global <type> $mainframe
   * @param <type> $params
   */
  function onPhotoUpload(&$params){
    global $mainframe;

    $oHubUser =& JFactory::getUser();
    $oImageInfoArray = $this->doUploadImage($oHubUser);

  }
  
  /**
   *
   * @global <type> $mainframe
   * @param <type> $params
   */
  function onDrawingUpload(&$params){
    global $mainframe;
    
    $oHubUser =& JFactory::getUser();
    $strName = $this->doUploadImage($oHubUser);

    $oDataFile = null;
    if($strName){
      $strTitle = JRequest::getVar('title');
      $strDesc = JRequest::getVar('desc');
      $strPath = JRequest::getVar('path');
      $strUsageTypeId = JRequest::getVar('usageType');

      $this->createImageDataFile($strName, $strPath, $strTitle, $strDesc, $strUsageTypeId);
    }

    return $oDataFile;
  }

  /**
   *
   * @global <type> $mainframe
   * @param <type> $params
   */
  function onFilmstripUpload(&$params){
    global $mainframe;



  }
  
  private function scaleImage($p_strSource, $p_iWidth, $p_iHeight, $p_strSaveFile){
    return PhotoHelper::resize($p_strSource, $p_iWidth, $p_iHeight, $p_strSaveFile);
  }

  private function scaleImageByWidth($p_strSource, $p_iWidth, $p_strSaveFile){
    return PhotoHelper::resizeByWidth($p_strSource, $p_iWidth, $p_strSaveFile);
  }

  /**
   * Scale an active DataFile
   * @global  $mainframe
   * @param <type> $params
   */
  function onScaleImageDataFile(&$params){
    global $mainframe;

    /* @var $oDataFile DataFile */
    $oDataFile = unserialize($_REQUEST[DataFilePeer::TABLE_NAME]);

    // input file
    $strSource = $oDataFile->getPath()."/".$oDataFile->getName();

    // output files
    $strWarehousePath = $oDataFile->getPath()."/".Files::GENERATED_PICS;
    
    $strThumbName = "thumb_".$oDataFile->getId()."_".$oDataFile->getName();
    $strThumbnailFile = $strWarehousePath."/".$strThumbName;

    $strDisplayName = "display_".$oDataFile->getId()."_".$oDataFile->getName();
    $strDisplayFile = $strWarehousePath."/".$strDisplayName;

    $bThumbExists = false;
    if(is_file($strThumbnailFile)){
      $bThumbExists = true;
    }

    $bDisplayExists = false;
    if(is_file($strDisplayFile)){
      $bDisplayExists = true;
    }

    if($bThumbExists && $bDisplayExists){
      return true;
    }

    //get the sizes
    $iThumbWidth = PhotoHelper::DEFAULT_THUMB_WIDTH;
    $iThumbHeight = PhotoHelper::DEFAULT_THUMB_HEIGHT;

    $iDisplayWidth = PhotoHelper::DEFAULT_DISPLAY_WIDTH;
    $iDisplayHeight = PhotoHelper::DEFAULT_DISPLAY_HEIGHT;

    $strUsageTypeName = "";

    /* @var $oUsageEntityType EntityType */
    $oUsageEntityType = $oDataFile->getEntityType();
    if($oUsageEntityType){
      $strUsageEntityType = $oUsageEntityType->getDatabaseTableName();
      switch ($strUsageEntityType){
        case "Project Image":
          $iThumbWidth = PhotoHelper::PROJECT_THUMB_WIDTH;
          $iThumbHeight = PhotoHelper::PROJECT_THUMB_HEIGHT;
        break;
        case "Experiment Image":
          $iThumbWidth = PhotoHelper::EXPERIMENT_THUMB_WIDTH;
          $iThumbHeight = PhotoHelper::EXPERIMENT_THUMB_HEIGHT;
        break;
      }
    }


    if(!is_dir($strWarehousePath)){
      $oFileCommand = FileCommandAPI::create($strWarehousePath);
      $oFileCommand->mkdir();
    }

    $bThumbnail = $this->scaleImage($strSource, $iThumbWidth, $iThumbHeight, $strThumbnailFile);
    if($bThumbnail){
      $oEntityType = EntityTypePeer::findByTableName("Thumbnail");

      $oThumbDataFile = new DataFile();
      $oThumbDataFile = $oThumbDataFile->newDataFileByFilesystem($strThumbName, $strWarehousePath, false, $oDataFile->getTitle(), $oDataFile->getDescription(), $oEntityType->getId());
    }

    $bDisplay = $this->scaleImage($strSource, $iDisplayWidth, $iDisplayHeight, $strDisplayFile);
    if($bDisplay){
      $oEntityType = EntityTypePeer::findByTableName("Data Photo");

      $oDisplayDataFile = new DataFile();
      $oDisplayDataFile = $oDisplayDataFile->newDataFileByFilesystem($strDisplayName, $strWarehousePath, false, $oDataFile->getTitle(), $oDataFile->getDescription(), $oEntityType->getId());
    }

    $escSource = escapeshellarg($strWarehousePath);
    exec("/nees/home/bin/fix_permissions $escSource", $output);

    return true;
  }

  /**
   * Scale an active DataFile
   * @global  $mainframe
   * @param <type> $params
   */
  function onScaleImageDataFileByWidth(&$params){
    global $mainframe;

    /* @var $oDataFile DataFile */
    $oDataFile = unserialize($_REQUEST[DataFilePeer::TABLE_NAME]);
    if(!$oDataFile){
      return false;
    }

    // input file
    $strSource = $oDataFile->getPath()."/".$oDataFile->getName();

    // output files
    $strWarehousePath = $oDataFile->getPath()."/".Files::GENERATED_PICS;

    $strThumbName = "thumb_".$oDataFile->getId()."_".$oDataFile->getName();
    $strThumbnailFile = $strWarehousePath."/".$strThumbName;

    $strDisplayName = "display_".$oDataFile->getId()."_".$oDataFile->getName();
    $strDisplayFile = $strWarehousePath."/".$strDisplayName;

    $strIconName = "icon_".$oDataFile->getId()."_".$oDataFile->getName();
    $strIconFile = $strWarehousePath."/".$strIconName;

    $bThumbExists = false;
    if(is_file($strThumbnailFile)){
      $bThumbExists = true;
    }

    $bDisplayExists = false;
    if(is_file($strDisplayFile)){
      $bDisplayExists = true;
    }

    if($bThumbExists && $bDisplayExists){
      return true;
    }

    //get the sizes
    $iThumbWidth = PhotoHelper::DEFAULT_THUMB_WIDTH;
    $iThumbHeight = PhotoHelper::DEFAULT_THUMB_HEIGHT;

    $iDisplayWidth = PhotoHelper::DEFAULT_DISPLAY_WIDTH;
    $iDisplayHeight = PhotoHelper::DEFAULT_DISPLAY_HEIGHT;

    $iId = $oDataFile->getId();
    /* @var $oUsageEntityType EntityType */
    $oUsageEntityType = $oDataFile->getEntityType();
    if($oUsageEntityType){
      $strUsageEntityType = $oUsageEntityType->getDatabaseTableName();
      switch ($strUsageEntityType){
        case "Project Image":
          $iThumbWidth = PhotoHelper::PROJECT_THUMB_WIDTH;
          $iThumbHeight = PhotoHelper::PROJECT_THUMB_HEIGHT;
        break;
        case "Experiment Image":
          $iThumbWidth = PhotoHelper::EXPERIMENT_THUMB_WIDTH;
          $iThumbHeight = PhotoHelper::EXPERIMENT_THUMB_HEIGHT;
        break;
      }
    }


    if(!is_dir($strWarehousePath)){
      $oFileCommand = FileCommandAPI::create($strWarehousePath);
      $oFileCommand->mkdir();
    }
    
    $bThumbnail = $this->scaleImageByWidth($strSource, $iThumbWidth, $strThumbnailFile);
    if($bThumbnail){
      $oEntityType = EntityTypePeer::findByTableName("Thumbnail");

      $oThumbDataFile = new DataFile();
      $oThumbDataFile = $oThumbDataFile->newDataFileByFilesystem($strThumbName, $strWarehousePath, false, $oDataFile->getTitle(), $oDataFile->getDescription(), $oEntityType->getId());
    }

    $bDisplay = $this->scaleImageByWidth($strSource, $iDisplayWidth, $strDisplayFile);
    if($bDisplay){
      $oEntityType = EntityTypePeer::findByTableName("Data Photo");

      $oDisplayDataFile = new DataFile();
      $oDisplayDataFile = $oDisplayDataFile->newDataFileByFilesystem($strDisplayName, $strWarehousePath, false, $oDataFile->getTitle(), $oDataFile->getDescription(), $oEntityType->getId());
    }

    //ONLY FOR PROJECTS. (displays in search results)
    if ($strUsageEntityType=="Project Image"){
      $bIcon = $this->scaleImageByWidth($strSource, PhotoHelper::DEFAULT_THUMB_WIDTH, $strIconFile);
      if($bIcon){
        $oEntityType = EntityTypePeer::findByTableName("Project Icon");

        //store the icon into the data_file table
        $oIconDataFile = new DataFile();
        $oIconDataFile = $oIconDataFile->newDataFileByFilesystem($strIconName, $strWarehousePath, false, $oDataFile->getTitle(), $oDataFile->getDescription(), $oEntityType->getId());

        //for record keeping, ensure thumbnail table knows about data_file
        $oDataFileLink = DataFileLinkPeer::retrieveByPK($oDataFile->getId());
        $oThumbnail = new Thumbnail($oDataFile->getId(), $oDataFileLink->getProjectId(), 1);
        $oThumbnail->save();

        //update the project's thumb_id
        $oDataFile->setThumbId($oIconDataFile->getId());
        $oDataFile->setView("PUBLIC");
        $oDataFile->save();
      }
    }

    return true;
  }
 
}
?>
