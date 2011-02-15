<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by SiteReportsQARRPSPeer::getOMClass()
include_once 'lib/data/SiteReportsQARRPS.php';

/**
 * Base static class for performing query and update operations on the 'SITEREPORTS_QAR_RPS' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSiteReportsQARRPSPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'SITEREPORTS_QAR_RPS';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.SiteReportsQARRPS';

	/** The total number of columns. */
	const NUM_COLUMNS = 26;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'SITEREPORTS_QAR_RPS.ID';

	/** the column name for the QAR_ID field */
	const QAR_ID = 'SITEREPORTS_QAR_RPS.QAR_ID';

	/** the column name for the PROJECT field */
	const PROJECT = 'SITEREPORTS_QAR_RPS.PROJECT';

	/** the column name for the PROJECT_WAREHOUSE_ID field */
	const PROJECT_WAREHOUSE_ID = 'SITEREPORTS_QAR_RPS.PROJECT_WAREHOUSE_ID';

	/** the column name for the NEESR_SHARED_USE_YEAR field */
	const NEESR_SHARED_USE_YEAR = 'SITEREPORTS_QAR_RPS.NEESR_SHARED_USE_YEAR';

	/** the column name for the OFFICIAL_AWARD_NUMBER field */
	const OFFICIAL_AWARD_NUMBER = 'SITEREPORTS_QAR_RPS.OFFICIAL_AWARD_NUMBER';

	/** the column name for the PROJECT_TITLE field */
	const PROJECT_TITLE = 'SITEREPORTS_QAR_RPS.PROJECT_TITLE';

	/** the column name for the PROJECT_NUMBER field */
	const PROJECT_NUMBER = 'SITEREPORTS_QAR_RPS.PROJECT_NUMBER';

	/** the column name for the PI_NAME field */
	const PI_NAME = 'SITEREPORTS_QAR_RPS.PI_NAME';

	/** the column name for the INSTITUTION field */
	const INSTITUTION = 'SITEREPORTS_QAR_RPS.INSTITUTION';

	/** the column name for the PPP_FY_START_PRG field */
	const PPP_FY_START_PRG = 'SITEREPORTS_QAR_RPS.PPP_FY_START_PRG';

	/** the column name for the PPP_FY_END_PRG field */
	const PPP_FY_END_PRG = 'SITEREPORTS_QAR_RPS.PPP_FY_END_PRG';

	/** the column name for the APP_Q1 field */
	const APP_Q1 = 'SITEREPORTS_QAR_RPS.APP_Q1';

	/** the column name for the APP_Q2 field */
	const APP_Q2 = 'SITEREPORTS_QAR_RPS.APP_Q2';

	/** the column name for the APP_Q3 field */
	const APP_Q3 = 'SITEREPORTS_QAR_RPS.APP_Q3';

	/** the column name for the APP_Q4 field */
	const APP_Q4 = 'SITEREPORTS_QAR_RPS.APP_Q4';

	/** the column name for the Q1_NAR field */
	const Q1_NAR = 'SITEREPORTS_QAR_RPS.Q1_NAR';

	/** the column name for the Q2_NAR field */
	const Q2_NAR = 'SITEREPORTS_QAR_RPS.Q2_NAR';

	/** the column name for the Q3_NAR field */
	const Q3_NAR = 'SITEREPORTS_QAR_RPS.Q3_NAR';

	/** the column name for the Q4_NAR field */
	const Q4_NAR = 'SITEREPORTS_QAR_RPS.Q4_NAR';

	/** the column name for the PROJECT_WEIGHT field */
	const PROJECT_WEIGHT = 'SITEREPORTS_QAR_RPS.PROJECT_WEIGHT';

	/** the column name for the WEIGHTED_PROGRESS field */
	const WEIGHTED_PROGRESS = 'SITEREPORTS_QAR_RPS.WEIGHTED_PROGRESS';

	/** the column name for the CREATED_BY field */
	const CREATED_BY = 'SITEREPORTS_QAR_RPS.CREATED_BY';

	/** the column name for the CREATED_ON field */
	const CREATED_ON = 'SITEREPORTS_QAR_RPS.CREATED_ON';

	/** the column name for the UPDATED_BY field */
	const UPDATED_BY = 'SITEREPORTS_QAR_RPS.UPDATED_BY';

	/** the column name for the UPDATED_ON field */
	const UPDATED_ON = 'SITEREPORTS_QAR_RPS.UPDATED_ON';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('ID', 'QAR_ID', 'PROJECT', 'PROJECT_WAREHOUSE_ID', 'NEESR_SHARED_USE_YEAR', 'OFFICIAL_AWARD_NUMBER', 'PROJECT_TITLE', 'PROJECT_NUMBER', 'PI_NAME', 'INSTITUTION', 'PPP_FY_START_PRG', 'PPP_FY_END_PRG', 'APP_Q1', 'APP_Q2', 'APP_Q3', 'APP_Q4', 'Q1_NAR', 'Q2_NAR', 'Q3_NAR', 'Q4_NAR', 'PROJECT_WEIGHT', 'WEIGHTED_PROGRESS', 'CREATED_BY', 'CREATED_ON', 'UPDATED_BY', 'UPDATED_ON', ),
		BasePeer::TYPE_COLNAME => array (SiteReportsQARRPSPeer::ID, SiteReportsQARRPSPeer::QAR_ID, SiteReportsQARRPSPeer::PROJECT, SiteReportsQARRPSPeer::PROJECT_WAREHOUSE_ID, SiteReportsQARRPSPeer::NEESR_SHARED_USE_YEAR, SiteReportsQARRPSPeer::OFFICIAL_AWARD_NUMBER, SiteReportsQARRPSPeer::PROJECT_TITLE, SiteReportsQARRPSPeer::PROJECT_NUMBER, SiteReportsQARRPSPeer::PI_NAME, SiteReportsQARRPSPeer::INSTITUTION, SiteReportsQARRPSPeer::PPP_FY_START_PRG, SiteReportsQARRPSPeer::PPP_FY_END_PRG, SiteReportsQARRPSPeer::APP_Q1, SiteReportsQARRPSPeer::APP_Q2, SiteReportsQARRPSPeer::APP_Q3, SiteReportsQARRPSPeer::APP_Q4, SiteReportsQARRPSPeer::Q1_NAR, SiteReportsQARRPSPeer::Q2_NAR, SiteReportsQARRPSPeer::Q3_NAR, SiteReportsQARRPSPeer::Q4_NAR, SiteReportsQARRPSPeer::PROJECT_WEIGHT, SiteReportsQARRPSPeer::WEIGHTED_PROGRESS, SiteReportsQARRPSPeer::CREATED_BY, SiteReportsQARRPSPeer::CREATED_ON, SiteReportsQARRPSPeer::UPDATED_BY, SiteReportsQARRPSPeer::UPDATED_ON, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'QAR_ID', 'PROJECT', 'PROJECT_WAREHOUSE_ID', 'NEESR_SHARED_USE_YEAR', 'OFFICIAL_AWARD_NUMBER', 'PROJECT_TITLE', 'PROJECT_NUMBER', 'PI_NAME', 'INSTITUTION', 'PPP_FY_START_PRG', 'PPP_FY_END_PRG', 'APP_Q1', 'APP_Q2', 'APP_Q3', 'APP_Q4', 'Q1_NAR', 'Q2_NAR', 'Q3_NAR', 'Q4_NAR', 'PROJECT_WEIGHT', 'WEIGHTED_PROGRESS', 'CREATED_BY', 'CREATED_ON', 'UPDATED_BY', 'UPDATED_ON', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('ID' => 0, 'QAR_ID' => 1, 'PROJECT' => 2, 'PROJECT_WAREHOUSE_ID' => 3, 'NEESR_SHARED_USE_YEAR' => 4, 'OFFICIAL_AWARD_NUMBER' => 5, 'PROJECT_TITLE' => 6, 'PROJECT_NUMBER' => 7, 'PI_NAME' => 8, 'INSTITUTION' => 9, 'PPP_FY_START_PRG' => 10, 'PPP_FY_END_PRG' => 11, 'APP_Q1' => 12, 'APP_Q2' => 13, 'APP_Q3' => 14, 'APP_Q4' => 15, 'Q1_NAR' => 16, 'Q2_NAR' => 17, 'Q3_NAR' => 18, 'Q4_NAR' => 19, 'PROJECT_WEIGHT' => 20, 'WEIGHTED_PROGRESS' => 21, 'CREATED_BY' => 22, 'CREATED_ON' => 23, 'UPDATED_BY' => 24, 'UPDATED_ON' => 25, ),
		BasePeer::TYPE_COLNAME => array (SiteReportsQARRPSPeer::ID => 0, SiteReportsQARRPSPeer::QAR_ID => 1, SiteReportsQARRPSPeer::PROJECT => 2, SiteReportsQARRPSPeer::PROJECT_WAREHOUSE_ID => 3, SiteReportsQARRPSPeer::NEESR_SHARED_USE_YEAR => 4, SiteReportsQARRPSPeer::OFFICIAL_AWARD_NUMBER => 5, SiteReportsQARRPSPeer::PROJECT_TITLE => 6, SiteReportsQARRPSPeer::PROJECT_NUMBER => 7, SiteReportsQARRPSPeer::PI_NAME => 8, SiteReportsQARRPSPeer::INSTITUTION => 9, SiteReportsQARRPSPeer::PPP_FY_START_PRG => 10, SiteReportsQARRPSPeer::PPP_FY_END_PRG => 11, SiteReportsQARRPSPeer::APP_Q1 => 12, SiteReportsQARRPSPeer::APP_Q2 => 13, SiteReportsQARRPSPeer::APP_Q3 => 14, SiteReportsQARRPSPeer::APP_Q4 => 15, SiteReportsQARRPSPeer::Q1_NAR => 16, SiteReportsQARRPSPeer::Q2_NAR => 17, SiteReportsQARRPSPeer::Q3_NAR => 18, SiteReportsQARRPSPeer::Q4_NAR => 19, SiteReportsQARRPSPeer::PROJECT_WEIGHT => 20, SiteReportsQARRPSPeer::WEIGHTED_PROGRESS => 21, SiteReportsQARRPSPeer::CREATED_BY => 22, SiteReportsQARRPSPeer::CREATED_ON => 23, SiteReportsQARRPSPeer::UPDATED_BY => 24, SiteReportsQARRPSPeer::UPDATED_ON => 25, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'QAR_ID' => 1, 'PROJECT' => 2, 'PROJECT_WAREHOUSE_ID' => 3, 'NEESR_SHARED_USE_YEAR' => 4, 'OFFICIAL_AWARD_NUMBER' => 5, 'PROJECT_TITLE' => 6, 'PROJECT_NUMBER' => 7, 'PI_NAME' => 8, 'INSTITUTION' => 9, 'PPP_FY_START_PRG' => 10, 'PPP_FY_END_PRG' => 11, 'APP_Q1' => 12, 'APP_Q2' => 13, 'APP_Q3' => 14, 'APP_Q4' => 15, 'Q1_NAR' => 16, 'Q2_NAR' => 17, 'Q3_NAR' => 18, 'Q4_NAR' => 19, 'PROJECT_WEIGHT' => 20, 'WEIGHTED_PROGRESS' => 21, 'CREATED_BY' => 22, 'CREATED_ON' => 23, 'UPDATED_BY' => 24, 'UPDATED_ON' => 25, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/SiteReportsQARRPSMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.SiteReportsQARRPSMapBuilder');
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
			$map = SiteReportsQARRPSPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. SiteReportsQARRPSPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(SiteReportsQARRPSPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::ID);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::QAR_ID);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::PROJECT);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::PROJECT_WAREHOUSE_ID);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::NEESR_SHARED_USE_YEAR);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::OFFICIAL_AWARD_NUMBER);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::PROJECT_TITLE);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::PROJECT_NUMBER);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::PI_NAME);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::INSTITUTION);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::PPP_FY_START_PRG);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::PPP_FY_END_PRG);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::APP_Q1);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::APP_Q2);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::APP_Q3);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::APP_Q4);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::Q1_NAR);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::Q2_NAR);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::Q3_NAR);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::Q4_NAR);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::PROJECT_WEIGHT);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::WEIGHTED_PROGRESS);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::CREATED_BY);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::CREATED_ON);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::UPDATED_BY);

		$criteria->addSelectColumn(SiteReportsQARRPSPeer::UPDATED_ON);

	}

	const COUNT = 'COUNT(SITEREPORTS_QAR_RPS.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT SITEREPORTS_QAR_RPS.ID)';

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
			$criteria->addSelectColumn(SiteReportsQARRPSPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SiteReportsQARRPSPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = SiteReportsQARRPSPeer::doSelectRS($criteria, $con);
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
	 * @return     SiteReportsQARRPS
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = SiteReportsQARRPSPeer::doSelect($critcopy, $con);
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
		return SiteReportsQARRPSPeer::populateObjects(SiteReportsQARRPSPeer::doSelectRS($criteria, $con));
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
			SiteReportsQARRPSPeer::addSelectColumns($criteria);
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
		$cls = SiteReportsQARRPSPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related SiteReportsQAR table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinSiteReportsQAR(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SiteReportsQARRPSPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SiteReportsQARRPSPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SiteReportsQARRPSPeer::QAR_ID, SiteReportsQARPeer::ID);

		$rs = SiteReportsQARRPSPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SiteReportsQARRPS objects pre-filled with their SiteReportsQAR objects.
	 *
	 * @return     array Array of SiteReportsQARRPS objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinSiteReportsQAR(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SiteReportsQARRPSPeer::addSelectColumns($c);
		$startcol = (SiteReportsQARRPSPeer::NUM_COLUMNS - SiteReportsQARRPSPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		SiteReportsQARPeer::addSelectColumns($c);

		$c->addJoin(SiteReportsQARRPSPeer::QAR_ID, SiteReportsQARPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SiteReportsQARRPSPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SiteReportsQARPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getSiteReportsQAR(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addSiteReportsQARRPS($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initSiteReportsQARRPSs();
				$obj2->addSiteReportsQARRPS($obj1); //CHECKME
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
			$criteria->addSelectColumn(SiteReportsQARRPSPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SiteReportsQARRPSPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SiteReportsQARRPSPeer::QAR_ID, SiteReportsQARPeer::ID);

		$rs = SiteReportsQARRPSPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SiteReportsQARRPS objects pre-filled with all related objects.
	 *
	 * @return     array Array of SiteReportsQARRPS objects.
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

		SiteReportsQARRPSPeer::addSelectColumns($c);
		$startcol2 = (SiteReportsQARRPSPeer::NUM_COLUMNS - SiteReportsQARRPSPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SiteReportsQARPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SiteReportsQARPeer::NUM_COLUMNS;

		$c->addJoin(SiteReportsQARRPSPeer::QAR_ID, SiteReportsQARPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SiteReportsQARRPSPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined SiteReportsQAR rows
	
			$omClass = SiteReportsQARPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSiteReportsQAR(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSiteReportsQARRPS($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initSiteReportsQARRPSs();
				$obj2->addSiteReportsQARRPS($obj1);
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
		return SiteReportsQARRPSPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a SiteReportsQARRPS or Criteria object.
	 *
	 * @param      mixed $values Criteria or SiteReportsQARRPS object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from SiteReportsQARRPS object
		}

		$criteria->remove(SiteReportsQARRPSPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a SiteReportsQARRPS or Criteria object.
	 *
	 * @param      mixed $values Criteria or SiteReportsQARRPS object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(SiteReportsQARRPSPeer::ID);
			$selectCriteria->add(SiteReportsQARRPSPeer::ID, $criteria->remove(SiteReportsQARRPSPeer::ID), $comparison);

		} else { // $values is SiteReportsQARRPS object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the SITEREPORTS_QAR_RPS table.
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
			$affectedRows += BasePeer::doDeleteAll(SiteReportsQARRPSPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a SiteReportsQARRPS or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or SiteReportsQARRPS object or primary key or array of primary keys
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
			$con = Propel::getConnection(SiteReportsQARRPSPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof SiteReportsQARRPS) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(SiteReportsQARRPSPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given SiteReportsQARRPS object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      SiteReportsQARRPS $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(SiteReportsQARRPS $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(SiteReportsQARRPSPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(SiteReportsQARRPSPeer::TABLE_NAME);

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

		return BasePeer::doValidate(SiteReportsQARRPSPeer::DATABASE_NAME, SiteReportsQARRPSPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     SiteReportsQARRPS
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(SiteReportsQARRPSPeer::DATABASE_NAME);

		$criteria->add(SiteReportsQARRPSPeer::ID, $pk);


		$v = SiteReportsQARRPSPeer::doSelect($criteria, $con);

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
			$criteria->add(SiteReportsQARRPSPeer::ID, $pks, Criteria::IN);
			$objs = SiteReportsQARRPSPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseSiteReportsQARRPSPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseSiteReportsQARRPSPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/SiteReportsQARRPSMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.SiteReportsQARRPSMapBuilder');
}
