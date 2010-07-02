<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by CoordinateSpacePeer::getOMClass()
include_once 'lib/data/CoordinateSpace.php';

/**
 * Base static class for performing query and update operations on the 'COORDINATE_SPACE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseCoordinateSpacePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'COORDINATE_SPACE';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.CoordinateSpace';

	/** The total number of columns. */
	const NUM_COLUMNS = 24;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'COORDINATE_SPACE.ID';

	/** the column name for the ALTITUDE field */
	const ALTITUDE = 'COORDINATE_SPACE.ALTITUDE';

	/** the column name for the ALTITUDE_UNIT field */
	const ALTITUDE_UNIT = 'COORDINATE_SPACE.ALTITUDE_UNIT';

	/** the column name for the DATE_CREATED field */
	const DATE_CREATED = 'COORDINATE_SPACE.DATE_CREATED';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'COORDINATE_SPACE.DESCRIPTION';

	/** the column name for the EXPID field */
	const EXPID = 'COORDINATE_SPACE.EXPID';

	/** the column name for the LATITUDE field */
	const LATITUDE = 'COORDINATE_SPACE.LATITUDE';

	/** the column name for the LONGITUDE field */
	const LONGITUDE = 'COORDINATE_SPACE.LONGITUDE';

	/** the column name for the NAME field */
	const NAME = 'COORDINATE_SPACE.NAME';

	/** the column name for the PARENT_ID field */
	const PARENT_ID = 'COORDINATE_SPACE.PARENT_ID';

	/** the column name for the ROTATIONX field */
	const ROTATIONX = 'COORDINATE_SPACE.ROTATIONX';

	/** the column name for the ROTATIONXUNIT_ID field */
	const ROTATIONXUNIT_ID = 'COORDINATE_SPACE.ROTATIONXUNIT_ID';

	/** the column name for the ROTATIONY field */
	const ROTATIONY = 'COORDINATE_SPACE.ROTATIONY';

	/** the column name for the ROTATIONYUNIT_ID field */
	const ROTATIONYUNIT_ID = 'COORDINATE_SPACE.ROTATIONYUNIT_ID';

	/** the column name for the ROTATIONZ field */
	const ROTATIONZ = 'COORDINATE_SPACE.ROTATIONZ';

	/** the column name for the ROTATIONZUNIT_ID field */
	const ROTATIONZUNIT_ID = 'COORDINATE_SPACE.ROTATIONZUNIT_ID';

	/** the column name for the SCALE field */
	const SCALE = 'COORDINATE_SPACE.SCALE';

	/** the column name for the SYSTEM_ID field */
	const SYSTEM_ID = 'COORDINATE_SPACE.SYSTEM_ID';

	/** the column name for the TRANSLATIONX field */
	const TRANSLATIONX = 'COORDINATE_SPACE.TRANSLATIONX';

	/** the column name for the TRANSLATIONXUNIT_ID field */
	const TRANSLATIONXUNIT_ID = 'COORDINATE_SPACE.TRANSLATIONXUNIT_ID';

	/** the column name for the TRANSLATIONY field */
	const TRANSLATIONY = 'COORDINATE_SPACE.TRANSLATIONY';

	/** the column name for the TRANSLATIONYUNIT_ID field */
	const TRANSLATIONYUNIT_ID = 'COORDINATE_SPACE.TRANSLATIONYUNIT_ID';

	/** the column name for the TRANSLATIONZ field */
	const TRANSLATIONZ = 'COORDINATE_SPACE.TRANSLATIONZ';

	/** the column name for the TRANSLATIONZUNIT_ID field */
	const TRANSLATIONZUNIT_ID = 'COORDINATE_SPACE.TRANSLATIONZUNIT_ID';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Altitude', 'AltitudeUnitId', 'DateCreated', 'Description', 'ExperimentId', 'Latitude', 'Longitude', 'Name', 'ParentId', 'RotationX', 'RotationXUnitId', 'RotationY', 'RotationYUnitId', 'RotationZ', 'RotationZUnitId', 'Scale', 'SystemId', 'TranslationX', 'TranslationXUnitId', 'TranslationY', 'TranslationYUnitId', 'TranslationZ', 'TranslationZUnitId', ),
		BasePeer::TYPE_COLNAME => array (CoordinateSpacePeer::ID, CoordinateSpacePeer::ALTITUDE, CoordinateSpacePeer::ALTITUDE_UNIT, CoordinateSpacePeer::DATE_CREATED, CoordinateSpacePeer::DESCRIPTION, CoordinateSpacePeer::EXPID, CoordinateSpacePeer::LATITUDE, CoordinateSpacePeer::LONGITUDE, CoordinateSpacePeer::NAME, CoordinateSpacePeer::PARENT_ID, CoordinateSpacePeer::ROTATIONX, CoordinateSpacePeer::ROTATIONXUNIT_ID, CoordinateSpacePeer::ROTATIONY, CoordinateSpacePeer::ROTATIONYUNIT_ID, CoordinateSpacePeer::ROTATIONZ, CoordinateSpacePeer::ROTATIONZUNIT_ID, CoordinateSpacePeer::SCALE, CoordinateSpacePeer::SYSTEM_ID, CoordinateSpacePeer::TRANSLATIONX, CoordinateSpacePeer::TRANSLATIONXUNIT_ID, CoordinateSpacePeer::TRANSLATIONY, CoordinateSpacePeer::TRANSLATIONYUNIT_ID, CoordinateSpacePeer::TRANSLATIONZ, CoordinateSpacePeer::TRANSLATIONZUNIT_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'ALTITUDE', 'ALTITUDE_UNIT', 'DATE_CREATED', 'DESCRIPTION', 'EXPID', 'LATITUDE', 'LONGITUDE', 'NAME', 'PARENT_ID', 'ROTATIONX', 'ROTATIONXUNIT_ID', 'ROTATIONY', 'ROTATIONYUNIT_ID', 'ROTATIONZ', 'ROTATIONZUNIT_ID', 'SCALE', 'SYSTEM_ID', 'TRANSLATIONX', 'TRANSLATIONXUNIT_ID', 'TRANSLATIONY', 'TRANSLATIONYUNIT_ID', 'TRANSLATIONZ', 'TRANSLATIONZUNIT_ID', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Altitude' => 1, 'AltitudeUnitId' => 2, 'DateCreated' => 3, 'Description' => 4, 'ExperimentId' => 5, 'Latitude' => 6, 'Longitude' => 7, 'Name' => 8, 'ParentId' => 9, 'RotationX' => 10, 'RotationXUnitId' => 11, 'RotationY' => 12, 'RotationYUnitId' => 13, 'RotationZ' => 14, 'RotationZUnitId' => 15, 'Scale' => 16, 'SystemId' => 17, 'TranslationX' => 18, 'TranslationXUnitId' => 19, 'TranslationY' => 20, 'TranslationYUnitId' => 21, 'TranslationZ' => 22, 'TranslationZUnitId' => 23, ),
		BasePeer::TYPE_COLNAME => array (CoordinateSpacePeer::ID => 0, CoordinateSpacePeer::ALTITUDE => 1, CoordinateSpacePeer::ALTITUDE_UNIT => 2, CoordinateSpacePeer::DATE_CREATED => 3, CoordinateSpacePeer::DESCRIPTION => 4, CoordinateSpacePeer::EXPID => 5, CoordinateSpacePeer::LATITUDE => 6, CoordinateSpacePeer::LONGITUDE => 7, CoordinateSpacePeer::NAME => 8, CoordinateSpacePeer::PARENT_ID => 9, CoordinateSpacePeer::ROTATIONX => 10, CoordinateSpacePeer::ROTATIONXUNIT_ID => 11, CoordinateSpacePeer::ROTATIONY => 12, CoordinateSpacePeer::ROTATIONYUNIT_ID => 13, CoordinateSpacePeer::ROTATIONZ => 14, CoordinateSpacePeer::ROTATIONZUNIT_ID => 15, CoordinateSpacePeer::SCALE => 16, CoordinateSpacePeer::SYSTEM_ID => 17, CoordinateSpacePeer::TRANSLATIONX => 18, CoordinateSpacePeer::TRANSLATIONXUNIT_ID => 19, CoordinateSpacePeer::TRANSLATIONY => 20, CoordinateSpacePeer::TRANSLATIONYUNIT_ID => 21, CoordinateSpacePeer::TRANSLATIONZ => 22, CoordinateSpacePeer::TRANSLATIONZUNIT_ID => 23, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'ALTITUDE' => 1, 'ALTITUDE_UNIT' => 2, 'DATE_CREATED' => 3, 'DESCRIPTION' => 4, 'EXPID' => 5, 'LATITUDE' => 6, 'LONGITUDE' => 7, 'NAME' => 8, 'PARENT_ID' => 9, 'ROTATIONX' => 10, 'ROTATIONXUNIT_ID' => 11, 'ROTATIONY' => 12, 'ROTATIONYUNIT_ID' => 13, 'ROTATIONZ' => 14, 'ROTATIONZUNIT_ID' => 15, 'SCALE' => 16, 'SYSTEM_ID' => 17, 'TRANSLATIONX' => 18, 'TRANSLATIONXUNIT_ID' => 19, 'TRANSLATIONY' => 20, 'TRANSLATIONYUNIT_ID' => 21, 'TRANSLATIONZ' => 22, 'TRANSLATIONZUNIT_ID' => 23, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/CoordinateSpaceMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.CoordinateSpaceMapBuilder');
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
			$map = CoordinateSpacePeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. CoordinateSpacePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(CoordinateSpacePeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(CoordinateSpacePeer::ID);

		$criteria->addSelectColumn(CoordinateSpacePeer::ALTITUDE);

		$criteria->addSelectColumn(CoordinateSpacePeer::ALTITUDE_UNIT);

		$criteria->addSelectColumn(CoordinateSpacePeer::DATE_CREATED);

		$criteria->addSelectColumn(CoordinateSpacePeer::DESCRIPTION);

		$criteria->addSelectColumn(CoordinateSpacePeer::EXPID);

		$criteria->addSelectColumn(CoordinateSpacePeer::LATITUDE);

		$criteria->addSelectColumn(CoordinateSpacePeer::LONGITUDE);

		$criteria->addSelectColumn(CoordinateSpacePeer::NAME);

		$criteria->addSelectColumn(CoordinateSpacePeer::PARENT_ID);

		$criteria->addSelectColumn(CoordinateSpacePeer::ROTATIONX);

		$criteria->addSelectColumn(CoordinateSpacePeer::ROTATIONXUNIT_ID);

		$criteria->addSelectColumn(CoordinateSpacePeer::ROTATIONY);

		$criteria->addSelectColumn(CoordinateSpacePeer::ROTATIONYUNIT_ID);

		$criteria->addSelectColumn(CoordinateSpacePeer::ROTATIONZ);

		$criteria->addSelectColumn(CoordinateSpacePeer::ROTATIONZUNIT_ID);

		$criteria->addSelectColumn(CoordinateSpacePeer::SCALE);

		$criteria->addSelectColumn(CoordinateSpacePeer::SYSTEM_ID);

		$criteria->addSelectColumn(CoordinateSpacePeer::TRANSLATIONX);

		$criteria->addSelectColumn(CoordinateSpacePeer::TRANSLATIONXUNIT_ID);

		$criteria->addSelectColumn(CoordinateSpacePeer::TRANSLATIONY);

		$criteria->addSelectColumn(CoordinateSpacePeer::TRANSLATIONYUNIT_ID);

		$criteria->addSelectColumn(CoordinateSpacePeer::TRANSLATIONZ);

		$criteria->addSelectColumn(CoordinateSpacePeer::TRANSLATIONZUNIT_ID);

	}

	const COUNT = 'COUNT(COORDINATE_SPACE.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT COORDINATE_SPACE.ID)';

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
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
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
	 * @return     CoordinateSpace
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = CoordinateSpacePeer::doSelect($critcopy, $con);
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
		return CoordinateSpacePeer::populateObjects(CoordinateSpacePeer::doSelectRS($criteria, $con));
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
			CoordinateSpacePeer::addSelectColumns($criteria);
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
		$cls = CoordinateSpacePeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related CoordinateSystem table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCoordinateSystem(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Experiment table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinExperiment(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByTranslationXUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByTranslationXUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByTranslationYUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByTranslationYUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByRotationZUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByRotationZUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByAltitudeUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByAltitudeUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::ALTITUDE_UNIT, MeasurementUnitPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByRotationYUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByRotationYUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByTranslationZUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByTranslationZUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByRotationXUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMeasurementUnitRelatedByRotationXUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with their CoordinateSystem objects.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCoordinateSystem(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		CoordinateSystemPeer::addSelectColumns($c);

		$c->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSystemPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getCoordinateSystem(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addCoordinateSpace($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with their Experiment objects.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinExperiment(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		ExperimentPeer::addSelectColumns($c);

		$c->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ExperimentPeer::getOMClass($rs, $startcol);

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addCoordinateSpace($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByTranslationXUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByTranslationXUnitId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addCoordinateSpaceRelatedByTranslationXUnitId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initCoordinateSpacesRelatedByTranslationXUnitId();
				$obj2->addCoordinateSpaceRelatedByTranslationXUnitId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByTranslationYUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByTranslationYUnitId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addCoordinateSpaceRelatedByTranslationYUnitId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initCoordinateSpacesRelatedByTranslationYUnitId();
				$obj2->addCoordinateSpaceRelatedByTranslationYUnitId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByRotationZUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(CoordinateSpacePeer::ROTATIONZUNIT_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByRotationZUnitId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addCoordinateSpaceRelatedByRotationZUnitId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initCoordinateSpacesRelatedByRotationZUnitId();
				$obj2->addCoordinateSpaceRelatedByRotationZUnitId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByAltitudeUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(CoordinateSpacePeer::ALTITUDE_UNIT, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByAltitudeUnitId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addCoordinateSpaceRelatedByAltitudeUnitId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initCoordinateSpacesRelatedByAltitudeUnitId();
				$obj2->addCoordinateSpaceRelatedByAltitudeUnitId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByRotationYUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(CoordinateSpacePeer::ROTATIONYUNIT_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByRotationYUnitId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addCoordinateSpaceRelatedByRotationYUnitId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initCoordinateSpacesRelatedByRotationYUnitId();
				$obj2->addCoordinateSpaceRelatedByRotationYUnitId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByTranslationZUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByTranslationZUnitId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addCoordinateSpaceRelatedByTranslationZUnitId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initCoordinateSpacesRelatedByTranslationZUnitId();
				$obj2->addCoordinateSpaceRelatedByTranslationZUnitId($obj1); //CHECKME
			}
			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with their MeasurementUnit objects.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMeasurementUnitRelatedByRotationXUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		MeasurementUnitPeer::addSelectColumns($c);

		$c->addJoin(CoordinateSpacePeer::ROTATIONXUNIT_ID, MeasurementUnitPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = MeasurementUnitPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getMeasurementUnitRelatedByRotationXUnitId(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addCoordinateSpaceRelatedByRotationXUnitId($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initCoordinateSpacesRelatedByRotationXUnitId();
				$obj2->addCoordinateSpaceRelatedByRotationXUnitId($obj1); //CHECKME
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
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ALTITUDE_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with all related objects.
	 *
	 * @return     array Array of CoordinateSpace objects.
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

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol2 = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSystemPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSystemPeer::NUM_COLUMNS;

		ExperimentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ExperimentPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol8 = $startcol7 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol9 = $startcol8 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol10 = $startcol9 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol11 = $startcol10 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$c->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ALTITUDE_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined CoordinateSystem rows
	
			$omClass = CoordinateSystemPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSystem(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCoordinateSpace($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1);
			}


				// Add objects for joined Experiment rows
	
			$omClass = ExperimentPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getExperiment(); // CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addCoordinateSpace($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj3->initCoordinateSpaces();
				$obj3->addCoordinateSpace($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4 = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnitRelatedByTranslationXUnitId(); // CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addCoordinateSpaceRelatedByTranslationXUnitId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj4->initCoordinateSpacesRelatedByTranslationXUnitId();
				$obj4->addCoordinateSpaceRelatedByTranslationXUnitId($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5 = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnitRelatedByTranslationYUnitId(); // CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addCoordinateSpaceRelatedByTranslationYUnitId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj5->initCoordinateSpacesRelatedByTranslationYUnitId();
				$obj5->addCoordinateSpaceRelatedByTranslationYUnitId($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj6 = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getMeasurementUnitRelatedByRotationZUnitId(); // CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addCoordinateSpaceRelatedByRotationZUnitId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj6->initCoordinateSpacesRelatedByRotationZUnitId();
				$obj6->addCoordinateSpaceRelatedByRotationZUnitId($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj7 = new $cls();
			$obj7->hydrate($rs, $startcol7);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj7 = $temp_obj1->getMeasurementUnitRelatedByAltitudeUnitId(); // CHECKME
				if ($temp_obj7->getPrimaryKey() === $obj7->getPrimaryKey()) {
					$newObject = false;
					$temp_obj7->addCoordinateSpaceRelatedByAltitudeUnitId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj7->initCoordinateSpacesRelatedByAltitudeUnitId();
				$obj7->addCoordinateSpaceRelatedByAltitudeUnitId($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj8 = new $cls();
			$obj8->hydrate($rs, $startcol8);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj8 = $temp_obj1->getMeasurementUnitRelatedByRotationYUnitId(); // CHECKME
				if ($temp_obj8->getPrimaryKey() === $obj8->getPrimaryKey()) {
					$newObject = false;
					$temp_obj8->addCoordinateSpaceRelatedByRotationYUnitId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj8->initCoordinateSpacesRelatedByRotationYUnitId();
				$obj8->addCoordinateSpaceRelatedByRotationYUnitId($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj9 = new $cls();
			$obj9->hydrate($rs, $startcol9);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj9 = $temp_obj1->getMeasurementUnitRelatedByTranslationZUnitId(); // CHECKME
				if ($temp_obj9->getPrimaryKey() === $obj9->getPrimaryKey()) {
					$newObject = false;
					$temp_obj9->addCoordinateSpaceRelatedByTranslationZUnitId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj9->initCoordinateSpacesRelatedByTranslationZUnitId();
				$obj9->addCoordinateSpaceRelatedByTranslationZUnitId($obj1);
			}


				// Add objects for joined MeasurementUnit rows
	
			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj10 = new $cls();
			$obj10->hydrate($rs, $startcol10);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj10 = $temp_obj1->getMeasurementUnitRelatedByRotationXUnitId(); // CHECKME
				if ($temp_obj10->getPrimaryKey() === $obj10->getPrimaryKey()) {
					$newObject = false;
					$temp_obj10->addCoordinateSpaceRelatedByRotationXUnitId($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj10->initCoordinateSpacesRelatedByRotationXUnitId();
				$obj10->addCoordinateSpaceRelatedByRotationXUnitId($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CoordinateSpaceRelatedByParentId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCoordinateSpaceRelatedByParentId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ALTITUDE_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CoordinateSystem table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCoordinateSystem(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ALTITUDE_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Experiment table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptExperiment(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ALTITUDE_UNIT, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::ROTATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByTranslationXUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByTranslationXUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByTranslationYUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByTranslationYUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByRotationZUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByRotationZUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByAltitudeUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByAltitudeUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByRotationYUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByRotationYUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByTranslationZUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByTranslationZUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related MeasurementUnitRelatedByRotationXUnitId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptMeasurementUnitRelatedByRotationXUnitId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(CoordinateSpacePeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$criteria->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$rs = CoordinateSpacePeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with all related objects except CoordinateSpaceRelatedByParentId.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCoordinateSpaceRelatedByParentId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol2 = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSystemPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSystemPeer::NUM_COLUMNS;

		ExperimentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ExperimentPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol8 = $startcol7 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol9 = $startcol8 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol10 = $startcol9 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol11 = $startcol10 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$c->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ALTITUDE_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONXUNIT_ID, MeasurementUnitPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSystemPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSystem(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1);
			}

			$omClass = ExperimentPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initCoordinateSpaces();
				$obj3->addCoordinateSpace($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnitRelatedByTranslationXUnitId(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addCoordinateSpaceRelatedByTranslationXUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initCoordinateSpacesRelatedByTranslationXUnitId();
				$obj4->addCoordinateSpaceRelatedByTranslationXUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnitRelatedByTranslationYUnitId(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addCoordinateSpaceRelatedByTranslationYUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initCoordinateSpacesRelatedByTranslationYUnitId();
				$obj5->addCoordinateSpaceRelatedByTranslationYUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj6  = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getMeasurementUnitRelatedByRotationZUnitId(); //CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addCoordinateSpaceRelatedByRotationZUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj6->initCoordinateSpacesRelatedByRotationZUnitId();
				$obj6->addCoordinateSpaceRelatedByRotationZUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj7  = new $cls();
			$obj7->hydrate($rs, $startcol7);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj7 = $temp_obj1->getMeasurementUnitRelatedByAltitudeUnitId(); //CHECKME
				if ($temp_obj7->getPrimaryKey() === $obj7->getPrimaryKey()) {
					$newObject = false;
					$temp_obj7->addCoordinateSpaceRelatedByAltitudeUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj7->initCoordinateSpacesRelatedByAltitudeUnitId();
				$obj7->addCoordinateSpaceRelatedByAltitudeUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj8  = new $cls();
			$obj8->hydrate($rs, $startcol8);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj8 = $temp_obj1->getMeasurementUnitRelatedByRotationYUnitId(); //CHECKME
				if ($temp_obj8->getPrimaryKey() === $obj8->getPrimaryKey()) {
					$newObject = false;
					$temp_obj8->addCoordinateSpaceRelatedByRotationYUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj8->initCoordinateSpacesRelatedByRotationYUnitId();
				$obj8->addCoordinateSpaceRelatedByRotationYUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj9  = new $cls();
			$obj9->hydrate($rs, $startcol9);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj9 = $temp_obj1->getMeasurementUnitRelatedByTranslationZUnitId(); //CHECKME
				if ($temp_obj9->getPrimaryKey() === $obj9->getPrimaryKey()) {
					$newObject = false;
					$temp_obj9->addCoordinateSpaceRelatedByTranslationZUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj9->initCoordinateSpacesRelatedByTranslationZUnitId();
				$obj9->addCoordinateSpaceRelatedByTranslationZUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj10  = new $cls();
			$obj10->hydrate($rs, $startcol10);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj10 = $temp_obj1->getMeasurementUnitRelatedByRotationXUnitId(); //CHECKME
				if ($temp_obj10->getPrimaryKey() === $obj10->getPrimaryKey()) {
					$newObject = false;
					$temp_obj10->addCoordinateSpaceRelatedByRotationXUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj10->initCoordinateSpacesRelatedByRotationXUnitId();
				$obj10->addCoordinateSpaceRelatedByRotationXUnitId($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with all related objects except CoordinateSystem.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCoordinateSystem(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol2 = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		ExperimentPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + ExperimentPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol8 = $startcol7 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol9 = $startcol8 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol10 = $startcol9 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ALTITUDE_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONXUNIT_ID, MeasurementUnitPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = ExperimentPeer::getOMClass($rs, $startcol2);


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMeasurementUnitRelatedByTranslationXUnitId(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addCoordinateSpaceRelatedByTranslationXUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initCoordinateSpacesRelatedByTranslationXUnitId();
				$obj3->addCoordinateSpaceRelatedByTranslationXUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnitRelatedByTranslationYUnitId(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addCoordinateSpaceRelatedByTranslationYUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initCoordinateSpacesRelatedByTranslationYUnitId();
				$obj4->addCoordinateSpaceRelatedByTranslationYUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnitRelatedByRotationZUnitId(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addCoordinateSpaceRelatedByRotationZUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initCoordinateSpacesRelatedByRotationZUnitId();
				$obj5->addCoordinateSpaceRelatedByRotationZUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj6  = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getMeasurementUnitRelatedByAltitudeUnitId(); //CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addCoordinateSpaceRelatedByAltitudeUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj6->initCoordinateSpacesRelatedByAltitudeUnitId();
				$obj6->addCoordinateSpaceRelatedByAltitudeUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj7  = new $cls();
			$obj7->hydrate($rs, $startcol7);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj7 = $temp_obj1->getMeasurementUnitRelatedByRotationYUnitId(); //CHECKME
				if ($temp_obj7->getPrimaryKey() === $obj7->getPrimaryKey()) {
					$newObject = false;
					$temp_obj7->addCoordinateSpaceRelatedByRotationYUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj7->initCoordinateSpacesRelatedByRotationYUnitId();
				$obj7->addCoordinateSpaceRelatedByRotationYUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj8  = new $cls();
			$obj8->hydrate($rs, $startcol8);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj8 = $temp_obj1->getMeasurementUnitRelatedByTranslationZUnitId(); //CHECKME
				if ($temp_obj8->getPrimaryKey() === $obj8->getPrimaryKey()) {
					$newObject = false;
					$temp_obj8->addCoordinateSpaceRelatedByTranslationZUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj8->initCoordinateSpacesRelatedByTranslationZUnitId();
				$obj8->addCoordinateSpaceRelatedByTranslationZUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj9  = new $cls();
			$obj9->hydrate($rs, $startcol9);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj9 = $temp_obj1->getMeasurementUnitRelatedByRotationXUnitId(); //CHECKME
				if ($temp_obj9->getPrimaryKey() === $obj9->getPrimaryKey()) {
					$newObject = false;
					$temp_obj9->addCoordinateSpaceRelatedByRotationXUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj9->initCoordinateSpacesRelatedByRotationXUnitId();
				$obj9->addCoordinateSpaceRelatedByRotationXUnitId($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with all related objects except Experiment.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptExperiment(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol2 = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSystemPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSystemPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol8 = $startcol7 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol9 = $startcol8 + MeasurementUnitPeer::NUM_COLUMNS;

		MeasurementUnitPeer::addSelectColumns($c);
		$startcol10 = $startcol9 + MeasurementUnitPeer::NUM_COLUMNS;

		$c->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONXUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ALTITUDE_UNIT, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONYUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::TRANSLATIONZUNIT_ID, MeasurementUnitPeer::ID);

		$c->addJoin(CoordinateSpacePeer::ROTATIONXUNIT_ID, MeasurementUnitPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSystemPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSystem(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getMeasurementUnitRelatedByTranslationXUnitId(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addCoordinateSpaceRelatedByTranslationXUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initCoordinateSpacesRelatedByTranslationXUnitId();
				$obj3->addCoordinateSpaceRelatedByTranslationXUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj4  = new $cls();
			$obj4->hydrate($rs, $startcol4);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj4 = $temp_obj1->getMeasurementUnitRelatedByTranslationYUnitId(); //CHECKME
				if ($temp_obj4->getPrimaryKey() === $obj4->getPrimaryKey()) {
					$newObject = false;
					$temp_obj4->addCoordinateSpaceRelatedByTranslationYUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj4->initCoordinateSpacesRelatedByTranslationYUnitId();
				$obj4->addCoordinateSpaceRelatedByTranslationYUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj5  = new $cls();
			$obj5->hydrate($rs, $startcol5);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj5 = $temp_obj1->getMeasurementUnitRelatedByRotationZUnitId(); //CHECKME
				if ($temp_obj5->getPrimaryKey() === $obj5->getPrimaryKey()) {
					$newObject = false;
					$temp_obj5->addCoordinateSpaceRelatedByRotationZUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj5->initCoordinateSpacesRelatedByRotationZUnitId();
				$obj5->addCoordinateSpaceRelatedByRotationZUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj6  = new $cls();
			$obj6->hydrate($rs, $startcol6);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj6 = $temp_obj1->getMeasurementUnitRelatedByAltitudeUnitId(); //CHECKME
				if ($temp_obj6->getPrimaryKey() === $obj6->getPrimaryKey()) {
					$newObject = false;
					$temp_obj6->addCoordinateSpaceRelatedByAltitudeUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj6->initCoordinateSpacesRelatedByAltitudeUnitId();
				$obj6->addCoordinateSpaceRelatedByAltitudeUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj7  = new $cls();
			$obj7->hydrate($rs, $startcol7);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj7 = $temp_obj1->getMeasurementUnitRelatedByRotationYUnitId(); //CHECKME
				if ($temp_obj7->getPrimaryKey() === $obj7->getPrimaryKey()) {
					$newObject = false;
					$temp_obj7->addCoordinateSpaceRelatedByRotationYUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj7->initCoordinateSpacesRelatedByRotationYUnitId();
				$obj7->addCoordinateSpaceRelatedByRotationYUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj8  = new $cls();
			$obj8->hydrate($rs, $startcol8);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj8 = $temp_obj1->getMeasurementUnitRelatedByTranslationZUnitId(); //CHECKME
				if ($temp_obj8->getPrimaryKey() === $obj8->getPrimaryKey()) {
					$newObject = false;
					$temp_obj8->addCoordinateSpaceRelatedByTranslationZUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj8->initCoordinateSpacesRelatedByTranslationZUnitId();
				$obj8->addCoordinateSpaceRelatedByTranslationZUnitId($obj1);
			}

			$omClass = MeasurementUnitPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj9  = new $cls();
			$obj9->hydrate($rs, $startcol9);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj9 = $temp_obj1->getMeasurementUnitRelatedByRotationXUnitId(); //CHECKME
				if ($temp_obj9->getPrimaryKey() === $obj9->getPrimaryKey()) {
					$newObject = false;
					$temp_obj9->addCoordinateSpaceRelatedByRotationXUnitId($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj9->initCoordinateSpacesRelatedByRotationXUnitId();
				$obj9->addCoordinateSpaceRelatedByRotationXUnitId($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with all related objects except MeasurementUnitRelatedByTranslationXUnitId.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByTranslationXUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol2 = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSystemPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSystemPeer::NUM_COLUMNS;

		ExperimentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ExperimentPeer::NUM_COLUMNS;

		$c->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$c->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSystemPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSystem(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1);
			}

			$omClass = ExperimentPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initCoordinateSpaces();
				$obj3->addCoordinateSpace($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with all related objects except MeasurementUnitRelatedByTranslationYUnitId.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByTranslationYUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol2 = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSystemPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSystemPeer::NUM_COLUMNS;

		ExperimentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ExperimentPeer::NUM_COLUMNS;

		$c->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$c->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSystemPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSystem(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1);
			}

			$omClass = ExperimentPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initCoordinateSpaces();
				$obj3->addCoordinateSpace($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with all related objects except MeasurementUnitRelatedByRotationZUnitId.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByRotationZUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol2 = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSystemPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSystemPeer::NUM_COLUMNS;

		ExperimentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ExperimentPeer::NUM_COLUMNS;

		$c->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$c->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSystemPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSystem(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1);
			}

			$omClass = ExperimentPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initCoordinateSpaces();
				$obj3->addCoordinateSpace($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with all related objects except MeasurementUnitRelatedByAltitudeUnitId.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByAltitudeUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol2 = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSystemPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSystemPeer::NUM_COLUMNS;

		ExperimentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ExperimentPeer::NUM_COLUMNS;

		$c->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$c->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSystemPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSystem(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1);
			}

			$omClass = ExperimentPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initCoordinateSpaces();
				$obj3->addCoordinateSpace($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with all related objects except MeasurementUnitRelatedByRotationYUnitId.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByRotationYUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol2 = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSystemPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSystemPeer::NUM_COLUMNS;

		ExperimentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ExperimentPeer::NUM_COLUMNS;

		$c->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$c->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSystemPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSystem(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1);
			}

			$omClass = ExperimentPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initCoordinateSpaces();
				$obj3->addCoordinateSpace($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with all related objects except MeasurementUnitRelatedByTranslationZUnitId.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByTranslationZUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol2 = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSystemPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSystemPeer::NUM_COLUMNS;

		ExperimentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ExperimentPeer::NUM_COLUMNS;

		$c->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$c->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSystemPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSystem(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1);
			}

			$omClass = ExperimentPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initCoordinateSpaces();
				$obj3->addCoordinateSpace($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of CoordinateSpace objects pre-filled with all related objects except MeasurementUnitRelatedByRotationXUnitId.
	 *
	 * @return     array Array of CoordinateSpace objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptMeasurementUnitRelatedByRotationXUnitId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CoordinateSpacePeer::addSelectColumns($c);
		$startcol2 = (CoordinateSpacePeer::NUM_COLUMNS - CoordinateSpacePeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		CoordinateSystemPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + CoordinateSystemPeer::NUM_COLUMNS;

		ExperimentPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + ExperimentPeer::NUM_COLUMNS;

		$c->addJoin(CoordinateSpacePeer::SYSTEM_ID, CoordinateSystemPeer::ID);

		$c->addJoin(CoordinateSpacePeer::EXPID, ExperimentPeer::EXPID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = CoordinateSpacePeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = CoordinateSystemPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getCoordinateSystem(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initCoordinateSpaces();
				$obj2->addCoordinateSpace($obj1);
			}

			$omClass = ExperimentPeer::getOMClass($rs, $startcol3);


			$cls = Propel::import($omClass);
			$obj3  = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getExperiment(); //CHECKME
				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addCoordinateSpace($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj3->initCoordinateSpaces();
				$obj3->addCoordinateSpace($obj1);
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
		return CoordinateSpacePeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a CoordinateSpace or Criteria object.
	 *
	 * @param      mixed $values Criteria or CoordinateSpace object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from CoordinateSpace object
		}

		$criteria->remove(CoordinateSpacePeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a CoordinateSpace or Criteria object.
	 *
	 * @param      mixed $values Criteria or CoordinateSpace object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(CoordinateSpacePeer::ID);
			$selectCriteria->add(CoordinateSpacePeer::ID, $criteria->remove(CoordinateSpacePeer::ID), $comparison);

		} else { // $values is CoordinateSpace object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the COORDINATE_SPACE table.
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
			$affectedRows += BasePeer::doDeleteAll(CoordinateSpacePeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a CoordinateSpace or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or CoordinateSpace object or primary key or array of primary keys
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
			$con = Propel::getConnection(CoordinateSpacePeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof CoordinateSpace) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(CoordinateSpacePeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given CoordinateSpace object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      CoordinateSpace $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(CoordinateSpace $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(CoordinateSpacePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(CoordinateSpacePeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::ALTITUDE))
			$columns[CoordinateSpacePeer::ALTITUDE] = $obj->getAltitude();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::ALTITUDE_UNIT))
			$columns[CoordinateSpacePeer::ALTITUDE_UNIT] = $obj->getAltitudeUnitId();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::DATE_CREATED))
			$columns[CoordinateSpacePeer::DATE_CREATED] = $obj->getDateCreated();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::DESCRIPTION))
			$columns[CoordinateSpacePeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::EXPID))
			$columns[CoordinateSpacePeer::EXPID] = $obj->getExperimentId();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::LATITUDE))
			$columns[CoordinateSpacePeer::LATITUDE] = $obj->getLatitude();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::LONGITUDE))
			$columns[CoordinateSpacePeer::LONGITUDE] = $obj->getLongitude();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::NAME))
			$columns[CoordinateSpacePeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::PARENT_ID))
			$columns[CoordinateSpacePeer::PARENT_ID] = $obj->getParentId();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::ROTATIONX))
			$columns[CoordinateSpacePeer::ROTATIONX] = $obj->getRotationX();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::ROTATIONXUNIT_ID))
			$columns[CoordinateSpacePeer::ROTATIONXUNIT_ID] = $obj->getRotationXUnitId();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::ROTATIONY))
			$columns[CoordinateSpacePeer::ROTATIONY] = $obj->getRotationY();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::ROTATIONYUNIT_ID))
			$columns[CoordinateSpacePeer::ROTATIONYUNIT_ID] = $obj->getRotationYUnitId();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::ROTATIONZ))
			$columns[CoordinateSpacePeer::ROTATIONZ] = $obj->getRotationZ();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::ROTATIONZUNIT_ID))
			$columns[CoordinateSpacePeer::ROTATIONZUNIT_ID] = $obj->getRotationZUnitId();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::SCALE))
			$columns[CoordinateSpacePeer::SCALE] = $obj->getScale();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::SYSTEM_ID))
			$columns[CoordinateSpacePeer::SYSTEM_ID] = $obj->getSystemId();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::TRANSLATIONX))
			$columns[CoordinateSpacePeer::TRANSLATIONX] = $obj->getTranslationX();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::TRANSLATIONXUNIT_ID))
			$columns[CoordinateSpacePeer::TRANSLATIONXUNIT_ID] = $obj->getTranslationXUnitId();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::TRANSLATIONY))
			$columns[CoordinateSpacePeer::TRANSLATIONY] = $obj->getTranslationY();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::TRANSLATIONYUNIT_ID))
			$columns[CoordinateSpacePeer::TRANSLATIONYUNIT_ID] = $obj->getTranslationYUnitId();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::TRANSLATIONZ))
			$columns[CoordinateSpacePeer::TRANSLATIONZ] = $obj->getTranslationZ();

		if ($obj->isNew() || $obj->isColumnModified(CoordinateSpacePeer::TRANSLATIONZUNIT_ID))
			$columns[CoordinateSpacePeer::TRANSLATIONZUNIT_ID] = $obj->getTranslationZUnitId();

		}

		return BasePeer::doValidate(CoordinateSpacePeer::DATABASE_NAME, CoordinateSpacePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     CoordinateSpace
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(CoordinateSpacePeer::DATABASE_NAME);

		$criteria->add(CoordinateSpacePeer::ID, $pk);


		$v = CoordinateSpacePeer::doSelect($criteria, $con);

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
			$criteria->add(CoordinateSpacePeer::ID, $pks, Criteria::IN);
			$objs = CoordinateSpacePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseCoordinateSpacePeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseCoordinateSpacePeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/CoordinateSpaceMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.CoordinateSpaceMapBuilder');
}
