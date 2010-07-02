<?php

require_once 'lib/data/om/BaseCalibration.php';
require_once 'lib/data/MeasurementUnit.php';


/**
 * Calibration
 *
 * Calibrations encapsulate behavior information taken
 * about a specific {@link Sensor} on a specific date {@see calibDate}
 *
 * @abstract Instruments are calibrated to give them a baseline record for knowing
 * possible sources and limits of errors for further analysis.
 * Equipment sites calibrate instruments. Good calibrations conform to
 * ISO standards. NEESinc was working to provide calibration procedures and standards.
 *
 * @package    lib.data
 * @uses Sensor
 *
 * @todo should use MeasurementUnits
 *
 */
class Calibration extends BaseCalibration {

  /**
   * Initializes internal state of Calibration object.
   */
  function __construct(Sensor $sensor = null,
                       $calibDate = null,
                       $calibrator = "",
                       $description = "",
                       $adjustments = null,
                       $minMeasuredValue = null,
                       $maxMeasuredValue = null,
                       $measuredValueUnits = "",
                       $sensitivity = null,
                       $sensitivityUnits = "",
                       $reference = null,
                       $referenceUnits = "",
                       $calibFactor = "",
                       $calibFactorUnits = "",
                       $deleted = 0 ){

    $this->setSensor($sensor);
    $this->setCalibDate($calibDate);
    $this->setCalibrator($calibrator);
    $this->setDescription($description);
    $this->setAdjustments($adjustments);
    $this->setMinMeasuredValue($minMeasuredValue);
    $this->setMaxMeasuredValue($maxMeasuredValue);
    $this->setMeasuredValueUnits($measuredValueUnits);
    $this->setSensitivity($sensitivity);
    $this->setSensitivityUnits($sensitivityUnits);
    $this->setReference($reference);
    $this->setReferenceUnits($referenceUnits);
    $this->setCalibFactor($calibFactor);
    $this->setCalibFactorUnits($calibFactorUnits);
    $this->setDeleted($deleted);
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $sensor = $this->getSensor();
    return $sensor->getRESTURI() . "/Calibration/{$this->getId()}";
  }



  public function getMeasuredRange(){
    $measRange = "";
    $minMeas = $this->getMinMeasuredValue();
    $maxMeas = $this->getMaxMeasuredValue();

    # get unit that may be text
    $unitId = $this->getMeasuredValueUnits();
    $unit = MeasurementUnitPeer::find($unitId);

    if ( $unit ) $units = " " . $unit->getAbbreviation();
    else ( $units = " " . $unitId );

    if ($minMeas !== null) $measRange = "from $minMeas";
    if ($maxMeas !== null) $measRange .= " to $maxMeas";
    if ($measRange !== "") $measRange .= $units;

    return $measRange;
  }


  public function getSensitivityWithUnit() {
    $sens = $this->getSensitivity();

    if ( ! is_null($sens)) {
      $unitId = $this->getSensitivityUnits();

      $unitStr = "";
      if(is_numeric($unitId)) {
        $unit = MeasurementUnitPeer::find($unitId);
        if ( $unit ) $unitStr = $unit->getAbbreviation();
      }
      else ( $unitStr = $unitId );

      $sens .= " " . $unitStr;
    }
    return $sens;
  }


  public function getReferenceWithUnit(){

    $ref = $this->getReference();

    if ( ! is_null($ref)) {
      $unitId = $this->getReferenceUnits();

      $unitStr = "";
      if(is_numeric($unitId)) {
        $unit = MeasurementUnitPeer::find($unitId);
        if ( $unit ) $unitStr = $unit->getAbbreviation();
      }
      else ( $unitStr = $unitId );

      $ref .= " " . $unitStr;
    }
    return $ref;
  }


  function getCalibFactorWithUnit(){

    $ref = $this->getCalibFactor();

    if ( ! is_null($ref)) {
      $unitId = $this->getCalibFactorUnits();

      $unitStr = "";
      if(is_numeric($unitId)) {
        $unit = MeasurementUnitPeer::find($unitId);
        if ( $unit ) $unitStr = $unit->getAbbreviation();
      }
      else ( $unitStr = $unitId );

      $ref .= " " . $unitStr;
    }
    return $ref;
  }


  /**
   * Return the Excel columns that supported for Export or Upload by excel spreadsheet
   *
   * @return array(String)
   */
  function getExcelColumnNames() {
    return array(
      1=>"Calibration ID",
      2=>"Sensor Name",
      3=>"Date (M[M]/D[D]/YYYY)",
      4=>"Person",
      5=>"Adjustments Value",
      6=>"Min. Measured Value",
      7=>"Max. Measured Value",
      8=>"Measured Value Unit",
      9=>"Sensitivity Value",
      10=>"Sensitivity Unit",
      11=>"Reference Value",
      12=>"Reference Unit",
      13=>"Calibration Factor",
      14=>"Calibration Factor Unit",
      15=>"Description");
  }


  /**
   * Each calibration is associated with a directory on disk.
   * This function returns the path of that directory for
   * this calibration.
   */
  public function getPathname() {
    return $this->getSensor()->getPathname() . "/calibrations/" . $this->getId();
  }

} // Calibration
?>
