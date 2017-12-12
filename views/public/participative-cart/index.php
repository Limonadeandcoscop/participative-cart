<?php
$title = __('Your carts');
echo head(array('title' => $title, 'bodyclass' => 'carts view'));
?>

<div class="title area">
	<h1><?php echo $title ?></h1>
	<div class="buttons">
		<a class="create button" data-toggle="modal" data-target="#modal-create-cart" href="#"><?php echo __('Create cart'); ?></a>
	</div>
</div>


<?php if (count($userCartsPrivate)): ?>
	<h3><?php echo __('Private carts') ?></h3>
	<?php foreach($userCartsPrivate as $cart): ?>
	<div class="cart">
		<?php $c = count($cart->items) ?>
		<a href="<?php echo url('cart/'.$cart->id) ?>"><?php echo $cart->name; ?></a>
		<a class="view" href="<?php echo url(array('cart-id' => $cart->id), 'pc_view_cart'); ?>"><?php echo __('View cart') ?> (<?php echo $c ?> item<?php echo ($c>1)?'s':'' ?>)</a>
	</div>
	<?php endforeach; ?>
<?php endif; ?>

<?php if (count($userCartsPublic)): ?>
	<h3><?php echo __('Public carts') ?></h3>
	<?php foreach($userCartsPublic as $cart): ?>
	<div class="cart">
		<?php $c = count($cart->items) ?>
		<a href="<?php echo url('cart/'.$cart->id) ?>"><?php echo $cart->name; ?></a>
		<a class="view" href="<?php echo url(array('cart-id' => $cart->id), 'pc_view_cart'); ?>"><?php echo __('View cart') ?> (<?php echo $c ?> item<?php echo ($c>1)?'s':'' ?>)</a>
	</div>
	<?php endforeach; ?>
<?php endif; ?>

<?php if (!count($userCartsPrivate) && !count($userCartsPublic)): ?>
	<p><?php echo __("You don't have a cart yet") ?></p>
<?php endif; ?>

<?php echo $this->partial('modals/create-cart.php', array('tags' => $tags)); ?>
<?php echo $this->partial('modals/create-cart-confirmation.php', array('redirect' => url("cart"), 'redirect_text' => __('Close'))); ?>

<?php echo foot(); ?>

<?php
	// Call confirmation modal
	$message = _('Are you sure you want to delete the cart and all its items ?');
	echo $this->partial('modals/confirmation.php', array('message' => $message));
?>

