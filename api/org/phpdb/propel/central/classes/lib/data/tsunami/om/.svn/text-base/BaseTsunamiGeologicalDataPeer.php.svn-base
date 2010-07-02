<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by TsunamiGeologicalDataPeer::getOMClass()
include_once 'lib/data/tsunami/TsunamiGeologicalData.php';

/**
 * Base static class for performing query and update operations on the 'TSUNAMI_GEOLOGICAL_DATA' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiGeologicalDataPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'TSUNAMI_GEOLOGICAL_DATA';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.tsunami.TsunamiGeologicalData';

	/** The total number of columns. */
	const NUM_COLUMNS = 38;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the TSUNAMI_GEOLOGICAL_DATA_ID field */
	const TSUNAMI_GEOLOGICAL_DATA_ID = 'TSUNAMI_GEOLOGICAL_DATA.TSUNAMI_GEOLOGICAL_DATA_ID';

	/** the column name for the DISPLACEMENT field */
	const DISPLACEMENT = 'TSUNAMI_GEOLOGICAL_DATA.DISPLACEMENT';

	/** the column name for the DISPLACEMENT_SUBSIDENCE field */
	const DISPLACEMENT_SUBSIDENCE = 'TSUNAMI_GEOLOGICAL_DATA.DISPLACEMENT_SUBSIDENCE';

	/** the column name for the DISPLACEMENT_UPLIFT field */
	const DISPLACEMENT_UPLIFT = 'TSUNAMI_GEOLOGICAL_DATA.DISPLACEMENT_UPLIFT';

	/** the column name for the EIL field */
	const EIL = 'TSUNAMI_GEOLOGICAL_DATA.EIL';

	/** the column name for the EIL_CHARACTERISTICS field */
	const EIL_CHARACTERISTICS = 'TSUNAMI_GEOLOGICAL_DATA.EIL_CHARACTERISTICS';

	/** the column name for the EIL_DIST_INLAND field */
	const EIL_DIST_INLAND = 'TSUNAMI_GEOLOGICAL_DATA.EIL_DIST_INLAND';

	/** the column name for the EIL_ELEVATION field */
	const EIL_ELEVATION = 'TSUNAMI_GEOLOGICAL_DATA.EIL_ELEVATION';

	/** the column name for the FAULT field */
	const FAULT = 'TSUNAMI_GEOLOGICAL_DATA.FAULT';

	/** the column name for the FAULT_GEOMORPHIC field */
	const FAULT_GEOMORPHIC = 'TSUNAMI_GEOLOGICAL_DATA.FAULT_GEOMORPHIC';

	/** the column name for the FAULT_OFFSET field */
	const FAULT_OFFSET = 'TSUNAMI_GEOLOGICAL_DATA.FAULT_OFFSET';

	/** the column name for the FAULT_PALEO field */
	const FAULT_PALEO = 'TSUNAMI_GEOLOGICAL_DATA.FAULT_PALEO';

	/** the column name for the FAULT_STRIKE_MEASURE field */
	const FAULT_STRIKE_MEASURE = 'TSUNAMI_GEOLOGICAL_DATA.FAULT_STRIKE_MEASURE';

	/** the column name for the FAULT_TYPE field */
	const FAULT_TYPE = 'TSUNAMI_GEOLOGICAL_DATA.FAULT_TYPE';

	/** the column name for the GMCHANGES field */
	const GMCHANGES = 'TSUNAMI_GEOLOGICAL_DATA.GMCHANGES';

	/** the column name for the GMCHANGES_BED_MOD field */
	const GMCHANGES_BED_MOD = 'TSUNAMI_GEOLOGICAL_DATA.GMCHANGES_BED_MOD';

	/** the column name for the GMCHANGES_DEPOSIT field */
	const GMCHANGES_DEPOSIT = 'TSUNAMI_GEOLOGICAL_DATA.GMCHANGES_DEPOSIT';

	/** the column name for the GMCHANGES_SCOUR field */
	const GMCHANGES_SCOUR = 'TSUNAMI_GEOLOGICAL_DATA.GMCHANGES_SCOUR';

	/** the column name for the PALEO field */
	const PALEO = 'TSUNAMI_GEOLOGICAL_DATA.PALEO';

	/** the column name for the PALEO_CHARACTERISTICS field */
	const PALEO_CHARACTERISTICS = 'TSUNAMI_GEOLOGICAL_DATA.PALEO_CHARACTERISTICS';

	/** the column name for the PALEO_CORE_SAMPLES field */
	const PALEO_CORE_SAMPLES = 'TSUNAMI_GEOLOGICAL_DATA.PALEO_CORE_SAMPLES';

	/** the column name for the PALEO_DIST_INLAND field */
	const PALEO_DIST_INLAND = 'TSUNAMI_GEOLOGICAL_DATA.PALEO_DIST_INLAND';

	/** the column name for the PALEO_ELEVATION field */
	const PALEO_ELEVATION = 'TSUNAMI_GEOLOGICAL_DATA.PALEO_ELEVATION';

	/** the column name for the PALEO_OUTCROPS field */
	const PALEO_OUTCROPS = 'TSUNAMI_GEOLOGICAL_DATA.PALEO_OUTCROPS';

	/** the column name for the PALEO_SCALE field */
	const PALEO_SCALE = 'TSUNAMI_GEOLOGICAL_DATA.PALEO_SCALE';

	/** the column name for the PALEO_SED_PEELS field */
	const PALEO_SED_PEELS = 'TSUNAMI_GEOLOGICAL_DATA.PALEO_SED_PEELS';

	/** the column name for the PALEO_SPATIAL_VAR field */
	const PALEO_SPATIAL_VAR = 'TSUNAMI_GEOLOGICAL_DATA.PALEO_SPATIAL_VAR';

	/** the column name for the SMSL field */
	const SMSL = 'TSUNAMI_GEOLOGICAL_DATA.SMSL';

	/** the column name for the SSL_COEFFICIENT_OF_FRICTION field */
	const SSL_COEFFICIENT_OF_FRICTION = 'TSUNAMI_GEOLOGICAL_DATA.SSL_COEFFICIENT_OF_FRICTION';

	/** the column name for the SSL_DEPOSITS field */
	const SSL_DEPOSITS = 'TSUNAMI_GEOLOGICAL_DATA.SSL_DEPOSITS';

	/** the column name for the SSL_SCARS field */
	const SSL_SCARS = 'TSUNAMI_GEOLOGICAL_DATA.SSL_SCARS';

	/** the column name for the TDCBM field */
	const TDCBM = 'TSUNAMI_GEOLOGICAL_DATA.TDCBM';

	/** the column name for the TDCBM_CHARACTERISTICS field */
	const TDCBM_CHARACTERISTICS = 'TSUNAMI_GEOLOGICAL_DATA.TDCBM_CHARACTERISTICS';

	/** the column name for the TDCBM_DIST_INLAND field */
	const TDCBM_DIST_INLAND = 'TSUNAMI_GEOLOGICAL_DATA.TDCBM_DIST_INLAND';

	/** the column name for the TDCBM_ELEVATION field */
	const TDCBM_ELEVATION = 'TSUNAMI_GEOLOGICAL_DATA.TDCBM_ELEVATION';

	/** the column name for the TDCBM_SCALE field */
	const TDCBM_SCALE = 'TSUNAMI_GEOLOGICAL_DATA.TDCBM_SCALE';

	/** the column name for the TDCBM_SPATIAL_VAR field */
	const TDCBM_SPATIAL_VAR = 'TSUNAMI_GEOLOGICAL_DATA.TDCBM_SPATIAL_VAR';

	/** the column name for the TSUNAMI_DOC_LIB_ID field */
	const TSUNAMI_DOC_LIB_ID = 'TSUNAMI_GEOLOGICAL_DATA.TSUNAMI_DOC_LIB_ID';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Displacement', 'DisplacementSubsidence', 'DisplacementUplift', 'Eil', 'EilCharacteristics', 'EilDistInland', 'EilElevation', 'Fault', 'FaultGeomorphic', 'FaultOffset', 'FaultPaleo', 'FaultStrikeMeasure', 'FaultType', 'Gmchanges', 'GmchangesBedMod', 'GmchangesDeposit', 'GmchangesScour', 'Paleo', 'PaleoCharacteristics', 'PaleoCoreSamples', 'PaleoDistInland', 'PaleoElevation', 'PaleoOutcrops', 'PaleoScale', 'PaleoSedPeels', 'PaleoSpatialVar', 'Smsl', 'SslCoefficientOfFriction', 'SslDeposits', 'SslScars', 'Tdcbm', 'TdcbmCharacteristics', 'TdcbmDistInland', 'TdcbmElevation', 'TdcbmScale', 'TdcbmSpatialVar', 'TsunamiDocLibId', ),
		BasePeer::TYPE_COLNAME => array (TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID, TsunamiGeologicalDataPeer::DISPLACEMENT, TsunamiGeologicalDataPeer::DISPLACEMENT_SUBSIDENCE, TsunamiGeologicalDataPeer::DISPLACEMENT_UPLIFT, TsunamiGeologicalDataPeer::EIL, TsunamiGeologicalDataPeer::EIL_CHARACTERISTICS, TsunamiGeologicalDataPeer::EIL_DIST_INLAND, TsunamiGeologicalDataPeer::EIL_ELEVATION, TsunamiGeologicalDataPeer::FAULT, TsunamiGeologicalDataPeer::FAULT_GEOMORPHIC, TsunamiGeologicalDataPeer::FAULT_OFFSET, TsunamiGeologicalDataPeer::FAULT_PALEO, TsunamiGeologicalDataPeer::FAULT_STRIKE_MEASURE, TsunamiGeologicalDataPeer::FAULT_TYPE, TsunamiGeologicalDataPeer::GMCHANGES, TsunamiGeologicalDataPeer::GMCHANGES_BED_MOD, TsunamiGeologicalDataPeer::GMCHANGES_DEPOSIT, TsunamiGeologicalDataPeer::GMCHANGES_SCOUR, TsunamiGeologicalDataPeer::PALEO, TsunamiGeologicalDataPeer::PALEO_CHARACTERISTICS, TsunamiGeologicalDataPeer::PALEO_CORE_SAMPLES, TsunamiGeologicalDataPeer::PALEO_DIST_INLAND, TsunamiGeologicalDataPeer::PALEO_ELEVATION, TsunamiGeologicalDataPeer::PALEO_OUTCROPS, TsunamiGeologicalDataPeer::PALEO_SCALE, TsunamiGeologicalDataPeer::PALEO_SED_PEELS, TsunamiGeologicalDataPeer::PALEO_SPATIAL_VAR, TsunamiGeologicalDataPeer::SMSL, TsunamiGeologicalDataPeer::SSL_COEFFICIENT_OF_FRICTION, TsunamiGeologicalDataPeer::SSL_DEPOSITS, TsunamiGeologicalDataPeer::SSL_SCARS, TsunamiGeologicalDataPeer::TDCBM, TsunamiGeologicalDataPeer::TDCBM_CHARACTERISTICS, TsunamiGeologicalDataPeer::TDCBM_DIST_INLAND, TsunamiGeologicalDataPeer::TDCBM_ELEVATION, TsunamiGeologicalDataPeer::TDCBM_SCALE, TsunamiGeologicalDataPeer::TDCBM_SPATIAL_VAR, TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_GEOLOGICAL_DATA_ID', 'DISPLACEMENT', 'DISPLACEMENT_SUBSIDENCE', 'DISPLACEMENT_UPLIFT', 'EIL', 'EIL_CHARACTERISTICS', 'EIL_DIST_INLAND', 'EIL_ELEVATION', 'FAULT', 'FAULT_GEOMORPHIC', 'FAULT_OFFSET', 'FAULT_PALEO', 'FAULT_STRIKE_MEASURE', 'FAULT_TYPE', 'GMCHANGES', 'GMCHANGES_BED_MOD', 'GMCHANGES_DEPOSIT', 'GMCHANGES_SCOUR', 'PALEO', 'PALEO_CHARACTERISTICS', 'PALEO_CORE_SAMPLES', 'PALEO_DIST_INLAND', 'PALEO_ELEVATION', 'PALEO_OUTCROPS', 'PALEO_SCALE', 'PALEO_SED_PEELS', 'PALEO_SPATIAL_VAR', 'SMSL', 'SSL_COEFFICIENT_OF_FRICTION', 'SSL_DEPOSITS', 'SSL_SCARS', 'TDCBM', 'TDCBM_CHARACTERISTICS', 'TDCBM_DIST_INLAND', 'TDCBM_ELEVATION', 'TDCBM_SCALE', 'TDCBM_SPATIAL_VAR', 'TSUNAMI_DOC_LIB_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Displacement' => 1, 'DisplacementSubsidence' => 2, 'DisplacementUplift' => 3, 'Eil' => 4, 'EilCharacteristics' => 5, 'EilDistInland' => 6, 'EilElevation' => 7, 'Fault' => 8, 'FaultGeomorphic' => 9, 'FaultOffset' => 10, 'FaultPaleo' => 11, 'FaultStrikeMeasure' => 12, 'FaultType' => 13, 'Gmchanges' => 14, 'GmchangesBedMod' => 15, 'GmchangesDeposit' => 16, 'GmchangesScour' => 17, 'Paleo' => 18, 'PaleoCharacteristics' => 19, 'PaleoCoreSamples' => 20, 'PaleoDistInland' => 21, 'PaleoElevation' => 22, 'PaleoOutcrops' => 23, 'PaleoScale' => 24, 'PaleoSedPeels' => 25, 'PaleoSpatialVar' => 26, 'Smsl' => 27, 'SslCoefficientOfFriction' => 28, 'SslDeposits' => 29, 'SslScars' => 30, 'Tdcbm' => 31, 'TdcbmCharacteristics' => 32, 'TdcbmDistInland' => 33, 'TdcbmElevation' => 34, 'TdcbmScale' => 35, 'TdcbmSpatialVar' => 36, 'TsunamiDocLibId' => 37, ),
		BasePeer::TYPE_COLNAME => array (TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID => 0, TsunamiGeologicalDataPeer::DISPLACEMENT => 1, TsunamiGeologicalDataPeer::DISPLACEMENT_SUBSIDENCE => 2, TsunamiGeologicalDataPeer::DISPLACEMENT_UPLIFT => 3, TsunamiGeologicalDataPeer::EIL => 4, TsunamiGeologicalDataPeer::EIL_CHARACTERISTICS => 5, TsunamiGeologicalDataPeer::EIL_DIST_INLAND => 6, TsunamiGeologicalDataPeer::EIL_ELEVATION => 7, TsunamiGeologicalDataPeer::FAULT => 8, TsunamiGeologicalDataPeer::FAULT_GEOMORPHIC => 9, TsunamiGeologicalDataPeer::FAULT_OFFSET => 10, TsunamiGeologicalDataPeer::FAULT_PALEO => 11, TsunamiGeologicalDataPeer::FAULT_STRIKE_MEASURE => 12, TsunamiGeologicalDataPeer::FAULT_TYPE => 13, TsunamiGeologicalDataPeer::GMCHANGES => 14, TsunamiGeologicalDataPeer::GMCHANGES_BED_MOD => 15, TsunamiGeologicalDataPeer::GMCHANGES_DEPOSIT => 16, TsunamiGeologicalDataPeer::GMCHANGES_SCOUR => 17, TsunamiGeologicalDataPeer::PALEO => 18, TsunamiGeologicalDataPeer::PALEO_CHARACTERISTICS => 19, TsunamiGeologicalDataPeer::PALEO_CORE_SAMPLES => 20, TsunamiGeologicalDataPeer::PALEO_DIST_INLAND => 21, TsunamiGeologicalDataPeer::PALEO_ELEVATION => 22, TsunamiGeologicalDataPeer::PALEO_OUTCROPS => 23, TsunamiGeologicalDataPeer::PALEO_SCALE => 24, TsunamiGeologicalDataPeer::PALEO_SED_PEELS => 25, TsunamiGeologicalDataPeer::PALEO_SPATIAL_VAR => 26, TsunamiGeologicalDataPeer::SMSL => 27, TsunamiGeologicalDataPeer::SSL_COEFFICIENT_OF_FRICTION => 28, TsunamiGeologicalDataPeer::SSL_DEPOSITS => 29, TsunamiGeologicalDataPeer::SSL_SCARS => 30, TsunamiGeologicalDataPeer::TDCBM => 31, TsunamiGeologicalDataPeer::TDCBM_CHARACTERISTICS => 32, TsunamiGeologicalDataPeer::TDCBM_DIST_INLAND => 33, TsunamiGeologicalDataPeer::TDCBM_ELEVATION => 34, TsunamiGeologicalDataPeer::TDCBM_SCALE => 35, TsunamiGeologicalDataPeer::TDCBM_SPATIAL_VAR => 36, TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID => 37, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_GEOLOGICAL_DATA_ID' => 0, 'DISPLACEMENT' => 1, 'DISPLACEMENT_SUBSIDENCE' => 2, 'DISPLACEMENT_UPLIFT' => 3, 'EIL' => 4, 'EIL_CHARACTERISTICS' => 5, 'EIL_DIST_INLAND' => 6, 'EIL_ELEVATION' => 7, 'FAULT' => 8, 'FAULT_GEOMORPHIC' => 9, 'FAULT_OFFSET' => 10, 'FAULT_PALEO' => 11, 'FAULT_STRIKE_MEASURE' => 12, 'FAULT_TYPE' => 13, 'GMCHANGES' => 14, 'GMCHANGES_BED_MOD' => 15, 'GMCHANGES_DEPOSIT' => 16, 'GMCHANGES_SCOUR' => 17, 'PALEO' => 18, 'PALEO_CHARACTERISTICS' => 19, 'PALEO_CORE_SAMPLES' => 20, 'PALEO_DIST_INLAND' => 21, 'PALEO_ELEVATION' => 22, 'PALEO_OUTCROPS' => 23, 'PALEO_SCALE' => 24, 'PALEO_SED_PEELS' => 25, 'PALEO_SPATIAL_VAR' => 26, 'SMSL' => 27, 'SSL_COEFFICIENT_OF_FRICTION' => 28, 'SSL_DEPOSITS' => 29, 'SSL_SCARS' => 30, 'TDCBM' => 31, 'TDCBM_CHARACTERISTICS' => 32, 'TDCBM_DIST_INLAND' => 33, 'TDCBM_ELEVATION' => 34, 'TDCBM_SCALE' => 35, 'TDCBM_SPATIAL_VAR' => 36, 'TSUNAMI_DOC_LIB_ID' => 37, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/tsunami/map/TsunamiGeologicalDataMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.tsunami.map.TsunamiGeologicalDataMapBuilder');
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
			$map = TsunamiGeologicalDataPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. TsunamiGeologicalDataPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(TsunamiGeologicalDataPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::DISPLACEMENT);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::DISPLACEMENT_SUBSIDENCE);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::DISPLACEMENT_UPLIFT);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::EIL);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::EIL_CHARACTERISTICS);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::EIL_DIST_INLAND);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::EIL_ELEVATION);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::FAULT);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::FAULT_GEOMORPHIC);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::FAULT_OFFSET);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::FAULT_PALEO);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::FAULT_STRIKE_MEASURE);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::FAULT_TYPE);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::GMCHANGES);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::GMCHANGES_BED_MOD);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::GMCHANGES_DEPOSIT);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::GMCHANGES_SCOUR);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::PALEO);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::PALEO_CHARACTERISTICS);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::PALEO_CORE_SAMPLES);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::PALEO_DIST_INLAND);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::PALEO_ELEVATION);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::PALEO_OUTCROPS);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::PALEO_SCALE);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::PALEO_SED_PEELS);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::PALEO_SPATIAL_VAR);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::SMSL);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::SSL_COEFFICIENT_OF_FRICTION);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::SSL_DEPOSITS);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::SSL_SCARS);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::TDCBM);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::TDCBM_CHARACTERISTICS);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::TDCBM_DIST_INLAND);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::TDCBM_ELEVATION);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::TDCBM_SCALE);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::TDCBM_SPATIAL_VAR);

		$criteria->addSelectColumn(TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID);

	}

	const COUNT = 'COUNT(TSUNAMI_GEOLOGICAL_DATA.TSUNAMI_GEOLOGICAL_DATA_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT TSUNAMI_GEOLOGICAL_DATA.TSUNAMI_GEOLOGICAL_DATA_ID)';

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
			$criteria->addSelectColumn(TsunamiGeologicalDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiGeologicalDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = TsunamiGeologicalDataPeer::doSelectRS($criteria, $con);
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
	 * @return     TsunamiGeologicalData
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = TsunamiGeologicalDataPeer::doSelect($critcopy, $con);
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
		return TsunamiGeologicalDataPeer::populateObjects(TsunamiGeologicalDataPeer::doSelectRS($criteria, $con));
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
			TsunamiGeologicalDataPeer::addSelectColumns($criteria);
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
		$cls = TsunamiGeologicalDataPeer::getOMClass();
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
			$criteria->addSelectColumn(TsunamiGeologicalDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiGeologicalDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = TsunamiGeologicalDataPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiGeologicalData objects pre-filled with their TsunamiDocLib objects.
	 *
	 * @return     array Array of TsunamiGeologicalData objects.
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

		TsunamiGeologicalDataPeer::addSelectColumns($c);
		$startcol = (TsunamiGeologicalDataPeer::NUM_COLUMNS - TsunamiGeologicalDataPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		TsunamiDocLibPeer::addSelectColumns($c);

		$c->addJoin(TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiGeologicalDataPeer::getOMClass();

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
					$temp_obj2->addTsunamiGeologicalData($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initTsunamiGeologicalDatas();
				$obj2->addTsunamiGeologicalData($obj1); //CHECKME
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
			$criteria->addSelectColumn(TsunamiGeologicalDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiGeologicalDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = TsunamiGeologicalDataPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiGeologicalData objects pre-filled with all related objects.
	 *
	 * @return     array Array of TsunamiGeologicalData objects.
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

		TsunamiGeologicalDataPeer::addSelectColumns($c);
		$startcol2 = (TsunamiGeologicalDataPeer::NUM_COLUMNS - TsunamiGeologicalDataPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		TsunamiDocLibPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + TsunamiDocLibPeer::NUM_COLUMNS;

		$c->addJoin(TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiGeologicalDataPeer::getOMClass();


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
					$temp_obj2->addTsunamiGeologicalData($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initTsunamiGeologicalDatas();
				$obj2->addTsunamiGeologicalData($obj1);
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
		return TsunamiGeologicalDataPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a TsunamiGeologicalData or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiGeologicalData object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from TsunamiGeologicalData object
		}

		$criteria->remove(TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a TsunamiGeologicalData or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiGeologicalData object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID);
			$selectCriteria->add(TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID, $criteria->remove(TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID), $comparison);

		} else { // $values is TsunamiGeologicalData object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the TSUNAMI_GEOLOGICAL_DATA table.
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
			$affectedRows += BasePeer::doDeleteAll(TsunamiGeologicalDataPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a TsunamiGeologicalData or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or TsunamiGeologicalData object or primary key or array of primary keys
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
			$con = Propel::getConnection(TsunamiGeologicalDataPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof TsunamiGeologicalData) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given TsunamiGeologicalData object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      TsunamiGeologicalData $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(TsunamiGeologicalData $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(TsunamiGeologicalDataPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(TsunamiGeologicalDataPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::DISPLACEMENT))
			$columns[TsunamiGeologicalDataPeer::DISPLACEMENT] = $obj->getDisplacement();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::DISPLACEMENT_SUBSIDENCE))
			$columns[TsunamiGeologicalDataPeer::DISPLACEMENT_SUBSIDENCE] = $obj->getDisplacementSubsidence();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::DISPLACEMENT_UPLIFT))
			$columns[TsunamiGeologicalDataPeer::DISPLACEMENT_UPLIFT] = $obj->getDisplacementUplift();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::EIL))
			$columns[TsunamiGeologicalDataPeer::EIL] = $obj->getEil();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::EIL_CHARACTERISTICS))
			$columns[TsunamiGeologicalDataPeer::EIL_CHARACTERISTICS] = $obj->getEilCharacteristics();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::EIL_DIST_INLAND))
			$columns[TsunamiGeologicalDataPeer::EIL_DIST_INLAND] = $obj->getEilDistInland();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::EIL_ELEVATION))
			$columns[TsunamiGeologicalDataPeer::EIL_ELEVATION] = $obj->getEilElevation();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::FAULT))
			$columns[TsunamiGeologicalDataPeer::FAULT] = $obj->getFault();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::FAULT_GEOMORPHIC))
			$columns[TsunamiGeologicalDataPeer::FAULT_GEOMORPHIC] = $obj->getFaultGeomorphic();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::FAULT_OFFSET))
			$columns[TsunamiGeologicalDataPeer::FAULT_OFFSET] = $obj->getFaultOffset();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::FAULT_PALEO))
			$columns[TsunamiGeologicalDataPeer::FAULT_PALEO] = $obj->getFaultPaleo();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::FAULT_STRIKE_MEASURE))
			$columns[TsunamiGeologicalDataPeer::FAULT_STRIKE_MEASURE] = $obj->getFaultStrikeMeasure();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::FAULT_TYPE))
			$columns[TsunamiGeologicalDataPeer::FAULT_TYPE] = $obj->getFaultType();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::GMCHANGES))
			$columns[TsunamiGeologicalDataPeer::GMCHANGES] = $obj->getGmchanges();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::GMCHANGES_BED_MOD))
			$columns[TsunamiGeologicalDataPeer::GMCHANGES_BED_MOD] = $obj->getGmchangesBedMod();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::GMCHANGES_DEPOSIT))
			$columns[TsunamiGeologicalDataPeer::GMCHANGES_DEPOSIT] = $obj->getGmchangesDeposit();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::GMCHANGES_SCOUR))
			$columns[TsunamiGeologicalDataPeer::GMCHANGES_SCOUR] = $obj->getGmchangesScour();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::PALEO))
			$columns[TsunamiGeologicalDataPeer::PALEO] = $obj->getPaleo();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::PALEO_CHARACTERISTICS))
			$columns[TsunamiGeologicalDataPeer::PALEO_CHARACTERISTICS] = $obj->getPaleoCharacteristics();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::PALEO_CORE_SAMPLES))
			$columns[TsunamiGeologicalDataPeer::PALEO_CORE_SAMPLES] = $obj->getPaleoCoreSamples();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::PALEO_DIST_INLAND))
			$columns[TsunamiGeologicalDataPeer::PALEO_DIST_INLAND] = $obj->getPaleoDistInland();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::PALEO_ELEVATION))
			$columns[TsunamiGeologicalDataPeer::PALEO_ELEVATION] = $obj->getPaleoElevation();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::PALEO_OUTCROPS))
			$columns[TsunamiGeologicalDataPeer::PALEO_OUTCROPS] = $obj->getPaleoOutcrops();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::PALEO_SCALE))
			$columns[TsunamiGeologicalDataPeer::PALEO_SCALE] = $obj->getPaleoScale();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::PALEO_SED_PEELS))
			$columns[TsunamiGeologicalDataPeer::PALEO_SED_PEELS] = $obj->getPaleoSedPeels();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::PALEO_SPATIAL_VAR))
			$columns[TsunamiGeologicalDataPeer::PALEO_SPATIAL_VAR] = $obj->getPaleoSpatialVar();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::SMSL))
			$columns[TsunamiGeologicalDataPeer::SMSL] = $obj->getSmsl();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::SSL_COEFFICIENT_OF_FRICTION))
			$columns[TsunamiGeologicalDataPeer::SSL_COEFFICIENT_OF_FRICTION] = $obj->getSslCoefficientOfFriction();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::SSL_DEPOSITS))
			$columns[TsunamiGeologicalDataPeer::SSL_DEPOSITS] = $obj->getSslDeposits();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::SSL_SCARS))
			$columns[TsunamiGeologicalDataPeer::SSL_SCARS] = $obj->getSslScars();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::TDCBM))
			$columns[TsunamiGeologicalDataPeer::TDCBM] = $obj->getTdcbm();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::TDCBM_CHARACTERISTICS))
			$columns[TsunamiGeologicalDataPeer::TDCBM_CHARACTERISTICS] = $obj->getTdcbmCharacteristics();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::TDCBM_DIST_INLAND))
			$columns[TsunamiGeologicalDataPeer::TDCBM_DIST_INLAND] = $obj->getTdcbmDistInland();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::TDCBM_ELEVATION))
			$columns[TsunamiGeologicalDataPeer::TDCBM_ELEVATION] = $obj->getTdcbmElevation();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::TDCBM_SCALE))
			$columns[TsunamiGeologicalDataPeer::TDCBM_SCALE] = $obj->getTdcbmScale();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::TDCBM_SPATIAL_VAR))
			$columns[TsunamiGeologicalDataPeer::TDCBM_SPATIAL_VAR] = $obj->getTdcbmSpatialVar();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID))
			$columns[TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID] = $obj->getTsunamiDocLibId();

		}

		return BasePeer::doValidate(TsunamiGeologicalDataPeer::DATABASE_NAME, TsunamiGeologicalDataPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     TsunamiGeologicalData
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(TsunamiGeologicalDataPeer::DATABASE_NAME);

		$criteria->add(TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID, $pk);


		$v = TsunamiGeologicalDataPeer::doSelect($criteria, $con);

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
			$criteria->add(TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID, $pks, Criteria::IN);
			$objs = TsunamiGeologicalDataPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseTsunamiGeologicalDataPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseTsunamiGeologicalDataPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/tsunami/map/TsunamiGeologicalDataMapBuilder.php';
	Propel::registerMapBuilder('lib.data.tsunami.map.TsunamiGeologicalDataMapBuilder');
}
