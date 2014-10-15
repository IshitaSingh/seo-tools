<!DOCTYPE html>
<html lang="<?=Rum::app()->lang?>" >
<head>
<meta charset="<?=Rum::app()->charset?>" />
<title><?=$title?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<script type="text/javascript" src="<?=\Rum::baseURI()?>/res/jquery/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="<?=\Rum::baseURI()?>/res/jquery/jquery.cookie.js"></script>
	<script type="text/javascript" src="<?=\Rum::baseURI()?>/res/jquery/ui/jquery-ui.min.js"></script>

	<link rel="stylesheet" type="text/css" href="<?=\Rum::baseURI()?>/res/jquery/ui/jquery-ui.min.css" media="screen" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
</head>
<body>

<div id="doc">
	<?php if(\Rum::messages()->count > 0) : ?>
	<?php foreach(\Rum::messages() as $message) : ?>
	<?=$message->message?>
	<?php endforeach; ?>
	<?php endif; ?>
	<?php $this->content() ?>
</div>

</body>
</html>
