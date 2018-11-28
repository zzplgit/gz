<?php

use backend\assets\FormAsset;

FormAsset::register($this);


$this->title = '学年设置';
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
          			<div class="box-body">
            			<form id="master-form" role="form" action="<?= \Yii::$app->urlManager->createUrl("year/edit") ?>" method="post">
                            <!-- text input -->
                            <div class="form-group <?= $model->getFirstError("title")?"has-error":"" ?>">
                              <label><i class="fa <?= $model->getFirstError("title")?"fa-times-circle-o":"" ?>"></i> 学年名称</label>
                              <input name="Year[title]" type="text" class="form-control" value="<?= empty($model->title)?date("Y")."到".(date("Y")+1)."学年":$model->title ?>" placeholder="请输入学年名称 ...">
                              <span class="help-block"><?= $model->getFirstError("title")?></span>
                            </div>
                            <div class="form-group <?= $model->getFirstError("start_at")?"has-error":"" ?>">
                              <label><i class="fa <?= $model->getFirstError("start_at")?"fa-times-circle-o":"" ?>"></i> 开始时间</label>
                              <div class="input-group">
                                  <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                  </div>
                                  <input type="text" name="Year[start_at]" class="form-control" value="<?= empty($model->start_at)?date("Y")."0901":date("Ymd", $model->start_at) ?>" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask>
                              </div>
                              <span class="help-block"><?= $model->getFirstError("start_at")?></span>
                            </div>
                            <div class="form-group <?= $model->getFirstError("end_at")?"has-error":"" ?>">
                              <label><i class="fa <?= $model->getFirstError("end_at")?"fa-times-circle-o":"" ?>"></i> 结束时间</label>
                              <div class="input-group">
                                  <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                  </div>
                                  <input type="text" name="Year[end_at]" class="form-control" value="<?= empty($model->end_at)?(date("Y")+1)."0701":date("Ymd", $model->end_at) ?>" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask>
                              </div>
                              <span class="help-block"><?= $model->getFirstError("end_at")?></span>
                            </div>
							<div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right">保存</button>
							</div>
							<input type="hidden" value="<?php echo Yii::$app->request->csrfToken; ?>" id="_csrf-backend" name="_csrf-backend" >
							<input type="hidden" value="<?= empty($model->id)?"":$model->id ?>" name="Year[id]" >
            			</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $this->beginBlock('js') ?>
$(document).ready(function(){
    $('[data-mask]').inputmask();

});
<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</div>
