<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/DAQChannelPeer.php';

/**
 * Base class that represents a row from the 'DAQCHANNEL' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseDAQChannel extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        DAQChannelPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the adcrange field.
	 * @var        double
	 */
	protected $adcrange;


	/**
	 * The value for the adcresolution field.
	 * @var        double
	 */
	protected $adcresolution;


	/**
	 * The value for the channel_order field.
	 * @var        double
	 */
	protected $channel_order;


	/**
	 * The value for the daqconfig_id field.
	 * @var        double
	 */
	protected $daqconfig_id;


	/**
	 * The value for the data_file_id field.
	 * @var        double
	 */
	protected $data_file_id;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the excitation field.
	 * @var        double
	 */
	protected $excitation;


	/**
	 * The value for the gain field.
	 * @var        double
	 */
	protected $gain;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the sensor_id field.
	 * @var        double
	 */
	protected $sensor_id;


	/**
	 * The value for the sensor_location_id field.
	 * @var        double
	 */
	protected $sensor_location_id;

	/**
	 * @var        DAQConfig
	 */
	protected $aDAQConfig;

	/**
	 * @var        DataFile
	 */
	protected $aDataFile;

	/**
	 * @var        Location
	 */
	protected $aLocation;

	/**
	 * @var        Sensor
	 */
	protected $aSensor;

	/**
	 * Collection to store aggregation of collDAQChannelEquipments.
	 * @var        array
	 */
	protected $collDAQChannelEquipments;

	/**
	 * The criteria used to select the current contents of collDAQChannelEquipments.
	 * @var        Criteria
	 */
	protected $lastDAQChannelEquipmentCriteria = null;

	/**
	 * Collection to store aggregation of collDAQChannelOutputs.
	 * @var        array
	 */
	protected $collDAQChannelOutputs;

	/**
	 * The criteria used to select the current contents of collDAQChannelOutputs.
	 * @var        Criteria
	 */
	protected $lastDAQChannelOutputCriteria = null;

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
	 * Get the [adcrange] column value.
	 * 
	 * @return     double
	 */
	public function getADCRange()
	{

		return $this->adcrange;
	}

	/**
	 * Get the [adcresolution] column value.
	 * 
	 * @return     double
	 */
	public function getADCResolution()
	{

		return $this->adcresolution;
	}

	/**
	 * Get the [channel_order] column value.
	 * 
	 * @return     double
	 */
	public function getChannelOrder()
	{

		return $this->channel_order;
	}

	/**
	 * Get the [daqconfig_id] column value.
	 * 
	 * @return     double
	 */
	public function getDAQConfigId()
	{

		return $this->daqconfig_id;
	}

	/**
	 * Get the [data_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getDataFileId()
	{

		return $this->data_file_id;
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
	 * Get the [excitation] column value.
	 * 
	 * @return     double
	 */
	public function getExcitation()
	{

		return $this->excitation;
	}

	/**
	 * Get the [gain] column value.
	 * 
	 * @return     double
	 */
	public function getGain()
	{

		return $this->gain;
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
	 * Get the [sensor_id] column value.
	 * 
	 * @return     double
	 */
	public function getSensorId()
	{

		return $this->sensor_id;
	}

	/**
	 * Get the [sensor_location_id] column value.
	 * 
	 * @return     double
	 */
	public function getSensorLocationId()
	{

		return $this->sensor_location_id;
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
			$this->modifiedColumns[] = DAQChannelPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [adcrange] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setADCRange($v)
	{

		if ($this->adcrange !== $v) {
			$this->adcrange = $v;
			$this->modifiedColumns[] = DAQChannelPeer::ADCRANGE;
		}

	} // setADCRange()

	/**
	 * Set the value of [adcresolution] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setADCResolution($v)
	{

		if ($this->adcresolution !== $v) {
			$this->adcresolution = $v;
			$this->modifiedColumns[] = DAQChannelPeer::ADCRESOLUTION;
		}

	} // setADCResolution()

	/**
	 * Set the value of [channel_order] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setChannelOrder($v)
	{

		if ($this->channel_order !== $v) {
			$this->channel_order = $v;
			$this->modifiedColumns[] = DAQChannelPeer::CHANNEL_ORDER;
		}

	} // setChannelOrder()

	/**
	 * Set the value of [daqconfig_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDAQConfigId($v)
	{

		if ($this->daqconfig_id !== $v) {
			$this->daqconfig_id = $v;
			$this->modifiedColumns[] = DAQChannelPeer::DAQCONFIG_ID;
		}

		if ($this->aDAQConfig !== null && $this->aDAQConfig->getId() !== $v) {
			$this->aDAQConfig = null;
		}

	} // setDAQConfigId()

	/**
	 * Set the value of [data_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDataFileId($v)
	{

		if ($this->data_file_id !== $v) {
			$this->data_file_id = $v;
			$this->modifiedColumns[] = DAQChannelPeer::DATA_FILE_ID;
		}

		if ($this->aDataFile !== null && $this->aDataFile->getId() !== $v) {
			$this->aDataFile = null;
		}

	} // setDataFileId()

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
			$this->modifiedColumns[] = DAQChannelPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [excitation] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setExcitation($v)
	{

		if ($this->excitation !== $v) {
			$this->excitation = $v;
			$this->modifiedColumns[] = DAQChannelPeer::EXCITATION;
		}

	} // setExcitation()

	/**
	 * Set the value of [gain] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setGain($v)
	{

		if ($this->gain !== $v) {
			$this->gain = $v;
			$this->modifiedColumns[] = DAQChannelPeer::GAIN;
		}

	} // setGain()

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
			$this->modifiedColumns[] = DAQChannelPeer::NAME;
		}

	} // setName()

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
			$this->modifiedColumns[] = DAQChannelPeer::SENSOR_ID;
		}

		if ($this->aSensor !== null && $this->aSensor->getId() !== $v) {
			$this->aSensor = null;
		}

	} // setSensorId()

	/**
	 * Set the value of [sensor_location_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSensorLocationId($v)
	{

		if ($this->sensor_location_id !== $v) {
			$this->sensor_location_id = $v;
			$this->modifiedColumns[] = DAQChannelPeer::SENSOR_LOCATION_ID;
		}

		if ($this->aLocation !== null && $this->aLocation->getId() !== $v) {
			$this->aLocation = null;
		}

	} // setSensorLocationId()

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

			$this->adcrange = $rs->getFloat($startcol + 1);

			$this->adcresolution = $rs->getFloat($startcol + 2);

			$this->channel_order = $rs->getFloat($startcol + 3);

			$this->daqconfig_id = $rs->getFloat($startcol + 4);

			$this->data_file_id = $rs->getFloat($startcol + 5);

			$this->description = $rs->getClob($startcol + 6);

			$this->excitation = $rs->getFloat($startcol + 7);

			$this->gain = $rs->getFloat($startcol + 8);

			$this->name = $rs->getString($startcol + 9);

			$this->sensor_id = $rs->getFloat($startcol + 10);

			$this->sensor_location_id = $rs->getFloat($startcol + 11);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 12; // 12 = DAQChannelPeer::NUM_COLUMNS - DAQChannelPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating DAQChannel object", $e);
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
			$con = Propel::getConnection(DAQChannelPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			DAQChannelPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(DAQChannelPeer::DATABASE_NAME);
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

			if ($this->aDAQConfig !== null) {
				if ($this->aDAQConfig->isModified()) {
					$affectedRows += $this->aDAQConfig->save($con);
				}
				$this->setDAQConfig($this->aDAQConfig);
			}

			if ($this->aDataFile !== null) {
				if ($this->aDataFile->isModified()) {
					$affectedRows += $this->aDataFile->save($con);
				}
				$this->setDataFile($this->aDataFile);
			}

			if ($this->aLocation !== null) {
				if ($this->aLocation->isModified()) {
					$affectedRows += $this->aLocation->save($con);
				}
				$this->setLocation($this->aLocation);
			}

			if ($this->aSensor !== null) {
				if ($this->aSensor->isModified()) {
					$affectedRows += $this->aSensor->save($con);
				}
				$this->setSensor($this->aSensor);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = DAQChannelPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += DAQChannelPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collDAQChannelEquipments !== null) {
				foreach($this->collDAQChannelEquipments as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collDAQChannelOutputs !== null) {
				foreach($this->collDAQChannelOutputs as $referrerFK) {
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

			if ($this->aDAQConfig !== null) {
				if (!$this->aDAQConfig->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDAQConfig->getValidationFailures());
				}
			}

			if ($this->aDataFile !== null) {
				if (!$this->aDataFile->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFile->getValidationFailures());
				}
			}

			if ($this->aLocation !== null) {
				if (!$this->aLocation->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aLocation->getValidationFailures());
				}
			}

			if ($this->aSensor !== null) {
				if (!$this->aSensor->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSensor->getValidationFailures());
				}
			}


			if (($retval = DAQChannelPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collDAQChannelEquipments !== null) {
					foreach($this->collDAQChannelEquipments as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collDAQChannelOutputs !== null) {
					foreach($this->collDAQChannelOutputs as $referrerFK) {
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
		$pos = DAQChannelPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getADCRange();
				break;
			case 2:
				return $this->getADCResolution();
				break;
			case 3:
				return $this->getChannelOrder();
				break;
			case 4:
				return $this->getDAQConfigId();
				break;
			case 5:
				return $this->getDataFileId();
				break;
			case 6:
				return $this->getDescription();
				break;
			case 7:
				return $this->getExcitation();
				break;
			case 8:
				return $this->getGain();
				break;
			case 9:
				return $this->getName();
				break;
			case 10:
				return $this->getSensorId();
				break;
			case 11:
				return $this->getSensorLocationId();
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
		$keys = DAQChannelPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getADCRange(),
			$keys[2] => $this->getADCResolution(),
			$keys[3] => $this->getChannelOrder(),
			$keys[4] => $this->getDAQConfigId(),
			$keys[5] => $this->getDataFileId(),
			$keys[6] => $this->getDescription(),
			$keys[7] => $this->getExcitation(),
			$keys[8] => $this->getGain(),
			$keys[9] => $this->getName(),
			$keys[10] => $this->getSensorId(),
			$keys[11] => $this->getSensorLocationId(),
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
		$pos = DAQChannelPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setADCRange($value);
				break;
			case 2:
				$this->setADCResolution($value);
				break;
			case 3:
				$this->setChannelOrder($value);
				break;
			case 4:
				$this->setDAQConfigId($value);
				break;
			case 5:
				$this->setDataFileId($value);
				break;
			case 6:
				$this->setDescription($value);
				break;
			case 7:
				$this->setExcitation($value);
				break;
			case 8:
				$this->setGain($value);
				break;
			case 9:
				$this->setName($value);
				break;
			case 10:
				$this->setSensorId($value);
				break;
			case 11:
				$this->setSensorLocationId($value);
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
		$keys = DAQChannelPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setADCRange($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setADCResolution($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setChannelOrder($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDAQConfigId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDataFileId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDescription($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setExcitation($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setGain($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setName($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setSensorId($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setSensorLocationId($arr[$keys[11]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(DAQChannelPeer::DATABASE_NAME);

		if ($this->isColumnModified(DAQChannelPeer::ID)) $criteria->add(DAQChannelPeer::ID, $this->id);
		if ($this->isColumnModified(DAQChannelPeer::ADCRANGE)) $criteria->add(DAQChannelPeer::ADCRANGE, $this->adcrange);
		if ($this->isColumnModified(DAQChannelPeer::ADCRESOLUTION)) $criteria->add(DAQChannelPeer::ADCRESOLUTION, $this->adcresolution);
		if ($this->isColumnModified(DAQChannelPeer::CHANNEL_ORDER)) $criteria->add(DAQChannelPeer::CHANNEL_ORDER, $this->channel_order);
		if ($this->isColumnModified(DAQChannelPeer::DAQCONFIG_ID)) $criteria->add(DAQChannelPeer::DAQCONFIG_ID, $this->daqconfig_id);
		if ($this->isColumnModified(DAQChannelPeer::DATA_FILE_ID)) $criteria->add(DAQChannelPeer::DATA_FILE_ID, $this->data_file_id);
		if ($this->isColumnModified(DAQChannelPeer::DESCRIPTION)) $criteria->add(DAQChannelPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(DAQChannelPeer::EXCITATION)) $criteria->add(DAQChannelPeer::EXCITATION, $this->excitation);
		if ($this->isColumnModified(DAQChannelPeer::GAIN)) $criteria->add(DAQChannelPeer::GAIN, $this->gain);
		if ($this->isColumnModified(DAQChannelPeer::NAME)) $criteria->add(DAQChannelPeer::NAME, $this->name);
		if ($this->isColumnModified(DAQChannelPeer::SENSOR_ID)) $criteria->add(DAQChannelPeer::SENSOR_ID, $this->sensor_id);
		if ($this->isColumnModified(DAQChannelPeer::SENSOR_LOCATION_ID)) $criteria->add(DAQChannelPeer::SENSOR_LOCATION_ID, $this->sensor_location_id);

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
		$criteria = new Criteria(DAQChannelPeer::DATABASE_NAME);

		$criteria->add(DAQChannelPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of DAQChannel (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setADCRange($this->adcrange);

		$copyObj->setADCResolution($this->adcresolution);

		$copyObj->setChannelOrder($this->channel_order);

		$copyObj->setDAQConfigId($this->daqconfig_id);

		$copyObj->setDataFileId($this->data_file_id);

		$copyObj->setDescription($this->description);

		$copyObj->setExcitation($this->excitation);

		$copyObj->setGain($this->gain);

		$copyObj->setName($this->name);

		$copyObj->setSensorId($this->sensor_id);

		$copyObj->setSensorLocationId($this->sensor_location_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getDAQChannelEquipments() as $relObj) {
				$copyObj->addDAQChannelEquipment($relObj->copy($deepCopy));
			}

			foreach($this->getDAQChannelOutputs() as $relObj) {
				$copyObj->addDAQChannelOutput($relObj->copy($deepCopy));
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
	 * @return     DAQChannel Clone of current object.
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
	 * @return     DAQChannelPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new DAQChannelPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a DAQConfig object.
	 *
	 * @param      DAQConfig $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDAQConfig($v)
	{


		if ($v === null) {
			$this->setDAQConfigId(NULL);
		} else {
			$this->setDAQConfigId($v->getId());
		}


		$this->aDAQConfig = $v;
	}


	/**
	 * Get the associated DAQConfig object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DAQConfig The associated DAQConfig object.
	 * @throws     PropelException
	 */
	public function getDAQConfig($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';

		if ($this->aDAQConfig === null && ($this->daqconfig_id > 0)) {

			$this->aDAQConfig = DAQConfigPeer::retrieveByPK($this->daqconfig_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DAQConfigPeer::retrieveByPK($this->daqconfig_id, $con);
			   $obj->addDAQConfigs($this);
			 */
		}
		return $this->aDAQConfig;
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
			$this->setDataFileId(NULL);
		} else {
			$this->setDataFileId($v->getId());
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

		if ($this->aDataFile === null && ($this->data_file_id > 0)) {

			$this->aDataFile = DataFilePeer::retrieveByPK($this->data_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->data_file_id, $con);
			   $obj->addDataFiles($this);
			 */
		}
		return $this->aDataFile;
	}

	/**
	 * Declares an association between this object and a Location object.
	 *
	 * @param      Location $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setLocation($v)
	{


		if ($v === null) {
			$this->setSensorLocationId(NULL);
		} else {
			$this->setSensorLocationId($v->getId());
		}


		$this->aLocation = $v;
	}


	/**
	 * Get the associated Location object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Location The associated Location object.
	 * @throws     PropelException
	 */
	public function getLocation($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';

		if ($this->aLocation === null && ($this->sensor_location_id > 0)) {

			$this->aLocation = LocationPeer::retrieveByPK($this->sensor_location_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = LocationPeer::retrieveByPK($this->sensor_location_id, $con);
			   $obj->addLocations($this);
			 */
		}
		return $this->aLocation;
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

	/**
	 * Temporary storage of collDAQChannelEquipments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDAQChannelEquipments()
	{
		if ($this->collDAQChannelEquipments === null) {
			$this->collDAQChannelEquipments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DAQChannel has previously
	 * been saved, it will retrieve related DAQChannelEquipments from storage.
	 * If this DAQChannel is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDAQChannelEquipments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannelEquipments === null) {
			if ($this->isNew()) {
			   $this->collDAQChannelEquipments = array();
			} else {

				$criteria->add(DAQChannelEquipmentPeer::DAQCHANNEL_ID, $this->getId());

				DAQChannelEquipmentPeer::addSelectColumns($criteria);
				$this->collDAQChannelEquipments = DAQChannelEquipmentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DAQChannelEquipmentPeer::DAQCHANNEL_ID, $this->getId());

				DAQChannelEquipmentPeer::addSelectColumns($criteria);
				if (!isset($this->lastDAQChannelEquipmentCriteria) || !$this->lastDAQChannelEquipmentCriteria->equals($criteria)) {
					$this->collDAQChannelEquipments = DAQChannelEquipmentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDAQChannelEquipmentCriteria = $criteria;
		return $this->collDAQChannelEquipments;
	}

	/**
	 * Returns the number of related DAQChannelEquipments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDAQChannelEquipments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DAQChannelEquipmentPeer::DAQCHANNEL_ID, $this->getId());

		return DAQChannelEquipmentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DAQChannelEquipment object to this object
	 * through the DAQChannelEquipment foreign key attribute
	 *
	 * @param      DAQChannelEquipment $l DAQChannelEquipment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDAQChannelEquipment(DAQChannelEquipment $l)
	{
		$this->collDAQChannelEquipments[] = $l;
		$l->setDAQChannel($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DAQChannel is new, it will return
	 * an empty collection; or if this DAQChannel has previously
	 * been saved, it will retrieve related DAQChannelEquipments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DAQChannel.
	 */
	public function getDAQChannelEquipmentsJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannelEquipments === null) {
			if ($this->isNew()) {
				$this->collDAQChannelEquipments = array();
			} else {

				$criteria->add(DAQChannelEquipmentPeer::DAQCHANNEL_ID, $this->getId());

				$this->collDAQChannelEquipments = DAQChannelEquipmentPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQChannelEquipmentPeer::DAQCHANNEL_ID, $this->getId());

			if (!isset($this->lastDAQChannelEquipmentCriteria) || !$this->lastDAQChannelEquipmentCriteria->equals($criteria)) {
				$this->collDAQChannelEquipments = DAQChannelEquipmentPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastDAQChannelEquipmentCriteria = $criteria;

		return $this->collDAQChannelEquipments;
	}

	/**
	 * Temporary storage of collDAQChannelOutputs to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDAQChannelOutputs()
	{
		if ($this->collDAQChannelOutputs === null) {
			$this->collDAQChannelOutputs = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DAQChannel has previously
	 * been saved, it will retrieve related DAQChannelOutputs from storage.
	 * If this DAQChannel is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDAQChannelOutputs($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelOutputPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannelOutputs === null) {
			if ($this->isNew()) {
			   $this->collDAQChannelOutputs = array();
			} else {

				$criteria->add(DAQChannelOutputPeer::DAQCHANNEL_ID, $this->getId());

				DAQChannelOutputPeer::addSelectColumns($criteria);
				$this->collDAQChannelOutputs = DAQChannelOutputPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DAQChannelOutputPeer::DAQCHANNEL_ID, $this->getId());

				DAQChannelOutputPeer::addSelectColumns($criteria);
				if (!isset($this->lastDAQChannelOutputCriteria) || !$this->lastDAQChannelOutputCriteria->equals($criteria)) {
					$this->collDAQChannelOutputs = DAQChannelOutputPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDAQChannelOutputCriteria = $criteria;
		return $this->collDAQChannelOutputs;
	}

	/**
	 * Returns the number of related DAQChannelOutputs.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDAQChannelOutputs($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelOutputPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DAQChannelOutputPeer::DAQCHANNEL_ID, $this->getId());

		return DAQChannelOutputPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DAQChannelOutput object to this object
	 * through the DAQChannelOutput foreign key attribute
	 *
	 * @param      DAQChannelOutput $l DAQChannelOutput
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDAQChannelOutput(DAQChannelOutput $l)
	{
		$this->collDAQChannelOutputs[] = $l;
		$l->setDAQChannel($this);
	}

} // BaseDAQChannel
