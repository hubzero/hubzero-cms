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
		if (!($form instanceof JForm))
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
