<ol id="<?= $widgetId ?>"
    class="<?= $widgetClass ?>  nav <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
    <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
    data-widget='<?= $dataWidget ?>'
    data-for="<?= $parentWidgetId ?>"
>
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $item) : ?>
        <?= $item ?>
    <?php endforeach; ?>
</ol>
