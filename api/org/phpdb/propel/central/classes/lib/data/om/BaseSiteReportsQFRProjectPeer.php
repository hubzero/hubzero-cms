<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by SiteReportsQFRProjectPeer::getOMClass()
include_once 'lib/data/SiteReportsQFRProject.php';

/**
 * Base static class for performing query and update operations on the 'SITEREPORTS_QFR_PROJECT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSiteReportsQFRProjectPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'SITEREPORTS_QFR_PROJECT';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.SiteReportsQFRProject';

	/** The total number of columns. */
	const NUM_COLUMNS = 14;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'SITEREPORTS_QFR_PROJECT.ID';

	/** the column name for the QFR_ID field */
	const QFR_ID = 'SITEREPORTS_QFR_PROJECT.QFR_ID';

	/** the column name for the PI field */
	const PI = 'SITEREPORTS_QFR_PROJECT.PI';

	/** the column name for the PROJECT_ID field */
	const PROJECT_ID = 'SITEREPORTS_QFR_PROJECT.PROJECT_ID';

	/** the column name for the PROJECT_NUMBER field */
	const PROJECT_NUMBER = 'SITEREPORTS_QFR_PROJECT.PROJECT_NUMBER';

	/** the column name for the P_COST field */
	const P_COST = 'SITEREPORTS_QFR_PROJECT.P_COST';

	/** the column name for the E_COST field */
	const E_COST = 'SITEREPORTS_QFR_PROJECT.E_COST';

	/** the column name for the PSC_COST field */
	const PSC_COST = 'SITEREPORTS_QFR_PROJECT.PSC_COST';

	/** the column name for the ODC_COST field */
	const ODC_COST = 'SITEREPORTS_QFR_PROJECT.ODC_COST';

	/** the column name for the IC_COST field */
	const IC_COST = 'SITEREPORTS_QFR_PROJECT.IC_COST';

	/** the column name for the CREATED_BY field */
	const CREATED_BY = 'SITEREPORTS_QFR_PROJECT.CREATED_BY';

	/** the column name for the CREATED_ON field */
	const CREATED_ON = 'SITEREPORTS_QFR_PROJECT.CREATED_ON';

	/** the column name for the UPDATED_BY field */
	const UPDATED_BY = 'SITEREPORTS_QFR_PROJECT.UPDATED_BY';

	/** the column name for the UPDATED_ON field */
	const UPDATED_ON = 'SITEREPORTS_QFR_PROJECT.UPDATED_ON';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('ID', 'QFR_ID', 'PI', 'PROJECT_ID', 'PROJECT_NUMBER', 'P_COST', 'E_COST', 'PSC_COST', 'ODC_COST', 'IC_COST', 'CREATED_BY', 'CREATED_ON', 'UPDATED_BY', 'UPDATED_ON', ),
		BasePeer::TYPE_COLNAME => array (SiteReportsQFRProjectPeer::ID, SiteReportsQFRProjectPeer::QFR_ID, SiteReportsQFRProjectPeer::PI, SiteReportsQFRProjectPeer::PROJECT_ID, SiteReportsQFRProjectPeer::PROJECT_NUMBER, SiteReportsQFRProjectPeer::P_COST, SiteReportsQFRProjectPeer::E_COST, SiteReportsQFRProjectPeer::PSC_COST, SiteReportsQFRProjectPeer::ODC_COST, SiteReportsQFRProjectPeer::IC_COST, SiteReportsQFRProjectPeer::CREATED_BY, SiteReportsQFRProjectPeer::CREATED_ON, SiteReportsQFRProjectPeer::UPDATED_BY, SiteReportsQFRProjectPeer::UPDATED_ON, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'QFR_ID', 'PI', 'PROJECT_ID', 'PROJECT_NUMBER', 'P_COST', 'E_COST', 'PSC_COST', 'ODC_COST', 'IC_COST', 'CREATED_BY', 'CREATED_ON', 'UPDATED_BY', 'UPDATED_ON', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('ID' => 0, 'QFR_ID' => 1, 'PI' => 2, 'PROJECT_ID' => 3, 'PROJECT_NUMBER' => 4, 'P_COST' => 5, 'E_COST' => 6, 'PSC_COST' => 7, 'ODC_COST' => 8, 'IC_COST' => 9, 'CREATED_BY' => 10, 'CREATED_ON' => 11, 'UPDATED_BY' => 12, 'UPDATED_ON' => 13, ),
		BasePeer::TYPE_COLNAME => array (SiteReportsQFRProjectPeer::ID => 0, SiteReportsQFRProjectPeer::QFR_ID => 1, SiteReportsQFRProjectPeer::PI => 2, SiteReportsQFRProjectPeer::PROJECT_ID => 3, SiteReportsQFRProjectPeer::PROJECT_NUMBER => 4, SiteReportsQFRProjectPeer::P_COST => 5, SiteReportsQFRProjectPeer::E_COST => 6, SiteReportsQFRProjectPeer::PSC_COST => 7, SiteReportsQFRProjectPeer::ODC_COST => 8, SiteReportsQFRProjectPeer::IC_COST => 9, SiteReportsQFRProjectPeer::CREATED_BY => 10, SiteReportsQFRProjectPeer::CREATED_ON => 11, SiteReportsQFRProjectPeer::UPDATED_BY => 12, SiteReportsQFRProjectPeer::UPDATED_ON => 13, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'QFR_ID' => 1, 'PI' => 2, 'PROJECT_ID' => 3, 'PROJECT_NUMBER' => 4, 'P_COST' => 5, 'E_COST' => 6, 'PSC_COST' => 7, 'ODC_COST' => 8, 'IC_COST' => 9, 'CREATED_BY' => 10, 'CREATED_ON' => 11, 'UPDATED_BY' => 12, 'UPDATED_ON' => 13, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/SiteReportsQFRProjectMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.SiteReportsQFRProjectMapBuilder');
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
			$map = SiteReportsQFRProjectPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. SiteReportsQFRProjectPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(SiteReportsQFRProjectPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::ID);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::QFR_ID);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::PI);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::PROJECT_ID);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::PROJECT_NUMBER);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::P_COST);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::E_COST);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::PSC_COST);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::ODC_COST);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::IC_COST);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::CREATED_BY);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::CREATED_ON);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::UPDATED_BY);

		$criteria->addSelectColumn(SiteReportsQFRProjectPeer::UPDATED_ON);

	}

	const COUNT = 'COUNT(SITEREPORTS_QFR_PROJECT.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT SITEREPORTS_QFR_PROJECT.ID)';

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
			$criteria->addSelectColumn(SiteReportsQFRProjectPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SiteReportsQFRProjectPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = SiteReportsQFRProjectPeer::doSelectRS($criteria, $con);
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
	 * @return     SiteReportsQFRProject
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = SiteReportsQFRProjectPeer::doSelect($critcopy, $con);
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
		return SiteReportsQFRProjectPeer::populateObjects(SiteReportsQFRProjectPeer::doSelectRS($criteria, $con));
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
			SiteReportsQFRProjectPeer::addSelectColumns($criteria);
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
		$cls = SiteReportsQFRProjectPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related SiteReportsQFR table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinSiteReportsQFR(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SiteReportsQFRProjectPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SiteReportsQFRProjectPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SiteReportsQFRProjectPeer::QFR_ID, SiteReportsQFRPeer::ID);

		$rs = SiteReportsQFRProjectPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SiteReportsQFRProject objects pre-filled with their SiteReportsQFR objects.
	 *
	 * @return     array Array of SiteReportsQFRProject objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinSiteReportsQFR(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SiteReportsQFRProjectPeer::addSelectColumns($c);
		$startcol = (SiteReportsQFRProjectPeer::NUM_COLUMNS - SiteReportsQFRProjectPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		SiteReportsQFRPeer::addSelectColumns($c);

		$c->addJoin(SiteReportsQFRProjectPeer::QFR_ID, SiteReportsQFRPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SiteReportsQFRProjectPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SiteReportsQFRPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getSiteReportsQFR(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addSiteReportsQFRProject($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initSiteReportsQFRProjects();
				$obj2->addSiteReportsQFRProject($obj1); //CHECKME
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
			$criteria->addSelectColumn(SiteReportsQFRProjectPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SiteReportsQFRProjectPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SiteReportsQFRProjectPeer::QFR_ID, SiteReportsQFRPeer::ID);

		$rs = SiteReportsQFRProjectPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SiteReportsQFRProject objects pre-filled with all related objects.
	 *
	 * @return     array Array of SiteReportsQFRProject objects.
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

		SiteReportsQFRProjectPeer::addSelectColumns($c);
		$startcol2 = (SiteReportsQFRProjectPeer::NUM_COLUMNS - SiteReportsQFRProjectPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SiteReportsQFRPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SiteReportsQFRPeer::NUM_COLUMNS;

		$c->addJoin(SiteReportsQFRProjectPeer::QFR_ID, SiteReportsQFRPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SiteReportsQFRProjectPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined SiteReportsQFR rows
	
			$omClass = SiteReportsQFRPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSiteReportsQFR(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSiteReportsQFRProject($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initSiteReportsQFRProjects();
				$obj2->addSiteReportsQFRProject($obj1);
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
		return SiteReportsQFRProjectPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a SiteReportsQFRProject or Criteria object.
	 *
	 * @param      mixed $values Criteria or SiteReportsQFRProject object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from SiteReportsQFRProject object
		}

		$criteria->remove(SiteReportsQFRProjectPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a SiteReportsQFRProject or Criteria object.
	 *
	 * @param      mixed $values Criteria or SiteReportsQFRProject object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(SiteReportsQFRProjectPeer::ID);
			$selectCriteria->add(SiteReportsQFRProjectPeer::ID, $criteria->remove(SiteReportsQFRProjectPeer::ID), $comparison);

		} else { // $values is SiteReportsQFRProject object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the SITEREPORTS_QFR_PROJECT table.
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
			$affectedRows += BasePeer::doDeleteAll(SiteReportsQFRProjectPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a SiteReportsQFRProject or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or SiteReportsQFRProject object or primary key or array of primary keys
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
			$con = Propel::getConnection(SiteReportsQFRProjectPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof SiteReportsQFRProject) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(SiteReportsQFRProjectPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given SiteReportsQFRProject object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      SiteReportsQFRProject $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(SiteReportsQFRProject $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(SiteReportsQFRProjectPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(SiteReportsQFRProjectPeer::TABLE_NAME);

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

		}

		return BasePeer::doValidate(SiteReportsQFRProjectPeer::DATABASE_NAME, SiteReportsQFRProjectPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     SiteReportsQFRProject
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(SiteReportsQFRProjectPeer::DATABASE_NAME);

		$criteria->add(SiteReportsQFRProjectPeer::ID, $pk);


		$v = SiteReportsQFRProjectPeer::doSelect($criteria, $con);

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
			$criteria->add(SiteReportsQFRProjectPeer::ID, $pks, Criteria::IN);
			$objs = SiteReportsQFRProjectPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseSiteReportsQFRProjectPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseSiteReportsQFRProjectPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/SiteReportsQFRProjectMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.SiteReportsQFRProjectMapBuilder');
}
