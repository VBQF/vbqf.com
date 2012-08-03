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
 * @copyright 2007-2012 Allon Moritz
 * @since 2.6.0
 */

defined('_JEXEC') or die('Restricted access');

class Com_GCalendarInstallerScript{

	public function install($parent){
		$this->run("update #__extensions set enabled=1 where type = 'plugin' and element = 'gcalendar'");
	}

	function update($parent){
		$version = $this->getParam('version');
		if(version_compare($version, '2.6.0')){
			$this->run("ALTER TABLE `#__gcalendar` ADD `access` TINYINT UNSIGNED NOT NULL DEFAULT '1';");
			$this->run("ALTER TABLE `#__gcalendar` ADD `access_content` TINYINT UNSIGNED NOT NULL DEFAULT '1';");
			$this->run("ALTER TABLE `#__gcalendar` ADD `username` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `magic_cookie`;");
			$this->run("ALTER TABLE `#__gcalendar` ADD `password` text NULL DEFAULT NULL AFTER `username`;");
		}
		if(version_compare($version, '2.6.2')){
			$this->run("update #__extensions set enabled=1 where type = 'plugin' and element = 'gcalendar'");
		}
		if(version_compare($version, '2.7.0')){
			foreach (JFolder::files(JPATH_ADMINISTRATOR.DS.'language', '.*gcalendar.*', true, true) as $file){
				JFile::delete($file);
			}
			foreach (JFolder::files(JPATH_SITE.DS.'language', '.*gcalendar.*', true, true) as $file){
				JFile::delete($file);
			}
		}
	}

	public function uninstall($parent){
	}

	public function preflight($type, $parent){
	}

	public function postflight($type, $parent){
	}

	private function run($query){
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	private function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_gcalendar"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
}