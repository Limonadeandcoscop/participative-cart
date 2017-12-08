<?php
$title = __("Cart &quot;{$cart->name}&quot;");
echo head(array('title' => $title, 'bodyclass' => 'cart view'));
?>

<h1><?php echo $title ?></h1>
<h2><?php echo $count; ?> item<?php echo $s ?></h2>

<?php foreach($items as $item): ?>

<div class="item">
	<?php echo link_to_item(null, null, null, $item) ?>
	<a class="remove" data-toggle="modal" data-target="#modal-confirmation" href="<?php echo url(array('cart-id' => $cart->id, 'item-id' => $item->id), 'pc_delete_item_from_cart'); ?>"><?php echo __('Remove from cart') ?></a>
</div>

<?php endforeach; ?>

<a href="<?php echo url('cart') ?>"><?php echo __('Back to your carts') ?></a>


<?php echo foot(); ?>

<?php
	// Call confirmation modal
	$message = _('Are you sure you want to remove this item ?');
	echo $this->partial('modals/confirmation.php', array('message' => $message));
?>






