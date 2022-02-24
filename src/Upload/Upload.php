<?php


namespace Taoran\HyperfPackage\Upload;

use Hyperf\Di\Annotation\Inject;

use Hyperf\HttpServer\Contract\RequestInterface;
use function Taoran\HyperfPackage\Helpers\get_msectime;


class Upload
{
    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;

    /**
     * 上传到本地
     *
     * @param $upload_path 上传路径
     */
    public function toLocal($file, $upload_path, $filename = null)
    {
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $filename = $filename ?? get_msectime() . '.' . $file->getExtension();

        $pathfull = $upload_path . $filename;
        $file->moveTo($pathfull);
        if (!$file->isMoved()) {
            throw new \Exception('文件上传失败!');
        }
        return $pathfull;
    }

    /**
     * 上传到alioss
     */
    public function toAlioss($file, $upload_remote_path, $upload_path = 'uploads/tmp/', $filename = null)
    {
        //文件名
        $filename = $filename ?? get_msectime() . '.' . $file->getExtension();
        //完整路径
        $pathfull = $upload_path . $filename;
        //先上传本地
        $this->toLocal($file, $upload_path, $filename);

        //上传alioss
        $uploadAli = new \Taoran\HyperfPackage\Upload\Aliyun\Upload();
        $uploadAli->uploadDirect($upload_remote_path . $filename, $pathfull, 'public');
        $path = config('aliyun.oss.bucket_domain') . '/' . $upload_remote_path . $filename;

        //清除临时文件
        @unlink($pathfull);
        //return
        return $this->responseCore->success([
            'path' => $path
        ]);
    }

    /**
     * check
     * @return \Hyperf\HttpMessage\Upload\UploadedFile|\Hyperf\HttpMessage\Upload\UploadedFile[]|null
     */
    public function checkFile()
    {
        if ($this->request->hasFile('file') && $this->request->file('file')->isValid()) {
            $file = $this->request->file('file');
        } else {
            throw new \Exception('文件上传失败!');
        }

        $this->extCheck($this->request->file('file')->getExtension());

        return $file;
    }

    /**
     * 文件格式验证
     *
     * @param $ext
     * @return bool
     * @throws Exception
     */
    public function extCheck($ext)
    {
        $ext = strtolower($ext);
        $exts = ExtGroup::$ext;
        if (!in_array($ext, $exts)) {
            throw new \Exception('不支持的文件类型！');
        }
        return true;
    }
}
