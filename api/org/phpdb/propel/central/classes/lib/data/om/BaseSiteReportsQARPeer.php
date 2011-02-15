<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by SiteReportsQARPeer::getOMClass()
include_once 'lib/data/SiteReportsQAR.php';

/**
 * Base static class for performing query and update operations on the 'SITEREPORTS_QAR' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSiteReportsQARPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'SITEREPORTS_QAR';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.SiteReportsQAR';

	/** The total number of columns. */
	const NUM_COLUMNS = 70;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'SITEREPORTS_QAR.ID';

	/** the column name for the FACILITY_ID field */
	const FACILITY_ID = 'SITEREPORTS_QAR.FACILITY_ID';

	/** the column name for the YEAR field */
	const YEAR = 'SITEREPORTS_QAR.YEAR';

	/** the column name for the QUARTER field */
	const QUARTER = 'SITEREPORTS_QAR.QUARTER';

	/** the column name for the RBS_SS_LAST_REV_DATE_Q1 field */
	const RBS_SS_LAST_REV_DATE_Q1 = 'SITEREPORTS_QAR.RBS_SS_LAST_REV_DATE_Q1';

	/** the column name for the RBS_SS_LAST_REV_DATE_Q2 field */
	const RBS_SS_LAST_REV_DATE_Q2 = 'SITEREPORTS_QAR.RBS_SS_LAST_REV_DATE_Q2';

	/** the column name for the RBS_SS_LAST_REV_DATE_Q3 field */
	const RBS_SS_LAST_REV_DATE_Q3 = 'SITEREPORTS_QAR.RBS_SS_LAST_REV_DATE_Q3';

	/** the column name for the RBS_SS_LAST_REV_DATE_Q4 field */
	const RBS_SS_LAST_REV_DATE_Q4 = 'SITEREPORTS_QAR.RBS_SS_LAST_REV_DATE_Q4';

	/** the column name for the RBS_SS_OSHA_RI_Q1 field */
	const RBS_SS_OSHA_RI_Q1 = 'SITEREPORTS_QAR.RBS_SS_OSHA_RI_Q1';

	/** the column name for the RBS_SS_OSHA_RI_Q2 field */
	const RBS_SS_OSHA_RI_Q2 = 'SITEREPORTS_QAR.RBS_SS_OSHA_RI_Q2';

	/** the column name for the RBS_SS_OSHA_RI_Q3 field */
	const RBS_SS_OSHA_RI_Q3 = 'SITEREPORTS_QAR.RBS_SS_OSHA_RI_Q3';

	/** the column name for the RBS_SS_OSHA_RI_Q4 field */
	const RBS_SS_OSHA_RI_Q4 = 'SITEREPORTS_QAR.RBS_SS_OSHA_RI_Q4';

	/** the column name for the RBS_SS_INJURY_NAR field */
	const RBS_SS_INJURY_NAR = 'SITEREPORTS_QAR.RBS_SS_INJURY_NAR';

	/** the column name for the RBS_SS_PSA_NAR field */
	const RBS_SS_PSA_NAR = 'SITEREPORTS_QAR.RBS_SS_PSA_NAR';

	/** the column name for the RBS_PMCR_PPM_PRG_Q1 field */
	const RBS_PMCR_PPM_PRG_Q1 = 'SITEREPORTS_QAR.RBS_PMCR_PPM_PRG_Q1';

	/** the column name for the RBS_PMCR_PPM_PRG_Q2 field */
	const RBS_PMCR_PPM_PRG_Q2 = 'SITEREPORTS_QAR.RBS_PMCR_PPM_PRG_Q2';

	/** the column name for the RBS_PMCR_PPM_PRG_Q3 field */
	const RBS_PMCR_PPM_PRG_Q3 = 'SITEREPORTS_QAR.RBS_PMCR_PPM_PRG_Q3';

	/** the column name for the RBS_PMCR_PPM_PRG_Q4 field */
	const RBS_PMCR_PPM_PRG_Q4 = 'SITEREPORTS_QAR.RBS_PMCR_PPM_PRG_Q4';

	/** the column name for the RBS_PMCR_PPM_NAR field */
	const RBS_PMCR_PPM_NAR = 'SITEREPORTS_QAR.RBS_PMCR_PPM_NAR';

	/** the column name for the RBS_PMCR_PC_PRG_Q1 field */
	const RBS_PMCR_PC_PRG_Q1 = 'SITEREPORTS_QAR.RBS_PMCR_PC_PRG_Q1';

	/** the column name for the RBS_PMCR_PC_PRG_Q2 field */
	const RBS_PMCR_PC_PRG_Q2 = 'SITEREPORTS_QAR.RBS_PMCR_PC_PRG_Q2';

	/** the column name for the RBS_PMCR_PC_PRG_Q3 field */
	const RBS_PMCR_PC_PRG_Q3 = 'SITEREPORTS_QAR.RBS_PMCR_PC_PRG_Q3';

	/** the column name for the RBS_PMCR_PC_PRG_Q4 field */
	const RBS_PMCR_PC_PRG_Q4 = 'SITEREPORTS_QAR.RBS_PMCR_PC_PRG_Q4';

	/** the column name for the RBS_PMCR_PC_NAR field */
	const RBS_PMCR_PC_NAR = 'SITEREPORTS_QAR.RBS_PMCR_PC_NAR';

	/** the column name for the RBS_PMCR_PR_PRG_Q1 field */
	const RBS_PMCR_PR_PRG_Q1 = 'SITEREPORTS_QAR.RBS_PMCR_PR_PRG_Q1';

	/** the column name for the RBS_PMCR_PR_PRG_Q2 field */
	const RBS_PMCR_PR_PRG_Q2 = 'SITEREPORTS_QAR.RBS_PMCR_PR_PRG_Q2';

	/** the column name for the RBS_PMCR_PR_PRG_Q3 field */
	const RBS_PMCR_PR_PRG_Q3 = 'SITEREPORTS_QAR.RBS_PMCR_PR_PRG_Q3';

	/** the column name for the RBS_PMCR_PR_PRG_Q4 field */
	const RBS_PMCR_PR_PRG_Q4 = 'SITEREPORTS_QAR.RBS_PMCR_PR_PRG_Q4';

	/** the column name for the RBS_PMCR_PR_NAR field */
	const RBS_PMCR_PR_NAR = 'SITEREPORTS_QAR.RBS_PMCR_PR_NAR';

	/** the column name for the CB_FE_PRG_Q1 field */
	const CB_FE_PRG_Q1 = 'SITEREPORTS_QAR.CB_FE_PRG_Q1';

	/** the column name for the CB_FE_PRG_Q2 field */
	const CB_FE_PRG_Q2 = 'SITEREPORTS_QAR.CB_FE_PRG_Q2';

	/** the column name for the CB_FE_PRG_Q3 field */
	const CB_FE_PRG_Q3 = 'SITEREPORTS_QAR.CB_FE_PRG_Q3';

	/** the column name for the CB_FE_PRG_Q4 field */
	const CB_FE_PRG_Q4 = 'SITEREPORTS_QAR.CB_FE_PRG_Q4';

	/** the column name for the CB_FE_NAR field */
	const CB_FE_NAR = 'SITEREPORTS_QAR.CB_FE_NAR';

	/** the column name for the NI_ITCA_PRG_Q1 field */
	const NI_ITCA_PRG_Q1 = 'SITEREPORTS_QAR.NI_ITCA_PRG_Q1';

	/** the column name for the NI_ITCA_PRG_Q2 field */
	const NI_ITCA_PRG_Q2 = 'SITEREPORTS_QAR.NI_ITCA_PRG_Q2';

	/** the column name for the NI_ITCA_PRG_Q3 field */
	const NI_ITCA_PRG_Q3 = 'SITEREPORTS_QAR.NI_ITCA_PRG_Q3';

	/** the column name for the NI_ITCA_PRG_Q4 field */
	const NI_ITCA_PRG_Q4 = 'SITEREPORTS_QAR.NI_ITCA_PRG_Q4';

	/** the column name for the NI_ITCA_NAR field */
	const NI_ITCA_NAR = 'SITEREPORTS_QAR.NI_ITCA_NAR';

	/** the column name for the NI_NEOT_PRG_Q1 field */
	const NI_NEOT_PRG_Q1 = 'SITEREPORTS_QAR.NI_NEOT_PRG_Q1';

	/** the column name for the NI_NEOT_PRG_Q2 field */
	const NI_NEOT_PRG_Q2 = 'SITEREPORTS_QAR.NI_NEOT_PRG_Q2';

	/** the column name for the NI_NEOT_PRG_Q3 field */
	const NI_NEOT_PRG_Q3 = 'SITEREPORTS_QAR.NI_NEOT_PRG_Q3';

	/** the column name for the NI_NEOT_PRG_Q4 field */
	const NI_NEOT_PRG_Q4 = 'SITEREPORTS_QAR.NI_NEOT_PRG_Q4';

	/** the column name for the NI_NEOT_NAR field */
	const NI_NEOT_NAR = 'SITEREPORTS_QAR.NI_NEOT_NAR';

	/** the column name for the NI_NRS_PRG_Q1 field */
	const NI_NRS_PRG_Q1 = 'SITEREPORTS_QAR.NI_NRS_PRG_Q1';

	/** the column name for the NI_NRS_PRG_Q2 field */
	const NI_NRS_PRG_Q2 = 'SITEREPORTS_QAR.NI_NRS_PRG_Q2';

	/** the column name for the NI_NRS_PRG_Q3 field */
	const NI_NRS_PRG_Q3 = 'SITEREPORTS_QAR.NI_NRS_PRG_Q3';

	/** the column name for the NI_NRS_PRG_Q4 field */
	const NI_NRS_PRG_Q4 = 'SITEREPORTS_QAR.NI_NRS_PRG_Q4';

	/** the column name for the NI_NRS_NAR field */
	const NI_NRS_NAR = 'SITEREPORTS_QAR.NI_NRS_NAR';

	/** the column name for the FH_NAR field */
	const FH_NAR = 'SITEREPORTS_QAR.FH_NAR';

	/** the column name for the AEM_NAR field */
	const AEM_NAR = 'SITEREPORTS_QAR.AEM_NAR';

	/** the column name for the SA1_PRG_Q1 field */
	const SA1_PRG_Q1 = 'SITEREPORTS_QAR.SA1_PRG_Q1';

	/** the column name for the SA1_PRG_Q2 field */
	const SA1_PRG_Q2 = 'SITEREPORTS_QAR.SA1_PRG_Q2';

	/** the column name for the SA1_PRG_Q3 field */
	const SA1_PRG_Q3 = 'SITEREPORTS_QAR.SA1_PRG_Q3';

	/** the column name for the SA1_PRG_Q4 field */
	const SA1_PRG_Q4 = 'SITEREPORTS_QAR.SA1_PRG_Q4';

	/** the column name for the SA1_PRG_NAR field */
	const SA1_PRG_NAR = 'SITEREPORTS_QAR.SA1_PRG_NAR';

	/** the column name for the SA2_PRG_Q1 field */
	const SA2_PRG_Q1 = 'SITEREPORTS_QAR.SA2_PRG_Q1';

	/** the column name for the SA2_PRG_Q2 field */
	const SA2_PRG_Q2 = 'SITEREPORTS_QAR.SA2_PRG_Q2';

	/** the column name for the SA2_PRG_Q3 field */
	const SA2_PRG_Q3 = 'SITEREPORTS_QAR.SA2_PRG_Q3';

	/** the column name for the SA2_PRG_Q4 field */
	const SA2_PRG_Q4 = 'SITEREPORTS_QAR.SA2_PRG_Q4';

	/** the column name for the SA2_PRG_NAR field */
	const SA2_PRG_NAR = 'SITEREPORTS_QAR.SA2_PRG_NAR';

	/** the column name for the SA3_PRG_Q1 field */
	const SA3_PRG_Q1 = 'SITEREPORTS_QAR.SA3_PRG_Q1';

	/** the column name for the SA3_PRG_Q2 field */
	const SA3_PRG_Q2 = 'SITEREPORTS_QAR.SA3_PRG_Q2';

	/** the column name for the SA3_PRG_Q3 field */
	const SA3_PRG_Q3 = 'SITEREPORTS_QAR.SA3_PRG_Q3';

	/** the column name for the SA3_PRG_Q4 field */
	const SA3_PRG_Q4 = 'SITEREPORTS_QAR.SA3_PRG_Q4';

	/** the column name for the SA3_PRG_NAR field */
	const SA3_PRG_NAR = 'SITEREPORTS_QAR.SA3_PRG_NAR';

	/** the column name for the CREATED_BY field */
	const CREATED_BY = 'SITEREPORTS_QAR.CREATED_BY';

	/** the column name for the CREATED_ON field */
	const CREATED_ON = 'SITEREPORTS_QAR.CREATED_ON';

	/** the column name for the UPDATED_BY field */
	const UPDATED_BY = 'SITEREPORTS_QAR.UPDATED_BY';

	/** the column name for the UPDATED_ON field */
	const UPDATED_ON = 'SITEREPORTS_QAR.UPDATED_ON';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('ID', 'FACILITY_ID', 'YEAR', 'QUARTER', 'RBS_SS_LAST_REV_DATE_Q1', 'RBS_SS_LAST_REV_DATE_Q2', 'RBS_SS_LAST_REV_DATE_Q3', 'RBS_SS_LAST_REV_DATE_Q4', 'RBS_SS_RI_Q1', 'RBS_SS_RI_Q2', 'RBS_SS_RI_Q3', 'RBS_SS_RI_Q4', 'RBS_SS_INJURY_NAR', 'RBS_SS_PSA_NAR', 'RBS_PMCR_PPM_PRG_Q1', 'RBS_PMCR_PPM_PRG_Q2', 'RBS_PMCR_PPM_PRG_Q3', 'RBS_PMCR_PPM_PRG_Q4', 'RBS_PMCR_PPM_NAR', 'RBS_PMCR_PC_PRG_Q1', 'RBS_PMCR_PC_PRG_Q2', 'RBS_PMCR_PC_PRG_Q3', 'RBS_PMCR_PC_PRG_Q4', 'RBS_PMCR_PC_NAR', 'RBS_PMCR_PR_PRG_Q1', 'RBS_PMCR_PR_PRG_Q2', 'RBS_PMCR_PR_PRG_Q3', 'RBS_PMCR_PR_PRG_Q4', 'RBS_PMCR_PR_NAR', 'CB_FE_PRG_Q1', 'CB_FE_PRG_Q2', 'CB_FE_PRG_Q3', 'CB_FE_PRG_Q4', 'CB_FE_NAR', 'NI_ITCA_PRG_Q1', 'NI_ITCA_PRG_Q2', 'NI_ITCA_PRG_Q3', 'NI_ITCA_PRG_Q4', 'NI_ITCA_NAR', 'NI_NEOT_PRG_Q1', 'NI_NEOT_PRG_Q2', 'NI_NEOT_PRG_Q3', 'NI_NEOT_PRG_Q4', 'NI_NEOT_NAR', 'NI_NRS_PRG_Q1', 'NI_NRS_PRG_Q2', 'NI_NRS_PRG_Q3', 'NI_NRS_PRG_Q4', 'NI_NRS_NAR', 'FH_NAR', 'AEM_NAR', 'SA1_PRG_Q1', 'SA1_PRG_Q2', 'SA1_PRG_Q3', 'SA1_PRG_Q4', 'SA1_PRG_NAR', 'SA2_PRG_Q1', 'SA2_PRG_Q2', 'SA2_PRG_Q3', 'SA2_PRG_Q4', 'SA2_PRG_NAR', 'SA3_PRG_Q1', 'SA3_PRG_Q2', 'SA3_PRG_Q3', 'SA3_PRG_Q4', 'SA3_PRG_NAR', 'CREATED_BY', 'CREATED_ON', 'UPDATED_BY', 'UPDATED_ON', ),
		BasePeer::TYPE_COLNAME => array (SiteReportsQARPeer::ID, SiteReportsQARPeer::FACILITY_ID, SiteReportsQARPeer::YEAR, SiteReportsQARPeer::QUARTER, SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q1, SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q2, SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q3, SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q4, SiteReportsQARPeer::RBS_SS_OSHA_RI_Q1, SiteReportsQARPeer::RBS_SS_OSHA_RI_Q2, SiteReportsQARPeer::RBS_SS_OSHA_RI_Q3, SiteReportsQARPeer::RBS_SS_OSHA_RI_Q4, SiteReportsQARPeer::RBS_SS_INJURY_NAR, SiteReportsQARPeer::RBS_SS_PSA_NAR, SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q1, SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q2, SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q3, SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q4, SiteReportsQARPeer::RBS_PMCR_PPM_NAR, SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q1, SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q2, SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q3, SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q4, SiteReportsQARPeer::RBS_PMCR_PC_NAR, SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q1, SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q2, SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q3, SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q4, SiteReportsQARPeer::RBS_PMCR_PR_NAR, SiteReportsQARPeer::CB_FE_PRG_Q1, SiteReportsQARPeer::CB_FE_PRG_Q2, SiteReportsQARPeer::CB_FE_PRG_Q3, SiteReportsQARPeer::CB_FE_PRG_Q4, SiteReportsQARPeer::CB_FE_NAR, SiteReportsQARPeer::NI_ITCA_PRG_Q1, SiteReportsQARPeer::NI_ITCA_PRG_Q2, SiteReportsQARPeer::NI_ITCA_PRG_Q3, SiteReportsQARPeer::NI_ITCA_PRG_Q4, SiteReportsQARPeer::NI_ITCA_NAR, SiteReportsQARPeer::NI_NEOT_PRG_Q1, SiteReportsQARPeer::NI_NEOT_PRG_Q2, SiteReportsQARPeer::NI_NEOT_PRG_Q3, SiteReportsQARPeer::NI_NEOT_PRG_Q4, SiteReportsQARPeer::NI_NEOT_NAR, SiteReportsQARPeer::NI_NRS_PRG_Q1, SiteReportsQARPeer::NI_NRS_PRG_Q2, SiteReportsQARPeer::NI_NRS_PRG_Q3, SiteReportsQARPeer::NI_NRS_PRG_Q4, SiteReportsQARPeer::NI_NRS_NAR, SiteReportsQARPeer::FH_NAR, SiteReportsQARPeer::AEM_NAR, SiteReportsQARPeer::SA1_PRG_Q1, SiteReportsQARPeer::SA1_PRG_Q2, SiteReportsQARPeer::SA1_PRG_Q3, SiteReportsQARPeer::SA1_PRG_Q4, SiteReportsQARPeer::SA1_PRG_NAR, SiteReportsQARPeer::SA2_PRG_Q1, SiteReportsQARPeer::SA2_PRG_Q2, SiteReportsQARPeer::SA2_PRG_Q3, SiteReportsQARPeer::SA2_PRG_Q4, SiteReportsQARPeer::SA2_PRG_NAR, SiteReportsQARPeer::SA3_PRG_Q1, SiteReportsQARPeer::SA3_PRG_Q2, SiteReportsQARPeer::SA3_PRG_Q3, SiteReportsQARPeer::SA3_PRG_Q4, SiteReportsQARPeer::SA3_PRG_NAR, SiteReportsQARPeer::CREATED_BY, SiteReportsQARPeer::CREATED_ON, SiteReportsQARPeer::UPDATED_BY, SiteReportsQARPeer::UPDATED_ON, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'FACILITY_ID', 'YEAR', 'QUARTER', 'RBS_SS_LAST_REV_DATE_Q1', 'RBS_SS_LAST_REV_DATE_Q2', 'RBS_SS_LAST_REV_DATE_Q3', 'RBS_SS_LAST_REV_DATE_Q4', 'RBS_SS_OSHA_RI_Q1', 'RBS_SS_OSHA_RI_Q2', 'RBS_SS_OSHA_RI_Q3', 'RBS_SS_OSHA_RI_Q4', 'RBS_SS_INJURY_NAR', 'RBS_SS_PSA_NAR', 'RBS_PMCR_PPM_PRG_Q1', 'RBS_PMCR_PPM_PRG_Q2', 'RBS_PMCR_PPM_PRG_Q3', 'RBS_PMCR_PPM_PRG_Q4', 'RBS_PMCR_PPM_NAR', 'RBS_PMCR_PC_PRG_Q1', 'RBS_PMCR_PC_PRG_Q2', 'RBS_PMCR_PC_PRG_Q3', 'RBS_PMCR_PC_PRG_Q4', 'RBS_PMCR_PC_NAR', 'RBS_PMCR_PR_PRG_Q1', 'RBS_PMCR_PR_PRG_Q2', 'RBS_PMCR_PR_PRG_Q3', 'RBS_PMCR_PR_PRG_Q4', 'RBS_PMCR_PR_NAR', 'CB_FE_PRG_Q1', 'CB_FE_PRG_Q2', 'CB_FE_PRG_Q3', 'CB_FE_PRG_Q4', 'CB_FE_NAR', 'NI_ITCA_PRG_Q1', 'NI_ITCA_PRG_Q2', 'NI_ITCA_PRG_Q3', 'NI_ITCA_PRG_Q4', 'NI_ITCA_NAR', 'NI_NEOT_PRG_Q1', 'NI_NEOT_PRG_Q2', 'NI_NEOT_PRG_Q3', 'NI_NEOT_PRG_Q4', 'NI_NEOT_NAR', 'NI_NRS_PRG_Q1', 'NI_NRS_PRG_Q2', 'NI_NRS_PRG_Q3', 'NI_NRS_PRG_Q4', 'NI_NRS_NAR', 'FH_NAR', 'AEM_NAR', 'SA1_PRG_Q1', 'SA1_PRG_Q2', 'SA1_PRG_Q3', 'SA1_PRG_Q4', 'SA1_PRG_NAR', 'SA2_PRG_Q1', 'SA2_PRG_Q2', 'SA2_PRG_Q3', 'SA2_PRG_Q4', 'SA2_PRG_NAR', 'SA3_PRG_Q1', 'SA3_PRG_Q2', 'SA3_PRG_Q3', 'SA3_PRG_Q4', 'SA3_PRG_NAR', 'CREATED_BY', 'CREATED_ON', 'UPDATED_BY', 'UPDATED_ON', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('ID' => 0, 'FACILITY_ID' => 1, 'YEAR' => 2, 'QUARTER' => 3, 'RBS_SS_LAST_REV_DATE_Q1' => 4, 'RBS_SS_LAST_REV_DATE_Q2' => 5, 'RBS_SS_LAST_REV_DATE_Q3' => 6, 'RBS_SS_LAST_REV_DATE_Q4' => 7, 'RBS_SS_RI_Q1' => 8, 'RBS_SS_RI_Q2' => 9, 'RBS_SS_RI_Q3' => 10, 'RBS_SS_RI_Q4' => 11, 'RBS_SS_INJURY_NAR' => 12, 'RBS_SS_PSA_NAR' => 13, 'RBS_PMCR_PPM_PRG_Q1' => 14, 'RBS_PMCR_PPM_PRG_Q2' => 15, 'RBS_PMCR_PPM_PRG_Q3' => 16, 'RBS_PMCR_PPM_PRG_Q4' => 17, 'RBS_PMCR_PPM_NAR' => 18, 'RBS_PMCR_PC_PRG_Q1' => 19, 'RBS_PMCR_PC_PRG_Q2' => 20, 'RBS_PMCR_PC_PRG_Q3' => 21, 'RBS_PMCR_PC_PRG_Q4' => 22, 'RBS_PMCR_PC_NAR' => 23, 'RBS_PMCR_PR_PRG_Q1' => 24, 'RBS_PMCR_PR_PRG_Q2' => 25, 'RBS_PMCR_PR_PRG_Q3' => 26, 'RBS_PMCR_PR_PRG_Q4' => 27, 'RBS_PMCR_PR_NAR' => 28, 'CB_FE_PRG_Q1' => 29, 'CB_FE_PRG_Q2' => 30, 'CB_FE_PRG_Q3' => 31, 'CB_FE_PRG_Q4' => 32, 'CB_FE_NAR' => 33, 'NI_ITCA_PRG_Q1' => 34, 'NI_ITCA_PRG_Q2' => 35, 'NI_ITCA_PRG_Q3' => 36, 'NI_ITCA_PRG_Q4' => 37, 'NI_ITCA_NAR' => 38, 'NI_NEOT_PRG_Q1' => 39, 'NI_NEOT_PRG_Q2' => 40, 'NI_NEOT_PRG_Q3' => 41, 'NI_NEOT_PRG_Q4' => 42, 'NI_NEOT_NAR' => 43, 'NI_NRS_PRG_Q1' => 44, 'NI_NRS_PRG_Q2' => 45, 'NI_NRS_PRG_Q3' => 46, 'NI_NRS_PRG_Q4' => 47, 'NI_NRS_NAR' => 48, 'FH_NAR' => 49, 'AEM_NAR' => 50, 'SA1_PRG_Q1' => 51, 'SA1_PRG_Q2' => 52, 'SA1_PRG_Q3' => 53, 'SA1_PRG_Q4' => 54, 'SA1_PRG_NAR' => 55, 'SA2_PRG_Q1' => 56, 'SA2_PRG_Q2' => 57, 'SA2_PRG_Q3' => 58, 'SA2_PRG_Q4' => 59, 'SA2_PRG_NAR' => 60, 'SA3_PRG_Q1' => 61, 'SA3_PRG_Q2' => 62, 'SA3_PRG_Q3' => 63, 'SA3_PRG_Q4' => 64, 'SA3_PRG_NAR' => 65, 'CREATED_BY' => 66, 'CREATED_ON' => 67, 'UPDATED_BY' => 68, 'UPDATED_ON' => 69, ),
		BasePeer::TYPE_COLNAME => array (SiteReportsQARPeer::ID => 0, SiteReportsQARPeer::FACILITY_ID => 1, SiteReportsQARPeer::YEAR => 2, SiteReportsQARPeer::QUARTER => 3, SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q1 => 4, SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q2 => 5, SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q3 => 6, SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q4 => 7, SiteReportsQARPeer::RBS_SS_OSHA_RI_Q1 => 8, SiteReportsQARPeer::RBS_SS_OSHA_RI_Q2 => 9, SiteReportsQARPeer::RBS_SS_OSHA_RI_Q3 => 10, SiteReportsQARPeer::RBS_SS_OSHA_RI_Q4 => 11, SiteReportsQARPeer::RBS_SS_INJURY_NAR => 12, SiteReportsQARPeer::RBS_SS_PSA_NAR => 13, SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q1 => 14, SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q2 => 15, SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q3 => 16, SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q4 => 17, SiteReportsQARPeer::RBS_PMCR_PPM_NAR => 18, SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q1 => 19, SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q2 => 20, SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q3 => 21, SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q4 => 22, SiteReportsQARPeer::RBS_PMCR_PC_NAR => 23, SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q1 => 24, SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q2 => 25, SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q3 => 26, SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q4 => 27, SiteReportsQARPeer::RBS_PMCR_PR_NAR => 28, SiteReportsQARPeer::CB_FE_PRG_Q1 => 29, SiteReportsQARPeer::CB_FE_PRG_Q2 => 30, SiteReportsQARPeer::CB_FE_PRG_Q3 => 31, SiteReportsQARPeer::CB_FE_PRG_Q4 => 32, SiteReportsQARPeer::CB_FE_NAR => 33, SiteReportsQARPeer::NI_ITCA_PRG_Q1 => 34, SiteReportsQARPeer::NI_ITCA_PRG_Q2 => 35, SiteReportsQARPeer::NI_ITCA_PRG_Q3 => 36, SiteReportsQARPeer::NI_ITCA_PRG_Q4 => 37, SiteReportsQARPeer::NI_ITCA_NAR => 38, SiteReportsQARPeer::NI_NEOT_PRG_Q1 => 39, SiteReportsQARPeer::NI_NEOT_PRG_Q2 => 40, SiteReportsQARPeer::NI_NEOT_PRG_Q3 => 41, SiteReportsQARPeer::NI_NEOT_PRG_Q4 => 42, SiteReportsQARPeer::NI_NEOT_NAR => 43, SiteReportsQARPeer::NI_NRS_PRG_Q1 => 44, SiteReportsQARPeer::NI_NRS_PRG_Q2 => 45, SiteReportsQARPeer::NI_NRS_PRG_Q3 => 46, SiteReportsQARPeer::NI_NRS_PRG_Q4 => 47, SiteReportsQARPeer::NI_NRS_NAR => 48, SiteReportsQARPeer::FH_NAR => 49, SiteReportsQARPeer::AEM_NAR => 50, SiteReportsQARPeer::SA1_PRG_Q1 => 51, SiteReportsQARPeer::SA1_PRG_Q2 => 52, SiteReportsQARPeer::SA1_PRG_Q3 => 53, SiteReportsQARPeer::SA1_PRG_Q4 => 54, SiteReportsQARPeer::SA1_PRG_NAR => 55, SiteReportsQARPeer::SA2_PRG_Q1 => 56, SiteReportsQARPeer::SA2_PRG_Q2 => 57, SiteReportsQARPeer::SA2_PRG_Q3 => 58, SiteReportsQARPeer::SA2_PRG_Q4 => 59, SiteReportsQARPeer::SA2_PRG_NAR => 60, SiteReportsQARPeer::SA3_PRG_Q1 => 61, SiteReportsQARPeer::SA3_PRG_Q2 => 62, SiteReportsQARPeer::SA3_PRG_Q3 => 63, SiteReportsQARPeer::SA3_PRG_Q4 => 64, SiteReportsQARPeer::SA3_PRG_NAR => 65, SiteReportsQARPeer::CREATED_BY => 66, SiteReportsQARPeer::CREATED_ON => 67, SiteReportsQARPeer::UPDATED_BY => 68, SiteReportsQARPeer::UPDATED_ON => 69, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'FACILITY_ID' => 1, 'YEAR' => 2, 'QUARTER' => 3, 'RBS_SS_LAST_REV_DATE_Q1' => 4, 'RBS_SS_LAST_REV_DATE_Q2' => 5, 'RBS_SS_LAST_REV_DATE_Q3' => 6, 'RBS_SS_LAST_REV_DATE_Q4' => 7, 'RBS_SS_OSHA_RI_Q1' => 8, 'RBS_SS_OSHA_RI_Q2' => 9, 'RBS_SS_OSHA_RI_Q3' => 10, 'RBS_SS_OSHA_RI_Q4' => 11, 'RBS_SS_INJURY_NAR' => 12, 'RBS_SS_PSA_NAR' => 13, 'RBS_PMCR_PPM_PRG_Q1' => 14, 'RBS_PMCR_PPM_PRG_Q2' => 15, 'RBS_PMCR_PPM_PRG_Q3' => 16, 'RBS_PMCR_PPM_PRG_Q4' => 17, 'RBS_PMCR_PPM_NAR' => 18, 'RBS_PMCR_PC_PRG_Q1' => 19, 'RBS_PMCR_PC_PRG_Q2' => 20, 'RBS_PMCR_PC_PRG_Q3' => 21, 'RBS_PMCR_PC_PRG_Q4' => 22, 'RBS_PMCR_PC_NAR' => 23, 'RBS_PMCR_PR_PRG_Q1' => 24, 'RBS_PMCR_PR_PRG_Q2' => 25, 'RBS_PMCR_PR_PRG_Q3' => 26, 'RBS_PMCR_PR_PRG_Q4' => 27, 'RBS_PMCR_PR_NAR' => 28, 'CB_FE_PRG_Q1' => 29, 'CB_FE_PRG_Q2' => 30, 'CB_FE_PRG_Q3' => 31, 'CB_FE_PRG_Q4' => 32, 'CB_FE_NAR' => 33, 'NI_ITCA_PRG_Q1' => 34, 'NI_ITCA_PRG_Q2' => 35, 'NI_ITCA_PRG_Q3' => 36, 'NI_ITCA_PRG_Q4' => 37, 'NI_ITCA_NAR' => 38, 'NI_NEOT_PRG_Q1' => 39, 'NI_NEOT_PRG_Q2' => 40, 'NI_NEOT_PRG_Q3' => 41, 'NI_NEOT_PRG_Q4' => 42, 'NI_NEOT_NAR' => 43, 'NI_NRS_PRG_Q1' => 44, 'NI_NRS_PRG_Q2' => 45, 'NI_NRS_PRG_Q3' => 46, 'NI_NRS_PRG_Q4' => 47, 'NI_NRS_NAR' => 48, 'FH_NAR' => 49, 'AEM_NAR' => 50, 'SA1_PRG_Q1' => 51, 'SA1_PRG_Q2' => 52, 'SA1_PRG_Q3' => 53, 'SA1_PRG_Q4' => 54, 'SA1_PRG_NAR' => 55, 'SA2_PRG_Q1' => 56, 'SA2_PRG_Q2' => 57, 'SA2_PRG_Q3' => 58, 'SA2_PRG_Q4' => 59, 'SA2_PRG_NAR' => 60, 'SA3_PRG_Q1' => 61, 'SA3_PRG_Q2' => 62, 'SA3_PRG_Q3' => 63, 'SA3_PRG_Q4' => 64, 'SA3_PRG_NAR' => 65, 'CREATED_BY' => 66, 'CREATED_ON' => 67, 'UPDATED_BY' => 68, 'UPDATED_ON' => 69, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/SiteReportsQARMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.SiteReportsQARMapBuilder');
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
			$map = SiteReportsQARPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. SiteReportsQARPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(SiteReportsQARPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(SiteReportsQARPeer::ID);

		$criteria->addSelectColumn(SiteReportsQARPeer::FACILITY_ID);

		$criteria->addSelectColumn(SiteReportsQARPeer::YEAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::QUARTER);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_SS_LAST_REV_DATE_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_SS_OSHA_RI_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_SS_INJURY_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_SS_PSA_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PPM_PRG_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PPM_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PC_PRG_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PC_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PR_PRG_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::RBS_PMCR_PR_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::CB_FE_PRG_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::CB_FE_PRG_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::CB_FE_PRG_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::CB_FE_PRG_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::CB_FE_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_ITCA_PRG_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_ITCA_PRG_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_ITCA_PRG_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_ITCA_PRG_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_ITCA_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_NEOT_PRG_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_NEOT_PRG_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_NEOT_PRG_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_NEOT_PRG_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_NEOT_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_NRS_PRG_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_NRS_PRG_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_NRS_PRG_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_NRS_PRG_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::NI_NRS_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::FH_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::AEM_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA1_PRG_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA1_PRG_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA1_PRG_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA1_PRG_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA1_PRG_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA2_PRG_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA2_PRG_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA2_PRG_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA2_PRG_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA2_PRG_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA3_PRG_Q1);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA3_PRG_Q2);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA3_PRG_Q3);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA3_PRG_Q4);

		$criteria->addSelectColumn(SiteReportsQARPeer::SA3_PRG_NAR);

		$criteria->addSelectColumn(SiteReportsQARPeer::CREATED_BY);

		$criteria->addSelectColumn(SiteReportsQARPeer::CREATED_ON);

		$criteria->addSelectColumn(SiteReportsQARPeer::UPDATED_BY);

		$criteria->addSelectColumn(SiteReportsQARPeer::UPDATED_ON);

	}

	const COUNT = 'COUNT(SITEREPORTS_QAR.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT SITEREPORTS_QAR.ID)';

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
			$criteria->addSelectColumn(SiteReportsQARPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SiteReportsQARPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = SiteReportsQARPeer::doSelectRS($criteria, $con);
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
	 * @return     SiteReportsQAR
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = SiteReportsQARPeer::doSelect($critcopy, $con);
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
		return SiteReportsQARPeer::populateObjects(SiteReportsQARPeer::doSelectRS($criteria, $con));
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
			SiteReportsQARPeer::addSelectColumns($criteria);
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
		$cls = SiteReportsQARPeer::getOMClass();
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
		return SiteReportsQARPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a SiteReportsQAR or Criteria object.
	 *
	 * @param      mixed $values Criteria or SiteReportsQAR object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from SiteReportsQAR object
		}

		$criteria->remove(SiteReportsQARPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a SiteReportsQAR or Criteria object.
	 *
	 * @param      mixed $values Criteria or SiteReportsQAR object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(SiteReportsQARPeer::ID);
			$selectCriteria->add(SiteReportsQARPeer::ID, $criteria->remove(SiteReportsQARPeer::ID), $comparison);

		} else { // $values is SiteReportsQAR object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the SITEREPORTS_QAR table.
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
			$affectedRows += BasePeer::doDeleteAll(SiteReportsQARPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a SiteReportsQAR or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or SiteReportsQAR object or primary key or array of primary keys
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
			$con = Propel::getConnection(SiteReportsQARPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof SiteReportsQAR) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(SiteReportsQARPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given SiteReportsQAR object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      SiteReportsQAR $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(SiteReportsQAR $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(SiteReportsQARPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(SiteReportsQARPeer::TABLE_NAME);

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

		return BasePeer::doValidate(SiteReportsQARPeer::DATABASE_NAME, SiteReportsQARPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     SiteReportsQAR
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(SiteReportsQARPeer::DATABASE_NAME);

		$criteria->add(SiteReportsQARPeer::ID, $pk);


		$v = SiteReportsQARPeer::doSelect($criteria, $con);

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
			$criteria->add(SiteReportsQARPeer::ID, $pks, Criteria::IN);
			$objs = SiteReportsQARPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseSiteReportsQARPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseSiteReportsQARPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/SiteReportsQARMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.SiteReportsQARMapBuilder');
}
