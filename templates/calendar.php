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
									<i class="fi-arrow-right"></i>
									Neste
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

				$days_per_row = 8; // dayboxes per row (incluing week num)
				$days_per_column = 6; // dayboxes per column. Some months span 6 week distinct weeks

				$cell_count = $days_per_row * $days_per_column;

				$days_in_month = date('t', mktime(0,0,0,$month,1,$year));
				$day_of_first = strftime('%u', mktime(0,0,0,$month,1,$year));
				$skip_days = 0;
				$first_row = true;
				for ($i=0; $i<$cell_count; $i++) {

					$d = $i - ($i / $days_per_row % $days_per_column) - $skip_days;

					if ($i && $i % $days_per_row) {
						if ($skip_days < $day_of_first-1) {
							$skip_days++;
							echo '<td class="prev-month">&nbsp;</td>';
							continue;
						}

						if ($d <= $days_in_month) {
							$ymd = sprintf('%02d-%02d-%d', $year, $month, $d);
							$class = '';//'date-'. $ymd;
							if ($ymd == date('Y-m-d')) {
								$class .= " is-today";
							}
							echo '<td class="' . $class . '">';
							echo '<span class="date">' . $d . '</span>';

							display_entries_for_date($d, $month, $year, $entries);

							echo '</td>';
						}
					}
					else if ($d < $days_in_month ) {
						// weeknumber
						$wn = strftime('%V', mktime(0, 0, 0, $month, $d + 1, $year));
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

function display_entries_for_date($day, $month, $year, $entries) {

	$key = date('Y-m-d', mktime(12, 0, 0, $month, $day, $year));
	if (empty($entries[$key])) return;

	foreach ($entries[$key] as $session) {
		/** @var Session $session */
		render_calendar_entry($session);
	}


}

function render_calendar_entry(Session $session) {
	echo '<em>' . $session->module->name . '</em>';
}

function get_month_link($month, $year, $add) {
	return '?showmonth=' . date('Y-m', mktime(10, 0, 0, $month + $add, 15, $year));
}