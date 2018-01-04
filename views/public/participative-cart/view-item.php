<?php
$title = '"'.$item->getProperty('display_title').'"'.__(" in cart ") . $cart->quote('name');
echo head(array('title' => $title, 'bodyclass' => 'cart item'));
?>


<div class="title area">
	<h1>
		<?php echo $title ?>
		<?php if ($count>0): ?>
			- <?php echo $count; ?> item<?php echo $s ?>
		<?php endif; ?>
		<div class="owner"><?php echo __('Owner') ?> : <?php echo $cart->getUser()->name ?></div>
	</h1>
	<div class="buttons">
		<a class="back button" href="<?php echo url(array('cart-id' => $cart->id), 'pc_view_cart'); ?>"><?php echo __('Back to cart') ?></a>
	</div>
</div>


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

	<?php if (current_user()->id == $cart->user_id || $request->userCanViewNotes()): ?>
		<div class="notes">
			<h2><?php echo __('Notes') ?></h2>

			<?php if (count($notes)): ?>
			<?php foreach ($notes as $note): ?>
				<div class="note">
					<strong><?php echo $note->note; ?></strong>
					<span class="info">(<?php echo $note->getUser()->name ?> - <?php echo get_date($note->inserted) ?>)</span>
					<?php if(current_user()->id == $cart->user_id || $request->userCanAddCommentsToCart()): ?>
						<a class="reply-link" href="#"><?php echo __('Reply to note') ?></a>
					<?php endif; ?>
					<?php if(current_user()->id == $cart->user_id || current_user()->id == $note->user_id || (isset($request) && $request->userCanDeleteItemOrNote())): ?>
						<a class="delete-note-link"  data-toggle="modal" data-target="#modal-confirmation" href="<?php echo url(array('note-id' => $note->id), 'pc_delete_note'); ?>"><?php echo __('Delete note') ?></a>
					<?php endif; ?>
					<form action="#" method="post">
						<input type="hidden" name="note-id" value="<?php echo $note->id ?>" />
						<textarea rows="3" name="comment"></textarea>
						<input class="save-reply" type="submit" value="<?php echo __('Save the reply') ?>" />
						<input class="cancel-reply" type="button" value="<?php echo __('Cancel') ?>" />
					</form>

					<?php if (current_user()->id == $cart->user_id || $request->userCanViewComments()): ?>
						<?php if (count($note->comments)): ?>
							<div class="comments">
							<?php foreach ($note->comments as $comment): ?>
								<div class="comment" style="margin-left:<?php echo (50*$comment->level) ?>px;">
									<strong><?php echo $comment->comment; ?></strong>
									<span class="info">(<?php echo $comment->getUser()->name ?> - <?php echo get_date($comment->inserted) ?>)</span>
									<?php if(current_user()->id == $cart->user_id || $request->userCanAddCommentsToCart()): ?>
										<a class="reply-link" href="#"><?php echo __('Reply') ?></a>
									<?php endif; ?>
									<?php if(current_user()->id == $comment->user_id || (isset($request) && $request->userCanDeleteComment())): ?>
										<a class="delete-comment-link"  data-toggle="modal" data-target="#modal-confirmation" href="<?php echo url(array('comment-id' => $comment->id), 'pc_delete_comment'); ?>"><?php echo __('Delete') ?></a>
									<?php endif; ?>
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
	<?php endif; ?>

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

<?php
    // Call confirmation modal
    echo $this->partial('participative-cart/modal-confirmation.php', array('message' => ''));
?>


<script>
jQuery(document).ready(function($) {

	$('.cart.item .reply-link').click(function() {
		var form  		= $(this).nextAll('form');
		var cancel 		= form.find('.cancel-reply');
		var textarea 	= form.find('textarea');
		cancel.click(function() {form.hide()});
		textarea.val('');
		form.toggle();
		return false;
	});
});
</script>



