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

defined('_JEXEC') or die('Restricted access'); ?>

<h4>GCalendar Help</h4>
<p>GCalendar is an extension for joomla based web sites which 
supports a smooth integration of your google calendar.</p>

<h4>Donation</h4>
<p>
There is more effort behind GCalendar than you think... 
<script type="text/javascript" src="http://www.ohloh.net/projects/26740/widgets/project_thin_badge"></script>

<br><br>
Because the sources are free the project depends on donations to support furthure 
releases!! Please make a small donation with paypal.....<br><br>
</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input name="cmd" value="_s-xclick" type="hidden">
<input name="hosted_button_id" value="302238" type="hidden">
<input src="https://www.paypal.com/en_US/CH/i/btn/btn_donateCC_LG.gif" name="submit" alt="" type="image" border="0">
<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" height="1" width="1" border="0">
</form>

<h4>Documentation and Support</h4>
<p>
At <a href="http://g4j.laoneo.net" target="_blank">g4j.laoneo.net</a> you will find all the informations about
the project as well as a forum to post questions.
</p>
<div align="center" style="clear: both">
	<?php echo sprintf(JText::_('COM_GCALENDAR_FOOTER'), JRequest::getVar('GCALENDAR_VERSION'));?>
</div>