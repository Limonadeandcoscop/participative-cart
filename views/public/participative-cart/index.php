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

<div class="left area">

	<?php if (count($userCartsPrivate)): ?>
		<h3><?php echo __('Private carts') ?></h3>
		<?php foreach($userCartsPrivate as $cart): ?>
		<div class="cart">
			<?php $c = count($cart->items) ?>
			<strong><?php echo $cart->name; ?></strong>
			<a class="view" href="<?php echo url(array('cart-id' => $cart->id), 'pc_view_cart'); ?>"><?php echo __('View cart') ?> (<?php echo $c ?> item<?php echo ($c>1)?'s':'' ?>)</a>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if (count($userCartsPublic)): ?>
		<h3><?php echo __('Public carts') ?></h3>
		<?php foreach($userCartsPublic as $cart): ?>
		<div class="cart">
			<?php $c = count($cart->items) ?>
			<strong><?php echo $cart->name; ?></strong>
			<div class="nb_requests">
				<?php echo __('Waiting requests') ?> : <?php echo $cart->nb_waiting_requests ?> | <?php echo __('Members') ?> : <?php echo $cart->nb_accepted_requests ?>
			</div>
			<a class="view" href="<?php echo url(array('cart-id' => $cart->id), 'pc_view_cart'); ?>"><?php echo __('View cart') ?> (<?php echo $c ?> item<?php echo ($c>1)?'s':'' ?>)</a>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>


	<?php if (count($sharedCarts)): ?>
		<h3><?php echo __('Shared carts') ?></h3>
		<?php foreach($sharedCarts as $cart): ?>
		<div class="cart">
			<?php $c = count($cart->items) ?>
			<strong><?php echo $cart->name; ?></strong>
			<div class="owner"><?php echo __('Owner') ?> : <?php echo $cart->getUser()->name; ?></div>
			<a class="view" href="<?php echo url(array('cart-id' => $cart->id), 'pc_view_cart'); ?>"><?php echo __('View cart') ?> (<?php echo $c ?> item<?php echo ($c>1)?'s':'' ?>)</a>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if (!count($userCartsPrivate) && !count($userCartsPublic)  && !count($sharedCarts)): ?>
		<p><?php echo __("You don't have a cart yet") ?></p>
	<?php endif; ?>

</div>

<div class="right area">
	<input style="margin-top:20px;float:left; width:180px;" type="text" value="" name="search" placeholder="<?php echo __('Search in all database') ?>" />
	<input class="button disable" style="margin-top:20px;float:left;" type="submit" value="<?php echo __('Search') ?>" />
</div>


<?php echo $this->partial('participative-cart/modal-create-cart.php', array('tags' => $tags)); ?>
<?php echo $this->partial('participative-cart/modal-create-cart-confirmation.php', array('redirect' => url("cart"), 'redirect_text' => __('Close'))); ?>

<?php echo foot(); ?>

<?php
	// Call confirmation modal
	echo $this->partial('participative-cart/modal-confirmation.php');
?>

