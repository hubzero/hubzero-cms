<?php

class TsunamiPropel{

  #
  #   $obj is the name of the object: Sensor, SensorModel, <others>
  #   $type is the DAO type - MySQL, <others>
  #
  #
  static function newTsunamiObject($obj){

    switch ($obj) {

      case "TsunamiBiologicalData":
        require_once("lib/data/tsunami/TsunamiBiologicalData.php");
        return new TsunamiBiologicalData();
        break;

      case "TsunamiDocLib":
        require_once("lib/data/tsunami/TsunamiDocLib.php");
        return new TsunamiDocLib();
        break;

      case "TsunamiEngineeringData":
        require_once("lib/data/tsunami/TsunamiEngineeringData.php");
        return new TsunamiEngineeringData();
        break;

      case "TsunamiGeologicalData":
        require_once("lib/data/tsunami/TsunamiGeologicalData.php");
        return new TsunamiGeologicalData();
        break;

      case "TsunamiHydrodynamicData":
        require_once("lib/data/tsunami/TsunamiHydrodynamicData.php");
        return new TsunamiHydrodynamicData();
        break;

        case "TsunamiProject":
        require_once("lib/data/tsunami/TsunamiProject.php");
        return new TsunamiProject();
        break;

      case "TsunamiSeismicData":
        require_once("lib/data/tsunami/TsunamiSeismicData.php");
        return new TsunamiSeismicData();
        break;

      case "TsunamiSite":
        require_once("lib/data/tsunami/TsunamiSite.php");
        return new TsunamiSite();
        break;

        case "TsunamiSiteConfiguration":
        require_once("lib/data/tsunami/TsunamiSiteConfiguration.php");
        return new TsunamiSiteConfiguration();
        break;

      case "TsunamiSocialScienceData":
        require_once("lib/data/tsunami/TsunamiSocialScienceData.php");
        return new TsunamiSocialScienceData();
        break;

      default:
        return "No such Propel Object for the $obj object";
    }
  }

  /**
   * Get the Peer Class from an String Object name
   *
   * @param String $obj
   * @return Peer_Class
   */
  static function getPeer($obj) {
    $propel = self::newTsunamiObject($obj);
    return $propel->getPeer();
  }

}


?>
