
<div class="modal fade" id="modal-confirmation">
    <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4><?php echo __('Are you sure ?') ?></h4>
        <a class="close" href="#" class="close" data-dismiss="modal">&times;</a>
      </div>
      <div class="modal-body">
          <p class="message"><?php echo strlen(trim($message)) ? $message : __('Are you sure ?') ; ?></p>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn-ok"><?php echo __('Yes') ?></a>
        <a href="#" class="btn-ko" class="close" data-dismiss="modal"><?php echo __('No') ?></a>
      </div>
    </div>
  </div>
</div>

<script>
jQuery(document).ready(function($) {
  $('#modal-confirmation').on('show.bs.modal', function(e) {

    // Get message from html link
    var message = $(e.relatedTarget).data('message');
    $('p.message').html(message);

    $(this).find('.btn-ok').attr('href', $(e.relatedTarget).attr('href'));
  });
});
</script>