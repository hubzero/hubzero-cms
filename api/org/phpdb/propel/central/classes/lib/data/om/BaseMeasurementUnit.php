<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/MeasurementUnitPeer.php';

/**
 * Base class that represents a row from the 'MEASUREMENT_UNIT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseMeasurementUnit extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        MeasurementUnitPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the abbreviation field.
	 * @var        string
	 */
	protected $abbreviation;


	/**
	 * The value for the base_unit field.
	 * @var        double
	 */
	protected $base_unit;


	/**
	 * The value for the category field.
	 * @var        double
	 */
	protected $category;


	/**
	 * The value for the comments field.
	 * @var        string
	 */
	protected $comments;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByBaseUnitId;

	/**
	 * @var        MeasurementUnitCategory
	 */
	protected $aMeasurementUnitCategory;

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
	 * Collection to store aggregation of collCoordinateSpacesRelatedByTranslationXUnitId.
	 * @var        array
	 */
	protected $collCoordinateSpacesRelatedByTranslationXUnitId;

	/**
	 * The criteria used to select the current contents of collCoordinateSpacesRelatedByTranslationXUnitId.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria = null;

	/**
	 * Collection to store aggregation of collCoordinateSpacesRelatedByTranslationYUnitId.
	 * @var        array
	 */
	protected $collCoordinateSpacesRelatedByTranslationYUnitId;

	/**
	 * The criteria used to select the current contents of collCoordinateSpacesRelatedByTranslationYUnitId.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria = null;

	/**
	 * Collection to store aggregation of collCoordinateSpacesRelatedByRotationZUnitId.
	 * @var        array
	 */
	protected $collCoordinateSpacesRelatedByRotationZUnitId;

	/**
	 * The criteria used to select the current contents of collCoordinateSpacesRelatedByRotationZUnitId.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceRelatedByRotationZUnitIdCriteria = null;

	/**
	 * Collection to store aggregation of collCoordinateSpacesRelatedByAltitudeUnitId.
	 * @var        array
	 */
	protected $collCoordinateSpacesRelatedByAltitudeUnitId;

	/**
	 * The criteria used to select the current contents of collCoordinateSpacesRelatedByAltitudeUnitId.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria = null;

	/**
	 * Collection to store aggregation of collCoordinateSpacesRelatedByRotationYUnitId.
	 * @var        array
	 */
	protected $collCoordinateSpacesRelatedByRotationYUnitId;

	/**
	 * The criteria used to select the current contents of collCoordinateSpacesRelatedByRotationYUnitId.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceRelatedByRotationYUnitIdCriteria = null;

	/**
	 * Collection to store aggregation of collCoordinateSpacesRelatedByTranslationZUnitId.
	 * @var        array
	 */
	protected $collCoordinateSpacesRelatedByTranslationZUnitId;

	/**
	 * The criteria used to select the current contents of collCoordinateSpacesRelatedByTranslationZUnitId.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria = null;

	/**
	 * Collection to store aggregation of collCoordinateSpacesRelatedByRotationXUnitId.
	 * @var        array
	 */
	protected $collCoordinateSpacesRelatedByRotationXUnitId;

	/**
	 * The criteria used to select the current contents of collCoordinateSpacesRelatedByRotationXUnitId.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceRelatedByRotationXUnitIdCriteria = null;

	/**
	 * Collection to store aggregation of collExperimentMeasurements.
	 * @var        array
	 */
	protected $collExperimentMeasurements;

	/**
	 * The criteria used to select the current contents of collExperimentMeasurements.
	 * @var        Criteria
	 */
	protected $lastExperimentMeasurementCriteria = null;

	/**
	 * Collection to store aggregation of collLocationsRelatedByJUnit.
	 * @var        array
	 */
	protected $collLocationsRelatedByJUnit;

	/**
	 * The criteria used to select the current contents of collLocationsRelatedByJUnit.
	 * @var        Criteria
	 */
	protected $lastLocationRelatedByJUnitCriteria = null;

	/**
	 * Collection to store aggregation of collLocationsRelatedByYUnit.
	 * @var        array
	 */
	protected $collLocationsRelatedByYUnit;

	/**
	 * The criteria used to select the current contents of collLocationsRelatedByYUnit.
	 * @var        Criteria
	 */
	protected $lastLocationRelatedByYUnitCriteria = null;

	/**
	 * Collection to store aggregation of collLocationsRelatedByXUnit.
	 * @var        array
	 */
	protected $collLocationsRelatedByXUnit;

	/**
	 * The criteria used to select the current contents of collLocationsRelatedByXUnit.
	 * @var        Criteria
	 */
	protected $lastLocationRelatedByXUnitCriteria = null;

	/**
	 * Collection to store aggregation of collLocationsRelatedByIUnit.
	 * @var        array
	 */
	protected $collLocationsRelatedByIUnit;

	/**
	 * The criteria used to select the current contents of collLocationsRelatedByIUnit.
	 * @var        Criteria
	 */
	protected $lastLocationRelatedByIUnitCriteria = null;

	/**
	 * Collection to store aggregation of collLocationsRelatedByZUnit.
	 * @var        array
	 */
	protected $collLocationsRelatedByZUnit;

	/**
	 * The criteria used to select the current contents of collLocationsRelatedByZUnit.
	 * @var        Criteria
	 */
	protected $lastLocationRelatedByZUnitCriteria = null;

	/**
	 * Collection to store aggregation of collLocationsRelatedByKUnit.
	 * @var        array
	 */
	protected $collLocationsRelatedByKUnit;

	/**
	 * The criteria used to select the current contents of collLocationsRelatedByKUnit.
	 * @var        Criteria
	 */
	protected $lastLocationRelatedByKUnitCriteria = null;

	/**
	 * Collection to store aggregation of collMaterialPropertys.
	 * @var        array
	 */
	protected $collMaterialPropertys;

	/**
	 * The criteria used to select the current contents of collMaterialPropertys.
	 * @var        Criteria
	 */
	protected $lastMaterialPropertyCriteria = null;

	/**
	 * Collection to store aggregation of collMeasurementUnitsRelatedByBaseUnitId.
	 * @var        array
	 */
	protected $collMeasurementUnitsRelatedByBaseUnitId;

	/**
	 * The criteria used to select the current contents of collMeasurementUnitsRelatedByBaseUnitId.
	 * @var        Criteria
	 */
	protected $lastMeasurementUnitRelatedByBaseUnitIdCriteria = null;

	/**
	 * Collection to store aggregation of collMeasurementUnitConversionsRelatedByToId.
	 * @var        array
	 */
	protected $collMeasurementUnitConversionsRelatedByToId;

	/**
	 * The criteria used to select the current contents of collMeasurementUnitConversionsRelatedByToId.
	 * @var        Criteria
	 */
	protected $lastMeasurementUnitConversionRelatedByToIdCriteria = null;

	/**
	 * Collection to store aggregation of collMeasurementUnitConversionsRelatedByFromId.
	 * @var        array
	 */
	protected $collMeasurementUnitConversionsRelatedByFromId;

	/**
	 * The criteria used to select the current contents of collMeasurementUnitConversionsRelatedByFromId.
	 * @var        Criteria
	 */
	protected $lastMeasurementUnitConversionRelatedByFromIdCriteria = null;

	/**
	 * Collection to store aggregation of collSensorModelsRelatedByMeasuredValueUnitsId.
	 * @var        array
	 */
	protected $collSensorModelsRelatedByMeasuredValueUnitsId;

	/**
	 * The criteria used to select the current contents of collSensorModelsRelatedByMeasuredValueUnitsId.
	 * @var        Criteria
	 */
	protected $lastSensorModelRelatedByMeasuredValueUnitsIdCriteria = null;

	/**
	 * Collection to store aggregation of collSensorModelsRelatedBySensitivityUnitsId.
	 * @var        array
	 */
	protected $collSensorModelsRelatedBySensitivityUnitsId;

	/**
	 * The criteria used to select the current contents of collSensorModelsRelatedBySensitivityUnitsId.
	 * @var        Criteria
	 */
	protected $lastSensorModelRelatedBySensitivityUnitsIdCriteria = null;

	/**
	 * Collection to store aggregation of collSensorModelsRelatedByTempUnitsId.
	 * @var        array
	 */
	protected $collSensorModelsRelatedByTempUnitsId;

	/**
	 * The criteria used to select the current contents of collSensorModelsRelatedByTempUnitsId.
	 * @var        Criteria
	 */
	protected $lastSensorModelRelatedByTempUnitsIdCriteria = null;

	/**
	 * Collection to store aggregation of collTrials.
	 * @var        array
	 */
	protected $collTrials;

	/**
	 * The criteria used to select the current contents of collTrials.
	 * @var        Criteria
	 */
	protected $lastTrialCriteria = null;

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
	 * Collection to store aggregation of collSpecimenComponentMaterialPropertys.
	 * @var        array
	 */
	protected $collSpecimenComponentMaterialPropertys;

	/**
	 * The criteria used to select the current contents of collSpecimenComponentMaterialPropertys.
	 * @var        Criteria
	 */
	protected $lastSpecimenComponentMaterialPropertyCriteria = null;

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
	 * Get the [abbreviation] column value.
	 * 
	 * @return     string
	 */
	public function getAbbreviation()
	{

		return $this->abbreviation;
	}

	/**
	 * Get the [base_unit] column value.
	 * 
	 * @return     double
	 */
	public function getBaseUnitId()
	{

		return $this->base_unit;
	}

	/**
	 * Get the [category] column value.
	 * 
	 * @return     double
	 */
	public function getCategoryId()
	{

		return $this->category;
	}

	/**
	 * Get the [comments] column value.
	 * 
	 * @return     string
	 */
	public function getComment()
	{

		return $this->comments;
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
			$this->modifiedColumns[] = MeasurementUnitPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [abbreviation] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setAbbreviation($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->abbreviation !== $v) {
			$this->abbreviation = $v;
			$this->modifiedColumns[] = MeasurementUnitPeer::ABBREVIATION;
		}

	} // setAbbreviation()

	/**
	 * Set the value of [base_unit] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setBaseUnitId($v)
	{

		if ($this->base_unit !== $v) {
			$this->base_unit = $v;
			$this->modifiedColumns[] = MeasurementUnitPeer::BASE_UNIT;
		}

		if ($this->aMeasurementUnitRelatedByBaseUnitId !== null && $this->aMeasurementUnitRelatedByBaseUnitId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByBaseUnitId = null;
		}

	} // setBaseUnitId()

	/**
	 * Set the value of [category] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCategoryId($v)
	{

		if ($this->category !== $v) {
			$this->category = $v;
			$this->modifiedColumns[] = MeasurementUnitPeer::CATEGORY;
		}

		if ($this->aMeasurementUnitCategory !== null && $this->aMeasurementUnitCategory->getId() !== $v) {
			$this->aMeasurementUnitCategory = null;
		}

	} // setCategoryId()

	/**
	 * Set the value of [comments] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setComment($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->comments) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->comments !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->comments = $obj;
			$this->modifiedColumns[] = MeasurementUnitPeer::COMMENTS;
		}

	} // setComment()

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
			$this->modifiedColumns[] = MeasurementUnitPeer::NAME;
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

			$this->abbreviation = $rs->getString($startcol + 1);

			$this->base_unit = $rs->getFloat($startcol + 2);

			$this->category = $rs->getFloat($startcol + 3);

			$this->comments = $rs->getClob($startcol + 4);

			$this->name = $rs->getString($startcol + 5);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = MeasurementUnitPeer::NUM_COLUMNS - MeasurementUnitPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating MeasurementUnit object", $e);
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
			$con = Propel::getConnection(MeasurementUnitPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			MeasurementUnitPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(MeasurementUnitPeer::DATABASE_NAME);
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

			if ($this->aMeasurementUnitRelatedByBaseUnitId !== null) {
				if ($this->aMeasurementUnitRelatedByBaseUnitId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByBaseUnitId->save($con);
				}
				$this->setMeasurementUnitRelatedByBaseUnitId($this->aMeasurementUnitRelatedByBaseUnitId);
			}

			if ($this->aMeasurementUnitCategory !== null) {
				if ($this->aMeasurementUnitCategory->isModified()) {
					$affectedRows += $this->aMeasurementUnitCategory->save($con);
				}
				$this->setMeasurementUnitCategory($this->aMeasurementUnitCategory);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = MeasurementUnitPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += MeasurementUnitPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collControllerConfigs !== null) {
				foreach($this->collControllerConfigs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinateSpacesRelatedByTranslationXUnitId !== null) {
				foreach($this->collCoordinateSpacesRelatedByTranslationXUnitId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinateSpacesRelatedByTranslationYUnitId !== null) {
				foreach($this->collCoordinateSpacesRelatedByTranslationYUnitId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinateSpacesRelatedByRotationZUnitId !== null) {
				foreach($this->collCoordinateSpacesRelatedByRotationZUnitId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinateSpacesRelatedByAltitudeUnitId !== null) {
				foreach($this->collCoordinateSpacesRelatedByAltitudeUnitId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinateSpacesRelatedByRotationYUnitId !== null) {
				foreach($this->collCoordinateSpacesRelatedByRotationYUnitId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinateSpacesRelatedByTranslationZUnitId !== null) {
				foreach($this->collCoordinateSpacesRelatedByTranslationZUnitId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinateSpacesRelatedByRotationXUnitId !== null) {
				foreach($this->collCoordinateSpacesRelatedByRotationXUnitId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collExperimentMeasurements !== null) {
				foreach($this->collExperimentMeasurements as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collLocationsRelatedByJUnit !== null) {
				foreach($this->collLocationsRelatedByJUnit as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collLocationsRelatedByYUnit !== null) {
				foreach($this->collLocationsRelatedByYUnit as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collLocationsRelatedByXUnit !== null) {
				foreach($this->collLocationsRelatedByXUnit as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collLocationsRelatedByIUnit !== null) {
				foreach($this->collLocationsRelatedByIUnit as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collLocationsRelatedByZUnit !== null) {
				foreach($this->collLocationsRelatedByZUnit as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collLocationsRelatedByKUnit !== null) {
				foreach($this->collLocationsRelatedByKUnit as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collMaterialPropertys !== null) {
				foreach($this->collMaterialPropertys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collMeasurementUnitsRelatedByBaseUnitId !== null) {
				foreach($this->collMeasurementUnitsRelatedByBaseUnitId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collMeasurementUnitConversionsRelatedByToId !== null) {
				foreach($this->collMeasurementUnitConversionsRelatedByToId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collMeasurementUnitConversionsRelatedByFromId !== null) {
				foreach($this->collMeasurementUnitConversionsRelatedByFromId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorModelsRelatedByMeasuredValueUnitsId !== null) {
				foreach($this->collSensorModelsRelatedByMeasuredValueUnitsId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorModelsRelatedBySensitivityUnitsId !== null) {
				foreach($this->collSensorModelsRelatedBySensitivityUnitsId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorModelsRelatedByTempUnitsId !== null) {
				foreach($this->collSensorModelsRelatedByTempUnitsId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collTrials !== null) {
				foreach($this->collTrials as $referrerFK) {
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

			if ($this->collSpecimenComponentMaterialPropertys !== null) {
				foreach($this->collSpecimenComponentMaterialPropertys as $referrerFK) {
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

			if ($this->aMeasurementUnitRelatedByBaseUnitId !== null) {
				if (!$this->aMeasurementUnitRelatedByBaseUnitId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByBaseUnitId->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitCategory !== null) {
				if (!$this->aMeasurementUnitCategory->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitCategory->getValidationFailures());
				}
			}


			if (($retval = MeasurementUnitPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collControllerConfigs !== null) {
					foreach($this->collControllerConfigs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCoordinateSpacesRelatedByTranslationXUnitId !== null) {
					foreach($this->collCoordinateSpacesRelatedByTranslationXUnitId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCoordinateSpacesRelatedByTranslationYUnitId !== null) {
					foreach($this->collCoordinateSpacesRelatedByTranslationYUnitId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCoordinateSpacesRelatedByRotationZUnitId !== null) {
					foreach($this->collCoordinateSpacesRelatedByRotationZUnitId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCoordinateSpacesRelatedByAltitudeUnitId !== null) {
					foreach($this->collCoordinateSpacesRelatedByAltitudeUnitId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCoordinateSpacesRelatedByRotationYUnitId !== null) {
					foreach($this->collCoordinateSpacesRelatedByRotationYUnitId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCoordinateSpacesRelatedByTranslationZUnitId !== null) {
					foreach($this->collCoordinateSpacesRelatedByTranslationZUnitId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCoordinateSpacesRelatedByRotationXUnitId !== null) {
					foreach($this->collCoordinateSpacesRelatedByRotationXUnitId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collExperimentMeasurements !== null) {
					foreach($this->collExperimentMeasurements as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collLocationsRelatedByJUnit !== null) {
					foreach($this->collLocationsRelatedByJUnit as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collLocationsRelatedByYUnit !== null) {
					foreach($this->collLocationsRelatedByYUnit as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collLocationsRelatedByXUnit !== null) {
					foreach($this->collLocationsRelatedByXUnit as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collLocationsRelatedByIUnit !== null) {
					foreach($this->collLocationsRelatedByIUnit as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collLocationsRelatedByZUnit !== null) {
					foreach($this->collLocationsRelatedByZUnit as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collLocationsRelatedByKUnit !== null) {
					foreach($this->collLocationsRelatedByKUnit as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collMaterialPropertys !== null) {
					foreach($this->collMaterialPropertys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collMeasurementUnitConversionsRelatedByToId !== null) {
					foreach($this->collMeasurementUnitConversionsRelatedByToId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collMeasurementUnitConversionsRelatedByFromId !== null) {
					foreach($this->collMeasurementUnitConversionsRelatedByFromId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorModelsRelatedByMeasuredValueUnitsId !== null) {
					foreach($this->collSensorModelsRelatedByMeasuredValueUnitsId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorModelsRelatedBySensitivityUnitsId !== null) {
					foreach($this->collSensorModelsRelatedBySensitivityUnitsId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorModelsRelatedByTempUnitsId !== null) {
					foreach($this->collSensorModelsRelatedByTempUnitsId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collTrials !== null) {
					foreach($this->collTrials as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSpecimenComponentAttributes !== null) {
					foreach($this->collSpecimenComponentAttributes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSpecimenComponentMaterialPropertys !== null) {
					foreach($this->collSpecimenComponentMaterialPropertys as $referrerFK) {
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
		$pos = MeasurementUnitPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAbbreviation();
				break;
			case 2:
				return $this->getBaseUnitId();
				break;
			case 3:
				return $this->getCategoryId();
				break;
			case 4:
				return $this->getComment();
				break;
			case 5:
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
		$keys = MeasurementUnitPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getAbbreviation(),
			$keys[2] => $this->getBaseUnitId(),
			$keys[3] => $this->getCategoryId(),
			$keys[4] => $this->getComment(),
			$keys[5] => $this->getName(),
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
		$pos = MeasurementUnitPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAbbreviation($value);
				break;
			case 2:
				$this->setBaseUnitId($value);
				break;
			case 3:
				$this->setCategoryId($value);
				break;
			case 4:
				$this->setComment($value);
				break;
			case 5:
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
		$keys = MeasurementUnitPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAbbreviation($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setBaseUnitId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCategoryId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setComment($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setName($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(MeasurementUnitPeer::DATABASE_NAME);

		if ($this->isColumnModified(MeasurementUnitPeer::ID)) $criteria->add(MeasurementUnitPeer::ID, $this->id);
		if ($this->isColumnModified(MeasurementUnitPeer::ABBREVIATION)) $criteria->add(MeasurementUnitPeer::ABBREVIATION, $this->abbreviation);
		if ($this->isColumnModified(MeasurementUnitPeer::BASE_UNIT)) $criteria->add(MeasurementUnitPeer::BASE_UNIT, $this->base_unit);
		if ($this->isColumnModified(MeasurementUnitPeer::CATEGORY)) $criteria->add(MeasurementUnitPeer::CATEGORY, $this->category);
		if ($this->isColumnModified(MeasurementUnitPeer::COMMENTS)) $criteria->add(MeasurementUnitPeer::COMMENTS, $this->comments);
		if ($this->isColumnModified(MeasurementUnitPeer::NAME)) $criteria->add(MeasurementUnitPeer::NAME, $this->name);

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
		$criteria = new Criteria(MeasurementUnitPeer::DATABASE_NAME);

		$criteria->add(MeasurementUnitPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of MeasurementUnit (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAbbreviation($this->abbreviation);

		$copyObj->setBaseUnitId($this->base_unit);

		$copyObj->setCategoryId($this->category);

		$copyObj->setComment($this->comments);

		$copyObj->setName($this->name);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getControllerConfigs() as $relObj) {
				$copyObj->addControllerConfig($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinateSpacesRelatedByTranslationXUnitId() as $relObj) {
				$copyObj->addCoordinateSpaceRelatedByTranslationXUnitId($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinateSpacesRelatedByTranslationYUnitId() as $relObj) {
				$copyObj->addCoordinateSpaceRelatedByTranslationYUnitId($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinateSpacesRelatedByRotationZUnitId() as $relObj) {
				$copyObj->addCoordinateSpaceRelatedByRotationZUnitId($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinateSpacesRelatedByAltitudeUnitId() as $relObj) {
				$copyObj->addCoordinateSpaceRelatedByAltitudeUnitId($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinateSpacesRelatedByRotationYUnitId() as $relObj) {
				$copyObj->addCoordinateSpaceRelatedByRotationYUnitId($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinateSpacesRelatedByTranslationZUnitId() as $relObj) {
				$copyObj->addCoordinateSpaceRelatedByTranslationZUnitId($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinateSpacesRelatedByRotationXUnitId() as $relObj) {
				$copyObj->addCoordinateSpaceRelatedByRotationXUnitId($relObj->copy($deepCopy));
			}

			foreach($this->getExperimentMeasurements() as $relObj) {
				$copyObj->addExperimentMeasurement($relObj->copy($deepCopy));
			}

			foreach($this->getLocationsRelatedByJUnit() as $relObj) {
				$copyObj->addLocationRelatedByJUnit($relObj->copy($deepCopy));
			}

			foreach($this->getLocationsRelatedByYUnit() as $relObj) {
				$copyObj->addLocationRelatedByYUnit($relObj->copy($deepCopy));
			}

			foreach($this->getLocationsRelatedByXUnit() as $relObj) {
				$copyObj->addLocationRelatedByXUnit($relObj->copy($deepCopy));
			}

			foreach($this->getLocationsRelatedByIUnit() as $relObj) {
				$copyObj->addLocationRelatedByIUnit($relObj->copy($deepCopy));
			}

			foreach($this->getLocationsRelatedByZUnit() as $relObj) {
				$copyObj->addLocationRelatedByZUnit($relObj->copy($deepCopy));
			}

			foreach($this->getLocationsRelatedByKUnit() as $relObj) {
				$copyObj->addLocationRelatedByKUnit($relObj->copy($deepCopy));
			}

			foreach($this->getMaterialPropertys() as $relObj) {
				$copyObj->addMaterialProperty($relObj->copy($deepCopy));
			}

			foreach($this->getMeasurementUnitsRelatedByBaseUnitId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addMeasurementUnitRelatedByBaseUnitId($relObj->copy($deepCopy));
			}

			foreach($this->getMeasurementUnitConversionsRelatedByToId() as $relObj) {
				$copyObj->addMeasurementUnitConversionRelatedByToId($relObj->copy($deepCopy));
			}

			foreach($this->getMeasurementUnitConversionsRelatedByFromId() as $relObj) {
				$copyObj->addMeasurementUnitConversionRelatedByFromId($relObj->copy($deepCopy));
			}

			foreach($this->getSensorModelsRelatedByMeasuredValueUnitsId() as $relObj) {
				$copyObj->addSensorModelRelatedByMeasuredValueUnitsId($relObj->copy($deepCopy));
			}

			foreach($this->getSensorModelsRelatedBySensitivityUnitsId() as $relObj) {
				$copyObj->addSensorModelRelatedBySensitivityUnitsId($relObj->copy($deepCopy));
			}

			foreach($this->getSensorModelsRelatedByTempUnitsId() as $relObj) {
				$copyObj->addSensorModelRelatedByTempUnitsId($relObj->copy($deepCopy));
			}

			foreach($this->getTrials() as $relObj) {
				$copyObj->addTrial($relObj->copy($deepCopy));
			}

			foreach($this->getSpecimenComponentAttributes() as $relObj) {
				$copyObj->addSpecimenComponentAttribute($relObj->copy($deepCopy));
			}

			foreach($this->getSpecimenComponentMaterialPropertys() as $relObj) {
				$copyObj->addSpecimenComponentMaterialProperty($relObj->copy($deepCopy));
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
	 * @return     MeasurementUnit Clone of current object.
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
	 * @return     MeasurementUnitPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new MeasurementUnitPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByBaseUnitId($v)
	{


		if ($v === null) {
			$this->setBaseUnitId(NULL);
		} else {
			$this->setBaseUnitId($v->getId());
		}


		$this->aMeasurementUnitRelatedByBaseUnitId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByBaseUnitId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByBaseUnitId === null && ($this->base_unit > 0)) {

			$this->aMeasurementUnitRelatedByBaseUnitId = MeasurementUnitPeer::retrieveByPK($this->base_unit, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->base_unit, $con);
			   $obj->addMeasurementUnitsRelatedByBaseUnitId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByBaseUnitId;
	}

	/**
	 * Declares an association between this object and a MeasurementUnitCategory object.
	 *
	 * @param      MeasurementUnitCategory $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitCategory($v)
	{


		if ($v === null) {
			$this->setCategoryId(NULL);
		} else {
			$this->setCategoryId($v->getId());
		}


		$this->aMeasurementUnitCategory = $v;
	}


	/**
	 * Get the associated MeasurementUnitCategory object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnitCategory The associated MeasurementUnitCategory object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitCategory($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitCategoryPeer.php';

		if ($this->aMeasurementUnitCategory === null && ($this->category > 0)) {

			$this->aMeasurementUnitCategory = MeasurementUnitCategoryPeer::retrieveByPK($this->category, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitCategoryPeer::retrieveByPK($this->category, $con);
			   $obj->addMeasurementUnitCategorys($this);
			 */
		}
		return $this->aMeasurementUnitCategory;
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
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 * If this MeasurementUnit is new, it will return
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

				$criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->getId());

				ControllerConfigPeer::addSelectColumns($criteria);
				$this->collControllerConfigs = ControllerConfigPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->getId());

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

		$criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->getId());

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
		$l->setMeasurementUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
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

				$criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinDataFileRelatedByInputDataFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->getId());

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
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
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

				$criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinDataFileRelatedByConfigDataFileId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->getId());

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
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getControllerConfigsJoinEquipment($criteria = null, $con = null)
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

				$criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->getId());

			if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastControllerConfigCriteria = $criteria;

		return $this->collControllerConfigs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
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

				$criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->getId());

				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->getId());

			if (!isset($this->lastControllerConfigCriteria) || !$this->lastControllerConfigCriteria->equals($criteria)) {
				$this->collControllerConfigs = ControllerConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastControllerConfigCriteria = $criteria;

		return $this->collControllerConfigs;
	}

	/**
	 * Temporary storage of collCoordinateSpacesRelatedByTranslationXUnitId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpacesRelatedByTranslationXUnitId()
	{
		if ($this->collCoordinateSpacesRelatedByTranslationXUnitId === null) {
			$this->collCoordinateSpacesRelatedByTranslationXUnitId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationXUnitId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpacesRelatedByTranslationXUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationXUnitId === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpacesRelatedByTranslationXUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				$this->collCoordinateSpacesRelatedByTranslationXUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria->equals($criteria)) {
					$this->collCoordinateSpacesRelatedByTranslationXUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria = $criteria;
		return $this->collCoordinateSpacesRelatedByTranslationXUnitId;
	}

	/**
	 * Returns the number of related CoordinateSpacesRelatedByTranslationXUnitId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpacesRelatedByTranslationXUnitId($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, $this->getId());

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
	public function addCoordinateSpaceRelatedByTranslationXUnitId(CoordinateSpace $l)
	{
		$this->collCoordinateSpacesRelatedByTranslationXUnitId[] = $l;
		$l->setMeasurementUnitRelatedByTranslationXUnitId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationXUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByTranslationXUnitIdJoinCoordinateSpaceRelatedByParentId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationXUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByTranslationXUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByTranslationXUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByTranslationXUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByTranslationXUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationXUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByTranslationXUnitIdJoinCoordinateSystem($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationXUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByTranslationXUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByTranslationXUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByTranslationXUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByTranslationXUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationXUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByTranslationXUnitIdJoinExperiment($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationXUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByTranslationXUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByTranslationXUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByTranslationXUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationXUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByTranslationXUnitId;
	}

	/**
	 * Temporary storage of collCoordinateSpacesRelatedByTranslationYUnitId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpacesRelatedByTranslationYUnitId()
	{
		if ($this->collCoordinateSpacesRelatedByTranslationYUnitId === null) {
			$this->collCoordinateSpacesRelatedByTranslationYUnitId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationYUnitId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpacesRelatedByTranslationYUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationYUnitId === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpacesRelatedByTranslationYUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				$this->collCoordinateSpacesRelatedByTranslationYUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria->equals($criteria)) {
					$this->collCoordinateSpacesRelatedByTranslationYUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria = $criteria;
		return $this->collCoordinateSpacesRelatedByTranslationYUnitId;
	}

	/**
	 * Returns the number of related CoordinateSpacesRelatedByTranslationYUnitId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpacesRelatedByTranslationYUnitId($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, $this->getId());

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
	public function addCoordinateSpaceRelatedByTranslationYUnitId(CoordinateSpace $l)
	{
		$this->collCoordinateSpacesRelatedByTranslationYUnitId[] = $l;
		$l->setMeasurementUnitRelatedByTranslationYUnitId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationYUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByTranslationYUnitIdJoinCoordinateSpaceRelatedByParentId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationYUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByTranslationYUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByTranslationYUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByTranslationYUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByTranslationYUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationYUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByTranslationYUnitIdJoinCoordinateSystem($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationYUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByTranslationYUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByTranslationYUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByTranslationYUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByTranslationYUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationYUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByTranslationYUnitIdJoinExperiment($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationYUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByTranslationYUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByTranslationYUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByTranslationYUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationYUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByTranslationYUnitId;
	}

	/**
	 * Temporary storage of collCoordinateSpacesRelatedByRotationZUnitId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpacesRelatedByRotationZUnitId()
	{
		if ($this->collCoordinateSpacesRelatedByRotationZUnitId === null) {
			$this->collCoordinateSpacesRelatedByRotationZUnitId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationZUnitId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpacesRelatedByRotationZUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationZUnitId === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpacesRelatedByRotationZUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONZUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				$this->collCoordinateSpacesRelatedByRotationZUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpacePeer::ROTATIONZUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria->equals($criteria)) {
					$this->collCoordinateSpacesRelatedByRotationZUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria = $criteria;
		return $this->collCoordinateSpacesRelatedByRotationZUnitId;
	}

	/**
	 * Returns the number of related CoordinateSpacesRelatedByRotationZUnitId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpacesRelatedByRotationZUnitId($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(CoordinateSpacePeer::ROTATIONZUNIT_ID, $this->getId());

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
	public function addCoordinateSpaceRelatedByRotationZUnitId(CoordinateSpace $l)
	{
		$this->collCoordinateSpacesRelatedByRotationZUnitId[] = $l;
		$l->setMeasurementUnitRelatedByRotationZUnitId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationZUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByRotationZUnitIdJoinCoordinateSpaceRelatedByParentId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationZUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByRotationZUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONZUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByRotationZUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ROTATIONZUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByRotationZUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByRotationZUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationZUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByRotationZUnitIdJoinCoordinateSystem($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationZUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByRotationZUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONZUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByRotationZUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ROTATIONZUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByRotationZUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByRotationZUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationZUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByRotationZUnitIdJoinExperiment($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationZUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByRotationZUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONZUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByRotationZUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ROTATIONZUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByRotationZUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationZUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByRotationZUnitId;
	}

	/**
	 * Temporary storage of collCoordinateSpacesRelatedByAltitudeUnitId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpacesRelatedByAltitudeUnitId()
	{
		if ($this->collCoordinateSpacesRelatedByAltitudeUnitId === null) {
			$this->collCoordinateSpacesRelatedByAltitudeUnitId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByAltitudeUnitId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpacesRelatedByAltitudeUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByAltitudeUnitId === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpacesRelatedByAltitudeUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ALTITUDE_UNIT, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				$this->collCoordinateSpacesRelatedByAltitudeUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpacePeer::ALTITUDE_UNIT, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria->equals($criteria)) {
					$this->collCoordinateSpacesRelatedByAltitudeUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria = $criteria;
		return $this->collCoordinateSpacesRelatedByAltitudeUnitId;
	}

	/**
	 * Returns the number of related CoordinateSpacesRelatedByAltitudeUnitId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpacesRelatedByAltitudeUnitId($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(CoordinateSpacePeer::ALTITUDE_UNIT, $this->getId());

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
	public function addCoordinateSpaceRelatedByAltitudeUnitId(CoordinateSpace $l)
	{
		$this->collCoordinateSpacesRelatedByAltitudeUnitId[] = $l;
		$l->setMeasurementUnitRelatedByAltitudeUnitId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByAltitudeUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByAltitudeUnitIdJoinCoordinateSpaceRelatedByParentId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByAltitudeUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByAltitudeUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ALTITUDE_UNIT, $this->getId());

				$this->collCoordinateSpacesRelatedByAltitudeUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ALTITUDE_UNIT, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByAltitudeUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByAltitudeUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByAltitudeUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByAltitudeUnitIdJoinCoordinateSystem($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByAltitudeUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByAltitudeUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ALTITUDE_UNIT, $this->getId());

				$this->collCoordinateSpacesRelatedByAltitudeUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ALTITUDE_UNIT, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByAltitudeUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByAltitudeUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByAltitudeUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByAltitudeUnitIdJoinExperiment($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByAltitudeUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByAltitudeUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ALTITUDE_UNIT, $this->getId());

				$this->collCoordinateSpacesRelatedByAltitudeUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ALTITUDE_UNIT, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByAltitudeUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByAltitudeUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByAltitudeUnitId;
	}

	/**
	 * Temporary storage of collCoordinateSpacesRelatedByRotationYUnitId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpacesRelatedByRotationYUnitId()
	{
		if ($this->collCoordinateSpacesRelatedByRotationYUnitId === null) {
			$this->collCoordinateSpacesRelatedByRotationYUnitId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationYUnitId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpacesRelatedByRotationYUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationYUnitId === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpacesRelatedByRotationYUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONYUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				$this->collCoordinateSpacesRelatedByRotationYUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpacePeer::ROTATIONYUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria->equals($criteria)) {
					$this->collCoordinateSpacesRelatedByRotationYUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria = $criteria;
		return $this->collCoordinateSpacesRelatedByRotationYUnitId;
	}

	/**
	 * Returns the number of related CoordinateSpacesRelatedByRotationYUnitId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpacesRelatedByRotationYUnitId($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(CoordinateSpacePeer::ROTATIONYUNIT_ID, $this->getId());

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
	public function addCoordinateSpaceRelatedByRotationYUnitId(CoordinateSpace $l)
	{
		$this->collCoordinateSpacesRelatedByRotationYUnitId[] = $l;
		$l->setMeasurementUnitRelatedByRotationYUnitId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationYUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByRotationYUnitIdJoinCoordinateSpaceRelatedByParentId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationYUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByRotationYUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONYUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByRotationYUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ROTATIONYUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByRotationYUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByRotationYUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationYUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByRotationYUnitIdJoinCoordinateSystem($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationYUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByRotationYUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONYUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByRotationYUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ROTATIONYUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByRotationYUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByRotationYUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationYUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByRotationYUnitIdJoinExperiment($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationYUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByRotationYUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONYUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByRotationYUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ROTATIONYUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByRotationYUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationYUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByRotationYUnitId;
	}

	/**
	 * Temporary storage of collCoordinateSpacesRelatedByTranslationZUnitId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpacesRelatedByTranslationZUnitId()
	{
		if ($this->collCoordinateSpacesRelatedByTranslationZUnitId === null) {
			$this->collCoordinateSpacesRelatedByTranslationZUnitId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationZUnitId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpacesRelatedByTranslationZUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationZUnitId === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpacesRelatedByTranslationZUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				$this->collCoordinateSpacesRelatedByTranslationZUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria->equals($criteria)) {
					$this->collCoordinateSpacesRelatedByTranslationZUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria = $criteria;
		return $this->collCoordinateSpacesRelatedByTranslationZUnitId;
	}

	/**
	 * Returns the number of related CoordinateSpacesRelatedByTranslationZUnitId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpacesRelatedByTranslationZUnitId($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, $this->getId());

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
	public function addCoordinateSpaceRelatedByTranslationZUnitId(CoordinateSpace $l)
	{
		$this->collCoordinateSpacesRelatedByTranslationZUnitId[] = $l;
		$l->setMeasurementUnitRelatedByTranslationZUnitId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationZUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByTranslationZUnitIdJoinCoordinateSpaceRelatedByParentId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationZUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByTranslationZUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByTranslationZUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByTranslationZUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByTranslationZUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationZUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByTranslationZUnitIdJoinCoordinateSystem($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationZUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByTranslationZUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByTranslationZUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByTranslationZUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByTranslationZUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByTranslationZUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByTranslationZUnitIdJoinExperiment($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByTranslationZUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByTranslationZUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByTranslationZUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByTranslationZUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByTranslationZUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByTranslationZUnitId;
	}

	/**
	 * Temporary storage of collCoordinateSpacesRelatedByRotationXUnitId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpacesRelatedByRotationXUnitId()
	{
		if ($this->collCoordinateSpacesRelatedByRotationXUnitId === null) {
			$this->collCoordinateSpacesRelatedByRotationXUnitId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationXUnitId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpacesRelatedByRotationXUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationXUnitId === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpacesRelatedByRotationXUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONXUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				$this->collCoordinateSpacesRelatedByRotationXUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpacePeer::ROTATIONXUNIT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria->equals($criteria)) {
					$this->collCoordinateSpacesRelatedByRotationXUnitId = CoordinateSpacePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria = $criteria;
		return $this->collCoordinateSpacesRelatedByRotationXUnitId;
	}

	/**
	 * Returns the number of related CoordinateSpacesRelatedByRotationXUnitId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpacesRelatedByRotationXUnitId($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(CoordinateSpacePeer::ROTATIONXUNIT_ID, $this->getId());

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
	public function addCoordinateSpaceRelatedByRotationXUnitId(CoordinateSpace $l)
	{
		$this->collCoordinateSpacesRelatedByRotationXUnitId[] = $l;
		$l->setMeasurementUnitRelatedByRotationXUnitId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationXUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByRotationXUnitIdJoinCoordinateSpaceRelatedByParentId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationXUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByRotationXUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONXUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByRotationXUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ROTATIONXUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByRotationXUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByRotationXUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationXUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByRotationXUnitIdJoinCoordinateSystem($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationXUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByRotationXUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONXUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByRotationXUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ROTATIONXUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByRotationXUnitId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByRotationXUnitId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByRotationXUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getCoordinateSpacesRelatedByRotationXUnitIdJoinExperiment($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByRotationXUnitId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByRotationXUnitId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::ROTATIONXUNIT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByRotationXUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::ROTATIONXUNIT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria) || !$this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByRotationXUnitId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByRotationXUnitIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByRotationXUnitId;
	}

	/**
	 * Temporary storage of collExperimentMeasurements to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initExperimentMeasurements()
	{
		if ($this->collExperimentMeasurements === null) {
			$this->collExperimentMeasurements = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related ExperimentMeasurements from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getExperimentMeasurements($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentMeasurementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentMeasurements === null) {
			if ($this->isNew()) {
			   $this->collExperimentMeasurements = array();
			} else {

				$criteria->add(ExperimentMeasurementPeer::DEFAULT_UNIT, $this->getId());

				ExperimentMeasurementPeer::addSelectColumns($criteria);
				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentMeasurementPeer::DEFAULT_UNIT, $this->getId());

				ExperimentMeasurementPeer::addSelectColumns($criteria);
				if (!isset($this->lastExperimentMeasurementCriteria) || !$this->lastExperimentMeasurementCriteria->equals($criteria)) {
					$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastExperimentMeasurementCriteria = $criteria;
		return $this->collExperimentMeasurements;
	}

	/**
	 * Returns the number of related ExperimentMeasurements.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countExperimentMeasurements($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentMeasurementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ExperimentMeasurementPeer::DEFAULT_UNIT, $this->getId());

		return ExperimentMeasurementPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ExperimentMeasurement object to this object
	 * through the ExperimentMeasurement foreign key attribute
	 *
	 * @param      ExperimentMeasurement $l ExperimentMeasurement
	 * @return     void
	 * @throws     PropelException
	 */
	public function addExperimentMeasurement(ExperimentMeasurement $l)
	{
		$this->collExperimentMeasurements[] = $l;
		$l->setMeasurementUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related ExperimentMeasurements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getExperimentMeasurementsJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentMeasurementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentMeasurements === null) {
			if ($this->isNew()) {
				$this->collExperimentMeasurements = array();
			} else {

				$criteria->add(ExperimentMeasurementPeer::DEFAULT_UNIT, $this->getId());

				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentMeasurementPeer::DEFAULT_UNIT, $this->getId());

			if (!isset($this->lastExperimentMeasurementCriteria) || !$this->lastExperimentMeasurementCriteria->equals($criteria)) {
				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastExperimentMeasurementCriteria = $criteria;

		return $this->collExperimentMeasurements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related ExperimentMeasurements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getExperimentMeasurementsJoinMeasurementUnitCategory($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentMeasurementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentMeasurements === null) {
			if ($this->isNew()) {
				$this->collExperimentMeasurements = array();
			} else {

				$criteria->add(ExperimentMeasurementPeer::DEFAULT_UNIT, $this->getId());

				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinMeasurementUnitCategory($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentMeasurementPeer::DEFAULT_UNIT, $this->getId());

			if (!isset($this->lastExperimentMeasurementCriteria) || !$this->lastExperimentMeasurementCriteria->equals($criteria)) {
				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinMeasurementUnitCategory($criteria, $con);
			}
		}
		$this->lastExperimentMeasurementCriteria = $criteria;

		return $this->collExperimentMeasurements;
	}

	/**
	 * Temporary storage of collLocationsRelatedByJUnit to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLocationsRelatedByJUnit()
	{
		if ($this->collLocationsRelatedByJUnit === null) {
			$this->collLocationsRelatedByJUnit = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByJUnit from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLocationsRelatedByJUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByJUnit === null) {
			if ($this->isNew()) {
			   $this->collLocationsRelatedByJUnit = array();
			} else {

				$criteria->add(LocationPeer::J_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				$this->collLocationsRelatedByJUnit = LocationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LocationPeer::J_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				if (!isset($this->lastLocationRelatedByJUnitCriteria) || !$this->lastLocationRelatedByJUnitCriteria->equals($criteria)) {
					$this->collLocationsRelatedByJUnit = LocationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLocationRelatedByJUnitCriteria = $criteria;
		return $this->collLocationsRelatedByJUnit;
	}

	/**
	 * Returns the number of related LocationsRelatedByJUnit.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLocationsRelatedByJUnit($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(LocationPeer::J_UNIT, $this->getId());

		return LocationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Location object to this object
	 * through the Location foreign key attribute
	 *
	 * @param      Location $l Location
	 * @return     void
	 * @throws     PropelException
	 */
	public function addLocationRelatedByJUnit(Location $l)
	{
		$this->collLocationsRelatedByJUnit[] = $l;
		$l->setMeasurementUnitRelatedByJUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByJUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByJUnitJoinCoordinateSpace($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByJUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByJUnit = array();
			} else {

				$criteria->add(LocationPeer::J_UNIT, $this->getId());

				$this->collLocationsRelatedByJUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::J_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByJUnitCriteria) || !$this->lastLocationRelatedByJUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByJUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		}
		$this->lastLocationRelatedByJUnitCriteria = $criteria;

		return $this->collLocationsRelatedByJUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByJUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByJUnitJoinLocationPlan($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByJUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByJUnit = array();
			} else {

				$criteria->add(LocationPeer::J_UNIT, $this->getId());

				$this->collLocationsRelatedByJUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::J_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByJUnitCriteria) || !$this->lastLocationRelatedByJUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByJUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		}
		$this->lastLocationRelatedByJUnitCriteria = $criteria;

		return $this->collLocationsRelatedByJUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByJUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByJUnitJoinSensorType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByJUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByJUnit = array();
			} else {

				$criteria->add(LocationPeer::J_UNIT, $this->getId());

				$this->collLocationsRelatedByJUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::J_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByJUnitCriteria) || !$this->lastLocationRelatedByJUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByJUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByJUnitCriteria = $criteria;

		return $this->collLocationsRelatedByJUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByJUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByJUnitJoinSourceType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByJUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByJUnit = array();
			} else {

				$criteria->add(LocationPeer::J_UNIT, $this->getId());

				$this->collLocationsRelatedByJUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::J_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByJUnitCriteria) || !$this->lastLocationRelatedByJUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByJUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByJUnitCriteria = $criteria;

		return $this->collLocationsRelatedByJUnit;
	}

	/**
	 * Temporary storage of collLocationsRelatedByYUnit to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLocationsRelatedByYUnit()
	{
		if ($this->collLocationsRelatedByYUnit === null) {
			$this->collLocationsRelatedByYUnit = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByYUnit from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLocationsRelatedByYUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByYUnit === null) {
			if ($this->isNew()) {
			   $this->collLocationsRelatedByYUnit = array();
			} else {

				$criteria->add(LocationPeer::Y_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				$this->collLocationsRelatedByYUnit = LocationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LocationPeer::Y_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				if (!isset($this->lastLocationRelatedByYUnitCriteria) || !$this->lastLocationRelatedByYUnitCriteria->equals($criteria)) {
					$this->collLocationsRelatedByYUnit = LocationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLocationRelatedByYUnitCriteria = $criteria;
		return $this->collLocationsRelatedByYUnit;
	}

	/**
	 * Returns the number of related LocationsRelatedByYUnit.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLocationsRelatedByYUnit($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(LocationPeer::Y_UNIT, $this->getId());

		return LocationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Location object to this object
	 * through the Location foreign key attribute
	 *
	 * @param      Location $l Location
	 * @return     void
	 * @throws     PropelException
	 */
	public function addLocationRelatedByYUnit(Location $l)
	{
		$this->collLocationsRelatedByYUnit[] = $l;
		$l->setMeasurementUnitRelatedByYUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByYUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByYUnitJoinCoordinateSpace($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByYUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByYUnit = array();
			} else {

				$criteria->add(LocationPeer::Y_UNIT, $this->getId());

				$this->collLocationsRelatedByYUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::Y_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByYUnitCriteria) || !$this->lastLocationRelatedByYUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByYUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		}
		$this->lastLocationRelatedByYUnitCriteria = $criteria;

		return $this->collLocationsRelatedByYUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByYUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByYUnitJoinLocationPlan($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByYUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByYUnit = array();
			} else {

				$criteria->add(LocationPeer::Y_UNIT, $this->getId());

				$this->collLocationsRelatedByYUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::Y_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByYUnitCriteria) || !$this->lastLocationRelatedByYUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByYUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		}
		$this->lastLocationRelatedByYUnitCriteria = $criteria;

		return $this->collLocationsRelatedByYUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByYUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByYUnitJoinSensorType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByYUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByYUnit = array();
			} else {

				$criteria->add(LocationPeer::Y_UNIT, $this->getId());

				$this->collLocationsRelatedByYUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::Y_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByYUnitCriteria) || !$this->lastLocationRelatedByYUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByYUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByYUnitCriteria = $criteria;

		return $this->collLocationsRelatedByYUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByYUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByYUnitJoinSourceType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByYUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByYUnit = array();
			} else {

				$criteria->add(LocationPeer::Y_UNIT, $this->getId());

				$this->collLocationsRelatedByYUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::Y_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByYUnitCriteria) || !$this->lastLocationRelatedByYUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByYUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByYUnitCriteria = $criteria;

		return $this->collLocationsRelatedByYUnit;
	}

	/**
	 * Temporary storage of collLocationsRelatedByXUnit to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLocationsRelatedByXUnit()
	{
		if ($this->collLocationsRelatedByXUnit === null) {
			$this->collLocationsRelatedByXUnit = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByXUnit from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLocationsRelatedByXUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByXUnit === null) {
			if ($this->isNew()) {
			   $this->collLocationsRelatedByXUnit = array();
			} else {

				$criteria->add(LocationPeer::X_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				$this->collLocationsRelatedByXUnit = LocationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LocationPeer::X_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				if (!isset($this->lastLocationRelatedByXUnitCriteria) || !$this->lastLocationRelatedByXUnitCriteria->equals($criteria)) {
					$this->collLocationsRelatedByXUnit = LocationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLocationRelatedByXUnitCriteria = $criteria;
		return $this->collLocationsRelatedByXUnit;
	}

	/**
	 * Returns the number of related LocationsRelatedByXUnit.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLocationsRelatedByXUnit($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(LocationPeer::X_UNIT, $this->getId());

		return LocationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Location object to this object
	 * through the Location foreign key attribute
	 *
	 * @param      Location $l Location
	 * @return     void
	 * @throws     PropelException
	 */
	public function addLocationRelatedByXUnit(Location $l)
	{
		$this->collLocationsRelatedByXUnit[] = $l;
		$l->setMeasurementUnitRelatedByXUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByXUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByXUnitJoinCoordinateSpace($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByXUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByXUnit = array();
			} else {

				$criteria->add(LocationPeer::X_UNIT, $this->getId());

				$this->collLocationsRelatedByXUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::X_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByXUnitCriteria) || !$this->lastLocationRelatedByXUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByXUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		}
		$this->lastLocationRelatedByXUnitCriteria = $criteria;

		return $this->collLocationsRelatedByXUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByXUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByXUnitJoinLocationPlan($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByXUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByXUnit = array();
			} else {

				$criteria->add(LocationPeer::X_UNIT, $this->getId());

				$this->collLocationsRelatedByXUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::X_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByXUnitCriteria) || !$this->lastLocationRelatedByXUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByXUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		}
		$this->lastLocationRelatedByXUnitCriteria = $criteria;

		return $this->collLocationsRelatedByXUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByXUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByXUnitJoinSensorType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByXUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByXUnit = array();
			} else {

				$criteria->add(LocationPeer::X_UNIT, $this->getId());

				$this->collLocationsRelatedByXUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::X_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByXUnitCriteria) || !$this->lastLocationRelatedByXUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByXUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByXUnitCriteria = $criteria;

		return $this->collLocationsRelatedByXUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByXUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByXUnitJoinSourceType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByXUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByXUnit = array();
			} else {

				$criteria->add(LocationPeer::X_UNIT, $this->getId());

				$this->collLocationsRelatedByXUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::X_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByXUnitCriteria) || !$this->lastLocationRelatedByXUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByXUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByXUnitCriteria = $criteria;

		return $this->collLocationsRelatedByXUnit;
	}

	/**
	 * Temporary storage of collLocationsRelatedByIUnit to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLocationsRelatedByIUnit()
	{
		if ($this->collLocationsRelatedByIUnit === null) {
			$this->collLocationsRelatedByIUnit = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByIUnit from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLocationsRelatedByIUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByIUnit === null) {
			if ($this->isNew()) {
			   $this->collLocationsRelatedByIUnit = array();
			} else {

				$criteria->add(LocationPeer::I_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				$this->collLocationsRelatedByIUnit = LocationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LocationPeer::I_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				if (!isset($this->lastLocationRelatedByIUnitCriteria) || !$this->lastLocationRelatedByIUnitCriteria->equals($criteria)) {
					$this->collLocationsRelatedByIUnit = LocationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLocationRelatedByIUnitCriteria = $criteria;
		return $this->collLocationsRelatedByIUnit;
	}

	/**
	 * Returns the number of related LocationsRelatedByIUnit.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLocationsRelatedByIUnit($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(LocationPeer::I_UNIT, $this->getId());

		return LocationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Location object to this object
	 * through the Location foreign key attribute
	 *
	 * @param      Location $l Location
	 * @return     void
	 * @throws     PropelException
	 */
	public function addLocationRelatedByIUnit(Location $l)
	{
		$this->collLocationsRelatedByIUnit[] = $l;
		$l->setMeasurementUnitRelatedByIUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByIUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByIUnitJoinCoordinateSpace($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByIUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByIUnit = array();
			} else {

				$criteria->add(LocationPeer::I_UNIT, $this->getId());

				$this->collLocationsRelatedByIUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::I_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByIUnitCriteria) || !$this->lastLocationRelatedByIUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByIUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		}
		$this->lastLocationRelatedByIUnitCriteria = $criteria;

		return $this->collLocationsRelatedByIUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByIUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByIUnitJoinLocationPlan($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByIUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByIUnit = array();
			} else {

				$criteria->add(LocationPeer::I_UNIT, $this->getId());

				$this->collLocationsRelatedByIUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::I_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByIUnitCriteria) || !$this->lastLocationRelatedByIUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByIUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		}
		$this->lastLocationRelatedByIUnitCriteria = $criteria;

		return $this->collLocationsRelatedByIUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByIUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByIUnitJoinSensorType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByIUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByIUnit = array();
			} else {

				$criteria->add(LocationPeer::I_UNIT, $this->getId());

				$this->collLocationsRelatedByIUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::I_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByIUnitCriteria) || !$this->lastLocationRelatedByIUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByIUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByIUnitCriteria = $criteria;

		return $this->collLocationsRelatedByIUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByIUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByIUnitJoinSourceType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByIUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByIUnit = array();
			} else {

				$criteria->add(LocationPeer::I_UNIT, $this->getId());

				$this->collLocationsRelatedByIUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::I_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByIUnitCriteria) || !$this->lastLocationRelatedByIUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByIUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByIUnitCriteria = $criteria;

		return $this->collLocationsRelatedByIUnit;
	}

	/**
	 * Temporary storage of collLocationsRelatedByZUnit to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLocationsRelatedByZUnit()
	{
		if ($this->collLocationsRelatedByZUnit === null) {
			$this->collLocationsRelatedByZUnit = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByZUnit from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLocationsRelatedByZUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByZUnit === null) {
			if ($this->isNew()) {
			   $this->collLocationsRelatedByZUnit = array();
			} else {

				$criteria->add(LocationPeer::Z_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				$this->collLocationsRelatedByZUnit = LocationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LocationPeer::Z_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				if (!isset($this->lastLocationRelatedByZUnitCriteria) || !$this->lastLocationRelatedByZUnitCriteria->equals($criteria)) {
					$this->collLocationsRelatedByZUnit = LocationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLocationRelatedByZUnitCriteria = $criteria;
		return $this->collLocationsRelatedByZUnit;
	}

	/**
	 * Returns the number of related LocationsRelatedByZUnit.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLocationsRelatedByZUnit($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(LocationPeer::Z_UNIT, $this->getId());

		return LocationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Location object to this object
	 * through the Location foreign key attribute
	 *
	 * @param      Location $l Location
	 * @return     void
	 * @throws     PropelException
	 */
	public function addLocationRelatedByZUnit(Location $l)
	{
		$this->collLocationsRelatedByZUnit[] = $l;
		$l->setMeasurementUnitRelatedByZUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByZUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByZUnitJoinCoordinateSpace($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByZUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByZUnit = array();
			} else {

				$criteria->add(LocationPeer::Z_UNIT, $this->getId());

				$this->collLocationsRelatedByZUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::Z_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByZUnitCriteria) || !$this->lastLocationRelatedByZUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByZUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		}
		$this->lastLocationRelatedByZUnitCriteria = $criteria;

		return $this->collLocationsRelatedByZUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByZUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByZUnitJoinLocationPlan($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByZUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByZUnit = array();
			} else {

				$criteria->add(LocationPeer::Z_UNIT, $this->getId());

				$this->collLocationsRelatedByZUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::Z_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByZUnitCriteria) || !$this->lastLocationRelatedByZUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByZUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		}
		$this->lastLocationRelatedByZUnitCriteria = $criteria;

		return $this->collLocationsRelatedByZUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByZUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByZUnitJoinSensorType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByZUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByZUnit = array();
			} else {

				$criteria->add(LocationPeer::Z_UNIT, $this->getId());

				$this->collLocationsRelatedByZUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::Z_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByZUnitCriteria) || !$this->lastLocationRelatedByZUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByZUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByZUnitCriteria = $criteria;

		return $this->collLocationsRelatedByZUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByZUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByZUnitJoinSourceType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByZUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByZUnit = array();
			} else {

				$criteria->add(LocationPeer::Z_UNIT, $this->getId());

				$this->collLocationsRelatedByZUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::Z_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByZUnitCriteria) || !$this->lastLocationRelatedByZUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByZUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByZUnitCriteria = $criteria;

		return $this->collLocationsRelatedByZUnit;
	}

	/**
	 * Temporary storage of collLocationsRelatedByKUnit to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLocationsRelatedByKUnit()
	{
		if ($this->collLocationsRelatedByKUnit === null) {
			$this->collLocationsRelatedByKUnit = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByKUnit from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLocationsRelatedByKUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByKUnit === null) {
			if ($this->isNew()) {
			   $this->collLocationsRelatedByKUnit = array();
			} else {

				$criteria->add(LocationPeer::K_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				$this->collLocationsRelatedByKUnit = LocationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LocationPeer::K_UNIT, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				if (!isset($this->lastLocationRelatedByKUnitCriteria) || !$this->lastLocationRelatedByKUnitCriteria->equals($criteria)) {
					$this->collLocationsRelatedByKUnit = LocationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLocationRelatedByKUnitCriteria = $criteria;
		return $this->collLocationsRelatedByKUnit;
	}

	/**
	 * Returns the number of related LocationsRelatedByKUnit.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLocationsRelatedByKUnit($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(LocationPeer::K_UNIT, $this->getId());

		return LocationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Location object to this object
	 * through the Location foreign key attribute
	 *
	 * @param      Location $l Location
	 * @return     void
	 * @throws     PropelException
	 */
	public function addLocationRelatedByKUnit(Location $l)
	{
		$this->collLocationsRelatedByKUnit[] = $l;
		$l->setMeasurementUnitRelatedByKUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByKUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByKUnitJoinCoordinateSpace($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByKUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByKUnit = array();
			} else {

				$criteria->add(LocationPeer::K_UNIT, $this->getId());

				$this->collLocationsRelatedByKUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::K_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByKUnitCriteria) || !$this->lastLocationRelatedByKUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByKUnit = LocationPeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		}
		$this->lastLocationRelatedByKUnitCriteria = $criteria;

		return $this->collLocationsRelatedByKUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByKUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByKUnitJoinLocationPlan($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByKUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByKUnit = array();
			} else {

				$criteria->add(LocationPeer::K_UNIT, $this->getId());

				$this->collLocationsRelatedByKUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::K_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByKUnitCriteria) || !$this->lastLocationRelatedByKUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByKUnit = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		}
		$this->lastLocationRelatedByKUnitCriteria = $criteria;

		return $this->collLocationsRelatedByKUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByKUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByKUnitJoinSensorType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByKUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByKUnit = array();
			} else {

				$criteria->add(LocationPeer::K_UNIT, $this->getId());

				$this->collLocationsRelatedByKUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::K_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByKUnitCriteria) || !$this->lastLocationRelatedByKUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByKUnit = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByKUnitCriteria = $criteria;

		return $this->collLocationsRelatedByKUnit;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related LocationsRelatedByKUnit from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getLocationsRelatedByKUnitJoinSourceType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationsRelatedByKUnit === null) {
			if ($this->isNew()) {
				$this->collLocationsRelatedByKUnit = array();
			} else {

				$criteria->add(LocationPeer::K_UNIT, $this->getId());

				$this->collLocationsRelatedByKUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::K_UNIT, $this->getId());

			if (!isset($this->lastLocationRelatedByKUnitCriteria) || !$this->lastLocationRelatedByKUnitCriteria->equals($criteria)) {
				$this->collLocationsRelatedByKUnit = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		}
		$this->lastLocationRelatedByKUnitCriteria = $criteria;

		return $this->collLocationsRelatedByKUnit;
	}

	/**
	 * Temporary storage of collMaterialPropertys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initMaterialPropertys()
	{
		if ($this->collMaterialPropertys === null) {
			$this->collMaterialPropertys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related MaterialPropertys from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getMaterialPropertys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialPropertys === null) {
			if ($this->isNew()) {
			   $this->collMaterialPropertys = array();
			} else {

				$criteria->add(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

				MaterialPropertyPeer::addSelectColumns($criteria);
				$this->collMaterialPropertys = MaterialPropertyPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

				MaterialPropertyPeer::addSelectColumns($criteria);
				if (!isset($this->lastMaterialPropertyCriteria) || !$this->lastMaterialPropertyCriteria->equals($criteria)) {
					$this->collMaterialPropertys = MaterialPropertyPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMaterialPropertyCriteria = $criteria;
		return $this->collMaterialPropertys;
	}

	/**
	 * Returns the number of related MaterialPropertys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countMaterialPropertys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

		return MaterialPropertyPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a MaterialProperty object to this object
	 * through the MaterialProperty foreign key attribute
	 *
	 * @param      MaterialProperty $l MaterialProperty
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMaterialProperty(MaterialProperty $l)
	{
		$this->collMaterialPropertys[] = $l;
		$l->setMeasurementUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related MaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getMaterialPropertysJoinMaterial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialPropertys === null) {
			if ($this->isNew()) {
				$this->collMaterialPropertys = array();
			} else {

				$criteria->add(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMaterial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

			if (!isset($this->lastMaterialPropertyCriteria) || !$this->lastMaterialPropertyCriteria->equals($criteria)) {
				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMaterial($criteria, $con);
			}
		}
		$this->lastMaterialPropertyCriteria = $criteria;

		return $this->collMaterialPropertys;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related MaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getMaterialPropertysJoinMaterialTypeProperty($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialPropertys === null) {
			if ($this->isNew()) {
				$this->collMaterialPropertys = array();
			} else {

				$criteria->add(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMaterialTypeProperty($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

			if (!isset($this->lastMaterialPropertyCriteria) || !$this->lastMaterialPropertyCriteria->equals($criteria)) {
				$this->collMaterialPropertys = MaterialPropertyPeer::doSelectJoinMaterialTypeProperty($criteria, $con);
			}
		}
		$this->lastMaterialPropertyCriteria = $criteria;

		return $this->collMaterialPropertys;
	}

	/**
	 * Temporary storage of collMeasurementUnitsRelatedByBaseUnitId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initMeasurementUnitsRelatedByBaseUnitId()
	{
		if ($this->collMeasurementUnitsRelatedByBaseUnitId === null) {
			$this->collMeasurementUnitsRelatedByBaseUnitId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related MeasurementUnitsRelatedByBaseUnitId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getMeasurementUnitsRelatedByBaseUnitId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMeasurementUnitsRelatedByBaseUnitId === null) {
			if ($this->isNew()) {
			   $this->collMeasurementUnitsRelatedByBaseUnitId = array();
			} else {

				$criteria->add(MeasurementUnitPeer::BASE_UNIT, $this->getId());

				MeasurementUnitPeer::addSelectColumns($criteria);
				$this->collMeasurementUnitsRelatedByBaseUnitId = MeasurementUnitPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MeasurementUnitPeer::BASE_UNIT, $this->getId());

				MeasurementUnitPeer::addSelectColumns($criteria);
				if (!isset($this->lastMeasurementUnitRelatedByBaseUnitIdCriteria) || !$this->lastMeasurementUnitRelatedByBaseUnitIdCriteria->equals($criteria)) {
					$this->collMeasurementUnitsRelatedByBaseUnitId = MeasurementUnitPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMeasurementUnitRelatedByBaseUnitIdCriteria = $criteria;
		return $this->collMeasurementUnitsRelatedByBaseUnitId;
	}

	/**
	 * Returns the number of related MeasurementUnitsRelatedByBaseUnitId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countMeasurementUnitsRelatedByBaseUnitId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(MeasurementUnitPeer::BASE_UNIT, $this->getId());

		return MeasurementUnitPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a MeasurementUnit object to this object
	 * through the MeasurementUnit foreign key attribute
	 *
	 * @param      MeasurementUnit $l MeasurementUnit
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMeasurementUnitRelatedByBaseUnitId(MeasurementUnit $l)
	{
		$this->collMeasurementUnitsRelatedByBaseUnitId[] = $l;
		$l->setMeasurementUnitRelatedByBaseUnitId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related MeasurementUnitsRelatedByBaseUnitId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getMeasurementUnitsRelatedByBaseUnitIdJoinMeasurementUnitCategory($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMeasurementUnitsRelatedByBaseUnitId === null) {
			if ($this->isNew()) {
				$this->collMeasurementUnitsRelatedByBaseUnitId = array();
			} else {

				$criteria->add(MeasurementUnitPeer::BASE_UNIT, $this->getId());

				$this->collMeasurementUnitsRelatedByBaseUnitId = MeasurementUnitPeer::doSelectJoinMeasurementUnitCategory($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MeasurementUnitPeer::BASE_UNIT, $this->getId());

			if (!isset($this->lastMeasurementUnitRelatedByBaseUnitIdCriteria) || !$this->lastMeasurementUnitRelatedByBaseUnitIdCriteria->equals($criteria)) {
				$this->collMeasurementUnitsRelatedByBaseUnitId = MeasurementUnitPeer::doSelectJoinMeasurementUnitCategory($criteria, $con);
			}
		}
		$this->lastMeasurementUnitRelatedByBaseUnitIdCriteria = $criteria;

		return $this->collMeasurementUnitsRelatedByBaseUnitId;
	}

	/**
	 * Temporary storage of collMeasurementUnitConversionsRelatedByToId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initMeasurementUnitConversionsRelatedByToId()
	{
		if ($this->collMeasurementUnitConversionsRelatedByToId === null) {
			$this->collMeasurementUnitConversionsRelatedByToId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related MeasurementUnitConversionsRelatedByToId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getMeasurementUnitConversionsRelatedByToId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMeasurementUnitConversionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMeasurementUnitConversionsRelatedByToId === null) {
			if ($this->isNew()) {
			   $this->collMeasurementUnitConversionsRelatedByToId = array();
			} else {

				$criteria->add(MeasurementUnitConversionPeer::TO_ID, $this->getId());

				MeasurementUnitConversionPeer::addSelectColumns($criteria);
				$this->collMeasurementUnitConversionsRelatedByToId = MeasurementUnitConversionPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MeasurementUnitConversionPeer::TO_ID, $this->getId());

				MeasurementUnitConversionPeer::addSelectColumns($criteria);
				if (!isset($this->lastMeasurementUnitConversionRelatedByToIdCriteria) || !$this->lastMeasurementUnitConversionRelatedByToIdCriteria->equals($criteria)) {
					$this->collMeasurementUnitConversionsRelatedByToId = MeasurementUnitConversionPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMeasurementUnitConversionRelatedByToIdCriteria = $criteria;
		return $this->collMeasurementUnitConversionsRelatedByToId;
	}

	/**
	 * Returns the number of related MeasurementUnitConversionsRelatedByToId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countMeasurementUnitConversionsRelatedByToId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMeasurementUnitConversionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(MeasurementUnitConversionPeer::TO_ID, $this->getId());

		return MeasurementUnitConversionPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a MeasurementUnitConversion object to this object
	 * through the MeasurementUnitConversion foreign key attribute
	 *
	 * @param      MeasurementUnitConversion $l MeasurementUnitConversion
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMeasurementUnitConversionRelatedByToId(MeasurementUnitConversion $l)
	{
		$this->collMeasurementUnitConversionsRelatedByToId[] = $l;
		$l->setMeasurementUnitRelatedByToId($this);
	}

	/**
	 * Temporary storage of collMeasurementUnitConversionsRelatedByFromId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initMeasurementUnitConversionsRelatedByFromId()
	{
		if ($this->collMeasurementUnitConversionsRelatedByFromId === null) {
			$this->collMeasurementUnitConversionsRelatedByFromId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related MeasurementUnitConversionsRelatedByFromId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getMeasurementUnitConversionsRelatedByFromId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMeasurementUnitConversionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMeasurementUnitConversionsRelatedByFromId === null) {
			if ($this->isNew()) {
			   $this->collMeasurementUnitConversionsRelatedByFromId = array();
			} else {

				$criteria->add(MeasurementUnitConversionPeer::FROM_ID, $this->getId());

				MeasurementUnitConversionPeer::addSelectColumns($criteria);
				$this->collMeasurementUnitConversionsRelatedByFromId = MeasurementUnitConversionPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MeasurementUnitConversionPeer::FROM_ID, $this->getId());

				MeasurementUnitConversionPeer::addSelectColumns($criteria);
				if (!isset($this->lastMeasurementUnitConversionRelatedByFromIdCriteria) || !$this->lastMeasurementUnitConversionRelatedByFromIdCriteria->equals($criteria)) {
					$this->collMeasurementUnitConversionsRelatedByFromId = MeasurementUnitConversionPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMeasurementUnitConversionRelatedByFromIdCriteria = $criteria;
		return $this->collMeasurementUnitConversionsRelatedByFromId;
	}

	/**
	 * Returns the number of related MeasurementUnitConversionsRelatedByFromId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countMeasurementUnitConversionsRelatedByFromId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMeasurementUnitConversionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(MeasurementUnitConversionPeer::FROM_ID, $this->getId());

		return MeasurementUnitConversionPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a MeasurementUnitConversion object to this object
	 * through the MeasurementUnitConversion foreign key attribute
	 *
	 * @param      MeasurementUnitConversion $l MeasurementUnitConversion
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMeasurementUnitConversionRelatedByFromId(MeasurementUnitConversion $l)
	{
		$this->collMeasurementUnitConversionsRelatedByFromId[] = $l;
		$l->setMeasurementUnitRelatedByFromId($this);
	}

	/**
	 * Temporary storage of collSensorModelsRelatedByMeasuredValueUnitsId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorModelsRelatedByMeasuredValueUnitsId()
	{
		if ($this->collSensorModelsRelatedByMeasuredValueUnitsId === null) {
			$this->collSensorModelsRelatedByMeasuredValueUnitsId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related SensorModelsRelatedByMeasuredValueUnitsId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorModelsRelatedByMeasuredValueUnitsId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModelsRelatedByMeasuredValueUnitsId === null) {
			if ($this->isNew()) {
			   $this->collSensorModelsRelatedByMeasuredValueUnitsId = array();
			} else {

				$criteria->add(SensorModelPeer::MEASURED_VALUE_UNITS_ID, $this->getId());

				SensorModelPeer::addSelectColumns($criteria);
				$this->collSensorModelsRelatedByMeasuredValueUnitsId = SensorModelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorModelPeer::MEASURED_VALUE_UNITS_ID, $this->getId());

				SensorModelPeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorModelRelatedByMeasuredValueUnitsIdCriteria) || !$this->lastSensorModelRelatedByMeasuredValueUnitsIdCriteria->equals($criteria)) {
					$this->collSensorModelsRelatedByMeasuredValueUnitsId = SensorModelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorModelRelatedByMeasuredValueUnitsIdCriteria = $criteria;
		return $this->collSensorModelsRelatedByMeasuredValueUnitsId;
	}

	/**
	 * Returns the number of related SensorModelsRelatedByMeasuredValueUnitsId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorModelsRelatedByMeasuredValueUnitsId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorModelPeer::MEASURED_VALUE_UNITS_ID, $this->getId());

		return SensorModelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorModel object to this object
	 * through the SensorModel foreign key attribute
	 *
	 * @param      SensorModel $l SensorModel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorModelRelatedByMeasuredValueUnitsId(SensorModel $l)
	{
		$this->collSensorModelsRelatedByMeasuredValueUnitsId[] = $l;
		$l->setMeasurementUnitRelatedByMeasuredValueUnitsId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related SensorModelsRelatedByMeasuredValueUnitsId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getSensorModelsRelatedByMeasuredValueUnitsIdJoinSensorType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModelsRelatedByMeasuredValueUnitsId === null) {
			if ($this->isNew()) {
				$this->collSensorModelsRelatedByMeasuredValueUnitsId = array();
			} else {

				$criteria->add(SensorModelPeer::MEASURED_VALUE_UNITS_ID, $this->getId());

				$this->collSensorModelsRelatedByMeasuredValueUnitsId = SensorModelPeer::doSelectJoinSensorType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorModelPeer::MEASURED_VALUE_UNITS_ID, $this->getId());

			if (!isset($this->lastSensorModelRelatedByMeasuredValueUnitsIdCriteria) || !$this->lastSensorModelRelatedByMeasuredValueUnitsIdCriteria->equals($criteria)) {
				$this->collSensorModelsRelatedByMeasuredValueUnitsId = SensorModelPeer::doSelectJoinSensorType($criteria, $con);
			}
		}
		$this->lastSensorModelRelatedByMeasuredValueUnitsIdCriteria = $criteria;

		return $this->collSensorModelsRelatedByMeasuredValueUnitsId;
	}

	/**
	 * Temporary storage of collSensorModelsRelatedBySensitivityUnitsId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorModelsRelatedBySensitivityUnitsId()
	{
		if ($this->collSensorModelsRelatedBySensitivityUnitsId === null) {
			$this->collSensorModelsRelatedBySensitivityUnitsId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related SensorModelsRelatedBySensitivityUnitsId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorModelsRelatedBySensitivityUnitsId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModelsRelatedBySensitivityUnitsId === null) {
			if ($this->isNew()) {
			   $this->collSensorModelsRelatedBySensitivityUnitsId = array();
			} else {

				$criteria->add(SensorModelPeer::SENSITIVITY_UNITS_ID, $this->getId());

				SensorModelPeer::addSelectColumns($criteria);
				$this->collSensorModelsRelatedBySensitivityUnitsId = SensorModelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorModelPeer::SENSITIVITY_UNITS_ID, $this->getId());

				SensorModelPeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorModelRelatedBySensitivityUnitsIdCriteria) || !$this->lastSensorModelRelatedBySensitivityUnitsIdCriteria->equals($criteria)) {
					$this->collSensorModelsRelatedBySensitivityUnitsId = SensorModelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorModelRelatedBySensitivityUnitsIdCriteria = $criteria;
		return $this->collSensorModelsRelatedBySensitivityUnitsId;
	}

	/**
	 * Returns the number of related SensorModelsRelatedBySensitivityUnitsId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorModelsRelatedBySensitivityUnitsId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorModelPeer::SENSITIVITY_UNITS_ID, $this->getId());

		return SensorModelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorModel object to this object
	 * through the SensorModel foreign key attribute
	 *
	 * @param      SensorModel $l SensorModel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorModelRelatedBySensitivityUnitsId(SensorModel $l)
	{
		$this->collSensorModelsRelatedBySensitivityUnitsId[] = $l;
		$l->setMeasurementUnitRelatedBySensitivityUnitsId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related SensorModelsRelatedBySensitivityUnitsId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getSensorModelsRelatedBySensitivityUnitsIdJoinSensorType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModelsRelatedBySensitivityUnitsId === null) {
			if ($this->isNew()) {
				$this->collSensorModelsRelatedBySensitivityUnitsId = array();
			} else {

				$criteria->add(SensorModelPeer::SENSITIVITY_UNITS_ID, $this->getId());

				$this->collSensorModelsRelatedBySensitivityUnitsId = SensorModelPeer::doSelectJoinSensorType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorModelPeer::SENSITIVITY_UNITS_ID, $this->getId());

			if (!isset($this->lastSensorModelRelatedBySensitivityUnitsIdCriteria) || !$this->lastSensorModelRelatedBySensitivityUnitsIdCriteria->equals($criteria)) {
				$this->collSensorModelsRelatedBySensitivityUnitsId = SensorModelPeer::doSelectJoinSensorType($criteria, $con);
			}
		}
		$this->lastSensorModelRelatedBySensitivityUnitsIdCriteria = $criteria;

		return $this->collSensorModelsRelatedBySensitivityUnitsId;
	}

	/**
	 * Temporary storage of collSensorModelsRelatedByTempUnitsId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorModelsRelatedByTempUnitsId()
	{
		if ($this->collSensorModelsRelatedByTempUnitsId === null) {
			$this->collSensorModelsRelatedByTempUnitsId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related SensorModelsRelatedByTempUnitsId from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorModelsRelatedByTempUnitsId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModelsRelatedByTempUnitsId === null) {
			if ($this->isNew()) {
			   $this->collSensorModelsRelatedByTempUnitsId = array();
			} else {

				$criteria->add(SensorModelPeer::TEMP_UNITS_ID, $this->getId());

				SensorModelPeer::addSelectColumns($criteria);
				$this->collSensorModelsRelatedByTempUnitsId = SensorModelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorModelPeer::TEMP_UNITS_ID, $this->getId());

				SensorModelPeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorModelRelatedByTempUnitsIdCriteria) || !$this->lastSensorModelRelatedByTempUnitsIdCriteria->equals($criteria)) {
					$this->collSensorModelsRelatedByTempUnitsId = SensorModelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorModelRelatedByTempUnitsIdCriteria = $criteria;
		return $this->collSensorModelsRelatedByTempUnitsId;
	}

	/**
	 * Returns the number of related SensorModelsRelatedByTempUnitsId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorModelsRelatedByTempUnitsId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorModelPeer::TEMP_UNITS_ID, $this->getId());

		return SensorModelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorModel object to this object
	 * through the SensorModel foreign key attribute
	 *
	 * @param      SensorModel $l SensorModel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorModelRelatedByTempUnitsId(SensorModel $l)
	{
		$this->collSensorModelsRelatedByTempUnitsId[] = $l;
		$l->setMeasurementUnitRelatedByTempUnitsId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related SensorModelsRelatedByTempUnitsId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getSensorModelsRelatedByTempUnitsIdJoinSensorType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModelsRelatedByTempUnitsId === null) {
			if ($this->isNew()) {
				$this->collSensorModelsRelatedByTempUnitsId = array();
			} else {

				$criteria->add(SensorModelPeer::TEMP_UNITS_ID, $this->getId());

				$this->collSensorModelsRelatedByTempUnitsId = SensorModelPeer::doSelectJoinSensorType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorModelPeer::TEMP_UNITS_ID, $this->getId());

			if (!isset($this->lastSensorModelRelatedByTempUnitsIdCriteria) || !$this->lastSensorModelRelatedByTempUnitsIdCriteria->equals($criteria)) {
				$this->collSensorModelsRelatedByTempUnitsId = SensorModelPeer::doSelectJoinSensorType($criteria, $con);
			}
		}
		$this->lastSensorModelRelatedByTempUnitsIdCriteria = $criteria;

		return $this->collSensorModelsRelatedByTempUnitsId;
	}

	/**
	 * Temporary storage of collTrials to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTrials()
	{
		if ($this->collTrials === null) {
			$this->collTrials = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related Trials from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTrials($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseTrialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTrials === null) {
			if ($this->isNew()) {
			   $this->collTrials = array();
			} else {

				$criteria->add(TrialPeer::BASE_ACCELERATION_UNIT_ID, $this->getId());

				TrialPeer::addSelectColumns($criteria);
				$this->collTrials = TrialPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TrialPeer::BASE_ACCELERATION_UNIT_ID, $this->getId());

				TrialPeer::addSelectColumns($criteria);
				if (!isset($this->lastTrialCriteria) || !$this->lastTrialCriteria->equals($criteria)) {
					$this->collTrials = TrialPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTrialCriteria = $criteria;
		return $this->collTrials;
	}

	/**
	 * Returns the number of related Trials.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTrials($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseTrialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TrialPeer::BASE_ACCELERATION_UNIT_ID, $this->getId());

		return TrialPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Trial object to this object
	 * through the Trial foreign key attribute
	 *
	 * @param      Trial $l Trial
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTrial(Trial $l)
	{
		$this->collTrials[] = $l;
		$l->setMeasurementUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related Trials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getTrialsJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseTrialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTrials === null) {
			if ($this->isNew()) {
				$this->collTrials = array();
			} else {

				$criteria->add(TrialPeer::BASE_ACCELERATION_UNIT_ID, $this->getId());

				$this->collTrials = TrialPeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(TrialPeer::BASE_ACCELERATION_UNIT_ID, $this->getId());

			if (!isset($this->lastTrialCriteria) || !$this->lastTrialCriteria->equals($criteria)) {
				$this->collTrials = TrialPeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastTrialCriteria = $criteria;

		return $this->collTrials;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related Trials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getTrialsJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseTrialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTrials === null) {
			if ($this->isNew()) {
				$this->collTrials = array();
			} else {

				$criteria->add(TrialPeer::BASE_ACCELERATION_UNIT_ID, $this->getId());

				$this->collTrials = TrialPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(TrialPeer::BASE_ACCELERATION_UNIT_ID, $this->getId());

			if (!isset($this->lastTrialCriteria) || !$this->lastTrialCriteria->equals($criteria)) {
				$this->collTrials = TrialPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastTrialCriteria = $criteria;

		return $this->collTrials;
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
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related SpecimenComponentAttributes from storage.
	 * If this MeasurementUnit is new, it will return
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

				$criteria->add(SpecimenComponentAttributePeer::UNIT_ID, $this->getId());

				SpecimenComponentAttributePeer::addSelectColumns($criteria);
				$this->collSpecimenComponentAttributes = SpecimenComponentAttributePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentAttributePeer::UNIT_ID, $this->getId());

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

		$criteria->add(SpecimenComponentAttributePeer::UNIT_ID, $this->getId());

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
		$l->setMeasurementUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related SpecimenComponentAttributes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getSpecimenComponentAttributesJoinSpecimenComponent($criteria = null, $con = null)
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

				$criteria->add(SpecimenComponentAttributePeer::UNIT_ID, $this->getId());

				$this->collSpecimenComponentAttributes = SpecimenComponentAttributePeer::doSelectJoinSpecimenComponent($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentAttributePeer::UNIT_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentAttributeCriteria) || !$this->lastSpecimenComponentAttributeCriteria->equals($criteria)) {
				$this->collSpecimenComponentAttributes = SpecimenComponentAttributePeer::doSelectJoinSpecimenComponent($criteria, $con);
			}
		}
		$this->lastSpecimenComponentAttributeCriteria = $criteria;

		return $this->collSpecimenComponentAttributes;
	}

	/**
	 * Temporary storage of collSpecimenComponentMaterialPropertys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSpecimenComponentMaterialPropertys()
	{
		if ($this->collSpecimenComponentMaterialPropertys === null) {
			$this->collSpecimenComponentMaterialPropertys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialPropertys from storage.
	 * If this MeasurementUnit is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSpecimenComponentMaterialPropertys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialPropertys === null) {
			if ($this->isNew()) {
			   $this->collSpecimenComponentMaterialPropertys = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

				SpecimenComponentMaterialPropertyPeer::addSelectColumns($criteria);
				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentMaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

				SpecimenComponentMaterialPropertyPeer::addSelectColumns($criteria);
				if (!isset($this->lastSpecimenComponentMaterialPropertyCriteria) || !$this->lastSpecimenComponentMaterialPropertyCriteria->equals($criteria)) {
					$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSpecimenComponentMaterialPropertyCriteria = $criteria;
		return $this->collSpecimenComponentMaterialPropertys;
	}

	/**
	 * Returns the number of related SpecimenComponentMaterialPropertys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSpecimenComponentMaterialPropertys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SpecimenComponentMaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

		return SpecimenComponentMaterialPropertyPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SpecimenComponentMaterialProperty object to this object
	 * through the SpecimenComponentMaterialProperty foreign key attribute
	 *
	 * @param      SpecimenComponentMaterialProperty $l SpecimenComponentMaterialProperty
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSpecimenComponentMaterialProperty(SpecimenComponentMaterialProperty $l)
	{
		$this->collSpecimenComponentMaterialPropertys[] = $l;
		$l->setMeasurementUnit($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getSpecimenComponentMaterialPropertysJoinSpecimenComponentMaterial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialPropertys === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentMaterialPropertys = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinSpecimenComponentMaterial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialPropertyCriteria) || !$this->lastSpecimenComponentMaterialPropertyCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinSpecimenComponentMaterial($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialPropertyCriteria = $criteria;

		return $this->collSpecimenComponentMaterialPropertys;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this MeasurementUnit is new, it will return
	 * an empty collection; or if this MeasurementUnit has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialPropertys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in MeasurementUnit.
	 */
	public function getSpecimenComponentMaterialPropertysJoinMaterialTypeProperty($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialPropertyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialPropertys === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentMaterialPropertys = array();
			} else {

				$criteria->add(SpecimenComponentMaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinMaterialTypeProperty($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialPropertyPeer::MEASUREMENT_UNIT_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialPropertyCriteria) || !$this->lastSpecimenComponentMaterialPropertyCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterialPropertys = SpecimenComponentMaterialPropertyPeer::doSelectJoinMaterialTypeProperty($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialPropertyCriteria = $criteria;

		return $this->collSpecimenComponentMaterialPropertys;
	}

} // BaseMeasurementUnit
