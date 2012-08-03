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

$data = array();
$SECSINDAY=86400;
if(!empty($this->calendars)){
	$itemID = JRequest::getVar('Itemid', null);
	foreach ($this->calendars as $calendar){
		if($itemID == null){
			$itemID = GCalendarUtil::getItemId($calendar->id);
		}
		$params = JFactory::getApplication()->getMenu()->getParams($itemID);
		$tmp = clone JComponentHelper::getParams('com_gcalendar');
		if(empty($params)){
			$params = $tmp;
		}else{
			$tmp->merge($params);
			$params = $tmp;
		}
		foreach ($calendar as $event) {

			$dateformat = $params->get('description_date_format', 'm.d.Y');
			$timeformat = $params->get('description_time_format', 'g:i a');

			$params->set('event_date_format', $dateformat);
			$params->set('event_time_format', $timeformat);

			// enable all params
			$params->set('show_calendar_name', 1);
			$params->set('show_event_title', 1);
			$params->set('show_event_date', 1);
			$params->set('show_event_attendees', 1);
			$params->set('show_event_location', 1);
			$params->set('show_event_location_map', 1);
			$params->set('show_event_description', 1);
			$params->set('show_event_author', 1);
			$params->set('show_event_copy_info', 1);

			if(!empty($itemID)) {
				$itemID = '&Itemid='.$itemID;
			} else {
				$menu = JFactory::getApplication()->getMenu();
				$activemenu = $menu->getActive();
				if($activemenu != null){
					$itemID = '&Itemid='.$activemenu->id;
				}
			}
			$description = GCalendarUtil::renderEvents(array($event), $params->get('description_format', '{{#events}}<p>{{date}}<br/>{{{description}}}</p>{{/events}}'), $params);
			if(strlen($description) > 200){
				$description = mb_substr($description, 0, 196).' ...';
			}
			$allDayEvent = $event->getDayType() == GCalendar_Entry::SINGLE_WHOLE_DAY || $event->getDayType() == GCalendar_Entry::MULTIPLE_WHOLE_DAY;

			$end = $event->getEndDate();
			if($allDayEvent) {
				$end = clone $event->getEndDate();
				$end->modify('-1 day');
			}
			$data[] = array(
					'id' => $event->getId(),
					'title' => htmlspecialchars_decode($event->getTitle()),
					'start' => $event->getStartDate()->format('Y-m-d\TH:i:s', true),
					'end' => $end->format('Y-m-d\TH:i:s', true),
					'url' => JRoute::_('index.php?option=com_gcalendar&view=event&eventID='.$event->getGCalId().'&gcid='.$event->getParam('gcid').(empty($itemID)?'':$itemID)),
					'className' => "gcal-event_gccal_".$event->getParam('gcid'),
					'allDay' => $allDayEvent,
					'description' => $description
			);
		}
	}
}
echo json_encode($data);