<?php
require '../active_resource.php';

class Page extends ActiveResource {
  var $site = 'http://localhost:3000';

}

// var_dump(Page::findAll());


var_dump(Page::create(array(
    'title' => 'This an title',
    'body' => 'article body'
)));


var_dump(Page::exists(1));

echo '<hr>';
$p = new Page;
var_dump($p->exists());


// 
// 
// $a = new Page(array(
//   'title' => 'This an title',
//   'body' => 'article body'
// ));
// 
// $a->save();
// 
// $a = Article::find(1);




?>