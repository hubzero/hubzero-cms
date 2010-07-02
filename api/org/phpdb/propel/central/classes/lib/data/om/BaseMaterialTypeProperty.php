<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/MaterialTypePropertyPeer.php';

/**
 * Base class that represents a row from the 'MATERIAL_TYPE_PROPERTY' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseMaterialTypeProperty extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        MaterialTypePropertyPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the datatype field.
	 * @var        string
	 */
	protected $datatype;


	/**
	 * The value for the display_name field.
	 * @var        string
	 */
	protected $display_name;


	/**
	 * The value for the material_type_id field.
	 * @var        double
	 */
	protected $material_type_id;


	/**
	 * The value for the measurement_unit_category_id field.
	 * @var        double
	 */
	protected $measurement_unit_category_id;


	/**
	 * The value for the options field.
	 * @var        string
	 */
	protected $options;


	/**
	 * The value for the required field.
	 * @var        double
	 */
	protected $required;


	/**
	 * The value for the status field.
	 * @var        double
	 */
	protected $status;


	/**
	 * The value for the units field.
	 * @var        string
	 */
	protected $units;

	/**
	 * @var        MaterialType
	 */
	protected $aMaterialType;

	/**
	 * @var        MeasurementUnitCategory
	 */
	protected $aMeasurementUnitCategory;

	/**
	 * Collection to store aggregation of collMaterialPropertys.
	 * @var        array
	 */
	protected $collMaterialPropertys;

	/**
	 * The criteria used to select the current contents of collMaterialPropertys.
	 * @var        Criteria
	 */
	protected $lastMaterialPropertyCriteria = null;

	/**
	 * Collection to store aggregation of collSpecimenComponentMaterialPropertys.
	 * @var        array
	 */
	protected $collSpecimenComponentMaterialPropertys;

	/**
	 * The criteria used to select the current contents of collSpecimenComponentMaterialPropertys.
	 * @var        Criteria
	 */
	protected $lastSpecimenComponentMaterialPropertyCriteria = null;

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
	 * Get the [datatype] column value.
	 * 
	 * @return     string
	 */
	public function getDataType()
	{

		return $this->datatype;
	}

	/**
	 * Get the [display_name] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayName()
	{

		return $this->display_name;
	}

	/**
	 * Get the [material_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getMaterialTypeId()
	{

		return $this->material_type_id;
	}

	/**
	 * Get the [measurement_unit_category_id] column value.
	 * 
	 * @return     double
	 */
	public function getMeasurementUnitCategoryId()
	{

		return $this->measurement_unit_category_id;
	}

	/**
	 * Get the [options] column value.
	 * 
	 * @return     string
	 */
	public function getOptions()
	{

		return $this->options;
	}

	/**
	 * Get the [required] column value.
	 * 
	 * @return     double
	 */
	public function getRequired()
	{

		return $this->required;
	}

	/**
	 * Get the [status] column value.
	 * 
	 * @return     double
	 */
	public function getStatus()
	{

		return $this->status;
	}

	/**
	 * Get the [units] column value.
	 * 
	 * @return     string
	 */
	public function getUnits()
	{

		return $this->units;
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
			$this->modifiedColumns[] = MaterialTypePropertyPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [datatype] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDataType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->datatype !== $v) {
			$this->datatype = $v;
			$this->modifiedColumns[] = MaterialTypePropertyPeer::DATATYPE;
		}

	} // setDataType()

	/**
	 * Set the value of [display_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDisplayName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->display_name !== $v) {
			$this->display_name = $v;
			$this->modifiedColumns[] = MaterialTypePropertyPeer::DISPLAY_NAME;
		}

	} // setDisplayName()

	/**
	 * Set the value of [material_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMaterialTypeId($v)
	{

		if ($this->material_type_id !== $v) {
			$this->material_type_id = $v;
			$this->modifiedColumns[] = MaterialTypePropertyPeer::MATERIAL_TYPE_ID;
		}

		if ($this->aMaterialType !== null && $this->aMaterialType->getId() !== $v) {
			$this->aMaterialType = null;
		}

	} // setMaterialTypeId()

	/**
	 * Set the value of [measurement_unit_category_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMeasurementUnitCategoryId($v)
	{

		if ($this->measurement_unit_category_id !== $v) {
			$this->measurement_unit_category_id = $v;
			$this->modifiedColumns[] = MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID;
		}

		if ($this->aMeasurementUnitCategory !== null && $this->aMeasurementUnitCategory->getId() !== $v) {
			$this->aMeasurementUnitCategory = null;
		}

	} // setMeasurementUnitCategoryId()

	/**
	 * Set the value of [options] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setOptions($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->options !== $v) {
			$this->options = $v;
			$this->modifiedColumns[] = MaterialTypePropertyPeer::OPTIONS;
		}

	} // setOptions()

	/**
	 * Set the value of [required] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRequired($v)
	{

		if ($this->required !== $v) {
			$this->required = $v;
			$this->modifiedColumns[] = MaterialTypePropertyPeer::REQUIRED;
		}

	} // setRequired()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setStatus($v)
	{

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = MaterialTypePropertyPeer::STATUS;
		}

	} // setStatus()

	/**
	 * Set the value of [units] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setUnits($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->units !== $v) {
			$this->units = $v;
			$this->modifiedColumns[] = MaterialTypePropertyPeer::UNITS;
		}

	} // setUnits()

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

			$this->datatype = $rs->getString($startcol + 1);

			$this->display_name = $rs->getString($startcol + 2);

			$this->material_type_id = $rs->getFloat($startcol + 3);

			$this->measurement_unit_category_id = $rs->getFloat($startcol + 4);

			$this->options = $rs->getString($startcol + 5);

			$this->required = $rs->getFloat($startcol + 6);

			$this->status = $rs->getFloat($startcol + 7);

			$this->units = $rs->getString($startcol + 8);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = MaterialTypePropertyPeer::NUM_COLUMNS - MaterialTypePropertyPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating MaterialTypeProperty object", $e);
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
			$con = Propel::getConnection(MaterialTypePropertyPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			MaterialTypePropertyPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(MaterialTypePropertyPeer::DATABASE_NAME);
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

			if ($this->aMaterialType !== null) {
				if ($this->aMaterialType->isModified()) {
					$affectedRows += $this->aMaterialType->save($con);
				}
				$this->setMaterialType($this->aMaterialType);
			}

			if ($this->aMeasurementUnitCategory !== null) {
				if ($this->aMeasurementUnitCategory->isModified()) {
					$affectedRows += $this->aMeasurementUnitCategory->save($con);
				}
				$this->setMeasurementUnitCategory($this->aMeasurementUnitCategory);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = MaterialTypePropertyPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += MaterialTypePropertyPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collMaterialPropertys !== null) {
				foreach($this->collMaterialPropertys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSpecimenComponentMaterialPropertys !== null) {
				foreach($this->collSpecimenComponentMaterialPropertys as $referrerFK) {
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

			if ($this->aMaterialType !== null) {
				if (!$this->aMaterialType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMaterialType->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitCategory !== null) {
				if (!$this->aMeasurementUnitCategory->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitCategory->getValidationFailures());
				}
			}


			if (($retval = MaterialTypePropertyPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collMaterialPropertys !== null) {
					foreach($this->collMaterialPropertys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSpecimenComponentMaterialPropertys !== null) {
					foreach($this->collSpecimenComponentMaterialPropertys as $referrerFK) {
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
		$pos = MaterialTypePropertyPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDataType();
				break;
			case 2:
				return $this->getDisplayName();
				break;
			case 3:
				return $this->getMaterialTypeId();
				break;
			case 4:
				return $this->getMeasurementUnitCategoryId();
				break;
			case 5:
				return $this->getOptions();
				break;
			case 6:
				return $this->getRequired();
				break;
			case 7:
				return $this->getStatus();
				break;
			case 8:
				return $this->getUnits();
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
		$keys = MaterialTypePropertyPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDataType(),
			$keys[2] => $this->getDisplayName(),
			$keys[3] => $this->getMaterialTypeId(),
			$keys[4] => $this->getMeasurementUnitCategoryId(),
			$keys[5] => $this->getOptions(),
			$keys[6] => $this->getRequired(),
			$keys[7] => $this->getStatus(),
			$keys[8] => $this->getUnits(),
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
		$pos = MaterialTypePropertyPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDataType($value);
				break;
			case 2:
				$this->setDisplayName($value);
				break;
			case 3:
				$this->setMaterialTypeId($value);
				break;
			case 4:
				$this->setMeasurementUnitCategoryId($value);
				break;
			case 5:
				$this->setOptions($value);
				break;
			case 6:
				$this->setRequired($value);
				break;
			case 7:
				$this->setStatus($value);
				break;
			case 8:
				$this->setUnits($value);
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
		$keys = MaterialTypePropertyPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDataType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDisplayName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setMaterialTypeId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setMeasurementUnitCategoryId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setOptions($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setRequired($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setStatus($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setUnits($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(MaterialTypePropertyPeer::DATABASE_NAME);

		if ($this->isColumnModified(MaterialTypePropertyPeer::ID)) $criteria->add(MaterialTypePropertyPeer::ID, $this->id);
		if ($this->isColumnModified(MaterialTypePropertyPeer::DATATYPE)) $criteria->add(MaterialTypePropertyPeer::DATATYPE, $this->datatype);
		if ($this->isColumnModified(MaterialTypePropertyPeer::DISPLAY_NAME)) $criteria->add(MaterialTypePropertyPeer::DISPLAY_NAME, $this->display_name);
		if ($this->isColumnModified(MaterialTypePropertyPeer::MATERIAL_TYPE_ID)) $criteria->add(MaterialTypePropertyPeer::MATERIAL_TYPE_ID, $this->material_type_id);
		if ($this->isColumnModified(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID)) $criteria->add(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, $this->measurement_unit_category_id);
		if ($this->isColumnModified(MaterialTypePropertyPeer::OPTIONS)) $criteria->add(MaterialTypePropertyPeer::OPTIONS, $this->options);
		if ($this->isColumnModified(MaterialTypePropertyPeer::REQUIRED)) $criteria->add(MaterialTypePropertyPeer::REQUIRED, $this->required);
		if ($this->isColumnModified(MaterialTypePropertyPeer::STATUS)) $criteria->add(MaterialTypePropertyPeer::STATUS, $this->status);
		if ($this->isColumnModified(MaterialTypePropertyPeer::UNITS)) $criteria->add(MaterialTypePropertyPeer::UNITS, $this->units);

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
		$criteria = new Criteria(MaterialTypePropertyPeer::DATABASE_NAME);

		$criteria->add(MaterialTypePropertyPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of MaterialTypeProperty (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDataType($this->datatype);

		$copyObj->setDisplayName($this->display_name);

		$copyObj->setMaterialTypeId($this->material_type_id);

		$copyObj->setMeasurementUnitCategoryId($this->measurement_unit_category_id);

		$copyObj->setOptions($this->options);

		$copyObj->setRequired($this->required);

		$copyObj->setStatus($this->status);

		$copyObj->setUnits($this->units);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getMaterialPropertys() as $relObj) {
				$copyObj->addMaterialProperty($relObj->copy($deepCopy));
			}

			foreach($this->getSpecimenComponentMaterialPropertys() as $relObj) {
				$copyObj->addSpecimenComponentMaterialProperty($relObj->copy($deepCopy));
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
	 * @return     MaterialTypeProperty Clone of current object.
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
	 * @return     MaterialTypePropertyPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new MaterialTypePropertyPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a MaterialType object.
	 *
	 * @param      MaterialType $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMaterialType($v)
	{


		if ($v === null) {
			$this->setMaterialTypeId(NULL);
		} else {
			$this->setMaterialTypeId($v->getId());
		}


		$this->aMaterialType = $v;
	}


	/**
	 * Get the associated MaterialType object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MaterialType The associated MaterialType object.
	 * @throws     PropelException
	 */
	public function getMaterialType($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMaterialTypePeer.php';

		if ($this->aMaterialType === null && ($this->material_type_id > 0)) {

			$this->aMaterialType = MaterialTypePeer::retrieveByPK($this->material_type_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MaterialTypePeer::retrieveByPK($this->material_type_id, $con);
			   $obj->addMaterialTypes($this);
			 */
		}
		return $this->aMaterialType;
	}

	/**
	 * Declares an association between this object and a MeasurementUnitCategory object.
	 *
	 * @param      MeasurementUnitCategory $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitCategory($v)
	{


		if ($v === null) {
			$this->setMeasurementUnitCategoryId(NULL);
		} else {
			$this->setMeasurementUnitCategoryId($v->getId());
		}


		$this->aMeasurementUnitCategory = $v;
	}


	/**
	 * Get the associated MeasurementUnitCategory object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnitCategory The associated MeasurementUnitCategory object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitCategory($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitCategoryPeer.php';

		if ($this->aMeasurementUnitCategory === null && ($this->measurement_unit_category_id > 0)) {

			$this->aMeasurementUnitCategory = MeasurementUnitCategoryPeer::retrieveByPK($this->measurement_unit_category_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitCategoryPeer::retrieveByPK($this->measurement_unit_category_id, $con);
			   $obj->addMeasurementUnitCategorys($this);
			 */
		}
		return $this->aMeasurementUnitCategory;
	}

	/**
	 * Temporary storage of collMaterialPropertys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initMaterialPropertys()
	{
		if ($this->collMaterialPropertys === null) {
			$this->collMaterialPropertys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MaterialTypeProperty has previously
	 * been saved, it will retrieve related MaterialPropertys from storage.
	 * If this MaterialTypeProperty is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getMaterialPropertys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialPropertys === null) {
			if ($this->isNew()) {
			   $this->collMaterialPropertys = array();
			} else {

				$criteria->add(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

				MaterialPropertyPeer::addSelectColumns($criteria);
				$this->collMaterialPropertys = MaterialPropertyPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

				MaterialPropertyPeer::addSelectColumns($criteria);
				if (!isset($this->lastMaterialPropertyCriteria) || !$this->lastMaterialPropertyCriteria->equals($criteria)) {
					$this->collMaterialPropertys = MaterialPropertyPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMaterialPropertyCriteria = $criteria;
		return $this->collMaterialPropertys;
	}

	/**
	 * Returns the number of related MaterialPropertys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countMaterialPropertys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

		return MaterialPropertyPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a MaterialProperty object to this object
	 * through the MaterialProperty foreign key attribute
	 *
	 * @param      MaterialProperty $l MaterialProperty
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMaterialProperty(MaterialProperty $l)
	{
		$this->collMaterialPropertys[] = $l;
		$l->setMaterialTypeProperty($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MaterialTypeProperty is new, it will return
	 * an empty collection; or if this MaterialTypeProperty has previously
	 * been saved, it will retrieve related MaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MaterialTypeProperty.
	 */
	public function getMaterialPropertysJoinMaterial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialPropertys === null) {
			if ($this->isNew()) {
				$this->collMaterialPropertys = array();
			} else {

				$criteria->add(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMaterial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

			if (!isset($this->lastMaterialPropertyCriteria) || !$this->lastMaterialPropertyCriteria->equals($criteria)) {
				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMaterial($criteria, $con);
			}
		}
		$this->lastMaterialPropertyCriteria = $criteria;

		return $this->collMaterialPropertys;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MaterialTypeProperty is new, it will return
	 * an empty collection; or if this MaterialTypeProperty has previously
	 * been saved, it will retrieve related MaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MaterialTypeProperty.
	 */
	public function getMaterialPropertysJoinMeasurementUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialPropertys === null) {
			if ($this->isNew()) {
				$this->collMaterialPropertys = array();
			} else {

				$criteria->add(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

			if (!isset($this->lastMaterialPropertyCriteria) || !$this->lastMaterialPropertyCriteria->equals($criteria)) {
				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastMaterialPropertyCriteria = $criteria;

		return $this->collMaterialPropertys;
	}

	/**
	 * Temporary storage of collSpecimenComponentMaterialPropertys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSpecimenComponentMaterialPropertys()
	{
		if ($this->collSpecimenComponentMaterialPropertys === null) {
			$this->collSpecimenComponentMaterialPropertys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MaterialTypeProperty has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialPropertys from storage.
	 * If this MaterialTypeProperty is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSpecimenComponentMaterialPropertys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialPropertys === null) {
			if ($this->isNew()) {
			   $this->collSpecimenComponentMaterialPropertys = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

				SpecimenComponentMaterialPropertyPeer::addSelectColumns($criteria);
				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentMaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

				SpecimenComponentMaterialPropertyPeer::addSelectColumns($criteria);
				if (!isset($this->lastSpecimenComponentMaterialPropertyCriteria) || !$this->lastSpecimenComponentMaterialPropertyCriteria->equals($criteria)) {
					$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSpecimenComponentMaterialPropertyCriteria = $criteria;
		return $this->collSpecimenComponentMaterialPropertys;
	}

	/**
	 * Returns the number of related SpecimenComponentMaterialPropertys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSpecimenComponentMaterialPropertys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SpecimenComponentMaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

		return SpecimenComponentMaterialPropertyPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SpecimenComponentMaterialProperty object to this object
	 * through the SpecimenComponentMaterialProperty foreign key attribute
	 *
	 * @param      SpecimenComponentMaterialProperty $l SpecimenComponentMaterialProperty
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSpecimenComponentMaterialProperty(SpecimenComponentMaterialProperty $l)
	{
		$this->collSpecimenComponentMaterialPropertys[] = $l;
		$l->setMaterialTypeProperty($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MaterialTypeProperty is new, it will return
	 * an empty collection; or if this MaterialTypeProperty has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MaterialTypeProperty.
	 */
	public function getSpecimenComponentMaterialPropertysJoinSpecimenComponentMaterial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialPropertys === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentMaterialPropertys = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinSpecimenComponentMaterial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialPropertyCriteria) || !$this->lastSpecimenComponentMaterialPropertyCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinSpecimenComponentMaterial($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialPropertyCriteria = $criteria;

		return $this->collSpecimenComponentMaterialPropertys;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MaterialTypeProperty is new, it will return
	 * an empty collection; or if this MaterialTypeProperty has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MaterialTypeProperty.
	 */
	public function getSpecimenComponentMaterialPropertysJoinMeasurementUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialPropertys === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentMaterialPropertys = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialPropertyCriteria) || !$this->lastSpecimenComponentMaterialPropertyCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialPropertyCriteria = $criteria;

		return $this->collSpecimenComponentMaterialPropertys;
	}

} // BaseMaterialTypeProperty
