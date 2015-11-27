$(document).foundation();

$('body')
	.on('click', '.js-calendar-add-session', function(e) {
		createPopup('add-session', '?action=new_session', e);
	})
	.on('click', '.js-calendar-edit-session', function(e) {
		createPopup('edit-session', '?action=edit_session&session_id=' + $(this).data('id'), e);
	})
	.on('click', '#popup .close', function() {
		$(this).parent().remove();
	})
;


function createPopup(css_class, url, click_event) {
	var popup_id = 'popup';
	var popup_selector = '#' + popup_id;

	if ($(popup_selector).length) {
		$(popup_selector).remove();
	}

	var $popup = $('<div id="' + popup_id + '" class="' + css_class + '"><a class="close" href="javascript:"><i class="fi-x"></i></a><div class="content"><span class="please-wait"></span></div></div>');

	if (click_event) {
		$popup.css('left', click_event.pageX + 'px');
		$popup.css('top', click_event.pageY + 'px');
	}

	$('body').append($popup);

	$.get(url, function(r) {
		$popup.find('.content').html(r);
	});
}