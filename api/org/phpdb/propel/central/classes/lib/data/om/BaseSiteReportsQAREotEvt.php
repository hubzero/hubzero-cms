<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SiteReportsQAREotEvtPeer.php';

/**
 * Base class that represents a row from the 'SITEREPORTS_QAR_EOT_EVT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSiteReportsQAREotEvt extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SiteReportsQAREotEvtPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the qar_id field.
	 * @var        double
	 */
	protected $qar_id;


	/**
	 * The value for the event_type field.
	 * @var        string
	 */
	protected $event_type;


	/**
	 * The value for the activity field.
	 * @var        string
	 */
	protected $activity;


	/**
	 * The value for the activity_objectives field.
	 * @var        string
	 */
	protected $activity_objectives;


	/**
	 * The value for the objective_met field.
	 * @var        string
	 */
	protected $objective_met;


	/**
	 * The value for the participant_cat1 field.
	 * @var        string
	 */
	protected $participant_cat1;


	/**
	 * The value for the num_of_participants1 field.
	 * @var        double
	 */
	protected $num_of_participants1;


	/**
	 * The value for the participant_details1 field.
	 * @var        string
	 */
	protected $participant_details1;


	/**
	 * The value for the participant_cat2 field.
	 * @var        string
	 */
	protected $participant_cat2;


	/**
	 * The value for the num_of_participants2 field.
	 * @var        double
	 */
	protected $num_of_participants2;


	/**
	 * The value for the participant_details2 field.
	 * @var        string
	 */
	protected $participant_details2;


	/**
	 * The value for the participant_cat3 field.
	 * @var        string
	 */
	protected $participant_cat3;


	/**
	 * The value for the num_of_participants3 field.
	 * @var        double
	 */
	protected $num_of_participants3;


	/**
	 * The value for the participant_details3 field.
	 * @var        string
	 */
	protected $participant_details3;


	/**
	 * The value for the participant_cat4 field.
	 * @var        string
	 */
	protected $participant_cat4;


	/**
	 * The value for the num_of_participants4 field.
	 * @var        double
	 */
	protected $num_of_participants4;


	/**
	 * The value for the participant_details4 field.
	 * @var        string
	 */
	protected $participant_details4;


	/**
	 * The value for the event_nar field.
	 * @var        string
	 */
	protected $event_nar;


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
	 * @var        SiteReportsQAR
	 */
	protected $aSiteReportsQAR;

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
	 * Get the [qar_id] column value.
	 * 
	 * @return     double
	 */
	public function getQAR_ID()
	{

		return $this->qar_id;
	}

	/**
	 * Get the [event_type] column value.
	 * 
	 * @return     string
	 */
	public function getEVENT_TYPE()
	{

		return $this->event_type;
	}

	/**
	 * Get the [activity] column value.
	 * 
	 * @return     string
	 */
	public function getACTIVITY()
	{

		return $this->activity;
	}

	/**
	 * Get the [activity_objectives] column value.
	 * 
	 * @return     string
	 */
	public function getACTIVITY_OBJECTIVES()
	{

		return $this->activity_objectives;
	}

	/**
	 * Get the [objective_met] column value.
	 * 
	 * @return     string
	 */
	public function getOBJECTIVE_MET()
	{

		return $this->objective_met;
	}

	/**
	 * Get the [participant_cat1] column value.
	 * 
	 * @return     string
	 */
	public function getPARTICIPANT_CAT1()
	{

		return $this->participant_cat1;
	}

	/**
	 * Get the [num_of_participants1] column value.
	 * 
	 * @return     double
	 */
	public function getNUM_OF_PARTICIPANTS1()
	{

		return $this->num_of_participants1;
	}

	/**
	 * Get the [participant_details1] column value.
	 * 
	 * @return     string
	 */
	public function getPARTICIPANT_DETAILS1()
	{

		return $this->participant_details1;
	}

	/**
	 * Get the [participant_cat2] column value.
	 * 
	 * @return     string
	 */
	public function getPARTICIPANT_CAT2()
	{

		return $this->participant_cat2;
	}

	/**
	 * Get the [num_of_participants2] column value.
	 * 
	 * @return     double
	 */
	public function getNUM_OF_PARTICIPANTS2()
	{

		return $this->num_of_participants2;
	}

	/**
	 * Get the [participant_details2] column value.
	 * 
	 * @return     string
	 */
	public function getPARTICIPANT_DETAILS2()
	{

		return $this->participant_details2;
	}

	/**
	 * Get the [participant_cat3] column value.
	 * 
	 * @return     string
	 */
	public function getPARTICIPANT_CAT3()
	{

		return $this->participant_cat3;
	}

	/**
	 * Get the [num_of_participants3] column value.
	 * 
	 * @return     double
	 */
	public function getNUM_OF_PARTICIPANTS3()
	{

		return $this->num_of_participants3;
	}

	/**
	 * Get the [participant_details3] column value.
	 * 
	 * @return     string
	 */
	public function getPARTICIPANT_DETAILS3()
	{

		return $this->participant_details3;
	}

	/**
	 * Get the [participant_cat4] column value.
	 * 
	 * @return     string
	 */
	public function getPARTICIPANT_CAT4()
	{

		return $this->participant_cat4;
	}

	/**
	 * Get the [num_of_participants4] column value.
	 * 
	 * @return     double
	 */
	public function getNUM_OF_PARTICIPANTS4()
	{

		return $this->num_of_participants4;
	}

	/**
	 * Get the [participant_details4] column value.
	 * 
	 * @return     string
	 */
	public function getPARTICIPANT_DETAILS4()
	{

		return $this->participant_details4;
	}

	/**
	 * Get the [event_nar] column value.
	 * 
	 * @return     string
	 */
	public function getEVENT_NAR()
	{

		return $this->event_nar;
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
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::ID;
		}

	} // setID()

	/**
	 * Set the value of [qar_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQAR_ID($v)
	{

		if ($this->qar_id !== $v) {
			$this->qar_id = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::QAR_ID;
		}

		if ($this->aSiteReportsQAR !== null && $this->aSiteReportsQAR->getID() !== $v) {
			$this->aSiteReportsQAR = null;
		}

	} // setQAR_ID()

	/**
	 * Set the value of [event_type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setEVENT_TYPE($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->event_type !== $v) {
			$this->event_type = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::EVENT_TYPE;
		}

	} // setEVENT_TYPE()

	/**
	 * Set the value of [activity] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setACTIVITY($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->activity !== $v) {
			$this->activity = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::ACTIVITY;
		}

	} // setACTIVITY()

	/**
	 * Set the value of [activity_objectives] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setACTIVITY_OBJECTIVES($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->activity_objectives !== $v) {
			$this->activity_objectives = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::ACTIVITY_OBJECTIVES;
		}

	} // setACTIVITY_OBJECTIVES()

	/**
	 * Set the value of [objective_met] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setOBJECTIVE_MET($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->objective_met !== $v) {
			$this->objective_met = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::OBJECTIVE_MET;
		}

	} // setOBJECTIVE_MET()

	/**
	 * Set the value of [participant_cat1] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPARTICIPANT_CAT1($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->participant_cat1 !== $v) {
			$this->participant_cat1 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::PARTICIPANT_CAT1;
		}

	} // setPARTICIPANT_CAT1()

	/**
	 * Set the value of [num_of_participants1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNUM_OF_PARTICIPANTS1($v)
	{

		if ($this->num_of_participants1 !== $v) {
			$this->num_of_participants1 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS1;
		}

	} // setNUM_OF_PARTICIPANTS1()

	/**
	 * Set the value of [participant_details1] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPARTICIPANT_DETAILS1($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->participant_details1 !== $v) {
			$this->participant_details1 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS1;
		}

	} // setPARTICIPANT_DETAILS1()

	/**
	 * Set the value of [participant_cat2] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPARTICIPANT_CAT2($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->participant_cat2 !== $v) {
			$this->participant_cat2 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::PARTICIPANT_CAT2;
		}

	} // setPARTICIPANT_CAT2()

	/**
	 * Set the value of [num_of_participants2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNUM_OF_PARTICIPANTS2($v)
	{

		if ($this->num_of_participants2 !== $v) {
			$this->num_of_participants2 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS2;
		}

	} // setNUM_OF_PARTICIPANTS2()

	/**
	 * Set the value of [participant_details2] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPARTICIPANT_DETAILS2($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->participant_details2 !== $v) {
			$this->participant_details2 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS2;
		}

	} // setPARTICIPANT_DETAILS2()

	/**
	 * Set the value of [participant_cat3] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPARTICIPANT_CAT3($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->participant_cat3 !== $v) {
			$this->participant_cat3 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::PARTICIPANT_CAT3;
		}

	} // setPARTICIPANT_CAT3()

	/**
	 * Set the value of [num_of_participants3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNUM_OF_PARTICIPANTS3($v)
	{

		if ($this->num_of_participants3 !== $v) {
			$this->num_of_participants3 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS3;
		}

	} // setNUM_OF_PARTICIPANTS3()

	/**
	 * Set the value of [participant_details3] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPARTICIPANT_DETAILS3($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->participant_details3 !== $v) {
			$this->participant_details3 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS3;
		}

	} // setPARTICIPANT_DETAILS3()

	/**
	 * Set the value of [participant_cat4] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPARTICIPANT_CAT4($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->participant_cat4 !== $v) {
			$this->participant_cat4 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::PARTICIPANT_CAT4;
		}

	} // setPARTICIPANT_CAT4()

	/**
	 * Set the value of [num_of_participants4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNUM_OF_PARTICIPANTS4($v)
	{

		if ($this->num_of_participants4 !== $v) {
			$this->num_of_participants4 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS4;
		}

	} // setNUM_OF_PARTICIPANTS4()

	/**
	 * Set the value of [participant_details4] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPARTICIPANT_DETAILS4($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->participant_details4 !== $v) {
			$this->participant_details4 = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS4;
		}

	} // setPARTICIPANT_DETAILS4()

	/**
	 * Set the value of [event_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setEVENT_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->event_nar !== $v) {
			$this->event_nar = $v;
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::EVENT_NAR;
		}

	} // setEVENT_NAR()

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
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::CREATED_BY;
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
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::CREATED_ON;
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
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::UPDATED_BY;
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
			$this->modifiedColumns[] = SiteReportsQAREotEvtPeer::UPDATED_ON;
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

			$this->qar_id = $rs->getFloat($startcol + 1);

			$this->event_type = $rs->getString($startcol + 2);

			$this->activity = $rs->getString($startcol + 3);

			$this->activity_objectives = $rs->getString($startcol + 4);

			$this->objective_met = $rs->getString($startcol + 5);

			$this->participant_cat1 = $rs->getString($startcol + 6);

			$this->num_of_participants1 = $rs->getFloat($startcol + 7);

			$this->participant_details1 = $rs->getString($startcol + 8);

			$this->participant_cat2 = $rs->getString($startcol + 9);

			$this->num_of_participants2 = $rs->getFloat($startcol + 10);

			$this->participant_details2 = $rs->getString($startcol + 11);

			$this->participant_cat3 = $rs->getString($startcol + 12);

			$this->num_of_participants3 = $rs->getFloat($startcol + 13);

			$this->participant_details3 = $rs->getString($startcol + 14);

			$this->participant_cat4 = $rs->getString($startcol + 15);

			$this->num_of_participants4 = $rs->getFloat($startcol + 16);

			$this->participant_details4 = $rs->getString($startcol + 17);

			$this->event_nar = $rs->getString($startcol + 18);

			$this->created_by = $rs->getString($startcol + 19);

			$this->created_on = $rs->getDate($startcol + 20, null);

			$this->updated_by = $rs->getString($startcol + 21);

			$this->updated_on = $rs->getDate($startcol + 22, null);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 23; // 23 = SiteReportsQAREotEvtPeer::NUM_COLUMNS - SiteReportsQAREotEvtPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SiteReportsQAREotEvt object", $e);
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
			$con = Propel::getConnection(SiteReportsQAREotEvtPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SiteReportsQAREotEvtPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SiteReportsQAREotEvtPeer::DATABASE_NAME);
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

			if ($this->aSiteReportsQAR !== null) {
				if ($this->aSiteReportsQAR->isModified()) {
					$affectedRows += $this->aSiteReportsQAR->save($con);
				}
				$this->setSiteReportsQAR($this->aSiteReportsQAR);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SiteReportsQAREotEvtPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setID($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SiteReportsQAREotEvtPeer::doUpdate($this, $con);
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

			if ($this->aSiteReportsQAR !== null) {
				if (!$this->aSiteReportsQAR->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSiteReportsQAR->getValidationFailures());
				}
			}


			if (($retval = SiteReportsQAREotEvtPeer::doValidate($this, $columns)) !== true) {
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
		$pos = SiteReportsQAREotEvtPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getQAR_ID();
				break;
			case 2:
				return $this->getEVENT_TYPE();
				break;
			case 3:
				return $this->getACTIVITY();
				break;
			case 4:
				return $this->getACTIVITY_OBJECTIVES();
				break;
			case 5:
				return $this->getOBJECTIVE_MET();
				break;
			case 6:
				return $this->getPARTICIPANT_CAT1();
				break;
			case 7:
				return $this->getNUM_OF_PARTICIPANTS1();
				break;
			case 8:
				return $this->getPARTICIPANT_DETAILS1();
				break;
			case 9:
				return $this->getPARTICIPANT_CAT2();
				break;
			case 10:
				return $this->getNUM_OF_PARTICIPANTS2();
				break;
			case 11:
				return $this->getPARTICIPANT_DETAILS2();
				break;
			case 12:
				return $this->getPARTICIPANT_CAT3();
				break;
			case 13:
				return $this->getNUM_OF_PARTICIPANTS3();
				break;
			case 14:
				return $this->getPARTICIPANT_DETAILS3();
				break;
			case 15:
				return $this->getPARTICIPANT_CAT4();
				break;
			case 16:
				return $this->getNUM_OF_PARTICIPANTS4();
				break;
			case 17:
				return $this->getPARTICIPANT_DETAILS4();
				break;
			case 18:
				return $this->getEVENT_NAR();
				break;
			case 19:
				return $this->getCREATED_BY();
				break;
			case 20:
				return $this->getCREATED_ON();
				break;
			case 21:
				return $this->getUPDATED_BY();
				break;
			case 22:
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
		$keys = SiteReportsQAREotEvtPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getID(),
			$keys[1] => $this->getQAR_ID(),
			$keys[2] => $this->getEVENT_TYPE(),
			$keys[3] => $this->getACTIVITY(),
			$keys[4] => $this->getACTIVITY_OBJECTIVES(),
			$keys[5] => $this->getOBJECTIVE_MET(),
			$keys[6] => $this->getPARTICIPANT_CAT1(),
			$keys[7] => $this->getNUM_OF_PARTICIPANTS1(),
			$keys[8] => $this->getPARTICIPANT_DETAILS1(),
			$keys[9] => $this->getPARTICIPANT_CAT2(),
			$keys[10] => $this->getNUM_OF_PARTICIPANTS2(),
			$keys[11] => $this->getPARTICIPANT_DETAILS2(),
			$keys[12] => $this->getPARTICIPANT_CAT3(),
			$keys[13] => $this->getNUM_OF_PARTICIPANTS3(),
			$keys[14] => $this->getPARTICIPANT_DETAILS3(),
			$keys[15] => $this->getPARTICIPANT_CAT4(),
			$keys[16] => $this->getNUM_OF_PARTICIPANTS4(),
			$keys[17] => $this->getPARTICIPANT_DETAILS4(),
			$keys[18] => $this->getEVENT_NAR(),
			$keys[19] => $this->getCREATED_BY(),
			$keys[20] => $this->getCREATED_ON(),
			$keys[21] => $this->getUPDATED_BY(),
			$keys[22] => $this->getUPDATED_ON(),
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
		$pos = SiteReportsQAREotEvtPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setQAR_ID($value);
				break;
			case 2:
				$this->setEVENT_TYPE($value);
				break;
			case 3:
				$this->setACTIVITY($value);
				break;
			case 4:
				$this->setACTIVITY_OBJECTIVES($value);
				break;
			case 5:
				$this->setOBJECTIVE_MET($value);
				break;
			case 6:
				$this->setPARTICIPANT_CAT1($value);
				break;
			case 7:
				$this->setNUM_OF_PARTICIPANTS1($value);
				break;
			case 8:
				$this->setPARTICIPANT_DETAILS1($value);
				break;
			case 9:
				$this->setPARTICIPANT_CAT2($value);
				break;
			case 10:
				$this->setNUM_OF_PARTICIPANTS2($value);
				break;
			case 11:
				$this->setPARTICIPANT_DETAILS2($value);
				break;
			case 12:
				$this->setPARTICIPANT_CAT3($value);
				break;
			case 13:
				$this->setNUM_OF_PARTICIPANTS3($value);
				break;
			case 14:
				$this->setPARTICIPANT_DETAILS3($value);
				break;
			case 15:
				$this->setPARTICIPANT_CAT4($value);
				break;
			case 16:
				$this->setNUM_OF_PARTICIPANTS4($value);
				break;
			case 17:
				$this->setPARTICIPANT_DETAILS4($value);
				break;
			case 18:
				$this->setEVENT_NAR($value);
				break;
			case 19:
				$this->setCREATED_BY($value);
				break;
			case 20:
				$this->setCREATED_ON($value);
				break;
			case 21:
				$this->setUPDATED_BY($value);
				break;
			case 22:
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
		$keys = SiteReportsQAREotEvtPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setID($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setQAR_ID($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEVENT_TYPE($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setACTIVITY($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setACTIVITY_OBJECTIVES($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setOBJECTIVE_MET($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setPARTICIPANT_CAT1($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setNUM_OF_PARTICIPANTS1($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setPARTICIPANT_DETAILS1($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setPARTICIPANT_CAT2($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setNUM_OF_PARTICIPANTS2($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setPARTICIPANT_DETAILS2($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setPARTICIPANT_CAT3($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setNUM_OF_PARTICIPANTS3($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setPARTICIPANT_DETAILS3($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setPARTICIPANT_CAT4($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setNUM_OF_PARTICIPANTS4($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setPARTICIPANT_DETAILS4($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setEVENT_NAR($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setCREATED_BY($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setCREATED_ON($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setUPDATED_BY($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setUPDATED_ON($arr[$keys[22]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SiteReportsQAREotEvtPeer::DATABASE_NAME);

		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::ID)) $criteria->add(SiteReportsQAREotEvtPeer::ID, $this->id);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::QAR_ID)) $criteria->add(SiteReportsQAREotEvtPeer::QAR_ID, $this->qar_id);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::EVENT_TYPE)) $criteria->add(SiteReportsQAREotEvtPeer::EVENT_TYPE, $this->event_type);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::ACTIVITY)) $criteria->add(SiteReportsQAREotEvtPeer::ACTIVITY, $this->activity);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::ACTIVITY_OBJECTIVES)) $criteria->add(SiteReportsQAREotEvtPeer::ACTIVITY_OBJECTIVES, $this->activity_objectives);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::OBJECTIVE_MET)) $criteria->add(SiteReportsQAREotEvtPeer::OBJECTIVE_MET, $this->objective_met);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT1)) $criteria->add(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT1, $this->participant_cat1);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS1)) $criteria->add(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS1, $this->num_of_participants1);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS1)) $criteria->add(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS1, $this->participant_details1);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT2)) $criteria->add(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT2, $this->participant_cat2);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS2)) $criteria->add(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS2, $this->num_of_participants2);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS2)) $criteria->add(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS2, $this->participant_details2);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT3)) $criteria->add(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT3, $this->participant_cat3);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS3)) $criteria->add(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS3, $this->num_of_participants3);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS3)) $criteria->add(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS3, $this->participant_details3);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT4)) $criteria->add(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT4, $this->participant_cat4);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS4)) $criteria->add(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS4, $this->num_of_participants4);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS4)) $criteria->add(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS4, $this->participant_details4);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::EVENT_NAR)) $criteria->add(SiteReportsQAREotEvtPeer::EVENT_NAR, $this->event_nar);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::CREATED_BY)) $criteria->add(SiteReportsQAREotEvtPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::CREATED_ON)) $criteria->add(SiteReportsQAREotEvtPeer::CREATED_ON, $this->created_on);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::UPDATED_BY)) $criteria->add(SiteReportsQAREotEvtPeer::UPDATED_BY, $this->updated_by);
		if ($this->isColumnModified(SiteReportsQAREotEvtPeer::UPDATED_ON)) $criteria->add(SiteReportsQAREotEvtPeer::UPDATED_ON, $this->updated_on);

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
		$criteria = new Criteria(SiteReportsQAREotEvtPeer::DATABASE_NAME);

		$criteria->add(SiteReportsQAREotEvtPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SiteReportsQAREotEvt (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setQAR_ID($this->qar_id);

		$copyObj->setEVENT_TYPE($this->event_type);

		$copyObj->setACTIVITY($this->activity);

		$copyObj->setACTIVITY_OBJECTIVES($this->activity_objectives);

		$copyObj->setOBJECTIVE_MET($this->objective_met);

		$copyObj->setPARTICIPANT_CAT1($this->participant_cat1);

		$copyObj->setNUM_OF_PARTICIPANTS1($this->num_of_participants1);

		$copyObj->setPARTICIPANT_DETAILS1($this->participant_details1);

		$copyObj->setPARTICIPANT_CAT2($this->participant_cat2);

		$copyObj->setNUM_OF_PARTICIPANTS2($this->num_of_participants2);

		$copyObj->setPARTICIPANT_DETAILS2($this->participant_details2);

		$copyObj->setPARTICIPANT_CAT3($this->participant_cat3);

		$copyObj->setNUM_OF_PARTICIPANTS3($this->num_of_participants3);

		$copyObj->setPARTICIPANT_DETAILS3($this->participant_details3);

		$copyObj->setPARTICIPANT_CAT4($this->participant_cat4);

		$copyObj->setNUM_OF_PARTICIPANTS4($this->num_of_participants4);

		$copyObj->setPARTICIPANT_DETAILS4($this->participant_details4);

		$copyObj->setEVENT_NAR($this->event_nar);

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
	 * @return     SiteReportsQAREotEvt Clone of current object.
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
	 * @return     SiteReportsQAREotEvtPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SiteReportsQAREotEvtPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a SiteReportsQAR object.
	 *
	 * @param      SiteReportsQAR $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSiteReportsQAR($v)
	{


		if ($v === null) {
			$this->setQAR_ID(NULL);
		} else {
			$this->setQAR_ID($v->getID());
		}


		$this->aSiteReportsQAR = $v;
	}


	/**
	 * Get the associated SiteReportsQAR object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SiteReportsQAR The associated SiteReportsQAR object.
	 * @throws     PropelException
	 */
	public function getSiteReportsQAR($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSiteReportsQARPeer.php';

		if ($this->aSiteReportsQAR === null && ($this->qar_id > 0)) {

			$this->aSiteReportsQAR = SiteReportsQARPeer::retrieveByPK($this->qar_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SiteReportsQARPeer::retrieveByPK($this->qar_id, $con);
			   $obj->addSiteReportsQARs($this);
			 */
		}
		return $this->aSiteReportsQAR;
	}

} // BaseSiteReportsQAREotEvt
