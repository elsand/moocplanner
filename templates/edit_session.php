<?php
/** @var Session $session */
/** @var Module[] $modules */
/** @var DateTime $date */
?>

<div id="edit_session" class="<?= $session->id ? 'edit' : 'new'?>">
	<form action="?action=save_session" method="post" data-ajax="true" data-ajax-onsubmit-success="onSessionSaveSuccess" data-ajax-onsubmit-fail="onSessionSaveFail">
		<input type="hidden" name="session_id" value="<?=$session->id?>">
		<input type="hidden" name="date" value="<?=$date->format('Y-m-d')?>">
		<div class="row large-collapse">
			<div class="large-6 columns">
				<label for="module_id" class="text-left middle">Velg modul du vil jobbe på:</label>
			</div>
			<div class="large-6 columns">
				<select id="module_id" name="module_id">
					<?php foreach ($modules as $m):
						$unbooked_hours = $m->estimated_hours - $m->spent_hours - $m->booked_hours;
						$is_editing = $session->module->id == $m->id;
						if ($unbooked_hours <= 0 && !$is_editing) {
							continue;
						}
						?>
					<option value="<?=$m->id?>"<?=$is_editing ? ' class="currently-selected" selected' : '' ?>>
						<?= h($m->name) ?>
						(<?= $unbooked_hours ?> timer ikke booket)
						<?= $is_editing ? ' (VALGT) ' : '' ?>
					</option>
					<? endforeach ?>
				</select>
			</div>
		</div>
		<div class="row large-collapse">
			<div class="large-8 columns">
				<label for="duration_hours" class="text-left middle">Hvor lang økt?</label>
			</div>
			<div class="large-2 columns">
				<input id="duration_hours" name="duration_hours" type="number" min="1" max="24" value="<?= $session->duration_hours ?: '' ?>">
			</div>
			<div class="large-2 columns">
				<label for="duration_hours" class="text-center middle">timer</label>
			</div>
		</div>

		<div class="row large-collapse">
			<input id="repeatable" name="repeatable" type="checkbox" <?=$session->repeatable ? ' checked' : ''?> value="1"><label for="repeatable" class="middle">Gjenta</label>
		</div>


		<div id="repeatable-container" <?=!$session->repeatable ? 'style="visibility:hidden"' : ''?>>
			<div class="row large-collapse">
				<div class="large-1 columns">
					<label for="repeat_interval_weeks" class="middle">Hver</label>
				</div>
				<div class="large-2 columns">
					<select name="repeat_interval_weeks" id="repeat_interval_weeks">
						<?php for ($i=1; $i<20; $i++): ?>
						<option value="<?=$i?>" <?= $session->repeat_interval_weeks == $i ? ' selected' : '' ?>><?=$i?></option>
						<? endfor ?>
					</select>
				</div>
				<div class="large-6 columns end">
					<label for="repeat_interval_weeks" class="middle text-left">&nbsp;uke(r), på ukedagene:</label>
				</div>
			</div>
			<div class="row large-collapse">
				<fieldset>
					<input type="checkbox" id="day_1" name="repeat_days[]" value="1"<?=in_array(1, $session->repeat_days) ? ' checked' : ''?>><label for="day_1">M</label>
					<input type="checkbox" id="day_2" name="repeat_days[]" value="2"<?=in_array(2, $session->repeat_days) ? ' checked' : ''?>><label for="day_2">T</label>
					<input type="checkbox" id="day_3" name="repeat_days[]" value="3"<?=in_array(3, $session->repeat_days) ? ' checked' : ''?>><label for="day_3">O</label>
					<input type="checkbox" id="day_4" name="repeat_days[]" value="4"<?=in_array(4, $session->repeat_days) ? ' checked' : ''?>><label for="day_4">T</label>
					<input type="checkbox" id="day_5" name="repeat_days[]" value="5"<?=in_array(5, $session->repeat_days) ? ' checked' : ''?>><label for="day_5">F</label>
					<input type="checkbox" id="day_6" name="repeat_days[]" value="6"<?=in_array(6, $session->repeat_days) ? ' checked' : ''?>><label for="day_6">L</label>
					<input type="checkbox" id="day_7" name="repeat_days[]" value="7"<?=in_array(7, $session->repeat_days) ? ' checked' : ''?>><label for="day_7">S</label>
				</fieldset>
			</div>
		</div>

		<div class="row large-collapse">
			<div class="large-6 columns text-left">
				<button type="button" class="small button secondary" id="cancel">Avbryt</button>
			</div>
			<div class="large-6 columns text-right">
				<button type="submit" class="small button" id="save">Lagre</button>
			</div>
		</div>
	</form>


</div>