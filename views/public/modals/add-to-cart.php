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
        <select>
          <option value=""><?php echo __("Choose a cart"); ?></option>
        </select>
        <a class="add" href="#"><?php echo __('Add to cart') ?></a>
      </div>
      <div class="modal-footer">
        <a href="#modal-create-cart" data-toggle="modal" data-dismiss="modal"><?php echo __('Create new cart') ?></a>
      </div>
    </div>
  </div>
</div>


<style>
#modal-add-to-cart a.add {
  width:max-content;
  display: block;
  text-align:center;
  margin:0 auto;
}
</style>