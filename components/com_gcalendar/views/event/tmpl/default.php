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

$dispatcher = JDispatcher::getInstance();
JPluginHelper::importPlugin('gcalendar');

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base(). 'components/com_gcalendar/views/gcalendar/tmpl/gcalendar.css' );
$document->addStyleSheet(JURI::base().'components/com_gcalendar/views/event/tmpl/default.css');
$document->addScript(JURI::base().'components/com_gcalendar/views/event/tmpl/default.js');

$content = '{{#events}}
{{#calendarLink}}
<table class="gcalendar-table">
	<tr>
		<td valign="middle">
			<a href="{{calendarLink}}">
				<img id="prevBtn_img" height="16" border="0" width="16" alt="backlink" src="media/com_gcalendar/images/back.png"/>
			</a>
		</td>
		<td valign="middle">
			<a href="{{calendarLink}}">{{{calendarLinkLabel}}}</a>
		</td>
	</tr>
</table>
{{/calendarLink}}
<div class="event_content">
<table id="content_table">
	<tr><td colspan="2">{{#pluginsBefore}} {{{.}}} {{/pluginsBefore}}</td></tr>
	{{#calendarName}}
	<tr><td class="event_content_key">{{calendarNameLabel}}: </td><td>{{calendarName}}</td></tr>
	{{/calendarName}}
	{{#title}}
	<tr><td class="event_content_key">{{titleLabel}}: </td><td>{{title}}</td></tr>
	{{/title}}
	{{#date}}
	<tr><td class="event_content_key">{{dateLabel}}: </td><td>{{date}}</td></tr>
	{{/date}}
	{{#hasAttendees}}
	<tr>
		<td class="event_content_key">{{attendeesLabel}}: </td>
		<td>
			{{#attendees}}{{name}} <a href="javascript:sdafgkl437jeeee("{{email}}")"><img height="11" border="0" width="16" alt="email" src="media/com_gcalendar/images/mail.png"/></a>, {{/attendees}}
		</td>
	</tr>
	{{/hasAttendees}}
	{{#location}}
	<tr><td class="event_content_key">{{locationLabel}}: </td><td>{{location}}</td></tr>
	{{/location}}
	{{#maplink}}
	<tr><td colspan="2"><iframe width="100%" height="300px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="{{{maplink}}}"></iframe></td></tr>
	{{/maplink}}
	{{#description}}
	<tr><td class="event_content_key">{{descriptionLabel}}: </td><td>{{{description}}}</td></tr>
	{{/description}}
	{{#hasAuthor}}
	<tr>
		<td class="event_content_key">{{authorLabel}}: </td>
		<td>
			{{#author}}{{name}} <a href="javascript:sdafgkl437jeeee("{{email}}")"><img height="11" border="0" width="16" alt="email" src="media/com_gcalendar/images/mail.png"/></a>, {{/author}}
		</td>
	</tr>
	{{/hasAuthor}}
	{{#copyGoogleUrl}}
	<tr>
		<td class="event_content_key">{{copyLabel}}: </td>
		<td>
			<a target="_blank" href="{{copyGoogleUrl}}">{{copyGoogleLabel}}</a>
		</td>
	</tr>
	{{/copyGoogleUrl}}
	{{#copyOutlookUrl}}
	<tr>
		<td class="event_content_key"></td>
		<td>
			<a target="_blank" href="{{copyOutlookUrl}}">{{copyOutlookLabel}}</a>
		</td>
	</tr>
	{{/copyOutlookUrl}}
	<tr><td colspan="2">{{#pluginsAfter}} {{{.}}} {{/pluginsAfter}}</td></tr>
</table>
</div>
{{/events}}
{{^events}}
{{emptyText}}
{{/events}}';

$plugins = array();
$plugins['pluginsBefore'] = array();
$plugins['pluginsAfter'] = array();
$dispatcher->trigger('onBeforeDisplayEvent', array($this->event,  &$content, &$plugins['pluginsBefore']));
$dispatcher->trigger('onAfterDisplayEvent', array($this->event,  &$content, &$plugins['pluginsAfter']));

echo GCalendarUtil::renderEvents(array($this->event), $content, JFactory::getApplication()->getParams(), $plugins);

if(!JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendarap'.DS.'gcalendarap.php'))
	echo "<div style=\"text-align:center;margin-top:10px\" ><a href=\"http://g4j.laoneo.net\">Powered by GCalendar</a></div>\n";
