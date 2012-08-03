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

class ModGCalendarUpcomingHelper{

	public static function getCalendarItems($params) {
		$calendarids = $params->get('calendarids');
		$results = GCalendarDBUtil::getCalendars($calendarids);
		if(empty($results)){
			JError::raiseWarning( 500, 'The selected calendar(s) were not found in the database.');
			return array();
		}

		$orderBy = $params->get( 'order', 1 ) == 1 ? GCalendarZendHelper::ORDER_BY_START_TIME : GCalendarZendHelper::ORDER_BY_LAST_MODIFIED;
		$maxEvents = $params->get('max_events', 10);
		$filter = $params->get('find', '');
		$startDate = $params->get('start_date', null);
		$endDate = $params->get('end_date', null);
		if(!empty($startDate)){
			$startDate = strtotime($startDate);
		}
		if( !empty($endDate)){
			$endDate = strtotime($endDate);
		}

		$values = array();
		foreach ($results as $result) {
			$events = GCalendarZendHelper::getEvents($result, $startDate, $endDate, $maxEvents, $filter, $orderBy);
			if(!empty($events)){
				foreach ($events as $event) {
					if(!($event instanceof GCalendar_Entry)){
						continue;
					}
					$values[] = $event;
				}
			}
		}

		usort($values, array("GCalendar_Entry", "compare"));

		return array_slice($values, 0, $maxEvents);
	}
}