<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by MaterialTypePropertyPeer::getOMClass()
include_once 'lib/data/MaterialTypeProperty.php';

/**
 * Base static class for performing query and update operations on the 'MATERIAL_TYPE_PROPERTY' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseMaterialTypePropertyPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'MATERIAL_TYPE_PROPERTY';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.MaterialTypeProperty';

	/** The total number of columns. */
	const NUM_COLUMNS = 9;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'MATERIAL_TYPE_PROPERTY.ID';

	/** the column name for the DATATYPE field */
	const DATATYPE = 'MATERIAL_TYPE_PROPERTY.DATATYPE';

	/** the column name for the DISPLAY_NAME field */
	const DISPLAY_NAME = 'MATERIAL_TYPE_PROPERTY.DISPLAY_NAME';

	/** the column name for the MATERIAL_TYPE_ID field */
	const MATERIAL_TYPE_ID = 'MATERIAL_TYPE_PROPERTY.MATERIAL_TYPE_ID';

	/** the column name for the MEASUREMENT_UNIT_CATEGORY_ID field */
	const MEASUREMENT_UNIT_CATEGORY_ID = 'MATERIAL_TYPE_PROPERTY.MEASUREMENT_UNIT_CATEGORY_ID';

	/** the column name for the OPTIONS field */
	const OPTIONS = 'MATERIAL_TYPE_PROPERTY.OPTIONS';

	/** the column name for the REQUIRED field */
	const REQUIRED = 'MATERIAL_TYPE_PROPERTY.REQUIRED';

	/** the column name for the STATUS field */
	const STATUS = 'MATERIAL_TYPE_PROPERTY.STATUS';

	/** the column name for the UNITS field */
	const UNITS = 'MATERIAL_TYPE_PROPERTY.UNITS';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'DataType', 'DisplayName', 'MaterialTypeId', 'MeasurementUnitCategoryId', 'Options', 'Required', 'Status', 'Units', ),
		BasePeer::TYPE_COLNAME => array (MaterialTypePropertyPeer::ID, MaterialTypePropertyPeer::DATATYPE, MaterialTypePropertyPeer::DISPLAY_NAME, MaterialTypePropertyPeer::MATERIAL_TYPE_ID, MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, MaterialTypePropertyPeer::OPTIONS, MaterialTypePropertyPeer::REQUIRED, MaterialTypePropertyPeer::STATUS, MaterialTypePropertyPeer::UNITS, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'DATATYPE', 'DISPLAY_NAME', 'MATERIAL_TYPE_ID', 'MEASUREMENT_UNIT_CATEGORY_ID', 'OPTIONS', 'REQUIRED', 'STATUS', 'UNITS', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'DataType' => 1, 'DisplayName' => 2, 'MaterialTypeId' => 3, 'MeasurementUnitCategoryId' => 4, 'Options' => 5, 'Required' => 6, 'Status' => 7, 'Units' => 8, ),
		BasePeer::TYPE_COLNAME => array (MaterialTypePropertyPeer::ID => 0, MaterialTypePropertyPeer::DATATYPE => 1, MaterialTypePropertyPeer::DISPLAY_NAME => 2, MaterialTypePropertyPeer::MATERIAL_TYPE_ID => 3, MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID => 4, MaterialTypePropertyPeer::OPTIONS => 5, MaterialTypePropertyPeer::REQUIRED => 6, MaterialTypePropertyPeer::STATUS => 7, MaterialTypePropertyPeer::UNITS => 8, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'DATATYPE' => 1, 'DISPLAY_NAME' => 2, 'MATERIAL_TYPE_ID' => 3, 'MEASUREMENT_UNIT_CATEGORY_ID' => 4, 'OPTIONS' => 5, 'REQUIRED' => 6, 'STATUS' => 7, 'UNITS' => 8, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/MaterialTypePropertyMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.MaterialTypePropertyMapBuilder');
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
			$map = MaterialTypePropertyPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. MaterialTypePropertyPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(MaterialTypePropertyPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(MaterialTypePropertyPeer::ID);

		$criteria->addSelectColumn(MaterialTypePropertyPeer::DATATYPE);

		$criteria->addSelectColumn(MaterialTypePropertyPeer::DISPLAY_NAME);

		$criteria->addSelectColumn(MaterialTypePropertyPeer::MATERIAL_TYPE_ID);

		$criteria->addSelectColumn(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID);

		$criteria->addSelectColumn(MaterialTypePropertyPeer::OPTIONS);

		$criteria->addSelectColumn(MaterialTypePropertyPeer::REQUIRED);

		$criteria->addSelectColumn(MaterialTypePropertyPeer::STATUS);

		$criteria->addSelectColumn(MaterialTypePropertyPeer::UNITS);

	}

	const COUNT = 'COUNT(MATERIAL_TYPE_PROPERTY.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT MATERIAL_TYPE_PROPERTY.ID)';

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
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = MaterialTypePropertyPeer::doSelectRS($criteria, $con);
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
	 * @return     MaterialTypeProperty
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = MaterialTypePropertyPeer::doSelect($critcopy, $con);
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
		return MaterialTypePropertyPeer::populateObjects(MaterialTypePropertyPeer::doSelectRS($criteria, $con));
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
			MaterialTypePropertyPeer::addSelectColumns($criteria);
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
		$cls = MaterialTypePropertyPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related MaterialType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMaterialType(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialTypePropertyPeer::MATERIAL_TYPE_ID, MaterialTypePeer::ID);

		$rs = MaterialTypePropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitCategory table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitCategory(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, MeasurementUnitCategoryPeer::ID);

		$rs = MaterialTypePropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of MaterialTypeProperty objects pre-filled with their MaterialType objects.
	 *
	 * @return     array Array of MaterialTypeProperty objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMaterialType(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		MaterialTypePropertyPeer::addSelectColumns($c);
		$startcol = (MaterialTypePropertyPeer::NUM_COLUMNS - MaterialTypePropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MaterialTypePeer::addSelectColumns($c);

		$c->addJoin(MaterialTypePropertyPeer::MATERIAL_TYPE_ID, MaterialTypePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialTypePropertyPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MaterialTypePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMaterialType(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addMaterialTypeProperty($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initMaterialTypePropertys();
				$obj2->addMaterialTypeProperty($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of MaterialTypeProperty objects pre-filled with their MeasurementUnitCategory objects.
	 *
	 * @return     array Array of MaterialTypeProperty objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitCategory(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		MaterialTypePropertyPeer::addSelectColumns($c);
		$startcol = (MaterialTypePropertyPeer::NUM_COLUMNS - MaterialTypePropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitCategoryPeer::addSelectColumns($c);

		$c->addJoin(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, MeasurementUnitCategoryPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialTypePropertyPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitCategoryPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitCategory(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addMaterialTypeProperty($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initMaterialTypePropertys();
				$obj2->addMaterialTypeProperty($obj1); //CHECKME
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
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialTypePropertyPeer::MATERIAL_TYPE_ID, MaterialTypePeer::ID);

		$criteria->addJoin(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, MeasurementUnitCategoryPeer::ID);

		$rs = MaterialTypePropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of MaterialTypeProperty objects pre-filled with all related objects.
	 *
	 * @return     array Array of MaterialTypeProperty objects.
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

		MaterialTypePropertyPeer::addSelectColumns($c);
		$startcol2 = (MaterialTypePropertyPeer::NUM_COLUMNS - MaterialTypePropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		MaterialTypePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + MaterialTypePeer::NUM_COLUMNS;

		MeasurementUnitCategoryPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitCategoryPeer::NUM_COLUMNS;

		$c->addJoin(MaterialTypePropertyPeer::MATERIAL_TYPE_ID, MaterialTypePeer::ID);

		$c->addJoin(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, MeasurementUnitCategoryPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialTypePropertyPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined MaterialType rows
	
			$omClass = MaterialTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getMaterialType(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addMaterialTypeProperty($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initMaterialTypePropertys();
				$obj2->addMaterialTypeProperty($obj1);
			}


				// Add objects for joined MeasurementUnitCategory rows
	
			$omClass = MeasurementUnitCategoryPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMeasurementUnitCategory(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addMaterialTypeProperty($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initMaterialTypePropertys();
				$obj3->addMaterialTypeProperty($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MaterialType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMaterialType(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, MeasurementUnitCategoryPeer::ID);

		$rs = MaterialTypePropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitCategory table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitCategory(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialTypePropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialTypePropertyPeer::MATERIAL_TYPE_ID, MaterialTypePeer::ID);

		$rs = MaterialTypePropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of MaterialTypeProperty objects pre-filled with all related objects except MaterialType.
	 *
	 * @return     array Array of MaterialTypeProperty objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMaterialType(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		MaterialTypePropertyPeer::addSelectColumns($c);
		$startcol2 = (MaterialTypePropertyPeer::NUM_COLUMNS - MaterialTypePropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		MeasurementUnitCategoryPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + MeasurementUnitCategoryPeer::NUM_COLUMNS;

		$c->addJoin(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID, MeasurementUnitCategoryPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialTypePropertyPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitCategoryPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getMeasurementUnitCategory(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addMaterialTypeProperty($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initMaterialTypePropertys();
				$obj2->addMaterialTypeProperty($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of MaterialTypeProperty objects pre-filled with all related objects except MeasurementUnitCategory.
	 *
	 * @return     array Array of MaterialTypeProperty objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitCategory(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		MaterialTypePropertyPeer::addSelectColumns($c);
		$startcol2 = (MaterialTypePropertyPeer::NUM_COLUMNS - MaterialTypePropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		MaterialTypePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + MaterialTypePeer::NUM_COLUMNS;

		$c->addJoin(MaterialTypePropertyPeer::MATERIAL_TYPE_ID, MaterialTypePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialTypePropertyPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MaterialTypePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getMaterialType(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addMaterialTypeProperty($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initMaterialTypePropertys();
				$obj2->addMaterialTypeProperty($obj1);
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
		return MaterialTypePropertyPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a MaterialTypeProperty or Criteria object.
	 *
	 * @param      mixed $values Criteria or MaterialTypeProperty object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from MaterialTypeProperty object
		}

		$criteria->remove(MaterialTypePropertyPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a MaterialTypeProperty or Criteria object.
	 *
	 * @param      mixed $values Criteria or MaterialTypeProperty object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(MaterialTypePropertyPeer::ID);
			$selectCriteria->add(MaterialTypePropertyPeer::ID, $criteria->remove(MaterialTypePropertyPeer::ID), $comparison);

		} else { // $values is MaterialTypeProperty object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the MATERIAL_TYPE_PROPERTY table.
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
			$affectedRows += BasePeer::doDeleteAll(MaterialTypePropertyPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a MaterialTypeProperty or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or MaterialTypeProperty object or primary key or array of primary keys
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
			$con = Propel::getConnection(MaterialTypePropertyPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof MaterialTypeProperty) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(MaterialTypePropertyPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given MaterialTypeProperty object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      MaterialTypeProperty $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(MaterialTypeProperty $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(MaterialTypePropertyPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(MaterialTypePropertyPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(MaterialTypePropertyPeer::DATATYPE))
			$columns[MaterialTypePropertyPeer::DATATYPE] = $obj->getDataType();

		if ($obj->isNew() || $obj->isColumnModified(MaterialTypePropertyPeer::DISPLAY_NAME))
			$columns[MaterialTypePropertyPeer::DISPLAY_NAME] = $obj->getDisplayName();

		if ($obj->isNew() || $obj->isColumnModified(MaterialTypePropertyPeer::MATERIAL_TYPE_ID))
			$columns[MaterialTypePropertyPeer::MATERIAL_TYPE_ID] = $obj->getMaterialTypeId();

		if ($obj->isNew() || $obj->isColumnModified(MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID))
			$columns[MaterialTypePropertyPeer::MEASUREMENT_UNIT_CATEGORY_ID] = $obj->getMeasurementUnitCategoryId();

		if ($obj->isNew() || $obj->isColumnModified(MaterialTypePropertyPeer::OPTIONS))
			$columns[MaterialTypePropertyPeer::OPTIONS] = $obj->getOptions();

		if ($obj->isNew() || $obj->isColumnModified(MaterialTypePropertyPeer::REQUIRED))
			$columns[MaterialTypePropertyPeer::REQUIRED] = $obj->getRequired();

		if ($obj->isNew() || $obj->isColumnModified(MaterialTypePropertyPeer::STATUS))
			$columns[MaterialTypePropertyPeer::STATUS] = $obj->getStatus();

		if ($obj->isNew() || $obj->isColumnModified(MaterialTypePropertyPeer::UNITS))
			$columns[MaterialTypePropertyPeer::UNITS] = $obj->getUnits();

		}

		return BasePeer::doValidate(MaterialTypePropertyPeer::DATABASE_NAME, MaterialTypePropertyPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     MaterialTypeProperty
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(MaterialTypePropertyPeer::DATABASE_NAME);

		$criteria->add(MaterialTypePropertyPeer::ID, $pk);


		$v = MaterialTypePropertyPeer::doSelect($criteria, $con);

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
			$criteria->add(MaterialTypePropertyPeer::ID, $pks, Criteria::IN);
			$objs = MaterialTypePropertyPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseMaterialTypePropertyPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseMaterialTypePropertyPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/MaterialTypePropertyMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.MaterialTypePropertyMapBuilder');
}
