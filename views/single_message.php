

			<?php foreach ($args['messages'] as $message) { ?>
				<?php if ($message->from_id == $args['user']->id) { ?>
					<tr id="message_<?=$message->id?>" class="message">
						<td style="width: 200px;">
                            <input type="hidden" class="last-id" value="<?=$message->id?>"/>
							<strong><?= $args['user']->user_nicename ?></strong><br>
							<small>(<?= $message->created ?>)</small>
                            <a href="#" onclick="removeMessage(<?= $message->id ?>); return false;" class="removeMessage">
                                <span style="color:red">
                                    <small>Remove</small>
                                </span>
                            </a>
						</td>
						<td>
                            <?php if(!empty($message->group_name)) { ?>
                                <span class="description">
                                    <?= __('Message to group ', 'smokesignal') ?>
                                    <?= $message->group_name ?>:<br/>
                                </span>
                            <?php } ?>
							<?= SmokeSignal::create_links($message->message) ?>
						</td>
						<td></td>
					</tr>
				<?php } else { ?>
					<tr class="alternate message" id="message_<?=$message->id?>">
						<td>
                            <input type="hidden" class="last-id" value="<?=$message->id?>"/>
                        </td>
						<td style="text-align:right;">
                            <?php if(!empty($message->group_name)) { ?>
                                <span class="grey"><?= $message->group_name ?></span>
                            <?php } ?>
							<?= SmokeSignal::create_links($message->message) ?>
						</td>
						<td style="width:200px;">
							<strong><?= __('me', 'smokesignal') ?></strong><br>
							<small>(<?= $message->created ?>)</small>
                            <a href="#" onclick="removeMessage(<?= $message->id ?>); return false;" class="removeMessage">
                                <span style="color:red">
                                    <small>Remove</small>
                                </span>
                            </a>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>


            <?php
            // Append element no_more_messages if there is nothing more to select
            if(count($args['messages']) < 5) { ?>
                <tr id="no-more-messages">
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

            <?php } ?>
