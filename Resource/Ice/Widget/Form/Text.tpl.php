<div <?php if (!isset($options['resetFormClass'])) : ?>class="form-group"<?php endif; ?>>
    <label
        for="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?>"
        class="control-label<?php if (isset($options['srOnly'])) : ?> sr-only<?php endif; ?><?php if (isset($options['horizontal'])) : ?> col-md-<?= $options['horizontal'] ?><?php endif; ?>"
    ><?= $options['label'] ?></label>

    <?php if (isset($options['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $options['horizontal'] ?>"><?php endif; ?>
        <input id="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?>"
               type="text"
               class="<?= $element ?> <?= $name ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               name="<?= $name ?>"
               value="<?= isset($params[$name]) ? $params[$name] : '' ?>"
               data-for="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
               <?php if (isset($onchange)) : ?>onchange="<?= $onchange ?> return false"<?php endif; ?>
            <?php if (isset($options['placeholder'])) : ?> placeholder="<?= $options['placeholder'] ?>"<?php endif; ?>
            <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
            <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>
            <?php if ($options['required']) : ?> required="required" <?php endif; ?>
            <?php if ($options['autofocus']) : ?> autofocus="autofocus" <?php endif; ?>>
        <?php if (isset($options['horizontal'])) : ?></div><?php endif; ?>
</div>
