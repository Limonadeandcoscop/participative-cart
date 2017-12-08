<?php
$title = __('Your carts');
echo head(array('title' => $title, 'bodyclass' => 'carts view'));
?>

<h1><?php echo $title ?></h1>

<?php if (count($userCarts)): ?>

	<?php foreach($userCarts as $cart): ?>
	<div class="cart">
		<?php $c = count($cart->items) ?>
		<a href="<?php echo url('cart/'.$cart->id) ?>"><?php echo $cart->name; ?> (<?php echo $c ?> item<?php echo ($c>1)?'s':'' ?>)</a>
		<a class="remove" data-toggle="modal" data-target="#modal-confirmation" href="<?php echo url(array('cart-id' => $cart->id), 'pc_delete_cart'); ?>"><?php echo __('Delete cart') ?></a>
	</div>
	<?php endforeach; ?>

<?php else: ?>

	<p><?php echo __("You don't have a cart yet") ?></p>

<?php endif; ?>

<?php echo foot(); ?>

<?php
	// Call confirmation modal
	$message = _('Are you sure you want to delete the cart and all its items ?');
	echo $this->partial('modals/confirmation.php', array('message' => $message));
?>

