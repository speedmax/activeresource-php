<?php

require 'simpletest/runner.php';
require '../active_resource.php';

define('RESOURCE_URI', 'http://creagency-insurance.local');

class Article extends ActiveResource {
  var $site = RESOURCE_URI;
  
  function __toString() {
    return $this->title;
  }
}

class Question extends ActiveResource {
  var $site = RESOURCE_URI;
  
  function __toString() {
    return $this->text;
  }
}


## Find all

// 
// $p = Page::create(array(
//     'title' => 'Hello there elton an title',
//     'body' => 'article body'
// ));
// 

?>


<h1> All articles </h1>

<? foreach( Article::find('all') as $article): ?>

 <li><a href=""><?= $article ?></a></li>

<? endforeach ?>


<hr>


<h1> All Question</h1>

<? foreach( Question::find('all') as $question): ?>

 <li><a href=""><?= $question ?></a></li>

<? endforeach ?>