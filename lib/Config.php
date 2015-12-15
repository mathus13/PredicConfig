<?php
namespace Ethereal;

use DirectoryIterator;
use Ethereal\Cache;
use Ethereal\Hooks;

/**
 * Class to hold and retrieve config data from a cached service
 * @author Shawn Barratt
 * @see Ethereal\Cache
 * @see Ethereal\Hooks
 */
class Config
{
    
    protected $config;
    protected $cache;
    protected $hooks;
    protected $dir = __DIR__.'/config/';
    protected $key = '\Ethereal\Config';
    
    /**
     * Build Config object
     * @param Ethereal\Cache $cache [description]
     * @param Ethereal\Hooks $hooks [description]
     */
    public function __construct(Cache $cache, Hooks $hooks)
    {
        $this->hooks = $hooks;
        $this->cache = $cache;
        $this->config = $this->getConfig();
    }

    protected function getConfig()
    {
        if ($config = $this->cache->get($this->key)) {
            return (array) $config;
        }
        $config = array();
        if (is_dir($this->dir)) {
            $files = array();
            foreach (new \DirectoryIterator($this->dir) as $file) {
                if (strpos($file->getFilename(), '.json') && $file->isReadable()) {
                    $files[$file->getFilename()] = $file->getPathname();
                }
            }
            ksort($files);
            foreach ($files as $name => $path) {
                $h = fopen($path, 'r+');
                $json = fread($h, 2048);
                if ($parse = json_decode($json)) {
                    foreach ($parse as $k => $v) {
                        $config[$k] = $v;
                    }
                } else {
                    error_log("Invalid Config: in {$path}\n {$json}");
                }
            }
        }
        $config = $this->hooks->fire("{$this->key}build", $config);
        $this->cache->set($this->key, $config);
        return $config;
    }

    /**
     * Set a config item
     * @param string $name [description]
     * @param any $data [description]
     */
    public function set($name, $data)
    {
        $this->config[$name] = $data;
        $this->cache->set($this->key, $config);
    }

    public function get($name)
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }
}
