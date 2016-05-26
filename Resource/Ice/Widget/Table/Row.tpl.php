<thead>
<tr>
    <?php foreach ($component->getOption('row', []) as $key => $column) : ?>
        <?php if (is_string($key)) : ?>
            <?php if (is_array($column)) : ?>
                <?php
                $options = $column;

                $column = isset($options['colspan']) ? $options['colspan'] : 1;
                ?>
            <?php endif; ?>

            <td colspan="<?= $column ?>"><?= $key ?></td>
        <?php else : ?>
            <td><?= $column ?></td>
        <?php endif; ?>
    <?php endforeach; ?>
</tr>
</thead>