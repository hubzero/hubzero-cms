<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SITEREPORTS_QAR' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.map
 */
class SiteReportsQARMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SiteReportsQARMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap('NEEScentral');

		$tMap = $this->dbMap->addTable('SITEREPORTS_QAR');
		$tMap->setPhpName('SiteReportsQAR');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SITEREPORTS_QAR_SEQ');

		$tMap->addPrimaryKey('ID', 'ID', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('FACILITY_ID', 'FACILITY_ID', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('YEAR', 'YEAR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QUARTER', 'QUARTER', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_SS_LAST_REV_DATE_Q1', 'RBS_SS_LAST_REV_DATE_Q1', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('RBS_SS_LAST_REV_DATE_Q2', 'RBS_SS_LAST_REV_DATE_Q2', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('RBS_SS_LAST_REV_DATE_Q3', 'RBS_SS_LAST_REV_DATE_Q3', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('RBS_SS_LAST_REV_DATE_Q4', 'RBS_SS_LAST_REV_DATE_Q4', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('RBS_SS_OSHA_RI_Q1', 'RBS_SS_RI_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_SS_OSHA_RI_Q2', 'RBS_SS_RI_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_SS_OSHA_RI_Q3', 'RBS_SS_RI_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_SS_OSHA_RI_Q4', 'RBS_SS_RI_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_SS_INJURY_NAR', 'RBS_SS_INJURY_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('RBS_SS_PSA_NAR', 'RBS_SS_PSA_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('RBS_PMCR_PPM_PRG_Q1', 'RBS_PMCR_PPM_PRG_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PPM_PRG_Q2', 'RBS_PMCR_PPM_PRG_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PPM_PRG_Q3', 'RBS_PMCR_PPM_PRG_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PPM_PRG_Q4', 'RBS_PMCR_PPM_PRG_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PPM_NAR', 'RBS_PMCR_PPM_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('RBS_PMCR_PC_PRG_Q1', 'RBS_PMCR_PC_PRG_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PC_PRG_Q2', 'RBS_PMCR_PC_PRG_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PC_PRG_Q3', 'RBS_PMCR_PC_PRG_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PC_PRG_Q4', 'RBS_PMCR_PC_PRG_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PC_NAR', 'RBS_PMCR_PC_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('RBS_PMCR_PR_PRG_Q1', 'RBS_PMCR_PR_PRG_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PR_PRG_Q2', 'RBS_PMCR_PR_PRG_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PR_PRG_Q3', 'RBS_PMCR_PR_PRG_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PR_PRG_Q4', 'RBS_PMCR_PR_PRG_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RBS_PMCR_PR_NAR', 'RBS_PMCR_PR_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('CB_FE_PRG_Q1', 'CB_FE_PRG_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CB_FE_PRG_Q2', 'CB_FE_PRG_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CB_FE_PRG_Q3', 'CB_FE_PRG_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CB_FE_PRG_Q4', 'CB_FE_PRG_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CB_FE_NAR', 'CB_FE_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('NI_ITCA_PRG_Q1', 'NI_ITCA_PRG_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_ITCA_PRG_Q2', 'NI_ITCA_PRG_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_ITCA_PRG_Q3', 'NI_ITCA_PRG_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_ITCA_PRG_Q4', 'NI_ITCA_PRG_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_ITCA_NAR', 'NI_ITCA_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('NI_NEOT_PRG_Q1', 'NI_NEOT_PRG_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_NEOT_PRG_Q2', 'NI_NEOT_PRG_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_NEOT_PRG_Q3', 'NI_NEOT_PRG_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_NEOT_PRG_Q4', 'NI_NEOT_PRG_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_NEOT_NAR', 'NI_NEOT_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('NI_NRS_PRG_Q1', 'NI_NRS_PRG_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_NRS_PRG_Q2', 'NI_NRS_PRG_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_NRS_PRG_Q3', 'NI_NRS_PRG_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_NRS_PRG_Q4', 'NI_NRS_PRG_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NI_NRS_NAR', 'NI_NRS_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('FH_NAR', 'FH_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('AEM_NAR', 'AEM_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('SA1_PRG_Q1', 'SA1_PRG_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA1_PRG_Q2', 'SA1_PRG_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA1_PRG_Q3', 'SA1_PRG_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA1_PRG_Q4', 'SA1_PRG_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA1_PRG_NAR', 'SA1_PRG_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('SA2_PRG_Q1', 'SA2_PRG_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA2_PRG_Q2', 'SA2_PRG_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA2_PRG_Q3', 'SA2_PRG_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA2_PRG_Q4', 'SA2_PRG_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA2_PRG_NAR', 'SA2_PRG_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('SA3_PRG_Q1', 'SA3_PRG_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA3_PRG_Q2', 'SA3_PRG_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA3_PRG_Q3', 'SA3_PRG_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA3_PRG_Q4', 'SA3_PRG_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SA3_PRG_NAR', 'SA3_PRG_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('CREATED_BY', 'CREATED_BY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('CREATED_ON', 'CREATED_ON', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('UPDATED_BY', 'UPDATED_BY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('UPDATED_ON', 'UPDATED_ON', 'int', CreoleTypes::DATE, false, null);

	} // doBuild()

} // SiteReportsQARMapBuilder
