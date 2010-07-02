<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by ControllerConfigPeer::getOMClass()
include_once 'lib/data/ControllerConfig.php';

/**
 * Base static class for performing query and update operations on the 'CONTROLLER_CONFIG' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseControllerConfigPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'CONTROLLER_CONFIG';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.ControllerConfig';

	/** The total number of columns. */
	const NUM_COLUMNS = 9;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'CONTROLLER_CONFIG.ID';

	/** the column name for the CONFIG_DATA_FILE_ID field */
	const CONFIG_DATA_FILE_ID = 'CONTROLLER_CONFIG.CONFIG_DATA_FILE_ID';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'CONTROLLER_CONFIG.DESCRIPTION';

	/** the column name for the EQUIPMENT_ID field */
	const EQUIPMENT_ID = 'CONTROLLER_CONFIG.EQUIPMENT_ID';

	/** the column name for the INPUT_DATA_FILE_ID field */
	const INPUT_DATA_FILE_ID = 'CONTROLLER_CONFIG.INPUT_DATA_FILE_ID';

	/** the column name for the NAME field */
	const NAME = 'CONTROLLER_CONFIG.NAME';

	/** the column name for the PEAK_BASE_ACCELERATION field */
	const PEAK_BASE_ACCELERATION = 'CONTROLLER_CONFIG.PEAK_BASE_ACCELERATION';

	/** the column name for the PEAK_BASE_ACCELERATION_UNIT_ID field */
	const PEAK_BASE_ACCELERATION_UNIT_ID = 'CONTROLLER_CONFIG.PEAK_BASE_ACCELERATION_UNIT_ID';

	/** the column name for the TRIAL_ID field */
	const TRIAL_ID = 'CONTROLLER_CONFIG.TRIAL_ID';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'ConfigDataFileId', 'Description', 'EquipmentId', 'InputDataFileId', 'Name', 'PeakBaseAcceleration', 'PeakBaseAccelerationUnitId', 'TrialId', ),
		BasePeer::TYPE_COLNAME => array (ControllerConfigPeer::ID, ControllerConfigPeer::CONFIG_DATA_FILE_ID, ControllerConfigPeer::DESCRIPTION, ControllerConfigPeer::EQUIPMENT_ID, ControllerConfigPeer::INPUT_DATA_FILE_ID, ControllerConfigPeer::NAME, ControllerConfigPeer::PEAK_BASE_ACCELERATION, ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, ControllerConfigPeer::TRIAL_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'CONFIG_DATA_FILE_ID', 'DESCRIPTION', 'EQUIPMENT_ID', 'INPUT_DATA_FILE_ID', 'NAME', 'PEAK_BASE_ACCELERATION', 'PEAK_BASE_ACCELERATION_UNIT_ID', 'TRIAL_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'ConfigDataFileId' => 1, 'Description' => 2, 'EquipmentId' => 3, 'InputDataFileId' => 4, 'Name' => 5, 'PeakBaseAcceleration' => 6, 'PeakBaseAccelerationUnitId' => 7, 'TrialId' => 8, ),
		BasePeer::TYPE_COLNAME => array (ControllerConfigPeer::ID => 0, ControllerConfigPeer::CONFIG_DATA_FILE_ID => 1, ControllerConfigPeer::DESCRIPTION => 2, ControllerConfigPeer::EQUIPMENT_ID => 3, ControllerConfigPeer::INPUT_DATA_FILE_ID => 4, ControllerConfigPeer::NAME => 5, ControllerConfigPeer::PEAK_BASE_ACCELERATION => 6, ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID => 7, ControllerConfigPeer::TRIAL_ID => 8, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'CONFIG_DATA_FILE_ID' => 1, 'DESCRIPTION' => 2, 'EQUIPMENT_ID' => 3, 'INPUT_DATA_FILE_ID' => 4, 'NAME' => 5, 'PEAK_BASE_ACCELERATION' => 6, 'PEAK_BASE_ACCELERATION_UNIT_ID' => 7, 'TRIAL_ID' => 8, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/ControllerConfigMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.ControllerConfigMapBuilder');
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
			$map = ControllerConfigPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. ControllerConfigPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(ControllerConfigPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(ControllerConfigPeer::ID);

		$criteria->addSelectColumn(ControllerConfigPeer::CONFIG_DATA_FILE_ID);

		$criteria->addSelectColumn(ControllerConfigPeer::DESCRIPTION);

		$criteria->addSelectColumn(ControllerConfigPeer::EQUIPMENT_ID);

		$criteria->addSelectColumn(ControllerConfigPeer::INPUT_DATA_FILE_ID);

		$criteria->addSelectColumn(ControllerConfigPeer::NAME);

		$criteria->addSelectColumn(ControllerConfigPeer::PEAK_BASE_ACCELERATION);

		$criteria->addSelectColumn(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID);

		$criteria->addSelectColumn(ControllerConfigPeer::TRIAL_ID);

	}

	const COUNT = 'COUNT(CONTROLLER_CONFIG.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT CONTROLLER_CONFIG.ID)';

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
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
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
	 * @return     ControllerConfig
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = ControllerConfigPeer::doSelect($critcopy, $con);
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
		return ControllerConfigPeer::populateObjects(ControllerConfigPeer::doSelectRS($criteria, $con));
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
			ControllerConfigPeer::addSelectColumns($criteria);
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
		$cls = ControllerConfigPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByInputDataFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinDataFileRelatedByInputDataFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerConfigPeer::INPUT_DATA_FILE_ID, DataFilePeer::ID);

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByConfigDataFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinDataFileRelatedByConfigDataFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerConfigPeer::CONFIG_DATA_FILE_ID, DataFilePeer::ID);

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Trial table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinTrial(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of ControllerConfig objects pre-filled with their DataFile objects.
	 *
	 * @return     array Array of ControllerConfig objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinDataFileRelatedByInputDataFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerConfigPeer::addSelectColumns($c);
		$startcol = (ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DataFilePeer::addSelectColumns($c);

		$c->addJoin(ControllerConfigPeer::INPUT_DATA_FILE_ID, DataFilePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerConfigPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getDataFileRelatedByInputDataFileId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addControllerConfigRelatedByInputDataFileId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initControllerConfigsRelatedByInputDataFileId();
				$obj2->addControllerConfigRelatedByInputDataFileId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerConfig objects pre-filled with their DataFile objects.
	 *
	 * @return     array Array of ControllerConfig objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinDataFileRelatedByConfigDataFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerConfigPeer::addSelectColumns($c);
		$startcol = (ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DataFilePeer::addSelectColumns($c);

		$c->addJoin(ControllerConfigPeer::CONFIG_DATA_FILE_ID, DataFilePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerConfigPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getDataFileRelatedByConfigDataFileId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addControllerConfigRelatedByConfigDataFileId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initControllerConfigsRelatedByConfigDataFileId();
				$obj2->addControllerConfigRelatedByConfigDataFileId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerConfig objects pre-filled with their Equipment objects.
	 *
	 * @return     array Array of ControllerConfig objects.
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

		ControllerConfigPeer::addSelectColumns($c);
		$startcol = (ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		EquipmentPeer::addSelectColumns($c);

		$c->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerConfigPeer::getOMClass();

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
					$temp_obj2->addControllerConfig($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initControllerConfigs();
				$obj2->addControllerConfig($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerConfig objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of ControllerConfig objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerConfigPeer::addSelectColumns($c);
		$startcol = (ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerConfigPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnit(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addControllerConfig($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initControllerConfigs();
				$obj2->addControllerConfig($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerConfig objects pre-filled with their Trial objects.
	 *
	 * @return     array Array of ControllerConfig objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinTrial(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerConfigPeer::addSelectColumns($c);
		$startcol = (ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		TrialPeer::addSelectColumns($c);

		$c->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerConfigPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = TrialPeer::getOMClass($rs, $startcol);

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getTrial(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addControllerConfig($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initControllerConfigs();
				$obj2->addControllerConfig($obj1); //CHECKME
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
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerConfigPeer::INPUT_DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerConfigPeer::CONFIG_DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of ControllerConfig objects pre-filled with all related objects.
	 *
	 * @return     array Array of ControllerConfig objects.
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

		ControllerConfigPeer::addSelectColumns($c);
		$startcol2 = (ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DataFilePeer::NUM_COLUMNS;

		EquipmentPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + EquipmentPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		TrialPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + TrialPeer::NUM_COLUMNS;

		$c->addJoin(ControllerConfigPeer::INPUT_DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerConfigPeer::CONFIG_DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerConfigPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined DataFile rows
	
			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getDataFileRelatedByInputDataFileId(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addControllerConfigRelatedByInputDataFileId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initControllerConfigsRelatedByInputDataFileId();
				$obj2->addControllerConfigRelatedByInputDataFileId($obj1);
			}


				// Add objects for joined DataFile rows
	
			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDataFileRelatedByConfigDataFileId(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addControllerConfigRelatedByConfigDataFileId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initControllerConfigsRelatedByConfigDataFileId();
				$obj3->addControllerConfigRelatedByConfigDataFileId($obj1);
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
					$temp_obj4->addControllerConfig($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initControllerConfigs();
				$obj4->addControllerConfig($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5 = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnit(); // CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addControllerConfig($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj5->initControllerConfigs();
				$obj5->addControllerConfig($obj1);
			}


				// Add objects for joined Trial rows
	
			$omClass = TrialPeer::getOMClass($rs, $startcol6);


			$cls = Propel::import($omClass);
			$obj6 = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getTrial(); // CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addControllerConfig($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj6->initControllerConfigs();
				$obj6->addControllerConfig($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByInputDataFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDataFileRelatedByInputDataFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByConfigDataFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDataFileRelatedByConfigDataFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerConfigPeer::INPUT_DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerConfigPeer::CONFIG_DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerConfigPeer::INPUT_DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerConfigPeer::CONFIG_DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Trial table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptTrial(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ControllerConfigPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ControllerConfigPeer::INPUT_DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerConfigPeer::CONFIG_DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = ControllerConfigPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of ControllerConfig objects pre-filled with all related objects except DataFileRelatedByInputDataFileId.
	 *
	 * @return     array Array of ControllerConfig objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDataFileRelatedByInputDataFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerConfigPeer::addSelectColumns($c);
		$startcol2 = (ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		TrialPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + TrialPeer::NUM_COLUMNS;

		$c->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerConfigPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initControllerConfigs();
				$obj2->addControllerConfig($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMeasurementUnit(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initControllerConfigs();
				$obj3->addControllerConfig($obj1);
			}

			$omClass = TrialPeer::getOMClass($rs, $startcol4);


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getTrial(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initControllerConfigs();
				$obj4->addControllerConfig($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerConfig objects pre-filled with all related objects except DataFileRelatedByConfigDataFileId.
	 *
	 * @return     array Array of ControllerConfig objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDataFileRelatedByConfigDataFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerConfigPeer::addSelectColumns($c);
		$startcol2 = (ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		TrialPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + TrialPeer::NUM_COLUMNS;

		$c->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerConfigPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initControllerConfigs();
				$obj2->addControllerConfig($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMeasurementUnit(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initControllerConfigs();
				$obj3->addControllerConfig($obj1);
			}

			$omClass = TrialPeer::getOMClass($rs, $startcol4);


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getTrial(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initControllerConfigs();
				$obj4->addControllerConfig($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerConfig objects pre-filled with all related objects except Equipment.
	 *
	 * @return     array Array of ControllerConfig objects.
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

		ControllerConfigPeer::addSelectColumns($c);
		$startcol2 = (ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DataFilePeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		TrialPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + TrialPeer::NUM_COLUMNS;

		$c->addJoin(ControllerConfigPeer::INPUT_DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerConfigPeer::CONFIG_DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerConfigPeer::getOMClass();

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
				$temp_obj2 = $temp_obj1->getDataFileRelatedByInputDataFileId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addControllerConfigRelatedByInputDataFileId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initControllerConfigsRelatedByInputDataFileId();
				$obj2->addControllerConfigRelatedByInputDataFileId($obj1);
			}

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDataFileRelatedByConfigDataFileId(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addControllerConfigRelatedByConfigDataFileId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initControllerConfigsRelatedByConfigDataFileId();
				$obj3->addControllerConfigRelatedByConfigDataFileId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnit(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initControllerConfigs();
				$obj4->addControllerConfig($obj1);
			}

			$omClass = TrialPeer::getOMClass($rs, $startcol5);


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getTrial(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initControllerConfigs();
				$obj5->addControllerConfig($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerConfig objects pre-filled with all related objects except MeasurementUnit.
	 *
	 * @return     array Array of ControllerConfig objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerConfigPeer::addSelectColumns($c);
		$startcol2 = (ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DataFilePeer::NUM_COLUMNS;

		EquipmentPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + EquipmentPeer::NUM_COLUMNS;

		TrialPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + TrialPeer::NUM_COLUMNS;

		$c->addJoin(ControllerConfigPeer::INPUT_DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerConfigPeer::CONFIG_DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(ControllerConfigPeer::TRIAL_ID, TrialPeer::TRIALID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerConfigPeer::getOMClass();

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
				$temp_obj2 = $temp_obj1->getDataFileRelatedByInputDataFileId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addControllerConfigRelatedByInputDataFileId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initControllerConfigsRelatedByInputDataFileId();
				$obj2->addControllerConfigRelatedByInputDataFileId($obj1);
			}

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDataFileRelatedByConfigDataFileId(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addControllerConfigRelatedByConfigDataFileId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initControllerConfigsRelatedByConfigDataFileId();
				$obj3->addControllerConfigRelatedByConfigDataFileId($obj1);
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
					$temp_obj4->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initControllerConfigs();
				$obj4->addControllerConfig($obj1);
			}

			$omClass = TrialPeer::getOMClass($rs, $startcol5);


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getTrial(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initControllerConfigs();
				$obj5->addControllerConfig($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of ControllerConfig objects pre-filled with all related objects except Trial.
	 *
	 * @return     array Array of ControllerConfig objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptTrial(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ControllerConfigPeer::addSelectColumns($c);
		$startcol2 = (ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DataFilePeer::NUM_COLUMNS;

		EquipmentPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + EquipmentPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(ControllerConfigPeer::INPUT_DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerConfigPeer::CONFIG_DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(ControllerConfigPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ControllerConfigPeer::getOMClass();

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
				$temp_obj2 = $temp_obj1->getDataFileRelatedByInputDataFileId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addControllerConfigRelatedByInputDataFileId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initControllerConfigsRelatedByInputDataFileId();
				$obj2->addControllerConfigRelatedByInputDataFileId($obj1);
			}

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDataFileRelatedByConfigDataFileId(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addControllerConfigRelatedByConfigDataFileId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initControllerConfigsRelatedByConfigDataFileId();
				$obj3->addControllerConfigRelatedByConfigDataFileId($obj1);
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
					$temp_obj4->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initControllerConfigs();
				$obj4->addControllerConfig($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnit(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addControllerConfig($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initControllerConfigs();
				$obj5->addControllerConfig($obj1);
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
		return ControllerConfigPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a ControllerConfig or Criteria object.
	 *
	 * @param      mixed $values Criteria or ControllerConfig object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from ControllerConfig object
		}

		$criteria->remove(ControllerConfigPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a ControllerConfig or Criteria object.
	 *
	 * @param      mixed $values Criteria or ControllerConfig object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(ControllerConfigPeer::ID);
			$selectCriteria->add(ControllerConfigPeer::ID, $criteria->remove(ControllerConfigPeer::ID), $comparison);

		} else { // $values is ControllerConfig object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the CONTROLLER_CONFIG table.
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
			$affectedRows += BasePeer::doDeleteAll(ControllerConfigPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a ControllerConfig or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or ControllerConfig object or primary key or array of primary keys
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
			$con = Propel::getConnection(ControllerConfigPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof ControllerConfig) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(ControllerConfigPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given ControllerConfig object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      ControllerConfig $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(ControllerConfig $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(ControllerConfigPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(ControllerConfigPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(ControllerConfigPeer::CONFIG_DATA_FILE_ID))
			$columns[ControllerConfigPeer::CONFIG_DATA_FILE_ID] = $obj->getConfigDataFileId();

		if ($obj->isNew() || $obj->isColumnModified(ControllerConfigPeer::DESCRIPTION))
			$columns[ControllerConfigPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(ControllerConfigPeer::EQUIPMENT_ID))
			$columns[ControllerConfigPeer::EQUIPMENT_ID] = $obj->getEquipmentId();

		if ($obj->isNew() || $obj->isColumnModified(ControllerConfigPeer::INPUT_DATA_FILE_ID))
			$columns[ControllerConfigPeer::INPUT_DATA_FILE_ID] = $obj->getInputDataFileId();

		if ($obj->isNew() || $obj->isColumnModified(ControllerConfigPeer::NAME))
			$columns[ControllerConfigPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(ControllerConfigPeer::PEAK_BASE_ACCELERATION))
			$columns[ControllerConfigPeer::PEAK_BASE_ACCELERATION] = $obj->getPeakBaseAcceleration();

		if ($obj->isNew() || $obj->isColumnModified(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID))
			$columns[ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID] = $obj->getPeakBaseAccelerationUnitId();

		if ($obj->isNew() || $obj->isColumnModified(ControllerConfigPeer::TRIAL_ID))
			$columns[ControllerConfigPeer::TRIAL_ID] = $obj->getTrialId();

		}

		return BasePeer::doValidate(ControllerConfigPeer::DATABASE_NAME, ControllerConfigPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     ControllerConfig
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(ControllerConfigPeer::DATABASE_NAME);

		$criteria->add(ControllerConfigPeer::ID, $pk);


		$v = ControllerConfigPeer::doSelect($criteria, $con);

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
			$criteria->add(ControllerConfigPeer::ID, $pks, Criteria::IN);
			$objs = ControllerConfigPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseControllerConfigPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseControllerConfigPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/ControllerConfigMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.ControllerConfigMapBuilder');
}
