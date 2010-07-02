<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/ExperimentDomainPeer.php';

/**
 * Base class that represents a row from the 'EXPERIMENT_DOMAIN' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseExperimentDomain extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ExperimentDomainPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the display_name field.
	 * @var        string
	 */
	protected $display_name;


	/**
	 * The value for the system_name field.
	 * @var        string
	 */
	protected $system_name;


	/**
	 * The value for the display_order field.
	 * @var        double
	 */
	protected $display_order;

	/**
	 * Collection to store aggregation of collExperiments.
	 * @var        array
	 */
	protected $collExperiments;

	/**
	 * The criteria used to select the current contents of collExperiments.
	 * @var        Criteria
	 */
	protected $lastExperimentCriteria = null;

	/**
	 * Collection to store aggregation of collSimilitudeLawGroups.
	 * @var        array
	 */
	protected $collSimilitudeLawGroups;

	/**
	 * The criteria used to select the current contents of collSimilitudeLawGroups.
	 * @var        Criteria
	 */
	protected $lastSimilitudeLawGroupCriteria = null;

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
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
	}

	/**
	 * Get the [display_name] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayName()
	{

		return $this->display_name;
	}

	/**
	 * Get the [system_name] column value.
	 * 
	 * @return     string
	 */
	public function getSystemName()
	{

		return $this->system_name;
	}

	/**
	 * Get the [display_order] column value.
	 * 
	 * @return     double
	 */
	public function getDisplayOrder()
	{

		return $this->display_order;
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
			$this->modifiedColumns[] = ExperimentDomainPeer::ID;
		}

	} // setId()

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
			$this->modifiedColumns[] = ExperimentDomainPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [display_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDisplayName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->display_name !== $v) {
			$this->display_name = $v;
			$this->modifiedColumns[] = ExperimentDomainPeer::DISPLAY_NAME;
		}

	} // setDisplayName()

	/**
	 * Set the value of [system_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSystemName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->system_name !== $v) {
			$this->system_name = $v;
			$this->modifiedColumns[] = ExperimentDomainPeer::SYSTEM_NAME;
		}

	} // setSystemName()

	/**
	 * Set the value of [display_order] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDisplayOrder($v)
	{

		if ($this->display_order !== $v) {
			$this->display_order = $v;
			$this->modifiedColumns[] = ExperimentDomainPeer::DISPLAY_ORDER;
		}

	} // setDisplayOrder()

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

			$this->description = $rs->getString($startcol + 1);

			$this->display_name = $rs->getString($startcol + 2);

			$this->system_name = $rs->getString($startcol + 3);

			$this->display_order = $rs->getFloat($startcol + 4);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 5; // 5 = ExperimentDomainPeer::NUM_COLUMNS - ExperimentDomainPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating ExperimentDomain object", $e);
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
			$con = Propel::getConnection(ExperimentDomainPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			ExperimentDomainPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ExperimentDomainPeer::DATABASE_NAME);
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
					$pk = ExperimentDomainPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += ExperimentDomainPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collExperiments !== null) {
				foreach($this->collExperiments as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSimilitudeLawGroups !== null) {
				foreach($this->collSimilitudeLawGroups as $referrerFK) {
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


			if (($retval = ExperimentDomainPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collExperiments !== null) {
					foreach($this->collExperiments as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSimilitudeLawGroups !== null) {
					foreach($this->collSimilitudeLawGroups as $referrerFK) {
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
		$pos = ExperimentDomainPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDescription();
				break;
			case 2:
				return $this->getDisplayName();
				break;
			case 3:
				return $this->getSystemName();
				break;
			case 4:
				return $this->getDisplayOrder();
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
		$keys = ExperimentDomainPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDescription(),
			$keys[2] => $this->getDisplayName(),
			$keys[3] => $this->getSystemName(),
			$keys[4] => $this->getDisplayOrder(),
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
		$pos = ExperimentDomainPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDescription($value);
				break;
			case 2:
				$this->setDisplayName($value);
				break;
			case 3:
				$this->setSystemName($value);
				break;
			case 4:
				$this->setDisplayOrder($value);
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
		$keys = ExperimentDomainPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDescription($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDisplayName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setSystemName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDisplayOrder($arr[$keys[4]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ExperimentDomainPeer::DATABASE_NAME);

		if ($this->isColumnModified(ExperimentDomainPeer::ID)) $criteria->add(ExperimentDomainPeer::ID, $this->id);
		if ($this->isColumnModified(ExperimentDomainPeer::DESCRIPTION)) $criteria->add(ExperimentDomainPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(ExperimentDomainPeer::DISPLAY_NAME)) $criteria->add(ExperimentDomainPeer::DISPLAY_NAME, $this->display_name);
		if ($this->isColumnModified(ExperimentDomainPeer::SYSTEM_NAME)) $criteria->add(ExperimentDomainPeer::SYSTEM_NAME, $this->system_name);
		if ($this->isColumnModified(ExperimentDomainPeer::DISPLAY_ORDER)) $criteria->add(ExperimentDomainPeer::DISPLAY_ORDER, $this->display_order);

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
		$criteria = new Criteria(ExperimentDomainPeer::DATABASE_NAME);

		$criteria->add(ExperimentDomainPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of ExperimentDomain (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDescription($this->description);

		$copyObj->setDisplayName($this->display_name);

		$copyObj->setSystemName($this->system_name);

		$copyObj->setDisplayOrder($this->display_order);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getExperiments() as $relObj) {
				$copyObj->addExperiment($relObj->copy($deepCopy));
			}

			foreach($this->getSimilitudeLawGroups() as $relObj) {
				$copyObj->addSimilitudeLawGroup($relObj->copy($deepCopy));
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
	 * @return     ExperimentDomain Clone of current object.
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
	 * @return     ExperimentDomainPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ExperimentDomainPeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collExperiments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initExperiments()
	{
		if ($this->collExperiments === null) {
			$this->collExperiments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ExperimentDomain has previously
	 * been saved, it will retrieve related Experiments from storage.
	 * If this ExperimentDomain is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getExperiments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperiments === null) {
			if ($this->isNew()) {
			   $this->collExperiments = array();
			} else {

				$criteria->add(ExperimentPeer::EXPERIMENT_DOMAIN_ID, $this->getId());

				ExperimentPeer::addSelectColumns($criteria);
				$this->collExperiments = ExperimentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentPeer::EXPERIMENT_DOMAIN_ID, $this->getId());

				ExperimentPeer::addSelectColumns($criteria);
				if (!isset($this->lastExperimentCriteria) || !$this->lastExperimentCriteria->equals($criteria)) {
					$this->collExperiments = ExperimentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastExperimentCriteria = $criteria;
		return $this->collExperiments;
	}

	/**
	 * Returns the number of related Experiments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countExperiments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ExperimentPeer::EXPERIMENT_DOMAIN_ID, $this->getId());

		return ExperimentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Experiment object to this object
	 * through the Experiment foreign key attribute
	 *
	 * @param      Experiment $l Experiment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addExperiment(Experiment $l)
	{
		$this->collExperiments[] = $l;
		$l->setExperimentDomain($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ExperimentDomain is new, it will return
	 * an empty collection; or if this ExperimentDomain has previously
	 * been saved, it will retrieve related Experiments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in ExperimentDomain.
	 */
	public function getExperimentsJoinProject($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperiments === null) {
			if ($this->isNew()) {
				$this->collExperiments = array();
			} else {

				$criteria->add(ExperimentPeer::EXPERIMENT_DOMAIN_ID, $this->getId());

				$this->collExperiments = ExperimentPeer::doSelectJoinProject($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentPeer::EXPERIMENT_DOMAIN_ID, $this->getId());

			if (!isset($this->lastExperimentCriteria) || !$this->lastExperimentCriteria->equals($criteria)) {
				$this->collExperiments = ExperimentPeer::doSelectJoinProject($criteria, $con);
			}
		}
		$this->lastExperimentCriteria = $criteria;

		return $this->collExperiments;
	}

	/**
	 * Temporary storage of collSimilitudeLawGroups to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSimilitudeLawGroups()
	{
		if ($this->collSimilitudeLawGroups === null) {
			$this->collSimilitudeLawGroups = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ExperimentDomain has previously
	 * been saved, it will retrieve related SimilitudeLawGroups from storage.
	 * If this ExperimentDomain is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSimilitudeLawGroups($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSimilitudeLawGroupPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSimilitudeLawGroups === null) {
			if ($this->isNew()) {
			   $this->collSimilitudeLawGroups = array();
			} else {

				$criteria->add(SimilitudeLawGroupPeer::EXPERIMENT_DOMAIN_ID, $this->getId());

				SimilitudeLawGroupPeer::addSelectColumns($criteria);
				$this->collSimilitudeLawGroups = SimilitudeLawGroupPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SimilitudeLawGroupPeer::EXPERIMENT_DOMAIN_ID, $this->getId());

				SimilitudeLawGroupPeer::addSelectColumns($criteria);
				if (!isset($this->lastSimilitudeLawGroupCriteria) || !$this->lastSimilitudeLawGroupCriteria->equals($criteria)) {
					$this->collSimilitudeLawGroups = SimilitudeLawGroupPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSimilitudeLawGroupCriteria = $criteria;
		return $this->collSimilitudeLawGroups;
	}

	/**
	 * Returns the number of related SimilitudeLawGroups.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSimilitudeLawGroups($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSimilitudeLawGroupPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SimilitudeLawGroupPeer::EXPERIMENT_DOMAIN_ID, $this->getId());

		return SimilitudeLawGroupPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SimilitudeLawGroup object to this object
	 * through the SimilitudeLawGroup foreign key attribute
	 *
	 * @param      SimilitudeLawGroup $l SimilitudeLawGroup
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSimilitudeLawGroup(SimilitudeLawGroup $l)
	{
		$this->collSimilitudeLawGroups[] = $l;
		$l->setExperimentDomain($this);
	}

} // BaseExperimentDomain
