<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<meta name="robots" content="noindex">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href=<?php echo $rootDir."common/reset.css"; ?>>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
		<link rel="stylesheet" href=<?php echo $rootDir."styles/style.css"; ?>>
		<title>
			<?php 
				if(!empty($pageTitle)) { echo $pageTitle; }
				else { echo 'noName';}
			?>
		</title>
		<style>
			<?php if(isset($newItemThumbUrl)) { ?>
				section.products div.panelWrap>div.panel1>a {
					background: url(<?php echo $newItemThumbUrl.'PC.png'; ?>) no-repeat 50%/cover;
				}
				@media screen and (max-width:768px) {
						section.products div.panelWrap>div.panel1>a {
						background: url(<?php echo $newItemThumbUrl.'SP.png'; ?>) no-repeat 50%/cover;
					}
				}
			<?php } ?>
		</style>
	</head>
	<body>
		<header>
			<div class="logo">
				<h1>
					<img src=<?php echo $rootDir."images/COMMON/ccdounutsLogo.svg"; ?> alt="ccdonutsロゴ">
				</h1>
			</div>
			<nav class="onHeader">
				<?php
					if(isset($_SESSION['customer'])) {
						echo '<a href="'.$rootDir.'logout.php?phase=Entry">';
						echo '	<div><img src="'.$rootDir.'images/COMMON/logoutIcon.svg"; ?></div>';
						echo '	<span>ログアウト</span>';
						echo '</a>';
					}
					else {
						echo '<a href="'.$rootDir.'login.php?phase=Entry">';
						echo '	<div><img src="'.$rootDir.'images/COMMON/loginIcon.svg"; ?></div>';
						echo '	<span>ログイン</span>';
						echo '</a>';
					}
				?>
				<a href=<?php echo $rootDir."viewCart.php"; ?>>
					<div><img src=<?php echo $rootDir."images/COMMON/cartIcon.svg"; ?>></div>
					<span>カート</span>
				</a>
			</nav>
			<input type="checkbox" id="headerNaviCheckBox"> <!-- ドロワ制御用 -->
			<label for="headerNaviCheckBox" class="navMenuLabel">
				<div class="navMenuIcon">
				<div class="menuBar"></div>
				</div>
			</label>
			<nav class="drawer">
				<div class="logo">
					<img src=<?php echo $rootDir."images/COMMON/ccdounutsLogo.svg"; ?> alt="ccdonutsロゴ">
				</div>
				<ul>
					<li><a href=<?php echo $rootDir."index.php"; ?>>TOP</a></li>
					<li><a href=<?php echo $rootDir."viewList.php?view=all"; ?>>商品一覧</a></li>
					<li><a href="">よくある質問</a></li>
					<li><a href="">問い合わせ</a></li>
					<li><a href="">当サイトのポリシー</a></li>
				</ul>
				<label for="headerNaviCheckBox">
					<div class="closeBtn"></div>
				</label>
			</nav>
			<div class="searchLine">
				<form action=<?php echo $rootDir."viewList.php"; ?> method="get">
					<div class="searchIcon">
						<input type="submit" value="">
						<img src=<?php echo $rootDir."images/COMMON/searchIcon.svg"; ?>>
					</div>
					<input type="hidden" name="view" value="search">
					<input type="search" name="keyword" placeholder="商品名で検索...">
				</form>
			</div>
			<?php
				if(!empty($breadInfo)) {
					echo '<div class="breadNav">';
					$count = -1;
					foreach( $breadInfo as $row) {
						$count++;
						if($count > 0 ) {
							echo ' > ';
						}
						echo '<a href="'.$row['url'].'">'.$row['name'].'</a>';
					}
					echo '</div>';
				}
			?>
			<p>ようこそ <a href="entryStore.php?phase=Entry">
				<?php
					if(empty($_SESSION['customer'])) {
						echo 'ゲスト';
					}
					else {
						echo $_SESSION['customer']['name'];
					}
				?></a> 様</p>
		</header>