<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/util/LogEntryPeer.php';

/**
 * Base class that represents a row from the 'WEB_LOG_ENTRY' table.
 *
 * 
 *
 * @package    lib.data.util.om
 */
abstract class BaseLogEntry extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        LogEntryPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the person_id field.
	 * @var        double
	 */
	protected $person_id;


	/**
	 * The value for the auto_create_date field.
	 * @var        int
	 */
	protected $auto_create_date;


	/**
	 * The value for the message field.
	 * @var        string
	 */
	protected $message;

	/**
	 * @var        Person
	 */
	protected $aPerson;

	/**
	 * Collection to store aggregation of collLogDetails.
	 * @var        array
	 */
	protected $collLogDetails;

	/**
	 * The criteria used to select the current contents of collLogDetails.
	 * @var        Criteria
	 */
	protected $lastLogDetailCriteria = null;

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
	 * Get the [person_id] column value.
	 * 
	 * @return     double
	 */
	public function getPersonId()
	{

		return $this->person_id;
	}

	/**
	 * Get the [optionally formatted] [auto_create_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getCreatedOn($format = 'Y-m-d H:i:s')
	{

		if ($this->auto_create_date === null || $this->auto_create_date === '') {
			return null;
		} elseif (!is_int($this->auto_create_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->auto_create_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [auto_create_date] as date/time value: " . var_export($this->auto_create_date, true));
			}
		} else {
			$ts = $this->auto_create_date;
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
	 * Get the [message] column value.
	 * 
	 * @return     string
	 */
	public function getMessage()
	{

		return $this->message;
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
			$this->modifiedColumns[] = LogEntryPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [person_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPersonId($v)
	{

		if ($this->person_id !== $v) {
			$this->person_id = $v;
			$this->modifiedColumns[] = LogEntryPeer::PERSON_ID;
		}

		if ($this->aPerson !== null && $this->aPerson->getId() !== $v) {
			$this->aPerson = null;
		}

	} // setPersonId()

	/**
	 * Set the value of [auto_create_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setCreatedOn($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [auto_create_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->auto_create_date !== $ts) {
			$this->auto_create_date = $ts;
			$this->modifiedColumns[] = LogEntryPeer::AUTO_CREATE_DATE;
		}

	} // setCreatedOn()

	/**
	 * Set the value of [message] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setMessage($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->message !== $v) {
			$this->message = $v;
			$this->modifiedColumns[] = LogEntryPeer::MESSAGE;
		}

	} // setMessage()

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

			$this->person_id = $rs->getFloat($startcol + 1);

			$this->auto_create_date = $rs->getTimestamp($startcol + 2, null);

			$this->message = $rs->getString($startcol + 3);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 4; // 4 = LogEntryPeer::NUM_COLUMNS - LogEntryPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating LogEntry object", $e);
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
			$con = Propel::getConnection(LogEntryPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			LogEntryPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(LogEntryPeer::DATABASE_NAME);
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

			if ($this->aPerson !== null) {
				if ($this->aPerson->isModified()) {
					$affectedRows += $this->aPerson->save($con);
				}
				$this->setPerson($this->aPerson);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = LogEntryPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += LogEntryPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collLogDetails !== null) {
				foreach($this->collLogDetails as $referrerFK) {
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

			if ($this->aPerson !== null) {
				if (!$this->aPerson->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aPerson->getValidationFailures());
				}
			}


			if (($retval = LogEntryPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collLogDetails !== null) {
					foreach($this->collLogDetails as $referrerFK) {
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
		$pos = LogEntryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPersonId();
				break;
			case 2:
				return $this->getCreatedOn();
				break;
			case 3:
				return $this->getMessage();
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
		$keys = LogEntryPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPersonId(),
			$keys[2] => $this->getCreatedOn(),
			$keys[3] => $this->getMessage(),
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
		$pos = LogEntryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPersonId($value);
				break;
			case 2:
				$this->setCreatedOn($value);
				break;
			case 3:
				$this->setMessage($value);
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
		$keys = LogEntryPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPersonId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCreatedOn($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setMessage($arr[$keys[3]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(LogEntryPeer::DATABASE_NAME);

		if ($this->isColumnModified(LogEntryPeer::ID)) $criteria->add(LogEntryPeer::ID, $this->id);
		if ($this->isColumnModified(LogEntryPeer::PERSON_ID)) $criteria->add(LogEntryPeer::PERSON_ID, $this->person_id);
		if ($this->isColumnModified(LogEntryPeer::AUTO_CREATE_DATE)) $criteria->add(LogEntryPeer::AUTO_CREATE_DATE, $this->auto_create_date);
		if ($this->isColumnModified(LogEntryPeer::MESSAGE)) $criteria->add(LogEntryPeer::MESSAGE, $this->message);

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
		$criteria = new Criteria(LogEntryPeer::DATABASE_NAME);

		$criteria->add(LogEntryPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of LogEntry (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPersonId($this->person_id);

		$copyObj->setCreatedOn($this->auto_create_date);

		$copyObj->setMessage($this->message);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getLogDetails() as $relObj) {
				$copyObj->addLogDetail($relObj->copy($deepCopy));
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
	 * @return     LogEntry Clone of current object.
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
	 * @return     LogEntryPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new LogEntryPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Person object.
	 *
	 * @param      Person $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setPerson($v)
	{


		if ($v === null) {
			$this->setPersonId(NULL);
		} else {
			$this->setPersonId($v->getId());
		}


		$this->aPerson = $v;
	}


	/**
	 * Get the associated Person object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Person The associated Person object.
	 * @throws     PropelException
	 */
	public function getPerson($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BasePersonPeer.php';

		if ($this->aPerson === null && ($this->person_id > 0)) {

			$this->aPerson = PersonPeer::retrieveByPK($this->person_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = PersonPeer::retrieveByPK($this->person_id, $con);
			   $obj->addPersons($this);
			 */
		}
		return $this->aPerson;
	}

	/**
	 * Temporary storage of collLogDetails to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLogDetails()
	{
		if ($this->collLogDetails === null) {
			$this->collLogDetails = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this LogEntry has previously
	 * been saved, it will retrieve related LogDetails from storage.
	 * If this LogEntry is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLogDetails($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/util/om/BaseLogDetailPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLogDetails === null) {
			if ($this->isNew()) {
			   $this->collLogDetails = array();
			} else {

				$criteria->add(LogDetailPeer::ENTRY_ID, $this->getId());

				LogDetailPeer::addSelectColumns($criteria);
				$this->collLogDetails = LogDetailPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LogDetailPeer::ENTRY_ID, $this->getId());

				LogDetailPeer::addSelectColumns($criteria);
				if (!isset($this->lastLogDetailCriteria) || !$this->lastLogDetailCriteria->equals($criteria)) {
					$this->collLogDetails = LogDetailPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLogDetailCriteria = $criteria;
		return $this->collLogDetails;
	}

	/**
	 * Returns the number of related LogDetails.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLogDetails($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/util/om/BaseLogDetailPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(LogDetailPeer::ENTRY_ID, $this->getId());

		return LogDetailPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a LogDetail object to this object
	 * through the LogDetail foreign key attribute
	 *
	 * @param      LogDetail $l LogDetail
	 * @return     void
	 * @throws     PropelException
	 */
	public function addLogDetail(LogDetail $l)
	{
		$this->collLogDetails[] = $l;
		$l->setLogEntry($this);
	}

} // BaseLogEntry
