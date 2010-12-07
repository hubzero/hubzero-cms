<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by ExperimentPeer::getOMClass()
include_once 'lib/data/Experiment.php';

/**
 * Base static class for performing query and update operations on the 'EXPERIMENT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseExperimentPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'EXPERIMENT';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.Experiment';

	/** The total number of columns. */
	const NUM_COLUMNS = 15;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the EXPID field */
	const EXPID = 'EXPERIMENT.EXPID';

	/** the column name for the CURATION_STATUS field */
	const CURATION_STATUS = 'EXPERIMENT.CURATION_STATUS';

	/** the column name for the DELETED field */
	const DELETED = 'EXPERIMENT.DELETED';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'EXPERIMENT.DESCRIPTION';

	/** the column name for the END_DATE field */
	const END_DATE = 'EXPERIMENT.END_DATE';

	/** the column name for the EXPERIMENT_DOMAIN_ID field */
	const EXPERIMENT_DOMAIN_ID = 'EXPERIMENT.EXPERIMENT_DOMAIN_ID';

	/** the column name for the EXP_TYPE_ID field */
	const EXP_TYPE_ID = 'EXPERIMENT.EXP_TYPE_ID';

	/** the column name for the NAME field */
	const NAME = 'EXPERIMENT.NAME';

	/** the column name for the OBJECTIVE field */
	const OBJECTIVE = 'EXPERIMENT.OBJECTIVE';

	/** the column name for the PROJID field */
	const PROJID = 'EXPERIMENT.PROJID';

	/** the column name for the START_DATE field */
	const START_DATE = 'EXPERIMENT.START_DATE';

	/** the column name for the STATUS field */
	const STATUS = 'EXPERIMENT.STATUS';

	/** the column name for the TITLE field */
	const TITLE = 'EXPERIMENT.TITLE';

	/** the column name for the VIEWABLE field */
	const VIEWABLE = 'EXPERIMENT.VIEWABLE';

	/** the column name for the CREATOR_ID field */
	const CREATOR_ID = 'EXPERIMENT.CREATOR_ID';

	/** A key representing a particular subclass */
	const CLASSKEY_0 = '0';

	/** A key representing a particular subclass */
	const CLASSKEY_EXPERIMENT = '0';

	/** A class that can be returned by this peer. */
	const CLASSNAME_0 = 'lib.data.Experiment';

	/** A key representing a particular subclass */
	const CLASSKEY_1 = '1';

	/** A key representing a particular subclass */
	const CLASSKEY_UNSTRUCTUREDEXPERIMENT = '1';

	/** A class that can be returned by this peer. */
	const CLASSNAME_1 = 'lib.data.UnstructuredExperiment';

	/** A key representing a particular subclass */
	const CLASSKEY_2 = '2';

	/** A key representing a particular subclass */
	const CLASSKEY_STRUCTUREDEXPERIMENT = '2';

	/** A class that can be returned by this peer. */
	const CLASSNAME_2 = 'lib.data.StructuredExperiment';

	/** A key representing a particular subclass */
	const CLASSKEY_3 = '3';

	/** A key representing a particular subclass */
	const CLASSKEY_SIMULATION = '3';

	/** A class that can be returned by this peer. */
	const CLASSNAME_3 = 'lib.data.Simulation';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CurationStatus', 'Deleted', 'Description', 'EndDate', 'ExperimentDomainId', 'ExperimentTypeId', 'Name', 'Objective', 'ProjectId', 'StartDate', 'Status', 'Title', 'View', 'CreatorId', ),
		BasePeer::TYPE_COLNAME => array (ExperimentPeer::EXPID, ExperimentPeer::CURATION_STATUS, ExperimentPeer::DELETED, ExperimentPeer::DESCRIPTION, ExperimentPeer::END_DATE, ExperimentPeer::EXPERIMENT_DOMAIN_ID, ExperimentPeer::EXP_TYPE_ID, ExperimentPeer::NAME, ExperimentPeer::OBJECTIVE, ExperimentPeer::PROJID, ExperimentPeer::START_DATE, ExperimentPeer::STATUS, ExperimentPeer::TITLE, ExperimentPeer::VIEWABLE, ExperimentPeer::CREATOR_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('EXPID', 'CURATION_STATUS', 'DELETED', 'DESCRIPTION', 'END_DATE', 'EXPERIMENT_DOMAIN_ID', 'EXP_TYPE_ID', 'NAME', 'OBJECTIVE', 'PROJID', 'START_DATE', 'STATUS', 'TITLE', 'VIEWABLE', 'CREATOR_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CurationStatus' => 1, 'Deleted' => 2, 'Description' => 3, 'EndDate' => 4, 'ExperimentDomainId' => 5, 'ExperimentTypeId' => 6, 'Name' => 7, 'Objective' => 8, 'ProjectId' => 9, 'StartDate' => 10, 'Status' => 11, 'Title' => 12, 'View' => 13, 'CreatorId' => 14, ),
		BasePeer::TYPE_COLNAME => array (ExperimentPeer::EXPID => 0, ExperimentPeer::CURATION_STATUS => 1, ExperimentPeer::DELETED => 2, ExperimentPeer::DESCRIPTION => 3, ExperimentPeer::END_DATE => 4, ExperimentPeer::EXPERIMENT_DOMAIN_ID => 5, ExperimentPeer::EXP_TYPE_ID => 6, ExperimentPeer::NAME => 7, ExperimentPeer::OBJECTIVE => 8, ExperimentPeer::PROJID => 9, ExperimentPeer::START_DATE => 10, ExperimentPeer::STATUS => 11, ExperimentPeer::TITLE => 12, ExperimentPeer::VIEWABLE => 13, ExperimentPeer::CREATOR_ID => 14, ),
		BasePeer::TYPE_FIELDNAME => array ('EXPID' => 0, 'CURATION_STATUS' => 1, 'DELETED' => 2, 'DESCRIPTION' => 3, 'END_DATE' => 4, 'EXPERIMENT_DOMAIN_ID' => 5, 'EXP_TYPE_ID' => 6, 'NAME' => 7, 'OBJECTIVE' => 8, 'PROJID' => 9, 'START_DATE' => 10, 'STATUS' => 11, 'TITLE' => 12, 'VIEWABLE' => 13, 'CREATOR_ID' => 14, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/ExperimentMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.ExperimentMapBuilder');
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
			$map = ExperimentPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. ExperimentPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(ExperimentPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(ExperimentPeer::EXPID);

		$criteria->addSelectColumn(ExperimentPeer::CURATION_STATUS);

		$criteria->addSelectColumn(ExperimentPeer::DELETED);

		$criteria->addSelectColumn(ExperimentPeer::DESCRIPTION);

		$criteria->addSelectColumn(ExperimentPeer::END_DATE);

		$criteria->addSelectColumn(ExperimentPeer::EXPERIMENT_DOMAIN_ID);

		$criteria->addSelectColumn(ExperimentPeer::EXP_TYPE_ID);

		$criteria->addSelectColumn(ExperimentPeer::NAME);

		$criteria->addSelectColumn(ExperimentPeer::OBJECTIVE);

		$criteria->addSelectColumn(ExperimentPeer::PROJID);

		$criteria->addSelectColumn(ExperimentPeer::START_DATE);

		$criteria->addSelectColumn(ExperimentPeer::STATUS);

		$criteria->addSelectColumn(ExperimentPeer::TITLE);

		$criteria->addSelectColumn(ExperimentPeer::VIEWABLE);

		$criteria->addSelectColumn(ExperimentPeer::CREATOR_ID);

	}

	const COUNT = 'COUNT(EXPERIMENT.EXPID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT EXPERIMENT.EXPID)';

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
			$criteria->addSelectColumn(ExperimentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ExperimentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = ExperimentPeer::doSelectRS($criteria, $con);
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
	 * @return     Experiment
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = ExperimentPeer::doSelect($critcopy, $con);
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
		return ExperimentPeer::populateObjects(ExperimentPeer::doSelectRS($criteria, $con));
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
			ExperimentPeer::addSelectColumns($criteria);
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
			$cls = Propel::import(ExperimentPeer::getOMClass($rs, 1));
			$obj = new $cls();
			$obj->hydrate($rs);
			$results[] = $obj;
			
		}
		return $results;
	}

	/**
	 * Returns the number of rows matching criteria, joining the related ExperimentDomain table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinExperimentDomain(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ExperimentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ExperimentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ExperimentPeer::EXPERIMENT_DOMAIN_ID, ExperimentDomainPeer::ID);

		$rs = ExperimentPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Project table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinProject(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ExperimentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ExperimentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ExperimentPeer::PROJID, ProjectPeer::PROJID);

		$rs = ExperimentPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Person table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinPerson(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ExperimentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ExperimentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ExperimentPeer::CREATOR_ID, PersonPeer::ID);

		$rs = ExperimentPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Experiment objects pre-filled with their ExperimentDomain objects.
	 *
	 * @return     array Array of Experiment objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinExperimentDomain(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ExperimentPeer::addSelectColumns($c);
		$startcol = (ExperimentPeer::NUM_COLUMNS - ExperimentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		ExperimentDomainPeer::addSelectColumns($c);

		$c->addJoin(ExperimentPeer::EXPERIMENT_DOMAIN_ID, ExperimentDomainPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ExperimentPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ExperimentDomainPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getExperimentDomain(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addExperiment($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initExperiments();
				$obj2->addExperiment($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Experiment objects pre-filled with their Project objects.
	 *
	 * @return     array Array of Experiment objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinProject(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ExperimentPeer::addSelectColumns($c);
		$startcol = (ExperimentPeer::NUM_COLUMNS - ExperimentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		ProjectPeer::addSelectColumns($c);

		$c->addJoin(ExperimentPeer::PROJID, ProjectPeer::PROJID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ExperimentPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ProjectPeer::getOMClass($rs, $startcol);

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getProject(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addExperiment($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initExperiments();
				$obj2->addExperiment($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Experiment objects pre-filled with their Person objects.
	 *
	 * @return     array Array of Experiment objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinPerson(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ExperimentPeer::addSelectColumns($c);
		$startcol = (ExperimentPeer::NUM_COLUMNS - ExperimentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		PersonPeer::addSelectColumns($c);

		$c->addJoin(ExperimentPeer::CREATOR_ID, PersonPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ExperimentPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = PersonPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getPerson(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addExperiment($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initExperiments();
				$obj2->addExperiment($obj1); //CHECKME
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
			$criteria->addSelectColumn(ExperimentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ExperimentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ExperimentPeer::EXPERIMENT_DOMAIN_ID, ExperimentDomainPeer::ID);

		$criteria->addJoin(ExperimentPeer::PROJID, ProjectPeer::PROJID);

		$criteria->addJoin(ExperimentPeer::CREATOR_ID, PersonPeer::ID);

		$rs = ExperimentPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Experiment objects pre-filled with all related objects.
	 *
	 * @return     array Array of Experiment objects.
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

		ExperimentPeer::addSelectColumns($c);
		$startcol2 = (ExperimentPeer::NUM_COLUMNS - ExperimentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		ExperimentDomainPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + ExperimentDomainPeer::NUM_COLUMNS;

		ProjectPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ProjectPeer::NUM_COLUMNS;

		PersonPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + PersonPeer::NUM_COLUMNS;

		$c->addJoin(ExperimentPeer::EXPERIMENT_DOMAIN_ID, ExperimentDomainPeer::ID);

		$c->addJoin(ExperimentPeer::PROJID, ProjectPeer::PROJID);

		$c->addJoin(ExperimentPeer::CREATOR_ID, PersonPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ExperimentPeer::getOMClass($rs, 1);


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined ExperimentDomain rows
	
			$omClass = ExperimentDomainPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getExperimentDomain(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addExperiment($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initExperiments();
				$obj2->addExperiment($obj1);
			}


				// Add objects for joined Project rows
	
			$omClass = ProjectPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getProject(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addExperiment($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initExperiments();
				$obj3->addExperiment($obj1);
			}


				// Add objects for joined Person rows
	
			$omClass = PersonPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getPerson(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addExperiment($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initExperiments();
				$obj4->addExperiment($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related ExperimentDomain table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptExperimentDomain(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ExperimentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ExperimentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ExperimentPeer::PROJID, ProjectPeer::PROJID);

		$criteria->addJoin(ExperimentPeer::CREATOR_ID, PersonPeer::ID);

		$rs = ExperimentPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Project table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptProject(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ExperimentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ExperimentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ExperimentPeer::EXPERIMENT_DOMAIN_ID, ExperimentDomainPeer::ID);

		$criteria->addJoin(ExperimentPeer::CREATOR_ID, PersonPeer::ID);

		$rs = ExperimentPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Person table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptPerson(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ExperimentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ExperimentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ExperimentPeer::EXPERIMENT_DOMAIN_ID, ExperimentDomainPeer::ID);

		$criteria->addJoin(ExperimentPeer::PROJID, ProjectPeer::PROJID);

		$rs = ExperimentPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Experiment objects pre-filled with all related objects except ExperimentDomain.
	 *
	 * @return     array Array of Experiment objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptExperimentDomain(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ExperimentPeer::addSelectColumns($c);
		$startcol2 = (ExperimentPeer::NUM_COLUMNS - ExperimentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		ProjectPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + ProjectPeer::NUM_COLUMNS;

		PersonPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + PersonPeer::NUM_COLUMNS;

		$c->addJoin(ExperimentPeer::PROJID, ProjectPeer::PROJID);

		$c->addJoin(ExperimentPeer::CREATOR_ID, PersonPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ExperimentPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ProjectPeer::getOMClass($rs, $startcol2);


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getProject(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addExperiment($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initExperiments();
				$obj2->addExperiment($obj1);
			}

			$omClass = PersonPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getPerson(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addExperiment($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initExperiments();
				$obj3->addExperiment($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Experiment objects pre-filled with all related objects except Project.
	 *
	 * @return     array Array of Experiment objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptProject(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ExperimentPeer::addSelectColumns($c);
		$startcol2 = (ExperimentPeer::NUM_COLUMNS - ExperimentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		ExperimentDomainPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + ExperimentDomainPeer::NUM_COLUMNS;

		PersonPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + PersonPeer::NUM_COLUMNS;

		$c->addJoin(ExperimentPeer::EXPERIMENT_DOMAIN_ID, ExperimentDomainPeer::ID);

		$c->addJoin(ExperimentPeer::CREATOR_ID, PersonPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ExperimentPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ExperimentDomainPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getExperimentDomain(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addExperiment($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initExperiments();
				$obj2->addExperiment($obj1);
			}

			$omClass = PersonPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getPerson(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addExperiment($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initExperiments();
				$obj3->addExperiment($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Experiment objects pre-filled with all related objects except Person.
	 *
	 * @return     array Array of Experiment objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptPerson(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ExperimentPeer::addSelectColumns($c);
		$startcol2 = (ExperimentPeer::NUM_COLUMNS - ExperimentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		ExperimentDomainPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + ExperimentDomainPeer::NUM_COLUMNS;

		ProjectPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ProjectPeer::NUM_COLUMNS;

		$c->addJoin(ExperimentPeer::EXPERIMENT_DOMAIN_ID, ExperimentDomainPeer::ID);

		$c->addJoin(ExperimentPeer::PROJID, ProjectPeer::PROJID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ExperimentPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ExperimentDomainPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getExperimentDomain(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addExperiment($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initExperiments();
				$obj2->addExperiment($obj1);
			}

			$omClass = ProjectPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getProject(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addExperiment($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initExperiments();
				$obj3->addExperiment($obj1);
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
			$classKey = $rs->getString($colnum - 1 + 7);

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

				case self::CLASSKEY_3:
					$omClass = self::CLASSNAME_3;
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
	 * Method perform an INSERT on the database, given a Experiment or Criteria object.
	 *
	 * @param      mixed $values Criteria or Experiment object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from Experiment object
		}

		$criteria->remove(ExperimentPeer::EXPID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a Experiment or Criteria object.
	 *
	 * @param      mixed $values Criteria or Experiment object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(ExperimentPeer::EXPID);
			$selectCriteria->add(ExperimentPeer::EXPID, $criteria->remove(ExperimentPeer::EXPID), $comparison);

		} else { // $values is Experiment object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the EXPERIMENT table.
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
			$affectedRows += BasePeer::doDeleteAll(ExperimentPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a Experiment or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Experiment object or primary key or array of primary keys
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
			$con = Propel::getConnection(ExperimentPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof Experiment) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(ExperimentPeer::EXPID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given Experiment object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Experiment $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(Experiment $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(ExperimentPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(ExperimentPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::CURATION_STATUS))
			$columns[ExperimentPeer::CURATION_STATUS] = $obj->getCurationStatus();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::DELETED))
			$columns[ExperimentPeer::DELETED] = $obj->getDeleted();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::DESCRIPTION))
			$columns[ExperimentPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::END_DATE))
			$columns[ExperimentPeer::END_DATE] = $obj->getEndDate();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::EXPERIMENT_DOMAIN_ID))
			$columns[ExperimentPeer::EXPERIMENT_DOMAIN_ID] = $obj->getExperimentDomainId();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::EXP_TYPE_ID))
			$columns[ExperimentPeer::EXP_TYPE_ID] = $obj->getExperimentTypeId();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::NAME))
			$columns[ExperimentPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::OBJECTIVE))
			$columns[ExperimentPeer::OBJECTIVE] = $obj->getObjective();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::PROJID))
			$columns[ExperimentPeer::PROJID] = $obj->getProjectId();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::START_DATE))
			$columns[ExperimentPeer::START_DATE] = $obj->getStartDate();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::STATUS))
			$columns[ExperimentPeer::STATUS] = $obj->getStatus();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::TITLE))
			$columns[ExperimentPeer::TITLE] = $obj->getTitle();

		if ($obj->isNew() || $obj->isColumnModified(ExperimentPeer::VIEWABLE))
			$columns[ExperimentPeer::VIEWABLE] = $obj->getView();

		}

		return BasePeer::doValidate(ExperimentPeer::DATABASE_NAME, ExperimentPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     Experiment
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(ExperimentPeer::DATABASE_NAME);

		$criteria->add(ExperimentPeer::EXPID, $pk);


		$v = ExperimentPeer::doSelect($criteria, $con);

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
			$criteria->add(ExperimentPeer::EXPID, $pks, Criteria::IN);
			$objs = ExperimentPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseExperimentPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseExperimentPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/ExperimentMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.ExperimentMapBuilder');
}
