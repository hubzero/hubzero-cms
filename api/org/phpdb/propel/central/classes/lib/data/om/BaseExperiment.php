<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/ExperimentPeer.php';

/**
 * Base class that represents a row from the 'EXPERIMENT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseExperiment extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ExperimentPeer
	 */
	protected static $peer;


	/**
	 * The value for the expid field.
	 * @var        double
	 */
	protected $expid;


	/**
	 * The value for the curation_status field.
	 * @var        string
	 */
	protected $curation_status;


	/**
	 * The value for the deleted field.
	 * @var        double
	 */
	protected $deleted;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the end_date field.
	 * @var        int
	 */
	protected $end_date;


	/**
	 * The value for the experiment_domain_id field.
	 * @var        double
	 */
	protected $experiment_domain_id;


	/**
	 * The value for the exp_type_id field.
	 * @var        double
	 */
	protected $exp_type_id = 0;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the objective field.
	 * @var        string
	 */
	protected $objective;


	/**
	 * The value for the projid field.
	 * @var        double
	 */
	protected $projid;


	/**
	 * The value for the start_date field.
	 * @var        int
	 */
	protected $start_date;


	/**
	 * The value for the status field.
	 * @var        string
	 */
	protected $status;


	/**
	 * The value for the title field.
	 * @var        string
	 */
	protected $title;


	/**
	 * The value for the viewable field.
	 * @var        string
	 */
	protected $viewable;


	/**
	 * The value for the creator_id field.
	 * @var        double
	 */
	protected $creator_id;


	/**
	 * The value for the created_date field.
	 * @var        int
	 */
	protected $created_date;


	/**
	 * The value for the modified_by_id field.
	 * @var        double
	 */
	protected $modified_by_id;


	/**
	 * The value for the modified_date field.
	 * @var        int
	 */
	protected $modified_date;


	/**
	 * The value for the app_id field.
	 * @var        double
	 */
	protected $app_id;

	/**
	 * @var        Person
	 */
	protected $aPersonRelatedByCreatorId;

	/**
	 * @var        Person
	 */
	protected $aPersonRelatedByModifiedById;

	/**
	 * @var        ExperimentDomain
	 */
	protected $aExperimentDomain;

	/**
	 * @var        Project
	 */
	protected $aProject;

	/**
	 * Collection to store aggregation of collAcknowledgements.
	 * @var        array
	 */
	protected $collAcknowledgements;

	/**
	 * The criteria used to select the current contents of collAcknowledgements.
	 * @var        Criteria
	 */
	protected $lastAcknowledgementCriteria = null;

	/**
	 * Collection to store aggregation of collCoordinateSpaces.
	 * @var        array
	 */
	protected $collCoordinateSpaces;

	/**
	 * The criteria used to select the current contents of collCoordinateSpaces.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceCriteria = null;

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
	 * Collection to store aggregation of collExperimentFacilitys.
	 * @var        array
	 */
	protected $collExperimentFacilitys;

	/**
	 * The criteria used to select the current contents of collExperimentFacilitys.
	 * @var        Criteria
	 */
	protected $lastExperimentFacilityCriteria = null;

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
	 * Collection to store aggregation of collExperimentModels.
	 * @var        array
	 */
	protected $collExperimentModels;

	/**
	 * The criteria used to select the current contents of collExperimentModels.
	 * @var        Criteria
	 */
	protected $lastExperimentModelCriteria = null;

	/**
	 * Collection to store aggregation of collExperimentOrganizations.
	 * @var        array
	 */
	protected $collExperimentOrganizations;

	/**
	 * The criteria used to select the current contents of collExperimentOrganizations.
	 * @var        Criteria
	 */
	protected $lastExperimentOrganizationCriteria = null;

	/**
	 * Collection to store aggregation of collLocationPlans.
	 * @var        array
	 */
	protected $collLocationPlans;

	/**
	 * The criteria used to select the current contents of collLocationPlans.
	 * @var        Criteria
	 */
	protected $lastLocationPlanCriteria = null;

	/**
	 * Collection to store aggregation of collMaterials.
	 * @var        array
	 */
	protected $collMaterials;

	/**
	 * The criteria used to select the current contents of collMaterials.
	 * @var        Criteria
	 */
	protected $lastMaterialCriteria = null;

	/**
	 * Collection to store aggregation of collSensorPools.
	 * @var        array
	 */
	protected $collSensorPools;

	/**
	 * The criteria used to select the current contents of collSensorPools.
	 * @var        Criteria
	 */
	protected $lastSensorPoolCriteria = null;

	/**
	 * Collection to store aggregation of collSimilitudeLawValues.
	 * @var        array
	 */
	protected $collSimilitudeLawValues;

	/**
	 * The criteria used to select the current contents of collSimilitudeLawValues.
	 * @var        Criteria
	 */
	protected $lastSimilitudeLawValueCriteria = null;

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
	 * Collection to store aggregation of collCoordinatorRunExperiments.
	 * @var        array
	 */
	protected $collCoordinatorRunExperiments;

	/**
	 * The criteria used to select the current contents of collCoordinatorRunExperiments.
	 * @var        Criteria
	 */
	protected $lastCoordinatorRunExperimentCriteria = null;

	/**
	 * Collection to store aggregation of collSpecimenComponentExperiments.
	 * @var        array
	 */
	protected $collSpecimenComponentExperiments;

	/**
	 * The criteria used to select the current contents of collSpecimenComponentExperiments.
	 * @var        Criteria
	 */
	protected $lastSpecimenComponentExperimentCriteria = null;

	/**
	 * Collection to store aggregation of collDataFileLinks.
	 * @var        array
	 */
	protected $collDataFileLinks;

	/**
	 * The criteria used to select the current contents of collDataFileLinks.
	 * @var        Criteria
	 */
	protected $lastDataFileLinkCriteria = null;

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
	 * Get the [expid] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->expid;
	}

	/**
	 * Get the [curation_status] column value.
	 * 
	 * @return     string
	 */
	public function getCurationStatus()
	{

		return $this->curation_status;
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
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
	}

	/**
	 * Get the [optionally formatted] [end_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getEndDate($format = '%Y-%m-%d')
	{

		if ($this->end_date === null || $this->end_date === '') {
			return null;
		} elseif (!is_int($this->end_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->end_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [end_date] as date/time value: " . var_export($this->end_date, true));
			}
		} else {
			$ts = $this->end_date;
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
	 * Get the [experiment_domain_id] column value.
	 * 
	 * @return     double
	 */
	public function getExperimentDomainId()
	{

		return $this->experiment_domain_id;
	}

	/**
	 * Get the [exp_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getExperimentTypeId()
	{

		return $this->exp_type_id;
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
	 * Get the [objective] column value.
	 * 
	 * @return     string
	 */
	public function getObjective()
	{

		return $this->objective;
	}

	/**
	 * Get the [projid] column value.
	 * 
	 * @return     double
	 */
	public function getProjectId()
	{

		return $this->projid;
	}

	/**
	 * Get the [optionally formatted] [start_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getStartDate($format = '%Y-%m-%d')
	{

		if ($this->start_date === null || $this->start_date === '') {
			return null;
		} elseif (!is_int($this->start_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->start_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [start_date] as date/time value: " . var_export($this->start_date, true));
			}
		} else {
			$ts = $this->start_date;
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
	 * Get the [status] column value.
	 * 
	 * @return     string
	 */
	public function getStatus()
	{

		return $this->status;
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
	 * Get the [viewable] column value.
	 * 
	 * @return     string
	 */
	public function getView()
	{

		return $this->viewable;
	}

	/**
	 * Get the [creator_id] column value.
	 * 
	 * @return     double
	 */
	public function getCreatorId()
	{

		return $this->creator_id;
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
	 * Get the [modified_by_id] column value.
	 * 
	 * @return     double
	 */
	public function getModifiedById()
	{

		return $this->modified_by_id;
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
	 * Get the [app_id] column value.
	 * 
	 * @return     double
	 */
	public function getAppId()
	{

		return $this->app_id;
	}

	/**
	 * Set the value of [expid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->expid !== $v) {
			$this->expid = $v;
			$this->modifiedColumns[] = ExperimentPeer::EXPID;
		}

	} // setId()

	/**
	 * Set the value of [curation_status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCurationStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->curation_status !== $v) {
			$this->curation_status = $v;
			$this->modifiedColumns[] = ExperimentPeer::CURATION_STATUS;
		}

	} // setCurationStatus()

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
			$this->modifiedColumns[] = ExperimentPeer::DELETED;
		}

	} // setDeleted()

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
			$this->modifiedColumns[] = ExperimentPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [end_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setEndDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [end_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->end_date !== $ts) {
			$this->end_date = $ts;
			$this->modifiedColumns[] = ExperimentPeer::END_DATE;
		}

	} // setEndDate()

	/**
	 * Set the value of [experiment_domain_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setExperimentDomainId($v)
	{

		if ($this->experiment_domain_id !== $v) {
			$this->experiment_domain_id = $v;
			$this->modifiedColumns[] = ExperimentPeer::EXPERIMENT_DOMAIN_ID;
		}

		if ($this->aExperimentDomain !== null && $this->aExperimentDomain->getId() !== $v) {
			$this->aExperimentDomain = null;
		}

	} // setExperimentDomainId()

	/**
	 * Set the value of [exp_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setExperimentTypeId($v)
	{

		if ($this->exp_type_id !== $v || $v === 0) {
			$this->exp_type_id = $v;
			$this->modifiedColumns[] = ExperimentPeer::EXP_TYPE_ID;
		}

	} // setExperimentTypeId()

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
			$this->modifiedColumns[] = ExperimentPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [objective] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setObjective($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->objective) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->objective !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->objective = $obj;
			$this->modifiedColumns[] = ExperimentPeer::OBJECTIVE;
		}

	} // setObjective()

	/**
	 * Set the value of [projid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setProjectId($v)
	{

		if ($this->projid !== $v) {
			$this->projid = $v;
			$this->modifiedColumns[] = ExperimentPeer::PROJID;
		}

		if ($this->aProject !== null && $this->aProject->getId() !== $v) {
			$this->aProject = null;
		}

	} // setProjectId()

	/**
	 * Set the value of [start_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setStartDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [start_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->start_date !== $ts) {
			$this->start_date = $ts;
			$this->modifiedColumns[] = ExperimentPeer::START_DATE;
		}

	} // setStartDate()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = ExperimentPeer::STATUS;
		}

	} // setStatus()

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
			$this->modifiedColumns[] = ExperimentPeer::TITLE;
		}

	} // setTitle()

	/**
	 * Set the value of [viewable] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setView($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->viewable !== $v) {
			$this->viewable = $v;
			$this->modifiedColumns[] = ExperimentPeer::VIEWABLE;
		}

	} // setView()

	/**
	 * Set the value of [creator_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCreatorId($v)
	{

		if ($this->creator_id !== $v) {
			$this->creator_id = $v;
			$this->modifiedColumns[] = ExperimentPeer::CREATOR_ID;
		}

		if ($this->aPersonRelatedByCreatorId !== null && $this->aPersonRelatedByCreatorId->getId() !== $v) {
			$this->aPersonRelatedByCreatorId = null;
		}

	} // setCreatorId()

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
			$this->modifiedColumns[] = ExperimentPeer::CREATED_DATE;
		}

	} // setCreatedDate()

	/**
	 * Set the value of [modified_by_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setModifiedById($v)
	{

		if ($this->modified_by_id !== $v) {
			$this->modified_by_id = $v;
			$this->modifiedColumns[] = ExperimentPeer::MODIFIED_BY_ID;
		}

		if ($this->aPersonRelatedByModifiedById !== null && $this->aPersonRelatedByModifiedById->getId() !== $v) {
			$this->aPersonRelatedByModifiedById = null;
		}

	} // setModifiedById()

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
			$this->modifiedColumns[] = ExperimentPeer::MODIFIED_DATE;
		}

	} // setModifiedDate()

	/**
	 * Set the value of [app_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAppId($v)
	{

		if ($this->app_id !== $v) {
			$this->app_id = $v;
			$this->modifiedColumns[] = ExperimentPeer::APP_ID;
		}

	} // setAppId()

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

			$this->expid = $rs->getFloat($startcol + 0);

			$this->curation_status = $rs->getString($startcol + 1);

			$this->deleted = $rs->getFloat($startcol + 2);

			$this->description = $rs->getClob($startcol + 3);

			$this->end_date = $rs->getDate($startcol + 4, null);

			$this->experiment_domain_id = $rs->getFloat($startcol + 5);

			$this->exp_type_id = $rs->getFloat($startcol + 6);

			$this->name = $rs->getString($startcol + 7);

			$this->objective = $rs->getClob($startcol + 8);

			$this->projid = $rs->getFloat($startcol + 9);

			$this->start_date = $rs->getDate($startcol + 10, null);

			$this->status = $rs->getString($startcol + 11);

			$this->title = $rs->getString($startcol + 12);

			$this->viewable = $rs->getString($startcol + 13);

			$this->creator_id = $rs->getFloat($startcol + 14);

			$this->created_date = $rs->getDate($startcol + 15, null);

			$this->modified_by_id = $rs->getFloat($startcol + 16);

			$this->modified_date = $rs->getDate($startcol + 17, null);

			$this->app_id = $rs->getFloat($startcol + 18);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 19; // 19 = ExperimentPeer::NUM_COLUMNS - ExperimentPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Experiment object", $e);
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
			$con = Propel::getConnection(ExperimentPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			ExperimentPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ExperimentPeer::DATABASE_NAME);
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

			if ($this->aPersonRelatedByCreatorId !== null) {
				if ($this->aPersonRelatedByCreatorId->isModified()) {
					$affectedRows += $this->aPersonRelatedByCreatorId->save($con);
				}
				$this->setPersonRelatedByCreatorId($this->aPersonRelatedByCreatorId);
			}

			if ($this->aPersonRelatedByModifiedById !== null) {
				if ($this->aPersonRelatedByModifiedById->isModified()) {
					$affectedRows += $this->aPersonRelatedByModifiedById->save($con);
				}
				$this->setPersonRelatedByModifiedById($this->aPersonRelatedByModifiedById);
			}

			if ($this->aExperimentDomain !== null) {
				if ($this->aExperimentDomain->isModified()) {
					$affectedRows += $this->aExperimentDomain->save($con);
				}
				$this->setExperimentDomain($this->aExperimentDomain);
			}

			if ($this->aProject !== null) {
				if ($this->aProject->isModified()) {
					$affectedRows += $this->aProject->save($con);
				}
				$this->setProject($this->aProject);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = ExperimentPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += ExperimentPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collAcknowledgements !== null) {
				foreach($this->collAcknowledgements as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinateSpaces !== null) {
				foreach($this->collCoordinateSpaces as $referrerFK) {
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

			if ($this->collExperimentFacilitys !== null) {
				foreach($this->collExperimentFacilitys as $referrerFK) {
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

			if ($this->collExperimentModels !== null) {
				foreach($this->collExperimentModels as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collExperimentOrganizations !== null) {
				foreach($this->collExperimentOrganizations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collLocationPlans !== null) {
				foreach($this->collLocationPlans as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collMaterials !== null) {
				foreach($this->collMaterials as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorPools !== null) {
				foreach($this->collSensorPools as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSimilitudeLawValues !== null) {
				foreach($this->collSimilitudeLawValues as $referrerFK) {
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

			if ($this->collCoordinatorRunExperiments !== null) {
				foreach($this->collCoordinatorRunExperiments as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSpecimenComponentExperiments !== null) {
				foreach($this->collSpecimenComponentExperiments as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collDataFileLinks !== null) {
				foreach($this->collDataFileLinks as $referrerFK) {
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

			if ($this->aPersonRelatedByCreatorId !== null) {
				if (!$this->aPersonRelatedByCreatorId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aPersonRelatedByCreatorId->getValidationFailures());
				}
			}

			if ($this->aPersonRelatedByModifiedById !== null) {
				if (!$this->aPersonRelatedByModifiedById->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aPersonRelatedByModifiedById->getValidationFailures());
				}
			}

			if ($this->aExperimentDomain !== null) {
				if (!$this->aExperimentDomain->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aExperimentDomain->getValidationFailures());
				}
			}

			if ($this->aProject !== null) {
				if (!$this->aProject->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aProject->getValidationFailures());
				}
			}


			if (($retval = ExperimentPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAcknowledgements !== null) {
					foreach($this->collAcknowledgements as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCoordinateSpaces !== null) {
					foreach($this->collCoordinateSpaces as $referrerFK) {
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

				if ($this->collExperimentFacilitys !== null) {
					foreach($this->collExperimentFacilitys as $referrerFK) {
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

				if ($this->collExperimentModels !== null) {
					foreach($this->collExperimentModels as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collExperimentOrganizations !== null) {
					foreach($this->collExperimentOrganizations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collLocationPlans !== null) {
					foreach($this->collLocationPlans as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collMaterials !== null) {
					foreach($this->collMaterials as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorPools !== null) {
					foreach($this->collSensorPools as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSimilitudeLawValues !== null) {
					foreach($this->collSimilitudeLawValues as $referrerFK) {
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

				if ($this->collCoordinatorRunExperiments !== null) {
					foreach($this->collCoordinatorRunExperiments as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSpecimenComponentExperiments !== null) {
					foreach($this->collSpecimenComponentExperiments as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collDataFileLinks !== null) {
					foreach($this->collDataFileLinks as $referrerFK) {
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
		$pos = ExperimentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCurationStatus();
				break;
			case 2:
				return $this->getDeleted();
				break;
			case 3:
				return $this->getDescription();
				break;
			case 4:
				return $this->getEndDate();
				break;
			case 5:
				return $this->getExperimentDomainId();
				break;
			case 6:
				return $this->getExperimentTypeId();
				break;
			case 7:
				return $this->getName();
				break;
			case 8:
				return $this->getObjective();
				break;
			case 9:
				return $this->getProjectId();
				break;
			case 10:
				return $this->getStartDate();
				break;
			case 11:
				return $this->getStatus();
				break;
			case 12:
				return $this->getTitle();
				break;
			case 13:
				return $this->getView();
				break;
			case 14:
				return $this->getCreatorId();
				break;
			case 15:
				return $this->getCreatedDate();
				break;
			case 16:
				return $this->getModifiedById();
				break;
			case 17:
				return $this->getModifiedDate();
				break;
			case 18:
				return $this->getAppId();
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
		$keys = ExperimentPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCurationStatus(),
			$keys[2] => $this->getDeleted(),
			$keys[3] => $this->getDescription(),
			$keys[4] => $this->getEndDate(),
			$keys[5] => $this->getExperimentDomainId(),
			$keys[6] => $this->getExperimentTypeId(),
			$keys[7] => $this->getName(),
			$keys[8] => $this->getObjective(),
			$keys[9] => $this->getProjectId(),
			$keys[10] => $this->getStartDate(),
			$keys[11] => $this->getStatus(),
			$keys[12] => $this->getTitle(),
			$keys[13] => $this->getView(),
			$keys[14] => $this->getCreatorId(),
			$keys[15] => $this->getCreatedDate(),
			$keys[16] => $this->getModifiedById(),
			$keys[17] => $this->getModifiedDate(),
			$keys[18] => $this->getAppId(),
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
		$pos = ExperimentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCurationStatus($value);
				break;
			case 2:
				$this->setDeleted($value);
				break;
			case 3:
				$this->setDescription($value);
				break;
			case 4:
				$this->setEndDate($value);
				break;
			case 5:
				$this->setExperimentDomainId($value);
				break;
			case 6:
				$this->setExperimentTypeId($value);
				break;
			case 7:
				$this->setName($value);
				break;
			case 8:
				$this->setObjective($value);
				break;
			case 9:
				$this->setProjectId($value);
				break;
			case 10:
				$this->setStartDate($value);
				break;
			case 11:
				$this->setStatus($value);
				break;
			case 12:
				$this->setTitle($value);
				break;
			case 13:
				$this->setView($value);
				break;
			case 14:
				$this->setCreatorId($value);
				break;
			case 15:
				$this->setCreatedDate($value);
				break;
			case 16:
				$this->setModifiedById($value);
				break;
			case 17:
				$this->setModifiedDate($value);
				break;
			case 18:
				$this->setAppId($value);
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
		$keys = ExperimentPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCurationStatus($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDeleted($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDescription($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setEndDate($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setExperimentDomainId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setExperimentTypeId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setName($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setObjective($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setProjectId($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setStartDate($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setStatus($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setTitle($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setView($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setCreatorId($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setCreatedDate($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setModifiedById($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setModifiedDate($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setAppId($arr[$keys[18]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ExperimentPeer::DATABASE_NAME);

		if ($this->isColumnModified(ExperimentPeer::EXPID)) $criteria->add(ExperimentPeer::EXPID, $this->expid);
		if ($this->isColumnModified(ExperimentPeer::CURATION_STATUS)) $criteria->add(ExperimentPeer::CURATION_STATUS, $this->curation_status);
		if ($this->isColumnModified(ExperimentPeer::DELETED)) $criteria->add(ExperimentPeer::DELETED, $this->deleted);
		if ($this->isColumnModified(ExperimentPeer::DESCRIPTION)) $criteria->add(ExperimentPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(ExperimentPeer::END_DATE)) $criteria->add(ExperimentPeer::END_DATE, $this->end_date);
		if ($this->isColumnModified(ExperimentPeer::EXPERIMENT_DOMAIN_ID)) $criteria->add(ExperimentPeer::EXPERIMENT_DOMAIN_ID, $this->experiment_domain_id);
		if ($this->isColumnModified(ExperimentPeer::EXP_TYPE_ID)) $criteria->add(ExperimentPeer::EXP_TYPE_ID, $this->exp_type_id);
		if ($this->isColumnModified(ExperimentPeer::NAME)) $criteria->add(ExperimentPeer::NAME, $this->name);
		if ($this->isColumnModified(ExperimentPeer::OBJECTIVE)) $criteria->add(ExperimentPeer::OBJECTIVE, $this->objective);
		if ($this->isColumnModified(ExperimentPeer::PROJID)) $criteria->add(ExperimentPeer::PROJID, $this->projid);
		if ($this->isColumnModified(ExperimentPeer::START_DATE)) $criteria->add(ExperimentPeer::START_DATE, $this->start_date);
		if ($this->isColumnModified(ExperimentPeer::STATUS)) $criteria->add(ExperimentPeer::STATUS, $this->status);
		if ($this->isColumnModified(ExperimentPeer::TITLE)) $criteria->add(ExperimentPeer::TITLE, $this->title);
		if ($this->isColumnModified(ExperimentPeer::VIEWABLE)) $criteria->add(ExperimentPeer::VIEWABLE, $this->viewable);
		if ($this->isColumnModified(ExperimentPeer::CREATOR_ID)) $criteria->add(ExperimentPeer::CREATOR_ID, $this->creator_id);
		if ($this->isColumnModified(ExperimentPeer::CREATED_DATE)) $criteria->add(ExperimentPeer::CREATED_DATE, $this->created_date);
		if ($this->isColumnModified(ExperimentPeer::MODIFIED_BY_ID)) $criteria->add(ExperimentPeer::MODIFIED_BY_ID, $this->modified_by_id);
		if ($this->isColumnModified(ExperimentPeer::MODIFIED_DATE)) $criteria->add(ExperimentPeer::MODIFIED_DATE, $this->modified_date);
		if ($this->isColumnModified(ExperimentPeer::APP_ID)) $criteria->add(ExperimentPeer::APP_ID, $this->app_id);

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
		$criteria = new Criteria(ExperimentPeer::DATABASE_NAME);

		$criteria->add(ExperimentPeer::EXPID, $this->expid);

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
	 * Generic method to set the primary key (expid column).
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
	 * @param      object $copyObj An object of Experiment (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCurationStatus($this->curation_status);

		$copyObj->setDeleted($this->deleted);

		$copyObj->setDescription($this->description);

		$copyObj->setEndDate($this->end_date);

		$copyObj->setExperimentDomainId($this->experiment_domain_id);

		$copyObj->setExperimentTypeId($this->exp_type_id);

		$copyObj->setName($this->name);

		$copyObj->setObjective($this->objective);

		$copyObj->setProjectId($this->projid);

		$copyObj->setStartDate($this->start_date);

		$copyObj->setStatus($this->status);

		$copyObj->setTitle($this->title);

		$copyObj->setView($this->viewable);

		$copyObj->setCreatorId($this->creator_id);

		$copyObj->setCreatedDate($this->created_date);

		$copyObj->setModifiedById($this->modified_by_id);

		$copyObj->setModifiedDate($this->modified_date);

		$copyObj->setAppId($this->app_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getAcknowledgements() as $relObj) {
				$copyObj->addAcknowledgement($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinateSpaces() as $relObj) {
				$copyObj->addCoordinateSpace($relObj->copy($deepCopy));
			}

			foreach($this->getExperimentEquipments() as $relObj) {
				$copyObj->addExperimentEquipment($relObj->copy($deepCopy));
			}

			foreach($this->getExperimentFacilitys() as $relObj) {
				$copyObj->addExperimentFacility($relObj->copy($deepCopy));
			}

			foreach($this->getExperimentMeasurements() as $relObj) {
				$copyObj->addExperimentMeasurement($relObj->copy($deepCopy));
			}

			foreach($this->getExperimentModels() as $relObj) {
				$copyObj->addExperimentModel($relObj->copy($deepCopy));
			}

			foreach($this->getExperimentOrganizations() as $relObj) {
				$copyObj->addExperimentOrganization($relObj->copy($deepCopy));
			}

			foreach($this->getLocationPlans() as $relObj) {
				$copyObj->addLocationPlan($relObj->copy($deepCopy));
			}

			foreach($this->getMaterials() as $relObj) {
				$copyObj->addMaterial($relObj->copy($deepCopy));
			}

			foreach($this->getSensorPools() as $relObj) {
				$copyObj->addSensorPool($relObj->copy($deepCopy));
			}

			foreach($this->getSimilitudeLawValues() as $relObj) {
				$copyObj->addSimilitudeLawValue($relObj->copy($deepCopy));
			}

			foreach($this->getTrials() as $relObj) {
				$copyObj->addTrial($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinatorRunExperiments() as $relObj) {
				$copyObj->addCoordinatorRunExperiment($relObj->copy($deepCopy));
			}

			foreach($this->getSpecimenComponentExperiments() as $relObj) {
				$copyObj->addSpecimenComponentExperiment($relObj->copy($deepCopy));
			}

			foreach($this->getDataFileLinks() as $relObj) {
				$copyObj->addDataFileLink($relObj->copy($deepCopy));
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
	 * @return     Experiment Clone of current object.
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
	 * @return     ExperimentPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ExperimentPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Person object.
	 *
	 * @param      Person $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setPersonRelatedByCreatorId($v)
	{


		if ($v === null) {
			$this->setCreatorId(NULL);
		} else {
			$this->setCreatorId($v->getId());
		}


		$this->aPersonRelatedByCreatorId = $v;
	}


	/**
	 * Get the associated Person object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Person The associated Person object.
	 * @throws     PropelException
	 */
	public function getPersonRelatedByCreatorId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BasePersonPeer.php';

		if ($this->aPersonRelatedByCreatorId === null && ($this->creator_id > 0)) {

			$this->aPersonRelatedByCreatorId = PersonPeer::retrieveByPK($this->creator_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = PersonPeer::retrieveByPK($this->creator_id, $con);
			   $obj->addPersonsRelatedByCreatorId($this);
			 */
		}
		return $this->aPersonRelatedByCreatorId;
	}

	/**
	 * Declares an association between this object and a Person object.
	 *
	 * @param      Person $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setPersonRelatedByModifiedById($v)
	{


		if ($v === null) {
			$this->setModifiedById(NULL);
		} else {
			$this->setModifiedById($v->getId());
		}


		$this->aPersonRelatedByModifiedById = $v;
	}


	/**
	 * Get the associated Person object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Person The associated Person object.
	 * @throws     PropelException
	 */
	public function getPersonRelatedByModifiedById($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BasePersonPeer.php';

		if ($this->aPersonRelatedByModifiedById === null && ($this->modified_by_id > 0)) {

			$this->aPersonRelatedByModifiedById = PersonPeer::retrieveByPK($this->modified_by_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = PersonPeer::retrieveByPK($this->modified_by_id, $con);
			   $obj->addPersonsRelatedByModifiedById($this);
			 */
		}
		return $this->aPersonRelatedByModifiedById;
	}

	/**
	 * Declares an association between this object and a ExperimentDomain object.
	 *
	 * @param      ExperimentDomain $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setExperimentDomain($v)
	{


		if ($v === null) {
			$this->setExperimentDomainId(NULL);
		} else {
			$this->setExperimentDomainId($v->getId());
		}


		$this->aExperimentDomain = $v;
	}


	/**
	 * Get the associated ExperimentDomain object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     ExperimentDomain The associated ExperimentDomain object.
	 * @throws     PropelException
	 */
	public function getExperimentDomain($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseExperimentDomainPeer.php';

		if ($this->aExperimentDomain === null && ($this->experiment_domain_id > 0)) {

			$this->aExperimentDomain = ExperimentDomainPeer::retrieveByPK($this->experiment_domain_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ExperimentDomainPeer::retrieveByPK($this->experiment_domain_id, $con);
			   $obj->addExperimentDomains($this);
			 */
		}
		return $this->aExperimentDomain;
	}

	/**
	 * Declares an association between this object and a Project object.
	 *
	 * @param      Project $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setProject($v)
	{


		if ($v === null) {
			$this->setProjectId(NULL);
		} else {
			$this->setProjectId($v->getId());
		}


		$this->aProject = $v;
	}


	/**
	 * Get the associated Project object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Project The associated Project object.
	 * @throws     PropelException
	 */
	public function getProject($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseProjectPeer.php';

		if ($this->aProject === null && ($this->projid > 0)) {

			$this->aProject = ProjectPeer::retrieveByPK($this->projid, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ProjectPeer::retrieveByPK($this->projid, $con);
			   $obj->addProjects($this);
			 */
		}
		return $this->aProject;
	}

	/**
	 * Temporary storage of collAcknowledgements to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initAcknowledgements()
	{
		if ($this->collAcknowledgements === null) {
			$this->collAcknowledgements = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related Acknowledgements from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getAcknowledgements($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAcknowledgementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAcknowledgements === null) {
			if ($this->isNew()) {
			   $this->collAcknowledgements = array();
			} else {

				$criteria->add(AcknowledgementPeer::EXPID, $this->getId());

				AcknowledgementPeer::addSelectColumns($criteria);
				$this->collAcknowledgements = AcknowledgementPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AcknowledgementPeer::EXPID, $this->getId());

				AcknowledgementPeer::addSelectColumns($criteria);
				if (!isset($this->lastAcknowledgementCriteria) || !$this->lastAcknowledgementCriteria->equals($criteria)) {
					$this->collAcknowledgements = AcknowledgementPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAcknowledgementCriteria = $criteria;
		return $this->collAcknowledgements;
	}

	/**
	 * Returns the number of related Acknowledgements.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countAcknowledgements($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAcknowledgementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(AcknowledgementPeer::EXPID, $this->getId());

		return AcknowledgementPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Acknowledgement object to this object
	 * through the Acknowledgement foreign key attribute
	 *
	 * @param      Acknowledgement $l Acknowledgement
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAcknowledgement(Acknowledgement $l)
	{
		$this->collAcknowledgements[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related Acknowledgements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getAcknowledgementsJoinProject($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAcknowledgementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAcknowledgements === null) {
			if ($this->isNew()) {
				$this->collAcknowledgements = array();
			} else {

				$criteria->add(AcknowledgementPeer::EXPID, $this->getId());

				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinProject($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AcknowledgementPeer::EXPID, $this->getId());

			if (!isset($this->lastAcknowledgementCriteria) || !$this->lastAcknowledgementCriteria->equals($criteria)) {
				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinProject($criteria, $con);
			}
		}
		$this->lastAcknowledgementCriteria = $criteria;

		return $this->collAcknowledgements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related Acknowledgements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getAcknowledgementsJoinTrial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseAcknowledgementPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAcknowledgements === null) {
			if ($this->isNew()) {
				$this->collAcknowledgements = array();
			} else {

				$criteria->add(AcknowledgementPeer::EXPID, $this->getId());

				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AcknowledgementPeer::EXPID, $this->getId());

			if (!isset($this->lastAcknowledgementCriteria) || !$this->lastAcknowledgementCriteria->equals($criteria)) {
				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastAcknowledgementCriteria = $criteria;

		return $this->collAcknowledgements;
	}

	/**
	 * Temporary storage of collCoordinateSpaces to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpaces()
	{
		if ($this->collCoordinateSpaces === null) {
			$this->collCoordinateSpaces = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpaces($criteria = null, $con = null)
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

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

				CoordinateSpacePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
					$this->collCoordinateSpaces = CoordinateSpacePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;
		return $this->collCoordinateSpaces;
	}

	/**
	 * Returns the number of related CoordinateSpaces.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpaces($criteria = null, $distinct = false, $con = null)
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

		$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

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
	public function addCoordinateSpace(CoordinateSpace $l)
	{
		$this->collCoordinateSpaces[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getCoordinateSpacesJoinCoordinateSpaceRelatedByParentId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinCoordinateSpaceRelatedByParentId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getCoordinateSpacesJoinCoordinateSystem($criteria = null, $con = null)
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

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinCoordinateSystem($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByTranslationXUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationXUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationXUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByTranslationYUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationYUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationYUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByRotationZUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationZUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationZUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByAltitudeUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByAltitudeUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByAltitudeUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByRotationYUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationYUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationYUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByTranslationZUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationZUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByTranslationZUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related CoordinateSpaces from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getCoordinateSpacesJoinMeasurementUnitRelatedByRotationXUnitId($criteria = null, $con = null)
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

		if ($this->collCoordinateSpaces === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaces = array();
			} else {

				$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationXUnitId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpacePeer::EXPID, $this->getId());

			if (!isset($this->lastCoordinateSpaceCriteria) || !$this->lastCoordinateSpaceCriteria->equals($criteria)) {
				$this->collCoordinateSpaces = CoordinateSpacePeer::doSelectJoinMeasurementUnitRelatedByRotationXUnitId($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceCriteria = $criteria;

		return $this->collCoordinateSpaces;
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
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related ExperimentEquipments from storage.
	 * If this Experiment is new, it will return
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

				$criteria->add(ExperimentEquipmentPeer::EXPERIMENT_ID, $this->getId());

				ExperimentEquipmentPeer::addSelectColumns($criteria);
				$this->collExperimentEquipments = ExperimentEquipmentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentEquipmentPeer::EXPERIMENT_ID, $this->getId());

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

		$criteria->add(ExperimentEquipmentPeer::EXPERIMENT_ID, $this->getId());

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
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related ExperimentEquipments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getExperimentEquipmentsJoinEquipment($criteria = null, $con = null)
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

				$criteria->add(ExperimentEquipmentPeer::EXPERIMENT_ID, $this->getId());

				$this->collExperimentEquipments = ExperimentEquipmentPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentEquipmentPeer::EXPERIMENT_ID, $this->getId());

			if (!isset($this->lastExperimentEquipmentCriteria) || !$this->lastExperimentEquipmentCriteria->equals($criteria)) {
				$this->collExperimentEquipments = ExperimentEquipmentPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastExperimentEquipmentCriteria = $criteria;

		return $this->collExperimentEquipments;
	}

	/**
	 * Temporary storage of collExperimentFacilitys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initExperimentFacilitys()
	{
		if ($this->collExperimentFacilitys === null) {
			$this->collExperimentFacilitys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related ExperimentFacilitys from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getExperimentFacilitys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentFacilitys === null) {
			if ($this->isNew()) {
			   $this->collExperimentFacilitys = array();
			} else {

				$criteria->add(ExperimentFacilityPeer::EXPID, $this->getId());

				ExperimentFacilityPeer::addSelectColumns($criteria);
				$this->collExperimentFacilitys = ExperimentFacilityPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentFacilityPeer::EXPID, $this->getId());

				ExperimentFacilityPeer::addSelectColumns($criteria);
				if (!isset($this->lastExperimentFacilityCriteria) || !$this->lastExperimentFacilityCriteria->equals($criteria)) {
					$this->collExperimentFacilitys = ExperimentFacilityPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastExperimentFacilityCriteria = $criteria;
		return $this->collExperimentFacilitys;
	}

	/**
	 * Returns the number of related ExperimentFacilitys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countExperimentFacilitys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ExperimentFacilityPeer::EXPID, $this->getId());

		return ExperimentFacilityPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ExperimentFacility object to this object
	 * through the ExperimentFacility foreign key attribute
	 *
	 * @param      ExperimentFacility $l ExperimentFacility
	 * @return     void
	 * @throws     PropelException
	 */
	public function addExperimentFacility(ExperimentFacility $l)
	{
		$this->collExperimentFacilitys[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related ExperimentFacilitys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getExperimentFacilitysJoinOrganization($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentFacilitys === null) {
			if ($this->isNew()) {
				$this->collExperimentFacilitys = array();
			} else {

				$criteria->add(ExperimentFacilityPeer::EXPID, $this->getId());

				$this->collExperimentFacilitys = ExperimentFacilityPeer::doSelectJoinOrganization($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentFacilityPeer::EXPID, $this->getId());

			if (!isset($this->lastExperimentFacilityCriteria) || !$this->lastExperimentFacilityCriteria->equals($criteria)) {
				$this->collExperimentFacilitys = ExperimentFacilityPeer::doSelectJoinOrganization($criteria, $con);
			}
		}
		$this->lastExperimentFacilityCriteria = $criteria;

		return $this->collExperimentFacilitys;
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
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related ExperimentMeasurements from storage.
	 * If this Experiment is new, it will return
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

				$criteria->add(ExperimentMeasurementPeer::EXPID, $this->getId());

				ExperimentMeasurementPeer::addSelectColumns($criteria);
				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentMeasurementPeer::EXPID, $this->getId());

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

		$criteria->add(ExperimentMeasurementPeer::EXPID, $this->getId());

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
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related ExperimentMeasurements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getExperimentMeasurementsJoinMeasurementUnit($criteria = null, $con = null)
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

				$criteria->add(ExperimentMeasurementPeer::EXPID, $this->getId());

				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentMeasurementPeer::EXPID, $this->getId());

			if (!isset($this->lastExperimentMeasurementCriteria) || !$this->lastExperimentMeasurementCriteria->equals($criteria)) {
				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastExperimentMeasurementCriteria = $criteria;

		return $this->collExperimentMeasurements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related ExperimentMeasurements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
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

				$criteria->add(ExperimentMeasurementPeer::EXPID, $this->getId());

				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinMeasurementUnitCategory($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentMeasurementPeer::EXPID, $this->getId());

			if (!isset($this->lastExperimentMeasurementCriteria) || !$this->lastExperimentMeasurementCriteria->equals($criteria)) {
				$this->collExperimentMeasurements = ExperimentMeasurementPeer::doSelectJoinMeasurementUnitCategory($criteria, $con);
			}
		}
		$this->lastExperimentMeasurementCriteria = $criteria;

		return $this->collExperimentMeasurements;
	}

	/**
	 * Temporary storage of collExperimentModels to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initExperimentModels()
	{
		if ($this->collExperimentModels === null) {
			$this->collExperimentModels = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related ExperimentModels from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getExperimentModels($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentModels === null) {
			if ($this->isNew()) {
			   $this->collExperimentModels = array();
			} else {

				$criteria->add(ExperimentModelPeer::EXPID, $this->getId());

				ExperimentModelPeer::addSelectColumns($criteria);
				$this->collExperimentModels = ExperimentModelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentModelPeer::EXPID, $this->getId());

				ExperimentModelPeer::addSelectColumns($criteria);
				if (!isset($this->lastExperimentModelCriteria) || !$this->lastExperimentModelCriteria->equals($criteria)) {
					$this->collExperimentModels = ExperimentModelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastExperimentModelCriteria = $criteria;
		return $this->collExperimentModels;
	}

	/**
	 * Returns the number of related ExperimentModels.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countExperimentModels($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ExperimentModelPeer::EXPID, $this->getId());

		return ExperimentModelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ExperimentModel object to this object
	 * through the ExperimentModel foreign key attribute
	 *
	 * @param      ExperimentModel $l ExperimentModel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addExperimentModel(ExperimentModel $l)
	{
		$this->collExperimentModels[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related ExperimentModels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getExperimentModelsJoinExperimentModelType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentModels === null) {
			if ($this->isNew()) {
				$this->collExperimentModels = array();
			} else {

				$criteria->add(ExperimentModelPeer::EXPID, $this->getId());

				$this->collExperimentModels = ExperimentModelPeer::doSelectJoinExperimentModelType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentModelPeer::EXPID, $this->getId());

			if (!isset($this->lastExperimentModelCriteria) || !$this->lastExperimentModelCriteria->equals($criteria)) {
				$this->collExperimentModels = ExperimentModelPeer::doSelectJoinExperimentModelType($criteria, $con);
			}
		}
		$this->lastExperimentModelCriteria = $criteria;

		return $this->collExperimentModels;
	}

	/**
	 * Temporary storage of collExperimentOrganizations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initExperimentOrganizations()
	{
		if ($this->collExperimentOrganizations === null) {
			$this->collExperimentOrganizations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related ExperimentOrganizations from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getExperimentOrganizations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentOrganizations === null) {
			if ($this->isNew()) {
			   $this->collExperimentOrganizations = array();
			} else {

				$criteria->add(ExperimentOrganizationPeer::EXPID, $this->getId());

				ExperimentOrganizationPeer::addSelectColumns($criteria);
				$this->collExperimentOrganizations = ExperimentOrganizationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentOrganizationPeer::EXPID, $this->getId());

				ExperimentOrganizationPeer::addSelectColumns($criteria);
				if (!isset($this->lastExperimentOrganizationCriteria) || !$this->lastExperimentOrganizationCriteria->equals($criteria)) {
					$this->collExperimentOrganizations = ExperimentOrganizationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastExperimentOrganizationCriteria = $criteria;
		return $this->collExperimentOrganizations;
	}

	/**
	 * Returns the number of related ExperimentOrganizations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countExperimentOrganizations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ExperimentOrganizationPeer::EXPID, $this->getId());

		return ExperimentOrganizationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ExperimentOrganization object to this object
	 * through the ExperimentOrganization foreign key attribute
	 *
	 * @param      ExperimentOrganization $l ExperimentOrganization
	 * @return     void
	 * @throws     PropelException
	 */
	public function addExperimentOrganization(ExperimentOrganization $l)
	{
		$this->collExperimentOrganizations[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related ExperimentOrganizations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getExperimentOrganizationsJoinOrganization($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentOrganizations === null) {
			if ($this->isNew()) {
				$this->collExperimentOrganizations = array();
			} else {

				$criteria->add(ExperimentOrganizationPeer::EXPID, $this->getId());

				$this->collExperimentOrganizations = ExperimentOrganizationPeer::doSelectJoinOrganization($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentOrganizationPeer::EXPID, $this->getId());

			if (!isset($this->lastExperimentOrganizationCriteria) || !$this->lastExperimentOrganizationCriteria->equals($criteria)) {
				$this->collExperimentOrganizations = ExperimentOrganizationPeer::doSelectJoinOrganization($criteria, $con);
			}
		}
		$this->lastExperimentOrganizationCriteria = $criteria;

		return $this->collExperimentOrganizations;
	}

	/**
	 * Temporary storage of collLocationPlans to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initLocationPlans()
	{
		if ($this->collLocationPlans === null) {
			$this->collLocationPlans = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related LocationPlans from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getLocationPlans($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPlanPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationPlans === null) {
			if ($this->isNew()) {
			   $this->collLocationPlans = array();
			} else {

				$criteria->add(LocationPlanPeer::EXPID, $this->getId());

				LocationPlanPeer::addSelectColumns($criteria);
				$this->collLocationPlans = LocationPlanPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(LocationPlanPeer::EXPID, $this->getId());

				LocationPlanPeer::addSelectColumns($criteria);
				if (!isset($this->lastLocationPlanCriteria) || !$this->lastLocationPlanCriteria->equals($criteria)) {
					$this->collLocationPlans = LocationPlanPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastLocationPlanCriteria = $criteria;
		return $this->collLocationPlans;
	}

	/**
	 * Returns the number of related LocationPlans.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countLocationPlans($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPlanPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(LocationPlanPeer::EXPID, $this->getId());

		return LocationPlanPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a LocationPlan object to this object
	 * through the LocationPlan foreign key attribute
	 *
	 * @param      LocationPlan $l LocationPlan
	 * @return     void
	 * @throws     PropelException
	 */
	public function addLocationPlan(LocationPlan $l)
	{
		$this->collLocationPlans[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related LocationPlans from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getLocationPlansJoinTrial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPlanPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationPlans === null) {
			if ($this->isNew()) {
				$this->collLocationPlans = array();
			} else {

				$criteria->add(LocationPlanPeer::EXPID, $this->getId());

				$this->collLocationPlans = LocationPlanPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPlanPeer::EXPID, $this->getId());

			if (!isset($this->lastLocationPlanCriteria) || !$this->lastLocationPlanCriteria->equals($criteria)) {
				$this->collLocationPlans = LocationPlanPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastLocationPlanCriteria = $criteria;

		return $this->collLocationPlans;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related LocationPlans from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getLocationPlansJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseLocationPlanPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collLocationPlans === null) {
			if ($this->isNew()) {
				$this->collLocationPlans = array();
			} else {

				$criteria->add(LocationPlanPeer::EXPID, $this->getId());

				$this->collLocationPlans = LocationPlanPeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(LocationPlanPeer::EXPID, $this->getId());

			if (!isset($this->lastLocationPlanCriteria) || !$this->lastLocationPlanCriteria->equals($criteria)) {
				$this->collLocationPlans = LocationPlanPeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastLocationPlanCriteria = $criteria;

		return $this->collLocationPlans;
	}

	/**
	 * Temporary storage of collMaterials to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initMaterials()
	{
		if ($this->collMaterials === null) {
			$this->collMaterials = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related Materials from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getMaterials($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterials === null) {
			if ($this->isNew()) {
			   $this->collMaterials = array();
			} else {

				$criteria->add(MaterialPeer::EXPID, $this->getId());

				MaterialPeer::addSelectColumns($criteria);
				$this->collMaterials = MaterialPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MaterialPeer::EXPID, $this->getId());

				MaterialPeer::addSelectColumns($criteria);
				if (!isset($this->lastMaterialCriteria) || !$this->lastMaterialCriteria->equals($criteria)) {
					$this->collMaterials = MaterialPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMaterialCriteria = $criteria;
		return $this->collMaterials;
	}

	/**
	 * Returns the number of related Materials.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countMaterials($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(MaterialPeer::EXPID, $this->getId());

		return MaterialPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Material object to this object
	 * through the Material foreign key attribute
	 *
	 * @param      Material $l Material
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMaterial(Material $l)
	{
		$this->collMaterials[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related Materials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getMaterialsJoinMaterialRelatedByPrototypeMaterialId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterials === null) {
			if ($this->isNew()) {
				$this->collMaterials = array();
			} else {

				$criteria->add(MaterialPeer::EXPID, $this->getId());

				$this->collMaterials = MaterialPeer::doSelectJoinMaterialRelatedByPrototypeMaterialId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialPeer::EXPID, $this->getId());

			if (!isset($this->lastMaterialCriteria) || !$this->lastMaterialCriteria->equals($criteria)) {
				$this->collMaterials = MaterialPeer::doSelectJoinMaterialRelatedByPrototypeMaterialId($criteria, $con);
			}
		}
		$this->lastMaterialCriteria = $criteria;

		return $this->collMaterials;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related Materials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getMaterialsJoinMaterialType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterials === null) {
			if ($this->isNew()) {
				$this->collMaterials = array();
			} else {

				$criteria->add(MaterialPeer::EXPID, $this->getId());

				$this->collMaterials = MaterialPeer::doSelectJoinMaterialType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialPeer::EXPID, $this->getId());

			if (!isset($this->lastMaterialCriteria) || !$this->lastMaterialCriteria->equals($criteria)) {
				$this->collMaterials = MaterialPeer::doSelectJoinMaterialType($criteria, $con);
			}
		}
		$this->lastMaterialCriteria = $criteria;

		return $this->collMaterials;
	}

	/**
	 * Temporary storage of collSensorPools to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorPools()
	{
		if ($this->collSensorPools === null) {
			$this->collSensorPools = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related SensorPools from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorPools($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorPoolPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorPools === null) {
			if ($this->isNew()) {
			   $this->collSensorPools = array();
			} else {

				$criteria->add(SensorPoolPeer::EXP_ID, $this->getId());

				SensorPoolPeer::addSelectColumns($criteria);
				$this->collSensorPools = SensorPoolPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorPoolPeer::EXP_ID, $this->getId());

				SensorPoolPeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorPoolCriteria) || !$this->lastSensorPoolCriteria->equals($criteria)) {
					$this->collSensorPools = SensorPoolPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorPoolCriteria = $criteria;
		return $this->collSensorPools;
	}

	/**
	 * Returns the number of related SensorPools.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorPools($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorPoolPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorPoolPeer::EXP_ID, $this->getId());

		return SensorPoolPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorPool object to this object
	 * through the SensorPool foreign key attribute
	 *
	 * @param      SensorPool $l SensorPool
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorPool(SensorPool $l)
	{
		$this->collSensorPools[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related SensorPools from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getSensorPoolsJoinSensorManifest($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorPoolPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorPools === null) {
			if ($this->isNew()) {
				$this->collSensorPools = array();
			} else {

				$criteria->add(SensorPoolPeer::EXP_ID, $this->getId());

				$this->collSensorPools = SensorPoolPeer::doSelectJoinSensorManifest($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorPoolPeer::EXP_ID, $this->getId());

			if (!isset($this->lastSensorPoolCriteria) || !$this->lastSensorPoolCriteria->equals($criteria)) {
				$this->collSensorPools = SensorPoolPeer::doSelectJoinSensorManifest($criteria, $con);
			}
		}
		$this->lastSensorPoolCriteria = $criteria;

		return $this->collSensorPools;
	}

	/**
	 * Temporary storage of collSimilitudeLawValues to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSimilitudeLawValues()
	{
		if ($this->collSimilitudeLawValues === null) {
			$this->collSimilitudeLawValues = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related SimilitudeLawValues from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSimilitudeLawValues($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSimilitudeLawValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSimilitudeLawValues === null) {
			if ($this->isNew()) {
			   $this->collSimilitudeLawValues = array();
			} else {

				$criteria->add(SimilitudeLawValuePeer::EXPID, $this->getId());

				SimilitudeLawValuePeer::addSelectColumns($criteria);
				$this->collSimilitudeLawValues = SimilitudeLawValuePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SimilitudeLawValuePeer::EXPID, $this->getId());

				SimilitudeLawValuePeer::addSelectColumns($criteria);
				if (!isset($this->lastSimilitudeLawValueCriteria) || !$this->lastSimilitudeLawValueCriteria->equals($criteria)) {
					$this->collSimilitudeLawValues = SimilitudeLawValuePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSimilitudeLawValueCriteria = $criteria;
		return $this->collSimilitudeLawValues;
	}

	/**
	 * Returns the number of related SimilitudeLawValues.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSimilitudeLawValues($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSimilitudeLawValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SimilitudeLawValuePeer::EXPID, $this->getId());

		return SimilitudeLawValuePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SimilitudeLawValue object to this object
	 * through the SimilitudeLawValue foreign key attribute
	 *
	 * @param      SimilitudeLawValue $l SimilitudeLawValue
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSimilitudeLawValue(SimilitudeLawValue $l)
	{
		$this->collSimilitudeLawValues[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related SimilitudeLawValues from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getSimilitudeLawValuesJoinSimilitudeLaw($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSimilitudeLawValuePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSimilitudeLawValues === null) {
			if ($this->isNew()) {
				$this->collSimilitudeLawValues = array();
			} else {

				$criteria->add(SimilitudeLawValuePeer::EXPID, $this->getId());

				$this->collSimilitudeLawValues = SimilitudeLawValuePeer::doSelectJoinSimilitudeLaw($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SimilitudeLawValuePeer::EXPID, $this->getId());

			if (!isset($this->lastSimilitudeLawValueCriteria) || !$this->lastSimilitudeLawValueCriteria->equals($criteria)) {
				$this->collSimilitudeLawValues = SimilitudeLawValuePeer::doSelectJoinSimilitudeLaw($criteria, $con);
			}
		}
		$this->lastSimilitudeLawValueCriteria = $criteria;

		return $this->collSimilitudeLawValues;
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
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related Trials from storage.
	 * If this Experiment is new, it will return
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

				$criteria->add(TrialPeer::EXPID, $this->getId());

				TrialPeer::addSelectColumns($criteria);
				$this->collTrials = TrialPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TrialPeer::EXPID, $this->getId());

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

		$criteria->add(TrialPeer::EXPID, $this->getId());

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
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related Trials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getTrialsJoinPersonRelatedByCreatorId($criteria = null, $con = null)
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

				$criteria->add(TrialPeer::EXPID, $this->getId());

				$this->collTrials = TrialPeer::doSelectJoinPersonRelatedByCreatorId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(TrialPeer::EXPID, $this->getId());

			if (!isset($this->lastTrialCriteria) || !$this->lastTrialCriteria->equals($criteria)) {
				$this->collTrials = TrialPeer::doSelectJoinPersonRelatedByCreatorId($criteria, $con);
			}
		}
		$this->lastTrialCriteria = $criteria;

		return $this->collTrials;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related Trials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getTrialsJoinPersonRelatedByModifiedById($criteria = null, $con = null)
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

				$criteria->add(TrialPeer::EXPID, $this->getId());

				$this->collTrials = TrialPeer::doSelectJoinPersonRelatedByModifiedById($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(TrialPeer::EXPID, $this->getId());

			if (!isset($this->lastTrialCriteria) || !$this->lastTrialCriteria->equals($criteria)) {
				$this->collTrials = TrialPeer::doSelectJoinPersonRelatedByModifiedById($criteria, $con);
			}
		}
		$this->lastTrialCriteria = $criteria;

		return $this->collTrials;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related Trials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
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

				$criteria->add(TrialPeer::EXPID, $this->getId());

				$this->collTrials = TrialPeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(TrialPeer::EXPID, $this->getId());

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
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related Trials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getTrialsJoinMeasurementUnit($criteria = null, $con = null)
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

				$criteria->add(TrialPeer::EXPID, $this->getId());

				$this->collTrials = TrialPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(TrialPeer::EXPID, $this->getId());

			if (!isset($this->lastTrialCriteria) || !$this->lastTrialCriteria->equals($criteria)) {
				$this->collTrials = TrialPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastTrialCriteria = $criteria;

		return $this->collTrials;
	}

	/**
	 * Temporary storage of collCoordinatorRunExperiments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinatorRunExperiments()
	{
		if ($this->collCoordinatorRunExperiments === null) {
			$this->collCoordinatorRunExperiments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related CoordinatorRunExperiments from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinatorRunExperiments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorRunExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinatorRunExperiments === null) {
			if ($this->isNew()) {
			   $this->collCoordinatorRunExperiments = array();
			} else {

				$criteria->add(CoordinatorRunExperimentPeer::EXP_ID, $this->getId());

				CoordinatorRunExperimentPeer::addSelectColumns($criteria);
				$this->collCoordinatorRunExperiments = CoordinatorRunExperimentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinatorRunExperimentPeer::EXP_ID, $this->getId());

				CoordinatorRunExperimentPeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinatorRunExperimentCriteria) || !$this->lastCoordinatorRunExperimentCriteria->equals($criteria)) {
					$this->collCoordinatorRunExperiments = CoordinatorRunExperimentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinatorRunExperimentCriteria = $criteria;
		return $this->collCoordinatorRunExperiments;
	}

	/**
	 * Returns the number of related CoordinatorRunExperiments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinatorRunExperiments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorRunExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(CoordinatorRunExperimentPeer::EXP_ID, $this->getId());

		return CoordinatorRunExperimentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a CoordinatorRunExperiment object to this object
	 * through the CoordinatorRunExperiment foreign key attribute
	 *
	 * @param      CoordinatorRunExperiment $l CoordinatorRunExperiment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCoordinatorRunExperiment(CoordinatorRunExperiment $l)
	{
		$this->collCoordinatorRunExperiments[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related CoordinatorRunExperiments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getCoordinatorRunExperimentsJoinCoordinatorRun($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorRunExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinatorRunExperiments === null) {
			if ($this->isNew()) {
				$this->collCoordinatorRunExperiments = array();
			} else {

				$criteria->add(CoordinatorRunExperimentPeer::EXP_ID, $this->getId());

				$this->collCoordinatorRunExperiments = CoordinatorRunExperimentPeer::doSelectJoinCoordinatorRun($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinatorRunExperimentPeer::EXP_ID, $this->getId());

			if (!isset($this->lastCoordinatorRunExperimentCriteria) || !$this->lastCoordinatorRunExperimentCriteria->equals($criteria)) {
				$this->collCoordinatorRunExperiments = CoordinatorRunExperimentPeer::doSelectJoinCoordinatorRun($criteria, $con);
			}
		}
		$this->lastCoordinatorRunExperimentCriteria = $criteria;

		return $this->collCoordinatorRunExperiments;
	}

	/**
	 * Temporary storage of collSpecimenComponentExperiments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSpecimenComponentExperiments()
	{
		if ($this->collSpecimenComponentExperiments === null) {
			$this->collSpecimenComponentExperiments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related SpecimenComponentExperiments from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSpecimenComponentExperiments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentExperiments === null) {
			if ($this->isNew()) {
			   $this->collSpecimenComponentExperiments = array();
			} else {

				$criteria->add(SpecimenComponentExperimentPeer::EXPID, $this->getId());

				SpecimenComponentExperimentPeer::addSelectColumns($criteria);
				$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentExperimentPeer::EXPID, $this->getId());

				SpecimenComponentExperimentPeer::addSelectColumns($criteria);
				if (!isset($this->lastSpecimenComponentExperimentCriteria) || !$this->lastSpecimenComponentExperimentCriteria->equals($criteria)) {
					$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSpecimenComponentExperimentCriteria = $criteria;
		return $this->collSpecimenComponentExperiments;
	}

	/**
	 * Returns the number of related SpecimenComponentExperiments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSpecimenComponentExperiments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SpecimenComponentExperimentPeer::EXPID, $this->getId());

		return SpecimenComponentExperimentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SpecimenComponentExperiment object to this object
	 * through the SpecimenComponentExperiment foreign key attribute
	 *
	 * @param      SpecimenComponentExperiment $l SpecimenComponentExperiment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSpecimenComponentExperiment(SpecimenComponentExperiment $l)
	{
		$this->collSpecimenComponentExperiments[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related SpecimenComponentExperiments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getSpecimenComponentExperimentsJoinSpecimenComponent($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentExperiments === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentExperiments = array();
			} else {

				$criteria->add(SpecimenComponentExperimentPeer::EXPID, $this->getId());

				$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelectJoinSpecimenComponent($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentExperimentPeer::EXPID, $this->getId());

			if (!isset($this->lastSpecimenComponentExperimentCriteria) || !$this->lastSpecimenComponentExperimentCriteria->equals($criteria)) {
				$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelectJoinSpecimenComponent($criteria, $con);
			}
		}
		$this->lastSpecimenComponentExperimentCriteria = $criteria;

		return $this->collSpecimenComponentExperiments;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related SpecimenComponentExperiments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getSpecimenComponentExperimentsJoinCoordinatorRunExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentExperiments === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentExperiments = array();
			} else {

				$criteria->add(SpecimenComponentExperimentPeer::EXPID, $this->getId());

				$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelectJoinCoordinatorRunExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentExperimentPeer::EXPID, $this->getId());

			if (!isset($this->lastSpecimenComponentExperimentCriteria) || !$this->lastSpecimenComponentExperimentCriteria->equals($criteria)) {
				$this->collSpecimenComponentExperiments = SpecimenComponentExperimentPeer::doSelectJoinCoordinatorRunExperiment($criteria, $con);
			}
		}
		$this->lastSpecimenComponentExperimentCriteria = $criteria;

		return $this->collSpecimenComponentExperiments;
	}

	/**
	 * Temporary storage of collDataFileLinks to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDataFileLinks()
	{
		if ($this->collDataFileLinks === null) {
			$this->collDataFileLinks = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 * If this Experiment is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDataFileLinks($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFileLinkPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFileLinks === null) {
			if ($this->isNew()) {
			   $this->collDataFileLinks = array();
			} else {

				$criteria->add(DataFileLinkPeer::EXP_ID, $this->getId());

				DataFileLinkPeer::addSelectColumns($criteria);
				$this->collDataFileLinks = DataFileLinkPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DataFileLinkPeer::EXP_ID, $this->getId());

				DataFileLinkPeer::addSelectColumns($criteria);
				if (!isset($this->lastDataFileLinkCriteria) || !$this->lastDataFileLinkCriteria->equals($criteria)) {
					$this->collDataFileLinks = DataFileLinkPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDataFileLinkCriteria = $criteria;
		return $this->collDataFileLinks;
	}

	/**
	 * Returns the number of related DataFileLinks.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDataFileLinks($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFileLinkPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DataFileLinkPeer::EXP_ID, $this->getId());

		return DataFileLinkPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DataFileLink object to this object
	 * through the DataFileLink foreign key attribute
	 *
	 * @param      DataFileLink $l DataFileLink
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDataFileLink(DataFileLink $l)
	{
		$this->collDataFileLinks[] = $l;
		$l->setExperiment($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getDataFileLinksJoinProject($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFileLinkPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFileLinks === null) {
			if ($this->isNew()) {
				$this->collDataFileLinks = array();
			} else {

				$criteria->add(DataFileLinkPeer::EXP_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinProject($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::EXP_ID, $this->getId());

			if (!isset($this->lastDataFileLinkCriteria) || !$this->lastDataFileLinkCriteria->equals($criteria)) {
				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinProject($criteria, $con);
			}
		}
		$this->lastDataFileLinkCriteria = $criteria;

		return $this->collDataFileLinks;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getDataFileLinksJoinTrial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFileLinkPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFileLinks === null) {
			if ($this->isNew()) {
				$this->collDataFileLinks = array();
			} else {

				$criteria->add(DataFileLinkPeer::EXP_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::EXP_ID, $this->getId());

			if (!isset($this->lastDataFileLinkCriteria) || !$this->lastDataFileLinkCriteria->equals($criteria)) {
				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastDataFileLinkCriteria = $criteria;

		return $this->collDataFileLinks;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Experiment is new, it will return
	 * an empty collection; or if this Experiment has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Experiment.
	 */
	public function getDataFileLinksJoinRepetition($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFileLinkPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFileLinks === null) {
			if ($this->isNew()) {
				$this->collDataFileLinks = array();
			} else {

				$criteria->add(DataFileLinkPeer::EXP_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinRepetition($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::EXP_ID, $this->getId());

			if (!isset($this->lastDataFileLinkCriteria) || !$this->lastDataFileLinkCriteria->equals($criteria)) {
				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinRepetition($criteria, $con);
			}
		}
		$this->lastDataFileLinkCriteria = $criteria;

		return $this->collDataFileLinks;
	}

} // BaseExperiment
