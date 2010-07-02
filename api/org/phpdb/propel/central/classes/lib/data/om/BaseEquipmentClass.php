<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/EquipmentClassPeer.php';

/**
 * Base class that represents a row from the 'EQUIPMENT_CLASS' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEquipmentClass extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EquipmentClassPeer
	 */
	protected static $peer;


	/**
	 * The value for the equipment_class_id field.
	 * @var        double
	 */
	protected $equipment_class_id;


	/**
	 * The value for the category field.
	 * @var        string
	 */
	protected $category;


	/**
	 * The value for the class_name field.
	 * @var        string
	 */
	protected $class_name;


	/**
	 * The value for the deprecated field.
	 * @var        double
	 */
	protected $deprecated;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the major field.
	 * @var        double
	 */
	protected $major;


	/**
	 * The value for the spec_available field.
	 * @var        double
	 */
	protected $spec_available;

	/**
	 * Collection to store aggregation of collAttributes.
	 * @var        array
	 */
	protected $collAttributes;

	/**
	 * The criteria used to select the current contents of collAttributes.
	 * @var        Criteria
	 */
	protected $lastAttributeCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentAttributeClasss.
	 * @var        array
	 */
	protected $collEquipmentAttributeClasss;

	/**
	 * The criteria used to select the current contents of collEquipmentAttributeClasss.
	 * @var        Criteria
	 */
	protected $lastEquipmentAttributeClassCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentModels.
	 * @var        array
	 */
	protected $collEquipmentModels;

	/**
	 * The criteria used to select the current contents of collEquipmentModels.
	 * @var        Criteria
	 */
	protected $lastEquipmentModelCriteria = null;

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
	 * Get the [equipment_class_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->equipment_class_id;
	}

	/**
	 * Get the [category] column value.
	 * 
	 * @return     string
	 */
	public function getCategory()
	{

		return $this->category;
	}

	/**
	 * Get the [class_name] column value.
	 * 
	 * @return     string
	 */
	public function getClassName()
	{

		return $this->class_name;
	}

	/**
	 * Get the [deprecated] column value.
	 * 
	 * @return     double
	 */
	public function getDeprecated()
	{

		return $this->deprecated;
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
	 * Get the [major] column value.
	 * 
	 * @return     double
	 */
	public function getMajor()
	{

		return $this->major;
	}

	/**
	 * Get the [spec_available] column value.
	 * 
	 * @return     double
	 */
	public function getSpecAvailable()
	{

		return $this->spec_available;
	}

	/**
	 * Set the value of [equipment_class_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->equipment_class_id !== $v) {
			$this->equipment_class_id = $v;
			$this->modifiedColumns[] = EquipmentClassPeer::EQUIPMENT_CLASS_ID;
		}

	} // setId()

	/**
	 * Set the value of [category] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCategory($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->category !== $v) {
			$this->category = $v;
			$this->modifiedColumns[] = EquipmentClassPeer::CATEGORY;
		}

	} // setCategory()

	/**
	 * Set the value of [class_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setClassName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->class_name !== $v) {
			$this->class_name = $v;
			$this->modifiedColumns[] = EquipmentClassPeer::CLASS_NAME;
		}

	} // setClassName()

	/**
	 * Set the value of [deprecated] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDeprecated($v)
	{

		if ($this->deprecated !== $v) {
			$this->deprecated = $v;
			$this->modifiedColumns[] = EquipmentClassPeer::DEPRECATED;
		}

	} // setDeprecated()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDescription($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->description) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->description !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->description = $obj;
			$this->modifiedColumns[] = EquipmentClassPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [major] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMajor($v)
	{

		if ($this->major !== $v) {
			$this->major = $v;
			$this->modifiedColumns[] = EquipmentClassPeer::MAJOR;
		}

	} // setMajor()

	/**
	 * Set the value of [spec_available] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSpecAvailable($v)
	{

		if ($this->spec_available !== $v) {
			$this->spec_available = $v;
			$this->modifiedColumns[] = EquipmentClassPeer::SPEC_AVAILABLE;
		}

	} // setSpecAvailable()

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

			$this->equipment_class_id = $rs->getFloat($startcol + 0);

			$this->category = $rs->getString($startcol + 1);

			$this->class_name = $rs->getString($startcol + 2);

			$this->deprecated = $rs->getFloat($startcol + 3);

			$this->description = $rs->getClob($startcol + 4);

			$this->major = $rs->getFloat($startcol + 5);

			$this->spec_available = $rs->getFloat($startcol + 6);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 7; // 7 = EquipmentClassPeer::NUM_COLUMNS - EquipmentClassPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating EquipmentClass object", $e);
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
			$con = Propel::getConnection(EquipmentClassPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			EquipmentClassPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(EquipmentClassPeer::DATABASE_NAME);
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
					$pk = EquipmentClassPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += EquipmentClassPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collAttributes !== null) {
				foreach($this->collAttributes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentAttributeClasss !== null) {
				foreach($this->collEquipmentAttributeClasss as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentModels !== null) {
				foreach($this->collEquipmentModels as $referrerFK) {
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


			if (($retval = EquipmentClassPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAttributes !== null) {
					foreach($this->collAttributes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentAttributeClasss !== null) {
					foreach($this->collEquipmentAttributeClasss as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentModels !== null) {
					foreach($this->collEquipmentModels as $referrerFK) {
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
		$pos = EquipmentClassPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCategory();
				break;
			case 2:
				return $this->getClassName();
				break;
			case 3:
				return $this->getDeprecated();
				break;
			case 4:
				return $this->getDescription();
				break;
			case 5:
				return $this->getMajor();
				break;
			case 6:
				return $this->getSpecAvailable();
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
		$keys = EquipmentClassPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCategory(),
			$keys[2] => $this->getClassName(),
			$keys[3] => $this->getDeprecated(),
			$keys[4] => $this->getDescription(),
			$keys[5] => $this->getMajor(),
			$keys[6] => $this->getSpecAvailable(),
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
		$pos = EquipmentClassPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCategory($value);
				break;
			case 2:
				$this->setClassName($value);
				break;
			case 3:
				$this->setDeprecated($value);
				break;
			case 4:
				$this->setDescription($value);
				break;
			case 5:
				$this->setMajor($value);
				break;
			case 6:
				$this->setSpecAvailable($value);
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
		$keys = EquipmentClassPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCategory($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setClassName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDeprecated($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDescription($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setMajor($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setSpecAvailable($arr[$keys[6]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EquipmentClassPeer::DATABASE_NAME);

		if ($this->isColumnModified(EquipmentClassPeer::EQUIPMENT_CLASS_ID)) $criteria->add(EquipmentClassPeer::EQUIPMENT_CLASS_ID, $this->equipment_class_id);
		if ($this->isColumnModified(EquipmentClassPeer::CATEGORY)) $criteria->add(EquipmentClassPeer::CATEGORY, $this->category);
		if ($this->isColumnModified(EquipmentClassPeer::CLASS_NAME)) $criteria->add(EquipmentClassPeer::CLASS_NAME, $this->class_name);
		if ($this->isColumnModified(EquipmentClassPeer::DEPRECATED)) $criteria->add(EquipmentClassPeer::DEPRECATED, $this->deprecated);
		if ($this->isColumnModified(EquipmentClassPeer::DESCRIPTION)) $criteria->add(EquipmentClassPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(EquipmentClassPeer::MAJOR)) $criteria->add(EquipmentClassPeer::MAJOR, $this->major);
		if ($this->isColumnModified(EquipmentClassPeer::SPEC_AVAILABLE)) $criteria->add(EquipmentClassPeer::SPEC_AVAILABLE, $this->spec_available);

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
		$criteria = new Criteria(EquipmentClassPeer::DATABASE_NAME);

		$criteria->add(EquipmentClassPeer::EQUIPMENT_CLASS_ID, $this->equipment_class_id);

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
	 * Generic method to set the primary key (equipment_class_id column).
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
	 * @param      object $copyObj An object of EquipmentClass (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCategory($this->category);

		$copyObj->setClassName($this->class_name);

		$copyObj->setDeprecated($this->deprecated);

		$copyObj->setDescription($this->description);

		$copyObj->setMajor($this->major);

		$copyObj->setSpecAvailable($this->spec_available);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getAttributes() as $relObj) {
				$copyObj->addAttribute($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentAttributeClasss() as $relObj) {
				$copyObj->addEquipmentAttributeClass($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentModels() as $relObj) {
				$copyObj->addEquipmentModel($relObj->copy($deepCopy));
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
	 * @return     EquipmentClass Clone of current object.
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
	 * @return     EquipmentClassPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EquipmentClassPeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collAttributes to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initAttributes()
	{
		if ($this->collAttributes === null) {
			$this->collAttributes = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentClass has previously
	 * been saved, it will retrieve related Attributes from storage.
	 * If this EquipmentClass is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getAttributes($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAttributes === null) {
			if ($this->isNew()) {
			   $this->collAttributes = array();
			} else {

				$criteria->add(AttributePeer::EQUIPMENT_CLASS_ID, $this->getId());

				AttributePeer::addSelectColumns($criteria);
				$this->collAttributes = AttributePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AttributePeer::EQUIPMENT_CLASS_ID, $this->getId());

				AttributePeer::addSelectColumns($criteria);
				if (!isset($this->lastAttributeCriteria) || !$this->lastAttributeCriteria->equals($criteria)) {
					$this->collAttributes = AttributePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAttributeCriteria = $criteria;
		return $this->collAttributes;
	}

	/**
	 * Returns the number of related Attributes.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countAttributes($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(AttributePeer::EQUIPMENT_CLASS_ID, $this->getId());

		return AttributePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Attribute object to this object
	 * through the Attribute foreign key attribute
	 *
	 * @param      Attribute $l Attribute
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAttribute(Attribute $l)
	{
		$this->collAttributes[] = $l;
		$l->setEquipmentClass($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentClass is new, it will return
	 * an empty collection; or if this EquipmentClass has previously
	 * been saved, it will retrieve related Attributes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentClass.
	 */
	public function getAttributesJoinUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAttributes === null) {
			if ($this->isNew()) {
				$this->collAttributes = array();
			} else {

				$criteria->add(AttributePeer::EQUIPMENT_CLASS_ID, $this->getId());

				$this->collAttributes = AttributePeer::doSelectJoinUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AttributePeer::EQUIPMENT_CLASS_ID, $this->getId());

			if (!isset($this->lastAttributeCriteria) || !$this->lastAttributeCriteria->equals($criteria)) {
				$this->collAttributes = AttributePeer::doSelectJoinUnit($criteria, $con);
			}
		}
		$this->lastAttributeCriteria = $criteria;

		return $this->collAttributes;
	}

	/**
	 * Temporary storage of collEquipmentAttributeClasss to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentAttributeClasss()
	{
		if ($this->collEquipmentAttributeClasss === null) {
			$this->collEquipmentAttributeClasss = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentClass has previously
	 * been saved, it will retrieve related EquipmentAttributeClasss from storage.
	 * If this EquipmentClass is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentAttributeClasss($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeClassPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeClasss === null) {
			if ($this->isNew()) {
			   $this->collEquipmentAttributeClasss = array();
			} else {

				$criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_CLASS_ID, $this->getId());

				EquipmentAttributeClassPeer::addSelectColumns($criteria);
				$this->collEquipmentAttributeClasss = EquipmentAttributeClassPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_CLASS_ID, $this->getId());

				EquipmentAttributeClassPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentAttributeClassCriteria) || !$this->lastEquipmentAttributeClassCriteria->equals($criteria)) {
					$this->collEquipmentAttributeClasss = EquipmentAttributeClassPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentAttributeClassCriteria = $criteria;
		return $this->collEquipmentAttributeClasss;
	}

	/**
	 * Returns the number of related EquipmentAttributeClasss.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentAttributeClasss($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeClassPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_CLASS_ID, $this->getId());

		return EquipmentAttributeClassPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentAttributeClass object to this object
	 * through the EquipmentAttributeClass foreign key attribute
	 *
	 * @param      EquipmentAttributeClass $l EquipmentAttributeClass
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentAttributeClass(EquipmentAttributeClass $l)
	{
		$this->collEquipmentAttributeClasss[] = $l;
		$l->setEquipmentClass($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentClass is new, it will return
	 * an empty collection; or if this EquipmentClass has previously
	 * been saved, it will retrieve related EquipmentAttributeClasss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentClass.
	 */
	public function getEquipmentAttributeClasssJoinEquipmentAttribute($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeClassPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeClasss === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeClasss = array();
			} else {

				$criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_CLASS_ID, $this->getId());

				$this->collEquipmentAttributeClasss = EquipmentAttributeClassPeer::doSelectJoinEquipmentAttribute($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeClassPeer::EQUIPMENT_CLASS_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeClassCriteria) || !$this->lastEquipmentAttributeClassCriteria->equals($criteria)) {
				$this->collEquipmentAttributeClasss = EquipmentAttributeClassPeer::doSelectJoinEquipmentAttribute($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeClassCriteria = $criteria;

		return $this->collEquipmentAttributeClasss;
	}

	/**
	 * Temporary storage of collEquipmentModels to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentModels()
	{
		if ($this->collEquipmentModels === null) {
			$this->collEquipmentModels = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentClass has previously
	 * been saved, it will retrieve related EquipmentModels from storage.
	 * If this EquipmentClass is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentModels($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModels === null) {
			if ($this->isNew()) {
			   $this->collEquipmentModels = array();
			} else {

				$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				$this->collEquipmentModels = EquipmentModelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentModelCriteria) || !$this->lastEquipmentModelCriteria->equals($criteria)) {
					$this->collEquipmentModels = EquipmentModelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentModelCriteria = $criteria;
		return $this->collEquipmentModels;
	}

	/**
	 * Returns the number of related EquipmentModels.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentModels($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

		return EquipmentModelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentModel object to this object
	 * through the EquipmentModel foreign key attribute
	 *
	 * @param      EquipmentModel $l EquipmentModel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentModel(EquipmentModel $l)
	{
		$this->collEquipmentModels[] = $l;
		$l->setEquipmentClass($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentClass is new, it will return
	 * an empty collection; or if this EquipmentClass has previously
	 * been saved, it will retrieve related EquipmentModels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentClass.
	 */
	public function getEquipmentModelsJoinDataFileRelatedByAdditionalSpecFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModels === null) {
			if ($this->isNew()) {
				$this->collEquipmentModels = array();
			} else {

				$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

				$this->collEquipmentModels = EquipmentModelPeer::doSelectJoinDataFileRelatedByAdditionalSpecFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

			if (!isset($this->lastEquipmentModelCriteria) || !$this->lastEquipmentModelCriteria->equals($criteria)) {
				$this->collEquipmentModels = EquipmentModelPeer::doSelectJoinDataFileRelatedByAdditionalSpecFileId($criteria, $con);
			}
		}
		$this->lastEquipmentModelCriteria = $criteria;

		return $this->collEquipmentModels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentClass is new, it will return
	 * an empty collection; or if this EquipmentClass has previously
	 * been saved, it will retrieve related EquipmentModels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentClass.
	 */
	public function getEquipmentModelsJoinDataFileRelatedByInterfaceDocFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModels === null) {
			if ($this->isNew()) {
				$this->collEquipmentModels = array();
			} else {

				$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

				$this->collEquipmentModels = EquipmentModelPeer::doSelectJoinDataFileRelatedByInterfaceDocFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

			if (!isset($this->lastEquipmentModelCriteria) || !$this->lastEquipmentModelCriteria->equals($criteria)) {
				$this->collEquipmentModels = EquipmentModelPeer::doSelectJoinDataFileRelatedByInterfaceDocFileId($criteria, $con);
			}
		}
		$this->lastEquipmentModelCriteria = $criteria;

		return $this->collEquipmentModels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentClass is new, it will return
	 * an empty collection; or if this EquipmentClass has previously
	 * been saved, it will retrieve related EquipmentModels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentClass.
	 */
	public function getEquipmentModelsJoinDataFileRelatedByManufacturerDocFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModels === null) {
			if ($this->isNew()) {
				$this->collEquipmentModels = array();
			} else {

				$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

				$this->collEquipmentModels = EquipmentModelPeer::doSelectJoinDataFileRelatedByManufacturerDocFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

			if (!isset($this->lastEquipmentModelCriteria) || !$this->lastEquipmentModelCriteria->equals($criteria)) {
				$this->collEquipmentModels = EquipmentModelPeer::doSelectJoinDataFileRelatedByManufacturerDocFileId($criteria, $con);
			}
		}
		$this->lastEquipmentModelCriteria = $criteria;

		return $this->collEquipmentModels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentClass is new, it will return
	 * an empty collection; or if this EquipmentClass has previously
	 * been saved, it will retrieve related EquipmentModels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentClass.
	 */
	public function getEquipmentModelsJoinDataFileRelatedBySubcomponentsDocFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModels === null) {
			if ($this->isNew()) {
				$this->collEquipmentModels = array();
			} else {

				$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

				$this->collEquipmentModels = EquipmentModelPeer::doSelectJoinDataFileRelatedBySubcomponentsDocFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

			if (!isset($this->lastEquipmentModelCriteria) || !$this->lastEquipmentModelCriteria->equals($criteria)) {
				$this->collEquipmentModels = EquipmentModelPeer::doSelectJoinDataFileRelatedBySubcomponentsDocFileId($criteria, $con);
			}
		}
		$this->lastEquipmentModelCriteria = $criteria;

		return $this->collEquipmentModels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentClass is new, it will return
	 * an empty collection; or if this EquipmentClass has previously
	 * been saved, it will retrieve related EquipmentModels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentClass.
	 */
	public function getEquipmentModelsJoinDataFileRelatedByDesignConsiderationFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModels === null) {
			if ($this->isNew()) {
				$this->collEquipmentModels = array();
			} else {

				$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

				$this->collEquipmentModels = EquipmentModelPeer::doSelectJoinDataFileRelatedByDesignConsiderationFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->getId());

			if (!isset($this->lastEquipmentModelCriteria) || !$this->lastEquipmentModelCriteria->equals($criteria)) {
				$this->collEquipmentModels = EquipmentModelPeer::doSelectJoinDataFileRelatedByDesignConsiderationFileId($criteria, $con);
			}
		}
		$this->lastEquipmentModelCriteria = $criteria;

		return $this->collEquipmentModels;
	}

} // BaseEquipmentClass
