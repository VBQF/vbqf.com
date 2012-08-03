<div style="width:500px;">
<h2><?php echo JText::_('COM_GCALENDAR_VIEW_CPANEL_WELCOME'); ?></h2>
<p>
<?php echo JText::_('COM_GCALENDAR_VIEW_CPANEL_INTRO'); ?>
</p>
<br>

<div id="cpanel" style="float:left">
    <div style="float:left;">
            <div class="icon">
                <a href="index.php?option=com_gcalendar&view=gcalendars" >
                <img src="<?php echo JURI::base(true);?>/../media/com_gcalendar/images/48-calendar.png" height="50px" width="50px">
                <span><?php echo JText::_('COM_GCALENDAR_VIEW_CPANEL_GCALENDARS'); ?></span>
                </a>
            </div>
            <div class="icon">
                <a href="index.php?option=com_gcalendar&task=import" >
                <img src="<?php echo JURI::base(true);?>/../media/com_gcalendar/images/admin/import.png" height="50px" width="50px">
                <span><?php echo JText::_('COM_GCALENDAR_VIEW_CPANEL_IMPORT'); ?></span>
                </a>
            </div>
            <div class="icon">
                <a href="index.php?option=com_gcalendar&view=gcalendar&layout=edit" >
                <img src="<?php echo JURI::base(true);?>/../media/com_gcalendar/images/admin/add.png" height="50px" width="50px">
                <span><?php echo JText::_('COM_GCALENDAR_VIEW_CPANEL_ADD'); ?></span>
                </a>
            </div>
            <div class="icon">
                <a href="index.php?option=com_gcalendar&view=tools" >
                <img src="<?php echo JURI::base(true);?>/../media/com_gcalendar/images/admin/tools.png" height="50px" width="50px">
                <span><?php echo JText::_('COM_GCALENDAR_SUBMENU_TOOLS'); ?></span>
                </a>
            </div>
            <div class="icon">
                <a href="index.php?option=com_gcalendar&view=support" >
                <img src="<?php echo JURI::base(true);?>/../media/com_gcalendar/images/admin/support.png" height="50px" width="50px">
                <span><?php echo JText::_('COM_GCALENDAR_SUBMENU_SUPPORT'); ?></span>
                </a>
            </div>
    </div>
</div>
</div>
<div id="twitter_div" style="float:left"></div>
<script src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
window.addEvent('domready', function() {
	new TWTR.Widget({
		  id: 'twitter_div',
		  version: 2,
		  type: 'profile',
		  rpp: 4,
		  interval: 30000,
		  width: 300,
		  height: 300,
		  theme: {
		    shell: {
		      background: '#CCCCCC',
		      color: '#000000'
		    },
		    tweets: {
		      background: '#FFFFFF',
		      color: '#000000',
		      links: '#0726eb'
		    }
		  },
		  features: {
		    scrollbar: true,
		    loop: true,
		    live: true,
		    behavior: 'all'
		  }
	}).render().setUser('g4joomla').start();
});
</script>

<div align="center" style="clear: both">
	<br>
	<?php echo sprintf(JText::_('COM_GCALENDAR_FOOTER'), JRequest::getVar('GCALENDAR_VERSION'));?>
</div>