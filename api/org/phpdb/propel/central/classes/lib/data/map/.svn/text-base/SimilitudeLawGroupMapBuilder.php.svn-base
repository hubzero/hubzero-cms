<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SIMILITUDE_LAW_GROUP' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.map
 */
class SimilitudeLawGroupMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SimilitudeLawGroupMapBuilder';

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

		$tMap = $this->dbMap->addTable('SIMILITUDE_LAW_GROUP');
		$tMap->setPhpName('SimilitudeLawGroup');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SIMILITUDE_LAW_GROUP_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('EXPERIMENT_DOMAIN_ID', 'ExperimentDomainId', 'double', CreoleTypes::NUMERIC, 'EXPERIMENT_DOMAIN', 'ID', false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addColumn('SYSTEM_NAME', 'SystemName', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addValidator('EXPERIMENT_DOMAIN_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXPERIMENT_DOMAIN_ID');

		$tMap->addValidator('EXPERIMENT_DOMAIN_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXPERIMENT_DOMAIN_ID');

		$tMap->addValidator('EXPERIMENT_DOMAIN_ID', 'required', 'propel.validator.RequiredValidator', '', 'EXPERIMENT_DOMAIN_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('SYSTEM_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '256', 'SYSTEM_NAME');

		$tMap->addValidator('SYSTEM_NAME', 'required', 'propel.validator.RequiredValidator', '', 'SYSTEM_NAME');

	} // doBuild()

} // SimilitudeLawGroupMapBuilder
