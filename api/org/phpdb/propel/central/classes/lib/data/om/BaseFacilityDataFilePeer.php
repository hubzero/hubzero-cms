<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by FacilityDataFilePeer::getOMClass()
include_once 'lib/data/FacilityDataFile.php';

/**
 * Base static class for performing query and update operations on the 'FACILITY_DATA_FILE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseFacilityDataFilePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'FACILITY_DATA_FILE';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.FacilityDataFile';

	/** The total number of columns. */
	const NUM_COLUMNS = 8;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'FACILITY_DATA_FILE.ID';

	/** the column name for the DATA_FILE_ID field */
	const DATA_FILE_ID = 'FACILITY_DATA_FILE.DATA_FILE_ID';

	/** the column name for the DOC_FORMAT_ID field */
	const DOC_FORMAT_ID = 'FACILITY_DATA_FILE.DOC_FORMAT_ID';

	/** the column name for the DOC_TYPE_ID field */
	const DOC_TYPE_ID = 'FACILITY_DATA_FILE.DOC_TYPE_ID';

	/** the column name for the FACILITY_ID field */
	const FACILITY_ID = 'FACILITY_DATA_FILE.FACILITY_ID';

	/** the column name for the GROUPBY field */
	const GROUPBY = 'FACILITY_DATA_FILE.GROUPBY';

	/** the column name for the INFO_TYPE field */
	const INFO_TYPE = 'FACILITY_DATA_FILE.INFO_TYPE';

	/** the column name for the SUB_INFO_TYPE field */
	const SUB_INFO_TYPE = 'FACILITY_DATA_FILE.SUB_INFO_TYPE';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'DataFileId', 'DocFormatId', 'DocTypeId', 'FacilityId', 'GroupBy', 'InfoType', 'SubInfoType', ),
		BasePeer::TYPE_COLNAME => array (FacilityDataFilePeer::ID, FacilityDataFilePeer::DATA_FILE_ID, FacilityDataFilePeer::DOC_FORMAT_ID, FacilityDataFilePeer::DOC_TYPE_ID, FacilityDataFilePeer::FACILITY_ID, FacilityDataFilePeer::GROUPBY, FacilityDataFilePeer::INFO_TYPE, FacilityDataFilePeer::SUB_INFO_TYPE, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'DATA_FILE_ID', 'DOC_FORMAT_ID', 'DOC_TYPE_ID', 'FACILITY_ID', 'GROUPBY', 'INFO_TYPE', 'SUB_INFO_TYPE', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'DataFileId' => 1, 'DocFormatId' => 2, 'DocTypeId' => 3, 'FacilityId' => 4, 'GroupBy' => 5, 'InfoType' => 6, 'SubInfoType' => 7, ),
		BasePeer::TYPE_COLNAME => array (FacilityDataFilePeer::ID => 0, FacilityDataFilePeer::DATA_FILE_ID => 1, FacilityDataFilePeer::DOC_FORMAT_ID => 2, FacilityDataFilePeer::DOC_TYPE_ID => 3, FacilityDataFilePeer::FACILITY_ID => 4, FacilityDataFilePeer::GROUPBY => 5, FacilityDataFilePeer::INFO_TYPE => 6, FacilityDataFilePeer::SUB_INFO_TYPE => 7, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'DATA_FILE_ID' => 1, 'DOC_FORMAT_ID' => 2, 'DOC_TYPE_ID' => 3, 'FACILITY_ID' => 4, 'GROUPBY' => 5, 'INFO_TYPE' => 6, 'SUB_INFO_TYPE' => 7, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/FacilityDataFileMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.FacilityDataFileMapBuilder');
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
			$map = FacilityDataFilePeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. FacilityDataFilePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(FacilityDataFilePeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(FacilityDataFilePeer::ID);

		$criteria->addSelectColumn(FacilityDataFilePeer::DATA_FILE_ID);

		$criteria->addSelectColumn(FacilityDataFilePeer::DOC_FORMAT_ID);

		$criteria->addSelectColumn(FacilityDataFilePeer::DOC_TYPE_ID);

		$criteria->addSelectColumn(FacilityDataFilePeer::FACILITY_ID);

		$criteria->addSelectColumn(FacilityDataFilePeer::GROUPBY);

		$criteria->addSelectColumn(FacilityDataFilePeer::INFO_TYPE);

		$criteria->addSelectColumn(FacilityDataFilePeer::SUB_INFO_TYPE);

	}

	const COUNT = 'COUNT(FACILITY_DATA_FILE.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT FACILITY_DATA_FILE.ID)';

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
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = FacilityDataFilePeer::doSelectRS($criteria, $con);
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
	 * @return     FacilityDataFile
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = FacilityDataFilePeer::doSelect($critcopy, $con);
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
		return FacilityDataFilePeer::populateObjects(FacilityDataFilePeer::doSelectRS($criteria, $con));
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
			FacilityDataFilePeer::addSelectColumns($criteria);
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
		$cls = FacilityDataFilePeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related DataFile table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinDataFile(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(FacilityDataFilePeer::DATA_FILE_ID, DataFilePeer::ID);

		$rs = FacilityDataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DocumentFormat table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinDocumentFormat(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(FacilityDataFilePeer::DOC_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$rs = FacilityDataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DocumentType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinDocumentType(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(FacilityDataFilePeer::DOC_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$rs = FacilityDataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Organization table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinOrganization(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(FacilityDataFilePeer::FACILITY_ID, OrganizationPeer::ORGID);

		$rs = FacilityDataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of FacilityDataFile objects pre-filled with their DataFile objects.
	 *
	 * @return     array Array of FacilityDataFile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinDataFile(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		FacilityDataFilePeer::addSelectColumns($c);
		$startcol = (FacilityDataFilePeer::NUM_COLUMNS - FacilityDataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DataFilePeer::addSelectColumns($c);

		$c->addJoin(FacilityDataFilePeer::DATA_FILE_ID, DataFilePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = FacilityDataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getDataFile(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addFacilityDataFile($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initFacilityDataFiles();
				$obj2->addFacilityDataFile($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of FacilityDataFile objects pre-filled with their DocumentFormat objects.
	 *
	 * @return     array Array of FacilityDataFile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinDocumentFormat(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		FacilityDataFilePeer::addSelectColumns($c);
		$startcol = (FacilityDataFilePeer::NUM_COLUMNS - FacilityDataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DocumentFormatPeer::addSelectColumns($c);

		$c->addJoin(FacilityDataFilePeer::DOC_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = FacilityDataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DocumentFormatPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getDocumentFormat(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addFacilityDataFile($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initFacilityDataFiles();
				$obj2->addFacilityDataFile($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of FacilityDataFile objects pre-filled with their DocumentType objects.
	 *
	 * @return     array Array of FacilityDataFile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinDocumentType(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		FacilityDataFilePeer::addSelectColumns($c);
		$startcol = (FacilityDataFilePeer::NUM_COLUMNS - FacilityDataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DocumentTypePeer::addSelectColumns($c);

		$c->addJoin(FacilityDataFilePeer::DOC_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = FacilityDataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DocumentTypePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getDocumentType(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addFacilityDataFile($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initFacilityDataFiles();
				$obj2->addFacilityDataFile($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of FacilityDataFile objects pre-filled with their Organization objects.
	 *
	 * @return     array Array of FacilityDataFile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinOrganization(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		FacilityDataFilePeer::addSelectColumns($c);
		$startcol = (FacilityDataFilePeer::NUM_COLUMNS - FacilityDataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		OrganizationPeer::addSelectColumns($c);

		$c->addJoin(FacilityDataFilePeer::FACILITY_ID, OrganizationPeer::ORGID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = FacilityDataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = OrganizationPeer::getOMClass($rs, $startcol);

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getOrganization(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addFacilityDataFile($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initFacilityDataFiles();
				$obj2->addFacilityDataFile($obj1); //CHECKME
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
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(FacilityDataFilePeer::DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(FacilityDataFilePeer::DOC_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$criteria->addJoin(FacilityDataFilePeer::DOC_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$criteria->addJoin(FacilityDataFilePeer::FACILITY_ID, OrganizationPeer::ORGID);

		$rs = FacilityDataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of FacilityDataFile objects pre-filled with all related objects.
	 *
	 * @return     array Array of FacilityDataFile objects.
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

		FacilityDataFilePeer::addSelectColumns($c);
		$startcol2 = (FacilityDataFilePeer::NUM_COLUMNS - FacilityDataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DocumentFormatPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DocumentFormatPeer::NUM_COLUMNS;

		DocumentTypePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + DocumentTypePeer::NUM_COLUMNS;

		OrganizationPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + OrganizationPeer::NUM_COLUMNS;

		$c->addJoin(FacilityDataFilePeer::DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(FacilityDataFilePeer::DOC_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$c->addJoin(FacilityDataFilePeer::DOC_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$c->addJoin(FacilityDataFilePeer::FACILITY_ID, OrganizationPeer::ORGID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = FacilityDataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined DataFile rows
	
			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getDataFile(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addFacilityDataFile($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initFacilityDataFiles();
				$obj2->addFacilityDataFile($obj1);
			}


				// Add objects for joined DocumentFormat rows
	
			$omClass = DocumentFormatPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDocumentFormat(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addFacilityDataFile($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initFacilityDataFiles();
				$obj3->addFacilityDataFile($obj1);
			}


				// Add objects for joined DocumentType rows
	
			$omClass = DocumentTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getDocumentType(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addFacilityDataFile($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initFacilityDataFiles();
				$obj4->addFacilityDataFile($obj1);
			}


				// Add objects for joined Organization rows
	
			$omClass = OrganizationPeer::getOMClass($rs, $startcol5);


			$cls = Propel::import($omClass);
			$obj5 = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getOrganization(); // CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addFacilityDataFile($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj5->initFacilityDataFiles();
				$obj5->addFacilityDataFile($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFile table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDataFile(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(FacilityDataFilePeer::DOC_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$criteria->addJoin(FacilityDataFilePeer::DOC_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$criteria->addJoin(FacilityDataFilePeer::FACILITY_ID, OrganizationPeer::ORGID);

		$rs = FacilityDataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DocumentFormat table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDocumentFormat(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(FacilityDataFilePeer::DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(FacilityDataFilePeer::DOC_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$criteria->addJoin(FacilityDataFilePeer::FACILITY_ID, OrganizationPeer::ORGID);

		$rs = FacilityDataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DocumentType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDocumentType(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(FacilityDataFilePeer::DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(FacilityDataFilePeer::DOC_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$criteria->addJoin(FacilityDataFilePeer::FACILITY_ID, OrganizationPeer::ORGID);

		$rs = FacilityDataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Organization table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptOrganization(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(FacilityDataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(FacilityDataFilePeer::DATA_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(FacilityDataFilePeer::DOC_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$criteria->addJoin(FacilityDataFilePeer::DOC_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$rs = FacilityDataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of FacilityDataFile objects pre-filled with all related objects except DataFile.
	 *
	 * @return     array Array of FacilityDataFile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDataFile(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		FacilityDataFilePeer::addSelectColumns($c);
		$startcol2 = (FacilityDataFilePeer::NUM_COLUMNS - FacilityDataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DocumentFormatPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DocumentFormatPeer::NUM_COLUMNS;

		DocumentTypePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DocumentTypePeer::NUM_COLUMNS;

		OrganizationPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + OrganizationPeer::NUM_COLUMNS;

		$c->addJoin(FacilityDataFilePeer::DOC_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$c->addJoin(FacilityDataFilePeer::DOC_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$c->addJoin(FacilityDataFilePeer::FACILITY_ID, OrganizationPeer::ORGID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = FacilityDataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DocumentFormatPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getDocumentFormat(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initFacilityDataFiles();
				$obj2->addFacilityDataFile($obj1);
			}

			$omClass = DocumentTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDocumentType(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initFacilityDataFiles();
				$obj3->addFacilityDataFile($obj1);
			}

			$omClass = OrganizationPeer::getOMClass($rs, $startcol4);


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getOrganization(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initFacilityDataFiles();
				$obj4->addFacilityDataFile($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of FacilityDataFile objects pre-filled with all related objects except DocumentFormat.
	 *
	 * @return     array Array of FacilityDataFile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDocumentFormat(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		FacilityDataFilePeer::addSelectColumns($c);
		$startcol2 = (FacilityDataFilePeer::NUM_COLUMNS - FacilityDataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DocumentTypePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DocumentTypePeer::NUM_COLUMNS;

		OrganizationPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + OrganizationPeer::NUM_COLUMNS;

		$c->addJoin(FacilityDataFilePeer::DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(FacilityDataFilePeer::DOC_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$c->addJoin(FacilityDataFilePeer::FACILITY_ID, OrganizationPeer::ORGID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = FacilityDataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getDataFile(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initFacilityDataFiles();
				$obj2->addFacilityDataFile($obj1);
			}

			$omClass = DocumentTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDocumentType(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initFacilityDataFiles();
				$obj3->addFacilityDataFile($obj1);
			}

			$omClass = OrganizationPeer::getOMClass($rs, $startcol4);


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getOrganization(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initFacilityDataFiles();
				$obj4->addFacilityDataFile($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of FacilityDataFile objects pre-filled with all related objects except DocumentType.
	 *
	 * @return     array Array of FacilityDataFile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDocumentType(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		FacilityDataFilePeer::addSelectColumns($c);
		$startcol2 = (FacilityDataFilePeer::NUM_COLUMNS - FacilityDataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DocumentFormatPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DocumentFormatPeer::NUM_COLUMNS;

		OrganizationPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + OrganizationPeer::NUM_COLUMNS;

		$c->addJoin(FacilityDataFilePeer::DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(FacilityDataFilePeer::DOC_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$c->addJoin(FacilityDataFilePeer::FACILITY_ID, OrganizationPeer::ORGID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = FacilityDataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getDataFile(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initFacilityDataFiles();
				$obj2->addFacilityDataFile($obj1);
			}

			$omClass = DocumentFormatPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDocumentFormat(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initFacilityDataFiles();
				$obj3->addFacilityDataFile($obj1);
			}

			$omClass = OrganizationPeer::getOMClass($rs, $startcol4);


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getOrganization(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initFacilityDataFiles();
				$obj4->addFacilityDataFile($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of FacilityDataFile objects pre-filled with all related objects except Organization.
	 *
	 * @return     array Array of FacilityDataFile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptOrganization(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		FacilityDataFilePeer::addSelectColumns($c);
		$startcol2 = (FacilityDataFilePeer::NUM_COLUMNS - FacilityDataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DocumentFormatPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DocumentFormatPeer::NUM_COLUMNS;

		DocumentTypePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + DocumentTypePeer::NUM_COLUMNS;

		$c->addJoin(FacilityDataFilePeer::DATA_FILE_ID, DataFilePeer::ID);

		$c->addJoin(FacilityDataFilePeer::DOC_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$c->addJoin(FacilityDataFilePeer::DOC_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = FacilityDataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getDataFile(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initFacilityDataFiles();
				$obj2->addFacilityDataFile($obj1);
			}

			$omClass = DocumentFormatPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDocumentFormat(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initFacilityDataFiles();
				$obj3->addFacilityDataFile($obj1);
			}

			$omClass = DocumentTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getDocumentType(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addFacilityDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initFacilityDataFiles();
				$obj4->addFacilityDataFile($obj1);
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
		return FacilityDataFilePeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a FacilityDataFile or Criteria object.
	 *
	 * @param      mixed $values Criteria or FacilityDataFile object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from FacilityDataFile object
		}

		$criteria->remove(FacilityDataFilePeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a FacilityDataFile or Criteria object.
	 *
	 * @param      mixed $values Criteria or FacilityDataFile object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(FacilityDataFilePeer::ID);
			$selectCriteria->add(FacilityDataFilePeer::ID, $criteria->remove(FacilityDataFilePeer::ID), $comparison);

		} else { // $values is FacilityDataFile object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the FACILITY_DATA_FILE table.
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
			$affectedRows += BasePeer::doDeleteAll(FacilityDataFilePeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a FacilityDataFile or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or FacilityDataFile object or primary key or array of primary keys
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
			$con = Propel::getConnection(FacilityDataFilePeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof FacilityDataFile) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(FacilityDataFilePeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given FacilityDataFile object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      FacilityDataFile $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(FacilityDataFile $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(FacilityDataFilePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(FacilityDataFilePeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(FacilityDataFilePeer::DATA_FILE_ID))
			$columns[FacilityDataFilePeer::DATA_FILE_ID] = $obj->getDataFileId();

		if ($obj->isNew() || $obj->isColumnModified(FacilityDataFilePeer::DOC_FORMAT_ID))
			$columns[FacilityDataFilePeer::DOC_FORMAT_ID] = $obj->getDocFormatId();

		if ($obj->isNew() || $obj->isColumnModified(FacilityDataFilePeer::DOC_TYPE_ID))
			$columns[FacilityDataFilePeer::DOC_TYPE_ID] = $obj->getDocTypeId();

		if ($obj->isNew() || $obj->isColumnModified(FacilityDataFilePeer::FACILITY_ID))
			$columns[FacilityDataFilePeer::FACILITY_ID] = $obj->getFacilityId();

		if ($obj->isNew() || $obj->isColumnModified(FacilityDataFilePeer::GROUPBY))
			$columns[FacilityDataFilePeer::GROUPBY] = $obj->getGroupBy();

		if ($obj->isNew() || $obj->isColumnModified(FacilityDataFilePeer::INFO_TYPE))
			$columns[FacilityDataFilePeer::INFO_TYPE] = $obj->getInfoType();

		if ($obj->isNew() || $obj->isColumnModified(FacilityDataFilePeer::SUB_INFO_TYPE))
			$columns[FacilityDataFilePeer::SUB_INFO_TYPE] = $obj->getSubInfoType();

		}

		return BasePeer::doValidate(FacilityDataFilePeer::DATABASE_NAME, FacilityDataFilePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     FacilityDataFile
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(FacilityDataFilePeer::DATABASE_NAME);

		$criteria->add(FacilityDataFilePeer::ID, $pk);


		$v = FacilityDataFilePeer::doSelect($criteria, $con);

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
			$criteria->add(FacilityDataFilePeer::ID, $pks, Criteria::IN);
			$objs = FacilityDataFilePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseFacilityDataFilePeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseFacilityDataFilePeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/FacilityDataFileMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.FacilityDataFileMapBuilder');
}
