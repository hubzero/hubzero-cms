<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/CoordinateSystemPeer.php';

/**
 * Base class that represents a row from the 'COORDINATE_SYSTEM' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseCoordinateSystem extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CoordinateSystemPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the dim1 field.
	 * @var        double
	 */
	protected $dim1;


	/**
	 * The value for the dim2 field.
	 * @var        double
	 */
	protected $dim2;


	/**
	 * The value for the dim3 field.
	 * @var        double
	 */
	protected $dim3;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * @var        CoordinateDimension
	 */
	protected $aCoordinateDimensionRelatedByDimension1;

	/**
	 * @var        CoordinateDimension
	 */
	protected $aCoordinateDimensionRelatedByDimension3;

	/**
	 * @var        CoordinateDimension
	 */
	protected $aCoordinateDimensionRelatedByDimension2;

	/**
	 * Collection to store aggregation of collCoordinateSpaces.
	 * @var        array
	 */
	protected $collCoordinateSpaces;

	/**
	 * The criteria used to select the current contents of collCoordinateSpaces.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceCriteria = null;

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
	 * Get the [dim1] column value.
	 * 
	 * @return     double
	 */
	public function getDimension1()
	{

		return $this->dim1;
	}

	/**
	 * Get the [dim2] column value.
	 * 
	 * @return     double
	 */
	public function getDimension2()
	{

		return $this->dim2;
	}

	/**
	 * Get the [dim3] column value.
	 * 
	 * @return     double
	 */
	public function getDimension3()
	{

		return $this->dim3;
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
	 * Set the value of [id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CoordinateSystemPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [dim1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDimension1($v)
	{

		if ($this->dim1 !== $v) {
			$this->dim1 = $v;
			$this->modifiedColumns[] = CoordinateSystemPeer::DIM1;
		}

		if ($this->aCoordinateDimensionRelatedByDimension1 !== null && $this->aCoordinateDimensionRelatedByDimension1->getId() !== $v) {
			$this->aCoordinateDimensionRelatedByDimension1 = null;
		}

	} // setDimension1()

	/**
	 * Set the value of [dim2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDimension2($v)
	{

		if ($this->dim2 !== $v) {
			$this->dim2 = $v;
			$this->modifiedColumns[] = CoordinateSystemPeer::DIM2;
		}

		if ($this->aCoordinateDimensionRelatedByDimension2 !== null && $this->aCoordinateDimensionRelatedByDimension2->getId() !== $v) {
			$this->aCoordinateDimensionRelatedByDimension2 = null;
		}

	} // setDimension2()

	/**
	 * Set the value of [dim3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDimension3($v)
	{

		if ($this->dim3 !== $v) {
			$this->dim3 = $v;
			$this->modifiedColumns[] = CoordinateSystemPeer::DIM3;
		}

		if ($this->aCoordinateDimensionRelatedByDimension3 !== null && $this->aCoordinateDimensionRelatedByDimension3->getId() !== $v) {
			$this->aCoordinateDimensionRelatedByDimension3 = null;
		}

	} // setDimension3()

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
			$this->modifiedColumns[] = CoordinateSystemPeer::NAME;
		}

	} // setName()

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

			$this->dim1 = $rs->getFloat($startcol + 1);

			$this->dim2 = $rs->getFloat($startcol + 2);

			$this->dim3 = $rs->getFloat($startcol + 3);

			$this->name = $rs->getString($startcol + 4);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 5; // 5 = CoordinateSystemPeer::NUM_COLUMNS - CoordinateSystemPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CoordinateSystem object", $e);
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
			$con = Propel::getConnection(CoordinateSystemPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			CoordinateSystemPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(CoordinateSystemPeer::DATABASE_NAME);
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

			if ($this->aCoordinateDimensionRelatedByDimension1 !== null) {
				if ($this->aCoordinateDimensionRelatedByDimension1->isModified()) {
					$affectedRows += $this->aCoordinateDimensionRelatedByDimension1->save($con);
				}
				$this->setCoordinateDimensionRelatedByDimension1($this->aCoordinateDimensionRelatedByDimension1);
			}

			if ($this->aCoordinateDimensionRelatedByDimension3 !== null) {
				if ($this->aCoordinateDimensionRelatedByDimension3->isModified()) {
					$affectedRows += $this->aCoordinateDimensionRelatedByDimension3->save($con);
				}
				$this->setCoordinateDimensionRelatedByDimension3($this->aCoordinateDimensionRelatedByDimension3);
			}

			if ($this->aCoordinateDimensionRelatedByDimension2 !== null) {
				if ($this->aCoordinateDimensionRelatedByDimension2->isModified()) {
					$affectedRows += $this->aCoordinateDimensionRelatedByDimension2->save($con);
				}
				$this->setCoordinateDimensionRelatedByDimension2($this->aCoordinateDimensionRelatedByDimension2);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = CoordinateSystemPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += CoordinateSystemPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCoordinateSpaces !== null) {
				foreach($this->collCoordinateSpaces as $referrerFK) {
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

			if ($this->aCoordinateDimensionRelatedByDimension1 !== null) {
				if (!$this->aCoordinateDimensionRelatedByDimension1->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCoordinateDimensionRelatedByDimension1->getValidationFailures());
				}
			}

			if ($this->aCoordinateDimensionRelatedByDimension3 !== null) {
				if (!$this->aCoordinateDimensionRelatedByDimension3->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCoordinateDimensionRelatedByDimension3->getValidationFailures());
				}
			}

			if ($this->aCoordinateDimensionRelatedByDimension2 !== null) {
				if (!$this->aCoordinateDimensionRelatedByDimension2->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCoordinateDimensionRelatedByDimension2->getValidationFailures());
				}
			}


			if (($retval = CoordinateSystemPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCoordinateSpaces !== null) {
					foreach($this->collCoordinateSpaces as $referrerFK) {
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
		$pos = CoordinateSystemPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDimension1();
				break;
			case 2:
				return $this->getDimension2();
				break;
			case 3:
				return $this->getDimension3();
				break;
			case 4:
				return $this->getName();
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
		$keys = CoordinateSystemPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDimension1(),
			$keys[2] => $this->getDimension2(),
			$keys[3] => $this->getDimension3(),
			$keys[4] => $this->getName(),
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
		$pos = CoordinateSystemPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDimension1($value);
				break;
			case 2:
				$this->setDimension2($value);
				break;
			case 3:
				$this->setDimension3($value);
				break;
			case 4:
				$this->setName($value);
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
		$keys = CoordinateSystemPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDimension1($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDimension2($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDimension3($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CoordinateSystemPeer::DATABASE_NAME);

		if ($this->isColumnModified(CoordinateSystemPeer::ID)) $criteria->add(CoordinateSystemPeer::ID, $this->id);
		if ($this->isColumnModified(CoordinateSystemPeer::DIM1)) $criteria->add(CoordinateSystemPeer::DIM1, $this->dim1);
		if ($this->isColumnModified(CoordinateSystemPeer::DIM2)) $criteria->add(CoordinateSystemPeer::DIM2, $this->dim2);
		if ($this->isColumnModified(CoordinateSystemPeer::DIM3)) $criteria->add(CoordinateSystemPeer::DIM3, $this->dim3);
		if ($this->isColumnModified(CoordinateSystemPeer::NAME)) $criteria->add(CoordinateSystemPeer::NAME, $this->name);

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
		$criteria = new Criteria(CoordinateSystemPeer::DATABASE_NAME);

		$criteria->add(CoordinateSystemPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of CoordinateSystem (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDimension1($this->dim1);

		$copyObj->setDimension2($this->dim2);

		$copyObj->setDimension3($this->dim3);

		$copyObj->setName($this->name);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getCoordinateSpaces() as $relObj) {
				$copyObj->addCoordinateSpace($relObj->copy($deepCopy));
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
	 * @return     CoordinateSystem Clone of current object.
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
	 * @return     CoordinateSystemPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CoordinateSystemPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CoordinateDimension object.
	 *
	 * @param      CoordinateDimension $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setCoordinateDimensionRelatedByDimension1($v)
	{


		if ($v === null) {
			$this->setDimension1(NULL);
		} else {
			$this->setDimension1($v->getId());
		}


		$this->aCoordinateDimensionRelatedByDimension1 = $v;
	}


	/**
	 * Get the associated CoordinateDimension object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     CoordinateDimension The associated CoordinateDimension object.
	 * @throws     PropelException
	 */
	public function getCoordinateDimensionRelatedByDimension1($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseCoordinateDimensionPeer.php';

		if ($this->aCoordinateDimensionRelatedByDimension1 === null && ($this->dim1 > 0)) {

			$this->aCoordinateDimensionRelatedByDimension1 = CoordinateDimensionPeer::retrieveByPK($this->dim1, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = CoordinateDimensionPeer::retrieveByPK($this->dim1, $con);
			   $obj->addCoordinateDimensionsRelatedByDimension1($this);
			 */
		}
		return $this->aCoordinateDimensionRelatedByDimension1;
	}

	/**
	 * Declares an association between this object and a CoordinateDimension object.
	 *
	 * @param      CoordinateDimension $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setCoordinateDimensionRelatedByDimension3($v)
	{


		if ($v === null) {
			$this->setDimension3(NULL);
		} else {
			$this->setDimension3($v->getId());
		}


		$this->aCoordinateDimensionRelatedByDimension3 = $v;
	}


	/**
	 * Get the associated CoordinateDimension object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     CoordinateDimension The associated CoordinateDimension object.
	 * @throws     PropelException
	 */
	public function getCoordinateDimensionRelatedByDimension3($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseCoordinateDimensionPeer.php';

		if ($this->aCoordinateDimensionRelatedByDimension3 === null && ($this->dim3 > 0)) {

			$this->aCoordinateDimensionRelatedByDimension3 = CoordinateDimensionPeer::retrieveByPK($this->dim3, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = CoordinateDimensionPeer::retrieveByPK($this->dim3, $con);
			   $obj->addCoordinateDimensionsRelatedByDimension3($this);
			 */
		}
		return $this->aCoordinateDimensionRelatedByDimension3;
	}

	/**
	 * Declares an association between this object and a CoordinateDimension object.
	 *
	 * @param      CoordinateDimension $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setCoordinateDimensionRelatedByDimension2($v)
	{


		if ($v === null) {
			$this->setDimension2(NULL);
		} else {
			$this->setDimension2($v->getId());
		}


		$this->aCoordinateDimensionRelatedByDimension2 = $v;
	}


	/**
	 * Get the associated CoordinateDimension object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     CoordinateDimension The associated CoordinateDimension object.
	 * @throws     PropelException
	 */
	public function getCoordinateDimensionRelatedByDimension2($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseCoordinateDimensionPeer.php';

		if ($this->aCoordinateDimensionRelatedByDimension2 === null && ($this->dim2 > 0)) {

			$this->aCoordinateDimensionRelatedByDimension2 = CoordinateDimensionPeer::retrieveByPK($this->dim2, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = CoordinateDimensionPeer::retrieveByPK($this->dim2, $con);
			   $obj->addCoordinateDimensionsRelatedByDimension2($this);
			 */
		}
		return $this->aCoordinateDimensionRelatedByDimension2;
	}

	/**
	 * Temporary storage of collCoordinateSpaces to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpaces()
	{
		if ($this->collCoordinateSpaces === null) {
			$this->collCoordinateSpaces = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSystem has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 * If this CoordinateSystem is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpaces($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
					$this->collCoordinateSpaces = CoordinateSpacePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;
		return $this->collCoordinateSpaces;
	}

	/**
	 * Returns the number of related CoordinateSpaces.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpaces($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

		return CoordinateSpacePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a CoordinateSpace object to this object
	 * through the CoordinateSpace foreign key attribute
	 *
	 * @param      CoordinateSpace $l CoordinateSpace
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCoordinateSpace(CoordinateSpace $l)
	{
		$this->collCoordinateSpaces[] = $l;
		$l->setCoordinateSystem($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSystem is new, it will return
	 * an empty collection; or if this CoordinateSystem has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSystem.
	 */
	public function getCoordinateSpacesJoinCoordinateSpaceRelatedByParentId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSystem is new, it will return
	 * an empty collection; or if this CoordinateSystem has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSystem.
	 */
	public function getCoordinateSpacesJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSystem is new, it will return
	 * an empty collection; or if this CoordinateSystem has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSystem.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByTranslationXUnitId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationXUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationXUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSystem is new, it will return
	 * an empty collection; or if this CoordinateSystem has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSystem.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByTranslationYUnitId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationYUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationYUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSystem is new, it will return
	 * an empty collection; or if this CoordinateSystem has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSystem.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByRotationZUnitId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationZUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationZUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSystem is new, it will return
	 * an empty collection; or if this CoordinateSystem has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSystem.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByAltitudeUnitId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByAltitudeUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByAltitudeUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSystem is new, it will return
	 * an empty collection; or if this CoordinateSystem has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSystem.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByRotationYUnitId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationYUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationYUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSystem is new, it will return
	 * an empty collection; or if this CoordinateSystem has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSystem.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByTranslationZUnitId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationZUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationZUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSystem is new, it will return
	 * an empty collection; or if this CoordinateSystem has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSystem.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByRotationXUnitId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationXUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationXUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}

} // BaseCoordinateSystem
