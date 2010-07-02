<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/EquipmentDocumentationPeer.php';

/**
 * Base class that represents a row from the 'EQUIPMENT_DOCUMENTATION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEquipmentDocumentation extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EquipmentDocumentationPeer
	 */
	protected static $peer;


	/**
	 * The value for the equipment_doc_id field.
	 * @var        double
	 */
	protected $equipment_doc_id;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the document_format_id field.
	 * @var        double
	 */
	protected $document_format_id;


	/**
	 * The value for the document_type_id field.
	 * @var        double
	 */
	protected $document_type_id;


	/**
	 * The value for the documentation_file_id field.
	 * @var        double
	 */
	protected $documentation_file_id;


	/**
	 * The value for the equipment_id field.
	 * @var        double
	 */
	protected $equipment_id;


	/**
	 * The value for the last_modified field.
	 * @var        int
	 */
	protected $last_modified;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the page_count field.
	 * @var        double
	 */
	protected $page_count;

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
	 * @var        Equipment
	 */
	protected $aEquipment;

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
	 * Get the [equipment_doc_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->equipment_doc_id;
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
	 * Get the [document_format_id] column value.
	 * 
	 * @return     double
	 */
	public function getDocumentFormatId()
	{

		return $this->document_format_id;
	}

	/**
	 * Get the [document_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getDocumentTypeId()
	{

		return $this->document_type_id;
	}

	/**
	 * Get the [documentation_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getDocumentationFileId()
	{

		return $this->documentation_file_id;
	}

	/**
	 * Get the [equipment_id] column value.
	 * 
	 * @return     double
	 */
	public function getEquipmentId()
	{

		return $this->equipment_id;
	}

	/**
	 * Get the [optionally formatted] [last_modified] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getLastModified($format = '%Y-%m-%d')
	{

		if ($this->last_modified === null || $this->last_modified === '') {
			return null;
		} elseif (!is_int($this->last_modified)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->last_modified);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [last_modified] as date/time value: " . var_export($this->last_modified, true));
			}
		} else {
			$ts = $this->last_modified;
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
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
	 * Get the [page_count] column value.
	 * 
	 * @return     double
	 */
	public function getPageCount()
	{

		return $this->page_count;
	}

	/**
	 * Set the value of [equipment_doc_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->equipment_doc_id !== $v) {
			$this->equipment_doc_id = $v;
			$this->modifiedColumns[] = EquipmentDocumentationPeer::EQUIPMENT_DOC_ID;
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

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = EquipmentDocumentationPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [document_format_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDocumentFormatId($v)
	{

		if ($this->document_format_id !== $v) {
			$this->document_format_id = $v;
			$this->modifiedColumns[] = EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID;
		}

		if ($this->aDocumentFormat !== null && $this->aDocumentFormat->getId() !== $v) {
			$this->aDocumentFormat = null;
		}

	} // setDocumentFormatId()

	/**
	 * Set the value of [document_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDocumentTypeId($v)
	{

		if ($this->document_type_id !== $v) {
			$this->document_type_id = $v;
			$this->modifiedColumns[] = EquipmentDocumentationPeer::DOCUMENT_TYPE_ID;
		}

		if ($this->aDocumentType !== null && $this->aDocumentType->getId() !== $v) {
			$this->aDocumentType = null;
		}

	} // setDocumentTypeId()

	/**
	 * Set the value of [documentation_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDocumentationFileId($v)
	{

		if ($this->documentation_file_id !== $v) {
			$this->documentation_file_id = $v;
			$this->modifiedColumns[] = EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID;
		}

		if ($this->aDataFile !== null && $this->aDataFile->getId() !== $v) {
			$this->aDataFile = null;
		}

	} // setDocumentationFileId()

	/**
	 * Set the value of [equipment_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEquipmentId($v)
	{

		if ($this->equipment_id !== $v) {
			$this->equipment_id = $v;
			$this->modifiedColumns[] = EquipmentDocumentationPeer::EQUIPMENT_ID;
		}

		if ($this->aEquipment !== null && $this->aEquipment->getId() !== $v) {
			$this->aEquipment = null;
		}

	} // setEquipmentId()

	/**
	 * Set the value of [last_modified] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setLastModified($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [last_modified] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->last_modified !== $ts) {
			$this->last_modified = $ts;
			$this->modifiedColumns[] = EquipmentDocumentationPeer::LAST_MODIFIED;
		}

	} // setLastModified()

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
			$this->modifiedColumns[] = EquipmentDocumentationPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [page_count] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPageCount($v)
	{

		if ($this->page_count !== $v) {
			$this->page_count = $v;
			$this->modifiedColumns[] = EquipmentDocumentationPeer::PAGE_COUNT;
		}

	} // setPageCount()

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

			$this->equipment_doc_id = $rs->getFloat($startcol + 0);

			$this->description = $rs->getString($startcol + 1);

			$this->document_format_id = $rs->getFloat($startcol + 2);

			$this->document_type_id = $rs->getFloat($startcol + 3);

			$this->documentation_file_id = $rs->getFloat($startcol + 4);

			$this->equipment_id = $rs->getFloat($startcol + 5);

			$this->last_modified = $rs->getDate($startcol + 6, null);

			$this->name = $rs->getString($startcol + 7);

			$this->page_count = $rs->getFloat($startcol + 8);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = EquipmentDocumentationPeer::NUM_COLUMNS - EquipmentDocumentationPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating EquipmentDocumentation object", $e);
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
			$con = Propel::getConnection(EquipmentDocumentationPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			EquipmentDocumentationPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(EquipmentDocumentationPeer::DATABASE_NAME);
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

			if ($this->aEquipment !== null) {
				if ($this->aEquipment->isModified()) {
					$affectedRows += $this->aEquipment->save($con);
				}
				$this->setEquipment($this->aEquipment);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = EquipmentDocumentationPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += EquipmentDocumentationPeer::doUpdate($this, $con);
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

			if ($this->aEquipment !== null) {
				if (!$this->aEquipment->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEquipment->getValidationFailures());
				}
			}


			if (($retval = EquipmentDocumentationPeer::doValidate($this, $columns)) !== true) {
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
		$pos = EquipmentDocumentationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDocumentFormatId();
				break;
			case 3:
				return $this->getDocumentTypeId();
				break;
			case 4:
				return $this->getDocumentationFileId();
				break;
			case 5:
				return $this->getEquipmentId();
				break;
			case 6:
				return $this->getLastModified();
				break;
			case 7:
				return $this->getName();
				break;
			case 8:
				return $this->getPageCount();
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
		$keys = EquipmentDocumentationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDescription(),
			$keys[2] => $this->getDocumentFormatId(),
			$keys[3] => $this->getDocumentTypeId(),
			$keys[4] => $this->getDocumentationFileId(),
			$keys[5] => $this->getEquipmentId(),
			$keys[6] => $this->getLastModified(),
			$keys[7] => $this->getName(),
			$keys[8] => $this->getPageCount(),
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
		$pos = EquipmentDocumentationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDocumentFormatId($value);
				break;
			case 3:
				$this->setDocumentTypeId($value);
				break;
			case 4:
				$this->setDocumentationFileId($value);
				break;
			case 5:
				$this->setEquipmentId($value);
				break;
			case 6:
				$this->setLastModified($value);
				break;
			case 7:
				$this->setName($value);
				break;
			case 8:
				$this->setPageCount($value);
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
		$keys = EquipmentDocumentationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDescription($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDocumentFormatId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDocumentTypeId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDocumentationFileId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setEquipmentId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setLastModified($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setName($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setPageCount($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EquipmentDocumentationPeer::DATABASE_NAME);

		if ($this->isColumnModified(EquipmentDocumentationPeer::EQUIPMENT_DOC_ID)) $criteria->add(EquipmentDocumentationPeer::EQUIPMENT_DOC_ID, $this->equipment_doc_id);
		if ($this->isColumnModified(EquipmentDocumentationPeer::DESCRIPTION)) $criteria->add(EquipmentDocumentationPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID)) $criteria->add(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, $this->document_format_id);
		if ($this->isColumnModified(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID)) $criteria->add(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, $this->document_type_id);
		if ($this->isColumnModified(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID)) $criteria->add(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, $this->documentation_file_id);
		if ($this->isColumnModified(EquipmentDocumentationPeer::EQUIPMENT_ID)) $criteria->add(EquipmentDocumentationPeer::EQUIPMENT_ID, $this->equipment_id);
		if ($this->isColumnModified(EquipmentDocumentationPeer::LAST_MODIFIED)) $criteria->add(EquipmentDocumentationPeer::LAST_MODIFIED, $this->last_modified);
		if ($this->isColumnModified(EquipmentDocumentationPeer::NAME)) $criteria->add(EquipmentDocumentationPeer::NAME, $this->name);
		if ($this->isColumnModified(EquipmentDocumentationPeer::PAGE_COUNT)) $criteria->add(EquipmentDocumentationPeer::PAGE_COUNT, $this->page_count);

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
		$criteria = new Criteria(EquipmentDocumentationPeer::DATABASE_NAME);

		$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_DOC_ID, $this->equipment_doc_id);

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
	 * Generic method to set the primary key (equipment_doc_id column).
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
	 * @param      object $copyObj An object of EquipmentDocumentation (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDescription($this->description);

		$copyObj->setDocumentFormatId($this->document_format_id);

		$copyObj->setDocumentTypeId($this->document_type_id);

		$copyObj->setDocumentationFileId($this->documentation_file_id);

		$copyObj->setEquipmentId($this->equipment_id);

		$copyObj->setLastModified($this->last_modified);

		$copyObj->setName($this->name);

		$copyObj->setPageCount($this->page_count);


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
	 * @return     EquipmentDocumentation Clone of current object.
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
	 * @return     EquipmentDocumentationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EquipmentDocumentationPeer();
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
			$this->setDocumentationFileId(NULL);
		} else {
			$this->setDocumentationFileId($v->getId());
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

		if ($this->aDataFile === null && ($this->documentation_file_id > 0)) {

			$this->aDataFile = DataFilePeer::retrieveByPK($this->documentation_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->documentation_file_id, $con);
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
			$this->setDocumentFormatId(NULL);
		} else {
			$this->setDocumentFormatId($v->getId());
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

		if ($this->aDocumentFormat === null && ($this->document_format_id > 0)) {

			$this->aDocumentFormat = DocumentFormatPeer::retrieveByPK($this->document_format_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DocumentFormatPeer::retrieveByPK($this->document_format_id, $con);
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
			$this->setDocumentTypeId(NULL);
		} else {
			$this->setDocumentTypeId($v->getId());
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

		if ($this->aDocumentType === null && ($this->document_type_id > 0)) {

			$this->aDocumentType = DocumentTypePeer::retrieveByPK($this->document_type_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DocumentTypePeer::retrieveByPK($this->document_type_id, $con);
			   $obj->addDocumentTypes($this);
			 */
		}
		return $this->aDocumentType;
	}

	/**
	 * Declares an association between this object and a Equipment object.
	 *
	 * @param      Equipment $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEquipment($v)
	{


		if ($v === null) {
			$this->setEquipmentId(NULL);
		} else {
			$this->setEquipmentId($v->getId());
		}


		$this->aEquipment = $v;
	}


	/**
	 * Get the associated Equipment object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Equipment The associated Equipment object.
	 * @throws     PropelException
	 */
	public function getEquipment($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';

		if ($this->aEquipment === null && ($this->equipment_id > 0)) {

			$this->aEquipment = EquipmentPeer::retrieveByPK($this->equipment_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EquipmentPeer::retrieveByPK($this->equipment_id, $con);
			   $obj->addEquipments($this);
			 */
		}
		return $this->aEquipment;
	}

} // BaseEquipmentDocumentation
