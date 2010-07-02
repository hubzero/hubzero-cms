<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/curation/NCCuratedContactLogPeer.php';

/**
 * Base class that represents a row from the 'CURATED_CONTACT_LOG' table.
 *
 * 
 *
 * @package    lib.data.curation.om
 */
abstract class BaseNCCuratedContactLog extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        NCCuratedContactLogPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the contact_date field.
	 * @var        int
	 */
	protected $contact_date;


	/**
	 * The value for the contact_first_name field.
	 * @var        string
	 */
	protected $contact_first_name;


	/**
	 * The value for the contact_last_name field.
	 * @var        string
	 */
	protected $contact_last_name;


	/**
	 * The value for the contact_method field.
	 * @var        string
	 */
	protected $contact_method;


	/**
	 * The value for the contact_reason field.
	 * @var        string
	 */
	protected $contact_reason;


	/**
	 * The value for the contact_resolution field.
	 * @var        string
	 */
	protected $contact_resolution;


	/**
	 * The value for the contact_status field.
	 * @var        string
	 */
	protected $contact_status;


	/**
	 * The value for the created_by field.
	 * @var        string
	 */
	protected $created_by;


	/**
	 * The value for the created_date field.
	 * @var        int
	 */
	protected $created_date;


	/**
	 * The value for the object_id field.
	 * @var        double
	 */
	protected $object_id;


	/**
	 * The value for the phone_number field.
	 * @var        string
	 */
	protected $phone_number;

	/**
	 * @var        NCCuratedObjects
	 */
	protected $aNCCuratedObjects;

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
	 * Get the [optionally formatted] [contact_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getContactDate($format = '%Y-%m-%d')
	{

		if ($this->contact_date === null || $this->contact_date === '') {
			return null;
		} elseif (!is_int($this->contact_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->contact_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [contact_date] as date/time value: " . var_export($this->contact_date, true));
			}
		} else {
			$ts = $this->contact_date;
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
	 * Get the [contact_first_name] column value.
	 * 
	 * @return     string
	 */
	public function getContactFirstName()
	{

		return $this->contact_first_name;
	}

	/**
	 * Get the [contact_last_name] column value.
	 * 
	 * @return     string
	 */
	public function getContactLastName()
	{

		return $this->contact_last_name;
	}

	/**
	 * Get the [contact_method] column value.
	 * 
	 * @return     string
	 */
	public function getContactMethod()
	{

		return $this->contact_method;
	}

	/**
	 * Get the [contact_reason] column value.
	 * 
	 * @return     string
	 */
	public function getContactReason()
	{

		return $this->contact_reason;
	}

	/**
	 * Get the [contact_resolution] column value.
	 * 
	 * @return     string
	 */
	public function getContactResolution()
	{

		return $this->contact_resolution;
	}

	/**
	 * Get the [contact_status] column value.
	 * 
	 * @return     string
	 */
	public function getContactStatus()
	{

		return $this->contact_status;
	}

	/**
	 * Get the [created_by] column value.
	 * 
	 * @return     string
	 */
	public function getCreatedBy()
	{

		return $this->created_by;
	}

	/**
	 * Get the [optionally formatted] [created_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getCreatedDate($format = '%Y-%m-%d')
	{

		if ($this->created_date === null || $this->created_date === '') {
			return null;
		} elseif (!is_int($this->created_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->created_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [created_date] as date/time value: " . var_export($this->created_date, true));
			}
		} else {
			$ts = $this->created_date;
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
	 * Get the [object_id] column value.
	 * 
	 * @return     double
	 */
	public function getObjectId()
	{

		return $this->object_id;
	}

	/**
	 * Get the [phone_number] column value.
	 * 
	 * @return     string
	 */
	public function getPhoneNumber()
	{

		return $this->phone_number;
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
			$this->modifiedColumns[] = NCCuratedContactLogPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [contact_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setContactDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [contact_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->contact_date !== $ts) {
			$this->contact_date = $ts;
			$this->modifiedColumns[] = NCCuratedContactLogPeer::CONTACT_DATE;
		}

	} // setContactDate()

	/**
	 * Set the value of [contact_first_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactFirstName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->contact_first_name !== $v) {
			$this->contact_first_name = $v;
			$this->modifiedColumns[] = NCCuratedContactLogPeer::CONTACT_FIRST_NAME;
		}

	} // setContactFirstName()

	/**
	 * Set the value of [contact_last_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactLastName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->contact_last_name !== $v) {
			$this->contact_last_name = $v;
			$this->modifiedColumns[] = NCCuratedContactLogPeer::CONTACT_LAST_NAME;
		}

	} // setContactLastName()

	/**
	 * Set the value of [contact_method] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactMethod($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->contact_method !== $v) {
			$this->contact_method = $v;
			$this->modifiedColumns[] = NCCuratedContactLogPeer::CONTACT_METHOD;
		}

	} // setContactMethod()

	/**
	 * Set the value of [contact_reason] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactReason($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->contact_reason) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->contact_reason !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->contact_reason = $obj;
			$this->modifiedColumns[] = NCCuratedContactLogPeer::CONTACT_REASON;
		}

	} // setContactReason()

	/**
	 * Set the value of [contact_resolution] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactResolution($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->contact_resolution) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->contact_resolution !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->contact_resolution = $obj;
			$this->modifiedColumns[] = NCCuratedContactLogPeer::CONTACT_RESOLUTION;
		}

	} // setContactResolution()

	/**
	 * Set the value of [contact_status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->contact_status !== $v) {
			$this->contact_status = $v;
			$this->modifiedColumns[] = NCCuratedContactLogPeer::CONTACT_STATUS;
		}

	} // setContactStatus()

	/**
	 * Set the value of [created_by] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCreatedBy($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->created_by !== $v) {
			$this->created_by = $v;
			$this->modifiedColumns[] = NCCuratedContactLogPeer::CREATED_BY;
		}

	} // setCreatedBy()

	/**
	 * Set the value of [created_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setCreatedDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [created_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->created_date !== $ts) {
			$this->created_date = $ts;
			$this->modifiedColumns[] = NCCuratedContactLogPeer::CREATED_DATE;
		}

	} // setCreatedDate()

	/**
	 * Set the value of [object_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setObjectId($v)
	{

		if ($this->object_id !== $v) {
			$this->object_id = $v;
			$this->modifiedColumns[] = NCCuratedContactLogPeer::OBJECT_ID;
		}

		if ($this->aNCCuratedObjects !== null && $this->aNCCuratedObjects->getObjectId() !== $v) {
			$this->aNCCuratedObjects = null;
		}

	} // setObjectId()

	/**
	 * Set the value of [phone_number] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPhoneNumber($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->phone_number !== $v) {
			$this->phone_number = $v;
			$this->modifiedColumns[] = NCCuratedContactLogPeer::PHONE_NUMBER;
		}

	} // setPhoneNumber()

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

			$this->contact_date = $rs->getDate($startcol + 1, null);

			$this->contact_first_name = $rs->getString($startcol + 2);

			$this->contact_last_name = $rs->getString($startcol + 3);

			$this->contact_method = $rs->getString($startcol + 4);

			$this->contact_reason = $rs->getClob($startcol + 5);

			$this->contact_resolution = $rs->getClob($startcol + 6);

			$this->contact_status = $rs->getString($startcol + 7);

			$this->created_by = $rs->getString($startcol + 8);

			$this->created_date = $rs->getDate($startcol + 9, null);

			$this->object_id = $rs->getFloat($startcol + 10);

			$this->phone_number = $rs->getString($startcol + 11);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 12; // 12 = NCCuratedContactLogPeer::NUM_COLUMNS - NCCuratedContactLogPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating NCCuratedContactLog object", $e);
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
			$con = Propel::getConnection(NCCuratedContactLogPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			NCCuratedContactLogPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(NCCuratedContactLogPeer::DATABASE_NAME);
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

			if ($this->aNCCuratedObjects !== null) {
				if ($this->aNCCuratedObjects->isModified()) {
					$affectedRows += $this->aNCCuratedObjects->save($con);
				}
				$this->setNCCuratedObjects($this->aNCCuratedObjects);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = NCCuratedContactLogPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += NCCuratedContactLogPeer::doUpdate($this, $con);
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

			if ($this->aNCCuratedObjects !== null) {
				if (!$this->aNCCuratedObjects->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aNCCuratedObjects->getValidationFailures());
				}
			}


			if (($retval = NCCuratedContactLogPeer::doValidate($this, $columns)) !== true) {
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
		$pos = NCCuratedContactLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getContactDate();
				break;
			case 2:
				return $this->getContactFirstName();
				break;
			case 3:
				return $this->getContactLastName();
				break;
			case 4:
				return $this->getContactMethod();
				break;
			case 5:
				return $this->getContactReason();
				break;
			case 6:
				return $this->getContactResolution();
				break;
			case 7:
				return $this->getContactStatus();
				break;
			case 8:
				return $this->getCreatedBy();
				break;
			case 9:
				return $this->getCreatedDate();
				break;
			case 10:
				return $this->getObjectId();
				break;
			case 11:
				return $this->getPhoneNumber();
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
		$keys = NCCuratedContactLogPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getContactDate(),
			$keys[2] => $this->getContactFirstName(),
			$keys[3] => $this->getContactLastName(),
			$keys[4] => $this->getContactMethod(),
			$keys[5] => $this->getContactReason(),
			$keys[6] => $this->getContactResolution(),
			$keys[7] => $this->getContactStatus(),
			$keys[8] => $this->getCreatedBy(),
			$keys[9] => $this->getCreatedDate(),
			$keys[10] => $this->getObjectId(),
			$keys[11] => $this->getPhoneNumber(),
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
		$pos = NCCuratedContactLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setContactDate($value);
				break;
			case 2:
				$this->setContactFirstName($value);
				break;
			case 3:
				$this->setContactLastName($value);
				break;
			case 4:
				$this->setContactMethod($value);
				break;
			case 5:
				$this->setContactReason($value);
				break;
			case 6:
				$this->setContactResolution($value);
				break;
			case 7:
				$this->setContactStatus($value);
				break;
			case 8:
				$this->setCreatedBy($value);
				break;
			case 9:
				$this->setCreatedDate($value);
				break;
			case 10:
				$this->setObjectId($value);
				break;
			case 11:
				$this->setPhoneNumber($value);
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
		$keys = NCCuratedContactLogPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setContactDate($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setContactFirstName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setContactLastName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setContactMethod($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setContactReason($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setContactResolution($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setContactStatus($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCreatedBy($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setCreatedDate($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setObjectId($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setPhoneNumber($arr[$keys[11]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(NCCuratedContactLogPeer::DATABASE_NAME);

		if ($this->isColumnModified(NCCuratedContactLogPeer::ID)) $criteria->add(NCCuratedContactLogPeer::ID, $this->id);
		if ($this->isColumnModified(NCCuratedContactLogPeer::CONTACT_DATE)) $criteria->add(NCCuratedContactLogPeer::CONTACT_DATE, $this->contact_date);
		if ($this->isColumnModified(NCCuratedContactLogPeer::CONTACT_FIRST_NAME)) $criteria->add(NCCuratedContactLogPeer::CONTACT_FIRST_NAME, $this->contact_first_name);
		if ($this->isColumnModified(NCCuratedContactLogPeer::CONTACT_LAST_NAME)) $criteria->add(NCCuratedContactLogPeer::CONTACT_LAST_NAME, $this->contact_last_name);
		if ($this->isColumnModified(NCCuratedContactLogPeer::CONTACT_METHOD)) $criteria->add(NCCuratedContactLogPeer::CONTACT_METHOD, $this->contact_method);
		if ($this->isColumnModified(NCCuratedContactLogPeer::CONTACT_REASON)) $criteria->add(NCCuratedContactLogPeer::CONTACT_REASON, $this->contact_reason);
		if ($this->isColumnModified(NCCuratedContactLogPeer::CONTACT_RESOLUTION)) $criteria->add(NCCuratedContactLogPeer::CONTACT_RESOLUTION, $this->contact_resolution);
		if ($this->isColumnModified(NCCuratedContactLogPeer::CONTACT_STATUS)) $criteria->add(NCCuratedContactLogPeer::CONTACT_STATUS, $this->contact_status);
		if ($this->isColumnModified(NCCuratedContactLogPeer::CREATED_BY)) $criteria->add(NCCuratedContactLogPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(NCCuratedContactLogPeer::CREATED_DATE)) $criteria->add(NCCuratedContactLogPeer::CREATED_DATE, $this->created_date);
		if ($this->isColumnModified(NCCuratedContactLogPeer::OBJECT_ID)) $criteria->add(NCCuratedContactLogPeer::OBJECT_ID, $this->object_id);
		if ($this->isColumnModified(NCCuratedContactLogPeer::PHONE_NUMBER)) $criteria->add(NCCuratedContactLogPeer::PHONE_NUMBER, $this->phone_number);

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
		$criteria = new Criteria(NCCuratedContactLogPeer::DATABASE_NAME);

		$criteria->add(NCCuratedContactLogPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of NCCuratedContactLog (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setContactDate($this->contact_date);

		$copyObj->setContactFirstName($this->contact_first_name);

		$copyObj->setContactLastName($this->contact_last_name);

		$copyObj->setContactMethod($this->contact_method);

		$copyObj->setContactReason($this->contact_reason);

		$copyObj->setContactResolution($this->contact_resolution);

		$copyObj->setContactStatus($this->contact_status);

		$copyObj->setCreatedBy($this->created_by);

		$copyObj->setCreatedDate($this->created_date);

		$copyObj->setObjectId($this->object_id);

		$copyObj->setPhoneNumber($this->phone_number);


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
	 * @return     NCCuratedContactLog Clone of current object.
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
	 * @return     NCCuratedContactLogPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new NCCuratedContactLogPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a NCCuratedObjects object.
	 *
	 * @param      NCCuratedObjects $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setNCCuratedObjects($v)
	{


		if ($v === null) {
			$this->setObjectId(NULL);
		} else {
			$this->setObjectId($v->getObjectId());
		}


		$this->aNCCuratedObjects = $v;
	}


	/**
	 * Get the associated NCCuratedObjects object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     NCCuratedObjects The associated NCCuratedObjects object.
	 * @throws     PropelException
	 */
	public function getNCCuratedObjects($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/curation/om/BaseNCCuratedObjectsPeer.php';

		if ($this->aNCCuratedObjects === null && ($this->object_id > 0)) {

			$this->aNCCuratedObjects = NCCuratedObjectsPeer::retrieveByPK($this->object_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = NCCuratedObjectsPeer::retrieveByPK($this->object_id, $con);
			   $obj->addNCCuratedObjectss($this);
			 */
		}
		return $this->aNCCuratedObjects;
	}

} // BaseNCCuratedContactLog
