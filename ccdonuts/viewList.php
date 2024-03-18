<?php session_start(); ?>

<?php // ページ情報
	$pageTitle = 'C.C.Donuts | 商品一覧';
	$rootDir = './';
	$breadInfo = [
		['name' => 'TOP', 'url' => $rootDir.'index.php'],
		['name' => '商品一覧', 'url' => $rootDir.'viewList.php?view=all'],
	];
?>

<?php require $rootDir.'common/header.php'; ?>
<?php
	switch( $_REQUEST['view'] ?? 'all' ) {
		case 'search' :
			$searchWord = htmlspecialchars($_REQUEST['keyword']);
			require $rootDir.'api/showSearchProduct.php';
			break;
		case 'all' :
		default :
			require $rootDir.'api/showAllProducts.php';
			break;
	}
?>
<?php require $rootDir.'common/footer.php'; ?>

