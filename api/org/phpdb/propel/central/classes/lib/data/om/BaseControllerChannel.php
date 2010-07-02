<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/ControllerChannelPeer.php';

/**
 * Base class that represents a row from the 'CONTROLLER_CHANNEL' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseControllerChannel extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ControllerChannelPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the controller_config_id field.
	 * @var        double
	 */
	protected $controller_config_id;


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
	 * The value for the direction field.
	 * @var        string
	 */
	protected $direction;


	/**
	 * The value for the equipment_id field.
	 * @var        double
	 */
	protected $equipment_id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the source_location_id field.
	 * @var        double
	 */
	protected $source_location_id;


	/**
	 * The value for the station field.
	 * @var        string
	 */
	protected $station;

	/**
	 * @var        ControllerConfig
	 */
	protected $aControllerConfig;

	/**
	 * @var        DataFile
	 */
	protected $aDataFile;

	/**
	 * @var        Equipment
	 */
	protected $aEquipment;

	/**
	 * @var        Location
	 */
	protected $aLocation;

	/**
	 * Collection to store aggregation of collControllerChannelEquipments.
	 * @var        array
	 */
	protected $collControllerChannelEquipments;

	/**
	 * The criteria used to select the current contents of collControllerChannelEquipments.
	 * @var        Criteria
	 */
	protected $lastControllerChannelEquipmentCriteria = null;

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
	 * Get the [controller_config_id] column value.
	 * 
	 * @return     double
	 */
	public function getControllerConfigId()
	{

		return $this->controller_config_id;
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
	 * Get the [direction] column value.
	 * 
	 * @return     string
	 */
	public function getDirection()
	{

		return $this->direction;
	}

	/**
	 * Get the [equipment_id] column value.
	 * 
	 * @return     double
	 */
	public function getEquipmentId()
	{

		return $this->equipment_id;
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
	 * Get the [source_location_id] column value.
	 * 
	 * @return     double
	 */
	public function getSourceLocationId()
	{

		return $this->source_location_id;
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
	 * Set the value of [id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = ControllerChannelPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [controller_config_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setControllerConfigId($v)
	{

		if ($this->controller_config_id !== $v) {
			$this->controller_config_id = $v;
			$this->modifiedColumns[] = ControllerChannelPeer::CONTROLLER_CONFIG_ID;
		}

		if ($this->aControllerConfig !== null && $this->aControllerConfig->getId() !== $v) {
			$this->aControllerConfig = null;
		}

	} // setControllerConfigId()

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
			$this->modifiedColumns[] = ControllerChannelPeer::DATA_FILE_ID;
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
			$this->modifiedColumns[] = ControllerChannelPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [direction] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDirection($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->direction !== $v) {
			$this->direction = $v;
			$this->modifiedColumns[] = ControllerChannelPeer::DIRECTION;
		}

	} // setDirection()

	/**
	 * Set the value of [equipment_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEquipmentId($v)
	{

		if ($this->equipment_id !== $v) {
			$this->equipment_id = $v;
			$this->modifiedColumns[] = ControllerChannelPeer::EQUIPMENT_ID;
		}

		if ($this->aEquipment !== null && $this->aEquipment->getId() !== $v) {
			$this->aEquipment = null;
		}

	} // setEquipmentId()

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
			$this->modifiedColumns[] = ControllerChannelPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [source_location_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSourceLocationId($v)
	{

		if ($this->source_location_id !== $v) {
			$this->source_location_id = $v;
			$this->modifiedColumns[] = ControllerChannelPeer::SOURCE_LOCATION_ID;
		}

		if ($this->aLocation !== null && $this->aLocation->getId() !== $v) {
			$this->aLocation = null;
		}

	} // setSourceLocationId()

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
			$this->modifiedColumns[] = ControllerChannelPeer::STATION;
		}

	} // setStation()

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

			$this->controller_config_id = $rs->getFloat($startcol + 1);

			$this->data_file_id = $rs->getFloat($startcol + 2);

			$this->description = $rs->getClob($startcol + 3);

			$this->direction = $rs->getString($startcol + 4);

			$this->equipment_id = $rs->getFloat($startcol + 5);

			$this->name = $rs->getString($startcol + 6);

			$this->source_location_id = $rs->getFloat($startcol + 7);

			$this->station = $rs->getString($startcol + 8);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = ControllerChannelPeer::NUM_COLUMNS - ControllerChannelPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating ControllerChannel object", $e);
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
			$con = Propel::getConnection(ControllerChannelPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			ControllerChannelPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ControllerChannelPeer::DATABASE_NAME);
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

			if ($this->aControllerConfig !== null) {
				if ($this->aControllerConfig->isModified()) {
					$affectedRows += $this->aControllerConfig->save($con);
				}
				$this->setControllerConfig($this->aControllerConfig);
			}

			if ($this->aDataFile !== null) {
				if ($this->aDataFile->isModified()) {
					$affectedRows += $this->aDataFile->save($con);
				}
				$this->setDataFile($this->aDataFile);
			}

			if ($this->aEquipment !== null) {
				if ($this->aEquipment->isModified()) {
					$affectedRows += $this->aEquipment->save($con);
				}
				$this->setEquipment($this->aEquipment);
			}

			if ($this->aLocation !== null) {
				if ($this->aLocation->isModified()) {
					$affectedRows += $this->aLocation->save($con);
				}
				$this->setLocation($this->aLocation);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = ControllerChannelPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += ControllerChannelPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collControllerChannelEquipments !== null) {
				foreach($this->collControllerChannelEquipments as $referrerFK) {
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

			if ($this->aControllerConfig !== null) {
				if (!$this->aControllerConfig->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aControllerConfig->getValidationFailures());
				}
			}

			if ($this->aDataFile !== null) {
				if (!$this->aDataFile->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFile->getValidationFailures());
				}
			}

			if ($this->aEquipment !== null) {
				if (!$this->aEquipment->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEquipment->getValidationFailures());
				}
			}

			if ($this->aLocation !== null) {
				if (!$this->aLocation->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aLocation->getValidationFailures());
				}
			}


			if (($retval = ControllerChannelPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collControllerChannelEquipments !== null) {
					foreach($this->collControllerChannelEquipments as $referrerFK) {
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
		$pos = ControllerChannelPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getControllerConfigId();
				break;
			case 2:
				return $this->getDataFileId();
				break;
			case 3:
				return $this->getDescription();
				break;
			case 4:
				return $this->getDirection();
				break;
			case 5:
				return $this->getEquipmentId();
				break;
			case 6:
				return $this->getName();
				break;
			case 7:
				return $this->getSourceLocationId();
				break;
			case 8:
				return $this->getStation();
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
		$keys = ControllerChannelPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getControllerConfigId(),
			$keys[2] => $this->getDataFileId(),
			$keys[3] => $this->getDescription(),
			$keys[4] => $this->getDirection(),
			$keys[5] => $this->getEquipmentId(),
			$keys[6] => $this->getName(),
			$keys[7] => $this->getSourceLocationId(),
			$keys[8] => $this->getStation(),
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
		$pos = ControllerChannelPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setControllerConfigId($value);
				break;
			case 2:
				$this->setDataFileId($value);
				break;
			case 3:
				$this->setDescription($value);
				break;
			case 4:
				$this->setDirection($value);
				break;
			case 5:
				$this->setEquipmentId($value);
				break;
			case 6:
				$this->setName($value);
				break;
			case 7:
				$this->setSourceLocationId($value);
				break;
			case 8:
				$this->setStation($value);
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
		$keys = ControllerChannelPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setControllerConfigId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDataFileId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDescription($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDirection($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setEquipmentId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setName($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setSourceLocationId($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setStation($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ControllerChannelPeer::DATABASE_NAME);

		if ($this->isColumnModified(ControllerChannelPeer::ID)) $criteria->add(ControllerChannelPeer::ID, $this->id);
		if ($this->isColumnModified(ControllerChannelPeer::CONTROLLER_CONFIG_ID)) $criteria->add(ControllerChannelPeer::CONTROLLER_CONFIG_ID, $this->controller_config_id);
		if ($this->isColumnModified(ControllerChannelPeer::DATA_FILE_ID)) $criteria->add(ControllerChannelPeer::DATA_FILE_ID, $this->data_file_id);
		if ($this->isColumnModified(ControllerChannelPeer::DESCRIPTION)) $criteria->add(ControllerChannelPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(ControllerChannelPeer::DIRECTION)) $criteria->add(ControllerChannelPeer::DIRECTION, $this->direction);
		if ($this->isColumnModified(ControllerChannelPeer::EQUIPMENT_ID)) $criteria->add(ControllerChannelPeer::EQUIPMENT_ID, $this->equipment_id);
		if ($this->isColumnModified(ControllerChannelPeer::NAME)) $criteria->add(ControllerChannelPeer::NAME, $this->name);
		if ($this->isColumnModified(ControllerChannelPeer::SOURCE_LOCATION_ID)) $criteria->add(ControllerChannelPeer::SOURCE_LOCATION_ID, $this->source_location_id);
		if ($this->isColumnModified(ControllerChannelPeer::STATION)) $criteria->add(ControllerChannelPeer::STATION, $this->station);

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
		$criteria = new Criteria(ControllerChannelPeer::DATABASE_NAME);

		$criteria->add(ControllerChannelPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of ControllerChannel (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setControllerConfigId($this->controller_config_id);

		$copyObj->setDataFileId($this->data_file_id);

		$copyObj->setDescription($this->description);

		$copyObj->setDirection($this->direction);

		$copyObj->setEquipmentId($this->equipment_id);

		$copyObj->setName($this->name);

		$copyObj->setSourceLocationId($this->source_location_id);

		$copyObj->setStation($this->station);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getControllerChannelEquipments() as $relObj) {
				$copyObj->addControllerChannelEquipment($relObj->copy($deepCopy));
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
	 * @return     ControllerChannel Clone of current object.
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
	 * @return     ControllerChannelPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ControllerChannelPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a ControllerConfig object.
	 *
	 * @param      ControllerConfig $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setControllerConfig($v)
	{


		if ($v === null) {
			$this->setControllerConfigId(NULL);
		} else {
			$this->setControllerConfigId($v->getId());
		}


		$this->aControllerConfig = $v;
	}


	/**
	 * Get the associated ControllerConfig object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     ControllerConfig The associated ControllerConfig object.
	 * @throws     PropelException
	 */
	public function getControllerConfig($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';

		if ($this->aControllerConfig === null && ($this->controller_config_id > 0)) {

			$this->aControllerConfig = ControllerConfigPeer::retrieveByPK($this->controller_config_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ControllerConfigPeer::retrieveByPK($this->controller_config_id, $con);
			   $obj->addControllerConfigs($this);
			 */
		}
		return $this->aControllerConfig;
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
	 * Declares an association between this object and a Equipment object.
	 *
	 * @param      Equipment $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEquipment($v)
	{


		if ($v === null) {
			$this->setEquipmentId(NULL);
		} else {
			$this->setEquipmentId($v->getId());
		}


		$this->aEquipment = $v;
	}


	/**
	 * Get the associated Equipment object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Equipment The associated Equipment object.
	 * @throws     PropelException
	 */
	public function getEquipment($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';

		if ($this->aEquipment === null && ($this->equipment_id > 0)) {

			$this->aEquipment = EquipmentPeer::retrieveByPK($this->equipment_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EquipmentPeer::retrieveByPK($this->equipment_id, $con);
			   $obj->addEquipments($this);
			 */
		}
		return $this->aEquipment;
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
			$this->setSourceLocationId(NULL);
		} else {
			$this->setSourceLocationId($v->getId());
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

		if ($this->aLocation === null && ($this->source_location_id > 0)) {

			$this->aLocation = LocationPeer::retrieveByPK($this->source_location_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = LocationPeer::retrieveByPK($this->source_location_id, $con);
			   $obj->addLocations($this);
			 */
		}
		return $this->aLocation;
	}

	/**
	 * Temporary storage of collControllerChannelEquipments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initControllerChannelEquipments()
	{
		if ($this->collControllerChannelEquipments === null) {
			$this->collControllerChannelEquipments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ControllerChannel has previously
	 * been saved, it will retrieve related ControllerChannelEquipments from storage.
	 * If this ControllerChannel is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getControllerChannelEquipments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannelEquipments === null) {
			if ($this->isNew()) {
			   $this->collControllerChannelEquipments = array();
			} else {

				$criteria->add(ControllerChannelEquipmentPeer::CONTROLLER_CHANNEL_ID, $this->getId());

				ControllerChannelEquipmentPeer::addSelectColumns($criteria);
				$this->collControllerChannelEquipments = ControllerChannelEquipmentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ControllerChannelEquipmentPeer::CONTROLLER_CHANNEL_ID, $this->getId());

				ControllerChannelEquipmentPeer::addSelectColumns($criteria);
				if (!isset($this->lastControllerChannelEquipmentCriteria) || !$this->lastControllerChannelEquipmentCriteria->equals($criteria)) {
					$this->collControllerChannelEquipments = ControllerChannelEquipmentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastControllerChannelEquipmentCriteria = $criteria;
		return $this->collControllerChannelEquipments;
	}

	/**
	 * Returns the number of related ControllerChannelEquipments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countControllerChannelEquipments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ControllerChannelEquipmentPeer::CONTROLLER_CHANNEL_ID, $this->getId());

		return ControllerChannelEquipmentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ControllerChannelEquipment object to this object
	 * through the ControllerChannelEquipment foreign key attribute
	 *
	 * @param      ControllerChannelEquipment $l ControllerChannelEquipment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addControllerChannelEquipment(ControllerChannelEquipment $l)
	{
		$this->collControllerChannelEquipments[] = $l;
		$l->setControllerChannel($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ControllerChannel is new, it will return
	 * an empty collection; or if this ControllerChannel has previously
	 * been saved, it will retrieve related ControllerChannelEquipments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in ControllerChannel.
	 */
	public function getControllerChannelEquipmentsJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannelEquipments === null) {
			if ($this->isNew()) {
				$this->collControllerChannelEquipments = array();
			} else {

				$criteria->add(ControllerChannelEquipmentPeer::CONTROLLER_CHANNEL_ID, $this->getId());

				$this->collControllerChannelEquipments = ControllerChannelEquipmentPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelEquipmentPeer::CONTROLLER_CHANNEL_ID, $this->getId());

			if (!isset($this->lastControllerChannelEquipmentCriteria) || !$this->lastControllerChannelEquipmentCriteria->equals($criteria)) {
				$this->collControllerChannelEquipments = ControllerChannelEquipmentPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastControllerChannelEquipmentCriteria = $criteria;

		return $this->collControllerChannelEquipments;
	}

} // BaseControllerChannel
