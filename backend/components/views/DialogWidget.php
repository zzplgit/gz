<div class="modal modal-warning fade" id="<?= $id ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">警告</h4>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal"><?= $leftBtn ?></button>
        <button type="button" class="btn btn-outline dialogWidget-submit"><?= $rightBtn ?></button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
var dialogWidget = {
	setBody: function(body){
		var bodyDom = document.querySelector(".modal-body");
		bodyDom.innerHTML = "<p>"+body+"</p>";
    },
    show: function(func){
    	$('#<?= $id ?>').modal('show');
		document.querySelector(".dialogWidget-submit").addEventListener("click", func);
    },
    hide: function(func){
    	$('#<?= $id ?>').modal('hide');
    }
		
};
</script>
