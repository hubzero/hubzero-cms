<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SpecimenComponentExperimentPeer.php';

/**
 * Base class that represents a row from the 'SPECCOMP_EXPERIMENT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSpecimenComponentExperiment extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SpecimenComponentExperimentPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the specimen_component_id field.
	 * @var        double
	 */
	protected $specimen_component_id;


	/**
	 * The value for the expid field.
	 * @var        double
	 */
	protected $expid;


	/**
	 * The value for the coord_run_exp_id field.
	 * @var        double
	 */
	protected $coord_run_exp_id;

	/**
	 * @var        SpecimenComponent
	 */
	protected $aSpecimenComponent;

	/**
	 * @var        Experiment
	 */
	protected $aExperiment;

	/**
	 * @var        CoordinatorRunExperiment
	 */
	protected $aCoordinatorRunExperiment;

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
	 * Get the [specimen_component_id] column value.
	 * 
	 * @return     double
	 */
	public function getSpecimenComponentId()
	{

		return $this->specimen_component_id;
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
	 * Get the [coord_run_exp_id] column value.
	 * 
	 * @return     double
	 */
	public function getCoordinatorRunExperimentId()
	{

		return $this->coord_run_exp_id;
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
			$this->modifiedColumns[] = SpecimenComponentExperimentPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [specimen_component_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSpecimenComponentId($v)
	{

		if ($this->specimen_component_id !== $v) {
			$this->specimen_component_id = $v;
			$this->modifiedColumns[] = SpecimenComponentExperimentPeer::SPECIMEN_COMPONENT_ID;
		}

		if ($this->aSpecimenComponent !== null && $this->aSpecimenComponent->getId() !== $v) {
			$this->aSpecimenComponent = null;
		}

	} // setSpecimenComponentId()

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
			$this->modifiedColumns[] = SpecimenComponentExperimentPeer::EXPID;
		}

		if ($this->aExperiment !== null && $this->aExperiment->getId() !== $v) {
			$this->aExperiment = null;
		}

	} // setExperimentId()

	/**
	 * Set the value of [coord_run_exp_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCoordinatorRunExperimentId($v)
	{

		if ($this->coord_run_exp_id !== $v) {
			$this->coord_run_exp_id = $v;
			$this->modifiedColumns[] = SpecimenComponentExperimentPeer::COORD_RUN_EXP_ID;
		}

		if ($this->aCoordinatorRunExperiment !== null && $this->aCoordinatorRunExperiment->getId() !== $v) {
			$this->aCoordinatorRunExperiment = null;
		}

	} // setCoordinatorRunExperimentId()

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

			$this->specimen_component_id = $rs->getFloat($startcol + 1);

			$this->expid = $rs->getFloat($startcol + 2);

			$this->coord_run_exp_id = $rs->getFloat($startcol + 3);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 4; // 4 = SpecimenComponentExperimentPeer::NUM_COLUMNS - SpecimenComponentExperimentPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SpecimenComponentExperiment object", $e);
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
			$con = Propel::getConnection(SpecimenComponentExperimentPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SpecimenComponentExperimentPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SpecimenComponentExperimentPeer::DATABASE_NAME);
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

			if ($this->aSpecimenComponent !== null) {
				if ($this->aSpecimenComponent->isModified()) {
					$affectedRows += $this->aSpecimenComponent->save($con);
				}
				$this->setSpecimenComponent($this->aSpecimenComponent);
			}

			if ($this->aExperiment !== null) {
				if ($this->aExperiment->isModified()) {
					$affectedRows += $this->aExperiment->save($con);
				}
				$this->setExperiment($this->aExperiment);
			}

			if ($this->aCoordinatorRunExperiment !== null) {
				if ($this->aCoordinatorRunExperiment->isModified()) {
					$affectedRows += $this->aCoordinatorRunExperiment->save($con);
				}
				$this->setCoordinatorRunExperiment($this->aCoordinatorRunExperiment);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SpecimenComponentExperimentPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SpecimenComponentExperimentPeer::doUpdate($this, $con);
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

			if ($this->aSpecimenComponent !== null) {
				if (!$this->aSpecimenComponent->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSpecimenComponent->getValidationFailures());
				}
			}

			if ($this->aExperiment !== null) {
				if (!$this->aExperiment->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aExperiment->getValidationFailures());
				}
			}

			if ($this->aCoordinatorRunExperiment !== null) {
				if (!$this->aCoordinatorRunExperiment->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCoordinatorRunExperiment->getValidationFailures());
				}
			}


			if (($retval = SpecimenComponentExperimentPeer::doValidate($this, $columns)) !== true) {
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
		$pos = SpecimenComponentExperimentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getSpecimenComponentId();
				break;
			case 2:
				return $this->getExperimentId();
				break;
			case 3:
				return $this->getCoordinatorRunExperimentId();
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
		$keys = SpecimenComponentExperimentPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getSpecimenComponentId(),
			$keys[2] => $this->getExperimentId(),
			$keys[3] => $this->getCoordinatorRunExperimentId(),
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
		$pos = SpecimenComponentExperimentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setSpecimenComponentId($value);
				break;
			case 2:
				$this->setExperimentId($value);
				break;
			case 3:
				$this->setCoordinatorRunExperimentId($value);
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
		$keys = SpecimenComponentExperimentPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setSpecimenComponentId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setExperimentId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCoordinatorRunExperimentId($arr[$keys[3]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SpecimenComponentExperimentPeer::DATABASE_NAME);

		if ($this->isColumnModified(SpecimenComponentExperimentPeer::ID)) $criteria->add(SpecimenComponentExperimentPeer::ID, $this->id);
		if ($this->isColumnModified(SpecimenComponentExperimentPeer::SPECIMEN_COMPONENT_ID)) $criteria->add(SpecimenComponentExperimentPeer::SPECIMEN_COMPONENT_ID, $this->specimen_component_id);
		if ($this->isColumnModified(SpecimenComponentExperimentPeer::EXPID)) $criteria->add(SpecimenComponentExperimentPeer::EXPID, $this->expid);
		if ($this->isColumnModified(SpecimenComponentExperimentPeer::COORD_RUN_EXP_ID)) $criteria->add(SpecimenComponentExperimentPeer::COORD_RUN_EXP_ID, $this->coord_run_exp_id);

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
		$criteria = new Criteria(SpecimenComponentExperimentPeer::DATABASE_NAME);

		$criteria->add(SpecimenComponentExperimentPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SpecimenComponentExperiment (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setSpecimenComponentId($this->specimen_component_id);

		$copyObj->setExperimentId($this->expid);

		$copyObj->setCoordinatorRunExperimentId($this->coord_run_exp_id);


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
	 * @return     SpecimenComponentExperiment Clone of current object.
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
	 * @return     SpecimenComponentExperimentPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SpecimenComponentExperimentPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a SpecimenComponent object.
	 *
	 * @param      SpecimenComponent $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSpecimenComponent($v)
	{


		if ($v === null) {
			$this->setSpecimenComponentId(NULL);
		} else {
			$this->setSpecimenComponentId($v->getId());
		}


		$this->aSpecimenComponent = $v;
	}


	/**
	 * Get the associated SpecimenComponent object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SpecimenComponent The associated SpecimenComponent object.
	 * @throws     PropelException
	 */
	public function getSpecimenComponent($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSpecimenComponentPeer.php';

		if ($this->aSpecimenComponent === null && ($this->specimen_component_id > 0)) {

			$this->aSpecimenComponent = SpecimenComponentPeer::retrieveByPK($this->specimen_component_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SpecimenComponentPeer::retrieveByPK($this->specimen_component_id, $con);
			   $obj->addSpecimenComponents($this);
			 */
		}
		return $this->aSpecimenComponent;
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
	 * Declares an association between this object and a CoordinatorRunExperiment object.
	 *
	 * @param      CoordinatorRunExperiment $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setCoordinatorRunExperiment($v)
	{


		if ($v === null) {
			$this->setCoordinatorRunExperimentId(NULL);
		} else {
			$this->setCoordinatorRunExperimentId($v->getId());
		}


		$this->aCoordinatorRunExperiment = $v;
	}


	/**
	 * Get the associated CoordinatorRunExperiment object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     CoordinatorRunExperiment The associated CoordinatorRunExperiment object.
	 * @throws     PropelException
	 */
	public function getCoordinatorRunExperiment($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseCoordinatorRunExperimentPeer.php';

		if ($this->aCoordinatorRunExperiment === null && ($this->coord_run_exp_id > 0)) {

			$this->aCoordinatorRunExperiment = CoordinatorRunExperimentPeer::retrieveByPK($this->coord_run_exp_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = CoordinatorRunExperimentPeer::retrieveByPK($this->coord_run_exp_id, $con);
			   $obj->addCoordinatorRunExperiments($this);
			 */
		}
		return $this->aCoordinatorRunExperiment;
	}

} // BaseSpecimenComponentExperiment
