<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\AliOss;

/**
 * UploadForm model
 */
class UploadForm extends Model {

    public $file;
    public $file_path;
    
    
    public function rules(){
        return [
        		[['file'], 'file', 'extensions' => 'doc, dot, docx', 'skipOnEmpty'=>true, 'on'=>['pccnProposalText']],
        		[['file'], 'file', 'extensions' => 'doc, dot, docx', 'skipOnEmpty'=>true, 'on'=>['pccnResearchReport']],
        		[['file'], 'file', 'extensions' => 'ppt, pptx', 'skipOnEmpty'=>true, 'on'=>['pccnReportPPT']],
        		[['file'], 'file', 'extensions' => 'doc, dot, docx', 'skipOnEmpty'=>true, 'on'=>['pccnProposalReport']],
        		[['file'], 'file', 'extensions' => 'doc, dot, docx, ppt, pptx, pdf', 'skipOnEmpty'=>true, 'on'=>['pccnReportCharacteristic']],
        		[['file'], 'file', 'extensions' => 'bmp, jpg, jpge, png', 'skipOnEmpty'=>true, 'on'=>['pccnSchoolImg']],
        		[['file'], 'file', 'extensions' => 'bmp, jpg, jpge, png', 'skipOnEmpty'=>true, 'on'=>['pccnProposalPic']],
        ];
    }

    /**
     * @inheritdoc
     * 表单中文名称映射
     */
    public function attributeLabels(){
        return [];
    }
    
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 保存文件
     * @author zgh
     * @param string $path 文件路径
     * @param string $name 文件名称
     * @return boolean
     */
    public function saveData($path = NULL){
    	if (empty($path)){
    		$dir1 = self::getRandStr(2);
    		$dir2 = self::getRandStr(2);
    		$path = '/files/cppcc/'.$dir1."/".$dir2;
    	}

    	$extension = $this->file->getExtension();
    	$rand = md5(time() . rand(1111,9999));
    	$fName = $rand."." . $extension;
        $uploadpath = self::getStaticPath($path);
        $this->file_path = $path."/".$fName;
        $this->file->saveAs($uploadpath."/".$fName);
        AliOss::uploadFile(Yii::$app->params['OssOpen'], $path."/".$fName, $uploadpath."/".$fName);
        return true;
    }

    public function saveBase64($base64, $type = NULL){
		$dir1 = self::getRandStr(2);
		$dir2 = self::getRandStr(2);
		$path = '/files'.($type ? "/".$type : '').'/'.$dir1."/".$dir2;
    	
    	if (strstr($base64,",")){
    		$base64 = explode(',',$base64);
    		$base64 = $base64[1];
    	}
    	$rand = substr(md5(time() . rand(1111,9999)),8,16);
    	$fName = $rand."." . "png";
    	$uploadpath = self::getStaticPath($path);
    	$r = file_put_contents($uploadpath."/".$fName, base64_decode($base64));
    	if (!$r) {
    		$this->addErrors(['file' => "图片生成失败"]);
            return false;
    	}
    	$this->file_path = $path."/".$fName;
    	AliOss::uploadFile(Yii::$app->params['OssOpen'], $path."/".$fName, $uploadpath."/".$fName);
    	return true;
    }
    
    /**
     * 删除图片
     * @author zgh
     * @param string $file
     */
    public function delPic($file){
        $uploadpath = self::getStaticPath();
        $info = $uploadpath.$file;
        if(is_file($info)){
            if(unlink($info)){
                return true;
            }else{
                if(chmod($info,0777)){
                    if(unlink($info)){
                        return true;
                    }else{
                        $this->addErrors(['file' => "删除失败。"]);
                        return false;
                    }
                }else{
                    $this->addErrors(['file' => "删除失败,没有权限。"]);
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * 等比缩放
     * @param string $image_path 源图片路径
     * @param string $max_width 最大宽
     * @param string $max_height 最大高
     * @param string $img_quality 图片质量
     * @return string
     */
    public function thumb($original_image, $image_path, $max_width=100, $max_height=100, $prefix=NULL, $nopic=NULL, $img_quality=100) {
        if ($original_image) {
            $image_arr = explode('/', $original_image);
            $image_name = $image_arr[count($image_arr) - 1];
            $thumbnails = $image_path . $prefix . $image_name; //缩略图
            $thumbnails_full = $thumbnails;
            $thumbnails_name = $prefix . $image_name;
            //图片处理开始
            list($width, $height, $type, $attr) = getimagesize($original_image);
            if ($width > $max_width) {
                $scale_1 = $max_width / $width;
                $scale_2 = $max_height / $height;
                if ($scale_1 < $scale_2)
                    $scale = $scale_1;
                else
                    $scale = $scale_2;
                $new_width = floor($width * $scale);
                $new_height = floor($height * $scale);
            }else {
                if ($height > $max_height) {
                    $scale = $max_height / $height;
                    $new_height = $max_height;
                    $new_width = floor($width * $scale);
                }
            }
            switch ($type) {
                case 1: $img = imagecreatefromgif($original_image);
                    break;
                case 2: $img = imagecreatefromjpeg($original_image);
                    break;
                case 3: $img = imagecreatefrompng($original_image);
                    break;
            }
            $new_img = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($new_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($new_img, $thumbnails_full, $img_quality);
            imagedestroy($new_img);
            //图片处理结束
            return $thumbnails_name;
        }else
            return $nopic;
    }

    
    /**
     * 返回static路径
     * @author zgh
     * @param string $path
     * @return boolean|string
     */
    public static function getStaticPath($path = '') {
    	$staticPath = \Yii::getAlias('@cdn') . $path;
    	if (!is_dir($staticPath)) {
    		if (!mkdir($staticPath, 0777, TRUE)) {
    			return false;
    		}
    	}
    	return $staticPath;
    }
    
    /**
          * 随机数
     * @param string $len
     * @return string
     */
    public static function getRandStr($len) {
    	$chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
    	$charsLen = count($chars) - 1;
    	shuffle($chars);
    	$output = "";
    	for ($i = 0; $i < $len; $i++) {
    		$output .= $chars[mt_rand(0, $charsLen)];
    	}
    	return $output;
    }
    
    
}
