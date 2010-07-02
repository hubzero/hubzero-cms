<?php

require_once 'lib/data/om/BaseLocation.php';
require_once 'lib/util/MeasurementUnitsManager.php';
require_once 'lib/util/Matrix.php';

/**
 * Location
 *
 * Locations are pairs of 3-tuples: (x,y,z) location and (i,j,k) orientation
 * within a given {@link CoordinateSpace}
 *
 * Location specifies the location (x,y,z) and orientation (i,j,k)
 * of an object within a LocationPlan.
 *
 * each (x,y,z) and (i,j,k) has an associated {@link MeasurementUnit}
 *
 * DAQ Channels refer to a particular Location.
 *
 * -----------------------------
 *
 * CoordinateSpace, Reference to the coordinate space
 * used for orientation and coordinates
 *
 * label == String, User-understanable label for this location
 *
 * (x,y,z) -- Floats, Coordinates of this location
 *
 * (i, j, k) -- Floats, Orientation of the sensor
 *
 * comment -- Notes.
 *
 * ------------------------------
 * @package    lib.data
 *
 * @uses MeasurementUnit
 * @uses CoordinateSpace
 */
class Location extends BaseLocation {

  /**
   * Initializes internal state of Location object.
   */
  public function __construct()
  {
    $this->setLocationTypeId(LocationPeer::CLASSKEY_LOCATION);
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/Location/{$this->getId()}";
  }


  public function getGlobalCoordinates() {
    $v = $this->getTransformationToCS()->apply($this->getX(), $this->getY(), $this->getZ());
    return $this->getCoordinateSpace()->localToGlobal($v[0],$v[1],$v[2]);
  }


  /**
   * Make sure we return a normalized vector.
   *
   * @return array[I,J,K]
   */
  public function getCorrectedOrientation() {

    $returnUnits = array( $this->getI(), $this->getJ(), $this->getK() );

    if(!$this->isNormal()) {
      $returnUnits = $this->normalize($returnUnits);
    }
    return $returnUnits;
  }

  /**
   * Check if Orientation array is Normal
   *
   * @return boolean
   */
  private function isNormal() {
    return Location::getMagnitude( array($this->getI(), $this->getJ(), $this->getK()) ) == 1;
  }


  /**
   * Get the Magnitude of array
   *
   * @param array[I,J,K] $v
   * @return int
   */
  public static function getMagnitude($v) {
    return sqrt( $v[0]*$v[0] + $v[1]*$v[1] + $v[2]*$v[2] );
  }


  /**
   * Normalize an array
   *
   * @param array[I1,J1,K1] $v
   * @return array[I2,J2,K2]
   */
  public static function normalize($v) {
    if($v[0] == "" && $v[1] == "" && $v[2] == "") return array("","","");

    $magnitude = Location::getMagnitude($v);
    if($magnitude != 0) {
      return array($v[0]/$magnitude, $v[1]/$magnitude, $v[2]/$magnitude);
    }
    else {
      return array(0, 0, 0);
    }
  }

  /**
   * Return a matrix that is the transformation from the "location coordinate space"
   * to the Location's coordinate space...
   * There's an extra coordinate-space in here that's not a CoordinateSpace:
   *   units are scaled from the CS units to the Location units
   *   i,j,k imply the rotations from the CS orientation to the Location orientation
   *
   * we'll return the matrix of the transformation from location to location in CS
   * for use by the CoordinateSpace's getTransformationTo/FromDefault
   */
  public function getTransformationToCS() {
    $mum = new MeasurementUnitsManager();
    $m = new Matrix();

    $iUnit = $this->getMeasurementUnitRelatedByIUnit();
    $jUnit = $this->getMeasurementUnitRelatedByJUnit();
    $kUnit = $this->getMeasurementUnitRelatedByKUnit();

    $xs = $mum->getScalingFromUnitToUnit($this->getMeasurementUnitRelatedByXUnit(), $this->getCoordinateSpace()->getTranslationUnitX());
    $ys = $mum->getScalingFromUnitToUnit($this->getMeasurementUnitRelatedByYUnit(), $this->getCoordinateSpace()->getTranslationUnitY());
    $zs = $mum->getScalingFromUnitToUnit($this->getMeasurementUnitRelatedByZUnit(), $this->getCoordinateSpace()->getTranslationUnitZ());

    $m->scl($xs,$ys,$zs);

    $ir = $mum->convertFromUnitToUnit($this->getI(),$iUnit, $this->getCoordinateSpace()->getRotationUnitX());
    $jr = $mum->convertFromUnitToUnit($this->getJ(),$jUnit, $this->getCoordinateSpace()->getRotationUnitY());
    $kr = $mum->convertFromUnitToUnit($this->getK(),$kUnit, $this->getCoordinateSpace()->getRotationUnitZ());

    //      $m->rot($ir,$jr,$kr);
    $m->rot(0,0,0);
    return $m;
  }

  public function getTransformationFromCS() {
    $mum = new MeasurementUnitsManager();
    $m = new Matrix();

    $ir = $mum->convertFromUnitToUnit($this->getI(),$this->getMeasurementUnitRelatedByIUnit(), $this->getCoordinateSpace()->getRotationUnitX());
    $jr = $mum->convertFromUnitToUnit($this->getJ(),$this->getMeasurementUnitRelatedByJUnit(), $this->getCoordinateSpace()->getRotationUnitY());
    $kr = $mum->convertFromUnitToUnit($this->getK(),$this->getMeasurementUnitRelatedByKUnit(), $this->getCoordinateSpace()->getRotationUnitZ());

    $m->rot(-$kr,-$jr,-$ir);

    $xs = $mum->getScalingFromUnitToUnit($this->getMeasurementUnitRelatedByXUnit(), $this->getCoordinateSpace()->getTranslationUnitX());
    $ys = $mum->getScalingFromUnitToUnit($this->getMeasurementUnitRelatedByYUnit(), $this->getCoordinateSpace()->getTranslationUnitY());
    $zs = $mum->getScalingFromUnitToUnit($this->getMeasurementUnitRelatedByZUnit(), $this->getCoordinateSpace()->getTranslationUnitZ());

    $m->scl(1.0/$xs,1.0/$ys,1.0/$zs);

    return $m;
  }

} // Location
?>
