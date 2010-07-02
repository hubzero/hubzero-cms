<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SensorTypePeer.php';

/**
 * Base class that represents a row from the 'SENSOR_TYPE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSensorType extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SensorTypePeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * Collection to store aggregation of collLocations.
	 * @var        array
	 */
	protected $collLocations;

	/**
	 * The criteria used to select the current contents of collLocations.
	 * @var        Criteria
	 */
	protected $lastLocationCriteria = null;

	/**
	 * Collection to store aggregation of collSensorModels.
	 * @var        array
	 */
	protected $collSensorModels;

	/**
	 * The criteria used to select the current contents of collSensorModels.
	 * @var        Criteria
	 */
	protected $lastSensorModelCriteria = null;

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
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{

		return $this->name;
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
			$this->modifiedColumns[] = SensorTypePeer::ID;
		}

	} // setId()

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
			$this->modifiedColumns[] = SensorTypePeer::NAME;
		}

	} // setName()

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

			$this->name = $rs->getString($startcol + 1);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 2; // 2 = SensorTypePeer::NUM_COLUMNS - SensorTypePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SensorType object", $e);
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
			$con = Propel::getConnection(SensorTypePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SensorTypePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SensorTypePeer::DATABASE_NAME);
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
					$pk = SensorTypePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SensorTypePeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collLocations !== null) {
				foreach($this->collLocations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorModels !== null) {
				foreach($this->collSensorModels as $referrerFK) {
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


			if (($retval = SensorTypePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collLocations !== null) {
					foreach($this->collLocations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorModels !== null) {
					foreach($this->collSensorModels as $referrerFK) {
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
		$pos = SensorTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getName();
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
		$keys = SensorTypePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getName(),
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
		$pos = SensorTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setName($value);
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
		$keys = SensorTypePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SensorTypePeer::DATABASE_NAME);

		if ($this->isColumnModified(SensorTypePeer::ID)) $criteria->add(SensorTypePeer::ID, $this->id);
		if ($this->isColumnModified(SensorTypePeer::NAME)) $criteria->add(SensorTypePeer::NAME, $this->name);

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
		$criteria = new Criteria(SensorTypePeer::DATABASE_NAME);

		$criteria->add(SensorTypePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SensorType (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setName($this->name);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getLocations() as $relObj) {
				$copyObj->addLocation($relObj->copy($deepCopy));
			}

			foreach($this->getSensorModels() as $relObj) {
				$copyObj->addSensorModel($relObj->copy($deepCopy));
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
	 * @return     SensorType Clone of current object.
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
	 * @return     SensorTypePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SensorTypePeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collLocations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLocations()
	{
		if ($this->collLocations === null) {
			$this->collLocations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType has previously
	 * been saved, it will retrieve related Locations from storage.
	 * If this SensorType is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLocations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocations === null) {
			if ($this->isNew()) {
			   $this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				$this->collLocations = LocationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
					$this->collLocations = LocationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLocationCriteria = $criteria;
		return $this->collLocations;
	}

	/**
	 * Returns the number of related Locations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLocations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

		return LocationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Location object to this object
	 * through the Location foreign key attribute
	 *
	 * @param      Location $l Location
	 * @return     void
	 * @throws     PropelException
	 */
	public function addLocation(Location $l)
	{
		$this->collLocations[] = $l;
		$l->setSensorType($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getLocationsJoinCoordinateSpace($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getLocationsJoinLocationPlan($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByJUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByJUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByJUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByYUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByYUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByYUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByXUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByXUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByXUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByIUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByIUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByIUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByZUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByZUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByZUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByKUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByKUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByKUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getLocationsJoinSourceType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}

	/**
	 * Temporary storage of collSensorModels to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorModels()
	{
		if ($this->collSensorModels === null) {
			$this->collSensorModels = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType has previously
	 * been saved, it will retrieve related SensorModels from storage.
	 * If this SensorType is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorModels($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModels === null) {
			if ($this->isNew()) {
			   $this->collSensorModels = array();
			} else {

				$criteria->add(SensorModelPeer::SENSOR_TYPE_ID, $this->getId());

				SensorModelPeer::addSelectColumns($criteria);
				$this->collSensorModels = SensorModelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorModelPeer::SENSOR_TYPE_ID, $this->getId());

				SensorModelPeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorModelCriteria) || !$this->lastSensorModelCriteria->equals($criteria)) {
					$this->collSensorModels = SensorModelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorModelCriteria = $criteria;
		return $this->collSensorModels;
	}

	/**
	 * Returns the number of related SensorModels.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorModels($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorModelPeer::SENSOR_TYPE_ID, $this->getId());

		return SensorModelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorModel object to this object
	 * through the SensorModel foreign key attribute
	 *
	 * @param      SensorModel $l SensorModel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorModel(SensorModel $l)
	{
		$this->collSensorModels[] = $l;
		$l->setSensorType($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related SensorModels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getSensorModelsJoinMeasurementUnitRelatedByMeasuredValueUnitsId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModels === null) {
			if ($this->isNew()) {
				$this->collSensorModels = array();
			} else {

				$criteria->add(SensorModelPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collSensorModels = SensorModelPeer::doSelectJoinMeasurementUnitRelatedByMeasuredValueUnitsId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorModelPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastSensorModelCriteria) || !$this->lastSensorModelCriteria->equals($criteria)) {
				$this->collSensorModels = SensorModelPeer::doSelectJoinMeasurementUnitRelatedByMeasuredValueUnitsId($criteria, $con);
			}
		}
		$this->lastSensorModelCriteria = $criteria;

		return $this->collSensorModels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related SensorModels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getSensorModelsJoinMeasurementUnitRelatedBySensitivityUnitsId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModels === null) {
			if ($this->isNew()) {
				$this->collSensorModels = array();
			} else {

				$criteria->add(SensorModelPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collSensorModels = SensorModelPeer::doSelectJoinMeasurementUnitRelatedBySensitivityUnitsId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorModelPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastSensorModelCriteria) || !$this->lastSensorModelCriteria->equals($criteria)) {
				$this->collSensorModels = SensorModelPeer::doSelectJoinMeasurementUnitRelatedBySensitivityUnitsId($criteria, $con);
			}
		}
		$this->lastSensorModelCriteria = $criteria;

		return $this->collSensorModels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorType is new, it will return
	 * an empty collection; or if this SensorType has previously
	 * been saved, it will retrieve related SensorModels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorType.
	 */
	public function getSensorModelsJoinMeasurementUnitRelatedByTempUnitsId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModels === null) {
			if ($this->isNew()) {
				$this->collSensorModels = array();
			} else {

				$criteria->add(SensorModelPeer::SENSOR_TYPE_ID, $this->getId());

				$this->collSensorModels = SensorModelPeer::doSelectJoinMeasurementUnitRelatedByTempUnitsId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorModelPeer::SENSOR_TYPE_ID, $this->getId());

			if (!isset($this->lastSensorModelCriteria) || !$this->lastSensorModelCriteria->equals($criteria)) {
				$this->collSensorModels = SensorModelPeer::doSelectJoinMeasurementUnitRelatedByTempUnitsId($criteria, $con);
			}
		}
		$this->lastSensorModelCriteria = $criteria;

		return $this->collSensorModels;
	}

} // BaseSensorType
