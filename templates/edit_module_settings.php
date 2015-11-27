<?php
/** @var Module[] $modules */
/** @var $standard_module_hours */
?>

<div id="edit_module_settings">
    <form action="" method="post">
        <div class="row">
            <div class="large-8 columns text-right">
                <label for="standardAll" class="right">Angi standard modullengde for alle moduler: </label>
            </div>
            <div class="large-2 columns">
                <input id="standardAll" type="number" value="30">
            </div>
            <div class="large-2 columns"> timer</div>
        </div>
        <hr />
        <?php foreach ($modules as $m): ?>
            <div class="row">
                <div class="large-8 columns">
                    <label for="module-<?=$m->index?>"><?=$m->name?></label>
                </div>
                <div class="large-2 columns">
                    <input type="text" id="module-<?=$m->index?>" value="<?=$m->estimated_hours?>">
                </div>
                <div class="large-2 columns">
                    <?php if($m->index == (count($modules)-1)): ?>
                        <button class="button small" id="abort">Avbryt</button>
                    <?php endif ?>
                    <?php if($m->index == count($modules)): ?>
                        <button class="button small" id="save_module_settings">Lagre</button>
                    <?php endif ?>
                </div>
            </div>
        <?php endforeach ?>
    </form>
</div>