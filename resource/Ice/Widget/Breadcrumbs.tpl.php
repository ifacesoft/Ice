<ol id="<?= $widgetId ?>"
    class="<?= $widgetClass ?> breadcrumb<?php if (!empty($classes)) { ?> <?= $classes ?><?php } ?>"
    data-widget='<?= $dataWidget ?>'
    data-params='<?= $dataParams ?>'
    data-for="<?= $parentWidgetId ?>"
>
    <?php foreach (reset($result) as $part) : ?>
        <?= $part->render() ?>
    <?php endforeach; ?>
</ol>