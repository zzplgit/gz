<?php
namespace backend\components;
use yii\base\Widget;

class DialogWidget extends Widget {
    
    public $id;
    public $leftBtn;
    public $rightBtn;
    
    
    public function init() {
        parent::init();
        if ($this->id === null) {
            $this->id = 'modal-dialog';
        }
        if ($this->leftBtn === null) {
            $this->leftBtn = "关闭";
        }
        if ($this->rightBtn === null) {
            $this->rightBtn = "保存";
        }
        
    }
  
    public function run() {
        return $this->render('DialogWidget',['id'=>$this->id, 'leftBtn'=>$this->leftBtn, 'rightBtn'=>$this->rightBtn]);
    }
}
?>