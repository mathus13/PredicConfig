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
    
    private $config;
    protected $cache;
    protected $hooks;
    protected $dir = __DIR__.'/config/';
    protected $key = '\Ethereal\Config';
    
    /**
     * Build Config object
     * @param Ethereal\Cache $cache [description]
     * @param Ethereal\Hooks $hooks [description]
     */
    public function __construct(Ethereal\Cache $cache, Ethereal\Hooks $hooks)
    {
        $this->hooks = $hooks;
        $this->cache = $cache;
        $this->config = $this->getConfig();
    }

    protected function getConfig()
    {
        if ($config = $this->cache->get($this->key)) {
            return $config;
        }
        if (!is_dir($this->dir)) {
            throw new \Exception('Invalid Config Directory: '.$this->dir);
        }
        $config = $this->hooks->fire("{$this->key}build", array());
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
        $name = "{$this->key}{$name}";
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }
}
