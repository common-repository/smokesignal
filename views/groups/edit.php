<?php
/**
 * @var array $args
 */

$params = array('page' => 'smokesignalgroup', 'view' => 'new-group');
$newGroupLink = add_query_arg($params);

$group = $args['group'];

?>

<div class="wrap">
    <h2><?= __('Edit group', 'smokesignal') ?></h2>

    <form method="post" action="">
        <input type="hidden" name="group_id" value="<?= $group->id ?>"/>
        <table class="form-table"><tbody>
            <tr>
                <th scope="row"><label for="group_name"><?= __('Group name', 'smokesignal') ?></label></th>
                <td><input type="text" name="group_name" value="<?= htmlspecialchars($group->name) ?>" /></td>
            </tr>
            </tbody>
        </table>

        <div>
            <h2><?= __('Users', 'smokesignal') ?></h2>
            <ul>
                <?php foreach($args['users'] as $user) { ?>
                    <li>
                    <input type="checkbox" value="1" name="user[<?= $user->user_id ?>]"
                        <?= !empty($user->group_id) ? 'checked="true"' : '' ?>
                        />
                    <label for="user[<?= $user->user_id ?>]">
                        <?= $user->display_name ?>
                    </label>
                    </li>
                <?php } ?>
            </ul>
        </div>


        <p class="submit">
            <input type="submit" class="button button-primary" name="submit_edit_group" value="<?= __('Save', 'smokesignal') ?>"/>
        </p>
    </form>
</div>