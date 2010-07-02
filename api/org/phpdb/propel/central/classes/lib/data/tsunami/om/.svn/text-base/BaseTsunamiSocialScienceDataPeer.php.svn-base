<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by TsunamiSocialScienceDataPeer::getOMClass()
include_once 'lib/data/tsunami/TsunamiSocialScienceData.php';

/**
 * Base static class for performing query and update operations on the 'TSUNAMI_SOCIAL_SCIENCE_DATA' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiSocialScienceDataPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'TSUNAMI_SOCIAL_SCIENCE_DATA';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.tsunami.TsunamiSocialScienceData';

	/** The total number of columns. */
	const NUM_COLUMNS = 40;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the TSUNAMI_SOCIAL_SCIENCE_DATA_ID field */
	const TSUNAMI_SOCIAL_SCIENCE_DATA_ID = 'TSUNAMI_SOCIAL_SCIENCE_DATA.TSUNAMI_SOCIAL_SCIENCE_DATA_ID';

	/** the column name for the BKG field */
	const BKG = 'TSUNAMI_SOCIAL_SCIENCE_DATA.BKG';

	/** the column name for the BKG_CENSUS field */
	const BKG_CENSUS = 'TSUNAMI_SOCIAL_SCIENCE_DATA.BKG_CENSUS';

	/** the column name for the BKG_LANGUAGE_ISSUES field */
	const BKG_LANGUAGE_ISSUES = 'TSUNAMI_SOCIAL_SCIENCE_DATA.BKG_LANGUAGE_ISSUES';

	/** the column name for the BKG_TOURIST_STATS field */
	const BKG_TOURIST_STATS = 'TSUNAMI_SOCIAL_SCIENCE_DATA.BKG_TOURIST_STATS';

	/** the column name for the BKG_TRANSPORT_SYSTEMS field */
	const BKG_TRANSPORT_SYSTEMS = 'TSUNAMI_SOCIAL_SCIENCE_DATA.BKG_TRANSPORT_SYSTEMS';

	/** the column name for the COMM field */
	const COMM = 'TSUNAMI_SOCIAL_SCIENCE_DATA.COMM';

	/** the column name for the COMM_INFO_FROMG field */
	const COMM_INFO_FROMG = 'TSUNAMI_SOCIAL_SCIENCE_DATA.COMM_INFO_FROMG';

	/** the column name for the COMM_WARN_SYS field */
	const COMM_WARN_SYS = 'TSUNAMI_SOCIAL_SCIENCE_DATA.COMM_WARN_SYS';

	/** the column name for the CRESPONSE field */
	const CRESPONSE = 'TSUNAMI_SOCIAL_SCIENCE_DATA.CRESPONSE';

	/** the column name for the CRESPONSE_INTERVW field */
	const CRESPONSE_INTERVW = 'TSUNAMI_SOCIAL_SCIENCE_DATA.CRESPONSE_INTERVW';

	/** the column name for the CRESPONSE_MITIGATION field */
	const CRESPONSE_MITIGATION = 'TSUNAMI_SOCIAL_SCIENCE_DATA.CRESPONSE_MITIGATION';

	/** the column name for the CRESPONSE_PREP field */
	const CRESPONSE_PREP = 'TSUNAMI_SOCIAL_SCIENCE_DATA.CRESPONSE_PREP';

	/** the column name for the CRESPONSE_RECOVERY field */
	const CRESPONSE_RECOVERY = 'TSUNAMI_SOCIAL_SCIENCE_DATA.CRESPONSE_RECOVERY';

	/** the column name for the CRESPONSE_WARNING field */
	const CRESPONSE_WARNING = 'TSUNAMI_SOCIAL_SCIENCE_DATA.CRESPONSE_WARNING';

	/** the column name for the DAMAGE field */
	const DAMAGE = 'TSUNAMI_SOCIAL_SCIENCE_DATA.DAMAGE';

	/** the column name for the DAMAGE_COST_EST field */
	const DAMAGE_COST_EST = 'TSUNAMI_SOCIAL_SCIENCE_DATA.DAMAGE_COST_EST';

	/** the column name for the DAMAGE_INDUSTRY field */
	const DAMAGE_INDUSTRY = 'TSUNAMI_SOCIAL_SCIENCE_DATA.DAMAGE_INDUSTRY';

	/** the column name for the DAMAGE_TYPE field */
	const DAMAGE_TYPE = 'TSUNAMI_SOCIAL_SCIENCE_DATA.DAMAGE_TYPE';

	/** the column name for the IMPACT field */
	const IMPACT = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IMPACT';

	/** the column name for the IMPACT_NUM_DEAD field */
	const IMPACT_NUM_DEAD = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IMPACT_NUM_DEAD';

	/** the column name for the IMPACT_NUM_FAM_SEP field */
	const IMPACT_NUM_FAM_SEP = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IMPACT_NUM_FAM_SEP';

	/** the column name for the IMPACT_NUM_HOMELESS field */
	const IMPACT_NUM_HOMELESS = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IMPACT_NUM_HOMELESS';

	/** the column name for the IMPACT_NUM_INJURED field */
	const IMPACT_NUM_INJURED = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IMPACT_NUM_INJURED';

	/** the column name for the IMPACT_NUM_MISSING field */
	const IMPACT_NUM_MISSING = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IMPACT_NUM_MISSING';

	/** the column name for the IRESPONSE field */
	const IRESPONSE = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IRESPONSE';

	/** the column name for the IRESPONSE_INTERVW field */
	const IRESPONSE_INTERVW = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IRESPONSE_INTERVW';

	/** the column name for the IRESPONSE_MITIGATION field */
	const IRESPONSE_MITIGATION = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IRESPONSE_MITIGATION';

	/** the column name for the IRESPONSE_PREP field */
	const IRESPONSE_PREP = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IRESPONSE_PREP';

	/** the column name for the IRESPONSE_RECOVERY field */
	const IRESPONSE_RECOVERY = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IRESPONSE_RECOVERY';

	/** the column name for the IRESPONSE_WARNINGS field */
	const IRESPONSE_WARNINGS = 'TSUNAMI_SOCIAL_SCIENCE_DATA.IRESPONSE_WARNINGS';

	/** the column name for the ORESPONSE field */
	const ORESPONSE = 'TSUNAMI_SOCIAL_SCIENCE_DATA.ORESPONSE';

	/** the column name for the ORESPONSE_DISEASE field */
	const ORESPONSE_DISEASE = 'TSUNAMI_SOCIAL_SCIENCE_DATA.ORESPONSE_DISEASE';

	/** the column name for the ORESPONSE_GRELIEF field */
	const ORESPONSE_GRELIEF = 'TSUNAMI_SOCIAL_SCIENCE_DATA.ORESPONSE_GRELIEF';

	/** the column name for the ORESPONSE_INTERVW field */
	const ORESPONSE_INTERVW = 'TSUNAMI_SOCIAL_SCIENCE_DATA.ORESPONSE_INTERVW';

	/** the column name for the ORESPONSE_MITIGATION field */
	const ORESPONSE_MITIGATION = 'TSUNAMI_SOCIAL_SCIENCE_DATA.ORESPONSE_MITIGATION';

	/** the column name for the ORESPONSENGORELIEF field */
	const ORESPONSENGORELIEF = 'TSUNAMI_SOCIAL_SCIENCE_DATA.ORESPONSENGORELIEF';

	/** the column name for the ORESPONSE_PREP field */
	const ORESPONSE_PREP = 'TSUNAMI_SOCIAL_SCIENCE_DATA.ORESPONSE_PREP';

	/** the column name for the ORESPONSE_RECOVERY field */
	const ORESPONSE_RECOVERY = 'TSUNAMI_SOCIAL_SCIENCE_DATA.ORESPONSE_RECOVERY';

	/** the column name for the TSUNAMI_DOC_LIB_ID field */
	const TSUNAMI_DOC_LIB_ID = 'TSUNAMI_SOCIAL_SCIENCE_DATA.TSUNAMI_DOC_LIB_ID';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Bkg', 'BkgCensus', 'BkgLanguageIssues', 'BkgTouristStats', 'BkgTransportSystems', 'Comm', 'CommInfoFromG', 'CommWarnSys', 'Cresponse', 'CresponseIntervw', 'CresponseMitigation', 'CresponsePrep', 'CresponseRecovery', 'CresponseWarning', 'Damage', 'DamageCostEst', 'DamageIndustry', 'DamageType', 'Impact', 'ImpactNumDead', 'ImpactNumFamSep', 'ImpactNumHomeless', 'ImpactNumInjured', 'ImpactNumMissing', 'Iresponse', 'IresponseIntervw', 'IresponseMitigation', 'IresponsePrep', 'IresponseRecovery', 'IresponseWarnings', 'Oresponse', 'OresponseDisease', 'OresponseGrelief', 'OresponseIntervw', 'OresponseMitigation', 'OresponseNGORelief', 'OresponsePrep', 'OresponseRecovery', 'TsunamiDocLibId', ),
		BasePeer::TYPE_COLNAME => array (TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID, TsunamiSocialScienceDataPeer::BKG, TsunamiSocialScienceDataPeer::BKG_CENSUS, TsunamiSocialScienceDataPeer::BKG_LANGUAGE_ISSUES, TsunamiSocialScienceDataPeer::BKG_TOURIST_STATS, TsunamiSocialScienceDataPeer::BKG_TRANSPORT_SYSTEMS, TsunamiSocialScienceDataPeer::COMM, TsunamiSocialScienceDataPeer::COMM_INFO_FROMG, TsunamiSocialScienceDataPeer::COMM_WARN_SYS, TsunamiSocialScienceDataPeer::CRESPONSE, TsunamiSocialScienceDataPeer::CRESPONSE_INTERVW, TsunamiSocialScienceDataPeer::CRESPONSE_MITIGATION, TsunamiSocialScienceDataPeer::CRESPONSE_PREP, TsunamiSocialScienceDataPeer::CRESPONSE_RECOVERY, TsunamiSocialScienceDataPeer::CRESPONSE_WARNING, TsunamiSocialScienceDataPeer::DAMAGE, TsunamiSocialScienceDataPeer::DAMAGE_COST_EST, TsunamiSocialScienceDataPeer::DAMAGE_INDUSTRY, TsunamiSocialScienceDataPeer::DAMAGE_TYPE, TsunamiSocialScienceDataPeer::IMPACT, TsunamiSocialScienceDataPeer::IMPACT_NUM_DEAD, TsunamiSocialScienceDataPeer::IMPACT_NUM_FAM_SEP, TsunamiSocialScienceDataPeer::IMPACT_NUM_HOMELESS, TsunamiSocialScienceDataPeer::IMPACT_NUM_INJURED, TsunamiSocialScienceDataPeer::IMPACT_NUM_MISSING, TsunamiSocialScienceDataPeer::IRESPONSE, TsunamiSocialScienceDataPeer::IRESPONSE_INTERVW, TsunamiSocialScienceDataPeer::IRESPONSE_MITIGATION, TsunamiSocialScienceDataPeer::IRESPONSE_PREP, TsunamiSocialScienceDataPeer::IRESPONSE_RECOVERY, TsunamiSocialScienceDataPeer::IRESPONSE_WARNINGS, TsunamiSocialScienceDataPeer::ORESPONSE, TsunamiSocialScienceDataPeer::ORESPONSE_DISEASE, TsunamiSocialScienceDataPeer::ORESPONSE_GRELIEF, TsunamiSocialScienceDataPeer::ORESPONSE_INTERVW, TsunamiSocialScienceDataPeer::ORESPONSE_MITIGATION, TsunamiSocialScienceDataPeer::ORESPONSENGORELIEF, TsunamiSocialScienceDataPeer::ORESPONSE_PREP, TsunamiSocialScienceDataPeer::ORESPONSE_RECOVERY, TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_SOCIAL_SCIENCE_DATA_ID', 'BKG', 'BKG_CENSUS', 'BKG_LANGUAGE_ISSUES', 'BKG_TOURIST_STATS', 'BKG_TRANSPORT_SYSTEMS', 'COMM', 'COMM_INFO_FROMG', 'COMM_WARN_SYS', 'CRESPONSE', 'CRESPONSE_INTERVW', 'CRESPONSE_MITIGATION', 'CRESPONSE_PREP', 'CRESPONSE_RECOVERY', 'CRESPONSE_WARNING', 'DAMAGE', 'DAMAGE_COST_EST', 'DAMAGE_INDUSTRY', 'DAMAGE_TYPE', 'IMPACT', 'IMPACT_NUM_DEAD', 'IMPACT_NUM_FAM_SEP', 'IMPACT_NUM_HOMELESS', 'IMPACT_NUM_INJURED', 'IMPACT_NUM_MISSING', 'IRESPONSE', 'IRESPONSE_INTERVW', 'IRESPONSE_MITIGATION', 'IRESPONSE_PREP', 'IRESPONSE_RECOVERY', 'IRESPONSE_WARNINGS', 'ORESPONSE', 'ORESPONSE_DISEASE', 'ORESPONSE_GRELIEF', 'ORESPONSE_INTERVW', 'ORESPONSE_MITIGATION', 'ORESPONSENGORELIEF', 'ORESPONSE_PREP', 'ORESPONSE_RECOVERY', 'TSUNAMI_DOC_LIB_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Bkg' => 1, 'BkgCensus' => 2, 'BkgLanguageIssues' => 3, 'BkgTouristStats' => 4, 'BkgTransportSystems' => 5, 'Comm' => 6, 'CommInfoFromG' => 7, 'CommWarnSys' => 8, 'Cresponse' => 9, 'CresponseIntervw' => 10, 'CresponseMitigation' => 11, 'CresponsePrep' => 12, 'CresponseRecovery' => 13, 'CresponseWarning' => 14, 'Damage' => 15, 'DamageCostEst' => 16, 'DamageIndustry' => 17, 'DamageType' => 18, 'Impact' => 19, 'ImpactNumDead' => 20, 'ImpactNumFamSep' => 21, 'ImpactNumHomeless' => 22, 'ImpactNumInjured' => 23, 'ImpactNumMissing' => 24, 'Iresponse' => 25, 'IresponseIntervw' => 26, 'IresponseMitigation' => 27, 'IresponsePrep' => 28, 'IresponseRecovery' => 29, 'IresponseWarnings' => 30, 'Oresponse' => 31, 'OresponseDisease' => 32, 'OresponseGrelief' => 33, 'OresponseIntervw' => 34, 'OresponseMitigation' => 35, 'OresponseNGORelief' => 36, 'OresponsePrep' => 37, 'OresponseRecovery' => 38, 'TsunamiDocLibId' => 39, ),
		BasePeer::TYPE_COLNAME => array (TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID => 0, TsunamiSocialScienceDataPeer::BKG => 1, TsunamiSocialScienceDataPeer::BKG_CENSUS => 2, TsunamiSocialScienceDataPeer::BKG_LANGUAGE_ISSUES => 3, TsunamiSocialScienceDataPeer::BKG_TOURIST_STATS => 4, TsunamiSocialScienceDataPeer::BKG_TRANSPORT_SYSTEMS => 5, TsunamiSocialScienceDataPeer::COMM => 6, TsunamiSocialScienceDataPeer::COMM_INFO_FROMG => 7, TsunamiSocialScienceDataPeer::COMM_WARN_SYS => 8, TsunamiSocialScienceDataPeer::CRESPONSE => 9, TsunamiSocialScienceDataPeer::CRESPONSE_INTERVW => 10, TsunamiSocialScienceDataPeer::CRESPONSE_MITIGATION => 11, TsunamiSocialScienceDataPeer::CRESPONSE_PREP => 12, TsunamiSocialScienceDataPeer::CRESPONSE_RECOVERY => 13, TsunamiSocialScienceDataPeer::CRESPONSE_WARNING => 14, TsunamiSocialScienceDataPeer::DAMAGE => 15, TsunamiSocialScienceDataPeer::DAMAGE_COST_EST => 16, TsunamiSocialScienceDataPeer::DAMAGE_INDUSTRY => 17, TsunamiSocialScienceDataPeer::DAMAGE_TYPE => 18, TsunamiSocialScienceDataPeer::IMPACT => 19, TsunamiSocialScienceDataPeer::IMPACT_NUM_DEAD => 20, TsunamiSocialScienceDataPeer::IMPACT_NUM_FAM_SEP => 21, TsunamiSocialScienceDataPeer::IMPACT_NUM_HOMELESS => 22, TsunamiSocialScienceDataPeer::IMPACT_NUM_INJURED => 23, TsunamiSocialScienceDataPeer::IMPACT_NUM_MISSING => 24, TsunamiSocialScienceDataPeer::IRESPONSE => 25, TsunamiSocialScienceDataPeer::IRESPONSE_INTERVW => 26, TsunamiSocialScienceDataPeer::IRESPONSE_MITIGATION => 27, TsunamiSocialScienceDataPeer::IRESPONSE_PREP => 28, TsunamiSocialScienceDataPeer::IRESPONSE_RECOVERY => 29, TsunamiSocialScienceDataPeer::IRESPONSE_WARNINGS => 30, TsunamiSocialScienceDataPeer::ORESPONSE => 31, TsunamiSocialScienceDataPeer::ORESPONSE_DISEASE => 32, TsunamiSocialScienceDataPeer::ORESPONSE_GRELIEF => 33, TsunamiSocialScienceDataPeer::ORESPONSE_INTERVW => 34, TsunamiSocialScienceDataPeer::ORESPONSE_MITIGATION => 35, TsunamiSocialScienceDataPeer::ORESPONSENGORELIEF => 36, TsunamiSocialScienceDataPeer::ORESPONSE_PREP => 37, TsunamiSocialScienceDataPeer::ORESPONSE_RECOVERY => 38, TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID => 39, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_SOCIAL_SCIENCE_DATA_ID' => 0, 'BKG' => 1, 'BKG_CENSUS' => 2, 'BKG_LANGUAGE_ISSUES' => 3, 'BKG_TOURIST_STATS' => 4, 'BKG_TRANSPORT_SYSTEMS' => 5, 'COMM' => 6, 'COMM_INFO_FROMG' => 7, 'COMM_WARN_SYS' => 8, 'CRESPONSE' => 9, 'CRESPONSE_INTERVW' => 10, 'CRESPONSE_MITIGATION' => 11, 'CRESPONSE_PREP' => 12, 'CRESPONSE_RECOVERY' => 13, 'CRESPONSE_WARNING' => 14, 'DAMAGE' => 15, 'DAMAGE_COST_EST' => 16, 'DAMAGE_INDUSTRY' => 17, 'DAMAGE_TYPE' => 18, 'IMPACT' => 19, 'IMPACT_NUM_DEAD' => 20, 'IMPACT_NUM_FAM_SEP' => 21, 'IMPACT_NUM_HOMELESS' => 22, 'IMPACT_NUM_INJURED' => 23, 'IMPACT_NUM_MISSING' => 24, 'IRESPONSE' => 25, 'IRESPONSE_INTERVW' => 26, 'IRESPONSE_MITIGATION' => 27, 'IRESPONSE_PREP' => 28, 'IRESPONSE_RECOVERY' => 29, 'IRESPONSE_WARNINGS' => 30, 'ORESPONSE' => 31, 'ORESPONSE_DISEASE' => 32, 'ORESPONSE_GRELIEF' => 33, 'ORESPONSE_INTERVW' => 34, 'ORESPONSE_MITIGATION' => 35, 'ORESPONSENGORELIEF' => 36, 'ORESPONSE_PREP' => 37, 'ORESPONSE_RECOVERY' => 38, 'TSUNAMI_DOC_LIB_ID' => 39, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/tsunami/map/TsunamiSocialScienceDataMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.tsunami.map.TsunamiSocialScienceDataMapBuilder');
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
			$map = TsunamiSocialScienceDataPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. TsunamiSocialScienceDataPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(TsunamiSocialScienceDataPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::BKG);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::BKG_CENSUS);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::BKG_LANGUAGE_ISSUES);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::BKG_TOURIST_STATS);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::BKG_TRANSPORT_SYSTEMS);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::COMM);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::COMM_INFO_FROMG);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::COMM_WARN_SYS);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::CRESPONSE);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::CRESPONSE_INTERVW);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::CRESPONSE_MITIGATION);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::CRESPONSE_PREP);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::CRESPONSE_RECOVERY);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::CRESPONSE_WARNING);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::DAMAGE);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::DAMAGE_COST_EST);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::DAMAGE_INDUSTRY);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::DAMAGE_TYPE);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IMPACT);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IMPACT_NUM_DEAD);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IMPACT_NUM_FAM_SEP);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IMPACT_NUM_HOMELESS);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IMPACT_NUM_INJURED);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IMPACT_NUM_MISSING);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IRESPONSE);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IRESPONSE_INTERVW);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IRESPONSE_MITIGATION);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IRESPONSE_PREP);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IRESPONSE_RECOVERY);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::IRESPONSE_WARNINGS);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::ORESPONSE);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::ORESPONSE_DISEASE);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::ORESPONSE_GRELIEF);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::ORESPONSE_INTERVW);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::ORESPONSE_MITIGATION);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::ORESPONSENGORELIEF);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::ORESPONSE_PREP);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::ORESPONSE_RECOVERY);

		$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID);

	}

	const COUNT = 'COUNT(TSUNAMI_SOCIAL_SCIENCE_DATA.TSUNAMI_SOCIAL_SCIENCE_DATA_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT TSUNAMI_SOCIAL_SCIENCE_DATA.TSUNAMI_SOCIAL_SCIENCE_DATA_ID)';

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
			$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = TsunamiSocialScienceDataPeer::doSelectRS($criteria, $con);
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
	 * @return     TsunamiSocialScienceData
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = TsunamiSocialScienceDataPeer::doSelect($critcopy, $con);
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
		return TsunamiSocialScienceDataPeer::populateObjects(TsunamiSocialScienceDataPeer::doSelectRS($criteria, $con));
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
			TsunamiSocialScienceDataPeer::addSelectColumns($criteria);
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
		$cls = TsunamiSocialScienceDataPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related TsunamiDocLib table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinTsunamiDocLib(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = TsunamiSocialScienceDataPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiSocialScienceData objects pre-filled with their TsunamiDocLib objects.
	 *
	 * @return     array Array of TsunamiSocialScienceData objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinTsunamiDocLib(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		TsunamiSocialScienceDataPeer::addSelectColumns($c);
		$startcol = (TsunamiSocialScienceDataPeer::NUM_COLUMNS - TsunamiSocialScienceDataPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		TsunamiDocLibPeer::addSelectColumns($c);

		$c->addJoin(TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiSocialScienceDataPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = TsunamiDocLibPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getTsunamiDocLib(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addTsunamiSocialScienceData($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initTsunamiSocialScienceDatas();
				$obj2->addTsunamiSocialScienceData($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining all related tables
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAll(Criteria $criteria, $distinct = false, $con = null)
	{
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiSocialScienceDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = TsunamiSocialScienceDataPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiSocialScienceData objects pre-filled with all related objects.
	 *
	 * @return     array Array of TsunamiSocialScienceData objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAll(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		TsunamiSocialScienceDataPeer::addSelectColumns($c);
		$startcol2 = (TsunamiSocialScienceDataPeer::NUM_COLUMNS - TsunamiSocialScienceDataPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		TsunamiDocLibPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + TsunamiDocLibPeer::NUM_COLUMNS;

		$c->addJoin(TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiSocialScienceDataPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined TsunamiDocLib rows
	
			$omClass = TsunamiDocLibPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getTsunamiDocLib(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addTsunamiSocialScienceData($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initTsunamiSocialScienceDatas();
				$obj2->addTsunamiSocialScienceData($obj1);
			}

			$results[] = $obj1;
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
		return TsunamiSocialScienceDataPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a TsunamiSocialScienceData or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiSocialScienceData object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from TsunamiSocialScienceData object
		}

		$criteria->remove(TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a TsunamiSocialScienceData or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiSocialScienceData object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID);
			$selectCriteria->add(TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID, $criteria->remove(TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID), $comparison);

		} else { // $values is TsunamiSocialScienceData object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the TSUNAMI_SOCIAL_SCIENCE_DATA table.
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
			$affectedRows += BasePeer::doDeleteAll(TsunamiSocialScienceDataPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a TsunamiSocialScienceData or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or TsunamiSocialScienceData object or primary key or array of primary keys
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
			$con = Propel::getConnection(TsunamiSocialScienceDataPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof TsunamiSocialScienceData) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given TsunamiSocialScienceData object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      TsunamiSocialScienceData $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(TsunamiSocialScienceData $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(TsunamiSocialScienceDataPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(TsunamiSocialScienceDataPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::BKG))
			$columns[TsunamiSocialScienceDataPeer::BKG] = $obj->getBkg();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::BKG_CENSUS))
			$columns[TsunamiSocialScienceDataPeer::BKG_CENSUS] = $obj->getBkgCensus();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::BKG_LANGUAGE_ISSUES))
			$columns[TsunamiSocialScienceDataPeer::BKG_LANGUAGE_ISSUES] = $obj->getBkgLanguageIssues();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::BKG_TOURIST_STATS))
			$columns[TsunamiSocialScienceDataPeer::BKG_TOURIST_STATS] = $obj->getBkgTouristStats();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::BKG_TRANSPORT_SYSTEMS))
			$columns[TsunamiSocialScienceDataPeer::BKG_TRANSPORT_SYSTEMS] = $obj->getBkgTransportSystems();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::COMM))
			$columns[TsunamiSocialScienceDataPeer::COMM] = $obj->getComm();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::COMM_INFO_FROMG))
			$columns[TsunamiSocialScienceDataPeer::COMM_INFO_FROMG] = $obj->getCommInfoFromG();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::COMM_WARN_SYS))
			$columns[TsunamiSocialScienceDataPeer::COMM_WARN_SYS] = $obj->getCommWarnSys();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE))
			$columns[TsunamiSocialScienceDataPeer::CRESPONSE] = $obj->getCresponse();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE_INTERVW))
			$columns[TsunamiSocialScienceDataPeer::CRESPONSE_INTERVW] = $obj->getCresponseIntervw();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE_MITIGATION))
			$columns[TsunamiSocialScienceDataPeer::CRESPONSE_MITIGATION] = $obj->getCresponseMitigation();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE_PREP))
			$columns[TsunamiSocialScienceDataPeer::CRESPONSE_PREP] = $obj->getCresponsePrep();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE_RECOVERY))
			$columns[TsunamiSocialScienceDataPeer::CRESPONSE_RECOVERY] = $obj->getCresponseRecovery();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE_WARNING))
			$columns[TsunamiSocialScienceDataPeer::CRESPONSE_WARNING] = $obj->getCresponseWarning();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::DAMAGE))
			$columns[TsunamiSocialScienceDataPeer::DAMAGE] = $obj->getDamage();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::DAMAGE_COST_EST))
			$columns[TsunamiSocialScienceDataPeer::DAMAGE_COST_EST] = $obj->getDamageCostEst();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::DAMAGE_INDUSTRY))
			$columns[TsunamiSocialScienceDataPeer::DAMAGE_INDUSTRY] = $obj->getDamageIndustry();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::DAMAGE_TYPE))
			$columns[TsunamiSocialScienceDataPeer::DAMAGE_TYPE] = $obj->getDamageType();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT))
			$columns[TsunamiSocialScienceDataPeer::IMPACT] = $obj->getImpact();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT_NUM_DEAD))
			$columns[TsunamiSocialScienceDataPeer::IMPACT_NUM_DEAD] = $obj->getImpactNumDead();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT_NUM_FAM_SEP))
			$columns[TsunamiSocialScienceDataPeer::IMPACT_NUM_FAM_SEP] = $obj->getImpactNumFamSep();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT_NUM_HOMELESS))
			$columns[TsunamiSocialScienceDataPeer::IMPACT_NUM_HOMELESS] = $obj->getImpactNumHomeless();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT_NUM_INJURED))
			$columns[TsunamiSocialScienceDataPeer::IMPACT_NUM_INJURED] = $obj->getImpactNumInjured();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT_NUM_MISSING))
			$columns[TsunamiSocialScienceDataPeer::IMPACT_NUM_MISSING] = $obj->getImpactNumMissing();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE))
			$columns[TsunamiSocialScienceDataPeer::IRESPONSE] = $obj->getIresponse();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE_INTERVW))
			$columns[TsunamiSocialScienceDataPeer::IRESPONSE_INTERVW] = $obj->getIresponseIntervw();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE_MITIGATION))
			$columns[TsunamiSocialScienceDataPeer::IRESPONSE_MITIGATION] = $obj->getIresponseMitigation();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE_PREP))
			$columns[TsunamiSocialScienceDataPeer::IRESPONSE_PREP] = $obj->getIresponsePrep();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE_RECOVERY))
			$columns[TsunamiSocialScienceDataPeer::IRESPONSE_RECOVERY] = $obj->getIresponseRecovery();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE_WARNINGS))
			$columns[TsunamiSocialScienceDataPeer::IRESPONSE_WARNINGS] = $obj->getIresponseWarnings();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE))
			$columns[TsunamiSocialScienceDataPeer::ORESPONSE] = $obj->getOresponse();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSENGORELIEF))
			$columns[TsunamiSocialScienceDataPeer::ORESPONSENGORELIEF] = $obj->getOresponseNGORelief();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_DISEASE))
			$columns[TsunamiSocialScienceDataPeer::ORESPONSE_DISEASE] = $obj->getOresponseDisease();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_GRELIEF))
			$columns[TsunamiSocialScienceDataPeer::ORESPONSE_GRELIEF] = $obj->getOresponseGrelief();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_INTERVW))
			$columns[TsunamiSocialScienceDataPeer::ORESPONSE_INTERVW] = $obj->getOresponseIntervw();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_MITIGATION))
			$columns[TsunamiSocialScienceDataPeer::ORESPONSE_MITIGATION] = $obj->getOresponseMitigation();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_PREP))
			$columns[TsunamiSocialScienceDataPeer::ORESPONSE_PREP] = $obj->getOresponsePrep();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_RECOVERY))
			$columns[TsunamiSocialScienceDataPeer::ORESPONSE_RECOVERY] = $obj->getOresponseRecovery();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID))
			$columns[TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID] = $obj->getTsunamiDocLibId();

		}

		return BasePeer::doValidate(TsunamiSocialScienceDataPeer::DATABASE_NAME, TsunamiSocialScienceDataPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     TsunamiSocialScienceData
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(TsunamiSocialScienceDataPeer::DATABASE_NAME);

		$criteria->add(TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID, $pk);


		$v = TsunamiSocialScienceDataPeer::doSelect($criteria, $con);

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
			$criteria->add(TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID, $pks, Criteria::IN);
			$objs = TsunamiSocialScienceDataPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseTsunamiSocialScienceDataPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseTsunamiSocialScienceDataPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/tsunami/map/TsunamiSocialScienceDataMapBuilder.php';
	Propel::registerMapBuilder('lib.data.tsunami.map.TsunamiSocialScienceDataMapBuilder');
}
