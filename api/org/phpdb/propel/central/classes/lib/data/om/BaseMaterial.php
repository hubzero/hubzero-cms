<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/MaterialPeer.php';

/**
 * Base class that represents a row from the 'MATERIAL' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseMaterial extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        MaterialPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the expid field.
	 * @var        double
	 */
	protected $expid;


	/**
	 * The value for the material_type_id field.
	 * @var        double
	 */
	protected $material_type_id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the prototype_material_id field.
	 * @var        double
	 */
	protected $prototype_material_id;

	/**
	 * @var        Experiment
	 */
	protected $aExperiment;

	/**
	 * @var        Material
	 */
	protected $aMaterialRelatedByPrototypeMaterialId;

	/**
	 * @var        MaterialType
	 */
	protected $aMaterialType;

	/**
	 * Collection to store aggregation of collMaterialsRelatedByPrototypeMaterialId.
	 * @var        array
	 */
	protected $collMaterialsRelatedByPrototypeMaterialId;

	/**
	 * The criteria used to select the current contents of collMaterialsRelatedByPrototypeMaterialId.
	 * @var        Criteria
	 */
	protected $lastMaterialRelatedByPrototypeMaterialIdCriteria = null;

	/**
	 * Collection to store aggregation of collMaterialFiles.
	 * @var        array
	 */
	protected $collMaterialFiles;

	/**
	 * The criteria used to select the current contents of collMaterialFiles.
	 * @var        Criteria
	 */
	protected $lastMaterialFileCriteria = null;

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
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
	}

	/**
	 * Get the [expid] column value.
	 * 
	 * @return     double
	 */
	public function getExperimentId()
	{

		return $this->expid;
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
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{

		return $this->name;
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
	 * Set the value of [id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = MaterialPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDescription($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->description) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->description !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->description = $obj;
			$this->modifiedColumns[] = MaterialPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [expid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setExperimentId($v)
	{

		if ($this->expid !== $v) {
			$this->expid = $v;
			$this->modifiedColumns[] = MaterialPeer::EXPID;
		}

		if ($this->aExperiment !== null && $this->aExperiment->getId() !== $v) {
			$this->aExperiment = null;
		}

	} // setExperimentId()

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
			$this->modifiedColumns[] = MaterialPeer::MATERIAL_TYPE_ID;
		}

		if ($this->aMaterialType !== null && $this->aMaterialType->getId() !== $v) {
			$this->aMaterialType = null;
		}

	} // setMaterialTypeId()

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
			$this->modifiedColumns[] = MaterialPeer::NAME;
		}

	} // setName()

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
			$this->modifiedColumns[] = MaterialPeer::PROTOTYPE_MATERIAL_ID;
		}

		if ($this->aMaterialRelatedByPrototypeMaterialId !== null && $this->aMaterialRelatedByPrototypeMaterialId->getId() !== $v) {
			$this->aMaterialRelatedByPrototypeMaterialId = null;
		}

	} // setPrototypeMaterialId()

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

			$this->description = $rs->getClob($startcol + 1);

			$this->expid = $rs->getFloat($startcol + 2);

			$this->material_type_id = $rs->getFloat($startcol + 3);

			$this->name = $rs->getString($startcol + 4);

			$this->prototype_material_id = $rs->getFloat($startcol + 5);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = MaterialPeer::NUM_COLUMNS - MaterialPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Material object", $e);
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
			$con = Propel::getConnection(MaterialPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			MaterialPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(MaterialPeer::DATABASE_NAME);
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

			if ($this->aExperiment !== null) {
				if ($this->aExperiment->isModified()) {
					$affectedRows += $this->aExperiment->save($con);
				}
				$this->setExperiment($this->aExperiment);
			}

			if ($this->aMaterialRelatedByPrototypeMaterialId !== null) {
				if ($this->aMaterialRelatedByPrototypeMaterialId->isModified()) {
					$affectedRows += $this->aMaterialRelatedByPrototypeMaterialId->save($con);
				}
				$this->setMaterialRelatedByPrototypeMaterialId($this->aMaterialRelatedByPrototypeMaterialId);
			}

			if ($this->aMaterialType !== null) {
				if ($this->aMaterialType->isModified()) {
					$affectedRows += $this->aMaterialType->save($con);
				}
				$this->setMaterialType($this->aMaterialType);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = MaterialPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += MaterialPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collMaterialsRelatedByPrototypeMaterialId !== null) {
				foreach($this->collMaterialsRelatedByPrototypeMaterialId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collMaterialFiles !== null) {
				foreach($this->collMaterialFiles as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collMaterialPropertys !== null) {
				foreach($this->collMaterialPropertys as $referrerFK) {
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

			if ($this->aExperiment !== null) {
				if (!$this->aExperiment->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aExperiment->getValidationFailures());
				}
			}

			if ($this->aMaterialRelatedByPrototypeMaterialId !== null) {
				if (!$this->aMaterialRelatedByPrototypeMaterialId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMaterialRelatedByPrototypeMaterialId->getValidationFailures());
				}
			}

			if ($this->aMaterialType !== null) {
				if (!$this->aMaterialType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMaterialType->getValidationFailures());
				}
			}


			if (($retval = MaterialPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collMaterialFiles !== null) {
					foreach($this->collMaterialFiles as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collMaterialPropertys !== null) {
					foreach($this->collMaterialPropertys as $referrerFK) {
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
		$pos = MaterialPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDescription();
				break;
			case 2:
				return $this->getExperimentId();
				break;
			case 3:
				return $this->getMaterialTypeId();
				break;
			case 4:
				return $this->getName();
				break;
			case 5:
				return $this->getPrototypeMaterialId();
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
		$keys = MaterialPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDescription(),
			$keys[2] => $this->getExperimentId(),
			$keys[3] => $this->getMaterialTypeId(),
			$keys[4] => $this->getName(),
			$keys[5] => $this->getPrototypeMaterialId(),
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
		$pos = MaterialPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDescription($value);
				break;
			case 2:
				$this->setExperimentId($value);
				break;
			case 3:
				$this->setMaterialTypeId($value);
				break;
			case 4:
				$this->setName($value);
				break;
			case 5:
				$this->setPrototypeMaterialId($value);
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
		$keys = MaterialPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDescription($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setExperimentId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setMaterialTypeId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setPrototypeMaterialId($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(MaterialPeer::DATABASE_NAME);

		if ($this->isColumnModified(MaterialPeer::ID)) $criteria->add(MaterialPeer::ID, $this->id);
		if ($this->isColumnModified(MaterialPeer::DESCRIPTION)) $criteria->add(MaterialPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(MaterialPeer::EXPID)) $criteria->add(MaterialPeer::EXPID, $this->expid);
		if ($this->isColumnModified(MaterialPeer::MATERIAL_TYPE_ID)) $criteria->add(MaterialPeer::MATERIAL_TYPE_ID, $this->material_type_id);
		if ($this->isColumnModified(MaterialPeer::NAME)) $criteria->add(MaterialPeer::NAME, $this->name);
		if ($this->isColumnModified(MaterialPeer::PROTOTYPE_MATERIAL_ID)) $criteria->add(MaterialPeer::PROTOTYPE_MATERIAL_ID, $this->prototype_material_id);

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
		$criteria = new Criteria(MaterialPeer::DATABASE_NAME);

		$criteria->add(MaterialPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of Material (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDescription($this->description);

		$copyObj->setExperimentId($this->expid);

		$copyObj->setMaterialTypeId($this->material_type_id);

		$copyObj->setName($this->name);

		$copyObj->setPrototypeMaterialId($this->prototype_material_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getMaterialsRelatedByPrototypeMaterialId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addMaterialRelatedByPrototypeMaterialId($relObj->copy($deepCopy));
			}

			foreach($this->getMaterialFiles() as $relObj) {
				$copyObj->addMaterialFile($relObj->copy($deepCopy));
			}

			foreach($this->getMaterialPropertys() as $relObj) {
				$copyObj->addMaterialProperty($relObj->copy($deepCopy));
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
	 * @return     Material Clone of current object.
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
	 * @return     MaterialPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new MaterialPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Experiment object.
	 *
	 * @param      Experiment $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setExperiment($v)
	{


		if ($v === null) {
			$this->setExperimentId(NULL);
		} else {
			$this->setExperimentId($v->getId());
		}


		$this->aExperiment = $v;
	}


	/**
	 * Get the associated Experiment object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Experiment The associated Experiment object.
	 * @throws     PropelException
	 */
	public function getExperiment($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseExperimentPeer.php';

		if ($this->aExperiment === null && ($this->expid > 0)) {

			$this->aExperiment = ExperimentPeer::retrieveByPK($this->expid, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ExperimentPeer::retrieveByPK($this->expid, $con);
			   $obj->addExperiments($this);
			 */
		}
		return $this->aExperiment;
	}

	/**
	 * Declares an association between this object and a Material object.
	 *
	 * @param      Material $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMaterialRelatedByPrototypeMaterialId($v)
	{


		if ($v === null) {
			$this->setPrototypeMaterialId(NULL);
		} else {
			$this->setPrototypeMaterialId($v->getId());
		}


		$this->aMaterialRelatedByPrototypeMaterialId = $v;
	}


	/**
	 * Get the associated Material object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Material The associated Material object.
	 * @throws     PropelException
	 */
	public function getMaterialRelatedByPrototypeMaterialId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMaterialPeer.php';

		if ($this->aMaterialRelatedByPrototypeMaterialId === null && ($this->prototype_material_id > 0)) {

			$this->aMaterialRelatedByPrototypeMaterialId = MaterialPeer::retrieveByPK($this->prototype_material_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MaterialPeer::retrieveByPK($this->prototype_material_id, $con);
			   $obj->addMaterialsRelatedByPrototypeMaterialId($this);
			 */
		}
		return $this->aMaterialRelatedByPrototypeMaterialId;
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
	 * Temporary storage of collMaterialsRelatedByPrototypeMaterialId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initMaterialsRelatedByPrototypeMaterialId()
	{
		if ($this->collMaterialsRelatedByPrototypeMaterialId === null) {
			$this->collMaterialsRelatedByPrototypeMaterialId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Material has previously
	 * been saved, it will retrieve related MaterialsRelatedByPrototypeMaterialId from storage.
	 * If this Material is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getMaterialsRelatedByPrototypeMaterialId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialsRelatedByPrototypeMaterialId === null) {
			if ($this->isNew()) {
			   $this->collMaterialsRelatedByPrototypeMaterialId = array();
			} else {

				$criteria->add(MaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

				MaterialPeer::addSelectColumns($criteria);
				$this->collMaterialsRelatedByPrototypeMaterialId = MaterialPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

				MaterialPeer::addSelectColumns($criteria);
				if (!isset($this->lastMaterialRelatedByPrototypeMaterialIdCriteria) || !$this->lastMaterialRelatedByPrototypeMaterialIdCriteria->equals($criteria)) {
					$this->collMaterialsRelatedByPrototypeMaterialId = MaterialPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMaterialRelatedByPrototypeMaterialIdCriteria = $criteria;
		return $this->collMaterialsRelatedByPrototypeMaterialId;
	}

	/**
	 * Returns the number of related MaterialsRelatedByPrototypeMaterialId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countMaterialsRelatedByPrototypeMaterialId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(MaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

		return MaterialPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Material object to this object
	 * through the Material foreign key attribute
	 *
	 * @param      Material $l Material
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMaterialRelatedByPrototypeMaterialId(Material $l)
	{
		$this->collMaterialsRelatedByPrototypeMaterialId[] = $l;
		$l->setMaterialRelatedByPrototypeMaterialId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Material is new, it will return
	 * an empty collection; or if this Material has previously
	 * been saved, it will retrieve related MaterialsRelatedByPrototypeMaterialId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Material.
	 */
	public function getMaterialsRelatedByPrototypeMaterialIdJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialsRelatedByPrototypeMaterialId === null) {
			if ($this->isNew()) {
				$this->collMaterialsRelatedByPrototypeMaterialId = array();
			} else {

				$criteria->add(MaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

				$this->collMaterialsRelatedByPrototypeMaterialId = MaterialPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

			if (!isset($this->lastMaterialRelatedByPrototypeMaterialIdCriteria) || !$this->lastMaterialRelatedByPrototypeMaterialIdCriteria->equals($criteria)) {
				$this->collMaterialsRelatedByPrototypeMaterialId = MaterialPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastMaterialRelatedByPrototypeMaterialIdCriteria = $criteria;

		return $this->collMaterialsRelatedByPrototypeMaterialId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Material is new, it will return
	 * an empty collection; or if this Material has previously
	 * been saved, it will retrieve related MaterialsRelatedByPrototypeMaterialId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Material.
	 */
	public function getMaterialsRelatedByPrototypeMaterialIdJoinMaterialType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialsRelatedByPrototypeMaterialId === null) {
			if ($this->isNew()) {
				$this->collMaterialsRelatedByPrototypeMaterialId = array();
			} else {

				$criteria->add(MaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

				$this->collMaterialsRelatedByPrototypeMaterialId = MaterialPeer::doSelectJoinMaterialType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialPeer::PROTOTYPE_MATERIAL_ID, $this->getId());

			if (!isset($this->lastMaterialRelatedByPrototypeMaterialIdCriteria) || !$this->lastMaterialRelatedByPrototypeMaterialIdCriteria->equals($criteria)) {
				$this->collMaterialsRelatedByPrototypeMaterialId = MaterialPeer::doSelectJoinMaterialType($criteria, $con);
			}
		}
		$this->lastMaterialRelatedByPrototypeMaterialIdCriteria = $criteria;

		return $this->collMaterialsRelatedByPrototypeMaterialId;
	}

	/**
	 * Temporary storage of collMaterialFiles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initMaterialFiles()
	{
		if ($this->collMaterialFiles === null) {
			$this->collMaterialFiles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Material has previously
	 * been saved, it will retrieve related MaterialFiles from storage.
	 * If this Material is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getMaterialFiles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialFiles === null) {
			if ($this->isNew()) {
			   $this->collMaterialFiles = array();
			} else {

				$criteria->add(MaterialFilePeer::MATERIAL_ID, $this->getId());

				MaterialFilePeer::addSelectColumns($criteria);
				$this->collMaterialFiles = MaterialFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MaterialFilePeer::MATERIAL_ID, $this->getId());

				MaterialFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastMaterialFileCriteria) || !$this->lastMaterialFileCriteria->equals($criteria)) {
					$this->collMaterialFiles = MaterialFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMaterialFileCriteria = $criteria;
		return $this->collMaterialFiles;
	}

	/**
	 * Returns the number of related MaterialFiles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countMaterialFiles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(MaterialFilePeer::MATERIAL_ID, $this->getId());

		return MaterialFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a MaterialFile object to this object
	 * through the MaterialFile foreign key attribute
	 *
	 * @param      MaterialFile $l MaterialFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMaterialFile(MaterialFile $l)
	{
		$this->collMaterialFiles[] = $l;
		$l->setMaterial($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Material is new, it will return
	 * an empty collection; or if this Material has previously
	 * been saved, it will retrieve related MaterialFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Material.
	 */
	public function getMaterialFilesJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialFiles === null) {
			if ($this->isNew()) {
				$this->collMaterialFiles = array();
			} else {

				$criteria->add(MaterialFilePeer::MATERIAL_ID, $this->getId());

				$this->collMaterialFiles = MaterialFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialFilePeer::MATERIAL_ID, $this->getId());

			if (!isset($this->lastMaterialFileCriteria) || !$this->lastMaterialFileCriteria->equals($criteria)) {
				$this->collMaterialFiles = MaterialFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastMaterialFileCriteria = $criteria;

		return $this->collMaterialFiles;
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
	 * Otherwise if this Material has previously
	 * been saved, it will retrieve related MaterialPropertys from storage.
	 * If this Material is new, it will return
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

				$criteria->add(MaterialPropertyPeer::MATERIAL_ID, $this->getId());

				MaterialPropertyPeer::addSelectColumns($criteria);
				$this->collMaterialPropertys = MaterialPropertyPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MaterialPropertyPeer::MATERIAL_ID, $this->getId());

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

		$criteria->add(MaterialPropertyPeer::MATERIAL_ID, $this->getId());

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
		$l->setMaterial($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Material is new, it will return
	 * an empty collection; or if this Material has previously
	 * been saved, it will retrieve related MaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Material.
	 */
	public function getMaterialPropertysJoinMaterialTypeProperty($criteria = null, $con = null)
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

				$criteria->add(MaterialPropertyPeer::MATERIAL_ID, $this->getId());

				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMaterialTypeProperty($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialPropertyPeer::MATERIAL_ID, $this->getId());

			if (!isset($this->lastMaterialPropertyCriteria) || !$this->lastMaterialPropertyCriteria->equals($criteria)) {
				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMaterialTypeProperty($criteria, $con);
			}
		}
		$this->lastMaterialPropertyCriteria = $criteria;

		return $this->collMaterialPropertys;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Material is new, it will return
	 * an empty collection; or if this Material has previously
	 * been saved, it will retrieve related MaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Material.
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

				$criteria->add(MaterialPropertyPeer::MATERIAL_ID, $this->getId());

				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialPropertyPeer::MATERIAL_ID, $this->getId());

			if (!isset($this->lastMaterialPropertyCriteria) || !$this->lastMaterialPropertyCriteria->equals($criteria)) {
				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastMaterialPropertyCriteria = $criteria;

		return $this->collMaterialPropertys;
	}

} // BaseMaterial
