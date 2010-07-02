<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/AttributePeer.php';

/**
 * Base class that represents a row from the 'ATTRIBUTE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseAttribute extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AttributePeer
	 */
	protected static $peer;


	/**
	 * The value for the attribute_id field.
	 * @var        double
	 */
	protected $attribute_id;


	/**
	 * The value for the data_type field.
	 * @var        string
	 */
	protected $data_type;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the equipment_class_id field.
	 * @var        double
	 */
	protected $equipment_class_id;


	/**
	 * The value for the label field.
	 * @var        string
	 */
	protected $label;


	/**
	 * The value for the max_value field.
	 * @var        double
	 */
	protected $max_value;


	/**
	 * The value for the min_value field.
	 * @var        double
	 */
	protected $min_value;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the unit_id field.
	 * @var        double
	 */
	protected $unit_id;

	/**
	 * @var        EquipmentClass
	 */
	protected $aEquipmentClass;

	/**
	 * @var        Unit
	 */
	protected $aUnit;

	/**
	 * Collection to store aggregation of collEquipmentGroupedAttributes.
	 * @var        array
	 */
	protected $collEquipmentGroupedAttributes;

	/**
	 * The criteria used to select the current contents of collEquipmentGroupedAttributes.
	 * @var        Criteria
	 */
	protected $lastEquipmentGroupedAttributeCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentGroupedAttributeMaps.
	 * @var        array
	 */
	protected $collEquipmentGroupedAttributeMaps;

	/**
	 * The criteria used to select the current contents of collEquipmentGroupedAttributeMaps.
	 * @var        Criteria
	 */
	protected $lastEquipmentGroupedAttributeMapCriteria = null;

	/**
	 * Collection to store aggregation of collSensorAttributes.
	 * @var        array
	 */
	protected $collSensorAttributes;

	/**
	 * The criteria used to select the current contents of collSensorAttributes.
	 * @var        Criteria
	 */
	protected $lastSensorAttributeCriteria = null;

	/**
	 * Collection to store aggregation of collSensorGroupedAttributes.
	 * @var        array
	 */
	protected $collSensorGroupedAttributes;

	/**
	 * The criteria used to select the current contents of collSensorGroupedAttributes.
	 * @var        Criteria
	 */
	protected $lastSensorGroupedAttributeCriteria = null;

	/**
	 * Flag to prevent endless save loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInSave = false;

	/**
	 * Flag to prevent endless validation loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInValidation = false;

	/**
	 * Get the [attribute_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->attribute_id;
	}

	/**
	 * Get the [data_type] column value.
	 * 
	 * @return     string
	 */
	public function getDataType()
	{

		return $this->data_type;
	}

	/**
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
	}

	/**
	 * Get the [equipment_class_id] column value.
	 * 
	 * @return     double
	 */
	public function getEquipmentClassId()
	{

		return $this->equipment_class_id;
	}

	/**
	 * Get the [label] column value.
	 * 
	 * @return     string
	 */
	public function getLabel()
	{

		return $this->label;
	}

	/**
	 * Get the [max_value] column value.
	 * 
	 * @return     double
	 */
	public function getMaxValue()
	{

		return $this->max_value;
	}

	/**
	 * Get the [min_value] column value.
	 * 
	 * @return     double
	 */
	public function getMinValue()
	{

		return $this->min_value;
	}

	/**
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{

		return $this->name;
	}

	/**
	 * Get the [unit_id] column value.
	 * 
	 * @return     double
	 */
	public function getUnitId()
	{

		return $this->unit_id;
	}

	/**
	 * Set the value of [attribute_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->attribute_id !== $v) {
			$this->attribute_id = $v;
			$this->modifiedColumns[] = AttributePeer::ATTRIBUTE_ID;
		}

	} // setId()

	/**
	 * Set the value of [data_type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDataType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->data_type !== $v) {
			$this->data_type = $v;
			$this->modifiedColumns[] = AttributePeer::DATA_TYPE;
		}

	} // setDataType()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDescription($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = AttributePeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [equipment_class_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEquipmentClassId($v)
	{

		if ($this->equipment_class_id !== $v) {
			$this->equipment_class_id = $v;
			$this->modifiedColumns[] = AttributePeer::EQUIPMENT_CLASS_ID;
		}

		if ($this->aEquipmentClass !== null && $this->aEquipmentClass->getId() !== $v) {
			$this->aEquipmentClass = null;
		}

	} // setEquipmentClassId()

	/**
	 * Set the value of [label] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setLabel($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->label !== $v) {
			$this->label = $v;
			$this->modifiedColumns[] = AttributePeer::LABEL;
		}

	} // setLabel()

	/**
	 * Set the value of [max_value] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMaxValue($v)
	{

		if ($this->max_value !== $v) {
			$this->max_value = $v;
			$this->modifiedColumns[] = AttributePeer::MAX_VALUE;
		}

	} // setMaxValue()

	/**
	 * Set the value of [min_value] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMinValue($v)
	{

		if ($this->min_value !== $v) {
			$this->min_value = $v;
			$this->modifiedColumns[] = AttributePeer::MIN_VALUE;
		}

	} // setMinValue()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = AttributePeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [unit_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setUnitId($v)
	{

		if ($this->unit_id !== $v) {
			$this->unit_id = $v;
			$this->modifiedColumns[] = AttributePeer::UNIT_ID;
		}

		if ($this->aUnit !== null && $this->aUnit->getId() !== $v) {
			$this->aUnit = null;
		}

	} // setUnitId()

	/**
	 * Hydrates (populates) the object variables with values from the database resultset.
	 *
	 * An offset (1-based "start column") is specified so that objects can be hydrated
	 * with a subset of the columns in the resultset rows.  This is needed, for example,
	 * for results of JOIN queries where the resultset row includes columns from two or
	 * more tables.
	 *
	 * @param      ResultSet $rs The ResultSet class with cursor advanced to desired record pos.
	 * @param      int $startcol 1-based offset column which indicates which restultset column to start with.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->attribute_id = $rs->getFloat($startcol + 0);

			$this->data_type = $rs->getString($startcol + 1);

			$this->description = $rs->getString($startcol + 2);

			$this->equipment_class_id = $rs->getFloat($startcol + 3);

			$this->label = $rs->getString($startcol + 4);

			$this->max_value = $rs->getFloat($startcol + 5);

			$this->min_value = $rs->getFloat($startcol + 6);

			$this->name = $rs->getString($startcol + 7);

			$this->unit_id = $rs->getFloat($startcol + 8);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = AttributePeer::NUM_COLUMNS - AttributePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Attribute object", $e);
		}
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      Connection $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(AttributePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			AttributePeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Stores the object in the database.  If the object is new,
	 * it inserts it; otherwise an update is performed.  This method
	 * wraps the doSave() worker method in a transaction.
	 *
	 * @param      Connection $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(AttributePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			$affectedRows = $this->doSave($con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Stores the object in the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All related objects are also updated in this method.
	 *
	 * @param      Connection $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave($con)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;


			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aEquipmentClass !== null) {
				if ($this->aEquipmentClass->isModified()) {
					$affectedRows += $this->aEquipmentClass->save($con);
				}
				$this->setEquipmentClass($this->aEquipmentClass);
			}

			if ($this->aUnit !== null) {
				if ($this->aUnit->isModified()) {
					$affectedRows += $this->aUnit->save($con);
				}
				$this->setUnit($this->aUnit);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = AttributePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += AttributePeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collEquipmentGroupedAttributes !== null) {
				foreach($this->collEquipmentGroupedAttributes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentGroupedAttributeMaps !== null) {
				foreach($this->collEquipmentGroupedAttributeMaps as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorAttributes !== null) {
				foreach($this->collSensorAttributes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorGroupedAttributes !== null) {
				foreach($this->collSensorGroupedAttributes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			$this->alreadyInSave = false;
		}
		return $affectedRows;
	} // doSave()

	/**
	 * Array of ValidationFailed objects.
	 * @var        array ValidationFailed[]
	 */
	protected $validationFailures = array();

	/**
	 * Gets any ValidationFailed objects that resulted from last call to validate().
	 *
	 *
	 * @return     array ValidationFailed[]
	 * @see        validate()
	 */
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	/**
	 * Validates the objects modified field values and all objects related to this table.
	 *
	 * If $columns is either a column name or an array of column names
	 * only those columns are validated.
	 *
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
	 * @see        doValidate()
	 * @see        getValidationFailures()
	 */
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aEquipmentClass !== null) {
				if (!$this->aEquipmentClass->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEquipmentClass->getValidationFailures());
				}
			}

			if ($this->aUnit !== null) {
				if (!$this->aUnit->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aUnit->getValidationFailures());
				}
			}


			if (($retval = AttributePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collEquipmentGroupedAttributes !== null) {
					foreach($this->collEquipmentGroupedAttributes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentGroupedAttributeMaps !== null) {
					foreach($this->collEquipmentGroupedAttributeMaps as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorAttributes !== null) {
					foreach($this->collSensorAttributes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorGroupedAttributes !== null) {
					foreach($this->collSensorGroupedAttributes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}


			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 * Retrieves a field from the object by name passed in as a string.
	 *
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants TYPE_PHPNAME,
	 *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = AttributePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getId();
				break;
			case 1:
				return $this->getDataType();
				break;
			case 2:
				return $this->getDescription();
				break;
			case 3:
				return $this->getEquipmentClassId();
				break;
			case 4:
				return $this->getLabel();
				break;
			case 5:
				return $this->getMaxValue();
				break;
			case 6:
				return $this->getMinValue();
				break;
			case 7:
				return $this->getName();
				break;
			case 8:
				return $this->getUnitId();
				break;
			default:
				return null;
				break;
		} // switch()
	}

	/**
	 * Exports the object as an array.
	 *
	 * You can specify the key type of the array by passing one of the class
	 * type constants.
	 *
	 * @param      string $keyType One of the class type constants TYPE_PHPNAME,
	 *                        TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = AttributePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDataType(),
			$keys[2] => $this->getDescription(),
			$keys[3] => $this->getEquipmentClassId(),
			$keys[4] => $this->getLabel(),
			$keys[5] => $this->getMaxValue(),
			$keys[6] => $this->getMinValue(),
			$keys[7] => $this->getName(),
			$keys[8] => $this->getUnitId(),
		);
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants TYPE_PHPNAME,
	 *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = AttributePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setDataType($value);
				break;
			case 2:
				$this->setDescription($value);
				break;
			case 3:
				$this->setEquipmentClassId($value);
				break;
			case 4:
				$this->setLabel($value);
				break;
			case 5:
				$this->setMaxValue($value);
				break;
			case 6:
				$this->setMinValue($value);
				break;
			case 7:
				$this->setName($value);
				break;
			case 8:
				$this->setUnitId($value);
				break;
		} // switch()
	}

	/**
	 * Populates the object using an array.
	 *
	 * This is particularly useful when populating an object from one of the
	 * request arrays (e.g. $_POST).  This method goes through the column
	 * names, checking to see whether a matching key exists in populated
	 * array. If so the setByName() method is called for that column.
	 *
	 * You can specify the key type of the array by additionally passing one
	 * of the class type constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME,
	 * TYPE_NUM. The default key type is the column's phpname (e.g. 'authorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = AttributePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDataType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDescription($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setEquipmentClassId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setLabel($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setMaxValue($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setMinValue($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setName($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setUnitId($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AttributePeer::DATABASE_NAME);

		if ($this->isColumnModified(AttributePeer::ATTRIBUTE_ID)) $criteria->add(AttributePeer::ATTRIBUTE_ID, $this->attribute_id);
		if ($this->isColumnModified(AttributePeer::DATA_TYPE)) $criteria->add(AttributePeer::DATA_TYPE, $this->data_type);
		if ($this->isColumnModified(AttributePeer::DESCRIPTION)) $criteria->add(AttributePeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(AttributePeer::EQUIPMENT_CLASS_ID)) $criteria->add(AttributePeer::EQUIPMENT_CLASS_ID, $this->equipment_class_id);
		if ($this->isColumnModified(AttributePeer::LABEL)) $criteria->add(AttributePeer::LABEL, $this->label);
		if ($this->isColumnModified(AttributePeer::MAX_VALUE)) $criteria->add(AttributePeer::MAX_VALUE, $this->max_value);
		if ($this->isColumnModified(AttributePeer::MIN_VALUE)) $criteria->add(AttributePeer::MIN_VALUE, $this->min_value);
		if ($this->isColumnModified(AttributePeer::NAME)) $criteria->add(AttributePeer::NAME, $this->name);
		if ($this->isColumnModified(AttributePeer::UNIT_ID)) $criteria->add(AttributePeer::UNIT_ID, $this->unit_id);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(AttributePeer::DATABASE_NAME);

		$criteria->add(AttributePeer::ATTRIBUTE_ID, $this->attribute_id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     double
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (attribute_id column).
	 *
	 * @param      double $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of Attribute (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDataType($this->data_type);

		$copyObj->setDescription($this->description);

		$copyObj->setEquipmentClassId($this->equipment_class_id);

		$copyObj->setLabel($this->label);

		$copyObj->setMaxValue($this->max_value);

		$copyObj->setMinValue($this->min_value);

		$copyObj->setName($this->name);

		$copyObj->setUnitId($this->unit_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getEquipmentGroupedAttributes() as $relObj) {
				$copyObj->addEquipmentGroupedAttribute($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentGroupedAttributeMaps() as $relObj) {
				$copyObj->addEquipmentGroupedAttributeMap($relObj->copy($deepCopy));
			}

			foreach($this->getSensorAttributes() as $relObj) {
				$copyObj->addSensorAttribute($relObj->copy($deepCopy));
			}

			foreach($this->getSensorGroupedAttributes() as $relObj) {
				$copyObj->addSensorGroupedAttribute($relObj->copy($deepCopy));
			}

		} // if ($deepCopy)


		$copyObj->setNew(true);

		$copyObj->setId(NULL); // this is a pkey column, so set to default value

	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     Attribute Clone of current object.
	 * @throws     PropelException
	 */
	public function copy($deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	/**
	 * Returns a peer instance associated with this om.
	 *
	 * Since Peer classes are not to have any instance attributes, this method returns the
	 * same instance for all member of this class. The method could therefore
	 * be static, but this would prevent one from overriding the behavior.
	 *
	 * @return     AttributePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AttributePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a EquipmentClass object.
	 *
	 * @param      EquipmentClass $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEquipmentClass($v)
	{


		if ($v === null) {
			$this->setEquipmentClassId(NULL);
		} else {
			$this->setEquipmentClassId($v->getId());
		}


		$this->aEquipmentClass = $v;
	}


	/**
	 * Get the associated EquipmentClass object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     EquipmentClass The associated EquipmentClass object.
	 * @throws     PropelException
	 */
	public function getEquipmentClass($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEquipmentClassPeer.php';

		if ($this->aEquipmentClass === null && ($this->equipment_class_id > 0)) {

			$this->aEquipmentClass = EquipmentClassPeer::retrieveByPK($this->equipment_class_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EquipmentClassPeer::retrieveByPK($this->equipment_class_id, $con);
			   $obj->addEquipmentClasss($this);
			 */
		}
		return $this->aEquipmentClass;
	}

	/**
	 * Declares an association between this object and a Unit object.
	 *
	 * @param      Unit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setUnit($v)
	{


		if ($v === null) {
			$this->setUnitId(NULL);
		} else {
			$this->setUnitId($v->getId());
		}


		$this->aUnit = $v;
	}


	/**
	 * Get the associated Unit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Unit The associated Unit object.
	 * @throws     PropelException
	 */
	public function getUnit($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseUnitPeer.php';

		if ($this->aUnit === null && ($this->unit_id > 0)) {

			$this->aUnit = UnitPeer::retrieveByPK($this->unit_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = UnitPeer::retrieveByPK($this->unit_id, $con);
			   $obj->addUnits($this);
			 */
		}
		return $this->aUnit;
	}

	/**
	 * Temporary storage of collEquipmentGroupedAttributes to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentGroupedAttributes()
	{
		if ($this->collEquipmentGroupedAttributes === null) {
			$this->collEquipmentGroupedAttributes = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Attribute has previously
	 * been saved, it will retrieve related EquipmentGroupedAttributes from storage.
	 * If this Attribute is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentGroupedAttributes($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentGroupedAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentGroupedAttributes === null) {
			if ($this->isNew()) {
			   $this->collEquipmentGroupedAttributes = array();
			} else {

				$criteria->add(EquipmentGroupedAttributePeer::ATTRIBUTE_ID, $this->getId());

				EquipmentGroupedAttributePeer::addSelectColumns($criteria);
				$this->collEquipmentGroupedAttributes = EquipmentGroupedAttributePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentGroupedAttributePeer::ATTRIBUTE_ID, $this->getId());

				EquipmentGroupedAttributePeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentGroupedAttributeCriteria) || !$this->lastEquipmentGroupedAttributeCriteria->equals($criteria)) {
					$this->collEquipmentGroupedAttributes = EquipmentGroupedAttributePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentGroupedAttributeCriteria = $criteria;
		return $this->collEquipmentGroupedAttributes;
	}

	/**
	 * Returns the number of related EquipmentGroupedAttributes.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentGroupedAttributes($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentGroupedAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentGroupedAttributePeer::ATTRIBUTE_ID, $this->getId());

		return EquipmentGroupedAttributePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentGroupedAttribute object to this object
	 * through the EquipmentGroupedAttribute foreign key attribute
	 *
	 * @param      EquipmentGroupedAttribute $l EquipmentGroupedAttribute
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentGroupedAttribute(EquipmentGroupedAttribute $l)
	{
		$this->collEquipmentGroupedAttributes[] = $l;
		$l->setAttribute($this);
	}

	/**
	 * Temporary storage of collEquipmentGroupedAttributeMaps to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentGroupedAttributeMaps()
	{
		if ($this->collEquipmentGroupedAttributeMaps === null) {
			$this->collEquipmentGroupedAttributeMaps = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Attribute has previously
	 * been saved, it will retrieve related EquipmentGroupedAttributeMaps from storage.
	 * If this Attribute is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentGroupedAttributeMaps($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentGroupedAttributeMapPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentGroupedAttributeMaps === null) {
			if ($this->isNew()) {
			   $this->collEquipmentGroupedAttributeMaps = array();
			} else {

				$criteria->add(EquipmentGroupedAttributeMapPeer::ATTRIBUTE_ID, $this->getId());

				EquipmentGroupedAttributeMapPeer::addSelectColumns($criteria);
				$this->collEquipmentGroupedAttributeMaps = EquipmentGroupedAttributeMapPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentGroupedAttributeMapPeer::ATTRIBUTE_ID, $this->getId());

				EquipmentGroupedAttributeMapPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentGroupedAttributeMapCriteria) || !$this->lastEquipmentGroupedAttributeMapCriteria->equals($criteria)) {
					$this->collEquipmentGroupedAttributeMaps = EquipmentGroupedAttributeMapPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentGroupedAttributeMapCriteria = $criteria;
		return $this->collEquipmentGroupedAttributeMaps;
	}

	/**
	 * Returns the number of related EquipmentGroupedAttributeMaps.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentGroupedAttributeMaps($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentGroupedAttributeMapPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentGroupedAttributeMapPeer::ATTRIBUTE_ID, $this->getId());

		return EquipmentGroupedAttributeMapPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentGroupedAttributeMap object to this object
	 * through the EquipmentGroupedAttributeMap foreign key attribute
	 *
	 * @param      EquipmentGroupedAttributeMap $l EquipmentGroupedAttributeMap
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentGroupedAttributeMap(EquipmentGroupedAttributeMap $l)
	{
		$this->collEquipmentGroupedAttributeMaps[] = $l;
		$l->setAttribute($this);
	}

	/**
	 * Temporary storage of collSensorAttributes to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorAttributes()
	{
		if ($this->collSensorAttributes === null) {
			$this->collSensorAttributes = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Attribute has previously
	 * been saved, it will retrieve related SensorAttributes from storage.
	 * If this Attribute is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorAttributes($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorAttributes === null) {
			if ($this->isNew()) {
			   $this->collSensorAttributes = array();
			} else {

				$criteria->add(SensorAttributePeer::ATTRIBUTE_ID, $this->getId());

				SensorAttributePeer::addSelectColumns($criteria);
				$this->collSensorAttributes = SensorAttributePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorAttributePeer::ATTRIBUTE_ID, $this->getId());

				SensorAttributePeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorAttributeCriteria) || !$this->lastSensorAttributeCriteria->equals($criteria)) {
					$this->collSensorAttributes = SensorAttributePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorAttributeCriteria = $criteria;
		return $this->collSensorAttributes;
	}

	/**
	 * Returns the number of related SensorAttributes.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorAttributes($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorAttributePeer::ATTRIBUTE_ID, $this->getId());

		return SensorAttributePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorAttribute object to this object
	 * through the SensorAttribute foreign key attribute
	 *
	 * @param      SensorAttribute $l SensorAttribute
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorAttribute(SensorAttribute $l)
	{
		$this->collSensorAttributes[] = $l;
		$l->setAttribute($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Attribute is new, it will return
	 * an empty collection; or if this Attribute has previously
	 * been saved, it will retrieve related SensorAttributes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Attribute.
	 */
	public function getSensorAttributesJoinSensor($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorAttributes === null) {
			if ($this->isNew()) {
				$this->collSensorAttributes = array();
			} else {

				$criteria->add(SensorAttributePeer::ATTRIBUTE_ID, $this->getId());

				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinSensor($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorAttributePeer::ATTRIBUTE_ID, $this->getId());

			if (!isset($this->lastSensorAttributeCriteria) || !$this->lastSensorAttributeCriteria->equals($criteria)) {
				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinSensor($criteria, $con);
			}
		}
		$this->lastSensorAttributeCriteria = $criteria;

		return $this->collSensorAttributes;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Attribute is new, it will return
	 * an empty collection; or if this Attribute has previously
	 * been saved, it will retrieve related SensorAttributes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Attribute.
	 */
	public function getSensorAttributesJoinUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorAttributes === null) {
			if ($this->isNew()) {
				$this->collSensorAttributes = array();
			} else {

				$criteria->add(SensorAttributePeer::ATTRIBUTE_ID, $this->getId());

				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorAttributePeer::ATTRIBUTE_ID, $this->getId());

			if (!isset($this->lastSensorAttributeCriteria) || !$this->lastSensorAttributeCriteria->equals($criteria)) {
				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinUnit($criteria, $con);
			}
		}
		$this->lastSensorAttributeCriteria = $criteria;

		return $this->collSensorAttributes;
	}

	/**
	 * Temporary storage of collSensorGroupedAttributes to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorGroupedAttributes()
	{
		if ($this->collSensorGroupedAttributes === null) {
			$this->collSensorGroupedAttributes = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Attribute has previously
	 * been saved, it will retrieve related SensorGroupedAttributes from storage.
	 * If this Attribute is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorGroupedAttributes($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorGroupedAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorGroupedAttributes === null) {
			if ($this->isNew()) {
			   $this->collSensorGroupedAttributes = array();
			} else {

				$criteria->add(SensorGroupedAttributePeer::ATTRIBUTE_ID, $this->getId());

				SensorGroupedAttributePeer::addSelectColumns($criteria);
				$this->collSensorGroupedAttributes = SensorGroupedAttributePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorGroupedAttributePeer::ATTRIBUTE_ID, $this->getId());

				SensorGroupedAttributePeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorGroupedAttributeCriteria) || !$this->lastSensorGroupedAttributeCriteria->equals($criteria)) {
					$this->collSensorGroupedAttributes = SensorGroupedAttributePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorGroupedAttributeCriteria = $criteria;
		return $this->collSensorGroupedAttributes;
	}

	/**
	 * Returns the number of related SensorGroupedAttributes.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorGroupedAttributes($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorGroupedAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorGroupedAttributePeer::ATTRIBUTE_ID, $this->getId());

		return SensorGroupedAttributePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorGroupedAttribute object to this object
	 * through the SensorGroupedAttribute foreign key attribute
	 *
	 * @param      SensorGroupedAttribute $l SensorGroupedAttribute
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorGroupedAttribute(SensorGroupedAttribute $l)
	{
		$this->collSensorGroupedAttributes[] = $l;
		$l->setAttribute($this);
	}

} // BaseAttribute
