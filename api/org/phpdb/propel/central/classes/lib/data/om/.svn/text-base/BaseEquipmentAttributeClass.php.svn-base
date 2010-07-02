<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/EquipmentAttributeClassPeer.php';

/**
 * Base class that represents a row from the 'EQUIPMENT_ATTRIBUTE_CLASS' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEquipmentAttributeClass extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EquipmentAttributeClassPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the equipment_attribute_id field.
	 * @var        double
	 */
	protected $equipment_attribute_id;


	/**
	 * The value for the equipment_class_id field.
	 * @var        double
	 */
	protected $equipment_class_id;

	/**
	 * @var        EquipmentAttribute
	 */
	protected $aEquipmentAttribute;

	/**
	 * @var        EquipmentClass
	 */
	protected $aEquipmentClass;

	/**
	 * Collection to store aggregation of collEquipmentAttributeValues.
	 * @var        array
	 */
	protected $collEquipmentAttributeValues;

	/**
	 * The criteria used to select the current contents of collEquipmentAttributeValues.
	 * @var        Criteria
	 */
	protected $lastEquipmentAttributeValueCriteria = null;

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
	 * Get the [equipment_attribute_id] column value.
	 * 
	 * @return     double
	 */
	public function getEquipmentAttributeId()
	{

		return $this->equipment_attribute_id;
	}

	/**
	 * Get the [equipment_class_id] column value.
	 * 
	 * @return     double
	 */
	public function getEquipmentClassId()
	{

		return $this->equipment_class_id;
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
			$this->modifiedColumns[] = EquipmentAttributeClassPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [equipment_attribute_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEquipmentAttributeId($v)
	{

		if ($this->equipment_attribute_id !== $v) {
			$this->equipment_attribute_id = $v;
			$this->modifiedColumns[] = EquipmentAttributeClassPeer::EQUIPMENT_ATTRIBUTE_ID;
		}

		if ($this->aEquipmentAttribute !== null && $this->aEquipmentAttribute->getId() !== $v) {
			$this->aEquipmentAttribute = null;
		}

	} // setEquipmentAttributeId()

	/**
	 * Set the value of [equipment_class_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEquipmentClassId($v)
	{

		if ($this->equipment_class_id !== $v) {
			$this->equipment_class_id = $v;
			$this->modifiedColumns[] = EquipmentAttributeClassPeer::EQUIPMENT_CLASS_ID;
		}

		if ($this->aEquipmentClass !== null && $this->aEquipmentClass->getId() !== $v) {
			$this->aEquipmentClass = null;
		}

	} // setEquipmentClassId()

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

			$this->equipment_attribute_id = $rs->getFloat($startcol + 1);

			$this->equipment_class_id = $rs->getFloat($startcol + 2);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 3; // 3 = EquipmentAttributeClassPeer::NUM_COLUMNS - EquipmentAttributeClassPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating EquipmentAttributeClass object", $e);
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
			$con = Propel::getConnection(EquipmentAttributeClassPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			EquipmentAttributeClassPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(EquipmentAttributeClassPeer::DATABASE_NAME);
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

			if ($this->aEquipmentAttribute !== null) {
				if ($this->aEquipmentAttribute->isModified()) {
					$affectedRows += $this->aEquipmentAttribute->save($con);
				}
				$this->setEquipmentAttribute($this->aEquipmentAttribute);
			}

			if ($this->aEquipmentClass !== null) {
				if ($this->aEquipmentClass->isModified()) {
					$affectedRows += $this->aEquipmentClass->save($con);
				}
				$this->setEquipmentClass($this->aEquipmentClass);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = EquipmentAttributeClassPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += EquipmentAttributeClassPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collEquipmentAttributeValues !== null) {
				foreach($this->collEquipmentAttributeValues as $referrerFK) {
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

			if ($this->aEquipmentAttribute !== null) {
				if (!$this->aEquipmentAttribute->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEquipmentAttribute->getValidationFailures());
				}
			}

			if ($this->aEquipmentClass !== null) {
				if (!$this->aEquipmentClass->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEquipmentClass->getValidationFailures());
				}
			}


			if (($retval = EquipmentAttributeClassPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collEquipmentAttributeValues !== null) {
					foreach($this->collEquipmentAttributeValues as $referrerFK) {
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
		$pos = EquipmentAttributeClassPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getEquipmentAttributeId();
				break;
			case 2:
				return $this->getEquipmentClassId();
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
		$keys = EquipmentAttributeClassPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getEquipmentAttributeId(),
			$keys[2] => $this->getEquipmentClassId(),
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
		$pos = EquipmentAttributeClassPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setEquipmentAttributeId($value);
				break;
			case 2:
				$this->setEquipmentClassId($value);
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
		$keys = EquipmentAttributeClassPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setEquipmentAttributeId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEquipmentClassId($arr[$keys[2]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EquipmentAttributeClassPeer::DATABASE_NAME);

		if ($this->isColumnModified(EquipmentAttributeClassPeer::ID)) $criteria->add(EquipmentAttributeClassPeer::ID, $this->id);
		if ($this->isColumnModified(EquipmentAttributeClassPeer::EQUIPMENT_ATTRIBUTE_ID)) $criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_ATTRIBUTE_ID, $this->equipment_attribute_id);
		if ($this->isColumnModified(EquipmentAttributeClassPeer::EQUIPMENT_CLASS_ID)) $criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_CLASS_ID, $this->equipment_class_id);

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
		$criteria = new Criteria(EquipmentAttributeClassPeer::DATABASE_NAME);

		$criteria->add(EquipmentAttributeClassPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of EquipmentAttributeClass (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setEquipmentAttributeId($this->equipment_attribute_id);

		$copyObj->setEquipmentClassId($this->equipment_class_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getEquipmentAttributeValues() as $relObj) {
				$copyObj->addEquipmentAttributeValue($relObj->copy($deepCopy));
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
	 * @return     EquipmentAttributeClass Clone of current object.
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
	 * @return     EquipmentAttributeClassPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EquipmentAttributeClassPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a EquipmentAttribute object.
	 *
	 * @param      EquipmentAttribute $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEquipmentAttribute($v)
	{


		if ($v === null) {
			$this->setEquipmentAttributeId(NULL);
		} else {
			$this->setEquipmentAttributeId($v->getId());
		}


		$this->aEquipmentAttribute = $v;
	}


	/**
	 * Get the associated EquipmentAttribute object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     EquipmentAttribute The associated EquipmentAttribute object.
	 * @throws     PropelException
	 */
	public function getEquipmentAttribute($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEquipmentAttributePeer.php';

		if ($this->aEquipmentAttribute === null && ($this->equipment_attribute_id > 0)) {

			$this->aEquipmentAttribute = EquipmentAttributePeer::retrieveByPK($this->equipment_attribute_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EquipmentAttributePeer::retrieveByPK($this->equipment_attribute_id, $con);
			   $obj->addEquipmentAttributes($this);
			 */
		}
		return $this->aEquipmentAttribute;
	}

	/**
	 * Declares an association between this object and a EquipmentClass object.
	 *
	 * @param      EquipmentClass $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEquipmentClass($v)
	{


		if ($v === null) {
			$this->setEquipmentClassId(NULL);
		} else {
			$this->setEquipmentClassId($v->getId());
		}


		$this->aEquipmentClass = $v;
	}


	/**
	 * Get the associated EquipmentClass object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     EquipmentClass The associated EquipmentClass object.
	 * @throws     PropelException
	 */
	public function getEquipmentClass($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEquipmentClassPeer.php';

		if ($this->aEquipmentClass === null && ($this->equipment_class_id > 0)) {

			$this->aEquipmentClass = EquipmentClassPeer::retrieveByPK($this->equipment_class_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EquipmentClassPeer::retrieveByPK($this->equipment_class_id, $con);
			   $obj->addEquipmentClasss($this);
			 */
		}
		return $this->aEquipmentClass;
	}

	/**
	 * Temporary storage of collEquipmentAttributeValues to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentAttributeValues()
	{
		if ($this->collEquipmentAttributeValues === null) {
			$this->collEquipmentAttributeValues = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttributeClass has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 * If this EquipmentAttributeClass is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentAttributeValues($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
			   $this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, $this->getId());

				EquipmentAttributeValuePeer::addSelectColumns($criteria);
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, $this->getId());

				EquipmentAttributeValuePeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
					$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;
		return $this->collEquipmentAttributeValues;
	}

	/**
	 * Returns the number of related EquipmentAttributeValues.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentAttributeValues($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, $this->getId());

		return EquipmentAttributeValuePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentAttributeValue object to this object
	 * through the EquipmentAttributeValue foreign key attribute
	 *
	 * @param      EquipmentAttributeValue $l EquipmentAttributeValue
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentAttributeValue(EquipmentAttributeValue $l)
	{
		$this->collEquipmentAttributeValues[] = $l;
		$l->setEquipmentAttributeClass($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttributeClass is new, it will return
	 * an empty collection; or if this EquipmentAttributeClass has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentAttributeClass.
	 */
	public function getEquipmentAttributeValuesJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttributeClass is new, it will return
	 * an empty collection; or if this EquipmentAttributeClass has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentAttributeClass.
	 */
	public function getEquipmentAttributeValuesJoinEquipmentAttribute($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttribute($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttribute($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentAttributeClass is new, it will return
	 * an empty collection; or if this EquipmentAttributeClass has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentAttributeClass.
	 */
	public function getEquipmentAttributeValuesJoinUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinUnit($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
	}

} // BaseEquipmentAttributeClass
