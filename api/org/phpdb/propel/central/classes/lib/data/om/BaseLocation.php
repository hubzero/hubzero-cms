<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/LocationPeer.php';

/**
 * Base class that represents a row from the 'LOCATION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseLocation extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        LocationPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the comments field.
	 * @var        string
	 */
	protected $comments;


	/**
	 * The value for the coordinate_space_id field.
	 * @var        double
	 */
	protected $coordinate_space_id;


	/**
	 * The value for the i field.
	 * @var        double
	 */
	protected $i;


	/**
	 * The value for the i_unit field.
	 * @var        double
	 */
	protected $i_unit;


	/**
	 * The value for the j field.
	 * @var        double
	 */
	protected $j;


	/**
	 * The value for the j_unit field.
	 * @var        double
	 */
	protected $j_unit;


	/**
	 * The value for the k field.
	 * @var        double
	 */
	protected $k;


	/**
	 * The value for the k_unit field.
	 * @var        double
	 */
	protected $k_unit;


	/**
	 * The value for the label field.
	 * @var        string
	 */
	protected $label;


	/**
	 * The value for the location_type_id field.
	 * @var        double
	 */
	protected $location_type_id = 0;


	/**
	 * The value for the plan_id field.
	 * @var        double
	 */
	protected $plan_id;


	/**
	 * The value for the sensor_type_id field.
	 * @var        double
	 */
	protected $sensor_type_id;


	/**
	 * The value for the source_type_id field.
	 * @var        double
	 */
	protected $source_type_id;


	/**
	 * The value for the x field.
	 * @var        double
	 */
	protected $x;


	/**
	 * The value for the x_unit field.
	 * @var        double
	 */
	protected $x_unit;


	/**
	 * The value for the y field.
	 * @var        double
	 */
	protected $y;


	/**
	 * The value for the y_unit field.
	 * @var        double
	 */
	protected $y_unit;


	/**
	 * The value for the z field.
	 * @var        double
	 */
	protected $z;


	/**
	 * The value for the z_unit field.
	 * @var        double
	 */
	protected $z_unit;

	/**
	 * @var        CoordinateSpace
	 */
	protected $aCoordinateSpace;

	/**
	 * @var        LocationPlan
	 */
	protected $aLocationPlan;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByJUnit;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByYUnit;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByXUnit;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByIUnit;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByZUnit;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByKUnit;

	/**
	 * @var        SensorType
	 */
	protected $aSensorType;

	/**
	 * @var        SourceType
	 */
	protected $aSourceType;

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
	 * Collection to store aggregation of collDAQChannels.
	 * @var        array
	 */
	protected $collDAQChannels;

	/**
	 * The criteria used to select the current contents of collDAQChannels.
	 * @var        Criteria
	 */
	protected $lastDAQChannelCriteria = null;

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
	 * Get the [comments] column value.
	 * 
	 * @return     string
	 */
	public function getComment()
	{

		return $this->comments;
	}

	/**
	 * Get the [coordinate_space_id] column value.
	 * 
	 * @return     double
	 */
	public function getCoordinateSpaceId()
	{

		return $this->coordinate_space_id;
	}

	/**
	 * Get the [i] column value.
	 * 
	 * @return     double
	 */
	public function getI()
	{

		return $this->i;
	}

	/**
	 * Get the [i_unit] column value.
	 * 
	 * @return     double
	 */
	public function getIUnit()
	{

		return $this->i_unit;
	}

	/**
	 * Get the [j] column value.
	 * 
	 * @return     double
	 */
	public function getJ()
	{

		return $this->j;
	}

	/**
	 * Get the [j_unit] column value.
	 * 
	 * @return     double
	 */
	public function getJUnit()
	{

		return $this->j_unit;
	}

	/**
	 * Get the [k] column value.
	 * 
	 * @return     double
	 */
	public function getK()
	{

		return $this->k;
	}

	/**
	 * Get the [k_unit] column value.
	 * 
	 * @return     double
	 */
	public function getKUnit()
	{

		return $this->k_unit;
	}

	/**
	 * Get the [label] column value.
	 * 
	 * @return     string
	 */
	public function getLabel()
	{

		return $this->label;
	}

	/**
	 * Get the [location_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getLocationTypeId()
	{

		return $this->location_type_id;
	}

	/**
	 * Get the [plan_id] column value.
	 * 
	 * @return     double
	 */
	public function getPlanId()
	{

		return $this->plan_id;
	}

	/**
	 * Get the [sensor_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getSensorTypeId()
	{

		return $this->sensor_type_id;
	}

	/**
	 * Get the [source_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getSourceTypeId()
	{

		return $this->source_type_id;
	}

	/**
	 * Get the [x] column value.
	 * 
	 * @return     double
	 */
	public function getX()
	{

		return $this->x;
	}

	/**
	 * Get the [x_unit] column value.
	 * 
	 * @return     double
	 */
	public function getXUnit()
	{

		return $this->x_unit;
	}

	/**
	 * Get the [y] column value.
	 * 
	 * @return     double
	 */
	public function getY()
	{

		return $this->y;
	}

	/**
	 * Get the [y_unit] column value.
	 * 
	 * @return     double
	 */
	public function getYUnit()
	{

		return $this->y_unit;
	}

	/**
	 * Get the [z] column value.
	 * 
	 * @return     double
	 */
	public function getZ()
	{

		return $this->z;
	}

	/**
	 * Get the [z_unit] column value.
	 * 
	 * @return     double
	 */
	public function getZUnit()
	{

		return $this->z_unit;
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
			$this->modifiedColumns[] = LocationPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [comments] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setComment($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->comments !== $v) {
			$this->comments = $v;
			$this->modifiedColumns[] = LocationPeer::COMMENTS;
		}

	} // setComment()

	/**
	 * Set the value of [coordinate_space_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCoordinateSpaceId($v)
	{

		if ($this->coordinate_space_id !== $v) {
			$this->coordinate_space_id = $v;
			$this->modifiedColumns[] = LocationPeer::COORDINATE_SPACE_ID;
		}

		if ($this->aCoordinateSpace !== null && $this->aCoordinateSpace->getId() !== $v) {
			$this->aCoordinateSpace = null;
		}

	} // setCoordinateSpaceId()

	/**
	 * Set the value of [i] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setI($v)
	{

		if ($this->i !== $v) {
			$this->i = $v;
			$this->modifiedColumns[] = LocationPeer::I;
		}

	} // setI()

	/**
	 * Set the value of [i_unit] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setIUnit($v)
	{

		if ($this->i_unit !== $v) {
			$this->i_unit = $v;
			$this->modifiedColumns[] = LocationPeer::I_UNIT;
		}

		if ($this->aMeasurementUnitRelatedByIUnit !== null && $this->aMeasurementUnitRelatedByIUnit->getId() !== $v) {
			$this->aMeasurementUnitRelatedByIUnit = null;
		}

	} // setIUnit()

	/**
	 * Set the value of [j] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setJ($v)
	{

		if ($this->j !== $v) {
			$this->j = $v;
			$this->modifiedColumns[] = LocationPeer::J;
		}

	} // setJ()

	/**
	 * Set the value of [j_unit] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setJUnit($v)
	{

		if ($this->j_unit !== $v) {
			$this->j_unit = $v;
			$this->modifiedColumns[] = LocationPeer::J_UNIT;
		}

		if ($this->aMeasurementUnitRelatedByJUnit !== null && $this->aMeasurementUnitRelatedByJUnit->getId() !== $v) {
			$this->aMeasurementUnitRelatedByJUnit = null;
		}

	} // setJUnit()

	/**
	 * Set the value of [k] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setK($v)
	{

		if ($this->k !== $v) {
			$this->k = $v;
			$this->modifiedColumns[] = LocationPeer::K;
		}

	} // setK()

	/**
	 * Set the value of [k_unit] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setKUnit($v)
	{

		if ($this->k_unit !== $v) {
			$this->k_unit = $v;
			$this->modifiedColumns[] = LocationPeer::K_UNIT;
		}

		if ($this->aMeasurementUnitRelatedByKUnit !== null && $this->aMeasurementUnitRelatedByKUnit->getId() !== $v) {
			$this->aMeasurementUnitRelatedByKUnit = null;
		}

	} // setKUnit()

	/**
	 * Set the value of [label] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setLabel($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->label !== $v) {
			$this->label = $v;
			$this->modifiedColumns[] = LocationPeer::LABEL;
		}

	} // setLabel()

	/**
	 * Set the value of [location_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setLocationTypeId($v)
	{

		if ($this->location_type_id !== $v || $v === 0) {
			$this->location_type_id = $v;
			$this->modifiedColumns[] = LocationPeer::LOCATION_TYPE_ID;
		}

	} // setLocationTypeId()

	/**
	 * Set the value of [plan_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPlanId($v)
	{

		if ($this->plan_id !== $v) {
			$this->plan_id = $v;
			$this->modifiedColumns[] = LocationPeer::PLAN_ID;
		}

		if ($this->aLocationPlan !== null && $this->aLocationPlan->getId() !== $v) {
			$this->aLocationPlan = null;
		}

	} // setPlanId()

	/**
	 * Set the value of [sensor_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSensorTypeId($v)
	{

		if ($this->sensor_type_id !== $v) {
			$this->sensor_type_id = $v;
			$this->modifiedColumns[] = LocationPeer::SENSOR_TYPE_ID;
		}

		if ($this->aSensorType !== null && $this->aSensorType->getId() !== $v) {
			$this->aSensorType = null;
		}

	} // setSensorTypeId()

	/**
	 * Set the value of [source_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSourceTypeId($v)
	{

		if ($this->source_type_id !== $v) {
			$this->source_type_id = $v;
			$this->modifiedColumns[] = LocationPeer::SOURCE_TYPE_ID;
		}

		if ($this->aSourceType !== null && $this->aSourceType->getId() !== $v) {
			$this->aSourceType = null;
		}

	} // setSourceTypeId()

	/**
	 * Set the value of [x] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setX($v)
	{

		if ($this->x !== $v) {
			$this->x = $v;
			$this->modifiedColumns[] = LocationPeer::X;
		}

	} // setX()

	/**
	 * Set the value of [x_unit] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setXUnit($v)
	{

		if ($this->x_unit !== $v) {
			$this->x_unit = $v;
			$this->modifiedColumns[] = LocationPeer::X_UNIT;
		}

		if ($this->aMeasurementUnitRelatedByXUnit !== null && $this->aMeasurementUnitRelatedByXUnit->getId() !== $v) {
			$this->aMeasurementUnitRelatedByXUnit = null;
		}

	} // setXUnit()

	/**
	 * Set the value of [y] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setY($v)
	{

		if ($this->y !== $v) {
			$this->y = $v;
			$this->modifiedColumns[] = LocationPeer::Y;
		}

	} // setY()

	/**
	 * Set the value of [y_unit] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setYUnit($v)
	{

		if ($this->y_unit !== $v) {
			$this->y_unit = $v;
			$this->modifiedColumns[] = LocationPeer::Y_UNIT;
		}

		if ($this->aMeasurementUnitRelatedByYUnit !== null && $this->aMeasurementUnitRelatedByYUnit->getId() !== $v) {
			$this->aMeasurementUnitRelatedByYUnit = null;
		}

	} // setYUnit()

	/**
	 * Set the value of [z] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setZ($v)
	{

		if ($this->z !== $v) {
			$this->z = $v;
			$this->modifiedColumns[] = LocationPeer::Z;
		}

	} // setZ()

	/**
	 * Set the value of [z_unit] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setZUnit($v)
	{

		if ($this->z_unit !== $v) {
			$this->z_unit = $v;
			$this->modifiedColumns[] = LocationPeer::Z_UNIT;
		}

		if ($this->aMeasurementUnitRelatedByZUnit !== null && $this->aMeasurementUnitRelatedByZUnit->getId() !== $v) {
			$this->aMeasurementUnitRelatedByZUnit = null;
		}

	} // setZUnit()

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

			$this->comments = $rs->getString($startcol + 1);

			$this->coordinate_space_id = $rs->getFloat($startcol + 2);

			$this->i = $rs->getFloat($startcol + 3);

			$this->i_unit = $rs->getFloat($startcol + 4);

			$this->j = $rs->getFloat($startcol + 5);

			$this->j_unit = $rs->getFloat($startcol + 6);

			$this->k = $rs->getFloat($startcol + 7);

			$this->k_unit = $rs->getFloat($startcol + 8);

			$this->label = $rs->getString($startcol + 9);

			$this->location_type_id = $rs->getFloat($startcol + 10);

			$this->plan_id = $rs->getFloat($startcol + 11);

			$this->sensor_type_id = $rs->getFloat($startcol + 12);

			$this->source_type_id = $rs->getFloat($startcol + 13);

			$this->x = $rs->getFloat($startcol + 14);

			$this->x_unit = $rs->getFloat($startcol + 15);

			$this->y = $rs->getFloat($startcol + 16);

			$this->y_unit = $rs->getFloat($startcol + 17);

			$this->z = $rs->getFloat($startcol + 18);

			$this->z_unit = $rs->getFloat($startcol + 19);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 20; // 20 = LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Location object", $e);
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
			$con = Propel::getConnection(LocationPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			LocationPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(LocationPeer::DATABASE_NAME);
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

			if ($this->aCoordinateSpace !== null) {
				if ($this->aCoordinateSpace->isModified()) {
					$affectedRows += $this->aCoordinateSpace->save($con);
				}
				$this->setCoordinateSpace($this->aCoordinateSpace);
			}

			if ($this->aLocationPlan !== null) {
				if ($this->aLocationPlan->isModified()) {
					$affectedRows += $this->aLocationPlan->save($con);
				}
				$this->setLocationPlan($this->aLocationPlan);
			}

			if ($this->aMeasurementUnitRelatedByJUnit !== null) {
				if ($this->aMeasurementUnitRelatedByJUnit->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByJUnit->save($con);
				}
				$this->setMeasurementUnitRelatedByJUnit($this->aMeasurementUnitRelatedByJUnit);
			}

			if ($this->aMeasurementUnitRelatedByYUnit !== null) {
				if ($this->aMeasurementUnitRelatedByYUnit->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByYUnit->save($con);
				}
				$this->setMeasurementUnitRelatedByYUnit($this->aMeasurementUnitRelatedByYUnit);
			}

			if ($this->aMeasurementUnitRelatedByXUnit !== null) {
				if ($this->aMeasurementUnitRelatedByXUnit->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByXUnit->save($con);
				}
				$this->setMeasurementUnitRelatedByXUnit($this->aMeasurementUnitRelatedByXUnit);
			}

			if ($this->aMeasurementUnitRelatedByIUnit !== null) {
				if ($this->aMeasurementUnitRelatedByIUnit->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByIUnit->save($con);
				}
				$this->setMeasurementUnitRelatedByIUnit($this->aMeasurementUnitRelatedByIUnit);
			}

			if ($this->aMeasurementUnitRelatedByZUnit !== null) {
				if ($this->aMeasurementUnitRelatedByZUnit->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByZUnit->save($con);
				}
				$this->setMeasurementUnitRelatedByZUnit($this->aMeasurementUnitRelatedByZUnit);
			}

			if ($this->aMeasurementUnitRelatedByKUnit !== null) {
				if ($this->aMeasurementUnitRelatedByKUnit->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByKUnit->save($con);
				}
				$this->setMeasurementUnitRelatedByKUnit($this->aMeasurementUnitRelatedByKUnit);
			}

			if ($this->aSensorType !== null) {
				if ($this->aSensorType->isModified()) {
					$affectedRows += $this->aSensorType->save($con);
				}
				$this->setSensorType($this->aSensorType);
			}

			if ($this->aSourceType !== null) {
				if ($this->aSourceType->isModified()) {
					$affectedRows += $this->aSourceType->save($con);
				}
				$this->setSourceType($this->aSourceType);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = LocationPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += LocationPeer::doUpdate($this, $con);
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

			if ($this->collDAQChannels !== null) {
				foreach($this->collDAQChannels as $referrerFK) {
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

			if ($this->aCoordinateSpace !== null) {
				if (!$this->aCoordinateSpace->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCoordinateSpace->getValidationFailures());
				}
			}

			if ($this->aLocationPlan !== null) {
				if (!$this->aLocationPlan->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aLocationPlan->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByJUnit !== null) {
				if (!$this->aMeasurementUnitRelatedByJUnit->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByJUnit->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByYUnit !== null) {
				if (!$this->aMeasurementUnitRelatedByYUnit->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByYUnit->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByXUnit !== null) {
				if (!$this->aMeasurementUnitRelatedByXUnit->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByXUnit->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByIUnit !== null) {
				if (!$this->aMeasurementUnitRelatedByIUnit->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByIUnit->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByZUnit !== null) {
				if (!$this->aMeasurementUnitRelatedByZUnit->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByZUnit->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByKUnit !== null) {
				if (!$this->aMeasurementUnitRelatedByKUnit->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByKUnit->getValidationFailures());
				}
			}

			if ($this->aSensorType !== null) {
				if (!$this->aSensorType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSensorType->getValidationFailures());
				}
			}

			if ($this->aSourceType !== null) {
				if (!$this->aSourceType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSourceType->getValidationFailures());
				}
			}


			if (($retval = LocationPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collControllerChannels !== null) {
					foreach($this->collControllerChannels as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collDAQChannels !== null) {
					foreach($this->collDAQChannels as $referrerFK) {
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
		$pos = LocationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getComment();
				break;
			case 2:
				return $this->getCoordinateSpaceId();
				break;
			case 3:
				return $this->getI();
				break;
			case 4:
				return $this->getIUnit();
				break;
			case 5:
				return $this->getJ();
				break;
			case 6:
				return $this->getJUnit();
				break;
			case 7:
				return $this->getK();
				break;
			case 8:
				return $this->getKUnit();
				break;
			case 9:
				return $this->getLabel();
				break;
			case 10:
				return $this->getLocationTypeId();
				break;
			case 11:
				return $this->getPlanId();
				break;
			case 12:
				return $this->getSensorTypeId();
				break;
			case 13:
				return $this->getSourceTypeId();
				break;
			case 14:
				return $this->getX();
				break;
			case 15:
				return $this->getXUnit();
				break;
			case 16:
				return $this->getY();
				break;
			case 17:
				return $this->getYUnit();
				break;
			case 18:
				return $this->getZ();
				break;
			case 19:
				return $this->getZUnit();
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
		$keys = LocationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getComment(),
			$keys[2] => $this->getCoordinateSpaceId(),
			$keys[3] => $this->getI(),
			$keys[4] => $this->getIUnit(),
			$keys[5] => $this->getJ(),
			$keys[6] => $this->getJUnit(),
			$keys[7] => $this->getK(),
			$keys[8] => $this->getKUnit(),
			$keys[9] => $this->getLabel(),
			$keys[10] => $this->getLocationTypeId(),
			$keys[11] => $this->getPlanId(),
			$keys[12] => $this->getSensorTypeId(),
			$keys[13] => $this->getSourceTypeId(),
			$keys[14] => $this->getX(),
			$keys[15] => $this->getXUnit(),
			$keys[16] => $this->getY(),
			$keys[17] => $this->getYUnit(),
			$keys[18] => $this->getZ(),
			$keys[19] => $this->getZUnit(),
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
		$pos = LocationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setComment($value);
				break;
			case 2:
				$this->setCoordinateSpaceId($value);
				break;
			case 3:
				$this->setI($value);
				break;
			case 4:
				$this->setIUnit($value);
				break;
			case 5:
				$this->setJ($value);
				break;
			case 6:
				$this->setJUnit($value);
				break;
			case 7:
				$this->setK($value);
				break;
			case 8:
				$this->setKUnit($value);
				break;
			case 9:
				$this->setLabel($value);
				break;
			case 10:
				$this->setLocationTypeId($value);
				break;
			case 11:
				$this->setPlanId($value);
				break;
			case 12:
				$this->setSensorTypeId($value);
				break;
			case 13:
				$this->setSourceTypeId($value);
				break;
			case 14:
				$this->setX($value);
				break;
			case 15:
				$this->setXUnit($value);
				break;
			case 16:
				$this->setY($value);
				break;
			case 17:
				$this->setYUnit($value);
				break;
			case 18:
				$this->setZ($value);
				break;
			case 19:
				$this->setZUnit($value);
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
		$keys = LocationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setComment($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCoordinateSpaceId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setI($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setIUnit($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setJ($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setJUnit($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setK($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setKUnit($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setLabel($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setLocationTypeId($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setPlanId($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setSensorTypeId($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setSourceTypeId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setX($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setXUnit($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setY($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setYUnit($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setZ($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setZUnit($arr[$keys[19]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(LocationPeer::DATABASE_NAME);

		if ($this->isColumnModified(LocationPeer::ID)) $criteria->add(LocationPeer::ID, $this->id);
		if ($this->isColumnModified(LocationPeer::COMMENTS)) $criteria->add(LocationPeer::COMMENTS, $this->comments);
		if ($this->isColumnModified(LocationPeer::COORDINATE_SPACE_ID)) $criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->coordinate_space_id);
		if ($this->isColumnModified(LocationPeer::I)) $criteria->add(LocationPeer::I, $this->i);
		if ($this->isColumnModified(LocationPeer::I_UNIT)) $criteria->add(LocationPeer::I_UNIT, $this->i_unit);
		if ($this->isColumnModified(LocationPeer::J)) $criteria->add(LocationPeer::J, $this->j);
		if ($this->isColumnModified(LocationPeer::J_UNIT)) $criteria->add(LocationPeer::J_UNIT, $this->j_unit);
		if ($this->isColumnModified(LocationPeer::K)) $criteria->add(LocationPeer::K, $this->k);
		if ($this->isColumnModified(LocationPeer::K_UNIT)) $criteria->add(LocationPeer::K_UNIT, $this->k_unit);
		if ($this->isColumnModified(LocationPeer::LABEL)) $criteria->add(LocationPeer::LABEL, $this->label);
		if ($this->isColumnModified(LocationPeer::LOCATION_TYPE_ID)) $criteria->add(LocationPeer::LOCATION_TYPE_ID, $this->location_type_id);
		if ($this->isColumnModified(LocationPeer::PLAN_ID)) $criteria->add(LocationPeer::PLAN_ID, $this->plan_id);
		if ($this->isColumnModified(LocationPeer::SENSOR_TYPE_ID)) $criteria->add(LocationPeer::SENSOR_TYPE_ID, $this->sensor_type_id);
		if ($this->isColumnModified(LocationPeer::SOURCE_TYPE_ID)) $criteria->add(LocationPeer::SOURCE_TYPE_ID, $this->source_type_id);
		if ($this->isColumnModified(LocationPeer::X)) $criteria->add(LocationPeer::X, $this->x);
		if ($this->isColumnModified(LocationPeer::X_UNIT)) $criteria->add(LocationPeer::X_UNIT, $this->x_unit);
		if ($this->isColumnModified(LocationPeer::Y)) $criteria->add(LocationPeer::Y, $this->y);
		if ($this->isColumnModified(LocationPeer::Y_UNIT)) $criteria->add(LocationPeer::Y_UNIT, $this->y_unit);
		if ($this->isColumnModified(LocationPeer::Z)) $criteria->add(LocationPeer::Z, $this->z);
		if ($this->isColumnModified(LocationPeer::Z_UNIT)) $criteria->add(LocationPeer::Z_UNIT, $this->z_unit);

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
		$criteria = new Criteria(LocationPeer::DATABASE_NAME);

		$criteria->add(LocationPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of Location (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setComment($this->comments);

		$copyObj->setCoordinateSpaceId($this->coordinate_space_id);

		$copyObj->setI($this->i);

		$copyObj->setIUnit($this->i_unit);

		$copyObj->setJ($this->j);

		$copyObj->setJUnit($this->j_unit);

		$copyObj->setK($this->k);

		$copyObj->setKUnit($this->k_unit);

		$copyObj->setLabel($this->label);

		$copyObj->setLocationTypeId($this->location_type_id);

		$copyObj->setPlanId($this->plan_id);

		$copyObj->setSensorTypeId($this->sensor_type_id);

		$copyObj->setSourceTypeId($this->source_type_id);

		$copyObj->setX($this->x);

		$copyObj->setXUnit($this->x_unit);

		$copyObj->setY($this->y);

		$copyObj->setYUnit($this->y_unit);

		$copyObj->setZ($this->z);

		$copyObj->setZUnit($this->z_unit);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getControllerChannels() as $relObj) {
				$copyObj->addControllerChannel($relObj->copy($deepCopy));
			}

			foreach($this->getDAQChannels() as $relObj) {
				$copyObj->addDAQChannel($relObj->copy($deepCopy));
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
	 * @return     Location Clone of current object.
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
	 * @return     LocationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new LocationPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CoordinateSpace object.
	 *
	 * @param      CoordinateSpace $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setCoordinateSpace($v)
	{


		if ($v === null) {
			$this->setCoordinateSpaceId(NULL);
		} else {
			$this->setCoordinateSpaceId($v->getId());
		}


		$this->aCoordinateSpace = $v;
	}


	/**
	 * Get the associated CoordinateSpace object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     CoordinateSpace The associated CoordinateSpace object.
	 * @throws     PropelException
	 */
	public function getCoordinateSpace($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';

		if ($this->aCoordinateSpace === null && ($this->coordinate_space_id > 0)) {

			$this->aCoordinateSpace = CoordinateSpacePeer::retrieveByPK($this->coordinate_space_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = CoordinateSpacePeer::retrieveByPK($this->coordinate_space_id, $con);
			   $obj->addCoordinateSpaces($this);
			 */
		}
		return $this->aCoordinateSpace;
	}

	/**
	 * Declares an association between this object and a LocationPlan object.
	 *
	 * @param      LocationPlan $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setLocationPlan($v)
	{


		if ($v === null) {
			$this->setPlanId(NULL);
		} else {
			$this->setPlanId($v->getId());
		}


		$this->aLocationPlan = $v;
	}


	/**
	 * Get the associated LocationPlan object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     LocationPlan The associated LocationPlan object.
	 * @throws     PropelException
	 */
	public function getLocationPlan($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseLocationPlanPeer.php';

		if ($this->aLocationPlan === null && ($this->plan_id > 0)) {

			$this->aLocationPlan = LocationPlanPeer::retrieveByPK($this->plan_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = LocationPlanPeer::retrieveByPK($this->plan_id, $con);
			   $obj->addLocationPlans($this);
			 */
		}
		return $this->aLocationPlan;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByJUnit($v)
	{


		if ($v === null) {
			$this->setJUnit(NULL);
		} else {
			$this->setJUnit($v->getId());
		}


		$this->aMeasurementUnitRelatedByJUnit = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByJUnit($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByJUnit === null && ($this->j_unit > 0)) {

			$this->aMeasurementUnitRelatedByJUnit = MeasurementUnitPeer::retrieveByPK($this->j_unit, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->j_unit, $con);
			   $obj->addMeasurementUnitsRelatedByJUnit($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByJUnit;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByYUnit($v)
	{


		if ($v === null) {
			$this->setYUnit(NULL);
		} else {
			$this->setYUnit($v->getId());
		}


		$this->aMeasurementUnitRelatedByYUnit = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByYUnit($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByYUnit === null && ($this->y_unit > 0)) {

			$this->aMeasurementUnitRelatedByYUnit = MeasurementUnitPeer::retrieveByPK($this->y_unit, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->y_unit, $con);
			   $obj->addMeasurementUnitsRelatedByYUnit($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByYUnit;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByXUnit($v)
	{


		if ($v === null) {
			$this->setXUnit(NULL);
		} else {
			$this->setXUnit($v->getId());
		}


		$this->aMeasurementUnitRelatedByXUnit = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByXUnit($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByXUnit === null && ($this->x_unit > 0)) {

			$this->aMeasurementUnitRelatedByXUnit = MeasurementUnitPeer::retrieveByPK($this->x_unit, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->x_unit, $con);
			   $obj->addMeasurementUnitsRelatedByXUnit($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByXUnit;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByIUnit($v)
	{


		if ($v === null) {
			$this->setIUnit(NULL);
		} else {
			$this->setIUnit($v->getId());
		}


		$this->aMeasurementUnitRelatedByIUnit = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByIUnit($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByIUnit === null && ($this->i_unit > 0)) {

			$this->aMeasurementUnitRelatedByIUnit = MeasurementUnitPeer::retrieveByPK($this->i_unit, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->i_unit, $con);
			   $obj->addMeasurementUnitsRelatedByIUnit($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByIUnit;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByZUnit($v)
	{


		if ($v === null) {
			$this->setZUnit(NULL);
		} else {
			$this->setZUnit($v->getId());
		}


		$this->aMeasurementUnitRelatedByZUnit = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByZUnit($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByZUnit === null && ($this->z_unit > 0)) {

			$this->aMeasurementUnitRelatedByZUnit = MeasurementUnitPeer::retrieveByPK($this->z_unit, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->z_unit, $con);
			   $obj->addMeasurementUnitsRelatedByZUnit($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByZUnit;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByKUnit($v)
	{


		if ($v === null) {
			$this->setKUnit(NULL);
		} else {
			$this->setKUnit($v->getId());
		}


		$this->aMeasurementUnitRelatedByKUnit = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByKUnit($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByKUnit === null && ($this->k_unit > 0)) {

			$this->aMeasurementUnitRelatedByKUnit = MeasurementUnitPeer::retrieveByPK($this->k_unit, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->k_unit, $con);
			   $obj->addMeasurementUnitsRelatedByKUnit($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByKUnit;
	}

	/**
	 * Declares an association between this object and a SensorType object.
	 *
	 * @param      SensorType $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSensorType($v)
	{


		if ($v === null) {
			$this->setSensorTypeId(NULL);
		} else {
			$this->setSensorTypeId($v->getId());
		}


		$this->aSensorType = $v;
	}


	/**
	 * Get the associated SensorType object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SensorType The associated SensorType object.
	 * @throws     PropelException
	 */
	public function getSensorType($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSensorTypePeer.php';

		if ($this->aSensorType === null && ($this->sensor_type_id > 0)) {

			$this->aSensorType = SensorTypePeer::retrieveByPK($this->sensor_type_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SensorTypePeer::retrieveByPK($this->sensor_type_id, $con);
			   $obj->addSensorTypes($this);
			 */
		}
		return $this->aSensorType;
	}

	/**
	 * Declares an association between this object and a SourceType object.
	 *
	 * @param      SourceType $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSourceType($v)
	{


		if ($v === null) {
			$this->setSourceTypeId(NULL);
		} else {
			$this->setSourceTypeId($v->getId());
		}


		$this->aSourceType = $v;
	}


	/**
	 * Get the associated SourceType object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SourceType The associated SourceType object.
	 * @throws     PropelException
	 */
	public function getSourceType($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSourceTypePeer.php';

		if ($this->aSourceType === null && ($this->source_type_id > 0)) {

			$this->aSourceType = SourceTypePeer::retrieveByPK($this->source_type_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SourceTypePeer::retrieveByPK($this->source_type_id, $con);
			   $obj->addSourceTypes($this);
			 */
		}
		return $this->aSourceType;
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
	 * Otherwise if this Location has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 * If this Location is new, it will return
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

				$criteria->add(ControllerChannelPeer::SOURCE_LOCATION_ID, $this->getId());

				ControllerChannelPeer::addSelectColumns($criteria);
				$this->collControllerChannels = ControllerChannelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ControllerChannelPeer::SOURCE_LOCATION_ID, $this->getId());

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

		$criteria->add(ControllerChannelPeer::SOURCE_LOCATION_ID, $this->getId());

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
		$l->setLocation($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Location is new, it will return
	 * an empty collection; or if this Location has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Location.
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

				$criteria->add(ControllerChannelPeer::SOURCE_LOCATION_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinControllerConfig($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::SOURCE_LOCATION_ID, $this->getId());

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
	 * Otherwise if this Location is new, it will return
	 * an empty collection; or if this Location has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Location.
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

				$criteria->add(ControllerChannelPeer::SOURCE_LOCATION_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::SOURCE_LOCATION_ID, $this->getId());

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
	 * Otherwise if this Location is new, it will return
	 * an empty collection; or if this Location has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Location.
	 */
	public function getControllerChannelsJoinEquipment($criteria = null, $con = null)
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

				$criteria->add(ControllerChannelPeer::SOURCE_LOCATION_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::SOURCE_LOCATION_ID, $this->getId());

			if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastControllerChannelCriteria = $criteria;

		return $this->collControllerChannels;
	}

	/**
	 * Temporary storage of collDAQChannels to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDAQChannels()
	{
		if ($this->collDAQChannels === null) {
			$this->collDAQChannels = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Location has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 * If this Location is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDAQChannels($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
			   $this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::SENSOR_LOCATION_ID, $this->getId());

				DAQChannelPeer::addSelectColumns($criteria);
				$this->collDAQChannels = DAQChannelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DAQChannelPeer::SENSOR_LOCATION_ID, $this->getId());

				DAQChannelPeer::addSelectColumns($criteria);
				if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
					$this->collDAQChannels = DAQChannelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDAQChannelCriteria = $criteria;
		return $this->collDAQChannels;
	}

	/**
	 * Returns the number of related DAQChannels.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDAQChannels($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DAQChannelPeer::SENSOR_LOCATION_ID, $this->getId());

		return DAQChannelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DAQChannel object to this object
	 * through the DAQChannel foreign key attribute
	 *
	 * @param      DAQChannel $l DAQChannel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDAQChannel(DAQChannel $l)
	{
		$this->collDAQChannels[] = $l;
		$l->setLocation($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Location is new, it will return
	 * an empty collection; or if this Location has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Location.
	 */
	public function getDAQChannelsJoinDAQConfig($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
				$this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::SENSOR_LOCATION_ID, $this->getId());

				$this->collDAQChannels = DAQChannelPeer::doSelectJoinDAQConfig($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQChannelPeer::SENSOR_LOCATION_ID, $this->getId());

			if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
				$this->collDAQChannels = DAQChannelPeer::doSelectJoinDAQConfig($criteria, $con);
			}
		}
		$this->lastDAQChannelCriteria = $criteria;

		return $this->collDAQChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Location is new, it will return
	 * an empty collection; or if this Location has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Location.
	 */
	public function getDAQChannelsJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
				$this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::SENSOR_LOCATION_ID, $this->getId());

				$this->collDAQChannels = DAQChannelPeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQChannelPeer::SENSOR_LOCATION_ID, $this->getId());

			if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
				$this->collDAQChannels = DAQChannelPeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastDAQChannelCriteria = $criteria;

		return $this->collDAQChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Location is new, it will return
	 * an empty collection; or if this Location has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Location.
	 */
	public function getDAQChannelsJoinSensor($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
				$this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::SENSOR_LOCATION_ID, $this->getId());

				$this->collDAQChannels = DAQChannelPeer::doSelectJoinSensor($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQChannelPeer::SENSOR_LOCATION_ID, $this->getId());

			if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
				$this->collDAQChannels = DAQChannelPeer::doSelectJoinSensor($criteria, $con);
			}
		}
		$this->lastDAQChannelCriteria = $criteria;

		return $this->collDAQChannels;
	}

} // BaseLocation
