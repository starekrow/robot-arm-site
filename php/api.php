<?php

namespace starekrow\robot_arm;

class api
{
    public $version = "1.0";
    public $query = null;
    public $post = null;
    public $path = null;
    public $method = null;

    const TMPDIR = "/tmp/starekrow-robot-arm";
    const THUMB_DIR = "/tmp/starekrow-robot-arm/thumbs";
    const IMAGE_DIR = "/tmp/starekrow-robot-arm/images";
    const PICS_DIR = "../pics";

    protected function typeForName($filename)
    {
        switch(strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            case "jpg":
            case "jpeg":
                return "image/jpeg";
            case "png":
                return "image/png";
            case "gif":
                return "image/gif";
        }
        return null;
    }

    protected function getPicNames()
    {
        $names = [];
        $pdir = self::PICS_DIR;
        $pd = opendir($pdir);
        for (;;) {
            $fn = readdir($pd);
            if (!is_string($fn)) {
                break;
            }
            if ($this->typeForName($fn)) {
                $names[] = $fn;
            }
        }
        closedir($pd);
        return $names;
    }

    public function makeThumbnail($sourcefile, $thumbfile)
    {
        if (!is_dir(self::THUMB_DIR)) {
            mkdir(self::THUMB_DIR);
        }
        list($w, $h) = getimagesize($sourcefile);
        if ($w > $h) {
            $thumb_w = min($w, 200);
            $thumb_h = floor($h * $thumb_w / $w);
        } else {
            $thumb_h = min($h, 200);
            $thumb_w = floor($w * $thumb_h / $h);
        }
        $type = $this->typeForName($sourcefile);
        switch($type){
            case 'image/jpeg':
                $source = imagecreatefromjpeg($sourcefile);
                break;
            case 'image/png':
                $source = imagecreatefrompng($sourcefile);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($sourcefile);
                break;
            default:
                return;
        }

        $thumb = imagecreatetruecolor($thumb_w, $thumb_h);
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $thumb_w, $thumb_h, $w, $h);
        switch($type){
            case 'image/jpeg':
                imagejpeg($thumb,$thumbfile,90);
                break;

            case 'image/png':
                imagepng($thumb,$thumbfile,90);
                break;

            case 'image/gif':
                imagegif($thumb,$thumbfile,90);
                break;
        }
    }

    public function flush()
    {
        exec("rm -rf " . self::TMPDIR);
        return true;
    }

    public function thumb($filename)
    {
        $sourcefile = self::PICS_DIR . "/$filename";
        $type = $this->typeForName($sourcefile);
        if (!$type) {
            return false;
        }
        if (file_exists($sourcefile)) {
            $thumbfile = self::THUMB_DIR . "/$filename";
            if (!file_exists($thumbfile)) {
                $this->makeThumbnail($sourcefile, $thumbfile);
            }
            if (file_exists($thumbfile)) {
                header("Content-Type: $type");
                readfile($thumbfile);
                die();
            }
        }
        return "NoSuchFile";
    }

    public function info()
    {
        $list = $this->getPicNames();
        return [ "list" => $list ];
    }

    public function image($id)
    {
        $fn = self::PICS_DIR . "/$id";
        $type = $this->typeForName($fn);
        if (!$type) {
            return false;
        }
        if (file_exists($fn)) {
            header("Content-Type: $type");
            readfile($fn);
            return null;
        }
        return false;
    }

    const SETTINGS_FILE = "/tmp/starekrow-robot-arm/settings";
    protected $currentSettings;

    protected function settingValue($setting, $dfault)
    {
        if (!$currentSettings) {
            $data = @file_get_contents(self::SETTINGS_FILE);
            if ($data) {
                $data = @unserialize($data);
            }
            if ($data) {
                $currentSettings = $data;
            } else  {
                $currentSettings = (object)[];
            }
        }
        if (property_exists($this->currentSettings, $setting)) {
            $s = $this->currentSettings->{$setting};
            if ($s->expires === false || $s->expires > time()) {
                return $s->value;
            }
        }
        return $dfault;
    }

    protected function settingApply($setting, $value, $ttl = false)
    {
        $check = $this->settingValue($setting);
        $expires = $ttl === false ? $ttl : $ttl + time();
        $this->currentSettings->{$setting} = (object)[
            "expires" => $expires,
            "value"   => $value
        ];
        file_put_contents(self::SETTINGS_FILE, serialize($this->currentSettings));
    }

    static function __route($path, $method, $query)
    {
        $api = new api();
        
        $parts = explode('/', $path);
        if (count($parts) <= 1) {
            header("Content-Type: text/plain");
            echo "\"Yep, this is the API.\"";
            return;
        }
        $api->tmpdir = self::TMPDIR;
        $api->method = $method;
        $api->query = $query;
        $api->path = implode('/', array_slice($parts, 2));
        $api->endpoint = $parts[1];
        if ($method == 'POST') {
            $api->post = $_POST;
        }
        if (method_exists($api, $parts[1])) {
            $reflection = new \ReflectionMethod($api, $parts[1]);
            if ($reflection->isPublic()) {
                $np = $reflection->getNumberOfParameters();
                $args = [];
                if ($np >= 1) {
                    $args[] = count($parts) > 2 ? $parts[2] : null;
                }
                if ($np >= 2) {
                    $args[] = count($parts) > 3 ? $parts[3] : null;
                }
                $part1 = count($parts) > 1 ? $parts[1] : null;
                $part2 = count($parts) > 2 ? $parts[2] : null;
                $result = $reflection->invokeArgs($api, $args);
                if ($result !== null) {
                    header("Content-Type: application/json");
                    echo json_encode($result);
                }
                return;
            }
        }
        header("Content-Type: text/plain");
        echo "\"NoSuchEndpoint\"";
    }

    protected function __construct()
    {
        if (!is_dir(self::TMPDIR)) {
            mkdir(self::TMPDIR, 0777, true);
        }
    }
}

api::__route($_SERVER['PATH_INFO'], $_SERVER['HTTP_METHOD'], $_GET);
