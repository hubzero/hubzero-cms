<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by CalibrationPeer::getOMClass()
include_once 'lib/data/Calibration.php';

/**
 * Base static class for performing query and update operations on the 'CALIBRATION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseCalibrationPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'CALIBRATION';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.Calibration';

	/** The total number of columns. */
	const NUM_COLUMNS = 16;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the CALIB_ID field */
	const CALIB_ID = 'CALIBRATION.CALIB_ID';

	/** the column name for the ADJUSTMENTS field */
	const ADJUSTMENTS = 'CALIBRATION.ADJUSTMENTS';

	/** the column name for the CALIB_DATE field */
	const CALIB_DATE = 'CALIBRATION.CALIB_DATE';

	/** the column name for the CALIB_FACTOR field */
	const CALIB_FACTOR = 'CALIBRATION.CALIB_FACTOR';

	/** the column name for the CALIB_FACTOR_UNITS field */
	const CALIB_FACTOR_UNITS = 'CALIBRATION.CALIB_FACTOR_UNITS';

	/** the column name for the CALIBRATOR field */
	const CALIBRATOR = 'CALIBRATION.CALIBRATOR';

	/** the column name for the DELETED field */
	const DELETED = 'CALIBRATION.DELETED';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'CALIBRATION.DESCRIPTION';

	/** the column name for the MAX_MEASURED_VALUE field */
	const MAX_MEASURED_VALUE = 'CALIBRATION.MAX_MEASURED_VALUE';

	/** the column name for the MEASURED_VALUE_UNITS field */
	const MEASURED_VALUE_UNITS = 'CALIBRATION.MEASURED_VALUE_UNITS';

	/** the column name for the MIN_MEASURED_VALUE field */
	const MIN_MEASURED_VALUE = 'CALIBRATION.MIN_MEASURED_VALUE';

	/** the column name for the REFERENCE field */
	const REFERENCE = 'CALIBRATION.REFERENCE';

	/** the column name for the REFERENCE_UNITS field */
	const REFERENCE_UNITS = 'CALIBRATION.REFERENCE_UNITS';

	/** the column name for the SENSITIVITY field */
	const SENSITIVITY = 'CALIBRATION.SENSITIVITY';

	/** the column name for the SENSITIVITY_UNITS field */
	const SENSITIVITY_UNITS = 'CALIBRATION.SENSITIVITY_UNITS';

	/** the column name for the SENSOR_ID field */
	const SENSOR_ID = 'CALIBRATION.SENSOR_ID';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Adjustments', 'CalibDate', 'CalibFactor', 'CalibFactorUnits', 'Calibrator', 'Deleted', 'Description', 'MaxMeasuredValue', 'MeasuredValueUnits', 'MinMeasuredValue', 'Reference', 'ReferenceUnits', 'Sensitivity', 'SensitivityUnits', 'SensorId', ),
		BasePeer::TYPE_COLNAME => array (CalibrationPeer::CALIB_ID, CalibrationPeer::ADJUSTMENTS, CalibrationPeer::CALIB_DATE, CalibrationPeer::CALIB_FACTOR, CalibrationPeer::CALIB_FACTOR_UNITS, CalibrationPeer::CALIBRATOR, CalibrationPeer::DELETED, CalibrationPeer::DESCRIPTION, CalibrationPeer::MAX_MEASURED_VALUE, CalibrationPeer::MEASURED_VALUE_UNITS, CalibrationPeer::MIN_MEASURED_VALUE, CalibrationPeer::REFERENCE, CalibrationPeer::REFERENCE_UNITS, CalibrationPeer::SENSITIVITY, CalibrationPeer::SENSITIVITY_UNITS, CalibrationPeer::SENSOR_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('CALIB_ID', 'ADJUSTMENTS', 'CALIB_DATE', 'CALIB_FACTOR', 'CALIB_FACTOR_UNITS', 'CALIBRATOR', 'DELETED', 'DESCRIPTION', 'MAX_MEASURED_VALUE', 'MEASURED_VALUE_UNITS', 'MIN_MEASURED_VALUE', 'REFERENCE', 'REFERENCE_UNITS', 'SENSITIVITY', 'SENSITIVITY_UNITS', 'SENSOR_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Adjustments' => 1, 'CalibDate' => 2, 'CalibFactor' => 3, 'CalibFactorUnits' => 4, 'Calibrator' => 5, 'Deleted' => 6, 'Description' => 7, 'MaxMeasuredValue' => 8, 'MeasuredValueUnits' => 9, 'MinMeasuredValue' => 10, 'Reference' => 11, 'ReferenceUnits' => 12, 'Sensitivity' => 13, 'SensitivityUnits' => 14, 'SensorId' => 15, ),
		BasePeer::TYPE_COLNAME => array (CalibrationPeer::CALIB_ID => 0, CalibrationPeer::ADJUSTMENTS => 1, CalibrationPeer::CALIB_DATE => 2, CalibrationPeer::CALIB_FACTOR => 3, CalibrationPeer::CALIB_FACTOR_UNITS => 4, CalibrationPeer::CALIBRATOR => 5, CalibrationPeer::DELETED => 6, CalibrationPeer::DESCRIPTION => 7, CalibrationPeer::MAX_MEASURED_VALUE => 8, CalibrationPeer::MEASURED_VALUE_UNITS => 9, CalibrationPeer::MIN_MEASURED_VALUE => 10, CalibrationPeer::REFERENCE => 11, CalibrationPeer::REFERENCE_UNITS => 12, CalibrationPeer::SENSITIVITY => 13, CalibrationPeer::SENSITIVITY_UNITS => 14, CalibrationPeer::SENSOR_ID => 15, ),
		BasePeer::TYPE_FIELDNAME => array ('CALIB_ID' => 0, 'ADJUSTMENTS' => 1, 'CALIB_DATE' => 2, 'CALIB_FACTOR' => 3, 'CALIB_FACTOR_UNITS' => 4, 'CALIBRATOR' => 5, 'DELETED' => 6, 'DESCRIPTION' => 7, 'MAX_MEASURED_VALUE' => 8, 'MEASURED_VALUE_UNITS' => 9, 'MIN_MEASURED_VALUE' => 10, 'REFERENCE' => 11, 'REFERENCE_UNITS' => 12, 'SENSITIVITY' => 13, 'SENSITIVITY_UNITS' => 14, 'SENSOR_ID' => 15, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/CalibrationMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.CalibrationMapBuilder');
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
			$map = CalibrationPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. CalibrationPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(CalibrationPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(CalibrationPeer::CALIB_ID);

		$criteria->addSelectColumn(CalibrationPeer::ADJUSTMENTS);

		$criteria->addSelectColumn(CalibrationPeer::CALIB_DATE);

		$criteria->addSelectColumn(CalibrationPeer::CALIB_FACTOR);

		$criteria->addSelectColumn(CalibrationPeer::CALIB_FACTOR_UNITS);

		$criteria->addSelectColumn(CalibrationPeer::CALIBRATOR);

		$criteria->addSelectColumn(CalibrationPeer::DELETED);

		$criteria->addSelectColumn(CalibrationPeer::DESCRIPTION);

		$criteria->addSelectColumn(CalibrationPeer::MAX_MEASURED_VALUE);

		$criteria->addSelectColumn(CalibrationPeer::MEASURED_VALUE_UNITS);

		$criteria->addSelectColumn(CalibrationPeer::MIN_MEASURED_VALUE);

		$criteria->addSelectColumn(CalibrationPeer::REFERENCE);

		$criteria->addSelectColumn(CalibrationPeer::REFERENCE_UNITS);

		$criteria->addSelectColumn(CalibrationPeer::SENSITIVITY);

		$criteria->addSelectColumn(CalibrationPeer::SENSITIVITY_UNITS);

		$criteria->addSelectColumn(CalibrationPeer::SENSOR_ID);

	}

	const COUNT = 'COUNT(CALIBRATION.CALIB_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT CALIBRATION.CALIB_ID)';

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
			$criteria->addSelectColumn(CalibrationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CalibrationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = CalibrationPeer::doSelectRS($criteria, $con);
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
	 * @return     Calibration
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = CalibrationPeer::doSelect($critcopy, $con);
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
		return CalibrationPeer::populateObjects(CalibrationPeer::doSelectRS($criteria, $con));
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
			CalibrationPeer::addSelectColumns($criteria);
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
		$cls = CalibrationPeer::getOMClass();
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
			$criteria->addSelectColumn(CalibrationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CalibrationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CalibrationPeer::SENSOR_ID, SensorPeer::SENSOR_ID);

		$rs = CalibrationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Calibration objects pre-filled with their Sensor objects.
	 *
	 * @return     array Array of Calibration objects.
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

		CalibrationPeer::addSelectColumns($c);
		$startcol = (CalibrationPeer::NUM_COLUMNS - CalibrationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		SensorPeer::addSelectColumns($c);

		$c->addJoin(CalibrationPeer::SENSOR_ID, SensorPeer::SENSOR_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CalibrationPeer::getOMClass();

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
					$temp_obj2->addCalibration($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initCalibrations();
				$obj2->addCalibration($obj1); //CHECKME
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
			$criteria->addSelectColumn(CalibrationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CalibrationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CalibrationPeer::SENSOR_ID, SensorPeer::SENSOR_ID);

		$rs = CalibrationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Calibration objects pre-filled with all related objects.
	 *
	 * @return     array Array of Calibration objects.
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

		CalibrationPeer::addSelectColumns($c);
		$startcol2 = (CalibrationPeer::NUM_COLUMNS - CalibrationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SensorPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SensorPeer::NUM_COLUMNS;

		$c->addJoin(CalibrationPeer::SENSOR_ID, SensorPeer::SENSOR_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CalibrationPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined Sensor rows
	
			$omClass = SensorPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSensor(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCalibration($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initCalibrations();
				$obj2->addCalibration($obj1);
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
		return CalibrationPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a Calibration or Criteria object.
	 *
	 * @param      mixed $values Criteria or Calibration object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from Calibration object
		}

		$criteria->remove(CalibrationPeer::CALIB_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a Calibration or Criteria object.
	 *
	 * @param      mixed $values Criteria or Calibration object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(CalibrationPeer::CALIB_ID);
			$selectCriteria->add(CalibrationPeer::CALIB_ID, $criteria->remove(CalibrationPeer::CALIB_ID), $comparison);

		} else { // $values is Calibration object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the CALIBRATION table.
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
			$affectedRows += BasePeer::doDeleteAll(CalibrationPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a Calibration or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Calibration object or primary key or array of primary keys
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
			$con = Propel::getConnection(CalibrationPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof Calibration) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(CalibrationPeer::CALIB_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given Calibration object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Calibration $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(Calibration $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(CalibrationPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(CalibrationPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::ADJUSTMENTS))
			$columns[CalibrationPeer::ADJUSTMENTS] = $obj->getAdjustments();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::CALIBRATOR))
			$columns[CalibrationPeer::CALIBRATOR] = $obj->getCalibrator();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::CALIB_DATE))
			$columns[CalibrationPeer::CALIB_DATE] = $obj->getCalibDate();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::CALIB_FACTOR))
			$columns[CalibrationPeer::CALIB_FACTOR] = $obj->getCalibFactor();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::CALIB_FACTOR_UNITS))
			$columns[CalibrationPeer::CALIB_FACTOR_UNITS] = $obj->getCalibFactorUnits();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::DELETED))
			$columns[CalibrationPeer::DELETED] = $obj->getDeleted();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::DESCRIPTION))
			$columns[CalibrationPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::MAX_MEASURED_VALUE))
			$columns[CalibrationPeer::MAX_MEASURED_VALUE] = $obj->getMaxMeasuredValue();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::MEASURED_VALUE_UNITS))
			$columns[CalibrationPeer::MEASURED_VALUE_UNITS] = $obj->getMeasuredValueUnits();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::MIN_MEASURED_VALUE))
			$columns[CalibrationPeer::MIN_MEASURED_VALUE] = $obj->getMinMeasuredValue();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::REFERENCE))
			$columns[CalibrationPeer::REFERENCE] = $obj->getReference();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::REFERENCE_UNITS))
			$columns[CalibrationPeer::REFERENCE_UNITS] = $obj->getReferenceUnits();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::SENSITIVITY))
			$columns[CalibrationPeer::SENSITIVITY] = $obj->getSensitivity();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::SENSITIVITY_UNITS))
			$columns[CalibrationPeer::SENSITIVITY_UNITS] = $obj->getSensitivityUnits();

		if ($obj->isNew() || $obj->isColumnModified(CalibrationPeer::SENSOR_ID))
			$columns[CalibrationPeer::SENSOR_ID] = $obj->getSensorId();

		}

		return BasePeer::doValidate(CalibrationPeer::DATABASE_NAME, CalibrationPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     Calibration
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(CalibrationPeer::DATABASE_NAME);

		$criteria->add(CalibrationPeer::CALIB_ID, $pk);


		$v = CalibrationPeer::doSelect($criteria, $con);

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
			$criteria->add(CalibrationPeer::CALIB_ID, $pks, Criteria::IN);
			$objs = CalibrationPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseCalibrationPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseCalibrationPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/CalibrationMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.CalibrationMapBuilder');
}
