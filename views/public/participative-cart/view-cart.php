<?php
$title = __("Cart &quot;{$cart->name}&quot;");
echo head(array('title' => $title, 'bodyclass' => 'cart view'));
?>

<h1><?php echo $title ?></h1>

<?php foreach($items as $item): ?>

<div class="item">
	<?php echo link_to_item(null, null, null, $item) ?>
</div>

<?php endforeach; ?>

<a href="<?php echo url('cart') ?>"><?php echo __('Back to your carts') ?></a>


<?php echo foot(); ?>
