<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/tsunami/TsunamiProjectPeer.php';

/**
 * Base class that represents a row from the 'TSUNAMI_PROJECT' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiProject extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        TsunamiProjectPeer
	 */
	protected static $peer;


	/**
	 * The value for the tsunami_project_id field.
	 * @var        double
	 */
	protected $tsunami_project_id;


	/**
	 * The value for the co_pi field.
	 * @var        string
	 */
	protected $co_pi;


	/**
	 * The value for the co_pi_institution field.
	 * @var        string
	 */
	protected $co_pi_institution;


	/**
	 * The value for the collaborators field.
	 * @var        string
	 */
	protected $collaborators;


	/**
	 * The value for the contact_email field.
	 * @var        string
	 */
	protected $contact_email;


	/**
	 * The value for the contact_name field.
	 * @var        string
	 */
	protected $contact_name;


	/**
	 * The value for the deleted field.
	 * @var        double
	 */
	protected $deleted;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the nsf_title field.
	 * @var        string
	 */
	protected $nsf_title;


	/**
	 * The value for the pi field.
	 * @var        string
	 */
	protected $pi;


	/**
	 * The value for the pi_institution field.
	 * @var        string
	 */
	protected $pi_institution;


	/**
	 * The value for the public_data field.
	 * @var        double
	 */
	protected $public_data;


	/**
	 * The value for the short_title field.
	 * @var        string
	 */
	protected $short_title;


	/**
	 * The value for the status field.
	 * @var        string
	 */
	protected $status;


	/**
	 * The value for the sysadmin_email field.
	 * @var        string
	 */
	protected $sysadmin_email;


	/**
	 * The value for the sysadmin_name field.
	 * @var        string
	 */
	protected $sysadmin_name;


	/**
	 * The value for the viewable field.
	 * @var        string
	 */
	protected $viewable;

	/**
	 * Collection to store aggregation of collTsunamiDocLibs.
	 * @var        array
	 */
	protected $collTsunamiDocLibs;

	/**
	 * The criteria used to select the current contents of collTsunamiDocLibs.
	 * @var        Criteria
	 */
	protected $lastTsunamiDocLibCriteria = null;

	/**
	 * Collection to store aggregation of collTsunamiSites.
	 * @var        array
	 */
	protected $collTsunamiSites;

	/**
	 * The criteria used to select the current contents of collTsunamiSites.
	 * @var        Criteria
	 */
	protected $lastTsunamiSiteCriteria = null;

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
	 * Get the [tsunami_project_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->tsunami_project_id;
	}

	/**
	 * Get the [co_pi] column value.
	 * 
	 * @return     string
	 */
	public function getCoPi()
	{

		return $this->co_pi;
	}

	/**
	 * Get the [co_pi_institution] column value.
	 * 
	 * @return     string
	 */
	public function getCoPiInstitution()
	{

		return $this->co_pi_institution;
	}

	/**
	 * Get the [collaborators] column value.
	 * 
	 * @return     string
	 */
	public function getCollaborators()
	{

		return $this->collaborators;
	}

	/**
	 * Get the [contact_email] column value.
	 * 
	 * @return     string
	 */
	public function getContactEmail()
	{

		return $this->contact_email;
	}

	/**
	 * Get the [contact_name] column value.
	 * 
	 * @return     string
	 */
	public function getContactName()
	{

		return $this->contact_name;
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
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
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
	 * Get the [nsf_title] column value.
	 * 
	 * @return     string
	 */
	public function getNsfTitle()
	{

		return $this->nsf_title;
	}

	/**
	 * Get the [pi] column value.
	 * 
	 * @return     string
	 */
	public function getPi()
	{

		return $this->pi;
	}

	/**
	 * Get the [pi_institution] column value.
	 * 
	 * @return     string
	 */
	public function getPiInstitution()
	{

		return $this->pi_institution;
	}

	/**
	 * Get the [public_data] column value.
	 * 
	 * @return     double
	 */
	public function getPublicData()
	{

		return $this->public_data;
	}

	/**
	 * Get the [short_title] column value.
	 * 
	 * @return     string
	 */
	public function getShortTitle()
	{

		return $this->short_title;
	}

	/**
	 * Get the [status] column value.
	 * 
	 * @return     string
	 */
	public function getStatus()
	{

		return $this->status;
	}

	/**
	 * Get the [sysadmin_email] column value.
	 * 
	 * @return     string
	 */
	public function getSysadminEmail()
	{

		return $this->sysadmin_email;
	}

	/**
	 * Get the [sysadmin_name] column value.
	 * 
	 * @return     string
	 */
	public function getSysadminName()
	{

		return $this->sysadmin_name;
	}

	/**
	 * Get the [viewable] column value.
	 * 
	 * @return     string
	 */
	public function getView()
	{

		return $this->viewable;
	}

	/**
	 * Set the value of [tsunami_project_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->tsunami_project_id !== $v) {
			$this->tsunami_project_id = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::TSUNAMI_PROJECT_ID;
		}

	} // setId()

	/**
	 * Set the value of [co_pi] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCoPi($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->co_pi !== $v) {
			$this->co_pi = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::CO_PI;
		}

	} // setCoPi()

	/**
	 * Set the value of [co_pi_institution] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCoPiInstitution($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->co_pi_institution !== $v) {
			$this->co_pi_institution = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::CO_PI_INSTITUTION;
		}

	} // setCoPiInstitution()

	/**
	 * Set the value of [collaborators] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCollaborators($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->collaborators !== $v) {
			$this->collaborators = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::COLLABORATORS;
		}

	} // setCollaborators()

	/**
	 * Set the value of [contact_email] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactEmail($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->contact_email !== $v) {
			$this->contact_email = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::CONTACT_EMAIL;
		}

	} // setContactEmail()

	/**
	 * Set the value of [contact_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->contact_name !== $v) {
			$this->contact_name = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::CONTACT_NAME;
		}

	} // setContactName()

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
			$this->modifiedColumns[] = TsunamiProjectPeer::DELETED;
		}

	} // setDeleted()

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
			$this->modifiedColumns[] = TsunamiProjectPeer::DESCRIPTION;
		}

	} // setDescription()

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
			$this->modifiedColumns[] = TsunamiProjectPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [nsf_title] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNsfTitle($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->nsf_title !== $v) {
			$this->nsf_title = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::NSF_TITLE;
		}

	} // setNsfTitle()

	/**
	 * Set the value of [pi] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPi($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->pi !== $v) {
			$this->pi = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::PI;
		}

	} // setPi()

	/**
	 * Set the value of [pi_institution] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPiInstitution($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->pi_institution !== $v) {
			$this->pi_institution = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::PI_INSTITUTION;
		}

	} // setPiInstitution()

	/**
	 * Set the value of [public_data] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPublicData($v)
	{

		if ($this->public_data !== $v) {
			$this->public_data = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::PUBLIC_DATA;
		}

	} // setPublicData()

	/**
	 * Set the value of [short_title] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setShortTitle($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->short_title !== $v) {
			$this->short_title = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::SHORT_TITLE;
		}

	} // setShortTitle()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::STATUS;
		}

	} // setStatus()

	/**
	 * Set the value of [sysadmin_email] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSysadminEmail($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sysadmin_email !== $v) {
			$this->sysadmin_email = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::SYSADMIN_EMAIL;
		}

	} // setSysadminEmail()

	/**
	 * Set the value of [sysadmin_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSysadminName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sysadmin_name !== $v) {
			$this->sysadmin_name = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::SYSADMIN_NAME;
		}

	} // setSysadminName()

	/**
	 * Set the value of [viewable] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setView($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->viewable !== $v) {
			$this->viewable = $v;
			$this->modifiedColumns[] = TsunamiProjectPeer::VIEWABLE;
		}

	} // setView()

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

			$this->tsunami_project_id = $rs->getFloat($startcol + 0);

			$this->co_pi = $rs->getString($startcol + 1);

			$this->co_pi_institution = $rs->getString($startcol + 2);

			$this->collaborators = $rs->getString($startcol + 3);

			$this->contact_email = $rs->getString($startcol + 4);

			$this->contact_name = $rs->getString($startcol + 5);

			$this->deleted = $rs->getFloat($startcol + 6);

			$this->description = $rs->getClob($startcol + 7);

			$this->name = $rs->getString($startcol + 8);

			$this->nsf_title = $rs->getString($startcol + 9);

			$this->pi = $rs->getString($startcol + 10);

			$this->pi_institution = $rs->getString($startcol + 11);

			$this->public_data = $rs->getFloat($startcol + 12);

			$this->short_title = $rs->getString($startcol + 13);

			$this->status = $rs->getString($startcol + 14);

			$this->sysadmin_email = $rs->getString($startcol + 15);

			$this->sysadmin_name = $rs->getString($startcol + 16);

			$this->viewable = $rs->getString($startcol + 17);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 18; // 18 = TsunamiProjectPeer::NUM_COLUMNS - TsunamiProjectPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating TsunamiProject object", $e);
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
			$con = Propel::getConnection(TsunamiProjectPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			TsunamiProjectPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(TsunamiProjectPeer::DATABASE_NAME);
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
					$pk = TsunamiProjectPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += TsunamiProjectPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collTsunamiDocLibs !== null) {
				foreach($this->collTsunamiDocLibs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collTsunamiSites !== null) {
				foreach($this->collTsunamiSites as $referrerFK) {
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


			if (($retval = TsunamiProjectPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collTsunamiDocLibs !== null) {
					foreach($this->collTsunamiDocLibs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collTsunamiSites !== null) {
					foreach($this->collTsunamiSites as $referrerFK) {
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
		$pos = TsunamiProjectPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCoPi();
				break;
			case 2:
				return $this->getCoPiInstitution();
				break;
			case 3:
				return $this->getCollaborators();
				break;
			case 4:
				return $this->getContactEmail();
				break;
			case 5:
				return $this->getContactName();
				break;
			case 6:
				return $this->getDeleted();
				break;
			case 7:
				return $this->getDescription();
				break;
			case 8:
				return $this->getName();
				break;
			case 9:
				return $this->getNsfTitle();
				break;
			case 10:
				return $this->getPi();
				break;
			case 11:
				return $this->getPiInstitution();
				break;
			case 12:
				return $this->getPublicData();
				break;
			case 13:
				return $this->getShortTitle();
				break;
			case 14:
				return $this->getStatus();
				break;
			case 15:
				return $this->getSysadminEmail();
				break;
			case 16:
				return $this->getSysadminName();
				break;
			case 17:
				return $this->getView();
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
		$keys = TsunamiProjectPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCoPi(),
			$keys[2] => $this->getCoPiInstitution(),
			$keys[3] => $this->getCollaborators(),
			$keys[4] => $this->getContactEmail(),
			$keys[5] => $this->getContactName(),
			$keys[6] => $this->getDeleted(),
			$keys[7] => $this->getDescription(),
			$keys[8] => $this->getName(),
			$keys[9] => $this->getNsfTitle(),
			$keys[10] => $this->getPi(),
			$keys[11] => $this->getPiInstitution(),
			$keys[12] => $this->getPublicData(),
			$keys[13] => $this->getShortTitle(),
			$keys[14] => $this->getStatus(),
			$keys[15] => $this->getSysadminEmail(),
			$keys[16] => $this->getSysadminName(),
			$keys[17] => $this->getView(),
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
		$pos = TsunamiProjectPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCoPi($value);
				break;
			case 2:
				$this->setCoPiInstitution($value);
				break;
			case 3:
				$this->setCollaborators($value);
				break;
			case 4:
				$this->setContactEmail($value);
				break;
			case 5:
				$this->setContactName($value);
				break;
			case 6:
				$this->setDeleted($value);
				break;
			case 7:
				$this->setDescription($value);
				break;
			case 8:
				$this->setName($value);
				break;
			case 9:
				$this->setNsfTitle($value);
				break;
			case 10:
				$this->setPi($value);
				break;
			case 11:
				$this->setPiInstitution($value);
				break;
			case 12:
				$this->setPublicData($value);
				break;
			case 13:
				$this->setShortTitle($value);
				break;
			case 14:
				$this->setStatus($value);
				break;
			case 15:
				$this->setSysadminEmail($value);
				break;
			case 16:
				$this->setSysadminName($value);
				break;
			case 17:
				$this->setView($value);
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
		$keys = TsunamiProjectPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCoPi($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCoPiInstitution($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCollaborators($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setContactEmail($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setContactName($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDeleted($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDescription($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setName($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setNsfTitle($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setPi($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setPiInstitution($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setPublicData($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setShortTitle($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setStatus($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setSysadminEmail($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setSysadminName($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setView($arr[$keys[17]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(TsunamiProjectPeer::DATABASE_NAME);

		if ($this->isColumnModified(TsunamiProjectPeer::TSUNAMI_PROJECT_ID)) $criteria->add(TsunamiProjectPeer::TSUNAMI_PROJECT_ID, $this->tsunami_project_id);
		if ($this->isColumnModified(TsunamiProjectPeer::CO_PI)) $criteria->add(TsunamiProjectPeer::CO_PI, $this->co_pi);
		if ($this->isColumnModified(TsunamiProjectPeer::CO_PI_INSTITUTION)) $criteria->add(TsunamiProjectPeer::CO_PI_INSTITUTION, $this->co_pi_institution);
		if ($this->isColumnModified(TsunamiProjectPeer::COLLABORATORS)) $criteria->add(TsunamiProjectPeer::COLLABORATORS, $this->collaborators);
		if ($this->isColumnModified(TsunamiProjectPeer::CONTACT_EMAIL)) $criteria->add(TsunamiProjectPeer::CONTACT_EMAIL, $this->contact_email);
		if ($this->isColumnModified(TsunamiProjectPeer::CONTACT_NAME)) $criteria->add(TsunamiProjectPeer::CONTACT_NAME, $this->contact_name);
		if ($this->isColumnModified(TsunamiProjectPeer::DELETED)) $criteria->add(TsunamiProjectPeer::DELETED, $this->deleted);
		if ($this->isColumnModified(TsunamiProjectPeer::DESCRIPTION)) $criteria->add(TsunamiProjectPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(TsunamiProjectPeer::NAME)) $criteria->add(TsunamiProjectPeer::NAME, $this->name);
		if ($this->isColumnModified(TsunamiProjectPeer::NSF_TITLE)) $criteria->add(TsunamiProjectPeer::NSF_TITLE, $this->nsf_title);
		if ($this->isColumnModified(TsunamiProjectPeer::PI)) $criteria->add(TsunamiProjectPeer::PI, $this->pi);
		if ($this->isColumnModified(TsunamiProjectPeer::PI_INSTITUTION)) $criteria->add(TsunamiProjectPeer::PI_INSTITUTION, $this->pi_institution);
		if ($this->isColumnModified(TsunamiProjectPeer::PUBLIC_DATA)) $criteria->add(TsunamiProjectPeer::PUBLIC_DATA, $this->public_data);
		if ($this->isColumnModified(TsunamiProjectPeer::SHORT_TITLE)) $criteria->add(TsunamiProjectPeer::SHORT_TITLE, $this->short_title);
		if ($this->isColumnModified(TsunamiProjectPeer::STATUS)) $criteria->add(TsunamiProjectPeer::STATUS, $this->status);
		if ($this->isColumnModified(TsunamiProjectPeer::SYSADMIN_EMAIL)) $criteria->add(TsunamiProjectPeer::SYSADMIN_EMAIL, $this->sysadmin_email);
		if ($this->isColumnModified(TsunamiProjectPeer::SYSADMIN_NAME)) $criteria->add(TsunamiProjectPeer::SYSADMIN_NAME, $this->sysadmin_name);
		if ($this->isColumnModified(TsunamiProjectPeer::VIEWABLE)) $criteria->add(TsunamiProjectPeer::VIEWABLE, $this->viewable);

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
		$criteria = new Criteria(TsunamiProjectPeer::DATABASE_NAME);

		$criteria->add(TsunamiProjectPeer::TSUNAMI_PROJECT_ID, $this->tsunami_project_id);

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
	 * Generic method to set the primary key (tsunami_project_id column).
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
	 * @param      object $copyObj An object of TsunamiProject (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCoPi($this->co_pi);

		$copyObj->setCoPiInstitution($this->co_pi_institution);

		$copyObj->setCollaborators($this->collaborators);

		$copyObj->setContactEmail($this->contact_email);

		$copyObj->setContactName($this->contact_name);

		$copyObj->setDeleted($this->deleted);

		$copyObj->setDescription($this->description);

		$copyObj->setName($this->name);

		$copyObj->setNsfTitle($this->nsf_title);

		$copyObj->setPi($this->pi);

		$copyObj->setPiInstitution($this->pi_institution);

		$copyObj->setPublicData($this->public_data);

		$copyObj->setShortTitle($this->short_title);

		$copyObj->setStatus($this->status);

		$copyObj->setSysadminEmail($this->sysadmin_email);

		$copyObj->setSysadminName($this->sysadmin_name);

		$copyObj->setView($this->viewable);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getTsunamiDocLibs() as $relObj) {
				$copyObj->addTsunamiDocLib($relObj->copy($deepCopy));
			}

			foreach($this->getTsunamiSites() as $relObj) {
				$copyObj->addTsunamiSite($relObj->copy($deepCopy));
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
	 * @return     TsunamiProject Clone of current object.
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
	 * @return     TsunamiProjectPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new TsunamiProjectPeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collTsunamiDocLibs to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTsunamiDocLibs()
	{
		if ($this->collTsunamiDocLibs === null) {
			$this->collTsunamiDocLibs = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiProject has previously
	 * been saved, it will retrieve related TsunamiDocLibs from storage.
	 * If this TsunamiProject is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTsunamiDocLibs($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiDocLibPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTsunamiDocLibs === null) {
			if ($this->isNew()) {
			   $this->collTsunamiDocLibs = array();
			} else {

				$criteria->add(TsunamiDocLibPeer::TSUNAMI_PROJECT_ID, $this->getId());

				TsunamiDocLibPeer::addSelectColumns($criteria);
				$this->collTsunamiDocLibs = TsunamiDocLibPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TsunamiDocLibPeer::TSUNAMI_PROJECT_ID, $this->getId());

				TsunamiDocLibPeer::addSelectColumns($criteria);
				if (!isset($this->lastTsunamiDocLibCriteria) || !$this->lastTsunamiDocLibCriteria->equals($criteria)) {
					$this->collTsunamiDocLibs = TsunamiDocLibPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTsunamiDocLibCriteria = $criteria;
		return $this->collTsunamiDocLibs;
	}

	/**
	 * Returns the number of related TsunamiDocLibs.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTsunamiDocLibs($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiDocLibPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TsunamiDocLibPeer::TSUNAMI_PROJECT_ID, $this->getId());

		return TsunamiDocLibPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a TsunamiDocLib object to this object
	 * through the TsunamiDocLib foreign key attribute
	 *
	 * @param      TsunamiDocLib $l TsunamiDocLib
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTsunamiDocLib(TsunamiDocLib $l)
	{
		$this->collTsunamiDocLibs[] = $l;
		$l->setTsunamiProject($this);
	}

	/**
	 * Temporary storage of collTsunamiSites to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTsunamiSites()
	{
		if ($this->collTsunamiSites === null) {
			$this->collTsunamiSites = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiProject has previously
	 * been saved, it will retrieve related TsunamiSites from storage.
	 * If this TsunamiProject is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTsunamiSites($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiSitePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTsunamiSites === null) {
			if ($this->isNew()) {
			   $this->collTsunamiSites = array();
			} else {

				$criteria->add(TsunamiSitePeer::TSUNAMI_PROJECT_ID, $this->getId());

				TsunamiSitePeer::addSelectColumns($criteria);
				$this->collTsunamiSites = TsunamiSitePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TsunamiSitePeer::TSUNAMI_PROJECT_ID, $this->getId());

				TsunamiSitePeer::addSelectColumns($criteria);
				if (!isset($this->lastTsunamiSiteCriteria) || !$this->lastTsunamiSiteCriteria->equals($criteria)) {
					$this->collTsunamiSites = TsunamiSitePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTsunamiSiteCriteria = $criteria;
		return $this->collTsunamiSites;
	}

	/**
	 * Returns the number of related TsunamiSites.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTsunamiSites($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiSitePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TsunamiSitePeer::TSUNAMI_PROJECT_ID, $this->getId());

		return TsunamiSitePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a TsunamiSite object to this object
	 * through the TsunamiSite foreign key attribute
	 *
	 * @param      TsunamiSite $l TsunamiSite
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTsunamiSite(TsunamiSite $l)
	{
		$this->collTsunamiSites[] = $l;
		$l->setTsunamiProject($this);
	}

} // BaseTsunamiProject
