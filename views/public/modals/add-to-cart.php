<div>
  <a data-toggle="modal" data-target="#modal-add-to-cart" href="#"><?php echo __('Add to cart'); ?></a>
</div>

<div class="modal fade" id="modal-add-to-cart">
    <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4><?php echo __('Add to existing cart') ?></h4>
        <a class="close" href="#" class="close" data-dismiss="modal">&times;</a>
      </div>
      <div class="modal-body">
          <form class="<?php echo count($carts) ? '' : 'hidden'; ?>" action="#" method="post" id="add-to-cart">
            <select name="cart_id" required data-msg="<?php echo __("You must choose a cart") ?>" >
              <option value=""><?php echo __("Choose a cart"); ?></option>
              <?php foreach($carts as $cart): ?>
                <option <?php if ($cart['contain_item']) echo "disabled" ?> value="<?php echo $cart['id']; ?>" url="<?php echo url(array('item-id' => $item_id, 'cart-id' => $cart['id']), 'pc_add_item_to_cart'); ?>"><?php echo $cart['name']; ?></option>
              <?php endforeach; ?>
            </select>
            <input type="submit" class="add" value="<?php echo __('Add to cart') ?>" />
          </form>
          <p class="no-cart <?php echo count($carts) ? 'hidden' : ''; ?>"><?php echo __('You don\'t have cart yet'); ?></p>
      </div>
      <div class="modal-footer">
        <a href="#modal-create-cart" data-toggle="modal" data-dismiss="modal"><?php echo __('Create new cart') ?></a>
      </div>
    </div>
  </div>
</div>


<script>
jQuery(document).ready(function($) {

    var modal   = $('#modal-add-to-cart'); // Current modal
    var form    = $("#add-to-cart"); // Current form
    var select  = form.find('select[name="cart_id"]');

    // Validate the form and call ajax proces
    form.validate({

        submitHandler: function() {
            var selectedOption = $('option:selected', select);
            var url = selectedOption.attr('url'); // Get Ajax URL from select option
            $.get({
                url: url,
                dataType: 'json',
                success: function(response) {
                    if (response.status === "ok" ) {
                        selectedOption.prop('disabled', true); // Disable this option if user reload the modal without reload the page
                        form.trigger('reset');
                        modal.modal('hide'); // Hide current modal
                    } else {
                        displayErrorsOnModals(form, response);
                    }
                },
                error: function() {
                  displayErrorsOnModals(form);
                }
            })
            return false;
        }
    });

    // Clear old errors messages
    select.change(function() {
        removeErrorsOnModals();
    });

    // Reset the form when modal is closed
    modal.on('hidden.bs.modal', function (e) {
        removeErrorsOnModals();
        form.trigger('reset');
    });
});
</script>


<style>
#modal-add-to-cart a.add {
  width:max-content;
  display: block;
  text-align:center;
  margin:0 auto;
}
</style>