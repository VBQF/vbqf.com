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

class GCalendarControllerImport extends JController
{

	function save()
	{
		$model = $this->getModel('Import');

		if ($model->store($post)) {
			$msg = JText::_( 'Calendar saved!' );
		} else {
			$msg = JText::_( 'Error saving calendar' );
		}

		$link = 'index.php?option=com_gcalendar&view=gcalendars';
		$this->setRedirect($link, $msg);
	}

	function cancel()
	{
		$msg = JText::_( 'Operation cancelled' );
		$this->setRedirect( 'index.php?option=com_gcalendar&view=gcalendars', $msg );
	}
}
?>
