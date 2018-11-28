<?php

use backend\assets\AdminLtePluginAsset;
use backend\components\DialogWidget;
use backend\components\DataTableWidget;

AdminLtePluginAsset::register($this);

$this->title = '园所设置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">

    <section class="content">
		<div class="row">
        	<div class="col-xs-12">
          		<div class="box">
          			<div class="box-header">
              			<h3 class="box-title">园所列表</h3>
              			<button id="addBtn" type="button" class="btn btn-default pull-right">
              				<i class="fa fa-plus"></i> 新增园所
              			</button>
            		</div>
          			<div class="box-body">
						<?= DataTableWidget::widget(['id'=>'school-list', 
						    'columns' => [
						        ['#', "id"],
						        ['园所名称', "school_name"],
						        ['园所电话', "school_tel"],
						        ['操作', null]
						    ],
						    'actionBar' => [
						        ['fa fa-edit', 'doEdit']
						    ],
						    'ajax' => [
						        'url' => "school/get-list"
						    ]
						]); ?>



					</div>
				</div>
			</div>
		</div>
	</section>
	<?= DialogWidget::widget(["id"=>"modal-warning", 'leftBtn'=>'取消', 'rightBtn'=>"确定"]); ?>

</div>

<?php $this->beginBlock('js') ?>

$(document).ready(function(){
	$("#addBtn").click(function(){
		window.location.href = "<?= \Yii::$app->urlManager->createUrl("school/edit") ?>";
	});
	
	tableList.doEdit = function(id){
		window.location.href = "<?= \Yii::$app->urlManager->createUrl("school/edit") ?>&id="+id;
	}
});

var school = {
	doEdit: function(el){
		console.info(el);
	}
};

<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
