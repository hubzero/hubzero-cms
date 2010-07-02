<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by NAWIPeer::getOMClass()
include_once 'lib/data/NAWI.php';

/**
 * Base static class for performing query and update operations on the 'NAWI' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseNAWIPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'NAWI';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.NAWI';

	/** The total number of columns. */
	const NUM_COLUMNS = 12;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the NAWIID field */
	const NAWIID = 'NAWI.NAWIID';

	/** the column name for the ACTIVE field */
	const ACTIVE = 'NAWI.ACTIVE';

	/** the column name for the CONTACT_EMAIL field */
	const CONTACT_EMAIL = 'NAWI.CONTACT_EMAIL';

	/** the column name for the CONTACT_NAME field */
	const CONTACT_NAME = 'NAWI.CONTACT_NAME';

	/** the column name for the EXP_DESCRIPT field */
	const EXP_DESCRIPT = 'NAWI.EXP_DESCRIPT';

	/** the column name for the EXP_NAME field */
	const EXP_NAME = 'NAWI.EXP_NAME';

	/** the column name for the EXP_PHASE field */
	const EXP_PHASE = 'NAWI.EXP_PHASE';

	/** the column name for the MOVIE_URL field */
	const MOVIE_URL = 'NAWI.MOVIE_URL';

	/** the column name for the TEST_DT field */
	const TEST_DT = 'NAWI.TEST_DT';

	/** the column name for the TEST_END field */
	const TEST_END = 'NAWI.TEST_END';

	/** the column name for the TEST_START field */
	const TEST_START = 'NAWI.TEST_START';

	/** the column name for the TEST_TZ field */
	const TEST_TZ = 'NAWI.TEST_TZ';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Active', 'ContactEmail', 'ContactName', 'ExperimentDescription', 'ExperimentName', 'ExperimentPhase', 'MovieUrl', 'TestDate', 'TestEndDate', 'TestStartDate', 'TestTimeZone', ),
		BasePeer::TYPE_COLNAME => array (NAWIPeer::NAWIID, NAWIPeer::ACTIVE, NAWIPeer::CONTACT_EMAIL, NAWIPeer::CONTACT_NAME, NAWIPeer::EXP_DESCRIPT, NAWIPeer::EXP_NAME, NAWIPeer::EXP_PHASE, NAWIPeer::MOVIE_URL, NAWIPeer::TEST_DT, NAWIPeer::TEST_END, NAWIPeer::TEST_START, NAWIPeer::TEST_TZ, ),
		BasePeer::TYPE_FIELDNAME => array ('NAWIID', 'ACTIVE', 'CONTACT_EMAIL', 'CONTACT_NAME', 'EXP_DESCRIPT', 'EXP_NAME', 'EXP_PHASE', 'MOVIE_URL', 'TEST_DT', 'TEST_END', 'TEST_START', 'TEST_TZ', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Active' => 1, 'ContactEmail' => 2, 'ContactName' => 3, 'ExperimentDescription' => 4, 'ExperimentName' => 5, 'ExperimentPhase' => 6, 'MovieUrl' => 7, 'TestDate' => 8, 'TestEndDate' => 9, 'TestStartDate' => 10, 'TestTimeZone' => 11, ),
		BasePeer::TYPE_COLNAME => array (NAWIPeer::NAWIID => 0, NAWIPeer::ACTIVE => 1, NAWIPeer::CONTACT_EMAIL => 2, NAWIPeer::CONTACT_NAME => 3, NAWIPeer::EXP_DESCRIPT => 4, NAWIPeer::EXP_NAME => 5, NAWIPeer::EXP_PHASE => 6, NAWIPeer::MOVIE_URL => 7, NAWIPeer::TEST_DT => 8, NAWIPeer::TEST_END => 9, NAWIPeer::TEST_START => 10, NAWIPeer::TEST_TZ => 11, ),
		BasePeer::TYPE_FIELDNAME => array ('NAWIID' => 0, 'ACTIVE' => 1, 'CONTACT_EMAIL' => 2, 'CONTACT_NAME' => 3, 'EXP_DESCRIPT' => 4, 'EXP_NAME' => 5, 'EXP_PHASE' => 6, 'MOVIE_URL' => 7, 'TEST_DT' => 8, 'TEST_END' => 9, 'TEST_START' => 10, 'TEST_TZ' => 11, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/NAWIMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.NAWIMapBuilder');
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
			$map = NAWIPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. NAWIPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(NAWIPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(NAWIPeer::NAWIID);

		$criteria->addSelectColumn(NAWIPeer::ACTIVE);

		$criteria->addSelectColumn(NAWIPeer::CONTACT_EMAIL);

		$criteria->addSelectColumn(NAWIPeer::CONTACT_NAME);

		$criteria->addSelectColumn(NAWIPeer::EXP_DESCRIPT);

		$criteria->addSelectColumn(NAWIPeer::EXP_NAME);

		$criteria->addSelectColumn(NAWIPeer::EXP_PHASE);

		$criteria->addSelectColumn(NAWIPeer::MOVIE_URL);

		$criteria->addSelectColumn(NAWIPeer::TEST_DT);

		$criteria->addSelectColumn(NAWIPeer::TEST_END);

		$criteria->addSelectColumn(NAWIPeer::TEST_START);

		$criteria->addSelectColumn(NAWIPeer::TEST_TZ);

	}

	const COUNT = 'COUNT(NAWI.NAWIID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT NAWI.NAWIID)';

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
			$criteria->addSelectColumn(NAWIPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(NAWIPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = NAWIPeer::doSelectRS($criteria, $con);
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
	 * @return     NAWI
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = NAWIPeer::doSelect($critcopy, $con);
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
		return NAWIPeer::populateObjects(NAWIPeer::doSelectRS($criteria, $con));
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
			NAWIPeer::addSelectColumns($criteria);
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
		$cls = NAWIPeer::getOMClass();
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
		return NAWIPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a NAWI or Criteria object.
	 *
	 * @param      mixed $values Criteria or NAWI object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from NAWI object
		}

		$criteria->remove(NAWIPeer::NAWIID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a NAWI or Criteria object.
	 *
	 * @param      mixed $values Criteria or NAWI object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(NAWIPeer::NAWIID);
			$selectCriteria->add(NAWIPeer::NAWIID, $criteria->remove(NAWIPeer::NAWIID), $comparison);

		} else { // $values is NAWI object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the NAWI table.
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
			$affectedRows += BasePeer::doDeleteAll(NAWIPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a NAWI or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or NAWI object or primary key or array of primary keys
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
			$con = Propel::getConnection(NAWIPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof NAWI) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(NAWIPeer::NAWIID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given NAWI object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      NAWI $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(NAWI $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(NAWIPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(NAWIPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(NAWIPeer::ACTIVE))
			$columns[NAWIPeer::ACTIVE] = $obj->getActive();

		if ($obj->isNew() || $obj->isColumnModified(NAWIPeer::CONTACT_EMAIL))
			$columns[NAWIPeer::CONTACT_EMAIL] = $obj->getContactEmail();

		if ($obj->isNew() || $obj->isColumnModified(NAWIPeer::CONTACT_NAME))
			$columns[NAWIPeer::CONTACT_NAME] = $obj->getContactName();

		if ($obj->isNew() || $obj->isColumnModified(NAWIPeer::EXP_DESCRIPT))
			$columns[NAWIPeer::EXP_DESCRIPT] = $obj->getExperimentDescription();

		if ($obj->isNew() || $obj->isColumnModified(NAWIPeer::EXP_NAME))
			$columns[NAWIPeer::EXP_NAME] = $obj->getExperimentName();

		if ($obj->isNew() || $obj->isColumnModified(NAWIPeer::EXP_PHASE))
			$columns[NAWIPeer::EXP_PHASE] = $obj->getExperimentPhase();

		if ($obj->isNew() || $obj->isColumnModified(NAWIPeer::MOVIE_URL))
			$columns[NAWIPeer::MOVIE_URL] = $obj->getMovieUrl();

		if ($obj->isNew() || $obj->isColumnModified(NAWIPeer::TEST_DT))
			$columns[NAWIPeer::TEST_DT] = $obj->getTestDate();

		if ($obj->isNew() || $obj->isColumnModified(NAWIPeer::TEST_END))
			$columns[NAWIPeer::TEST_END] = $obj->getTestEndDate();

		if ($obj->isNew() || $obj->isColumnModified(NAWIPeer::TEST_START))
			$columns[NAWIPeer::TEST_START] = $obj->getTestStartDate();

		if ($obj->isNew() || $obj->isColumnModified(NAWIPeer::TEST_TZ))
			$columns[NAWIPeer::TEST_TZ] = $obj->getTestTimeZone();

		}

		return BasePeer::doValidate(NAWIPeer::DATABASE_NAME, NAWIPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     NAWI
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(NAWIPeer::DATABASE_NAME);

		$criteria->add(NAWIPeer::NAWIID, $pk);


		$v = NAWIPeer::doSelect($criteria, $con);

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
			$criteria->add(NAWIPeer::NAWIID, $pks, Criteria::IN);
			$objs = NAWIPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseNAWIPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseNAWIPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/NAWIMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.NAWIMapBuilder');
}
