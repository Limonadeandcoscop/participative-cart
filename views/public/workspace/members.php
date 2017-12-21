<?php
$title = __('Members');
echo head(array('title' => $title, 'bodyclass' => 'members'));
?>

<h1><?php echo $title ?></h1>

<h3><?php echo __('Waiting') ?></h3>

<?php if (count($waitingRequests)): ?>

    <?php foreach ($waitingRequests as $request): ?>
        <div class="request">
            <div class="user">
                <div class="name"><?php echo $request->getUser()->name; ?></div>
                <a href="#"><?php echo __('View profile') ?></a>
                <div class="infos">
                    <span><?php echo __('Profession').' : ' ?><?php echo __('TODO'); ?></span><br />
                    <span><?php echo __('Institution/Society').' : ' ?><?php echo __('TODO'); ?></span>
                </div>
                <div class="rights">
                    <select>
                        <option value=""><?php echo __('Choose') ?></option>
                        <option value="">Right 1</option>
                        <option value="">Right 2</option>
                        <option value="">Right 3</option>
                    </select>
                </div>
                <div class="save"><a href=""><?php echo __('Save') ?></a></div>
                <div class="delete"><a href="<?php echo url(array('request-id' => $request->id), 'pc_delete_request') ?>"><?php echo __('Decline') ?></a></div>
            </div>
        </div>
    <?php endforeach; ?>

<?php else: ?>
    <p><?php echo __("You don't have any request for this cart") ?></p>
<?php endif; ?>

<?php echo foot(); ?>

<script>
jQuery(document).ready(function($) {
});
</script>
