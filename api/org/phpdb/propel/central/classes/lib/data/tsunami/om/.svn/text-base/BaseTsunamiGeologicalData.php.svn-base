<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/tsunami/TsunamiGeologicalDataPeer.php';

/**
 * Base class that represents a row from the 'TSUNAMI_GEOLOGICAL_DATA' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiGeologicalData extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        TsunamiGeologicalDataPeer
	 */
	protected static $peer;


	/**
	 * The value for the tsunami_geological_data_id field.
	 * @var        double
	 */
	protected $tsunami_geological_data_id;


	/**
	 * The value for the displacement field.
	 * @var        double
	 */
	protected $displacement;


	/**
	 * The value for the displacement_subsidence field.
	 * @var        double
	 */
	protected $displacement_subsidence;


	/**
	 * The value for the displacement_uplift field.
	 * @var        double
	 */
	protected $displacement_uplift;


	/**
	 * The value for the eil field.
	 * @var        double
	 */
	protected $eil;


	/**
	 * The value for the eil_characteristics field.
	 * @var        double
	 */
	protected $eil_characteristics;


	/**
	 * The value for the eil_dist_inland field.
	 * @var        double
	 */
	protected $eil_dist_inland;


	/**
	 * The value for the eil_elevation field.
	 * @var        double
	 */
	protected $eil_elevation;


	/**
	 * The value for the fault field.
	 * @var        double
	 */
	protected $fault;


	/**
	 * The value for the fault_geomorphic field.
	 * @var        double
	 */
	protected $fault_geomorphic;


	/**
	 * The value for the fault_offset field.
	 * @var        double
	 */
	protected $fault_offset;


	/**
	 * The value for the fault_paleo field.
	 * @var        double
	 */
	protected $fault_paleo;


	/**
	 * The value for the fault_strike_measure field.
	 * @var        double
	 */
	protected $fault_strike_measure;


	/**
	 * The value for the fault_type field.
	 * @var        double
	 */
	protected $fault_type;


	/**
	 * The value for the gmchanges field.
	 * @var        double
	 */
	protected $gmchanges;


	/**
	 * The value for the gmchanges_bed_mod field.
	 * @var        double
	 */
	protected $gmchanges_bed_mod;


	/**
	 * The value for the gmchanges_deposit field.
	 * @var        double
	 */
	protected $gmchanges_deposit;


	/**
	 * The value for the gmchanges_scour field.
	 * @var        double
	 */
	protected $gmchanges_scour;


	/**
	 * The value for the paleo field.
	 * @var        double
	 */
	protected $paleo;


	/**
	 * The value for the paleo_characteristics field.
	 * @var        double
	 */
	protected $paleo_characteristics;


	/**
	 * The value for the paleo_core_samples field.
	 * @var        double
	 */
	protected $paleo_core_samples;


	/**
	 * The value for the paleo_dist_inland field.
	 * @var        double
	 */
	protected $paleo_dist_inland;


	/**
	 * The value for the paleo_elevation field.
	 * @var        double
	 */
	protected $paleo_elevation;


	/**
	 * The value for the paleo_outcrops field.
	 * @var        double
	 */
	protected $paleo_outcrops;


	/**
	 * The value for the paleo_scale field.
	 * @var        double
	 */
	protected $paleo_scale;


	/**
	 * The value for the paleo_sed_peels field.
	 * @var        double
	 */
	protected $paleo_sed_peels;


	/**
	 * The value for the paleo_spatial_var field.
	 * @var        double
	 */
	protected $paleo_spatial_var;


	/**
	 * The value for the smsl field.
	 * @var        double
	 */
	protected $smsl;


	/**
	 * The value for the ssl_coefficient_of_friction field.
	 * @var        double
	 */
	protected $ssl_coefficient_of_friction;


	/**
	 * The value for the ssl_deposits field.
	 * @var        double
	 */
	protected $ssl_deposits;


	/**
	 * The value for the ssl_scars field.
	 * @var        double
	 */
	protected $ssl_scars;


	/**
	 * The value for the tdcbm field.
	 * @var        double
	 */
	protected $tdcbm;


	/**
	 * The value for the tdcbm_characteristics field.
	 * @var        double
	 */
	protected $tdcbm_characteristics;


	/**
	 * The value for the tdcbm_dist_inland field.
	 * @var        double
	 */
	protected $tdcbm_dist_inland;


	/**
	 * The value for the tdcbm_elevation field.
	 * @var        double
	 */
	protected $tdcbm_elevation;


	/**
	 * The value for the tdcbm_scale field.
	 * @var        double
	 */
	protected $tdcbm_scale;


	/**
	 * The value for the tdcbm_spatial_var field.
	 * @var        double
	 */
	protected $tdcbm_spatial_var;


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
	 * Get the [tsunami_geological_data_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->tsunami_geological_data_id;
	}

	/**
	 * Get the [displacement] column value.
	 * 
	 * @return     double
	 */
	public function getDisplacement()
	{

		return $this->displacement;
	}

	/**
	 * Get the [displacement_subsidence] column value.
	 * 
	 * @return     double
	 */
	public function getDisplacementSubsidence()
	{

		return $this->displacement_subsidence;
	}

	/**
	 * Get the [displacement_uplift] column value.
	 * 
	 * @return     double
	 */
	public function getDisplacementUplift()
	{

		return $this->displacement_uplift;
	}

	/**
	 * Get the [eil] column value.
	 * 
	 * @return     double
	 */
	public function getEil()
	{

		return $this->eil;
	}

	/**
	 * Get the [eil_characteristics] column value.
	 * 
	 * @return     double
	 */
	public function getEilCharacteristics()
	{

		return $this->eil_characteristics;
	}

	/**
	 * Get the [eil_dist_inland] column value.
	 * 
	 * @return     double
	 */
	public function getEilDistInland()
	{

		return $this->eil_dist_inland;
	}

	/**
	 * Get the [eil_elevation] column value.
	 * 
	 * @return     double
	 */
	public function getEilElevation()
	{

		return $this->eil_elevation;
	}

	/**
	 * Get the [fault] column value.
	 * 
	 * @return     double
	 */
	public function getFault()
	{

		return $this->fault;
	}

	/**
	 * Get the [fault_geomorphic] column value.
	 * 
	 * @return     double
	 */
	public function getFaultGeomorphic()
	{

		return $this->fault_geomorphic;
	}

	/**
	 * Get the [fault_offset] column value.
	 * 
	 * @return     double
	 */
	public function getFaultOffset()
	{

		return $this->fault_offset;
	}

	/**
	 * Get the [fault_paleo] column value.
	 * 
	 * @return     double
	 */
	public function getFaultPaleo()
	{

		return $this->fault_paleo;
	}

	/**
	 * Get the [fault_strike_measure] column value.
	 * 
	 * @return     double
	 */
	public function getFaultStrikeMeasure()
	{

		return $this->fault_strike_measure;
	}

	/**
	 * Get the [fault_type] column value.
	 * 
	 * @return     double
	 */
	public function getFaultType()
	{

		return $this->fault_type;
	}

	/**
	 * Get the [gmchanges] column value.
	 * 
	 * @return     double
	 */
	public function getGmchanges()
	{

		return $this->gmchanges;
	}

	/**
	 * Get the [gmchanges_bed_mod] column value.
	 * 
	 * @return     double
	 */
	public function getGmchangesBedMod()
	{

		return $this->gmchanges_bed_mod;
	}

	/**
	 * Get the [gmchanges_deposit] column value.
	 * 
	 * @return     double
	 */
	public function getGmchangesDeposit()
	{

		return $this->gmchanges_deposit;
	}

	/**
	 * Get the [gmchanges_scour] column value.
	 * 
	 * @return     double
	 */
	public function getGmchangesScour()
	{

		return $this->gmchanges_scour;
	}

	/**
	 * Get the [paleo] column value.
	 * 
	 * @return     double
	 */
	public function getPaleo()
	{

		return $this->paleo;
	}

	/**
	 * Get the [paleo_characteristics] column value.
	 * 
	 * @return     double
	 */
	public function getPaleoCharacteristics()
	{

		return $this->paleo_characteristics;
	}

	/**
	 * Get the [paleo_core_samples] column value.
	 * 
	 * @return     double
	 */
	public function getPaleoCoreSamples()
	{

		return $this->paleo_core_samples;
	}

	/**
	 * Get the [paleo_dist_inland] column value.
	 * 
	 * @return     double
	 */
	public function getPaleoDistInland()
	{

		return $this->paleo_dist_inland;
	}

	/**
	 * Get the [paleo_elevation] column value.
	 * 
	 * @return     double
	 */
	public function getPaleoElevation()
	{

		return $this->paleo_elevation;
	}

	/**
	 * Get the [paleo_outcrops] column value.
	 * 
	 * @return     double
	 */
	public function getPaleoOutcrops()
	{

		return $this->paleo_outcrops;
	}

	/**
	 * Get the [paleo_scale] column value.
	 * 
	 * @return     double
	 */
	public function getPaleoScale()
	{

		return $this->paleo_scale;
	}

	/**
	 * Get the [paleo_sed_peels] column value.
	 * 
	 * @return     double
	 */
	public function getPaleoSedPeels()
	{

		return $this->paleo_sed_peels;
	}

	/**
	 * Get the [paleo_spatial_var] column value.
	 * 
	 * @return     double
	 */
	public function getPaleoSpatialVar()
	{

		return $this->paleo_spatial_var;
	}

	/**
	 * Get the [smsl] column value.
	 * 
	 * @return     double
	 */
	public function getSmsl()
	{

		return $this->smsl;
	}

	/**
	 * Get the [ssl_coefficient_of_friction] column value.
	 * 
	 * @return     double
	 */
	public function getSslCoefficientOfFriction()
	{

		return $this->ssl_coefficient_of_friction;
	}

	/**
	 * Get the [ssl_deposits] column value.
	 * 
	 * @return     double
	 */
	public function getSslDeposits()
	{

		return $this->ssl_deposits;
	}

	/**
	 * Get the [ssl_scars] column value.
	 * 
	 * @return     double
	 */
	public function getSslScars()
	{

		return $this->ssl_scars;
	}

	/**
	 * Get the [tdcbm] column value.
	 * 
	 * @return     double
	 */
	public function getTdcbm()
	{

		return $this->tdcbm;
	}

	/**
	 * Get the [tdcbm_characteristics] column value.
	 * 
	 * @return     double
	 */
	public function getTdcbmCharacteristics()
	{

		return $this->tdcbm_characteristics;
	}

	/**
	 * Get the [tdcbm_dist_inland] column value.
	 * 
	 * @return     double
	 */
	public function getTdcbmDistInland()
	{

		return $this->tdcbm_dist_inland;
	}

	/**
	 * Get the [tdcbm_elevation] column value.
	 * 
	 * @return     double
	 */
	public function getTdcbmElevation()
	{

		return $this->tdcbm_elevation;
	}

	/**
	 * Get the [tdcbm_scale] column value.
	 * 
	 * @return     double
	 */
	public function getTdcbmScale()
	{

		return $this->tdcbm_scale;
	}

	/**
	 * Get the [tdcbm_spatial_var] column value.
	 * 
	 * @return     double
	 */
	public function getTdcbmSpatialVar()
	{

		return $this->tdcbm_spatial_var;
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
	 * Set the value of [tsunami_geological_data_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->tsunami_geological_data_id !== $v) {
			$this->tsunami_geological_data_id = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID;
		}

	} // setId()

	/**
	 * Set the value of [displacement] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDisplacement($v)
	{

		if ($this->displacement !== $v) {
			$this->displacement = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::DISPLACEMENT;
		}

	} // setDisplacement()

	/**
	 * Set the value of [displacement_subsidence] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDisplacementSubsidence($v)
	{

		if ($this->displacement_subsidence !== $v) {
			$this->displacement_subsidence = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::DISPLACEMENT_SUBSIDENCE;
		}

	} // setDisplacementSubsidence()

	/**
	 * Set the value of [displacement_uplift] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDisplacementUplift($v)
	{

		if ($this->displacement_uplift !== $v) {
			$this->displacement_uplift = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::DISPLACEMENT_UPLIFT;
		}

	} // setDisplacementUplift()

	/**
	 * Set the value of [eil] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEil($v)
	{

		if ($this->eil !== $v) {
			$this->eil = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::EIL;
		}

	} // setEil()

	/**
	 * Set the value of [eil_characteristics] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEilCharacteristics($v)
	{

		if ($this->eil_characteristics !== $v) {
			$this->eil_characteristics = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::EIL_CHARACTERISTICS;
		}

	} // setEilCharacteristics()

	/**
	 * Set the value of [eil_dist_inland] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEilDistInland($v)
	{

		if ($this->eil_dist_inland !== $v) {
			$this->eil_dist_inland = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::EIL_DIST_INLAND;
		}

	} // setEilDistInland()

	/**
	 * Set the value of [eil_elevation] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEilElevation($v)
	{

		if ($this->eil_elevation !== $v) {
			$this->eil_elevation = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::EIL_ELEVATION;
		}

	} // setEilElevation()

	/**
	 * Set the value of [fault] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFault($v)
	{

		if ($this->fault !== $v) {
			$this->fault = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::FAULT;
		}

	} // setFault()

	/**
	 * Set the value of [fault_geomorphic] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFaultGeomorphic($v)
	{

		if ($this->fault_geomorphic !== $v) {
			$this->fault_geomorphic = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::FAULT_GEOMORPHIC;
		}

	} // setFaultGeomorphic()

	/**
	 * Set the value of [fault_offset] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFaultOffset($v)
	{

		if ($this->fault_offset !== $v) {
			$this->fault_offset = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::FAULT_OFFSET;
		}

	} // setFaultOffset()

	/**
	 * Set the value of [fault_paleo] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFaultPaleo($v)
	{

		if ($this->fault_paleo !== $v) {
			$this->fault_paleo = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::FAULT_PALEO;
		}

	} // setFaultPaleo()

	/**
	 * Set the value of [fault_strike_measure] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFaultStrikeMeasure($v)
	{

		if ($this->fault_strike_measure !== $v) {
			$this->fault_strike_measure = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::FAULT_STRIKE_MEASURE;
		}

	} // setFaultStrikeMeasure()

	/**
	 * Set the value of [fault_type] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFaultType($v)
	{

		if ($this->fault_type !== $v) {
			$this->fault_type = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::FAULT_TYPE;
		}

	} // setFaultType()

	/**
	 * Set the value of [gmchanges] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setGmchanges($v)
	{

		if ($this->gmchanges !== $v) {
			$this->gmchanges = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::GMCHANGES;
		}

	} // setGmchanges()

	/**
	 * Set the value of [gmchanges_bed_mod] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setGmchangesBedMod($v)
	{

		if ($this->gmchanges_bed_mod !== $v) {
			$this->gmchanges_bed_mod = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::GMCHANGES_BED_MOD;
		}

	} // setGmchangesBedMod()

	/**
	 * Set the value of [gmchanges_deposit] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setGmchangesDeposit($v)
	{

		if ($this->gmchanges_deposit !== $v) {
			$this->gmchanges_deposit = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::GMCHANGES_DEPOSIT;
		}

	} // setGmchangesDeposit()

	/**
	 * Set the value of [gmchanges_scour] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setGmchangesScour($v)
	{

		if ($this->gmchanges_scour !== $v) {
			$this->gmchanges_scour = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::GMCHANGES_SCOUR;
		}

	} // setGmchangesScour()

	/**
	 * Set the value of [paleo] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPaleo($v)
	{

		if ($this->paleo !== $v) {
			$this->paleo = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::PALEO;
		}

	} // setPaleo()

	/**
	 * Set the value of [paleo_characteristics] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPaleoCharacteristics($v)
	{

		if ($this->paleo_characteristics !== $v) {
			$this->paleo_characteristics = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::PALEO_CHARACTERISTICS;
		}

	} // setPaleoCharacteristics()

	/**
	 * Set the value of [paleo_core_samples] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPaleoCoreSamples($v)
	{

		if ($this->paleo_core_samples !== $v) {
			$this->paleo_core_samples = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::PALEO_CORE_SAMPLES;
		}

	} // setPaleoCoreSamples()

	/**
	 * Set the value of [paleo_dist_inland] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPaleoDistInland($v)
	{

		if ($this->paleo_dist_inland !== $v) {
			$this->paleo_dist_inland = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::PALEO_DIST_INLAND;
		}

	} // setPaleoDistInland()

	/**
	 * Set the value of [paleo_elevation] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPaleoElevation($v)
	{

		if ($this->paleo_elevation !== $v) {
			$this->paleo_elevation = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::PALEO_ELEVATION;
		}

	} // setPaleoElevation()

	/**
	 * Set the value of [paleo_outcrops] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPaleoOutcrops($v)
	{

		if ($this->paleo_outcrops !== $v) {
			$this->paleo_outcrops = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::PALEO_OUTCROPS;
		}

	} // setPaleoOutcrops()

	/**
	 * Set the value of [paleo_scale] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPaleoScale($v)
	{

		if ($this->paleo_scale !== $v) {
			$this->paleo_scale = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::PALEO_SCALE;
		}

	} // setPaleoScale()

	/**
	 * Set the value of [paleo_sed_peels] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPaleoSedPeels($v)
	{

		if ($this->paleo_sed_peels !== $v) {
			$this->paleo_sed_peels = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::PALEO_SED_PEELS;
		}

	} // setPaleoSedPeels()

	/**
	 * Set the value of [paleo_spatial_var] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPaleoSpatialVar($v)
	{

		if ($this->paleo_spatial_var !== $v) {
			$this->paleo_spatial_var = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::PALEO_SPATIAL_VAR;
		}

	} // setPaleoSpatialVar()

	/**
	 * Set the value of [smsl] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSmsl($v)
	{

		if ($this->smsl !== $v) {
			$this->smsl = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::SMSL;
		}

	} // setSmsl()

	/**
	 * Set the value of [ssl_coefficient_of_friction] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSslCoefficientOfFriction($v)
	{

		if ($this->ssl_coefficient_of_friction !== $v) {
			$this->ssl_coefficient_of_friction = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::SSL_COEFFICIENT_OF_FRICTION;
		}

	} // setSslCoefficientOfFriction()

	/**
	 * Set the value of [ssl_deposits] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSslDeposits($v)
	{

		if ($this->ssl_deposits !== $v) {
			$this->ssl_deposits = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::SSL_DEPOSITS;
		}

	} // setSslDeposits()

	/**
	 * Set the value of [ssl_scars] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSslScars($v)
	{

		if ($this->ssl_scars !== $v) {
			$this->ssl_scars = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::SSL_SCARS;
		}

	} // setSslScars()

	/**
	 * Set the value of [tdcbm] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTdcbm($v)
	{

		if ($this->tdcbm !== $v) {
			$this->tdcbm = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::TDCBM;
		}

	} // setTdcbm()

	/**
	 * Set the value of [tdcbm_characteristics] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTdcbmCharacteristics($v)
	{

		if ($this->tdcbm_characteristics !== $v) {
			$this->tdcbm_characteristics = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::TDCBM_CHARACTERISTICS;
		}

	} // setTdcbmCharacteristics()

	/**
	 * Set the value of [tdcbm_dist_inland] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTdcbmDistInland($v)
	{

		if ($this->tdcbm_dist_inland !== $v) {
			$this->tdcbm_dist_inland = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::TDCBM_DIST_INLAND;
		}

	} // setTdcbmDistInland()

	/**
	 * Set the value of [tdcbm_elevation] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTdcbmElevation($v)
	{

		if ($this->tdcbm_elevation !== $v) {
			$this->tdcbm_elevation = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::TDCBM_ELEVATION;
		}

	} // setTdcbmElevation()

	/**
	 * Set the value of [tdcbm_scale] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTdcbmScale($v)
	{

		if ($this->tdcbm_scale !== $v) {
			$this->tdcbm_scale = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::TDCBM_SCALE;
		}

	} // setTdcbmScale()

	/**
	 * Set the value of [tdcbm_spatial_var] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTdcbmSpatialVar($v)
	{

		if ($this->tdcbm_spatial_var !== $v) {
			$this->tdcbm_spatial_var = $v;
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::TDCBM_SPATIAL_VAR;
		}

	} // setTdcbmSpatialVar()

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
			$this->modifiedColumns[] = TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID;
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

			$this->tsunami_geological_data_id = $rs->getFloat($startcol + 0);

			$this->displacement = $rs->getFloat($startcol + 1);

			$this->displacement_subsidence = $rs->getFloat($startcol + 2);

			$this->displacement_uplift = $rs->getFloat($startcol + 3);

			$this->eil = $rs->getFloat($startcol + 4);

			$this->eil_characteristics = $rs->getFloat($startcol + 5);

			$this->eil_dist_inland = $rs->getFloat($startcol + 6);

			$this->eil_elevation = $rs->getFloat($startcol + 7);

			$this->fault = $rs->getFloat($startcol + 8);

			$this->fault_geomorphic = $rs->getFloat($startcol + 9);

			$this->fault_offset = $rs->getFloat($startcol + 10);

			$this->fault_paleo = $rs->getFloat($startcol + 11);

			$this->fault_strike_measure = $rs->getFloat($startcol + 12);

			$this->fault_type = $rs->getFloat($startcol + 13);

			$this->gmchanges = $rs->getFloat($startcol + 14);

			$this->gmchanges_bed_mod = $rs->getFloat($startcol + 15);

			$this->gmchanges_deposit = $rs->getFloat($startcol + 16);

			$this->gmchanges_scour = $rs->getFloat($startcol + 17);

			$this->paleo = $rs->getFloat($startcol + 18);

			$this->paleo_characteristics = $rs->getFloat($startcol + 19);

			$this->paleo_core_samples = $rs->getFloat($startcol + 20);

			$this->paleo_dist_inland = $rs->getFloat($startcol + 21);

			$this->paleo_elevation = $rs->getFloat($startcol + 22);

			$this->paleo_outcrops = $rs->getFloat($startcol + 23);

			$this->paleo_scale = $rs->getFloat($startcol + 24);

			$this->paleo_sed_peels = $rs->getFloat($startcol + 25);

			$this->paleo_spatial_var = $rs->getFloat($startcol + 26);

			$this->smsl = $rs->getFloat($startcol + 27);

			$this->ssl_coefficient_of_friction = $rs->getFloat($startcol + 28);

			$this->ssl_deposits = $rs->getFloat($startcol + 29);

			$this->ssl_scars = $rs->getFloat($startcol + 30);

			$this->tdcbm = $rs->getFloat($startcol + 31);

			$this->tdcbm_characteristics = $rs->getFloat($startcol + 32);

			$this->tdcbm_dist_inland = $rs->getFloat($startcol + 33);

			$this->tdcbm_elevation = $rs->getFloat($startcol + 34);

			$this->tdcbm_scale = $rs->getFloat($startcol + 35);

			$this->tdcbm_spatial_var = $rs->getFloat($startcol + 36);

			$this->tsunami_doc_lib_id = $rs->getFloat($startcol + 37);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 38; // 38 = TsunamiGeologicalDataPeer::NUM_COLUMNS - TsunamiGeologicalDataPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating TsunamiGeologicalData object", $e);
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
			$con = Propel::getConnection(TsunamiGeologicalDataPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			TsunamiGeologicalDataPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(TsunamiGeologicalDataPeer::DATABASE_NAME);
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
					$pk = TsunamiGeologicalDataPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += TsunamiGeologicalDataPeer::doUpdate($this, $con);
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


			if (($retval = TsunamiGeologicalDataPeer::doValidate($this, $columns)) !== true) {
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
		$pos = TsunamiGeologicalDataPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDisplacement();
				break;
			case 2:
				return $this->getDisplacementSubsidence();
				break;
			case 3:
				return $this->getDisplacementUplift();
				break;
			case 4:
				return $this->getEil();
				break;
			case 5:
				return $this->getEilCharacteristics();
				break;
			case 6:
				return $this->getEilDistInland();
				break;
			case 7:
				return $this->getEilElevation();
				break;
			case 8:
				return $this->getFault();
				break;
			case 9:
				return $this->getFaultGeomorphic();
				break;
			case 10:
				return $this->getFaultOffset();
				break;
			case 11:
				return $this->getFaultPaleo();
				break;
			case 12:
				return $this->getFaultStrikeMeasure();
				break;
			case 13:
				return $this->getFaultType();
				break;
			case 14:
				return $this->getGmchanges();
				break;
			case 15:
				return $this->getGmchangesBedMod();
				break;
			case 16:
				return $this->getGmchangesDeposit();
				break;
			case 17:
				return $this->getGmchangesScour();
				break;
			case 18:
				return $this->getPaleo();
				break;
			case 19:
				return $this->getPaleoCharacteristics();
				break;
			case 20:
				return $this->getPaleoCoreSamples();
				break;
			case 21:
				return $this->getPaleoDistInland();
				break;
			case 22:
				return $this->getPaleoElevation();
				break;
			case 23:
				return $this->getPaleoOutcrops();
				break;
			case 24:
				return $this->getPaleoScale();
				break;
			case 25:
				return $this->getPaleoSedPeels();
				break;
			case 26:
				return $this->getPaleoSpatialVar();
				break;
			case 27:
				return $this->getSmsl();
				break;
			case 28:
				return $this->getSslCoefficientOfFriction();
				break;
			case 29:
				return $this->getSslDeposits();
				break;
			case 30:
				return $this->getSslScars();
				break;
			case 31:
				return $this->getTdcbm();
				break;
			case 32:
				return $this->getTdcbmCharacteristics();
				break;
			case 33:
				return $this->getTdcbmDistInland();
				break;
			case 34:
				return $this->getTdcbmElevation();
				break;
			case 35:
				return $this->getTdcbmScale();
				break;
			case 36:
				return $this->getTdcbmSpatialVar();
				break;
			case 37:
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
		$keys = TsunamiGeologicalDataPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDisplacement(),
			$keys[2] => $this->getDisplacementSubsidence(),
			$keys[3] => $this->getDisplacementUplift(),
			$keys[4] => $this->getEil(),
			$keys[5] => $this->getEilCharacteristics(),
			$keys[6] => $this->getEilDistInland(),
			$keys[7] => $this->getEilElevation(),
			$keys[8] => $this->getFault(),
			$keys[9] => $this->getFaultGeomorphic(),
			$keys[10] => $this->getFaultOffset(),
			$keys[11] => $this->getFaultPaleo(),
			$keys[12] => $this->getFaultStrikeMeasure(),
			$keys[13] => $this->getFaultType(),
			$keys[14] => $this->getGmchanges(),
			$keys[15] => $this->getGmchangesBedMod(),
			$keys[16] => $this->getGmchangesDeposit(),
			$keys[17] => $this->getGmchangesScour(),
			$keys[18] => $this->getPaleo(),
			$keys[19] => $this->getPaleoCharacteristics(),
			$keys[20] => $this->getPaleoCoreSamples(),
			$keys[21] => $this->getPaleoDistInland(),
			$keys[22] => $this->getPaleoElevation(),
			$keys[23] => $this->getPaleoOutcrops(),
			$keys[24] => $this->getPaleoScale(),
			$keys[25] => $this->getPaleoSedPeels(),
			$keys[26] => $this->getPaleoSpatialVar(),
			$keys[27] => $this->getSmsl(),
			$keys[28] => $this->getSslCoefficientOfFriction(),
			$keys[29] => $this->getSslDeposits(),
			$keys[30] => $this->getSslScars(),
			$keys[31] => $this->getTdcbm(),
			$keys[32] => $this->getTdcbmCharacteristics(),
			$keys[33] => $this->getTdcbmDistInland(),
			$keys[34] => $this->getTdcbmElevation(),
			$keys[35] => $this->getTdcbmScale(),
			$keys[36] => $this->getTdcbmSpatialVar(),
			$keys[37] => $this->getTsunamiDocLibId(),
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
		$pos = TsunamiGeologicalDataPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDisplacement($value);
				break;
			case 2:
				$this->setDisplacementSubsidence($value);
				break;
			case 3:
				$this->setDisplacementUplift($value);
				break;
			case 4:
				$this->setEil($value);
				break;
			case 5:
				$this->setEilCharacteristics($value);
				break;
			case 6:
				$this->setEilDistInland($value);
				break;
			case 7:
				$this->setEilElevation($value);
				break;
			case 8:
				$this->setFault($value);
				break;
			case 9:
				$this->setFaultGeomorphic($value);
				break;
			case 10:
				$this->setFaultOffset($value);
				break;
			case 11:
				$this->setFaultPaleo($value);
				break;
			case 12:
				$this->setFaultStrikeMeasure($value);
				break;
			case 13:
				$this->setFaultType($value);
				break;
			case 14:
				$this->setGmchanges($value);
				break;
			case 15:
				$this->setGmchangesBedMod($value);
				break;
			case 16:
				$this->setGmchangesDeposit($value);
				break;
			case 17:
				$this->setGmchangesScour($value);
				break;
			case 18:
				$this->setPaleo($value);
				break;
			case 19:
				$this->setPaleoCharacteristics($value);
				break;
			case 20:
				$this->setPaleoCoreSamples($value);
				break;
			case 21:
				$this->setPaleoDistInland($value);
				break;
			case 22:
				$this->setPaleoElevation($value);
				break;
			case 23:
				$this->setPaleoOutcrops($value);
				break;
			case 24:
				$this->setPaleoScale($value);
				break;
			case 25:
				$this->setPaleoSedPeels($value);
				break;
			case 26:
				$this->setPaleoSpatialVar($value);
				break;
			case 27:
				$this->setSmsl($value);
				break;
			case 28:
				$this->setSslCoefficientOfFriction($value);
				break;
			case 29:
				$this->setSslDeposits($value);
				break;
			case 30:
				$this->setSslScars($value);
				break;
			case 31:
				$this->setTdcbm($value);
				break;
			case 32:
				$this->setTdcbmCharacteristics($value);
				break;
			case 33:
				$this->setTdcbmDistInland($value);
				break;
			case 34:
				$this->setTdcbmElevation($value);
				break;
			case 35:
				$this->setTdcbmScale($value);
				break;
			case 36:
				$this->setTdcbmSpatialVar($value);
				break;
			case 37:
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
		$keys = TsunamiGeologicalDataPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDisplacement($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDisplacementSubsidence($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDisplacementUplift($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setEil($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setEilCharacteristics($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setEilDistInland($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setEilElevation($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setFault($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setFaultGeomorphic($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setFaultOffset($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setFaultPaleo($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setFaultStrikeMeasure($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setFaultType($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setGmchanges($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setGmchangesBedMod($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setGmchangesDeposit($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setGmchangesScour($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setPaleo($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setPaleoCharacteristics($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setPaleoCoreSamples($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setPaleoDistInland($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setPaleoElevation($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setPaleoOutcrops($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setPaleoScale($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setPaleoSedPeels($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setPaleoSpatialVar($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setSmsl($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setSslCoefficientOfFriction($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setSslDeposits($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setSslScars($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setTdcbm($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setTdcbmCharacteristics($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setTdcbmDistInland($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setTdcbmElevation($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setTdcbmScale($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setTdcbmSpatialVar($arr[$keys[36]]);
		if (array_key_exists($keys[37], $arr)) $this->setTsunamiDocLibId($arr[$keys[37]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(TsunamiGeologicalDataPeer::DATABASE_NAME);

		if ($this->isColumnModified(TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID)) $criteria->add(TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID, $this->tsunami_geological_data_id);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::DISPLACEMENT)) $criteria->add(TsunamiGeologicalDataPeer::DISPLACEMENT, $this->displacement);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::DISPLACEMENT_SUBSIDENCE)) $criteria->add(TsunamiGeologicalDataPeer::DISPLACEMENT_SUBSIDENCE, $this->displacement_subsidence);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::DISPLACEMENT_UPLIFT)) $criteria->add(TsunamiGeologicalDataPeer::DISPLACEMENT_UPLIFT, $this->displacement_uplift);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::EIL)) $criteria->add(TsunamiGeologicalDataPeer::EIL, $this->eil);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::EIL_CHARACTERISTICS)) $criteria->add(TsunamiGeologicalDataPeer::EIL_CHARACTERISTICS, $this->eil_characteristics);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::EIL_DIST_INLAND)) $criteria->add(TsunamiGeologicalDataPeer::EIL_DIST_INLAND, $this->eil_dist_inland);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::EIL_ELEVATION)) $criteria->add(TsunamiGeologicalDataPeer::EIL_ELEVATION, $this->eil_elevation);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::FAULT)) $criteria->add(TsunamiGeologicalDataPeer::FAULT, $this->fault);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::FAULT_GEOMORPHIC)) $criteria->add(TsunamiGeologicalDataPeer::FAULT_GEOMORPHIC, $this->fault_geomorphic);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::FAULT_OFFSET)) $criteria->add(TsunamiGeologicalDataPeer::FAULT_OFFSET, $this->fault_offset);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::FAULT_PALEO)) $criteria->add(TsunamiGeologicalDataPeer::FAULT_PALEO, $this->fault_paleo);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::FAULT_STRIKE_MEASURE)) $criteria->add(TsunamiGeologicalDataPeer::FAULT_STRIKE_MEASURE, $this->fault_strike_measure);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::FAULT_TYPE)) $criteria->add(TsunamiGeologicalDataPeer::FAULT_TYPE, $this->fault_type);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::GMCHANGES)) $criteria->add(TsunamiGeologicalDataPeer::GMCHANGES, $this->gmchanges);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::GMCHANGES_BED_MOD)) $criteria->add(TsunamiGeologicalDataPeer::GMCHANGES_BED_MOD, $this->gmchanges_bed_mod);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::GMCHANGES_DEPOSIT)) $criteria->add(TsunamiGeologicalDataPeer::GMCHANGES_DEPOSIT, $this->gmchanges_deposit);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::GMCHANGES_SCOUR)) $criteria->add(TsunamiGeologicalDataPeer::GMCHANGES_SCOUR, $this->gmchanges_scour);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::PALEO)) $criteria->add(TsunamiGeologicalDataPeer::PALEO, $this->paleo);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::PALEO_CHARACTERISTICS)) $criteria->add(TsunamiGeologicalDataPeer::PALEO_CHARACTERISTICS, $this->paleo_characteristics);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::PALEO_CORE_SAMPLES)) $criteria->add(TsunamiGeologicalDataPeer::PALEO_CORE_SAMPLES, $this->paleo_core_samples);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::PALEO_DIST_INLAND)) $criteria->add(TsunamiGeologicalDataPeer::PALEO_DIST_INLAND, $this->paleo_dist_inland);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::PALEO_ELEVATION)) $criteria->add(TsunamiGeologicalDataPeer::PALEO_ELEVATION, $this->paleo_elevation);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::PALEO_OUTCROPS)) $criteria->add(TsunamiGeologicalDataPeer::PALEO_OUTCROPS, $this->paleo_outcrops);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::PALEO_SCALE)) $criteria->add(TsunamiGeologicalDataPeer::PALEO_SCALE, $this->paleo_scale);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::PALEO_SED_PEELS)) $criteria->add(TsunamiGeologicalDataPeer::PALEO_SED_PEELS, $this->paleo_sed_peels);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::PALEO_SPATIAL_VAR)) $criteria->add(TsunamiGeologicalDataPeer::PALEO_SPATIAL_VAR, $this->paleo_spatial_var);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::SMSL)) $criteria->add(TsunamiGeologicalDataPeer::SMSL, $this->smsl);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::SSL_COEFFICIENT_OF_FRICTION)) $criteria->add(TsunamiGeologicalDataPeer::SSL_COEFFICIENT_OF_FRICTION, $this->ssl_coefficient_of_friction);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::SSL_DEPOSITS)) $criteria->add(TsunamiGeologicalDataPeer::SSL_DEPOSITS, $this->ssl_deposits);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::SSL_SCARS)) $criteria->add(TsunamiGeologicalDataPeer::SSL_SCARS, $this->ssl_scars);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::TDCBM)) $criteria->add(TsunamiGeologicalDataPeer::TDCBM, $this->tdcbm);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::TDCBM_CHARACTERISTICS)) $criteria->add(TsunamiGeologicalDataPeer::TDCBM_CHARACTERISTICS, $this->tdcbm_characteristics);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::TDCBM_DIST_INLAND)) $criteria->add(TsunamiGeologicalDataPeer::TDCBM_DIST_INLAND, $this->tdcbm_dist_inland);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::TDCBM_ELEVATION)) $criteria->add(TsunamiGeologicalDataPeer::TDCBM_ELEVATION, $this->tdcbm_elevation);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::TDCBM_SCALE)) $criteria->add(TsunamiGeologicalDataPeer::TDCBM_SCALE, $this->tdcbm_scale);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::TDCBM_SPATIAL_VAR)) $criteria->add(TsunamiGeologicalDataPeer::TDCBM_SPATIAL_VAR, $this->tdcbm_spatial_var);
		if ($this->isColumnModified(TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID)) $criteria->add(TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID, $this->tsunami_doc_lib_id);

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
		$criteria = new Criteria(TsunamiGeologicalDataPeer::DATABASE_NAME);

		$criteria->add(TsunamiGeologicalDataPeer::TSUNAMI_GEOLOGICAL_DATA_ID, $this->tsunami_geological_data_id);

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
	 * Generic method to set the primary key (tsunami_geological_data_id column).
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
	 * @param      object $copyObj An object of TsunamiGeologicalData (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDisplacement($this->displacement);

		$copyObj->setDisplacementSubsidence($this->displacement_subsidence);

		$copyObj->setDisplacementUplift($this->displacement_uplift);

		$copyObj->setEil($this->eil);

		$copyObj->setEilCharacteristics($this->eil_characteristics);

		$copyObj->setEilDistInland($this->eil_dist_inland);

		$copyObj->setEilElevation($this->eil_elevation);

		$copyObj->setFault($this->fault);

		$copyObj->setFaultGeomorphic($this->fault_geomorphic);

		$copyObj->setFaultOffset($this->fault_offset);

		$copyObj->setFaultPaleo($this->fault_paleo);

		$copyObj->setFaultStrikeMeasure($this->fault_strike_measure);

		$copyObj->setFaultType($this->fault_type);

		$copyObj->setGmchanges($this->gmchanges);

		$copyObj->setGmchangesBedMod($this->gmchanges_bed_mod);

		$copyObj->setGmchangesDeposit($this->gmchanges_deposit);

		$copyObj->setGmchangesScour($this->gmchanges_scour);

		$copyObj->setPaleo($this->paleo);

		$copyObj->setPaleoCharacteristics($this->paleo_characteristics);

		$copyObj->setPaleoCoreSamples($this->paleo_core_samples);

		$copyObj->setPaleoDistInland($this->paleo_dist_inland);

		$copyObj->setPaleoElevation($this->paleo_elevation);

		$copyObj->setPaleoOutcrops($this->paleo_outcrops);

		$copyObj->setPaleoScale($this->paleo_scale);

		$copyObj->setPaleoSedPeels($this->paleo_sed_peels);

		$copyObj->setPaleoSpatialVar($this->paleo_spatial_var);

		$copyObj->setSmsl($this->smsl);

		$copyObj->setSslCoefficientOfFriction($this->ssl_coefficient_of_friction);

		$copyObj->setSslDeposits($this->ssl_deposits);

		$copyObj->setSslScars($this->ssl_scars);

		$copyObj->setTdcbm($this->tdcbm);

		$copyObj->setTdcbmCharacteristics($this->tdcbm_characteristics);

		$copyObj->setTdcbmDistInland($this->tdcbm_dist_inland);

		$copyObj->setTdcbmElevation($this->tdcbm_elevation);

		$copyObj->setTdcbmScale($this->tdcbm_scale);

		$copyObj->setTdcbmSpatialVar($this->tdcbm_spatial_var);

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
	 * @return     TsunamiGeologicalData Clone of current object.
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
	 * @return     TsunamiGeologicalDataPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new TsunamiGeologicalDataPeer();
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

} // BaseTsunamiGeologicalData
