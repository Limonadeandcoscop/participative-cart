<?php
$title = '"'.$item->getProperty('display_title').'"'.__(" in cart ") . $cart->quote('name');
echo head(array('title' => $title, 'bodyclass' => 'cart item'));
?>

<?php if ($description = $cart->description): ?>
	<div class="group description">
		<?php echo $description ?>
	</div>
<?php endif; ?>


<div class="top area">
	<div class="search">
		<input type="text" value="" name="search" placeholder="<?php echo __('Search in all database') ?>" />
		<input class="button disable" type="submit" value="<?php echo __('Search') ?>" />
	</div>
</div><!--/top-area-->



<div class="left area">

	<h1><?php echo $title ?></h1>
	<?php echo $this->partial('participative-cart/view-item-content.php', array('item' => $item)); ?>

	<div class="notes">
		<h2><?php echo __('Notes') ?></h2>

		<?php if (count($notes)): ?>
		<?php foreach ($notes as $note): ?>
			<div class="note">
				<?php echo $note->note; ?>
			</div>
		<?php endforeach; ?>
		<?php endif; ?>

		<div class="add-note">
			<h3><?php echo __('Add note') ?></h3>
			<form action="#" method="post">
				<textarea rows="3" name="note"></textarea>
				<input id="save-note" type="submit" value="<?php echo __('Save') ?>" />
			</form>
		</div>
	</div>

</div><!--/left-area-->



<div class="right area">
	<div class="group download">
		<a class="button disable" href="#"><?php echo __('Download') ?></a>
	</div>
	<div class="group participate">
		<a class="button disable" href="#"><?php echo __('Participate') ?></a>
	</div>
	<div class="group print">
		<a class="button disable" href="#"><?php echo __('Print') ?></a>
	</div>
</div><!--/right-area-->

<?php echo foot(); ?>





