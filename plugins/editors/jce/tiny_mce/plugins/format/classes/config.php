<?php
/**
 * @version		$Id: config.php 48 2009-05-27 10:46:36Z happynoodleboy $
 * @package     JCE
 * @copyright   Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
 * @author		Ryan Demmer
 * @license     GNU/GPL 2 - See licence.txt
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
class FormatConfig
{
    function getConfig(&$vars)
    {
        jimport('joomla.filesystem.file');
        
        $jce = &JContentEditor::getInstance();
        $params = $jce->getEditorParams();
        
		// Add format plugin to plugins list
        if (!in_array('format', $vars['plugins'])) {
            $vars['plugins'][] = 'format';
        }

        // Encoding
        $vars['entity_encoding'] = $jce->getEditorParam('editor_entity_encoding', 'raw', 'named');
        $vars['inline_styles'] = $jce->getEditorParam('editor_inline_styles', '1', '1');

		$vars['content_css'] = FormatConfig::getStyleSheet();
        
        // Paragraph handling
        $vars['forced_root_block'] = $jce->getEditorParam('editor_forced_root_block', 0, 'p');
		
		// Format list / Remove Format
		$vars['theme_advanced_blockformats'] = $jce->getEditorParam('editor_theme_advanced_blockformats', 'p,div,address,pre,h1,h2,h3,h4,h5,h6,code,samp', 'p,address,pre,h1,h2,h3,h4,h5,h6');
        $vars['removeformat_selector'] = $jce->getEditorParam('editor_removeformat_selector', 'span,b,strong,em,i,font,u,strike', 'span,b,strong,em,i,font,u,strike');
        
        $fs = $vars['removeformat_selector'] == '' ? 'span,b,strong,em,i,font,u,strike' : $vars['removeformat_selector'];

        $fs = explode(',', $fs);
        $bf = explode(',', $vars['theme_advanced_blockformats']);
        
        $rb = ($vars['forced_root_block'] === '') ? 'p' : $vars['forced_root_block'];
        
        foreach ($bf as $k=>$v) {
            if ($v == $rb) {
                unset($bf[$k]);
            }
        }
        
        $vars['removeformat_selector'] = implode(',', array_unique(array_merge($fs, $bf)));
        
        if ($params->get('editor_newlines', 0) == 1) {
            $vars['force_br_newlines'] = 1;
            $vars['force_p_newlines'] = 0;
        } else {
            $vars['force_br_newlines'] = 0;
            $vars['force_p_newlines'] = 1;
        }
        
        // Relative urls
        $vars['relative_urls'] = $jce->getEditorParam('editor_relative_urls', 1, 1);
        if ($vars['relative_urls'] == 0) {
            $vars['remove_script_host'] = 0;
        }
        
        // Fonts
        $vars['theme_advanced_fonts'] = $jce->getEditorFonts($jce->getEditorParam('editor_theme_advanced_fonts_add', ''), $jce->getEditorParam('editor_theme_advanced_fonts_remove', ''));
        $vars['theme_advanced_font_sizes'] = $jce->getEditorParam('editor_theme_advanced_font_sizes', '8pt,10pt,12pt,14pt,18pt,24pt,36pt');
		$vars['theme_advanced_default_foreground_color'] = $jce->getEditorParam('editor_theme_advanced_default_foreground_color', '#000000');
		$vars['theme_advanced_default_background_color'] = $jce->getEditorParam('editor_theme_advanced_default_background_color', '#FFFF00');
        
        $vars['custom_colors'] = $jce->getEditorParam('editor_custom_colors', '', '');
    }
	
	function getStyleSheet() {
		jimport('joomla.filesystem.file');
        
        $jce = &JContentEditor::getInstance();
        $params = $jce->getEditorParams();
		
		// Template CSS
        $path 	= JPATH_SITE.DS.'templates'.DS.$jce->getSiteTemplate().DS.'css';
        $url 	= "/templates/".$jce->getSiteTemplate()."/css";
		 
		$stylesheet = '';
		
		// Joomla! 1.5 standard
		$file = 'template.css';
	
		// Check for template.css
		if (!JFile::exists($path.DS.$file)) {
			// check for legacy template_css.css
			if (JFile::exists($path.DS.'template_css.css')) {
            	$file = 'template_css.css';
       	 	}
		}

        // Custom template css URL
        if (intval($params->get('editor_content_css', 1)) == 0) {
        	$custom = $params->get('editor_content_css_custom', '');
			if ($custom) {
				// Replace $template variable with site template name
				$custom = str_replace('$template', $jce->getSiteTemplate(), $custom);
				// Show error if file does not exist and use default
				if (!JFile::exists(JPATH_SITE.DS.$custom)) {
					JError::raiseNotice('SOME_ERROR_CODE', sprintf(JText::_('CUSTOMCSSFILENOTPRESENT'), $custom));
				} else {
					$stylesheet = JURI::root(true).'/'.$custom;
				}
			}			
        } else {
        	if (!JFile::exists($path.DS.$file)) {
        		// display error
				JError::raiseNotice('SOME_ERROR_CODE', sprintf(JText::_('TEMPLATECSSFILENOTPRESENT'), $file));
			} else {
				$stylesheet = JURI::root(true).$url.'/'.$file;
			}
        }
		
		// default to template.css or template_css.css or system editor.css
		if (!$stylesheet) {
			if (!JFile::exists($path.DS.$file)) {
				$file 		= '/templates/system/css/editor.css';
				$stylesheet = JURI::root(true) .'/templates/system/css/editor.css';
			} else {
				$file = $url.'/'.$file;
			}
			$stylesheet = JURI::root(true).$file;
			JError::raiseNotice('SOME_ERROR_CODE', sprintf(JText::_('CSSFILEDEFAULT'), $file));
		}
		
		return $stylesheet;
	}
}
?>
