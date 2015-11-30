<?php
/**
 * Footer template
 *
 * @var Module[] $not_fully_booked
 */

?>
<div id="footer">
	<div class="row collapse">
		<?php if ($not_fully_booked): ?>
		<div class="large-5 columns callout warning" id="notfullybooked">
			<strong><i class="fi-alert"></i> Følgende moduler er ikke fullt booket i kalenderen:</strong>
			<table>
			<?php foreach ($not_fully_booked as $m):
				$perc = floor((($m->spent_hours + $m->booked_hours) / $m->estimated_hours) * 100);
			?>
				<tr>
					<td class="text-right"><?= $m->index ?></td>
					<td class="module-name"><span><?=h($m->name)?></span></td>
					<td>
						<div class="progress inline-text">
							<span class="progress-meter-text"><?=$perc?>%</span>
							<span class="progress-meter" style="width:<?=$perc?>%"></span>
						</div>
					</td>
					<td class="text-right">Mangler <?= $m->estimated_hours - $m->spent_hours - $m->booked_hours ?> timer</td>
				</tr>
			<?php endforeach ?>
			</table>
		</div>
		<?php else: ?>
		<div class="large-5 columns"></div>
		<?php endif ?>
		<div class="large-5 columns">
			<div id="flash-container"></div>
		</div>
		<div class="large-2 columns text-right">
			<button class="button secondary" id="js-module-settings">Modulinnstillinger</button>
		</div>
	</div>
</div>
