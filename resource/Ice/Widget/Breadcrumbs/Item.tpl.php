<li <?= $component->getIdAttribute() ?> <?= $component->getClassAttribute() ?>>
    <a href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
        <?= $component->getEventAttributesCode() ?>><?= $component->getValue() ?></a>
</li>