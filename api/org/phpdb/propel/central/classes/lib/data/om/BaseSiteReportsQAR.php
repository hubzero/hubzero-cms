<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SiteReportsQARPeer.php';

/**
 * Base class that represents a row from the 'SITEREPORTS_QAR' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSiteReportsQAR extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SiteReportsQARPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the facility_id field.
	 * @var        double
	 */
	protected $facility_id;


	/**
	 * The value for the year field.
	 * @var        double
	 */
	protected $year;


	/**
	 * The value for the quarter field.
	 * @var        double
	 */
	protected $quarter;


	/**
	 * The value for the rbs_ss_last_rev_date_q1 field.
	 * @var        int
	 */
	protected $rbs_ss_last_rev_date_q1;


	/**
	 * The value for the rbs_ss_last_rev_date_q2 field.
	 * @var        int
	 */
	protected $rbs_ss_last_rev_date_q2;


	/**
	 * The value for the rbs_ss_last_rev_date_q3 field.
	 * @var        int
	 */
	protected $rbs_ss_last_rev_date_q3;


	/**
	 * The value for the rbs_ss_last_rev_date_q4 field.
	 * @var        int
	 */
	protected $rbs_ss_last_rev_date_q4;


	/**
	 * The value for the rbs_ss_osha_ri_q1 field.
	 * @var        double
	 */
	protected $rbs_ss_osha_ri_q1;


	/**
	 * The value for the rbs_ss_osha_ri_q2 field.
	 * @var        double
	 */
	protected $rbs_ss_osha_ri_q2;


	/**
	 * The value for the rbs_ss_osha_ri_q3 field.
	 * @var        double
	 */
	protected $rbs_ss_osha_ri_q3;


	/**
	 * The value for the rbs_ss_osha_ri_q4 field.
	 * @var        double
	 */
	protected $rbs_ss_osha_ri_q4;


	/**
	 * The value for the rbs_ss_injury_nar field.
	 * @var        string
	 */
	protected $rbs_ss_injury_nar;


	/**
	 * The value for the rbs_ss_psa_nar field.
	 * @var        string
	 */
	protected $rbs_ss_psa_nar;


	/**
	 * The value for the rbs_pmcr_ppm_prg_q1 field.
	 * @var        double
	 */
	protected $rbs_pmcr_ppm_prg_q1;


	/**
	 * The value for the rbs_pmcr_ppm_prg_q2 field.
	 * @var        double
	 */
	protected $rbs_pmcr_ppm_prg_q2;


	/**
	 * The value for the rbs_pmcr_ppm_prg_q3 field.
	 * @var        double
	 */
	protected $rbs_pmcr_ppm_prg_q3;


	/**
	 * The value for the rbs_pmcr_ppm_prg_q4 field.
	 * @var        double
	 */
	protected $rbs_pmcr_ppm_prg_q4;


	/**
	 * The value for the rbs_pmcr_ppm_nar field.
	 * @var        string
	 */
	protected $rbs_pmcr_ppm_nar;


	/**
	 * The value for the rbs_pmcr_pc_prg_q1 field.
	 * @var        double
	 */
	protected $rbs_pmcr_pc_prg_q1;


	/**
	 * The value for the rbs_pmcr_pc_prg_q2 field.
	 * @var        double
	 */
	protected $rbs_pmcr_pc_prg_q2;


	/**
	 * The value for the rbs_pmcr_pc_prg_q3 field.
	 * @var        double
	 */
	protected $rbs_pmcr_pc_prg_q3;


	/**
	 * The value for the rbs_pmcr_pc_prg_q4 field.
	 * @var        double
	 */
	protected $rbs_pmcr_pc_prg_q4;


	/**
	 * The value for the rbs_pmcr_pc_nar field.
	 * @var        string
	 */
	protected $rbs_pmcr_pc_nar;


	/**
	 * The value for the rbs_pmcr_pr_prg_q1 field.
	 * @var        double
	 */
	protected $rbs_pmcr_pr_prg_q1;


	/**
	 * The value for the rbs_pmcr_pr_prg_q2 field.
	 * @var        double
	 */
	protected $rbs_pmcr_pr_prg_q2;


	/**
	 * The value for the rbs_pmcr_pr_prg_q3 field.
	 * @var        double
	 */
	protected $rbs_pmcr_pr_prg_q3;


	/**
	 * The value for the rbs_pmcr_pr_prg_q4 field.
	 * @var        double
	 */
	protected $rbs_pmcr_pr_prg_q4;


	/**
	 * The value for the rbs_pmcr_pr_nar field.
	 * @var        string
	 */
	protected $rbs_pmcr_pr_nar;


	/**
	 * The value for the cb_fe_prg_q1 field.
	 * @var        double
	 */
	protected $cb_fe_prg_q1;


	/**
	 * The value for the cb_fe_prg_q2 field.
	 * @var        double
	 */
	protected $cb_fe_prg_q2;


	/**
	 * The value for the cb_fe_prg_q3 field.
	 * @var        double
	 */
	protected $cb_fe_prg_q3;


	/**
	 * The value for the cb_fe_prg_q4 field.
	 * @var        double
	 */
	protected $cb_fe_prg_q4;


	/**
	 * The value for the cb_fe_nar field.
	 * @var        string
	 */
	protected $cb_fe_nar;


	/**
	 * The value for the ni_itca_prg_q1 field.
	 * @var        double
	 */
	protected $ni_itca_prg_q1;


	/**
	 * The value for the ni_itca_prg_q2 field.
	 * @var        double
	 */
	protected $ni_itca_prg_q2;


	/**
	 * The value for the ni_itca_prg_q3 field.
	 * @var        double
	 */
	protected $ni_itca_prg_q3;


	/**
	 * The value for the ni_itca_prg_q4 field.
	 * @var        double
	 */
	protected $ni_itca_prg_q4;


	/**
	 * The value for the ni_itca_nar field.
	 * @var        string
	 */
	protected $ni_itca_nar;


	/**
	 * The value for the ni_neot_prg_q1 field.
	 * @var        double
	 */
	protected $ni_neot_prg_q1;


	/**
	 * The value for the ni_neot_prg_q2 field.
	 * @var        double
	 */
	protected $ni_neot_prg_q2;


	/**
	 * The value for the ni_neot_prg_q3 field.
	 * @var        double
	 */
	protected $ni_neot_prg_q3;


	/**
	 * The value for the ni_neot_prg_q4 field.
	 * @var        double
	 */
	protected $ni_neot_prg_q4;


	/**
	 * The value for the ni_neot_nar field.
	 * @var        string
	 */
	protected $ni_neot_nar;


	/**
	 * The value for the ni_nrs_prg_q1 field.
	 * @var        double
	 */
	protected $ni_nrs_prg_q1;


	/**
	 * The value for the ni_nrs_prg_q2 field.
	 * @var        double
	 */
	protected $ni_nrs_prg_q2;


	/**
	 * The value for the ni_nrs_prg_q3 field.
	 * @var        double
	 */
	protected $ni_nrs_prg_q3;


	/**
	 * The value for the ni_nrs_prg_q4 field.
	 * @var        double
	 */
	protected $ni_nrs_prg_q4;


	/**
	 * The value for the ni_nrs_nar field.
	 * @var        string
	 */
	protected $ni_nrs_nar;


	/**
	 * The value for the fh_nar field.
	 * @var        string
	 */
	protected $fh_nar;


	/**
	 * The value for the aem_nar field.
	 * @var        string
	 */
	protected $aem_nar;


	/**
	 * The value for the sa1_prg_q1 field.
	 * @var        double
	 */
	protected $sa1_prg_q1;


	/**
	 * The value for the sa1_prg_q2 field.
	 * @var        double
	 */
	protected $sa1_prg_q2;


	/**
	 * The value for the sa1_prg_q3 field.
	 * @var        double
	 */
	protected $sa1_prg_q3;


	/**
	 * The value for the sa1_prg_q4 field.
	 * @var        double
	 */
	protected $sa1_prg_q4;


	/**
	 * The value for the sa1_prg_nar field.
	 * @var        string
	 */
	protected $sa1_prg_nar;


	/**
	 * The value for the sa2_prg_q1 field.
	 * @var        double
	 */
	protected $sa2_prg_q1;


	/**
	 * The value for the sa2_prg_q2 field.
	 * @var        double
	 */
	protected $sa2_prg_q2;


	/**
	 * The value for the sa2_prg_q3 field.
	 * @var        double
	 */
	protected $sa2_prg_q3;


	/**
	 * The value for the sa2_prg_q4 field.
	 * @var        double
	 */
	protected $sa2_prg_q4;


	/**
	 * The value for the sa2_prg_nar field.
	 * @var        string
	 */
	protected $sa2_prg_nar;


	/**
	 * The value for the sa3_prg_q1 field.
	 * @var        double
	 */
	protected $sa3_prg_q1;


	/**
	 * The value for the sa3_prg_q2 field.
	 * @var        double
	 */
	protected $sa3_prg_q2;


	/**
	 * The value for the sa3_prg_q3 field.
	 * @var        double
	 */
	protected $sa3_prg_q3;


	/**
	 * The value for the sa3_prg_q4 field.
	 * @var        double
	 */
	protected $sa3_prg_q4;


	/**
	 * The value for the sa3_prg_nar field.
	 * @var        string
	 */
	protected $sa3_prg_nar;


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
	 * Collection to store aggregation of collSiteReportsQAREotEvts.
	 * @var        array
	 */
	protected $collSiteReportsQAREotEvts;

	/**
	 * The criteria used to select the current contents of collSiteReportsQAREotEvts.
	 * @var        Criteria
	 */
	protected $lastSiteReportsQAREotEvtCriteria = null;

	/**
	 * Collection to store aggregation of collSiteReportsQARRPSs.
	 * @var        array
	 */
	protected $collSiteReportsQARRPSs;

	/**
	 * The criteria used to select the current contents of collSiteReportsQARRPSs.
	 * @var        Criteria
	 */
	protected $lastSiteReportsQARRPSCriteria = null;

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
	 * Get the [facility_id] column value.
	 * 
	 * @return     double
	 */
	public function getFACILITY_ID()
	{

		return $this->facility_id;
	}

	/**
	 * Get the [year] column value.
	 * 
	 * @return     double
	 */
	public function getYEAR()
	{

		return $this->year;
	}

	/**
	 * Get the [quarter] column value.
	 * 
	 * @return     double
	 */
	public function getQUARTER()
	{

		return $this->quarter;
	}

	/**
	 * Get the [optionally formatted] [rbs_ss_last_rev_date_q1] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getRBS_SS_LAST_REV_DATE_Q1($format = '%Y-%m-%d')
	{

		if ($this->rbs_ss_last_rev_date_q1 === null || $this->rbs_ss_last_rev_date_q1 === '') {
			return null;
		} elseif (!is_int($this->rbs_ss_last_rev_date_q1)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->rbs_ss_last_rev_date_q1);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [rbs_ss_last_rev_date_q1] as date/time value: " . var_export($this->rbs_ss_last_rev_date_q1, true));
			}
		} else {
			$ts = $this->rbs_ss_last_rev_date_q1;
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
	 * Get the [optionally formatted] [rbs_ss_last_rev_date_q2] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getRBS_SS_LAST_REV_DATE_Q2($format = '%Y-%m-%d')
	{

		if ($this->rbs_ss_last_rev_date_q2 === null || $this->rbs_ss_last_rev_date_q2 === '') {
			return null;
		} elseif (!is_int($this->rbs_ss_last_rev_date_q2)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->rbs_ss_last_rev_date_q2);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [rbs_ss_last_rev_date_q2] as date/time value: " . var_export($this->rbs_ss_last_rev_date_q2, true));
			}
		} else {
			$ts = $this->rbs_ss_last_rev_date_q2;
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
	 * Get the [optionally formatted] [rbs_ss_last_rev_date_q3] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getRBS_SS_LAST_REV_DATE_Q3($format = '%Y-%m-%d')
	{

		if ($this->rbs_ss_last_rev_date_q3 === null || $this->rbs_ss_last_rev_date_q3 === '') {
			return null;
		} elseif (!is_int($this->rbs_ss_last_rev_date_q3)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->rbs_ss_last_rev_date_q3);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [rbs_ss_last_rev_date_q3] as date/time value: " . var_export($this->rbs_ss_last_rev_date_q3, true));
			}
		} else {
			$ts = $this->rbs_ss_last_rev_date_q3;
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
	 * Get the [optionally formatted] [rbs_ss_last_rev_date_q4] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getRBS_SS_LAST_REV_DATE_Q4($format = '%Y-%m-%d')
	{

		if ($this->rbs_ss_last_rev_date_q4 === null || $this->rbs_ss_last_rev_date_q4 === '') {
			return null;
		} elseif (!is_int($this->rbs_ss_last_rev_date_q4)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->rbs_ss_last_rev_date_q4);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [rbs_ss_last_rev_date_q4] as date/time value: " . var_export($this->rbs_ss_last_rev_date_q4, true));
			}
		} else {
			$ts = $this->rbs_ss_last_rev_date_q4;
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
	 * Get the [rbs_ss_osha_ri_q1] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_SS_RI_Q1()
	{

		return $this->rbs_ss_osha_ri_q1;
	}

	/**
	 * Get the [rbs_ss_osha_ri_q2] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_SS_RI_Q2()
	{

		return $this->rbs_ss_osha_ri_q2;
	}

	/**
	 * Get the [rbs_ss_osha_ri_q3] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_SS_RI_Q3()
	{

		return $this->rbs_ss_osha_ri_q3;
	}

	/**
	 * Get the [rbs_ss_osha_ri_q4] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_SS_RI_Q4()
	{

		return $this->rbs_ss_osha_ri_q4;
	}

	/**
	 * Get the [rbs_ss_injury_nar] column value.
	 * 
	 * @return     string
	 */
	public function getRBS_SS_INJURY_NAR()
	{

		return $this->rbs_ss_injury_nar;
	}

	/**
	 * Get the [rbs_ss_psa_nar] column value.
	 * 
	 * @return     string
	 */
	public function getRBS_SS_PSA_NAR()
	{

		return $this->rbs_ss_psa_nar;
	}

	/**
	 * Get the [rbs_pmcr_ppm_prg_q1] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PPM_PRG_Q1()
	{

		return $this->rbs_pmcr_ppm_prg_q1;
	}

	/**
	 * Get the [rbs_pmcr_ppm_prg_q2] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PPM_PRG_Q2()
	{

		return $this->rbs_pmcr_ppm_prg_q2;
	}

	/**
	 * Get the [rbs_pmcr_ppm_prg_q3] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PPM_PRG_Q3()
	{

		return $this->rbs_pmcr_ppm_prg_q3;
	}

	/**
	 * Get the [rbs_pmcr_ppm_prg_q4] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PPM_PRG_Q4()
	{

		return $this->rbs_pmcr_ppm_prg_q4;
	}

	/**
	 * Get the [rbs_pmcr_ppm_nar] column value.
	 * 
	 * @return     string
	 */
	public function getRBS_PMCR_PPM_NAR()
	{

		return $this->rbs_pmcr_ppm_nar;
	}

	/**
	 * Get the [rbs_pmcr_pc_prg_q1] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PC_PRG_Q1()
	{

		return $this->rbs_pmcr_pc_prg_q1;
	}

	/**
	 * Get the [rbs_pmcr_pc_prg_q2] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PC_PRG_Q2()
	{

		return $this->rbs_pmcr_pc_prg_q2;
	}

	/**
	 * Get the [rbs_pmcr_pc_prg_q3] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PC_PRG_Q3()
	{

		return $this->rbs_pmcr_pc_prg_q3;
	}

	/**
	 * Get the [rbs_pmcr_pc_prg_q4] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PC_PRG_Q4()
	{

		return $this->rbs_pmcr_pc_prg_q4;
	}

	/**
	 * Get the [rbs_pmcr_pc_nar] column value.
	 * 
	 * @return     string
	 */
	public function getRBS_PMCR_PC_NAR()
	{

		return $this->rbs_pmcr_pc_nar;
	}

	/**
	 * Get the [rbs_pmcr_pr_prg_q1] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PR_PRG_Q1()
	{

		return $this->rbs_pmcr_pr_prg_q1;
	}

	/**
	 * Get the [rbs_pmcr_pr_prg_q2] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PR_PRG_Q2()
	{

		return $this->rbs_pmcr_pr_prg_q2;
	}

	/**
	 * Get the [rbs_pmcr_pr_prg_q3] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PR_PRG_Q3()
	{

		return $this->rbs_pmcr_pr_prg_q3;
	}

	/**
	 * Get the [rbs_pmcr_pr_prg_q4] column value.
	 * 
	 * @return     double
	 */
	public function getRBS_PMCR_PR_PRG_Q4()
	{

		return $this->rbs_pmcr_pr_prg_q4;
	}

	/**
	 * Get the [rbs_pmcr_pr_nar] column value.
	 * 
	 * @return     string
	 */
	public function getRBS_PMCR_PR_NAR()
	{

		return $this->rbs_pmcr_pr_nar;
	}

	/**
	 * Get the [cb_fe_prg_q1] column value.
	 * 
	 * @return     double
	 */
	public function getCB_FE_PRG_Q1()
	{

		return $this->cb_fe_prg_q1;
	}

	/**
	 * Get the [cb_fe_prg_q2] column value.
	 * 
	 * @return     double
	 */
	public function getCB_FE_PRG_Q2()
	{

		return $this->cb_fe_prg_q2;
	}

	/**
	 * Get the [cb_fe_prg_q3] column value.
	 * 
	 * @return     double
	 */
	public function getCB_FE_PRG_Q3()
	{

		return $this->cb_fe_prg_q3;
	}

	/**
	 * Get the [cb_fe_prg_q4] column value.
	 * 
	 * @return     double
	 */
	public function getCB_FE_PRG_Q4()
	{

		return $this->cb_fe_prg_q4;
	}

	/**
	 * Get the [cb_fe_nar] column value.
	 * 
	 * @return     string
	 */
	public function getCB_FE_NAR()
	{

		return $this->cb_fe_nar;
	}

	/**
	 * Get the [ni_itca_prg_q1] column value.
	 * 
	 * @return     double
	 */
	public function getNI_ITCA_PRG_Q1()
	{

		return $this->ni_itca_prg_q1;
	}

	/**
	 * Get the [ni_itca_prg_q2] column value.
	 * 
	 * @return     double
	 */
	public function getNI_ITCA_PRG_Q2()
	{

		return $this->ni_itca_prg_q2;
	}

	/**
	 * Get the [ni_itca_prg_q3] column value.
	 * 
	 * @return     double
	 */
	public function getNI_ITCA_PRG_Q3()
	{

		return $this->ni_itca_prg_q3;
	}

	/**
	 * Get the [ni_itca_prg_q4] column value.
	 * 
	 * @return     double
	 */
	public function getNI_ITCA_PRG_Q4()
	{

		return $this->ni_itca_prg_q4;
	}

	/**
	 * Get the [ni_itca_nar] column value.
	 * 
	 * @return     string
	 */
	public function getNI_ITCA_NAR()
	{

		return $this->ni_itca_nar;
	}

	/**
	 * Get the [ni_neot_prg_q1] column value.
	 * 
	 * @return     double
	 */
	public function getNI_NEOT_PRG_Q1()
	{

		return $this->ni_neot_prg_q1;
	}

	/**
	 * Get the [ni_neot_prg_q2] column value.
	 * 
	 * @return     double
	 */
	public function getNI_NEOT_PRG_Q2()
	{

		return $this->ni_neot_prg_q2;
	}

	/**
	 * Get the [ni_neot_prg_q3] column value.
	 * 
	 * @return     double
	 */
	public function getNI_NEOT_PRG_Q3()
	{

		return $this->ni_neot_prg_q3;
	}

	/**
	 * Get the [ni_neot_prg_q4] column value.
	 * 
	 * @return     double
	 */
	public function getNI_NEOT_PRG_Q4()
	{

		return $this->ni_neot_prg_q4;
	}

	/**
	 * Get the [ni_neot_nar] column value.
	 * 
	 * @return     string
	 */
	public function getNI_NEOT_NAR()
	{

		return $this->ni_neot_nar;
	}

	/**
	 * Get the [ni_nrs_prg_q1] column value.
	 * 
	 * @return     double
	 */
	public function getNI_NRS_PRG_Q1()
	{

		return $this->ni_nrs_prg_q1;
	}

	/**
	 * Get the [ni_nrs_prg_q2] column value.
	 * 
	 * @return     double
	 */
	public function getNI_NRS_PRG_Q2()
	{

		return $this->ni_nrs_prg_q2;
	}

	/**
	 * Get the [ni_nrs_prg_q3] column value.
	 * 
	 * @return     double
	 */
	public function getNI_NRS_PRG_Q3()
	{

		return $this->ni_nrs_prg_q3;
	}

	/**
	 * Get the [ni_nrs_prg_q4] column value.
	 * 
	 * @return     double
	 */
	public function getNI_NRS_PRG_Q4()
	{

		return $this->ni_nrs_prg_q4;
	}

	/**
	 * Get the [ni_nrs_nar] column value.
	 * 
	 * @return     string
	 */
	public function getNI_NRS_NAR()
	{

		return $this->ni_nrs_nar;
	}

	/**
	 * Get the [fh_nar] column value.
	 * 
	 * @return     string
	 */
	public function getFH_NAR()
	{

		return $this->fh_nar;
	}

	/**
	 * Get the [aem_nar] column value.
	 * 
	 * @return     string
	 */
	public function getAEM_NAR()
	{

		return $this->aem_nar;
	}

	/**
	 * Get the [sa1_prg_q1] column value.
	 * 
	 * @return     double
	 */
	public function getSA1_PRG_Q1()
	{

		return $this->sa1_prg_q1;
	}

	/**
	 * Get the [sa1_prg_q2] column value.
	 * 
	 * @return     double
	 */
	public function getSA1_PRG_Q2()
	{

		return $this->sa1_prg_q2;
	}

	/**
	 * Get the [sa1_prg_q3] column value.
	 * 
	 * @return     double
	 */
	public function getSA1_PRG_Q3()
	{

		return $this->sa1_prg_q3;
	}

	/**
	 * Get the [sa1_prg_q4] column value.
	 * 
	 * @return     double
	 */
	public function getSA1_PRG_Q4()
	{

		return $this->sa1_prg_q4;
	}

	/**
	 * Get the [sa1_prg_nar] column value.
	 * 
	 * @return     string
	 */
	public function getSA1_PRG_NAR()
	{

		return $this->sa1_prg_nar;
	}

	/**
	 * Get the [sa2_prg_q1] column value.
	 * 
	 * @return     double
	 */
	public function getSA2_PRG_Q1()
	{

		return $this->sa2_prg_q1;
	}

	/**
	 * Get the [sa2_prg_q2] column value.
	 * 
	 * @return     double
	 */
	public function getSA2_PRG_Q2()
	{

		return $this->sa2_prg_q2;
	}

	/**
	 * Get the [sa2_prg_q3] column value.
	 * 
	 * @return     double
	 */
	public function getSA2_PRG_Q3()
	{

		return $this->sa2_prg_q3;
	}

	/**
	 * Get the [sa2_prg_q4] column value.
	 * 
	 * @return     double
	 */
	public function getSA2_PRG_Q4()
	{

		return $this->sa2_prg_q4;
	}

	/**
	 * Get the [sa2_prg_nar] column value.
	 * 
	 * @return     string
	 */
	public function getSA2_PRG_NAR()
	{

		return $this->sa2_prg_nar;
	}

	/**
	 * Get the [sa3_prg_q1] column value.
	 * 
	 * @return     double
	 */
	public function getSA3_PRG_Q1()
	{

		return $this->sa3_prg_q1;
	}

	/**
	 * Get the [sa3_prg_q2] column value.
	 * 
	 * @return     double
	 */
	public function getSA3_PRG_Q2()
	{

		return $this->sa3_prg_q2;
	}

	/**
	 * Get the [sa3_prg_q3] column value.
	 * 
	 * @return     double
	 */
	public function getSA3_PRG_Q3()
	{

		return $this->sa3_prg_q3;
	}

	/**
	 * Get the [sa3_prg_q4] column value.
	 * 
	 * @return     double
	 */
	public function getSA3_PRG_Q4()
	{

		return $this->sa3_prg_q4;
	}

	/**
	 * Get the [sa3_prg_nar] column value.
	 * 
	 * @return     string
	 */
	public function getSA3_PRG_NAR()
	{

		return $this->sa3_prg_nar;
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
			$this->modifiedColumns[] = SiteReportsQARPeer::ID;
		}

	} // setID()

	/**
	 * Set the value of [facility_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFACILITY_ID($v)
	{

		if ($this->facility_id !== $v) {
			$this->facility_id = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::FACILITY_ID;
		}

	} // setFACILITY_ID()

	/**
	 * Set the value of [year] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setYEAR($v)
	{

		if ($this->year !== $v) {
			$this->year = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::YEAR;
		}

	} // setYEAR()

	/**
	 * Set the value of [quarter] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQUARTER($v)
	{

		if ($this->quarter !== $v) {
			$this->quarter = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::QUARTER;
		}

	} // setQUARTER()

	/**
	 * Set the value of [rbs_ss_last_rev_date_q1] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setRBS_SS_LAST_REV_DATE_Q1($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [rbs_ss_last_rev_date_q1] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->rbs_ss_last_rev_date_q1 !== $ts) {
			$this->rbs_ss_last_rev_date_q1 = $ts;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q1;
		}

	} // setRBS_SS_LAST_REV_DATE_Q1()

	/**
	 * Set the value of [rbs_ss_last_rev_date_q2] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setRBS_SS_LAST_REV_DATE_Q2($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [rbs_ss_last_rev_date_q2] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->rbs_ss_last_rev_date_q2 !== $ts) {
			$this->rbs_ss_last_rev_date_q2 = $ts;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q2;
		}

	} // setRBS_SS_LAST_REV_DATE_Q2()

	/**
	 * Set the value of [rbs_ss_last_rev_date_q3] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setRBS_SS_LAST_REV_DATE_Q3($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [rbs_ss_last_rev_date_q3] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->rbs_ss_last_rev_date_q3 !== $ts) {
			$this->rbs_ss_last_rev_date_q3 = $ts;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q3;
		}

	} // setRBS_SS_LAST_REV_DATE_Q3()

	/**
	 * Set the value of [rbs_ss_last_rev_date_q4] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setRBS_SS_LAST_REV_DATE_Q4($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [rbs_ss_last_rev_date_q4] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->rbs_ss_last_rev_date_q4 !== $ts) {
			$this->rbs_ss_last_rev_date_q4 = $ts;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q4;
		}

	} // setRBS_SS_LAST_REV_DATE_Q4()

	/**
	 * Set the value of [rbs_ss_osha_ri_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_SS_RI_Q1($v)
	{

		if ($this->rbs_ss_osha_ri_q1 !== $v) {
			$this->rbs_ss_osha_ri_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_SS_OSHA_RI_Q1;
		}

	} // setRBS_SS_RI_Q1()

	/**
	 * Set the value of [rbs_ss_osha_ri_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_SS_RI_Q2($v)
	{

		if ($this->rbs_ss_osha_ri_q2 !== $v) {
			$this->rbs_ss_osha_ri_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_SS_OSHA_RI_Q2;
		}

	} // setRBS_SS_RI_Q2()

	/**
	 * Set the value of [rbs_ss_osha_ri_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_SS_RI_Q3($v)
	{

		if ($this->rbs_ss_osha_ri_q3 !== $v) {
			$this->rbs_ss_osha_ri_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_SS_OSHA_RI_Q3;
		}

	} // setRBS_SS_RI_Q3()

	/**
	 * Set the value of [rbs_ss_osha_ri_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_SS_RI_Q4($v)
	{

		if ($this->rbs_ss_osha_ri_q4 !== $v) {
			$this->rbs_ss_osha_ri_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_SS_OSHA_RI_Q4;
		}

	} // setRBS_SS_RI_Q4()

	/**
	 * Set the value of [rbs_ss_injury_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setRBS_SS_INJURY_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->rbs_ss_injury_nar !== $v) {
			$this->rbs_ss_injury_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_SS_INJURY_NAR;
		}

	} // setRBS_SS_INJURY_NAR()

	/**
	 * Set the value of [rbs_ss_psa_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setRBS_SS_PSA_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->rbs_ss_psa_nar !== $v) {
			$this->rbs_ss_psa_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_SS_PSA_NAR;
		}

	} // setRBS_SS_PSA_NAR()

	/**
	 * Set the value of [rbs_pmcr_ppm_prg_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PPM_PRG_Q1($v)
	{

		if ($this->rbs_pmcr_ppm_prg_q1 !== $v) {
			$this->rbs_pmcr_ppm_prg_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q1;
		}

	} // setRBS_PMCR_PPM_PRG_Q1()

	/**
	 * Set the value of [rbs_pmcr_ppm_prg_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PPM_PRG_Q2($v)
	{

		if ($this->rbs_pmcr_ppm_prg_q2 !== $v) {
			$this->rbs_pmcr_ppm_prg_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q2;
		}

	} // setRBS_PMCR_PPM_PRG_Q2()

	/**
	 * Set the value of [rbs_pmcr_ppm_prg_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PPM_PRG_Q3($v)
	{

		if ($this->rbs_pmcr_ppm_prg_q3 !== $v) {
			$this->rbs_pmcr_ppm_prg_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q3;
		}

	} // setRBS_PMCR_PPM_PRG_Q3()

	/**
	 * Set the value of [rbs_pmcr_ppm_prg_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PPM_PRG_Q4($v)
	{

		if ($this->rbs_pmcr_ppm_prg_q4 !== $v) {
			$this->rbs_pmcr_ppm_prg_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q4;
		}

	} // setRBS_PMCR_PPM_PRG_Q4()

	/**
	 * Set the value of [rbs_pmcr_ppm_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PPM_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->rbs_pmcr_ppm_nar !== $v) {
			$this->rbs_pmcr_ppm_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PPM_NAR;
		}

	} // setRBS_PMCR_PPM_NAR()

	/**
	 * Set the value of [rbs_pmcr_pc_prg_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PC_PRG_Q1($v)
	{

		if ($this->rbs_pmcr_pc_prg_q1 !== $v) {
			$this->rbs_pmcr_pc_prg_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q1;
		}

	} // setRBS_PMCR_PC_PRG_Q1()

	/**
	 * Set the value of [rbs_pmcr_pc_prg_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PC_PRG_Q2($v)
	{

		if ($this->rbs_pmcr_pc_prg_q2 !== $v) {
			$this->rbs_pmcr_pc_prg_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q2;
		}

	} // setRBS_PMCR_PC_PRG_Q2()

	/**
	 * Set the value of [rbs_pmcr_pc_prg_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PC_PRG_Q3($v)
	{

		if ($this->rbs_pmcr_pc_prg_q3 !== $v) {
			$this->rbs_pmcr_pc_prg_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q3;
		}

	} // setRBS_PMCR_PC_PRG_Q3()

	/**
	 * Set the value of [rbs_pmcr_pc_prg_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PC_PRG_Q4($v)
	{

		if ($this->rbs_pmcr_pc_prg_q4 !== $v) {
			$this->rbs_pmcr_pc_prg_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q4;
		}

	} // setRBS_PMCR_PC_PRG_Q4()

	/**
	 * Set the value of [rbs_pmcr_pc_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PC_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->rbs_pmcr_pc_nar !== $v) {
			$this->rbs_pmcr_pc_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PC_NAR;
		}

	} // setRBS_PMCR_PC_NAR()

	/**
	 * Set the value of [rbs_pmcr_pr_prg_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PR_PRG_Q1($v)
	{

		if ($this->rbs_pmcr_pr_prg_q1 !== $v) {
			$this->rbs_pmcr_pr_prg_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q1;
		}

	} // setRBS_PMCR_PR_PRG_Q1()

	/**
	 * Set the value of [rbs_pmcr_pr_prg_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PR_PRG_Q2($v)
	{

		if ($this->rbs_pmcr_pr_prg_q2 !== $v) {
			$this->rbs_pmcr_pr_prg_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q2;
		}

	} // setRBS_PMCR_PR_PRG_Q2()

	/**
	 * Set the value of [rbs_pmcr_pr_prg_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PR_PRG_Q3($v)
	{

		if ($this->rbs_pmcr_pr_prg_q3 !== $v) {
			$this->rbs_pmcr_pr_prg_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q3;
		}

	} // setRBS_PMCR_PR_PRG_Q3()

	/**
	 * Set the value of [rbs_pmcr_pr_prg_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PR_PRG_Q4($v)
	{

		if ($this->rbs_pmcr_pr_prg_q4 !== $v) {
			$this->rbs_pmcr_pr_prg_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q4;
		}

	} // setRBS_PMCR_PR_PRG_Q4()

	/**
	 * Set the value of [rbs_pmcr_pr_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setRBS_PMCR_PR_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->rbs_pmcr_pr_nar !== $v) {
			$this->rbs_pmcr_pr_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::RBS_PMCR_PR_NAR;
		}

	} // setRBS_PMCR_PR_NAR()

	/**
	 * Set the value of [cb_fe_prg_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCB_FE_PRG_Q1($v)
	{

		if ($this->cb_fe_prg_q1 !== $v) {
			$this->cb_fe_prg_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::CB_FE_PRG_Q1;
		}

	} // setCB_FE_PRG_Q1()

	/**
	 * Set the value of [cb_fe_prg_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCB_FE_PRG_Q2($v)
	{

		if ($this->cb_fe_prg_q2 !== $v) {
			$this->cb_fe_prg_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::CB_FE_PRG_Q2;
		}

	} // setCB_FE_PRG_Q2()

	/**
	 * Set the value of [cb_fe_prg_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCB_FE_PRG_Q3($v)
	{

		if ($this->cb_fe_prg_q3 !== $v) {
			$this->cb_fe_prg_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::CB_FE_PRG_Q3;
		}

	} // setCB_FE_PRG_Q3()

	/**
	 * Set the value of [cb_fe_prg_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCB_FE_PRG_Q4($v)
	{

		if ($this->cb_fe_prg_q4 !== $v) {
			$this->cb_fe_prg_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::CB_FE_PRG_Q4;
		}

	} // setCB_FE_PRG_Q4()

	/**
	 * Set the value of [cb_fe_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCB_FE_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->cb_fe_nar !== $v) {
			$this->cb_fe_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::CB_FE_NAR;
		}

	} // setCB_FE_NAR()

	/**
	 * Set the value of [ni_itca_prg_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_ITCA_PRG_Q1($v)
	{

		if ($this->ni_itca_prg_q1 !== $v) {
			$this->ni_itca_prg_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_ITCA_PRG_Q1;
		}

	} // setNI_ITCA_PRG_Q1()

	/**
	 * Set the value of [ni_itca_prg_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_ITCA_PRG_Q2($v)
	{

		if ($this->ni_itca_prg_q2 !== $v) {
			$this->ni_itca_prg_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_ITCA_PRG_Q2;
		}

	} // setNI_ITCA_PRG_Q2()

	/**
	 * Set the value of [ni_itca_prg_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_ITCA_PRG_Q3($v)
	{

		if ($this->ni_itca_prg_q3 !== $v) {
			$this->ni_itca_prg_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_ITCA_PRG_Q3;
		}

	} // setNI_ITCA_PRG_Q3()

	/**
	 * Set the value of [ni_itca_prg_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_ITCA_PRG_Q4($v)
	{

		if ($this->ni_itca_prg_q4 !== $v) {
			$this->ni_itca_prg_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_ITCA_PRG_Q4;
		}

	} // setNI_ITCA_PRG_Q4()

	/**
	 * Set the value of [ni_itca_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNI_ITCA_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->ni_itca_nar !== $v) {
			$this->ni_itca_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_ITCA_NAR;
		}

	} // setNI_ITCA_NAR()

	/**
	 * Set the value of [ni_neot_prg_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_NEOT_PRG_Q1($v)
	{

		if ($this->ni_neot_prg_q1 !== $v) {
			$this->ni_neot_prg_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_NEOT_PRG_Q1;
		}

	} // setNI_NEOT_PRG_Q1()

	/**
	 * Set the value of [ni_neot_prg_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_NEOT_PRG_Q2($v)
	{

		if ($this->ni_neot_prg_q2 !== $v) {
			$this->ni_neot_prg_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_NEOT_PRG_Q2;
		}

	} // setNI_NEOT_PRG_Q2()

	/**
	 * Set the value of [ni_neot_prg_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_NEOT_PRG_Q3($v)
	{

		if ($this->ni_neot_prg_q3 !== $v) {
			$this->ni_neot_prg_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_NEOT_PRG_Q3;
		}

	} // setNI_NEOT_PRG_Q3()

	/**
	 * Set the value of [ni_neot_prg_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_NEOT_PRG_Q4($v)
	{

		if ($this->ni_neot_prg_q4 !== $v) {
			$this->ni_neot_prg_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_NEOT_PRG_Q4;
		}

	} // setNI_NEOT_PRG_Q4()

	/**
	 * Set the value of [ni_neot_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNI_NEOT_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->ni_neot_nar !== $v) {
			$this->ni_neot_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_NEOT_NAR;
		}

	} // setNI_NEOT_NAR()

	/**
	 * Set the value of [ni_nrs_prg_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_NRS_PRG_Q1($v)
	{

		if ($this->ni_nrs_prg_q1 !== $v) {
			$this->ni_nrs_prg_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_NRS_PRG_Q1;
		}

	} // setNI_NRS_PRG_Q1()

	/**
	 * Set the value of [ni_nrs_prg_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_NRS_PRG_Q2($v)
	{

		if ($this->ni_nrs_prg_q2 !== $v) {
			$this->ni_nrs_prg_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_NRS_PRG_Q2;
		}

	} // setNI_NRS_PRG_Q2()

	/**
	 * Set the value of [ni_nrs_prg_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_NRS_PRG_Q3($v)
	{

		if ($this->ni_nrs_prg_q3 !== $v) {
			$this->ni_nrs_prg_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_NRS_PRG_Q3;
		}

	} // setNI_NRS_PRG_Q3()

	/**
	 * Set the value of [ni_nrs_prg_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setNI_NRS_PRG_Q4($v)
	{

		if ($this->ni_nrs_prg_q4 !== $v) {
			$this->ni_nrs_prg_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_NRS_PRG_Q4;
		}

	} // setNI_NRS_PRG_Q4()

	/**
	 * Set the value of [ni_nrs_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNI_NRS_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->ni_nrs_nar !== $v) {
			$this->ni_nrs_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::NI_NRS_NAR;
		}

	} // setNI_NRS_NAR()

	/**
	 * Set the value of [fh_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFH_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->fh_nar !== $v) {
			$this->fh_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::FH_NAR;
		}

	} // setFH_NAR()

	/**
	 * Set the value of [aem_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setAEM_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->aem_nar !== $v) {
			$this->aem_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::AEM_NAR;
		}

	} // setAEM_NAR()

	/**
	 * Set the value of [sa1_prg_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA1_PRG_Q1($v)
	{

		if ($this->sa1_prg_q1 !== $v) {
			$this->sa1_prg_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA1_PRG_Q1;
		}

	} // setSA1_PRG_Q1()

	/**
	 * Set the value of [sa1_prg_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA1_PRG_Q2($v)
	{

		if ($this->sa1_prg_q2 !== $v) {
			$this->sa1_prg_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA1_PRG_Q2;
		}

	} // setSA1_PRG_Q2()

	/**
	 * Set the value of [sa1_prg_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA1_PRG_Q3($v)
	{

		if ($this->sa1_prg_q3 !== $v) {
			$this->sa1_prg_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA1_PRG_Q3;
		}

	} // setSA1_PRG_Q3()

	/**
	 * Set the value of [sa1_prg_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA1_PRG_Q4($v)
	{

		if ($this->sa1_prg_q4 !== $v) {
			$this->sa1_prg_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA1_PRG_Q4;
		}

	} // setSA1_PRG_Q4()

	/**
	 * Set the value of [sa1_prg_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSA1_PRG_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sa1_prg_nar !== $v) {
			$this->sa1_prg_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA1_PRG_NAR;
		}

	} // setSA1_PRG_NAR()

	/**
	 * Set the value of [sa2_prg_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA2_PRG_Q1($v)
	{

		if ($this->sa2_prg_q1 !== $v) {
			$this->sa2_prg_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA2_PRG_Q1;
		}

	} // setSA2_PRG_Q1()

	/**
	 * Set the value of [sa2_prg_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA2_PRG_Q2($v)
	{

		if ($this->sa2_prg_q2 !== $v) {
			$this->sa2_prg_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA2_PRG_Q2;
		}

	} // setSA2_PRG_Q2()

	/**
	 * Set the value of [sa2_prg_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA2_PRG_Q3($v)
	{

		if ($this->sa2_prg_q3 !== $v) {
			$this->sa2_prg_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA2_PRG_Q3;
		}

	} // setSA2_PRG_Q3()

	/**
	 * Set the value of [sa2_prg_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA2_PRG_Q4($v)
	{

		if ($this->sa2_prg_q4 !== $v) {
			$this->sa2_prg_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA2_PRG_Q4;
		}

	} // setSA2_PRG_Q4()

	/**
	 * Set the value of [sa2_prg_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSA2_PRG_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sa2_prg_nar !== $v) {
			$this->sa2_prg_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA2_PRG_NAR;
		}

	} // setSA2_PRG_NAR()

	/**
	 * Set the value of [sa3_prg_q1] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA3_PRG_Q1($v)
	{

		if ($this->sa3_prg_q1 !== $v) {
			$this->sa3_prg_q1 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA3_PRG_Q1;
		}

	} // setSA3_PRG_Q1()

	/**
	 * Set the value of [sa3_prg_q2] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA3_PRG_Q2($v)
	{

		if ($this->sa3_prg_q2 !== $v) {
			$this->sa3_prg_q2 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA3_PRG_Q2;
		}

	} // setSA3_PRG_Q2()

	/**
	 * Set the value of [sa3_prg_q3] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA3_PRG_Q3($v)
	{

		if ($this->sa3_prg_q3 !== $v) {
			$this->sa3_prg_q3 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA3_PRG_Q3;
		}

	} // setSA3_PRG_Q3()

	/**
	 * Set the value of [sa3_prg_q4] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSA3_PRG_Q4($v)
	{

		if ($this->sa3_prg_q4 !== $v) {
			$this->sa3_prg_q4 = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA3_PRG_Q4;
		}

	} // setSA3_PRG_Q4()

	/**
	 * Set the value of [sa3_prg_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSA3_PRG_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sa3_prg_nar !== $v) {
			$this->sa3_prg_nar = $v;
			$this->modifiedColumns[] = SiteReportsQARPeer::SA3_PRG_NAR;
		}

	} // setSA3_PRG_NAR()

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
			$this->modifiedColumns[] = SiteReportsQARPeer::CREATED_BY;
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
			$this->modifiedColumns[] = SiteReportsQARPeer::CREATED_ON;
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
			$this->modifiedColumns[] = SiteReportsQARPeer::UPDATED_BY;
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
			$this->modifiedColumns[] = SiteReportsQARPeer::UPDATED_ON;
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

			$this->facility_id = $rs->getFloat($startcol + 1);

			$this->year = $rs->getFloat($startcol + 2);

			$this->quarter = $rs->getFloat($startcol + 3);

			$this->rbs_ss_last_rev_date_q1 = $rs->getDate($startcol + 4, null);

			$this->rbs_ss_last_rev_date_q2 = $rs->getDate($startcol + 5, null);

			$this->rbs_ss_last_rev_date_q3 = $rs->getDate($startcol + 6, null);

			$this->rbs_ss_last_rev_date_q4 = $rs->getDate($startcol + 7, null);

			$this->rbs_ss_osha_ri_q1 = $rs->getFloat($startcol + 8);

			$this->rbs_ss_osha_ri_q2 = $rs->getFloat($startcol + 9);

			$this->rbs_ss_osha_ri_q3 = $rs->getFloat($startcol + 10);

			$this->rbs_ss_osha_ri_q4 = $rs->getFloat($startcol + 11);

			$this->rbs_ss_injury_nar = $rs->getString($startcol + 12);

			$this->rbs_ss_psa_nar = $rs->getString($startcol + 13);

			$this->rbs_pmcr_ppm_prg_q1 = $rs->getFloat($startcol + 14);

			$this->rbs_pmcr_ppm_prg_q2 = $rs->getFloat($startcol + 15);

			$this->rbs_pmcr_ppm_prg_q3 = $rs->getFloat($startcol + 16);

			$this->rbs_pmcr_ppm_prg_q4 = $rs->getFloat($startcol + 17);

			$this->rbs_pmcr_ppm_nar = $rs->getString($startcol + 18);

			$this->rbs_pmcr_pc_prg_q1 = $rs->getFloat($startcol + 19);

			$this->rbs_pmcr_pc_prg_q2 = $rs->getFloat($startcol + 20);

			$this->rbs_pmcr_pc_prg_q3 = $rs->getFloat($startcol + 21);

			$this->rbs_pmcr_pc_prg_q4 = $rs->getFloat($startcol + 22);

			$this->rbs_pmcr_pc_nar = $rs->getString($startcol + 23);

			$this->rbs_pmcr_pr_prg_q1 = $rs->getFloat($startcol + 24);

			$this->rbs_pmcr_pr_prg_q2 = $rs->getFloat($startcol + 25);

			$this->rbs_pmcr_pr_prg_q3 = $rs->getFloat($startcol + 26);

			$this->rbs_pmcr_pr_prg_q4 = $rs->getFloat($startcol + 27);

			$this->rbs_pmcr_pr_nar = $rs->getString($startcol + 28);

			$this->cb_fe_prg_q1 = $rs->getFloat($startcol + 29);

			$this->cb_fe_prg_q2 = $rs->getFloat($startcol + 30);

			$this->cb_fe_prg_q3 = $rs->getFloat($startcol + 31);

			$this->cb_fe_prg_q4 = $rs->getFloat($startcol + 32);

			$this->cb_fe_nar = $rs->getString($startcol + 33);

			$this->ni_itca_prg_q1 = $rs->getFloat($startcol + 34);

			$this->ni_itca_prg_q2 = $rs->getFloat($startcol + 35);

			$this->ni_itca_prg_q3 = $rs->getFloat($startcol + 36);

			$this->ni_itca_prg_q4 = $rs->getFloat($startcol + 37);

			$this->ni_itca_nar = $rs->getString($startcol + 38);

			$this->ni_neot_prg_q1 = $rs->getFloat($startcol + 39);

			$this->ni_neot_prg_q2 = $rs->getFloat($startcol + 40);

			$this->ni_neot_prg_q3 = $rs->getFloat($startcol + 41);

			$this->ni_neot_prg_q4 = $rs->getFloat($startcol + 42);

			$this->ni_neot_nar = $rs->getString($startcol + 43);

			$this->ni_nrs_prg_q1 = $rs->getFloat($startcol + 44);

			$this->ni_nrs_prg_q2 = $rs->getFloat($startcol + 45);

			$this->ni_nrs_prg_q3 = $rs->getFloat($startcol + 46);

			$this->ni_nrs_prg_q4 = $rs->getFloat($startcol + 47);

			$this->ni_nrs_nar = $rs->getString($startcol + 48);

			$this->fh_nar = $rs->getString($startcol + 49);

			$this->aem_nar = $rs->getString($startcol + 50);

			$this->sa1_prg_q1 = $rs->getFloat($startcol + 51);

			$this->sa1_prg_q2 = $rs->getFloat($startcol + 52);

			$this->sa1_prg_q3 = $rs->getFloat($startcol + 53);

			$this->sa1_prg_q4 = $rs->getFloat($startcol + 54);

			$this->sa1_prg_nar = $rs->getString($startcol + 55);

			$this->sa2_prg_q1 = $rs->getFloat($startcol + 56);

			$this->sa2_prg_q2 = $rs->getFloat($startcol + 57);

			$this->sa2_prg_q3 = $rs->getFloat($startcol + 58);

			$this->sa2_prg_q4 = $rs->getFloat($startcol + 59);

			$this->sa2_prg_nar = $rs->getString($startcol + 60);

			$this->sa3_prg_q1 = $rs->getFloat($startcol + 61);

			$this->sa3_prg_q2 = $rs->getFloat($startcol + 62);

			$this->sa3_prg_q3 = $rs->getFloat($startcol + 63);

			$this->sa3_prg_q4 = $rs->getFloat($startcol + 64);

			$this->sa3_prg_nar = $rs->getString($startcol + 65);

			$this->created_by = $rs->getString($startcol + 66);

			$this->created_on = $rs->getDate($startcol + 67, null);

			$this->updated_by = $rs->getString($startcol + 68);

			$this->updated_on = $rs->getDate($startcol + 69, null);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 70; // 70 = SiteReportsQARPeer::NUM_COLUMNS - SiteReportsQARPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SiteReportsQAR object", $e);
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
			$con = Propel::getConnection(SiteReportsQARPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SiteReportsQARPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SiteReportsQARPeer::DATABASE_NAME);
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
					$pk = SiteReportsQARPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setID($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SiteReportsQARPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collSiteReportsQAREotEvts !== null) {
				foreach($this->collSiteReportsQAREotEvts as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSiteReportsQARRPSs !== null) {
				foreach($this->collSiteReportsQARRPSs as $referrerFK) {
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


			if (($retval = SiteReportsQARPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collSiteReportsQAREotEvts !== null) {
					foreach($this->collSiteReportsQAREotEvts as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSiteReportsQARRPSs !== null) {
					foreach($this->collSiteReportsQARRPSs as $referrerFK) {
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
		$pos = SiteReportsQARPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getFACILITY_ID();
				break;
			case 2:
				return $this->getYEAR();
				break;
			case 3:
				return $this->getQUARTER();
				break;
			case 4:
				return $this->getRBS_SS_LAST_REV_DATE_Q1();
				break;
			case 5:
				return $this->getRBS_SS_LAST_REV_DATE_Q2();
				break;
			case 6:
				return $this->getRBS_SS_LAST_REV_DATE_Q3();
				break;
			case 7:
				return $this->getRBS_SS_LAST_REV_DATE_Q4();
				break;
			case 8:
				return $this->getRBS_SS_RI_Q1();
				break;
			case 9:
				return $this->getRBS_SS_RI_Q2();
				break;
			case 10:
				return $this->getRBS_SS_RI_Q3();
				break;
			case 11:
				return $this->getRBS_SS_RI_Q4();
				break;
			case 12:
				return $this->getRBS_SS_INJURY_NAR();
				break;
			case 13:
				return $this->getRBS_SS_PSA_NAR();
				break;
			case 14:
				return $this->getRBS_PMCR_PPM_PRG_Q1();
				break;
			case 15:
				return $this->getRBS_PMCR_PPM_PRG_Q2();
				break;
			case 16:
				return $this->getRBS_PMCR_PPM_PRG_Q3();
				break;
			case 17:
				return $this->getRBS_PMCR_PPM_PRG_Q4();
				break;
			case 18:
				return $this->getRBS_PMCR_PPM_NAR();
				break;
			case 19:
				return $this->getRBS_PMCR_PC_PRG_Q1();
				break;
			case 20:
				return $this->getRBS_PMCR_PC_PRG_Q2();
				break;
			case 21:
				return $this->getRBS_PMCR_PC_PRG_Q3();
				break;
			case 22:
				return $this->getRBS_PMCR_PC_PRG_Q4();
				break;
			case 23:
				return $this->getRBS_PMCR_PC_NAR();
				break;
			case 24:
				return $this->getRBS_PMCR_PR_PRG_Q1();
				break;
			case 25:
				return $this->getRBS_PMCR_PR_PRG_Q2();
				break;
			case 26:
				return $this->getRBS_PMCR_PR_PRG_Q3();
				break;
			case 27:
				return $this->getRBS_PMCR_PR_PRG_Q4();
				break;
			case 28:
				return $this->getRBS_PMCR_PR_NAR();
				break;
			case 29:
				return $this->getCB_FE_PRG_Q1();
				break;
			case 30:
				return $this->getCB_FE_PRG_Q2();
				break;
			case 31:
				return $this->getCB_FE_PRG_Q3();
				break;
			case 32:
				return $this->getCB_FE_PRG_Q4();
				break;
			case 33:
				return $this->getCB_FE_NAR();
				break;
			case 34:
				return $this->getNI_ITCA_PRG_Q1();
				break;
			case 35:
				return $this->getNI_ITCA_PRG_Q2();
				break;
			case 36:
				return $this->getNI_ITCA_PRG_Q3();
				break;
			case 37:
				return $this->getNI_ITCA_PRG_Q4();
				break;
			case 38:
				return $this->getNI_ITCA_NAR();
				break;
			case 39:
				return $this->getNI_NEOT_PRG_Q1();
				break;
			case 40:
				return $this->getNI_NEOT_PRG_Q2();
				break;
			case 41:
				return $this->getNI_NEOT_PRG_Q3();
				break;
			case 42:
				return $this->getNI_NEOT_PRG_Q4();
				break;
			case 43:
				return $this->getNI_NEOT_NAR();
				break;
			case 44:
				return $this->getNI_NRS_PRG_Q1();
				break;
			case 45:
				return $this->getNI_NRS_PRG_Q2();
				break;
			case 46:
				return $this->getNI_NRS_PRG_Q3();
				break;
			case 47:
				return $this->getNI_NRS_PRG_Q4();
				break;
			case 48:
				return $this->getNI_NRS_NAR();
				break;
			case 49:
				return $this->getFH_NAR();
				break;
			case 50:
				return $this->getAEM_NAR();
				break;
			case 51:
				return $this->getSA1_PRG_Q1();
				break;
			case 52:
				return $this->getSA1_PRG_Q2();
				break;
			case 53:
				return $this->getSA1_PRG_Q3();
				break;
			case 54:
				return $this->getSA1_PRG_Q4();
				break;
			case 55:
				return $this->getSA1_PRG_NAR();
				break;
			case 56:
				return $this->getSA2_PRG_Q1();
				break;
			case 57:
				return $this->getSA2_PRG_Q2();
				break;
			case 58:
				return $this->getSA2_PRG_Q3();
				break;
			case 59:
				return $this->getSA2_PRG_Q4();
				break;
			case 60:
				return $this->getSA2_PRG_NAR();
				break;
			case 61:
				return $this->getSA3_PRG_Q1();
				break;
			case 62:
				return $this->getSA3_PRG_Q2();
				break;
			case 63:
				return $this->getSA3_PRG_Q3();
				break;
			case 64:
				return $this->getSA3_PRG_Q4();
				break;
			case 65:
				return $this->getSA3_PRG_NAR();
				break;
			case 66:
				return $this->getCREATED_BY();
				break;
			case 67:
				return $this->getCREATED_ON();
				break;
			case 68:
				return $this->getUPDATED_BY();
				break;
			case 69:
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
		$keys = SiteReportsQARPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getID(),
			$keys[1] => $this->getFACILITY_ID(),
			$keys[2] => $this->getYEAR(),
			$keys[3] => $this->getQUARTER(),
			$keys[4] => $this->getRBS_SS_LAST_REV_DATE_Q1(),
			$keys[5] => $this->getRBS_SS_LAST_REV_DATE_Q2(),
			$keys[6] => $this->getRBS_SS_LAST_REV_DATE_Q3(),
			$keys[7] => $this->getRBS_SS_LAST_REV_DATE_Q4(),
			$keys[8] => $this->getRBS_SS_RI_Q1(),
			$keys[9] => $this->getRBS_SS_RI_Q2(),
			$keys[10] => $this->getRBS_SS_RI_Q3(),
			$keys[11] => $this->getRBS_SS_RI_Q4(),
			$keys[12] => $this->getRBS_SS_INJURY_NAR(),
			$keys[13] => $this->getRBS_SS_PSA_NAR(),
			$keys[14] => $this->getRBS_PMCR_PPM_PRG_Q1(),
			$keys[15] => $this->getRBS_PMCR_PPM_PRG_Q2(),
			$keys[16] => $this->getRBS_PMCR_PPM_PRG_Q3(),
			$keys[17] => $this->getRBS_PMCR_PPM_PRG_Q4(),
			$keys[18] => $this->getRBS_PMCR_PPM_NAR(),
			$keys[19] => $this->getRBS_PMCR_PC_PRG_Q1(),
			$keys[20] => $this->getRBS_PMCR_PC_PRG_Q2(),
			$keys[21] => $this->getRBS_PMCR_PC_PRG_Q3(),
			$keys[22] => $this->getRBS_PMCR_PC_PRG_Q4(),
			$keys[23] => $this->getRBS_PMCR_PC_NAR(),
			$keys[24] => $this->getRBS_PMCR_PR_PRG_Q1(),
			$keys[25] => $this->getRBS_PMCR_PR_PRG_Q2(),
			$keys[26] => $this->getRBS_PMCR_PR_PRG_Q3(),
			$keys[27] => $this->getRBS_PMCR_PR_PRG_Q4(),
			$keys[28] => $this->getRBS_PMCR_PR_NAR(),
			$keys[29] => $this->getCB_FE_PRG_Q1(),
			$keys[30] => $this->getCB_FE_PRG_Q2(),
			$keys[31] => $this->getCB_FE_PRG_Q3(),
			$keys[32] => $this->getCB_FE_PRG_Q4(),
			$keys[33] => $this->getCB_FE_NAR(),
			$keys[34] => $this->getNI_ITCA_PRG_Q1(),
			$keys[35] => $this->getNI_ITCA_PRG_Q2(),
			$keys[36] => $this->getNI_ITCA_PRG_Q3(),
			$keys[37] => $this->getNI_ITCA_PRG_Q4(),
			$keys[38] => $this->getNI_ITCA_NAR(),
			$keys[39] => $this->getNI_NEOT_PRG_Q1(),
			$keys[40] => $this->getNI_NEOT_PRG_Q2(),
			$keys[41] => $this->getNI_NEOT_PRG_Q3(),
			$keys[42] => $this->getNI_NEOT_PRG_Q4(),
			$keys[43] => $this->getNI_NEOT_NAR(),
			$keys[44] => $this->getNI_NRS_PRG_Q1(),
			$keys[45] => $this->getNI_NRS_PRG_Q2(),
			$keys[46] => $this->getNI_NRS_PRG_Q3(),
			$keys[47] => $this->getNI_NRS_PRG_Q4(),
			$keys[48] => $this->getNI_NRS_NAR(),
			$keys[49] => $this->getFH_NAR(),
			$keys[50] => $this->getAEM_NAR(),
			$keys[51] => $this->getSA1_PRG_Q1(),
			$keys[52] => $this->getSA1_PRG_Q2(),
			$keys[53] => $this->getSA1_PRG_Q3(),
			$keys[54] => $this->getSA1_PRG_Q4(),
			$keys[55] => $this->getSA1_PRG_NAR(),
			$keys[56] => $this->getSA2_PRG_Q1(),
			$keys[57] => $this->getSA2_PRG_Q2(),
			$keys[58] => $this->getSA2_PRG_Q3(),
			$keys[59] => $this->getSA2_PRG_Q4(),
			$keys[60] => $this->getSA2_PRG_NAR(),
			$keys[61] => $this->getSA3_PRG_Q1(),
			$keys[62] => $this->getSA3_PRG_Q2(),
			$keys[63] => $this->getSA3_PRG_Q3(),
			$keys[64] => $this->getSA3_PRG_Q4(),
			$keys[65] => $this->getSA3_PRG_NAR(),
			$keys[66] => $this->getCREATED_BY(),
			$keys[67] => $this->getCREATED_ON(),
			$keys[68] => $this->getUPDATED_BY(),
			$keys[69] => $this->getUPDATED_ON(),
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
		$pos = SiteReportsQARPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setFACILITY_ID($value);
				break;
			case 2:
				$this->setYEAR($value);
				break;
			case 3:
				$this->setQUARTER($value);
				break;
			case 4:
				$this->setRBS_SS_LAST_REV_DATE_Q1($value);
				break;
			case 5:
				$this->setRBS_SS_LAST_REV_DATE_Q2($value);
				break;
			case 6:
				$this->setRBS_SS_LAST_REV_DATE_Q3($value);
				break;
			case 7:
				$this->setRBS_SS_LAST_REV_DATE_Q4($value);
				break;
			case 8:
				$this->setRBS_SS_RI_Q1($value);
				break;
			case 9:
				$this->setRBS_SS_RI_Q2($value);
				break;
			case 10:
				$this->setRBS_SS_RI_Q3($value);
				break;
			case 11:
				$this->setRBS_SS_RI_Q4($value);
				break;
			case 12:
				$this->setRBS_SS_INJURY_NAR($value);
				break;
			case 13:
				$this->setRBS_SS_PSA_NAR($value);
				break;
			case 14:
				$this->setRBS_PMCR_PPM_PRG_Q1($value);
				break;
			case 15:
				$this->setRBS_PMCR_PPM_PRG_Q2($value);
				break;
			case 16:
				$this->setRBS_PMCR_PPM_PRG_Q3($value);
				break;
			case 17:
				$this->setRBS_PMCR_PPM_PRG_Q4($value);
				break;
			case 18:
				$this->setRBS_PMCR_PPM_NAR($value);
				break;
			case 19:
				$this->setRBS_PMCR_PC_PRG_Q1($value);
				break;
			case 20:
				$this->setRBS_PMCR_PC_PRG_Q2($value);
				break;
			case 21:
				$this->setRBS_PMCR_PC_PRG_Q3($value);
				break;
			case 22:
				$this->setRBS_PMCR_PC_PRG_Q4($value);
				break;
			case 23:
				$this->setRBS_PMCR_PC_NAR($value);
				break;
			case 24:
				$this->setRBS_PMCR_PR_PRG_Q1($value);
				break;
			case 25:
				$this->setRBS_PMCR_PR_PRG_Q2($value);
				break;
			case 26:
				$this->setRBS_PMCR_PR_PRG_Q3($value);
				break;
			case 27:
				$this->setRBS_PMCR_PR_PRG_Q4($value);
				break;
			case 28:
				$this->setRBS_PMCR_PR_NAR($value);
				break;
			case 29:
				$this->setCB_FE_PRG_Q1($value);
				break;
			case 30:
				$this->setCB_FE_PRG_Q2($value);
				break;
			case 31:
				$this->setCB_FE_PRG_Q3($value);
				break;
			case 32:
				$this->setCB_FE_PRG_Q4($value);
				break;
			case 33:
				$this->setCB_FE_NAR($value);
				break;
			case 34:
				$this->setNI_ITCA_PRG_Q1($value);
				break;
			case 35:
				$this->setNI_ITCA_PRG_Q2($value);
				break;
			case 36:
				$this->setNI_ITCA_PRG_Q3($value);
				break;
			case 37:
				$this->setNI_ITCA_PRG_Q4($value);
				break;
			case 38:
				$this->setNI_ITCA_NAR($value);
				break;
			case 39:
				$this->setNI_NEOT_PRG_Q1($value);
				break;
			case 40:
				$this->setNI_NEOT_PRG_Q2($value);
				break;
			case 41:
				$this->setNI_NEOT_PRG_Q3($value);
				break;
			case 42:
				$this->setNI_NEOT_PRG_Q4($value);
				break;
			case 43:
				$this->setNI_NEOT_NAR($value);
				break;
			case 44:
				$this->setNI_NRS_PRG_Q1($value);
				break;
			case 45:
				$this->setNI_NRS_PRG_Q2($value);
				break;
			case 46:
				$this->setNI_NRS_PRG_Q3($value);
				break;
			case 47:
				$this->setNI_NRS_PRG_Q4($value);
				break;
			case 48:
				$this->setNI_NRS_NAR($value);
				break;
			case 49:
				$this->setFH_NAR($value);
				break;
			case 50:
				$this->setAEM_NAR($value);
				break;
			case 51:
				$this->setSA1_PRG_Q1($value);
				break;
			case 52:
				$this->setSA1_PRG_Q2($value);
				break;
			case 53:
				$this->setSA1_PRG_Q3($value);
				break;
			case 54:
				$this->setSA1_PRG_Q4($value);
				break;
			case 55:
				$this->setSA1_PRG_NAR($value);
				break;
			case 56:
				$this->setSA2_PRG_Q1($value);
				break;
			case 57:
				$this->setSA2_PRG_Q2($value);
				break;
			case 58:
				$this->setSA2_PRG_Q3($value);
				break;
			case 59:
				$this->setSA2_PRG_Q4($value);
				break;
			case 60:
				$this->setSA2_PRG_NAR($value);
				break;
			case 61:
				$this->setSA3_PRG_Q1($value);
				break;
			case 62:
				$this->setSA3_PRG_Q2($value);
				break;
			case 63:
				$this->setSA3_PRG_Q3($value);
				break;
			case 64:
				$this->setSA3_PRG_Q4($value);
				break;
			case 65:
				$this->setSA3_PRG_NAR($value);
				break;
			case 66:
				$this->setCREATED_BY($value);
				break;
			case 67:
				$this->setCREATED_ON($value);
				break;
			case 68:
				$this->setUPDATED_BY($value);
				break;
			case 69:
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
		$keys = SiteReportsQARPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setID($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setFACILITY_ID($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setYEAR($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setQUARTER($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setRBS_SS_LAST_REV_DATE_Q1($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setRBS_SS_LAST_REV_DATE_Q2($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setRBS_SS_LAST_REV_DATE_Q3($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setRBS_SS_LAST_REV_DATE_Q4($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setRBS_SS_RI_Q1($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setRBS_SS_RI_Q2($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setRBS_SS_RI_Q3($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setRBS_SS_RI_Q4($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setRBS_SS_INJURY_NAR($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setRBS_SS_PSA_NAR($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setRBS_PMCR_PPM_PRG_Q1($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setRBS_PMCR_PPM_PRG_Q2($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setRBS_PMCR_PPM_PRG_Q3($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setRBS_PMCR_PPM_PRG_Q4($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setRBS_PMCR_PPM_NAR($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setRBS_PMCR_PC_PRG_Q1($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setRBS_PMCR_PC_PRG_Q2($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setRBS_PMCR_PC_PRG_Q3($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setRBS_PMCR_PC_PRG_Q4($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setRBS_PMCR_PC_NAR($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setRBS_PMCR_PR_PRG_Q1($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setRBS_PMCR_PR_PRG_Q2($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setRBS_PMCR_PR_PRG_Q3($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setRBS_PMCR_PR_PRG_Q4($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setRBS_PMCR_PR_NAR($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setCB_FE_PRG_Q1($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setCB_FE_PRG_Q2($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setCB_FE_PRG_Q3($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setCB_FE_PRG_Q4($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setCB_FE_NAR($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setNI_ITCA_PRG_Q1($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setNI_ITCA_PRG_Q2($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setNI_ITCA_PRG_Q3($arr[$keys[36]]);
		if (array_key_exists($keys[37], $arr)) $this->setNI_ITCA_PRG_Q4($arr[$keys[37]]);
		if (array_key_exists($keys[38], $arr)) $this->setNI_ITCA_NAR($arr[$keys[38]]);
		if (array_key_exists($keys[39], $arr)) $this->setNI_NEOT_PRG_Q1($arr[$keys[39]]);
		if (array_key_exists($keys[40], $arr)) $this->setNI_NEOT_PRG_Q2($arr[$keys[40]]);
		if (array_key_exists($keys[41], $arr)) $this->setNI_NEOT_PRG_Q3($arr[$keys[41]]);
		if (array_key_exists($keys[42], $arr)) $this->setNI_NEOT_PRG_Q4($arr[$keys[42]]);
		if (array_key_exists($keys[43], $arr)) $this->setNI_NEOT_NAR($arr[$keys[43]]);
		if (array_key_exists($keys[44], $arr)) $this->setNI_NRS_PRG_Q1($arr[$keys[44]]);
		if (array_key_exists($keys[45], $arr)) $this->setNI_NRS_PRG_Q2($arr[$keys[45]]);
		if (array_key_exists($keys[46], $arr)) $this->setNI_NRS_PRG_Q3($arr[$keys[46]]);
		if (array_key_exists($keys[47], $arr)) $this->setNI_NRS_PRG_Q4($arr[$keys[47]]);
		if (array_key_exists($keys[48], $arr)) $this->setNI_NRS_NAR($arr[$keys[48]]);
		if (array_key_exists($keys[49], $arr)) $this->setFH_NAR($arr[$keys[49]]);
		if (array_key_exists($keys[50], $arr)) $this->setAEM_NAR($arr[$keys[50]]);
		if (array_key_exists($keys[51], $arr)) $this->setSA1_PRG_Q1($arr[$keys[51]]);
		if (array_key_exists($keys[52], $arr)) $this->setSA1_PRG_Q2($arr[$keys[52]]);
		if (array_key_exists($keys[53], $arr)) $this->setSA1_PRG_Q3($arr[$keys[53]]);
		if (array_key_exists($keys[54], $arr)) $this->setSA1_PRG_Q4($arr[$keys[54]]);
		if (array_key_exists($keys[55], $arr)) $this->setSA1_PRG_NAR($arr[$keys[55]]);
		if (array_key_exists($keys[56], $arr)) $this->setSA2_PRG_Q1($arr[$keys[56]]);
		if (array_key_exists($keys[57], $arr)) $this->setSA2_PRG_Q2($arr[$keys[57]]);
		if (array_key_exists($keys[58], $arr)) $this->setSA2_PRG_Q3($arr[$keys[58]]);
		if (array_key_exists($keys[59], $arr)) $this->setSA2_PRG_Q4($arr[$keys[59]]);
		if (array_key_exists($keys[60], $arr)) $this->setSA2_PRG_NAR($arr[$keys[60]]);
		if (array_key_exists($keys[61], $arr)) $this->setSA3_PRG_Q1($arr[$keys[61]]);
		if (array_key_exists($keys[62], $arr)) $this->setSA3_PRG_Q2($arr[$keys[62]]);
		if (array_key_exists($keys[63], $arr)) $this->setSA3_PRG_Q3($arr[$keys[63]]);
		if (array_key_exists($keys[64], $arr)) $this->setSA3_PRG_Q4($arr[$keys[64]]);
		if (array_key_exists($keys[65], $arr)) $this->setSA3_PRG_NAR($arr[$keys[65]]);
		if (array_key_exists($keys[66], $arr)) $this->setCREATED_BY($arr[$keys[66]]);
		if (array_key_exists($keys[67], $arr)) $this->setCREATED_ON($arr[$keys[67]]);
		if (array_key_exists($keys[68], $arr)) $this->setUPDATED_BY($arr[$keys[68]]);
		if (array_key_exists($keys[69], $arr)) $this->setUPDATED_ON($arr[$keys[69]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SiteReportsQARPeer::DATABASE_NAME);

		if ($this->isColumnModified(SiteReportsQARPeer::ID)) $criteria->add(SiteReportsQARPeer::ID, $this->id);
		if ($this->isColumnModified(SiteReportsQARPeer::FACILITY_ID)) $criteria->add(SiteReportsQARPeer::FACILITY_ID, $this->facility_id);
		if ($this->isColumnModified(SiteReportsQARPeer::YEAR)) $criteria->add(SiteReportsQARPeer::YEAR, $this->year);
		if ($this->isColumnModified(SiteReportsQARPeer::QUARTER)) $criteria->add(SiteReportsQARPeer::QUARTER, $this->quarter);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q1)) $criteria->add(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q1, $this->rbs_ss_last_rev_date_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q2)) $criteria->add(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q2, $this->rbs_ss_last_rev_date_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q3)) $criteria->add(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q3, $this->rbs_ss_last_rev_date_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q4)) $criteria->add(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q4, $this->rbs_ss_last_rev_date_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q1)) $criteria->add(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q1, $this->rbs_ss_osha_ri_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q2)) $criteria->add(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q2, $this->rbs_ss_osha_ri_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q3)) $criteria->add(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q3, $this->rbs_ss_osha_ri_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q4)) $criteria->add(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q4, $this->rbs_ss_osha_ri_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_SS_INJURY_NAR)) $criteria->add(SiteReportsQARPeer::RBS_SS_INJURY_NAR, $this->rbs_ss_injury_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_SS_PSA_NAR)) $criteria->add(SiteReportsQARPeer::RBS_SS_PSA_NAR, $this->rbs_ss_psa_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q1)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q1, $this->rbs_pmcr_ppm_prg_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q2)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q2, $this->rbs_pmcr_ppm_prg_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q3)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q3, $this->rbs_pmcr_ppm_prg_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q4)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q4, $this->rbs_pmcr_ppm_prg_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PPM_NAR)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PPM_NAR, $this->rbs_pmcr_ppm_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q1)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q1, $this->rbs_pmcr_pc_prg_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q2)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q2, $this->rbs_pmcr_pc_prg_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q3)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q3, $this->rbs_pmcr_pc_prg_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q4)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q4, $this->rbs_pmcr_pc_prg_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PC_NAR)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PC_NAR, $this->rbs_pmcr_pc_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q1)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q1, $this->rbs_pmcr_pr_prg_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q2)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q2, $this->rbs_pmcr_pr_prg_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q3)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q3, $this->rbs_pmcr_pr_prg_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q4)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q4, $this->rbs_pmcr_pr_prg_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::RBS_PMCR_PR_NAR)) $criteria->add(SiteReportsQARPeer::RBS_PMCR_PR_NAR, $this->rbs_pmcr_pr_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::CB_FE_PRG_Q1)) $criteria->add(SiteReportsQARPeer::CB_FE_PRG_Q1, $this->cb_fe_prg_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::CB_FE_PRG_Q2)) $criteria->add(SiteReportsQARPeer::CB_FE_PRG_Q2, $this->cb_fe_prg_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::CB_FE_PRG_Q3)) $criteria->add(SiteReportsQARPeer::CB_FE_PRG_Q3, $this->cb_fe_prg_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::CB_FE_PRG_Q4)) $criteria->add(SiteReportsQARPeer::CB_FE_PRG_Q4, $this->cb_fe_prg_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::CB_FE_NAR)) $criteria->add(SiteReportsQARPeer::CB_FE_NAR, $this->cb_fe_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_ITCA_PRG_Q1)) $criteria->add(SiteReportsQARPeer::NI_ITCA_PRG_Q1, $this->ni_itca_prg_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_ITCA_PRG_Q2)) $criteria->add(SiteReportsQARPeer::NI_ITCA_PRG_Q2, $this->ni_itca_prg_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_ITCA_PRG_Q3)) $criteria->add(SiteReportsQARPeer::NI_ITCA_PRG_Q3, $this->ni_itca_prg_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_ITCA_PRG_Q4)) $criteria->add(SiteReportsQARPeer::NI_ITCA_PRG_Q4, $this->ni_itca_prg_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_ITCA_NAR)) $criteria->add(SiteReportsQARPeer::NI_ITCA_NAR, $this->ni_itca_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_NEOT_PRG_Q1)) $criteria->add(SiteReportsQARPeer::NI_NEOT_PRG_Q1, $this->ni_neot_prg_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_NEOT_PRG_Q2)) $criteria->add(SiteReportsQARPeer::NI_NEOT_PRG_Q2, $this->ni_neot_prg_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_NEOT_PRG_Q3)) $criteria->add(SiteReportsQARPeer::NI_NEOT_PRG_Q3, $this->ni_neot_prg_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_NEOT_PRG_Q4)) $criteria->add(SiteReportsQARPeer::NI_NEOT_PRG_Q4, $this->ni_neot_prg_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_NEOT_NAR)) $criteria->add(SiteReportsQARPeer::NI_NEOT_NAR, $this->ni_neot_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_NRS_PRG_Q1)) $criteria->add(SiteReportsQARPeer::NI_NRS_PRG_Q1, $this->ni_nrs_prg_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_NRS_PRG_Q2)) $criteria->add(SiteReportsQARPeer::NI_NRS_PRG_Q2, $this->ni_nrs_prg_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_NRS_PRG_Q3)) $criteria->add(SiteReportsQARPeer::NI_NRS_PRG_Q3, $this->ni_nrs_prg_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_NRS_PRG_Q4)) $criteria->add(SiteReportsQARPeer::NI_NRS_PRG_Q4, $this->ni_nrs_prg_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::NI_NRS_NAR)) $criteria->add(SiteReportsQARPeer::NI_NRS_NAR, $this->ni_nrs_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::FH_NAR)) $criteria->add(SiteReportsQARPeer::FH_NAR, $this->fh_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::AEM_NAR)) $criteria->add(SiteReportsQARPeer::AEM_NAR, $this->aem_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::SA1_PRG_Q1)) $criteria->add(SiteReportsQARPeer::SA1_PRG_Q1, $this->sa1_prg_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::SA1_PRG_Q2)) $criteria->add(SiteReportsQARPeer::SA1_PRG_Q2, $this->sa1_prg_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::SA1_PRG_Q3)) $criteria->add(SiteReportsQARPeer::SA1_PRG_Q3, $this->sa1_prg_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::SA1_PRG_Q4)) $criteria->add(SiteReportsQARPeer::SA1_PRG_Q4, $this->sa1_prg_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::SA1_PRG_NAR)) $criteria->add(SiteReportsQARPeer::SA1_PRG_NAR, $this->sa1_prg_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::SA2_PRG_Q1)) $criteria->add(SiteReportsQARPeer::SA2_PRG_Q1, $this->sa2_prg_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::SA2_PRG_Q2)) $criteria->add(SiteReportsQARPeer::SA2_PRG_Q2, $this->sa2_prg_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::SA2_PRG_Q3)) $criteria->add(SiteReportsQARPeer::SA2_PRG_Q3, $this->sa2_prg_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::SA2_PRG_Q4)) $criteria->add(SiteReportsQARPeer::SA2_PRG_Q4, $this->sa2_prg_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::SA2_PRG_NAR)) $criteria->add(SiteReportsQARPeer::SA2_PRG_NAR, $this->sa2_prg_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::SA3_PRG_Q1)) $criteria->add(SiteReportsQARPeer::SA3_PRG_Q1, $this->sa3_prg_q1);
		if ($this->isColumnModified(SiteReportsQARPeer::SA3_PRG_Q2)) $criteria->add(SiteReportsQARPeer::SA3_PRG_Q2, $this->sa3_prg_q2);
		if ($this->isColumnModified(SiteReportsQARPeer::SA3_PRG_Q3)) $criteria->add(SiteReportsQARPeer::SA3_PRG_Q3, $this->sa3_prg_q3);
		if ($this->isColumnModified(SiteReportsQARPeer::SA3_PRG_Q4)) $criteria->add(SiteReportsQARPeer::SA3_PRG_Q4, $this->sa3_prg_q4);
		if ($this->isColumnModified(SiteReportsQARPeer::SA3_PRG_NAR)) $criteria->add(SiteReportsQARPeer::SA3_PRG_NAR, $this->sa3_prg_nar);
		if ($this->isColumnModified(SiteReportsQARPeer::CREATED_BY)) $criteria->add(SiteReportsQARPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(SiteReportsQARPeer::CREATED_ON)) $criteria->add(SiteReportsQARPeer::CREATED_ON, $this->created_on);
		if ($this->isColumnModified(SiteReportsQARPeer::UPDATED_BY)) $criteria->add(SiteReportsQARPeer::UPDATED_BY, $this->updated_by);
		if ($this->isColumnModified(SiteReportsQARPeer::UPDATED_ON)) $criteria->add(SiteReportsQARPeer::UPDATED_ON, $this->updated_on);

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
		$criteria = new Criteria(SiteReportsQARPeer::DATABASE_NAME);

		$criteria->add(SiteReportsQARPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SiteReportsQAR (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setFACILITY_ID($this->facility_id);

		$copyObj->setYEAR($this->year);

		$copyObj->setQUARTER($this->quarter);

		$copyObj->setRBS_SS_LAST_REV_DATE_Q1($this->rbs_ss_last_rev_date_q1);

		$copyObj->setRBS_SS_LAST_REV_DATE_Q2($this->rbs_ss_last_rev_date_q2);

		$copyObj->setRBS_SS_LAST_REV_DATE_Q3($this->rbs_ss_last_rev_date_q3);

		$copyObj->setRBS_SS_LAST_REV_DATE_Q4($this->rbs_ss_last_rev_date_q4);

		$copyObj->setRBS_SS_RI_Q1($this->rbs_ss_osha_ri_q1);

		$copyObj->setRBS_SS_RI_Q2($this->rbs_ss_osha_ri_q2);

		$copyObj->setRBS_SS_RI_Q3($this->rbs_ss_osha_ri_q3);

		$copyObj->setRBS_SS_RI_Q4($this->rbs_ss_osha_ri_q4);

		$copyObj->setRBS_SS_INJURY_NAR($this->rbs_ss_injury_nar);

		$copyObj->setRBS_SS_PSA_NAR($this->rbs_ss_psa_nar);

		$copyObj->setRBS_PMCR_PPM_PRG_Q1($this->rbs_pmcr_ppm_prg_q1);

		$copyObj->setRBS_PMCR_PPM_PRG_Q2($this->rbs_pmcr_ppm_prg_q2);

		$copyObj->setRBS_PMCR_PPM_PRG_Q3($this->rbs_pmcr_ppm_prg_q3);

		$copyObj->setRBS_PMCR_PPM_PRG_Q4($this->rbs_pmcr_ppm_prg_q4);

		$copyObj->setRBS_PMCR_PPM_NAR($this->rbs_pmcr_ppm_nar);

		$copyObj->setRBS_PMCR_PC_PRG_Q1($this->rbs_pmcr_pc_prg_q1);

		$copyObj->setRBS_PMCR_PC_PRG_Q2($this->rbs_pmcr_pc_prg_q2);

		$copyObj->setRBS_PMCR_PC_PRG_Q3($this->rbs_pmcr_pc_prg_q3);

		$copyObj->setRBS_PMCR_PC_PRG_Q4($this->rbs_pmcr_pc_prg_q4);

		$copyObj->setRBS_PMCR_PC_NAR($this->rbs_pmcr_pc_nar);

		$copyObj->setRBS_PMCR_PR_PRG_Q1($this->rbs_pmcr_pr_prg_q1);

		$copyObj->setRBS_PMCR_PR_PRG_Q2($this->rbs_pmcr_pr_prg_q2);

		$copyObj->setRBS_PMCR_PR_PRG_Q3($this->rbs_pmcr_pr_prg_q3);

		$copyObj->setRBS_PMCR_PR_PRG_Q4($this->rbs_pmcr_pr_prg_q4);

		$copyObj->setRBS_PMCR_PR_NAR($this->rbs_pmcr_pr_nar);

		$copyObj->setCB_FE_PRG_Q1($this->cb_fe_prg_q1);

		$copyObj->setCB_FE_PRG_Q2($this->cb_fe_prg_q2);

		$copyObj->setCB_FE_PRG_Q3($this->cb_fe_prg_q3);

		$copyObj->setCB_FE_PRG_Q4($this->cb_fe_prg_q4);

		$copyObj->setCB_FE_NAR($this->cb_fe_nar);

		$copyObj->setNI_ITCA_PRG_Q1($this->ni_itca_prg_q1);

		$copyObj->setNI_ITCA_PRG_Q2($this->ni_itca_prg_q2);

		$copyObj->setNI_ITCA_PRG_Q3($this->ni_itca_prg_q3);

		$copyObj->setNI_ITCA_PRG_Q4($this->ni_itca_prg_q4);

		$copyObj->setNI_ITCA_NAR($this->ni_itca_nar);

		$copyObj->setNI_NEOT_PRG_Q1($this->ni_neot_prg_q1);

		$copyObj->setNI_NEOT_PRG_Q2($this->ni_neot_prg_q2);

		$copyObj->setNI_NEOT_PRG_Q3($this->ni_neot_prg_q3);

		$copyObj->setNI_NEOT_PRG_Q4($this->ni_neot_prg_q4);

		$copyObj->setNI_NEOT_NAR($this->ni_neot_nar);

		$copyObj->setNI_NRS_PRG_Q1($this->ni_nrs_prg_q1);

		$copyObj->setNI_NRS_PRG_Q2($this->ni_nrs_prg_q2);

		$copyObj->setNI_NRS_PRG_Q3($this->ni_nrs_prg_q3);

		$copyObj->setNI_NRS_PRG_Q4($this->ni_nrs_prg_q4);

		$copyObj->setNI_NRS_NAR($this->ni_nrs_nar);

		$copyObj->setFH_NAR($this->fh_nar);

		$copyObj->setAEM_NAR($this->aem_nar);

		$copyObj->setSA1_PRG_Q1($this->sa1_prg_q1);

		$copyObj->setSA1_PRG_Q2($this->sa1_prg_q2);

		$copyObj->setSA1_PRG_Q3($this->sa1_prg_q3);

		$copyObj->setSA1_PRG_Q4($this->sa1_prg_q4);

		$copyObj->setSA1_PRG_NAR($this->sa1_prg_nar);

		$copyObj->setSA2_PRG_Q1($this->sa2_prg_q1);

		$copyObj->setSA2_PRG_Q2($this->sa2_prg_q2);

		$copyObj->setSA2_PRG_Q3($this->sa2_prg_q3);

		$copyObj->setSA2_PRG_Q4($this->sa2_prg_q4);

		$copyObj->setSA2_PRG_NAR($this->sa2_prg_nar);

		$copyObj->setSA3_PRG_Q1($this->sa3_prg_q1);

		$copyObj->setSA3_PRG_Q2($this->sa3_prg_q2);

		$copyObj->setSA3_PRG_Q3($this->sa3_prg_q3);

		$copyObj->setSA3_PRG_Q4($this->sa3_prg_q4);

		$copyObj->setSA3_PRG_NAR($this->sa3_prg_nar);

		$copyObj->setCREATED_BY($this->created_by);

		$copyObj->setCREATED_ON($this->created_on);

		$copyObj->setUPDATED_BY($this->updated_by);

		$copyObj->setUPDATED_ON($this->updated_on);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getSiteReportsQAREotEvts() as $relObj) {
				$copyObj->addSiteReportsQAREotEvt($relObj->copy($deepCopy));
			}

			foreach($this->getSiteReportsQARRPSs() as $relObj) {
				$copyObj->addSiteReportsQARRPS($relObj->copy($deepCopy));
			}

		} // if ($deepCopy)


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
	 * @return     SiteReportsQAR Clone of current object.
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
	 * @return     SiteReportsQARPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SiteReportsQARPeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collSiteReportsQAREotEvts to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSiteReportsQAREotEvts()
	{
		if ($this->collSiteReportsQAREotEvts === null) {
			$this->collSiteReportsQAREotEvts = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SiteReportsQAR has previously
	 * been saved, it will retrieve related SiteReportsQAREotEvts from storage.
	 * If this SiteReportsQAR is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSiteReportsQAREotEvts($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSiteReportsQAREotEvtPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSiteReportsQAREotEvts === null) {
			if ($this->isNew()) {
			   $this->collSiteReportsQAREotEvts = array();
			} else {

				$criteria->add(SiteReportsQAREotEvtPeer::QAR_ID, $this->getID());

				SiteReportsQAREotEvtPeer::addSelectColumns($criteria);
				$this->collSiteReportsQAREotEvts = SiteReportsQAREotEvtPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SiteReportsQAREotEvtPeer::QAR_ID, $this->getID());

				SiteReportsQAREotEvtPeer::addSelectColumns($criteria);
				if (!isset($this->lastSiteReportsQAREotEvtCriteria) || !$this->lastSiteReportsQAREotEvtCriteria->equals($criteria)) {
					$this->collSiteReportsQAREotEvts = SiteReportsQAREotEvtPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSiteReportsQAREotEvtCriteria = $criteria;
		return $this->collSiteReportsQAREotEvts;
	}

	/**
	 * Returns the number of related SiteReportsQAREotEvts.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSiteReportsQAREotEvts($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSiteReportsQAREotEvtPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SiteReportsQAREotEvtPeer::QAR_ID, $this->getID());

		return SiteReportsQAREotEvtPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SiteReportsQAREotEvt object to this object
	 * through the SiteReportsQAREotEvt foreign key attribute
	 *
	 * @param      SiteReportsQAREotEvt $l SiteReportsQAREotEvt
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSiteReportsQAREotEvt(SiteReportsQAREotEvt $l)
	{
		$this->collSiteReportsQAREotEvts[] = $l;
		$l->setSiteReportsQAR($this);
	}

	/**
	 * Temporary storage of collSiteReportsQARRPSs to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSiteReportsQARRPSs()
	{
		if ($this->collSiteReportsQARRPSs === null) {
			$this->collSiteReportsQARRPSs = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SiteReportsQAR has previously
	 * been saved, it will retrieve related SiteReportsQARRPSs from storage.
	 * If this SiteReportsQAR is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSiteReportsQARRPSs($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSiteReportsQARRPSPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSiteReportsQARRPSs === null) {
			if ($this->isNew()) {
			   $this->collSiteReportsQARRPSs = array();
			} else {

				$criteria->add(SiteReportsQARRPSPeer::QAR_ID, $this->getID());

				SiteReportsQARRPSPeer::addSelectColumns($criteria);
				$this->collSiteReportsQARRPSs = SiteReportsQARRPSPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SiteReportsQARRPSPeer::QAR_ID, $this->getID());

				SiteReportsQARRPSPeer::addSelectColumns($criteria);
				if (!isset($this->lastSiteReportsQARRPSCriteria) || !$this->lastSiteReportsQARRPSCriteria->equals($criteria)) {
					$this->collSiteReportsQARRPSs = SiteReportsQARRPSPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSiteReportsQARRPSCriteria = $criteria;
		return $this->collSiteReportsQARRPSs;
	}

	/**
	 * Returns the number of related SiteReportsQARRPSs.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSiteReportsQARRPSs($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSiteReportsQARRPSPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SiteReportsQARRPSPeer::QAR_ID, $this->getID());

		return SiteReportsQARRPSPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SiteReportsQARRPS object to this object
	 * through the SiteReportsQARRPS foreign key attribute
	 *
	 * @param      SiteReportsQARRPS $l SiteReportsQARRPS
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSiteReportsQARRPS(SiteReportsQARRPS $l)
	{
		$this->collSiteReportsQARRPSs[] = $l;
		$l->setSiteReportsQAR($this);
	}

} // BaseSiteReportsQAR
