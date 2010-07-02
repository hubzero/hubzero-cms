<?php

require_once 'lib/data/Location.php';
require_once 'lib/data/om/BaseLocation.php';


/**
 * SourceLocation
 *
 * SourceLocation specifies the location (x,y,z) and orientation (i,j,k)
 * of a source within a SourceLocationPlan.
 * DAQ Channels refer to a particular SourceLocation.
 *
 * @package    lib.data
 *
 * @uses SourceLocationPlan
 * @uses SourceType
 *
 */
class SourceLocation extends Location {

  /**
   * Constructs a new SourceLocation object, setting the
   * location_type_id column to LocationPeer::CLASSKEY_2.
   */
  public function __construct( SourceLocationPlan $sourceLocationPlan = null,
                               SourceType $sourceType = null, //SourceType::UNKNOWN,
                               $label = "",
                               $x = 0.0,
                               $y = 0.0,
                               $z = 0.0,
                               $i = 0.0,
                               $j = 0.0,
                               $k = 0.0,
                               CoordinateSpace $coordinateSpace = null,
                               $comment = "",
                               $xUnit = null,
                               $yUnit = null,
                               $zUnit = null,
                               $iUnit = null,
                               $jUnit = null,
                               $kUnit = null)
  {
    parent::__construct();

    $this->setLocationTypeId(LocationPeer::CLASSKEY_SOURCELOCATION);
    $this->setLocationPlan($sourceLocationPlan);
    $this->setSourceType($sourceType);
    $this->setLabel($label);
    $this->setX($x);
    $this->setY($y);
    $this->setZ($z);
    $this->setI($i);
    $this->setJ($j);
    $this->setK($k);
    $this->setCoordinateSpace($coordinateSpace);
    $this->setComment($comment);
    $this->setXUnit($xUnit);
    $this->setYUnit($yUnit);
    $this->setZUnit($zUnit);
    $this->setIUnit($iUnit);
    $this->setJUnit($jUnit);
    $this->setKUnit($kUnit);
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  public function getRESTURI() {
    $slp = $this->getSourceLocationPlan();
    return $slp->getRESTURI() . "/SourceLocation/{$this->getId()}";
  }


  /**
   * Wrap {@link Location::setLocationPlan()} to match DM API
   *
   * @param SourceLocationPlan $slp
   */
  public function setSourceLocationPlan(SourceLocationPlan $slp) {
     $this->setLocationPlan($slp);
  }


  /**
   * Get the SourceLocationPlan for this SourceLocation
   * Backward compatible with NEEScentral 1.7
   *
   * @return SourceLocationPlan
   */
  public function getSourceLocationPlan() {
    return $this->getLocationPlan();
  }





} // SourceLocation
?>
