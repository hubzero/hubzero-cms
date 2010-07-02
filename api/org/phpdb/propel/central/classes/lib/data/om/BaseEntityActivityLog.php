<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/EntityActivityLogPeer.php';

/**
 * Base class that represents a row from the 'ENTITY_ACTIVITY_LOG' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEntityActivityLog extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EntityActivityLogPeer
	 */
	protected static $peer;


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
	 * The value for the view_count field.
	 * @var        double
	 */
	protected $view_count;


	/**
	 * The value for the download_count field.
	 * @var        double
	 */
	protected $download_count;

	/**
	 * @var        EntityType
	 */
	protected $aEntityType;

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
	 * Get the [view_count] column value.
	 * 
	 * @return     double
	 */
	public function getViewCount()
	{

		return $this->view_count;
	}

	/**
	 * Get the [download_count] column value.
	 * 
	 * @return     double
	 */
	public function getDownloadCount()
	{

		return $this->download_count;
	}

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
			$this->modifiedColumns[] = EntityActivityLogPeer::ENTITY_TYPE_ID;
		}

		if ($this->aEntityType !== null && $this->aEntityType->getId() !== $v) {
			$this->aEntityType = null;
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
			$this->modifiedColumns[] = EntityActivityLogPeer::ENTITY_ID;
		}

	} // setEntityId()

	/**
	 * Set the value of [view_count] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setViewCount($v)
	{

		if ($this->view_count !== $v) {
			$this->view_count = $v;
			$this->modifiedColumns[] = EntityActivityLogPeer::VIEW_COUNT;
		}

	} // setViewCount()

	/**
	 * Set the value of [download_count] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDownloadCount($v)
	{

		if ($this->download_count !== $v) {
			$this->download_count = $v;
			$this->modifiedColumns[] = EntityActivityLogPeer::DOWNLOAD_COUNT;
		}

	} // setDownloadCount()

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

			$this->entity_type_id = $rs->getFloat($startcol + 0);

			$this->entity_id = $rs->getFloat($startcol + 1);

			$this->view_count = $rs->getFloat($startcol + 2);

			$this->download_count = $rs->getFloat($startcol + 3);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 4; // 4 = EntityActivityLogPeer::NUM_COLUMNS - EntityActivityLogPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating EntityActivityLog object", $e);
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
			$con = Propel::getConnection(EntityActivityLogPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			EntityActivityLogPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(EntityActivityLogPeer::DATABASE_NAME);
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
					$pk = EntityActivityLogPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += EntityActivityLogPeer::doUpdate($this, $con);
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

			if ($this->aEntityType !== null) {
				if (!$this->aEntityType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEntityType->getValidationFailures());
				}
			}


			if (($retval = EntityActivityLogPeer::doValidate($this, $columns)) !== true) {
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
		$pos = EntityActivityLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getEntityTypeId();
				break;
			case 1:
				return $this->getEntityId();
				break;
			case 2:
				return $this->getViewCount();
				break;
			case 3:
				return $this->getDownloadCount();
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
		$keys = EntityActivityLogPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getEntityTypeId(),
			$keys[1] => $this->getEntityId(),
			$keys[2] => $this->getViewCount(),
			$keys[3] => $this->getDownloadCount(),
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
		$pos = EntityActivityLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setEntityTypeId($value);
				break;
			case 1:
				$this->setEntityId($value);
				break;
			case 2:
				$this->setViewCount($value);
				break;
			case 3:
				$this->setDownloadCount($value);
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
		$keys = EntityActivityLogPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setEntityTypeId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setEntityId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setViewCount($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDownloadCount($arr[$keys[3]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EntityActivityLogPeer::DATABASE_NAME);

		if ($this->isColumnModified(EntityActivityLogPeer::ENTITY_TYPE_ID)) $criteria->add(EntityActivityLogPeer::ENTITY_TYPE_ID, $this->entity_type_id);
		if ($this->isColumnModified(EntityActivityLogPeer::ENTITY_ID)) $criteria->add(EntityActivityLogPeer::ENTITY_ID, $this->entity_id);
		if ($this->isColumnModified(EntityActivityLogPeer::VIEW_COUNT)) $criteria->add(EntityActivityLogPeer::VIEW_COUNT, $this->view_count);
		if ($this->isColumnModified(EntityActivityLogPeer::DOWNLOAD_COUNT)) $criteria->add(EntityActivityLogPeer::DOWNLOAD_COUNT, $this->download_count);

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
		$criteria = new Criteria(EntityActivityLogPeer::DATABASE_NAME);


		return $criteria;
	}

	/**
	 * Returns NULL since this table doesn't have a primary key.
	 * This method exists only for BC and is deprecated!
	 * @return     null
	 */
	public function getPrimaryKey()
	{
		return null;
	}

	/**
	 * Dummy primary key setter.
	 *
	 * This function only exists to preserve backwards compatibility.  It is no longer
	 * needed or required by the Persistent interface.  It will be removed in next BC-breaking
	 * release of Propel.
	 *
	 * @deprecated
	 */
	 public function setPrimaryKey($pk)
	 {
		 // do nothing, because this object doesn't have any primary keys
	 }

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of EntityActivityLog (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setEntityTypeId($this->entity_type_id);

		$copyObj->setEntityId($this->entity_id);

		$copyObj->setViewCount($this->view_count);

		$copyObj->setDownloadCount($this->download_count);


		$copyObj->setNew(true);

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
	 * @return     EntityActivityLog Clone of current object.
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
	 * @return     EntityActivityLogPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EntityActivityLogPeer();
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

} // BaseEntityActivityLog
