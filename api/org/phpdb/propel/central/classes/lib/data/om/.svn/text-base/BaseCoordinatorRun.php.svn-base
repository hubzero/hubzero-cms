<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/CoordinatorRunPeer.php';

/**
 * Base class that represents a row from the 'COORDINATOR_RUN' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseCoordinatorRun extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CoordinatorRunPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the coordinator_id field.
	 * @var        double
	 */
	protected $coordinator_id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the title field.
	 * @var        string
	 */
	protected $title;


	/**
	 * The value for the objective field.
	 * @var        string
	 */
	protected $objective;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the start_date field.
	 * @var        int
	 */
	protected $start_date;


	/**
	 * The value for the end_date field.
	 * @var        int
	 */
	protected $end_date;

	/**
	 * @var        Coordinator
	 */
	protected $aCoordinator;

	/**
	 * Collection to store aggregation of collCoordinatorRunExperiments.
	 * @var        array
	 */
	protected $collCoordinatorRunExperiments;

	/**
	 * The criteria used to select the current contents of collCoordinatorRunExperiments.
	 * @var        Criteria
	 */
	protected $lastCoordinatorRunExperimentCriteria = null;

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
	 * Get the [coordinator_id] column value.
	 * 
	 * @return     double
	 */
	public function getCoordinatorId()
	{

		return $this->coordinator_id;
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
	 * Get the [title] column value.
	 * 
	 * @return     string
	 */
	public function getTitle()
	{

		return $this->title;
	}

	/**
	 * Get the [objective] column value.
	 * 
	 * @return     string
	 */
	public function getObjective()
	{

		return $this->objective;
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
	 * Get the [optionally formatted] [start_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getStartDate($format = '%Y-%m-%d')
	{

		if ($this->start_date === null || $this->start_date === '') {
			return null;
		} elseif (!is_int($this->start_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->start_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [start_date] as date/time value: " . var_export($this->start_date, true));
			}
		} else {
			$ts = $this->start_date;
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
	 * Get the [optionally formatted] [end_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getEndDate($format = '%Y-%m-%d')
	{

		if ($this->end_date === null || $this->end_date === '') {
			return null;
		} elseif (!is_int($this->end_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->end_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [end_date] as date/time value: " . var_export($this->end_date, true));
			}
		} else {
			$ts = $this->end_date;
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
	 * Set the value of [id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CoordinatorRunPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [coordinator_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCoordinatorId($v)
	{

		if ($this->coordinator_id !== $v) {
			$this->coordinator_id = $v;
			$this->modifiedColumns[] = CoordinatorRunPeer::COORDINATOR_ID;
		}

		if ($this->aCoordinator !== null && $this->aCoordinator->getId() !== $v) {
			$this->aCoordinator = null;
		}

	} // setCoordinatorId()

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
			$this->modifiedColumns[] = CoordinatorRunPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [title] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTitle($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->title !== $v) {
			$this->title = $v;
			$this->modifiedColumns[] = CoordinatorRunPeer::TITLE;
		}

	} // setTitle()

	/**
	 * Set the value of [objective] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setObjective($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->objective !== $v) {
			$this->objective = $v;
			$this->modifiedColumns[] = CoordinatorRunPeer::OBJECTIVE;
		}

	} // setObjective()

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
			$this->modifiedColumns[] = CoordinatorRunPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [start_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setStartDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [start_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->start_date !== $ts) {
			$this->start_date = $ts;
			$this->modifiedColumns[] = CoordinatorRunPeer::START_DATE;
		}

	} // setStartDate()

	/**
	 * Set the value of [end_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setEndDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [end_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->end_date !== $ts) {
			$this->end_date = $ts;
			$this->modifiedColumns[] = CoordinatorRunPeer::END_DATE;
		}

	} // setEndDate()

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

			$this->coordinator_id = $rs->getFloat($startcol + 1);

			$this->name = $rs->getString($startcol + 2);

			$this->title = $rs->getString($startcol + 3);

			$this->objective = $rs->getString($startcol + 4);

			$this->description = $rs->getString($startcol + 5);

			$this->start_date = $rs->getDate($startcol + 6, null);

			$this->end_date = $rs->getDate($startcol + 7, null);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 8; // 8 = CoordinatorRunPeer::NUM_COLUMNS - CoordinatorRunPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CoordinatorRun object", $e);
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
			$con = Propel::getConnection(CoordinatorRunPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			CoordinatorRunPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(CoordinatorRunPeer::DATABASE_NAME);
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

			if ($this->aCoordinator !== null) {
				if ($this->aCoordinator->isModified()) {
					$affectedRows += $this->aCoordinator->save($con);
				}
				$this->setCoordinator($this->aCoordinator);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = CoordinatorRunPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += CoordinatorRunPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCoordinatorRunExperiments !== null) {
				foreach($this->collCoordinatorRunExperiments as $referrerFK) {
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

			if ($this->aCoordinator !== null) {
				if (!$this->aCoordinator->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCoordinator->getValidationFailures());
				}
			}


			if (($retval = CoordinatorRunPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCoordinatorRunExperiments !== null) {
					foreach($this->collCoordinatorRunExperiments as $referrerFK) {
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
		$pos = CoordinatorRunPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCoordinatorId();
				break;
			case 2:
				return $this->getName();
				break;
			case 3:
				return $this->getTitle();
				break;
			case 4:
				return $this->getObjective();
				break;
			case 5:
				return $this->getDescription();
				break;
			case 6:
				return $this->getStartDate();
				break;
			case 7:
				return $this->getEndDate();
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
		$keys = CoordinatorRunPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCoordinatorId(),
			$keys[2] => $this->getName(),
			$keys[3] => $this->getTitle(),
			$keys[4] => $this->getObjective(),
			$keys[5] => $this->getDescription(),
			$keys[6] => $this->getStartDate(),
			$keys[7] => $this->getEndDate(),
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
		$pos = CoordinatorRunPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCoordinatorId($value);
				break;
			case 2:
				$this->setName($value);
				break;
			case 3:
				$this->setTitle($value);
				break;
			case 4:
				$this->setObjective($value);
				break;
			case 5:
				$this->setDescription($value);
				break;
			case 6:
				$this->setStartDate($value);
				break;
			case 7:
				$this->setEndDate($value);
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
		$keys = CoordinatorRunPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCoordinatorId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setTitle($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setObjective($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDescription($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setStartDate($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setEndDate($arr[$keys[7]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CoordinatorRunPeer::DATABASE_NAME);

		if ($this->isColumnModified(CoordinatorRunPeer::ID)) $criteria->add(CoordinatorRunPeer::ID, $this->id);
		if ($this->isColumnModified(CoordinatorRunPeer::COORDINATOR_ID)) $criteria->add(CoordinatorRunPeer::COORDINATOR_ID, $this->coordinator_id);
		if ($this->isColumnModified(CoordinatorRunPeer::NAME)) $criteria->add(CoordinatorRunPeer::NAME, $this->name);
		if ($this->isColumnModified(CoordinatorRunPeer::TITLE)) $criteria->add(CoordinatorRunPeer::TITLE, $this->title);
		if ($this->isColumnModified(CoordinatorRunPeer::OBJECTIVE)) $criteria->add(CoordinatorRunPeer::OBJECTIVE, $this->objective);
		if ($this->isColumnModified(CoordinatorRunPeer::DESCRIPTION)) $criteria->add(CoordinatorRunPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(CoordinatorRunPeer::START_DATE)) $criteria->add(CoordinatorRunPeer::START_DATE, $this->start_date);
		if ($this->isColumnModified(CoordinatorRunPeer::END_DATE)) $criteria->add(CoordinatorRunPeer::END_DATE, $this->end_date);

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
		$criteria = new Criteria(CoordinatorRunPeer::DATABASE_NAME);

		$criteria->add(CoordinatorRunPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of CoordinatorRun (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCoordinatorId($this->coordinator_id);

		$copyObj->setName($this->name);

		$copyObj->setTitle($this->title);

		$copyObj->setObjective($this->objective);

		$copyObj->setDescription($this->description);

		$copyObj->setStartDate($this->start_date);

		$copyObj->setEndDate($this->end_date);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getCoordinatorRunExperiments() as $relObj) {
				$copyObj->addCoordinatorRunExperiment($relObj->copy($deepCopy));
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
	 * @return     CoordinatorRun Clone of current object.
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
	 * @return     CoordinatorRunPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CoordinatorRunPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Coordinator object.
	 *
	 * @param      Coordinator $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setCoordinator($v)
	{


		if ($v === null) {
			$this->setCoordinatorId(NULL);
		} else {
			$this->setCoordinatorId($v->getId());
		}


		$this->aCoordinator = $v;
	}


	/**
	 * Get the associated Coordinator object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Coordinator The associated Coordinator object.
	 * @throws     PropelException
	 */
	public function getCoordinator($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseCoordinatorPeer.php';

		if ($this->aCoordinator === null && ($this->coordinator_id > 0)) {

			$this->aCoordinator = CoordinatorPeer::retrieveByPK($this->coordinator_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = CoordinatorPeer::retrieveByPK($this->coordinator_id, $con);
			   $obj->addCoordinators($this);
			 */
		}
		return $this->aCoordinator;
	}

	/**
	 * Temporary storage of collCoordinatorRunExperiments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinatorRunExperiments()
	{
		if ($this->collCoordinatorRunExperiments === null) {
			$this->collCoordinatorRunExperiments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinatorRun has previously
	 * been saved, it will retrieve related CoordinatorRunExperiments from storage.
	 * If this CoordinatorRun is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinatorRunExperiments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorRunExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinatorRunExperiments === null) {
			if ($this->isNew()) {
			   $this->collCoordinatorRunExperiments = array();
			} else {

				$criteria->add(CoordinatorRunExperimentPeer::COORDINATOR_RUN_ID, $this->getId());

				CoordinatorRunExperimentPeer::addSelectColumns($criteria);
				$this->collCoordinatorRunExperiments = CoordinatorRunExperimentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinatorRunExperimentPeer::COORDINATOR_RUN_ID, $this->getId());

				CoordinatorRunExperimentPeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinatorRunExperimentCriteria) || !$this->lastCoordinatorRunExperimentCriteria->equals($criteria)) {
					$this->collCoordinatorRunExperiments = CoordinatorRunExperimentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinatorRunExperimentCriteria = $criteria;
		return $this->collCoordinatorRunExperiments;
	}

	/**
	 * Returns the number of related CoordinatorRunExperiments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinatorRunExperiments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorRunExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(CoordinatorRunExperimentPeer::COORDINATOR_RUN_ID, $this->getId());

		return CoordinatorRunExperimentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a CoordinatorRunExperiment object to this object
	 * through the CoordinatorRunExperiment foreign key attribute
	 *
	 * @param      CoordinatorRunExperiment $l CoordinatorRunExperiment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCoordinatorRunExperiment(CoordinatorRunExperiment $l)
	{
		$this->collCoordinatorRunExperiments[] = $l;
		$l->setCoordinatorRun($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinatorRun is new, it will return
	 * an empty collection; or if this CoordinatorRun has previously
	 * been saved, it will retrieve related CoordinatorRunExperiments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinatorRun.
	 */
	public function getCoordinatorRunExperimentsJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorRunExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinatorRunExperiments === null) {
			if ($this->isNew()) {
				$this->collCoordinatorRunExperiments = array();
			} else {

				$criteria->add(CoordinatorRunExperimentPeer::COORDINATOR_RUN_ID, $this->getId());

				$this->collCoordinatorRunExperiments = CoordinatorRunExperimentPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinatorRunExperimentPeer::COORDINATOR_RUN_ID, $this->getId());

			if (!isset($this->lastCoordinatorRunExperimentCriteria) || !$this->lastCoordinatorRunExperimentCriteria->equals($criteria)) {
				$this->collCoordinatorRunExperiments = CoordinatorRunExperimentPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastCoordinatorRunExperimentCriteria = $criteria;

		return $this->collCoordinatorRunExperiments;
	}

} // BaseCoordinatorRun
