<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/tsunami/TsunamiSitePeer.php';

/**
 * Base class that represents a row from the 'TSUNAMI_SITE' table.
 *
 * 
 *
 * @package    lib.data.tsunami.om
 */
abstract class BaseTsunamiSite extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        TsunamiSitePeer
	 */
	protected static $peer;


	/**
	 * The value for the tsunami_site_id field.
	 * @var        double
	 */
	protected $tsunami_site_id;


	/**
	 * The value for the bounding_polygon field.
	 * @var        string
	 */
	protected $bounding_polygon;


	/**
	 * The value for the country field.
	 * @var        string
	 */
	protected $country;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the site_lat field.
	 * @var        double
	 */
	protected $site_lat;


	/**
	 * The value for the site_lon field.
	 * @var        double
	 */
	protected $site_lon;


	/**
	 * The value for the tsunami_project_id field.
	 * @var        double
	 */
	protected $tsunami_project_id;


	/**
	 * The value for the type field.
	 * @var        string
	 */
	protected $type;

	/**
	 * @var        TsunamiProject
	 */
	protected $aTsunamiProject;

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
	 * Get the [tsunami_site_id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->tsunami_site_id;
	}

	/**
	 * Get the [bounding_polygon] column value.
	 * 
	 * @return     string
	 */
	public function getBoundingPolygon()
	{

		return $this->bounding_polygon;
	}

	/**
	 * Get the [country] column value.
	 * 
	 * @return     string
	 */
	public function getCountry()
	{

		return $this->country;
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
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{

		return $this->name;
	}

	/**
	 * Get the [site_lat] column value.
	 * 
	 * @return     double
	 */
	public function getSiteLatitude()
	{

		return $this->site_lat;
	}

	/**
	 * Get the [site_lon] column value.
	 * 
	 * @return     double
	 */
	public function getSiteLongitude()
	{

		return $this->site_lon;
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
	 * Get the [type] column value.
	 * 
	 * @return     string
	 */
	public function getType()
	{

		return $this->type;
	}

	/**
	 * Set the value of [tsunami_site_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->tsunami_site_id !== $v) {
			$this->tsunami_site_id = $v;
			$this->modifiedColumns[] = TsunamiSitePeer::TSUNAMI_SITE_ID;
		}

	} // setId()

	/**
	 * Set the value of [bounding_polygon] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setBoundingPolygon($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->bounding_polygon) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->bounding_polygon !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->bounding_polygon = $obj;
			$this->modifiedColumns[] = TsunamiSitePeer::BOUNDING_POLYGON;
		}

	} // setBoundingPolygon()

	/**
	 * Set the value of [country] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setCountry($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->country !== $v) {
			$this->country = $v;
			$this->modifiedColumns[] = TsunamiSitePeer::COUNTRY;
		}

	} // setCountry()

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
			$this->modifiedColumns[] = TsunamiSitePeer::DESCRIPTION;
		}

	} // setDescription()

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
			$this->modifiedColumns[] = TsunamiSitePeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [site_lat] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSiteLatitude($v)
	{

		if ($this->site_lat !== $v) {
			$this->site_lat = $v;
			$this->modifiedColumns[] = TsunamiSitePeer::SITE_LAT;
		}

	} // setSiteLatitude()

	/**
	 * Set the value of [site_lon] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setSiteLongitude($v)
	{

		if ($this->site_lon !== $v) {
			$this->site_lon = $v;
			$this->modifiedColumns[] = TsunamiSitePeer::SITE_LON;
		}

	} // setSiteLongitude()

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
			$this->modifiedColumns[] = TsunamiSitePeer::TSUNAMI_PROJECT_ID;
		}

		if ($this->aTsunamiProject !== null && $this->aTsunamiProject->getId() !== $v) {
			$this->aTsunamiProject = null;
		}

	} // setTsunamiProjectId()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = TsunamiSitePeer::TYPE;
		}

	} // setType()

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

			$this->tsunami_site_id = $rs->getFloat($startcol + 0);

			$this->bounding_polygon = $rs->getClob($startcol + 1);

			$this->country = $rs->getString($startcol + 2);

			$this->description = $rs->getClob($startcol + 3);

			$this->name = $rs->getString($startcol + 4);

			$this->site_lat = $rs->getFloat($startcol + 5);

			$this->site_lon = $rs->getFloat($startcol + 6);

			$this->tsunami_project_id = $rs->getFloat($startcol + 7);

			$this->type = $rs->getString($startcol + 8);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = TsunamiSitePeer::NUM_COLUMNS - TsunamiSitePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating TsunamiSite object", $e);
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
			$con = Propel::getConnection(TsunamiSitePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			TsunamiSitePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(TsunamiSitePeer::DATABASE_NAME);
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
					$pk = TsunamiSitePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += TsunamiSitePeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collTsunamiSiteDocRelationships !== null) {
				foreach($this->collTsunamiSiteDocRelationships as $referrerFK) {
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


			if (($retval = TsunamiSitePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collTsunamiSiteDocRelationships !== null) {
					foreach($this->collTsunamiSiteDocRelationships as $referrerFK) {
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
		$pos = TsunamiSitePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getBoundingPolygon();
				break;
			case 2:
				return $this->getCountry();
				break;
			case 3:
				return $this->getDescription();
				break;
			case 4:
				return $this->getName();
				break;
			case 5:
				return $this->getSiteLatitude();
				break;
			case 6:
				return $this->getSiteLongitude();
				break;
			case 7:
				return $this->getTsunamiProjectId();
				break;
			case 8:
				return $this->getType();
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
		$keys = TsunamiSitePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getBoundingPolygon(),
			$keys[2] => $this->getCountry(),
			$keys[3] => $this->getDescription(),
			$keys[4] => $this->getName(),
			$keys[5] => $this->getSiteLatitude(),
			$keys[6] => $this->getSiteLongitude(),
			$keys[7] => $this->getTsunamiProjectId(),
			$keys[8] => $this->getType(),
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
		$pos = TsunamiSitePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setBoundingPolygon($value);
				break;
			case 2:
				$this->setCountry($value);
				break;
			case 3:
				$this->setDescription($value);
				break;
			case 4:
				$this->setName($value);
				break;
			case 5:
				$this->setSiteLatitude($value);
				break;
			case 6:
				$this->setSiteLongitude($value);
				break;
			case 7:
				$this->setTsunamiProjectId($value);
				break;
			case 8:
				$this->setType($value);
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
		$keys = TsunamiSitePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setBoundingPolygon($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCountry($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDescription($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setSiteLatitude($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setSiteLongitude($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setTsunamiProjectId($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setType($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(TsunamiSitePeer::DATABASE_NAME);

		if ($this->isColumnModified(TsunamiSitePeer::TSUNAMI_SITE_ID)) $criteria->add(TsunamiSitePeer::TSUNAMI_SITE_ID, $this->tsunami_site_id);
		if ($this->isColumnModified(TsunamiSitePeer::BOUNDING_POLYGON)) $criteria->add(TsunamiSitePeer::BOUNDING_POLYGON, $this->bounding_polygon);
		if ($this->isColumnModified(TsunamiSitePeer::COUNTRY)) $criteria->add(TsunamiSitePeer::COUNTRY, $this->country);
		if ($this->isColumnModified(TsunamiSitePeer::DESCRIPTION)) $criteria->add(TsunamiSitePeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(TsunamiSitePeer::NAME)) $criteria->add(TsunamiSitePeer::NAME, $this->name);
		if ($this->isColumnModified(TsunamiSitePeer::SITE_LAT)) $criteria->add(TsunamiSitePeer::SITE_LAT, $this->site_lat);
		if ($this->isColumnModified(TsunamiSitePeer::SITE_LON)) $criteria->add(TsunamiSitePeer::SITE_LON, $this->site_lon);
		if ($this->isColumnModified(TsunamiSitePeer::TSUNAMI_PROJECT_ID)) $criteria->add(TsunamiSitePeer::TSUNAMI_PROJECT_ID, $this->tsunami_project_id);
		if ($this->isColumnModified(TsunamiSitePeer::TYPE)) $criteria->add(TsunamiSitePeer::TYPE, $this->type);

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
		$criteria = new Criteria(TsunamiSitePeer::DATABASE_NAME);

		$criteria->add(TsunamiSitePeer::TSUNAMI_SITE_ID, $this->tsunami_site_id);

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
	 * Generic method to set the primary key (tsunami_site_id column).
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
	 * @param      object $copyObj An object of TsunamiSite (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setBoundingPolygon($this->bounding_polygon);

		$copyObj->setCountry($this->country);

		$copyObj->setDescription($this->description);

		$copyObj->setName($this->name);

		$copyObj->setSiteLatitude($this->site_lat);

		$copyObj->setSiteLongitude($this->site_lon);

		$copyObj->setTsunamiProjectId($this->tsunami_project_id);

		$copyObj->setType($this->type);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getTsunamiSiteDocRelationships() as $relObj) {
				$copyObj->addTsunamiSiteDocRelationship($relObj->copy($deepCopy));
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
	 * @return     TsunamiSite Clone of current object.
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
	 * @return     TsunamiSitePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new TsunamiSitePeer();
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
	 * Otherwise if this TsunamiSite has previously
	 * been saved, it will retrieve related TsunamiSiteDocRelationships from storage.
	 * If this TsunamiSite is new, it will return
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

				$criteria->add(TsunamiSiteDocRelationshipPeer::TSUNAMI_SITE_ID, $this->getId());

				TsunamiSiteDocRelationshipPeer::addSelectColumns($criteria);
				$this->collTsunamiSiteDocRelationships = TsunamiSiteDocRelationshipPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(TsunamiSiteDocRelationshipPeer::TSUNAMI_SITE_ID, $this->getId());

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

		$criteria->add(TsunamiSiteDocRelationshipPeer::TSUNAMI_SITE_ID, $this->getId());

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
		$l->setTsunamiSite($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this TsunamiSite is new, it will return
	 * an empty collection; or if this TsunamiSite has previously
	 * been saved, it will retrieve related TsunamiSiteDocRelationships from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in TsunamiSite.
	 */
	public function getTsunamiSiteDocRelationshipsJoinTsunamiDocLib($criteria = null, $con = null)
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

				$criteria->add(TsunamiSiteDocRelationshipPeer::TSUNAMI_SITE_ID, $this->getId());

				$this->collTsunamiSiteDocRelationships = TsunamiSiteDocRelationshipPeer::doSelectJoinTsunamiDocLib($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(TsunamiSiteDocRelationshipPeer::TSUNAMI_SITE_ID, $this->getId());

			if (!isset($this->lastTsunamiSiteDocRelationshipCriteria) || !$this->lastTsunamiSiteDocRelationshipCriteria->equals($criteria)) {
				$this->collTsunamiSiteDocRelationships = TsunamiSiteDocRelationshipPeer::doSelectJoinTsunamiDocLib($criteria, $con);
			}
		}
		$this->lastTsunamiSiteDocRelationshipCriteria = $criteria;

		return $this->collTsunamiSiteDocRelationships;
	}

} // BaseTsunamiSite
