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
 * @since 2.6.3
 */
defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

class plgSystemGCalendar extends JPlugin {

	public function onAfterRoute() {
		if(JFactory::getApplication()->isAdmin() && JRequest::getVar('option', null) != 'com_gcalendar'){
			return;
		}
		
		if($this->params->get('load-jquery', 1) == 0 || JFactory::getDocument()->getType() != 'html'){
			return;
		}
		
		if($this->params->get('load-jquery', 1) == 2 && JRequest::getVar('option', null) != 'com_gcalendar'){
			return;
		}
		
		JHTML::_(' behavior.mootools');

		JFactory::getDocument()->addScript("/GCJQLIB");
		JFactory::getDocument()->addScriptDeclaration("jQuery.noConflict();");

		JFactory::getApplication()->set('jQuery', true);
		JFactory::getApplication()->set('jquery', true);
	}

	public function onAfterRender() {
		if(JFactory::getApplication()->isAdmin() && JRequest::getVar('option', null) != 'com_gcalendar'){
			return;
		}
		
		if($this->params->get('load-jquery', 1) == 0 || JFactory::getDocument()->getType() != 'html'){
			return;
		}
		
		if($this->params->get('load-jquery', 1) == 2 && JRequest::getVar('option', null) != 'com_gcalendar'){
			return;
		}

		$body =& JResponse::getBody();

		$body = preg_replace("#([\\\/a-zA-Z0-9_:\.-]*)jquery([0-9\.-]|min|pack)*?.js#", "", $body);
		$body = str_ireplace('<script src="" type="text/javascript"></script>', "", $body);
		$body = preg_replace("#/GCJQLIB#", JURI::root().'/components/com_gcalendar/libraries/jquery/jquery.min.js', $body);

		JResponse::setBody($body);
	}
}