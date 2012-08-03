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

defined('_JEXEC') or die( 'Restricted access' );

class JFormFieldColor extends JFormFieldText
{
	protected $type = 'Color';

	public function getInput()
	{
		$document = &JFactory::getDocument();
		$document->addScript(JURI::base(). 'components/com_gcalendar/libraries/jscolor/jscolor.js' );
		return parent::getInput();
	}

	public function setup(& $element, $value, $group = null)
	{
		$return= parent::setup($element, $value, $group);
		$this->element['class'] = $this->element['class'].' color';
		return $return;
	}
}
?>