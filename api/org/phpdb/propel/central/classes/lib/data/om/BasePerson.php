<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/PersonPeer.php';

/**
 * Base class that represents a row from the 'PERSON' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BasePerson extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        PersonPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the address field.
	 * @var        string
	 */
	protected $address;


	/**
	 * The value for the admin_status field.
	 * @var        double
	 */
	protected $admin_status;


	/**
	 * The value for the category field.
	 * @var        string
	 */
	protected $category;


	/**
	 * The value for the comments field.
	 * @var        string
	 */
	protected $comments;


	/**
	 * The value for the e_mail field.
	 * @var        string
	 */
	protected $e_mail;


	/**
	 * The value for the fax field.
	 * @var        string
	 */
	protected $fax;


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
	 * The value for the user_name field.
	 * @var        string
	 */
	protected $user_name;

	/**
	 * Collection to store aggregation of collAuthorizations.
	 * @var        array
	 */
	protected $collAuthorizations;

	/**
	 * The criteria used to select the current contents of collAuthorizations.
	 * @var        Criteria
	 */
	protected $lastAuthorizationCriteria = null;

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
	 * Collection to store aggregation of collProjects.
	 * @var        array
	 */
	protected $collProjects;

	/**
	 * The criteria used to select the current contents of collProjects.
	 * @var        Criteria
	 */
	protected $lastProjectCriteria = null;

	/**
	 * Collection to store aggregation of collPermissionsViews.
	 * @var        array
	 */
	protected $collPermissionsViews;

	/**
	 * The criteria used to select the current contents of collPermissionsViews.
	 * @var        Criteria
	 */
	protected $lastPermissionsViewCriteria = null;

	/**
	 * Collection to store aggregation of collLogEntrys.
	 * @var        array
	 */
	protected $collLogEntrys;

	/**
	 * The criteria used to select the current contents of collLogEntrys.
	 * @var        Criteria
	 */
	protected $lastLogEntryCriteria = null;

	/**
	 * Collection to store aggregation of collAnnotations.
	 * @var        array
	 */
	protected $collAnnotations;

	/**
	 * The criteria used to select the current contents of collAnnotations.
	 * @var        Criteria
	 */
	protected $lastAnnotationCriteria = null;

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
	 * Get the [address] column value.
	 * 
	 * @return     string
	 */
	public function getAddress()
	{

		return $this->address;
	}

	/**
	 * Get the [admin_status] column value.
	 * 
	 * @return     double
	 */
	public function getAdminStatus()
	{

		return $this->admin_status;
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
	 * Get the [comments] column value.
	 * 
	 * @return     string
	 */
	public function getComment()
	{

		return $this->comments;
	}

	/**
	 * Get the [e_mail] column value.
	 * 
	 * @return     string
	 */
	public function getEMail()
	{

		return $this->e_mail;
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
	 * Get the [user_name] column value.
	 * 
	 * @return     string
	 */
	public function getUserName()
	{

		return $this->user_name;
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
			$this->modifiedColumns[] = PersonPeer::ID;
		}

	} // setId()

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
			$this->modifiedColumns[] = PersonPeer::ADDRESS;
		}

	} // setAddress()

	/**
	 * Set the value of [admin_status] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAdminStatus($v)
	{

		if ($this->admin_status !== $v) {
			$this->admin_status = $v;
			$this->modifiedColumns[] = PersonPeer::ADMIN_STATUS;
		}

	} // setAdminStatus()

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
			$this->modifiedColumns[] = PersonPeer::CATEGORY;
		}

	} // setCategory()

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
			$this->modifiedColumns[] = PersonPeer::COMMENTS;
		}

	} // setComment()

	/**
	 * Set the value of [e_mail] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setEMail($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->e_mail !== $v) {
			$this->e_mail = $v;
			$this->modifiedColumns[] = PersonPeer::E_MAIL;
		}

	} // setEMail()

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
			$this->modifiedColumns[] = PersonPeer::FAX;
		}

	} // setFax()

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
			$this->modifiedColumns[] = PersonPeer::FIRST_NAME;
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
			$this->modifiedColumns[] = PersonPeer::LAST_NAME;
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
			$this->modifiedColumns[] = PersonPeer::PHONE;
		}

	} // setPhone()

	/**
	 * Set the value of [user_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setUserName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->user_name !== $v) {
			$this->user_name = $v;
			$this->modifiedColumns[] = PersonPeer::USER_NAME;
		}

	} // setUserName()

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

			$this->address = $rs->getString($startcol + 1);

			$this->admin_status = $rs->getFloat($startcol + 2);

			$this->category = $rs->getString($startcol + 3);

			$this->comments = $rs->getString($startcol + 4);

			$this->e_mail = $rs->getString($startcol + 5);

			$this->fax = $rs->getString($startcol + 6);

			$this->first_name = $rs->getString($startcol + 7);

			$this->last_name = $rs->getString($startcol + 8);

			$this->phone = $rs->getString($startcol + 9);

			$this->user_name = $rs->getString($startcol + 10);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 11; // 11 = PersonPeer::NUM_COLUMNS - PersonPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Person object", $e);
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
			$con = Propel::getConnection(PersonPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			PersonPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(PersonPeer::DATABASE_NAME);
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
					$pk = PersonPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += PersonPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collAuthorizations !== null) {
				foreach($this->collAuthorizations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collPersonEntityRoles !== null) {
				foreach($this->collPersonEntityRoles as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collProjects !== null) {
				foreach($this->collProjects as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collPermissionsViews !== null) {
				foreach($this->collPermissionsViews as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collLogEntrys !== null) {
				foreach($this->collLogEntrys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collAnnotations !== null) {
				foreach($this->collAnnotations as $referrerFK) {
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


			if (($retval = PersonPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAuthorizations !== null) {
					foreach($this->collAuthorizations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collPersonEntityRoles !== null) {
					foreach($this->collPersonEntityRoles as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collProjects !== null) {
					foreach($this->collProjects as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collPermissionsViews !== null) {
					foreach($this->collPermissionsViews as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collLogEntrys !== null) {
					foreach($this->collLogEntrys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAnnotations !== null) {
					foreach($this->collAnnotations as $referrerFK) {
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
		$pos = PersonPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAddress();
				break;
			case 2:
				return $this->getAdminStatus();
				break;
			case 3:
				return $this->getCategory();
				break;
			case 4:
				return $this->getComment();
				break;
			case 5:
				return $this->getEMail();
				break;
			case 6:
				return $this->getFax();
				break;
			case 7:
				return $this->getFirstName();
				break;
			case 8:
				return $this->getLastName();
				break;
			case 9:
				return $this->getPhone();
				break;
			case 10:
				return $this->getUserName();
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
		$keys = PersonPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getAddress(),
			$keys[2] => $this->getAdminStatus(),
			$keys[3] => $this->getCategory(),
			$keys[4] => $this->getComment(),
			$keys[5] => $this->getEMail(),
			$keys[6] => $this->getFax(),
			$keys[7] => $this->getFirstName(),
			$keys[8] => $this->getLastName(),
			$keys[9] => $this->getPhone(),
			$keys[10] => $this->getUserName(),
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
		$pos = PersonPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAddress($value);
				break;
			case 2:
				$this->setAdminStatus($value);
				break;
			case 3:
				$this->setCategory($value);
				break;
			case 4:
				$this->setComment($value);
				break;
			case 5:
				$this->setEMail($value);
				break;
			case 6:
				$this->setFax($value);
				break;
			case 7:
				$this->setFirstName($value);
				break;
			case 8:
				$this->setLastName($value);
				break;
			case 9:
				$this->setPhone($value);
				break;
			case 10:
				$this->setUserName($value);
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
		$keys = PersonPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAddress($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAdminStatus($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCategory($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setComment($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setEMail($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setFax($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setFirstName($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setLastName($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setPhone($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setUserName($arr[$keys[10]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(PersonPeer::DATABASE_NAME);

		if ($this->isColumnModified(PersonPeer::ID)) $criteria->add(PersonPeer::ID, $this->id);
		if ($this->isColumnModified(PersonPeer::ADDRESS)) $criteria->add(PersonPeer::ADDRESS, $this->address);
		if ($this->isColumnModified(PersonPeer::ADMIN_STATUS)) $criteria->add(PersonPeer::ADMIN_STATUS, $this->admin_status);
		if ($this->isColumnModified(PersonPeer::CATEGORY)) $criteria->add(PersonPeer::CATEGORY, $this->category);
		if ($this->isColumnModified(PersonPeer::COMMENTS)) $criteria->add(PersonPeer::COMMENTS, $this->comments);
		if ($this->isColumnModified(PersonPeer::E_MAIL)) $criteria->add(PersonPeer::E_MAIL, $this->e_mail);
		if ($this->isColumnModified(PersonPeer::FAX)) $criteria->add(PersonPeer::FAX, $this->fax);
		if ($this->isColumnModified(PersonPeer::FIRST_NAME)) $criteria->add(PersonPeer::FIRST_NAME, $this->first_name);
		if ($this->isColumnModified(PersonPeer::LAST_NAME)) $criteria->add(PersonPeer::LAST_NAME, $this->last_name);
		if ($this->isColumnModified(PersonPeer::PHONE)) $criteria->add(PersonPeer::PHONE, $this->phone);
		if ($this->isColumnModified(PersonPeer::USER_NAME)) $criteria->add(PersonPeer::USER_NAME, $this->user_name);

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
		$criteria = new Criteria(PersonPeer::DATABASE_NAME);

		$criteria->add(PersonPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of Person (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAddress($this->address);

		$copyObj->setAdminStatus($this->admin_status);

		$copyObj->setCategory($this->category);

		$copyObj->setComment($this->comments);

		$copyObj->setEMail($this->e_mail);

		$copyObj->setFax($this->fax);

		$copyObj->setFirstName($this->first_name);

		$copyObj->setLastName($this->last_name);

		$copyObj->setPhone($this->phone);

		$copyObj->setUserName($this->user_name);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getAuthorizations() as $relObj) {
				$copyObj->addAuthorization($relObj->copy($deepCopy));
			}

			foreach($this->getPersonEntityRoles() as $relObj) {
				$copyObj->addPersonEntityRole($relObj->copy($deepCopy));
			}

			foreach($this->getProjects() as $relObj) {
				$copyObj->addProject($relObj->copy($deepCopy));
			}

			foreach($this->getPermissionsViews() as $relObj) {
				$copyObj->addPermissionsView($relObj->copy($deepCopy));
			}

			foreach($this->getLogEntrys() as $relObj) {
				$copyObj->addLogEntry($relObj->copy($deepCopy));
			}

			foreach($this->getAnnotations() as $relObj) {
				$copyObj->addAnnotation($relObj->copy($deepCopy));
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
	 * @return     Person Clone of current object.
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
	 * @return     PersonPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new PersonPeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collAuthorizations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initAuthorizations()
	{
		if ($this->collAuthorizations === null) {
			$this->collAuthorizations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Person has previously
	 * been saved, it will retrieve related Authorizations from storage.
	 * If this Person is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getAuthorizations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAuthorizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAuthorizations === null) {
			if ($this->isNew()) {
			   $this->collAuthorizations = array();
			} else {

				$criteria->add(AuthorizationPeer::PERSON_ID, $this->getId());

				AuthorizationPeer::addSelectColumns($criteria);
				$this->collAuthorizations = AuthorizationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AuthorizationPeer::PERSON_ID, $this->getId());

				AuthorizationPeer::addSelectColumns($criteria);
				if (!isset($this->lastAuthorizationCriteria) || !$this->lastAuthorizationCriteria->equals($criteria)) {
					$this->collAuthorizations = AuthorizationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAuthorizationCriteria = $criteria;
		return $this->collAuthorizations;
	}

	/**
	 * Returns the number of related Authorizations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countAuthorizations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAuthorizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(AuthorizationPeer::PERSON_ID, $this->getId());

		return AuthorizationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Authorization object to this object
	 * through the Authorization foreign key attribute
	 *
	 * @param      Authorization $l Authorization
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAuthorization(Authorization $l)
	{
		$this->collAuthorizations[] = $l;
		$l->setPerson($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Person is new, it will return
	 * an empty collection; or if this Person has previously
	 * been saved, it will retrieve related Authorizations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Person.
	 */
	public function getAuthorizationsJoinEntityType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAuthorizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAuthorizations === null) {
			if ($this->isNew()) {
				$this->collAuthorizations = array();
			} else {

				$criteria->add(AuthorizationPeer::PERSON_ID, $this->getId());

				$this->collAuthorizations = AuthorizationPeer::doSelectJoinEntityType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AuthorizationPeer::PERSON_ID, $this->getId());

			if (!isset($this->lastAuthorizationCriteria) || !$this->lastAuthorizationCriteria->equals($criteria)) {
				$this->collAuthorizations = AuthorizationPeer::doSelectJoinEntityType($criteria, $con);
			}
		}
		$this->lastAuthorizationCriteria = $criteria;

		return $this->collAuthorizations;
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
	 * Otherwise if this Person has previously
	 * been saved, it will retrieve related PersonEntityRoles from storage.
	 * If this Person is new, it will return
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

				$criteria->add(PersonEntityRolePeer::PERSON_ID, $this->getId());

				PersonEntityRolePeer::addSelectColumns($criteria);
				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(PersonEntityRolePeer::PERSON_ID, $this->getId());

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

		$criteria->add(PersonEntityRolePeer::PERSON_ID, $this->getId());

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
		$l->setPerson($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Person is new, it will return
	 * an empty collection; or if this Person has previously
	 * been saved, it will retrieve related PersonEntityRoles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Person.
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

				$criteria->add(PersonEntityRolePeer::PERSON_ID, $this->getId());

				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelectJoinEntityType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(PersonEntityRolePeer::PERSON_ID, $this->getId());

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
	 * Otherwise if this Person is new, it will return
	 * an empty collection; or if this Person has previously
	 * been saved, it will retrieve related PersonEntityRoles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Person.
	 */
	public function getPersonEntityRolesJoinRole($criteria = null, $con = null)
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

				$criteria->add(PersonEntityRolePeer::PERSON_ID, $this->getId());

				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelectJoinRole($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(PersonEntityRolePeer::PERSON_ID, $this->getId());

			if (!isset($this->lastPersonEntityRoleCriteria) || !$this->lastPersonEntityRoleCriteria->equals($criteria)) {
				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelectJoinRole($criteria, $con);
			}
		}
		$this->lastPersonEntityRoleCriteria = $criteria;

		return $this->collPersonEntityRoles;
	}

	/**
	 * Temporary storage of collProjects to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initProjects()
	{
		if ($this->collProjects === null) {
			$this->collProjects = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Person has previously
	 * been saved, it will retrieve related Projects from storage.
	 * If this Person is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getProjects($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjects === null) {
			if ($this->isNew()) {
			   $this->collProjects = array();
			} else {

				$criteria->add(ProjectPeer::CREATOR_ID, $this->getId());

				ProjectPeer::addSelectColumns($criteria);
				$this->collProjects = ProjectPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ProjectPeer::CREATOR_ID, $this->getId());

				ProjectPeer::addSelectColumns($criteria);
				if (!isset($this->lastProjectCriteria) || !$this->lastProjectCriteria->equals($criteria)) {
					$this->collProjects = ProjectPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastProjectCriteria = $criteria;
		return $this->collProjects;
	}

	/**
	 * Returns the number of related Projects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countProjects($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ProjectPeer::CREATOR_ID, $this->getId());

		return ProjectPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Project object to this object
	 * through the Project foreign key attribute
	 *
	 * @param      Project $l Project
	 * @return     void
	 * @throws     PropelException
	 */
	public function addProject(Project $l)
	{
		$this->collProjects[] = $l;
		$l->setPerson($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Person is new, it will return
	 * an empty collection; or if this Person has previously
	 * been saved, it will retrieve related Projects from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Person.
	 */
	public function getProjectsJoinProjectRelatedBySuperProjectId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjects === null) {
			if ($this->isNew()) {
				$this->collProjects = array();
			} else {

				$criteria->add(ProjectPeer::CREATOR_ID, $this->getId());

				$this->collProjects = ProjectPeer::doSelectJoinProjectRelatedBySuperProjectId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ProjectPeer::CREATOR_ID, $this->getId());

			if (!isset($this->lastProjectCriteria) || !$this->lastProjectCriteria->equals($criteria)) {
				$this->collProjects = ProjectPeer::doSelectJoinProjectRelatedBySuperProjectId($criteria, $con);
			}
		}
		$this->lastProjectCriteria = $criteria;

		return $this->collProjects;
	}

	/**
	 * Temporary storage of collPermissionsViews to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initPermissionsViews()
	{
		if ($this->collPermissionsViews === null) {
			$this->collPermissionsViews = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Person has previously
	 * been saved, it will retrieve related PermissionsViews from storage.
	 * If this Person is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getPermissionsViews($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/security/om/BasePermissionsViewPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collPermissionsViews === null) {
			if ($this->isNew()) {
			   $this->collPermissionsViews = array();
			} else {

				$criteria->add(PermissionsViewPeer::PERSON_ID, $this->getId());

				PermissionsViewPeer::addSelectColumns($criteria);
				$this->collPermissionsViews = PermissionsViewPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(PermissionsViewPeer::PERSON_ID, $this->getId());

				PermissionsViewPeer::addSelectColumns($criteria);
				if (!isset($this->lastPermissionsViewCriteria) || !$this->lastPermissionsViewCriteria->equals($criteria)) {
					$this->collPermissionsViews = PermissionsViewPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastPermissionsViewCriteria = $criteria;
		return $this->collPermissionsViews;
	}

	/**
	 * Returns the number of related PermissionsViews.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countPermissionsViews($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/security/om/BasePermissionsViewPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(PermissionsViewPeer::PERSON_ID, $this->getId());

		return PermissionsViewPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a PermissionsView object to this object
	 * through the PermissionsView foreign key attribute
	 *
	 * @param      PermissionsView $l PermissionsView
	 * @return     void
	 * @throws     PropelException
	 */
	public function addPermissionsView(PermissionsView $l)
	{
		$this->collPermissionsViews[] = $l;
		$l->setPerson($this);
	}

	/**
	 * Temporary storage of collLogEntrys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLogEntrys()
	{
		if ($this->collLogEntrys === null) {
			$this->collLogEntrys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Person has previously
	 * been saved, it will retrieve related LogEntrys from storage.
	 * If this Person is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLogEntrys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/util/om/BaseLogEntryPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLogEntrys === null) {
			if ($this->isNew()) {
			   $this->collLogEntrys = array();
			} else {

				$criteria->add(LogEntryPeer::PERSON_ID, $this->getId());

				LogEntryPeer::addSelectColumns($criteria);
				$this->collLogEntrys = LogEntryPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LogEntryPeer::PERSON_ID, $this->getId());

				LogEntryPeer::addSelectColumns($criteria);
				if (!isset($this->lastLogEntryCriteria) || !$this->lastLogEntryCriteria->equals($criteria)) {
					$this->collLogEntrys = LogEntryPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLogEntryCriteria = $criteria;
		return $this->collLogEntrys;
	}

	/**
	 * Returns the number of related LogEntrys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLogEntrys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/util/om/BaseLogEntryPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(LogEntryPeer::PERSON_ID, $this->getId());

		return LogEntryPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a LogEntry object to this object
	 * through the LogEntry foreign key attribute
	 *
	 * @param      LogEntry $l LogEntry
	 * @return     void
	 * @throws     PropelException
	 */
	public function addLogEntry(LogEntry $l)
	{
		$this->collLogEntrys[] = $l;
		$l->setPerson($this);
	}

	/**
	 * Temporary storage of collAnnotations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initAnnotations()
	{
		if ($this->collAnnotations === null) {
			$this->collAnnotations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Person has previously
	 * been saved, it will retrieve related Annotations from storage.
	 * If this Person is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getAnnotations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAnnotationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAnnotations === null) {
			if ($this->isNew()) {
			   $this->collAnnotations = array();
			} else {

				$criteria->add(AnnotationPeer::CREATOR_ID, $this->getId());

				AnnotationPeer::addSelectColumns($criteria);
				$this->collAnnotations = AnnotationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AnnotationPeer::CREATOR_ID, $this->getId());

				AnnotationPeer::addSelectColumns($criteria);
				if (!isset($this->lastAnnotationCriteria) || !$this->lastAnnotationCriteria->equals($criteria)) {
					$this->collAnnotations = AnnotationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAnnotationCriteria = $criteria;
		return $this->collAnnotations;
	}

	/**
	 * Returns the number of related Annotations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countAnnotations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAnnotationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(AnnotationPeer::CREATOR_ID, $this->getId());

		return AnnotationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Annotation object to this object
	 * through the Annotation foreign key attribute
	 *
	 * @param      Annotation $l Annotation
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAnnotation(Annotation $l)
	{
		$this->collAnnotations[] = $l;
		$l->setPerson($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Person is new, it will return
	 * an empty collection; or if this Person has previously
	 * been saved, it will retrieve related Annotations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Person.
	 */
	public function getAnnotationsJoinEntityTypeRelatedBySubjectTypeId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAnnotationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAnnotations === null) {
			if ($this->isNew()) {
				$this->collAnnotations = array();
			} else {

				$criteria->add(AnnotationPeer::CREATOR_ID, $this->getId());

				$this->collAnnotations = AnnotationPeer::doSelectJoinEntityTypeRelatedBySubjectTypeId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AnnotationPeer::CREATOR_ID, $this->getId());

			if (!isset($this->lastAnnotationCriteria) || !$this->lastAnnotationCriteria->equals($criteria)) {
				$this->collAnnotations = AnnotationPeer::doSelectJoinEntityTypeRelatedBySubjectTypeId($criteria, $con);
			}
		}
		$this->lastAnnotationCriteria = $criteria;

		return $this->collAnnotations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Person is new, it will return
	 * an empty collection; or if this Person has previously
	 * been saved, it will retrieve related Annotations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Person.
	 */
	public function getAnnotationsJoinEntityTypeRelatedByObjectTypeId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAnnotationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAnnotations === null) {
			if ($this->isNew()) {
				$this->collAnnotations = array();
			} else {

				$criteria->add(AnnotationPeer::CREATOR_ID, $this->getId());

				$this->collAnnotations = AnnotationPeer::doSelectJoinEntityTypeRelatedByObjectTypeId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AnnotationPeer::CREATOR_ID, $this->getId());

			if (!isset($this->lastAnnotationCriteria) || !$this->lastAnnotationCriteria->equals($criteria)) {
				$this->collAnnotations = AnnotationPeer::doSelectJoinEntityTypeRelatedByObjectTypeId($criteria, $con);
			}
		}
		$this->lastAnnotationCriteria = $criteria;

		return $this->collAnnotations;
	}

} // BasePerson
