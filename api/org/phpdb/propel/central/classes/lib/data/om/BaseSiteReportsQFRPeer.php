<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by SiteReportsQFRPeer::getOMClass()
include_once 'lib/data/SiteReportsQFR.php';

/**
 * Base static class for performing query and update operations on the 'SITEREPORTS_QFR' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSiteReportsQFRPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'SITEREPORTS_QFR';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.SiteReportsQFR';

	/** The total number of columns. */
	const NUM_COLUMNS = 132;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'SITEREPORTS_QFR.ID';

	/** the column name for the FACILITY_ID field */
	const FACILITY_ID = 'SITEREPORTS_QFR.FACILITY_ID';

	/** the column name for the YEAR field */
	const YEAR = 'SITEREPORTS_QFR.YEAR';

	/** the column name for the QUARTER field */
	const QUARTER = 'SITEREPORTS_QFR.QUARTER';

	/** the column name for the PREPARED_BY field */
	const PREPARED_BY = 'SITEREPORTS_QFR.PREPARED_BY';

	/** the column name for the PREPARERS_TITLE field */
	const PREPARERS_TITLE = 'SITEREPORTS_QFR.PREPARERS_TITLE';

	/** the column name for the PREPARED_DATE field */
	const PREPARED_DATE = 'SITEREPORTS_QFR.PREPARED_DATE';

	/** the column name for the REPORT_PERIOD field */
	const REPORT_PERIOD = 'SITEREPORTS_QFR.REPORT_PERIOD';

	/** the column name for the SUBAWARDED_FUNDED_AMT field */
	const SUBAWARDED_FUNDED_AMT = 'SITEREPORTS_QFR.SUBAWARDED_FUNDED_AMT';

	/** the column name for the QFR_SR_P_COST field */
	const QFR_SR_P_COST = 'SITEREPORTS_QFR.QFR_SR_P_COST';

	/** the column name for the QFR_SR_E_COST field */
	const QFR_SR_E_COST = 'SITEREPORTS_QFR.QFR_SR_E_COST';

	/** the column name for the QFR_SR_PSC_COST field */
	const QFR_SR_PSC_COST = 'SITEREPORTS_QFR.QFR_SR_PSC_COST';

	/** the column name for the QFR_SR_ODC_COST field */
	const QFR_SR_ODC_COST = 'SITEREPORTS_QFR.QFR_SR_ODC_COST';

	/** the column name for the QFR_SR_IC_COST field */
	const QFR_SR_IC_COST = 'SITEREPORTS_QFR.QFR_SR_IC_COST';

	/** the column name for the QFR_NR_P_COST field */
	const QFR_NR_P_COST = 'SITEREPORTS_QFR.QFR_NR_P_COST';

	/** the column name for the QFR_NR_E_COST field */
	const QFR_NR_E_COST = 'SITEREPORTS_QFR.QFR_NR_E_COST';

	/** the column name for the QFR_NR_PSC_COST field */
	const QFR_NR_PSC_COST = 'SITEREPORTS_QFR.QFR_NR_PSC_COST';

	/** the column name for the QFR_NR_ODC_COST field */
	const QFR_NR_ODC_COST = 'SITEREPORTS_QFR.QFR_NR_ODC_COST';

	/** the column name for the QFR_NR_IC_COST field */
	const QFR_NR_IC_COST = 'SITEREPORTS_QFR.QFR_NR_IC_COST';

	/** the column name for the QFR_ITCA_P_COST field */
	const QFR_ITCA_P_COST = 'SITEREPORTS_QFR.QFR_ITCA_P_COST';

	/** the column name for the QFR_ITCA_E_COST field */
	const QFR_ITCA_E_COST = 'SITEREPORTS_QFR.QFR_ITCA_E_COST';

	/** the column name for the QFR_ITCA_PSC_COST field */
	const QFR_ITCA_PSC_COST = 'SITEREPORTS_QFR.QFR_ITCA_PSC_COST';

	/** the column name for the QFR_ITCA_ODC_COST field */
	const QFR_ITCA_ODC_COST = 'SITEREPORTS_QFR.QFR_ITCA_ODC_COST';

	/** the column name for the QFR_ITCA_IC_COST field */
	const QFR_ITCA_IC_COST = 'SITEREPORTS_QFR.QFR_ITCA_IC_COST';

	/** the column name for the QFR_NEOT_P_COST field */
	const QFR_NEOT_P_COST = 'SITEREPORTS_QFR.QFR_NEOT_P_COST';

	/** the column name for the QFR_NEOT_E_COST field */
	const QFR_NEOT_E_COST = 'SITEREPORTS_QFR.QFR_NEOT_E_COST';

	/** the column name for the QFR_NEOT_PSC_COST field */
	const QFR_NEOT_PSC_COST = 'SITEREPORTS_QFR.QFR_NEOT_PSC_COST';

	/** the column name for the QFR_NEOT_ODC_COST field */
	const QFR_NEOT_ODC_COST = 'SITEREPORTS_QFR.QFR_NEOT_ODC_COST';

	/** the column name for the QFR_NEOT_IC_COST field */
	const QFR_NEOT_IC_COST = 'SITEREPORTS_QFR.QFR_NEOT_IC_COST';

	/** the column name for the QFR_FEA_P_COST field */
	const QFR_FEA_P_COST = 'SITEREPORTS_QFR.QFR_FEA_P_COST';

	/** the column name for the QFR_FEA_E_COST field */
	const QFR_FEA_E_COST = 'SITEREPORTS_QFR.QFR_FEA_E_COST';

	/** the column name for the QFR_FEA_PSC_COST field */
	const QFR_FEA_PSC_COST = 'SITEREPORTS_QFR.QFR_FEA_PSC_COST';

	/** the column name for the QFR_FEA_ODC_COST field */
	const QFR_FEA_ODC_COST = 'SITEREPORTS_QFR.QFR_FEA_ODC_COST';

	/** the column name for the QFR_FEA_IC_COST field */
	const QFR_FEA_IC_COST = 'SITEREPORTS_QFR.QFR_FEA_IC_COST';

	/** the column name for the QFR_AEM_P_COST field */
	const QFR_AEM_P_COST = 'SITEREPORTS_QFR.QFR_AEM_P_COST';

	/** the column name for the QFR_AEM_E_COST field */
	const QFR_AEM_E_COST = 'SITEREPORTS_QFR.QFR_AEM_E_COST';

	/** the column name for the QFR_AEM_PSC_COST field */
	const QFR_AEM_PSC_COST = 'SITEREPORTS_QFR.QFR_AEM_PSC_COST';

	/** the column name for the QFR_AEM_ODC_COST field */
	const QFR_AEM_ODC_COST = 'SITEREPORTS_QFR.QFR_AEM_ODC_COST';

	/** the column name for the QFR_AEM_IC_COST field */
	const QFR_AEM_IC_COST = 'SITEREPORTS_QFR.QFR_AEM_IC_COST';

	/** the column name for the QFR_NRS_P_COST field */
	const QFR_NRS_P_COST = 'SITEREPORTS_QFR.QFR_NRS_P_COST';

	/** the column name for the QFR_NRS_E_COST field */
	const QFR_NRS_E_COST = 'SITEREPORTS_QFR.QFR_NRS_E_COST';

	/** the column name for the QFR_NRS_PSC_COST field */
	const QFR_NRS_PSC_COST = 'SITEREPORTS_QFR.QFR_NRS_PSC_COST';

	/** the column name for the QFR_NRS_ODC_COST field */
	const QFR_NRS_ODC_COST = 'SITEREPORTS_QFR.QFR_NRS_ODC_COST';

	/** the column name for the QFR_NRS_IC_COST field */
	const QFR_NRS_IC_COST = 'SITEREPORTS_QFR.QFR_NRS_IC_COST';

	/** the column name for the FY_BUDGET_SURS field */
	const FY_BUDGET_SURS = 'SITEREPORTS_QFR.FY_BUDGET_SURS';

	/** the column name for the FY_BUDGET_SR field */
	const FY_BUDGET_SR = 'SITEREPORTS_QFR.FY_BUDGET_SR';

	/** the column name for the FY_BUDGET_NR field */
	const FY_BUDGET_NR = 'SITEREPORTS_QFR.FY_BUDGET_NR';

	/** the column name for the FY_BUDGET_ITCA field */
	const FY_BUDGET_ITCA = 'SITEREPORTS_QFR.FY_BUDGET_ITCA';

	/** the column name for the FY_BUDGET_FEA field */
	const FY_BUDGET_FEA = 'SITEREPORTS_QFR.FY_BUDGET_FEA';

	/** the column name for the FY_BUDGET_NEOT field */
	const FY_BUDGET_NEOT = 'SITEREPORTS_QFR.FY_BUDGET_NEOT';

	/** the column name for the FY_BUDGET_AEM field */
	const FY_BUDGET_AEM = 'SITEREPORTS_QFR.FY_BUDGET_AEM';

	/** the column name for the FY_BUDGET_NRS field */
	const FY_BUDGET_NRS = 'SITEREPORTS_QFR.FY_BUDGET_NRS';

	/** the column name for the Q1RE_SURS field */
	const Q1RE_SURS = 'SITEREPORTS_QFR.Q1RE_SURS';

	/** the column name for the Q1RE_SR field */
	const Q1RE_SR = 'SITEREPORTS_QFR.Q1RE_SR';

	/** the column name for the Q1RE_NR field */
	const Q1RE_NR = 'SITEREPORTS_QFR.Q1RE_NR';

	/** the column name for the Q1RE_ITCA field */
	const Q1RE_ITCA = 'SITEREPORTS_QFR.Q1RE_ITCA';

	/** the column name for the Q1RE_FEA field */
	const Q1RE_FEA = 'SITEREPORTS_QFR.Q1RE_FEA';

	/** the column name for the Q1RE_NEOT field */
	const Q1RE_NEOT = 'SITEREPORTS_QFR.Q1RE_NEOT';

	/** the column name for the Q1RE_AEM field */
	const Q1RE_AEM = 'SITEREPORTS_QFR.Q1RE_AEM';

	/** the column name for the Q1RE_NRS field */
	const Q1RE_NRS = 'SITEREPORTS_QFR.Q1RE_NRS';

	/** the column name for the Q2RE_SURS field */
	const Q2RE_SURS = 'SITEREPORTS_QFR.Q2RE_SURS';

	/** the column name for the Q2RE_SR field */
	const Q2RE_SR = 'SITEREPORTS_QFR.Q2RE_SR';

	/** the column name for the Q2RE_NR field */
	const Q2RE_NR = 'SITEREPORTS_QFR.Q2RE_NR';

	/** the column name for the Q2RE_ITCA field */
	const Q2RE_ITCA = 'SITEREPORTS_QFR.Q2RE_ITCA';

	/** the column name for the Q2RE_FEA field */
	const Q2RE_FEA = 'SITEREPORTS_QFR.Q2RE_FEA';

	/** the column name for the Q2RE_NEOT field */
	const Q2RE_NEOT = 'SITEREPORTS_QFR.Q2RE_NEOT';

	/** the column name for the Q2RE_AEM field */
	const Q2RE_AEM = 'SITEREPORTS_QFR.Q2RE_AEM';

	/** the column name for the Q2RE_NRS field */
	const Q2RE_NRS = 'SITEREPORTS_QFR.Q2RE_NRS';

	/** the column name for the Q3RE_SURS field */
	const Q3RE_SURS = 'SITEREPORTS_QFR.Q3RE_SURS';

	/** the column name for the Q3RE_SR field */
	const Q3RE_SR = 'SITEREPORTS_QFR.Q3RE_SR';

	/** the column name for the Q3RE_NR field */
	const Q3RE_NR = 'SITEREPORTS_QFR.Q3RE_NR';

	/** the column name for the Q3RE_ITCA field */
	const Q3RE_ITCA = 'SITEREPORTS_QFR.Q3RE_ITCA';

	/** the column name for the Q3RE_FEA field */
	const Q3RE_FEA = 'SITEREPORTS_QFR.Q3RE_FEA';

	/** the column name for the Q3RE_NEOT field */
	const Q3RE_NEOT = 'SITEREPORTS_QFR.Q3RE_NEOT';

	/** the column name for the Q3RE_AEM field */
	const Q3RE_AEM = 'SITEREPORTS_QFR.Q3RE_AEM';

	/** the column name for the Q3RE_NRS field */
	const Q3RE_NRS = 'SITEREPORTS_QFR.Q3RE_NRS';

	/** the column name for the Q4RE_SURS field */
	const Q4RE_SURS = 'SITEREPORTS_QFR.Q4RE_SURS';

	/** the column name for the Q4RE_SR field */
	const Q4RE_SR = 'SITEREPORTS_QFR.Q4RE_SR';

	/** the column name for the Q4RE_NR field */
	const Q4RE_NR = 'SITEREPORTS_QFR.Q4RE_NR';

	/** the column name for the Q4RE_ITCA field */
	const Q4RE_ITCA = 'SITEREPORTS_QFR.Q4RE_ITCA';

	/** the column name for the Q4RE_FEA field */
	const Q4RE_FEA = 'SITEREPORTS_QFR.Q4RE_FEA';

	/** the column name for the Q4RE_NEOT field */
	const Q4RE_NEOT = 'SITEREPORTS_QFR.Q4RE_NEOT';

	/** the column name for the Q4RE_AEM field */
	const Q4RE_AEM = 'SITEREPORTS_QFR.Q4RE_AEM';

	/** the column name for the Q4RE_NRS field */
	const Q4RE_NRS = 'SITEREPORTS_QFR.Q4RE_NRS';

	/** the column name for the PQA_SURS field */
	const PQA_SURS = 'SITEREPORTS_QFR.PQA_SURS';

	/** the column name for the PQA_SR field */
	const PQA_SR = 'SITEREPORTS_QFR.PQA_SR';

	/** the column name for the PQA_NR field */
	const PQA_NR = 'SITEREPORTS_QFR.PQA_NR';

	/** the column name for the PQA_ITCA field */
	const PQA_ITCA = 'SITEREPORTS_QFR.PQA_ITCA';

	/** the column name for the PQA_FEA field */
	const PQA_FEA = 'SITEREPORTS_QFR.PQA_FEA';

	/** the column name for the PQA_NEOT field */
	const PQA_NEOT = 'SITEREPORTS_QFR.PQA_NEOT';

	/** the column name for the PQA_AEM field */
	const PQA_AEM = 'SITEREPORTS_QFR.PQA_AEM';

	/** the column name for the PQA_NRS field */
	const PQA_NRS = 'SITEREPORTS_QFR.PQA_NRS';

	/** the column name for the CQE_SURS field */
	const CQE_SURS = 'SITEREPORTS_QFR.CQE_SURS';

	/** the column name for the CQE_SR field */
	const CQE_SR = 'SITEREPORTS_QFR.CQE_SR';

	/** the column name for the CQE_NR field */
	const CQE_NR = 'SITEREPORTS_QFR.CQE_NR';

	/** the column name for the CQE_ITCA field */
	const CQE_ITCA = 'SITEREPORTS_QFR.CQE_ITCA';

	/** the column name for the CQE_FEA field */
	const CQE_FEA = 'SITEREPORTS_QFR.CQE_FEA';

	/** the column name for the CQE_NEOT field */
	const CQE_NEOT = 'SITEREPORTS_QFR.CQE_NEOT';

	/** the column name for the CQE_AEM field */
	const CQE_AEM = 'SITEREPORTS_QFR.CQE_AEM';

	/** the column name for the CQE_NRS field */
	const CQE_NRS = 'SITEREPORTS_QFR.CQE_NRS';

	/** the column name for the SUPBUD_SUP1_P field */
	const SUPBUD_SUP1_P = 'SITEREPORTS_QFR.SUPBUD_SUP1_P';

	/** the column name for the SUPBUD_SUP1_E field */
	const SUPBUD_SUP1_E = 'SITEREPORTS_QFR.SUPBUD_SUP1_E';

	/** the column name for the SUPBUD_SUP1_PSC field */
	const SUPBUD_SUP1_PSC = 'SITEREPORTS_QFR.SUPBUD_SUP1_PSC';

	/** the column name for the SUPBUD_SUP1_ODC field */
	const SUPBUD_SUP1_ODC = 'SITEREPORTS_QFR.SUPBUD_SUP1_ODC';

	/** the column name for the SUPBUD_SUP1_IC field */
	const SUPBUD_SUP1_IC = 'SITEREPORTS_QFR.SUPBUD_SUP1_IC';

	/** the column name for the SUPBUD_SUP1_SA field */
	const SUPBUD_SUP1_SA = 'SITEREPORTS_QFR.SUPBUD_SUP1_SA';

	/** the column name for the SUPBUD_SUP2_P field */
	const SUPBUD_SUP2_P = 'SITEREPORTS_QFR.SUPBUD_SUP2_P';

	/** the column name for the SUPBUD_SUP2_E field */
	const SUPBUD_SUP2_E = 'SITEREPORTS_QFR.SUPBUD_SUP2_E';

	/** the column name for the SUPBUD_SUP2_PSC field */
	const SUPBUD_SUP2_PSC = 'SITEREPORTS_QFR.SUPBUD_SUP2_PSC';

	/** the column name for the SUPBUD_SUP2_ODC field */
	const SUPBUD_SUP2_ODC = 'SITEREPORTS_QFR.SUPBUD_SUP2_ODC';

	/** the column name for the SUPBUD_SUP2_IC field */
	const SUPBUD_SUP2_IC = 'SITEREPORTS_QFR.SUPBUD_SUP2_IC';

	/** the column name for the SUPBUD_SUP2_SA field */
	const SUPBUD_SUP2_SA = 'SITEREPORTS_QFR.SUPBUD_SUP2_SA';

	/** the column name for the SUPBUD_SUP3_P field */
	const SUPBUD_SUP3_P = 'SITEREPORTS_QFR.SUPBUD_SUP3_P';

	/** the column name for the SUPBUD_SUP3_E field */
	const SUPBUD_SUP3_E = 'SITEREPORTS_QFR.SUPBUD_SUP3_E';

	/** the column name for the SUPBUD_SUP3_PSC field */
	const SUPBUD_SUP3_PSC = 'SITEREPORTS_QFR.SUPBUD_SUP3_PSC';

	/** the column name for the SUPBUD_SUP3_ODC field */
	const SUPBUD_SUP3_ODC = 'SITEREPORTS_QFR.SUPBUD_SUP3_ODC';

	/** the column name for the SUPBUD_SUP3_IC field */
	const SUPBUD_SUP3_IC = 'SITEREPORTS_QFR.SUPBUD_SUP3_IC';

	/** the column name for the SUPBUD_SUP3_SA field */
	const SUPBUD_SUP3_SA = 'SITEREPORTS_QFR.SUPBUD_SUP3_SA';

	/** the column name for the SUPBUD_SUP4_P field */
	const SUPBUD_SUP4_P = 'SITEREPORTS_QFR.SUPBUD_SUP4_P';

	/** the column name for the SUPBUD_SUP4_E field */
	const SUPBUD_SUP4_E = 'SITEREPORTS_QFR.SUPBUD_SUP4_E';

	/** the column name for the SUPBUD_SUP4_PSC field */
	const SUPBUD_SUP4_PSC = 'SITEREPORTS_QFR.SUPBUD_SUP4_PSC';

	/** the column name for the SUPBUD_SUP4_ODC field */
	const SUPBUD_SUP4_ODC = 'SITEREPORTS_QFR.SUPBUD_SUP4_ODC';

	/** the column name for the SUPBUD_SUP4_IC field */
	const SUPBUD_SUP4_IC = 'SITEREPORTS_QFR.SUPBUD_SUP4_IC';

	/** the column name for the SUPBUD_SUP4_SA field */
	const SUPBUD_SUP4_SA = 'SITEREPORTS_QFR.SUPBUD_SUP4_SA';

	/** the column name for the PI_BEG_BAL field */
	const PI_BEG_BAL = 'SITEREPORTS_QFR.PI_BEG_BAL';

	/** the column name for the PI_PIR field */
	const PI_PIR = 'SITEREPORTS_QFR.PI_PIR';

	/** the column name for the PI_PIE field */
	const PI_PIE = 'SITEREPORTS_QFR.PI_PIE';

	/** the column name for the PI_NAR field */
	const PI_NAR = 'SITEREPORTS_QFR.PI_NAR';

	/** the column name for the CREATED_BY field */
	const CREATED_BY = 'SITEREPORTS_QFR.CREATED_BY';

	/** the column name for the CREATED_ON field */
	const CREATED_ON = 'SITEREPORTS_QFR.CREATED_ON';

	/** the column name for the UPDATED_BY field */
	const UPDATED_BY = 'SITEREPORTS_QFR.UPDATED_BY';

	/** the column name for the UPDATED_ON field */
	const UPDATED_ON = 'SITEREPORTS_QFR.UPDATED_ON';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('ID', 'FACILITY_ID', 'YEAR', 'QUARTER', 'PREPARED_BY', 'PREPARERS_TITLE', 'PREPARED_DATE', 'REPORT_PERIOD', 'SUBAWARDED_FUNDED_AMT', 'QFR_SR_P_COST', 'QFR_SR_E_COST', 'QFR_SR_PSC_COST', 'QFR_SR_ODC_COST', 'QFR_SR_IC_COST', 'QFR_NR_P_COST', 'QFR_NR_E_COST', 'QFR_NR_PSC_COST', 'QFR_NR_ODC_COST', 'QFR_NR_IC_COST', 'QFR_ITCA_P_COST', 'QFR_ITCA_E_COST', 'QFR_ITCA_PSC_COST', 'QFR_ITCA_ODC_COST', 'QFR_ITCA_IC_COST', 'QFR_NEOT_P_COST', 'QFR_NEOT_E_COST', 'QFR_NEOT_PSC_COST', 'QFR_NEOT_ODC_COST', 'QFR_NEOT_IC_COST', 'QFR_FEA_P_COST', 'QFR_FEA_E_COST', 'QFR_FEA_PSC_COST', 'QFR_FEA_ODC_COST', 'QFR_FEA_IC_COST', 'QFR_AEM_P_COST', 'QFR_AEM_E_COST', 'QFR_AEM_PSC_COST', 'QFR_AEM_ODC_COST', 'QFR_AEM_IC_COST', 'QFR_NRS_P_COST', 'QFR_NRS_E_COST', 'QFR_NRS_PSC_COST', 'QFR_NRS_ODC_COST', 'QFR_NRS_IC_COST', 'FY_BUDGET_SURS', 'FY_BUDGET_SR', 'FY_BUDGET_NR', 'FY_BUDGET_ITCA', 'FY_BUDGET_FEA', 'FY_BUDGET_NEOT', 'FY_BUDGET_AEM', 'FY_BUDGET_NRS', 'Q1RE_SURS', 'Q1RE_SR', 'Q1RE_NR', 'Q1RE_ITCA', 'Q1RE_FEA', 'Q1RE_NEOT', 'Q1RE_AEM', 'Q1RE_NRS', 'Q2RE_SURS', 'Q2RE_SR', 'Q2RE_NR', 'Q2RE_ITCA', 'Q2RE_FEA', 'Q2RE_NEOT', 'Q2RE_AEM', 'Q2RE_NRS', 'Q3RE_SURS', 'Q3RE_SR', 'Q3RE_NR', 'Q3RE_ITCA', 'Q3RE_FEA', 'Q3RE_NEOT', 'Q3RE_AEM', 'Q3RE_NRS', 'Q4RE_SURS', 'Q4RE_SR', 'Q4RE_NR', 'Q4RE_ITCA', 'Q4RE_FEA', 'Q4RE_NEOT', 'Q4RE_AEM', 'Q4RE_NRS', 'PQA_SURS', 'PQA_SR', 'PQA_NR', 'PQA_ITCA', 'PQA_FEA', 'PQA_NEOT', 'PQA_AEM', 'PQA_NRS', 'CQE_SURS', 'CQE_SR', 'CQE_NR', 'CQE_ITCA', 'CQE_FEA', 'CQE_NEOT', 'CQE_AEM', 'CQE_NRS', 'SUPBUD_SUP1_P', 'SUPBUD_SUP1_E', 'SUPBUD_SUP1_PSC', 'SUPBUD_SUP1_ODC', 'SUPBUD_SUP1_IC', 'SUPBUD_SUP1_SA', 'SUPBUD_SUP2_P', 'SUPBUD_SUP2_E', 'SUPBUD_SUP2_PSC', 'SUPBUD_SUP2_ODC', 'SUPBUD_SUP2_IC', 'SUPBUD_SUP2_SA', 'SUPBUD_SUP3_P', 'SUPBUD_SUP3_E', 'SUPBUD_SUP3_PSC', 'SUPBUD_SUP3_ODC', 'SUPBUD_SUP3_IC', 'SUPBUD_SUP3_SA', 'SUPBUD_SUP4_P', 'SUPBUD_SUP4_E', 'SUPBUD_SUP4_PSC', 'SUPBUD_SUP4_ODC', 'SUPBUD_SUP4_IC', 'SUPBUD_SUP4_SA', 'PI_BEG_BAL', 'PI_PIR', 'PI_PIE', 'PI_NAR', 'CREATED_BY', 'CREATED_ON', 'UPDATED_BY', 'UPDATED_ON', ),
		BasePeer::TYPE_COLNAME => array (SiteReportsQFRPeer::ID, SiteReportsQFRPeer::FACILITY_ID, SiteReportsQFRPeer::YEAR, SiteReportsQFRPeer::QUARTER, SiteReportsQFRPeer::PREPARED_BY, SiteReportsQFRPeer::PREPARERS_TITLE, SiteReportsQFRPeer::PREPARED_DATE, SiteReportsQFRPeer::REPORT_PERIOD, SiteReportsQFRPeer::SUBAWARDED_FUNDED_AMT, SiteReportsQFRPeer::QFR_SR_P_COST, SiteReportsQFRPeer::QFR_SR_E_COST, SiteReportsQFRPeer::QFR_SR_PSC_COST, SiteReportsQFRPeer::QFR_SR_ODC_COST, SiteReportsQFRPeer::QFR_SR_IC_COST, SiteReportsQFRPeer::QFR_NR_P_COST, SiteReportsQFRPeer::QFR_NR_E_COST, SiteReportsQFRPeer::QFR_NR_PSC_COST, SiteReportsQFRPeer::QFR_NR_ODC_COST, SiteReportsQFRPeer::QFR_NR_IC_COST, SiteReportsQFRPeer::QFR_ITCA_P_COST, SiteReportsQFRPeer::QFR_ITCA_E_COST, SiteReportsQFRPeer::QFR_ITCA_PSC_COST, SiteReportsQFRPeer::QFR_ITCA_ODC_COST, SiteReportsQFRPeer::QFR_ITCA_IC_COST, SiteReportsQFRPeer::QFR_NEOT_P_COST, SiteReportsQFRPeer::QFR_NEOT_E_COST, SiteReportsQFRPeer::QFR_NEOT_PSC_COST, SiteReportsQFRPeer::QFR_NEOT_ODC_COST, SiteReportsQFRPeer::QFR_NEOT_IC_COST, SiteReportsQFRPeer::QFR_FEA_P_COST, SiteReportsQFRPeer::QFR_FEA_E_COST, SiteReportsQFRPeer::QFR_FEA_PSC_COST, SiteReportsQFRPeer::QFR_FEA_ODC_COST, SiteReportsQFRPeer::QFR_FEA_IC_COST, SiteReportsQFRPeer::QFR_AEM_P_COST, SiteReportsQFRPeer::QFR_AEM_E_COST, SiteReportsQFRPeer::QFR_AEM_PSC_COST, SiteReportsQFRPeer::QFR_AEM_ODC_COST, SiteReportsQFRPeer::QFR_AEM_IC_COST, SiteReportsQFRPeer::QFR_NRS_P_COST, SiteReportsQFRPeer::QFR_NRS_E_COST, SiteReportsQFRPeer::QFR_NRS_PSC_COST, SiteReportsQFRPeer::QFR_NRS_ODC_COST, SiteReportsQFRPeer::QFR_NRS_IC_COST, SiteReportsQFRPeer::FY_BUDGET_SURS, SiteReportsQFRPeer::FY_BUDGET_SR, SiteReportsQFRPeer::FY_BUDGET_NR, SiteReportsQFRPeer::FY_BUDGET_ITCA, SiteReportsQFRPeer::FY_BUDGET_FEA, SiteReportsQFRPeer::FY_BUDGET_NEOT, SiteReportsQFRPeer::FY_BUDGET_AEM, SiteReportsQFRPeer::FY_BUDGET_NRS, SiteReportsQFRPeer::Q1RE_SURS, SiteReportsQFRPeer::Q1RE_SR, SiteReportsQFRPeer::Q1RE_NR, SiteReportsQFRPeer::Q1RE_ITCA, SiteReportsQFRPeer::Q1RE_FEA, SiteReportsQFRPeer::Q1RE_NEOT, SiteReportsQFRPeer::Q1RE_AEM, SiteReportsQFRPeer::Q1RE_NRS, SiteReportsQFRPeer::Q2RE_SURS, SiteReportsQFRPeer::Q2RE_SR, SiteReportsQFRPeer::Q2RE_NR, SiteReportsQFRPeer::Q2RE_ITCA, SiteReportsQFRPeer::Q2RE_FEA, SiteReportsQFRPeer::Q2RE_NEOT, SiteReportsQFRPeer::Q2RE_AEM, SiteReportsQFRPeer::Q2RE_NRS, SiteReportsQFRPeer::Q3RE_SURS, SiteReportsQFRPeer::Q3RE_SR, SiteReportsQFRPeer::Q3RE_NR, SiteReportsQFRPeer::Q3RE_ITCA, SiteReportsQFRPeer::Q3RE_FEA, SiteReportsQFRPeer::Q3RE_NEOT, SiteReportsQFRPeer::Q3RE_AEM, SiteReportsQFRPeer::Q3RE_NRS, SiteReportsQFRPeer::Q4RE_SURS, SiteReportsQFRPeer::Q4RE_SR, SiteReportsQFRPeer::Q4RE_NR, SiteReportsQFRPeer::Q4RE_ITCA, SiteReportsQFRPeer::Q4RE_FEA, SiteReportsQFRPeer::Q4RE_NEOT, SiteReportsQFRPeer::Q4RE_AEM, SiteReportsQFRPeer::Q4RE_NRS, SiteReportsQFRPeer::PQA_SURS, SiteReportsQFRPeer::PQA_SR, SiteReportsQFRPeer::PQA_NR, SiteReportsQFRPeer::PQA_ITCA, SiteReportsQFRPeer::PQA_FEA, SiteReportsQFRPeer::PQA_NEOT, SiteReportsQFRPeer::PQA_AEM, SiteReportsQFRPeer::PQA_NRS, SiteReportsQFRPeer::CQE_SURS, SiteReportsQFRPeer::CQE_SR, SiteReportsQFRPeer::CQE_NR, SiteReportsQFRPeer::CQE_ITCA, SiteReportsQFRPeer::CQE_FEA, SiteReportsQFRPeer::CQE_NEOT, SiteReportsQFRPeer::CQE_AEM, SiteReportsQFRPeer::CQE_NRS, SiteReportsQFRPeer::SUPBUD_SUP1_P, SiteReportsQFRPeer::SUPBUD_SUP1_E, SiteReportsQFRPeer::SUPBUD_SUP1_PSC, SiteReportsQFRPeer::SUPBUD_SUP1_ODC, SiteReportsQFRPeer::SUPBUD_SUP1_IC, SiteReportsQFRPeer::SUPBUD_SUP1_SA, SiteReportsQFRPeer::SUPBUD_SUP2_P, SiteReportsQFRPeer::SUPBUD_SUP2_E, SiteReportsQFRPeer::SUPBUD_SUP2_PSC, SiteReportsQFRPeer::SUPBUD_SUP2_ODC, SiteReportsQFRPeer::SUPBUD_SUP2_IC, SiteReportsQFRPeer::SUPBUD_SUP2_SA, SiteReportsQFRPeer::SUPBUD_SUP3_P, SiteReportsQFRPeer::SUPBUD_SUP3_E, SiteReportsQFRPeer::SUPBUD_SUP3_PSC, SiteReportsQFRPeer::SUPBUD_SUP3_ODC, SiteReportsQFRPeer::SUPBUD_SUP3_IC, SiteReportsQFRPeer::SUPBUD_SUP3_SA, SiteReportsQFRPeer::SUPBUD_SUP4_P, SiteReportsQFRPeer::SUPBUD_SUP4_E, SiteReportsQFRPeer::SUPBUD_SUP4_PSC, SiteReportsQFRPeer::SUPBUD_SUP4_ODC, SiteReportsQFRPeer::SUPBUD_SUP4_IC, SiteReportsQFRPeer::SUPBUD_SUP4_SA, SiteReportsQFRPeer::PI_BEG_BAL, SiteReportsQFRPeer::PI_PIR, SiteReportsQFRPeer::PI_PIE, SiteReportsQFRPeer::PI_NAR, SiteReportsQFRPeer::CREATED_BY, SiteReportsQFRPeer::CREATED_ON, SiteReportsQFRPeer::UPDATED_BY, SiteReportsQFRPeer::UPDATED_ON, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'FACILITY_ID', 'YEAR', 'QUARTER', 'PREPARED_BY', 'PREPARERS_TITLE', 'PREPARED_DATE', 'REPORT_PERIOD', 'SUBAWARDED_FUNDED_AMT', 'QFR_SR_P_COST', 'QFR_SR_E_COST', 'QFR_SR_PSC_COST', 'QFR_SR_ODC_COST', 'QFR_SR_IC_COST', 'QFR_NR_P_COST', 'QFR_NR_E_COST', 'QFR_NR_PSC_COST', 'QFR_NR_ODC_COST', 'QFR_NR_IC_COST', 'QFR_ITCA_P_COST', 'QFR_ITCA_E_COST', 'QFR_ITCA_PSC_COST', 'QFR_ITCA_ODC_COST', 'QFR_ITCA_IC_COST', 'QFR_NEOT_P_COST', 'QFR_NEOT_E_COST', 'QFR_NEOT_PSC_COST', 'QFR_NEOT_ODC_COST', 'QFR_NEOT_IC_COST', 'QFR_FEA_P_COST', 'QFR_FEA_E_COST', 'QFR_FEA_PSC_COST', 'QFR_FEA_ODC_COST', 'QFR_FEA_IC_COST', 'QFR_AEM_P_COST', 'QFR_AEM_E_COST', 'QFR_AEM_PSC_COST', 'QFR_AEM_ODC_COST', 'QFR_AEM_IC_COST', 'QFR_NRS_P_COST', 'QFR_NRS_E_COST', 'QFR_NRS_PSC_COST', 'QFR_NRS_ODC_COST', 'QFR_NRS_IC_COST', 'FY_BUDGET_SURS', 'FY_BUDGET_SR', 'FY_BUDGET_NR', 'FY_BUDGET_ITCA', 'FY_BUDGET_FEA', 'FY_BUDGET_NEOT', 'FY_BUDGET_AEM', 'FY_BUDGET_NRS', 'Q1RE_SURS', 'Q1RE_SR', 'Q1RE_NR', 'Q1RE_ITCA', 'Q1RE_FEA', 'Q1RE_NEOT', 'Q1RE_AEM', 'Q1RE_NRS', 'Q2RE_SURS', 'Q2RE_SR', 'Q2RE_NR', 'Q2RE_ITCA', 'Q2RE_FEA', 'Q2RE_NEOT', 'Q2RE_AEM', 'Q2RE_NRS', 'Q3RE_SURS', 'Q3RE_SR', 'Q3RE_NR', 'Q3RE_ITCA', 'Q3RE_FEA', 'Q3RE_NEOT', 'Q3RE_AEM', 'Q3RE_NRS', 'Q4RE_SURS', 'Q4RE_SR', 'Q4RE_NR', 'Q4RE_ITCA', 'Q4RE_FEA', 'Q4RE_NEOT', 'Q4RE_AEM', 'Q4RE_NRS', 'PQA_SURS', 'PQA_SR', 'PQA_NR', 'PQA_ITCA', 'PQA_FEA', 'PQA_NEOT', 'PQA_AEM', 'PQA_NRS', 'CQE_SURS', 'CQE_SR', 'CQE_NR', 'CQE_ITCA', 'CQE_FEA', 'CQE_NEOT', 'CQE_AEM', 'CQE_NRS', 'SUPBUD_SUP1_P', 'SUPBUD_SUP1_E', 'SUPBUD_SUP1_PSC', 'SUPBUD_SUP1_ODC', 'SUPBUD_SUP1_IC', 'SUPBUD_SUP1_SA', 'SUPBUD_SUP2_P', 'SUPBUD_SUP2_E', 'SUPBUD_SUP2_PSC', 'SUPBUD_SUP2_ODC', 'SUPBUD_SUP2_IC', 'SUPBUD_SUP2_SA', 'SUPBUD_SUP3_P', 'SUPBUD_SUP3_E', 'SUPBUD_SUP3_PSC', 'SUPBUD_SUP3_ODC', 'SUPBUD_SUP3_IC', 'SUPBUD_SUP3_SA', 'SUPBUD_SUP4_P', 'SUPBUD_SUP4_E', 'SUPBUD_SUP4_PSC', 'SUPBUD_SUP4_ODC', 'SUPBUD_SUP4_IC', 'SUPBUD_SUP4_SA', 'PI_BEG_BAL', 'PI_PIR', 'PI_PIE', 'PI_NAR', 'CREATED_BY', 'CREATED_ON', 'UPDATED_BY', 'UPDATED_ON', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('ID' => 0, 'FACILITY_ID' => 1, 'YEAR' => 2, 'QUARTER' => 3, 'PREPARED_BY' => 4, 'PREPARERS_TITLE' => 5, 'PREPARED_DATE' => 6, 'REPORT_PERIOD' => 7, 'SUBAWARDED_FUNDED_AMT' => 8, 'QFR_SR_P_COST' => 9, 'QFR_SR_E_COST' => 10, 'QFR_SR_PSC_COST' => 11, 'QFR_SR_ODC_COST' => 12, 'QFR_SR_IC_COST' => 13, 'QFR_NR_P_COST' => 14, 'QFR_NR_E_COST' => 15, 'QFR_NR_PSC_COST' => 16, 'QFR_NR_ODC_COST' => 17, 'QFR_NR_IC_COST' => 18, 'QFR_ITCA_P_COST' => 19, 'QFR_ITCA_E_COST' => 20, 'QFR_ITCA_PSC_COST' => 21, 'QFR_ITCA_ODC_COST' => 22, 'QFR_ITCA_IC_COST' => 23, 'QFR_NEOT_P_COST' => 24, 'QFR_NEOT_E_COST' => 25, 'QFR_NEOT_PSC_COST' => 26, 'QFR_NEOT_ODC_COST' => 27, 'QFR_NEOT_IC_COST' => 28, 'QFR_FEA_P_COST' => 29, 'QFR_FEA_E_COST' => 30, 'QFR_FEA_PSC_COST' => 31, 'QFR_FEA_ODC_COST' => 32, 'QFR_FEA_IC_COST' => 33, 'QFR_AEM_P_COST' => 34, 'QFR_AEM_E_COST' => 35, 'QFR_AEM_PSC_COST' => 36, 'QFR_AEM_ODC_COST' => 37, 'QFR_AEM_IC_COST' => 38, 'QFR_NRS_P_COST' => 39, 'QFR_NRS_E_COST' => 40, 'QFR_NRS_PSC_COST' => 41, 'QFR_NRS_ODC_COST' => 42, 'QFR_NRS_IC_COST' => 43, 'FY_BUDGET_SURS' => 44, 'FY_BUDGET_SR' => 45, 'FY_BUDGET_NR' => 46, 'FY_BUDGET_ITCA' => 47, 'FY_BUDGET_FEA' => 48, 'FY_BUDGET_NEOT' => 49, 'FY_BUDGET_AEM' => 50, 'FY_BUDGET_NRS' => 51, 'Q1RE_SURS' => 52, 'Q1RE_SR' => 53, 'Q1RE_NR' => 54, 'Q1RE_ITCA' => 55, 'Q1RE_FEA' => 56, 'Q1RE_NEOT' => 57, 'Q1RE_AEM' => 58, 'Q1RE_NRS' => 59, 'Q2RE_SURS' => 60, 'Q2RE_SR' => 61, 'Q2RE_NR' => 62, 'Q2RE_ITCA' => 63, 'Q2RE_FEA' => 64, 'Q2RE_NEOT' => 65, 'Q2RE_AEM' => 66, 'Q2RE_NRS' => 67, 'Q3RE_SURS' => 68, 'Q3RE_SR' => 69, 'Q3RE_NR' => 70, 'Q3RE_ITCA' => 71, 'Q3RE_FEA' => 72, 'Q3RE_NEOT' => 73, 'Q3RE_AEM' => 74, 'Q3RE_NRS' => 75, 'Q4RE_SURS' => 76, 'Q4RE_SR' => 77, 'Q4RE_NR' => 78, 'Q4RE_ITCA' => 79, 'Q4RE_FEA' => 80, 'Q4RE_NEOT' => 81, 'Q4RE_AEM' => 82, 'Q4RE_NRS' => 83, 'PQA_SURS' => 84, 'PQA_SR' => 85, 'PQA_NR' => 86, 'PQA_ITCA' => 87, 'PQA_FEA' => 88, 'PQA_NEOT' => 89, 'PQA_AEM' => 90, 'PQA_NRS' => 91, 'CQE_SURS' => 92, 'CQE_SR' => 93, 'CQE_NR' => 94, 'CQE_ITCA' => 95, 'CQE_FEA' => 96, 'CQE_NEOT' => 97, 'CQE_AEM' => 98, 'CQE_NRS' => 99, 'SUPBUD_SUP1_P' => 100, 'SUPBUD_SUP1_E' => 101, 'SUPBUD_SUP1_PSC' => 102, 'SUPBUD_SUP1_ODC' => 103, 'SUPBUD_SUP1_IC' => 104, 'SUPBUD_SUP1_SA' => 105, 'SUPBUD_SUP2_P' => 106, 'SUPBUD_SUP2_E' => 107, 'SUPBUD_SUP2_PSC' => 108, 'SUPBUD_SUP2_ODC' => 109, 'SUPBUD_SUP2_IC' => 110, 'SUPBUD_SUP2_SA' => 111, 'SUPBUD_SUP3_P' => 112, 'SUPBUD_SUP3_E' => 113, 'SUPBUD_SUP3_PSC' => 114, 'SUPBUD_SUP3_ODC' => 115, 'SUPBUD_SUP3_IC' => 116, 'SUPBUD_SUP3_SA' => 117, 'SUPBUD_SUP4_P' => 118, 'SUPBUD_SUP4_E' => 119, 'SUPBUD_SUP4_PSC' => 120, 'SUPBUD_SUP4_ODC' => 121, 'SUPBUD_SUP4_IC' => 122, 'SUPBUD_SUP4_SA' => 123, 'PI_BEG_BAL' => 124, 'PI_PIR' => 125, 'PI_PIE' => 126, 'PI_NAR' => 127, 'CREATED_BY' => 128, 'CREATED_ON' => 129, 'UPDATED_BY' => 130, 'UPDATED_ON' => 131, ),
		BasePeer::TYPE_COLNAME => array (SiteReportsQFRPeer::ID => 0, SiteReportsQFRPeer::FACILITY_ID => 1, SiteReportsQFRPeer::YEAR => 2, SiteReportsQFRPeer::QUARTER => 3, SiteReportsQFRPeer::PREPARED_BY => 4, SiteReportsQFRPeer::PREPARERS_TITLE => 5, SiteReportsQFRPeer::PREPARED_DATE => 6, SiteReportsQFRPeer::REPORT_PERIOD => 7, SiteReportsQFRPeer::SUBAWARDED_FUNDED_AMT => 8, SiteReportsQFRPeer::QFR_SR_P_COST => 9, SiteReportsQFRPeer::QFR_SR_E_COST => 10, SiteReportsQFRPeer::QFR_SR_PSC_COST => 11, SiteReportsQFRPeer::QFR_SR_ODC_COST => 12, SiteReportsQFRPeer::QFR_SR_IC_COST => 13, SiteReportsQFRPeer::QFR_NR_P_COST => 14, SiteReportsQFRPeer::QFR_NR_E_COST => 15, SiteReportsQFRPeer::QFR_NR_PSC_COST => 16, SiteReportsQFRPeer::QFR_NR_ODC_COST => 17, SiteReportsQFRPeer::QFR_NR_IC_COST => 18, SiteReportsQFRPeer::QFR_ITCA_P_COST => 19, SiteReportsQFRPeer::QFR_ITCA_E_COST => 20, SiteReportsQFRPeer::QFR_ITCA_PSC_COST => 21, SiteReportsQFRPeer::QFR_ITCA_ODC_COST => 22, SiteReportsQFRPeer::QFR_ITCA_IC_COST => 23, SiteReportsQFRPeer::QFR_NEOT_P_COST => 24, SiteReportsQFRPeer::QFR_NEOT_E_COST => 25, SiteReportsQFRPeer::QFR_NEOT_PSC_COST => 26, SiteReportsQFRPeer::QFR_NEOT_ODC_COST => 27, SiteReportsQFRPeer::QFR_NEOT_IC_COST => 28, SiteReportsQFRPeer::QFR_FEA_P_COST => 29, SiteReportsQFRPeer::QFR_FEA_E_COST => 30, SiteReportsQFRPeer::QFR_FEA_PSC_COST => 31, SiteReportsQFRPeer::QFR_FEA_ODC_COST => 32, SiteReportsQFRPeer::QFR_FEA_IC_COST => 33, SiteReportsQFRPeer::QFR_AEM_P_COST => 34, SiteReportsQFRPeer::QFR_AEM_E_COST => 35, SiteReportsQFRPeer::QFR_AEM_PSC_COST => 36, SiteReportsQFRPeer::QFR_AEM_ODC_COST => 37, SiteReportsQFRPeer::QFR_AEM_IC_COST => 38, SiteReportsQFRPeer::QFR_NRS_P_COST => 39, SiteReportsQFRPeer::QFR_NRS_E_COST => 40, SiteReportsQFRPeer::QFR_NRS_PSC_COST => 41, SiteReportsQFRPeer::QFR_NRS_ODC_COST => 42, SiteReportsQFRPeer::QFR_NRS_IC_COST => 43, SiteReportsQFRPeer::FY_BUDGET_SURS => 44, SiteReportsQFRPeer::FY_BUDGET_SR => 45, SiteReportsQFRPeer::FY_BUDGET_NR => 46, SiteReportsQFRPeer::FY_BUDGET_ITCA => 47, SiteReportsQFRPeer::FY_BUDGET_FEA => 48, SiteReportsQFRPeer::FY_BUDGET_NEOT => 49, SiteReportsQFRPeer::FY_BUDGET_AEM => 50, SiteReportsQFRPeer::FY_BUDGET_NRS => 51, SiteReportsQFRPeer::Q1RE_SURS => 52, SiteReportsQFRPeer::Q1RE_SR => 53, SiteReportsQFRPeer::Q1RE_NR => 54, SiteReportsQFRPeer::Q1RE_ITCA => 55, SiteReportsQFRPeer::Q1RE_FEA => 56, SiteReportsQFRPeer::Q1RE_NEOT => 57, SiteReportsQFRPeer::Q1RE_AEM => 58, SiteReportsQFRPeer::Q1RE_NRS => 59, SiteReportsQFRPeer::Q2RE_SURS => 60, SiteReportsQFRPeer::Q2RE_SR => 61, SiteReportsQFRPeer::Q2RE_NR => 62, SiteReportsQFRPeer::Q2RE_ITCA => 63, SiteReportsQFRPeer::Q2RE_FEA => 64, SiteReportsQFRPeer::Q2RE_NEOT => 65, SiteReportsQFRPeer::Q2RE_AEM => 66, SiteReportsQFRPeer::Q2RE_NRS => 67, SiteReportsQFRPeer::Q3RE_SURS => 68, SiteReportsQFRPeer::Q3RE_SR => 69, SiteReportsQFRPeer::Q3RE_NR => 70, SiteReportsQFRPeer::Q3RE_ITCA => 71, SiteReportsQFRPeer::Q3RE_FEA => 72, SiteReportsQFRPeer::Q3RE_NEOT => 73, SiteReportsQFRPeer::Q3RE_AEM => 74, SiteReportsQFRPeer::Q3RE_NRS => 75, SiteReportsQFRPeer::Q4RE_SURS => 76, SiteReportsQFRPeer::Q4RE_SR => 77, SiteReportsQFRPeer::Q4RE_NR => 78, SiteReportsQFRPeer::Q4RE_ITCA => 79, SiteReportsQFRPeer::Q4RE_FEA => 80, SiteReportsQFRPeer::Q4RE_NEOT => 81, SiteReportsQFRPeer::Q4RE_AEM => 82, SiteReportsQFRPeer::Q4RE_NRS => 83, SiteReportsQFRPeer::PQA_SURS => 84, SiteReportsQFRPeer::PQA_SR => 85, SiteReportsQFRPeer::PQA_NR => 86, SiteReportsQFRPeer::PQA_ITCA => 87, SiteReportsQFRPeer::PQA_FEA => 88, SiteReportsQFRPeer::PQA_NEOT => 89, SiteReportsQFRPeer::PQA_AEM => 90, SiteReportsQFRPeer::PQA_NRS => 91, SiteReportsQFRPeer::CQE_SURS => 92, SiteReportsQFRPeer::CQE_SR => 93, SiteReportsQFRPeer::CQE_NR => 94, SiteReportsQFRPeer::CQE_ITCA => 95, SiteReportsQFRPeer::CQE_FEA => 96, SiteReportsQFRPeer::CQE_NEOT => 97, SiteReportsQFRPeer::CQE_AEM => 98, SiteReportsQFRPeer::CQE_NRS => 99, SiteReportsQFRPeer::SUPBUD_SUP1_P => 100, SiteReportsQFRPeer::SUPBUD_SUP1_E => 101, SiteReportsQFRPeer::SUPBUD_SUP1_PSC => 102, SiteReportsQFRPeer::SUPBUD_SUP1_ODC => 103, SiteReportsQFRPeer::SUPBUD_SUP1_IC => 104, SiteReportsQFRPeer::SUPBUD_SUP1_SA => 105, SiteReportsQFRPeer::SUPBUD_SUP2_P => 106, SiteReportsQFRPeer::SUPBUD_SUP2_E => 107, SiteReportsQFRPeer::SUPBUD_SUP2_PSC => 108, SiteReportsQFRPeer::SUPBUD_SUP2_ODC => 109, SiteReportsQFRPeer::SUPBUD_SUP2_IC => 110, SiteReportsQFRPeer::SUPBUD_SUP2_SA => 111, SiteReportsQFRPeer::SUPBUD_SUP3_P => 112, SiteReportsQFRPeer::SUPBUD_SUP3_E => 113, SiteReportsQFRPeer::SUPBUD_SUP3_PSC => 114, SiteReportsQFRPeer::SUPBUD_SUP3_ODC => 115, SiteReportsQFRPeer::SUPBUD_SUP3_IC => 116, SiteReportsQFRPeer::SUPBUD_SUP3_SA => 117, SiteReportsQFRPeer::SUPBUD_SUP4_P => 118, SiteReportsQFRPeer::SUPBUD_SUP4_E => 119, SiteReportsQFRPeer::SUPBUD_SUP4_PSC => 120, SiteReportsQFRPeer::SUPBUD_SUP4_ODC => 121, SiteReportsQFRPeer::SUPBUD_SUP4_IC => 122, SiteReportsQFRPeer::SUPBUD_SUP4_SA => 123, SiteReportsQFRPeer::PI_BEG_BAL => 124, SiteReportsQFRPeer::PI_PIR => 125, SiteReportsQFRPeer::PI_PIE => 126, SiteReportsQFRPeer::PI_NAR => 127, SiteReportsQFRPeer::CREATED_BY => 128, SiteReportsQFRPeer::CREATED_ON => 129, SiteReportsQFRPeer::UPDATED_BY => 130, SiteReportsQFRPeer::UPDATED_ON => 131, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'FACILITY_ID' => 1, 'YEAR' => 2, 'QUARTER' => 3, 'PREPARED_BY' => 4, 'PREPARERS_TITLE' => 5, 'PREPARED_DATE' => 6, 'REPORT_PERIOD' => 7, 'SUBAWARDED_FUNDED_AMT' => 8, 'QFR_SR_P_COST' => 9, 'QFR_SR_E_COST' => 10, 'QFR_SR_PSC_COST' => 11, 'QFR_SR_ODC_COST' => 12, 'QFR_SR_IC_COST' => 13, 'QFR_NR_P_COST' => 14, 'QFR_NR_E_COST' => 15, 'QFR_NR_PSC_COST' => 16, 'QFR_NR_ODC_COST' => 17, 'QFR_NR_IC_COST' => 18, 'QFR_ITCA_P_COST' => 19, 'QFR_ITCA_E_COST' => 20, 'QFR_ITCA_PSC_COST' => 21, 'QFR_ITCA_ODC_COST' => 22, 'QFR_ITCA_IC_COST' => 23, 'QFR_NEOT_P_COST' => 24, 'QFR_NEOT_E_COST' => 25, 'QFR_NEOT_PSC_COST' => 26, 'QFR_NEOT_ODC_COST' => 27, 'QFR_NEOT_IC_COST' => 28, 'QFR_FEA_P_COST' => 29, 'QFR_FEA_E_COST' => 30, 'QFR_FEA_PSC_COST' => 31, 'QFR_FEA_ODC_COST' => 32, 'QFR_FEA_IC_COST' => 33, 'QFR_AEM_P_COST' => 34, 'QFR_AEM_E_COST' => 35, 'QFR_AEM_PSC_COST' => 36, 'QFR_AEM_ODC_COST' => 37, 'QFR_AEM_IC_COST' => 38, 'QFR_NRS_P_COST' => 39, 'QFR_NRS_E_COST' => 40, 'QFR_NRS_PSC_COST' => 41, 'QFR_NRS_ODC_COST' => 42, 'QFR_NRS_IC_COST' => 43, 'FY_BUDGET_SURS' => 44, 'FY_BUDGET_SR' => 45, 'FY_BUDGET_NR' => 46, 'FY_BUDGET_ITCA' => 47, 'FY_BUDGET_FEA' => 48, 'FY_BUDGET_NEOT' => 49, 'FY_BUDGET_AEM' => 50, 'FY_BUDGET_NRS' => 51, 'Q1RE_SURS' => 52, 'Q1RE_SR' => 53, 'Q1RE_NR' => 54, 'Q1RE_ITCA' => 55, 'Q1RE_FEA' => 56, 'Q1RE_NEOT' => 57, 'Q1RE_AEM' => 58, 'Q1RE_NRS' => 59, 'Q2RE_SURS' => 60, 'Q2RE_SR' => 61, 'Q2RE_NR' => 62, 'Q2RE_ITCA' => 63, 'Q2RE_FEA' => 64, 'Q2RE_NEOT' => 65, 'Q2RE_AEM' => 66, 'Q2RE_NRS' => 67, 'Q3RE_SURS' => 68, 'Q3RE_SR' => 69, 'Q3RE_NR' => 70, 'Q3RE_ITCA' => 71, 'Q3RE_FEA' => 72, 'Q3RE_NEOT' => 73, 'Q3RE_AEM' => 74, 'Q3RE_NRS' => 75, 'Q4RE_SURS' => 76, 'Q4RE_SR' => 77, 'Q4RE_NR' => 78, 'Q4RE_ITCA' => 79, 'Q4RE_FEA' => 80, 'Q4RE_NEOT' => 81, 'Q4RE_AEM' => 82, 'Q4RE_NRS' => 83, 'PQA_SURS' => 84, 'PQA_SR' => 85, 'PQA_NR' => 86, 'PQA_ITCA' => 87, 'PQA_FEA' => 88, 'PQA_NEOT' => 89, 'PQA_AEM' => 90, 'PQA_NRS' => 91, 'CQE_SURS' => 92, 'CQE_SR' => 93, 'CQE_NR' => 94, 'CQE_ITCA' => 95, 'CQE_FEA' => 96, 'CQE_NEOT' => 97, 'CQE_AEM' => 98, 'CQE_NRS' => 99, 'SUPBUD_SUP1_P' => 100, 'SUPBUD_SUP1_E' => 101, 'SUPBUD_SUP1_PSC' => 102, 'SUPBUD_SUP1_ODC' => 103, 'SUPBUD_SUP1_IC' => 104, 'SUPBUD_SUP1_SA' => 105, 'SUPBUD_SUP2_P' => 106, 'SUPBUD_SUP2_E' => 107, 'SUPBUD_SUP2_PSC' => 108, 'SUPBUD_SUP2_ODC' => 109, 'SUPBUD_SUP2_IC' => 110, 'SUPBUD_SUP2_SA' => 111, 'SUPBUD_SUP3_P' => 112, 'SUPBUD_SUP3_E' => 113, 'SUPBUD_SUP3_PSC' => 114, 'SUPBUD_SUP3_ODC' => 115, 'SUPBUD_SUP3_IC' => 116, 'SUPBUD_SUP3_SA' => 117, 'SUPBUD_SUP4_P' => 118, 'SUPBUD_SUP4_E' => 119, 'SUPBUD_SUP4_PSC' => 120, 'SUPBUD_SUP4_ODC' => 121, 'SUPBUD_SUP4_IC' => 122, 'SUPBUD_SUP4_SA' => 123, 'PI_BEG_BAL' => 124, 'PI_PIR' => 125, 'PI_PIE' => 126, 'PI_NAR' => 127, 'CREATED_BY' => 128, 'CREATED_ON' => 129, 'UPDATED_BY' => 130, 'UPDATED_ON' => 131, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/SiteReportsQFRMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.SiteReportsQFRMapBuilder');
	}
	/**
	 * Gets a map (hash) of PHP names to DB column names.
	 *
	 * @return     array The PHP to DB name map for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @deprecated Use the getFieldNames() and translateFieldName() methods instead of this.
	 */
	public static function getPhpNameMap()
	{
		if (self::$phpNameMap === null) {
			$map = SiteReportsQFRPeer::getTableMap();
			$columns = $map->getColumns();
			$nameMap = array();
			foreach ($columns as $column) {
				$nameMap[$column->getPhpName()] = $column->getColumnName();
			}
			self::$phpNameMap = $nameMap;
		}
		return self::$phpNameMap;
	}
	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants TYPE_PHPNAME,
	 *                         TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants TYPE_PHPNAME,
	 *                      TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	/**
	 * Convenience method which changes table.column to alias.column.
	 *
	 * Using this method you can maintain SQL abstraction while using column aliases.
	 * <code>
	 *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
	 *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
	 * </code>
	 * @param      string $alias The alias for the current table.
	 * @param      string $column The column name for current table. (i.e. SiteReportsQFRPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(SiteReportsQFRPeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      criteria object containing the columns to add.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria)
	{

		$criteria->addSelectColumn(SiteReportsQFRPeer::ID);

		$criteria->addSelectColumn(SiteReportsQFRPeer::FACILITY_ID);

		$criteria->addSelectColumn(SiteReportsQFRPeer::YEAR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QUARTER);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PREPARED_BY);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PREPARERS_TITLE);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PREPARED_DATE);

		$criteria->addSelectColumn(SiteReportsQFRPeer::REPORT_PERIOD);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUBAWARDED_FUNDED_AMT);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_SR_P_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_SR_E_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_SR_PSC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_SR_ODC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_SR_IC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NR_P_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NR_E_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NR_PSC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NR_ODC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NR_IC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_ITCA_P_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_ITCA_E_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_ITCA_PSC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_ITCA_ODC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_ITCA_IC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NEOT_P_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NEOT_E_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NEOT_PSC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NEOT_ODC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NEOT_IC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_FEA_P_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_FEA_E_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_FEA_PSC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_FEA_ODC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_FEA_IC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_AEM_P_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_AEM_E_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_AEM_PSC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_AEM_ODC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_AEM_IC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NRS_P_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NRS_E_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NRS_PSC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NRS_ODC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::QFR_NRS_IC_COST);

		$criteria->addSelectColumn(SiteReportsQFRPeer::FY_BUDGET_SURS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::FY_BUDGET_SR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::FY_BUDGET_NR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::FY_BUDGET_ITCA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::FY_BUDGET_FEA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::FY_BUDGET_NEOT);

		$criteria->addSelectColumn(SiteReportsQFRPeer::FY_BUDGET_AEM);

		$criteria->addSelectColumn(SiteReportsQFRPeer::FY_BUDGET_NRS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q1RE_SURS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q1RE_SR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q1RE_NR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q1RE_ITCA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q1RE_FEA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q1RE_NEOT);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q1RE_AEM);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q1RE_NRS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q2RE_SURS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q2RE_SR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q2RE_NR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q2RE_ITCA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q2RE_FEA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q2RE_NEOT);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q2RE_AEM);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q2RE_NRS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q3RE_SURS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q3RE_SR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q3RE_NR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q3RE_ITCA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q3RE_FEA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q3RE_NEOT);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q3RE_AEM);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q3RE_NRS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q4RE_SURS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q4RE_SR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q4RE_NR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q4RE_ITCA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q4RE_FEA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q4RE_NEOT);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q4RE_AEM);

		$criteria->addSelectColumn(SiteReportsQFRPeer::Q4RE_NRS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PQA_SURS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PQA_SR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PQA_NR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PQA_ITCA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PQA_FEA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PQA_NEOT);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PQA_AEM);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PQA_NRS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::CQE_SURS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::CQE_SR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::CQE_NR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::CQE_ITCA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::CQE_FEA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::CQE_NEOT);

		$criteria->addSelectColumn(SiteReportsQFRPeer::CQE_AEM);

		$criteria->addSelectColumn(SiteReportsQFRPeer::CQE_NRS);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP1_P);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP1_E);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP1_PSC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP1_ODC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP1_IC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP1_SA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP2_P);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP2_E);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP2_PSC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP2_ODC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP2_IC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP2_SA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP3_P);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP3_E);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP3_PSC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP3_ODC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP3_IC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP3_SA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP4_P);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP4_E);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP4_PSC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP4_ODC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP4_IC);

		$criteria->addSelectColumn(SiteReportsQFRPeer::SUPBUD_SUP4_SA);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PI_BEG_BAL);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PI_PIR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PI_PIE);

		$criteria->addSelectColumn(SiteReportsQFRPeer::PI_NAR);

		$criteria->addSelectColumn(SiteReportsQFRPeer::CREATED_BY);

		$criteria->addSelectColumn(SiteReportsQFRPeer::CREATED_ON);

		$criteria->addSelectColumn(SiteReportsQFRPeer::UPDATED_BY);

		$criteria->addSelectColumn(SiteReportsQFRPeer::UPDATED_ON);

	}

	const COUNT = 'COUNT(SITEREPORTS_QFR.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT SITEREPORTS_QFR.ID)';

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SiteReportsQFRPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SiteReportsQFRPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = SiteReportsQFRPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      Connection $con
	 * @return     SiteReportsQFR
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = SiteReportsQFRPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Method to do selects.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      Connection $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, $con = null)
	{
		return SiteReportsQFRPeer::populateObjects(SiteReportsQFRPeer::doSelectRS($criteria, $con));
	}
	/**
	 * Prepares the Criteria object and uses the parent doSelect()
	 * method to get a ResultSet.
	 *
	 * Use this method directly if you want to just get the resultset
	 * (instead of an array of objects).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      Connection $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     ResultSet The resultset object with numerically-indexed fields.
	 * @see        BasePeer::doSelect()
	 */
	public static function doSelectRS(Criteria $criteria, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		if (!$criteria->getSelectColumns()) {
			$criteria = clone $criteria;
			SiteReportsQFRPeer::addSelectColumns($criteria);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// BasePeer returns a Creole ResultSet, set to return
		// rows indexed numerically.
		return BasePeer::doSelect($criteria, $con);
	}
	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(ResultSet $rs)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = SiteReportsQFRPeer::getOMClass();
		$cls = Propel::import($cls);
		// populate the object(s)
		while($rs->next()) {
		
			$obj = new $cls();
			$obj->hydrate($rs);
			$results[] = $obj;
			
		}
		return $results;
	}
	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * This uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass()
	{
		return SiteReportsQFRPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a SiteReportsQFR or Criteria object.
	 *
	 * @param      mixed $values Criteria or SiteReportsQFR object containing data that is used to create the INSERT statement.
	 * @param      Connection $con the connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from SiteReportsQFR object
		}

		$criteria->remove(SiteReportsQFRPeer::ID); // remove pkey col since this table uses auto-increment


		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->begin();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollback();
			throw $e;
		}

		return $pk;
	}

	/**
	 * Method perform an UPDATE on the database, given a SiteReportsQFR or Criteria object.
	 *
	 * @param      mixed $values Criteria or SiteReportsQFR object containing data that is used to create the UPDATE statement.
	 * @param      Connection $con The connection to use (specify Connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(SiteReportsQFRPeer::ID);
			$selectCriteria->add(SiteReportsQFRPeer::ID, $criteria->remove(SiteReportsQFRPeer::ID), $comparison);

		} else { // $values is SiteReportsQFR object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the SITEREPORTS_QFR table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->begin();
			$affectedRows += BasePeer::doDeleteAll(SiteReportsQFRPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a SiteReportsQFR or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or SiteReportsQFR object or primary key or array of primary keys
	 *              which is used to create the DELETE statement
	 * @param      Connection $con the connection to use
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	 public static function doDelete($values, $con = null)
	 {
		if ($con === null) {
			$con = Propel::getConnection(SiteReportsQFRPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof SiteReportsQFR) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(SiteReportsQFRPeer::ID, (array) $values, Criteria::IN);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->begin();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given SiteReportsQFR object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      SiteReportsQFR $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(SiteReportsQFR $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(SiteReportsQFRPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(SiteReportsQFRPeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
					$get = 'get' . $tableMap->getColumn($colName)->getPhpName();
					$columns[$colName] = $obj->$get();
				}
			}
		} else {

		}

		return BasePeer::doValidate(SiteReportsQFRPeer::DATABASE_NAME, SiteReportsQFRPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     SiteReportsQFR
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(SiteReportsQFRPeer::DATABASE_NAME);

		$criteria->add(SiteReportsQFRPeer::ID, $pk);


		$v = SiteReportsQFRPeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}

	/**
	 * Retrieve multiple objects by pkey.
	 *
	 * @param      array $pks List of primary keys
	 * @param      Connection $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKs($pks, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria();
			$criteria->add(SiteReportsQFRPeer::ID, $pks, Criteria::IN);
			$objs = SiteReportsQFRPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseSiteReportsQFRPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseSiteReportsQFRPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/SiteReportsQFRMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.SiteReportsQFRMapBuilder');
}
