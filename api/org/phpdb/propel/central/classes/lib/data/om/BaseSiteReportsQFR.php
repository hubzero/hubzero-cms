<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/SiteReportsQFRPeer.php';

/**
 * Base class that represents a row from the 'SITEREPORTS_QFR' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSiteReportsQFR extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SiteReportsQFRPeer
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
	 * The value for the prepared_by field.
	 * @var        string
	 */
	protected $prepared_by;


	/**
	 * The value for the preparers_title field.
	 * @var        string
	 */
	protected $preparers_title;


	/**
	 * The value for the prepared_date field.
	 * @var        int
	 */
	protected $prepared_date;


	/**
	 * The value for the report_period field.
	 * @var        string
	 */
	protected $report_period;


	/**
	 * The value for the subawarded_funded_amt field.
	 * @var        double
	 */
	protected $subawarded_funded_amt;


	/**
	 * The value for the qfr_sr_p_cost field.
	 * @var        double
	 */
	protected $qfr_sr_p_cost;


	/**
	 * The value for the qfr_sr_e_cost field.
	 * @var        double
	 */
	protected $qfr_sr_e_cost;


	/**
	 * The value for the qfr_sr_psc_cost field.
	 * @var        double
	 */
	protected $qfr_sr_psc_cost;


	/**
	 * The value for the qfr_sr_odc_cost field.
	 * @var        double
	 */
	protected $qfr_sr_odc_cost;


	/**
	 * The value for the qfr_sr_ic_cost field.
	 * @var        double
	 */
	protected $qfr_sr_ic_cost;


	/**
	 * The value for the qfr_nr_p_cost field.
	 * @var        double
	 */
	protected $qfr_nr_p_cost;


	/**
	 * The value for the qfr_nr_e_cost field.
	 * @var        double
	 */
	protected $qfr_nr_e_cost;


	/**
	 * The value for the qfr_nr_psc_cost field.
	 * @var        double
	 */
	protected $qfr_nr_psc_cost;


	/**
	 * The value for the qfr_nr_odc_cost field.
	 * @var        double
	 */
	protected $qfr_nr_odc_cost;


	/**
	 * The value for the qfr_nr_ic_cost field.
	 * @var        double
	 */
	protected $qfr_nr_ic_cost;


	/**
	 * The value for the qfr_itca_p_cost field.
	 * @var        double
	 */
	protected $qfr_itca_p_cost;


	/**
	 * The value for the qfr_itca_e_cost field.
	 * @var        double
	 */
	protected $qfr_itca_e_cost;


	/**
	 * The value for the qfr_itca_psc_cost field.
	 * @var        double
	 */
	protected $qfr_itca_psc_cost;


	/**
	 * The value for the qfr_itca_odc_cost field.
	 * @var        double
	 */
	protected $qfr_itca_odc_cost;


	/**
	 * The value for the qfr_itca_ic_cost field.
	 * @var        double
	 */
	protected $qfr_itca_ic_cost;


	/**
	 * The value for the qfr_neot_p_cost field.
	 * @var        double
	 */
	protected $qfr_neot_p_cost;


	/**
	 * The value for the qfr_neot_e_cost field.
	 * @var        double
	 */
	protected $qfr_neot_e_cost;


	/**
	 * The value for the qfr_neot_psc_cost field.
	 * @var        double
	 */
	protected $qfr_neot_psc_cost;


	/**
	 * The value for the qfr_neot_odc_cost field.
	 * @var        double
	 */
	protected $qfr_neot_odc_cost;


	/**
	 * The value for the qfr_neot_ic_cost field.
	 * @var        double
	 */
	protected $qfr_neot_ic_cost;


	/**
	 * The value for the qfr_fea_p_cost field.
	 * @var        double
	 */
	protected $qfr_fea_p_cost;


	/**
	 * The value for the qfr_fea_e_cost field.
	 * @var        double
	 */
	protected $qfr_fea_e_cost;


	/**
	 * The value for the qfr_fea_psc_cost field.
	 * @var        double
	 */
	protected $qfr_fea_psc_cost;


	/**
	 * The value for the qfr_fea_odc_cost field.
	 * @var        double
	 */
	protected $qfr_fea_odc_cost;


	/**
	 * The value for the qfr_fea_ic_cost field.
	 * @var        double
	 */
	protected $qfr_fea_ic_cost;


	/**
	 * The value for the qfr_aem_p_cost field.
	 * @var        double
	 */
	protected $qfr_aem_p_cost;


	/**
	 * The value for the qfr_aem_e_cost field.
	 * @var        double
	 */
	protected $qfr_aem_e_cost;


	/**
	 * The value for the qfr_aem_psc_cost field.
	 * @var        double
	 */
	protected $qfr_aem_psc_cost;


	/**
	 * The value for the qfr_aem_odc_cost field.
	 * @var        double
	 */
	protected $qfr_aem_odc_cost;


	/**
	 * The value for the qfr_aem_ic_cost field.
	 * @var        double
	 */
	protected $qfr_aem_ic_cost;


	/**
	 * The value for the qfr_nrs_p_cost field.
	 * @var        double
	 */
	protected $qfr_nrs_p_cost;


	/**
	 * The value for the qfr_nrs_e_cost field.
	 * @var        double
	 */
	protected $qfr_nrs_e_cost;


	/**
	 * The value for the qfr_nrs_psc_cost field.
	 * @var        double
	 */
	protected $qfr_nrs_psc_cost;


	/**
	 * The value for the qfr_nrs_odc_cost field.
	 * @var        double
	 */
	protected $qfr_nrs_odc_cost;


	/**
	 * The value for the qfr_nrs_ic_cost field.
	 * @var        double
	 */
	protected $qfr_nrs_ic_cost;


	/**
	 * The value for the fy_budget_surs field.
	 * @var        double
	 */
	protected $fy_budget_surs;


	/**
	 * The value for the fy_budget_sr field.
	 * @var        double
	 */
	protected $fy_budget_sr;


	/**
	 * The value for the fy_budget_nr field.
	 * @var        double
	 */
	protected $fy_budget_nr;


	/**
	 * The value for the fy_budget_itca field.
	 * @var        double
	 */
	protected $fy_budget_itca;


	/**
	 * The value for the fy_budget_fea field.
	 * @var        double
	 */
	protected $fy_budget_fea;


	/**
	 * The value for the fy_budget_neot field.
	 * @var        double
	 */
	protected $fy_budget_neot;


	/**
	 * The value for the fy_budget_aem field.
	 * @var        double
	 */
	protected $fy_budget_aem;


	/**
	 * The value for the fy_budget_nrs field.
	 * @var        double
	 */
	protected $fy_budget_nrs;


	/**
	 * The value for the q1re_surs field.
	 * @var        double
	 */
	protected $q1re_surs;


	/**
	 * The value for the q1re_sr field.
	 * @var        double
	 */
	protected $q1re_sr;


	/**
	 * The value for the q1re_nr field.
	 * @var        double
	 */
	protected $q1re_nr;


	/**
	 * The value for the q1re_itca field.
	 * @var        double
	 */
	protected $q1re_itca;


	/**
	 * The value for the q1re_fea field.
	 * @var        double
	 */
	protected $q1re_fea;


	/**
	 * The value for the q1re_neot field.
	 * @var        double
	 */
	protected $q1re_neot;


	/**
	 * The value for the q1re_aem field.
	 * @var        double
	 */
	protected $q1re_aem;


	/**
	 * The value for the q1re_nrs field.
	 * @var        double
	 */
	protected $q1re_nrs;


	/**
	 * The value for the q2re_surs field.
	 * @var        double
	 */
	protected $q2re_surs;


	/**
	 * The value for the q2re_sr field.
	 * @var        double
	 */
	protected $q2re_sr;


	/**
	 * The value for the q2re_nr field.
	 * @var        double
	 */
	protected $q2re_nr;


	/**
	 * The value for the q2re_itca field.
	 * @var        double
	 */
	protected $q2re_itca;


	/**
	 * The value for the q2re_fea field.
	 * @var        double
	 */
	protected $q2re_fea;


	/**
	 * The value for the q2re_neot field.
	 * @var        double
	 */
	protected $q2re_neot;


	/**
	 * The value for the q2re_aem field.
	 * @var        double
	 */
	protected $q2re_aem;


	/**
	 * The value for the q2re_nrs field.
	 * @var        double
	 */
	protected $q2re_nrs;


	/**
	 * The value for the q3re_surs field.
	 * @var        double
	 */
	protected $q3re_surs;


	/**
	 * The value for the q3re_sr field.
	 * @var        double
	 */
	protected $q3re_sr;


	/**
	 * The value for the q3re_nr field.
	 * @var        double
	 */
	protected $q3re_nr;


	/**
	 * The value for the q3re_itca field.
	 * @var        double
	 */
	protected $q3re_itca;


	/**
	 * The value for the q3re_fea field.
	 * @var        double
	 */
	protected $q3re_fea;


	/**
	 * The value for the q3re_neot field.
	 * @var        double
	 */
	protected $q3re_neot;


	/**
	 * The value for the q3re_aem field.
	 * @var        double
	 */
	protected $q3re_aem;


	/**
	 * The value for the q3re_nrs field.
	 * @var        double
	 */
	protected $q3re_nrs;


	/**
	 * The value for the q4re_surs field.
	 * @var        double
	 */
	protected $q4re_surs;


	/**
	 * The value for the q4re_sr field.
	 * @var        double
	 */
	protected $q4re_sr;


	/**
	 * The value for the q4re_nr field.
	 * @var        double
	 */
	protected $q4re_nr;


	/**
	 * The value for the q4re_itca field.
	 * @var        double
	 */
	protected $q4re_itca;


	/**
	 * The value for the q4re_fea field.
	 * @var        double
	 */
	protected $q4re_fea;


	/**
	 * The value for the q4re_neot field.
	 * @var        double
	 */
	protected $q4re_neot;


	/**
	 * The value for the q4re_aem field.
	 * @var        double
	 */
	protected $q4re_aem;


	/**
	 * The value for the q4re_nrs field.
	 * @var        double
	 */
	protected $q4re_nrs;


	/**
	 * The value for the pqa_surs field.
	 * @var        double
	 */
	protected $pqa_surs;


	/**
	 * The value for the pqa_sr field.
	 * @var        double
	 */
	protected $pqa_sr;


	/**
	 * The value for the pqa_nr field.
	 * @var        double
	 */
	protected $pqa_nr;


	/**
	 * The value for the pqa_itca field.
	 * @var        double
	 */
	protected $pqa_itca;


	/**
	 * The value for the pqa_fea field.
	 * @var        double
	 */
	protected $pqa_fea;


	/**
	 * The value for the pqa_neot field.
	 * @var        double
	 */
	protected $pqa_neot;


	/**
	 * The value for the pqa_aem field.
	 * @var        double
	 */
	protected $pqa_aem;


	/**
	 * The value for the pqa_nrs field.
	 * @var        double
	 */
	protected $pqa_nrs;


	/**
	 * The value for the cqe_surs field.
	 * @var        double
	 */
	protected $cqe_surs;


	/**
	 * The value for the cqe_sr field.
	 * @var        double
	 */
	protected $cqe_sr;


	/**
	 * The value for the cqe_nr field.
	 * @var        double
	 */
	protected $cqe_nr;


	/**
	 * The value for the cqe_itca field.
	 * @var        double
	 */
	protected $cqe_itca;


	/**
	 * The value for the cqe_fea field.
	 * @var        double
	 */
	protected $cqe_fea;


	/**
	 * The value for the cqe_neot field.
	 * @var        double
	 */
	protected $cqe_neot;


	/**
	 * The value for the cqe_aem field.
	 * @var        double
	 */
	protected $cqe_aem;


	/**
	 * The value for the cqe_nrs field.
	 * @var        double
	 */
	protected $cqe_nrs;


	/**
	 * The value for the supbud_sup1_p field.
	 * @var        double
	 */
	protected $supbud_sup1_p;


	/**
	 * The value for the supbud_sup1_e field.
	 * @var        double
	 */
	protected $supbud_sup1_e;


	/**
	 * The value for the supbud_sup1_psc field.
	 * @var        double
	 */
	protected $supbud_sup1_psc;


	/**
	 * The value for the supbud_sup1_odc field.
	 * @var        double
	 */
	protected $supbud_sup1_odc;


	/**
	 * The value for the supbud_sup1_ic field.
	 * @var        double
	 */
	protected $supbud_sup1_ic;


	/**
	 * The value for the supbud_sup1_sa field.
	 * @var        double
	 */
	protected $supbud_sup1_sa;


	/**
	 * The value for the supbud_sup2_p field.
	 * @var        double
	 */
	protected $supbud_sup2_p;


	/**
	 * The value for the supbud_sup2_e field.
	 * @var        double
	 */
	protected $supbud_sup2_e;


	/**
	 * The value for the supbud_sup2_psc field.
	 * @var        double
	 */
	protected $supbud_sup2_psc;


	/**
	 * The value for the supbud_sup2_odc field.
	 * @var        double
	 */
	protected $supbud_sup2_odc;


	/**
	 * The value for the supbud_sup2_ic field.
	 * @var        double
	 */
	protected $supbud_sup2_ic;


	/**
	 * The value for the supbud_sup2_sa field.
	 * @var        double
	 */
	protected $supbud_sup2_sa;


	/**
	 * The value for the supbud_sup3_p field.
	 * @var        double
	 */
	protected $supbud_sup3_p;


	/**
	 * The value for the supbud_sup3_e field.
	 * @var        double
	 */
	protected $supbud_sup3_e;


	/**
	 * The value for the supbud_sup3_psc field.
	 * @var        double
	 */
	protected $supbud_sup3_psc;


	/**
	 * The value for the supbud_sup3_odc field.
	 * @var        double
	 */
	protected $supbud_sup3_odc;


	/**
	 * The value for the supbud_sup3_ic field.
	 * @var        double
	 */
	protected $supbud_sup3_ic;


	/**
	 * The value for the supbud_sup3_sa field.
	 * @var        double
	 */
	protected $supbud_sup3_sa;


	/**
	 * The value for the supbud_sup4_p field.
	 * @var        double
	 */
	protected $supbud_sup4_p;


	/**
	 * The value for the supbud_sup4_e field.
	 * @var        double
	 */
	protected $supbud_sup4_e;


	/**
	 * The value for the supbud_sup4_psc field.
	 * @var        double
	 */
	protected $supbud_sup4_psc;


	/**
	 * The value for the supbud_sup4_odc field.
	 * @var        double
	 */
	protected $supbud_sup4_odc;


	/**
	 * The value for the supbud_sup4_ic field.
	 * @var        double
	 */
	protected $supbud_sup4_ic;


	/**
	 * The value for the supbud_sup4_sa field.
	 * @var        double
	 */
	protected $supbud_sup4_sa;


	/**
	 * The value for the pi_beg_bal field.
	 * @var        double
	 */
	protected $pi_beg_bal;


	/**
	 * The value for the pi_pir field.
	 * @var        double
	 */
	protected $pi_pir;


	/**
	 * The value for the pi_pie field.
	 * @var        double
	 */
	protected $pi_pie;


	/**
	 * The value for the pi_nar field.
	 * @var        string
	 */
	protected $pi_nar;


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
	 * Collection to store aggregation of collSiteReportsQFREPcds.
	 * @var        array
	 */
	protected $collSiteReportsQFREPcds;

	/**
	 * The criteria used to select the current contents of collSiteReportsQFREPcds.
	 * @var        Criteria
	 */
	protected $lastSiteReportsQFREPcdCriteria = null;

	/**
	 * Collection to store aggregation of collSiteReportsQFRProjects.
	 * @var        array
	 */
	protected $collSiteReportsQFRProjects;

	/**
	 * The criteria used to select the current contents of collSiteReportsQFRProjects.
	 * @var        Criteria
	 */
	protected $lastSiteReportsQFRProjectCriteria = null;

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
	 * Get the [prepared_by] column value.
	 * 
	 * @return     string
	 */
	public function getPREPARED_BY()
	{

		return $this->prepared_by;
	}

	/**
	 * Get the [preparers_title] column value.
	 * 
	 * @return     string
	 */
	public function getPREPARERS_TITLE()
	{

		return $this->preparers_title;
	}

	/**
	 * Get the [optionally formatted] [prepared_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getPREPARED_DATE($format = '%Y-%m-%d')
	{

		if ($this->prepared_date === null || $this->prepared_date === '') {
			return null;
		} elseif (!is_int($this->prepared_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->prepared_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [prepared_date] as date/time value: " . var_export($this->prepared_date, true));
			}
		} else {
			$ts = $this->prepared_date;
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
	 * Get the [report_period] column value.
	 * 
	 * @return     string
	 */
	public function getREPORT_PERIOD()
	{

		return $this->report_period;
	}

	/**
	 * Get the [subawarded_funded_amt] column value.
	 * 
	 * @return     double
	 */
	public function getSUBAWARDED_FUNDED_AMT()
	{

		return $this->subawarded_funded_amt;
	}

	/**
	 * Get the [qfr_sr_p_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_SR_P_COST()
	{

		return $this->qfr_sr_p_cost;
	}

	/**
	 * Get the [qfr_sr_e_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_SR_E_COST()
	{

		return $this->qfr_sr_e_cost;
	}

	/**
	 * Get the [qfr_sr_psc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_SR_PSC_COST()
	{

		return $this->qfr_sr_psc_cost;
	}

	/**
	 * Get the [qfr_sr_odc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_SR_ODC_COST()
	{

		return $this->qfr_sr_odc_cost;
	}

	/**
	 * Get the [qfr_sr_ic_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_SR_IC_COST()
	{

		return $this->qfr_sr_ic_cost;
	}

	/**
	 * Get the [qfr_nr_p_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NR_P_COST()
	{

		return $this->qfr_nr_p_cost;
	}

	/**
	 * Get the [qfr_nr_e_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NR_E_COST()
	{

		return $this->qfr_nr_e_cost;
	}

	/**
	 * Get the [qfr_nr_psc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NR_PSC_COST()
	{

		return $this->qfr_nr_psc_cost;
	}

	/**
	 * Get the [qfr_nr_odc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NR_ODC_COST()
	{

		return $this->qfr_nr_odc_cost;
	}

	/**
	 * Get the [qfr_nr_ic_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NR_IC_COST()
	{

		return $this->qfr_nr_ic_cost;
	}

	/**
	 * Get the [qfr_itca_p_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_ITCA_P_COST()
	{

		return $this->qfr_itca_p_cost;
	}

	/**
	 * Get the [qfr_itca_e_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_ITCA_E_COST()
	{

		return $this->qfr_itca_e_cost;
	}

	/**
	 * Get the [qfr_itca_psc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_ITCA_PSC_COST()
	{

		return $this->qfr_itca_psc_cost;
	}

	/**
	 * Get the [qfr_itca_odc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_ITCA_ODC_COST()
	{

		return $this->qfr_itca_odc_cost;
	}

	/**
	 * Get the [qfr_itca_ic_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_ITCA_IC_COST()
	{

		return $this->qfr_itca_ic_cost;
	}

	/**
	 * Get the [qfr_neot_p_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NEOT_P_COST()
	{

		return $this->qfr_neot_p_cost;
	}

	/**
	 * Get the [qfr_neot_e_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NEOT_E_COST()
	{

		return $this->qfr_neot_e_cost;
	}

	/**
	 * Get the [qfr_neot_psc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NEOT_PSC_COST()
	{

		return $this->qfr_neot_psc_cost;
	}

	/**
	 * Get the [qfr_neot_odc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NEOT_ODC_COST()
	{

		return $this->qfr_neot_odc_cost;
	}

	/**
	 * Get the [qfr_neot_ic_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NEOT_IC_COST()
	{

		return $this->qfr_neot_ic_cost;
	}

	/**
	 * Get the [qfr_fea_p_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_FEA_P_COST()
	{

		return $this->qfr_fea_p_cost;
	}

	/**
	 * Get the [qfr_fea_e_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_FEA_E_COST()
	{

		return $this->qfr_fea_e_cost;
	}

	/**
	 * Get the [qfr_fea_psc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_FEA_PSC_COST()
	{

		return $this->qfr_fea_psc_cost;
	}

	/**
	 * Get the [qfr_fea_odc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_FEA_ODC_COST()
	{

		return $this->qfr_fea_odc_cost;
	}

	/**
	 * Get the [qfr_fea_ic_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_FEA_IC_COST()
	{

		return $this->qfr_fea_ic_cost;
	}

	/**
	 * Get the [qfr_aem_p_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_AEM_P_COST()
	{

		return $this->qfr_aem_p_cost;
	}

	/**
	 * Get the [qfr_aem_e_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_AEM_E_COST()
	{

		return $this->qfr_aem_e_cost;
	}

	/**
	 * Get the [qfr_aem_psc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_AEM_PSC_COST()
	{

		return $this->qfr_aem_psc_cost;
	}

	/**
	 * Get the [qfr_aem_odc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_AEM_ODC_COST()
	{

		return $this->qfr_aem_odc_cost;
	}

	/**
	 * Get the [qfr_aem_ic_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_AEM_IC_COST()
	{

		return $this->qfr_aem_ic_cost;
	}

	/**
	 * Get the [qfr_nrs_p_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NRS_P_COST()
	{

		return $this->qfr_nrs_p_cost;
	}

	/**
	 * Get the [qfr_nrs_e_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NRS_E_COST()
	{

		return $this->qfr_nrs_e_cost;
	}

	/**
	 * Get the [qfr_nrs_psc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NRS_PSC_COST()
	{

		return $this->qfr_nrs_psc_cost;
	}

	/**
	 * Get the [qfr_nrs_odc_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NRS_ODC_COST()
	{

		return $this->qfr_nrs_odc_cost;
	}

	/**
	 * Get the [qfr_nrs_ic_cost] column value.
	 * 
	 * @return     double
	 */
	public function getQFR_NRS_IC_COST()
	{

		return $this->qfr_nrs_ic_cost;
	}

	/**
	 * Get the [fy_budget_surs] column value.
	 * 
	 * @return     double
	 */
	public function getFY_BUDGET_SURS()
	{

		return $this->fy_budget_surs;
	}

	/**
	 * Get the [fy_budget_sr] column value.
	 * 
	 * @return     double
	 */
	public function getFY_BUDGET_SR()
	{

		return $this->fy_budget_sr;
	}

	/**
	 * Get the [fy_budget_nr] column value.
	 * 
	 * @return     double
	 */
	public function getFY_BUDGET_NR()
	{

		return $this->fy_budget_nr;
	}

	/**
	 * Get the [fy_budget_itca] column value.
	 * 
	 * @return     double
	 */
	public function getFY_BUDGET_ITCA()
	{

		return $this->fy_budget_itca;
	}

	/**
	 * Get the [fy_budget_fea] column value.
	 * 
	 * @return     double
	 */
	public function getFY_BUDGET_FEA()
	{

		return $this->fy_budget_fea;
	}

	/**
	 * Get the [fy_budget_neot] column value.
	 * 
	 * @return     double
	 */
	public function getFY_BUDGET_NEOT()
	{

		return $this->fy_budget_neot;
	}

	/**
	 * Get the [fy_budget_aem] column value.
	 * 
	 * @return     double
	 */
	public function getFY_BUDGET_AEM()
	{

		return $this->fy_budget_aem;
	}

	/**
	 * Get the [fy_budget_nrs] column value.
	 * 
	 * @return     double
	 */
	public function getFY_BUDGET_NRS()
	{

		return $this->fy_budget_nrs;
	}

	/**
	 * Get the [q1re_surs] column value.
	 * 
	 * @return     double
	 */
	public function getQ1RE_SURS()
	{

		return $this->q1re_surs;
	}

	/**
	 * Get the [q1re_sr] column value.
	 * 
	 * @return     double
	 */
	public function getQ1RE_SR()
	{

		return $this->q1re_sr;
	}

	/**
	 * Get the [q1re_nr] column value.
	 * 
	 * @return     double
	 */
	public function getQ1RE_NR()
	{

		return $this->q1re_nr;
	}

	/**
	 * Get the [q1re_itca] column value.
	 * 
	 * @return     double
	 */
	public function getQ1RE_ITCA()
	{

		return $this->q1re_itca;
	}

	/**
	 * Get the [q1re_fea] column value.
	 * 
	 * @return     double
	 */
	public function getQ1RE_FEA()
	{

		return $this->q1re_fea;
	}

	/**
	 * Get the [q1re_neot] column value.
	 * 
	 * @return     double
	 */
	public function getQ1RE_NEOT()
	{

		return $this->q1re_neot;
	}

	/**
	 * Get the [q1re_aem] column value.
	 * 
	 * @return     double
	 */
	public function getQ1RE_AEM()
	{

		return $this->q1re_aem;
	}

	/**
	 * Get the [q1re_nrs] column value.
	 * 
	 * @return     double
	 */
	public function getQ1RE_NRS()
	{

		return $this->q1re_nrs;
	}

	/**
	 * Get the [q2re_surs] column value.
	 * 
	 * @return     double
	 */
	public function getQ2RE_SURS()
	{

		return $this->q2re_surs;
	}

	/**
	 * Get the [q2re_sr] column value.
	 * 
	 * @return     double
	 */
	public function getQ2RE_SR()
	{

		return $this->q2re_sr;
	}

	/**
	 * Get the [q2re_nr] column value.
	 * 
	 * @return     double
	 */
	public function getQ2RE_NR()
	{

		return $this->q2re_nr;
	}

	/**
	 * Get the [q2re_itca] column value.
	 * 
	 * @return     double
	 */
	public function getQ2RE_ITCA()
	{

		return $this->q2re_itca;
	}

	/**
	 * Get the [q2re_fea] column value.
	 * 
	 * @return     double
	 */
	public function getQ2RE_FEA()
	{

		return $this->q2re_fea;
	}

	/**
	 * Get the [q2re_neot] column value.
	 * 
	 * @return     double
	 */
	public function getQ2RE_NEOT()
	{

		return $this->q2re_neot;
	}

	/**
	 * Get the [q2re_aem] column value.
	 * 
	 * @return     double
	 */
	public function getQ2RE_AEM()
	{

		return $this->q2re_aem;
	}

	/**
	 * Get the [q2re_nrs] column value.
	 * 
	 * @return     double
	 */
	public function getQ2RE_NRS()
	{

		return $this->q2re_nrs;
	}

	/**
	 * Get the [q3re_surs] column value.
	 * 
	 * @return     double
	 */
	public function getQ3RE_SURS()
	{

		return $this->q3re_surs;
	}

	/**
	 * Get the [q3re_sr] column value.
	 * 
	 * @return     double
	 */
	public function getQ3RE_SR()
	{

		return $this->q3re_sr;
	}

	/**
	 * Get the [q3re_nr] column value.
	 * 
	 * @return     double
	 */
	public function getQ3RE_NR()
	{

		return $this->q3re_nr;
	}

	/**
	 * Get the [q3re_itca] column value.
	 * 
	 * @return     double
	 */
	public function getQ3RE_ITCA()
	{

		return $this->q3re_itca;
	}

	/**
	 * Get the [q3re_fea] column value.
	 * 
	 * @return     double
	 */
	public function getQ3RE_FEA()
	{

		return $this->q3re_fea;
	}

	/**
	 * Get the [q3re_neot] column value.
	 * 
	 * @return     double
	 */
	public function getQ3RE_NEOT()
	{

		return $this->q3re_neot;
	}

	/**
	 * Get the [q3re_aem] column value.
	 * 
	 * @return     double
	 */
	public function getQ3RE_AEM()
	{

		return $this->q3re_aem;
	}

	/**
	 * Get the [q3re_nrs] column value.
	 * 
	 * @return     double
	 */
	public function getQ3RE_NRS()
	{

		return $this->q3re_nrs;
	}

	/**
	 * Get the [q4re_surs] column value.
	 * 
	 * @return     double
	 */
	public function getQ4RE_SURS()
	{

		return $this->q4re_surs;
	}

	/**
	 * Get the [q4re_sr] column value.
	 * 
	 * @return     double
	 */
	public function getQ4RE_SR()
	{

		return $this->q4re_sr;
	}

	/**
	 * Get the [q4re_nr] column value.
	 * 
	 * @return     double
	 */
	public function getQ4RE_NR()
	{

		return $this->q4re_nr;
	}

	/**
	 * Get the [q4re_itca] column value.
	 * 
	 * @return     double
	 */
	public function getQ4RE_ITCA()
	{

		return $this->q4re_itca;
	}

	/**
	 * Get the [q4re_fea] column value.
	 * 
	 * @return     double
	 */
	public function getQ4RE_FEA()
	{

		return $this->q4re_fea;
	}

	/**
	 * Get the [q4re_neot] column value.
	 * 
	 * @return     double
	 */
	public function getQ4RE_NEOT()
	{

		return $this->q4re_neot;
	}

	/**
	 * Get the [q4re_aem] column value.
	 * 
	 * @return     double
	 */
	public function getQ4RE_AEM()
	{

		return $this->q4re_aem;
	}

	/**
	 * Get the [q4re_nrs] column value.
	 * 
	 * @return     double
	 */
	public function getQ4RE_NRS()
	{

		return $this->q4re_nrs;
	}

	/**
	 * Get the [pqa_surs] column value.
	 * 
	 * @return     double
	 */
	public function getPQA_SURS()
	{

		return $this->pqa_surs;
	}

	/**
	 * Get the [pqa_sr] column value.
	 * 
	 * @return     double
	 */
	public function getPQA_SR()
	{

		return $this->pqa_sr;
	}

	/**
	 * Get the [pqa_nr] column value.
	 * 
	 * @return     double
	 */
	public function getPQA_NR()
	{

		return $this->pqa_nr;
	}

	/**
	 * Get the [pqa_itca] column value.
	 * 
	 * @return     double
	 */
	public function getPQA_ITCA()
	{

		return $this->pqa_itca;
	}

	/**
	 * Get the [pqa_fea] column value.
	 * 
	 * @return     double
	 */
	public function getPQA_FEA()
	{

		return $this->pqa_fea;
	}

	/**
	 * Get the [pqa_neot] column value.
	 * 
	 * @return     double
	 */
	public function getPQA_NEOT()
	{

		return $this->pqa_neot;
	}

	/**
	 * Get the [pqa_aem] column value.
	 * 
	 * @return     double
	 */
	public function getPQA_AEM()
	{

		return $this->pqa_aem;
	}

	/**
	 * Get the [pqa_nrs] column value.
	 * 
	 * @return     double
	 */
	public function getPQA_NRS()
	{

		return $this->pqa_nrs;
	}

	/**
	 * Get the [cqe_surs] column value.
	 * 
	 * @return     double
	 */
	public function getCQE_SURS()
	{

		return $this->cqe_surs;
	}

	/**
	 * Get the [cqe_sr] column value.
	 * 
	 * @return     double
	 */
	public function getCQE_SR()
	{

		return $this->cqe_sr;
	}

	/**
	 * Get the [cqe_nr] column value.
	 * 
	 * @return     double
	 */
	public function getCQE_NR()
	{

		return $this->cqe_nr;
	}

	/**
	 * Get the [cqe_itca] column value.
	 * 
	 * @return     double
	 */
	public function getCQE_ITCA()
	{

		return $this->cqe_itca;
	}

	/**
	 * Get the [cqe_fea] column value.
	 * 
	 * @return     double
	 */
	public function getCQE_FEA()
	{

		return $this->cqe_fea;
	}

	/**
	 * Get the [cqe_neot] column value.
	 * 
	 * @return     double
	 */
	public function getCQE_NEOT()
	{

		return $this->cqe_neot;
	}

	/**
	 * Get the [cqe_aem] column value.
	 * 
	 * @return     double
	 */
	public function getCQE_AEM()
	{

		return $this->cqe_aem;
	}

	/**
	 * Get the [cqe_nrs] column value.
	 * 
	 * @return     double
	 */
	public function getCQE_NRS()
	{

		return $this->cqe_nrs;
	}

	/**
	 * Get the [supbud_sup1_p] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP1_P()
	{

		return $this->supbud_sup1_p;
	}

	/**
	 * Get the [supbud_sup1_e] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP1_E()
	{

		return $this->supbud_sup1_e;
	}

	/**
	 * Get the [supbud_sup1_psc] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP1_PSC()
	{

		return $this->supbud_sup1_psc;
	}

	/**
	 * Get the [supbud_sup1_odc] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP1_ODC()
	{

		return $this->supbud_sup1_odc;
	}

	/**
	 * Get the [supbud_sup1_ic] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP1_IC()
	{

		return $this->supbud_sup1_ic;
	}

	/**
	 * Get the [supbud_sup1_sa] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP1_SA()
	{

		return $this->supbud_sup1_sa;
	}

	/**
	 * Get the [supbud_sup2_p] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP2_P()
	{

		return $this->supbud_sup2_p;
	}

	/**
	 * Get the [supbud_sup2_e] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP2_E()
	{

		return $this->supbud_sup2_e;
	}

	/**
	 * Get the [supbud_sup2_psc] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP2_PSC()
	{

		return $this->supbud_sup2_psc;
	}

	/**
	 * Get the [supbud_sup2_odc] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP2_ODC()
	{

		return $this->supbud_sup2_odc;
	}

	/**
	 * Get the [supbud_sup2_ic] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP2_IC()
	{

		return $this->supbud_sup2_ic;
	}

	/**
	 * Get the [supbud_sup2_sa] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP2_SA()
	{

		return $this->supbud_sup2_sa;
	}

	/**
	 * Get the [supbud_sup3_p] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP3_P()
	{

		return $this->supbud_sup3_p;
	}

	/**
	 * Get the [supbud_sup3_e] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP3_E()
	{

		return $this->supbud_sup3_e;
	}

	/**
	 * Get the [supbud_sup3_psc] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP3_PSC()
	{

		return $this->supbud_sup3_psc;
	}

	/**
	 * Get the [supbud_sup3_odc] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP3_ODC()
	{

		return $this->supbud_sup3_odc;
	}

	/**
	 * Get the [supbud_sup3_ic] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP3_IC()
	{

		return $this->supbud_sup3_ic;
	}

	/**
	 * Get the [supbud_sup3_sa] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP3_SA()
	{

		return $this->supbud_sup3_sa;
	}

	/**
	 * Get the [supbud_sup4_p] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP4_P()
	{

		return $this->supbud_sup4_p;
	}

	/**
	 * Get the [supbud_sup4_e] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP4_E()
	{

		return $this->supbud_sup4_e;
	}

	/**
	 * Get the [supbud_sup4_psc] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP4_PSC()
	{

		return $this->supbud_sup4_psc;
	}

	/**
	 * Get the [supbud_sup4_odc] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP4_ODC()
	{

		return $this->supbud_sup4_odc;
	}

	/**
	 * Get the [supbud_sup4_ic] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP4_IC()
	{

		return $this->supbud_sup4_ic;
	}

	/**
	 * Get the [supbud_sup4_sa] column value.
	 * 
	 * @return     double
	 */
	public function getSUPBUD_SUP4_SA()
	{

		return $this->supbud_sup4_sa;
	}

	/**
	 * Get the [pi_beg_bal] column value.
	 * 
	 * @return     double
	 */
	public function getPI_BEG_BAL()
	{

		return $this->pi_beg_bal;
	}

	/**
	 * Get the [pi_pir] column value.
	 * 
	 * @return     double
	 */
	public function getPI_PIR()
	{

		return $this->pi_pir;
	}

	/**
	 * Get the [pi_pie] column value.
	 * 
	 * @return     double
	 */
	public function getPI_PIE()
	{

		return $this->pi_pie;
	}

	/**
	 * Get the [pi_nar] column value.
	 * 
	 * @return     string
	 */
	public function getPI_NAR()
	{

		return $this->pi_nar;
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
			$this->modifiedColumns[] = SiteReportsQFRPeer::ID;
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
			$this->modifiedColumns[] = SiteReportsQFRPeer::FACILITY_ID;
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
			$this->modifiedColumns[] = SiteReportsQFRPeer::YEAR;
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
			$this->modifiedColumns[] = SiteReportsQFRPeer::QUARTER;
		}

	} // setQUARTER()

	/**
	 * Set the value of [prepared_by] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPREPARED_BY($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->prepared_by !== $v) {
			$this->prepared_by = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PREPARED_BY;
		}

	} // setPREPARED_BY()

	/**
	 * Set the value of [preparers_title] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPREPARERS_TITLE($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->preparers_title !== $v) {
			$this->preparers_title = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PREPARERS_TITLE;
		}

	} // setPREPARERS_TITLE()

	/**
	 * Set the value of [prepared_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setPREPARED_DATE($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [prepared_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->prepared_date !== $ts) {
			$this->prepared_date = $ts;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PREPARED_DATE;
		}

	} // setPREPARED_DATE()

	/**
	 * Set the value of [report_period] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setREPORT_PERIOD($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->report_period !== $v) {
			$this->report_period = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::REPORT_PERIOD;
		}

	} // setREPORT_PERIOD()

	/**
	 * Set the value of [subawarded_funded_amt] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUBAWARDED_FUNDED_AMT($v)
	{

		if ($this->subawarded_funded_amt !== $v) {
			$this->subawarded_funded_amt = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUBAWARDED_FUNDED_AMT;
		}

	} // setSUBAWARDED_FUNDED_AMT()

	/**
	 * Set the value of [qfr_sr_p_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_SR_P_COST($v)
	{

		if ($this->qfr_sr_p_cost !== $v) {
			$this->qfr_sr_p_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_SR_P_COST;
		}

	} // setQFR_SR_P_COST()

	/**
	 * Set the value of [qfr_sr_e_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_SR_E_COST($v)
	{

		if ($this->qfr_sr_e_cost !== $v) {
			$this->qfr_sr_e_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_SR_E_COST;
		}

	} // setQFR_SR_E_COST()

	/**
	 * Set the value of [qfr_sr_psc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_SR_PSC_COST($v)
	{

		if ($this->qfr_sr_psc_cost !== $v) {
			$this->qfr_sr_psc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_SR_PSC_COST;
		}

	} // setQFR_SR_PSC_COST()

	/**
	 * Set the value of [qfr_sr_odc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_SR_ODC_COST($v)
	{

		if ($this->qfr_sr_odc_cost !== $v) {
			$this->qfr_sr_odc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_SR_ODC_COST;
		}

	} // setQFR_SR_ODC_COST()

	/**
	 * Set the value of [qfr_sr_ic_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_SR_IC_COST($v)
	{

		if ($this->qfr_sr_ic_cost !== $v) {
			$this->qfr_sr_ic_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_SR_IC_COST;
		}

	} // setQFR_SR_IC_COST()

	/**
	 * Set the value of [qfr_nr_p_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NR_P_COST($v)
	{

		if ($this->qfr_nr_p_cost !== $v) {
			$this->qfr_nr_p_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NR_P_COST;
		}

	} // setQFR_NR_P_COST()

	/**
	 * Set the value of [qfr_nr_e_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NR_E_COST($v)
	{

		if ($this->qfr_nr_e_cost !== $v) {
			$this->qfr_nr_e_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NR_E_COST;
		}

	} // setQFR_NR_E_COST()

	/**
	 * Set the value of [qfr_nr_psc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NR_PSC_COST($v)
	{

		if ($this->qfr_nr_psc_cost !== $v) {
			$this->qfr_nr_psc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NR_PSC_COST;
		}

	} // setQFR_NR_PSC_COST()

	/**
	 * Set the value of [qfr_nr_odc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NR_ODC_COST($v)
	{

		if ($this->qfr_nr_odc_cost !== $v) {
			$this->qfr_nr_odc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NR_ODC_COST;
		}

	} // setQFR_NR_ODC_COST()

	/**
	 * Set the value of [qfr_nr_ic_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NR_IC_COST($v)
	{

		if ($this->qfr_nr_ic_cost !== $v) {
			$this->qfr_nr_ic_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NR_IC_COST;
		}

	} // setQFR_NR_IC_COST()

	/**
	 * Set the value of [qfr_itca_p_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_ITCA_P_COST($v)
	{

		if ($this->qfr_itca_p_cost !== $v) {
			$this->qfr_itca_p_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_ITCA_P_COST;
		}

	} // setQFR_ITCA_P_COST()

	/**
	 * Set the value of [qfr_itca_e_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_ITCA_E_COST($v)
	{

		if ($this->qfr_itca_e_cost !== $v) {
			$this->qfr_itca_e_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_ITCA_E_COST;
		}

	} // setQFR_ITCA_E_COST()

	/**
	 * Set the value of [qfr_itca_psc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_ITCA_PSC_COST($v)
	{

		if ($this->qfr_itca_psc_cost !== $v) {
			$this->qfr_itca_psc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_ITCA_PSC_COST;
		}

	} // setQFR_ITCA_PSC_COST()

	/**
	 * Set the value of [qfr_itca_odc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_ITCA_ODC_COST($v)
	{

		if ($this->qfr_itca_odc_cost !== $v) {
			$this->qfr_itca_odc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_ITCA_ODC_COST;
		}

	} // setQFR_ITCA_ODC_COST()

	/**
	 * Set the value of [qfr_itca_ic_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_ITCA_IC_COST($v)
	{

		if ($this->qfr_itca_ic_cost !== $v) {
			$this->qfr_itca_ic_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_ITCA_IC_COST;
		}

	} // setQFR_ITCA_IC_COST()

	/**
	 * Set the value of [qfr_neot_p_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NEOT_P_COST($v)
	{

		if ($this->qfr_neot_p_cost !== $v) {
			$this->qfr_neot_p_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NEOT_P_COST;
		}

	} // setQFR_NEOT_P_COST()

	/**
	 * Set the value of [qfr_neot_e_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NEOT_E_COST($v)
	{

		if ($this->qfr_neot_e_cost !== $v) {
			$this->qfr_neot_e_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NEOT_E_COST;
		}

	} // setQFR_NEOT_E_COST()

	/**
	 * Set the value of [qfr_neot_psc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NEOT_PSC_COST($v)
	{

		if ($this->qfr_neot_psc_cost !== $v) {
			$this->qfr_neot_psc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NEOT_PSC_COST;
		}

	} // setQFR_NEOT_PSC_COST()

	/**
	 * Set the value of [qfr_neot_odc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NEOT_ODC_COST($v)
	{

		if ($this->qfr_neot_odc_cost !== $v) {
			$this->qfr_neot_odc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NEOT_ODC_COST;
		}

	} // setQFR_NEOT_ODC_COST()

	/**
	 * Set the value of [qfr_neot_ic_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NEOT_IC_COST($v)
	{

		if ($this->qfr_neot_ic_cost !== $v) {
			$this->qfr_neot_ic_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NEOT_IC_COST;
		}

	} // setQFR_NEOT_IC_COST()

	/**
	 * Set the value of [qfr_fea_p_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_FEA_P_COST($v)
	{

		if ($this->qfr_fea_p_cost !== $v) {
			$this->qfr_fea_p_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_FEA_P_COST;
		}

	} // setQFR_FEA_P_COST()

	/**
	 * Set the value of [qfr_fea_e_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_FEA_E_COST($v)
	{

		if ($this->qfr_fea_e_cost !== $v) {
			$this->qfr_fea_e_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_FEA_E_COST;
		}

	} // setQFR_FEA_E_COST()

	/**
	 * Set the value of [qfr_fea_psc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_FEA_PSC_COST($v)
	{

		if ($this->qfr_fea_psc_cost !== $v) {
			$this->qfr_fea_psc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_FEA_PSC_COST;
		}

	} // setQFR_FEA_PSC_COST()

	/**
	 * Set the value of [qfr_fea_odc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_FEA_ODC_COST($v)
	{

		if ($this->qfr_fea_odc_cost !== $v) {
			$this->qfr_fea_odc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_FEA_ODC_COST;
		}

	} // setQFR_FEA_ODC_COST()

	/**
	 * Set the value of [qfr_fea_ic_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_FEA_IC_COST($v)
	{

		if ($this->qfr_fea_ic_cost !== $v) {
			$this->qfr_fea_ic_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_FEA_IC_COST;
		}

	} // setQFR_FEA_IC_COST()

	/**
	 * Set the value of [qfr_aem_p_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_AEM_P_COST($v)
	{

		if ($this->qfr_aem_p_cost !== $v) {
			$this->qfr_aem_p_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_AEM_P_COST;
		}

	} // setQFR_AEM_P_COST()

	/**
	 * Set the value of [qfr_aem_e_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_AEM_E_COST($v)
	{

		if ($this->qfr_aem_e_cost !== $v) {
			$this->qfr_aem_e_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_AEM_E_COST;
		}

	} // setQFR_AEM_E_COST()

	/**
	 * Set the value of [qfr_aem_psc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_AEM_PSC_COST($v)
	{

		if ($this->qfr_aem_psc_cost !== $v) {
			$this->qfr_aem_psc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_AEM_PSC_COST;
		}

	} // setQFR_AEM_PSC_COST()

	/**
	 * Set the value of [qfr_aem_odc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_AEM_ODC_COST($v)
	{

		if ($this->qfr_aem_odc_cost !== $v) {
			$this->qfr_aem_odc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_AEM_ODC_COST;
		}

	} // setQFR_AEM_ODC_COST()

	/**
	 * Set the value of [qfr_aem_ic_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_AEM_IC_COST($v)
	{

		if ($this->qfr_aem_ic_cost !== $v) {
			$this->qfr_aem_ic_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_AEM_IC_COST;
		}

	} // setQFR_AEM_IC_COST()

	/**
	 * Set the value of [qfr_nrs_p_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NRS_P_COST($v)
	{

		if ($this->qfr_nrs_p_cost !== $v) {
			$this->qfr_nrs_p_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NRS_P_COST;
		}

	} // setQFR_NRS_P_COST()

	/**
	 * Set the value of [qfr_nrs_e_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NRS_E_COST($v)
	{

		if ($this->qfr_nrs_e_cost !== $v) {
			$this->qfr_nrs_e_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NRS_E_COST;
		}

	} // setQFR_NRS_E_COST()

	/**
	 * Set the value of [qfr_nrs_psc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NRS_PSC_COST($v)
	{

		if ($this->qfr_nrs_psc_cost !== $v) {
			$this->qfr_nrs_psc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NRS_PSC_COST;
		}

	} // setQFR_NRS_PSC_COST()

	/**
	 * Set the value of [qfr_nrs_odc_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NRS_ODC_COST($v)
	{

		if ($this->qfr_nrs_odc_cost !== $v) {
			$this->qfr_nrs_odc_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NRS_ODC_COST;
		}

	} // setQFR_NRS_ODC_COST()

	/**
	 * Set the value of [qfr_nrs_ic_cost] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQFR_NRS_IC_COST($v)
	{

		if ($this->qfr_nrs_ic_cost !== $v) {
			$this->qfr_nrs_ic_cost = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::QFR_NRS_IC_COST;
		}

	} // setQFR_NRS_IC_COST()

	/**
	 * Set the value of [fy_budget_surs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFY_BUDGET_SURS($v)
	{

		if ($this->fy_budget_surs !== $v) {
			$this->fy_budget_surs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::FY_BUDGET_SURS;
		}

	} // setFY_BUDGET_SURS()

	/**
	 * Set the value of [fy_budget_sr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFY_BUDGET_SR($v)
	{

		if ($this->fy_budget_sr !== $v) {
			$this->fy_budget_sr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::FY_BUDGET_SR;
		}

	} // setFY_BUDGET_SR()

	/**
	 * Set the value of [fy_budget_nr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFY_BUDGET_NR($v)
	{

		if ($this->fy_budget_nr !== $v) {
			$this->fy_budget_nr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::FY_BUDGET_NR;
		}

	} // setFY_BUDGET_NR()

	/**
	 * Set the value of [fy_budget_itca] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFY_BUDGET_ITCA($v)
	{

		if ($this->fy_budget_itca !== $v) {
			$this->fy_budget_itca = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::FY_BUDGET_ITCA;
		}

	} // setFY_BUDGET_ITCA()

	/**
	 * Set the value of [fy_budget_fea] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFY_BUDGET_FEA($v)
	{

		if ($this->fy_budget_fea !== $v) {
			$this->fy_budget_fea = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::FY_BUDGET_FEA;
		}

	} // setFY_BUDGET_FEA()

	/**
	 * Set the value of [fy_budget_neot] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFY_BUDGET_NEOT($v)
	{

		if ($this->fy_budget_neot !== $v) {
			$this->fy_budget_neot = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::FY_BUDGET_NEOT;
		}

	} // setFY_BUDGET_NEOT()

	/**
	 * Set the value of [fy_budget_aem] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFY_BUDGET_AEM($v)
	{

		if ($this->fy_budget_aem !== $v) {
			$this->fy_budget_aem = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::FY_BUDGET_AEM;
		}

	} // setFY_BUDGET_AEM()

	/**
	 * Set the value of [fy_budget_nrs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFY_BUDGET_NRS($v)
	{

		if ($this->fy_budget_nrs !== $v) {
			$this->fy_budget_nrs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::FY_BUDGET_NRS;
		}

	} // setFY_BUDGET_NRS()

	/**
	 * Set the value of [q1re_surs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ1RE_SURS($v)
	{

		if ($this->q1re_surs !== $v) {
			$this->q1re_surs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q1RE_SURS;
		}

	} // setQ1RE_SURS()

	/**
	 * Set the value of [q1re_sr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ1RE_SR($v)
	{

		if ($this->q1re_sr !== $v) {
			$this->q1re_sr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q1RE_SR;
		}

	} // setQ1RE_SR()

	/**
	 * Set the value of [q1re_nr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ1RE_NR($v)
	{

		if ($this->q1re_nr !== $v) {
			$this->q1re_nr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q1RE_NR;
		}

	} // setQ1RE_NR()

	/**
	 * Set the value of [q1re_itca] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ1RE_ITCA($v)
	{

		if ($this->q1re_itca !== $v) {
			$this->q1re_itca = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q1RE_ITCA;
		}

	} // setQ1RE_ITCA()

	/**
	 * Set the value of [q1re_fea] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ1RE_FEA($v)
	{

		if ($this->q1re_fea !== $v) {
			$this->q1re_fea = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q1RE_FEA;
		}

	} // setQ1RE_FEA()

	/**
	 * Set the value of [q1re_neot] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ1RE_NEOT($v)
	{

		if ($this->q1re_neot !== $v) {
			$this->q1re_neot = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q1RE_NEOT;
		}

	} // setQ1RE_NEOT()

	/**
	 * Set the value of [q1re_aem] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ1RE_AEM($v)
	{

		if ($this->q1re_aem !== $v) {
			$this->q1re_aem = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q1RE_AEM;
		}

	} // setQ1RE_AEM()

	/**
	 * Set the value of [q1re_nrs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ1RE_NRS($v)
	{

		if ($this->q1re_nrs !== $v) {
			$this->q1re_nrs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q1RE_NRS;
		}

	} // setQ1RE_NRS()

	/**
	 * Set the value of [q2re_surs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ2RE_SURS($v)
	{

		if ($this->q2re_surs !== $v) {
			$this->q2re_surs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q2RE_SURS;
		}

	} // setQ2RE_SURS()

	/**
	 * Set the value of [q2re_sr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ2RE_SR($v)
	{

		if ($this->q2re_sr !== $v) {
			$this->q2re_sr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q2RE_SR;
		}

	} // setQ2RE_SR()

	/**
	 * Set the value of [q2re_nr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ2RE_NR($v)
	{

		if ($this->q2re_nr !== $v) {
			$this->q2re_nr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q2RE_NR;
		}

	} // setQ2RE_NR()

	/**
	 * Set the value of [q2re_itca] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ2RE_ITCA($v)
	{

		if ($this->q2re_itca !== $v) {
			$this->q2re_itca = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q2RE_ITCA;
		}

	} // setQ2RE_ITCA()

	/**
	 * Set the value of [q2re_fea] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ2RE_FEA($v)
	{

		if ($this->q2re_fea !== $v) {
			$this->q2re_fea = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q2RE_FEA;
		}

	} // setQ2RE_FEA()

	/**
	 * Set the value of [q2re_neot] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ2RE_NEOT($v)
	{

		if ($this->q2re_neot !== $v) {
			$this->q2re_neot = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q2RE_NEOT;
		}

	} // setQ2RE_NEOT()

	/**
	 * Set the value of [q2re_aem] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ2RE_AEM($v)
	{

		if ($this->q2re_aem !== $v) {
			$this->q2re_aem = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q2RE_AEM;
		}

	} // setQ2RE_AEM()

	/**
	 * Set the value of [q2re_nrs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ2RE_NRS($v)
	{

		if ($this->q2re_nrs !== $v) {
			$this->q2re_nrs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q2RE_NRS;
		}

	} // setQ2RE_NRS()

	/**
	 * Set the value of [q3re_surs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ3RE_SURS($v)
	{

		if ($this->q3re_surs !== $v) {
			$this->q3re_surs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q3RE_SURS;
		}

	} // setQ3RE_SURS()

	/**
	 * Set the value of [q3re_sr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ3RE_SR($v)
	{

		if ($this->q3re_sr !== $v) {
			$this->q3re_sr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q3RE_SR;
		}

	} // setQ3RE_SR()

	/**
	 * Set the value of [q3re_nr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ3RE_NR($v)
	{

		if ($this->q3re_nr !== $v) {
			$this->q3re_nr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q3RE_NR;
		}

	} // setQ3RE_NR()

	/**
	 * Set the value of [q3re_itca] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ3RE_ITCA($v)
	{

		if ($this->q3re_itca !== $v) {
			$this->q3re_itca = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q3RE_ITCA;
		}

	} // setQ3RE_ITCA()

	/**
	 * Set the value of [q3re_fea] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ3RE_FEA($v)
	{

		if ($this->q3re_fea !== $v) {
			$this->q3re_fea = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q3RE_FEA;
		}

	} // setQ3RE_FEA()

	/**
	 * Set the value of [q3re_neot] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ3RE_NEOT($v)
	{

		if ($this->q3re_neot !== $v) {
			$this->q3re_neot = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q3RE_NEOT;
		}

	} // setQ3RE_NEOT()

	/**
	 * Set the value of [q3re_aem] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ3RE_AEM($v)
	{

		if ($this->q3re_aem !== $v) {
			$this->q3re_aem = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q3RE_AEM;
		}

	} // setQ3RE_AEM()

	/**
	 * Set the value of [q3re_nrs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ3RE_NRS($v)
	{

		if ($this->q3re_nrs !== $v) {
			$this->q3re_nrs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q3RE_NRS;
		}

	} // setQ3RE_NRS()

	/**
	 * Set the value of [q4re_surs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ4RE_SURS($v)
	{

		if ($this->q4re_surs !== $v) {
			$this->q4re_surs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q4RE_SURS;
		}

	} // setQ4RE_SURS()

	/**
	 * Set the value of [q4re_sr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ4RE_SR($v)
	{

		if ($this->q4re_sr !== $v) {
			$this->q4re_sr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q4RE_SR;
		}

	} // setQ4RE_SR()

	/**
	 * Set the value of [q4re_nr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ4RE_NR($v)
	{

		if ($this->q4re_nr !== $v) {
			$this->q4re_nr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q4RE_NR;
		}

	} // setQ4RE_NR()

	/**
	 * Set the value of [q4re_itca] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ4RE_ITCA($v)
	{

		if ($this->q4re_itca !== $v) {
			$this->q4re_itca = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q4RE_ITCA;
		}

	} // setQ4RE_ITCA()

	/**
	 * Set the value of [q4re_fea] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ4RE_FEA($v)
	{

		if ($this->q4re_fea !== $v) {
			$this->q4re_fea = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q4RE_FEA;
		}

	} // setQ4RE_FEA()

	/**
	 * Set the value of [q4re_neot] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ4RE_NEOT($v)
	{

		if ($this->q4re_neot !== $v) {
			$this->q4re_neot = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q4RE_NEOT;
		}

	} // setQ4RE_NEOT()

	/**
	 * Set the value of [q4re_aem] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ4RE_AEM($v)
	{

		if ($this->q4re_aem !== $v) {
			$this->q4re_aem = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q4RE_AEM;
		}

	} // setQ4RE_AEM()

	/**
	 * Set the value of [q4re_nrs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setQ4RE_NRS($v)
	{

		if ($this->q4re_nrs !== $v) {
			$this->q4re_nrs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::Q4RE_NRS;
		}

	} // setQ4RE_NRS()

	/**
	 * Set the value of [pqa_surs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPQA_SURS($v)
	{

		if ($this->pqa_surs !== $v) {
			$this->pqa_surs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PQA_SURS;
		}

	} // setPQA_SURS()

	/**
	 * Set the value of [pqa_sr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPQA_SR($v)
	{

		if ($this->pqa_sr !== $v) {
			$this->pqa_sr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PQA_SR;
		}

	} // setPQA_SR()

	/**
	 * Set the value of [pqa_nr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPQA_NR($v)
	{

		if ($this->pqa_nr !== $v) {
			$this->pqa_nr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PQA_NR;
		}

	} // setPQA_NR()

	/**
	 * Set the value of [pqa_itca] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPQA_ITCA($v)
	{

		if ($this->pqa_itca !== $v) {
			$this->pqa_itca = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PQA_ITCA;
		}

	} // setPQA_ITCA()

	/**
	 * Set the value of [pqa_fea] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPQA_FEA($v)
	{

		if ($this->pqa_fea !== $v) {
			$this->pqa_fea = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PQA_FEA;
		}

	} // setPQA_FEA()

	/**
	 * Set the value of [pqa_neot] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPQA_NEOT($v)
	{

		if ($this->pqa_neot !== $v) {
			$this->pqa_neot = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PQA_NEOT;
		}

	} // setPQA_NEOT()

	/**
	 * Set the value of [pqa_aem] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPQA_AEM($v)
	{

		if ($this->pqa_aem !== $v) {
			$this->pqa_aem = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PQA_AEM;
		}

	} // setPQA_AEM()

	/**
	 * Set the value of [pqa_nrs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPQA_NRS($v)
	{

		if ($this->pqa_nrs !== $v) {
			$this->pqa_nrs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PQA_NRS;
		}

	} // setPQA_NRS()

	/**
	 * Set the value of [cqe_surs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCQE_SURS($v)
	{

		if ($this->cqe_surs !== $v) {
			$this->cqe_surs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::CQE_SURS;
		}

	} // setCQE_SURS()

	/**
	 * Set the value of [cqe_sr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCQE_SR($v)
	{

		if ($this->cqe_sr !== $v) {
			$this->cqe_sr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::CQE_SR;
		}

	} // setCQE_SR()

	/**
	 * Set the value of [cqe_nr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCQE_NR($v)
	{

		if ($this->cqe_nr !== $v) {
			$this->cqe_nr = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::CQE_NR;
		}

	} // setCQE_NR()

	/**
	 * Set the value of [cqe_itca] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCQE_ITCA($v)
	{

		if ($this->cqe_itca !== $v) {
			$this->cqe_itca = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::CQE_ITCA;
		}

	} // setCQE_ITCA()

	/**
	 * Set the value of [cqe_fea] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCQE_FEA($v)
	{

		if ($this->cqe_fea !== $v) {
			$this->cqe_fea = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::CQE_FEA;
		}

	} // setCQE_FEA()

	/**
	 * Set the value of [cqe_neot] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCQE_NEOT($v)
	{

		if ($this->cqe_neot !== $v) {
			$this->cqe_neot = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::CQE_NEOT;
		}

	} // setCQE_NEOT()

	/**
	 * Set the value of [cqe_aem] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCQE_AEM($v)
	{

		if ($this->cqe_aem !== $v) {
			$this->cqe_aem = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::CQE_AEM;
		}

	} // setCQE_AEM()

	/**
	 * Set the value of [cqe_nrs] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCQE_NRS($v)
	{

		if ($this->cqe_nrs !== $v) {
			$this->cqe_nrs = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::CQE_NRS;
		}

	} // setCQE_NRS()

	/**
	 * Set the value of [supbud_sup1_p] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP1_P($v)
	{

		if ($this->supbud_sup1_p !== $v) {
			$this->supbud_sup1_p = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP1_P;
		}

	} // setSUPBUD_SUP1_P()

	/**
	 * Set the value of [supbud_sup1_e] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP1_E($v)
	{

		if ($this->supbud_sup1_e !== $v) {
			$this->supbud_sup1_e = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP1_E;
		}

	} // setSUPBUD_SUP1_E()

	/**
	 * Set the value of [supbud_sup1_psc] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP1_PSC($v)
	{

		if ($this->supbud_sup1_psc !== $v) {
			$this->supbud_sup1_psc = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP1_PSC;
		}

	} // setSUPBUD_SUP1_PSC()

	/**
	 * Set the value of [supbud_sup1_odc] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP1_ODC($v)
	{

		if ($this->supbud_sup1_odc !== $v) {
			$this->supbud_sup1_odc = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP1_ODC;
		}

	} // setSUPBUD_SUP1_ODC()

	/**
	 * Set the value of [supbud_sup1_ic] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP1_IC($v)
	{

		if ($this->supbud_sup1_ic !== $v) {
			$this->supbud_sup1_ic = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP1_IC;
		}

	} // setSUPBUD_SUP1_IC()

	/**
	 * Set the value of [supbud_sup1_sa] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP1_SA($v)
	{

		if ($this->supbud_sup1_sa !== $v) {
			$this->supbud_sup1_sa = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP1_SA;
		}

	} // setSUPBUD_SUP1_SA()

	/**
	 * Set the value of [supbud_sup2_p] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP2_P($v)
	{

		if ($this->supbud_sup2_p !== $v) {
			$this->supbud_sup2_p = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP2_P;
		}

	} // setSUPBUD_SUP2_P()

	/**
	 * Set the value of [supbud_sup2_e] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP2_E($v)
	{

		if ($this->supbud_sup2_e !== $v) {
			$this->supbud_sup2_e = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP2_E;
		}

	} // setSUPBUD_SUP2_E()

	/**
	 * Set the value of [supbud_sup2_psc] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP2_PSC($v)
	{

		if ($this->supbud_sup2_psc !== $v) {
			$this->supbud_sup2_psc = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP2_PSC;
		}

	} // setSUPBUD_SUP2_PSC()

	/**
	 * Set the value of [supbud_sup2_odc] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP2_ODC($v)
	{

		if ($this->supbud_sup2_odc !== $v) {
			$this->supbud_sup2_odc = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP2_ODC;
		}

	} // setSUPBUD_SUP2_ODC()

	/**
	 * Set the value of [supbud_sup2_ic] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP2_IC($v)
	{

		if ($this->supbud_sup2_ic !== $v) {
			$this->supbud_sup2_ic = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP2_IC;
		}

	} // setSUPBUD_SUP2_IC()

	/**
	 * Set the value of [supbud_sup2_sa] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP2_SA($v)
	{

		if ($this->supbud_sup2_sa !== $v) {
			$this->supbud_sup2_sa = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP2_SA;
		}

	} // setSUPBUD_SUP2_SA()

	/**
	 * Set the value of [supbud_sup3_p] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP3_P($v)
	{

		if ($this->supbud_sup3_p !== $v) {
			$this->supbud_sup3_p = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP3_P;
		}

	} // setSUPBUD_SUP3_P()

	/**
	 * Set the value of [supbud_sup3_e] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP3_E($v)
	{

		if ($this->supbud_sup3_e !== $v) {
			$this->supbud_sup3_e = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP3_E;
		}

	} // setSUPBUD_SUP3_E()

	/**
	 * Set the value of [supbud_sup3_psc] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP3_PSC($v)
	{

		if ($this->supbud_sup3_psc !== $v) {
			$this->supbud_sup3_psc = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP3_PSC;
		}

	} // setSUPBUD_SUP3_PSC()

	/**
	 * Set the value of [supbud_sup3_odc] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP3_ODC($v)
	{

		if ($this->supbud_sup3_odc !== $v) {
			$this->supbud_sup3_odc = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP3_ODC;
		}

	} // setSUPBUD_SUP3_ODC()

	/**
	 * Set the value of [supbud_sup3_ic] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP3_IC($v)
	{

		if ($this->supbud_sup3_ic !== $v) {
			$this->supbud_sup3_ic = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP3_IC;
		}

	} // setSUPBUD_SUP3_IC()

	/**
	 * Set the value of [supbud_sup3_sa] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP3_SA($v)
	{

		if ($this->supbud_sup3_sa !== $v) {
			$this->supbud_sup3_sa = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP3_SA;
		}

	} // setSUPBUD_SUP3_SA()

	/**
	 * Set the value of [supbud_sup4_p] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP4_P($v)
	{

		if ($this->supbud_sup4_p !== $v) {
			$this->supbud_sup4_p = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP4_P;
		}

	} // setSUPBUD_SUP4_P()

	/**
	 * Set the value of [supbud_sup4_e] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP4_E($v)
	{

		if ($this->supbud_sup4_e !== $v) {
			$this->supbud_sup4_e = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP4_E;
		}

	} // setSUPBUD_SUP4_E()

	/**
	 * Set the value of [supbud_sup4_psc] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP4_PSC($v)
	{

		if ($this->supbud_sup4_psc !== $v) {
			$this->supbud_sup4_psc = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP4_PSC;
		}

	} // setSUPBUD_SUP4_PSC()

	/**
	 * Set the value of [supbud_sup4_odc] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP4_ODC($v)
	{

		if ($this->supbud_sup4_odc !== $v) {
			$this->supbud_sup4_odc = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP4_ODC;
		}

	} // setSUPBUD_SUP4_ODC()

	/**
	 * Set the value of [supbud_sup4_ic] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP4_IC($v)
	{

		if ($this->supbud_sup4_ic !== $v) {
			$this->supbud_sup4_ic = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP4_IC;
		}

	} // setSUPBUD_SUP4_IC()

	/**
	 * Set the value of [supbud_sup4_sa] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSUPBUD_SUP4_SA($v)
	{

		if ($this->supbud_sup4_sa !== $v) {
			$this->supbud_sup4_sa = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::SUPBUD_SUP4_SA;
		}

	} // setSUPBUD_SUP4_SA()

	/**
	 * Set the value of [pi_beg_bal] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPI_BEG_BAL($v)
	{

		if ($this->pi_beg_bal !== $v) {
			$this->pi_beg_bal = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PI_BEG_BAL;
		}

	} // setPI_BEG_BAL()

	/**
	 * Set the value of [pi_pir] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPI_PIR($v)
	{

		if ($this->pi_pir !== $v) {
			$this->pi_pir = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PI_PIR;
		}

	} // setPI_PIR()

	/**
	 * Set the value of [pi_pie] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPI_PIE($v)
	{

		if ($this->pi_pie !== $v) {
			$this->pi_pie = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PI_PIE;
		}

	} // setPI_PIE()

	/**
	 * Set the value of [pi_nar] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPI_NAR($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->pi_nar !== $v) {
			$this->pi_nar = $v;
			$this->modifiedColumns[] = SiteReportsQFRPeer::PI_NAR;
		}

	} // setPI_NAR()

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
			$this->modifiedColumns[] = SiteReportsQFRPeer::CREATED_BY;
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
			$this->modifiedColumns[] = SiteReportsQFRPeer::CREATED_ON;
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
			$this->modifiedColumns[] = SiteReportsQFRPeer::UPDATED_BY;
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
			$this->modifiedColumns[] = SiteReportsQFRPeer::UPDATED_ON;
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

			$this->prepared_by = $rs->getString($startcol + 4);

			$this->preparers_title = $rs->getString($startcol + 5);

			$this->prepared_date = $rs->getDate($startcol + 6, null);

			$this->report_period = $rs->getString($startcol + 7);

			$this->subawarded_funded_amt = $rs->getFloat($startcol + 8);

			$this->qfr_sr_p_cost = $rs->getFloat($startcol + 9);

			$this->qfr_sr_e_cost = $rs->getFloat($startcol + 10);

			$this->qfr_sr_psc_cost = $rs->getFloat($startcol + 11);

			$this->qfr_sr_odc_cost = $rs->getFloat($startcol + 12);

			$this->qfr_sr_ic_cost = $rs->getFloat($startcol + 13);

			$this->qfr_nr_p_cost = $rs->getFloat($startcol + 14);

			$this->qfr_nr_e_cost = $rs->getFloat($startcol + 15);

			$this->qfr_nr_psc_cost = $rs->getFloat($startcol + 16);

			$this->qfr_nr_odc_cost = $rs->getFloat($startcol + 17);

			$this->qfr_nr_ic_cost = $rs->getFloat($startcol + 18);

			$this->qfr_itca_p_cost = $rs->getFloat($startcol + 19);

			$this->qfr_itca_e_cost = $rs->getFloat($startcol + 20);

			$this->qfr_itca_psc_cost = $rs->getFloat($startcol + 21);

			$this->qfr_itca_odc_cost = $rs->getFloat($startcol + 22);

			$this->qfr_itca_ic_cost = $rs->getFloat($startcol + 23);

			$this->qfr_neot_p_cost = $rs->getFloat($startcol + 24);

			$this->qfr_neot_e_cost = $rs->getFloat($startcol + 25);

			$this->qfr_neot_psc_cost = $rs->getFloat($startcol + 26);

			$this->qfr_neot_odc_cost = $rs->getFloat($startcol + 27);

			$this->qfr_neot_ic_cost = $rs->getFloat($startcol + 28);

			$this->qfr_fea_p_cost = $rs->getFloat($startcol + 29);

			$this->qfr_fea_e_cost = $rs->getFloat($startcol + 30);

			$this->qfr_fea_psc_cost = $rs->getFloat($startcol + 31);

			$this->qfr_fea_odc_cost = $rs->getFloat($startcol + 32);

			$this->qfr_fea_ic_cost = $rs->getFloat($startcol + 33);

			$this->qfr_aem_p_cost = $rs->getFloat($startcol + 34);

			$this->qfr_aem_e_cost = $rs->getFloat($startcol + 35);

			$this->qfr_aem_psc_cost = $rs->getFloat($startcol + 36);

			$this->qfr_aem_odc_cost = $rs->getFloat($startcol + 37);

			$this->qfr_aem_ic_cost = $rs->getFloat($startcol + 38);

			$this->qfr_nrs_p_cost = $rs->getFloat($startcol + 39);

			$this->qfr_nrs_e_cost = $rs->getFloat($startcol + 40);

			$this->qfr_nrs_psc_cost = $rs->getFloat($startcol + 41);

			$this->qfr_nrs_odc_cost = $rs->getFloat($startcol + 42);

			$this->qfr_nrs_ic_cost = $rs->getFloat($startcol + 43);

			$this->fy_budget_surs = $rs->getFloat($startcol + 44);

			$this->fy_budget_sr = $rs->getFloat($startcol + 45);

			$this->fy_budget_nr = $rs->getFloat($startcol + 46);

			$this->fy_budget_itca = $rs->getFloat($startcol + 47);

			$this->fy_budget_fea = $rs->getFloat($startcol + 48);

			$this->fy_budget_neot = $rs->getFloat($startcol + 49);

			$this->fy_budget_aem = $rs->getFloat($startcol + 50);

			$this->fy_budget_nrs = $rs->getFloat($startcol + 51);

			$this->q1re_surs = $rs->getFloat($startcol + 52);

			$this->q1re_sr = $rs->getFloat($startcol + 53);

			$this->q1re_nr = $rs->getFloat($startcol + 54);

			$this->q1re_itca = $rs->getFloat($startcol + 55);

			$this->q1re_fea = $rs->getFloat($startcol + 56);

			$this->q1re_neot = $rs->getFloat($startcol + 57);

			$this->q1re_aem = $rs->getFloat($startcol + 58);

			$this->q1re_nrs = $rs->getFloat($startcol + 59);

			$this->q2re_surs = $rs->getFloat($startcol + 60);

			$this->q2re_sr = $rs->getFloat($startcol + 61);

			$this->q2re_nr = $rs->getFloat($startcol + 62);

			$this->q2re_itca = $rs->getFloat($startcol + 63);

			$this->q2re_fea = $rs->getFloat($startcol + 64);

			$this->q2re_neot = $rs->getFloat($startcol + 65);

			$this->q2re_aem = $rs->getFloat($startcol + 66);

			$this->q2re_nrs = $rs->getFloat($startcol + 67);

			$this->q3re_surs = $rs->getFloat($startcol + 68);

			$this->q3re_sr = $rs->getFloat($startcol + 69);

			$this->q3re_nr = $rs->getFloat($startcol + 70);

			$this->q3re_itca = $rs->getFloat($startcol + 71);

			$this->q3re_fea = $rs->getFloat($startcol + 72);

			$this->q3re_neot = $rs->getFloat($startcol + 73);

			$this->q3re_aem = $rs->getFloat($startcol + 74);

			$this->q3re_nrs = $rs->getFloat($startcol + 75);

			$this->q4re_surs = $rs->getFloat($startcol + 76);

			$this->q4re_sr = $rs->getFloat($startcol + 77);

			$this->q4re_nr = $rs->getFloat($startcol + 78);

			$this->q4re_itca = $rs->getFloat($startcol + 79);

			$this->q4re_fea = $rs->getFloat($startcol + 80);

			$this->q4re_neot = $rs->getFloat($startcol + 81);

			$this->q4re_aem = $rs->getFloat($startcol + 82);

			$this->q4re_nrs = $rs->getFloat($startcol + 83);

			$this->pqa_surs = $rs->getFloat($startcol + 84);

			$this->pqa_sr = $rs->getFloat($startcol + 85);

			$this->pqa_nr = $rs->getFloat($startcol + 86);

			$this->pqa_itca = $rs->getFloat($startcol + 87);

			$this->pqa_fea = $rs->getFloat($startcol + 88);

			$this->pqa_neot = $rs->getFloat($startcol + 89);

			$this->pqa_aem = $rs->getFloat($startcol + 90);

			$this->pqa_nrs = $rs->getFloat($startcol + 91);

			$this->cqe_surs = $rs->getFloat($startcol + 92);

			$this->cqe_sr = $rs->getFloat($startcol + 93);

			$this->cqe_nr = $rs->getFloat($startcol + 94);

			$this->cqe_itca = $rs->getFloat($startcol + 95);

			$this->cqe_fea = $rs->getFloat($startcol + 96);

			$this->cqe_neot = $rs->getFloat($startcol + 97);

			$this->cqe_aem = $rs->getFloat($startcol + 98);

			$this->cqe_nrs = $rs->getFloat($startcol + 99);

			$this->supbud_sup1_p = $rs->getFloat($startcol + 100);

			$this->supbud_sup1_e = $rs->getFloat($startcol + 101);

			$this->supbud_sup1_psc = $rs->getFloat($startcol + 102);

			$this->supbud_sup1_odc = $rs->getFloat($startcol + 103);

			$this->supbud_sup1_ic = $rs->getFloat($startcol + 104);

			$this->supbud_sup1_sa = $rs->getFloat($startcol + 105);

			$this->supbud_sup2_p = $rs->getFloat($startcol + 106);

			$this->supbud_sup2_e = $rs->getFloat($startcol + 107);

			$this->supbud_sup2_psc = $rs->getFloat($startcol + 108);

			$this->supbud_sup2_odc = $rs->getFloat($startcol + 109);

			$this->supbud_sup2_ic = $rs->getFloat($startcol + 110);

			$this->supbud_sup2_sa = $rs->getFloat($startcol + 111);

			$this->supbud_sup3_p = $rs->getFloat($startcol + 112);

			$this->supbud_sup3_e = $rs->getFloat($startcol + 113);

			$this->supbud_sup3_psc = $rs->getFloat($startcol + 114);

			$this->supbud_sup3_odc = $rs->getFloat($startcol + 115);

			$this->supbud_sup3_ic = $rs->getFloat($startcol + 116);

			$this->supbud_sup3_sa = $rs->getFloat($startcol + 117);

			$this->supbud_sup4_p = $rs->getFloat($startcol + 118);

			$this->supbud_sup4_e = $rs->getFloat($startcol + 119);

			$this->supbud_sup4_psc = $rs->getFloat($startcol + 120);

			$this->supbud_sup4_odc = $rs->getFloat($startcol + 121);

			$this->supbud_sup4_ic = $rs->getFloat($startcol + 122);

			$this->supbud_sup4_sa = $rs->getFloat($startcol + 123);

			$this->pi_beg_bal = $rs->getFloat($startcol + 124);

			$this->pi_pir = $rs->getFloat($startcol + 125);

			$this->pi_pie = $rs->getFloat($startcol + 126);

			$this->pi_nar = $rs->getString($startcol + 127);

			$this->created_by = $rs->getString($startcol + 128);

			$this->created_on = $rs->getDate($startcol + 129, null);

			$this->updated_by = $rs->getString($startcol + 130);

			$this->updated_on = $rs->getDate($startcol + 131, null);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 132; // 132 = SiteReportsQFRPeer::NUM_COLUMNS - SiteReportsQFRPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SiteReportsQFR object", $e);
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
			$con = Propel::getConnection(SiteReportsQFRPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SiteReportsQFRPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SiteReportsQFRPeer::DATABASE_NAME);
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
					$pk = SiteReportsQFRPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setID($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SiteReportsQFRPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collSiteReportsQFREPcds !== null) {
				foreach($this->collSiteReportsQFREPcds as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSiteReportsQFRProjects !== null) {
				foreach($this->collSiteReportsQFRProjects as $referrerFK) {
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


			if (($retval = SiteReportsQFRPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collSiteReportsQFREPcds !== null) {
					foreach($this->collSiteReportsQFREPcds as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSiteReportsQFRProjects !== null) {
					foreach($this->collSiteReportsQFRProjects as $referrerFK) {
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
		$pos = SiteReportsQFRPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPREPARED_BY();
				break;
			case 5:
				return $this->getPREPARERS_TITLE();
				break;
			case 6:
				return $this->getPREPARED_DATE();
				break;
			case 7:
				return $this->getREPORT_PERIOD();
				break;
			case 8:
				return $this->getSUBAWARDED_FUNDED_AMT();
				break;
			case 9:
				return $this->getQFR_SR_P_COST();
				break;
			case 10:
				return $this->getQFR_SR_E_COST();
				break;
			case 11:
				return $this->getQFR_SR_PSC_COST();
				break;
			case 12:
				return $this->getQFR_SR_ODC_COST();
				break;
			case 13:
				return $this->getQFR_SR_IC_COST();
				break;
			case 14:
				return $this->getQFR_NR_P_COST();
				break;
			case 15:
				return $this->getQFR_NR_E_COST();
				break;
			case 16:
				return $this->getQFR_NR_PSC_COST();
				break;
			case 17:
				return $this->getQFR_NR_ODC_COST();
				break;
			case 18:
				return $this->getQFR_NR_IC_COST();
				break;
			case 19:
				return $this->getQFR_ITCA_P_COST();
				break;
			case 20:
				return $this->getQFR_ITCA_E_COST();
				break;
			case 21:
				return $this->getQFR_ITCA_PSC_COST();
				break;
			case 22:
				return $this->getQFR_ITCA_ODC_COST();
				break;
			case 23:
				return $this->getQFR_ITCA_IC_COST();
				break;
			case 24:
				return $this->getQFR_NEOT_P_COST();
				break;
			case 25:
				return $this->getQFR_NEOT_E_COST();
				break;
			case 26:
				return $this->getQFR_NEOT_PSC_COST();
				break;
			case 27:
				return $this->getQFR_NEOT_ODC_COST();
				break;
			case 28:
				return $this->getQFR_NEOT_IC_COST();
				break;
			case 29:
				return $this->getQFR_FEA_P_COST();
				break;
			case 30:
				return $this->getQFR_FEA_E_COST();
				break;
			case 31:
				return $this->getQFR_FEA_PSC_COST();
				break;
			case 32:
				return $this->getQFR_FEA_ODC_COST();
				break;
			case 33:
				return $this->getQFR_FEA_IC_COST();
				break;
			case 34:
				return $this->getQFR_AEM_P_COST();
				break;
			case 35:
				return $this->getQFR_AEM_E_COST();
				break;
			case 36:
				return $this->getQFR_AEM_PSC_COST();
				break;
			case 37:
				return $this->getQFR_AEM_ODC_COST();
				break;
			case 38:
				return $this->getQFR_AEM_IC_COST();
				break;
			case 39:
				return $this->getQFR_NRS_P_COST();
				break;
			case 40:
				return $this->getQFR_NRS_E_COST();
				break;
			case 41:
				return $this->getQFR_NRS_PSC_COST();
				break;
			case 42:
				return $this->getQFR_NRS_ODC_COST();
				break;
			case 43:
				return $this->getQFR_NRS_IC_COST();
				break;
			case 44:
				return $this->getFY_BUDGET_SURS();
				break;
			case 45:
				return $this->getFY_BUDGET_SR();
				break;
			case 46:
				return $this->getFY_BUDGET_NR();
				break;
			case 47:
				return $this->getFY_BUDGET_ITCA();
				break;
			case 48:
				return $this->getFY_BUDGET_FEA();
				break;
			case 49:
				return $this->getFY_BUDGET_NEOT();
				break;
			case 50:
				return $this->getFY_BUDGET_AEM();
				break;
			case 51:
				return $this->getFY_BUDGET_NRS();
				break;
			case 52:
				return $this->getQ1RE_SURS();
				break;
			case 53:
				return $this->getQ1RE_SR();
				break;
			case 54:
				return $this->getQ1RE_NR();
				break;
			case 55:
				return $this->getQ1RE_ITCA();
				break;
			case 56:
				return $this->getQ1RE_FEA();
				break;
			case 57:
				return $this->getQ1RE_NEOT();
				break;
			case 58:
				return $this->getQ1RE_AEM();
				break;
			case 59:
				return $this->getQ1RE_NRS();
				break;
			case 60:
				return $this->getQ2RE_SURS();
				break;
			case 61:
				return $this->getQ2RE_SR();
				break;
			case 62:
				return $this->getQ2RE_NR();
				break;
			case 63:
				return $this->getQ2RE_ITCA();
				break;
			case 64:
				return $this->getQ2RE_FEA();
				break;
			case 65:
				return $this->getQ2RE_NEOT();
				break;
			case 66:
				return $this->getQ2RE_AEM();
				break;
			case 67:
				return $this->getQ2RE_NRS();
				break;
			case 68:
				return $this->getQ3RE_SURS();
				break;
			case 69:
				return $this->getQ3RE_SR();
				break;
			case 70:
				return $this->getQ3RE_NR();
				break;
			case 71:
				return $this->getQ3RE_ITCA();
				break;
			case 72:
				return $this->getQ3RE_FEA();
				break;
			case 73:
				return $this->getQ3RE_NEOT();
				break;
			case 74:
				return $this->getQ3RE_AEM();
				break;
			case 75:
				return $this->getQ3RE_NRS();
				break;
			case 76:
				return $this->getQ4RE_SURS();
				break;
			case 77:
				return $this->getQ4RE_SR();
				break;
			case 78:
				return $this->getQ4RE_NR();
				break;
			case 79:
				return $this->getQ4RE_ITCA();
				break;
			case 80:
				return $this->getQ4RE_FEA();
				break;
			case 81:
				return $this->getQ4RE_NEOT();
				break;
			case 82:
				return $this->getQ4RE_AEM();
				break;
			case 83:
				return $this->getQ4RE_NRS();
				break;
			case 84:
				return $this->getPQA_SURS();
				break;
			case 85:
				return $this->getPQA_SR();
				break;
			case 86:
				return $this->getPQA_NR();
				break;
			case 87:
				return $this->getPQA_ITCA();
				break;
			case 88:
				return $this->getPQA_FEA();
				break;
			case 89:
				return $this->getPQA_NEOT();
				break;
			case 90:
				return $this->getPQA_AEM();
				break;
			case 91:
				return $this->getPQA_NRS();
				break;
			case 92:
				return $this->getCQE_SURS();
				break;
			case 93:
				return $this->getCQE_SR();
				break;
			case 94:
				return $this->getCQE_NR();
				break;
			case 95:
				return $this->getCQE_ITCA();
				break;
			case 96:
				return $this->getCQE_FEA();
				break;
			case 97:
				return $this->getCQE_NEOT();
				break;
			case 98:
				return $this->getCQE_AEM();
				break;
			case 99:
				return $this->getCQE_NRS();
				break;
			case 100:
				return $this->getSUPBUD_SUP1_P();
				break;
			case 101:
				return $this->getSUPBUD_SUP1_E();
				break;
			case 102:
				return $this->getSUPBUD_SUP1_PSC();
				break;
			case 103:
				return $this->getSUPBUD_SUP1_ODC();
				break;
			case 104:
				return $this->getSUPBUD_SUP1_IC();
				break;
			case 105:
				return $this->getSUPBUD_SUP1_SA();
				break;
			case 106:
				return $this->getSUPBUD_SUP2_P();
				break;
			case 107:
				return $this->getSUPBUD_SUP2_E();
				break;
			case 108:
				return $this->getSUPBUD_SUP2_PSC();
				break;
			case 109:
				return $this->getSUPBUD_SUP2_ODC();
				break;
			case 110:
				return $this->getSUPBUD_SUP2_IC();
				break;
			case 111:
				return $this->getSUPBUD_SUP2_SA();
				break;
			case 112:
				return $this->getSUPBUD_SUP3_P();
				break;
			case 113:
				return $this->getSUPBUD_SUP3_E();
				break;
			case 114:
				return $this->getSUPBUD_SUP3_PSC();
				break;
			case 115:
				return $this->getSUPBUD_SUP3_ODC();
				break;
			case 116:
				return $this->getSUPBUD_SUP3_IC();
				break;
			case 117:
				return $this->getSUPBUD_SUP3_SA();
				break;
			case 118:
				return $this->getSUPBUD_SUP4_P();
				break;
			case 119:
				return $this->getSUPBUD_SUP4_E();
				break;
			case 120:
				return $this->getSUPBUD_SUP4_PSC();
				break;
			case 121:
				return $this->getSUPBUD_SUP4_ODC();
				break;
			case 122:
				return $this->getSUPBUD_SUP4_IC();
				break;
			case 123:
				return $this->getSUPBUD_SUP4_SA();
				break;
			case 124:
				return $this->getPI_BEG_BAL();
				break;
			case 125:
				return $this->getPI_PIR();
				break;
			case 126:
				return $this->getPI_PIE();
				break;
			case 127:
				return $this->getPI_NAR();
				break;
			case 128:
				return $this->getCREATED_BY();
				break;
			case 129:
				return $this->getCREATED_ON();
				break;
			case 130:
				return $this->getUPDATED_BY();
				break;
			case 131:
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
		$keys = SiteReportsQFRPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getID(),
			$keys[1] => $this->getFACILITY_ID(),
			$keys[2] => $this->getYEAR(),
			$keys[3] => $this->getQUARTER(),
			$keys[4] => $this->getPREPARED_BY(),
			$keys[5] => $this->getPREPARERS_TITLE(),
			$keys[6] => $this->getPREPARED_DATE(),
			$keys[7] => $this->getREPORT_PERIOD(),
			$keys[8] => $this->getSUBAWARDED_FUNDED_AMT(),
			$keys[9] => $this->getQFR_SR_P_COST(),
			$keys[10] => $this->getQFR_SR_E_COST(),
			$keys[11] => $this->getQFR_SR_PSC_COST(),
			$keys[12] => $this->getQFR_SR_ODC_COST(),
			$keys[13] => $this->getQFR_SR_IC_COST(),
			$keys[14] => $this->getQFR_NR_P_COST(),
			$keys[15] => $this->getQFR_NR_E_COST(),
			$keys[16] => $this->getQFR_NR_PSC_COST(),
			$keys[17] => $this->getQFR_NR_ODC_COST(),
			$keys[18] => $this->getQFR_NR_IC_COST(),
			$keys[19] => $this->getQFR_ITCA_P_COST(),
			$keys[20] => $this->getQFR_ITCA_E_COST(),
			$keys[21] => $this->getQFR_ITCA_PSC_COST(),
			$keys[22] => $this->getQFR_ITCA_ODC_COST(),
			$keys[23] => $this->getQFR_ITCA_IC_COST(),
			$keys[24] => $this->getQFR_NEOT_P_COST(),
			$keys[25] => $this->getQFR_NEOT_E_COST(),
			$keys[26] => $this->getQFR_NEOT_PSC_COST(),
			$keys[27] => $this->getQFR_NEOT_ODC_COST(),
			$keys[28] => $this->getQFR_NEOT_IC_COST(),
			$keys[29] => $this->getQFR_FEA_P_COST(),
			$keys[30] => $this->getQFR_FEA_E_COST(),
			$keys[31] => $this->getQFR_FEA_PSC_COST(),
			$keys[32] => $this->getQFR_FEA_ODC_COST(),
			$keys[33] => $this->getQFR_FEA_IC_COST(),
			$keys[34] => $this->getQFR_AEM_P_COST(),
			$keys[35] => $this->getQFR_AEM_E_COST(),
			$keys[36] => $this->getQFR_AEM_PSC_COST(),
			$keys[37] => $this->getQFR_AEM_ODC_COST(),
			$keys[38] => $this->getQFR_AEM_IC_COST(),
			$keys[39] => $this->getQFR_NRS_P_COST(),
			$keys[40] => $this->getQFR_NRS_E_COST(),
			$keys[41] => $this->getQFR_NRS_PSC_COST(),
			$keys[42] => $this->getQFR_NRS_ODC_COST(),
			$keys[43] => $this->getQFR_NRS_IC_COST(),
			$keys[44] => $this->getFY_BUDGET_SURS(),
			$keys[45] => $this->getFY_BUDGET_SR(),
			$keys[46] => $this->getFY_BUDGET_NR(),
			$keys[47] => $this->getFY_BUDGET_ITCA(),
			$keys[48] => $this->getFY_BUDGET_FEA(),
			$keys[49] => $this->getFY_BUDGET_NEOT(),
			$keys[50] => $this->getFY_BUDGET_AEM(),
			$keys[51] => $this->getFY_BUDGET_NRS(),
			$keys[52] => $this->getQ1RE_SURS(),
			$keys[53] => $this->getQ1RE_SR(),
			$keys[54] => $this->getQ1RE_NR(),
			$keys[55] => $this->getQ1RE_ITCA(),
			$keys[56] => $this->getQ1RE_FEA(),
			$keys[57] => $this->getQ1RE_NEOT(),
			$keys[58] => $this->getQ1RE_AEM(),
			$keys[59] => $this->getQ1RE_NRS(),
			$keys[60] => $this->getQ2RE_SURS(),
			$keys[61] => $this->getQ2RE_SR(),
			$keys[62] => $this->getQ2RE_NR(),
			$keys[63] => $this->getQ2RE_ITCA(),
			$keys[64] => $this->getQ2RE_FEA(),
			$keys[65] => $this->getQ2RE_NEOT(),
			$keys[66] => $this->getQ2RE_AEM(),
			$keys[67] => $this->getQ2RE_NRS(),
			$keys[68] => $this->getQ3RE_SURS(),
			$keys[69] => $this->getQ3RE_SR(),
			$keys[70] => $this->getQ3RE_NR(),
			$keys[71] => $this->getQ3RE_ITCA(),
			$keys[72] => $this->getQ3RE_FEA(),
			$keys[73] => $this->getQ3RE_NEOT(),
			$keys[74] => $this->getQ3RE_AEM(),
			$keys[75] => $this->getQ3RE_NRS(),
			$keys[76] => $this->getQ4RE_SURS(),
			$keys[77] => $this->getQ4RE_SR(),
			$keys[78] => $this->getQ4RE_NR(),
			$keys[79] => $this->getQ4RE_ITCA(),
			$keys[80] => $this->getQ4RE_FEA(),
			$keys[81] => $this->getQ4RE_NEOT(),
			$keys[82] => $this->getQ4RE_AEM(),
			$keys[83] => $this->getQ4RE_NRS(),
			$keys[84] => $this->getPQA_SURS(),
			$keys[85] => $this->getPQA_SR(),
			$keys[86] => $this->getPQA_NR(),
			$keys[87] => $this->getPQA_ITCA(),
			$keys[88] => $this->getPQA_FEA(),
			$keys[89] => $this->getPQA_NEOT(),
			$keys[90] => $this->getPQA_AEM(),
			$keys[91] => $this->getPQA_NRS(),
			$keys[92] => $this->getCQE_SURS(),
			$keys[93] => $this->getCQE_SR(),
			$keys[94] => $this->getCQE_NR(),
			$keys[95] => $this->getCQE_ITCA(),
			$keys[96] => $this->getCQE_FEA(),
			$keys[97] => $this->getCQE_NEOT(),
			$keys[98] => $this->getCQE_AEM(),
			$keys[99] => $this->getCQE_NRS(),
			$keys[100] => $this->getSUPBUD_SUP1_P(),
			$keys[101] => $this->getSUPBUD_SUP1_E(),
			$keys[102] => $this->getSUPBUD_SUP1_PSC(),
			$keys[103] => $this->getSUPBUD_SUP1_ODC(),
			$keys[104] => $this->getSUPBUD_SUP1_IC(),
			$keys[105] => $this->getSUPBUD_SUP1_SA(),
			$keys[106] => $this->getSUPBUD_SUP2_P(),
			$keys[107] => $this->getSUPBUD_SUP2_E(),
			$keys[108] => $this->getSUPBUD_SUP2_PSC(),
			$keys[109] => $this->getSUPBUD_SUP2_ODC(),
			$keys[110] => $this->getSUPBUD_SUP2_IC(),
			$keys[111] => $this->getSUPBUD_SUP2_SA(),
			$keys[112] => $this->getSUPBUD_SUP3_P(),
			$keys[113] => $this->getSUPBUD_SUP3_E(),
			$keys[114] => $this->getSUPBUD_SUP3_PSC(),
			$keys[115] => $this->getSUPBUD_SUP3_ODC(),
			$keys[116] => $this->getSUPBUD_SUP3_IC(),
			$keys[117] => $this->getSUPBUD_SUP3_SA(),
			$keys[118] => $this->getSUPBUD_SUP4_P(),
			$keys[119] => $this->getSUPBUD_SUP4_E(),
			$keys[120] => $this->getSUPBUD_SUP4_PSC(),
			$keys[121] => $this->getSUPBUD_SUP4_ODC(),
			$keys[122] => $this->getSUPBUD_SUP4_IC(),
			$keys[123] => $this->getSUPBUD_SUP4_SA(),
			$keys[124] => $this->getPI_BEG_BAL(),
			$keys[125] => $this->getPI_PIR(),
			$keys[126] => $this->getPI_PIE(),
			$keys[127] => $this->getPI_NAR(),
			$keys[128] => $this->getCREATED_BY(),
			$keys[129] => $this->getCREATED_ON(),
			$keys[130] => $this->getUPDATED_BY(),
			$keys[131] => $this->getUPDATED_ON(),
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
		$pos = SiteReportsQFRPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPREPARED_BY($value);
				break;
			case 5:
				$this->setPREPARERS_TITLE($value);
				break;
			case 6:
				$this->setPREPARED_DATE($value);
				break;
			case 7:
				$this->setREPORT_PERIOD($value);
				break;
			case 8:
				$this->setSUBAWARDED_FUNDED_AMT($value);
				break;
			case 9:
				$this->setQFR_SR_P_COST($value);
				break;
			case 10:
				$this->setQFR_SR_E_COST($value);
				break;
			case 11:
				$this->setQFR_SR_PSC_COST($value);
				break;
			case 12:
				$this->setQFR_SR_ODC_COST($value);
				break;
			case 13:
				$this->setQFR_SR_IC_COST($value);
				break;
			case 14:
				$this->setQFR_NR_P_COST($value);
				break;
			case 15:
				$this->setQFR_NR_E_COST($value);
				break;
			case 16:
				$this->setQFR_NR_PSC_COST($value);
				break;
			case 17:
				$this->setQFR_NR_ODC_COST($value);
				break;
			case 18:
				$this->setQFR_NR_IC_COST($value);
				break;
			case 19:
				$this->setQFR_ITCA_P_COST($value);
				break;
			case 20:
				$this->setQFR_ITCA_E_COST($value);
				break;
			case 21:
				$this->setQFR_ITCA_PSC_COST($value);
				break;
			case 22:
				$this->setQFR_ITCA_ODC_COST($value);
				break;
			case 23:
				$this->setQFR_ITCA_IC_COST($value);
				break;
			case 24:
				$this->setQFR_NEOT_P_COST($value);
				break;
			case 25:
				$this->setQFR_NEOT_E_COST($value);
				break;
			case 26:
				$this->setQFR_NEOT_PSC_COST($value);
				break;
			case 27:
				$this->setQFR_NEOT_ODC_COST($value);
				break;
			case 28:
				$this->setQFR_NEOT_IC_COST($value);
				break;
			case 29:
				$this->setQFR_FEA_P_COST($value);
				break;
			case 30:
				$this->setQFR_FEA_E_COST($value);
				break;
			case 31:
				$this->setQFR_FEA_PSC_COST($value);
				break;
			case 32:
				$this->setQFR_FEA_ODC_COST($value);
				break;
			case 33:
				$this->setQFR_FEA_IC_COST($value);
				break;
			case 34:
				$this->setQFR_AEM_P_COST($value);
				break;
			case 35:
				$this->setQFR_AEM_E_COST($value);
				break;
			case 36:
				$this->setQFR_AEM_PSC_COST($value);
				break;
			case 37:
				$this->setQFR_AEM_ODC_COST($value);
				break;
			case 38:
				$this->setQFR_AEM_IC_COST($value);
				break;
			case 39:
				$this->setQFR_NRS_P_COST($value);
				break;
			case 40:
				$this->setQFR_NRS_E_COST($value);
				break;
			case 41:
				$this->setQFR_NRS_PSC_COST($value);
				break;
			case 42:
				$this->setQFR_NRS_ODC_COST($value);
				break;
			case 43:
				$this->setQFR_NRS_IC_COST($value);
				break;
			case 44:
				$this->setFY_BUDGET_SURS($value);
				break;
			case 45:
				$this->setFY_BUDGET_SR($value);
				break;
			case 46:
				$this->setFY_BUDGET_NR($value);
				break;
			case 47:
				$this->setFY_BUDGET_ITCA($value);
				break;
			case 48:
				$this->setFY_BUDGET_FEA($value);
				break;
			case 49:
				$this->setFY_BUDGET_NEOT($value);
				break;
			case 50:
				$this->setFY_BUDGET_AEM($value);
				break;
			case 51:
				$this->setFY_BUDGET_NRS($value);
				break;
			case 52:
				$this->setQ1RE_SURS($value);
				break;
			case 53:
				$this->setQ1RE_SR($value);
				break;
			case 54:
				$this->setQ1RE_NR($value);
				break;
			case 55:
				$this->setQ1RE_ITCA($value);
				break;
			case 56:
				$this->setQ1RE_FEA($value);
				break;
			case 57:
				$this->setQ1RE_NEOT($value);
				break;
			case 58:
				$this->setQ1RE_AEM($value);
				break;
			case 59:
				$this->setQ1RE_NRS($value);
				break;
			case 60:
				$this->setQ2RE_SURS($value);
				break;
			case 61:
				$this->setQ2RE_SR($value);
				break;
			case 62:
				$this->setQ2RE_NR($value);
				break;
			case 63:
				$this->setQ2RE_ITCA($value);
				break;
			case 64:
				$this->setQ2RE_FEA($value);
				break;
			case 65:
				$this->setQ2RE_NEOT($value);
				break;
			case 66:
				$this->setQ2RE_AEM($value);
				break;
			case 67:
				$this->setQ2RE_NRS($value);
				break;
			case 68:
				$this->setQ3RE_SURS($value);
				break;
			case 69:
				$this->setQ3RE_SR($value);
				break;
			case 70:
				$this->setQ3RE_NR($value);
				break;
			case 71:
				$this->setQ3RE_ITCA($value);
				break;
			case 72:
				$this->setQ3RE_FEA($value);
				break;
			case 73:
				$this->setQ3RE_NEOT($value);
				break;
			case 74:
				$this->setQ3RE_AEM($value);
				break;
			case 75:
				$this->setQ3RE_NRS($value);
				break;
			case 76:
				$this->setQ4RE_SURS($value);
				break;
			case 77:
				$this->setQ4RE_SR($value);
				break;
			case 78:
				$this->setQ4RE_NR($value);
				break;
			case 79:
				$this->setQ4RE_ITCA($value);
				break;
			case 80:
				$this->setQ4RE_FEA($value);
				break;
			case 81:
				$this->setQ4RE_NEOT($value);
				break;
			case 82:
				$this->setQ4RE_AEM($value);
				break;
			case 83:
				$this->setQ4RE_NRS($value);
				break;
			case 84:
				$this->setPQA_SURS($value);
				break;
			case 85:
				$this->setPQA_SR($value);
				break;
			case 86:
				$this->setPQA_NR($value);
				break;
			case 87:
				$this->setPQA_ITCA($value);
				break;
			case 88:
				$this->setPQA_FEA($value);
				break;
			case 89:
				$this->setPQA_NEOT($value);
				break;
			case 90:
				$this->setPQA_AEM($value);
				break;
			case 91:
				$this->setPQA_NRS($value);
				break;
			case 92:
				$this->setCQE_SURS($value);
				break;
			case 93:
				$this->setCQE_SR($value);
				break;
			case 94:
				$this->setCQE_NR($value);
				break;
			case 95:
				$this->setCQE_ITCA($value);
				break;
			case 96:
				$this->setCQE_FEA($value);
				break;
			case 97:
				$this->setCQE_NEOT($value);
				break;
			case 98:
				$this->setCQE_AEM($value);
				break;
			case 99:
				$this->setCQE_NRS($value);
				break;
			case 100:
				$this->setSUPBUD_SUP1_P($value);
				break;
			case 101:
				$this->setSUPBUD_SUP1_E($value);
				break;
			case 102:
				$this->setSUPBUD_SUP1_PSC($value);
				break;
			case 103:
				$this->setSUPBUD_SUP1_ODC($value);
				break;
			case 104:
				$this->setSUPBUD_SUP1_IC($value);
				break;
			case 105:
				$this->setSUPBUD_SUP1_SA($value);
				break;
			case 106:
				$this->setSUPBUD_SUP2_P($value);
				break;
			case 107:
				$this->setSUPBUD_SUP2_E($value);
				break;
			case 108:
				$this->setSUPBUD_SUP2_PSC($value);
				break;
			case 109:
				$this->setSUPBUD_SUP2_ODC($value);
				break;
			case 110:
				$this->setSUPBUD_SUP2_IC($value);
				break;
			case 111:
				$this->setSUPBUD_SUP2_SA($value);
				break;
			case 112:
				$this->setSUPBUD_SUP3_P($value);
				break;
			case 113:
				$this->setSUPBUD_SUP3_E($value);
				break;
			case 114:
				$this->setSUPBUD_SUP3_PSC($value);
				break;
			case 115:
				$this->setSUPBUD_SUP3_ODC($value);
				break;
			case 116:
				$this->setSUPBUD_SUP3_IC($value);
				break;
			case 117:
				$this->setSUPBUD_SUP3_SA($value);
				break;
			case 118:
				$this->setSUPBUD_SUP4_P($value);
				break;
			case 119:
				$this->setSUPBUD_SUP4_E($value);
				break;
			case 120:
				$this->setSUPBUD_SUP4_PSC($value);
				break;
			case 121:
				$this->setSUPBUD_SUP4_ODC($value);
				break;
			case 122:
				$this->setSUPBUD_SUP4_IC($value);
				break;
			case 123:
				$this->setSUPBUD_SUP4_SA($value);
				break;
			case 124:
				$this->setPI_BEG_BAL($value);
				break;
			case 125:
				$this->setPI_PIR($value);
				break;
			case 126:
				$this->setPI_PIE($value);
				break;
			case 127:
				$this->setPI_NAR($value);
				break;
			case 128:
				$this->setCREATED_BY($value);
				break;
			case 129:
				$this->setCREATED_ON($value);
				break;
			case 130:
				$this->setUPDATED_BY($value);
				break;
			case 131:
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
		$keys = SiteReportsQFRPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setID($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setFACILITY_ID($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setYEAR($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setQUARTER($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setPREPARED_BY($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setPREPARERS_TITLE($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setPREPARED_DATE($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setREPORT_PERIOD($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setSUBAWARDED_FUNDED_AMT($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setQFR_SR_P_COST($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setQFR_SR_E_COST($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setQFR_SR_PSC_COST($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setQFR_SR_ODC_COST($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setQFR_SR_IC_COST($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setQFR_NR_P_COST($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setQFR_NR_E_COST($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setQFR_NR_PSC_COST($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setQFR_NR_ODC_COST($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setQFR_NR_IC_COST($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setQFR_ITCA_P_COST($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setQFR_ITCA_E_COST($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setQFR_ITCA_PSC_COST($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setQFR_ITCA_ODC_COST($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setQFR_ITCA_IC_COST($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setQFR_NEOT_P_COST($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setQFR_NEOT_E_COST($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setQFR_NEOT_PSC_COST($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setQFR_NEOT_ODC_COST($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setQFR_NEOT_IC_COST($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setQFR_FEA_P_COST($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setQFR_FEA_E_COST($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setQFR_FEA_PSC_COST($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setQFR_FEA_ODC_COST($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setQFR_FEA_IC_COST($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setQFR_AEM_P_COST($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setQFR_AEM_E_COST($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setQFR_AEM_PSC_COST($arr[$keys[36]]);
		if (array_key_exists($keys[37], $arr)) $this->setQFR_AEM_ODC_COST($arr[$keys[37]]);
		if (array_key_exists($keys[38], $arr)) $this->setQFR_AEM_IC_COST($arr[$keys[38]]);
		if (array_key_exists($keys[39], $arr)) $this->setQFR_NRS_P_COST($arr[$keys[39]]);
		if (array_key_exists($keys[40], $arr)) $this->setQFR_NRS_E_COST($arr[$keys[40]]);
		if (array_key_exists($keys[41], $arr)) $this->setQFR_NRS_PSC_COST($arr[$keys[41]]);
		if (array_key_exists($keys[42], $arr)) $this->setQFR_NRS_ODC_COST($arr[$keys[42]]);
		if (array_key_exists($keys[43], $arr)) $this->setQFR_NRS_IC_COST($arr[$keys[43]]);
		if (array_key_exists($keys[44], $arr)) $this->setFY_BUDGET_SURS($arr[$keys[44]]);
		if (array_key_exists($keys[45], $arr)) $this->setFY_BUDGET_SR($arr[$keys[45]]);
		if (array_key_exists($keys[46], $arr)) $this->setFY_BUDGET_NR($arr[$keys[46]]);
		if (array_key_exists($keys[47], $arr)) $this->setFY_BUDGET_ITCA($arr[$keys[47]]);
		if (array_key_exists($keys[48], $arr)) $this->setFY_BUDGET_FEA($arr[$keys[48]]);
		if (array_key_exists($keys[49], $arr)) $this->setFY_BUDGET_NEOT($arr[$keys[49]]);
		if (array_key_exists($keys[50], $arr)) $this->setFY_BUDGET_AEM($arr[$keys[50]]);
		if (array_key_exists($keys[51], $arr)) $this->setFY_BUDGET_NRS($arr[$keys[51]]);
		if (array_key_exists($keys[52], $arr)) $this->setQ1RE_SURS($arr[$keys[52]]);
		if (array_key_exists($keys[53], $arr)) $this->setQ1RE_SR($arr[$keys[53]]);
		if (array_key_exists($keys[54], $arr)) $this->setQ1RE_NR($arr[$keys[54]]);
		if (array_key_exists($keys[55], $arr)) $this->setQ1RE_ITCA($arr[$keys[55]]);
		if (array_key_exists($keys[56], $arr)) $this->setQ1RE_FEA($arr[$keys[56]]);
		if (array_key_exists($keys[57], $arr)) $this->setQ1RE_NEOT($arr[$keys[57]]);
		if (array_key_exists($keys[58], $arr)) $this->setQ1RE_AEM($arr[$keys[58]]);
		if (array_key_exists($keys[59], $arr)) $this->setQ1RE_NRS($arr[$keys[59]]);
		if (array_key_exists($keys[60], $arr)) $this->setQ2RE_SURS($arr[$keys[60]]);
		if (array_key_exists($keys[61], $arr)) $this->setQ2RE_SR($arr[$keys[61]]);
		if (array_key_exists($keys[62], $arr)) $this->setQ2RE_NR($arr[$keys[62]]);
		if (array_key_exists($keys[63], $arr)) $this->setQ2RE_ITCA($arr[$keys[63]]);
		if (array_key_exists($keys[64], $arr)) $this->setQ2RE_FEA($arr[$keys[64]]);
		if (array_key_exists($keys[65], $arr)) $this->setQ2RE_NEOT($arr[$keys[65]]);
		if (array_key_exists($keys[66], $arr)) $this->setQ2RE_AEM($arr[$keys[66]]);
		if (array_key_exists($keys[67], $arr)) $this->setQ2RE_NRS($arr[$keys[67]]);
		if (array_key_exists($keys[68], $arr)) $this->setQ3RE_SURS($arr[$keys[68]]);
		if (array_key_exists($keys[69], $arr)) $this->setQ3RE_SR($arr[$keys[69]]);
		if (array_key_exists($keys[70], $arr)) $this->setQ3RE_NR($arr[$keys[70]]);
		if (array_key_exists($keys[71], $arr)) $this->setQ3RE_ITCA($arr[$keys[71]]);
		if (array_key_exists($keys[72], $arr)) $this->setQ3RE_FEA($arr[$keys[72]]);
		if (array_key_exists($keys[73], $arr)) $this->setQ3RE_NEOT($arr[$keys[73]]);
		if (array_key_exists($keys[74], $arr)) $this->setQ3RE_AEM($arr[$keys[74]]);
		if (array_key_exists($keys[75], $arr)) $this->setQ3RE_NRS($arr[$keys[75]]);
		if (array_key_exists($keys[76], $arr)) $this->setQ4RE_SURS($arr[$keys[76]]);
		if (array_key_exists($keys[77], $arr)) $this->setQ4RE_SR($arr[$keys[77]]);
		if (array_key_exists($keys[78], $arr)) $this->setQ4RE_NR($arr[$keys[78]]);
		if (array_key_exists($keys[79], $arr)) $this->setQ4RE_ITCA($arr[$keys[79]]);
		if (array_key_exists($keys[80], $arr)) $this->setQ4RE_FEA($arr[$keys[80]]);
		if (array_key_exists($keys[81], $arr)) $this->setQ4RE_NEOT($arr[$keys[81]]);
		if (array_key_exists($keys[82], $arr)) $this->setQ4RE_AEM($arr[$keys[82]]);
		if (array_key_exists($keys[83], $arr)) $this->setQ4RE_NRS($arr[$keys[83]]);
		if (array_key_exists($keys[84], $arr)) $this->setPQA_SURS($arr[$keys[84]]);
		if (array_key_exists($keys[85], $arr)) $this->setPQA_SR($arr[$keys[85]]);
		if (array_key_exists($keys[86], $arr)) $this->setPQA_NR($arr[$keys[86]]);
		if (array_key_exists($keys[87], $arr)) $this->setPQA_ITCA($arr[$keys[87]]);
		if (array_key_exists($keys[88], $arr)) $this->setPQA_FEA($arr[$keys[88]]);
		if (array_key_exists($keys[89], $arr)) $this->setPQA_NEOT($arr[$keys[89]]);
		if (array_key_exists($keys[90], $arr)) $this->setPQA_AEM($arr[$keys[90]]);
		if (array_key_exists($keys[91], $arr)) $this->setPQA_NRS($arr[$keys[91]]);
		if (array_key_exists($keys[92], $arr)) $this->setCQE_SURS($arr[$keys[92]]);
		if (array_key_exists($keys[93], $arr)) $this->setCQE_SR($arr[$keys[93]]);
		if (array_key_exists($keys[94], $arr)) $this->setCQE_NR($arr[$keys[94]]);
		if (array_key_exists($keys[95], $arr)) $this->setCQE_ITCA($arr[$keys[95]]);
		if (array_key_exists($keys[96], $arr)) $this->setCQE_FEA($arr[$keys[96]]);
		if (array_key_exists($keys[97], $arr)) $this->setCQE_NEOT($arr[$keys[97]]);
		if (array_key_exists($keys[98], $arr)) $this->setCQE_AEM($arr[$keys[98]]);
		if (array_key_exists($keys[99], $arr)) $this->setCQE_NRS($arr[$keys[99]]);
		if (array_key_exists($keys[100], $arr)) $this->setSUPBUD_SUP1_P($arr[$keys[100]]);
		if (array_key_exists($keys[101], $arr)) $this->setSUPBUD_SUP1_E($arr[$keys[101]]);
		if (array_key_exists($keys[102], $arr)) $this->setSUPBUD_SUP1_PSC($arr[$keys[102]]);
		if (array_key_exists($keys[103], $arr)) $this->setSUPBUD_SUP1_ODC($arr[$keys[103]]);
		if (array_key_exists($keys[104], $arr)) $this->setSUPBUD_SUP1_IC($arr[$keys[104]]);
		if (array_key_exists($keys[105], $arr)) $this->setSUPBUD_SUP1_SA($arr[$keys[105]]);
		if (array_key_exists($keys[106], $arr)) $this->setSUPBUD_SUP2_P($arr[$keys[106]]);
		if (array_key_exists($keys[107], $arr)) $this->setSUPBUD_SUP2_E($arr[$keys[107]]);
		if (array_key_exists($keys[108], $arr)) $this->setSUPBUD_SUP2_PSC($arr[$keys[108]]);
		if (array_key_exists($keys[109], $arr)) $this->setSUPBUD_SUP2_ODC($arr[$keys[109]]);
		if (array_key_exists($keys[110], $arr)) $this->setSUPBUD_SUP2_IC($arr[$keys[110]]);
		if (array_key_exists($keys[111], $arr)) $this->setSUPBUD_SUP2_SA($arr[$keys[111]]);
		if (array_key_exists($keys[112], $arr)) $this->setSUPBUD_SUP3_P($arr[$keys[112]]);
		if (array_key_exists($keys[113], $arr)) $this->setSUPBUD_SUP3_E($arr[$keys[113]]);
		if (array_key_exists($keys[114], $arr)) $this->setSUPBUD_SUP3_PSC($arr[$keys[114]]);
		if (array_key_exists($keys[115], $arr)) $this->setSUPBUD_SUP3_ODC($arr[$keys[115]]);
		if (array_key_exists($keys[116], $arr)) $this->setSUPBUD_SUP3_IC($arr[$keys[116]]);
		if (array_key_exists($keys[117], $arr)) $this->setSUPBUD_SUP3_SA($arr[$keys[117]]);
		if (array_key_exists($keys[118], $arr)) $this->setSUPBUD_SUP4_P($arr[$keys[118]]);
		if (array_key_exists($keys[119], $arr)) $this->setSUPBUD_SUP4_E($arr[$keys[119]]);
		if (array_key_exists($keys[120], $arr)) $this->setSUPBUD_SUP4_PSC($arr[$keys[120]]);
		if (array_key_exists($keys[121], $arr)) $this->setSUPBUD_SUP4_ODC($arr[$keys[121]]);
		if (array_key_exists($keys[122], $arr)) $this->setSUPBUD_SUP4_IC($arr[$keys[122]]);
		if (array_key_exists($keys[123], $arr)) $this->setSUPBUD_SUP4_SA($arr[$keys[123]]);
		if (array_key_exists($keys[124], $arr)) $this->setPI_BEG_BAL($arr[$keys[124]]);
		if (array_key_exists($keys[125], $arr)) $this->setPI_PIR($arr[$keys[125]]);
		if (array_key_exists($keys[126], $arr)) $this->setPI_PIE($arr[$keys[126]]);
		if (array_key_exists($keys[127], $arr)) $this->setPI_NAR($arr[$keys[127]]);
		if (array_key_exists($keys[128], $arr)) $this->setCREATED_BY($arr[$keys[128]]);
		if (array_key_exists($keys[129], $arr)) $this->setCREATED_ON($arr[$keys[129]]);
		if (array_key_exists($keys[130], $arr)) $this->setUPDATED_BY($arr[$keys[130]]);
		if (array_key_exists($keys[131], $arr)) $this->setUPDATED_ON($arr[$keys[131]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SiteReportsQFRPeer::DATABASE_NAME);

		if ($this->isColumnModified(SiteReportsQFRPeer::ID)) $criteria->add(SiteReportsQFRPeer::ID, $this->id);
		if ($this->isColumnModified(SiteReportsQFRPeer::FACILITY_ID)) $criteria->add(SiteReportsQFRPeer::FACILITY_ID, $this->facility_id);
		if ($this->isColumnModified(SiteReportsQFRPeer::YEAR)) $criteria->add(SiteReportsQFRPeer::YEAR, $this->year);
		if ($this->isColumnModified(SiteReportsQFRPeer::QUARTER)) $criteria->add(SiteReportsQFRPeer::QUARTER, $this->quarter);
		if ($this->isColumnModified(SiteReportsQFRPeer::PREPARED_BY)) $criteria->add(SiteReportsQFRPeer::PREPARED_BY, $this->prepared_by);
		if ($this->isColumnModified(SiteReportsQFRPeer::PREPARERS_TITLE)) $criteria->add(SiteReportsQFRPeer::PREPARERS_TITLE, $this->preparers_title);
		if ($this->isColumnModified(SiteReportsQFRPeer::PREPARED_DATE)) $criteria->add(SiteReportsQFRPeer::PREPARED_DATE, $this->prepared_date);
		if ($this->isColumnModified(SiteReportsQFRPeer::REPORT_PERIOD)) $criteria->add(SiteReportsQFRPeer::REPORT_PERIOD, $this->report_period);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUBAWARDED_FUNDED_AMT)) $criteria->add(SiteReportsQFRPeer::SUBAWARDED_FUNDED_AMT, $this->subawarded_funded_amt);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_SR_P_COST)) $criteria->add(SiteReportsQFRPeer::QFR_SR_P_COST, $this->qfr_sr_p_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_SR_E_COST)) $criteria->add(SiteReportsQFRPeer::QFR_SR_E_COST, $this->qfr_sr_e_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_SR_PSC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_SR_PSC_COST, $this->qfr_sr_psc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_SR_ODC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_SR_ODC_COST, $this->qfr_sr_odc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_SR_IC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_SR_IC_COST, $this->qfr_sr_ic_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NR_P_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NR_P_COST, $this->qfr_nr_p_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NR_E_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NR_E_COST, $this->qfr_nr_e_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NR_PSC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NR_PSC_COST, $this->qfr_nr_psc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NR_ODC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NR_ODC_COST, $this->qfr_nr_odc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NR_IC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NR_IC_COST, $this->qfr_nr_ic_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_ITCA_P_COST)) $criteria->add(SiteReportsQFRPeer::QFR_ITCA_P_COST, $this->qfr_itca_p_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_ITCA_E_COST)) $criteria->add(SiteReportsQFRPeer::QFR_ITCA_E_COST, $this->qfr_itca_e_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_ITCA_PSC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_ITCA_PSC_COST, $this->qfr_itca_psc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_ITCA_ODC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_ITCA_ODC_COST, $this->qfr_itca_odc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_ITCA_IC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_ITCA_IC_COST, $this->qfr_itca_ic_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NEOT_P_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NEOT_P_COST, $this->qfr_neot_p_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NEOT_E_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NEOT_E_COST, $this->qfr_neot_e_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NEOT_PSC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NEOT_PSC_COST, $this->qfr_neot_psc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NEOT_ODC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NEOT_ODC_COST, $this->qfr_neot_odc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NEOT_IC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NEOT_IC_COST, $this->qfr_neot_ic_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_FEA_P_COST)) $criteria->add(SiteReportsQFRPeer::QFR_FEA_P_COST, $this->qfr_fea_p_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_FEA_E_COST)) $criteria->add(SiteReportsQFRPeer::QFR_FEA_E_COST, $this->qfr_fea_e_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_FEA_PSC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_FEA_PSC_COST, $this->qfr_fea_psc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_FEA_ODC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_FEA_ODC_COST, $this->qfr_fea_odc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_FEA_IC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_FEA_IC_COST, $this->qfr_fea_ic_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_AEM_P_COST)) $criteria->add(SiteReportsQFRPeer::QFR_AEM_P_COST, $this->qfr_aem_p_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_AEM_E_COST)) $criteria->add(SiteReportsQFRPeer::QFR_AEM_E_COST, $this->qfr_aem_e_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_AEM_PSC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_AEM_PSC_COST, $this->qfr_aem_psc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_AEM_ODC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_AEM_ODC_COST, $this->qfr_aem_odc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_AEM_IC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_AEM_IC_COST, $this->qfr_aem_ic_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NRS_P_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NRS_P_COST, $this->qfr_nrs_p_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NRS_E_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NRS_E_COST, $this->qfr_nrs_e_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NRS_PSC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NRS_PSC_COST, $this->qfr_nrs_psc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NRS_ODC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NRS_ODC_COST, $this->qfr_nrs_odc_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::QFR_NRS_IC_COST)) $criteria->add(SiteReportsQFRPeer::QFR_NRS_IC_COST, $this->qfr_nrs_ic_cost);
		if ($this->isColumnModified(SiteReportsQFRPeer::FY_BUDGET_SURS)) $criteria->add(SiteReportsQFRPeer::FY_BUDGET_SURS, $this->fy_budget_surs);
		if ($this->isColumnModified(SiteReportsQFRPeer::FY_BUDGET_SR)) $criteria->add(SiteReportsQFRPeer::FY_BUDGET_SR, $this->fy_budget_sr);
		if ($this->isColumnModified(SiteReportsQFRPeer::FY_BUDGET_NR)) $criteria->add(SiteReportsQFRPeer::FY_BUDGET_NR, $this->fy_budget_nr);
		if ($this->isColumnModified(SiteReportsQFRPeer::FY_BUDGET_ITCA)) $criteria->add(SiteReportsQFRPeer::FY_BUDGET_ITCA, $this->fy_budget_itca);
		if ($this->isColumnModified(SiteReportsQFRPeer::FY_BUDGET_FEA)) $criteria->add(SiteReportsQFRPeer::FY_BUDGET_FEA, $this->fy_budget_fea);
		if ($this->isColumnModified(SiteReportsQFRPeer::FY_BUDGET_NEOT)) $criteria->add(SiteReportsQFRPeer::FY_BUDGET_NEOT, $this->fy_budget_neot);
		if ($this->isColumnModified(SiteReportsQFRPeer::FY_BUDGET_AEM)) $criteria->add(SiteReportsQFRPeer::FY_BUDGET_AEM, $this->fy_budget_aem);
		if ($this->isColumnModified(SiteReportsQFRPeer::FY_BUDGET_NRS)) $criteria->add(SiteReportsQFRPeer::FY_BUDGET_NRS, $this->fy_budget_nrs);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q1RE_SURS)) $criteria->add(SiteReportsQFRPeer::Q1RE_SURS, $this->q1re_surs);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q1RE_SR)) $criteria->add(SiteReportsQFRPeer::Q1RE_SR, $this->q1re_sr);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q1RE_NR)) $criteria->add(SiteReportsQFRPeer::Q1RE_NR, $this->q1re_nr);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q1RE_ITCA)) $criteria->add(SiteReportsQFRPeer::Q1RE_ITCA, $this->q1re_itca);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q1RE_FEA)) $criteria->add(SiteReportsQFRPeer::Q1RE_FEA, $this->q1re_fea);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q1RE_NEOT)) $criteria->add(SiteReportsQFRPeer::Q1RE_NEOT, $this->q1re_neot);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q1RE_AEM)) $criteria->add(SiteReportsQFRPeer::Q1RE_AEM, $this->q1re_aem);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q1RE_NRS)) $criteria->add(SiteReportsQFRPeer::Q1RE_NRS, $this->q1re_nrs);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q2RE_SURS)) $criteria->add(SiteReportsQFRPeer::Q2RE_SURS, $this->q2re_surs);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q2RE_SR)) $criteria->add(SiteReportsQFRPeer::Q2RE_SR, $this->q2re_sr);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q2RE_NR)) $criteria->add(SiteReportsQFRPeer::Q2RE_NR, $this->q2re_nr);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q2RE_ITCA)) $criteria->add(SiteReportsQFRPeer::Q2RE_ITCA, $this->q2re_itca);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q2RE_FEA)) $criteria->add(SiteReportsQFRPeer::Q2RE_FEA, $this->q2re_fea);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q2RE_NEOT)) $criteria->add(SiteReportsQFRPeer::Q2RE_NEOT, $this->q2re_neot);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q2RE_AEM)) $criteria->add(SiteReportsQFRPeer::Q2RE_AEM, $this->q2re_aem);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q2RE_NRS)) $criteria->add(SiteReportsQFRPeer::Q2RE_NRS, $this->q2re_nrs);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q3RE_SURS)) $criteria->add(SiteReportsQFRPeer::Q3RE_SURS, $this->q3re_surs);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q3RE_SR)) $criteria->add(SiteReportsQFRPeer::Q3RE_SR, $this->q3re_sr);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q3RE_NR)) $criteria->add(SiteReportsQFRPeer::Q3RE_NR, $this->q3re_nr);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q3RE_ITCA)) $criteria->add(SiteReportsQFRPeer::Q3RE_ITCA, $this->q3re_itca);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q3RE_FEA)) $criteria->add(SiteReportsQFRPeer::Q3RE_FEA, $this->q3re_fea);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q3RE_NEOT)) $criteria->add(SiteReportsQFRPeer::Q3RE_NEOT, $this->q3re_neot);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q3RE_AEM)) $criteria->add(SiteReportsQFRPeer::Q3RE_AEM, $this->q3re_aem);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q3RE_NRS)) $criteria->add(SiteReportsQFRPeer::Q3RE_NRS, $this->q3re_nrs);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q4RE_SURS)) $criteria->add(SiteReportsQFRPeer::Q4RE_SURS, $this->q4re_surs);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q4RE_SR)) $criteria->add(SiteReportsQFRPeer::Q4RE_SR, $this->q4re_sr);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q4RE_NR)) $criteria->add(SiteReportsQFRPeer::Q4RE_NR, $this->q4re_nr);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q4RE_ITCA)) $criteria->add(SiteReportsQFRPeer::Q4RE_ITCA, $this->q4re_itca);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q4RE_FEA)) $criteria->add(SiteReportsQFRPeer::Q4RE_FEA, $this->q4re_fea);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q4RE_NEOT)) $criteria->add(SiteReportsQFRPeer::Q4RE_NEOT, $this->q4re_neot);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q4RE_AEM)) $criteria->add(SiteReportsQFRPeer::Q4RE_AEM, $this->q4re_aem);
		if ($this->isColumnModified(SiteReportsQFRPeer::Q4RE_NRS)) $criteria->add(SiteReportsQFRPeer::Q4RE_NRS, $this->q4re_nrs);
		if ($this->isColumnModified(SiteReportsQFRPeer::PQA_SURS)) $criteria->add(SiteReportsQFRPeer::PQA_SURS, $this->pqa_surs);
		if ($this->isColumnModified(SiteReportsQFRPeer::PQA_SR)) $criteria->add(SiteReportsQFRPeer::PQA_SR, $this->pqa_sr);
		if ($this->isColumnModified(SiteReportsQFRPeer::PQA_NR)) $criteria->add(SiteReportsQFRPeer::PQA_NR, $this->pqa_nr);
		if ($this->isColumnModified(SiteReportsQFRPeer::PQA_ITCA)) $criteria->add(SiteReportsQFRPeer::PQA_ITCA, $this->pqa_itca);
		if ($this->isColumnModified(SiteReportsQFRPeer::PQA_FEA)) $criteria->add(SiteReportsQFRPeer::PQA_FEA, $this->pqa_fea);
		if ($this->isColumnModified(SiteReportsQFRPeer::PQA_NEOT)) $criteria->add(SiteReportsQFRPeer::PQA_NEOT, $this->pqa_neot);
		if ($this->isColumnModified(SiteReportsQFRPeer::PQA_AEM)) $criteria->add(SiteReportsQFRPeer::PQA_AEM, $this->pqa_aem);
		if ($this->isColumnModified(SiteReportsQFRPeer::PQA_NRS)) $criteria->add(SiteReportsQFRPeer::PQA_NRS, $this->pqa_nrs);
		if ($this->isColumnModified(SiteReportsQFRPeer::CQE_SURS)) $criteria->add(SiteReportsQFRPeer::CQE_SURS, $this->cqe_surs);
		if ($this->isColumnModified(SiteReportsQFRPeer::CQE_SR)) $criteria->add(SiteReportsQFRPeer::CQE_SR, $this->cqe_sr);
		if ($this->isColumnModified(SiteReportsQFRPeer::CQE_NR)) $criteria->add(SiteReportsQFRPeer::CQE_NR, $this->cqe_nr);
		if ($this->isColumnModified(SiteReportsQFRPeer::CQE_ITCA)) $criteria->add(SiteReportsQFRPeer::CQE_ITCA, $this->cqe_itca);
		if ($this->isColumnModified(SiteReportsQFRPeer::CQE_FEA)) $criteria->add(SiteReportsQFRPeer::CQE_FEA, $this->cqe_fea);
		if ($this->isColumnModified(SiteReportsQFRPeer::CQE_NEOT)) $criteria->add(SiteReportsQFRPeer::CQE_NEOT, $this->cqe_neot);
		if ($this->isColumnModified(SiteReportsQFRPeer::CQE_AEM)) $criteria->add(SiteReportsQFRPeer::CQE_AEM, $this->cqe_aem);
		if ($this->isColumnModified(SiteReportsQFRPeer::CQE_NRS)) $criteria->add(SiteReportsQFRPeer::CQE_NRS, $this->cqe_nrs);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP1_P)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP1_P, $this->supbud_sup1_p);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP1_E)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP1_E, $this->supbud_sup1_e);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP1_PSC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP1_PSC, $this->supbud_sup1_psc);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP1_ODC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP1_ODC, $this->supbud_sup1_odc);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP1_IC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP1_IC, $this->supbud_sup1_ic);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP1_SA)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP1_SA, $this->supbud_sup1_sa);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP2_P)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP2_P, $this->supbud_sup2_p);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP2_E)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP2_E, $this->supbud_sup2_e);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP2_PSC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP2_PSC, $this->supbud_sup2_psc);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP2_ODC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP2_ODC, $this->supbud_sup2_odc);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP2_IC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP2_IC, $this->supbud_sup2_ic);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP2_SA)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP2_SA, $this->supbud_sup2_sa);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP3_P)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP3_P, $this->supbud_sup3_p);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP3_E)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP3_E, $this->supbud_sup3_e);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP3_PSC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP3_PSC, $this->supbud_sup3_psc);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP3_ODC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP3_ODC, $this->supbud_sup3_odc);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP3_IC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP3_IC, $this->supbud_sup3_ic);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP3_SA)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP3_SA, $this->supbud_sup3_sa);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP4_P)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP4_P, $this->supbud_sup4_p);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP4_E)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP4_E, $this->supbud_sup4_e);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP4_PSC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP4_PSC, $this->supbud_sup4_psc);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP4_ODC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP4_ODC, $this->supbud_sup4_odc);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP4_IC)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP4_IC, $this->supbud_sup4_ic);
		if ($this->isColumnModified(SiteReportsQFRPeer::SUPBUD_SUP4_SA)) $criteria->add(SiteReportsQFRPeer::SUPBUD_SUP4_SA, $this->supbud_sup4_sa);
		if ($this->isColumnModified(SiteReportsQFRPeer::PI_BEG_BAL)) $criteria->add(SiteReportsQFRPeer::PI_BEG_BAL, $this->pi_beg_bal);
		if ($this->isColumnModified(SiteReportsQFRPeer::PI_PIR)) $criteria->add(SiteReportsQFRPeer::PI_PIR, $this->pi_pir);
		if ($this->isColumnModified(SiteReportsQFRPeer::PI_PIE)) $criteria->add(SiteReportsQFRPeer::PI_PIE, $this->pi_pie);
		if ($this->isColumnModified(SiteReportsQFRPeer::PI_NAR)) $criteria->add(SiteReportsQFRPeer::PI_NAR, $this->pi_nar);
		if ($this->isColumnModified(SiteReportsQFRPeer::CREATED_BY)) $criteria->add(SiteReportsQFRPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(SiteReportsQFRPeer::CREATED_ON)) $criteria->add(SiteReportsQFRPeer::CREATED_ON, $this->created_on);
		if ($this->isColumnModified(SiteReportsQFRPeer::UPDATED_BY)) $criteria->add(SiteReportsQFRPeer::UPDATED_BY, $this->updated_by);
		if ($this->isColumnModified(SiteReportsQFRPeer::UPDATED_ON)) $criteria->add(SiteReportsQFRPeer::UPDATED_ON, $this->updated_on);

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
		$criteria = new Criteria(SiteReportsQFRPeer::DATABASE_NAME);

		$criteria->add(SiteReportsQFRPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SiteReportsQFR (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setFACILITY_ID($this->facility_id);

		$copyObj->setYEAR($this->year);

		$copyObj->setQUARTER($this->quarter);

		$copyObj->setPREPARED_BY($this->prepared_by);

		$copyObj->setPREPARERS_TITLE($this->preparers_title);

		$copyObj->setPREPARED_DATE($this->prepared_date);

		$copyObj->setREPORT_PERIOD($this->report_period);

		$copyObj->setSUBAWARDED_FUNDED_AMT($this->subawarded_funded_amt);

		$copyObj->setQFR_SR_P_COST($this->qfr_sr_p_cost);

		$copyObj->setQFR_SR_E_COST($this->qfr_sr_e_cost);

		$copyObj->setQFR_SR_PSC_COST($this->qfr_sr_psc_cost);

		$copyObj->setQFR_SR_ODC_COST($this->qfr_sr_odc_cost);

		$copyObj->setQFR_SR_IC_COST($this->qfr_sr_ic_cost);

		$copyObj->setQFR_NR_P_COST($this->qfr_nr_p_cost);

		$copyObj->setQFR_NR_E_COST($this->qfr_nr_e_cost);

		$copyObj->setQFR_NR_PSC_COST($this->qfr_nr_psc_cost);

		$copyObj->setQFR_NR_ODC_COST($this->qfr_nr_odc_cost);

		$copyObj->setQFR_NR_IC_COST($this->qfr_nr_ic_cost);

		$copyObj->setQFR_ITCA_P_COST($this->qfr_itca_p_cost);

		$copyObj->setQFR_ITCA_E_COST($this->qfr_itca_e_cost);

		$copyObj->setQFR_ITCA_PSC_COST($this->qfr_itca_psc_cost);

		$copyObj->setQFR_ITCA_ODC_COST($this->qfr_itca_odc_cost);

		$copyObj->setQFR_ITCA_IC_COST($this->qfr_itca_ic_cost);

		$copyObj->setQFR_NEOT_P_COST($this->qfr_neot_p_cost);

		$copyObj->setQFR_NEOT_E_COST($this->qfr_neot_e_cost);

		$copyObj->setQFR_NEOT_PSC_COST($this->qfr_neot_psc_cost);

		$copyObj->setQFR_NEOT_ODC_COST($this->qfr_neot_odc_cost);

		$copyObj->setQFR_NEOT_IC_COST($this->qfr_neot_ic_cost);

		$copyObj->setQFR_FEA_P_COST($this->qfr_fea_p_cost);

		$copyObj->setQFR_FEA_E_COST($this->qfr_fea_e_cost);

		$copyObj->setQFR_FEA_PSC_COST($this->qfr_fea_psc_cost);

		$copyObj->setQFR_FEA_ODC_COST($this->qfr_fea_odc_cost);

		$copyObj->setQFR_FEA_IC_COST($this->qfr_fea_ic_cost);

		$copyObj->setQFR_AEM_P_COST($this->qfr_aem_p_cost);

		$copyObj->setQFR_AEM_E_COST($this->qfr_aem_e_cost);

		$copyObj->setQFR_AEM_PSC_COST($this->qfr_aem_psc_cost);

		$copyObj->setQFR_AEM_ODC_COST($this->qfr_aem_odc_cost);

		$copyObj->setQFR_AEM_IC_COST($this->qfr_aem_ic_cost);

		$copyObj->setQFR_NRS_P_COST($this->qfr_nrs_p_cost);

		$copyObj->setQFR_NRS_E_COST($this->qfr_nrs_e_cost);

		$copyObj->setQFR_NRS_PSC_COST($this->qfr_nrs_psc_cost);

		$copyObj->setQFR_NRS_ODC_COST($this->qfr_nrs_odc_cost);

		$copyObj->setQFR_NRS_IC_COST($this->qfr_nrs_ic_cost);

		$copyObj->setFY_BUDGET_SURS($this->fy_budget_surs);

		$copyObj->setFY_BUDGET_SR($this->fy_budget_sr);

		$copyObj->setFY_BUDGET_NR($this->fy_budget_nr);

		$copyObj->setFY_BUDGET_ITCA($this->fy_budget_itca);

		$copyObj->setFY_BUDGET_FEA($this->fy_budget_fea);

		$copyObj->setFY_BUDGET_NEOT($this->fy_budget_neot);

		$copyObj->setFY_BUDGET_AEM($this->fy_budget_aem);

		$copyObj->setFY_BUDGET_NRS($this->fy_budget_nrs);

		$copyObj->setQ1RE_SURS($this->q1re_surs);

		$copyObj->setQ1RE_SR($this->q1re_sr);

		$copyObj->setQ1RE_NR($this->q1re_nr);

		$copyObj->setQ1RE_ITCA($this->q1re_itca);

		$copyObj->setQ1RE_FEA($this->q1re_fea);

		$copyObj->setQ1RE_NEOT($this->q1re_neot);

		$copyObj->setQ1RE_AEM($this->q1re_aem);

		$copyObj->setQ1RE_NRS($this->q1re_nrs);

		$copyObj->setQ2RE_SURS($this->q2re_surs);

		$copyObj->setQ2RE_SR($this->q2re_sr);

		$copyObj->setQ2RE_NR($this->q2re_nr);

		$copyObj->setQ2RE_ITCA($this->q2re_itca);

		$copyObj->setQ2RE_FEA($this->q2re_fea);

		$copyObj->setQ2RE_NEOT($this->q2re_neot);

		$copyObj->setQ2RE_AEM($this->q2re_aem);

		$copyObj->setQ2RE_NRS($this->q2re_nrs);

		$copyObj->setQ3RE_SURS($this->q3re_surs);

		$copyObj->setQ3RE_SR($this->q3re_sr);

		$copyObj->setQ3RE_NR($this->q3re_nr);

		$copyObj->setQ3RE_ITCA($this->q3re_itca);

		$copyObj->setQ3RE_FEA($this->q3re_fea);

		$copyObj->setQ3RE_NEOT($this->q3re_neot);

		$copyObj->setQ3RE_AEM($this->q3re_aem);

		$copyObj->setQ3RE_NRS($this->q3re_nrs);

		$copyObj->setQ4RE_SURS($this->q4re_surs);

		$copyObj->setQ4RE_SR($this->q4re_sr);

		$copyObj->setQ4RE_NR($this->q4re_nr);

		$copyObj->setQ4RE_ITCA($this->q4re_itca);

		$copyObj->setQ4RE_FEA($this->q4re_fea);

		$copyObj->setQ4RE_NEOT($this->q4re_neot);

		$copyObj->setQ4RE_AEM($this->q4re_aem);

		$copyObj->setQ4RE_NRS($this->q4re_nrs);

		$copyObj->setPQA_SURS($this->pqa_surs);

		$copyObj->setPQA_SR($this->pqa_sr);

		$copyObj->setPQA_NR($this->pqa_nr);

		$copyObj->setPQA_ITCA($this->pqa_itca);

		$copyObj->setPQA_FEA($this->pqa_fea);

		$copyObj->setPQA_NEOT($this->pqa_neot);

		$copyObj->setPQA_AEM($this->pqa_aem);

		$copyObj->setPQA_NRS($this->pqa_nrs);

		$copyObj->setCQE_SURS($this->cqe_surs);

		$copyObj->setCQE_SR($this->cqe_sr);

		$copyObj->setCQE_NR($this->cqe_nr);

		$copyObj->setCQE_ITCA($this->cqe_itca);

		$copyObj->setCQE_FEA($this->cqe_fea);

		$copyObj->setCQE_NEOT($this->cqe_neot);

		$copyObj->setCQE_AEM($this->cqe_aem);

		$copyObj->setCQE_NRS($this->cqe_nrs);

		$copyObj->setSUPBUD_SUP1_P($this->supbud_sup1_p);

		$copyObj->setSUPBUD_SUP1_E($this->supbud_sup1_e);

		$copyObj->setSUPBUD_SUP1_PSC($this->supbud_sup1_psc);

		$copyObj->setSUPBUD_SUP1_ODC($this->supbud_sup1_odc);

		$copyObj->setSUPBUD_SUP1_IC($this->supbud_sup1_ic);

		$copyObj->setSUPBUD_SUP1_SA($this->supbud_sup1_sa);

		$copyObj->setSUPBUD_SUP2_P($this->supbud_sup2_p);

		$copyObj->setSUPBUD_SUP2_E($this->supbud_sup2_e);

		$copyObj->setSUPBUD_SUP2_PSC($this->supbud_sup2_psc);

		$copyObj->setSUPBUD_SUP2_ODC($this->supbud_sup2_odc);

		$copyObj->setSUPBUD_SUP2_IC($this->supbud_sup2_ic);

		$copyObj->setSUPBUD_SUP2_SA($this->supbud_sup2_sa);

		$copyObj->setSUPBUD_SUP3_P($this->supbud_sup3_p);

		$copyObj->setSUPBUD_SUP3_E($this->supbud_sup3_e);

		$copyObj->setSUPBUD_SUP3_PSC($this->supbud_sup3_psc);

		$copyObj->setSUPBUD_SUP3_ODC($this->supbud_sup3_odc);

		$copyObj->setSUPBUD_SUP3_IC($this->supbud_sup3_ic);

		$copyObj->setSUPBUD_SUP3_SA($this->supbud_sup3_sa);

		$copyObj->setSUPBUD_SUP4_P($this->supbud_sup4_p);

		$copyObj->setSUPBUD_SUP4_E($this->supbud_sup4_e);

		$copyObj->setSUPBUD_SUP4_PSC($this->supbud_sup4_psc);

		$copyObj->setSUPBUD_SUP4_ODC($this->supbud_sup4_odc);

		$copyObj->setSUPBUD_SUP4_IC($this->supbud_sup4_ic);

		$copyObj->setSUPBUD_SUP4_SA($this->supbud_sup4_sa);

		$copyObj->setPI_BEG_BAL($this->pi_beg_bal);

		$copyObj->setPI_PIR($this->pi_pir);

		$copyObj->setPI_PIE($this->pi_pie);

		$copyObj->setPI_NAR($this->pi_nar);

		$copyObj->setCREATED_BY($this->created_by);

		$copyObj->setCREATED_ON($this->created_on);

		$copyObj->setUPDATED_BY($this->updated_by);

		$copyObj->setUPDATED_ON($this->updated_on);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getSiteReportsQFREPcds() as $relObj) {
				$copyObj->addSiteReportsQFREPcd($relObj->copy($deepCopy));
			}

			foreach($this->getSiteReportsQFRProjects() as $relObj) {
				$copyObj->addSiteReportsQFRProject($relObj->copy($deepCopy));
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
	 * @return     SiteReportsQFR Clone of current object.
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
	 * @return     SiteReportsQFRPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SiteReportsQFRPeer();
		}
		return self::$peer;
	}

	/**
	 * Temporary storage of collSiteReportsQFREPcds to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSiteReportsQFREPcds()
	{
		if ($this->collSiteReportsQFREPcds === null) {
			$this->collSiteReportsQFREPcds = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SiteReportsQFR has previously
	 * been saved, it will retrieve related SiteReportsQFREPcds from storage.
	 * If this SiteReportsQFR is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSiteReportsQFREPcds($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSiteReportsQFREPcdPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSiteReportsQFREPcds === null) {
			if ($this->isNew()) {
			   $this->collSiteReportsQFREPcds = array();
			} else {

				$criteria->add(SiteReportsQFREPcdPeer::QFR_ID, $this->getID());

				SiteReportsQFREPcdPeer::addSelectColumns($criteria);
				$this->collSiteReportsQFREPcds = SiteReportsQFREPcdPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SiteReportsQFREPcdPeer::QFR_ID, $this->getID());

				SiteReportsQFREPcdPeer::addSelectColumns($criteria);
				if (!isset($this->lastSiteReportsQFREPcdCriteria) || !$this->lastSiteReportsQFREPcdCriteria->equals($criteria)) {
					$this->collSiteReportsQFREPcds = SiteReportsQFREPcdPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSiteReportsQFREPcdCriteria = $criteria;
		return $this->collSiteReportsQFREPcds;
	}

	/**
	 * Returns the number of related SiteReportsQFREPcds.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSiteReportsQFREPcds($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSiteReportsQFREPcdPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SiteReportsQFREPcdPeer::QFR_ID, $this->getID());

		return SiteReportsQFREPcdPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SiteReportsQFREPcd object to this object
	 * through the SiteReportsQFREPcd foreign key attribute
	 *
	 * @param      SiteReportsQFREPcd $l SiteReportsQFREPcd
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSiteReportsQFREPcd(SiteReportsQFREPcd $l)
	{
		$this->collSiteReportsQFREPcds[] = $l;
		$l->setSiteReportsQFR($this);
	}

	/**
	 * Temporary storage of collSiteReportsQFRProjects to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSiteReportsQFRProjects()
	{
		if ($this->collSiteReportsQFRProjects === null) {
			$this->collSiteReportsQFRProjects = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this SiteReportsQFR has previously
	 * been saved, it will retrieve related SiteReportsQFRProjects from storage.
	 * If this SiteReportsQFR is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSiteReportsQFRProjects($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSiteReportsQFRProjectPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSiteReportsQFRProjects === null) {
			if ($this->isNew()) {
			   $this->collSiteReportsQFRProjects = array();
			} else {

				$criteria->add(SiteReportsQFRProjectPeer::QFR_ID, $this->getID());

				SiteReportsQFRProjectPeer::addSelectColumns($criteria);
				$this->collSiteReportsQFRProjects = SiteReportsQFRProjectPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SiteReportsQFRProjectPeer::QFR_ID, $this->getID());

				SiteReportsQFRProjectPeer::addSelectColumns($criteria);
				if (!isset($this->lastSiteReportsQFRProjectCriteria) || !$this->lastSiteReportsQFRProjectCriteria->equals($criteria)) {
					$this->collSiteReportsQFRProjects = SiteReportsQFRProjectPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSiteReportsQFRProjectCriteria = $criteria;
		return $this->collSiteReportsQFRProjects;
	}

	/**
	 * Returns the number of related SiteReportsQFRProjects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSiteReportsQFRProjects($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSiteReportsQFRProjectPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SiteReportsQFRProjectPeer::QFR_ID, $this->getID());

		return SiteReportsQFRProjectPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SiteReportsQFRProject object to this object
	 * through the SiteReportsQFRProject foreign key attribute
	 *
	 * @param      SiteReportsQFRProject $l SiteReportsQFRProject
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSiteReportsQFRProject(SiteReportsQFRProject $l)
	{
		$this->collSiteReportsQFRProjects[] = $l;
		$l->setSiteReportsQFR($this);
	}

} // BaseSiteReportsQFR
