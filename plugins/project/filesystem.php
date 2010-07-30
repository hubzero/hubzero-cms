<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Import library dependencies
jimport('joomla.event.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once 'api/org/nees/lib/filesystem/FileCommandAPI.php';
require_once 'api/org/nees/static/Files.php';

 
class plgProjectFileSystem extends JPlugin{
	
   /**
    * Constructor
    *
    * 
    */
  function plgProjectFileSystem( &$subject ){
    parent::__construct( $subject );
 
    // load plugin parameters
    $this->_plugin = JPluginHelper::getPlugin( 'project', 'filesystem' );
    $this->_params = new JParameter( $this->_plugin->params );
  }

  /**
   *
   * @global <type> $mainframe
   * @param array $params
   */
  function onMkDir(&$params){
    global $mainframe;

    $bIsProject = $_REQUEST[Files::WAREHOUSE];
    $strProjectName = "";
    $strNewAbsoluteArray = $_REQUEST[Files::ABSOLUTE_DIRECTORY_PATH_LIST];
    if($strNewAbsoluteArray){
      foreach($strNewAbsoluteArray as $strNewAbsolute){
        if($bIsProject){
          $strPattern = "/^(\/nees\/home)/";
          $bIsValidProjectDir = $this->isValidateProjectDirectory("/^(\/nees\/home)/", $strNewAbsolute, Files::PROJECT_NAME);
          if($bIsValidProjectDir){
            $strProjectName = $_REQUEST[Files::PROJECT_NAME];
          }else{
            echo "Invalid project directory - ".$strNewAbsolute;
            return;
          }
        }

        //remove the leading slash
        $strDirectoryArray = explode("/", $strNewAbsolute);
        if(strlen($strDirectoryArray[0])==0){
          unset ($strDirectoryArray[0]);
        }

        $strThisDirectory = "";
        foreach($strDirectoryArray as $iIndex=>$strThisSubFolder){ 
          $strThisDirectory .= "/".$strThisSubFolder;
          if(!is_dir($strThisDirectory)){
            //remove the final slash
            $oFileCommand = FileCommandAPI::create($strThisDirectory);
            $oFileCommand->mkdir();

            if(StringHelper::hasText($strProjectName)){
              $strProjectGroupCname = FileCommandAPI::getProjectDirectoryGroup($strProjectName);
              $escSrc = escapeshellarg($strProjectGroupCname);
              $escDest = escapeshellarg($strThisDirectory);
              exec("chgrp $escSrc $escDest", $output, $returncode);
            }//end chgrp
          }//end if is_dir
        }//end foreach 2
      }//end foreach 1
    }//end if $strNewAbsoluteArray
  }

  private function isValidateProjectDirectory($p_strPattern, $p_strAbsolutePath, $p_strProjectName){
    if(!preg_match($p_strPattern, $p_strAbsolutePath)){
      echo "pattern error <br>";
      return false;
    }

    if(!isset($_REQUEST[$p_strProjectName])){
      echo "request parameter not set <br>";
      return false;
    }

    return true;
  }
  
  
 
}
?>
