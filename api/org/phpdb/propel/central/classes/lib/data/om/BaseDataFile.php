<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/DataFilePeer.php';

/**
 * Base class that represents a row from the 'DATA_FILE' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseDataFile extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        DataFilePeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the author_emails field.
	 * @var        string
	 */
	protected $author_emails;


	/**
	 * The value for the authors field.
	 * @var        string
	 */
	protected $authors;


	/**
	 * The value for the checksum field.
	 * @var        string
	 */
	protected $checksum;


	/**
	 * The value for the created field.
	 * @var        int
	 */
	protected $created;


	/**
	 * The value for the curation_status field.
	 * @var        string
	 */
	protected $curation_status = 'Uncurated';


	/**
	 * The value for the deleted field.
	 * @var        double
	 */
	protected $deleted = 0;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the directory field.
	 * @var        double
	 */
	protected $directory = 0;


	/**
	 * The value for the filesize field.
	 * @var        double
	 */
	protected $filesize;


	/**
	 * The value for the how_to_cite field.
	 * @var        string
	 */
	protected $how_to_cite;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the page_count field.
	 * @var        double
	 */
	protected $page_count;


	/**
	 * The value for the path field.
	 * @var        string
	 */
	protected $path;


	/**
	 * The value for the title field.
	 * @var        string
	 */
	protected $title;


	/**
	 * The value for the viewable field.
	 * @var        string
	 */
	protected $viewable = 'MEMBERS';


	/**
	 * The value for the thumb_id field.
	 * @var        double
	 */
	protected $thumb_id;


	/**
	 * The value for the document_format_id field.
	 * @var        double
	 */
	protected $document_format_id;


	/**
	 * The value for the opening_tool field.
	 * @var        string
	 */
	protected $opening_tool;


	/**
	 * The value for the usage_type_id field.
	 * @var        double
	 */
	protected $usage_type_id;

	/**
	 * @var        DataFile
	 */
	protected $aDataFileRelatedByThumbId;

	/**
	 * @var        DocumentFormat
	 */
	protected $aDocumentFormat;

	/**
	 * @var        EntityType
	 */
	protected $aEntityType;

	/**
	 * Collection to store aggregation of collControllerChannels.
	 * @var        array
	 */
	protected $collControllerChannels;

	/**
	 * The criteria used to select the current contents of collControllerChannels.
	 * @var        Criteria
	 */
	protected $lastControllerChannelCriteria = null;

	/**
	 * Collection to store aggregation of collControllerConfigsRelatedByInputDataFileId.
	 * @var        array
	 */
	protected $collControllerConfigsRelatedByInputDataFileId;

	/**
	 * The criteria used to select the current contents of collControllerConfigsRelatedByInputDataFileId.
	 * @var        Criteria
	 */
	protected $lastControllerConfigRelatedByInputDataFileIdCriteria = null;

	/**
	 * Collection to store aggregation of collControllerConfigsRelatedByConfigDataFileId.
	 * @var        array
	 */
	protected $collControllerConfigsRelatedByConfigDataFileId;

	/**
	 * The criteria used to select the current contents of collControllerConfigsRelatedByConfigDataFileId.
	 * @var        Criteria
	 */
	protected $lastControllerConfigRelatedByConfigDataFileIdCriteria = null;

	/**
	 * Collection to store aggregation of collCoordinateSpaceDataFiles.
	 * @var        array
	 */
	protected $collCoordinateSpaceDataFiles;

	/**
	 * The criteria used to select the current contents of collCoordinateSpaceDataFiles.
	 * @var        Criteria
	 */
	protected $lastCoordinateSpaceDataFileCriteria = null;

	/**
	 * Collection to store aggregation of collDAQChannels.
	 * @var        array
	 */
	protected $collDAQChannels;

	/**
	 * The criteria used to select the current contents of collDAQChannels.
	 * @var        Criteria
	 */
	protected $lastDAQChannelCriteria = null;

	/**
	 * Collection to store aggregation of collDAQConfigsRelatedByOutputDataFileId.
	 * @var        array
	 */
	protected $collDAQConfigsRelatedByOutputDataFileId;

	/**
	 * The criteria used to select the current contents of collDAQConfigsRelatedByOutputDataFileId.
	 * @var        Criteria
	 */
	protected $lastDAQConfigRelatedByOutputDataFileIdCriteria = null;

	/**
	 * Collection to store aggregation of collDAQConfigsRelatedByConfigDataFileId.
	 * @var        array
	 */
	protected $collDAQConfigsRelatedByConfigDataFileId;

	/**
	 * The criteria used to select the current contents of collDAQConfigsRelatedByConfigDataFileId.
	 * @var        Criteria
	 */
	protected $lastDAQConfigRelatedByConfigDataFileIdCriteria = null;

	/**
	 * Collection to store aggregation of collDataFilesRelatedByThumbId.
	 * @var        array
	 */
	protected $collDataFilesRelatedByThumbId;

	/**
	 * The criteria used to select the current contents of collDataFilesRelatedByThumbId.
	 * @var        Criteria
	 */
	protected $lastDataFileRelatedByThumbIdCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentDocumentations.
	 * @var        array
	 */
	protected $collEquipmentDocumentations;

	/**
	 * The criteria used to select the current contents of collEquipmentDocumentations.
	 * @var        Criteria
	 */
	protected $lastEquipmentDocumentationCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentModelsRelatedByAdditionalSpecFileId.
	 * @var        array
	 */
	protected $collEquipmentModelsRelatedByAdditionalSpecFileId;

	/**
	 * The criteria used to select the current contents of collEquipmentModelsRelatedByAdditionalSpecFileId.
	 * @var        Criteria
	 */
	protected $lastEquipmentModelRelatedByAdditionalSpecFileIdCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentModelsRelatedByInterfaceDocFileId.
	 * @var        array
	 */
	protected $collEquipmentModelsRelatedByInterfaceDocFileId;

	/**
	 * The criteria used to select the current contents of collEquipmentModelsRelatedByInterfaceDocFileId.
	 * @var        Criteria
	 */
	protected $lastEquipmentModelRelatedByInterfaceDocFileIdCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentModelsRelatedByManufacturerDocFileId.
	 * @var        array
	 */
	protected $collEquipmentModelsRelatedByManufacturerDocFileId;

	/**
	 * The criteria used to select the current contents of collEquipmentModelsRelatedByManufacturerDocFileId.
	 * @var        Criteria
	 */
	protected $lastEquipmentModelRelatedByManufacturerDocFileIdCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentModelsRelatedBySubcomponentsDocFileId.
	 * @var        array
	 */
	protected $collEquipmentModelsRelatedBySubcomponentsDocFileId;

	/**
	 * The criteria used to select the current contents of collEquipmentModelsRelatedBySubcomponentsDocFileId.
	 * @var        Criteria
	 */
	protected $lastEquipmentModelRelatedBySubcomponentsDocFileIdCriteria = null;

	/**
	 * Collection to store aggregation of collEquipmentModelsRelatedByDesignConsiderationFileId.
	 * @var        array
	 */
	protected $collEquipmentModelsRelatedByDesignConsiderationFileId;

	/**
	 * The criteria used to select the current contents of collEquipmentModelsRelatedByDesignConsiderationFileId.
	 * @var        Criteria
	 */
	protected $lastEquipmentModelRelatedByDesignConsiderationFileIdCriteria = null;

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
	 * Collection to store aggregation of collMaterialFiles.
	 * @var        array
	 */
	protected $collMaterialFiles;

	/**
	 * The criteria used to select the current contents of collMaterialFiles.
	 * @var        Criteria
	 */
	protected $lastMaterialFileCriteria = null;

	/**
	 * Collection to store aggregation of collSensorModelDataFiles.
	 * @var        array
	 */
	protected $collSensorModelDataFiles;

	/**
	 * The criteria used to select the current contents of collSensorModelDataFiles.
	 * @var        Criteria
	 */
	protected $lastSensorModelDataFileCriteria = null;

	/**
	 * Collection to store aggregation of collThumbnails.
	 * @var        array
	 */
	protected $collThumbnails;

	/**
	 * The criteria used to select the current contents of collThumbnails.
	 * @var        Criteria
	 */
	protected $lastThumbnailCriteria = null;

	/**
	 * Collection to store aggregation of collTrials.
	 * @var        array
	 */
	protected $collTrials;

	/**
	 * The criteria used to select the current contents of collTrials.
	 * @var        Criteria
	 */
	protected $lastTrialCriteria = null;

	/**
	 * Collection to store aggregation of collProjectHomepages.
	 * @var        array
	 */
	protected $collProjectHomepages;

	/**
	 * The criteria used to select the current contents of collProjectHomepages.
	 * @var        Criteria
	 */
	protected $lastProjectHomepageCriteria = null;

	/**
	 * Collection to store aggregation of collSpecimenComponentMaterialFiles.
	 * @var        array
	 */
	protected $collSpecimenComponentMaterialFiles;

	/**
	 * The criteria used to select the current contents of collSpecimenComponentMaterialFiles.
	 * @var        Criteria
	 */
	protected $lastSpecimenComponentMaterialFileCriteria = null;

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
	 * Get the [id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->id;
	}

	/**
	 * Get the [author_emails] column value.
	 * 
	 * @return     string
	 */
	public function getAuthorEmails()
	{

		return $this->author_emails;
	}

	/**
	 * Get the [authors] column value.
	 * 
	 * @return     string
	 */
	public function getAuthors()
	{

		return $this->authors;
	}

	/**
	 * Get the [checksum] column value.
	 * 
	 * @return     string
	 */
	public function getChecksum()
	{

		return $this->checksum;
	}

	/**
	 * Get the [optionally formatted] [created] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getCreated($format = 'Y-m-d H:i:s')
	{

		if ($this->created === null || $this->created === '') {
			return null;
		} elseif (!is_int($this->created)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->created);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [created] as date/time value: " . var_export($this->created, true));
			}
		} else {
			$ts = $this->created;
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
	}

	/**
	 * Get the [curation_status] column value.
	 * 
	 * @return     string
	 */
	public function getCurationStatus()
	{

		return $this->curation_status;
	}

	/**
	 * Get the [deleted] column value.
	 * 
	 * @return     double
	 */
	public function getDeleted()
	{

		return $this->deleted;
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
	 * Get the [directory] column value.
	 * 
	 * @return     double
	 */
	public function getDirectory()
	{

		return $this->directory;
	}

	/**
	 * Get the [filesize] column value.
	 * 
	 * @return     double
	 */
	public function getFilesize()
	{

		return $this->filesize;
	}

	/**
	 * Get the [how_to_cite] column value.
	 * 
	 * @return     string
	 */
	public function getHowToCite()
	{

		return $this->how_to_cite;
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
	 * Get the [page_count] column value.
	 * 
	 * @return     double
	 */
	public function getPageCount()
	{

		return $this->page_count;
	}

	/**
	 * Get the [path] column value.
	 * 
	 * @return     string
	 */
	public function getPath()
	{

		return $this->path;
	}

	/**
	 * Get the [title] column value.
	 * 
	 * @return     string
	 */
	public function getTitle()
	{

		return $this->title;
	}

	/**
	 * Get the [viewable] column value.
	 * 
	 * @return     string
	 */
	public function getView()
	{

		return $this->viewable;
	}

	/**
	 * Get the [thumb_id] column value.
	 * 
	 * @return     double
	 */
	public function getThumbId()
	{

		return $this->thumb_id;
	}

	/**
	 * Get the [document_format_id] column value.
	 * 
	 * @return     double
	 */
	public function getDocumentFormatId()
	{

		return $this->document_format_id;
	}

	/**
	 * Get the [opening_tool] column value.
	 * 
	 * @return     string
	 */
	public function getOpeningTool()
	{

		return $this->opening_tool;
	}

	/**
	 * Get the [usage_type_id] column value.
	 * 
	 * @return     double
	 */
	public function getUsageTypeId()
	{

		return $this->usage_type_id;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = DataFilePeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [author_emails] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setAuthorEmails($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->author_emails !== $v) {
			$this->author_emails = $v;
			$this->modifiedColumns[] = DataFilePeer::AUTHOR_EMAILS;
		}

	} // setAuthorEmails()

	/**
	 * Set the value of [authors] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setAuthors($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->authors !== $v) {
			$this->authors = $v;
			$this->modifiedColumns[] = DataFilePeer::AUTHORS;
		}

	} // setAuthors()

	/**
	 * Set the value of [checksum] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setChecksum($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->checksum !== $v) {
			$this->checksum = $v;
			$this->modifiedColumns[] = DataFilePeer::CHECKSUM;
		}

	} // setChecksum()

	/**
	 * Set the value of [created] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setCreated($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [created] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->created !== $ts) {
			$this->created = $ts;
			$this->modifiedColumns[] = DataFilePeer::CREATED;
		}

	} // setCreated()

	/**
	 * Set the value of [curation_status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCurationStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->curation_status !== $v || $v === 'Uncurated') {
			$this->curation_status = $v;
			$this->modifiedColumns[] = DataFilePeer::CURATION_STATUS;
		}

	} // setCurationStatus()

	/**
	 * Set the value of [deleted] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDeleted($v)
	{

		if ($this->deleted !== $v || $v === 0) {
			$this->deleted = $v;
			$this->modifiedColumns[] = DataFilePeer::DELETED;
		}

	} // setDeleted()

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
			$this->modifiedColumns[] = DataFilePeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [directory] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDirectory($v)
	{

		if ($this->directory !== $v || $v === 0) {
			$this->directory = $v;
			$this->modifiedColumns[] = DataFilePeer::DIRECTORY;
		}

	} // setDirectory()

	/**
	 * Set the value of [filesize] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFilesize($v)
	{

		if ($this->filesize !== $v) {
			$this->filesize = $v;
			$this->modifiedColumns[] = DataFilePeer::FILESIZE;
		}

	} // setFilesize()

	/**
	 * Set the value of [how_to_cite] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setHowToCite($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->how_to_cite !== $v) {
			$this->how_to_cite = $v;
			$this->modifiedColumns[] = DataFilePeer::HOW_TO_CITE;
		}

	} // setHowToCite()

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
			$this->modifiedColumns[] = DataFilePeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [page_count] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPageCount($v)
	{

		if ($this->page_count !== $v) {
			$this->page_count = $v;
			$this->modifiedColumns[] = DataFilePeer::PAGE_COUNT;
		}

	} // setPageCount()

	/**
	 * Set the value of [path] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPath($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->path !== $v) {
			$this->path = $v;
			$this->modifiedColumns[] = DataFilePeer::PATH;
		}

	} // setPath()

	/**
	 * Set the value of [title] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTitle($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->title !== $v) {
			$this->title = $v;
			$this->modifiedColumns[] = DataFilePeer::TITLE;
		}

	} // setTitle()

	/**
	 * Set the value of [viewable] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setView($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->viewable !== $v || $v === 'MEMBERS') {
			$this->viewable = $v;
			$this->modifiedColumns[] = DataFilePeer::VIEWABLE;
		}

	} // setView()

	/**
	 * Set the value of [thumb_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setThumbId($v)
	{

		if ($this->thumb_id !== $v) {
			$this->thumb_id = $v;
			$this->modifiedColumns[] = DataFilePeer::THUMB_ID;
		}

		if ($this->aDataFileRelatedByThumbId !== null && $this->aDataFileRelatedByThumbId->getId() !== $v) {
			$this->aDataFileRelatedByThumbId = null;
		}

	} // setThumbId()

	/**
	 * Set the value of [document_format_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDocumentFormatId($v)
	{

		if ($this->document_format_id !== $v) {
			$this->document_format_id = $v;
			$this->modifiedColumns[] = DataFilePeer::DOCUMENT_FORMAT_ID;
		}

		if ($this->aDocumentFormat !== null && $this->aDocumentFormat->getId() !== $v) {
			$this->aDocumentFormat = null;
		}

	} // setDocumentFormatId()

	/**
	 * Set the value of [opening_tool] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setOpeningTool($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->opening_tool !== $v) {
			$this->opening_tool = $v;
			$this->modifiedColumns[] = DataFilePeer::OPENING_TOOL;
		}

	} // setOpeningTool()

	/**
	 * Set the value of [usage_type_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setUsageTypeId($v)
	{

		if ($this->usage_type_id !== $v) {
			$this->usage_type_id = $v;
			$this->modifiedColumns[] = DataFilePeer::USAGE_TYPE_ID;
		}

		if ($this->aEntityType !== null && $this->aEntityType->getId() !== $v) {
			$this->aEntityType = null;
		}

	} // setUsageTypeId()

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

			$this->id = $rs->getFloat($startcol + 0);

			$this->author_emails = $rs->getString($startcol + 1);

			$this->authors = $rs->getString($startcol + 2);

			$this->checksum = $rs->getString($startcol + 3);

			$this->created = $rs->getTimestamp($startcol + 4, null);

			$this->curation_status = $rs->getString($startcol + 5);

			$this->deleted = $rs->getFloat($startcol + 6);

			$this->description = $rs->getClob($startcol + 7);

			$this->directory = $rs->getFloat($startcol + 8);

			$this->filesize = $rs->getFloat($startcol + 9);

			$this->how_to_cite = $rs->getString($startcol + 10);

			$this->name = $rs->getString($startcol + 11);

			$this->page_count = $rs->getFloat($startcol + 12);

			$this->path = $rs->getString($startcol + 13);

			$this->title = $rs->getString($startcol + 14);

			$this->viewable = $rs->getString($startcol + 15);

			$this->thumb_id = $rs->getFloat($startcol + 16);

			$this->document_format_id = $rs->getFloat($startcol + 17);

			$this->opening_tool = $rs->getString($startcol + 18);

			$this->usage_type_id = $rs->getFloat($startcol + 19);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 20; // 20 = DataFilePeer::NUM_COLUMNS - DataFilePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating DataFile object", $e);
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
			$con = Propel::getConnection(DataFilePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			DataFilePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(DataFilePeer::DATABASE_NAME);
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

			if ($this->aDataFileRelatedByThumbId !== null) {
				if ($this->aDataFileRelatedByThumbId->isModified()) {
					$affectedRows += $this->aDataFileRelatedByThumbId->save($con);
				}
				$this->setDataFileRelatedByThumbId($this->aDataFileRelatedByThumbId);
			}

			if ($this->aDocumentFormat !== null) {
				if ($this->aDocumentFormat->isModified()) {
					$affectedRows += $this->aDocumentFormat->save($con);
				}
				$this->setDocumentFormat($this->aDocumentFormat);
			}

			if ($this->aEntityType !== null) {
				if ($this->aEntityType->isModified()) {
					$affectedRows += $this->aEntityType->save($con);
				}
				$this->setEntityType($this->aEntityType);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = DataFilePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += DataFilePeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collControllerChannels !== null) {
				foreach($this->collControllerChannels as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collControllerConfigsRelatedByInputDataFileId !== null) {
				foreach($this->collControllerConfigsRelatedByInputDataFileId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collControllerConfigsRelatedByConfigDataFileId !== null) {
				foreach($this->collControllerConfigsRelatedByConfigDataFileId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCoordinateSpaceDataFiles !== null) {
				foreach($this->collCoordinateSpaceDataFiles as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collDAQChannels !== null) {
				foreach($this->collDAQChannels as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collDAQConfigsRelatedByOutputDataFileId !== null) {
				foreach($this->collDAQConfigsRelatedByOutputDataFileId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collDAQConfigsRelatedByConfigDataFileId !== null) {
				foreach($this->collDAQConfigsRelatedByConfigDataFileId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collDataFilesRelatedByThumbId !== null) {
				foreach($this->collDataFilesRelatedByThumbId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentDocumentations !== null) {
				foreach($this->collEquipmentDocumentations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentModelsRelatedByAdditionalSpecFileId !== null) {
				foreach($this->collEquipmentModelsRelatedByAdditionalSpecFileId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentModelsRelatedByInterfaceDocFileId !== null) {
				foreach($this->collEquipmentModelsRelatedByInterfaceDocFileId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentModelsRelatedByManufacturerDocFileId !== null) {
				foreach($this->collEquipmentModelsRelatedByManufacturerDocFileId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentModelsRelatedBySubcomponentsDocFileId !== null) {
				foreach($this->collEquipmentModelsRelatedBySubcomponentsDocFileId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEquipmentModelsRelatedByDesignConsiderationFileId !== null) {
				foreach($this->collEquipmentModelsRelatedByDesignConsiderationFileId as $referrerFK) {
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

			if ($this->collMaterialFiles !== null) {
				foreach($this->collMaterialFiles as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSensorModelDataFiles !== null) {
				foreach($this->collSensorModelDataFiles as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collThumbnails !== null) {
				foreach($this->collThumbnails as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collTrials !== null) {
				foreach($this->collTrials as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collProjectHomepages !== null) {
				foreach($this->collProjectHomepages as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSpecimenComponentMaterialFiles !== null) {
				foreach($this->collSpecimenComponentMaterialFiles as $referrerFK) {
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

			if ($this->aDataFileRelatedByThumbId !== null) {
				if (!$this->aDataFileRelatedByThumbId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFileRelatedByThumbId->getValidationFailures());
				}
			}

			if ($this->aDocumentFormat !== null) {
				if (!$this->aDocumentFormat->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDocumentFormat->getValidationFailures());
				}
			}

			if ($this->aEntityType !== null) {
				if (!$this->aEntityType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEntityType->getValidationFailures());
				}
			}


			if (($retval = DataFilePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collControllerChannels !== null) {
					foreach($this->collControllerChannels as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collControllerConfigsRelatedByInputDataFileId !== null) {
					foreach($this->collControllerConfigsRelatedByInputDataFileId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collControllerConfigsRelatedByConfigDataFileId !== null) {
					foreach($this->collControllerConfigsRelatedByConfigDataFileId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCoordinateSpaceDataFiles !== null) {
					foreach($this->collCoordinateSpaceDataFiles as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collDAQChannels !== null) {
					foreach($this->collDAQChannels as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collDAQConfigsRelatedByOutputDataFileId !== null) {
					foreach($this->collDAQConfigsRelatedByOutputDataFileId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collDAQConfigsRelatedByConfigDataFileId !== null) {
					foreach($this->collDAQConfigsRelatedByConfigDataFileId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentDocumentations !== null) {
					foreach($this->collEquipmentDocumentations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentModelsRelatedByAdditionalSpecFileId !== null) {
					foreach($this->collEquipmentModelsRelatedByAdditionalSpecFileId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentModelsRelatedByInterfaceDocFileId !== null) {
					foreach($this->collEquipmentModelsRelatedByInterfaceDocFileId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentModelsRelatedByManufacturerDocFileId !== null) {
					foreach($this->collEquipmentModelsRelatedByManufacturerDocFileId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentModelsRelatedBySubcomponentsDocFileId !== null) {
					foreach($this->collEquipmentModelsRelatedBySubcomponentsDocFileId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEquipmentModelsRelatedByDesignConsiderationFileId !== null) {
					foreach($this->collEquipmentModelsRelatedByDesignConsiderationFileId as $referrerFK) {
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

				if ($this->collMaterialFiles !== null) {
					foreach($this->collMaterialFiles as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSensorModelDataFiles !== null) {
					foreach($this->collSensorModelDataFiles as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collThumbnails !== null) {
					foreach($this->collThumbnails as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collTrials !== null) {
					foreach($this->collTrials as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collProjectHomepages !== null) {
					foreach($this->collProjectHomepages as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSpecimenComponentMaterialFiles !== null) {
					foreach($this->collSpecimenComponentMaterialFiles as $referrerFK) {
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
		$pos = DataFilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAuthorEmails();
				break;
			case 2:
				return $this->getAuthors();
				break;
			case 3:
				return $this->getChecksum();
				break;
			case 4:
				return $this->getCreated();
				break;
			case 5:
				return $this->getCurationStatus();
				break;
			case 6:
				return $this->getDeleted();
				break;
			case 7:
				return $this->getDescription();
				break;
			case 8:
				return $this->getDirectory();
				break;
			case 9:
				return $this->getFilesize();
				break;
			case 10:
				return $this->getHowToCite();
				break;
			case 11:
				return $this->getName();
				break;
			case 12:
				return $this->getPageCount();
				break;
			case 13:
				return $this->getPath();
				break;
			case 14:
				return $this->getTitle();
				break;
			case 15:
				return $this->getView();
				break;
			case 16:
				return $this->getThumbId();
				break;
			case 17:
				return $this->getDocumentFormatId();
				break;
			case 18:
				return $this->getOpeningTool();
				break;
			case 19:
				return $this->getUsageTypeId();
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
		$keys = DataFilePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getAuthorEmails(),
			$keys[2] => $this->getAuthors(),
			$keys[3] => $this->getChecksum(),
			$keys[4] => $this->getCreated(),
			$keys[5] => $this->getCurationStatus(),
			$keys[6] => $this->getDeleted(),
			$keys[7] => $this->getDescription(),
			$keys[8] => $this->getDirectory(),
			$keys[9] => $this->getFilesize(),
			$keys[10] => $this->getHowToCite(),
			$keys[11] => $this->getName(),
			$keys[12] => $this->getPageCount(),
			$keys[13] => $this->getPath(),
			$keys[14] => $this->getTitle(),
			$keys[15] => $this->getView(),
			$keys[16] => $this->getThumbId(),
			$keys[17] => $this->getDocumentFormatId(),
			$keys[18] => $this->getOpeningTool(),
			$keys[19] => $this->getUsageTypeId(),
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
		$pos = DataFilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAuthorEmails($value);
				break;
			case 2:
				$this->setAuthors($value);
				break;
			case 3:
				$this->setChecksum($value);
				break;
			case 4:
				$this->setCreated($value);
				break;
			case 5:
				$this->setCurationStatus($value);
				break;
			case 6:
				$this->setDeleted($value);
				break;
			case 7:
				$this->setDescription($value);
				break;
			case 8:
				$this->setDirectory($value);
				break;
			case 9:
				$this->setFilesize($value);
				break;
			case 10:
				$this->setHowToCite($value);
				break;
			case 11:
				$this->setName($value);
				break;
			case 12:
				$this->setPageCount($value);
				break;
			case 13:
				$this->setPath($value);
				break;
			case 14:
				$this->setTitle($value);
				break;
			case 15:
				$this->setView($value);
				break;
			case 16:
				$this->setThumbId($value);
				break;
			case 17:
				$this->setDocumentFormatId($value);
				break;
			case 18:
				$this->setOpeningTool($value);
				break;
			case 19:
				$this->setUsageTypeId($value);
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
		$keys = DataFilePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAuthorEmails($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAuthors($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setChecksum($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setCreated($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCurationStatus($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDeleted($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDescription($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setDirectory($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setFilesize($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setHowToCite($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setName($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setPageCount($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setPath($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setTitle($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setView($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setThumbId($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setDocumentFormatId($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setOpeningTool($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setUsageTypeId($arr[$keys[19]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(DataFilePeer::DATABASE_NAME);

		if ($this->isColumnModified(DataFilePeer::ID)) $criteria->add(DataFilePeer::ID, $this->id);
		if ($this->isColumnModified(DataFilePeer::AUTHOR_EMAILS)) $criteria->add(DataFilePeer::AUTHOR_EMAILS, $this->author_emails);
		if ($this->isColumnModified(DataFilePeer::AUTHORS)) $criteria->add(DataFilePeer::AUTHORS, $this->authors);
		if ($this->isColumnModified(DataFilePeer::CHECKSUM)) $criteria->add(DataFilePeer::CHECKSUM, $this->checksum);
		if ($this->isColumnModified(DataFilePeer::CREATED)) $criteria->add(DataFilePeer::CREATED, $this->created);
		if ($this->isColumnModified(DataFilePeer::CURATION_STATUS)) $criteria->add(DataFilePeer::CURATION_STATUS, $this->curation_status);
		if ($this->isColumnModified(DataFilePeer::DELETED)) $criteria->add(DataFilePeer::DELETED, $this->deleted);
		if ($this->isColumnModified(DataFilePeer::DESCRIPTION)) $criteria->add(DataFilePeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(DataFilePeer::DIRECTORY)) $criteria->add(DataFilePeer::DIRECTORY, $this->directory);
		if ($this->isColumnModified(DataFilePeer::FILESIZE)) $criteria->add(DataFilePeer::FILESIZE, $this->filesize);
		if ($this->isColumnModified(DataFilePeer::HOW_TO_CITE)) $criteria->add(DataFilePeer::HOW_TO_CITE, $this->how_to_cite);
		if ($this->isColumnModified(DataFilePeer::NAME)) $criteria->add(DataFilePeer::NAME, $this->name);
		if ($this->isColumnModified(DataFilePeer::PAGE_COUNT)) $criteria->add(DataFilePeer::PAGE_COUNT, $this->page_count);
		if ($this->isColumnModified(DataFilePeer::PATH)) $criteria->add(DataFilePeer::PATH, $this->path);
		if ($this->isColumnModified(DataFilePeer::TITLE)) $criteria->add(DataFilePeer::TITLE, $this->title);
		if ($this->isColumnModified(DataFilePeer::VIEWABLE)) $criteria->add(DataFilePeer::VIEWABLE, $this->viewable);
		if ($this->isColumnModified(DataFilePeer::THUMB_ID)) $criteria->add(DataFilePeer::THUMB_ID, $this->thumb_id);
		if ($this->isColumnModified(DataFilePeer::DOCUMENT_FORMAT_ID)) $criteria->add(DataFilePeer::DOCUMENT_FORMAT_ID, $this->document_format_id);
		if ($this->isColumnModified(DataFilePeer::OPENING_TOOL)) $criteria->add(DataFilePeer::OPENING_TOOL, $this->opening_tool);
		if ($this->isColumnModified(DataFilePeer::USAGE_TYPE_ID)) $criteria->add(DataFilePeer::USAGE_TYPE_ID, $this->usage_type_id);

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
		$criteria = new Criteria(DataFilePeer::DATABASE_NAME);

		$criteria->add(DataFilePeer::ID, $this->id);

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
	 * Generic method to set the primary key (id column).
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
	 * @param      object $copyObj An object of DataFile (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAuthorEmails($this->author_emails);

		$copyObj->setAuthors($this->authors);

		$copyObj->setChecksum($this->checksum);

		$copyObj->setCreated($this->created);

		$copyObj->setCurationStatus($this->curation_status);

		$copyObj->setDeleted($this->deleted);

		$copyObj->setDescription($this->description);

		$copyObj->setDirectory($this->directory);

		$copyObj->setFilesize($this->filesize);

		$copyObj->setHowToCite($this->how_to_cite);

		$copyObj->setName($this->name);

		$copyObj->setPageCount($this->page_count);

		$copyObj->setPath($this->path);

		$copyObj->setTitle($this->title);

		$copyObj->setView($this->viewable);

		$copyObj->setThumbId($this->thumb_id);

		$copyObj->setDocumentFormatId($this->document_format_id);

		$copyObj->setOpeningTool($this->opening_tool);

		$copyObj->setUsageTypeId($this->usage_type_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getControllerChannels() as $relObj) {
				$copyObj->addControllerChannel($relObj->copy($deepCopy));
			}

			foreach($this->getControllerConfigsRelatedByInputDataFileId() as $relObj) {
				$copyObj->addControllerConfigRelatedByInputDataFileId($relObj->copy($deepCopy));
			}

			foreach($this->getControllerConfigsRelatedByConfigDataFileId() as $relObj) {
				$copyObj->addControllerConfigRelatedByConfigDataFileId($relObj->copy($deepCopy));
			}

			foreach($this->getCoordinateSpaceDataFiles() as $relObj) {
				$copyObj->addCoordinateSpaceDataFile($relObj->copy($deepCopy));
			}

			foreach($this->getDAQChannels() as $relObj) {
				$copyObj->addDAQChannel($relObj->copy($deepCopy));
			}

			foreach($this->getDAQConfigsRelatedByOutputDataFileId() as $relObj) {
				$copyObj->addDAQConfigRelatedByOutputDataFileId($relObj->copy($deepCopy));
			}

			foreach($this->getDAQConfigsRelatedByConfigDataFileId() as $relObj) {
				$copyObj->addDAQConfigRelatedByConfigDataFileId($relObj->copy($deepCopy));
			}

			foreach($this->getDataFilesRelatedByThumbId() as $relObj) {
				if($this->getPrimaryKey() === $relObj->getPrimaryKey()) {
						continue;
				}

				$copyObj->addDataFileRelatedByThumbId($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentDocumentations() as $relObj) {
				$copyObj->addEquipmentDocumentation($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentModelsRelatedByAdditionalSpecFileId() as $relObj) {
				$copyObj->addEquipmentModelRelatedByAdditionalSpecFileId($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentModelsRelatedByInterfaceDocFileId() as $relObj) {
				$copyObj->addEquipmentModelRelatedByInterfaceDocFileId($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentModelsRelatedByManufacturerDocFileId() as $relObj) {
				$copyObj->addEquipmentModelRelatedByManufacturerDocFileId($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentModelsRelatedBySubcomponentsDocFileId() as $relObj) {
				$copyObj->addEquipmentModelRelatedBySubcomponentsDocFileId($relObj->copy($deepCopy));
			}

			foreach($this->getEquipmentModelsRelatedByDesignConsiderationFileId() as $relObj) {
				$copyObj->addEquipmentModelRelatedByDesignConsiderationFileId($relObj->copy($deepCopy));
			}

			foreach($this->getFacilityDataFiles() as $relObj) {
				$copyObj->addFacilityDataFile($relObj->copy($deepCopy));
			}

			foreach($this->getMaterialFiles() as $relObj) {
				$copyObj->addMaterialFile($relObj->copy($deepCopy));
			}

			foreach($this->getSensorModelDataFiles() as $relObj) {
				$copyObj->addSensorModelDataFile($relObj->copy($deepCopy));
			}

			foreach($this->getThumbnails() as $relObj) {
				$copyObj->addThumbnail($relObj->copy($deepCopy));
			}

			foreach($this->getTrials() as $relObj) {
				$copyObj->addTrial($relObj->copy($deepCopy));
			}

			foreach($this->getProjectHomepages() as $relObj) {
				$copyObj->addProjectHomepage($relObj->copy($deepCopy));
			}

			foreach($this->getSpecimenComponentMaterialFiles() as $relObj) {
				$copyObj->addSpecimenComponentMaterialFile($relObj->copy($deepCopy));
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
	 * @return     DataFile Clone of current object.
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
	 * @return     DataFilePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new DataFilePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a DataFile object.
	 *
	 * @param      DataFile $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDataFileRelatedByThumbId($v)
	{


		if ($v === null) {
			$this->setThumbId(NULL);
		} else {
			$this->setThumbId($v->getId());
		}


		$this->aDataFileRelatedByThumbId = $v;
	}


	/**
	 * Get the associated DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
	public function getDataFileRelatedByThumbId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';

		if ($this->aDataFileRelatedByThumbId === null && ($this->thumb_id > 0)) {

			$this->aDataFileRelatedByThumbId = DataFilePeer::retrieveByPK($this->thumb_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->thumb_id, $con);
			   $obj->addDataFilesRelatedByThumbId($this);
			 */
		}
		return $this->aDataFileRelatedByThumbId;
	}

	/**
	 * Declares an association between this object and a DocumentFormat object.
	 *
	 * @param      DocumentFormat $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDocumentFormat($v)
	{


		if ($v === null) {
			$this->setDocumentFormatId(NULL);
		} else {
			$this->setDocumentFormatId($v->getId());
		}


		$this->aDocumentFormat = $v;
	}


	/**
	 * Get the associated DocumentFormat object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DocumentFormat The associated DocumentFormat object.
	 * @throws     PropelException
	 */
	public function getDocumentFormat($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDocumentFormatPeer.php';

		if ($this->aDocumentFormat === null && ($this->document_format_id > 0)) {

			$this->aDocumentFormat = DocumentFormatPeer::retrieveByPK($this->document_format_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DocumentFormatPeer::retrieveByPK($this->document_format_id, $con);
			   $obj->addDocumentFormats($this);
			 */
		}
		return $this->aDocumentFormat;
	}

	/**
	 * Declares an association between this object and a EntityType object.
	 *
	 * @param      EntityType $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEntityType($v)
	{


		if ($v === null) {
			$this->setUsageTypeId(NULL);
		} else {
			$this->setUsageTypeId($v->getId());
		}


		$this->aEntityType = $v;
	}


	/**
	 * Get the associated EntityType object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     EntityType The associated EntityType object.
	 * @throws     PropelException
	 */
	public function getEntityType($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEntityTypePeer.php';

		if ($this->aEntityType === null && ($this->usage_type_id > 0)) {

			$this->aEntityType = EntityTypePeer::retrieveByPK($this->usage_type_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EntityTypePeer::retrieveByPK($this->usage_type_id, $con);
			   $obj->addEntityTypes($this);
			 */
		}
		return $this->aEntityType;
	}

	/**
	 * Temporary storage of collControllerChannels to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initControllerChannels()
	{
		if ($this->collControllerChannels === null) {
			$this->collControllerChannels = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getControllerChannels($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
			   $this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::DATA_FILE_ID, $this->getId());

				ControllerChannelPeer::addSelectColumns($criteria);
				$this->collControllerChannels = ControllerChannelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ControllerChannelPeer::DATA_FILE_ID, $this->getId());

				ControllerChannelPeer::addSelectColumns($criteria);
				if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
					$this->collControllerChannels = ControllerChannelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastControllerChannelCriteria = $criteria;
		return $this->collControllerChannels;
	}

	/**
	 * Returns the number of related ControllerChannels.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countControllerChannels($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ControllerChannelPeer::DATA_FILE_ID, $this->getId());

		return ControllerChannelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ControllerChannel object to this object
	 * through the ControllerChannel foreign key attribute
	 *
	 * @param      ControllerChannel $l ControllerChannel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addControllerChannel(ControllerChannel $l)
	{
		$this->collControllerChannels[] = $l;
		$l->setDataFile($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getControllerChannelsJoinControllerConfig($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
				$this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::DATA_FILE_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinControllerConfig($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinControllerConfig($criteria, $con);
			}
		}
		$this->lastControllerChannelCriteria = $criteria;

		return $this->collControllerChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getControllerChannelsJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
				$this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::DATA_FILE_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastControllerChannelCriteria = $criteria;

		return $this->collControllerChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getControllerChannelsJoinLocation($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
				$this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::DATA_FILE_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinLocation($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinLocation($criteria, $con);
			}
		}
		$this->lastControllerChannelCriteria = $criteria;

		return $this->collControllerChannels;
	}

	/**
	 * Temporary storage of collControllerConfigsRelatedByInputDataFileId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initControllerConfigsRelatedByInputDataFileId()
	{
		if ($this->collControllerConfigsRelatedByInputDataFileId === null) {
			$this->collControllerConfigsRelatedByInputDataFileId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related ControllerConfigsRelatedByInputDataFileId from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getControllerConfigsRelatedByInputDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigsRelatedByInputDataFileId === null) {
			if ($this->isNew()) {
			   $this->collControllerConfigsRelatedByInputDataFileId = array();
			} else {

				$criteria->add(ControllerConfigPeer::INPUT_DATA_FILE_ID, $this->getId());

				ControllerConfigPeer::addSelectColumns($criteria);
				$this->collControllerConfigsRelatedByInputDataFileId = ControllerConfigPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ControllerConfigPeer::INPUT_DATA_FILE_ID, $this->getId());

				ControllerConfigPeer::addSelectColumns($criteria);
				if (!isset($this->lastControllerConfigRelatedByInputDataFileIdCriteria) || !$this->lastControllerConfigRelatedByInputDataFileIdCriteria->equals($criteria)) {
					$this->collControllerConfigsRelatedByInputDataFileId = ControllerConfigPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastControllerConfigRelatedByInputDataFileIdCriteria = $criteria;
		return $this->collControllerConfigsRelatedByInputDataFileId;
	}

	/**
	 * Returns the number of related ControllerConfigsRelatedByInputDataFileId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countControllerConfigsRelatedByInputDataFileId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ControllerConfigPeer::INPUT_DATA_FILE_ID, $this->getId());

		return ControllerConfigPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ControllerConfig object to this object
	 * through the ControllerConfig foreign key attribute
	 *
	 * @param      ControllerConfig $l ControllerConfig
	 * @return     void
	 * @throws     PropelException
	 */
	public function addControllerConfigRelatedByInputDataFileId(ControllerConfig $l)
	{
		$this->collControllerConfigsRelatedByInputDataFileId[] = $l;
		$l->setDataFileRelatedByInputDataFileId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related ControllerConfigsRelatedByInputDataFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getControllerConfigsRelatedByInputDataFileIdJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigsRelatedByInputDataFileId === null) {
			if ($this->isNew()) {
				$this->collControllerConfigsRelatedByInputDataFileId = array();
			} else {

				$criteria->add(ControllerConfigPeer::INPUT_DATA_FILE_ID, $this->getId());

				$this->collControllerConfigsRelatedByInputDataFileId = ControllerConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::INPUT_DATA_FILE_ID, $this->getId());

			if (!isset($this->lastControllerConfigRelatedByInputDataFileIdCriteria) || !$this->lastControllerConfigRelatedByInputDataFileIdCriteria->equals($criteria)) {
				$this->collControllerConfigsRelatedByInputDataFileId = ControllerConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastControllerConfigRelatedByInputDataFileIdCriteria = $criteria;

		return $this->collControllerConfigsRelatedByInputDataFileId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related ControllerConfigsRelatedByInputDataFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getControllerConfigsRelatedByInputDataFileIdJoinMeasurementUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigsRelatedByInputDataFileId === null) {
			if ($this->isNew()) {
				$this->collControllerConfigsRelatedByInputDataFileId = array();
			} else {

				$criteria->add(ControllerConfigPeer::INPUT_DATA_FILE_ID, $this->getId());

				$this->collControllerConfigsRelatedByInputDataFileId = ControllerConfigPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::INPUT_DATA_FILE_ID, $this->getId());

			if (!isset($this->lastControllerConfigRelatedByInputDataFileIdCriteria) || !$this->lastControllerConfigRelatedByInputDataFileIdCriteria->equals($criteria)) {
				$this->collControllerConfigsRelatedByInputDataFileId = ControllerConfigPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastControllerConfigRelatedByInputDataFileIdCriteria = $criteria;

		return $this->collControllerConfigsRelatedByInputDataFileId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related ControllerConfigsRelatedByInputDataFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getControllerConfigsRelatedByInputDataFileIdJoinTrial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigsRelatedByInputDataFileId === null) {
			if ($this->isNew()) {
				$this->collControllerConfigsRelatedByInputDataFileId = array();
			} else {

				$criteria->add(ControllerConfigPeer::INPUT_DATA_FILE_ID, $this->getId());

				$this->collControllerConfigsRelatedByInputDataFileId = ControllerConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::INPUT_DATA_FILE_ID, $this->getId());

			if (!isset($this->lastControllerConfigRelatedByInputDataFileIdCriteria) || !$this->lastControllerConfigRelatedByInputDataFileIdCriteria->equals($criteria)) {
				$this->collControllerConfigsRelatedByInputDataFileId = ControllerConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastControllerConfigRelatedByInputDataFileIdCriteria = $criteria;

		return $this->collControllerConfigsRelatedByInputDataFileId;
	}

	/**
	 * Temporary storage of collControllerConfigsRelatedByConfigDataFileId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initControllerConfigsRelatedByConfigDataFileId()
	{
		if ($this->collControllerConfigsRelatedByConfigDataFileId === null) {
			$this->collControllerConfigsRelatedByConfigDataFileId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related ControllerConfigsRelatedByConfigDataFileId from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getControllerConfigsRelatedByConfigDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigsRelatedByConfigDataFileId === null) {
			if ($this->isNew()) {
			   $this->collControllerConfigsRelatedByConfigDataFileId = array();
			} else {

				$criteria->add(ControllerConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

				ControllerConfigPeer::addSelectColumns($criteria);
				$this->collControllerConfigsRelatedByConfigDataFileId = ControllerConfigPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ControllerConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

				ControllerConfigPeer::addSelectColumns($criteria);
				if (!isset($this->lastControllerConfigRelatedByConfigDataFileIdCriteria) || !$this->lastControllerConfigRelatedByConfigDataFileIdCriteria->equals($criteria)) {
					$this->collControllerConfigsRelatedByConfigDataFileId = ControllerConfigPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastControllerConfigRelatedByConfigDataFileIdCriteria = $criteria;
		return $this->collControllerConfigsRelatedByConfigDataFileId;
	}

	/**
	 * Returns the number of related ControllerConfigsRelatedByConfigDataFileId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countControllerConfigsRelatedByConfigDataFileId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ControllerConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

		return ControllerConfigPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ControllerConfig object to this object
	 * through the ControllerConfig foreign key attribute
	 *
	 * @param      ControllerConfig $l ControllerConfig
	 * @return     void
	 * @throws     PropelException
	 */
	public function addControllerConfigRelatedByConfigDataFileId(ControllerConfig $l)
	{
		$this->collControllerConfigsRelatedByConfigDataFileId[] = $l;
		$l->setDataFileRelatedByConfigDataFileId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related ControllerConfigsRelatedByConfigDataFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getControllerConfigsRelatedByConfigDataFileIdJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigsRelatedByConfigDataFileId === null) {
			if ($this->isNew()) {
				$this->collControllerConfigsRelatedByConfigDataFileId = array();
			} else {

				$criteria->add(ControllerConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

				$this->collControllerConfigsRelatedByConfigDataFileId = ControllerConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

			if (!isset($this->lastControllerConfigRelatedByConfigDataFileIdCriteria) || !$this->lastControllerConfigRelatedByConfigDataFileIdCriteria->equals($criteria)) {
				$this->collControllerConfigsRelatedByConfigDataFileId = ControllerConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastControllerConfigRelatedByConfigDataFileIdCriteria = $criteria;

		return $this->collControllerConfigsRelatedByConfigDataFileId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related ControllerConfigsRelatedByConfigDataFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getControllerConfigsRelatedByConfigDataFileIdJoinMeasurementUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigsRelatedByConfigDataFileId === null) {
			if ($this->isNew()) {
				$this->collControllerConfigsRelatedByConfigDataFileId = array();
			} else {

				$criteria->add(ControllerConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

				$this->collControllerConfigsRelatedByConfigDataFileId = ControllerConfigPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

			if (!isset($this->lastControllerConfigRelatedByConfigDataFileIdCriteria) || !$this->lastControllerConfigRelatedByConfigDataFileIdCriteria->equals($criteria)) {
				$this->collControllerConfigsRelatedByConfigDataFileId = ControllerConfigPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastControllerConfigRelatedByConfigDataFileIdCriteria = $criteria;

		return $this->collControllerConfigsRelatedByConfigDataFileId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related ControllerConfigsRelatedByConfigDataFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getControllerConfigsRelatedByConfigDataFileIdJoinTrial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerConfigsRelatedByConfigDataFileId === null) {
			if ($this->isNew()) {
				$this->collControllerConfigsRelatedByConfigDataFileId = array();
			} else {

				$criteria->add(ControllerConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

				$this->collControllerConfigsRelatedByConfigDataFileId = ControllerConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

			if (!isset($this->lastControllerConfigRelatedByConfigDataFileIdCriteria) || !$this->lastControllerConfigRelatedByConfigDataFileIdCriteria->equals($criteria)) {
				$this->collControllerConfigsRelatedByConfigDataFileId = ControllerConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastControllerConfigRelatedByConfigDataFileIdCriteria = $criteria;

		return $this->collControllerConfigsRelatedByConfigDataFileId;
	}

	/**
	 * Temporary storage of collCoordinateSpaceDataFiles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initCoordinateSpaceDataFiles()
	{
		if ($this->collCoordinateSpaceDataFiles === null) {
			$this->collCoordinateSpaceDataFiles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related CoordinateSpaceDataFiles from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getCoordinateSpaceDataFiles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpaceDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaceDataFiles === null) {
			if ($this->isNew()) {
			   $this->collCoordinateSpaceDataFiles = array();
			} else {

				$criteria->add(CoordinateSpaceDataFilePeer::DATA_FILE_ID, $this->getId());

				CoordinateSpaceDataFilePeer::addSelectColumns($criteria);
				$this->collCoordinateSpaceDataFiles = CoordinateSpaceDataFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CoordinateSpaceDataFilePeer::DATA_FILE_ID, $this->getId());

				CoordinateSpaceDataFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastCoordinateSpaceDataFileCriteria) || !$this->lastCoordinateSpaceDataFileCriteria->equals($criteria)) {
					$this->collCoordinateSpaceDataFiles = CoordinateSpaceDataFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCoordinateSpaceDataFileCriteria = $criteria;
		return $this->collCoordinateSpaceDataFiles;
	}

	/**
	 * Returns the number of related CoordinateSpaceDataFiles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countCoordinateSpaceDataFiles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpaceDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(CoordinateSpaceDataFilePeer::DATA_FILE_ID, $this->getId());

		return CoordinateSpaceDataFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a CoordinateSpaceDataFile object to this object
	 * through the CoordinateSpaceDataFile foreign key attribute
	 *
	 * @param      CoordinateSpaceDataFile $l CoordinateSpaceDataFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCoordinateSpaceDataFile(CoordinateSpaceDataFile $l)
	{
		$this->collCoordinateSpaceDataFiles[] = $l;
		$l->setDataFile($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related CoordinateSpaceDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getCoordinateSpaceDataFilesJoinCoordinateSpace($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseCoordinateSpaceDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCoordinateSpaceDataFiles === null) {
			if ($this->isNew()) {
				$this->collCoordinateSpaceDataFiles = array();
			} else {

				$criteria->add(CoordinateSpaceDataFilePeer::DATA_FILE_ID, $this->getId());

				$this->collCoordinateSpaceDataFiles = CoordinateSpaceDataFilePeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CoordinateSpaceDataFilePeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastCoordinateSpaceDataFileCriteria) || !$this->lastCoordinateSpaceDataFileCriteria->equals($criteria)) {
				$this->collCoordinateSpaceDataFiles = CoordinateSpaceDataFilePeer::doSelectJoinCoordinateSpace($criteria, $con);
			}
		}
		$this->lastCoordinateSpaceDataFileCriteria = $criteria;

		return $this->collCoordinateSpaceDataFiles;
	}

	/**
	 * Temporary storage of collDAQChannels to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDAQChannels()
	{
		if ($this->collDAQChannels === null) {
			$this->collDAQChannels = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDAQChannels($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
			   $this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::DATA_FILE_ID, $this->getId());

				DAQChannelPeer::addSelectColumns($criteria);
				$this->collDAQChannels = DAQChannelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DAQChannelPeer::DATA_FILE_ID, $this->getId());

				DAQChannelPeer::addSelectColumns($criteria);
				if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
					$this->collDAQChannels = DAQChannelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDAQChannelCriteria = $criteria;
		return $this->collDAQChannels;
	}

	/**
	 * Returns the number of related DAQChannels.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDAQChannels($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DAQChannelPeer::DATA_FILE_ID, $this->getId());

		return DAQChannelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DAQChannel object to this object
	 * through the DAQChannel foreign key attribute
	 *
	 * @param      DAQChannel $l DAQChannel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDAQChannel(DAQChannel $l)
	{
		$this->collDAQChannels[] = $l;
		$l->setDataFile($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getDAQChannelsJoinDAQConfig($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
				$this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::DATA_FILE_ID, $this->getId());

				$this->collDAQChannels = DAQChannelPeer::doSelectJoinDAQConfig($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQChannelPeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
				$this->collDAQChannels = DAQChannelPeer::doSelectJoinDAQConfig($criteria, $con);
			}
		}
		$this->lastDAQChannelCriteria = $criteria;

		return $this->collDAQChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getDAQChannelsJoinLocation($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
				$this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::DATA_FILE_ID, $this->getId());

				$this->collDAQChannels = DAQChannelPeer::doSelectJoinLocation($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQChannelPeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
				$this->collDAQChannels = DAQChannelPeer::doSelectJoinLocation($criteria, $con);
			}
		}
		$this->lastDAQChannelCriteria = $criteria;

		return $this->collDAQChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related DAQChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getDAQChannelsJoinSensor($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQChannels === null) {
			if ($this->isNew()) {
				$this->collDAQChannels = array();
			} else {

				$criteria->add(DAQChannelPeer::DATA_FILE_ID, $this->getId());

				$this->collDAQChannels = DAQChannelPeer::doSelectJoinSensor($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQChannelPeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastDAQChannelCriteria) || !$this->lastDAQChannelCriteria->equals($criteria)) {
				$this->collDAQChannels = DAQChannelPeer::doSelectJoinSensor($criteria, $con);
			}
		}
		$this->lastDAQChannelCriteria = $criteria;

		return $this->collDAQChannels;
	}

	/**
	 * Temporary storage of collDAQConfigsRelatedByOutputDataFileId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDAQConfigsRelatedByOutputDataFileId()
	{
		if ($this->collDAQConfigsRelatedByOutputDataFileId === null) {
			$this->collDAQConfigsRelatedByOutputDataFileId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related DAQConfigsRelatedByOutputDataFileId from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDAQConfigsRelatedByOutputDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigsRelatedByOutputDataFileId === null) {
			if ($this->isNew()) {
			   $this->collDAQConfigsRelatedByOutputDataFileId = array();
			} else {

				$criteria->add(DAQConfigPeer::OUTPUT_DATA_FILE_ID, $this->getId());

				DAQConfigPeer::addSelectColumns($criteria);
				$this->collDAQConfigsRelatedByOutputDataFileId = DAQConfigPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DAQConfigPeer::OUTPUT_DATA_FILE_ID, $this->getId());

				DAQConfigPeer::addSelectColumns($criteria);
				if (!isset($this->lastDAQConfigRelatedByOutputDataFileIdCriteria) || !$this->lastDAQConfigRelatedByOutputDataFileIdCriteria->equals($criteria)) {
					$this->collDAQConfigsRelatedByOutputDataFileId = DAQConfigPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDAQConfigRelatedByOutputDataFileIdCriteria = $criteria;
		return $this->collDAQConfigsRelatedByOutputDataFileId;
	}

	/**
	 * Returns the number of related DAQConfigsRelatedByOutputDataFileId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDAQConfigsRelatedByOutputDataFileId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DAQConfigPeer::OUTPUT_DATA_FILE_ID, $this->getId());

		return DAQConfigPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DAQConfig object to this object
	 * through the DAQConfig foreign key attribute
	 *
	 * @param      DAQConfig $l DAQConfig
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDAQConfigRelatedByOutputDataFileId(DAQConfig $l)
	{
		$this->collDAQConfigsRelatedByOutputDataFileId[] = $l;
		$l->setDataFileRelatedByOutputDataFileId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related DAQConfigsRelatedByOutputDataFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getDAQConfigsRelatedByOutputDataFileIdJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigsRelatedByOutputDataFileId === null) {
			if ($this->isNew()) {
				$this->collDAQConfigsRelatedByOutputDataFileId = array();
			} else {

				$criteria->add(DAQConfigPeer::OUTPUT_DATA_FILE_ID, $this->getId());

				$this->collDAQConfigsRelatedByOutputDataFileId = DAQConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQConfigPeer::OUTPUT_DATA_FILE_ID, $this->getId());

			if (!isset($this->lastDAQConfigRelatedByOutputDataFileIdCriteria) || !$this->lastDAQConfigRelatedByOutputDataFileIdCriteria->equals($criteria)) {
				$this->collDAQConfigsRelatedByOutputDataFileId = DAQConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastDAQConfigRelatedByOutputDataFileIdCriteria = $criteria;

		return $this->collDAQConfigsRelatedByOutputDataFileId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related DAQConfigsRelatedByOutputDataFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getDAQConfigsRelatedByOutputDataFileIdJoinTrial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigsRelatedByOutputDataFileId === null) {
			if ($this->isNew()) {
				$this->collDAQConfigsRelatedByOutputDataFileId = array();
			} else {

				$criteria->add(DAQConfigPeer::OUTPUT_DATA_FILE_ID, $this->getId());

				$this->collDAQConfigsRelatedByOutputDataFileId = DAQConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQConfigPeer::OUTPUT_DATA_FILE_ID, $this->getId());

			if (!isset($this->lastDAQConfigRelatedByOutputDataFileIdCriteria) || !$this->lastDAQConfigRelatedByOutputDataFileIdCriteria->equals($criteria)) {
				$this->collDAQConfigsRelatedByOutputDataFileId = DAQConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastDAQConfigRelatedByOutputDataFileIdCriteria = $criteria;

		return $this->collDAQConfigsRelatedByOutputDataFileId;
	}

	/**
	 * Temporary storage of collDAQConfigsRelatedByConfigDataFileId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDAQConfigsRelatedByConfigDataFileId()
	{
		if ($this->collDAQConfigsRelatedByConfigDataFileId === null) {
			$this->collDAQConfigsRelatedByConfigDataFileId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related DAQConfigsRelatedByConfigDataFileId from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDAQConfigsRelatedByConfigDataFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigsRelatedByConfigDataFileId === null) {
			if ($this->isNew()) {
			   $this->collDAQConfigsRelatedByConfigDataFileId = array();
			} else {

				$criteria->add(DAQConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

				DAQConfigPeer::addSelectColumns($criteria);
				$this->collDAQConfigsRelatedByConfigDataFileId = DAQConfigPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DAQConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

				DAQConfigPeer::addSelectColumns($criteria);
				if (!isset($this->lastDAQConfigRelatedByConfigDataFileIdCriteria) || !$this->lastDAQConfigRelatedByConfigDataFileIdCriteria->equals($criteria)) {
					$this->collDAQConfigsRelatedByConfigDataFileId = DAQConfigPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDAQConfigRelatedByConfigDataFileIdCriteria = $criteria;
		return $this->collDAQConfigsRelatedByConfigDataFileId;
	}

	/**
	 * Returns the number of related DAQConfigsRelatedByConfigDataFileId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDAQConfigsRelatedByConfigDataFileId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DAQConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

		return DAQConfigPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DAQConfig object to this object
	 * through the DAQConfig foreign key attribute
	 *
	 * @param      DAQConfig $l DAQConfig
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDAQConfigRelatedByConfigDataFileId(DAQConfig $l)
	{
		$this->collDAQConfigsRelatedByConfigDataFileId[] = $l;
		$l->setDataFileRelatedByConfigDataFileId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related DAQConfigsRelatedByConfigDataFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getDAQConfigsRelatedByConfigDataFileIdJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigsRelatedByConfigDataFileId === null) {
			if ($this->isNew()) {
				$this->collDAQConfigsRelatedByConfigDataFileId = array();
			} else {

				$criteria->add(DAQConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

				$this->collDAQConfigsRelatedByConfigDataFileId = DAQConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

			if (!isset($this->lastDAQConfigRelatedByConfigDataFileIdCriteria) || !$this->lastDAQConfigRelatedByConfigDataFileIdCriteria->equals($criteria)) {
				$this->collDAQConfigsRelatedByConfigDataFileId = DAQConfigPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastDAQConfigRelatedByConfigDataFileIdCriteria = $criteria;

		return $this->collDAQConfigsRelatedByConfigDataFileId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related DAQConfigsRelatedByConfigDataFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getDAQConfigsRelatedByConfigDataFileIdJoinTrial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDAQConfigPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDAQConfigsRelatedByConfigDataFileId === null) {
			if ($this->isNew()) {
				$this->collDAQConfigsRelatedByConfigDataFileId = array();
			} else {

				$criteria->add(DAQConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

				$this->collDAQConfigsRelatedByConfigDataFileId = DAQConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DAQConfigPeer::CONFIG_DATA_FILE_ID, $this->getId());

			if (!isset($this->lastDAQConfigRelatedByConfigDataFileIdCriteria) || !$this->lastDAQConfigRelatedByConfigDataFileIdCriteria->equals($criteria)) {
				$this->collDAQConfigsRelatedByConfigDataFileId = DAQConfigPeer::doSelectJoinTrial($criteria, $con);
			}
		}
		$this->lastDAQConfigRelatedByConfigDataFileIdCriteria = $criteria;

		return $this->collDAQConfigsRelatedByConfigDataFileId;
	}

	/**
	 * Temporary storage of collDataFilesRelatedByThumbId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initDataFilesRelatedByThumbId()
	{
		if ($this->collDataFilesRelatedByThumbId === null) {
			$this->collDataFilesRelatedByThumbId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related DataFilesRelatedByThumbId from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getDataFilesRelatedByThumbId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFilesRelatedByThumbId === null) {
			if ($this->isNew()) {
			   $this->collDataFilesRelatedByThumbId = array();
			} else {

				$criteria->add(DataFilePeer::THUMB_ID, $this->getId());

				DataFilePeer::addSelectColumns($criteria);
				$this->collDataFilesRelatedByThumbId = DataFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(DataFilePeer::THUMB_ID, $this->getId());

				DataFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastDataFileRelatedByThumbIdCriteria) || !$this->lastDataFileRelatedByThumbIdCriteria->equals($criteria)) {
					$this->collDataFilesRelatedByThumbId = DataFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastDataFileRelatedByThumbIdCriteria = $criteria;
		return $this->collDataFilesRelatedByThumbId;
	}

	/**
	 * Returns the number of related DataFilesRelatedByThumbId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countDataFilesRelatedByThumbId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(DataFilePeer::THUMB_ID, $this->getId());

		return DataFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a DataFile object to this object
	 * through the DataFile foreign key attribute
	 *
	 * @param      DataFile $l DataFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addDataFileRelatedByThumbId(DataFile $l)
	{
		$this->collDataFilesRelatedByThumbId[] = $l;
		$l->setDataFileRelatedByThumbId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related DataFilesRelatedByThumbId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getDataFilesRelatedByThumbIdJoinDocumentFormat($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFilesRelatedByThumbId === null) {
			if ($this->isNew()) {
				$this->collDataFilesRelatedByThumbId = array();
			} else {

				$criteria->add(DataFilePeer::THUMB_ID, $this->getId());

				$this->collDataFilesRelatedByThumbId = DataFilePeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFilePeer::THUMB_ID, $this->getId());

			if (!isset($this->lastDataFileRelatedByThumbIdCriteria) || !$this->lastDataFileRelatedByThumbIdCriteria->equals($criteria)) {
				$this->collDataFilesRelatedByThumbId = DataFilePeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		}
		$this->lastDataFileRelatedByThumbIdCriteria = $criteria;

		return $this->collDataFilesRelatedByThumbId;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related DataFilesRelatedByThumbId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getDataFilesRelatedByThumbIdJoinEntityType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collDataFilesRelatedByThumbId === null) {
			if ($this->isNew()) {
				$this->collDataFilesRelatedByThumbId = array();
			} else {

				$criteria->add(DataFilePeer::THUMB_ID, $this->getId());

				$this->collDataFilesRelatedByThumbId = DataFilePeer::doSelectJoinEntityType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(DataFilePeer::THUMB_ID, $this->getId());

			if (!isset($this->lastDataFileRelatedByThumbIdCriteria) || !$this->lastDataFileRelatedByThumbIdCriteria->equals($criteria)) {
				$this->collDataFilesRelatedByThumbId = DataFilePeer::doSelectJoinEntityType($criteria, $con);
			}
		}
		$this->lastDataFileRelatedByThumbIdCriteria = $criteria;

		return $this->collDataFilesRelatedByThumbId;
	}

	/**
	 * Temporary storage of collEquipmentDocumentations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentDocumentations()
	{
		if ($this->collEquipmentDocumentations === null) {
			$this->collEquipmentDocumentations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentDocumentations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentDocumentationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentDocumentations === null) {
			if ($this->isNew()) {
			   $this->collEquipmentDocumentations = array();
			} else {

				$criteria->add(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, $this->getId());

				EquipmentDocumentationPeer::addSelectColumns($criteria);
				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, $this->getId());

				EquipmentDocumentationPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentDocumentationCriteria) || !$this->lastEquipmentDocumentationCriteria->equals($criteria)) {
					$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentDocumentationCriteria = $criteria;
		return $this->collEquipmentDocumentations;
	}

	/**
	 * Returns the number of related EquipmentDocumentations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentDocumentations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentDocumentationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, $this->getId());

		return EquipmentDocumentationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentDocumentation object to this object
	 * through the EquipmentDocumentation foreign key attribute
	 *
	 * @param      EquipmentDocumentation $l EquipmentDocumentation
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentDocumentation(EquipmentDocumentation $l)
	{
		$this->collEquipmentDocumentations[] = $l;
		$l->setDataFile($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getEquipmentDocumentationsJoinDocumentFormat($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentDocumentationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentDocumentations === null) {
			if ($this->isNew()) {
				$this->collEquipmentDocumentations = array();
			} else {

				$criteria->add(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, $this->getId());

				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, $this->getId());

			if (!isset($this->lastEquipmentDocumentationCriteria) || !$this->lastEquipmentDocumentationCriteria->equals($criteria)) {
				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		}
		$this->lastEquipmentDocumentationCriteria = $criteria;

		return $this->collEquipmentDocumentations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getEquipmentDocumentationsJoinDocumentType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentDocumentationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentDocumentations === null) {
			if ($this->isNew()) {
				$this->collEquipmentDocumentations = array();
			} else {

				$criteria->add(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, $this->getId());

				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDocumentType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, $this->getId());

			if (!isset($this->lastEquipmentDocumentationCriteria) || !$this->lastEquipmentDocumentationCriteria->equals($criteria)) {
				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinDocumentType($criteria, $con);
			}
		}
		$this->lastEquipmentDocumentationCriteria = $criteria;

		return $this->collEquipmentDocumentations;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related EquipmentDocumentations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getEquipmentDocumentationsJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentDocumentationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentDocumentations === null) {
			if ($this->isNew()) {
				$this->collEquipmentDocumentations = array();
			} else {

				$criteria->add(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, $this->getId());

				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentDocumentationPeer::DOCUMENTATION_FILE_ID, $this->getId());

			if (!isset($this->lastEquipmentDocumentationCriteria) || !$this->lastEquipmentDocumentationCriteria->equals($criteria)) {
				$this->collEquipmentDocumentations = EquipmentDocumentationPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastEquipmentDocumentationCriteria = $criteria;

		return $this->collEquipmentDocumentations;
	}

	/**
	 * Temporary storage of collEquipmentModelsRelatedByAdditionalSpecFileId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentModelsRelatedByAdditionalSpecFileId()
	{
		if ($this->collEquipmentModelsRelatedByAdditionalSpecFileId === null) {
			$this->collEquipmentModelsRelatedByAdditionalSpecFileId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related EquipmentModelsRelatedByAdditionalSpecFileId from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentModelsRelatedByAdditionalSpecFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModelsRelatedByAdditionalSpecFileId === null) {
			if ($this->isNew()) {
			   $this->collEquipmentModelsRelatedByAdditionalSpecFileId = array();
			} else {

				$criteria->add(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				$this->collEquipmentModelsRelatedByAdditionalSpecFileId = EquipmentModelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentModelRelatedByAdditionalSpecFileIdCriteria) || !$this->lastEquipmentModelRelatedByAdditionalSpecFileIdCriteria->equals($criteria)) {
					$this->collEquipmentModelsRelatedByAdditionalSpecFileId = EquipmentModelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentModelRelatedByAdditionalSpecFileIdCriteria = $criteria;
		return $this->collEquipmentModelsRelatedByAdditionalSpecFileId;
	}

	/**
	 * Returns the number of related EquipmentModelsRelatedByAdditionalSpecFileId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentModelsRelatedByAdditionalSpecFileId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, $this->getId());

		return EquipmentModelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentModel object to this object
	 * through the EquipmentModel foreign key attribute
	 *
	 * @param      EquipmentModel $l EquipmentModel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentModelRelatedByAdditionalSpecFileId(EquipmentModel $l)
	{
		$this->collEquipmentModelsRelatedByAdditionalSpecFileId[] = $l;
		$l->setDataFileRelatedByAdditionalSpecFileId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related EquipmentModelsRelatedByAdditionalSpecFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getEquipmentModelsRelatedByAdditionalSpecFileIdJoinEquipmentClass($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModelsRelatedByAdditionalSpecFileId === null) {
			if ($this->isNew()) {
				$this->collEquipmentModelsRelatedByAdditionalSpecFileId = array();
			} else {

				$criteria->add(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, $this->getId());

				$this->collEquipmentModelsRelatedByAdditionalSpecFileId = EquipmentModelPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentModelPeer::ADDITIONAL_SPEC_FILE_ID, $this->getId());

			if (!isset($this->lastEquipmentModelRelatedByAdditionalSpecFileIdCriteria) || !$this->lastEquipmentModelRelatedByAdditionalSpecFileIdCriteria->equals($criteria)) {
				$this->collEquipmentModelsRelatedByAdditionalSpecFileId = EquipmentModelPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		}
		$this->lastEquipmentModelRelatedByAdditionalSpecFileIdCriteria = $criteria;

		return $this->collEquipmentModelsRelatedByAdditionalSpecFileId;
	}

	/**
	 * Temporary storage of collEquipmentModelsRelatedByInterfaceDocFileId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentModelsRelatedByInterfaceDocFileId()
	{
		if ($this->collEquipmentModelsRelatedByInterfaceDocFileId === null) {
			$this->collEquipmentModelsRelatedByInterfaceDocFileId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related EquipmentModelsRelatedByInterfaceDocFileId from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentModelsRelatedByInterfaceDocFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModelsRelatedByInterfaceDocFileId === null) {
			if ($this->isNew()) {
			   $this->collEquipmentModelsRelatedByInterfaceDocFileId = array();
			} else {

				$criteria->add(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				$this->collEquipmentModelsRelatedByInterfaceDocFileId = EquipmentModelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentModelRelatedByInterfaceDocFileIdCriteria) || !$this->lastEquipmentModelRelatedByInterfaceDocFileIdCriteria->equals($criteria)) {
					$this->collEquipmentModelsRelatedByInterfaceDocFileId = EquipmentModelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentModelRelatedByInterfaceDocFileIdCriteria = $criteria;
		return $this->collEquipmentModelsRelatedByInterfaceDocFileId;
	}

	/**
	 * Returns the number of related EquipmentModelsRelatedByInterfaceDocFileId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentModelsRelatedByInterfaceDocFileId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, $this->getId());

		return EquipmentModelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentModel object to this object
	 * through the EquipmentModel foreign key attribute
	 *
	 * @param      EquipmentModel $l EquipmentModel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentModelRelatedByInterfaceDocFileId(EquipmentModel $l)
	{
		$this->collEquipmentModelsRelatedByInterfaceDocFileId[] = $l;
		$l->setDataFileRelatedByInterfaceDocFileId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related EquipmentModelsRelatedByInterfaceDocFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getEquipmentModelsRelatedByInterfaceDocFileIdJoinEquipmentClass($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModelsRelatedByInterfaceDocFileId === null) {
			if ($this->isNew()) {
				$this->collEquipmentModelsRelatedByInterfaceDocFileId = array();
			} else {

				$criteria->add(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, $this->getId());

				$this->collEquipmentModelsRelatedByInterfaceDocFileId = EquipmentModelPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentModelPeer::INTERFACE_DOC_FILE_ID, $this->getId());

			if (!isset($this->lastEquipmentModelRelatedByInterfaceDocFileIdCriteria) || !$this->lastEquipmentModelRelatedByInterfaceDocFileIdCriteria->equals($criteria)) {
				$this->collEquipmentModelsRelatedByInterfaceDocFileId = EquipmentModelPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		}
		$this->lastEquipmentModelRelatedByInterfaceDocFileIdCriteria = $criteria;

		return $this->collEquipmentModelsRelatedByInterfaceDocFileId;
	}

	/**
	 * Temporary storage of collEquipmentModelsRelatedByManufacturerDocFileId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentModelsRelatedByManufacturerDocFileId()
	{
		if ($this->collEquipmentModelsRelatedByManufacturerDocFileId === null) {
			$this->collEquipmentModelsRelatedByManufacturerDocFileId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related EquipmentModelsRelatedByManufacturerDocFileId from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentModelsRelatedByManufacturerDocFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModelsRelatedByManufacturerDocFileId === null) {
			if ($this->isNew()) {
			   $this->collEquipmentModelsRelatedByManufacturerDocFileId = array();
			} else {

				$criteria->add(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				$this->collEquipmentModelsRelatedByManufacturerDocFileId = EquipmentModelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentModelRelatedByManufacturerDocFileIdCriteria) || !$this->lastEquipmentModelRelatedByManufacturerDocFileIdCriteria->equals($criteria)) {
					$this->collEquipmentModelsRelatedByManufacturerDocFileId = EquipmentModelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentModelRelatedByManufacturerDocFileIdCriteria = $criteria;
		return $this->collEquipmentModelsRelatedByManufacturerDocFileId;
	}

	/**
	 * Returns the number of related EquipmentModelsRelatedByManufacturerDocFileId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentModelsRelatedByManufacturerDocFileId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, $this->getId());

		return EquipmentModelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentModel object to this object
	 * through the EquipmentModel foreign key attribute
	 *
	 * @param      EquipmentModel $l EquipmentModel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentModelRelatedByManufacturerDocFileId(EquipmentModel $l)
	{
		$this->collEquipmentModelsRelatedByManufacturerDocFileId[] = $l;
		$l->setDataFileRelatedByManufacturerDocFileId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related EquipmentModelsRelatedByManufacturerDocFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getEquipmentModelsRelatedByManufacturerDocFileIdJoinEquipmentClass($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModelsRelatedByManufacturerDocFileId === null) {
			if ($this->isNew()) {
				$this->collEquipmentModelsRelatedByManufacturerDocFileId = array();
			} else {

				$criteria->add(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, $this->getId());

				$this->collEquipmentModelsRelatedByManufacturerDocFileId = EquipmentModelPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentModelPeer::MANUFACTURER_DOC_FILE_ID, $this->getId());

			if (!isset($this->lastEquipmentModelRelatedByManufacturerDocFileIdCriteria) || !$this->lastEquipmentModelRelatedByManufacturerDocFileIdCriteria->equals($criteria)) {
				$this->collEquipmentModelsRelatedByManufacturerDocFileId = EquipmentModelPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		}
		$this->lastEquipmentModelRelatedByManufacturerDocFileIdCriteria = $criteria;

		return $this->collEquipmentModelsRelatedByManufacturerDocFileId;
	}

	/**
	 * Temporary storage of collEquipmentModelsRelatedBySubcomponentsDocFileId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentModelsRelatedBySubcomponentsDocFileId()
	{
		if ($this->collEquipmentModelsRelatedBySubcomponentsDocFileId === null) {
			$this->collEquipmentModelsRelatedBySubcomponentsDocFileId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related EquipmentModelsRelatedBySubcomponentsDocFileId from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentModelsRelatedBySubcomponentsDocFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModelsRelatedBySubcomponentsDocFileId === null) {
			if ($this->isNew()) {
			   $this->collEquipmentModelsRelatedBySubcomponentsDocFileId = array();
			} else {

				$criteria->add(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				$this->collEquipmentModelsRelatedBySubcomponentsDocFileId = EquipmentModelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentModelRelatedBySubcomponentsDocFileIdCriteria) || !$this->lastEquipmentModelRelatedBySubcomponentsDocFileIdCriteria->equals($criteria)) {
					$this->collEquipmentModelsRelatedBySubcomponentsDocFileId = EquipmentModelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentModelRelatedBySubcomponentsDocFileIdCriteria = $criteria;
		return $this->collEquipmentModelsRelatedBySubcomponentsDocFileId;
	}

	/**
	 * Returns the number of related EquipmentModelsRelatedBySubcomponentsDocFileId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentModelsRelatedBySubcomponentsDocFileId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, $this->getId());

		return EquipmentModelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentModel object to this object
	 * through the EquipmentModel foreign key attribute
	 *
	 * @param      EquipmentModel $l EquipmentModel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentModelRelatedBySubcomponentsDocFileId(EquipmentModel $l)
	{
		$this->collEquipmentModelsRelatedBySubcomponentsDocFileId[] = $l;
		$l->setDataFileRelatedBySubcomponentsDocFileId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related EquipmentModelsRelatedBySubcomponentsDocFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getEquipmentModelsRelatedBySubcomponentsDocFileIdJoinEquipmentClass($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModelsRelatedBySubcomponentsDocFileId === null) {
			if ($this->isNew()) {
				$this->collEquipmentModelsRelatedBySubcomponentsDocFileId = array();
			} else {

				$criteria->add(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, $this->getId());

				$this->collEquipmentModelsRelatedBySubcomponentsDocFileId = EquipmentModelPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentModelPeer::SUBCOMPONENTS_DOC_FILE_ID, $this->getId());

			if (!isset($this->lastEquipmentModelRelatedBySubcomponentsDocFileIdCriteria) || !$this->lastEquipmentModelRelatedBySubcomponentsDocFileIdCriteria->equals($criteria)) {
				$this->collEquipmentModelsRelatedBySubcomponentsDocFileId = EquipmentModelPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		}
		$this->lastEquipmentModelRelatedBySubcomponentsDocFileIdCriteria = $criteria;

		return $this->collEquipmentModelsRelatedBySubcomponentsDocFileId;
	}

	/**
	 * Temporary storage of collEquipmentModelsRelatedByDesignConsiderationFileId to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initEquipmentModelsRelatedByDesignConsiderationFileId()
	{
		if ($this->collEquipmentModelsRelatedByDesignConsiderationFileId === null) {
			$this->collEquipmentModelsRelatedByDesignConsiderationFileId = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related EquipmentModelsRelatedByDesignConsiderationFileId from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getEquipmentModelsRelatedByDesignConsiderationFileId($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModelsRelatedByDesignConsiderationFileId === null) {
			if ($this->isNew()) {
			   $this->collEquipmentModelsRelatedByDesignConsiderationFileId = array();
			} else {

				$criteria->add(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				$this->collEquipmentModelsRelatedByDesignConsiderationFileId = EquipmentModelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, $this->getId());

				EquipmentModelPeer::addSelectColumns($criteria);
				if (!isset($this->lastEquipmentModelRelatedByDesignConsiderationFileIdCriteria) || !$this->lastEquipmentModelRelatedByDesignConsiderationFileIdCriteria->equals($criteria)) {
					$this->collEquipmentModelsRelatedByDesignConsiderationFileId = EquipmentModelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEquipmentModelRelatedByDesignConsiderationFileIdCriteria = $criteria;
		return $this->collEquipmentModelsRelatedByDesignConsiderationFileId;
	}

	/**
	 * Returns the number of related EquipmentModelsRelatedByDesignConsiderationFileId.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countEquipmentModelsRelatedByDesignConsiderationFileId($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, $this->getId());

		return EquipmentModelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a EquipmentModel object to this object
	 * through the EquipmentModel foreign key attribute
	 *
	 * @param      EquipmentModel $l EquipmentModel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEquipmentModelRelatedByDesignConsiderationFileId(EquipmentModel $l)
	{
		$this->collEquipmentModelsRelatedByDesignConsiderationFileId[] = $l;
		$l->setDataFileRelatedByDesignConsiderationFileId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related EquipmentModelsRelatedByDesignConsiderationFileId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getEquipmentModelsRelatedByDesignConsiderationFileIdJoinEquipmentClass($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseEquipmentModelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEquipmentModelsRelatedByDesignConsiderationFileId === null) {
			if ($this->isNew()) {
				$this->collEquipmentModelsRelatedByDesignConsiderationFileId = array();
			} else {

				$criteria->add(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, $this->getId());

				$this->collEquipmentModelsRelatedByDesignConsiderationFileId = EquipmentModelPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EquipmentModelPeer::DESIGN_CONSIDERATION_FILE_ID, $this->getId());

			if (!isset($this->lastEquipmentModelRelatedByDesignConsiderationFileIdCriteria) || !$this->lastEquipmentModelRelatedByDesignConsiderationFileIdCriteria->equals($criteria)) {
				$this->collEquipmentModelsRelatedByDesignConsiderationFileId = EquipmentModelPeer::doSelectJoinEquipmentClass($criteria, $con);
			}
		}
		$this->lastEquipmentModelRelatedByDesignConsiderationFileIdCriteria = $criteria;

		return $this->collEquipmentModelsRelatedByDesignConsiderationFileId;
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
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 * If this DataFile is new, it will return
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

				$criteria->add(FacilityDataFilePeer::DATA_FILE_ID, $this->getId());

				FacilityDataFilePeer::addSelectColumns($criteria);
				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(FacilityDataFilePeer::DATA_FILE_ID, $this->getId());

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

		$criteria->add(FacilityDataFilePeer::DATA_FILE_ID, $this->getId());

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
		$l->setDataFile($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
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

				$criteria->add(FacilityDataFilePeer::DATA_FILE_ID, $this->getId());

				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDocumentFormat($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(FacilityDataFilePeer::DATA_FILE_ID, $this->getId());

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
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
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

				$criteria->add(FacilityDataFilePeer::DATA_FILE_ID, $this->getId());

				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDocumentType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(FacilityDataFilePeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastFacilityDataFileCriteria) || !$this->lastFacilityDataFileCriteria->equals($criteria)) {
				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinDocumentType($criteria, $con);
			}
		}
		$this->lastFacilityDataFileCriteria = $criteria;

		return $this->collFacilityDataFiles;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related FacilityDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getFacilityDataFilesJoinOrganization($criteria = null, $con = null)
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

				$criteria->add(FacilityDataFilePeer::DATA_FILE_ID, $this->getId());

				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinOrganization($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(FacilityDataFilePeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastFacilityDataFileCriteria) || !$this->lastFacilityDataFileCriteria->equals($criteria)) {
				$this->collFacilityDataFiles = FacilityDataFilePeer::doSelectJoinOrganization($criteria, $con);
			}
		}
		$this->lastFacilityDataFileCriteria = $criteria;

		return $this->collFacilityDataFiles;
	}

	/**
	 * Temporary storage of collMaterialFiles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initMaterialFiles()
	{
		if ($this->collMaterialFiles === null) {
			$this->collMaterialFiles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related MaterialFiles from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getMaterialFiles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialFiles === null) {
			if ($this->isNew()) {
			   $this->collMaterialFiles = array();
			} else {

				$criteria->add(MaterialFilePeer::DATA_FILE_ID, $this->getId());

				MaterialFilePeer::addSelectColumns($criteria);
				$this->collMaterialFiles = MaterialFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MaterialFilePeer::DATA_FILE_ID, $this->getId());

				MaterialFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastMaterialFileCriteria) || !$this->lastMaterialFileCriteria->equals($criteria)) {
					$this->collMaterialFiles = MaterialFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMaterialFileCriteria = $criteria;
		return $this->collMaterialFiles;
	}

	/**
	 * Returns the number of related MaterialFiles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countMaterialFiles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(MaterialFilePeer::DATA_FILE_ID, $this->getId());

		return MaterialFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a MaterialFile object to this object
	 * through the MaterialFile foreign key attribute
	 *
	 * @param      MaterialFile $l MaterialFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMaterialFile(MaterialFile $l)
	{
		$this->collMaterialFiles[] = $l;
		$l->setDataFile($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related MaterialFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getMaterialFilesJoinMaterial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMaterialFiles === null) {
			if ($this->isNew()) {
				$this->collMaterialFiles = array();
			} else {

				$criteria->add(MaterialFilePeer::DATA_FILE_ID, $this->getId());

				$this->collMaterialFiles = MaterialFilePeer::doSelectJoinMaterial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(MaterialFilePeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastMaterialFileCriteria) || !$this->lastMaterialFileCriteria->equals($criteria)) {
				$this->collMaterialFiles = MaterialFilePeer::doSelectJoinMaterial($criteria, $con);
			}
		}
		$this->lastMaterialFileCriteria = $criteria;

		return $this->collMaterialFiles;
	}

	/**
	 * Temporary storage of collSensorModelDataFiles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSensorModelDataFiles()
	{
		if ($this->collSensorModelDataFiles === null) {
			$this->collSensorModelDataFiles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related SensorModelDataFiles from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSensorModelDataFiles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModelDataFiles === null) {
			if ($this->isNew()) {
			   $this->collSensorModelDataFiles = array();
			} else {

				$criteria->add(SensorModelDataFilePeer::DATA_FILE_ID, $this->getId());

				SensorModelDataFilePeer::addSelectColumns($criteria);
				$this->collSensorModelDataFiles = SensorModelDataFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SensorModelDataFilePeer::DATA_FILE_ID, $this->getId());

				SensorModelDataFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastSensorModelDataFileCriteria) || !$this->lastSensorModelDataFileCriteria->equals($criteria)) {
					$this->collSensorModelDataFiles = SensorModelDataFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSensorModelDataFileCriteria = $criteria;
		return $this->collSensorModelDataFiles;
	}

	/**
	 * Returns the number of related SensorModelDataFiles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSensorModelDataFiles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SensorModelDataFilePeer::DATA_FILE_ID, $this->getId());

		return SensorModelDataFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SensorModelDataFile object to this object
	 * through the SensorModelDataFile foreign key attribute
	 *
	 * @param      SensorModelDataFile $l SensorModelDataFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSensorModelDataFile(SensorModelDataFile $l)
	{
		$this->collSensorModelDataFiles[] = $l;
		$l->setDataFile($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related SensorModelDataFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getSensorModelDataFilesJoinSensorModel($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSensorModelDataFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSensorModelDataFiles === null) {
			if ($this->isNew()) {
				$this->collSensorModelDataFiles = array();
			} else {

				$criteria->add(SensorModelDataFilePeer::DATA_FILE_ID, $this->getId());

				$this->collSensorModelDataFiles = SensorModelDataFilePeer::doSelectJoinSensorModel($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SensorModelDataFilePeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastSensorModelDataFileCriteria) || !$this->lastSensorModelDataFileCriteria->equals($criteria)) {
				$this->collSensorModelDataFiles = SensorModelDataFilePeer::doSelectJoinSensorModel($criteria, $con);
			}
		}
		$this->lastSensorModelDataFileCriteria = $criteria;

		return $this->collSensorModelDataFiles;
	}

	/**
	 * Temporary storage of collThumbnails to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initThumbnails()
	{
		if ($this->collThumbnails === null) {
			$this->collThumbnails = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related Thumbnails from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getThumbnails($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseThumbnailPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collThumbnails === null) {
			if ($this->isNew()) {
			   $this->collThumbnails = array();
			} else {

				$criteria->add(ThumbnailPeer::DATAFILE_ID, $this->getId());

				ThumbnailPeer::addSelectColumns($criteria);
				$this->collThumbnails = ThumbnailPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ThumbnailPeer::DATAFILE_ID, $this->getId());

				ThumbnailPeer::addSelectColumns($criteria);
				if (!isset($this->lastThumbnailCriteria) || !$this->lastThumbnailCriteria->equals($criteria)) {
					$this->collThumbnails = ThumbnailPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastThumbnailCriteria = $criteria;
		return $this->collThumbnails;
	}

	/**
	 * Returns the number of related Thumbnails.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countThumbnails($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseThumbnailPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ThumbnailPeer::DATAFILE_ID, $this->getId());

		return ThumbnailPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Thumbnail object to this object
	 * through the Thumbnail foreign key attribute
	 *
	 * @param      Thumbnail $l Thumbnail
	 * @return     void
	 * @throws     PropelException
	 */
	public function addThumbnail(Thumbnail $l)
	{
		$this->collThumbnails[] = $l;
		$l->setDataFile($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related Thumbnails from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getThumbnailsJoinEntityType($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseThumbnailPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collThumbnails === null) {
			if ($this->isNew()) {
				$this->collThumbnails = array();
			} else {

				$criteria->add(ThumbnailPeer::DATAFILE_ID, $this->getId());

				$this->collThumbnails = ThumbnailPeer::doSelectJoinEntityType($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ThumbnailPeer::DATAFILE_ID, $this->getId());

			if (!isset($this->lastThumbnailCriteria) || !$this->lastThumbnailCriteria->equals($criteria)) {
				$this->collThumbnails = ThumbnailPeer::doSelectJoinEntityType($criteria, $con);
			}
		}
		$this->lastThumbnailCriteria = $criteria;

		return $this->collThumbnails;
	}

	/**
	 * Temporary storage of collTrials to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTrials()
	{
		if ($this->collTrials === null) {
			$this->collTrials = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related Trials from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTrials($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseTrialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTrials === null) {
			if ($this->isNew()) {
			   $this->collTrials = array();
			} else {

				$criteria->add(TrialPeer::MOTION_FILE_ID, $this->getId());

				TrialPeer::addSelectColumns($criteria);
				$this->collTrials = TrialPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TrialPeer::MOTION_FILE_ID, $this->getId());

				TrialPeer::addSelectColumns($criteria);
				if (!isset($this->lastTrialCriteria) || !$this->lastTrialCriteria->equals($criteria)) {
					$this->collTrials = TrialPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTrialCriteria = $criteria;
		return $this->collTrials;
	}

	/**
	 * Returns the number of related Trials.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTrials($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseTrialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TrialPeer::MOTION_FILE_ID, $this->getId());

		return TrialPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a Trial object to this object
	 * through the Trial foreign key attribute
	 *
	 * @param      Trial $l Trial
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTrial(Trial $l)
	{
		$this->collTrials[] = $l;
		$l->setDataFile($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related Trials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getTrialsJoinExperiment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseTrialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTrials === null) {
			if ($this->isNew()) {
				$this->collTrials = array();
			} else {

				$criteria->add(TrialPeer::MOTION_FILE_ID, $this->getId());

				$this->collTrials = TrialPeer::doSelectJoinExperiment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(TrialPeer::MOTION_FILE_ID, $this->getId());

			if (!isset($this->lastTrialCriteria) || !$this->lastTrialCriteria->equals($criteria)) {
				$this->collTrials = TrialPeer::doSelectJoinExperiment($criteria, $con);
			}
		}
		$this->lastTrialCriteria = $criteria;

		return $this->collTrials;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related Trials from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getTrialsJoinMeasurementUnit($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseTrialPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTrials === null) {
			if ($this->isNew()) {
				$this->collTrials = array();
			} else {

				$criteria->add(TrialPeer::MOTION_FILE_ID, $this->getId());

				$this->collTrials = TrialPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(TrialPeer::MOTION_FILE_ID, $this->getId());

			if (!isset($this->lastTrialCriteria) || !$this->lastTrialCriteria->equals($criteria)) {
				$this->collTrials = TrialPeer::doSelectJoinMeasurementUnit($criteria, $con);
			}
		}
		$this->lastTrialCriteria = $criteria;

		return $this->collTrials;
	}

	/**
	 * Temporary storage of collProjectHomepages to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initProjectHomepages()
	{
		if ($this->collProjectHomepages === null) {
			$this->collProjectHomepages = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related ProjectHomepages from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getProjectHomepages($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectHomepagePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjectHomepages === null) {
			if ($this->isNew()) {
			   $this->collProjectHomepages = array();
			} else {

				$criteria->add(ProjectHomepagePeer::DATA_FILE_ID, $this->getId());

				ProjectHomepagePeer::addSelectColumns($criteria);
				$this->collProjectHomepages = ProjectHomepagePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ProjectHomepagePeer::DATA_FILE_ID, $this->getId());

				ProjectHomepagePeer::addSelectColumns($criteria);
				if (!isset($this->lastProjectHomepageCriteria) || !$this->lastProjectHomepageCriteria->equals($criteria)) {
					$this->collProjectHomepages = ProjectHomepagePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastProjectHomepageCriteria = $criteria;
		return $this->collProjectHomepages;
	}

	/**
	 * Returns the number of related ProjectHomepages.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countProjectHomepages($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectHomepagePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ProjectHomepagePeer::DATA_FILE_ID, $this->getId());

		return ProjectHomepagePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ProjectHomepage object to this object
	 * through the ProjectHomepage foreign key attribute
	 *
	 * @param      ProjectHomepage $l ProjectHomepage
	 * @return     void
	 * @throws     PropelException
	 */
	public function addProjectHomepage(ProjectHomepage $l)
	{
		$this->collProjectHomepages[] = $l;
		$l->setDataFile($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related ProjectHomepages from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getProjectHomepagesJoinProject($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseProjectHomepagePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collProjectHomepages === null) {
			if ($this->isNew()) {
				$this->collProjectHomepages = array();
			} else {

				$criteria->add(ProjectHomepagePeer::DATA_FILE_ID, $this->getId());

				$this->collProjectHomepages = ProjectHomepagePeer::doSelectJoinProject($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ProjectHomepagePeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastProjectHomepageCriteria) || !$this->lastProjectHomepageCriteria->equals($criteria)) {
				$this->collProjectHomepages = ProjectHomepagePeer::doSelectJoinProject($criteria, $con);
			}
		}
		$this->lastProjectHomepageCriteria = $criteria;

		return $this->collProjectHomepages;
	}

	/**
	 * Temporary storage of collSpecimenComponentMaterialFiles to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initSpecimenComponentMaterialFiles()
	{
		if ($this->collSpecimenComponentMaterialFiles === null) {
			$this->collSpecimenComponentMaterialFiles = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialFiles from storage.
	 * If this DataFile is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getSpecimenComponentMaterialFiles($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialFiles === null) {
			if ($this->isNew()) {
			   $this->collSpecimenComponentMaterialFiles = array();
			} else {

				$criteria->add(SpecimenComponentMaterialFilePeer::DATA_FILE_ID, $this->getId());

				SpecimenComponentMaterialFilePeer::addSelectColumns($criteria);
				$this->collSpecimenComponentMaterialFiles = SpecimenComponentMaterialFilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SpecimenComponentMaterialFilePeer::DATA_FILE_ID, $this->getId());

				SpecimenComponentMaterialFilePeer::addSelectColumns($criteria);
				if (!isset($this->lastSpecimenComponentMaterialFileCriteria) || !$this->lastSpecimenComponentMaterialFileCriteria->equals($criteria)) {
					$this->collSpecimenComponentMaterialFiles = SpecimenComponentMaterialFilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSpecimenComponentMaterialFileCriteria = $criteria;
		return $this->collSpecimenComponentMaterialFiles;
	}

	/**
	 * Returns the number of related SpecimenComponentMaterialFiles.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countSpecimenComponentMaterialFiles($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SpecimenComponentMaterialFilePeer::DATA_FILE_ID, $this->getId());

		return SpecimenComponentMaterialFilePeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a SpecimenComponentMaterialFile object to this object
	 * through the SpecimenComponentMaterialFile foreign key attribute
	 *
	 * @param      SpecimenComponentMaterialFile $l SpecimenComponentMaterialFile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSpecimenComponentMaterialFile(SpecimenComponentMaterialFile $l)
	{
		$this->collSpecimenComponentMaterialFiles[] = $l;
		$l->setDataFile($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this DataFile is new, it will return
	 * an empty collection; or if this DataFile has previously
	 * been saved, it will retrieve related SpecimenComponentMaterialFiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in DataFile.
	 */
	public function getSpecimenComponentMaterialFilesJoinSpecimenComponentMaterial($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseSpecimenComponentMaterialFilePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSpecimenComponentMaterialFiles === null) {
			if ($this->isNew()) {
				$this->collSpecimenComponentMaterialFiles = array();
			} else {

				$criteria->add(SpecimenComponentMaterialFilePeer::DATA_FILE_ID, $this->getId());

				$this->collSpecimenComponentMaterialFiles = SpecimenComponentMaterialFilePeer::doSelectJoinSpecimenComponentMaterial($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(SpecimenComponentMaterialFilePeer::DATA_FILE_ID, $this->getId());

			if (!isset($this->lastSpecimenComponentMaterialFileCriteria) || !$this->lastSpecimenComponentMaterialFileCriteria->equals($criteria)) {
				$this->collSpecimenComponentMaterialFiles = SpecimenComponentMaterialFilePeer::doSelectJoinSpecimenComponentMaterial($criteria, $con);
			}
		}
		$this->lastSpecimenComponentMaterialFileCriteria = $criteria;

		return $this->collSpecimenComponentMaterialFiles;
	}

} // BaseDataFile
