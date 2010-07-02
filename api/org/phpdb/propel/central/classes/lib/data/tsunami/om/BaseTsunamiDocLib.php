<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/tsunami/TsunamiDocLibPeer.php';

/**
 * Base class that represents a row from the 'TSUNAMI_DOC_LIB' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiDocLib extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        TsunamiDocLibPeer
	 */
	protected static $peer;


	/**
	 * The value for the tsunami_doc_lib_id field.
	 * @var        double
	 */
	protected $tsunami_doc_lib_id;


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
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the dirty field.
	 * @var        double
	 */
	protected $dirty;


	/**
	 * The value for the file_location field.
	 * @var        string
	 */
	protected $file_location;


	/**
	 * The value for the file_size field.
	 * @var        double
	 */
	protected $file_size;


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
	 * The value for the specific_lat field.
	 * @var        double
	 */
	protected $specific_lat;


	/**
	 * The value for the specific_lon field.
	 * @var        double
	 */
	protected $specific_lon;


	/**
	 * The value for the start_date field.
	 * @var        int
	 */
	protected $start_date;


	/**
	 * The value for the title field.
	 * @var        string
	 */
	protected $title;


	/**
	 * The value for the tsunami_project_id field.
	 * @var        double
	 */
	protected $tsunami_project_id;


	/**
	 * The value for the type_of_material field.
	 * @var        string
	 */
	protected $type_of_material;

	/**
	 * @var        TsunamiProject
	 */
	protected $aTsunamiProject;

	/**
	 * Collection to store aggregation of collTsunamiBiologicalDatas.
	 * @var        array
	 */
	protected $collTsunamiBiologicalDatas;

	/**
	 * The criteria used to select the current contents of collTsunamiBiologicalDatas.
	 * @var        Criteria
	 */
	protected $lastTsunamiBiologicalDataCriteria = null;

	/**
	 * Collection to store aggregation of collTsunamiEngineeringDatas.
	 * @var        array
	 */
	protected $collTsunamiEngineeringDatas;

	/**
	 * The criteria used to select the current contents of collTsunamiEngineeringDatas.
	 * @var        Criteria
	 */
	protected $lastTsunamiEngineeringDataCriteria = null;

	/**
	 * Collection to store aggregation of collTsunamiGeologicalDatas.
	 * @var        array
	 */
	protected $collTsunamiGeologicalDatas;

	/**
	 * The criteria used to select the current contents of collTsunamiGeologicalDatas.
	 * @var        Criteria
	 */
	protected $lastTsunamiGeologicalDataCriteria = null;

	/**
	 * Collection to store aggregation of collTsunamiHydrodynamicDatas.
	 * @var        array
	 */
	protected $collTsunamiHydrodynamicDatas;

	/**
	 * The criteria used to select the current contents of collTsunamiHydrodynamicDatas.
	 * @var        Criteria
	 */
	protected $lastTsunamiHydrodynamicDataCriteria = null;

	/**
	 * Collection to store aggregation of collTsunamiSeismicDatas.
	 * @var        array
	 */
	protected $collTsunamiSeismicDatas;

	/**
	 * The criteria used to select the current contents of collTsunamiSeismicDatas.
	 * @var        Criteria
	 */
	protected $lastTsunamiSeismicDataCriteria = null;

	/**
	 * Collection to store aggregation of collTsunamiSiteConfigurations.
	 * @var        array
	 */
	protected $collTsunamiSiteConfigurations;

	/**
	 * The criteria used to select the current contents of collTsunamiSiteConfigurations.
	 * @var        Criteria
	 */
	protected $lastTsunamiSiteConfigurationCriteria = null;

	/**
	 * Collection to store aggregation of collTsunamiSiteDocRelationships.
	 * @var        array
	 */
	protected $collTsunamiSiteDocRelationships;

	/**
	 * The criteria used to select the current contents of collTsunamiSiteDocRelationships.
	 * @var        Criteria
	 */
	protected $lastTsunamiSiteDocRelationshipCriteria = null;

	/**
	 * Collection to store aggregation of collTsunamiSocialScienceDatas.
	 * @var        array
	 */
	protected $collTsunamiSocialScienceDatas;

	/**
	 * The criteria used to select the current contents of collTsunamiSocialScienceDatas.
	 * @var        Criteria
	 */
	protected $lastTsunamiSocialScienceDataCriteria = null;

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
	 * Get the [tsunami_doc_lib_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->tsunami_doc_lib_id;
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
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
	}

	/**
	 * Get the [dirty] column value.
	 * 
	 * @return     double
	 */
	public function getDirty()
	{

		return $this->dirty;
	}

	/**
	 * Get the [file_location] column value.
	 * 
	 * @return     string
	 */
	public function getFileLocation()
	{

		return $this->file_location;
	}

	/**
	 * Get the [file_size] column value.
	 * 
	 * @return     double
	 */
	public function getFileSize()
	{

		return $this->file_size;
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
	 * Get the [specific_lat] column value.
	 * 
	 * @return     double
	 */
	public function getSpecificLatitude()
	{

		return $this->specific_lat;
	}

	/**
	 * Get the [specific_lon] column value.
	 * 
	 * @return     double
	 */
	public function getSpecificLongitude()
	{

		return $this->specific_lon;
	}

	/**
	 * Get the [optionally formatted] [start_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getStartDate($format = '%Y-%m-%d')
	{

		if ($this->start_date === null || $this->start_date === '') {
			return null;
		} elseif (!is_int($this->start_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->start_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [start_date] as date/time value: " . var_export($this->start_date, true));
			}
		} else {
			$ts = $this->start_date;
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
	 * Get the [title] column value.
	 * 
	 * @return     string
	 */
	public function getTitle()
	{

		return $this->title;
	}

	/**
	 * Get the [tsunami_project_id] column value.
	 * 
	 * @return     double
	 */
	public function getTsunamiProjectId()
	{

		return $this->tsunami_project_id;
	}

	/**
	 * Get the [type_of_material] column value.
	 * 
	 * @return     string
	 */
	public function getTypeOfMaterial()
	{

		return $this->type_of_material;
	}

	/**
	 * Set the value of [tsunami_doc_lib_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->tsunami_doc_lib_id !== $v) {
			$this->tsunami_doc_lib_id = $v;
			$this->modifiedColumns[] = TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID;
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
			$this->modifiedColumns[] = TsunamiDocLibPeer::AUTHOR_EMAILS;
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
			$this->modifiedColumns[] = TsunamiDocLibPeer::AUTHORS;
		}

	} // setAuthors()

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
			$this->modifiedColumns[] = TsunamiDocLibPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [dirty] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setDirty($v)
	{

		if ($this->dirty !== $v) {
			$this->dirty = $v;
			$this->modifiedColumns[] = TsunamiDocLibPeer::DIRTY;
		}

	} // setDirty()

	/**
	 * Set the value of [file_location] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFileLocation($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->file_location !== $v) {
			$this->file_location = $v;
			$this->modifiedColumns[] = TsunamiDocLibPeer::FILE_LOCATION;
		}

	} // setFileLocation()

	/**
	 * Set the value of [file_size] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setFileSize($v)
	{

		if ($this->file_size !== $v) {
			$this->file_size = $v;
			$this->modifiedColumns[] = TsunamiDocLibPeer::FILE_SIZE;
		}

	} // setFileSize()

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
			$this->modifiedColumns[] = TsunamiDocLibPeer::HOW_TO_CITE;
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
			$this->modifiedColumns[] = TsunamiDocLibPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [specific_lat] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSpecificLatitude($v)
	{

		if ($this->specific_lat !== $v) {
			$this->specific_lat = $v;
			$this->modifiedColumns[] = TsunamiDocLibPeer::SPECIFIC_LAT;
		}

	} // setSpecificLatitude()

	/**
	 * Set the value of [specific_lon] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSpecificLongitude($v)
	{

		if ($this->specific_lon !== $v) {
			$this->specific_lon = $v;
			$this->modifiedColumns[] = TsunamiDocLibPeer::SPECIFIC_LON;
		}

	} // setSpecificLongitude()

	/**
	 * Set the value of [start_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setStartDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [start_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->start_date !== $ts) {
			$this->start_date = $ts;
			$this->modifiedColumns[] = TsunamiDocLibPeer::START_DATE;
		}

	} // setStartDate()

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
			$this->modifiedColumns[] = TsunamiDocLibPeer::TITLE;
		}

	} // setTitle()

	/**
	 * Set the value of [tsunami_project_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTsunamiProjectId($v)
	{

		if ($this->tsunami_project_id !== $v) {
			$this->tsunami_project_id = $v;
			$this->modifiedColumns[] = TsunamiDocLibPeer::TSUNAMI_PROJECT_ID;
		}

		if ($this->aTsunamiProject !== null && $this->aTsunamiProject->getId() !== $v) {
			$this->aTsunamiProject = null;
		}

	} // setTsunamiProjectId()

	/**
	 * Set the value of [type_of_material] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTypeOfMaterial($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->type_of_material !== $v) {
			$this->type_of_material = $v;
			$this->modifiedColumns[] = TsunamiDocLibPeer::TYPE_OF_MATERIAL;
		}

	} // setTypeOfMaterial()

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

			$this->tsunami_doc_lib_id = $rs->getFloat($startcol + 0);

			$this->author_emails = $rs->getString($startcol + 1);

			$this->authors = $rs->getString($startcol + 2);

			$this->description = $rs->getClob($startcol + 3);

			$this->dirty = $rs->getFloat($startcol + 4);

			$this->file_location = $rs->getString($startcol + 5);

			$this->file_size = $rs->getFloat($startcol + 6);

			$this->how_to_cite = $rs->getString($startcol + 7);

			$this->name = $rs->getString($startcol + 8);

			$this->specific_lat = $rs->getFloat($startcol + 9);

			$this->specific_lon = $rs->getFloat($startcol + 10);

			$this->start_date = $rs->getDate($startcol + 11, null);

			$this->title = $rs->getString($startcol + 12);

			$this->tsunami_project_id = $rs->getFloat($startcol + 13);

			$this->type_of_material = $rs->getString($startcol + 14);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 15; // 15 = TsunamiDocLibPeer::NUM_COLUMNS - TsunamiDocLibPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating TsunamiDocLib object", $e);
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
			$con = Propel::getConnection(TsunamiDocLibPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			TsunamiDocLibPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(TsunamiDocLibPeer::DATABASE_NAME);
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

			if ($this->aTsunamiProject !== null) {
				if ($this->aTsunamiProject->isModified()) {
					$affectedRows += $this->aTsunamiProject->save($con);
				}
				$this->setTsunamiProject($this->aTsunamiProject);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = TsunamiDocLibPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += TsunamiDocLibPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collTsunamiBiologicalDatas !== null) {
				foreach($this->collTsunamiBiologicalDatas as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collTsunamiEngineeringDatas !== null) {
				foreach($this->collTsunamiEngineeringDatas as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collTsunamiGeologicalDatas !== null) {
				foreach($this->collTsunamiGeologicalDatas as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collTsunamiHydrodynamicDatas !== null) {
				foreach($this->collTsunamiHydrodynamicDatas as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collTsunamiSeismicDatas !== null) {
				foreach($this->collTsunamiSeismicDatas as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collTsunamiSiteConfigurations !== null) {
				foreach($this->collTsunamiSiteConfigurations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collTsunamiSiteDocRelationships !== null) {
				foreach($this->collTsunamiSiteDocRelationships as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collTsunamiSocialScienceDatas !== null) {
				foreach($this->collTsunamiSocialScienceDatas as $referrerFK) {
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

			if ($this->aTsunamiProject !== null) {
				if (!$this->aTsunamiProject->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aTsunamiProject->getValidationFailures());
				}
			}


			if (($retval = TsunamiDocLibPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collTsunamiBiologicalDatas !== null) {
					foreach($this->collTsunamiBiologicalDatas as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collTsunamiEngineeringDatas !== null) {
					foreach($this->collTsunamiEngineeringDatas as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collTsunamiGeologicalDatas !== null) {
					foreach($this->collTsunamiGeologicalDatas as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collTsunamiHydrodynamicDatas !== null) {
					foreach($this->collTsunamiHydrodynamicDatas as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collTsunamiSeismicDatas !== null) {
					foreach($this->collTsunamiSeismicDatas as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collTsunamiSiteConfigurations !== null) {
					foreach($this->collTsunamiSiteConfigurations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collTsunamiSiteDocRelationships !== null) {
					foreach($this->collTsunamiSiteDocRelationships as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collTsunamiSocialScienceDatas !== null) {
					foreach($this->collTsunamiSocialScienceDatas as $referrerFK) {
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
		$pos = TsunamiDocLibPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDescription();
				break;
			case 4:
				return $this->getDirty();
				break;
			case 5:
				return $this->getFileLocation();
				break;
			case 6:
				return $this->getFileSize();
				break;
			case 7:
				return $this->getHowToCite();
				break;
			case 8:
				return $this->getName();
				break;
			case 9:
				return $this->getSpecificLatitude();
				break;
			case 10:
				return $this->getSpecificLongitude();
				break;
			case 11:
				return $this->getStartDate();
				break;
			case 12:
				return $this->getTitle();
				break;
			case 13:
				return $this->getTsunamiProjectId();
				break;
			case 14:
				return $this->getTypeOfMaterial();
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
		$keys = TsunamiDocLibPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getAuthorEmails(),
			$keys[2] => $this->getAuthors(),
			$keys[3] => $this->getDescription(),
			$keys[4] => $this->getDirty(),
			$keys[5] => $this->getFileLocation(),
			$keys[6] => $this->getFileSize(),
			$keys[7] => $this->getHowToCite(),
			$keys[8] => $this->getName(),
			$keys[9] => $this->getSpecificLatitude(),
			$keys[10] => $this->getSpecificLongitude(),
			$keys[11] => $this->getStartDate(),
			$keys[12] => $this->getTitle(),
			$keys[13] => $this->getTsunamiProjectId(),
			$keys[14] => $this->getTypeOfMaterial(),
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
		$pos = TsunamiDocLibPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDescription($value);
				break;
			case 4:
				$this->setDirty($value);
				break;
			case 5:
				$this->setFileLocation($value);
				break;
			case 6:
				$this->setFileSize($value);
				break;
			case 7:
				$this->setHowToCite($value);
				break;
			case 8:
				$this->setName($value);
				break;
			case 9:
				$this->setSpecificLatitude($value);
				break;
			case 10:
				$this->setSpecificLongitude($value);
				break;
			case 11:
				$this->setStartDate($value);
				break;
			case 12:
				$this->setTitle($value);
				break;
			case 13:
				$this->setTsunamiProjectId($value);
				break;
			case 14:
				$this->setTypeOfMaterial($value);
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
		$keys = TsunamiDocLibPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAuthorEmails($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAuthors($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDescription($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDirty($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setFileLocation($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setFileSize($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setHowToCite($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setName($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setSpecificLatitude($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setSpecificLongitude($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setStartDate($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setTitle($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setTsunamiProjectId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setTypeOfMaterial($arr[$keys[14]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(TsunamiDocLibPeer::DATABASE_NAME);

		if ($this->isColumnModified(TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID)) $criteria->add(TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID, $this->tsunami_doc_lib_id);
		if ($this->isColumnModified(TsunamiDocLibPeer::AUTHOR_EMAILS)) $criteria->add(TsunamiDocLibPeer::AUTHOR_EMAILS, $this->author_emails);
		if ($this->isColumnModified(TsunamiDocLibPeer::AUTHORS)) $criteria->add(TsunamiDocLibPeer::AUTHORS, $this->authors);
		if ($this->isColumnModified(TsunamiDocLibPeer::DESCRIPTION)) $criteria->add(TsunamiDocLibPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(TsunamiDocLibPeer::DIRTY)) $criteria->add(TsunamiDocLibPeer::DIRTY, $this->dirty);
		if ($this->isColumnModified(TsunamiDocLibPeer::FILE_LOCATION)) $criteria->add(TsunamiDocLibPeer::FILE_LOCATION, $this->file_location);
		if ($this->isColumnModified(TsunamiDocLibPeer::FILE_SIZE)) $criteria->add(TsunamiDocLibPeer::FILE_SIZE, $this->file_size);
		if ($this->isColumnModified(TsunamiDocLibPeer::HOW_TO_CITE)) $criteria->add(TsunamiDocLibPeer::HOW_TO_CITE, $this->how_to_cite);
		if ($this->isColumnModified(TsunamiDocLibPeer::NAME)) $criteria->add(TsunamiDocLibPeer::NAME, $this->name);
		if ($this->isColumnModified(TsunamiDocLibPeer::SPECIFIC_LAT)) $criteria->add(TsunamiDocLibPeer::SPECIFIC_LAT, $this->specific_lat);
		if ($this->isColumnModified(TsunamiDocLibPeer::SPECIFIC_LON)) $criteria->add(TsunamiDocLibPeer::SPECIFIC_LON, $this->specific_lon);
		if ($this->isColumnModified(TsunamiDocLibPeer::START_DATE)) $criteria->add(TsunamiDocLibPeer::START_DATE, $this->start_date);
		if ($this->isColumnModified(TsunamiDocLibPeer::TITLE)) $criteria->add(TsunamiDocLibPeer::TITLE, $this->title);
		if ($this->isColumnModified(TsunamiDocLibPeer::TSUNAMI_PROJECT_ID)) $criteria->add(TsunamiDocLibPeer::TSUNAMI_PROJECT_ID, $this->tsunami_project_id);
		if ($this->isColumnModified(TsunamiDocLibPeer::TYPE_OF_MATERIAL)) $criteria->add(TsunamiDocLibPeer::TYPE_OF_MATERIAL, $this->type_of_material);

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
		$criteria = new Criteria(TsunamiDocLibPeer::DATABASE_NAME);

		$criteria->add(TsunamiDocLibPeer::TSUNAMI_DOC_LIB_ID, $this->tsunami_doc_lib_id);

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
	 * Generic method to set the primary key (tsunami_doc_lib_id column).
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
	 * @param      object $copyObj An object of TsunamiDocLib (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAuthorEmails($this->author_emails);

		$copyObj->setAuthors($this->authors);

		$copyObj->setDescription($this->description);

		$copyObj->setDirty($this->dirty);

		$copyObj->setFileLocation($this->file_location);

		$copyObj->setFileSize($this->file_size);

		$copyObj->setHowToCite($this->how_to_cite);

		$copyObj->setName($this->name);

		$copyObj->setSpecificLatitude($this->specific_lat);

		$copyObj->setSpecificLongitude($this->specific_lon);

		$copyObj->setStartDate($this->start_date);

		$copyObj->setTitle($this->title);

		$copyObj->setTsunamiProjectId($this->tsunami_project_id);

		$copyObj->setTypeOfMaterial($this->type_of_material);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getTsunamiBiologicalDatas() as $relObj) {
				$copyObj->addTsunamiBiologicalData($relObj->copy($deepCopy));
			}

			foreach($this->getTsunamiEngineeringDatas() as $relObj) {
				$copyObj->addTsunamiEngineeringData($relObj->copy($deepCopy));
			}

			foreach($this->getTsunamiGeologicalDatas() as $relObj) {
				$copyObj->addTsunamiGeologicalData($relObj->copy($deepCopy));
			}

			foreach($this->getTsunamiHydrodynamicDatas() as $relObj) {
				$copyObj->addTsunamiHydrodynamicData($relObj->copy($deepCopy));
			}

			foreach($this->getTsunamiSeismicDatas() as $relObj) {
				$copyObj->addTsunamiSeismicData($relObj->copy($deepCopy));
			}

			foreach($this->getTsunamiSiteConfigurations() as $relObj) {
				$copyObj->addTsunamiSiteConfiguration($relObj->copy($deepCopy));
			}

			foreach($this->getTsunamiSiteDocRelationships() as $relObj) {
				$copyObj->addTsunamiSiteDocRelationship($relObj->copy($deepCopy));
			}

			foreach($this->getTsunamiSocialScienceDatas() as $relObj) {
				$copyObj->addTsunamiSocialScienceData($relObj->copy($deepCopy));
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
	 * @return     TsunamiDocLib Clone of current object.
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
	 * @return     TsunamiDocLibPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new TsunamiDocLibPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a TsunamiProject object.
	 *
	 * @param      TsunamiProject $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setTsunamiProject($v)
	{


		if ($v === null) {
			$this->setTsunamiProjectId(NULL);
		} else {
			$this->setTsunamiProjectId($v->getId());
		}


		$this->aTsunamiProject = $v;
	}


	/**
	 * Get the associated TsunamiProject object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     TsunamiProject The associated TsunamiProject object.
	 * @throws     PropelException
	 */
	public function getTsunamiProject($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiProjectPeer.php';

		if ($this->aTsunamiProject === null && ($this->tsunami_project_id > 0)) {

			$this->aTsunamiProject = TsunamiProjectPeer::retrieveByPK($this->tsunami_project_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = TsunamiProjectPeer::retrieveByPK($this->tsunami_project_id, $con);
			   $obj->addTsunamiProjects($this);
			 */
		}
		return $this->aTsunamiProject;
	}

	/**
	 * Temporary storage of collTsunamiBiologicalDatas to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTsunamiBiologicalDatas()
	{
		if ($this->collTsunamiBiologicalDatas === null) {
			$this->collTsunamiBiologicalDatas = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiDocLib has previously
	 * been saved, it will retrieve related TsunamiBiologicalDatas from storage.
	 * If this TsunamiDocLib is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTsunamiBiologicalDatas($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiBiologicalDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTsunamiBiologicalDatas === null) {
			if ($this->isNew()) {
			   $this->collTsunamiBiologicalDatas = array();
			} else {

				$criteria->add(TsunamiBiologicalDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiBiologicalDataPeer::addSelectColumns($criteria);
				$this->collTsunamiBiologicalDatas = TsunamiBiologicalDataPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TsunamiBiologicalDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiBiologicalDataPeer::addSelectColumns($criteria);
				if (!isset($this->lastTsunamiBiologicalDataCriteria) || !$this->lastTsunamiBiologicalDataCriteria->equals($criteria)) {
					$this->collTsunamiBiologicalDatas = TsunamiBiologicalDataPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTsunamiBiologicalDataCriteria = $criteria;
		return $this->collTsunamiBiologicalDatas;
	}

	/**
	 * Returns the number of related TsunamiBiologicalDatas.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTsunamiBiologicalDatas($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiBiologicalDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TsunamiBiologicalDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

		return TsunamiBiologicalDataPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a TsunamiBiologicalData object to this object
	 * through the TsunamiBiologicalData foreign key attribute
	 *
	 * @param      TsunamiBiologicalData $l TsunamiBiologicalData
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTsunamiBiologicalData(TsunamiBiologicalData $l)
	{
		$this->collTsunamiBiologicalDatas[] = $l;
		$l->setTsunamiDocLib($this);
	}

	/**
	 * Temporary storage of collTsunamiEngineeringDatas to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTsunamiEngineeringDatas()
	{
		if ($this->collTsunamiEngineeringDatas === null) {
			$this->collTsunamiEngineeringDatas = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiDocLib has previously
	 * been saved, it will retrieve related TsunamiEngineeringDatas from storage.
	 * If this TsunamiDocLib is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTsunamiEngineeringDatas($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiEngineeringDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTsunamiEngineeringDatas === null) {
			if ($this->isNew()) {
			   $this->collTsunamiEngineeringDatas = array();
			} else {

				$criteria->add(TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiEngineeringDataPeer::addSelectColumns($criteria);
				$this->collTsunamiEngineeringDatas = TsunamiEngineeringDataPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiEngineeringDataPeer::addSelectColumns($criteria);
				if (!isset($this->lastTsunamiEngineeringDataCriteria) || !$this->lastTsunamiEngineeringDataCriteria->equals($criteria)) {
					$this->collTsunamiEngineeringDatas = TsunamiEngineeringDataPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTsunamiEngineeringDataCriteria = $criteria;
		return $this->collTsunamiEngineeringDatas;
	}

	/**
	 * Returns the number of related TsunamiEngineeringDatas.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTsunamiEngineeringDatas($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiEngineeringDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TsunamiEngineeringDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

		return TsunamiEngineeringDataPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a TsunamiEngineeringData object to this object
	 * through the TsunamiEngineeringData foreign key attribute
	 *
	 * @param      TsunamiEngineeringData $l TsunamiEngineeringData
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTsunamiEngineeringData(TsunamiEngineeringData $l)
	{
		$this->collTsunamiEngineeringDatas[] = $l;
		$l->setTsunamiDocLib($this);
	}

	/**
	 * Temporary storage of collTsunamiGeologicalDatas to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTsunamiGeologicalDatas()
	{
		if ($this->collTsunamiGeologicalDatas === null) {
			$this->collTsunamiGeologicalDatas = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiDocLib has previously
	 * been saved, it will retrieve related TsunamiGeologicalDatas from storage.
	 * If this TsunamiDocLib is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTsunamiGeologicalDatas($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiGeologicalDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTsunamiGeologicalDatas === null) {
			if ($this->isNew()) {
			   $this->collTsunamiGeologicalDatas = array();
			} else {

				$criteria->add(TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiGeologicalDataPeer::addSelectColumns($criteria);
				$this->collTsunamiGeologicalDatas = TsunamiGeologicalDataPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiGeologicalDataPeer::addSelectColumns($criteria);
				if (!isset($this->lastTsunamiGeologicalDataCriteria) || !$this->lastTsunamiGeologicalDataCriteria->equals($criteria)) {
					$this->collTsunamiGeologicalDatas = TsunamiGeologicalDataPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTsunamiGeologicalDataCriteria = $criteria;
		return $this->collTsunamiGeologicalDatas;
	}

	/**
	 * Returns the number of related TsunamiGeologicalDatas.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTsunamiGeologicalDatas($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiGeologicalDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TsunamiGeologicalDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

		return TsunamiGeologicalDataPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a TsunamiGeologicalData object to this object
	 * through the TsunamiGeologicalData foreign key attribute
	 *
	 * @param      TsunamiGeologicalData $l TsunamiGeologicalData
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTsunamiGeologicalData(TsunamiGeologicalData $l)
	{
		$this->collTsunamiGeologicalDatas[] = $l;
		$l->setTsunamiDocLib($this);
	}

	/**
	 * Temporary storage of collTsunamiHydrodynamicDatas to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTsunamiHydrodynamicDatas()
	{
		if ($this->collTsunamiHydrodynamicDatas === null) {
			$this->collTsunamiHydrodynamicDatas = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiDocLib has previously
	 * been saved, it will retrieve related TsunamiHydrodynamicDatas from storage.
	 * If this TsunamiDocLib is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTsunamiHydrodynamicDatas($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiHydrodynamicDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTsunamiHydrodynamicDatas === null) {
			if ($this->isNew()) {
			   $this->collTsunamiHydrodynamicDatas = array();
			} else {

				$criteria->add(TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiHydrodynamicDataPeer::addSelectColumns($criteria);
				$this->collTsunamiHydrodynamicDatas = TsunamiHydrodynamicDataPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiHydrodynamicDataPeer::addSelectColumns($criteria);
				if (!isset($this->lastTsunamiHydrodynamicDataCriteria) || !$this->lastTsunamiHydrodynamicDataCriteria->equals($criteria)) {
					$this->collTsunamiHydrodynamicDatas = TsunamiHydrodynamicDataPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTsunamiHydrodynamicDataCriteria = $criteria;
		return $this->collTsunamiHydrodynamicDatas;
	}

	/**
	 * Returns the number of related TsunamiHydrodynamicDatas.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTsunamiHydrodynamicDatas($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiHydrodynamicDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TsunamiHydrodynamicDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

		return TsunamiHydrodynamicDataPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a TsunamiHydrodynamicData object to this object
	 * through the TsunamiHydrodynamicData foreign key attribute
	 *
	 * @param      TsunamiHydrodynamicData $l TsunamiHydrodynamicData
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTsunamiHydrodynamicData(TsunamiHydrodynamicData $l)
	{
		$this->collTsunamiHydrodynamicDatas[] = $l;
		$l->setTsunamiDocLib($this);
	}

	/**
	 * Temporary storage of collTsunamiSeismicDatas to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTsunamiSeismicDatas()
	{
		if ($this->collTsunamiSeismicDatas === null) {
			$this->collTsunamiSeismicDatas = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiDocLib has previously
	 * been saved, it will retrieve related TsunamiSeismicDatas from storage.
	 * If this TsunamiDocLib is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTsunamiSeismicDatas($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiSeismicDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTsunamiSeismicDatas === null) {
			if ($this->isNew()) {
			   $this->collTsunamiSeismicDatas = array();
			} else {

				$criteria->add(TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiSeismicDataPeer::addSelectColumns($criteria);
				$this->collTsunamiSeismicDatas = TsunamiSeismicDataPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiSeismicDataPeer::addSelectColumns($criteria);
				if (!isset($this->lastTsunamiSeismicDataCriteria) || !$this->lastTsunamiSeismicDataCriteria->equals($criteria)) {
					$this->collTsunamiSeismicDatas = TsunamiSeismicDataPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTsunamiSeismicDataCriteria = $criteria;
		return $this->collTsunamiSeismicDatas;
	}

	/**
	 * Returns the number of related TsunamiSeismicDatas.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTsunamiSeismicDatas($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiSeismicDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TsunamiSeismicDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

		return TsunamiSeismicDataPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a TsunamiSeismicData object to this object
	 * through the TsunamiSeismicData foreign key attribute
	 *
	 * @param      TsunamiSeismicData $l TsunamiSeismicData
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTsunamiSeismicData(TsunamiSeismicData $l)
	{
		$this->collTsunamiSeismicDatas[] = $l;
		$l->setTsunamiDocLib($this);
	}

	/**
	 * Temporary storage of collTsunamiSiteConfigurations to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTsunamiSiteConfigurations()
	{
		if ($this->collTsunamiSiteConfigurations === null) {
			$this->collTsunamiSiteConfigurations = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiDocLib has previously
	 * been saved, it will retrieve related TsunamiSiteConfigurations from storage.
	 * If this TsunamiDocLib is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTsunamiSiteConfigurations($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiSiteConfigurationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTsunamiSiteConfigurations === null) {
			if ($this->isNew()) {
			   $this->collTsunamiSiteConfigurations = array();
			} else {

				$criteria->add(TsunamiSiteConfigurationPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiSiteConfigurationPeer::addSelectColumns($criteria);
				$this->collTsunamiSiteConfigurations = TsunamiSiteConfigurationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TsunamiSiteConfigurationPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiSiteConfigurationPeer::addSelectColumns($criteria);
				if (!isset($this->lastTsunamiSiteConfigurationCriteria) || !$this->lastTsunamiSiteConfigurationCriteria->equals($criteria)) {
					$this->collTsunamiSiteConfigurations = TsunamiSiteConfigurationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTsunamiSiteConfigurationCriteria = $criteria;
		return $this->collTsunamiSiteConfigurations;
	}

	/**
	 * Returns the number of related TsunamiSiteConfigurations.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTsunamiSiteConfigurations($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiSiteConfigurationPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TsunamiSiteConfigurationPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

		return TsunamiSiteConfigurationPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a TsunamiSiteConfiguration object to this object
	 * through the TsunamiSiteConfiguration foreign key attribute
	 *
	 * @param      TsunamiSiteConfiguration $l TsunamiSiteConfiguration
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTsunamiSiteConfiguration(TsunamiSiteConfiguration $l)
	{
		$this->collTsunamiSiteConfigurations[] = $l;
		$l->setTsunamiDocLib($this);
	}

	/**
	 * Temporary storage of collTsunamiSiteDocRelationships to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTsunamiSiteDocRelationships()
	{
		if ($this->collTsunamiSiteDocRelationships === null) {
			$this->collTsunamiSiteDocRelationships = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiDocLib has previously
	 * been saved, it will retrieve related TsunamiSiteDocRelationships from storage.
	 * If this TsunamiDocLib is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTsunamiSiteDocRelationships($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiSiteDocRelationshipPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTsunamiSiteDocRelationships === null) {
			if ($this->isNew()) {
			   $this->collTsunamiSiteDocRelationships = array();
			} else {

				$criteria->add(TsunamiSiteDocRelationshipPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiSiteDocRelationshipPeer::addSelectColumns($criteria);
				$this->collTsunamiSiteDocRelationships = TsunamiSiteDocRelationshipPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TsunamiSiteDocRelationshipPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiSiteDocRelationshipPeer::addSelectColumns($criteria);
				if (!isset($this->lastTsunamiSiteDocRelationshipCriteria) || !$this->lastTsunamiSiteDocRelationshipCriteria->equals($criteria)) {
					$this->collTsunamiSiteDocRelationships = TsunamiSiteDocRelationshipPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTsunamiSiteDocRelationshipCriteria = $criteria;
		return $this->collTsunamiSiteDocRelationships;
	}

	/**
	 * Returns the number of related TsunamiSiteDocRelationships.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTsunamiSiteDocRelationships($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiSiteDocRelationshipPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TsunamiSiteDocRelationshipPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

		return TsunamiSiteDocRelationshipPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a TsunamiSiteDocRelationship object to this object
	 * through the TsunamiSiteDocRelationship foreign key attribute
	 *
	 * @param      TsunamiSiteDocRelationship $l TsunamiSiteDocRelationship
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTsunamiSiteDocRelationship(TsunamiSiteDocRelationship $l)
	{
		$this->collTsunamiSiteDocRelationships[] = $l;
		$l->setTsunamiDocLib($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiDocLib is new, it will return
	 * an empty collection; or if this TsunamiDocLib has previously
	 * been saved, it will retrieve related TsunamiSiteDocRelationships from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in TsunamiDocLib.
	 */
	public function getTsunamiSiteDocRelationshipsJoinTsunamiSite($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiSiteDocRelationshipPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTsunamiSiteDocRelationships === null) {
			if ($this->isNew()) {
				$this->collTsunamiSiteDocRelationships = array();
			} else {

				$criteria->add(TsunamiSiteDocRelationshipPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				$this->collTsunamiSiteDocRelationships = TsunamiSiteDocRelationshipPeer::doSelectJoinTsunamiSite($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(TsunamiSiteDocRelationshipPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

			if (!isset($this->lastTsunamiSiteDocRelationshipCriteria) || !$this->lastTsunamiSiteDocRelationshipCriteria->equals($criteria)) {
				$this->collTsunamiSiteDocRelationships = TsunamiSiteDocRelationshipPeer::doSelectJoinTsunamiSite($criteria, $con);
			}
		}
		$this->lastTsunamiSiteDocRelationshipCriteria = $criteria;

		return $this->collTsunamiSiteDocRelationships;
	}

	/**
	 * Temporary storage of collTsunamiSocialScienceDatas to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initTsunamiSocialScienceDatas()
	{
		if ($this->collTsunamiSocialScienceDatas === null) {
			$this->collTsunamiSocialScienceDatas = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiDocLib has previously
	 * been saved, it will retrieve related TsunamiSocialScienceDatas from storage.
	 * If this TsunamiDocLib is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getTsunamiSocialScienceDatas($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiSocialScienceDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collTsunamiSocialScienceDatas === null) {
			if ($this->isNew()) {
			   $this->collTsunamiSocialScienceDatas = array();
			} else {

				$criteria->add(TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiSocialScienceDataPeer::addSelectColumns($criteria);
				$this->collTsunamiSocialScienceDatas = TsunamiSocialScienceDataPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

				TsunamiSocialScienceDataPeer::addSelectColumns($criteria);
				if (!isset($this->lastTsunamiSocialScienceDataCriteria) || !$this->lastTsunamiSocialScienceDataCriteria->equals($criteria)) {
					$this->collTsunamiSocialScienceDatas = TsunamiSocialScienceDataPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastTsunamiSocialScienceDataCriteria = $criteria;
		return $this->collTsunamiSocialScienceDatas;
	}

	/**
	 * Returns the number of related TsunamiSocialScienceDatas.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countTsunamiSocialScienceDatas($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/tsunami/om/BaseTsunamiSocialScienceDataPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(TsunamiSocialScienceDataPeer::TSUNAMI_DOC_LIB_ID, $this->getId());

		return TsunamiSocialScienceDataPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a TsunamiSocialScienceData object to this object
	 * through the TsunamiSocialScienceData foreign key attribute
	 *
	 * @param      TsunamiSocialScienceData $l TsunamiSocialScienceData
	 * @return     void
	 * @throws     PropelException
	 */
	public function addTsunamiSocialScienceData(TsunamiSocialScienceData $l)
	{
		$this->collTsunamiSocialScienceDatas[] = $l;
		$l->setTsunamiDocLib($this);
	}

} // BaseTsunamiDocLib
