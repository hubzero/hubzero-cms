<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SimilitudeLawPeer.php';

/**
 * Base class that represents a row from the 'SIMILITUDE_LAW' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSimilitudeLaw extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SimilitudeLawPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the compute_equation field.
	 * @var        string
	 */
	protected $compute_equation;


	/**
	 * The value for the dependence field.
	 * @var        string
	 */
	protected $dependence;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the display_equation field.
	 * @var        string
	 */
	protected $display_equation;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the similitude_law_group_id field.
	 * @var        double
	 */
	protected $similitude_law_group_id;


	/**
	 * The value for the symbol field.
	 * @var        string
	 */
	protected $symbol;


	/**
	 * The value for the system_name field.
	 * @var        string
	 */
	protected $system_name;


	/**
	 * The value for the unit_description field.
	 * @var        string
	 */
	protected $unit_description;

	/**
	 * @var        SimilitudeLawGroup
	 */
	protected $aSimilitudeLawGroup;

	/**
	 * Collection to store aggregation of collSimilitudeLawValues.
	 * @var        array
	 */
	protected $collSimilitudeLawValues;

	/**
	 * The criteria used to select the current contents of collSimilitudeLawValues.
	 * @var        Criteria
	 */
	protected $lastSimilitudeLawValueCriteria = null;

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
	 * Get the [compute_equation] column value.
	 * 
	 * @return     string
	 */
	public function getComputeEquation()
	{

		return $this->compute_equation;
	}

	/**
	 * Get the [dependence] column value.
	 * 
	 * @return     string
	 */
	public function getDependence()
	{

		return $this->dependence;
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
	 * Get the [display_equation] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayEquation()
	{

		return $this->display_equation;
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
	 * Get the [similitude_law_group_id] column value.
	 * 
	 * @return     double
	 */
	public function getSimilitudeLawGroupId()
	{

		return $this->similitude_law_group_id;
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
	 * Get the [system_name] column value.
	 * 
	 * @return     string
	 */
	public function getSystemName()
	{

		return $this->system_name;
	}

	/**
	 * Get the [unit_description] column value.
	 * 
	 * @return     string
	 */
	public function getUnitDescription()
	{

		return $this->unit_description;
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
			$this->modifiedColumns[] = SimilitudeLawPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [compute_equation] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setComputeEquation($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->compute_equation !== $v) {
			$this->compute_equation = $v;
			$this->modifiedColumns[] = SimilitudeLawPeer::COMPUTE_EQUATION;
		}

	} // setComputeEquation()

	/**
	 * Set the value of [dependence] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDependence($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->dependence !== $v) {
			$this->dependence = $v;
			$this->modifiedColumns[] = SimilitudeLawPeer::DEPENDENCE;
		}

	} // setDependence()

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
			$this->modifiedColumns[] = SimilitudeLawPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [display_equation] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDisplayEquation($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->display_equation !== $v) {
			$this->display_equation = $v;
			$this->modifiedColumns[] = SimilitudeLawPeer::DISPLAY_EQUATION;
		}

	} // setDisplayEquation()

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
			$this->modifiedColumns[] = SimilitudeLawPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [similitude_law_group_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSimilitudeLawGroupId($v)
	{

		if ($this->similitude_law_group_id !== $v) {
			$this->similitude_law_group_id = $v;
			$this->modifiedColumns[] = SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID;
		}

		if ($this->aSimilitudeLawGroup !== null && $this->aSimilitudeLawGroup->getId() !== $v) {
			$this->aSimilitudeLawGroup = null;
		}

	} // setSimilitudeLawGroupId()

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
			$this->modifiedColumns[] = SimilitudeLawPeer::SYMBOL;
		}

	} // setSymbol()

	/**
	 * Set the value of [system_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSystemName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->system_name !== $v) {
			$this->system_name = $v;
			$this->modifiedColumns[] = SimilitudeLawPeer::SYSTEM_NAME;
		}

	} // setSystemName()

	/**
	 * Set the value of [unit_description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setUnitDescription($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->unit_description !== $v) {
			$this->unit_description = $v;
			$this->modifiedColumns[] = SimilitudeLawPeer::UNIT_DESCRIPTION;
		}

	} // setUnitDescription()

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

			$this->compute_equation = $rs->getString($startcol + 1);

			$this->dependence = $rs->getString($startcol + 2);

			$this->description = $rs->getString($startcol + 3);

			$this->display_equation = $rs->getString($startcol + 4);

			$this->name = $rs->getString($startcol + 5);

			$this->similitude_law_group_id = $rs->getFloat($startcol + 6);

			$this->symbol = $rs->getString($startcol + 7);

			$this->system_name = $rs->getString($startcol + 8);

			$this->unit_description = $rs->getString($startcol + 9);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 10; // 10 = SimilitudeLawPeer::NUM_COLUMNS - SimilitudeLawPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SimilitudeLaw object", $e);
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
			$con = Propel::getConnection(SimilitudeLawPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SimilitudeLawPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SimilitudeLawPeer::DATABASE_NAME);
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

			if ($this->aSimilitudeLawGroup !== null) {
				if ($this->aSimilitudeLawGroup->isModified()) {
					$affectedRows += $this->aSimilitudeLawGroup->save($con);
				}
				$this->setSimilitudeLawGroup($this->aSimilitudeLawGroup);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SimilitudeLawPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SimilitudeLawPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collSimilitudeLawValues !== null) {
				foreach($this->collSimilitudeLawValues as $referrerFK) {
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

			if ($this->aSimilitudeLawGroup !== null) {
				if (!$this->aSimilitudeLawGroup->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSimilitudeLawGroup->getValidationFailures());
				}
			}


			if (($retval = SimilitudeLawPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collSimilitudeLawValues !== null) {
					foreach($this->collSimilitudeLawValues as $referrerFK) {
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
		$pos = SimilitudeLawPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getComputeEquation();
				break;
			case 2:
				return $this->getDependence();
				break;
			case 3:
				return $this->getDescription();
				break;
			case 4:
				return $this->getDisplayEquation();
				break;
			case 5:
				return $this->getName();
				break;
			case 6:
				return $this->getSimilitudeLawGroupId();
				break;
			case 7:
				return $this->getSymbol();
				break;
			case 8:
				return $this->getSystemName();
				break;
			case 9:
				return $this->getUnitDescription();
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
		$keys = SimilitudeLawPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getComputeEquation(),
			$keys[2] => $this->getDependence(),
			$keys[3] => $this->getDescription(),
			$keys[4] => $this->getDisplayEquation(),
			$keys[5] => $this->getName(),
			$keys[6] => $this->getSimilitudeLawGroupId(),
			$keys[7] => $this->getSymbol(),
			$keys[8] => $this->getSystemName(),
			$keys[9] => $this->getUnitDescription(),
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
		$pos = SimilitudeLawPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setComputeEquation($value);
				break;
			case 2:
				$this->setDependence($value);
				break;
			case 3:
				$this->setDescription($value);
				break;
			case 4:
				$this->setDisplayEquation($value);
				break;
			case 5:
				$this->setName($value);
				break;
			case 6:
				$this->setSimilitudeLawGroupId($value);
				break;
			case 7:
				$this->setSymbol($value);
				break;
			case 8:
				$this->setSystemName($value);
				break;
			case 9:
				$this->setUnitDescription($value);
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
		$keys = SimilitudeLawPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setComputeEquation($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDependence($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDescription($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDisplayEquation($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setName($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setSimilitudeLawGroupId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setSymbol($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setSystemName($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setUnitDescription($arr[$keys[9]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SimilitudeLawPeer::DATABASE_NAME);

		if ($this->isColumnModified(SimilitudeLawPeer::ID)) $criteria->add(SimilitudeLawPeer::ID, $this->id);
		if ($this->isColumnModified(SimilitudeLawPeer::COMPUTE_EQUATION)) $criteria->add(SimilitudeLawPeer::COMPUTE_EQUATION, $this->compute_equation);
		if ($this->isColumnModified(SimilitudeLawPeer::DEPENDENCE)) $criteria->add(SimilitudeLawPeer::DEPENDENCE, $this->dependence);
		if ($this->isColumnModified(SimilitudeLawPeer::DESCRIPTION)) $criteria->add(SimilitudeLawPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(SimilitudeLawPeer::DISPLAY_EQUATION)) $criteria->add(SimilitudeLawPeer::DISPLAY_EQUATION, $this->display_equation);
		if ($this->isColumnModified(SimilitudeLawPeer::NAME)) $criteria->add(SimilitudeLawPeer::NAME, $this->name);
		if ($this->isColumnModified(SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID)) $criteria->add(SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID, $this->similitude_law_group_id);
		if ($this->isColumnModified(SimilitudeLawPeer::SYMBOL)) $criteria->add(SimilitudeLawPeer::SYMBOL, $this->symbol);
		if ($this->isColumnModified(SimilitudeLawPeer::SYSTEM_NAME)) $criteria->add(SimilitudeLawPeer::SYSTEM_NAME, $this->system_name);
		if ($this->isColumnModified(SimilitudeLawPeer::UNIT_DESCRIPTION)) $criteria->add(SimilitudeLawPeer::UNIT_DESCRIPTION, $this->unit_description);

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
		$criteria = new Criteria(SimilitudeLawPeer::DATABASE_NAME);

		$criteria->add(SimilitudeLawPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SimilitudeLaw (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setComputeEquation($this->compute_equation);

		$copyObj->setDependence($this->dependence);

		$copyObj->setDescription($this->description);

		$copyObj->setDisplayEquation($this->display_equation);

		$copyObj->setName($this->name);

		$copyObj->setSimilitudeLawGroupId($this->similitude_law_group_id);

		$copyObj->setSymbol($this->symbol);

		$copyObj->setSystemName($this->system_name);

		$copyObj->setUnitDescription($this->unit_description);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getSimilitudeLawValues() as $relObj) {
				$copyObj->addSimilitudeLawValue($relObj->copy($deepCopy));
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
	 * @return     SimilitudeLaw Clone of current object.
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
	 * @return     SimilitudeLawPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SimilitudeLawPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a SimilitudeLawGroup object.
	 *
	 * @param      SimilitudeLawGroup $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSimilitudeLawGroup($v)
	{


		if ($v === null) {
			$this->setSimilitudeLawGroupId(NULL);
		} else {
			$this->setSimilitudeLawGroupId($v->getId());
		}


		$this->aSimilitudeLawGroup = $v;
	}


	/**
	 * Get the associated SimilitudeLawGroup object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SimilitudeLawGroup The associated SimilitudeLawGroup object.
	 * @throws     PropelException
	 */
	public function getSimilitudeLawGroup($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSimilitudeLawGroupPeer.php';

		if ($this->aSimilitudeLawGroup === null && ($this->similitude_law_group_id > 0)) {

			$this->aSimilitudeLawGroup = SimilitudeLawGroupPeer::retrieveByPK($this->similitude_law_group_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SimilitudeLawGroupPeer::retrieveByPK($this->similitude_law_group_id, $con);
			   $obj->addSimilitudeLawGroups($this);
			 */
		}
		return $this->aSimilitudeLawGroup;
	}

	/**
	 * Temporary storage of collSimilitudeLawValues to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSimilitudeLawValues()
	{
		if ($this->collSimilitudeLawValues === null) {
			$this->collSimilitudeLawValues = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SimilitudeLaw has previously
	 * been saved, it will retrieve related SimilitudeLawValues from storage.
	 * If this SimilitudeLaw is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSimilitudeLawValues($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSimilitudeLawValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSimilitudeLawValues === null) {
			if ($this->isNew()) {
			   $this->collSimilitudeLawValues = array();
			} else {

				$criteria->add(SimilitudeLawValuePeer::SIMILITUDE_LAW_ID, $this->getId());

				SimilitudeLawValuePeer::addSelectColumns($criteria);
				$this->collSimilitudeLawValues = SimilitudeLawValuePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SimilitudeLawValuePeer::SIMILITUDE_LAW_ID, $this->getId());

				SimilitudeLawValuePeer::addSelectColumns($criteria);
				if (!isset($this->lastSimilitudeLawValueCriteria) || !$this->lastSimilitudeLawValueCriteria->equals($criteria)) {
					$this->collSimilitudeLawValues = SimilitudeLawValuePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSimilitudeLawValueCriteria = $criteria;
		return $this->collSimilitudeLawValues;
	}

	/**
	 * Returns the number of related SimilitudeLawValues.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSimilitudeLawValues($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSimilitudeLawValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SimilitudeLawValuePeer::SIMILITUDE_LAW_ID, $this->getId());

		return SimilitudeLawValuePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SimilitudeLawValue object to this object
	 * through the SimilitudeLawValue foreign key attribute
	 *
	 * @param      SimilitudeLawValue $l SimilitudeLawValue
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSimilitudeLawValue(SimilitudeLawValue $l)
	{
		$this->collSimilitudeLawValues[] = $l;
		$l->setSimilitudeLaw($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SimilitudeLaw is new, it will return
	 * an empty collection; or if this SimilitudeLaw has previously
	 * been saved, it will retrieve related SimilitudeLawValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in SimilitudeLaw.
	 */
	public function getSimilitudeLawValuesJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSimilitudeLawValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSimilitudeLawValues === null) {
			if ($this->isNew()) {
				$this->collSimilitudeLawValues = array();
			} else {

				$criteria->add(SimilitudeLawValuePeer::SIMILITUDE_LAW_ID, $this->getId());

				$this->collSimilitudeLawValues = SimilitudeLawValuePeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SimilitudeLawValuePeer::SIMILITUDE_LAW_ID, $this->getId());

			if (!isset($this->lastSimilitudeLawValueCriteria) || !$this->lastSimilitudeLawValueCriteria->equals($criteria)) {
				$this->collSimilitudeLawValues = SimilitudeLawValuePeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastSimilitudeLawValueCriteria = $criteria;

		return $this->collSimilitudeLawValues;
	}

} // BaseSimilitudeLaw
