<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/tsunami/TsunamiHydrodynamicDataPeer.php';

/**
 * Base class that represents a row from the 'TSUNAMI_HYDRODYNAMIC_DATA' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiHydrodynamicData extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        TsunamiHydrodynamicDataPeer
	 */
	protected static $peer;


	/**
	 * The value for the tsunami_hydrodynamic_data_id field.
	 * @var        double
	 */
	protected $tsunami_hydrodynamic_data_id;


	/**
	 * The value for the condition_sea field.
	 * @var        double
	 */
	protected $condition_sea;


	/**
	 * The value for the condition_source field.
	 * @var        double
	 */
	protected $condition_source;


	/**
	 * The value for the condition_weather field.
	 * @var        double
	 */
	protected $condition_weather;


	/**
	 * The value for the condition_wind field.
	 * @var        double
	 */
	protected $condition_wind;


	/**
	 * The value for the econdition field.
	 * @var        double
	 */
	protected $econdition;


	/**
	 * The value for the flow field.
	 * @var        double
	 */
	protected $flow;


	/**
	 * The value for the flow_direction field.
	 * @var        double
	 */
	protected $flow_direction;


	/**
	 * The value for the flow_source field.
	 * @var        double
	 */
	protected $flow_source;


	/**
	 * The value for the flow_speed field.
	 * @var        double
	 */
	protected $flow_speed;


	/**
	 * The value for the inundation field.
	 * @var        double
	 */
	protected $inundation;


	/**
	 * The value for the inundation_dist field.
	 * @var        double
	 */
	protected $inundation_dist;


	/**
	 * The value for the inundation_quality field.
	 * @var        double
	 */
	protected $inundation_quality;


	/**
	 * The value for the inundation_source field.
	 * @var        double
	 */
	protected $inundation_source;


	/**
	 * The value for the runup field.
	 * @var        double
	 */
	protected $runup;


	/**
	 * The value for the runup_adj_method field.
	 * @var        double
	 */
	protected $runup_adj_method;


	/**
	 * The value for the runup_height field.
	 * @var        double
	 */
	protected $runup_height;


	/**
	 * The value for the runup_porheight field.
	 * @var        double
	 */
	protected $runup_porheight;


	/**
	 * The value for the runup_porloc field.
	 * @var        double
	 */
	protected $runup_porloc;


	/**
	 * The value for the runup_quality field.
	 * @var        double
	 */
	protected $runup_quality;


	/**
	 * The value for the runup_source field.
	 * @var        double
	 */
	protected $runup_source;


	/**
	 * The value for the runup_tidal_adj field.
	 * @var        double
	 */
	protected $runup_tidal_adj;


	/**
	 * The value for the tidegauge field.
	 * @var        double
	 */
	protected $tidegauge;


	/**
	 * The value for the tidegauge_source field.
	 * @var        double
	 */
	protected $tidegauge_source;


	/**
	 * The value for the tidegauge_type field.
	 * @var        double
	 */
	protected $tidegauge_type;


	/**
	 * The value for the tsunami_doc_lib_id field.
	 * @var        double
	 */
	protected $tsunami_doc_lib_id;


	/**
	 * The value for the wave field.
	 * @var        double
	 */
	protected $wave;


	/**
	 * The value for the wave_arrival_times field.
	 * @var        double
	 */
	protected $wave_arrival_times;


	/**
	 * The value for the wave_form field.
	 * @var        double
	 */
	protected $wave_form;


	/**
	 * The value for the wave_height field.
	 * @var        double
	 */
	protected $wave_height;


	/**
	 * The value for the wave_number field.
	 * @var        double
	 */
	protected $wave_number;


	/**
	 * The value for the wave_period field.
	 * @var        double
	 */
	protected $wave_period;


	/**
	 * The value for the wave_source field.
	 * @var        double
	 */
	protected $wave_source;


	/**
	 * The value for the wave_time_to_norm field.
	 * @var        double
	 */
	protected $wave_time_to_norm;

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
	 * Get the [tsunami_hydrodynamic_data_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->tsunami_hydrodynamic_data_id;
	}

	/**
	 * Get the [condition_sea] column value.
	 * 
	 * @return     double
	 */
	public function getConditionSea()
	{

		return $this->condition_sea;
	}

	/**
	 * Get the [condition_source] column value.
	 * 
	 * @return     double
	 */
	public function getConditionSource()
	{

		return $this->condition_source;
	}

	/**
	 * Get the [condition_weather] column value.
	 * 
	 * @return     double
	 */
	public function getConditionWeather()
	{

		return $this->condition_weather;
	}

	/**
	 * Get the [condition_wind] column value.
	 * 
	 * @return     double
	 */
	public function getConditionWind()
	{

		return $this->condition_wind;
	}

	/**
	 * Get the [econdition] column value.
	 * 
	 * @return     double
	 */
	public function getEcondition()
	{

		return $this->econdition;
	}

	/**
	 * Get the [flow] column value.
	 * 
	 * @return     double
	 */
	public function getFlow()
	{

		return $this->flow;
	}

	/**
	 * Get the [flow_direction] column value.
	 * 
	 * @return     double
	 */
	public function getFlowDirection()
	{

		return $this->flow_direction;
	}

	/**
	 * Get the [flow_source] column value.
	 * 
	 * @return     double
	 */
	public function getFlowSource()
	{

		return $this->flow_source;
	}

	/**
	 * Get the [flow_speed] column value.
	 * 
	 * @return     double
	 */
	public function getFlowSpeed()
	{

		return $this->flow_speed;
	}

	/**
	 * Get the [inundation] column value.
	 * 
	 * @return     double
	 */
	public function getInundation()
	{

		return $this->inundation;
	}

	/**
	 * Get the [inundation_dist] column value.
	 * 
	 * @return     double
	 */
	public function getInundationDist()
	{

		return $this->inundation_dist;
	}

	/**
	 * Get the [inundation_quality] column value.
	 * 
	 * @return     double
	 */
	public function getInundationQuality()
	{

		return $this->inundation_quality;
	}

	/**
	 * Get the [inundation_source] column value.
	 * 
	 * @return     double
	 */
	public function getInundationSource()
	{

		return $this->inundation_source;
	}

	/**
	 * Get the [runup] column value.
	 * 
	 * @return     double
	 */
	public function getRunup()
	{

		return $this->runup;
	}

	/**
	 * Get the [runup_adj_method] column value.
	 * 
	 * @return     double
	 */
	public function getRunupAdjMethod()
	{

		return $this->runup_adj_method;
	}

	/**
	 * Get the [runup_height] column value.
	 * 
	 * @return     double
	 */
	public function getRunupHeight()
	{

		return $this->runup_height;
	}

	/**
	 * Get the [runup_porheight] column value.
	 * 
	 * @return     double
	 */
	public function getRunupPoRHeight()
	{

		return $this->runup_porheight;
	}

	/**
	 * Get the [runup_porloc] column value.
	 * 
	 * @return     double
	 */
	public function getRunupPoRLoc()
	{

		return $this->runup_porloc;
	}

	/**
	 * Get the [runup_quality] column value.
	 * 
	 * @return     double
	 */
	public function getRunupQuality()
	{

		return $this->runup_quality;
	}

	/**
	 * Get the [runup_source] column value.
	 * 
	 * @return     double
	 */
	public function getRunupSource()
	{

		return $this->runup_source;
	}

	/**
	 * Get the [runup_tidal_adj] column value.
	 * 
	 * @return     double
	 */
	public function getRunupTidalAdj()
	{

		return $this->runup_tidal_adj;
	}

	/**
	 * Get the [tidegauge] column value.
	 * 
	 * @return     double
	 */
	public function getTidegauge()
	{

		return $this->tidegauge;
	}

	/**
	 * Get the [tidegauge_source] column value.
	 * 
	 * @return     double
	 */
	public function getTidegaugeSource()
	{

		return $this->tidegauge_source;
	}

	/**
	 * Get the [tidegauge_type] column value.
	 * 
	 * @return     double
	 */
	public function getTidegaugeType()
	{

		return $this->tidegauge_type;
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
	 * Get the [wave] column value.
	 * 
	 * @return     double
	 */
	public function getWave()
	{

		return $this->wave;
	}

	/**
	 * Get the [wave_arrival_times] column value.
	 * 
	 * @return     double
	 */
	public function getWaveArrivalTimes()
	{

		return $this->wave_arrival_times;
	}

	/**
	 * Get the [wave_form] column value.
	 * 
	 * @return     double
	 */
	public function getWaveForm()
	{

		return $this->wave_form;
	}

	/**
	 * Get the [wave_height] column value.
	 * 
	 * @return     double
	 */
	public function getWaveHeight()
	{

		return $this->wave_height;
	}

	/**
	 * Get the [wave_number] column value.
	 * 
	 * @return     double
	 */
	public function getWaveNumber()
	{

		return $this->wave_number;
	}

	/**
	 * Get the [wave_period] column value.
	 * 
	 * @return     double
	 */
	public function getWavePeriod()
	{

		return $this->wave_period;
	}

	/**
	 * Get the [wave_source] column value.
	 * 
	 * @return     double
	 */
	public function getWaveSource()
	{

		return $this->wave_source;
	}

	/**
	 * Get the [wave_time_to_norm] column value.
	 * 
	 * @return     double
	 */
	public function getWaveTimeToNorm()
	{

		return $this->wave_time_to_norm;
	}

	/**
	 * Set the value of [tsunami_hydrodynamic_data_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->tsunami_hydrodynamic_data_id !== $v) {
			$this->tsunami_hydrodynamic_data_id = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID;
		}

	} // setId()

	/**
	 * Set the value of [condition_sea] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setConditionSea($v)
	{

		if ($this->condition_sea !== $v) {
			$this->condition_sea = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::CONDITION_SEA;
		}

	} // setConditionSea()

	/**
	 * Set the value of [condition_source] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setConditionSource($v)
	{

		if ($this->condition_source !== $v) {
			$this->condition_source = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::CONDITION_SOURCE;
		}

	} // setConditionSource()

	/**
	 * Set the value of [condition_weather] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setConditionWeather($v)
	{

		if ($this->condition_weather !== $v) {
			$this->condition_weather = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::CONDITION_WEATHER;
		}

	} // setConditionWeather()

	/**
	 * Set the value of [condition_wind] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setConditionWind($v)
	{

		if ($this->condition_wind !== $v) {
			$this->condition_wind = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::CONDITION_WIND;
		}

	} // setConditionWind()

	/**
	 * Set the value of [econdition] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEcondition($v)
	{

		if ($this->econdition !== $v) {
			$this->econdition = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::ECONDITION;
		}

	} // setEcondition()

	/**
	 * Set the value of [flow] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFlow($v)
	{

		if ($this->flow !== $v) {
			$this->flow = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::FLOW;
		}

	} // setFlow()

	/**
	 * Set the value of [flow_direction] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFlowDirection($v)
	{

		if ($this->flow_direction !== $v) {
			$this->flow_direction = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::FLOW_DIRECTION;
		}

	} // setFlowDirection()

	/**
	 * Set the value of [flow_source] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFlowSource($v)
	{

		if ($this->flow_source !== $v) {
			$this->flow_source = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::FLOW_SOURCE;
		}

	} // setFlowSource()

	/**
	 * Set the value of [flow_speed] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFlowSpeed($v)
	{

		if ($this->flow_speed !== $v) {
			$this->flow_speed = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::FLOW_SPEED;
		}

	} // setFlowSpeed()

	/**
	 * Set the value of [inundation] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setInundation($v)
	{

		if ($this->inundation !== $v) {
			$this->inundation = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::INUNDATION;
		}

	} // setInundation()

	/**
	 * Set the value of [inundation_dist] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setInundationDist($v)
	{

		if ($this->inundation_dist !== $v) {
			$this->inundation_dist = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::INUNDATION_DIST;
		}

	} // setInundationDist()

	/**
	 * Set the value of [inundation_quality] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setInundationQuality($v)
	{

		if ($this->inundation_quality !== $v) {
			$this->inundation_quality = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::INUNDATION_QUALITY;
		}

	} // setInundationQuality()

	/**
	 * Set the value of [inundation_source] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setInundationSource($v)
	{

		if ($this->inundation_source !== $v) {
			$this->inundation_source = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::INUNDATION_SOURCE;
		}

	} // setInundationSource()

	/**
	 * Set the value of [runup] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRunup($v)
	{

		if ($this->runup !== $v) {
			$this->runup = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::RUNUP;
		}

	} // setRunup()

	/**
	 * Set the value of [runup_adj_method] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRunupAdjMethod($v)
	{

		if ($this->runup_adj_method !== $v) {
			$this->runup_adj_method = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::RUNUP_ADJ_METHOD;
		}

	} // setRunupAdjMethod()

	/**
	 * Set the value of [runup_height] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRunupHeight($v)
	{

		if ($this->runup_height !== $v) {
			$this->runup_height = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::RUNUP_HEIGHT;
		}

	} // setRunupHeight()

	/**
	 * Set the value of [runup_porheight] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRunupPoRHeight($v)
	{

		if ($this->runup_porheight !== $v) {
			$this->runup_porheight = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::RUNUP_PORHEIGHT;
		}

	} // setRunupPoRHeight()

	/**
	 * Set the value of [runup_porloc] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRunupPoRLoc($v)
	{

		if ($this->runup_porloc !== $v) {
			$this->runup_porloc = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::RUNUP_PORLOC;
		}

	} // setRunupPoRLoc()

	/**
	 * Set the value of [runup_quality] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRunupQuality($v)
	{

		if ($this->runup_quality !== $v) {
			$this->runup_quality = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::RUNUP_QUALITY;
		}

	} // setRunupQuality()

	/**
	 * Set the value of [runup_source] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRunupSource($v)
	{

		if ($this->runup_source !== $v) {
			$this->runup_source = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::RUNUP_SOURCE;
		}

	} // setRunupSource()

	/**
	 * Set the value of [runup_tidal_adj] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setRunupTidalAdj($v)
	{

		if ($this->runup_tidal_adj !== $v) {
			$this->runup_tidal_adj = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::RUNUP_TIDAL_ADJ;
		}

	} // setRunupTidalAdj()

	/**
	 * Set the value of [tidegauge] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTidegauge($v)
	{

		if ($this->tidegauge !== $v) {
			$this->tidegauge = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::TIDEGAUGE;
		}

	} // setTidegauge()

	/**
	 * Set the value of [tidegauge_source] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTidegaugeSource($v)
	{

		if ($this->tidegauge_source !== $v) {
			$this->tidegauge_source = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::TIDEGAUGE_SOURCE;
		}

	} // setTidegaugeSource()

	/**
	 * Set the value of [tidegauge_type] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTidegaugeType($v)
	{

		if ($this->tidegauge_type !== $v) {
			$this->tidegauge_type = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::TIDEGAUGE_TYPE;
		}

	} // setTidegaugeType()

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
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID;
		}

		if ($this->aTsunamiDocLib !== null && $this->aTsunamiDocLib->getId() !== $v) {
			$this->aTsunamiDocLib = null;
		}

	} // setTsunamiDocLibId()

	/**
	 * Set the value of [wave] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setWave($v)
	{

		if ($this->wave !== $v) {
			$this->wave = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::WAVE;
		}

	} // setWave()

	/**
	 * Set the value of [wave_arrival_times] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setWaveArrivalTimes($v)
	{

		if ($this->wave_arrival_times !== $v) {
			$this->wave_arrival_times = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::WAVE_ARRIVAL_TIMES;
		}

	} // setWaveArrivalTimes()

	/**
	 * Set the value of [wave_form] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setWaveForm($v)
	{

		if ($this->wave_form !== $v) {
			$this->wave_form = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::WAVE_FORM;
		}

	} // setWaveForm()

	/**
	 * Set the value of [wave_height] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setWaveHeight($v)
	{

		if ($this->wave_height !== $v) {
			$this->wave_height = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::WAVE_HEIGHT;
		}

	} // setWaveHeight()

	/**
	 * Set the value of [wave_number] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setWaveNumber($v)
	{

		if ($this->wave_number !== $v) {
			$this->wave_number = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::WAVE_NUMBER;
		}

	} // setWaveNumber()

	/**
	 * Set the value of [wave_period] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setWavePeriod($v)
	{

		if ($this->wave_period !== $v) {
			$this->wave_period = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::WAVE_PERIOD;
		}

	} // setWavePeriod()

	/**
	 * Set the value of [wave_source] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setWaveSource($v)
	{

		if ($this->wave_source !== $v) {
			$this->wave_source = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::WAVE_SOURCE;
		}

	} // setWaveSource()

	/**
	 * Set the value of [wave_time_to_norm] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setWaveTimeToNorm($v)
	{

		if ($this->wave_time_to_norm !== $v) {
			$this->wave_time_to_norm = $v;
			$this->modifiedColumns[] = TsunamiHydrodynamicDataPeer::WAVE_TIME_TO_NORM;
		}

	} // setWaveTimeToNorm()

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

			$this->tsunami_hydrodynamic_data_id = $rs->getFloat($startcol + 0);

			$this->condition_sea = $rs->getFloat($startcol + 1);

			$this->condition_source = $rs->getFloat($startcol + 2);

			$this->condition_weather = $rs->getFloat($startcol + 3);

			$this->condition_wind = $rs->getFloat($startcol + 4);

			$this->econdition = $rs->getFloat($startcol + 5);

			$this->flow = $rs->getFloat($startcol + 6);

			$this->flow_direction = $rs->getFloat($startcol + 7);

			$this->flow_source = $rs->getFloat($startcol + 8);

			$this->flow_speed = $rs->getFloat($startcol + 9);

			$this->inundation = $rs->getFloat($startcol + 10);

			$this->inundation_dist = $rs->getFloat($startcol + 11);

			$this->inundation_quality = $rs->getFloat($startcol + 12);

			$this->inundation_source = $rs->getFloat($startcol + 13);

			$this->runup = $rs->getFloat($startcol + 14);

			$this->runup_adj_method = $rs->getFloat($startcol + 15);

			$this->runup_height = $rs->getFloat($startcol + 16);

			$this->runup_porheight = $rs->getFloat($startcol + 17);

			$this->runup_porloc = $rs->getFloat($startcol + 18);

			$this->runup_quality = $rs->getFloat($startcol + 19);

			$this->runup_source = $rs->getFloat($startcol + 20);

			$this->runup_tidal_adj = $rs->getFloat($startcol + 21);

			$this->tidegauge = $rs->getFloat($startcol + 22);

			$this->tidegauge_source = $rs->getFloat($startcol + 23);

			$this->tidegauge_type = $rs->getFloat($startcol + 24);

			$this->tsunami_doc_lib_id = $rs->getFloat($startcol + 25);

			$this->wave = $rs->getFloat($startcol + 26);

			$this->wave_arrival_times = $rs->getFloat($startcol + 27);

			$this->wave_form = $rs->getFloat($startcol + 28);

			$this->wave_height = $rs->getFloat($startcol + 29);

			$this->wave_number = $rs->getFloat($startcol + 30);

			$this->wave_period = $rs->getFloat($startcol + 31);

			$this->wave_source = $rs->getFloat($startcol + 32);

			$this->wave_time_to_norm = $rs->getFloat($startcol + 33);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 34; // 34 = TsunamiHydrodynamicDataPeer::NUM_COLUMNS - TsunamiHydrodynamicDataPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating TsunamiHydrodynamicData object", $e);
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
			$con = Propel::getConnection(TsunamiHydrodynamicDataPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			TsunamiHydrodynamicDataPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(TsunamiHydrodynamicDataPeer::DATABASE_NAME);
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
					$pk = TsunamiHydrodynamicDataPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += TsunamiHydrodynamicDataPeer::doUpdate($this, $con);
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


			if (($retval = TsunamiHydrodynamicDataPeer::doValidate($this, $columns)) !== true) {
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
		$pos = TsunamiHydrodynamicDataPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getConditionSea();
				break;
			case 2:
				return $this->getConditionSource();
				break;
			case 3:
				return $this->getConditionWeather();
				break;
			case 4:
				return $this->getConditionWind();
				break;
			case 5:
				return $this->getEcondition();
				break;
			case 6:
				return $this->getFlow();
				break;
			case 7:
				return $this->getFlowDirection();
				break;
			case 8:
				return $this->getFlowSource();
				break;
			case 9:
				return $this->getFlowSpeed();
				break;
			case 10:
				return $this->getInundation();
				break;
			case 11:
				return $this->getInundationDist();
				break;
			case 12:
				return $this->getInundationQuality();
				break;
			case 13:
				return $this->getInundationSource();
				break;
			case 14:
				return $this->getRunup();
				break;
			case 15:
				return $this->getRunupAdjMethod();
				break;
			case 16:
				return $this->getRunupHeight();
				break;
			case 17:
				return $this->getRunupPoRHeight();
				break;
			case 18:
				return $this->getRunupPoRLoc();
				break;
			case 19:
				return $this->getRunupQuality();
				break;
			case 20:
				return $this->getRunupSource();
				break;
			case 21:
				return $this->getRunupTidalAdj();
				break;
			case 22:
				return $this->getTidegauge();
				break;
			case 23:
				return $this->getTidegaugeSource();
				break;
			case 24:
				return $this->getTidegaugeType();
				break;
			case 25:
				return $this->getTsunamiDocLibId();
				break;
			case 26:
				return $this->getWave();
				break;
			case 27:
				return $this->getWaveArrivalTimes();
				break;
			case 28:
				return $this->getWaveForm();
				break;
			case 29:
				return $this->getWaveHeight();
				break;
			case 30:
				return $this->getWaveNumber();
				break;
			case 31:
				return $this->getWavePeriod();
				break;
			case 32:
				return $this->getWaveSource();
				break;
			case 33:
				return $this->getWaveTimeToNorm();
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
		$keys = TsunamiHydrodynamicDataPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getConditionSea(),
			$keys[2] => $this->getConditionSource(),
			$keys[3] => $this->getConditionWeather(),
			$keys[4] => $this->getConditionWind(),
			$keys[5] => $this->getEcondition(),
			$keys[6] => $this->getFlow(),
			$keys[7] => $this->getFlowDirection(),
			$keys[8] => $this->getFlowSource(),
			$keys[9] => $this->getFlowSpeed(),
			$keys[10] => $this->getInundation(),
			$keys[11] => $this->getInundationDist(),
			$keys[12] => $this->getInundationQuality(),
			$keys[13] => $this->getInundationSource(),
			$keys[14] => $this->getRunup(),
			$keys[15] => $this->getRunupAdjMethod(),
			$keys[16] => $this->getRunupHeight(),
			$keys[17] => $this->getRunupPoRHeight(),
			$keys[18] => $this->getRunupPoRLoc(),
			$keys[19] => $this->getRunupQuality(),
			$keys[20] => $this->getRunupSource(),
			$keys[21] => $this->getRunupTidalAdj(),
			$keys[22] => $this->getTidegauge(),
			$keys[23] => $this->getTidegaugeSource(),
			$keys[24] => $this->getTidegaugeType(),
			$keys[25] => $this->getTsunamiDocLibId(),
			$keys[26] => $this->getWave(),
			$keys[27] => $this->getWaveArrivalTimes(),
			$keys[28] => $this->getWaveForm(),
			$keys[29] => $this->getWaveHeight(),
			$keys[30] => $this->getWaveNumber(),
			$keys[31] => $this->getWavePeriod(),
			$keys[32] => $this->getWaveSource(),
			$keys[33] => $this->getWaveTimeToNorm(),
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
		$pos = TsunamiHydrodynamicDataPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setConditionSea($value);
				break;
			case 2:
				$this->setConditionSource($value);
				break;
			case 3:
				$this->setConditionWeather($value);
				break;
			case 4:
				$this->setConditionWind($value);
				break;
			case 5:
				$this->setEcondition($value);
				break;
			case 6:
				$this->setFlow($value);
				break;
			case 7:
				$this->setFlowDirection($value);
				break;
			case 8:
				$this->setFlowSource($value);
				break;
			case 9:
				$this->setFlowSpeed($value);
				break;
			case 10:
				$this->setInundation($value);
				break;
			case 11:
				$this->setInundationDist($value);
				break;
			case 12:
				$this->setInundationQuality($value);
				break;
			case 13:
				$this->setInundationSource($value);
				break;
			case 14:
				$this->setRunup($value);
				break;
			case 15:
				$this->setRunupAdjMethod($value);
				break;
			case 16:
				$this->setRunupHeight($value);
				break;
			case 17:
				$this->setRunupPoRHeight($value);
				break;
			case 18:
				$this->setRunupPoRLoc($value);
				break;
			case 19:
				$this->setRunupQuality($value);
				break;
			case 20:
				$this->setRunupSource($value);
				break;
			case 21:
				$this->setRunupTidalAdj($value);
				break;
			case 22:
				$this->setTidegauge($value);
				break;
			case 23:
				$this->setTidegaugeSource($value);
				break;
			case 24:
				$this->setTidegaugeType($value);
				break;
			case 25:
				$this->setTsunamiDocLibId($value);
				break;
			case 26:
				$this->setWave($value);
				break;
			case 27:
				$this->setWaveArrivalTimes($value);
				break;
			case 28:
				$this->setWaveForm($value);
				break;
			case 29:
				$this->setWaveHeight($value);
				break;
			case 30:
				$this->setWaveNumber($value);
				break;
			case 31:
				$this->setWavePeriod($value);
				break;
			case 32:
				$this->setWaveSource($value);
				break;
			case 33:
				$this->setWaveTimeToNorm($value);
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
		$keys = TsunamiHydrodynamicDataPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setConditionSea($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setConditionSource($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setConditionWeather($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setConditionWind($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setEcondition($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setFlow($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setFlowDirection($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setFlowSource($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setFlowSpeed($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setInundation($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setInundationDist($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setInundationQuality($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setInundationSource($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setRunup($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setRunupAdjMethod($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setRunupHeight($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setRunupPoRHeight($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setRunupPoRLoc($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setRunupQuality($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setRunupSource($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setRunupTidalAdj($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setTidegauge($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setTidegaugeSource($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setTidegaugeType($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setTsunamiDocLibId($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setWave($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setWaveArrivalTimes($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setWaveForm($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setWaveHeight($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setWaveNumber($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setWavePeriod($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setWaveSource($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setWaveTimeToNorm($arr[$keys[33]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(TsunamiHydrodynamicDataPeer::DATABASE_NAME);

		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID)) $criteria->add(TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID, $this->tsunami_hydrodynamic_data_id);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::CONDITION_SEA)) $criteria->add(TsunamiHydrodynamicDataPeer::CONDITION_SEA, $this->condition_sea);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::CONDITION_SOURCE)) $criteria->add(TsunamiHydrodynamicDataPeer::CONDITION_SOURCE, $this->condition_source);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::CONDITION_WEATHER)) $criteria->add(TsunamiHydrodynamicDataPeer::CONDITION_WEATHER, $this->condition_weather);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::CONDITION_WIND)) $criteria->add(TsunamiHydrodynamicDataPeer::CONDITION_WIND, $this->condition_wind);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::ECONDITION)) $criteria->add(TsunamiHydrodynamicDataPeer::ECONDITION, $this->econdition);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::FLOW)) $criteria->add(TsunamiHydrodynamicDataPeer::FLOW, $this->flow);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::FLOW_DIRECTION)) $criteria->add(TsunamiHydrodynamicDataPeer::FLOW_DIRECTION, $this->flow_direction);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::FLOW_SOURCE)) $criteria->add(TsunamiHydrodynamicDataPeer::FLOW_SOURCE, $this->flow_source);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::FLOW_SPEED)) $criteria->add(TsunamiHydrodynamicDataPeer::FLOW_SPEED, $this->flow_speed);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::INUNDATION)) $criteria->add(TsunamiHydrodynamicDataPeer::INUNDATION, $this->inundation);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::INUNDATION_DIST)) $criteria->add(TsunamiHydrodynamicDataPeer::INUNDATION_DIST, $this->inundation_dist);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::INUNDATION_QUALITY)) $criteria->add(TsunamiHydrodynamicDataPeer::INUNDATION_QUALITY, $this->inundation_quality);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::INUNDATION_SOURCE)) $criteria->add(TsunamiHydrodynamicDataPeer::INUNDATION_SOURCE, $this->inundation_source);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP)) $criteria->add(TsunamiHydrodynamicDataPeer::RUNUP, $this->runup);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_ADJ_METHOD)) $criteria->add(TsunamiHydrodynamicDataPeer::RUNUP_ADJ_METHOD, $this->runup_adj_method);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_HEIGHT)) $criteria->add(TsunamiHydrodynamicDataPeer::RUNUP_HEIGHT, $this->runup_height);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_PORHEIGHT)) $criteria->add(TsunamiHydrodynamicDataPeer::RUNUP_PORHEIGHT, $this->runup_porheight);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_PORLOC)) $criteria->add(TsunamiHydrodynamicDataPeer::RUNUP_PORLOC, $this->runup_porloc);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_QUALITY)) $criteria->add(TsunamiHydrodynamicDataPeer::RUNUP_QUALITY, $this->runup_quality);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_SOURCE)) $criteria->add(TsunamiHydrodynamicDataPeer::RUNUP_SOURCE, $this->runup_source);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::RUNUP_TIDAL_ADJ)) $criteria->add(TsunamiHydrodynamicDataPeer::RUNUP_TIDAL_ADJ, $this->runup_tidal_adj);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::TIDEGAUGE)) $criteria->add(TsunamiHydrodynamicDataPeer::TIDEGAUGE, $this->tidegauge);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::TIDEGAUGE_SOURCE)) $criteria->add(TsunamiHydrodynamicDataPeer::TIDEGAUGE_SOURCE, $this->tidegauge_source);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::TIDEGAUGE_TYPE)) $criteria->add(TsunamiHydrodynamicDataPeer::TIDEGAUGE_TYPE, $this->tidegauge_type);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID)) $criteria->add(TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID, $this->tsunami_doc_lib_id);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE)) $criteria->add(TsunamiHydrodynamicDataPeer::WAVE, $this->wave);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_ARRIVAL_TIMES)) $criteria->add(TsunamiHydrodynamicDataPeer::WAVE_ARRIVAL_TIMES, $this->wave_arrival_times);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_FORM)) $criteria->add(TsunamiHydrodynamicDataPeer::WAVE_FORM, $this->wave_form);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_HEIGHT)) $criteria->add(TsunamiHydrodynamicDataPeer::WAVE_HEIGHT, $this->wave_height);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_NUMBER)) $criteria->add(TsunamiHydrodynamicDataPeer::WAVE_NUMBER, $this->wave_number);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_PERIOD)) $criteria->add(TsunamiHydrodynamicDataPeer::WAVE_PERIOD, $this->wave_period);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_SOURCE)) $criteria->add(TsunamiHydrodynamicDataPeer::WAVE_SOURCE, $this->wave_source);
		if ($this->isColumnModified(TsunamiHydrodynamicDataPeer::WAVE_TIME_TO_NORM)) $criteria->add(TsunamiHydrodynamicDataPeer::WAVE_TIME_TO_NORM, $this->wave_time_to_norm);

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
		$criteria = new Criteria(TsunamiHydrodynamicDataPeer::DATABASE_NAME);

		$criteria->add(TsunamiHydrodynamicDataPeer::TSUNAMI_HYDRODYNAMIC_DATA_ID, $this->tsunami_hydrodynamic_data_id);

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
	 * Generic method to set the primary key (tsunami_hydrodynamic_data_id column).
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
	 * @param      object $copyObj An object of TsunamiHydrodynamicData (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setConditionSea($this->condition_sea);

		$copyObj->setConditionSource($this->condition_source);

		$copyObj->setConditionWeather($this->condition_weather);

		$copyObj->setConditionWind($this->condition_wind);

		$copyObj->setEcondition($this->econdition);

		$copyObj->setFlow($this->flow);

		$copyObj->setFlowDirection($this->flow_direction);

		$copyObj->setFlowSource($this->flow_source);

		$copyObj->setFlowSpeed($this->flow_speed);

		$copyObj->setInundation($this->inundation);

		$copyObj->setInundationDist($this->inundation_dist);

		$copyObj->setInundationQuality($this->inundation_quality);

		$copyObj->setInundationSource($this->inundation_source);

		$copyObj->setRunup($this->runup);

		$copyObj->setRunupAdjMethod($this->runup_adj_method);

		$copyObj->setRunupHeight($this->runup_height);

		$copyObj->setRunupPoRHeight($this->runup_porheight);

		$copyObj->setRunupPoRLoc($this->runup_porloc);

		$copyObj->setRunupQuality($this->runup_quality);

		$copyObj->setRunupSource($this->runup_source);

		$copyObj->setRunupTidalAdj($this->runup_tidal_adj);

		$copyObj->setTidegauge($this->tidegauge);

		$copyObj->setTidegaugeSource($this->tidegauge_source);

		$copyObj->setTidegaugeType($this->tidegauge_type);

		$copyObj->setTsunamiDocLibId($this->tsunami_doc_lib_id);

		$copyObj->setWave($this->wave);

		$copyObj->setWaveArrivalTimes($this->wave_arrival_times);

		$copyObj->setWaveForm($this->wave_form);

		$copyObj->setWaveHeight($this->wave_height);

		$copyObj->setWaveNumber($this->wave_number);

		$copyObj->setWavePeriod($this->wave_period);

		$copyObj->setWaveSource($this->wave_source);

		$copyObj->setWaveTimeToNorm($this->wave_time_to_norm);


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
	 * @return     TsunamiHydrodynamicData Clone of current object.
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
	 * @return     TsunamiHydrodynamicDataPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new TsunamiHydrodynamicDataPeer();
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

} // BaseTsunamiHydrodynamicData
