<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SpecimenComponentMaterialPeer.php';

/**
 * Base class that represents a row from the 'SPECCOMP_MATERIAL' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSpecimenComponentMaterial extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SpecimenComponentMaterialPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the specimen_component_id field.
	 * @var        double
	 */
	protected $specimen_component_id;


	/**
	 * The value for the material_type_id field.
	 * @var        double
	 */
	protected $material_type_id;


	/**
	 * The value for the prototype_material_id field.
	 * @var        double
	 */
	protected $prototype_material_id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * @var        SpecimenComponent
	 */
	protected $aSpecimenComponent;

	/**
	 * @var        MaterialType
	 */
	protected $aMaterialType;

	/**
	 * @var        SpecimenComponentMaterial
	 */
	protected $aSpecimenComponentMaterialRelatedByPrototypeMaterialId;

	/**
	 * Collection to store aggregation of collSpecimenComponentMaterialsRelatedByPrototypeMaterialId.
	 * @var        array
	 */
	protected $collSpecimenComponentMaterialsRelatedByPrototypeMaterialId;

	/**
	 * The criteria used to select the current contents of collSpecimenComponentMaterialsRelatedByPrototypeMaterialId.
	 * @var        Criteria
	 */
	protected $lastSpecimenComponentMaterialRelatedByPrototypeMaterialIdCriteria = null;

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
	 * Collection to store aggregation of collSpecimenComponentMaterialFiles.
	 * @var        array
	 */
	protected $collSpecimenComponentMaterialFiles;

	/**
	 * The criteria used to select the current contents of collSpecimenComponentMaterialFiles.
	 * @var        Criteria
	 */
	protected $lastSpecimenComponentMaterialFileCriteria = null;

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
	 * Get the [specimen_component_id] column value.
	 * 
	 * @return     double
	 */
	public function getSpecimenComponentId()
	{

		return $this->specimen_component_id;
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
	 * Get the [prototype_material_id] column value.
	 * 
	 * @return     double
	 */
	public function getPrototypeMaterialId()
	{

		return $this->prototype_material_id;
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
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
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
			$this->modifiedColumns[] = SpecimenComponentMaterialPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [specimen_component_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSpecimenComponentId($v)
	{

		if ($this->specimen_component_id !== $v) {
			$this->specimen_component_id = $v;
			$this->modifiedColumns[] = SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID;
		}

		if ($this->aSpecimenComponent !== null && $this->aSpecimenComponent->getId() !== $v) {
			$this->aSpecimenComponent = null;
		}

	} // setSpecimenComponentId()

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
			$this->modifiedColumns[] = SpecimenComponentMaterialPeer::MATERIAL_TYPE_ID;
		}

		if ($this->aMaterialType !== null && $this->aMaterialType->getId() !== $v) {
			$this->aMaterialType = null;
		}

	} // setMaterialTypeId()

	/**
	 * Set the value of [prototype_material_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPrototypeMaterialId($v)
	{

		if ($this->prototype_material_id !== $v) {
			$this->prototype_material_id = $v;
			$this->modifiedColumns[] = SpecimenComponentMaterialPeer::PROTOTYPE_MATERIAL_ID;
		}

		if ($this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId !== null && $this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId->getId() !== $v) {
			$this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId = null;
		}

	} // setPrototypeMaterialId()

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
			$this->modifiedColumns[] = SpecimenComponentMaterialPeer::NAME;
		}

	} // setName()

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
			$this->modifiedColumns[] = SpecimenComponentMaterialPeer::DESCRIPTION;
		}

	} // setDescription()

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

			$this->specimen_component_id = $rs->getFloat($startcol + 1);

			$this->material_type_id = $rs->getFloat($startcol + 2);

			$this->prototype_material_id = $rs->getFloat($startcol + 3);

			$this->name = $rs->getString($startcol + 4);

			$this->description = $rs->getString($startcol + 5);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = SpecimenComponentMaterialPeer::NUM_COLUMNS - SpecimenComponentMaterialPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SpecimenComponentMaterial object", $e);
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
			$con = Propel::getConnection(SpecimenComponentMaterialPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SpecimenComponentMaterialPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SpecimenComponentMaterialPeer::DATABASE_NAME);
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

			if ($this->aMaterialType !== null) {
				if ($this->aMaterialType->isModified()) {
					$affectedRows += $this->aMaterialType->save($con);
				}
				$this->setMaterialType($this->aMaterialType);
			}

			if ($this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId !== null) {
				if ($this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId->isModified()) {
					$affectedRows += $this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId->save($con);
				}
				$this->setSpecimenComponentMaterialRelatedByPrototypeMaterialId($this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SpecimenComponentMaterialPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SpecimenComponentMaterialPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId !== null) {
				foreach($this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId as $referrerFK) {
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

			if ($this->collSpecimenComponentMaterialFiles !== null) {
				foreach($this->collSpecimenComponentMaterialFiles as $referrerFK) {
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

			if ($this->aSpecimenComponent !== null) {
				if (!$this->aSpecimenComponent->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSpecimenComponent->getValidationFailures());
				}
			}

			if ($this->aMaterialType !== null) {
				if (!$this->aMaterialType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMaterialType->getValidationFailures());
				}
			}

			if ($this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId !== null) {
				if (!$this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId->getValidationFailures());
				}
			}


			if (($retval = SpecimenComponentMaterialPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collSpecimenComponentMaterialPropertys !== null) {
					foreach($this->collSpecimenComponentMaterialPropertys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSpecimenComponentMaterialFiles !== null) {
					foreach($this->collSpecimenComponentMaterialFiles as $referrerFK) {
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
		$pos = SpecimenComponentMaterialPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getMaterialTypeId();
				break;
			case 3:
				return $this->getPrototypeMaterialId();
				break;
			case 4:
				return $this->getName();
				break;
			case 5:
				return $this->getDescription();
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
		$keys = SpecimenComponentMaterialPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getSpecimenComponentId(),
			$keys[2] => $this->getMaterialTypeId(),
			$keys[3] => $this->getPrototypeMaterialId(),
			$keys[4] => $this->getName(),
			$keys[5] => $this->getDescription(),
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
		$pos = SpecimenComponentMaterialPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setMaterialTypeId($value);
				break;
			case 3:
				$this->setPrototypeMaterialId($value);
				break;
			case 4:
				$this->setName($value);
				break;
			case 5:
				$this->setDescription($value);
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
		$keys = SpecimenComponentMaterialPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setSpecimenComponentId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setMaterialTypeId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPrototypeMaterialId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDescription($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SpecimenComponentMaterialPeer::DATABASE_NAME);

		if ($this->isColumnModified(SpecimenComponentMaterialPeer::ID)) $criteria->add(SpecimenComponentMaterialPeer::ID, $this->id);
		if ($this->isColumnModified(SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID)) $criteria->add(SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID, $this->specimen_component_id);
		if ($this->isColumnModified(SpecimenComponentMaterialPeer::MATERIAL_TYPE_ID)) $criteria->add(SpecimenComponentMaterialPeer::MATERIAL_TYPE_ID, $this->material_type_id);
		if ($this->isColumnModified(SpecimenComponentMaterialPeer::PROTOTYPE_MATERIAL_ID)) $criteria->add(SpecimenComponentMaterialPeer::PROTOTYPE_MATERIAL_ID, $this->prototype_material_id);
		if ($this->isColumnModified(SpecimenComponentMaterialPeer::NAME)) $criteria->add(SpecimenComponentMaterialPeer::NAME, $this->name);
		if ($this->isColumnModified(SpecimenComponentMaterialPeer::DESCRIPTION)) $criteria->add(SpecimenComponentMaterialPeer::DESCRIPTION, $this->description);

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
		$criteria = new Criteria(SpecimenComponentMaterialPeer::DATABASE_NAME);

		$criteria->add(SpecimenComponentMaterialPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SpecimenComponentMaterial (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setSpecimenComponentId($this->specimen_component_id);

		$copyObj->setMaterialTypeId($this->material_type_id);

		$copyObj->setPrototypeMaterialId($this->prototype_material_id);

		$copyObj->setName($this->name);

		$copyObj->setDescription($this->description);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getSpecimenComponentMaterialsRelatedByPrototypeMaterialId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addSpecimenComponentMaterialRelatedByPrototypeMaterialId($relObj->copy($deepCopy));
			}

			foreach($this->getSpecimenComponentMaterialPropertys() as $relObj) {
				$copyObj->addSpecimenComponentMaterialProperty($relObj->copy($deepCopy));
			}

			foreach($this->getSpecimenComponentMaterialFiles() as $relObj) {
				$copyObj->addSpecimenComponentMaterialFile($relObj->copy($deepCopy));
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
	 * @return     SpecimenComponentMaterial Clone of current object.
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
	 * @return     SpecimenComponentMaterialPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SpecimenComponentMaterialPeer();
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

		if ($this->aSpecimenComponent === null && ($this->specimen_component_id > 0)) {

			$this->aSpecimenComponent = SpecimenComponentPeer::retrieveByPK($this->specimen_component_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SpecimenComponentPeer::retrieveByPK($this->specimen_component_id, $con);
			   $obj->addSpecimenComponents($this);
			 */
		}
		return $this->aSpecimenComponent;
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
	 * Declares an association between this object and a SpecimenComponentMaterial object.
	 *
	 * @param      SpecimenComponentMaterial $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSpecimenComponentMaterialRelatedByPrototypeMaterialId($v)
	{


		if ($v === null) {
			$this->setPrototypeMaterialId(NULL);
		} else {
			$this->setPrototypeMaterialId($v->getId());
		}


		$this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId = $v;
	}


	/**
	 * Get the associated SpecimenComponentMaterial object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SpecimenComponentMaterial The associated SpecimenComponentMaterial object.
	 * @throws     PropelException
	 */
	public function getSpecimenComponentMaterialRelatedByPrototypeMaterialId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPeer.php';

		if ($this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId === null && ($this->prototype_material_id > 0)) {

			$this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId = SpecimenComponentMaterialPeer::retrieveByPK($this->prototype_material_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SpecimenComponentMaterialPeer::retrieveByPK($this->prototype_material_id, $con);
			   $obj->addSpecimenComponentMaterialsRelatedByPrototypeMaterialId($this);
			 */
		}
		return $this->aSpecimenComponentMaterialRelatedByPrototypeMaterialId;
	}

	/**
	 * Temporary storage of collSpecimenComponentMaterialsRelatedByPrototypeMaterialId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSpecimenComponentMaterialsRelatedByPrototypeMaterialId()
	{
		if ($this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId === null) {
			$this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponentMaterial has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialsRelatedByPrototypeMaterialId from storage.
	 * If this SpecimenComponentMaterial is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSpecimenComponentMaterialsRelatedByPrototypeMaterialId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId === null) {
			if ($this->isNew()) {
			   $this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

				SpecimenComponentMaterialPeer::addSelectColumns($criteria);
				$this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId = SpecimenComponentMaterialPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentMaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

				SpecimenComponentMaterialPeer::addSelectColumns($criteria);
				if (!isset($this->lastSpecimenComponentMaterialRelatedByPrototypeMaterialIdCriteria) || !$this->lastSpecimenComponentMaterialRelatedByPrototypeMaterialIdCriteria->equals($criteria)) {
					$this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId = SpecimenComponentMaterialPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSpecimenComponentMaterialRelatedByPrototypeMaterialIdCriteria = $criteria;
		return $this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId;
	}

	/**
	 * Returns the number of related SpecimenComponentMaterialsRelatedByPrototypeMaterialId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSpecimenComponentMaterialsRelatedByPrototypeMaterialId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SpecimenComponentMaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

		return SpecimenComponentMaterialPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SpecimenComponentMaterial object to this object
	 * through the SpecimenComponentMaterial foreign key attribute
	 *
	 * @param      SpecimenComponentMaterial $l SpecimenComponentMaterial
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSpecimenComponentMaterialRelatedByPrototypeMaterialId(SpecimenComponentMaterial $l)
	{
		$this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId[] = $l;
		$l->setSpecimenComponentMaterialRelatedByPrototypeMaterialId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponentMaterial is new, it will return
	 * an empty collection; or if this SpecimenComponentMaterial has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialsRelatedByPrototypeMaterialId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SpecimenComponentMaterial.
	 */
	public function getSpecimenComponentMaterialsRelatedByPrototypeMaterialIdJoinSpecimenComponent($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

				$this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId = SpecimenComponentMaterialPeer::doSelectJoinSpecimenComponent($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialRelatedByPrototypeMaterialIdCriteria) || !$this->lastSpecimenComponentMaterialRelatedByPrototypeMaterialIdCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId = SpecimenComponentMaterialPeer::doSelectJoinSpecimenComponent($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialRelatedByPrototypeMaterialIdCriteria = $criteria;

		return $this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponentMaterial is new, it will return
	 * an empty collection; or if this SpecimenComponentMaterial has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialsRelatedByPrototypeMaterialId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SpecimenComponentMaterial.
	 */
	public function getSpecimenComponentMaterialsRelatedByPrototypeMaterialIdJoinMaterialType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

				$this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId = SpecimenComponentMaterialPeer::doSelectJoinMaterialType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialRelatedByPrototypeMaterialIdCriteria) || !$this->lastSpecimenComponentMaterialRelatedByPrototypeMaterialIdCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId = SpecimenComponentMaterialPeer::doSelectJoinMaterialType($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialRelatedByPrototypeMaterialIdCriteria = $criteria;

		return $this->collSpecimenComponentMaterialsRelatedByPrototypeMaterialId;
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
	 * Otherwise if this SpecimenComponentMaterial has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialPropertys from storage.
	 * If this SpecimenComponentMaterial is new, it will return
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

				$criteria->add(SpecimenComponentMaterialPropertyPeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

				SpecimenComponentMaterialPropertyPeer::addSelectColumns($criteria);
				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentMaterialPropertyPeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

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

		$criteria->add(SpecimenComponentMaterialPropertyPeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

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
		$l->setSpecimenComponentMaterial($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponentMaterial is new, it will return
	 * an empty collection; or if this SpecimenComponentMaterial has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SpecimenComponentMaterial.
	 */
	public function getSpecimenComponentMaterialPropertysJoinMaterialTypeProperty($criteria = null, $con = null)
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

				$criteria->add(SpecimenComponentMaterialPropertyPeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinMaterialTypeProperty($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialPropertyPeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialPropertyCriteria) || !$this->lastSpecimenComponentMaterialPropertyCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinMaterialTypeProperty($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialPropertyCriteria = $criteria;

		return $this->collSpecimenComponentMaterialPropertys;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponentMaterial is new, it will return
	 * an empty collection; or if this SpecimenComponentMaterial has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SpecimenComponentMaterial.
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

				$criteria->add(SpecimenComponentMaterialPropertyPeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialPropertyPeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialPropertyCriteria) || !$this->lastSpecimenComponentMaterialPropertyCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialPropertyCriteria = $criteria;

		return $this->collSpecimenComponentMaterialPropertys;
	}

	/**
	 * Temporary storage of collSpecimenComponentMaterialFiles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSpecimenComponentMaterialFiles()
	{
		if ($this->collSpecimenComponentMaterialFiles === null) {
			$this->collSpecimenComponentMaterialFiles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponentMaterial has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialFiles from storage.
	 * If this SpecimenComponentMaterial is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSpecimenComponentMaterialFiles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialFiles === null) {
			if ($this->isNew()) {
			   $this->collSpecimenComponentMaterialFiles = array();
			} else {

				$criteria->add(SpecimenComponentMaterialFilePeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

				SpecimenComponentMaterialFilePeer::addSelectColumns($criteria);
				$this->collSpecimenComponentMaterialFiles = SpecimenComponentMaterialFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentMaterialFilePeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

				SpecimenComponentMaterialFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastSpecimenComponentMaterialFileCriteria) || !$this->lastSpecimenComponentMaterialFileCriteria->equals($criteria)) {
					$this->collSpecimenComponentMaterialFiles = SpecimenComponentMaterialFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSpecimenComponentMaterialFileCriteria = $criteria;
		return $this->collSpecimenComponentMaterialFiles;
	}

	/**
	 * Returns the number of related SpecimenComponentMaterialFiles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSpecimenComponentMaterialFiles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SpecimenComponentMaterialFilePeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

		return SpecimenComponentMaterialFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SpecimenComponentMaterialFile object to this object
	 * through the SpecimenComponentMaterialFile foreign key attribute
	 *
	 * @param      SpecimenComponentMaterialFile $l SpecimenComponentMaterialFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSpecimenComponentMaterialFile(SpecimenComponentMaterialFile $l)
	{
		$this->collSpecimenComponentMaterialFiles[] = $l;
		$l->setSpecimenComponentMaterial($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponentMaterial is new, it will return
	 * an empty collection; or if this SpecimenComponentMaterial has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SpecimenComponentMaterial.
	 */
	public function getSpecimenComponentMaterialFilesJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialFiles === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentMaterialFiles = array();
			} else {

				$criteria->add(SpecimenComponentMaterialFilePeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

				$this->collSpecimenComponentMaterialFiles = SpecimenComponentMaterialFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialFilePeer::SPECIMEN_COMPONENT_MATERIAL_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialFileCriteria) || !$this->lastSpecimenComponentMaterialFileCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterialFiles = SpecimenComponentMaterialFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialFileCriteria = $criteria;

		return $this->collSpecimenComponentMaterialFiles;
	}

} // BaseSpecimenComponentMaterial
