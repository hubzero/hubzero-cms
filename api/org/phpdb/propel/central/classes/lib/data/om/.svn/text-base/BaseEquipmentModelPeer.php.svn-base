<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by EquipmentModelPeer::getOMClass()
include_once 'lib/data/EquipmentModel.php';

/**
 * Base static class for performing query and update operations on the 'EQUIPMENT_MODEL' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEquipmentModelPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'EQUIPMENT_MODEL';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.EquipmentModel';

	/** The total number of columns. */
	const NUM_COLUMNS = 16;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'EQUIPMENT_MODEL.ID';

	/** the column name for the ADDITIONAL_SPEC_FILE_ID field */
	const ADDITIONAL_SPEC_FILE_ID = 'EQUIPMENT_MODEL.ADDITIONAL_SPEC_FILE_ID';

	/** the column name for the ADDITIONAL_SPEC_PAGE_COUNT field */
	const ADDITIONAL_SPEC_PAGE_COUNT = 'EQUIPMENT_MODEL.ADDITIONAL_SPEC_PAGE_COUNT';

	/** the column name for the DESIGN_CONSIDERATION_FILE_ID field */
	const DESIGN_CONSIDERATION_FILE_ID = 'EQUIPMENT_MODEL.DESIGN_CONSIDERATION_FILE_ID';

	/** the column name for the DESIGN_PAGE_COUNT field */
	const DESIGN_PAGE_COUNT = 'EQUIPMENT_MODEL.DESIGN_PAGE_COUNT';

	/** the column name for the EQUIPMENT_CLASS_ID field */
	const EQUIPMENT_CLASS_ID = 'EQUIPMENT_MODEL.EQUIPMENT_CLASS_ID';

	/** the column name for the INTERFACE_DOC_FILE_ID field */
	const INTERFACE_DOC_FILE_ID = 'EQUIPMENT_MODEL.INTERFACE_DOC_FILE_ID';

	/** the column name for the INTERFACE_DOC_PAGE_COUNT field */
	const INTERFACE_DOC_PAGE_COUNT = 'EQUIPMENT_MODEL.INTERFACE_DOC_PAGE_COUNT';

	/** the column name for the MANUFACTURER field */
	const MANUFACTURER = 'EQUIPMENT_MODEL.MANUFACTURER';

	/** the column name for the MANUFACTURER_DOC_FILE_ID field */
	const MANUFACTURER_DOC_FILE_ID = 'EQUIPMENT_MODEL.MANUFACTURER_DOC_FILE_ID';

	/** the column name for the MANUFACTURER_DOC_PAGE_COUNT field */
	const MANUFACTURER_DOC_PAGE_COUNT = 'EQUIPMENT_MODEL.MANUFACTURER_DOC_PAGE_COUNT';

	/** the column name for the MODEL_NUMBER field */
	const MODEL_NUMBER = 'EQUIPMENT_MODEL.MODEL_NUMBER';

	/** the column name for the NAME field */
	const NAME = 'EQUIPMENT_MODEL.NAME';

	/** the column name for the SUBCOMPONENTS_DOC_FILE_ID field */
	const SUBCOMPONENTS_DOC_FILE_ID = 'EQUIPMENT_MODEL.SUBCOMPONENTS_DOC_FILE_ID';

	/** the column name for the SUBCOMPONENTS_DOC_PAGE_COUNT field */
	const SUBCOMPONENTS_DOC_PAGE_COUNT = 'EQUIPMENT_MODEL.SUBCOMPONENTS_DOC_PAGE_COUNT';

	/** the column name for the SUPPLIER field */
	const SUPPLIER = 'EQUIPMENT_MODEL.SUPPLIER';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'AdditionalSpecFileId', 'AdditionalSpecPageCount', 'DesignConsiderationFileId', 'DesignConsiderationPageCount', 'EquipmentClassId', 'InterfaceDocFileId', 'InterfaceDocPageCount', 'Manufacturer', 'ManufacturerDocFileId', 'ManufacturerDocPageCount', 'ModelNumber', 'Name', 'SubcomponentsDocFileId', 'SubcomponentsDocPageCount', 'Supplier', ),
		BasePeer::TYPE_COLNAME => array (EquipmentModelPeer::ID, EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, EquipmentModelPeer::ADDITIONAL_SPEC_PAGE_COUNT, EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, EquipmentModelPeer::DESIGN_PAGE_COUNT, EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentModelPeer::INTERFACE_DOC_FILE_ID, EquipmentModelPeer::INTERFACE_DOC_PAGE_COUNT, EquipmentModelPeer::MANUFACTURER, EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, EquipmentModelPeer::MANUFACTURER_DOC_PAGE_COUNT, EquipmentModelPeer::MODEL_NUMBER, EquipmentModelPeer::NAME, EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, EquipmentModelPeer::SUBCOMPONENTS_DOC_PAGE_COUNT, EquipmentModelPeer::SUPPLIER, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'ADDITIONAL_SPEC_FILE_ID', 'ADDITIONAL_SPEC_PAGE_COUNT', 'DESIGN_CONSIDERATION_FILE_ID', 'DESIGN_PAGE_COUNT', 'EQUIPMENT_CLASS_ID', 'INTERFACE_DOC_FILE_ID', 'INTERFACE_DOC_PAGE_COUNT', 'MANUFACTURER', 'MANUFACTURER_DOC_FILE_ID', 'MANUFACTURER_DOC_PAGE_COUNT', 'MODEL_NUMBER', 'NAME', 'SUBCOMPONENTS_DOC_FILE_ID', 'SUBCOMPONENTS_DOC_PAGE_COUNT', 'SUPPLIER', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'AdditionalSpecFileId' => 1, 'AdditionalSpecPageCount' => 2, 'DesignConsiderationFileId' => 3, 'DesignConsiderationPageCount' => 4, 'EquipmentClassId' => 5, 'InterfaceDocFileId' => 6, 'InterfaceDocPageCount' => 7, 'Manufacturer' => 8, 'ManufacturerDocFileId' => 9, 'ManufacturerDocPageCount' => 10, 'ModelNumber' => 11, 'Name' => 12, 'SubcomponentsDocFileId' => 13, 'SubcomponentsDocPageCount' => 14, 'Supplier' => 15, ),
		BasePeer::TYPE_COLNAME => array (EquipmentModelPeer::ID => 0, EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID => 1, EquipmentModelPeer::ADDITIONAL_SPEC_PAGE_COUNT => 2, EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID => 3, EquipmentModelPeer::DESIGN_PAGE_COUNT => 4, EquipmentModelPeer::EQUIPMENT_CLASS_ID => 5, EquipmentModelPeer::INTERFACE_DOC_FILE_ID => 6, EquipmentModelPeer::INTERFACE_DOC_PAGE_COUNT => 7, EquipmentModelPeer::MANUFACTURER => 8, EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID => 9, EquipmentModelPeer::MANUFACTURER_DOC_PAGE_COUNT => 10, EquipmentModelPeer::MODEL_NUMBER => 11, EquipmentModelPeer::NAME => 12, EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID => 13, EquipmentModelPeer::SUBCOMPONENTS_DOC_PAGE_COUNT => 14, EquipmentModelPeer::SUPPLIER => 15, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'ADDITIONAL_SPEC_FILE_ID' => 1, 'ADDITIONAL_SPEC_PAGE_COUNT' => 2, 'DESIGN_CONSIDERATION_FILE_ID' => 3, 'DESIGN_PAGE_COUNT' => 4, 'EQUIPMENT_CLASS_ID' => 5, 'INTERFACE_DOC_FILE_ID' => 6, 'INTERFACE_DOC_PAGE_COUNT' => 7, 'MANUFACTURER' => 8, 'MANUFACTURER_DOC_FILE_ID' => 9, 'MANUFACTURER_DOC_PAGE_COUNT' => 10, 'MODEL_NUMBER' => 11, 'NAME' => 12, 'SUBCOMPONENTS_DOC_FILE_ID' => 13, 'SUBCOMPONENTS_DOC_PAGE_COUNT' => 14, 'SUPPLIER' => 15, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/EquipmentModelMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.EquipmentModelMapBuilder');
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
			$map = EquipmentModelPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. EquipmentModelPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(EquipmentModelPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(EquipmentModelPeer::ID);

		$criteria->addSelectColumn(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID);

		$criteria->addSelectColumn(EquipmentModelPeer::ADDITIONAL_SPEC_PAGE_COUNT);

		$criteria->addSelectColumn(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID);

		$criteria->addSelectColumn(EquipmentModelPeer::DESIGN_PAGE_COUNT);

		$criteria->addSelectColumn(EquipmentModelPeer::EQUIPMENT_CLASS_ID);

		$criteria->addSelectColumn(EquipmentModelPeer::INTERFACE_DOC_FILE_ID);

		$criteria->addSelectColumn(EquipmentModelPeer::INTERFACE_DOC_PAGE_COUNT);

		$criteria->addSelectColumn(EquipmentModelPeer::MANUFACTURER);

		$criteria->addSelectColumn(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID);

		$criteria->addSelectColumn(EquipmentModelPeer::MANUFACTURER_DOC_PAGE_COUNT);

		$criteria->addSelectColumn(EquipmentModelPeer::MODEL_NUMBER);

		$criteria->addSelectColumn(EquipmentModelPeer::NAME);

		$criteria->addSelectColumn(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID);

		$criteria->addSelectColumn(EquipmentModelPeer::SUBCOMPONENTS_DOC_PAGE_COUNT);

		$criteria->addSelectColumn(EquipmentModelPeer::SUPPLIER);

	}

	const COUNT = 'COUNT(EQUIPMENT_MODEL.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT EQUIPMENT_MODEL.ID)';

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
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
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
	 * @return     EquipmentModel
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = EquipmentModelPeer::doSelect($critcopy, $con);
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
		return EquipmentModelPeer::populateObjects(EquipmentModelPeer::doSelectRS($criteria, $con));
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
			EquipmentModelPeer::addSelectColumns($criteria);
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
		$cls = EquipmentModelPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByAdditionalSpecFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinDataFileRelatedByAdditionalSpecFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, DataFilePeer::ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByInterfaceDocFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinDataFileRelatedByInterfaceDocFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, DataFilePeer::ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByManufacturerDocFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinDataFileRelatedByManufacturerDocFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, DataFilePeer::ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedBySubcomponentsDocFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinDataFileRelatedBySubcomponentsDocFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, DataFilePeer::ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByDesignConsiderationFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinDataFileRelatedByDesignConsiderationFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, DataFilePeer::ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related EquipmentClass table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEquipmentClass(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with their DataFile objects.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinDataFileRelatedByAdditionalSpecFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DataFilePeer::addSelectColumns($c);

		$c->addJoin(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, DataFilePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getDataFileRelatedByAdditionalSpecFileId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addEquipmentModelRelatedByAdditionalSpecFileId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentModelsRelatedByAdditionalSpecFileId();
				$obj2->addEquipmentModelRelatedByAdditionalSpecFileId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with their DataFile objects.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinDataFileRelatedByInterfaceDocFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DataFilePeer::addSelectColumns($c);

		$c->addJoin(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, DataFilePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getDataFileRelatedByInterfaceDocFileId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addEquipmentModelRelatedByInterfaceDocFileId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentModelsRelatedByInterfaceDocFileId();
				$obj2->addEquipmentModelRelatedByInterfaceDocFileId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with their DataFile objects.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinDataFileRelatedByManufacturerDocFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DataFilePeer::addSelectColumns($c);

		$c->addJoin(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, DataFilePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getDataFileRelatedByManufacturerDocFileId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addEquipmentModelRelatedByManufacturerDocFileId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentModelsRelatedByManufacturerDocFileId();
				$obj2->addEquipmentModelRelatedByManufacturerDocFileId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with their DataFile objects.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinDataFileRelatedBySubcomponentsDocFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DataFilePeer::addSelectColumns($c);

		$c->addJoin(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, DataFilePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getDataFileRelatedBySubcomponentsDocFileId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addEquipmentModelRelatedBySubcomponentsDocFileId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentModelsRelatedBySubcomponentsDocFileId();
				$obj2->addEquipmentModelRelatedBySubcomponentsDocFileId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with their DataFile objects.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinDataFileRelatedByDesignConsiderationFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		DataFilePeer::addSelectColumns($c);

		$c->addJoin(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, DataFilePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = DataFilePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getDataFileRelatedByDesignConsiderationFileId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addEquipmentModelRelatedByDesignConsiderationFileId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentModelsRelatedByDesignConsiderationFileId();
				$obj2->addEquipmentModelRelatedByDesignConsiderationFileId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with their EquipmentClass objects.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEquipmentClass(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		EquipmentClassPeer::addSelectColumns($c);

		$c->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentClassPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getEquipmentClass(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addEquipmentModel($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentModels();
				$obj2->addEquipmentModel($obj1); //CHECKME
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
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with all related objects.
	 *
	 * @return     array Array of EquipmentModel objects.
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

		EquipmentModelPeer::addSelectColumns($c);
		$startcol2 = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol6 = $startcol5 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol7 = $startcol6 + DataFilePeer::NUM_COLUMNS;

		EquipmentClassPeer::addSelectColumns($c);
		$startcol8 = $startcol7 + EquipmentClassPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();


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
				$temp_obj2 = $temp_obj1->getDataFileRelatedByAdditionalSpecFileId(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentModelRelatedByAdditionalSpecFileId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentModelsRelatedByAdditionalSpecFileId();
				$obj2->addEquipmentModelRelatedByAdditionalSpecFileId($obj1);
			}


				// Add objects for joined DataFile rows
	
			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDataFileRelatedByInterfaceDocFileId(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addEquipmentModelRelatedByInterfaceDocFileId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentModelsRelatedByInterfaceDocFileId();
				$obj3->addEquipmentModelRelatedByInterfaceDocFileId($obj1);
			}


				// Add objects for joined DataFile rows
	
			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getDataFileRelatedByManufacturerDocFileId(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addEquipmentModelRelatedByManufacturerDocFileId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentModelsRelatedByManufacturerDocFileId();
				$obj4->addEquipmentModelRelatedByManufacturerDocFileId($obj1);
			}


				// Add objects for joined DataFile rows
	
			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5 = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getDataFileRelatedBySubcomponentsDocFileId(); // CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addEquipmentModelRelatedBySubcomponentsDocFileId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj5->initEquipmentModelsRelatedBySubcomponentsDocFileId();
				$obj5->addEquipmentModelRelatedBySubcomponentsDocFileId($obj1);
			}


				// Add objects for joined DataFile rows
	
			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj6 = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getDataFileRelatedByDesignConsiderationFileId(); // CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addEquipmentModelRelatedByDesignConsiderationFileId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj6->initEquipmentModelsRelatedByDesignConsiderationFileId();
				$obj6->addEquipmentModelRelatedByDesignConsiderationFileId($obj1);
			}


				// Add objects for joined EquipmentClass rows
	
			$omClass = EquipmentClassPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj7 = new $cls();
			$obj7->hydrate($rs, $startcol7);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj7 = $temp_obj1->getEquipmentClass(); // CHECKME
				if ($temp_obj7->getPrimaryKey() === $obj7->getPrimaryKey()) {
					$newObject = false;
					$temp_obj7->addEquipmentModel($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj7->initEquipmentModels();
				$obj7->addEquipmentModel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByAdditionalSpecFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDataFileRelatedByAdditionalSpecFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByInterfaceDocFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDataFileRelatedByInterfaceDocFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByManufacturerDocFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDataFileRelatedByManufacturerDocFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedBySubcomponentsDocFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDataFileRelatedBySubcomponentsDocFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related DataFileRelatedByDesignConsiderationFileId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptDataFileRelatedByDesignConsiderationFileId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related EquipmentClass table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEquipmentClass(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentModelPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, DataFilePeer::ID);

		$criteria->addJoin(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, DataFilePeer::ID);

		$rs = EquipmentModelPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with all related objects except DataFileRelatedByAdditionalSpecFileId.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDataFileRelatedByAdditionalSpecFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol2 = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentClassPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentClassPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentClassPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipmentClass(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentModel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentModels();
				$obj2->addEquipmentModel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with all related objects except DataFileRelatedByInterfaceDocFileId.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDataFileRelatedByInterfaceDocFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol2 = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentClassPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentClassPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentClassPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipmentClass(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentModel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentModels();
				$obj2->addEquipmentModel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with all related objects except DataFileRelatedByManufacturerDocFileId.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDataFileRelatedByManufacturerDocFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol2 = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentClassPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentClassPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentClassPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipmentClass(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentModel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentModels();
				$obj2->addEquipmentModel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with all related objects except DataFileRelatedBySubcomponentsDocFileId.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDataFileRelatedBySubcomponentsDocFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol2 = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentClassPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentClassPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentClassPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipmentClass(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentModel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentModels();
				$obj2->addEquipmentModel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with all related objects except DataFileRelatedByDesignConsiderationFileId.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptDataFileRelatedByDesignConsiderationFileId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol2 = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentClassPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentClassPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentModelPeer::EQUIPMENT_CLASS_ID, EquipmentClassPeer::EQUIPMENT_CLASS_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentClassPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipmentClass(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentModel($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentModels();
				$obj2->addEquipmentModel($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentModel objects pre-filled with all related objects except EquipmentClass.
	 *
	 * @return     array Array of EquipmentModel objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEquipmentClass(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentModelPeer::addSelectColumns($c);
		$startcol2 = (EquipmentModelPeer::NUM_COLUMNS - EquipmentModelPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		DataFilePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol6 = $startcol5 + DataFilePeer::NUM_COLUMNS;

		DataFilePeer::addSelectColumns($c);
		$startcol7 = $startcol6 + DataFilePeer::NUM_COLUMNS;

		$c->addJoin(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, DataFilePeer::ID);

		$c->addJoin(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, DataFilePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentModelPeer::getOMClass();

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
				$temp_obj2 = $temp_obj1->getDataFileRelatedByAdditionalSpecFileId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentModelRelatedByAdditionalSpecFileId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentModelsRelatedByAdditionalSpecFileId();
				$obj2->addEquipmentModelRelatedByAdditionalSpecFileId($obj1);
			}

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getDataFileRelatedByInterfaceDocFileId(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addEquipmentModelRelatedByInterfaceDocFileId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentModelsRelatedByInterfaceDocFileId();
				$obj3->addEquipmentModelRelatedByInterfaceDocFileId($obj1);
			}

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getDataFileRelatedByManufacturerDocFileId(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addEquipmentModelRelatedByManufacturerDocFileId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentModelsRelatedByManufacturerDocFileId();
				$obj4->addEquipmentModelRelatedByManufacturerDocFileId($obj1);
			}

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getDataFileRelatedBySubcomponentsDocFileId(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addEquipmentModelRelatedBySubcomponentsDocFileId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initEquipmentModelsRelatedBySubcomponentsDocFileId();
				$obj5->addEquipmentModelRelatedBySubcomponentsDocFileId($obj1);
			}

			$omClass = DataFilePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj6  = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getDataFileRelatedByDesignConsiderationFileId(); //CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addEquipmentModelRelatedByDesignConsiderationFileId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj6->initEquipmentModelsRelatedByDesignConsiderationFileId();
				$obj6->addEquipmentModelRelatedByDesignConsiderationFileId($obj1);
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
		return EquipmentModelPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a EquipmentModel or Criteria object.
	 *
	 * @param      mixed $values Criteria or EquipmentModel object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from EquipmentModel object
		}

		$criteria->remove(EquipmentModelPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a EquipmentModel or Criteria object.
	 *
	 * @param      mixed $values Criteria or EquipmentModel object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(EquipmentModelPeer::ID);
			$selectCriteria->add(EquipmentModelPeer::ID, $criteria->remove(EquipmentModelPeer::ID), $comparison);

		} else { // $values is EquipmentModel object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the EQUIPMENT_MODEL table.
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
			$affectedRows += BasePeer::doDeleteAll(EquipmentModelPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a EquipmentModel or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or EquipmentModel object or primary key or array of primary keys
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
			$con = Propel::getConnection(EquipmentModelPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof EquipmentModel) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(EquipmentModelPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given EquipmentModel object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      EquipmentModel $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(EquipmentModel $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(EquipmentModelPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(EquipmentModelPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID))
			$columns[EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID] = $obj->getAdditionalSpecFileId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::ADDITIONAL_SPEC_PAGE_COUNT))
			$columns[EquipmentModelPeer::ADDITIONAL_SPEC_PAGE_COUNT] = $obj->getAdditionalSpecPageCount();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID))
			$columns[EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID] = $obj->getDesignConsiderationFileId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::DESIGN_PAGE_COUNT))
			$columns[EquipmentModelPeer::DESIGN_PAGE_COUNT] = $obj->getDesignConsiderationPageCount();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::EQUIPMENT_CLASS_ID))
			$columns[EquipmentModelPeer::EQUIPMENT_CLASS_ID] = $obj->getEquipmentClassId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::INTERFACE_DOC_FILE_ID))
			$columns[EquipmentModelPeer::INTERFACE_DOC_FILE_ID] = $obj->getInterfaceDocFileId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::INTERFACE_DOC_PAGE_COUNT))
			$columns[EquipmentModelPeer::INTERFACE_DOC_PAGE_COUNT] = $obj->getInterfaceDocPageCount();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::MANUFACTURER))
			$columns[EquipmentModelPeer::MANUFACTURER] = $obj->getManufacturer();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID))
			$columns[EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID] = $obj->getManufacturerDocFileId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::MANUFACTURER_DOC_PAGE_COUNT))
			$columns[EquipmentModelPeer::MANUFACTURER_DOC_PAGE_COUNT] = $obj->getManufacturerDocPageCount();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::MODEL_NUMBER))
			$columns[EquipmentModelPeer::MODEL_NUMBER] = $obj->getModelNumber();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::NAME))
			$columns[EquipmentModelPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID))
			$columns[EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID] = $obj->getSubcomponentsDocFileId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::SUBCOMPONENTS_DOC_PAGE_COUNT))
			$columns[EquipmentModelPeer::SUBCOMPONENTS_DOC_PAGE_COUNT] = $obj->getSubcomponentsDocPageCount();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentModelPeer::SUPPLIER))
			$columns[EquipmentModelPeer::SUPPLIER] = $obj->getSupplier();

		}

		return BasePeer::doValidate(EquipmentModelPeer::DATABASE_NAME, EquipmentModelPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     EquipmentModel
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(EquipmentModelPeer::DATABASE_NAME);

		$criteria->add(EquipmentModelPeer::ID, $pk);


		$v = EquipmentModelPeer::doSelect($criteria, $con);

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
			$criteria->add(EquipmentModelPeer::ID, $pks, Criteria::IN);
			$objs = EquipmentModelPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseEquipmentModelPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseEquipmentModelPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/EquipmentModelMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.EquipmentModelMapBuilder');
}
