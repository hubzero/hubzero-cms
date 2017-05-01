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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     2.1.1
 */

namespace Components\Search\Helpers;

use Components\Search\Models\Hubtype;
use stdClass;
use Solarium;

/**
 * Solr helper class
 */
class SolrHelper
{
	public function __construct()
	{
		$config = Component::params('com_search');
		$core = $config->get('solr_core');
		$port = $config->get('solr_port');
		$host = $config->get('solr_host');
		$path = $config->get('solr_path');

		$solrConfig = array('endpoint' =>
			array($core =>
				array('host' => $host, 'port' => $port,
							'path' => $path, 'core' => $core,)
						)
		);

		$this->connection = new Solarium\Client($solrConfig);
		$this->query = $this->connection->createSelect();

		return $this;
	}
	/**
	 * parseDocumentID - returns a friendly way to access the type and id from a solr ID 
	 * 
	 * @param   string  $id 
	 * @static
	 * @access  public
	 * @return  mixed
	 */
	public function parseDocumentID($id = '')
	{
		if ($id != '')
		{
			$parts = explode('-', $id);

			if (count($parts) == 3)
			{
				$type = $parts[0] . '-' . $parts[1];
				$id   = $parts[2];
			}
			elseif (count($parts) == 2)
			{
				$type = $parts[0];
				$id   = $parts[1];
			}

			return array('type' => $type, 'id' => $id);
		}
		return false;
	}

	/**
	 * removeDocument - Removes a single document from the search index
	 *
	 * @param string $id
	 * @access public
	 * @return boolean 
	 */
	public function removeDocument($id)
	{
		if ($id != null)
		{
			$update = $this->connection->createUpdate();
			$update->addDeleteQuery('id:'.$id);
			$update->addCommit();
			$response = $this->connection->update($update);

			// @FIXME: Increase error checking 
			// Wild assumption that the update was successful
			return true;
		}
		else
		{
			return false;
		}
	}
}
