<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SITEREPORTS_QFR' table to 'NEEScentral' DatabaseMap object.
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
class SiteReportsQFRMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SiteReportsQFRMapBuilder';

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

		$tMap = $this->dbMap->addTable('SITEREPORTS_QFR');
		$tMap->setPhpName('SiteReportsQFR');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SITEREPORTS_QFR_SEQ');

		$tMap->addPrimaryKey('ID', 'ID', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('FACILITY_ID', 'FACILITY_ID', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('YEAR', 'YEAR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QUARTER', 'QUARTER', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PREPARED_BY', 'PREPARED_BY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('PREPARERS_TITLE', 'PREPARERS_TITLE', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('PREPARED_DATE', 'PREPARED_DATE', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('REPORT_PERIOD', 'REPORT_PERIOD', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('SUBAWARDED_FUNDED_AMT', 'SUBAWARDED_FUNDED_AMT', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_SR_P_COST', 'QFR_SR_P_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_SR_E_COST', 'QFR_SR_E_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_SR_PSC_COST', 'QFR_SR_PSC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_SR_ODC_COST', 'QFR_SR_ODC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_SR_IC_COST', 'QFR_SR_IC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NR_P_COST', 'QFR_NR_P_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NR_E_COST', 'QFR_NR_E_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NR_PSC_COST', 'QFR_NR_PSC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NR_ODC_COST', 'QFR_NR_ODC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NR_IC_COST', 'QFR_NR_IC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_ITCA_P_COST', 'QFR_ITCA_P_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_ITCA_E_COST', 'QFR_ITCA_E_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_ITCA_PSC_COST', 'QFR_ITCA_PSC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_ITCA_ODC_COST', 'QFR_ITCA_ODC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_ITCA_IC_COST', 'QFR_ITCA_IC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NEOT_P_COST', 'QFR_NEOT_P_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NEOT_E_COST', 'QFR_NEOT_E_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NEOT_PSC_COST', 'QFR_NEOT_PSC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NEOT_ODC_COST', 'QFR_NEOT_ODC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NEOT_IC_COST', 'QFR_NEOT_IC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_FEA_P_COST', 'QFR_FEA_P_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_FEA_E_COST', 'QFR_FEA_E_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_FEA_PSC_COST', 'QFR_FEA_PSC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_FEA_ODC_COST', 'QFR_FEA_ODC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_FEA_IC_COST', 'QFR_FEA_IC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_AEM_P_COST', 'QFR_AEM_P_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_AEM_E_COST', 'QFR_AEM_E_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_AEM_PSC_COST', 'QFR_AEM_PSC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_AEM_ODC_COST', 'QFR_AEM_ODC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_AEM_IC_COST', 'QFR_AEM_IC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NRS_P_COST', 'QFR_NRS_P_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NRS_E_COST', 'QFR_NRS_E_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NRS_PSC_COST', 'QFR_NRS_PSC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NRS_ODC_COST', 'QFR_NRS_ODC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('QFR_NRS_IC_COST', 'QFR_NRS_IC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FY_BUDGET_SURS', 'FY_BUDGET_SURS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FY_BUDGET_SR', 'FY_BUDGET_SR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FY_BUDGET_NR', 'FY_BUDGET_NR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FY_BUDGET_ITCA', 'FY_BUDGET_ITCA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FY_BUDGET_FEA', 'FY_BUDGET_FEA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FY_BUDGET_NEOT', 'FY_BUDGET_NEOT', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FY_BUDGET_AEM', 'FY_BUDGET_AEM', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FY_BUDGET_NRS', 'FY_BUDGET_NRS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q1RE_SURS', 'Q1RE_SURS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q1RE_SR', 'Q1RE_SR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q1RE_NR', 'Q1RE_NR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q1RE_ITCA', 'Q1RE_ITCA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q1RE_FEA', 'Q1RE_FEA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q1RE_NEOT', 'Q1RE_NEOT', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q1RE_AEM', 'Q1RE_AEM', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q1RE_NRS', 'Q1RE_NRS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q2RE_SURS', 'Q2RE_SURS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q2RE_SR', 'Q2RE_SR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q2RE_NR', 'Q2RE_NR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q2RE_ITCA', 'Q2RE_ITCA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q2RE_FEA', 'Q2RE_FEA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q2RE_NEOT', 'Q2RE_NEOT', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q2RE_AEM', 'Q2RE_AEM', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q2RE_NRS', 'Q2RE_NRS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q3RE_SURS', 'Q3RE_SURS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q3RE_SR', 'Q3RE_SR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q3RE_NR', 'Q3RE_NR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q3RE_ITCA', 'Q3RE_ITCA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q3RE_FEA', 'Q3RE_FEA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q3RE_NEOT', 'Q3RE_NEOT', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q3RE_AEM', 'Q3RE_AEM', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q3RE_NRS', 'Q3RE_NRS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q4RE_SURS', 'Q4RE_SURS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q4RE_SR', 'Q4RE_SR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q4RE_NR', 'Q4RE_NR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q4RE_ITCA', 'Q4RE_ITCA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q4RE_FEA', 'Q4RE_FEA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q4RE_NEOT', 'Q4RE_NEOT', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q4RE_AEM', 'Q4RE_AEM', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q4RE_NRS', 'Q4RE_NRS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PQA_SURS', 'PQA_SURS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PQA_SR', 'PQA_SR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PQA_NR', 'PQA_NR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PQA_ITCA', 'PQA_ITCA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PQA_FEA', 'PQA_FEA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PQA_NEOT', 'PQA_NEOT', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PQA_AEM', 'PQA_AEM', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PQA_NRS', 'PQA_NRS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CQE_SURS', 'CQE_SURS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CQE_SR', 'CQE_SR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CQE_NR', 'CQE_NR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CQE_ITCA', 'CQE_ITCA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CQE_FEA', 'CQE_FEA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CQE_NEOT', 'CQE_NEOT', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CQE_AEM', 'CQE_AEM', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CQE_NRS', 'CQE_NRS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP1_P', 'SUPBUD_SUP1_P', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP1_E', 'SUPBUD_SUP1_E', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP1_PSC', 'SUPBUD_SUP1_PSC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP1_ODC', 'SUPBUD_SUP1_ODC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP1_IC', 'SUPBUD_SUP1_IC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP1_SA', 'SUPBUD_SUP1_SA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP2_P', 'SUPBUD_SUP2_P', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP2_E', 'SUPBUD_SUP2_E', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP2_PSC', 'SUPBUD_SUP2_PSC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP2_ODC', 'SUPBUD_SUP2_ODC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP2_IC', 'SUPBUD_SUP2_IC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP2_SA', 'SUPBUD_SUP2_SA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP3_P', 'SUPBUD_SUP3_P', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP3_E', 'SUPBUD_SUP3_E', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP3_PSC', 'SUPBUD_SUP3_PSC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP3_ODC', 'SUPBUD_SUP3_ODC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP3_IC', 'SUPBUD_SUP3_IC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP3_SA', 'SUPBUD_SUP3_SA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP4_P', 'SUPBUD_SUP4_P', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP4_E', 'SUPBUD_SUP4_E', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP4_PSC', 'SUPBUD_SUP4_PSC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP4_ODC', 'SUPBUD_SUP4_ODC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP4_IC', 'SUPBUD_SUP4_IC', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPBUD_SUP4_SA', 'SUPBUD_SUP4_SA', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PI_BEG_BAL', 'PI_BEG_BAL', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PI_PIR', 'PI_PIR', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PI_PIE', 'PI_PIE', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PI_NAR', 'PI_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('CREATED_BY', 'CREATED_BY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('CREATED_ON', 'CREATED_ON', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('UPDATED_BY', 'UPDATED_BY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('UPDATED_ON', 'UPDATED_ON', 'int', CreoleTypes::DATE, false, null);

	} // doBuild()

} // SiteReportsQFRMapBuilder
