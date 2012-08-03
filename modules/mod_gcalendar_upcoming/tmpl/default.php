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

defined( '_JEXEC' ) or die( 'Restricted access' );

$tmp = clone JComponentHelper::getParams('com_gcalendar');
$tmp->set('event_date_format', $params->get('date_format', $tmp->get('event_date_format')));
$tmp->set('event_time_format', $params->get('time_format', $tmp->get('event_time_format')));
$tmp->set('grouping', $params->get('output_grouping', ''));

// enable all params
$tmp->set('show_calendar_name', 1);
$tmp->set('show_event_title', 1);
$tmp->set('show_event_date', 1);
$tmp->set('show_event_attendees', 1);
$tmp->set('show_event_location', 1);
$tmp->set('show_event_location_map', 1);
$tmp->set('show_event_description', 1);
$tmp->set('show_event_author', 1);
$tmp->set('show_event_copy_info', 1);

$output = $params->get('output', '{{#events}}
{{#header}}<p style="clear: both;"><strong>{{header}}</strong></p>{{/header}}
<p style="clear: both;"/>
<div style="float:left;margin-right:6px;width:42px;height:42px;background-image:url(\'modules/mod_gcalendar_upcoming/tmpl/images/calendar-icon.gif\')">
	<div style="background-color: #{{calendarcolor}};width:32px;height:10px;margin-top:6px;margin-left:5px;"></div>
	<div style="color: #FFFFFF;padding:2px;font-weight:bold;font-size:10px;text-align:center;position:relative;margin-top:-13px;margin-bottom:-4px;">{{month}}</div>
	<div style="color: #{{calendarcolor}};font-weight:bold;font-size:1.3em;width:42px;text-align:center;">{{day}}</div>
</div>
<p>{{date}}<br/><a href="{{{backlink}}}">{{title}}</a></p>
<p style="clear: both;"/>
{{/events}}
{{^events}}
{{emptyText}}
{{/events}}');
echo GCalendarUtil::renderEvents($events, $output, $tmp);