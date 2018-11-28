<?php

use backend\assets\FormAsset;

FormAsset::register($this);

$this->title = '园所设置';
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
              			<h3 class="box-title">编辑园所信息</h3>
            		</div>
          			<div class="box-body">
            			<form id="master-form" role="form" action="<?= \Yii::$app->urlManager->createUrl("school/edit") ?>" method="post">
                            <!-- text input -->
                            
                            <?php if(empty($model->id)):?>
                            <div class="form-group <?= $model->getFirstError("school_name")?"has-error":"" ?>">
                              <label><i class="fa <?= $model->getFirstError("school_name")?"fa-times-circle-o":"" ?>"></i> 园所名称</label>
                              <input id="school_name" name="School[school_name]" type="text" class="form-control" value="<?= empty($model->school_name)?"":$model->school_name ?>" placeholder="请输入园所名称 ...">
                              <span class="help-block"><?= $model->getFirstError("school_name")?></span>
                            </div>
                            <?php else:?>
                            <input type="hidden" value="<?= empty($model->school_name)?"":$model->school_name ?>" name="School[school_name]" >
                            <?php endif;?>
                            
                            <div class="form-group <?= $model->getFirstError("school_tel")?"has-error":"" ?>">
                              <label><i class="fa <?= $model->getFirstError("school_tel")?"fa-times-circle-o":"" ?>"></i> 园所电话</label>
                              <input id="school_tel" name="School[school_tel]" type="text" class="form-control" value="<?= empty($model->school_tel)?"":$model->school_tel ?>" placeholder="请输入园所电话 ...">
                              <span class="help-block"><?= $model->getFirstError("school_tel")?></span>
                            </div>
							<div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right">保存</button>
							</div>
							<input type="hidden" value="<?php echo Yii::$app->request->csrfToken; ?>" id="_csrf-backend" name="_csrf-backend" >
							<input type="hidden" value="<?= empty($model->id)?"":$model->id ?>" name="School[id]" >
            			</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $this->beginBlock('js') ?>
$(document).ready(function(){
    

});
<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</div>
