<?php
/** @var Module[] $not_fully_booked */
// Comments
?>
<div class="row">
    <div class="large-8 columns">
        <?php if (!$not_fully_booked): ?>
            <em>Alle moduler er booket i kalenderen.</em>
        <?php else: ?>
            <dl>
                <dt>Følgende moduler er ikke fullt booket i kalenderen:</dt>
                <div class="footer_module_list">
                    <?php foreach ($not_fully_booked as $m): ?>
                        <dd><?= "Modul " . $m->index . ": " . $m->name . " " . floor(($m->booked_hours / $m->estimated_hours) * 100) . "%" . " (" . $m->booked_hours . " av " . $m->estimated_hours . " timer)"; ?></dd>
                    <?php endforeach ?>
                </div>
            </dl>
        <?php endif ?>
    </div>
    <div class="large-2 columns">
        <button class="button secondary" id="js-module-settings">Modulinnstillinger</button>
    </div>
</div>