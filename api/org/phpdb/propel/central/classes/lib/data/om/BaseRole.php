<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/RolePeer.php';

/**
 * Base class that represents a row from the 'ROLE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseRole extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        RolePeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the default_permissions field.
	 * @var        string
	 */
	protected $default_permissions;


	/**
	 * The value for the entity_type_id field.
	 * @var        double
	 */
	protected $entity_type_id;


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
	 * The value for the super_role field.
	 * @var        double
	 */
	protected $super_role;

	/**
	 * @var        EntityType
	 */
	protected $aEntityType;

	/**
	 * Collection to store aggregation of collPersonEntityRoles.
	 * @var        array
	 */
	protected $collPersonEntityRoles;

	/**
	 * The criteria used to select the current contents of collPersonEntityRoles.
	 * @var        Criteria
	 */
	protected $lastPersonEntityRoleCriteria = null;

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
	 * Get the [default_permissions] column value.
	 * 
	 * @return     string
	 */
	public function getDefaultPermissionsStr()
	{

		return $this->default_permissions;
	}

	/**
	 * Get the [entity_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getEntityTypeId()
	{

		return $this->entity_type_id;
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
	 * Get the [super_role] column value.
	 * 
	 * @return     double
	 */
	public function getSuperRole()
	{

		return $this->super_role;
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
			$this->modifiedColumns[] = RolePeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [default_permissions] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDefaultPermissionsStr($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->default_permissions !== $v) {
			$this->default_permissions = $v;
			$this->modifiedColumns[] = RolePeer::DEFAULT_PERMISSIONS;
		}

	} // setDefaultPermissionsStr()

	/**
	 * Set the value of [entity_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEntityTypeId($v)
	{

		if ($this->entity_type_id !== $v) {
			$this->entity_type_id = $v;
			$this->modifiedColumns[] = RolePeer::ENTITY_TYPE_ID;
		}

		if ($this->aEntityType !== null && $this->aEntityType->getId() !== $v) {
			$this->aEntityType = null;
		}

	} // setEntityTypeId()

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
			$this->modifiedColumns[] = RolePeer::DISPLAY_NAME;
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
			$this->modifiedColumns[] = RolePeer::SYSTEM_NAME;
		}

	} // setSystemName()

	/**
	 * Set the value of [super_role] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSuperRole($v)
	{

		if ($this->super_role !== $v) {
			$this->super_role = $v;
			$this->modifiedColumns[] = RolePeer::SUPER_ROLE;
		}

	} // setSuperRole()

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

			$this->default_permissions = $rs->getString($startcol + 1);

			$this->entity_type_id = $rs->getFloat($startcol + 2);

			$this->display_name = $rs->getString($startcol + 3);

			$this->system_name = $rs->getString($startcol + 4);

			$this->super_role = $rs->getFloat($startcol + 5);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = RolePeer::NUM_COLUMNS - RolePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Role object", $e);
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
			$con = Propel::getConnection(RolePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			RolePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(RolePeer::DATABASE_NAME);
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

			if ($this->aEntityType !== null) {
				if ($this->aEntityType->isModified()) {
					$affectedRows += $this->aEntityType->save($con);
				}
				$this->setEntityType($this->aEntityType);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = RolePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += RolePeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collPersonEntityRoles !== null) {
				foreach($this->collPersonEntityRoles as $referrerFK) {
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

			if ($this->aEntityType !== null) {
				if (!$this->aEntityType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEntityType->getValidationFailures());
				}
			}


			if (($retval = RolePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collPersonEntityRoles !== null) {
					foreach($this->collPersonEntityRoles as $referrerFK) {
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
		$pos = RolePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDefaultPermissionsStr();
				break;
			case 2:
				return $this->getEntityTypeId();
				break;
			case 3:
				return $this->getDisplayName();
				break;
			case 4:
				return $this->getSystemName();
				break;
			case 5:
				return $this->getSuperRole();
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
		$keys = RolePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDefaultPermissionsStr(),
			$keys[2] => $this->getEntityTypeId(),
			$keys[3] => $this->getDisplayName(),
			$keys[4] => $this->getSystemName(),
			$keys[5] => $this->getSuperRole(),
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
		$pos = RolePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDefaultPermissionsStr($value);
				break;
			case 2:
				$this->setEntityTypeId($value);
				break;
			case 3:
				$this->setDisplayName($value);
				break;
			case 4:
				$this->setSystemName($value);
				break;
			case 5:
				$this->setSuperRole($value);
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
		$keys = RolePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDefaultPermissionsStr($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEntityTypeId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDisplayName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setSystemName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setSuperRole($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(RolePeer::DATABASE_NAME);

		if ($this->isColumnModified(RolePeer::ID)) $criteria->add(RolePeer::ID, $this->id);
		if ($this->isColumnModified(RolePeer::DEFAULT_PERMISSIONS)) $criteria->add(RolePeer::DEFAULT_PERMISSIONS, $this->default_permissions);
		if ($this->isColumnModified(RolePeer::ENTITY_TYPE_ID)) $criteria->add(RolePeer::ENTITY_TYPE_ID, $this->entity_type_id);
		if ($this->isColumnModified(RolePeer::DISPLAY_NAME)) $criteria->add(RolePeer::DISPLAY_NAME, $this->display_name);
		if ($this->isColumnModified(RolePeer::SYSTEM_NAME)) $criteria->add(RolePeer::SYSTEM_NAME, $this->system_name);
		if ($this->isColumnModified(RolePeer::SUPER_ROLE)) $criteria->add(RolePeer::SUPER_ROLE, $this->super_role);

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
		$criteria = new Criteria(RolePeer::DATABASE_NAME);

		$criteria->add(RolePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of Role (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDefaultPermissionsStr($this->default_permissions);

		$copyObj->setEntityTypeId($this->entity_type_id);

		$copyObj->setDisplayName($this->display_name);

		$copyObj->setSystemName($this->system_name);

		$copyObj->setSuperRole($this->super_role);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getPersonEntityRoles() as $relObj) {
				$copyObj->addPersonEntityRole($relObj->copy($deepCopy));
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
	 * @return     Role Clone of current object.
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
	 * @return     RolePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new RolePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a EntityType object.
	 *
	 * @param      EntityType $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEntityType($v)
	{


		if ($v === null) {
			$this->setEntityTypeId(NULL);
		} else {
			$this->setEntityTypeId($v->getId());
		}


		$this->aEntityType = $v;
	}


	/**
	 * Get the associated EntityType object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     EntityType The associated EntityType object.
	 * @throws     PropelException
	 */
	public function getEntityType($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEntityTypePeer.php';

		if ($this->aEntityType === null && ($this->entity_type_id > 0)) {

			$this->aEntityType = EntityTypePeer::retrieveByPK($this->entity_type_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EntityTypePeer::retrieveByPK($this->entity_type_id, $con);
			   $obj->addEntityTypes($this);
			 */
		}
		return $this->aEntityType;
	}

	/**
	 * Temporary storage of collPersonEntityRoles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initPersonEntityRoles()
	{
		if ($this->collPersonEntityRoles === null) {
			$this->collPersonEntityRoles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Role has previously
	 * been saved, it will retrieve related PersonEntityRoles from storage.
	 * If this Role is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getPersonEntityRoles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BasePersonEntityRolePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collPersonEntityRoles === null) {
			if ($this->isNew()) {
			   $this->collPersonEntityRoles = array();
			} else {

				$criteria->add(PersonEntityRolePeer::ROLE_ID, $this->getId());

				PersonEntityRolePeer::addSelectColumns($criteria);
				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(PersonEntityRolePeer::ROLE_ID, $this->getId());

				PersonEntityRolePeer::addSelectColumns($criteria);
				if (!isset($this->lastPersonEntityRoleCriteria) || !$this->lastPersonEntityRoleCriteria->equals($criteria)) {
					$this->collPersonEntityRoles = PersonEntityRolePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastPersonEntityRoleCriteria = $criteria;
		return $this->collPersonEntityRoles;
	}

	/**
	 * Returns the number of related PersonEntityRoles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countPersonEntityRoles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BasePersonEntityRolePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(PersonEntityRolePeer::ROLE_ID, $this->getId());

		return PersonEntityRolePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a PersonEntityRole object to this object
	 * through the PersonEntityRole foreign key attribute
	 *
	 * @param      PersonEntityRole $l PersonEntityRole
	 * @return     void
	 * @throws     PropelException
	 */
	public function addPersonEntityRole(PersonEntityRole $l)
	{
		$this->collPersonEntityRoles[] = $l;
		$l->setRole($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Role is new, it will return
	 * an empty collection; or if this Role has previously
	 * been saved, it will retrieve related PersonEntityRoles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Role.
	 */
	public function getPersonEntityRolesJoinEntityType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BasePersonEntityRolePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collPersonEntityRoles === null) {
			if ($this->isNew()) {
				$this->collPersonEntityRoles = array();
			} else {

				$criteria->add(PersonEntityRolePeer::ROLE_ID, $this->getId());

				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelectJoinEntityType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(PersonEntityRolePeer::ROLE_ID, $this->getId());

			if (!isset($this->lastPersonEntityRoleCriteria) || !$this->lastPersonEntityRoleCriteria->equals($criteria)) {
				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelectJoinEntityType($criteria, $con);
			}
		}
		$this->lastPersonEntityRoleCriteria = $criteria;

		return $this->collPersonEntityRoles;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Role is new, it will return
	 * an empty collection; or if this Role has previously
	 * been saved, it will retrieve related PersonEntityRoles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Role.
	 */
	public function getPersonEntityRolesJoinPerson($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BasePersonEntityRolePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collPersonEntityRoles === null) {
			if ($this->isNew()) {
				$this->collPersonEntityRoles = array();
			} else {

				$criteria->add(PersonEntityRolePeer::ROLE_ID, $this->getId());

				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelectJoinPerson($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(PersonEntityRolePeer::ROLE_ID, $this->getId());

			if (!isset($this->lastPersonEntityRoleCriteria) || !$this->lastPersonEntityRoleCriteria->equals($criteria)) {
				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelectJoinPerson($criteria, $con);
			}
		}
		$this->lastPersonEntityRoleCriteria = $criteria;

		return $this->collPersonEntityRoles;
	}

} // BaseRole
