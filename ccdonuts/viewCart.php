<?php session_start(); ?>

<?php // ページ情報
$pageTitle = 'C.C.Donuts | カート';
$rootDir = './';
$breadInfo = [
	['name' => 'TOP', 'url' => $rootDir.'index.php'],
	['name' => 'カート', 'url' => $rootDir.'viewCart.php'],
];
?>

<?php require $rootDir.'common/header.php'; ?>
<?php require $rootDir.'api/showCart.php'; ?>
<?php require $rootDir.'common/footer.php'; ?>