<?php
$params = array('page' => 'smokesignalgroup', 'view' => 'new-group');
$newGroupLink = add_query_arg($params);
?>

<div class="wrap">
    <h2><?= __('User groups', 'smokesignal') ?>
        <a class="add-new-h2" href="<?= $newGroupLink ?>"><?= __('New group', 'smokesignal') ?></a>
    </h2>

    <table class="wp-list-table widefat fixed posts">
        <tbody>
        <?php if(empty($args['groups'])) { ?>
            <tr>
                <td>
                    <?= __('No created groups.', 'smokesignal') ?>
                </td>
            </tr>
        <?php } else { ?>
            <?php $i = 0; ?>
            <tr>
                <th><?= __('Group name', 'smokesignal') ?></th>
                <th><?= __('Users', 'smokesignal') ?></th>
                <th></th>
            </tr>
            <?php foreach($args['groups'] as $group) { ?>
                <?php
                $params = array('page' => 'smokesignalgroup', 'view' => 'edit-group', 'group_id' => $group->id);
                $editLink = add_query_arg($params);
                $i++;
                ?>
                <tr class="<?= $i%2 ? 'alternate' : '' ?>">
                    <td>
                        <a href="<?= $editLink ?>">
                            <?= htmlspecialchars($group->name) ?>
                        </a>
                    </td>
                    <td>
                        <?= $group->user_names ?>
                    </td>
                    <td>
                        <a class="add-new-h2" href="<?= $editLink ?>"><?= __('Edit', 'smokesignal') ?></a>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>

        </tbody>

    </table>

</div>