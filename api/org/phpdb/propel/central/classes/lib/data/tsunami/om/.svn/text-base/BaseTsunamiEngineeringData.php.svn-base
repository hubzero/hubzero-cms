<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/tsunami/TsunamiEngineeringDataPeer.php';

/**
 * Base class that represents a row from the 'TSUNAMI_ENGINEERING_DATA' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiEngineeringData extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        TsunamiEngineeringDataPeer
	 */
	protected static $peer;


	/**
	 * The value for the tsunami_engineering_data_id field.
	 * @var        double
	 */
	protected $tsunami_engineering_data_id;


	/**
	 * The value for the event field.
	 * @var        double
	 */
	protected $event;


	/**
	 * The value for the event_sensor_data field.
	 * @var        double
	 */
	protected $event_sensor_data;


	/**
	 * The value for the event_video field.
	 * @var        double
	 */
	protected $event_video;


	/**
	 * The value for the geotech field.
	 * @var        double
	 */
	protected $geotech;


	/**
	 * The value for the geotech_damage_descr field.
	 * @var        double
	 */
	protected $geotech_damage_descr;


	/**
	 * The value for the geotech_site_char field.
	 * @var        double
	 */
	protected $geotech_site_char;


	/**
	 * The value for the geotech_soil_char field.
	 * @var        double
	 */
	protected $geotech_soil_char;


	/**
	 * The value for the geotech_vul_assessment field.
	 * @var        double
	 */
	protected $geotech_vul_assessment;


	/**
	 * The value for the hm field.
	 * @var        double
	 */
	protected $hm;


	/**
	 * The value for the hm_evac_plan_maps field.
	 * @var        double
	 */
	protected $hm_evac_plan_maps;


	/**
	 * The value for the hm_fault_maps field.
	 * @var        double
	 */
	protected $hm_fault_maps;


	/**
	 * The value for the hm_hazard_assessment field.
	 * @var        double
	 */
	protected $hm_hazard_assessment;


	/**
	 * The value for the hm_hazard_maps field.
	 * @var        double
	 */
	protected $hm_hazard_maps;


	/**
	 * The value for the hm_shelter_locations field.
	 * @var        double
	 */
	protected $hm_shelter_locations;


	/**
	 * The value for the lifeline field.
	 * @var        double
	 */
	protected $lifeline;


	/**
	 * The value for the lifeline_damage_descr field.
	 * @var        double
	 */
	protected $lifeline_damage_descr;


	/**
	 * The value for the lifeline_design field.
	 * @var        double
	 */
	protected $lifeline_design;


	/**
	 * The value for the lifeline_seismic_design field.
	 * @var        double
	 */
	protected $lifeline_seismic_design;


	/**
	 * The value for the lifeline_type field.
	 * @var        double
	 */
	protected $lifeline_type;


	/**
	 * The value for the lifeline_vul_assessment field.
	 * @var        double
	 */
	protected $lifeline_vul_assessment;


	/**
	 * The value for the lifeline_year field.
	 * @var        double
	 */
	protected $lifeline_year;


	/**
	 * The value for the structure field.
	 * @var        double
	 */
	protected $structure;


	/**
	 * The value for the structure_damage_descr field.
	 * @var        double
	 */
	protected $structure_damage_descr;


	/**
	 * The value for the structure_design field.
	 * @var        double
	 */
	protected $structure_design;


	/**
	 * The value for the structure_seismic_design field.
	 * @var        double
	 */
	protected $structure_seismic_design;


	/**
	 * The value for the structure_type field.
	 * @var        double
	 */
	protected $structure_type;


	/**
	 * The value for the structure_vul_assessment field.
	 * @var        double
	 */
	protected $structure_vul_assessment;


	/**
	 * The value for the structure_year field.
	 * @var        double
	 */
	protected $structure_year;


	/**
	 * The value for the tsunami_doc_lib_id field.
	 * @var        double
	 */
	protected $tsunami_doc_lib_id;

	/**
	 * @var        TsunamiDocLib
	 */
	protected $aTsunamiDocLib;

	/**
	 * Flag to prevent endless save loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInSave = false;

	/**
	 * Flag to prevent endless validation loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInValidation = false;

	/**
	 * Get the [tsunami_engineering_data_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->tsunami_engineering_data_id;
	}

	/**
	 * Get the [event] column value.
	 * 
	 * @return     double
	 */
	public function getEvent()
	{

		return $this->event;
	}

	/**
	 * Get the [event_sensor_data] column value.
	 * 
	 * @return     double
	 */
	public function getEventSensorData()
	{

		return $this->event_sensor_data;
	}

	/**
	 * Get the [event_video] column value.
	 * 
	 * @return     double
	 */
	public function getEventVideo()
	{

		return $this->event_video;
	}

	/**
	 * Get the [geotech] column value.
	 * 
	 * @return     double
	 */
	public function getGeotech()
	{

		return $this->geotech;
	}

	/**
	 * Get the [geotech_damage_descr] column value.
	 * 
	 * @return     double
	 */
	public function getGeotechDamageDescr()
	{

		return $this->geotech_damage_descr;
	}

	/**
	 * Get the [geotech_site_char] column value.
	 * 
	 * @return     double
	 */
	public function getGeotechSiteChar()
	{

		return $this->geotech_site_char;
	}

	/**
	 * Get the [geotech_soil_char] column value.
	 * 
	 * @return     double
	 */
	public function getGeotechSoilChar()
	{

		return $this->geotech_soil_char;
	}

	/**
	 * Get the [geotech_vul_assessment] column value.
	 * 
	 * @return     double
	 */
	public function getGeotechVulAssessment()
	{

		return $this->geotech_vul_assessment;
	}

	/**
	 * Get the [hm] column value.
	 * 
	 * @return     double
	 */
	public function getHm()
	{

		return $this->hm;
	}

	/**
	 * Get the [hm_evac_plan_maps] column value.
	 * 
	 * @return     double
	 */
	public function getHmEvacPlanMaps()
	{

		return $this->hm_evac_plan_maps;
	}

	/**
	 * Get the [hm_fault_maps] column value.
	 * 
	 * @return     double
	 */
	public function getHmFaultMaps()
	{

		return $this->hm_fault_maps;
	}

	/**
	 * Get the [hm_hazard_assessment] column value.
	 * 
	 * @return     double
	 */
	public function getHmHazardAssessment()
	{

		return $this->hm_hazard_assessment;
	}

	/**
	 * Get the [hm_hazard_maps] column value.
	 * 
	 * @return     double
	 */
	public function getHmHazardMaps()
	{

		return $this->hm_hazard_maps;
	}

	/**
	 * Get the [hm_shelter_locations] column value.
	 * 
	 * @return     double
	 */
	public function getHmShelterLocations()
	{

		return $this->hm_shelter_locations;
	}

	/**
	 * Get the [lifeline] column value.
	 * 
	 * @return     double
	 */
	public function getLifeline()
	{

		return $this->lifeline;
	}

	/**
	 * Get the [lifeline_damage_descr] column value.
	 * 
	 * @return     double
	 */
	public function getLifelineDamageDescription()
	{

		return $this->lifeline_damage_descr;
	}

	/**
	 * Get the [lifeline_design] column value.
	 * 
	 * @return     double
	 */
	public function getLifelineDesign()
	{

		return $this->lifeline_design;
	}

	/**
	 * Get the [lifeline_seismic_design] column value.
	 * 
	 * @return     double
	 */
	public function getLifelineSeismicDesign()
	{

		return $this->lifeline_seismic_design;
	}

	/**
	 * Get the [lifeline_type] column value.
	 * 
	 * @return     double
	 */
	public function getLifelineType()
	{

		return $this->lifeline_type;
	}

	/**
	 * Get the [lifeline_vul_assessment] column value.
	 * 
	 * @return     double
	 */
	public function getLifelineVulAssessment()
	{

		return $this->lifeline_vul_assessment;
	}

	/**
	 * Get the [lifeline_year] column value.
	 * 
	 * @return     double
	 */
	public function getLifelineYear()
	{

		return $this->lifeline_year;
	}

	/**
	 * Get the [structure] column value.
	 * 
	 * @return     double
	 */
	public function getStructure()
	{

		return $this->structure;
	}

	/**
	 * Get the [structure_damage_descr] column value.
	 * 
	 * @return     double
	 */
	public function getStructureDamageDescription()
	{

		return $this->structure_damage_descr;
	}

	/**
	 * Get the [structure_design] column value.
	 * 
	 * @return     double
	 */
	public function getStructureDesign()
	{

		return $this->structure_design;
	}

	/**
	 * Get the [structure_seismic_design] column value.
	 * 
	 * @return     double
	 */
	public function getStructureSeismicDesign()
	{

		return $this->structure_seismic_design;
	}

	/**
	 * Get the [structure_type] column value.
	 * 
	 * @return     double
	 */
	public function getStructureType()
	{

		return $this->structure_type;
	}

	/**
	 * Get the [structure_vul_assessment] column value.
	 * 
	 * @return     double
	 */
	public function getStructureVulAssessment()
	{

		return $this->structure_vul_assessment;
	}

	/**
	 * Get the [structure_year] column value.
	 * 
	 * @return     double
	 */
	public function getStructureYear()
	{

		return $this->structure_year;
	}

	/**
	 * Get the [tsunami_doc_lib_id] column value.
	 * 
	 * @return     double
	 */
	public function getTsunamiDocLibId()
	{

		return $this->tsunami_doc_lib_id;
	}

	/**
	 * Set the value of [tsunami_engineering_data_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->tsunami_engineering_data_id !== $v) {
			$this->tsunami_engineering_data_id = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID;
		}

	} // setId()

	/**
	 * Set the value of [event] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEvent($v)
	{

		if ($this->event !== $v) {
			$this->event = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::EVENT;
		}

	} // setEvent()

	/**
	 * Set the value of [event_sensor_data] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEventSensorData($v)
	{

		if ($this->event_sensor_data !== $v) {
			$this->event_sensor_data = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::EVENT_SENSOR_DATA;
		}

	} // setEventSensorData()

	/**
	 * Set the value of [event_video] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEventVideo($v)
	{

		if ($this->event_video !== $v) {
			$this->event_video = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::EVENT_VIDEO;
		}

	} // setEventVideo()

	/**
	 * Set the value of [geotech] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setGeotech($v)
	{

		if ($this->geotech !== $v) {
			$this->geotech = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::GEOTECH;
		}

	} // setGeotech()

	/**
	 * Set the value of [geotech_damage_descr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setGeotechDamageDescr($v)
	{

		if ($this->geotech_damage_descr !== $v) {
			$this->geotech_damage_descr = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::GEOTECH_DAMAGE_DESCR;
		}

	} // setGeotechDamageDescr()

	/**
	 * Set the value of [geotech_site_char] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setGeotechSiteChar($v)
	{

		if ($this->geotech_site_char !== $v) {
			$this->geotech_site_char = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::GEOTECH_SITE_CHAR;
		}

	} // setGeotechSiteChar()

	/**
	 * Set the value of [geotech_soil_char] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setGeotechSoilChar($v)
	{

		if ($this->geotech_soil_char !== $v) {
			$this->geotech_soil_char = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::GEOTECH_SOIL_CHAR;
		}

	} // setGeotechSoilChar()

	/**
	 * Set the value of [geotech_vul_assessment] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setGeotechVulAssessment($v)
	{

		if ($this->geotech_vul_assessment !== $v) {
			$this->geotech_vul_assessment = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::GEOTECH_VUL_ASSESSMENT;
		}

	} // setGeotechVulAssessment()

	/**
	 * Set the value of [hm] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setHm($v)
	{

		if ($this->hm !== $v) {
			$this->hm = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::HM;
		}

	} // setHm()

	/**
	 * Set the value of [hm_evac_plan_maps] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setHmEvacPlanMaps($v)
	{

		if ($this->hm_evac_plan_maps !== $v) {
			$this->hm_evac_plan_maps = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::HM_EVAC_PLAN_MAPS;
		}

	} // setHmEvacPlanMaps()

	/**
	 * Set the value of [hm_fault_maps] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setHmFaultMaps($v)
	{

		if ($this->hm_fault_maps !== $v) {
			$this->hm_fault_maps = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::HM_FAULT_MAPS;
		}

	} // setHmFaultMaps()

	/**
	 * Set the value of [hm_hazard_assessment] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setHmHazardAssessment($v)
	{

		if ($this->hm_hazard_assessment !== $v) {
			$this->hm_hazard_assessment = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::HM_HAZARD_ASSESSMENT;
		}

	} // setHmHazardAssessment()

	/**
	 * Set the value of [hm_hazard_maps] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setHmHazardMaps($v)
	{

		if ($this->hm_hazard_maps !== $v) {
			$this->hm_hazard_maps = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::HM_HAZARD_MAPS;
		}

	} // setHmHazardMaps()

	/**
	 * Set the value of [hm_shelter_locations] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setHmShelterLocations($v)
	{

		if ($this->hm_shelter_locations !== $v) {
			$this->hm_shelter_locations = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::HM_SHELTER_LOCATIONS;
		}

	} // setHmShelterLocations()

	/**
	 * Set the value of [lifeline] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setLifeline($v)
	{

		if ($this->lifeline !== $v) {
			$this->lifeline = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::LIFELINE;
		}

	} // setLifeline()

	/**
	 * Set the value of [lifeline_damage_descr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setLifelineDamageDescription($v)
	{

		if ($this->lifeline_damage_descr !== $v) {
			$this->lifeline_damage_descr = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::LIFELINE_DAMAGE_DESCR;
		}

	} // setLifelineDamageDescription()

	/**
	 * Set the value of [lifeline_design] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setLifelineDesign($v)
	{

		if ($this->lifeline_design !== $v) {
			$this->lifeline_design = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::LIFELINE_DESIGN;
		}

	} // setLifelineDesign()

	/**
	 * Set the value of [lifeline_seismic_design] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setLifelineSeismicDesign($v)
	{

		if ($this->lifeline_seismic_design !== $v) {
			$this->lifeline_seismic_design = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::LIFELINE_SEISMIC_DESIGN;
		}

	} // setLifelineSeismicDesign()

	/**
	 * Set the value of [lifeline_type] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setLifelineType($v)
	{

		if ($this->lifeline_type !== $v) {
			$this->lifeline_type = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::LIFELINE_TYPE;
		}

	} // setLifelineType()

	/**
	 * Set the value of [lifeline_vul_assessment] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setLifelineVulAssessment($v)
	{

		if ($this->lifeline_vul_assessment !== $v) {
			$this->lifeline_vul_assessment = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::LIFELINE_VUL_ASSESSMENT;
		}

	} // setLifelineVulAssessment()

	/**
	 * Set the value of [lifeline_year] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setLifelineYear($v)
	{

		if ($this->lifeline_year !== $v) {
			$this->lifeline_year = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::LIFELINE_YEAR;
		}

	} // setLifelineYear()

	/**
	 * Set the value of [structure] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setStructure($v)
	{

		if ($this->structure !== $v) {
			$this->structure = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::STRUCTURE;
		}

	} // setStructure()

	/**
	 * Set the value of [structure_damage_descr] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setStructureDamageDescription($v)
	{

		if ($this->structure_damage_descr !== $v) {
			$this->structure_damage_descr = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::STRUCTURE_DAMAGE_DESCR;
		}

	} // setStructureDamageDescription()

	/**
	 * Set the value of [structure_design] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setStructureDesign($v)
	{

		if ($this->structure_design !== $v) {
			$this->structure_design = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::STRUCTURE_DESIGN;
		}

	} // setStructureDesign()

	/**
	 * Set the value of [structure_seismic_design] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setStructureSeismicDesign($v)
	{

		if ($this->structure_seismic_design !== $v) {
			$this->structure_seismic_design = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::STRUCTURE_SEISMIC_DESIGN;
		}

	} // setStructureSeismicDesign()

	/**
	 * Set the value of [structure_type] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setStructureType($v)
	{

		if ($this->structure_type !== $v) {
			$this->structure_type = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::STRUCTURE_TYPE;
		}

	} // setStructureType()

	/**
	 * Set the value of [structure_vul_assessment] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setStructureVulAssessment($v)
	{

		if ($this->structure_vul_assessment !== $v) {
			$this->structure_vul_assessment = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::STRUCTURE_VUL_ASSESSMENT;
		}

	} // setStructureVulAssessment()

	/**
	 * Set the value of [structure_year] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setStructureYear($v)
	{

		if ($this->structure_year !== $v) {
			$this->structure_year = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::STRUCTURE_YEAR;
		}

	} // setStructureYear()

	/**
	 * Set the value of [tsunami_doc_lib_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTsunamiDocLibId($v)
	{

		if ($this->tsunami_doc_lib_id !== $v) {
			$this->tsunami_doc_lib_id = $v;
			$this->modifiedColumns[] = TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID;
		}

		if ($this->aTsunamiDocLib !== null && $this->aTsunamiDocLib->getId() !== $v) {
			$this->aTsunamiDocLib = null;
		}

	} // setTsunamiDocLibId()

	/**
	 * Hydrates (populates) the object variables with values from the database resultset.
	 *
	 * An offset (1-based "start column") is specified so that objects can be hydrated
	 * with a subset of the columns in the resultset rows.  This is needed, for example,
	 * for results of JOIN queries where the resultset row includes columns from two or
	 * more tables.
	 *
	 * @param      ResultSet $rs The ResultSet class with cursor advanced to desired record pos.
	 * @param      int $startcol 1-based offset column which indicates which restultset column to start with.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->tsunami_engineering_data_id = $rs->getFloat($startcol + 0);

			$this->event = $rs->getFloat($startcol + 1);

			$this->event_sensor_data = $rs->getFloat($startcol + 2);

			$this->event_video = $rs->getFloat($startcol + 3);

			$this->geotech = $rs->getFloat($startcol + 4);

			$this->geotech_damage_descr = $rs->getFloat($startcol + 5);

			$this->geotech_site_char = $rs->getFloat($startcol + 6);

			$this->geotech_soil_char = $rs->getFloat($startcol + 7);

			$this->geotech_vul_assessment = $rs->getFloat($startcol + 8);

			$this->hm = $rs->getFloat($startcol + 9);

			$this->hm_evac_plan_maps = $rs->getFloat($startcol + 10);

			$this->hm_fault_maps = $rs->getFloat($startcol + 11);

			$this->hm_hazard_assessment = $rs->getFloat($startcol + 12);

			$this->hm_hazard_maps = $rs->getFloat($startcol + 13);

			$this->hm_shelter_locations = $rs->getFloat($startcol + 14);

			$this->lifeline = $rs->getFloat($startcol + 15);

			$this->lifeline_damage_descr = $rs->getFloat($startcol + 16);

			$this->lifeline_design = $rs->getFloat($startcol + 17);

			$this->lifeline_seismic_design = $rs->getFloat($startcol + 18);

			$this->lifeline_type = $rs->getFloat($startcol + 19);

			$this->lifeline_vul_assessment = $rs->getFloat($startcol + 20);

			$this->lifeline_year = $rs->getFloat($startcol + 21);

			$this->structure = $rs->getFloat($startcol + 22);

			$this->structure_damage_descr = $rs->getFloat($startcol + 23);

			$this->structure_design = $rs->getFloat($startcol + 24);

			$this->structure_seismic_design = $rs->getFloat($startcol + 25);

			$this->structure_type = $rs->getFloat($startcol + 26);

			$this->structure_vul_assessment = $rs->getFloat($startcol + 27);

			$this->structure_year = $rs->getFloat($startcol + 28);

			$this->tsunami_doc_lib_id = $rs->getFloat($startcol + 29);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 30; // 30 = TsunamiEngineeringDataPeer::NUM_COLUMNS - TsunamiEngineeringDataPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating TsunamiEngineeringData object", $e);
		}
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      Connection $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(TsunamiEngineeringDataPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			TsunamiEngineeringDataPeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Stores the object in the database.  If the object is new,
	 * it inserts it; otherwise an update is performed.  This method
	 * wraps the doSave() worker method in a transaction.
	 *
	 * @param      Connection $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(TsunamiEngineeringDataPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			$affectedRows = $this->doSave($con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Stores the object in the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All related objects are also updated in this method.
	 *
	 * @param      Connection $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave($con)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;


			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aTsunamiDocLib !== null) {
				if ($this->aTsunamiDocLib->isModified()) {
					$affectedRows += $this->aTsunamiDocLib->save($con);
				}
				$this->setTsunamiDocLib($this->aTsunamiDocLib);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = TsunamiEngineeringDataPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += TsunamiEngineeringDataPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;
		}
		return $affectedRows;
	} // doSave()

	/**
	 * Array of ValidationFailed objects.
	 * @var        array ValidationFailed[]
	 */
	protected $validationFailures = array();

	/**
	 * Gets any ValidationFailed objects that resulted from last call to validate().
	 *
	 *
	 * @return     array ValidationFailed[]
	 * @see        validate()
	 */
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	/**
	 * Validates the objects modified field values and all objects related to this table.
	 *
	 * If $columns is either a column name or an array of column names
	 * only those columns are validated.
	 *
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
	 * @see        doValidate()
	 * @see        getValidationFailures()
	 */
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aTsunamiDocLib !== null) {
				if (!$this->aTsunamiDocLib->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aTsunamiDocLib->getValidationFailures());
				}
			}


			if (($retval = TsunamiEngineeringDataPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 * Retrieves a field from the object by name passed in as a string.
	 *
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants TYPE_PHPNAME,
	 *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = TsunamiEngineeringDataPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getId();
				break;
			case 1:
				return $this->getEvent();
				break;
			case 2:
				return $this->getEventSensorData();
				break;
			case 3:
				return $this->getEventVideo();
				break;
			case 4:
				return $this->getGeotech();
				break;
			case 5:
				return $this->getGeotechDamageDescr();
				break;
			case 6:
				return $this->getGeotechSiteChar();
				break;
			case 7:
				return $this->getGeotechSoilChar();
				break;
			case 8:
				return $this->getGeotechVulAssessment();
				break;
			case 9:
				return $this->getHm();
				break;
			case 10:
				return $this->getHmEvacPlanMaps();
				break;
			case 11:
				return $this->getHmFaultMaps();
				break;
			case 12:
				return $this->getHmHazardAssessment();
				break;
			case 13:
				return $this->getHmHazardMaps();
				break;
			case 14:
				return $this->getHmShelterLocations();
				break;
			case 15:
				return $this->getLifeline();
				break;
			case 16:
				return $this->getLifelineDamageDescription();
				break;
			case 17:
				return $this->getLifelineDesign();
				break;
			case 18:
				return $this->getLifelineSeismicDesign();
				break;
			case 19:
				return $this->getLifelineType();
				break;
			case 20:
				return $this->getLifelineVulAssessment();
				break;
			case 21:
				return $this->getLifelineYear();
				break;
			case 22:
				return $this->getStructure();
				break;
			case 23:
				return $this->getStructureDamageDescription();
				break;
			case 24:
				return $this->getStructureDesign();
				break;
			case 25:
				return $this->getStructureSeismicDesign();
				break;
			case 26:
				return $this->getStructureType();
				break;
			case 27:
				return $this->getStructureVulAssessment();
				break;
			case 28:
				return $this->getStructureYear();
				break;
			case 29:
				return $this->getTsunamiDocLibId();
				break;
			default:
				return null;
				break;
		} // switch()
	}

	/**
	 * Exports the object as an array.
	 *
	 * You can specify the key type of the array by passing one of the class
	 * type constants.
	 *
	 * @param      string $keyType One of the class type constants TYPE_PHPNAME,
	 *                        TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = TsunamiEngineeringDataPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getEvent(),
			$keys[2] => $this->getEventSensorData(),
			$keys[3] => $this->getEventVideo(),
			$keys[4] => $this->getGeotech(),
			$keys[5] => $this->getGeotechDamageDescr(),
			$keys[6] => $this->getGeotechSiteChar(),
			$keys[7] => $this->getGeotechSoilChar(),
			$keys[8] => $this->getGeotechVulAssessment(),
			$keys[9] => $this->getHm(),
			$keys[10] => $this->getHmEvacPlanMaps(),
			$keys[11] => $this->getHmFaultMaps(),
			$keys[12] => $this->getHmHazardAssessment(),
			$keys[13] => $this->getHmHazardMaps(),
			$keys[14] => $this->getHmShelterLocations(),
			$keys[15] => $this->getLifeline(),
			$keys[16] => $this->getLifelineDamageDescription(),
			$keys[17] => $this->getLifelineDesign(),
			$keys[18] => $this->getLifelineSeismicDesign(),
			$keys[19] => $this->getLifelineType(),
			$keys[20] => $this->getLifelineVulAssessment(),
			$keys[21] => $this->getLifelineYear(),
			$keys[22] => $this->getStructure(),
			$keys[23] => $this->getStructureDamageDescription(),
			$keys[24] => $this->getStructureDesign(),
			$keys[25] => $this->getStructureSeismicDesign(),
			$keys[26] => $this->getStructureType(),
			$keys[27] => $this->getStructureVulAssessment(),
			$keys[28] => $this->getStructureYear(),
			$keys[29] => $this->getTsunamiDocLibId(),
		);
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants TYPE_PHPNAME,
	 *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = TsunamiEngineeringDataPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setEvent($value);
				break;
			case 2:
				$this->setEventSensorData($value);
				break;
			case 3:
				$this->setEventVideo($value);
				break;
			case 4:
				$this->setGeotech($value);
				break;
			case 5:
				$this->setGeotechDamageDescr($value);
				break;
			case 6:
				$this->setGeotechSiteChar($value);
				break;
			case 7:
				$this->setGeotechSoilChar($value);
				break;
			case 8:
				$this->setGeotechVulAssessment($value);
				break;
			case 9:
				$this->setHm($value);
				break;
			case 10:
				$this->setHmEvacPlanMaps($value);
				break;
			case 11:
				$this->setHmFaultMaps($value);
				break;
			case 12:
				$this->setHmHazardAssessment($value);
				break;
			case 13:
				$this->setHmHazardMaps($value);
				break;
			case 14:
				$this->setHmShelterLocations($value);
				break;
			case 15:
				$this->setLifeline($value);
				break;
			case 16:
				$this->setLifelineDamageDescription($value);
				break;
			case 17:
				$this->setLifelineDesign($value);
				break;
			case 18:
				$this->setLifelineSeismicDesign($value);
				break;
			case 19:
				$this->setLifelineType($value);
				break;
			case 20:
				$this->setLifelineVulAssessment($value);
				break;
			case 21:
				$this->setLifelineYear($value);
				break;
			case 22:
				$this->setStructure($value);
				break;
			case 23:
				$this->setStructureDamageDescription($value);
				break;
			case 24:
				$this->setStructureDesign($value);
				break;
			case 25:
				$this->setStructureSeismicDesign($value);
				break;
			case 26:
				$this->setStructureType($value);
				break;
			case 27:
				$this->setStructureVulAssessment($value);
				break;
			case 28:
				$this->setStructureYear($value);
				break;
			case 29:
				$this->setTsunamiDocLibId($value);
				break;
		} // switch()
	}

	/**
	 * Populates the object using an array.
	 *
	 * This is particularly useful when populating an object from one of the
	 * request arrays (e.g. $_POST).  This method goes through the column
	 * names, checking to see whether a matching key exists in populated
	 * array. If so the setByName() method is called for that column.
	 *
	 * You can specify the key type of the array by additionally passing one
	 * of the class type constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME,
	 * TYPE_NUM. The default key type is the column's phpname (e.g. 'authorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = TsunamiEngineeringDataPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setEvent($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEventSensorData($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setEventVideo($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setGeotech($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setGeotechDamageDescr($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setGeotechSiteChar($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setGeotechSoilChar($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setGeotechVulAssessment($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setHm($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setHmEvacPlanMaps($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setHmFaultMaps($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setHmHazardAssessment($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setHmHazardMaps($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setHmShelterLocations($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setLifeline($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setLifelineDamageDescription($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setLifelineDesign($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setLifelineSeismicDesign($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setLifelineType($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setLifelineVulAssessment($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setLifelineYear($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setStructure($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setStructureDamageDescription($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setStructureDesign($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setStructureSeismicDesign($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setStructureType($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setStructureVulAssessment($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setStructureYear($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setTsunamiDocLibId($arr[$keys[29]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(TsunamiEngineeringDataPeer::DATABASE_NAME);

		if ($this->isColumnModified(TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID)) $criteria->add(TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID, $this->tsunami_engineering_data_id);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::EVENT)) $criteria->add(TsunamiEngineeringDataPeer::EVENT, $this->event);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::EVENT_SENSOR_DATA)) $criteria->add(TsunamiEngineeringDataPeer::EVENT_SENSOR_DATA, $this->event_sensor_data);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::EVENT_VIDEO)) $criteria->add(TsunamiEngineeringDataPeer::EVENT_VIDEO, $this->event_video);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::GEOTECH)) $criteria->add(TsunamiEngineeringDataPeer::GEOTECH, $this->geotech);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::GEOTECH_DAMAGE_DESCR)) $criteria->add(TsunamiEngineeringDataPeer::GEOTECH_DAMAGE_DESCR, $this->geotech_damage_descr);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::GEOTECH_SITE_CHAR)) $criteria->add(TsunamiEngineeringDataPeer::GEOTECH_SITE_CHAR, $this->geotech_site_char);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::GEOTECH_SOIL_CHAR)) $criteria->add(TsunamiEngineeringDataPeer::GEOTECH_SOIL_CHAR, $this->geotech_soil_char);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::GEOTECH_VUL_ASSESSMENT)) $criteria->add(TsunamiEngineeringDataPeer::GEOTECH_VUL_ASSESSMENT, $this->geotech_vul_assessment);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::HM)) $criteria->add(TsunamiEngineeringDataPeer::HM, $this->hm);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::HM_EVAC_PLAN_MAPS)) $criteria->add(TsunamiEngineeringDataPeer::HM_EVAC_PLAN_MAPS, $this->hm_evac_plan_maps);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::HM_FAULT_MAPS)) $criteria->add(TsunamiEngineeringDataPeer::HM_FAULT_MAPS, $this->hm_fault_maps);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::HM_HAZARD_ASSESSMENT)) $criteria->add(TsunamiEngineeringDataPeer::HM_HAZARD_ASSESSMENT, $this->hm_hazard_assessment);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::HM_HAZARD_MAPS)) $criteria->add(TsunamiEngineeringDataPeer::HM_HAZARD_MAPS, $this->hm_hazard_maps);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::HM_SHELTER_LOCATIONS)) $criteria->add(TsunamiEngineeringDataPeer::HM_SHELTER_LOCATIONS, $this->hm_shelter_locations);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE)) $criteria->add(TsunamiEngineeringDataPeer::LIFELINE, $this->lifeline);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_DAMAGE_DESCR)) $criteria->add(TsunamiEngineeringDataPeer::LIFELINE_DAMAGE_DESCR, $this->lifeline_damage_descr);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_DESIGN)) $criteria->add(TsunamiEngineeringDataPeer::LIFELINE_DESIGN, $this->lifeline_design);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_SEISMIC_DESIGN)) $criteria->add(TsunamiEngineeringDataPeer::LIFELINE_SEISMIC_DESIGN, $this->lifeline_seismic_design);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_TYPE)) $criteria->add(TsunamiEngineeringDataPeer::LIFELINE_TYPE, $this->lifeline_type);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_VUL_ASSESSMENT)) $criteria->add(TsunamiEngineeringDataPeer::LIFELINE_VUL_ASSESSMENT, $this->lifeline_vul_assessment);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::LIFELINE_YEAR)) $criteria->add(TsunamiEngineeringDataPeer::LIFELINE_YEAR, $this->lifeline_year);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE)) $criteria->add(TsunamiEngineeringDataPeer::STRUCTURE, $this->structure);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_DAMAGE_DESCR)) $criteria->add(TsunamiEngineeringDataPeer::STRUCTURE_DAMAGE_DESCR, $this->structure_damage_descr);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_DESIGN)) $criteria->add(TsunamiEngineeringDataPeer::STRUCTURE_DESIGN, $this->structure_design);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_SEISMIC_DESIGN)) $criteria->add(TsunamiEngineeringDataPeer::STRUCTURE_SEISMIC_DESIGN, $this->structure_seismic_design);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_TYPE)) $criteria->add(TsunamiEngineeringDataPeer::STRUCTURE_TYPE, $this->structure_type);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_VUL_ASSESSMENT)) $criteria->add(TsunamiEngineeringDataPeer::STRUCTURE_VUL_ASSESSMENT, $this->structure_vul_assessment);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::STRUCTURE_YEAR)) $criteria->add(TsunamiEngineeringDataPeer::STRUCTURE_YEAR, $this->structure_year);
		if ($this->isColumnModified(TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID)) $criteria->add(TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID, $this->tsunami_doc_lib_id);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(TsunamiEngineeringDataPeer::DATABASE_NAME);

		$criteria->add(TsunamiEngineeringDataPeer::TSUNAMI_ENGINEERING_DATA_ID, $this->tsunami_engineering_data_id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     double
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (tsunami_engineering_data_id column).
	 *
	 * @param      double $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of TsunamiEngineeringData (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setEvent($this->event);

		$copyObj->setEventSensorData($this->event_sensor_data);

		$copyObj->setEventVideo($this->event_video);

		$copyObj->setGeotech($this->geotech);

		$copyObj->setGeotechDamageDescr($this->geotech_damage_descr);

		$copyObj->setGeotechSiteChar($this->geotech_site_char);

		$copyObj->setGeotechSoilChar($this->geotech_soil_char);

		$copyObj->setGeotechVulAssessment($this->geotech_vul_assessment);

		$copyObj->setHm($this->hm);

		$copyObj->setHmEvacPlanMaps($this->hm_evac_plan_maps);

		$copyObj->setHmFaultMaps($this->hm_fault_maps);

		$copyObj->setHmHazardAssessment($this->hm_hazard_assessment);

		$copyObj->setHmHazardMaps($this->hm_hazard_maps);

		$copyObj->setHmShelterLocations($this->hm_shelter_locations);

		$copyObj->setLifeline($this->lifeline);

		$copyObj->setLifelineDamageDescription($this->lifeline_damage_descr);

		$copyObj->setLifelineDesign($this->lifeline_design);

		$copyObj->setLifelineSeismicDesign($this->lifeline_seismic_design);

		$copyObj->setLifelineType($this->lifeline_type);

		$copyObj->setLifelineVulAssessment($this->lifeline_vul_assessment);

		$copyObj->setLifelineYear($this->lifeline_year);

		$copyObj->setStructure($this->structure);

		$copyObj->setStructureDamageDescription($this->structure_damage_descr);

		$copyObj->setStructureDesign($this->structure_design);

		$copyObj->setStructureSeismicDesign($this->structure_seismic_design);

		$copyObj->setStructureType($this->structure_type);

		$copyObj->setStructureVulAssessment($this->structure_vul_assessment);

		$copyObj->setStructureYear($this->structure_year);

		$copyObj->setTsunamiDocLibId($this->tsunami_doc_lib_id);


		$copyObj->setNew(true);

		$copyObj->setId(NULL); // this is a pkey column, so set to default value

	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     TsunamiEngineeringData Clone of current object.
	 * @throws     PropelException
	 */
	public function copy($deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	/**
	 * Returns a peer instance associated with this om.
	 *
	 * Since Peer classes are not to have any instance attributes, this method returns the
	 * same instance for all member of this class. The method could therefore
	 * be static, but this would prevent one from overriding the behavior.
	 *
	 * @return     TsunamiEngineeringDataPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new TsunamiEngineeringDataPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a TsunamiDocLib object.
	 *
	 * @param      TsunamiDocLib $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setTsunamiDocLib($v)
	{


		if ($v === null) {
			$this->setTsunamiDocLibId(NULL);
		} else {
			$this->setTsunamiDocLibId($v->getId());
		}


		$this->aTsunamiDocLib = $v;
	}


	/**
	 * Get the associated TsunamiDocLib object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     TsunamiDocLib The associated TsunamiDocLib object.
	 * @throws     PropelException
	 */
	public function getTsunamiDocLib($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiDocLibPeer.php';

		if ($this->aTsunamiDocLib === null && ($this->tsunami_doc_lib_id > 0)) {

			$this->aTsunamiDocLib = TsunamiDocLibPeer::retrieveByPK($this->tsunami_doc_lib_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = TsunamiDocLibPeer::retrieveByPK($this->tsunami_doc_lib_id, $con);
			   $obj->addTsunamiDocLibs($this);
			 */
		}
		return $this->aTsunamiDocLib;
	}

} // BaseTsunamiEngineeringData
