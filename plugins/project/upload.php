<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Import library dependencies
jimport('joomla.event.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once 'lib/data/DataFile.php';
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DocumentFormatPeer.php';
require_once 'lib/data/DocumentFormat.php';
require_once 'lib/data/EntityType.php';
require_once 'lib/data/EntityTypePeer.php';
require_once 'lib/data/Thumbnail.php';
require_once 'lib/data/DataFileLinkPeer.php';
require_once 'lib/data/DataFileLink.php';
require_once 'api/org/nees/util/PhotoHelper.php';
require_once 'api/org/nees/util/FileHelper.php';
require_once 'api/org/nees/static/Files.php';
require_once 'api/org/nees/static/ProjectEditor.php';
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
  private function doUploadImage($p_oHubUser, $p_iIndex=0){
    //this is the name of the field in the html form, filedata is the default name for swfupload
    //so we will leave it as that
    $fieldName = 'upload';
    
    //any errors the server registered on uploading
    //$fileError = ($p_iIndex===0) ? $_FILES[$fieldName]['error'] : $_FILES[$fieldName]['error'][$p_iIndex];
    $fileError = (is_array($_FILES[$fieldName]['error'])) ? $_FILES[$fieldName]['error'][$p_iIndex] : $_FILES[$fieldName]['error'];
    if ($fileError > 0) {
      switch ($fileError){
        case 1:
        //echo JText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );
        //return null;
        return ProjectEditor::UPLOAD_CODE_FILE_LARGER_PHP_INI;

        case 2:
        //echo JText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );
        //return null;
        return ProjectEditor::UPLOAD_CODE_FILE_LARGER_HTML_FORM;

        case 3:
        //echo JText::_( 'ERROR PARTIAL UPLOAD' );
        //return null;
        return ProjectEditor::UPLOAD_CODE_PARTIAL_UPLOAD;

        case 4:
        //echo JText::_( 'ERROR NO FILE' );
        //return null;
        return ProjectEditor::UPLOAD_CODE_NO_FILE;
      }
    }

    //check for filesize (We're going to have files larger than 1.5MB.)
    $fileSize = (is_array($_FILES[$fieldName]['size'])) ? $_FILES[$fieldName]['size'][$p_iIndex] : $_FILES[$fieldName]['size'];
    if($fileSize > 1500000000){
      return ProjectEditor::UPLOAD_CODE_FILE_TOO_BIG;
    }

    //check the file extension is ok
    $fileName = (is_array($_FILES[$fieldName]['name'])) ? $_FILES[$fieldName]['name'][$p_iIndex] : $_FILES[$fieldName]['name'];
    $uploadedFileNameParts = explode('.',$fileName);
    $uploadedFileExtension = array_pop($uploadedFileNameParts);

    $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);

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
      return ProjectEditor::UPLOAD_CODE_INVALID_EXTENSION;
    }

    //the name of the file in PHP's temp directory that we are going to move to our folder
    $fileTemp = (is_array($_FILES[$fieldName]['tmp_name'])) ? $_FILES[$fieldName]['tmp_name'][$p_iIndex] : $_FILES[$fieldName]['tmp_name'];

    //for security purposes, we will also do a getimagesize on the temp file (before we have moved it
    //to the folder) to check the MIME type of the file, and whether it has a width and height
    $imageinfo = getimagesize($fileTemp);

    //we are going to define what file extensions/MIMEs are ok, and only let these ones in (whitelisting), rather than try to scan for bad
    //types, where we might miss one (whitelisting is always better than blacklisting)
    $okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif';
    $validFileTypes = explode(",", $okMIMETypes);

    //if the temp file does not have a width or a height, or it has a non ok MIME, return
    if( !is_int($imageinfo[0]) || !is_int($imageinfo[1]) ||  !in_array($imageinfo['mime'], $validFileTypes) ){
      //echo JText::_( 'INVALID FILETYPE' );
      //return null;
      return ProjectEditor::UPLOAD_CODE_INVALID_FILE_TYPE;
    }

    //lose any special characters in the filename
    $fileName = ereg_replace("[^A-Za-z0-9.]", "-", $fileName);

    //always use constants when making file paths, to avoid the possibilty of remote file inclusion
    $uploadPath = JRequest::getVar('path').DS.$fileName;
    //$uploadPath = JPATH_COMPONENT.DS.'uploads'.DS.'members'.DS.$p_oHubUser->username.DS.$fileName;

    if(!JFile::upload($fileTemp, $uploadPath)) {
      //echo JText::_( 'ERROR MOVING FILE' );
      //return null;
      return ProjectEditor::UPLOAD_CODE_ERROR_MOVING_FILE;
    }else{
      // success, exit with code 0 for Mac users, otherwise they receive an IO Error
      //exit(0);
    }

    // success, exit with imageinfo array

    return $fileName;
  }

  /**
   * We don't have an image.  Upload the given file.
   * @param JUser $p_oHubUser
   * @param string $p_strFieldName
   * @return string
   */
  private function doUploadFile($p_oHubUser, $p_strFieldName, $p_iIndex=0){
    //this is the name of the field in the html form, filedata is the default name for swfupload
    //so we will leave it as that
    $fieldName = $p_strFieldName;

    //any errors the server registered on uploading
    $fileError = $_FILES[$fieldName]['error'][$p_iIndex];
    if ($fileError > 0) {
      switch ($fileError){
        case 1:
        //echo JText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );
        //return null;
        return ProjectEditor::UPLOAD_CODE_FILE_LARGER_PHP_INI;

        case 2:
        //echo JText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );
        //return null;
        return ProjectEditor::UPLOAD_CODE_FILE_LARGER_HTML_FORM;

        case 3:
        //echo JText::_( 'ERROR PARTIAL UPLOAD' );
        //return null;
        return ProjectEditor::UPLOAD_CODE_PARTIAL_UPLOAD;

        case 4:
        //echo JText::_( 'ERROR NO FILE' );
        //return null;
        return ProjectEditor::UPLOAD_CODE_NO_FILE;
      }
    }

    //check for filesize (We're going to have files larger than 2MB.)
    $fileSize = $_FILES[$fieldName]['size'][$p_iIndex];
    if($fileSize > 1000000000){
      return ProjectEditor::UPLOAD_CODE_FILE_TOO_BIG;
    }

    //check the file extension is ok
    $fileName = $_FILES[$fieldName]['name'][$p_iIndex];
    $uploadedFileNameParts = explode('.',$fileName);
    $uploadedFileExtension = array_pop($uploadedFileNameParts);

    //the name of the file in PHP's temp directory that we are going to move to our folder
    $fileTemp = $_FILES[$fieldName]['tmp_name'][$p_iIndex];

    //lose any special characters in the filename
    $fileName = ereg_replace("[^A-Za-z0-9.]", "-", $fileName);

    //always use constants when making file paths, to avoid the possibilty of remote file inclusion
    $uploadPath = JRequest::getVar('path').DS.$fileName;
    //$uploadPath = JPATH_COMPONENT.DS.'uploads'.DS.'members'.DS.$p_oHubUser->username.DS.$fileName;

    if(!JFile::upload($fileTemp, $uploadPath)) {
      //echo JText::_( 'ERROR MOVING FILE' );
      //return null;
      return ProjectEditor::UPLOAD_CODE_ERROR_MOVING_FILE;
    }else{
      // success, exit with code 0 for Mac users, otherwise they receive an IO Error
      //exit(0);
    }

    
    return $fileName;
  }

  /**
   * Save and scale a data file image.
   * @param string $p_strName
   * @param string $p_strWarehousePath
   * @param string $p_strTitle
   * @param string $p_strDescription
   * @param string $p_strUsageId
   */
  private function createImageDataFile($p_strName, $p_strWarehousePath, $p_strTitle, $p_strDescription, $p_strUsageId=null){
    $oDataFile = new DataFile();
    $oDataFile = $oDataFile->newDataFileByFilesystem($p_strName, $p_strWarehousePath, false, $p_strTitle, $p_strDescription, $p_strUsageId);
    $uploadedFileNameParts = explode('.', $p_strName);
    if(sizeof($uploadedFileNameParts) == 2){
      $uploadedFileExtension = array_pop($uploadedFileNameParts);

      /* @var $oDocumentFormat DocumentFormat */
      $oDocumentFormat = DocumentFormatPeer::findByDefaultExtension($uploadedFileExtension);
      $oDataFile->setDocumentFormat($oDocumentFormat);
      $oDataFile->save();
    }

    // input file
    $strSource = $p_strWarehousePath."/".$p_strName;

    $bMkDir = false;

    // output files
    $p_strWarehousePath .= "/".Files::GENERATED_PICS;
    if(!is_dir($p_strWarehousePath)){
      $oFileCommand = FileCommandAPI::create($p_strWarehousePath);
      $bMkDir = $oFileCommand->mkdir();
    }

    $bThumbnail = false;
    $bDisplay = false;

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

    if($bThumbnail || $bDisplay || $bMkDir){
      FileHelper::fixPermissions($p_strWarehousePath);
    }
  }

  private function createProjectImageDataFile($p_strName, $p_strWarehousePath, $p_strTitle, $p_strDescription, $p_strUsageId=null, $p_iFixPermissions=0){
    $oDataFile = new DataFile();
    $oDataFile = $oDataFile->newDataFileByFilesystem($p_strName, $p_strWarehousePath, false, $p_strTitle, $p_strDescription, $p_strUsageId);
    $uploadedFileNameParts = explode('.', $p_strName);
    if(sizeof($uploadedFileNameParts) == 2){
      $uploadedFileExtension = array_pop($uploadedFileNameParts);

      /* @var $oDocumentFormat DocumentFormat */
      $oDocumentFormat = DocumentFormatPeer::findByDefaultExtension($uploadedFileExtension);
      $oDataFile->setDocumentFormat($oDocumentFormat);
      $oDataFile->save();
    }

    // input file
    $strSource = $p_strWarehousePath."/".$p_strName;

    $bMkDir = false;

    // output files
    $p_strWarehousePath .= "/".Files::GENERATED_PICS;
    if(!is_dir($p_strWarehousePath)){
      $oFileCommand = FileCommandAPI::create($p_strWarehousePath);
      $bMkDir = $oFileCommand->mkdir();
    }

    $bThumbnail = false;
    $bDisplay = false;

    $strThumbName = "thumb_".$oDataFile->getId()."_".$p_strName;
    $strThumbnailFile = $p_strWarehousePath."/".$strThumbName;
    $bThumbnail = $this->scaleImageByWidth($strSource, PhotoHelper::PROJECT_THUMB_WIDTH, $strThumbnailFile);
    if($bThumbnail){
      /* @var $oEntityType EntityType */
      $oEntityType = EntityTypePeer::findByTableName("Thumbnail");

      $oThumbDataFile = new DataFile();
      $oThumbDataFile = $oThumbDataFile->newDataFileByFilesystem($strThumbName, $p_strWarehousePath, false, $p_strTitle, $p_strDescription, $oEntityType->getId());
      $oThumbDataFile->setView("PUBLIC");
      $oThumbDataFile->save();
    }

    $strDisplayName = "display_".$oDataFile->getId()."_".$p_strName;
    $strDisplayFile = $p_strWarehousePath."/".$strDisplayName;
    $bDisplay = $this->scaleImageByWidth($strSource, PhotoHelper::DEFAULT_DISPLAY_WIDTH, $strDisplayFile);
    if($bDisplay){
      /* @var $oEntityType EntityType */
      $oEntityType = EntityTypePeer::findByTableName("Data Photo");

      $oDisplayDataFile = new DataFile();
      $oDisplayDataFile = $oDisplayDataFile->newDataFileByFilesystem($strDisplayName, $p_strWarehousePath, false, $p_strTitle, $p_strDescription, $oEntityType->getId());
      $oDisplayDataFile->setView("PUBLIC");
      $oDisplayDataFile->save();
    }

    $strIconName = "icon_".$oDataFile->getId()."_".$p_strName;
    $strIconFile = $p_strWarehousePath."/".$strIconName;
    $bIcon = $this->scaleImageByWidth($strSource, PhotoHelper::DEFAULT_THUMB_WIDTH, $strIconFile);
    if($bIcon){
      /* @var $oEntityType EntityType */
      $oEntityType = EntityTypePeer::findByTableName("Project Icon");

      //store the icon into the data_file table
      $oIconDataFile = new DataFile();
      $oIconDataFile = $oIconDataFile->newDataFileByFilesystem($strIconName, $p_strWarehousePath, false, $p_strTitle, $p_strDescription, $oEntityType->getId());
      $oIconDataFile->setView("PUBLIC");
      $oIconDataFile->save();

      //for record keeping, ensure thumbnail table knows about data_file
      $oDataFileLink = DataFileLinkPeer::retrieveByPK($oDataFile->getId());

      $oThumbnail = null;
      if(ThumbnailPeer::exists($oDataFileLink->getProjectId(), 1)){
        $oThumbnail = ThumbnailPeer::updateThumbnail($oDataFile->getId(), $oDataFileLink->getProjectId(), 1);
      }else{
        $oThumbnail = new Thumbnail($oDataFile->getId(), $oDataFileLink->getProjectId(), 1);
        $oThumbnail->save();
      }

      $strMessage = "";

      if($oThumbnail){
        try{
        $oDataFile->setThumbId($oThumbDataFile->getId());
        $oDataFile->save();
        $strMessage = '<br>thumb_id updated<br>';
        }catch(Exception $e){
          $strMessage = $e->getMessage();
        }
      }
    }

    if($p_iFixPermissions){
      if($bThumbnail || $bDisplay || $bIcon || $bMkDir){
        FileHelper::fixPermissions($p_strWarehousePath);
      }
    }

    return $strMessage;
  }

  private function createExperimentImageDataFile($p_strName, $p_strWarehousePath, $p_strTitle, $p_strDescription, $p_strUsageId=null, $p_iFixPermissions=0){
    $oDataFile = new DataFile();
    $oDataFile = $oDataFile->newDataFileByFilesystem($p_strName, $p_strWarehousePath, false, $p_strTitle, $p_strDescription, $p_strUsageId);
    $uploadedFileNameParts = explode('.', $p_strName);
    if(sizeof($uploadedFileNameParts) == 2){
      $uploadedFileExtension = array_pop($uploadedFileNameParts);

      /* @var $oDocumentFormat DocumentFormat */
      $oDocumentFormat = DocumentFormatPeer::findByDefaultExtension($uploadedFileExtension);
      $oDataFile->setDocumentFormat($oDocumentFormat);
      $oDataFile->save();
    }
    
    // input file
    $strSource = $p_strWarehousePath."/".$p_strName;

    $bMkDir = false;

    // output files
    $p_strWarehousePath .= "/".Files::GENERATED_PICS;
    if(!is_dir($p_strWarehousePath)){
      $oFileCommand = FileCommandAPI::create($p_strWarehousePath);
      $bMkDir = $oFileCommand->mkdir();
    }

    $bThumbnail = false;
    $bDisplay = false;

    $strThumbName = "thumb_".$oDataFile->getId()."_".$p_strName;
    $strThumbnailFile = $p_strWarehousePath."/".$strThumbName;
    $bThumbnail = $this->scaleImage($strSource, PhotoHelper::EXPERIMENT_THUMB_WIDTH, PhotoHelper::EXPERIMENT_THUMB_HEIGHT, $strThumbnailFile);
    if($bThumbnail){
      /* @var $oEntityType EntityType */
      $oEntityType = EntityTypePeer::findByTableName("Thumbnail");

      $oThumbDataFile = new DataFile();
      $oThumbDataFile = $oThumbDataFile->newDataFileByFilesystem($strThumbName, $p_strWarehousePath, false, $p_strTitle, $p_strDescription, $oEntityType->getId());
      $oThumbDataFile->setView("PUBLIC");
      $oThumbDataFile->save();
    }

    $strDisplayName = "display_".$oDataFile->getId()."_".$p_strName;
    $strDisplayFile = $p_strWarehousePath."/".$strDisplayName;
    $bDisplay = $this->scaleImage($strSource, PhotoHelper::DEFAULT_DISPLAY_WIDTH, PhotoHelper::DEFAULT_DISPLAY_HEIGHT, $strDisplayFile);
    if($bDisplay){
      /* @var $oEntityType EntityType */
      $oEntityType = EntityTypePeer::findByTableName("Data Photo");

      $oDisplayDataFile = new DataFile();
      $oDisplayDataFile = $oDisplayDataFile->newDataFileByFilesystem($strDisplayName, $p_strWarehousePath, false, $p_strTitle, $p_strDescription, $oEntityType->getId());
      $oDisplayDataFile->setView("PUBLIC");
      $oDisplayDataFile->save();
    }

    if($bThumbnail || $bDisplay){
      //for record keeping, ensure thumbnail table knows about data_file
      $oDataFileLink = DataFileLinkPeer::retrieveByPK($oDataFile->getId());

      $oThumbnail = null;
      if(ThumbnailPeer::exists($oDataFileLink->getExperimentId(), 3)){
        $oThumbnail = ThumbnailPeer::updateThumbnail($oDataFile->getId(), $oDataFileLink->getExperimentId(), 3);
      }else{
        $oThumbnail = new Thumbnail($oDataFile->getId(), $oDataFileLink->getExperimentId(), 3);
        $oThumbnail->save();
      }

      $strMessage = "";
      if($oThumbnail){
        try{
          $oDataFile->setThumbId($oThumbDataFile->getId());
          $oDataFile->save();
        }catch(Exception $e){
          $strMessage = $e->getMessage();
        }
      }
    }

    if($p_iFixPermissions){
      if($bThumbnail || $bDisplay || $bMkDir){
        FileHelper::fixPermissions($p_strWarehousePath);
      }
    }

    return $strMessage;
  }

  /**
   * Uploads images.  If the path is not supplied, put the file in the
   * user's upload directory.  Otherwise, place the file where instructed.
   * @global  $mainframe
   * @param <type> $params
   * @return <type>
   */
  function onPhotoUpload(&$params){
    global $mainframe;

    $strName = "";
    $bValidPath = true;

    $oHubUser =& JFactory::getUser();

    $strPath = JRequest::getVar('path','');
    if(!StringHelper::hasText($strPath)){  
      $strPath = ProjectEditor::PROJECT_UPLOAD_DIR."/".$oHubUser->username;
      if(!is_dir($strPath)){
        $bValidPath = mkdir($strPath, 0700);
      }
    }

    $iNumFiles = JRequest::getInt('files_num', 1);
    $i=0;
    while($i < $iNumFiles){
      $ii = $i+1;
      if($bValidPath){
        JRequest::setVar('path', $strPath);
        $oReturn = $this->doUploadImage($oHubUser, $i);
        if(is_numeric($oReturn)){
          return $oReturn;
        }
      }

      $strName = $oReturn;
      if($strName){
        $strTitle = JRequest::getVar('title','');
        $strDescription = JRequest::getVar('desc','');
        $iUsageTypeId = JRequest::getVar('usageType');
        $strFileTitle = ($iNumFiles <= 1) ? $strTitle : $strTitle." (".$ii." of ".$iNumFiles.")";

        $this->createImageDataFile($strName, $strPath, $strFileTitle, $strDescription, $iUsageTypeId);
      }
      ++$i;
    }
    

    return $strName;
  }

  /**
   * Uploads images.  If the path is not supplied, put the file in the
   * user's upload directory.  Otherwise, place the file where instructed.
   * @global  $mainframe
   * @param <type> $params
   * @return <type>
   */
  function onPhotoPreviewUpload(&$params){
    global $mainframe;

    $bValidPath = true;

    $oHubUser =& JFactory::getUser();

    $strPath = JRequest::getVar('path','');
    if(!StringHelper::hasText($strPath)){
      $strPath = ProjectEditor::PROJECT_UPLOAD_DIR."/".$oHubUser->username;
      if(!is_dir($strPath)){
        $bValidPath = mkdir($strPath, 0700);
      }
    }

    if($bValidPath){
      JRequest::setVar('path', $strPath);
      $oReturn = $this->doUploadImage($oHubUser);
      if(is_numeric($oReturn)){
        return $oReturn;
      }
    }

    //not error code.  return file name
    return $oReturn;
  }

  /**
   *
   * @global  $mainframe
   * @param <type> $params
   * @return <type>
   */
  function onDataUpload(&$params){
    global $mainframe;

    $oDataFile = null;

    $oHubUser =& JFactory::getUser();

    $iUsageTypeId = JRequest::getVar('usageType');
    $strUploadedPath = JRequest::getVar('path');
    $strTool = JRequest::getVar('tool');

    /* @var $oEntityType EntityType */
    $oEntityType = EntityTypePeer::retrieveByPK($iUsageTypeId);
    if($oEntityType){
      $strUsageType = $oEntityType->getDatabaseTableName();
      $strUsageTypeArray = explode("-", $strUsageType);
      if(sizeof($strUsageTypeArray)==2){
        $strUploadedPath = $strUploadedPath. "/". $strUsageTypeArray[1];
        JRequest::setVar('path', $strUploadedPath);
      }
    }else{
      $iUsageTypeId = null;
    }

    $iCounter = 0;
    $i=0;
    $iNumFiles = JRequest::getInt('files_num', 1);
    while($i < $iNumFiles){
      $ii = $i+1;

      $strFileName = $this->doUploadFile($oHubUser, ProjectEditor::UPLOAD_FIELD_NAME, $i);
      if(is_numeric($strFileName)){
        //if we are here, we have an error code
        return $strFileName;
      }

      //echo "file check=$strFileName<br>";
      if($strFileName){
        $strDescription = JRequest::getVar('desc');
        $strTitle = JRequest::getVar('title');
        $strFileTitle = ($iNumFiles <= 1) ? $strTitle : $strTitle." (".$ii." of ".$iNumFiles.")";
        
        //store the data file
        $oDataFile = new DataFile();
        $oDataFile = $oDataFile->newDataFileByFilesystem($strFileName, $strUploadedPath, false, $strFileTitle, $strDescription, $iUsageTypeId, $strTool);
        if($oDataFile){
          ++$iCounter;
          $uploadedFileNameParts = explode('.', $strFileName);
          if(sizeof($uploadedFileNameParts) == 2){
            $uploadedFileExtension = array_pop($uploadedFileNameParts);

            /* @var $oDocumentFormat DocumentFormat */
            $oDocumentFormat = DocumentFormatPeer::findByDefaultExtension($uploadedFileExtension);
            $oDataFile->setDocumentFormat($oDocumentFormat);
            $oDataFile->save();
          }
        }
      }

      ++$i;
    }

    if($iCounter > 0){
      FileHelper::fixPermissions($strUploadedPath);
    }

    return serialize($oDataFile);
  }

  /**
   *
   * @global <type> $mainframe
   * @param <type> $params
   */
  function onProjectPhotoUpload(&$params){
    global $mainframe;

    $strTitle = JRequest::getVar('title');
    $strDesc = JRequest::getVar('desc','');
    $strPath = JRequest::getVar('path');
    $iUsageTypeId = JRequest::getVar('usageType');
    $strName = JRequest::getVar('name');
    $iFixPermissions = JRequest::getVar('fixPermissions', 0);

    $oDataFile = null;
    if($strName){
      $this->createProjectImageDataFile($strName, $strPath, $strTitle, $strDesc, $iUsageTypeId, $iFixPermissions);
    }

  }

  /**
   *
   * @global <type> $mainframe
   * @param <type> $params
   */
  function onExperimentPhotoUpload(&$params){
    global $mainframe;

    $strTitle = JRequest::getVar('title');
    $strDesc = JRequest::getVar('desc','');
    $strPath = JRequest::getVar('path');
    $iUsageTypeId = JRequest::getVar('usageType');
    $iFixPermissions = JRequest::getVar('fixPermissions', 0);

    $oHubUser =& JFactory::getUser();

    $oReturn = $this->doUploadImage($oHubUser);
    if(is_numeric($oReturn)){
      return $oReturn;
    }

    $strFileName = $oReturn;
    if($strFileName){
      $this->createExperimentImageDataFile($strFileName, $strPath, $strTitle, $strDesc, $iUsageTypeId, $iFixPermissions);
    }

  }

  /**
   *
   * @global <type> $mainframe
   * @param <type> $params
   */
  function onDrawingUpload(&$params){
    global $mainframe;

    //incoming
    $strTitle = JRequest::getVar('title');
    $strDesc = JRequest::getVar('desc');
    $strPath = JRequest::getVar('path');
    $iUsageTypeId = JRequest::getVar('usageType');
    $iNumFiles = JRequest::getInt('files_num', 1);

    /* @var $oEntityType EntityType */
    $oEntityType = EntityTypePeer::retrieveByPK($iUsageTypeId);
    if($oEntityType){
      $strUsageType = $oEntityType->getDatabaseTableName();
      $strUsageTypeArray = explode("-", $strUsageType);
      if(sizeof($strUsageTypeArray)==2){
        $strPath = $strPath. "/". $strUsageTypeArray[1];
        JRequest::setVar('path', $strPath);
      }
    }

    $oHubUser =& JFactory::getUser();

    $i=0;
    while($i < $iNumFiles){
      $ii = $i+1;
      $oReturn = $this->doUploadImage($oHubUser, $i);
      if(is_numeric($oReturn)){
        return $oReturn;
      }

      $strName = $oReturn;
      $strFileTitle = ($iNumFiles <= 1) ? $strTitle : $strTitle." (".$ii." of ".$iNumFiles.")";
      if($strName){
        $this->createImageDataFile($strName, $strPath, $strFileTitle, $strDesc, $iUsageTypeId);
      }
      ++$i;
    }

    return $strName;
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

    $bMkDir = false;

    if(!is_dir($strWarehousePath)){
      $oFileCommand = FileCommandAPI::create($strWarehousePath);
      $bMkDir = $oFileCommand->mkdir();
    }

    $bThumbnail = false;
    $bDisplay = false;

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

    $bFixPermissionsLater = false;
    if(isset($_REQUEST["fixPermissionsLater"])){
      $bFixPermissions = $_REQUEST["fixPermissionsLater"];
    }

    if(!$bFixPermissionsLater){
      if($bThumbnail || $bDisplay || $bMkDir){
        FileHelper::fixPermissions($strWarehousePath);
      }
    }

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


    $bMkDir = false;
    if(!is_dir($strWarehousePath)){
      $oFileCommand = FileCommandAPI::create($strWarehousePath);
      $bMkDir = $oFileCommand->mkdir();
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

    $bIcon = false;

    //ONLY FOR PROJECTS. (displays in search results)
    if ($strUsageEntityType=="Project Image"){
      $bIcon = $this->scaleImageByWidth($strSource, PhotoHelper::DEFAULT_THUMB_WIDTH, $strIconFile);
      if($bIcon){
        $oEntityType = EntityTypePeer::findByTableName("Project Icon");

        //store the icon into the data_file table
        $oIconDataFile = new DataFile();
        $oIconDataFile = $oIconDataFile->newDataFileByFilesystem($strIconName, $strWarehousePath, false, $oDataFile->getTitle(), $oDataFile->getDescription(), $oEntityType->getId());
        $oIconDataFile->setView("PUBLIC");
        $oIconDataFile->save();

        //for record keeping, ensure thumbnail table knows about data_file
        $oDataFileLink = DataFileLinkPeer::retrieveByPK($oDataFile->getId());

        if(ThumbnailPeer::exists($oDataFileLink->getProjectId(), 1)){
          ThumbnailPeer::updateThumbnail($oDataFile->getId(), $oDataFileLink->getProjectId(), 1);
        }else{
          $oThumbnail = new Thumbnail($oDataFile->getId(), $oDataFileLink->getProjectId(), 1);
          $oThumbnail->save();
        }

        //update the project's thumb_id
        $oDataFile->setThumbId($oIconDataFile->getId());
        $oDataFile->setView("PUBLIC");
        $oDataFile->save();
      }
    }

    if($bThumbnail || $bDisplay || $bMkDir || $bIcon){
      FileHelper::fixPermissions($strWarehousePath);
    }

    return true;
  }
 
}
?>
