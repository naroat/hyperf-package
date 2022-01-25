<?php


namespace Taoran\HyperfPackage\Upload;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\HttpServer\Contract\RequestInterface;
class Upload extends UploadAbstract
{
    /**
     * 上传驱动
     *
     * local, aliyun
     * @var bool|string
     */
    public $drive = 'local';

    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;

    /**
     * 文件
     *
     * @var
     */
    public $file;

    /**
     * 文件名
     *
     * @var
     */
    public $filename;

    /**
     * 文件后缀名
     *
     * @var
     */
    public $ext;

    /**
     * 存储路径
     *
     * @var
     */
    public $path;

    /**
     * 访问权限
     *
     * @var public/private(公共读/私有)
     */
    public $acl = 'private';

    /**
     * 上传类
     *
     * @var
     */
    public $upload;

    public function __construct()
    {
        $this->drive = config('upload.drive');
        $this->setFile();
        $this->getDrive($this->drive);
    }

    /**
     * 设置文件
     */
    public function setFile()
    {
        if ($this->request->hasFile('file') && $this->request->file('file')->isValid()) {
            $this->file = $this->request->file('file');
        } else {
            throw new Exception('文件错误!');
        }
    }

    /**
     * 获取驱动
     *
     * @param string $drive
     */
    public function getDrive(string $drive)
    {
        $drive = '\Taoran\HyperfPackage\Upload\\' . ucfirst($drive) . '\Upload';

        if (class_exists($drive)) {
            $this->upload = new $drive();
        } else {
            throw new \Exception('上传失败!');
        }
    }

    /**
     * 上传入口
     */
    public function upload()
    {
        //验证扩展名
        $this->extCheck($this->getExt());
        //验证大小
        $this->sizeCheck();
        //上传文件
        return $this->upload->upload($this->file, $this->getPath(), $this->getFileName(), $this->acl);
    }

    /**
     * 获取存储路径
     */
    public function getPath()
    {
        return !empty($this->path) ? $this->path : 'uploads';
    }

    /**
     * 获取文件名
     */
    public function getFileName()
    {
        return !empty($this->filename) ? $this->filename : md5(time() . rand(1000, 9999)) . '.' . $this->getExt();
    }

    /**
     * 文件大小验证
     */
    public function sizeCheck()
    {
        //文件大小
        $filesize = $this->file->getClientSize();

        //php.ini中配置的上传文件的最大大小
        $maxFileSize = $this->file->getMaxFilesize();

        if ($filesize > $maxFileSize) {
            throw new \Exception('上传失败，文件过大！');
        }
    }

    /**
     * 获取后缀名
     */
    public function getExt()
    {
        //根据mime类型获取扩展名
        $this->ext = empty($this->ext) ? $this->file->guessExtension() : $this->ext;
        return $this->ext;
    }
}
