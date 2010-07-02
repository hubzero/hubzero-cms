<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/security/PermissionsViewPeer.php';

/**
 * Base class that represents a row from the 'PERMISSION' table.
 *
 * 
 *
 * @package    lib.security.om
 */
abstract class BasePermissionsView extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        PermissionsViewPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the person_id field.
	 * @var        double
	 */
	protected $person_id;


	/**
	 * The value for the entity_type_id field.
	 * @var        double
	 */
	protected $entity_type_id;


	/**
	 * The value for the entity_id field.
	 * @var        double
	 */
	protected $entity_id;


	/**
	 * The value for the can_view field.
	 * @var        double
	 */
	protected $can_view;


	/**
	 * The value for the can_create field.
	 * @var        double
	 */
	protected $can_create;


	/**
	 * The value for the can_edit field.
	 * @var        double
	 */
	protected $can_edit;


	/**
	 * The value for the can_delete field.
	 * @var        double
	 */
	protected $can_delete;


	/**
	 * The value for the can_grant field.
	 * @var        double
	 */
	protected $can_grant;


	/**
	 * The value for the is_super_role field.
	 * @var        double
	 */
	protected $is_super_role;


	/**
	 * The value for the permissions field.
	 * @var        string
	 */
	protected $permissions;

	/**
	 * @var        Person
	 */
	protected $aPerson;

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
	 * Get the [person_id] column value.
	 * 
	 * @return     double
	 */
	public function getPersonId()
	{

		return $this->person_id;
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
	 * Get the [entity_id] column value.
	 * 
	 * @return     double
	 */
	public function getEntityId()
	{

		return $this->entity_id;
	}

	/**
	 * Get the [can_view] column value.
	 * 
	 * @return     double
	 */
	public function getCanView()
	{

		return $this->can_view;
	}

	/**
	 * Get the [can_create] column value.
	 * 
	 * @return     double
	 */
	public function getCanCreate()
	{

		return $this->can_create;
	}

	/**
	 * Get the [can_edit] column value.
	 * 
	 * @return     double
	 */
	public function getCanEdit()
	{

		return $this->can_edit;
	}

	/**
	 * Get the [can_delete] column value.
	 * 
	 * @return     double
	 */
	public function getCanDelete()
	{

		return $this->can_delete;
	}

	/**
	 * Get the [can_grant] column value.
	 * 
	 * @return     double
	 */
	public function getCanGrant()
	{

		return $this->can_grant;
	}

	/**
	 * Get the [is_super_role] column value.
	 * 
	 * @return     double
	 */
	public function getSuperRole()
	{

		return $this->is_super_role;
	}

	/**
	 * Get the [permissions] column value.
	 * 
	 * @return     string
	 */
	public function getPermissionsStr()
	{

		return $this->permissions;
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
			$this->modifiedColumns[] = PermissionsViewPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [person_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPersonId($v)
	{

		if ($this->person_id !== $v) {
			$this->person_id = $v;
			$this->modifiedColumns[] = PermissionsViewPeer::PERSON_ID;
		}

		if ($this->aPerson !== null && $this->aPerson->getId() !== $v) {
			$this->aPerson = null;
		}

	} // setPersonId()

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
			$this->modifiedColumns[] = PermissionsViewPeer::ENTITY_TYPE_ID;
		}

	} // setEntityTypeId()

	/**
	 * Set the value of [entity_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEntityId($v)
	{

		if ($this->entity_id !== $v) {
			$this->entity_id = $v;
			$this->modifiedColumns[] = PermissionsViewPeer::ENTITY_ID;
		}

	} // setEntityId()

	/**
	 * Set the value of [can_view] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCanView($v)
	{

		if ($this->can_view !== $v) {
			$this->can_view = $v;
			$this->modifiedColumns[] = PermissionsViewPeer::CAN_VIEW;
		}

	} // setCanView()

	/**
	 * Set the value of [can_create] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCanCreate($v)
	{

		if ($this->can_create !== $v) {
			$this->can_create = $v;
			$this->modifiedColumns[] = PermissionsViewPeer::CAN_CREATE;
		}

	} // setCanCreate()

	/**
	 * Set the value of [can_edit] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCanEdit($v)
	{

		if ($this->can_edit !== $v) {
			$this->can_edit = $v;
			$this->modifiedColumns[] = PermissionsViewPeer::CAN_EDIT;
		}

	} // setCanEdit()

	/**
	 * Set the value of [can_delete] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCanDelete($v)
	{

		if ($this->can_delete !== $v) {
			$this->can_delete = $v;
			$this->modifiedColumns[] = PermissionsViewPeer::CAN_DELETE;
		}

	} // setCanDelete()

	/**
	 * Set the value of [can_grant] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCanGrant($v)
	{

		if ($this->can_grant !== $v) {
			$this->can_grant = $v;
			$this->modifiedColumns[] = PermissionsViewPeer::CAN_GRANT;
		}

	} // setCanGrant()

	/**
	 * Set the value of [is_super_role] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSuperRole($v)
	{

		if ($this->is_super_role !== $v) {
			$this->is_super_role = $v;
			$this->modifiedColumns[] = PermissionsViewPeer::IS_SUPER_ROLE;
		}

	} // setSuperRole()

	/**
	 * Set the value of [permissions] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPermissionsStr($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->permissions !== $v) {
			$this->permissions = $v;
			$this->modifiedColumns[] = PermissionsViewPeer::PERMISSIONS;
		}

	} // setPermissionsStr()

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

			$this->person_id = $rs->getFloat($startcol + 1);

			$this->entity_type_id = $rs->getFloat($startcol + 2);

			$this->entity_id = $rs->getFloat($startcol + 3);

			$this->can_view = $rs->getFloat($startcol + 4);

			$this->can_create = $rs->getFloat($startcol + 5);

			$this->can_edit = $rs->getFloat($startcol + 6);

			$this->can_delete = $rs->getFloat($startcol + 7);

			$this->can_grant = $rs->getFloat($startcol + 8);

			$this->is_super_role = $rs->getFloat($startcol + 9);

			$this->permissions = $rs->getString($startcol + 10);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 11; // 11 = PermissionsViewPeer::NUM_COLUMNS - PermissionsViewPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating PermissionsView object", $e);
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
			$con = Propel::getConnection(PermissionsViewPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			PermissionsViewPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(PermissionsViewPeer::DATABASE_NAME);
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

			if ($this->aPerson !== null) {
				if ($this->aPerson->isModified()) {
					$affectedRows += $this->aPerson->save($con);
				}
				$this->setPerson($this->aPerson);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = PermissionsViewPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += PermissionsViewPeer::doUpdate($this, $con);
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

			if ($this->aPerson !== null) {
				if (!$this->aPerson->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aPerson->getValidationFailures());
				}
			}


			if (($retval = PermissionsViewPeer::doValidate($this, $columns)) !== true) {
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
		$pos = PermissionsViewPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPersonId();
				break;
			case 2:
				return $this->getEntityTypeId();
				break;
			case 3:
				return $this->getEntityId();
				break;
			case 4:
				return $this->getCanView();
				break;
			case 5:
				return $this->getCanCreate();
				break;
			case 6:
				return $this->getCanEdit();
				break;
			case 7:
				return $this->getCanDelete();
				break;
			case 8:
				return $this->getCanGrant();
				break;
			case 9:
				return $this->getSuperRole();
				break;
			case 10:
				return $this->getPermissionsStr();
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
		$keys = PermissionsViewPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPersonId(),
			$keys[2] => $this->getEntityTypeId(),
			$keys[3] => $this->getEntityId(),
			$keys[4] => $this->getCanView(),
			$keys[5] => $this->getCanCreate(),
			$keys[6] => $this->getCanEdit(),
			$keys[7] => $this->getCanDelete(),
			$keys[8] => $this->getCanGrant(),
			$keys[9] => $this->getSuperRole(),
			$keys[10] => $this->getPermissionsStr(),
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
		$pos = PermissionsViewPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPersonId($value);
				break;
			case 2:
				$this->setEntityTypeId($value);
				break;
			case 3:
				$this->setEntityId($value);
				break;
			case 4:
				$this->setCanView($value);
				break;
			case 5:
				$this->setCanCreate($value);
				break;
			case 6:
				$this->setCanEdit($value);
				break;
			case 7:
				$this->setCanDelete($value);
				break;
			case 8:
				$this->setCanGrant($value);
				break;
			case 9:
				$this->setSuperRole($value);
				break;
			case 10:
				$this->setPermissionsStr($value);
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
		$keys = PermissionsViewPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPersonId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEntityTypeId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setEntityId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setCanView($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCanCreate($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setCanEdit($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCanDelete($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCanGrant($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setSuperRole($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setPermissionsStr($arr[$keys[10]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(PermissionsViewPeer::DATABASE_NAME);

		if ($this->isColumnModified(PermissionsViewPeer::ID)) $criteria->add(PermissionsViewPeer::ID, $this->id);
		if ($this->isColumnModified(PermissionsViewPeer::PERSON_ID)) $criteria->add(PermissionsViewPeer::PERSON_ID, $this->person_id);
		if ($this->isColumnModified(PermissionsViewPeer::ENTITY_TYPE_ID)) $criteria->add(PermissionsViewPeer::ENTITY_TYPE_ID, $this->entity_type_id);
		if ($this->isColumnModified(PermissionsViewPeer::ENTITY_ID)) $criteria->add(PermissionsViewPeer::ENTITY_ID, $this->entity_id);
		if ($this->isColumnModified(PermissionsViewPeer::CAN_VIEW)) $criteria->add(PermissionsViewPeer::CAN_VIEW, $this->can_view);
		if ($this->isColumnModified(PermissionsViewPeer::CAN_CREATE)) $criteria->add(PermissionsViewPeer::CAN_CREATE, $this->can_create);
		if ($this->isColumnModified(PermissionsViewPeer::CAN_EDIT)) $criteria->add(PermissionsViewPeer::CAN_EDIT, $this->can_edit);
		if ($this->isColumnModified(PermissionsViewPeer::CAN_DELETE)) $criteria->add(PermissionsViewPeer::CAN_DELETE, $this->can_delete);
		if ($this->isColumnModified(PermissionsViewPeer::CAN_GRANT)) $criteria->add(PermissionsViewPeer::CAN_GRANT, $this->can_grant);
		if ($this->isColumnModified(PermissionsViewPeer::IS_SUPER_ROLE)) $criteria->add(PermissionsViewPeer::IS_SUPER_ROLE, $this->is_super_role);
		if ($this->isColumnModified(PermissionsViewPeer::PERMISSIONS)) $criteria->add(PermissionsViewPeer::PERMISSIONS, $this->permissions);

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
		$criteria = new Criteria(PermissionsViewPeer::DATABASE_NAME);

		$criteria->add(PermissionsViewPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of PermissionsView (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPersonId($this->person_id);

		$copyObj->setEntityTypeId($this->entity_type_id);

		$copyObj->setEntityId($this->entity_id);

		$copyObj->setCanView($this->can_view);

		$copyObj->setCanCreate($this->can_create);

		$copyObj->setCanEdit($this->can_edit);

		$copyObj->setCanDelete($this->can_delete);

		$copyObj->setCanGrant($this->can_grant);

		$copyObj->setSuperRole($this->is_super_role);

		$copyObj->setPermissionsStr($this->permissions);


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
	 * @return     PermissionsView Clone of current object.
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
	 * @return     PermissionsViewPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new PermissionsViewPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Person object.
	 *
	 * @param      Person $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setPerson($v)
	{


		if ($v === null) {
			$this->setPersonId(NULL);
		} else {
			$this->setPersonId($v->getId());
		}


		$this->aPerson = $v;
	}


	/**
	 * Get the associated Person object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Person The associated Person object.
	 * @throws     PropelException
	 */
	public function getPerson($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BasePersonPeer.php';

		if ($this->aPerson === null && ($this->person_id > 0)) {

			$this->aPerson = PersonPeer::retrieveByPK($this->person_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = PersonPeer::retrieveByPK($this->person_id, $con);
			   $obj->addPersons($this);
			 */
		}
		return $this->aPerson;
	}

} // BasePermissionsView
