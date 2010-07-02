<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by OrganizationPeer::getOMClass()
include_once 'lib/data/Organization.php';

/**
 * Base static class for performing query and update operations on the 'ORGANIZATION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseOrganizationPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'ORGANIZATION';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.Organization';

	/** The total number of columns. */
	const NUM_COLUMNS = 24;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ORGID field */
	const ORGID = 'ORGANIZATION.ORGID';

	/** the column name for the DEPARTMENT field */
	const DEPARTMENT = 'ORGANIZATION.DEPARTMENT';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'ORGANIZATION.DESCRIPTION';

	/** the column name for the FACILITYID field */
	const FACILITYID = 'ORGANIZATION.FACILITYID';

	/** the column name for the FLEXTPS_URL field */
	const FLEXTPS_URL = 'ORGANIZATION.FLEXTPS_URL';

	/** the column name for the IMAGE_URL field */
	const IMAGE_URL = 'ORGANIZATION.IMAGE_URL';

	/** the column name for the LABORATORY field */
	const LABORATORY = 'ORGANIZATION.LABORATORY';

	/** the column name for the NAME field */
	const NAME = 'ORGANIZATION.NAME';

	/** the column name for the NAWI_ADMIN_USERS field */
	const NAWI_ADMIN_USERS = 'ORGANIZATION.NAWI_ADMIN_USERS';

	/** the column name for the NAWI_STATUS field */
	const NAWI_STATUS = 'ORGANIZATION.NAWI_STATUS';

	/** the column name for the NSF_ACKNOWLEDGEMENT field */
	const NSF_ACKNOWLEDGEMENT = 'ORGANIZATION.NSF_ACKNOWLEDGEMENT';

	/** the column name for the NSF_AWARD_URL field */
	const NSF_AWARD_URL = 'ORGANIZATION.NSF_AWARD_URL';

	/** the column name for the ORG_TYPE_ID field */
	const ORG_TYPE_ID = 'ORGANIZATION.ORG_TYPE_ID';

	/** the column name for the PARENT_ORG_ID field */
	const PARENT_ORG_ID = 'ORGANIZATION.PARENT_ORG_ID';

	/** the column name for the POP_URL field */
	const POP_URL = 'ORGANIZATION.POP_URL';

	/** the column name for the SENSOR_MANIFEST_ID field */
	const SENSOR_MANIFEST_ID = 'ORGANIZATION.SENSOR_MANIFEST_ID';

	/** the column name for the SHORT_NAME field */
	const SHORT_NAME = 'ORGANIZATION.SHORT_NAME';

	/** the column name for the SITENAME field */
	const SITENAME = 'ORGANIZATION.SITENAME';

	/** the column name for the SITE_OP_USER field */
	const SITE_OP_USER = 'ORGANIZATION.SITE_OP_USER';

	/** the column name for the SYSADMIN field */
	const SYSADMIN = 'ORGANIZATION.SYSADMIN';

	/** the column name for the SYSADMIN_EMAIL field */
	const SYSADMIN_EMAIL = 'ORGANIZATION.SYSADMIN_EMAIL';

	/** the column name for the SYSADMIN_USER field */
	const SYSADMIN_USER = 'ORGANIZATION.SYSADMIN_USER';

	/** the column name for the TIMEZONE field */
	const TIMEZONE = 'ORGANIZATION.TIMEZONE';

	/** the column name for the URL field */
	const URL = 'ORGANIZATION.URL';

	/** A key representing a particular subclass */
	const CLASSKEY_0 = '0';

	/** A key representing a particular subclass */
	const CLASSKEY_ORGANIZATION = '0';

	/** A class that can be returned by this peer. */
	const CLASSNAME_0 = 'lib.data.Organization';

	/** A key representing a particular subclass */
	const CLASSKEY_1 = '1';

	/** A key representing a particular subclass */
	const CLASSKEY_FACILITY = '1';

	/** A class that can be returned by this peer. */
	const CLASSNAME_1 = 'lib.data.Facility';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Department', 'Description', 'FacilityId', 'FlexTpsUrl', 'ImageUrl', 'Laboratory', 'Name', 'NawiAdminUsers', 'NawiStatus', 'NsfAcknowledgement', 'NsfAwardUrl', 'OrganizationTypeId', 'ParentOrgId', 'PopUrl', 'SensorManifestId', 'ShortName', 'SiteName', 'SiteOpUser', 'Sysadmin', 'SysadminEmail', 'SysadminUser', 'Timezone', 'Url', ),
		BasePeer::TYPE_COLNAME => array (OrganizationPeer::ORGID, OrganizationPeer::DEPARTMENT, OrganizationPeer::DESCRIPTION, OrganizationPeer::FACILITYID, OrganizationPeer::FLEXTPS_URL, OrganizationPeer::IMAGE_URL, OrganizationPeer::LABORATORY, OrganizationPeer::NAME, OrganizationPeer::NAWI_ADMIN_USERS, OrganizationPeer::NAWI_STATUS, OrganizationPeer::NSF_ACKNOWLEDGEMENT, OrganizationPeer::NSF_AWARD_URL, OrganizationPeer::ORG_TYPE_ID, OrganizationPeer::PARENT_ORG_ID, OrganizationPeer::POP_URL, OrganizationPeer::SENSOR_MANIFEST_ID, OrganizationPeer::SHORT_NAME, OrganizationPeer::SITENAME, OrganizationPeer::SITE_OP_USER, OrganizationPeer::SYSADMIN, OrganizationPeer::SYSADMIN_EMAIL, OrganizationPeer::SYSADMIN_USER, OrganizationPeer::TIMEZONE, OrganizationPeer::URL, ),
		BasePeer::TYPE_FIELDNAME => array ('ORGID', 'DEPARTMENT', 'DESCRIPTION', 'FACILITYID', 'FLEXTPS_URL', 'IMAGE_URL', 'LABORATORY', 'NAME', 'NAWI_ADMIN_USERS', 'NAWI_STATUS', 'NSF_ACKNOWLEDGEMENT', 'NSF_AWARD_URL', 'ORG_TYPE_ID', 'PARENT_ORG_ID', 'POP_URL', 'SENSOR_MANIFEST_ID', 'SHORT_NAME', 'SITENAME', 'SITE_OP_USER', 'SYSADMIN', 'SYSADMIN_EMAIL', 'SYSADMIN_USER', 'TIMEZONE', 'URL', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Department' => 1, 'Description' => 2, 'FacilityId' => 3, 'FlexTpsUrl' => 4, 'ImageUrl' => 5, 'Laboratory' => 6, 'Name' => 7, 'NawiAdminUsers' => 8, 'NawiStatus' => 9, 'NsfAcknowledgement' => 10, 'NsfAwardUrl' => 11, 'OrganizationTypeId' => 12, 'ParentOrgId' => 13, 'PopUrl' => 14, 'SensorManifestId' => 15, 'ShortName' => 16, 'SiteName' => 17, 'SiteOpUser' => 18, 'Sysadmin' => 19, 'SysadminEmail' => 20, 'SysadminUser' => 21, 'Timezone' => 22, 'Url' => 23, ),
		BasePeer::TYPE_COLNAME => array (OrganizationPeer::ORGID => 0, OrganizationPeer::DEPARTMENT => 1, OrganizationPeer::DESCRIPTION => 2, OrganizationPeer::FACILITYID => 3, OrganizationPeer::FLEXTPS_URL => 4, OrganizationPeer::IMAGE_URL => 5, OrganizationPeer::LABORATORY => 6, OrganizationPeer::NAME => 7, OrganizationPeer::NAWI_ADMIN_USERS => 8, OrganizationPeer::NAWI_STATUS => 9, OrganizationPeer::NSF_ACKNOWLEDGEMENT => 10, OrganizationPeer::NSF_AWARD_URL => 11, OrganizationPeer::ORG_TYPE_ID => 12, OrganizationPeer::PARENT_ORG_ID => 13, OrganizationPeer::POP_URL => 14, OrganizationPeer::SENSOR_MANIFEST_ID => 15, OrganizationPeer::SHORT_NAME => 16, OrganizationPeer::SITENAME => 17, OrganizationPeer::SITE_OP_USER => 18, OrganizationPeer::SYSADMIN => 19, OrganizationPeer::SYSADMIN_EMAIL => 20, OrganizationPeer::SYSADMIN_USER => 21, OrganizationPeer::TIMEZONE => 22, OrganizationPeer::URL => 23, ),
		BasePeer::TYPE_FIELDNAME => array ('ORGID' => 0, 'DEPARTMENT' => 1, 'DESCRIPTION' => 2, 'FACILITYID' => 3, 'FLEXTPS_URL' => 4, 'IMAGE_URL' => 5, 'LABORATORY' => 6, 'NAME' => 7, 'NAWI_ADMIN_USERS' => 8, 'NAWI_STATUS' => 9, 'NSF_ACKNOWLEDGEMENT' => 10, 'NSF_AWARD_URL' => 11, 'ORG_TYPE_ID' => 12, 'PARENT_ORG_ID' => 13, 'POP_URL' => 14, 'SENSOR_MANIFEST_ID' => 15, 'SHORT_NAME' => 16, 'SITENAME' => 17, 'SITE_OP_USER' => 18, 'SYSADMIN' => 19, 'SYSADMIN_EMAIL' => 20, 'SYSADMIN_USER' => 21, 'TIMEZONE' => 22, 'URL' => 23, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/OrganizationMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.OrganizationMapBuilder');
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
			$map = OrganizationPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. OrganizationPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(OrganizationPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(OrganizationPeer::ORGID);

		$criteria->addSelectColumn(OrganizationPeer::DEPARTMENT);

		$criteria->addSelectColumn(OrganizationPeer::DESCRIPTION);

		$criteria->addSelectColumn(OrganizationPeer::FACILITYID);

		$criteria->addSelectColumn(OrganizationPeer::FLEXTPS_URL);

		$criteria->addSelectColumn(OrganizationPeer::IMAGE_URL);

		$criteria->addSelectColumn(OrganizationPeer::LABORATORY);

		$criteria->addSelectColumn(OrganizationPeer::NAME);

		$criteria->addSelectColumn(OrganizationPeer::NAWI_ADMIN_USERS);

		$criteria->addSelectColumn(OrganizationPeer::NAWI_STATUS);

		$criteria->addSelectColumn(OrganizationPeer::NSF_ACKNOWLEDGEMENT);

		$criteria->addSelectColumn(OrganizationPeer::NSF_AWARD_URL);

		$criteria->addSelectColumn(OrganizationPeer::ORG_TYPE_ID);

		$criteria->addSelectColumn(OrganizationPeer::PARENT_ORG_ID);

		$criteria->addSelectColumn(OrganizationPeer::POP_URL);

		$criteria->addSelectColumn(OrganizationPeer::SENSOR_MANIFEST_ID);

		$criteria->addSelectColumn(OrganizationPeer::SHORT_NAME);

		$criteria->addSelectColumn(OrganizationPeer::SITENAME);

		$criteria->addSelectColumn(OrganizationPeer::SITE_OP_USER);

		$criteria->addSelectColumn(OrganizationPeer::SYSADMIN);

		$criteria->addSelectColumn(OrganizationPeer::SYSADMIN_EMAIL);

		$criteria->addSelectColumn(OrganizationPeer::SYSADMIN_USER);

		$criteria->addSelectColumn(OrganizationPeer::TIMEZONE);

		$criteria->addSelectColumn(OrganizationPeer::URL);

	}

	const COUNT = 'COUNT(ORGANIZATION.ORGID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT ORGANIZATION.ORGID)';

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
			$criteria->addSelectColumn(OrganizationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(OrganizationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = OrganizationPeer::doSelectRS($criteria, $con);
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
	 * @return     Organization
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = OrganizationPeer::doSelect($critcopy, $con);
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
		return OrganizationPeer::populateObjects(OrganizationPeer::doSelectRS($criteria, $con));
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
			OrganizationPeer::addSelectColumns($criteria);
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
	
		// populate the object(s)
		while($rs->next()) {
		
			// class must be set each time from the record row
			$cls = Propel::import(OrganizationPeer::getOMClass($rs, 1));
			$obj = new $cls();
			$obj->hydrate($rs);
			$results[] = $obj;
			
		}
		return $results;
	}

	/**
	 * Returns the number of rows matching criteria, joining the related SensorManifest table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinSensorManifest(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(OrganizationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(OrganizationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(OrganizationPeer::SENSOR_MANIFEST_ID, SensorManifestPeer::ID);

		$rs = OrganizationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Organization objects pre-filled with their SensorManifest objects.
	 *
	 * @return     array Array of Organization objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinSensorManifest(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		OrganizationPeer::addSelectColumns($c);
		$startcol = (OrganizationPeer::NUM_COLUMNS - OrganizationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		SensorManifestPeer::addSelectColumns($c);

		$c->addJoin(OrganizationPeer::SENSOR_MANIFEST_ID, SensorManifestPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = OrganizationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SensorManifestPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getSensorManifest(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addOrganization($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initOrganizations();
				$obj2->addOrganization($obj1); //CHECKME
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
			$criteria->addSelectColumn(OrganizationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(OrganizationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(OrganizationPeer::SENSOR_MANIFEST_ID, SensorManifestPeer::ID);

		$rs = OrganizationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Organization objects pre-filled with all related objects.
	 *
	 * @return     array Array of Organization objects.
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

		OrganizationPeer::addSelectColumns($c);
		$startcol2 = (OrganizationPeer::NUM_COLUMNS - OrganizationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SensorManifestPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SensorManifestPeer::NUM_COLUMNS;

		$c->addJoin(OrganizationPeer::SENSOR_MANIFEST_ID, SensorManifestPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = OrganizationPeer::getOMClass($rs, 1);


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined SensorManifest rows
	
			$omClass = SensorManifestPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSensorManifest(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addOrganization($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initOrganizations();
				$obj2->addOrganization($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related OrganizationRelatedByFacilityId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptOrganizationRelatedByFacilityId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(OrganizationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(OrganizationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(OrganizationPeer::SENSOR_MANIFEST_ID, SensorManifestPeer::ID);

		$rs = OrganizationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related OrganizationRelatedByParentOrgId table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptOrganizationRelatedByParentOrgId(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(OrganizationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(OrganizationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(OrganizationPeer::SENSOR_MANIFEST_ID, SensorManifestPeer::ID);

		$rs = OrganizationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Returns the number of rows matching criteria, joining the related SensorManifest table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptSensorManifest(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(OrganizationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(OrganizationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = OrganizationPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of Organization objects pre-filled with all related objects except OrganizationRelatedByFacilityId.
	 *
	 * @return     array Array of Organization objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptOrganizationRelatedByFacilityId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		OrganizationPeer::addSelectColumns($c);
		$startcol2 = (OrganizationPeer::NUM_COLUMNS - OrganizationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SensorManifestPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SensorManifestPeer::NUM_COLUMNS;

		$c->addJoin(OrganizationPeer::SENSOR_MANIFEST_ID, SensorManifestPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = OrganizationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SensorManifestPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSensorManifest(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addOrganization($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initOrganizations();
				$obj2->addOrganization($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Organization objects pre-filled with all related objects except OrganizationRelatedByParentOrgId.
	 *
	 * @return     array Array of Organization objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptOrganizationRelatedByParentOrgId(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		OrganizationPeer::addSelectColumns($c);
		$startcol2 = (OrganizationPeer::NUM_COLUMNS - OrganizationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SensorManifestPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SensorManifestPeer::NUM_COLUMNS;

		$c->addJoin(OrganizationPeer::SENSOR_MANIFEST_ID, SensorManifestPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = OrganizationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SensorManifestPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSensorManifest(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addOrganization($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initOrganizations();
				$obj2->addOrganization($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	/**
	 * Selects a collection of Organization objects pre-filled with all related objects except SensorManifest.
	 *
	 * @return     array Array of Organization objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptSensorManifest(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		OrganizationPeer::addSelectColumns($c);
		$startcol2 = (OrganizationPeer::NUM_COLUMNS - OrganizationPeer::NUM_LAZY_LOAD_COLUMNS) + 1;


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = OrganizationPeer::getOMClass($rs, 1);

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

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
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      ResultSet $rs ResultSet with pointer to record containing om class.
	 * @param      int $colnum Column to examine for OM class information (first is 1).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass(ResultSet $rs, $colnum)
	{
		try {

			$omClass = null;
			$classKey = $rs->getString($colnum - 1 + 13);

			switch($classKey) {

				case self::CLASSKEY_0:
					$omClass = self::CLASSNAME_0;
					break;

				case self::CLASSKEY_1:
					$omClass = self::CLASSNAME_1;
					break;

				default:
					$omClass = self::CLASS_DEFAULT;

			} // switch

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a Organization or Criteria object.
	 *
	 * @param      mixed $values Criteria or Organization object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from Organization object
		}

		$criteria->remove(OrganizationPeer::ORGID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a Organization or Criteria object.
	 *
	 * @param      mixed $values Criteria or Organization object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(OrganizationPeer::ORGID);
			$selectCriteria->add(OrganizationPeer::ORGID, $criteria->remove(OrganizationPeer::ORGID), $comparison);

		} else { // $values is Organization object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the ORGANIZATION table.
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
			$affectedRows += BasePeer::doDeleteAll(OrganizationPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a Organization or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Organization object or primary key or array of primary keys
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
			$con = Propel::getConnection(OrganizationPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof Organization) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(OrganizationPeer::ORGID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given Organization object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Organization $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(Organization $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(OrganizationPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(OrganizationPeer::TABLE_NAME);

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

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::DEPARTMENT))
			$columns[OrganizationPeer::DEPARTMENT] = $obj->getDepartment();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::DESCRIPTION))
			$columns[OrganizationPeer::DESCRIPTION] = $obj->getDescription();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::FACILITYID))
			$columns[OrganizationPeer::FACILITYID] = $obj->getFacilityId();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::FLEXTPS_URL))
			$columns[OrganizationPeer::FLEXTPS_URL] = $obj->getFlexTpsUrl();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::IMAGE_URL))
			$columns[OrganizationPeer::IMAGE_URL] = $obj->getImageUrl();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::LABORATORY))
			$columns[OrganizationPeer::LABORATORY] = $obj->getLaboratory();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::NAME))
			$columns[OrganizationPeer::NAME] = $obj->getName();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::NAWI_ADMIN_USERS))
			$columns[OrganizationPeer::NAWI_ADMIN_USERS] = $obj->getNawiAdminUsers();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::NAWI_STATUS))
			$columns[OrganizationPeer::NAWI_STATUS] = $obj->getNawiStatus();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::NSF_ACKNOWLEDGEMENT))
			$columns[OrganizationPeer::NSF_ACKNOWLEDGEMENT] = $obj->getNsfAcknowledgement();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::NSF_AWARD_URL))
			$columns[OrganizationPeer::NSF_AWARD_URL] = $obj->getNsfAwardUrl();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::ORG_TYPE_ID))
			$columns[OrganizationPeer::ORG_TYPE_ID] = $obj->getOrganizationTypeId();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::PARENT_ORG_ID))
			$columns[OrganizationPeer::PARENT_ORG_ID] = $obj->getParentOrgId();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::POP_URL))
			$columns[OrganizationPeer::POP_URL] = $obj->getPopUrl();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::SENSOR_MANIFEST_ID))
			$columns[OrganizationPeer::SENSOR_MANIFEST_ID] = $obj->getSensorManifestId();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::SHORT_NAME))
			$columns[OrganizationPeer::SHORT_NAME] = $obj->getShortName();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::SITENAME))
			$columns[OrganizationPeer::SITENAME] = $obj->getSiteName();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::SITE_OP_USER))
			$columns[OrganizationPeer::SITE_OP_USER] = $obj->getSiteOpUser();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::SYSADMIN))
			$columns[OrganizationPeer::SYSADMIN] = $obj->getSysadmin();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::SYSADMIN_EMAIL))
			$columns[OrganizationPeer::SYSADMIN_EMAIL] = $obj->getSysadminEmail();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::SYSADMIN_USER))
			$columns[OrganizationPeer::SYSADMIN_USER] = $obj->getSysadminUser();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::TIMEZONE))
			$columns[OrganizationPeer::TIMEZONE] = $obj->getTimezone();

		if ($obj->isNew() || $obj->isColumnModified(OrganizationPeer::URL))
			$columns[OrganizationPeer::URL] = $obj->getUrl();

		}

		return BasePeer::doValidate(OrganizationPeer::DATABASE_NAME, OrganizationPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     Organization
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(OrganizationPeer::DATABASE_NAME);

		$criteria->add(OrganizationPeer::ORGID, $pk);


		$v = OrganizationPeer::doSelect($criteria, $con);

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
			$criteria->add(OrganizationPeer::ORGID, $pks, Criteria::IN);
			$objs = OrganizationPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseOrganizationPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseOrganizationPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/OrganizationMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.OrganizationMapBuilder');
}
