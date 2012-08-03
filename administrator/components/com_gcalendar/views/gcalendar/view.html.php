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
 * GCalendar View
 *
 */
class GCalendarViewGCalendar extends JView
{
	/**
	 * display method of GCalendar view
	 * @return void
	 **/
	function display($tpl = null)
	{
		
		// get the Data
		$form = $this->get('Form');
		$gcalendar	= $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Assign the Data
		$this->form = $form;
		$this->gcalendar = $gcalendar;

		//get the calendar
		$isNew = $gcalendar->id < 1;

		JToolBarHelper::title(JText::_( 'COM_GCALENDAR_MANAGER_GCALENDAR' ), 'calendar');

		JRequest::setVar('hidemainmenu', true);
		$canDo = GCalendarUtil::getActions($gcalendar->id);
		if ($isNew){
			if ($canDo->get('core.create')){
				JToolBarHelper::apply('gcalendar.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('gcalendar.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('gcalendar.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('gcalendar.cancel', 'JTOOLBAR_CANCEL');
		}else{
			if ($canDo->get('core.edit')){
				// We can save the new record
				JToolBarHelper::apply('gcalendar.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('gcalendar.save', 'JTOOLBAR_SAVE');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create')){
					JToolBarHelper::custom('gcalendar.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($canDo->get('core.create')){
				JToolBarHelper::custom('gcalendar.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('gcalendar.cancel', 'JTOOLBAR_CLOSE');
		}
		
		parent::display($tpl);
	}
}
