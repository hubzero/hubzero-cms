<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SensorModelPeer.php';

/**
 * Base class that represents a row from the 'SENSOR_MODEL' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSensorModel extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SensorModelPeer
	 */
	protected static $peer;


	/**
	 * The value for the sensor_model_id field.
	 * @var        double
	 */
	protected $sensor_model_id;


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
	 * The value for the manufacturer field.
	 * @var        string
	 */
	protected $manufacturer;


	/**
	 * The value for the max_measured_value field.
	 * @var        double
	 */
	protected $max_measured_value;


	/**
	 * The value for the max_op_temp field.
	 * @var        double
	 */
	protected $max_op_temp;


	/**
	 * The value for the measured_value_units_id field.
	 * @var        double
	 */
	protected $measured_value_units_id;


	/**
	 * The value for the min_measured_value field.
	 * @var        double
	 */
	protected $min_measured_value;


	/**
	 * The value for the min_op_temp field.
	 * @var        double
	 */
	protected $min_op_temp;


	/**
	 * The value for the model field.
	 * @var        string
	 */
	protected $model;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the note field.
	 * @var        string
	 */
	protected $note;


	/**
	 * The value for the sensitivity field.
	 * @var        double
	 */
	protected $sensitivity;


	/**
	 * The value for the sensitivity_units_id field.
	 * @var        double
	 */
	protected $sensitivity_units_id;


	/**
	 * The value for the sensor_type_id field.
	 * @var        double
	 */
	protected $sensor_type_id;


	/**
	 * The value for the signal_type field.
	 * @var        string
	 */
	protected $signal_type;


	/**
	 * The value for the temp_units_id field.
	 * @var        double
	 */
	protected $temp_units_id;

	/**
	 * @var        SensorType
	 */
	protected $aSensorType;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByMeasuredValueUnitsId;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedBySensitivityUnitsId;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByTempUnitsId;

	/**
	 * Collection to store aggregation of collSensors.
	 * @var        array
	 */
	protected $collSensors;

	/**
	 * The criteria used to select the current contents of collSensors.
	 * @var        Criteria
	 */
	protected $lastSensorCriteria = null;

	/**
	 * Collection to store aggregation of collSensorModelDataFiles.
	 * @var        array
	 */
	protected $collSensorModelDataFiles;

	/**
	 * The criteria used to select the current contents of collSensorModelDataFiles.
	 * @var        Criteria
	 */
	protected $lastSensorModelDataFileCriteria = null;

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
	 * Get the [sensor_model_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->sensor_model_id;
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
	 * Get the [manufacturer] column value.
	 * 
	 * @return     string
	 */
	public function getManufacturer()
	{

		return $this->manufacturer;
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
	 * Get the [max_op_temp] column value.
	 * 
	 * @return     double
	 */
	public function getMaxOpTemp()
	{

		return $this->max_op_temp;
	}

	/**
	 * Get the [measured_value_units_id] column value.
	 * 
	 * @return     double
	 */
	public function getMeasuredValueUnitsId()
	{

		return $this->measured_value_units_id;
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
	 * Get the [min_op_temp] column value.
	 * 
	 * @return     double
	 */
	public function getMinOpTemp()
	{

		return $this->min_op_temp;
	}

	/**
	 * Get the [model] column value.
	 * 
	 * @return     string
	 */
	public function getModel()
	{

		return $this->model;
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
	 * Get the [note] column value.
	 * 
	 * @return     string
	 */
	public function getNote()
	{

		return $this->note;
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
	 * Get the [sensitivity_units_id] column value.
	 * 
	 * @return     double
	 */
	public function getSensitivityUnitsId()
	{

		return $this->sensitivity_units_id;
	}

	/**
	 * Get the [sensor_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getSensorTypeId()
	{

		return $this->sensor_type_id;
	}

	/**
	 * Get the [signal_type] column value.
	 * 
	 * @return     string
	 */
	public function getSignalType()
	{

		return $this->signal_type;
	}

	/**
	 * Get the [temp_units_id] column value.
	 * 
	 * @return     double
	 */
	public function getTempUnitsId()
	{

		return $this->temp_units_id;
	}

	/**
	 * Set the value of [sensor_model_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->sensor_model_id !== $v) {
			$this->sensor_model_id = $v;
			$this->modifiedColumns[] = SensorModelPeer::SENSOR_MODEL_ID;
		}

	} // setId()

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
			$this->modifiedColumns[] = SensorModelPeer::DELETED;
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
			$this->modifiedColumns[] = SensorModelPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [manufacturer] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setManufacturer($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->manufacturer !== $v) {
			$this->manufacturer = $v;
			$this->modifiedColumns[] = SensorModelPeer::MANUFACTURER;
		}

	} // setManufacturer()

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
			$this->modifiedColumns[] = SensorModelPeer::MAX_MEASURED_VALUE;
		}

	} // setMaxMeasuredValue()

	/**
	 * Set the value of [max_op_temp] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMaxOpTemp($v)
	{

		if ($this->max_op_temp !== $v) {
			$this->max_op_temp = $v;
			$this->modifiedColumns[] = SensorModelPeer::MAX_OP_TEMP;
		}

	} // setMaxOpTemp()

	/**
	 * Set the value of [measured_value_units_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMeasuredValueUnitsId($v)
	{

		if ($this->measured_value_units_id !== $v) {
			$this->measured_value_units_id = $v;
			$this->modifiedColumns[] = SensorModelPeer::MEASURED_VALUE_UNITS_ID;
		}

		if ($this->aMeasurementUnitRelatedByMeasuredValueUnitsId !== null && $this->aMeasurementUnitRelatedByMeasuredValueUnitsId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByMeasuredValueUnitsId = null;
		}

	} // setMeasuredValueUnitsId()

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
			$this->modifiedColumns[] = SensorModelPeer::MIN_MEASURED_VALUE;
		}

	} // setMinMeasuredValue()

	/**
	 * Set the value of [min_op_temp] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMinOpTemp($v)
	{

		if ($this->min_op_temp !== $v) {
			$this->min_op_temp = $v;
			$this->modifiedColumns[] = SensorModelPeer::MIN_OP_TEMP;
		}

	} // setMinOpTemp()

	/**
	 * Set the value of [model] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setModel($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->model !== $v) {
			$this->model = $v;
			$this->modifiedColumns[] = SensorModelPeer::MODEL;
		}

	} // setModel()

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
			$this->modifiedColumns[] = SensorModelPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [note] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNote($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->note !== $v) {
			$this->note = $v;
			$this->modifiedColumns[] = SensorModelPeer::NOTE;
		}

	} // setNote()

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
			$this->modifiedColumns[] = SensorModelPeer::SENSITIVITY;
		}

	} // setSensitivity()

	/**
	 * Set the value of [sensitivity_units_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSensitivityUnitsId($v)
	{

		if ($this->sensitivity_units_id !== $v) {
			$this->sensitivity_units_id = $v;
			$this->modifiedColumns[] = SensorModelPeer::SENSITIVITY_UNITS_ID;
		}

		if ($this->aMeasurementUnitRelatedBySensitivityUnitsId !== null && $this->aMeasurementUnitRelatedBySensitivityUnitsId->getId() !== $v) {
			$this->aMeasurementUnitRelatedBySensitivityUnitsId = null;
		}

	} // setSensitivityUnitsId()

	/**
	 * Set the value of [sensor_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSensorTypeId($v)
	{

		if ($this->sensor_type_id !== $v) {
			$this->sensor_type_id = $v;
			$this->modifiedColumns[] = SensorModelPeer::SENSOR_TYPE_ID;
		}

		if ($this->aSensorType !== null && $this->aSensorType->getId() !== $v) {
			$this->aSensorType = null;
		}

	} // setSensorTypeId()

	/**
	 * Set the value of [signal_type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSignalType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->signal_type !== $v) {
			$this->signal_type = $v;
			$this->modifiedColumns[] = SensorModelPeer::SIGNAL_TYPE;
		}

	} // setSignalType()

	/**
	 * Set the value of [temp_units_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTempUnitsId($v)
	{

		if ($this->temp_units_id !== $v) {
			$this->temp_units_id = $v;
			$this->modifiedColumns[] = SensorModelPeer::TEMP_UNITS_ID;
		}

		if ($this->aMeasurementUnitRelatedByTempUnitsId !== null && $this->aMeasurementUnitRelatedByTempUnitsId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByTempUnitsId = null;
		}

	} // setTempUnitsId()

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

			$this->sensor_model_id = $rs->getFloat($startcol + 0);

			$this->deleted = $rs->getFloat($startcol + 1);

			$this->description = $rs->getString($startcol + 2);

			$this->manufacturer = $rs->getString($startcol + 3);

			$this->max_measured_value = $rs->getFloat($startcol + 4);

			$this->max_op_temp = $rs->getFloat($startcol + 5);

			$this->measured_value_units_id = $rs->getFloat($startcol + 6);

			$this->min_measured_value = $rs->getFloat($startcol + 7);

			$this->min_op_temp = $rs->getFloat($startcol + 8);

			$this->model = $rs->getString($startcol + 9);

			$this->name = $rs->getString($startcol + 10);

			$this->note = $rs->getString($startcol + 11);

			$this->sensitivity = $rs->getFloat($startcol + 12);

			$this->sensitivity_units_id = $rs->getFloat($startcol + 13);

			$this->sensor_type_id = $rs->getFloat($startcol + 14);

			$this->signal_type = $rs->getString($startcol + 15);

			$this->temp_units_id = $rs->getFloat($startcol + 16);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 17; // 17 = SensorModelPeer::NUM_COLUMNS - SensorModelPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SensorModel object", $e);
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
			$con = Propel::getConnection(SensorModelPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SensorModelPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SensorModelPeer::DATABASE_NAME);
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

			if ($this->aSensorType !== null) {
				if ($this->aSensorType->isModified()) {
					$affectedRows += $this->aSensorType->save($con);
				}
				$this->setSensorType($this->aSensorType);
			}

			if ($this->aMeasurementUnitRelatedByMeasuredValueUnitsId !== null) {
				if ($this->aMeasurementUnitRelatedByMeasuredValueUnitsId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByMeasuredValueUnitsId->save($con);
				}
				$this->setMeasurementUnitRelatedByMeasuredValueUnitsId($this->aMeasurementUnitRelatedByMeasuredValueUnitsId);
			}

			if ($this->aMeasurementUnitRelatedBySensitivityUnitsId !== null) {
				if ($this->aMeasurementUnitRelatedBySensitivityUnitsId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedBySensitivityUnitsId->save($con);
				}
				$this->setMeasurementUnitRelatedBySensitivityUnitsId($this->aMeasurementUnitRelatedBySensitivityUnitsId);
			}

			if ($this->aMeasurementUnitRelatedByTempUnitsId !== null) {
				if ($this->aMeasurementUnitRelatedByTempUnitsId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByTempUnitsId->save($con);
				}
				$this->setMeasurementUnitRelatedByTempUnitsId($this->aMeasurementUnitRelatedByTempUnitsId);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SensorModelPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SensorModelPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collSensors !== null) {
				foreach($this->collSensors as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorModelDataFiles !== null) {
				foreach($this->collSensorModelDataFiles as $referrerFK) {
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

			if ($this->aSensorType !== null) {
				if (!$this->aSensorType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSensorType->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByMeasuredValueUnitsId !== null) {
				if (!$this->aMeasurementUnitRelatedByMeasuredValueUnitsId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByMeasuredValueUnitsId->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedBySensitivityUnitsId !== null) {
				if (!$this->aMeasurementUnitRelatedBySensitivityUnitsId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedBySensitivityUnitsId->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByTempUnitsId !== null) {
				if (!$this->aMeasurementUnitRelatedByTempUnitsId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByTempUnitsId->getValidationFailures());
				}
			}


			if (($retval = SensorModelPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collSensors !== null) {
					foreach($this->collSensors as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorModelDataFiles !== null) {
					foreach($this->collSensorModelDataFiles as $referrerFK) {
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
		$pos = SensorModelPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDeleted();
				break;
			case 2:
				return $this->getDescription();
				break;
			case 3:
				return $this->getManufacturer();
				break;
			case 4:
				return $this->getMaxMeasuredValue();
				break;
			case 5:
				return $this->getMaxOpTemp();
				break;
			case 6:
				return $this->getMeasuredValueUnitsId();
				break;
			case 7:
				return $this->getMinMeasuredValue();
				break;
			case 8:
				return $this->getMinOpTemp();
				break;
			case 9:
				return $this->getModel();
				break;
			case 10:
				return $this->getName();
				break;
			case 11:
				return $this->getNote();
				break;
			case 12:
				return $this->getSensitivity();
				break;
			case 13:
				return $this->getSensitivityUnitsId();
				break;
			case 14:
				return $this->getSensorTypeId();
				break;
			case 15:
				return $this->getSignalType();
				break;
			case 16:
				return $this->getTempUnitsId();
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
		$keys = SensorModelPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDeleted(),
			$keys[2] => $this->getDescription(),
			$keys[3] => $this->getManufacturer(),
			$keys[4] => $this->getMaxMeasuredValue(),
			$keys[5] => $this->getMaxOpTemp(),
			$keys[6] => $this->getMeasuredValueUnitsId(),
			$keys[7] => $this->getMinMeasuredValue(),
			$keys[8] => $this->getMinOpTemp(),
			$keys[9] => $this->getModel(),
			$keys[10] => $this->getName(),
			$keys[11] => $this->getNote(),
			$keys[12] => $this->getSensitivity(),
			$keys[13] => $this->getSensitivityUnitsId(),
			$keys[14] => $this->getSensorTypeId(),
			$keys[15] => $this->getSignalType(),
			$keys[16] => $this->getTempUnitsId(),
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
		$pos = SensorModelPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDeleted($value);
				break;
			case 2:
				$this->setDescription($value);
				break;
			case 3:
				$this->setManufacturer($value);
				break;
			case 4:
				$this->setMaxMeasuredValue($value);
				break;
			case 5:
				$this->setMaxOpTemp($value);
				break;
			case 6:
				$this->setMeasuredValueUnitsId($value);
				break;
			case 7:
				$this->setMinMeasuredValue($value);
				break;
			case 8:
				$this->setMinOpTemp($value);
				break;
			case 9:
				$this->setModel($value);
				break;
			case 10:
				$this->setName($value);
				break;
			case 11:
				$this->setNote($value);
				break;
			case 12:
				$this->setSensitivity($value);
				break;
			case 13:
				$this->setSensitivityUnitsId($value);
				break;
			case 14:
				$this->setSensorTypeId($value);
				break;
			case 15:
				$this->setSignalType($value);
				break;
			case 16:
				$this->setTempUnitsId($value);
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
		$keys = SensorModelPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDeleted($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDescription($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setManufacturer($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setMaxMeasuredValue($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setMaxOpTemp($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setMeasuredValueUnitsId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setMinMeasuredValue($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setMinOpTemp($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setModel($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setName($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setNote($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setSensitivity($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setSensitivityUnitsId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setSensorTypeId($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setSignalType($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setTempUnitsId($arr[$keys[16]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SensorModelPeer::DATABASE_NAME);

		if ($this->isColumnModified(SensorModelPeer::SENSOR_MODEL_ID)) $criteria->add(SensorModelPeer::SENSOR_MODEL_ID, $this->sensor_model_id);
		if ($this->isColumnModified(SensorModelPeer::DELETED)) $criteria->add(SensorModelPeer::DELETED, $this->deleted);
		if ($this->isColumnModified(SensorModelPeer::DESCRIPTION)) $criteria->add(SensorModelPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(SensorModelPeer::MANUFACTURER)) $criteria->add(SensorModelPeer::MANUFACTURER, $this->manufacturer);
		if ($this->isColumnModified(SensorModelPeer::MAX_MEASURED_VALUE)) $criteria->add(SensorModelPeer::MAX_MEASURED_VALUE, $this->max_measured_value);
		if ($this->isColumnModified(SensorModelPeer::MAX_OP_TEMP)) $criteria->add(SensorModelPeer::MAX_OP_TEMP, $this->max_op_temp);
		if ($this->isColumnModified(SensorModelPeer::MEASURED_VALUE_UNITS_ID)) $criteria->add(SensorModelPeer::MEASURED_VALUE_UNITS_ID, $this->measured_value_units_id);
		if ($this->isColumnModified(SensorModelPeer::MIN_MEASURED_VALUE)) $criteria->add(SensorModelPeer::MIN_MEASURED_VALUE, $this->min_measured_value);
		if ($this->isColumnModified(SensorModelPeer::MIN_OP_TEMP)) $criteria->add(SensorModelPeer::MIN_OP_TEMP, $this->min_op_temp);
		if ($this->isColumnModified(SensorModelPeer::MODEL)) $criteria->add(SensorModelPeer::MODEL, $this->model);
		if ($this->isColumnModified(SensorModelPeer::NAME)) $criteria->add(SensorModelPeer::NAME, $this->name);
		if ($this->isColumnModified(SensorModelPeer::NOTE)) $criteria->add(SensorModelPeer::NOTE, $this->note);
		if ($this->isColumnModified(SensorModelPeer::SENSITIVITY)) $criteria->add(SensorModelPeer::SENSITIVITY, $this->sensitivity);
		if ($this->isColumnModified(SensorModelPeer::SENSITIVITY_UNITS_ID)) $criteria->add(SensorModelPeer::SENSITIVITY_UNITS_ID, $this->sensitivity_units_id);
		if ($this->isColumnModified(SensorModelPeer::SENSOR_TYPE_ID)) $criteria->add(SensorModelPeer::SENSOR_TYPE_ID, $this->sensor_type_id);
		if ($this->isColumnModified(SensorModelPeer::SIGNAL_TYPE)) $criteria->add(SensorModelPeer::SIGNAL_TYPE, $this->signal_type);
		if ($this->isColumnModified(SensorModelPeer::TEMP_UNITS_ID)) $criteria->add(SensorModelPeer::TEMP_UNITS_ID, $this->temp_units_id);

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
		$criteria = new Criteria(SensorModelPeer::DATABASE_NAME);

		$criteria->add(SensorModelPeer::SENSOR_MODEL_ID, $this->sensor_model_id);

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
	 * Generic method to set the primary key (sensor_model_id column).
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
	 * @param      object $copyObj An object of SensorModel (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDeleted($this->deleted);

		$copyObj->setDescription($this->description);

		$copyObj->setManufacturer($this->manufacturer);

		$copyObj->setMaxMeasuredValue($this->max_measured_value);

		$copyObj->setMaxOpTemp($this->max_op_temp);

		$copyObj->setMeasuredValueUnitsId($this->measured_value_units_id);

		$copyObj->setMinMeasuredValue($this->min_measured_value);

		$copyObj->setMinOpTemp($this->min_op_temp);

		$copyObj->setModel($this->model);

		$copyObj->setName($this->name);

		$copyObj->setNote($this->note);

		$copyObj->setSensitivity($this->sensitivity);

		$copyObj->setSensitivityUnitsId($this->sensitivity_units_id);

		$copyObj->setSensorTypeId($this->sensor_type_id);

		$copyObj->setSignalType($this->signal_type);

		$copyObj->setTempUnitsId($this->temp_units_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getSensors() as $relObj) {
				$copyObj->addSensor($relObj->copy($deepCopy));
			}

			foreach($this->getSensorModelDataFiles() as $relObj) {
				$copyObj->addSensorModelDataFile($relObj->copy($deepCopy));
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
	 * @return     SensorModel Clone of current object.
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
	 * @return     SensorModelPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SensorModelPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a SensorType object.
	 *
	 * @param      SensorType $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSensorType($v)
	{


		if ($v === null) {
			$this->setSensorTypeId(NULL);
		} else {
			$this->setSensorTypeId($v->getId());
		}


		$this->aSensorType = $v;
	}


	/**
	 * Get the associated SensorType object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SensorType The associated SensorType object.
	 * @throws     PropelException
	 */
	public function getSensorType($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSensorTypePeer.php';

		if ($this->aSensorType === null && ($this->sensor_type_id > 0)) {

			$this->aSensorType = SensorTypePeer::retrieveByPK($this->sensor_type_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SensorTypePeer::retrieveByPK($this->sensor_type_id, $con);
			   $obj->addSensorTypes($this);
			 */
		}
		return $this->aSensorType;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByMeasuredValueUnitsId($v)
	{


		if ($v === null) {
			$this->setMeasuredValueUnitsId(NULL);
		} else {
			$this->setMeasuredValueUnitsId($v->getId());
		}


		$this->aMeasurementUnitRelatedByMeasuredValueUnitsId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByMeasuredValueUnitsId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByMeasuredValueUnitsId === null && ($this->measured_value_units_id > 0)) {

			$this->aMeasurementUnitRelatedByMeasuredValueUnitsId = MeasurementUnitPeer::retrieveByPK($this->measured_value_units_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->measured_value_units_id, $con);
			   $obj->addMeasurementUnitsRelatedByMeasuredValueUnitsId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByMeasuredValueUnitsId;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedBySensitivityUnitsId($v)
	{


		if ($v === null) {
			$this->setSensitivityUnitsId(NULL);
		} else {
			$this->setSensitivityUnitsId($v->getId());
		}


		$this->aMeasurementUnitRelatedBySensitivityUnitsId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedBySensitivityUnitsId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedBySensitivityUnitsId === null && ($this->sensitivity_units_id > 0)) {

			$this->aMeasurementUnitRelatedBySensitivityUnitsId = MeasurementUnitPeer::retrieveByPK($this->sensitivity_units_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->sensitivity_units_id, $con);
			   $obj->addMeasurementUnitsRelatedBySensitivityUnitsId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedBySensitivityUnitsId;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByTempUnitsId($v)
	{


		if ($v === null) {
			$this->setTempUnitsId(NULL);
		} else {
			$this->setTempUnitsId($v->getId());
		}


		$this->aMeasurementUnitRelatedByTempUnitsId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByTempUnitsId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByTempUnitsId === null && ($this->temp_units_id > 0)) {

			$this->aMeasurementUnitRelatedByTempUnitsId = MeasurementUnitPeer::retrieveByPK($this->temp_units_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->temp_units_id, $con);
			   $obj->addMeasurementUnitsRelatedByTempUnitsId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByTempUnitsId;
	}

	/**
	 * Temporary storage of collSensors to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensors()
	{
		if ($this->collSensors === null) {
			$this->collSensors = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorModel has previously
	 * been saved, it will retrieve related Sensors from storage.
	 * If this SensorModel is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensors($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensors === null) {
			if ($this->isNew()) {
			   $this->collSensors = array();
			} else {

				$criteria->add(SensorPeer::SENSOR_MODEL_ID, $this->getId());

				SensorPeer::addSelectColumns($criteria);
				$this->collSensors = SensorPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorPeer::SENSOR_MODEL_ID, $this->getId());

				SensorPeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorCriteria) || !$this->lastSensorCriteria->equals($criteria)) {
					$this->collSensors = SensorPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorCriteria = $criteria;
		return $this->collSensors;
	}

	/**
	 * Returns the number of related Sensors.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensors($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorPeer::SENSOR_MODEL_ID, $this->getId());

		return SensorPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Sensor object to this object
	 * through the Sensor foreign key attribute
	 *
	 * @param      Sensor $l Sensor
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensor(Sensor $l)
	{
		$this->collSensors[] = $l;
		$l->setSensorModel($this);
	}

	/**
	 * Temporary storage of collSensorModelDataFiles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorModelDataFiles()
	{
		if ($this->collSensorModelDataFiles === null) {
			$this->collSensorModelDataFiles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorModel has previously
	 * been saved, it will retrieve related SensorModelDataFiles from storage.
	 * If this SensorModel is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorModelDataFiles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModelDataFiles === null) {
			if ($this->isNew()) {
			   $this->collSensorModelDataFiles = array();
			} else {

				$criteria->add(SensorModelDataFilePeer::SENSOR_MODEL_ID, $this->getId());

				SensorModelDataFilePeer::addSelectColumns($criteria);
				$this->collSensorModelDataFiles = SensorModelDataFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorModelDataFilePeer::SENSOR_MODEL_ID, $this->getId());

				SensorModelDataFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorModelDataFileCriteria) || !$this->lastSensorModelDataFileCriteria->equals($criteria)) {
					$this->collSensorModelDataFiles = SensorModelDataFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorModelDataFileCriteria = $criteria;
		return $this->collSensorModelDataFiles;
	}

	/**
	 * Returns the number of related SensorModelDataFiles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorModelDataFiles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorModelDataFilePeer::SENSOR_MODEL_ID, $this->getId());

		return SensorModelDataFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorModelDataFile object to this object
	 * through the SensorModelDataFile foreign key attribute
	 *
	 * @param      SensorModelDataFile $l SensorModelDataFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorModelDataFile(SensorModelDataFile $l)
	{
		$this->collSensorModelDataFiles[] = $l;
		$l->setSensorModel($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorModel is new, it will return
	 * an empty collection; or if this SensorModel has previously
	 * been saved, it will retrieve related SensorModelDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorModel.
	 */
	public function getSensorModelDataFilesJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModelDataFiles === null) {
			if ($this->isNew()) {
				$this->collSensorModelDataFiles = array();
			} else {

				$criteria->add(SensorModelDataFilePeer::SENSOR_MODEL_ID, $this->getId());

				$this->collSensorModelDataFiles = SensorModelDataFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorModelDataFilePeer::SENSOR_MODEL_ID, $this->getId());

			if (!isset($this->lastSensorModelDataFileCriteria) || !$this->lastSensorModelDataFileCriteria->equals($criteria)) {
				$this->collSensorModelDataFiles = SensorModelDataFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastSensorModelDataFileCriteria = $criteria;

		return $this->collSensorModelDataFiles;
	}

} // BaseSensorModel
