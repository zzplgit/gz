<?php

use backend\assets\FormAsset;
use backend\components\DialogWidget;

FormAsset::register($this);

$this->title = '园长设置';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-contact">

	<?php if($model->getFirstError("alert")):?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> 错误!</h4>
        <?= $model->getFirstError("alert") ?>
    </div>
    <?php endif;?>
    

    <section class="content">
		<div class="row">
        	<div class="col-xs-12">
          		<div class="box">
          			<div class="box-header">
              			<h3 class="box-title">编辑园长信息</h3>
            		</div>
          			<div class="box-body">
            			<form id="master-form" role="form" action="<?= \Yii::$app->urlManager->createUrl("headmaster/edit") ?>" method="post">
                            <!-- text input -->
                            <div class="form-group <?= $model->getFirstError("school_name")?"has-error":"" ?>">
                              <label><i class="fa <?= $model->getFirstError("school_name")?"fa-times-circle-o":"" ?>"></i> 园所名称</label>
                              <input id="school_name" name="Headmaster[school_name]" type="text" class="form-control" value="<?= empty($model->school_name)?"":$model->school_name ?>" placeholder="请输入园所名称 ...">
                              <span class="help-block"><?= $model->getFirstError("school_name")?></span>
                            </div>
                            <div class="form-group <?= $model->getFirstError("school_tel")?"has-error":"" ?>">
                              <label><i class="fa <?= $model->getFirstError("school_tel")?"fa-times-circle-o":"" ?>"></i> 园所电话</label>
                              <input id="school_tel" name="Headmaster[school_tel]" type="text" class="form-control" value="<?= empty($model->school_tel)?"":$model->school_tel ?>" placeholder="请输入园所电话 ...">
                              <span class="help-block"><?= $model->getFirstError("school_tel")?></span>
                            </div>
                            <div class="form-group <?= $model->getFirstError("name")?"has-error":"" ?>">
                              <label><i class="fa <?= $model->getFirstError("name")?"fa-times-circle-o":"" ?>"></i> 园长名称</label>
                              <input id="name" name="Headmaster[name]" type="text" class="form-control" value="<?= empty($model->name)?"":$model->name ?>" placeholder="请输入园长名称 ...">
                              <span class="help-block"><?= $model->getFirstError("name")?></span>
                            </div>
                            <div class="form-group <?= $model->getFirstError("tel")?"has-error":"" ?>">
                              <label><i class="fa <?= $model->getFirstError("tel")?"fa-times-circle-o":"" ?>"></i> 园长电话</label>
                              <input id="tel" name="Headmaster[tel]" type="text" class="form-control" value="<?= empty($model->tel)?"":$model->tel ?>" placeholder="请输入园长电话 ...">
                              <span class="help-block"><?= $model->getFirstError("tel")?></span>
                            </div>
							<div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right">保存</button>
							</div>
							<input type="hidden" value="<?php echo Yii::$app->request->csrfToken; ?>" id="_csrf-backend" name="_csrf-backend" >
							<input type="hidden" value="<?= empty($model->uid)?"":$model->uid ?>" name="Headmaster[uid]" >
            			</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?= DialogWidget::widget(["id"=>"modal-warning"]); ?>
</div>

<?php $this->beginBlock('js') ?>
$(document).ready(function(){
    var showWarning = true;
    
    <?php if($schoolModel):?>
    var availableTags = [
    	<?php foreach ($schoolModel as $schoolVal):?>
    	{label:"<?= $schoolVal->school_name; ?>", tel:'<?= $schoolVal->school_tel; ?>','code':'<?= $schoolVal->school_code; ?>'},
    	<?php endforeach;?>
    ]
    <?php else:?>
    var availableTags = [];
    <?php endif;?>

    $( "#school_name" ).autocomplete({
		source: availableTags,
		select: function(event, ui){
			$("#school_tel").val(ui.item.tel);
		}
    });

	$("#master-form").submit(function(){
	    var endSubmit = false;
		if(showWarning){
    		var schoolName = $("#school_name").val();
    		var schoolTel = $("#school_tel").val();
    		$.each(availableTags, function(i, n){
    			if(schoolName == n.label && schoolTel != n.tel){
    				dialogWidget.setBody("您已修改预设园所("+schoolName+")电话，保存后将同时修改对应园信息");
    				dialogWidget.show(function(){
    					showWarning = false;
    					$("#master-form").submit();
    				});
    				endSubmit = true;
    				return false;
    			}
    		});
		}
		if(endSubmit){
			return false;
		}
	});
});
<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</div>
