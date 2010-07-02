<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by ControllerChannelPeer::getOMClass()
include_once 'lib/data/ControllerChannel.php';

/**
 * Base static class for performing query and update operations on the 'CONTROLLER_CHANNEL' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseControllerChannelPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'CONTROLLER_CHANNEL';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.ControllerChannel';

	/** The total number of columns. */
	const NUM_COLUMNS = 9;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'CONTROLLER_CHANNEL.ID';

	/** the column name for the CONTROLLER_CONFIG_ID field */
	const CONTROLLER_CONFIG_ID = 'CONTROLLER_CHANNEL.CONTROLLER_CONFIG_ID';

	/** the column name for the DATA_FILE_ID field */
	const DATA_FILE_ID = 'CONTROLLER_CHANNEL.DATA_FILE_ID';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'CONTROLLER_CHANNEL.DESCRIPTION';

	/** the column name for the DIRECTION field */
	const DIRECTION = 'CONTROLLER_CHANNEL.DIRECTION';

	/** the column name for the EQUIPMENT_ID field */
	const EQUIPMENT_ID = 'CONTROLLER_CHANNEL.EQUIPMENT_ID';

	/** the column name for the NAME field */
	const NAME = 'CONTROLLER_CHANNEL.NAME';

	/** the column name for the SOURCE_LOCATION_ID field */
	const SOURCE_LOCATION_ID = 'CONTROLLER_CHANNEL.SOURCE_LOCATION_ID';

	/** the column name for the STATION field */
	const STATION = 'CONTROLLER_CHANNEL.STATION';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'ControllerConfigId', 'DataFileId', 'Description', 'Direction', 'EquipmentId', 'Name', 'SourceLocationId', 'Station', ),
		BasePeer::TYPE_COLNAME => array (ControllerChannelPeer::ID, ControllerChannelPeer::CONTROLLER_CONFIG_ID, ControllerChannelPeer::DATA_FILE_ID, ControllerChannelPeer::DESCRIPTION, ControllerChannelPeer::DIRECTION, ControllerChannelPeer::EQUIPMENT_ID, ControllerChannelPeer::NAME, ControllerChannelPeer::SOURCE_LOCATION_ID, ControllerChannelPeer::STATION, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'CONTROLLER_CONFIG_ID', 'DATA_FILE_ID', 'DESCRIPTION', 'DIRECTION', 'EQUIPMENT_ID', 'NAME', 'SOURCE_LOCATION_ID', 'STATION', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'ControllerConfigId' => 1, 'DataFileId' => 2, 'Description' => 3, 'Direction' => 4, 'EquipmentId' => 5, 'Name' => 6, 'SourceLocationId' => 7, 'Station' => 8, ),
		BasePeer::TYPE_COLNAME => array (ControllerChannelPeer::ID => 0, ControllerChannelPeer::CONTROLLER_CONFIG_ID => 1, ControllerChannelPeer::DATA_FILE_ID => 2, ControllerChannelPeer::DESCRIPTION => 3, ControllerChannelPeer::DIRECTION => 4, ControllerChannelPeer::EQUIPMENT_ID => 5, ControllerChannelPeer::NAME => 6, ControllerChannelPeer::SOURCE_LOCATION_ID => 7, ControllerChannelPeer::STATION => 8, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'CONTROLLER_CONFIG_ID' => 1, 'DATA_FILE_ID' => 2, 'DESCRIPTION' => 3, 'DIRECTION' => 4, 'EQUIPMENT_ID' => 5, 'NAME' => 6, 'SOURCE_LOCATION_ID' => 7, 'STATION' => 8, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/ControllerChannelMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.ControllerChannelMapBuilder');
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
			$map = ControllerChannelPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. ControllerChannelPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(ControllerChannelPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(ControllerChannelPeer::ID);

		$criteria->addSelectColumn(ControllerChannelPeer::CONTROLLER_CONFIG_ID);

		$criteria->addSelectColumn(ControllerChannelPeer::DATA_FILE_ID);

		$criteria->addSelectColumn(ControllerChannelPeer::DESCRIPTION);

		$criteria->addSelectColumn(ControllerChannelPeer::DIRECTION);

		$criteria->addSelectColumn(ControllerChannelPeer::EQUIPMENT_ID);

		$criteria->addSelectColumn(ControllerChannelPeer::NAME);

		$criteria->addSelectColumn(ControllerChannelPeer::SOURCE_LOCATION_ID);

		$criteria->addSelectColumn(ControllerChannelPeer::STATION);

	}

	const COUNT = 'COUNT(CONTROLLER_CHANNEL.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT CONTROLLER_CHANNEL.ID)';

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
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = ControllerChannelPeer::doSelectRS($criteria, $con);
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
	 * @return     ControllerChannel
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = ControllerChannelPeer::doSelect($critcopy, $con);
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
		return ControllerChannelPeer::populateObjects(ControllerChannelPeer::doSelectRS($criteria, $con));
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
			ControllerChannelPeer::addSelectColumns($criteria);
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
		$cls = ControllerChannelPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related ControllerConfig table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinControllerConfig(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerChannelPeer::CONTROLLER_CONFIG_ID, ControllerConfigPeer::ID);

		$rs = ControllerChannelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFile table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinDataFile(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerChannelPeer::DATA_FILE_ID, DataFilePeer::ID);

		$rs = ControllerChannelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Equipment table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEquipment(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerChannelPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$rs = ControllerChannelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Location table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinLocation(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerChannelPeer::SOURCE_LOCATION_ID, LocationPeer::ID);

		$rs = ControllerChannelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of ControllerChannel objects pre-filled with their ControllerConfig objects.
	 *
	 * @return     array Array of ControllerChannel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinControllerConfig(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerChannelPeer::addSelectColumns($c);
		$startcol = (ControllerChannelPeer::NUM_COLUMNS - ControllerChannelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		ControllerConfigPeer::addSelectColumns($c);

		$c->addJoin(ControllerChannelPeer::CONTROLLER_CONFIG_ID, ControllerConfigPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerChannelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ControllerConfigPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getControllerConfig(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addControllerChannel($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initControllerChannels();
				$obj2->addControllerChannel($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerChannel objects pre-filled with their DataFile objects.
	 *
	 * @return     array Array of ControllerChannel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinDataFile(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerChannelPeer::addSelectColumns($c);
		$startcol = (ControllerChannelPeer::NUM_COLUMNS - ControllerChannelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DataFilePeer::addSelectColumns($c);

		$c->addJoin(ControllerChannelPeer::DATA_FILE_ID, DataFilePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerChannelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getDataFile(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addControllerChannel($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initControllerChannels();
				$obj2->addControllerChannel($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerChannel objects pre-filled with their Equipment objects.
	 *
	 * @return     array Array of ControllerChannel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEquipment(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerChannelPeer::addSelectColumns($c);
		$startcol = (ControllerChannelPeer::NUM_COLUMNS - ControllerChannelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		EquipmentPeer::addSelectColumns($c);

		$c->addJoin(ControllerChannelPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerChannelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addControllerChannel($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initControllerChannels();
				$obj2->addControllerChannel($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerChannel objects pre-filled with their Location objects.
	 *
	 * @return     array Array of ControllerChannel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinLocation(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerChannelPeer::addSelectColumns($c);
		$startcol = (ControllerChannelPeer::NUM_COLUMNS - ControllerChannelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		LocationPeer::addSelectColumns($c);

		$c->addJoin(ControllerChannelPeer::SOURCE_LOCATION_ID, LocationPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerChannelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = LocationPeer::getOMClass($rs, $startcol);

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getLocation(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addControllerChannel($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initControllerChannels();
				$obj2->addControllerChannel($obj1); //CHECKME
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
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerChannelPeer::CONTROLLER_CONFIG_ID, ControllerConfigPeer::ID);

		$criteria->addJoin(ControllerChannelPeer::DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerChannelPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(ControllerChannelPeer::SOURCE_LOCATION_ID, LocationPeer::ID);

		$rs = ControllerChannelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of ControllerChannel objects pre-filled with all related objects.
	 *
	 * @return     array Array of ControllerChannel objects.
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

		ControllerChannelPeer::addSelectColumns($c);
		$startcol2 = (ControllerChannelPeer::NUM_COLUMNS - ControllerChannelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		ControllerConfigPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + ControllerConfigPeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DataFilePeer::NUM_COLUMNS;

		EquipmentPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + EquipmentPeer::NUM_COLUMNS;

		LocationPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + LocationPeer::NUM_COLUMNS;

		$c->addJoin(ControllerChannelPeer::CONTROLLER_CONFIG_ID, ControllerConfigPeer::ID);

		$c->addJoin(ControllerChannelPeer::DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerChannelPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(ControllerChannelPeer::SOURCE_LOCATION_ID, LocationPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerChannelPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined ControllerConfig rows
	
			$omClass = ControllerConfigPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getControllerConfig(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addControllerChannel($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initControllerChannels();
				$obj2->addControllerChannel($obj1);
			}


				// Add objects for joined DataFile rows
	
			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDataFile(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addControllerChannel($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initControllerChannels();
				$obj3->addControllerChannel($obj1);
			}


				// Add objects for joined Equipment rows
	
			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getEquipment(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addControllerChannel($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initControllerChannels();
				$obj4->addControllerChannel($obj1);
			}


				// Add objects for joined Location rows
	
			$omClass = LocationPeer::getOMClass($rs, $startcol5);


			$cls = Propel::import($omClass);
			$obj5 = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getLocation(); // CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addControllerChannel($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj5->initControllerChannels();
				$obj5->addControllerChannel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related ControllerConfig table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptControllerConfig(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerChannelPeer::DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerChannelPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(ControllerChannelPeer::SOURCE_LOCATION_ID, LocationPeer::ID);

		$rs = ControllerChannelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFile table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDataFile(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerChannelPeer::CONTROLLER_CONFIG_ID, ControllerConfigPeer::ID);

		$criteria->addJoin(ControllerChannelPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(ControllerChannelPeer::SOURCE_LOCATION_ID, LocationPeer::ID);

		$rs = ControllerChannelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Equipment table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEquipment(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerChannelPeer::CONTROLLER_CONFIG_ID, ControllerConfigPeer::ID);

		$criteria->addJoin(ControllerChannelPeer::DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerChannelPeer::SOURCE_LOCATION_ID, LocationPeer::ID);

		$rs = ControllerChannelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Location table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptLocation(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerChannelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerChannelPeer::CONTROLLER_CONFIG_ID, ControllerConfigPeer::ID);

		$criteria->addJoin(ControllerChannelPeer::DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerChannelPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$rs = ControllerChannelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of ControllerChannel objects pre-filled with all related objects except ControllerConfig.
	 *
	 * @return     array Array of ControllerChannel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptControllerConfig(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerChannelPeer::addSelectColumns($c);
		$startcol2 = (ControllerChannelPeer::NUM_COLUMNS - ControllerChannelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		EquipmentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + EquipmentPeer::NUM_COLUMNS;

		LocationPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + LocationPeer::NUM_COLUMNS;

		$c->addJoin(ControllerChannelPeer::DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerChannelPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(ControllerChannelPeer::SOURCE_LOCATION_ID, LocationPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerChannelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getDataFile(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initControllerChannels();
				$obj2->addControllerChannel($obj1);
			}

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initControllerChannels();
				$obj3->addControllerChannel($obj1);
			}

			$omClass = LocationPeer::getOMClass($rs, $startcol4);


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getLocation(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initControllerChannels();
				$obj4->addControllerChannel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerChannel objects pre-filled with all related objects except DataFile.
	 *
	 * @return     array Array of ControllerChannel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDataFile(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerChannelPeer::addSelectColumns($c);
		$startcol2 = (ControllerChannelPeer::NUM_COLUMNS - ControllerChannelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		ControllerConfigPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + ControllerConfigPeer::NUM_COLUMNS;

		EquipmentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + EquipmentPeer::NUM_COLUMNS;

		LocationPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + LocationPeer::NUM_COLUMNS;

		$c->addJoin(ControllerChannelPeer::CONTROLLER_CONFIG_ID, ControllerConfigPeer::ID);

		$c->addJoin(ControllerChannelPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(ControllerChannelPeer::SOURCE_LOCATION_ID, LocationPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerChannelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ControllerConfigPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getControllerConfig(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initControllerChannels();
				$obj2->addControllerChannel($obj1);
			}

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initControllerChannels();
				$obj3->addControllerChannel($obj1);
			}

			$omClass = LocationPeer::getOMClass($rs, $startcol4);


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getLocation(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initControllerChannels();
				$obj4->addControllerChannel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerChannel objects pre-filled with all related objects except Equipment.
	 *
	 * @return     array Array of ControllerChannel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEquipment(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerChannelPeer::addSelectColumns($c);
		$startcol2 = (ControllerChannelPeer::NUM_COLUMNS - ControllerChannelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		ControllerConfigPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + ControllerConfigPeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DataFilePeer::NUM_COLUMNS;

		LocationPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + LocationPeer::NUM_COLUMNS;

		$c->addJoin(ControllerChannelPeer::CONTROLLER_CONFIG_ID, ControllerConfigPeer::ID);

		$c->addJoin(ControllerChannelPeer::DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerChannelPeer::SOURCE_LOCATION_ID, LocationPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerChannelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ControllerConfigPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getControllerConfig(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initControllerChannels();
				$obj2->addControllerChannel($obj1);
			}

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDataFile(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initControllerChannels();
				$obj3->addControllerChannel($obj1);
			}

			$omClass = LocationPeer::getOMClass($rs, $startcol4);


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getLocation(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initControllerChannels();
				$obj4->addControllerChannel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerChannel objects pre-filled with all related objects except Location.
	 *
	 * @return     array Array of ControllerChannel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptLocation(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerChannelPeer::addSelectColumns($c);
		$startcol2 = (ControllerChannelPeer::NUM_COLUMNS - ControllerChannelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		ControllerConfigPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + ControllerConfigPeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DataFilePeer::NUM_COLUMNS;

		EquipmentPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + EquipmentPeer::NUM_COLUMNS;

		$c->addJoin(ControllerChannelPeer::CONTROLLER_CONFIG_ID, ControllerConfigPeer::ID);

		$c->addJoin(ControllerChannelPeer::DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerChannelPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerChannelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ControllerConfigPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getControllerConfig(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initControllerChannels();
				$obj2->addControllerChannel($obj1);
			}

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDataFile(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initControllerChannels();
				$obj3->addControllerChannel($obj1);
			}

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addControllerChannel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initControllerChannels();
				$obj4->addControllerChannel($obj1);
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
		return ControllerChannelPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a ControllerChannel or Criteria object.
	 *
	 * @param      mixed $values Criteria or ControllerChannel object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from ControllerChannel object
		}

		$criteria->remove(ControllerChannelPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a ControllerChannel or Criteria object.
	 *
	 * @param      mixed $values Criteria or ControllerChannel object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(ControllerChannelPeer::ID);
			$selectCriteria->add(ControllerChannelPeer::ID, $criteria->remove(ControllerChannelPeer::ID), $comparison);

		} else { // $values is ControllerChannel object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the CONTROLLER_CHANNEL table.
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
			$affectedRows += BasePeer::doDeleteAll(ControllerChannelPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a ControllerChannel or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or ControllerChannel object or primary key or array of primary keys
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
			$con = Propel::getConnection(ControllerChannelPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof ControllerChannel) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(ControllerChannelPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given ControllerChannel object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      ControllerChannel $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(ControllerChannel $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(ControllerChannelPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(ControllerChannelPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(ControllerChannelPeer::CONTROLLER_CONFIG_ID))
			$columns[ControllerChannelPeer::CONTROLLER_CONFIG_ID] = $obj->getControllerConfigId();

		if ($obj->isNew() || $obj->isColumnModified(ControllerChannelPeer::DATA_FILE_ID))
			$columns[ControllerChannelPeer::DATA_FILE_ID] = $obj->getDataFileId();

		if ($obj->isNew() || $obj->isColumnModified(ControllerChannelPeer::DESCRIPTION))
			$columns[ControllerChannelPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(ControllerChannelPeer::DIRECTION))
			$columns[ControllerChannelPeer::DIRECTION] = $obj->getDirection();

		if ($obj->isNew() || $obj->isColumnModified(ControllerChannelPeer::EQUIPMENT_ID))
			$columns[ControllerChannelPeer::EQUIPMENT_ID] = $obj->getEquipmentId();

		if ($obj->isNew() || $obj->isColumnModified(ControllerChannelPeer::NAME))
			$columns[ControllerChannelPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(ControllerChannelPeer::SOURCE_LOCATION_ID))
			$columns[ControllerChannelPeer::SOURCE_LOCATION_ID] = $obj->getSourceLocationId();

		if ($obj->isNew() || $obj->isColumnModified(ControllerChannelPeer::STATION))
			$columns[ControllerChannelPeer::STATION] = $obj->getStation();

		}

		return BasePeer::doValidate(ControllerChannelPeer::DATABASE_NAME, ControllerChannelPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     ControllerChannel
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(ControllerChannelPeer::DATABASE_NAME);

		$criteria->add(ControllerChannelPeer::ID, $pk);


		$v = ControllerChannelPeer::doSelect($criteria, $con);

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
			$criteria->add(ControllerChannelPeer::ID, $pks, Criteria::IN);
			$objs = ControllerChannelPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseControllerChannelPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseControllerChannelPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/ControllerChannelMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.ControllerChannelMapBuilder');
}
