<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SiteReportsQFREPcdPeer.php';

/**
 * Base class that represents a row from the 'SITEREPORTS_QFR_EPCD' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSiteReportsQFREPcd extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SiteReportsQFREPcdPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the qfr_id field.
	 * @var        double
	 */
	protected $qfr_id;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the details field.
	 * @var        string
	 */
	protected $details;


	/**
	 * The value for the est_amt field.
	 * @var        double
	 */
	protected $est_amt;


	/**
	 * The value for the eq_or_psc_type field.
	 * @var        double
	 */
	protected $eq_or_psc_type;


	/**
	 * The value for the created_by field.
	 * @var        string
	 */
	protected $created_by;


	/**
	 * The value for the created_on field.
	 * @var        int
	 */
	protected $created_on;


	/**
	 * The value for the updated_by field.
	 * @var        string
	 */
	protected $updated_by;


	/**
	 * The value for the updated_on field.
	 * @var        int
	 */
	protected $updated_on;

	/**
	 * @var        SiteReportsQFR
	 */
	protected $aSiteReportsQFR;

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
	public function getID()
	{

		return $this->id;
	}

	/**
	 * Get the [qfr_id] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_ID()
	{

		return $this->qfr_id;
	}

	/**
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDESCRIPTION()
	{

		return $this->description;
	}

	/**
	 * Get the [details] column value.
	 * 
	 * @return     string
	 */
	public function getDETAILS()
	{

		return $this->details;
	}

	/**
	 * Get the [est_amt] column value.
	 * 
	 * @return     double
	 */
	public function getEST_AMT()
	{

		return $this->est_amt;
	}

	/**
	 * Get the [eq_or_psc_type] column value.
	 * 
	 * @return     double
	 */
	public function getEQ_OR_PSC_TYPE()
	{

		return $this->eq_or_psc_type;
	}

	/**
	 * Get the [created_by] column value.
	 * 
	 * @return     string
	 */
	public function getCREATED_BY()
	{

		return $this->created_by;
	}

	/**
	 * Get the [optionally formatted] [created_on] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getCREATED_ON($format = '%Y-%m-%d')
	{

		if ($this->created_on === null || $this->created_on === '') {
			return null;
		} elseif (!is_int($this->created_on)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->created_on);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [created_on] as date/time value: " . var_export($this->created_on, true));
			}
		} else {
			$ts = $this->created_on;
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
	 * Get the [updated_by] column value.
	 * 
	 * @return     string
	 */
	public function getUPDATED_BY()
	{

		return $this->updated_by;
	}

	/**
	 * Get the [optionally formatted] [updated_on] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getUPDATED_ON($format = '%Y-%m-%d')
	{

		if ($this->updated_on === null || $this->updated_on === '') {
			return null;
		} elseif (!is_int($this->updated_on)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->updated_on);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [updated_on] as date/time value: " . var_export($this->updated_on, true));
			}
		} else {
			$ts = $this->updated_on;
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
	 * Set the value of [id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setID($v)
	{

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = SiteReportsQFREPcdPeer::ID;
		}

	} // setID()

	/**
	 * Set the value of [qfr_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_ID($v)
	{

		if ($this->qfr_id !== $v) {
			$this->qfr_id = $v;
			$this->modifiedColumns[] = SiteReportsQFREPcdPeer::QFR_ID;
		}

		if ($this->aSiteReportsQFR !== null && $this->aSiteReportsQFR->getID() !== $v) {
			$this->aSiteReportsQFR = null;
		}

	} // setQFR_ID()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDESCRIPTION($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = SiteReportsQFREPcdPeer::DESCRIPTION;
		}

	} // setDESCRIPTION()

	/**
	 * Set the value of [details] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDETAILS($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->details !== $v) {
			$this->details = $v;
			$this->modifiedColumns[] = SiteReportsQFREPcdPeer::DETAILS;
		}

	} // setDETAILS()

	/**
	 * Set the value of [est_amt] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEST_AMT($v)
	{

		if ($this->est_amt !== $v) {
			$this->est_amt = $v;
			$this->modifiedColumns[] = SiteReportsQFREPcdPeer::EST_AMT;
		}

	} // setEST_AMT()

	/**
	 * Set the value of [eq_or_psc_type] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEQ_OR_PSC_TYPE($v)
	{

		if ($this->eq_or_psc_type !== $v) {
			$this->eq_or_psc_type = $v;
			$this->modifiedColumns[] = SiteReportsQFREPcdPeer::EQ_OR_PSC_TYPE;
		}

	} // setEQ_OR_PSC_TYPE()

	/**
	 * Set the value of [created_by] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCREATED_BY($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->created_by !== $v) {
			$this->created_by = $v;
			$this->modifiedColumns[] = SiteReportsQFREPcdPeer::CREATED_BY;
		}

	} // setCREATED_BY()

	/**
	 * Set the value of [created_on] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setCREATED_ON($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [created_on] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->created_on !== $ts) {
			$this->created_on = $ts;
			$this->modifiedColumns[] = SiteReportsQFREPcdPeer::CREATED_ON;
		}

	} // setCREATED_ON()

	/**
	 * Set the value of [updated_by] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setUPDATED_BY($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->updated_by !== $v) {
			$this->updated_by = $v;
			$this->modifiedColumns[] = SiteReportsQFREPcdPeer::UPDATED_BY;
		}

	} // setUPDATED_BY()

	/**
	 * Set the value of [updated_on] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setUPDATED_ON($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [updated_on] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->updated_on !== $ts) {
			$this->updated_on = $ts;
			$this->modifiedColumns[] = SiteReportsQFREPcdPeer::UPDATED_ON;
		}

	} // setUPDATED_ON()

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

			$this->qfr_id = $rs->getFloat($startcol + 1);

			$this->description = $rs->getString($startcol + 2);

			$this->details = $rs->getString($startcol + 3);

			$this->est_amt = $rs->getFloat($startcol + 4);

			$this->eq_or_psc_type = $rs->getFloat($startcol + 5);

			$this->created_by = $rs->getString($startcol + 6);

			$this->created_on = $rs->getDate($startcol + 7, null);

			$this->updated_by = $rs->getString($startcol + 8);

			$this->updated_on = $rs->getDate($startcol + 9, null);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 10; // 10 = SiteReportsQFREPcdPeer::NUM_COLUMNS - SiteReportsQFREPcdPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SiteReportsQFREPcd object", $e);
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
			$con = Propel::getConnection(SiteReportsQFREPcdPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SiteReportsQFREPcdPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SiteReportsQFREPcdPeer::DATABASE_NAME);
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

			if ($this->aSiteReportsQFR !== null) {
				if ($this->aSiteReportsQFR->isModified()) {
					$affectedRows += $this->aSiteReportsQFR->save($con);
				}
				$this->setSiteReportsQFR($this->aSiteReportsQFR);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SiteReportsQFREPcdPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setID($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SiteReportsQFREPcdPeer::doUpdate($this, $con);
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

			if ($this->aSiteReportsQFR !== null) {
				if (!$this->aSiteReportsQFR->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSiteReportsQFR->getValidationFailures());
				}
			}


			if (($retval = SiteReportsQFREPcdPeer::doValidate($this, $columns)) !== true) {
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
		$pos = SiteReportsQFREPcdPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getID();
				break;
			case 1:
				return $this->getQFR_ID();
				break;
			case 2:
				return $this->getDESCRIPTION();
				break;
			case 3:
				return $this->getDETAILS();
				break;
			case 4:
				return $this->getEST_AMT();
				break;
			case 5:
				return $this->getEQ_OR_PSC_TYPE();
				break;
			case 6:
				return $this->getCREATED_BY();
				break;
			case 7:
				return $this->getCREATED_ON();
				break;
			case 8:
				return $this->getUPDATED_BY();
				break;
			case 9:
				return $this->getUPDATED_ON();
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
		$keys = SiteReportsQFREPcdPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getID(),
			$keys[1] => $this->getQFR_ID(),
			$keys[2] => $this->getDESCRIPTION(),
			$keys[3] => $this->getDETAILS(),
			$keys[4] => $this->getEST_AMT(),
			$keys[5] => $this->getEQ_OR_PSC_TYPE(),
			$keys[6] => $this->getCREATED_BY(),
			$keys[7] => $this->getCREATED_ON(),
			$keys[8] => $this->getUPDATED_BY(),
			$keys[9] => $this->getUPDATED_ON(),
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
		$pos = SiteReportsQFREPcdPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setID($value);
				break;
			case 1:
				$this->setQFR_ID($value);
				break;
			case 2:
				$this->setDESCRIPTION($value);
				break;
			case 3:
				$this->setDETAILS($value);
				break;
			case 4:
				$this->setEST_AMT($value);
				break;
			case 5:
				$this->setEQ_OR_PSC_TYPE($value);
				break;
			case 6:
				$this->setCREATED_BY($value);
				break;
			case 7:
				$this->setCREATED_ON($value);
				break;
			case 8:
				$this->setUPDATED_BY($value);
				break;
			case 9:
				$this->setUPDATED_ON($value);
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
		$keys = SiteReportsQFREPcdPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setID($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setQFR_ID($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDESCRIPTION($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDETAILS($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setEST_AMT($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setEQ_OR_PSC_TYPE($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setCREATED_BY($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCREATED_ON($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setUPDATED_BY($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setUPDATED_ON($arr[$keys[9]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SiteReportsQFREPcdPeer::DATABASE_NAME);

		if ($this->isColumnModified(SiteReportsQFREPcdPeer::ID)) $criteria->add(SiteReportsQFREPcdPeer::ID, $this->id);
		if ($this->isColumnModified(SiteReportsQFREPcdPeer::QFR_ID)) $criteria->add(SiteReportsQFREPcdPeer::QFR_ID, $this->qfr_id);
		if ($this->isColumnModified(SiteReportsQFREPcdPeer::DESCRIPTION)) $criteria->add(SiteReportsQFREPcdPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(SiteReportsQFREPcdPeer::DETAILS)) $criteria->add(SiteReportsQFREPcdPeer::DETAILS, $this->details);
		if ($this->isColumnModified(SiteReportsQFREPcdPeer::EST_AMT)) $criteria->add(SiteReportsQFREPcdPeer::EST_AMT, $this->est_amt);
		if ($this->isColumnModified(SiteReportsQFREPcdPeer::EQ_OR_PSC_TYPE)) $criteria->add(SiteReportsQFREPcdPeer::EQ_OR_PSC_TYPE, $this->eq_or_psc_type);
		if ($this->isColumnModified(SiteReportsQFREPcdPeer::CREATED_BY)) $criteria->add(SiteReportsQFREPcdPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(SiteReportsQFREPcdPeer::CREATED_ON)) $criteria->add(SiteReportsQFREPcdPeer::CREATED_ON, $this->created_on);
		if ($this->isColumnModified(SiteReportsQFREPcdPeer::UPDATED_BY)) $criteria->add(SiteReportsQFREPcdPeer::UPDATED_BY, $this->updated_by);
		if ($this->isColumnModified(SiteReportsQFREPcdPeer::UPDATED_ON)) $criteria->add(SiteReportsQFREPcdPeer::UPDATED_ON, $this->updated_on);

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
		$criteria = new Criteria(SiteReportsQFREPcdPeer::DATABASE_NAME);

		$criteria->add(SiteReportsQFREPcdPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     double
	 */
	public function getPrimaryKey()
	{
		return $this->getID();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      double $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setID($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of SiteReportsQFREPcd (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setQFR_ID($this->qfr_id);

		$copyObj->setDESCRIPTION($this->description);

		$copyObj->setDETAILS($this->details);

		$copyObj->setEST_AMT($this->est_amt);

		$copyObj->setEQ_OR_PSC_TYPE($this->eq_or_psc_type);

		$copyObj->setCREATED_BY($this->created_by);

		$copyObj->setCREATED_ON($this->created_on);

		$copyObj->setUPDATED_BY($this->updated_by);

		$copyObj->setUPDATED_ON($this->updated_on);


		$copyObj->setNew(true);

		$copyObj->setID(NULL); // this is a pkey column, so set to default value

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
	 * @return     SiteReportsQFREPcd Clone of current object.
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
	 * @return     SiteReportsQFREPcdPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SiteReportsQFREPcdPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a SiteReportsQFR object.
	 *
	 * @param      SiteReportsQFR $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSiteReportsQFR($v)
	{


		if ($v === null) {
			$this->setQFR_ID(NULL);
		} else {
			$this->setQFR_ID($v->getID());
		}


		$this->aSiteReportsQFR = $v;
	}


	/**
	 * Get the associated SiteReportsQFR object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SiteReportsQFR The associated SiteReportsQFR object.
	 * @throws     PropelException
	 */
	public function getSiteReportsQFR($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSiteReportsQFRPeer.php';

		if ($this->aSiteReportsQFR === null && ($this->qfr_id > 0)) {

			$this->aSiteReportsQFR = SiteReportsQFRPeer::retrieveByPK($this->qfr_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SiteReportsQFRPeer::retrieveByPK($this->qfr_id, $con);
			   $obj->addSiteReportsQFRs($this);
			 */
		}
		return $this->aSiteReportsQFR;
	}

} // BaseSiteReportsQFREPcd
