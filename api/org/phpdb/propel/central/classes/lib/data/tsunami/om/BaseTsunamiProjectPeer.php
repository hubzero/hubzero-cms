<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by TsunamiProjectPeer::getOMClass()
include_once 'lib/data/tsunami/TsunamiProject.php';

/**
 * Base static class for performing query and update operations on the 'TSUNAMI_PROJECT' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiProjectPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'TSUNAMI_PROJECT';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.tsunami.TsunamiProject';

	/** The total number of columns. */
	const NUM_COLUMNS = 18;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the TSUNAMI_PROJECT_ID field */
	const TSUNAMI_PROJECT_ID = 'TSUNAMI_PROJECT.TSUNAMI_PROJECT_ID';

	/** the column name for the CO_PI field */
	const CO_PI = 'TSUNAMI_PROJECT.CO_PI';

	/** the column name for the CO_PI_INSTITUTION field */
	const CO_PI_INSTITUTION = 'TSUNAMI_PROJECT.CO_PI_INSTITUTION';

	/** the column name for the COLLABORATORS field */
	const COLLABORATORS = 'TSUNAMI_PROJECT.COLLABORATORS';

	/** the column name for the CONTACT_EMAIL field */
	const CONTACT_EMAIL = 'TSUNAMI_PROJECT.CONTACT_EMAIL';

	/** the column name for the CONTACT_NAME field */
	const CONTACT_NAME = 'TSUNAMI_PROJECT.CONTACT_NAME';

	/** the column name for the DELETED field */
	const DELETED = 'TSUNAMI_PROJECT.DELETED';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'TSUNAMI_PROJECT.DESCRIPTION';

	/** the column name for the NAME field */
	const NAME = 'TSUNAMI_PROJECT.NAME';

	/** the column name for the NSF_TITLE field */
	const NSF_TITLE = 'TSUNAMI_PROJECT.NSF_TITLE';

	/** the column name for the PI field */
	const PI = 'TSUNAMI_PROJECT.PI';

	/** the column name for the PI_INSTITUTION field */
	const PI_INSTITUTION = 'TSUNAMI_PROJECT.PI_INSTITUTION';

	/** the column name for the PUBLIC_DATA field */
	const PUBLIC_DATA = 'TSUNAMI_PROJECT.PUBLIC_DATA';

	/** the column name for the SHORT_TITLE field */
	const SHORT_TITLE = 'TSUNAMI_PROJECT.SHORT_TITLE';

	/** the column name for the STATUS field */
	const STATUS = 'TSUNAMI_PROJECT.STATUS';

	/** the column name for the SYSADMIN_EMAIL field */
	const SYSADMIN_EMAIL = 'TSUNAMI_PROJECT.SYSADMIN_EMAIL';

	/** the column name for the SYSADMIN_NAME field */
	const SYSADMIN_NAME = 'TSUNAMI_PROJECT.SYSADMIN_NAME';

	/** the column name for the VIEWABLE field */
	const VIEWABLE = 'TSUNAMI_PROJECT.VIEWABLE';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CoPi', 'CoPiInstitution', 'Collaborators', 'ContactEmail', 'ContactName', 'Deleted', 'Description', 'Name', 'NsfTitle', 'Pi', 'PiInstitution', 'PublicData', 'ShortTitle', 'Status', 'SysadminEmail', 'SysadminName', 'View', ),
		BasePeer::TYPE_COLNAME => array (TsunamiProjectPeer::TSUNAMI_PROJECT_ID, TsunamiProjectPeer::CO_PI, TsunamiProjectPeer::CO_PI_INSTITUTION, TsunamiProjectPeer::COLLABORATORS, TsunamiProjectPeer::CONTACT_EMAIL, TsunamiProjectPeer::CONTACT_NAME, TsunamiProjectPeer::DELETED, TsunamiProjectPeer::DESCRIPTION, TsunamiProjectPeer::NAME, TsunamiProjectPeer::NSF_TITLE, TsunamiProjectPeer::PI, TsunamiProjectPeer::PI_INSTITUTION, TsunamiProjectPeer::PUBLIC_DATA, TsunamiProjectPeer::SHORT_TITLE, TsunamiProjectPeer::STATUS, TsunamiProjectPeer::SYSADMIN_EMAIL, TsunamiProjectPeer::SYSADMIN_NAME, TsunamiProjectPeer::VIEWABLE, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_PROJECT_ID', 'CO_PI', 'CO_PI_INSTITUTION', 'COLLABORATORS', 'CONTACT_EMAIL', 'CONTACT_NAME', 'DELETED', 'DESCRIPTION', 'NAME', 'NSF_TITLE', 'PI', 'PI_INSTITUTION', 'PUBLIC_DATA', 'SHORT_TITLE', 'STATUS', 'SYSADMIN_EMAIL', 'SYSADMIN_NAME', 'VIEWABLE', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CoPi' => 1, 'CoPiInstitution' => 2, 'Collaborators' => 3, 'ContactEmail' => 4, 'ContactName' => 5, 'Deleted' => 6, 'Description' => 7, 'Name' => 8, 'NsfTitle' => 9, 'Pi' => 10, 'PiInstitution' => 11, 'PublicData' => 12, 'ShortTitle' => 13, 'Status' => 14, 'SysadminEmail' => 15, 'SysadminName' => 16, 'View' => 17, ),
		BasePeer::TYPE_COLNAME => array (TsunamiProjectPeer::TSUNAMI_PROJECT_ID => 0, TsunamiProjectPeer::CO_PI => 1, TsunamiProjectPeer::CO_PI_INSTITUTION => 2, TsunamiProjectPeer::COLLABORATORS => 3, TsunamiProjectPeer::CONTACT_EMAIL => 4, TsunamiProjectPeer::CONTACT_NAME => 5, TsunamiProjectPeer::DELETED => 6, TsunamiProjectPeer::DESCRIPTION => 7, TsunamiProjectPeer::NAME => 8, TsunamiProjectPeer::NSF_TITLE => 9, TsunamiProjectPeer::PI => 10, TsunamiProjectPeer::PI_INSTITUTION => 11, TsunamiProjectPeer::PUBLIC_DATA => 12, TsunamiProjectPeer::SHORT_TITLE => 13, TsunamiProjectPeer::STATUS => 14, TsunamiProjectPeer::SYSADMIN_EMAIL => 15, TsunamiProjectPeer::SYSADMIN_NAME => 16, TsunamiProjectPeer::VIEWABLE => 17, ),
		BasePeer::TYPE_FIELDNAME => array ('TSUNAMI_PROJECT_ID' => 0, 'CO_PI' => 1, 'CO_PI_INSTITUTION' => 2, 'COLLABORATORS' => 3, 'CONTACT_EMAIL' => 4, 'CONTACT_NAME' => 5, 'DELETED' => 6, 'DESCRIPTION' => 7, 'NAME' => 8, 'NSF_TITLE' => 9, 'PI' => 10, 'PI_INSTITUTION' => 11, 'PUBLIC_DATA' => 12, 'SHORT_TITLE' => 13, 'STATUS' => 14, 'SYSADMIN_EMAIL' => 15, 'SYSADMIN_NAME' => 16, 'VIEWABLE' => 17, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/tsunami/map/TsunamiProjectMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.tsunami.map.TsunamiProjectMapBuilder');
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
			$map = TsunamiProjectPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. TsunamiProjectPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(TsunamiProjectPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(TsunamiProjectPeer::TSUNAMI_PROJECT_ID);

		$criteria->addSelectColumn(TsunamiProjectPeer::CO_PI);

		$criteria->addSelectColumn(TsunamiProjectPeer::CO_PI_INSTITUTION);

		$criteria->addSelectColumn(TsunamiProjectPeer::COLLABORATORS);

		$criteria->addSelectColumn(TsunamiProjectPeer::CONTACT_EMAIL);

		$criteria->addSelectColumn(TsunamiProjectPeer::CONTACT_NAME);

		$criteria->addSelectColumn(TsunamiProjectPeer::DELETED);

		$criteria->addSelectColumn(TsunamiProjectPeer::DESCRIPTION);

		$criteria->addSelectColumn(TsunamiProjectPeer::NAME);

		$criteria->addSelectColumn(TsunamiProjectPeer::NSF_TITLE);

		$criteria->addSelectColumn(TsunamiProjectPeer::PI);

		$criteria->addSelectColumn(TsunamiProjectPeer::PI_INSTITUTION);

		$criteria->addSelectColumn(TsunamiProjectPeer::PUBLIC_DATA);

		$criteria->addSelectColumn(TsunamiProjectPeer::SHORT_TITLE);

		$criteria->addSelectColumn(TsunamiProjectPeer::STATUS);

		$criteria->addSelectColumn(TsunamiProjectPeer::SYSADMIN_EMAIL);

		$criteria->addSelectColumn(TsunamiProjectPeer::SYSADMIN_NAME);

		$criteria->addSelectColumn(TsunamiProjectPeer::VIEWABLE);

	}

	const COUNT = 'COUNT(TSUNAMI_PROJECT.TSUNAMI_PROJECT_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT TSUNAMI_PROJECT.TSUNAMI_PROJECT_ID)';

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
			$criteria->addSelectColumn(TsunamiProjectPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(TsunamiProjectPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = TsunamiProjectPeer::doSelectRS($criteria, $con);
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
	 * @return     TsunamiProject
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = TsunamiProjectPeer::doSelect($critcopy, $con);
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
		return TsunamiProjectPeer::populateObjects(TsunamiProjectPeer::doSelectRS($criteria, $con));
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
			TsunamiProjectPeer::addSelectColumns($criteria);
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
		$cls = TsunamiProjectPeer::getOMClass();
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
		return TsunamiProjectPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a TsunamiProject or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiProject object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from TsunamiProject object
		}

		$criteria->remove(TsunamiProjectPeer::TSUNAMI_PROJECT_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a TsunamiProject or Criteria object.
	 *
	 * @param      mixed $values Criteria or TsunamiProject object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(TsunamiProjectPeer::TSUNAMI_PROJECT_ID);
			$selectCriteria->add(TsunamiProjectPeer::TSUNAMI_PROJECT_ID, $criteria->remove(TsunamiProjectPeer::TSUNAMI_PROJECT_ID), $comparison);

		} else { // $values is TsunamiProject object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the TSUNAMI_PROJECT table.
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
			$affectedRows += BasePeer::doDeleteAll(TsunamiProjectPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a TsunamiProject or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or TsunamiProject object or primary key or array of primary keys
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
			$con = Propel::getConnection(TsunamiProjectPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof TsunamiProject) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(TsunamiProjectPeer::TSUNAMI_PROJECT_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given TsunamiProject object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      TsunamiProject $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(TsunamiProject $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(TsunamiProjectPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(TsunamiProjectPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::COLLABORATORS))
			$columns[TsunamiProjectPeer::COLLABORATORS] = $obj->getCollaborators();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::CONTACT_EMAIL))
			$columns[TsunamiProjectPeer::CONTACT_EMAIL] = $obj->getContactEmail();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::CONTACT_NAME))
			$columns[TsunamiProjectPeer::CONTACT_NAME] = $obj->getContactName();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::CO_PI))
			$columns[TsunamiProjectPeer::CO_PI] = $obj->getCoPi();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::CO_PI_INSTITUTION))
			$columns[TsunamiProjectPeer::CO_PI_INSTITUTION] = $obj->getCoPiInstitution();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::DELETED))
			$columns[TsunamiProjectPeer::DELETED] = $obj->getDeleted();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::DESCRIPTION))
			$columns[TsunamiProjectPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::NAME))
			$columns[TsunamiProjectPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::NSF_TITLE))
			$columns[TsunamiProjectPeer::NSF_TITLE] = $obj->getNsfTitle();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::PI))
			$columns[TsunamiProjectPeer::PI] = $obj->getPi();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::PI_INSTITUTION))
			$columns[TsunamiProjectPeer::PI_INSTITUTION] = $obj->getPiInstitution();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::PUBLIC_DATA))
			$columns[TsunamiProjectPeer::PUBLIC_DATA] = $obj->getPublicData();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::SHORT_TITLE))
			$columns[TsunamiProjectPeer::SHORT_TITLE] = $obj->getShortTitle();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::STATUS))
			$columns[TsunamiProjectPeer::STATUS] = $obj->getStatus();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::SYSADMIN_EMAIL))
			$columns[TsunamiProjectPeer::SYSADMIN_EMAIL] = $obj->getSysadminEmail();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::SYSADMIN_NAME))
			$columns[TsunamiProjectPeer::SYSADMIN_NAME] = $obj->getSysadminName();

		if ($obj->isNew() || $obj->isColumnModified(TsunamiProjectPeer::VIEWABLE))
			$columns[TsunamiProjectPeer::VIEWABLE] = $obj->getView();

		}

		return BasePeer::doValidate(TsunamiProjectPeer::DATABASE_NAME, TsunamiProjectPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     TsunamiProject
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(TsunamiProjectPeer::DATABASE_NAME);

		$criteria->add(TsunamiProjectPeer::TSUNAMI_PROJECT_ID, $pk);


		$v = TsunamiProjectPeer::doSelect($criteria, $con);

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
			$criteria->add(TsunamiProjectPeer::TSUNAMI_PROJECT_ID, $pks, Criteria::IN);
			$objs = TsunamiProjectPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseTsunamiProjectPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseTsunamiProjectPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/tsunami/map/TsunamiProjectMapBuilder.php';
	Propel::registerMapBuilder('lib.data.tsunami.map.TsunamiProjectMapBuilder');
}
