<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SensorManifestPeer.php';

/**
 * Base class that represents a row from the 'SENSOR_MANIFEST' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSensorManifest extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SensorManifestPeer
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
	 * Collection to store aggregation of collOrganizations.
	 * @var        array
	 */
	protected $collOrganizations;

	/**
	 * The criteria used to select the current contents of collOrganizations.
	 * @var        Criteria
	 */
	protected $lastOrganizationCriteria = null;

	/**
	 * Collection to store aggregation of collSensorPools.
	 * @var        array
	 */
	protected $collSensorPools;

	/**
	 * The criteria used to select the current contents of collSensorPools.
	 * @var        Criteria
	 */
	protected $lastSensorPoolCriteria = null;

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
			$this->modifiedColumns[] = SensorManifestPeer::ID;
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
			$this->modifiedColumns[] = SensorManifestPeer::NAME;
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
			return $startcol + 2; // 2 = SensorManifestPeer::NUM_COLUMNS - SensorManifestPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SensorManifest object", $e);
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
			$con = Propel::getConnection(SensorManifestPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SensorManifestPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SensorManifestPeer::DATABASE_NAME);
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
					$pk = SensorManifestPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SensorManifestPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collOrganizations !== null) {
				foreach($this->collOrganizations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorPools !== null) {
				foreach($this->collSensorPools as $referrerFK) {
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


			if (($retval = SensorManifestPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collOrganizations !== null) {
					foreach($this->collOrganizations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorPools !== null) {
					foreach($this->collSensorPools as $referrerFK) {
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
		$pos = SensorManifestPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
		$keys = SensorManifestPeer::getFieldNames($keyType);
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
		$pos = SensorManifestPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
		$keys = SensorManifestPeer::getFieldNames($keyType);

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
		$criteria = new Criteria(SensorManifestPeer::DATABASE_NAME);

		if ($this->isColumnModified(SensorManifestPeer::ID)) $criteria->add(SensorManifestPeer::ID, $this->id);
		if ($this->isColumnModified(SensorManifestPeer::NAME)) $criteria->add(SensorManifestPeer::NAME, $this->name);

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
		$criteria = new Criteria(SensorManifestPeer::DATABASE_NAME);

		$criteria->add(SensorManifestPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SensorManifest (or compatible) type.
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

			foreach($this->getOrganizations() as $relObj) {
				$copyObj->addOrganization($relObj->copy($deepCopy));
			}

			foreach($this->getSensorPools() as $relObj) {
				$copyObj->addSensorPool($relObj->copy($deepCopy));
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
	 * @return     SensorManifest Clone of current object.
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
	 * @return     SensorManifestPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SensorManifestPeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collOrganizations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initOrganizations()
	{
		if ($this->collOrganizations === null) {
			$this->collOrganizations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorManifest has previously
	 * been saved, it will retrieve related Organizations from storage.
	 * If this SensorManifest is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getOrganizations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collOrganizations === null) {
			if ($this->isNew()) {
			   $this->collOrganizations = array();
			} else {

				$criteria->add(OrganizationPeer::SENSOR_MANIFEST_ID, $this->getId());

				OrganizationPeer::addSelectColumns($criteria);
				$this->collOrganizations = OrganizationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(OrganizationPeer::SENSOR_MANIFEST_ID, $this->getId());

				OrganizationPeer::addSelectColumns($criteria);
				if (!isset($this->lastOrganizationCriteria) || !$this->lastOrganizationCriteria->equals($criteria)) {
					$this->collOrganizations = OrganizationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastOrganizationCriteria = $criteria;
		return $this->collOrganizations;
	}

	/**
	 * Returns the number of related Organizations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countOrganizations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(OrganizationPeer::SENSOR_MANIFEST_ID, $this->getId());

		return OrganizationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Organization object to this object
	 * through the Organization foreign key attribute
	 *
	 * @param      Organization $l Organization
	 * @return     void
	 * @throws     PropelException
	 */
	public function addOrganization(Organization $l)
	{
		$this->collOrganizations[] = $l;
		$l->setSensorManifest($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorManifest is new, it will return
	 * an empty collection; or if this SensorManifest has previously
	 * been saved, it will retrieve related Organizations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorManifest.
	 */
	public function getOrganizationsJoinOrganizationRelatedByFacilityId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collOrganizations === null) {
			if ($this->isNew()) {
				$this->collOrganizations = array();
			} else {

				$criteria->add(OrganizationPeer::SENSOR_MANIFEST_ID, $this->getId());

				$this->collOrganizations = OrganizationPeer::doSelectJoinOrganizationRelatedByFacilityId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(OrganizationPeer::SENSOR_MANIFEST_ID, $this->getId());

			if (!isset($this->lastOrganizationCriteria) || !$this->lastOrganizationCriteria->equals($criteria)) {
				$this->collOrganizations = OrganizationPeer::doSelectJoinOrganizationRelatedByFacilityId($criteria, $con);
			}
		}
		$this->lastOrganizationCriteria = $criteria;

		return $this->collOrganizations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorManifest is new, it will return
	 * an empty collection; or if this SensorManifest has previously
	 * been saved, it will retrieve related Organizations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorManifest.
	 */
	public function getOrganizationsJoinOrganizationRelatedByParentOrgId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collOrganizations === null) {
			if ($this->isNew()) {
				$this->collOrganizations = array();
			} else {

				$criteria->add(OrganizationPeer::SENSOR_MANIFEST_ID, $this->getId());

				$this->collOrganizations = OrganizationPeer::doSelectJoinOrganizationRelatedByParentOrgId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(OrganizationPeer::SENSOR_MANIFEST_ID, $this->getId());

			if (!isset($this->lastOrganizationCriteria) || !$this->lastOrganizationCriteria->equals($criteria)) {
				$this->collOrganizations = OrganizationPeer::doSelectJoinOrganizationRelatedByParentOrgId($criteria, $con);
			}
		}
		$this->lastOrganizationCriteria = $criteria;

		return $this->collOrganizations;
	}

	/**
	 * Temporary storage of collSensorPools to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorPools()
	{
		if ($this->collSensorPools === null) {
			$this->collSensorPools = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorManifest has previously
	 * been saved, it will retrieve related SensorPools from storage.
	 * If this SensorManifest is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorPools($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorPoolPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorPools === null) {
			if ($this->isNew()) {
			   $this->collSensorPools = array();
			} else {

				$criteria->add(SensorPoolPeer::MANIFEST_ID, $this->getId());

				SensorPoolPeer::addSelectColumns($criteria);
				$this->collSensorPools = SensorPoolPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorPoolPeer::MANIFEST_ID, $this->getId());

				SensorPoolPeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorPoolCriteria) || !$this->lastSensorPoolCriteria->equals($criteria)) {
					$this->collSensorPools = SensorPoolPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorPoolCriteria = $criteria;
		return $this->collSensorPools;
	}

	/**
	 * Returns the number of related SensorPools.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorPools($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorPoolPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorPoolPeer::MANIFEST_ID, $this->getId());

		return SensorPoolPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorPool object to this object
	 * through the SensorPool foreign key attribute
	 *
	 * @param      SensorPool $l SensorPool
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorPool(SensorPool $l)
	{
		$this->collSensorPools[] = $l;
		$l->setSensorManifest($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorManifest is new, it will return
	 * an empty collection; or if this SensorManifest has previously
	 * been saved, it will retrieve related SensorPools from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorManifest.
	 */
	public function getSensorPoolsJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorPoolPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorPools === null) {
			if ($this->isNew()) {
				$this->collSensorPools = array();
			} else {

				$criteria->add(SensorPoolPeer::MANIFEST_ID, $this->getId());

				$this->collSensorPools = SensorPoolPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorPoolPeer::MANIFEST_ID, $this->getId());

			if (!isset($this->lastSensorPoolCriteria) || !$this->lastSensorPoolCriteria->equals($criteria)) {
				$this->collSensorPools = SensorPoolPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastSensorPoolCriteria = $criteria;

		return $this->collSensorPools;
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
	 * Otherwise if this SensorManifest has previously
	 * been saved, it will retrieve related SensorSensorManifests from storage.
	 * If this SensorManifest is new, it will return
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

				$criteria->add(SensorSensorManifestPeer::MANIFEST_ID, $this->getId());

				SensorSensorManifestPeer::addSelectColumns($criteria);
				$this->collSensorSensorManifests = SensorSensorManifestPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorSensorManifestPeer::MANIFEST_ID, $this->getId());

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

		$criteria->add(SensorSensorManifestPeer::MANIFEST_ID, $this->getId());

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
		$l->setSensorManifest($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SensorManifest is new, it will return
	 * an empty collection; or if this SensorManifest has previously
	 * been saved, it will retrieve related SensorSensorManifests from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SensorManifest.
	 */
	public function getSensorSensorManifestsJoinSensor($criteria = null, $con = null)
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

				$criteria->add(SensorSensorManifestPeer::MANIFEST_ID, $this->getId());

				$this->collSensorSensorManifests = SensorSensorManifestPeer::doSelectJoinSensor($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorSensorManifestPeer::MANIFEST_ID, $this->getId());

			if (!isset($this->lastSensorSensorManifestCriteria) || !$this->lastSensorSensorManifestCriteria->equals($criteria)) {
				$this->collSensorSensorManifests = SensorSensorManifestPeer::doSelectJoinSensor($criteria, $con);
			}
		}
		$this->lastSensorSensorManifestCriteria = $criteria;

		return $this->collSensorSensorManifests;
	}

} // BaseSensorManifest
