<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/CoordinateSpacePeer.php';

/**
 * Base class that represents a row from the 'COORDINATE_SPACE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseCoordinateSpace extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CoordinateSpacePeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the altitude field.
	 * @var        double
	 */
	protected $altitude;


	/**
	 * The value for the altitude_unit field.
	 * @var        double
	 */
	protected $altitude_unit;


	/**
	 * The value for the date_created field.
	 * @var        int
	 */
	protected $date_created;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the expid field.
	 * @var        double
	 */
	protected $expid;


	/**
	 * The value for the latitude field.
	 * @var        double
	 */
	protected $latitude;


	/**
	 * The value for the longitude field.
	 * @var        double
	 */
	protected $longitude;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the parent_id field.
	 * @var        double
	 */
	protected $parent_id;


	/**
	 * The value for the rotationx field.
	 * @var        double
	 */
	protected $rotationx;


	/**
	 * The value for the rotationxunit_id field.
	 * @var        double
	 */
	protected $rotationxunit_id;


	/**
	 * The value for the rotationy field.
	 * @var        double
	 */
	protected $rotationy;


	/**
	 * The value for the rotationyunit_id field.
	 * @var        double
	 */
	protected $rotationyunit_id;


	/**
	 * The value for the rotationz field.
	 * @var        double
	 */
	protected $rotationz;


	/**
	 * The value for the rotationzunit_id field.
	 * @var        double
	 */
	protected $rotationzunit_id;


	/**
	 * The value for the scale field.
	 * @var        double
	 */
	protected $scale;


	/**
	 * The value for the system_id field.
	 * @var        double
	 */
	protected $system_id;


	/**
	 * The value for the translationx field.
	 * @var        double
	 */
	protected $translationx;


	/**
	 * The value for the translationxunit_id field.
	 * @var        double
	 */
	protected $translationxunit_id;


	/**
	 * The value for the translationy field.
	 * @var        double
	 */
	protected $translationy;


	/**
	 * The value for the translationyunit_id field.
	 * @var        double
	 */
	protected $translationyunit_id;


	/**
	 * The value for the translationz field.
	 * @var        double
	 */
	protected $translationz;


	/**
	 * The value for the translationzunit_id field.
	 * @var        double
	 */
	protected $translationzunit_id;

	/**
	 * @var        CoordinateSpace
	 */
	protected $aCoordinateSpaceRelatedByParentId;

	/**
	 * @var        CoordinateSystem
	 */
	protected $aCoordinateSystem;

	/**
	 * @var        Experiment
	 */
	protected $aExperiment;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByTranslationXUnitId;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByTranslationYUnitId;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByRotationZUnitId;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByAltitudeUnitId;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByRotationYUnitId;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByTranslationZUnitId;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnitRelatedByRotationXUnitId;

	/**
	 * Collection to store aggregation of collCoordinateSpacesRelatedByParentId.
	 * @var        array
	 */
	protected $collCoordinateSpacesRelatedByParentId;

	/**
	 * The criteria used to select the current contents of collCoordinateSpacesRelatedByParentId.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceRelatedByParentIdCriteria = null;

	/**
	 * Collection to store aggregation of collCoordinateSpaceDataFiles.
	 * @var        array
	 */
	protected $collCoordinateSpaceDataFiles;

	/**
	 * The criteria used to select the current contents of collCoordinateSpaceDataFiles.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceDataFileCriteria = null;

	/**
	 * Collection to store aggregation of collLocations.
	 * @var        array
	 */
	protected $collLocations;

	/**
	 * The criteria used to select the current contents of collLocations.
	 * @var        Criteria
	 */
	protected $lastLocationCriteria = null;

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
	 * Get the [altitude] column value.
	 * 
	 * @return     double
	 */
	public function getAltitude()
	{

		return $this->altitude;
	}

	/**
	 * Get the [altitude_unit] column value.
	 * 
	 * @return     double
	 */
	public function getAltitudeUnitId()
	{

		return $this->altitude_unit;
	}

	/**
	 * Get the [optionally formatted] [date_created] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getDateCreated($format = '%Y-%m-%d')
	{

		if ($this->date_created === null || $this->date_created === '') {
			return null;
		} elseif (!is_int($this->date_created)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->date_created);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [date_created] as date/time value: " . var_export($this->date_created, true));
			}
		} else {
			$ts = $this->date_created;
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
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
	}

	/**
	 * Get the [expid] column value.
	 * 
	 * @return     double
	 */
	public function getExperimentId()
	{

		return $this->expid;
	}

	/**
	 * Get the [latitude] column value.
	 * 
	 * @return     double
	 */
	public function getLatitude()
	{

		return $this->latitude;
	}

	/**
	 * Get the [longitude] column value.
	 * 
	 * @return     double
	 */
	public function getLongitude()
	{

		return $this->longitude;
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
	 * Get the [parent_id] column value.
	 * 
	 * @return     double
	 */
	public function getParentId()
	{

		return $this->parent_id;
	}

	/**
	 * Get the [rotationx] column value.
	 * 
	 * @return     double
	 */
	public function getRotationX()
	{

		return $this->rotationx;
	}

	/**
	 * Get the [rotationxunit_id] column value.
	 * 
	 * @return     double
	 */
	public function getRotationXUnitId()
	{

		return $this->rotationxunit_id;
	}

	/**
	 * Get the [rotationy] column value.
	 * 
	 * @return     double
	 */
	public function getRotationY()
	{

		return $this->rotationy;
	}

	/**
	 * Get the [rotationyunit_id] column value.
	 * 
	 * @return     double
	 */
	public function getRotationYUnitId()
	{

		return $this->rotationyunit_id;
	}

	/**
	 * Get the [rotationz] column value.
	 * 
	 * @return     double
	 */
	public function getRotationZ()
	{

		return $this->rotationz;
	}

	/**
	 * Get the [rotationzunit_id] column value.
	 * 
	 * @return     double
	 */
	public function getRotationZUnitId()
	{

		return $this->rotationzunit_id;
	}

	/**
	 * Get the [scale] column value.
	 * 
	 * @return     double
	 */
	public function getScale()
	{

		return $this->scale;
	}

	/**
	 * Get the [system_id] column value.
	 * 
	 * @return     double
	 */
	public function getSystemId()
	{

		return $this->system_id;
	}

	/**
	 * Get the [translationx] column value.
	 * 
	 * @return     double
	 */
	public function getTranslationX()
	{

		return $this->translationx;
	}

	/**
	 * Get the [translationxunit_id] column value.
	 * 
	 * @return     double
	 */
	public function getTranslationXUnitId()
	{

		return $this->translationxunit_id;
	}

	/**
	 * Get the [translationy] column value.
	 * 
	 * @return     double
	 */
	public function getTranslationY()
	{

		return $this->translationy;
	}

	/**
	 * Get the [translationyunit_id] column value.
	 * 
	 * @return     double
	 */
	public function getTranslationYUnitId()
	{

		return $this->translationyunit_id;
	}

	/**
	 * Get the [translationz] column value.
	 * 
	 * @return     double
	 */
	public function getTranslationZ()
	{

		return $this->translationz;
	}

	/**
	 * Get the [translationzunit_id] column value.
	 * 
	 * @return     double
	 */
	public function getTranslationZUnitId()
	{

		return $this->translationzunit_id;
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
			$this->modifiedColumns[] = CoordinateSpacePeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [altitude] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAltitude($v)
	{

		if ($this->altitude !== $v) {
			$this->altitude = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::ALTITUDE;
		}

	} // setAltitude()

	/**
	 * Set the value of [altitude_unit] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAltitudeUnitId($v)
	{

		if ($this->altitude_unit !== $v) {
			$this->altitude_unit = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::ALTITUDE_UNIT;
		}

		if ($this->aMeasurementUnitRelatedByAltitudeUnitId !== null && $this->aMeasurementUnitRelatedByAltitudeUnitId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByAltitudeUnitId = null;
		}

	} // setAltitudeUnitId()

	/**
	 * Set the value of [date_created] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setDateCreated($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [date_created] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->date_created !== $ts) {
			$this->date_created = $ts;
			$this->modifiedColumns[] = CoordinateSpacePeer::DATE_CREATED;
		}

	} // setDateCreated()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDescription($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->description) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->description !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->description = $obj;
			$this->modifiedColumns[] = CoordinateSpacePeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [expid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setExperimentId($v)
	{

		if ($this->expid !== $v) {
			$this->expid = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::EXPID;
		}

		if ($this->aExperiment !== null && $this->aExperiment->getId() !== $v) {
			$this->aExperiment = null;
		}

	} // setExperimentId()

	/**
	 * Set the value of [latitude] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setLatitude($v)
	{

		if ($this->latitude !== $v) {
			$this->latitude = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::LATITUDE;
		}

	} // setLatitude()

	/**
	 * Set the value of [longitude] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setLongitude($v)
	{

		if ($this->longitude !== $v) {
			$this->longitude = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::LONGITUDE;
		}

	} // setLongitude()

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
			$this->modifiedColumns[] = CoordinateSpacePeer::NAME;
		}

	} // setName()

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
			$this->modifiedColumns[] = CoordinateSpacePeer::PARENT_ID;
		}

		if ($this->aCoordinateSpaceRelatedByParentId !== null && $this->aCoordinateSpaceRelatedByParentId->getId() !== $v) {
			$this->aCoordinateSpaceRelatedByParentId = null;
		}

	} // setParentId()

	/**
	 * Set the value of [rotationx] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRotationX($v)
	{

		if ($this->rotationx !== $v) {
			$this->rotationx = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::ROTATIONX;
		}

	} // setRotationX()

	/**
	 * Set the value of [rotationxunit_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRotationXUnitId($v)
	{

		if ($this->rotationxunit_id !== $v) {
			$this->rotationxunit_id = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::ROTATIONXUNIT_ID;
		}

		if ($this->aMeasurementUnitRelatedByRotationXUnitId !== null && $this->aMeasurementUnitRelatedByRotationXUnitId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByRotationXUnitId = null;
		}

	} // setRotationXUnitId()

	/**
	 * Set the value of [rotationy] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRotationY($v)
	{

		if ($this->rotationy !== $v) {
			$this->rotationy = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::ROTATIONY;
		}

	} // setRotationY()

	/**
	 * Set the value of [rotationyunit_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRotationYUnitId($v)
	{

		if ($this->rotationyunit_id !== $v) {
			$this->rotationyunit_id = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::ROTATIONYUNIT_ID;
		}

		if ($this->aMeasurementUnitRelatedByRotationYUnitId !== null && $this->aMeasurementUnitRelatedByRotationYUnitId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByRotationYUnitId = null;
		}

	} // setRotationYUnitId()

	/**
	 * Set the value of [rotationz] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRotationZ($v)
	{

		if ($this->rotationz !== $v) {
			$this->rotationz = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::ROTATIONZ;
		}

	} // setRotationZ()

	/**
	 * Set the value of [rotationzunit_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRotationZUnitId($v)
	{

		if ($this->rotationzunit_id !== $v) {
			$this->rotationzunit_id = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::ROTATIONZUNIT_ID;
		}

		if ($this->aMeasurementUnitRelatedByRotationZUnitId !== null && $this->aMeasurementUnitRelatedByRotationZUnitId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByRotationZUnitId = null;
		}

	} // setRotationZUnitId()

	/**
	 * Set the value of [scale] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setScale($v)
	{

		if ($this->scale !== $v) {
			$this->scale = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::SCALE;
		}

	} // setScale()

	/**
	 * Set the value of [system_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSystemId($v)
	{

		if ($this->system_id !== $v) {
			$this->system_id = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::SYSTEM_ID;
		}

		if ($this->aCoordinateSystem !== null && $this->aCoordinateSystem->getId() !== $v) {
			$this->aCoordinateSystem = null;
		}

	} // setSystemId()

	/**
	 * Set the value of [translationx] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTranslationX($v)
	{

		if ($this->translationx !== $v) {
			$this->translationx = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::TRANSLATIONX;
		}

	} // setTranslationX()

	/**
	 * Set the value of [translationxunit_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTranslationXUnitId($v)
	{

		if ($this->translationxunit_id !== $v) {
			$this->translationxunit_id = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::TRANSLATIONXUNIT_ID;
		}

		if ($this->aMeasurementUnitRelatedByTranslationXUnitId !== null && $this->aMeasurementUnitRelatedByTranslationXUnitId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByTranslationXUnitId = null;
		}

	} // setTranslationXUnitId()

	/**
	 * Set the value of [translationy] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTranslationY($v)
	{

		if ($this->translationy !== $v) {
			$this->translationy = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::TRANSLATIONY;
		}

	} // setTranslationY()

	/**
	 * Set the value of [translationyunit_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTranslationYUnitId($v)
	{

		if ($this->translationyunit_id !== $v) {
			$this->translationyunit_id = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::TRANSLATIONYUNIT_ID;
		}

		if ($this->aMeasurementUnitRelatedByTranslationYUnitId !== null && $this->aMeasurementUnitRelatedByTranslationYUnitId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByTranslationYUnitId = null;
		}

	} // setTranslationYUnitId()

	/**
	 * Set the value of [translationz] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTranslationZ($v)
	{

		if ($this->translationz !== $v) {
			$this->translationz = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::TRANSLATIONZ;
		}

	} // setTranslationZ()

	/**
	 * Set the value of [translationzunit_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTranslationZUnitId($v)
	{

		if ($this->translationzunit_id !== $v) {
			$this->translationzunit_id = $v;
			$this->modifiedColumns[] = CoordinateSpacePeer::TRANSLATIONZUNIT_ID;
		}

		if ($this->aMeasurementUnitRelatedByTranslationZUnitId !== null && $this->aMeasurementUnitRelatedByTranslationZUnitId->getId() !== $v) {
			$this->aMeasurementUnitRelatedByTranslationZUnitId = null;
		}

	} // setTranslationZUnitId()

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

			$this->altitude = $rs->getFloat($startcol + 1);

			$this->altitude_unit = $rs->getFloat($startcol + 2);

			$this->date_created = $rs->getDate($startcol + 3, null);

			$this->description = $rs->getClob($startcol + 4);

			$this->expid = $rs->getFloat($startcol + 5);

			$this->latitude = $rs->getFloat($startcol + 6);

			$this->longitude = $rs->getFloat($startcol + 7);

			$this->name = $rs->getString($startcol + 8);

			$this->parent_id = $rs->getFloat($startcol + 9);

			$this->rotationx = $rs->getFloat($startcol + 10);

			$this->rotationxunit_id = $rs->getFloat($startcol + 11);

			$this->rotationy = $rs->getFloat($startcol + 12);

			$this->rotationyunit_id = $rs->getFloat($startcol + 13);

			$this->rotationz = $rs->getFloat($startcol + 14);

			$this->rotationzunit_id = $rs->getFloat($startcol + 15);

			$this->scale = $rs->getFloat($startcol + 16);

			$this->system_id = $rs->getFloat($startcol + 17);

			$this->translationx = $rs->getFloat($startcol + 18);

			$this->translationxunit_id = $rs->getFloat($startcol + 19);

			$this->translationy = $rs->getFloat($startcol + 20);

			$this->translationyunit_id = $rs->getFloat($startcol + 21);

			$this->translationz = $rs->getFloat($startcol + 22);

			$this->translationzunit_id = $rs->getFloat($startcol + 23);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 24; // 24 = CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CoordinateSpace object", $e);
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
			$con = Propel::getConnection(CoordinateSpacePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			CoordinateSpacePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(CoordinateSpacePeer::DATABASE_NAME);
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

			if ($this->aCoordinateSpaceRelatedByParentId !== null) {
				if ($this->aCoordinateSpaceRelatedByParentId->isModified()) {
					$affectedRows += $this->aCoordinateSpaceRelatedByParentId->save($con);
				}
				$this->setCoordinateSpaceRelatedByParentId($this->aCoordinateSpaceRelatedByParentId);
			}

			if ($this->aCoordinateSystem !== null) {
				if ($this->aCoordinateSystem->isModified()) {
					$affectedRows += $this->aCoordinateSystem->save($con);
				}
				$this->setCoordinateSystem($this->aCoordinateSystem);
			}

			if ($this->aExperiment !== null) {
				if ($this->aExperiment->isModified()) {
					$affectedRows += $this->aExperiment->save($con);
				}
				$this->setExperiment($this->aExperiment);
			}

			if ($this->aMeasurementUnitRelatedByTranslationXUnitId !== null) {
				if ($this->aMeasurementUnitRelatedByTranslationXUnitId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByTranslationXUnitId->save($con);
				}
				$this->setMeasurementUnitRelatedByTranslationXUnitId($this->aMeasurementUnitRelatedByTranslationXUnitId);
			}

			if ($this->aMeasurementUnitRelatedByTranslationYUnitId !== null) {
				if ($this->aMeasurementUnitRelatedByTranslationYUnitId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByTranslationYUnitId->save($con);
				}
				$this->setMeasurementUnitRelatedByTranslationYUnitId($this->aMeasurementUnitRelatedByTranslationYUnitId);
			}

			if ($this->aMeasurementUnitRelatedByRotationZUnitId !== null) {
				if ($this->aMeasurementUnitRelatedByRotationZUnitId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByRotationZUnitId->save($con);
				}
				$this->setMeasurementUnitRelatedByRotationZUnitId($this->aMeasurementUnitRelatedByRotationZUnitId);
			}

			if ($this->aMeasurementUnitRelatedByAltitudeUnitId !== null) {
				if ($this->aMeasurementUnitRelatedByAltitudeUnitId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByAltitudeUnitId->save($con);
				}
				$this->setMeasurementUnitRelatedByAltitudeUnitId($this->aMeasurementUnitRelatedByAltitudeUnitId);
			}

			if ($this->aMeasurementUnitRelatedByRotationYUnitId !== null) {
				if ($this->aMeasurementUnitRelatedByRotationYUnitId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByRotationYUnitId->save($con);
				}
				$this->setMeasurementUnitRelatedByRotationYUnitId($this->aMeasurementUnitRelatedByRotationYUnitId);
			}

			if ($this->aMeasurementUnitRelatedByTranslationZUnitId !== null) {
				if ($this->aMeasurementUnitRelatedByTranslationZUnitId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByTranslationZUnitId->save($con);
				}
				$this->setMeasurementUnitRelatedByTranslationZUnitId($this->aMeasurementUnitRelatedByTranslationZUnitId);
			}

			if ($this->aMeasurementUnitRelatedByRotationXUnitId !== null) {
				if ($this->aMeasurementUnitRelatedByRotationXUnitId->isModified()) {
					$affectedRows += $this->aMeasurementUnitRelatedByRotationXUnitId->save($con);
				}
				$this->setMeasurementUnitRelatedByRotationXUnitId($this->aMeasurementUnitRelatedByRotationXUnitId);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = CoordinateSpacePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += CoordinateSpacePeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCoordinateSpacesRelatedByParentId !== null) {
				foreach($this->collCoordinateSpacesRelatedByParentId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinateSpaceDataFiles !== null) {
				foreach($this->collCoordinateSpaceDataFiles as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collLocations !== null) {
				foreach($this->collLocations as $referrerFK) {
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

			if ($this->aCoordinateSpaceRelatedByParentId !== null) {
				if (!$this->aCoordinateSpaceRelatedByParentId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCoordinateSpaceRelatedByParentId->getValidationFailures());
				}
			}

			if ($this->aCoordinateSystem !== null) {
				if (!$this->aCoordinateSystem->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCoordinateSystem->getValidationFailures());
				}
			}

			if ($this->aExperiment !== null) {
				if (!$this->aExperiment->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aExperiment->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByTranslationXUnitId !== null) {
				if (!$this->aMeasurementUnitRelatedByTranslationXUnitId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByTranslationXUnitId->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByTranslationYUnitId !== null) {
				if (!$this->aMeasurementUnitRelatedByTranslationYUnitId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByTranslationYUnitId->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByRotationZUnitId !== null) {
				if (!$this->aMeasurementUnitRelatedByRotationZUnitId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByRotationZUnitId->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByAltitudeUnitId !== null) {
				if (!$this->aMeasurementUnitRelatedByAltitudeUnitId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByAltitudeUnitId->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByRotationYUnitId !== null) {
				if (!$this->aMeasurementUnitRelatedByRotationYUnitId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByRotationYUnitId->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByTranslationZUnitId !== null) {
				if (!$this->aMeasurementUnitRelatedByTranslationZUnitId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByTranslationZUnitId->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnitRelatedByRotationXUnitId !== null) {
				if (!$this->aMeasurementUnitRelatedByRotationXUnitId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnitRelatedByRotationXUnitId->getValidationFailures());
				}
			}


			if (($retval = CoordinateSpacePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCoordinateSpaceDataFiles !== null) {
					foreach($this->collCoordinateSpaceDataFiles as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collLocations !== null) {
					foreach($this->collLocations as $referrerFK) {
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
		$pos = CoordinateSpacePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAltitude();
				break;
			case 2:
				return $this->getAltitudeUnitId();
				break;
			case 3:
				return $this->getDateCreated();
				break;
			case 4:
				return $this->getDescription();
				break;
			case 5:
				return $this->getExperimentId();
				break;
			case 6:
				return $this->getLatitude();
				break;
			case 7:
				return $this->getLongitude();
				break;
			case 8:
				return $this->getName();
				break;
			case 9:
				return $this->getParentId();
				break;
			case 10:
				return $this->getRotationX();
				break;
			case 11:
				return $this->getRotationXUnitId();
				break;
			case 12:
				return $this->getRotationY();
				break;
			case 13:
				return $this->getRotationYUnitId();
				break;
			case 14:
				return $this->getRotationZ();
				break;
			case 15:
				return $this->getRotationZUnitId();
				break;
			case 16:
				return $this->getScale();
				break;
			case 17:
				return $this->getSystemId();
				break;
			case 18:
				return $this->getTranslationX();
				break;
			case 19:
				return $this->getTranslationXUnitId();
				break;
			case 20:
				return $this->getTranslationY();
				break;
			case 21:
				return $this->getTranslationYUnitId();
				break;
			case 22:
				return $this->getTranslationZ();
				break;
			case 23:
				return $this->getTranslationZUnitId();
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
		$keys = CoordinateSpacePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getAltitude(),
			$keys[2] => $this->getAltitudeUnitId(),
			$keys[3] => $this->getDateCreated(),
			$keys[4] => $this->getDescription(),
			$keys[5] => $this->getExperimentId(),
			$keys[6] => $this->getLatitude(),
			$keys[7] => $this->getLongitude(),
			$keys[8] => $this->getName(),
			$keys[9] => $this->getParentId(),
			$keys[10] => $this->getRotationX(),
			$keys[11] => $this->getRotationXUnitId(),
			$keys[12] => $this->getRotationY(),
			$keys[13] => $this->getRotationYUnitId(),
			$keys[14] => $this->getRotationZ(),
			$keys[15] => $this->getRotationZUnitId(),
			$keys[16] => $this->getScale(),
			$keys[17] => $this->getSystemId(),
			$keys[18] => $this->getTranslationX(),
			$keys[19] => $this->getTranslationXUnitId(),
			$keys[20] => $this->getTranslationY(),
			$keys[21] => $this->getTranslationYUnitId(),
			$keys[22] => $this->getTranslationZ(),
			$keys[23] => $this->getTranslationZUnitId(),
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
		$pos = CoordinateSpacePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAltitude($value);
				break;
			case 2:
				$this->setAltitudeUnitId($value);
				break;
			case 3:
				$this->setDateCreated($value);
				break;
			case 4:
				$this->setDescription($value);
				break;
			case 5:
				$this->setExperimentId($value);
				break;
			case 6:
				$this->setLatitude($value);
				break;
			case 7:
				$this->setLongitude($value);
				break;
			case 8:
				$this->setName($value);
				break;
			case 9:
				$this->setParentId($value);
				break;
			case 10:
				$this->setRotationX($value);
				break;
			case 11:
				$this->setRotationXUnitId($value);
				break;
			case 12:
				$this->setRotationY($value);
				break;
			case 13:
				$this->setRotationYUnitId($value);
				break;
			case 14:
				$this->setRotationZ($value);
				break;
			case 15:
				$this->setRotationZUnitId($value);
				break;
			case 16:
				$this->setScale($value);
				break;
			case 17:
				$this->setSystemId($value);
				break;
			case 18:
				$this->setTranslationX($value);
				break;
			case 19:
				$this->setTranslationXUnitId($value);
				break;
			case 20:
				$this->setTranslationY($value);
				break;
			case 21:
				$this->setTranslationYUnitId($value);
				break;
			case 22:
				$this->setTranslationZ($value);
				break;
			case 23:
				$this->setTranslationZUnitId($value);
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
		$keys = CoordinateSpacePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAltitude($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAltitudeUnitId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDateCreated($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDescription($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setExperimentId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setLatitude($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setLongitude($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setName($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setParentId($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setRotationX($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setRotationXUnitId($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setRotationY($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setRotationYUnitId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setRotationZ($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setRotationZUnitId($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setScale($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setSystemId($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setTranslationX($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setTranslationXUnitId($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setTranslationY($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setTranslationYUnitId($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setTranslationZ($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setTranslationZUnitId($arr[$keys[23]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CoordinateSpacePeer::DATABASE_NAME);

		if ($this->isColumnModified(CoordinateSpacePeer::ID)) $criteria->add(CoordinateSpacePeer::ID, $this->id);
		if ($this->isColumnModified(CoordinateSpacePeer::ALTITUDE)) $criteria->add(CoordinateSpacePeer::ALTITUDE, $this->altitude);
		if ($this->isColumnModified(CoordinateSpacePeer::ALTITUDE_UNIT)) $criteria->add(CoordinateSpacePeer::ALTITUDE_UNIT, $this->altitude_unit);
		if ($this->isColumnModified(CoordinateSpacePeer::DATE_CREATED)) $criteria->add(CoordinateSpacePeer::DATE_CREATED, $this->date_created);
		if ($this->isColumnModified(CoordinateSpacePeer::DESCRIPTION)) $criteria->add(CoordinateSpacePeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(CoordinateSpacePeer::EXPID)) $criteria->add(CoordinateSpacePeer::EXPID, $this->expid);
		if ($this->isColumnModified(CoordinateSpacePeer::LATITUDE)) $criteria->add(CoordinateSpacePeer::LATITUDE, $this->latitude);
		if ($this->isColumnModified(CoordinateSpacePeer::LONGITUDE)) $criteria->add(CoordinateSpacePeer::LONGITUDE, $this->longitude);
		if ($this->isColumnModified(CoordinateSpacePeer::NAME)) $criteria->add(CoordinateSpacePeer::NAME, $this->name);
		if ($this->isColumnModified(CoordinateSpacePeer::PARENT_ID)) $criteria->add(CoordinateSpacePeer::PARENT_ID, $this->parent_id);
		if ($this->isColumnModified(CoordinateSpacePeer::ROTATIONX)) $criteria->add(CoordinateSpacePeer::ROTATIONX, $this->rotationx);
		if ($this->isColumnModified(CoordinateSpacePeer::ROTATIONXUNIT_ID)) $criteria->add(CoordinateSpacePeer::ROTATIONXUNIT_ID, $this->rotationxunit_id);
		if ($this->isColumnModified(CoordinateSpacePeer::ROTATIONY)) $criteria->add(CoordinateSpacePeer::ROTATIONY, $this->rotationy);
		if ($this->isColumnModified(CoordinateSpacePeer::ROTATIONYUNIT_ID)) $criteria->add(CoordinateSpacePeer::ROTATIONYUNIT_ID, $this->rotationyunit_id);
		if ($this->isColumnModified(CoordinateSpacePeer::ROTATIONZ)) $criteria->add(CoordinateSpacePeer::ROTATIONZ, $this->rotationz);
		if ($this->isColumnModified(CoordinateSpacePeer::ROTATIONZUNIT_ID)) $criteria->add(CoordinateSpacePeer::ROTATIONZUNIT_ID, $this->rotationzunit_id);
		if ($this->isColumnModified(CoordinateSpacePeer::SCALE)) $criteria->add(CoordinateSpacePeer::SCALE, $this->scale);
		if ($this->isColumnModified(CoordinateSpacePeer::SYSTEM_ID)) $criteria->add(CoordinateSpacePeer::SYSTEM_ID, $this->system_id);
		if ($this->isColumnModified(CoordinateSpacePeer::TRANSLATIONX)) $criteria->add(CoordinateSpacePeer::TRANSLATIONX, $this->translationx);
		if ($this->isColumnModified(CoordinateSpacePeer::TRANSLATIONXUNIT_ID)) $criteria->add(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, $this->translationxunit_id);
		if ($this->isColumnModified(CoordinateSpacePeer::TRANSLATIONY)) $criteria->add(CoordinateSpacePeer::TRANSLATIONY, $this->translationy);
		if ($this->isColumnModified(CoordinateSpacePeer::TRANSLATIONYUNIT_ID)) $criteria->add(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, $this->translationyunit_id);
		if ($this->isColumnModified(CoordinateSpacePeer::TRANSLATIONZ)) $criteria->add(CoordinateSpacePeer::TRANSLATIONZ, $this->translationz);
		if ($this->isColumnModified(CoordinateSpacePeer::TRANSLATIONZUNIT_ID)) $criteria->add(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, $this->translationzunit_id);

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
		$criteria = new Criteria(CoordinateSpacePeer::DATABASE_NAME);

		$criteria->add(CoordinateSpacePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of CoordinateSpace (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAltitude($this->altitude);

		$copyObj->setAltitudeUnitId($this->altitude_unit);

		$copyObj->setDateCreated($this->date_created);

		$copyObj->setDescription($this->description);

		$copyObj->setExperimentId($this->expid);

		$copyObj->setLatitude($this->latitude);

		$copyObj->setLongitude($this->longitude);

		$copyObj->setName($this->name);

		$copyObj->setParentId($this->parent_id);

		$copyObj->setRotationX($this->rotationx);

		$copyObj->setRotationXUnitId($this->rotationxunit_id);

		$copyObj->setRotationY($this->rotationy);

		$copyObj->setRotationYUnitId($this->rotationyunit_id);

		$copyObj->setRotationZ($this->rotationz);

		$copyObj->setRotationZUnitId($this->rotationzunit_id);

		$copyObj->setScale($this->scale);

		$copyObj->setSystemId($this->system_id);

		$copyObj->setTranslationX($this->translationx);

		$copyObj->setTranslationXUnitId($this->translationxunit_id);

		$copyObj->setTranslationY($this->translationy);

		$copyObj->setTranslationYUnitId($this->translationyunit_id);

		$copyObj->setTranslationZ($this->translationz);

		$copyObj->setTranslationZUnitId($this->translationzunit_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getCoordinateSpacesRelatedByParentId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addCoordinateSpaceRelatedByParentId($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinateSpaceDataFiles() as $relObj) {
				$copyObj->addCoordinateSpaceDataFile($relObj->copy($deepCopy));
			}

			foreach($this->getLocations() as $relObj) {
				$copyObj->addLocation($relObj->copy($deepCopy));
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
	 * @return     CoordinateSpace Clone of current object.
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
	 * @return     CoordinateSpacePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CoordinateSpacePeer();
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
	public function setCoordinateSpaceRelatedByParentId($v)
	{


		if ($v === null) {
			$this->setParentId(NULL);
		} else {
			$this->setParentId($v->getId());
		}


		$this->aCoordinateSpaceRelatedByParentId = $v;
	}


	/**
	 * Get the associated CoordinateSpace object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     CoordinateSpace The associated CoordinateSpace object.
	 * @throws     PropelException
	 */
	public function getCoordinateSpaceRelatedByParentId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseCoordinateSpacePeer.php';

		if ($this->aCoordinateSpaceRelatedByParentId === null && ($this->parent_id > 0)) {

			$this->aCoordinateSpaceRelatedByParentId = CoordinateSpacePeer::retrieveByPK($this->parent_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = CoordinateSpacePeer::retrieveByPK($this->parent_id, $con);
			   $obj->addCoordinateSpacesRelatedByParentId($this);
			 */
		}
		return $this->aCoordinateSpaceRelatedByParentId;
	}

	/**
	 * Declares an association between this object and a CoordinateSystem object.
	 *
	 * @param      CoordinateSystem $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setCoordinateSystem($v)
	{


		if ($v === null) {
			$this->setSystemId(NULL);
		} else {
			$this->setSystemId($v->getId());
		}


		$this->aCoordinateSystem = $v;
	}


	/**
	 * Get the associated CoordinateSystem object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     CoordinateSystem The associated CoordinateSystem object.
	 * @throws     PropelException
	 */
	public function getCoordinateSystem($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseCoordinateSystemPeer.php';

		if ($this->aCoordinateSystem === null && ($this->system_id > 0)) {

			$this->aCoordinateSystem = CoordinateSystemPeer::retrieveByPK($this->system_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = CoordinateSystemPeer::retrieveByPK($this->system_id, $con);
			   $obj->addCoordinateSystems($this);
			 */
		}
		return $this->aCoordinateSystem;
	}

	/**
	 * Declares an association between this object and a Experiment object.
	 *
	 * @param      Experiment $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setExperiment($v)
	{


		if ($v === null) {
			$this->setExperimentId(NULL);
		} else {
			$this->setExperimentId($v->getId());
		}


		$this->aExperiment = $v;
	}


	/**
	 * Get the associated Experiment object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Experiment The associated Experiment object.
	 * @throws     PropelException
	 */
	public function getExperiment($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseExperimentPeer.php';

		if ($this->aExperiment === null && ($this->expid > 0)) {

			$this->aExperiment = ExperimentPeer::retrieveByPK($this->expid, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ExperimentPeer::retrieveByPK($this->expid, $con);
			   $obj->addExperiments($this);
			 */
		}
		return $this->aExperiment;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByTranslationXUnitId($v)
	{


		if ($v === null) {
			$this->setTranslationXUnitId(NULL);
		} else {
			$this->setTranslationXUnitId($v->getId());
		}


		$this->aMeasurementUnitRelatedByTranslationXUnitId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByTranslationXUnitId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByTranslationXUnitId === null && ($this->translationxunit_id > 0)) {

			$this->aMeasurementUnitRelatedByTranslationXUnitId = MeasurementUnitPeer::retrieveByPK($this->translationxunit_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->translationxunit_id, $con);
			   $obj->addMeasurementUnitsRelatedByTranslationXUnitId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByTranslationXUnitId;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByTranslationYUnitId($v)
	{


		if ($v === null) {
			$this->setTranslationYUnitId(NULL);
		} else {
			$this->setTranslationYUnitId($v->getId());
		}


		$this->aMeasurementUnitRelatedByTranslationYUnitId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByTranslationYUnitId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByTranslationYUnitId === null && ($this->translationyunit_id > 0)) {

			$this->aMeasurementUnitRelatedByTranslationYUnitId = MeasurementUnitPeer::retrieveByPK($this->translationyunit_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->translationyunit_id, $con);
			   $obj->addMeasurementUnitsRelatedByTranslationYUnitId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByTranslationYUnitId;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByRotationZUnitId($v)
	{


		if ($v === null) {
			$this->setRotationZUnitId(NULL);
		} else {
			$this->setRotationZUnitId($v->getId());
		}


		$this->aMeasurementUnitRelatedByRotationZUnitId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByRotationZUnitId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByRotationZUnitId === null && ($this->rotationzunit_id > 0)) {

			$this->aMeasurementUnitRelatedByRotationZUnitId = MeasurementUnitPeer::retrieveByPK($this->rotationzunit_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->rotationzunit_id, $con);
			   $obj->addMeasurementUnitsRelatedByRotationZUnitId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByRotationZUnitId;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByAltitudeUnitId($v)
	{


		if ($v === null) {
			$this->setAltitudeUnitId(NULL);
		} else {
			$this->setAltitudeUnitId($v->getId());
		}


		$this->aMeasurementUnitRelatedByAltitudeUnitId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByAltitudeUnitId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByAltitudeUnitId === null && ($this->altitude_unit > 0)) {

			$this->aMeasurementUnitRelatedByAltitudeUnitId = MeasurementUnitPeer::retrieveByPK($this->altitude_unit, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->altitude_unit, $con);
			   $obj->addMeasurementUnitsRelatedByAltitudeUnitId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByAltitudeUnitId;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByRotationYUnitId($v)
	{


		if ($v === null) {
			$this->setRotationYUnitId(NULL);
		} else {
			$this->setRotationYUnitId($v->getId());
		}


		$this->aMeasurementUnitRelatedByRotationYUnitId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByRotationYUnitId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByRotationYUnitId === null && ($this->rotationyunit_id > 0)) {

			$this->aMeasurementUnitRelatedByRotationYUnitId = MeasurementUnitPeer::retrieveByPK($this->rotationyunit_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->rotationyunit_id, $con);
			   $obj->addMeasurementUnitsRelatedByRotationYUnitId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByRotationYUnitId;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByTranslationZUnitId($v)
	{


		if ($v === null) {
			$this->setTranslationZUnitId(NULL);
		} else {
			$this->setTranslationZUnitId($v->getId());
		}


		$this->aMeasurementUnitRelatedByTranslationZUnitId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByTranslationZUnitId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByTranslationZUnitId === null && ($this->translationzunit_id > 0)) {

			$this->aMeasurementUnitRelatedByTranslationZUnitId = MeasurementUnitPeer::retrieveByPK($this->translationzunit_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->translationzunit_id, $con);
			   $obj->addMeasurementUnitsRelatedByTranslationZUnitId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByTranslationZUnitId;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnitRelatedByRotationXUnitId($v)
	{


		if ($v === null) {
			$this->setRotationXUnitId(NULL);
		} else {
			$this->setRotationXUnitId($v->getId());
		}


		$this->aMeasurementUnitRelatedByRotationXUnitId = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnitRelatedByRotationXUnitId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnitRelatedByRotationXUnitId === null && ($this->rotationxunit_id > 0)) {

			$this->aMeasurementUnitRelatedByRotationXUnitId = MeasurementUnitPeer::retrieveByPK($this->rotationxunit_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->rotationxunit_id, $con);
			   $obj->addMeasurementUnitsRelatedByRotationXUnitId($this);
			 */
		}
		return $this->aMeasurementUnitRelatedByRotationXUnitId;
	}

	/**
	 * Temporary storage of collCoordinateSpacesRelatedByParentId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpacesRelatedByParentId()
	{
		if ($this->collCoordinateSpacesRelatedByParentId === null) {
			$this->collCoordinateSpacesRelatedByParentId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByParentId from storage.
	 * If this CoordinateSpace is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpacesRelatedByParentId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByParentId === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpacesRelatedByParentId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceRelatedByParentIdCriteria) || !$this->lastCoordinateSpaceRelatedByParentIdCriteria->equals($criteria)) {
					$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceRelatedByParentIdCriteria = $criteria;
		return $this->collCoordinateSpacesRelatedByParentId;
	}

	/**
	 * Returns the number of related CoordinateSpacesRelatedByParentId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpacesRelatedByParentId($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

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
	public function addCoordinateSpaceRelatedByParentId(CoordinateSpace $l)
	{
		$this->collCoordinateSpacesRelatedByParentId[] = $l;
		$l->setCoordinateSpaceRelatedByParentId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getCoordinateSpacesRelatedByParentIdJoinCoordinateSystem($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByParentId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByParentIdCriteria) || !$this->lastCoordinateSpaceRelatedByParentIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByParentIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByParentId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getCoordinateSpacesRelatedByParentIdJoinExperiment($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByParentId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByParentIdCriteria) || !$this->lastCoordinateSpaceRelatedByParentIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByParentIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByParentId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getCoordinateSpacesRelatedByParentIdJoinMeasurementUnitRelatedByTranslationXUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByParentId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationXUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByParentIdCriteria) || !$this->lastCoordinateSpaceRelatedByParentIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationXUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByParentIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByParentId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getCoordinateSpacesRelatedByParentIdJoinMeasurementUnitRelatedByTranslationYUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByParentId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationYUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByParentIdCriteria) || !$this->lastCoordinateSpaceRelatedByParentIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationYUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByParentIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByParentId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getCoordinateSpacesRelatedByParentIdJoinMeasurementUnitRelatedByRotationZUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByParentId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationZUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByParentIdCriteria) || !$this->lastCoordinateSpaceRelatedByParentIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationZUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByParentIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByParentId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getCoordinateSpacesRelatedByParentIdJoinMeasurementUnitRelatedByAltitudeUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByParentId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByAltitudeUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByParentIdCriteria) || !$this->lastCoordinateSpaceRelatedByParentIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByAltitudeUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByParentIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByParentId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getCoordinateSpacesRelatedByParentIdJoinMeasurementUnitRelatedByRotationYUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByParentId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationYUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByParentIdCriteria) || !$this->lastCoordinateSpaceRelatedByParentIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationYUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByParentIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByParentId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getCoordinateSpacesRelatedByParentIdJoinMeasurementUnitRelatedByTranslationZUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByParentId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationZUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByParentIdCriteria) || !$this->lastCoordinateSpaceRelatedByParentIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationZUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByParentIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByParentId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpacesRelatedByParentId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getCoordinateSpacesRelatedByParentIdJoinMeasurementUnitRelatedByRotationXUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpacesRelatedByParentId === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpacesRelatedByParentId = array();
			} else {

				$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationXUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::PARENT_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceRelatedByParentIdCriteria) || !$this->lastCoordinateSpaceRelatedByParentIdCriteria->equals($criteria)) {
				$this->collCoordinateSpacesRelatedByParentId = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationXUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceRelatedByParentIdCriteria = $criteria;

		return $this->collCoordinateSpacesRelatedByParentId;
	}

	/**
	 * Temporary storage of collCoordinateSpaceDataFiles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpaceDataFiles()
	{
		if ($this->collCoordinateSpaceDataFiles === null) {
			$this->collCoordinateSpaceDataFiles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpaceDataFiles from storage.
	 * If this CoordinateSpace is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpaceDataFiles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpaceDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaceDataFiles === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpaceDataFiles = array();
			} else {

				$criteria->add(CoordinateSpaceDataFilePeer::COORDINATE_SPACE_ID, $this->getId());

				CoordinateSpaceDataFilePeer::addSelectColumns($criteria);
				$this->collCoordinateSpaceDataFiles = CoordinateSpaceDataFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpaceDataFilePeer::COORDINATE_SPACE_ID, $this->getId());

				CoordinateSpaceDataFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceDataFileCriteria) || !$this->lastCoordinateSpaceDataFileCriteria->equals($criteria)) {
					$this->collCoordinateSpaceDataFiles = CoordinateSpaceDataFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceDataFileCriteria = $criteria;
		return $this->collCoordinateSpaceDataFiles;
	}

	/**
	 * Returns the number of related CoordinateSpaceDataFiles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpaceDataFiles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpaceDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(CoordinateSpaceDataFilePeer::COORDINATE_SPACE_ID, $this->getId());

		return CoordinateSpaceDataFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a CoordinateSpaceDataFile object to this object
	 * through the CoordinateSpaceDataFile foreign key attribute
	 *
	 * @param      CoordinateSpaceDataFile $l CoordinateSpaceDataFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCoordinateSpaceDataFile(CoordinateSpaceDataFile $l)
	{
		$this->collCoordinateSpaceDataFiles[] = $l;
		$l->setCoordinateSpace($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related CoordinateSpaceDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getCoordinateSpaceDataFilesJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpaceDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaceDataFiles === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaceDataFiles = array();
			} else {

				$criteria->add(CoordinateSpaceDataFilePeer::COORDINATE_SPACE_ID, $this->getId());

				$this->collCoordinateSpaceDataFiles = CoordinateSpaceDataFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpaceDataFilePeer::COORDINATE_SPACE_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceDataFileCriteria) || !$this->lastCoordinateSpaceDataFileCriteria->equals($criteria)) {
				$this->collCoordinateSpaceDataFiles = CoordinateSpaceDataFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceDataFileCriteria = $criteria;

		return $this->collCoordinateSpaceDataFiles;
	}

	/**
	 * Temporary storage of collLocations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLocations()
	{
		if ($this->collLocations === null) {
			$this->collLocations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace has previously
	 * been saved, it will retrieve related Locations from storage.
	 * If this CoordinateSpace is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLocations($criteria = null, $con = null)
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

		if ($this->collLocations === null) {
			if ($this->isNew()) {
			   $this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				$this->collLocations = LocationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

				LocationPeer::addSelectColumns($criteria);
				if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
					$this->collLocations = LocationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLocationCriteria = $criteria;
		return $this->collLocations;
	}

	/**
	 * Returns the number of related Locations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLocations($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

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
	public function addLocation(Location $l)
	{
		$this->collLocations[] = $l;
		$l->setCoordinateSpace($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getLocationsJoinLocationPlan($criteria = null, $con = null)
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

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinLocationPlan($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByJUnit($criteria = null, $con = null)
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

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByJUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByJUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByYUnit($criteria = null, $con = null)
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

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByYUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByYUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByXUnit($criteria = null, $con = null)
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

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByXUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByXUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByIUnit($criteria = null, $con = null)
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

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByIUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByIUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByZUnit($criteria = null, $con = null)
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

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByZUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByZUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getLocationsJoinMeasurementUnitRelatedByKUnit($criteria = null, $con = null)
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

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByKUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinMeasurementUnitRelatedByKUnit($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getLocationsJoinSensorType($criteria = null, $con = null)
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

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinSensorType($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CoordinateSpace is new, it will return
	 * an empty collection; or if this CoordinateSpace has previously
	 * been saved, it will retrieve related Locations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CoordinateSpace.
	 */
	public function getLocationsJoinSourceType($criteria = null, $con = null)
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

		if ($this->collLocations === null) {
			if ($this->isNew()) {
				$this->collLocations = array();
			} else {

				$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

				$this->collLocations = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPeer::COORDINATE_SPACE_ID, $this->getId());

			if (!isset($this->lastLocationCriteria) || !$this->lastLocationCriteria->equals($criteria)) {
				$this->collLocations = LocationPeer::doSelectJoinSourceType($criteria, $con);
			}
		}
		$this->lastLocationCriteria = $criteria;

		return $this->collLocations;
	}

} // BaseCoordinateSpace
