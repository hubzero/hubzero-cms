<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/TrialPeer.php';

/**
 * Base class that represents a row from the 'TRIAL' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseTrial extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        TrialPeer
	 */
	protected static $peer;


	/**
	 * The value for the trialid field.
	 * @var        double
	 */
	protected $trialid;


	/**
	 * The value for the acceleration field.
	 * @var        double
	 */
	protected $acceleration;


	/**
	 * The value for the base_acceleration field.
	 * @var        double
	 */
	protected $base_acceleration;


	/**
	 * The value for the base_acceleration_unit_id field.
	 * @var        double
	 */
	protected $base_acceleration_unit_id;


	/**
	 * The value for the component field.
	 * @var        string
	 */
	protected $component;


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
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the end_date field.
	 * @var        int
	 */
	protected $end_date;


	/**
	 * The value for the expid field.
	 * @var        double
	 */
	protected $expid;


	/**
	 * The value for the motion_file_id field.
	 * @var        double
	 */
	protected $motion_file_id;


	/**
	 * The value for the motion_name field.
	 * @var        string
	 */
	protected $motion_name;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the objective field.
	 * @var        string
	 */
	protected $objective;


	/**
	 * The value for the start_date field.
	 * @var        int
	 */
	protected $start_date;


	/**
	 * The value for the station field.
	 * @var        string
	 */
	protected $station;


	/**
	 * The value for the status field.
	 * @var        string
	 */
	protected $status;


	/**
	 * The value for the title field.
	 * @var        string
	 */
	protected $title;


	/**
	 * The value for the trial_type_id field.
	 * @var        double
	 */
	protected $trial_type_id = 0;

	/**
	 * @var        DataFile
	 */
	protected $aDataFile;

	/**
	 * @var        Experiment
	 */
	protected $aExperiment;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnit;

	/**
	 * Collection to store aggregation of collAcknowledgements.
	 * @var        array
	 */
	protected $collAcknowledgements;

	/**
	 * The criteria used to select the current contents of collAcknowledgements.
	 * @var        Criteria
	 */
	protected $lastAcknowledgementCriteria = null;

	/**
	 * Collection to store aggregation of collControllerConfigs.
	 * @var        array
	 */
	protected $collControllerConfigs;

	/**
	 * The criteria used to select the current contents of collControllerConfigs.
	 * @var        Criteria
	 */
	protected $lastControllerConfigCriteria = null;

	/**
	 * Collection to store aggregation of collDAQConfigs.
	 * @var        array
	 */
	protected $collDAQConfigs;

	/**
	 * The criteria used to select the current contents of collDAQConfigs.
	 * @var        Criteria
	 */
	protected $lastDAQConfigCriteria = null;

	/**
	 * Collection to store aggregation of collLocationPlans.
	 * @var        array
	 */
	protected $collLocationPlans;

	/**
	 * The criteria used to select the current contents of collLocationPlans.
	 * @var        Criteria
	 */
	protected $lastLocationPlanCriteria = null;

	/**
	 * Collection to store aggregation of collRepetitions.
	 * @var        array
	 */
	protected $collRepetitions;

	/**
	 * The criteria used to select the current contents of collRepetitions.
	 * @var        Criteria
	 */
	protected $lastRepetitionCriteria = null;

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
	 * Get the [trialid] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->trialid;
	}

	/**
	 * Get the [acceleration] column value.
	 * 
	 * @return     double
	 */
	public function getAcceleration()
	{

		return $this->acceleration;
	}

	/**
	 * Get the [base_acceleration] column value.
	 * 
	 * @return     double
	 */
	public function getBaseAcceleration()
	{

		return $this->base_acceleration;
	}

	/**
	 * Get the [base_acceleration_unit_id] column value.
	 * 
	 * @return     double
	 */
	public function getBaseAccelerationUnitId()
	{

		return $this->base_acceleration_unit_id;
	}

	/**
	 * Get the [component] column value.
	 * 
	 * @return     string
	 */
	public function getComponent()
	{

		return $this->component;
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
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
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
	 * Get the [expid] column value.
	 * 
	 * @return     double
	 */
	public function getExperimentId()
	{

		return $this->expid;
	}

	/**
	 * Get the [motion_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getMotionFileId()
	{

		return $this->motion_file_id;
	}

	/**
	 * Get the [motion_name] column value.
	 * 
	 * @return     string
	 */
	public function getMotionName()
	{

		return $this->motion_name;
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
	 * Get the [objective] column value.
	 * 
	 * @return     string
	 */
	public function getObjective()
	{

		return $this->objective;
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
	 * Get the [station] column value.
	 * 
	 * @return     string
	 */
	public function getStation()
	{

		return $this->station;
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
	 * Get the [title] column value.
	 * 
	 * @return     string
	 */
	public function getTitle()
	{

		return $this->title;
	}

	/**
	 * Get the [trial_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getTrialTypeId()
	{

		return $this->trial_type_id;
	}

	/**
	 * Set the value of [trialid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->trialid !== $v) {
			$this->trialid = $v;
			$this->modifiedColumns[] = TrialPeer::TRIALID;
		}

	} // setId()

	/**
	 * Set the value of [acceleration] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAcceleration($v)
	{

		if ($this->acceleration !== $v) {
			$this->acceleration = $v;
			$this->modifiedColumns[] = TrialPeer::ACCELERATION;
		}

	} // setAcceleration()

	/**
	 * Set the value of [base_acceleration] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setBaseAcceleration($v)
	{

		if ($this->base_acceleration !== $v) {
			$this->base_acceleration = $v;
			$this->modifiedColumns[] = TrialPeer::BASE_ACCELERATION;
		}

	} // setBaseAcceleration()

	/**
	 * Set the value of [base_acceleration_unit_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setBaseAccelerationUnitId($v)
	{

		if ($this->base_acceleration_unit_id !== $v) {
			$this->base_acceleration_unit_id = $v;
			$this->modifiedColumns[] = TrialPeer::BASE_ACCELERATION_UNIT_ID;
		}

		if ($this->aMeasurementUnit !== null && $this->aMeasurementUnit->getId() !== $v) {
			$this->aMeasurementUnit = null;
		}

	} // setBaseAccelerationUnitId()

	/**
	 * Set the value of [component] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setComponent($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->component !== $v) {
			$this->component = $v;
			$this->modifiedColumns[] = TrialPeer::COMPONENT;
		}

	} // setComponent()

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
			$this->modifiedColumns[] = TrialPeer::CURATION_STATUS;
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
			$this->modifiedColumns[] = TrialPeer::DELETED;
		}

	} // setDeleted()

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
			$this->modifiedColumns[] = TrialPeer::DESCRIPTION;
		}

	} // setDescription()

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
			$this->modifiedColumns[] = TrialPeer::END_DATE;
		}

	} // setEndDate()

	/**
	 * Set the value of [expid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setExperimentId($v)
	{

		if ($this->expid !== $v) {
			$this->expid = $v;
			$this->modifiedColumns[] = TrialPeer::EXPID;
		}

		if ($this->aExperiment !== null && $this->aExperiment->getId() !== $v) {
			$this->aExperiment = null;
		}

	} // setExperimentId()

	/**
	 * Set the value of [motion_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMotionFileId($v)
	{

		if ($this->motion_file_id !== $v) {
			$this->motion_file_id = $v;
			$this->modifiedColumns[] = TrialPeer::MOTION_FILE_ID;
		}

		if ($this->aDataFile !== null && $this->aDataFile->getId() !== $v) {
			$this->aDataFile = null;
		}

	} // setMotionFileId()

	/**
	 * Set the value of [motion_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setMotionName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->motion_name !== $v) {
			$this->motion_name = $v;
			$this->modifiedColumns[] = TrialPeer::MOTION_NAME;
		}

	} // setMotionName()

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
			$this->modifiedColumns[] = TrialPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [objective] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setObjective($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->objective) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->objective !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->objective = $obj;
			$this->modifiedColumns[] = TrialPeer::OBJECTIVE;
		}

	} // setObjective()

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
			$this->modifiedColumns[] = TrialPeer::START_DATE;
		}

	} // setStartDate()

	/**
	 * Set the value of [station] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setStation($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->station !== $v) {
			$this->station = $v;
			$this->modifiedColumns[] = TrialPeer::STATION;
		}

	} // setStation()

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
			$this->modifiedColumns[] = TrialPeer::STATUS;
		}

	} // setStatus()

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
			$this->modifiedColumns[] = TrialPeer::TITLE;
		}

	} // setTitle()

	/**
	 * Set the value of [trial_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTrialTypeId($v)
	{

		if ($this->trial_type_id !== $v || $v === 0) {
			$this->trial_type_id = $v;
			$this->modifiedColumns[] = TrialPeer::TRIAL_TYPE_ID;
		}

	} // setTrialTypeId()

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

			$this->trialid = $rs->getFloat($startcol + 0);

			$this->acceleration = $rs->getFloat($startcol + 1);

			$this->base_acceleration = $rs->getFloat($startcol + 2);

			$this->base_acceleration_unit_id = $rs->getFloat($startcol + 3);

			$this->component = $rs->getString($startcol + 4);

			$this->curation_status = $rs->getString($startcol + 5);

			$this->deleted = $rs->getFloat($startcol + 6);

			$this->description = $rs->getClob($startcol + 7);

			$this->end_date = $rs->getDate($startcol + 8, null);

			$this->expid = $rs->getFloat($startcol + 9);

			$this->motion_file_id = $rs->getFloat($startcol + 10);

			$this->motion_name = $rs->getString($startcol + 11);

			$this->name = $rs->getString($startcol + 12);

			$this->objective = $rs->getClob($startcol + 13);

			$this->start_date = $rs->getDate($startcol + 14, null);

			$this->station = $rs->getString($startcol + 15);

			$this->status = $rs->getString($startcol + 16);

			$this->title = $rs->getString($startcol + 17);

			$this->trial_type_id = $rs->getFloat($startcol + 18);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 19; // 19 = TrialPeer::NUM_COLUMNS - TrialPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Trial object", $e);
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
			$con = Propel::getConnection(TrialPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			TrialPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(TrialPeer::DATABASE_NAME);
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

			if ($this->aDataFile !== null) {
				if ($this->aDataFile->isModified()) {
					$affectedRows += $this->aDataFile->save($con);
				}
				$this->setDataFile($this->aDataFile);
			}

			if ($this->aExperiment !== null) {
				if ($this->aExperiment->isModified()) {
					$affectedRows += $this->aExperiment->save($con);
				}
				$this->setExperiment($this->aExperiment);
			}

			if ($this->aMeasurementUnit !== null) {
				if ($this->aMeasurementUnit->isModified()) {
					$affectedRows += $this->aMeasurementUnit->save($con);
				}
				$this->setMeasurementUnit($this->aMeasurementUnit);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = TrialPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += TrialPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collAcknowledgements !== null) {
				foreach($this->collAcknowledgements as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collControllerConfigs !== null) {
				foreach($this->collControllerConfigs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collDAQConfigs !== null) {
				foreach($this->collDAQConfigs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collLocationPlans !== null) {
				foreach($this->collLocationPlans as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collRepetitions !== null) {
				foreach($this->collRepetitions as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
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

			if ($this->aDataFile !== null) {
				if (!$this->aDataFile->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFile->getValidationFailures());
				}
			}

			if ($this->aExperiment !== null) {
				if (!$this->aExperiment->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aExperiment->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnit !== null) {
				if (!$this->aMeasurementUnit->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnit->getValidationFailures());
				}
			}


			if (($retval = TrialPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAcknowledgements !== null) {
					foreach($this->collAcknowledgements as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collControllerConfigs !== null) {
					foreach($this->collControllerConfigs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collDAQConfigs !== null) {
					foreach($this->collDAQConfigs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collLocationPlans !== null) {
					foreach($this->collLocationPlans as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collRepetitions !== null) {
					foreach($this->collRepetitions as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
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
		$pos = TrialPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAcceleration();
				break;
			case 2:
				return $this->getBaseAcceleration();
				break;
			case 3:
				return $this->getBaseAccelerationUnitId();
				break;
			case 4:
				return $this->getComponent();
				break;
			case 5:
				return $this->getCurationStatus();
				break;
			case 6:
				return $this->getDeleted();
				break;
			case 7:
				return $this->getDescription();
				break;
			case 8:
				return $this->getEndDate();
				break;
			case 9:
				return $this->getExperimentId();
				break;
			case 10:
				return $this->getMotionFileId();
				break;
			case 11:
				return $this->getMotionName();
				break;
			case 12:
				return $this->getName();
				break;
			case 13:
				return $this->getObjective();
				break;
			case 14:
				return $this->getStartDate();
				break;
			case 15:
				return $this->getStation();
				break;
			case 16:
				return $this->getStatus();
				break;
			case 17:
				return $this->getTitle();
				break;
			case 18:
				return $this->getTrialTypeId();
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
		$keys = TrialPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getAcceleration(),
			$keys[2] => $this->getBaseAcceleration(),
			$keys[3] => $this->getBaseAccelerationUnitId(),
			$keys[4] => $this->getComponent(),
			$keys[5] => $this->getCurationStatus(),
			$keys[6] => $this->getDeleted(),
			$keys[7] => $this->getDescription(),
			$keys[8] => $this->getEndDate(),
			$keys[9] => $this->getExperimentId(),
			$keys[10] => $this->getMotionFileId(),
			$keys[11] => $this->getMotionName(),
			$keys[12] => $this->getName(),
			$keys[13] => $this->getObjective(),
			$keys[14] => $this->getStartDate(),
			$keys[15] => $this->getStation(),
			$keys[16] => $this->getStatus(),
			$keys[17] => $this->getTitle(),
			$keys[18] => $this->getTrialTypeId(),
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
		$pos = TrialPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAcceleration($value);
				break;
			case 2:
				$this->setBaseAcceleration($value);
				break;
			case 3:
				$this->setBaseAccelerationUnitId($value);
				break;
			case 4:
				$this->setComponent($value);
				break;
			case 5:
				$this->setCurationStatus($value);
				break;
			case 6:
				$this->setDeleted($value);
				break;
			case 7:
				$this->setDescription($value);
				break;
			case 8:
				$this->setEndDate($value);
				break;
			case 9:
				$this->setExperimentId($value);
				break;
			case 10:
				$this->setMotionFileId($value);
				break;
			case 11:
				$this->setMotionName($value);
				break;
			case 12:
				$this->setName($value);
				break;
			case 13:
				$this->setObjective($value);
				break;
			case 14:
				$this->setStartDate($value);
				break;
			case 15:
				$this->setStation($value);
				break;
			case 16:
				$this->setStatus($value);
				break;
			case 17:
				$this->setTitle($value);
				break;
			case 18:
				$this->setTrialTypeId($value);
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
		$keys = TrialPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAcceleration($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setBaseAcceleration($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setBaseAccelerationUnitId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setComponent($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCurationStatus($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDeleted($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDescription($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setEndDate($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setExperimentId($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setMotionFileId($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setMotionName($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setName($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setObjective($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setStartDate($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setStation($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setStatus($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setTitle($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setTrialTypeId($arr[$keys[18]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(TrialPeer::DATABASE_NAME);

		if ($this->isColumnModified(TrialPeer::TRIALID)) $criteria->add(TrialPeer::TRIALID, $this->trialid);
		if ($this->isColumnModified(TrialPeer::ACCELERATION)) $criteria->add(TrialPeer::ACCELERATION, $this->acceleration);
		if ($this->isColumnModified(TrialPeer::BASE_ACCELERATION)) $criteria->add(TrialPeer::BASE_ACCELERATION, $this->base_acceleration);
		if ($this->isColumnModified(TrialPeer::BASE_ACCELERATION_UNIT_ID)) $criteria->add(TrialPeer::BASE_ACCELERATION_UNIT_ID, $this->base_acceleration_unit_id);
		if ($this->isColumnModified(TrialPeer::COMPONENT)) $criteria->add(TrialPeer::COMPONENT, $this->component);
		if ($this->isColumnModified(TrialPeer::CURATION_STATUS)) $criteria->add(TrialPeer::CURATION_STATUS, $this->curation_status);
		if ($this->isColumnModified(TrialPeer::DELETED)) $criteria->add(TrialPeer::DELETED, $this->deleted);
		if ($this->isColumnModified(TrialPeer::DESCRIPTION)) $criteria->add(TrialPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(TrialPeer::END_DATE)) $criteria->add(TrialPeer::END_DATE, $this->end_date);
		if ($this->isColumnModified(TrialPeer::EXPID)) $criteria->add(TrialPeer::EXPID, $this->expid);
		if ($this->isColumnModified(TrialPeer::MOTION_FILE_ID)) $criteria->add(TrialPeer::MOTION_FILE_ID, $this->motion_file_id);
		if ($this->isColumnModified(TrialPeer::MOTION_NAME)) $criteria->add(TrialPeer::MOTION_NAME, $this->motion_name);
		if ($this->isColumnModified(TrialPeer::NAME)) $criteria->add(TrialPeer::NAME, $this->name);
		if ($this->isColumnModified(TrialPeer::OBJECTIVE)) $criteria->add(TrialPeer::OBJECTIVE, $this->objective);
		if ($this->isColumnModified(TrialPeer::START_DATE)) $criteria->add(TrialPeer::START_DATE, $this->start_date);
		if ($this->isColumnModified(TrialPeer::STATION)) $criteria->add(TrialPeer::STATION, $this->station);
		if ($this->isColumnModified(TrialPeer::STATUS)) $criteria->add(TrialPeer::STATUS, $this->status);
		if ($this->isColumnModified(TrialPeer::TITLE)) $criteria->add(TrialPeer::TITLE, $this->title);
		if ($this->isColumnModified(TrialPeer::TRIAL_TYPE_ID)) $criteria->add(TrialPeer::TRIAL_TYPE_ID, $this->trial_type_id);

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
		$criteria = new Criteria(TrialPeer::DATABASE_NAME);

		$criteria->add(TrialPeer::TRIALID, $this->trialid);

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
	 * Generic method to set the primary key (trialid column).
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
	 * @param      object $copyObj An object of Trial (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAcceleration($this->acceleration);

		$copyObj->setBaseAcceleration($this->base_acceleration);

		$copyObj->setBaseAccelerationUnitId($this->base_acceleration_unit_id);

		$copyObj->setComponent($this->component);

		$copyObj->setCurationStatus($this->curation_status);

		$copyObj->setDeleted($this->deleted);

		$copyObj->setDescription($this->description);

		$copyObj->setEndDate($this->end_date);

		$copyObj->setExperimentId($this->expid);

		$copyObj->setMotionFileId($this->motion_file_id);

		$copyObj->setMotionName($this->motion_name);

		$copyObj->setName($this->name);

		$copyObj->setObjective($this->objective);

		$copyObj->setStartDate($this->start_date);

		$copyObj->setStation($this->station);

		$copyObj->setStatus($this->status);

		$copyObj->setTitle($this->title);

		$copyObj->setTrialTypeId($this->trial_type_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getAcknowledgements() as $relObj) {
				$copyObj->addAcknowledgement($relObj->copy($deepCopy));
			}

			foreach($this->getControllerConfigs() as $relObj) {
				$copyObj->addControllerConfig($relObj->copy($deepCopy));
			}

			foreach($this->getDAQConfigs() as $relObj) {
				$copyObj->addDAQConfig($relObj->copy($deepCopy));
			}

			foreach($this->getLocationPlans() as $relObj) {
				$copyObj->addLocationPlan($relObj->copy($deepCopy));
			}

			foreach($this->getRepetitions() as $relObj) {
				$copyObj->addRepetition($relObj->copy($deepCopy));
			}

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
	 * @return     Trial Clone of current object.
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
	 * @return     TrialPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new TrialPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a DataFile object.
	 *
	 * @param      DataFile $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDataFile($v)
	{


		if ($v === null) {
			$this->setMotionFileId(NULL);
		} else {
			$this->setMotionFileId($v->getId());
		}


		$this->aDataFile = $v;
	}


	/**
	 * Get the associated DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
	public function getDataFile($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';

		if ($this->aDataFile === null && ($this->motion_file_id > 0)) {

			$this->aDataFile = DataFilePeer::retrieveByPK($this->motion_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->motion_file_id, $con);
			   $obj->addDataFiles($this);
			 */
		}
		return $this->aDataFile;
	}

	/**
	 * Declares an association between this object and a Experiment object.
	 *
	 * @param      Experiment $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setExperiment($v)
	{


		if ($v === null) {
			$this->setExperimentId(NULL);
		} else {
			$this->setExperimentId($v->getId());
		}


		$this->aExperiment = $v;
	}


	/**
	 * Get the associated Experiment object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Experiment The associated Experiment object.
	 * @throws     PropelException
	 */
	public function getExperiment($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseExperimentPeer.php';

		if ($this->aExperiment === null && ($this->expid > 0)) {

			$this->aExperiment = ExperimentPeer::retrieveByPK($this->expid, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ExperimentPeer::retrieveByPK($this->expid, $con);
			   $obj->addExperiments($this);
			 */
		}
		return $this->aExperiment;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnit($v)
	{


		if ($v === null) {
			$this->setBaseAccelerationUnitId(NULL);
		} else {
			$this->setBaseAccelerationUnitId($v->getId());
		}


		$this->aMeasurementUnit = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnit($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnit === null && ($this->base_acceleration_unit_id > 0)) {

			$this->aMeasurementUnit = MeasurementUnitPeer::retrieveByPK($this->base_acceleration_unit_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->base_acceleration_unit_id, $con);
			   $obj->addMeasurementUnits($this);
			 */
		}
		return $this->aMeasurementUnit;
	}

	/**
	 * Temporary storage of collAcknowledgements to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initAcknowledgements()
	{
		if ($this->collAcknowledgements === null) {
			$this->collAcknowledgements = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial has previously
	 * been saved, it will retrieve related Acknowledgements from storage.
	 * If this Trial is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getAcknowledgements($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAcknowledgementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAcknowledgements === null) {
			if ($this->isNew()) {
			   $this->collAcknowledgements = array();
			} else {

				$criteria->add(AcknowledgementPeer::TRIALID, $this->getId());

				AcknowledgementPeer::addSelectColumns($criteria);
				$this->collAcknowledgements = AcknowledgementPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AcknowledgementPeer::TRIALID, $this->getId());

				AcknowledgementPeer::addSelectColumns($criteria);
				if (!isset($this->lastAcknowledgementCriteria) || !$this->lastAcknowledgementCriteria->equals($criteria)) {
					$this->collAcknowledgements = AcknowledgementPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAcknowledgementCriteria = $criteria;
		return $this->collAcknowledgements;
	}

	/**
	 * Returns the number of related Acknowledgements.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countAcknowledgements($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAcknowledgementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(AcknowledgementPeer::TRIALID, $this->getId());

		return AcknowledgementPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Acknowledgement object to this object
	 * through the Acknowledgement foreign key attribute
	 *
	 * @param      Acknowledgement $l Acknowledgement
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAcknowledgement(Acknowledgement $l)
	{
		$this->collAcknowledgements[] = $l;
		$l->setTrial($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related Acknowledgements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
	 */
	public function getAcknowledgementsJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAcknowledgementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAcknowledgements === null) {
			if ($this->isNew()) {
				$this->collAcknowledgements = array();
			} else {

				$criteria->add(AcknowledgementPeer::TRIALID, $this->getId());

				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AcknowledgementPeer::TRIALID, $this->getId());

			if (!isset($this->lastAcknowledgementCriteria) || !$this->lastAcknowledgementCriteria->equals($criteria)) {
				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastAcknowledgementCriteria = $criteria;

		return $this->collAcknowledgements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related Acknowledgements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
	 */
	public function getAcknowledgementsJoinProject($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAcknowledgementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAcknowledgements === null) {
			if ($this->isNew()) {
				$this->collAcknowledgements = array();
			} else {

				$criteria->add(AcknowledgementPeer::TRIALID, $this->getId());

				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinProject($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AcknowledgementPeer::TRIALID, $this->getId());

			if (!isset($this->lastAcknowledgementCriteria) || !$this->lastAcknowledgementCriteria->equals($criteria)) {
				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinProject($criteria, $con);
			}
		}
		$this->lastAcknowledgementCriteria = $criteria;

		return $this->collAcknowledgements;
	}

	/**
	 * Temporary storage of collControllerConfigs to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initControllerConfigs()
	{
		if ($this->collControllerConfigs === null) {
			$this->collControllerConfigs = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 * If this Trial is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getControllerConfigs($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigs === null) {
			if ($this->isNew()) {
			   $this->collControllerConfigs = array();
			} else {

				$criteria->add(ControllerConfigPeer::TRIAL_ID, $this->getId());

				ControllerConfigPeer::addSelectColumns($criteria);
				$this->collControllerConfigs = ControllerConfigPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ControllerConfigPeer::TRIAL_ID, $this->getId());

				ControllerConfigPeer::addSelectColumns($criteria);
				if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
					$this->collControllerConfigs = ControllerConfigPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastControllerConfigCriteria = $criteria;
		return $this->collControllerConfigs;
	}

	/**
	 * Returns the number of related ControllerConfigs.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countControllerConfigs($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ControllerConfigPeer::TRIAL_ID, $this->getId());

		return ControllerConfigPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ControllerConfig object to this object
	 * through the ControllerConfig foreign key attribute
	 *
	 * @param      ControllerConfig $l ControllerConfig
	 * @return     void
	 * @throws     PropelException
	 */
	public function addControllerConfig(ControllerConfig $l)
	{
		$this->collControllerConfigs[] = $l;
		$l->setTrial($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
	 */
	public function getControllerConfigsJoinDataFileRelatedByInputDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigs === null) {
			if ($this->isNew()) {
				$this->collControllerConfigs = array();
			} else {

				$criteria->add(ControllerConfigPeer::TRIAL_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinDataFileRelatedByInputDataFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::TRIAL_ID, $this->getId());

			if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinDataFileRelatedByInputDataFileId($criteria, $con);
			}
		}
		$this->lastControllerConfigCriteria = $criteria;

		return $this->collControllerConfigs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
	 */
	public function getControllerConfigsJoinDataFileRelatedByConfigDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigs === null) {
			if ($this->isNew()) {
				$this->collControllerConfigs = array();
			} else {

				$criteria->add(ControllerConfigPeer::TRIAL_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinDataFileRelatedByConfigDataFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::TRIAL_ID, $this->getId());

			if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinDataFileRelatedByConfigDataFileId($criteria, $con);
			}
		}
		$this->lastControllerConfigCriteria = $criteria;

		return $this->collControllerConfigs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
	 */
	public function getControllerConfigsJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigs === null) {
			if ($this->isNew()) {
				$this->collControllerConfigs = array();
			} else {

				$criteria->add(ControllerConfigPeer::TRIAL_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::TRIAL_ID, $this->getId());

			if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastControllerConfigCriteria = $criteria;

		return $this->collControllerConfigs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
	 */
	public function getControllerConfigsJoinMeasurementUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigs === null) {
			if ($this->isNew()) {
				$this->collControllerConfigs = array();
			} else {

				$criteria->add(ControllerConfigPeer::TRIAL_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::TRIAL_ID, $this->getId());

			if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastControllerConfigCriteria = $criteria;

		return $this->collControllerConfigs;
	}

	/**
	 * Temporary storage of collDAQConfigs to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDAQConfigs()
	{
		if ($this->collDAQConfigs === null) {
			$this->collDAQConfigs = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial has previously
	 * been saved, it will retrieve related DAQConfigs from storage.
	 * If this Trial is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDAQConfigs($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigs === null) {
			if ($this->isNew()) {
			   $this->collDAQConfigs = array();
			} else {

				$criteria->add(DAQConfigPeer::TRIAL_ID, $this->getId());

				DAQConfigPeer::addSelectColumns($criteria);
				$this->collDAQConfigs = DAQConfigPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DAQConfigPeer::TRIAL_ID, $this->getId());

				DAQConfigPeer::addSelectColumns($criteria);
				if (!isset($this->lastDAQConfigCriteria) || !$this->lastDAQConfigCriteria->equals($criteria)) {
					$this->collDAQConfigs = DAQConfigPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDAQConfigCriteria = $criteria;
		return $this->collDAQConfigs;
	}

	/**
	 * Returns the number of related DAQConfigs.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDAQConfigs($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DAQConfigPeer::TRIAL_ID, $this->getId());

		return DAQConfigPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DAQConfig object to this object
	 * through the DAQConfig foreign key attribute
	 *
	 * @param      DAQConfig $l DAQConfig
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDAQConfig(DAQConfig $l)
	{
		$this->collDAQConfigs[] = $l;
		$l->setTrial($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related DAQConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
	 */
	public function getDAQConfigsJoinDataFileRelatedByOutputDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigs === null) {
			if ($this->isNew()) {
				$this->collDAQConfigs = array();
			} else {

				$criteria->add(DAQConfigPeer::TRIAL_ID, $this->getId());

				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinDataFileRelatedByOutputDataFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQConfigPeer::TRIAL_ID, $this->getId());

			if (!isset($this->lastDAQConfigCriteria) || !$this->lastDAQConfigCriteria->equals($criteria)) {
				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinDataFileRelatedByOutputDataFileId($criteria, $con);
			}
		}
		$this->lastDAQConfigCriteria = $criteria;

		return $this->collDAQConfigs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related DAQConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
	 */
	public function getDAQConfigsJoinDataFileRelatedByConfigDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigs === null) {
			if ($this->isNew()) {
				$this->collDAQConfigs = array();
			} else {

				$criteria->add(DAQConfigPeer::TRIAL_ID, $this->getId());

				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinDataFileRelatedByConfigDataFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQConfigPeer::TRIAL_ID, $this->getId());

			if (!isset($this->lastDAQConfigCriteria) || !$this->lastDAQConfigCriteria->equals($criteria)) {
				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinDataFileRelatedByConfigDataFileId($criteria, $con);
			}
		}
		$this->lastDAQConfigCriteria = $criteria;

		return $this->collDAQConfigs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related DAQConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
	 */
	public function getDAQConfigsJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigs === null) {
			if ($this->isNew()) {
				$this->collDAQConfigs = array();
			} else {

				$criteria->add(DAQConfigPeer::TRIAL_ID, $this->getId());

				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQConfigPeer::TRIAL_ID, $this->getId());

			if (!isset($this->lastDAQConfigCriteria) || !$this->lastDAQConfigCriteria->equals($criteria)) {
				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastDAQConfigCriteria = $criteria;

		return $this->collDAQConfigs;
	}

	/**
	 * Temporary storage of collLocationPlans to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLocationPlans()
	{
		if ($this->collLocationPlans === null) {
			$this->collLocationPlans = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial has previously
	 * been saved, it will retrieve related LocationPlans from storage.
	 * If this Trial is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLocationPlans($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPlanPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationPlans === null) {
			if ($this->isNew()) {
			   $this->collLocationPlans = array();
			} else {

				$criteria->add(LocationPlanPeer::TRIAL_ID, $this->getId());

				LocationPlanPeer::addSelectColumns($criteria);
				$this->collLocationPlans = LocationPlanPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LocationPlanPeer::TRIAL_ID, $this->getId());

				LocationPlanPeer::addSelectColumns($criteria);
				if (!isset($this->lastLocationPlanCriteria) || !$this->lastLocationPlanCriteria->equals($criteria)) {
					$this->collLocationPlans = LocationPlanPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLocationPlanCriteria = $criteria;
		return $this->collLocationPlans;
	}

	/**
	 * Returns the number of related LocationPlans.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLocationPlans($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPlanPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(LocationPlanPeer::TRIAL_ID, $this->getId());

		return LocationPlanPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a LocationPlan object to this object
	 * through the LocationPlan foreign key attribute
	 *
	 * @param      LocationPlan $l LocationPlan
	 * @return     void
	 * @throws     PropelException
	 */
	public function addLocationPlan(LocationPlan $l)
	{
		$this->collLocationPlans[] = $l;
		$l->setTrial($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related LocationPlans from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
	 */
	public function getLocationPlansJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPlanPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationPlans === null) {
			if ($this->isNew()) {
				$this->collLocationPlans = array();
			} else {

				$criteria->add(LocationPlanPeer::TRIAL_ID, $this->getId());

				$this->collLocationPlans = LocationPlanPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPlanPeer::TRIAL_ID, $this->getId());

			if (!isset($this->lastLocationPlanCriteria) || !$this->lastLocationPlanCriteria->equals($criteria)) {
				$this->collLocationPlans = LocationPlanPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastLocationPlanCriteria = $criteria;

		return $this->collLocationPlans;
	}

	/**
	 * Temporary storage of collRepetitions to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initRepetitions()
	{
		if ($this->collRepetitions === null) {
			$this->collRepetitions = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial has previously
	 * been saved, it will retrieve related Repetitions from storage.
	 * If this Trial is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getRepetitions($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseRepetitionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collRepetitions === null) {
			if ($this->isNew()) {
			   $this->collRepetitions = array();
			} else {

				$criteria->add(RepetitionPeer::TRIALID, $this->getId());

				RepetitionPeer::addSelectColumns($criteria);
				$this->collRepetitions = RepetitionPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(RepetitionPeer::TRIALID, $this->getId());

				RepetitionPeer::addSelectColumns($criteria);
				if (!isset($this->lastRepetitionCriteria) || !$this->lastRepetitionCriteria->equals($criteria)) {
					$this->collRepetitions = RepetitionPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastRepetitionCriteria = $criteria;
		return $this->collRepetitions;
	}

	/**
	 * Returns the number of related Repetitions.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countRepetitions($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseRepetitionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(RepetitionPeer::TRIALID, $this->getId());

		return RepetitionPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Repetition object to this object
	 * through the Repetition foreign key attribute
	 *
	 * @param      Repetition $l Repetition
	 * @return     void
	 * @throws     PropelException
	 */
	public function addRepetition(Repetition $l)
	{
		$this->collRepetitions[] = $l;
		$l->setTrial($this);
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
	 * Otherwise if this Trial has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 * If this Trial is new, it will return
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

				$criteria->add(DataFileLinkPeer::TRIAL_ID, $this->getId());

				DataFileLinkPeer::addSelectColumns($criteria);
				$this->collDataFileLinks = DataFileLinkPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DataFileLinkPeer::TRIAL_ID, $this->getId());

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

		$criteria->add(DataFileLinkPeer::TRIAL_ID, $this->getId());

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
		$l->setTrial($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
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

				$criteria->add(DataFileLinkPeer::TRIAL_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinProject($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::TRIAL_ID, $this->getId());

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
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
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

				$criteria->add(DataFileLinkPeer::TRIAL_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::TRIAL_ID, $this->getId());

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
	 * Otherwise if this Trial is new, it will return
	 * an empty collection; or if this Trial has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Trial.
	 */
	public function getDataFileLinksJoinRepetition($criteria = null, $con = null)
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

				$criteria->add(DataFileLinkPeer::TRIAL_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinRepetition($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::TRIAL_ID, $this->getId());

			if (!isset($this->lastDataFileLinkCriteria) || !$this->lastDataFileLinkCriteria->equals($criteria)) {
				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinRepetition($criteria, $con);
			}
		}
		$this->lastDataFileLinkCriteria = $criteria;

		return $this->collDataFileLinks;
	}

} // BaseTrial
