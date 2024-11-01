<?php
/**
 * @var $args array Array of arguments
 */
?>

<div class="wrap">
	<h2><?= __('New message to a group', 'smokesignal') ?></h2>

	<form method="post" action="">		
		<table class="form-table"><tbody>
				<tr>
					<th scope="row"><label for="group"><?= __('Send to all users in group', 'smokesignal') ?></label></th>
					<td>
						<select name="group_id" id="group_id">
							<?php foreach($args['groups'] as $group) { ?>
							<option value="<?= $group->id ?>"><?= $group->name ?></option>
							<?php } ?>
						</select>
					</td>
				</tr><tr>
					<th scope="row"><label for="message"><?= __('Message', 'smokesignal') ?></label></th>
					<td>
                        <textarea name="message" id="message" style="width: 500px; height: 100px;"></textarea>
                        <input id="smokesignal_add_attachment" type="button" value="<?= __('Add attachment', 'smokesignal') ?>" class="button" />
                    </td>
				</tr><tr>
			</tbody></table>
		<p class="submit">
			<input type="submit" class="button button-primary" name="submit_new_group_message" id="submit_new_group_message" value="<?= __('Send', 'smokesignal') ?>"/>
		</p>
	</form>
</div>