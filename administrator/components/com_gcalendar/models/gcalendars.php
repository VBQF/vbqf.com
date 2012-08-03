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

jimport('joomla.application.component.modellist');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'tables');

class GCalendarModelGCalendars extends JModelList
{

	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$items = parent::_getList($query, $limitstart, $limit);
		if($items === null){
			return $items;
		}
		$tmp = array();
		foreach ($items as $item) {
			$table = JTable::getInstance('GCalendar', 'GCalendarTable');
			$table->load($item->id);
			$tmp[] = $table;
		}
		return $tmp;
	}

	protected function getListQuery()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user	= JFactory::getUser();

		$query->select('*');
		$calendarIDs = $this->getState('ids', null);
		if(!empty($calendarIDs)){
			if(is_array($calendarIDs)) {
				$query->where('id IN ( '.implode(',', array_map('intval', $calendarIDs)).')');
			} else {
				$query->where($condition = 'id = '.(int)rtrim($calendarIDs, ','));
			}
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('access IN ('.$groups.')');
		}

		$query->from('#__gcalendar');
		return $query;
	}
}