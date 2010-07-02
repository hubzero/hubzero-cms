<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/tsunami/TsunamiBiologicalDataPeer.php';

/**
 * Base class that represents a row from the 'TSUNAMI_BIOLOGICAL_DATA' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiBiologicalData extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        TsunamiBiologicalDataPeer
	 */
	protected static $peer;


	/**
	 * The value for the tsunami_biological_data_id field.
	 * @var        double
	 */
	protected $tsunami_biological_data_id;


	/**
	 * The value for the fauna field.
	 * @var        double
	 */
	protected $fauna;


	/**
	 * The value for the flora field.
	 * @var        double
	 */
	protected $flora;


	/**
	 * The value for the marine_biology field.
	 * @var        double
	 */
	protected $marine_biology;


	/**
	 * The value for the tsunami_doc_lib_id field.
	 * @var        double
	 */
	protected $tsunami_doc_lib_id;

	/**
	 * @var        TsunamiDocLib
	 */
	protected $aTsunamiDocLib;

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
	 * Get the [tsunami_biological_data_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->tsunami_biological_data_id;
	}

	/**
	 * Get the [fauna] column value.
	 * 
	 * @return     double
	 */
	public function getFauna()
	{

		return $this->fauna;
	}

	/**
	 * Get the [flora] column value.
	 * 
	 * @return     double
	 */
	public function getFlora()
	{

		return $this->flora;
	}

	/**
	 * Get the [marine_biology] column value.
	 * 
	 * @return     double
	 */
	public function getMarineBiology()
	{

		return $this->marine_biology;
	}

	/**
	 * Get the [tsunami_doc_lib_id] column value.
	 * 
	 * @return     double
	 */
	public function getTsunamiDocLibId()
	{

		return $this->tsunami_doc_lib_id;
	}

	/**
	 * Set the value of [tsunami_biological_data_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->tsunami_biological_data_id !== $v) {
			$this->tsunami_biological_data_id = $v;
			$this->modifiedColumns[] = TsunamiBiologicalDataPeer::TSUNAMI_BIOLOGICAL_DATA_ID;
		}

	} // setId()

	/**
	 * Set the value of [fauna] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFauna($v)
	{

		if ($this->fauna !== $v) {
			$this->fauna = $v;
			$this->modifiedColumns[] = TsunamiBiologicalDataPeer::FAUNA;
		}

	} // setFauna()

	/**
	 * Set the value of [flora] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFlora($v)
	{

		if ($this->flora !== $v) {
			$this->flora = $v;
			$this->modifiedColumns[] = TsunamiBiologicalDataPeer::FLORA;
		}

	} // setFlora()

	/**
	 * Set the value of [marine_biology] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMarineBiology($v)
	{

		if ($this->marine_biology !== $v) {
			$this->marine_biology = $v;
			$this->modifiedColumns[] = TsunamiBiologicalDataPeer::MARINE_BIOLOGY;
		}

	} // setMarineBiology()

	/**
	 * Set the value of [tsunami_doc_lib_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTsunamiDocLibId($v)
	{

		if ($this->tsunami_doc_lib_id !== $v) {
			$this->tsunami_doc_lib_id = $v;
			$this->modifiedColumns[] = TsunamiBiologicalDataPeer::TSUNAMI_DOC_LIB_ID;
		}

		if ($this->aTsunamiDocLib !== null && $this->aTsunamiDocLib->getId() !== $v) {
			$this->aTsunamiDocLib = null;
		}

	} // setTsunamiDocLibId()

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

			$this->tsunami_biological_data_id = $rs->getFloat($startcol + 0);

			$this->fauna = $rs->getFloat($startcol + 1);

			$this->flora = $rs->getFloat($startcol + 2);

			$this->marine_biology = $rs->getFloat($startcol + 3);

			$this->tsunami_doc_lib_id = $rs->getFloat($startcol + 4);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 5; // 5 = TsunamiBiologicalDataPeer::NUM_COLUMNS - TsunamiBiologicalDataPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating TsunamiBiologicalData object", $e);
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
			$con = Propel::getConnection(TsunamiBiologicalDataPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			TsunamiBiologicalDataPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(TsunamiBiologicalDataPeer::DATABASE_NAME);
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

			if ($this->aTsunamiDocLib !== null) {
				if ($this->aTsunamiDocLib->isModified()) {
					$affectedRows += $this->aTsunamiDocLib->save($con);
				}
				$this->setTsunamiDocLib($this->aTsunamiDocLib);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = TsunamiBiologicalDataPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += TsunamiBiologicalDataPeer::doUpdate($this, $con);
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

			if ($this->aTsunamiDocLib !== null) {
				if (!$this->aTsunamiDocLib->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aTsunamiDocLib->getValidationFailures());
				}
			}


			if (($retval = TsunamiBiologicalDataPeer::doValidate($this, $columns)) !== true) {
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
		$pos = TsunamiBiologicalDataPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getFauna();
				break;
			case 2:
				return $this->getFlora();
				break;
			case 3:
				return $this->getMarineBiology();
				break;
			case 4:
				return $this->getTsunamiDocLibId();
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
		$keys = TsunamiBiologicalDataPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getFauna(),
			$keys[2] => $this->getFlora(),
			$keys[3] => $this->getMarineBiology(),
			$keys[4] => $this->getTsunamiDocLibId(),
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
		$pos = TsunamiBiologicalDataPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setFauna($value);
				break;
			case 2:
				$this->setFlora($value);
				break;
			case 3:
				$this->setMarineBiology($value);
				break;
			case 4:
				$this->setTsunamiDocLibId($value);
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
		$keys = TsunamiBiologicalDataPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setFauna($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setFlora($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setMarineBiology($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setTsunamiDocLibId($arr[$keys[4]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(TsunamiBiologicalDataPeer::DATABASE_NAME);

		if ($this->isColumnModified(TsunamiBiologicalDataPeer::TSUNAMI_BIOLOGICAL_DATA_ID)) $criteria->add(TsunamiBiologicalDataPeer::TSUNAMI_BIOLOGICAL_DATA_ID, $this->tsunami_biological_data_id);
		if ($this->isColumnModified(TsunamiBiologicalDataPeer::FAUNA)) $criteria->add(TsunamiBiologicalDataPeer::FAUNA, $this->fauna);
		if ($this->isColumnModified(TsunamiBiologicalDataPeer::FLORA)) $criteria->add(TsunamiBiologicalDataPeer::FLORA, $this->flora);
		if ($this->isColumnModified(TsunamiBiologicalDataPeer::MARINE_BIOLOGY)) $criteria->add(TsunamiBiologicalDataPeer::MARINE_BIOLOGY, $this->marine_biology);
		if ($this->isColumnModified(TsunamiBiologicalDataPeer::TSUNAMI_DOC_LIB_ID)) $criteria->add(TsunamiBiologicalDataPeer::TSUNAMI_DOC_LIB_ID, $this->tsunami_doc_lib_id);

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
		$criteria = new Criteria(TsunamiBiologicalDataPeer::DATABASE_NAME);

		$criteria->add(TsunamiBiologicalDataPeer::TSUNAMI_BIOLOGICAL_DATA_ID, $this->tsunami_biological_data_id);

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
	 * Generic method to set the primary key (tsunami_biological_data_id column).
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
	 * @param      object $copyObj An object of TsunamiBiologicalData (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setFauna($this->fauna);

		$copyObj->setFlora($this->flora);

		$copyObj->setMarineBiology($this->marine_biology);

		$copyObj->setTsunamiDocLibId($this->tsunami_doc_lib_id);


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
	 * @return     TsunamiBiologicalData Clone of current object.
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
	 * @return     TsunamiBiologicalDataPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new TsunamiBiologicalDataPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a TsunamiDocLib object.
	 *
	 * @param      TsunamiDocLib $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setTsunamiDocLib($v)
	{


		if ($v === null) {
			$this->setTsunamiDocLibId(NULL);
		} else {
			$this->setTsunamiDocLibId($v->getId());
		}


		$this->aTsunamiDocLib = $v;
	}


	/**
	 * Get the associated TsunamiDocLib object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     TsunamiDocLib The associated TsunamiDocLib object.
	 * @throws     PropelException
	 */
	public function getTsunamiDocLib($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiDocLibPeer.php';

		if ($this->aTsunamiDocLib === null && ($this->tsunami_doc_lib_id > 0)) {

			$this->aTsunamiDocLib = TsunamiDocLibPeer::retrieveByPK($this->tsunami_doc_lib_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = TsunamiDocLibPeer::retrieveByPK($this->tsunami_doc_lib_id, $con);
			   $obj->addTsunamiDocLibs($this);
			 */
		}
		return $this->aTsunamiDocLib;
	}

} // BaseTsunamiBiologicalData
