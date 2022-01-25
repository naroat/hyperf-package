<?php


namespace Taoran\HyperfPackage\Upload\Local;

use Taoran\HyperfPackage\Upload\UploadInterface;

class Upload implements UploadInterface
{
    /**
     * 上传
     *
     * @param $file
     * @param $filename
     * @return mixed
     */
    public function upload($file, $path, $filename, $acl)
    {
        try {
            $result = $file->moveTo($path, $filename);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        //返回路径 + 文件名
        return '/' . $path . '/' . $filename;
    }
}
