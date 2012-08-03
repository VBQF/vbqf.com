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
 * This code was based on Allon Moritz great work in the companion
 * upcoming module.
 *
 * @author Eric Horne
 * @copyright 2009-2011 Eric Horne
 * @since 2.2.0
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.html.parameter' );

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'util.php');
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'libraries'.DS.'GCalendar'.DS.'GCalendarZendHelper.php');

class plgContentgcalendar_next extends JPlugin {

	public function onContentPrepare($context, &$article, &$params, $page = 0 ) {
		if (JRequest::getCmd('option') != 'com_content') return;
		if (!$article->text) return;

		$text = preg_replace_callback('/{gcalnext\s+(.*?)\s*?}(.*?){\/gcalnext}/', array($this, 'embedEvent'), $article->text);

		if ($text) {
			$article->text = $text;
		}
	}

	public function embedEvent($gcalnext) {
		$param_str = $gcalnext[1];
		$fmt_str = $gcalnext[2];

		$helper = new GCalendarKeywordsHelper($this->params, $param_str, $fmt_str);
		if (!$helper->event()) {
			return $this->params->get('no_event');
		}

		$start = $helper->event()->getStartDate();
		$end = $helper->event()->getEndDate();
		$now = time();
		$start_soon = date($this->params->get('start_soon', '-4 hours'), $start->format('U', true));
		$end_soon = date($this->params->get('end_soon', '-2 hours'), $end->format('U', true));
		$text = '';

		if ($fmt_str) {
			$this->params->set('output', $fmt_str);
		}

		if ($end->format('U') <= $now) {
			// AND it hasn't ended
			if ($start >= $now) {
				// If it has started
				$text = $this->params->get('output_now');
			}
			elseif ($start_soon >= $now) {
				$text = $this->params->get('output_start_soon', JText::_('PLG_GCALENDAR_NEXT_OUTPUT_STARTING_SOON'));
			}
			elseif ($end_soon >= $now ) {
				$text = $this->params->get('output_end_soon', JText::_('PLG_GCALENDAR_NEXT_OUTPUT_ENDING_SOON'));
			}
		}

		if ($text == "" or $text == null) {
			$text = $this->params->get('output');
		}

		return $helper->replace($text);
	}
}

class PluginKeywordsHelper {

	protected $params;
	private $argre;
	private $txtParam;
	private $txtFmt;
	private $dataobj;
	private $plgParams = Array();

	public function PluginKeywordsHelper($params, $txtParam, $txtFmt, $argre = '/(?:\[\$)\s*(.*?)\s*(?:\$\])/') {
		$this->params = new JParameter($params->toString("INI")); // Prevents bleedover to other instances
		$this->txtParam = $txtParam;
		$this->txtFmt = $txtFmt;
		$this->argre = $argre;

		$matches = Array();
		preg_match_all($this->argre, $this->txtParam, $matches);
		foreach ($matches[1] as $match) {
			list($key, $value) = explode(' ', $match, 2) + Array("", "");
			$value = str_replace("\\",'',$value);
			$this->params->set($key, $value);
			$this->plgParams[$key] = $value;
		}

		$this->dataobj = $this->setDataObj();
	}

	public function setDataObj() {
		return "";
	}

	public function dataobj() {
		return $this->dataobj;
	}

	public function plgText() {
		return $plgText;
	}

	public function replace($txt) {
		return preg_replace_callback($this->argre, array($this, 'replaceSingle'), $txt);
	}

	public function replaceSingle($val) {
		list($func, $arg) = explode(' ', $val[1], 2) + Array("", "");

		if (is_callable(array($this, $func))) {
			return call_user_func(array($this, $func), $arg);
		}

		return $val;
	}
}

class GCalendarKeywordsHelper extends PluginKeywordsHelper {

	public function setDataObj() {
		$calendarids = $this->params->get('calendarids');
		$results = GCalendarDBUtil::getCalendars($calendarids);
		if(empty($results)){
			JError::raiseWarning( 500, 'The selected calendar(s) were not found in the database.');
			return null;
		}

		$orderBy = $this->params->get( 'order', 1 ) == 1 ? GCalendarZendHelper::ORDER_BY_START_TIME : GCalendarZendHelper::ORDER_BY_LAST_MODIFIED;
		$maxEvents = $this->params->get('max_events', 10);
		$filter = $this->params->get('find', '');
		$titleFilter = $this->params->get('title_filter', '.*');

		$values = array();
		foreach ($results as $result) {
			$events = GCalendarZendHelper::getEvents($result, null, null, $maxEvents, $filter, $orderBy);
			if(!empty($events)){
				foreach ($events as $event) {
					if(!($event instanceof GCalendar_Entry)){
						continue;
					}
					$event->setParam('moduleFilter', $titleFilter);
					$values[] = $event;
				}
			}
		}

		usort($values, array("GCalendar_Entry", "compare"));

		$events = array_filter($values, array('GCalendarKeywordsHelper', "filter"));

		$offset = $this->params->get('offset', 0);
		$numevents = $this->params->get('count', $maxEvents);

		return array_shift($values);
	}

	private static function filter($event) {
		if (!preg_match('/'.$event->getParam('moduleFilter').'/', $event->getTitle())) {
			return false;
		}
		if ($event->getEndDate()->format('U') > time()) {
			return true;
		}

		return false;
	}

	public function event() {
		return $this->dataobj();
	}


	public function date($format, $time) {
		if ($format == "") {
			$format = $this->params->get("dateformat", 'F d, Y @ g:ia');
		}
		return JFactory::getDate($time)->format($format, true);
	}

	public function datecalc($param, $time) {
		list($formula, $fmt) = explode(',', $param, 2) + Array("", "");
		return $this->date($fmt, strtotime($formula, $time));
	}


	public function startoffset($param) {
		return $this->datecalc($param, $this->event()->getStartDate()->format('U', true));
	}

	public function finishoffset($param) {
		return $this->datecalc($param, $this->event()->getEndDate()->format('U', true));
	}

	public function startdate($param) {
		return $this->start($param);
	}

	public function start($param) {
		return $this->date($param, $this->event()->getStartDate()->format('U', true));
	}

	public function finishdate($param) {
		return $this->finish($param);
	}

	public function finish($param) {
		$ftime = $this->event()->getEndDate();
		$daytype = $this->event()->getDayType();
		if ($daytype == GCalendar_Entry::MULTIPLE_WHOLE_DAY) {
			$ftime = $ftime->format('U') - 1; // to account for midnight
		}

		return $this->date($param, $ftime);
	}

	public function range($param) {
		if ($param) {
			$fmt = $param;
		}
		else {
			switch($this->event()->getDayType()) {
				case GCalendar_Entry::SINGLE_WHOLE_DAY:
					$fmt = $this->params->get("only-whole_day", JText::_('PLG_GCALENDAR_NEXT_OUTPUT_SINGLE_WHOLE_DAY'));
					break;
				case GCalendar_Entry::SINGLE_PART_DAY:
					$fmt = $this->params->get("only-part_day", JText::_('PLG_GCALENDAR_NEXT_OUTPUT_SINGLE_PART_DAY'));
					break;
				case GCalendar_Entry::MULTIPLE_WHOLE_DAY:
					$fmt = $this->params->get("multi-whole_day", JText::_('PLG_GCALENDAR_NEXT_OUTPUT_MULTI_WHOLE_DAY'));
					break;
				case GCalendar_Entry::MULTIPLE_PART_DAY:
					$fmt = $this->params->get("multi-part_day", JText::_('PLG_GCALENDAR_NEXT_OUTPUT_MULTI_PART_DAY'));
					break;
			}
		}

		return $this->replace($fmt);
	}

	public function duration($param, $interval) {
		$days = 0;
		$hours = 0;
		$minutes = 0;
		$seconds = 0;

		if (strpos($param, 'd') !== FALSE) {
			$days = intval($interval / (24 * 3600));
			$interval = $interval - ($days * 24 * 3600);
			$param = str_replace('d', $days, $param);
		}

		if (strpos($param, '%h') !== FALSE) {
			$hours = intval($interval / (3600));
			$interval = $interval - ($hours * 3600);
			$param = str_replace('%h', $hours, $param);
		}

		if (strpos($param, '%m') !== FALSE) {
			$minutes = intval($interval / (60));
			$interval = $interval - ($minutes * 60);
			$param = str_replace('%m', $minutes, $param);
		}

		if (strpos($param, '%s') !== FALSE) {
			$seconds = intval($interval);
			$param = str_replace('%s', $seconds, $param);
		}

		return $param;
	}

	public function lasts($param) {
		return $this->duration($param, $this->event()->getEndDate()->format('U', true) - $this->event()->getStartDate()->format('U', true));
	}

	public function startsin($param) {
		return $this->duration($param, $this->event()->getStartDate()->format('U', true) - time());
	}

	public function endsin($param) {
		return $this->duration($param, $this->event()->getEndDate()->format('U', true) - time());
	}

	public function title($param) {
		return $this->event()->getTitle();
	}

	public function description($param) {
		$desc = preg_replace("@(src|href)=\"https?://@i",'\\1="',$this->event()->getContent());
		return preg_replace("@(((f|ht)tps?://)[^\"\'\>\s]+)@",'<a href="\\1" target="_blank">\\1</a>', $desc);
	}

	public function backlink($param) {
		$gcid = $this->event()->getParam('gcid');
		$itemID = GCalendarUtil::getItemID($gcid);
		if (!empty($itemID)) $itemID = '&Itemid='.$itemID;
		return JRoute::_('index.php?option=com_gcalendar&view=event&eventID='.$this->event()->getGCalId().'&gcid='.$gcid.$itemID);
	}

	public function link($param) {
		$timezone = GCalendarUtil::getComponentParameter('timezone', 'UTC');
		return $this->event()->getLink() . '&ctz=' . $timezone;
	}

	public function maplink($param) {
		return '<a class="gcalendar_location_link" href="' . $this->maphref($param) . '">' . $this->location($param) . '</a>';
	}

	public function maphref($param) {
		return 'http://maps.google.com/?q=' . urlencode($this->location($param));
	}

	public function location($param) {
		return $this->where($param);
	}

	public function where($param) {
		return $this->event()->getLocation();
	}

	public function calendarname($param) {
		return $this->event()->getParam('gcname');
	}

	public function calendarcolor($param) {
		return $this->event()->getParam('gccolor');
	}
}
?>
