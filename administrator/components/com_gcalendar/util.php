<?php
/**
 * GCalendar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GCalendar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GCalendar.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Allon Moritz
 * @copyright 2007-2011 Allon Moritz
 * @since 2.2.0
 */

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'dbutil.php');

if(!class_exists('Mustache')){
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'libraries'.DS.'mustache'.DS.'Mustache.php');
}

class GCalendarUtil{

	public static function getComponentParameter($key, $defaultValue = null){
		$params = JComponentHelper::getParams('com_gcalendar');
		$value = $params->get($key, $defaultValue);

		if($key == 'timezone' && empty($value)) {
			$user = JFactory::getUser();
			if($user->get('id')) {
				$value = $user->getParam('timezone');
			}
			if(empty($value)){
				$value = JFactory::getApplication()->getCfg('offset', 'UTC');
			}
		}
		return $value;
	}

	public static function getFrLanguage(){
		$conf = JFactory::getConfig();
		return $conf->getValue('config.language');
	}

	public static function getItemId($calendarId, $strict = false){
		$component = JComponentHelper::getComponent('com_gcalendar');
		$menu = JFactory::getApplication()->getMenu();
		$items = $menu->getItems('component_id', $component->id);

		$default = null;
		if (is_array($items)){
			foreach($items as $item) {
				$default = $item;
				$paramsItem	= $menu->getParams($item->id);
				$calendarids = $paramsItem->get('calendarids');
				if(empty($calendarids)){
					$results = GCalendarDBUtil::getAllCalendars();
					if($results){
						$calendarids = array();
						foreach ($results as $result) {
							$calendarids[] = $result->id;
						}
					}
				}
				$contains_gc_id = FALSE;
				if ($calendarids){
					if( is_array( $calendarids ) ) {
						$contains_gc_id = in_array($calendarId,$calendarids);
					} else {
						$contains_gc_id = $calendarId == $calendarids;
					}
				}
				if($contains_gc_id){
					return $item->id;
				}
			}
		}
		if($strict = true){
			return null;
		}
		return $default;
	}

	public static function renderEvents(array $events = null, $output, $params = null, $eventParams = array()){
		if($events === null){
			$events = array();
		}

		JFactory::getLanguage()->load('com_gcalendar', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar');

		$lastHeading = '';

		$configuration = array();
		$configuration['events'] = array();
		foreach ($events as $event) {
			if(!is_object($event)) {
				continue;
			}
			$variables = $eventParams;

			$itemID = GCalendarUtil::getItemId($event->getParam('gcid', null));
			if(!empty($itemID) && JRequest::getVar('tmpl', null) != 'component' && $event != null){
				$component = JComponentHelper::getComponent('com_gcalendar');
				$menu = JFactory::getApplication()->getMenu();
				$item = $menu->getItem($itemID);
				if($item !=null){
					$backLinkView = $item->query['view'];
					$dateHash = '';
					if($backLinkView == 'gcalendar'){
						$day = $event->getStartDate()->format('d', true);
						$month = $event->getStartDate()->format('m', true);
						$year = $event->getStartDate()->format('Y', true);
						$dateHash = '#year='.$year.'&month='.$month.'&day='.$day;
					}
				}
				$variables['calendarLink'] = JRoute::_('index.php?option=com_gcalendar&Itemid='.$itemID.$dateHash);
			}

			$itemID = GCalendarUtil::getItemId($event->getParam('gcid'));
			if(!empty($itemID)){
				$itemID = '&Itemid='.$itemID;
			}else{
				$menu = JFactory::getApplication()->getMenu();
				$activemenu = $menu->getActive();
				if($activemenu != null){
					$itemID = '&Itemid='.$activemenu->id;
				}
			}

			$variables['backlink'] = JRoute::_('index.php?option=com_gcalendar&view=event&eventID='.$event->getGCalId().'&gcid='.$event->getParam('gcid').$itemID);

			$variables['link'] = $event->getLink('alternate')->getHref();
			$variables['calendarcolor'] = $event->getParam('gccolor');

			// the date formats from http://php.net/date
			$dateformat = $params->get('event_date_format', 'm.d.Y');
			$timeformat = $params->get('event_time_format', 'g:i a');

			// These are the dates we'll display
			$startDate = $event->getStartDate()->format($dateformat, true);
			$startTime = $event->getStartDate()->format($timeformat, true);
			$endDate = $event->getEndDate()->format($dateformat, true);
			$endTime = $event->getEndDate()->format($timeformat, true);
			$dateSeparator = '-';

			$timeString = $startTime.' '.$startDate.' '.$dateSeparator.' '.$endTime.' '.$endDate;
			$copyDateTimeFormat = 'Ymd';
			switch($event->getDayType()){
				case GCalendar_Entry::SINGLE_WHOLE_DAY:
					$timeString = $startDate;
					$copyDateTimeFormat = 'Ymd';

					$startTime = '';
					$endTime = '';
					$dateSeparator = '';
					break;
				case GCalendar_Entry::SINGLE_PART_DAY:
					$timeString = $startDate.' '.$startTime.' '.$dateSeparator.' '.$endTime;
					$copyDateTimeFormat = 'Ymd\THis';
					break;
				case GCalendar_Entry::MULTIPLE_WHOLE_DAY:
					$tmp = clone $event->getEndDate();
					$tmp->modify('-1 day');
					$endDate = $tmp->format($dateformat, true);
					$timeString = $startDate.' '.$dateSeparator.' '.$endDate;
					$copyDateTimeFormat = 'Ymd';

					$startTime = '';
					$endTime = '';
					$dateSeparator = '';
					break;
				case GCalendar_Entry::MULTIPLE_PART_DAY:
					$timeString = $startTime.' '.$startDate.' '.$dateSeparator.' '.$endTime.' '.$endDate;
					$copyDateTimeFormat = 'Ymd\THis';
					break;
			}
			$variables['calendarName'] = $params->get('show_calendar_name', 1) == 1 ? $event->getParam('gcname') : null;
			$variables['title'] = $params->get('show_event_title', 1) == 1 ? (string)$event->getTitle() : null;
			if($params->get('show_event_date', 1) == 1){
				$variables['date'] = $timeString;
				$variables['startDate'] = $startDate;
				$variables['startTime'] = $startTime;
				$variables['endDate'] = $endDate;
				$variables['endTime'] = $endTime;
				$variables['dateseparator'] = $dateSeparator;

				$variables['month'] = strtoupper($event->getStartDate()->format('M', true));
				$variables['day'] = $event->getStartDate()->format('d', true);
			}
			$variables['modifieddate'] = $params->get('show_event_modified_date', 1) == 1 ? $event->getModifiedDate()->format($timeformat, true).' '.$event->getModifiedDate()->format($dateformat, true) : null;

			if($params->get('show_event_attendees', 2) == 1 && count($event->getWho()) > 0){
				$variables['hasAttendees'] = true;
				$variables['attendees'] = array();
				foreach ($event->getWho() as $a) {
					$variables['attendees'][] = array('name' => (string)$a->getValueString(), 'email' =>  base64_encode(str_replace('@','#',$a->getEmail())));
				}
			}
			$location = $event->getLocation();
			$variables['location'] = $params->get('show_event_location', 1) == 1 ? $location : null;
			if(!empty($location)){
				$variables['maplink'] = $params->get('show_event_location_map', 1) == 1 ? "http://maps.google.com/?q=".urlencode($location).'&hl='.substr(GCalendarUtil::getFrLanguage(),0,2).'&output=embed' : null;
			}

			if($params->get('show_event_description', 1) == 1) {
				$variables['description'] = (string)$event->getContent();
				if($params->get('event_description_format', 1) == 1) {
					$variables['description'] = preg_replace("@(src|href)=\"https?://@i",'\\1="', $event->getContent());
					$variables['description'] = nl2br(preg_replace("@(((f|ht)tp:\/\/)[^\"\'\>\s]+)@",'<a href="\\1" target="_blank">\\1</a>', $variables['description']));
				}
			}
			if($params->get('show_event_author', 2) == 1){
				$variables['hasAuthor'] = true;
				$variables['author'] = array();
				foreach ($event->getAuthor() as $author) {
					$variables['author'][] = array('name' => (string)$author->getName(), 'email' =>  base64_encode(str_replace('@','#',$author->getEmail())));
				}
			}

			if($params->get('show_event_copy_info', 1) == 1){
				$variables['copyGoogleUrl'] = 'http://www.google.com/calendar/render?action=TEMPLATE&text='.urlencode($event->getTitle());
				$variables['copyGoogleUrl'] .= '&dates='.$event->getStartDate()->format($copyDateTimeFormat).'%2F'.$event->getEndDate()->format($copyDateTimeFormat);
				$variables['copyGoogleUrl'] .= '&location='.urlencode($event->getLocation());
				$variables['copyGoogleUrl'] .= '&details='.urlencode($event->getContent());
				$variables['copyGoogleUrl'] .= '&hl='.GCalendarUtil::getFrLanguage().'&ctz=Etc/GMT';
				$variables['copyGoogleUrl'] .= '&sf=true&output=xml';

				$ical_timeString_start =  $startTime.' '.$startDate;
				$ical_timeString_start = strtotime($ical_timeString_start);
				$ical_timeString_end =  $endTime.' '.$endDate;
				$ical_timeString_end = strtotime($ical_timeString_end);
				$loc = $event->getLocation();
				$variables['copyOutlookUrl'] = JRoute::_("index.php?option=com_gcalendar&view=ical&format=raw&eventID=".$event->getGCalId().'&gcid='.$event->getParam('gcid'));
			}

			$groupHeading = $event->getStartDate()->format($params->get('grouping', ''), true);
			if ($groupHeading != $lastHeading) {
				$lastHeading = $groupHeading;
				$variables['header'] =  $groupHeading;
			}

			$variables['calendarLinkLabel'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_CALENDAR_BACK_LINK');
			$variables['calendarNameLabel'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_CALENDAR_NAME');
			$variables['titleLabel'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_EVENT_TITLE');
			$variables['dateLabel'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_WHEN');
			$variables['attendeesLabel'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_ATTENDEES');
			$variables['locationLabel'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_LOCATION');
			$variables['descriptionLabel'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_DESCRIPTION');
			$variables['authorLabel'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_AUTHOR');
			$variables['copyLabel'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_COPY');
			$variables['copyGoogleLabel'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_COPY_TO_MY_CALENDAR');
			$variables['copyOutlookLabel'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_COPY_TO_MY_CALENDAR_ICS');
			$variables['language'] = substr(GCalendarUtil::getFrLanguage(),0,2);

			$configuration['events'][] = $variables;
		}

		$configuration['emptyText'] = JText::_('COM_GCALENDAR_FIELD_CONFIG_EVENT_LABEL_NO_EVENT_TEXT');

		try{
			$m = new Mustache;
			return $m->render($output, $configuration);
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}

	public static function getFadedColor($color, $percentage = 85) {
		$percentage = 100 - $percentage;
		$rgbValues = array_map( 'hexDec', str_split( ltrim($color, '#'), 2 ) );

		for ($i = 0, $len = count($rgbValues); $i < $len; $i++) {
			$rgbValues[$i] = decHex( floor($rgbValues[$i] + (255 - $rgbValues[$i]) * ($percentage / 100) ) );
		}

		return '#'.implode('', $rgbValues);
	}

	public static function dayToString($day, $abbr = false)
	{
		$name = '';
		switch ($day) {
			case 0:
				$name = $abbr ? JText::_('SUN') : JText::_('SUNDAY');
				break;
			case 1:
				$name = $abbr ? JText::_('MON') : JText::_('MONDAY');
				break;
			case 2:
				$name = $abbr ? JText::_('TUE') : JText::_('TUESDAY');
				break;
			case 3:
				$name = $abbr ? JText::_('WED') : JText::_('WEDNESDAY');
				break;
			case 4:
				$name = $abbr ? JText::_('THU') : JText::_('THURSDAY');
				break;
			case 5:
				$name = $abbr ? JText::_('FRI') : JText::_('FRIDAY');
				break;
			case 6:
				$name = $abbr ? JText::_('SAT') : JText::_('SATURDAY');
				break;
		}
		return addslashes($name);
	}

	public static function monthToString($month, $abbr = false)
	{
		$name = '';
		switch ($month) {
			case 1:
				$name = $abbr ? JText::_('JANUARY_SHORT')	: JText::_('JANUARY');
				break;
			case 2:
				$name = $abbr ? JText::_('FEBRUARY_SHORT')	: JText::_('FEBRUARY');
				break;
			case 3:
				$name = $abbr ? JText::_('MARCH_SHORT')		: JText::_('MARCH');
				break;
			case 4:
				$name = $abbr ? JText::_('APRIL_SHORT')		: JText::_('APRIL');
				break;
			case 5:
				$name = $abbr ? JText::_('MAY_SHORT')		: JText::_('MAY');
				break;
			case 6:
				$name = $abbr ? JText::_('JUNE_SHORT')		: JText::_('JUNE');
				break;
			case 7:
				$name = $abbr ? JText::_('JULY_SHORT')		: JText::_('JULY');
				break;
			case 8:
				$name = $abbr ? JText::_('AUGUST_SHORT')	: JText::_('AUGUST');
				break;
			case 9:
				$name = $abbr ? JText::_('SEPTEMBER_SHORT')	: JText::_('SEPTEMBER');
				break;
			case 10:
				$name = $abbr ? JText::_('OCTOBER_SHORT')	: JText::_('OCTOBER');
				break;
			case 11:
				$name = $abbr ? JText::_('NOVEMBER_SHORT')	: JText::_('NOVEMBER');
				break;
			case 12:
				$name = $abbr ? JText::_('DECEMBER_SHORT')	: JText::_('DECEMBER');
				break;
		}
		return addslashes($name);
	}

	public static function getActions($calendarId = 0){
		$user  = JFactory::getUser();
		$result  = new JObject;

		if (empty($calendarId)) {
			$assetName = 'com_gcalendar';
		}
		else {
			$assetName = 'com_gcalendar.calendar.'.(int) $calendarId;
		}

		$actions = array('core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete');

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	// http://core.trac.wordpress.org/browser/trunk/wp-includes/formatting.php#L1461
	public static function transformUrl( $text ) {
		$ret = ' ' . $ret;
		// in testing, using arrays here was found to be faster
		$ret = preg_replace_callback('#([\s>])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
		$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
		$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
		// this one is not in an array because we need it to run last, for cleanup of accidental links within links
		$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
		$ret = trim($ret);
		return $ret;
	}
}