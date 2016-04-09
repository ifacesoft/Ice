<li id="<?= $component->getPartId() ?>"
    class="<?= $component->getComponentName() ?>
<?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>
<?php if ($component->isActive()) : ?> active<?php endif; ?>
">
    <a href="<?php if (!empty($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $component->getComponentName() ?>"
       <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
       data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
       data-for="<?= $component->getWidgetId() ?>"><?= $component->getLabel() ?></a>
</li>