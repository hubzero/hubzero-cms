<?php
/**
 * @version		$Id: view.html.php 102 2009-06-21 19:20:52Z happynoodleboy $
 * @package		JCE
 * @copyright	Copyright (C) 2009 Ryan Demmer. All rights reserved.
 * @license		GNU/GPL
 * This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.view');


/**
 * Extension Manager Default View
 *
 * @package		JCE
 * @since		1.5
 */
class ConfigViewConfig extends JView
{
    function display($tpl = null)
    {
        $db = & JFactory::getDBO();

        $lang = & JFactory::getLanguage();
        $lang->load('plg_editors_jce', JPATH_SITE);

        $client = JRequest::getWord('client', 'site');

        $lists = array ();
        $row = & JTable::getInstance('plugin');

        $query = 'SELECT id'
        .' FROM #__plugins'
        .' WHERE element = "jce"'
        ;
        $db->setQuery($query);
        $id = $db->loadResult();

        // load the row from the db table
        $row->load(intval($id));

        $xml = JCE_LIBRARIES.DS.'xml'.DS.'config'.DS.'config.xml';
        if (!file_exists($xml)) {
        	$xml = JPATH_PLUGINS.DS.'editors'.DS.'jce.xml';
        }

        // get params definitions
        $params = new JParameter($row->params, $xml);
        $params->addElementPath(JPATH_COMPONENT.DS.'elements');
        $this->assignRef('params', $params);
        $this->assignRef('client', $client);

        parent::display($tpl);
    }
}
