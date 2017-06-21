<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cache_manager
 *
 * @author Sohail Ahmad
 */

require_once dirname(dirname(__FILE__)) . '/cache_manager/phpfastcache/src/autoload.php';
use phpFastCache\CacheManager;
use phpFastCache\Core\phpFastCache;

class Cache_manager {
    
    private $instance_cache = null;
    private $cached_value = null;

    public function __construct() {

        CacheManager::setDefaultConfig([ // Setup File Path on your config files
            "path" => sys_get_temp_dir(),
            "defaultTtl" => 0,
        ]);
        
        // In your class, function, you can call the Cache
        $this->instance_cache = CacheManager::getInstance('files');
        
    }
    
    
    public function get_item($cache_key){

        $this->cached_value = $this->instance_cache->getItem((string)$cache_key);
        return $this->cached_value->get();
        
    }
    
    
    public function set_item($value, $cache_key = false){

        if($cache_key){
            $this->cached_value = $this->instance_cache->getItem((string)$cache_key);
        }
        $this->cached_value->set($value);
        $this->instance_cache->save($this->cached_value);
        
    }
    
    
    public function delete_item($cache_key){

        if(!is_null($this->instance_cache->getItem((string)$cache_key))){
            $this->instance_cache->deleteItem((string)$cache_key);
            return true;
        } else {
            return false;
        }
    }
    
}
