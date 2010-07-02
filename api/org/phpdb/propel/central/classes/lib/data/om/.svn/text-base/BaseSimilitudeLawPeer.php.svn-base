<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by SimilitudeLawPeer::getOMClass()
include_once 'lib/data/SimilitudeLaw.php';

/**
 * Base static class for performing query and update operations on the 'SIMILITUDE_LAW' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSimilitudeLawPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'SIMILITUDE_LAW';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.SimilitudeLaw';

	/** The total number of columns. */
	const NUM_COLUMNS = 10;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'SIMILITUDE_LAW.ID';

	/** the column name for the COMPUTE_EQUATION field */
	const COMPUTE_EQUATION = 'SIMILITUDE_LAW.COMPUTE_EQUATION';

	/** the column name for the DEPENDENCE field */
	const DEPENDENCE = 'SIMILITUDE_LAW.DEPENDENCE';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'SIMILITUDE_LAW.DESCRIPTION';

	/** the column name for the DISPLAY_EQUATION field */
	const DISPLAY_EQUATION = 'SIMILITUDE_LAW.DISPLAY_EQUATION';

	/** the column name for the NAME field */
	const NAME = 'SIMILITUDE_LAW.NAME';

	/** the column name for the SIMILITUDE_LAW_GROUP_ID field */
	const SIMILITUDE_LAW_GROUP_ID = 'SIMILITUDE_LAW.SIMILITUDE_LAW_GROUP_ID';

	/** the column name for the SYMBOL field */
	const SYMBOL = 'SIMILITUDE_LAW.SYMBOL';

	/** the column name for the SYSTEM_NAME field */
	const SYSTEM_NAME = 'SIMILITUDE_LAW.SYSTEM_NAME';

	/** the column name for the UNIT_DESCRIPTION field */
	const UNIT_DESCRIPTION = 'SIMILITUDE_LAW.UNIT_DESCRIPTION';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'ComputeEquation', 'Dependence', 'Description', 'DisplayEquation', 'Name', 'SimilitudeLawGroupId', 'Symbol', 'SystemName', 'UnitDescription', ),
		BasePeer::TYPE_COLNAME => array (SimilitudeLawPeer::ID, SimilitudeLawPeer::COMPUTE_EQUATION, SimilitudeLawPeer::DEPENDENCE, SimilitudeLawPeer::DESCRIPTION, SimilitudeLawPeer::DISPLAY_EQUATION, SimilitudeLawPeer::NAME, SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID, SimilitudeLawPeer::SYMBOL, SimilitudeLawPeer::SYSTEM_NAME, SimilitudeLawPeer::UNIT_DESCRIPTION, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'COMPUTE_EQUATION', 'DEPENDENCE', 'DESCRIPTION', 'DISPLAY_EQUATION', 'NAME', 'SIMILITUDE_LAW_GROUP_ID', 'SYMBOL', 'SYSTEM_NAME', 'UNIT_DESCRIPTION', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'ComputeEquation' => 1, 'Dependence' => 2, 'Description' => 3, 'DisplayEquation' => 4, 'Name' => 5, 'SimilitudeLawGroupId' => 6, 'Symbol' => 7, 'SystemName' => 8, 'UnitDescription' => 9, ),
		BasePeer::TYPE_COLNAME => array (SimilitudeLawPeer::ID => 0, SimilitudeLawPeer::COMPUTE_EQUATION => 1, SimilitudeLawPeer::DEPENDENCE => 2, SimilitudeLawPeer::DESCRIPTION => 3, SimilitudeLawPeer::DISPLAY_EQUATION => 4, SimilitudeLawPeer::NAME => 5, SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID => 6, SimilitudeLawPeer::SYMBOL => 7, SimilitudeLawPeer::SYSTEM_NAME => 8, SimilitudeLawPeer::UNIT_DESCRIPTION => 9, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'COMPUTE_EQUATION' => 1, 'DEPENDENCE' => 2, 'DESCRIPTION' => 3, 'DISPLAY_EQUATION' => 4, 'NAME' => 5, 'SIMILITUDE_LAW_GROUP_ID' => 6, 'SYMBOL' => 7, 'SYSTEM_NAME' => 8, 'UNIT_DESCRIPTION' => 9, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/SimilitudeLawMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.SimilitudeLawMapBuilder');
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
			$map = SimilitudeLawPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. SimilitudeLawPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(SimilitudeLawPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(SimilitudeLawPeer::ID);

		$criteria->addSelectColumn(SimilitudeLawPeer::COMPUTE_EQUATION);

		$criteria->addSelectColumn(SimilitudeLawPeer::DEPENDENCE);

		$criteria->addSelectColumn(SimilitudeLawPeer::DESCRIPTION);

		$criteria->addSelectColumn(SimilitudeLawPeer::DISPLAY_EQUATION);

		$criteria->addSelectColumn(SimilitudeLawPeer::NAME);

		$criteria->addSelectColumn(SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID);

		$criteria->addSelectColumn(SimilitudeLawPeer::SYMBOL);

		$criteria->addSelectColumn(SimilitudeLawPeer::SYSTEM_NAME);

		$criteria->addSelectColumn(SimilitudeLawPeer::UNIT_DESCRIPTION);

	}

	const COUNT = 'COUNT(SIMILITUDE_LAW.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT SIMILITUDE_LAW.ID)';

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
			$criteria->addSelectColumn(SimilitudeLawPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SimilitudeLawPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = SimilitudeLawPeer::doSelectRS($criteria, $con);
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
	 * @return     SimilitudeLaw
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = SimilitudeLawPeer::doSelect($critcopy, $con);
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
		return SimilitudeLawPeer::populateObjects(SimilitudeLawPeer::doSelectRS($criteria, $con));
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
			SimilitudeLawPeer::addSelectColumns($criteria);
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
		$cls = SimilitudeLawPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related SimilitudeLawGroup table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinSimilitudeLawGroup(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SimilitudeLawPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SimilitudeLawPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID, SimilitudeLawGroupPeer::ID);

		$rs = SimilitudeLawPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SimilitudeLaw objects pre-filled with their SimilitudeLawGroup objects.
	 *
	 * @return     array Array of SimilitudeLaw objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinSimilitudeLawGroup(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SimilitudeLawPeer::addSelectColumns($c);
		$startcol = (SimilitudeLawPeer::NUM_COLUMNS - SimilitudeLawPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		SimilitudeLawGroupPeer::addSelectColumns($c);

		$c->addJoin(SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID, SimilitudeLawGroupPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SimilitudeLawPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SimilitudeLawGroupPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getSimilitudeLawGroup(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addSimilitudeLaw($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initSimilitudeLaws();
				$obj2->addSimilitudeLaw($obj1); //CHECKME
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
			$criteria->addSelectColumn(SimilitudeLawPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SimilitudeLawPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID, SimilitudeLawGroupPeer::ID);

		$rs = SimilitudeLawPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SimilitudeLaw objects pre-filled with all related objects.
	 *
	 * @return     array Array of SimilitudeLaw objects.
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

		SimilitudeLawPeer::addSelectColumns($c);
		$startcol2 = (SimilitudeLawPeer::NUM_COLUMNS - SimilitudeLawPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SimilitudeLawGroupPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SimilitudeLawGroupPeer::NUM_COLUMNS;

		$c->addJoin(SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID, SimilitudeLawGroupPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SimilitudeLawPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined SimilitudeLawGroup rows
	
			$omClass = SimilitudeLawGroupPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSimilitudeLawGroup(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSimilitudeLaw($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initSimilitudeLaws();
				$obj2->addSimilitudeLaw($obj1);
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
		return SimilitudeLawPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a SimilitudeLaw or Criteria object.
	 *
	 * @param      mixed $values Criteria or SimilitudeLaw object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from SimilitudeLaw object
		}

		$criteria->remove(SimilitudeLawPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a SimilitudeLaw or Criteria object.
	 *
	 * @param      mixed $values Criteria or SimilitudeLaw object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(SimilitudeLawPeer::ID);
			$selectCriteria->add(SimilitudeLawPeer::ID, $criteria->remove(SimilitudeLawPeer::ID), $comparison);

		} else { // $values is SimilitudeLaw object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the SIMILITUDE_LAW table.
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
			$affectedRows += BasePeer::doDeleteAll(SimilitudeLawPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a SimilitudeLaw or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or SimilitudeLaw object or primary key or array of primary keys
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
			$con = Propel::getConnection(SimilitudeLawPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof SimilitudeLaw) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(SimilitudeLawPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given SimilitudeLaw object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      SimilitudeLaw $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(SimilitudeLaw $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(SimilitudeLawPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(SimilitudeLawPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(SimilitudeLawPeer::COMPUTE_EQUATION))
			$columns[SimilitudeLawPeer::COMPUTE_EQUATION] = $obj->getComputeEquation();

		if ($obj->isNew() || $obj->isColumnModified(SimilitudeLawPeer::DEPENDENCE))
			$columns[SimilitudeLawPeer::DEPENDENCE] = $obj->getDependence();

		if ($obj->isNew() || $obj->isColumnModified(SimilitudeLawPeer::DESCRIPTION))
			$columns[SimilitudeLawPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(SimilitudeLawPeer::DISPLAY_EQUATION))
			$columns[SimilitudeLawPeer::DISPLAY_EQUATION] = $obj->getDisplayEquation();

		if ($obj->isNew() || $obj->isColumnModified(SimilitudeLawPeer::NAME))
			$columns[SimilitudeLawPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID))
			$columns[SimilitudeLawPeer::SIMILITUDE_LAW_GROUP_ID] = $obj->getSimilitudeLawGroupId();

		if ($obj->isNew() || $obj->isColumnModified(SimilitudeLawPeer::SYMBOL))
			$columns[SimilitudeLawPeer::SYMBOL] = $obj->getSymbol();

		if ($obj->isNew() || $obj->isColumnModified(SimilitudeLawPeer::SYSTEM_NAME))
			$columns[SimilitudeLawPeer::SYSTEM_NAME] = $obj->getSystemName();

		if ($obj->isNew() || $obj->isColumnModified(SimilitudeLawPeer::UNIT_DESCRIPTION))
			$columns[SimilitudeLawPeer::UNIT_DESCRIPTION] = $obj->getUnitDescription();

		}

		return BasePeer::doValidate(SimilitudeLawPeer::DATABASE_NAME, SimilitudeLawPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     SimilitudeLaw
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(SimilitudeLawPeer::DATABASE_NAME);

		$criteria->add(SimilitudeLawPeer::ID, $pk);


		$v = SimilitudeLawPeer::doSelect($criteria, $con);

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
			$criteria->add(SimilitudeLawPeer::ID, $pks, Criteria::IN);
			$objs = SimilitudeLawPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseSimilitudeLawPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseSimilitudeLawPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/SimilitudeLawMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.SimilitudeLawMapBuilder');
}
