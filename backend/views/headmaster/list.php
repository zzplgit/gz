<?php

use backend\assets\AdminLtePluginAsset;
use backend\components\DialogWidget;

AdminLtePluginAsset::register($this);

$this->title = '园长设置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">

    <section class="content">
		<div class="row">
        	<div class="col-xs-12">
          		<div class="box">
          			<div class="box-header">
              			<h3 class="box-title">园长列表</h3>
              			<button id="addMaster" type="button" class="btn btn-default pull-right">
              				<i class="fa fa-plus"></i> 新增园长
              			</button>
            		</div>
          			<div class="box-body">
          				<table id="example" class="table table-bordered table-striped" style="width:100%">
          					<thead>
                                <tr>
                                    <th>#</th>
                                    <th>园长电话</th>
                                    <th>园长姓名</th>
                                    <th>园所名称</th>
                                    <th>园所电话</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
          				</table>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?= DialogWidget::widget(["id"=>"modal-warning", 'leftBtn'=>'取消', 'rightBtn'=>"确定"]); ?>

</div>

<?php $this->beginBlock('js') ?>
var table
$(document).ready(function(){
	$("#addMaster").click(function(){
		window.location.href = "<?= \Yii::$app->urlManager->createUrl("headmaster/edit") ?>";
	});

    table = $('#example').DataTable({
    	lengthChange: false,
    	processing: true,
    	searching: false,
    	destroy: true,
    	paging: true,
    	ordering: false,
        serverSide: true,
        //pagingType: "first_last_numbers",
        language: {
        	"sZeroRecords": "没有匹配结果",
        	"sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
        	"sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
        	"sInfoFiltered": "(由 _MAX_ 项结果过滤)",
        	"sInfoPostFix": "",
            "sSearch": "搜索:",
            "sUrl": "",
            "sEmptyTable": "表中数据为空",
            "sInfoThousands": ",",
            "paginate": {
            	"first": "首页",
                "previous": "上页",
                "next": "下页",
            	"last": "末页",
                "processing": "正在处理中。。。"
            }

        },
        "columns": [
        	{data: 'id'},
        	{data: 'tel'},
        	{data: 'name'},
        	{data: 'school_name'},
        	{data: 'school_tel'},
        	{data: 'created_at'},
        	{data: null}
        ],
        "columnDefs": [
			{
				"targets": [-1],
				"orderable": false,
				render: function(data, type, row) { 
					var html ='<button class="btn btn-xs btn-info fa fa-edit" onclick="javascript:headMaster.doEdit('+row.id+')" value="'+row.id+'"></button>&nbsp;&nbsp;&nbsp;&nbsp;' +'<button class="btn btn-xs btn-danger fa fa-trash-o" onclick="javascript:headMaster.doDel('+row.id+')"></button>'; 
					return html; 
				}

        	}
        ],
        "ajax": {
        	type: "POST",
        	url: "<?= \Yii::$app->urlManager->createUrl("headmaster/get-list") ?>",
        	data:{}
        }
    });
    
});

var headMaster = {
	doEdit: function(id){
		window.location.href = "<?= \Yii::$app->urlManager->createUrl("headmaster/edit") ?>&id="+id;
	},
	doDel: function(id){
		dialogWidget.setBody("确定要删除园长信息？");
		dialogWidget.show(function(){
			dialogWidget.hide();
			var url = "<?= \Yii::$app->urlManager->createUrl("headmaster/del") ?>";
			var data = {'id': id, '_csrf-backend': '<?= Yii::$app->request->csrfToken; ?>'};
			$.post(url, data, function(res){
				table.ajax.reload();
			});
			
		});
	}
};


<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
