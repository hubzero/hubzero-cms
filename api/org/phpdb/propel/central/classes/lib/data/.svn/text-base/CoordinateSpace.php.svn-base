<?php

require_once 'lib/data/om/BaseCoordinateSpace.php';
require_once 'lib/data/CoordinateSystem.php';
require_once 'lib/data/MeasurementUnit.php';
require_once 'lib/util/MeasurementUnitsManager.php';
require_once 'lib/util/Matrix.php';

/**
 * CoordinateSpace
 *
 * Encapsulates the coordinate space used for a given
 * experimental model
 *
 * Allows the user to define reference bases for measurements
 * (establishes a reference point) of the experiment/specimin.
 * Arranged hierarchically.
 *
 * @package    lib.data
 * @uses CoordinateSystem
 * @uses Experiment
 * @uses CoordinateSpaceDataFile
 * @uses MeasurementUnit
 */
class CoordinateSpace extends BaseCoordinateSpace {

  /**
   * Initializes internal state of ControllerChannel object.
   */
  public function __construct($name = '',
                              CoordinateSpace $parentSpace=null,
                              CoordinateSystem $system = null,
                              $translation = array(0, 0, 0),
                              $rotation = array(0, 0, 0),
                              $scale=1.0,
                              Experiment $experiment = null,
                              $translationUnits = array(null, null, null),
                              $rotationUnits = array(null, null, null),
                              $dataFiles = null,   // Array <CoordinateSpaceDataFile>
                              $description="",
                              $dateCreated=null) {

    $this->setName($name);
    $this->setCoordinateSpaceRelatedByParentId($parentSpace);
    $this->setCoordinateSystem($system);
    $this->setTranslation($translation);
    $this->setRotation($rotation);
    $this->setScale($scale);
    $this->setExperiment($experiment);
    $this->setTranslationUnits($translationUnits);
    $this->setRotationUnits($rotationUnits);
    $this->setDataFiles($dataFiles);
    $this->setDescription($description);
    $this->setDateCreated($dateCreated);
  }

  /**
   * Determine if this is the default coordinate space
   * The default coordinate space has no parent
   *
   * @return boolean true if this is the default coordinate
   *  space, else false
   */
  public function isDefaultCoordinateSpace() {
    return is_null($this->getParentSpace());
  }

  /**
   * Wrap {@link BaseCoordinateSpace::getCoordinateSpaceRelatedByParentId}
   * to match domain/CoordinateSpace getParentSpace interface
   *
   * @return CoordinateSpace the parent space
   */
  public function getParentSpace() {
    return $this->getCoordinateSpaceRelatedByParentId();
  }

  /**
   * Wrap {@link BaseCoordinateSpace::addCoordinateSpaceRelatedByParentId}
   * to match domain/CoordinateSpace setParentSpace interface
   *
   * @param CoordinateSpace: The parent space
   */
  public function setParentSpace($parent) {
    $this->setCoordinateSpaceRelatedByParentId($parent);
  }

  /**
   * Create the default global CoordinateSpace
   *
   * @param Experiment $experiment
   * @return boolean
   */
  public static function createDefaultCoordSpace( Experiment $experiment ) {
    $cstype = CoordinateSystemPeer::findByName('Cartesian');
    if( !$cstype ) {
      return false;
    }

    $angle  = MeasurementUnitPeer::findByName('radian');
    if( !$angle) {
      return false;
    }

    $length = MeasurementUnitPeer::findByName('meter');

    if( !$length ) {
      return false;
    }

    $cs = new CoordinateSpace('Global Coordinate Space', null, $cstype);

    $cs->setDescription("The global coordinate space relative to which all other coordinate spaces are defined.");

    $cs->setScale(1);
    $cs->setRotation(array(0,0,0));
    $cs->setTranslation(array(0,0,0));

    $cs->setTranslationUnits(array($length, $length, $length));
    $cs->setRotationUnits(array($angle, $angle, $angle));

    $cs->setExperiment($experiment);
    $cs->save();

    return true;
  }

  /**
   * Wrap {@link BaseCoordinateSpace::getCoordinateSystem} for
   * interface compatibility
   *
   * @return CoordinateSystem
   */
  public function getSystem() {
    return $this->getCoordinateSystem();
  }


  /**
   * Wrap {@link BaseCoordinateSpace::setCoordinateSystem} for
   * interface compatibility
   *
   * @param CoordinateSystem
   */
  public function setSystem(CoordinateSystem $coordinateSystem) {
    return $this->setCoordinateSystem($coordinateSystem);
  }


  /**
   * Get array of CoordinateSpaceDataFiles
   * Backward compatible to NEEScentral 1.7
   * use: getCoordinateSpaceDataFiles() instead
   *
   * @return array <CoordinateSpaceDataFile>
   */
  public function getDataFiles() {
    return $this->getCoordinateSpaceDataFiles();
  }

  /**
   * set the CoordinateSpaceDataFiles for this CoordinateSpace
   * Backward compatible to NEEScentral 1.7
   *
   * @param Array <CoordinateSpaceDataFile>
   */
  public function setDataFiles($files) {
    // variable collCoordinateSpaceDataFiles was defined in BaseCoordinateSpace class
    $this->collCoordinateSpaceDataFiles = $files;
  }


  public function getAltitudeUnit() {
    return $this->getMeasurementUnitRelatedByAltitudeUnitId();
  }

  public function setAltitudeUnit($m) {
    $this->setMeasurementUnitRelatedByAltitudeUnitId($m);
  }


  /**
   * Get the MeasurementUnit Related By TranslationXUnitId
   *
   * @return MeasurementUnit
   */
  public function getTranslationUnitX() {
    return $this->getMeasurementUnitRelatedByTranslationXUnitId();
  }


  /**
   * Set the MeasurementUnit Related By TranslationXUnitId
   *
   * @param MeasurementUnit
   */
  public function setTranslationUnitX($m) {
    $this->setMeasurementUnitRelatedByTranslationXUnitId($m);
  }


  /**
   * Get the MeasurementUnit Related By TranslationYUnitId
   *
   * @return MeasurementUnit
   */
  public function getTranslationUnitY() {
    return $this->getMeasurementUnitRelatedByTranslationYUnitId();
  }


  /**
   * Set the MeasurementUnit Related By TranslationYUnitId
   *
   * @param MeasurementUnit
   */
  public function setTranslationUnitY($m) {
    $this->setMeasurementUnitRelatedByTranslationYUnitId($m);
  }


  /**
   * Get the MeasurementUnit Related By TranslationZUnitId
   *
   * @return MeasurementUnit
   */
  public function getTranslationUnitZ() {
    return $this->getMeasurementUnitRelatedByTranslationZUnitId();
  }


  /**
   * Set the MeasurementUnit Related By TranslationZUnitId
   *
   * @param MeasurementUnit
   */
  public function setTranslationUnitZ($m) {
    $this->setMeasurementUnitRelatedByTranslationZUnitId($m);
  }


  /**
   * Get the MeasurementUnit Related By RotationXUnitId
   *
   * @return MeasurementUnit
   */
  public function getRotationUnitX() {
    return $this-> getMeasurementUnitRelatedByRotationXUnitId();
  }


  /**
   * Set the MeasurementUnit Related By RotationXUnitId
   *
   * @param MeasurementUnit
   */
  public function setRotationUnitX($m) {
    return $this-> setMeasurementUnitRelatedByRotationXUnitId($m);
  }


  /**
   * Get the MeasurementUnit Related By RotationYUnitId
   *
   * @return MeasurementUnit
   */
  public function getRotationUnitY() {
    return $this-> getMeasurementUnitRelatedByRotationYUnitId();
  }


  /**
   * Set the MeasurementUnit Related By RotationYUnitId
   *
   * @param MeasurementUnit
   */
  public function setRotationUnitY($m) {
    $this-> setMeasurementUnitRelatedByRotationYUnitId($m);
  }


  /**
   * Get the MeasurementUnit Related By RotationZUnitId
   *
   * @return MeasurementUnit
   */
  public function getRotationUnitZ() {
    return $this-> getMeasurementUnitRelatedByRotationZUnitId();
  }


  /**
   * Set the MeasurementUnit Related By RotationZUnitId
   *
   * @param MeasurementUnit
   */
  public function setRotationUnitZ($m) {
    $this-> setMeasurementUnitRelatedByRotationZUnitId($m);
  }



  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $exp = $this->getExperiment();
    return $exp->getRESTURI() . "/CoordinateSpace/{$this->getId()}";
  }

  //
  // Transformation Support: {@link lib.util.Matrix}
  //



  /**
   * Convenience method for accessing translations as a 3-element array
   *
   * @returns array [x,y,z]
   */
  public function getTranslation() {
    return array($this->getTranslationX(), $this->getTranslationY(), $this->getTranslationZ());
  }


  /**
   * Convenience method for setting translations from a 3-element array
   *
   * @param array $translation [x,y,z]
   */
  public function setTranslation($translation) {
    $this->setTranslationX($translation[0]);
    $this->setTranslationY($translation[1]);
    $this->setTranslationZ($translation[2]);
  }

  /**
   * Convenience method for accessing translation MeasurementUnits as
   * a 3-element array
   *
   * @return array [x-unit, y-unit, z-unit]
   */
  public function getTranslationUnits() {
    return array($this->getTranslationUnitX(), $this->getTranslationUnitY(), $this->getTranslationUnitZ());
  }

  /**
   * Convenience method for setting translation MeasurementUnit
   * from a 3-element array
   *
   * @param array<MeasurementUnit> $units [x-unit, y-unit, z-unit]
   */
  public function setTranslationUnits($units) {
    $this->setTranslationUnitX($units[0]);
    $this->setTranslationUnitY($units[1]);
    $this->setTranslationUnitZ($units[2]);
  }

  /**
   * Convenience method for accessing rotation as a 3-element array
   *
   * @return array<float> [x-rotation,y-rotation,z-rotation]
   */
  public function getRotation() {
    return array($this->getRotationX(), $this->getRotationY(), $this->getRotationZ());
  }

  /**
   * Convenience method for setting rotation from a 3-element array
   *
   * @param array<float> [x-rotation, y-rotation, z-rotation]
   */
  public function setRotation($rotation) {
    $this->setRotationX($rotation[0]);
    $this->setRotationY($rotation[1]);
    $this->setRotationZ($rotation[2]);
  }


  /**
   * Convenience method for accessing rotationUnit as a 3-element array
   *
   * @return 3-element array<MeasurementUnit x-rotation, MeasurementUnit y-rotation, MeasurementUnit z-rotation>
   */
  public function getRotationUnits() {
    return array(
      $this->getMeasurementUnitRelatedByRotationXUnitId(),
      $this->getMeasurementUnitRelatedByRotationYUnitId(),
      $this->getMeasurementUnitRelatedByRotationZUnitId()
    );
  }


  /**
   * Convenience method for setting rotationUnit as a 3-element array
   *
   * @param 3-element array<MeasurementUnit x-rotation, MeasurementUnit y-rotation, MeasurementUnit z-rotation>
   */
  public function setRotationUnits($units) {
    $this->setMeasurementUnitRelatedByRotationXUnitId($units[0]);
    $this->setMeasurementUnitRelatedByRotationYUnitId($units[1]);
    $this->setMeasurementUnitRelatedByRotationZUnitId($units[2]);
  }


  function getDefaultCoordinateSpace() {
    if ($this->isDefaultCoordinateSpace()) return $this;
    return $this->getParentSpace()->getDefaultCoordinateSpace();
  }


  /**
   * get the Transformation to Default CoordinateSpace
   *
   * @return lib.util.Matrix
   */
  public function getTransformationToDefault() {
    if ($this->isDefaultCoordinateSpace())
      return new Matrix();

    $m = $this->getParentSpace()->getTransformationToDefault();

    $mum = new MeasurementUnitsManager();

    $m->trn($this->getTranslationX(), $this->getTranslationY(), $this->getTranslationZ());

    $xr = $mum->convertFromUnitToUnit($this->getRotationX(), $this->getRotationUnitX(), $this->getParentSpace()->getRotationUnitX());
    $yr = $mum->convertFromUnitToUnit($this->getRotationY(), $this->getRotationUnitY(), $this->getParentSpace()->getRotationUnitY());
    $zr = $mum->convertFromUnitToUnit($this->getRotationZ(), $this->getRotationUnitZ(), $this->getParentSpace()->getRotationUnitZ());

    $m->rot($xr,$yr,$zr);

    // scale units to parent units
    $scale = $this->getScale();
    $xs = $scale * $mum->getScalingFromUnitToUnit($this->getTranslationUnitX(), $this->getParentSpace()->getTranslationUnitX());
    $ys = $scale * $mum->getScalingFromUnitToUnit($this->getTranslationUnitY(), $this->getParentSpace()->getTranslationUnitY());
    $zs = $scale * $mum->getScalingFromUnitToUnit($this->getTranslationUnitZ(), $this->getParentSpace()->getTranslationUnitZ());

    $m->scl($xs,$ys,$zs);


    return $m;
  }

  /**
   * get the Transformation from Default CoordinateSpace
   *
   * @return lib.util.Matrix
   */
  public function getTransformationFromDefault() {
    if ($this->isDefaultCoordinateSpace())
      return new Matrix();

    $m = $this->getParentSpace()->getTransformationFromDefault();

    $mum = new MeasurementUnitsManager();

    $scale = $this->getScale();
    $xs = $scale * $mum->getScalingFromUnitToUnit($this->getTranslationUnitX(), $this->getParentSpace()->getTranslationUnitX());
    $ys = $scale * $mum->getScalingFromUnitToUnit($this->getTranslationUnitY(), $this->getParentSpace()->getTranslationUnitY());
    $zs = $scale * $mum->getScalingFromUnitToUnit($this->getTranslationUnitZ(), $this->getParentSpace()->getTranslationUnitZ());

    $m->scl(1.0/$xs,1.0/$ys,1.0/$zs);

    $xr = $mum->convertFromUnitToUnit($this->getRotationX(), $this->getParentSpace()->getRotationUnitX(), $this->getRotationUnitX());
    $yr = $mum->convertFromUnitToUnit($this->getRotationY(), $this->getParentSpace()->getRotationUnitY(), $this->getRotationUnitY());
    $zr = $mum->convertFromUnitToUnit($this->getRotationZ(), $this->getParentSpace()->getRotationUnitZ(), $this->getRotationUnitZ());

    $m->rot(-$xr,-$yr,-$zr);

    $m->trn(- $this->getTranslationX(), - $this->getTranslationY(), - $this->getTranslationZ());

    return $m;

  }

  /**
   *  globalToLocal
   *  @return a list (x,y,z) transformed from global coords
   *          to the local coord space
   */
  public function globalToLocal($x,$y,$z) {
    $t = $this->getTransformationFromDefault();
    return $t->apply($x,$y,$z);
  }

  /**
   *  localToGlobal
   *  @return a list (x,y,z) transformed from local coords
   *          to global coords
   */
  public function localToGlobal($x,$y,$z) {
    return $this->getTransformationToDefault()->apply($x,$y,$z);
  }


  /**
   * Each CoordinateSpace is associated with a directory on disk.
   * This function returns the path of that directory for
   * this coordinatespace.
   */
  public function getPathname() {
    return $this->getExperiment()->getPathname() . '/CoordinateSpace' . $this->getId();
  }

} // CoordinateSpace
?>
