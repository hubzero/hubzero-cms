<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by EquipmentAttributeValuePeer::getOMClass()
include_once 'lib/data/EquipmentAttributeValue.php';

/**
 * Base static class for performing query and update operations on the 'EQUIPMENT_ATTRIBUTE_VALUE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEquipmentAttributeValuePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'EQUIPMENT_ATTRIBUTE_VALUE';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.EquipmentAttributeValue';

	/** The total number of columns. */
	const NUM_COLUMNS = 7;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'EQUIPMENT_ATTRIBUTE_VALUE.ID';

	/** the column name for the EQUIPMENT_ATTRIBUTE_CLASS_ID field */
	const EQUIPMENT_ATTRIBUTE_CLASS_ID = 'EQUIPMENT_ATTRIBUTE_VALUE.EQUIPMENT_ATTRIBUTE_CLASS_ID';

	/** the column name for the EQUIPMENT_ATTRIBUTE_ID field */
	const EQUIPMENT_ATTRIBUTE_ID = 'EQUIPMENT_ATTRIBUTE_VALUE.EQUIPMENT_ATTRIBUTE_ID';

	/** the column name for the EQUIPMENT_ID field */
	const EQUIPMENT_ID = 'EQUIPMENT_ATTRIBUTE_VALUE.EQUIPMENT_ID';

	/** the column name for the NOTE field */
	const NOTE = 'EQUIPMENT_ATTRIBUTE_VALUE.NOTE';

	/** the column name for the UNIT_ID field */
	const UNIT_ID = 'EQUIPMENT_ATTRIBUTE_VALUE.UNIT_ID';

	/** the column name for the VALUE field */
	const VALUE = 'EQUIPMENT_ATTRIBUTE_VALUE.VALUE';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'EquipmentAttributeClassId', 'EquipmentAttributeId', 'EquipmentId', 'Note', 'UnitId', 'Value', ),
		BasePeer::TYPE_COLNAME => array (EquipmentAttributeValuePeer::ID, EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, EquipmentAttributeValuePeer::EQUIPMENT_ID, EquipmentAttributeValuePeer::NOTE, EquipmentAttributeValuePeer::UNIT_ID, EquipmentAttributeValuePeer::VALUE, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'EQUIPMENT_ATTRIBUTE_CLASS_ID', 'EQUIPMENT_ATTRIBUTE_ID', 'EQUIPMENT_ID', 'NOTE', 'UNIT_ID', 'VALUE', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'EquipmentAttributeClassId' => 1, 'EquipmentAttributeId' => 2, 'EquipmentId' => 3, 'Note' => 4, 'UnitId' => 5, 'Value' => 6, ),
		BasePeer::TYPE_COLNAME => array (EquipmentAttributeValuePeer::ID => 0, EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID => 1, EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID => 2, EquipmentAttributeValuePeer::EQUIPMENT_ID => 3, EquipmentAttributeValuePeer::NOTE => 4, EquipmentAttributeValuePeer::UNIT_ID => 5, EquipmentAttributeValuePeer::VALUE => 6, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'EQUIPMENT_ATTRIBUTE_CLASS_ID' => 1, 'EQUIPMENT_ATTRIBUTE_ID' => 2, 'EQUIPMENT_ID' => 3, 'NOTE' => 4, 'UNIT_ID' => 5, 'VALUE' => 6, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/EquipmentAttributeValueMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.EquipmentAttributeValueMapBuilder');
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
			$map = EquipmentAttributeValuePeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. EquipmentAttributeValuePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(EquipmentAttributeValuePeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(EquipmentAttributeValuePeer::ID);

		$criteria->addSelectColumn(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID);

		$criteria->addSelectColumn(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID);

		$criteria->addSelectColumn(EquipmentAttributeValuePeer::EQUIPMENT_ID);

		$criteria->addSelectColumn(EquipmentAttributeValuePeer::NOTE);

		$criteria->addSelectColumn(EquipmentAttributeValuePeer::UNIT_ID);

		$criteria->addSelectColumn(EquipmentAttributeValuePeer::VALUE);

	}

	const COUNT = 'COUNT(EQUIPMENT_ATTRIBUTE_VALUE.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT EQUIPMENT_ATTRIBUTE_VALUE.ID)';

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
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = EquipmentAttributeValuePeer::doSelectRS($criteria, $con);
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
	 * @return     EquipmentAttributeValue
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = EquipmentAttributeValuePeer::doSelect($critcopy, $con);
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
		return EquipmentAttributeValuePeer::populateObjects(EquipmentAttributeValuePeer::doSelectRS($criteria, $con));
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
			EquipmentAttributeValuePeer::addSelectColumns($criteria);
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
		$cls = EquipmentAttributeValuePeer::getOMClass();
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
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$rs = EquipmentAttributeValuePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related EquipmentAttribute table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEquipmentAttribute(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, EquipmentAttributePeer::ID);

		$rs = EquipmentAttributeValuePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related EquipmentAttributeClass table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEquipmentAttributeClass(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, EquipmentAttributeClassPeer::ID);

		$rs = EquipmentAttributeValuePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Unit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentAttributeValuePeer::UNIT_ID, UnitPeer::UNIT_ID);

		$rs = EquipmentAttributeValuePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of EquipmentAttributeValue objects pre-filled with their Equipment objects.
	 *
	 * @return     array Array of EquipmentAttributeValue objects.
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

		EquipmentAttributeValuePeer::addSelectColumns($c);
		$startcol = (EquipmentAttributeValuePeer::NUM_COLUMNS - EquipmentAttributeValuePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		EquipmentPeer::addSelectColumns($c);

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentAttributeValuePeer::getOMClass();

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
					$temp_obj2->addEquipmentAttributeValue($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentAttributeValues();
				$obj2->addEquipmentAttributeValue($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentAttributeValue objects pre-filled with their EquipmentAttribute objects.
	 *
	 * @return     array Array of EquipmentAttributeValue objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEquipmentAttribute(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentAttributeValuePeer::addSelectColumns($c);
		$startcol = (EquipmentAttributeValuePeer::NUM_COLUMNS - EquipmentAttributeValuePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		EquipmentAttributePeer::addSelectColumns($c);

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, EquipmentAttributePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentAttributeValuePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentAttributePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getEquipmentAttribute(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addEquipmentAttributeValue($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentAttributeValues();
				$obj2->addEquipmentAttributeValue($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentAttributeValue objects pre-filled with their EquipmentAttributeClass objects.
	 *
	 * @return     array Array of EquipmentAttributeValue objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEquipmentAttributeClass(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentAttributeValuePeer::addSelectColumns($c);
		$startcol = (EquipmentAttributeValuePeer::NUM_COLUMNS - EquipmentAttributeValuePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		EquipmentAttributeClassPeer::addSelectColumns($c);

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, EquipmentAttributeClassPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentAttributeValuePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentAttributeClassPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getEquipmentAttributeClass(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addEquipmentAttributeValue($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentAttributeValues();
				$obj2->addEquipmentAttributeValue($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentAttributeValue objects pre-filled with their Unit objects.
	 *
	 * @return     array Array of EquipmentAttributeValue objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentAttributeValuePeer::addSelectColumns($c);
		$startcol = (EquipmentAttributeValuePeer::NUM_COLUMNS - EquipmentAttributeValuePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		UnitPeer::addSelectColumns($c);

		$c->addJoin(EquipmentAttributeValuePeer::UNIT_ID, UnitPeer::UNIT_ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentAttributeValuePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = UnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getUnit(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addEquipmentAttributeValue($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipmentAttributeValues();
				$obj2->addEquipmentAttributeValue($obj1); //CHECKME
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
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, EquipmentAttributePeer::ID);

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, EquipmentAttributeClassPeer::ID);

		$criteria->addJoin(EquipmentAttributeValuePeer::UNIT_ID, UnitPeer::UNIT_ID);

		$rs = EquipmentAttributeValuePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of EquipmentAttributeValue objects pre-filled with all related objects.
	 *
	 * @return     array Array of EquipmentAttributeValue objects.
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

		EquipmentAttributeValuePeer::addSelectColumns($c);
		$startcol2 = (EquipmentAttributeValuePeer::NUM_COLUMNS - EquipmentAttributeValuePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentPeer::NUM_COLUMNS;

		EquipmentAttributePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + EquipmentAttributePeer::NUM_COLUMNS;

		EquipmentAttributeClassPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + EquipmentAttributeClassPeer::NUM_COLUMNS;

		UnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + UnitPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, EquipmentAttributePeer::ID);

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, EquipmentAttributeClassPeer::ID);

		$c->addJoin(EquipmentAttributeValuePeer::UNIT_ID, UnitPeer::UNIT_ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentAttributeValuePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined Equipment rows
	
			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipment(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentAttributeValue($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentAttributeValues();
				$obj2->addEquipmentAttributeValue($obj1);
			}


				// Add objects for joined EquipmentAttribute rows
	
			$omClass = EquipmentAttributePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getEquipmentAttribute(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addEquipmentAttributeValue($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentAttributeValues();
				$obj3->addEquipmentAttributeValue($obj1);
			}


				// Add objects for joined EquipmentAttributeClass rows
	
			$omClass = EquipmentAttributeClassPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getEquipmentAttributeClass(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addEquipmentAttributeValue($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentAttributeValues();
				$obj4->addEquipmentAttributeValue($obj1);
			}


				// Add objects for joined Unit rows
	
			$omClass = UnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5 = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getUnit(); // CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addEquipmentAttributeValue($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj5->initEquipmentAttributeValues();
				$obj5->addEquipmentAttributeValue($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
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
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, EquipmentAttributePeer::ID);

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, EquipmentAttributeClassPeer::ID);

		$criteria->addJoin(EquipmentAttributeValuePeer::UNIT_ID, UnitPeer::UNIT_ID);

		$rs = EquipmentAttributeValuePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related EquipmentAttribute table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEquipmentAttribute(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, EquipmentAttributeClassPeer::ID);

		$criteria->addJoin(EquipmentAttributeValuePeer::UNIT_ID, UnitPeer::UNIT_ID);

		$rs = EquipmentAttributeValuePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related EquipmentAttributeClass table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEquipmentAttributeClass(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, EquipmentAttributePeer::ID);

		$criteria->addJoin(EquipmentAttributeValuePeer::UNIT_ID, UnitPeer::UNIT_ID);

		$rs = EquipmentAttributeValuePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Unit table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptUnit(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentAttributeValuePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, EquipmentAttributePeer::ID);

		$criteria->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, EquipmentAttributeClassPeer::ID);

		$rs = EquipmentAttributeValuePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of EquipmentAttributeValue objects pre-filled with all related objects except Equipment.
	 *
	 * @return     array Array of EquipmentAttributeValue objects.
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

		EquipmentAttributeValuePeer::addSelectColumns($c);
		$startcol2 = (EquipmentAttributeValuePeer::NUM_COLUMNS - EquipmentAttributeValuePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentAttributePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentAttributePeer::NUM_COLUMNS;

		EquipmentAttributeClassPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + EquipmentAttributeClassPeer::NUM_COLUMNS;

		UnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + UnitPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, EquipmentAttributePeer::ID);

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, EquipmentAttributeClassPeer::ID);

		$c->addJoin(EquipmentAttributeValuePeer::UNIT_ID, UnitPeer::UNIT_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentAttributeValuePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentAttributePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipmentAttribute(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentAttributeValues();
				$obj2->addEquipmentAttributeValue($obj1);
			}

			$omClass = EquipmentAttributeClassPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getEquipmentAttributeClass(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentAttributeValues();
				$obj3->addEquipmentAttributeValue($obj1);
			}

			$omClass = UnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getUnit(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentAttributeValues();
				$obj4->addEquipmentAttributeValue($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentAttributeValue objects pre-filled with all related objects except EquipmentAttribute.
	 *
	 * @return     array Array of EquipmentAttributeValue objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEquipmentAttribute(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentAttributeValuePeer::addSelectColumns($c);
		$startcol2 = (EquipmentAttributeValuePeer::NUM_COLUMNS - EquipmentAttributeValuePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentPeer::NUM_COLUMNS;

		EquipmentAttributeClassPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + EquipmentAttributeClassPeer::NUM_COLUMNS;

		UnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + UnitPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, EquipmentAttributeClassPeer::ID);

		$c->addJoin(EquipmentAttributeValuePeer::UNIT_ID, UnitPeer::UNIT_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentAttributeValuePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentAttributeValues();
				$obj2->addEquipmentAttributeValue($obj1);
			}

			$omClass = EquipmentAttributeClassPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getEquipmentAttributeClass(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentAttributeValues();
				$obj3->addEquipmentAttributeValue($obj1);
			}

			$omClass = UnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getUnit(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentAttributeValues();
				$obj4->addEquipmentAttributeValue($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentAttributeValue objects pre-filled with all related objects except EquipmentAttributeClass.
	 *
	 * @return     array Array of EquipmentAttributeValue objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEquipmentAttributeClass(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentAttributeValuePeer::addSelectColumns($c);
		$startcol2 = (EquipmentAttributeValuePeer::NUM_COLUMNS - EquipmentAttributeValuePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentPeer::NUM_COLUMNS;

		EquipmentAttributePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + EquipmentAttributePeer::NUM_COLUMNS;

		UnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + UnitPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, EquipmentAttributePeer::ID);

		$c->addJoin(EquipmentAttributeValuePeer::UNIT_ID, UnitPeer::UNIT_ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentAttributeValuePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentAttributeValues();
				$obj2->addEquipmentAttributeValue($obj1);
			}

			$omClass = EquipmentAttributePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getEquipmentAttribute(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentAttributeValues();
				$obj3->addEquipmentAttributeValue($obj1);
			}

			$omClass = UnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getUnit(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentAttributeValues();
				$obj4->addEquipmentAttributeValue($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of EquipmentAttributeValue objects pre-filled with all related objects except Unit.
	 *
	 * @return     array Array of EquipmentAttributeValue objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptUnit(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentAttributeValuePeer::addSelectColumns($c);
		$startcol2 = (EquipmentAttributeValuePeer::NUM_COLUMNS - EquipmentAttributeValuePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentPeer::NUM_COLUMNS;

		EquipmentAttributePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + EquipmentAttributePeer::NUM_COLUMNS;

		EquipmentAttributeClassPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + EquipmentAttributeClassPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ID, EquipmentPeer::EQUIPMENT_ID);

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID, EquipmentAttributePeer::ID);

		$c->addJoin(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID, EquipmentAttributeClassPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentAttributeValuePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipment(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipmentAttributeValues();
				$obj2->addEquipmentAttributeValue($obj1);
			}

			$omClass = EquipmentAttributePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getEquipmentAttribute(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipmentAttributeValues();
				$obj3->addEquipmentAttributeValue($obj1);
			}

			$omClass = EquipmentAttributeClassPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getEquipmentAttributeClass(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addEquipmentAttributeValue($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initEquipmentAttributeValues();
				$obj4->addEquipmentAttributeValue($obj1);
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
		return EquipmentAttributeValuePeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a EquipmentAttributeValue or Criteria object.
	 *
	 * @param      mixed $values Criteria or EquipmentAttributeValue object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from EquipmentAttributeValue object
		}

		$criteria->remove(EquipmentAttributeValuePeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a EquipmentAttributeValue or Criteria object.
	 *
	 * @param      mixed $values Criteria or EquipmentAttributeValue object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(EquipmentAttributeValuePeer::ID);
			$selectCriteria->add(EquipmentAttributeValuePeer::ID, $criteria->remove(EquipmentAttributeValuePeer::ID), $comparison);

		} else { // $values is EquipmentAttributeValue object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the EQUIPMENT_ATTRIBUTE_VALUE table.
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
			$affectedRows += BasePeer::doDeleteAll(EquipmentAttributeValuePeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a EquipmentAttributeValue or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or EquipmentAttributeValue object or primary key or array of primary keys
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
			$con = Propel::getConnection(EquipmentAttributeValuePeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof EquipmentAttributeValue) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(EquipmentAttributeValuePeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given EquipmentAttributeValue object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      EquipmentAttributeValue $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(EquipmentAttributeValue $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(EquipmentAttributeValuePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(EquipmentAttributeValuePeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID))
			$columns[EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_CLASS_ID] = $obj->getEquipmentAttributeClassId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID))
			$columns[EquipmentAttributeValuePeer::EQUIPMENT_ATTRIBUTE_ID] = $obj->getEquipmentAttributeId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentAttributeValuePeer::EQUIPMENT_ID))
			$columns[EquipmentAttributeValuePeer::EQUIPMENT_ID] = $obj->getEquipmentId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentAttributeValuePeer::NOTE))
			$columns[EquipmentAttributeValuePeer::NOTE] = $obj->getNote();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentAttributeValuePeer::UNIT_ID))
			$columns[EquipmentAttributeValuePeer::UNIT_ID] = $obj->getUnitId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentAttributeValuePeer::VALUE))
			$columns[EquipmentAttributeValuePeer::VALUE] = $obj->getValue();

		}

		return BasePeer::doValidate(EquipmentAttributeValuePeer::DATABASE_NAME, EquipmentAttributeValuePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     EquipmentAttributeValue
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(EquipmentAttributeValuePeer::DATABASE_NAME);

		$criteria->add(EquipmentAttributeValuePeer::ID, $pk);


		$v = EquipmentAttributeValuePeer::doSelect($criteria, $con);

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
			$criteria->add(EquipmentAttributeValuePeer::ID, $pks, Criteria::IN);
			$objs = EquipmentAttributeValuePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseEquipmentAttributeValuePeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseEquipmentAttributeValuePeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/EquipmentAttributeValueMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.EquipmentAttributeValueMapBuilder');
}
