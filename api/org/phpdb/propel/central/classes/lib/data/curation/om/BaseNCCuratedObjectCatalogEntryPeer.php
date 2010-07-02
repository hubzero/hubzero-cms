<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by NCCuratedObjectCatalogEntryPeer::getOMClass()
include_once 'lib/data/curation/NCCuratedObjectCatalogEntry.php';

/**
 * Base static class for performing query and update operations on the 'CURATED_OBJECT_CATALOG_ENTRY' table.
 *
 * 
 *
 * @package    lib.data.curation.om
 */
abstract class BaseNCCuratedObjectCatalogEntryPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'CURATED_OBJECT_CATALOG_ENTRY';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.curation.NCCuratedObjectCatalogEntry';

	/** The total number of columns. */
	const NUM_COLUMNS = 6;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'CURATED_OBJECT_CATALOG_ENTRY.ID';

	/** the column name for the CREATED_BY field */
	const CREATED_BY = 'CURATED_OBJECT_CATALOG_ENTRY.CREATED_BY';

	/** the column name for the CREATED_DATE field */
	const CREATED_DATE = 'CURATED_OBJECT_CATALOG_ENTRY.CREATED_DATE';

	/** the column name for the OBJECT_ID field */
	const OBJECT_ID = 'CURATED_OBJECT_CATALOG_ENTRY.OBJECT_ID';

	/** the column name for the ONTOLOGY_TERM field */
	const ONTOLOGY_TERM = 'CURATED_OBJECT_CATALOG_ENTRY.ONTOLOGY_TERM';

	/** the column name for the RELEVANCE_LEVEL field */
	const RELEVANCE_LEVEL = 'CURATED_OBJECT_CATALOG_ENTRY.RELEVANCE_LEVEL';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CreatedBy', 'CreatedDate', 'ObjectId', 'OntologyTerm', 'RelevanceLevel', ),
		BasePeer::TYPE_COLNAME => array (NCCuratedObjectCatalogEntryPeer::ID, NCCuratedObjectCatalogEntryPeer::CREATED_BY, NCCuratedObjectCatalogEntryPeer::CREATED_DATE, NCCuratedObjectCatalogEntryPeer::OBJECT_ID, NCCuratedObjectCatalogEntryPeer::ONTOLOGY_TERM, NCCuratedObjectCatalogEntryPeer::RELEVANCE_LEVEL, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'CREATED_BY', 'CREATED_DATE', 'OBJECT_ID', 'ONTOLOGY_TERM', 'RELEVANCE_LEVEL', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CreatedBy' => 1, 'CreatedDate' => 2, 'ObjectId' => 3, 'OntologyTerm' => 4, 'RelevanceLevel' => 5, ),
		BasePeer::TYPE_COLNAME => array (NCCuratedObjectCatalogEntryPeer::ID => 0, NCCuratedObjectCatalogEntryPeer::CREATED_BY => 1, NCCuratedObjectCatalogEntryPeer::CREATED_DATE => 2, NCCuratedObjectCatalogEntryPeer::OBJECT_ID => 3, NCCuratedObjectCatalogEntryPeer::ONTOLOGY_TERM => 4, NCCuratedObjectCatalogEntryPeer::RELEVANCE_LEVEL => 5, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'CREATED_BY' => 1, 'CREATED_DATE' => 2, 'OBJECT_ID' => 3, 'ONTOLOGY_TERM' => 4, 'RELEVANCE_LEVEL' => 5, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/curation/map/NCCuratedObjectCatalogEntryMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.curation.map.NCCuratedObjectCatalogEntryMapBuilder');
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
			$map = NCCuratedObjectCatalogEntryPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. NCCuratedObjectCatalogEntryPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(NCCuratedObjectCatalogEntryPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::ID);

		$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::CREATED_BY);

		$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::CREATED_DATE);

		$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::OBJECT_ID);

		$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::ONTOLOGY_TERM);

		$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::RELEVANCE_LEVEL);

	}

	const COUNT = 'COUNT(CURATED_OBJECT_CATALOG_ENTRY.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT CURATED_OBJECT_CATALOG_ENTRY.ID)';

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
			$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = NCCuratedObjectCatalogEntryPeer::doSelectRS($criteria, $con);
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
	 * @return     NCCuratedObjectCatalogEntry
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = NCCuratedObjectCatalogEntryPeer::doSelect($critcopy, $con);
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
		return NCCuratedObjectCatalogEntryPeer::populateObjects(NCCuratedObjectCatalogEntryPeer::doSelectRS($criteria, $con));
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
			NCCuratedObjectCatalogEntryPeer::addSelectColumns($criteria);
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
		$cls = NCCuratedObjectCatalogEntryPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related NCCuratedObjects table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinNCCuratedObjects(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(NCCuratedObjectCatalogEntryPeer::OBJECT_ID, NCCuratedObjectsPeer::OBJECT_ID);

		$rs = NCCuratedObjectCatalogEntryPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of NCCuratedObjectCatalogEntry objects pre-filled with their NCCuratedObjects objects.
	 *
	 * @return     array Array of NCCuratedObjectCatalogEntry objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinNCCuratedObjects(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		NCCuratedObjectCatalogEntryPeer::addSelectColumns($c);
		$startcol = (NCCuratedObjectCatalogEntryPeer::NUM_COLUMNS - NCCuratedObjectCatalogEntryPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		NCCuratedObjectsPeer::addSelectColumns($c);

		$c->addJoin(NCCuratedObjectCatalogEntryPeer::OBJECT_ID, NCCuratedObjectsPeer::OBJECT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = NCCuratedObjectCatalogEntryPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = NCCuratedObjectsPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getNCCuratedObjects(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addNCCuratedObjectCatalogEntry($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initNCCuratedObjectCatalogEntrys();
				$obj2->addNCCuratedObjectCatalogEntry($obj1); //CHECKME
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
			$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(NCCuratedObjectCatalogEntryPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(NCCuratedObjectCatalogEntryPeer::OBJECT_ID, NCCuratedObjectsPeer::OBJECT_ID);

		$rs = NCCuratedObjectCatalogEntryPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of NCCuratedObjectCatalogEntry objects pre-filled with all related objects.
	 *
	 * @return     array Array of NCCuratedObjectCatalogEntry objects.
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

		NCCuratedObjectCatalogEntryPeer::addSelectColumns($c);
		$startcol2 = (NCCuratedObjectCatalogEntryPeer::NUM_COLUMNS - NCCuratedObjectCatalogEntryPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		NCCuratedObjectsPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + NCCuratedObjectsPeer::NUM_COLUMNS;

		$c->addJoin(NCCuratedObjectCatalogEntryPeer::OBJECT_ID, NCCuratedObjectsPeer::OBJECT_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = NCCuratedObjectCatalogEntryPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined NCCuratedObjects rows
	
			$omClass = NCCuratedObjectsPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getNCCuratedObjects(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addNCCuratedObjectCatalogEntry($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initNCCuratedObjectCatalogEntrys();
				$obj2->addNCCuratedObjectCatalogEntry($obj1);
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
		return NCCuratedObjectCatalogEntryPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a NCCuratedObjectCatalogEntry or Criteria object.
	 *
	 * @param      mixed $values Criteria or NCCuratedObjectCatalogEntry object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from NCCuratedObjectCatalogEntry object
		}

		$criteria->remove(NCCuratedObjectCatalogEntryPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a NCCuratedObjectCatalogEntry or Criteria object.
	 *
	 * @param      mixed $values Criteria or NCCuratedObjectCatalogEntry object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(NCCuratedObjectCatalogEntryPeer::ID);
			$selectCriteria->add(NCCuratedObjectCatalogEntryPeer::ID, $criteria->remove(NCCuratedObjectCatalogEntryPeer::ID), $comparison);

		} else { // $values is NCCuratedObjectCatalogEntry object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the CURATED_OBJECT_CATALOG_ENTRY table.
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
			$affectedRows += BasePeer::doDeleteAll(NCCuratedObjectCatalogEntryPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a NCCuratedObjectCatalogEntry or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or NCCuratedObjectCatalogEntry object or primary key or array of primary keys
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
			$con = Propel::getConnection(NCCuratedObjectCatalogEntryPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof NCCuratedObjectCatalogEntry) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(NCCuratedObjectCatalogEntryPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given NCCuratedObjectCatalogEntry object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      NCCuratedObjectCatalogEntry $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(NCCuratedObjectCatalogEntry $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(NCCuratedObjectCatalogEntryPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(NCCuratedObjectCatalogEntryPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectCatalogEntryPeer::CREATED_BY))
			$columns[NCCuratedObjectCatalogEntryPeer::CREATED_BY] = $obj->getCreatedBy();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectCatalogEntryPeer::CREATED_DATE))
			$columns[NCCuratedObjectCatalogEntryPeer::CREATED_DATE] = $obj->getCreatedDate();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectCatalogEntryPeer::OBJECT_ID))
			$columns[NCCuratedObjectCatalogEntryPeer::OBJECT_ID] = $obj->getObjectId();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectCatalogEntryPeer::ONTOLOGY_TERM))
			$columns[NCCuratedObjectCatalogEntryPeer::ONTOLOGY_TERM] = $obj->getOntologyTerm();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectCatalogEntryPeer::RELEVANCE_LEVEL))
			$columns[NCCuratedObjectCatalogEntryPeer::RELEVANCE_LEVEL] = $obj->getRelevanceLevel();

		}

		return BasePeer::doValidate(NCCuratedObjectCatalogEntryPeer::DATABASE_NAME, NCCuratedObjectCatalogEntryPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     NCCuratedObjectCatalogEntry
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(NCCuratedObjectCatalogEntryPeer::DATABASE_NAME);

		$criteria->add(NCCuratedObjectCatalogEntryPeer::ID, $pk);


		$v = NCCuratedObjectCatalogEntryPeer::doSelect($criteria, $con);

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
			$criteria->add(NCCuratedObjectCatalogEntryPeer::ID, $pks, Criteria::IN);
			$objs = NCCuratedObjectCatalogEntryPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseNCCuratedObjectCatalogEntryPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseNCCuratedObjectCatalogEntryPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/curation/map/NCCuratedObjectCatalogEntryMapBuilder.php';
	Propel::registerMapBuilder('lib.data.curation.map.NCCuratedObjectCatalogEntryMapBuilder');
}
