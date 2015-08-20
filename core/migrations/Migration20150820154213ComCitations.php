<?php

require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'format.php');
use Hubzero\Content\Migration\Base;
use Components\Citations\Models\Format;

/**
 * Migration script for adding IEEE and APA formats as defaults 
 **/
class Migration20150820154213ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// get all formats on the hub
		$formats = Components\Citations\Models\Format::all();
		$ieee = false; // flag for IEEE format
		$apa = false; // flag for APA format

		foreach ($formats as $format)
		{
			// check for IEEE format
			if (strtolower($format->style) == 'ieee')
			{
				$ieee = true;
			}

			// check for APA style
			if (strtolower($format->style) == 'apa')
			{
				$apa = true;
			}
		} // end foreach

		if (!$apa)
		{
			//insert apa
			$apaFormat = Components\Citations\Models\Format::oneOrNew(null);
			$apaFormat->set(array(
				'style'  => 'APA',
				'format' => '{AUTHORS}, {EDITORS} ({YEAR}), {TITLE/CHAPTER}, <i>{JOURNAL}</i>, <i>{BOOK TITLE}</i>, {EDITION}, {CHAPTER},
				{SERIES}, {PUBLISHER}, {ADDRESS}, <b>{VOLUME}</b>, <b>{ISSUE/NUMBER}</b>: {PAGES}, {ORGANIZATION}, {INSTITUTION},
				{SCHOOL}, {LOCATION}, {MONTH}, {ISBN/ISSN}, (DOI: {DOI}). Cited by: <a href=\'{SECONDARY LINK}\'>{SECONDARY COUNT}</a>'
				));

			$apaFormat->save();
		}

		if (!$ieee)
		{
			//insert ieee
			$ieeeFormat = Components\Citations\Models\Format::oneOrNew(null);
			$ieeeFormat->set(array(
				'style'  => 'IEEE',
				'format' => '{AUTHORS}, {EDITORS} ({YEAR}), {TITLE/CHAPTER}, <i>{JOURNAL}</i>, <i>{BOOK TITLE}</i>, {EDITION}, {CHAPTER},
				{SERIES}, {PUBLISHER}, {ADDRESS}, <b>{VOLUME}</b>, <b>{ISSUE/NUMBER}</b>: {PAGES}, {ORGANIZATION}, {INSTITUTION},
				{SCHOOL}, {LOCATION}, {MONTH}, {ISBN/ISSN}, (DOI: {DOI})'
				));

			$ieeeFormat->save();
		}
	} // end up()

	/**
	 * Down
	 **/
	public function down()
	{
		// get all formats on the hub
		$formats = Components\Citations\Models\Format::all();
		$ieee = false; // flag for IEEE format
		$apa = false; // flag for APA format

		foreach ($formats as $format)
		{
			// check for IEEE format
			if (strtolower($format->style) == 'ieee')
			{
				$ieee = $format->id;
			}

			// check for APA style
			if (strtolower($format->style) == 'apa')
			{
				$apa = $format->id;
			}
		} // end foreach

		if ($apa)
		{
			//insert apa
			$apaFormat = Components\Citations\Models\Format::oneOrFail($apa);
			$apaFormat->destroy();
		}

		if ($ieee)
		{
			//insert ieee
			$ieeeFormat = Components\Citations\Models\Format::oneOrFail($ieee);
			$ieeeFormat->destroy();
		}
	}
}
