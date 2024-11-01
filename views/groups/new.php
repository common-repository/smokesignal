<?php
$params = array('page' => 'smokesignalgroup', 'view' => 'new-group');
$newGroupLink = add_query_arg($params);
?>

<div class="wrap">
    <h2><?= __('Create new group', 'smokesignal') ?></h2>

    <form method="post" action="">
        <table class="form-table"><tbody>
            <tr>
                <th scope="row"><label for="group_name"><?= __('Group name', 'smokesignal') ?></label></th>
                <td><input type="text" name="group_name" /></td>
            </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" class="button button-primary" name="submit_new_group" value="<?= __('Create', 'smokesignal') ?>"/>
        </p>
    </form>
</div>