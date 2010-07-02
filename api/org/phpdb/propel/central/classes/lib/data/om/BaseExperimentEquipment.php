<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/ExperimentEquipmentPeer.php';

/**
 * Base class that represents a row from the 'EXPERIMENT_EQUIPMENT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseExperimentEquipment extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ExperimentEquipmentPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the comments field.
	 * @var        string
	 */
	protected $comments;


	/**
	 * The value for the equipment_id field.
	 * @var        double
	 */
	protected $equipment_id;


	/**
	 * The value for the experiment_id field.
	 * @var        double
	 */
	protected $experiment_id;

	/**
	 * @var        Equipment
	 */
	protected $aEquipment;

	/**
	 * @var        Experiment
	 */
	protected $aExperiment;

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
	 * Get the [comments] column value.
	 * 
	 * @return     string
	 */
	public function getComment()
	{

		return $this->comments;
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
	 * Get the [experiment_id] column value.
	 * 
	 * @return     double
	 */
	public function getExperimentId()
	{

		return $this->experiment_id;
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
			$this->modifiedColumns[] = ExperimentEquipmentPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [comments] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setComment($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->comments) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->comments !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->comments = $obj;
			$this->modifiedColumns[] = ExperimentEquipmentPeer::COMMENTS;
		}

	} // setComment()

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
			$this->modifiedColumns[] = ExperimentEquipmentPeer::EQUIPMENT_ID;
		}

		if ($this->aEquipment !== null && $this->aEquipment->getId() !== $v) {
			$this->aEquipment = null;
		}

	} // setEquipmentId()

	/**
	 * Set the value of [experiment_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setExperimentId($v)
	{

		if ($this->experiment_id !== $v) {
			$this->experiment_id = $v;
			$this->modifiedColumns[] = ExperimentEquipmentPeer::EXPERIMENT_ID;
		}

		if ($this->aExperiment !== null && $this->aExperiment->getId() !== $v) {
			$this->aExperiment = null;
		}

	} // setExperimentId()

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

			$this->comments = $rs->getClob($startcol + 1);

			$this->equipment_id = $rs->getFloat($startcol + 2);

			$this->experiment_id = $rs->getFloat($startcol + 3);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 4; // 4 = ExperimentEquipmentPeer::NUM_COLUMNS - ExperimentEquipmentPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating ExperimentEquipment object", $e);
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
			$con = Propel::getConnection(ExperimentEquipmentPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			ExperimentEquipmentPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ExperimentEquipmentPeer::DATABASE_NAME);
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

			if ($this->aEquipment !== null) {
				if ($this->aEquipment->isModified()) {
					$affectedRows += $this->aEquipment->save($con);
				}
				$this->setEquipment($this->aEquipment);
			}

			if ($this->aExperiment !== null) {
				if ($this->aExperiment->isModified()) {
					$affectedRows += $this->aExperiment->save($con);
				}
				$this->setExperiment($this->aExperiment);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = ExperimentEquipmentPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += ExperimentEquipmentPeer::doUpdate($this, $con);
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

			if ($this->aEquipment !== null) {
				if (!$this->aEquipment->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEquipment->getValidationFailures());
				}
			}

			if ($this->aExperiment !== null) {
				if (!$this->aExperiment->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aExperiment->getValidationFailures());
				}
			}


			if (($retval = ExperimentEquipmentPeer::doValidate($this, $columns)) !== true) {
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
		$pos = ExperimentEquipmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getComment();
				break;
			case 2:
				return $this->getEquipmentId();
				break;
			case 3:
				return $this->getExperimentId();
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
		$keys = ExperimentEquipmentPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getComment(),
			$keys[2] => $this->getEquipmentId(),
			$keys[3] => $this->getExperimentId(),
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
		$pos = ExperimentEquipmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setComment($value);
				break;
			case 2:
				$this->setEquipmentId($value);
				break;
			case 3:
				$this->setExperimentId($value);
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
		$keys = ExperimentEquipmentPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setComment($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEquipmentId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setExperimentId($arr[$keys[3]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ExperimentEquipmentPeer::DATABASE_NAME);

		if ($this->isColumnModified(ExperimentEquipmentPeer::ID)) $criteria->add(ExperimentEquipmentPeer::ID, $this->id);
		if ($this->isColumnModified(ExperimentEquipmentPeer::COMMENTS)) $criteria->add(ExperimentEquipmentPeer::COMMENTS, $this->comments);
		if ($this->isColumnModified(ExperimentEquipmentPeer::EQUIPMENT_ID)) $criteria->add(ExperimentEquipmentPeer::EQUIPMENT_ID, $this->equipment_id);
		if ($this->isColumnModified(ExperimentEquipmentPeer::EXPERIMENT_ID)) $criteria->add(ExperimentEquipmentPeer::EXPERIMENT_ID, $this->experiment_id);

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
		$criteria = new Criteria(ExperimentEquipmentPeer::DATABASE_NAME);

		$criteria->add(ExperimentEquipmentPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of ExperimentEquipment (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setComment($this->comments);

		$copyObj->setEquipmentId($this->equipment_id);

		$copyObj->setExperimentId($this->experiment_id);


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
	 * @return     ExperimentEquipment Clone of current object.
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
	 * @return     ExperimentEquipmentPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ExperimentEquipmentPeer();
		}
		return self::$peer;
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

		if ($this->aExperiment === null && ($this->experiment_id > 0)) {

			$this->aExperiment = ExperimentPeer::retrieveByPK($this->experiment_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ExperimentPeer::retrieveByPK($this->experiment_id, $con);
			   $obj->addExperiments($this);
			 */
		}
		return $this->aExperiment;
	}

} // BaseExperimentEquipment
