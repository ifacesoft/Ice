<li id="<?= $partId ?>"
    class="<?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>
<?php if (!empty($options['active'])) : ?> active<?php endif; ?>">
    <a href="<?php if (isset($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"
       <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>
       data-name="<?= $name ?>"
       data-params='<?= $dataParams ?>'
       <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
       data-for="<?= $widgetId ?>"><?= $options['label'] ?></a>
</li>