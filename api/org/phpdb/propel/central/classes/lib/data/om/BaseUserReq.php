<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/UserReqPeer.php';

/**
 * Base class that represents a row from the 'USER_REQ' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseUserReq extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        UserReqPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the passwd field.
	 * @var        string
	 */
	protected $passwd;


	/**
	 * The value for the email field.
	 * @var        string
	 */
	protected $email;


	/**
	 * The value for the category field.
	 * @var        string
	 */
	protected $category;


	/**
	 * The value for the first_name field.
	 * @var        string
	 */
	protected $first_name;


	/**
	 * The value for the last_name field.
	 * @var        string
	 */
	protected $last_name;


	/**
	 * The value for the phone field.
	 * @var        string
	 */
	protected $phone;


	/**
	 * The value for the fax field.
	 * @var        string
	 */
	protected $fax;


	/**
	 * The value for the address field.
	 * @var        string
	 */
	protected $address;


	/**
	 * The value for the comments field.
	 * @var        string
	 */
	protected $comments;


	/**
	 * The value for the orgid field.
	 * @var        double
	 */
	protected $orgid;


	/**
	 * The value for the org_role_id field.
	 * @var        double
	 */
	protected $org_role_id;


	/**
	 * The value for the ee_organization field.
	 * @var        string
	 */
	protected $ee_organization;


	/**
	 * The value for the personal_reference field.
	 * @var        string
	 */
	protected $personal_reference;

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
	 * Get the [passwd] column value.
	 * 
	 * @return     string
	 */
	public function getPassword()
	{

		return $this->passwd;
	}

	/**
	 * Get the [email] column value.
	 * 
	 * @return     string
	 */
	public function getEmail()
	{

		return $this->email;
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
	 * Get the [first_name] column value.
	 * 
	 * @return     string
	 */
	public function getFirstName()
	{

		return $this->first_name;
	}

	/**
	 * Get the [last_name] column value.
	 * 
	 * @return     string
	 */
	public function getLastName()
	{

		return $this->last_name;
	}

	/**
	 * Get the [phone] column value.
	 * 
	 * @return     string
	 */
	public function getPhone()
	{

		return $this->phone;
	}

	/**
	 * Get the [fax] column value.
	 * 
	 * @return     string
	 */
	public function getFax()
	{

		return $this->fax;
	}

	/**
	 * Get the [address] column value.
	 * 
	 * @return     string
	 */
	public function getAddress()
	{

		return $this->address;
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
	 * Get the [orgid] column value.
	 * 
	 * @return     double
	 */
	public function getOrganizationId()
	{

		return $this->orgid;
	}

	/**
	 * Get the [org_role_id] column value.
	 * 
	 * @return     double
	 */
	public function getOrgRoleId()
	{

		return $this->org_role_id;
	}

	/**
	 * Get the [ee_organization] column value.
	 * 
	 * @return     string
	 */
	public function getEEOrganization()
	{

		return $this->ee_organization;
	}

	/**
	 * Get the [personal_reference] column value.
	 * 
	 * @return     string
	 */
	public function getPersonalReference()
	{

		return $this->personal_reference;
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
			$this->modifiedColumns[] = UserReqPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [passwd] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPassword($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->passwd !== $v) {
			$this->passwd = $v;
			$this->modifiedColumns[] = UserReqPeer::PASSWD;
		}

	} // setPassword()

	/**
	 * Set the value of [email] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setEmail($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->email !== $v) {
			$this->email = $v;
			$this->modifiedColumns[] = UserReqPeer::EMAIL;
		}

	} // setEmail()

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
			$this->modifiedColumns[] = UserReqPeer::CATEGORY;
		}

	} // setCategory()

	/**
	 * Set the value of [first_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFirstName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->first_name !== $v) {
			$this->first_name = $v;
			$this->modifiedColumns[] = UserReqPeer::FIRST_NAME;
		}

	} // setFirstName()

	/**
	 * Set the value of [last_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setLastName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->last_name !== $v) {
			$this->last_name = $v;
			$this->modifiedColumns[] = UserReqPeer::LAST_NAME;
		}

	} // setLastName()

	/**
	 * Set the value of [phone] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPhone($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->phone !== $v) {
			$this->phone = $v;
			$this->modifiedColumns[] = UserReqPeer::PHONE;
		}

	} // setPhone()

	/**
	 * Set the value of [fax] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFax($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->fax !== $v) {
			$this->fax = $v;
			$this->modifiedColumns[] = UserReqPeer::FAX;
		}

	} // setFax()

	/**
	 * Set the value of [address] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setAddress($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->address !== $v) {
			$this->address = $v;
			$this->modifiedColumns[] = UserReqPeer::ADDRESS;
		}

	} // setAddress()

	/**
	 * Set the value of [comments] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setComment($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->comments !== $v) {
			$this->comments = $v;
			$this->modifiedColumns[] = UserReqPeer::COMMENTS;
		}

	} // setComment()

	/**
	 * Set the value of [orgid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOrganizationId($v)
	{

		if ($this->orgid !== $v) {
			$this->orgid = $v;
			$this->modifiedColumns[] = UserReqPeer::ORGID;
		}

	} // setOrganizationId()

	/**
	 * Set the value of [org_role_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOrgRoleId($v)
	{

		if ($this->org_role_id !== $v) {
			$this->org_role_id = $v;
			$this->modifiedColumns[] = UserReqPeer::ORG_ROLE_ID;
		}

	} // setOrgRoleId()

	/**
	 * Set the value of [ee_organization] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setEEOrganization($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->ee_organization !== $v) {
			$this->ee_organization = $v;
			$this->modifiedColumns[] = UserReqPeer::EE_ORGANIZATION;
		}

	} // setEEOrganization()

	/**
	 * Set the value of [personal_reference] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPersonalReference($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->personal_reference !== $v) {
			$this->personal_reference = $v;
			$this->modifiedColumns[] = UserReqPeer::PERSONAL_REFERENCE;
		}

	} // setPersonalReference()

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

			$this->passwd = $rs->getString($startcol + 1);

			$this->email = $rs->getString($startcol + 2);

			$this->category = $rs->getString($startcol + 3);

			$this->first_name = $rs->getString($startcol + 4);

			$this->last_name = $rs->getString($startcol + 5);

			$this->phone = $rs->getString($startcol + 6);

			$this->fax = $rs->getString($startcol + 7);

			$this->address = $rs->getString($startcol + 8);

			$this->comments = $rs->getString($startcol + 9);

			$this->orgid = $rs->getFloat($startcol + 10);

			$this->org_role_id = $rs->getFloat($startcol + 11);

			$this->ee_organization = $rs->getString($startcol + 12);

			$this->personal_reference = $rs->getString($startcol + 13);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 14; // 14 = UserReqPeer::NUM_COLUMNS - UserReqPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating UserReq object", $e);
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
			$con = Propel::getConnection(UserReqPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			UserReqPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(UserReqPeer::DATABASE_NAME);
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
					$pk = UserReqPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += UserReqPeer::doUpdate($this, $con);
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


			if (($retval = UserReqPeer::doValidate($this, $columns)) !== true) {
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
		$pos = UserReqPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPassword();
				break;
			case 2:
				return $this->getEmail();
				break;
			case 3:
				return $this->getCategory();
				break;
			case 4:
				return $this->getFirstName();
				break;
			case 5:
				return $this->getLastName();
				break;
			case 6:
				return $this->getPhone();
				break;
			case 7:
				return $this->getFax();
				break;
			case 8:
				return $this->getAddress();
				break;
			case 9:
				return $this->getComment();
				break;
			case 10:
				return $this->getOrganizationId();
				break;
			case 11:
				return $this->getOrgRoleId();
				break;
			case 12:
				return $this->getEEOrganization();
				break;
			case 13:
				return $this->getPersonalReference();
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
		$keys = UserReqPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPassword(),
			$keys[2] => $this->getEmail(),
			$keys[3] => $this->getCategory(),
			$keys[4] => $this->getFirstName(),
			$keys[5] => $this->getLastName(),
			$keys[6] => $this->getPhone(),
			$keys[7] => $this->getFax(),
			$keys[8] => $this->getAddress(),
			$keys[9] => $this->getComment(),
			$keys[10] => $this->getOrganizationId(),
			$keys[11] => $this->getOrgRoleId(),
			$keys[12] => $this->getEEOrganization(),
			$keys[13] => $this->getPersonalReference(),
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
		$pos = UserReqPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPassword($value);
				break;
			case 2:
				$this->setEmail($value);
				break;
			case 3:
				$this->setCategory($value);
				break;
			case 4:
				$this->setFirstName($value);
				break;
			case 5:
				$this->setLastName($value);
				break;
			case 6:
				$this->setPhone($value);
				break;
			case 7:
				$this->setFax($value);
				break;
			case 8:
				$this->setAddress($value);
				break;
			case 9:
				$this->setComment($value);
				break;
			case 10:
				$this->setOrganizationId($value);
				break;
			case 11:
				$this->setOrgRoleId($value);
				break;
			case 12:
				$this->setEEOrganization($value);
				break;
			case 13:
				$this->setPersonalReference($value);
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
		$keys = UserReqPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPassword($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEmail($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCategory($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setFirstName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setLastName($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setPhone($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setFax($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setAddress($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setComment($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setOrganizationId($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setOrgRoleId($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setEEOrganization($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setPersonalReference($arr[$keys[13]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(UserReqPeer::DATABASE_NAME);

		if ($this->isColumnModified(UserReqPeer::ID)) $criteria->add(UserReqPeer::ID, $this->id);
		if ($this->isColumnModified(UserReqPeer::PASSWD)) $criteria->add(UserReqPeer::PASSWD, $this->passwd);
		if ($this->isColumnModified(UserReqPeer::EMAIL)) $criteria->add(UserReqPeer::EMAIL, $this->email);
		if ($this->isColumnModified(UserReqPeer::CATEGORY)) $criteria->add(UserReqPeer::CATEGORY, $this->category);
		if ($this->isColumnModified(UserReqPeer::FIRST_NAME)) $criteria->add(UserReqPeer::FIRST_NAME, $this->first_name);
		if ($this->isColumnModified(UserReqPeer::LAST_NAME)) $criteria->add(UserReqPeer::LAST_NAME, $this->last_name);
		if ($this->isColumnModified(UserReqPeer::PHONE)) $criteria->add(UserReqPeer::PHONE, $this->phone);
		if ($this->isColumnModified(UserReqPeer::FAX)) $criteria->add(UserReqPeer::FAX, $this->fax);
		if ($this->isColumnModified(UserReqPeer::ADDRESS)) $criteria->add(UserReqPeer::ADDRESS, $this->address);
		if ($this->isColumnModified(UserReqPeer::COMMENTS)) $criteria->add(UserReqPeer::COMMENTS, $this->comments);
		if ($this->isColumnModified(UserReqPeer::ORGID)) $criteria->add(UserReqPeer::ORGID, $this->orgid);
		if ($this->isColumnModified(UserReqPeer::ORG_ROLE_ID)) $criteria->add(UserReqPeer::ORG_ROLE_ID, $this->org_role_id);
		if ($this->isColumnModified(UserReqPeer::EE_ORGANIZATION)) $criteria->add(UserReqPeer::EE_ORGANIZATION, $this->ee_organization);
		if ($this->isColumnModified(UserReqPeer::PERSONAL_REFERENCE)) $criteria->add(UserReqPeer::PERSONAL_REFERENCE, $this->personal_reference);

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
		$criteria = new Criteria(UserReqPeer::DATABASE_NAME);

		$criteria->add(UserReqPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of UserReq (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPassword($this->passwd);

		$copyObj->setEmail($this->email);

		$copyObj->setCategory($this->category);

		$copyObj->setFirstName($this->first_name);

		$copyObj->setLastName($this->last_name);

		$copyObj->setPhone($this->phone);

		$copyObj->setFax($this->fax);

		$copyObj->setAddress($this->address);

		$copyObj->setComment($this->comments);

		$copyObj->setOrganizationId($this->orgid);

		$copyObj->setOrgRoleId($this->org_role_id);

		$copyObj->setEEOrganization($this->ee_organization);

		$copyObj->setPersonalReference($this->personal_reference);


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
	 * @return     UserReq Clone of current object.
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
	 * @return     UserReqPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new UserReqPeer();
		}
		return self::$peer;
	}

} // BaseUserReq
