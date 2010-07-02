<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/ProjectPeer.php';

/**
 * Base class that represents a row from the 'PROJECT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseProject extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ProjectPeer
	 */
	protected static $peer;


	/**
	 * The value for the projid field.
	 * @var        double
	 */
	protected $projid;


	/**
	 * The value for the contact_email field.
	 * @var        string
	 */
	protected $contact_email;


	/**
	 * The value for the contact_name field.
	 * @var        string
	 */
	protected $contact_name;


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
	 * The value for the fundorg field.
	 * @var        string
	 */
	protected $fundorg;


	/**
	 * The value for the fundorgprojid field.
	 * @var        string
	 */
	protected $fundorgprojid;


	/**
	 * The value for the nees field.
	 * @var        double
	 */
	protected $nees;


	/**
	 * The value for the nsftitle field.
	 * @var        string
	 */
	protected $nsftitle;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the nickname field.
	 * @var        string
	 */
	protected $nickname;


	/**
	 * The value for the short_title field.
	 * @var        string
	 */
	protected $short_title;


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
	 * The value for the sysadmin_email field.
	 * @var        string
	 */
	protected $sysadmin_email;


	/**
	 * The value for the sysadmin_name field.
	 * @var        string
	 */
	protected $sysadmin_name;


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
	 * The value for the super_project_id field.
	 * @var        double
	 */
	protected $super_project_id;


	/**
	 * The value for the project_type_id field.
	 * @var        double
	 */
	protected $project_type_id = 0;

	/**
	 * @var        Project
	 */
	protected $aProjectRelatedBySuperProjectId;

	/**
	 * @var        Person
	 */
	protected $aPerson;

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
	 * Collection to store aggregation of collExperiments.
	 * @var        array
	 */
	protected $collExperiments;

	/**
	 * The criteria used to select the current contents of collExperiments.
	 * @var        Criteria
	 */
	protected $lastExperimentCriteria = null;

	/**
	 * Collection to store aggregation of collProjectsRelatedBySuperProjectId.
	 * @var        array
	 */
	protected $collProjectsRelatedBySuperProjectId;

	/**
	 * The criteria used to select the current contents of collProjectsRelatedBySuperProjectId.
	 * @var        Criteria
	 */
	protected $lastProjectRelatedBySuperProjectIdCriteria = null;

	/**
	 * Collection to store aggregation of collProjectOrganizations.
	 * @var        array
	 */
	protected $collProjectOrganizations;

	/**
	 * The criteria used to select the current contents of collProjectOrganizations.
	 * @var        Criteria
	 */
	protected $lastProjectOrganizationCriteria = null;

	/**
	 * Collection to store aggregation of collProjectHomepages.
	 * @var        array
	 */
	protected $collProjectHomepages;

	/**
	 * The criteria used to select the current contents of collProjectHomepages.
	 * @var        Criteria
	 */
	protected $lastProjectHomepageCriteria = null;

	/**
	 * Collection to store aggregation of collSpecimens.
	 * @var        array
	 */
	protected $collSpecimens;

	/**
	 * The criteria used to select the current contents of collSpecimens.
	 * @var        Criteria
	 */
	protected $lastSpecimenCriteria = null;

	/**
	 * Collection to store aggregation of collCoordinators.
	 * @var        array
	 */
	protected $collCoordinators;

	/**
	 * The criteria used to select the current contents of collCoordinators.
	 * @var        Criteria
	 */
	protected $lastCoordinatorCriteria = null;

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
	 * Collection to store aggregation of collProjectGrants.
	 * @var        array
	 */
	protected $collProjectGrants;

	/**
	 * The criteria used to select the current contents of collProjectGrants.
	 * @var        Criteria
	 */
	protected $lastProjectGrantCriteria = null;

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
	 * Get the [projid] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->projid;
	}

	/**
	 * Get the [contact_email] column value.
	 * 
	 * @return     string
	 */
	public function getContactEmail()
	{

		return $this->contact_email;
	}

	/**
	 * Get the [contact_name] column value.
	 * 
	 * @return     string
	 */
	public function getContactName()
	{

		return $this->contact_name;
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
	 * Get the [fundorg] column value.
	 * 
	 * @return     string
	 */
	public function getFundorg()
	{

		return $this->fundorg;
	}

	/**
	 * Get the [fundorgprojid] column value.
	 * 
	 * @return     string
	 */
	public function getFundorgProjId()
	{

		return $this->fundorgprojid;
	}

	/**
	 * Get the [nees] column value.
	 * 
	 * @return     double
	 */
	public function getNEES()
	{

		return $this->nees;
	}

	/**
	 * Get the [nsftitle] column value.
	 * 
	 * @return     string
	 */
	public function getNSFTitle()
	{

		return $this->nsftitle;
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
	 * Get the [nickname] column value.
	 * 
	 * @return     string
	 */
	public function getNickname()
	{

		return $this->nickname;
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
	 * Get the [sysadmin_email] column value.
	 * 
	 * @return     string
	 */
	public function getSysadminEmail()
	{

		return $this->sysadmin_email;
	}

	/**
	 * Get the [sysadmin_name] column value.
	 * 
	 * @return     string
	 */
	public function getSysadminName()
	{

		return $this->sysadmin_name;
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
	 * Get the [super_project_id] column value.
	 * 
	 * @return     double
	 */
	public function getSuperProjectId()
	{

		return $this->super_project_id;
	}

	/**
	 * Get the [project_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getProjectTypeId()
	{

		return $this->project_type_id;
	}

	/**
	 * Set the value of [projid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->projid !== $v) {
			$this->projid = $v;
			$this->modifiedColumns[] = ProjectPeer::PROJID;
		}

	} // setId()

	/**
	 * Set the value of [contact_email] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactEmail($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->contact_email !== $v) {
			$this->contact_email = $v;
			$this->modifiedColumns[] = ProjectPeer::CONTACT_EMAIL;
		}

	} // setContactEmail()

	/**
	 * Set the value of [contact_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setContactName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->contact_name !== $v) {
			$this->contact_name = $v;
			$this->modifiedColumns[] = ProjectPeer::CONTACT_NAME;
		}

	} // setContactName()

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
			$this->modifiedColumns[] = ProjectPeer::CURATION_STATUS;
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
			$this->modifiedColumns[] = ProjectPeer::DELETED;
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
			$this->modifiedColumns[] = ProjectPeer::DESCRIPTION;
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
			$this->modifiedColumns[] = ProjectPeer::END_DATE;
		}

	} // setEndDate()

	/**
	 * Set the value of [fundorg] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFundorg($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->fundorg !== $v) {
			$this->fundorg = $v;
			$this->modifiedColumns[] = ProjectPeer::FUNDORG;
		}

	} // setFundorg()

	/**
	 * Set the value of [fundorgprojid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFundorgProjId($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->fundorgprojid !== $v) {
			$this->fundorgprojid = $v;
			$this->modifiedColumns[] = ProjectPeer::FUNDORGPROJID;
		}

	} // setFundorgProjId()

	/**
	 * Set the value of [nees] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNEES($v)
	{

		if ($this->nees !== $v) {
			$this->nees = $v;
			$this->modifiedColumns[] = ProjectPeer::NEES;
		}

	} // setNEES()

	/**
	 * Set the value of [nsftitle] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNSFTitle($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->nsftitle !== $v) {
			$this->nsftitle = $v;
			$this->modifiedColumns[] = ProjectPeer::NSFTITLE;
		}

	} // setNSFTitle()

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
			$this->modifiedColumns[] = ProjectPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [nickname] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNickname($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->nickname !== $v) {
			$this->nickname = $v;
			$this->modifiedColumns[] = ProjectPeer::NICKNAME;
		}

	} // setNickname()

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
			$this->modifiedColumns[] = ProjectPeer::SHORT_TITLE;
		}

	} // setShortTitle()

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
			$this->modifiedColumns[] = ProjectPeer::START_DATE;
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
			$this->modifiedColumns[] = ProjectPeer::STATUS;
		}

	} // setStatus()

	/**
	 * Set the value of [sysadmin_email] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSysadminEmail($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sysadmin_email !== $v) {
			$this->sysadmin_email = $v;
			$this->modifiedColumns[] = ProjectPeer::SYSADMIN_EMAIL;
		}

	} // setSysadminEmail()

	/**
	 * Set the value of [sysadmin_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSysadminName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sysadmin_name !== $v) {
			$this->sysadmin_name = $v;
			$this->modifiedColumns[] = ProjectPeer::SYSADMIN_NAME;
		}

	} // setSysadminName()

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
			$this->modifiedColumns[] = ProjectPeer::TITLE;
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
			$this->modifiedColumns[] = ProjectPeer::VIEWABLE;
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
			$this->modifiedColumns[] = ProjectPeer::CREATOR_ID;
		}

		if ($this->aPerson !== null && $this->aPerson->getId() !== $v) {
			$this->aPerson = null;
		}

	} // setCreatorId()

	/**
	 * Set the value of [super_project_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSuperProjectId($v)
	{

		if ($this->super_project_id !== $v) {
			$this->super_project_id = $v;
			$this->modifiedColumns[] = ProjectPeer::SUPER_PROJECT_ID;
		}

		if ($this->aProjectRelatedBySuperProjectId !== null && $this->aProjectRelatedBySuperProjectId->getId() !== $v) {
			$this->aProjectRelatedBySuperProjectId = null;
		}

	} // setSuperProjectId()

	/**
	 * Set the value of [project_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setProjectTypeId($v)
	{

		if ($this->project_type_id !== $v || $v === 0) {
			$this->project_type_id = $v;
			$this->modifiedColumns[] = ProjectPeer::PROJECT_TYPE_ID;
		}

	} // setProjectTypeId()

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

			$this->projid = $rs->getFloat($startcol + 0);

			$this->contact_email = $rs->getString($startcol + 1);

			$this->contact_name = $rs->getString($startcol + 2);

			$this->curation_status = $rs->getString($startcol + 3);

			$this->deleted = $rs->getFloat($startcol + 4);

			$this->description = $rs->getClob($startcol + 5);

			$this->end_date = $rs->getDate($startcol + 6, null);

			$this->fundorg = $rs->getString($startcol + 7);

			$this->fundorgprojid = $rs->getString($startcol + 8);

			$this->nees = $rs->getFloat($startcol + 9);

			$this->nsftitle = $rs->getString($startcol + 10);

			$this->name = $rs->getString($startcol + 11);

			$this->nickname = $rs->getString($startcol + 12);

			$this->short_title = $rs->getString($startcol + 13);

			$this->start_date = $rs->getDate($startcol + 14, null);

			$this->status = $rs->getString($startcol + 15);

			$this->sysadmin_email = $rs->getString($startcol + 16);

			$this->sysadmin_name = $rs->getString($startcol + 17);

			$this->title = $rs->getString($startcol + 18);

			$this->viewable = $rs->getString($startcol + 19);

			$this->creator_id = $rs->getFloat($startcol + 20);

			$this->super_project_id = $rs->getFloat($startcol + 21);

			$this->project_type_id = $rs->getFloat($startcol + 22);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 23; // 23 = ProjectPeer::NUM_COLUMNS - ProjectPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Project object", $e);
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
			$con = Propel::getConnection(ProjectPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			ProjectPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ProjectPeer::DATABASE_NAME);
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

			if ($this->aProjectRelatedBySuperProjectId !== null) {
				if ($this->aProjectRelatedBySuperProjectId->isModified()) {
					$affectedRows += $this->aProjectRelatedBySuperProjectId->save($con);
				}
				$this->setProjectRelatedBySuperProjectId($this->aProjectRelatedBySuperProjectId);
			}

			if ($this->aPerson !== null) {
				if ($this->aPerson->isModified()) {
					$affectedRows += $this->aPerson->save($con);
				}
				$this->setPerson($this->aPerson);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = ProjectPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += ProjectPeer::doUpdate($this, $con);
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

			if ($this->collExperiments !== null) {
				foreach($this->collExperiments as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collProjectsRelatedBySuperProjectId !== null) {
				foreach($this->collProjectsRelatedBySuperProjectId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collProjectOrganizations !== null) {
				foreach($this->collProjectOrganizations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collProjectHomepages !== null) {
				foreach($this->collProjectHomepages as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSpecimens !== null) {
				foreach($this->collSpecimens as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinators !== null) {
				foreach($this->collCoordinators as $referrerFK) {
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

			if ($this->collProjectGrants !== null) {
				foreach($this->collProjectGrants as $referrerFK) {
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

			if ($this->aProjectRelatedBySuperProjectId !== null) {
				if (!$this->aProjectRelatedBySuperProjectId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aProjectRelatedBySuperProjectId->getValidationFailures());
				}
			}

			if ($this->aPerson !== null) {
				if (!$this->aPerson->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aPerson->getValidationFailures());
				}
			}


			if (($retval = ProjectPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAcknowledgements !== null) {
					foreach($this->collAcknowledgements as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collExperiments !== null) {
					foreach($this->collExperiments as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collProjectOrganizations !== null) {
					foreach($this->collProjectOrganizations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collProjectHomepages !== null) {
					foreach($this->collProjectHomepages as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSpecimens !== null) {
					foreach($this->collSpecimens as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCoordinators !== null) {
					foreach($this->collCoordinators as $referrerFK) {
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

				if ($this->collProjectGrants !== null) {
					foreach($this->collProjectGrants as $referrerFK) {
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
		$pos = ProjectPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getContactEmail();
				break;
			case 2:
				return $this->getContactName();
				break;
			case 3:
				return $this->getCurationStatus();
				break;
			case 4:
				return $this->getDeleted();
				break;
			case 5:
				return $this->getDescription();
				break;
			case 6:
				return $this->getEndDate();
				break;
			case 7:
				return $this->getFundorg();
				break;
			case 8:
				return $this->getFundorgProjId();
				break;
			case 9:
				return $this->getNEES();
				break;
			case 10:
				return $this->getNSFTitle();
				break;
			case 11:
				return $this->getName();
				break;
			case 12:
				return $this->getNickname();
				break;
			case 13:
				return $this->getShortTitle();
				break;
			case 14:
				return $this->getStartDate();
				break;
			case 15:
				return $this->getStatus();
				break;
			case 16:
				return $this->getSysadminEmail();
				break;
			case 17:
				return $this->getSysadminName();
				break;
			case 18:
				return $this->getTitle();
				break;
			case 19:
				return $this->getView();
				break;
			case 20:
				return $this->getCreatorId();
				break;
			case 21:
				return $this->getSuperProjectId();
				break;
			case 22:
				return $this->getProjectTypeId();
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
		$keys = ProjectPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getContactEmail(),
			$keys[2] => $this->getContactName(),
			$keys[3] => $this->getCurationStatus(),
			$keys[4] => $this->getDeleted(),
			$keys[5] => $this->getDescription(),
			$keys[6] => $this->getEndDate(),
			$keys[7] => $this->getFundorg(),
			$keys[8] => $this->getFundorgProjId(),
			$keys[9] => $this->getNEES(),
			$keys[10] => $this->getNSFTitle(),
			$keys[11] => $this->getName(),
			$keys[12] => $this->getNickname(),
			$keys[13] => $this->getShortTitle(),
			$keys[14] => $this->getStartDate(),
			$keys[15] => $this->getStatus(),
			$keys[16] => $this->getSysadminEmail(),
			$keys[17] => $this->getSysadminName(),
			$keys[18] => $this->getTitle(),
			$keys[19] => $this->getView(),
			$keys[20] => $this->getCreatorId(),
			$keys[21] => $this->getSuperProjectId(),
			$keys[22] => $this->getProjectTypeId(),
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
		$pos = ProjectPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setContactEmail($value);
				break;
			case 2:
				$this->setContactName($value);
				break;
			case 3:
				$this->setCurationStatus($value);
				break;
			case 4:
				$this->setDeleted($value);
				break;
			case 5:
				$this->setDescription($value);
				break;
			case 6:
				$this->setEndDate($value);
				break;
			case 7:
				$this->setFundorg($value);
				break;
			case 8:
				$this->setFundorgProjId($value);
				break;
			case 9:
				$this->setNEES($value);
				break;
			case 10:
				$this->setNSFTitle($value);
				break;
			case 11:
				$this->setName($value);
				break;
			case 12:
				$this->setNickname($value);
				break;
			case 13:
				$this->setShortTitle($value);
				break;
			case 14:
				$this->setStartDate($value);
				break;
			case 15:
				$this->setStatus($value);
				break;
			case 16:
				$this->setSysadminEmail($value);
				break;
			case 17:
				$this->setSysadminName($value);
				break;
			case 18:
				$this->setTitle($value);
				break;
			case 19:
				$this->setView($value);
				break;
			case 20:
				$this->setCreatorId($value);
				break;
			case 21:
				$this->setSuperProjectId($value);
				break;
			case 22:
				$this->setProjectTypeId($value);
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
		$keys = ProjectPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setContactEmail($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setContactName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCurationStatus($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDeleted($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDescription($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setEndDate($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setFundorg($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setFundorgProjId($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setNEES($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setNSFTitle($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setName($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setNickname($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setShortTitle($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setStartDate($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setStatus($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setSysadminEmail($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setSysadminName($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setTitle($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setView($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setCreatorId($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setSuperProjectId($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setProjectTypeId($arr[$keys[22]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ProjectPeer::DATABASE_NAME);

		if ($this->isColumnModified(ProjectPeer::PROJID)) $criteria->add(ProjectPeer::PROJID, $this->projid);
		if ($this->isColumnModified(ProjectPeer::CONTACT_EMAIL)) $criteria->add(ProjectPeer::CONTACT_EMAIL, $this->contact_email);
		if ($this->isColumnModified(ProjectPeer::CONTACT_NAME)) $criteria->add(ProjectPeer::CONTACT_NAME, $this->contact_name);
		if ($this->isColumnModified(ProjectPeer::CURATION_STATUS)) $criteria->add(ProjectPeer::CURATION_STATUS, $this->curation_status);
		if ($this->isColumnModified(ProjectPeer::DELETED)) $criteria->add(ProjectPeer::DELETED, $this->deleted);
		if ($this->isColumnModified(ProjectPeer::DESCRIPTION)) $criteria->add(ProjectPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(ProjectPeer::END_DATE)) $criteria->add(ProjectPeer::END_DATE, $this->end_date);
		if ($this->isColumnModified(ProjectPeer::FUNDORG)) $criteria->add(ProjectPeer::FUNDORG, $this->fundorg);
		if ($this->isColumnModified(ProjectPeer::FUNDORGPROJID)) $criteria->add(ProjectPeer::FUNDORGPROJID, $this->fundorgprojid);
		if ($this->isColumnModified(ProjectPeer::NEES)) $criteria->add(ProjectPeer::NEES, $this->nees);
		if ($this->isColumnModified(ProjectPeer::NSFTITLE)) $criteria->add(ProjectPeer::NSFTITLE, $this->nsftitle);
		if ($this->isColumnModified(ProjectPeer::NAME)) $criteria->add(ProjectPeer::NAME, $this->name);
		if ($this->isColumnModified(ProjectPeer::NICKNAME)) $criteria->add(ProjectPeer::NICKNAME, $this->nickname);
		if ($this->isColumnModified(ProjectPeer::SHORT_TITLE)) $criteria->add(ProjectPeer::SHORT_TITLE, $this->short_title);
		if ($this->isColumnModified(ProjectPeer::START_DATE)) $criteria->add(ProjectPeer::START_DATE, $this->start_date);
		if ($this->isColumnModified(ProjectPeer::STATUS)) $criteria->add(ProjectPeer::STATUS, $this->status);
		if ($this->isColumnModified(ProjectPeer::SYSADMIN_EMAIL)) $criteria->add(ProjectPeer::SYSADMIN_EMAIL, $this->sysadmin_email);
		if ($this->isColumnModified(ProjectPeer::SYSADMIN_NAME)) $criteria->add(ProjectPeer::SYSADMIN_NAME, $this->sysadmin_name);
		if ($this->isColumnModified(ProjectPeer::TITLE)) $criteria->add(ProjectPeer::TITLE, $this->title);
		if ($this->isColumnModified(ProjectPeer::VIEWABLE)) $criteria->add(ProjectPeer::VIEWABLE, $this->viewable);
		if ($this->isColumnModified(ProjectPeer::CREATOR_ID)) $criteria->add(ProjectPeer::CREATOR_ID, $this->creator_id);
		if ($this->isColumnModified(ProjectPeer::SUPER_PROJECT_ID)) $criteria->add(ProjectPeer::SUPER_PROJECT_ID, $this->super_project_id);
		if ($this->isColumnModified(ProjectPeer::PROJECT_TYPE_ID)) $criteria->add(ProjectPeer::PROJECT_TYPE_ID, $this->project_type_id);

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
		$criteria = new Criteria(ProjectPeer::DATABASE_NAME);

		$criteria->add(ProjectPeer::PROJID, $this->projid);

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
	 * Generic method to set the primary key (projid column).
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
	 * @param      object $copyObj An object of Project (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setContactEmail($this->contact_email);

		$copyObj->setContactName($this->contact_name);

		$copyObj->setCurationStatus($this->curation_status);

		$copyObj->setDeleted($this->deleted);

		$copyObj->setDescription($this->description);

		$copyObj->setEndDate($this->end_date);

		$copyObj->setFundorg($this->fundorg);

		$copyObj->setFundorgProjId($this->fundorgprojid);

		$copyObj->setNEES($this->nees);

		$copyObj->setNSFTitle($this->nsftitle);

		$copyObj->setName($this->name);

		$copyObj->setNickname($this->nickname);

		$copyObj->setShortTitle($this->short_title);

		$copyObj->setStartDate($this->start_date);

		$copyObj->setStatus($this->status);

		$copyObj->setSysadminEmail($this->sysadmin_email);

		$copyObj->setSysadminName($this->sysadmin_name);

		$copyObj->setTitle($this->title);

		$copyObj->setView($this->viewable);

		$copyObj->setCreatorId($this->creator_id);

		$copyObj->setSuperProjectId($this->super_project_id);

		$copyObj->setProjectTypeId($this->project_type_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getAcknowledgements() as $relObj) {
				$copyObj->addAcknowledgement($relObj->copy($deepCopy));
			}

			foreach($this->getExperiments() as $relObj) {
				$copyObj->addExperiment($relObj->copy($deepCopy));
			}

			foreach($this->getProjectsRelatedBySuperProjectId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addProjectRelatedBySuperProjectId($relObj->copy($deepCopy));
			}

			foreach($this->getProjectOrganizations() as $relObj) {
				$copyObj->addProjectOrganization($relObj->copy($deepCopy));
			}

			foreach($this->getProjectHomepages() as $relObj) {
				$copyObj->addProjectHomepage($relObj->copy($deepCopy));
			}

			foreach($this->getSpecimens() as $relObj) {
				$copyObj->addSpecimen($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinators() as $relObj) {
				$copyObj->addCoordinator($relObj->copy($deepCopy));
			}

			foreach($this->getDataFileLinks() as $relObj) {
				$copyObj->addDataFileLink($relObj->copy($deepCopy));
			}

			foreach($this->getProjectGrants() as $relObj) {
				$copyObj->addProjectGrant($relObj->copy($deepCopy));
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
	 * @return     Project Clone of current object.
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
	 * @return     ProjectPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ProjectPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Project object.
	 *
	 * @param      Project $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setProjectRelatedBySuperProjectId($v)
	{


		if ($v === null) {
			$this->setSuperProjectId(NULL);
		} else {
			$this->setSuperProjectId($v->getId());
		}


		$this->aProjectRelatedBySuperProjectId = $v;
	}


	/**
	 * Get the associated Project object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Project The associated Project object.
	 * @throws     PropelException
	 */
	public function getProjectRelatedBySuperProjectId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseProjectPeer.php';

		if ($this->aProjectRelatedBySuperProjectId === null && ($this->super_project_id > 0)) {

			$this->aProjectRelatedBySuperProjectId = ProjectPeer::retrieveByPK($this->super_project_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = ProjectPeer::retrieveByPK($this->super_project_id, $con);
			   $obj->addProjectsRelatedBySuperProjectId($this);
			 */
		}
		return $this->aProjectRelatedBySuperProjectId;
	}

	/**
	 * Declares an association between this object and a Person object.
	 *
	 * @param      Person $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setPerson($v)
	{


		if ($v === null) {
			$this->setCreatorId(NULL);
		} else {
			$this->setCreatorId($v->getId());
		}


		$this->aPerson = $v;
	}


	/**
	 * Get the associated Person object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Person The associated Person object.
	 * @throws     PropelException
	 */
	public function getPerson($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BasePersonPeer.php';

		if ($this->aPerson === null && ($this->creator_id > 0)) {

			$this->aPerson = PersonPeer::retrieveByPK($this->creator_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = PersonPeer::retrieveByPK($this->creator_id, $con);
			   $obj->addPersons($this);
			 */
		}
		return $this->aPerson;
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
	 * Otherwise if this Project has previously
	 * been saved, it will retrieve related Acknowledgements from storage.
	 * If this Project is new, it will return
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

				$criteria->add(AcknowledgementPeer::PROJID, $this->getId());

				AcknowledgementPeer::addSelectColumns($criteria);
				$this->collAcknowledgements = AcknowledgementPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AcknowledgementPeer::PROJID, $this->getId());

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

		$criteria->add(AcknowledgementPeer::PROJID, $this->getId());

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
		$l->setProject($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project is new, it will return
	 * an empty collection; or if this Project has previously
	 * been saved, it will retrieve related Acknowledgements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Project.
	 */
	public function getAcknowledgementsJoinExperiment($criteria = null, $con = null)
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

				$criteria->add(AcknowledgementPeer::PROJID, $this->getId());

				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AcknowledgementPeer::PROJID, $this->getId());

			if (!isset($this->lastAcknowledgementCriteria) || !$this->lastAcknowledgementCriteria->equals($criteria)) {
				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastAcknowledgementCriteria = $criteria;

		return $this->collAcknowledgements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project is new, it will return
	 * an empty collection; or if this Project has previously
	 * been saved, it will retrieve related Acknowledgements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Project.
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

				$criteria->add(AcknowledgementPeer::PROJID, $this->getId());

				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AcknowledgementPeer::PROJID, $this->getId());

			if (!isset($this->lastAcknowledgementCriteria) || !$this->lastAcknowledgementCriteria->equals($criteria)) {
				$this->collAcknowledgements = AcknowledgementPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastAcknowledgementCriteria = $criteria;

		return $this->collAcknowledgements;
	}

	/**
	 * Temporary storage of collExperiments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initExperiments()
	{
		if ($this->collExperiments === null) {
			$this->collExperiments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project has previously
	 * been saved, it will retrieve related Experiments from storage.
	 * If this Project is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getExperiments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperiments === null) {
			if ($this->isNew()) {
			   $this->collExperiments = array();
			} else {

				$criteria->add(ExperimentPeer::PROJID, $this->getId());

				ExperimentPeer::addSelectColumns($criteria);
				$this->collExperiments = ExperimentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentPeer::PROJID, $this->getId());

				ExperimentPeer::addSelectColumns($criteria);
				if (!isset($this->lastExperimentCriteria) || !$this->lastExperimentCriteria->equals($criteria)) {
					$this->collExperiments = ExperimentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastExperimentCriteria = $criteria;
		return $this->collExperiments;
	}

	/**
	 * Returns the number of related Experiments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countExperiments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ExperimentPeer::PROJID, $this->getId());

		return ExperimentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Experiment object to this object
	 * through the Experiment foreign key attribute
	 *
	 * @param      Experiment $l Experiment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addExperiment(Experiment $l)
	{
		$this->collExperiments[] = $l;
		$l->setProject($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project is new, it will return
	 * an empty collection; or if this Project has previously
	 * been saved, it will retrieve related Experiments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Project.
	 */
	public function getExperimentsJoinExperimentDomain($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperiments === null) {
			if ($this->isNew()) {
				$this->collExperiments = array();
			} else {

				$criteria->add(ExperimentPeer::PROJID, $this->getId());

				$this->collExperiments = ExperimentPeer::doSelectJoinExperimentDomain($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentPeer::PROJID, $this->getId());

			if (!isset($this->lastExperimentCriteria) || !$this->lastExperimentCriteria->equals($criteria)) {
				$this->collExperiments = ExperimentPeer::doSelectJoinExperimentDomain($criteria, $con);
			}
		}
		$this->lastExperimentCriteria = $criteria;

		return $this->collExperiments;
	}

	/**
	 * Temporary storage of collProjectsRelatedBySuperProjectId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initProjectsRelatedBySuperProjectId()
	{
		if ($this->collProjectsRelatedBySuperProjectId === null) {
			$this->collProjectsRelatedBySuperProjectId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project has previously
	 * been saved, it will retrieve related ProjectsRelatedBySuperProjectId from storage.
	 * If this Project is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getProjectsRelatedBySuperProjectId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjectsRelatedBySuperProjectId === null) {
			if ($this->isNew()) {
			   $this->collProjectsRelatedBySuperProjectId = array();
			} else {

				$criteria->add(ProjectPeer::SUPER_PROJECT_ID, $this->getId());

				ProjectPeer::addSelectColumns($criteria);
				$this->collProjectsRelatedBySuperProjectId = ProjectPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ProjectPeer::SUPER_PROJECT_ID, $this->getId());

				ProjectPeer::addSelectColumns($criteria);
				if (!isset($this->lastProjectRelatedBySuperProjectIdCriteria) || !$this->lastProjectRelatedBySuperProjectIdCriteria->equals($criteria)) {
					$this->collProjectsRelatedBySuperProjectId = ProjectPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastProjectRelatedBySuperProjectIdCriteria = $criteria;
		return $this->collProjectsRelatedBySuperProjectId;
	}

	/**
	 * Returns the number of related ProjectsRelatedBySuperProjectId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countProjectsRelatedBySuperProjectId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ProjectPeer::SUPER_PROJECT_ID, $this->getId());

		return ProjectPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Project object to this object
	 * through the Project foreign key attribute
	 *
	 * @param      Project $l Project
	 * @return     void
	 * @throws     PropelException
	 */
	public function addProjectRelatedBySuperProjectId(Project $l)
	{
		$this->collProjectsRelatedBySuperProjectId[] = $l;
		$l->setProjectRelatedBySuperProjectId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project is new, it will return
	 * an empty collection; or if this Project has previously
	 * been saved, it will retrieve related ProjectsRelatedBySuperProjectId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Project.
	 */
	public function getProjectsRelatedBySuperProjectIdJoinPerson($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjectsRelatedBySuperProjectId === null) {
			if ($this->isNew()) {
				$this->collProjectsRelatedBySuperProjectId = array();
			} else {

				$criteria->add(ProjectPeer::SUPER_PROJECT_ID, $this->getId());

				$this->collProjectsRelatedBySuperProjectId = ProjectPeer::doSelectJoinPerson($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ProjectPeer::SUPER_PROJECT_ID, $this->getId());

			if (!isset($this->lastProjectRelatedBySuperProjectIdCriteria) || !$this->lastProjectRelatedBySuperProjectIdCriteria->equals($criteria)) {
				$this->collProjectsRelatedBySuperProjectId = ProjectPeer::doSelectJoinPerson($criteria, $con);
			}
		}
		$this->lastProjectRelatedBySuperProjectIdCriteria = $criteria;

		return $this->collProjectsRelatedBySuperProjectId;
	}

	/**
	 * Temporary storage of collProjectOrganizations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initProjectOrganizations()
	{
		if ($this->collProjectOrganizations === null) {
			$this->collProjectOrganizations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project has previously
	 * been saved, it will retrieve related ProjectOrganizations from storage.
	 * If this Project is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getProjectOrganizations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjectOrganizations === null) {
			if ($this->isNew()) {
			   $this->collProjectOrganizations = array();
			} else {

				$criteria->add(ProjectOrganizationPeer::PROJID, $this->getId());

				ProjectOrganizationPeer::addSelectColumns($criteria);
				$this->collProjectOrganizations = ProjectOrganizationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ProjectOrganizationPeer::PROJID, $this->getId());

				ProjectOrganizationPeer::addSelectColumns($criteria);
				if (!isset($this->lastProjectOrganizationCriteria) || !$this->lastProjectOrganizationCriteria->equals($criteria)) {
					$this->collProjectOrganizations = ProjectOrganizationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastProjectOrganizationCriteria = $criteria;
		return $this->collProjectOrganizations;
	}

	/**
	 * Returns the number of related ProjectOrganizations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countProjectOrganizations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ProjectOrganizationPeer::PROJID, $this->getId());

		return ProjectOrganizationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ProjectOrganization object to this object
	 * through the ProjectOrganization foreign key attribute
	 *
	 * @param      ProjectOrganization $l ProjectOrganization
	 * @return     void
	 * @throws     PropelException
	 */
	public function addProjectOrganization(ProjectOrganization $l)
	{
		$this->collProjectOrganizations[] = $l;
		$l->setProject($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project is new, it will return
	 * an empty collection; or if this Project has previously
	 * been saved, it will retrieve related ProjectOrganizations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Project.
	 */
	public function getProjectOrganizationsJoinOrganization($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjectOrganizations === null) {
			if ($this->isNew()) {
				$this->collProjectOrganizations = array();
			} else {

				$criteria->add(ProjectOrganizationPeer::PROJID, $this->getId());

				$this->collProjectOrganizations = ProjectOrganizationPeer::doSelectJoinOrganization($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ProjectOrganizationPeer::PROJID, $this->getId());

			if (!isset($this->lastProjectOrganizationCriteria) || !$this->lastProjectOrganizationCriteria->equals($criteria)) {
				$this->collProjectOrganizations = ProjectOrganizationPeer::doSelectJoinOrganization($criteria, $con);
			}
		}
		$this->lastProjectOrganizationCriteria = $criteria;

		return $this->collProjectOrganizations;
	}

	/**
	 * Temporary storage of collProjectHomepages to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initProjectHomepages()
	{
		if ($this->collProjectHomepages === null) {
			$this->collProjectHomepages = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project has previously
	 * been saved, it will retrieve related ProjectHomepages from storage.
	 * If this Project is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getProjectHomepages($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectHomepagePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjectHomepages === null) {
			if ($this->isNew()) {
			   $this->collProjectHomepages = array();
			} else {

				$criteria->add(ProjectHomepagePeer::PROJECT_ID, $this->getId());

				ProjectHomepagePeer::addSelectColumns($criteria);
				$this->collProjectHomepages = ProjectHomepagePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ProjectHomepagePeer::PROJECT_ID, $this->getId());

				ProjectHomepagePeer::addSelectColumns($criteria);
				if (!isset($this->lastProjectHomepageCriteria) || !$this->lastProjectHomepageCriteria->equals($criteria)) {
					$this->collProjectHomepages = ProjectHomepagePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastProjectHomepageCriteria = $criteria;
		return $this->collProjectHomepages;
	}

	/**
	 * Returns the number of related ProjectHomepages.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countProjectHomepages($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectHomepagePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ProjectHomepagePeer::PROJECT_ID, $this->getId());

		return ProjectHomepagePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ProjectHomepage object to this object
	 * through the ProjectHomepage foreign key attribute
	 *
	 * @param      ProjectHomepage $l ProjectHomepage
	 * @return     void
	 * @throws     PropelException
	 */
	public function addProjectHomepage(ProjectHomepage $l)
	{
		$this->collProjectHomepages[] = $l;
		$l->setProject($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project is new, it will return
	 * an empty collection; or if this Project has previously
	 * been saved, it will retrieve related ProjectHomepages from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Project.
	 */
	public function getProjectHomepagesJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectHomepagePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjectHomepages === null) {
			if ($this->isNew()) {
				$this->collProjectHomepages = array();
			} else {

				$criteria->add(ProjectHomepagePeer::PROJECT_ID, $this->getId());

				$this->collProjectHomepages = ProjectHomepagePeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ProjectHomepagePeer::PROJECT_ID, $this->getId());

			if (!isset($this->lastProjectHomepageCriteria) || !$this->lastProjectHomepageCriteria->equals($criteria)) {
				$this->collProjectHomepages = ProjectHomepagePeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastProjectHomepageCriteria = $criteria;

		return $this->collProjectHomepages;
	}

	/**
	 * Temporary storage of collSpecimens to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSpecimens()
	{
		if ($this->collSpecimens === null) {
			$this->collSpecimens = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project has previously
	 * been saved, it will retrieve related Specimens from storage.
	 * If this Project is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSpecimens($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimens === null) {
			if ($this->isNew()) {
			   $this->collSpecimens = array();
			} else {

				$criteria->add(SpecimenPeer::PROJID, $this->getId());

				SpecimenPeer::addSelectColumns($criteria);
				$this->collSpecimens = SpecimenPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenPeer::PROJID, $this->getId());

				SpecimenPeer::addSelectColumns($criteria);
				if (!isset($this->lastSpecimenCriteria) || !$this->lastSpecimenCriteria->equals($criteria)) {
					$this->collSpecimens = SpecimenPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSpecimenCriteria = $criteria;
		return $this->collSpecimens;
	}

	/**
	 * Returns the number of related Specimens.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSpecimens($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SpecimenPeer::PROJID, $this->getId());

		return SpecimenPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Specimen object to this object
	 * through the Specimen foreign key attribute
	 *
	 * @param      Specimen $l Specimen
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSpecimen(Specimen $l)
	{
		$this->collSpecimens[] = $l;
		$l->setProject($this);
	}

	/**
	 * Temporary storage of collCoordinators to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinators()
	{
		if ($this->collCoordinators === null) {
			$this->collCoordinators = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project has previously
	 * been saved, it will retrieve related Coordinators from storage.
	 * If this Project is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinators($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinators === null) {
			if ($this->isNew()) {
			   $this->collCoordinators = array();
			} else {

				$criteria->add(CoordinatorPeer::PROJID, $this->getId());

				CoordinatorPeer::addSelectColumns($criteria);
				$this->collCoordinators = CoordinatorPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinatorPeer::PROJID, $this->getId());

				CoordinatorPeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinatorCriteria) || !$this->lastCoordinatorCriteria->equals($criteria)) {
					$this->collCoordinators = CoordinatorPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinatorCriteria = $criteria;
		return $this->collCoordinators;
	}

	/**
	 * Returns the number of related Coordinators.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinators($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(CoordinatorPeer::PROJID, $this->getId());

		return CoordinatorPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Coordinator object to this object
	 * through the Coordinator foreign key attribute
	 *
	 * @param      Coordinator $l Coordinator
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCoordinator(Coordinator $l)
	{
		$this->collCoordinators[] = $l;
		$l->setProject($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project is new, it will return
	 * an empty collection; or if this Project has previously
	 * been saved, it will retrieve related Coordinators from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Project.
	 */
	public function getCoordinatorsJoinOrganization($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinators === null) {
			if ($this->isNew()) {
				$this->collCoordinators = array();
			} else {

				$criteria->add(CoordinatorPeer::PROJID, $this->getId());

				$this->collCoordinators = CoordinatorPeer::doSelectJoinOrganization($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinatorPeer::PROJID, $this->getId());

			if (!isset($this->lastCoordinatorCriteria) || !$this->lastCoordinatorCriteria->equals($criteria)) {
				$this->collCoordinators = CoordinatorPeer::doSelectJoinOrganization($criteria, $con);
			}
		}
		$this->lastCoordinatorCriteria = $criteria;

		return $this->collCoordinators;
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
	 * Otherwise if this Project has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 * If this Project is new, it will return
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

				$criteria->add(DataFileLinkPeer::PROJ_ID, $this->getId());

				DataFileLinkPeer::addSelectColumns($criteria);
				$this->collDataFileLinks = DataFileLinkPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DataFileLinkPeer::PROJ_ID, $this->getId());

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

		$criteria->add(DataFileLinkPeer::PROJ_ID, $this->getId());

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
		$l->setProject($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project is new, it will return
	 * an empty collection; or if this Project has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Project.
	 */
	public function getDataFileLinksJoinExperiment($criteria = null, $con = null)
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

				$criteria->add(DataFileLinkPeer::PROJ_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::PROJ_ID, $this->getId());

			if (!isset($this->lastDataFileLinkCriteria) || !$this->lastDataFileLinkCriteria->equals($criteria)) {
				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastDataFileLinkCriteria = $criteria;

		return $this->collDataFileLinks;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project is new, it will return
	 * an empty collection; or if this Project has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Project.
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

				$criteria->add(DataFileLinkPeer::PROJ_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::PROJ_ID, $this->getId());

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
	 * Otherwise if this Project is new, it will return
	 * an empty collection; or if this Project has previously
	 * been saved, it will retrieve related DataFileLinks from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Project.
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

				$criteria->add(DataFileLinkPeer::PROJ_ID, $this->getId());

				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinRepetition($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFileLinkPeer::PROJ_ID, $this->getId());

			if (!isset($this->lastDataFileLinkCriteria) || !$this->lastDataFileLinkCriteria->equals($criteria)) {
				$this->collDataFileLinks = DataFileLinkPeer::doSelectJoinRepetition($criteria, $con);
			}
		}
		$this->lastDataFileLinkCriteria = $criteria;

		return $this->collDataFileLinks;
	}

	/**
	 * Temporary storage of collProjectGrants to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initProjectGrants()
	{
		if ($this->collProjectGrants === null) {
			$this->collProjectGrants = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Project has previously
	 * been saved, it will retrieve related ProjectGrants from storage.
	 * If this Project is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getProjectGrants($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectGrantPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjectGrants === null) {
			if ($this->isNew()) {
			   $this->collProjectGrants = array();
			} else {

				$criteria->add(ProjectGrantPeer::PROJID, $this->getId());

				ProjectGrantPeer::addSelectColumns($criteria);
				$this->collProjectGrants = ProjectGrantPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ProjectGrantPeer::PROJID, $this->getId());

				ProjectGrantPeer::addSelectColumns($criteria);
				if (!isset($this->lastProjectGrantCriteria) || !$this->lastProjectGrantCriteria->equals($criteria)) {
					$this->collProjectGrants = ProjectGrantPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastProjectGrantCriteria = $criteria;
		return $this->collProjectGrants;
	}

	/**
	 * Returns the number of related ProjectGrants.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countProjectGrants($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectGrantPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ProjectGrantPeer::PROJID, $this->getId());

		return ProjectGrantPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ProjectGrant object to this object
	 * through the ProjectGrant foreign key attribute
	 *
	 * @param      ProjectGrant $l ProjectGrant
	 * @return     void
	 * @throws     PropelException
	 */
	public function addProjectGrant(ProjectGrant $l)
	{
		$this->collProjectGrants[] = $l;
		$l->setProject($this);
	}

} // BaseProject
