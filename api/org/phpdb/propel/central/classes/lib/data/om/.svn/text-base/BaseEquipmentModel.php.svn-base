<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/EquipmentModelPeer.php';

/**
 * Base class that represents a row from the 'EQUIPMENT_MODEL' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEquipmentModel extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EquipmentModelPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the additional_spec_file_id field.
	 * @var        double
	 */
	protected $additional_spec_file_id;


	/**
	 * The value for the additional_spec_page_count field.
	 * @var        double
	 */
	protected $additional_spec_page_count;


	/**
	 * The value for the design_consideration_file_id field.
	 * @var        double
	 */
	protected $design_consideration_file_id;


	/**
	 * The value for the design_page_count field.
	 * @var        double
	 */
	protected $design_page_count;


	/**
	 * The value for the equipment_class_id field.
	 * @var        double
	 */
	protected $equipment_class_id;


	/**
	 * The value for the interface_doc_file_id field.
	 * @var        double
	 */
	protected $interface_doc_file_id;


	/**
	 * The value for the interface_doc_page_count field.
	 * @var        double
	 */
	protected $interface_doc_page_count;


	/**
	 * The value for the manufacturer field.
	 * @var        string
	 */
	protected $manufacturer;


	/**
	 * The value for the manufacturer_doc_file_id field.
	 * @var        double
	 */
	protected $manufacturer_doc_file_id;


	/**
	 * The value for the manufacturer_doc_page_count field.
	 * @var        double
	 */
	protected $manufacturer_doc_page_count;


	/**
	 * The value for the model_number field.
	 * @var        string
	 */
	protected $model_number;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the subcomponents_doc_file_id field.
	 * @var        double
	 */
	protected $subcomponents_doc_file_id;


	/**
	 * The value for the subcomponents_doc_page_count field.
	 * @var        double
	 */
	protected $subcomponents_doc_page_count;


	/**
	 * The value for the supplier field.
	 * @var        string
	 */
	protected $supplier;

	/**
	 * @var        DataFile
	 */
	protected $aDataFileRelatedByAdditionalSpecFileId;

	/**
	 * @var        DataFile
	 */
	protected $aDataFileRelatedByInterfaceDocFileId;

	/**
	 * @var        DataFile
	 */
	protected $aDataFileRelatedByManufacturerDocFileId;

	/**
	 * @var        DataFile
	 */
	protected $aDataFileRelatedBySubcomponentsDocFileId;

	/**
	 * @var        DataFile
	 */
	protected $aDataFileRelatedByDesignConsiderationFileId;

	/**
	 * @var        EquipmentClass
	 */
	protected $aEquipmentClass;

	/**
	 * Collection to store aggregation of collEquipments.
	 * @var        array
	 */
	protected $collEquipments;

	/**
	 * The criteria used to select the current contents of collEquipments.
	 * @var        Criteria
	 */
	protected $lastEquipmentCriteria = null;

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
	 * Get the [additional_spec_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getAdditionalSpecFileId()
	{

		return $this->additional_spec_file_id;
	}

	/**
	 * Get the [additional_spec_page_count] column value.
	 * 
	 * @return     double
	 */
	public function getAdditionalSpecPageCount()
	{

		return $this->additional_spec_page_count;
	}

	/**
	 * Get the [design_consideration_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getDesignConsiderationFileId()
	{

		return $this->design_consideration_file_id;
	}

	/**
	 * Get the [design_page_count] column value.
	 * 
	 * @return     double
	 */
	public function getDesignConsiderationPageCount()
	{

		return $this->design_page_count;
	}

	/**
	 * Get the [equipment_class_id] column value.
	 * 
	 * @return     double
	 */
	public function getEquipmentClassId()
	{

		return $this->equipment_class_id;
	}

	/**
	 * Get the [interface_doc_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getInterfaceDocFileId()
	{

		return $this->interface_doc_file_id;
	}

	/**
	 * Get the [interface_doc_page_count] column value.
	 * 
	 * @return     double
	 */
	public function getInterfaceDocPageCount()
	{

		return $this->interface_doc_page_count;
	}

	/**
	 * Get the [manufacturer] column value.
	 * 
	 * @return     string
	 */
	public function getManufacturer()
	{

		return $this->manufacturer;
	}

	/**
	 * Get the [manufacturer_doc_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getManufacturerDocFileId()
	{

		return $this->manufacturer_doc_file_id;
	}

	/**
	 * Get the [manufacturer_doc_page_count] column value.
	 * 
	 * @return     double
	 */
	public function getManufacturerDocPageCount()
	{

		return $this->manufacturer_doc_page_count;
	}

	/**
	 * Get the [model_number] column value.
	 * 
	 * @return     string
	 */
	public function getModelNumber()
	{

		return $this->model_number;
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
	 * Get the [subcomponents_doc_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getSubcomponentsDocFileId()
	{

		return $this->subcomponents_doc_file_id;
	}

	/**
	 * Get the [subcomponents_doc_page_count] column value.
	 * 
	 * @return     double
	 */
	public function getSubcomponentsDocPageCount()
	{

		return $this->subcomponents_doc_page_count;
	}

	/**
	 * Get the [supplier] column value.
	 * 
	 * @return     string
	 */
	public function getSupplier()
	{

		return $this->supplier;
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
			$this->modifiedColumns[] = EquipmentModelPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [additional_spec_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAdditionalSpecFileId($v)
	{

		if ($this->additional_spec_file_id !== $v) {
			$this->additional_spec_file_id = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID;
		}

		if ($this->aDataFileRelatedByAdditionalSpecFileId !== null && $this->aDataFileRelatedByAdditionalSpecFileId->getId() !== $v) {
			$this->aDataFileRelatedByAdditionalSpecFileId = null;
		}

	} // setAdditionalSpecFileId()

	/**
	 * Set the value of [additional_spec_page_count] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAdditionalSpecPageCount($v)
	{

		if ($this->additional_spec_page_count !== $v) {
			$this->additional_spec_page_count = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::ADDITIONAL_SPEC_PAGE_COUNT;
		}

	} // setAdditionalSpecPageCount()

	/**
	 * Set the value of [design_consideration_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDesignConsiderationFileId($v)
	{

		if ($this->design_consideration_file_id !== $v) {
			$this->design_consideration_file_id = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID;
		}

		if ($this->aDataFileRelatedByDesignConsiderationFileId !== null && $this->aDataFileRelatedByDesignConsiderationFileId->getId() !== $v) {
			$this->aDataFileRelatedByDesignConsiderationFileId = null;
		}

	} // setDesignConsiderationFileId()

	/**
	 * Set the value of [design_page_count] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDesignConsiderationPageCount($v)
	{

		if ($this->design_page_count !== $v) {
			$this->design_page_count = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::DESIGN_PAGE_COUNT;
		}

	} // setDesignConsiderationPageCount()

	/**
	 * Set the value of [equipment_class_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEquipmentClassId($v)
	{

		if ($this->equipment_class_id !== $v) {
			$this->equipment_class_id = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::EQUIPMENT_CLASS_ID;
		}

		if ($this->aEquipmentClass !== null && $this->aEquipmentClass->getId() !== $v) {
			$this->aEquipmentClass = null;
		}

	} // setEquipmentClassId()

	/**
	 * Set the value of [interface_doc_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setInterfaceDocFileId($v)
	{

		if ($this->interface_doc_file_id !== $v) {
			$this->interface_doc_file_id = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::INTERFACE_DOC_FILE_ID;
		}

		if ($this->aDataFileRelatedByInterfaceDocFileId !== null && $this->aDataFileRelatedByInterfaceDocFileId->getId() !== $v) {
			$this->aDataFileRelatedByInterfaceDocFileId = null;
		}

	} // setInterfaceDocFileId()

	/**
	 * Set the value of [interface_doc_page_count] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setInterfaceDocPageCount($v)
	{

		if ($this->interface_doc_page_count !== $v) {
			$this->interface_doc_page_count = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::INTERFACE_DOC_PAGE_COUNT;
		}

	} // setInterfaceDocPageCount()

	/**
	 * Set the value of [manufacturer] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setManufacturer($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->manufacturer !== $v) {
			$this->manufacturer = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::MANUFACTURER;
		}

	} // setManufacturer()

	/**
	 * Set the value of [manufacturer_doc_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setManufacturerDocFileId($v)
	{

		if ($this->manufacturer_doc_file_id !== $v) {
			$this->manufacturer_doc_file_id = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID;
		}

		if ($this->aDataFileRelatedByManufacturerDocFileId !== null && $this->aDataFileRelatedByManufacturerDocFileId->getId() !== $v) {
			$this->aDataFileRelatedByManufacturerDocFileId = null;
		}

	} // setManufacturerDocFileId()

	/**
	 * Set the value of [manufacturer_doc_page_count] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setManufacturerDocPageCount($v)
	{

		if ($this->manufacturer_doc_page_count !== $v) {
			$this->manufacturer_doc_page_count = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::MANUFACTURER_DOC_PAGE_COUNT;
		}

	} // setManufacturerDocPageCount()

	/**
	 * Set the value of [model_number] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setModelNumber($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->model_number !== $v) {
			$this->model_number = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::MODEL_NUMBER;
		}

	} // setModelNumber()

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
			$this->modifiedColumns[] = EquipmentModelPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [subcomponents_doc_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSubcomponentsDocFileId($v)
	{

		if ($this->subcomponents_doc_file_id !== $v) {
			$this->subcomponents_doc_file_id = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID;
		}

		if ($this->aDataFileRelatedBySubcomponentsDocFileId !== null && $this->aDataFileRelatedBySubcomponentsDocFileId->getId() !== $v) {
			$this->aDataFileRelatedBySubcomponentsDocFileId = null;
		}

	} // setSubcomponentsDocFileId()

	/**
	 * Set the value of [subcomponents_doc_page_count] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSubcomponentsDocPageCount($v)
	{

		if ($this->subcomponents_doc_page_count !== $v) {
			$this->subcomponents_doc_page_count = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::SUBCOMPONENTS_DOC_PAGE_COUNT;
		}

	} // setSubcomponentsDocPageCount()

	/**
	 * Set the value of [supplier] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSupplier($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->supplier !== $v) {
			$this->supplier = $v;
			$this->modifiedColumns[] = EquipmentModelPeer::SUPPLIER;
		}

	} // setSupplier()

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

			$this->additional_spec_file_id = $rs->getFloat($startcol + 1);

			$this->additional_spec_page_count = $rs->getFloat($startcol + 2);

			$this->design_consideration_file_id = $rs->getFloat($startcol + 3);

			$this->design_page_count = $rs->getFloat($startcol + 4);

			$this->equipment_class_id = $rs->getFloat($startcol + 5);

			$this->interface_doc_file_id = $rs->getFloat($startcol + 6);

			$this->interface_doc_page_count = $rs->getFloat($startcol + 7);

			$this->manufacturer = $rs->getString($startcol + 8);

			$this->manufacturer_doc_file_id = $rs->getFloat($startcol + 9);

			$this->manufacturer_doc_page_count = $rs->getFloat($startcol + 10);

			$this->model_number = $rs->getString($startcol + 11);

			$this->name = $rs->getString($startcol + 12);

			$this->subcomponents_doc_file_id = $rs->getFloat($startcol + 13);

			$this->subcomponents_doc_page_count = $rs->getFloat($startcol + 14);

			$this->supplier = $rs->getString($startcol + 15);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 16; // 16 = EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating EquipmentModel object", $e);
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
			$con = Propel::getConnection(EquipmentModelPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			EquipmentModelPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(EquipmentModelPeer::DATABASE_NAME);
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

			if ($this->aDataFileRelatedByAdditionalSpecFileId !== null) {
				if ($this->aDataFileRelatedByAdditionalSpecFileId->isModified()) {
					$affectedRows += $this->aDataFileRelatedByAdditionalSpecFileId->save($con);
				}
				$this->setDataFileRelatedByAdditionalSpecFileId($this->aDataFileRelatedByAdditionalSpecFileId);
			}

			if ($this->aDataFileRelatedByInterfaceDocFileId !== null) {
				if ($this->aDataFileRelatedByInterfaceDocFileId->isModified()) {
					$affectedRows += $this->aDataFileRelatedByInterfaceDocFileId->save($con);
				}
				$this->setDataFileRelatedByInterfaceDocFileId($this->aDataFileRelatedByInterfaceDocFileId);
			}

			if ($this->aDataFileRelatedByManufacturerDocFileId !== null) {
				if ($this->aDataFileRelatedByManufacturerDocFileId->isModified()) {
					$affectedRows += $this->aDataFileRelatedByManufacturerDocFileId->save($con);
				}
				$this->setDataFileRelatedByManufacturerDocFileId($this->aDataFileRelatedByManufacturerDocFileId);
			}

			if ($this->aDataFileRelatedBySubcomponentsDocFileId !== null) {
				if ($this->aDataFileRelatedBySubcomponentsDocFileId->isModified()) {
					$affectedRows += $this->aDataFileRelatedBySubcomponentsDocFileId->save($con);
				}
				$this->setDataFileRelatedBySubcomponentsDocFileId($this->aDataFileRelatedBySubcomponentsDocFileId);
			}

			if ($this->aDataFileRelatedByDesignConsiderationFileId !== null) {
				if ($this->aDataFileRelatedByDesignConsiderationFileId->isModified()) {
					$affectedRows += $this->aDataFileRelatedByDesignConsiderationFileId->save($con);
				}
				$this->setDataFileRelatedByDesignConsiderationFileId($this->aDataFileRelatedByDesignConsiderationFileId);
			}

			if ($this->aEquipmentClass !== null) {
				if ($this->aEquipmentClass->isModified()) {
					$affectedRows += $this->aEquipmentClass->save($con);
				}
				$this->setEquipmentClass($this->aEquipmentClass);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = EquipmentModelPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += EquipmentModelPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collEquipments !== null) {
				foreach($this->collEquipments as $referrerFK) {
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

			if ($this->aDataFileRelatedByAdditionalSpecFileId !== null) {
				if (!$this->aDataFileRelatedByAdditionalSpecFileId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFileRelatedByAdditionalSpecFileId->getValidationFailures());
				}
			}

			if ($this->aDataFileRelatedByInterfaceDocFileId !== null) {
				if (!$this->aDataFileRelatedByInterfaceDocFileId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFileRelatedByInterfaceDocFileId->getValidationFailures());
				}
			}

			if ($this->aDataFileRelatedByManufacturerDocFileId !== null) {
				if (!$this->aDataFileRelatedByManufacturerDocFileId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFileRelatedByManufacturerDocFileId->getValidationFailures());
				}
			}

			if ($this->aDataFileRelatedBySubcomponentsDocFileId !== null) {
				if (!$this->aDataFileRelatedBySubcomponentsDocFileId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFileRelatedBySubcomponentsDocFileId->getValidationFailures());
				}
			}

			if ($this->aDataFileRelatedByDesignConsiderationFileId !== null) {
				if (!$this->aDataFileRelatedByDesignConsiderationFileId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFileRelatedByDesignConsiderationFileId->getValidationFailures());
				}
			}

			if ($this->aEquipmentClass !== null) {
				if (!$this->aEquipmentClass->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEquipmentClass->getValidationFailures());
				}
			}


			if (($retval = EquipmentModelPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collEquipments !== null) {
					foreach($this->collEquipments as $referrerFK) {
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
		$pos = EquipmentModelPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAdditionalSpecFileId();
				break;
			case 2:
				return $this->getAdditionalSpecPageCount();
				break;
			case 3:
				return $this->getDesignConsiderationFileId();
				break;
			case 4:
				return $this->getDesignConsiderationPageCount();
				break;
			case 5:
				return $this->getEquipmentClassId();
				break;
			case 6:
				return $this->getInterfaceDocFileId();
				break;
			case 7:
				return $this->getInterfaceDocPageCount();
				break;
			case 8:
				return $this->getManufacturer();
				break;
			case 9:
				return $this->getManufacturerDocFileId();
				break;
			case 10:
				return $this->getManufacturerDocPageCount();
				break;
			case 11:
				return $this->getModelNumber();
				break;
			case 12:
				return $this->getName();
				break;
			case 13:
				return $this->getSubcomponentsDocFileId();
				break;
			case 14:
				return $this->getSubcomponentsDocPageCount();
				break;
			case 15:
				return $this->getSupplier();
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
		$keys = EquipmentModelPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getAdditionalSpecFileId(),
			$keys[2] => $this->getAdditionalSpecPageCount(),
			$keys[3] => $this->getDesignConsiderationFileId(),
			$keys[4] => $this->getDesignConsiderationPageCount(),
			$keys[5] => $this->getEquipmentClassId(),
			$keys[6] => $this->getInterfaceDocFileId(),
			$keys[7] => $this->getInterfaceDocPageCount(),
			$keys[8] => $this->getManufacturer(),
			$keys[9] => $this->getManufacturerDocFileId(),
			$keys[10] => $this->getManufacturerDocPageCount(),
			$keys[11] => $this->getModelNumber(),
			$keys[12] => $this->getName(),
			$keys[13] => $this->getSubcomponentsDocFileId(),
			$keys[14] => $this->getSubcomponentsDocPageCount(),
			$keys[15] => $this->getSupplier(),
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
		$pos = EquipmentModelPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAdditionalSpecFileId($value);
				break;
			case 2:
				$this->setAdditionalSpecPageCount($value);
				break;
			case 3:
				$this->setDesignConsiderationFileId($value);
				break;
			case 4:
				$this->setDesignConsiderationPageCount($value);
				break;
			case 5:
				$this->setEquipmentClassId($value);
				break;
			case 6:
				$this->setInterfaceDocFileId($value);
				break;
			case 7:
				$this->setInterfaceDocPageCount($value);
				break;
			case 8:
				$this->setManufacturer($value);
				break;
			case 9:
				$this->setManufacturerDocFileId($value);
				break;
			case 10:
				$this->setManufacturerDocPageCount($value);
				break;
			case 11:
				$this->setModelNumber($value);
				break;
			case 12:
				$this->setName($value);
				break;
			case 13:
				$this->setSubcomponentsDocFileId($value);
				break;
			case 14:
				$this->setSubcomponentsDocPageCount($value);
				break;
			case 15:
				$this->setSupplier($value);
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
		$keys = EquipmentModelPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAdditionalSpecFileId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAdditionalSpecPageCount($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDesignConsiderationFileId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDesignConsiderationPageCount($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setEquipmentClassId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setInterfaceDocFileId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setInterfaceDocPageCount($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setManufacturer($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setManufacturerDocFileId($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setManufacturerDocPageCount($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setModelNumber($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setName($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setSubcomponentsDocFileId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setSubcomponentsDocPageCount($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setSupplier($arr[$keys[15]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EquipmentModelPeer::DATABASE_NAME);

		if ($this->isColumnModified(EquipmentModelPeer::ID)) $criteria->add(EquipmentModelPeer::ID, $this->id);
		if ($this->isColumnModified(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID)) $criteria->add(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, $this->additional_spec_file_id);
		if ($this->isColumnModified(EquipmentModelPeer::ADDITIONAL_SPEC_PAGE_COUNT)) $criteria->add(EquipmentModelPeer::ADDITIONAL_SPEC_PAGE_COUNT, $this->additional_spec_page_count);
		if ($this->isColumnModified(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID)) $criteria->add(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, $this->design_consideration_file_id);
		if ($this->isColumnModified(EquipmentModelPeer::DESIGN_PAGE_COUNT)) $criteria->add(EquipmentModelPeer::DESIGN_PAGE_COUNT, $this->design_page_count);
		if ($this->isColumnModified(EquipmentModelPeer::EQUIPMENT_CLASS_ID)) $criteria->add(EquipmentModelPeer::EQUIPMENT_CLASS_ID, $this->equipment_class_id);
		if ($this->isColumnModified(EquipmentModelPeer::INTERFACE_DOC_FILE_ID)) $criteria->add(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, $this->interface_doc_file_id);
		if ($this->isColumnModified(EquipmentModelPeer::INTERFACE_DOC_PAGE_COUNT)) $criteria->add(EquipmentModelPeer::INTERFACE_DOC_PAGE_COUNT, $this->interface_doc_page_count);
		if ($this->isColumnModified(EquipmentModelPeer::MANUFACTURER)) $criteria->add(EquipmentModelPeer::MANUFACTURER, $this->manufacturer);
		if ($this->isColumnModified(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID)) $criteria->add(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, $this->manufacturer_doc_file_id);
		if ($this->isColumnModified(EquipmentModelPeer::MANUFACTURER_DOC_PAGE_COUNT)) $criteria->add(EquipmentModelPeer::MANUFACTURER_DOC_PAGE_COUNT, $this->manufacturer_doc_page_count);
		if ($this->isColumnModified(EquipmentModelPeer::MODEL_NUMBER)) $criteria->add(EquipmentModelPeer::MODEL_NUMBER, $this->model_number);
		if ($this->isColumnModified(EquipmentModelPeer::NAME)) $criteria->add(EquipmentModelPeer::NAME, $this->name);
		if ($this->isColumnModified(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID)) $criteria->add(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, $this->subcomponents_doc_file_id);
		if ($this->isColumnModified(EquipmentModelPeer::SUBCOMPONENTS_DOC_PAGE_COUNT)) $criteria->add(EquipmentModelPeer::SUBCOMPONENTS_DOC_PAGE_COUNT, $this->subcomponents_doc_page_count);
		if ($this->isColumnModified(EquipmentModelPeer::SUPPLIER)) $criteria->add(EquipmentModelPeer::SUPPLIER, $this->supplier);

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
		$criteria = new Criteria(EquipmentModelPeer::DATABASE_NAME);

		$criteria->add(EquipmentModelPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of EquipmentModel (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAdditionalSpecFileId($this->additional_spec_file_id);

		$copyObj->setAdditionalSpecPageCount($this->additional_spec_page_count);

		$copyObj->setDesignConsiderationFileId($this->design_consideration_file_id);

		$copyObj->setDesignConsiderationPageCount($this->design_page_count);

		$copyObj->setEquipmentClassId($this->equipment_class_id);

		$copyObj->setInterfaceDocFileId($this->interface_doc_file_id);

		$copyObj->setInterfaceDocPageCount($this->interface_doc_page_count);

		$copyObj->setManufacturer($this->manufacturer);

		$copyObj->setManufacturerDocFileId($this->manufacturer_doc_file_id);

		$copyObj->setManufacturerDocPageCount($this->manufacturer_doc_page_count);

		$copyObj->setModelNumber($this->model_number);

		$copyObj->setName($this->name);

		$copyObj->setSubcomponentsDocFileId($this->subcomponents_doc_file_id);

		$copyObj->setSubcomponentsDocPageCount($this->subcomponents_doc_page_count);

		$copyObj->setSupplier($this->supplier);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getEquipments() as $relObj) {
				$copyObj->addEquipment($relObj->copy($deepCopy));
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
	 * @return     EquipmentModel Clone of current object.
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
	 * @return     EquipmentModelPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EquipmentModelPeer();
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
	public function setDataFileRelatedByAdditionalSpecFileId($v)
	{


		if ($v === null) {
			$this->setAdditionalSpecFileId(NULL);
		} else {
			$this->setAdditionalSpecFileId($v->getId());
		}


		$this->aDataFileRelatedByAdditionalSpecFileId = $v;
	}


	/**
	 * Get the associated DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
	public function getDataFileRelatedByAdditionalSpecFileId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';

		if ($this->aDataFileRelatedByAdditionalSpecFileId === null && ($this->additional_spec_file_id > 0)) {

			$this->aDataFileRelatedByAdditionalSpecFileId = DataFilePeer::retrieveByPK($this->additional_spec_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->additional_spec_file_id, $con);
			   $obj->addDataFilesRelatedByAdditionalSpecFileId($this);
			 */
		}
		return $this->aDataFileRelatedByAdditionalSpecFileId;
	}

	/**
	 * Declares an association between this object and a DataFile object.
	 *
	 * @param      DataFile $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDataFileRelatedByInterfaceDocFileId($v)
	{


		if ($v === null) {
			$this->setInterfaceDocFileId(NULL);
		} else {
			$this->setInterfaceDocFileId($v->getId());
		}


		$this->aDataFileRelatedByInterfaceDocFileId = $v;
	}


	/**
	 * Get the associated DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
	public function getDataFileRelatedByInterfaceDocFileId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';

		if ($this->aDataFileRelatedByInterfaceDocFileId === null && ($this->interface_doc_file_id > 0)) {

			$this->aDataFileRelatedByInterfaceDocFileId = DataFilePeer::retrieveByPK($this->interface_doc_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->interface_doc_file_id, $con);
			   $obj->addDataFilesRelatedByInterfaceDocFileId($this);
			 */
		}
		return $this->aDataFileRelatedByInterfaceDocFileId;
	}

	/**
	 * Declares an association between this object and a DataFile object.
	 *
	 * @param      DataFile $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDataFileRelatedByManufacturerDocFileId($v)
	{


		if ($v === null) {
			$this->setManufacturerDocFileId(NULL);
		} else {
			$this->setManufacturerDocFileId($v->getId());
		}


		$this->aDataFileRelatedByManufacturerDocFileId = $v;
	}


	/**
	 * Get the associated DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
	public function getDataFileRelatedByManufacturerDocFileId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';

		if ($this->aDataFileRelatedByManufacturerDocFileId === null && ($this->manufacturer_doc_file_id > 0)) {

			$this->aDataFileRelatedByManufacturerDocFileId = DataFilePeer::retrieveByPK($this->manufacturer_doc_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->manufacturer_doc_file_id, $con);
			   $obj->addDataFilesRelatedByManufacturerDocFileId($this);
			 */
		}
		return $this->aDataFileRelatedByManufacturerDocFileId;
	}

	/**
	 * Declares an association between this object and a DataFile object.
	 *
	 * @param      DataFile $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDataFileRelatedBySubcomponentsDocFileId($v)
	{


		if ($v === null) {
			$this->setSubcomponentsDocFileId(NULL);
		} else {
			$this->setSubcomponentsDocFileId($v->getId());
		}


		$this->aDataFileRelatedBySubcomponentsDocFileId = $v;
	}


	/**
	 * Get the associated DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
	public function getDataFileRelatedBySubcomponentsDocFileId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';

		if ($this->aDataFileRelatedBySubcomponentsDocFileId === null && ($this->subcomponents_doc_file_id > 0)) {

			$this->aDataFileRelatedBySubcomponentsDocFileId = DataFilePeer::retrieveByPK($this->subcomponents_doc_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->subcomponents_doc_file_id, $con);
			   $obj->addDataFilesRelatedBySubcomponentsDocFileId($this);
			 */
		}
		return $this->aDataFileRelatedBySubcomponentsDocFileId;
	}

	/**
	 * Declares an association between this object and a DataFile object.
	 *
	 * @param      DataFile $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDataFileRelatedByDesignConsiderationFileId($v)
	{


		if ($v === null) {
			$this->setDesignConsiderationFileId(NULL);
		} else {
			$this->setDesignConsiderationFileId($v->getId());
		}


		$this->aDataFileRelatedByDesignConsiderationFileId = $v;
	}


	/**
	 * Get the associated DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
	public function getDataFileRelatedByDesignConsiderationFileId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';

		if ($this->aDataFileRelatedByDesignConsiderationFileId === null && ($this->design_consideration_file_id > 0)) {

			$this->aDataFileRelatedByDesignConsiderationFileId = DataFilePeer::retrieveByPK($this->design_consideration_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->design_consideration_file_id, $con);
			   $obj->addDataFilesRelatedByDesignConsiderationFileId($this);
			 */
		}
		return $this->aDataFileRelatedByDesignConsiderationFileId;
	}

	/**
	 * Declares an association between this object and a EquipmentClass object.
	 *
	 * @param      EquipmentClass $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEquipmentClass($v)
	{


		if ($v === null) {
			$this->setEquipmentClassId(NULL);
		} else {
			$this->setEquipmentClassId($v->getId());
		}


		$this->aEquipmentClass = $v;
	}


	/**
	 * Get the associated EquipmentClass object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     EquipmentClass The associated EquipmentClass object.
	 * @throws     PropelException
	 */
	public function getEquipmentClass($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEquipmentClassPeer.php';

		if ($this->aEquipmentClass === null && ($this->equipment_class_id > 0)) {

			$this->aEquipmentClass = EquipmentClassPeer::retrieveByPK($this->equipment_class_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EquipmentClassPeer::retrieveByPK($this->equipment_class_id, $con);
			   $obj->addEquipmentClasss($this);
			 */
		}
		return $this->aEquipmentClass;
	}

	/**
	 * Temporary storage of collEquipments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipments()
	{
		if ($this->collEquipments === null) {
			$this->collEquipments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentModel has previously
	 * been saved, it will retrieve related Equipments from storage.
	 * If this EquipmentModel is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipments($criteria = null, $con = null)
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

		if ($this->collEquipments === null) {
			if ($this->isNew()) {
			   $this->collEquipments = array();
			} else {

				$criteria->add(EquipmentPeer::MODEL_ID, $this->getId());

				EquipmentPeer::addSelectColumns($criteria);
				$this->collEquipments = EquipmentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentPeer::MODEL_ID, $this->getId());

				EquipmentPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentCriteria) || !$this->lastEquipmentCriteria->equals($criteria)) {
					$this->collEquipments = EquipmentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentCriteria = $criteria;
		return $this->collEquipments;
	}

	/**
	 * Returns the number of related Equipments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipments($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(EquipmentPeer::MODEL_ID, $this->getId());

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
	public function addEquipment(Equipment $l)
	{
		$this->collEquipments[] = $l;
		$l->setEquipmentModel($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentModel is new, it will return
	 * an empty collection; or if this EquipmentModel has previously
	 * been saved, it will retrieve related Equipments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentModel.
	 */
	public function getEquipmentsJoinEquipmentRelatedByParentId($criteria = null, $con = null)
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

		if ($this->collEquipments === null) {
			if ($this->isNew()) {
				$this->collEquipments = array();
			} else {

				$criteria->add(EquipmentPeer::MODEL_ID, $this->getId());

				$this->collEquipments = EquipmentPeer::doSelectJoinEquipmentRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentPeer::MODEL_ID, $this->getId());

			if (!isset($this->lastEquipmentCriteria) || !$this->lastEquipmentCriteria->equals($criteria)) {
				$this->collEquipments = EquipmentPeer::doSelectJoinEquipmentRelatedByParentId($criteria, $con);
			}
		}
		$this->lastEquipmentCriteria = $criteria;

		return $this->collEquipments;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EquipmentModel is new, it will return
	 * an empty collection; or if this EquipmentModel has previously
	 * been saved, it will retrieve related Equipments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EquipmentModel.
	 */
	public function getEquipmentsJoinOrganization($criteria = null, $con = null)
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

		if ($this->collEquipments === null) {
			if ($this->isNew()) {
				$this->collEquipments = array();
			} else {

				$criteria->add(EquipmentPeer::MODEL_ID, $this->getId());

				$this->collEquipments = EquipmentPeer::doSelectJoinOrganization($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentPeer::MODEL_ID, $this->getId());

			if (!isset($this->lastEquipmentCriteria) || !$this->lastEquipmentCriteria->equals($criteria)) {
				$this->collEquipments = EquipmentPeer::doSelectJoinOrganization($criteria, $con);
			}
		}
		$this->lastEquipmentCriteria = $criteria;

		return $this->collEquipments;
	}

} // BaseEquipmentModel
