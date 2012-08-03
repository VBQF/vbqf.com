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

// no direct access
defined('_JEXEC') or die('Restricted access');

// ini_set('display_errors',1);
// error_reporting(E_ALL);

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_gcalendar')){
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'util.php');
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'libraries'.DS.'GCalendar'.DS.'GCalendarZendHelper.php');

require_once (JPATH_COMPONENT.DS.'controller.php');
jimport('joomla.application.component.controller');

$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'gcalendar.xml';
if(file_exists($path)){
	$manifest = simplexml_load_file($path);
	JRequest::setVar('GCALENDAR_VERSION', $manifest->version);
}else{
	JRequest::setVar('GCALENDAR_VERSION', '');
}
$controller = JController::getInstance('GCalendar');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();