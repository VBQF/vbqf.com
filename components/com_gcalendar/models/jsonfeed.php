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

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class GCalendarModelJSONFeed extends JModel {

	public function getGoogleCalendarFeeds() {
		$startDate = JRequest::getVar('start', null);
		$endDate = JRequest::getVar('end', null);

		$browserTz = JRequest::getInt('browserTimezone', null);
		if(!empty($browserTz))
			$browserTz = $browserTz * 60;
		else
			$browserTz = 0;

		$serverOffset = date('Z', $startDate);
		$startDate = $startDate - $browserTz;
		$endDate = $endDate - $browserTz;

		$calendarids = '';
		if(JRequest::getVar('gcids', null) != null){
			if(is_array(JRequest::getVar('gcids', null)))
				$calendarids = JRequest::getVar('gcids', null);
			else
				$calendarids = explode(',', JRequest::getVar('gcids', null));
		}else{
			$calendarids = JRequest::getVar('gcid', null);
		}
		$results = GCalendarDBUtil::getCalendars($calendarids);
		if(empty($results))
			return null;

		$calendars = array();
		foreach ($results as $result) {
			if(empty($result->calendar_id))
				continue;

			$events = GCalendarZendHelper::getEvents($result, $startDate, $endDate, 1000);
			if($events == null){
				continue;
			}
			$calendars[] = $events;
		}

		return $calendars;
	}
}