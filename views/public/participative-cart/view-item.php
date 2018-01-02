<?php
$title = '"'.$item->getProperty('display_title').'"'.__(" in cart ") . $cart->quote('name');
echo head(array('title' => $title, 'bodyclass' => 'cart item'));
?>

<?php if ($description = $cart->description): ?>
	<div class="group description">
		<?php echo $description ?>
	</div>
<?php endif; ?>


<div class="top area">
	<div class="search">
		<input type="text" value="" name="search" placeholder="<?php echo __('Search in all database') ?>" />
		<input class="button disable" type="submit" value="<?php echo __('Search') ?>" />
	</div>
</div><!--/top-area-->



<div class="left area">

	<h1><?php echo $title ?></h1>
	<?php echo $this->partial('participative-cart/view-item-content.php', array('item' => $item)); ?>

	<div class="notes">
		<h2><?php echo __('Notes') ?></h2>

		<?php if (count($notes)): ?>
		<?php foreach ($notes as $note): ?>
			<div class="note">
				<?php echo $note->note; ?> <span class="info">(<?php echo $note->getUser()->name ?>)</span>
				<a class="reply-link" href="#"><?php echo __('Reply to note') ?></a>
				<form action="#" method="post">
					<input type="hidden" name="note-id" value="<?php echo $note->id ?>" />
					<textarea rows="3" name="comment"></textarea>
					<input class="save-reply" type="submit" value="<?php echo __('Save the reply') ?>" />
					<input class="cancel-reply" type="button" value="<?php echo __('Cancel') ?>" />
				</form>
				<?php if (count($note->comments)): ?>
					<div class="comments">
					<?php foreach ($note->comments as $comment): ?>
						<div class="comment" style="margin-left:<?php echo (50*$comment->level) ?>px;">
							<?php echo $comment->comment; ?> <span class="info">(<?php echo $comment->getUser()->name ?> - <?php echo get_date($comment->inserted) ?>)</span>
							<a class="reply-link" href="#"><?php echo __('Reply') ?></a>
							<form action="#" method="post">
								<input type="hidden" name="note-id" value="<?php echo $note->id ?>" />
								<input type="hidden" name="comment-id" value="<?php echo $comment->id ?>" />
								<input type="hidden" name="level" value="<?php echo $comment->level ?>" />
								<textarea rows="3" name="comment"></textarea>
								<input class="save-reply" type="submit" value="<?php echo __('Save the reply') ?>" />
								<input class="cancel-reply" type="button" value="<?php echo __('Cancel') ?>" />
							</form>
						</div>
					<?php endforeach; ?>
					</div><!--/comment-->
				<?php endif; ?>
			</div><!--/note-->
		<?php endforeach; ?>
		<?php endif; ?>

		<div class="add-note">
			<h3><?php echo __('Add note') ?></h3>
			<form action="#" method="post">
				<textarea rows="3" name="note"></textarea>
				<input id="save-note" type="submit" value="<?php echo __('Save the note') ?>" name="save-note" />
			</form>
		</div>
	</div><!--/notes-->

</div><!--/left-area-->



<div class="right area">
	<div class="group download">
		<a class="button disable" href="#"><?php echo __('Download') ?></a>
	</div>
	<div class="group participate">
		<a class="button disable" href="#"><?php echo __('Participate') ?></a>
	</div>
	<div class="group print">
		<a class="button disable" href="#"><?php echo __('Print') ?></a>
	</div>
</div><!--/right-area-->

<?php echo foot(); ?>


<script>
jQuery(document).ready(function($) {

	$('.cart.item .reply-link').click(function() {

		var form  		= $(this).next('form');
		var cancel 		= form.find('.cancel-reply');
		var textarea 	= form.find('textarea');

		cancel.click(function() {form.hide()});
		textarea.val('');
		form.toggle();
		return false;

	});
});
</script>



