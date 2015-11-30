<?php
/** @var Module[] $modules */
/** @var $course_standard_module_hours */
/** @var $user_standard_module_hours */
?>

<div id="edit_module_settings">
    <form action="?action=save_module_settings" method="post" data-ajax="true" data-ajax-onsubmit-success="onModuleSettingsSaveSuccess" data-ajax-onsubmit-fail="onAjaxSaveFail">
        <div class="row">
            <div class="large-7 columns text-left">
                <label for="user_standard_module_hours"><strong>Standard modullengde for alle moduler:</strong></label>
            </div>
            <div class="large-3 columns">
                <input name="user_standard_module_hours" class="<?=$user_standard_module_hours ? ' overridden' : ''?>" id="user_standard_module_hours" type="number"
                       placeholder="<?=$course_standard_module_hours?> (standard)" value="<?=$user_standard_module_hours?>" data-orig-value="<?=$course_standard_module_hours?>">
            </div>
            <div class="large-1 columns end">
                <label for="user_standard_module_hours"> timer</label>
            </div>
        </div>
        <?php foreach ($modules as $m): ?>
            <div class="row">
                <div class="large-8 columns">
                    <label for="module-<?=$m->id?>"><?=$m->name?></label>
                </div>
                <div class="large-2 columns">
                    <input type="text" class="js-module-setting<?= $m->is_estimate_overridden ? ' orig-overridden' : ''?>"  id="module-<?=$m->id?>" value="<?=$m->estimated_hours?>" data-orig-value="<?=$m->estimated_hours ?>">
                </div>
            </div>
        <?php endforeach ?>
	    <div class="row">
	        <div class="large-6 columns">
		        <button type="button" class="button small secondary" id="close">Avbryt</button>
            </div>
		    <div class="large-6 columns text-right">
			    <button type="submit" class="button small success " id="save">Lagre</button>
            </div>
		</div>
    </form>
</div>