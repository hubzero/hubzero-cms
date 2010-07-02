<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SpecimenComponentPeer.php';

/**
 * Base class that represents a row from the 'SPECIMEN_COMPONENT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSpecimenComponent extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SpecimenComponentPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the title field.
	 * @var        string
	 */
	protected $title;


	/**
	 * The value for the specimen_id field.
	 * @var        double
	 */
	protected $specimen_id;


	/**
	 * The value for the parent_spec_comp_id field.
	 * @var        double
	 */
	protected $parent_spec_comp_id;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * @var        Specimen
	 */
	protected $aSpecimen;

	/**
	 * @var        SpecimenComponent
	 */
	protected $aSpecimenComponentRelatedByParentId;

	/**
	 * Collection to store aggregation of collSpecimenComponentsRelatedByParentId.
	 * @var        array
	 */
	protected $collSpecimenComponentsRelatedByParentId;

	/**
	 * The criteria used to select the current contents of collSpecimenComponentsRelatedByParentId.
	 * @var        Criteria
	 */
	protected $lastSpecimenComponentRelatedByParentIdCriteria = null;

	/**
	 * Collection to store aggregation of collSpecimenComponentAttributes.
	 * @var        array
	 */
	protected $collSpecimenComponentAttributes;

	/**
	 * The criteria used to select the current contents of collSpecimenComponentAttributes.
	 * @var        Criteria
	 */
	protected $lastSpecimenComponentAttributeCriteria = null;

	/**
	 * Collection to store aggregation of collSpecimenComponentMaterials.
	 * @var        array
	 */
	protected $collSpecimenComponentMaterials;

	/**
	 * The criteria used to select the current contents of collSpecimenComponentMaterials.
	 * @var        Criteria
	 */
	protected $lastSpecimenComponentMaterialCriteria = null;

	/**
	 * Collection to store aggregation of collSpecimenComponentExperiments.
	 * @var        array
	 */
	protected $collSpecimenComponentExperiments;

	/**
	 * The criteria used to select the current contents of collSpecimenComponentExperiments.
	 * @var        Criteria
	 */
	protected $lastSpecimenComponentExperimentCriteria = null;

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
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{

		return $this->name;
	}

	/**
	 * Get the [title] column value.
	 * 
	 * @return     string
	 */
	public function getTitle()
	{

		return $this->title;
	}

	/**
	 * Get the [specimen_id] column value.
	 * 
	 * @return     double
	 */
	public function getSpecimenId()
	{

		return $this->specimen_id;
	}

	/**
	 * Get the [parent_spec_comp_id] column value.
	 * 
	 * @return     double
	 */
	public function getParentId()
	{

		return $this->parent_spec_comp_id;
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
			$this->modifiedColumns[] = SpecimenComponentPeer::ID;
		}

	} // setId()

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
			$this->modifiedColumns[] = SpecimenComponentPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [title] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTitle($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->title !== $v) {
			$this->title = $v;
			$this->modifiedColumns[] = SpecimenComponentPeer::TITLE;
		}

	} // setTitle()

	/**
	 * Set the value of [specimen_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSpecimenId($v)
	{

		if ($this->specimen_id !== $v) {
			$this->specimen_id = $v;
			$this->modifiedColumns[] = SpecimenComponentPeer::SPECIMEN_ID;
		}

		if ($this->aSpecimen !== null && $this->aSpecimen->getId() !== $v) {
			$this->aSpecimen = null;
		}

	} // setSpecimenId()

	/**
	 * Set the value of [parent_spec_comp_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setParentId($v)
	{

		if ($this->parent_spec_comp_id !== $v) {
			$this->parent_spec_comp_id = $v;
			$this->modifiedColumns[] = SpecimenComponentPeer::PARENT_SPEC_COMP_ID;
		}

		if ($this->aSpecimenComponentRelatedByParentId !== null && $this->aSpecimenComponentRelatedByParentId->getId() !== $v) {
			$this->aSpecimenComponentRelatedByParentId = null;
		}

	} // setParentId()

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
			$this->modifiedColumns[] = SpecimenComponentPeer::DESCRIPTION;
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

			$this->name = $rs->getString($startcol + 1);

			$this->title = $rs->getString($startcol + 2);

			$this->specimen_id = $rs->getFloat($startcol + 3);

			$this->parent_spec_comp_id = $rs->getFloat($startcol + 4);

			$this->description = $rs->getString($startcol + 5);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = SpecimenComponentPeer::NUM_COLUMNS - SpecimenComponentPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SpecimenComponent object", $e);
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
			$con = Propel::getConnection(SpecimenComponentPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SpecimenComponentPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SpecimenComponentPeer::DATABASE_NAME);
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

			if ($this->aSpecimen !== null) {
				if ($this->aSpecimen->isModified()) {
					$affectedRows += $this->aSpecimen->save($con);
				}
				$this->setSpecimen($this->aSpecimen);
			}

			if ($this->aSpecimenComponentRelatedByParentId !== null) {
				if ($this->aSpecimenComponentRelatedByParentId->isModified()) {
					$affectedRows += $this->aSpecimenComponentRelatedByParentId->save($con);
				}
				$this->setSpecimenComponentRelatedByParentId($this->aSpecimenComponentRelatedByParentId);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SpecimenComponentPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SpecimenComponentPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collSpecimenComponentsRelatedByParentId !== null) {
				foreach($this->collSpecimenComponentsRelatedByParentId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSpecimenComponentAttributes !== null) {
				foreach($this->collSpecimenComponentAttributes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSpecimenComponentMaterials !== null) {
				foreach($this->collSpecimenComponentMaterials as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSpecimenComponentExperiments !== null) {
				foreach($this->collSpecimenComponentExperiments as $referrerFK) {
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

			if ($this->aSpecimen !== null) {
				if (!$this->aSpecimen->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSpecimen->getValidationFailures());
				}
			}

			if ($this->aSpecimenComponentRelatedByParentId !== null) {
				if (!$this->aSpecimenComponentRelatedByParentId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSpecimenComponentRelatedByParentId->getValidationFailures());
				}
			}


			if (($retval = SpecimenComponentPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collSpecimenComponentAttributes !== null) {
					foreach($this->collSpecimenComponentAttributes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSpecimenComponentMaterials !== null) {
					foreach($this->collSpecimenComponentMaterials as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSpecimenComponentExperiments !== null) {
					foreach($this->collSpecimenComponentExperiments as $referrerFK) {
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
		$pos = SpecimenComponentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getName();
				break;
			case 2:
				return $this->getTitle();
				break;
			case 3:
				return $this->getSpecimenId();
				break;
			case 4:
				return $this->getParentId();
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
		$keys = SpecimenComponentPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getName(),
			$keys[2] => $this->getTitle(),
			$keys[3] => $this->getSpecimenId(),
			$keys[4] => $this->getParentId(),
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
		$pos = SpecimenComponentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setName($value);
				break;
			case 2:
				$this->setTitle($value);
				break;
			case 3:
				$this->setSpecimenId($value);
				break;
			case 4:
				$this->setParentId($value);
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
		$keys = SpecimenComponentPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setTitle($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setSpecimenId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setParentId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDescription($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SpecimenComponentPeer::DATABASE_NAME);

		if ($this->isColumnModified(SpecimenComponentPeer::ID)) $criteria->add(SpecimenComponentPeer::ID, $this->id);
		if ($this->isColumnModified(SpecimenComponentPeer::NAME)) $criteria->add(SpecimenComponentPeer::NAME, $this->name);
		if ($this->isColumnModified(SpecimenComponentPeer::TITLE)) $criteria->add(SpecimenComponentPeer::TITLE, $this->title);
		if ($this->isColumnModified(SpecimenComponentPeer::SPECIMEN_ID)) $criteria->add(SpecimenComponentPeer::SPECIMEN_ID, $this->specimen_id);
		if ($this->isColumnModified(SpecimenComponentPeer::PARENT_SPEC_COMP_ID)) $criteria->add(SpecimenComponentPeer::PARENT_SPEC_COMP_ID, $this->parent_spec_comp_id);
		if ($this->isColumnModified(SpecimenComponentPeer::DESCRIPTION)) $criteria->add(SpecimenComponentPeer::DESCRIPTION, $this->description);

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
		$criteria = new Criteria(SpecimenComponentPeer::DATABASE_NAME);

		$criteria->add(SpecimenComponentPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SpecimenComponent (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setName($this->name);

		$copyObj->setTitle($this->title);

		$copyObj->setSpecimenId($this->specimen_id);

		$copyObj->setParentId($this->parent_spec_comp_id);

		$copyObj->setDescription($this->description);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getSpecimenComponentsRelatedByParentId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addSpecimenComponentRelatedByParentId($relObj->copy($deepCopy));
			}

			foreach($this->getSpecimenComponentAttributes() as $relObj) {
				$copyObj->addSpecimenComponentAttribute($relObj->copy($deepCopy));
			}

			foreach($this->getSpecimenComponentMaterials() as $relObj) {
				$copyObj->addSpecimenComponentMaterial($relObj->copy($deepCopy));
			}

			foreach($this->getSpecimenComponentExperiments() as $relObj) {
				$copyObj->addSpecimenComponentExperiment($relObj->copy($deepCopy));
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
	 * @return     SpecimenComponent Clone of current object.
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
	 * @return     SpecimenComponentPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SpecimenComponentPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Specimen object.
	 *
	 * @param      Specimen $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSpecimen($v)
	{


		if ($v === null) {
			$this->setSpecimenId(NULL);
		} else {
			$this->setSpecimenId($v->getId());
		}


		$this->aSpecimen = $v;
	}


	/**
	 * Get the associated Specimen object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Specimen The associated Specimen object.
	 * @throws     PropelException
	 */
	public function getSpecimen($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSpecimenPeer.php';

		if ($this->aSpecimen === null && ($this->specimen_id > 0)) {

			$this->aSpecimen = SpecimenPeer::retrieveByPK($this->specimen_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SpecimenPeer::retrieveByPK($this->specimen_id, $con);
			   $obj->addSpecimens($this);
			 */
		}
		return $this->aSpecimen;
	}

	/**
	 * Declares an association between this object and a SpecimenComponent object.
	 *
	 * @param      SpecimenComponent $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSpecimenComponentRelatedByParentId($v)
	{


		if ($v === null) {
			$this->setParentId(NULL);
		} else {
			$this->setParentId($v->getId());
		}


		$this->aSpecimenComponentRelatedByParentId = $v;
	}


	/**
	 * Get the associated SpecimenComponent object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SpecimenComponent The associated SpecimenComponent object.
	 * @throws     PropelException
	 */
	public function getSpecimenComponentRelatedByParentId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSpecimenComponentPeer.php';

		if ($this->aSpecimenComponentRelatedByParentId === null && ($this->parent_spec_comp_id > 0)) {

			$this->aSpecimenComponentRelatedByParentId = SpecimenComponentPeer::retrieveByPK($this->parent_spec_comp_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SpecimenComponentPeer::retrieveByPK($this->parent_spec_comp_id, $con);
			   $obj->addSpecimenComponentsRelatedByParentId($this);
			 */
		}
		return $this->aSpecimenComponentRelatedByParentId;
	}

	/**
	 * Temporary storage of collSpecimenComponentsRelatedByParentId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSpecimenComponentsRelatedByParentId()
	{
		if ($this->collSpecimenComponentsRelatedByParentId === null) {
			$this->collSpecimenComponentsRelatedByParentId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponent has previously
	 * been saved, it will retrieve related SpecimenComponentsRelatedByParentId from storage.
	 * If this SpecimenComponent is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSpecimenComponentsRelatedByParentId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentsRelatedByParentId === null) {
			if ($this->isNew()) {
			   $this->collSpecimenComponentsRelatedByParentId = array();
			} else {

				$criteria->add(SpecimenComponentPeer::PARENT_SPEC_COMP_ID, $this->getId());

				SpecimenComponentPeer::addSelectColumns($criteria);
				$this->collSpecimenComponentsRelatedByParentId = SpecimenComponentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentPeer::PARENT_SPEC_COMP_ID, $this->getId());

				SpecimenComponentPeer::addSelectColumns($criteria);
				if (!isset($this->lastSpecimenComponentRelatedByParentIdCriteria) || !$this->lastSpecimenComponentRelatedByParentIdCriteria->equals($criteria)) {
					$this->collSpecimenComponentsRelatedByParentId = SpecimenComponentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSpecimenComponentRelatedByParentIdCriteria = $criteria;
		return $this->collSpecimenComponentsRelatedByParentId;
	}

	/**
	 * Returns the number of related SpecimenComponentsRelatedByParentId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSpecimenComponentsRelatedByParentId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SpecimenComponentPeer::PARENT_SPEC_COMP_ID, $this->getId());

		return SpecimenComponentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SpecimenComponent object to this object
	 * through the SpecimenComponent foreign key attribute
	 *
	 * @param      SpecimenComponent $l SpecimenComponent
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSpecimenComponentRelatedByParentId(SpecimenComponent $l)
	{
		$this->collSpecimenComponentsRelatedByParentId[] = $l;
		$l->setSpecimenComponentRelatedByParentId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponent is new, it will return
	 * an empty collection; or if this SpecimenComponent has previously
	 * been saved, it will retrieve related SpecimenComponentsRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SpecimenComponent.
	 */
	public function getSpecimenComponentsRelatedByParentIdJoinSpecimen($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentsRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentsRelatedByParentId = array();
			} else {

				$criteria->add(SpecimenComponentPeer::PARENT_SPEC_COMP_ID, $this->getId());

				$this->collSpecimenComponentsRelatedByParentId = SpecimenComponentPeer::doSelectJoinSpecimen($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentPeer::PARENT_SPEC_COMP_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentRelatedByParentIdCriteria) || !$this->lastSpecimenComponentRelatedByParentIdCriteria->equals($criteria)) {
				$this->collSpecimenComponentsRelatedByParentId = SpecimenComponentPeer::doSelectJoinSpecimen($criteria, $con);
			}
		}
		$this->lastSpecimenComponentRelatedByParentIdCriteria = $criteria;

		return $this->collSpecimenComponentsRelatedByParentId;
	}

	/**
	 * Temporary storage of collSpecimenComponentAttributes to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSpecimenComponentAttributes()
	{
		if ($this->collSpecimenComponentAttributes === null) {
			$this->collSpecimenComponentAttributes = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponent has previously
	 * been saved, it will retrieve related SpecimenComponentAttributes from storage.
	 * If this SpecimenComponent is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSpecimenComponentAttributes($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentAttributes === null) {
			if ($this->isNew()) {
			   $this->collSpecimenComponentAttributes = array();
			} else {

				$criteria->add(SpecimenComponentAttributePeer::SPEC_COMP_ID, $this->getId());

				SpecimenComponentAttributePeer::addSelectColumns($criteria);
				$this->collSpecimenComponentAttributes = SpecimenComponentAttributePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentAttributePeer::SPEC_COMP_ID, $this->getId());

				SpecimenComponentAttributePeer::addSelectColumns($criteria);
				if (!isset($this->lastSpecimenComponentAttributeCriteria) || !$this->lastSpecimenComponentAttributeCriteria->equals($criteria)) {
					$this->collSpecimenComponentAttributes = SpecimenComponentAttributePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSpecimenComponentAttributeCriteria = $criteria;
		return $this->collSpecimenComponentAttributes;
	}

	/**
	 * Returns the number of related SpecimenComponentAttributes.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSpecimenComponentAttributes($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SpecimenComponentAttributePeer::SPEC_COMP_ID, $this->getId());

		return SpecimenComponentAttributePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SpecimenComponentAttribute object to this object
	 * through the SpecimenComponentAttribute foreign key attribute
	 *
	 * @param      SpecimenComponentAttribute $l SpecimenComponentAttribute
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSpecimenComponentAttribute(SpecimenComponentAttribute $l)
	{
		$this->collSpecimenComponentAttributes[] = $l;
		$l->setSpecimenComponent($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponent is new, it will return
	 * an empty collection; or if this SpecimenComponent has previously
	 * been saved, it will retrieve related SpecimenComponentAttributes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SpecimenComponent.
	 */
	public function getSpecimenComponentAttributesJoinMeasurementUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentAttributePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentAttributes === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentAttributes = array();
			} else {

				$criteria->add(SpecimenComponentAttributePeer::SPEC_COMP_ID, $this->getId());

				$this->collSpecimenComponentAttributes = SpecimenComponentAttributePeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentAttributePeer::SPEC_COMP_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentAttributeCriteria) || !$this->lastSpecimenComponentAttributeCriteria->equals($criteria)) {
				$this->collSpecimenComponentAttributes = SpecimenComponentAttributePeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastSpecimenComponentAttributeCriteria = $criteria;

		return $this->collSpecimenComponentAttributes;
	}

	/**
	 * Temporary storage of collSpecimenComponentMaterials to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSpecimenComponentMaterials()
	{
		if ($this->collSpecimenComponentMaterials === null) {
			$this->collSpecimenComponentMaterials = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponent has previously
	 * been saved, it will retrieve related SpecimenComponentMaterials from storage.
	 * If this SpecimenComponent is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSpecimenComponentMaterials($criteria = null, $con = null)
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

		if ($this->collSpecimenComponentMaterials === null) {
			if ($this->isNew()) {
			   $this->collSpecimenComponentMaterials = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID, $this->getId());

				SpecimenComponentMaterialPeer::addSelectColumns($criteria);
				$this->collSpecimenComponentMaterials = SpecimenComponentMaterialPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID, $this->getId());

				SpecimenComponentMaterialPeer::addSelectColumns($criteria);
				if (!isset($this->lastSpecimenComponentMaterialCriteria) || !$this->lastSpecimenComponentMaterialCriteria->equals($criteria)) {
					$this->collSpecimenComponentMaterials = SpecimenComponentMaterialPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSpecimenComponentMaterialCriteria = $criteria;
		return $this->collSpecimenComponentMaterials;
	}

	/**
	 * Returns the number of related SpecimenComponentMaterials.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSpecimenComponentMaterials($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID, $this->getId());

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
	public function addSpecimenComponentMaterial(SpecimenComponentMaterial $l)
	{
		$this->collSpecimenComponentMaterials[] = $l;
		$l->setSpecimenComponent($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponent is new, it will return
	 * an empty collection; or if this SpecimenComponent has previously
	 * been saved, it will retrieve related SpecimenComponentMaterials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SpecimenComponent.
	 */
	public function getSpecimenComponentMaterialsJoinMaterialType($criteria = null, $con = null)
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

		if ($this->collSpecimenComponentMaterials === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentMaterials = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID, $this->getId());

				$this->collSpecimenComponentMaterials = SpecimenComponentMaterialPeer::doSelectJoinMaterialType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialCriteria) || !$this->lastSpecimenComponentMaterialCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterials = SpecimenComponentMaterialPeer::doSelectJoinMaterialType($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialCriteria = $criteria;

		return $this->collSpecimenComponentMaterials;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponent is new, it will return
	 * an empty collection; or if this SpecimenComponent has previously
	 * been saved, it will retrieve related SpecimenComponentMaterials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SpecimenComponent.
	 */
	public function getSpecimenComponentMaterialsJoinSpecimenComponentMaterialRelatedByPrototypeMaterialId($criteria = null, $con = null)
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

		if ($this->collSpecimenComponentMaterials === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentMaterials = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID, $this->getId());

				$this->collSpecimenComponentMaterials = SpecimenComponentMaterialPeer::doSelectJoinSpecimenComponentMaterialRelatedByPrototypeMaterialId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialCriteria) || !$this->lastSpecimenComponentMaterialCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterials = SpecimenComponentMaterialPeer::doSelectJoinSpecimenComponentMaterialRelatedByPrototypeMaterialId($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialCriteria = $criteria;

		return $this->collSpecimenComponentMaterials;
	}

	/**
	 * Temporary storage of collSpecimenComponentExperiments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSpecimenComponentExperiments()
	{
		if ($this->collSpecimenComponentExperiments === null) {
			$this->collSpecimenComponentExperiments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponent has previously
	 * been saved, it will retrieve related SpecimenComponentExperiments from storage.
	 * If this SpecimenComponent is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSpecimenComponentExperiments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentExperiments === null) {
			if ($this->isNew()) {
			   $this->collSpecimenComponentExperiments = array();
			} else {

				$criteria->add(SpecimenComponentExperimentPeer::SPECIMEN_COMPONENT_ID, $this->getId());

				SpecimenComponentExperimentPeer::addSelectColumns($criteria);
				$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentExperimentPeer::SPECIMEN_COMPONENT_ID, $this->getId());

				SpecimenComponentExperimentPeer::addSelectColumns($criteria);
				if (!isset($this->lastSpecimenComponentExperimentCriteria) || !$this->lastSpecimenComponentExperimentCriteria->equals($criteria)) {
					$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSpecimenComponentExperimentCriteria = $criteria;
		return $this->collSpecimenComponentExperiments;
	}

	/**
	 * Returns the number of related SpecimenComponentExperiments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSpecimenComponentExperiments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SpecimenComponentExperimentPeer::SPECIMEN_COMPONENT_ID, $this->getId());

		return SpecimenComponentExperimentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SpecimenComponentExperiment object to this object
	 * through the SpecimenComponentExperiment foreign key attribute
	 *
	 * @param      SpecimenComponentExperiment $l SpecimenComponentExperiment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSpecimenComponentExperiment(SpecimenComponentExperiment $l)
	{
		$this->collSpecimenComponentExperiments[] = $l;
		$l->setSpecimenComponent($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponent is new, it will return
	 * an empty collection; or if this SpecimenComponent has previously
	 * been saved, it will retrieve related SpecimenComponentExperiments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SpecimenComponent.
	 */
	public function getSpecimenComponentExperimentsJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentExperiments === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentExperiments = array();
			} else {

				$criteria->add(SpecimenComponentExperimentPeer::SPECIMEN_COMPONENT_ID, $this->getId());

				$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentExperimentPeer::SPECIMEN_COMPONENT_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentExperimentCriteria) || !$this->lastSpecimenComponentExperimentCriteria->equals($criteria)) {
				$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastSpecimenComponentExperimentCriteria = $criteria;

		return $this->collSpecimenComponentExperiments;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SpecimenComponent is new, it will return
	 * an empty collection; or if this SpecimenComponent has previously
	 * been saved, it will retrieve related SpecimenComponentExperiments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SpecimenComponent.
	 */
	public function getSpecimenComponentExperimentsJoinCoordinatorRunExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentExperiments === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentExperiments = array();
			} else {

				$criteria->add(SpecimenComponentExperimentPeer::SPECIMEN_COMPONENT_ID, $this->getId());

				$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelectJoinCoordinatorRunExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentExperimentPeer::SPECIMEN_COMPONENT_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentExperimentCriteria) || !$this->lastSpecimenComponentExperimentCriteria->equals($criteria)) {
				$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelectJoinCoordinatorRunExperiment($criteria, $con);
			}
		}
		$this->lastSpecimenComponentExperimentCriteria = $criteria;

		return $this->collSpecimenComponentExperiments;
	}

} // BaseSpecimenComponent
