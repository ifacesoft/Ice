<li class="menu_item<?php if (!empty($options['active'])) : ?> active<?php endif; ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>">
    <a href="<?php if (isset($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"
       <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?> return false;"<?php endif; ?>
       data-name="<?= $name ?>"
       data-params='<?= $dataParams ?>'
       data-for="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"><?= $title ?></a>
</li>