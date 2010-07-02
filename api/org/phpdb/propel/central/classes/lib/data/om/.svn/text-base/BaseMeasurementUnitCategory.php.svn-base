<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/MeasurementUnitCategoryPeer.php';

/**
 * Base class that represents a row from the 'MEASUREMENT_UNIT_CATEGORY' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseMeasurementUnitCategory extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        MeasurementUnitCategoryPeer
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
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * Collection to store aggregation of collCoordinateDimensions.
	 * @var        array
	 */
	protected $collCoordinateDimensions;

	/**
	 * The criteria used to select the current contents of collCoordinateDimensions.
	 * @var        Criteria
	 */
	protected $lastCoordinateDimensionCriteria = null;

	/**
	 * Collection to store aggregation of collExperimentMeasurements.
	 * @var        array
	 */
	protected $collExperimentMeasurements;

	/**
	 * The criteria used to select the current contents of collExperimentMeasurements.
	 * @var        Criteria
	 */
	protected $lastExperimentMeasurementCriteria = null;

	/**
	 * Collection to store aggregation of collMaterialTypePropertys.
	 * @var        array
	 */
	protected $collMaterialTypePropertys;

	/**
	 * The criteria used to select the current contents of collMaterialTypePropertys.
	 * @var        Criteria
	 */
	protected $lastMaterialTypePropertyCriteria = null;

	/**
	 * Collection to store aggregation of collMeasurementUnits.
	 * @var        array
	 */
	protected $collMeasurementUnits;

	/**
	 * The criteria used to select the current contents of collMeasurementUnits.
	 * @var        Criteria
	 */
	protected $lastMeasurementUnitCriteria = null;

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
			$this->modifiedColumns[] = MeasurementUnitCategoryPeer::ID;
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
			$this->modifiedColumns[] = MeasurementUnitCategoryPeer::COMMENTS;
		}

	} // setComment()

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
			$this->modifiedColumns[] = MeasurementUnitCategoryPeer::NAME;
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

			$this->comments = $rs->getClob($startcol + 1);

			$this->name = $rs->getString($startcol + 2);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 3; // 3 = MeasurementUnitCategoryPeer::NUM_COLUMNS - MeasurementUnitCategoryPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating MeasurementUnitCategory object", $e);
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
			$con = Propel::getConnection(MeasurementUnitCategoryPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			MeasurementUnitCategoryPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(MeasurementUnitCategoryPeer::DATABASE_NAME);
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
					$pk = MeasurementUnitCategoryPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += MeasurementUnitCategoryPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCoordinateDimensions !== null) {
				foreach($this->collCoordinateDimensions as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collExperimentMeasurements !== null) {
				foreach($this->collExperimentMeasurements as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collMaterialTypePropertys !== null) {
				foreach($this->collMaterialTypePropertys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collMeasurementUnits !== null) {
				foreach($this->collMeasurementUnits as $referrerFK) {
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


			if (($retval = MeasurementUnitCategoryPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCoordinateDimensions !== null) {
					foreach($this->collCoordinateDimensions as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collExperimentMeasurements !== null) {
					foreach($this->collExperimentMeasurements as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collMaterialTypePropertys !== null) {
					foreach($this->collMaterialTypePropertys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collMeasurementUnits !== null) {
					foreach($this->collMeasurementUnits as $referrerFK) {
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
		$pos = MeasurementUnitCategoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
		$keys = MeasurementUnitCategoryPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getComment(),
			$keys[2] => $this->getName(),
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
		$pos = MeasurementUnitCategoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
		$keys = MeasurementUnitCategoryPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setComment($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(MeasurementUnitCategoryPeer::DATABASE_NAME);

		if ($this->isColumnModified(MeasurementUnitCategoryPeer::ID)) $criteria->add(MeasurementUnitCategoryPeer::ID, $this->id);
		if ($this->isColumnModified(MeasurementUnitCategoryPeer::COMMENTS)) $criteria->add(MeasurementUnitCategoryPeer::COMMENTS, $this->comments);
		if ($this->isColumnModified(MeasurementUnitCategoryPeer::NAME)) $criteria->add(MeasurementUnitCategoryPeer::NAME, $this->name);

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
		$criteria = new Criteria(MeasurementUnitCategoryPeer::DATABASE_NAME);

		$criteria->add(MeasurementUnitCategoryPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of MeasurementUnitCategory (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setComment($this->comments);

		$copyObj->setName($this->name);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getCoordinateDimensions() as $relObj) {
				$copyObj->addCoordinateDimension($relObj->copy($deepCopy));
			}

			foreach($this->getExperimentMeasurements() as $relObj) {
				$copyObj->addExperimentMeasurement($relObj->copy($deepCopy));
			}

			foreach($this->getMaterialTypePropertys() as $relObj) {
				$copyObj->addMaterialTypeProperty($relObj->copy($deepCopy));
			}

			foreach($this->getMeasurementUnits() as $relObj) {
				$copyObj->addMeasurementUnit($relObj->copy($deepCopy));
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
	 * @return     MeasurementUnitCategory Clone of current object.
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
	 * @return     MeasurementUnitCategoryPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new MeasurementUnitCategoryPeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collCoordinateDimensions to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateDimensions()
	{
		if ($this->collCoordinateDimensions === null) {
			$this->collCoordinateDimensions = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnitCategory has previously
	 * been saved, it will retrieve related CoordinateDimensions from storage.
	 * If this MeasurementUnitCategory is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateDimensions($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateDimensionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateDimensions === null) {
			if ($this->isNew()) {
			   $this->collCoordinateDimensions = array();
			} else {

				$criteria->add(CoordinateDimensionPeer::UNIT_CATEGORY_ID, $this->getId());

				CoordinateDimensionPeer::addSelectColumns($criteria);
				$this->collCoordinateDimensions = CoordinateDimensionPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateDimensionPeer::UNIT_CATEGORY_ID, $this->getId());

				CoordinateDimensionPeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateDimensionCriteria) || !$this->lastCoordinateDimensionCriteria->equals($criteria)) {
					$this->collCoordinateDimensions = CoordinateDimensionPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateDimensionCriteria = $criteria;
		return $this->collCoordinateDimensions;
	}

	/**
	 * Returns the number of related CoordinateDimensions.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateDimensions($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateDimensionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(CoordinateDimensionPeer::UNIT_CATEGORY_ID, $this->getId());

		return CoordinateDimensionPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a CoordinateDimension object to this object
	 * through the CoordinateDimension foreign key attribute
	 *
	 * @param      CoordinateDimension $l CoordinateDimension
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCoordinateDimension(CoordinateDimension $l)
	{
		$this->collCoordinateDimensions[] = $l;
		$l->setMeasurementUnitCategory($this);
	}

	/**
	 * Temporary storage of collExperimentMeasurements to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initExperimentMeasurements()
	{
		if ($this->collExperimentMeasurements === null) {
			$this->collExperimentMeasurements = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnitCategory has previously
	 * been saved, it will retrieve related ExperimentMeasurements from storage.
	 * If this MeasurementUnitCategory is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getExperimentMeasurements($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentMeasurementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentMeasurements === null) {
			if ($this->isNew()) {
			   $this->collExperimentMeasurements = array();
			} else {

				$criteria->add(ExperimentMeasurementPeer::CATEGORY, $this->getId());

				ExperimentMeasurementPeer::addSelectColumns($criteria);
				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentMeasurementPeer::CATEGORY, $this->getId());

				ExperimentMeasurementPeer::addSelectColumns($criteria);
				if (!isset($this->lastExperimentMeasurementCriteria) || !$this->lastExperimentMeasurementCriteria->equals($criteria)) {
					$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastExperimentMeasurementCriteria = $criteria;
		return $this->collExperimentMeasurements;
	}

	/**
	 * Returns the number of related ExperimentMeasurements.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countExperimentMeasurements($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentMeasurementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ExperimentMeasurementPeer::CATEGORY, $this->getId());

		return ExperimentMeasurementPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ExperimentMeasurement object to this object
	 * through the ExperimentMeasurement foreign key attribute
	 *
	 * @param      ExperimentMeasurement $l ExperimentMeasurement
	 * @return     void
	 * @throws     PropelException
	 */
	public function addExperimentMeasurement(ExperimentMeasurement $l)
	{
		$this->collExperimentMeasurements[] = $l;
		$l->setMeasurementUnitCategory($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnitCategory is new, it will return
	 * an empty collection; or if this MeasurementUnitCategory has previously
	 * been saved, it will retrieve related ExperimentMeasurements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnitCategory.
	 */
	public function getExperimentMeasurementsJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentMeasurementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentMeasurements === null) {
			if ($this->isNew()) {
				$this->collExperimentMeasurements = array();
			} else {

				$criteria->add(ExperimentMeasurementPeer::CATEGORY, $this->getId());

				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentMeasurementPeer::CATEGORY, $this->getId());

			if (!isset($this->lastExperimentMeasurementCriteria) || !$this->lastExperimentMeasurementCriteria->equals($criteria)) {
				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastExperimentMeasurementCriteria = $criteria;

		return $this->collExperimentMeasurements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnitCategory is new, it will return
	 * an empty collection; or if this MeasurementUnitCategory has previously
	 * been saved, it will retrieve related ExperimentMeasurements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnitCategory.
	 */
	public function getExperimentMeasurementsJoinMeasurementUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentMeasurementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentMeasurements === null) {
			if ($this->isNew()) {
				$this->collExperimentMeasurements = array();
			} else {

				$criteria->add(ExperimentMeasurementPeer::CATEGORY, $this->getId());

				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentMeasurementPeer::CATEGORY, $this->getId());

			if (!isset($this->lastExperimentMeasurementCriteria) || !$this->lastExperimentMeasurementCriteria->equals($criteria)) {
				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastExperimentMeasurementCriteria = $criteria;

		return $this->collExperimentMeasurements;
	}

	/**
	 * Temporary storage of collMaterialTypePropertys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initMaterialTypePropertys()
	{
		if ($this->collMaterialTypePropertys === null) {
			$this->collMaterialTypePropertys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnitCategory has previously
	 * been saved, it will retrieve related MaterialTypePropertys from storage.
	 * If this MeasurementUnitCategory is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getMaterialTypePropertys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialTypePropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialTypePropertys === null) {
			if ($this->isNew()) {
			   $this->collMaterialTypePropertys = array();
			} else {

				$criteria->add(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, $this->getId());

				MaterialTypePropertyPeer::addSelectColumns($criteria);
				$this->collMaterialTypePropertys = MaterialTypePropertyPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, $this->getId());

				MaterialTypePropertyPeer::addSelectColumns($criteria);
				if (!isset($this->lastMaterialTypePropertyCriteria) || !$this->lastMaterialTypePropertyCriteria->equals($criteria)) {
					$this->collMaterialTypePropertys = MaterialTypePropertyPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMaterialTypePropertyCriteria = $criteria;
		return $this->collMaterialTypePropertys;
	}

	/**
	 * Returns the number of related MaterialTypePropertys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countMaterialTypePropertys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialTypePropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, $this->getId());

		return MaterialTypePropertyPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a MaterialTypeProperty object to this object
	 * through the MaterialTypeProperty foreign key attribute
	 *
	 * @param      MaterialTypeProperty $l MaterialTypeProperty
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMaterialTypeProperty(MaterialTypeProperty $l)
	{
		$this->collMaterialTypePropertys[] = $l;
		$l->setMeasurementUnitCategory($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnitCategory is new, it will return
	 * an empty collection; or if this MeasurementUnitCategory has previously
	 * been saved, it will retrieve related MaterialTypePropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnitCategory.
	 */
	public function getMaterialTypePropertysJoinMaterialType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialTypePropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialTypePropertys === null) {
			if ($this->isNew()) {
				$this->collMaterialTypePropertys = array();
			} else {

				$criteria->add(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, $this->getId());

				$this->collMaterialTypePropertys = MaterialTypePropertyPeer::doSelectJoinMaterialType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, $this->getId());

			if (!isset($this->lastMaterialTypePropertyCriteria) || !$this->lastMaterialTypePropertyCriteria->equals($criteria)) {
				$this->collMaterialTypePropertys = MaterialTypePropertyPeer::doSelectJoinMaterialType($criteria, $con);
			}
		}
		$this->lastMaterialTypePropertyCriteria = $criteria;

		return $this->collMaterialTypePropertys;
	}

	/**
	 * Temporary storage of collMeasurementUnits to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initMeasurementUnits()
	{
		if ($this->collMeasurementUnits === null) {
			$this->collMeasurementUnits = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnitCategory has previously
	 * been saved, it will retrieve related MeasurementUnits from storage.
	 * If this MeasurementUnitCategory is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getMeasurementUnits($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMeasurementUnits === null) {
			if ($this->isNew()) {
			   $this->collMeasurementUnits = array();
			} else {

				$criteria->add(MeasurementUnitPeer::CATEGORY, $this->getId());

				MeasurementUnitPeer::addSelectColumns($criteria);
				$this->collMeasurementUnits = MeasurementUnitPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MeasurementUnitPeer::CATEGORY, $this->getId());

				MeasurementUnitPeer::addSelectColumns($criteria);
				if (!isset($this->lastMeasurementUnitCriteria) || !$this->lastMeasurementUnitCriteria->equals($criteria)) {
					$this->collMeasurementUnits = MeasurementUnitPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMeasurementUnitCriteria = $criteria;
		return $this->collMeasurementUnits;
	}

	/**
	 * Returns the number of related MeasurementUnits.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countMeasurementUnits($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(MeasurementUnitPeer::CATEGORY, $this->getId());

		return MeasurementUnitPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a MeasurementUnit object to this object
	 * through the MeasurementUnit foreign key attribute
	 *
	 * @param      MeasurementUnit $l MeasurementUnit
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMeasurementUnit(MeasurementUnit $l)
	{
		$this->collMeasurementUnits[] = $l;
		$l->setMeasurementUnitCategory($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnitCategory is new, it will return
	 * an empty collection; or if this MeasurementUnitCategory has previously
	 * been saved, it will retrieve related MeasurementUnits from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnitCategory.
	 */
	public function getMeasurementUnitsJoinMeasurementUnitRelatedByBaseUnitId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMeasurementUnits === null) {
			if ($this->isNew()) {
				$this->collMeasurementUnits = array();
			} else {

				$criteria->add(MeasurementUnitPeer::CATEGORY, $this->getId());

				$this->collMeasurementUnits = MeasurementUnitPeer::doSelectJoinMeasurementUnitRelatedByBaseUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MeasurementUnitPeer::CATEGORY, $this->getId());

			if (!isset($this->lastMeasurementUnitCriteria) || !$this->lastMeasurementUnitCriteria->equals($criteria)) {
				$this->collMeasurementUnits = MeasurementUnitPeer::doSelectJoinMeasurementUnitRelatedByBaseUnitId($criteria, $con);
			}
		}
		$this->lastMeasurementUnitCriteria = $criteria;

		return $this->collMeasurementUnits;
	}

} // BaseMeasurementUnitCategory
