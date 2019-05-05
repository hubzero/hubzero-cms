<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Element;

use Components\Publications\Models\Element as Base;
use Lang;

/**
 * Renders a languages element
 */
class Languages extends Base
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Language List';

	/**
	 * Language list
	 *
	 * @var  array
	 */
	protected $_codes = array(
		"aa" => "Afar",
		"ab" => "Abkhazian",
		"ae" => "Avestan",
		"af" => "Afrikaans",
		"ak" => "Akan",
		"am" => "Amharic",
		"an" => "Aragonese",
		"ar" => "Arabic",
		"as" => "Assamese",
		"av" => "Avaric",
		"ay" => "Aymara",
		"az" => "Azerbaijani",
		"ba" => "Bashkir",
		"be" => "Belarusian",
		"bg" => "Bulgarian",
		"bh" => "Bihari",
		"bi" => "Bislama",
		"bm" => "Bambara",
		"bn" => "Bengali",
		"bo" => "Tibetan",
		"br" => "Breton",
		"bs" => "Bosnian",
		"ca" => "Catalan",
		"ce" => "Chechen",
		"ch" => "Chamorro",
		"co" => "Corsican",
		"cr" => "Cree",
		"cs" => "Czech",
		"cu" => "Church Slavic",
		"cv" => "Chuvash",
		"cy" => "Welsh",
		"da" => "Danish",
		"de" => "German",
		"dv" => "Divehi",
		"dz" => "Dzongkha",
		"ee" => "Ewe",
		"el" => "Greek",
		"en" => "English",
		"eo" => "Esperanto",
		"es" => "Spanish",
		"et" => "Estonian",
		"eu" => "Basque",
		"fa" => "Persian",
		"ff" => "Fulah",
		"fi" => "Finnish",
		"fj" => "Fijian",
		"fo" => "Faroese",
		"fr" => "French",
		"fy" => "Western Frisian",
		"ga" => "Irish",
		"gd" => "Scottish Gaelic",
		"gl" => "Galician",
		"gn" => "Guarani",
		"gu" => "Gujarati",
		"gv" => "Manx",
		"ha" => "Hausa",
		"he" => "Hebrew",
		"hi" => "Hindi",
		"ho" => "Hiri Motu",
		"hr" => "Croatian",
		"ht" => "Haitian",
		"hu" => "Hungarian",
		"hy" => "Armenian",
		"hz" => "Herero",
		"ia" => "Interlingua",
		"id" => "Indonesian",
		"ie" => "Interlingue",
		"ig" => "Igbo",
		"ii" => "Sichuan Yi",
		"ik" => "Inupiaq",
		"io" => "Ido",
		"is" => "Icelandic",
		"it" => "Italian",
		"iu" => "Inuktitut",
		"ja" => "Japanese",
		"jv" => "Javanese",
		"ka" => "Georgian",
		"kg" => "Kongo",
		"ki" => "Kikuyu",
		"kj" => "Kwanyama",
		"kk" => "Kazakh",
		"kl" => "Kalaallisut",
		"km" => "Khmer",
		"kn" => "Kannada",
		"ko" => "Korean",
		"kr" => "Kanuri",
		"ks" => "Kashmiri",
		"ku" => "Kurdish",
		"kv" => "Komi",
		"kw" => "Cornish",
		"ky" => "Kirghiz",
		"la" => "Latin",
		"lb" => "Luxembourgish",
		"lg" => "Ganda",
		"li" => "Limburgish",
		"ln" => "Lingala",
		"lo" => "Lao",
		"lt" => "Lithuanian",
		"lu" => "Luba-Katanga",
		"lv" => "Latvian",
		"mg" => "Malagasy",
		"mh" => "Marshallese",
		"mi" => "Maori",
		"mk" => "Macedonian",
		"ml" => "Malayalam",
		"mn" => "Mongolian",
		"mr" => "Marathi",
		"ms" => "Malay",
		"mt" => "Maltese",
		"my" => "Burmese",
		"na" => "Nauru",
		"nb" => "Norwegian Bokmal",
		"nd" => "North Ndebele",
		"ne" => "Nepali",
		"ng" => "Ndonga",
		"nl" => "Dutch",
		"nn" => "Norwegian Nynorsk",
		"no" => "Norwegian",
		"nr" => "South Ndebele",
		"nv" => "Navajo",
		"ny" => "Chichewa",
		"oc" => "Occitan",
		"oj" => "Ojibwa",
		"om" => "Oromo",
		"or" => "Oriya",
		"os" => "Ossetian",
		"pa" => "Panjabi",
		"pi" => "Pali",
		"pl" => "Polish",
		"ps" => "Pashto",
		"pt" => "Portuguese",
		"qu" => "Quechua",
		"rm" => "Raeto-Romance",
		"rn" => "Kirundi",
		"ro" => "Romanian",
		"ru" => "Russian",
		"rw" => "Kinyarwanda",
		"sa" => "Sanskrit",
		"sc" => "Sardinian",
		"sd" => "Sindhi",
		"se" => "Northern Sami",
		"sg" => "Sango",
		"si" => "Sinhala",
		"sk" => "Slovak",
		"sl" => "Slovenian",
		"sm" => "Samoan",
		"sn" => "Shona",
		"so" => "Somali",
		"sq" => "Albanian",
		"sr" => "Serbian",
		"ss" => "Swati",
		"st" => "Southern Sotho",
		"su" => "Sundanese",
		"sv" => "Swedish",
		"sw" => "Swahili",
		"ta" => "Tamil",
		"te" => "Telugu",
		"tg" => "Tajik",
		"th" => "Thai",
		"ti" => "Tigrinya",
		"tk" => "Turkmen",
		"tl" => "Tagalog",
		"tn" => "Tswana",
		"to" => "Tonga",
		"tr" => "Turkish",
		"ts" => "Tsonga",
		"tt" => "Tatar",
		"tw" => "Twi",
		"ty" => "Tahitian",
		"ug" => "Uighur",
		"uk" => "Ukrainian",
		"ur" => "Urdu",
		"uz" => "Uzbek",
		"ve" => "Venda",
		"vi" => "Vietnamese",
		"vo" => "Volapuk",
		"wa" => "Walloon",
		"wo" => "Wolof",
		"xh" => "Xhosa",
		"yi" => "Yiddish",
		"yo" => "Yoruba",
		"za" => "Zhuang",
		"zh" => "Chinese",
		"zu" => "Zulu"
	);

	/**
	 * Return any options this element may have
	 *
	 * @param   string  $name          Name of the field
	 * @param   string  $value         Value to check against
	 * @param   object  $element       Data Source Object.
	 * @param   string  $control_name  Control name (eg, control[fieldname])
	 * @return  string  HTML
	 */
	public function fetchElement($name, $value, &$element, $control_name)
	{
		if (!$value)
		{
			$language = Lang::detect();
			$language = explode('-', $language);
			$value = $language[0];
		}

		$languages = array();
		foreach ($this->_codes as $code => $lang)
		{
			$languages[] = \Html::select('option', $code, $lang);
		}

		array_unshift($languages, \Html::select('option', '', '- '.Lang::txt('Select Language').' -'));

		return '<span class="field-wrap">' . \Html::select('genericlist', $languages, $control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.'-'.$name) . '</span>';
	}

	/**
	 * Display the language for a language code
	 *
	 * @param   string  $value   Data
	 * @return  string  Formatted string.
	 */
	public function display($value)
	{
		$value = trim(strip_tags($value));
		return (isset($this->_codes[$value]) ? $this->_codes[$value] : $value);
	}
}
