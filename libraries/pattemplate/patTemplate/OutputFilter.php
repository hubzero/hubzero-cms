<?PHP
/**
 * Base class for patTemplate output filter
 *
 * $Id: OutputFilter.php 8287 2007-08-01 08:38:59Z eddieajau $
 *
 * An output filter is used to modify the output
 * after it has been processed by patTemplate, but before
 * it is sent to the browser.
 *
 * @package		patTemplate
 * @subpackage	Filters
 * @author		Stephan Schmidt <schst@php.net>
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Base class for patTemplate output filter
 *
 * $Id: OutputFilter.php 8287 2007-08-01 08:38:59Z eddieajau $
 *
 * An output filter is used to modify the output
 * after it has been processed by patTemplate, but before
 * it is sent to the browser.
 *
 * @abstract
 * @package		patTemplate
 * @subpackage	Filters
 * @author		Stephan Schmidt <schst@php.net>
 */
class patTemplate_OutputFilter extends patTemplate_Module
{
	/**
	* apply the filter
	*
	* @access	public
	* @param	string		data
	* @return	string		filtered data
	*/
	function apply( $data )
	{
		return $data;
	}
}
?>
