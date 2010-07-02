<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TSUNAMI_SOCIAL_SCIENCE_DATA' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.tsunami.map
 */
class TsunamiSocialScienceDataMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.tsunami.map.TsunamiSocialScienceDataMapBuilder';

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

		$tMap = $this->dbMap->addTable('TSUNAMI_SOCIAL_SCIENCE_DATA');
		$tMap->setPhpName('TsunamiSocialScienceData');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TSNM_SCL_SCNC_DT_SEQ');

		$tMap->addPrimaryKey('TSUNAMI_SOCIAL_SCIENCE_DATA_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('BKG', 'Bkg', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('BKG_CENSUS', 'BkgCensus', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('BKG_LANGUAGE_ISSUES', 'BkgLanguageIssues', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('BKG_TOURIST_STATS', 'BkgTouristStats', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('BKG_TRANSPORT_SYSTEMS', 'BkgTransportSystems', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('COMM', 'Comm', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('COMM_INFO_FROMG', 'CommInfoFromG', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('COMM_WARN_SYS', 'CommWarnSys', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CRESPONSE', 'Cresponse', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CRESPONSE_INTERVW', 'CresponseIntervw', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CRESPONSE_MITIGATION', 'CresponseMitigation', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CRESPONSE_PREP', 'CresponsePrep', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CRESPONSE_RECOVERY', 'CresponseRecovery', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CRESPONSE_WARNING', 'CresponseWarning', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DAMAGE', 'Damage', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DAMAGE_COST_EST', 'DamageCostEst', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DAMAGE_INDUSTRY', 'DamageIndustry', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DAMAGE_TYPE', 'DamageType', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IMPACT', 'Impact', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IMPACT_NUM_DEAD', 'ImpactNumDead', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IMPACT_NUM_FAM_SEP', 'ImpactNumFamSep', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IMPACT_NUM_HOMELESS', 'ImpactNumHomeless', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IMPACT_NUM_INJURED', 'ImpactNumInjured', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IMPACT_NUM_MISSING', 'ImpactNumMissing', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IRESPONSE', 'Iresponse', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IRESPONSE_INTERVW', 'IresponseIntervw', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IRESPONSE_MITIGATION', 'IresponseMitigation', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IRESPONSE_PREP', 'IresponsePrep', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IRESPONSE_RECOVERY', 'IresponseRecovery', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IRESPONSE_WARNINGS', 'IresponseWarnings', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ORESPONSE', 'Oresponse', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ORESPONSE_DISEASE', 'OresponseDisease', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ORESPONSE_GRELIEF', 'OresponseGrelief', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ORESPONSE_INTERVW', 'OresponseIntervw', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ORESPONSE_MITIGATION', 'OresponseMitigation', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ORESPONSENGORELIEF', 'OresponseNGORelief', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ORESPONSE_PREP', 'OresponsePrep', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ORESPONSE_RECOVERY', 'OresponseRecovery', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('TSUNAMI_DOC_LIB_ID', 'TsunamiDocLibId', 'double', CreoleTypes::NUMERIC, 'TSUNAMI_DOC_LIB', 'TSUNAMI_DOC_LIB_ID', false, 22);

		$tMap->addValidator('BKG', 'maxValue', 'propel.validator.MaxValueValidator', '', 'BKG');

		$tMap->addValidator('BKG', 'notMatch', 'propel.validator.NotMatchValidator', '', 'BKG');

		$tMap->addValidator('BKG', 'required', 'propel.validator.RequiredValidator', '', 'BKG');

		$tMap->addValidator('BKG_CENSUS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'BKG_CENSUS');

		$tMap->addValidator('BKG_CENSUS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'BKG_CENSUS');

		$tMap->addValidator('BKG_CENSUS', 'required', 'propel.validator.RequiredValidator', '', 'BKG_CENSUS');

		$tMap->addValidator('BKG_LANGUAGE_ISSUES', 'maxValue', 'propel.validator.MaxValueValidator', '', 'BKG_LANGUAGE_ISSUES');

		$tMap->addValidator('BKG_LANGUAGE_ISSUES', 'notMatch', 'propel.validator.NotMatchValidator', '', 'BKG_LANGUAGE_ISSUES');

		$tMap->addValidator('BKG_LANGUAGE_ISSUES', 'required', 'propel.validator.RequiredValidator', '', 'BKG_LANGUAGE_ISSUES');

		$tMap->addValidator('BKG_TOURIST_STATS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'BKG_TOURIST_STATS');

		$tMap->addValidator('BKG_TOURIST_STATS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'BKG_TOURIST_STATS');

		$tMap->addValidator('BKG_TOURIST_STATS', 'required', 'propel.validator.RequiredValidator', '', 'BKG_TOURIST_STATS');

		$tMap->addValidator('BKG_TRANSPORT_SYSTEMS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'BKG_TRANSPORT_SYSTEMS');

		$tMap->addValidator('BKG_TRANSPORT_SYSTEMS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'BKG_TRANSPORT_SYSTEMS');

		$tMap->addValidator('BKG_TRANSPORT_SYSTEMS', 'required', 'propel.validator.RequiredValidator', '', 'BKG_TRANSPORT_SYSTEMS');

		$tMap->addValidator('COMM', 'maxValue', 'propel.validator.MaxValueValidator', '', 'COMM');

		$tMap->addValidator('COMM', 'notMatch', 'propel.validator.NotMatchValidator', '', 'COMM');

		$tMap->addValidator('COMM', 'required', 'propel.validator.RequiredValidator', '', 'COMM');

		$tMap->addValidator('COMM_INFO_FROMG', 'maxValue', 'propel.validator.MaxValueValidator', '', 'COMM_INFO_FROMG');

		$tMap->addValidator('COMM_INFO_FROMG', 'notMatch', 'propel.validator.NotMatchValidator', '', 'COMM_INFO_FROMG');

		$tMap->addValidator('COMM_INFO_FROMG', 'required', 'propel.validator.RequiredValidator', '', 'COMM_INFO_FROMG');

		$tMap->addValidator('COMM_WARN_SYS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'COMM_WARN_SYS');

		$tMap->addValidator('COMM_WARN_SYS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'COMM_WARN_SYS');

		$tMap->addValidator('COMM_WARN_SYS', 'required', 'propel.validator.RequiredValidator', '', 'COMM_WARN_SYS');

		$tMap->addValidator('CRESPONSE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CRESPONSE');

		$tMap->addValidator('CRESPONSE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CRESPONSE');

		$tMap->addValidator('CRESPONSE', 'required', 'propel.validator.RequiredValidator', '', 'CRESPONSE');

		$tMap->addValidator('CRESPONSE_INTERVW', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CRESPONSE_INTERVW');

		$tMap->addValidator('CRESPONSE_INTERVW', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CRESPONSE_INTERVW');

		$tMap->addValidator('CRESPONSE_INTERVW', 'required', 'propel.validator.RequiredValidator', '', 'CRESPONSE_INTERVW');

		$tMap->addValidator('CRESPONSE_MITIGATION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CRESPONSE_MITIGATION');

		$tMap->addValidator('CRESPONSE_MITIGATION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CRESPONSE_MITIGATION');

		$tMap->addValidator('CRESPONSE_MITIGATION', 'required', 'propel.validator.RequiredValidator', '', 'CRESPONSE_MITIGATION');

		$tMap->addValidator('CRESPONSE_PREP', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CRESPONSE_PREP');

		$tMap->addValidator('CRESPONSE_PREP', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CRESPONSE_PREP');

		$tMap->addValidator('CRESPONSE_PREP', 'required', 'propel.validator.RequiredValidator', '', 'CRESPONSE_PREP');

		$tMap->addValidator('CRESPONSE_RECOVERY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CRESPONSE_RECOVERY');

		$tMap->addValidator('CRESPONSE_RECOVERY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CRESPONSE_RECOVERY');

		$tMap->addValidator('CRESPONSE_RECOVERY', 'required', 'propel.validator.RequiredValidator', '', 'CRESPONSE_RECOVERY');

		$tMap->addValidator('CRESPONSE_WARNING', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CRESPONSE_WARNING');

		$tMap->addValidator('CRESPONSE_WARNING', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CRESPONSE_WARNING');

		$tMap->addValidator('CRESPONSE_WARNING', 'required', 'propel.validator.RequiredValidator', '', 'CRESPONSE_WARNING');

		$tMap->addValidator('DAMAGE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DAMAGE');

		$tMap->addValidator('DAMAGE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DAMAGE');

		$tMap->addValidator('DAMAGE', 'required', 'propel.validator.RequiredValidator', '', 'DAMAGE');

		$tMap->addValidator('DAMAGE_COST_EST', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DAMAGE_COST_EST');

		$tMap->addValidator('DAMAGE_COST_EST', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DAMAGE_COST_EST');

		$tMap->addValidator('DAMAGE_COST_EST', 'required', 'propel.validator.RequiredValidator', '', 'DAMAGE_COST_EST');

		$tMap->addValidator('DAMAGE_INDUSTRY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DAMAGE_INDUSTRY');

		$tMap->addValidator('DAMAGE_INDUSTRY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DAMAGE_INDUSTRY');

		$tMap->addValidator('DAMAGE_INDUSTRY', 'required', 'propel.validator.RequiredValidator', '', 'DAMAGE_INDUSTRY');

		$tMap->addValidator('DAMAGE_TYPE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DAMAGE_TYPE');

		$tMap->addValidator('DAMAGE_TYPE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DAMAGE_TYPE');

		$tMap->addValidator('DAMAGE_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'DAMAGE_TYPE');

		$tMap->addValidator('IMPACT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IMPACT');

		$tMap->addValidator('IMPACT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IMPACT');

		$tMap->addValidator('IMPACT', 'required', 'propel.validator.RequiredValidator', '', 'IMPACT');

		$tMap->addValidator('IMPACT_NUM_DEAD', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IMPACT_NUM_DEAD');

		$tMap->addValidator('IMPACT_NUM_DEAD', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IMPACT_NUM_DEAD');

		$tMap->addValidator('IMPACT_NUM_DEAD', 'required', 'propel.validator.RequiredValidator', '', 'IMPACT_NUM_DEAD');

		$tMap->addValidator('IMPACT_NUM_FAM_SEP', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IMPACT_NUM_FAM_SEP');

		$tMap->addValidator('IMPACT_NUM_FAM_SEP', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IMPACT_NUM_FAM_SEP');

		$tMap->addValidator('IMPACT_NUM_FAM_SEP', 'required', 'propel.validator.RequiredValidator', '', 'IMPACT_NUM_FAM_SEP');

		$tMap->addValidator('IMPACT_NUM_HOMELESS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IMPACT_NUM_HOMELESS');

		$tMap->addValidator('IMPACT_NUM_HOMELESS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IMPACT_NUM_HOMELESS');

		$tMap->addValidator('IMPACT_NUM_HOMELESS', 'required', 'propel.validator.RequiredValidator', '', 'IMPACT_NUM_HOMELESS');

		$tMap->addValidator('IMPACT_NUM_INJURED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IMPACT_NUM_INJURED');

		$tMap->addValidator('IMPACT_NUM_INJURED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IMPACT_NUM_INJURED');

		$tMap->addValidator('IMPACT_NUM_INJURED', 'required', 'propel.validator.RequiredValidator', '', 'IMPACT_NUM_INJURED');

		$tMap->addValidator('IMPACT_NUM_MISSING', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IMPACT_NUM_MISSING');

		$tMap->addValidator('IMPACT_NUM_MISSING', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IMPACT_NUM_MISSING');

		$tMap->addValidator('IMPACT_NUM_MISSING', 'required', 'propel.validator.RequiredValidator', '', 'IMPACT_NUM_MISSING');

		$tMap->addValidator('IRESPONSE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IRESPONSE');

		$tMap->addValidator('IRESPONSE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IRESPONSE');

		$tMap->addValidator('IRESPONSE', 'required', 'propel.validator.RequiredValidator', '', 'IRESPONSE');

		$tMap->addValidator('IRESPONSE_INTERVW', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IRESPONSE_INTERVW');

		$tMap->addValidator('IRESPONSE_INTERVW', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IRESPONSE_INTERVW');

		$tMap->addValidator('IRESPONSE_INTERVW', 'required', 'propel.validator.RequiredValidator', '', 'IRESPONSE_INTERVW');

		$tMap->addValidator('IRESPONSE_MITIGATION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IRESPONSE_MITIGATION');

		$tMap->addValidator('IRESPONSE_MITIGATION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IRESPONSE_MITIGATION');

		$tMap->addValidator('IRESPONSE_MITIGATION', 'required', 'propel.validator.RequiredValidator', '', 'IRESPONSE_MITIGATION');

		$tMap->addValidator('IRESPONSE_PREP', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IRESPONSE_PREP');

		$tMap->addValidator('IRESPONSE_PREP', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IRESPONSE_PREP');

		$tMap->addValidator('IRESPONSE_PREP', 'required', 'propel.validator.RequiredValidator', '', 'IRESPONSE_PREP');

		$tMap->addValidator('IRESPONSE_RECOVERY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IRESPONSE_RECOVERY');

		$tMap->addValidator('IRESPONSE_RECOVERY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IRESPONSE_RECOVERY');

		$tMap->addValidator('IRESPONSE_RECOVERY', 'required', 'propel.validator.RequiredValidator', '', 'IRESPONSE_RECOVERY');

		$tMap->addValidator('IRESPONSE_WARNINGS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'IRESPONSE_WARNINGS');

		$tMap->addValidator('IRESPONSE_WARNINGS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'IRESPONSE_WARNINGS');

		$tMap->addValidator('IRESPONSE_WARNINGS', 'required', 'propel.validator.RequiredValidator', '', 'IRESPONSE_WARNINGS');

		$tMap->addValidator('ORESPONSE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ORESPONSE');

		$tMap->addValidator('ORESPONSE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ORESPONSE');

		$tMap->addValidator('ORESPONSE', 'required', 'propel.validator.RequiredValidator', '', 'ORESPONSE');

		$tMap->addValidator('ORESPONSENGORELIEF', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ORESPONSENGORELIEF');

		$tMap->addValidator('ORESPONSENGORELIEF', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ORESPONSENGORELIEF');

		$tMap->addValidator('ORESPONSENGORELIEF', 'required', 'propel.validator.RequiredValidator', '', 'ORESPONSENGORELIEF');

		$tMap->addValidator('ORESPONSE_DISEASE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ORESPONSE_DISEASE');

		$tMap->addValidator('ORESPONSE_DISEASE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ORESPONSE_DISEASE');

		$tMap->addValidator('ORESPONSE_DISEASE', 'required', 'propel.validator.RequiredValidator', '', 'ORESPONSE_DISEASE');

		$tMap->addValidator('ORESPONSE_GRELIEF', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ORESPONSE_GRELIEF');

		$tMap->addValidator('ORESPONSE_GRELIEF', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ORESPONSE_GRELIEF');

		$tMap->addValidator('ORESPONSE_GRELIEF', 'required', 'propel.validator.RequiredValidator', '', 'ORESPONSE_GRELIEF');

		$tMap->addValidator('ORESPONSE_INTERVW', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ORESPONSE_INTERVW');

		$tMap->addValidator('ORESPONSE_INTERVW', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ORESPONSE_INTERVW');

		$tMap->addValidator('ORESPONSE_INTERVW', 'required', 'propel.validator.RequiredValidator', '', 'ORESPONSE_INTERVW');

		$tMap->addValidator('ORESPONSE_MITIGATION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ORESPONSE_MITIGATION');

		$tMap->addValidator('ORESPONSE_MITIGATION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ORESPONSE_MITIGATION');

		$tMap->addValidator('ORESPONSE_MITIGATION', 'required', 'propel.validator.RequiredValidator', '', 'ORESPONSE_MITIGATION');

		$tMap->addValidator('ORESPONSE_PREP', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ORESPONSE_PREP');

		$tMap->addValidator('ORESPONSE_PREP', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ORESPONSE_PREP');

		$tMap->addValidator('ORESPONSE_PREP', 'required', 'propel.validator.RequiredValidator', '', 'ORESPONSE_PREP');

		$tMap->addValidator('ORESPONSE_RECOVERY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ORESPONSE_RECOVERY');

		$tMap->addValidator('ORESPONSE_RECOVERY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ORESPONSE_RECOVERY');

		$tMap->addValidator('ORESPONSE_RECOVERY', 'required', 'propel.validator.RequiredValidator', '', 'ORESPONSE_RECOVERY');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_SOCIAL_SCIENCE_DATA_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_SOCIAL_SCIENCE_DATA_ID');

		$tMap->addValidator('TSUNAMI_SOCIAL_SCIENCE_DATA_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_SOCIAL_SCIENCE_DATA_ID');

		$tMap->addValidator('TSUNAMI_SOCIAL_SCIENCE_DATA_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_SOCIAL_SCIENCE_DATA_ID');

		$tMap->addValidator('TSUNAMI_SOCIAL_SCIENCE_DATA_ID', 'unique', 'propel.validator.UniqueValidator', '', 'TSUNAMI_SOCIAL_SCIENCE_DATA_ID');

	} // doBuild()

} // TsunamiSocialScienceDataMapBuilder
