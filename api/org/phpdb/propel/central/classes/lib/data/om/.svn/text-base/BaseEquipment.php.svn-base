<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/EquipmentPeer.php';

/**
 * Base class that represents a row from the 'EQUIPMENT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEquipment extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EquipmentPeer
	 */
	protected static $peer;


	/**
	 * The value for the equipment_id field.
	 * @var        double
	 */
	protected $equipment_id;


	/**
	 * The value for the calibration_information field.
	 * @var        string
	 */
	protected $calibration_information;


	/**
	 * The value for the commission_date field.
	 * @var        int
	 */
	protected $commission_date;


	/**
	 * The value for the deleted field.
	 * @var        double
	 */
	protected $deleted;


	/**
	 * The value for the lab_assigned_id field.
	 * @var        string
	 */
	protected $lab_assigned_id;


	/**
	 * The value for the major field.
	 * @var        double
	 */
	protected $major;


	/**
	 * The value for the model_id field.
	 * @var        double
	 */
	protected $model_id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the nees_operated field.
	 * @var        string
	 */
	protected $nees_operated;


	/**
	 * The value for the note field.
	 * @var        string
	 */
	protected $note;


	/**
	 * The value for the orgid field.
	 * @var        double
	 */
	protected $orgid;


	/**
	 * The value for the owner field.
	 * @var        string
	 */
	protected $owner;


	/**
	 * The value for the parent_id field.
	 * @var        double
	 */
	protected $parent_id;


	/**
	 * The value for the quantity field.
	 * @var        double
	 */
	protected $quantity;


	/**
	 * The value for the separate_scheduling field.
	 * @var        double
	 */
	protected $separate_scheduling;


	/**
	 * The value for the serial_number field.
	 * @var        string
	 */
	protected $serial_number;

	/**
	 * @var        Equipment
	 */
	protected $aEquipmentRelatedByParentId;

	/**
	 * @var        EquipmentModel
	 */
	protected $aEquipmentModel;

	/**
	 * @var        Organization
	 */
	protected $aOrganization;

	/**
	 * Collection to store aggregation of collControllerChannels.
	 * @var        array
	 */
	protected $collControllerChannels;

	/**
	 * The criteria used to select the current contents of collControllerChannels.
	 * @var        Criteria
	 */
	protected $lastControllerChannelCriteria = null;

	/**
	 * Collection to store aggregation of collControllerChannelEquipments.
	 * @var        array
	 */
	protected $collControllerChannelEquipments;

	/**
	 * The criteria used to select the current contents of collControllerChannelEquipments.
	 * @var        Criteria
	 */
	protected $lastControllerChannelEquipmentCriteria = null;

	/**
	 * Collection to store aggregation of collControllerConfigs.
	 * @var        array
	 */
	protected $collControllerConfigs;

	/**
	 * The criteria used to select the current contents of collControllerConfigs.
	 * @var        Criteria
	 */
	protected $lastControllerConfigCriteria = null;

	/**
	 * Collection to store aggregation of collDAQChannelEquipments.
	 * @var        array
	 */
	protected $collDAQChannelEquipments;

	/**
	 * The criteria used to select the current contents of collDAQChannelEquipments.
	 * @var        Criteria
	 */
	protected $lastDAQChannelEquipmentCriteria = null;

	/**
	 * Collection to store aggregation of collDAQConfigs.
	 * @var        array
	 */
	protected $collDAQConfigs;

	/**
	 * The criteria used to select the current contents of collDAQConfigs.
	 * @var        Criteria
	 */
	protected $lastDAQConfigCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentsRelatedByParentId.
	 * @var        array
	 */
	protected $collEquipmentsRelatedByParentId;

	/**
	 * The criteria used to select the current contents of collEquipmentsRelatedByParentId.
	 * @var        Criteria
	 */
	protected $lastEquipmentRelatedByParentIdCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentAttributeValues.
	 * @var        array
	 */
	protected $collEquipmentAttributeValues;

	/**
	 * The criteria used to select the current contents of collEquipmentAttributeValues.
	 * @var        Criteria
	 */
	protected $lastEquipmentAttributeValueCriteria = null;

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
	 * Collection to store aggregation of collExperimentEquipments.
	 * @var        array
	 */
	protected $collExperimentEquipments;

	/**
	 * The criteria used to select the current contents of collExperimentEquipments.
	 * @var        Criteria
	 */
	protected $lastExperimentEquipmentCriteria = null;

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
	 * Get the [equipment_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->equipment_id;
	}

	/**
	 * Get the [calibration_information] column value.
	 * 
	 * @return     string
	 */
	public function getCalibrationInformation()
	{

		return $this->calibration_information;
	}

	/**
	 * Get the [optionally formatted] [commission_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getCommissionDate($format = '%Y-%m-%d')
	{

		if ($this->commission_date === null || $this->commission_date === '') {
			return null;
		} elseif (!is_int($this->commission_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->commission_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [commission_date] as date/time value: " . var_export($this->commission_date, true));
			}
		} else {
			$ts = $this->commission_date;
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
	 * Get the [deleted] column value.
	 * 
	 * @return     double
	 */
	public function getDeleted()
	{

		return $this->deleted;
	}

	/**
	 * Get the [lab_assigned_id] column value.
	 * 
	 * @return     string
	 */
	public function getLabAssignedId()
	{

		return $this->lab_assigned_id;
	}

	/**
	 * Get the [major] column value.
	 * 
	 * @return     double
	 */
	public function getMajor()
	{

		return $this->major;
	}

	/**
	 * Get the [model_id] column value.
	 * 
	 * @return     double
	 */
	public function getModelId()
	{

		return $this->model_id;
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
	 * Get the [nees_operated] column value.
	 * 
	 * @return     string
	 */
	public function getNeesOperated()
	{

		return $this->nees_operated;
	}

	/**
	 * Get the [note] column value.
	 * 
	 * @return     string
	 */
	public function getNote()
	{

		return $this->note;
	}

	/**
	 * Get the [orgid] column value.
	 * 
	 * @return     double
	 */
	public function getOrganizationId()
	{

		return $this->orgid;
	}

	/**
	 * Get the [owner] column value.
	 * 
	 * @return     string
	 */
	public function getOwner()
	{

		return $this->owner;
	}

	/**
	 * Get the [parent_id] column value.
	 * 
	 * @return     double
	 */
	public function getParentId()
	{

		return $this->parent_id;
	}

	/**
	 * Get the [quantity] column value.
	 * 
	 * @return     double
	 */
	public function getQuantity()
	{

		return $this->quantity;
	}

	/**
	 * Get the [separate_scheduling] column value.
	 * 
	 * @return     double
	 */
	public function getSeparateScheduling()
	{

		return $this->separate_scheduling;
	}

	/**
	 * Get the [serial_number] column value.
	 * 
	 * @return     string
	 */
	public function getSerialNumber()
	{

		return $this->serial_number;
	}

	/**
	 * Set the value of [equipment_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->equipment_id !== $v) {
			$this->equipment_id = $v;
			$this->modifiedColumns[] = EquipmentPeer::EQUIPMENT_ID;
		}

	} // setId()

	/**
	 * Set the value of [calibration_information] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCalibrationInformation($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->calibration_information !== $v) {
			$this->calibration_information = $v;
			$this->modifiedColumns[] = EquipmentPeer::CALIBRATION_INFORMATION;
		}

	} // setCalibrationInformation()

	/**
	 * Set the value of [commission_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setCommissionDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [commission_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->commission_date !== $ts) {
			$this->commission_date = $ts;
			$this->modifiedColumns[] = EquipmentPeer::COMMISSION_DATE;
		}

	} // setCommissionDate()

	/**
	 * Set the value of [deleted] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDeleted($v)
	{

		if ($this->deleted !== $v) {
			$this->deleted = $v;
			$this->modifiedColumns[] = EquipmentPeer::DELETED;
		}

	} // setDeleted()

	/**
	 * Set the value of [lab_assigned_id] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setLabAssignedId($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->lab_assigned_id !== $v) {
			$this->lab_assigned_id = $v;
			$this->modifiedColumns[] = EquipmentPeer::LAB_ASSIGNED_ID;
		}

	} // setLabAssignedId()

	/**
	 * Set the value of [major] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setMajor($v)
	{

		if ($this->major !== $v) {
			$this->major = $v;
			$this->modifiedColumns[] = EquipmentPeer::MAJOR;
		}

	} // setMajor()

	/**
	 * Set the value of [model_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setModelId($v)
	{

		if ($this->model_id !== $v) {
			$this->model_id = $v;
			$this->modifiedColumns[] = EquipmentPeer::MODEL_ID;
		}

		if ($this->aEquipmentModel !== null && $this->aEquipmentModel->getId() !== $v) {
			$this->aEquipmentModel = null;
		}

	} // setModelId()

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
			$this->modifiedColumns[] = EquipmentPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [nees_operated] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNeesOperated($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->nees_operated !== $v) {
			$this->nees_operated = $v;
			$this->modifiedColumns[] = EquipmentPeer::NEES_OPERATED;
		}

	} // setNeesOperated()

	/**
	 * Set the value of [note] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNote($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->note) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->note !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->note = $obj;
			$this->modifiedColumns[] = EquipmentPeer::NOTE;
		}

	} // setNote()

	/**
	 * Set the value of [orgid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOrganizationId($v)
	{

		if ($this->orgid !== $v) {
			$this->orgid = $v;
			$this->modifiedColumns[] = EquipmentPeer::ORGID;
		}

		if ($this->aOrganization !== null && $this->aOrganization->getId() !== $v) {
			$this->aOrganization = null;
		}

	} // setOrganizationId()

	/**
	 * Set the value of [owner] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setOwner($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->owner !== $v) {
			$this->owner = $v;
			$this->modifiedColumns[] = EquipmentPeer::OWNER;
		}

	} // setOwner()

	/**
	 * Set the value of [parent_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setParentId($v)
	{

		if ($this->parent_id !== $v) {
			$this->parent_id = $v;
			$this->modifiedColumns[] = EquipmentPeer::PARENT_ID;
		}

		if ($this->aEquipmentRelatedByParentId !== null && $this->aEquipmentRelatedByParentId->getId() !== $v) {
			$this->aEquipmentRelatedByParentId = null;
		}

	} // setParentId()

	/**
	 * Set the value of [quantity] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQuantity($v)
	{

		if ($this->quantity !== $v) {
			$this->quantity = $v;
			$this->modifiedColumns[] = EquipmentPeer::QUANTITY;
		}

	} // setQuantity()

	/**
	 * Set the value of [separate_scheduling] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSeparateScheduling($v)
	{

		if ($this->separate_scheduling !== $v) {
			$this->separate_scheduling = $v;
			$this->modifiedColumns[] = EquipmentPeer::SEPARATE_SCHEDULING;
		}

	} // setSeparateScheduling()

	/**
	 * Set the value of [serial_number] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSerialNumber($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->serial_number !== $v) {
			$this->serial_number = $v;
			$this->modifiedColumns[] = EquipmentPeer::SERIAL_NUMBER;
		}

	} // setSerialNumber()

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

			$this->equipment_id = $rs->getFloat($startcol + 0);

			$this->calibration_information = $rs->getString($startcol + 1);

			$this->commission_date = $rs->getDate($startcol + 2, null);

			$this->deleted = $rs->getFloat($startcol + 3);

			$this->lab_assigned_id = $rs->getString($startcol + 4);

			$this->major = $rs->getFloat($startcol + 5);

			$this->model_id = $rs->getFloat($startcol + 6);

			$this->name = $rs->getString($startcol + 7);

			$this->nees_operated = $rs->getString($startcol + 8);

			$this->note = $rs->getClob($startcol + 9);

			$this->orgid = $rs->getFloat($startcol + 10);

			$this->owner = $rs->getString($startcol + 11);

			$this->parent_id = $rs->getFloat($startcol + 12);

			$this->quantity = $rs->getFloat($startcol + 13);

			$this->separate_scheduling = $rs->getFloat($startcol + 14);

			$this->serial_number = $rs->getString($startcol + 15);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 16; // 16 = EquipmentPeer::NUM_COLUMNS - EquipmentPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Equipment object", $e);
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
			$con = Propel::getConnection(EquipmentPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			EquipmentPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(EquipmentPeer::DATABASE_NAME);
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

			if ($this->aEquipmentRelatedByParentId !== null) {
				if ($this->aEquipmentRelatedByParentId->isModified()) {
					$affectedRows += $this->aEquipmentRelatedByParentId->save($con);
				}
				$this->setEquipmentRelatedByParentId($this->aEquipmentRelatedByParentId);
			}

			if ($this->aEquipmentModel !== null) {
				if ($this->aEquipmentModel->isModified()) {
					$affectedRows += $this->aEquipmentModel->save($con);
				}
				$this->setEquipmentModel($this->aEquipmentModel);
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
					$pk = EquipmentPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += EquipmentPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collControllerChannels !== null) {
				foreach($this->collControllerChannels as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collControllerChannelEquipments !== null) {
				foreach($this->collControllerChannelEquipments as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collControllerConfigs !== null) {
				foreach($this->collControllerConfigs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collDAQChannelEquipments !== null) {
				foreach($this->collDAQChannelEquipments as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collDAQConfigs !== null) {
				foreach($this->collDAQConfigs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentsRelatedByParentId !== null) {
				foreach($this->collEquipmentsRelatedByParentId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentAttributeValues !== null) {
				foreach($this->collEquipmentAttributeValues as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentDocumentations !== null) {
				foreach($this->collEquipmentDocumentations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collExperimentEquipments !== null) {
				foreach($this->collExperimentEquipments as $referrerFK) {
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

			if ($this->aEquipmentRelatedByParentId !== null) {
				if (!$this->aEquipmentRelatedByParentId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEquipmentRelatedByParentId->getValidationFailures());
				}
			}

			if ($this->aEquipmentModel !== null) {
				if (!$this->aEquipmentModel->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEquipmentModel->getValidationFailures());
				}
			}

			if ($this->aOrganization !== null) {
				if (!$this->aOrganization->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aOrganization->getValidationFailures());
				}
			}


			if (($retval = EquipmentPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collControllerChannels !== null) {
					foreach($this->collControllerChannels as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collControllerChannelEquipments !== null) {
					foreach($this->collControllerChannelEquipments as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collControllerConfigs !== null) {
					foreach($this->collControllerConfigs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collDAQChannelEquipments !== null) {
					foreach($this->collDAQChannelEquipments as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collDAQConfigs !== null) {
					foreach($this->collDAQConfigs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentAttributeValues !== null) {
					foreach($this->collEquipmentAttributeValues as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentDocumentations !== null) {
					foreach($this->collEquipmentDocumentations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collExperimentEquipments !== null) {
					foreach($this->collExperimentEquipments as $referrerFK) {
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
		$pos = EquipmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCalibrationInformation();
				break;
			case 2:
				return $this->getCommissionDate();
				break;
			case 3:
				return $this->getDeleted();
				break;
			case 4:
				return $this->getLabAssignedId();
				break;
			case 5:
				return $this->getMajor();
				break;
			case 6:
				return $this->getModelId();
				break;
			case 7:
				return $this->getName();
				break;
			case 8:
				return $this->getNeesOperated();
				break;
			case 9:
				return $this->getNote();
				break;
			case 10:
				return $this->getOrganizationId();
				break;
			case 11:
				return $this->getOwner();
				break;
			case 12:
				return $this->getParentId();
				break;
			case 13:
				return $this->getQuantity();
				break;
			case 14:
				return $this->getSeparateScheduling();
				break;
			case 15:
				return $this->getSerialNumber();
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
		$keys = EquipmentPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCalibrationInformation(),
			$keys[2] => $this->getCommissionDate(),
			$keys[3] => $this->getDeleted(),
			$keys[4] => $this->getLabAssignedId(),
			$keys[5] => $this->getMajor(),
			$keys[6] => $this->getModelId(),
			$keys[7] => $this->getName(),
			$keys[8] => $this->getNeesOperated(),
			$keys[9] => $this->getNote(),
			$keys[10] => $this->getOrganizationId(),
			$keys[11] => $this->getOwner(),
			$keys[12] => $this->getParentId(),
			$keys[13] => $this->getQuantity(),
			$keys[14] => $this->getSeparateScheduling(),
			$keys[15] => $this->getSerialNumber(),
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
		$pos = EquipmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCalibrationInformation($value);
				break;
			case 2:
				$this->setCommissionDate($value);
				break;
			case 3:
				$this->setDeleted($value);
				break;
			case 4:
				$this->setLabAssignedId($value);
				break;
			case 5:
				$this->setMajor($value);
				break;
			case 6:
				$this->setModelId($value);
				break;
			case 7:
				$this->setName($value);
				break;
			case 8:
				$this->setNeesOperated($value);
				break;
			case 9:
				$this->setNote($value);
				break;
			case 10:
				$this->setOrganizationId($value);
				break;
			case 11:
				$this->setOwner($value);
				break;
			case 12:
				$this->setParentId($value);
				break;
			case 13:
				$this->setQuantity($value);
				break;
			case 14:
				$this->setSeparateScheduling($value);
				break;
			case 15:
				$this->setSerialNumber($value);
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
		$keys = EquipmentPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCalibrationInformation($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCommissionDate($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDeleted($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setLabAssignedId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setMajor($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setModelId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setName($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setNeesOperated($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setNote($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setOrganizationId($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setOwner($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setParentId($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setQuantity($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setSeparateScheduling($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setSerialNumber($arr[$keys[15]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EquipmentPeer::DATABASE_NAME);

		if ($this->isColumnModified(EquipmentPeer::EQUIPMENT_ID)) $criteria->add(EquipmentPeer::EQUIPMENT_ID, $this->equipment_id);
		if ($this->isColumnModified(EquipmentPeer::CALIBRATION_INFORMATION)) $criteria->add(EquipmentPeer::CALIBRATION_INFORMATION, $this->calibration_information);
		if ($this->isColumnModified(EquipmentPeer::COMMISSION_DATE)) $criteria->add(EquipmentPeer::COMMISSION_DATE, $this->commission_date);
		if ($this->isColumnModified(EquipmentPeer::DELETED)) $criteria->add(EquipmentPeer::DELETED, $this->deleted);
		if ($this->isColumnModified(EquipmentPeer::LAB_ASSIGNED_ID)) $criteria->add(EquipmentPeer::LAB_ASSIGNED_ID, $this->lab_assigned_id);
		if ($this->isColumnModified(EquipmentPeer::MAJOR)) $criteria->add(EquipmentPeer::MAJOR, $this->major);
		if ($this->isColumnModified(EquipmentPeer::MODEL_ID)) $criteria->add(EquipmentPeer::MODEL_ID, $this->model_id);
		if ($this->isColumnModified(EquipmentPeer::NAME)) $criteria->add(EquipmentPeer::NAME, $this->name);
		if ($this->isColumnModified(EquipmentPeer::NEES_OPERATED)) $criteria->add(EquipmentPeer::NEES_OPERATED, $this->nees_operated);
		if ($this->isColumnModified(EquipmentPeer::NOTE)) $criteria->add(EquipmentPeer::NOTE, $this->note);
		if ($this->isColumnModified(EquipmentPeer::ORGID)) $criteria->add(EquipmentPeer::ORGID, $this->orgid);
		if ($this->isColumnModified(EquipmentPeer::OWNER)) $criteria->add(EquipmentPeer::OWNER, $this->owner);
		if ($this->isColumnModified(EquipmentPeer::PARENT_ID)) $criteria->add(EquipmentPeer::PARENT_ID, $this->parent_id);
		if ($this->isColumnModified(EquipmentPeer::QUANTITY)) $criteria->add(EquipmentPeer::QUANTITY, $this->quantity);
		if ($this->isColumnModified(EquipmentPeer::SEPARATE_SCHEDULING)) $criteria->add(EquipmentPeer::SEPARATE_SCHEDULING, $this->separate_scheduling);
		if ($this->isColumnModified(EquipmentPeer::SERIAL_NUMBER)) $criteria->add(EquipmentPeer::SERIAL_NUMBER, $this->serial_number);

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
		$criteria = new Criteria(EquipmentPeer::DATABASE_NAME);

		$criteria->add(EquipmentPeer::EQUIPMENT_ID, $this->equipment_id);

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
	 * Generic method to set the primary key (equipment_id column).
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
	 * @param      object $copyObj An object of Equipment (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCalibrationInformation($this->calibration_information);

		$copyObj->setCommissionDate($this->commission_date);

		$copyObj->setDeleted($this->deleted);

		$copyObj->setLabAssignedId($this->lab_assigned_id);

		$copyObj->setMajor($this->major);

		$copyObj->setModelId($this->model_id);

		$copyObj->setName($this->name);

		$copyObj->setNeesOperated($this->nees_operated);

		$copyObj->setNote($this->note);

		$copyObj->setOrganizationId($this->orgid);

		$copyObj->setOwner($this->owner);

		$copyObj->setParentId($this->parent_id);

		$copyObj->setQuantity($this->quantity);

		$copyObj->setSeparateScheduling($this->separate_scheduling);

		$copyObj->setSerialNumber($this->serial_number);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getControllerChannels() as $relObj) {
				$copyObj->addControllerChannel($relObj->copy($deepCopy));
			}

			foreach($this->getControllerChannelEquipments() as $relObj) {
				$copyObj->addControllerChannelEquipment($relObj->copy($deepCopy));
			}

			foreach($this->getControllerConfigs() as $relObj) {
				$copyObj->addControllerConfig($relObj->copy($deepCopy));
			}

			foreach($this->getDAQChannelEquipments() as $relObj) {
				$copyObj->addDAQChannelEquipment($relObj->copy($deepCopy));
			}

			foreach($this->getDAQConfigs() as $relObj) {
				$copyObj->addDAQConfig($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentsRelatedByParentId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addEquipmentRelatedByParentId($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentAttributeValues() as $relObj) {
				$copyObj->addEquipmentAttributeValue($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentDocumentations() as $relObj) {
				$copyObj->addEquipmentDocumentation($relObj->copy($deepCopy));
			}

			foreach($this->getExperimentEquipments() as $relObj) {
				$copyObj->addExperimentEquipment($relObj->copy($deepCopy));
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
	 * @return     Equipment Clone of current object.
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
	 * @return     EquipmentPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EquipmentPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Equipment object.
	 *
	 * @param      Equipment $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEquipmentRelatedByParentId($v)
	{


		if ($v === null) {
			$this->setParentId(NULL);
		} else {
			$this->setParentId($v->getId());
		}


		$this->aEquipmentRelatedByParentId = $v;
	}


	/**
	 * Get the associated Equipment object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Equipment The associated Equipment object.
	 * @throws     PropelException
	 */
	public function getEquipmentRelatedByParentId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';

		if ($this->aEquipmentRelatedByParentId === null && ($this->parent_id > 0)) {

			$this->aEquipmentRelatedByParentId = EquipmentPeer::retrieveByPK($this->parent_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EquipmentPeer::retrieveByPK($this->parent_id, $con);
			   $obj->addEquipmentsRelatedByParentId($this);
			 */
		}
		return $this->aEquipmentRelatedByParentId;
	}

	/**
	 * Declares an association between this object and a EquipmentModel object.
	 *
	 * @param      EquipmentModel $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEquipmentModel($v)
	{


		if ($v === null) {
			$this->setModelId(NULL);
		} else {
			$this->setModelId($v->getId());
		}


		$this->aEquipmentModel = $v;
	}


	/**
	 * Get the associated EquipmentModel object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     EquipmentModel The associated EquipmentModel object.
	 * @throws     PropelException
	 */
	public function getEquipmentModel($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';

		if ($this->aEquipmentModel === null && ($this->model_id > 0)) {

			$this->aEquipmentModel = EquipmentModelPeer::retrieveByPK($this->model_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EquipmentModelPeer::retrieveByPK($this->model_id, $con);
			   $obj->addEquipmentModels($this);
			 */
		}
		return $this->aEquipmentModel;
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
			$this->setOrganizationId(NULL);
		} else {
			$this->setOrganizationId($v->getId());
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

		if ($this->aOrganization === null && ($this->orgid > 0)) {

			$this->aOrganization = OrganizationPeer::retrieveByPK($this->orgid, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = OrganizationPeer::retrieveByPK($this->orgid, $con);
			   $obj->addOrganizations($this);
			 */
		}
		return $this->aOrganization;
	}

	/**
	 * Temporary storage of collControllerChannels to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initControllerChannels()
	{
		if ($this->collControllerChannels === null) {
			$this->collControllerChannels = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 * If this Equipment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getControllerChannels($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
			   $this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::EQUIPMENT_ID, $this->getId());

				ControllerChannelPeer::addSelectColumns($criteria);
				$this->collControllerChannels = ControllerChannelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ControllerChannelPeer::EQUIPMENT_ID, $this->getId());

				ControllerChannelPeer::addSelectColumns($criteria);
				if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
					$this->collControllerChannels = ControllerChannelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastControllerChannelCriteria = $criteria;
		return $this->collControllerChannels;
	}

	/**
	 * Returns the number of related ControllerChannels.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countControllerChannels($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ControllerChannelPeer::EQUIPMENT_ID, $this->getId());

		return ControllerChannelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ControllerChannel object to this object
	 * through the ControllerChannel foreign key attribute
	 *
	 * @param      ControllerChannel $l ControllerChannel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addControllerChannel(ControllerChannel $l)
	{
		$this->collControllerChannels[] = $l;
		$l->setEquipment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getControllerChannelsJoinControllerConfig($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
				$this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::EQUIPMENT_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinControllerConfig($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinControllerConfig($criteria, $con);
			}
		}
		$this->lastControllerChannelCriteria = $criteria;

		return $this->collControllerChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getControllerChannelsJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
				$this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::EQUIPMENT_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastControllerChannelCriteria = $criteria;

		return $this->collControllerChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getControllerChannelsJoinLocation($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
				$this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::EQUIPMENT_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinLocation($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinLocation($criteria, $con);
			}
		}
		$this->lastControllerChannelCriteria = $criteria;

		return $this->collControllerChannels;
	}

	/**
	 * Temporary storage of collControllerChannelEquipments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initControllerChannelEquipments()
	{
		if ($this->collControllerChannelEquipments === null) {
			$this->collControllerChannelEquipments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment has previously
	 * been saved, it will retrieve related ControllerChannelEquipments from storage.
	 * If this Equipment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getControllerChannelEquipments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannelEquipments === null) {
			if ($this->isNew()) {
			   $this->collControllerChannelEquipments = array();
			} else {

				$criteria->add(ControllerChannelEquipmentPeer::EQUIPMENT_ID, $this->getId());

				ControllerChannelEquipmentPeer::addSelectColumns($criteria);
				$this->collControllerChannelEquipments = ControllerChannelEquipmentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ControllerChannelEquipmentPeer::EQUIPMENT_ID, $this->getId());

				ControllerChannelEquipmentPeer::addSelectColumns($criteria);
				if (!isset($this->lastControllerChannelEquipmentCriteria) || !$this->lastControllerChannelEquipmentCriteria->equals($criteria)) {
					$this->collControllerChannelEquipments = ControllerChannelEquipmentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastControllerChannelEquipmentCriteria = $criteria;
		return $this->collControllerChannelEquipments;
	}

	/**
	 * Returns the number of related ControllerChannelEquipments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countControllerChannelEquipments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ControllerChannelEquipmentPeer::EQUIPMENT_ID, $this->getId());

		return ControllerChannelEquipmentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ControllerChannelEquipment object to this object
	 * through the ControllerChannelEquipment foreign key attribute
	 *
	 * @param      ControllerChannelEquipment $l ControllerChannelEquipment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addControllerChannelEquipment(ControllerChannelEquipment $l)
	{
		$this->collControllerChannelEquipments[] = $l;
		$l->setEquipment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related ControllerChannelEquipments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getControllerChannelEquipmentsJoinControllerChannel($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannelEquipments === null) {
			if ($this->isNew()) {
				$this->collControllerChannelEquipments = array();
			} else {

				$criteria->add(ControllerChannelEquipmentPeer::EQUIPMENT_ID, $this->getId());

				$this->collControllerChannelEquipments = ControllerChannelEquipmentPeer::doSelectJoinControllerChannel($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelEquipmentPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastControllerChannelEquipmentCriteria) || !$this->lastControllerChannelEquipmentCriteria->equals($criteria)) {
				$this->collControllerChannelEquipments = ControllerChannelEquipmentPeer::doSelectJoinControllerChannel($criteria, $con);
			}
		}
		$this->lastControllerChannelEquipmentCriteria = $criteria;

		return $this->collControllerChannelEquipments;
	}

	/**
	 * Temporary storage of collControllerConfigs to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initControllerConfigs()
	{
		if ($this->collControllerConfigs === null) {
			$this->collControllerConfigs = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 * If this Equipment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getControllerConfigs($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigs === null) {
			if ($this->isNew()) {
			   $this->collControllerConfigs = array();
			} else {

				$criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->getId());

				ControllerConfigPeer::addSelectColumns($criteria);
				$this->collControllerConfigs = ControllerConfigPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->getId());

				ControllerConfigPeer::addSelectColumns($criteria);
				if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
					$this->collControllerConfigs = ControllerConfigPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastControllerConfigCriteria = $criteria;
		return $this->collControllerConfigs;
	}

	/**
	 * Returns the number of related ControllerConfigs.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countControllerConfigs($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->getId());

		return ControllerConfigPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ControllerConfig object to this object
	 * through the ControllerConfig foreign key attribute
	 *
	 * @param      ControllerConfig $l ControllerConfig
	 * @return     void
	 * @throws     PropelException
	 */
	public function addControllerConfig(ControllerConfig $l)
	{
		$this->collControllerConfigs[] = $l;
		$l->setEquipment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getControllerConfigsJoinDataFileRelatedByInputDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigs === null) {
			if ($this->isNew()) {
				$this->collControllerConfigs = array();
			} else {

				$criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinDataFileRelatedByInputDataFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinDataFileRelatedByInputDataFileId($criteria, $con);
			}
		}
		$this->lastControllerConfigCriteria = $criteria;

		return $this->collControllerConfigs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getControllerConfigsJoinDataFileRelatedByConfigDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigs === null) {
			if ($this->isNew()) {
				$this->collControllerConfigs = array();
			} else {

				$criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinDataFileRelatedByConfigDataFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinDataFileRelatedByConfigDataFileId($criteria, $con);
			}
		}
		$this->lastControllerConfigCriteria = $criteria;

		return $this->collControllerConfigs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getControllerConfigsJoinMeasurementUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigs === null) {
			if ($this->isNew()) {
				$this->collControllerConfigs = array();
			} else {

				$criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastControllerConfigCriteria = $criteria;

		return $this->collControllerConfigs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getControllerConfigsJoinTrial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigs === null) {
			if ($this->isNew()) {
				$this->collControllerConfigs = array();
			} else {

				$criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastControllerConfigCriteria = $criteria;

		return $this->collControllerConfigs;
	}

	/**
	 * Temporary storage of collDAQChannelEquipments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDAQChannelEquipments()
	{
		if ($this->collDAQChannelEquipments === null) {
			$this->collDAQChannelEquipments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment has previously
	 * been saved, it will retrieve related DAQChannelEquipments from storage.
	 * If this Equipment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDAQChannelEquipments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannelEquipments === null) {
			if ($this->isNew()) {
			   $this->collDAQChannelEquipments = array();
			} else {

				$criteria->add(DAQChannelEquipmentPeer::EQUIPMENT_ID, $this->getId());

				DAQChannelEquipmentPeer::addSelectColumns($criteria);
				$this->collDAQChannelEquipments = DAQChannelEquipmentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DAQChannelEquipmentPeer::EQUIPMENT_ID, $this->getId());

				DAQChannelEquipmentPeer::addSelectColumns($criteria);
				if (!isset($this->lastDAQChannelEquipmentCriteria) || !$this->lastDAQChannelEquipmentCriteria->equals($criteria)) {
					$this->collDAQChannelEquipments = DAQChannelEquipmentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDAQChannelEquipmentCriteria = $criteria;
		return $this->collDAQChannelEquipments;
	}

	/**
	 * Returns the number of related DAQChannelEquipments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDAQChannelEquipments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DAQChannelEquipmentPeer::EQUIPMENT_ID, $this->getId());

		return DAQChannelEquipmentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DAQChannelEquipment object to this object
	 * through the DAQChannelEquipment foreign key attribute
	 *
	 * @param      DAQChannelEquipment $l DAQChannelEquipment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDAQChannelEquipment(DAQChannelEquipment $l)
	{
		$this->collDAQChannelEquipments[] = $l;
		$l->setEquipment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related DAQChannelEquipments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getDAQChannelEquipmentsJoinDAQChannel($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannelEquipments === null) {
			if ($this->isNew()) {
				$this->collDAQChannelEquipments = array();
			} else {

				$criteria->add(DAQChannelEquipmentPeer::EQUIPMENT_ID, $this->getId());

				$this->collDAQChannelEquipments = DAQChannelEquipmentPeer::doSelectJoinDAQChannel($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQChannelEquipmentPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastDAQChannelEquipmentCriteria) || !$this->lastDAQChannelEquipmentCriteria->equals($criteria)) {
				$this->collDAQChannelEquipments = DAQChannelEquipmentPeer::doSelectJoinDAQChannel($criteria, $con);
			}
		}
		$this->lastDAQChannelEquipmentCriteria = $criteria;

		return $this->collDAQChannelEquipments;
	}

	/**
	 * Temporary storage of collDAQConfigs to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDAQConfigs()
	{
		if ($this->collDAQConfigs === null) {
			$this->collDAQConfigs = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment has previously
	 * been saved, it will retrieve related DAQConfigs from storage.
	 * If this Equipment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDAQConfigs($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigs === null) {
			if ($this->isNew()) {
			   $this->collDAQConfigs = array();
			} else {

				$criteria->add(DAQConfigPeer::EQUIPMENT_ID, $this->getId());

				DAQConfigPeer::addSelectColumns($criteria);
				$this->collDAQConfigs = DAQConfigPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DAQConfigPeer::EQUIPMENT_ID, $this->getId());

				DAQConfigPeer::addSelectColumns($criteria);
				if (!isset($this->lastDAQConfigCriteria) || !$this->lastDAQConfigCriteria->equals($criteria)) {
					$this->collDAQConfigs = DAQConfigPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDAQConfigCriteria = $criteria;
		return $this->collDAQConfigs;
	}

	/**
	 * Returns the number of related DAQConfigs.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDAQConfigs($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DAQConfigPeer::EQUIPMENT_ID, $this->getId());

		return DAQConfigPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DAQConfig object to this object
	 * through the DAQConfig foreign key attribute
	 *
	 * @param      DAQConfig $l DAQConfig
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDAQConfig(DAQConfig $l)
	{
		$this->collDAQConfigs[] = $l;
		$l->setEquipment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related DAQConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getDAQConfigsJoinDataFileRelatedByOutputDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigs === null) {
			if ($this->isNew()) {
				$this->collDAQConfigs = array();
			} else {

				$criteria->add(DAQConfigPeer::EQUIPMENT_ID, $this->getId());

				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinDataFileRelatedByOutputDataFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQConfigPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastDAQConfigCriteria) || !$this->lastDAQConfigCriteria->equals($criteria)) {
				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinDataFileRelatedByOutputDataFileId($criteria, $con);
			}
		}
		$this->lastDAQConfigCriteria = $criteria;

		return $this->collDAQConfigs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related DAQConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getDAQConfigsJoinDataFileRelatedByConfigDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigs === null) {
			if ($this->isNew()) {
				$this->collDAQConfigs = array();
			} else {

				$criteria->add(DAQConfigPeer::EQUIPMENT_ID, $this->getId());

				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinDataFileRelatedByConfigDataFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQConfigPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastDAQConfigCriteria) || !$this->lastDAQConfigCriteria->equals($criteria)) {
				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinDataFileRelatedByConfigDataFileId($criteria, $con);
			}
		}
		$this->lastDAQConfigCriteria = $criteria;

		return $this->collDAQConfigs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related DAQConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getDAQConfigsJoinTrial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigs === null) {
			if ($this->isNew()) {
				$this->collDAQConfigs = array();
			} else {

				$criteria->add(DAQConfigPeer::EQUIPMENT_ID, $this->getId());

				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQConfigPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastDAQConfigCriteria) || !$this->lastDAQConfigCriteria->equals($criteria)) {
				$this->collDAQConfigs = DAQConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastDAQConfigCriteria = $criteria;

		return $this->collDAQConfigs;
	}

	/**
	 * Temporary storage of collEquipmentsRelatedByParentId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentsRelatedByParentId()
	{
		if ($this->collEquipmentsRelatedByParentId === null) {
			$this->collEquipmentsRelatedByParentId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment has previously
	 * been saved, it will retrieve related EquipmentsRelatedByParentId from storage.
	 * If this Equipment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentsRelatedByParentId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentsRelatedByParentId === null) {
			if ($this->isNew()) {
			   $this->collEquipmentsRelatedByParentId = array();
			} else {

				$criteria->add(EquipmentPeer::PARENT_ID, $this->getId());

				EquipmentPeer::addSelectColumns($criteria);
				$this->collEquipmentsRelatedByParentId = EquipmentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentPeer::PARENT_ID, $this->getId());

				EquipmentPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentRelatedByParentIdCriteria) || !$this->lastEquipmentRelatedByParentIdCriteria->equals($criteria)) {
					$this->collEquipmentsRelatedByParentId = EquipmentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentRelatedByParentIdCriteria = $criteria;
		return $this->collEquipmentsRelatedByParentId;
	}

	/**
	 * Returns the number of related EquipmentsRelatedByParentId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentsRelatedByParentId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentPeer::PARENT_ID, $this->getId());

		return EquipmentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Equipment object to this object
	 * through the Equipment foreign key attribute
	 *
	 * @param      Equipment $l Equipment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentRelatedByParentId(Equipment $l)
	{
		$this->collEquipmentsRelatedByParentId[] = $l;
		$l->setEquipmentRelatedByParentId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related EquipmentsRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getEquipmentsRelatedByParentIdJoinEquipmentModel($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentsRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collEquipmentsRelatedByParentId = array();
			} else {

				$criteria->add(EquipmentPeer::PARENT_ID, $this->getId());

				$this->collEquipmentsRelatedByParentId = EquipmentPeer::doSelectJoinEquipmentModel($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentPeer::PARENT_ID, $this->getId());

			if (!isset($this->lastEquipmentRelatedByParentIdCriteria) || !$this->lastEquipmentRelatedByParentIdCriteria->equals($criteria)) {
				$this->collEquipmentsRelatedByParentId = EquipmentPeer::doSelectJoinEquipmentModel($criteria, $con);
			}
		}
		$this->lastEquipmentRelatedByParentIdCriteria = $criteria;

		return $this->collEquipmentsRelatedByParentId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related EquipmentsRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getEquipmentsRelatedByParentIdJoinOrganization($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentsRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collEquipmentsRelatedByParentId = array();
			} else {

				$criteria->add(EquipmentPeer::PARENT_ID, $this->getId());

				$this->collEquipmentsRelatedByParentId = EquipmentPeer::doSelectJoinOrganization($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentPeer::PARENT_ID, $this->getId());

			if (!isset($this->lastEquipmentRelatedByParentIdCriteria) || !$this->lastEquipmentRelatedByParentIdCriteria->equals($criteria)) {
				$this->collEquipmentsRelatedByParentId = EquipmentPeer::doSelectJoinOrganization($criteria, $con);
			}
		}
		$this->lastEquipmentRelatedByParentIdCriteria = $criteria;

		return $this->collEquipmentsRelatedByParentId;
	}

	/**
	 * Temporary storage of collEquipmentAttributeValues to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentAttributeValues()
	{
		if ($this->collEquipmentAttributeValues === null) {
			$this->collEquipmentAttributeValues = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 * If this Equipment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentAttributeValues($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
			   $this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ID, $this->getId());

				EquipmentAttributeValuePeer::addSelectColumns($criteria);
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ID, $this->getId());

				EquipmentAttributeValuePeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
					$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;
		return $this->collEquipmentAttributeValues;
	}

	/**
	 * Returns the number of related EquipmentAttributeValues.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentAttributeValues($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ID, $this->getId());

		return EquipmentAttributeValuePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentAttributeValue object to this object
	 * through the EquipmentAttributeValue foreign key attribute
	 *
	 * @param      EquipmentAttributeValue $l EquipmentAttributeValue
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentAttributeValue(EquipmentAttributeValue $l)
	{
		$this->collEquipmentAttributeValues[] = $l;
		$l->setEquipment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getEquipmentAttributeValuesJoinEquipmentAttribute($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttribute($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttribute($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getEquipmentAttributeValuesJoinEquipmentAttributeClass($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttributeClass($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinEquipmentAttributeClass($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related EquipmentAttributeValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getEquipmentAttributeValuesJoinUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentAttributeValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentAttributeValues === null) {
			if ($this->isNew()) {
				$this->collEquipmentAttributeValues = array();
			} else {

				$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ID, $this->getId());

				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentAttributeValuePeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastEquipmentAttributeValueCriteria) || !$this->lastEquipmentAttributeValueCriteria->equals($criteria)) {
				$this->collEquipmentAttributeValues = EquipmentAttributeValuePeer::doSelectJoinUnit($criteria, $con);
			}
		}
		$this->lastEquipmentAttributeValueCriteria = $criteria;

		return $this->collEquipmentAttributeValues;
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
	 * Otherwise if this Equipment has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 * If this Equipment is new, it will return
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

				$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_ID, $this->getId());

				EquipmentDocumentationPeer::addSelectColumns($criteria);
				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_ID, $this->getId());

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

		$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_ID, $this->getId());

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
		$l->setEquipment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
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

				$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_ID, $this->getId());

				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_ID, $this->getId());

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
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
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

				$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_ID, $this->getId());

				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_ID, $this->getId());

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
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getEquipmentDocumentationsJoinDocumentType($criteria = null, $con = null)
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

				$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_ID, $this->getId());

				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDocumentType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastEquipmentDocumentationCriteria) || !$this->lastEquipmentDocumentationCriteria->equals($criteria)) {
				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDocumentType($criteria, $con);
			}
		}
		$this->lastEquipmentDocumentationCriteria = $criteria;

		return $this->collEquipmentDocumentations;
	}

	/**
	 * Temporary storage of collExperimentEquipments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initExperimentEquipments()
	{
		if ($this->collExperimentEquipments === null) {
			$this->collExperimentEquipments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment has previously
	 * been saved, it will retrieve related ExperimentEquipments from storage.
	 * If this Equipment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getExperimentEquipments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentEquipments === null) {
			if ($this->isNew()) {
			   $this->collExperimentEquipments = array();
			} else {

				$criteria->add(ExperimentEquipmentPeer::EQUIPMENT_ID, $this->getId());

				ExperimentEquipmentPeer::addSelectColumns($criteria);
				$this->collExperimentEquipments = ExperimentEquipmentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentEquipmentPeer::EQUIPMENT_ID, $this->getId());

				ExperimentEquipmentPeer::addSelectColumns($criteria);
				if (!isset($this->lastExperimentEquipmentCriteria) || !$this->lastExperimentEquipmentCriteria->equals($criteria)) {
					$this->collExperimentEquipments = ExperimentEquipmentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastExperimentEquipmentCriteria = $criteria;
		return $this->collExperimentEquipments;
	}

	/**
	 * Returns the number of related ExperimentEquipments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countExperimentEquipments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ExperimentEquipmentPeer::EQUIPMENT_ID, $this->getId());

		return ExperimentEquipmentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ExperimentEquipment object to this object
	 * through the ExperimentEquipment foreign key attribute
	 *
	 * @param      ExperimentEquipment $l ExperimentEquipment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addExperimentEquipment(ExperimentEquipment $l)
	{
		$this->collExperimentEquipments[] = $l;
		$l->setEquipment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Equipment is new, it will return
	 * an empty collection; or if this Equipment has previously
	 * been saved, it will retrieve related ExperimentEquipments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Equipment.
	 */
	public function getExperimentEquipmentsJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentEquipments === null) {
			if ($this->isNew()) {
				$this->collExperimentEquipments = array();
			} else {

				$criteria->add(ExperimentEquipmentPeer::EQUIPMENT_ID, $this->getId());

				$this->collExperimentEquipments = ExperimentEquipmentPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentEquipmentPeer::EQUIPMENT_ID, $this->getId());

			if (!isset($this->lastExperimentEquipmentCriteria) || !$this->lastExperimentEquipmentCriteria->equals($criteria)) {
				$this->collExperimentEquipments = ExperimentEquipmentPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastExperimentEquipmentCriteria = $criteria;

		return $this->collExperimentEquipments;
	}

} // BaseEquipment
