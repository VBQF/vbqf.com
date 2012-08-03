window
		.addEvent(
				'domready',
				function() {
					var gcSlide = new Fx.Slide('gc_google_view_list');

					$('gc_google_view_toggle')
							.addEvent(
									'click',
									function(e) {
										e = new Event(e);
										gcSlide.toggle();

										var oldImage = window.document
												.getElementById('gc_google_view_toggle_status').src;
										var gcalImage = oldImage;
										var path = oldImage.substring(0,
												oldImage.lastIndexOf('/'));
										if (gcSlide.open)
											var gcalImage = path + '/down.png';
										else
											var gcalImage = path + '/up.png';
										window.document
												.getElementById('gc_google_view_toggle_status').src = gcalImage;
										e.stop();
									});
					gcSlide.hide();
				});

function updateGCalendarFrame(calendar) {
	var orig_url = window.document.getElementById('gcalendar_frame').src;
	var new_url = "";
	if (calendar.checked) {
		new_url = orig_url + calendar.value;
	} else {
		new_url = orig_url.replace(calendar.value, "");
	}
	window.document.getElementById('gcalendar_frame').src = new_url;
}
