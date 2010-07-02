<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/curation/NCCuratedObjectsPeer.php';

/**
 * Base class that represents a row from the 'CURATED_OBJECTS' table.
 *
 * 
 *
 * @package    lib.data.curation.om
 */
abstract class BaseNCCuratedObjects extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        NCCuratedObjectsPeer
	 */
	protected static $peer;


	/**
	 * The value for the object_id field.
	 * @var        double
	 */
	protected $object_id;


	/**
	 * The value for the conformance_level field.
	 * @var        string
	 */
	protected $conformance_level;


	/**
	 * The value for the created_by field.
	 * @var        string
	 */
	protected $created_by;


	/**
	 * The value for the created_date field.
	 * @var        int
	 */
	protected $created_date;


	/**
	 * The value for the curation_state field.
	 * @var        string
	 */
	protected $curation_state;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the initial_curation_date field.
	 * @var        int
	 */
	protected $initial_curation_date;


	/**
	 * The value for the link field.
	 * @var        string
	 */
	protected $link;


	/**
	 * The value for the modified_by field.
	 * @var        string
	 */
	protected $modified_by;


	/**
	 * The value for the modified_date field.
	 * @var        int
	 */
	protected $modified_date;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the object_creation_date field.
	 * @var        int
	 */
	protected $object_creation_date;


	/**
	 * The value for the object_status field.
	 * @var        string
	 */
	protected $object_status;


	/**
	 * The value for the object_type field.
	 * @var        string
	 */
	protected $object_type;


	/**
	 * The value for the object_visibility field.
	 * @var        string
	 */
	protected $object_visibility;


	/**
	 * The value for the short_title field.
	 * @var        string
	 */
	protected $short_title;


	/**
	 * The value for the title field.
	 * @var        string
	 */
	protected $title;


	/**
	 * The value for the version field.
	 * @var        string
	 */
	protected $version;

	/**
	 * Collection to store aggregation of collNCCuratedContactLogs.
	 * @var        array
	 */
	protected $collNCCuratedContactLogs;

	/**
	 * The criteria used to select the current contents of collNCCuratedContactLogs.
	 * @var        Criteria
	 */
	protected $lastNCCuratedContactLogCriteria = null;

	/**
	 * Collection to store aggregation of collNCCuratedExtendedAttributess.
	 * @var        array
	 */
	protected $collNCCuratedExtendedAttributess;

	/**
	 * The criteria used to select the current contents of collNCCuratedExtendedAttributess.
	 * @var        Criteria
	 */
	protected $lastNCCuratedExtendedAttributesCriteria = null;

	/**
	 * Collection to store aggregation of collNCCuratedObjectAuthorss.
	 * @var        array
	 */
	protected $collNCCuratedObjectAuthorss;

	/**
	 * The criteria used to select the current contents of collNCCuratedObjectAuthorss.
	 * @var        Criteria
	 */
	protected $lastNCCuratedObjectAuthorsCriteria = null;

	/**
	 * Collection to store aggregation of collNCCuratedObjectCatalogEntrys.
	 * @var        array
	 */
	protected $collNCCuratedObjectCatalogEntrys;

	/**
	 * The criteria used to select the current contents of collNCCuratedObjectCatalogEntrys.
	 * @var        Criteria
	 */
	protected $lastNCCuratedObjectCatalogEntryCriteria = null;

	/**
	 * Collection to store aggregation of collNCEntityCurationHistorys.
	 * @var        array
	 */
	protected $collNCEntityCurationHistorys;

	/**
	 * The criteria used to select the current contents of collNCEntityCurationHistorys.
	 * @var        Criteria
	 */
	protected $lastNCEntityCurationHistoryCriteria = null;

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
	 * Get the [object_id] column value.
	 * 
	 * @return     double
	 */
	public function getObjectId()
	{

		return $this->object_id;
	}

	/**
	 * Get the [conformance_level] column value.
	 * 
	 * @return     string
	 */
	public function getConformanceLevel()
	{

		return $this->conformance_level;
	}

	/**
	 * Get the [created_by] column value.
	 * 
	 * @return     string
	 */
	public function getCreatedBy()
	{

		return $this->created_by;
	}

	/**
	 * Get the [optionally formatted] [created_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getCreatedDate($format = '%Y-%m-%d')
	{

		if ($this->created_date === null || $this->created_date === '') {
			return null;
		} elseif (!is_int($this->created_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->created_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [created_date] as date/time value: " . var_export($this->created_date, true));
			}
		} else {
			$ts = $this->created_date;
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
	 * Get the [curation_state] column value.
	 * 
	 * @return     string
	 */
	public function getCurationState()
	{

		return $this->curation_state;
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
	 * Get the [optionally formatted] [initial_curation_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getInitialCurationDate($format = '%Y-%m-%d')
	{

		if ($this->initial_curation_date === null || $this->initial_curation_date === '') {
			return null;
		} elseif (!is_int($this->initial_curation_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->initial_curation_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [initial_curation_date] as date/time value: " . var_export($this->initial_curation_date, true));
			}
		} else {
			$ts = $this->initial_curation_date;
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
	 * Get the [link] column value.
	 * 
	 * @return     string
	 */
	public function getLink()
	{

		return $this->link;
	}

	/**
	 * Get the [modified_by] column value.
	 * 
	 * @return     string
	 */
	public function getModifiedBy()
	{

		return $this->modified_by;
	}

	/**
	 * Get the [optionally formatted] [modified_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getModifiedDate($format = '%Y-%m-%d')
	{

		if ($this->modified_date === null || $this->modified_date === '') {
			return null;
		} elseif (!is_int($this->modified_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->modified_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [modified_date] as date/time value: " . var_export($this->modified_date, true));
			}
		} else {
			$ts = $this->modified_date;
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
	 * Get the [optionally formatted] [object_creation_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getObjectCreationDate($format = '%Y-%m-%d')
	{

		if ($this->object_creation_date === null || $this->object_creation_date === '') {
			return null;
		} elseif (!is_int($this->object_creation_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->object_creation_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [object_creation_date] as date/time value: " . var_export($this->object_creation_date, true));
			}
		} else {
			$ts = $this->object_creation_date;
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
	 * Get the [object_status] column value.
	 * 
	 * @return     string
	 */
	public function getObjectStatus()
	{

		return $this->object_status;
	}

	/**
	 * Get the [object_type] column value.
	 * 
	 * @return     string
	 */
	public function getObjectType()
	{

		return $this->object_type;
	}

	/**
	 * Get the [object_visibility] column value.
	 * 
	 * @return     string
	 */
	public function getObjectVisibility()
	{

		return $this->object_visibility;
	}

	/**
	 * Get the [short_title] column value.
	 * 
	 * @return     string
	 */
	public function getShortTitle()
	{

		return $this->short_title;
	}

	/**
	 * Get the [title] column value.
	 * 
	 * @return     string
	 */
	public function getTitle()
	{

		return $this->title;
	}

	/**
	 * Get the [version] column value.
	 * 
	 * @return     string
	 */
	public function getVersion()
	{

		return $this->version;
	}

	/**
	 * Set the value of [object_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setObjectId($v)
	{

		if ($this->object_id !== $v) {
			$this->object_id = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::OBJECT_ID;
		}

	} // setObjectId()

	/**
	 * Set the value of [conformance_level] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setConformanceLevel($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->conformance_level !== $v) {
			$this->conformance_level = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::CONFORMANCE_LEVEL;
		}

	} // setConformanceLevel()

	/**
	 * Set the value of [created_by] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCreatedBy($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->created_by !== $v) {
			$this->created_by = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::CREATED_BY;
		}

	} // setCreatedBy()

	/**
	 * Set the value of [created_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setCreatedDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [created_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->created_date !== $ts) {
			$this->created_date = $ts;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::CREATED_DATE;
		}

	} // setCreatedDate()

	/**
	 * Set the value of [curation_state] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCurationState($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->curation_state !== $v) {
			$this->curation_state = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::CURATION_STATE;
		}

	} // setCurationState()

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
			$this->modifiedColumns[] = NCCuratedObjectsPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [initial_curation_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setInitialCurationDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [initial_curation_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->initial_curation_date !== $ts) {
			$this->initial_curation_date = $ts;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::INITIAL_CURATION_DATE;
		}

	} // setInitialCurationDate()

	/**
	 * Set the value of [link] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setLink($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->link !== $v) {
			$this->link = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::LINK;
		}

	} // setLink()

	/**
	 * Set the value of [modified_by] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setModifiedBy($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->modified_by !== $v) {
			$this->modified_by = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::MODIFIED_BY;
		}

	} // setModifiedBy()

	/**
	 * Set the value of [modified_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setModifiedDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [modified_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->modified_date !== $ts) {
			$this->modified_date = $ts;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::MODIFIED_DATE;
		}

	} // setModifiedDate()

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
			$this->modifiedColumns[] = NCCuratedObjectsPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [object_creation_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setObjectCreationDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [object_creation_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->object_creation_date !== $ts) {
			$this->object_creation_date = $ts;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::OBJECT_CREATION_DATE;
		}

	} // setObjectCreationDate()

	/**
	 * Set the value of [object_status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setObjectStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->object_status !== $v) {
			$this->object_status = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::OBJECT_STATUS;
		}

	} // setObjectStatus()

	/**
	 * Set the value of [object_type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setObjectType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->object_type !== $v) {
			$this->object_type = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::OBJECT_TYPE;
		}

	} // setObjectType()

	/**
	 * Set the value of [object_visibility] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setObjectVisibility($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->object_visibility !== $v) {
			$this->object_visibility = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::OBJECT_VISIBILITY;
		}

	} // setObjectVisibility()

	/**
	 * Set the value of [short_title] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setShortTitle($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->short_title !== $v) {
			$this->short_title = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::SHORT_TITLE;
		}

	} // setShortTitle()

	/**
	 * Set the value of [title] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTitle($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->title !== $v) {
			$this->title = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::TITLE;
		}

	} // setTitle()

	/**
	 * Set the value of [version] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setVersion($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->version !== $v) {
			$this->version = $v;
			$this->modifiedColumns[] = NCCuratedObjectsPeer::VERSION;
		}

	} // setVersion()

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

			$this->object_id = $rs->getFloat($startcol + 0);

			$this->conformance_level = $rs->getString($startcol + 1);

			$this->created_by = $rs->getString($startcol + 2);

			$this->created_date = $rs->getDate($startcol + 3, null);

			$this->curation_state = $rs->getString($startcol + 4);

			$this->description = $rs->getClob($startcol + 5);

			$this->initial_curation_date = $rs->getDate($startcol + 6, null);

			$this->link = $rs->getString($startcol + 7);

			$this->modified_by = $rs->getString($startcol + 8);

			$this->modified_date = $rs->getDate($startcol + 9, null);

			$this->name = $rs->getString($startcol + 10);

			$this->object_creation_date = $rs->getDate($startcol + 11, null);

			$this->object_status = $rs->getString($startcol + 12);

			$this->object_type = $rs->getString($startcol + 13);

			$this->object_visibility = $rs->getString($startcol + 14);

			$this->short_title = $rs->getString($startcol + 15);

			$this->title = $rs->getString($startcol + 16);

			$this->version = $rs->getString($startcol + 17);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 18; // 18 = NCCuratedObjectsPeer::NUM_COLUMNS - NCCuratedObjectsPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating NCCuratedObjects object", $e);
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
			$con = Propel::getConnection(NCCuratedObjectsPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			NCCuratedObjectsPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(NCCuratedObjectsPeer::DATABASE_NAME);
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
					$pk = NCCuratedObjectsPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setObjectId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += NCCuratedObjectsPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collNCCuratedContactLogs !== null) {
				foreach($this->collNCCuratedContactLogs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collNCCuratedExtendedAttributess !== null) {
				foreach($this->collNCCuratedExtendedAttributess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collNCCuratedObjectAuthorss !== null) {
				foreach($this->collNCCuratedObjectAuthorss as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collNCCuratedObjectCatalogEntrys !== null) {
				foreach($this->collNCCuratedObjectCatalogEntrys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collNCEntityCurationHistorys !== null) {
				foreach($this->collNCEntityCurationHistorys as $referrerFK) {
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


			if (($retval = NCCuratedObjectsPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collNCCuratedContactLogs !== null) {
					foreach($this->collNCCuratedContactLogs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collNCCuratedExtendedAttributess !== null) {
					foreach($this->collNCCuratedExtendedAttributess as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collNCCuratedObjectAuthorss !== null) {
					foreach($this->collNCCuratedObjectAuthorss as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collNCCuratedObjectCatalogEntrys !== null) {
					foreach($this->collNCCuratedObjectCatalogEntrys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collNCEntityCurationHistorys !== null) {
					foreach($this->collNCEntityCurationHistorys as $referrerFK) {
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
		$pos = NCCuratedObjectsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getObjectId();
				break;
			case 1:
				return $this->getConformanceLevel();
				break;
			case 2:
				return $this->getCreatedBy();
				break;
			case 3:
				return $this->getCreatedDate();
				break;
			case 4:
				return $this->getCurationState();
				break;
			case 5:
				return $this->getDescription();
				break;
			case 6:
				return $this->getInitialCurationDate();
				break;
			case 7:
				return $this->getLink();
				break;
			case 8:
				return $this->getModifiedBy();
				break;
			case 9:
				return $this->getModifiedDate();
				break;
			case 10:
				return $this->getName();
				break;
			case 11:
				return $this->getObjectCreationDate();
				break;
			case 12:
				return $this->getObjectStatus();
				break;
			case 13:
				return $this->getObjectType();
				break;
			case 14:
				return $this->getObjectVisibility();
				break;
			case 15:
				return $this->getShortTitle();
				break;
			case 16:
				return $this->getTitle();
				break;
			case 17:
				return $this->getVersion();
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
		$keys = NCCuratedObjectsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getObjectId(),
			$keys[1] => $this->getConformanceLevel(),
			$keys[2] => $this->getCreatedBy(),
			$keys[3] => $this->getCreatedDate(),
			$keys[4] => $this->getCurationState(),
			$keys[5] => $this->getDescription(),
			$keys[6] => $this->getInitialCurationDate(),
			$keys[7] => $this->getLink(),
			$keys[8] => $this->getModifiedBy(),
			$keys[9] => $this->getModifiedDate(),
			$keys[10] => $this->getName(),
			$keys[11] => $this->getObjectCreationDate(),
			$keys[12] => $this->getObjectStatus(),
			$keys[13] => $this->getObjectType(),
			$keys[14] => $this->getObjectVisibility(),
			$keys[15] => $this->getShortTitle(),
			$keys[16] => $this->getTitle(),
			$keys[17] => $this->getVersion(),
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
		$pos = NCCuratedObjectsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setObjectId($value);
				break;
			case 1:
				$this->setConformanceLevel($value);
				break;
			case 2:
				$this->setCreatedBy($value);
				break;
			case 3:
				$this->setCreatedDate($value);
				break;
			case 4:
				$this->setCurationState($value);
				break;
			case 5:
				$this->setDescription($value);
				break;
			case 6:
				$this->setInitialCurationDate($value);
				break;
			case 7:
				$this->setLink($value);
				break;
			case 8:
				$this->setModifiedBy($value);
				break;
			case 9:
				$this->setModifiedDate($value);
				break;
			case 10:
				$this->setName($value);
				break;
			case 11:
				$this->setObjectCreationDate($value);
				break;
			case 12:
				$this->setObjectStatus($value);
				break;
			case 13:
				$this->setObjectType($value);
				break;
			case 14:
				$this->setObjectVisibility($value);
				break;
			case 15:
				$this->setShortTitle($value);
				break;
			case 16:
				$this->setTitle($value);
				break;
			case 17:
				$this->setVersion($value);
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
		$keys = NCCuratedObjectsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setObjectId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setConformanceLevel($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCreatedBy($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCreatedDate($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setCurationState($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDescription($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setInitialCurationDate($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setLink($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setModifiedBy($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setModifiedDate($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setName($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setObjectCreationDate($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setObjectStatus($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setObjectType($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setObjectVisibility($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setShortTitle($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setTitle($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setVersion($arr[$keys[17]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(NCCuratedObjectsPeer::DATABASE_NAME);

		if ($this->isColumnModified(NCCuratedObjectsPeer::OBJECT_ID)) $criteria->add(NCCuratedObjectsPeer::OBJECT_ID, $this->object_id);
		if ($this->isColumnModified(NCCuratedObjectsPeer::CONFORMANCE_LEVEL)) $criteria->add(NCCuratedObjectsPeer::CONFORMANCE_LEVEL, $this->conformance_level);
		if ($this->isColumnModified(NCCuratedObjectsPeer::CREATED_BY)) $criteria->add(NCCuratedObjectsPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(NCCuratedObjectsPeer::CREATED_DATE)) $criteria->add(NCCuratedObjectsPeer::CREATED_DATE, $this->created_date);
		if ($this->isColumnModified(NCCuratedObjectsPeer::CURATION_STATE)) $criteria->add(NCCuratedObjectsPeer::CURATION_STATE, $this->curation_state);
		if ($this->isColumnModified(NCCuratedObjectsPeer::DESCRIPTION)) $criteria->add(NCCuratedObjectsPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(NCCuratedObjectsPeer::INITIAL_CURATION_DATE)) $criteria->add(NCCuratedObjectsPeer::INITIAL_CURATION_DATE, $this->initial_curation_date);
		if ($this->isColumnModified(NCCuratedObjectsPeer::LINK)) $criteria->add(NCCuratedObjectsPeer::LINK, $this->link);
		if ($this->isColumnModified(NCCuratedObjectsPeer::MODIFIED_BY)) $criteria->add(NCCuratedObjectsPeer::MODIFIED_BY, $this->modified_by);
		if ($this->isColumnModified(NCCuratedObjectsPeer::MODIFIED_DATE)) $criteria->add(NCCuratedObjectsPeer::MODIFIED_DATE, $this->modified_date);
		if ($this->isColumnModified(NCCuratedObjectsPeer::NAME)) $criteria->add(NCCuratedObjectsPeer::NAME, $this->name);
		if ($this->isColumnModified(NCCuratedObjectsPeer::OBJECT_CREATION_DATE)) $criteria->add(NCCuratedObjectsPeer::OBJECT_CREATION_DATE, $this->object_creation_date);
		if ($this->isColumnModified(NCCuratedObjectsPeer::OBJECT_STATUS)) $criteria->add(NCCuratedObjectsPeer::OBJECT_STATUS, $this->object_status);
		if ($this->isColumnModified(NCCuratedObjectsPeer::OBJECT_TYPE)) $criteria->add(NCCuratedObjectsPeer::OBJECT_TYPE, $this->object_type);
		if ($this->isColumnModified(NCCuratedObjectsPeer::OBJECT_VISIBILITY)) $criteria->add(NCCuratedObjectsPeer::OBJECT_VISIBILITY, $this->object_visibility);
		if ($this->isColumnModified(NCCuratedObjectsPeer::SHORT_TITLE)) $criteria->add(NCCuratedObjectsPeer::SHORT_TITLE, $this->short_title);
		if ($this->isColumnModified(NCCuratedObjectsPeer::TITLE)) $criteria->add(NCCuratedObjectsPeer::TITLE, $this->title);
		if ($this->isColumnModified(NCCuratedObjectsPeer::VERSION)) $criteria->add(NCCuratedObjectsPeer::VERSION, $this->version);

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
		$criteria = new Criteria(NCCuratedObjectsPeer::DATABASE_NAME);

		$criteria->add(NCCuratedObjectsPeer::OBJECT_ID, $this->object_id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     double
	 */
	public function getPrimaryKey()
	{
		return $this->getObjectId();
	}

	/**
	 * Generic method to set the primary key (object_id column).
	 *
	 * @param      double $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setObjectId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of NCCuratedObjects (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setConformanceLevel($this->conformance_level);

		$copyObj->setCreatedBy($this->created_by);

		$copyObj->setCreatedDate($this->created_date);

		$copyObj->setCurationState($this->curation_state);

		$copyObj->setDescription($this->description);

		$copyObj->setInitialCurationDate($this->initial_curation_date);

		$copyObj->setLink($this->link);

		$copyObj->setModifiedBy($this->modified_by);

		$copyObj->setModifiedDate($this->modified_date);

		$copyObj->setName($this->name);

		$copyObj->setObjectCreationDate($this->object_creation_date);

		$copyObj->setObjectStatus($this->object_status);

		$copyObj->setObjectType($this->object_type);

		$copyObj->setObjectVisibility($this->object_visibility);

		$copyObj->setShortTitle($this->short_title);

		$copyObj->setTitle($this->title);

		$copyObj->setVersion($this->version);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getNCCuratedContactLogs() as $relObj) {
				$copyObj->addNCCuratedContactLog($relObj->copy($deepCopy));
			}

			foreach($this->getNCCuratedExtendedAttributess() as $relObj) {
				$copyObj->addNCCuratedExtendedAttributes($relObj->copy($deepCopy));
			}

			foreach($this->getNCCuratedObjectAuthorss() as $relObj) {
				$copyObj->addNCCuratedObjectAuthors($relObj->copy($deepCopy));
			}

			foreach($this->getNCCuratedObjectCatalogEntrys() as $relObj) {
				$copyObj->addNCCuratedObjectCatalogEntry($relObj->copy($deepCopy));
			}

			foreach($this->getNCEntityCurationHistorys() as $relObj) {
				$copyObj->addNCEntityCurationHistory($relObj->copy($deepCopy));
			}

		} // if ($deepCopy)


		$copyObj->setNew(true);

		$copyObj->setObjectId(NULL); // this is a pkey column, so set to default value

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
	 * @return     NCCuratedObjects Clone of current object.
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
	 * @return     NCCuratedObjectsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new NCCuratedObjectsPeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collNCCuratedContactLogs to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initNCCuratedContactLogs()
	{
		if ($this->collNCCuratedContactLogs === null) {
			$this->collNCCuratedContactLogs = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this NCCuratedObjects has previously
	 * been saved, it will retrieve related NCCuratedContactLogs from storage.
	 * If this NCCuratedObjects is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getNCCuratedContactLogs($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/curation/om/BaseNCCuratedContactLogPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collNCCuratedContactLogs === null) {
			if ($this->isNew()) {
			   $this->collNCCuratedContactLogs = array();
			} else {

				$criteria->add(NCCuratedContactLogPeer::OBJECT_ID, $this->getObjectId());

				NCCuratedContactLogPeer::addSelectColumns($criteria);
				$this->collNCCuratedContactLogs = NCCuratedContactLogPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(NCCuratedContactLogPeer::OBJECT_ID, $this->getObjectId());

				NCCuratedContactLogPeer::addSelectColumns($criteria);
				if (!isset($this->lastNCCuratedContactLogCriteria) || !$this->lastNCCuratedContactLogCriteria->equals($criteria)) {
					$this->collNCCuratedContactLogs = NCCuratedContactLogPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastNCCuratedContactLogCriteria = $criteria;
		return $this->collNCCuratedContactLogs;
	}

	/**
	 * Returns the number of related NCCuratedContactLogs.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countNCCuratedContactLogs($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/curation/om/BaseNCCuratedContactLogPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(NCCuratedContactLogPeer::OBJECT_ID, $this->getObjectId());

		return NCCuratedContactLogPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a NCCuratedContactLog object to this object
	 * through the NCCuratedContactLog foreign key attribute
	 *
	 * @param      NCCuratedContactLog $l NCCuratedContactLog
	 * @return     void
	 * @throws     PropelException
	 */
	public function addNCCuratedContactLog(NCCuratedContactLog $l)
	{
		$this->collNCCuratedContactLogs[] = $l;
		$l->setNCCuratedObjects($this);
	}

	/**
	 * Temporary storage of collNCCuratedExtendedAttributess to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initNCCuratedExtendedAttributess()
	{
		if ($this->collNCCuratedExtendedAttributess === null) {
			$this->collNCCuratedExtendedAttributess = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this NCCuratedObjects has previously
	 * been saved, it will retrieve related NCCuratedExtendedAttributess from storage.
	 * If this NCCuratedObjects is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getNCCuratedExtendedAttributess($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/curation/om/BaseNCCuratedExtendedAttributesPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collNCCuratedExtendedAttributess === null) {
			if ($this->isNew()) {
			   $this->collNCCuratedExtendedAttributess = array();
			} else {

				$criteria->add(NCCuratedExtendedAttributesPeer::OBJECT_ID, $this->getObjectId());

				NCCuratedExtendedAttributesPeer::addSelectColumns($criteria);
				$this->collNCCuratedExtendedAttributess = NCCuratedExtendedAttributesPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(NCCuratedExtendedAttributesPeer::OBJECT_ID, $this->getObjectId());

				NCCuratedExtendedAttributesPeer::addSelectColumns($criteria);
				if (!isset($this->lastNCCuratedExtendedAttributesCriteria) || !$this->lastNCCuratedExtendedAttributesCriteria->equals($criteria)) {
					$this->collNCCuratedExtendedAttributess = NCCuratedExtendedAttributesPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastNCCuratedExtendedAttributesCriteria = $criteria;
		return $this->collNCCuratedExtendedAttributess;
	}

	/**
	 * Returns the number of related NCCuratedExtendedAttributess.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countNCCuratedExtendedAttributess($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/curation/om/BaseNCCuratedExtendedAttributesPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(NCCuratedExtendedAttributesPeer::OBJECT_ID, $this->getObjectId());

		return NCCuratedExtendedAttributesPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a NCCuratedExtendedAttributes object to this object
	 * through the NCCuratedExtendedAttributes foreign key attribute
	 *
	 * @param      NCCuratedExtendedAttributes $l NCCuratedExtendedAttributes
	 * @return     void
	 * @throws     PropelException
	 */
	public function addNCCuratedExtendedAttributes(NCCuratedExtendedAttributes $l)
	{
		$this->collNCCuratedExtendedAttributess[] = $l;
		$l->setNCCuratedObjects($this);
	}

	/**
	 * Temporary storage of collNCCuratedObjectAuthorss to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initNCCuratedObjectAuthorss()
	{
		if ($this->collNCCuratedObjectAuthorss === null) {
			$this->collNCCuratedObjectAuthorss = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this NCCuratedObjects has previously
	 * been saved, it will retrieve related NCCuratedObjectAuthorss from storage.
	 * If this NCCuratedObjects is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getNCCuratedObjectAuthorss($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/curation/om/BaseNCCuratedObjectAuthorsPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collNCCuratedObjectAuthorss === null) {
			if ($this->isNew()) {
			   $this->collNCCuratedObjectAuthorss = array();
			} else {

				$criteria->add(NCCuratedObjectAuthorsPeer::OBJECT_ID, $this->getObjectId());

				NCCuratedObjectAuthorsPeer::addSelectColumns($criteria);
				$this->collNCCuratedObjectAuthorss = NCCuratedObjectAuthorsPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(NCCuratedObjectAuthorsPeer::OBJECT_ID, $this->getObjectId());

				NCCuratedObjectAuthorsPeer::addSelectColumns($criteria);
				if (!isset($this->lastNCCuratedObjectAuthorsCriteria) || !$this->lastNCCuratedObjectAuthorsCriteria->equals($criteria)) {
					$this->collNCCuratedObjectAuthorss = NCCuratedObjectAuthorsPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastNCCuratedObjectAuthorsCriteria = $criteria;
		return $this->collNCCuratedObjectAuthorss;
	}

	/**
	 * Returns the number of related NCCuratedObjectAuthorss.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countNCCuratedObjectAuthorss($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/curation/om/BaseNCCuratedObjectAuthorsPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(NCCuratedObjectAuthorsPeer::OBJECT_ID, $this->getObjectId());

		return NCCuratedObjectAuthorsPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a NCCuratedObjectAuthors object to this object
	 * through the NCCuratedObjectAuthors foreign key attribute
	 *
	 * @param      NCCuratedObjectAuthors $l NCCuratedObjectAuthors
	 * @return     void
	 * @throws     PropelException
	 */
	public function addNCCuratedObjectAuthors(NCCuratedObjectAuthors $l)
	{
		$this->collNCCuratedObjectAuthorss[] = $l;
		$l->setNCCuratedObjects($this);
	}

	/**
	 * Temporary storage of collNCCuratedObjectCatalogEntrys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initNCCuratedObjectCatalogEntrys()
	{
		if ($this->collNCCuratedObjectCatalogEntrys === null) {
			$this->collNCCuratedObjectCatalogEntrys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this NCCuratedObjects has previously
	 * been saved, it will retrieve related NCCuratedObjectCatalogEntrys from storage.
	 * If this NCCuratedObjects is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getNCCuratedObjectCatalogEntrys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/curation/om/BaseNCCuratedObjectCatalogEntryPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collNCCuratedObjectCatalogEntrys === null) {
			if ($this->isNew()) {
			   $this->collNCCuratedObjectCatalogEntrys = array();
			} else {

				$criteria->add(NCCuratedObjectCatalogEntryPeer::OBJECT_ID, $this->getObjectId());

				NCCuratedObjectCatalogEntryPeer::addSelectColumns($criteria);
				$this->collNCCuratedObjectCatalogEntrys = NCCuratedObjectCatalogEntryPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(NCCuratedObjectCatalogEntryPeer::OBJECT_ID, $this->getObjectId());

				NCCuratedObjectCatalogEntryPeer::addSelectColumns($criteria);
				if (!isset($this->lastNCCuratedObjectCatalogEntryCriteria) || !$this->lastNCCuratedObjectCatalogEntryCriteria->equals($criteria)) {
					$this->collNCCuratedObjectCatalogEntrys = NCCuratedObjectCatalogEntryPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastNCCuratedObjectCatalogEntryCriteria = $criteria;
		return $this->collNCCuratedObjectCatalogEntrys;
	}

	/**
	 * Returns the number of related NCCuratedObjectCatalogEntrys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countNCCuratedObjectCatalogEntrys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/curation/om/BaseNCCuratedObjectCatalogEntryPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(NCCuratedObjectCatalogEntryPeer::OBJECT_ID, $this->getObjectId());

		return NCCuratedObjectCatalogEntryPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a NCCuratedObjectCatalogEntry object to this object
	 * through the NCCuratedObjectCatalogEntry foreign key attribute
	 *
	 * @param      NCCuratedObjectCatalogEntry $l NCCuratedObjectCatalogEntry
	 * @return     void
	 * @throws     PropelException
	 */
	public function addNCCuratedObjectCatalogEntry(NCCuratedObjectCatalogEntry $l)
	{
		$this->collNCCuratedObjectCatalogEntrys[] = $l;
		$l->setNCCuratedObjects($this);
	}

	/**
	 * Temporary storage of collNCEntityCurationHistorys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initNCEntityCurationHistorys()
	{
		if ($this->collNCEntityCurationHistorys === null) {
			$this->collNCEntityCurationHistorys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this NCCuratedObjects has previously
	 * been saved, it will retrieve related NCEntityCurationHistorys from storage.
	 * If this NCCuratedObjects is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getNCEntityCurationHistorys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/curation/om/BaseNCEntityCurationHistoryPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collNCEntityCurationHistorys === null) {
			if ($this->isNew()) {
			   $this->collNCEntityCurationHistorys = array();
			} else {

				$criteria->add(NCEntityCurationHistoryPeer::OBJECT_ID, $this->getObjectId());

				NCEntityCurationHistoryPeer::addSelectColumns($criteria);
				$this->collNCEntityCurationHistorys = NCEntityCurationHistoryPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(NCEntityCurationHistoryPeer::OBJECT_ID, $this->getObjectId());

				NCEntityCurationHistoryPeer::addSelectColumns($criteria);
				if (!isset($this->lastNCEntityCurationHistoryCriteria) || !$this->lastNCEntityCurationHistoryCriteria->equals($criteria)) {
					$this->collNCEntityCurationHistorys = NCEntityCurationHistoryPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastNCEntityCurationHistoryCriteria = $criteria;
		return $this->collNCEntityCurationHistorys;
	}

	/**
	 * Returns the number of related NCEntityCurationHistorys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countNCEntityCurationHistorys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/curation/om/BaseNCEntityCurationHistoryPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(NCEntityCurationHistoryPeer::OBJECT_ID, $this->getObjectId());

		return NCEntityCurationHistoryPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a NCEntityCurationHistory object to this object
	 * through the NCEntityCurationHistory foreign key attribute
	 *
	 * @param      NCEntityCurationHistory $l NCEntityCurationHistory
	 * @return     void
	 * @throws     PropelException
	 */
	public function addNCEntityCurationHistory(NCEntityCurationHistory $l)
	{
		$this->collNCEntityCurationHistorys[] = $l;
		$l->setNCCuratedObjects($this);
	}

} // BaseNCCuratedObjects
