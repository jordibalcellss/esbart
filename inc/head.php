<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ca" lang="ca">
	<head>
		<link rel="stylesheet" href="css/style-<?=MODE?>.css" media="screen,print" type="text/css" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<title><?=TITLE?></title>
	</head>
	<body>
		<div class="container_12">
			<div class="grid_12" id="cont">
				<div class="grid_3 omega" id="session">
					<span><?=greeting?>, <?=$_SESSION['id']?>! - <a href="login.php?action=logout"><?=logout?></a></span>
				</div>
			</div>
		</div>
		<div class="grid_10 prefix_1 suffix_1 alpha" id="trunk">
