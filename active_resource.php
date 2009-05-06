<?php
require dirname(__FILE__).'/lib/core_ext.php';
require dirname(__FILE__).'/lib/curl.php';

class ActiveResource {
  protected $attributes;
  public $site = 'http://localhost:3000';
  public $timeout = 2000;
  public $connection;
  public $response;
  public $resources;
  public $extension = 'xml';
  
  function __construct($attributes = array()) {
    $this->attributes = $attributes;
    $this->connection = new Curl;
    $this->resources = pluralize(strtolower(get_class($this)));;
  }
  
  /* Class Methods */
  
  static function create($attributes = array()) {
    $class = get_called_class();
    $instance = new $class($attributes);
    
    if ($id = $instance->save()) {
      return $instance;
    }
    return false;
  }

  static function find($scope, $options = array()) {
    $class = get_called_class();
    switch ($scope) {
      case 'all': 
        return self::find_every($class, $options);
      case 'first':
        $collection = self::find_every($class, $options);
        return $collection[0];
      case 'last':
        return end(self::find_every($class, $options));
      default:
        return self::find_single($scope, $class, $options);
    }
  }

  
  /* Instance methods */
  
  function get($path = null, $params = array()) {
    
  }
  
  function post($path = null, $params = array()) {
    $data = $this->request_body($this->attributes);
    $this->connection->put($this->element_url(), $data);
  }
  
  
  function put($path = null, $params = array()) {
    
  }

  function delete($path = null, $params = array()) {
    $this->collection->delete($this->element_url());
  }

  function exists($id = null) {
    # class Method
    if (!isset($this) && $class = get_called_class()) {
        $obj = new $class();
        $obj->id = $id;
        return $obj->exists();
    }

    # instance Method
    if (!$this->id) return false;
    $class = get_class($this);
    
    $response = $this->connection->get(self::element_url($this->id, $class));
    return $response->headers["Status-Code"] == 200;
  }
  
  function destroy($id = null, $params = array()) {
    # class Method
    if (!isset($this) && $class = get_called_class()) {
      $obj = new $class(array('id'=> $id));
      return $obj->destroy();
    }
    
    # instance Method
    $class = get_class($this);
    if (!$this->id) return false;
    $response = $this->connection->delete(self::element_url($this->id, $class));
    return $response->headers["Status-Code"] == 200;
  }

  function save() {
    $data = $this->request_body($this->attributes);
    $class = get_class($this);
    
    if (isset($this->id)) {
      $this->connection->headers = array("Content-Type" => "application/xml");
      $this->connection->put(self::element_url($this->id, $class), $data);
      return true;
    }
    $this->connection->headers = array("Content-Type" => "application/xml");
    $response = $this->connection->post(self::collection_url($class), $data);
    
    # hack to get ID after its saved
    $this->id = end(explode('/', $response->headers["Location"]));
    return $this->id;
  }


  /* Magic Methids */
  function __get($attr) {
    $attr = str_replace('_', '-', $attr);
    return $this->attributes[$attr];
  }

  function __set($attr, $value = null) {
    $attr = str_replace('-', '_', $attr);
    return $this->attributes[$attr] = $value;
  }
  
  function __isset($attr) {
    return isset($this->attributes[$attr]);
  }

  function __callStatic($attr) {
  }

  private 

    static function find_single($scope, $class, $options = array()) {      
      $connection = new Curl;
      $url = self::element_url($scope, $class, $options);
      $response = $connection->get($url);
      
      if ($response->headers["Status-Code"] == "404") {
        throw new Exception("Object not found");
      }

      $xml = simplexml_load_string($resp->body)->{strtolower($class)};
      return new $class((array) $xml);
    }

    static function find_every($class, $options = array()) {
      $connection = new Curl;
      $url = self::collection_url($class, $options);
      $response = $connection->get($url);

      if ($response->headers["Status-Code"] == "404") {
        throw new Exception("Object not found");
      }
      
      $results = simplexml_load_string($response->body)->xpath("//". strtolower($class));
      
      if (!is_array($results))
        $results = array($results);

      foreach ($results as &$result) {
        $result = new $class((array) $result);
      }
      return $results;
    }

    static function config($class) {
      extract(get_class_vars($class));
      if ($site[strlen($site) - 1] === '/')
        $site = substr($site, 0, -1);
      return compact('site', 'timeout', 'extension');
    }

    static function collection_name($class = null) {
        if (!$class)
          $class = get_called_class();
        return strtolower(pluralize($class));
    }
      
    static function collection_url($class, $options = array()) {
      extract(self::config($class));
      $collection = self::collection_name($class);
      
      $query = !empty($options) ? '?' . http_build_query($options) : '';
      $url = "{$site}/{$collection}.{$extension}{$query}";
      return $url;
    }

    static function element_url($id, $class, $options = array()) {
      extract(self::config($class));
      $collection = self::collection_name($class);
      
      $query = !empty($options) ? '?' . http_build_query($options) : '';
      $url = "{$site}/{$collection}/{$id}.{$extension}{$query}";
      return $url;
    }

    function request_body($params) {
      $src = '';
      $element = strtolower(get_class($this));

      foreach ($params as $k => $v)
        $src .= "<{$k}>". utf8_encode($v) ."</{$k}>";
      return "<{$element}>{$src}</{$element}>";
    }
}
?>