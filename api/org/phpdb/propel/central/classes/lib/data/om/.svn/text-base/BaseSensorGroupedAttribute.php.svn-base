<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SensorGroupedAttributePeer.php';

/**
 * Base class that represents a row from the 'SENSOR_GROUPED_ATTRIBUTE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSensorGroupedAttribute extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SensorGroupedAttributePeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the attribute_id field.
	 * @var        double
	 */
	protected $attribute_id;


	/**
	 * The value for the date_value field.
	 * @var        int
	 */
	protected $date_value;


	/**
	 * The value for the g_attribute_id field.
	 * @var        double
	 */
	protected $g_attribute_id;


	/**
	 * The value for the int_value field.
	 * @var        double
	 */
	protected $int_value;


	/**
	 * The value for the note field.
	 * @var        string
	 */
	protected $note;


	/**
	 * The value for the num_value field.
	 * @var        double
	 */
	protected $num_value;


	/**
	 * The value for the page_count field.
	 * @var        double
	 */
	protected $page_count;


	/**
	 * The value for the string_value field.
	 * @var        string
	 */
	protected $string_value;


	/**
	 * The value for the unit_id field.
	 * @var        double
	 */
	protected $unit_id;

	/**
	 * @var        Attribute
	 */
	protected $aAttribute;

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
	 * Get the [attribute_id] column value.
	 * 
	 * @return     double
	 */
	public function getAttributeId()
	{

		return $this->attribute_id;
	}

	/**
	 * Get the [optionally formatted] [date_value] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getDateValue($format = '%Y-%m-%d')
	{

		if ($this->date_value === null || $this->date_value === '') {
			return null;
		} elseif (!is_int($this->date_value)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->date_value);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [date_value] as date/time value: " . var_export($this->date_value, true));
			}
		} else {
			$ts = $this->date_value;
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
	}

	/**
	 * Get the [g_attribute_id] column value.
	 * 
	 * @return     double
	 */
	public function getGroupAttributeId()
	{

		return $this->g_attribute_id;
	}

	/**
	 * Get the [int_value] column value.
	 * 
	 * @return     double
	 */
	public function getIntValue()
	{

		return $this->int_value;
	}

	/**
	 * Get the [note] column value.
	 * 
	 * @return     string
	 */
	public function getNote()
	{

		return $this->note;
	}

	/**
	 * Get the [num_value] column value.
	 * 
	 * @return     double
	 */
	public function getNumValue()
	{

		return $this->num_value;
	}

	/**
	 * Get the [page_count] column value.
	 * 
	 * @return     double
	 */
	public function getPageCount()
	{

		return $this->page_count;
	}

	/**
	 * Get the [string_value] column value.
	 * 
	 * @return     string
	 */
	public function getStringValue()
	{

		return $this->string_value;
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
			$this->modifiedColumns[] = SensorGroupedAttributePeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [attribute_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAttributeId($v)
	{

		if ($this->attribute_id !== $v) {
			$this->attribute_id = $v;
			$this->modifiedColumns[] = SensorGroupedAttributePeer::ATTRIBUTE_ID;
		}

		if ($this->aAttribute !== null && $this->aAttribute->getId() !== $v) {
			$this->aAttribute = null;
		}

	} // setAttributeId()

	/**
	 * Set the value of [date_value] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setDateValue($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [date_value] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->date_value !== $ts) {
			$this->date_value = $ts;
			$this->modifiedColumns[] = SensorGroupedAttributePeer::DATE_VALUE;
		}

	} // setDateValue()

	/**
	 * Set the value of [g_attribute_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setGroupAttributeId($v)
	{

		if ($this->g_attribute_id !== $v) {
			$this->g_attribute_id = $v;
			$this->modifiedColumns[] = SensorGroupedAttributePeer::G_ATTRIBUTE_ID;
		}

	} // setGroupAttributeId()

	/**
	 * Set the value of [int_value] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setIntValue($v)
	{

		if ($this->int_value !== $v) {
			$this->int_value = $v;
			$this->modifiedColumns[] = SensorGroupedAttributePeer::INT_VALUE;
		}

	} // setIntValue()

	/**
	 * Set the value of [note] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNote($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->note) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->note !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->note = $obj;
			$this->modifiedColumns[] = SensorGroupedAttributePeer::NOTE;
		}

	} // setNote()

	/**
	 * Set the value of [num_value] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNumValue($v)
	{

		if ($this->num_value !== $v) {
			$this->num_value = $v;
			$this->modifiedColumns[] = SensorGroupedAttributePeer::NUM_VALUE;
		}

	} // setNumValue()

	/**
	 * Set the value of [page_count] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPageCount($v)
	{

		if ($this->page_count !== $v) {
			$this->page_count = $v;
			$this->modifiedColumns[] = SensorGroupedAttributePeer::PAGE_COUNT;
		}

	} // setPageCount()

	/**
	 * Set the value of [string_value] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setStringValue($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->string_value) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->string_value !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->string_value = $obj;
			$this->modifiedColumns[] = SensorGroupedAttributePeer::STRING_VALUE;
		}

	} // setStringValue()

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
			$this->modifiedColumns[] = SensorGroupedAttributePeer::UNIT_ID;
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

			$this->attribute_id = $rs->getFloat($startcol + 1);

			$this->date_value = $rs->getDate($startcol + 2, null);

			$this->g_attribute_id = $rs->getFloat($startcol + 3);

			$this->int_value = $rs->getFloat($startcol + 4);

			$this->note = $rs->getClob($startcol + 5);

			$this->num_value = $rs->getFloat($startcol + 6);

			$this->page_count = $rs->getFloat($startcol + 7);

			$this->string_value = $rs->getClob($startcol + 8);

			$this->unit_id = $rs->getFloat($startcol + 9);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 10; // 10 = SensorGroupedAttributePeer::NUM_COLUMNS - SensorGroupedAttributePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SensorGroupedAttribute object", $e);
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
			$con = Propel::getConnection(SensorGroupedAttributePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SensorGroupedAttributePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SensorGroupedAttributePeer::DATABASE_NAME);
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

			if ($this->aAttribute !== null) {
				if ($this->aAttribute->isModified()) {
					$affectedRows += $this->aAttribute->save($con);
				}
				$this->setAttribute($this->aAttribute);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SensorGroupedAttributePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SensorGroupedAttributePeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
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

			if ($this->aAttribute !== null) {
				if (!$this->aAttribute->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAttribute->getValidationFailures());
				}
			}


			if (($retval = SensorGroupedAttributePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
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
		$pos = SensorGroupedAttributePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAttributeId();
				break;
			case 2:
				return $this->getDateValue();
				break;
			case 3:
				return $this->getGroupAttributeId();
				break;
			case 4:
				return $this->getIntValue();
				break;
			case 5:
				return $this->getNote();
				break;
			case 6:
				return $this->getNumValue();
				break;
			case 7:
				return $this->getPageCount();
				break;
			case 8:
				return $this->getStringValue();
				break;
			case 9:
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
		$keys = SensorGroupedAttributePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getAttributeId(),
			$keys[2] => $this->getDateValue(),
			$keys[3] => $this->getGroupAttributeId(),
			$keys[4] => $this->getIntValue(),
			$keys[5] => $this->getNote(),
			$keys[6] => $this->getNumValue(),
			$keys[7] => $this->getPageCount(),
			$keys[8] => $this->getStringValue(),
			$keys[9] => $this->getUnitId(),
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
		$pos = SensorGroupedAttributePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAttributeId($value);
				break;
			case 2:
				$this->setDateValue($value);
				break;
			case 3:
				$this->setGroupAttributeId($value);
				break;
			case 4:
				$this->setIntValue($value);
				break;
			case 5:
				$this->setNote($value);
				break;
			case 6:
				$this->setNumValue($value);
				break;
			case 7:
				$this->setPageCount($value);
				break;
			case 8:
				$this->setStringValue($value);
				break;
			case 9:
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
		$keys = SensorGroupedAttributePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAttributeId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDateValue($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setGroupAttributeId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setIntValue($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setNote($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setNumValue($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setPageCount($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setStringValue($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setUnitId($arr[$keys[9]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SensorGroupedAttributePeer::DATABASE_NAME);

		if ($this->isColumnModified(SensorGroupedAttributePeer::ID)) $criteria->add(SensorGroupedAttributePeer::ID, $this->id);
		if ($this->isColumnModified(SensorGroupedAttributePeer::ATTRIBUTE_ID)) $criteria->add(SensorGroupedAttributePeer::ATTRIBUTE_ID, $this->attribute_id);
		if ($this->isColumnModified(SensorGroupedAttributePeer::DATE_VALUE)) $criteria->add(SensorGroupedAttributePeer::DATE_VALUE, $this->date_value);
		if ($this->isColumnModified(SensorGroupedAttributePeer::G_ATTRIBUTE_ID)) $criteria->add(SensorGroupedAttributePeer::G_ATTRIBUTE_ID, $this->g_attribute_id);
		if ($this->isColumnModified(SensorGroupedAttributePeer::INT_VALUE)) $criteria->add(SensorGroupedAttributePeer::INT_VALUE, $this->int_value);
		if ($this->isColumnModified(SensorGroupedAttributePeer::NOTE)) $criteria->add(SensorGroupedAttributePeer::NOTE, $this->note);
		if ($this->isColumnModified(SensorGroupedAttributePeer::NUM_VALUE)) $criteria->add(SensorGroupedAttributePeer::NUM_VALUE, $this->num_value);
		if ($this->isColumnModified(SensorGroupedAttributePeer::PAGE_COUNT)) $criteria->add(SensorGroupedAttributePeer::PAGE_COUNT, $this->page_count);
		if ($this->isColumnModified(SensorGroupedAttributePeer::STRING_VALUE)) $criteria->add(SensorGroupedAttributePeer::STRING_VALUE, $this->string_value);
		if ($this->isColumnModified(SensorGroupedAttributePeer::UNIT_ID)) $criteria->add(SensorGroupedAttributePeer::UNIT_ID, $this->unit_id);

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
		$criteria = new Criteria(SensorGroupedAttributePeer::DATABASE_NAME);

		$criteria->add(SensorGroupedAttributePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SensorGroupedAttribute (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAttributeId($this->attribute_id);

		$copyObj->setDateValue($this->date_value);

		$copyObj->setGroupAttributeId($this->g_attribute_id);

		$copyObj->setIntValue($this->int_value);

		$copyObj->setNote($this->note);

		$copyObj->setNumValue($this->num_value);

		$copyObj->setPageCount($this->page_count);

		$copyObj->setStringValue($this->string_value);

		$copyObj->setUnitId($this->unit_id);


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
	 * @return     SensorGroupedAttribute Clone of current object.
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
	 * @return     SensorGroupedAttributePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SensorGroupedAttributePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Attribute object.
	 *
	 * @param      Attribute $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setAttribute($v)
	{


		if ($v === null) {
			$this->setAttributeId(NULL);
		} else {
			$this->setAttributeId($v->getId());
		}


		$this->aAttribute = $v;
	}


	/**
	 * Get the associated Attribute object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Attribute The associated Attribute object.
	 * @throws     PropelException
	 */
	public function getAttribute($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseAttributePeer.php';

		if ($this->aAttribute === null && ($this->attribute_id > 0)) {

			$this->aAttribute = AttributePeer::retrieveByPK($this->attribute_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = AttributePeer::retrieveByPK($this->attribute_id, $con);
			   $obj->addAttributes($this);
			 */
		}
		return $this->aAttribute;
	}

} // BaseSensorGroupedAttribute
