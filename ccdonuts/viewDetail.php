<?php session_start(); ?>

<?php // 表示に使う値なので、処理
	$searchName = htmlspecialchars( $_REQUEST['name'] );
?>

<?php // ページ情報
	$pageTitle = 'C.C.Donuts | '.$searchName;
	$rootDir = './';
	$breadInfo = [
		['name' => 'TOP', 'url' => $rootDir.'index.php'],
		['name' => '商品一覧', 'url' => $rootDir.'viewList.php?view=all'],
		['name' => $searchName, 'url' => $rootDir.'viewDetail.php?name='.$searchName],
	];
?>

<?php require $rootDir.'common/header.php'; ?>
<?php
	require $rootDir.'api/showDetail.php';
?>
<?php require $rootDir.'common/footer.php'; ?>