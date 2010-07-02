<?php

require_once 'lib/data/om/BaseAttribute.php';


/**
 * Attribute
 * Skeleton subclass for representing a row from the 'Attribute' table.
 *
 * An attribute can be an INTEGER, NUMBER, STRING, URL or "GROUP" as determined
 * by the dataType column
 *
 * Each instance of EquipmentClass has zero or more attributes.
 *
 * @package    lib.data
 *
 * @uses EquipmentClass
 * @uses Unit
 *
 * @todo Rename attribute to id
 * @todo Eliminate redundant ENUMs / database entries
 *
 */
class Attribute extends BaseAttribute {

	const DATATYPE_GROUP = "GROUP";
	const DATATYPE_INTEGER = "INTEGER";
	const DATATYPE_NUMBER = "NUMBER";
	const DATATYPE_STRING = "STRING";
	const DATATYPE_URL = "URL";

	/**
	 * Initializes internal state of Attribute object.
	 */
  public function __construct($name=null,
                              $description=null,
                              Unit $unit=null,
                              EquipmentClass $equipmentClass=null,
                              $minValue=null,
                              $maxValue=null,
                              $dataType=null,
                              $label=null) {
		$this->setName($name);
		$this->setDescription($description);
		$this->setMinValue($minValue);
		$this->setMaxValue($maxValue);
		$this->setDataType($dataType);
		$this->setLabel($label);
		$this->setUnit($unit);
		$this->setEquipmentClass($equipmentClass);
	}

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/Attribute/{$this->getId()}";
  }

} // Attribute
?>
