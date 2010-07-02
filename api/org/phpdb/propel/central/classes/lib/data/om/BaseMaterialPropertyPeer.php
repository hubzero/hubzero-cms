<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by MaterialPropertyPeer::getOMClass()
include_once 'lib/data/MaterialProperty.php';

/**
 * Base static class for performing query and update operations on the 'MATERIAL_PROPERTY' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseMaterialPropertyPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'MATERIAL_PROPERTY';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.MaterialProperty';

	/** The total number of columns. */
	const NUM_COLUMNS = 5;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'MATERIAL_PROPERTY.ID';

	/** the column name for the MATERIAL_ID field */
	const MATERIAL_ID = 'MATERIAL_PROPERTY.MATERIAL_ID';

	/** the column name for the MATERIAL_TYPE_PROPERTY_ID field */
	const MATERIAL_TYPE_PROPERTY_ID = 'MATERIAL_PROPERTY.MATERIAL_TYPE_PROPERTY_ID';

	/** the column name for the MEASUREMENT_UNIT_ID field */
	const MEASUREMENT_UNIT_ID = 'MATERIAL_PROPERTY.MEASUREMENT_UNIT_ID';

	/** the column name for the VALUE field */
	const VALUE = 'MATERIAL_PROPERTY.VALUE';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'MaterialId', 'MaterialTypePropertyId', 'MeasurementUnitId', 'Value', ),
		BasePeer::TYPE_COLNAME => array (MaterialPropertyPeer::ID, MaterialPropertyPeer::MATERIAL_ID, MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, MaterialPropertyPeer::MEASUREMENT_UNIT_ID, MaterialPropertyPeer::VALUE, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'MATERIAL_ID', 'MATERIAL_TYPE_PROPERTY_ID', 'MEASUREMENT_UNIT_ID', 'VALUE', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'MaterialId' => 1, 'MaterialTypePropertyId' => 2, 'MeasurementUnitId' => 3, 'Value' => 4, ),
		BasePeer::TYPE_COLNAME => array (MaterialPropertyPeer::ID => 0, MaterialPropertyPeer::MATERIAL_ID => 1, MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID => 2, MaterialPropertyPeer::MEASUREMENT_UNIT_ID => 3, MaterialPropertyPeer::VALUE => 4, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'MATERIAL_ID' => 1, 'MATERIAL_TYPE_PROPERTY_ID' => 2, 'MEASUREMENT_UNIT_ID' => 3, 'VALUE' => 4, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/MaterialPropertyMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.MaterialPropertyMapBuilder');
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
			$map = MaterialPropertyPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. MaterialPropertyPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(MaterialPropertyPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(MaterialPropertyPeer::ID);

		$criteria->addSelectColumn(MaterialPropertyPeer::MATERIAL_ID);

		$criteria->addSelectColumn(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID);

		$criteria->addSelectColumn(MaterialPropertyPeer::MEASUREMENT_UNIT_ID);

		$criteria->addSelectColumn(MaterialPropertyPeer::VALUE);

	}

	const COUNT = 'COUNT(MATERIAL_PROPERTY.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT MATERIAL_PROPERTY.ID)';

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
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = MaterialPropertyPeer::doSelectRS($criteria, $con);
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
	 * @return     MaterialProperty
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = MaterialPropertyPeer::doSelect($critcopy, $con);
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
		return MaterialPropertyPeer::populateObjects(MaterialPropertyPeer::doSelectRS($criteria, $con));
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
			MaterialPropertyPeer::addSelectColumns($criteria);
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
		$cls = MaterialPropertyPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related Material table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMaterial(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialPropertyPeer::MATERIAL_ID, MaterialPeer::ID);

		$rs = MaterialPropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MaterialTypeProperty table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMaterialTypeProperty(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, MaterialTypePropertyPeer::ID);

		$rs = MaterialPropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = MaterialPropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of MaterialProperty objects pre-filled with their Material objects.
	 *
	 * @return     array Array of MaterialProperty objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMaterial(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		MaterialPropertyPeer::addSelectColumns($c);
		$startcol = (MaterialPropertyPeer::NUM_COLUMNS - MaterialPropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MaterialPeer::addSelectColumns($c);

		$c->addJoin(MaterialPropertyPeer::MATERIAL_ID, MaterialPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialPropertyPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MaterialPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMaterial(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addMaterialProperty($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initMaterialPropertys();
				$obj2->addMaterialProperty($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of MaterialProperty objects pre-filled with their MaterialTypeProperty objects.
	 *
	 * @return     array Array of MaterialProperty objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMaterialTypeProperty(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		MaterialPropertyPeer::addSelectColumns($c);
		$startcol = (MaterialPropertyPeer::NUM_COLUMNS - MaterialPropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MaterialTypePropertyPeer::addSelectColumns($c);

		$c->addJoin(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, MaterialTypePropertyPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialPropertyPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MaterialTypePropertyPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMaterialTypeProperty(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addMaterialProperty($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initMaterialPropertys();
				$obj2->addMaterialProperty($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of MaterialProperty objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of MaterialProperty objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		MaterialPropertyPeer::addSelectColumns($c);
		$startcol = (MaterialPropertyPeer::NUM_COLUMNS - MaterialPropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialPropertyPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnit(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addMaterialProperty($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initMaterialPropertys();
				$obj2->addMaterialProperty($obj1); //CHECKME
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
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialPropertyPeer::MATERIAL_ID, MaterialPeer::ID);

		$criteria->addJoin(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, MaterialTypePropertyPeer::ID);

		$criteria->addJoin(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = MaterialPropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of MaterialProperty objects pre-filled with all related objects.
	 *
	 * @return     array Array of MaterialProperty objects.
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

		MaterialPropertyPeer::addSelectColumns($c);
		$startcol2 = (MaterialPropertyPeer::NUM_COLUMNS - MaterialPropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		MaterialPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + MaterialPeer::NUM_COLUMNS;

		MaterialTypePropertyPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MaterialTypePropertyPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(MaterialPropertyPeer::MATERIAL_ID, MaterialPeer::ID);

		$c->addJoin(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, MaterialTypePropertyPeer::ID);

		$c->addJoin(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialPropertyPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined Material rows
	
			$omClass = MaterialPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getMaterial(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addMaterialProperty($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initMaterialPropertys();
				$obj2->addMaterialProperty($obj1);
			}


				// Add objects for joined MaterialTypeProperty rows
	
			$omClass = MaterialTypePropertyPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMaterialTypeProperty(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addMaterialProperty($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initMaterialPropertys();
				$obj3->addMaterialProperty($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnit(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addMaterialProperty($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initMaterialPropertys();
				$obj4->addMaterialProperty($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Material table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMaterial(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, MaterialTypePropertyPeer::ID);

		$criteria->addJoin(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = MaterialPropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MaterialTypeProperty table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMaterialTypeProperty(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialPropertyPeer::MATERIAL_ID, MaterialPeer::ID);

		$criteria->addJoin(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, MeasurementUnitPeer::ID);

		$rs = MaterialPropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(MaterialPropertyPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(MaterialPropertyPeer::MATERIAL_ID, MaterialPeer::ID);

		$criteria->addJoin(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, MaterialTypePropertyPeer::ID);

		$rs = MaterialPropertyPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of MaterialProperty objects pre-filled with all related objects except Material.
	 *
	 * @return     array Array of MaterialProperty objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMaterial(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		MaterialPropertyPeer::addSelectColumns($c);
		$startcol2 = (MaterialPropertyPeer::NUM_COLUMNS - MaterialPropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		MaterialTypePropertyPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + MaterialTypePropertyPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, MaterialTypePropertyPeer::ID);

		$c->addJoin(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, MeasurementUnitPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialPropertyPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MaterialTypePropertyPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getMaterialTypeProperty(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addMaterialProperty($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initMaterialPropertys();
				$obj2->addMaterialProperty($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMeasurementUnit(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addMaterialProperty($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initMaterialPropertys();
				$obj3->addMaterialProperty($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of MaterialProperty objects pre-filled with all related objects except MaterialTypeProperty.
	 *
	 * @return     array Array of MaterialProperty objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMaterialTypeProperty(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		MaterialPropertyPeer::addSelectColumns($c);
		$startcol2 = (MaterialPropertyPeer::NUM_COLUMNS - MaterialPropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		MaterialPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + MaterialPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(MaterialPropertyPeer::MATERIAL_ID, MaterialPeer::ID);

		$c->addJoin(MaterialPropertyPeer::MEASUREMENT_UNIT_ID, MeasurementUnitPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialPropertyPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MaterialPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getMaterial(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addMaterialProperty($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initMaterialPropertys();
				$obj2->addMaterialProperty($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMeasurementUnit(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addMaterialProperty($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initMaterialPropertys();
				$obj3->addMaterialProperty($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of MaterialProperty objects pre-filled with all related objects except MeasurementUnit.
	 *
	 * @return     array Array of MaterialProperty objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		MaterialPropertyPeer::addSelectColumns($c);
		$startcol2 = (MaterialPropertyPeer::NUM_COLUMNS - MaterialPropertyPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		MaterialPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + MaterialPeer::NUM_COLUMNS;

		MaterialTypePropertyPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MaterialTypePropertyPeer::NUM_COLUMNS;

		$c->addJoin(MaterialPropertyPeer::MATERIAL_ID, MaterialPeer::ID);

		$c->addJoin(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID, MaterialTypePropertyPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = MaterialPropertyPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MaterialPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getMaterial(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addMaterialProperty($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initMaterialPropertys();
				$obj2->addMaterialProperty($obj1);
			}

			$omClass = MaterialTypePropertyPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMaterialTypeProperty(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addMaterialProperty($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initMaterialPropertys();
				$obj3->addMaterialProperty($obj1);
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
		return MaterialPropertyPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a MaterialProperty or Criteria object.
	 *
	 * @param      mixed $values Criteria or MaterialProperty object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from MaterialProperty object
		}

		$criteria->remove(MaterialPropertyPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a MaterialProperty or Criteria object.
	 *
	 * @param      mixed $values Criteria or MaterialProperty object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(MaterialPropertyPeer::ID);
			$selectCriteria->add(MaterialPropertyPeer::ID, $criteria->remove(MaterialPropertyPeer::ID), $comparison);

		} else { // $values is MaterialProperty object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the MATERIAL_PROPERTY table.
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
			$affectedRows += BasePeer::doDeleteAll(MaterialPropertyPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a MaterialProperty or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or MaterialProperty object or primary key or array of primary keys
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
			$con = Propel::getConnection(MaterialPropertyPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof MaterialProperty) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(MaterialPropertyPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given MaterialProperty object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      MaterialProperty $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(MaterialProperty $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(MaterialPropertyPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(MaterialPropertyPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(MaterialPropertyPeer::MATERIAL_ID))
			$columns[MaterialPropertyPeer::MATERIAL_ID] = $obj->getMaterialId();

		if ($obj->isNew() || $obj->isColumnModified(MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID))
			$columns[MaterialPropertyPeer::MATERIAL_TYPE_PROPERTY_ID] = $obj->getMaterialTypePropertyId();

		if ($obj->isNew() || $obj->isColumnModified(MaterialPropertyPeer::MEASUREMENT_UNIT_ID))
			$columns[MaterialPropertyPeer::MEASUREMENT_UNIT_ID] = $obj->getMeasurementUnitId();

		if ($obj->isNew() || $obj->isColumnModified(MaterialPropertyPeer::VALUE))
			$columns[MaterialPropertyPeer::VALUE] = $obj->getValue();

		}

		return BasePeer::doValidate(MaterialPropertyPeer::DATABASE_NAME, MaterialPropertyPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     MaterialProperty
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(MaterialPropertyPeer::DATABASE_NAME);

		$criteria->add(MaterialPropertyPeer::ID, $pk);


		$v = MaterialPropertyPeer::doSelect($criteria, $con);

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
			$criteria->add(MaterialPropertyPeer::ID, $pks, Criteria::IN);
			$objs = MaterialPropertyPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseMaterialPropertyPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseMaterialPropertyPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/MaterialPropertyMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.MaterialPropertyMapBuilder');
}
