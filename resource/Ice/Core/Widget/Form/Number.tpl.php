<div class="form-group">
    <label for="<?= $formName . '_' . $fieldName ?>"><?= $title ?></label>
    <input type="text" class="form-control" id="<?= $formName . '_' . $fieldName ?>"
           placeholder="<?= $options['placeholder'] ?>"
           name="<?= $fieldName ?>" value="<?= $value ?>" style="width: 100%;"
           <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
           data-url='<?= $dataUrl ?>'
           data-json='<?= $dataJson ?>'
           data-action='<?= $dataAction ?>'
           data-block='<?= $dataBlock ?>'
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
</div>
