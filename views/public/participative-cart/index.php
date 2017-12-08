<?php
$title = __('Your carts');
echo head(array('title' => $title, 'bodyclass' => 'carts view'));
?>

<h1><?php echo $title ?></h1>

<?php if (count($userCarts)): ?>

	<?php foreach($userCarts as $cart): ?>
	<div class="cart">
		<a href="<?php echo url('cart/'.$cart->id) ?>"><?php echo $cart->name; ?> (<?php echo count($cart->items) ?>)</a>
	</div>
	<?php endforeach; ?>

<?php else: ?>

	<p><?php echo __("You don't have a cart yet") ?></p>

<?php endif; ?>

<?php echo foot(); ?>
