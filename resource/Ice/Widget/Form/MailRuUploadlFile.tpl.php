<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <div class="b-upload b-upload_dnd">
        <div class="b-upload__dnd"><?= $component->getLabel() ?></div>
        <div class="b-upload__dnd-not-supported" style="display: none;">
            <div class="btn btn-success js-fileapi-wrapper">
                <span>Choose files</span>
                <input <?= $component->getIdAttribute() ?>
                    name="filedata" multiple="" type="file">
            </div>
        </div>
        <div class="js-files b-upload__files">
            <div class="js-file-tpl b-thumb" data-id="<%=uid%>" title="<%-name%>, <%-sizeText%>">
                <div class="b-thumb__preview">
                    <div class="b-thumb__preview__pic"></div>
                </div>
                <div class="b-thumb__progress progress progress-small">
                    <div class="bar"></div>
                </div>
                <div class="b-thumb__name"><%-name%></div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('#<?= $component->getId() ?>').fileapi({
                url: '/ice/widget/form/file/upload',
                paramName: 'filedata',
                data: {
                    token: '<?= $dataToken ?>',
                    formName: '<?= $widgetClassName ?>',
                    fieldName: '<?= $component->getName() ?>'
                },
                autoUpload: <?php if (!empty($component->getOption('autoUpload')) && $component->getOption('autoUpload') == false) : ?>false<?php else : ?>true<?php endif; ?>,
                elements: {
                    list: '.js-files',
                    file: {
                        tpl: '.js-file-tpl',
                        preview: {
                            el: '.b-thumb__preview',
                            width: 80,
                            height: 80
                        },
                        upload: {show: '.progress'},
                        complete: {hide: '.progress'},
                        progress: '.progress .bar'
                    },
                    dnd: {
                        el: '.b-upload__dnd',
                        hover: 'b-upload__dnd_hover',
                        fallback: '.b-upload__dnd-not-supported'
                    }
                }
            });
        });
    </script>
</div>
