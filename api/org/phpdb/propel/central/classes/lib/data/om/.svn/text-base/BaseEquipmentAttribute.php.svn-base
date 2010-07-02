<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/EquipmentAttributePeer.php';

/**
 * Base class that represents a row from the 'EQUIPMENT_ATTRIBUTE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEquipmentAttribute extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EquipmentAttributePeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


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
	 * The value for the parent_id field.
	 * @var        double
	 */
	protected $parent_id;


	/**
	 * The value for the unit_id field.
	 * @var        double
	 */
	protected $unit_id;

	/**
	 * @var        EquipmentAttribute
	 */
	protected $aEquipmentAttributeRelatedByParentId;

	/**
	 * @var        Unit
	 */
	protected $aUnit;

	/**
	 * Collection to store aggregation of collEquipmentAttributesRelatedByParentId.
	 * @var        array
	 */
	protected $collEquipmentAttributesRelatedByParentId;

	/**
	 * The criteria used to select the current contents of collEquipmentAttributesRelatedByParentId.
	 * @var        Criteria
	 */
	protected $lastEquipmentAttributeRelatedByParentIdCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentAttributeClasss.
	 * @var        array
	 */
	protected $collEquipmentAttributeClasss;

	/**
	 * The criteria used to select the current contents of collEquipmentAttributeClasss.
	 * @var        Criteria
	 */
	protected $lastEquipmentAttributeClassCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentAttributeValues.
	 * @var        array
	 */
	protected $collEquipmentAttributeValues;

	/**
	 * The criteria used to select the current contents of collEquipmentAttributeValues.
	 * @var        Criteria
	 */
	protected $lastEquipmentAttributeValueCriteria = null;

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
	 * Get the [id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->id;
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
	 * Get the [parent_id] column value.
	 * 
	 * @return     double
	 */
	public function getParentId()
	{

		return $this->parent_id;
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
	 * Set the value of [id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = EquipmentAttributePeer::ID;
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
			$this->modifiedColumns[] = EquipmentAttributePeer::DATA_TYPE;
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
			$this->modifiedColumns[] = EquipmentAttributePeer::DESCRIPTION;
		}

	} // setDescription()

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
			$this->modifiedColumns[] = EquipmentAttributePeer::LABEL;
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
			$this->modifiedColumns[] = EquipmentAttributePeer::MAX_VALUE;
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
			$this->modifiedColumns[] = EquipmentAttributePeer::MIN_VALUE;
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
			$this->modifiedColumns[] = EquipmentAttributePeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [parent_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setParentId($v)
	{

		if ($this->parent_id !== $v) {
			$this->parent_id = $v;
			$this->modifiedColumns[] = EquipmentAttributePeer::PARENT_ID;
		}

		if ($this->aEquipmentAttributeRelatedByParentId !== null && $this->aEquipmentAttributeRelatedByParentId->getId() !== $v) {
			$this->aEquipmentAttributeRelatedByParentId = null;
		}

	} // setParentId()

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
			$this->modifiedColumns[] = EquipmentAttributePeer::UNIT_ID;
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

			$this->id = $rs->getFloat($startcol + 0);

			$this->data_type = $rs->getString($startcol + 1);

			$this->description = $rs->getString($startcol + 2);

			$this->label = $rs->getString($startcol + 3);

			$this->max_value = $rs->getFloat($startcol + 4);

			$this->min_value = $rs->getFloat($startcol + 5);

			$this->name = $rs->getString($startcol + 6);

			$this->parent_id = $rs->getFloat($startcol + 7);

			$this->unit_id = $rs->getFloat($startcol + 8);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = EquipmentAttributePeer::NUM_COLUMNS - EquipmentAttributePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating EquipmentAttribute object", $e);
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
			$con = Propel::getConnection(EquipmentAttributePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			EquipmentAttributePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(EquipmentAttributePeer::DATABASE_NAME);
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

			if ($this->aEquipmentAttributeRelatedByParentId !== null) {
				if ($this->aEquipmentAttributeRelatedByParentId->isModified()) {
					$affectedRows += $this->aEquipmentAttributeRelatedByParentId->save($con);
				}
				$this->setEquipmentAttributeRelatedByParentId($this->aEquipmentAttributeRelatedByParentId);
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
					$pk = EquipmentAttributePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += EquipmentAttributePeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collEquipmentAttributesRelatedByParentId !== null) {
				foreach($this->collEquipmentAttributesRelatedByParentId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentAttributeClasss !== null) {
				foreach($this->collEquipmentAttributeClasss as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentAttributeValues !== null) {
				foreach($this->collEquipmentAttributeValues as $referrerFK) {
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

			if ($this->aEquipmentAttributeRelatedByParentId !== null) {
				if (!$this->aEquipmentAttributeRelatedByParentId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEquipmentAttributeRelatedByParentId->getValidationFailures());
				}
			}

			if ($this->aUnit !== null) {
				if (!$this->aUnit->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aUnit->getValidationFailures());
				}
			}


			if (($retval = EquipmentAttributePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collEquipmentAttributeClasss !== null) {
					foreach($this->collEquipmentAttributeClasss as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentAttributeValues !== null) {
					foreach($this->collEquipmentAttributeValues as $referrerFK) {
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
		$pos = EquipmentAttributePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getLabel();
				break;
			case 4:
				return $this->getMaxValue();
				break;
			case 5:
				return $this->getMinValue();
				break;
			case 6:
				return $this->getName();
				break;
			case 7:
				return $this->getParentId();
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
		$keys = EquipmentAttributePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDataType(),
			$keys[2] => $this->getDescription(),
			$keys[3] => $this->getLabel(),
			$keys[4] => $this->getMaxValue(),
			$keys[5] => $this->getMinValue(),
			$keys[6] => $this->getName(),
			$keys[7] => $this->getParentId(),
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
		$pos = EquipmentAttributePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setLabel($value);
				break;
			case 4:
				$this->setMaxValue($value);
				break;
			case 5:
				$this->setMinValue($value);
				break;
			case 6:
				$this->setName($value);
				break;
			case 7:
				$this->setParentId($value);
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
		$keys = EquipmentAttributePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDataType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDescription($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setLabel($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setMaxValue($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setMinValue($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setName($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setParentId($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setUnitId($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EquipmentAttributePeer::DATABASE_NAME);

		if ($this->isColumnModified(EquipmentAttributePeer::ID)) $criteria->add(EquipmentAttributePeer::ID, $this->id);
		if ($this->isColumnModified(EquipmentAttributePeer::DATA_TYPE)) $criteria->add(EquipmentAttributePeer::DATA_TYPE, $this->data_type);
		if ($this->isColumnModified(EquipmentAttributePeer::DESCRIPTION)) $criteria->add(EquipmentAttributePeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(EquipmentAttributePeer::LABEL)) $criteria->add(EquipmentAttributePeer::LABEL, $this->label);
		if ($this->isColumnModified(EquipmentAttributePeer::MAX_VALUE)) $criteria->add(EquipmentAttributePeer::MAX_VALUE, $this->max_value);
		if ($this->isColumnModified(EquipmentAttributePeer::MIN_VALUE)) $criteria->add(EquipmentAttributePeer::MIN_VALUE, $this->min_value);
		if ($this->isColumnModified(EquipmentAttributePeer::NAME)) $criteria->add(EquipmentAttributePeer::NAME, $this->name);
		if ($this->isColumnModified(EquipmentAttributePeer::PARENT_ID)) $criteria->add(EquipmentAttributePeer::PARENT_ID, $this->parent_id);
		if ($this->isColumnModified(EquipmentAttributePeer::UNIT_ID)) $criteria->add(EquipmentAttributePeer::UNIT_ID, $this->unit_id);

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
		$criteria = new Criteria(EquipmentAttributePeer::DATABASE_NAME);

		$criteria->add(EquipmentAttributePeer::ID, $this->id);

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
	 * Generic method to set the primary key (id column).
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
	 * @param      object $copyObj An object of EquipmentAttribute (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDataType($this->data_type);

		$copyObj->setDescription($this->description);

		$copyObj->setLabel($this->label);

		$copyObj->setMaxValue($this->max_value);

		$copyObj->setMinValue($this->min_value);

		$copyObj->setName($this->name);

		$copyObj->setParentId($this->parent_id);

		$copyObj->setUnitId($this->unit_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getEquipmentAttributesRelatedByParentId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addEquipmentAttributeRelatedByParentId($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentAttributeClasss() as $relObj) {
				$copyObj->addEquipmentAttributeClass($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentAttributeValues() as $relObj) {
				$copyObj->addEquipmentAttributeValue($relObj->copy($deepCopy));
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
	 * @return     EquipmentAttribute Clone of current object.
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
	 * @return     EquipmentAttributePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EquipmentAttributePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a EquipmentAttribute object.
	 *
	 * @param      EquipmentAttribute $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEquipmentAttributeRelatedByParentId($v)
	{


		if ($v === null) {
			$this->setParentId(NULL);
		} else {
			$this->setParentId($v->getId());
		}


		$this->aEquipmentAttributeRelatedByParentId = $v;
	}


	/**
	 * Get the associated EquipmentAttribute object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     EquipmentAttribute The associated EquipmentAttribute object.
	 * @throws     PropelException
	 */
	public function getEquipmentAttributeRelatedByParentId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEquipmentAttributePeer.php';

		if ($this->aEquipmentAttributeRelatedByParentId === null && ($this->parent_id > 0)) {

			$this->aEquipmentAttributeRelatedByParentId = EquipmentAttributePeer::retrieveByPK($this->parent_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EquipmentAttributePeer::retrieveByPK($this->parent_id, $con);
			   $obj->addEquipmentAttributesRelatedByParentId($this);
			 */
		}
		return $this->aEquipmentAttributeRelatedByParentId;
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
	 * Temporary storage of collEquipmentAttributesRelatedByParentId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentAttributesRelatedByParentId()
	{
		if ($this->collEquipmentAttributesRelatedByParentId === null) {
			$this->collEquipmentAttributesRelatedByParentId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttribute has previously
	 * been saved, it will retrieve related EquipmentAttributesRelatedByParentId from storage.
	 * If this EquipmentAttribute is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentAttributesRelatedByParentId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributesRelatedByParentId === null) {
			if ($this->isNew()) {
			   $this->collEquipmentAttributesRelatedByParentId = array();
			} else {

				$criteria->add(EquipmentAttributePeer::PARENT_ID, $this->getId());

				EquipmentAttributePeer::addSelectColumns($criteria);
				$this->collEquipmentAttributesRelatedByParentId = EquipmentAttributePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentAttributePeer::PARENT_ID, $this->getId());

				EquipmentAttributePeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentAttributeRelatedByParentIdCriteria) || !$this->lastEquipmentAttributeRelatedByParentIdCriteria->equals($criteria)) {
					$this->collEquipmentAttributesRelatedByParentId = EquipmentAttributePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentAttributeRelatedByParentIdCriteria = $criteria;
		return $this->collEquipmentAttributesRelatedByParentId;
	}

	/**
	 * Returns the number of related EquipmentAttributesRelatedByParentId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentAttributesRelatedByParentId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentAttributePeer::PARENT_ID, $this->getId());

		return EquipmentAttributePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentAttribute object to this object
	 * through the EquipmentAttribute foreign key attribute
	 *
	 * @param      EquipmentAttribute $l EquipmentAttribute
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentAttributeRelatedByParentId(EquipmentAttribute $l)
	{
		$this->collEquipmentAttributesRelatedByParentId[] = $l;
		$l->setEquipmentAttributeRelatedByParentId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttribute is new, it will return
	 * an empty collection; or if this EquipmentAttribute has previously
	 * been saved, it will retrieve related EquipmentAttributesRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentAttribute.
	 */
	public function getEquipmentAttributesRelatedByParentIdJoinUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributesRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributesRelatedByParentId = array();
			} else {

				$criteria->add(EquipmentAttributePeer::PARENT_ID, $this->getId());

				$this->collEquipmentAttributesRelatedByParentId = EquipmentAttributePeer::doSelectJoinUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributePeer::PARENT_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeRelatedByParentIdCriteria) || !$this->lastEquipmentAttributeRelatedByParentIdCriteria->equals($criteria)) {
				$this->collEquipmentAttributesRelatedByParentId = EquipmentAttributePeer::doSelectJoinUnit($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeRelatedByParentIdCriteria = $criteria;

		return $this->collEquipmentAttributesRelatedByParentId;
	}

	/**
	 * Temporary storage of collEquipmentAttributeClasss to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentAttributeClasss()
	{
		if ($this->collEquipmentAttributeClasss === null) {
			$this->collEquipmentAttributeClasss = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttribute has previously
	 * been saved, it will retrieve related EquipmentAttributeClasss from storage.
	 * If this EquipmentAttribute is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentAttributeClasss($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeClassPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeClasss === null) {
			if ($this->isNew()) {
			   $this->collEquipmentAttributeClasss = array();
			} else {

				$criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

				EquipmentAttributeClassPeer::addSelectColumns($criteria);
				$this->collEquipmentAttributeClasss = EquipmentAttributeClassPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

				EquipmentAttributeClassPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentAttributeClassCriteria) || !$this->lastEquipmentAttributeClassCriteria->equals($criteria)) {
					$this->collEquipmentAttributeClasss = EquipmentAttributeClassPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentAttributeClassCriteria = $criteria;
		return $this->collEquipmentAttributeClasss;
	}

	/**
	 * Returns the number of related EquipmentAttributeClasss.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentAttributeClasss($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeClassPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

		return EquipmentAttributeClassPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentAttributeClass object to this object
	 * through the EquipmentAttributeClass foreign key attribute
	 *
	 * @param      EquipmentAttributeClass $l EquipmentAttributeClass
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentAttributeClass(EquipmentAttributeClass $l)
	{
		$this->collEquipmentAttributeClasss[] = $l;
		$l->setEquipmentAttribute($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttribute is new, it will return
	 * an empty collection; or if this EquipmentAttribute has previously
	 * been saved, it will retrieve related EquipmentAttributeClasss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentAttribute.
	 */
	public function getEquipmentAttributeClasssJoinEquipmentClass($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeClassPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeClasss === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeClasss = array();
			} else {

				$criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

				$this->collEquipmentAttributeClasss = EquipmentAttributeClassPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeClassCriteria) || !$this->lastEquipmentAttributeClassCriteria->equals($criteria)) {
				$this->collEquipmentAttributeClasss = EquipmentAttributeClassPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeClassCriteria = $criteria;

		return $this->collEquipmentAttributeClasss;
	}

	/**
	 * Temporary storage of collEquipmentAttributeValues to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentAttributeValues()
	{
		if ($this->collEquipmentAttributeValues === null) {
			$this->collEquipmentAttributeValues = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttribute has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 * If this EquipmentAttribute is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentAttributeValues($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
			   $this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

				EquipmentAttributeValuePeer::addSelectColumns($criteria);
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

				EquipmentAttributeValuePeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
					$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;
		return $this->collEquipmentAttributeValues;
	}

	/**
	 * Returns the number of related EquipmentAttributeValues.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentAttributeValues($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

		return EquipmentAttributeValuePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentAttributeValue object to this object
	 * through the EquipmentAttributeValue foreign key attribute
	 *
	 * @param      EquipmentAttributeValue $l EquipmentAttributeValue
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentAttributeValue(EquipmentAttributeValue $l)
	{
		$this->collEquipmentAttributeValues[] = $l;
		$l->setEquipmentAttribute($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttribute is new, it will return
	 * an empty collection; or if this EquipmentAttribute has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentAttribute.
	 */
	public function getEquipmentAttributeValuesJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttribute is new, it will return
	 * an empty collection; or if this EquipmentAttribute has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentAttribute.
	 */
	public function getEquipmentAttributeValuesJoinEquipmentAttributeClass($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttributeClass($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttributeClass($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttribute is new, it will return
	 * an empty collection; or if this EquipmentAttribute has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentAttribute.
	 */
	public function getEquipmentAttributeValuesJoinUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinUnit($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
	}

} // BaseEquipmentAttribute
