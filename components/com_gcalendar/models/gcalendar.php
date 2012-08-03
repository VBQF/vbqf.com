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

class GCalendarModelGCalendar extends JModel {

	private $cached_data = null;
	protected $_extension = 'com_gcalendar';

	protected function populateState(){
		$this->setState('filter.extension', $this->_extension);

		$calendarids = JFactory::getApplication()->getParams()->get('calendarids');
		if(!is_array($calendarids) && !empty($calendarids)){
			$calendarids = array($calendarids);
		}
		$tmp = JRequest::getVar('gcids', null);
		if(!empty($tmp)){
			$calendarids = explode(',', $tmp);
		}
		$this->setState('calendarids', $calendarids);

		$this->setState('params', JFactory::getApplication()->getParams());
	}

	public function getDBCalendars(){
		if($this->cached_data == null){
			$calendarids = $this->getState('calendarids');
			if(!empty($calendarids)){
				$this->cached_data = GCalendarDBUtil::getCalendars($calendarids);
			} else {
				$this->cached_data = GCalendarDBUtil::getAllCalendars();
			}
		}
		return $this->cached_data;
	}
}