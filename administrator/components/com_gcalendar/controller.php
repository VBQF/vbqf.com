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

jimport('joomla.application.component.controller');

class GCalendarController extends JController{

	public function display($cachable = false, $urlparams = false){
		JRequest::setVar('view', JRequest::getCmd('view', 'cpanel'));
		parent::display($cachable, $urlparams);
		$view = JRequest::getVar('view', 'cpanel');

		JSubMenuHelper::addEntry(JText::_('COM_GCALENDAR_SUBMENU_CPANEL'), 'index.php?option=com_gcalendar&view=cpanel', $view == 'cpanel');
		JSubMenuHelper::addEntry(JText::_('COM_GCALENDAR_SUBMENU_GCALENDARS'), 'index.php?option=com_gcalendar&view=gcalendars', $view == 'gcalendars');
		JSubMenuHelper::addEntry(JText::_('COM_GCALENDAR_SUBMENU_TOOLS'), 'index.php?option=com_gcalendar&view=tools', $view == 'tools');
		JSubMenuHelper::addEntry(JText::_('COM_GCALENDAR_SUBMENU_SUPPORT'), 'index.php?option=com_gcalendar&view=support', $view == 'support');
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-calendar {background-image: url(../media/com_gcalendar/images/48-calendar.png);background-repeat: no-repeat;}');

		$params = JComponentHelper::getParams('com_gcalendar');
		if($params->get('timezone', '') == ''){
			JError::raiseNotice(0, JText::_('COM_GCALENDAR_FIELD_CONFIG_SETTINGS_TIMEZONE_WARNING'));
		}
	}

	public function import(){
		if(JRequest::getVar('user', null) != null){
			$data = $this->getModel('Import', 'GCalendarModel')->getOnlineData();
			if($data == null){
				JRequest::setVar( 'nextTask', 'import'  );
				JRequest::setVar( 'view', 'login'  );
			} else {
				JRequest::setVar( 'view', 'import'  );
			}
		}else{
			JRequest::setVar( 'nextTask', 'import'  );
			JRequest::setVar( 'view', 'login'  );
		}
		JRequest::setVar('hidemainmenu', 0);

		$this->display();
	}
}