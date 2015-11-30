<?php
/** @var $year */
/** @var $month */
/** @var $entries */
?>

<div id="calendar" class="row">
	<div class="large-12 columns">
		<table class="">
			<thead>
				<tr>
					<th colspan="8">
						<div class="row">
							<div class="large-3 columns">
								<a href="<?=get_month_link($month, $year, -1)?>"  class="small button" type="button">
									<i class="fi-arrow-left"></i>
									Forrige
								</a>
							</div>
							<div class="large-6 columns text-center">
								<h4><?= month_num_to_name($month) ?> <?= $year ?></h4>
							</div>
							<div class="large-3 columns text-right">
								<a href="<?=get_month_link($month, $year, 1)?>" class="small button" type="button">
									Neste
									<i class="fi-arrow-right"></i>
								</a>
							</div>
						</div>
					</th>
				</tr>
				<tr>
					<th class="week-header week-num">Uke</th>
					<th class="week-header">Mandag</th>
					<th class="week-header">Tirsdag</th>
					<th class="week-header">Onsdag</th>
					<th class="week-header">Torsdag</th>
					<th class="week-header">Fredag</th>
					<th class="week-header">Lørdag</th>
					<th class="week-header">Søndag</th>
				</tr>
			</thead>
			<tbody>
				<?php

				// Render the calendard view

				$days_per_row = 8; // dayboxes per row (including week num)
				$days_per_column = 6; // dayboxes per column. Some months span 6 week distinct weeks

				$cell_count = $days_per_row * $days_per_column;

				$days_in_month = date('t', mktime(0,0,0,$month,1,$year));
				$day_of_first = strftime('%u', mktime(0,0,0,$month,1,$year));
				$skip_days = 0;
				$first_row = true;
				for ($i=0; $i<$cell_count; $i++) {

					$date = $i - ($i / $days_per_row % $days_per_column) - $skip_days;

					if ($i && $i % $days_per_row) {
						if ($skip_days < $day_of_first-1) {
							$skip_days++;
							echo '<td class="prev-month">&nbsp;</td>';
							continue;
						}

						if ($date <= $days_in_month) {
							$ymd = sprintf('%02d-%02d-%02d', $year, $month, $date);
							$class = '';
							if ($ymd == date('Y-m-d')) {
								$class .= " is-today";
							}
							echo '<td class="' . $class . '">';
							echo '<span class="date"><a title="Klikk for å legge til økt på denne dagen" class="js-calendar-add-session" data-date="' . $ymd . '" id="date-' . $ymd . '" href="javascript:">' . $date . '</a></span>';
							echo '<button class="tiny button js-calendar-add-session" data-date="' . $ymd . '"><i class="fi-plus"></i></button>';

							display_entries_for_date($date, $entries);

							echo '</td>';
						}
					}
					else if ($date < $days_in_month ) {
						// weeknumber
						$wn = strftime('%V', mktime(0, 0, 0, $month, $date + 1, $year));
						if ($first_row) {
							$first_row = false;
						}
						else {
							echo "</tr>";
						}
						echo '<tr><th class="weeknum"><span>'. $wn . '</span></th>';
					}
				}
				echo "</tr>";

				?>
			</tbody>
		</table>
	</div>
</div>


<?php

/**
 * Loops the supplied entries from server side for this date, and called render_calendar_entry() for each
 *
 * @param $date
 * @param $entries
 */
function display_entries_for_date($date, $entries) {
	if (empty($entries[$date])) return;
	foreach ($entries[$date] as $session) {
		/** @var Session $session */
		render_calendar_entry($session);
	}
}

/**
 * Renders a "box" representing the session in the calendar, using a semi-unique color (derived from name) and a progressbar.
 *
 * @param Session $session
 */
function render_calendar_entry(Session $session) {
	$perc_complete = round(($session->module->spent_hours + $session->module->booked_hours) / $session->module->estimated_hours * 100);
?>
<div class="session session-<?=$session->id?> <?= $session->module->completed ? 'completed' : ''?> js-calendar-edit-session" data-id="<?=$session->id?>" id="session-<?=$session->id?>-<?=$session->date->format('Ymd')?>" style="<?= generate_style_from_module($session->module)?> ">
	<span class="module-order"><?=$session->module->index?></span>
	<span class="session-repeated"<?=$session->is_repeated ? ' title="Dette er en repetert arbeidsøkt"' : ''?>><?=$session->is_repeated ? 'R' : ''?></span>
	<span class="module-name" title="<?=h($session->module->name)?>"><?=h($session->module->name)?></span>
	<span class="session-duration"><?=$session->duration_hours?>t</span>
	<div class="progress">
		<span class="progress-meter" style="width: <?=$perc_complete?>%" title="Etter denne økten er du <?=$perc_complete?>% ferdig med modulen"></span>
	</div>
</div>

<?php
}

/**
 * Generates links for navigating the calendar. Use either 1 or -1 for next or prev-links.
 *
 * @param $month
 * @param $year
 * @param $add
 *
 * @return string
 */
function get_month_link($month, $year, $add) {
	return '?showmonth=' . date('Y-m', mktime(10, 0, 0, $month + $add, 15, $year));
}

/**
 * Generates a somewhat unique color based on the name of the module, using md5() to make it
 * likely that it does not closely match any other module.
 *
 * Checks luminance on the generated color, and changes to white text if too dark.
 *
 * @param Module $module
 *
 * @return string
 */
function generate_style_from_module(Module $module) {

	$ret = 'background-color: #%s; color: #%s';
	$background_color_rgb = substr(md5($module->name . $module->index), 0, 6);
	$r = hexdec(substr($background_color_rgb, 0, 2));
	$g = hexdec(substr($background_color_rgb, 2, 2));
	$b = hexdec(substr($background_color_rgb, 4, 2));
	 // Using CCIR 601 formula, see https://en.wikipedia.org/wiki/Luma_%28video%29
	$luminance = 1 - ( 0.299 * $r + 0.587 * $g + 0.114 * $b)/255;
	$text_color_rgb = $luminance < 0.6 ? '000000' : 'FFFFFF';

	return sprintf($ret, $background_color_rgb, $text_color_rgb);
}