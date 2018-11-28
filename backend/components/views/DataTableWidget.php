<?php
?>


<table id="<?= $id ?>" class="table table-bordered table-striped" style="width:100%">
    <thead>
        <tr>
        	<?php foreach ($columns as $c_value):?>
            <th><?= $c_value[0] ?></th>
            <?php endforeach;?>
        </tr>
    </thead>
</table>

<?php $this->beginBlock('js') ?>

(function(doc, $, owner){
	var dataTable = $('#<?= $id ?>').DataTable({
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
        	<?php foreach ($columns as $c_value):?>
            {data: '<?= $c_value[1] ?>'},
            <?php endforeach;?>
        ],
        <?php if($actionBar):?>
        "columnDefs": [
			{
				"targets": [-1],
				"orderable": false,
				render: function(data, type, row) { 
					var html = '';
					<?php foreach ($actionBar as $act_value):?>
					html += '<button class="btn btn-xs btn-info <?= $act_value[0] ?>" onclick="javascript:window.tableList.<?= $act_value[1] ?>('+row.id+')"></button>';
					 
					<?php endforeach;?>
					return html; 
				}

        	}
        ],
        <?php endif;?>
        "ajax": {
        	type: "GET",
        	url: "<?= \Yii::$app->urlManager->createUrl($ajax['url']) ?>",
        	data:{}
        }
	});
	owner.dataTable = dataTable;

}(document, $, window.tableList = {}));




<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>