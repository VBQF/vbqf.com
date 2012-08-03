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

jimport( 'joomla.application.component.view' );

/**
 * GCalendars View
 *
 */
class GCalendarViewGCalendars extends JView
{
	/**
	 * GCalendars view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'COM_GCALENDAR_MANAGER_GCALENDAR' ),  'calendar');

		$canDo = GCalendarUtil::getActions();
		if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('gcalendar.add', 'JTOOLBAR_NEW');
			JToolBarHelper::custom('import', 'upload.png', 'upload.png', 'COM_GCALENDAR_VIEW_GCALENDARS_BUTTON_IMPORT', false);
		}
		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::editList('gcalendar.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'gcalendars.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_gcalendar', 550);
			JToolBarHelper::divider();
		}

		$items = & $this->get( 'Items');
		$pagination =& $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->items = $items;
		$this->pagination = $pagination;

		parent::display($tpl);
	}
}
