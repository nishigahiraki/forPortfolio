<?php session_start(); ?>

<?php // 表示に使う値なので、処理
	$requestPhase = htmlspecialchars( $_REQUEST['phase'] ) ?? 'Entry';
?>

<?php // ページ情報
	$rootDir = './';
	$pageTitleList = [
		'Entry' => 'ご購入確認',
		'Result' => 'ご購入完了',
	];
	// requestPhaseのチェック
	if( !array_key_exists($requestPhase, $pageTitleList) ) {
		// URLが気持ち悪いので、リダイレクトする。
		header('location: '.$rootDir.'login.php?phase=Entry'); // リダイレクト
		exit();
	}
	//
	$pageTitle = 'C.C.Donuts | '.$pageTitleList[$requestPhase];
	$breadInfo = [];
	switch( $_REQUEST['phase'] ) {
		case 'Result' :
			array_unshift($breadInfo,
				['name' => '購入完了', 'url' => $rootDir.'purchase.php?phase=Result']);
		case 'Entry' :
			array_unshift($breadInfo,
				['name' => '購入確認', 'url' => $rootDir.'purchase.php?phase=Entry']);
		default :
			array_unshift($breadInfo,
				['name' => 'カート', 'url' => $rootDir.'viewCart.php']);
			array_unshift($breadInfo,
				['name' => 'TOP', 'url' => $rootDir.'index.php']);
	}
?>

<?php // 引継ぎ
	if(isset($_SESSION['cart']) && isset($_SESSION['customer'])) {
		$yourCart = $_SESSION['cart'];
		$your = $_SESSION['customer'];
	}
?>

<?php require $rootDir.'common/header.php'; ?>
<?php
if( empty($_SESSION['customer']) ) {
	// ログイン前の状態
	$messageSet =[
		'parentSection' => 'purchase',
		'messages' => ['ログインしてください。',],
		'forBtnWrap' => ['ログインする', $rootDir.'login.php?phase=Entry'],
		'forLinkWraps' => [
			['TOPページへもどる', $rootDir.'index.php'],
		]
	];
	require $rootDir.'api/functions.php';
	viewErrorMes($messageSet);
}
else if( empty($_SESSION['cart']) ) {
	// カートが空 (表示されない想定)
	$messageSet =[
		'parentSection' => 'purchase',
		'messages' => ['カートは空です。',],
		'forBtnWrap' => ['商品一覧へ', $rootDir.'viewList.php'],
		'forLinkWraps' => [
			['TOPページへもどる', $rootDir.'index.php'],
		]
	];
	require $rootDir.'api/functions.php';
	viewErrorMes($messageSet);
}
else {
	require $rootDir.'api/purchase'.$requestPhase.'.php';
}
?>
<?php require $rootDir.'common/footer.php'; ?>