$(document).foundation();

// Bind all events to body, so that we can refresh all the content wihout losing handlers
$('body')
	// When clicking "Modulinnstillinger"
	.on('click', '#js-module-settings', function(e) {
		createPopup('edit-module-settings', 'Rediger modulinnstillinger', '?action=edit_module_settings');
	})
	// When checking a checkbox in the list "Påbegynte moduler ..."
	.on('click', '.mark-completed-checkbox', function(e) {
		if (!confirm('Er du sikker du vil merke denne modulen som fullført?')) return;
		var id = $(this).attr('data-id');
		$(this).parents('.row').first().fadeOut(function() {
			$.post('?action=complete_module', { module_id: id }, function(r) {
				r.is_error ? onAjaxSaveFail(r) : onAjaxSaveSuccess(r, 'Modulen er nå satt som fullført.');
			}, 'json')
		});
	})
	// When clicking a date in the calendar
	.on('click', '.js-calendar-add-session', function(e) {
		createPopup('edit-session new-session', 'Ny arbeidsøkt: ' + $(e.currentTarget).data('date'), '?action=new_session&date=' + $(e.currentTarget).data('date'), e);
	})
	// When clicking a session in the calendar
	.on('click', '.js-calendar-edit-session', function(e) {
		createPopup('edit-session', 'Endre arbeidsøkt', '?action=edit_session&session_id=' + $(this).data('id'), e);
	})
	// When clicking either the X-button or "Avbryt" in a popup
	.on('click', '#popup .close, #popup #close', function() {
		$("#popup").remove();
	})
	// When toggling the "Gjenta" checkbox in edit session
	.on('click', '#popup #repeatable', function() {
		if ($(this).is(':checked')) {
			$('#repeatable-container').css('visibility', 'visible');
		}
		else {
			$('#repeatable-container').css('visibility', 'hidden');
		}
	})
	// When clicking the "Slett" button in edit session
	.on('click', '#edit_session #delete', function() {
		if (!confirm('Er du sikker på du vil slette denne sesjonen? Dette vil slette alle reperterte økter.')) return;
		$.post('?action=delete_session', { session_id: $(this).attr('data-id') }, function(r) {
			r.is_error ? onAjaxSaveFail(r) : onAjaxSaveSuccess(r, 'Sesjonen er nå slettet.');
		}, 'json')
	})
	// When clicking the "Fullfør" button in edit session
	.on('click', '#edit_session #complete', function() {
		if (!confirm('Er du sikker på du vil merke denne modulen som fullført?')) return;
		$.post('?action=complete_module', { module_id: $(this).attr('data-id') }, function(r) {
			r.is_error ? onAjaxSaveFail(r) : onAjaxSaveSuccess(r, 'Modulen er nå satt som fullført.');
		}, 'json')
	})
	// When clicking the "Gjenåpne" button in edit session
	.on('click', '#edit_session #reopen', function() {
		if (!confirm('Er du sikker på du vil gjenåpne denne modulen?')) return;
		$.post('?action=reopen_module', { module_id: $(this).attr('data-id') }, function(r) {
			r.is_error ? onAjaxSaveFail(r) : onAjaxSaveSuccess(r, 'Modulen er nå gjenåpnet.');
		}, 'json')
	})
	// When changing an hour estimate in the module settings popup. A name attribute is added if
	// the value differs from the one saved in the db, causing the new value to be submitted
	.on('change', '.js-module-setting', function() {
		var $input = $(this);
		console.log('change');
		if ($input.val() != $input.attr('data-orig-value')) {
			$input.attr('name', $input.attr('id'));
			$input.addClass('overridden');
		}
		else {
			$input.removeAttr('name');
			$input.removeClass('overridden');
		}
	})
	// Standard handler for "ajaxified" forms, overriding standard submissions handling
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

// Callback after successfully saving module estimate settings. Reload the content of the popup
// as well as rest of the app.
function onModuleSettingsSaveSuccess() {
	$("#popup").find('.content').load('?action=edit_module_settings');
	reloadEverything(function() { showFlash('Modulinnstillinger lagret', 'success')});
}

// Default success handler for dealing with ajax submissions. Close the popup, and reload the view,
// showing a flash message upon completion
function onAjaxSaveSuccess(r, msg) {
	closePopup();
	msg = msg || 'Oppføringen er oppdatert.';
	reloadEverything(function() {
		showFlash(msg, 'success');
	});
}

// Default error handler for dealing with ajax submissions. Show a flash with the error message
// from the server-side.
function onAjaxSaveFail(r) {
	showFlash(r.data[0], 'alert');
}

// Reload the view via ajax, and call the supplied callback with the results
function reloadEverything(cb) {
	$('#main-container').load(window.location.toString(), function(r) {
		if (typeof cb == "function") {
			cb(r);
		}
	});
}

// Method for displaying a flash message in the footer of the screen for ~5 seconds
var _vto;
function showFlash(str, type) {
	clearTimeout(_vto);
	var $cont = $('#flash-container');
	$cont.html('');
	$cont.html('<div class="callout ' + type + '"><i class="fi-' + type + '"></i> ' + str + '</str>');
	_vto = setTimeout(function() {
		$cont.find('div').fadeOut(2000, function() {
			$cont.find('div').remove();
		});
	}, 3000)
}

// Create a faux popup window and populate with supplied URL via ajax. Use jQuery UI to make it draggable.
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

	$popup.draggable();
}

// Closes the popup if open. Only one popup can be open at a time.
function closePopup() {
	var popup_id = 'popup';
	var popup_selector = '#' + popup_id;

	if ($(popup_selector).length) {
		$(popup_selector).remove();
	}
}