<?php
$title = __('Edit cart ') . $cart->quote('name');
echo head(array('title' => $title, 'bodyclass' => 'cart edit'));
?>

<div class="title area">
	<h1><?php echo $title ?></h1>
	<div class="buttons">
		<a class="back button" href="<?php echo url(array('cart-id' => $cart->id), 'pc_view_cart'); ?>"><?php echo __('Back to the cart') ?></a>
		<a class="delete button" data-toggle="modal" data-target="#modal-confirmation" href="<?php echo url(array('cart-id' => $cart->id), 'pc_delete_cart'); ?>"><?php echo __('Delete cart') ?></a>
	</div>
</div>


<form action="#" method="post" id="update-form">

	<div class="group name">
		<label for="name"><?php echo __('Cart name') ?></label>
		<input required data-msg="<?php echo __("Name of the cart your required") ?>" name="name" value="<?php echo htmlspecialchars($cart->name, ENT_QUOTES) ?>" />
	</div>

	<div class="group descriptions">
		<label for="name"><?php echo __('Description') ?></label>
		<textarea name="description" cols="80" rows="4"><?php echo $cart->description ?></textarea>
	</div>

	<div class="group notes">
		<label for="name"><?php echo __('Notes') ?></label>
		<?php if (count($cart->getCartNotes())): ?>
			<?php foreach($cart->getCartNotes() as $note): ?>
				<div class="note">
					<textarea name="note[]" cols="80" rows="4"><?php echo $note->note; ?></textarea>
					<a href="#" class="remove-note"><?php echo __('Remove note') ?></a>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="note">
				<label for="name"><?php echo __('Note') ?></label>
				<textarea name="note[]" cols="80" rows="4"></textarea>
				<a href="#" class="remove-note"><?php echo __('Remove note') ?></a>
			</div>
		<?php endif; ?>
		<a id="add-cart" href="#"><?php echo __('Add note') ?></a>
	</div>

    <script src="https://semantic-ui.com/dist/semantic.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://semantic-ui.com/dist/semantic.min.css">
	<div class="group tags">
		<label for="tags"><?php echo __('Tags') ?></label>
		<div class="ui fluid search normal selection multiple dropdown">
	        <input type="hidden" name="tags">
	        <i class="dropdown icon"></i>
	        <div class="default text"><?php echo __('Search or add tags') ?></div>
	        <div class="menu">
	        <?php foreach($this->tags as $tag): ?>
	            <div class="item" data-value="<?php echo $tag->id; ?>"><?php echo $tag->name ?></div>
	        <?php endforeach; ?>
	        </div>
	    </div>
	</div>

	<div class="group status">
		<label for="status"><?php echo __('Status') ?></label>
		<?php echo $cart->status ?>
		<p><input type="checkbox" name="status" value="private" <?php if ($cart->status == ParticipativeCart::CART_STATUS_PRIVATE) echo 'checked' ?> /><?php echo __('Private') ?> (<?php echo __('private carts are not published to other users') ?>)</p>
	</div>

	<div class="group buttons">
		<input type="submit" value="<?php echo __("Save changes") ?>" />
	</div>

</form>

<?php echo foot(); ?>


<script>
jQuery(document).ready(function($) {


	var form = $("#update-form");

    // Validate the form and call ajax proces
    form.validate();

	// Create a new 'note' area
    $('#add-cart').click(function() {
    	var lastNote	= $('.notes > .note').last();
    	var cloneNote 	= lastNote.clone();
    	cloneNote.find('textarea').val('');
    	lastNote.after(cloneNote);
    	return false;
    });

	// Delete a 'note' area
	$(document).on("click", "a.remove-note", function(){
    	var currentNote	= $(this).parent('.note');
    	currentNote.remove();
    	return false;
    });

	// Enable search dropdown
	<?php foreach($cart->tags as $tag) @$selectedTags .= "'".$tag->id."',"; ?>
	$('.ui.dropdown').dropdown('set selected', [<?php echo @$selectedTags ?>]).dropdown({'allowAdditions': true, 'keys': {delimiter: 13}});

});
</script>

<?php
	// Call confirmation modal
	echo $this->partial('modals/confirmation.php', array('message' => ''));
?>