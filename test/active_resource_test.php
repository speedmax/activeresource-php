<?php
require '../active_resource.php';

class Page extends ActiveResource {
  var $site = 'http://localhost:3000';
}


## Find all


$p = Page::create(array(
    'title' => 'This an title',
    'body' => 'article body'
));

$p->title ="This is an title changed";
$p->save();

// var_dump(Page::find(1));

// Expecting Object not found exception
//var_dump(Page::find(100));

var_dump(Page::find('all'));
 

var_dump(Page::find('first'));
var_dump(Page::find('all'));
var_dump(Page::find('last'));

var_dump(Page::destroy(Page::find('last')->id));
var_dump(Page::find('last')->destroy());

var_dump(Page::exists(1));

echo '<hr>';
$p = new Page;
$p->id = 100;
var_dump($p->exists());


// 

$a = new Page(array(
  'title' => 'This an title',
  'body' => 'article body'
));

$a->save();
// 
// $a = Article::find(1);




?>