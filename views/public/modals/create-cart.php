<div class="modal fade" id="modal-create-cart">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4><?php echo __('Create new cart') ?></h4>
        <a class="close" href="#" class="close" data-dismiss="modal">&times;</a>
      </div>
      <div class="modal-body">
        <input class="name" type="text" name="name" placeholder="<?php echo __("Enter the name of your cart") ?>">
        <a class="create" href="<?php echo url('/participative_cart'); ?>"><?php echo __('Create'); ?></a>
      </div>
      <div class="modal-footer">
        <a href="#modal-add-to-cart" data-toggle="modal" data-dismiss="modal"><?php echo __('Back') ?></a>
      </div>
    </div>
  </div>
</div>


<script>
jQuery(document).ready(function($) {

  $('#modal-create-cart .create').click(function() {

    url = $(this).attr('href');
/*
    $.ajax({
        //beforeSend: function(event) { $(element).addClass('loading'); },
        //complete: function(event) { $(element).removeClass('loading'); },
        data: {format: 'json'},
        success: function(data) { onSuccess(data, element); },
        error: function(event) { displayError('JSON error'); },
        type: 'GET',
        url: $(element).attr('href')
    })
*/
    return false;
  });

});
</script>


<style>
#modal-create-cart input {
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