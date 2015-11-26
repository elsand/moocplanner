<?php
/** @var Module[] $modules */
/** @var $standard_module_hours */
?>

<div id="module_settings">
    <div class="row">
        <div class="large-12 columns text-right">
            <label>Standard modullengde <input type="number" value="<?= $standard_module_hours ?>"> timer</label>
        </div>
    </div>
            <?php foreach ($modules as $m): ?>
                <div class="row">
                    <div class="large-6 columns">
                        <label for="module-<?=$m->id?>"><?=$m->name?></label>
                    </div>
                    <div class="large-6 columns">
                        <input id="module-<?=$m->id?>" value="<?=$m->estimated_hours?>">
                    </div>
                </div>
            <?php endforeach ?>
</div>