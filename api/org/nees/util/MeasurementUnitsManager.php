<?php
/**
 * @abstract UnitsManager buisiness logic object. Provides variety of methods for easily managing units and conversions.
 **
 * @author GPC
 *
 */

require_once 'lib/data/MeasurementUnit.php';
require_once 'lib/data/MeasurementUnitCategory.php';
require_once 'lib/data/MeasurementUnitConversion.php';

class MeasurementUnitsManager {

  private $conversion;
  private $k0;
  private $k1;

  public function __construct() {
  }

  /**
 * Helper method for calculating conversion coefficients
 * The coefficients are calculated using two passed points (x1,y1) and (x2,y2)
 * Coefficients are passed as variable parameters.
 *
 * @param double $x1
 * @param double $y1
 * @param double $x2
 * @param double $y2
 * @param double $k0
 * @param double $k1
 * @access private
 *
 */
  private function calculateConversionCoefficients($x1, $y1, $x2, $y2, &$k0, &$k1) {
    // $y2 = $x2 * $k1 + $k0;
    $k1 = ($y2 - $y1) / ($x2 - $x1);
    $k0 = $y2 - ($x2 * $k1);
  }

  /**
 * Helper function to tokenize lines from Units definition file
 * Format of string to tokenize is - CMD: token1,token2,token3...
 *
 * @param string $str
 * @return array $tokens
 * @access private
 */
  private function tokenize($str) {
    $tokens = array();

    $tokens[] = trim(strtok($str, ":"));

    while ($tok=strtok(","))
    $tokens[] = trim($tok);

    // Pad token list with empty tokens...
    for ($i = 0; $i < 6; $i++)
    $tokens[] = "";

    return $tokens;
  }

  /**
 * Creates a category in the MeasurementUnitCategory table.
 *
 * @param string $name
 * @param string $comment
 * @return int $categoryId
 * @access public
 *
 */
  public function createCategory($name, $comment="") {
    $unitCategory = new MeasurementUnitCategory($name, $comment);
    $unitCategory->save();
    return $unitCategory->getId();
  }

  /**
 * Creates a unit in the MeasurementUnit table.
 *
 * @param string $name
 * @param string $abbreviation
 * @param string $category
 * @param optional boolean $isBase
 * @param optional string $comment
 * @access public
 *
 */
  public function createUnit($name, $abbreviation, $category, $isBase=false, $comment="") {

    // Make certain the specified category exists
    $unitCategory = MeasurementUnitCategoryPeer::findByName($category);

    if (is_null($unitCategory)) {
      throw new Exception( sprintf("Can't find category: %s", $category) ) ;
    }

    // If this is not a base unit, find base unit for this category.
    $baseUnit = null;
    if (!$isBase) {
      $baseUnit = MeasurementUnitPeer::findBaseUnitByCategory( $unitCategory->getId() );
    }

    // $unit = new MeasurementUnit(null, $name, $myBaseUnit, $abbreviation, $unitCategory->getId(), "");
    $unit = new MeasurementUnit($name, $baseUnit, $abbreviation, $unitCategory, "");
    $unit->save();
  }

  /**
 * Creates a conversion and an inverse conversion in the UnitConversion table.
 * If necessary the conversion will be broken into two steps relative to the base units.
 * Two points which define the conversion are passed as (x1,y1), (x2,y2)
 *
 * @param string $fromUnitName
 * @param string $toUnitName
 * @param double x1
 * @param double y1
 * @param double x2
 * @param double y2
 * @access public
 *
 */

  public function createConversion($fromUnitName, $toUnitName, $x1, $y1, $x2, $y2) {

    $fromUnit = MeasurementUnitPeer::findByName($fromUnitName);
    $toUnit = MeasurementUnitPeer::findByName($toUnitName);

    // Both categories had better be the same!
    if ($fromUnit->getCategory() != $toUnit->getCategory() ) {
      throw new Exception("Can not create conversion between units of different categories.");
    }

    // Find the BaseUnit for this conversion...

    $baseUnit = MeasurementUnitPeer::findBaseUnitByCategory( $fromUnit->getCategoryId() );

    // If $toUnit, or $fromUnit is a base unit, then this is a straightforward entry...
    if ( $toUnit->isBaseUnit() || $fromUnit->isBaseUnit() ) {

      $this->calculateConversionCoefficients($x1, $y1, $x2, $y2, $k0, $k1);

      $conv = new MeasurementUnitConversion(null, $fromUnit, $toUnit, $k0, $k1);
      $conv->save();

      // Now, create the inverse conversion...

      $this->calculateConversionCoefficients($y1, $x1, $y2, $x2, $k0, $k1);

      $conv = new MeasurementUnitConversion(null, $toUnit, $fromUnit, $k0, $k1);
      $conv->save();
    }
    else {
      // All conversions must be relative to the base units...
      // So, if neither the "from" nor "to" parameter is a base unit we need to find an existing
      // conversion back to base units and use this exsiting conversion to create a new conversion.
      // This means that either the "from" or "to" unit must have an existing conversion back to
      // base.

      $from2BaseConv = MeasurementUnitConversionPeer::findByFromTo($fromUnit->getId(), $baseUnit->getId());
      $to2BaseConv = MeasurementUnitConversionPeer::findByFromTo($toUnit->getId(), $baseUnit->getId());

      // Is there an existing conversion for "to->base"?
      if ($to2BaseConv) {

        $base2ToConv = MeasurementUnitConversionPeer::findByFromTo($baseUnit->getId(), $toUnit->getId());

        $this->calculateConversionCoefficients($x1, $y1, $x2, $y2, $k0, $k1);

        $k0 += $to2BaseConv->getK0();
        $k1 *= $to2BaseConv->getK1();

        $conv = new MeasurementUnitConversion(null, $fromUnit, $baseUnit, $k0, $k1);
        $conv->save();

        // Now, create the inverse conversion...

        $this->calculateConversionCoefficients($y1, $x1, $y2, $x2, $k0, $k1);
        $k0 += $base2ToConv->getK0();
        $k1 *= $base2ToConv->getK1();

        $conv = new MeasurementUnitConversion(null, $baseUnit, $fromUnit, $k0, $k1);
        $conv->save();

      }
      else {
        if ($from2BaseConv) {

          $base2FromConv = MeasurementUnitConversionPeer::findByFromTo($baseUnit->getId(), $fromUnit->getId());

          $this->calculateConversionCoefficients($y1, $x1, $y2, $x2, $k0, $k1);

          $k0 += $from2BaseConv->getK0();
          $k1 *= $from2BaseConv->getK1();

          $conv = new MeasurementUnitConversion(null, $toUnit, $baseUnit, $k0, $k1);
          $conv->save();

          // Now, create the inverse conversion...

          $this->calculateConversionCoefficients($x1, $y1, $x2, $y2, $k0, $k1);

          $k0 += $base2FromConv->getK0();
          $k1 *= $base2FromConv->getK1();

          $conv = new MeasurementUnitConversion(null, $baseUnit, $toUnit, $k0, $k1);
          $conv->save();

        }
        else
        throw new Exception(sprintf("UnitManager::createConversion - (%s->%s) No conversion path to base exists", $fromUnit->getName(), $toUnit->getName() ));
      }
    }
  }

  /**
 * Converts passed value to units defined by the setConversionCoeffs method
 *
 * @param double $value
 * @return double $convertedValue
 * @access public
 */
  public function convert($value) {
    return ($value * $this->k1) + $this->k0;
  }

  /**
 * Converts passed value between units specified in parameters
 *
 * @param double $value
 * @param string $fromUnitName
 * @param string $toUnitName
 * @return double $convertedValue
 * @access public
 */
  public function convertFromTo($value, $fromUnitName, $toUnitName) {
    $this->setConversionCoeffs($fromUnitName, $toUnitName);

    return $this->convert($value);
  }

  /**
 * Converts passed value between units specified in parameters
 *
 * @param double $value
 * @param string $fromUnit
 * @param string $toUnit
 * @return double $convertedValue
 * @access public
 */
  public function convertFromUnitToUnit($value, $fromUnit, $toUnit) {
    if (is_null($fromUnit) || is_null($toUnit))
    return $value;
    return $this->convertFromTo($value, $fromUnit->getName(), $toUnit->getName());
  }

  /**
 * Converts passed value between units specified in parameters
 *
 * @param double $value
 * @param string $fromUnit
 * @param string $toUnit
 * @return double $convertedValue
 * @access public
 */
  public function getScalingFromUnitToUnit($fromUnit, $toUnit) {
    if (is_null($fromUnit) || is_null($toUnit))
    return 1.0;
    $this->setConversionCoeffs($fromUnit->getName(), $toUnit->getName());
    return $this->k1;
  }

  /**
 * Sets conversion system to be used by subsequent calls to "convert"
 *
 * @param string $fromUnitName
 * @param string $toUnitName
 * @access public
 */
  public function setConversionCoeffs($fromUnitName, $toUnitName) {
    if (strcmp($fromUnitName,$toUnitName) == 0) {
      $this->k0 = 0.0;
      $this->k1 = 1.0;
      return;
    }

    // If "from" or "to" is a base unit, then the conversion is simple.
    // Otherwise, we need to do a two step conversion: from->base, base->to

    $fromUnit = MeasurementUnitPeer::findByName($fromUnitName);
    $toUnit = MeasurementUnitPeer::findByName($toUnitName);

    if (is_null($fromUnit)) {
      throw new Exception( sprintf("Cannot find requested unit (%s) to convert from.", $fromUnitName) ) ;
    }
    if (is_null($toUnit)) {
      throw new Exception(sprintf("Cannot find requested unit (%s) to convert to.", $toUnitName) );
    }

    if ($fromUnit->getCategory() != $toUnit->getCategory() ) {
      throw new Exception("Can not convert between units of different categories.");
    }


    if ( $fromUnit->isBaseUnit() || $toUnit->isBaseUnit() ) {
      $this->conversion = MeasurementUnitConversionPeer::findByFromTo($fromUnit->getId(), $toUnit->getId());
      $this->k0 = $this->conversion->getK0();
      $this->k1 = $this->conversion->getK1();
    }
    else {
      // Find the BaseUnit for this conversion...
      $baseUnit = MeasurementUnitPeer::findBaseUnitByCategory( $fromUnit->getCategoryId() );

      $from2BaseConv = MeasurementUnitConversionPeer::findByFromTo($fromUnit->getId(), $baseUnit->getId());

      $this->k0 = $from2BaseConv->getK0();
      $this->k1 = $from2BaseConv->getK1();

      $base2ToConv = MeasurementUnitConversionPeer::findByFromTo($baseUnit->getId(), $toUnit->getId());
      $this->k0 += $base2ToConv->getK0();
      $this->k1 *= $base2ToConv->getK1();
    }
  }

  /**
 * Deletes entries from UnitConversion table which use units specified by parameter $unitName
 *
 * @param string $unitName
 */
  public function deleteConversionsFor($unitName) {

    // When a unit is deleted, all of the conversions which rely on that unit should be
    // deleted also.  This method makes such deletions straightforward.

    $unit = MeasurementUnitPeer::findByName($unitName);

    // Delete conversions with specified unit in "fromId" field
    $conversions = MeasurementUnitConversionPeer::findByFrom($unit->getId());

    foreach($conversions as $conversion) {
      $conversion->delete();
    }

    // Delete conversions with specified unit in "toId" field
    $conversions = MeasurementUnitConversionPeer::findByTo($unit->getId());

    foreach($conversions as $conversion) {
      $conversion->delete();
    }
  }

  /**
 * Ingest Unit defintions contained in passed file name.
 *
 * @param string $defFilePath
 * @access public
 */
  public function ingestDefinitions($defFilePath) {

    try {
      $fDef = fopen($defFilePath, "rt");

      while (!feof($fDef) ) {

        $inStr = rtrim(fgets($fDef, 1024));

        if (strlen($inStr) > 4) {

          $tokens = $this->tokenize($inStr);

          switch(strtoupper($tokens[0])) {
            case 'CAT' :
              //CAT: name, comment
              $categoryName = $tokens[1];
              $this->createCategory($categoryName, $tokens[2]);
              break;

            case 'BASEUNIT' :
              //BASEUNIT: name, abbr, comment
              $this->createUnit($tokens[1], $tokens[2], $categoryName, true, $tokens[3]);
              break;

            case 'UNIT' :
              //UNIT: name, abbr, comment
              $this->createUnit($tokens[1], $tokens[2], $categoryName, false, $tokens[3]);
              break;

            case 'CONV' :
              //CONV: from, to, x1, y1, x2, y2
              $this->createConversion($tokens[1], $tokens[2], $tokens[3], $tokens[4], $tokens[5], $tokens[6]);
              break;
          }
        }
      }

      fclose($fDef);

    }
    catch (Exception $e) {
      fclose($fDef);
      throw $e;
    }
  }

  public function getConversion() {
    return $this->conversion();
  }
}
?>
