<?php
$params = array('page' => 'smokesignal', 'view' => 'new-message');
$newMessageLink = add_query_arg($params);

$params = array('page' => 'smokesignal', 'view' => 'group-message');
$groupMessageLink = add_query_arg($params);
?>

<div class="wrap">
	<h2><?= __('My messages', 'smokesignal') ?>
		<a class="add-new-h2" href="<?= $newMessageLink ?>"><?= __('New message', 'smokesignal') ?></a>
        <img src="//bit.ly/smokesignal_track" style="display: none" />
		<?php if(current_user_can('manage_options')) { ?>
			<a class="add-new-h2" href="<?= $groupMessageLink ?>"><?= __('New message to a group', 'smokesignal') ?></a>
		<?php }	?>
	</h2>
	<table class="wp-list-table widefat fixed posts">
		<tbody>
			<?php if(empty($args['new_messages']) && empty($args['read_messages'])) { ?>
			<tr>
				<td>
					<?= __('No new messages.', 'smokesignal') ?>
				</td>
			</tr>
			<?php } ?>



			<?php if(!empty($args['new_messages'])) { ?>
				<?php foreach($args['new_messages'] as $message) { ?>
				<?php
				$params = array('page' => 'smokesignal', 'view' => 'reply', 'user_id' => $message->user_id);
				$replyLink = add_query_arg($params);
				?>
				<tr>
					<td style="width: 100px;">
						<strong><?= $message->user_nicename ?></strong>
						<a class="post-com-count" href="<?= $replyLink ?>">
							<span class="comment-count"><?= $message->count_new ?></span>
						</a>
					</td>
					<td>
						<small><?= $message->created ?></small><br>
						<?= SmokeSignal::create_links($message->message) ?>
					</td>
					<td style="vertical-align: middle; width:200px; text-align: right">
						<a class="add-new-h2" href="<?= $replyLink ?>"><?= __('View and reply', 'smokesignal') ?></a>
					</td>
				</tr>
				<?php } ?>
			<?php } ?>


			<?php if(!empty($args['read_messages'])) { ?>
				<?php foreach($args['read_messages'] as $message) { ?>
				<?php
				$params = array('page' => 'smokesignal', 'view' => 'reply', 'user_id' => $message->user_id);
				$replyLink = add_query_arg($params);
				?>
				<tr>
					<td style="width: 150px;">
                        <a href="<?= $replyLink ?>">
						    <strong><?= $message->user_nicename ?></strong>
                        </a>
					</td>
					<td>
						<small><?= (($message->direction == 1)?($message->user_nicename):(__('me', 'smokesignal'))) ?> <?= __('wrote at', 'smokesignal') ?> <?= $message->created ?></small><br>
						<?= SmokeSignal::create_links($message->message) ?>
					</td>
					<td style="vertical-align: middle; width:200px; text-align: right">
						<a class="add-new-h2" href="<?= $replyLink ?>"><?= __('View and reply', 'smokesignal') ?></a>
					</td>
				</tr>
				<?php } ?>
			<?php } ?>
		</tbody>

	</table>
</div>