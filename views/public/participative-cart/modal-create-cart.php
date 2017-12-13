
<div class="modal" id="modal-create-cart">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4><?php echo __('Create new cart') ?></h4>
        <a class="close" href="#" class="close" data-dismiss="modal">&times;</a>
      </div>
      <div class="modal-body">
        <form action="<?php echo url(array(), 'pc_add_cart'); ?>" method="post" id="create-form">
            <label for="name"><?php echo __('Name') ?></label>
            <input required data-msg="<?php echo __("Name of the cart your required") ?>" class="name" type="text" name="name" placeholder="<?php echo __("Enter the name of your cart") ?>">

            <?php if (isset($tags)): ?>
              <div class="tags">
                <link rel="stylesheet" type="text/css" href="https://semantic-ui.com/dist/semantic.min.css">
                <label for="tags"><?php echo __('Tags') ?></label>
                <div class="ui fluid search normal selection multiple dropdown">
                      <input type="hidden" name="tags">
                      <i class="dropdown icon"></i>
                      <div class="default text"><?php echo __('Search or add tags') ?></div>
                      <div class="menu">
                      <?php foreach($this->tags as $tag): ?>
                          <div class="item" data-value="<?php echo $tag->id; ?>"><?php echo $tag->name ?></div>
                      <?php endforeach; ?>
                      </div>
                  </div>
              </div>
            <?php endif; ?>

            <input class="create" type="submit" value="<?php echo __('Create'); ?>"/>
        </form>
      </div>
      <div class="modal-footer">
        <a href="#modal-add-to-cart" data-toggle="modal" data-dismiss="modal"><?php echo __('Back') ?></a>
      </div>
    </div>
  </div>
</div>


<script>
jQuery(document).ready(function($) {

    var currentModal        = $('#modal-create-cart');
    var confirmationModal   = $('#modal-create-cart-confirmation');
    var form                = $("#create-form");

    // Validate the form and call ajax proces
    form.validate({

        submitHandler: function(e) {
            var name = form.find('input[name="name"]').val(); // Retrieve choosen name in field
            var tags = form.find('input[name=tags]').val();
            $.get({
                url: form.attr('action'),
                data: {name: name, tags: tags},
                dataType: 'json',
                success: function(response) {
                    if (response.status === "ok" ) {
                      confirmationModal.find("div.name").html(name); // Put name in confirmation modal
                      confirmationModal.find('input[name="cart_id"]').val(response.cart_id); // Put cart_id in confirmation modal
                      confirmationModal.modal('show'); // Show confirmation modal
                      currentModal.modal('hide'); // Hide current modal
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

    // Reset the form when modal is closed
    currentModal.on('hidden.bs.modal', function (e) {
      jQuery('label.error').remove();
      jQuery('div.ajax-errors').remove();
      form.trigger('reset');
    });

    // Enable search dropdown
    $('.ui.dropdown').dropdown({'allowAdditions':true, 'keys':{delimiter: 13}});
});
</script>


<style>
#modal-create-cart input.name {
  display: block;
  margin:0 auto;
}

#modal-create-cart a.create {
  width:max-content;
  display: block;
  text-align:center;
  margin:0 auto;
}
</style>
