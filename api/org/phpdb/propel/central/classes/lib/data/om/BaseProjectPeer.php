<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by ProjectPeer::getOMClass()
include_once 'lib/data/Project.php';

/**
 * Base static class for performing query and update operations on the 'PROJECT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseProjectPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'PROJECT';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.Project';

	/** The total number of columns. */
	const NUM_COLUMNS = 23;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the PROJID field */
	const PROJID = 'PROJECT.PROJID';

	/** the column name for the CONTACT_EMAIL field */
	const CONTACT_EMAIL = 'PROJECT.CONTACT_EMAIL';

	/** the column name for the CONTACT_NAME field */
	const CONTACT_NAME = 'PROJECT.CONTACT_NAME';

	/** the column name for the CURATION_STATUS field */
	const CURATION_STATUS = 'PROJECT.CURATION_STATUS';

	/** the column name for the DELETED field */
	const DELETED = 'PROJECT.DELETED';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'PROJECT.DESCRIPTION';

	/** the column name for the END_DATE field */
	const END_DATE = 'PROJECT.END_DATE';

	/** the column name for the FUNDORG field */
	const FUNDORG = 'PROJECT.FUNDORG';

	/** the column name for the FUNDORGPROJID field */
	const FUNDORGPROJID = 'PROJECT.FUNDORGPROJID';

	/** the column name for the NEES field */
	const NEES = 'PROJECT.NEES';

	/** the column name for the NSFTITLE field */
	const NSFTITLE = 'PROJECT.NSFTITLE';

	/** the column name for the NAME field */
	const NAME = 'PROJECT.NAME';

	/** the column name for the NICKNAME field */
	const NICKNAME = 'PROJECT.NICKNAME';

	/** the column name for the SHORT_TITLE field */
	const SHORT_TITLE = 'PROJECT.SHORT_TITLE';

	/** the column name for the START_DATE field */
	const START_DATE = 'PROJECT.START_DATE';

	/** the column name for the STATUS field */
	const STATUS = 'PROJECT.STATUS';

	/** the column name for the SYSADMIN_EMAIL field */
	const SYSADMIN_EMAIL = 'PROJECT.SYSADMIN_EMAIL';

	/** the column name for the SYSADMIN_NAME field */
	const SYSADMIN_NAME = 'PROJECT.SYSADMIN_NAME';

	/** the column name for the TITLE field */
	const TITLE = 'PROJECT.TITLE';

	/** the column name for the VIEWABLE field */
	const VIEWABLE = 'PROJECT.VIEWABLE';

	/** the column name for the CREATOR_ID field */
	const CREATOR_ID = 'PROJECT.CREATOR_ID';

	/** the column name for the SUPER_PROJECT_ID field */
	const SUPER_PROJECT_ID = 'PROJECT.SUPER_PROJECT_ID';

	/** the column name for the PROJECT_TYPE_ID field */
	const PROJECT_TYPE_ID = 'PROJECT.PROJECT_TYPE_ID';

	/** A key representing a particular subclass */
	const CLASSKEY_0 = '0';

	/** A key representing a particular subclass */
	const CLASSKEY_PROJECT = '0';

	/** A class that can be returned by this peer. */
	const CLASSNAME_0 = 'lib.data.Project';

	/** A key representing a particular subclass */
	const CLASSKEY_1 = '1';

	/** A key representing a particular subclass */
	const CLASSKEY_UNSTRUCTUREDPROJECT = '1';

	/** A class that can be returned by this peer. */
	const CLASSNAME_1 = 'lib.data.UnstructuredProject';

	/** A key representing a particular subclass */
	const CLASSKEY_2 = '2';

	/** A key representing a particular subclass */
	const CLASSKEY_STRUCTUREDPROJECT = '2';

	/** A class that can be returned by this peer. */
	const CLASSNAME_2 = 'lib.data.StructuredProject';

	/** A key representing a particular subclass */
	const CLASSKEY_3 = '3';

	/** A key representing a particular subclass */
	const CLASSKEY_SUPERPROJECT = '3';

	/** A class that can be returned by this peer. */
	const CLASSNAME_3 = 'lib.data.SuperProject';

	/** A key representing a particular subclass */
	const CLASSKEY_4 = '4';

	/** A key representing a particular subclass */
	const CLASSKEY_HYBRIDPROJECT = '4';

	/** A class that can be returned by this peer. */
	const CLASSNAME_4 = 'lib.data.HybridProject';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'ContactEmail', 'ContactName', 'CurationStatus', 'Deleted', 'Description', 'EndDate', 'Fundorg', 'FundorgProjId', 'NEES', 'NSFTitle', 'Name', 'Nickname', 'ShortTitle', 'StartDate', 'Status', 'SysadminEmail', 'SysadminName', 'Title', 'View', 'CreatorId', 'SuperProjectId', 'ProjectTypeId', ),
		BasePeer::TYPE_COLNAME => array (ProjectPeer::PROJID, ProjectPeer::CONTACT_EMAIL, ProjectPeer::CONTACT_NAME, ProjectPeer::CURATION_STATUS, ProjectPeer::DELETED, ProjectPeer::DESCRIPTION, ProjectPeer::END_DATE, ProjectPeer::FUNDORG, ProjectPeer::FUNDORGPROJID, ProjectPeer::NEES, ProjectPeer::NSFTITLE, ProjectPeer::NAME, ProjectPeer::NICKNAME, ProjectPeer::SHORT_TITLE, ProjectPeer::START_DATE, ProjectPeer::STATUS, ProjectPeer::SYSADMIN_EMAIL, ProjectPeer::SYSADMIN_NAME, ProjectPeer::TITLE, ProjectPeer::VIEWABLE, ProjectPeer::CREATOR_ID, ProjectPeer::SUPER_PROJECT_ID, ProjectPeer::PROJECT_TYPE_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('PROJID', 'CONTACT_EMAIL', 'CONTACT_NAME', 'CURATION_STATUS', 'DELETED', 'DESCRIPTION', 'END_DATE', 'FUNDORG', 'FUNDORGPROJID', 'NEES', 'NSFTITLE', 'NAME', 'NICKNAME', 'SHORT_TITLE', 'START_DATE', 'STATUS', 'SYSADMIN_EMAIL', 'SYSADMIN_NAME', 'TITLE', 'VIEWABLE', 'CREATOR_ID', 'SUPER_PROJECT_ID', 'PROJECT_TYPE_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'ContactEmail' => 1, 'ContactName' => 2, 'CurationStatus' => 3, 'Deleted' => 4, 'Description' => 5, 'EndDate' => 6, 'Fundorg' => 7, 'FundorgProjId' => 8, 'NEES' => 9, 'NSFTitle' => 10, 'Name' => 11, 'Nickname' => 12, 'ShortTitle' => 13, 'StartDate' => 14, 'Status' => 15, 'SysadminEmail' => 16, 'SysadminName' => 17, 'Title' => 18, 'View' => 19, 'CreatorId' => 20, 'SuperProjectId' => 21, 'ProjectTypeId' => 22, ),
		BasePeer::TYPE_COLNAME => array (ProjectPeer::PROJID => 0, ProjectPeer::CONTACT_EMAIL => 1, ProjectPeer::CONTACT_NAME => 2, ProjectPeer::CURATION_STATUS => 3, ProjectPeer::DELETED => 4, ProjectPeer::DESCRIPTION => 5, ProjectPeer::END_DATE => 6, ProjectPeer::FUNDORG => 7, ProjectPeer::FUNDORGPROJID => 8, ProjectPeer::NEES => 9, ProjectPeer::NSFTITLE => 10, ProjectPeer::NAME => 11, ProjectPeer::NICKNAME => 12, ProjectPeer::SHORT_TITLE => 13, ProjectPeer::START_DATE => 14, ProjectPeer::STATUS => 15, ProjectPeer::SYSADMIN_EMAIL => 16, ProjectPeer::SYSADMIN_NAME => 17, ProjectPeer::TITLE => 18, ProjectPeer::VIEWABLE => 19, ProjectPeer::CREATOR_ID => 20, ProjectPeer::SUPER_PROJECT_ID => 21, ProjectPeer::PROJECT_TYPE_ID => 22, ),
		BasePeer::TYPE_FIELDNAME => array ('PROJID' => 0, 'CONTACT_EMAIL' => 1, 'CONTACT_NAME' => 2, 'CURATION_STATUS' => 3, 'DELETED' => 4, 'DESCRIPTION' => 5, 'END_DATE' => 6, 'FUNDORG' => 7, 'FUNDORGPROJID' => 8, 'NEES' => 9, 'NSFTITLE' => 10, 'NAME' => 11, 'NICKNAME' => 12, 'SHORT_TITLE' => 13, 'START_DATE' => 14, 'STATUS' => 15, 'SYSADMIN_EMAIL' => 16, 'SYSADMIN_NAME' => 17, 'TITLE' => 18, 'VIEWABLE' => 19, 'CREATOR_ID' => 20, 'SUPER_PROJECT_ID' => 21, 'PROJECT_TYPE_ID' => 22, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/ProjectMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.ProjectMapBuilder');
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
			$map = ProjectPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. ProjectPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(ProjectPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(ProjectPeer::PROJID);

		$criteria->addSelectColumn(ProjectPeer::CONTACT_EMAIL);

		$criteria->addSelectColumn(ProjectPeer::CONTACT_NAME);

		$criteria->addSelectColumn(ProjectPeer::CURATION_STATUS);

		$criteria->addSelectColumn(ProjectPeer::DELETED);

		$criteria->addSelectColumn(ProjectPeer::DESCRIPTION);

		$criteria->addSelectColumn(ProjectPeer::END_DATE);

		$criteria->addSelectColumn(ProjectPeer::FUNDORG);

		$criteria->addSelectColumn(ProjectPeer::FUNDORGPROJID);

		$criteria->addSelectColumn(ProjectPeer::NEES);

		$criteria->addSelectColumn(ProjectPeer::NSFTITLE);

		$criteria->addSelectColumn(ProjectPeer::NAME);

		$criteria->addSelectColumn(ProjectPeer::NICKNAME);

		$criteria->addSelectColumn(ProjectPeer::SHORT_TITLE);

		$criteria->addSelectColumn(ProjectPeer::START_DATE);

		$criteria->addSelectColumn(ProjectPeer::STATUS);

		$criteria->addSelectColumn(ProjectPeer::SYSADMIN_EMAIL);

		$criteria->addSelectColumn(ProjectPeer::SYSADMIN_NAME);

		$criteria->addSelectColumn(ProjectPeer::TITLE);

		$criteria->addSelectColumn(ProjectPeer::VIEWABLE);

		$criteria->addSelectColumn(ProjectPeer::CREATOR_ID);

		$criteria->addSelectColumn(ProjectPeer::SUPER_PROJECT_ID);

		$criteria->addSelectColumn(ProjectPeer::PROJECT_TYPE_ID);

	}

	const COUNT = 'COUNT(PROJECT.PROJID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT PROJECT.PROJID)';

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
			$criteria->addSelectColumn(ProjectPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ProjectPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = ProjectPeer::doSelectRS($criteria, $con);
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
	 * @return     Project
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = ProjectPeer::doSelect($critcopy, $con);
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
		return ProjectPeer::populateObjects(ProjectPeer::doSelectRS($criteria, $con));
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
			ProjectPeer::addSelectColumns($criteria);
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
			$cls = Propel::import(ProjectPeer::getOMClass($rs, 1));
			$obj = new $cls();
			$obj->hydrate($rs);
			$results[] = $obj;
			
		}
		return $results;
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
			$criteria->addSelectColumn(ProjectPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ProjectPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ProjectPeer::CREATOR_ID, PersonPeer::ID);

		$rs = ProjectPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Project objects pre-filled with their Person objects.
	 *
	 * @return     array Array of Project objects.
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

		ProjectPeer::addSelectColumns($c);
		$startcol = (ProjectPeer::NUM_COLUMNS - ProjectPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		PersonPeer::addSelectColumns($c);

		$c->addJoin(ProjectPeer::CREATOR_ID, PersonPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ProjectPeer::getOMClass($rs, 1);

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
					$temp_obj2->addProject($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initProjects();
				$obj2->addProject($obj1); //CHECKME
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
			$criteria->addSelectColumn(ProjectPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ProjectPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ProjectPeer::CREATOR_ID, PersonPeer::ID);

		$rs = ProjectPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Project objects pre-filled with all related objects.
	 *
	 * @return     array Array of Project objects.
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

		ProjectPeer::addSelectColumns($c);
		$startcol2 = (ProjectPeer::NUM_COLUMNS - ProjectPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		PersonPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + PersonPeer::NUM_COLUMNS;

		$c->addJoin(ProjectPeer::CREATOR_ID, PersonPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ProjectPeer::getOMClass($rs, 1);


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined Person rows
	
			$omClass = PersonPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getPerson(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addProject($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initProjects();
				$obj2->addProject($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related ProjectRelatedBySuperProjectId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptProjectRelatedBySuperProjectId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(ProjectPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ProjectPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(ProjectPeer::CREATOR_ID, PersonPeer::ID);

		$rs = ProjectPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(ProjectPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ProjectPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = ProjectPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Project objects pre-filled with all related objects except ProjectRelatedBySuperProjectId.
	 *
	 * @return     array Array of Project objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptProjectRelatedBySuperProjectId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		ProjectPeer::addSelectColumns($c);
		$startcol2 = (ProjectPeer::NUM_COLUMNS - ProjectPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		PersonPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + PersonPeer::NUM_COLUMNS;

		$c->addJoin(ProjectPeer::CREATOR_ID, PersonPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ProjectPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = PersonPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getPerson(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addProject($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initProjects();
				$obj2->addProject($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Project objects pre-filled with all related objects except Person.
	 *
	 * @return     array Array of Project objects.
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

		ProjectPeer::addSelectColumns($c);
		$startcol2 = (ProjectPeer::NUM_COLUMNS - ProjectPeer::NUM_LAZY_LOAD_COLUMNS) + 1;


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = ProjectPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

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
			$classKey = $rs->getString($colnum - 1 + 23);

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

				case self::CLASSKEY_4:
					$omClass = self::CLASSNAME_4;
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
	 * Method perform an INSERT on the database, given a Project or Criteria object.
	 *
	 * @param      mixed $values Criteria or Project object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from Project object
		}

		$criteria->remove(ProjectPeer::PROJID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a Project or Criteria object.
	 *
	 * @param      mixed $values Criteria or Project object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(ProjectPeer::PROJID);
			$selectCriteria->add(ProjectPeer::PROJID, $criteria->remove(ProjectPeer::PROJID), $comparison);

		} else { // $values is Project object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the PROJECT table.
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
			$affectedRows += BasePeer::doDeleteAll(ProjectPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a Project or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Project object or primary key or array of primary keys
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
			$con = Propel::getConnection(ProjectPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof Project) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(ProjectPeer::PROJID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given Project object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Project $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(Project $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(ProjectPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(ProjectPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::CONTACT_EMAIL))
			$columns[ProjectPeer::CONTACT_EMAIL] = $obj->getContactEmail();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::CONTACT_NAME))
			$columns[ProjectPeer::CONTACT_NAME] = $obj->getContactName();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::CURATION_STATUS))
			$columns[ProjectPeer::CURATION_STATUS] = $obj->getCurationStatus();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::FUNDORG))
			$columns[ProjectPeer::FUNDORG] = $obj->getFundorg();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::FUNDORGPROJID))
			$columns[ProjectPeer::FUNDORGPROJID] = $obj->getFundorgProjId();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::NAME))
			$columns[ProjectPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::NICKNAME))
			$columns[ProjectPeer::NICKNAME] = $obj->getNickname();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::NSFTITLE))
			$columns[ProjectPeer::NSFTITLE] = $obj->getNSFTitle();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::SHORT_TITLE))
			$columns[ProjectPeer::SHORT_TITLE] = $obj->getShortTitle();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::STATUS))
			$columns[ProjectPeer::STATUS] = $obj->getStatus();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::SYSADMIN_EMAIL))
			$columns[ProjectPeer::SYSADMIN_EMAIL] = $obj->getSysadminEmail();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::SYSADMIN_NAME))
			$columns[ProjectPeer::SYSADMIN_NAME] = $obj->getSysadminName();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::TITLE))
			$columns[ProjectPeer::TITLE] = $obj->getTitle();

		if ($obj->isNew() || $obj->isColumnModified(ProjectPeer::VIEWABLE))
			$columns[ProjectPeer::VIEWABLE] = $obj->getView();

		}

		return BasePeer::doValidate(ProjectPeer::DATABASE_NAME, ProjectPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     Project
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(ProjectPeer::DATABASE_NAME);

		$criteria->add(ProjectPeer::PROJID, $pk);


		$v = ProjectPeer::doSelect($criteria, $con);

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
			$criteria->add(ProjectPeer::PROJID, $pks, Criteria::IN);
			$objs = ProjectPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseProjectPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseProjectPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/ProjectMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.ProjectMapBuilder');
}
