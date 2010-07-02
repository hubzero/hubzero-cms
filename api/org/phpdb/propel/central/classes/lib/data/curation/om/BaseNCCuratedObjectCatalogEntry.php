<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/curation/NCCuratedObjectCatalogEntryPeer.php';

/**
 * Base class that represents a row from the 'CURATED_OBJECT_CATALOG_ENTRY' table.
 *
 * 
 *
 * @package    lib.data.curation.om
 */
abstract class BaseNCCuratedObjectCatalogEntry extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        NCCuratedObjectCatalogEntryPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the created_by field.
	 * @var        string
	 */
	protected $created_by;


	/**
	 * The value for the created_date field.
	 * @var        int
	 */
	protected $created_date;


	/**
	 * The value for the object_id field.
	 * @var        double
	 */
	protected $object_id;


	/**
	 * The value for the ontology_term field.
	 * @var        string
	 */
	protected $ontology_term;


	/**
	 * The value for the relevance_level field.
	 * @var        string
	 */
	protected $relevance_level;

	/**
	 * @var        NCCuratedObjects
	 */
	protected $aNCCuratedObjects;

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
	 * Get the [created_by] column value.
	 * 
	 * @return     string
	 */
	public function getCreatedBy()
	{

		return $this->created_by;
	}

	/**
	 * Get the [optionally formatted] [created_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getCreatedDate($format = '%Y-%m-%d')
	{

		if ($this->created_date === null || $this->created_date === '') {
			return null;
		} elseif (!is_int($this->created_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->created_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [created_date] as date/time value: " . var_export($this->created_date, true));
			}
		} else {
			$ts = $this->created_date;
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
	}

	/**
	 * Get the [object_id] column value.
	 * 
	 * @return     double
	 */
	public function getObjectId()
	{

		return $this->object_id;
	}

	/**
	 * Get the [ontology_term] column value.
	 * 
	 * @return     string
	 */
	public function getOntologyTerm()
	{

		return $this->ontology_term;
	}

	/**
	 * Get the [relevance_level] column value.
	 * 
	 * @return     string
	 */
	public function getRelevanceLevel()
	{

		return $this->relevance_level;
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
			$this->modifiedColumns[] = NCCuratedObjectCatalogEntryPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [created_by] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCreatedBy($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->created_by !== $v) {
			$this->created_by = $v;
			$this->modifiedColumns[] = NCCuratedObjectCatalogEntryPeer::CREATED_BY;
		}

	} // setCreatedBy()

	/**
	 * Set the value of [created_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setCreatedDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [created_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->created_date !== $ts) {
			$this->created_date = $ts;
			$this->modifiedColumns[] = NCCuratedObjectCatalogEntryPeer::CREATED_DATE;
		}

	} // setCreatedDate()

	/**
	 * Set the value of [object_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setObjectId($v)
	{

		if ($this->object_id !== $v) {
			$this->object_id = $v;
			$this->modifiedColumns[] = NCCuratedObjectCatalogEntryPeer::OBJECT_ID;
		}

		if ($this->aNCCuratedObjects !== null && $this->aNCCuratedObjects->getObjectId() !== $v) {
			$this->aNCCuratedObjects = null;
		}

	} // setObjectId()

	/**
	 * Set the value of [ontology_term] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setOntologyTerm($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->ontology_term !== $v) {
			$this->ontology_term = $v;
			$this->modifiedColumns[] = NCCuratedObjectCatalogEntryPeer::ONTOLOGY_TERM;
		}

	} // setOntologyTerm()

	/**
	 * Set the value of [relevance_level] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setRelevanceLevel($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->relevance_level !== $v) {
			$this->relevance_level = $v;
			$this->modifiedColumns[] = NCCuratedObjectCatalogEntryPeer::RELEVANCE_LEVEL;
		}

	} // setRelevanceLevel()

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

			$this->created_by = $rs->getString($startcol + 1);

			$this->created_date = $rs->getDate($startcol + 2, null);

			$this->object_id = $rs->getFloat($startcol + 3);

			$this->ontology_term = $rs->getString($startcol + 4);

			$this->relevance_level = $rs->getString($startcol + 5);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = NCCuratedObjectCatalogEntryPeer::NUM_COLUMNS - NCCuratedObjectCatalogEntryPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating NCCuratedObjectCatalogEntry object", $e);
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
			$con = Propel::getConnection(NCCuratedObjectCatalogEntryPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			NCCuratedObjectCatalogEntryPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(NCCuratedObjectCatalogEntryPeer::DATABASE_NAME);
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

			if ($this->aNCCuratedObjects !== null) {
				if ($this->aNCCuratedObjects->isModified()) {
					$affectedRows += $this->aNCCuratedObjects->save($con);
				}
				$this->setNCCuratedObjects($this->aNCCuratedObjects);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = NCCuratedObjectCatalogEntryPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += NCCuratedObjectCatalogEntryPeer::doUpdate($this, $con);
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

			if ($this->aNCCuratedObjects !== null) {
				if (!$this->aNCCuratedObjects->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aNCCuratedObjects->getValidationFailures());
				}
			}


			if (($retval = NCCuratedObjectCatalogEntryPeer::doValidate($this, $columns)) !== true) {
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
		$pos = NCCuratedObjectCatalogEntryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCreatedBy();
				break;
			case 2:
				return $this->getCreatedDate();
				break;
			case 3:
				return $this->getObjectId();
				break;
			case 4:
				return $this->getOntologyTerm();
				break;
			case 5:
				return $this->getRelevanceLevel();
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
		$keys = NCCuratedObjectCatalogEntryPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCreatedBy(),
			$keys[2] => $this->getCreatedDate(),
			$keys[3] => $this->getObjectId(),
			$keys[4] => $this->getOntologyTerm(),
			$keys[5] => $this->getRelevanceLevel(),
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
		$pos = NCCuratedObjectCatalogEntryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCreatedBy($value);
				break;
			case 2:
				$this->setCreatedDate($value);
				break;
			case 3:
				$this->setObjectId($value);
				break;
			case 4:
				$this->setOntologyTerm($value);
				break;
			case 5:
				$this->setRelevanceLevel($value);
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
		$keys = NCCuratedObjectCatalogEntryPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCreatedBy($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCreatedDate($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setObjectId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setOntologyTerm($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setRelevanceLevel($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(NCCuratedObjectCatalogEntryPeer::DATABASE_NAME);

		if ($this->isColumnModified(NCCuratedObjectCatalogEntryPeer::ID)) $criteria->add(NCCuratedObjectCatalogEntryPeer::ID, $this->id);
		if ($this->isColumnModified(NCCuratedObjectCatalogEntryPeer::CREATED_BY)) $criteria->add(NCCuratedObjectCatalogEntryPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(NCCuratedObjectCatalogEntryPeer::CREATED_DATE)) $criteria->add(NCCuratedObjectCatalogEntryPeer::CREATED_DATE, $this->created_date);
		if ($this->isColumnModified(NCCuratedObjectCatalogEntryPeer::OBJECT_ID)) $criteria->add(NCCuratedObjectCatalogEntryPeer::OBJECT_ID, $this->object_id);
		if ($this->isColumnModified(NCCuratedObjectCatalogEntryPeer::ONTOLOGY_TERM)) $criteria->add(NCCuratedObjectCatalogEntryPeer::ONTOLOGY_TERM, $this->ontology_term);
		if ($this->isColumnModified(NCCuratedObjectCatalogEntryPeer::RELEVANCE_LEVEL)) $criteria->add(NCCuratedObjectCatalogEntryPeer::RELEVANCE_LEVEL, $this->relevance_level);

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
		$criteria = new Criteria(NCCuratedObjectCatalogEntryPeer::DATABASE_NAME);

		$criteria->add(NCCuratedObjectCatalogEntryPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of NCCuratedObjectCatalogEntry (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCreatedBy($this->created_by);

		$copyObj->setCreatedDate($this->created_date);

		$copyObj->setObjectId($this->object_id);

		$copyObj->setOntologyTerm($this->ontology_term);

		$copyObj->setRelevanceLevel($this->relevance_level);


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
	 * @return     NCCuratedObjectCatalogEntry Clone of current object.
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
	 * @return     NCCuratedObjectCatalogEntryPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new NCCuratedObjectCatalogEntryPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a NCCuratedObjects object.
	 *
	 * @param      NCCuratedObjects $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setNCCuratedObjects($v)
	{


		if ($v === null) {
			$this->setObjectId(NULL);
		} else {
			$this->setObjectId($v->getObjectId());
		}


		$this->aNCCuratedObjects = $v;
	}


	/**
	 * Get the associated NCCuratedObjects object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     NCCuratedObjects The associated NCCuratedObjects object.
	 * @throws     PropelException
	 */
	public function getNCCuratedObjects($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/curation/om/BaseNCCuratedObjectsPeer.php';

		if ($this->aNCCuratedObjects === null && ($this->object_id > 0)) {

			$this->aNCCuratedObjects = NCCuratedObjectsPeer::retrieveByPK($this->object_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = NCCuratedObjectsPeer::retrieveByPK($this->object_id, $con);
			   $obj->addNCCuratedObjectss($this);
			 */
		}
		return $this->aNCCuratedObjects;
	}

} // BaseNCCuratedObjectCatalogEntry
