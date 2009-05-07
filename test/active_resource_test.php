<?php

require '../active_resource.php';

define('RESOURCE_URI', 'http://admin:CheeseBurger@localhost');

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

# edit form
if (!empty($_GET['action']) && $_GET['action'] == 'edit'):
  $article = Article::find($_GET['article']);

  if (!empty($_POST['article'])) {
    $article->title = $_POST['article']['title'];
    $article->body = $_POST['article']['body'];
    $article->save();
  
    header("Location: {$_SERVER["PHP_SELF"]}");
  }
?>
<form method="post">
  <p>
  <label>Title</label><br/>
  <input name="article[title]" value="<?=$article->title?>">
  </p>
  
  <p>
  <label>Body</label><br/>
  <textarea name="article[body]" rows="20" cols="80"><?=$article->body?></textarea><br/>
  </p>

  <input type="submit" value="Update">
</form>
<? die;endif ?>


<? if (isset($_GET['article'])): ?>
  <? $article = Article::find($_GET['article']) ?>
  <h1><?= $article->title ?></h1>
  <a href="<?=$_SERVER['REQUEST_URI']?>&action=edit">edit</a>
  <p><?= $article->body ?>

<? endif ?>


<h1> All articles </h1>
<? foreach( Article::find('all') as $article): ?>
 <li><a href="<?= $_SERVER["PHP_SELF"]?>?article=<?=$article->id?>"><?= $article ?></a></li>
<? endforeach ?>
<hr>


<h1> All Question</h1>
<? foreach( Question::find('all') as $question): ?>
 <li><a href=""><?= $question ?></a></li>
<? endforeach ?>