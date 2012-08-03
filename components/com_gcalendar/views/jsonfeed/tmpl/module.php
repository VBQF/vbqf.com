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

$document = JFactory::getDocument();
$document->setMimeEncoding('application/json');

$tmp = array();
if(!empty($this->calendars)){
	foreach ($this->calendars as $calendar){
		if(empty($calendar)){
			continue;
		}
		foreach ($calendar as $item) {
			$date = $item->getStartDate()->format('Y-m-d', true);
			if(!key_exists($date, $tmp)){
				$tmp[$date] = array();
			}
			$tmp[$date][] = $item;
		}
	}
}

$params = clone JComponentHelper::getParams('com_gcalendar');
$params->set('show_event_title', 1);
$data = array();
foreach ($tmp as $date => $events){
	$linkIDs = array();
	$itemId = '';
	foreach ($events as $event) {
		$linkIDs[$event->getParam('gcid')] = $event->getParam('gcid');

		$id = GCalendarUtil::getItemId($event->getParam('gcid'), true);
		if(!empty($id))
			$itemId = '&Itemid='.$id;
	}

	$parts = explode('-', $date);
	$day = $parts[2];
	$month = $parts[1];
	$year = $parts[0];
	$url = JRoute::_('index.php?option=com_gcalendar&view=gcalendar&gcids='.implode(',', $linkIDs).$itemId.'#year='.$year.'&month='.$month.'&day='.$day.'&view=agendaDay');

	$data[] = array(
			'id' => $date,
			'title' => utf8_encode(chr(160)), //space only works in IE, empty only in Chrome... sighh
			'start' => $date,
			'url' => $url,
			'allDay' => true,
			'description' => GCalendarUtil::renderEvents($events, sprintf(JText::_('COM_GCALENDAR_JSON_VIEW_EVENT_TITLE'), count($events)).'<ul>{{#events}}<li>{{title}}</li>{{/events}}</ul>', $params)
	);
}
echo json_encode($data);