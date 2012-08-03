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

jimport( 'joomla.application.component.view' );

class GCalendarViewImport extends JView
{
	
	function display($tpl = null)
	{
		JToolBarHelper::title(JText::_( 'COM_GCALENDAR_MANAGER_GCALENDAR' ), 'calendar');
		
		$canDo = GCalendarUtil::getActions();
		if ($canDo->get('core.create')){
			JToolBarHelper::custom('import.save', 'new.png', 'new.png', 'COM_GCALENDAR_VIEW_IMPORT_BUTTON_ADD', false);
		}
		JToolBarHelper::cancel('gcalendar.cancel', 'JTOOLBAR_CANCEL');
		
		$items = $this->get( 'OnlineData');
		$this->assignRef('online_items', $items);
		$dbitems = $this->get( 'DBData');
		$this->assignRef('db_items', $dbitems);
		parent::display($tpl);
	}
}
