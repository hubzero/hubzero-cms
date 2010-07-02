<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by TsunamiDocLibPeer::getOMClass()
include_once 'lib/data/tsunami/TsunamiDocLib.php';

/**
 * Base static class for performing query and update operations on the 'TSUNAMI_DOC_LIB' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiDocLibPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'TSUNAMI_DOC_LIB';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.tsunami.TsunamiDocLib';

	/** The total number of columns. */
	const NUM_COLUMNS = 15;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the TSUNAMI_DOC_LIB_ID field */
	const TSUNAMI_DOC_LIB_ID = 'TSUNAMI_DOC_LIB.TSUNAMI_DOC_LIB_ID';

	/** the column name for the AUTHOR_EMAILS field */
	const AUTHOR_EMAILS = 'TSUNAMI_DOC_LIB.AUTHOR_EMAILS';

	/** the column name for the AUTHORS field */
	const AUTHORS = 'TSUNAMI_DOC_LIB.AUTHORS';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'TSUNAMI_DOC_LIB.DESCRIPTION';

	/** the column name for the DIRTY field */
	const DIRTY = 'TSUNAMI_DOC_LIB.DIRTY';

	/** the column name for the FILE_LOCATION field */
	const FILE_LOCATION = 'TSUNAMI_DOC_LIB.FILE_LOCATION';

	/** the column name for the FILE_SIZE field */
	const FILE_SIZE = 'TSUNAMI_DOC_LIB.FILE_SIZE';

	/** the column name for the HOW_TO_CITE field */
	const HOW_TO_CITE = 'TSUNAMI_DOC_LIB.HOW_TO_CITE';

	/** the column name for the NAME field */
	const NAME = 'TSUNAMI_DOC_LIB.NAME';

	/** the column name for the SPECIFIC_LAT field */
	const SPECIFIC_LAT = 'TSUNAMI_DOC_LIB.SPECIFIC_LAT';

	/** the column name for the SPECIFIC_LON field */
	const SPECIFIC_LON = 'TSUNAMI_DOC_LIB.SPECIFIC_LON';

	/** the column name for the START_DATE field */
	const START_DATE = 'TSUNAMI_DOC_LIB.START_DATE';

	/** the column name for the TITLE field */
	const TITLE = 'TSUNAMI_DOC_LIB.TITLE';

	/** the column name for the TSUNAMI_PROJECT_ID field */
	const TSUNAMI_PROJECT_ID = 'TSUNAMI_DOC_LIB.TSUNAMI_PROJECT_ID';

	/** the column name for the TYPE_OF_MATERIAL field */
	const TYPE_OF_MATERIAL = 'TSUNAMI_DOC_LIB.TYPE_OF_MATERIAL';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'AuthorEmails', 'Authors', 'Description', 'Dirty', 'FileLocation', 'FileSize', 'HowToCite', 'Name', 'SpecificLatitude', 'SpecificLongitude', 'StartDate', 'Title', 'TsunamiProjectId', 'TypeOfMaterial', ),
		BasePeer::TYPE_COLNAME => array (TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::AUTHOR_EMAILS, TsunamiDocLibPeer::AUTHORS, TsunamiDocLibPeer::DESCRIPTION, TsunamiDocLibPeer::DIRTY, TsunamiDocLibPeer::FILE_LOCATION, TsunamiDocLibPeer::FILE_SIZE, TsunamiDocLibPeer::HOW_TO_CITE, TsunamiDocLibPeer::NAME, TsunamiDocLibPeer::SPECIFIC_LAT, TsunamiDocLibPeer::SPECIFIC_LON, TsunamiDocLibPeer::START_DATE, TsunamiDocLibPeer::TITLE, TsunamiDocLibPeer::TSUNAMI_PROJECT_ID, TsunamiDocLibPeer::TYPE_OF_MATERIAL, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_DOC_LIB_ID', 'AUTHOR_EMAILS', 'AUTHORS', 'DESCRIPTION', 'DIRTY', 'FILE_LOCATION', 'FILE_SIZE', 'HOW_TO_CITE', 'NAME', 'SPECIFIC_LAT', 'SPECIFIC_LON', 'START_DATE', 'TITLE', 'TSUNAMI_PROJECT_ID', 'TYPE_OF_MATERIAL', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'AuthorEmails' => 1, 'Authors' => 2, 'Description' => 3, 'Dirty' => 4, 'FileLocation' => 5, 'FileSize' => 6, 'HowToCite' => 7, 'Name' => 8, 'SpecificLatitude' => 9, 'SpecificLongitude' => 10, 'StartDate' => 11, 'Title' => 12, 'TsunamiProjectId' => 13, 'TypeOfMaterial' => 14, ),
		BasePeer::TYPE_COLNAME => array (TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID => 0, TsunamiDocLibPeer::AUTHOR_EMAILS => 1, TsunamiDocLibPeer::AUTHORS => 2, TsunamiDocLibPeer::DESCRIPTION => 3, TsunamiDocLibPeer::DIRTY => 4, TsunamiDocLibPeer::FILE_LOCATION => 5, TsunamiDocLibPeer::FILE_SIZE => 6, TsunamiDocLibPeer::HOW_TO_CITE => 7, TsunamiDocLibPeer::NAME => 8, TsunamiDocLibPeer::SPECIFIC_LAT => 9, TsunamiDocLibPeer::SPECIFIC_LON => 10, TsunamiDocLibPeer::START_DATE => 11, TsunamiDocLibPeer::TITLE => 12, TsunamiDocLibPeer::TSUNAMI_PROJECT_ID => 13, TsunamiDocLibPeer::TYPE_OF_MATERIAL => 14, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_DOC_LIB_ID' => 0, 'AUTHOR_EMAILS' => 1, 'AUTHORS' => 2, 'DESCRIPTION' => 3, 'DIRTY' => 4, 'FILE_LOCATION' => 5, 'FILE_SIZE' => 6, 'HOW_TO_CITE' => 7, 'NAME' => 8, 'SPECIFIC_LAT' => 9, 'SPECIFIC_LON' => 10, 'START_DATE' => 11, 'TITLE' => 12, 'TSUNAMI_PROJECT_ID' => 13, 'TYPE_OF_MATERIAL' => 14, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/tsunami/map/TsunamiDocLibMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.tsunami.map.TsunamiDocLibMapBuilder');
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
			$map = TsunamiDocLibPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. TsunamiDocLibPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(TsunamiDocLibPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$criteria->addSelectColumn(TsunamiDocLibPeer::AUTHOR_EMAILS);

		$criteria->addSelectColumn(TsunamiDocLibPeer::AUTHORS);

		$criteria->addSelectColumn(TsunamiDocLibPeer::DESCRIPTION);

		$criteria->addSelectColumn(TsunamiDocLibPeer::DIRTY);

		$criteria->addSelectColumn(TsunamiDocLibPeer::FILE_LOCATION);

		$criteria->addSelectColumn(TsunamiDocLibPeer::FILE_SIZE);

		$criteria->addSelectColumn(TsunamiDocLibPeer::HOW_TO_CITE);

		$criteria->addSelectColumn(TsunamiDocLibPeer::NAME);

		$criteria->addSelectColumn(TsunamiDocLibPeer::SPECIFIC_LAT);

		$criteria->addSelectColumn(TsunamiDocLibPeer::SPECIFIC_LON);

		$criteria->addSelectColumn(TsunamiDocLibPeer::START_DATE);

		$criteria->addSelectColumn(TsunamiDocLibPeer::TITLE);

		$criteria->addSelectColumn(TsunamiDocLibPeer::TSUNAMI_PROJECT_ID);

		$criteria->addSelectColumn(TsunamiDocLibPeer::TYPE_OF_MATERIAL);

	}

	const COUNT = 'COUNT(TSUNAMI_DOC_LIB.TSUNAMI_DOC_LIB_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT TSUNAMI_DOC_LIB.TSUNAMI_DOC_LIB_ID)';

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
			$criteria->addSelectColumn(TsunamiDocLibPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiDocLibPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = TsunamiDocLibPeer::doSelectRS($criteria, $con);
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
	 * @return     TsunamiDocLib
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = TsunamiDocLibPeer::doSelect($critcopy, $con);
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
		return TsunamiDocLibPeer::populateObjects(TsunamiDocLibPeer::doSelectRS($criteria, $con));
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
			TsunamiDocLibPeer::addSelectColumns($criteria);
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
		$cls = TsunamiDocLibPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related TsunamiProject table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinTsunamiProject(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(TsunamiDocLibPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiDocLibPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiDocLibPeer::TSUNAMI_PROJECT_ID, TsunamiProjectPeer::TSUNAMI_PROJECT_ID);

		$rs = TsunamiDocLibPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiDocLib objects pre-filled with their TsunamiProject objects.
	 *
	 * @return     array Array of TsunamiDocLib objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinTsunamiProject(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		TsunamiDocLibPeer::addSelectColumns($c);
		$startcol = (TsunamiDocLibPeer::NUM_COLUMNS - TsunamiDocLibPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		TsunamiProjectPeer::addSelectColumns($c);

		$c->addJoin(TsunamiDocLibPeer::TSUNAMI_PROJECT_ID, TsunamiProjectPeer::TSUNAMI_PROJECT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiDocLibPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = TsunamiProjectPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getTsunamiProject(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addTsunamiDocLib($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initTsunamiDocLibs();
				$obj2->addTsunamiDocLib($obj1); //CHECKME
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
			$criteria->addSelectColumn(TsunamiDocLibPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiDocLibPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiDocLibPeer::TSUNAMI_PROJECT_ID, TsunamiProjectPeer::TSUNAMI_PROJECT_ID);

		$rs = TsunamiDocLibPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiDocLib objects pre-filled with all related objects.
	 *
	 * @return     array Array of TsunamiDocLib objects.
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

		TsunamiDocLibPeer::addSelectColumns($c);
		$startcol2 = (TsunamiDocLibPeer::NUM_COLUMNS - TsunamiDocLibPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		TsunamiProjectPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + TsunamiProjectPeer::NUM_COLUMNS;

		$c->addJoin(TsunamiDocLibPeer::TSUNAMI_PROJECT_ID, TsunamiProjectPeer::TSUNAMI_PROJECT_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiDocLibPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined TsunamiProject rows
	
			$omClass = TsunamiProjectPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getTsunamiProject(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addTsunamiDocLib($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initTsunamiDocLibs();
				$obj2->addTsunamiDocLib($obj1);
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
		return TsunamiDocLibPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a TsunamiDocLib or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiDocLib object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from TsunamiDocLib object
		}

		$criteria->remove(TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a TsunamiDocLib or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiDocLib object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);
			$selectCriteria->add(TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID, $criteria->remove(TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID), $comparison);

		} else { // $values is TsunamiDocLib object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the TSUNAMI_DOC_LIB table.
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
			$affectedRows += BasePeer::doDeleteAll(TsunamiDocLibPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a TsunamiDocLib or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or TsunamiDocLib object or primary key or array of primary keys
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
			$con = Propel::getConnection(TsunamiDocLibPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof TsunamiDocLib) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given TsunamiDocLib object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      TsunamiDocLib $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(TsunamiDocLib $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(TsunamiDocLibPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(TsunamiDocLibPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::AUTHORS))
			$columns[TsunamiDocLibPeer::AUTHORS] = $obj->getAuthors();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::AUTHOR_EMAILS))
			$columns[TsunamiDocLibPeer::AUTHOR_EMAILS] = $obj->getAuthorEmails();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::DESCRIPTION))
			$columns[TsunamiDocLibPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::DIRTY))
			$columns[TsunamiDocLibPeer::DIRTY] = $obj->getDirty();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::FILE_LOCATION))
			$columns[TsunamiDocLibPeer::FILE_LOCATION] = $obj->getFileLocation();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::FILE_SIZE))
			$columns[TsunamiDocLibPeer::FILE_SIZE] = $obj->getFileSize();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::HOW_TO_CITE))
			$columns[TsunamiDocLibPeer::HOW_TO_CITE] = $obj->getHowToCite();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::NAME))
			$columns[TsunamiDocLibPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::SPECIFIC_LAT))
			$columns[TsunamiDocLibPeer::SPECIFIC_LAT] = $obj->getSpecificLatitude();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::SPECIFIC_LON))
			$columns[TsunamiDocLibPeer::SPECIFIC_LON] = $obj->getSpecificLongitude();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::START_DATE))
			$columns[TsunamiDocLibPeer::START_DATE] = $obj->getStartDate();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::TITLE))
			$columns[TsunamiDocLibPeer::TITLE] = $obj->getTitle();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::TSUNAMI_PROJECT_ID))
			$columns[TsunamiDocLibPeer::TSUNAMI_PROJECT_ID] = $obj->getTsunamiProjectId();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiDocLibPeer::TYPE_OF_MATERIAL))
			$columns[TsunamiDocLibPeer::TYPE_OF_MATERIAL] = $obj->getTypeOfMaterial();

		}

		return BasePeer::doValidate(TsunamiDocLibPeer::DATABASE_NAME, TsunamiDocLibPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     TsunamiDocLib
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(TsunamiDocLibPeer::DATABASE_NAME);

		$criteria->add(TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID, $pk);


		$v = TsunamiDocLibPeer::doSelect($criteria, $con);

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
			$criteria->add(TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID, $pks, Criteria::IN);
			$objs = TsunamiDocLibPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseTsunamiDocLibPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseTsunamiDocLibPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/tsunami/map/TsunamiDocLibMapBuilder.php';
	Propel::registerMapBuilder('lib.data.tsunami.map.TsunamiDocLibMapBuilder');
}
