<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/ProjectHomepagePeer.php';

/**
 * Base class that represents a row from the 'PROJECT_HOMEPAGE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseProjectHomepage extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ProjectHomepagePeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the project_id field.
	 * @var        double
	 */
	protected $project_id;


	/**
	 * The value for the data_file_id field.
	 * @var        double
	 */
	protected $data_file_id;


	/**
	 * The value for the caption field.
	 * @var        string
	 */
	protected $caption;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the url field.
	 * @var        string
	 */
	protected $url;


	/**
	 * The value for the project_homepage_type_id field.
	 * @var        double
	 */
	protected $project_homepage_type_id;

	/**
	 * @var        Project
	 */
	protected $aProject;

	/**
	 * @var        DataFile
	 */
	protected $aDataFile;

	/**
	 * @var        ProjectHomepageTypeLookup
	 */
	protected $aProjectHomepageTypeLookup;

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
	 * Get the [project_id] column value.
	 * 
	 * @return     double
	 */
	public function getProjectId()
	{

		return $this->project_id;
	}

	/**
	 * Get the [data_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getDataFileId()
	{

		return $this->data_file_id;
	}

	/**
	 * Get the [caption] column value.
	 * 
	 * @return     string
	 */
	public function getCaption()
	{

		return $this->caption;
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
	 * Get the [url] column value.
	 * 
	 * @return     string
	 */
	public function getUrl()
	{

		return $this->url;
	}

	/**
	 * Get the [project_homepage_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getProjectHomepageTypeId()
	{

		return $this->project_homepage_type_id;
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
			$this->modifiedColumns[] = ProjectHomepagePeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [project_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setProjectId($v)
	{

		if ($this->project_id !== $v) {
			$this->project_id = $v;
			$this->modifiedColumns[] = ProjectHomepagePeer::PROJECT_ID;
		}

		if ($this->aProject !== null && $this->aProject->getId() !== $v) {
			$this->aProject = null;
		}

	} // setProjectId()

	/**
	 * Set the value of [data_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDataFileId($v)
	{

		if ($this->data_file_id !== $v) {
			$this->data_file_id = $v;
			$this->modifiedColumns[] = ProjectHomepagePeer::DATA_FILE_ID;
		}

		if ($this->aDataFile !== null && $this->aDataFile->getId() !== $v) {
			$this->aDataFile = null;
		}

	} // setDataFileId()

	/**
	 * Set the value of [caption] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCaption($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->caption !== $v) {
			$this->caption = $v;
			$this->modifiedColumns[] = ProjectHomepagePeer::CAPTION;
		}

	} // setCaption()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDescription($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = ProjectHomepagePeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [url] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setUrl($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->url !== $v) {
			$this->url = $v;
			$this->modifiedColumns[] = ProjectHomepagePeer::URL;
		}

	} // setUrl()

	/**
	 * Set the value of [project_homepage_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setProjectHomepageTypeId($v)
	{

		if ($this->project_homepage_type_id !== $v) {
			$this->project_homepage_type_id = $v;
			$this->modifiedColumns[] = ProjectHomepagePeer::PROJECT_HOMEPAGE_TYPE_ID;
		}

		if ($this->aProjectHomepageTypeLookup !== null && $this->aProjectHomepageTypeLookup->getId() !== $v) {
			$this->aProjectHomepageTypeLookup = null;
		}

	} // setProjectHomepageTypeId()

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

			$this->project_id = $rs->getFloat($startcol + 1);

			$this->data_file_id = $rs->getFloat($startcol + 2);

			$this->caption = $rs->getString($startcol + 3);

			$this->description = $rs->getString($startcol + 4);

			$this->url = $rs->getString($startcol + 5);

			$this->project_homepage_type_id = $rs->getFloat($startcol + 6);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 7; // 7 = ProjectHomepagePeer::NUM_COLUMNS - ProjectHomepagePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating ProjectHomepage object", $e);
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
			$con = Propel::getConnection(ProjectHomepagePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			ProjectHomepagePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ProjectHomepagePeer::DATABASE_NAME);
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

			if ($this->aProject !== null) {
				if ($this->aProject->isModified()) {
					$affectedRows += $this->aProject->save($con);
				}
				$this->setProject($this->aProject);
			}

			if ($this->aDataFile !== null) {
				if ($this->aDataFile->isModified()) {
					$affectedRows += $this->aDataFile->save($con);
				}
				$this->setDataFile($this->aDataFile);
			}

			if ($this->aProjectHomepageTypeLookup !== null) {
				if ($this->aProjectHomepageTypeLookup->isModified()) {
					$affectedRows += $this->aProjectHomepageTypeLookup->save($con);
				}
				$this->setProjectHomepageTypeLookup($this->aProjectHomepageTypeLookup);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = ProjectHomepagePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += ProjectHomepagePeer::doUpdate($this, $con);
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

			if ($this->aProject !== null) {
				if (!$this->aProject->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aProject->getValidationFailures());
				}
			}

			if ($this->aDataFile !== null) {
				if (!$this->aDataFile->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFile->getValidationFailures());
				}
			}

			if ($this->aProjectHomepageTypeLookup !== null) {
				if (!$this->aProjectHomepageTypeLookup->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aProjectHomepageTypeLookup->getValidationFailures());
				}
			}


			if (($retval = ProjectHomepagePeer::doValidate($this, $columns)) !== true) {
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
		$pos = ProjectHomepagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getProjectId();
				break;
			case 2:
				return $this->getDataFileId();
				break;
			case 3:
				return $this->getCaption();
				break;
			case 4:
				return $this->getDescription();
				break;
			case 5:
				return $this->getUrl();
				break;
			case 6:
				return $this->getProjectHomepageTypeId();
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
		$keys = ProjectHomepagePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getProjectId(),
			$keys[2] => $this->getDataFileId(),
			$keys[3] => $this->getCaption(),
			$keys[4] => $this->getDescription(),
			$keys[5] => $this->getUrl(),
			$keys[6] => $this->getProjectHomepageTypeId(),
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
		$pos = ProjectHomepagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setProjectId($value);
				break;
			case 2:
				$this->setDataFileId($value);
				break;
			case 3:
				$this->setCaption($value);
				break;
			case 4:
				$this->setDescription($value);
				break;
			case 5:
				$this->setUrl($value);
				break;
			case 6:
				$this->setProjectHomepageTypeId($value);
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
		$keys = ProjectHomepagePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setProjectId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDataFileId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCaption($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDescription($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setUrl($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setProjectHomepageTypeId($arr[$keys[6]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ProjectHomepagePeer::DATABASE_NAME);

		if ($this->isColumnModified(ProjectHomepagePeer::ID)) $criteria->add(ProjectHomepagePeer::ID, $this->id);
		if ($this->isColumnModified(ProjectHomepagePeer::PROJECT_ID)) $criteria->add(ProjectHomepagePeer::PROJECT_ID, $this->project_id);
		if ($this->isColumnModified(ProjectHomepagePeer::DATA_FILE_ID)) $criteria->add(ProjectHomepagePeer::DATA_FILE_ID, $this->data_file_id);
		if ($this->isColumnModified(ProjectHomepagePeer::CAPTION)) $criteria->add(ProjectHomepagePeer::CAPTION, $this->caption);
		if ($this->isColumnModified(ProjectHomepagePeer::DESCRIPTION)) $criteria->add(ProjectHomepagePeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(ProjectHomepagePeer::URL)) $criteria->add(ProjectHomepagePeer::URL, $this->url);
		if ($this->isColumnModified(ProjectHomepagePeer::PROJECT_HOMEPAGE_TYPE_ID)) $criteria->add(ProjectHomepagePeer::PROJECT_HOMEPAGE_TYPE_ID, $this->project_homepage_type_id);

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
		$criteria = new Criteria(ProjectHomepagePeer::DATABASE_NAME);

		$criteria->add(ProjectHomepagePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of ProjectHomepage (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setProjectId($this->project_id);

		$copyObj->setDataFileId($this->data_file_id);

		$copyObj->setCaption($this->caption);

		$copyObj->setDescription($this->description);

		$copyObj->setUrl($this->url);

		$copyObj->setProjectHomepageTypeId($this->project_homepage_type_id);


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
	 * @return     ProjectHomepage Clone of current object.
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
	 * @return     ProjectHomepagePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ProjectHomepagePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Project object.
	 *
	 * @param      Project $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setProject($v)
	{


		if ($v === null) {
			$this->setProjectId(NULL);
		} else {
			$this->setProjectId($v->getId());
		}


		$this->aProject = $v;
	}


	/**
	 * Get the associated Project object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Project The associated Project object.
	 * @throws     PropelException
	 */
	public function getProject($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseProjectPeer.php';

		if ($this->aProject === null && ($this->project_id > 0)) {

			$this->aProject = ProjectPeer::retrieveByPK($this->project_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ProjectPeer::retrieveByPK($this->project_id, $con);
			   $obj->addProjects($this);
			 */
		}
		return $this->aProject;
	}

	/**
	 * Declares an association between this object and a DataFile object.
	 *
	 * @param      DataFile $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDataFile($v)
	{


		if ($v === null) {
			$this->setDataFileId(NULL);
		} else {
			$this->setDataFileId($v->getId());
		}


		$this->aDataFile = $v;
	}


	/**
	 * Get the associated DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
	public function getDataFile($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';

		if ($this->aDataFile === null && ($this->data_file_id > 0)) {

			$this->aDataFile = DataFilePeer::retrieveByPK($this->data_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->data_file_id, $con);
			   $obj->addDataFiles($this);
			 */
		}
		return $this->aDataFile;
	}

	/**
	 * Declares an association between this object and a ProjectHomepageTypeLookup object.
	 *
	 * @param      ProjectHomepageTypeLookup $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setProjectHomepageTypeLookup($v)
	{


		if ($v === null) {
			$this->setProjectHomepageTypeId(NULL);
		} else {
			$this->setProjectHomepageTypeId($v->getId());
		}


		$this->aProjectHomepageTypeLookup = $v;
	}


	/**
	 * Get the associated ProjectHomepageTypeLookup object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     ProjectHomepageTypeLookup The associated ProjectHomepageTypeLookup object.
	 * @throws     PropelException
	 */
	public function getProjectHomepageTypeLookup($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseProjectHomepageTypeLookupPeer.php';

		if ($this->aProjectHomepageTypeLookup === null && ($this->project_homepage_type_id > 0)) {

			$this->aProjectHomepageTypeLookup = ProjectHomepageTypeLookupPeer::retrieveByPK($this->project_homepage_type_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ProjectHomepageTypeLookupPeer::retrieveByPK($this->project_homepage_type_id, $con);
			   $obj->addProjectHomepageTypeLookups($this);
			 */
		}
		return $this->aProjectHomepageTypeLookup;
	}

} // BaseProjectHomepage
