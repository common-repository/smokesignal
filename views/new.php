
<?php
$options = get_option('smokesignal_options');

$selectUser = true;
if(!empty($options['recipient_input_type']) && $options['recipient_input_type'] == 'input') {
    $selectUser = false;
}

?>

<div class="wrap">
	<h2><?= __('New message', 'smokesignal') ?></h2>

	<form method="post" action="">		
		<table class="form-table"><tbody>
				<tr>
					<th scope="row"><label for="to_id"><?= __('Send to', 'smokesignal') ?></label></th>
					<td>
                        <?php if($selectUser) { ?>
                            <select name="to_id" id="to_id">
                                <?php foreach($args['users'] as $user) { ?>
                                    <option value="<?= $user->id ?>"><?= $user->user_nicename ?></option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <input name="to_name" id="to_name" />
                        <?php } ?>

					</td>
				</tr>
                <tr>
					<th scope="row"><label for="message"><?= __('Message', 'smokesignal') ?></label></th>
					<td>
                        <textarea name="message" id="message" style="width: 500px; height: 100px;"></textarea>
                        <?php if(current_user_can('manage_options')) { ?>
                        <input id="smokesignal_add_attachment" type="button" value="<?= __('Add attachment', 'smokesignal') ?>" class="button" />
                        <?php } ?>
                    </td>
				</tr>
			</tbody></table>
		<p class="submit">
			<input type="submit" class="button button-primary" name="submit_new_message" id="submit_new_message" value="<?= __('Send', 'smokesignal') ?>"/>
		</p>
	</form>
</div>