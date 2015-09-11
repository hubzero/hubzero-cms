<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for citations
 */
class plgPublicationsCitations extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $publication 	Current publication
	 * @param      string $version 		Version name
	 * @param      boolean $extended 	Whether or not to show panel
	 * @return     array
	 */
	public function &onPublicationAreas( $publication, $version = 'default', $extended = true)
	{
		if ($publication->_category->_params->get('plg_citations'))
		{
			$areas = array(
				'citations' => Lang::txt('PLG_PUBLICATION_CITATIONS')
			);
		}
		else
		{
			$areas = array();
		}
		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param      object  	$publication 	Current publication
	 * @param      string  	$option    		Name of the component
	 * @param      array   	$areas     		Active area(s)
	 * @param      string  	$rtrn      		Data to be returned
	 * @param      string 	$version 		Version name
	 * @param      boolean 	$extended 		Whether or not to show panel
	 * @return     array
	 */
	public function onPublication( $publication, $option, $areas,
		$rtrn='all', $version = 'default', $extended = true  )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ))
		{
			if (!array_intersect( $areas, $this->onPublicationAreas( $publication ) )
			&& !array_intersect( $areas, array_keys( $this->onPublicationAreas( $publication ) ) ))
			{
				$rtrn = 'metadata';
			}
		}

		if (!$publication->_category->_params->get('plg_citations'))
		{
			return $arr;
		}

		$database = App::get('db');

		// Get a needed library
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'author.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'secondary.php');

		// Get citations for this publication
		$c = new \Components\Citations\Tables\Citation( $database );
		$citations = $c->getCitations( 'publication', $publication->id );

		$arr['count'] = $citations ? count($citations) : 0;
		$arr['name']  = 'citations';

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			$config = Component::params($option);
			// Instantiate a view
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'=>'publications',
					'element'=>'citations',
					'name'=>'browse'
				)
			);

			// Pass the view some info
			$view->option 		= $option;
			$view->publication 	= $publication;
			$view->citations 	= $citations;
			$view->format 		= $config->get('citation_format', 'apa');
			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'metadata'
				)
			);
			$view->url = Route::url('index.php?option=' . $option . '&' . ($publication->alias ? 'alias=' . $publication->alias : 'id=' . $publication->id) . '&active=citations&v=' . $publication->version_number);
			$view->citations = $citations;

			$arr['metadata'] = $view->loadTemplate();
		}

		// Return results
		return $arr;
	}
}
