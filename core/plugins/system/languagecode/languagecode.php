<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die;

/**
 * Language Code plugin class.
 */
class plgSystemLanguagecode extends \Hubzero\Plugin\Plugin
{
	/**
	 * Plugin that change the language code used in the <html /> tag
	 *
	 * @return  void
	 */
	public function onAfterRender()
	{
		// Use this plugin only in site application
		if (App::isSite())
		{
			// Get the response body
			$body = App::get('response')->getContent();

			// Get the current language code
			$code = Document::getLanguage();

			// Get the new code
			$new_code = $this->params->get($code);

			// Replace the old code by the new code in the <html /> tag
			if ($new_code)
			{
				// Replace the new code in the HTML document
				$patterns = array(
					chr(1) . '(<html.*\s+xml:lang=")(' . $code . ')(".*>)' . chr(1) . 'i',
					chr(1) . '(<html.*\s+lang=")(' . $code . ')(".*>)' . chr(1) . 'i',
				);
				$replace = array(
					'${1}' . strtolower($new_code) . '${3}',
					'${1}' . strtolower($new_code) . '${3}'
				);
			}
			else
			{
				$patterns = array();
				$replace  = array();
			}

			// Replace codes in <link hreflang="" /> attributes
			preg_match_all(chr(1) . '(<link.*\s+hreflang=")([0-9a-z\-]*)(".*\s+rel="alternate".*/>)' . chr(1) . 'i', $body, $matches);
			foreach ($matches[2] as $match)
			{
				$new_code = $this->params->get(strtolower($match));
				if ($new_code)
				{
					$patterns[] = chr(1) . '(<link.*\s+hreflang=")(' . $match . ')(".*\s+rel="alternate".*/>)' . chr(1) . 'i';
					$replace[] = '${1}' . $new_code . '${3}';
				}
			}

			preg_match_all(chr(1) . '(<link.*\s+rel="alternate".*\s+hreflang=")([0-9A-Za-z\-]*)(".*/>)' . chr(1) . 'i', $body, $matches);
			foreach ($matches[2] as $match)
			{
				$new_code = $this->params->get(strtolower($match));
				if ($new_code)
				{
					$patterns[] = chr(1) . '(<link.*\s+rel="alternate".*\s+hreflang=")(' . $match . ')(".*/>)' . chr(1) . 'i';
					$replace[] = '${1}' . $new_code . '${3}';
				}
			}

			App::get('response')->setContent(preg_replace($patterns, $replace, $body));
		}
	}

	/**
	 * Injects language selection into a form
	 *
	 * @param   object   $form  The form to be altered.
	 * @param   array    $data  The associated data for the form.
	 * @return  boolean
	 */
	public function onContentPrepareForm($form, $data)
	{
		// Check we have a form
		if (!($form instanceof Hubzero\Form\Form))
		{
			$this->setError('JERROR_NOT_A_FORM');
			return false;
		}

		// Check we are manipulating a valid form.
		if ($form->getName() != 'com_plugins.plugin'
			|| isset($data->name) && $data->name != 'plg_system_languagecode'
			|| empty($data) && !User::getState('plg_system_language_code.edit')
		)
		{
			return true;
		}

		// Mark the plugin as being edited
		User::setState('plg_system_language_code.edit', $data->name == 'plg_system_languagecode');

		// Get site languages
		$languages = Lang::getKnownLanguages(PATH_CORE);

		// Inject fields into the form
		foreach ($languages as $tag => $language)
		{
			$form->load('
<form>
	<fields name="params">
		<fieldset
			name="languagecode"
			label="PLG_SYSTEM_LANGUAGECODE_FIELDSET_LABEL"
			description="PLG_SYSTEM_LANGUAGECODE_FIELDSET_DESC"
		>
			<field
				name="'.strtolower($tag).'"
				type="text"
				description="' . htmlspecialchars(Lang::txt('PLG_SYSTEM_LANGUAGECODE_FIELD_DESC', $language['name']), ENT_COMPAT, 'UTF-8') . '"
				translate_description="false"
				label="' . $tag . '"
				translate_label="false"
				size="7"
				filter="cmd"
			/>
		</fieldset>
	</fields>
</form>
			');
		}
		return true;
	}
}
