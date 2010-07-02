<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by DataFilePeer::getOMClass()
include_once 'lib/data/DataFile.php';

/**
 * Base static class for performing query and update operations on the 'DATA_FILE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseDataFilePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'DATA_FILE';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.DataFile';

	/** The total number of columns. */
	const NUM_COLUMNS = 20;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'DATA_FILE.ID';

	/** the column name for the AUTHOR_EMAILS field */
	const AUTHOR_EMAILS = 'DATA_FILE.AUTHOR_EMAILS';

	/** the column name for the AUTHORS field */
	const AUTHORS = 'DATA_FILE.AUTHORS';

	/** the column name for the CHECKSUM field */
	const CHECKSUM = 'DATA_FILE.CHECKSUM';

	/** the column name for the CREATED field */
	const CREATED = 'DATA_FILE.CREATED';

	/** the column name for the CURATION_STATUS field */
	const CURATION_STATUS = 'DATA_FILE.CURATION_STATUS';

	/** the column name for the DELETED field */
	const DELETED = 'DATA_FILE.DELETED';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'DATA_FILE.DESCRIPTION';

	/** the column name for the DIRECTORY field */
	const DIRECTORY = 'DATA_FILE.DIRECTORY';

	/** the column name for the FILESIZE field */
	const FILESIZE = 'DATA_FILE.FILESIZE';

	/** the column name for the HOW_TO_CITE field */
	const HOW_TO_CITE = 'DATA_FILE.HOW_TO_CITE';

	/** the column name for the NAME field */
	const NAME = 'DATA_FILE.NAME';

	/** the column name for the PAGE_COUNT field */
	const PAGE_COUNT = 'DATA_FILE.PAGE_COUNT';

	/** the column name for the PATH field */
	const PATH = 'DATA_FILE.PATH';

	/** the column name for the TITLE field */
	const TITLE = 'DATA_FILE.TITLE';

	/** the column name for the VIEWABLE field */
	const VIEWABLE = 'DATA_FILE.VIEWABLE';

	/** the column name for the THUMB_ID field */
	const THUMB_ID = 'DATA_FILE.THUMB_ID';

	/** the column name for the DOCUMENT_FORMAT_ID field */
	const DOCUMENT_FORMAT_ID = 'DATA_FILE.DOCUMENT_FORMAT_ID';

	/** the column name for the OPENING_TOOL field */
	const OPENING_TOOL = 'DATA_FILE.OPENING_TOOL';

	/** the column name for the USAGE_TYPE_ID field */
	const USAGE_TYPE_ID = 'DATA_FILE.USAGE_TYPE_ID';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'AuthorEmails', 'Authors', 'Checksum', 'Created', 'CurationStatus', 'Deleted', 'Description', 'Directory', 'Filesize', 'HowToCite', 'Name', 'PageCount', 'Path', 'Title', 'View', 'ThumbId', 'DocumentFormatId', 'OpeningTool', 'UsageTypeId', ),
		BasePeer::TYPE_COLNAME => array (DataFilePeer::ID, DataFilePeer::AUTHOR_EMAILS, DataFilePeer::AUTHORS, DataFilePeer::CHECKSUM, DataFilePeer::CREATED, DataFilePeer::CURATION_STATUS, DataFilePeer::DELETED, DataFilePeer::DESCRIPTION, DataFilePeer::DIRECTORY, DataFilePeer::FILESIZE, DataFilePeer::HOW_TO_CITE, DataFilePeer::NAME, DataFilePeer::PAGE_COUNT, DataFilePeer::PATH, DataFilePeer::TITLE, DataFilePeer::VIEWABLE, DataFilePeer::THUMB_ID, DataFilePeer::DOCUMENT_FORMAT_ID, DataFilePeer::OPENING_TOOL, DataFilePeer::USAGE_TYPE_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'AUTHOR_EMAILS', 'AUTHORS', 'CHECKSUM', 'CREATED', 'CURATION_STATUS', 'DELETED', 'DESCRIPTION', 'DIRECTORY', 'FILESIZE', 'HOW_TO_CITE', 'NAME', 'PAGE_COUNT', 'PATH', 'TITLE', 'VIEWABLE', 'THUMB_ID', 'DOCUMENT_FORMAT_ID', 'OPENING_TOOL', 'USAGE_TYPE_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'AuthorEmails' => 1, 'Authors' => 2, 'Checksum' => 3, 'Created' => 4, 'CurationStatus' => 5, 'Deleted' => 6, 'Description' => 7, 'Directory' => 8, 'Filesize' => 9, 'HowToCite' => 10, 'Name' => 11, 'PageCount' => 12, 'Path' => 13, 'Title' => 14, 'View' => 15, 'ThumbId' => 16, 'DocumentFormatId' => 17, 'OpeningTool' => 18, 'UsageTypeId' => 19, ),
		BasePeer::TYPE_COLNAME => array (DataFilePeer::ID => 0, DataFilePeer::AUTHOR_EMAILS => 1, DataFilePeer::AUTHORS => 2, DataFilePeer::CHECKSUM => 3, DataFilePeer::CREATED => 4, DataFilePeer::CURATION_STATUS => 5, DataFilePeer::DELETED => 6, DataFilePeer::DESCRIPTION => 7, DataFilePeer::DIRECTORY => 8, DataFilePeer::FILESIZE => 9, DataFilePeer::HOW_TO_CITE => 10, DataFilePeer::NAME => 11, DataFilePeer::PAGE_COUNT => 12, DataFilePeer::PATH => 13, DataFilePeer::TITLE => 14, DataFilePeer::VIEWABLE => 15, DataFilePeer::THUMB_ID => 16, DataFilePeer::DOCUMENT_FORMAT_ID => 17, DataFilePeer::OPENING_TOOL => 18, DataFilePeer::USAGE_TYPE_ID => 19, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'AUTHOR_EMAILS' => 1, 'AUTHORS' => 2, 'CHECKSUM' => 3, 'CREATED' => 4, 'CURATION_STATUS' => 5, 'DELETED' => 6, 'DESCRIPTION' => 7, 'DIRECTORY' => 8, 'FILESIZE' => 9, 'HOW_TO_CITE' => 10, 'NAME' => 11, 'PAGE_COUNT' => 12, 'PATH' => 13, 'TITLE' => 14, 'VIEWABLE' => 15, 'THUMB_ID' => 16, 'DOCUMENT_FORMAT_ID' => 17, 'OPENING_TOOL' => 18, 'USAGE_TYPE_ID' => 19, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/DataFileMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.DataFileMapBuilder');
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
			$map = DataFilePeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. DataFilePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(DataFilePeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(DataFilePeer::ID);

		$criteria->addSelectColumn(DataFilePeer::AUTHOR_EMAILS);

		$criteria->addSelectColumn(DataFilePeer::AUTHORS);

		$criteria->addSelectColumn(DataFilePeer::CHECKSUM);

		$criteria->addSelectColumn(DataFilePeer::CREATED);

		$criteria->addSelectColumn(DataFilePeer::CURATION_STATUS);

		$criteria->addSelectColumn(DataFilePeer::DELETED);

		$criteria->addSelectColumn(DataFilePeer::DESCRIPTION);

		$criteria->addSelectColumn(DataFilePeer::DIRECTORY);

		$criteria->addSelectColumn(DataFilePeer::FILESIZE);

		$criteria->addSelectColumn(DataFilePeer::HOW_TO_CITE);

		$criteria->addSelectColumn(DataFilePeer::NAME);

		$criteria->addSelectColumn(DataFilePeer::PAGE_COUNT);

		$criteria->addSelectColumn(DataFilePeer::PATH);

		$criteria->addSelectColumn(DataFilePeer::TITLE);

		$criteria->addSelectColumn(DataFilePeer::VIEWABLE);

		$criteria->addSelectColumn(DataFilePeer::THUMB_ID);

		$criteria->addSelectColumn(DataFilePeer::DOCUMENT_FORMAT_ID);

		$criteria->addSelectColumn(DataFilePeer::OPENING_TOOL);

		$criteria->addSelectColumn(DataFilePeer::USAGE_TYPE_ID);

	}

	const COUNT = 'COUNT(DATA_FILE.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT DATA_FILE.ID)';

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
			$criteria->addSelectColumn(DataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(DataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = DataFilePeer::doSelectRS($criteria, $con);
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
	 * @return     DataFile
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = DataFilePeer::doSelect($critcopy, $con);
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
		return DataFilePeer::populateObjects(DataFilePeer::doSelectRS($criteria, $con));
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
			DataFilePeer::addSelectColumns($criteria);
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
		$cls = DataFilePeer::getOMClass();
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
			$criteria->addSelectColumn(DataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(DataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(DataFilePeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$rs = DataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related EntityType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEntityType(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(DataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(DataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(DataFilePeer::USAGE_TYPE_ID, EntityTypePeer::ID);

		$rs = DataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of DataFile objects pre-filled with their DocumentFormat objects.
	 *
	 * @return     array Array of DataFile objects.
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

		DataFilePeer::addSelectColumns($c);
		$startcol = (DataFilePeer::NUM_COLUMNS - DataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DocumentFormatPeer::addSelectColumns($c);

		$c->addJoin(DataFilePeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = DataFilePeer::getOMClass();

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
					$temp_obj2->addDataFile($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initDataFiles();
				$obj2->addDataFile($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of DataFile objects pre-filled with their EntityType objects.
	 *
	 * @return     array Array of DataFile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEntityType(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		DataFilePeer::addSelectColumns($c);
		$startcol = (DataFilePeer::NUM_COLUMNS - DataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		EntityTypePeer::addSelectColumns($c);

		$c->addJoin(DataFilePeer::USAGE_TYPE_ID, EntityTypePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = DataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EntityTypePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getEntityType(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addDataFile($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initDataFiles();
				$obj2->addDataFile($obj1); //CHECKME
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
			$criteria->addSelectColumn(DataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(DataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(DataFilePeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$criteria->addJoin(DataFilePeer::USAGE_TYPE_ID, EntityTypePeer::ID);

		$rs = DataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of DataFile objects pre-filled with all related objects.
	 *
	 * @return     array Array of DataFile objects.
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

		DataFilePeer::addSelectColumns($c);
		$startcol2 = (DataFilePeer::NUM_COLUMNS - DataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DocumentFormatPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DocumentFormatPeer::NUM_COLUMNS;

		EntityTypePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + EntityTypePeer::NUM_COLUMNS;

		$c->addJoin(DataFilePeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$c->addJoin(DataFilePeer::USAGE_TYPE_ID, EntityTypePeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined DocumentFormat rows
	
			$omClass = DocumentFormatPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getDocumentFormat(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addDataFile($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initDataFiles();
				$obj2->addDataFile($obj1);
			}


				// Add objects for joined EntityType rows
	
			$omClass = EntityTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getEntityType(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addDataFile($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initDataFiles();
				$obj3->addDataFile($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByThumbId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDataFileRelatedByThumbId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(DataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(DataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(DataFilePeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$criteria->addJoin(DataFilePeer::USAGE_TYPE_ID, EntityTypePeer::ID);

		$rs = DataFilePeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(DataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(DataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(DataFilePeer::USAGE_TYPE_ID, EntityTypePeer::ID);

		$rs = DataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related EntityType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEntityType(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(DataFilePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(DataFilePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(DataFilePeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$rs = DataFilePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of DataFile objects pre-filled with all related objects except DataFileRelatedByThumbId.
	 *
	 * @return     array Array of DataFile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDataFileRelatedByThumbId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		DataFilePeer::addSelectColumns($c);
		$startcol2 = (DataFilePeer::NUM_COLUMNS - DataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DocumentFormatPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DocumentFormatPeer::NUM_COLUMNS;

		EntityTypePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + EntityTypePeer::NUM_COLUMNS;

		$c->addJoin(DataFilePeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$c->addJoin(DataFilePeer::USAGE_TYPE_ID, EntityTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = DataFilePeer::getOMClass();

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
					$temp_obj2->addDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initDataFiles();
				$obj2->addDataFile($obj1);
			}

			$omClass = EntityTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getEntityType(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initDataFiles();
				$obj3->addDataFile($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of DataFile objects pre-filled with all related objects except DocumentFormat.
	 *
	 * @return     array Array of DataFile objects.
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

		DataFilePeer::addSelectColumns($c);
		$startcol2 = (DataFilePeer::NUM_COLUMNS - DataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EntityTypePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EntityTypePeer::NUM_COLUMNS;

		$c->addJoin(DataFilePeer::USAGE_TYPE_ID, EntityTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = DataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EntityTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEntityType(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initDataFiles();
				$obj2->addDataFile($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of DataFile objects pre-filled with all related objects except EntityType.
	 *
	 * @return     array Array of DataFile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEntityType(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		DataFilePeer::addSelectColumns($c);
		$startcol2 = (DataFilePeer::NUM_COLUMNS - DataFilePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DocumentFormatPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DocumentFormatPeer::NUM_COLUMNS;

		$c->addJoin(DataFilePeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = DataFilePeer::getOMClass();

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
					$temp_obj2->addDataFile($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initDataFiles();
				$obj2->addDataFile($obj1);
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
		return DataFilePeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a DataFile or Criteria object.
	 *
	 * @param      mixed $values Criteria or DataFile object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from DataFile object
		}

		$criteria->remove(DataFilePeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a DataFile or Criteria object.
	 *
	 * @param      mixed $values Criteria or DataFile object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(DataFilePeer::ID);
			$selectCriteria->add(DataFilePeer::ID, $criteria->remove(DataFilePeer::ID), $comparison);

		} else { // $values is DataFile object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the DATA_FILE table.
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
			$affectedRows += BasePeer::doDeleteAll(DataFilePeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a DataFile or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or DataFile object or primary key or array of primary keys
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
			$con = Propel::getConnection(DataFilePeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof DataFile) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(DataFilePeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given DataFile object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      DataFile $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(DataFile $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(DataFilePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(DataFilePeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::AUTHORS))
			$columns[DataFilePeer::AUTHORS] = $obj->getAuthors();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::AUTHOR_EMAILS))
			$columns[DataFilePeer::AUTHOR_EMAILS] = $obj->getAuthorEmails();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::CHECKSUM))
			$columns[DataFilePeer::CHECKSUM] = $obj->getChecksum();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::CREATED))
			$columns[DataFilePeer::CREATED] = $obj->getCreated();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::CURATION_STATUS))
			$columns[DataFilePeer::CURATION_STATUS] = $obj->getCurationStatus();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::DELETED))
			$columns[DataFilePeer::DELETED] = $obj->getDeleted();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::DESCRIPTION))
			$columns[DataFilePeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::DIRECTORY))
			$columns[DataFilePeer::DIRECTORY] = $obj->getDirectory();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::FILESIZE))
			$columns[DataFilePeer::FILESIZE] = $obj->getFilesize();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::HOW_TO_CITE))
			$columns[DataFilePeer::HOW_TO_CITE] = $obj->getHowToCite();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::NAME))
			$columns[DataFilePeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::PAGE_COUNT))
			$columns[DataFilePeer::PAGE_COUNT] = $obj->getPageCount();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::PATH))
			$columns[DataFilePeer::PATH] = $obj->getPath();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::TITLE))
			$columns[DataFilePeer::TITLE] = $obj->getTitle();

		if ($obj->isNew() || $obj->isColumnModified(DataFilePeer::VIEWABLE))
			$columns[DataFilePeer::VIEWABLE] = $obj->getView();

		}

		return BasePeer::doValidate(DataFilePeer::DATABASE_NAME, DataFilePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     DataFile
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(DataFilePeer::DATABASE_NAME);

		$criteria->add(DataFilePeer::ID, $pk);


		$v = DataFilePeer::doSelect($criteria, $con);

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
			$criteria->add(DataFilePeer::ID, $pks, Criteria::IN);
			$objs = DataFilePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseDataFilePeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseDataFilePeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/DataFileMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.DataFileMapBuilder');
}
