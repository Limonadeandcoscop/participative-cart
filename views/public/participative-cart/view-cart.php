<?php
$title = __("Cart ") . $cart->quote('name');
echo head(array('title' => $title, 'bodyclass' => 'cart view'));
?>

<div class="title area">
	<h1><?php echo $title ?></h1>
	<div class="buttons">
		<a class="back button" href="<?php echo url('cart') ?>"><?php echo __('Back to your carts') ?></a>
		<a class="delete button" data-toggle="modal" data-target="#modal-confirmation" href="<?php echo url(array('cart-id' => $cart->id), 'pc_delete_cart'); ?>"><?php echo __('Delete cart') ?></a>
		<a class="edit button" href="<?php echo url(array('cart-id' => $cart->id), 'pc_edit_cart'); ?>"><?php echo __('Edit cart') ?></a>
	</div>
</div>

<?php if ($count>0): ?>
	<h2><?php echo $count; ?> item<?php echo $s ?></h2>
<?php else: ?>
	<?php echo __('There are no items in this cart'); ?>
<?php endif; ?>

<?php if ($description = $cart->description): ?>
	<div class="group description">
		<?php echo $description ?>
	</div>
<?php endif; ?>

<div class="left area">

	<div class="pagination-links">
		<?php echo pagination_links(); ?>
	</div>

	<?php foreach ($items_in_cart as $item_in_cart): ?>
		<?php $item = get_record_by_id('Item', $item_in_cart->item_id) ?>
		<div class="item">
			<?php echo link_to_item(null, null, null, $item) ?>
			<a class="remove" data-toggle="modal" data-target="#modal-confirmation" href="<?php echo url(array('cart-id' => $cart->id, 'item-id' => $item->id), 'pc_delete_item_from_cart'); ?>"><?php echo __('Remove from cart') ?></a>
		</div>
	<?php endforeach; ?>
</div>


<div class="right area">
	<div class="group status">
		<strong><?php echo __('Status') ?></strong><p><?php echo $cart->status; ?></p>
	</div>

	<?php if(count($cart->notes)): ?>
	<div class="group notes">
		<strong><?php echo __('Notes') ?></strong>
		<p>
		<?php foreach ($cart->notes as $note): ?>
			<?php echo $note->note; ?><br />
		<?php endforeach; ?>
		</p>
	</div>
	<?php endif; ?>

	<?php if(count($cart->tags)): ?>
	<div class="group tags">
		<strong><?php echo __('Tags') ?></strong>
		<p>
		<?php foreach ($cart->tags as $tag): ?>
			<?php echo $tag->name; ?><br />
		<?php endforeach; ?>
		</p>
	</div>
	<?php endif; ?>

	<?php $inserted = date( 'd/m/y G:i', strtotime($cart->inserted)) ?>
	<div class="group inserted">
		<strong><?php echo __('Inserted') ?></strong><p><?php echo $inserted; ?></p>
	</div>

</div>

<?php echo pagination_links(); ?>

<?php echo foot(); ?>

<?php
	// Call confirmation modal
	echo $this->partial('participative-cart/modal-confirmation.php', array('message' => ''));
?>






