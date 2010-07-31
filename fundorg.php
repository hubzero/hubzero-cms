<?php

set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/nees" . PATH_SEPARATOR . get_include_path());

//spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("/www/neeshub/api/org/phpdb/propel/central/conf/central-conf.php");

require_once '/www/neeshub/api/org/nees/static/Files.php';
require_once '/www/neeshub/api/org/nees/util/StringHelper.php';
require_once '/www/neeshub/api/org/nees/util/PhotoHelper.php';
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/ProjectGrant.php';


$oProjectArray = ProjectPeer::findByFundOrg();
echo "# projects = ".sizeof($oProjectArray)."\n";

/* @var $oProject Project */
foreach($oProjectArray as $i=>$oProject){
  $iProjectId = $oProject->getId();
  $strAgency = $oProject->getFundorg();
  $strAward = $oProject->getFundorgProjId();

  $strAwardArray = array();
  
  if(strtoupper($strAgency)=="NSF"){
    $strAgency = strtoupper($strAgency);
    $strUrl = "http://www.nsf.gov/awardsearch/showAward.do?AwardNumber=";

    $strAwardArray = explode(",", $strAward);
    if(sizeof($strAwardArray) > 1){
      foreach($strAwardArray as $strThisAward){
        $strThisAward = trim($strThisAward);
        echo "COMMA $i) proj=".$iProjectId.", sponsor=".$strAgency.", award=".$strThisAward."\n";
        if(is_numeric($strThisAward)){
          $oProjectGrant = new ProjectGrant($strAgency, $strThisAward, $strUrl.$strThisAward, ProjectPeer::retrieveByPK($iProjectId));
          $oProjectGrant->save();
        }else{
          $oProjectGrant = new ProjectGrant($strAgency, $strThisAward, null, ProjectPeer::retrieveByPK($iProjectId));
          $oProjectGrant->save();
        }
      }
    }else{
      $strAwardArray = array();
    }

    if(empty($strAwardArray)){
      $strAwardArray = explode("#", $strAward);
      if(sizeof($strAwardArray) != 2){
        $strAwardArray = array();
      }else{
        $strThisAward = trim($strAwardArray[1]);
        echo "POUND $i) proj=".$iProjectId.", sponsor=".$strAgency.", award=".$strThisAward."\n";
        if(is_numeric($strThisAward)){
          $oProjectGrant = new ProjectGrant($strAgency, $strThisAward, $strUrl.$strThisAward, ProjectPeer::retrieveByPK($iProjectId));
          $oProjectGrant->save();
        }else{
          $oProjectGrant = new ProjectGrant($strAgency, $strThisAward, null, ProjectPeer::retrieveByPK($iProjectId));
          $oProjectGrant->save();
        }
      }

    }

    if(empty($strAwardArray)){
      $strAwardArray = explode("-", $strAward);
      if(sizeof($strAwardArray) != 2){
        $strAwardArray = array();
      }else{
        $strThisAward = trim($strAwardArray[1]);
        if(strlen($strThisAward) > 4){
          echo "DASH $i) proj=".$iProjectId.", sponsor=".$strAgency.", award=".$strThisAward."\n";
          if(is_numeric($strThisAward)){
            $oProjectGrant = new ProjectGrant($strAgency, $strThisAward, $strUrl.$strThisAward, ProjectPeer::retrieveByPK($iProjectId));
            $oProjectGrant->save();
          }else{
            $oProjectGrant = new ProjectGrant($strAgency, $strThisAward, null, ProjectPeer::retrieveByPK($iProjectId));
            $oProjectGrant->save();
          }
        }else{
          $strAwardArray = array();
        }
      }
    }

    if(empty($strAwardArray)){
      $strAwardArray = explode(" ", $strAward);
      if(sizeof($strAwardArray) != 2){
        $strAwardArray = array();
      }else{
        $strThisAward = trim($strAwardArray[1]);
        if(strlen($strThisAward) > 4){
          echo "SPACE $i) proj=".$iProjectId.", sponsor=".$strAgency.", award=".$strThisAward."\n";
          if(is_numeric($strThisAward)){
            $oProjectGrant = new ProjectGrant($strAgency, $strThisAward, $strUrl.$strThisAward, ProjectPeer::retrieveByPK($iProjectId));
            $oProjectGrant->save();
          }else{
            $oProjectGrant = new ProjectGrant($strAgency, $strThisAward, null, ProjectPeer::retrieveByPK($iProjectId));
            $oProjectGrant->save();
          }
        }else{
          $strAwardArray = array();
        }
      }
    }

    //insert as-is
    if(empty($strAwardArray)){
      $strThisAward = trim($strAward);
      echo "AS-IS $i) proj=".$iProjectId.", sponsor=".$strAgency.", award=".$strThisAward."\n";
      if(is_numeric($strThisAward)){
        $oProjectGrant = new ProjectGrant($strAgency, $strThisAward, $strUrl.$strThisAward, ProjectPeer::retrieveByPK($iProjectId));
        $oProjectGrant->save();
      }else{
        $oProjectGrant = new ProjectGrant($strAgency, $strThisAward, null, ProjectPeer::retrieveByPK($iProjectId));
        $oProjectGrant->save();
      }
    }

  }else{
    $strThisAward = trim($strAward);
    echo "NOT $i) proj=".$iProjectId.", sponsor=".$strAgency.", award=".$strThisAward."\n";
    $oProjectGrant = new ProjectGrant($strAgency, $strThisAward, null, ProjectPeer::retrieveByPK($iProjectId));
    $oProjectGrant->save();
  }

}

?>
