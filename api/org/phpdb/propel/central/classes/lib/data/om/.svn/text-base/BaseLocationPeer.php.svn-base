<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by LocationPeer::getOMClass()
include_once 'lib/data/Location.php';

/**
 * Base static class for performing query and update operations on the 'LOCATION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseLocationPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'LOCATION';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.Location';

	/** The total number of columns. */
	const NUM_COLUMNS = 20;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'LOCATION.ID';

	/** the column name for the COMMENTS field */
	const COMMENTS = 'LOCATION.COMMENTS';

	/** the column name for the COORDINATE_SPACE_ID field */
	const COORDINATE_SPACE_ID = 'LOCATION.COORDINATE_SPACE_ID';

	/** the column name for the I field */
	const I = 'LOCATION.I';

	/** the column name for the I_UNIT field */
	const I_UNIT = 'LOCATION.I_UNIT';

	/** the column name for the J field */
	const J = 'LOCATION.J';

	/** the column name for the J_UNIT field */
	const J_UNIT = 'LOCATION.J_UNIT';

	/** the column name for the K field */
	const K = 'LOCATION.K';

	/** the column name for the K_UNIT field */
	const K_UNIT = 'LOCATION.K_UNIT';

	/** the column name for the LABEL field */
	const LABEL = 'LOCATION.LABEL';

	/** the column name for the LOCATION_TYPE_ID field */
	const LOCATION_TYPE_ID = 'LOCATION.LOCATION_TYPE_ID';

	/** the column name for the PLAN_ID field */
	const PLAN_ID = 'LOCATION.PLAN_ID';

	/** the column name for the SENSOR_TYPE_ID field */
	const SENSOR_TYPE_ID = 'LOCATION.SENSOR_TYPE_ID';

	/** the column name for the SOURCE_TYPE_ID field */
	const SOURCE_TYPE_ID = 'LOCATION.SOURCE_TYPE_ID';

	/** the column name for the X field */
	const X = 'LOCATION.X';

	/** the column name for the X_UNIT field */
	const X_UNIT = 'LOCATION.X_UNIT';

	/** the column name for the Y field */
	const Y = 'LOCATION.Y';

	/** the column name for the Y_UNIT field */
	const Y_UNIT = 'LOCATION.Y_UNIT';

	/** the column name for the Z field */
	const Z = 'LOCATION.Z';

	/** the column name for the Z_UNIT field */
	const Z_UNIT = 'LOCATION.Z_UNIT';

	/** A key representing a particular subclass */
	const CLASSKEY_0 = '0';

	/** A key representing a particular subclass */
	const CLASSKEY_LOCATION = '0';

	/** A class that can be returned by this peer. */
	const CLASSNAME_0 = 'lib.data.Location';

	/** A key representing a particular subclass */
	const CLASSKEY_1 = '1';

	/** A key representing a particular subclass */
	const CLASSKEY_SENSORLOCATION = '1';

	/** A class that can be returned by this peer. */
	const CLASSNAME_1 = 'lib.data.SensorLocation';

	/** A key representing a particular subclass */
	const CLASSKEY_2 = '2';

	/** A key representing a particular subclass */
	const CLASSKEY_SOURCELOCATION = '2';

	/** A class that can be returned by this peer. */
	const CLASSNAME_2 = 'lib.data.SourceLocation';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Comment', 'CoordinateSpaceId', 'I', 'IUnit', 'J', 'JUnit', 'K', 'KUnit', 'Label', 'LocationTypeId', 'PlanId', 'SensorTypeId', 'SourceTypeId', 'X', 'XUnit', 'Y', 'YUnit', 'Z', 'ZUnit', ),
		BasePeer::TYPE_COLNAME => array (LocationPeer::ID, LocationPeer::COMMENTS, LocationPeer::COORDINATE_SPACE_ID, LocationPeer::I, LocationPeer::I_UNIT, LocationPeer::J, LocationPeer::J_UNIT, LocationPeer::K, LocationPeer::K_UNIT, LocationPeer::LABEL, LocationPeer::LOCATION_TYPE_ID, LocationPeer::PLAN_ID, LocationPeer::SENSOR_TYPE_ID, LocationPeer::SOURCE_TYPE_ID, LocationPeer::X, LocationPeer::X_UNIT, LocationPeer::Y, LocationPeer::Y_UNIT, LocationPeer::Z, LocationPeer::Z_UNIT, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'COMMENTS', 'COORDINATE_SPACE_ID', 'I', 'I_UNIT', 'J', 'J_UNIT', 'K', 'K_UNIT', 'LABEL', 'LOCATION_TYPE_ID', 'PLAN_ID', 'SENSOR_TYPE_ID', 'SOURCE_TYPE_ID', 'X', 'X_UNIT', 'Y', 'Y_UNIT', 'Z', 'Z_UNIT', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Comment' => 1, 'CoordinateSpaceId' => 2, 'I' => 3, 'IUnit' => 4, 'J' => 5, 'JUnit' => 6, 'K' => 7, 'KUnit' => 8, 'Label' => 9, 'LocationTypeId' => 10, 'PlanId' => 11, 'SensorTypeId' => 12, 'SourceTypeId' => 13, 'X' => 14, 'XUnit' => 15, 'Y' => 16, 'YUnit' => 17, 'Z' => 18, 'ZUnit' => 19, ),
		BasePeer::TYPE_COLNAME => array (LocationPeer::ID => 0, LocationPeer::COMMENTS => 1, LocationPeer::COORDINATE_SPACE_ID => 2, LocationPeer::I => 3, LocationPeer::I_UNIT => 4, LocationPeer::J => 5, LocationPeer::J_UNIT => 6, LocationPeer::K => 7, LocationPeer::K_UNIT => 8, LocationPeer::LABEL => 9, LocationPeer::LOCATION_TYPE_ID => 10, LocationPeer::PLAN_ID => 11, LocationPeer::SENSOR_TYPE_ID => 12, LocationPeer::SOURCE_TYPE_ID => 13, LocationPeer::X => 14, LocationPeer::X_UNIT => 15, LocationPeer::Y => 16, LocationPeer::Y_UNIT => 17, LocationPeer::Z => 18, LocationPeer::Z_UNIT => 19, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'COMMENTS' => 1, 'COORDINATE_SPACE_ID' => 2, 'I' => 3, 'I_UNIT' => 4, 'J' => 5, 'J_UNIT' => 6, 'K' => 7, 'K_UNIT' => 8, 'LABEL' => 9, 'LOCATION_TYPE_ID' => 10, 'PLAN_ID' => 11, 'SENSOR_TYPE_ID' => 12, 'SOURCE_TYPE_ID' => 13, 'X' => 14, 'X_UNIT' => 15, 'Y' => 16, 'Y_UNIT' => 17, 'Z' => 18, 'Z_UNIT' => 19, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/LocationMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.LocationMapBuilder');
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
			$map = LocationPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. LocationPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(LocationPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(LocationPeer::ID);

		$criteria->addSelectColumn(LocationPeer::COMMENTS);

		$criteria->addSelectColumn(LocationPeer::COORDINATE_SPACE_ID);

		$criteria->addSelectColumn(LocationPeer::I);

		$criteria->addSelectColumn(LocationPeer::I_UNIT);

		$criteria->addSelectColumn(LocationPeer::J);

		$criteria->addSelectColumn(LocationPeer::J_UNIT);

		$criteria->addSelectColumn(LocationPeer::K);

		$criteria->addSelectColumn(LocationPeer::K_UNIT);

		$criteria->addSelectColumn(LocationPeer::LABEL);

		$criteria->addSelectColumn(LocationPeer::LOCATION_TYPE_ID);

		$criteria->addSelectColumn(LocationPeer::PLAN_ID);

		$criteria->addSelectColumn(LocationPeer::SENSOR_TYPE_ID);

		$criteria->addSelectColumn(LocationPeer::SOURCE_TYPE_ID);

		$criteria->addSelectColumn(LocationPeer::X);

		$criteria->addSelectColumn(LocationPeer::X_UNIT);

		$criteria->addSelectColumn(LocationPeer::Y);

		$criteria->addSelectColumn(LocationPeer::Y_UNIT);

		$criteria->addSelectColumn(LocationPeer::Z);

		$criteria->addSelectColumn(LocationPeer::Z_UNIT);

	}

	const COUNT = 'COUNT(LOCATION.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT LOCATION.ID)';

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
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = LocationPeer::doSelectRS($criteria, $con);
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
	 * @return     Location
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = LocationPeer::doSelect($critcopy, $con);
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
		return LocationPeer::populateObjects(LocationPeer::doSelectRS($criteria, $con));
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
			LocationPeer::addSelectColumns($criteria);
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
	
		// populate the object(s)
		while($rs->next()) {
		
			// class must be set each time from the record row
			$cls = Propel::import(LocationPeer::getOMClass($rs, 1));
			$obj = new $cls();
			$obj->hydrate($rs);
			$results[] = $obj;
			
		}
		return $results;
	}

	/**
	 * Returns the number of rows matching criteria, joining the related CoordinateSpace table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCoordinateSpace(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related LocationPlan table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinLocationPlan(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByJUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByJUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByYUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByYUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByXUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByXUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByIUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByIUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByZUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByZUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByKUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByKUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related SensorType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinSensorType(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related SourceType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinSourceType(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Location objects pre-filled with their CoordinateSpace objects.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCoordinateSpace(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		CoordinateSpacePeer::addSelectColumns($c);

		$c->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getCoordinateSpace(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addLocation($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with their LocationPlan objects.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinLocationPlan(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		LocationPlanPeer::addSelectColumns($c);

		$c->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = LocationPlanPeer::getOMClass($rs, $startcol);

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getLocationPlan(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addLocation($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByJUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByJUnit(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addLocationRelatedByJUnit($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initLocationsRelatedByJUnit();
				$obj2->addLocationRelatedByJUnit($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByYUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByYUnit(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addLocationRelatedByYUnit($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initLocationsRelatedByYUnit();
				$obj2->addLocationRelatedByYUnit($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByXUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByXUnit(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addLocationRelatedByXUnit($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initLocationsRelatedByXUnit();
				$obj2->addLocationRelatedByXUnit($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByIUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByIUnit(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addLocationRelatedByIUnit($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initLocationsRelatedByIUnit();
				$obj2->addLocationRelatedByIUnit($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByZUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByZUnit(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addLocationRelatedByZUnit($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initLocationsRelatedByZUnit();
				$obj2->addLocationRelatedByZUnit($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByKUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByKUnit(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addLocationRelatedByKUnit($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initLocationsRelatedByKUnit();
				$obj2->addLocationRelatedByKUnit($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with their SensorType objects.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinSensorType(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		SensorTypePeer::addSelectColumns($c);

		$c->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SensorTypePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addLocation($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with their SourceType objects.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinSourceType(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		SourceTypePeer::addSelectColumns($c);

		$c->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SourceTypePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getSourceType(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addLocation($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1); //CHECKME
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
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$criteria->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$criteria->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$criteria->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Location objects pre-filled with all related objects.
	 *
	 * @return     array Array of Location objects.
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

		LocationPeer::addSelectColumns($c);
		$startcol2 = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSpacePeer::NUM_COLUMNS;

		LocationPlanPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + LocationPlanPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol8 = $startcol7 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol9 = $startcol8 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol10 = $startcol9 + MeasurementUnitPeer::NUM_COLUMNS;

		SensorTypePeer::addSelectColumns($c);
		$startcol11 = $startcol10 + SensorTypePeer::NUM_COLUMNS;

		SourceTypePeer::addSelectColumns($c);
		$startcol12 = $startcol11 + SourceTypePeer::NUM_COLUMNS;

		$c->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$c->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$c->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$c->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined CoordinateSpace rows
	
			$omClass = CoordinateSpacePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSpace(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addLocation($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1);
			}


				// Add objects for joined LocationPlan rows
	
			$omClass = LocationPlanPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getLocationPlan(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addLocation($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initLocations();
				$obj3->addLocation($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnitRelatedByJUnit(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addLocationRelatedByJUnit($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initLocationsRelatedByJUnit();
				$obj4->addLocationRelatedByJUnit($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5 = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnitRelatedByYUnit(); // CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addLocationRelatedByYUnit($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj5->initLocationsRelatedByYUnit();
				$obj5->addLocationRelatedByYUnit($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj6 = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getMeasurementUnitRelatedByXUnit(); // CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addLocationRelatedByXUnit($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj6->initLocationsRelatedByXUnit();
				$obj6->addLocationRelatedByXUnit($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj7 = new $cls();
			$obj7->hydrate($rs, $startcol7);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj7 = $temp_obj1->getMeasurementUnitRelatedByIUnit(); // CHECKME
				if ($temp_obj7->getPrimaryKey() === $obj7->getPrimaryKey()) {
					$newObject = false;
					$temp_obj7->addLocationRelatedByIUnit($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj7->initLocationsRelatedByIUnit();
				$obj7->addLocationRelatedByIUnit($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj8 = new $cls();
			$obj8->hydrate($rs, $startcol8);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj8 = $temp_obj1->getMeasurementUnitRelatedByZUnit(); // CHECKME
				if ($temp_obj8->getPrimaryKey() === $obj8->getPrimaryKey()) {
					$newObject = false;
					$temp_obj8->addLocationRelatedByZUnit($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj8->initLocationsRelatedByZUnit();
				$obj8->addLocationRelatedByZUnit($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj9 = new $cls();
			$obj9->hydrate($rs, $startcol9);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj9 = $temp_obj1->getMeasurementUnitRelatedByKUnit(); // CHECKME
				if ($temp_obj9->getPrimaryKey() === $obj9->getPrimaryKey()) {
					$newObject = false;
					$temp_obj9->addLocationRelatedByKUnit($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj9->initLocationsRelatedByKUnit();
				$obj9->addLocationRelatedByKUnit($obj1);
			}


				// Add objects for joined SensorType rows
	
			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj10 = new $cls();
			$obj10->hydrate($rs, $startcol10);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj10 = $temp_obj1->getSensorType(); // CHECKME
				if ($temp_obj10->getPrimaryKey() === $obj10->getPrimaryKey()) {
					$newObject = false;
					$temp_obj10->addLocation($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj10->initLocations();
				$obj10->addLocation($obj1);
			}


				// Add objects for joined SourceType rows
	
			$omClass = SourceTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj11 = new $cls();
			$obj11->hydrate($rs, $startcol11);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj11 = $temp_obj1->getSourceType(); // CHECKME
				if ($temp_obj11->getPrimaryKey() === $obj11->getPrimaryKey()) {
					$newObject = false;
					$temp_obj11->addLocation($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj11->initLocations();
				$obj11->addLocation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CoordinateSpace table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCoordinateSpace(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$criteria->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$criteria->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related LocationPlan table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptLocationPlan(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$criteria->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$criteria->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByJUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByJUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$criteria->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$criteria->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$criteria->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByYUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByYUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$criteria->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$criteria->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$criteria->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByXUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByXUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$criteria->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$criteria->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$criteria->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByIUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByIUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$criteria->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$criteria->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$criteria->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByZUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByZUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$criteria->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$criteria->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$criteria->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByKUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByKUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$criteria->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$criteria->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$criteria->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related SensorType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptSensorType(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$criteria->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$criteria->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related SourceType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptSourceType(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(LocationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(LocationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$criteria->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$criteria->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$rs = LocationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Location objects pre-filled with all related objects except CoordinateSpace.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCoordinateSpace(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol2 = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		LocationPlanPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + LocationPlanPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol8 = $startcol7 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol9 = $startcol8 + MeasurementUnitPeer::NUM_COLUMNS;

		SensorTypePeer::addSelectColumns($c);
		$startcol10 = $startcol9 + SensorTypePeer::NUM_COLUMNS;

		SourceTypePeer::addSelectColumns($c);
		$startcol11 = $startcol10 + SourceTypePeer::NUM_COLUMNS;

		$c->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$c->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$c->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = LocationPlanPeer::getOMClass($rs, $startcol2);


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getLocationPlan(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMeasurementUnitRelatedByJUnit(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addLocationRelatedByJUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initLocationsRelatedByJUnit();
				$obj3->addLocationRelatedByJUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnitRelatedByYUnit(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addLocationRelatedByYUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initLocationsRelatedByYUnit();
				$obj4->addLocationRelatedByYUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnitRelatedByXUnit(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addLocationRelatedByXUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initLocationsRelatedByXUnit();
				$obj5->addLocationRelatedByXUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj6  = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getMeasurementUnitRelatedByIUnit(); //CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addLocationRelatedByIUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj6->initLocationsRelatedByIUnit();
				$obj6->addLocationRelatedByIUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj7  = new $cls();
			$obj7->hydrate($rs, $startcol7);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj7 = $temp_obj1->getMeasurementUnitRelatedByZUnit(); //CHECKME
				if ($temp_obj7->getPrimaryKey() === $obj7->getPrimaryKey()) {
					$newObject = false;
					$temp_obj7->addLocationRelatedByZUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj7->initLocationsRelatedByZUnit();
				$obj7->addLocationRelatedByZUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj8  = new $cls();
			$obj8->hydrate($rs, $startcol8);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj8 = $temp_obj1->getMeasurementUnitRelatedByKUnit(); //CHECKME
				if ($temp_obj8->getPrimaryKey() === $obj8->getPrimaryKey()) {
					$newObject = false;
					$temp_obj8->addLocationRelatedByKUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj8->initLocationsRelatedByKUnit();
				$obj8->addLocationRelatedByKUnit($obj1);
			}

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj9  = new $cls();
			$obj9->hydrate($rs, $startcol9);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj9 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj9->getPrimaryKey() === $obj9->getPrimaryKey()) {
					$newObject = false;
					$temp_obj9->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj9->initLocations();
				$obj9->addLocation($obj1);
			}

			$omClass = SourceTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj10  = new $cls();
			$obj10->hydrate($rs, $startcol10);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj10 = $temp_obj1->getSourceType(); //CHECKME
				if ($temp_obj10->getPrimaryKey() === $obj10->getPrimaryKey()) {
					$newObject = false;
					$temp_obj10->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj10->initLocations();
				$obj10->addLocation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with all related objects except LocationPlan.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptLocationPlan(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol2 = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSpacePeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol8 = $startcol7 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol9 = $startcol8 + MeasurementUnitPeer::NUM_COLUMNS;

		SensorTypePeer::addSelectColumns($c);
		$startcol10 = $startcol9 + SensorTypePeer::NUM_COLUMNS;

		SourceTypePeer::addSelectColumns($c);
		$startcol11 = $startcol10 + SourceTypePeer::NUM_COLUMNS;

		$c->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$c->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$c->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSpacePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSpace(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMeasurementUnitRelatedByJUnit(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addLocationRelatedByJUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initLocationsRelatedByJUnit();
				$obj3->addLocationRelatedByJUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnitRelatedByYUnit(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addLocationRelatedByYUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initLocationsRelatedByYUnit();
				$obj4->addLocationRelatedByYUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnitRelatedByXUnit(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addLocationRelatedByXUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initLocationsRelatedByXUnit();
				$obj5->addLocationRelatedByXUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj6  = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getMeasurementUnitRelatedByIUnit(); //CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addLocationRelatedByIUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj6->initLocationsRelatedByIUnit();
				$obj6->addLocationRelatedByIUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj7  = new $cls();
			$obj7->hydrate($rs, $startcol7);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj7 = $temp_obj1->getMeasurementUnitRelatedByZUnit(); //CHECKME
				if ($temp_obj7->getPrimaryKey() === $obj7->getPrimaryKey()) {
					$newObject = false;
					$temp_obj7->addLocationRelatedByZUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj7->initLocationsRelatedByZUnit();
				$obj7->addLocationRelatedByZUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj8  = new $cls();
			$obj8->hydrate($rs, $startcol8);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj8 = $temp_obj1->getMeasurementUnitRelatedByKUnit(); //CHECKME
				if ($temp_obj8->getPrimaryKey() === $obj8->getPrimaryKey()) {
					$newObject = false;
					$temp_obj8->addLocationRelatedByKUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj8->initLocationsRelatedByKUnit();
				$obj8->addLocationRelatedByKUnit($obj1);
			}

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj9  = new $cls();
			$obj9->hydrate($rs, $startcol9);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj9 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj9->getPrimaryKey() === $obj9->getPrimaryKey()) {
					$newObject = false;
					$temp_obj9->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj9->initLocations();
				$obj9->addLocation($obj1);
			}

			$omClass = SourceTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj10  = new $cls();
			$obj10->hydrate($rs, $startcol10);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj10 = $temp_obj1->getSourceType(); //CHECKME
				if ($temp_obj10->getPrimaryKey() === $obj10->getPrimaryKey()) {
					$newObject = false;
					$temp_obj10->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj10->initLocations();
				$obj10->addLocation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with all related objects except MeasurementUnitRelatedByJUnit.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByJUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol2 = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSpacePeer::NUM_COLUMNS;

		LocationPlanPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + LocationPlanPeer::NUM_COLUMNS;

		SensorTypePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + SensorTypePeer::NUM_COLUMNS;

		SourceTypePeer::addSelectColumns($c);
		$startcol6 = $startcol5 + SourceTypePeer::NUM_COLUMNS;

		$c->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$c->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$c->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$c->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSpacePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSpace(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1);
			}

			$omClass = LocationPlanPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getLocationPlan(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initLocations();
				$obj3->addLocation($obj1);
			}

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initLocations();
				$obj4->addLocation($obj1);
			}

			$omClass = SourceTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getSourceType(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initLocations();
				$obj5->addLocation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with all related objects except MeasurementUnitRelatedByYUnit.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByYUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol2 = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSpacePeer::NUM_COLUMNS;

		LocationPlanPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + LocationPlanPeer::NUM_COLUMNS;

		SensorTypePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + SensorTypePeer::NUM_COLUMNS;

		SourceTypePeer::addSelectColumns($c);
		$startcol6 = $startcol5 + SourceTypePeer::NUM_COLUMNS;

		$c->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$c->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$c->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$c->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSpacePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSpace(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1);
			}

			$omClass = LocationPlanPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getLocationPlan(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initLocations();
				$obj3->addLocation($obj1);
			}

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initLocations();
				$obj4->addLocation($obj1);
			}

			$omClass = SourceTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getSourceType(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initLocations();
				$obj5->addLocation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with all related objects except MeasurementUnitRelatedByXUnit.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByXUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol2 = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSpacePeer::NUM_COLUMNS;

		LocationPlanPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + LocationPlanPeer::NUM_COLUMNS;

		SensorTypePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + SensorTypePeer::NUM_COLUMNS;

		SourceTypePeer::addSelectColumns($c);
		$startcol6 = $startcol5 + SourceTypePeer::NUM_COLUMNS;

		$c->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$c->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$c->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$c->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSpacePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSpace(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1);
			}

			$omClass = LocationPlanPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getLocationPlan(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initLocations();
				$obj3->addLocation($obj1);
			}

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initLocations();
				$obj4->addLocation($obj1);
			}

			$omClass = SourceTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getSourceType(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initLocations();
				$obj5->addLocation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with all related objects except MeasurementUnitRelatedByIUnit.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByIUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol2 = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSpacePeer::NUM_COLUMNS;

		LocationPlanPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + LocationPlanPeer::NUM_COLUMNS;

		SensorTypePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + SensorTypePeer::NUM_COLUMNS;

		SourceTypePeer::addSelectColumns($c);
		$startcol6 = $startcol5 + SourceTypePeer::NUM_COLUMNS;

		$c->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$c->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$c->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$c->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSpacePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSpace(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1);
			}

			$omClass = LocationPlanPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getLocationPlan(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initLocations();
				$obj3->addLocation($obj1);
			}

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initLocations();
				$obj4->addLocation($obj1);
			}

			$omClass = SourceTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getSourceType(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initLocations();
				$obj5->addLocation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with all related objects except MeasurementUnitRelatedByZUnit.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByZUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol2 = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSpacePeer::NUM_COLUMNS;

		LocationPlanPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + LocationPlanPeer::NUM_COLUMNS;

		SensorTypePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + SensorTypePeer::NUM_COLUMNS;

		SourceTypePeer::addSelectColumns($c);
		$startcol6 = $startcol5 + SourceTypePeer::NUM_COLUMNS;

		$c->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$c->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$c->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$c->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSpacePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSpace(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1);
			}

			$omClass = LocationPlanPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getLocationPlan(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initLocations();
				$obj3->addLocation($obj1);
			}

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initLocations();
				$obj4->addLocation($obj1);
			}

			$omClass = SourceTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getSourceType(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initLocations();
				$obj5->addLocation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with all related objects except MeasurementUnitRelatedByKUnit.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByKUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol2 = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSpacePeer::NUM_COLUMNS;

		LocationPlanPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + LocationPlanPeer::NUM_COLUMNS;

		SensorTypePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + SensorTypePeer::NUM_COLUMNS;

		SourceTypePeer::addSelectColumns($c);
		$startcol6 = $startcol5 + SourceTypePeer::NUM_COLUMNS;

		$c->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$c->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$c->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$c->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSpacePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSpace(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1);
			}

			$omClass = LocationPlanPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getLocationPlan(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initLocations();
				$obj3->addLocation($obj1);
			}

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initLocations();
				$obj4->addLocation($obj1);
			}

			$omClass = SourceTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getSourceType(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initLocations();
				$obj5->addLocation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with all related objects except SensorType.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptSensorType(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol2 = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSpacePeer::NUM_COLUMNS;

		LocationPlanPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + LocationPlanPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol8 = $startcol7 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol9 = $startcol8 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol10 = $startcol9 + MeasurementUnitPeer::NUM_COLUMNS;

		SourceTypePeer::addSelectColumns($c);
		$startcol11 = $startcol10 + SourceTypePeer::NUM_COLUMNS;

		$c->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$c->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$c->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::SOURCE_TYPE_ID, SourceTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSpacePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSpace(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1);
			}

			$omClass = LocationPlanPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getLocationPlan(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initLocations();
				$obj3->addLocation($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnitRelatedByJUnit(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addLocationRelatedByJUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initLocationsRelatedByJUnit();
				$obj4->addLocationRelatedByJUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnitRelatedByYUnit(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addLocationRelatedByYUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initLocationsRelatedByYUnit();
				$obj5->addLocationRelatedByYUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj6  = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getMeasurementUnitRelatedByXUnit(); //CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addLocationRelatedByXUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj6->initLocationsRelatedByXUnit();
				$obj6->addLocationRelatedByXUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj7  = new $cls();
			$obj7->hydrate($rs, $startcol7);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj7 = $temp_obj1->getMeasurementUnitRelatedByIUnit(); //CHECKME
				if ($temp_obj7->getPrimaryKey() === $obj7->getPrimaryKey()) {
					$newObject = false;
					$temp_obj7->addLocationRelatedByIUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj7->initLocationsRelatedByIUnit();
				$obj7->addLocationRelatedByIUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj8  = new $cls();
			$obj8->hydrate($rs, $startcol8);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj8 = $temp_obj1->getMeasurementUnitRelatedByZUnit(); //CHECKME
				if ($temp_obj8->getPrimaryKey() === $obj8->getPrimaryKey()) {
					$newObject = false;
					$temp_obj8->addLocationRelatedByZUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj8->initLocationsRelatedByZUnit();
				$obj8->addLocationRelatedByZUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj9  = new $cls();
			$obj9->hydrate($rs, $startcol9);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj9 = $temp_obj1->getMeasurementUnitRelatedByKUnit(); //CHECKME
				if ($temp_obj9->getPrimaryKey() === $obj9->getPrimaryKey()) {
					$newObject = false;
					$temp_obj9->addLocationRelatedByKUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj9->initLocationsRelatedByKUnit();
				$obj9->addLocationRelatedByKUnit($obj1);
			}

			$omClass = SourceTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj10  = new $cls();
			$obj10->hydrate($rs, $startcol10);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj10 = $temp_obj1->getSourceType(); //CHECKME
				if ($temp_obj10->getPrimaryKey() === $obj10->getPrimaryKey()) {
					$newObject = false;
					$temp_obj10->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj10->initLocations();
				$obj10->addLocation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Location objects pre-filled with all related objects except SourceType.
	 *
	 * @return     array Array of Location objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptSourceType(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		LocationPeer::addSelectColumns($c);
		$startcol2 = (LocationPeer::NUM_COLUMNS - LocationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSpacePeer::NUM_COLUMNS;

		LocationPlanPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + LocationPlanPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol8 = $startcol7 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol9 = $startcol8 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol10 = $startcol9 + MeasurementUnitPeer::NUM_COLUMNS;

		SensorTypePeer::addSelectColumns($c);
		$startcol11 = $startcol10 + SensorTypePeer::NUM_COLUMNS;

		$c->addJoin(LocationPeer::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);

		$c->addJoin(LocationPeer::PLAN_ID, LocationPlanPeer::ID);

		$c->addJoin(LocationPeer::J_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::Y_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::X_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::I_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::Z_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::K_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(LocationPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = LocationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSpacePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSpace(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initLocations();
				$obj2->addLocation($obj1);
			}

			$omClass = LocationPlanPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getLocationPlan(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initLocations();
				$obj3->addLocation($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnitRelatedByJUnit(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addLocationRelatedByJUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initLocationsRelatedByJUnit();
				$obj4->addLocationRelatedByJUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnitRelatedByYUnit(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addLocationRelatedByYUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initLocationsRelatedByYUnit();
				$obj5->addLocationRelatedByYUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj6  = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getMeasurementUnitRelatedByXUnit(); //CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addLocationRelatedByXUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj6->initLocationsRelatedByXUnit();
				$obj6->addLocationRelatedByXUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj7  = new $cls();
			$obj7->hydrate($rs, $startcol7);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj7 = $temp_obj1->getMeasurementUnitRelatedByIUnit(); //CHECKME
				if ($temp_obj7->getPrimaryKey() === $obj7->getPrimaryKey()) {
					$newObject = false;
					$temp_obj7->addLocationRelatedByIUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj7->initLocationsRelatedByIUnit();
				$obj7->addLocationRelatedByIUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj8  = new $cls();
			$obj8->hydrate($rs, $startcol8);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj8 = $temp_obj1->getMeasurementUnitRelatedByZUnit(); //CHECKME
				if ($temp_obj8->getPrimaryKey() === $obj8->getPrimaryKey()) {
					$newObject = false;
					$temp_obj8->addLocationRelatedByZUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj8->initLocationsRelatedByZUnit();
				$obj8->addLocationRelatedByZUnit($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj9  = new $cls();
			$obj9->hydrate($rs, $startcol9);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj9 = $temp_obj1->getMeasurementUnitRelatedByKUnit(); //CHECKME
				if ($temp_obj9->getPrimaryKey() === $obj9->getPrimaryKey()) {
					$newObject = false;
					$temp_obj9->addLocationRelatedByKUnit($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj9->initLocationsRelatedByKUnit();
				$obj9->addLocationRelatedByKUnit($obj1);
			}

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj10  = new $cls();
			$obj10->hydrate($rs, $startcol10);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj10 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj10->getPrimaryKey() === $obj10->getPrimaryKey()) {
					$newObject = false;
					$temp_obj10->addLocation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj10->initLocations();
				$obj10->addLocation($obj1);
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
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      ResultSet $rs ResultSet with pointer to record containing om class.
	 * @param      int $colnum Column to examine for OM class information (first is 1).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass(ResultSet $rs, $colnum)
	{
		try {

			$omClass = null;
			$classKey = $rs->getString($colnum - 1 + 11);

			switch($classKey) {

				case self::CLASSKEY_0:
					$omClass = self::CLASSNAME_0;
					break;

				case self::CLASSKEY_1:
					$omClass = self::CLASSNAME_1;
					break;

				case self::CLASSKEY_2:
					$omClass = self::CLASSNAME_2;
					break;

				default:
					$omClass = self::CLASS_DEFAULT;

			} // switch

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a Location or Criteria object.
	 *
	 * @param      mixed $values Criteria or Location object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from Location object
		}

		$criteria->remove(LocationPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a Location or Criteria object.
	 *
	 * @param      mixed $values Criteria or Location object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(LocationPeer::ID);
			$selectCriteria->add(LocationPeer::ID, $criteria->remove(LocationPeer::ID), $comparison);

		} else { // $values is Location object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the LOCATION table.
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
			$affectedRows += BasePeer::doDeleteAll(LocationPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a Location or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Location object or primary key or array of primary keys
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
			$con = Propel::getConnection(LocationPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof Location) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(LocationPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given Location object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Location $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(Location $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(LocationPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(LocationPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::COMMENTS))
			$columns[LocationPeer::COMMENTS] = $obj->getComment();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::COORDINATE_SPACE_ID))
			$columns[LocationPeer::COORDINATE_SPACE_ID] = $obj->getCoordinateSpaceId();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::I_UNIT))
			$columns[LocationPeer::I_UNIT] = $obj->getIUnit();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::J_UNIT))
			$columns[LocationPeer::J_UNIT] = $obj->getJUnit();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::K_UNIT))
			$columns[LocationPeer::K_UNIT] = $obj->getKUnit();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::LABEL))
			$columns[LocationPeer::LABEL] = $obj->getLabel();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::LOCATION_TYPE_ID))
			$columns[LocationPeer::LOCATION_TYPE_ID] = $obj->getLocationTypeId();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::PLAN_ID))
			$columns[LocationPeer::PLAN_ID] = $obj->getPlanId();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::X))
			$columns[LocationPeer::X] = $obj->getX();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::X_UNIT))
			$columns[LocationPeer::X_UNIT] = $obj->getXUnit();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::Y))
			$columns[LocationPeer::Y] = $obj->getY();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::Y_UNIT))
			$columns[LocationPeer::Y_UNIT] = $obj->getYUnit();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::Z))
			$columns[LocationPeer::Z] = $obj->getZ();

		if ($obj->isNew() || $obj->isColumnModified(LocationPeer::Z_UNIT))
			$columns[LocationPeer::Z_UNIT] = $obj->getZUnit();

		}

		return BasePeer::doValidate(LocationPeer::DATABASE_NAME, LocationPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     Location
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(LocationPeer::DATABASE_NAME);

		$criteria->add(LocationPeer::ID, $pk);


		$v = LocationPeer::doSelect($criteria, $con);

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
			$criteria->add(LocationPeer::ID, $pks, Criteria::IN);
			$objs = LocationPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseLocationPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseLocationPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/LocationMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.LocationMapBuilder');
}
