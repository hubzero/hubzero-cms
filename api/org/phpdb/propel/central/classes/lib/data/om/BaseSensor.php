<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SensorPeer.php';

/**
 * Base class that represents a row from the 'SENSOR' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSensor extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SensorPeer
	 */
	protected static $peer;


	/**
	 * The value for the sensor_id field.
	 * @var        double
	 */
	protected $sensor_id;


	/**
	 * The value for the commission_date field.
	 * @var        int
	 */
	protected $commission_date;


	/**
	 * The value for the decommission_date field.
	 * @var        int
	 */
	protected $decommission_date;


	/**
	 * The value for the deleted field.
	 * @var        double
	 */
	protected $deleted;


	/**
	 * The value for the local_id field.
	 * @var        string
	 */
	protected $local_id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the sensor_model_id field.
	 * @var        double
	 */
	protected $sensor_model_id;


	/**
	 * The value for the serial_number field.
	 * @var        string
	 */
	protected $serial_number;


	/**
	 * The value for the supplier field.
	 * @var        string
	 */
	protected $supplier;

	/**
	 * @var        SensorModel
	 */
	protected $aSensorModel;

	/**
	 * Collection to store aggregation of collCalibrations.
	 * @var        array
	 */
	protected $collCalibrations;

	/**
	 * The criteria used to select the current contents of collCalibrations.
	 * @var        Criteria
	 */
	protected $lastCalibrationCriteria = null;

	/**
	 * Collection to store aggregation of collDAQChannels.
	 * @var        array
	 */
	protected $collDAQChannels;

	/**
	 * The criteria used to select the current contents of collDAQChannels.
	 * @var        Criteria
	 */
	protected $lastDAQChannelCriteria = null;

	/**
	 * Collection to store aggregation of collSensorAttributes.
	 * @var        array
	 */
	protected $collSensorAttributes;

	/**
	 * The criteria used to select the current contents of collSensorAttributes.
	 * @var        Criteria
	 */
	protected $lastSensorAttributeCriteria = null;

	/**
	 * Collection to store aggregation of collSensorSensorManifests.
	 * @var        array
	 */
	protected $collSensorSensorManifests;

	/**
	 * The criteria used to select the current contents of collSensorSensorManifests.
	 * @var        Criteria
	 */
	protected $lastSensorSensorManifestCriteria = null;

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
	 * Get the [sensor_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->sensor_id;
	}

	/**
	 * Get the [optionally formatted] [commission_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getCommissionDate($format = '%Y-%m-%d')
	{

		if ($this->commission_date === null || $this->commission_date === '') {
			return null;
		} elseif (!is_int($this->commission_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->commission_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [commission_date] as date/time value: " . var_export($this->commission_date, true));
			}
		} else {
			$ts = $this->commission_date;
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
	 * Get the [optionally formatted] [decommission_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getDecommissionDate($format = '%Y-%m-%d')
	{

		if ($this->decommission_date === null || $this->decommission_date === '') {
			return null;
		} elseif (!is_int($this->decommission_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->decommission_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [decommission_date] as date/time value: " . var_export($this->decommission_date, true));
			}
		} else {
			$ts = $this->decommission_date;
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
	 * Get the [deleted] column value.
	 * 
	 * @return     double
	 */
	public function getDeleted()
	{

		return $this->deleted;
	}

	/**
	 * Get the [local_id] column value.
	 * 
	 * @return     string
	 */
	public function getLocalId()
	{

		return $this->local_id;
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
	 * Get the [sensor_model_id] column value.
	 * 
	 * @return     double
	 */
	public function getSensorModelId()
	{

		return $this->sensor_model_id;
	}

	/**
	 * Get the [serial_number] column value.
	 * 
	 * @return     string
	 */
	public function getSerialNumber()
	{

		return $this->serial_number;
	}

	/**
	 * Get the [supplier] column value.
	 * 
	 * @return     string
	 */
	public function getSupplier()
	{

		return $this->supplier;
	}

	/**
	 * Set the value of [sensor_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->sensor_id !== $v) {
			$this->sensor_id = $v;
			$this->modifiedColumns[] = SensorPeer::SENSOR_ID;
		}

	} // setId()

	/**
	 * Set the value of [commission_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setCommissionDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [commission_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->commission_date !== $ts) {
			$this->commission_date = $ts;
			$this->modifiedColumns[] = SensorPeer::COMMISSION_DATE;
		}

	} // setCommissionDate()

	/**
	 * Set the value of [decommission_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setDecommissionDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [decommission_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->decommission_date !== $ts) {
			$this->decommission_date = $ts;
			$this->modifiedColumns[] = SensorPeer::DECOMMISSION_DATE;
		}

	} // setDecommissionDate()

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
			$this->modifiedColumns[] = SensorPeer::DELETED;
		}

	} // setDeleted()

	/**
	 * Set the value of [local_id] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setLocalId($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->local_id !== $v) {
			$this->local_id = $v;
			$this->modifiedColumns[] = SensorPeer::LOCAL_ID;
		}

	} // setLocalId()

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
			$this->modifiedColumns[] = SensorPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [sensor_model_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSensorModelId($v)
	{

		if ($this->sensor_model_id !== $v) {
			$this->sensor_model_id = $v;
			$this->modifiedColumns[] = SensorPeer::SENSOR_MODEL_ID;
		}

		if ($this->aSensorModel !== null && $this->aSensorModel->getId() !== $v) {
			$this->aSensorModel = null;
		}

	} // setSensorModelId()

	/**
	 * Set the value of [serial_number] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSerialNumber($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->serial_number !== $v) {
			$this->serial_number = $v;
			$this->modifiedColumns[] = SensorPeer::SERIAL_NUMBER;
		}

	} // setSerialNumber()

	/**
	 * Set the value of [supplier] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSupplier($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->supplier !== $v) {
			$this->supplier = $v;
			$this->modifiedColumns[] = SensorPeer::SUPPLIER;
		}

	} // setSupplier()

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

			$this->sensor_id = $rs->getFloat($startcol + 0);

			$this->commission_date = $rs->getDate($startcol + 1, null);

			$this->decommission_date = $rs->getDate($startcol + 2, null);

			$this->deleted = $rs->getFloat($startcol + 3);

			$this->local_id = $rs->getString($startcol + 4);

			$this->name = $rs->getString($startcol + 5);

			$this->sensor_model_id = $rs->getFloat($startcol + 6);

			$this->serial_number = $rs->getString($startcol + 7);

			$this->supplier = $rs->getString($startcol + 8);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = SensorPeer::NUM_COLUMNS - SensorPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Sensor object", $e);
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
			$con = Propel::getConnection(SensorPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SensorPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SensorPeer::DATABASE_NAME);
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

			if ($this->aSensorModel !== null) {
				if ($this->aSensorModel->isModified()) {
					$affectedRows += $this->aSensorModel->save($con);
				}
				$this->setSensorModel($this->aSensorModel);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SensorPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SensorPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCalibrations !== null) {
				foreach($this->collCalibrations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collDAQChannels !== null) {
				foreach($this->collDAQChannels as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorAttributes !== null) {
				foreach($this->collSensorAttributes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorSensorManifests !== null) {
				foreach($this->collSensorSensorManifests as $referrerFK) {
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

			if ($this->aSensorModel !== null) {
				if (!$this->aSensorModel->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSensorModel->getValidationFailures());
				}
			}


			if (($retval = SensorPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCalibrations !== null) {
					foreach($this->collCalibrations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collDAQChannels !== null) {
					foreach($this->collDAQChannels as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorAttributes !== null) {
					foreach($this->collSensorAttributes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorSensorManifests !== null) {
					foreach($this->collSensorSensorManifests as $referrerFK) {
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
		$pos = SensorPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCommissionDate();
				break;
			case 2:
				return $this->getDecommissionDate();
				break;
			case 3:
				return $this->getDeleted();
				break;
			case 4:
				return $this->getLocalId();
				break;
			case 5:
				return $this->getName();
				break;
			case 6:
				return $this->getSensorModelId();
				break;
			case 7:
				return $this->getSerialNumber();
				break;
			case 8:
				return $this->getSupplier();
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
		$keys = SensorPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCommissionDate(),
			$keys[2] => $this->getDecommissionDate(),
			$keys[3] => $this->getDeleted(),
			$keys[4] => $this->getLocalId(),
			$keys[5] => $this->getName(),
			$keys[6] => $this->getSensorModelId(),
			$keys[7] => $this->getSerialNumber(),
			$keys[8] => $this->getSupplier(),
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
		$pos = SensorPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCommissionDate($value);
				break;
			case 2:
				$this->setDecommissionDate($value);
				break;
			case 3:
				$this->setDeleted($value);
				break;
			case 4:
				$this->setLocalId($value);
				break;
			case 5:
				$this->setName($value);
				break;
			case 6:
				$this->setSensorModelId($value);
				break;
			case 7:
				$this->setSerialNumber($value);
				break;
			case 8:
				$this->setSupplier($value);
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
		$keys = SensorPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCommissionDate($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDecommissionDate($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDeleted($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setLocalId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setName($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setSensorModelId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setSerialNumber($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setSupplier($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SensorPeer::DATABASE_NAME);

		if ($this->isColumnModified(SensorPeer::SENSOR_ID)) $criteria->add(SensorPeer::SENSOR_ID, $this->sensor_id);
		if ($this->isColumnModified(SensorPeer::COMMISSION_DATE)) $criteria->add(SensorPeer::COMMISSION_DATE, $this->commission_date);
		if ($this->isColumnModified(SensorPeer::DECOMMISSION_DATE)) $criteria->add(SensorPeer::DECOMMISSION_DATE, $this->decommission_date);
		if ($this->isColumnModified(SensorPeer::DELETED)) $criteria->add(SensorPeer::DELETED, $this->deleted);
		if ($this->isColumnModified(SensorPeer::LOCAL_ID)) $criteria->add(SensorPeer::LOCAL_ID, $this->local_id);
		if ($this->isColumnModified(SensorPeer::NAME)) $criteria->add(SensorPeer::NAME, $this->name);
		if ($this->isColumnModified(SensorPeer::SENSOR_MODEL_ID)) $criteria->add(SensorPeer::SENSOR_MODEL_ID, $this->sensor_model_id);
		if ($this->isColumnModified(SensorPeer::SERIAL_NUMBER)) $criteria->add(SensorPeer::SERIAL_NUMBER, $this->serial_number);
		if ($this->isColumnModified(SensorPeer::SUPPLIER)) $criteria->add(SensorPeer::SUPPLIER, $this->supplier);

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
		$criteria = new Criteria(SensorPeer::DATABASE_NAME);

		$criteria->add(SensorPeer::SENSOR_ID, $this->sensor_id);

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
	 * Generic method to set the primary key (sensor_id column).
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
	 * @param      object $copyObj An object of Sensor (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCommissionDate($this->commission_date);

		$copyObj->setDecommissionDate($this->decommission_date);

		$copyObj->setDeleted($this->deleted);

		$copyObj->setLocalId($this->local_id);

		$copyObj->setName($this->name);

		$copyObj->setSensorModelId($this->sensor_model_id);

		$copyObj->setSerialNumber($this->serial_number);

		$copyObj->setSupplier($this->supplier);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getCalibrations() as $relObj) {
				$copyObj->addCalibration($relObj->copy($deepCopy));
			}

			foreach($this->getDAQChannels() as $relObj) {
				$copyObj->addDAQChannel($relObj->copy($deepCopy));
			}

			foreach($this->getSensorAttributes() as $relObj) {
				$copyObj->addSensorAttribute($relObj->copy($deepCopy));
			}

			foreach($this->getSensorSensorManifests() as $relObj) {
				$copyObj->addSensorSensorManifest($relObj->copy($deepCopy));
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
	 * @return     Sensor Clone of current object.
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
	 * @return     SensorPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SensorPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a SensorModel object.
	 *
	 * @param      SensorModel $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSensorModel($v)
	{


		if ($v === null) {
			$this->setSensorModelId(NULL);
		} else {
			$this->setSensorModelId($v->getId());
		}


		$this->aSensorModel = $v;
	}


	/**
	 * Get the associated SensorModel object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SensorModel The associated SensorModel object.
	 * @throws     PropelException
	 */
	public function getSensorModel($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';

		if ($this->aSensorModel === null && ($this->sensor_model_id > 0)) {

			$this->aSensorModel = SensorModelPeer::retrieveByPK($this->sensor_model_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SensorModelPeer::retrieveByPK($this->sensor_model_id, $con);
			   $obj->addSensorModels($this);
			 */
		}
		return $this->aSensorModel;
	}

	/**
	 * Temporary storage of collCalibrations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCalibrations()
	{
		if ($this->collCalibrations === null) {
			$this->collCalibrations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Sensor has previously
	 * been saved, it will retrieve related Calibrations from storage.
	 * If this Sensor is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCalibrations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCalibrationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCalibrations === null) {
			if ($this->isNew()) {
			   $this->collCalibrations = array();
			} else {

				$criteria->add(CalibrationPeer::SENSOR_ID, $this->getId());

				CalibrationPeer::addSelectColumns($criteria);
				$this->collCalibrations = CalibrationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CalibrationPeer::SENSOR_ID, $this->getId());

				CalibrationPeer::addSelectColumns($criteria);
				if (!isset($this->lastCalibrationCriteria) || !$this->lastCalibrationCriteria->equals($criteria)) {
					$this->collCalibrations = CalibrationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCalibrationCriteria = $criteria;
		return $this->collCalibrations;
	}

	/**
	 * Returns the number of related Calibrations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCalibrations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCalibrationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(CalibrationPeer::SENSOR_ID, $this->getId());

		return CalibrationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Calibration object to this object
	 * through the Calibration foreign key attribute
	 *
	 * @param      Calibration $l Calibration
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCalibration(Calibration $l)
	{
		$this->collCalibrations[] = $l;
		$l->setSensor($this);
	}

	/**
	 * Temporary storage of collDAQChannels to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDAQChannels()
	{
		if ($this->collDAQChannels === null) {
			$this->collDAQChannels = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Sensor has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 * If this Sensor is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDAQChannels($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
			   $this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::SENSOR_ID, $this->getId());

				DAQChannelPeer::addSelectColumns($criteria);
				$this->collDAQChannels = DAQChannelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DAQChannelPeer::SENSOR_ID, $this->getId());

				DAQChannelPeer::addSelectColumns($criteria);
				if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
					$this->collDAQChannels = DAQChannelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDAQChannelCriteria = $criteria;
		return $this->collDAQChannels;
	}

	/**
	 * Returns the number of related DAQChannels.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDAQChannels($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DAQChannelPeer::SENSOR_ID, $this->getId());

		return DAQChannelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DAQChannel object to this object
	 * through the DAQChannel foreign key attribute
	 *
	 * @param      DAQChannel $l DAQChannel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDAQChannel(DAQChannel $l)
	{
		$this->collDAQChannels[] = $l;
		$l->setSensor($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Sensor is new, it will return
	 * an empty collection; or if this Sensor has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Sensor.
	 */
	public function getDAQChannelsJoinDAQConfig($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
				$this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::SENSOR_ID, $this->getId());

				$this->collDAQChannels = DAQChannelPeer::doSelectJoinDAQConfig($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQChannelPeer::SENSOR_ID, $this->getId());

			if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
				$this->collDAQChannels = DAQChannelPeer::doSelectJoinDAQConfig($criteria, $con);
			}
		}
		$this->lastDAQChannelCriteria = $criteria;

		return $this->collDAQChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Sensor is new, it will return
	 * an empty collection; or if this Sensor has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Sensor.
	 */
	public function getDAQChannelsJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
				$this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::SENSOR_ID, $this->getId());

				$this->collDAQChannels = DAQChannelPeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQChannelPeer::SENSOR_ID, $this->getId());

			if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
				$this->collDAQChannels = DAQChannelPeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastDAQChannelCriteria = $criteria;

		return $this->collDAQChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Sensor is new, it will return
	 * an empty collection; or if this Sensor has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Sensor.
	 */
	public function getDAQChannelsJoinLocation($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
				$this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::SENSOR_ID, $this->getId());

				$this->collDAQChannels = DAQChannelPeer::doSelectJoinLocation($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQChannelPeer::SENSOR_ID, $this->getId());

			if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
				$this->collDAQChannels = DAQChannelPeer::doSelectJoinLocation($criteria, $con);
			}
		}
		$this->lastDAQChannelCriteria = $criteria;

		return $this->collDAQChannels;
	}

	/**
	 * Temporary storage of collSensorAttributes to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorAttributes()
	{
		if ($this->collSensorAttributes === null) {
			$this->collSensorAttributes = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Sensor has previously
	 * been saved, it will retrieve related SensorAttributes from storage.
	 * If this Sensor is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorAttributes($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorAttributes === null) {
			if ($this->isNew()) {
			   $this->collSensorAttributes = array();
			} else {

				$criteria->add(SensorAttributePeer::SENSOR_ID, $this->getId());

				SensorAttributePeer::addSelectColumns($criteria);
				$this->collSensorAttributes = SensorAttributePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorAttributePeer::SENSOR_ID, $this->getId());

				SensorAttributePeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorAttributeCriteria) || !$this->lastSensorAttributeCriteria->equals($criteria)) {
					$this->collSensorAttributes = SensorAttributePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorAttributeCriteria = $criteria;
		return $this->collSensorAttributes;
	}

	/**
	 * Returns the number of related SensorAttributes.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorAttributes($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorAttributePeer::SENSOR_ID, $this->getId());

		return SensorAttributePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorAttribute object to this object
	 * through the SensorAttribute foreign key attribute
	 *
	 * @param      SensorAttribute $l SensorAttribute
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorAttribute(SensorAttribute $l)
	{
		$this->collSensorAttributes[] = $l;
		$l->setSensor($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Sensor is new, it will return
	 * an empty collection; or if this Sensor has previously
	 * been saved, it will retrieve related SensorAttributes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Sensor.
	 */
	public function getSensorAttributesJoinAttribute($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorAttributes === null) {
			if ($this->isNew()) {
				$this->collSensorAttributes = array();
			} else {

				$criteria->add(SensorAttributePeer::SENSOR_ID, $this->getId());

				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinAttribute($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorAttributePeer::SENSOR_ID, $this->getId());

			if (!isset($this->lastSensorAttributeCriteria) || !$this->lastSensorAttributeCriteria->equals($criteria)) {
				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinAttribute($criteria, $con);
			}
		}
		$this->lastSensorAttributeCriteria = $criteria;

		return $this->collSensorAttributes;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Sensor is new, it will return
	 * an empty collection; or if this Sensor has previously
	 * been saved, it will retrieve related SensorAttributes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Sensor.
	 */
	public function getSensorAttributesJoinUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorAttributes === null) {
			if ($this->isNew()) {
				$this->collSensorAttributes = array();
			} else {

				$criteria->add(SensorAttributePeer::SENSOR_ID, $this->getId());

				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorAttributePeer::SENSOR_ID, $this->getId());

			if (!isset($this->lastSensorAttributeCriteria) || !$this->lastSensorAttributeCriteria->equals($criteria)) {
				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinUnit($criteria, $con);
			}
		}
		$this->lastSensorAttributeCriteria = $criteria;

		return $this->collSensorAttributes;
	}

	/**
	 * Temporary storage of collSensorSensorManifests to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorSensorManifests()
	{
		if ($this->collSensorSensorManifests === null) {
			$this->collSensorSensorManifests = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Sensor has previously
	 * been saved, it will retrieve related SensorSensorManifests from storage.
	 * If this Sensor is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorSensorManifests($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorSensorManifestPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorSensorManifests === null) {
			if ($this->isNew()) {
			   $this->collSensorSensorManifests = array();
			} else {

				$criteria->add(SensorSensorManifestPeer::SENSOR_ID, $this->getId());

				SensorSensorManifestPeer::addSelectColumns($criteria);
				$this->collSensorSensorManifests = SensorSensorManifestPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorSensorManifestPeer::SENSOR_ID, $this->getId());

				SensorSensorManifestPeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorSensorManifestCriteria) || !$this->lastSensorSensorManifestCriteria->equals($criteria)) {
					$this->collSensorSensorManifests = SensorSensorManifestPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorSensorManifestCriteria = $criteria;
		return $this->collSensorSensorManifests;
	}

	/**
	 * Returns the number of related SensorSensorManifests.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorSensorManifests($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorSensorManifestPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorSensorManifestPeer::SENSOR_ID, $this->getId());

		return SensorSensorManifestPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorSensorManifest object to this object
	 * through the SensorSensorManifest foreign key attribute
	 *
	 * @param      SensorSensorManifest $l SensorSensorManifest
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorSensorManifest(SensorSensorManifest $l)
	{
		$this->collSensorSensorManifests[] = $l;
		$l->setSensor($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Sensor is new, it will return
	 * an empty collection; or if this Sensor has previously
	 * been saved, it will retrieve related SensorSensorManifests from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Sensor.
	 */
	public function getSensorSensorManifestsJoinSensorManifest($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorSensorManifestPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorSensorManifests === null) {
			if ($this->isNew()) {
				$this->collSensorSensorManifests = array();
			} else {

				$criteria->add(SensorSensorManifestPeer::SENSOR_ID, $this->getId());

				$this->collSensorSensorManifests = SensorSensorManifestPeer::doSelectJoinSensorManifest($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorSensorManifestPeer::SENSOR_ID, $this->getId());

			if (!isset($this->lastSensorSensorManifestCriteria) || !$this->lastSensorSensorManifestCriteria->equals($criteria)) {
				$this->collSensorSensorManifests = SensorSensorManifestPeer::doSelectJoinSensorManifest($criteria, $con);
			}
		}
		$this->lastSensorSensorManifestCriteria = $criteria;

		return $this->collSensorSensorManifests;
	}

} // BaseSensor
