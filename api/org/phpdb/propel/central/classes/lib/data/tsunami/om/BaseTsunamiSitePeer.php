<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by TsunamiSitePeer::getOMClass()
include_once 'lib/data/tsunami/TsunamiSite.php';

/**
 * Base static class for performing query and update operations on the 'TSUNAMI_SITE' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiSitePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'TSUNAMI_SITE';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.tsunami.TsunamiSite';

	/** The total number of columns. */
	const NUM_COLUMNS = 9;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the TSUNAMI_SITE_ID field */
	const TSUNAMI_SITE_ID = 'TSUNAMI_SITE.TSUNAMI_SITE_ID';

	/** the column name for the BOUNDING_POLYGON field */
	const BOUNDING_POLYGON = 'TSUNAMI_SITE.BOUNDING_POLYGON';

	/** the column name for the COUNTRY field */
	const COUNTRY = 'TSUNAMI_SITE.COUNTRY';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'TSUNAMI_SITE.DESCRIPTION';

	/** the column name for the NAME field */
	const NAME = 'TSUNAMI_SITE.NAME';

	/** the column name for the SITE_LAT field */
	const SITE_LAT = 'TSUNAMI_SITE.SITE_LAT';

	/** the column name for the SITE_LON field */
	const SITE_LON = 'TSUNAMI_SITE.SITE_LON';

	/** the column name for the TSUNAMI_PROJECT_ID field */
	const TSUNAMI_PROJECT_ID = 'TSUNAMI_SITE.TSUNAMI_PROJECT_ID';

	/** the column name for the TYPE field */
	const TYPE = 'TSUNAMI_SITE.TYPE';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'BoundingPolygon', 'Country', 'Description', 'Name', 'SiteLatitude', 'SiteLongitude', 'TsunamiProjectId', 'Type', ),
		BasePeer::TYPE_COLNAME => array (TsunamiSitePeer::TSUNAMI_SITE_ID, TsunamiSitePeer::BOUNDING_POLYGON, TsunamiSitePeer::COUNTRY, TsunamiSitePeer::DESCRIPTION, TsunamiSitePeer::NAME, TsunamiSitePeer::SITE_LAT, TsunamiSitePeer::SITE_LON, TsunamiSitePeer::TSUNAMI_PROJECT_ID, TsunamiSitePeer::TYPE, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_SITE_ID', 'BOUNDING_POLYGON', 'COUNTRY', 'DESCRIPTION', 'NAME', 'SITE_LAT', 'SITE_LON', 'TSUNAMI_PROJECT_ID', 'TYPE', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'BoundingPolygon' => 1, 'Country' => 2, 'Description' => 3, 'Name' => 4, 'SiteLatitude' => 5, 'SiteLongitude' => 6, 'TsunamiProjectId' => 7, 'Type' => 8, ),
		BasePeer::TYPE_COLNAME => array (TsunamiSitePeer::TSUNAMI_SITE_ID => 0, TsunamiSitePeer::BOUNDING_POLYGON => 1, TsunamiSitePeer::COUNTRY => 2, TsunamiSitePeer::DESCRIPTION => 3, TsunamiSitePeer::NAME => 4, TsunamiSitePeer::SITE_LAT => 5, TsunamiSitePeer::SITE_LON => 6, TsunamiSitePeer::TSUNAMI_PROJECT_ID => 7, TsunamiSitePeer::TYPE => 8, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_SITE_ID' => 0, 'BOUNDING_POLYGON' => 1, 'COUNTRY' => 2, 'DESCRIPTION' => 3, 'NAME' => 4, 'SITE_LAT' => 5, 'SITE_LON' => 6, 'TSUNAMI_PROJECT_ID' => 7, 'TYPE' => 8, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/tsunami/map/TsunamiSiteMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.tsunami.map.TsunamiSiteMapBuilder');
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
			$map = TsunamiSitePeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. TsunamiSitePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(TsunamiSitePeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(TsunamiSitePeer::TSUNAMI_SITE_ID);

		$criteria->addSelectColumn(TsunamiSitePeer::BOUNDING_POLYGON);

		$criteria->addSelectColumn(TsunamiSitePeer::COUNTRY);

		$criteria->addSelectColumn(TsunamiSitePeer::DESCRIPTION);

		$criteria->addSelectColumn(TsunamiSitePeer::NAME);

		$criteria->addSelectColumn(TsunamiSitePeer::SITE_LAT);

		$criteria->addSelectColumn(TsunamiSitePeer::SITE_LON);

		$criteria->addSelectColumn(TsunamiSitePeer::TSUNAMI_PROJECT_ID);

		$criteria->addSelectColumn(TsunamiSitePeer::TYPE);

	}

	const COUNT = 'COUNT(TSUNAMI_SITE.TSUNAMI_SITE_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT TSUNAMI_SITE.TSUNAMI_SITE_ID)';

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
			$criteria->addSelectColumn(TsunamiSitePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiSitePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = TsunamiSitePeer::doSelectRS($criteria, $con);
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
	 * @return     TsunamiSite
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = TsunamiSitePeer::doSelect($critcopy, $con);
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
		return TsunamiSitePeer::populateObjects(TsunamiSitePeer::doSelectRS($criteria, $con));
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
			TsunamiSitePeer::addSelectColumns($criteria);
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
		$cls = TsunamiSitePeer::getOMClass();
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
			$criteria->addSelectColumn(TsunamiSitePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiSitePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiSitePeer::TSUNAMI_PROJECT_ID, TsunamiProjectPeer::TSUNAMI_PROJECT_ID);

		$rs = TsunamiSitePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiSite objects pre-filled with their TsunamiProject objects.
	 *
	 * @return     array Array of TsunamiSite objects.
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

		TsunamiSitePeer::addSelectColumns($c);
		$startcol = (TsunamiSitePeer::NUM_COLUMNS - TsunamiSitePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		TsunamiProjectPeer::addSelectColumns($c);

		$c->addJoin(TsunamiSitePeer::TSUNAMI_PROJECT_ID, TsunamiProjectPeer::TSUNAMI_PROJECT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiSitePeer::getOMClass();

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
					$temp_obj2->addTsunamiSite($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initTsunamiSites();
				$obj2->addTsunamiSite($obj1); //CHECKME
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
			$criteria->addSelectColumn(TsunamiSitePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiSitePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiSitePeer::TSUNAMI_PROJECT_ID, TsunamiProjectPeer::TSUNAMI_PROJECT_ID);

		$rs = TsunamiSitePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiSite objects pre-filled with all related objects.
	 *
	 * @return     array Array of TsunamiSite objects.
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

		TsunamiSitePeer::addSelectColumns($c);
		$startcol2 = (TsunamiSitePeer::NUM_COLUMNS - TsunamiSitePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		TsunamiProjectPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + TsunamiProjectPeer::NUM_COLUMNS;

		$c->addJoin(TsunamiSitePeer::TSUNAMI_PROJECT_ID, TsunamiProjectPeer::TSUNAMI_PROJECT_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiSitePeer::getOMClass();


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
					$temp_obj2->addTsunamiSite($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initTsunamiSites();
				$obj2->addTsunamiSite($obj1);
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
		return TsunamiSitePeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a TsunamiSite or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiSite object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from TsunamiSite object
		}

		$criteria->remove(TsunamiSitePeer::TSUNAMI_SITE_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a TsunamiSite or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiSite object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(TsunamiSitePeer::TSUNAMI_SITE_ID);
			$selectCriteria->add(TsunamiSitePeer::TSUNAMI_SITE_ID, $criteria->remove(TsunamiSitePeer::TSUNAMI_SITE_ID), $comparison);

		} else { // $values is TsunamiSite object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the TSUNAMI_SITE table.
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
			$affectedRows += BasePeer::doDeleteAll(TsunamiSitePeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a TsunamiSite or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or TsunamiSite object or primary key or array of primary keys
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
			$con = Propel::getConnection(TsunamiSitePeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof TsunamiSite) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(TsunamiSitePeer::TSUNAMI_SITE_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given TsunamiSite object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      TsunamiSite $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(TsunamiSite $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(TsunamiSitePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(TsunamiSitePeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSitePeer::BOUNDING_POLYGON))
			$columns[TsunamiSitePeer::BOUNDING_POLYGON] = $obj->getBoundingPolygon();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSitePeer::COUNTRY))
			$columns[TsunamiSitePeer::COUNTRY] = $obj->getCountry();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSitePeer::DESCRIPTION))
			$columns[TsunamiSitePeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSitePeer::NAME))
			$columns[TsunamiSitePeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSitePeer::SITE_LAT))
			$columns[TsunamiSitePeer::SITE_LAT] = $obj->getSiteLatitude();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSitePeer::SITE_LON))
			$columns[TsunamiSitePeer::SITE_LON] = $obj->getSiteLongitude();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSitePeer::TSUNAMI_PROJECT_ID))
			$columns[TsunamiSitePeer::TSUNAMI_PROJECT_ID] = $obj->getTsunamiProjectId();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSitePeer::TYPE))
			$columns[TsunamiSitePeer::TYPE] = $obj->getType();

		}

		return BasePeer::doValidate(TsunamiSitePeer::DATABASE_NAME, TsunamiSitePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     TsunamiSite
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(TsunamiSitePeer::DATABASE_NAME);

		$criteria->add(TsunamiSitePeer::TSUNAMI_SITE_ID, $pk);


		$v = TsunamiSitePeer::doSelect($criteria, $con);

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
			$criteria->add(TsunamiSitePeer::TSUNAMI_SITE_ID, $pks, Criteria::IN);
			$objs = TsunamiSitePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseTsunamiSitePeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseTsunamiSitePeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/tsunami/map/TsunamiSiteMapBuilder.php';
	Propel::registerMapBuilder('lib.data.tsunami.map.TsunamiSiteMapBuilder');
}
