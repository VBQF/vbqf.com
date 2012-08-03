<?php
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item){?>
	<tr class="row<?php echo $i % 2; ?>">
		<td><?php echo $item->id; ?></td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_( 'index.php?option=com_gcalendar&task=gcalendar.edit&id='. $item->id ); ?>">
				<?php echo $item->name; ?>
			</a>
		</td>
		<td width="40px"><div style="background-color: <?php echo GCalendarUtil::getFadedColor($item->color);?>;width: 40px;height: 40px;"></div></td>
		<td style="border: 0;"><?php echo urldecode($item->calendar_id); ?></td>
		<td style="border: 0;"><?php 
		if(!empty($item->magic_cookie)){
			echo JText::_('COM_GCALENDAR_FIELD_MAGIC_COOKIE_LABEL');
		} else if(!empty($item->username)){
			echo JText::_('COM_GCALENDAR_VIEW_GCALENDARS_COLUMN_AUTHENTICATION_USERNAME');
		} else {
			echo JText::_('COM_GCALENDAR_VIEW_GCALENDARS_COLUMN_AUTHENTICATION_NO');
		}
		?></td>
	</tr>
<?php } ?>