<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/FacilityDataFilePeer.php';

/**
 * Base class that represents a row from the 'FACILITY_DATA_FILE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseFacilityDataFile extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        FacilityDataFilePeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the data_file_id field.
	 * @var        double
	 */
	protected $data_file_id;


	/**
	 * The value for the doc_format_id field.
	 * @var        double
	 */
	protected $doc_format_id;


	/**
	 * The value for the doc_type_id field.
	 * @var        double
	 */
	protected $doc_type_id;


	/**
	 * The value for the facility_id field.
	 * @var        double
	 */
	protected $facility_id;


	/**
	 * The value for the groupby field.
	 * @var        string
	 */
	protected $groupby;


	/**
	 * The value for the info_type field.
	 * @var        string
	 */
	protected $info_type;


	/**
	 * The value for the sub_info_type field.
	 * @var        string
	 */
	protected $sub_info_type;

	/**
	 * @var        DataFile
	 */
	protected $aDataFile;

	/**
	 * @var        DocumentFormat
	 */
	protected $aDocumentFormat;

	/**
	 * @var        DocumentType
	 */
	protected $aDocumentType;

	/**
	 * @var        Organization
	 */
	protected $aOrganization;

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
	 * Get the [data_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getDataFileId()
	{

		return $this->data_file_id;
	}

	/**
	 * Get the [doc_format_id] column value.
	 * 
	 * @return     double
	 */
	public function getDocFormatId()
	{

		return $this->doc_format_id;
	}

	/**
	 * Get the [doc_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getDocTypeId()
	{

		return $this->doc_type_id;
	}

	/**
	 * Get the [facility_id] column value.
	 * 
	 * @return     double
	 */
	public function getFacilityId()
	{

		return $this->facility_id;
	}

	/**
	 * Get the [groupby] column value.
	 * 
	 * @return     string
	 */
	public function getGroupBy()
	{

		return $this->groupby;
	}

	/**
	 * Get the [info_type] column value.
	 * 
	 * @return     string
	 */
	public function getInfoType()
	{

		return $this->info_type;
	}

	/**
	 * Get the [sub_info_type] column value.
	 * 
	 * @return     string
	 */
	public function getSubInfoType()
	{

		return $this->sub_info_type;
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
			$this->modifiedColumns[] = FacilityDataFilePeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [data_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDataFileId($v)
	{

		if ($this->data_file_id !== $v) {
			$this->data_file_id = $v;
			$this->modifiedColumns[] = FacilityDataFilePeer::DATA_FILE_ID;
		}

		if ($this->aDataFile !== null && $this->aDataFile->getId() !== $v) {
			$this->aDataFile = null;
		}

	} // setDataFileId()

	/**
	 * Set the value of [doc_format_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDocFormatId($v)
	{

		if ($this->doc_format_id !== $v) {
			$this->doc_format_id = $v;
			$this->modifiedColumns[] = FacilityDataFilePeer::DOC_FORMAT_ID;
		}

		if ($this->aDocumentFormat !== null && $this->aDocumentFormat->getId() !== $v) {
			$this->aDocumentFormat = null;
		}

	} // setDocFormatId()

	/**
	 * Set the value of [doc_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDocTypeId($v)
	{

		if ($this->doc_type_id !== $v) {
			$this->doc_type_id = $v;
			$this->modifiedColumns[] = FacilityDataFilePeer::DOC_TYPE_ID;
		}

		if ($this->aDocumentType !== null && $this->aDocumentType->getId() !== $v) {
			$this->aDocumentType = null;
		}

	} // setDocTypeId()

	/**
	 * Set the value of [facility_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFacilityId($v)
	{

		if ($this->facility_id !== $v) {
			$this->facility_id = $v;
			$this->modifiedColumns[] = FacilityDataFilePeer::FACILITY_ID;
		}

		if ($this->aOrganization !== null && $this->aOrganization->getId() !== $v) {
			$this->aOrganization = null;
		}

	} // setFacilityId()

	/**
	 * Set the value of [groupby] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setGroupBy($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->groupby !== $v) {
			$this->groupby = $v;
			$this->modifiedColumns[] = FacilityDataFilePeer::GROUPBY;
		}

	} // setGroupBy()

	/**
	 * Set the value of [info_type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setInfoType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->info_type !== $v) {
			$this->info_type = $v;
			$this->modifiedColumns[] = FacilityDataFilePeer::INFO_TYPE;
		}

	} // setInfoType()

	/**
	 * Set the value of [sub_info_type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSubInfoType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sub_info_type !== $v) {
			$this->sub_info_type = $v;
			$this->modifiedColumns[] = FacilityDataFilePeer::SUB_INFO_TYPE;
		}

	} // setSubInfoType()

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

			$this->data_file_id = $rs->getFloat($startcol + 1);

			$this->doc_format_id = $rs->getFloat($startcol + 2);

			$this->doc_type_id = $rs->getFloat($startcol + 3);

			$this->facility_id = $rs->getFloat($startcol + 4);

			$this->groupby = $rs->getString($startcol + 5);

			$this->info_type = $rs->getString($startcol + 6);

			$this->sub_info_type = $rs->getString($startcol + 7);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 8; // 8 = FacilityDataFilePeer::NUM_COLUMNS - FacilityDataFilePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating FacilityDataFile object", $e);
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
			$con = Propel::getConnection(FacilityDataFilePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			FacilityDataFilePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(FacilityDataFilePeer::DATABASE_NAME);
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

			if ($this->aDataFile !== null) {
				if ($this->aDataFile->isModified()) {
					$affectedRows += $this->aDataFile->save($con);
				}
				$this->setDataFile($this->aDataFile);
			}

			if ($this->aDocumentFormat !== null) {
				if ($this->aDocumentFormat->isModified()) {
					$affectedRows += $this->aDocumentFormat->save($con);
				}
				$this->setDocumentFormat($this->aDocumentFormat);
			}

			if ($this->aDocumentType !== null) {
				if ($this->aDocumentType->isModified()) {
					$affectedRows += $this->aDocumentType->save($con);
				}
				$this->setDocumentType($this->aDocumentType);
			}

			if ($this->aOrganization !== null) {
				if ($this->aOrganization->isModified()) {
					$affectedRows += $this->aOrganization->save($con);
				}
				$this->setOrganization($this->aOrganization);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = FacilityDataFilePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += FacilityDataFilePeer::doUpdate($this, $con);
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

			if ($this->aDataFile !== null) {
				if (!$this->aDataFile->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFile->getValidationFailures());
				}
			}

			if ($this->aDocumentFormat !== null) {
				if (!$this->aDocumentFormat->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDocumentFormat->getValidationFailures());
				}
			}

			if ($this->aDocumentType !== null) {
				if (!$this->aDocumentType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDocumentType->getValidationFailures());
				}
			}

			if ($this->aOrganization !== null) {
				if (!$this->aOrganization->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aOrganization->getValidationFailures());
				}
			}


			if (($retval = FacilityDataFilePeer::doValidate($this, $columns)) !== true) {
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
		$pos = FacilityDataFilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDataFileId();
				break;
			case 2:
				return $this->getDocFormatId();
				break;
			case 3:
				return $this->getDocTypeId();
				break;
			case 4:
				return $this->getFacilityId();
				break;
			case 5:
				return $this->getGroupBy();
				break;
			case 6:
				return $this->getInfoType();
				break;
			case 7:
				return $this->getSubInfoType();
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
		$keys = FacilityDataFilePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDataFileId(),
			$keys[2] => $this->getDocFormatId(),
			$keys[3] => $this->getDocTypeId(),
			$keys[4] => $this->getFacilityId(),
			$keys[5] => $this->getGroupBy(),
			$keys[6] => $this->getInfoType(),
			$keys[7] => $this->getSubInfoType(),
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
		$pos = FacilityDataFilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDataFileId($value);
				break;
			case 2:
				$this->setDocFormatId($value);
				break;
			case 3:
				$this->setDocTypeId($value);
				break;
			case 4:
				$this->setFacilityId($value);
				break;
			case 5:
				$this->setGroupBy($value);
				break;
			case 6:
				$this->setInfoType($value);
				break;
			case 7:
				$this->setSubInfoType($value);
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
		$keys = FacilityDataFilePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDataFileId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDocFormatId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDocTypeId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setFacilityId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setGroupBy($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setInfoType($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setSubInfoType($arr[$keys[7]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(FacilityDataFilePeer::DATABASE_NAME);

		if ($this->isColumnModified(FacilityDataFilePeer::ID)) $criteria->add(FacilityDataFilePeer::ID, $this->id);
		if ($this->isColumnModified(FacilityDataFilePeer::DATA_FILE_ID)) $criteria->add(FacilityDataFilePeer::DATA_FILE_ID, $this->data_file_id);
		if ($this->isColumnModified(FacilityDataFilePeer::DOC_FORMAT_ID)) $criteria->add(FacilityDataFilePeer::DOC_FORMAT_ID, $this->doc_format_id);
		if ($this->isColumnModified(FacilityDataFilePeer::DOC_TYPE_ID)) $criteria->add(FacilityDataFilePeer::DOC_TYPE_ID, $this->doc_type_id);
		if ($this->isColumnModified(FacilityDataFilePeer::FACILITY_ID)) $criteria->add(FacilityDataFilePeer::FACILITY_ID, $this->facility_id);
		if ($this->isColumnModified(FacilityDataFilePeer::GROUPBY)) $criteria->add(FacilityDataFilePeer::GROUPBY, $this->groupby);
		if ($this->isColumnModified(FacilityDataFilePeer::INFO_TYPE)) $criteria->add(FacilityDataFilePeer::INFO_TYPE, $this->info_type);
		if ($this->isColumnModified(FacilityDataFilePeer::SUB_INFO_TYPE)) $criteria->add(FacilityDataFilePeer::SUB_INFO_TYPE, $this->sub_info_type);

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
		$criteria = new Criteria(FacilityDataFilePeer::DATABASE_NAME);

		$criteria->add(FacilityDataFilePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of FacilityDataFile (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDataFileId($this->data_file_id);

		$copyObj->setDocFormatId($this->doc_format_id);

		$copyObj->setDocTypeId($this->doc_type_id);

		$copyObj->setFacilityId($this->facility_id);

		$copyObj->setGroupBy($this->groupby);

		$copyObj->setInfoType($this->info_type);

		$copyObj->setSubInfoType($this->sub_info_type);


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
	 * @return     FacilityDataFile Clone of current object.
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
	 * @return     FacilityDataFilePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new FacilityDataFilePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a DataFile object.
	 *
	 * @param      DataFile $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDataFile($v)
	{


		if ($v === null) {
			$this->setDataFileId(NULL);
		} else {
			$this->setDataFileId($v->getId());
		}


		$this->aDataFile = $v;
	}


	/**
	 * Get the associated DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
	public function getDataFile($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';

		if ($this->aDataFile === null && ($this->data_file_id > 0)) {

			$this->aDataFile = DataFilePeer::retrieveByPK($this->data_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->data_file_id, $con);
			   $obj->addDataFiles($this);
			 */
		}
		return $this->aDataFile;
	}

	/**
	 * Declares an association between this object and a DocumentFormat object.
	 *
	 * @param      DocumentFormat $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDocumentFormat($v)
	{


		if ($v === null) {
			$this->setDocFormatId(NULL);
		} else {
			$this->setDocFormatId($v->getId());
		}


		$this->aDocumentFormat = $v;
	}


	/**
	 * Get the associated DocumentFormat object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DocumentFormat The associated DocumentFormat object.
	 * @throws     PropelException
	 */
	public function getDocumentFormat($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDocumentFormatPeer.php';

		if ($this->aDocumentFormat === null && ($this->doc_format_id > 0)) {

			$this->aDocumentFormat = DocumentFormatPeer::retrieveByPK($this->doc_format_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DocumentFormatPeer::retrieveByPK($this->doc_format_id, $con);
			   $obj->addDocumentFormats($this);
			 */
		}
		return $this->aDocumentFormat;
	}

	/**
	 * Declares an association between this object and a DocumentType object.
	 *
	 * @param      DocumentType $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDocumentType($v)
	{


		if ($v === null) {
			$this->setDocTypeId(NULL);
		} else {
			$this->setDocTypeId($v->getId());
		}


		$this->aDocumentType = $v;
	}


	/**
	 * Get the associated DocumentType object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DocumentType The associated DocumentType object.
	 * @throws     PropelException
	 */
	public function getDocumentType($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDocumentTypePeer.php';

		if ($this->aDocumentType === null && ($this->doc_type_id > 0)) {

			$this->aDocumentType = DocumentTypePeer::retrieveByPK($this->doc_type_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DocumentTypePeer::retrieveByPK($this->doc_type_id, $con);
			   $obj->addDocumentTypes($this);
			 */
		}
		return $this->aDocumentType;
	}

	/**
	 * Declares an association between this object and a Organization object.
	 *
	 * @param      Organization $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setOrganization($v)
	{


		if ($v === null) {
			$this->setFacilityId(NULL);
		} else {
			$this->setFacilityId($v->getId());
		}


		$this->aOrganization = $v;
	}


	/**
	 * Get the associated Organization object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Organization The associated Organization object.
	 * @throws     PropelException
	 */
	public function getOrganization($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';

		if ($this->aOrganization === null && ($this->facility_id > 0)) {

			$this->aOrganization = OrganizationPeer::retrieveByPK($this->facility_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = OrganizationPeer::retrieveByPK($this->facility_id, $con);
			   $obj->addOrganizations($this);
			 */
		}
		return $this->aOrganization;
	}

} // BaseFacilityDataFile
