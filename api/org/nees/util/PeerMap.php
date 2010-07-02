<?php

/**
 * Get Peer Class from string of Peer Class Name
 *
 */

class PeerMap {

  /**
   * Get the Peer Class given by a PHP Object Name
   *
   * @param String $objName
   * @param String $application: "", "curation", "tsunami"
   * @return Peer Class
   */
  public static function getPropelObject( $objName, $application = null) {

    $application_dir = empty($application) ? "" : $application . "/";

    if (@include_once("lib/data/" . $application_dir . $objName . ".php")) {
      if ( class_exists( $objName ) ) {
        return new $objName();
      }
    }

    return null;
  }


  /**
   * Get the Peer Class given by a PHP Object Name
   *
   * @param String $objName
   * @param String $application: "", "curation", "tsunami"
   * @return Peer Class
   */
  public static function getPeer( $objName, $application = null) {

    $peer_class = "{$objName}Peer";
    $application_dir = empty($application) ? "" : $application . "/";

    if (@include_once("lib/data/" . $application_dir . $peer_class . ".php")) {
      if ( class_exists( $peer_class ) ) {
        return new $peer_class();
      }
    }

    return null;
  }


  public static function getAutoIncreamentKeyArray($objName, $application = null) {

    $application_dir = empty($application) ? "" : $application . "/";
    $peer_class = self::getPeer($objName, $application);

    if ( is_null($peer_class)) return null;

    $tableMap = $peer_class->getTableMap();

    if( $tableMap->isUseIdGenerator()) {
      $seq_pk = $tableMap->getPrimaryKeyMethodInfo();
      $columnMaps = $tableMap->getColumns();

      foreach ($columnMaps as $columnMap) {
        if ($columnMap->isPrimaryKey()) {
          $pk = $columnMap->getColumnName();

          return array($pk, $seq_pk);
        }
      }
    }

    return null;
  }


  public function insert($table_name, $column_names, $column_values) {
    $propel_class = findPropelClass($table_name);
    $propel_object = new $propel_class();

    foreach ($column_names as $k => $v) {
      $propel_object->setByName($v, $column_values[$k], BasePeer::TYPE_COLNAME);
    }

    $propel_object->save();
  }


}

?>

