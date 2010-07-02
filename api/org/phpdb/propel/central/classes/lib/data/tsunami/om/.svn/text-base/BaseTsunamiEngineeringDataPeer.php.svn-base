<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by TsunamiEngineeringDataPeer::getOMClass()
include_once 'lib/data/tsunami/TsunamiEngineeringData.php';

/**
 * Base static class for performing query and update operations on the 'TSUNAMI_ENGINEERING_DATA' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiEngineeringDataPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'TSUNAMI_ENGINEERING_DATA';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.tsunami.TsunamiEngineeringData';

	/** The total number of columns. */
	const NUM_COLUMNS = 30;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the TSUNAMI_ENGINEERING_DATA_ID field */
	const TSUNAMI_ENGINEERING_DATA_ID = 'TSUNAMI_ENGINEERING_DATA.TSUNAMI_ENGINEERING_DATA_ID';

	/** the column name for the EVENT field */
	const EVENT = 'TSUNAMI_ENGINEERING_DATA.EVENT';

	/** the column name for the EVENT_SENSOR_DATA field */
	const EVENT_SENSOR_DATA = 'TSUNAMI_ENGINEERING_DATA.EVENT_SENSOR_DATA';

	/** the column name for the EVENT_VIDEO field */
	const EVENT_VIDEO = 'TSUNAMI_ENGINEERING_DATA.EVENT_VIDEO';

	/** the column name for the GEOTECH field */
	const GEOTECH = 'TSUNAMI_ENGINEERING_DATA.GEOTECH';

	/** the column name for the GEOTECH_DAMAGE_DESCR field */
	const GEOTECH_DAMAGE_DESCR = 'TSUNAMI_ENGINEERING_DATA.GEOTECH_DAMAGE_DESCR';

	/** the column name for the GEOTECH_SITE_CHAR field */
	const GEOTECH_SITE_CHAR = 'TSUNAMI_ENGINEERING_DATA.GEOTECH_SITE_CHAR';

	/** the column name for the GEOTECH_SOIL_CHAR field */
	const GEOTECH_SOIL_CHAR = 'TSUNAMI_ENGINEERING_DATA.GEOTECH_SOIL_CHAR';

	/** the column name for the GEOTECH_VUL_ASSESSMENT field */
	const GEOTECH_VUL_ASSESSMENT = 'TSUNAMI_ENGINEERING_DATA.GEOTECH_VUL_ASSESSMENT';

	/** the column name for the HM field */
	const HM = 'TSUNAMI_ENGINEERING_DATA.HM';

	/** the column name for the HM_EVAC_PLAN_MAPS field */
	const HM_EVAC_PLAN_MAPS = 'TSUNAMI_ENGINEERING_DATA.HM_EVAC_PLAN_MAPS';

	/** the column name for the HM_FAULT_MAPS field */
	const HM_FAULT_MAPS = 'TSUNAMI_ENGINEERING_DATA.HM_FAULT_MAPS';

	/** the column name for the HM_HAZARD_ASSESSMENT field */
	const HM_HAZARD_ASSESSMENT = 'TSUNAMI_ENGINEERING_DATA.HM_HAZARD_ASSESSMENT';

	/** the column name for the HM_HAZARD_MAPS field */
	const HM_HAZARD_MAPS = 'TSUNAMI_ENGINEERING_DATA.HM_HAZARD_MAPS';

	/** the column name for the HM_SHELTER_LOCATIONS field */
	const HM_SHELTER_LOCATIONS = 'TSUNAMI_ENGINEERING_DATA.HM_SHELTER_LOCATIONS';

	/** the column name for the LIFELINE field */
	const LIFELINE = 'TSUNAMI_ENGINEERING_DATA.LIFELINE';

	/** the column name for the LIFELINE_DAMAGE_DESCR field */
	const LIFELINE_DAMAGE_DESCR = 'TSUNAMI_ENGINEERING_DATA.LIFELINE_DAMAGE_DESCR';

	/** the column name for the LIFELINE_DESIGN field */
	const LIFELINE_DESIGN = 'TSUNAMI_ENGINEERING_DATA.LIFELINE_DESIGN';

	/** the column name for the LIFELINE_SEISMIC_DESIGN field */
	const LIFELINE_SEISMIC_DESIGN = 'TSUNAMI_ENGINEERING_DATA.LIFELINE_SEISMIC_DESIGN';

	/** the column name for the LIFELINE_TYPE field */
	const LIFELINE_TYPE = 'TSUNAMI_ENGINEERING_DATA.LIFELINE_TYPE';

	/** the column name for the LIFELINE_VUL_ASSESSMENT field */
	const LIFELINE_VUL_ASSESSMENT = 'TSUNAMI_ENGINEERING_DATA.LIFELINE_VUL_ASSESSMENT';

	/** the column name for the LIFELINE_YEAR field */
	const LIFELINE_YEAR = 'TSUNAMI_ENGINEERING_DATA.LIFELINE_YEAR';

	/** the column name for the STRUCTURE field */
	const STRUCTURE = 'TSUNAMI_ENGINEERING_DATA.STRUCTURE';

	/** the column name for the STRUCTURE_DAMAGE_DESCR field */
	const STRUCTURE_DAMAGE_DESCR = 'TSUNAMI_ENGINEERING_DATA.STRUCTURE_DAMAGE_DESCR';

	/** the column name for the STRUCTURE_DESIGN field */
	const STRUCTURE_DESIGN = 'TSUNAMI_ENGINEERING_DATA.STRUCTURE_DESIGN';

	/** the column name for the STRUCTURE_SEISMIC_DESIGN field */
	const STRUCTURE_SEISMIC_DESIGN = 'TSUNAMI_ENGINEERING_DATA.STRUCTURE_SEISMIC_DESIGN';

	/** the column name for the STRUCTURE_TYPE field */
	const STRUCTURE_TYPE = 'TSUNAMI_ENGINEERING_DATA.STRUCTURE_TYPE';

	/** the column name for the STRUCTURE_VUL_ASSESSMENT field */
	const STRUCTURE_VUL_ASSESSMENT = 'TSUNAMI_ENGINEERING_DATA.STRUCTURE_VUL_ASSESSMENT';

	/** the column name for the STRUCTURE_YEAR field */
	const STRUCTURE_YEAR = 'TSUNAMI_ENGINEERING_DATA.STRUCTURE_YEAR';

	/** the column name for the TSUNAMI_DOC_LIB_ID field */
	const TSUNAMI_DOC_LIB_ID = 'TSUNAMI_ENGINEERING_DATA.TSUNAMI_DOC_LIB_ID';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Event', 'EventSensorData', 'EventVideo', 'Geotech', 'GeotechDamageDescr', 'GeotechSiteChar', 'GeotechSoilChar', 'GeotechVulAssessment', 'Hm', 'HmEvacPlanMaps', 'HmFaultMaps', 'HmHazardAssessment', 'HmHazardMaps', 'HmShelterLocations', 'Lifeline', 'LifelineDamageDescription', 'LifelineDesign', 'LifelineSeismicDesign', 'LifelineType', 'LifelineVulAssessment', 'LifelineYear', 'Structure', 'StructureDamageDescription', 'StructureDesign', 'StructureSeismicDesign', 'StructureType', 'StructureVulAssessment', 'StructureYear', 'TsunamiDocLibId', ),
		BasePeer::TYPE_COLNAME => array (TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID, TsunamiEngineeringDataPeer::EVENT, TsunamiEngineeringDataPeer::EVENT_SENSOR_DATA, TsunamiEngineeringDataPeer::EVENT_VIDEO, TsunamiEngineeringDataPeer::GEOTECH, TsunamiEngineeringDataPeer::GEOTECH_DAMAGE_DESCR, TsunamiEngineeringDataPeer::GEOTECH_SITE_CHAR, TsunamiEngineeringDataPeer::GEOTECH_SOIL_CHAR, TsunamiEngineeringDataPeer::GEOTECH_VUL_ASSESSMENT, TsunamiEngineeringDataPeer::HM, TsunamiEngineeringDataPeer::HM_EVAC_PLAN_MAPS, TsunamiEngineeringDataPeer::HM_FAULT_MAPS, TsunamiEngineeringDataPeer::HM_HAZARD_ASSESSMENT, TsunamiEngineeringDataPeer::HM_HAZARD_MAPS, TsunamiEngineeringDataPeer::HM_SHELTER_LOCATIONS, TsunamiEngineeringDataPeer::LIFELINE, TsunamiEngineeringDataPeer::LIFELINE_DAMAGE_DESCR, TsunamiEngineeringDataPeer::LIFELINE_DESIGN, TsunamiEngineeringDataPeer::LIFELINE_SEISMIC_DESIGN, TsunamiEngineeringDataPeer::LIFELINE_TYPE, TsunamiEngineeringDataPeer::LIFELINE_VUL_ASSESSMENT, TsunamiEngineeringDataPeer::LIFELINE_YEAR, TsunamiEngineeringDataPeer::STRUCTURE, TsunamiEngineeringDataPeer::STRUCTURE_DAMAGE_DESCR, TsunamiEngineeringDataPeer::STRUCTURE_DESIGN, TsunamiEngineeringDataPeer::STRUCTURE_SEISMIC_DESIGN, TsunamiEngineeringDataPeer::STRUCTURE_TYPE, TsunamiEngineeringDataPeer::STRUCTURE_VUL_ASSESSMENT, TsunamiEngineeringDataPeer::STRUCTURE_YEAR, TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_ENGINEERING_DATA_ID', 'EVENT', 'EVENT_SENSOR_DATA', 'EVENT_VIDEO', 'GEOTECH', 'GEOTECH_DAMAGE_DESCR', 'GEOTECH_SITE_CHAR', 'GEOTECH_SOIL_CHAR', 'GEOTECH_VUL_ASSESSMENT', 'HM', 'HM_EVAC_PLAN_MAPS', 'HM_FAULT_MAPS', 'HM_HAZARD_ASSESSMENT', 'HM_HAZARD_MAPS', 'HM_SHELTER_LOCATIONS', 'LIFELINE', 'LIFELINE_DAMAGE_DESCR', 'LIFELINE_DESIGN', 'LIFELINE_SEISMIC_DESIGN', 'LIFELINE_TYPE', 'LIFELINE_VUL_ASSESSMENT', 'LIFELINE_YEAR', 'STRUCTURE', 'STRUCTURE_DAMAGE_DESCR', 'STRUCTURE_DESIGN', 'STRUCTURE_SEISMIC_DESIGN', 'STRUCTURE_TYPE', 'STRUCTURE_VUL_ASSESSMENT', 'STRUCTURE_YEAR', 'TSUNAMI_DOC_LIB_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Event' => 1, 'EventSensorData' => 2, 'EventVideo' => 3, 'Geotech' => 4, 'GeotechDamageDescr' => 5, 'GeotechSiteChar' => 6, 'GeotechSoilChar' => 7, 'GeotechVulAssessment' => 8, 'Hm' => 9, 'HmEvacPlanMaps' => 10, 'HmFaultMaps' => 11, 'HmHazardAssessment' => 12, 'HmHazardMaps' => 13, 'HmShelterLocations' => 14, 'Lifeline' => 15, 'LifelineDamageDescription' => 16, 'LifelineDesign' => 17, 'LifelineSeismicDesign' => 18, 'LifelineType' => 19, 'LifelineVulAssessment' => 20, 'LifelineYear' => 21, 'Structure' => 22, 'StructureDamageDescription' => 23, 'StructureDesign' => 24, 'StructureSeismicDesign' => 25, 'StructureType' => 26, 'StructureVulAssessment' => 27, 'StructureYear' => 28, 'TsunamiDocLibId' => 29, ),
		BasePeer::TYPE_COLNAME => array (TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID => 0, TsunamiEngineeringDataPeer::EVENT => 1, TsunamiEngineeringDataPeer::EVENT_SENSOR_DATA => 2, TsunamiEngineeringDataPeer::EVENT_VIDEO => 3, TsunamiEngineeringDataPeer::GEOTECH => 4, TsunamiEngineeringDataPeer::GEOTECH_DAMAGE_DESCR => 5, TsunamiEngineeringDataPeer::GEOTECH_SITE_CHAR => 6, TsunamiEngineeringDataPeer::GEOTECH_SOIL_CHAR => 7, TsunamiEngineeringDataPeer::GEOTECH_VUL_ASSESSMENT => 8, TsunamiEngineeringDataPeer::HM => 9, TsunamiEngineeringDataPeer::HM_EVAC_PLAN_MAPS => 10, TsunamiEngineeringDataPeer::HM_FAULT_MAPS => 11, TsunamiEngineeringDataPeer::HM_HAZARD_ASSESSMENT => 12, TsunamiEngineeringDataPeer::HM_HAZARD_MAPS => 13, TsunamiEngineeringDataPeer::HM_SHELTER_LOCATIONS => 14, TsunamiEngineeringDataPeer::LIFELINE => 15, TsunamiEngineeringDataPeer::LIFELINE_DAMAGE_DESCR => 16, TsunamiEngineeringDataPeer::LIFELINE_DESIGN => 17, TsunamiEngineeringDataPeer::LIFELINE_SEISMIC_DESIGN => 18, TsunamiEngineeringDataPeer::LIFELINE_TYPE => 19, TsunamiEngineeringDataPeer::LIFELINE_VUL_ASSESSMENT => 20, TsunamiEngineeringDataPeer::LIFELINE_YEAR => 21, TsunamiEngineeringDataPeer::STRUCTURE => 22, TsunamiEngineeringDataPeer::STRUCTURE_DAMAGE_DESCR => 23, TsunamiEngineeringDataPeer::STRUCTURE_DESIGN => 24, TsunamiEngineeringDataPeer::STRUCTURE_SEISMIC_DESIGN => 25, TsunamiEngineeringDataPeer::STRUCTURE_TYPE => 26, TsunamiEngineeringDataPeer::STRUCTURE_VUL_ASSESSMENT => 27, TsunamiEngineeringDataPeer::STRUCTURE_YEAR => 28, TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID => 29, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_ENGINEERING_DATA_ID' => 0, 'EVENT' => 1, 'EVENT_SENSOR_DATA' => 2, 'EVENT_VIDEO' => 3, 'GEOTECH' => 4, 'GEOTECH_DAMAGE_DESCR' => 5, 'GEOTECH_SITE_CHAR' => 6, 'GEOTECH_SOIL_CHAR' => 7, 'GEOTECH_VUL_ASSESSMENT' => 8, 'HM' => 9, 'HM_EVAC_PLAN_MAPS' => 10, 'HM_FAULT_MAPS' => 11, 'HM_HAZARD_ASSESSMENT' => 12, 'HM_HAZARD_MAPS' => 13, 'HM_SHELTER_LOCATIONS' => 14, 'LIFELINE' => 15, 'LIFELINE_DAMAGE_DESCR' => 16, 'LIFELINE_DESIGN' => 17, 'LIFELINE_SEISMIC_DESIGN' => 18, 'LIFELINE_TYPE' => 19, 'LIFELINE_VUL_ASSESSMENT' => 20, 'LIFELINE_YEAR' => 21, 'STRUCTURE' => 22, 'STRUCTURE_DAMAGE_DESCR' => 23, 'STRUCTURE_DESIGN' => 24, 'STRUCTURE_SEISMIC_DESIGN' => 25, 'STRUCTURE_TYPE' => 26, 'STRUCTURE_VUL_ASSESSMENT' => 27, 'STRUCTURE_YEAR' => 28, 'TSUNAMI_DOC_LIB_ID' => 29, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/tsunami/map/TsunamiEngineeringDataMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.tsunami.map.TsunamiEngineeringDataMapBuilder');
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
			$map = TsunamiEngineeringDataPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. TsunamiEngineeringDataPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(TsunamiEngineeringDataPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::EVENT);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::EVENT_SENSOR_DATA);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::EVENT_VIDEO);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::GEOTECH);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::GEOTECH_DAMAGE_DESCR);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::GEOTECH_SITE_CHAR);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::GEOTECH_SOIL_CHAR);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::GEOTECH_VUL_ASSESSMENT);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::HM);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::HM_EVAC_PLAN_MAPS);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::HM_FAULT_MAPS);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::HM_HAZARD_ASSESSMENT);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::HM_HAZARD_MAPS);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::HM_SHELTER_LOCATIONS);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::LIFELINE);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::LIFELINE_DAMAGE_DESCR);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::LIFELINE_DESIGN);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::LIFELINE_SEISMIC_DESIGN);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::LIFELINE_TYPE);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::LIFELINE_VUL_ASSESSMENT);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::LIFELINE_YEAR);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::STRUCTURE);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::STRUCTURE_DAMAGE_DESCR);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::STRUCTURE_DESIGN);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::STRUCTURE_SEISMIC_DESIGN);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::STRUCTURE_TYPE);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::STRUCTURE_VUL_ASSESSMENT);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::STRUCTURE_YEAR);

		$criteria->addSelectColumn(TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID);

	}

	const COUNT = 'COUNT(TSUNAMI_ENGINEERING_DATA.TSUNAMI_ENGINEERING_DATA_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT TSUNAMI_ENGINEERING_DATA.TSUNAMI_ENGINEERING_DATA_ID)';

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
			$criteria->addSelectColumn(TsunamiEngineeringDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiEngineeringDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = TsunamiEngineeringDataPeer::doSelectRS($criteria, $con);
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
	 * @return     TsunamiEngineeringData
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = TsunamiEngineeringDataPeer::doSelect($critcopy, $con);
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
		return TsunamiEngineeringDataPeer::populateObjects(TsunamiEngineeringDataPeer::doSelectRS($criteria, $con));
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
			TsunamiEngineeringDataPeer::addSelectColumns($criteria);
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
		$cls = TsunamiEngineeringDataPeer::getOMClass();
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
			$criteria->addSelectColumn(TsunamiEngineeringDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiEngineeringDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = TsunamiEngineeringDataPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiEngineeringData objects pre-filled with their TsunamiDocLib objects.
	 *
	 * @return     array Array of TsunamiEngineeringData objects.
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

		TsunamiEngineeringDataPeer::addSelectColumns($c);
		$startcol = (TsunamiEngineeringDataPeer::NUM_COLUMNS - TsunamiEngineeringDataPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		TsunamiDocLibPeer::addSelectColumns($c);

		$c->addJoin(TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiEngineeringDataPeer::getOMClass();

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
					$temp_obj2->addTsunamiEngineeringData($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initTsunamiEngineeringDatas();
				$obj2->addTsunamiEngineeringData($obj1); //CHECKME
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
			$criteria->addSelectColumn(TsunamiEngineeringDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiEngineeringDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = TsunamiEngineeringDataPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiEngineeringData objects pre-filled with all related objects.
	 *
	 * @return     array Array of TsunamiEngineeringData objects.
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

		TsunamiEngineeringDataPeer::addSelectColumns($c);
		$startcol2 = (TsunamiEngineeringDataPeer::NUM_COLUMNS - TsunamiEngineeringDataPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		TsunamiDocLibPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + TsunamiDocLibPeer::NUM_COLUMNS;

		$c->addJoin(TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiEngineeringDataPeer::getOMClass();


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
					$temp_obj2->addTsunamiEngineeringData($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initTsunamiEngineeringDatas();
				$obj2->addTsunamiEngineeringData($obj1);
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
		return TsunamiEngineeringDataPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a TsunamiEngineeringData or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiEngineeringData object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from TsunamiEngineeringData object
		}

		$criteria->remove(TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a TsunamiEngineeringData or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiEngineeringData object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID);
			$selectCriteria->add(TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID, $criteria->remove(TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID), $comparison);

		} else { // $values is TsunamiEngineeringData object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the TSUNAMI_ENGINEERING_DATA table.
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
			$affectedRows += BasePeer::doDeleteAll(TsunamiEngineeringDataPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a TsunamiEngineeringData or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or TsunamiEngineeringData object or primary key or array of primary keys
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
			$con = Propel::getConnection(TsunamiEngineeringDataPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof TsunamiEngineeringData) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given TsunamiEngineeringData object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      TsunamiEngineeringData $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(TsunamiEngineeringData $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(TsunamiEngineeringDataPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(TsunamiEngineeringDataPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::EVENT))
			$columns[TsunamiEngineeringDataPeer::EVENT] = $obj->getEvent();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::EVENT_SENSOR_DATA))
			$columns[TsunamiEngineeringDataPeer::EVENT_SENSOR_DATA] = $obj->getEventSensorData();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::EVENT_VIDEO))
			$columns[TsunamiEngineeringDataPeer::EVENT_VIDEO] = $obj->getEventVideo();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::GEOTECH))
			$columns[TsunamiEngineeringDataPeer::GEOTECH] = $obj->getGeotech();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::GEOTECH_DAMAGE_DESCR))
			$columns[TsunamiEngineeringDataPeer::GEOTECH_DAMAGE_DESCR] = $obj->getGeotechDamageDescr();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::GEOTECH_SITE_CHAR))
			$columns[TsunamiEngineeringDataPeer::GEOTECH_SITE_CHAR] = $obj->getGeotechSiteChar();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::GEOTECH_SOIL_CHAR))
			$columns[TsunamiEngineeringDataPeer::GEOTECH_SOIL_CHAR] = $obj->getGeotechSoilChar();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::GEOTECH_VUL_ASSESSMENT))
			$columns[TsunamiEngineeringDataPeer::GEOTECH_VUL_ASSESSMENT] = $obj->getGeotechVulAssessment();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::HM))
			$columns[TsunamiEngineeringDataPeer::HM] = $obj->getHm();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::HM_EVAC_PLAN_MAPS))
			$columns[TsunamiEngineeringDataPeer::HM_EVAC_PLAN_MAPS] = $obj->getHmEvacPlanMaps();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::HM_FAULT_MAPS))
			$columns[TsunamiEngineeringDataPeer::HM_FAULT_MAPS] = $obj->getHmFaultMaps();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::HM_HAZARD_ASSESSMENT))
			$columns[TsunamiEngineeringDataPeer::HM_HAZARD_ASSESSMENT] = $obj->getHmHazardAssessment();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::HM_HAZARD_MAPS))
			$columns[TsunamiEngineeringDataPeer::HM_HAZARD_MAPS] = $obj->getHmHazardMaps();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::HM_SHELTER_LOCATIONS))
			$columns[TsunamiEngineeringDataPeer::HM_SHELTER_LOCATIONS] = $obj->getHmShelterLocations();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE))
			$columns[TsunamiEngineeringDataPeer::LIFELINE] = $obj->getLifeline();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_DAMAGE_DESCR))
			$columns[TsunamiEngineeringDataPeer::LIFELINE_DAMAGE_DESCR] = $obj->getLifelineDamageDescription();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_DESIGN))
			$columns[TsunamiEngineeringDataPeer::LIFELINE_DESIGN] = $obj->getLifelineDesign();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_SEISMIC_DESIGN))
			$columns[TsunamiEngineeringDataPeer::LIFELINE_SEISMIC_DESIGN] = $obj->getLifelineSeismicDesign();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_TYPE))
			$columns[TsunamiEngineeringDataPeer::LIFELINE_TYPE] = $obj->getLifelineType();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_VUL_ASSESSMENT))
			$columns[TsunamiEngineeringDataPeer::LIFELINE_VUL_ASSESSMENT] = $obj->getLifelineVulAssessment();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_YEAR))
			$columns[TsunamiEngineeringDataPeer::LIFELINE_YEAR] = $obj->getLifelineYear();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE))
			$columns[TsunamiEngineeringDataPeer::STRUCTURE] = $obj->getStructure();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_DAMAGE_DESCR))
			$columns[TsunamiEngineeringDataPeer::STRUCTURE_DAMAGE_DESCR] = $obj->getStructureDamageDescription();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_DESIGN))
			$columns[TsunamiEngineeringDataPeer::STRUCTURE_DESIGN] = $obj->getStructureDesign();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_SEISMIC_DESIGN))
			$columns[TsunamiEngineeringDataPeer::STRUCTURE_SEISMIC_DESIGN] = $obj->getStructureSeismicDesign();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_TYPE))
			$columns[TsunamiEngineeringDataPeer::STRUCTURE_TYPE] = $obj->getStructureType();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_VUL_ASSESSMENT))
			$columns[TsunamiEngineeringDataPeer::STRUCTURE_VUL_ASSESSMENT] = $obj->getStructureVulAssessment();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_YEAR))
			$columns[TsunamiEngineeringDataPeer::STRUCTURE_YEAR] = $obj->getStructureYear();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID))
			$columns[TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID] = $obj->getTsunamiDocLibId();

		}

		return BasePeer::doValidate(TsunamiEngineeringDataPeer::DATABASE_NAME, TsunamiEngineeringDataPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     TsunamiEngineeringData
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(TsunamiEngineeringDataPeer::DATABASE_NAME);

		$criteria->add(TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID, $pk);


		$v = TsunamiEngineeringDataPeer::doSelect($criteria, $con);

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
			$criteria->add(TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID, $pks, Criteria::IN);
			$objs = TsunamiEngineeringDataPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseTsunamiEngineeringDataPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseTsunamiEngineeringDataPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/tsunami/map/TsunamiEngineeringDataMapBuilder.php';
	Propel::registerMapBuilder('lib.data.tsunami.map.TsunamiEngineeringDataMapBuilder');
}
