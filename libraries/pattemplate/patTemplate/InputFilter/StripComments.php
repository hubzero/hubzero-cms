<?PHP
/**
 * patTemplate StripComments input filter
 *
 * $Id: StripComments.php 8287 2007-08-01 08:38:59Z eddieajau $
 *
 * Will remove all HTML comments.
 *
 * @package		patTemplate
 * @subpackage	Filters
 * @author		Stephan Schmidt <schst@php.net>
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * patTemplate StripComments output filter
 *
 * $Id: StripComments.php 8287 2007-08-01 08:38:59Z eddieajau $
 *
 * Will remove all HTML comments.
 *
 * @package		patTemplate
 * @subpackage	Filters
 * @author		Stephan Schmidt <schst@php.net>
 */
class patTemplate_InputFilter_StripComments extends patTemplate_InputFilter
{
	/**
	* filter name
	*
	* @access	protected
	* @abstract
	* @var	string
	*/
	var	$_name	=	'StripComments';

	/**
	* compress the data
	*
	* @access	public
	* @param	string		data
	* @return	string		data without whitespace
	*/
	function apply( $data )
	{
		$data = preg_replace( '�<!--.*-->�msU', '', $data );
		$data = preg_replace( '�/\*.*\*/�msU', '', $data );

		return $data;
	}
}
?>
