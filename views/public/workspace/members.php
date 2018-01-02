<?php
$title = __('Members of '.$cart->quote('name'));
echo head(array('title' => $title, 'bodyclass' => 'members'));
?>

<h1><?php echo $title ?></h1>

<h3><?php echo __('Waiting') ?></h3>

<?php if (count($waitingRequests)): ?>

    <?php foreach ($waitingRequests as $request): ?>
        <?php $user = $request->getUser(); ?>
        <div class="request waiting">
            <div class="user">
                <div class="name"><?php echo $user->name; ?></div>
                <a href="#"><?php echo __('View profile') ?></a>
                <?php $profession = $user->profession; ?>
                <?php $institution = $user->institution; ?>
                <?php if ($profession || $institution): ?>
                    <div class="infos">
                        <?php if (strlen(trim($profession))): ?>
                            <span><?php echo __('Profession').' : ' ?><?php echo $profession ?></span><br />
                        <?php endif; ?>
                        <?php if (strlen(trim($institution))): ?>
                            <span><?php echo __('Institution/Society').' : ' ?><?php echo $institution; ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="rights">
                    <form action="#" method="post">
                        <input type="hidden" name="request_id" value="<?php echo $request->id ?>" />
                        <select name="rights">
                            <option value=""><?php echo __('Choose') ?></option>
                            <option value="<?php echo ParticipativeCartRequest::VIEW_ITEMS ?>">View items</option>
                            <option value="<?php echo ParticipativeCartRequest::VIEW_ITEMS_NOTES ?>">View items and notes</option>
                            <option value="<?php echo ParticipativeCartRequest::VIEW_ITEMS_NOTES_COMMENTS ?>">View items, notes and comments</option>
                            <option value="<?php echo ParticipativeCartRequest::ADD_ITEMS_NOTES ?>">Add items and notes</option>
                            <option value="<?php echo ParticipativeCartRequest::ADD_ITEMS_NOTES_COMMENTS ?>">Add items, notes and comments</option>
                            <option value="<?php echo ParticipativeCartRequest::DELETE_ITEMS_NOTES ?>">Delete items and notes</option>
                            <option value="<?php echo ParticipativeCartRequest::DELETE_ITEMS_NOTES_COMMENTS ?>">Delete items, notes and comments</option>
                        </select>
                        <input type="submit" value="<?php echo __('Save') ?>">
                        <input type="submit" value="<?php echo __('Delete') ?>" name="delete">
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p><?php echo __("You don't have any waiting request for this cart") ?></p>
<?php endif; ?>


<h3><?php echo __('Accepted') ?></h3>

<?php if (count($acceptedRequests)): ?>

    <?php foreach ($acceptedRequests as $request): ?>
        <?php $user = $request->getUser(); ?>
        <?php $selected[$request->rights] = 'selected'; ?>
        <div class="request accepted">
            <div class="user">
                <div class="name"><?php echo $user->name; ?></div>
                <a href="#"><?php echo __('View profile') ?></a>
                <?php $profession = $user->profession; ?>
                <?php $institution = $user->institution; ?>
                <?php if ($profession || $institution): ?>
                    <div class="infos">
                        <?php if (strlen(trim($profession))): ?>
                            <span><?php echo __('Profession').' : ' ?><?php echo $profession ?></span><br />
                        <?php endif; ?>
                        <?php if (strlen(trim($institution))): ?>
                            <span><?php echo __('Institution/Society').' : ' ?><?php echo $institution; ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="rights">
                    <form action="#" method="post">
                        <input type="hidden" name="request_id" value="<?php echo $request->id ?>" />
                        <select name="rights">
                            <option <?php echo @$selected[ParticipativeCartRequest::VIEW_ITEMS] ?> value="<?php echo ParticipativeCartRequest::VIEW_ITEMS ?>">View items</option>
                            <option <?php echo @$selected[ParticipativeCartRequest::VIEW_ITEMS_NOTES] ?> value="<?php echo ParticipativeCartRequest::VIEW_ITEMS_NOTES ?>">View items and notes</option>
                            <option <?php echo @$selected[ParticipativeCartRequest::VIEW_ITEMS_NOTES_COMMENTS] ?> value="<?php echo ParticipativeCartRequest::VIEW_ITEMS_NOTES_COMMENTS ?>">View items, notes and comments</option>
                            <option <?php echo @$selected[ParticipativeCartRequest::ADD_ITEMS_NOTES] ?> value="<?php echo ParticipativeCartRequest::ADD_ITEMS_NOTES ?>">Add items and notes</option>
                            <option <?php echo @$selected[ParticipativeCartRequest::ADD_ITEMS_NOTES_COMMENTS] ?> value="<?php echo ParticipativeCartRequest::ADD_ITEMS_NOTES_COMMENTS ?>">Add items, notes and comments</option>
                            <option <?php echo @$selected[ParticipativeCartRequest::DELETE_ITEMS_NOTES] ?> value="<?php echo ParticipativeCartRequest::DELETE_ITEMS_NOTES ?>">Delete items and notes</option>
                            <option <?php echo @$selected[ParticipativeCartRequest::DELETE_ITEMS_NOTES_COMMENTS] ?> value="<?php echo ParticipativeCartRequest::DELETE_ITEMS_NOTES_COMMENTS ?>">Delete items, notes and comments</option>
                        </select>
                        <input type="submit" value="<?php echo __('Save') ?>">
                        <input type="submit" value="<?php echo __('Delete') ?>" name="delete">
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p><?php echo __("You don't have any accepted request for this cart") ?></p>
<?php endif; ?>


<?php echo foot(); ?>

<script>
jQuery(document).ready(function($) {
});
</script>
