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

defined('_JEXEC') or die('Restricted access');
?>
<fieldset><legend><?php echo JText::_('COM_GCALENDAR_VIEW_LOGIN_AUTH_DEFAULT_LABEL');?></legend>
<form action="<?php echo JRoute::_( 'index.php?option=com_gcalendar&task='.JRequest::getCmd('task'));?>" method="post">
	<table>
	<tr><td><?php echo JText::_('COM_GCALENDAR_VIEW_LOGIN_AUTH_DEFAULT_FIELD_NAME');?>:</td><td><input type="text" name="user" size="100"/></td></tr>
	<tr><td><?php echo JText::_('COM_GCALENDAR_VIEW_LOGIN_AUTH_DEFAULT_FIELD_PASSWORD');?>:</td><td><input type="password"name="pass" size="100"/></td></tr>
	<tr><td><input type="submit" value="Login"/></td><td></td></tr>
	</table>
</form>
</fieldset>
<div align="center" style="clear: both">
	<?php echo sprintf(JText::_('COM_GCALENDAR_FOOTER'), JRequest::getVar('GCALENDAR_VERSION'));?>
</div>