<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

use Components\Projects\Tables;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Request;

/**
 * Project Note model
 */
class Note extends \Components\Wiki\Models\Book
{
    protected $cache;
    protected $db;
    protected $scope;

    /**
     * Project group cn
     *
     * @var  string
     */
    private $group_cn = '';

    /**
     * Project id
     *
     * @var  string|int
     */
    private $project_id = '';

    /**
     * Constructor
     *
     * @param   string   $scope
     * @param   string   $group_cn
     * @param   integer  $project_id
     * @return  void
     */
    public function __construct($scope = 'project', $group_cn = '', $project_id = 0)
    {
        $this->db = App::get('db');
        $this->scope = $scope;
        $this->group_cn = $group_cn;
        $this->project_id = $project_id;

        parent::__construct($scope);
    }

    /**
     * Set and get a specific page
     *
     * @param   mixed   $id     Integer or string of tag to look up
     * @param   string  $scope
     * @return  object
     */
    public function page($id = null, $scope = '')
    {
        $scope = $scope ? $scope : $this->scope;
        $this->cache['page'] = \Components\Wiki\Models\Page::oneByPath($id, 'project', $this->project_id);

        return $this->cache['page'];
    }

    /**
     * Get public stamp for note
     *
     * @param   integer  $id
     * @param   boolean  $register
     * @param   boolean  $listed
     * @return  mixed
     */
    public function getPublicStamp($id = 0, $register = false, $listed = null)
    {
        if (!is_file(dirname(__DIR__) . DS . 'tables' . DS . 'publicstamp.php')) {
            return false;
        }

        $page = $this->page($id);

        if (!$page) {
            return false;
        }

        $objSt = new Tables\Stamp($this->db);

        // Build reference for latest revision of page
        $reference = [
            'pageid'   => $page->get('id'),
            'pagename' => $page->get('pagename'),
            'revision' => null,
        ];

        // Check valid stamp
        $objSt->checkStamp($this->project_id, json_encode($reference), 'notes');
        $list = ($listed !== null && $listed != $objSt->listed) ? true : false;

        if ($list == true) {
            return $objSt->registerStamp($this->project_id, json_encode($reference), 'notes', $listed);
        }

        // Register new stamp?
        if (!$objSt->id && $register == true) {
            $objSt->registerStamp($this->project_id, json_encode($reference), 'notes', $listed);
            $objSt->checkStamp($this->project_id, json_encode($reference), 'notes');
        }

        return $objSt;
    }

    /**
     * Get default project note
     *
     * @param      string $prefix
     * @return     mixed
     */
    public function getFirstNote($prefix = '')
    {
        $query  = "SELECT p.pagename FROM `#__wiki_pages` AS p
				  WHERE p.scope='project' AND p.scope_id=" . $this->db->quote($this->project_id) . " AND p.state!=2";

        $query .= $prefix ? " AND p.pagename LIKE " . $this->db->quote($prefix . '%') : "";
        $query .= " ORDER BY p.times_rated, p.id ASC LIMIT 1";

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Get a list of parent project notes
     *
     * @param   string  $scope
     * @return  array
     */
    public function getParentNotes($scope = '')
    {
        $scope = $scope ? $scope : $this->scope;
        $parts = explode('/', $scope);
        $remaining = array_slice($parts, 3);
        if ($remaining) {
            $query  = "SELECT DISTINCT p.pagename, p.title, p.path, p.scope, p.scope_id ";
            $query .= "FROM `#__wiki_pages` AS p ";
            $query .= "WHERE p.scope='project' AND p.scope_id="
                . $this->db->quote($this->project_id)
                . " AND p.state!=2 ";
            $k = 1;
            $where = '';
            foreach ($remaining as $r) {
                $where .= "p.pagename=" . $this->db->quote(trim($r));
                $where .= $k == count($remaining) ? '' : ' OR ';
                $k++;
            }
            $query .= "AND (" . $where . ")";
            $this->db->setQuery($query);
            return $this->db->loadObjectList();
        }

        return [];
    }

    /**
     * Get a list of project notes
     *
     * @param   integer  $limit
     * @param   string   $orderby
     * @return  array
     */
    public function getNotes($limit = 0, $orderby = 'p.path ASC, p.title ASC, p.times_rated ASC, p.id')
    {
        $query = "SELECT DISTINCT p.id, p.path, p.pagename, p.title, p.scope, p.times_rated
				  FROM `#__wiki_pages` AS p
				  WHERE p.scope='project' AND p.scope_id=" . $this->db->quote($this->project_id) . "
				  AND p.pagename NOT LIKE 'Template:%'
				  AND p.state!=2
				  ORDER BY " . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $orderby) ?: '1') . " ";

        $query .= intval($limit) ? " LIMIT " . (int) $limit : '';

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get count of project notes
     *
     * @return  integer
     */
    public function getNoteCount()
    {
        $query = "SELECT COUNT(*) FROM `#__wiki_pages` AS p
				  WHERE p.scope='project' AND p.scope_id=" . $this->db->quote($this->project_id) . " AND p.state!=2
				  AND p.pagename NOT LIKE 'Template:%'";

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Get project note
     *
     * @param   string  $id
     * @return  mixed
     */
    public function getSelectedNote($id = '')
    {
        $query = "SELECT DISTINCT p.id, p.pagename, p.title, p.scope, p.path, p.scope_id, p.times_rated,
				  (SELECT v.version FROM `#__wiki_versions` as v WHERE v.page_id=p.id
				  ORDER by v.version DESC LIMIT 1) as version,
				  (SELECT vv.id FROM `#__wiki_versions` as vv WHERE vv.page_id=p.id
				  ORDER by vv.id DESC LIMIT 1) as instance
				  FROM `#__wiki_pages` AS p
				  WHERE p.scope='project' AND p.scope_id=" . $this->db->quote($this->project_id) . "
				  AND p.state!=2
				  AND p.pagename NOT LIKE 'Template:%'";

        $query .= is_numeric($id)
            ? " AND p.id=" . (int) $id . " LIMIT 1"
            : " AND p.pagename=" . $this->db->quote($id) . " LIMIT 1";

        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();

        return $result ? $result[0] : null;
    }

    /**
     * Get last note order
     *
     * @param   string  $scope
     * @return  integer
     */
    public function getLastNoteOrder($scope = '')
    {
        $scope = $scope ? $scope : $this->scope;
        $query = "SELECT p.times_rated FROM `#__wiki_pages` AS p
				  WHERE p.scope='project' AND p.scope_id=" . $this->db->quote($this->project_id) . "
				  ORDER BY p.times_rated DESC LIMIT 1";

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Fix scope paths after page rename
     *
     * @param   string  $scope
     * @param   string  $oldpagename
     * @param   string  $newpagename
     * @return  boolean
     */
    public function fixScopePaths($scope, $oldpagename, $newpagename)
    {
        $query = "UPDATE `#__wiki_pages` AS p SET p.path=replace(p.path, " . $this->db->quote('/' . $oldpagename) . ", "
            . $this->db->quote('/' . $newpagename) . ")
				  WHERE p.scope='project' AND p.scope_id=" . $this->db->quote($this->project_id);

        $this->db->setQuery($query);
        if (!$this->db->query()) {
            return false;
        }
        return true;
    }

    /**
     * Save note order
     *
     * @param   string   $scope
     * @param   integer  $order
     * @return  boolean
     */
    public function saveNoteOrder($scope, $order = 0)
    {
        $query = "UPDATE `#__wiki_pages` AS p SET p.times_rated=" . $this->db->quote($order) . "
				  WHERE p.scope='project' AND p.scope_id=" . $this->db->quote($this->project_id) . "
				  AND p.times_rated='0'";

        $this->db->setQuery($query);
        if (!$this->db->query()) {
            return false;
        }
        return true;
    }

    /**
     * Get path to wiki page images and files
     *
     * @param   integer  $id
     * @return  string|false
     */
    public function getWikiPath($id = 0)
    {
        // Ensure we have an ID to work with
        $listdir = Request::getInt('lid', 0);
        $id = $id ? $id : $listdir;

        if (!$id) {
            return false;
        }

        // Load wiki configs
        $wiki_config = Component::params('com_wiki');

        $path =  DS . trim($wiki_config->get('filepath', '/site/wiki'), DS) . DS . $id;

        if (!is_dir(PATH_APP . $path)) {
            if (!Filesystem::makeDirectory(PATH_APP . $path)) {
                return false;
            }
        }

        return $path;
    }
}
