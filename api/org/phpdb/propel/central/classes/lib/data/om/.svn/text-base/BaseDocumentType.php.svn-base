<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/DocumentTypePeer.php';

/**
 * Base class that represents a row from the 'DOCUMENT_TYPE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseDocumentType extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        DocumentTypePeer
	 */
	protected static $peer;


	/**
	 * The value for the document_type_id field.
	 * @var        double
	 */
	protected $document_type_id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the preferred_format field.
	 * @var        string
	 */
	protected $preferred_format;

	/**
	 * Collection to store aggregation of collEquipmentDocumentations.
	 * @var        array
	 */
	protected $collEquipmentDocumentations;

	/**
	 * The criteria used to select the current contents of collEquipmentDocumentations.
	 * @var        Criteria
	 */
	protected $lastEquipmentDocumentationCriteria = null;

	/**
	 * Collection to store aggregation of collFacilityDataFiles.
	 * @var        array
	 */
	protected $collFacilityDataFiles;

	/**
	 * The criteria used to select the current contents of collFacilityDataFiles.
	 * @var        Criteria
	 */
	protected $lastFacilityDataFileCriteria = null;

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
	 * Get the [document_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->document_type_id;
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
	 * Get the [preferred_format] column value.
	 * 
	 * @return     string
	 */
	public function getPreferredFormat()
	{

		return $this->preferred_format;
	}

	/**
	 * Set the value of [document_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->document_type_id !== $v) {
			$this->document_type_id = $v;
			$this->modifiedColumns[] = DocumentTypePeer::DOCUMENT_TYPE_ID;
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
			$this->modifiedColumns[] = DocumentTypePeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [preferred_format] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPreferredFormat($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->preferred_format !== $v) {
			$this->preferred_format = $v;
			$this->modifiedColumns[] = DocumentTypePeer::PREFERRED_FORMAT;
		}

	} // setPreferredFormat()

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

			$this->document_type_id = $rs->getFloat($startcol + 0);

			$this->name = $rs->getString($startcol + 1);

			$this->preferred_format = $rs->getString($startcol + 2);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 3; // 3 = DocumentTypePeer::NUM_COLUMNS - DocumentTypePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating DocumentType object", $e);
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
			$con = Propel::getConnection(DocumentTypePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			DocumentTypePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(DocumentTypePeer::DATABASE_NAME);
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


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = DocumentTypePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += DocumentTypePeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collEquipmentDocumentations !== null) {
				foreach($this->collEquipmentDocumentations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collFacilityDataFiles !== null) {
				foreach($this->collFacilityDataFiles as $referrerFK) {
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


			if (($retval = DocumentTypePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collEquipmentDocumentations !== null) {
					foreach($this->collEquipmentDocumentations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collFacilityDataFiles !== null) {
					foreach($this->collFacilityDataFiles as $referrerFK) {
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
		$pos = DocumentTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPreferredFormat();
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
		$keys = DocumentTypePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getName(),
			$keys[2] => $this->getPreferredFormat(),
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
		$pos = DocumentTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPreferredFormat($value);
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
		$keys = DocumentTypePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPreferredFormat($arr[$keys[2]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(DocumentTypePeer::DATABASE_NAME);

		if ($this->isColumnModified(DocumentTypePeer::DOCUMENT_TYPE_ID)) $criteria->add(DocumentTypePeer::DOCUMENT_TYPE_ID, $this->document_type_id);
		if ($this->isColumnModified(DocumentTypePeer::NAME)) $criteria->add(DocumentTypePeer::NAME, $this->name);
		if ($this->isColumnModified(DocumentTypePeer::PREFERRED_FORMAT)) $criteria->add(DocumentTypePeer::PREFERRED_FORMAT, $this->preferred_format);

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
		$criteria = new Criteria(DocumentTypePeer::DATABASE_NAME);

		$criteria->add(DocumentTypePeer::DOCUMENT_TYPE_ID, $this->document_type_id);

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
	 * Generic method to set the primary key (document_type_id column).
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
	 * @param      object $copyObj An object of DocumentType (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setName($this->name);

		$copyObj->setPreferredFormat($this->preferred_format);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getEquipmentDocumentations() as $relObj) {
				$copyObj->addEquipmentDocumentation($relObj->copy($deepCopy));
			}

			foreach($this->getFacilityDataFiles() as $relObj) {
				$copyObj->addFacilityDataFile($relObj->copy($deepCopy));
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
	 * @return     DocumentType Clone of current object.
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
	 * @return     DocumentTypePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new DocumentTypePeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collEquipmentDocumentations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentDocumentations()
	{
		if ($this->collEquipmentDocumentations === null) {
			$this->collEquipmentDocumentations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DocumentType has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 * If this DocumentType is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentDocumentations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentDocumentationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentDocumentations === null) {
			if ($this->isNew()) {
			   $this->collEquipmentDocumentations = array();
			} else {

				$criteria->add(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, $this->getId());

				EquipmentDocumentationPeer::addSelectColumns($criteria);
				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, $this->getId());

				EquipmentDocumentationPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentDocumentationCriteria) || !$this->lastEquipmentDocumentationCriteria->equals($criteria)) {
					$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentDocumentationCriteria = $criteria;
		return $this->collEquipmentDocumentations;
	}

	/**
	 * Returns the number of related EquipmentDocumentations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentDocumentations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentDocumentationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, $this->getId());

		return EquipmentDocumentationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentDocumentation object to this object
	 * through the EquipmentDocumentation foreign key attribute
	 *
	 * @param      EquipmentDocumentation $l EquipmentDocumentation
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentDocumentation(EquipmentDocumentation $l)
	{
		$this->collEquipmentDocumentations[] = $l;
		$l->setDocumentType($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DocumentType is new, it will return
	 * an empty collection; or if this DocumentType has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DocumentType.
	 */
	public function getEquipmentDocumentationsJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentDocumentationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentDocumentations === null) {
			if ($this->isNew()) {
				$this->collEquipmentDocumentations = array();
			} else {

				$criteria->add(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, $this->getId());

				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, $this->getId());

			if (!isset($this->lastEquipmentDocumentationCriteria) || !$this->lastEquipmentDocumentationCriteria->equals($criteria)) {
				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastEquipmentDocumentationCriteria = $criteria;

		return $this->collEquipmentDocumentations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DocumentType is new, it will return
	 * an empty collection; or if this DocumentType has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DocumentType.
	 */
	public function getEquipmentDocumentationsJoinDocumentFormat($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentDocumentationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentDocumentations === null) {
			if ($this->isNew()) {
				$this->collEquipmentDocumentations = array();
			} else {

				$criteria->add(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, $this->getId());

				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, $this->getId());

			if (!isset($this->lastEquipmentDocumentationCriteria) || !$this->lastEquipmentDocumentationCriteria->equals($criteria)) {
				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		}
		$this->lastEquipmentDocumentationCriteria = $criteria;

		return $this->collEquipmentDocumentations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DocumentType is new, it will return
	 * an empty collection; or if this DocumentType has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DocumentType.
	 */
	public function getEquipmentDocumentationsJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentDocumentationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentDocumentations === null) {
			if ($this->isNew()) {
				$this->collEquipmentDocumentations = array();
			} else {

				$criteria->add(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, $this->getId());

				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, $this->getId());

			if (!isset($this->lastEquipmentDocumentationCriteria) || !$this->lastEquipmentDocumentationCriteria->equals($criteria)) {
				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastEquipmentDocumentationCriteria = $criteria;

		return $this->collEquipmentDocumentations;
	}

	/**
	 * Temporary storage of collFacilityDataFiles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initFacilityDataFiles()
	{
		if ($this->collFacilityDataFiles === null) {
			$this->collFacilityDataFiles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DocumentType has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 * If this DocumentType is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getFacilityDataFiles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseFacilityDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collFacilityDataFiles === null) {
			if ($this->isNew()) {
			   $this->collFacilityDataFiles = array();
			} else {

				$criteria->add(FacilityDataFilePeer::DOC_TYPE_ID, $this->getId());

				FacilityDataFilePeer::addSelectColumns($criteria);
				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(FacilityDataFilePeer::DOC_TYPE_ID, $this->getId());

				FacilityDataFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastFacilityDataFileCriteria) || !$this->lastFacilityDataFileCriteria->equals($criteria)) {
					$this->collFacilityDataFiles = FacilityDataFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastFacilityDataFileCriteria = $criteria;
		return $this->collFacilityDataFiles;
	}

	/**
	 * Returns the number of related FacilityDataFiles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countFacilityDataFiles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseFacilityDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(FacilityDataFilePeer::DOC_TYPE_ID, $this->getId());

		return FacilityDataFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a FacilityDataFile object to this object
	 * through the FacilityDataFile foreign key attribute
	 *
	 * @param      FacilityDataFile $l FacilityDataFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addFacilityDataFile(FacilityDataFile $l)
	{
		$this->collFacilityDataFiles[] = $l;
		$l->setDocumentType($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DocumentType is new, it will return
	 * an empty collection; or if this DocumentType has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DocumentType.
	 */
	public function getFacilityDataFilesJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseFacilityDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collFacilityDataFiles === null) {
			if ($this->isNew()) {
				$this->collFacilityDataFiles = array();
			} else {

				$criteria->add(FacilityDataFilePeer::DOC_TYPE_ID, $this->getId());

				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(FacilityDataFilePeer::DOC_TYPE_ID, $this->getId());

			if (!isset($this->lastFacilityDataFileCriteria) || !$this->lastFacilityDataFileCriteria->equals($criteria)) {
				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastFacilityDataFileCriteria = $criteria;

		return $this->collFacilityDataFiles;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DocumentType is new, it will return
	 * an empty collection; or if this DocumentType has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DocumentType.
	 */
	public function getFacilityDataFilesJoinDocumentFormat($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseFacilityDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collFacilityDataFiles === null) {
			if ($this->isNew()) {
				$this->collFacilityDataFiles = array();
			} else {

				$criteria->add(FacilityDataFilePeer::DOC_TYPE_ID, $this->getId());

				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(FacilityDataFilePeer::DOC_TYPE_ID, $this->getId());

			if (!isset($this->lastFacilityDataFileCriteria) || !$this->lastFacilityDataFileCriteria->equals($criteria)) {
				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		}
		$this->lastFacilityDataFileCriteria = $criteria;

		return $this->collFacilityDataFiles;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DocumentType is new, it will return
	 * an empty collection; or if this DocumentType has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DocumentType.
	 */
	public function getFacilityDataFilesJoinOrganization($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseFacilityDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collFacilityDataFiles === null) {
			if ($this->isNew()) {
				$this->collFacilityDataFiles = array();
			} else {

				$criteria->add(FacilityDataFilePeer::DOC_TYPE_ID, $this->getId());

				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinOrganization($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(FacilityDataFilePeer::DOC_TYPE_ID, $this->getId());

			if (!isset($this->lastFacilityDataFileCriteria) || !$this->lastFacilityDataFileCriteria->equals($criteria)) {
				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinOrganization($criteria, $con);
			}
		}
		$this->lastFacilityDataFileCriteria = $criteria;

		return $this->collFacilityDataFiles;
	}

} // BaseDocumentType
