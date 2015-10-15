<div<?php if (!isset($options['resetFormClass'])) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $partId ?>"
        class="control-label<?php if (!empty($options['srOnly'])) : ?> sr-only<?php endif; ?><?php if (!empty($options['horizontal'])) : ?> col-md-<?= $options['horizontal'] ?><?php endif; ?>"
    ><?= $options['label'] ?></label>

    <?php if (!empty($options['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $options['horizontal'] ?>"><?php endif; ?>
        <input type="text"
               class="<?= $element ?> <?= $name ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               id="<?= $widgetClassName . '_' . $widgetName . '_' . $name ?>"
               name="<?= $name ?>"
               value="<?= isset($params[$name]) ? $params[$name] : '' ?>"
               data-for="<?= $widgetId ?>"
               <?php if (!empty($options['placeholder'])) : ?>placeholder="<?= $options['placeholder'] ?>"<?php endif; ?>
               <?php if (!empty($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?>"<?php endif; ?>
               <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
               <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
               <?php if (!empty($options['required'])) : ?>required="required" <?php endif; ?>
            <?php if (!empty($options['autofocus'])) : ?>autofocus="autofocus" <?php endif; ?>
        >
        <?php if (!empty($options['horizontal'])) : ?></div><?php endif; ?>
    <script>
        $(function () {
            $("#<?= $widgetClassName . '_' . $widgetName . '_' . $name ?>").datepicker({dateFormat: 'yy-mm-dd'});
        });
    </script>
</div>
