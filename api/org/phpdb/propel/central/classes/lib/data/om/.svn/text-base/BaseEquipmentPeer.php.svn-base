<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by EquipmentPeer::getOMClass()
include_once 'lib/data/Equipment.php';

/**
 * Base static class for performing query and update operations on the 'EQUIPMENT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseEquipmentPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'EQUIPMENT';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.Equipment';

	/** The total number of columns. */
	const NUM_COLUMNS = 16;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the EQUIPMENT_ID field */
	const EQUIPMENT_ID = 'EQUIPMENT.EQUIPMENT_ID';

	/** the column name for the CALIBRATION_INFORMATION field */
	const CALIBRATION_INFORMATION = 'EQUIPMENT.CALIBRATION_INFORMATION';

	/** the column name for the COMMISSION_DATE field */
	const COMMISSION_DATE = 'EQUIPMENT.COMMISSION_DATE';

	/** the column name for the DELETED field */
	const DELETED = 'EQUIPMENT.DELETED';

	/** the column name for the LAB_ASSIGNED_ID field */
	const LAB_ASSIGNED_ID = 'EQUIPMENT.LAB_ASSIGNED_ID';

	/** the column name for the MAJOR field */
	const MAJOR = 'EQUIPMENT.MAJOR';

	/** the column name for the MODEL_ID field */
	const MODEL_ID = 'EQUIPMENT.MODEL_ID';

	/** the column name for the NAME field */
	const NAME = 'EQUIPMENT.NAME';

	/** the column name for the NEES_OPERATED field */
	const NEES_OPERATED = 'EQUIPMENT.NEES_OPERATED';

	/** the column name for the NOTE field */
	const NOTE = 'EQUIPMENT.NOTE';

	/** the column name for the ORGID field */
	const ORGID = 'EQUIPMENT.ORGID';

	/** the column name for the OWNER field */
	const OWNER = 'EQUIPMENT.OWNER';

	/** the column name for the PARENT_ID field */
	const PARENT_ID = 'EQUIPMENT.PARENT_ID';

	/** the column name for the QUANTITY field */
	const QUANTITY = 'EQUIPMENT.QUANTITY';

	/** the column name for the SEPARATE_SCHEDULING field */
	const SEPARATE_SCHEDULING = 'EQUIPMENT.SEPARATE_SCHEDULING';

	/** the column name for the SERIAL_NUMBER field */
	const SERIAL_NUMBER = 'EQUIPMENT.SERIAL_NUMBER';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CalibrationInformation', 'CommissionDate', 'Deleted', 'LabAssignedId', 'Major', 'ModelId', 'Name', 'NeesOperated', 'Note', 'OrganizationId', 'Owner', 'ParentId', 'Quantity', 'SeparateScheduling', 'SerialNumber', ),
		BasePeer::TYPE_COLNAME => array (EquipmentPeer::EQUIPMENT_ID, EquipmentPeer::CALIBRATION_INFORMATION, EquipmentPeer::COMMISSION_DATE, EquipmentPeer::DELETED, EquipmentPeer::LAB_ASSIGNED_ID, EquipmentPeer::MAJOR, EquipmentPeer::MODEL_ID, EquipmentPeer::NAME, EquipmentPeer::NEES_OPERATED, EquipmentPeer::NOTE, EquipmentPeer::ORGID, EquipmentPeer::OWNER, EquipmentPeer::PARENT_ID, EquipmentPeer::QUANTITY, EquipmentPeer::SEPARATE_SCHEDULING, EquipmentPeer::SERIAL_NUMBER, ),
		BasePeer::TYPE_FIELDNAME => array ('EQUIPMENT_ID', 'CALIBRATION_INFORMATION', 'COMMISSION_DATE', 'DELETED', 'LAB_ASSIGNED_ID', 'MAJOR', 'MODEL_ID', 'NAME', 'NEES_OPERATED', 'NOTE', 'ORGID', 'OWNER', 'PARENT_ID', 'QUANTITY', 'SEPARATE_SCHEDULING', 'SERIAL_NUMBER', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CalibrationInformation' => 1, 'CommissionDate' => 2, 'Deleted' => 3, 'LabAssignedId' => 4, 'Major' => 5, 'ModelId' => 6, 'Name' => 7, 'NeesOperated' => 8, 'Note' => 9, 'OrganizationId' => 10, 'Owner' => 11, 'ParentId' => 12, 'Quantity' => 13, 'SeparateScheduling' => 14, 'SerialNumber' => 15, ),
		BasePeer::TYPE_COLNAME => array (EquipmentPeer::EQUIPMENT_ID => 0, EquipmentPeer::CALIBRATION_INFORMATION => 1, EquipmentPeer::COMMISSION_DATE => 2, EquipmentPeer::DELETED => 3, EquipmentPeer::LAB_ASSIGNED_ID => 4, EquipmentPeer::MAJOR => 5, EquipmentPeer::MODEL_ID => 6, EquipmentPeer::NAME => 7, EquipmentPeer::NEES_OPERATED => 8, EquipmentPeer::NOTE => 9, EquipmentPeer::ORGID => 10, EquipmentPeer::OWNER => 11, EquipmentPeer::PARENT_ID => 12, EquipmentPeer::QUANTITY => 13, EquipmentPeer::SEPARATE_SCHEDULING => 14, EquipmentPeer::SERIAL_NUMBER => 15, ),
		BasePeer::TYPE_FIELDNAME => array ('EQUIPMENT_ID' => 0, 'CALIBRATION_INFORMATION' => 1, 'COMMISSION_DATE' => 2, 'DELETED' => 3, 'LAB_ASSIGNED_ID' => 4, 'MAJOR' => 5, 'MODEL_ID' => 6, 'NAME' => 7, 'NEES_OPERATED' => 8, 'NOTE' => 9, 'ORGID' => 10, 'OWNER' => 11, 'PARENT_ID' => 12, 'QUANTITY' => 13, 'SEPARATE_SCHEDULING' => 14, 'SERIAL_NUMBER' => 15, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/EquipmentMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.EquipmentMapBuilder');
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
			$map = EquipmentPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. EquipmentPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(EquipmentPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(EquipmentPeer::EQUIPMENT_ID);

		$criteria->addSelectColumn(EquipmentPeer::CALIBRATION_INFORMATION);

		$criteria->addSelectColumn(EquipmentPeer::COMMISSION_DATE);

		$criteria->addSelectColumn(EquipmentPeer::DELETED);

		$criteria->addSelectColumn(EquipmentPeer::LAB_ASSIGNED_ID);

		$criteria->addSelectColumn(EquipmentPeer::MAJOR);

		$criteria->addSelectColumn(EquipmentPeer::MODEL_ID);

		$criteria->addSelectColumn(EquipmentPeer::NAME);

		$criteria->addSelectColumn(EquipmentPeer::NEES_OPERATED);

		$criteria->addSelectColumn(EquipmentPeer::NOTE);

		$criteria->addSelectColumn(EquipmentPeer::ORGID);

		$criteria->addSelectColumn(EquipmentPeer::OWNER);

		$criteria->addSelectColumn(EquipmentPeer::PARENT_ID);

		$criteria->addSelectColumn(EquipmentPeer::QUANTITY);

		$criteria->addSelectColumn(EquipmentPeer::SEPARATE_SCHEDULING);

		$criteria->addSelectColumn(EquipmentPeer::SERIAL_NUMBER);

	}

	const COUNT = 'COUNT(EQUIPMENT.EQUIPMENT_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT EQUIPMENT.EQUIPMENT_ID)';

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
			$criteria->addSelectColumn(EquipmentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = EquipmentPeer::doSelectRS($criteria, $con);
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
	 * @return     Equipment
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = EquipmentPeer::doSelect($critcopy, $con);
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
		return EquipmentPeer::populateObjects(EquipmentPeer::doSelectRS($criteria, $con));
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
			EquipmentPeer::addSelectColumns($criteria);
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
		$cls = EquipmentPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related EquipmentModel table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEquipmentModel(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentPeer::MODEL_ID, EquipmentModelPeer::ID);

		$rs = EquipmentPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(EquipmentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentPeer::ORGID, OrganizationPeer::ORGID);

		$rs = EquipmentPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Equipment objects pre-filled with their EquipmentModel objects.
	 *
	 * @return     array Array of Equipment objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEquipmentModel(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentPeer::addSelectColumns($c);
		$startcol = (EquipmentPeer::NUM_COLUMNS - EquipmentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		EquipmentModelPeer::addSelectColumns($c);

		$c->addJoin(EquipmentPeer::MODEL_ID, EquipmentModelPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentModelPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getEquipmentModel(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addEquipment($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipments();
				$obj2->addEquipment($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Equipment objects pre-filled with their Organization objects.
	 *
	 * @return     array Array of Equipment objects.
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

		EquipmentPeer::addSelectColumns($c);
		$startcol = (EquipmentPeer::NUM_COLUMNS - EquipmentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		OrganizationPeer::addSelectColumns($c);

		$c->addJoin(EquipmentPeer::ORGID, OrganizationPeer::ORGID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentPeer::getOMClass();

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
					$temp_obj2->addEquipment($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initEquipments();
				$obj2->addEquipment($obj1); //CHECKME
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
			$criteria->addSelectColumn(EquipmentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentPeer::MODEL_ID, EquipmentModelPeer::ID);

		$criteria->addJoin(EquipmentPeer::ORGID, OrganizationPeer::ORGID);

		$rs = EquipmentPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Equipment objects pre-filled with all related objects.
	 *
	 * @return     array Array of Equipment objects.
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

		EquipmentPeer::addSelectColumns($c);
		$startcol2 = (EquipmentPeer::NUM_COLUMNS - EquipmentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentModelPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentModelPeer::NUM_COLUMNS;

		OrganizationPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + OrganizationPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentPeer::MODEL_ID, EquipmentModelPeer::ID);

		$c->addJoin(EquipmentPeer::ORGID, OrganizationPeer::ORGID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined EquipmentModel rows
	
			$omClass = EquipmentModelPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipmentModel(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipment($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipments();
				$obj2->addEquipment($obj1);
			}


				// Add objects for joined Organization rows
	
			$omClass = OrganizationPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getOrganization(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addEquipment($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipments();
				$obj3->addEquipment($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related EquipmentRelatedByParentId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEquipmentRelatedByParentId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentPeer::MODEL_ID, EquipmentModelPeer::ID);

		$criteria->addJoin(EquipmentPeer::ORGID, OrganizationPeer::ORGID);

		$rs = EquipmentPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related EquipmentModel table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEquipmentModel(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(EquipmentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentPeer::ORGID, OrganizationPeer::ORGID);

		$rs = EquipmentPeer::doSelectRS($criteria, $con);
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
			$criteria->addSelectColumn(EquipmentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(EquipmentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(EquipmentPeer::MODEL_ID, EquipmentModelPeer::ID);

		$rs = EquipmentPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Equipment objects pre-filled with all related objects except EquipmentRelatedByParentId.
	 *
	 * @return     array Array of Equipment objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEquipmentRelatedByParentId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentPeer::addSelectColumns($c);
		$startcol2 = (EquipmentPeer::NUM_COLUMNS - EquipmentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentModelPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentModelPeer::NUM_COLUMNS;

		OrganizationPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + OrganizationPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentPeer::MODEL_ID, EquipmentModelPeer::ID);

		$c->addJoin(EquipmentPeer::ORGID, OrganizationPeer::ORGID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentModelPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipmentModel(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipment($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipments();
				$obj2->addEquipment($obj1);
			}

			$omClass = OrganizationPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getOrganization(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addEquipment($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initEquipments();
				$obj3->addEquipment($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Equipment objects pre-filled with all related objects except EquipmentModel.
	 *
	 * @return     array Array of Equipment objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEquipmentModel(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EquipmentPeer::addSelectColumns($c);
		$startcol2 = (EquipmentPeer::NUM_COLUMNS - EquipmentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		OrganizationPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + OrganizationPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentPeer::ORGID, OrganizationPeer::ORGID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = OrganizationPeer::getOMClass($rs, $startcol2);


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getOrganization(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipment($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipments();
				$obj2->addEquipment($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Equipment objects pre-filled with all related objects except Organization.
	 *
	 * @return     array Array of Equipment objects.
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

		EquipmentPeer::addSelectColumns($c);
		$startcol2 = (EquipmentPeer::NUM_COLUMNS - EquipmentPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		EquipmentModelPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + EquipmentModelPeer::NUM_COLUMNS;

		$c->addJoin(EquipmentPeer::MODEL_ID, EquipmentModelPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = EquipmentPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = EquipmentModelPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getEquipmentModel(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addEquipment($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initEquipments();
				$obj2->addEquipment($obj1);
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
		return EquipmentPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a Equipment or Criteria object.
	 *
	 * @param      mixed $values Criteria or Equipment object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from Equipment object
		}

		$criteria->remove(EquipmentPeer::EQUIPMENT_ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a Equipment or Criteria object.
	 *
	 * @param      mixed $values Criteria or Equipment object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(EquipmentPeer::EQUIPMENT_ID);
			$selectCriteria->add(EquipmentPeer::EQUIPMENT_ID, $criteria->remove(EquipmentPeer::EQUIPMENT_ID), $comparison);

		} else { // $values is Equipment object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the EQUIPMENT table.
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
			$affectedRows += BasePeer::doDeleteAll(EquipmentPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a Equipment or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Equipment object or primary key or array of primary keys
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
			$con = Propel::getConnection(EquipmentPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof Equipment) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(EquipmentPeer::EQUIPMENT_ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given Equipment object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Equipment $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(Equipment $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(EquipmentPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(EquipmentPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::CALIBRATION_INFORMATION))
			$columns[EquipmentPeer::CALIBRATION_INFORMATION] = $obj->getCalibrationInformation();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::COMMISSION_DATE))
			$columns[EquipmentPeer::COMMISSION_DATE] = $obj->getCommissionDate();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::DELETED))
			$columns[EquipmentPeer::DELETED] = $obj->getDeleted();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::LAB_ASSIGNED_ID))
			$columns[EquipmentPeer::LAB_ASSIGNED_ID] = $obj->getLabAssignedId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::MAJOR))
			$columns[EquipmentPeer::MAJOR] = $obj->getMajor();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::MODEL_ID))
			$columns[EquipmentPeer::MODEL_ID] = $obj->getModelId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::NAME))
			$columns[EquipmentPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::NEES_OPERATED))
			$columns[EquipmentPeer::NEES_OPERATED] = $obj->getNeesOperated();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::NOTE))
			$columns[EquipmentPeer::NOTE] = $obj->getNote();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::ORGID))
			$columns[EquipmentPeer::ORGID] = $obj->getOrganizationId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::OWNER))
			$columns[EquipmentPeer::OWNER] = $obj->getOwner();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::PARENT_ID))
			$columns[EquipmentPeer::PARENT_ID] = $obj->getParentId();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::QUANTITY))
			$columns[EquipmentPeer::QUANTITY] = $obj->getQuantity();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::SEPARATE_SCHEDULING))
			$columns[EquipmentPeer::SEPARATE_SCHEDULING] = $obj->getSeparateScheduling();

		if ($obj->isNew() || $obj->isColumnModified(EquipmentPeer::SERIAL_NUMBER))
			$columns[EquipmentPeer::SERIAL_NUMBER] = $obj->getSerialNumber();

		}

		return BasePeer::doValidate(EquipmentPeer::DATABASE_NAME, EquipmentPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     Equipment
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(EquipmentPeer::DATABASE_NAME);

		$criteria->add(EquipmentPeer::EQUIPMENT_ID, $pk);


		$v = EquipmentPeer::doSelect($criteria, $con);

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
			$criteria->add(EquipmentPeer::EQUIPMENT_ID, $pks, Criteria::IN);
			$objs = EquipmentPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseEquipmentPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseEquipmentPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/EquipmentMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.EquipmentMapBuilder');
}
