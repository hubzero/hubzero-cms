<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by TsunamiHydrodynamicDataPeer::getOMClass()
include_once 'lib/data/tsunami/TsunamiHydrodynamicData.php';

/**
 * Base static class for performing query and update operations on the 'TSUNAMI_HYDRODYNAMIC_DATA' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiHydrodynamicDataPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'TSUNAMI_HYDRODYNAMIC_DATA';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.tsunami.TsunamiHydrodynamicData';

	/** The total number of columns. */
	const NUM_COLUMNS = 34;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the TSUNAMI_HYDRODYNAMIC_DATA_ID field */
	const TSUNAMI_HYDRODYNAMIC_DATA_ID = 'TSUNAMI_HYDRODYNAMIC_DATA.TSUNAMI_HYDRODYNAMIC_DATA_ID';

	/** the column name for the CONDITION_SEA field */
	const CONDITION_SEA = 'TSUNAMI_HYDRODYNAMIC_DATA.CONDITION_SEA';

	/** the column name for the CONDITION_SOURCE field */
	const CONDITION_SOURCE = 'TSUNAMI_HYDRODYNAMIC_DATA.CONDITION_SOURCE';

	/** the column name for the CONDITION_WEATHER field */
	const CONDITION_WEATHER = 'TSUNAMI_HYDRODYNAMIC_DATA.CONDITION_WEATHER';

	/** the column name for the CONDITION_WIND field */
	const CONDITION_WIND = 'TSUNAMI_HYDRODYNAMIC_DATA.CONDITION_WIND';

	/** the column name for the ECONDITION field */
	const ECONDITION = 'TSUNAMI_HYDRODYNAMIC_DATA.ECONDITION';

	/** the column name for the FLOW field */
	const FLOW = 'TSUNAMI_HYDRODYNAMIC_DATA.FLOW';

	/** the column name for the FLOW_DIRECTION field */
	const FLOW_DIRECTION = 'TSUNAMI_HYDRODYNAMIC_DATA.FLOW_DIRECTION';

	/** the column name for the FLOW_SOURCE field */
	const FLOW_SOURCE = 'TSUNAMI_HYDRODYNAMIC_DATA.FLOW_SOURCE';

	/** the column name for the FLOW_SPEED field */
	const FLOW_SPEED = 'TSUNAMI_HYDRODYNAMIC_DATA.FLOW_SPEED';

	/** the column name for the INUNDATION field */
	const INUNDATION = 'TSUNAMI_HYDRODYNAMIC_DATA.INUNDATION';

	/** the column name for the INUNDATION_DIST field */
	const INUNDATION_DIST = 'TSUNAMI_HYDRODYNAMIC_DATA.INUNDATION_DIST';

	/** the column name for the INUNDATION_QUALITY field */
	const INUNDATION_QUALITY = 'TSUNAMI_HYDRODYNAMIC_DATA.INUNDATION_QUALITY';

	/** the column name for the INUNDATION_SOURCE field */
	const INUNDATION_SOURCE = 'TSUNAMI_HYDRODYNAMIC_DATA.INUNDATION_SOURCE';

	/** the column name for the RUNUP field */
	const RUNUP = 'TSUNAMI_HYDRODYNAMIC_DATA.RUNUP';

	/** the column name for the RUNUP_ADJ_METHOD field */
	const RUNUP_ADJ_METHOD = 'TSUNAMI_HYDRODYNAMIC_DATA.RUNUP_ADJ_METHOD';

	/** the column name for the RUNUP_HEIGHT field */
	const RUNUP_HEIGHT = 'TSUNAMI_HYDRODYNAMIC_DATA.RUNUP_HEIGHT';

	/** the column name for the RUNUP_PORHEIGHT field */
	const RUNUP_PORHEIGHT = 'TSUNAMI_HYDRODYNAMIC_DATA.RUNUP_PORHEIGHT';

	/** the column name for the RUNUP_PORLOC field */
	const RUNUP_PORLOC = 'TSUNAMI_HYDRODYNAMIC_DATA.RUNUP_PORLOC';

	/** the column name for the RUNUP_QUALITY field */
	const RUNUP_QUALITY = 'TSUNAMI_HYDRODYNAMIC_DATA.RUNUP_QUALITY';

	/** the column name for the RUNUP_SOURCE field */
	const RUNUP_SOURCE = 'TSUNAMI_HYDRODYNAMIC_DATA.RUNUP_SOURCE';

	/** the column name for the RUNUP_TIDAL_ADJ field */
	const RUNUP_TIDAL_ADJ = 'TSUNAMI_HYDRODYNAMIC_DATA.RUNUP_TIDAL_ADJ';

	/** the column name for the TIDEGAUGE field */
	const TIDEGAUGE = 'TSUNAMI_HYDRODYNAMIC_DATA.TIDEGAUGE';

	/** the column name for the TIDEGAUGE_SOURCE field */
	const TIDEGAUGE_SOURCE = 'TSUNAMI_HYDRODYNAMIC_DATA.TIDEGAUGE_SOURCE';

	/** the column name for the TIDEGAUGE_TYPE field */
	const TIDEGAUGE_TYPE = 'TSUNAMI_HYDRODYNAMIC_DATA.TIDEGAUGE_TYPE';

	/** the column name for the TSUNAMI_DOC_LIB_ID field */
	const TSUNAMI_DOC_LIB_ID = 'TSUNAMI_HYDRODYNAMIC_DATA.TSUNAMI_DOC_LIB_ID';

	/** the column name for the WAVE field */
	const WAVE = 'TSUNAMI_HYDRODYNAMIC_DATA.WAVE';

	/** the column name for the WAVE_ARRIVAL_TIMES field */
	const WAVE_ARRIVAL_TIMES = 'TSUNAMI_HYDRODYNAMIC_DATA.WAVE_ARRIVAL_TIMES';

	/** the column name for the WAVE_FORM field */
	const WAVE_FORM = 'TSUNAMI_HYDRODYNAMIC_DATA.WAVE_FORM';

	/** the column name for the WAVE_HEIGHT field */
	const WAVE_HEIGHT = 'TSUNAMI_HYDRODYNAMIC_DATA.WAVE_HEIGHT';

	/** the column name for the WAVE_NUMBER field */
	const WAVE_NUMBER = 'TSUNAMI_HYDRODYNAMIC_DATA.WAVE_NUMBER';

	/** the column name for the WAVE_PERIOD field */
	const WAVE_PERIOD = 'TSUNAMI_HYDRODYNAMIC_DATA.WAVE_PERIOD';

	/** the column name for the WAVE_SOURCE field */
	const WAVE_SOURCE = 'TSUNAMI_HYDRODYNAMIC_DATA.WAVE_SOURCE';

	/** the column name for the WAVE_TIME_TO_NORM field */
	const WAVE_TIME_TO_NORM = 'TSUNAMI_HYDRODYNAMIC_DATA.WAVE_TIME_TO_NORM';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'ConditionSea', 'ConditionSource', 'ConditionWeather', 'ConditionWind', 'Econdition', 'Flow', 'FlowDirection', 'FlowSource', 'FlowSpeed', 'Inundation', 'InundationDist', 'InundationQuality', 'InundationSource', 'Runup', 'RunupAdjMethod', 'RunupHeight', 'RunupPoRHeight', 'RunupPoRLoc', 'RunupQuality', 'RunupSource', 'RunupTidalAdj', 'Tidegauge', 'TidegaugeSource', 'TidegaugeType', 'TsunamiDocLibId', 'Wave', 'WaveArrivalTimes', 'WaveForm', 'WaveHeight', 'WaveNumber', 'WavePeriod', 'WaveSource', 'WaveTimeToNorm', ),
		BasePeer::TYPE_COLNAME => array (TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID, TsunamiHydrodynamicDataPeer::CONDITION_SEA, TsunamiHydrodynamicDataPeer::CONDITION_SOURCE, TsunamiHydrodynamicDataPeer::CONDITION_WEATHER, TsunamiHydrodynamicDataPeer::CONDITION_WIND, TsunamiHydrodynamicDataPeer::ECONDITION, TsunamiHydrodynamicDataPeer::FLOW, TsunamiHydrodynamicDataPeer::FLOW_DIRECTION, TsunamiHydrodynamicDataPeer::FLOW_SOURCE, TsunamiHydrodynamicDataPeer::FLOW_SPEED, TsunamiHydrodynamicDataPeer::INUNDATION, TsunamiHydrodynamicDataPeer::INUNDATION_DIST, TsunamiHydrodynamicDataPeer::INUNDATION_QUALITY, TsunamiHydrodynamicDataPeer::INUNDATION_SOURCE, TsunamiHydrodynamicDataPeer::RUNUP, TsunamiHydrodynamicDataPeer::RUNUP_ADJ_METHOD, TsunamiHydrodynamicDataPeer::RUNUP_HEIGHT, TsunamiHydrodynamicDataPeer::RUNUP_PORHEIGHT, TsunamiHydrodynamicDataPeer::RUNUP_PORLOC, TsunamiHydrodynamicDataPeer::RUNUP_QUALITY, TsunamiHydrodynamicDataPeer::RUNUP_SOURCE, TsunamiHydrodynamicDataPeer::RUNUP_TIDAL_ADJ, TsunamiHydrodynamicDataPeer::TIDEGAUGE, TsunamiHydrodynamicDataPeer::TIDEGAUGE_SOURCE, TsunamiHydrodynamicDataPeer::TIDEGAUGE_TYPE, TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiHydrodynamicDataPeer::WAVE, TsunamiHydrodynamicDataPeer::WAVE_ARRIVAL_TIMES, TsunamiHydrodynamicDataPeer::WAVE_FORM, TsunamiHydrodynamicDataPeer::WAVE_HEIGHT, TsunamiHydrodynamicDataPeer::WAVE_NUMBER, TsunamiHydrodynamicDataPeer::WAVE_PERIOD, TsunamiHydrodynamicDataPeer::WAVE_SOURCE, TsunamiHydrodynamicDataPeer::WAVE_TIME_TO_NORM, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_HYDRODYNAMIC_DATA_ID', 'CONDITION_SEA', 'CONDITION_SOURCE', 'CONDITION_WEATHER', 'CONDITION_WIND', 'ECONDITION', 'FLOW', 'FLOW_DIRECTION', 'FLOW_SOURCE', 'FLOW_SPEED', 'INUNDATION', 'INUNDATION_DIST', 'INUNDATION_QUALITY', 'INUNDATION_SOURCE', 'RUNUP', 'RUNUP_ADJ_METHOD', 'RUNUP_HEIGHT', 'RUNUP_PORHEIGHT', 'RUNUP_PORLOC', 'RUNUP_QUALITY', 'RUNUP_SOURCE', 'RUNUP_TIDAL_ADJ', 'TIDEGAUGE', 'TIDEGAUGE_SOURCE', 'TIDEGAUGE_TYPE', 'TSUNAMI_DOC_LIB_ID', 'WAVE', 'WAVE_ARRIVAL_TIMES', 'WAVE_FORM', 'WAVE_HEIGHT', 'WAVE_NUMBER', 'WAVE_PERIOD', 'WAVE_SOURCE', 'WAVE_TIME_TO_NORM', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'ConditionSea' => 1, 'ConditionSource' => 2, 'ConditionWeather' => 3, 'ConditionWind' => 4, 'Econdition' => 5, 'Flow' => 6, 'FlowDirection' => 7, 'FlowSource' => 8, 'FlowSpeed' => 9, 'Inundation' => 10, 'InundationDist' => 11, 'InundationQuality' => 12, 'InundationSource' => 13, 'Runup' => 14, 'RunupAdjMethod' => 15, 'RunupHeight' => 16, 'RunupPoRHeight' => 17, 'RunupPoRLoc' => 18, 'RunupQuality' => 19, 'RunupSource' => 20, 'RunupTidalAdj' => 21, 'Tidegauge' => 22, 'TidegaugeSource' => 23, 'TidegaugeType' => 24, 'TsunamiDocLibId' => 25, 'Wave' => 26, 'WaveArrivalTimes' => 27, 'WaveForm' => 28, 'WaveHeight' => 29, 'WaveNumber' => 30, 'WavePeriod' => 31, 'WaveSource' => 32, 'WaveTimeToNorm' => 33, ),
		BasePeer::TYPE_COLNAME => array (TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID => 0, TsunamiHydrodynamicDataPeer::CONDITION_SEA => 1, TsunamiHydrodynamicDataPeer::CONDITION_SOURCE => 2, TsunamiHydrodynamicDataPeer::CONDITION_WEATHER => 3, TsunamiHydrodynamicDataPeer::CONDITION_WIND => 4, TsunamiHydrodynamicDataPeer::ECONDITION => 5, TsunamiHydrodynamicDataPeer::FLOW => 6, TsunamiHydrodynamicDataPeer::FLOW_DIRECTION => 7, TsunamiHydrodynamicDataPeer::FLOW_SOURCE => 8, TsunamiHydrodynamicDataPeer::FLOW_SPEED => 9, TsunamiHydrodynamicDataPeer::INUNDATION => 10, TsunamiHydrodynamicDataPeer::INUNDATION_DIST => 11, TsunamiHydrodynamicDataPeer::INUNDATION_QUALITY => 12, TsunamiHydrodynamicDataPeer::INUNDATION_SOURCE => 13, TsunamiHydrodynamicDataPeer::RUNUP => 14, TsunamiHydrodynamicDataPeer::RUNUP_ADJ_METHOD => 15, TsunamiHydrodynamicDataPeer::RUNUP_HEIGHT => 16, TsunamiHydrodynamicDataPeer::RUNUP_PORHEIGHT => 17, TsunamiHydrodynamicDataPeer::RUNUP_PORLOC => 18, TsunamiHydrodynamicDataPeer::RUNUP_QUALITY => 19, TsunamiHydrodynamicDataPeer::RUNUP_SOURCE => 20, TsunamiHydrodynamicDataPeer::RUNUP_TIDAL_ADJ => 21, TsunamiHydrodynamicDataPeer::TIDEGAUGE => 22, TsunamiHydrodynamicDataPeer::TIDEGAUGE_SOURCE => 23, TsunamiHydrodynamicDataPeer::TIDEGAUGE_TYPE => 24, TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID => 25, TsunamiHydrodynamicDataPeer::WAVE => 26, TsunamiHydrodynamicDataPeer::WAVE_ARRIVAL_TIMES => 27, TsunamiHydrodynamicDataPeer::WAVE_FORM => 28, TsunamiHydrodynamicDataPeer::WAVE_HEIGHT => 29, TsunamiHydrodynamicDataPeer::WAVE_NUMBER => 30, TsunamiHydrodynamicDataPeer::WAVE_PERIOD => 31, TsunamiHydrodynamicDataPeer::WAVE_SOURCE => 32, TsunamiHydrodynamicDataPeer::WAVE_TIME_TO_NORM => 33, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_HYDRODYNAMIC_DATA_ID' => 0, 'CONDITION_SEA' => 1, 'CONDITION_SOURCE' => 2, 'CONDITION_WEATHER' => 3, 'CONDITION_WIND' => 4, 'ECONDITION' => 5, 'FLOW' => 6, 'FLOW_DIRECTION' => 7, 'FLOW_SOURCE' => 8, 'FLOW_SPEED' => 9, 'INUNDATION' => 10, 'INUNDATION_DIST' => 11, 'INUNDATION_QUALITY' => 12, 'INUNDATION_SOURCE' => 13, 'RUNUP' => 14, 'RUNUP_ADJ_METHOD' => 15, 'RUNUP_HEIGHT' => 16, 'RUNUP_PORHEIGHT' => 17, 'RUNUP_PORLOC' => 18, 'RUNUP_QUALITY' => 19, 'RUNUP_SOURCE' => 20, 'RUNUP_TIDAL_ADJ' => 21, 'TIDEGAUGE' => 22, 'TIDEGAUGE_SOURCE' => 23, 'TIDEGAUGE_TYPE' => 24, 'TSUNAMI_DOC_LIB_ID' => 25, 'WAVE' => 26, 'WAVE_ARRIVAL_TIMES' => 27, 'WAVE_FORM' => 28, 'WAVE_HEIGHT' => 29, 'WAVE_NUMBER' => 30, 'WAVE_PERIOD' => 31, 'WAVE_SOURCE' => 32, 'WAVE_TIME_TO_NORM' => 33, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/tsunami/map/TsunamiHydrodynamicDataMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.tsunami.map.TsunamiHydrodynamicDataMapBuilder');
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
			$map = TsunamiHydrodynamicDataPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. TsunamiHydrodynamicDataPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(TsunamiHydrodynamicDataPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::CONDITION_SEA);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::CONDITION_SOURCE);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::CONDITION_WEATHER);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::CONDITION_WIND);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::ECONDITION);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::FLOW);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::FLOW_DIRECTION);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::FLOW_SOURCE);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::FLOW_SPEED);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::INUNDATION);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::INUNDATION_DIST);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::INUNDATION_QUALITY);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::INUNDATION_SOURCE);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::RUNUP);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::RUNUP_ADJ_METHOD);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::RUNUP_HEIGHT);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::RUNUP_PORHEIGHT);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::RUNUP_PORLOC);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::RUNUP_QUALITY);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::RUNUP_SOURCE);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::RUNUP_TIDAL_ADJ);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::TIDEGAUGE);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::TIDEGAUGE_SOURCE);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::TIDEGAUGE_TYPE);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::WAVE);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::WAVE_ARRIVAL_TIMES);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::WAVE_FORM);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::WAVE_HEIGHT);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::WAVE_NUMBER);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::WAVE_PERIOD);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::WAVE_SOURCE);

		$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::WAVE_TIME_TO_NORM);

	}

	const COUNT = 'COUNT(TSUNAMI_HYDRODYNAMIC_DATA.TSUNAMI_HYDRODYNAMIC_DATA_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT TSUNAMI_HYDRODYNAMIC_DATA.TSUNAMI_HYDRODYNAMIC_DATA_ID)';

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
			$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = TsunamiHydrodynamicDataPeer::doSelectRS($criteria, $con);
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
	 * @return     TsunamiHydrodynamicData
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = TsunamiHydrodynamicDataPeer::doSelect($critcopy, $con);
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
		return TsunamiHydrodynamicDataPeer::populateObjects(TsunamiHydrodynamicDataPeer::doSelectRS($criteria, $con));
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
			TsunamiHydrodynamicDataPeer::addSelectColumns($criteria);
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
		$cls = TsunamiHydrodynamicDataPeer::getOMClass();
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
			$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = TsunamiHydrodynamicDataPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiHydrodynamicData objects pre-filled with their TsunamiDocLib objects.
	 *
	 * @return     array Array of TsunamiHydrodynamicData objects.
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

		TsunamiHydrodynamicDataPeer::addSelectColumns($c);
		$startcol = (TsunamiHydrodynamicDataPeer::NUM_COLUMNS - TsunamiHydrodynamicDataPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		TsunamiDocLibPeer::addSelectColumns($c);

		$c->addJoin(TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiHydrodynamicDataPeer::getOMClass();

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
					$temp_obj2->addTsunamiHydrodynamicData($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initTsunamiHydrodynamicDatas();
				$obj2->addTsunamiHydrodynamicData($obj1); //CHECKME
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
			$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiHydrodynamicDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = TsunamiHydrodynamicDataPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiHydrodynamicData objects pre-filled with all related objects.
	 *
	 * @return     array Array of TsunamiHydrodynamicData objects.
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

		TsunamiHydrodynamicDataPeer::addSelectColumns($c);
		$startcol2 = (TsunamiHydrodynamicDataPeer::NUM_COLUMNS - TsunamiHydrodynamicDataPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		TsunamiDocLibPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + TsunamiDocLibPeer::NUM_COLUMNS;

		$c->addJoin(TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiHydrodynamicDataPeer::getOMClass();


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
					$temp_obj2->addTsunamiHydrodynamicData($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initTsunamiHydrodynamicDatas();
				$obj2->addTsunamiHydrodynamicData($obj1);
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
		return TsunamiHydrodynamicDataPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a TsunamiHydrodynamicData or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiHydrodynamicData object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from TsunamiHydrodynamicData object
		}

		$criteria->remove(TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a TsunamiHydrodynamicData or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiHydrodynamicData object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID);
			$selectCriteria->add(TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID, $criteria->remove(TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID), $comparison);

		} else { // $values is TsunamiHydrodynamicData object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the TSUNAMI_HYDRODYNAMIC_DATA table.
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
			$affectedRows += BasePeer::doDeleteAll(TsunamiHydrodynamicDataPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a TsunamiHydrodynamicData or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or TsunamiHydrodynamicData object or primary key or array of primary keys
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
			$con = Propel::getConnection(TsunamiHydrodynamicDataPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof TsunamiHydrodynamicData) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given TsunamiHydrodynamicData object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      TsunamiHydrodynamicData $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(TsunamiHydrodynamicData $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(TsunamiHydrodynamicDataPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(TsunamiHydrodynamicDataPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::CONDITION_SEA))
			$columns[TsunamiHydrodynamicDataPeer::CONDITION_SEA] = $obj->getConditionSea();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::CONDITION_SOURCE))
			$columns[TsunamiHydrodynamicDataPeer::CONDITION_SOURCE] = $obj->getConditionSource();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::CONDITION_WEATHER))
			$columns[TsunamiHydrodynamicDataPeer::CONDITION_WEATHER] = $obj->getConditionWeather();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::CONDITION_WIND))
			$columns[TsunamiHydrodynamicDataPeer::CONDITION_WIND] = $obj->getConditionWind();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::ECONDITION))
			$columns[TsunamiHydrodynamicDataPeer::ECONDITION] = $obj->getEcondition();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::FLOW))
			$columns[TsunamiHydrodynamicDataPeer::FLOW] = $obj->getFlow();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::FLOW_DIRECTION))
			$columns[TsunamiHydrodynamicDataPeer::FLOW_DIRECTION] = $obj->getFlowDirection();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::FLOW_SOURCE))
			$columns[TsunamiHydrodynamicDataPeer::FLOW_SOURCE] = $obj->getFlowSource();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::FLOW_SPEED))
			$columns[TsunamiHydrodynamicDataPeer::FLOW_SPEED] = $obj->getFlowSpeed();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::INUNDATION))
			$columns[TsunamiHydrodynamicDataPeer::INUNDATION] = $obj->getInundation();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::INUNDATION_DIST))
			$columns[TsunamiHydrodynamicDataPeer::INUNDATION_DIST] = $obj->getInundationDist();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::INUNDATION_QUALITY))
			$columns[TsunamiHydrodynamicDataPeer::INUNDATION_QUALITY] = $obj->getInundationQuality();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::INUNDATION_SOURCE))
			$columns[TsunamiHydrodynamicDataPeer::INUNDATION_SOURCE] = $obj->getInundationSource();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP))
			$columns[TsunamiHydrodynamicDataPeer::RUNUP] = $obj->getRunup();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_ADJ_METHOD))
			$columns[TsunamiHydrodynamicDataPeer::RUNUP_ADJ_METHOD] = $obj->getRunupAdjMethod();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_HEIGHT))
			$columns[TsunamiHydrodynamicDataPeer::RUNUP_HEIGHT] = $obj->getRunupHeight();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_PORHEIGHT))
			$columns[TsunamiHydrodynamicDataPeer::RUNUP_PORHEIGHT] = $obj->getRunupPoRHeight();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_PORLOC))
			$columns[TsunamiHydrodynamicDataPeer::RUNUP_PORLOC] = $obj->getRunupPoRLoc();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_QUALITY))
			$columns[TsunamiHydrodynamicDataPeer::RUNUP_QUALITY] = $obj->getRunupQuality();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_SOURCE))
			$columns[TsunamiHydrodynamicDataPeer::RUNUP_SOURCE] = $obj->getRunupSource();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_TIDAL_ADJ))
			$columns[TsunamiHydrodynamicDataPeer::RUNUP_TIDAL_ADJ] = $obj->getRunupTidalAdj();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::TIDEGAUGE))
			$columns[TsunamiHydrodynamicDataPeer::TIDEGAUGE] = $obj->getTidegauge();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::TIDEGAUGE_SOURCE))
			$columns[TsunamiHydrodynamicDataPeer::TIDEGAUGE_SOURCE] = $obj->getTidegaugeSource();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::TIDEGAUGE_TYPE))
			$columns[TsunamiHydrodynamicDataPeer::TIDEGAUGE_TYPE] = $obj->getTidegaugeType();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID))
			$columns[TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID] = $obj->getTsunamiDocLibId();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE))
			$columns[TsunamiHydrodynamicDataPeer::WAVE] = $obj->getWave();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_ARRIVAL_TIMES))
			$columns[TsunamiHydrodynamicDataPeer::WAVE_ARRIVAL_TIMES] = $obj->getWaveArrivalTimes();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_FORM))
			$columns[TsunamiHydrodynamicDataPeer::WAVE_FORM] = $obj->getWaveForm();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_HEIGHT))
			$columns[TsunamiHydrodynamicDataPeer::WAVE_HEIGHT] = $obj->getWaveHeight();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_NUMBER))
			$columns[TsunamiHydrodynamicDataPeer::WAVE_NUMBER] = $obj->getWaveNumber();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_PERIOD))
			$columns[TsunamiHydrodynamicDataPeer::WAVE_PERIOD] = $obj->getWavePeriod();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_SOURCE))
			$columns[TsunamiHydrodynamicDataPeer::WAVE_SOURCE] = $obj->getWaveSource();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_TIME_TO_NORM))
			$columns[TsunamiHydrodynamicDataPeer::WAVE_TIME_TO_NORM] = $obj->getWaveTimeToNorm();

		}

		return BasePeer::doValidate(TsunamiHydrodynamicDataPeer::DATABASE_NAME, TsunamiHydrodynamicDataPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     TsunamiHydrodynamicData
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(TsunamiHydrodynamicDataPeer::DATABASE_NAME);

		$criteria->add(TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID, $pk);


		$v = TsunamiHydrodynamicDataPeer::doSelect($criteria, $con);

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
			$criteria->add(TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID, $pks, Criteria::IN);
			$objs = TsunamiHydrodynamicDataPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseTsunamiHydrodynamicDataPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseTsunamiHydrodynamicDataPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/tsunami/map/TsunamiHydrodynamicDataMapBuilder.php';
	Propel::registerMapBuilder('lib.data.tsunami.map.TsunamiHydrodynamicDataMapBuilder');
}
