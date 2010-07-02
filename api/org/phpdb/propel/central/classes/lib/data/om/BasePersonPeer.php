<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by PersonPeer::getOMClass()
include_once 'lib/data/Person.php';

/**
 * Base static class for performing query and update operations on the 'PERSON' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BasePersonPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'PERSON';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.Person';

	/** The total number of columns. */
	const NUM_COLUMNS = 11;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'PERSON.ID';

	/** the column name for the ADDRESS field */
	const ADDRESS = 'PERSON.ADDRESS';

	/** the column name for the ADMIN_STATUS field */
	const ADMIN_STATUS = 'PERSON.ADMIN_STATUS';

	/** the column name for the CATEGORY field */
	const CATEGORY = 'PERSON.CATEGORY';

	/** the column name for the COMMENTS field */
	const COMMENTS = 'PERSON.COMMENTS';

	/** the column name for the E_MAIL field */
	const E_MAIL = 'PERSON.E_MAIL';

	/** the column name for the FAX field */
	const FAX = 'PERSON.FAX';

	/** the column name for the FIRST_NAME field */
	const FIRST_NAME = 'PERSON.FIRST_NAME';

	/** the column name for the LAST_NAME field */
	const LAST_NAME = 'PERSON.LAST_NAME';

	/** the column name for the PHONE field */
	const PHONE = 'PERSON.PHONE';

	/** the column name for the USER_NAME field */
	const USER_NAME = 'PERSON.USER_NAME';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Address', 'AdminStatus', 'Category', 'Comment', 'EMail', 'Fax', 'FirstName', 'LastName', 'Phone', 'UserName', ),
		BasePeer::TYPE_COLNAME => array (PersonPeer::ID, PersonPeer::ADDRESS, PersonPeer::ADMIN_STATUS, PersonPeer::CATEGORY, PersonPeer::COMMENTS, PersonPeer::E_MAIL, PersonPeer::FAX, PersonPeer::FIRST_NAME, PersonPeer::LAST_NAME, PersonPeer::PHONE, PersonPeer::USER_NAME, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'ADDRESS', 'ADMIN_STATUS', 'CATEGORY', 'COMMENTS', 'E_MAIL', 'FAX', 'FIRST_NAME', 'LAST_NAME', 'PHONE', 'USER_NAME', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Address' => 1, 'AdminStatus' => 2, 'Category' => 3, 'Comment' => 4, 'EMail' => 5, 'Fax' => 6, 'FirstName' => 7, 'LastName' => 8, 'Phone' => 9, 'UserName' => 10, ),
		BasePeer::TYPE_COLNAME => array (PersonPeer::ID => 0, PersonPeer::ADDRESS => 1, PersonPeer::ADMIN_STATUS => 2, PersonPeer::CATEGORY => 3, PersonPeer::COMMENTS => 4, PersonPeer::E_MAIL => 5, PersonPeer::FAX => 6, PersonPeer::FIRST_NAME => 7, PersonPeer::LAST_NAME => 8, PersonPeer::PHONE => 9, PersonPeer::USER_NAME => 10, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'ADDRESS' => 1, 'ADMIN_STATUS' => 2, 'CATEGORY' => 3, 'COMMENTS' => 4, 'E_MAIL' => 5, 'FAX' => 6, 'FIRST_NAME' => 7, 'LAST_NAME' => 8, 'PHONE' => 9, 'USER_NAME' => 10, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/PersonMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.PersonMapBuilder');
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
			$map = PersonPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. PersonPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(PersonPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(PersonPeer::ID);

		$criteria->addSelectColumn(PersonPeer::ADDRESS);

		$criteria->addSelectColumn(PersonPeer::ADMIN_STATUS);

		$criteria->addSelectColumn(PersonPeer::CATEGORY);

		$criteria->addSelectColumn(PersonPeer::COMMENTS);

		$criteria->addSelectColumn(PersonPeer::E_MAIL);

		$criteria->addSelectColumn(PersonPeer::FAX);

		$criteria->addSelectColumn(PersonPeer::FIRST_NAME);

		$criteria->addSelectColumn(PersonPeer::LAST_NAME);

		$criteria->addSelectColumn(PersonPeer::PHONE);

		$criteria->addSelectColumn(PersonPeer::USER_NAME);

	}

	const COUNT = 'COUNT(PERSON.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT PERSON.ID)';

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
			$criteria->addSelectColumn(PersonPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(PersonPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = PersonPeer::doSelectRS($criteria, $con);
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
	 * @return     Person
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = PersonPeer::doSelect($critcopy, $con);
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
		return PersonPeer::populateObjects(PersonPeer::doSelectRS($criteria, $con));
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
			PersonPeer::addSelectColumns($criteria);
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
		$cls = PersonPeer::getOMClass();
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
		return PersonPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a Person or Criteria object.
	 *
	 * @param      mixed $values Criteria or Person object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from Person object
		}

		$criteria->remove(PersonPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a Person or Criteria object.
	 *
	 * @param      mixed $values Criteria or Person object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(PersonPeer::ID);
			$selectCriteria->add(PersonPeer::ID, $criteria->remove(PersonPeer::ID), $comparison);

		} else { // $values is Person object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the PERSON table.
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
			$affectedRows += BasePeer::doDeleteAll(PersonPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a Person or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Person object or primary key or array of primary keys
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
			$con = Propel::getConnection(PersonPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof Person) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(PersonPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given Person object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Person $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(Person $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(PersonPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(PersonPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(PersonPeer::ADDRESS))
			$columns[PersonPeer::ADDRESS] = $obj->getAddress();

		if ($obj->isNew() || $obj->isColumnModified(PersonPeer::ADMIN_STATUS))
			$columns[PersonPeer::ADMIN_STATUS] = $obj->getAdminStatus();

		if ($obj->isNew() || $obj->isColumnModified(PersonPeer::CATEGORY))
			$columns[PersonPeer::CATEGORY] = $obj->getCategory();

		if ($obj->isNew() || $obj->isColumnModified(PersonPeer::COMMENTS))
			$columns[PersonPeer::COMMENTS] = $obj->getComment();

		if ($obj->isNew() || $obj->isColumnModified(PersonPeer::E_MAIL))
			$columns[PersonPeer::E_MAIL] = $obj->getEMail();

		if ($obj->isNew() || $obj->isColumnModified(PersonPeer::FAX))
			$columns[PersonPeer::FAX] = $obj->getFax();

		if ($obj->isNew() || $obj->isColumnModified(PersonPeer::FIRST_NAME))
			$columns[PersonPeer::FIRST_NAME] = $obj->getFirstName();

		if ($obj->isNew() || $obj->isColumnModified(PersonPeer::LAST_NAME))
			$columns[PersonPeer::LAST_NAME] = $obj->getLastName();

		if ($obj->isNew() || $obj->isColumnModified(PersonPeer::PHONE))
			$columns[PersonPeer::PHONE] = $obj->getPhone();

		if ($obj->isNew() || $obj->isColumnModified(PersonPeer::USER_NAME))
			$columns[PersonPeer::USER_NAME] = $obj->getUserName();

		}

		return BasePeer::doValidate(PersonPeer::DATABASE_NAME, PersonPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     Person
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(PersonPeer::DATABASE_NAME);

		$criteria->add(PersonPeer::ID, $pk);


		$v = PersonPeer::doSelect($criteria, $con);

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
			$criteria->add(PersonPeer::ID, $pks, Criteria::IN);
			$objs = PersonPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BasePersonPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BasePersonPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/PersonMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.PersonMapBuilder');
}
