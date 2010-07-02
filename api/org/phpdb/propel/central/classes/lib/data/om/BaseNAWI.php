<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/NAWIPeer.php';

/**
 * Base class that represents a row from the 'NAWI' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseNAWI extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        NAWIPeer
	 */
	protected static $peer;


	/**
	 * The value for the nawiid field.
	 * @var        double
	 */
	protected $nawiid;


	/**
	 * The value for the active field.
	 * @var        double
	 */
	protected $active;


	/**
	 * The value for the contact_email field.
	 * @var        string
	 */
	protected $contact_email;


	/**
	 * The value for the contact_name field.
	 * @var        string
	 */
	protected $contact_name;


	/**
	 * The value for the exp_descript field.
	 * @var        string
	 */
	protected $exp_descript;


	/**
	 * The value for the exp_name field.
	 * @var        string
	 */
	protected $exp_name;


	/**
	 * The value for the exp_phase field.
	 * @var        string
	 */
	protected $exp_phase;


	/**
	 * The value for the movie_url field.
	 * @var        string
	 */
	protected $movie_url;


	/**
	 * The value for the test_dt field.
	 * @var        int
	 */
	protected $test_dt;


	/**
	 * The value for the test_end field.
	 * @var        int
	 */
	protected $test_end;


	/**
	 * The value for the test_start field.
	 * @var        int
	 */
	protected $test_start;


	/**
	 * The value for the test_tz field.
	 * @var        string
	 */
	protected $test_tz;

	/**
	 * Collection to store aggregation of collNAWIFacilitys.
	 * @var        array
	 */
	protected $collNAWIFacilitys;

	/**
	 * The criteria used to select the current contents of collNAWIFacilitys.
	 * @var        Criteria
	 */
	protected $lastNAWIFacilityCriteria = null;

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
	 * Get the [nawiid] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->nawiid;
	}

	/**
	 * Get the [active] column value.
	 * 
	 * @return     double
	 */
	public function getActive()
	{

		return $this->active;
	}

	/**
	 * Get the [contact_email] column value.
	 * 
	 * @return     string
	 */
	public function getContactEmail()
	{

		return $this->contact_email;
	}

	/**
	 * Get the [contact_name] column value.
	 * 
	 * @return     string
	 */
	public function getContactName()
	{

		return $this->contact_name;
	}

	/**
	 * Get the [exp_descript] column value.
	 * 
	 * @return     string
	 */
	public function getExperimentDescription()
	{

		return $this->exp_descript;
	}

	/**
	 * Get the [exp_name] column value.
	 * 
	 * @return     string
	 */
	public function getExperimentName()
	{

		return $this->exp_name;
	}

	/**
	 * Get the [exp_phase] column value.
	 * 
	 * @return     string
	 */
	public function getExperimentPhase()
	{

		return $this->exp_phase;
	}

	/**
	 * Get the [movie_url] column value.
	 * 
	 * @return     string
	 */
	public function getMovieUrl()
	{

		return $this->movie_url;
	}

	/**
	 * Get the [optionally formatted] [test_dt] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getTestDate($format = 'Y-m-d H:i:s')
	{

		if ($this->test_dt === null || $this->test_dt === '') {
			return null;
		} elseif (!is_int($this->test_dt)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->test_dt);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [test_dt] as date/time value: " . var_export($this->test_dt, true));
			}
		} else {
			$ts = $this->test_dt;
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
	 * Get the [optionally formatted] [test_end] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getTestEndDate($format = '%Y-%m-%d')
	{

		if ($this->test_end === null || $this->test_end === '') {
			return null;
		} elseif (!is_int($this->test_end)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->test_end);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [test_end] as date/time value: " . var_export($this->test_end, true));
			}
		} else {
			$ts = $this->test_end;
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
	 * Get the [optionally formatted] [test_start] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getTestStartDate($format = '%Y-%m-%d')
	{

		if ($this->test_start === null || $this->test_start === '') {
			return null;
		} elseif (!is_int($this->test_start)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->test_start);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [test_start] as date/time value: " . var_export($this->test_start, true));
			}
		} else {
			$ts = $this->test_start;
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
	 * Get the [test_tz] column value.
	 * 
	 * @return     string
	 */
	public function getTestTimeZone()
	{

		return $this->test_tz;
	}

	/**
	 * Set the value of [nawiid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->nawiid !== $v) {
			$this->nawiid = $v;
			$this->modifiedColumns[] = NAWIPeer::NAWIID;
		}

	} // setId()

	/**
	 * Set the value of [active] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setActive($v)
	{

		if ($this->active !== $v) {
			$this->active = $v;
			$this->modifiedColumns[] = NAWIPeer::ACTIVE;
		}

	} // setActive()

	/**
	 * Set the value of [contact_email] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactEmail($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->contact_email !== $v) {
			$this->contact_email = $v;
			$this->modifiedColumns[] = NAWIPeer::CONTACT_EMAIL;
		}

	} // setContactEmail()

	/**
	 * Set the value of [contact_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->contact_name !== $v) {
			$this->contact_name = $v;
			$this->modifiedColumns[] = NAWIPeer::CONTACT_NAME;
		}

	} // setContactName()

	/**
	 * Set the value of [exp_descript] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setExperimentDescription($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->exp_descript) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->exp_descript !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->exp_descript = $obj;
			$this->modifiedColumns[] = NAWIPeer::EXP_DESCRIPT;
		}

	} // setExperimentDescription()

	/**
	 * Set the value of [exp_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setExperimentName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->exp_name !== $v) {
			$this->exp_name = $v;
			$this->modifiedColumns[] = NAWIPeer::EXP_NAME;
		}

	} // setExperimentName()

	/**
	 * Set the value of [exp_phase] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setExperimentPhase($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->exp_phase !== $v) {
			$this->exp_phase = $v;
			$this->modifiedColumns[] = NAWIPeer::EXP_PHASE;
		}

	} // setExperimentPhase()

	/**
	 * Set the value of [movie_url] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setMovieUrl($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->movie_url !== $v) {
			$this->movie_url = $v;
			$this->modifiedColumns[] = NAWIPeer::MOVIE_URL;
		}

	} // setMovieUrl()

	/**
	 * Set the value of [test_dt] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setTestDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [test_dt] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->test_dt !== $ts) {
			$this->test_dt = $ts;
			$this->modifiedColumns[] = NAWIPeer::TEST_DT;
		}

	} // setTestDate()

	/**
	 * Set the value of [test_end] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setTestEndDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [test_end] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->test_end !== $ts) {
			$this->test_end = $ts;
			$this->modifiedColumns[] = NAWIPeer::TEST_END;
		}

	} // setTestEndDate()

	/**
	 * Set the value of [test_start] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setTestStartDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [test_start] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->test_start !== $ts) {
			$this->test_start = $ts;
			$this->modifiedColumns[] = NAWIPeer::TEST_START;
		}

	} // setTestStartDate()

	/**
	 * Set the value of [test_tz] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTestTimeZone($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->test_tz !== $v) {
			$this->test_tz = $v;
			$this->modifiedColumns[] = NAWIPeer::TEST_TZ;
		}

	} // setTestTimeZone()

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

			$this->nawiid = $rs->getFloat($startcol + 0);

			$this->active = $rs->getFloat($startcol + 1);

			$this->contact_email = $rs->getString($startcol + 2);

			$this->contact_name = $rs->getString($startcol + 3);

			$this->exp_descript = $rs->getClob($startcol + 4);

			$this->exp_name = $rs->getString($startcol + 5);

			$this->exp_phase = $rs->getString($startcol + 6);

			$this->movie_url = $rs->getString($startcol + 7);

			$this->test_dt = $rs->getTimestamp($startcol + 8, null);

			$this->test_end = $rs->getDate($startcol + 9, null);

			$this->test_start = $rs->getDate($startcol + 10, null);

			$this->test_tz = $rs->getString($startcol + 11);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 12; // 12 = NAWIPeer::NUM_COLUMNS - NAWIPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating NAWI object", $e);
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
			$con = Propel::getConnection(NAWIPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			NAWIPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(NAWIPeer::DATABASE_NAME);
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
					$pk = NAWIPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += NAWIPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collNAWIFacilitys !== null) {
				foreach($this->collNAWIFacilitys as $referrerFK) {
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


			if (($retval = NAWIPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collNAWIFacilitys !== null) {
					foreach($this->collNAWIFacilitys as $referrerFK) {
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
		$pos = NAWIPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getActive();
				break;
			case 2:
				return $this->getContactEmail();
				break;
			case 3:
				return $this->getContactName();
				break;
			case 4:
				return $this->getExperimentDescription();
				break;
			case 5:
				return $this->getExperimentName();
				break;
			case 6:
				return $this->getExperimentPhase();
				break;
			case 7:
				return $this->getMovieUrl();
				break;
			case 8:
				return $this->getTestDate();
				break;
			case 9:
				return $this->getTestEndDate();
				break;
			case 10:
				return $this->getTestStartDate();
				break;
			case 11:
				return $this->getTestTimeZone();
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
		$keys = NAWIPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getActive(),
			$keys[2] => $this->getContactEmail(),
			$keys[3] => $this->getContactName(),
			$keys[4] => $this->getExperimentDescription(),
			$keys[5] => $this->getExperimentName(),
			$keys[6] => $this->getExperimentPhase(),
			$keys[7] => $this->getMovieUrl(),
			$keys[8] => $this->getTestDate(),
			$keys[9] => $this->getTestEndDate(),
			$keys[10] => $this->getTestStartDate(),
			$keys[11] => $this->getTestTimeZone(),
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
		$pos = NAWIPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setActive($value);
				break;
			case 2:
				$this->setContactEmail($value);
				break;
			case 3:
				$this->setContactName($value);
				break;
			case 4:
				$this->setExperimentDescription($value);
				break;
			case 5:
				$this->setExperimentName($value);
				break;
			case 6:
				$this->setExperimentPhase($value);
				break;
			case 7:
				$this->setMovieUrl($value);
				break;
			case 8:
				$this->setTestDate($value);
				break;
			case 9:
				$this->setTestEndDate($value);
				break;
			case 10:
				$this->setTestStartDate($value);
				break;
			case 11:
				$this->setTestTimeZone($value);
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
		$keys = NAWIPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setActive($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setContactEmail($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setContactName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setExperimentDescription($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setExperimentName($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setExperimentPhase($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setMovieUrl($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setTestDate($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setTestEndDate($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setTestStartDate($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setTestTimeZone($arr[$keys[11]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(NAWIPeer::DATABASE_NAME);

		if ($this->isColumnModified(NAWIPeer::NAWIID)) $criteria->add(NAWIPeer::NAWIID, $this->nawiid);
		if ($this->isColumnModified(NAWIPeer::ACTIVE)) $criteria->add(NAWIPeer::ACTIVE, $this->active);
		if ($this->isColumnModified(NAWIPeer::CONTACT_EMAIL)) $criteria->add(NAWIPeer::CONTACT_EMAIL, $this->contact_email);
		if ($this->isColumnModified(NAWIPeer::CONTACT_NAME)) $criteria->add(NAWIPeer::CONTACT_NAME, $this->contact_name);
		if ($this->isColumnModified(NAWIPeer::EXP_DESCRIPT)) $criteria->add(NAWIPeer::EXP_DESCRIPT, $this->exp_descript);
		if ($this->isColumnModified(NAWIPeer::EXP_NAME)) $criteria->add(NAWIPeer::EXP_NAME, $this->exp_name);
		if ($this->isColumnModified(NAWIPeer::EXP_PHASE)) $criteria->add(NAWIPeer::EXP_PHASE, $this->exp_phase);
		if ($this->isColumnModified(NAWIPeer::MOVIE_URL)) $criteria->add(NAWIPeer::MOVIE_URL, $this->movie_url);
		if ($this->isColumnModified(NAWIPeer::TEST_DT)) $criteria->add(NAWIPeer::TEST_DT, $this->test_dt);
		if ($this->isColumnModified(NAWIPeer::TEST_END)) $criteria->add(NAWIPeer::TEST_END, $this->test_end);
		if ($this->isColumnModified(NAWIPeer::TEST_START)) $criteria->add(NAWIPeer::TEST_START, $this->test_start);
		if ($this->isColumnModified(NAWIPeer::TEST_TZ)) $criteria->add(NAWIPeer::TEST_TZ, $this->test_tz);

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
		$criteria = new Criteria(NAWIPeer::DATABASE_NAME);

		$criteria->add(NAWIPeer::NAWIID, $this->nawiid);

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
	 * Generic method to set the primary key (nawiid column).
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
	 * @param      object $copyObj An object of NAWI (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setActive($this->active);

		$copyObj->setContactEmail($this->contact_email);

		$copyObj->setContactName($this->contact_name);

		$copyObj->setExperimentDescription($this->exp_descript);

		$copyObj->setExperimentName($this->exp_name);

		$copyObj->setExperimentPhase($this->exp_phase);

		$copyObj->setMovieUrl($this->movie_url);

		$copyObj->setTestDate($this->test_dt);

		$copyObj->setTestEndDate($this->test_end);

		$copyObj->setTestStartDate($this->test_start);

		$copyObj->setTestTimeZone($this->test_tz);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getNAWIFacilitys() as $relObj) {
				$copyObj->addNAWIFacility($relObj->copy($deepCopy));
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
	 * @return     NAWI Clone of current object.
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
	 * @return     NAWIPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new NAWIPeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collNAWIFacilitys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initNAWIFacilitys()
	{
		if ($this->collNAWIFacilitys === null) {
			$this->collNAWIFacilitys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this NAWI has previously
	 * been saved, it will retrieve related NAWIFacilitys from storage.
	 * If this NAWI is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getNAWIFacilitys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseNAWIFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collNAWIFacilitys === null) {
			if ($this->isNew()) {
			   $this->collNAWIFacilitys = array();
			} else {

				$criteria->add(NAWIFacilityPeer::NAWIID, $this->getId());

				NAWIFacilityPeer::addSelectColumns($criteria);
				$this->collNAWIFacilitys = NAWIFacilityPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(NAWIFacilityPeer::NAWIID, $this->getId());

				NAWIFacilityPeer::addSelectColumns($criteria);
				if (!isset($this->lastNAWIFacilityCriteria) || !$this->lastNAWIFacilityCriteria->equals($criteria)) {
					$this->collNAWIFacilitys = NAWIFacilityPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastNAWIFacilityCriteria = $criteria;
		return $this->collNAWIFacilitys;
	}

	/**
	 * Returns the number of related NAWIFacilitys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countNAWIFacilitys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseNAWIFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(NAWIFacilityPeer::NAWIID, $this->getId());

		return NAWIFacilityPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a NAWIFacility object to this object
	 * through the NAWIFacility foreign key attribute
	 *
	 * @param      NAWIFacility $l NAWIFacility
	 * @return     void
	 * @throws     PropelException
	 */
	public function addNAWIFacility(NAWIFacility $l)
	{
		$this->collNAWIFacilitys[] = $l;
		$l->setNAWI($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this NAWI is new, it will return
	 * an empty collection; or if this NAWI has previously
	 * been saved, it will retrieve related NAWIFacilitys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in NAWI.
	 */
	public function getNAWIFacilitysJoinOrganization($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseNAWIFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collNAWIFacilitys === null) {
			if ($this->isNew()) {
				$this->collNAWIFacilitys = array();
			} else {

				$criteria->add(NAWIFacilityPeer::NAWIID, $this->getId());

				$this->collNAWIFacilitys = NAWIFacilityPeer::doSelectJoinOrganization($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(NAWIFacilityPeer::NAWIID, $this->getId());

			if (!isset($this->lastNAWIFacilityCriteria) || !$this->lastNAWIFacilityCriteria->equals($criteria)) {
				$this->collNAWIFacilitys = NAWIFacilityPeer::doSelectJoinOrganization($criteria, $con);
			}
		}
		$this->lastNAWIFacilityCriteria = $criteria;

		return $this->collNAWIFacilitys;
	}

} // BaseNAWI
