<?php
/**
 * Joomla! 1.5 component Joomdle
 *
 * @version $Id: view.html.php 2009-04-17 03:54:05 svn $
 * @author Antonio Durán Terrés
 * @package Joomla
 * @subpackage Joomdle
 * @license GNU/GPL
 *
 * Shows information about Moodle courses
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class JoomdleViewDefault extends JView {
 function showButton( $link, $image, $text )
        {
                global $mainframe;
                $lang           =& JFactory::getLanguage();
                ?>
                <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
                        <div class="icon">
                                <a href="<?php echo $link; ?>">
					<?php echo JHTML::_('image', 'administrator/components/com_joomdle/assets/icons/' . $image , NULL, NULL, $text ); ?>

                                        <span><?php echo $text; ?></span></a>
                        </div>
                </div>
                <?php
        }

 
 function renderAbout ()
 {
	$xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'manifest.xml';

	if (file_exists($xmlfile))
	{
		if ($data = JApplicationHelper::parseXMLInstallFile($xmlfile)) {
			$version =  $data['version'];
		}
	}


	 $output = '<div style="padding: 5px;">';
	 $output .= JText::sprintf('CJ ABOUT_TEXT_VERSION', $version);
	 $output .= '<P>'.JText::sprintf('CJ ABOUT_TEXT_PROVIDES');
	 $output .= '<P>'.JText::sprintf('CJ ABOUT_TEXT_SUPPORT');
	 $output .= '<P>'.JText::sprintf('CJ ABOUT_TEXT_DONATION');
	 $output .= '<P>'.JText::sprintf('CJ ABOUT_TEXT_JED');
	 $output .= '</div>';

	 return $output;

 }

    function display($tpl = null) {


        parent::display($tpl);
    }
}
?>
