<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/OrganizationPeer.php';

/**
 * Base class that represents a row from the 'ORGANIZATION' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseOrganization extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        OrganizationPeer
	 */
	protected static $peer;


	/**
	 * The value for the orgid field.
	 * @var        double
	 */
	protected $orgid;


	/**
	 * The value for the department field.
	 * @var        string
	 */
	protected $department;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the facilityid field.
	 * @var        double
	 */
	protected $facilityid;


	/**
	 * The value for the flextps_url field.
	 * @var        string
	 */
	protected $flextps_url;


	/**
	 * The value for the image_url field.
	 * @var        string
	 */
	protected $image_url;


	/**
	 * The value for the laboratory field.
	 * @var        string
	 */
	protected $laboratory;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the nawi_admin_users field.
	 * @var        string
	 */
	protected $nawi_admin_users;


	/**
	 * The value for the nawi_status field.
	 * @var        string
	 */
	protected $nawi_status;


	/**
	 * The value for the nsf_acknowledgement field.
	 * @var        string
	 */
	protected $nsf_acknowledgement;


	/**
	 * The value for the nsf_award_url field.
	 * @var        string
	 */
	protected $nsf_award_url;


	/**
	 * The value for the org_type_id field.
	 * @var        double
	 */
	protected $org_type_id = 0;


	/**
	 * The value for the parent_org_id field.
	 * @var        double
	 */
	protected $parent_org_id;


	/**
	 * The value for the pop_url field.
	 * @var        string
	 */
	protected $pop_url;


	/**
	 * The value for the sensor_manifest_id field.
	 * @var        double
	 */
	protected $sensor_manifest_id;


	/**
	 * The value for the short_name field.
	 * @var        string
	 */
	protected $short_name;


	/**
	 * The value for the sitename field.
	 * @var        string
	 */
	protected $sitename;


	/**
	 * The value for the site_op_user field.
	 * @var        string
	 */
	protected $site_op_user;


	/**
	 * The value for the sysadmin field.
	 * @var        string
	 */
	protected $sysadmin;


	/**
	 * The value for the sysadmin_email field.
	 * @var        string
	 */
	protected $sysadmin_email;


	/**
	 * The value for the sysadmin_user field.
	 * @var        string
	 */
	protected $sysadmin_user;


	/**
	 * The value for the timezone field.
	 * @var        string
	 */
	protected $timezone;


	/**
	 * The value for the url field.
	 * @var        string
	 */
	protected $url;

	/**
	 * @var        Organization
	 */
	protected $aOrganizationRelatedByFacilityId;

	/**
	 * @var        Organization
	 */
	protected $aOrganizationRelatedByParentOrgId;

	/**
	 * @var        SensorManifest
	 */
	protected $aSensorManifest;

	/**
	 * Collection to store aggregation of collEquipments.
	 * @var        array
	 */
	protected $collEquipments;

	/**
	 * The criteria used to select the current contents of collEquipments.
	 * @var        Criteria
	 */
	protected $lastEquipmentCriteria = null;

	/**
	 * Collection to store aggregation of collExperimentFacilitys.
	 * @var        array
	 */
	protected $collExperimentFacilitys;

	/**
	 * The criteria used to select the current contents of collExperimentFacilitys.
	 * @var        Criteria
	 */
	protected $lastExperimentFacilityCriteria = null;

	/**
	 * Collection to store aggregation of collExperimentOrganizations.
	 * @var        array
	 */
	protected $collExperimentOrganizations;

	/**
	 * The criteria used to select the current contents of collExperimentOrganizations.
	 * @var        Criteria
	 */
	protected $lastExperimentOrganizationCriteria = null;

	/**
	 * Collection to store aggregation of collFacilityDataFiles.
	 * @var        array
	 */
	protected $collFacilityDataFiles;

	/**
	 * The criteria used to select the current contents of collFacilityDataFiles.
	 * @var        Criteria
	 */
	protected $lastFacilityDataFileCriteria = null;

	/**
	 * Collection to store aggregation of collNAWIFacilitys.
	 * @var        array
	 */
	protected $collNAWIFacilitys;

	/**
	 * The criteria used to select the current contents of collNAWIFacilitys.
	 * @var        Criteria
	 */
	protected $lastNAWIFacilityCriteria = null;

	/**
	 * Collection to store aggregation of collOrganizationsRelatedByFacilityId.
	 * @var        array
	 */
	protected $collOrganizationsRelatedByFacilityId;

	/**
	 * The criteria used to select the current contents of collOrganizationsRelatedByFacilityId.
	 * @var        Criteria
	 */
	protected $lastOrganizationRelatedByFacilityIdCriteria = null;

	/**
	 * Collection to store aggregation of collOrganizationsRelatedByParentOrgId.
	 * @var        array
	 */
	protected $collOrganizationsRelatedByParentOrgId;

	/**
	 * The criteria used to select the current contents of collOrganizationsRelatedByParentOrgId.
	 * @var        Criteria
	 */
	protected $lastOrganizationRelatedByParentOrgIdCriteria = null;

	/**
	 * Collection to store aggregation of collProjectOrganizations.
	 * @var        array
	 */
	protected $collProjectOrganizations;

	/**
	 * The criteria used to select the current contents of collProjectOrganizations.
	 * @var        Criteria
	 */
	protected $lastProjectOrganizationCriteria = null;

	/**
	 * Collection to store aggregation of collCoordinators.
	 * @var        array
	 */
	protected $collCoordinators;

	/**
	 * The criteria used to select the current contents of collCoordinators.
	 * @var        Criteria
	 */
	protected $lastCoordinatorCriteria = null;

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
	 * Get the [orgid] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->orgid;
	}

	/**
	 * Get the [department] column value.
	 * 
	 * @return     string
	 */
	public function getDepartment()
	{

		return $this->department;
	}

	/**
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
	}

	/**
	 * Get the [facilityid] column value.
	 * 
	 * @return     double
	 */
	public function getFacilityId()
	{

		return $this->facilityid;
	}

	/**
	 * Get the [flextps_url] column value.
	 * 
	 * @return     string
	 */
	public function getFlexTpsUrl()
	{

		return $this->flextps_url;
	}

	/**
	 * Get the [image_url] column value.
	 * 
	 * @return     string
	 */
	public function getImageUrl()
	{

		return $this->image_url;
	}

	/**
	 * Get the [laboratory] column value.
	 * 
	 * @return     string
	 */
	public function getLaboratory()
	{

		return $this->laboratory;
	}

	/**
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{

		return $this->name;
	}

	/**
	 * Get the [nawi_admin_users] column value.
	 * 
	 * @return     string
	 */
	public function getNawiAdminUsers()
	{

		return $this->nawi_admin_users;
	}

	/**
	 * Get the [nawi_status] column value.
	 * 
	 * @return     string
	 */
	public function getNawiStatus()
	{

		return $this->nawi_status;
	}

	/**
	 * Get the [nsf_acknowledgement] column value.
	 * 
	 * @return     string
	 */
	public function getNsfAcknowledgement()
	{

		return $this->nsf_acknowledgement;
	}

	/**
	 * Get the [nsf_award_url] column value.
	 * 
	 * @return     string
	 */
	public function getNsfAwardUrl()
	{

		return $this->nsf_award_url;
	}

	/**
	 * Get the [org_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getOrganizationTypeId()
	{

		return $this->org_type_id;
	}

	/**
	 * Get the [parent_org_id] column value.
	 * 
	 * @return     double
	 */
	public function getParentOrgId()
	{

		return $this->parent_org_id;
	}

	/**
	 * Get the [pop_url] column value.
	 * 
	 * @return     string
	 */
	public function getPopUrl()
	{

		return $this->pop_url;
	}

	/**
	 * Get the [sensor_manifest_id] column value.
	 * 
	 * @return     double
	 */
	public function getSensorManifestId()
	{

		return $this->sensor_manifest_id;
	}

	/**
	 * Get the [short_name] column value.
	 * 
	 * @return     string
	 */
	public function getShortName()
	{

		return $this->short_name;
	}

	/**
	 * Get the [sitename] column value.
	 * 
	 * @return     string
	 */
	public function getSiteName()
	{

		return $this->sitename;
	}

	/**
	 * Get the [site_op_user] column value.
	 * 
	 * @return     string
	 */
	public function getSiteOpUser()
	{

		return $this->site_op_user;
	}

	/**
	 * Get the [sysadmin] column value.
	 * 
	 * @return     string
	 */
	public function getSysadmin()
	{

		return $this->sysadmin;
	}

	/**
	 * Get the [sysadmin_email] column value.
	 * 
	 * @return     string
	 */
	public function getSysadminEmail()
	{

		return $this->sysadmin_email;
	}

	/**
	 * Get the [sysadmin_user] column value.
	 * 
	 * @return     string
	 */
	public function getSysadminUser()
	{

		return $this->sysadmin_user;
	}

	/**
	 * Get the [timezone] column value.
	 * 
	 * @return     string
	 */
	public function getTimezone()
	{

		return $this->timezone;
	}

	/**
	 * Get the [url] column value.
	 * 
	 * @return     string
	 */
	public function getUrl()
	{

		return $this->url;
	}

	/**
	 * Set the value of [orgid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->orgid !== $v) {
			$this->orgid = $v;
			$this->modifiedColumns[] = OrganizationPeer::ORGID;
		}

	} // setId()

	/**
	 * Set the value of [department] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDepartment($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->department !== $v) {
			$this->department = $v;
			$this->modifiedColumns[] = OrganizationPeer::DEPARTMENT;
		}

	} // setDepartment()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDescription($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->description) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->description !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->description = $obj;
			$this->modifiedColumns[] = OrganizationPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [facilityid] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFacilityId($v)
	{

		if ($this->facilityid !== $v) {
			$this->facilityid = $v;
			$this->modifiedColumns[] = OrganizationPeer::FACILITYID;
		}

		if ($this->aOrganizationRelatedByFacilityId !== null && $this->aOrganizationRelatedByFacilityId->getId() !== $v) {
			$this->aOrganizationRelatedByFacilityId = null;
		}

	} // setFacilityId()

	/**
	 * Set the value of [flextps_url] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFlexTpsUrl($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->flextps_url !== $v) {
			$this->flextps_url = $v;
			$this->modifiedColumns[] = OrganizationPeer::FLEXTPS_URL;
		}

	} // setFlexTpsUrl()

	/**
	 * Set the value of [image_url] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setImageUrl($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->image_url !== $v) {
			$this->image_url = $v;
			$this->modifiedColumns[] = OrganizationPeer::IMAGE_URL;
		}

	} // setImageUrl()

	/**
	 * Set the value of [laboratory] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setLaboratory($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->laboratory !== $v) {
			$this->laboratory = $v;
			$this->modifiedColumns[] = OrganizationPeer::LABORATORY;
		}

	} // setLaboratory()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = OrganizationPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [nawi_admin_users] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNawiAdminUsers($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->nawi_admin_users !== $v) {
			$this->nawi_admin_users = $v;
			$this->modifiedColumns[] = OrganizationPeer::NAWI_ADMIN_USERS;
		}

	} // setNawiAdminUsers()

	/**
	 * Set the value of [nawi_status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNawiStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->nawi_status !== $v) {
			$this->nawi_status = $v;
			$this->modifiedColumns[] = OrganizationPeer::NAWI_STATUS;
		}

	} // setNawiStatus()

	/**
	 * Set the value of [nsf_acknowledgement] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNsfAcknowledgement($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->nsf_acknowledgement) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->nsf_acknowledgement !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->nsf_acknowledgement = $obj;
			$this->modifiedColumns[] = OrganizationPeer::NSF_ACKNOWLEDGEMENT;
		}

	} // setNsfAcknowledgement()

	/**
	 * Set the value of [nsf_award_url] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setNsfAwardUrl($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->nsf_award_url !== $v) {
			$this->nsf_award_url = $v;
			$this->modifiedColumns[] = OrganizationPeer::NSF_AWARD_URL;
		}

	} // setNsfAwardUrl()

	/**
	 * Set the value of [org_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setOrganizationTypeId($v)
	{

		if ($this->org_type_id !== $v || $v === 0) {
			$this->org_type_id = $v;
			$this->modifiedColumns[] = OrganizationPeer::ORG_TYPE_ID;
		}

	} // setOrganizationTypeId()

	/**
	 * Set the value of [parent_org_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setParentOrgId($v)
	{

		if ($this->parent_org_id !== $v) {
			$this->parent_org_id = $v;
			$this->modifiedColumns[] = OrganizationPeer::PARENT_ORG_ID;
		}

		if ($this->aOrganizationRelatedByParentOrgId !== null && $this->aOrganizationRelatedByParentOrgId->getId() !== $v) {
			$this->aOrganizationRelatedByParentOrgId = null;
		}

	} // setParentOrgId()

	/**
	 * Set the value of [pop_url] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPopUrl($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->pop_url !== $v) {
			$this->pop_url = $v;
			$this->modifiedColumns[] = OrganizationPeer::POP_URL;
		}

	} // setPopUrl()

	/**
	 * Set the value of [sensor_manifest_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSensorManifestId($v)
	{

		if ($this->sensor_manifest_id !== $v) {
			$this->sensor_manifest_id = $v;
			$this->modifiedColumns[] = OrganizationPeer::SENSOR_MANIFEST_ID;
		}

		if ($this->aSensorManifest !== null && $this->aSensorManifest->getId() !== $v) {
			$this->aSensorManifest = null;
		}

	} // setSensorManifestId()

	/**
	 * Set the value of [short_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setShortName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->short_name !== $v) {
			$this->short_name = $v;
			$this->modifiedColumns[] = OrganizationPeer::SHORT_NAME;
		}

	} // setShortName()

	/**
	 * Set the value of [sitename] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSiteName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sitename !== $v) {
			$this->sitename = $v;
			$this->modifiedColumns[] = OrganizationPeer::SITENAME;
		}

	} // setSiteName()

	/**
	 * Set the value of [site_op_user] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSiteOpUser($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->site_op_user !== $v) {
			$this->site_op_user = $v;
			$this->modifiedColumns[] = OrganizationPeer::SITE_OP_USER;
		}

	} // setSiteOpUser()

	/**
	 * Set the value of [sysadmin] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSysadmin($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sysadmin !== $v) {
			$this->sysadmin = $v;
			$this->modifiedColumns[] = OrganizationPeer::SYSADMIN;
		}

	} // setSysadmin()

	/**
	 * Set the value of [sysadmin_email] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSysadminEmail($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sysadmin_email !== $v) {
			$this->sysadmin_email = $v;
			$this->modifiedColumns[] = OrganizationPeer::SYSADMIN_EMAIL;
		}

	} // setSysadminEmail()

	/**
	 * Set the value of [sysadmin_user] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSysadminUser($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->sysadmin_user !== $v) {
			$this->sysadmin_user = $v;
			$this->modifiedColumns[] = OrganizationPeer::SYSADMIN_USER;
		}

	} // setSysadminUser()

	/**
	 * Set the value of [timezone] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTimezone($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->timezone !== $v) {
			$this->timezone = $v;
			$this->modifiedColumns[] = OrganizationPeer::TIMEZONE;
		}

	} // setTimezone()

	/**
	 * Set the value of [url] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setUrl($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->url !== $v) {
			$this->url = $v;
			$this->modifiedColumns[] = OrganizationPeer::URL;
		}

	} // setUrl()

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

			$this->orgid = $rs->getFloat($startcol + 0);

			$this->department = $rs->getString($startcol + 1);

			$this->description = $rs->getClob($startcol + 2);

			$this->facilityid = $rs->getFloat($startcol + 3);

			$this->flextps_url = $rs->getString($startcol + 4);

			$this->image_url = $rs->getString($startcol + 5);

			$this->laboratory = $rs->getString($startcol + 6);

			$this->name = $rs->getString($startcol + 7);

			$this->nawi_admin_users = $rs->getString($startcol + 8);

			$this->nawi_status = $rs->getString($startcol + 9);

			$this->nsf_acknowledgement = $rs->getClob($startcol + 10);

			$this->nsf_award_url = $rs->getString($startcol + 11);

			$this->org_type_id = $rs->getFloat($startcol + 12);

			$this->parent_org_id = $rs->getFloat($startcol + 13);

			$this->pop_url = $rs->getString($startcol + 14);

			$this->sensor_manifest_id = $rs->getFloat($startcol + 15);

			$this->short_name = $rs->getString($startcol + 16);

			$this->sitename = $rs->getString($startcol + 17);

			$this->site_op_user = $rs->getString($startcol + 18);

			$this->sysadmin = $rs->getString($startcol + 19);

			$this->sysadmin_email = $rs->getString($startcol + 20);

			$this->sysadmin_user = $rs->getString($startcol + 21);

			$this->timezone = $rs->getString($startcol + 22);

			$this->url = $rs->getString($startcol + 23);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 24; // 24 = OrganizationPeer::NUM_COLUMNS - OrganizationPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Organization object", $e);
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
			$con = Propel::getConnection(OrganizationPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			OrganizationPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(OrganizationPeer::DATABASE_NAME);
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

			if ($this->aOrganizationRelatedByFacilityId !== null) {
				if ($this->aOrganizationRelatedByFacilityId->isModified()) {
					$affectedRows += $this->aOrganizationRelatedByFacilityId->save($con);
				}
				$this->setOrganizationRelatedByFacilityId($this->aOrganizationRelatedByFacilityId);
			}

			if ($this->aOrganizationRelatedByParentOrgId !== null) {
				if ($this->aOrganizationRelatedByParentOrgId->isModified()) {
					$affectedRows += $this->aOrganizationRelatedByParentOrgId->save($con);
				}
				$this->setOrganizationRelatedByParentOrgId($this->aOrganizationRelatedByParentOrgId);
			}

			if ($this->aSensorManifest !== null) {
				if ($this->aSensorManifest->isModified()) {
					$affectedRows += $this->aSensorManifest->save($con);
				}
				$this->setSensorManifest($this->aSensorManifest);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = OrganizationPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += OrganizationPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collEquipments !== null) {
				foreach($this->collEquipments as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collExperimentFacilitys !== null) {
				foreach($this->collExperimentFacilitys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collExperimentOrganizations !== null) {
				foreach($this->collExperimentOrganizations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collFacilityDataFiles !== null) {
				foreach($this->collFacilityDataFiles as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collNAWIFacilitys !== null) {
				foreach($this->collNAWIFacilitys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collOrganizationsRelatedByFacilityId !== null) {
				foreach($this->collOrganizationsRelatedByFacilityId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collOrganizationsRelatedByParentOrgId !== null) {
				foreach($this->collOrganizationsRelatedByParentOrgId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collProjectOrganizations !== null) {
				foreach($this->collProjectOrganizations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinators !== null) {
				foreach($this->collCoordinators as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
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

			if ($this->aOrganizationRelatedByFacilityId !== null) {
				if (!$this->aOrganizationRelatedByFacilityId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aOrganizationRelatedByFacilityId->getValidationFailures());
				}
			}

			if ($this->aOrganizationRelatedByParentOrgId !== null) {
				if (!$this->aOrganizationRelatedByParentOrgId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aOrganizationRelatedByParentOrgId->getValidationFailures());
				}
			}

			if ($this->aSensorManifest !== null) {
				if (!$this->aSensorManifest->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSensorManifest->getValidationFailures());
				}
			}


			if (($retval = OrganizationPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collEquipments !== null) {
					foreach($this->collEquipments as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collExperimentFacilitys !== null) {
					foreach($this->collExperimentFacilitys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collExperimentOrganizations !== null) {
					foreach($this->collExperimentOrganizations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collFacilityDataFiles !== null) {
					foreach($this->collFacilityDataFiles as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collNAWIFacilitys !== null) {
					foreach($this->collNAWIFacilitys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collProjectOrganizations !== null) {
					foreach($this->collProjectOrganizations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCoordinators !== null) {
					foreach($this->collCoordinators as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
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
		$pos = OrganizationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDepartment();
				break;
			case 2:
				return $this->getDescription();
				break;
			case 3:
				return $this->getFacilityId();
				break;
			case 4:
				return $this->getFlexTpsUrl();
				break;
			case 5:
				return $this->getImageUrl();
				break;
			case 6:
				return $this->getLaboratory();
				break;
			case 7:
				return $this->getName();
				break;
			case 8:
				return $this->getNawiAdminUsers();
				break;
			case 9:
				return $this->getNawiStatus();
				break;
			case 10:
				return $this->getNsfAcknowledgement();
				break;
			case 11:
				return $this->getNsfAwardUrl();
				break;
			case 12:
				return $this->getOrganizationTypeId();
				break;
			case 13:
				return $this->getParentOrgId();
				break;
			case 14:
				return $this->getPopUrl();
				break;
			case 15:
				return $this->getSensorManifestId();
				break;
			case 16:
				return $this->getShortName();
				break;
			case 17:
				return $this->getSiteName();
				break;
			case 18:
				return $this->getSiteOpUser();
				break;
			case 19:
				return $this->getSysadmin();
				break;
			case 20:
				return $this->getSysadminEmail();
				break;
			case 21:
				return $this->getSysadminUser();
				break;
			case 22:
				return $this->getTimezone();
				break;
			case 23:
				return $this->getUrl();
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
		$keys = OrganizationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getDepartment(),
			$keys[2] => $this->getDescription(),
			$keys[3] => $this->getFacilityId(),
			$keys[4] => $this->getFlexTpsUrl(),
			$keys[5] => $this->getImageUrl(),
			$keys[6] => $this->getLaboratory(),
			$keys[7] => $this->getName(),
			$keys[8] => $this->getNawiAdminUsers(),
			$keys[9] => $this->getNawiStatus(),
			$keys[10] => $this->getNsfAcknowledgement(),
			$keys[11] => $this->getNsfAwardUrl(),
			$keys[12] => $this->getOrganizationTypeId(),
			$keys[13] => $this->getParentOrgId(),
			$keys[14] => $this->getPopUrl(),
			$keys[15] => $this->getSensorManifestId(),
			$keys[16] => $this->getShortName(),
			$keys[17] => $this->getSiteName(),
			$keys[18] => $this->getSiteOpUser(),
			$keys[19] => $this->getSysadmin(),
			$keys[20] => $this->getSysadminEmail(),
			$keys[21] => $this->getSysadminUser(),
			$keys[22] => $this->getTimezone(),
			$keys[23] => $this->getUrl(),
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
		$pos = OrganizationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDepartment($value);
				break;
			case 2:
				$this->setDescription($value);
				break;
			case 3:
				$this->setFacilityId($value);
				break;
			case 4:
				$this->setFlexTpsUrl($value);
				break;
			case 5:
				$this->setImageUrl($value);
				break;
			case 6:
				$this->setLaboratory($value);
				break;
			case 7:
				$this->setName($value);
				break;
			case 8:
				$this->setNawiAdminUsers($value);
				break;
			case 9:
				$this->setNawiStatus($value);
				break;
			case 10:
				$this->setNsfAcknowledgement($value);
				break;
			case 11:
				$this->setNsfAwardUrl($value);
				break;
			case 12:
				$this->setOrganizationTypeId($value);
				break;
			case 13:
				$this->setParentOrgId($value);
				break;
			case 14:
				$this->setPopUrl($value);
				break;
			case 15:
				$this->setSensorManifestId($value);
				break;
			case 16:
				$this->setShortName($value);
				break;
			case 17:
				$this->setSiteName($value);
				break;
			case 18:
				$this->setSiteOpUser($value);
				break;
			case 19:
				$this->setSysadmin($value);
				break;
			case 20:
				$this->setSysadminEmail($value);
				break;
			case 21:
				$this->setSysadminUser($value);
				break;
			case 22:
				$this->setTimezone($value);
				break;
			case 23:
				$this->setUrl($value);
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
		$keys = OrganizationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDepartment($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDescription($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setFacilityId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setFlexTpsUrl($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setImageUrl($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setLaboratory($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setName($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setNawiAdminUsers($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setNawiStatus($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setNsfAcknowledgement($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setNsfAwardUrl($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setOrganizationTypeId($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setParentOrgId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setPopUrl($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setSensorManifestId($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setShortName($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setSiteName($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setSiteOpUser($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setSysadmin($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setSysadminEmail($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setSysadminUser($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setTimezone($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setUrl($arr[$keys[23]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(OrganizationPeer::DATABASE_NAME);

		if ($this->isColumnModified(OrganizationPeer::ORGID)) $criteria->add(OrganizationPeer::ORGID, $this->orgid);
		if ($this->isColumnModified(OrganizationPeer::DEPARTMENT)) $criteria->add(OrganizationPeer::DEPARTMENT, $this->department);
		if ($this->isColumnModified(OrganizationPeer::DESCRIPTION)) $criteria->add(OrganizationPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(OrganizationPeer::FACILITYID)) $criteria->add(OrganizationPeer::FACILITYID, $this->facilityid);
		if ($this->isColumnModified(OrganizationPeer::FLEXTPS_URL)) $criteria->add(OrganizationPeer::FLEXTPS_URL, $this->flextps_url);
		if ($this->isColumnModified(OrganizationPeer::IMAGE_URL)) $criteria->add(OrganizationPeer::IMAGE_URL, $this->image_url);
		if ($this->isColumnModified(OrganizationPeer::LABORATORY)) $criteria->add(OrganizationPeer::LABORATORY, $this->laboratory);
		if ($this->isColumnModified(OrganizationPeer::NAME)) $criteria->add(OrganizationPeer::NAME, $this->name);
		if ($this->isColumnModified(OrganizationPeer::NAWI_ADMIN_USERS)) $criteria->add(OrganizationPeer::NAWI_ADMIN_USERS, $this->nawi_admin_users);
		if ($this->isColumnModified(OrganizationPeer::NAWI_STATUS)) $criteria->add(OrganizationPeer::NAWI_STATUS, $this->nawi_status);
		if ($this->isColumnModified(OrganizationPeer::NSF_ACKNOWLEDGEMENT)) $criteria->add(OrganizationPeer::NSF_ACKNOWLEDGEMENT, $this->nsf_acknowledgement);
		if ($this->isColumnModified(OrganizationPeer::NSF_AWARD_URL)) $criteria->add(OrganizationPeer::NSF_AWARD_URL, $this->nsf_award_url);
		if ($this->isColumnModified(OrganizationPeer::ORG_TYPE_ID)) $criteria->add(OrganizationPeer::ORG_TYPE_ID, $this->org_type_id);
		if ($this->isColumnModified(OrganizationPeer::PARENT_ORG_ID)) $criteria->add(OrganizationPeer::PARENT_ORG_ID, $this->parent_org_id);
		if ($this->isColumnModified(OrganizationPeer::POP_URL)) $criteria->add(OrganizationPeer::POP_URL, $this->pop_url);
		if ($this->isColumnModified(OrganizationPeer::SENSOR_MANIFEST_ID)) $criteria->add(OrganizationPeer::SENSOR_MANIFEST_ID, $this->sensor_manifest_id);
		if ($this->isColumnModified(OrganizationPeer::SHORT_NAME)) $criteria->add(OrganizationPeer::SHORT_NAME, $this->short_name);
		if ($this->isColumnModified(OrganizationPeer::SITENAME)) $criteria->add(OrganizationPeer::SITENAME, $this->sitename);
		if ($this->isColumnModified(OrganizationPeer::SITE_OP_USER)) $criteria->add(OrganizationPeer::SITE_OP_USER, $this->site_op_user);
		if ($this->isColumnModified(OrganizationPeer::SYSADMIN)) $criteria->add(OrganizationPeer::SYSADMIN, $this->sysadmin);
		if ($this->isColumnModified(OrganizationPeer::SYSADMIN_EMAIL)) $criteria->add(OrganizationPeer::SYSADMIN_EMAIL, $this->sysadmin_email);
		if ($this->isColumnModified(OrganizationPeer::SYSADMIN_USER)) $criteria->add(OrganizationPeer::SYSADMIN_USER, $this->sysadmin_user);
		if ($this->isColumnModified(OrganizationPeer::TIMEZONE)) $criteria->add(OrganizationPeer::TIMEZONE, $this->timezone);
		if ($this->isColumnModified(OrganizationPeer::URL)) $criteria->add(OrganizationPeer::URL, $this->url);

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
		$criteria = new Criteria(OrganizationPeer::DATABASE_NAME);

		$criteria->add(OrganizationPeer::ORGID, $this->orgid);

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
	 * Generic method to set the primary key (orgid column).
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
	 * @param      object $copyObj An object of Organization (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDepartment($this->department);

		$copyObj->setDescription($this->description);

		$copyObj->setFacilityId($this->facilityid);

		$copyObj->setFlexTpsUrl($this->flextps_url);

		$copyObj->setImageUrl($this->image_url);

		$copyObj->setLaboratory($this->laboratory);

		$copyObj->setName($this->name);

		$copyObj->setNawiAdminUsers($this->nawi_admin_users);

		$copyObj->setNawiStatus($this->nawi_status);

		$copyObj->setNsfAcknowledgement($this->nsf_acknowledgement);

		$copyObj->setNsfAwardUrl($this->nsf_award_url);

		$copyObj->setOrganizationTypeId($this->org_type_id);

		$copyObj->setParentOrgId($this->parent_org_id);

		$copyObj->setPopUrl($this->pop_url);

		$copyObj->setSensorManifestId($this->sensor_manifest_id);

		$copyObj->setShortName($this->short_name);

		$copyObj->setSiteName($this->sitename);

		$copyObj->setSiteOpUser($this->site_op_user);

		$copyObj->setSysadmin($this->sysadmin);

		$copyObj->setSysadminEmail($this->sysadmin_email);

		$copyObj->setSysadminUser($this->sysadmin_user);

		$copyObj->setTimezone($this->timezone);

		$copyObj->setUrl($this->url);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getEquipments() as $relObj) {
				$copyObj->addEquipment($relObj->copy($deepCopy));
			}

			foreach($this->getExperimentFacilitys() as $relObj) {
				$copyObj->addExperimentFacility($relObj->copy($deepCopy));
			}

			foreach($this->getExperimentOrganizations() as $relObj) {
				$copyObj->addExperimentOrganization($relObj->copy($deepCopy));
			}

			foreach($this->getFacilityDataFiles() as $relObj) {
				$copyObj->addFacilityDataFile($relObj->copy($deepCopy));
			}

			foreach($this->getNAWIFacilitys() as $relObj) {
				$copyObj->addNAWIFacility($relObj->copy($deepCopy));
			}

			foreach($this->getOrganizationsRelatedByFacilityId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addOrganizationRelatedByFacilityId($relObj->copy($deepCopy));
			}

			foreach($this->getOrganizationsRelatedByParentOrgId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addOrganizationRelatedByParentOrgId($relObj->copy($deepCopy));
			}

			foreach($this->getProjectOrganizations() as $relObj) {
				$copyObj->addProjectOrganization($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinators() as $relObj) {
				$copyObj->addCoordinator($relObj->copy($deepCopy));
			}

		} // if ($deepCopy)


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
	 * @return     Organization Clone of current object.
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
	 * @return     OrganizationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new OrganizationPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Organization object.
	 *
	 * @param      Organization $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setOrganizationRelatedByFacilityId($v)
	{


		if ($v === null) {
			$this->setFacilityId(NULL);
		} else {
			$this->setFacilityId($v->getId());
		}


		$this->aOrganizationRelatedByFacilityId = $v;
	}


	/**
	 * Get the associated Organization object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Organization The associated Organization object.
	 * @throws     PropelException
	 */
	public function getOrganizationRelatedByFacilityId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';

		if ($this->aOrganizationRelatedByFacilityId === null && ($this->facilityid > 0)) {

			$this->aOrganizationRelatedByFacilityId = OrganizationPeer::retrieveByPK($this->facilityid, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = OrganizationPeer::retrieveByPK($this->facilityid, $con);
			   $obj->addOrganizationsRelatedByFacilityId($this);
			 */
		}
		return $this->aOrganizationRelatedByFacilityId;
	}

	/**
	 * Declares an association between this object and a Organization object.
	 *
	 * @param      Organization $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setOrganizationRelatedByParentOrgId($v)
	{


		if ($v === null) {
			$this->setParentOrgId(NULL);
		} else {
			$this->setParentOrgId($v->getId());
		}


		$this->aOrganizationRelatedByParentOrgId = $v;
	}


	/**
	 * Get the associated Organization object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Organization The associated Organization object.
	 * @throws     PropelException
	 */
	public function getOrganizationRelatedByParentOrgId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';

		if ($this->aOrganizationRelatedByParentOrgId === null && ($this->parent_org_id > 0)) {

			$this->aOrganizationRelatedByParentOrgId = OrganizationPeer::retrieveByPK($this->parent_org_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = OrganizationPeer::retrieveByPK($this->parent_org_id, $con);
			   $obj->addOrganizationsRelatedByParentOrgId($this);
			 */
		}
		return $this->aOrganizationRelatedByParentOrgId;
	}

	/**
	 * Declares an association between this object and a SensorManifest object.
	 *
	 * @param      SensorManifest $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setSensorManifest($v)
	{


		if ($v === null) {
			$this->setSensorManifestId(NULL);
		} else {
			$this->setSensorManifestId($v->getId());
		}


		$this->aSensorManifest = $v;
	}


	/**
	 * Get the associated SensorManifest object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     SensorManifest The associated SensorManifest object.
	 * @throws     PropelException
	 */
	public function getSensorManifest($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseSensorManifestPeer.php';

		if ($this->aSensorManifest === null && ($this->sensor_manifest_id > 0)) {

			$this->aSensorManifest = SensorManifestPeer::retrieveByPK($this->sensor_manifest_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = SensorManifestPeer::retrieveByPK($this->sensor_manifest_id, $con);
			   $obj->addSensorManifests($this);
			 */
		}
		return $this->aSensorManifest;
	}

	/**
	 * Temporary storage of collEquipments to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipments()
	{
		if ($this->collEquipments === null) {
			$this->collEquipments = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization has previously
	 * been saved, it will retrieve related Equipments from storage.
	 * If this Organization is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipments($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipments === null) {
			if ($this->isNew()) {
			   $this->collEquipments = array();
			} else {

				$criteria->add(EquipmentPeer::ORGID, $this->getId());

				EquipmentPeer::addSelectColumns($criteria);
				$this->collEquipments = EquipmentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentPeer::ORGID, $this->getId());

				EquipmentPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentCriteria) || !$this->lastEquipmentCriteria->equals($criteria)) {
					$this->collEquipments = EquipmentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentCriteria = $criteria;
		return $this->collEquipments;
	}

	/**
	 * Returns the number of related Equipments.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipments($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentPeer::ORGID, $this->getId());

		return EquipmentPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Equipment object to this object
	 * through the Equipment foreign key attribute
	 *
	 * @param      Equipment $l Equipment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipment(Equipment $l)
	{
		$this->collEquipments[] = $l;
		$l->setOrganization($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related Equipments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getEquipmentsJoinEquipmentRelatedByParentId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipments === null) {
			if ($this->isNew()) {
				$this->collEquipments = array();
			} else {

				$criteria->add(EquipmentPeer::ORGID, $this->getId());

				$this->collEquipments = EquipmentPeer::doSelectJoinEquipmentRelatedByParentId($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentPeer::ORGID, $this->getId());

			if (!isset($this->lastEquipmentCriteria) || !$this->lastEquipmentCriteria->equals($criteria)) {
				$this->collEquipments = EquipmentPeer::doSelectJoinEquipmentRelatedByParentId($criteria, $con);
			}
		}
		$this->lastEquipmentCriteria = $criteria;

		return $this->collEquipments;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related Equipments from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getEquipmentsJoinEquipmentModel($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipments === null) {
			if ($this->isNew()) {
				$this->collEquipments = array();
			} else {

				$criteria->add(EquipmentPeer::ORGID, $this->getId());

				$this->collEquipments = EquipmentPeer::doSelectJoinEquipmentModel($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentPeer::ORGID, $this->getId());

			if (!isset($this->lastEquipmentCriteria) || !$this->lastEquipmentCriteria->equals($criteria)) {
				$this->collEquipments = EquipmentPeer::doSelectJoinEquipmentModel($criteria, $con);
			}
		}
		$this->lastEquipmentCriteria = $criteria;

		return $this->collEquipments;
	}

	/**
	 * Temporary storage of collExperimentFacilitys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initExperimentFacilitys()
	{
		if ($this->collExperimentFacilitys === null) {
			$this->collExperimentFacilitys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization has previously
	 * been saved, it will retrieve related ExperimentFacilitys from storage.
	 * If this Organization is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getExperimentFacilitys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentFacilitys === null) {
			if ($this->isNew()) {
			   $this->collExperimentFacilitys = array();
			} else {

				$criteria->add(ExperimentFacilityPeer::FACILITYID, $this->getId());

				ExperimentFacilityPeer::addSelectColumns($criteria);
				$this->collExperimentFacilitys = ExperimentFacilityPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentFacilityPeer::FACILITYID, $this->getId());

				ExperimentFacilityPeer::addSelectColumns($criteria);
				if (!isset($this->lastExperimentFacilityCriteria) || !$this->lastExperimentFacilityCriteria->equals($criteria)) {
					$this->collExperimentFacilitys = ExperimentFacilityPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastExperimentFacilityCriteria = $criteria;
		return $this->collExperimentFacilitys;
	}

	/**
	 * Returns the number of related ExperimentFacilitys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countExperimentFacilitys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ExperimentFacilityPeer::FACILITYID, $this->getId());

		return ExperimentFacilityPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ExperimentFacility object to this object
	 * through the ExperimentFacility foreign key attribute
	 *
	 * @param      ExperimentFacility $l ExperimentFacility
	 * @return     void
	 * @throws     PropelException
	 */
	public function addExperimentFacility(ExperimentFacility $l)
	{
		$this->collExperimentFacilitys[] = $l;
		$l->setOrganization($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related ExperimentFacilitys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getExperimentFacilitysJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentFacilitys === null) {
			if ($this->isNew()) {
				$this->collExperimentFacilitys = array();
			} else {

				$criteria->add(ExperimentFacilityPeer::FACILITYID, $this->getId());

				$this->collExperimentFacilitys = ExperimentFacilityPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentFacilityPeer::FACILITYID, $this->getId());

			if (!isset($this->lastExperimentFacilityCriteria) || !$this->lastExperimentFacilityCriteria->equals($criteria)) {
				$this->collExperimentFacilitys = ExperimentFacilityPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastExperimentFacilityCriteria = $criteria;

		return $this->collExperimentFacilitys;
	}

	/**
	 * Temporary storage of collExperimentOrganizations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initExperimentOrganizations()
	{
		if ($this->collExperimentOrganizations === null) {
			$this->collExperimentOrganizations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization has previously
	 * been saved, it will retrieve related ExperimentOrganizations from storage.
	 * If this Organization is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getExperimentOrganizations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentOrganizations === null) {
			if ($this->isNew()) {
			   $this->collExperimentOrganizations = array();
			} else {

				$criteria->add(ExperimentOrganizationPeer::ORGID, $this->getId());

				ExperimentOrganizationPeer::addSelectColumns($criteria);
				$this->collExperimentOrganizations = ExperimentOrganizationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ExperimentOrganizationPeer::ORGID, $this->getId());

				ExperimentOrganizationPeer::addSelectColumns($criteria);
				if (!isset($this->lastExperimentOrganizationCriteria) || !$this->lastExperimentOrganizationCriteria->equals($criteria)) {
					$this->collExperimentOrganizations = ExperimentOrganizationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastExperimentOrganizationCriteria = $criteria;
		return $this->collExperimentOrganizations;
	}

	/**
	 * Returns the number of related ExperimentOrganizations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countExperimentOrganizations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ExperimentOrganizationPeer::ORGID, $this->getId());

		return ExperimentOrganizationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ExperimentOrganization object to this object
	 * through the ExperimentOrganization foreign key attribute
	 *
	 * @param      ExperimentOrganization $l ExperimentOrganization
	 * @return     void
	 * @throws     PropelException
	 */
	public function addExperimentOrganization(ExperimentOrganization $l)
	{
		$this->collExperimentOrganizations[] = $l;
		$l->setOrganization($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related ExperimentOrganizations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getExperimentOrganizationsJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseExperimentOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collExperimentOrganizations === null) {
			if ($this->isNew()) {
				$this->collExperimentOrganizations = array();
			} else {

				$criteria->add(ExperimentOrganizationPeer::ORGID, $this->getId());

				$this->collExperimentOrganizations = ExperimentOrganizationPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ExperimentOrganizationPeer::ORGID, $this->getId());

			if (!isset($this->lastExperimentOrganizationCriteria) || !$this->lastExperimentOrganizationCriteria->equals($criteria)) {
				$this->collExperimentOrganizations = ExperimentOrganizationPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastExperimentOrganizationCriteria = $criteria;

		return $this->collExperimentOrganizations;
	}

	/**
	 * Temporary storage of collFacilityDataFiles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initFacilityDataFiles()
	{
		if ($this->collFacilityDataFiles === null) {
			$this->collFacilityDataFiles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 * If this Organization is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getFacilityDataFiles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseFacilityDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collFacilityDataFiles === null) {
			if ($this->isNew()) {
			   $this->collFacilityDataFiles = array();
			} else {

				$criteria->add(FacilityDataFilePeer::FACILITY_ID, $this->getId());

				FacilityDataFilePeer::addSelectColumns($criteria);
				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(FacilityDataFilePeer::FACILITY_ID, $this->getId());

				FacilityDataFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastFacilityDataFileCriteria) || !$this->lastFacilityDataFileCriteria->equals($criteria)) {
					$this->collFacilityDataFiles = FacilityDataFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastFacilityDataFileCriteria = $criteria;
		return $this->collFacilityDataFiles;
	}

	/**
	 * Returns the number of related FacilityDataFiles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countFacilityDataFiles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseFacilityDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(FacilityDataFilePeer::FACILITY_ID, $this->getId());

		return FacilityDataFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a FacilityDataFile object to this object
	 * through the FacilityDataFile foreign key attribute
	 *
	 * @param      FacilityDataFile $l FacilityDataFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addFacilityDataFile(FacilityDataFile $l)
	{
		$this->collFacilityDataFiles[] = $l;
		$l->setOrganization($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getFacilityDataFilesJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseFacilityDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collFacilityDataFiles === null) {
			if ($this->isNew()) {
				$this->collFacilityDataFiles = array();
			} else {

				$criteria->add(FacilityDataFilePeer::FACILITY_ID, $this->getId());

				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(FacilityDataFilePeer::FACILITY_ID, $this->getId());

			if (!isset($this->lastFacilityDataFileCriteria) || !$this->lastFacilityDataFileCriteria->equals($criteria)) {
				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastFacilityDataFileCriteria = $criteria;

		return $this->collFacilityDataFiles;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getFacilityDataFilesJoinDocumentFormat($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseFacilityDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collFacilityDataFiles === null) {
			if ($this->isNew()) {
				$this->collFacilityDataFiles = array();
			} else {

				$criteria->add(FacilityDataFilePeer::FACILITY_ID, $this->getId());

				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(FacilityDataFilePeer::FACILITY_ID, $this->getId());

			if (!isset($this->lastFacilityDataFileCriteria) || !$this->lastFacilityDataFileCriteria->equals($criteria)) {
				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		}
		$this->lastFacilityDataFileCriteria = $criteria;

		return $this->collFacilityDataFiles;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getFacilityDataFilesJoinDocumentType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseFacilityDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collFacilityDataFiles === null) {
			if ($this->isNew()) {
				$this->collFacilityDataFiles = array();
			} else {

				$criteria->add(FacilityDataFilePeer::FACILITY_ID, $this->getId());

				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDocumentType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(FacilityDataFilePeer::FACILITY_ID, $this->getId());

			if (!isset($this->lastFacilityDataFileCriteria) || !$this->lastFacilityDataFileCriteria->equals($criteria)) {
				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDocumentType($criteria, $con);
			}
		}
		$this->lastFacilityDataFileCriteria = $criteria;

		return $this->collFacilityDataFiles;
	}

	/**
	 * Temporary storage of collNAWIFacilitys to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initNAWIFacilitys()
	{
		if ($this->collNAWIFacilitys === null) {
			$this->collNAWIFacilitys = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization has previously
	 * been saved, it will retrieve related NAWIFacilitys from storage.
	 * If this Organization is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getNAWIFacilitys($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseNAWIFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collNAWIFacilitys === null) {
			if ($this->isNew()) {
			   $this->collNAWIFacilitys = array();
			} else {

				$criteria->add(NAWIFacilityPeer::FACILITYID, $this->getId());

				NAWIFacilityPeer::addSelectColumns($criteria);
				$this->collNAWIFacilitys = NAWIFacilityPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(NAWIFacilityPeer::FACILITYID, $this->getId());

				NAWIFacilityPeer::addSelectColumns($criteria);
				if (!isset($this->lastNAWIFacilityCriteria) || !$this->lastNAWIFacilityCriteria->equals($criteria)) {
					$this->collNAWIFacilitys = NAWIFacilityPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastNAWIFacilityCriteria = $criteria;
		return $this->collNAWIFacilitys;
	}

	/**
	 * Returns the number of related NAWIFacilitys.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countNAWIFacilitys($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseNAWIFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(NAWIFacilityPeer::FACILITYID, $this->getId());

		return NAWIFacilityPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a NAWIFacility object to this object
	 * through the NAWIFacility foreign key attribute
	 *
	 * @param      NAWIFacility $l NAWIFacility
	 * @return     void
	 * @throws     PropelException
	 */
	public function addNAWIFacility(NAWIFacility $l)
	{
		$this->collNAWIFacilitys[] = $l;
		$l->setOrganization($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related NAWIFacilitys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getNAWIFacilitysJoinNAWI($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseNAWIFacilityPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collNAWIFacilitys === null) {
			if ($this->isNew()) {
				$this->collNAWIFacilitys = array();
			} else {

				$criteria->add(NAWIFacilityPeer::FACILITYID, $this->getId());

				$this->collNAWIFacilitys = NAWIFacilityPeer::doSelectJoinNAWI($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(NAWIFacilityPeer::FACILITYID, $this->getId());

			if (!isset($this->lastNAWIFacilityCriteria) || !$this->lastNAWIFacilityCriteria->equals($criteria)) {
				$this->collNAWIFacilitys = NAWIFacilityPeer::doSelectJoinNAWI($criteria, $con);
			}
		}
		$this->lastNAWIFacilityCriteria = $criteria;

		return $this->collNAWIFacilitys;
	}

	/**
	 * Temporary storage of collOrganizationsRelatedByFacilityId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initOrganizationsRelatedByFacilityId()
	{
		if ($this->collOrganizationsRelatedByFacilityId === null) {
			$this->collOrganizationsRelatedByFacilityId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization has previously
	 * been saved, it will retrieve related OrganizationsRelatedByFacilityId from storage.
	 * If this Organization is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getOrganizationsRelatedByFacilityId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collOrganizationsRelatedByFacilityId === null) {
			if ($this->isNew()) {
			   $this->collOrganizationsRelatedByFacilityId = array();
			} else {

				$criteria->add(OrganizationPeer::FACILITYID, $this->getId());

				OrganizationPeer::addSelectColumns($criteria);
				$this->collOrganizationsRelatedByFacilityId = OrganizationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(OrganizationPeer::FACILITYID, $this->getId());

				OrganizationPeer::addSelectColumns($criteria);
				if (!isset($this->lastOrganizationRelatedByFacilityIdCriteria) || !$this->lastOrganizationRelatedByFacilityIdCriteria->equals($criteria)) {
					$this->collOrganizationsRelatedByFacilityId = OrganizationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastOrganizationRelatedByFacilityIdCriteria = $criteria;
		return $this->collOrganizationsRelatedByFacilityId;
	}

	/**
	 * Returns the number of related OrganizationsRelatedByFacilityId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countOrganizationsRelatedByFacilityId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(OrganizationPeer::FACILITYID, $this->getId());

		return OrganizationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Organization object to this object
	 * through the Organization foreign key attribute
	 *
	 * @param      Organization $l Organization
	 * @return     void
	 * @throws     PropelException
	 */
	public function addOrganizationRelatedByFacilityId(Organization $l)
	{
		$this->collOrganizationsRelatedByFacilityId[] = $l;
		$l->setOrganizationRelatedByFacilityId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related OrganizationsRelatedByFacilityId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getOrganizationsRelatedByFacilityIdJoinSensorManifest($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collOrganizationsRelatedByFacilityId === null) {
			if ($this->isNew()) {
				$this->collOrganizationsRelatedByFacilityId = array();
			} else {

				$criteria->add(OrganizationPeer::FACILITYID, $this->getId());

				$this->collOrganizationsRelatedByFacilityId = OrganizationPeer::doSelectJoinSensorManifest($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(OrganizationPeer::FACILITYID, $this->getId());

			if (!isset($this->lastOrganizationRelatedByFacilityIdCriteria) || !$this->lastOrganizationRelatedByFacilityIdCriteria->equals($criteria)) {
				$this->collOrganizationsRelatedByFacilityId = OrganizationPeer::doSelectJoinSensorManifest($criteria, $con);
			}
		}
		$this->lastOrganizationRelatedByFacilityIdCriteria = $criteria;

		return $this->collOrganizationsRelatedByFacilityId;
	}

	/**
	 * Temporary storage of collOrganizationsRelatedByParentOrgId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initOrganizationsRelatedByParentOrgId()
	{
		if ($this->collOrganizationsRelatedByParentOrgId === null) {
			$this->collOrganizationsRelatedByParentOrgId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization has previously
	 * been saved, it will retrieve related OrganizationsRelatedByParentOrgId from storage.
	 * If this Organization is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getOrganizationsRelatedByParentOrgId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collOrganizationsRelatedByParentOrgId === null) {
			if ($this->isNew()) {
			   $this->collOrganizationsRelatedByParentOrgId = array();
			} else {

				$criteria->add(OrganizationPeer::PARENT_ORG_ID, $this->getId());

				OrganizationPeer::addSelectColumns($criteria);
				$this->collOrganizationsRelatedByParentOrgId = OrganizationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(OrganizationPeer::PARENT_ORG_ID, $this->getId());

				OrganizationPeer::addSelectColumns($criteria);
				if (!isset($this->lastOrganizationRelatedByParentOrgIdCriteria) || !$this->lastOrganizationRelatedByParentOrgIdCriteria->equals($criteria)) {
					$this->collOrganizationsRelatedByParentOrgId = OrganizationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastOrganizationRelatedByParentOrgIdCriteria = $criteria;
		return $this->collOrganizationsRelatedByParentOrgId;
	}

	/**
	 * Returns the number of related OrganizationsRelatedByParentOrgId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countOrganizationsRelatedByParentOrgId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(OrganizationPeer::PARENT_ORG_ID, $this->getId());

		return OrganizationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Organization object to this object
	 * through the Organization foreign key attribute
	 *
	 * @param      Organization $l Organization
	 * @return     void
	 * @throws     PropelException
	 */
	public function addOrganizationRelatedByParentOrgId(Organization $l)
	{
		$this->collOrganizationsRelatedByParentOrgId[] = $l;
		$l->setOrganizationRelatedByParentOrgId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related OrganizationsRelatedByParentOrgId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getOrganizationsRelatedByParentOrgIdJoinSensorManifest($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collOrganizationsRelatedByParentOrgId === null) {
			if ($this->isNew()) {
				$this->collOrganizationsRelatedByParentOrgId = array();
			} else {

				$criteria->add(OrganizationPeer::PARENT_ORG_ID, $this->getId());

				$this->collOrganizationsRelatedByParentOrgId = OrganizationPeer::doSelectJoinSensorManifest($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(OrganizationPeer::PARENT_ORG_ID, $this->getId());

			if (!isset($this->lastOrganizationRelatedByParentOrgIdCriteria) || !$this->lastOrganizationRelatedByParentOrgIdCriteria->equals($criteria)) {
				$this->collOrganizationsRelatedByParentOrgId = OrganizationPeer::doSelectJoinSensorManifest($criteria, $con);
			}
		}
		$this->lastOrganizationRelatedByParentOrgIdCriteria = $criteria;

		return $this->collOrganizationsRelatedByParentOrgId;
	}

	/**
	 * Temporary storage of collProjectOrganizations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initProjectOrganizations()
	{
		if ($this->collProjectOrganizations === null) {
			$this->collProjectOrganizations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization has previously
	 * been saved, it will retrieve related ProjectOrganizations from storage.
	 * If this Organization is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getProjectOrganizations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjectOrganizations === null) {
			if ($this->isNew()) {
			   $this->collProjectOrganizations = array();
			} else {

				$criteria->add(ProjectOrganizationPeer::ORGID, $this->getId());

				ProjectOrganizationPeer::addSelectColumns($criteria);
				$this->collProjectOrganizations = ProjectOrganizationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ProjectOrganizationPeer::ORGID, $this->getId());

				ProjectOrganizationPeer::addSelectColumns($criteria);
				if (!isset($this->lastProjectOrganizationCriteria) || !$this->lastProjectOrganizationCriteria->equals($criteria)) {
					$this->collProjectOrganizations = ProjectOrganizationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastProjectOrganizationCriteria = $criteria;
		return $this->collProjectOrganizations;
	}

	/**
	 * Returns the number of related ProjectOrganizations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countProjectOrganizations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ProjectOrganizationPeer::ORGID, $this->getId());

		return ProjectOrganizationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ProjectOrganization object to this object
	 * through the ProjectOrganization foreign key attribute
	 *
	 * @param      ProjectOrganization $l ProjectOrganization
	 * @return     void
	 * @throws     PropelException
	 */
	public function addProjectOrganization(ProjectOrganization $l)
	{
		$this->collProjectOrganizations[] = $l;
		$l->setOrganization($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related ProjectOrganizations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getProjectOrganizationsJoinProject($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectOrganizationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjectOrganizations === null) {
			if ($this->isNew()) {
				$this->collProjectOrganizations = array();
			} else {

				$criteria->add(ProjectOrganizationPeer::ORGID, $this->getId());

				$this->collProjectOrganizations = ProjectOrganizationPeer::doSelectJoinProject($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ProjectOrganizationPeer::ORGID, $this->getId());

			if (!isset($this->lastProjectOrganizationCriteria) || !$this->lastProjectOrganizationCriteria->equals($criteria)) {
				$this->collProjectOrganizations = ProjectOrganizationPeer::doSelectJoinProject($criteria, $con);
			}
		}
		$this->lastProjectOrganizationCriteria = $criteria;

		return $this->collProjectOrganizations;
	}

	/**
	 * Temporary storage of collCoordinators to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinators()
	{
		if ($this->collCoordinators === null) {
			$this->collCoordinators = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization has previously
	 * been saved, it will retrieve related Coordinators from storage.
	 * If this Organization is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinators($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinators === null) {
			if ($this->isNew()) {
			   $this->collCoordinators = array();
			} else {

				$criteria->add(CoordinatorPeer::FACILITY_ID, $this->getId());

				CoordinatorPeer::addSelectColumns($criteria);
				$this->collCoordinators = CoordinatorPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinatorPeer::FACILITY_ID, $this->getId());

				CoordinatorPeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinatorCriteria) || !$this->lastCoordinatorCriteria->equals($criteria)) {
					$this->collCoordinators = CoordinatorPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinatorCriteria = $criteria;
		return $this->collCoordinators;
	}

	/**
	 * Returns the number of related Coordinators.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinators($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(CoordinatorPeer::FACILITY_ID, $this->getId());

		return CoordinatorPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Coordinator object to this object
	 * through the Coordinator foreign key attribute
	 *
	 * @param      Coordinator $l Coordinator
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCoordinator(Coordinator $l)
	{
		$this->collCoordinators[] = $l;
		$l->setOrganization($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Organization is new, it will return
	 * an empty collection; or if this Organization has previously
	 * been saved, it will retrieve related Coordinators from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Organization.
	 */
	public function getCoordinatorsJoinProject($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinatorPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinators === null) {
			if ($this->isNew()) {
				$this->collCoordinators = array();
			} else {

				$criteria->add(CoordinatorPeer::FACILITY_ID, $this->getId());

				$this->collCoordinators = CoordinatorPeer::doSelectJoinProject($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinatorPeer::FACILITY_ID, $this->getId());

			if (!isset($this->lastCoordinatorCriteria) || !$this->lastCoordinatorCriteria->equals($criteria)) {
				$this->collCoordinators = CoordinatorPeer::doSelectJoinProject($criteria, $con);
			}
		}
		$this->lastCoordinatorCriteria = $criteria;

		return $this->collCoordinators;
	}

} // BaseOrganization
