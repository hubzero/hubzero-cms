<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ANNOTATION' table to 'NEEScentral' DatabaseMap object.
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
class AnnotationMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.AnnotationMapBuilder';

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

		$tMap = $this->dbMap->addTable('ANNOTATION');
		$tMap->setPhpName('Annotation');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('CREATOR_ID', 'Creator', 'double', CreoleTypes::NUMERIC, 'PERSON', 'ID', false, 22);

		$tMap->addColumn('KEY', 'Key', 'string', CreoleTypes::VARCHAR, false, 64);

		$tMap->addColumn('MESSAGE', 'Message', 'string', CreoleTypes::VARCHAR, false, 4000);

		$tMap->addForeignKey('SUBJECT_TYPE_ID', 'SubjectTypeId', 'double', CreoleTypes::NUMERIC, 'ENTITY_TYPE', 'ID', false, 22);

		$tMap->addColumn('SUBJECT_ID', 'SubjectId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('OBJECT_TYPE_ID', 'ObjectTypeId', 'double', CreoleTypes::NUMERIC, 'ENTITY_TYPE', 'ID', false, 22);

		$tMap->addColumn('OBJECT_ID', 'ObjectId', 'double', CreoleTypes::NUMERIC, false, 22);

	} // doBuild()

} // AnnotationMapBuilder
