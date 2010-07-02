<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SensorSensorManifestPeer.php';

/**
 * Base class that represents a row from the 'SENSOR_SENSOR_MANIFEST' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSensorSensorManifest extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SensorSensorManifestPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the sensor_id field.
	 * @var        double
	 */
	protected $sensor_id;


	/**
	 * The value for the manifest_id field.
	 * @var        double
	 */
	protected $manifest_id;

	/**
	 * @var        Sensor
	 */
	protected $aSensor;

	/**
	 * @var        SensorManifest
	 */
	protected $aSensorManifest;

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
	 * Get the [sensor_id] column value.
	 * 
	 * @return     double
	 */
	public function getSensorId()
	{

		return $this->sensor_id;
	}

	/**
	 * Get the [manifest_id] column value.
	 * 
	 * @return     double
	 */
	public function getSensorManifestId()
	{

		return $this->manifest_id;
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
			$this->modifiedColumns[] = SensorSensorManifestPeer::ID;
		}

	} // setId()

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
			$this->modifiedColumns[] = SensorSensorManifestPeer::SENSOR_ID;
		}

		if ($this->aSensor !== null && $this->aSensor->getId() !== $v) {
			$this->aSensor = null;
		}

	} // setSensorId()

	/**
	 * Set the value of [manifest_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSensorManifestId($v)
	{

		if ($this->manifest_id !== $v) {
			$this->manifest_id = $v;
			$this->modifiedColumns[] = SensorSensorManifestPeer::MANIFEST_ID;
		}

		if ($this->aSensorManifest !== null && $this->aSensorManifest->getId() !== $v) {
			$this->aSensorManifest = null;
		}

	} // setSensorManifestId()

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

			$this->sensor_id = $rs->getFloat($startcol + 1);

			$this->manifest_id = $rs->getFloat($startcol + 2);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 3; // 3 = SensorSensorManifestPeer::NUM_COLUMNS - SensorSensorManifestPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SensorSensorManifest object", $e);
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
			$con = Propel::getConnection(SensorSensorManifestPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SensorSensorManifestPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SensorSensorManifestPeer::DATABASE_NAME);
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

			if ($this->aSensorManifest !== null) {
				if ($this->aSensorManifest->isModified()) {
					$affectedRows += $this->aSensorManifest->save($con);
				}
				$this->setSensorManifest($this->aSensorManifest);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SensorSensorManifestPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SensorSensorManifestPeer::doUpdate($this, $con);
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

			if ($this->aSensorManifest !== null) {
				if (!$this->aSensorManifest->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSensorManifest->getValidationFailures());
				}
			}


			if (($retval = SensorSensorManifestPeer::doValidate($this, $columns)) !== true) {
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
		$pos = SensorSensorManifestPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getSensorId();
				break;
			case 2:
				return $this->getSensorManifestId();
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
		$keys = SensorSensorManifestPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getSensorId(),
			$keys[2] => $this->getSensorManifestId(),
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
		$pos = SensorSensorManifestPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setSensorId($value);
				break;
			case 2:
				$this->setSensorManifestId($value);
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
		$keys = SensorSensorManifestPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setSensorId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setSensorManifestId($arr[$keys[2]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SensorSensorManifestPeer::DATABASE_NAME);

		if ($this->isColumnModified(SensorSensorManifestPeer::ID)) $criteria->add(SensorSensorManifestPeer::ID, $this->id);
		if ($this->isColumnModified(SensorSensorManifestPeer::SENSOR_ID)) $criteria->add(SensorSensorManifestPeer::SENSOR_ID, $this->sensor_id);
		if ($this->isColumnModified(SensorSensorManifestPeer::MANIFEST_ID)) $criteria->add(SensorSensorManifestPeer::MANIFEST_ID, $this->manifest_id);

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
		$criteria = new Criteria(SensorSensorManifestPeer::DATABASE_NAME);

		$criteria->add(SensorSensorManifestPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SensorSensorManifest (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setSensorId($this->sensor_id);

		$copyObj->setSensorManifestId($this->manifest_id);


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
	 * @return     SensorSensorManifest Clone of current object.
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
	 * @return     SensorSensorManifestPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SensorSensorManifestPeer();
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

	/**
	 * Declares an association between this object and a SensorManifest object.
	 *
	 * @param      SensorManifest $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSensorManifest($v)
	{


		if ($v === null) {
			$this->setSensorManifestId(NULL);
		} else {
			$this->setSensorManifestId($v->getId());
		}


		$this->aSensorManifest = $v;
	}


	/**
	 * Get the associated SensorManifest object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SensorManifest The associated SensorManifest object.
	 * @throws     PropelException
	 */
	public function getSensorManifest($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSensorManifestPeer.php';

		if ($this->aSensorManifest === null && ($this->manifest_id > 0)) {

			$this->aSensorManifest = SensorManifestPeer::retrieveByPK($this->manifest_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SensorManifestPeer::retrieveByPK($this->manifest_id, $con);
			   $obj->addSensorManifests($this);
			 */
		}
		return $this->aSensorManifest;
	}

} // BaseSensorSensorManifest
