<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CURATED_CONTACT_LOG' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.curation.map
 */
class NCCuratedContactLogMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCCuratedContactLogMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap('NEEScentral');

		$tMap = $this->dbMap->addTable('CURATED_CONTACT_LOG');
		$tMap->setPhpName('NCCuratedContactLog');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('CURATED_CONTACT_LOG_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CONTACT_DATE', 'ContactDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('CONTACT_FIRST_NAME', 'ContactFirstName', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('CONTACT_LAST_NAME', 'ContactLastName', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('CONTACT_METHOD', 'ContactMethod', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addColumn('CONTACT_REASON', 'ContactReason', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('CONTACT_RESOLUTION', 'ContactResolution', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('CONTACT_STATUS', 'ContactStatus', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addColumn('CREATED_BY', 'CreatedBy', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('CREATED_DATE', 'CreatedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addForeignKey('OBJECT_ID', 'ObjectId', 'double', CreoleTypes::NUMERIC, 'CURATED_OBJECTS', 'OBJECT_ID', false, 22);

		$tMap->addColumn('PHONE_NUMBER', 'PhoneNumber', 'string', CreoleTypes::VARCHAR, false, 120);

		$tMap->addValidator('CONTACT_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONTACT_DATE');

		$tMap->addValidator('CONTACT_DATE', 'required', 'propel.validator.RequiredValidator', '', 'CONTACT_DATE');

		$tMap->addValidator('CONTACT_FIRST_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'CONTACT_FIRST_NAME');

		$tMap->addValidator('CONTACT_FIRST_NAME', 'required', 'propel.validator.RequiredValidator', '', 'CONTACT_FIRST_NAME');

		$tMap->addValidator('CONTACT_LAST_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'CONTACT_LAST_NAME');

		$tMap->addValidator('CONTACT_LAST_NAME', 'required', 'propel.validator.RequiredValidator', '', 'CONTACT_LAST_NAME');

		$tMap->addValidator('CONTACT_METHOD', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'CONTACT_METHOD');

		$tMap->addValidator('CONTACT_METHOD', 'required', 'propel.validator.RequiredValidator', '', 'CONTACT_METHOD');

		$tMap->addValidator('CONTACT_REASON', 'required', 'propel.validator.RequiredValidator', '', 'CONTACT_REASON');

		$tMap->addValidator('CONTACT_RESOLUTION', 'required', 'propel.validator.RequiredValidator', '', 'CONTACT_RESOLUTION');

		$tMap->addValidator('CONTACT_STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'CONTACT_STATUS');

		$tMap->addValidator('CONTACT_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'CONTACT_STATUS');

		$tMap->addValidator('CREATED_BY', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'CREATED_BY');

		$tMap->addValidator('CREATED_BY', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_BY');

		$tMap->addValidator('CREATED_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CREATED_DATE');

		$tMap->addValidator('CREATED_DATE', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_DATE');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('OBJECT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_ID', 'required', 'propel.validator.RequiredValidator', '', 'OBJECT_ID');

		$tMap->addValidator('PHONE_NUMBER', 'maxLength', 'propel.validator.MaxLengthValidator', '120', 'PHONE_NUMBER');

		$tMap->addValidator('PHONE_NUMBER', 'required', 'propel.validator.RequiredValidator', '', 'PHONE_NUMBER');

	} // doBuild()

} // NCCuratedContactLogMapBuilder
