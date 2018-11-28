<?php
namespace backend\components;
use yii\base\Widget;

class DataTableWidget extends Widget {
    
    public $id;
    public $columns;
    public $actionBar;
    public $ajax;
    
    private $params = [];
    
    public function init() {
        parent::init();
        if ($this->id === null) {
            $this->id = 'data-table';
        }
        if ($this->columns === null) {
            $this->columns = [];
        }
        if ($this->actionBar === null) {
            $this->actionBar = false;
        }
        if ($this->ajax === null) {
            $this->ajax = [];
        }
    }
  
    public function run() {
        $this->getParams();
        return $this->render('DataTableWidget', $this->params);
    }
    
    private function getParams() {
        $this->params = [
            'id'=>$this->id,
            'columns'=>$this->columns,
            'actionBar'=>$this->actionBar,
            'ajax' => $this->ajax
        ];
    }
    
}
?>