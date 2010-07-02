<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SpecimenComponentAttributePeer.php';

/**
 * Base class that represents a row from the 'SPECIMEN_COMPONENT_ATTRIBUTE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSpecimenComponentAttribute extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SpecimenComponentAttributePeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the spec_comp_id field.
	 * @var        double
	 */
	protected $spec_comp_id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the value field.
	 * @var        string
	 */
	protected $value;


	/**
	 * The value for the unit_id field.
	 * @var        double
	 */
	protected $unit_id;


	/**
	 * The value for the display_order field.
	 * @var        double
	 */
	protected $display_order;

	/**
	 * @var        SpecimenComponent
	 */
	protected $aSpecimenComponent;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnit;

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
	 * Get the [spec_comp_id] column value.
	 * 
	 * @return     double
	 */
	public function getSpecimenComponentId()
	{

		return $this->spec_comp_id;
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
	 * Get the [value] column value.
	 * 
	 * @return     string
	 */
	public function getValue()
	{

		return $this->value;
	}

	/**
	 * Get the [unit_id] column value.
	 * 
	 * @return     double
	 */
	public function getUnitId()
	{

		return $this->unit_id;
	}

	/**
	 * Get the [display_order] column value.
	 * 
	 * @return     double
	 */
	public function getDisplayOrder()
	{

		return $this->display_order;
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
			$this->modifiedColumns[] = SpecimenComponentAttributePeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [spec_comp_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSpecimenComponentId($v)
	{

		if ($this->spec_comp_id !== $v) {
			$this->spec_comp_id = $v;
			$this->modifiedColumns[] = SpecimenComponentAttributePeer::SPEC_COMP_ID;
		}

		if ($this->aSpecimenComponent !== null && $this->aSpecimenComponent->getId() !== $v) {
			$this->aSpecimenComponent = null;
		}

	} // setSpecimenComponentId()

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
			$this->modifiedColumns[] = SpecimenComponentAttributePeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [value] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setValue($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->value !== $v) {
			$this->value = $v;
			$this->modifiedColumns[] = SpecimenComponentAttributePeer::VALUE;
		}

	} // setValue()

	/**
	 * Set the value of [unit_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setUnitId($v)
	{

		if ($this->unit_id !== $v) {
			$this->unit_id = $v;
			$this->modifiedColumns[] = SpecimenComponentAttributePeer::UNIT_ID;
		}

		if ($this->aMeasurementUnit !== null && $this->aMeasurementUnit->getId() !== $v) {
			$this->aMeasurementUnit = null;
		}

	} // setUnitId()

	/**
	 * Set the value of [display_order] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDisplayOrder($v)
	{

		if ($this->display_order !== $v) {
			$this->display_order = $v;
			$this->modifiedColumns[] = SpecimenComponentAttributePeer::DISPLAY_ORDER;
		}

	} // setDisplayOrder()

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

			$this->spec_comp_id = $rs->getFloat($startcol + 1);

			$this->name = $rs->getString($startcol + 2);

			$this->value = $rs->getString($startcol + 3);

			$this->unit_id = $rs->getFloat($startcol + 4);

			$this->display_order = $rs->getFloat($startcol + 5);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = SpecimenComponentAttributePeer::NUM_COLUMNS - SpecimenComponentAttributePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SpecimenComponentAttribute object", $e);
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
			$con = Propel::getConnection(SpecimenComponentAttributePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SpecimenComponentAttributePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SpecimenComponentAttributePeer::DATABASE_NAME);
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

			if ($this->aSpecimenComponent !== null) {
				if ($this->aSpecimenComponent->isModified()) {
					$affectedRows += $this->aSpecimenComponent->save($con);
				}
				$this->setSpecimenComponent($this->aSpecimenComponent);
			}

			if ($this->aMeasurementUnit !== null) {
				if ($this->aMeasurementUnit->isModified()) {
					$affectedRows += $this->aMeasurementUnit->save($con);
				}
				$this->setMeasurementUnit($this->aMeasurementUnit);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SpecimenComponentAttributePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SpecimenComponentAttributePeer::doUpdate($this, $con);
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

			if ($this->aSpecimenComponent !== null) {
				if (!$this->aSpecimenComponent->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSpecimenComponent->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnit !== null) {
				if (!$this->aMeasurementUnit->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnit->getValidationFailures());
				}
			}


			if (($retval = SpecimenComponentAttributePeer::doValidate($this, $columns)) !== true) {
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
		$pos = SpecimenComponentAttributePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getSpecimenComponentId();
				break;
			case 2:
				return $this->getName();
				break;
			case 3:
				return $this->getValue();
				break;
			case 4:
				return $this->getUnitId();
				break;
			case 5:
				return $this->getDisplayOrder();
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
		$keys = SpecimenComponentAttributePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getSpecimenComponentId(),
			$keys[2] => $this->getName(),
			$keys[3] => $this->getValue(),
			$keys[4] => $this->getUnitId(),
			$keys[5] => $this->getDisplayOrder(),
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
		$pos = SpecimenComponentAttributePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setSpecimenComponentId($value);
				break;
			case 2:
				$this->setName($value);
				break;
			case 3:
				$this->setValue($value);
				break;
			case 4:
				$this->setUnitId($value);
				break;
			case 5:
				$this->setDisplayOrder($value);
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
		$keys = SpecimenComponentAttributePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setSpecimenComponentId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setValue($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setUnitId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDisplayOrder($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SpecimenComponentAttributePeer::DATABASE_NAME);

		if ($this->isColumnModified(SpecimenComponentAttributePeer::ID)) $criteria->add(SpecimenComponentAttributePeer::ID, $this->id);
		if ($this->isColumnModified(SpecimenComponentAttributePeer::SPEC_COMP_ID)) $criteria->add(SpecimenComponentAttributePeer::SPEC_COMP_ID, $this->spec_comp_id);
		if ($this->isColumnModified(SpecimenComponentAttributePeer::NAME)) $criteria->add(SpecimenComponentAttributePeer::NAME, $this->name);
		if ($this->isColumnModified(SpecimenComponentAttributePeer::VALUE)) $criteria->add(SpecimenComponentAttributePeer::VALUE, $this->value);
		if ($this->isColumnModified(SpecimenComponentAttributePeer::UNIT_ID)) $criteria->add(SpecimenComponentAttributePeer::UNIT_ID, $this->unit_id);
		if ($this->isColumnModified(SpecimenComponentAttributePeer::DISPLAY_ORDER)) $criteria->add(SpecimenComponentAttributePeer::DISPLAY_ORDER, $this->display_order);

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
		$criteria = new Criteria(SpecimenComponentAttributePeer::DATABASE_NAME);

		$criteria->add(SpecimenComponentAttributePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SpecimenComponentAttribute (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setSpecimenComponentId($this->spec_comp_id);

		$copyObj->setName($this->name);

		$copyObj->setValue($this->value);

		$copyObj->setUnitId($this->unit_id);

		$copyObj->setDisplayOrder($this->display_order);


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
	 * @return     SpecimenComponentAttribute Clone of current object.
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
	 * @return     SpecimenComponentAttributePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SpecimenComponentAttributePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a SpecimenComponent object.
	 *
	 * @param      SpecimenComponent $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSpecimenComponent($v)
	{


		if ($v === null) {
			$this->setSpecimenComponentId(NULL);
		} else {
			$this->setSpecimenComponentId($v->getId());
		}


		$this->aSpecimenComponent = $v;
	}


	/**
	 * Get the associated SpecimenComponent object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SpecimenComponent The associated SpecimenComponent object.
	 * @throws     PropelException
	 */
	public function getSpecimenComponent($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSpecimenComponentPeer.php';

		if ($this->aSpecimenComponent === null && ($this->spec_comp_id > 0)) {

			$this->aSpecimenComponent = SpecimenComponentPeer::retrieveByPK($this->spec_comp_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SpecimenComponentPeer::retrieveByPK($this->spec_comp_id, $con);
			   $obj->addSpecimenComponents($this);
			 */
		}
		return $this->aSpecimenComponent;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnit($v)
	{


		if ($v === null) {
			$this->setUnitId(NULL);
		} else {
			$this->setUnitId($v->getId());
		}


		$this->aMeasurementUnit = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnit($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnit === null && ($this->unit_id > 0)) {

			$this->aMeasurementUnit = MeasurementUnitPeer::retrieveByPK($this->unit_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->unit_id, $con);
			   $obj->addMeasurementUnits($this);
			 */
		}
		return $this->aMeasurementUnit;
	}

} // BaseSpecimenComponentAttribute
