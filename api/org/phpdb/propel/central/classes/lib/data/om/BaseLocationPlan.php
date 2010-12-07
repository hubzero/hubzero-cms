<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/LocationPlanPeer.php';

/**
 * Base class that represents a row from the 'LOCATION_PLAN' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseLocationPlan extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        LocationPlanPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the expid field.
	 * @var        double
	 */
	protected $expid;


	/**
	 * The value for the trial_id field.
	 * @var        double
	 */
	protected $trial_id;


	/**
	 * The value for the data_file_id field.
	 * @var        double
	 */
	protected $data_file_id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the plan_type_id field.
	 * @var        double
	 */
	protected $plan_type_id = 0;

	/**
	 * @var        Experiment
	 */
	protected $aExperiment;

	/**
	 * @var        Trial
	 */
	protected $aTrial;

	/**
	 * @var        DataFile
	 */
	protected $aDataFile;

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
	 * Get the [expid] column value.
	 * 
	 * @return     double
	 */
	public function getExperimentId()
	{

		return $this->expid;
	}

	/**
	 * Get the [trial_id] column value.
	 * 
	 * @return     double
	 */
	public function getTrialId()
	{

		return $this->trial_id;
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
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{

		return $this->name;
	}

	/**
	 * Get the [plan_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getPlanTypeId()
	{

		return $this->plan_type_id;
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
			$this->modifiedColumns[] = LocationPlanPeer::ID;
		}

	} // setId()

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
			$this->modifiedColumns[] = LocationPlanPeer::EXPID;
		}

		if ($this->aExperiment !== null && $this->aExperiment->getId() !== $v) {
			$this->aExperiment = null;
		}

	} // setExperimentId()

	/**
	 * Set the value of [trial_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTrialId($v)
	{

		if ($this->trial_id !== $v) {
			$this->trial_id = $v;
			$this->modifiedColumns[] = LocationPlanPeer::TRIAL_ID;
		}

		if ($this->aTrial !== null && $this->aTrial->getId() !== $v) {
			$this->aTrial = null;
		}

	} // setTrialId()

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
			$this->modifiedColumns[] = LocationPlanPeer::DATA_FILE_ID;
		}

		if ($this->aDataFile !== null && $this->aDataFile->getId() !== $v) {
			$this->aDataFile = null;
		}

	} // setDataFileId()

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
			$this->modifiedColumns[] = LocationPlanPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [plan_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPlanTypeId($v)
	{

		if ($this->plan_type_id !== $v || $v === 0) {
			$this->plan_type_id = $v;
			$this->modifiedColumns[] = LocationPlanPeer::PLAN_TYPE_ID;
		}

	} // setPlanTypeId()

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

			$this->expid = $rs->getFloat($startcol + 1);

			$this->trial_id = $rs->getFloat($startcol + 2);

			$this->data_file_id = $rs->getFloat($startcol + 3);

			$this->name = $rs->getString($startcol + 4);

			$this->plan_type_id = $rs->getFloat($startcol + 5);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = LocationPlanPeer::NUM_COLUMNS - LocationPlanPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating LocationPlan object", $e);
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
			$con = Propel::getConnection(LocationPlanPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			LocationPlanPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(LocationPlanPeer::DATABASE_NAME);
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

			if ($this->aExperiment !== null) {
				if ($this->aExperiment->isModified()) {
					$affectedRows += $this->aExperiment->save($con);
				}
				$this->setExperiment($this->aExperiment);
			}

			if ($this->aTrial !== null) {
				if ($this->aTrial->isModified()) {
					$affectedRows += $this->aTrial->save($con);
				}
				$this->setTrial($this->aTrial);
			}

			if ($this->aDataFile !== null) {
				if ($this->aDataFile->isModified()) {
					$affectedRows += $this->aDataFile->save($con);
				}
				$this->setDataFile($this->aDataFile);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = LocationPlanPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += LocationPlanPeer::doUpdate($this, $con);
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

			if ($this->aExperiment !== null) {
				if (!$this->aExperiment->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aExperiment->getValidationFailures());
				}
			}

			if ($this->aTrial !== null) {
				if (!$this->aTrial->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aTrial->getValidationFailures());
				}
			}

			if ($this->aDataFile !== null) {
				if (!$this->aDataFile->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFile->getValidationFailures());
				}
			}


			if (($retval = LocationPlanPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collLocations !== null) {
					foreach($this->collLocations as $referrerFK) {
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
		$pos = LocationPlanPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getExperimentId();
				break;
			case 2:
				return $this->getTrialId();
				break;
			case 3:
				return $this->getDataFileId();
				break;
			case 4:
				return $this->getName();
				break;
			case 5:
				return $this->getPlanTypeId();
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
		$keys = LocationPlanPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getExperimentId(),
			$keys[2] => $this->getTrialId(),
			$keys[3] => $this->getDataFileId(),
			$keys[4] => $this->getName(),
			$keys[5] => $this->getPlanTypeId(),
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
		$pos = LocationPlanPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setExperimentId($value);
				break;
			case 2:
				$this->setTrialId($value);
				break;
			case 3:
				$this->setDataFileId($value);
				break;
			case 4:
				$this->setName($value);
				break;
			case 5:
				$this->setPlanTypeId($value);
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
		$keys = LocationPlanPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setExperimentId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setTrialId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDataFileId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setPlanTypeId($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(LocationPlanPeer::DATABASE_NAME);

		if ($this->isColumnModified(LocationPlanPeer::ID)) $criteria->add(LocationPlanPeer::ID, $this->id);
		if ($this->isColumnModified(LocationPlanPeer::EXPID)) $criteria->add(LocationPlanPeer::EXPID, $this->expid);
		if ($this->isColumnModified(LocationPlanPeer::TRIAL_ID)) $criteria->add(LocationPlanPeer::TRIAL_ID, $this->trial_id);
		if ($this->isColumnModified(LocationPlanPeer::DATA_FILE_ID)) $criteria->add(LocationPlanPeer::DATA_FILE_ID, $this->data_file_id);
		if ($this->isColumnModified(LocationPlanPeer::NAME)) $criteria->add(LocationPlanPeer::NAME, $this->name);
		if ($this->isColumnModified(LocationPlanPeer::PLAN_TYPE_ID)) $criteria->add(LocationPlanPeer::PLAN_TYPE_ID, $this->plan_type_id);

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
		$criteria = new Criteria(LocationPlanPeer::DATABASE_NAME);

		$criteria->add(LocationPlanPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of LocationPlan (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setExperimentId($this->expid);

		$copyObj->setTrialId($this->trial_id);

		$copyObj->setDataFileId($this->data_file_id);

		$copyObj->setName($this->name);

		$copyObj->setPlanTypeId($this->plan_type_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getLocations() as $relObj) {
				$copyObj->addLocation($relObj->copy($deepCopy));
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
	 * @return     LocationPlan Clone of current object.
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
	 * @return     LocationPlanPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new LocationPlanPeer();
		}
		return self::$peer;
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
	 * Declares an association between this object and a Trial object.
	 *
	 * @param      Trial $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setTrial($v)
	{


		if ($v === null) {
			$this->setTrialId(NULL);
		} else {
			$this->setTrialId($v->getId());
		}


		$this->aTrial = $v;
	}


	/**
	 * Get the associated Trial object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Trial The associated Trial object.
	 * @throws     PropelException
	 */
	public function getTrial($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseTrialPeer.php';

		if ($this->aTrial === null && ($this->trial_id > 0)) {

			$this->aTrial = TrialPeer::retrieveByPK($this->trial_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = TrialPeer::retrieveByPK($this->trial_id, $con);
			   $obj->addTrials($this);
			 */
		}
		return $this->aTrial;
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
	 * Otherwise if this LocationPlan has previously
	 * been saved, it will retrieve related Locations from storage.
	 * If this LocationPlan is new, it will return
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

				$criteria->add(LocationPeer::PLAN_ID, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				$this->collLocations = LocationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LocationPeer::PLAN_ID, $this->getId());

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

		$criteria->add(LocationPeer::PLAN_ID, $this->getId());

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
		$l->setLocationPlan($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this LocationPlan is new, it will return
	 * an empty collection; or if this LocationPlan has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in LocationPlan.
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

				$criteria->add(LocationPeer::PLAN_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::PLAN_ID, $this->getId());

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
	 * Otherwise if this LocationPlan is new, it will return
	 * an empty collection; or if this LocationPlan has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in LocationPlan.
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

				$criteria->add(LocationPeer::PLAN_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByJUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::PLAN_ID, $this->getId());

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
	 * Otherwise if this LocationPlan is new, it will return
	 * an empty collection; or if this LocationPlan has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in LocationPlan.
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

				$criteria->add(LocationPeer::PLAN_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByYUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::PLAN_ID, $this->getId());

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
	 * Otherwise if this LocationPlan is new, it will return
	 * an empty collection; or if this LocationPlan has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in LocationPlan.
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

				$criteria->add(LocationPeer::PLAN_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByXUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::PLAN_ID, $this->getId());

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
	 * Otherwise if this LocationPlan is new, it will return
	 * an empty collection; or if this LocationPlan has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in LocationPlan.
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

				$criteria->add(LocationPeer::PLAN_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByIUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::PLAN_ID, $this->getId());

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
	 * Otherwise if this LocationPlan is new, it will return
	 * an empty collection; or if this LocationPlan has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in LocationPlan.
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

				$criteria->add(LocationPeer::PLAN_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByZUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::PLAN_ID, $this->getId());

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
	 * Otherwise if this LocationPlan is new, it will return
	 * an empty collection; or if this LocationPlan has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in LocationPlan.
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

				$criteria->add(LocationPeer::PLAN_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByKUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::PLAN_ID, $this->getId());

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
	 * Otherwise if this LocationPlan is new, it will return
	 * an empty collection; or if this LocationPlan has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in LocationPlan.
	 */
	public function getLocationsJoinSensorType($criteria = null, $con = null)
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

				$criteria->add(LocationPeer::PLAN_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::PLAN_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this LocationPlan is new, it will return
	 * an empty collection; or if this LocationPlan has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in LocationPlan.
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

				$criteria->add(LocationPeer::PLAN_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::PLAN_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}

} // BaseLocationPlan
