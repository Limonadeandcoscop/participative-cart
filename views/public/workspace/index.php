<?php
$title = __('Workspace');
echo head(array('title' => $title, 'bodyclass' => 'workspace'));
?>

<h1><?php echo $title ?></h1>

<p class="intro">Apud has gentes, quarum exordiens initium ab Assyriis ad Nili cataractas porrigitur et confinia Blemmyarum, omnes pari sorte sunt bellatores seminudi coloratis sagulis pube tenus amicti, equorum adiumento pernicium graciliumque camelorum per diversa se raptantes, in tranquillis vel turbidis rebus: nec eorum quisquam aliquando stivam adprehendit vel arborem colit aut arva subigendo quaeritat victum, sed errant semper per spatia longe lateque distenta sine lare sine sedibus fixis aut legibus: nec idem perferunt diutius caelum aut tractus unius soli illis umquam placet.</p>

<div class="search" style="text-align: right; padding:10px; width:100%">
    <input type="text" value="" name="search" placeholder="<?php echo __('Search in all database') ?>" />
    <input class="button disable" type="submit" value="<?php echo __('Search') ?>" />
</div>

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
                    <a href="#" class="users facet" rel="<?php echo $user_id ?>"><b><?php echo $user['name'] ?><i>(<?php echo $user['nb'] ?>)</i></b></a>
                    <!--
                	<input <?php if (in_array($user_id, $users_selected)): ?> checked="checked" <?php endif; ?> type="checkbox" class="users facet" value="<?php echo $user_id ?>" />
                	<label><b><?php echo $user['name'] ?><i>(<?php echo $user['nb'] ?>)</i></b></label>
                    -->
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
			<div class="title"><strong><?php echo $cart->name; ?></strong></div>
            <div class="description"><?php echo cut_string($cart->description); ?></div>
			<div class="author"><strong><?php echo __('Author') ?> : </strong><a class="author" value="<?php echo $cart->user_id ?>" href="#"><?php echo $cart->getUser()->name; ?></a></div>
			<div class="request">
                <?php if ($user = current_user()): // User is logged in ?>
                    <?php if ($user->id != $cart->user_id): // If the user isn't the cart owner ?>
                        <?php if ($request = $cart->haveRequestFromUser()): ?>
                            <a href="#" class="button waiting"><?php echo __('Request sent') ?></a>
                        <?php else: ?>
                            <a href="<?php echo url(array('cart-id' => $cart['id']), 'pc_send_request') ?>" class="button send-request"><?php echo __('Send request') ?></a>
                        <?php endif; ?>
                   <?php endif; ?>
                <?php else: // The user is not logged in ?>
                    <p class="notice" style="font-style:italic;float:right"><?php echo __('You must be logged in for send a request') ?><a href="<?php echo url('guest-user/user/login') ?>"> <?php echo __('Login'); ?></a></p>
                <?php endif; ?>
            </div>
            <div class="nb-items"><strong><?php echo __('Notes') ?></strong> : 0</div>
            <div class="nb-notes"><strong><?php echo __('Items') ?></strong> : <?php echo count($cart->getItems(false)); ?></div>
		</div>
	<?php endforeach; ?>
    <?php echo pagination_links(); ?>
</div>
<?php else: ?>
    <p class="no-carts"><?php echo __("You don't have any cart in your workspace"); ?></p>
<?php endif; ?>


<?php echo foot(); ?>


<script>
jQuery(document).ready(function($) {

    // Manage clicks on facets links
    $('a.users.facet').click(function() {
        var key = $(this).attr('class').split(' ')[0];
        var value = $(this).attr('rel');
        url = updateQueryStringParameter(key, value);
        document.location.href = url;
    });

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

    // Manage clicks on "send request" buttons
    $('a.send-request').on('click', function(e) {
        var confirmationModal   = $('#modal-confirmation');
        var url = $(this).attr('href');
        var requestArea = $(this).parents('div.request');

        $.get({
            url: url,
            dataType: 'json',
            success: function(response) {
                if (response.status === "ok" ) {
                    requestArea.html('<a href="#" class="button waiting"><?php echo __('Request sent') ?></a>');
                    confirmationModal.modal('show');
                }
            },
        });

        return false;
    });

});
</script>



