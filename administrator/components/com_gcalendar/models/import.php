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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * GCalendar Model
 *
 */
class GCalendarModelImport extends JModel
{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Method to set the calendar identifier
	 *
	 * @access	public
	 * @param	int Calendar identifier
	 * @return	void
	 */
	public function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	public function getOnlineData() {
		try{
			$user = JRequest::getVar('user', null);
			$pass = JRequest::getVar('pass', null);

			$calendars = GCalendarZendHelper::getCalendars($user, $pass);
			if($calendars == null){
				return null;
			}

			$this->_data = array();
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'tables');
			foreach ($calendars as $calendar) {
				$table = & $this->getTable('Import', 'GCalendarTable');
				$table->id = 0;
				$cal_id = substr($calendar->getId(),strripos($calendar->getId(),'/')+1);
				$table->calendar_id = $cal_id;
				$table->username = $user;
				$table->password = $pass;
				$table->name = (string)$calendar->getTitle();
				if(strpos($calendar->getColor(), '#') === 0){
					$color = str_replace("#","", (string)$calendar->getColor());
					$table->color = $color;
				}

				$this->_data[] = $table;
			}
		} catch(Exception $e){
			JError::raiseWarning(200, $e->getMessage());
			$this->_data = null;
		}

		return $this->_data;
	}

	/**
	 * Method to get a calendar
	 * @return object with data
	 */
	public function getDBData()
	{
		$query = " SELECT * FROM #__gcalendar";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function store()	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'tables');
		$row = & $this->getTable('Import', 'GCalendarTable');

		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (count($cids)>0) {
			foreach ($cids as $cid) {
				$row->id = 0;
				$row->calendar_id = strtok($cid, ',');
				$row->color = strtok(',');
				$row->name = strtok(',');
				$row->magic_cookie = strtok(',');
				if($row->magic_cookie === false){
					$row->magic_cookie = null;
				}

				// Make sure the calendar record is valid
				if (!$row->check()) {
					JError::raiseWarning( 500, $row->getError() );
					return false;
				}

				// Store the calendar table to the database
				if (!$row->store()) {
					JError::raiseWarning( 500, $row->getError() );
					return false;
				}
			}
		}
		return true;
	}
}