<?php

require_once 'lib/data/om/BaseCoordinateSystem.php';


/**
 * CoordinateSystem
 *
 * A coordinate system is a named collection of {@link CoordinateDimension}
 * that establishes the type of a {@link CoordinateSpace}, e.g. a Cartesian
 * space might have dimensions measured in units of length
 *
 * Most experiments use multiple coordinate spaces mostly Cartesian...
 * @package    lib.data
 * @uses CoordinateDimension
 *
 */

class CoordinateSystem extends BaseCoordinateSystem {

  /**
   * Initializes internal state of CoordinateSystem object.
   */
  public function __construct($name='',
                              CoordinateDimension $dim1=null,
                              CoordinateDimension $dim2=null,
                              CoordinateDimension $dim3=null ) {
    $this->setName($name);
    $this->setDimension1($dim1);
    $this->setDimension2($dim2);
    $this->setDimension3($dim3);
  }

  /**
   * Get nth-dimension of this CoordinateSystem
   *
   * @return CoordinateDimension the nth-coordinate dimension
   * @deprecated
   */
  public function getDimension($idx) {
    if (($idx < 0) || ($idx > 2)) {
      exit("index $idx must be in the range [0,2]");
    }

    $j = $idx+1;
    $getter = "getDimension" . $j;
    return $this->$getter();
  }

  /**
   * Set the n-th dimension of this CoordinateSystem
   *
   * @param integer $idx index of the dimension to set
   * @param CoordinateDimension $value
   */
  public function setDimension($idx, CoordinateDimension $value = null) {
    if (($idx < 0) || ($idx > 2)) {
      exit("index $idx must be in the range [0,2]");
    }

    $j = $idx + 1;
    $setter = "setDimension" . $j;
    $this->$setter($value);
  }

} // CoordinateSystem
?>
