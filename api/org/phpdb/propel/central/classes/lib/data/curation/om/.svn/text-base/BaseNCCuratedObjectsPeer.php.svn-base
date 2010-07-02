<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by NCCuratedObjectsPeer::getOMClass()
include_once 'lib/data/curation/NCCuratedObjects.php';

/**
 * Base static class for performing query and update operations on the 'CURATED_OBJECTS' table.
 *
 * 
 *
 * @package    lib.data.curation.om
 */
abstract class BaseNCCuratedObjectsPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'CURATED_OBJECTS';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.curation.NCCuratedObjects';

	/** The total number of columns. */
	const NUM_COLUMNS = 18;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the OBJECT_ID field */
	const OBJECT_ID = 'CURATED_OBJECTS.OBJECT_ID';

	/** the column name for the CONFORMANCE_LEVEL field */
	const CONFORMANCE_LEVEL = 'CURATED_OBJECTS.CONFORMANCE_LEVEL';

	/** the column name for the CREATED_BY field */
	const CREATED_BY = 'CURATED_OBJECTS.CREATED_BY';

	/** the column name for the CREATED_DATE field */
	const CREATED_DATE = 'CURATED_OBJECTS.CREATED_DATE';

	/** the column name for the CURATION_STATE field */
	const CURATION_STATE = 'CURATED_OBJECTS.CURATION_STATE';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'CURATED_OBJECTS.DESCRIPTION';

	/** the column name for the INITIAL_CURATION_DATE field */
	const INITIAL_CURATION_DATE = 'CURATED_OBJECTS.INITIAL_CURATION_DATE';

	/** the column name for the LINK field */
	const LINK = 'CURATED_OBJECTS.LINK';

	/** the column name for the MODIFIED_BY field */
	const MODIFIED_BY = 'CURATED_OBJECTS.MODIFIED_BY';

	/** the column name for the MODIFIED_DATE field */
	const MODIFIED_DATE = 'CURATED_OBJECTS.MODIFIED_DATE';

	/** the column name for the NAME field */
	const NAME = 'CURATED_OBJECTS.NAME';

	/** the column name for the OBJECT_CREATION_DATE field */
	const OBJECT_CREATION_DATE = 'CURATED_OBJECTS.OBJECT_CREATION_DATE';

	/** the column name for the OBJECT_STATUS field */
	const OBJECT_STATUS = 'CURATED_OBJECTS.OBJECT_STATUS';

	/** the column name for the OBJECT_TYPE field */
	const OBJECT_TYPE = 'CURATED_OBJECTS.OBJECT_TYPE';

	/** the column name for the OBJECT_VISIBILITY field */
	const OBJECT_VISIBILITY = 'CURATED_OBJECTS.OBJECT_VISIBILITY';

	/** the column name for the SHORT_TITLE field */
	const SHORT_TITLE = 'CURATED_OBJECTS.SHORT_TITLE';

	/** the column name for the TITLE field */
	const TITLE = 'CURATED_OBJECTS.TITLE';

	/** the column name for the VERSION field */
	const VERSION = 'CURATED_OBJECTS.VERSION';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('ObjectId', 'ConformanceLevel', 'CreatedBy', 'CreatedDate', 'CurationState', 'Description', 'InitialCurationDate', 'Link', 'ModifiedBy', 'ModifiedDate', 'Name', 'ObjectCreationDate', 'ObjectStatus', 'ObjectType', 'ObjectVisibility', 'ShortTitle', 'Title', 'Version', ),
		BasePeer::TYPE_COLNAME => array (NCCuratedObjectsPeer::OBJECT_ID, NCCuratedObjectsPeer::CONFORMANCE_LEVEL, NCCuratedObjectsPeer::CREATED_BY, NCCuratedObjectsPeer::CREATED_DATE, NCCuratedObjectsPeer::CURATION_STATE, NCCuratedObjectsPeer::DESCRIPTION, NCCuratedObjectsPeer::INITIAL_CURATION_DATE, NCCuratedObjectsPeer::LINK, NCCuratedObjectsPeer::MODIFIED_BY, NCCuratedObjectsPeer::MODIFIED_DATE, NCCuratedObjectsPeer::NAME, NCCuratedObjectsPeer::OBJECT_CREATION_DATE, NCCuratedObjectsPeer::OBJECT_STATUS, NCCuratedObjectsPeer::OBJECT_TYPE, NCCuratedObjectsPeer::OBJECT_VISIBILITY, NCCuratedObjectsPeer::SHORT_TITLE, NCCuratedObjectsPeer::TITLE, NCCuratedObjectsPeer::VERSION, ),
		BasePeer::TYPE_FIELDNAME => array ('OBJECT_ID', 'CONFORMANCE_LEVEL', 'CREATED_BY', 'CREATED_DATE', 'CURATION_STATE', 'DESCRIPTION', 'INITIAL_CURATION_DATE', 'LINK', 'MODIFIED_BY', 'MODIFIED_DATE', 'NAME', 'OBJECT_CREATION_DATE', 'OBJECT_STATUS', 'OBJECT_TYPE', 'OBJECT_VISIBILITY', 'SHORT_TITLE', 'TITLE', 'VERSION', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('ObjectId' => 0, 'ConformanceLevel' => 1, 'CreatedBy' => 2, 'CreatedDate' => 3, 'CurationState' => 4, 'Description' => 5, 'InitialCurationDate' => 6, 'Link' => 7, 'ModifiedBy' => 8, 'ModifiedDate' => 9, 'Name' => 10, 'ObjectCreationDate' => 11, 'ObjectStatus' => 12, 'ObjectType' => 13, 'ObjectVisibility' => 14, 'ShortTitle' => 15, 'Title' => 16, 'Version' => 17, ),
		BasePeer::TYPE_COLNAME => array (NCCuratedObjectsPeer::OBJECT_ID => 0, NCCuratedObjectsPeer::CONFORMANCE_LEVEL => 1, NCCuratedObjectsPeer::CREATED_BY => 2, NCCuratedObjectsPeer::CREATED_DATE => 3, NCCuratedObjectsPeer::CURATION_STATE => 4, NCCuratedObjectsPeer::DESCRIPTION => 5, NCCuratedObjectsPeer::INITIAL_CURATION_DATE => 6, NCCuratedObjectsPeer::LINK => 7, NCCuratedObjectsPeer::MODIFIED_BY => 8, NCCuratedObjectsPeer::MODIFIED_DATE => 9, NCCuratedObjectsPeer::NAME => 10, NCCuratedObjectsPeer::OBJECT_CREATION_DATE => 11, NCCuratedObjectsPeer::OBJECT_STATUS => 12, NCCuratedObjectsPeer::OBJECT_TYPE => 13, NCCuratedObjectsPeer::OBJECT_VISIBILITY => 14, NCCuratedObjectsPeer::SHORT_TITLE => 15, NCCuratedObjectsPeer::TITLE => 16, NCCuratedObjectsPeer::VERSION => 17, ),
		BasePeer::TYPE_FIELDNAME => array ('OBJECT_ID' => 0, 'CONFORMANCE_LEVEL' => 1, 'CREATED_BY' => 2, 'CREATED_DATE' => 3, 'CURATION_STATE' => 4, 'DESCRIPTION' => 5, 'INITIAL_CURATION_DATE' => 6, 'LINK' => 7, 'MODIFIED_BY' => 8, 'MODIFIED_DATE' => 9, 'NAME' => 10, 'OBJECT_CREATION_DATE' => 11, 'OBJECT_STATUS' => 12, 'OBJECT_TYPE' => 13, 'OBJECT_VISIBILITY' => 14, 'SHORT_TITLE' => 15, 'TITLE' => 16, 'VERSION' => 17, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/curation/map/NCCuratedObjectsMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.curation.map.NCCuratedObjectsMapBuilder');
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
			$map = NCCuratedObjectsPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. NCCuratedObjectsPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(NCCuratedObjectsPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(NCCuratedObjectsPeer::OBJECT_ID);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::CONFORMANCE_LEVEL);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::CREATED_BY);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::CREATED_DATE);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::CURATION_STATE);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::DESCRIPTION);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::INITIAL_CURATION_DATE);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::LINK);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::MODIFIED_BY);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::MODIFIED_DATE);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::NAME);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::OBJECT_CREATION_DATE);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::OBJECT_STATUS);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::OBJECT_TYPE);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::OBJECT_VISIBILITY);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::SHORT_TITLE);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::TITLE);

		$criteria->addSelectColumn(NCCuratedObjectsPeer::VERSION);

	}

	const COUNT = 'COUNT(CURATED_OBJECTS.OBJECT_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT CURATED_OBJECTS.OBJECT_ID)';

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
			$criteria->addSelectColumn(NCCuratedObjectsPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(NCCuratedObjectsPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = NCCuratedObjectsPeer::doSelectRS($criteria, $con);
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
	 * @return     NCCuratedObjects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = NCCuratedObjectsPeer::doSelect($critcopy, $con);
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
		return NCCuratedObjectsPeer::populateObjects(NCCuratedObjectsPeer::doSelectRS($criteria, $con));
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
			NCCuratedObjectsPeer::addSelectColumns($criteria);
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
		$cls = NCCuratedObjectsPeer::getOMClass();
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
		return NCCuratedObjectsPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a NCCuratedObjects or Criteria object.
	 *
	 * @param      mixed $values Criteria or NCCuratedObjects object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from NCCuratedObjects object
		}

		$criteria->remove(NCCuratedObjectsPeer::OBJECT_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a NCCuratedObjects or Criteria object.
	 *
	 * @param      mixed $values Criteria or NCCuratedObjects object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(NCCuratedObjectsPeer::OBJECT_ID);
			$selectCriteria->add(NCCuratedObjectsPeer::OBJECT_ID, $criteria->remove(NCCuratedObjectsPeer::OBJECT_ID), $comparison);

		} else { // $values is NCCuratedObjects object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the CURATED_OBJECTS table.
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
			$affectedRows += BasePeer::doDeleteAll(NCCuratedObjectsPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a NCCuratedObjects or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or NCCuratedObjects object or primary key or array of primary keys
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
			$con = Propel::getConnection(NCCuratedObjectsPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof NCCuratedObjects) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(NCCuratedObjectsPeer::OBJECT_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given NCCuratedObjects object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      NCCuratedObjects $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(NCCuratedObjects $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(NCCuratedObjectsPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(NCCuratedObjectsPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::CONFORMANCE_LEVEL))
			$columns[NCCuratedObjectsPeer::CONFORMANCE_LEVEL] = $obj->getConformanceLevel();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::CREATED_BY))
			$columns[NCCuratedObjectsPeer::CREATED_BY] = $obj->getCreatedBy();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::CREATED_DATE))
			$columns[NCCuratedObjectsPeer::CREATED_DATE] = $obj->getCreatedDate();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::CURATION_STATE))
			$columns[NCCuratedObjectsPeer::CURATION_STATE] = $obj->getCurationState();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::DESCRIPTION))
			$columns[NCCuratedObjectsPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::INITIAL_CURATION_DATE))
			$columns[NCCuratedObjectsPeer::INITIAL_CURATION_DATE] = $obj->getInitialCurationDate();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::LINK))
			$columns[NCCuratedObjectsPeer::LINK] = $obj->getLink();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::MODIFIED_BY))
			$columns[NCCuratedObjectsPeer::MODIFIED_BY] = $obj->getModifiedBy();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::MODIFIED_DATE))
			$columns[NCCuratedObjectsPeer::MODIFIED_DATE] = $obj->getModifiedDate();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::NAME))
			$columns[NCCuratedObjectsPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::OBJECT_CREATION_DATE))
			$columns[NCCuratedObjectsPeer::OBJECT_CREATION_DATE] = $obj->getObjectCreationDate();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::OBJECT_STATUS))
			$columns[NCCuratedObjectsPeer::OBJECT_STATUS] = $obj->getObjectStatus();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::OBJECT_TYPE))
			$columns[NCCuratedObjectsPeer::OBJECT_TYPE] = $obj->getObjectType();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::OBJECT_VISIBILITY))
			$columns[NCCuratedObjectsPeer::OBJECT_VISIBILITY] = $obj->getObjectVisibility();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::SHORT_TITLE))
			$columns[NCCuratedObjectsPeer::SHORT_TITLE] = $obj->getShortTitle();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::TITLE))
			$columns[NCCuratedObjectsPeer::TITLE] = $obj->getTitle();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedObjectsPeer::VERSION))
			$columns[NCCuratedObjectsPeer::VERSION] = $obj->getVersion();

		}

		return BasePeer::doValidate(NCCuratedObjectsPeer::DATABASE_NAME, NCCuratedObjectsPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     NCCuratedObjects
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(NCCuratedObjectsPeer::DATABASE_NAME);

		$criteria->add(NCCuratedObjectsPeer::OBJECT_ID, $pk);


		$v = NCCuratedObjectsPeer::doSelect($criteria, $con);

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
			$criteria->add(NCCuratedObjectsPeer::OBJECT_ID, $pks, Criteria::IN);
			$objs = NCCuratedObjectsPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseNCCuratedObjectsPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseNCCuratedObjectsPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/curation/map/NCCuratedObjectsMapBuilder.php';
	Propel::registerMapBuilder('lib.data.curation.map.NCCuratedObjectsMapBuilder');
}
