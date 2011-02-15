<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by SiteReportsQAREotEvtPeer::getOMClass()
include_once 'lib/data/SiteReportsQAREotEvt.php';

/**
 * Base static class for performing query and update operations on the 'SITEREPORTS_QAR_EOT_EVT' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseSiteReportsQAREotEvtPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'SITEREPORTS_QAR_EOT_EVT';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.SiteReportsQAREotEvt';

	/** The total number of columns. */
	const NUM_COLUMNS = 23;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'SITEREPORTS_QAR_EOT_EVT.ID';

	/** the column name for the QAR_ID field */
	const QAR_ID = 'SITEREPORTS_QAR_EOT_EVT.QAR_ID';

	/** the column name for the EVENT_TYPE field */
	const EVENT_TYPE = 'SITEREPORTS_QAR_EOT_EVT.EVENT_TYPE';

	/** the column name for the ACTIVITY field */
	const ACTIVITY = 'SITEREPORTS_QAR_EOT_EVT.ACTIVITY';

	/** the column name for the ACTIVITY_OBJECTIVES field */
	const ACTIVITY_OBJECTIVES = 'SITEREPORTS_QAR_EOT_EVT.ACTIVITY_OBJECTIVES';

	/** the column name for the OBJECTIVE_MET field */
	const OBJECTIVE_MET = 'SITEREPORTS_QAR_EOT_EVT.OBJECTIVE_MET';

	/** the column name for the PARTICIPANT_CAT1 field */
	const PARTICIPANT_CAT1 = 'SITEREPORTS_QAR_EOT_EVT.PARTICIPANT_CAT1';

	/** the column name for the NUM_OF_PARTICIPANTS1 field */
	const NUM_OF_PARTICIPANTS1 = 'SITEREPORTS_QAR_EOT_EVT.NUM_OF_PARTICIPANTS1';

	/** the column name for the PARTICIPANT_DETAILS1 field */
	const PARTICIPANT_DETAILS1 = 'SITEREPORTS_QAR_EOT_EVT.PARTICIPANT_DETAILS1';

	/** the column name for the PARTICIPANT_CAT2 field */
	const PARTICIPANT_CAT2 = 'SITEREPORTS_QAR_EOT_EVT.PARTICIPANT_CAT2';

	/** the column name for the NUM_OF_PARTICIPANTS2 field */
	const NUM_OF_PARTICIPANTS2 = 'SITEREPORTS_QAR_EOT_EVT.NUM_OF_PARTICIPANTS2';

	/** the column name for the PARTICIPANT_DETAILS2 field */
	const PARTICIPANT_DETAILS2 = 'SITEREPORTS_QAR_EOT_EVT.PARTICIPANT_DETAILS2';

	/** the column name for the PARTICIPANT_CAT3 field */
	const PARTICIPANT_CAT3 = 'SITEREPORTS_QAR_EOT_EVT.PARTICIPANT_CAT3';

	/** the column name for the NUM_OF_PARTICIPANTS3 field */
	const NUM_OF_PARTICIPANTS3 = 'SITEREPORTS_QAR_EOT_EVT.NUM_OF_PARTICIPANTS3';

	/** the column name for the PARTICIPANT_DETAILS3 field */
	const PARTICIPANT_DETAILS3 = 'SITEREPORTS_QAR_EOT_EVT.PARTICIPANT_DETAILS3';

	/** the column name for the PARTICIPANT_CAT4 field */
	const PARTICIPANT_CAT4 = 'SITEREPORTS_QAR_EOT_EVT.PARTICIPANT_CAT4';

	/** the column name for the NUM_OF_PARTICIPANTS4 field */
	const NUM_OF_PARTICIPANTS4 = 'SITEREPORTS_QAR_EOT_EVT.NUM_OF_PARTICIPANTS4';

	/** the column name for the PARTICIPANT_DETAILS4 field */
	const PARTICIPANT_DETAILS4 = 'SITEREPORTS_QAR_EOT_EVT.PARTICIPANT_DETAILS4';

	/** the column name for the EVENT_NAR field */
	const EVENT_NAR = 'SITEREPORTS_QAR_EOT_EVT.EVENT_NAR';

	/** the column name for the CREATED_BY field */
	const CREATED_BY = 'SITEREPORTS_QAR_EOT_EVT.CREATED_BY';

	/** the column name for the CREATED_ON field */
	const CREATED_ON = 'SITEREPORTS_QAR_EOT_EVT.CREATED_ON';

	/** the column name for the UPDATED_BY field */
	const UPDATED_BY = 'SITEREPORTS_QAR_EOT_EVT.UPDATED_BY';

	/** the column name for the UPDATED_ON field */
	const UPDATED_ON = 'SITEREPORTS_QAR_EOT_EVT.UPDATED_ON';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('ID', 'QAR_ID', 'EVENT_TYPE', 'ACTIVITY', 'ACTIVITY_OBJECTIVES', 'OBJECTIVE_MET', 'PARTICIPANT_CAT1', 'NUM_OF_PARTICIPANTS1', 'PARTICIPANT_DETAILS1', 'PARTICIPANT_CAT2', 'NUM_OF_PARTICIPANTS2', 'PARTICIPANT_DETAILS2', 'PARTICIPANT_CAT3', 'NUM_OF_PARTICIPANTS3', 'PARTICIPANT_DETAILS3', 'PARTICIPANT_CAT4', 'NUM_OF_PARTICIPANTS4', 'PARTICIPANT_DETAILS4', 'EVENT_NAR', 'CREATED_BY', 'CREATED_ON', 'UPDATED_BY', 'UPDATED_ON', ),
		BasePeer::TYPE_COLNAME => array (SiteReportsQAREotEvtPeer::ID, SiteReportsQAREotEvtPeer::QAR_ID, SiteReportsQAREotEvtPeer::EVENT_TYPE, SiteReportsQAREotEvtPeer::ACTIVITY, SiteReportsQAREotEvtPeer::ACTIVITY_OBJECTIVES, SiteReportsQAREotEvtPeer::OBJECTIVE_MET, SiteReportsQAREotEvtPeer::PARTICIPANT_CAT1, SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS1, SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS1, SiteReportsQAREotEvtPeer::PARTICIPANT_CAT2, SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS2, SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS2, SiteReportsQAREotEvtPeer::PARTICIPANT_CAT3, SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS3, SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS3, SiteReportsQAREotEvtPeer::PARTICIPANT_CAT4, SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS4, SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS4, SiteReportsQAREotEvtPeer::EVENT_NAR, SiteReportsQAREotEvtPeer::CREATED_BY, SiteReportsQAREotEvtPeer::CREATED_ON, SiteReportsQAREotEvtPeer::UPDATED_BY, SiteReportsQAREotEvtPeer::UPDATED_ON, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'QAR_ID', 'EVENT_TYPE', 'ACTIVITY', 'ACTIVITY_OBJECTIVES', 'OBJECTIVE_MET', 'PARTICIPANT_CAT1', 'NUM_OF_PARTICIPANTS1', 'PARTICIPANT_DETAILS1', 'PARTICIPANT_CAT2', 'NUM_OF_PARTICIPANTS2', 'PARTICIPANT_DETAILS2', 'PARTICIPANT_CAT3', 'NUM_OF_PARTICIPANTS3', 'PARTICIPANT_DETAILS3', 'PARTICIPANT_CAT4', 'NUM_OF_PARTICIPANTS4', 'PARTICIPANT_DETAILS4', 'EVENT_NAR', 'CREATED_BY', 'CREATED_ON', 'UPDATED_BY', 'UPDATED_ON', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('ID' => 0, 'QAR_ID' => 1, 'EVENT_TYPE' => 2, 'ACTIVITY' => 3, 'ACTIVITY_OBJECTIVES' => 4, 'OBJECTIVE_MET' => 5, 'PARTICIPANT_CAT1' => 6, 'NUM_OF_PARTICIPANTS1' => 7, 'PARTICIPANT_DETAILS1' => 8, 'PARTICIPANT_CAT2' => 9, 'NUM_OF_PARTICIPANTS2' => 10, 'PARTICIPANT_DETAILS2' => 11, 'PARTICIPANT_CAT3' => 12, 'NUM_OF_PARTICIPANTS3' => 13, 'PARTICIPANT_DETAILS3' => 14, 'PARTICIPANT_CAT4' => 15, 'NUM_OF_PARTICIPANTS4' => 16, 'PARTICIPANT_DETAILS4' => 17, 'EVENT_NAR' => 18, 'CREATED_BY' => 19, 'CREATED_ON' => 20, 'UPDATED_BY' => 21, 'UPDATED_ON' => 22, ),
		BasePeer::TYPE_COLNAME => array (SiteReportsQAREotEvtPeer::ID => 0, SiteReportsQAREotEvtPeer::QAR_ID => 1, SiteReportsQAREotEvtPeer::EVENT_TYPE => 2, SiteReportsQAREotEvtPeer::ACTIVITY => 3, SiteReportsQAREotEvtPeer::ACTIVITY_OBJECTIVES => 4, SiteReportsQAREotEvtPeer::OBJECTIVE_MET => 5, SiteReportsQAREotEvtPeer::PARTICIPANT_CAT1 => 6, SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS1 => 7, SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS1 => 8, SiteReportsQAREotEvtPeer::PARTICIPANT_CAT2 => 9, SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS2 => 10, SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS2 => 11, SiteReportsQAREotEvtPeer::PARTICIPANT_CAT3 => 12, SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS3 => 13, SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS3 => 14, SiteReportsQAREotEvtPeer::PARTICIPANT_CAT4 => 15, SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS4 => 16, SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS4 => 17, SiteReportsQAREotEvtPeer::EVENT_NAR => 18, SiteReportsQAREotEvtPeer::CREATED_BY => 19, SiteReportsQAREotEvtPeer::CREATED_ON => 20, SiteReportsQAREotEvtPeer::UPDATED_BY => 21, SiteReportsQAREotEvtPeer::UPDATED_ON => 22, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'QAR_ID' => 1, 'EVENT_TYPE' => 2, 'ACTIVITY' => 3, 'ACTIVITY_OBJECTIVES' => 4, 'OBJECTIVE_MET' => 5, 'PARTICIPANT_CAT1' => 6, 'NUM_OF_PARTICIPANTS1' => 7, 'PARTICIPANT_DETAILS1' => 8, 'PARTICIPANT_CAT2' => 9, 'NUM_OF_PARTICIPANTS2' => 10, 'PARTICIPANT_DETAILS2' => 11, 'PARTICIPANT_CAT3' => 12, 'NUM_OF_PARTICIPANTS3' => 13, 'PARTICIPANT_DETAILS3' => 14, 'PARTICIPANT_CAT4' => 15, 'NUM_OF_PARTICIPANTS4' => 16, 'PARTICIPANT_DETAILS4' => 17, 'EVENT_NAR' => 18, 'CREATED_BY' => 19, 'CREATED_ON' => 20, 'UPDATED_BY' => 21, 'UPDATED_ON' => 22, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/map/SiteReportsQAREotEvtMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.map.SiteReportsQAREotEvtMapBuilder');
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
			$map = SiteReportsQAREotEvtPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. SiteReportsQAREotEvtPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(SiteReportsQAREotEvtPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::ID);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::QAR_ID);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::EVENT_TYPE);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::ACTIVITY);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::ACTIVITY_OBJECTIVES);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::OBJECTIVE_MET);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT1);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS1);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS1);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT2);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS2);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS2);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT3);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS3);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS3);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::PARTICIPANT_CAT4);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::NUM_OF_PARTICIPANTS4);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::PARTICIPANT_DETAILS4);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::EVENT_NAR);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::CREATED_BY);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::CREATED_ON);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::UPDATED_BY);

		$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::UPDATED_ON);

	}

	const COUNT = 'COUNT(SITEREPORTS_QAR_EOT_EVT.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT SITEREPORTS_QAR_EOT_EVT.ID)';

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
			$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = SiteReportsQAREotEvtPeer::doSelectRS($criteria, $con);
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
	 * @return     SiteReportsQAREotEvt
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = SiteReportsQAREotEvtPeer::doSelect($critcopy, $con);
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
		return SiteReportsQAREotEvtPeer::populateObjects(SiteReportsQAREotEvtPeer::doSelectRS($criteria, $con));
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
			SiteReportsQAREotEvtPeer::addSelectColumns($criteria);
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
		$cls = SiteReportsQAREotEvtPeer::getOMClass();
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
	 * Returns the number of rows matching criteria, joining the related SiteReportsQAR table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinSiteReportsQAR(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SiteReportsQAREotEvtPeer::QAR_ID, SiteReportsQARPeer::ID);

		$rs = SiteReportsQAREotEvtPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SiteReportsQAREotEvt objects pre-filled with their SiteReportsQAR objects.
	 *
	 * @return     array Array of SiteReportsQAREotEvt objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinSiteReportsQAR(Criteria $c, $con = null)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		SiteReportsQAREotEvtPeer::addSelectColumns($c);
		$startcol = (SiteReportsQAREotEvtPeer::NUM_COLUMNS - SiteReportsQAREotEvtPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		SiteReportsQARPeer::addSelectColumns($c);

		$c->addJoin(SiteReportsQAREotEvtPeer::QAR_ID, SiteReportsQARPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SiteReportsQAREotEvtPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = SiteReportsQARPeer::getOMClass();

			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getSiteReportsQAR(); //CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					// e.g. $author->addBookRelatedByBookId()
					$temp_obj2->addSiteReportsQAREotEvt($obj1); //CHECKME
					break;
				}
			}
			if ($newObject) {
				$obj2->initSiteReportsQAREotEvts();
				$obj2->addSiteReportsQAREotEvt($obj1); //CHECKME
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
			$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(SiteReportsQAREotEvtPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(SiteReportsQAREotEvtPeer::QAR_ID, SiteReportsQARPeer::ID);

		$rs = SiteReportsQAREotEvtPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}


	/**
	 * Selects a collection of SiteReportsQAREotEvt objects pre-filled with all related objects.
	 *
	 * @return     array Array of SiteReportsQAREotEvt objects.
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

		SiteReportsQAREotEvtPeer::addSelectColumns($c);
		$startcol2 = (SiteReportsQAREotEvtPeer::NUM_COLUMNS - SiteReportsQAREotEvtPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		SiteReportsQARPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + SiteReportsQARPeer::NUM_COLUMNS;

		$c->addJoin(SiteReportsQAREotEvtPeer::QAR_ID, SiteReportsQARPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = SiteReportsQAREotEvtPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


				// Add objects for joined SiteReportsQAR rows
	
			$omClass = SiteReportsQARPeer::getOMClass();


			$cls = Propel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getSiteReportsQAR(); // CHECKME
				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addSiteReportsQAREotEvt($obj1); // CHECKME
					break;
				}
			}

			if ($newObject) {
				$obj2->initSiteReportsQAREotEvts();
				$obj2->addSiteReportsQAREotEvt($obj1);
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
		return SiteReportsQAREotEvtPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a SiteReportsQAREotEvt or Criteria object.
	 *
	 * @param      mixed $values Criteria or SiteReportsQAREotEvt object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from SiteReportsQAREotEvt object
		}

		$criteria->remove(SiteReportsQAREotEvtPeer::ID); // remove pkey col since this table uses auto-increment


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
	 * Method perform an UPDATE on the database, given a SiteReportsQAREotEvt or Criteria object.
	 *
	 * @param      mixed $values Criteria or SiteReportsQAREotEvt object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(SiteReportsQAREotEvtPeer::ID);
			$selectCriteria->add(SiteReportsQAREotEvtPeer::ID, $criteria->remove(SiteReportsQAREotEvtPeer::ID), $comparison);

		} else { // $values is SiteReportsQAREotEvt object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the SITEREPORTS_QAR_EOT_EVT table.
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
			$affectedRows += BasePeer::doDeleteAll(SiteReportsQAREotEvtPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a SiteReportsQAREotEvt or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or SiteReportsQAREotEvt object or primary key or array of primary keys
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
			$con = Propel::getConnection(SiteReportsQAREotEvtPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof SiteReportsQAREotEvt) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(SiteReportsQAREotEvtPeer::ID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given SiteReportsQAREotEvt object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      SiteReportsQAREotEvt $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(SiteReportsQAREotEvt $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(SiteReportsQAREotEvtPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(SiteReportsQAREotEvtPeer::TABLE_NAME);

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

		}

		return BasePeer::doValidate(SiteReportsQAREotEvtPeer::DATABASE_NAME, SiteReportsQAREotEvtPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     SiteReportsQAREotEvt
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(SiteReportsQAREotEvtPeer::DATABASE_NAME);

		$criteria->add(SiteReportsQAREotEvtPeer::ID, $pk);


		$v = SiteReportsQAREotEvtPeer::doSelect($criteria, $con);

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
			$criteria->add(SiteReportsQAREotEvtPeer::ID, $pks, Criteria::IN);
			$objs = SiteReportsQAREotEvtPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseSiteReportsQAREotEvtPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseSiteReportsQAREotEvtPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/map/SiteReportsQAREotEvtMapBuilder.php';
	Propel::registerMapBuilder('lib.data.map.SiteReportsQAREotEvtMapBuilder');
}
