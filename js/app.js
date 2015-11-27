$(document).foundation();

$('body')
	.on('click', '.js-calendar-add-session', function(e) {
		console.log(e);
		createPopup('edit-session new-session', 'Ny arbeidsøkt', '?action=new_session&date=' + $(e.currentTarget).attr('id').replace('date-', ''), e);
	})
	.on('click', '.js-calendar-edit-session', function(e) {
		createPopup('edit-session', 'Endre arbeidsøkt', '?action=edit_session&session_id=' + $(this).data('id'), e);
	})
	.on('click', '#popup .close', function() {
		$(this).parent().remove();
	})
	.on('click', '#popup #repeatable', function() {

		if ($(this).is(':checked')) {
			$('#repeatable-container').css('visibility', 'visible');
		}
		else {
			$('#repeatable-container').css('visibility', 'hidden');
		}
	})
	.on('submit', 'form[data-ajax="true"]', function() {

		var $form = $(this);
		var data = $form.serialize();
		var url = $form.attr('action');
		$form.css('opacity', '0.3');
		$form.find(':input').attr('disabled', 'disabled');

		var success_callback = $form.attr('data-ajax-onsubmit-success') ? window[$form.attr('data-ajax-onsubmit-success')] : function() {};
		var fail_callback = $form.attr('data-ajax-onsubmit-fail') ? window[$form.attr('data-ajax-onsubmit-fail')] : function() {};

		$.post(url, data, function(r) {
			$form.find(':input').removeAttr('disabled');
			$form.css('opacity', '1');
			if (r.is_error) {
				fail_callback(r);
			}
			else {
				success_callback(r);
			}
		}, 'json');

		return false;
	})

;

function onSessionSaveSuccess(r) {
	closePopup();
	reloadCalendar();
}

function onSessionSaveFail(r) {
	alert(r.data[0]);
}

function reloadCalendar() {
	$('#calendar').load(window.location.toString() + "  #calendar >*");
}

function createPopup(css_class, title, url, click_event) {
	var popup_id = 'popup';
	var popup_selector = '#' + popup_id;

	if ($(popup_selector).length) {
		$(popup_selector).remove();
	}

	var $popup = $('<div id="' + popup_id + '" class="' + css_class + '">' +
		'<span class="title">' + title + '</span>' +
		'<a class="close" href="javascript:"><i class="fi-x"></i></a>' +
		'<div class="content"><span class="please-wait"></span></div>' +
		'</div>');

	if (click_event) {
		$popup.css('left', click_event.pageX + 'px');
		$popup.css('top', click_event.pageY + 'px');
	}

	$('body').append($popup);

	$.get(url, function(r) {
		$popup.find('.content').html(r);
	});
}

function closePopup() {
	var popup_id = 'popup';
	var popup_selector = '#' + popup_id;

	if ($(popup_selector).length) {
		$(popup_selector).remove();
	}
}