<?php
namespace common\models;
use Yii;

use OSS\OssClient;
use OSS\Core\OssException;
require_once(Yii::getAlias('@vendor/aliyuncs/oss-sdk-php/autoload.php'));

/**
 * 阿里云oss存储sdk
 */
class  AliOss
{
    const OSS_ACCESS_ID = 'LTAIX0CdWCgXYIAZ';
    const OSS_ACCESS_KEY = 'Kmg2t6XHVEFSFqqytltEgJ2ZptlbiY';
    const OSS_ENDPOINT = 'oss-cn-hangzhou.aliyuncs.com';
    const OSS_BUCKET = 'gaozhan';

    function __construct()
    {

    }

    /**
    * 根据Config配置，得到一个OssClient实例
    *
    * @return OssClient 一个OssClient实例
    */
    public static function getOssClient($bucket=self::OSS_BUCKET)
    {
        try {
            $ossClient = new OssClient(self::OSS_ACCESS_ID, self::OSS_ACCESS_KEY, self::OSS_ENDPOINT, false);
        } catch (OssException $e) {
            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
        return $ossClient;
    }

    /**
    * 这个文件是否在OSS中 如果在 返回 true
    * 格式：$object = 'images/user/713/avatar/20170704110020149913722047641.jpg'
    *  AliOss::isOssFile( 'images/user/713/avatar/20170704110020149913722047641.jpg');
    */
    public  static  function isOssFile($object='',$bucket=self::OSS_BUCKET)
    {
        try {
            $object=self::quChuFanXieGang($object);
            $is_file = self::getOssClient()->doesObjectExist($bucket, $object);
        } catch (OssException $e) {
            return json_encode(['error'=>$e->getMessage()],true);
        }
        return (bool)$is_file;
    }

    /**
    * 删除OSS文件 如果$files为数组删除多个[] 如果为字符串删除单个
    * 格式：$object = 'images/user/713/avatar/20170704110020149913722047641.jpg'
    * AliOss::delectFile('images/user/713/avatar/20170704110020149913722047641.jpg');
    * AliOss::delectFile(['images/user/713/avatar/20170704110020149913722047641.jpg']);删除多个
    */
    public static function delectFile($OssOpen,$files,$bucket=self::OSS_BUCKET)
    {
        if(!$OssOpen) return false;
        try{
            if(is_array($files))
            {    //如果第一个字符为/  循环去除  /
                $files=array_map(array(__CLASS__,'quChuFanXieGang'),$files);
                return  self::getOssClient()->deleteObjects($bucket,$files);
            }else{
                $files=self::quChuFanXieGang($files);
                return self::getOssClient()->deleteObject($bucket,$files);
            }
        }catch(OssException $e) {
            return json_encode(['error'=>$e->getMessage()],true);
        }
    }

    /*
    * 删除OSS中文件 这个方法会先去判断OSS 中有没有该文件 然后删除
    */
    public static function isDelectFile($OssOpen,$files,$bucket=self::OSS_BUCKET){
        if(!$OssOpen) return false;
        try {
            if (!empty($files)){
                $files=self::quChuFanXieGang($files);
                $oss=self::getOssClient();
                if($oss->doesObjectExist($bucket, $files)){
                    $oss->deleteObject($bucket,$files);
                }
            }
            return true;
        }catch(OssException $e) {
            return json_encode(['error'=>$e->getMessage()],true);
        }
    }



    /*
    *
    *  此方法用于 将旧文件删除 然后添加新文件 可支持 多文件数组
    */

    public static function  updateFiles($OssOpen,$cdn_file,$Old,$Objects,$bucket=self::OSS_BUCKET)
    {
        if(!$OssOpen) return false;
        try{
            //判断有没有上传文件
            $oss = self::getOssClient();
            if (is_array($Old) && !empty($Old)) {    //如果第一个字符为/  循环去除  /
                foreach ($Old as $key => $value) {
                    $value= self::quChuFanXieGang($value);

                    if ($oss->doesObjectExist($bucket, $value)) {
                        /** 判断是否OSS中存在**/
                        $oss->deleteObject($bucket, $value);
                    }
                    if(!empty($Objects[$key])){
                        $str= self::quChuFanXieGang($Objects[$key]);
                        $oss->uploadFile($bucket, $str,$cdn_file.'/'.$str);

                    }
                }

            }else {
                if (!empty((array)$Objects)) {
                    foreach($Objects as $k=>$val)
                    {
                        $val= self::quChuFanXieGang($val);
                        $oss->uploadFile($bucket, $val,$cdn_file.'/'.$val);
                    }
                    return true;
                }
                return false;
            }
            return true;
        }catch (OssException $e){
            return json_encode(['error' => $e->getMessage()], true);
        }
    }



    /**
    * multipart上传统一封装，从初始化到完成multipart，以及出错后中止动作  $bucket, $object, $file, $options = null
    * OssOpen 是否使用 OSS 上传
    * $object 在OSS 上的地址 开头不需要 /   images/user/713/avatar/20170704110020149913722047641.jpg
    * $file 为本地路径
    * AliOss::upFile(Yii::$app->params['OssOpen'],'images/user/713/avatar/20170704134729149914724992746.jpg'（服务器上传路径）,本地上传路径);
    */
    public  static function upFile($OssOpen,$object,$file,$bucket=self::OSS_BUCKET, $options = null){
        if(!$OssOpen) return false;
        try{
            $object=self::quChuFanXieGang($object);
            if (!file_exists($file)) {
                return json_encode(['error'=>" file does not exist"],true);
            }
            return self::getOssClient()->multiuploadFile(self::OSS_BUCKET,$object,$file,$options);
        }catch(OssException $e) {
            return json_encode(['error'=>$e->getMessage()],true);
        }
    }

    /**
    * 上传本地文件
    *
    * @param string $bucket bucket名称
    * @param string $object object名称
    * @param string $file 本地文件路径
    * @param array $options
    * @return null
    * @throws OssException
    * AliOss::uploadFile(Yii::$app->params['OssOpen'],'images/user/713/avatar/20170704134729149914724992746.jpg'（服务器上传路径）,本地上传路径);
    */
    public static function uploadFile($OssOpen,$object, $file,$bucket=self::OSS_BUCKET, $options = NULL)
    {
        if(!$OssOpen) return false;
        try{
            $object=self::quChuFanXieGang($object);
            if (!file_exists($file)) {
                return json_encode(['error'=>" file does not exist"],true);
            }
            return self::getOssClient()->uploadFile(self::OSS_BUCKET,$object,$file,$options);
        }catch(OssException $e) {
            return json_encode(['error'=>$e->getMessage()],true);
        }
    }
    public static function uploadFileNoReturn($OssOpen,$object, $file,$bucket=self::OSS_BUCKET, $options = NULL)
    {
        if(!$OssOpen) return false;
        try{
            $object=self::quChuFanXieGang($object);
            if (!file_exists($file)) {
                return json_encode(['error'=>" file does not exist"],true);
            }
            self::getOssClient()->uploadFile(self::OSS_BUCKET,$object,$file,$options);
        }catch(OssException $e) {
            return json_encode(['error'=>$e->getMessage()],true);
        }
    }


   /*
    * $result = $ossClient->uploadDir(self::OSS_BUCKET,'demo/1/2/3','/Users/yasin/demo/1/2/3');
    * /**
    * 上传本地目录内的文件或者目录到指定bucket的指定prefix的object中
    *
    * @param string $bucket bucket名称
    * @param string $prefix 需要上传到的object的key前缀，可以理解成bucket中的子目录，结尾不能是'/'，接口中会补充'/'
    * @param string $localDirectory 需要上传的本地目录
    * @param string $exclude 需要排除的目录
    * @param bool $recursive 是否递归的上传localDirectory下的子目录内容
    * @param bool $checkMd5
    * @return array 返回两个列表 array("succeededList" => array("object"), "failedList" => array("object"=>"errorMessage"))
    * @throws OssException
    */
    public static function uploadDir($prefix, $localDirectory,$bucket=self::OSS_BUCKET,  $exclude = '.|..|.svn|.git', $recursive = false, $checkMd5 = true)
    {
        try{
            $prefix=self::quChuFanXieGang($prefix);
            return self::getOssClient()->uploadDir($bucket,$prefix,$localDirectory,$exclude,$recursive,$checkMd5);
        }catch(OssException $e) {
            return json_encode(['error'=>$e->getMessage()],true);
        }
    }

    /*
    *去除 路径第一符号 /
    */
    public static function quChuFanXieGang($object)
    {
        if(substr($object, 0, 1 ) == '/'){
           $object=substr($object, 1);
       }
       return  $object;
   }

}
