<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/tsunami/TsunamiSocialScienceDataPeer.php';

/**
 * Base class that represents a row from the 'TSUNAMI_SOCIAL_SCIENCE_DATA' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiSocialScienceData extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        TsunamiSocialScienceDataPeer
	 */
	protected static $peer;


	/**
	 * The value for the tsunami_social_science_data_id field.
	 * @var        double
	 */
	protected $tsunami_social_science_data_id;


	/**
	 * The value for the bkg field.
	 * @var        double
	 */
	protected $bkg;


	/**
	 * The value for the bkg_census field.
	 * @var        double
	 */
	protected $bkg_census;


	/**
	 * The value for the bkg_language_issues field.
	 * @var        double
	 */
	protected $bkg_language_issues;


	/**
	 * The value for the bkg_tourist_stats field.
	 * @var        double
	 */
	protected $bkg_tourist_stats;


	/**
	 * The value for the bkg_transport_systems field.
	 * @var        double
	 */
	protected $bkg_transport_systems;


	/**
	 * The value for the comm field.
	 * @var        double
	 */
	protected $comm;


	/**
	 * The value for the comm_info_fromg field.
	 * @var        double
	 */
	protected $comm_info_fromg;


	/**
	 * The value for the comm_warn_sys field.
	 * @var        double
	 */
	protected $comm_warn_sys;


	/**
	 * The value for the cresponse field.
	 * @var        double
	 */
	protected $cresponse;


	/**
	 * The value for the cresponse_intervw field.
	 * @var        double
	 */
	protected $cresponse_intervw;


	/**
	 * The value for the cresponse_mitigation field.
	 * @var        double
	 */
	protected $cresponse_mitigation;


	/**
	 * The value for the cresponse_prep field.
	 * @var        double
	 */
	protected $cresponse_prep;


	/**
	 * The value for the cresponse_recovery field.
	 * @var        double
	 */
	protected $cresponse_recovery;


	/**
	 * The value for the cresponse_warning field.
	 * @var        double
	 */
	protected $cresponse_warning;


	/**
	 * The value for the damage field.
	 * @var        double
	 */
	protected $damage;


	/**
	 * The value for the damage_cost_est field.
	 * @var        double
	 */
	protected $damage_cost_est;


	/**
	 * The value for the damage_industry field.
	 * @var        double
	 */
	protected $damage_industry;


	/**
	 * The value for the damage_type field.
	 * @var        double
	 */
	protected $damage_type;


	/**
	 * The value for the impact field.
	 * @var        double
	 */
	protected $impact;


	/**
	 * The value for the impact_num_dead field.
	 * @var        double
	 */
	protected $impact_num_dead;


	/**
	 * The value for the impact_num_fam_sep field.
	 * @var        double
	 */
	protected $impact_num_fam_sep;


	/**
	 * The value for the impact_num_homeless field.
	 * @var        double
	 */
	protected $impact_num_homeless;


	/**
	 * The value for the impact_num_injured field.
	 * @var        double
	 */
	protected $impact_num_injured;


	/**
	 * The value for the impact_num_missing field.
	 * @var        double
	 */
	protected $impact_num_missing;


	/**
	 * The value for the iresponse field.
	 * @var        double
	 */
	protected $iresponse;


	/**
	 * The value for the iresponse_intervw field.
	 * @var        double
	 */
	protected $iresponse_intervw;


	/**
	 * The value for the iresponse_mitigation field.
	 * @var        double
	 */
	protected $iresponse_mitigation;


	/**
	 * The value for the iresponse_prep field.
	 * @var        double
	 */
	protected $iresponse_prep;


	/**
	 * The value for the iresponse_recovery field.
	 * @var        double
	 */
	protected $iresponse_recovery;


	/**
	 * The value for the iresponse_warnings field.
	 * @var        double
	 */
	protected $iresponse_warnings;


	/**
	 * The value for the oresponse field.
	 * @var        double
	 */
	protected $oresponse;


	/**
	 * The value for the oresponse_disease field.
	 * @var        double
	 */
	protected $oresponse_disease;


	/**
	 * The value for the oresponse_grelief field.
	 * @var        double
	 */
	protected $oresponse_grelief;


	/**
	 * The value for the oresponse_intervw field.
	 * @var        double
	 */
	protected $oresponse_intervw;


	/**
	 * The value for the oresponse_mitigation field.
	 * @var        double
	 */
	protected $oresponse_mitigation;


	/**
	 * The value for the oresponsengorelief field.
	 * @var        double
	 */
	protected $oresponsengorelief;


	/**
	 * The value for the oresponse_prep field.
	 * @var        double
	 */
	protected $oresponse_prep;


	/**
	 * The value for the oresponse_recovery field.
	 * @var        double
	 */
	protected $oresponse_recovery;


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
	 * Get the [tsunami_social_science_data_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->tsunami_social_science_data_id;
	}

	/**
	 * Get the [bkg] column value.
	 * 
	 * @return     double
	 */
	public function getBkg()
	{

		return $this->bkg;
	}

	/**
	 * Get the [bkg_census] column value.
	 * 
	 * @return     double
	 */
	public function getBkgCensus()
	{

		return $this->bkg_census;
	}

	/**
	 * Get the [bkg_language_issues] column value.
	 * 
	 * @return     double
	 */
	public function getBkgLanguageIssues()
	{

		return $this->bkg_language_issues;
	}

	/**
	 * Get the [bkg_tourist_stats] column value.
	 * 
	 * @return     double
	 */
	public function getBkgTouristStats()
	{

		return $this->bkg_tourist_stats;
	}

	/**
	 * Get the [bkg_transport_systems] column value.
	 * 
	 * @return     double
	 */
	public function getBkgTransportSystems()
	{

		return $this->bkg_transport_systems;
	}

	/**
	 * Get the [comm] column value.
	 * 
	 * @return     double
	 */
	public function getComm()
	{

		return $this->comm;
	}

	/**
	 * Get the [comm_info_fromg] column value.
	 * 
	 * @return     double
	 */
	public function getCommInfoFromG()
	{

		return $this->comm_info_fromg;
	}

	/**
	 * Get the [comm_warn_sys] column value.
	 * 
	 * @return     double
	 */
	public function getCommWarnSys()
	{

		return $this->comm_warn_sys;
	}

	/**
	 * Get the [cresponse] column value.
	 * 
	 * @return     double
	 */
	public function getCresponse()
	{

		return $this->cresponse;
	}

	/**
	 * Get the [cresponse_intervw] column value.
	 * 
	 * @return     double
	 */
	public function getCresponseIntervw()
	{

		return $this->cresponse_intervw;
	}

	/**
	 * Get the [cresponse_mitigation] column value.
	 * 
	 * @return     double
	 */
	public function getCresponseMitigation()
	{

		return $this->cresponse_mitigation;
	}

	/**
	 * Get the [cresponse_prep] column value.
	 * 
	 * @return     double
	 */
	public function getCresponsePrep()
	{

		return $this->cresponse_prep;
	}

	/**
	 * Get the [cresponse_recovery] column value.
	 * 
	 * @return     double
	 */
	public function getCresponseRecovery()
	{

		return $this->cresponse_recovery;
	}

	/**
	 * Get the [cresponse_warning] column value.
	 * 
	 * @return     double
	 */
	public function getCresponseWarning()
	{

		return $this->cresponse_warning;
	}

	/**
	 * Get the [damage] column value.
	 * 
	 * @return     double
	 */
	public function getDamage()
	{

		return $this->damage;
	}

	/**
	 * Get the [damage_cost_est] column value.
	 * 
	 * @return     double
	 */
	public function getDamageCostEst()
	{

		return $this->damage_cost_est;
	}

	/**
	 * Get the [damage_industry] column value.
	 * 
	 * @return     double
	 */
	public function getDamageIndustry()
	{

		return $this->damage_industry;
	}

	/**
	 * Get the [damage_type] column value.
	 * 
	 * @return     double
	 */
	public function getDamageType()
	{

		return $this->damage_type;
	}

	/**
	 * Get the [impact] column value.
	 * 
	 * @return     double
	 */
	public function getImpact()
	{

		return $this->impact;
	}

	/**
	 * Get the [impact_num_dead] column value.
	 * 
	 * @return     double
	 */
	public function getImpactNumDead()
	{

		return $this->impact_num_dead;
	}

	/**
	 * Get the [impact_num_fam_sep] column value.
	 * 
	 * @return     double
	 */
	public function getImpactNumFamSep()
	{

		return $this->impact_num_fam_sep;
	}

	/**
	 * Get the [impact_num_homeless] column value.
	 * 
	 * @return     double
	 */
	public function getImpactNumHomeless()
	{

		return $this->impact_num_homeless;
	}

	/**
	 * Get the [impact_num_injured] column value.
	 * 
	 * @return     double
	 */
	public function getImpactNumInjured()
	{

		return $this->impact_num_injured;
	}

	/**
	 * Get the [impact_num_missing] column value.
	 * 
	 * @return     double
	 */
	public function getImpactNumMissing()
	{

		return $this->impact_num_missing;
	}

	/**
	 * Get the [iresponse] column value.
	 * 
	 * @return     double
	 */
	public function getIresponse()
	{

		return $this->iresponse;
	}

	/**
	 * Get the [iresponse_intervw] column value.
	 * 
	 * @return     double
	 */
	public function getIresponseIntervw()
	{

		return $this->iresponse_intervw;
	}

	/**
	 * Get the [iresponse_mitigation] column value.
	 * 
	 * @return     double
	 */
	public function getIresponseMitigation()
	{

		return $this->iresponse_mitigation;
	}

	/**
	 * Get the [iresponse_prep] column value.
	 * 
	 * @return     double
	 */
	public function getIresponsePrep()
	{

		return $this->iresponse_prep;
	}

	/**
	 * Get the [iresponse_recovery] column value.
	 * 
	 * @return     double
	 */
	public function getIresponseRecovery()
	{

		return $this->iresponse_recovery;
	}

	/**
	 * Get the [iresponse_warnings] column value.
	 * 
	 * @return     double
	 */
	public function getIresponseWarnings()
	{

		return $this->iresponse_warnings;
	}

	/**
	 * Get the [oresponse] column value.
	 * 
	 * @return     double
	 */
	public function getOresponse()
	{

		return $this->oresponse;
	}

	/**
	 * Get the [oresponse_disease] column value.
	 * 
	 * @return     double
	 */
	public function getOresponseDisease()
	{

		return $this->oresponse_disease;
	}

	/**
	 * Get the [oresponse_grelief] column value.
	 * 
	 * @return     double
	 */
	public function getOresponseGrelief()
	{

		return $this->oresponse_grelief;
	}

	/**
	 * Get the [oresponse_intervw] column value.
	 * 
	 * @return     double
	 */
	public function getOresponseIntervw()
	{

		return $this->oresponse_intervw;
	}

	/**
	 * Get the [oresponse_mitigation] column value.
	 * 
	 * @return     double
	 */
	public function getOresponseMitigation()
	{

		return $this->oresponse_mitigation;
	}

	/**
	 * Get the [oresponsengorelief] column value.
	 * 
	 * @return     double
	 */
	public function getOresponseNGORelief()
	{

		return $this->oresponsengorelief;
	}

	/**
	 * Get the [oresponse_prep] column value.
	 * 
	 * @return     double
	 */
	public function getOresponsePrep()
	{

		return $this->oresponse_prep;
	}

	/**
	 * Get the [oresponse_recovery] column value.
	 * 
	 * @return     double
	 */
	public function getOresponseRecovery()
	{

		return $this->oresponse_recovery;
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
	 * Set the value of [tsunami_social_science_data_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->tsunami_social_science_data_id !== $v) {
			$this->tsunami_social_science_data_id = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID;
		}

	} // setId()

	/**
	 * Set the value of [bkg] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setBkg($v)
	{

		if ($this->bkg !== $v) {
			$this->bkg = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::BKG;
		}

	} // setBkg()

	/**
	 * Set the value of [bkg_census] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setBkgCensus($v)
	{

		if ($this->bkg_census !== $v) {
			$this->bkg_census = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::BKG_CENSUS;
		}

	} // setBkgCensus()

	/**
	 * Set the value of [bkg_language_issues] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setBkgLanguageIssues($v)
	{

		if ($this->bkg_language_issues !== $v) {
			$this->bkg_language_issues = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::BKG_LANGUAGE_ISSUES;
		}

	} // setBkgLanguageIssues()

	/**
	 * Set the value of [bkg_tourist_stats] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setBkgTouristStats($v)
	{

		if ($this->bkg_tourist_stats !== $v) {
			$this->bkg_tourist_stats = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::BKG_TOURIST_STATS;
		}

	} // setBkgTouristStats()

	/**
	 * Set the value of [bkg_transport_systems] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setBkgTransportSystems($v)
	{

		if ($this->bkg_transport_systems !== $v) {
			$this->bkg_transport_systems = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::BKG_TRANSPORT_SYSTEMS;
		}

	} // setBkgTransportSystems()

	/**
	 * Set the value of [comm] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setComm($v)
	{

		if ($this->comm !== $v) {
			$this->comm = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::COMM;
		}

	} // setComm()

	/**
	 * Set the value of [comm_info_fromg] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCommInfoFromG($v)
	{

		if ($this->comm_info_fromg !== $v) {
			$this->comm_info_fromg = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::COMM_INFO_FROMG;
		}

	} // setCommInfoFromG()

	/**
	 * Set the value of [comm_warn_sys] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCommWarnSys($v)
	{

		if ($this->comm_warn_sys !== $v) {
			$this->comm_warn_sys = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::COMM_WARN_SYS;
		}

	} // setCommWarnSys()

	/**
	 * Set the value of [cresponse] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCresponse($v)
	{

		if ($this->cresponse !== $v) {
			$this->cresponse = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::CRESPONSE;
		}

	} // setCresponse()

	/**
	 * Set the value of [cresponse_intervw] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCresponseIntervw($v)
	{

		if ($this->cresponse_intervw !== $v) {
			$this->cresponse_intervw = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::CRESPONSE_INTERVW;
		}

	} // setCresponseIntervw()

	/**
	 * Set the value of [cresponse_mitigation] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCresponseMitigation($v)
	{

		if ($this->cresponse_mitigation !== $v) {
			$this->cresponse_mitigation = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::CRESPONSE_MITIGATION;
		}

	} // setCresponseMitigation()

	/**
	 * Set the value of [cresponse_prep] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCresponsePrep($v)
	{

		if ($this->cresponse_prep !== $v) {
			$this->cresponse_prep = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::CRESPONSE_PREP;
		}

	} // setCresponsePrep()

	/**
	 * Set the value of [cresponse_recovery] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCresponseRecovery($v)
	{

		if ($this->cresponse_recovery !== $v) {
			$this->cresponse_recovery = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::CRESPONSE_RECOVERY;
		}

	} // setCresponseRecovery()

	/**
	 * Set the value of [cresponse_warning] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setCresponseWarning($v)
	{

		if ($this->cresponse_warning !== $v) {
			$this->cresponse_warning = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::CRESPONSE_WARNING;
		}

	} // setCresponseWarning()

	/**
	 * Set the value of [damage] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDamage($v)
	{

		if ($this->damage !== $v) {
			$this->damage = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::DAMAGE;
		}

	} // setDamage()

	/**
	 * Set the value of [damage_cost_est] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDamageCostEst($v)
	{

		if ($this->damage_cost_est !== $v) {
			$this->damage_cost_est = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::DAMAGE_COST_EST;
		}

	} // setDamageCostEst()

	/**
	 * Set the value of [damage_industry] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDamageIndustry($v)
	{

		if ($this->damage_industry !== $v) {
			$this->damage_industry = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::DAMAGE_INDUSTRY;
		}

	} // setDamageIndustry()

	/**
	 * Set the value of [damage_type] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDamageType($v)
	{

		if ($this->damage_type !== $v) {
			$this->damage_type = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::DAMAGE_TYPE;
		}

	} // setDamageType()

	/**
	 * Set the value of [impact] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setImpact($v)
	{

		if ($this->impact !== $v) {
			$this->impact = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IMPACT;
		}

	} // setImpact()

	/**
	 * Set the value of [impact_num_dead] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setImpactNumDead($v)
	{

		if ($this->impact_num_dead !== $v) {
			$this->impact_num_dead = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IMPACT_NUM_DEAD;
		}

	} // setImpactNumDead()

	/**
	 * Set the value of [impact_num_fam_sep] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setImpactNumFamSep($v)
	{

		if ($this->impact_num_fam_sep !== $v) {
			$this->impact_num_fam_sep = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IMPACT_NUM_FAM_SEP;
		}

	} // setImpactNumFamSep()

	/**
	 * Set the value of [impact_num_homeless] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setImpactNumHomeless($v)
	{

		if ($this->impact_num_homeless !== $v) {
			$this->impact_num_homeless = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IMPACT_NUM_HOMELESS;
		}

	} // setImpactNumHomeless()

	/**
	 * Set the value of [impact_num_injured] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setImpactNumInjured($v)
	{

		if ($this->impact_num_injured !== $v) {
			$this->impact_num_injured = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IMPACT_NUM_INJURED;
		}

	} // setImpactNumInjured()

	/**
	 * Set the value of [impact_num_missing] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setImpactNumMissing($v)
	{

		if ($this->impact_num_missing !== $v) {
			$this->impact_num_missing = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IMPACT_NUM_MISSING;
		}

	} // setImpactNumMissing()

	/**
	 * Set the value of [iresponse] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setIresponse($v)
	{

		if ($this->iresponse !== $v) {
			$this->iresponse = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IRESPONSE;
		}

	} // setIresponse()

	/**
	 * Set the value of [iresponse_intervw] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setIresponseIntervw($v)
	{

		if ($this->iresponse_intervw !== $v) {
			$this->iresponse_intervw = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IRESPONSE_INTERVW;
		}

	} // setIresponseIntervw()

	/**
	 * Set the value of [iresponse_mitigation] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setIresponseMitigation($v)
	{

		if ($this->iresponse_mitigation !== $v) {
			$this->iresponse_mitigation = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IRESPONSE_MITIGATION;
		}

	} // setIresponseMitigation()

	/**
	 * Set the value of [iresponse_prep] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setIresponsePrep($v)
	{

		if ($this->iresponse_prep !== $v) {
			$this->iresponse_prep = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IRESPONSE_PREP;
		}

	} // setIresponsePrep()

	/**
	 * Set the value of [iresponse_recovery] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setIresponseRecovery($v)
	{

		if ($this->iresponse_recovery !== $v) {
			$this->iresponse_recovery = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IRESPONSE_RECOVERY;
		}

	} // setIresponseRecovery()

	/**
	 * Set the value of [iresponse_warnings] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setIresponseWarnings($v)
	{

		if ($this->iresponse_warnings !== $v) {
			$this->iresponse_warnings = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::IRESPONSE_WARNINGS;
		}

	} // setIresponseWarnings()

	/**
	 * Set the value of [oresponse] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOresponse($v)
	{

		if ($this->oresponse !== $v) {
			$this->oresponse = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::ORESPONSE;
		}

	} // setOresponse()

	/**
	 * Set the value of [oresponse_disease] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOresponseDisease($v)
	{

		if ($this->oresponse_disease !== $v) {
			$this->oresponse_disease = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::ORESPONSE_DISEASE;
		}

	} // setOresponseDisease()

	/**
	 * Set the value of [oresponse_grelief] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOresponseGrelief($v)
	{

		if ($this->oresponse_grelief !== $v) {
			$this->oresponse_grelief = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::ORESPONSE_GRELIEF;
		}

	} // setOresponseGrelief()

	/**
	 * Set the value of [oresponse_intervw] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOresponseIntervw($v)
	{

		if ($this->oresponse_intervw !== $v) {
			$this->oresponse_intervw = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::ORESPONSE_INTERVW;
		}

	} // setOresponseIntervw()

	/**
	 * Set the value of [oresponse_mitigation] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOresponseMitigation($v)
	{

		if ($this->oresponse_mitigation !== $v) {
			$this->oresponse_mitigation = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::ORESPONSE_MITIGATION;
		}

	} // setOresponseMitigation()

	/**
	 * Set the value of [oresponsengorelief] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOresponseNGORelief($v)
	{

		if ($this->oresponsengorelief !== $v) {
			$this->oresponsengorelief = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::ORESPONSENGORELIEF;
		}

	} // setOresponseNGORelief()

	/**
	 * Set the value of [oresponse_prep] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOresponsePrep($v)
	{

		if ($this->oresponse_prep !== $v) {
			$this->oresponse_prep = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::ORESPONSE_PREP;
		}

	} // setOresponsePrep()

	/**
	 * Set the value of [oresponse_recovery] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOresponseRecovery($v)
	{

		if ($this->oresponse_recovery !== $v) {
			$this->oresponse_recovery = $v;
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::ORESPONSE_RECOVERY;
		}

	} // setOresponseRecovery()

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
			$this->modifiedColumns[] = TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID;
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

			$this->tsunami_social_science_data_id = $rs->getFloat($startcol + 0);

			$this->bkg = $rs->getFloat($startcol + 1);

			$this->bkg_census = $rs->getFloat($startcol + 2);

			$this->bkg_language_issues = $rs->getFloat($startcol + 3);

			$this->bkg_tourist_stats = $rs->getFloat($startcol + 4);

			$this->bkg_transport_systems = $rs->getFloat($startcol + 5);

			$this->comm = $rs->getFloat($startcol + 6);

			$this->comm_info_fromg = $rs->getFloat($startcol + 7);

			$this->comm_warn_sys = $rs->getFloat($startcol + 8);

			$this->cresponse = $rs->getFloat($startcol + 9);

			$this->cresponse_intervw = $rs->getFloat($startcol + 10);

			$this->cresponse_mitigation = $rs->getFloat($startcol + 11);

			$this->cresponse_prep = $rs->getFloat($startcol + 12);

			$this->cresponse_recovery = $rs->getFloat($startcol + 13);

			$this->cresponse_warning = $rs->getFloat($startcol + 14);

			$this->damage = $rs->getFloat($startcol + 15);

			$this->damage_cost_est = $rs->getFloat($startcol + 16);

			$this->damage_industry = $rs->getFloat($startcol + 17);

			$this->damage_type = $rs->getFloat($startcol + 18);

			$this->impact = $rs->getFloat($startcol + 19);

			$this->impact_num_dead = $rs->getFloat($startcol + 20);

			$this->impact_num_fam_sep = $rs->getFloat($startcol + 21);

			$this->impact_num_homeless = $rs->getFloat($startcol + 22);

			$this->impact_num_injured = $rs->getFloat($startcol + 23);

			$this->impact_num_missing = $rs->getFloat($startcol + 24);

			$this->iresponse = $rs->getFloat($startcol + 25);

			$this->iresponse_intervw = $rs->getFloat($startcol + 26);

			$this->iresponse_mitigation = $rs->getFloat($startcol + 27);

			$this->iresponse_prep = $rs->getFloat($startcol + 28);

			$this->iresponse_recovery = $rs->getFloat($startcol + 29);

			$this->iresponse_warnings = $rs->getFloat($startcol + 30);

			$this->oresponse = $rs->getFloat($startcol + 31);

			$this->oresponse_disease = $rs->getFloat($startcol + 32);

			$this->oresponse_grelief = $rs->getFloat($startcol + 33);

			$this->oresponse_intervw = $rs->getFloat($startcol + 34);

			$this->oresponse_mitigation = $rs->getFloat($startcol + 35);

			$this->oresponsengorelief = $rs->getFloat($startcol + 36);

			$this->oresponse_prep = $rs->getFloat($startcol + 37);

			$this->oresponse_recovery = $rs->getFloat($startcol + 38);

			$this->tsunami_doc_lib_id = $rs->getFloat($startcol + 39);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 40; // 40 = TsunamiSocialScienceDataPeer::NUM_COLUMNS - TsunamiSocialScienceDataPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating TsunamiSocialScienceData object", $e);
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
			$con = Propel::getConnection(TsunamiSocialScienceDataPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			TsunamiSocialScienceDataPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(TsunamiSocialScienceDataPeer::DATABASE_NAME);
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
					$pk = TsunamiSocialScienceDataPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += TsunamiSocialScienceDataPeer::doUpdate($this, $con);
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


			if (($retval = TsunamiSocialScienceDataPeer::doValidate($this, $columns)) !== true) {
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
		$pos = TsunamiSocialScienceDataPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getBkg();
				break;
			case 2:
				return $this->getBkgCensus();
				break;
			case 3:
				return $this->getBkgLanguageIssues();
				break;
			case 4:
				return $this->getBkgTouristStats();
				break;
			case 5:
				return $this->getBkgTransportSystems();
				break;
			case 6:
				return $this->getComm();
				break;
			case 7:
				return $this->getCommInfoFromG();
				break;
			case 8:
				return $this->getCommWarnSys();
				break;
			case 9:
				return $this->getCresponse();
				break;
			case 10:
				return $this->getCresponseIntervw();
				break;
			case 11:
				return $this->getCresponseMitigation();
				break;
			case 12:
				return $this->getCresponsePrep();
				break;
			case 13:
				return $this->getCresponseRecovery();
				break;
			case 14:
				return $this->getCresponseWarning();
				break;
			case 15:
				return $this->getDamage();
				break;
			case 16:
				return $this->getDamageCostEst();
				break;
			case 17:
				return $this->getDamageIndustry();
				break;
			case 18:
				return $this->getDamageType();
				break;
			case 19:
				return $this->getImpact();
				break;
			case 20:
				return $this->getImpactNumDead();
				break;
			case 21:
				return $this->getImpactNumFamSep();
				break;
			case 22:
				return $this->getImpactNumHomeless();
				break;
			case 23:
				return $this->getImpactNumInjured();
				break;
			case 24:
				return $this->getImpactNumMissing();
				break;
			case 25:
				return $this->getIresponse();
				break;
			case 26:
				return $this->getIresponseIntervw();
				break;
			case 27:
				return $this->getIresponseMitigation();
				break;
			case 28:
				return $this->getIresponsePrep();
				break;
			case 29:
				return $this->getIresponseRecovery();
				break;
			case 30:
				return $this->getIresponseWarnings();
				break;
			case 31:
				return $this->getOresponse();
				break;
			case 32:
				return $this->getOresponseDisease();
				break;
			case 33:
				return $this->getOresponseGrelief();
				break;
			case 34:
				return $this->getOresponseIntervw();
				break;
			case 35:
				return $this->getOresponseMitigation();
				break;
			case 36:
				return $this->getOresponseNGORelief();
				break;
			case 37:
				return $this->getOresponsePrep();
				break;
			case 38:
				return $this->getOresponseRecovery();
				break;
			case 39:
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
		$keys = TsunamiSocialScienceDataPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getBkg(),
			$keys[2] => $this->getBkgCensus(),
			$keys[3] => $this->getBkgLanguageIssues(),
			$keys[4] => $this->getBkgTouristStats(),
			$keys[5] => $this->getBkgTransportSystems(),
			$keys[6] => $this->getComm(),
			$keys[7] => $this->getCommInfoFromG(),
			$keys[8] => $this->getCommWarnSys(),
			$keys[9] => $this->getCresponse(),
			$keys[10] => $this->getCresponseIntervw(),
			$keys[11] => $this->getCresponseMitigation(),
			$keys[12] => $this->getCresponsePrep(),
			$keys[13] => $this->getCresponseRecovery(),
			$keys[14] => $this->getCresponseWarning(),
			$keys[15] => $this->getDamage(),
			$keys[16] => $this->getDamageCostEst(),
			$keys[17] => $this->getDamageIndustry(),
			$keys[18] => $this->getDamageType(),
			$keys[19] => $this->getImpact(),
			$keys[20] => $this->getImpactNumDead(),
			$keys[21] => $this->getImpactNumFamSep(),
			$keys[22] => $this->getImpactNumHomeless(),
			$keys[23] => $this->getImpactNumInjured(),
			$keys[24] => $this->getImpactNumMissing(),
			$keys[25] => $this->getIresponse(),
			$keys[26] => $this->getIresponseIntervw(),
			$keys[27] => $this->getIresponseMitigation(),
			$keys[28] => $this->getIresponsePrep(),
			$keys[29] => $this->getIresponseRecovery(),
			$keys[30] => $this->getIresponseWarnings(),
			$keys[31] => $this->getOresponse(),
			$keys[32] => $this->getOresponseDisease(),
			$keys[33] => $this->getOresponseGrelief(),
			$keys[34] => $this->getOresponseIntervw(),
			$keys[35] => $this->getOresponseMitigation(),
			$keys[36] => $this->getOresponseNGORelief(),
			$keys[37] => $this->getOresponsePrep(),
			$keys[38] => $this->getOresponseRecovery(),
			$keys[39] => $this->getTsunamiDocLibId(),
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
		$pos = TsunamiSocialScienceDataPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setBkg($value);
				break;
			case 2:
				$this->setBkgCensus($value);
				break;
			case 3:
				$this->setBkgLanguageIssues($value);
				break;
			case 4:
				$this->setBkgTouristStats($value);
				break;
			case 5:
				$this->setBkgTransportSystems($value);
				break;
			case 6:
				$this->setComm($value);
				break;
			case 7:
				$this->setCommInfoFromG($value);
				break;
			case 8:
				$this->setCommWarnSys($value);
				break;
			case 9:
				$this->setCresponse($value);
				break;
			case 10:
				$this->setCresponseIntervw($value);
				break;
			case 11:
				$this->setCresponseMitigation($value);
				break;
			case 12:
				$this->setCresponsePrep($value);
				break;
			case 13:
				$this->setCresponseRecovery($value);
				break;
			case 14:
				$this->setCresponseWarning($value);
				break;
			case 15:
				$this->setDamage($value);
				break;
			case 16:
				$this->setDamageCostEst($value);
				break;
			case 17:
				$this->setDamageIndustry($value);
				break;
			case 18:
				$this->setDamageType($value);
				break;
			case 19:
				$this->setImpact($value);
				break;
			case 20:
				$this->setImpactNumDead($value);
				break;
			case 21:
				$this->setImpactNumFamSep($value);
				break;
			case 22:
				$this->setImpactNumHomeless($value);
				break;
			case 23:
				$this->setImpactNumInjured($value);
				break;
			case 24:
				$this->setImpactNumMissing($value);
				break;
			case 25:
				$this->setIresponse($value);
				break;
			case 26:
				$this->setIresponseIntervw($value);
				break;
			case 27:
				$this->setIresponseMitigation($value);
				break;
			case 28:
				$this->setIresponsePrep($value);
				break;
			case 29:
				$this->setIresponseRecovery($value);
				break;
			case 30:
				$this->setIresponseWarnings($value);
				break;
			case 31:
				$this->setOresponse($value);
				break;
			case 32:
				$this->setOresponseDisease($value);
				break;
			case 33:
				$this->setOresponseGrelief($value);
				break;
			case 34:
				$this->setOresponseIntervw($value);
				break;
			case 35:
				$this->setOresponseMitigation($value);
				break;
			case 36:
				$this->setOresponseNGORelief($value);
				break;
			case 37:
				$this->setOresponsePrep($value);
				break;
			case 38:
				$this->setOresponseRecovery($value);
				break;
			case 39:
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
		$keys = TsunamiSocialScienceDataPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setBkg($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setBkgCensus($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setBkgLanguageIssues($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setBkgTouristStats($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setBkgTransportSystems($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setComm($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCommInfoFromG($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCommWarnSys($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setCresponse($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setCresponseIntervw($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setCresponseMitigation($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setCresponsePrep($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setCresponseRecovery($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setCresponseWarning($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setDamage($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setDamageCostEst($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setDamageIndustry($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setDamageType($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setImpact($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setImpactNumDead($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setImpactNumFamSep($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setImpactNumHomeless($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setImpactNumInjured($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setImpactNumMissing($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setIresponse($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setIresponseIntervw($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setIresponseMitigation($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setIresponsePrep($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setIresponseRecovery($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setIresponseWarnings($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setOresponse($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setOresponseDisease($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setOresponseGrelief($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setOresponseIntervw($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setOresponseMitigation($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setOresponseNGORelief($arr[$keys[36]]);
		if (array_key_exists($keys[37], $arr)) $this->setOresponsePrep($arr[$keys[37]]);
		if (array_key_exists($keys[38], $arr)) $this->setOresponseRecovery($arr[$keys[38]]);
		if (array_key_exists($keys[39], $arr)) $this->setTsunamiDocLibId($arr[$keys[39]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(TsunamiSocialScienceDataPeer::DATABASE_NAME);

		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID)) $criteria->add(TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID, $this->tsunami_social_science_data_id);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::BKG)) $criteria->add(TsunamiSocialScienceDataPeer::BKG, $this->bkg);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::BKG_CENSUS)) $criteria->add(TsunamiSocialScienceDataPeer::BKG_CENSUS, $this->bkg_census);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::BKG_LANGUAGE_ISSUES)) $criteria->add(TsunamiSocialScienceDataPeer::BKG_LANGUAGE_ISSUES, $this->bkg_language_issues);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::BKG_TOURIST_STATS)) $criteria->add(TsunamiSocialScienceDataPeer::BKG_TOURIST_STATS, $this->bkg_tourist_stats);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::BKG_TRANSPORT_SYSTEMS)) $criteria->add(TsunamiSocialScienceDataPeer::BKG_TRANSPORT_SYSTEMS, $this->bkg_transport_systems);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::COMM)) $criteria->add(TsunamiSocialScienceDataPeer::COMM, $this->comm);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::COMM_INFO_FROMG)) $criteria->add(TsunamiSocialScienceDataPeer::COMM_INFO_FROMG, $this->comm_info_fromg);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::COMM_WARN_SYS)) $criteria->add(TsunamiSocialScienceDataPeer::COMM_WARN_SYS, $this->comm_warn_sys);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE)) $criteria->add(TsunamiSocialScienceDataPeer::CRESPONSE, $this->cresponse);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE_INTERVW)) $criteria->add(TsunamiSocialScienceDataPeer::CRESPONSE_INTERVW, $this->cresponse_intervw);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE_MITIGATION)) $criteria->add(TsunamiSocialScienceDataPeer::CRESPONSE_MITIGATION, $this->cresponse_mitigation);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE_PREP)) $criteria->add(TsunamiSocialScienceDataPeer::CRESPONSE_PREP, $this->cresponse_prep);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE_RECOVERY)) $criteria->add(TsunamiSocialScienceDataPeer::CRESPONSE_RECOVERY, $this->cresponse_recovery);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::CRESPONSE_WARNING)) $criteria->add(TsunamiSocialScienceDataPeer::CRESPONSE_WARNING, $this->cresponse_warning);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::DAMAGE)) $criteria->add(TsunamiSocialScienceDataPeer::DAMAGE, $this->damage);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::DAMAGE_COST_EST)) $criteria->add(TsunamiSocialScienceDataPeer::DAMAGE_COST_EST, $this->damage_cost_est);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::DAMAGE_INDUSTRY)) $criteria->add(TsunamiSocialScienceDataPeer::DAMAGE_INDUSTRY, $this->damage_industry);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::DAMAGE_TYPE)) $criteria->add(TsunamiSocialScienceDataPeer::DAMAGE_TYPE, $this->damage_type);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT)) $criteria->add(TsunamiSocialScienceDataPeer::IMPACT, $this->impact);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT_NUM_DEAD)) $criteria->add(TsunamiSocialScienceDataPeer::IMPACT_NUM_DEAD, $this->impact_num_dead);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT_NUM_FAM_SEP)) $criteria->add(TsunamiSocialScienceDataPeer::IMPACT_NUM_FAM_SEP, $this->impact_num_fam_sep);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT_NUM_HOMELESS)) $criteria->add(TsunamiSocialScienceDataPeer::IMPACT_NUM_HOMELESS, $this->impact_num_homeless);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT_NUM_INJURED)) $criteria->add(TsunamiSocialScienceDataPeer::IMPACT_NUM_INJURED, $this->impact_num_injured);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IMPACT_NUM_MISSING)) $criteria->add(TsunamiSocialScienceDataPeer::IMPACT_NUM_MISSING, $this->impact_num_missing);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE)) $criteria->add(TsunamiSocialScienceDataPeer::IRESPONSE, $this->iresponse);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE_INTERVW)) $criteria->add(TsunamiSocialScienceDataPeer::IRESPONSE_INTERVW, $this->iresponse_intervw);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE_MITIGATION)) $criteria->add(TsunamiSocialScienceDataPeer::IRESPONSE_MITIGATION, $this->iresponse_mitigation);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE_PREP)) $criteria->add(TsunamiSocialScienceDataPeer::IRESPONSE_PREP, $this->iresponse_prep);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE_RECOVERY)) $criteria->add(TsunamiSocialScienceDataPeer::IRESPONSE_RECOVERY, $this->iresponse_recovery);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::IRESPONSE_WARNINGS)) $criteria->add(TsunamiSocialScienceDataPeer::IRESPONSE_WARNINGS, $this->iresponse_warnings);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE)) $criteria->add(TsunamiSocialScienceDataPeer::ORESPONSE, $this->oresponse);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_DISEASE)) $criteria->add(TsunamiSocialScienceDataPeer::ORESPONSE_DISEASE, $this->oresponse_disease);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_GRELIEF)) $criteria->add(TsunamiSocialScienceDataPeer::ORESPONSE_GRELIEF, $this->oresponse_grelief);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_INTERVW)) $criteria->add(TsunamiSocialScienceDataPeer::ORESPONSE_INTERVW, $this->oresponse_intervw);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_MITIGATION)) $criteria->add(TsunamiSocialScienceDataPeer::ORESPONSE_MITIGATION, $this->oresponse_mitigation);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSENGORELIEF)) $criteria->add(TsunamiSocialScienceDataPeer::ORESPONSENGORELIEF, $this->oresponsengorelief);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_PREP)) $criteria->add(TsunamiSocialScienceDataPeer::ORESPONSE_PREP, $this->oresponse_prep);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::ORESPONSE_RECOVERY)) $criteria->add(TsunamiSocialScienceDataPeer::ORESPONSE_RECOVERY, $this->oresponse_recovery);
		if ($this->isColumnModified(TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID)) $criteria->add(TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID, $this->tsunami_doc_lib_id);

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
		$criteria = new Criteria(TsunamiSocialScienceDataPeer::DATABASE_NAME);

		$criteria->add(TsunamiSocialScienceDataPeer::TSUNAMI_SOCIAL_SCIENCE_DATA_ID, $this->tsunami_social_science_data_id);

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
	 * Generic method to set the primary key (tsunami_social_science_data_id column).
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
	 * @param      object $copyObj An object of TsunamiSocialScienceData (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setBkg($this->bkg);

		$copyObj->setBkgCensus($this->bkg_census);

		$copyObj->setBkgLanguageIssues($this->bkg_language_issues);

		$copyObj->setBkgTouristStats($this->bkg_tourist_stats);

		$copyObj->setBkgTransportSystems($this->bkg_transport_systems);

		$copyObj->setComm($this->comm);

		$copyObj->setCommInfoFromG($this->comm_info_fromg);

		$copyObj->setCommWarnSys($this->comm_warn_sys);

		$copyObj->setCresponse($this->cresponse);

		$copyObj->setCresponseIntervw($this->cresponse_intervw);

		$copyObj->setCresponseMitigation($this->cresponse_mitigation);

		$copyObj->setCresponsePrep($this->cresponse_prep);

		$copyObj->setCresponseRecovery($this->cresponse_recovery);

		$copyObj->setCresponseWarning($this->cresponse_warning);

		$copyObj->setDamage($this->damage);

		$copyObj->setDamageCostEst($this->damage_cost_est);

		$copyObj->setDamageIndustry($this->damage_industry);

		$copyObj->setDamageType($this->damage_type);

		$copyObj->setImpact($this->impact);

		$copyObj->setImpactNumDead($this->impact_num_dead);

		$copyObj->setImpactNumFamSep($this->impact_num_fam_sep);

		$copyObj->setImpactNumHomeless($this->impact_num_homeless);

		$copyObj->setImpactNumInjured($this->impact_num_injured);

		$copyObj->setImpactNumMissing($this->impact_num_missing);

		$copyObj->setIresponse($this->iresponse);

		$copyObj->setIresponseIntervw($this->iresponse_intervw);

		$copyObj->setIresponseMitigation($this->iresponse_mitigation);

		$copyObj->setIresponsePrep($this->iresponse_prep);

		$copyObj->setIresponseRecovery($this->iresponse_recovery);

		$copyObj->setIresponseWarnings($this->iresponse_warnings);

		$copyObj->setOresponse($this->oresponse);

		$copyObj->setOresponseDisease($this->oresponse_disease);

		$copyObj->setOresponseGrelief($this->oresponse_grelief);

		$copyObj->setOresponseIntervw($this->oresponse_intervw);

		$copyObj->setOresponseMitigation($this->oresponse_mitigation);

		$copyObj->setOresponseNGORelief($this->oresponsengorelief);

		$copyObj->setOresponsePrep($this->oresponse_prep);

		$copyObj->setOresponseRecovery($this->oresponse_recovery);

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
	 * @return     TsunamiSocialScienceData Clone of current object.
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
	 * @return     TsunamiSocialScienceDataPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new TsunamiSocialScienceDataPeer();
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

} // BaseTsunamiSocialScienceData
