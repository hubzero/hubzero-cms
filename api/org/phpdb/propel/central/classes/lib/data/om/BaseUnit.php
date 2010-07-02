<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/UnitPeer.php';

/**
 * Base class that represents a row from the 'UNIT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseUnit extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        UnitPeer
	 */
	protected static $peer;


	/**
	 * The value for the unit_id field.
	 * @var        double
	 */
	protected $unit_id;


	/**
	 * The value for the base_id field.
	 * @var        double
	 */
	protected $base_id;


	/**
	 * The value for the conversion field.
	 * @var        double
	 */
	protected $conversion;


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
	 * The value for the symbol field.
	 * @var        string
	 */
	protected $symbol;


	/**
	 * The value for the unicode field.
	 * @var        string
	 */
	protected $unicode;

	/**
	 * @var        Unit
	 */
	protected $aUnitRelatedByBaseId;

	/**
	 * Collection to store aggregation of collAttributes.
	 * @var        array
	 */
	protected $collAttributes;

	/**
	 * The criteria used to select the current contents of collAttributes.
	 * @var        Criteria
	 */
	protected $lastAttributeCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentAttributes.
	 * @var        array
	 */
	protected $collEquipmentAttributes;

	/**
	 * The criteria used to select the current contents of collEquipmentAttributes.
	 * @var        Criteria
	 */
	protected $lastEquipmentAttributeCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentAttributeValues.
	 * @var        array
	 */
	protected $collEquipmentAttributeValues;

	/**
	 * The criteria used to select the current contents of collEquipmentAttributeValues.
	 * @var        Criteria
	 */
	protected $lastEquipmentAttributeValueCriteria = null;

	/**
	 * Collection to store aggregation of collSensorAttributes.
	 * @var        array
	 */
	protected $collSensorAttributes;

	/**
	 * The criteria used to select the current contents of collSensorAttributes.
	 * @var        Criteria
	 */
	protected $lastSensorAttributeCriteria = null;

	/**
	 * Collection to store aggregation of collUnitsRelatedByBaseId.
	 * @var        array
	 */
	protected $collUnitsRelatedByBaseId;

	/**
	 * The criteria used to select the current contents of collUnitsRelatedByBaseId.
	 * @var        Criteria
	 */
	protected $lastUnitRelatedByBaseIdCriteria = null;

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
	 * Get the [unit_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->unit_id;
	}

	/**
	 * Get the [base_id] column value.
	 * 
	 * @return     double
	 */
	public function getBaseId()
	{

		return $this->base_id;
	}

	/**
	 * Get the [conversion] column value.
	 * 
	 * @return     double
	 */
	public function getConversion()
	{

		return $this->conversion;
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
	 * Get the [symbol] column value.
	 * 
	 * @return     string
	 */
	public function getSymbol()
	{

		return $this->symbol;
	}

	/**
	 * Get the [unicode] column value.
	 * 
	 * @return     string
	 */
	public function getUnicode()
	{

		return $this->unicode;
	}

	/**
	 * Set the value of [unit_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->unit_id !== $v) {
			$this->unit_id = $v;
			$this->modifiedColumns[] = UnitPeer::UNIT_ID;
		}

	} // setId()

	/**
	 * Set the value of [base_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setBaseId($v)
	{

		if ($this->base_id !== $v) {
			$this->base_id = $v;
			$this->modifiedColumns[] = UnitPeer::BASE_ID;
		}

		if ($this->aUnitRelatedByBaseId !== null && $this->aUnitRelatedByBaseId->getId() !== $v) {
			$this->aUnitRelatedByBaseId = null;
		}

	} // setBaseId()

	/**
	 * Set the value of [conversion] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setConversion($v)
	{

		if ($this->conversion !== $v) {
			$this->conversion = $v;
			$this->modifiedColumns[] = UnitPeer::CONVERSION;
		}

	} // setConversion()

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
			$this->modifiedColumns[] = UnitPeer::DESCRIPTION;
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
			$this->modifiedColumns[] = UnitPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [symbol] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSymbol($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->symbol !== $v) {
			$this->symbol = $v;
			$this->modifiedColumns[] = UnitPeer::SYMBOL;
		}

	} // setSymbol()

	/**
	 * Set the value of [unicode] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setUnicode($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->unicode !== $v) {
			$this->unicode = $v;
			$this->modifiedColumns[] = UnitPeer::UNICODE;
		}

	} // setUnicode()

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

			$this->unit_id = $rs->getFloat($startcol + 0);

			$this->base_id = $rs->getFloat($startcol + 1);

			$this->conversion = $rs->getFloat($startcol + 2);

			$this->description = $rs->getString($startcol + 3);

			$this->name = $rs->getString($startcol + 4);

			$this->symbol = $rs->getString($startcol + 5);

			$this->unicode = $rs->getString($startcol + 6);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 7; // 7 = UnitPeer::NUM_COLUMNS - UnitPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Unit object", $e);
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
			$con = Propel::getConnection(UnitPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			UnitPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(UnitPeer::DATABASE_NAME);
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

			if ($this->aUnitRelatedByBaseId !== null) {
				if ($this->aUnitRelatedByBaseId->isModified()) {
					$affectedRows += $this->aUnitRelatedByBaseId->save($con);
				}
				$this->setUnitRelatedByBaseId($this->aUnitRelatedByBaseId);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = UnitPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += UnitPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collAttributes !== null) {
				foreach($this->collAttributes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentAttributes !== null) {
				foreach($this->collEquipmentAttributes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentAttributeValues !== null) {
				foreach($this->collEquipmentAttributeValues as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorAttributes !== null) {
				foreach($this->collSensorAttributes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collUnitsRelatedByBaseId !== null) {
				foreach($this->collUnitsRelatedByBaseId as $referrerFK) {
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


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aUnitRelatedByBaseId !== null) {
				if (!$this->aUnitRelatedByBaseId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aUnitRelatedByBaseId->getValidationFailures());
				}
			}


			if (($retval = UnitPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAttributes !== null) {
					foreach($this->collAttributes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentAttributes !== null) {
					foreach($this->collEquipmentAttributes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentAttributeValues !== null) {
					foreach($this->collEquipmentAttributeValues as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorAttributes !== null) {
					foreach($this->collSensorAttributes as $referrerFK) {
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
		$pos = UnitPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getBaseId();
				break;
			case 2:
				return $this->getConversion();
				break;
			case 3:
				return $this->getDescription();
				break;
			case 4:
				return $this->getName();
				break;
			case 5:
				return $this->getSymbol();
				break;
			case 6:
				return $this->getUnicode();
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
		$keys = UnitPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getBaseId(),
			$keys[2] => $this->getConversion(),
			$keys[3] => $this->getDescription(),
			$keys[4] => $this->getName(),
			$keys[5] => $this->getSymbol(),
			$keys[6] => $this->getUnicode(),
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
		$pos = UnitPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setBaseId($value);
				break;
			case 2:
				$this->setConversion($value);
				break;
			case 3:
				$this->setDescription($value);
				break;
			case 4:
				$this->setName($value);
				break;
			case 5:
				$this->setSymbol($value);
				break;
			case 6:
				$this->setUnicode($value);
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
		$keys = UnitPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setBaseId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setConversion($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDescription($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setSymbol($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setUnicode($arr[$keys[6]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(UnitPeer::DATABASE_NAME);

		if ($this->isColumnModified(UnitPeer::UNIT_ID)) $criteria->add(UnitPeer::UNIT_ID, $this->unit_id);
		if ($this->isColumnModified(UnitPeer::BASE_ID)) $criteria->add(UnitPeer::BASE_ID, $this->base_id);
		if ($this->isColumnModified(UnitPeer::CONVERSION)) $criteria->add(UnitPeer::CONVERSION, $this->conversion);
		if ($this->isColumnModified(UnitPeer::DESCRIPTION)) $criteria->add(UnitPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(UnitPeer::NAME)) $criteria->add(UnitPeer::NAME, $this->name);
		if ($this->isColumnModified(UnitPeer::SYMBOL)) $criteria->add(UnitPeer::SYMBOL, $this->symbol);
		if ($this->isColumnModified(UnitPeer::UNICODE)) $criteria->add(UnitPeer::UNICODE, $this->unicode);

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
		$criteria = new Criteria(UnitPeer::DATABASE_NAME);

		$criteria->add(UnitPeer::UNIT_ID, $this->unit_id);

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
	 * Generic method to set the primary key (unit_id column).
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
	 * @param      object $copyObj An object of Unit (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setBaseId($this->base_id);

		$copyObj->setConversion($this->conversion);

		$copyObj->setDescription($this->description);

		$copyObj->setName($this->name);

		$copyObj->setSymbol($this->symbol);

		$copyObj->setUnicode($this->unicode);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getAttributes() as $relObj) {
				$copyObj->addAttribute($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentAttributes() as $relObj) {
				$copyObj->addEquipmentAttribute($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentAttributeValues() as $relObj) {
				$copyObj->addEquipmentAttributeValue($relObj->copy($deepCopy));
			}

			foreach($this->getSensorAttributes() as $relObj) {
				$copyObj->addSensorAttribute($relObj->copy($deepCopy));
			}

			foreach($this->getUnitsRelatedByBaseId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addUnitRelatedByBaseId($relObj->copy($deepCopy));
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
	 * @return     Unit Clone of current object.
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
	 * @return     UnitPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new UnitPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Unit object.
	 *
	 * @param      Unit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setUnitRelatedByBaseId($v)
	{


		if ($v === null) {
			$this->setBaseId(NULL);
		} else {
			$this->setBaseId($v->getId());
		}


		$this->aUnitRelatedByBaseId = $v;
	}


	/**
	 * Get the associated Unit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Unit The associated Unit object.
	 * @throws     PropelException
	 */
	public function getUnitRelatedByBaseId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseUnitPeer.php';

		if ($this->aUnitRelatedByBaseId === null && ($this->base_id > 0)) {

			$this->aUnitRelatedByBaseId = UnitPeer::retrieveByPK($this->base_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = UnitPeer::retrieveByPK($this->base_id, $con);
			   $obj->addUnitsRelatedByBaseId($this);
			 */
		}
		return $this->aUnitRelatedByBaseId;
	}

	/**
	 * Temporary storage of collAttributes to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initAttributes()
	{
		if ($this->collAttributes === null) {
			$this->collAttributes = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit has previously
	 * been saved, it will retrieve related Attributes from storage.
	 * If this Unit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getAttributes($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAttributes === null) {
			if ($this->isNew()) {
			   $this->collAttributes = array();
			} else {

				$criteria->add(AttributePeer::UNIT_ID, $this->getId());

				AttributePeer::addSelectColumns($criteria);
				$this->collAttributes = AttributePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AttributePeer::UNIT_ID, $this->getId());

				AttributePeer::addSelectColumns($criteria);
				if (!isset($this->lastAttributeCriteria) || !$this->lastAttributeCriteria->equals($criteria)) {
					$this->collAttributes = AttributePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAttributeCriteria = $criteria;
		return $this->collAttributes;
	}

	/**
	 * Returns the number of related Attributes.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countAttributes($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(AttributePeer::UNIT_ID, $this->getId());

		return AttributePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Attribute object to this object
	 * through the Attribute foreign key attribute
	 *
	 * @param      Attribute $l Attribute
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAttribute(Attribute $l)
	{
		$this->collAttributes[] = $l;
		$l->setUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit is new, it will return
	 * an empty collection; or if this Unit has previously
	 * been saved, it will retrieve related Attributes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Unit.
	 */
	public function getAttributesJoinEquipmentClass($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAttributes === null) {
			if ($this->isNew()) {
				$this->collAttributes = array();
			} else {

				$criteria->add(AttributePeer::UNIT_ID, $this->getId());

				$this->collAttributes = AttributePeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AttributePeer::UNIT_ID, $this->getId());

			if (!isset($this->lastAttributeCriteria) || !$this->lastAttributeCriteria->equals($criteria)) {
				$this->collAttributes = AttributePeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		}
		$this->lastAttributeCriteria = $criteria;

		return $this->collAttributes;
	}

	/**
	 * Temporary storage of collEquipmentAttributes to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentAttributes()
	{
		if ($this->collEquipmentAttributes === null) {
			$this->collEquipmentAttributes = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit has previously
	 * been saved, it will retrieve related EquipmentAttributes from storage.
	 * If this Unit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentAttributes($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributes === null) {
			if ($this->isNew()) {
			   $this->collEquipmentAttributes = array();
			} else {

				$criteria->add(EquipmentAttributePeer::UNIT_ID, $this->getId());

				EquipmentAttributePeer::addSelectColumns($criteria);
				$this->collEquipmentAttributes = EquipmentAttributePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentAttributePeer::UNIT_ID, $this->getId());

				EquipmentAttributePeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentAttributeCriteria) || !$this->lastEquipmentAttributeCriteria->equals($criteria)) {
					$this->collEquipmentAttributes = EquipmentAttributePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentAttributeCriteria = $criteria;
		return $this->collEquipmentAttributes;
	}

	/**
	 * Returns the number of related EquipmentAttributes.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentAttributes($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentAttributePeer::UNIT_ID, $this->getId());

		return EquipmentAttributePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentAttribute object to this object
	 * through the EquipmentAttribute foreign key attribute
	 *
	 * @param      EquipmentAttribute $l EquipmentAttribute
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentAttribute(EquipmentAttribute $l)
	{
		$this->collEquipmentAttributes[] = $l;
		$l->setUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit is new, it will return
	 * an empty collection; or if this Unit has previously
	 * been saved, it will retrieve related EquipmentAttributes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Unit.
	 */
	public function getEquipmentAttributesJoinEquipmentAttributeRelatedByParentId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributes === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributes = array();
			} else {

				$criteria->add(EquipmentAttributePeer::UNIT_ID, $this->getId());

				$this->collEquipmentAttributes = EquipmentAttributePeer::doSelectJoinEquipmentAttributeRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributePeer::UNIT_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeCriteria) || !$this->lastEquipmentAttributeCriteria->equals($criteria)) {
				$this->collEquipmentAttributes = EquipmentAttributePeer::doSelectJoinEquipmentAttributeRelatedByParentId($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeCriteria = $criteria;

		return $this->collEquipmentAttributes;
	}

	/**
	 * Temporary storage of collEquipmentAttributeValues to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentAttributeValues()
	{
		if ($this->collEquipmentAttributeValues === null) {
			$this->collEquipmentAttributeValues = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 * If this Unit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentAttributeValues($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
			   $this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::UNIT_ID, $this->getId());

				EquipmentAttributeValuePeer::addSelectColumns($criteria);
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentAttributeValuePeer::UNIT_ID, $this->getId());

				EquipmentAttributeValuePeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
					$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;
		return $this->collEquipmentAttributeValues;
	}

	/**
	 * Returns the number of related EquipmentAttributeValues.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentAttributeValues($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentAttributeValuePeer::UNIT_ID, $this->getId());

		return EquipmentAttributeValuePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentAttributeValue object to this object
	 * through the EquipmentAttributeValue foreign key attribute
	 *
	 * @param      EquipmentAttributeValue $l EquipmentAttributeValue
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentAttributeValue(EquipmentAttributeValue $l)
	{
		$this->collEquipmentAttributeValues[] = $l;
		$l->setUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit is new, it will return
	 * an empty collection; or if this Unit has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Unit.
	 */
	public function getEquipmentAttributeValuesJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::UNIT_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::UNIT_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit is new, it will return
	 * an empty collection; or if this Unit has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Unit.
	 */
	public function getEquipmentAttributeValuesJoinEquipmentAttribute($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::UNIT_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttribute($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::UNIT_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttribute($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit is new, it will return
	 * an empty collection; or if this Unit has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Unit.
	 */
	public function getEquipmentAttributeValuesJoinEquipmentAttributeClass($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::UNIT_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttributeClass($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::UNIT_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttributeClass($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
	}

	/**
	 * Temporary storage of collSensorAttributes to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorAttributes()
	{
		if ($this->collSensorAttributes === null) {
			$this->collSensorAttributes = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit has previously
	 * been saved, it will retrieve related SensorAttributes from storage.
	 * If this Unit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorAttributes($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorAttributes === null) {
			if ($this->isNew()) {
			   $this->collSensorAttributes = array();
			} else {

				$criteria->add(SensorAttributePeer::UNIT_ID, $this->getId());

				SensorAttributePeer::addSelectColumns($criteria);
				$this->collSensorAttributes = SensorAttributePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorAttributePeer::UNIT_ID, $this->getId());

				SensorAttributePeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorAttributeCriteria) || !$this->lastSensorAttributeCriteria->equals($criteria)) {
					$this->collSensorAttributes = SensorAttributePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorAttributeCriteria = $criteria;
		return $this->collSensorAttributes;
	}

	/**
	 * Returns the number of related SensorAttributes.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorAttributes($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorAttributePeer::UNIT_ID, $this->getId());

		return SensorAttributePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorAttribute object to this object
	 * through the SensorAttribute foreign key attribute
	 *
	 * @param      SensorAttribute $l SensorAttribute
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorAttribute(SensorAttribute $l)
	{
		$this->collSensorAttributes[] = $l;
		$l->setUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit is new, it will return
	 * an empty collection; or if this Unit has previously
	 * been saved, it will retrieve related SensorAttributes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Unit.
	 */
	public function getSensorAttributesJoinAttribute($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorAttributes === null) {
			if ($this->isNew()) {
				$this->collSensorAttributes = array();
			} else {

				$criteria->add(SensorAttributePeer::UNIT_ID, $this->getId());

				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinAttribute($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorAttributePeer::UNIT_ID, $this->getId());

			if (!isset($this->lastSensorAttributeCriteria) || !$this->lastSensorAttributeCriteria->equals($criteria)) {
				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinAttribute($criteria, $con);
			}
		}
		$this->lastSensorAttributeCriteria = $criteria;

		return $this->collSensorAttributes;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit is new, it will return
	 * an empty collection; or if this Unit has previously
	 * been saved, it will retrieve related SensorAttributes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Unit.
	 */
	public function getSensorAttributesJoinSensor($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorAttributes === null) {
			if ($this->isNew()) {
				$this->collSensorAttributes = array();
			} else {

				$criteria->add(SensorAttributePeer::UNIT_ID, $this->getId());

				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinSensor($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorAttributePeer::UNIT_ID, $this->getId());

			if (!isset($this->lastSensorAttributeCriteria) || !$this->lastSensorAttributeCriteria->equals($criteria)) {
				$this->collSensorAttributes = SensorAttributePeer::doSelectJoinSensor($criteria, $con);
			}
		}
		$this->lastSensorAttributeCriteria = $criteria;

		return $this->collSensorAttributes;
	}

	/**
	 * Temporary storage of collUnitsRelatedByBaseId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initUnitsRelatedByBaseId()
	{
		if ($this->collUnitsRelatedByBaseId === null) {
			$this->collUnitsRelatedByBaseId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Unit has previously
	 * been saved, it will retrieve related UnitsRelatedByBaseId from storage.
	 * If this Unit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getUnitsRelatedByBaseId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseUnitPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collUnitsRelatedByBaseId === null) {
			if ($this->isNew()) {
			   $this->collUnitsRelatedByBaseId = array();
			} else {

				$criteria->add(UnitPeer::BASE_ID, $this->getId());

				UnitPeer::addSelectColumns($criteria);
				$this->collUnitsRelatedByBaseId = UnitPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(UnitPeer::BASE_ID, $this->getId());

				UnitPeer::addSelectColumns($criteria);
				if (!isset($this->lastUnitRelatedByBaseIdCriteria) || !$this->lastUnitRelatedByBaseIdCriteria->equals($criteria)) {
					$this->collUnitsRelatedByBaseId = UnitPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastUnitRelatedByBaseIdCriteria = $criteria;
		return $this->collUnitsRelatedByBaseId;
	}

	/**
	 * Returns the number of related UnitsRelatedByBaseId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countUnitsRelatedByBaseId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseUnitPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(UnitPeer::BASE_ID, $this->getId());

		return UnitPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Unit object to this object
	 * through the Unit foreign key attribute
	 *
	 * @param      Unit $l Unit
	 * @return     void
	 * @throws     PropelException
	 */
	public function addUnitRelatedByBaseId(Unit $l)
	{
		$this->collUnitsRelatedByBaseId[] = $l;
		$l->setUnitRelatedByBaseId($this);
	}

} // BaseUnit
