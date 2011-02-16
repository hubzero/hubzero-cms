<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by RepetitionPeer::getOMClass()
include_once 'lib/data/Repetition.php';

/**
 * Base static class for performing query and update operations on the 'REPETITION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseRepetitionPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'REPETITION';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.Repetition';

	/** The total number of columns. */
	const NUM_COLUMNS = 15;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the REPID field */
	const REPID = 'REPETITION.REPID';

	/** the column name for the CURATION_STATUS field */
	const CURATION_STATUS = 'REPETITION.CURATION_STATUS';

	/** the column name for the DELETED field */
	const DELETED = 'REPETITION.DELETED';

	/** the column name for the END_DATE field */
	const END_DATE = 'REPETITION.END_DATE';

	/** the column name for the NAME field */
	const NAME = 'REPETITION.NAME';

	/** the column name for the START_DATE field */
	const START_DATE = 'REPETITION.START_DATE';

	/** the column name for the STATUS field */
	const STATUS = 'REPETITION.STATUS';

	/** the column name for the TRIALID field */
	const TRIALID = 'REPETITION.TRIALID';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'REPETITION.DESCRIPTION';

	/** the column name for the TITLE field */
	const TITLE = 'REPETITION.TITLE';

	/** the column name for the CREATOR_ID field */
	const CREATOR_ID = 'REPETITION.CREATOR_ID';

	/** the column name for the CREATED_DATE field */
	const CREATED_DATE = 'REPETITION.CREATED_DATE';

	/** the column name for the MODIFIED_BY_ID field */
	const MODIFIED_BY_ID = 'REPETITION.MODIFIED_BY_ID';

	/** the column name for the MODIFIED_DATE field */
	const MODIFIED_DATE = 'REPETITION.MODIFIED_DATE';

	/** the column name for the APP_ID field */
	const APP_ID = 'REPETITION.APP_ID';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CurationStatus', 'Deleted', 'EndDate', 'Name', 'StartDate', 'Status', 'TrialId', 'Description', 'Title', 'CreatorId', 'CreatedDate', 'ModifiedById', 'ModifiedDate', 'AppId', ),
		BasePeer::TYPE_COLNAME => array (RepetitionPeer::REPID, RepetitionPeer::CURATION_STATUS, RepetitionPeer::DELETED, RepetitionPeer::END_DATE, RepetitionPeer::NAME, RepetitionPeer::START_DATE, RepetitionPeer::STATUS, RepetitionPeer::TRIALID, RepetitionPeer::DESCRIPTION, RepetitionPeer::TITLE, RepetitionPeer::CREATOR_ID, RepetitionPeer::CREATED_DATE, RepetitionPeer::MODIFIED_BY_ID, RepetitionPeer::MODIFIED_DATE, RepetitionPeer::APP_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('REPID', 'CURATION_STATUS', 'DELETED', 'END_DATE', 'NAME', 'START_DATE', 'STATUS', 'TRIALID', 'DESCRIPTION', 'TITLE', 'CREATOR_ID', 'CREATED_DATE', 'MODIFIED_BY_ID', 'MODIFIED_DATE', 'APP_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CurationStatus' => 1, 'Deleted' => 2, 'EndDate' => 3, 'Name' => 4, 'StartDate' => 5, 'Status' => 6, 'TrialId' => 7, 'Description' => 8, 'Title' => 9, 'CreatorId' => 10, 'CreatedDate' => 11, 'ModifiedById' => 12, 'ModifiedDate' => 13, 'AppId' => 14, ),
		BasePeer::TYPE_COLNAME => array (RepetitionPeer::REPID => 0, RepetitionPeer::CURATION_STATUS => 1, RepetitionPeer::DELETED => 2, RepetitionPeer::END_DATE => 3, RepetitionPeer::NAME => 4, RepetitionPeer::START_DATE => 5, RepetitionPeer::STATUS => 6, RepetitionPeer::TRIALID => 7, RepetitionPeer::DESCRIPTION => 8, RepetitionPeer::TITLE => 9, RepetitionPeer::CREATOR_ID => 10, RepetitionPeer::CREATED_DATE => 11, RepetitionPeer::MODIFIED_BY_ID => 12, RepetitionPeer::MODIFIED_DATE => 13, RepetitionPeer::APP_ID => 14, ),
		BasePeer::TYPE_FIELDNAME => array ('REPID' => 0, 'CURATION_STATUS' => 1, 'DELETED' => 2, 'END_DATE' => 3, 'NAME' => 4, 'START_DATE' => 5, 'STATUS' => 6, 'TRIALID' => 7, 'DESCRIPTION' => 8, 'TITLE' => 9, 'CREATOR_ID' => 10, 'CREATED_DATE' => 11, 'MODIFIED_BY_ID' => 12, 'MODIFIED_DATE' => 13, 'APP_ID' => 14, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/RepetitionMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.RepetitionMapBuilder');
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
			$map = RepetitionPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. RepetitionPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(RepetitionPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(RepetitionPeer::REPID);

		$criteria->addSelectColumn(RepetitionPeer::CURATION_STATUS);

		$criteria->addSelectColumn(RepetitionPeer::DELETED);

		$criteria->addSelectColumn(RepetitionPeer::END_DATE);

		$criteria->addSelectColumn(RepetitionPeer::NAME);

		$criteria->addSelectColumn(RepetitionPeer::START_DATE);

		$criteria->addSelectColumn(RepetitionPeer::STATUS);

		$criteria->addSelectColumn(RepetitionPeer::TRIALID);

		$criteria->addSelectColumn(RepetitionPeer::DESCRIPTION);

		$criteria->addSelectColumn(RepetitionPeer::TITLE);

		$criteria->addSelectColumn(RepetitionPeer::CREATOR_ID);

		$criteria->addSelectColumn(RepetitionPeer::CREATED_DATE);

		$criteria->addSelectColumn(RepetitionPeer::MODIFIED_BY_ID);

		$criteria->addSelectColumn(RepetitionPeer::MODIFIED_DATE);

		$criteria->addSelectColumn(RepetitionPeer::APP_ID);

	}

	const COUNT = 'COUNT(REPETITION.REPID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT REPETITION.REPID)';

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
			$criteria->addSelectColumn(RepetitionPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(RepetitionPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = RepetitionPeer::doSelectRS($criteria, $con);
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
	 * @return     Repetition
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = RepetitionPeer::doSelect($critcopy, $con);
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
		return RepetitionPeer::populateObjects(RepetitionPeer::doSelectRS($criteria, $con));
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
			RepetitionPeer::addSelectColumns($criteria);
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
		$cls = RepetitionPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related PersonRelatedByCreatorId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinPersonRelatedByCreatorId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(RepetitionPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(RepetitionPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(RepetitionPeer::CREATOR_ID, PersonPeer::ID);

		$rs = RepetitionPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related PersonRelatedByModifiedById table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinPersonRelatedByModifiedById(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(RepetitionPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(RepetitionPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(RepetitionPeer::MODIFIED_BY_ID, PersonPeer::ID);

		$rs = RepetitionPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(RepetitionPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(RepetitionPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(RepetitionPeer::TRIALID, TrialPeer::TRIALID);

		$rs = RepetitionPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Repetition objects pre-filled with their Person objects.
	 *
	 * @return     array Array of Repetition objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinPersonRelatedByCreatorId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		RepetitionPeer::addSelectColumns($c);
		$startcol = (RepetitionPeer::NUM_COLUMNS - RepetitionPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		PersonPeer::addSelectColumns($c);

		$c->addJoin(RepetitionPeer::CREATOR_ID, PersonPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = RepetitionPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = PersonPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getPersonRelatedByCreatorId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addRepetitionRelatedByCreatorId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initRepetitionsRelatedByCreatorId();
				$obj2->addRepetitionRelatedByCreatorId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Repetition objects pre-filled with their Person objects.
	 *
	 * @return     array Array of Repetition objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinPersonRelatedByModifiedById(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		RepetitionPeer::addSelectColumns($c);
		$startcol = (RepetitionPeer::NUM_COLUMNS - RepetitionPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		PersonPeer::addSelectColumns($c);

		$c->addJoin(RepetitionPeer::MODIFIED_BY_ID, PersonPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = RepetitionPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = PersonPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getPersonRelatedByModifiedById(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addRepetitionRelatedByModifiedById($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initRepetitionsRelatedByModifiedById();
				$obj2->addRepetitionRelatedByModifiedById($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Repetition objects pre-filled with their Trial objects.
	 *
	 * @return     array Array of Repetition objects.
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

		RepetitionPeer::addSelectColumns($c);
		$startcol = (RepetitionPeer::NUM_COLUMNS - RepetitionPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		TrialPeer::addSelectColumns($c);

		$c->addJoin(RepetitionPeer::TRIALID, TrialPeer::TRIALID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = RepetitionPeer::getOMClass();

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
					$temp_obj2->addRepetition($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initRepetitions();
				$obj2->addRepetition($obj1); //CHECKME
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
			$criteria->addSelectColumn(RepetitionPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(RepetitionPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(RepetitionPeer::CREATOR_ID, PersonPeer::ID);

		$criteria->addJoin(RepetitionPeer::MODIFIED_BY_ID, PersonPeer::ID);

		$criteria->addJoin(RepetitionPeer::TRIALID, TrialPeer::TRIALID);

		$rs = RepetitionPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Repetition objects pre-filled with all related objects.
	 *
	 * @return     array Array of Repetition objects.
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

		RepetitionPeer::addSelectColumns($c);
		$startcol2 = (RepetitionPeer::NUM_COLUMNS - RepetitionPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		PersonPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + PersonPeer::NUM_COLUMNS;

		PersonPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + PersonPeer::NUM_COLUMNS;

		TrialPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + TrialPeer::NUM_COLUMNS;

		$c->addJoin(RepetitionPeer::CREATOR_ID, PersonPeer::ID);

		$c->addJoin(RepetitionPeer::MODIFIED_BY_ID, PersonPeer::ID);

		$c->addJoin(RepetitionPeer::TRIALID, TrialPeer::TRIALID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = RepetitionPeer::getOMClass();


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
				$temp_obj2 = $temp_obj1->getPersonRelatedByCreatorId(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addRepetitionRelatedByCreatorId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initRepetitionsRelatedByCreatorId();
				$obj2->addRepetitionRelatedByCreatorId($obj1);
			}


				// Add objects for joined Person rows
	
			$omClass = PersonPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getPersonRelatedByModifiedById(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addRepetitionRelatedByModifiedById($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initRepetitionsRelatedByModifiedById();
				$obj3->addRepetitionRelatedByModifiedById($obj1);
			}


				// Add objects for joined Trial rows
	
			$omClass = TrialPeer::getOMClass($rs, $startcol4);


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getTrial(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addRepetition($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initRepetitions();
				$obj4->addRepetition($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related PersonRelatedByCreatorId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptPersonRelatedByCreatorId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(RepetitionPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(RepetitionPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(RepetitionPeer::TRIALID, TrialPeer::TRIALID);

		$rs = RepetitionPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related PersonRelatedByModifiedById table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptPersonRelatedByModifiedById(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(RepetitionPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(RepetitionPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(RepetitionPeer::TRIALID, TrialPeer::TRIALID);

		$rs = RepetitionPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(RepetitionPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(RepetitionPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(RepetitionPeer::CREATOR_ID, PersonPeer::ID);

		$criteria->addJoin(RepetitionPeer::MODIFIED_BY_ID, PersonPeer::ID);

		$rs = RepetitionPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Repetition objects pre-filled with all related objects except PersonRelatedByCreatorId.
	 *
	 * @return     array Array of Repetition objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptPersonRelatedByCreatorId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		RepetitionPeer::addSelectColumns($c);
		$startcol2 = (RepetitionPeer::NUM_COLUMNS - RepetitionPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		TrialPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + TrialPeer::NUM_COLUMNS;

		$c->addJoin(RepetitionPeer::TRIALID, TrialPeer::TRIALID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = RepetitionPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = TrialPeer::getOMClass($rs, $startcol2);


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getTrial(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addRepetition($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initRepetitions();
				$obj2->addRepetition($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Repetition objects pre-filled with all related objects except PersonRelatedByModifiedById.
	 *
	 * @return     array Array of Repetition objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptPersonRelatedByModifiedById(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		RepetitionPeer::addSelectColumns($c);
		$startcol2 = (RepetitionPeer::NUM_COLUMNS - RepetitionPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		TrialPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + TrialPeer::NUM_COLUMNS;

		$c->addJoin(RepetitionPeer::TRIALID, TrialPeer::TRIALID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = RepetitionPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = TrialPeer::getOMClass($rs, $startcol2);


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getTrial(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addRepetition($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initRepetitions();
				$obj2->addRepetition($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Repetition objects pre-filled with all related objects except Trial.
	 *
	 * @return     array Array of Repetition objects.
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

		RepetitionPeer::addSelectColumns($c);
		$startcol2 = (RepetitionPeer::NUM_COLUMNS - RepetitionPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		PersonPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + PersonPeer::NUM_COLUMNS;

		PersonPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + PersonPeer::NUM_COLUMNS;

		$c->addJoin(RepetitionPeer::CREATOR_ID, PersonPeer::ID);

		$c->addJoin(RepetitionPeer::MODIFIED_BY_ID, PersonPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = RepetitionPeer::getOMClass();

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
				$temp_obj2 = $temp_obj1->getPersonRelatedByCreatorId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addRepetitionRelatedByCreatorId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initRepetitionsRelatedByCreatorId();
				$obj2->addRepetitionRelatedByCreatorId($obj1);
			}

			$omClass = PersonPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getPersonRelatedByModifiedById(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addRepetitionRelatedByModifiedById($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initRepetitionsRelatedByModifiedById();
				$obj3->addRepetitionRelatedByModifiedById($obj1);
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
		return RepetitionPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a Repetition or Criteria object.
	 *
	 * @param      mixed $values Criteria or Repetition object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from Repetition object
		}

		$criteria->remove(RepetitionPeer::REPID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a Repetition or Criteria object.
	 *
	 * @param      mixed $values Criteria or Repetition object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(RepetitionPeer::REPID);
			$selectCriteria->add(RepetitionPeer::REPID, $criteria->remove(RepetitionPeer::REPID), $comparison);

		} else { // $values is Repetition object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the REPETITION table.
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
			$affectedRows += BasePeer::doDeleteAll(RepetitionPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a Repetition or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Repetition object or primary key or array of primary keys
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
			$con = Propel::getConnection(RepetitionPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof Repetition) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(RepetitionPeer::REPID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given Repetition object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Repetition $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(Repetition $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(RepetitionPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(RepetitionPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(RepetitionPeer::CURATION_STATUS))
			$columns[RepetitionPeer::CURATION_STATUS] = $obj->getCurationStatus();

		if ($obj->isNew() || $obj->isColumnModified(RepetitionPeer::DELETED))
			$columns[RepetitionPeer::DELETED] = $obj->getDeleted();

		if ($obj->isNew() || $obj->isColumnModified(RepetitionPeer::END_DATE))
			$columns[RepetitionPeer::END_DATE] = $obj->getEndDate();

		if ($obj->isNew() || $obj->isColumnModified(RepetitionPeer::NAME))
			$columns[RepetitionPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(RepetitionPeer::START_DATE))
			$columns[RepetitionPeer::START_DATE] = $obj->getStartDate();

		if ($obj->isNew() || $obj->isColumnModified(RepetitionPeer::STATUS))
			$columns[RepetitionPeer::STATUS] = $obj->getStatus();

		if ($obj->isNew() || $obj->isColumnModified(RepetitionPeer::TRIALID))
			$columns[RepetitionPeer::TRIALID] = $obj->getTrialId();

		}

		return BasePeer::doValidate(RepetitionPeer::DATABASE_NAME, RepetitionPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     Repetition
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(RepetitionPeer::DATABASE_NAME);

		$criteria->add(RepetitionPeer::REPID, $pk);


		$v = RepetitionPeer::doSelect($criteria, $con);

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
			$criteria->add(RepetitionPeer::REPID, $pks, Criteria::IN);
			$objs = RepetitionPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseRepetitionPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseRepetitionPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/RepetitionMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.RepetitionMapBuilder');
}
