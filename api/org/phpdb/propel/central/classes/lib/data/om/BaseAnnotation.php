<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/AnnotationPeer.php';

/**
 * Base class that represents a row from the 'ANNOTATION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseAnnotation extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AnnotationPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the creator_id field.
	 * @var        double
	 */
	protected $creator_id;


	/**
	 * The value for the key field.
	 * @var        string
	 */
	protected $key;


	/**
	 * The value for the message field.
	 * @var        string
	 */
	protected $message;


	/**
	 * The value for the subject_type_id field.
	 * @var        double
	 */
	protected $subject_type_id;


	/**
	 * The value for the subject_id field.
	 * @var        double
	 */
	protected $subject_id;


	/**
	 * The value for the object_type_id field.
	 * @var        double
	 */
	protected $object_type_id;


	/**
	 * The value for the object_id field.
	 * @var        double
	 */
	protected $object_id;

	/**
	 * @var        Person
	 */
	protected $aPerson;

	/**
	 * @var        EntityType
	 */
	protected $aEntityTypeRelatedBySubjectTypeId;

	/**
	 * @var        EntityType
	 */
	protected $aEntityTypeRelatedByObjectTypeId;

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
	 * Get the [creator_id] column value.
	 * 
	 * @return     double
	 */
	public function getCreator()
	{

		return $this->creator_id;
	}

	/**
	 * Get the [key] column value.
	 * 
	 * @return     string
	 */
	public function getKey()
	{

		return $this->key;
	}

	/**
	 * Get the [message] column value.
	 * 
	 * @return     string
	 */
	public function getMessage()
	{

		return $this->message;
	}

	/**
	 * Get the [subject_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getSubjectTypeId()
	{

		return $this->subject_type_id;
	}

	/**
	 * Get the [subject_id] column value.
	 * 
	 * @return     double
	 */
	public function getSubjectId()
	{

		return $this->subject_id;
	}

	/**
	 * Get the [object_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getObjectTypeId()
	{

		return $this->object_type_id;
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
	 * Set the value of [id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AnnotationPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [creator_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCreator($v)
	{

		if ($this->creator_id !== $v) {
			$this->creator_id = $v;
			$this->modifiedColumns[] = AnnotationPeer::CREATOR_ID;
		}

		if ($this->aPerson !== null && $this->aPerson->getId() !== $v) {
			$this->aPerson = null;
		}

	} // setCreator()

	/**
	 * Set the value of [key] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setKey($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->key !== $v) {
			$this->key = $v;
			$this->modifiedColumns[] = AnnotationPeer::KEY;
		}

	} // setKey()

	/**
	 * Set the value of [message] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setMessage($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->message !== $v) {
			$this->message = $v;
			$this->modifiedColumns[] = AnnotationPeer::MESSAGE;
		}

	} // setMessage()

	/**
	 * Set the value of [subject_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSubjectTypeId($v)
	{

		if ($this->subject_type_id !== $v) {
			$this->subject_type_id = $v;
			$this->modifiedColumns[] = AnnotationPeer::SUBJECT_TYPE_ID;
		}

		if ($this->aEntityTypeRelatedBySubjectTypeId !== null && $this->aEntityTypeRelatedBySubjectTypeId->getId() !== $v) {
			$this->aEntityTypeRelatedBySubjectTypeId = null;
		}

	} // setSubjectTypeId()

	/**
	 * Set the value of [subject_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSubjectId($v)
	{

		if ($this->subject_id !== $v) {
			$this->subject_id = $v;
			$this->modifiedColumns[] = AnnotationPeer::SUBJECT_ID;
		}

	} // setSubjectId()

	/**
	 * Set the value of [object_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setObjectTypeId($v)
	{

		if ($this->object_type_id !== $v) {
			$this->object_type_id = $v;
			$this->modifiedColumns[] = AnnotationPeer::OBJECT_TYPE_ID;
		}

		if ($this->aEntityTypeRelatedByObjectTypeId !== null && $this->aEntityTypeRelatedByObjectTypeId->getId() !== $v) {
			$this->aEntityTypeRelatedByObjectTypeId = null;
		}

	} // setObjectTypeId()

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
			$this->modifiedColumns[] = AnnotationPeer::OBJECT_ID;
		}

	} // setObjectId()

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

			$this->creator_id = $rs->getFloat($startcol + 1);

			$this->key = $rs->getString($startcol + 2);

			$this->message = $rs->getString($startcol + 3);

			$this->subject_type_id = $rs->getFloat($startcol + 4);

			$this->subject_id = $rs->getFloat($startcol + 5);

			$this->object_type_id = $rs->getFloat($startcol + 6);

			$this->object_id = $rs->getFloat($startcol + 7);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 8; // 8 = AnnotationPeer::NUM_COLUMNS - AnnotationPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Annotation object", $e);
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
			$con = Propel::getConnection(AnnotationPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			AnnotationPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(AnnotationPeer::DATABASE_NAME);
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

			if ($this->aEntityTypeRelatedBySubjectTypeId !== null) {
				if ($this->aEntityTypeRelatedBySubjectTypeId->isModified()) {
					$affectedRows += $this->aEntityTypeRelatedBySubjectTypeId->save($con);
				}
				$this->setEntityTypeRelatedBySubjectTypeId($this->aEntityTypeRelatedBySubjectTypeId);
			}

			if ($this->aEntityTypeRelatedByObjectTypeId !== null) {
				if ($this->aEntityTypeRelatedByObjectTypeId->isModified()) {
					$affectedRows += $this->aEntityTypeRelatedByObjectTypeId->save($con);
				}
				$this->setEntityTypeRelatedByObjectTypeId($this->aEntityTypeRelatedByObjectTypeId);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = AnnotationPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += AnnotationPeer::doUpdate($this, $con);
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

			if ($this->aEntityTypeRelatedBySubjectTypeId !== null) {
				if (!$this->aEntityTypeRelatedBySubjectTypeId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEntityTypeRelatedBySubjectTypeId->getValidationFailures());
				}
			}

			if ($this->aEntityTypeRelatedByObjectTypeId !== null) {
				if (!$this->aEntityTypeRelatedByObjectTypeId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEntityTypeRelatedByObjectTypeId->getValidationFailures());
				}
			}


			if (($retval = AnnotationPeer::doValidate($this, $columns)) !== true) {
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
		$pos = AnnotationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCreator();
				break;
			case 2:
				return $this->getKey();
				break;
			case 3:
				return $this->getMessage();
				break;
			case 4:
				return $this->getSubjectTypeId();
				break;
			case 5:
				return $this->getSubjectId();
				break;
			case 6:
				return $this->getObjectTypeId();
				break;
			case 7:
				return $this->getObjectId();
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
		$keys = AnnotationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCreator(),
			$keys[2] => $this->getKey(),
			$keys[3] => $this->getMessage(),
			$keys[4] => $this->getSubjectTypeId(),
			$keys[5] => $this->getSubjectId(),
			$keys[6] => $this->getObjectTypeId(),
			$keys[7] => $this->getObjectId(),
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
		$pos = AnnotationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCreator($value);
				break;
			case 2:
				$this->setKey($value);
				break;
			case 3:
				$this->setMessage($value);
				break;
			case 4:
				$this->setSubjectTypeId($value);
				break;
			case 5:
				$this->setSubjectId($value);
				break;
			case 6:
				$this->setObjectTypeId($value);
				break;
			case 7:
				$this->setObjectId($value);
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
		$keys = AnnotationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCreator($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setKey($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setMessage($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setSubjectTypeId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setSubjectId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setObjectTypeId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setObjectId($arr[$keys[7]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AnnotationPeer::DATABASE_NAME);

		if ($this->isColumnModified(AnnotationPeer::ID)) $criteria->add(AnnotationPeer::ID, $this->id);
		if ($this->isColumnModified(AnnotationPeer::CREATOR_ID)) $criteria->add(AnnotationPeer::CREATOR_ID, $this->creator_id);
		if ($this->isColumnModified(AnnotationPeer::KEY)) $criteria->add(AnnotationPeer::KEY, $this->key);
		if ($this->isColumnModified(AnnotationPeer::MESSAGE)) $criteria->add(AnnotationPeer::MESSAGE, $this->message);
		if ($this->isColumnModified(AnnotationPeer::SUBJECT_TYPE_ID)) $criteria->add(AnnotationPeer::SUBJECT_TYPE_ID, $this->subject_type_id);
		if ($this->isColumnModified(AnnotationPeer::SUBJECT_ID)) $criteria->add(AnnotationPeer::SUBJECT_ID, $this->subject_id);
		if ($this->isColumnModified(AnnotationPeer::OBJECT_TYPE_ID)) $criteria->add(AnnotationPeer::OBJECT_TYPE_ID, $this->object_type_id);
		if ($this->isColumnModified(AnnotationPeer::OBJECT_ID)) $criteria->add(AnnotationPeer::OBJECT_ID, $this->object_id);

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
		$criteria = new Criteria(AnnotationPeer::DATABASE_NAME);

		$criteria->add(AnnotationPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of Annotation (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCreator($this->creator_id);

		$copyObj->setKey($this->key);

		$copyObj->setMessage($this->message);

		$copyObj->setSubjectTypeId($this->subject_type_id);

		$copyObj->setSubjectId($this->subject_id);

		$copyObj->setObjectTypeId($this->object_type_id);

		$copyObj->setObjectId($this->object_id);


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
	 * @return     Annotation Clone of current object.
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
	 * @return     AnnotationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AnnotationPeer();
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
			$this->setCreator(NULL);
		} else {
			$this->setCreator($v->getId());
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

		if ($this->aPerson === null && ($this->creator_id > 0)) {

			$this->aPerson = PersonPeer::retrieveByPK($this->creator_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = PersonPeer::retrieveByPK($this->creator_id, $con);
			   $obj->addPersons($this);
			 */
		}
		return $this->aPerson;
	}

	/**
	 * Declares an association between this object and a EntityType object.
	 *
	 * @param      EntityType $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEntityTypeRelatedBySubjectTypeId($v)
	{


		if ($v === null) {
			$this->setSubjectTypeId(NULL);
		} else {
			$this->setSubjectTypeId($v->getId());
		}


		$this->aEntityTypeRelatedBySubjectTypeId = $v;
	}


	/**
	 * Get the associated EntityType object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     EntityType The associated EntityType object.
	 * @throws     PropelException
	 */
	public function getEntityTypeRelatedBySubjectTypeId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEntityTypePeer.php';

		if ($this->aEntityTypeRelatedBySubjectTypeId === null && ($this->subject_type_id > 0)) {

			$this->aEntityTypeRelatedBySubjectTypeId = EntityTypePeer::retrieveByPK($this->subject_type_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EntityTypePeer::retrieveByPK($this->subject_type_id, $con);
			   $obj->addEntityTypesRelatedBySubjectTypeId($this);
			 */
		}
		return $this->aEntityTypeRelatedBySubjectTypeId;
	}

	/**
	 * Declares an association between this object and a EntityType object.
	 *
	 * @param      EntityType $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEntityTypeRelatedByObjectTypeId($v)
	{


		if ($v === null) {
			$this->setObjectTypeId(NULL);
		} else {
			$this->setObjectTypeId($v->getId());
		}


		$this->aEntityTypeRelatedByObjectTypeId = $v;
	}


	/**
	 * Get the associated EntityType object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     EntityType The associated EntityType object.
	 * @throws     PropelException
	 */
	public function getEntityTypeRelatedByObjectTypeId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEntityTypePeer.php';

		if ($this->aEntityTypeRelatedByObjectTypeId === null && ($this->object_type_id > 0)) {

			$this->aEntityTypeRelatedByObjectTypeId = EntityTypePeer::retrieveByPK($this->object_type_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EntityTypePeer::retrieveByPK($this->object_type_id, $con);
			   $obj->addEntityTypesRelatedByObjectTypeId($this);
			 */
		}
		return $this->aEntityTypeRelatedByObjectTypeId;
	}

} // BaseAnnotation
