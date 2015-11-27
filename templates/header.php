<?php
/** @var CourseEnrollment $course_data */
/** @var Module[] $active_modules */

?>
<div class="row">
	<div class="large-4 columns">
		<dl id="course-info">
			<dt>Kurs:</dt>
			<dd><?= $course_data->name ?> (<?= $course_data->modules_count ?> moduler, <?= $course_data->exam_count ?> eksamener)</dd>

			<dt>Påmeldt:</dt>
			<dd><?= fdate(DATE_FORMAT_LONG_DATE, $course_data->enrolled_date) ?></dd>

			<dt>Fullførte moduler:</dt>
			<dd>
				<div class="progress" role="progressbar">
					<span class="progress-meter success" style="width: <?=
						$course_data->completed_modules_count / $course_data->modules_count * 100
					?>%"></span>
					<span class="progress-meter-text"><?= $course_data->completed_modules_count ?> / <?= $course_data->modules_count ?></span>
				</div>

			</dd>
		</dl>
	</div>
	<div class="large-8 columns">
	<h4>Påbegynte moduler pr. <?= fdate(DATE_FORMAT_SHORT_DATE) ?> </h4>
	<?php if (!count($active_modules)): ?>
		<em>Ingen påbegynte moduler</em>
	<?php else: ?>
		<ul id="active-modules">
		<? foreach ($active_modules as $m): ?>
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