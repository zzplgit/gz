<?php

use backend\assets\AdminLtePluginAsset;
use backend\components\DialogWidget;
use backend\components\DataTableWidget;

AdminLtePluginAsset::register($this);

$this->title = '学年设置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">

    <section class="content">
		<div class="row">
        	<div class="col-xs-12">
          		<div class="box">
          			<div class="box-header">
              			<h3 class="box-title">学年列表</h3>
              			<button id="addBtn" type="button" class="btn btn-default pull-right">
              				<i class="fa fa-plus"></i> 新增学年
              			</button>
            		</div>
          			<div class="box-body">
						<?= DataTableWidget::widget(['id'=>'year-list', 
						    'columns' => [
						        ['#', "id"],
						        ['学年名称', "title"],
						        ['学年开始时间', "start_at"],
						        ['学年结束时间', "end_at"],
						        ['创建时间', "created_at"],
						        ['修改时间', "updated_at"],
						        ['操作', null]
						    ],
						    'actionBar' => [
						        ['fa fa-edit', 'doEdit']
						    ],
						    'ajax' => [
						        'url' => "year/get-list"
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
		window.location.href = "<?= \Yii::$app->urlManager->createUrl("year/edit") ?>";
	});
	
	tableList.doEdit = function(id){
		window.location.href = "<?= \Yii::$app->urlManager->createUrl("year/edit") ?>&id="+id;
	}
});

<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
