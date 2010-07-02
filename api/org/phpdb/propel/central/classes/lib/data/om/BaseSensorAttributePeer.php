<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by SensorAttributePeer::getOMClass()
include_once 'lib/data/SensorAttribute.php';

/**
 * Base static class for performing query and update operations on the 'SENSOR_ATTRIBUTE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSensorAttributePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'SENSOR_ATTRIBUTE';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.SensorAttribute';

	/** The total number of columns. */
	const NUM_COLUMNS = 11;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'SENSOR_ATTRIBUTE.ID';

	/** the column name for the ATTRIBUTE_ID field */
	const ATTRIBUTE_ID = 'SENSOR_ATTRIBUTE.ATTRIBUTE_ID';

	/** the column name for the DATE_VALUE field */
	const DATE_VALUE = 'SENSOR_ATTRIBUTE.DATE_VALUE';

	/** the column name for the GROUP_VALUE field */
	const GROUP_VALUE = 'SENSOR_ATTRIBUTE.GROUP_VALUE';

	/** the column name for the INT_VALUE field */
	const INT_VALUE = 'SENSOR_ATTRIBUTE.INT_VALUE';

	/** the column name for the NOTE field */
	const NOTE = 'SENSOR_ATTRIBUTE.NOTE';

	/** the column name for the NUM_VALUE field */
	const NUM_VALUE = 'SENSOR_ATTRIBUTE.NUM_VALUE';

	/** the column name for the PAGE_COUNT field */
	const PAGE_COUNT = 'SENSOR_ATTRIBUTE.PAGE_COUNT';

	/** the column name for the SENSOR_ID field */
	const SENSOR_ID = 'SENSOR_ATTRIBUTE.SENSOR_ID';

	/** the column name for the STRING_VALUE field */
	const STRING_VALUE = 'SENSOR_ATTRIBUTE.STRING_VALUE';

	/** the column name for the UNIT_ID field */
	const UNIT_ID = 'SENSOR_ATTRIBUTE.UNIT_ID';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'AttributeId', 'DateValue', 'GroupValue', 'IntValue', 'Note', 'NumValue', 'PageCount', 'SensorId', 'StringValue', 'UnitId', ),
		BasePeer::TYPE_COLNAME => array (SensorAttributePeer::ID, SensorAttributePeer::ATTRIBUTE_ID, SensorAttributePeer::DATE_VALUE, SensorAttributePeer::GROUP_VALUE, SensorAttributePeer::INT_VALUE, SensorAttributePeer::NOTE, SensorAttributePeer::NUM_VALUE, SensorAttributePeer::PAGE_COUNT, SensorAttributePeer::SENSOR_ID, SensorAttributePeer::STRING_VALUE, SensorAttributePeer::UNIT_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'ATTRIBUTE_ID', 'DATE_VALUE', 'GROUP_VALUE', 'INT_VALUE', 'NOTE', 'NUM_VALUE', 'PAGE_COUNT', 'SENSOR_ID', 'STRING_VALUE', 'UNIT_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'AttributeId' => 1, 'DateValue' => 2, 'GroupValue' => 3, 'IntValue' => 4, 'Note' => 5, 'NumValue' => 6, 'PageCount' => 7, 'SensorId' => 8, 'StringValue' => 9, 'UnitId' => 10, ),
		BasePeer::TYPE_COLNAME => array (SensorAttributePeer::ID => 0, SensorAttributePeer::ATTRIBUTE_ID => 1, SensorAttributePeer::DATE_VALUE => 2, SensorAttributePeer::GROUP_VALUE => 3, SensorAttributePeer::INT_VALUE => 4, SensorAttributePeer::NOTE => 5, SensorAttributePeer::NUM_VALUE => 6, SensorAttributePeer::PAGE_COUNT => 7, SensorAttributePeer::SENSOR_ID => 8, SensorAttributePeer::STRING_VALUE => 9, SensorAttributePeer::UNIT_ID => 10, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'ATTRIBUTE_ID' => 1, 'DATE_VALUE' => 2, 'GROUP_VALUE' => 3, 'INT_VALUE' => 4, 'NOTE' => 5, 'NUM_VALUE' => 6, 'PAGE_COUNT' => 7, 'SENSOR_ID' => 8, 'STRING_VALUE' => 9, 'UNIT_ID' => 10, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/SensorAttributeMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.SensorAttributeMapBuilder');
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
			$map = SensorAttributePeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. SensorAttributePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(SensorAttributePeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(SensorAttributePeer::ID);

		$criteria->addSelectColumn(SensorAttributePeer::ATTRIBUTE_ID);

		$criteria->addSelectColumn(SensorAttributePeer::DATE_VALUE);

		$criteria->addSelectColumn(SensorAttributePeer::GROUP_VALUE);

		$criteria->addSelectColumn(SensorAttributePeer::INT_VALUE);

		$criteria->addSelectColumn(SensorAttributePeer::NOTE);

		$criteria->addSelectColumn(SensorAttributePeer::NUM_VALUE);

		$criteria->addSelectColumn(SensorAttributePeer::PAGE_COUNT);

		$criteria->addSelectColumn(SensorAttributePeer::SENSOR_ID);

		$criteria->addSelectColumn(SensorAttributePeer::STRING_VALUE);

		$criteria->addSelectColumn(SensorAttributePeer::UNIT_ID);

	}

	const COUNT = 'COUNT(SENSOR_ATTRIBUTE.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT SENSOR_ATTRIBUTE.ID)';

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
			$criteria->addSelectColumn(SensorAttributePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = SensorAttributePeer::doSelectRS($criteria, $con);
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
	 * @return     SensorAttribute
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = SensorAttributePeer::doSelect($critcopy, $con);
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
		return SensorAttributePeer::populateObjects(SensorAttributePeer::doSelectRS($criteria, $con));
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
			SensorAttributePeer::addSelectColumns($criteria);
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
		$cls = SensorAttributePeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related Attribute table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAttribute(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorAttributePeer::ATTRIBUTE_ID, AttributePeer::ATTRIBUTE_ID);

		$rs = SensorAttributePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Sensor table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinSensor(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorAttributePeer::SENSOR_ID, SensorPeer::SENSOR_ID);

		$rs = SensorAttributePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Unit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorAttributePeer::UNIT_ID, UnitPeer::UNIT_ID);

		$rs = SensorAttributePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SensorAttribute objects pre-filled with their Attribute objects.
	 *
	 * @return     array Array of SensorAttribute objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAttribute(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorAttributePeer::addSelectColumns($c);
		$startcol = (SensorAttributePeer::NUM_COLUMNS - SensorAttributePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		AttributePeer::addSelectColumns($c);

		$c->addJoin(SensorAttributePeer::ATTRIBUTE_ID, AttributePeer::ATTRIBUTE_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorAttributePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = AttributePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getAttribute(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addSensorAttribute($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initSensorAttributes();
				$obj2->addSensorAttribute($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of SensorAttribute objects pre-filled with their Sensor objects.
	 *
	 * @return     array Array of SensorAttribute objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinSensor(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorAttributePeer::addSelectColumns($c);
		$startcol = (SensorAttributePeer::NUM_COLUMNS - SensorAttributePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		SensorPeer::addSelectColumns($c);

		$c->addJoin(SensorAttributePeer::SENSOR_ID, SensorPeer::SENSOR_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorAttributePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SensorPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getSensor(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addSensorAttribute($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initSensorAttributes();
				$obj2->addSensorAttribute($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of SensorAttribute objects pre-filled with their Unit objects.
	 *
	 * @return     array Array of SensorAttribute objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorAttributePeer::addSelectColumns($c);
		$startcol = (SensorAttributePeer::NUM_COLUMNS - SensorAttributePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		UnitPeer::addSelectColumns($c);

		$c->addJoin(SensorAttributePeer::UNIT_ID, UnitPeer::UNIT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorAttributePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = UnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getUnit(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addSensorAttribute($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initSensorAttributes();
				$obj2->addSensorAttribute($obj1); //CHECKME
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
			$criteria->addSelectColumn(SensorAttributePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorAttributePeer::ATTRIBUTE_ID, AttributePeer::ATTRIBUTE_ID);

		$criteria->addJoin(SensorAttributePeer::SENSOR_ID, SensorPeer::SENSOR_ID);

		$criteria->addJoin(SensorAttributePeer::UNIT_ID, UnitPeer::UNIT_ID);

		$rs = SensorAttributePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SensorAttribute objects pre-filled with all related objects.
	 *
	 * @return     array Array of SensorAttribute objects.
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

		SensorAttributePeer::addSelectColumns($c);
		$startcol2 = (SensorAttributePeer::NUM_COLUMNS - SensorAttributePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		AttributePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + AttributePeer::NUM_COLUMNS;

		SensorPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + SensorPeer::NUM_COLUMNS;

		UnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + UnitPeer::NUM_COLUMNS;

		$c->addJoin(SensorAttributePeer::ATTRIBUTE_ID, AttributePeer::ATTRIBUTE_ID);

		$c->addJoin(SensorAttributePeer::SENSOR_ID, SensorPeer::SENSOR_ID);

		$c->addJoin(SensorAttributePeer::UNIT_ID, UnitPeer::UNIT_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorAttributePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined Attribute rows
	
			$omClass = AttributePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getAttribute(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSensorAttribute($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initSensorAttributes();
				$obj2->addSensorAttribute($obj1);
			}


				// Add objects for joined Sensor rows
	
			$omClass = SensorPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getSensor(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addSensorAttribute($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initSensorAttributes();
				$obj3->addSensorAttribute($obj1);
			}


				// Add objects for joined Unit rows
	
			$omClass = UnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getUnit(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addSensorAttribute($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initSensorAttributes();
				$obj4->addSensorAttribute($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Attribute table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptAttribute(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorAttributePeer::SENSOR_ID, SensorPeer::SENSOR_ID);

		$criteria->addJoin(SensorAttributePeer::UNIT_ID, UnitPeer::UNIT_ID);

		$rs = SensorAttributePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Sensor table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptSensor(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorAttributePeer::ATTRIBUTE_ID, AttributePeer::ATTRIBUTE_ID);

		$criteria->addJoin(SensorAttributePeer::UNIT_ID, UnitPeer::UNIT_ID);

		$rs = SensorAttributePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Unit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorAttributePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorAttributePeer::ATTRIBUTE_ID, AttributePeer::ATTRIBUTE_ID);

		$criteria->addJoin(SensorAttributePeer::SENSOR_ID, SensorPeer::SENSOR_ID);

		$rs = SensorAttributePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SensorAttribute objects pre-filled with all related objects except Attribute.
	 *
	 * @return     array Array of SensorAttribute objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptAttribute(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorAttributePeer::addSelectColumns($c);
		$startcol2 = (SensorAttributePeer::NUM_COLUMNS - SensorAttributePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SensorPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SensorPeer::NUM_COLUMNS;

		UnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + UnitPeer::NUM_COLUMNS;

		$c->addJoin(SensorAttributePeer::SENSOR_ID, SensorPeer::SENSOR_ID);

		$c->addJoin(SensorAttributePeer::UNIT_ID, UnitPeer::UNIT_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorAttributePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SensorPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSensor(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSensorAttribute($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initSensorAttributes();
				$obj2->addSensorAttribute($obj1);
			}

			$omClass = UnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getUnit(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addSensorAttribute($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initSensorAttributes();
				$obj3->addSensorAttribute($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of SensorAttribute objects pre-filled with all related objects except Sensor.
	 *
	 * @return     array Array of SensorAttribute objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptSensor(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorAttributePeer::addSelectColumns($c);
		$startcol2 = (SensorAttributePeer::NUM_COLUMNS - SensorAttributePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		AttributePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + AttributePeer::NUM_COLUMNS;

		UnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + UnitPeer::NUM_COLUMNS;

		$c->addJoin(SensorAttributePeer::ATTRIBUTE_ID, AttributePeer::ATTRIBUTE_ID);

		$c->addJoin(SensorAttributePeer::UNIT_ID, UnitPeer::UNIT_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorAttributePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = AttributePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getAttribute(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSensorAttribute($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initSensorAttributes();
				$obj2->addSensorAttribute($obj1);
			}

			$omClass = UnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getUnit(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addSensorAttribute($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initSensorAttributes();
				$obj3->addSensorAttribute($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of SensorAttribute objects pre-filled with all related objects except Unit.
	 *
	 * @return     array Array of SensorAttribute objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorAttributePeer::addSelectColumns($c);
		$startcol2 = (SensorAttributePeer::NUM_COLUMNS - SensorAttributePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		AttributePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + AttributePeer::NUM_COLUMNS;

		SensorPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + SensorPeer::NUM_COLUMNS;

		$c->addJoin(SensorAttributePeer::ATTRIBUTE_ID, AttributePeer::ATTRIBUTE_ID);

		$c->addJoin(SensorAttributePeer::SENSOR_ID, SensorPeer::SENSOR_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorAttributePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = AttributePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getAttribute(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSensorAttribute($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initSensorAttributes();
				$obj2->addSensorAttribute($obj1);
			}

			$omClass = SensorPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getSensor(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addSensorAttribute($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initSensorAttributes();
				$obj3->addSensorAttribute($obj1);
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
		return SensorAttributePeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a SensorAttribute or Criteria object.
	 *
	 * @param      mixed $values Criteria or SensorAttribute object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from SensorAttribute object
		}

		$criteria->remove(SensorAttributePeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a SensorAttribute or Criteria object.
	 *
	 * @param      mixed $values Criteria or SensorAttribute object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(SensorAttributePeer::ID);
			$selectCriteria->add(SensorAttributePeer::ID, $criteria->remove(SensorAttributePeer::ID), $comparison);

		} else { // $values is SensorAttribute object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the SENSOR_ATTRIBUTE table.
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
			$affectedRows += BasePeer::doDeleteAll(SensorAttributePeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a SensorAttribute or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or SensorAttribute object or primary key or array of primary keys
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
			$con = Propel::getConnection(SensorAttributePeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof SensorAttribute) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(SensorAttributePeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given SensorAttribute object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      SensorAttribute $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(SensorAttribute $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(SensorAttributePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(SensorAttributePeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(SensorAttributePeer::ATTRIBUTE_ID))
			$columns[SensorAttributePeer::ATTRIBUTE_ID] = $obj->getAttributeId();

		if ($obj->isNew() || $obj->isColumnModified(SensorAttributePeer::DATE_VALUE))
			$columns[SensorAttributePeer::DATE_VALUE] = $obj->getDateValue();

		if ($obj->isNew() || $obj->isColumnModified(SensorAttributePeer::GROUP_VALUE))
			$columns[SensorAttributePeer::GROUP_VALUE] = $obj->getGroupValue();

		if ($obj->isNew() || $obj->isColumnModified(SensorAttributePeer::INT_VALUE))
			$columns[SensorAttributePeer::INT_VALUE] = $obj->getIntValue();

		if ($obj->isNew() || $obj->isColumnModified(SensorAttributePeer::NOTE))
			$columns[SensorAttributePeer::NOTE] = $obj->getNote();

		if ($obj->isNew() || $obj->isColumnModified(SensorAttributePeer::NUM_VALUE))
			$columns[SensorAttributePeer::NUM_VALUE] = $obj->getNumValue();

		if ($obj->isNew() || $obj->isColumnModified(SensorAttributePeer::PAGE_COUNT))
			$columns[SensorAttributePeer::PAGE_COUNT] = $obj->getPageCount();

		if ($obj->isNew() || $obj->isColumnModified(SensorAttributePeer::SENSOR_ID))
			$columns[SensorAttributePeer::SENSOR_ID] = $obj->getSensorId();

		if ($obj->isNew() || $obj->isColumnModified(SensorAttributePeer::STRING_VALUE))
			$columns[SensorAttributePeer::STRING_VALUE] = $obj->getStringValue();

		if ($obj->isNew() || $obj->isColumnModified(SensorAttributePeer::UNIT_ID))
			$columns[SensorAttributePeer::UNIT_ID] = $obj->getUnitId();

		}

		return BasePeer::doValidate(SensorAttributePeer::DATABASE_NAME, SensorAttributePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     SensorAttribute
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(SensorAttributePeer::DATABASE_NAME);

		$criteria->add(SensorAttributePeer::ID, $pk);


		$v = SensorAttributePeer::doSelect($criteria, $con);

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
			$criteria->add(SensorAttributePeer::ID, $pks, Criteria::IN);
			$objs = SensorAttributePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseSensorAttributePeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseSensorAttributePeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/SensorAttributeMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.SensorAttributeMapBuilder');
}
