jQuery(document).ready(function(){
	var gcSlide = new Fx.Slide('gc_gcalendar_view_list');

	jQuery('#gc_gcalendar_view_toggle_status').bind('click', function(e) {

		e = new Event(e);
		gcSlide.toggle();

		var oldImage = jQuery('#gc_gcalendar_view_toggle_status').attr('src');
		var gcalImage = oldImage;
		var path = oldImage.substring(0, oldImage.lastIndexOf('/'));

		if (gcSlide.open)
			var gcalImage = path + '/down.png';
		else
			var gcalImage = path + '/up.png';

		jQuery('#gc_gcalendar_view_toggle_status').attr('src', gcalImage);

		e.stop();

	});
	gcSlide.hide();
});

function updateGCalendarFrame(calendar) {
	if (calendar.checked) {
		jQuery('#gcalendar_component').fullCalendar('addEventSource', calendar.value);
	} else {
		jQuery('#gcalendar_component').fullCalendar('removeEventSource', calendar.value);
	}
}

/* Not used for the moment, can be used to select only one calendar source*/
function updateGCalendarSelectOne(feedUrl) {

	//remove all event sources
	jQuery('#gcalendar_component').fullCalendar('removeEventSource',false);	

	//add new event source
	jQuery('#gcalendar_component').fullCalendar('addEventSource',feedUrl);

	//fixed calendars, always displayed
	//var vacations = feedUrl.replace(/gcid=[0-9]+/, 'gcid=1');
	//jQuery('#gcalendar_component').fullCalendar('addEventSource',vacations);
}
