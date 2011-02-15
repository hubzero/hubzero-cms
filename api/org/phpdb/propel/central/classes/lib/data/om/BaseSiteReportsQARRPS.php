<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SiteReportsQARRPSPeer.php';

/**
 * Base class that represents a row from the 'SITEREPORTS_QAR_RPS' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSiteReportsQARRPS extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SiteReportsQARRPSPeer
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
	 * The value for the project field.
	 * @var        string
	 */
	protected $project;


	/**
	 * The value for the project_warehouse_id field.
	 * @var        string
	 */
	protected $project_warehouse_id;


	/**
	 * The value for the neesr_shared_use_year field.
	 * @var        string
	 */
	protected $neesr_shared_use_year;


	/**
	 * The value for the official_award_number field.
	 * @var        string
	 */
	protected $official_award_number;


	/**
	 * The value for the project_title field.
	 * @var        string
	 */
	protected $project_title;


	/**
	 * The value for the project_number field.
	 * @var        double
	 */
	protected $project_number;


	/**
	 * The value for the pi_name field.
	 * @var        string
	 */
	protected $pi_name;


	/**
	 * The value for the institution field.
	 * @var        string
	 */
	protected $institution;


	/**
	 * The value for the ppp_fy_start_prg field.
	 * @var        double
	 */
	protected $ppp_fy_start_prg;


	/**
	 * The value for the ppp_fy_end_prg field.
	 * @var        double
	 */
	protected $ppp_fy_end_prg;


	/**
	 * The value for the app_q1 field.
	 * @var        double
	 */
	protected $app_q1;


	/**
	 * The value for the app_q2 field.
	 * @var        double
	 */
	protected $app_q2;


	/**
	 * The value for the app_q3 field.
	 * @var        double
	 */
	protected $app_q3;


	/**
	 * The value for the app_q4 field.
	 * @var        double
	 */
	protected $app_q4;


	/**
	 * The value for the q1_nar field.
	 * @var        string
	 */
	protected $q1_nar;


	/**
	 * The value for the q2_nar field.
	 * @var        string
	 */
	protected $q2_nar;


	/**
	 * The value for the q3_nar field.
	 * @var        string
	 */
	protected $q3_nar;


	/**
	 * The value for the q4_nar field.
	 * @var        string
	 */
	protected $q4_nar;


	/**
	 * The value for the project_weight field.
	 * @var        double
	 */
	protected $project_weight;


	/**
	 * The value for the weighted_progress field.
	 * @var        double
	 */
	protected $weighted_progress;


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
	 * Get the [project] column value.
	 * 
	 * @return     string
	 */
	public function getPROJECT()
	{

		return $this->project;
	}

	/**
	 * Get the [project_warehouse_id] column value.
	 * 
	 * @return     string
	 */
	public function getPROJECT_WAREHOUSE_ID()
	{

		return $this->project_warehouse_id;
	}

	/**
	 * Get the [neesr_shared_use_year] column value.
	 * 
	 * @return     string
	 */
	public function getNEESR_SHARED_USE_YEAR()
	{

		return $this->neesr_shared_use_year;
	}

	/**
	 * Get the [official_award_number] column value.
	 * 
	 * @return     string
	 */
	public function getOFFICIAL_AWARD_NUMBER()
	{

		return $this->official_award_number;
	}

	/**
	 * Get the [project_title] column value.
	 * 
	 * @return     string
	 */
	public function getPROJECT_TITLE()
	{

		return $this->project_title;
	}

	/**
	 * Get the [project_number] column value.
	 * 
	 * @return     double
	 */
	public function getPROJECT_NUMBER()
	{

		return $this->project_number;
	}

	/**
	 * Get the [pi_name] column value.
	 * 
	 * @return     string
	 */
	public function getPI_NAME()
	{

		return $this->pi_name;
	}

	/**
	 * Get the [institution] column value.
	 * 
	 * @return     string
	 */
	public function getINSTITUTION()
	{

		return $this->institution;
	}

	/**
	 * Get the [ppp_fy_start_prg] column value.
	 * 
	 * @return     double
	 */
	public function getPPP_FY_START_PRG()
	{

		return $this->ppp_fy_start_prg;
	}

	/**
	 * Get the [ppp_fy_end_prg] column value.
	 * 
	 * @return     double
	 */
	public function getPPP_FY_END_PRG()
	{

		return $this->ppp_fy_end_prg;
	}

	/**
	 * Get the [app_q1] column value.
	 * 
	 * @return     double
	 */
	public function getAPP_Q1()
	{

		return $this->app_q1;
	}

	/**
	 * Get the [app_q2] column value.
	 * 
	 * @return     double
	 */
	public function getAPP_Q2()
	{

		return $this->app_q2;
	}

	/**
	 * Get the [app_q3] column value.
	 * 
	 * @return     double
	 */
	public function getAPP_Q3()
	{

		return $this->app_q3;
	}

	/**
	 * Get the [app_q4] column value.
	 * 
	 * @return     double
	 */
	public function getAPP_Q4()
	{

		return $this->app_q4;
	}

	/**
	 * Get the [q1_nar] column value.
	 * 
	 * @return     string
	 */
	public function getQ1_NAR()
	{

		return $this->q1_nar;
	}

	/**
	 * Get the [q2_nar] column value.
	 * 
	 * @return     string
	 */
	public function getQ2_NAR()
	{

		return $this->q2_nar;
	}

	/**
	 * Get the [q3_nar] column value.
	 * 
	 * @return     string
	 */
	public function getQ3_NAR()
	{

		return $this->q3_nar;
	}

	/**
	 * Get the [q4_nar] column value.
	 * 
	 * @return     string
	 */
	public function getQ4_NAR()
	{

		return $this->q4_nar;
	}

	/**
	 * Get the [project_weight] column value.
	 * 
	 * @return     double
	 */
	public function getPROJECT_WEIGHT()
	{

		return $this->project_weight;
	}

	/**
	 * Get the [weighted_progress] column value.
	 * 
	 * @return     double
	 */
	public function getWEIGHTED_PROGRESS()
	{

		return $this->weighted_progress;
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
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::ID;
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
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::QAR_ID;
		}

		if ($this->aSiteReportsQAR !== null && $this->aSiteReportsQAR->getID() !== $v) {
			$this->aSiteReportsQAR = null;
		}

	} // setQAR_ID()

	/**
	 * Set the value of [project] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPROJECT($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->project !== $v) {
			$this->project = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::PROJECT;
		}

	} // setPROJECT()

	/**
	 * Set the value of [project_warehouse_id] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPROJECT_WAREHOUSE_ID($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->project_warehouse_id !== $v) {
			$this->project_warehouse_id = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::PROJECT_WAREHOUSE_ID;
		}

	} // setPROJECT_WAREHOUSE_ID()

	/**
	 * Set the value of [neesr_shared_use_year] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNEESR_SHARED_USE_YEAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->neesr_shared_use_year !== $v) {
			$this->neesr_shared_use_year = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::NEESR_SHARED_USE_YEAR;
		}

	} // setNEESR_SHARED_USE_YEAR()

	/**
	 * Set the value of [official_award_number] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setOFFICIAL_AWARD_NUMBER($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->official_award_number !== $v) {
			$this->official_award_number = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::OFFICIAL_AWARD_NUMBER;
		}

	} // setOFFICIAL_AWARD_NUMBER()

	/**
	 * Set the value of [project_title] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPROJECT_TITLE($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->project_title !== $v) {
			$this->project_title = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::PROJECT_TITLE;
		}

	} // setPROJECT_TITLE()

	/**
	 * Set the value of [project_number] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPROJECT_NUMBER($v)
	{

		if ($this->project_number !== $v) {
			$this->project_number = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::PROJECT_NUMBER;
		}

	} // setPROJECT_NUMBER()

	/**
	 * Set the value of [pi_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPI_NAME($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->pi_name !== $v) {
			$this->pi_name = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::PI_NAME;
		}

	} // setPI_NAME()

	/**
	 * Set the value of [institution] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setINSTITUTION($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->institution !== $v) {
			$this->institution = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::INSTITUTION;
		}

	} // setINSTITUTION()

	/**
	 * Set the value of [ppp_fy_start_prg] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPPP_FY_START_PRG($v)
	{

		if ($this->ppp_fy_start_prg !== $v) {
			$this->ppp_fy_start_prg = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::PPP_FY_START_PRG;
		}

	} // setPPP_FY_START_PRG()

	/**
	 * Set the value of [ppp_fy_end_prg] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPPP_FY_END_PRG($v)
	{

		if ($this->ppp_fy_end_prg !== $v) {
			$this->ppp_fy_end_prg = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::PPP_FY_END_PRG;
		}

	} // setPPP_FY_END_PRG()

	/**
	 * Set the value of [app_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAPP_Q1($v)
	{

		if ($this->app_q1 !== $v) {
			$this->app_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::APP_Q1;
		}

	} // setAPP_Q1()

	/**
	 * Set the value of [app_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAPP_Q2($v)
	{

		if ($this->app_q2 !== $v) {
			$this->app_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::APP_Q2;
		}

	} // setAPP_Q2()

	/**
	 * Set the value of [app_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAPP_Q3($v)
	{

		if ($this->app_q3 !== $v) {
			$this->app_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::APP_Q3;
		}

	} // setAPP_Q3()

	/**
	 * Set the value of [app_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAPP_Q4($v)
	{

		if ($this->app_q4 !== $v) {
			$this->app_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::APP_Q4;
		}

	} // setAPP_Q4()

	/**
	 * Set the value of [q1_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setQ1_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->q1_nar !== $v) {
			$this->q1_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::Q1_NAR;
		}

	} // setQ1_NAR()

	/**
	 * Set the value of [q2_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setQ2_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->q2_nar !== $v) {
			$this->q2_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::Q2_NAR;
		}

	} // setQ2_NAR()

	/**
	 * Set the value of [q3_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setQ3_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->q3_nar !== $v) {
			$this->q3_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::Q3_NAR;
		}

	} // setQ3_NAR()

	/**
	 * Set the value of [q4_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setQ4_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->q4_nar !== $v) {
			$this->q4_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::Q4_NAR;
		}

	} // setQ4_NAR()

	/**
	 * Set the value of [project_weight] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPROJECT_WEIGHT($v)
	{

		if ($this->project_weight !== $v) {
			$this->project_weight = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::PROJECT_WEIGHT;
		}

	} // setPROJECT_WEIGHT()

	/**
	 * Set the value of [weighted_progress] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setWEIGHTED_PROGRESS($v)
	{

		if ($this->weighted_progress !== $v) {
			$this->weighted_progress = $v;
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::WEIGHTED_PROGRESS;
		}

	} // setWEIGHTED_PROGRESS()

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
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::CREATED_BY;
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
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::CREATED_ON;
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
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::UPDATED_BY;
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
			$this->modifiedColumns[] = SiteReportsQARRPSPeer::UPDATED_ON;
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

			$this->project = $rs->getString($startcol + 2);

			$this->project_warehouse_id = $rs->getString($startcol + 3);

			$this->neesr_shared_use_year = $rs->getString($startcol + 4);

			$this->official_award_number = $rs->getString($startcol + 5);

			$this->project_title = $rs->getString($startcol + 6);

			$this->project_number = $rs->getFloat($startcol + 7);

			$this->pi_name = $rs->getString($startcol + 8);

			$this->institution = $rs->getString($startcol + 9);

			$this->ppp_fy_start_prg = $rs->getFloat($startcol + 10);

			$this->ppp_fy_end_prg = $rs->getFloat($startcol + 11);

			$this->app_q1 = $rs->getFloat($startcol + 12);

			$this->app_q2 = $rs->getFloat($startcol + 13);

			$this->app_q3 = $rs->getFloat($startcol + 14);

			$this->app_q4 = $rs->getFloat($startcol + 15);

			$this->q1_nar = $rs->getString($startcol + 16);

			$this->q2_nar = $rs->getString($startcol + 17);

			$this->q3_nar = $rs->getString($startcol + 18);

			$this->q4_nar = $rs->getString($startcol + 19);

			$this->project_weight = $rs->getFloat($startcol + 20);

			$this->weighted_progress = $rs->getFloat($startcol + 21);

			$this->created_by = $rs->getString($startcol + 22);

			$this->created_on = $rs->getDate($startcol + 23, null);

			$this->updated_by = $rs->getString($startcol + 24);

			$this->updated_on = $rs->getDate($startcol + 25, null);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 26; // 26 = SiteReportsQARRPSPeer::NUM_COLUMNS - SiteReportsQARRPSPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SiteReportsQARRPS object", $e);
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
			$con = Propel::getConnection(SiteReportsQARRPSPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SiteReportsQARRPSPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SiteReportsQARRPSPeer::DATABASE_NAME);
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
					$pk = SiteReportsQARRPSPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setID($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SiteReportsQARRPSPeer::doUpdate($this, $con);
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


			if (($retval = SiteReportsQARRPSPeer::doValidate($this, $columns)) !== true) {
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
		$pos = SiteReportsQARRPSPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPROJECT();
				break;
			case 3:
				return $this->getPROJECT_WAREHOUSE_ID();
				break;
			case 4:
				return $this->getNEESR_SHARED_USE_YEAR();
				break;
			case 5:
				return $this->getOFFICIAL_AWARD_NUMBER();
				break;
			case 6:
				return $this->getPROJECT_TITLE();
				break;
			case 7:
				return $this->getPROJECT_NUMBER();
				break;
			case 8:
				return $this->getPI_NAME();
				break;
			case 9:
				return $this->getINSTITUTION();
				break;
			case 10:
				return $this->getPPP_FY_START_PRG();
				break;
			case 11:
				return $this->getPPP_FY_END_PRG();
				break;
			case 12:
				return $this->getAPP_Q1();
				break;
			case 13:
				return $this->getAPP_Q2();
				break;
			case 14:
				return $this->getAPP_Q3();
				break;
			case 15:
				return $this->getAPP_Q4();
				break;
			case 16:
				return $this->getQ1_NAR();
				break;
			case 17:
				return $this->getQ2_NAR();
				break;
			case 18:
				return $this->getQ3_NAR();
				break;
			case 19:
				return $this->getQ4_NAR();
				break;
			case 20:
				return $this->getPROJECT_WEIGHT();
				break;
			case 21:
				return $this->getWEIGHTED_PROGRESS();
				break;
			case 22:
				return $this->getCREATED_BY();
				break;
			case 23:
				return $this->getCREATED_ON();
				break;
			case 24:
				return $this->getUPDATED_BY();
				break;
			case 25:
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
		$keys = SiteReportsQARRPSPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getID(),
			$keys[1] => $this->getQAR_ID(),
			$keys[2] => $this->getPROJECT(),
			$keys[3] => $this->getPROJECT_WAREHOUSE_ID(),
			$keys[4] => $this->getNEESR_SHARED_USE_YEAR(),
			$keys[5] => $this->getOFFICIAL_AWARD_NUMBER(),
			$keys[6] => $this->getPROJECT_TITLE(),
			$keys[7] => $this->getPROJECT_NUMBER(),
			$keys[8] => $this->getPI_NAME(),
			$keys[9] => $this->getINSTITUTION(),
			$keys[10] => $this->getPPP_FY_START_PRG(),
			$keys[11] => $this->getPPP_FY_END_PRG(),
			$keys[12] => $this->getAPP_Q1(),
			$keys[13] => $this->getAPP_Q2(),
			$keys[14] => $this->getAPP_Q3(),
			$keys[15] => $this->getAPP_Q4(),
			$keys[16] => $this->getQ1_NAR(),
			$keys[17] => $this->getQ2_NAR(),
			$keys[18] => $this->getQ3_NAR(),
			$keys[19] => $this->getQ4_NAR(),
			$keys[20] => $this->getPROJECT_WEIGHT(),
			$keys[21] => $this->getWEIGHTED_PROGRESS(),
			$keys[22] => $this->getCREATED_BY(),
			$keys[23] => $this->getCREATED_ON(),
			$keys[24] => $this->getUPDATED_BY(),
			$keys[25] => $this->getUPDATED_ON(),
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
		$pos = SiteReportsQARRPSPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPROJECT($value);
				break;
			case 3:
				$this->setPROJECT_WAREHOUSE_ID($value);
				break;
			case 4:
				$this->setNEESR_SHARED_USE_YEAR($value);
				break;
			case 5:
				$this->setOFFICIAL_AWARD_NUMBER($value);
				break;
			case 6:
				$this->setPROJECT_TITLE($value);
				break;
			case 7:
				$this->setPROJECT_NUMBER($value);
				break;
			case 8:
				$this->setPI_NAME($value);
				break;
			case 9:
				$this->setINSTITUTION($value);
				break;
			case 10:
				$this->setPPP_FY_START_PRG($value);
				break;
			case 11:
				$this->setPPP_FY_END_PRG($value);
				break;
			case 12:
				$this->setAPP_Q1($value);
				break;
			case 13:
				$this->setAPP_Q2($value);
				break;
			case 14:
				$this->setAPP_Q3($value);
				break;
			case 15:
				$this->setAPP_Q4($value);
				break;
			case 16:
				$this->setQ1_NAR($value);
				break;
			case 17:
				$this->setQ2_NAR($value);
				break;
			case 18:
				$this->setQ3_NAR($value);
				break;
			case 19:
				$this->setQ4_NAR($value);
				break;
			case 20:
				$this->setPROJECT_WEIGHT($value);
				break;
			case 21:
				$this->setWEIGHTED_PROGRESS($value);
				break;
			case 22:
				$this->setCREATED_BY($value);
				break;
			case 23:
				$this->setCREATED_ON($value);
				break;
			case 24:
				$this->setUPDATED_BY($value);
				break;
			case 25:
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
		$keys = SiteReportsQARRPSPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setID($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setQAR_ID($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPROJECT($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPROJECT_WAREHOUSE_ID($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setNEESR_SHARED_USE_YEAR($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setOFFICIAL_AWARD_NUMBER($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setPROJECT_TITLE($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setPROJECT_NUMBER($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setPI_NAME($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setINSTITUTION($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setPPP_FY_START_PRG($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setPPP_FY_END_PRG($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setAPP_Q1($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setAPP_Q2($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setAPP_Q3($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setAPP_Q4($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setQ1_NAR($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setQ2_NAR($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setQ3_NAR($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setQ4_NAR($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setPROJECT_WEIGHT($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setWEIGHTED_PROGRESS($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setCREATED_BY($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setCREATED_ON($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setUPDATED_BY($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setUPDATED_ON($arr[$keys[25]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SiteReportsQARRPSPeer::DATABASE_NAME);

		if ($this->isColumnModified(SiteReportsQARRPSPeer::ID)) $criteria->add(SiteReportsQARRPSPeer::ID, $this->id);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::QAR_ID)) $criteria->add(SiteReportsQARRPSPeer::QAR_ID, $this->qar_id);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::PROJECT)) $criteria->add(SiteReportsQARRPSPeer::PROJECT, $this->project);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::PROJECT_WAREHOUSE_ID)) $criteria->add(SiteReportsQARRPSPeer::PROJECT_WAREHOUSE_ID, $this->project_warehouse_id);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::NEESR_SHARED_USE_YEAR)) $criteria->add(SiteReportsQARRPSPeer::NEESR_SHARED_USE_YEAR, $this->neesr_shared_use_year);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::OFFICIAL_AWARD_NUMBER)) $criteria->add(SiteReportsQARRPSPeer::OFFICIAL_AWARD_NUMBER, $this->official_award_number);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::PROJECT_TITLE)) $criteria->add(SiteReportsQARRPSPeer::PROJECT_TITLE, $this->project_title);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::PROJECT_NUMBER)) $criteria->add(SiteReportsQARRPSPeer::PROJECT_NUMBER, $this->project_number);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::PI_NAME)) $criteria->add(SiteReportsQARRPSPeer::PI_NAME, $this->pi_name);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::INSTITUTION)) $criteria->add(SiteReportsQARRPSPeer::INSTITUTION, $this->institution);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::PPP_FY_START_PRG)) $criteria->add(SiteReportsQARRPSPeer::PPP_FY_START_PRG, $this->ppp_fy_start_prg);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::PPP_FY_END_PRG)) $criteria->add(SiteReportsQARRPSPeer::PPP_FY_END_PRG, $this->ppp_fy_end_prg);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::APP_Q1)) $criteria->add(SiteReportsQARRPSPeer::APP_Q1, $this->app_q1);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::APP_Q2)) $criteria->add(SiteReportsQARRPSPeer::APP_Q2, $this->app_q2);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::APP_Q3)) $criteria->add(SiteReportsQARRPSPeer::APP_Q3, $this->app_q3);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::APP_Q4)) $criteria->add(SiteReportsQARRPSPeer::APP_Q4, $this->app_q4);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::Q1_NAR)) $criteria->add(SiteReportsQARRPSPeer::Q1_NAR, $this->q1_nar);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::Q2_NAR)) $criteria->add(SiteReportsQARRPSPeer::Q2_NAR, $this->q2_nar);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::Q3_NAR)) $criteria->add(SiteReportsQARRPSPeer::Q3_NAR, $this->q3_nar);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::Q4_NAR)) $criteria->add(SiteReportsQARRPSPeer::Q4_NAR, $this->q4_nar);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::PROJECT_WEIGHT)) $criteria->add(SiteReportsQARRPSPeer::PROJECT_WEIGHT, $this->project_weight);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::WEIGHTED_PROGRESS)) $criteria->add(SiteReportsQARRPSPeer::WEIGHTED_PROGRESS, $this->weighted_progress);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::CREATED_BY)) $criteria->add(SiteReportsQARRPSPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::CREATED_ON)) $criteria->add(SiteReportsQARRPSPeer::CREATED_ON, $this->created_on);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::UPDATED_BY)) $criteria->add(SiteReportsQARRPSPeer::UPDATED_BY, $this->updated_by);
		if ($this->isColumnModified(SiteReportsQARRPSPeer::UPDATED_ON)) $criteria->add(SiteReportsQARRPSPeer::UPDATED_ON, $this->updated_on);

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
		$criteria = new Criteria(SiteReportsQARRPSPeer::DATABASE_NAME);

		$criteria->add(SiteReportsQARRPSPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SiteReportsQARRPS (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setQAR_ID($this->qar_id);

		$copyObj->setPROJECT($this->project);

		$copyObj->setPROJECT_WAREHOUSE_ID($this->project_warehouse_id);

		$copyObj->setNEESR_SHARED_USE_YEAR($this->neesr_shared_use_year);

		$copyObj->setOFFICIAL_AWARD_NUMBER($this->official_award_number);

		$copyObj->setPROJECT_TITLE($this->project_title);

		$copyObj->setPROJECT_NUMBER($this->project_number);

		$copyObj->setPI_NAME($this->pi_name);

		$copyObj->setINSTITUTION($this->institution);

		$copyObj->setPPP_FY_START_PRG($this->ppp_fy_start_prg);

		$copyObj->setPPP_FY_END_PRG($this->ppp_fy_end_prg);

		$copyObj->setAPP_Q1($this->app_q1);

		$copyObj->setAPP_Q2($this->app_q2);

		$copyObj->setAPP_Q3($this->app_q3);

		$copyObj->setAPP_Q4($this->app_q4);

		$copyObj->setQ1_NAR($this->q1_nar);

		$copyObj->setQ2_NAR($this->q2_nar);

		$copyObj->setQ3_NAR($this->q3_nar);

		$copyObj->setQ4_NAR($this->q4_nar);

		$copyObj->setPROJECT_WEIGHT($this->project_weight);

		$copyObj->setWEIGHTED_PROGRESS($this->weighted_progress);

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
	 * @return     SiteReportsQARRPS Clone of current object.
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
	 * @return     SiteReportsQARRPSPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SiteReportsQARRPSPeer();
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

} // BaseSiteReportsQARRPS
