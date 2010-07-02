<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by TrialPeer::getOMClass()
include_once 'lib/data/Trial.php';

/**
 * Base static class for performing query and update operations on the 'TRIAL' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseTrialPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'TRIAL';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.Trial';

	/** The total number of columns. */
	const NUM_COLUMNS = 19;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the TRIALID field */
	const TRIALID = 'TRIAL.TRIALID';

	/** the column name for the ACCELERATION field */
	const ACCELERATION = 'TRIAL.ACCELERATION';

	/** the column name for the BASE_ACCELERATION field */
	const BASE_ACCELERATION = 'TRIAL.BASE_ACCELERATION';

	/** the column name for the BASE_ACCELERATION_UNIT_ID field */
	const BASE_ACCELERATION_UNIT_ID = 'TRIAL.BASE_ACCELERATION_UNIT_ID';

	/** the column name for the COMPONENT field */
	const COMPONENT = 'TRIAL.COMPONENT';

	/** the column name for the CURATION_STATUS field */
	const CURATION_STATUS = 'TRIAL.CURATION_STATUS';

	/** the column name for the DELETED field */
	const DELETED = 'TRIAL.DELETED';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'TRIAL.DESCRIPTION';

	/** the column name for the END_DATE field */
	const END_DATE = 'TRIAL.END_DATE';

	/** the column name for the EXPID field */
	const EXPID = 'TRIAL.EXPID';

	/** the column name for the MOTION_FILE_ID field */
	const MOTION_FILE_ID = 'TRIAL.MOTION_FILE_ID';

	/** the column name for the MOTION_NAME field */
	const MOTION_NAME = 'TRIAL.MOTION_NAME';

	/** the column name for the NAME field */
	const NAME = 'TRIAL.NAME';

	/** the column name for the OBJECTIVE field */
	const OBJECTIVE = 'TRIAL.OBJECTIVE';

	/** the column name for the START_DATE field */
	const START_DATE = 'TRIAL.START_DATE';

	/** the column name for the STATION field */
	const STATION = 'TRIAL.STATION';

	/** the column name for the STATUS field */
	const STATUS = 'TRIAL.STATUS';

	/** the column name for the TITLE field */
	const TITLE = 'TRIAL.TITLE';

	/** the column name for the TRIAL_TYPE_ID field */
	const TRIAL_TYPE_ID = 'TRIAL.TRIAL_TYPE_ID';

	/** A key representing a particular subclass */
	const CLASSKEY_0 = '0';

	/** A key representing a particular subclass */
	const CLASSKEY_TRIAL = '0';

	/** A class that can be returned by this peer. */
	const CLASSNAME_0 = 'lib.data.Trial';

	/** A key representing a particular subclass */
	const CLASSKEY_1 = '1';

	/** A key representing a particular subclass */
	const CLASSKEY_SIMULATIONRUN = '1';

	/** A class that can be returned by this peer. */
	const CLASSNAME_1 = 'lib.data.SimulationRun';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Acceleration', 'BaseAcceleration', 'BaseAccelerationUnitId', 'Component', 'CurationStatus', 'Deleted', 'Description', 'EndDate', 'ExperimentId', 'MotionFileId', 'MotionName', 'Name', 'Objective', 'StartDate', 'Station', 'Status', 'Title', 'TrialTypeId', ),
		BasePeer::TYPE_COLNAME => array (TrialPeer::TRIALID, TrialPeer::ACCELERATION, TrialPeer::BASE_ACCELERATION, TrialPeer::BASE_ACCELERATION_UNIT_ID, TrialPeer::COMPONENT, TrialPeer::CURATION_STATUS, TrialPeer::DELETED, TrialPeer::DESCRIPTION, TrialPeer::END_DATE, TrialPeer::EXPID, TrialPeer::MOTION_FILE_ID, TrialPeer::MOTION_NAME, TrialPeer::NAME, TrialPeer::OBJECTIVE, TrialPeer::START_DATE, TrialPeer::STATION, TrialPeer::STATUS, TrialPeer::TITLE, TrialPeer::TRIAL_TYPE_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('TRIALID', 'ACCELERATION', 'BASE_ACCELERATION', 'BASE_ACCELERATION_UNIT_ID', 'COMPONENT', 'CURATION_STATUS', 'DELETED', 'DESCRIPTION', 'END_DATE', 'EXPID', 'MOTION_FILE_ID', 'MOTION_NAME', 'NAME', 'OBJECTIVE', 'START_DATE', 'STATION', 'STATUS', 'TITLE', 'TRIAL_TYPE_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Acceleration' => 1, 'BaseAcceleration' => 2, 'BaseAccelerationUnitId' => 3, 'Component' => 4, 'CurationStatus' => 5, 'Deleted' => 6, 'Description' => 7, 'EndDate' => 8, 'ExperimentId' => 9, 'MotionFileId' => 10, 'MotionName' => 11, 'Name' => 12, 'Objective' => 13, 'StartDate' => 14, 'Station' => 15, 'Status' => 16, 'Title' => 17, 'TrialTypeId' => 18, ),
		BasePeer::TYPE_COLNAME => array (TrialPeer::TRIALID => 0, TrialPeer::ACCELERATION => 1, TrialPeer::BASE_ACCELERATION => 2, TrialPeer::BASE_ACCELERATION_UNIT_ID => 3, TrialPeer::COMPONENT => 4, TrialPeer::CURATION_STATUS => 5, TrialPeer::DELETED => 6, TrialPeer::DESCRIPTION => 7, TrialPeer::END_DATE => 8, TrialPeer::EXPID => 9, TrialPeer::MOTION_FILE_ID => 10, TrialPeer::MOTION_NAME => 11, TrialPeer::NAME => 12, TrialPeer::OBJECTIVE => 13, TrialPeer::START_DATE => 14, TrialPeer::STATION => 15, TrialPeer::STATUS => 16, TrialPeer::TITLE => 17, TrialPeer::TRIAL_TYPE_ID => 18, ),
		BasePeer::TYPE_FIELDNAME => array ('TRIALID' => 0, 'ACCELERATION' => 1, 'BASE_ACCELERATION' => 2, 'BASE_ACCELERATION_UNIT_ID' => 3, 'COMPONENT' => 4, 'CURATION_STATUS' => 5, 'DELETED' => 6, 'DESCRIPTION' => 7, 'END_DATE' => 8, 'EXPID' => 9, 'MOTION_FILE_ID' => 10, 'MOTION_NAME' => 11, 'NAME' => 12, 'OBJECTIVE' => 13, 'START_DATE' => 14, 'STATION' => 15, 'STATUS' => 16, 'TITLE' => 17, 'TRIAL_TYPE_ID' => 18, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/TrialMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.TrialMapBuilder');
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
			$map = TrialPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. TrialPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(TrialPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(TrialPeer::TRIALID);

		$criteria->addSelectColumn(TrialPeer::ACCELERATION);

		$criteria->addSelectColumn(TrialPeer::BASE_ACCELERATION);

		$criteria->addSelectColumn(TrialPeer::BASE_ACCELERATION_UNIT_ID);

		$criteria->addSelectColumn(TrialPeer::COMPONENT);

		$criteria->addSelectColumn(TrialPeer::CURATION_STATUS);

		$criteria->addSelectColumn(TrialPeer::DELETED);

		$criteria->addSelectColumn(TrialPeer::DESCRIPTION);

		$criteria->addSelectColumn(TrialPeer::END_DATE);

		$criteria->addSelectColumn(TrialPeer::EXPID);

		$criteria->addSelectColumn(TrialPeer::MOTION_FILE_ID);

		$criteria->addSelectColumn(TrialPeer::MOTION_NAME);

		$criteria->addSelectColumn(TrialPeer::NAME);

		$criteria->addSelectColumn(TrialPeer::OBJECTIVE);

		$criteria->addSelectColumn(TrialPeer::START_DATE);

		$criteria->addSelectColumn(TrialPeer::STATION);

		$criteria->addSelectColumn(TrialPeer::STATUS);

		$criteria->addSelectColumn(TrialPeer::TITLE);

		$criteria->addSelectColumn(TrialPeer::TRIAL_TYPE_ID);

	}

	const COUNT = 'COUNT(TRIAL.TRIALID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT TRIAL.TRIALID)';

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
			$criteria->addSelectColumn(TrialPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TrialPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = TrialPeer::doSelectRS($criteria, $con);
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
	 * @return     Trial
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = TrialPeer::doSelect($critcopy, $con);
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
		return TrialPeer::populateObjects(TrialPeer::doSelectRS($criteria, $con));
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
			TrialPeer::addSelectColumns($criteria);
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
			$cls = Propel::import(TrialPeer::getOMClass($rs, 1));
			$obj = new $cls();
			$obj->hydrate($rs);
			$results[] = $obj;
			
		}
		return $results;
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
			$criteria->addSelectColumn(TrialPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TrialPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TrialPeer::MOTION_FILE_ID, DataFilePeer::ID);

		$rs = TrialPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Experiment table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinExperiment(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(TrialPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TrialPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TrialPeer::EXPID, ExperimentPeer::EXPID);

		$rs = TrialPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(TrialPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TrialPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TrialPeer::BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = TrialPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Trial objects pre-filled with their DataFile objects.
	 *
	 * @return     array Array of Trial objects.
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

		TrialPeer::addSelectColumns($c);
		$startcol = (TrialPeer::NUM_COLUMNS - TrialPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DataFilePeer::addSelectColumns($c);

		$c->addJoin(TrialPeer::MOTION_FILE_ID, DataFilePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TrialPeer::getOMClass($rs, 1);

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
					$temp_obj2->addTrial($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initTrials();
				$obj2->addTrial($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Trial objects pre-filled with their Experiment objects.
	 *
	 * @return     array Array of Trial objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinExperiment(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		TrialPeer::addSelectColumns($c);
		$startcol = (TrialPeer::NUM_COLUMNS - TrialPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		ExperimentPeer::addSelectColumns($c);

		$c->addJoin(TrialPeer::EXPID, ExperimentPeer::EXPID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TrialPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ExperimentPeer::getOMClass($rs, $startcol);

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addTrial($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initTrials();
				$obj2->addTrial($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Trial objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of Trial objects.
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

		TrialPeer::addSelectColumns($c);
		$startcol = (TrialPeer::NUM_COLUMNS - TrialPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(TrialPeer::BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TrialPeer::getOMClass($rs, 1);

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
					$temp_obj2->addTrial($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initTrials();
				$obj2->addTrial($obj1); //CHECKME
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
			$criteria->addSelectColumn(TrialPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TrialPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TrialPeer::MOTION_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(TrialPeer::EXPID, ExperimentPeer::EXPID);

		$criteria->addJoin(TrialPeer::BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = TrialPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Trial objects pre-filled with all related objects.
	 *
	 * @return     array Array of Trial objects.
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

		TrialPeer::addSelectColumns($c);
		$startcol2 = (TrialPeer::NUM_COLUMNS - TrialPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		ExperimentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ExperimentPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(TrialPeer::MOTION_FILE_ID, DataFilePeer::ID);

		$c->addJoin(TrialPeer::EXPID, ExperimentPeer::EXPID);

		$c->addJoin(TrialPeer::BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TrialPeer::getOMClass($rs, 1);


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
				$temp_obj2 = $temp_obj1->getDataFile(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addTrial($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initTrials();
				$obj2->addTrial($obj1);
			}


				// Add objects for joined Experiment rows
	
			$omClass = ExperimentPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getExperiment(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addTrial($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initTrials();
				$obj3->addTrial($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnit(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addTrial($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initTrials();
				$obj4->addTrial($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
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
			$criteria->addSelectColumn(TrialPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TrialPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TrialPeer::EXPID, ExperimentPeer::EXPID);

		$criteria->addJoin(TrialPeer::BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = TrialPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Experiment table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptExperiment(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(TrialPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TrialPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TrialPeer::MOTION_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(TrialPeer::BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = TrialPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(TrialPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TrialPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TrialPeer::MOTION_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(TrialPeer::EXPID, ExperimentPeer::EXPID);

		$rs = TrialPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Trial objects pre-filled with all related objects except DataFile.
	 *
	 * @return     array Array of Trial objects.
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

		TrialPeer::addSelectColumns($c);
		$startcol2 = (TrialPeer::NUM_COLUMNS - TrialPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		ExperimentPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + ExperimentPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(TrialPeer::EXPID, ExperimentPeer::EXPID);

		$c->addJoin(TrialPeer::BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TrialPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ExperimentPeer::getOMClass($rs, $startcol2);


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addTrial($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initTrials();
				$obj2->addTrial($obj1);
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
					$temp_obj3->addTrial($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initTrials();
				$obj3->addTrial($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Trial objects pre-filled with all related objects except Experiment.
	 *
	 * @return     array Array of Trial objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptExperiment(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		TrialPeer::addSelectColumns($c);
		$startcol2 = (TrialPeer::NUM_COLUMNS - TrialPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(TrialPeer::MOTION_FILE_ID, DataFilePeer::ID);

		$c->addJoin(TrialPeer::BASE_ACCELERATION_UNIT_ID, MeasurementUnitPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TrialPeer::getOMClass($rs, 1);

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
					$temp_obj2->addTrial($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initTrials();
				$obj2->addTrial($obj1);
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
					$temp_obj3->addTrial($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initTrials();
				$obj3->addTrial($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Trial objects pre-filled with all related objects except MeasurementUnit.
	 *
	 * @return     array Array of Trial objects.
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

		TrialPeer::addSelectColumns($c);
		$startcol2 = (TrialPeer::NUM_COLUMNS - TrialPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		ExperimentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ExperimentPeer::NUM_COLUMNS;

		$c->addJoin(TrialPeer::MOTION_FILE_ID, DataFilePeer::ID);

		$c->addJoin(TrialPeer::EXPID, ExperimentPeer::EXPID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TrialPeer::getOMClass($rs, 1);

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
					$temp_obj2->addTrial($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initTrials();
				$obj2->addTrial($obj1);
			}

			$omClass = ExperimentPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addTrial($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initTrials();
				$obj3->addTrial($obj1);
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
			$classKey = $rs->getString($colnum - 1 + 19);

			switch($classKey) {

				case self::CLASSKEY_0:
					$omClass = self::CLASSNAME_0;
					break;

				case self::CLASSKEY_1:
					$omClass = self::CLASSNAME_1;
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
	 * Method perform an INSERT on the database, given a Trial or Criteria object.
	 *
	 * @param      mixed $values Criteria or Trial object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from Trial object
		}

		$criteria->remove(TrialPeer::TRIALID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a Trial or Criteria object.
	 *
	 * @param      mixed $values Criteria or Trial object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(TrialPeer::TRIALID);
			$selectCriteria->add(TrialPeer::TRIALID, $criteria->remove(TrialPeer::TRIALID), $comparison);

		} else { // $values is Trial object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the TRIAL table.
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
			$affectedRows += BasePeer::doDeleteAll(TrialPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a Trial or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Trial object or primary key or array of primary keys
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
			$con = Propel::getConnection(TrialPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof Trial) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(TrialPeer::TRIALID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given Trial object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Trial $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(Trial $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(TrialPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(TrialPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::ACCELERATION))
			$columns[TrialPeer::ACCELERATION] = $obj->getAcceleration();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::BASE_ACCELERATION))
			$columns[TrialPeer::BASE_ACCELERATION] = $obj->getBaseAcceleration();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::BASE_ACCELERATION_UNIT_ID))
			$columns[TrialPeer::BASE_ACCELERATION_UNIT_ID] = $obj->getBaseAccelerationUnitId();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::COMPONENT))
			$columns[TrialPeer::COMPONENT] = $obj->getComponent();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::CURATION_STATUS))
			$columns[TrialPeer::CURATION_STATUS] = $obj->getCurationStatus();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::DELETED))
			$columns[TrialPeer::DELETED] = $obj->getDeleted();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::DESCRIPTION))
			$columns[TrialPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::END_DATE))
			$columns[TrialPeer::END_DATE] = $obj->getEndDate();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::EXPID))
			$columns[TrialPeer::EXPID] = $obj->getExperimentId();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::MOTION_FILE_ID))
			$columns[TrialPeer::MOTION_FILE_ID] = $obj->getMotionFileId();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::MOTION_NAME))
			$columns[TrialPeer::MOTION_NAME] = $obj->getMotionName();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::NAME))
			$columns[TrialPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::OBJECTIVE))
			$columns[TrialPeer::OBJECTIVE] = $obj->getObjective();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::START_DATE))
			$columns[TrialPeer::START_DATE] = $obj->getStartDate();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::STATION))
			$columns[TrialPeer::STATION] = $obj->getStation();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::STATUS))
			$columns[TrialPeer::STATUS] = $obj->getStatus();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::TITLE))
			$columns[TrialPeer::TITLE] = $obj->getTitle();

		if ($obj->isNew() || $obj->isColumnModified(TrialPeer::TRIAL_TYPE_ID))
			$columns[TrialPeer::TRIAL_TYPE_ID] = $obj->getTrialTypeId();

		}

		return BasePeer::doValidate(TrialPeer::DATABASE_NAME, TrialPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     Trial
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(TrialPeer::DATABASE_NAME);

		$criteria->add(TrialPeer::TRIALID, $pk);


		$v = TrialPeer::doSelect($criteria, $con);

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
			$criteria->add(TrialPeer::TRIALID, $pks, Criteria::IN);
			$objs = TrialPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseTrialPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseTrialPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/TrialMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.TrialMapBuilder');
}
