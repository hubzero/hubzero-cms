<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/MeasurementUnitConversionPeer.php';

/**
 * Base class that represents a row from the 'MEASUREMENT_UNIT_CONVERSION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseMeasurementUnitConversion extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        MeasurementUnitConversionPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the from_id field.
	 * @var        double
	 */
	protected $from_id;


	/**
	 * The value for the k0 field.
	 * @var        double
	 */
	protected $k0;


	/**
	 * The value for the k1 field.
	 * @var        double
	 */
	protected $k1;


	/**
	 * The value for the to_id field.
	 * @var        double
	 */
	protected $to_id;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByToId;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByFromId;

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
	 * Get the [from_id] column value.
	 * 
	 * @return     double
	 */
	public function getFromId()
	{

		return $this->from_id;
	}

	/**
	 * Get the [k0] column value.
	 * 
	 * @return     double
	 */
	public function getK0()
	{

		return $this->k0;
	}

	/**
	 * Get the [k1] column value.
	 * 
	 * @return     double
	 */
	public function getK1()
	{

		return $this->k1;
	}

	/**
	 * Get the [to_id] column value.
	 * 
	 * @return     double
	 */
	public function getToId()
	{

		return $this->to_id;
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
			$this->modifiedColumns[] = MeasurementUnitConversionPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [from_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFromId($v)
	{

		if ($this->from_id !== $v) {
			$this->from_id = $v;
			$this->modifiedColumns[] = MeasurementUnitConversionPeer::FROM_ID;
		}

		if ($this->aMeasurementUnitRelatedByFromId !== null && $this->aMeasurementUnitRelatedByFromId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByFromId = null;
		}

	} // setFromId()

	/**
	 * Set the value of [k0] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setK0($v)
	{

		if ($this->k0 !== $v) {
			$this->k0 = $v;
			$this->modifiedColumns[] = MeasurementUnitConversionPeer::K0;
		}

	} // setK0()

	/**
	 * Set the value of [k1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setK1($v)
	{

		if ($this->k1 !== $v) {
			$this->k1 = $v;
			$this->modifiedColumns[] = MeasurementUnitConversionPeer::K1;
		}

	} // setK1()

	/**
	 * Set the value of [to_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setToId($v)
	{

		if ($this->to_id !== $v) {
			$this->to_id = $v;
			$this->modifiedColumns[] = MeasurementUnitConversionPeer::TO_ID;
		}

		if ($this->aMeasurementUnitRelatedByToId !== null && $this->aMeasurementUnitRelatedByToId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByToId = null;
		}

	} // setToId()

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

			$this->from_id = $rs->getFloat($startcol + 1);

			$this->k0 = $rs->getFloat($startcol + 2);

			$this->k1 = $rs->getFloat($startcol + 3);

			$this->to_id = $rs->getFloat($startcol + 4);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 5; // 5 = MeasurementUnitConversionPeer::NUM_COLUMNS - MeasurementUnitConversionPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating MeasurementUnitConversion object", $e);
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
			$con = Propel::getConnection(MeasurementUnitConversionPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			MeasurementUnitConversionPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(MeasurementUnitConversionPeer::DATABASE_NAME);
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

			if ($this->aMeasurementUnitRelatedByToId !== null) {
				if ($this->aMeasurementUnitRelatedByToId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByToId->save($con);
				}
				$this->setMeasurementUnitRelatedByToId($this->aMeasurementUnitRelatedByToId);
			}

			if ($this->aMeasurementUnitRelatedByFromId !== null) {
				if ($this->aMeasurementUnitRelatedByFromId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByFromId->save($con);
				}
				$this->setMeasurementUnitRelatedByFromId($this->aMeasurementUnitRelatedByFromId);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = MeasurementUnitConversionPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += MeasurementUnitConversionPeer::doUpdate($this, $con);
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

			if ($this->aMeasurementUnitRelatedByToId !== null) {
				if (!$this->aMeasurementUnitRelatedByToId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByToId->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByFromId !== null) {
				if (!$this->aMeasurementUnitRelatedByFromId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByFromId->getValidationFailures());
				}
			}


			if (($retval = MeasurementUnitConversionPeer::doValidate($this, $columns)) !== true) {
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
		$pos = MeasurementUnitConversionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getFromId();
				break;
			case 2:
				return $this->getK0();
				break;
			case 3:
				return $this->getK1();
				break;
			case 4:
				return $this->getToId();
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
		$keys = MeasurementUnitConversionPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getFromId(),
			$keys[2] => $this->getK0(),
			$keys[3] => $this->getK1(),
			$keys[4] => $this->getToId(),
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
		$pos = MeasurementUnitConversionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setFromId($value);
				break;
			case 2:
				$this->setK0($value);
				break;
			case 3:
				$this->setK1($value);
				break;
			case 4:
				$this->setToId($value);
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
		$keys = MeasurementUnitConversionPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setFromId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setK0($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setK1($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setToId($arr[$keys[4]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(MeasurementUnitConversionPeer::DATABASE_NAME);

		if ($this->isColumnModified(MeasurementUnitConversionPeer::ID)) $criteria->add(MeasurementUnitConversionPeer::ID, $this->id);
		if ($this->isColumnModified(MeasurementUnitConversionPeer::FROM_ID)) $criteria->add(MeasurementUnitConversionPeer::FROM_ID, $this->from_id);
		if ($this->isColumnModified(MeasurementUnitConversionPeer::K0)) $criteria->add(MeasurementUnitConversionPeer::K0, $this->k0);
		if ($this->isColumnModified(MeasurementUnitConversionPeer::K1)) $criteria->add(MeasurementUnitConversionPeer::K1, $this->k1);
		if ($this->isColumnModified(MeasurementUnitConversionPeer::TO_ID)) $criteria->add(MeasurementUnitConversionPeer::TO_ID, $this->to_id);

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
		$criteria = new Criteria(MeasurementUnitConversionPeer::DATABASE_NAME);

		$criteria->add(MeasurementUnitConversionPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of MeasurementUnitConversion (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setFromId($this->from_id);

		$copyObj->setK0($this->k0);

		$copyObj->setK1($this->k1);

		$copyObj->setToId($this->to_id);


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
	 * @return     MeasurementUnitConversion Clone of current object.
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
	 * @return     MeasurementUnitConversionPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new MeasurementUnitConversionPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByToId($v)
	{


		if ($v === null) {
			$this->setToId(NULL);
		} else {
			$this->setToId($v->getId());
		}


		$this->aMeasurementUnitRelatedByToId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByToId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByToId === null && ($this->to_id > 0)) {

			$this->aMeasurementUnitRelatedByToId = MeasurementUnitPeer::retrieveByPK($this->to_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->to_id, $con);
			   $obj->addMeasurementUnitsRelatedByToId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByToId;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByFromId($v)
	{


		if ($v === null) {
			$this->setFromId(NULL);
		} else {
			$this->setFromId($v->getId());
		}


		$this->aMeasurementUnitRelatedByFromId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByFromId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByFromId === null && ($this->from_id > 0)) {

			$this->aMeasurementUnitRelatedByFromId = MeasurementUnitPeer::retrieveByPK($this->from_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->from_id, $con);
			   $obj->addMeasurementUnitsRelatedByFromId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByFromId;
	}

} // BaseMeasurementUnitConversion
