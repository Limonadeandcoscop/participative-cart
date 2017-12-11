
<div class="modal" id="modal-create-cart-confirmation">
    <input type="hidden" name="cart_id" />
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4><?php echo __('Your cart has been created') ?></h4>
                <a class="close" href="#" class="close" data-dismiss="modal">&times;</a>
            </div>
            <div class="modal-body">
                <div class="name"></div>
            </div>
            <div class="modal-footer">
                <a href="#modal-add-to-cart" data-toggle="modal" data-dismiss="modal"><?php echo __('Back to cart selection') ?></a>
            </div>
        </div>
    </div>
</div>



<script>
jQuery(document).ready(function($) {

    var currentModal        = $('#modal-create-cart-confirmation');
    var addToCartModal      = $('#modal-add-to-cart');

    // Populate select of "add to cart" modal before close current modal
    currentModal.on('hidden.bs.modal', function (e) {

        var cart_id = currentModal.find('input[name="cart_id"]').val();
        var name    = currentModal.find('div.name').html();
        var form    = addToCartModal.find('form');
        var select  = addToCartModal.find('select[name="cart_id"]');

        form.show();
        addToCartModal.find('.no-cart').hide();

        var url = "<?php echo WEB_DIR ?>" + "/cart/" + cart_id + '/add/' + <?php echo $item_id ?>; // Build ajax uRL
        select.append('<option selected="selected" url="'+url+'" value="'+cart_id+'">'+name+'</option>');
    });

});
</script>
