<?php
require dirname(__FILE__).'/lib/core_ext.php';
require dirname(__FILE__).'/lib/curl.php';

# TODO: generate authenticity token

class ActiveResource {
  protected $attributes;
  public $site = 'http://localhost:3000';
  public $timeout = 2000;
  public $http;
  public $resource;
  
  function __construct($attributes = array()) {
    $this->attributes = $attributes;
    $this->http = new Curl;
    $this->resources = pluralize(strtolower(get_class($this)));;
  }
  
  /* Class Methods */
  
  static function create($attributes = array()) {
    $class = get_called_class();
    $o = new $class($attributes);
    return $o->save();
  }
  
  static function find($type, $conditions) {
    $class = get_called_class();

    if (is_int($type)) {
    }
  }
  
  static function findAll($conditions = array()) {
    $class = get_called_class();
    $resources = pluralize(strtolower($class));
    extract(get_class_vars($class));
    
    $http = new Curl;
    $resp = $http->get($site . '/' . $resources . '.xml');
    
    $results = simplexml_load_string($resp->body)->page;
    
    if (!is_array($results))
      $results = array($results);

    foreach ($results as &$result) {
      $result = new $class((array) $result);
    }
    
    return $results;
  }

  /* Magic Methids */
  function __get($attr) {
    return $this->attributes[$attr];
  }

  function __set($attr, $value = null) {
    return $this->attributes[$attr] = $value;
  }
  
  function __callStatic($attr) {
    
  }
  
  function __find($type, $conditions = array()) {
    
  }

  /* Instance methods */
  
  function get($path = null, $params = array()) {}
  
  function post($path = null, $params = array()) {}
  
  function put($path = null, $params = array()) {}

  function exists($id = null) {
    # class Method
    if ($class = get_called_class()) {
        $obj = new $class(array('id'=> $id));
        return $obj->exists();
    }

    # instance Method
    if (!$this->id)
      return false;
    
    $url = "{$this->site}/{$this->resources}/{$this->id}.xml";
    $resp = $this->http->get($url);
    return $resp->headers["Status-Code"] == 200;
  }
  
  function delete($path = null, $params = array()) {
    # class Method
    if ($class = get_called_class()) {
      
    }
    
    # instance Method
  }

  function save() {
    if ($this->exists()) {
      $url = "{$this->site}/{$this->resources}/{$this->id}.xml";
      return $this->http->put($url, $this->attributes);
    }
    
    $url = "{$this->site}/{$this->resources}.xml";
    return $this->http->post($url, $this->attributes);
  }
  
  private 
    function url($path = null) {
      if ($path)
        return "{$this->site}/{$this->resources}/{$path}.xml";
      else
        return "{$this->site}/{$this->resources}.xml";
    }
}

?>