<?php
require_once("lib/data/tsunami/dao/TsunamiBaseDAO.php");


class TsunamiDAOFactory{

  #
  #   $obj is the name of the object: Sensor, SensorModel, <others>
  #   $type is the DAO type - MySQL, <others>
  #
  #
  static function newDAO($obj,$type="MySQL"){

    switch ($obj) {

      case "TsunamiProject":
        require_once("lib/data/tsunami/dao/TsunamiProjectDAO.php");
        return new TsunamiProjectDAO();
        break;

      case "TsunamiSite":
        require_once("lib/data/tsunami/dao/TsunamiSiteDAO.php");
        return new TsunamiSiteDAO();
        break;

      case "TsunamiDocLib":
        require_once("lib/data/tsunami/dao/TsunamiDocLibDAO.php");
        return new TsunamiDocLibDAO();
        break;

      case "TsunamiEngineeringData":
        require_once("lib/data/tsunami/dao/TsunamiEngineeringDataDAO.php");
        return new TsunamiEngineeringDataDAO();
        break;

      case "TsunamiBiologicalData":
        require_once("lib/data/tsunami/dao/TsunamiBiologicalDataDAO.php");
        return new TsunamiBiologicalDataDAO();
        break;

      case "TsunamiGeologicalData":
        require_once("lib/data/tsunami/dao/TsunamiGeologicalDataDAO.php");
        return new TsunamiGeologicalDataDAO();
        break;

      case "TsunamiHydrodynamicData":
        require_once("lib/data/tsunami/dao/TsunamiHydrodynamicDataDAO.php");
        return new TsunamiHydrodynamicDataDAO();
        break;

      case "TsunamiSeismicData":
        require_once("lib/data/tsunami/dao/TsunamiSeismicDataDAO.php");
        return new TsunamiSeismicDataDAO();
        break;

      case "TsunamiSocialScienceData":
        require_once("lib/data/tsunami/dao/TsunamiSocialScienceDataDAO.php");
        return new TsunamiSocialScienceDataDAO();
        break;

      case "TsunamiSiteConfiguration":
        require_once("lib/data/tsunami/dao/TsunamiSiteConfigurationDAO.php");
        return new TsunamiSiteConfigurationDAO();
        break;

      default:
        return "No such DAO for the $obj object of type $type.";

    }
  }
}

#
#   Equipment Base Class
#
#
class Tsunami{

  protected $DAO=null;
  protected $Error;



  #
  #   paramsValid
  #
  protected function paramsValid(){

    $meta = $this->getMetadata();
    foreach($meta as $param => $p_a){

      #   skip the auto generated parameters
      if ($p_a["auto"]) continue;

      $value = $this->$param;

      if ($value != "" && $value != null ){

        #   Check if value has to be one of a list of members
        if(isset($p_a["values"])){
          if (!in_array($value, $p_a["values"])){
            return $this->setError("Valid '$param' values are limited to: ".implode(",",$p_a["values"]).".");
          }
        }

        #   See if this is an object reference and test for validity
        if (isset($p_a["ref"])){
          #   New appropriate DAO
          $refDAO = TsunamiDAOFactory::newDAO($p_a["ref"],$this->getDAO()->getDAOType());
          if (!$refDAO->isValidId($value)){
            return $this->setError("$value is not a valid ".$p_a["ref"]." id.");
          }
        }



      }else{

        #   Check if this parameter is required
        if ($p_a["required"]){
          return $this->setError("A $param value is required.");
        }

      }
    }
    return true;
  }

  #
  #   setFromArray
  #
  public function setFromArray($array){

    #   Check the argument
    if (!is_array($array)){
      return $this->setError("setFromArray requires an array argument.");
    }

    #   Get the list of parameter names
    $meta = $this->getMetadata();

    #   Make a "lower case parameter mapping"
    foreach (array_keys($meta) as $param){ $lcpm[strtolower($param)] = $param; }

    #   Make all the passed keys lower case;
    $array = array_change_key_case($array, CASE_LOWER);

    #   Step through the passed array
    foreach($array as $k => $v){
      if (isset($lcpm[$k])){
        $param = $lcpm[$k];
        $this->$param = $v;
      }else{

        #   Ignore this instead, but should we attach it to an error?
        #return $this->setError("$k is not a valid parameter.");

      }
    }
    return true;
  }

  #
  #   convertToArray
  #
  public function convertToArray($capitalize = 0){
    $meta = $this->getMetadata();
    $ret = array();
    foreach (array_keys($meta) as $param){
      if ($capitalize) {
        $ret[ucfirst($param)] = $this->$param;
      }else{
        $ret[$param] = $this->$param;
      }
    }
    return $ret;
  }

  #   DAO
  public function setDAO(&$dao){
    $this->DAO = $dao;
  }
  public function getDAO(){
    if (empty($this->DAO)){
      return $this->setError("No Data Access Object defined.");
    }
    return $this->DAO;
  }

  #   Error
  public function getError(){
    return $this->Error;
  }

  protected function setError($msg = null){
    if ($msg != null){
      $this->Error = $msg;
    }else $this->Error = "setError called with no message.";
    return false;
  }
}



?>
