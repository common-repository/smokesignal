
<div class="wrap">
	<h2><?= __('Communication with', 'smokesignal') ?> <?= $args['user']->user_nicename ?></h2>

	<form method="post">
		<input type="hidden" name="to_id" id="to_id" value="<?= $args['user']->id ?>" />
		<div>
            <textarea name="message" id="message" style="width: 500px; height: 100px;"></textarea>
        </div>
        <div>
            <?php if(current_user_can('manage_options')) { ?>
            <input id="smokesignal_add_attachment" type="button" value="<?= __('Add attachment', 'smokesignal') ?>" class="button" />
            <?php } ?>
            <input type="submit" class="button button-primary"
                   name="submit_new_message" id="submit_new_message"
                   value="<?= __('Send', 'smokesignal') ?>"/>
        </div>
	</form>
</div>

<div class="wrap">
	<table class="widefat">
		<tbody id="messages">

		</tbody>
	</table>
</div>

<div class="spinner" id="loading" style="float: left;"></div>
<div class="wrap" style="text-align:center;">
<a href="#" onclick="loadMore(); return false;" class="button button-primary button-hero" id="load-more-button">
    <?= __('Load more', 'smokesignal') ?>
</a>
    <span style="display: none" id="no-more-messages-caption"><?= __('No more messages', 'smokesignal') ?></span>
</div>

