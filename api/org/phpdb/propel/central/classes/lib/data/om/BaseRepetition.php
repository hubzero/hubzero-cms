<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/RepetitionPeer.php';

/**
 * Base class that represents a row from the 'REPETITION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseRepetition extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        RepetitionPeer
	 */
	protected static $peer;


	/**
	 * The value for the repid field.
	 * @var        double
	 */
	protected $repid;


	/**
	 * The value for the curation_status field.
	 * @var        string
	 */
	protected $curation_status;


	/**
	 * The value for the deleted field.
	 * @var        double
	 */
	protected $deleted;


	/**
	 * The value for the end_date field.
	 * @var        int
	 */
	protected $end_date;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the start_date field.
	 * @var        int
	 */
	protected $start_date;


	/**
	 * The value for the status field.
	 * @var        string
	 */
	protected $status;


	/**
	 * The value for the trialid field.
	 * @var        double
	 */
	protected $trialid;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the title field.
	 * @var        string
	 */
	protected $title;


	/**
	 * The value for the creator_id field.
	 * @var        double
	 */
	protected $creator_id;


	/**
	 * The value for the created_date field.
	 * @var        int
	 */
	protected $created_date;


	/**
	 * The value for the modified_by_id field.
	 * @var        double
	 */
	protected $modified_by_id;


	/**
	 * The value for the modified_date field.
	 * @var        int
	 */
	protected $modified_date;


	/**
	 * The value for the app_id field.
	 * @var        double
	 */
	protected $app_id;

	/**
	 * @var        Person
	 */
	protected $aPersonRelatedByCreatorId;

	/**
	 * @var        Person
	 */
	protected $aPersonRelatedByModifiedById;

	/**
	 * @var        Trial
	 */
	protected $aTrial;

	/**
	 * Collection to store aggregation of collDataFileLinks.
	 * @var        array
	 */
	protected $collDataFileLinks;

	/**
	 * The criteria used to select the current contents of collDataFileLinks.
	 * @var        Criteria
	 */
	protected $lastDataFileLinkCriteria = null;

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
	 * Get the [repid] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->repid;
	}

	/**
	 * Get the [curation_status] column value.
	 * 
	 * @return     string
	 */
	public function getCurationStatus()
	{

		return $this->curation_status;
	}

	/**
	 * Get the [deleted] column value.
	 * 
	 * @return     double
	 */
	public function getDeleted()
	{

		return $this->deleted;
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
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{

		return $this->name;
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
	 * Get the [status] column value.
	 * 
	 * @return     string
	 */
	public function getStatus()
	{

		return $this->status;
	}

	/**
	 * Get the [trialid] column value.
	 * 
	 * @return     double
	 */
	public function getTrialId()
	{

		return $this->trialid;
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
	 * Get the [title] column value.
	 * 
	 * @return     string
	 */
	public function getTitle()
	{

		return $this->title;
	}

	/**
	 * Get the [creator_id] column value.
	 * 
	 * @return     double
	 */
	public function getCreatorId()
	{

		return $this->creator_id;
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
	 * Get the [modified_by_id] column value.
	 * 
	 * @return     double
	 */
	public function getModifiedById()
	{

		return $this->modified_by_id;
	}

	/**
	 * Get the [optionally formatted] [modified_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getModifiedDate($format = '%Y-%m-%d')
	{

		if ($this->modified_date === null || $this->modified_date === '') {
			return null;
		} elseif (!is_int($this->modified_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->modified_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [modified_date] as date/time value: " . var_export($this->modified_date, true));
			}
		} else {
			$ts = $this->modified_date;
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
	 * Get the [app_id] column value.
	 * 
	 * @return     double
	 */
	public function getAppId()
	{

		return $this->app_id;
	}

	/**
	 * Set the value of [repid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->repid !== $v) {
			$this->repid = $v;
			$this->modifiedColumns[] = RepetitionPeer::REPID;
		}

	} // setId()

	/**
	 * Set the value of [curation_status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCurationStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->curation_status !== $v) {
			$this->curation_status = $v;
			$this->modifiedColumns[] = RepetitionPeer::CURATION_STATUS;
		}

	} // setCurationStatus()

	/**
	 * Set the value of [deleted] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDeleted($v)
	{

		if ($this->deleted !== $v) {
			$this->deleted = $v;
			$this->modifiedColumns[] = RepetitionPeer::DELETED;
		}

	} // setDeleted()

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
			$this->modifiedColumns[] = RepetitionPeer::END_DATE;
		}

	} // setEndDate()

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
			$this->modifiedColumns[] = RepetitionPeer::NAME;
		}

	} // setName()

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
			$this->modifiedColumns[] = RepetitionPeer::START_DATE;
		}

	} // setStartDate()

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
			$this->modifiedColumns[] = RepetitionPeer::STATUS;
		}

	} // setStatus()

	/**
	 * Set the value of [trialid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTrialId($v)
	{

		if ($this->trialid !== $v) {
			$this->trialid = $v;
			$this->modifiedColumns[] = RepetitionPeer::TRIALID;
		}

		if ($this->aTrial !== null && $this->aTrial->getId() !== $v) {
			$this->aTrial = null;
		}

	} // setTrialId()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDescription($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->description) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->description !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->description = $obj;
			$this->modifiedColumns[] = RepetitionPeer::DESCRIPTION;
		}

	} // setDescription()

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
			$this->modifiedColumns[] = RepetitionPeer::TITLE;
		}

	} // setTitle()

	/**
	 * Set the value of [creator_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCreatorId($v)
	{

		if ($this->creator_id !== $v) {
			$this->creator_id = $v;
			$this->modifiedColumns[] = RepetitionPeer::CREATOR_ID;
		}

		if ($this->aPersonRelatedByCreatorId !== null && $this->aPersonRelatedByCreatorId->getId() !== $v) {
			$this->aPersonRelatedByCreatorId = null;
		}

	} // setCreatorId()

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
			$this->modifiedColumns[] = RepetitionPeer::CREATED_DATE;
		}

	} // setCreatedDate()

	/**
	 * Set the value of [modified_by_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setModifiedById($v)
	{

		if ($this->modified_by_id !== $v) {
			$this->modified_by_id = $v;
			$this->modifiedColumns[] = RepetitionPeer::MODIFIED_BY_ID;
		}

		if ($this->aPersonRelatedByModifiedById !== null && $this->aPersonRelatedByModifiedById->getId() !== $v) {
			$this->aPersonRelatedByModifiedById = null;
		}

	} // setModifiedById()

	/**
	 * Set the value of [modified_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setModifiedDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [modified_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->modified_date !== $ts) {
			$this->modified_date = $ts;
			$this->modifiedColumns[] = RepetitionPeer::MODIFIED_DATE;
		}

	} // setModifiedDate()

	/**
	 * Set the value of [app_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAppId($v)
	{

		if ($this->app_id !== $v) {
			$this->app_id = $v;
			$this->modifiedColumns[] = RepetitionPeer::APP_ID;
		}

	} // setAppId()

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

			$this->repid = $rs->getFloat($startcol + 0);

			$this->curation_status = $rs->getString($startcol + 1);

			$this->deleted = $rs->getFloat($startcol + 2);

			$this->end_date = $rs->getDate($startcol + 3, null);

			$this->name = $rs->getString($startcol + 4);

			$this->start_date = $rs->getDate($startcol + 5, null);

			$this->status = $rs->getString($startcol + 6);

			$this->trialid = $rs->getFloat($startcol + 7);

			$this->description = $rs->getClob($startcol + 8);

			$this->title = $rs->getString($startcol + 9);

			$this->creator_id = $rs->getFloat($startcol + 10);

			$this->created_date = $rs->getDate($startcol + 11, null);

			$this->modified_by_id = $rs->getFloat($startcol + 12);

			$this->modified_date = $rs->getDate($startcol + 13, null);

			$this->app_id = $rs->getFloat($startcol + 14);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 15; // 15 = RepetitionPeer::NUM_COLUMNS - RepetitionPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Repetition object", $e);
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
			$con = Propel::getConnection(RepetitionPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			RepetitionPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(RepetitionPeer::DATABASE_NAME);
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

			if ($this->aPersonRelatedByCreatorId !== null) {
				if ($this->aPersonRelatedByCreatorId->isModified()) {
					$affectedRows += $this->aPersonRelatedByCreatorId->save($con);
				}
				$this->setPersonRelatedByCreatorId($this->aPersonRelatedByCreatorId);
			}

			if ($this->aPersonRelatedByModifiedById !== null) {
				if ($this->aPersonRelatedByModifiedById->isModified()) {
					$affectedRows += $this->aPersonRelatedByModifiedById->save($con);
				}
				$this->setPersonRelatedByModifiedById($this->aPersonRelatedByModifiedById);
			}

			if ($this->aTrial !== null) {
				if ($this->aTrial->isModified()) {
					$affectedRows += $this->aTrial->save($con);
				}
				$this->setTrial($this->aTrial);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = RepetitionPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += RepetitionPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collDataFileLinks !== null) {
				foreach($this->collDataFileLinks as $referrerFK) {
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

			if ($this->aPersonRelatedByCreatorId !== null) {
				if (!$this->aPersonRelatedByCreatorId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aPersonRelatedByCreatorId->getValidationFailures());
				}
			}

			if ($this->aPersonRelatedByModifiedById !== null) {
				if (!$this->aPersonRelatedByModifiedById->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aPersonRelatedByModifiedById->getValidationFailures());
				}
			}

			if ($this->aTrial !== null) {
				if (!$this->aTrial->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aTrial->getValidationFailures());
				}
			}


			if (($retval = RepetitionPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collDataFileLinks !== null) {
					foreach($this->collDataFileLinks as $referrerFK) {
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
		$pos = RepetitionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCurationStatus();
				break;
			case 2:
				return $this->getDeleted();
				break;
			case 3:
				return $this->getEndDate();
				break;
			case 4:
				return $this->getName();
				break;
			case 5:
				return $this->getStartDate();
				break;
			case 6:
				return $this->getStatus();
				break;
			case 7:
				return $this->getTrialId();
				break;
			case 8:
				return $this->getDescription();
				break;
			case 9:
				return $this->getTitle();
				break;
			case 10:
				return $this->getCreatorId();
				break;
			case 11:
				return $this->getCreatedDate();
				break;
			case 12:
				return $this->getModifiedById();
				break;
			case 13:
				return $this->getModifiedDate();
				break;
			case 14:
				return $this->getAppId();
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
		$keys = RepetitionPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCurationStatus(),
			$keys[2] => $this->getDeleted(),
			$keys[3] => $this->getEndDate(),
			$keys[4] => $this->getName(),
			$keys[5] => $this->getStartDate(),
			$keys[6] => $this->getStatus(),
			$keys[7] => $this->getTrialId(),
			$keys[8] => $this->getDescription(),
			$keys[9] => $this->getTitle(),
			$keys[10] => $this->getCreatorId(),
			$keys[11] => $this->getCreatedDate(),
			$keys[12] => $this->getModifiedById(),
			$keys[13] => $this->getModifiedDate(),
			$keys[14] => $this->getAppId(),
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
		$pos = RepetitionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCurationStatus($value);
				break;
			case 2:
				$this->setDeleted($value);
				break;
			case 3:
				$this->setEndDate($value);
				break;
			case 4:
				$this->setName($value);
				break;
			case 5:
				$this->setStartDate($value);
				break;
			case 6:
				$this->setStatus($value);
				break;
			case 7:
				$this->setTrialId($value);
				break;
			case 8:
				$this->setDescription($value);
				break;
			case 9:
				$this->setTitle($value);
				break;
			case 10:
				$this->setCreatorId($value);
				break;
			case 11:
				$this->setCreatedDate($value);
				break;
			case 12:
				$this->setModifiedById($value);
				break;
			case 13:
				$this->setModifiedDate($value);
				break;
			case 14:
				$this->setAppId($value);
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
		$keys = RepetitionPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCurationStatus($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDeleted($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setEndDate($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setStartDate($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setStatus($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setTrialId($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setDescription($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setTitle($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setCreatorId($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setCreatedDate($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setModifiedById($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setModifiedDate($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setAppId($arr[$keys[14]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(RepetitionPeer::DATABASE_NAME);

		if ($this->isColumnModified(RepetitionPeer::REPID)) $criteria->add(RepetitionPeer::REPID, $this->repid);
		if ($this->isColumnModified(RepetitionPeer::CURATION_STATUS)) $criteria->add(RepetitionPeer::CURATION_STATUS, $this->curation_status);
		if ($this->isColumnModified(RepetitionPeer::DELETED)) $criteria->add(RepetitionPeer::DELETED, $this->deleted);
		if ($this->isColumnModified(RepetitionPeer::END_DATE)) $criteria->add(RepetitionPeer::END_DATE, $this->end_date);
		if ($this->isColumnModified(RepetitionPeer::NAME)) $criteria->add(RepetitionPeer::NAME, $this->name);
		if ($this->isColumnModified(RepetitionPeer::START_DATE)) $criteria->add(RepetitionPeer::START_DATE, $this->start_date);
		if ($this->isColumnModified(RepetitionPeer::STATUS)) $criteria->add(RepetitionPeer::STATUS, $this->status);
		if ($this->isColumnModified(RepetitionPeer::TRIALID)) $criteria->add(RepetitionPeer::TRIALID, $this->trialid);
		if ($this->isColumnModified(RepetitionPeer::DESCRIPTION)) $criteria->add(RepetitionPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(RepetitionPeer::TITLE)) $criteria->add(RepetitionPeer::TITLE, $this->title);
		if ($this->isColumnModified(RepetitionPeer::CREATOR_ID)) $criteria->add(RepetitionPeer::CREATOR_ID, $this->creator_id);
		if ($this->isColumnModified(RepetitionPeer::CREATED_DATE)) $criteria->add(RepetitionPeer::CREATED_DATE, $this->created_date);
		if ($this->isColumnModified(RepetitionPeer::MODIFIED_BY_ID)) $criteria->add(RepetitionPeer::MODIFIED_BY_ID, $this->modified_by_id);
		if ($this->isColumnModified(RepetitionPeer::MODIFIED_DATE)) $criteria->add(RepetitionPeer::MODIFIED_DATE, $this->modified_date);
		if ($this->isColumnModified(RepetitionPeer::APP_ID)) $criteria->add(RepetitionPeer::APP_ID, $this->app_id);

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
		$criteria = new Criteria(RepetitionPeer::DATABASE_NAME);

		$criteria->add(RepetitionPeer::REPID, $this->repid);

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
	 * Generic method to set the primary key (repid column).
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
	 * @param      object $copyObj An object of Repetition (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCurationStatus($this->curation_status);

		$copyObj->setDeleted($this->deleted);

		$copyObj->setEndDate($this->end_date);

		$copyObj->setName($this->name);

		$copyObj->setStartDate($this->start_date);

		$copyObj->setStatus($this->status);

		$copyObj->setTrialId($this->trialid);

		$copyObj->setDescription($this->description);

		$copyObj->setTitle($this->title);

		$copyObj->setCreatorId($this->creator_id);

		$copyObj->setCreatedDate($this->created_date);

		$copyObj->setModifiedById($this->modified_by_id);

		$copyObj->setModifiedDate($this->modified_date);

		$copyObj->setAppId($this->app_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getDataFileLinks() as $relObj) {
				$copyObj->addDataFileLink($relObj->copy($deepCopy));
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
	 * @return     Repetition Clone of current object.
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
	 * @return     RepetitionPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new RepetitionPeer();
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
	public function setPersonRelatedByCreatorId($v)
	{


		if ($v === null) {
			$this->setCreatorId(NULL);
		} else {
			$this->setCreatorId($v->getId());
		}


		$this->aPersonRelatedByCreatorId = $v;
	}


	/**
	 * Get the associated Person object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Person The associated Person object.
	 * @throws     PropelException
	 */
	public function getPersonRelatedByCreatorId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BasePersonPeer.php';

		if ($this->aPersonRelatedByCreatorId === null && ($this->creator_id > 0)) {

			$this->aPersonRelatedByCreatorId = PersonPeer::retrieveByPK($this->creator_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = PersonPeer::retrieveByPK($this->creator_id, $con);
			   $obj->addPersonsRelatedByCreatorId($this);
			 */
		}
		return $this->aPersonRelatedByCreatorId;
	}

	/**
	 * Declares an association between this object and a Person object.
	 *
	 * @param      Person $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setPersonRelatedByModifiedById($v)
	{


		if ($v === null) {
			$this->setModifiedById(NULL);
		} else {
			$this->setModifiedById($v->getId());
		}


		$this->aPersonRelatedByModifiedById = $v;
	}


	/**
	 * Get the associated Person object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Person The associated Person object.
	 * @throws     PropelException
	 */
	public function getPersonRelatedByModifiedById($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BasePersonPeer.php';

		if ($this->aPersonRelatedByModifiedById === null && ($this->modified_by_id > 0)) {

			$this->aPersonRelatedByModifiedById = PersonPeer::retrieveByPK($this->modified_by_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = PersonPeer::retrieveByPK($this->modified_by_id, $con);
			   $obj->addPersonsRelatedByModifiedById($this);
			 */
		}
		return $this->aPersonRelatedByModifiedById;
	}

	/**
	 * Declares an association between this object and a Trial object.
	 *
	 * @param      Trial $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setTrial($v)
	{


		if ($v === null) {
			$this->setTrialId(NULL);
		} else {
			$this->setTrialId($v->getId());
		}


		$this->aTrial = $v;
	}


	/**
	 * Get the associated Trial object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Trial The associated Trial object.
	 * @throws     PropelException
	 */
	public function getTrial($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseTrialPeer.php';

		if ($this->aTrial === null && ($this->trialid > 0)) {

			$this->aTrial = TrialPeer::retrieveByPK($this->trialid, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = TrialPeer::retrieveByPK($this->trialid, $con);
			   $obj->addTrials($this);
			 */
		}
		return $this->aTrial;
	}

	/**
	 * Temporary storage of collDataFileLinks to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDataFileLinks()
	{
		if ($this->collDataFileLinks === null) {
			$this->collDataFileLinks = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Repetition has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 * If this Repetition is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDataFileLinks($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFileLinkPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFileLinks === null) {
			if ($this->isNew()) {
			   $this->collDataFileLinks = array();
			} else {

				$criteria->add(DataFileLinkPeer::REP_ID, $this->getId());

				DataFileLinkPeer::addSelectColumns($criteria);
				$this->collDataFileLinks = DataFileLinkPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DataFileLinkPeer::REP_ID, $this->getId());

				DataFileLinkPeer::addSelectColumns($criteria);
				if (!isset($this->lastDataFileLinkCriteria) || !$this->lastDataFileLinkCriteria->equals($criteria)) {
					$this->collDataFileLinks = DataFileLinkPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDataFileLinkCriteria = $criteria;
		return $this->collDataFileLinks;
	}

	/**
	 * Returns the number of related DataFileLinks.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDataFileLinks($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFileLinkPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DataFileLinkPeer::REP_ID, $this->getId());

		return DataFileLinkPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DataFileLink object to this object
	 * through the DataFileLink foreign key attribute
	 *
	 * @param      DataFileLink $l DataFileLink
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDataFileLink(DataFileLink $l)
	{
		$this->collDataFileLinks[] = $l;
		$l->setRepetition($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Repetition is new, it will return
	 * an empty collection; or if this Repetition has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Repetition.
	 */
	public function getDataFileLinksJoinProject($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFileLinkPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFileLinks === null) {
			if ($this->isNew()) {
				$this->collDataFileLinks = array();
			} else {

				$criteria->add(DataFileLinkPeer::REP_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinProject($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::REP_ID, $this->getId());

			if (!isset($this->lastDataFileLinkCriteria) || !$this->lastDataFileLinkCriteria->equals($criteria)) {
				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinProject($criteria, $con);
			}
		}
		$this->lastDataFileLinkCriteria = $criteria;

		return $this->collDataFileLinks;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Repetition is new, it will return
	 * an empty collection; or if this Repetition has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Repetition.
	 */
	public function getDataFileLinksJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFileLinkPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFileLinks === null) {
			if ($this->isNew()) {
				$this->collDataFileLinks = array();
			} else {

				$criteria->add(DataFileLinkPeer::REP_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::REP_ID, $this->getId());

			if (!isset($this->lastDataFileLinkCriteria) || !$this->lastDataFileLinkCriteria->equals($criteria)) {
				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastDataFileLinkCriteria = $criteria;

		return $this->collDataFileLinks;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Repetition is new, it will return
	 * an empty collection; or if this Repetition has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Repetition.
	 */
	public function getDataFileLinksJoinTrial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFileLinkPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFileLinks === null) {
			if ($this->isNew()) {
				$this->collDataFileLinks = array();
			} else {

				$criteria->add(DataFileLinkPeer::REP_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::REP_ID, $this->getId());

			if (!isset($this->lastDataFileLinkCriteria) || !$this->lastDataFileLinkCriteria->equals($criteria)) {
				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastDataFileLinkCriteria = $criteria;

		return $this->collDataFileLinks;
	}

} // BaseRepetition
