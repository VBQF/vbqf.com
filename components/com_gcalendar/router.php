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

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'util.php');

function GCalendarBuildRoute( &$query )
{
	$segments = array();
	$view = null;
	if(isset($query['view']))
	{
		$segments[] = $query['view'];
		$view = $query['view'];
		unset( $query['view'] );
	}
	if($view === 'event'){
		if(isset($query['gcid']))
		{
			$segments[] = $query['gcid'];
			unset( $query['gcid'] );
		}
		if(isset($query['eventID']))
		{
			$segments[] = $query['eventID'];
			unset( $query['eventID'] );
		}
	}
	return $segments;
}

/**
 * @param	array
 * @return	array
 */
function GCalendarParseRoute( $segments )
{
	// Get the active menu item
	$menu =& JSite::getMenu();
	$item =& $menu->getActive();

	$vars = array();
	$view = $segments[0];
	//if the view is calendars it is a menu link
	if($view == 'calendars')
	$view = $item->query['view'];
	$vars['view'] = $view;

	switch($view)
	{
		case 'event':
			$vars['gcid'] = $segments[1];
			$vars['eventID'] = $segments[2];
			$vars['Itemid'] = GCalendarUtil::getItemId($segments[2]);
			break;
		case 'google':
		case 'gcalendar':
			// do nothing
			break;
	}
	return $vars;
}