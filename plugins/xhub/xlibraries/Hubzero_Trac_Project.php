<?php

class Hubzero_Trac_Project
{
	private $id = null;
	private $name = null;
	private $_updatedkeys = array();
	private $_list_keys = array();

	private function __construct()
	{
	}

	public function clear()
	{
		$cvars = get_class_vars(__CLASS__);

		$this->_updatedkeys = array();

		foreach ($cvars as $key=>$value) {
			if ($key{0} != '_') {
				unset($this->$key);

				if (!in_array($key, $this->_list_keys)) {
					$this->$key = null;
				}
				else {
					$this->$key = array();
				}
			}
		}

		$this->_updatedkeys = array();

		return true;
	}

	private function logDebug($msg)
	{
		$xlog = &XFactory::getLogger();
		$xlog->logDebug($msg);
	}

	public function toArray()
	{
		$result = array();

		$cvars = get_class_vars(__CLASS__);

		foreach ($cvars as $key=>$value) {
			if ($key{0} == '_') {
				continue;
			}

			$current = $this->__get($key);

			$result[$key] = $current;
		}

		return $result;
	}

	public function find($name)
	{
		$hztp = new Hubzero_Trac_Project();
		
		if (is_numeric($name))
			$hztp->id = $name;
		else
			$hztp->name = $name;

		if ($hztp->read() == false) {
			return false;
		}

		return $hztp;
	}

	public function find_or_create($name)
	{
		$hztp = new Hubzero_Trac_Project();

		if (is_numeric($name))
			$hztp->id = $name;
		else
			$hztp->name = $name;

		if ($hztp->read() == false) {
			if ($hztp->create() == false)
				return false;
		}

		return $hztp;
	}

	public function create()
	{
		$db = &JFactory::getDBO();

		if (empty($db)) {
			return false;
		}

		if (is_numeric($this->id)) {
			$query = "INSERT INTO #__trac_project (id,name) VALUES ( " . $db->Quote($this->id) . "," . $db->Quote($this->name) . ");";
			$db->setQuery($query);

			$result = $db->query();

			if ($result !== false || $db->getErrorNum() == 1062) {
				return true;
			}
		}
		else {
			$query = "INSERT INTO #__trac_project (name) VALUES ( " . $db->Quote($this->name) . ");";

			$db->setQuery($query);

			$result = $db->loadResult();

			if ($result === false && $db->getErrorNum() == 1062) {
				$query = "SELECT id FROM #__trac_project WHERE name=" . $db->Quote($this->name) . ";";

				$db->setQuery($query);

				$result = $db->loadResult();

				if ($result == null) {
					return false;
				}

				$this->id = $result;
				return true;
			}
			else if ($result !== false) {
				$this->id = $db->insertid();
				return true;
			}
		}

		return false;
	}

	public function read()
	{
		$db = &JFactory::getDBO();

		$lazyloading = false;

		if (empty($db)) {
			return false;
		}

		if (is_numeric($this->id)) {
			$query = "SELECT * FROM #__trac_project WHERE id = " . $db->Quote($this->id) . ";";
		}
		else {
			$query = "SELECT * FROM #__trac_project WHERE name = " . $db->Quote($this->name) . ";";
		}

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if (empty($result)) {
			return false;
		}

		$this->clear();

		foreach ($result as $key=>$value) {
			if (property_exists(__CLASS__, $key) && $key{0} != '_') {
				$this->__set($key, $value);
			}
		}

		$this->_updatedkeys = array();

		return true;
	}

	public function update($all = false)
	{
		$db = &JFactory::getDBO();

		$query = "UPDATE #__trac_project SET ";

		$classvars = get_class_vars(__CLASS__);

		$first = true;

		foreach ($classvars as $property=>$value) {
			if (($property{0} == '_') || in_array($property, $this->_list_keys)) {
				continue;
			}

			if (!$all && !in_array($property, $this->_updatedkeys)) {
				continue;
			}

			if (!$first) {
				$query .= ',';
			}
			else {
				$first = false;
			}

			$value = $this->__get($property);

			if ($value === null) {
				$query .= "`$property`=NULL";
			}
			else {
				$query .= "`$property`=" . $db->Quote($value);
			}
		}

		$query .= " WHERE `id`=" . $db->Quote($this->__get('id')) . ";";

		if ($first == true) {
			$query = '';
		}

		if (!empty($query)) {
			$db->setQuery($query);

			$result = $db->query();

			if ($result === false) {
				return false;
			}

			$affected = mysql_affected_rows($db->_resource);

			if ($affected < 1) {
				$this->create();

				$db->setQuery($query);

				$result = $db->query();

				if ($result === false) {
					return false;
				}

				$affected = mysql_affected_rows($db->_resource);

				if ($affected < 1) {
					return false;
				}
			}
		}

		return true;
	}

	public function delete()
	{
		if (!isset($this->name) && !isset($this->id)) {
			return false;
		}

		$db = JFactory::getDBO();

		if (empty($db)) {
			return false;
		}

		if (!isset($this->id)) {
			$db->setQuery("SELECT id FROM #__trac_project WHERE name=" . $db->Quote($this->name) . ";");

			$this->id = $db->loadResult();
		}

		if (empty($this->id)) {
			return false;
		}

		$db->setQuery("DELETE FROM #__trac_project WHERE id=" . $db->Quote($this->id) . ";");

		if (!$db->query()) {
			return false;
		}

		$db->setQuery("DELETE FROM #__trac_user_permission WHERE trac_project_id=" . $db->Quote($this->id) . ";");
		$db->query();
		$db->setQuery("DELETE FROM #__trac_group_permission WHERE trac_project_id=" . $db->Quote($this->id) . ";");
		$db->query();

		return true;
	}

	private function __get($property = null)
	{
		$xlog = &XFactory::getLogger();

		if (!property_exists(__CLASS__, $property) || $property{0} == '_') {
			if (empty($property)) {
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

        if (in_array($property, $this->_list_keys)) {
            if (!array_key_exists($property, get_object_vars($this))) {
                $db = &JFactory::getDBO();

                if (is_object($db)) {
                    $query = null;

					if (!empty($query)) {
	                    $db->setQuery($query);

    	                $result = $db->loadResultArray();
					}

                    if ($result !== false) {
                        $this->__set($property, $result);
                    }
                }
            }
        }

		if (isset($this->$property)) {
			return $this->$property;
		}

		if (array_key_exists($property, get_object_vars($this))) {
			return null;
		}

		$this->_error("Undefined property " . __CLASS__ . "::$" . $property, E_USER_NOTICE);

		return null;
	}

	private function __set($property = null, $value = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_') {
			if (empty($property)) {
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

        if (in_array($property, $this->_list_keys)) {
            $value = array_diff((array) $value, array(''));
            $value = array_unique($value);
            $value = array_values($value);
            $this->$property = $value;
        }
        else {
            $this->$property = $value;
        }

		if (!in_array($property, $this->_updatedkeys)) {
			$this->_updatedkeys[] = $property;
		}
	}

	private function __isset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_') {
			if (empty($property)) {
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		return isset($this->$property);
	}

	private function __unset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_') {
			if (empty($property)) {
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		$this->_updatedkeys = array_diff($this->_updatedkeys, array($property));

		unset($this->$property);
	}

	private function _error($message, $level = E_USER_NOTICE)
	{
		$caller = next(debug_backtrace());

		switch ($level)
		{
			case E_USER_NOTICE:
				echo "Notice: ";
				break;
			case E_USER_ERROR:
				echo "Fatal error: ";
				break;
			default:
				echo "Unknown error: ";
				break;
		}

		echo $message . ' in ' . $caller['file'] . ' on line ' . $caller['line'] . "\n";
	}

	public function get($key)
	{
		return $this->__get($key);
	}

	public function set($key, $value)
	{
		return $this->__set($key, $value);
	}

	public function add_user_permission($user,$action)
	{
		$db = &JFactory::getDBO();

		if ($user == 'anonymous') {
			$user = '0';
		}

		if (!is_numeric($user)) {
			$query = "SELECT id FROM #__users WHERE username=" . $db->Quote($user) . ";";
			$db->setQuery($query);
			$user_id = $db->loadResult();

			if ($user_id === false) {
				$this->_error("Unknown user $user");
				return false;
			}
		}

		$quoted_project_id = $db->Quote($this->id);
		$quoted_user_id = $db->Quote($user_id);
		$values = '';

		foreach((array) $action as $a) {
			if (!empty($values)) {
				$values .= ',';
			}
			$values .= "($quoted_project_id,$quoted_user_id," . $db->Quote($a) .")";
		}

		$query = "INSERT IGNORE INTO #__trac_user_permission (trac_project_id,user_id,action) VALUES " .  $values . ";";

		$db->setQuery($query);
		$db->query();
	}

	public function add_group_permission($group,$action)
	{
		$db = &JFactory::getDBO();

		if ($group == 'authenticated') {
			$group = '0';
		}

		if (!is_numeric($group)) {
			$query = "SELECT gidNumber FROM #__xgroups WHERE cn=" . $db->Quote($group) . ";";
			$db->setQuery($query);
			$group_id = $db->loadResult();

			if ($group_id === false) {
				$this->_error("Unknown group $group");
				return false;
			}
		}

		$quoted_project_id = $db->Quote($this->id);
		$quoted_group_id = $db->Quote($group_id);
		$values = '';

		foreach((array) $action as $a) {
			if (!empty($values)) {
				$values .= ',';
			}
			$values .= "($quoted_project_id,$quoted_group_id," . $db->Quote($a) .")";
		}

		$query = "INSERT IGNORE INTO #__trac_group_permission (trac_project_id,group_id,action) VALUES " .  $values . ";";

		$db->setQuery($query);
		$db->query();
	}

	public function remove_user_permission($user,$action)
	{
		$db = &JFactory::getDBO();
		$all = false;

		if ($user == 'anonymous') {
			$user = '0';
		}

		if (!is_numeric($user)) {
			$query = "SELECT id FROM #__users WHERE username=" . $db->Quote($user) . ";";
			$db->setQuery($query);
			$user_id = $db->loadResult();

			if ($user_id === false) {
				$this->_error("Unknown user $user");
				return false;
			}
		}

		$quoted_project_id = $db->Quote($this->id);
		$quoted_user_id = $db->Quote($user_id);
		$values = '';

		foreach((array) $action as $a) {
			if ($a == '*') {
				$all = true;
			}
			if (!empty($values)) {
				$values .= ',';
			}
			$values .= $db->Quote($a);
		}

		$query = "DELETE FROM  #__trac_user_permission WHERE trac_project_id=$quoted_project_id AND user_id=$quoted_user_id";
	   
		if (!$all)
			$query .= " AND action IN (" .  $values . ");";

		$db->setQuery($query);
		$db->query();
	}

	public function remove_group_permission($group,$action)
	{
		$db = &JFactory::getDBO();
		$all = false;

		if ($group == 'authenticated') {
			$group = '0';
		}

		if (!is_numeric($group)) {
			$query = "SELECT gidNumber FROM #__xgroups WHERE cn=" . $db->Quote($group) . ";";
			$db->setQuery($query);
			$group_id = $db->loadResult();

			if ($group_id === null) {
				$this->_error("Unknown group $group");
				return false;
			}
		}

		$quoted_project_id = $db->Quote($this->id);
		$quoted_group_id = $db->Quote($group_id);
		$values = '';

		foreach((array) $action as $a) {
			if ($a == '*') {
				$all = true;
			}
			if (!empty($values)) {
				$values .= ',';
			}
			$values .= $db->Quote($a);
		}

		$query = "DELETE FROM  #__trac_group_permission WHERE trac_project_id=$quoted_project_id AND group_id=$quoted_group_id";
	   
		if (!$all)
			$query .= " AND action IN (" .  $values . ");";

		$db->setQuery($query);
		$db->query();
	}

	public function get_user_permission($user)
	{
		$db = &JFactory::getDBO();
		$quoted_project_id = $db->Quote($this->id);

		if ($user == "anonymous") {
			$user = '0';
		}
		$quoted_user = $db->Quote($user);
		if (is_numeric($user)) {
			$query = "SELECT action FROM #__trac_user_permission AS up WHERE up.trac_project_id=$quoted_project_id AND up.user_id=$quoted_user;";
		}
		else {
			$query = "SELECT action FROM #__trac_user_permission AS up, #__users AS u WHERE up.trac_project_id=$quoted_project_id AND u.id=up.user_id AND u.username=$quoted_user;";
		}

		$db->setQuery($query);
		$result = $db->loadResultArray();

		return $result;
	}

	public function get_group_permission($group)
	{
		$db = &JFactory::getDBO();
		$quoted_project_id = $db->Quote($this->id);

		if ($group == 'authenticated') {
			$group = '0';
		}
		$quoted_group = $db->Quote($group);
		if (is_numeric($group)) {
			$query = "SELECT action FROM #__trac_group_permission AS gp WHERE gp.trac_project_id=$quoted_project_id AND gp.group_id=$quoted_group;";
		}
		else {
			$query = "SELECT action FROM #__trac_group_permission AS gp, #__xgroups AS g WHERE gp.trac_project_id=$quoted_project_id AND g.gidNumber=gp.group_id AND g.cn=$quoted_group;";
		}

		$db->setQuery($query);
		$result = $db->loadResultArray();

		return $result;
	}
}

