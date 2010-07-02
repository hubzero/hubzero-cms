<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by SensorModelPeer::getOMClass()
include_once 'lib/data/SensorModel.php';

/**
 * Base static class for performing query and update operations on the 'SENSOR_MODEL' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSensorModelPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'SENSOR_MODEL';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.SensorModel';

	/** The total number of columns. */
	const NUM_COLUMNS = 17;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the SENSOR_MODEL_ID field */
	const SENSOR_MODEL_ID = 'SENSOR_MODEL.SENSOR_MODEL_ID';

	/** the column name for the DELETED field */
	const DELETED = 'SENSOR_MODEL.DELETED';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'SENSOR_MODEL.DESCRIPTION';

	/** the column name for the MANUFACTURER field */
	const MANUFACTURER = 'SENSOR_MODEL.MANUFACTURER';

	/** the column name for the MAX_MEASURED_VALUE field */
	const MAX_MEASURED_VALUE = 'SENSOR_MODEL.MAX_MEASURED_VALUE';

	/** the column name for the MAX_OP_TEMP field */
	const MAX_OP_TEMP = 'SENSOR_MODEL.MAX_OP_TEMP';

	/** the column name for the MEASURED_VALUE_UNITS_ID field */
	const MEASURED_VALUE_UNITS_ID = 'SENSOR_MODEL.MEASURED_VALUE_UNITS_ID';

	/** the column name for the MIN_MEASURED_VALUE field */
	const MIN_MEASURED_VALUE = 'SENSOR_MODEL.MIN_MEASURED_VALUE';

	/** the column name for the MIN_OP_TEMP field */
	const MIN_OP_TEMP = 'SENSOR_MODEL.MIN_OP_TEMP';

	/** the column name for the MODEL field */
	const MODEL = 'SENSOR_MODEL.MODEL';

	/** the column name for the NAME field */
	const NAME = 'SENSOR_MODEL.NAME';

	/** the column name for the NOTE field */
	const NOTE = 'SENSOR_MODEL.NOTE';

	/** the column name for the SENSITIVITY field */
	const SENSITIVITY = 'SENSOR_MODEL.SENSITIVITY';

	/** the column name for the SENSITIVITY_UNITS_ID field */
	const SENSITIVITY_UNITS_ID = 'SENSOR_MODEL.SENSITIVITY_UNITS_ID';

	/** the column name for the SENSOR_TYPE_ID field */
	const SENSOR_TYPE_ID = 'SENSOR_MODEL.SENSOR_TYPE_ID';

	/** the column name for the SIGNAL_TYPE field */
	const SIGNAL_TYPE = 'SENSOR_MODEL.SIGNAL_TYPE';

	/** the column name for the TEMP_UNITS_ID field */
	const TEMP_UNITS_ID = 'SENSOR_MODEL.TEMP_UNITS_ID';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Deleted', 'Description', 'Manufacturer', 'MaxMeasuredValue', 'MaxOpTemp', 'MeasuredValueUnitsId', 'MinMeasuredValue', 'MinOpTemp', 'Model', 'Name', 'Note', 'Sensitivity', 'SensitivityUnitsId', 'SensorTypeId', 'SignalType', 'TempUnitsId', ),
		BasePeer::TYPE_COLNAME => array (SensorModelPeer::SENSOR_MODEL_ID, SensorModelPeer::DELETED, SensorModelPeer::DESCRIPTION, SensorModelPeer::MANUFACTURER, SensorModelPeer::MAX_MEASURED_VALUE, SensorModelPeer::MAX_OP_TEMP, SensorModelPeer::MEASURED_VALUE_UNITS_ID, SensorModelPeer::MIN_MEASURED_VALUE, SensorModelPeer::MIN_OP_TEMP, SensorModelPeer::MODEL, SensorModelPeer::NAME, SensorModelPeer::NOTE, SensorModelPeer::SENSITIVITY, SensorModelPeer::SENSITIVITY_UNITS_ID, SensorModelPeer::SENSOR_TYPE_ID, SensorModelPeer::SIGNAL_TYPE, SensorModelPeer::TEMP_UNITS_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('SENSOR_MODEL_ID', 'DELETED', 'DESCRIPTION', 'MANUFACTURER', 'MAX_MEASURED_VALUE', 'MAX_OP_TEMP', 'MEASURED_VALUE_UNITS_ID', 'MIN_MEASURED_VALUE', 'MIN_OP_TEMP', 'MODEL', 'NAME', 'NOTE', 'SENSITIVITY', 'SENSITIVITY_UNITS_ID', 'SENSOR_TYPE_ID', 'SIGNAL_TYPE', 'TEMP_UNITS_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Deleted' => 1, 'Description' => 2, 'Manufacturer' => 3, 'MaxMeasuredValue' => 4, 'MaxOpTemp' => 5, 'MeasuredValueUnitsId' => 6, 'MinMeasuredValue' => 7, 'MinOpTemp' => 8, 'Model' => 9, 'Name' => 10, 'Note' => 11, 'Sensitivity' => 12, 'SensitivityUnitsId' => 13, 'SensorTypeId' => 14, 'SignalType' => 15, 'TempUnitsId' => 16, ),
		BasePeer::TYPE_COLNAME => array (SensorModelPeer::SENSOR_MODEL_ID => 0, SensorModelPeer::DELETED => 1, SensorModelPeer::DESCRIPTION => 2, SensorModelPeer::MANUFACTURER => 3, SensorModelPeer::MAX_MEASURED_VALUE => 4, SensorModelPeer::MAX_OP_TEMP => 5, SensorModelPeer::MEASURED_VALUE_UNITS_ID => 6, SensorModelPeer::MIN_MEASURED_VALUE => 7, SensorModelPeer::MIN_OP_TEMP => 8, SensorModelPeer::MODEL => 9, SensorModelPeer::NAME => 10, SensorModelPeer::NOTE => 11, SensorModelPeer::SENSITIVITY => 12, SensorModelPeer::SENSITIVITY_UNITS_ID => 13, SensorModelPeer::SENSOR_TYPE_ID => 14, SensorModelPeer::SIGNAL_TYPE => 15, SensorModelPeer::TEMP_UNITS_ID => 16, ),
		BasePeer::TYPE_FIELDNAME => array ('SENSOR_MODEL_ID' => 0, 'DELETED' => 1, 'DESCRIPTION' => 2, 'MANUFACTURER' => 3, 'MAX_MEASURED_VALUE' => 4, 'MAX_OP_TEMP' => 5, 'MEASURED_VALUE_UNITS_ID' => 6, 'MIN_MEASURED_VALUE' => 7, 'MIN_OP_TEMP' => 8, 'MODEL' => 9, 'NAME' => 10, 'NOTE' => 11, 'SENSITIVITY' => 12, 'SENSITIVITY_UNITS_ID' => 13, 'SENSOR_TYPE_ID' => 14, 'SIGNAL_TYPE' => 15, 'TEMP_UNITS_ID' => 16, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/SensorModelMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.SensorModelMapBuilder');
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
			$map = SensorModelPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. SensorModelPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(SensorModelPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(SensorModelPeer::SENSOR_MODEL_ID);

		$criteria->addSelectColumn(SensorModelPeer::DELETED);

		$criteria->addSelectColumn(SensorModelPeer::DESCRIPTION);

		$criteria->addSelectColumn(SensorModelPeer::MANUFACTURER);

		$criteria->addSelectColumn(SensorModelPeer::MAX_MEASURED_VALUE);

		$criteria->addSelectColumn(SensorModelPeer::MAX_OP_TEMP);

		$criteria->addSelectColumn(SensorModelPeer::MEASURED_VALUE_UNITS_ID);

		$criteria->addSelectColumn(SensorModelPeer::MIN_MEASURED_VALUE);

		$criteria->addSelectColumn(SensorModelPeer::MIN_OP_TEMP);

		$criteria->addSelectColumn(SensorModelPeer::MODEL);

		$criteria->addSelectColumn(SensorModelPeer::NAME);

		$criteria->addSelectColumn(SensorModelPeer::NOTE);

		$criteria->addSelectColumn(SensorModelPeer::SENSITIVITY);

		$criteria->addSelectColumn(SensorModelPeer::SENSITIVITY_UNITS_ID);

		$criteria->addSelectColumn(SensorModelPeer::SENSOR_TYPE_ID);

		$criteria->addSelectColumn(SensorModelPeer::SIGNAL_TYPE);

		$criteria->addSelectColumn(SensorModelPeer::TEMP_UNITS_ID);

	}

	const COUNT = 'COUNT(SENSOR_MODEL.SENSOR_MODEL_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT SENSOR_MODEL.SENSOR_MODEL_ID)';

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
			$criteria->addSelectColumn(SensorModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = SensorModelPeer::doSelectRS($criteria, $con);
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
	 * @return     SensorModel
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = SensorModelPeer::doSelect($critcopy, $con);
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
		return SensorModelPeer::populateObjects(SensorModelPeer::doSelectRS($criteria, $con));
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
			SensorModelPeer::addSelectColumns($criteria);
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
		$cls = SensorModelPeer::getOMClass();
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
			$criteria->addSelectColumn(SensorModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorModelPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$rs = SensorModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByMeasuredValueUnitsId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByMeasuredValueUnitsId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorModelPeer::MEASURED_VALUE_UNITS_ID, MeasurementUnitPeer::ID);

		$rs = SensorModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedBySensitivityUnitsId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedBySensitivityUnitsId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorModelPeer::SENSITIVITY_UNITS_ID, MeasurementUnitPeer::ID);

		$rs = SensorModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByTempUnitsId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByTempUnitsId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorModelPeer::TEMP_UNITS_ID, MeasurementUnitPeer::ID);

		$rs = SensorModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SensorModel objects pre-filled with their SensorType objects.
	 *
	 * @return     array Array of SensorModel objects.
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

		SensorModelPeer::addSelectColumns($c);
		$startcol = (SensorModelPeer::NUM_COLUMNS - SensorModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		SensorTypePeer::addSelectColumns($c);

		$c->addJoin(SensorModelPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorModelPeer::getOMClass();

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
					$temp_obj2->addSensorModel($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initSensorModels();
				$obj2->addSensorModel($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of SensorModel objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of SensorModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByMeasuredValueUnitsId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorModelPeer::addSelectColumns($c);
		$startcol = (SensorModelPeer::NUM_COLUMNS - SensorModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(SensorModelPeer::MEASURED_VALUE_UNITS_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByMeasuredValueUnitsId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addSensorModelRelatedByMeasuredValueUnitsId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initSensorModelsRelatedByMeasuredValueUnitsId();
				$obj2->addSensorModelRelatedByMeasuredValueUnitsId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of SensorModel objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of SensorModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedBySensitivityUnitsId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorModelPeer::addSelectColumns($c);
		$startcol = (SensorModelPeer::NUM_COLUMNS - SensorModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(SensorModelPeer::SENSITIVITY_UNITS_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedBySensitivityUnitsId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addSensorModelRelatedBySensitivityUnitsId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initSensorModelsRelatedBySensitivityUnitsId();
				$obj2->addSensorModelRelatedBySensitivityUnitsId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of SensorModel objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of SensorModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByTempUnitsId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorModelPeer::addSelectColumns($c);
		$startcol = (SensorModelPeer::NUM_COLUMNS - SensorModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(SensorModelPeer::TEMP_UNITS_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByTempUnitsId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addSensorModelRelatedByTempUnitsId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initSensorModelsRelatedByTempUnitsId();
				$obj2->addSensorModelRelatedByTempUnitsId($obj1); //CHECKME
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
			$criteria->addSelectColumn(SensorModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorModelPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$criteria->addJoin(SensorModelPeer::MEASURED_VALUE_UNITS_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(SensorModelPeer::SENSITIVITY_UNITS_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(SensorModelPeer::TEMP_UNITS_ID, MeasurementUnitPeer::ID);

		$rs = SensorModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SensorModel objects pre-filled with all related objects.
	 *
	 * @return     array Array of SensorModel objects.
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

		SensorModelPeer::addSelectColumns($c);
		$startcol2 = (SensorModelPeer::NUM_COLUMNS - SensorModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SensorTypePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SensorTypePeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(SensorModelPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$c->addJoin(SensorModelPeer::MEASURED_VALUE_UNITS_ID, MeasurementUnitPeer::ID);

		$c->addJoin(SensorModelPeer::SENSITIVITY_UNITS_ID, MeasurementUnitPeer::ID);

		$c->addJoin(SensorModelPeer::TEMP_UNITS_ID, MeasurementUnitPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorModelPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined SensorType rows
	
			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSensorType(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSensorModel($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initSensorModels();
				$obj2->addSensorModel($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMeasurementUnitRelatedByMeasuredValueUnitsId(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addSensorModelRelatedByMeasuredValueUnitsId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initSensorModelsRelatedByMeasuredValueUnitsId();
				$obj3->addSensorModelRelatedByMeasuredValueUnitsId($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnitRelatedBySensitivityUnitsId(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addSensorModelRelatedBySensitivityUnitsId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initSensorModelsRelatedBySensitivityUnitsId();
				$obj4->addSensorModelRelatedBySensitivityUnitsId($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5 = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnitRelatedByTempUnitsId(); // CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addSensorModelRelatedByTempUnitsId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj5->initSensorModelsRelatedByTempUnitsId();
				$obj5->addSensorModelRelatedByTempUnitsId($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
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
			$criteria->addSelectColumn(SensorModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorModelPeer::MEASURED_VALUE_UNITS_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(SensorModelPeer::SENSITIVITY_UNITS_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(SensorModelPeer::TEMP_UNITS_ID, MeasurementUnitPeer::ID);

		$rs = SensorModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByMeasuredValueUnitsId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByMeasuredValueUnitsId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorModelPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$rs = SensorModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedBySensitivityUnitsId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedBySensitivityUnitsId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorModelPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$rs = SensorModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByTempUnitsId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByTempUnitsId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SensorModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SensorModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SensorModelPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);

		$rs = SensorModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SensorModel objects pre-filled with all related objects except SensorType.
	 *
	 * @return     array Array of SensorModel objects.
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

		SensorModelPeer::addSelectColumns($c);
		$startcol2 = (SensorModelPeer::NUM_COLUMNS - SensorModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(SensorModelPeer::MEASURED_VALUE_UNITS_ID, MeasurementUnitPeer::ID);

		$c->addJoin(SensorModelPeer::SENSITIVITY_UNITS_ID, MeasurementUnitPeer::ID);

		$c->addJoin(SensorModelPeer::TEMP_UNITS_ID, MeasurementUnitPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByMeasuredValueUnitsId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSensorModelRelatedByMeasuredValueUnitsId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initSensorModelsRelatedByMeasuredValueUnitsId();
				$obj2->addSensorModelRelatedByMeasuredValueUnitsId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMeasurementUnitRelatedBySensitivityUnitsId(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addSensorModelRelatedBySensitivityUnitsId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initSensorModelsRelatedBySensitivityUnitsId();
				$obj3->addSensorModelRelatedBySensitivityUnitsId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnitRelatedByTempUnitsId(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addSensorModelRelatedByTempUnitsId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initSensorModelsRelatedByTempUnitsId();
				$obj4->addSensorModelRelatedByTempUnitsId($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of SensorModel objects pre-filled with all related objects except MeasurementUnitRelatedByMeasuredValueUnitsId.
	 *
	 * @return     array Array of SensorModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByMeasuredValueUnitsId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorModelPeer::addSelectColumns($c);
		$startcol2 = (SensorModelPeer::NUM_COLUMNS - SensorModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SensorTypePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SensorTypePeer::NUM_COLUMNS;

		$c->addJoin(SensorModelPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSensorModel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initSensorModels();
				$obj2->addSensorModel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of SensorModel objects pre-filled with all related objects except MeasurementUnitRelatedBySensitivityUnitsId.
	 *
	 * @return     array Array of SensorModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedBySensitivityUnitsId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorModelPeer::addSelectColumns($c);
		$startcol2 = (SensorModelPeer::NUM_COLUMNS - SensorModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SensorTypePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SensorTypePeer::NUM_COLUMNS;

		$c->addJoin(SensorModelPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSensorModel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initSensorModels();
				$obj2->addSensorModel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of SensorModel objects pre-filled with all related objects except MeasurementUnitRelatedByTempUnitsId.
	 *
	 * @return     array Array of SensorModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByTempUnitsId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SensorModelPeer::addSelectColumns($c);
		$startcol2 = (SensorModelPeer::NUM_COLUMNS - SensorModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SensorTypePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SensorTypePeer::NUM_COLUMNS;

		$c->addJoin(SensorModelPeer::SENSOR_TYPE_ID, SensorTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SensorModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SensorTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSensorType(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSensorModel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initSensorModels();
				$obj2->addSensorModel($obj1);
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
		return SensorModelPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a SensorModel or Criteria object.
	 *
	 * @param      mixed $values Criteria or SensorModel object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from SensorModel object
		}

		$criteria->remove(SensorModelPeer::SENSOR_MODEL_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a SensorModel or Criteria object.
	 *
	 * @param      mixed $values Criteria or SensorModel object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(SensorModelPeer::SENSOR_MODEL_ID);
			$selectCriteria->add(SensorModelPeer::SENSOR_MODEL_ID, $criteria->remove(SensorModelPeer::SENSOR_MODEL_ID), $comparison);

		} else { // $values is SensorModel object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the SENSOR_MODEL table.
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
			$affectedRows += BasePeer::doDeleteAll(SensorModelPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a SensorModel or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or SensorModel object or primary key or array of primary keys
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
			$con = Propel::getConnection(SensorModelPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof SensorModel) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(SensorModelPeer::SENSOR_MODEL_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given SensorModel object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      SensorModel $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(SensorModel $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(SensorModelPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(SensorModelPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::DELETED))
			$columns[SensorModelPeer::DELETED] = $obj->getDeleted();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::DESCRIPTION))
			$columns[SensorModelPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::MANUFACTURER))
			$columns[SensorModelPeer::MANUFACTURER] = $obj->getManufacturer();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::MAX_MEASURED_VALUE))
			$columns[SensorModelPeer::MAX_MEASURED_VALUE] = $obj->getMaxMeasuredValue();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::MAX_OP_TEMP))
			$columns[SensorModelPeer::MAX_OP_TEMP] = $obj->getMaxOpTemp();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::MEASURED_VALUE_UNITS_ID))
			$columns[SensorModelPeer::MEASURED_VALUE_UNITS_ID] = $obj->getMeasuredValueUnitsId();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::MIN_MEASURED_VALUE))
			$columns[SensorModelPeer::MIN_MEASURED_VALUE] = $obj->getMinMeasuredValue();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::MIN_OP_TEMP))
			$columns[SensorModelPeer::MIN_OP_TEMP] = $obj->getMinOpTemp();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::MODEL))
			$columns[SensorModelPeer::MODEL] = $obj->getModel();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::NAME))
			$columns[SensorModelPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::NOTE))
			$columns[SensorModelPeer::NOTE] = $obj->getNote();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::SENSITIVITY))
			$columns[SensorModelPeer::SENSITIVITY] = $obj->getSensitivity();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::SENSITIVITY_UNITS_ID))
			$columns[SensorModelPeer::SENSITIVITY_UNITS_ID] = $obj->getSensitivityUnitsId();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::SENSOR_TYPE_ID))
			$columns[SensorModelPeer::SENSOR_TYPE_ID] = $obj->getSensorTypeId();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::SIGNAL_TYPE))
			$columns[SensorModelPeer::SIGNAL_TYPE] = $obj->getSignalType();

		if ($obj->isNew() || $obj->isColumnModified(SensorModelPeer::TEMP_UNITS_ID))
			$columns[SensorModelPeer::TEMP_UNITS_ID] = $obj->getTempUnitsId();

		}

		return BasePeer::doValidate(SensorModelPeer::DATABASE_NAME, SensorModelPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     SensorModel
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(SensorModelPeer::DATABASE_NAME);

		$criteria->add(SensorModelPeer::SENSOR_MODEL_ID, $pk);


		$v = SensorModelPeer::doSelect($criteria, $con);

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
			$criteria->add(SensorModelPeer::SENSOR_MODEL_ID, $pks, Criteria::IN);
			$objs = SensorModelPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseSensorModelPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseSensorModelPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/SensorModelMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.SensorModelMapBuilder');
}
