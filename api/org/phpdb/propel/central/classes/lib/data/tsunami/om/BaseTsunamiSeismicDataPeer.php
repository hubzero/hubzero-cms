<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by TsunamiSeismicDataPeer::getOMClass()
include_once 'lib/data/tsunami/TsunamiSeismicData.php';

/**
 * Base static class for performing query and update operations on the 'TSUNAMI_SEISMIC_DATA' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiSeismicDataPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'TSUNAMI_SEISMIC_DATA';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.tsunami.TsunamiSeismicData';

	/** The total number of columns. */
	const NUM_COLUMNS = 10;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the TSUNAMI_SEISMIC_DATA_ID field */
	const TSUNAMI_SEISMIC_DATA_ID = 'TSUNAMI_SEISMIC_DATA.TSUNAMI_SEISMIC_DATA_ID';

	/** the column name for the LOCAL field */
	const LOCAL = 'TSUNAMI_SEISMIC_DATA.LOCAL';

	/** the column name for the LOCAL_DATA_SOURCES field */
	const LOCAL_DATA_SOURCES = 'TSUNAMI_SEISMIC_DATA.LOCAL_DATA_SOURCES';

	/** the column name for the LOCAL_TYPE field */
	const LOCAL_TYPE = 'TSUNAMI_SEISMIC_DATA.LOCAL_TYPE';

	/** the column name for the MEASURES field */
	const MEASURES = 'TSUNAMI_SEISMIC_DATA.MEASURES';

	/** the column name for the MEASURES_SITE_CONFIG field */
	const MEASURES_SITE_CONFIG = 'TSUNAMI_SEISMIC_DATA.MEASURES_SITE_CONFIG';

	/** the column name for the MEASURES_TYPES field */
	const MEASURES_TYPES = 'TSUNAMI_SEISMIC_DATA.MEASURES_TYPES';

	/** the column name for the MIA field */
	const MIA = 'TSUNAMI_SEISMIC_DATA.MIA';

	/** the column name for the MIA_SOURCE field */
	const MIA_SOURCE = 'TSUNAMI_SEISMIC_DATA.MIA_SOURCE';

	/** the column name for the TSUNAMI_DOC_LIB_ID field */
	const TSUNAMI_DOC_LIB_ID = 'TSUNAMI_SEISMIC_DATA.TSUNAMI_DOC_LIB_ID';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Local', 'LocalDataSources', 'LocalType', 'Measures', 'MeasuresSiteConfig', 'MeasuresTypes', 'Mia', 'MiaSource', 'TsunamiDocLibId', ),
		BasePeer::TYPE_COLNAME => array (TsunamiSeismicDataPeer::TSUNAMI_SEISMIC_DATA_ID, TsunamiSeismicDataPeer::LOCAL, TsunamiSeismicDataPeer::LOCAL_DATA_SOURCES, TsunamiSeismicDataPeer::LOCAL_TYPE, TsunamiSeismicDataPeer::MEASURES, TsunamiSeismicDataPeer::MEASURES_SITE_CONFIG, TsunamiSeismicDataPeer::MEASURES_TYPES, TsunamiSeismicDataPeer::MIA, TsunamiSeismicDataPeer::MIA_SOURCE, TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_SEISMIC_DATA_ID', 'LOCAL', 'LOCAL_DATA_SOURCES', 'LOCAL_TYPE', 'MEASURES', 'MEASURES_SITE_CONFIG', 'MEASURES_TYPES', 'MIA', 'MIA_SOURCE', 'TSUNAMI_DOC_LIB_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Local' => 1, 'LocalDataSources' => 2, 'LocalType' => 3, 'Measures' => 4, 'MeasuresSiteConfig' => 5, 'MeasuresTypes' => 6, 'Mia' => 7, 'MiaSource' => 8, 'TsunamiDocLibId' => 9, ),
		BasePeer::TYPE_COLNAME => array (TsunamiSeismicDataPeer::TSUNAMI_SEISMIC_DATA_ID => 0, TsunamiSeismicDataPeer::LOCAL => 1, TsunamiSeismicDataPeer::LOCAL_DATA_SOURCES => 2, TsunamiSeismicDataPeer::LOCAL_TYPE => 3, TsunamiSeismicDataPeer::MEASURES => 4, TsunamiSeismicDataPeer::MEASURES_SITE_CONFIG => 5, TsunamiSeismicDataPeer::MEASURES_TYPES => 6, TsunamiSeismicDataPeer::MIA => 7, TsunamiSeismicDataPeer::MIA_SOURCE => 8, TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID => 9, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_SEISMIC_DATA_ID' => 0, 'LOCAL' => 1, 'LOCAL_DATA_SOURCES' => 2, 'LOCAL_TYPE' => 3, 'MEASURES' => 4, 'MEASURES_SITE_CONFIG' => 5, 'MEASURES_TYPES' => 6, 'MIA' => 7, 'MIA_SOURCE' => 8, 'TSUNAMI_DOC_LIB_ID' => 9, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/tsunami/map/TsunamiSeismicDataMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.tsunami.map.TsunamiSeismicDataMapBuilder');
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
			$map = TsunamiSeismicDataPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. TsunamiSeismicDataPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(TsunamiSeismicDataPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(TsunamiSeismicDataPeer::TSUNAMI_SEISMIC_DATA_ID);

		$criteria->addSelectColumn(TsunamiSeismicDataPeer::LOCAL);

		$criteria->addSelectColumn(TsunamiSeismicDataPeer::LOCAL_DATA_SOURCES);

		$criteria->addSelectColumn(TsunamiSeismicDataPeer::LOCAL_TYPE);

		$criteria->addSelectColumn(TsunamiSeismicDataPeer::MEASURES);

		$criteria->addSelectColumn(TsunamiSeismicDataPeer::MEASURES_SITE_CONFIG);

		$criteria->addSelectColumn(TsunamiSeismicDataPeer::MEASURES_TYPES);

		$criteria->addSelectColumn(TsunamiSeismicDataPeer::MIA);

		$criteria->addSelectColumn(TsunamiSeismicDataPeer::MIA_SOURCE);

		$criteria->addSelectColumn(TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID);

	}

	const COUNT = 'COUNT(TSUNAMI_SEISMIC_DATA.TSUNAMI_SEISMIC_DATA_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT TSUNAMI_SEISMIC_DATA.TSUNAMI_SEISMIC_DATA_ID)';

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
			$criteria->addSelectColumn(TsunamiSeismicDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiSeismicDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = TsunamiSeismicDataPeer::doSelectRS($criteria, $con);
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
	 * @return     TsunamiSeismicData
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = TsunamiSeismicDataPeer::doSelect($critcopy, $con);
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
		return TsunamiSeismicDataPeer::populateObjects(TsunamiSeismicDataPeer::doSelectRS($criteria, $con));
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
			TsunamiSeismicDataPeer::addSelectColumns($criteria);
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
		$cls = TsunamiSeismicDataPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related TsunamiDocLib table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinTsunamiDocLib(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(TsunamiSeismicDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiSeismicDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = TsunamiSeismicDataPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiSeismicData objects pre-filled with their TsunamiDocLib objects.
	 *
	 * @return     array Array of TsunamiSeismicData objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinTsunamiDocLib(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		TsunamiSeismicDataPeer::addSelectColumns($c);
		$startcol = (TsunamiSeismicDataPeer::NUM_COLUMNS - TsunamiSeismicDataPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		TsunamiDocLibPeer::addSelectColumns($c);

		$c->addJoin(TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiSeismicDataPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = TsunamiDocLibPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getTsunamiDocLib(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addTsunamiSeismicData($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initTsunamiSeismicDatas();
				$obj2->addTsunamiSeismicData($obj1); //CHECKME
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
			$criteria->addSelectColumn(TsunamiSeismicDataPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiSeismicDataPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = TsunamiSeismicDataPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of TsunamiSeismicData objects pre-filled with all related objects.
	 *
	 * @return     array Array of TsunamiSeismicData objects.
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

		TsunamiSeismicDataPeer::addSelectColumns($c);
		$startcol2 = (TsunamiSeismicDataPeer::NUM_COLUMNS - TsunamiSeismicDataPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		TsunamiDocLibPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + TsunamiDocLibPeer::NUM_COLUMNS;

		$c->addJoin(TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID, TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = TsunamiSeismicDataPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined TsunamiDocLib rows
	
			$omClass = TsunamiDocLibPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getTsunamiDocLib(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addTsunamiSeismicData($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initTsunamiSeismicDatas();
				$obj2->addTsunamiSeismicData($obj1);
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
		return TsunamiSeismicDataPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a TsunamiSeismicData or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiSeismicData object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from TsunamiSeismicData object
		}

		$criteria->remove(TsunamiSeismicDataPeer::TSUNAMI_SEISMIC_DATA_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a TsunamiSeismicData or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiSeismicData object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(TsunamiSeismicDataPeer::TSUNAMI_SEISMIC_DATA_ID);
			$selectCriteria->add(TsunamiSeismicDataPeer::TSUNAMI_SEISMIC_DATA_ID, $criteria->remove(TsunamiSeismicDataPeer::TSUNAMI_SEISMIC_DATA_ID), $comparison);

		} else { // $values is TsunamiSeismicData object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the TSUNAMI_SEISMIC_DATA table.
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
			$affectedRows += BasePeer::doDeleteAll(TsunamiSeismicDataPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a TsunamiSeismicData or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or TsunamiSeismicData object or primary key or array of primary keys
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
			$con = Propel::getConnection(TsunamiSeismicDataPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof TsunamiSeismicData) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(TsunamiSeismicDataPeer::TSUNAMI_SEISMIC_DATA_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given TsunamiSeismicData object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      TsunamiSeismicData $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(TsunamiSeismicData $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(TsunamiSeismicDataPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(TsunamiSeismicDataPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSeismicDataPeer::LOCAL))
			$columns[TsunamiSeismicDataPeer::LOCAL] = $obj->getLocal();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSeismicDataPeer::LOCAL_DATA_SOURCES))
			$columns[TsunamiSeismicDataPeer::LOCAL_DATA_SOURCES] = $obj->getLocalDataSources();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSeismicDataPeer::LOCAL_TYPE))
			$columns[TsunamiSeismicDataPeer::LOCAL_TYPE] = $obj->getLocalType();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSeismicDataPeer::MEASURES))
			$columns[TsunamiSeismicDataPeer::MEASURES] = $obj->getMeasures();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSeismicDataPeer::MEASURES_SITE_CONFIG))
			$columns[TsunamiSeismicDataPeer::MEASURES_SITE_CONFIG] = $obj->getMeasuresSiteConfig();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSeismicDataPeer::MEASURES_TYPES))
			$columns[TsunamiSeismicDataPeer::MEASURES_TYPES] = $obj->getMeasuresTypes();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSeismicDataPeer::MIA))
			$columns[TsunamiSeismicDataPeer::MIA] = $obj->getMia();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSeismicDataPeer::MIA_SOURCE))
			$columns[TsunamiSeismicDataPeer::MIA_SOURCE] = $obj->getMiaSource();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID))
			$columns[TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID] = $obj->getTsunamiDocLibId();

		}

		return BasePeer::doValidate(TsunamiSeismicDataPeer::DATABASE_NAME, TsunamiSeismicDataPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     TsunamiSeismicData
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(TsunamiSeismicDataPeer::DATABASE_NAME);

		$criteria->add(TsunamiSeismicDataPeer::TSUNAMI_SEISMIC_DATA_ID, $pk);


		$v = TsunamiSeismicDataPeer::doSelect($criteria, $con);

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
			$criteria->add(TsunamiSeismicDataPeer::TSUNAMI_SEISMIC_DATA_ID, $pks, Criteria::IN);
			$objs = TsunamiSeismicDataPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseTsunamiSeismicDataPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseTsunamiSeismicDataPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/tsunami/map/TsunamiSeismicDataMapBuilder.php';
	Propel::registerMapBuilder('lib.data.tsunami.map.TsunamiSeismicDataMapBuilder');
}
