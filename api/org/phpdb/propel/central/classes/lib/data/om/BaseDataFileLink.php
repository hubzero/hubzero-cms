<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/DataFileLinkPeer.php';

/**
 * Base class that represents a row from the 'DATA_FILE_LINK' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseDataFileLink extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        DataFileLinkPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the proj_id field.
	 * @var        double
	 */
	protected $proj_id;


	/**
	 * The value for the exp_id field.
	 * @var        double
	 */
	protected $exp_id;


	/**
	 * The value for the trial_id field.
	 * @var        double
	 */
	protected $trial_id;


	/**
	 * The value for the rep_id field.
	 * @var        double
	 */
	protected $rep_id;


	/**
	 * The value for the deleted field.
	 * @var        double
	 */
	protected $deleted;

	/**
	 * @var        Project
	 */
	protected $aProject;

	/**
	 * @var        Experiment
	 */
	protected $aExperiment;

	/**
	 * @var        Trial
	 */
	protected $aTrial;

	/**
	 * @var        Repetition
	 */
	protected $aRepetition;

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
	 * Get the [proj_id] column value.
	 * 
	 * @return     double
	 */
	public function getProjectId()
	{

		return $this->proj_id;
	}

	/**
	 * Get the [exp_id] column value.
	 * 
	 * @return     double
	 */
	public function getExperimentId()
	{

		return $this->exp_id;
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
	 * Get the [rep_id] column value.
	 * 
	 * @return     double
	 */
	public function getRepId()
	{

		return $this->rep_id;
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
	 * Set the value of [id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = DataFileLinkPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [proj_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setProjectId($v)
	{

		if ($this->proj_id !== $v) {
			$this->proj_id = $v;
			$this->modifiedColumns[] = DataFileLinkPeer::PROJ_ID;
		}

		if ($this->aProject !== null && $this->aProject->getId() !== $v) {
			$this->aProject = null;
		}

	} // setProjectId()

	/**
	 * Set the value of [exp_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setExperimentId($v)
	{

		if ($this->exp_id !== $v) {
			$this->exp_id = $v;
			$this->modifiedColumns[] = DataFileLinkPeer::EXP_ID;
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
			$this->modifiedColumns[] = DataFileLinkPeer::TRIAL_ID;
		}

		if ($this->aTrial !== null && $this->aTrial->getId() !== $v) {
			$this->aTrial = null;
		}

	} // setTrialId()

	/**
	 * Set the value of [rep_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRepId($v)
	{

		if ($this->rep_id !== $v) {
			$this->rep_id = $v;
			$this->modifiedColumns[] = DataFileLinkPeer::REP_ID;
		}

		if ($this->aRepetition !== null && $this->aRepetition->getId() !== $v) {
			$this->aRepetition = null;
		}

	} // setRepId()

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
			$this->modifiedColumns[] = DataFileLinkPeer::DELETED;
		}

	} // setDeleted()

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

			$this->proj_id = $rs->getFloat($startcol + 1);

			$this->exp_id = $rs->getFloat($startcol + 2);

			$this->trial_id = $rs->getFloat($startcol + 3);

			$this->rep_id = $rs->getFloat($startcol + 4);

			$this->deleted = $rs->getFloat($startcol + 5);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = DataFileLinkPeer::NUM_COLUMNS - DataFileLinkPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating DataFileLink object", $e);
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
			$con = Propel::getConnection(DataFileLinkPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			DataFileLinkPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(DataFileLinkPeer::DATABASE_NAME);
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

			if ($this->aProject !== null) {
				if ($this->aProject->isModified()) {
					$affectedRows += $this->aProject->save($con);
				}
				$this->setProject($this->aProject);
			}

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

			if ($this->aRepetition !== null) {
				if ($this->aRepetition->isModified()) {
					$affectedRows += $this->aRepetition->save($con);
				}
				$this->setRepetition($this->aRepetition);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = DataFileLinkPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += DataFileLinkPeer::doUpdate($this, $con);
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

			if ($this->aProject !== null) {
				if (!$this->aProject->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aProject->getValidationFailures());
				}
			}

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

			if ($this->aRepetition !== null) {
				if (!$this->aRepetition->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aRepetition->getValidationFailures());
				}
			}


			if (($retval = DataFileLinkPeer::doValidate($this, $columns)) !== true) {
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
		$pos = DataFileLinkPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getProjectId();
				break;
			case 2:
				return $this->getExperimentId();
				break;
			case 3:
				return $this->getTrialId();
				break;
			case 4:
				return $this->getRepId();
				break;
			case 5:
				return $this->getDeleted();
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
		$keys = DataFileLinkPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getProjectId(),
			$keys[2] => $this->getExperimentId(),
			$keys[3] => $this->getTrialId(),
			$keys[4] => $this->getRepId(),
			$keys[5] => $this->getDeleted(),
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
		$pos = DataFileLinkPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setProjectId($value);
				break;
			case 2:
				$this->setExperimentId($value);
				break;
			case 3:
				$this->setTrialId($value);
				break;
			case 4:
				$this->setRepId($value);
				break;
			case 5:
				$this->setDeleted($value);
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
		$keys = DataFileLinkPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setProjectId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setExperimentId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setTrialId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setRepId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDeleted($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(DataFileLinkPeer::DATABASE_NAME);

		if ($this->isColumnModified(DataFileLinkPeer::ID)) $criteria->add(DataFileLinkPeer::ID, $this->id);
		if ($this->isColumnModified(DataFileLinkPeer::PROJ_ID)) $criteria->add(DataFileLinkPeer::PROJ_ID, $this->proj_id);
		if ($this->isColumnModified(DataFileLinkPeer::EXP_ID)) $criteria->add(DataFileLinkPeer::EXP_ID, $this->exp_id);
		if ($this->isColumnModified(DataFileLinkPeer::TRIAL_ID)) $criteria->add(DataFileLinkPeer::TRIAL_ID, $this->trial_id);
		if ($this->isColumnModified(DataFileLinkPeer::REP_ID)) $criteria->add(DataFileLinkPeer::REP_ID, $this->rep_id);
		if ($this->isColumnModified(DataFileLinkPeer::DELETED)) $criteria->add(DataFileLinkPeer::DELETED, $this->deleted);

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
		$criteria = new Criteria(DataFileLinkPeer::DATABASE_NAME);

		$criteria->add(DataFileLinkPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of DataFileLink (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setProjectId($this->proj_id);

		$copyObj->setExperimentId($this->exp_id);

		$copyObj->setTrialId($this->trial_id);

		$copyObj->setRepId($this->rep_id);

		$copyObj->setDeleted($this->deleted);


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
	 * @return     DataFileLink Clone of current object.
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
	 * @return     DataFileLinkPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new DataFileLinkPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Project object.
	 *
	 * @param      Project $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setProject($v)
	{


		if ($v === null) {
			$this->setProjectId(NULL);
		} else {
			$this->setProjectId($v->getId());
		}


		$this->aProject = $v;
	}


	/**
	 * Get the associated Project object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Project The associated Project object.
	 * @throws     PropelException
	 */
	public function getProject($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseProjectPeer.php';

		if ($this->aProject === null && ($this->proj_id > 0)) {

			$this->aProject = ProjectPeer::retrieveByPK($this->proj_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ProjectPeer::retrieveByPK($this->proj_id, $con);
			   $obj->addProjects($this);
			 */
		}
		return $this->aProject;
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

		if ($this->aExperiment === null && ($this->exp_id > 0)) {

			$this->aExperiment = ExperimentPeer::retrieveByPK($this->exp_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ExperimentPeer::retrieveByPK($this->exp_id, $con);
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
	 * Declares an association between this object and a Repetition object.
	 *
	 * @param      Repetition $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setRepetition($v)
	{


		if ($v === null) {
			$this->setRepId(NULL);
		} else {
			$this->setRepId($v->getId());
		}


		$this->aRepetition = $v;
	}


	/**
	 * Get the associated Repetition object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Repetition The associated Repetition object.
	 * @throws     PropelException
	 */
	public function getRepetition($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseRepetitionPeer.php';

		if ($this->aRepetition === null && ($this->rep_id > 0)) {

			$this->aRepetition = RepetitionPeer::retrieveByPK($this->rep_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = RepetitionPeer::retrieveByPK($this->rep_id, $con);
			   $obj->addRepetitions($this);
			 */
		}
		return $this->aRepetition;
	}

} // BaseDataFileLink
