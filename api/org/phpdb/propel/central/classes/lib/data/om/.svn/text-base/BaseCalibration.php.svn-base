<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/CalibrationPeer.php';

/**
 * Base class that represents a row from the 'CALIBRATION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseCalibration extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CalibrationPeer
	 */
	protected static $peer;


	/**
	 * The value for the calib_id field.
	 * @var        double
	 */
	protected $calib_id;


	/**
	 * The value for the adjustments field.
	 * @var        double
	 */
	protected $adjustments;


	/**
	 * The value for the calib_date field.
	 * @var        int
	 */
	protected $calib_date;


	/**
	 * The value for the calib_factor field.
	 * @var        string
	 */
	protected $calib_factor;


	/**
	 * The value for the calib_factor_units field.
	 * @var        string
	 */
	protected $calib_factor_units;


	/**
	 * The value for the calibrator field.
	 * @var        string
	 */
	protected $calibrator;


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
	 * The value for the max_measured_value field.
	 * @var        double
	 */
	protected $max_measured_value;


	/**
	 * The value for the measured_value_units field.
	 * @var        string
	 */
	protected $measured_value_units;


	/**
	 * The value for the min_measured_value field.
	 * @var        double
	 */
	protected $min_measured_value;


	/**
	 * The value for the reference field.
	 * @var        double
	 */
	protected $reference;


	/**
	 * The value for the reference_units field.
	 * @var        string
	 */
	protected $reference_units;


	/**
	 * The value for the sensitivity field.
	 * @var        double
	 */
	protected $sensitivity;


	/**
	 * The value for the sensitivity_units field.
	 * @var        string
	 */
	protected $sensitivity_units;


	/**
	 * The value for the sensor_id field.
	 * @var        double
	 */
	protected $sensor_id;

	/**
	 * @var        Sensor
	 */
	protected $aSensor;

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
	 * Get the [calib_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->calib_id;
	}

	/**
	 * Get the [adjustments] column value.
	 * 
	 * @return     double
	 */
	public function getAdjustments()
	{

		return $this->adjustments;
	}

	/**
	 * Get the [optionally formatted] [calib_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getCalibDate($format = '%Y-%m-%d')
	{

		if ($this->calib_date === null || $this->calib_date === '') {
			return null;
		} elseif (!is_int($this->calib_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->calib_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [calib_date] as date/time value: " . var_export($this->calib_date, true));
			}
		} else {
			$ts = $this->calib_date;
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
	 * Get the [calib_factor] column value.
	 * 
	 * @return     string
	 */
	public function getCalibFactor()
	{

		return $this->calib_factor;
	}

	/**
	 * Get the [calib_factor_units] column value.
	 * 
	 * @return     string
	 */
	public function getCalibFactorUnits()
	{

		return $this->calib_factor_units;
	}

	/**
	 * Get the [calibrator] column value.
	 * 
	 * @return     string
	 */
	public function getCalibrator()
	{

		return $this->calibrator;
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
	 * Get the [max_measured_value] column value.
	 * 
	 * @return     double
	 */
	public function getMaxMeasuredValue()
	{

		return $this->max_measured_value;
	}

	/**
	 * Get the [measured_value_units] column value.
	 * 
	 * @return     string
	 */
	public function getMeasuredValueUnits()
	{

		return $this->measured_value_units;
	}

	/**
	 * Get the [min_measured_value] column value.
	 * 
	 * @return     double
	 */
	public function getMinMeasuredValue()
	{

		return $this->min_measured_value;
	}

	/**
	 * Get the [reference] column value.
	 * 
	 * @return     double
	 */
	public function getReference()
	{

		return $this->reference;
	}

	/**
	 * Get the [reference_units] column value.
	 * 
	 * @return     string
	 */
	public function getReferenceUnits()
	{

		return $this->reference_units;
	}

	/**
	 * Get the [sensitivity] column value.
	 * 
	 * @return     double
	 */
	public function getSensitivity()
	{

		return $this->sensitivity;
	}

	/**
	 * Get the [sensitivity_units] column value.
	 * 
	 * @return     string
	 */
	public function getSensitivityUnits()
	{

		return $this->sensitivity_units;
	}

	/**
	 * Get the [sensor_id] column value.
	 * 
	 * @return     double
	 */
	public function getSensorId()
	{

		return $this->sensor_id;
	}

	/**
	 * Set the value of [calib_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->calib_id !== $v) {
			$this->calib_id = $v;
			$this->modifiedColumns[] = CalibrationPeer::CALIB_ID;
		}

	} // setId()

	/**
	 * Set the value of [adjustments] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAdjustments($v)
	{

		if ($this->adjustments !== $v) {
			$this->adjustments = $v;
			$this->modifiedColumns[] = CalibrationPeer::ADJUSTMENTS;
		}

	} // setAdjustments()

	/**
	 * Set the value of [calib_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setCalibDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [calib_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->calib_date !== $ts) {
			$this->calib_date = $ts;
			$this->modifiedColumns[] = CalibrationPeer::CALIB_DATE;
		}

	} // setCalibDate()

	/**
	 * Set the value of [calib_factor] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCalibFactor($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->calib_factor !== $v) {
			$this->calib_factor = $v;
			$this->modifiedColumns[] = CalibrationPeer::CALIB_FACTOR;
		}

	} // setCalibFactor()

	/**
	 * Set the value of [calib_factor_units] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCalibFactorUnits($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->calib_factor_units !== $v) {
			$this->calib_factor_units = $v;
			$this->modifiedColumns[] = CalibrationPeer::CALIB_FACTOR_UNITS;
		}

	} // setCalibFactorUnits()

	/**
	 * Set the value of [calibrator] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCalibrator($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->calibrator !== $v) {
			$this->calibrator = $v;
			$this->modifiedColumns[] = CalibrationPeer::CALIBRATOR;
		}

	} // setCalibrator()

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
			$this->modifiedColumns[] = CalibrationPeer::DELETED;
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

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = CalibrationPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [max_measured_value] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMaxMeasuredValue($v)
	{

		if ($this->max_measured_value !== $v) {
			$this->max_measured_value = $v;
			$this->modifiedColumns[] = CalibrationPeer::MAX_MEASURED_VALUE;
		}

	} // setMaxMeasuredValue()

	/**
	 * Set the value of [measured_value_units] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setMeasuredValueUnits($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->measured_value_units !== $v) {
			$this->measured_value_units = $v;
			$this->modifiedColumns[] = CalibrationPeer::MEASURED_VALUE_UNITS;
		}

	} // setMeasuredValueUnits()

	/**
	 * Set the value of [min_measured_value] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMinMeasuredValue($v)
	{

		if ($this->min_measured_value !== $v) {
			$this->min_measured_value = $v;
			$this->modifiedColumns[] = CalibrationPeer::MIN_MEASURED_VALUE;
		}

	} // setMinMeasuredValue()

	/**
	 * Set the value of [reference] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setReference($v)
	{

		if ($this->reference !== $v) {
			$this->reference = $v;
			$this->modifiedColumns[] = CalibrationPeer::REFERENCE;
		}

	} // setReference()

	/**
	 * Set the value of [reference_units] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setReferenceUnits($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->reference_units !== $v) {
			$this->reference_units = $v;
			$this->modifiedColumns[] = CalibrationPeer::REFERENCE_UNITS;
		}

	} // setReferenceUnits()

	/**
	 * Set the value of [sensitivity] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSensitivity($v)
	{

		if ($this->sensitivity !== $v) {
			$this->sensitivity = $v;
			$this->modifiedColumns[] = CalibrationPeer::SENSITIVITY;
		}

	} // setSensitivity()

	/**
	 * Set the value of [sensitivity_units] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSensitivityUnits($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sensitivity_units !== $v) {
			$this->sensitivity_units = $v;
			$this->modifiedColumns[] = CalibrationPeer::SENSITIVITY_UNITS;
		}

	} // setSensitivityUnits()

	/**
	 * Set the value of [sensor_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSensorId($v)
	{

		if ($this->sensor_id !== $v) {
			$this->sensor_id = $v;
			$this->modifiedColumns[] = CalibrationPeer::SENSOR_ID;
		}

		if ($this->aSensor !== null && $this->aSensor->getId() !== $v) {
			$this->aSensor = null;
		}

	} // setSensorId()

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

			$this->calib_id = $rs->getFloat($startcol + 0);

			$this->adjustments = $rs->getFloat($startcol + 1);

			$this->calib_date = $rs->getDate($startcol + 2, null);

			$this->calib_factor = $rs->getString($startcol + 3);

			$this->calib_factor_units = $rs->getString($startcol + 4);

			$this->calibrator = $rs->getString($startcol + 5);

			$this->deleted = $rs->getFloat($startcol + 6);

			$this->description = $rs->getString($startcol + 7);

			$this->max_measured_value = $rs->getFloat($startcol + 8);

			$this->measured_value_units = $rs->getString($startcol + 9);

			$this->min_measured_value = $rs->getFloat($startcol + 10);

			$this->reference = $rs->getFloat($startcol + 11);

			$this->reference_units = $rs->getString($startcol + 12);

			$this->sensitivity = $rs->getFloat($startcol + 13);

			$this->sensitivity_units = $rs->getString($startcol + 14);

			$this->sensor_id = $rs->getFloat($startcol + 15);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 16; // 16 = CalibrationPeer::NUM_COLUMNS - CalibrationPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Calibration object", $e);
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
			$con = Propel::getConnection(CalibrationPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			CalibrationPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(CalibrationPeer::DATABASE_NAME);
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

			if ($this->aSensor !== null) {
				if ($this->aSensor->isModified()) {
					$affectedRows += $this->aSensor->save($con);
				}
				$this->setSensor($this->aSensor);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = CalibrationPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += CalibrationPeer::doUpdate($this, $con);
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

			if ($this->aSensor !== null) {
				if (!$this->aSensor->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSensor->getValidationFailures());
				}
			}


			if (($retval = CalibrationPeer::doValidate($this, $columns)) !== true) {
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
		$pos = CalibrationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAdjustments();
				break;
			case 2:
				return $this->getCalibDate();
				break;
			case 3:
				return $this->getCalibFactor();
				break;
			case 4:
				return $this->getCalibFactorUnits();
				break;
			case 5:
				return $this->getCalibrator();
				break;
			case 6:
				return $this->getDeleted();
				break;
			case 7:
				return $this->getDescription();
				break;
			case 8:
				return $this->getMaxMeasuredValue();
				break;
			case 9:
				return $this->getMeasuredValueUnits();
				break;
			case 10:
				return $this->getMinMeasuredValue();
				break;
			case 11:
				return $this->getReference();
				break;
			case 12:
				return $this->getReferenceUnits();
				break;
			case 13:
				return $this->getSensitivity();
				break;
			case 14:
				return $this->getSensitivityUnits();
				break;
			case 15:
				return $this->getSensorId();
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
		$keys = CalibrationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getAdjustments(),
			$keys[2] => $this->getCalibDate(),
			$keys[3] => $this->getCalibFactor(),
			$keys[4] => $this->getCalibFactorUnits(),
			$keys[5] => $this->getCalibrator(),
			$keys[6] => $this->getDeleted(),
			$keys[7] => $this->getDescription(),
			$keys[8] => $this->getMaxMeasuredValue(),
			$keys[9] => $this->getMeasuredValueUnits(),
			$keys[10] => $this->getMinMeasuredValue(),
			$keys[11] => $this->getReference(),
			$keys[12] => $this->getReferenceUnits(),
			$keys[13] => $this->getSensitivity(),
			$keys[14] => $this->getSensitivityUnits(),
			$keys[15] => $this->getSensorId(),
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
		$pos = CalibrationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAdjustments($value);
				break;
			case 2:
				$this->setCalibDate($value);
				break;
			case 3:
				$this->setCalibFactor($value);
				break;
			case 4:
				$this->setCalibFactorUnits($value);
				break;
			case 5:
				$this->setCalibrator($value);
				break;
			case 6:
				$this->setDeleted($value);
				break;
			case 7:
				$this->setDescription($value);
				break;
			case 8:
				$this->setMaxMeasuredValue($value);
				break;
			case 9:
				$this->setMeasuredValueUnits($value);
				break;
			case 10:
				$this->setMinMeasuredValue($value);
				break;
			case 11:
				$this->setReference($value);
				break;
			case 12:
				$this->setReferenceUnits($value);
				break;
			case 13:
				$this->setSensitivity($value);
				break;
			case 14:
				$this->setSensitivityUnits($value);
				break;
			case 15:
				$this->setSensorId($value);
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
		$keys = CalibrationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAdjustments($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCalibDate($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCalibFactor($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setCalibFactorUnits($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCalibrator($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDeleted($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDescription($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setMaxMeasuredValue($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setMeasuredValueUnits($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setMinMeasuredValue($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setReference($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setReferenceUnits($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setSensitivity($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setSensitivityUnits($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setSensorId($arr[$keys[15]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CalibrationPeer::DATABASE_NAME);

		if ($this->isColumnModified(CalibrationPeer::CALIB_ID)) $criteria->add(CalibrationPeer::CALIB_ID, $this->calib_id);
		if ($this->isColumnModified(CalibrationPeer::ADJUSTMENTS)) $criteria->add(CalibrationPeer::ADJUSTMENTS, $this->adjustments);
		if ($this->isColumnModified(CalibrationPeer::CALIB_DATE)) $criteria->add(CalibrationPeer::CALIB_DATE, $this->calib_date);
		if ($this->isColumnModified(CalibrationPeer::CALIB_FACTOR)) $criteria->add(CalibrationPeer::CALIB_FACTOR, $this->calib_factor);
		if ($this->isColumnModified(CalibrationPeer::CALIB_FACTOR_UNITS)) $criteria->add(CalibrationPeer::CALIB_FACTOR_UNITS, $this->calib_factor_units);
		if ($this->isColumnModified(CalibrationPeer::CALIBRATOR)) $criteria->add(CalibrationPeer::CALIBRATOR, $this->calibrator);
		if ($this->isColumnModified(CalibrationPeer::DELETED)) $criteria->add(CalibrationPeer::DELETED, $this->deleted);
		if ($this->isColumnModified(CalibrationPeer::DESCRIPTION)) $criteria->add(CalibrationPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(CalibrationPeer::MAX_MEASURED_VALUE)) $criteria->add(CalibrationPeer::MAX_MEASURED_VALUE, $this->max_measured_value);
		if ($this->isColumnModified(CalibrationPeer::MEASURED_VALUE_UNITS)) $criteria->add(CalibrationPeer::MEASURED_VALUE_UNITS, $this->measured_value_units);
		if ($this->isColumnModified(CalibrationPeer::MIN_MEASURED_VALUE)) $criteria->add(CalibrationPeer::MIN_MEASURED_VALUE, $this->min_measured_value);
		if ($this->isColumnModified(CalibrationPeer::REFERENCE)) $criteria->add(CalibrationPeer::REFERENCE, $this->reference);
		if ($this->isColumnModified(CalibrationPeer::REFERENCE_UNITS)) $criteria->add(CalibrationPeer::REFERENCE_UNITS, $this->reference_units);
		if ($this->isColumnModified(CalibrationPeer::SENSITIVITY)) $criteria->add(CalibrationPeer::SENSITIVITY, $this->sensitivity);
		if ($this->isColumnModified(CalibrationPeer::SENSITIVITY_UNITS)) $criteria->add(CalibrationPeer::SENSITIVITY_UNITS, $this->sensitivity_units);
		if ($this->isColumnModified(CalibrationPeer::SENSOR_ID)) $criteria->add(CalibrationPeer::SENSOR_ID, $this->sensor_id);

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
		$criteria = new Criteria(CalibrationPeer::DATABASE_NAME);

		$criteria->add(CalibrationPeer::CALIB_ID, $this->calib_id);

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
	 * Generic method to set the primary key (calib_id column).
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
	 * @param      object $copyObj An object of Calibration (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAdjustments($this->adjustments);

		$copyObj->setCalibDate($this->calib_date);

		$copyObj->setCalibFactor($this->calib_factor);

		$copyObj->setCalibFactorUnits($this->calib_factor_units);

		$copyObj->setCalibrator($this->calibrator);

		$copyObj->setDeleted($this->deleted);

		$copyObj->setDescription($this->description);

		$copyObj->setMaxMeasuredValue($this->max_measured_value);

		$copyObj->setMeasuredValueUnits($this->measured_value_units);

		$copyObj->setMinMeasuredValue($this->min_measured_value);

		$copyObj->setReference($this->reference);

		$copyObj->setReferenceUnits($this->reference_units);

		$copyObj->setSensitivity($this->sensitivity);

		$copyObj->setSensitivityUnits($this->sensitivity_units);

		$copyObj->setSensorId($this->sensor_id);


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
	 * @return     Calibration Clone of current object.
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
	 * @return     CalibrationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CalibrationPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Sensor object.
	 *
	 * @param      Sensor $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSensor($v)
	{


		if ($v === null) {
			$this->setSensorId(NULL);
		} else {
			$this->setSensorId($v->getId());
		}


		$this->aSensor = $v;
	}


	/**
	 * Get the associated Sensor object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Sensor The associated Sensor object.
	 * @throws     PropelException
	 */
	public function getSensor($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSensorPeer.php';

		if ($this->aSensor === null && ($this->sensor_id > 0)) {

			$this->aSensor = SensorPeer::retrieveByPK($this->sensor_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SensorPeer::retrieveByPK($this->sensor_id, $con);
			   $obj->addSensors($this);
			 */
		}
		return $this->aSensor;
	}

} // BaseCalibration
