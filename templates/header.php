<?php
/** @var CourseEnrollment $course_data */
/** @var Module[] $active_modules */

?>
<div class="row">
	<div class="large-5 columns" id="course-overview">
		<h5>Kursoversikt</h5>
		<div class="row">
			<div class="large-2 columns">Kurs:</div>
			<div class="large-10 columns"><strong><?= $course_data->name ?> (<?= $course_data->modules_count ?> moduler, <?= $course_data->exam_count ?> eksamener)</strong></div>
		</div>
		<div class="row">
			<div class="large-2 columns">Påmeldt:</div>
			<div class="large-10 columns"><strong><?= fdate(DATE_FORMAT_LONG_DATE, $course_data->enrolled_date) ?></strong></div>
		</div>
		<div class="row">
			<div class="large-2 columns">Fremdrift:</div>
			<div class="large-10 columns">
				<div class="progress" role="progressbar">
					<span class="progress-meter success" style="width: <?=
						$course_data->completed_modules_count / $course_data->modules_count * 100
					?>%"></span>
					<span class="progress-meter-text"><?= $course_data->completed_modules_count ?> / <?= $course_data->modules_count ?> moduler fullført</span>
				</div>
			</div>
		</div>
	</div>
	<div class="large-7 columns">
	<h5>Påbegynte moduler per <?= fdate(DATE_FORMAT_LONG_DATE) ?> </h4>
	<?php if (!count($active_modules)): ?>
		<em>Ingen påbegynte moduler</em>
	<?php else: ?>
		<ul id="active-modules">
		<?php foreach ($active_modules as $m): ?>
			<li>
				<input type="checkbox"> <?= $m->name ?>
				<div class="progress multiple" role="progressbar">
					<span class="progress-meter spent" style="width: <?=
						$m->spent_hours / $m->estimated_hours * 100
					?>%"></span><span class="progress-meter booked" style="width: <?=
						$m->booked_hours / $m->estimated_hours * 100
					?>%"></span>

				</div>

			</li>
		<?php endforeach ?>
		</ul>
	<?php endif ?>
	</div>
</div>