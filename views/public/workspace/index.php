<?php
$title = __('Workspace');
echo head(array('title' => $title, 'bodyclass' => 'workspace'));
?>

<h1><?php echo $title ?></h1>

<?php
$sortLinks[__('Author')] = 'users.name';
$sortLinks[__('Date Added')] = 'inserted';
?>
<div id="sort-links">
    <span class="sort-label"><?php echo __('Sort by: '); ?></span><?php echo browse_sort_links($sortLinks); ?>
</div>


<?php foreach ($carts as $c): ?>
	<?php $cart = get_record_by_id('ParticipativeCart', $c->id) ?>
	<div class="item">
		<div class="title"><?php echo $cart->name; ?></div>
		<?php echo link_to_item(__('See cart'), null, null, $cart) ?>
	</div>
<?php endforeach; ?>

<?php echo pagination_links(); ?>

<?php echo foot(); ?>
