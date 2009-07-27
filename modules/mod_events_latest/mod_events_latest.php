<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Parameters:
// ===========
//
// maxEvents = max. no. of events to display in the module (1 to 10, default is 5)
//
// mode:
// = 0  (default) display events for current week and following week only up to 'maxEvents'.
//
// = 1  same as 'mode'=0 except some past events for the current week will also be
//      displayed if num of future events is less than $maxEvents.
//
// = 2  display events for +'days' range relative to current day up to $maxEvents.
//
// = 3  same as mode 2 except if there are < 'maxEvents' in the range,
//      then display past events within -'days' range.
//
// = 4  display events for current month up to 'maxEvents'.
//
// days: (default=7) range of days relative to current day to display events for mode 1 or 3.
//
// displayLinks = 1 (default is 0) display event titles as links to the 'view_detail' com_events
//                   task which will display details of the event.
//
// displayYear = 1 (default is 0) display year when displaying dates in the non-customized event's listing.
//
// New for rev 1.1:
//
// disableDateStyle = 1 (default is 0) disables the application of the css style 'mod_events_latest_date' to
//                  the displayed events.  Use this when full customization of the display format is desired.
//                  See customFormat parameter below.
//
// disableTitleStyle = 1 (default is 0) disables the application of the css style 'mod_events_latest_title' to
//                  the displayed event's title.  Use this when full customization of the display format is desired.
//                  See customFormat parameter below.
//
// customFormatStr = string (default is null).  allows a customized specification of the desired event fields and
//                format to be used to display the event in the module.  The string can specify html directly.
//                As well, certain event fields can be specified as ${event_field} in the string.  If desired,
//                the user can even specify overriding inline styles in the event format using <div> or <span>
//                to delineate.  Or the <div>'s or <span>'s can actually reference new css style classes which you
//                can create in the template css file.
//                The ${startDate} and ${endDate} are special event fields which can support further customization
//                of the date and time display by allowing a user to specify exactly how to display the date with
//                identical format control codes to the PHP 'date()' function.
//
//                Event fields available:
//
//                ${startDate}, ${endDate}, ${eventDate}, ${title}, ${category}, ${contact}, ${content}, ${addressInfo}, ${extraInfo},
//                ${createdByAlias}, ${createdByUserName}, ${createdByUserEmail}, ${createdByUserEmailLink},
//                ${eventDetailLink}, ${color}
//
//                ${startDate}, ${eventDate} and ${endDate} can also specify a format in the form of a strftime() format or a
//                date() function format.  If a '%' sign is detected in the format string, strftime() is assumed
//                to be used (supports locale international dates).  An example of a format used:
//                ${startDate('D, M jS, Y, @g:ia')}
//
// Note that the default customFormatStr is '${eventDate}<br />${title}' which will almost display the same information
// and in the same format as in rev 1.11.  ${eventDate} is the actual date of an event within an event's
// start and end publish date ranges.  This more accurately reflects a multi-day event's actual date.

if (!class_exists('modEventsLatest')) {
	class modEventsLatest
	{
		private $attributes = array();

		//-----------

		public function __set($property, $value)
		{
			$this->attributes[$property] = $value;
		}

		//-----------

		public function __get($property)
		{
			if (isset($this->attributes[$property])) {
				return $this->attributes[$property];
			}
		}

		//-----------

		public function display()
		{
			// Check the events component
			if (file_exists( JPATH_ROOT.DS.'components'.DS.'com_events'.DS.'events.html.php' ) ) { 
				include_once( JPATH_ROOT.DS.'components'.DS.'com_events'.DS.'events.html.php' );
				include_once( JPATH_ROOT.DS.'components'.DS.'com_events'.DS.'events.date.php');
				include_once( JPATH_ROOT.DS.'components'.DS.'com_events'.DS.'events.repeat.php');
			} else {
				return JText::_('EVENTS_COMPONENT_REQUIRED');
			}

			$database =& JFactory::getDBO();

			// Get the module parameters
			$params =& $this->params;

			ximport('xdocument');
			XDocument::addModuleStyleSheet('mod_events_latest');

			return $this->displayLatestEvents($module, $database, $params);
		}

		//-----------
		// This custom sort compare function compares the start times of events that are refernced by the a & b vars
		public function cmpByStartTime(&$a, &$b) 
		{
			list($date, $aStrtTime) = split(' ',$a->publish_up);
			list($date, $bStrtTime) = split(' ',$b->publish_up);
			if ($aStrtTime == $bStrtTime) return 0;
			return ($aStrtTime > $bStrtTime) ? -1 : 1;
		}

		//-----------
		// The function below is essentially the 'ShowEventsByDate' function in the com_events module,
		// except no actual output is performed.  Rather this function returns an array of references to
		// $rows within the $rows (ie events) input array which occur on the input '$date'.  This
		// is determined by the complicated com_event algorithm according to the event's repeatting type.
		private function getEventsByDate(&$rows, $date, &$seenThisEvent, $noRepeats) 
		{
	    	$num_events = count($rows);
	    	$new_rows_events = array();

			$eventCheck = new EventsRepeat;

			if ($num_events>0) {
				$year = date('Y', $date);
		    	$month = date('m', $date);
		    	$day = date('d', $date);

				for ($r = 0; $r < count($rows); $r++) 
				{
					$row = $rows[$r];
					if (isset($seenThisEvent[$row->id]) && $noRepeats) continue;
					if ($eventCheck->EventsRepeat($row, $year, $month, $day)){
						$seenThisEvent[$row->id] = 1;
						$new_rows_events[] =& $rows[$r];
					}
				}

				usort($new_rows_events, array('modEventsLatest','cmpByStartTime'));
			}

			return $new_rows_events;
		}

		//-----------

		private function displayLatestEvents(&$module, &$database, $params)
		{
			// Get the user GID (used in some queries)
			$juser =& JFactory::getUser();
			$gid = $juser->get('gid');

			// Get the site language setting
			$lang =& JFactory::getLanguage();
			$Config_lang = $lang->getBackwardLang();

			// Get module parameters
			$mode              = $params->get( 'mode' )                ? abs(intval($params->get( 'mode' ))) : 4;
			$days              = $params->get( 'days' )                ? abs(intval($params->get( 'days' ))) : 7;
			$maxEvents         = $params->get( 'max_events' )          ? abs(intval($params->get( 'max_events' ))) : 5;
			$displayLinks      = $params->get( 'display_links' )       ? abs(intval($params->get( 'display_links' ))) : 0;
			$displayYear       = $params->get( 'display_year' )        ? abs(intval($params->get( 'display_year' ))) : 0;
			$disableTitleStyle = $params->get( 'display_title_style' ) ? abs(intval($params->get( 'display_title_style' ))) : 0;
			$disableDateStyle  = $params->get( 'display_date_style' )  ? abs(intval($params->get( 'display_date_style' ))) : 0;
			$customFormatStr   = $params->get( 'custom_format_str' )   ? $params->get( 'custom_format_str' ) : NULL;
			$norepeat          = $params->get( 'no_repeat' )           ? abs(intval($params->get( 'no_repeat' ))) : 0;
			$charlimit         = $params->get( 'char_limit' )          ? abs(intval($params->get( 'char_limit' ))) : 150;
			$announcements     = $params->get( 'announcements' )       ? abs(intval($params->get( 'announcements' ))) : 0;

			// Can't have a mode greater than 4
			if ($mode > 4) {
				$mode = 0;
			}

			// Hardcoded to 10 for now to avoid bad mistakes in params
			if (!$maxEvents || $maxEvents > 100) {
				$maxEvents = 10;
			}

			// Derive the event date range we want based on current date and form the db query.
			$todayBegin = date('Y-m-d')." 00:00:00";
			$yesterdayEnd = date('Y-m-d', mktime(0,0,0,date('m'),date('d') - 1, date('Y')))." 23:59:59";

			// Get the start day
			$startday = $params->get( 'start_day' );
			if (!defined('_CAL_CONF_STARDAY')) {
				define('_CAL_CONF_STARDAY',$startday);
			}

			// Set some vars depending upon mode
			switch ($mode)
			{
				case 0:
				case 1:
					// week start (ie. Sun or Mon) is according to what has been selected in the events
					// component configuration thru the events admin interface.
					//if (!defined(_CAL_CONF_STARDAY)) define(_CAL_CONF_STARDAY, 0);
					$numDay = (date("w")-_CAL_CONF_STARDAY + 7)%7;
					// begin of this week
					$beginDate = date('Y-m-d', mktime(0,0,0,date('m'),date('d') - $numDay, date('Y')))." 00:00:00";
					//$thisWeekEnd = date('Y-m-d', mktime(0,0,0,date('m'),date('d') - date('w')+6, date('Y'))." 23:59:59";
					// end of next week
					$endDate = date('Y-m-d', mktime(0,0,0,date('m'),date('d') - $numDay + 13, date('Y')))." 23:59:59";
					break;
				case 2:
				case 3:
					// Begin of today - $days
					$beginDate = date('Y-m-d', mktime(0,0,0,date('m'),date('d') - $days, date('Y')))." 00:00:00";
					// End of today + $days
					$endDate = date('Y-m-d', mktime(0,0,0,date('m'),date('d') + $days, date('Y')))." 23:59:59";
					break;
				case 4:
				default:
					// Beginning of this month
					//$beginDate = date('Y-m-d', mktime(0,0,0,date('m'),1, date('Y')))." 00:00:00";
					//start today
					$beginDate = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 00:00:00";
					// end of this month
					//$endDate = date('Y-m-d', mktime(0,0,0,date('m')+1,0, date('Y')))." 23:59:59";
					// end of this year
					$endDate = date('Y-m-d', mktime(0,0,0,date('m'),0, date('Y')+1))." 23:59:59";
					break;
			}

			switch ($announcements) 
			{
				case 2:
					$ancmnt = "AND #__events.announcement='1'";
				break;
				case 1:
					$ancmnt = "AND #__events.announcement!='1'";
				break;
				case 0:
				default:
					$ancmnt = "";
				break;
			}

			// Display only events that are not announcements
			$query = "SELECT #__events.* FROM #__events, #__categories as b"
				. "\nWHERE #__events.catid = b.id AND b.access <= $gid AND #__events.access <= $gid AND (#__events.state='1' $ancmnt AND #__events.checked_out='0')"
				. "\n	AND ((publish_up <= '$todayBegin%' AND publish_down >= '$todayBegin%')"
				. "\n	OR (publish_up <= '$endDate%' AND publish_down >= '$endDate%')"
				. "\n   OR (publish_up <= '$endDate%' AND publish_up >= '$todayBegin%')"
				. "\n   OR (publish_down <= '$endDate%' AND publish_down >= '$todayBegin%'))"
				. "\nORDER BY publish_up ASC";

			// Retrieve the list of returned records as an array of objects
			$database->setQuery( $query );
			$rows = $database->loadObjectList();

			// Determine the events that occur each day within our range
			$events = 0;
			$date = mktime(0,0,0);
			$lastDate = mktime(0,0,0,intval(substr($endDate,5,2)),intval(substr($endDate,8,2)),intval(substr($endDate,0,4)));
			$i = 0;

			$content  = '';
			$seenThisEvent = array();

			if (count($rows)) {
				while ($date <= $lastDate)
				{
					// Get the events for this $date
					$eventsThisDay = $this->getEventsByDate($rows, $date, $seenThisEvent, $norepeat);
					if (count($eventsThisDay)) {
						// dmcd May 7/04  bug fix to not exceed maxEvents
						$eventsToAdd = min($maxEvents-$events, count($eventsThisDay));
						$eventsThisDay = array_slice($eventsThisDay, 0, $eventsToAdd);
						$eventsByRelDay[$i] = $eventsThisDay;
						$events += count($eventsByRelDay[$i]);
					}
					if ($events >= $maxEvents) break;
					$date = mktime(0,0,0,date('m', $date),date('d', $date)+1,date('Y', $date));
					$i++;
				}
			}

			// Do we actually have any events to display?
			if ($events < $maxEvents && ($mode==1 || $mode==3)) {
				// display some recent previous events too up to a total of $maxEvents
				//Changed by Swaroop to display only events that are not announcements
				$query = "SELECT #__events.* FROM #__events, #__categories as b"
					. "\nWHERE #__events.catid = b.id AND b.access <= $gid AND #__events.access <= $gid AND (#__events.state='1' $ancmnt AND #__events.checked_out='0')"
					. "\n	AND ((publish_up <= '$beginDate%' AND publish_down >= '$beginDate%')"
					. "\n	OR (publish_up <= '$yesterdayEnd%' AND publish_down >= '$yesterdayEnd%')"
					. "\n   OR (publish_up <= '$yesterdayEnd%' AND publish_up >= '$beginDate%')"
					. "\n   OR (publish_down <= '$yesterdayEnd%' AND publish_down >= '$beginDate%'))"
					. "\n  ORDER BY publish_up DESC";

				// initialise the query in the $database connector
				// this translates the '#__' prefix into the real database prefix
				$database->setQuery( $query );

				// retrieve the list of returned records as an array of objects
				$prows = $database->loadObjectList();

				if (count($prows)) {
					// start from yesterday
					$date = mktime(23,59,59,date('m'),date('d')-1,date('Y'));
					$lastDate = mktime(0,0,0,intval(substr($beginDate,5,2)),intval(substr($beginDate,8,2)),intval(substr($beginDate,0,4)));
					$i=-1;

					while ($date >= $lastDate)
					{
						// get the events for this $date
						$eventsThisDay = $this->getEventsByDate($prows, $date, $seenThisEvent, $norepeat);
						if (count($eventsThisDay)) {
							$eventsByRelDay[$i] = $eventsThisDay;
							$events += count($eventsByRelDay[$i]);
						}
						if ($events >= $maxEvents) break;
						$date = mktime(0,0,0,date('m', $date),date('d', $date)-1,date('Y', $date));
						$i--;
					}
				}
			}

			// initialize name of com_events module and task defined to view
			// event detail.  Note that these could change in future com_event
			// component revisions!!  Note that the '$itemId' can be left out in
			// the link parameters for event details below since the event.php
			// component handler will fetch its own id from the db menu table
			// anyways as far as I understand it.
			$com_events = 'com_events';
			$task_events = 'details';

			if (isset($eventsByRelDay) && count($eventsByRelDay)) {
				// Now to display these events, we just start at the smallest index of the $eventsByRelDay array
				// and work our way up.
				ksort($eventsByRelDay, SORT_NUMERIC);
				reset($eventsByRelDay);

				$firstTime = true;

				// Note we MUST get the $Itemid value for the events component
				// here, or some things can break.
				//$database->setQuery("SELECT id FROM #__menu WHERE link = 'index.php?option=$com_events'");
				//$Itemid = $database->loadResult();

				// Get the com_events category names from the categories mos table
				$database->setQuery("SELECT id, name, title, image FROM #__categories WHERE section= 'com_events' AND published='1'");
				$category = $database->loadObjectList('id');

				// Get the usernames and email addresses from the users mos table
				$database->setQuery("SELECT id, username, sendEmail, email FROM #__users WHERE block ='0'");
				$users = $database->loadObjectList('id');

				// Set the default format string
				$defaultfFormatStr= '${eventDate}<br />${title}';

				// See if $customFormatStr has been specified.  If not, set it to the default format of date followed by event title.
				if ($customFormatStr == NULL) {
					$customFormatStr = $defaultfFormatStr;
				} else {
					$customFormatStr = preg_replace('/^"(.*)"$/', "\$1", $customFormatStr);
					$customFormatStr = preg_replace("/^'(.*)'$/", "\$1", $customFormatStr);
					$customFormatStr = preg_replace('/"/','\"', $customFormatStr);  // escape all " within the string
				}

				// Parse the event variables and reformat them into php syntax with special handling for the startDate and endDate fields.
				$customFormat = $customFormatStr;
				$evalString = '"';

				while (preg_match('/\$\{(content|eventDetailLink|createdByAlias|color|createdByUserName|createdByUserEmail|createdByUserEmailLink|eventDate|endDate|startDate|title|category|contact|addressInfo|extraInfo|eventIcon)(\(([^\)]*)\))?[\t ]*\}(.*)$/',$customFormat,$matches))
				{
					$evalString .= substr($customFormat,0,strpos($customFormat,$matches[0]));

					switch ($matches[1])
					{
						case 'endDate':
						case 'startDate':
						case 'eventDate':
							// Note we need to examine the date specifiers used to determine if language translation will be
							// necessary.  Do this later when script is debugged.
							if (!$disableDateStyle) $evalString .= '<span class=\"mod_events_latest_date\">';
							if (!isset($matches[3]) || $matches[3] == ''){
								// no actual format specified, use default, eg. Fri Oct 12th, @7:30pm\
								// use the strftime function for international support
								if ($Config_lang == 'english'){
									$dateFormat = $displayYear ?  "'D, M jS, Y, @g:ia'": "'D, M jS, @g:ia'";
									$evalString .= '". date('.$dateFormat.',$'.$matches[1].')."';
								} else {
									$dateFormat = $displayYear ? "'%a %b %d, %Y @%I:%M%p'" : "'%a %b %d @%I:%M%p'";
									$evalString .= '". strftime('.$dateFormat.',$'.$matches[1].')."';
								}
							} else {
								$matches[3] = trim($matches[3]);
								// make sure the date format specifiers are surrounded by quotes
								if (!(preg_match('/^\'.*\'$/', $matches[3]) || preg_match('/^".*"$/', $matches[3])))
									$matches[3] = "'".$matches[3]."'";

								// if a '%' sign detected in date format string, we assume strftime() is to be used,
								if (preg_match("/\%/", $matches[3])) $evalString .= '". strftime('.$matches[3].', $'.$matches[1].')."';
								// otherwise the date() function is assumed.
								else $evalString .= '".date('.$matches[3].', $'.$matches[1].')."';
							}

							if (!$disableDateStyle) $evalString .= '</span>';
							break;
						case 'eventIcon':
							$evalString .= '".$eventIcon."';
							break;
						case 'title':
							if (!$disableTitleStyle) $evalString .= '<span class=\"mod_events_latest_content\">';
							if ($displayLinks) $evalString .= '<a href=\"index.php?option='.$com_events.'&task='.$task_events.'&id=".$dayEvent->id."\">';

							$evalString .= '".$dayEvent->title."';

							if ($displayLinks) $evalString .= '</a>';
							if (!$disableTitleStyle) $evalString .= '</span>';
							break;
						case 'category':
							$evalString .= '".$category[$dayEvent->catid]->name."';
							break;
						case 'contact':
							$evalString .= '".$dayEvent->contact_info."';
							break;
						case 'content':  // Added by Kaz McCoy 1-10-2004
							$evalString .= '".substr(strip_tags($dayEvent->content), 0, $charlimit)." ...';
							break;
						case 'addressInfo':
							$evalString .= '".$dayEvent->adresse_info."';
							break;
						case 'extraInfo':
							$evalString .= '".$dayEvent->extra_info."';
							break;
						case 'createdByAlias':
							$evalString .= '".$dayEvent->created_by_alias."';
							break;
						case 'createdByUserName':
							$evalString .= '".$users[$dayEvent->created_by]->username."';
							break;
						case 'createdByUserEmail':
							// Note that users email address will NOT be available if they don't want to receive email
							$evalString .= $users[$dayEvent->created_by]->sendEmail ? '". $users[$dayEvent->created_by]->email."' : '';
							break;
						case 'createdByUserEmailLink':
							// Note that users email address will NOT be available if they don't want to receive email
							$evalString .= JRoute::_("index.php?option=".$com_events."&amp;task=".$task_events."&amp;id=".$dayEvent->id);
							break;
						case 'color':
							$evalString .= '".$dayEvent->color_bar."';
							break;
						case 'eventDetailLink':
							$evalString .= "'index.php?option=".$com_events."&amp;task=".$task_events."&amp;id=".$dayEvent->id."'";
							break;
						default:
							break;
					}
					$customFormat = $matches[4];
				}

				// If no event variables were found, I guess we just print the custom string.  Let the user figure it out
				if ($evalString == '"') {
					$evalString .= $customFormatStr . '"';
				} else {
					$evalString .= $customFormat. '"';
				}

				// Counter for alternating row colors
				$row_counter = 0;
				foreach ($eventsByRelDay as $relDay => $daysEvents)
				{
					reset($daysEvents);

					// Get all of the events for this day
					foreach ($daysEvents as $dayEvent)
					{
						// Get the title and start time
						$startDate = $dayEvent->publish_up;
						$eventDate = mktime(substr($startDate,11,2),substr($startDate,14,2), substr($startDate,17,2),date('m'),date('d') + $relDay,date('Y'));
						$startDate = mktime(substr($startDate,11,2),substr($startDate,14,2), substr($startDate,17,2), substr($startDate,5,2), substr($startDate,8,2), substr($startDate,0,4));
						$endDate = $dayEvent->publish_down;
						$endDate = mktime(substr($endDate,11,2),substr($endDate,14,2), substr($endDate,17,2), substr($endDate,5,2), substr($endDate,8,2), substr($endDate,0,4));

						$year = date('Y', $startDate);
						$month = date('m', $startDate);
						$day = date('d', $startDate);

						// added by mindy
						// check event category
						$eventType = $dayEvent->catid;

						// add appropriate icon: Swaroop
						$eventImage = $category[$dayEvent->catid]->title;
						$eventIcon = "<img src=\"components/com_events/images/event_".$eventImage.".gif\" width=\"19\" height=\"18\" alt=\"icon\" />";

						//commented by mindy
						//if($firstTime) $content .= "<tr><td class='mod_events_latest_first'>";
						//else 
						// Alternating row colors
						if ($row_counter & 1) {
							$content .= ' <tr class="odd';
						} else {
							$content .= ' <tr class="even';
						}
						if ($dayEvent->announcement == 1) {
							$content .= ' announcement';
						}
						$content .= '">'."\n";

						if ($firstTime && @eval('$eventStr = '.$evalString.";") != NULL) {
							//$evalString = '"<span class=\'mod_events_latest_date\'>". date(\'D, M jS, @g:ia\',$startDate)."</span><br /><span class=\'mod_events_latest_content\'>".$dayEvent->title."</span>"';
							//$content .= "<span style='color:#ff0000;font-weight:bold;'>Syntax Error in Parameter 'customFormatStr'</span></td></tr><tr><td class='mod_events_latest'>";
						}
						eval('$eventStr = '.$evalString.";");

						// Make URLs web standard compliant
						//$eventStr = str_replace('&amp;','&',$eventStr);
						//$eventStr = str_replace('&','&amp;',$eventStr);
						$eventStr = $this->ampReplace($eventStr);

						$content .= $eventStr."\n".' </tr>'."\n";
						$firstTime = false;
						$row_counter++;
					}
				}
			} else {
				// Do nothing if there are no events to display
				$content .= '<tr><td class="mod_events_latest_noevents">'.JText::_('NO_EVENTS_FOUND').'</td></tr>'."\n"; 
			}

			// Build the final output
			$html = '';
			if ($content) {
				$html .= '<table class="latest_events_tbl" summary="'.JText::_('MOD_EVENTS_LATEST_TABLE_SUMMARY').'">'."\n";
				$html .= ' <tbody>'."\n";
				$html .= stripslashes($content);
				$html .= ' </tbody>'."\n";
				$html .= '</table>'."\n\n";
			}
			$html .= '<p class="more"><a href="'.JRoute::_('index.php?option='.$com_events.'&year='.strftime("%Y", time() ).'&month='.strftime("%m", time() )).'">'.JText::_('MORE_EVENTS').'</a></p>'."\n";
			//$html .= '<p class="more"><a href="'.JRoute::_('index.php?option='.$com_events.'&task=add').'">'.JText::_('SUBMIT_EVENT').'</a></p>'."\n";

			return $html;
		}

		private function ampReplace( $text )
		{
			$text = str_replace( '&&', '*--*', $text );
			$text = str_replace( '&#', '*-*', $text );
			$text = str_replace( '&amp;', '&', $text );
			$text = preg_replace( '|&(?![\w]+;)|', '&amp;', $text );
			$text = str_replace( '*-*', '&#', $text );
			$text = str_replace( '*--*', '&&', $text );

			return $text;
		}
	}
}

//-------------------------------------------------------------

$modeventslatest = new modEventsLatest();
$modeventslatest->params = $params;

require( JModuleHelper::getLayoutPath('mod_events_latest') );
?>
