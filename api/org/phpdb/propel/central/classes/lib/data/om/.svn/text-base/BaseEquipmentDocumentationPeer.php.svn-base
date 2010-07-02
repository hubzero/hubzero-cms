<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by EquipmentDocumentationPeer::getOMClass()
include_once 'lib/data/EquipmentDocumentation.php';

/**
 * Base static class for performing query and update operations on the 'EQUIPMENT_DOCUMENTATION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEquipmentDocumentationPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'EQUIPMENT_DOCUMENTATION';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.EquipmentDocumentation';

	/** The total number of columns. */
	const NUM_COLUMNS = 9;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the EQUIPMENT_DOC_ID field */
	const EQUIPMENT_DOC_ID = 'EQUIPMENT_DOCUMENTATION.EQUIPMENT_DOC_ID';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'EQUIPMENT_DOCUMENTATION.DESCRIPTION';

	/** the column name for the DOCUMENT_FORMAT_ID field */
	const DOCUMENT_FORMAT_ID = 'EQUIPMENT_DOCUMENTATION.DOCUMENT_FORMAT_ID';

	/** the column name for the DOCUMENT_TYPE_ID field */
	const DOCUMENT_TYPE_ID = 'EQUIPMENT_DOCUMENTATION.DOCUMENT_TYPE_ID';

	/** the column name for the DOCUMENTATION_FILE_ID field */
	const DOCUMENTATION_FILE_ID = 'EQUIPMENT_DOCUMENTATION.DOCUMENTATION_FILE_ID';

	/** the column name for the EQUIPMENT_ID field */
	const EQUIPMENT_ID = 'EQUIPMENT_DOCUMENTATION.EQUIPMENT_ID';

	/** the column name for the LAST_MODIFIED field */
	const LAST_MODIFIED = 'EQUIPMENT_DOCUMENTATION.LAST_MODIFIED';

	/** the column name for the NAME field */
	const NAME = 'EQUIPMENT_DOCUMENTATION.NAME';

	/** the column name for the PAGE_COUNT field */
	const PAGE_COUNT = 'EQUIPMENT_DOCUMENTATION.PAGE_COUNT';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Description', 'DocumentFormatId', 'DocumentTypeId', 'DocumentationFileId', 'EquipmentId', 'LastModified', 'Name', 'PageCount', ),
		BasePeer::TYPE_COLNAME => array (EquipmentDocumentationPeer::EQUIPMENT_DOC_ID, EquipmentDocumentationPeer::DESCRIPTION, EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, EquipmentDocumentationPeer::EQUIPMENT_ID, EquipmentDocumentationPeer::LAST_MODIFIED, EquipmentDocumentationPeer::NAME, EquipmentDocumentationPeer::PAGE_COUNT, ),
		BasePeer::TYPE_FIELDNAME => array ('EQUIPMENT_DOC_ID', 'DESCRIPTION', 'DOCUMENT_FORMAT_ID', 'DOCUMENT_TYPE_ID', 'DOCUMENTATION_FILE_ID', 'EQUIPMENT_ID', 'LAST_MODIFIED', 'NAME', 'PAGE_COUNT', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Description' => 1, 'DocumentFormatId' => 2, 'DocumentTypeId' => 3, 'DocumentationFileId' => 4, 'EquipmentId' => 5, 'LastModified' => 6, 'Name' => 7, 'PageCount' => 8, ),
		BasePeer::TYPE_COLNAME => array (EquipmentDocumentationPeer::EQUIPMENT_DOC_ID => 0, EquipmentDocumentationPeer::DESCRIPTION => 1, EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID => 2, EquipmentDocumentationPeer::DOCUMENT_TYPE_ID => 3, EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID => 4, EquipmentDocumentationPeer::EQUIPMENT_ID => 5, EquipmentDocumentationPeer::LAST_MODIFIED => 6, EquipmentDocumentationPeer::NAME => 7, EquipmentDocumentationPeer::PAGE_COUNT => 8, ),
		BasePeer::TYPE_FIELDNAME => array ('EQUIPMENT_DOC_ID' => 0, 'DESCRIPTION' => 1, 'DOCUMENT_FORMAT_ID' => 2, 'DOCUMENT_TYPE_ID' => 3, 'DOCUMENTATION_FILE_ID' => 4, 'EQUIPMENT_ID' => 5, 'LAST_MODIFIED' => 6, 'NAME' => 7, 'PAGE_COUNT' => 8, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/EquipmentDocumentationMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.EquipmentDocumentationMapBuilder');
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
			$map = EquipmentDocumentationPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. EquipmentDocumentationPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(EquipmentDocumentationPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(EquipmentDocumentationPeer::EQUIPMENT_DOC_ID);

		$criteria->addSelectColumn(EquipmentDocumentationPeer::DESCRIPTION);

		$criteria->addSelectColumn(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID);

		$criteria->addSelectColumn(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID);

		$criteria->addSelectColumn(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID);

		$criteria->addSelectColumn(EquipmentDocumentationPeer::EQUIPMENT_ID);

		$criteria->addSelectColumn(EquipmentDocumentationPeer::LAST_MODIFIED);

		$criteria->addSelectColumn(EquipmentDocumentationPeer::NAME);

		$criteria->addSelectColumn(EquipmentDocumentationPeer::PAGE_COUNT);

	}

	const COUNT = 'COUNT(EQUIPMENT_DOCUMENTATION.EQUIPMENT_DOC_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT EQUIPMENT_DOCUMENTATION.EQUIPMENT_DOC_ID)';

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
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = EquipmentDocumentationPeer::doSelectRS($criteria, $con);
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
	 * @return     EquipmentDocumentation
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = EquipmentDocumentationPeer::doSelect($critcopy, $con);
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
		return EquipmentDocumentationPeer::populateObjects(EquipmentDocumentationPeer::doSelectRS($criteria, $con));
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
			EquipmentDocumentationPeer::addSelectColumns($criteria);
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
		$cls = EquipmentDocumentationPeer::getOMClass();
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
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, DataFilePeer::ID);

		$rs = EquipmentDocumentationPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$rs = EquipmentDocumentationPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$rs = EquipmentDocumentationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Equipment table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEquipment(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentDocumentationPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$rs = EquipmentDocumentationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of EquipmentDocumentation objects pre-filled with their DataFile objects.
	 *
	 * @return     array Array of EquipmentDocumentation objects.
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

		EquipmentDocumentationPeer::addSelectColumns($c);
		$startcol = (EquipmentDocumentationPeer::NUM_COLUMNS - EquipmentDocumentationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DataFilePeer::addSelectColumns($c);

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, DataFilePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentDocumentationPeer::getOMClass();

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
					$temp_obj2->addEquipmentDocumentation($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentDocumentations();
				$obj2->addEquipmentDocumentation($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentDocumentation objects pre-filled with their DocumentFormat objects.
	 *
	 * @return     array Array of EquipmentDocumentation objects.
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

		EquipmentDocumentationPeer::addSelectColumns($c);
		$startcol = (EquipmentDocumentationPeer::NUM_COLUMNS - EquipmentDocumentationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DocumentFormatPeer::addSelectColumns($c);

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentDocumentationPeer::getOMClass();

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
					$temp_obj2->addEquipmentDocumentation($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentDocumentations();
				$obj2->addEquipmentDocumentation($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentDocumentation objects pre-filled with their DocumentType objects.
	 *
	 * @return     array Array of EquipmentDocumentation objects.
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

		EquipmentDocumentationPeer::addSelectColumns($c);
		$startcol = (EquipmentDocumentationPeer::NUM_COLUMNS - EquipmentDocumentationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DocumentTypePeer::addSelectColumns($c);

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentDocumentationPeer::getOMClass();

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
					$temp_obj2->addEquipmentDocumentation($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentDocumentations();
				$obj2->addEquipmentDocumentation($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentDocumentation objects pre-filled with their Equipment objects.
	 *
	 * @return     array Array of EquipmentDocumentation objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEquipment(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentDocumentationPeer::addSelectColumns($c);
		$startcol = (EquipmentDocumentationPeer::NUM_COLUMNS - EquipmentDocumentationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		EquipmentPeer::addSelectColumns($c);

		$c->addJoin(EquipmentDocumentationPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentDocumentationPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addEquipmentDocumentation($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentDocumentations();
				$obj2->addEquipmentDocumentation($obj1); //CHECKME
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
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$criteria->addJoin(EquipmentDocumentationPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$rs = EquipmentDocumentationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of EquipmentDocumentation objects pre-filled with all related objects.
	 *
	 * @return     array Array of EquipmentDocumentation objects.
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

		EquipmentDocumentationPeer::addSelectColumns($c);
		$startcol2 = (EquipmentDocumentationPeer::NUM_COLUMNS - EquipmentDocumentationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DocumentFormatPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DocumentFormatPeer::NUM_COLUMNS;

		DocumentTypePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + DocumentTypePeer::NUM_COLUMNS;

		EquipmentPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + EquipmentPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$c->addJoin(EquipmentDocumentationPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentDocumentationPeer::getOMClass();


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
					$temp_obj2->addEquipmentDocumentation($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentDocumentations();
				$obj2->addEquipmentDocumentation($obj1);
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
					$temp_obj3->addEquipmentDocumentation($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentDocumentations();
				$obj3->addEquipmentDocumentation($obj1);
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
					$temp_obj4->addEquipmentDocumentation($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentDocumentations();
				$obj4->addEquipmentDocumentation($obj1);
			}


				// Add objects for joined Equipment rows
	
			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5 = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getEquipment(); // CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addEquipmentDocumentation($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj5->initEquipmentDocumentations();
				$obj5->addEquipmentDocumentation($obj1);
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
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$criteria->addJoin(EquipmentDocumentationPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$rs = EquipmentDocumentationPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$criteria->addJoin(EquipmentDocumentationPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$rs = EquipmentDocumentationPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$criteria->addJoin(EquipmentDocumentationPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$rs = EquipmentDocumentationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Equipment table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEquipment(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentDocumentationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$criteria->addJoin(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$rs = EquipmentDocumentationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of EquipmentDocumentation objects pre-filled with all related objects except DataFile.
	 *
	 * @return     array Array of EquipmentDocumentation objects.
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

		EquipmentDocumentationPeer::addSelectColumns($c);
		$startcol2 = (EquipmentDocumentationPeer::NUM_COLUMNS - EquipmentDocumentationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DocumentFormatPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DocumentFormatPeer::NUM_COLUMNS;

		DocumentTypePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DocumentTypePeer::NUM_COLUMNS;

		EquipmentPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + EquipmentPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$c->addJoin(EquipmentDocumentationPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentDocumentationPeer::getOMClass();

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
					$temp_obj2->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentDocumentations();
				$obj2->addEquipmentDocumentation($obj1);
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
					$temp_obj3->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentDocumentations();
				$obj3->addEquipmentDocumentation($obj1);
			}

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentDocumentations();
				$obj4->addEquipmentDocumentation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentDocumentation objects pre-filled with all related objects except DocumentFormat.
	 *
	 * @return     array Array of EquipmentDocumentation objects.
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

		EquipmentDocumentationPeer::addSelectColumns($c);
		$startcol2 = (EquipmentDocumentationPeer::NUM_COLUMNS - EquipmentDocumentationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DocumentTypePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DocumentTypePeer::NUM_COLUMNS;

		EquipmentPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + EquipmentPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);

		$c->addJoin(EquipmentDocumentationPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentDocumentationPeer::getOMClass();

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
					$temp_obj2->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentDocumentations();
				$obj2->addEquipmentDocumentation($obj1);
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
					$temp_obj3->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentDocumentations();
				$obj3->addEquipmentDocumentation($obj1);
			}

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentDocumentations();
				$obj4->addEquipmentDocumentation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentDocumentation objects pre-filled with all related objects except DocumentType.
	 *
	 * @return     array Array of EquipmentDocumentation objects.
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

		EquipmentDocumentationPeer::addSelectColumns($c);
		$startcol2 = (EquipmentDocumentationPeer::NUM_COLUMNS - EquipmentDocumentationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DocumentFormatPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DocumentFormatPeer::NUM_COLUMNS;

		EquipmentPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + EquipmentPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$c->addJoin(EquipmentDocumentationPeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentDocumentationPeer::getOMClass();

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
					$temp_obj2->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentDocumentations();
				$obj2->addEquipmentDocumentation($obj1);
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
					$temp_obj3->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentDocumentations();
				$obj3->addEquipmentDocumentation($obj1);
			}

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentDocumentations();
				$obj4->addEquipmentDocumentation($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentDocumentation objects pre-filled with all related objects except Equipment.
	 *
	 * @return     array Array of EquipmentDocumentation objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEquipment(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentDocumentationPeer::addSelectColumns($c);
		$startcol2 = (EquipmentDocumentationPeer::NUM_COLUMNS - EquipmentDocumentationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DocumentFormatPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DocumentFormatPeer::NUM_COLUMNS;

		DocumentTypePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + DocumentTypePeer::NUM_COLUMNS;

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID, DocumentFormatPeer::DOCUMENT_FORMAT_ID);

		$c->addJoin(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID, DocumentTypePeer::DOCUMENT_TYPE_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentDocumentationPeer::getOMClass();

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
					$temp_obj2->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentDocumentations();
				$obj2->addEquipmentDocumentation($obj1);
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
					$temp_obj3->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentDocumentations();
				$obj3->addEquipmentDocumentation($obj1);
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
					$temp_obj4->addEquipmentDocumentation($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentDocumentations();
				$obj4->addEquipmentDocumentation($obj1);
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
		return EquipmentDocumentationPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a EquipmentDocumentation or Criteria object.
	 *
	 * @param      mixed $values Criteria or EquipmentDocumentation object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from EquipmentDocumentation object
		}

		$criteria->remove(EquipmentDocumentationPeer::EQUIPMENT_DOC_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a EquipmentDocumentation or Criteria object.
	 *
	 * @param      mixed $values Criteria or EquipmentDocumentation object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(EquipmentDocumentationPeer::EQUIPMENT_DOC_ID);
			$selectCriteria->add(EquipmentDocumentationPeer::EQUIPMENT_DOC_ID, $criteria->remove(EquipmentDocumentationPeer::EQUIPMENT_DOC_ID), $comparison);

		} else { // $values is EquipmentDocumentation object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the EQUIPMENT_DOCUMENTATION table.
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
			$affectedRows += BasePeer::doDeleteAll(EquipmentDocumentationPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a EquipmentDocumentation or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or EquipmentDocumentation object or primary key or array of primary keys
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
			$con = Propel::getConnection(EquipmentDocumentationPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof EquipmentDocumentation) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_DOC_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given EquipmentDocumentation object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      EquipmentDocumentation $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(EquipmentDocumentation $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(EquipmentDocumentationPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(EquipmentDocumentationPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(EquipmentDocumentationPeer::DESCRIPTION))
			$columns[EquipmentDocumentationPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID))
			$columns[EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID] = $obj->getDocumentationFileId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID))
			$columns[EquipmentDocumentationPeer::DOCUMENT_FORMAT_ID] = $obj->getDocumentFormatId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentDocumentationPeer::DOCUMENT_TYPE_ID))
			$columns[EquipmentDocumentationPeer::DOCUMENT_TYPE_ID] = $obj->getDocumentTypeId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentDocumentationPeer::EQUIPMENT_ID))
			$columns[EquipmentDocumentationPeer::EQUIPMENT_ID] = $obj->getEquipmentId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentDocumentationPeer::LAST_MODIFIED))
			$columns[EquipmentDocumentationPeer::LAST_MODIFIED] = $obj->getLastModified();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentDocumentationPeer::NAME))
			$columns[EquipmentDocumentationPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentDocumentationPeer::PAGE_COUNT))
			$columns[EquipmentDocumentationPeer::PAGE_COUNT] = $obj->getPageCount();

		}

		return BasePeer::doValidate(EquipmentDocumentationPeer::DATABASE_NAME, EquipmentDocumentationPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     EquipmentDocumentation
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(EquipmentDocumentationPeer::DATABASE_NAME);

		$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_DOC_ID, $pk);


		$v = EquipmentDocumentationPeer::doSelect($criteria, $con);

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
			$criteria->add(EquipmentDocumentationPeer::EQUIPMENT_DOC_ID, $pks, Criteria::IN);
			$objs = EquipmentDocumentationPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseEquipmentDocumentationPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseEquipmentDocumentationPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/EquipmentDocumentationMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.EquipmentDocumentationMapBuilder');
}
