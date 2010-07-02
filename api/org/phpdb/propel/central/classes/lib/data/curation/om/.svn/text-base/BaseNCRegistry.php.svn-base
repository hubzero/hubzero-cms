<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/curation/NCRegistryPeer.php';

/**
 * Base class that represents a row from the 'REGISTRY' table.
 *
 * 
 *
 * @package    lib.data.curation.om
 */
abstract class BaseNCRegistry extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        NCRegistryPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the access_time field.
	 * @var        int
	 */
	protected $access_time;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the key_name field.
	 * @var        string
	 */
	protected $key_name;


	/**
	 * The value for the key_type field.
	 * @var        string
	 */
	protected $key_type;


	/**
	 * The value for the key_value field.
	 * @var        string
	 */
	protected $key_value;


	/**
	 * The value for the name_space field.
	 * @var        string
	 */
	protected $name_space;


	/**
	 * The value for the status field.
	 * @var        string
	 */
	protected $status;

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
	 * Get the [optionally formatted] [access_time] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getAccessTime($format = '%Y-%m-%d')
	{

		if ($this->access_time === null || $this->access_time === '') {
			return null;
		} elseif (!is_int($this->access_time)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->access_time);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [access_time] as date/time value: " . var_export($this->access_time, true));
			}
		} else {
			$ts = $this->access_time;
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
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
	}

	/**
	 * Get the [key_name] column value.
	 * 
	 * @return     string
	 */
	public function getKeyName()
	{

		return $this->key_name;
	}

	/**
	 * Get the [key_type] column value.
	 * 
	 * @return     string
	 */
	public function getKeyType()
	{

		return $this->key_type;
	}

	/**
	 * Get the [key_value] column value.
	 * 
	 * @return     string
	 */
	public function getKeyValue()
	{

		return $this->key_value;
	}

	/**
	 * Get the [name_space] column value.
	 * 
	 * @return     string
	 */
	public function getNameSpace()
	{

		return $this->name_space;
	}

	/**
	 * Get the [status] column value.
	 * 
	 * @return     string
	 */
	public function getStatus()
	{

		return $this->status;
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
			$this->modifiedColumns[] = NCRegistryPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [access_time] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setAccessTime($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [access_time] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->access_time !== $ts) {
			$this->access_time = $ts;
			$this->modifiedColumns[] = NCRegistryPeer::ACCESS_TIME;
		}

	} // setAccessTime()

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
			$this->modifiedColumns[] = NCRegistryPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [key_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setKeyName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->key_name !== $v) {
			$this->key_name = $v;
			$this->modifiedColumns[] = NCRegistryPeer::KEY_NAME;
		}

	} // setKeyName()

	/**
	 * Set the value of [key_type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setKeyType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->key_type !== $v) {
			$this->key_type = $v;
			$this->modifiedColumns[] = NCRegistryPeer::KEY_TYPE;
		}

	} // setKeyType()

	/**
	 * Set the value of [key_value] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setKeyValue($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->key_value !== $v) {
			$this->key_value = $v;
			$this->modifiedColumns[] = NCRegistryPeer::KEY_VALUE;
		}

	} // setKeyValue()

	/**
	 * Set the value of [name_space] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNameSpace($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->name_space !== $v) {
			$this->name_space = $v;
			$this->modifiedColumns[] = NCRegistryPeer::NAME_SPACE;
		}

	} // setNameSpace()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = NCRegistryPeer::STATUS;
		}

	} // setStatus()

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

			$this->access_time = $rs->getDate($startcol + 1, null);

			$this->description = $rs->getString($startcol + 2);

			$this->key_name = $rs->getString($startcol + 3);

			$this->key_type = $rs->getString($startcol + 4);

			$this->key_value = $rs->getString($startcol + 5);

			$this->name_space = $rs->getString($startcol + 6);

			$this->status = $rs->getString($startcol + 7);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 8; // 8 = NCRegistryPeer::NUM_COLUMNS - NCRegistryPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating NCRegistry object", $e);
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
			$con = Propel::getConnection(NCRegistryPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			NCRegistryPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(NCRegistryPeer::DATABASE_NAME);
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


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = NCRegistryPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += NCRegistryPeer::doUpdate($this, $con);
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


			if (($retval = NCRegistryPeer::doValidate($this, $columns)) !== true) {
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
		$pos = NCRegistryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAccessTime();
				break;
			case 2:
				return $this->getDescription();
				break;
			case 3:
				return $this->getKeyName();
				break;
			case 4:
				return $this->getKeyType();
				break;
			case 5:
				return $this->getKeyValue();
				break;
			case 6:
				return $this->getNameSpace();
				break;
			case 7:
				return $this->getStatus();
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
		$keys = NCRegistryPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getAccessTime(),
			$keys[2] => $this->getDescription(),
			$keys[3] => $this->getKeyName(),
			$keys[4] => $this->getKeyType(),
			$keys[5] => $this->getKeyValue(),
			$keys[6] => $this->getNameSpace(),
			$keys[7] => $this->getStatus(),
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
		$pos = NCRegistryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAccessTime($value);
				break;
			case 2:
				$this->setDescription($value);
				break;
			case 3:
				$this->setKeyName($value);
				break;
			case 4:
				$this->setKeyType($value);
				break;
			case 5:
				$this->setKeyValue($value);
				break;
			case 6:
				$this->setNameSpace($value);
				break;
			case 7:
				$this->setStatus($value);
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
		$keys = NCRegistryPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAccessTime($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDescription($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setKeyName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setKeyType($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setKeyValue($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setNameSpace($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setStatus($arr[$keys[7]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(NCRegistryPeer::DATABASE_NAME);

		if ($this->isColumnModified(NCRegistryPeer::ID)) $criteria->add(NCRegistryPeer::ID, $this->id);
		if ($this->isColumnModified(NCRegistryPeer::ACCESS_TIME)) $criteria->add(NCRegistryPeer::ACCESS_TIME, $this->access_time);
		if ($this->isColumnModified(NCRegistryPeer::DESCRIPTION)) $criteria->add(NCRegistryPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(NCRegistryPeer::KEY_NAME)) $criteria->add(NCRegistryPeer::KEY_NAME, $this->key_name);
		if ($this->isColumnModified(NCRegistryPeer::KEY_TYPE)) $criteria->add(NCRegistryPeer::KEY_TYPE, $this->key_type);
		if ($this->isColumnModified(NCRegistryPeer::KEY_VALUE)) $criteria->add(NCRegistryPeer::KEY_VALUE, $this->key_value);
		if ($this->isColumnModified(NCRegistryPeer::NAME_SPACE)) $criteria->add(NCRegistryPeer::NAME_SPACE, $this->name_space);
		if ($this->isColumnModified(NCRegistryPeer::STATUS)) $criteria->add(NCRegistryPeer::STATUS, $this->status);

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
		$criteria = new Criteria(NCRegistryPeer::DATABASE_NAME);

		$criteria->add(NCRegistryPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of NCRegistry (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAccessTime($this->access_time);

		$copyObj->setDescription($this->description);

		$copyObj->setKeyName($this->key_name);

		$copyObj->setKeyType($this->key_type);

		$copyObj->setKeyValue($this->key_value);

		$copyObj->setNameSpace($this->name_space);

		$copyObj->setStatus($this->status);


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
	 * @return     NCRegistry Clone of current object.
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
	 * @return     NCRegistryPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new NCRegistryPeer();
		}
		return self::$peer;
	}

} // BaseNCRegistry
