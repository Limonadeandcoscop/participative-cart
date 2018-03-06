<?php
$title = __('Contributors');
echo head(array('title' => $title, 'bodyclass' => 'carts contributors'));
?>

<div class="title area">
	<h1><?php echo $title ?></h1>
</div>

<div class="area">

<?php foreach ($contributions as $user_id => $contribution): ?>
	<div class="contributor contributor_<?php echo $user_id ?>">
		<?php $user = get_record_by_id("User", $user_id); ?>
		<h4><?php echo $user->name; ?></h4>

		<div class="profession"><?php echo GuestUserPlugin::getUserProfession($user); ?> <?php echo GuestUserPlugin::getUserInstitution($user); ?></div>

		<?php foreach ($contribution as $contrib): ?>	
			<?php $cartItem = get_record_by_id("ParticipativeCartItem", $contrib); ?>	
			<?php $item = get_record_by_id("Item", $cartItem->item_id); ?>	
			<div class="title"><?php echo metadata($item, array("Dublin Core", "Title")) ?></div>
			<div class="description"><?php echo metadata($item, array("Dublin Core", "Description")) ?></div>
			<a href="<?php echo url(array('cart-id' => $cartItem->cart_id), 'pc_view_cart'); ?>"><?php echo __('See cart'); ?><a>
		<?php endforeach; ?>				

	</div>
<?php endforeach; ?>	
</div>

<?php echo foot(); ?>

