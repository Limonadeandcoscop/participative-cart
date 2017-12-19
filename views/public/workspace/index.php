<?php
$title = __('Workspace');
echo head(array('title' => $title, 'bodyclass' => 'workspace'));
?>

<h1><?php echo $title ?></h1>

<?php if (count($carts)): ?>
<div class="left area" id="refinements" style="width:30%; background:#eee;padding:10px;">
	<h2><?php echo __("Filter by") ?></h2>

	<?php if ($refine): ?>
        <a id="delete-refinements" href="<?php echo $this->original_uri ?>"><i class="fa fa-close"></i>Effacer</a>
    <?php endif; ?>

    <?php if (@$refinements['users']): ?>
        <?php $users_selected = explode(get_option('tag_delimiter'), @$params['users']); ?>
        <div class="facet users">
            <h3><?php echo __('Users') ?></h3>
            <ul>
            <?php foreach ($refinements['users'] as $user_id => $user): ?>
            	<li>
                	<input <?php if (in_array($user_id, $users_selected)): ?> checked="checked" <?php endif; ?> type="checkbox" class="users facet" value="<?php echo $user_id ?>" />
                	<label><b><?php echo $user['name'] ?><i>(<?php echo $user['nb'] ?>)</i></b></label>
				</li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (@$refinements['tags']): ?>
        <?php $tags_selected = explode(get_option('tag_delimiter'), @$params['tags']); ?>
        <div class="facet tags">
            <h3><?php echo __('Tags') ?></h3>
            <ul>
            <?php foreach ($refinements['tags'] as $tag_id => $tag): ?>
            	<li>
                	<input <?php if (in_array($tag_id, $tags_selected)): ?> checked="checked" <?php endif; ?> type="checkbox" class="tags facet" value="<?php echo $tag_id ?>" />
                	<label><b><?php echo $tag['name'] ?><i>(<?php echo $tag['nb'] ?>)</i></b></label>
				</li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<div class="left area" style="float:left">

	<div id="total-results">
		<h2><?php echo $total_results; ?> <?php echo __('Cart'); ?><?php echo $total_results>1?'s':''; ?></h2>
	</div>

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
			<div class="tags"><?php echo $cart->displayTags(); ?></div>
			<div class="author"><strong><?php echo __('Author') ?> : </strong><a class="author" value="<?php echo $cart->user_id ?>" href="#"><?php echo $cart->getUser()->name; ?></a></div>
			<div class="see"><?php echo link_to_item(__('Send request'), array('class' => 'button disable'), null, $cart) ?></div>
		</div>
	<?php endforeach; ?>
</div>
<?php else: ?>
    <p class="no-carts"><?php echo __("You don't have any cart in your workspace"); ?></p>
<?php endif; ?>

<?php echo pagination_links(); ?>

<?php echo foot(); ?>


<script>
jQuery(document).ready(function($) {

    // Manage clicks on facets checkboxes
    $('input[type=checkbox].facet').click(function() {
        var key = $(this).attr('class').split(' ')[0];
        var value = $(this).attr('value');
        url = updateQueryStringParameter(key, value);
        document.location.href = url;
    });

    // Manage clicks on facets labels
    $('#refinements .facet label').click(function() {
      var checkbox = $(this).prev('input[type=checkbox]');
      checkbox.click();
    });


    // Manage click on author in page content
    /*
    $('a.author').click(function(e) {
    	e.preventDefault();
    	var key = 'users';
    	var value = $(this).attr('value');
		url = updateQueryStringParameter(key, value);
		document.location.href = url;
    });
    */
});
</script>

