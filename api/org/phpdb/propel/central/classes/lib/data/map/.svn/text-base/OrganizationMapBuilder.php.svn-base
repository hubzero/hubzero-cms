<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ORGANIZATION' table to 'NEEScentral' DatabaseMap object.
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
class OrganizationMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.OrganizationMapBuilder';

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

		$tMap = $this->dbMap->addTable('ORGANIZATION');
		$tMap->setPhpName('Organization');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('ORGANIZATION_SEQ');

		$tMap->addPrimaryKey('ORGID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DEPARTMENT', 'Department', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addForeignKey('FACILITYID', 'FacilityId', 'double', CreoleTypes::NUMERIC, 'ORGANIZATION', 'ORGID', false, 22);

		$tMap->addColumn('FLEXTPS_URL', 'FlexTpsUrl', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('IMAGE_URL', 'ImageUrl', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('LABORATORY', 'Laboratory', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('NAWI_ADMIN_USERS', 'NawiAdminUsers', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('NAWI_STATUS', 'NawiStatus', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addColumn('NSF_ACKNOWLEDGEMENT', 'NsfAcknowledgement', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('NSF_AWARD_URL', 'NsfAwardUrl', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('ORG_TYPE_ID', 'OrganizationTypeId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('PARENT_ORG_ID', 'ParentOrgId', 'double', CreoleTypes::NUMERIC, 'ORGANIZATION', 'ORGID', false, 22);

		$tMap->addColumn('POP_URL', 'PopUrl', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addForeignKey('SENSOR_MANIFEST_ID', 'SensorManifestId', 'double', CreoleTypes::NUMERIC, 'SENSOR_MANIFEST', 'ID', false, 22);

		$tMap->addColumn('SHORT_NAME', 'ShortName', 'string', CreoleTypes::VARCHAR, false, 64);

		$tMap->addColumn('SITENAME', 'SiteName', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('SITE_OP_USER', 'SiteOpUser', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('SYSADMIN', 'Sysadmin', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('SYSADMIN_EMAIL', 'SysadminEmail', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('SYSADMIN_USER', 'SysadminUser', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('TIMEZONE', 'Timezone', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addColumn('URL', 'Url', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addValidator('DEPARTMENT', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'DEPARTMENT');

		$tMap->addValidator('DEPARTMENT', 'required', 'propel.validator.RequiredValidator', '', 'DEPARTMENT');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('FACILITYID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FACILITYID');

		$tMap->addValidator('FACILITYID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FACILITYID');

		$tMap->addValidator('FACILITYID', 'required', 'propel.validator.RequiredValidator', '', 'FACILITYID');

		$tMap->addValidator('FLEXTPS_URL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'FLEXTPS_URL');

		$tMap->addValidator('FLEXTPS_URL', 'required', 'propel.validator.RequiredValidator', '', 'FLEXTPS_URL');

		$tMap->addValidator('IMAGE_URL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'IMAGE_URL');

		$tMap->addValidator('IMAGE_URL', 'required', 'propel.validator.RequiredValidator', '', 'IMAGE_URL');

		$tMap->addValidator('LABORATORY', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'LABORATORY');

		$tMap->addValidator('LABORATORY', 'required', 'propel.validator.RequiredValidator', '', 'LABORATORY');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('NAWI_ADMIN_USERS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NAWI_ADMIN_USERS');

		$tMap->addValidator('NAWI_ADMIN_USERS', 'required', 'propel.validator.RequiredValidator', '', 'NAWI_ADMIN_USERS');

		$tMap->addValidator('NAWI_STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'NAWI_STATUS');

		$tMap->addValidator('NAWI_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'NAWI_STATUS');

		$tMap->addValidator('NSF_ACKNOWLEDGEMENT', 'required', 'propel.validator.RequiredValidator', '', 'NSF_ACKNOWLEDGEMENT');

		$tMap->addValidator('NSF_AWARD_URL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NSF_AWARD_URL');

		$tMap->addValidator('NSF_AWARD_URL', 'required', 'propel.validator.RequiredValidator', '', 'NSF_AWARD_URL');

		$tMap->addValidator('ORGID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ORGID');

		$tMap->addValidator('ORGID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ORGID');

		$tMap->addValidator('ORGID', 'required', 'propel.validator.RequiredValidator', '', 'ORGID');

		$tMap->addValidator('ORGID', 'unique', 'propel.validator.UniqueValidator', '', 'ORGID');

		$tMap->addValidator('ORG_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ORG_TYPE_ID');

		$tMap->addValidator('ORG_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ORG_TYPE_ID');

		$tMap->addValidator('ORG_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'ORG_TYPE_ID');

		$tMap->addValidator('PARENT_ORG_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PARENT_ORG_ID');

		$tMap->addValidator('PARENT_ORG_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PARENT_ORG_ID');

		$tMap->addValidator('PARENT_ORG_ID', 'required', 'propel.validator.RequiredValidator', '', 'PARENT_ORG_ID');

		$tMap->addValidator('POP_URL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'POP_URL');

		$tMap->addValidator('POP_URL', 'required', 'propel.validator.RequiredValidator', '', 'POP_URL');

		$tMap->addValidator('SENSOR_MANIFEST_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SENSOR_MANIFEST_ID');

		$tMap->addValidator('SENSOR_MANIFEST_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SENSOR_MANIFEST_ID');

		$tMap->addValidator('SENSOR_MANIFEST_ID', 'required', 'propel.validator.RequiredValidator', '', 'SENSOR_MANIFEST_ID');

		$tMap->addValidator('SHORT_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '64', 'SHORT_NAME');

		$tMap->addValidator('SHORT_NAME', 'required', 'propel.validator.RequiredValidator', '', 'SHORT_NAME');

		$tMap->addValidator('SITENAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'SITENAME');

		$tMap->addValidator('SITENAME', 'required', 'propel.validator.RequiredValidator', '', 'SITENAME');

		$tMap->addValidator('SITE_OP_USER', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'SITE_OP_USER');

		$tMap->addValidator('SITE_OP_USER', 'required', 'propel.validator.RequiredValidator', '', 'SITE_OP_USER');

		$tMap->addValidator('SYSADMIN', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'SYSADMIN');

		$tMap->addValidator('SYSADMIN', 'required', 'propel.validator.RequiredValidator', '', 'SYSADMIN');

		$tMap->addValidator('SYSADMIN_EMAIL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'SYSADMIN_EMAIL');

		$tMap->addValidator('SYSADMIN_EMAIL', 'required', 'propel.validator.RequiredValidator', '', 'SYSADMIN_EMAIL');

		$tMap->addValidator('SYSADMIN_USER', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'SYSADMIN_USER');

		$tMap->addValidator('SYSADMIN_USER', 'required', 'propel.validator.RequiredValidator', '', 'SYSADMIN_USER');

		$tMap->addValidator('TIMEZONE', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'TIMEZONE');

		$tMap->addValidator('TIMEZONE', 'required', 'propel.validator.RequiredValidator', '', 'TIMEZONE');

		$tMap->addValidator('URL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'URL');

		$tMap->addValidator('URL', 'required', 'propel.validator.RequiredValidator', '', 'URL');

	} // doBuild()

} // OrganizationMapBuilder
