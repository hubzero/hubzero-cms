<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/EntityTypePeer.php';

/**
 * Base class that represents a row from the 'ENTITY_TYPE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEntityType extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EntityTypePeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the class_name field.
	 * @var        string
	 */
	protected $class_name;


	/**
	 * The value for the n_table_name field.
	 * @var        string
	 */
	protected $n_table_name;


	/**
	 * The value for the table_id_column field.
	 * @var        string
	 */
	protected $table_id_column;

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
	 * Collection to store aggregation of collDataFiles.
	 * @var        array
	 */
	protected $collDataFiles;

	/**
	 * The criteria used to select the current contents of collDataFiles.
	 * @var        Criteria
	 */
	protected $lastDataFileCriteria = null;

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
	 * Collection to store aggregation of collRoles.
	 * @var        array
	 */
	protected $collRoles;

	/**
	 * The criteria used to select the current contents of collRoles.
	 * @var        Criteria
	 */
	protected $lastRoleCriteria = null;

	/**
	 * Collection to store aggregation of collThumbnails.
	 * @var        array
	 */
	protected $collThumbnails;

	/**
	 * The criteria used to select the current contents of collThumbnails.
	 * @var        Criteria
	 */
	protected $lastThumbnailCriteria = null;

	/**
	 * Collection to store aggregation of collAnnotationsRelatedBySubjectTypeId.
	 * @var        array
	 */
	protected $collAnnotationsRelatedBySubjectTypeId;

	/**
	 * The criteria used to select the current contents of collAnnotationsRelatedBySubjectTypeId.
	 * @var        Criteria
	 */
	protected $lastAnnotationRelatedBySubjectTypeIdCriteria = null;

	/**
	 * Collection to store aggregation of collAnnotationsRelatedByObjectTypeId.
	 * @var        array
	 */
	protected $collAnnotationsRelatedByObjectTypeId;

	/**
	 * The criteria used to select the current contents of collAnnotationsRelatedByObjectTypeId.
	 * @var        Criteria
	 */
	protected $lastAnnotationRelatedByObjectTypeIdCriteria = null;

	/**
	 * Collection to store aggregation of collResearcherKeywords.
	 * @var        array
	 */
	protected $collResearcherKeywords;

	/**
	 * The criteria used to select the current contents of collResearcherKeywords.
	 * @var        Criteria
	 */
	protected $lastResearcherKeywordCriteria = null;

	/**
	 * Collection to store aggregation of collEntityActivityLogs.
	 * @var        array
	 */
	protected $collEntityActivityLogs;

	/**
	 * The criteria used to select the current contents of collEntityActivityLogs.
	 * @var        Criteria
	 */
	protected $lastEntityActivityLogCriteria = null;

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
	 * Get the [class_name] column value.
	 * 
	 * @return     string
	 */
	public function getClassName()
	{

		return $this->class_name;
	}

	/**
	 * Get the [n_table_name] column value.
	 * 
	 * @return     string
	 */
	public function getDatabaseTableName()
	{

		return $this->n_table_name;
	}

	/**
	 * Get the [table_id_column] column value.
	 * 
	 * @return     string
	 */
	public function getTableIdColumn()
	{

		return $this->table_id_column;
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
			$this->modifiedColumns[] = EntityTypePeer::ID;
		}

	} // setId()

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
			$this->modifiedColumns[] = EntityTypePeer::CLASS_NAME;
		}

	} // setClassName()

	/**
	 * Set the value of [n_table_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDatabaseTableName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->n_table_name !== $v) {
			$this->n_table_name = $v;
			$this->modifiedColumns[] = EntityTypePeer::N_TABLE_NAME;
		}

	} // setDatabaseTableName()

	/**
	 * Set the value of [table_id_column] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTableIdColumn($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->table_id_column !== $v) {
			$this->table_id_column = $v;
			$this->modifiedColumns[] = EntityTypePeer::TABLE_ID_COLUMN;
		}

	} // setTableIdColumn()

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

			$this->class_name = $rs->getString($startcol + 1);

			$this->n_table_name = $rs->getString($startcol + 2);

			$this->table_id_column = $rs->getString($startcol + 3);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 4; // 4 = EntityTypePeer::NUM_COLUMNS - EntityTypePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating EntityType object", $e);
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
			$con = Propel::getConnection(EntityTypePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			EntityTypePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(EntityTypePeer::DATABASE_NAME);
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
					$pk = EntityTypePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += EntityTypePeer::doUpdate($this, $con);
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

			if ($this->collDataFiles !== null) {
				foreach($this->collDataFiles as $referrerFK) {
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

			if ($this->collRoles !== null) {
				foreach($this->collRoles as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collThumbnails !== null) {
				foreach($this->collThumbnails as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collAnnotationsRelatedBySubjectTypeId !== null) {
				foreach($this->collAnnotationsRelatedBySubjectTypeId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collAnnotationsRelatedByObjectTypeId !== null) {
				foreach($this->collAnnotationsRelatedByObjectTypeId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collResearcherKeywords !== null) {
				foreach($this->collResearcherKeywords as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEntityActivityLogs !== null) {
				foreach($this->collEntityActivityLogs as $referrerFK) {
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


			if (($retval = EntityTypePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAuthorizations !== null) {
					foreach($this->collAuthorizations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collDataFiles !== null) {
					foreach($this->collDataFiles as $referrerFK) {
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

				if ($this->collRoles !== null) {
					foreach($this->collRoles as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collThumbnails !== null) {
					foreach($this->collThumbnails as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAnnotationsRelatedBySubjectTypeId !== null) {
					foreach($this->collAnnotationsRelatedBySubjectTypeId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAnnotationsRelatedByObjectTypeId !== null) {
					foreach($this->collAnnotationsRelatedByObjectTypeId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collResearcherKeywords !== null) {
					foreach($this->collResearcherKeywords as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEntityActivityLogs !== null) {
					foreach($this->collEntityActivityLogs as $referrerFK) {
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
		$pos = EntityTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getClassName();
				break;
			case 2:
				return $this->getDatabaseTableName();
				break;
			case 3:
				return $this->getTableIdColumn();
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
		$keys = EntityTypePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getClassName(),
			$keys[2] => $this->getDatabaseTableName(),
			$keys[3] => $this->getTableIdColumn(),
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
		$pos = EntityTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setClassName($value);
				break;
			case 2:
				$this->setDatabaseTableName($value);
				break;
			case 3:
				$this->setTableIdColumn($value);
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
		$keys = EntityTypePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setClassName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDatabaseTableName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setTableIdColumn($arr[$keys[3]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EntityTypePeer::DATABASE_NAME);

		if ($this->isColumnModified(EntityTypePeer::ID)) $criteria->add(EntityTypePeer::ID, $this->id);
		if ($this->isColumnModified(EntityTypePeer::CLASS_NAME)) $criteria->add(EntityTypePeer::CLASS_NAME, $this->class_name);
		if ($this->isColumnModified(EntityTypePeer::N_TABLE_NAME)) $criteria->add(EntityTypePeer::N_TABLE_NAME, $this->n_table_name);
		if ($this->isColumnModified(EntityTypePeer::TABLE_ID_COLUMN)) $criteria->add(EntityTypePeer::TABLE_ID_COLUMN, $this->table_id_column);

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
		$criteria = new Criteria(EntityTypePeer::DATABASE_NAME);

		$criteria->add(EntityTypePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of EntityType (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setClassName($this->class_name);

		$copyObj->setDatabaseTableName($this->n_table_name);

		$copyObj->setTableIdColumn($this->table_id_column);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getAuthorizations() as $relObj) {
				$copyObj->addAuthorization($relObj->copy($deepCopy));
			}

			foreach($this->getDataFiles() as $relObj) {
				$copyObj->addDataFile($relObj->copy($deepCopy));
			}

			foreach($this->getPersonEntityRoles() as $relObj) {
				$copyObj->addPersonEntityRole($relObj->copy($deepCopy));
			}

			foreach($this->getRoles() as $relObj) {
				$copyObj->addRole($relObj->copy($deepCopy));
			}

			foreach($this->getThumbnails() as $relObj) {
				$copyObj->addThumbnail($relObj->copy($deepCopy));
			}

			foreach($this->getAnnotationsRelatedBySubjectTypeId() as $relObj) {
				$copyObj->addAnnotationRelatedBySubjectTypeId($relObj->copy($deepCopy));
			}

			foreach($this->getAnnotationsRelatedByObjectTypeId() as $relObj) {
				$copyObj->addAnnotationRelatedByObjectTypeId($relObj->copy($deepCopy));
			}

			foreach($this->getResearcherKeywords() as $relObj) {
				$copyObj->addResearcherKeyword($relObj->copy($deepCopy));
			}

			foreach($this->getEntityActivityLogs() as $relObj) {
				$copyObj->addEntityActivityLog($relObj->copy($deepCopy));
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
	 * @return     EntityType Clone of current object.
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
	 * @return     EntityTypePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EntityTypePeer();
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
	 * Otherwise if this EntityType has previously
	 * been saved, it will retrieve related Authorizations from storage.
	 * If this EntityType is new, it will return
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

				$criteria->add(AuthorizationPeer::ENTITY_TYPE_ID, $this->getId());

				AuthorizationPeer::addSelectColumns($criteria);
				$this->collAuthorizations = AuthorizationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AuthorizationPeer::ENTITY_TYPE_ID, $this->getId());

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

		$criteria->add(AuthorizationPeer::ENTITY_TYPE_ID, $this->getId());

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
		$l->setEntityType($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType is new, it will return
	 * an empty collection; or if this EntityType has previously
	 * been saved, it will retrieve related Authorizations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EntityType.
	 */
	public function getAuthorizationsJoinPerson($criteria = null, $con = null)
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

				$criteria->add(AuthorizationPeer::ENTITY_TYPE_ID, $this->getId());

				$this->collAuthorizations = AuthorizationPeer::doSelectJoinPerson($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AuthorizationPeer::ENTITY_TYPE_ID, $this->getId());

			if (!isset($this->lastAuthorizationCriteria) || !$this->lastAuthorizationCriteria->equals($criteria)) {
				$this->collAuthorizations = AuthorizationPeer::doSelectJoinPerson($criteria, $con);
			}
		}
		$this->lastAuthorizationCriteria = $criteria;

		return $this->collAuthorizations;
	}

	/**
	 * Temporary storage of collDataFiles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDataFiles()
	{
		if ($this->collDataFiles === null) {
			$this->collDataFiles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType has previously
	 * been saved, it will retrieve related DataFiles from storage.
	 * If this EntityType is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDataFiles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFiles === null) {
			if ($this->isNew()) {
			   $this->collDataFiles = array();
			} else {

				$criteria->add(DataFilePeer::USAGE_TYPE_ID, $this->getId());

				DataFilePeer::addSelectColumns($criteria);
				$this->collDataFiles = DataFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DataFilePeer::USAGE_TYPE_ID, $this->getId());

				DataFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastDataFileCriteria) || !$this->lastDataFileCriteria->equals($criteria)) {
					$this->collDataFiles = DataFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDataFileCriteria = $criteria;
		return $this->collDataFiles;
	}

	/**
	 * Returns the number of related DataFiles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDataFiles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DataFilePeer::USAGE_TYPE_ID, $this->getId());

		return DataFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DataFile object to this object
	 * through the DataFile foreign key attribute
	 *
	 * @param      DataFile $l DataFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDataFile(DataFile $l)
	{
		$this->collDataFiles[] = $l;
		$l->setEntityType($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType is new, it will return
	 * an empty collection; or if this EntityType has previously
	 * been saved, it will retrieve related DataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EntityType.
	 */
	public function getDataFilesJoinDataFileRelatedByThumbId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFiles === null) {
			if ($this->isNew()) {
				$this->collDataFiles = array();
			} else {

				$criteria->add(DataFilePeer::USAGE_TYPE_ID, $this->getId());

				$this->collDataFiles = DataFilePeer::doSelectJoinDataFileRelatedByThumbId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFilePeer::USAGE_TYPE_ID, $this->getId());

			if (!isset($this->lastDataFileCriteria) || !$this->lastDataFileCriteria->equals($criteria)) {
				$this->collDataFiles = DataFilePeer::doSelectJoinDataFileRelatedByThumbId($criteria, $con);
			}
		}
		$this->lastDataFileCriteria = $criteria;

		return $this->collDataFiles;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType is new, it will return
	 * an empty collection; or if this EntityType has previously
	 * been saved, it will retrieve related DataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EntityType.
	 */
	public function getDataFilesJoinDocumentFormat($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFiles === null) {
			if ($this->isNew()) {
				$this->collDataFiles = array();
			} else {

				$criteria->add(DataFilePeer::USAGE_TYPE_ID, $this->getId());

				$this->collDataFiles = DataFilePeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFilePeer::USAGE_TYPE_ID, $this->getId());

			if (!isset($this->lastDataFileCriteria) || !$this->lastDataFileCriteria->equals($criteria)) {
				$this->collDataFiles = DataFilePeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		}
		$this->lastDataFileCriteria = $criteria;

		return $this->collDataFiles;
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
	 * Otherwise if this EntityType has previously
	 * been saved, it will retrieve related PersonEntityRoles from storage.
	 * If this EntityType is new, it will return
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

				$criteria->add(PersonEntityRolePeer::ENTITY_TYPE_ID, $this->getId());

				PersonEntityRolePeer::addSelectColumns($criteria);
				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(PersonEntityRolePeer::ENTITY_TYPE_ID, $this->getId());

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

		$criteria->add(PersonEntityRolePeer::ENTITY_TYPE_ID, $this->getId());

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
		$l->setEntityType($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType is new, it will return
	 * an empty collection; or if this EntityType has previously
	 * been saved, it will retrieve related PersonEntityRoles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EntityType.
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

				$criteria->add(PersonEntityRolePeer::ENTITY_TYPE_ID, $this->getId());

				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelectJoinPerson($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(PersonEntityRolePeer::ENTITY_TYPE_ID, $this->getId());

			if (!isset($this->lastPersonEntityRoleCriteria) || !$this->lastPersonEntityRoleCriteria->equals($criteria)) {
				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelectJoinPerson($criteria, $con);
			}
		}
		$this->lastPersonEntityRoleCriteria = $criteria;

		return $this->collPersonEntityRoles;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType is new, it will return
	 * an empty collection; or if this EntityType has previously
	 * been saved, it will retrieve related PersonEntityRoles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EntityType.
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

				$criteria->add(PersonEntityRolePeer::ENTITY_TYPE_ID, $this->getId());

				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelectJoinRole($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(PersonEntityRolePeer::ENTITY_TYPE_ID, $this->getId());

			if (!isset($this->lastPersonEntityRoleCriteria) || !$this->lastPersonEntityRoleCriteria->equals($criteria)) {
				$this->collPersonEntityRoles = PersonEntityRolePeer::doSelectJoinRole($criteria, $con);
			}
		}
		$this->lastPersonEntityRoleCriteria = $criteria;

		return $this->collPersonEntityRoles;
	}

	/**
	 * Temporary storage of collRoles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initRoles()
	{
		if ($this->collRoles === null) {
			$this->collRoles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType has previously
	 * been saved, it will retrieve related Roles from storage.
	 * If this EntityType is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getRoles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseRolePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collRoles === null) {
			if ($this->isNew()) {
			   $this->collRoles = array();
			} else {

				$criteria->add(RolePeer::ENTITY_TYPE_ID, $this->getId());

				RolePeer::addSelectColumns($criteria);
				$this->collRoles = RolePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(RolePeer::ENTITY_TYPE_ID, $this->getId());

				RolePeer::addSelectColumns($criteria);
				if (!isset($this->lastRoleCriteria) || !$this->lastRoleCriteria->equals($criteria)) {
					$this->collRoles = RolePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastRoleCriteria = $criteria;
		return $this->collRoles;
	}

	/**
	 * Returns the number of related Roles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countRoles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseRolePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(RolePeer::ENTITY_TYPE_ID, $this->getId());

		return RolePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Role object to this object
	 * through the Role foreign key attribute
	 *
	 * @param      Role $l Role
	 * @return     void
	 * @throws     PropelException
	 */
	public function addRole(Role $l)
	{
		$this->collRoles[] = $l;
		$l->setEntityType($this);
	}

	/**
	 * Temporary storage of collThumbnails to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initThumbnails()
	{
		if ($this->collThumbnails === null) {
			$this->collThumbnails = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType has previously
	 * been saved, it will retrieve related Thumbnails from storage.
	 * If this EntityType is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getThumbnails($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseThumbnailPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collThumbnails === null) {
			if ($this->isNew()) {
			   $this->collThumbnails = array();
			} else {

				$criteria->add(ThumbnailPeer::ENTITY_TYPE_ID, $this->getId());

				ThumbnailPeer::addSelectColumns($criteria);
				$this->collThumbnails = ThumbnailPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ThumbnailPeer::ENTITY_TYPE_ID, $this->getId());

				ThumbnailPeer::addSelectColumns($criteria);
				if (!isset($this->lastThumbnailCriteria) || !$this->lastThumbnailCriteria->equals($criteria)) {
					$this->collThumbnails = ThumbnailPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastThumbnailCriteria = $criteria;
		return $this->collThumbnails;
	}

	/**
	 * Returns the number of related Thumbnails.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countThumbnails($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseThumbnailPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ThumbnailPeer::ENTITY_TYPE_ID, $this->getId());

		return ThumbnailPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Thumbnail object to this object
	 * through the Thumbnail foreign key attribute
	 *
	 * @param      Thumbnail $l Thumbnail
	 * @return     void
	 * @throws     PropelException
	 */
	public function addThumbnail(Thumbnail $l)
	{
		$this->collThumbnails[] = $l;
		$l->setEntityType($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType is new, it will return
	 * an empty collection; or if this EntityType has previously
	 * been saved, it will retrieve related Thumbnails from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EntityType.
	 */
	public function getThumbnailsJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseThumbnailPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collThumbnails === null) {
			if ($this->isNew()) {
				$this->collThumbnails = array();
			} else {

				$criteria->add(ThumbnailPeer::ENTITY_TYPE_ID, $this->getId());

				$this->collThumbnails = ThumbnailPeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ThumbnailPeer::ENTITY_TYPE_ID, $this->getId());

			if (!isset($this->lastThumbnailCriteria) || !$this->lastThumbnailCriteria->equals($criteria)) {
				$this->collThumbnails = ThumbnailPeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastThumbnailCriteria = $criteria;

		return $this->collThumbnails;
	}

	/**
	 * Temporary storage of collAnnotationsRelatedBySubjectTypeId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initAnnotationsRelatedBySubjectTypeId()
	{
		if ($this->collAnnotationsRelatedBySubjectTypeId === null) {
			$this->collAnnotationsRelatedBySubjectTypeId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType has previously
	 * been saved, it will retrieve related AnnotationsRelatedBySubjectTypeId from storage.
	 * If this EntityType is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getAnnotationsRelatedBySubjectTypeId($criteria = null, $con = null)
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

		if ($this->collAnnotationsRelatedBySubjectTypeId === null) {
			if ($this->isNew()) {
			   $this->collAnnotationsRelatedBySubjectTypeId = array();
			} else {

				$criteria->add(AnnotationPeer::SUBJECT_TYPE_ID, $this->getId());

				AnnotationPeer::addSelectColumns($criteria);
				$this->collAnnotationsRelatedBySubjectTypeId = AnnotationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AnnotationPeer::SUBJECT_TYPE_ID, $this->getId());

				AnnotationPeer::addSelectColumns($criteria);
				if (!isset($this->lastAnnotationRelatedBySubjectTypeIdCriteria) || !$this->lastAnnotationRelatedBySubjectTypeIdCriteria->equals($criteria)) {
					$this->collAnnotationsRelatedBySubjectTypeId = AnnotationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAnnotationRelatedBySubjectTypeIdCriteria = $criteria;
		return $this->collAnnotationsRelatedBySubjectTypeId;
	}

	/**
	 * Returns the number of related AnnotationsRelatedBySubjectTypeId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countAnnotationsRelatedBySubjectTypeId($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(AnnotationPeer::SUBJECT_TYPE_ID, $this->getId());

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
	public function addAnnotationRelatedBySubjectTypeId(Annotation $l)
	{
		$this->collAnnotationsRelatedBySubjectTypeId[] = $l;
		$l->setEntityTypeRelatedBySubjectTypeId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType is new, it will return
	 * an empty collection; or if this EntityType has previously
	 * been saved, it will retrieve related AnnotationsRelatedBySubjectTypeId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EntityType.
	 */
	public function getAnnotationsRelatedBySubjectTypeIdJoinPerson($criteria = null, $con = null)
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

		if ($this->collAnnotationsRelatedBySubjectTypeId === null) {
			if ($this->isNew()) {
				$this->collAnnotationsRelatedBySubjectTypeId = array();
			} else {

				$criteria->add(AnnotationPeer::SUBJECT_TYPE_ID, $this->getId());

				$this->collAnnotationsRelatedBySubjectTypeId = AnnotationPeer::doSelectJoinPerson($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AnnotationPeer::SUBJECT_TYPE_ID, $this->getId());

			if (!isset($this->lastAnnotationRelatedBySubjectTypeIdCriteria) || !$this->lastAnnotationRelatedBySubjectTypeIdCriteria->equals($criteria)) {
				$this->collAnnotationsRelatedBySubjectTypeId = AnnotationPeer::doSelectJoinPerson($criteria, $con);
			}
		}
		$this->lastAnnotationRelatedBySubjectTypeIdCriteria = $criteria;

		return $this->collAnnotationsRelatedBySubjectTypeId;
	}

	/**
	 * Temporary storage of collAnnotationsRelatedByObjectTypeId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initAnnotationsRelatedByObjectTypeId()
	{
		if ($this->collAnnotationsRelatedByObjectTypeId === null) {
			$this->collAnnotationsRelatedByObjectTypeId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType has previously
	 * been saved, it will retrieve related AnnotationsRelatedByObjectTypeId from storage.
	 * If this EntityType is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getAnnotationsRelatedByObjectTypeId($criteria = null, $con = null)
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

		if ($this->collAnnotationsRelatedByObjectTypeId === null) {
			if ($this->isNew()) {
			   $this->collAnnotationsRelatedByObjectTypeId = array();
			} else {

				$criteria->add(AnnotationPeer::OBJECT_TYPE_ID, $this->getId());

				AnnotationPeer::addSelectColumns($criteria);
				$this->collAnnotationsRelatedByObjectTypeId = AnnotationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AnnotationPeer::OBJECT_TYPE_ID, $this->getId());

				AnnotationPeer::addSelectColumns($criteria);
				if (!isset($this->lastAnnotationRelatedByObjectTypeIdCriteria) || !$this->lastAnnotationRelatedByObjectTypeIdCriteria->equals($criteria)) {
					$this->collAnnotationsRelatedByObjectTypeId = AnnotationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAnnotationRelatedByObjectTypeIdCriteria = $criteria;
		return $this->collAnnotationsRelatedByObjectTypeId;
	}

	/**
	 * Returns the number of related AnnotationsRelatedByObjectTypeId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countAnnotationsRelatedByObjectTypeId($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(AnnotationPeer::OBJECT_TYPE_ID, $this->getId());

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
	public function addAnnotationRelatedByObjectTypeId(Annotation $l)
	{
		$this->collAnnotationsRelatedByObjectTypeId[] = $l;
		$l->setEntityTypeRelatedByObjectTypeId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType is new, it will return
	 * an empty collection; or if this EntityType has previously
	 * been saved, it will retrieve related AnnotationsRelatedByObjectTypeId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EntityType.
	 */
	public function getAnnotationsRelatedByObjectTypeIdJoinPerson($criteria = null, $con = null)
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

		if ($this->collAnnotationsRelatedByObjectTypeId === null) {
			if ($this->isNew()) {
				$this->collAnnotationsRelatedByObjectTypeId = array();
			} else {

				$criteria->add(AnnotationPeer::OBJECT_TYPE_ID, $this->getId());

				$this->collAnnotationsRelatedByObjectTypeId = AnnotationPeer::doSelectJoinPerson($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AnnotationPeer::OBJECT_TYPE_ID, $this->getId());

			if (!isset($this->lastAnnotationRelatedByObjectTypeIdCriteria) || !$this->lastAnnotationRelatedByObjectTypeIdCriteria->equals($criteria)) {
				$this->collAnnotationsRelatedByObjectTypeId = AnnotationPeer::doSelectJoinPerson($criteria, $con);
			}
		}
		$this->lastAnnotationRelatedByObjectTypeIdCriteria = $criteria;

		return $this->collAnnotationsRelatedByObjectTypeId;
	}

	/**
	 * Temporary storage of collResearcherKeywords to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initResearcherKeywords()
	{
		if ($this->collResearcherKeywords === null) {
			$this->collResearcherKeywords = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType has previously
	 * been saved, it will retrieve related ResearcherKeywords from storage.
	 * If this EntityType is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getResearcherKeywords($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseResearcherKeywordPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collResearcherKeywords === null) {
			if ($this->isNew()) {
			   $this->collResearcherKeywords = array();
			} else {

				$criteria->add(ResearcherKeywordPeer::ENTITY_TYPE_ID, $this->getId());

				ResearcherKeywordPeer::addSelectColumns($criteria);
				$this->collResearcherKeywords = ResearcherKeywordPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ResearcherKeywordPeer::ENTITY_TYPE_ID, $this->getId());

				ResearcherKeywordPeer::addSelectColumns($criteria);
				if (!isset($this->lastResearcherKeywordCriteria) || !$this->lastResearcherKeywordCriteria->equals($criteria)) {
					$this->collResearcherKeywords = ResearcherKeywordPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastResearcherKeywordCriteria = $criteria;
		return $this->collResearcherKeywords;
	}

	/**
	 * Returns the number of related ResearcherKeywords.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countResearcherKeywords($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseResearcherKeywordPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ResearcherKeywordPeer::ENTITY_TYPE_ID, $this->getId());

		return ResearcherKeywordPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ResearcherKeyword object to this object
	 * through the ResearcherKeyword foreign key attribute
	 *
	 * @param      ResearcherKeyword $l ResearcherKeyword
	 * @return     void
	 * @throws     PropelException
	 */
	public function addResearcherKeyword(ResearcherKeyword $l)
	{
		$this->collResearcherKeywords[] = $l;
		$l->setEntityType($this);
	}

	/**
	 * Temporary storage of collEntityActivityLogs to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEntityActivityLogs()
	{
		if ($this->collEntityActivityLogs === null) {
			$this->collEntityActivityLogs = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EntityType has previously
	 * been saved, it will retrieve related EntityActivityLogs from storage.
	 * If this EntityType is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEntityActivityLogs($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEntityActivityLogPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEntityActivityLogs === null) {
			if ($this->isNew()) {
			   $this->collEntityActivityLogs = array();
			} else {

				$criteria->add(EntityActivityLogPeer::ENTITY_TYPE_ID, $this->getId());

				EntityActivityLogPeer::addSelectColumns($criteria);
				$this->collEntityActivityLogs = EntityActivityLogPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EntityActivityLogPeer::ENTITY_TYPE_ID, $this->getId());

				EntityActivityLogPeer::addSelectColumns($criteria);
				if (!isset($this->lastEntityActivityLogCriteria) || !$this->lastEntityActivityLogCriteria->equals($criteria)) {
					$this->collEntityActivityLogs = EntityActivityLogPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEntityActivityLogCriteria = $criteria;
		return $this->collEntityActivityLogs;
	}

	/**
	 * Returns the number of related EntityActivityLogs.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEntityActivityLogs($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEntityActivityLogPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EntityActivityLogPeer::ENTITY_TYPE_ID, $this->getId());

		return EntityActivityLogPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EntityActivityLog object to this object
	 * through the EntityActivityLog foreign key attribute
	 *
	 * @param      EntityActivityLog $l EntityActivityLog
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEntityActivityLog(EntityActivityLog $l)
	{
		$this->collEntityActivityLogs[] = $l;
		$l->setEntityType($this);
	}

} // BaseEntityType
