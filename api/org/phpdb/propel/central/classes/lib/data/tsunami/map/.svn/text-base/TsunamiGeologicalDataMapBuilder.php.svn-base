<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TSUNAMI_GEOLOGICAL_DATA' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.tsunami.map
 */
class TsunamiGeologicalDataMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.tsunami.map.TsunamiGeologicalDataMapBuilder';

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

		$tMap = $this->dbMap->addTable('TSUNAMI_GEOLOGICAL_DATA');
		$tMap->setPhpName('TsunamiGeologicalData');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TSNM_GLGCL_DT_SEQ');

		$tMap->addPrimaryKey('TSUNAMI_GEOLOGICAL_DATA_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DISPLACEMENT', 'Displacement', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DISPLACEMENT_SUBSIDENCE', 'DisplacementSubsidence', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DISPLACEMENT_UPLIFT', 'DisplacementUplift', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('EIL', 'Eil', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('EIL_CHARACTERISTICS', 'EilCharacteristics', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('EIL_DIST_INLAND', 'EilDistInland', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('EIL_ELEVATION', 'EilElevation', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FAULT', 'Fault', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FAULT_GEOMORPHIC', 'FaultGeomorphic', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FAULT_OFFSET', 'FaultOffset', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FAULT_PALEO', 'FaultPaleo', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FAULT_STRIKE_MEASURE', 'FaultStrikeMeasure', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FAULT_TYPE', 'FaultType', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('GMCHANGES', 'Gmchanges', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('GMCHANGES_BED_MOD', 'GmchangesBedMod', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('GMCHANGES_DEPOSIT', 'GmchangesDeposit', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('GMCHANGES_SCOUR', 'GmchangesScour', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PALEO', 'Paleo', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PALEO_CHARACTERISTICS', 'PaleoCharacteristics', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PALEO_CORE_SAMPLES', 'PaleoCoreSamples', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PALEO_DIST_INLAND', 'PaleoDistInland', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PALEO_ELEVATION', 'PaleoElevation', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PALEO_OUTCROPS', 'PaleoOutcrops', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PALEO_SCALE', 'PaleoScale', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PALEO_SED_PEELS', 'PaleoSedPeels', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PALEO_SPATIAL_VAR', 'PaleoSpatialVar', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SMSL', 'Smsl', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SSL_COEFFICIENT_OF_FRICTION', 'SslCoefficientOfFriction', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SSL_DEPOSITS', 'SslDeposits', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SSL_SCARS', 'SslScars', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('TDCBM', 'Tdcbm', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('TDCBM_CHARACTERISTICS', 'TdcbmCharacteristics', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('TDCBM_DIST_INLAND', 'TdcbmDistInland', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('TDCBM_ELEVATION', 'TdcbmElevation', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('TDCBM_SCALE', 'TdcbmScale', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('TDCBM_SPATIAL_VAR', 'TdcbmSpatialVar', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('TSUNAMI_DOC_LIB_ID', 'TsunamiDocLibId', 'double', CreoleTypes::NUMERIC, 'TSUNAMI_DOC_LIB', 'TSUNAMI_DOC_LIB_ID', false, 22);

		$tMap->addValidator('DISPLACEMENT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DISPLACEMENT');

		$tMap->addValidator('DISPLACEMENT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DISPLACEMENT');

		$tMap->addValidator('DISPLACEMENT', 'required', 'propel.validator.RequiredValidator', '', 'DISPLACEMENT');

		$tMap->addValidator('DISPLACEMENT_SUBSIDENCE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DISPLACEMENT_SUBSIDENCE');

		$tMap->addValidator('DISPLACEMENT_SUBSIDENCE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DISPLACEMENT_SUBSIDENCE');

		$tMap->addValidator('DISPLACEMENT_SUBSIDENCE', 'required', 'propel.validator.RequiredValidator', '', 'DISPLACEMENT_SUBSIDENCE');

		$tMap->addValidator('DISPLACEMENT_UPLIFT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DISPLACEMENT_UPLIFT');

		$tMap->addValidator('DISPLACEMENT_UPLIFT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DISPLACEMENT_UPLIFT');

		$tMap->addValidator('DISPLACEMENT_UPLIFT', 'required', 'propel.validator.RequiredValidator', '', 'DISPLACEMENT_UPLIFT');

		$tMap->addValidator('EIL', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EIL');

		$tMap->addValidator('EIL', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EIL');

		$tMap->addValidator('EIL', 'required', 'propel.validator.RequiredValidator', '', 'EIL');

		$tMap->addValidator('EIL_CHARACTERISTICS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EIL_CHARACTERISTICS');

		$tMap->addValidator('EIL_CHARACTERISTICS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EIL_CHARACTERISTICS');

		$tMap->addValidator('EIL_CHARACTERISTICS', 'required', 'propel.validator.RequiredValidator', '', 'EIL_CHARACTERISTICS');

		$tMap->addValidator('EIL_DIST_INLAND', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EIL_DIST_INLAND');

		$tMap->addValidator('EIL_DIST_INLAND', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EIL_DIST_INLAND');

		$tMap->addValidator('EIL_DIST_INLAND', 'required', 'propel.validator.RequiredValidator', '', 'EIL_DIST_INLAND');

		$tMap->addValidator('EIL_ELEVATION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EIL_ELEVATION');

		$tMap->addValidator('EIL_ELEVATION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EIL_ELEVATION');

		$tMap->addValidator('EIL_ELEVATION', 'required', 'propel.validator.RequiredValidator', '', 'EIL_ELEVATION');

		$tMap->addValidator('FAULT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FAULT');

		$tMap->addValidator('FAULT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FAULT');

		$tMap->addValidator('FAULT', 'required', 'propel.validator.RequiredValidator', '', 'FAULT');

		$tMap->addValidator('FAULT_GEOMORPHIC', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FAULT_GEOMORPHIC');

		$tMap->addValidator('FAULT_GEOMORPHIC', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FAULT_GEOMORPHIC');

		$tMap->addValidator('FAULT_GEOMORPHIC', 'required', 'propel.validator.RequiredValidator', '', 'FAULT_GEOMORPHIC');

		$tMap->addValidator('FAULT_OFFSET', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FAULT_OFFSET');

		$tMap->addValidator('FAULT_OFFSET', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FAULT_OFFSET');

		$tMap->addValidator('FAULT_OFFSET', 'required', 'propel.validator.RequiredValidator', '', 'FAULT_OFFSET');

		$tMap->addValidator('FAULT_PALEO', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FAULT_PALEO');

		$tMap->addValidator('FAULT_PALEO', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FAULT_PALEO');

		$tMap->addValidator('FAULT_PALEO', 'required', 'propel.validator.RequiredValidator', '', 'FAULT_PALEO');

		$tMap->addValidator('FAULT_STRIKE_MEASURE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FAULT_STRIKE_MEASURE');

		$tMap->addValidator('FAULT_STRIKE_MEASURE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FAULT_STRIKE_MEASURE');

		$tMap->addValidator('FAULT_STRIKE_MEASURE', 'required', 'propel.validator.RequiredValidator', '', 'FAULT_STRIKE_MEASURE');

		$tMap->addValidator('FAULT_TYPE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FAULT_TYPE');

		$tMap->addValidator('FAULT_TYPE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FAULT_TYPE');

		$tMap->addValidator('FAULT_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'FAULT_TYPE');

		$tMap->addValidator('GMCHANGES', 'maxValue', 'propel.validator.MaxValueValidator', '', 'GMCHANGES');

		$tMap->addValidator('GMCHANGES', 'notMatch', 'propel.validator.NotMatchValidator', '', 'GMCHANGES');

		$tMap->addValidator('GMCHANGES', 'required', 'propel.validator.RequiredValidator', '', 'GMCHANGES');

		$tMap->addValidator('GMCHANGES_BED_MOD', 'maxValue', 'propel.validator.MaxValueValidator', '', 'GMCHANGES_BED_MOD');

		$tMap->addValidator('GMCHANGES_BED_MOD', 'notMatch', 'propel.validator.NotMatchValidator', '', 'GMCHANGES_BED_MOD');

		$tMap->addValidator('GMCHANGES_BED_MOD', 'required', 'propel.validator.RequiredValidator', '', 'GMCHANGES_BED_MOD');

		$tMap->addValidator('GMCHANGES_DEPOSIT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'GMCHANGES_DEPOSIT');

		$tMap->addValidator('GMCHANGES_DEPOSIT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'GMCHANGES_DEPOSIT');

		$tMap->addValidator('GMCHANGES_DEPOSIT', 'required', 'propel.validator.RequiredValidator', '', 'GMCHANGES_DEPOSIT');

		$tMap->addValidator('GMCHANGES_SCOUR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'GMCHANGES_SCOUR');

		$tMap->addValidator('GMCHANGES_SCOUR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'GMCHANGES_SCOUR');

		$tMap->addValidator('GMCHANGES_SCOUR', 'required', 'propel.validator.RequiredValidator', '', 'GMCHANGES_SCOUR');

		$tMap->addValidator('PALEO', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PALEO');

		$tMap->addValidator('PALEO', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PALEO');

		$tMap->addValidator('PALEO', 'required', 'propel.validator.RequiredValidator', '', 'PALEO');

		$tMap->addValidator('PALEO_CHARACTERISTICS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PALEO_CHARACTERISTICS');

		$tMap->addValidator('PALEO_CHARACTERISTICS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PALEO_CHARACTERISTICS');

		$tMap->addValidator('PALEO_CHARACTERISTICS', 'required', 'propel.validator.RequiredValidator', '', 'PALEO_CHARACTERISTICS');

		$tMap->addValidator('PALEO_CORE_SAMPLES', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PALEO_CORE_SAMPLES');

		$tMap->addValidator('PALEO_CORE_SAMPLES', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PALEO_CORE_SAMPLES');

		$tMap->addValidator('PALEO_CORE_SAMPLES', 'required', 'propel.validator.RequiredValidator', '', 'PALEO_CORE_SAMPLES');

		$tMap->addValidator('PALEO_DIST_INLAND', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PALEO_DIST_INLAND');

		$tMap->addValidator('PALEO_DIST_INLAND', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PALEO_DIST_INLAND');

		$tMap->addValidator('PALEO_DIST_INLAND', 'required', 'propel.validator.RequiredValidator', '', 'PALEO_DIST_INLAND');

		$tMap->addValidator('PALEO_ELEVATION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PALEO_ELEVATION');

		$tMap->addValidator('PALEO_ELEVATION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PALEO_ELEVATION');

		$tMap->addValidator('PALEO_ELEVATION', 'required', 'propel.validator.RequiredValidator', '', 'PALEO_ELEVATION');

		$tMap->addValidator('PALEO_OUTCROPS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PALEO_OUTCROPS');

		$tMap->addValidator('PALEO_OUTCROPS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PALEO_OUTCROPS');

		$tMap->addValidator('PALEO_OUTCROPS', 'required', 'propel.validator.RequiredValidator', '', 'PALEO_OUTCROPS');

		$tMap->addValidator('PALEO_SCALE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PALEO_SCALE');

		$tMap->addValidator('PALEO_SCALE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PALEO_SCALE');

		$tMap->addValidator('PALEO_SCALE', 'required', 'propel.validator.RequiredValidator', '', 'PALEO_SCALE');

		$tMap->addValidator('PALEO_SED_PEELS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PALEO_SED_PEELS');

		$tMap->addValidator('PALEO_SED_PEELS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PALEO_SED_PEELS');

		$tMap->addValidator('PALEO_SED_PEELS', 'required', 'propel.validator.RequiredValidator', '', 'PALEO_SED_PEELS');

		$tMap->addValidator('PALEO_SPATIAL_VAR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PALEO_SPATIAL_VAR');

		$tMap->addValidator('PALEO_SPATIAL_VAR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PALEO_SPATIAL_VAR');

		$tMap->addValidator('PALEO_SPATIAL_VAR', 'required', 'propel.validator.RequiredValidator', '', 'PALEO_SPATIAL_VAR');

		$tMap->addValidator('SMSL', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SMSL');

		$tMap->addValidator('SMSL', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SMSL');

		$tMap->addValidator('SMSL', 'required', 'propel.validator.RequiredValidator', '', 'SMSL');

		$tMap->addValidator('SSL_COEFFICIENT_OF_FRICTION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SSL_COEFFICIENT_OF_FRICTION');

		$tMap->addValidator('SSL_COEFFICIENT_OF_FRICTION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SSL_COEFFICIENT_OF_FRICTION');

		$tMap->addValidator('SSL_COEFFICIENT_OF_FRICTION', 'required', 'propel.validator.RequiredValidator', '', 'SSL_COEFFICIENT_OF_FRICTION');

		$tMap->addValidator('SSL_DEPOSITS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SSL_DEPOSITS');

		$tMap->addValidator('SSL_DEPOSITS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SSL_DEPOSITS');

		$tMap->addValidator('SSL_DEPOSITS', 'required', 'propel.validator.RequiredValidator', '', 'SSL_DEPOSITS');

		$tMap->addValidator('SSL_SCARS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SSL_SCARS');

		$tMap->addValidator('SSL_SCARS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SSL_SCARS');

		$tMap->addValidator('SSL_SCARS', 'required', 'propel.validator.RequiredValidator', '', 'SSL_SCARS');

		$tMap->addValidator('TDCBM', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TDCBM');

		$tMap->addValidator('TDCBM', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TDCBM');

		$tMap->addValidator('TDCBM', 'required', 'propel.validator.RequiredValidator', '', 'TDCBM');

		$tMap->addValidator('TDCBM_CHARACTERISTICS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TDCBM_CHARACTERISTICS');

		$tMap->addValidator('TDCBM_CHARACTERISTICS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TDCBM_CHARACTERISTICS');

		$tMap->addValidator('TDCBM_CHARACTERISTICS', 'required', 'propel.validator.RequiredValidator', '', 'TDCBM_CHARACTERISTICS');

		$tMap->addValidator('TDCBM_DIST_INLAND', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TDCBM_DIST_INLAND');

		$tMap->addValidator('TDCBM_DIST_INLAND', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TDCBM_DIST_INLAND');

		$tMap->addValidator('TDCBM_DIST_INLAND', 'required', 'propel.validator.RequiredValidator', '', 'TDCBM_DIST_INLAND');

		$tMap->addValidator('TDCBM_ELEVATION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TDCBM_ELEVATION');

		$tMap->addValidator('TDCBM_ELEVATION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TDCBM_ELEVATION');

		$tMap->addValidator('TDCBM_ELEVATION', 'required', 'propel.validator.RequiredValidator', '', 'TDCBM_ELEVATION');

		$tMap->addValidator('TDCBM_SCALE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TDCBM_SCALE');

		$tMap->addValidator('TDCBM_SCALE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TDCBM_SCALE');

		$tMap->addValidator('TDCBM_SCALE', 'required', 'propel.validator.RequiredValidator', '', 'TDCBM_SCALE');

		$tMap->addValidator('TDCBM_SPATIAL_VAR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TDCBM_SPATIAL_VAR');

		$tMap->addValidator('TDCBM_SPATIAL_VAR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TDCBM_SPATIAL_VAR');

		$tMap->addValidator('TDCBM_SPATIAL_VAR', 'required', 'propel.validator.RequiredValidator', '', 'TDCBM_SPATIAL_VAR');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_GEOLOGICAL_DATA_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_GEOLOGICAL_DATA_ID');

		$tMap->addValidator('TSUNAMI_GEOLOGICAL_DATA_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_GEOLOGICAL_DATA_ID');

		$tMap->addValidator('TSUNAMI_GEOLOGICAL_DATA_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_GEOLOGICAL_DATA_ID');

		$tMap->addValidator('TSUNAMI_GEOLOGICAL_DATA_ID', 'unique', 'propel.validator.UniqueValidator', '', 'TSUNAMI_GEOLOGICAL_DATA_ID');

	} // doBuild()

} // TsunamiGeologicalDataMapBuilder
