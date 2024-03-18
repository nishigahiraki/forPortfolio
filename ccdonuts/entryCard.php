<?php session_start(); ?>

<?php // 表示に使う値なので、処理
	$requestPhase = htmlspecialchars( $_REQUEST['phase'] ) ?? 'Entry';
	if( isset($_REQUEST['option']) ) {
		$requestOption = htmlspecialchars( $_REQUEST['option'] );
	}
	else { $requestOption = null; }
	if( isset($_REQUEST['card_id']) ) {
		$requestCardId = htmlspecialchars( $_REQUEST['card_id'] );
	}
	else { $requestCardId = null; }
	//echo 'requestCardID:::'.$_REQUEST['card_id'];
?>

<?php // ページ情報
	$rootDir = './';
	$pageTitleList = [
		'Entry' => 'カード情報',
		'Check' => '情報確認',
		'Result' => '登録完了',
	];
	// requestPhaseのチェック
	if( !array_key_exists($requestPhase, $pageTitleList) ) {
		// URLが気持ち悪いので、リダイレクトする。
		header('location: '.$rootDir.'login.php?phase=Entry'); // リダイレクト
		exit();
	}
	$pageTitle = 'C.C.Donuts | '.$pageTitleList[$requestPhase];
	$breadInfo = [];
	switch( $requestPhase ) {
		case 'Result' :
			array_unshift($breadInfo,
				['name' => '登録完了', 'url' => $rootDir.'entryCard.php?phase=Result']);
		case 'Check' :
			array_unshift($breadInfo,
				['name' => '情報確認', 'url' => $rootDir.'entryCard.php?phase=Check']);
		case 'Entry' :
			array_unshift($breadInfo,
				['name' => 'カード情報', 'url' => $rootDir.'entryCard.php?phase=Entry']);
		default :
			array_unshift($breadInfo,
				['name' => '購入確認', 'url' => $rootDir.'purchase.php?phase=Entry']);
			array_unshift($breadInfo,
				['name' => 'カート', 'url' => $rootDir.'viewCart.php']);
			array_unshift($breadInfo,
				['name' => 'TOP', 'url' => $rootDir.'index.php']);
	}
?>

<?php
	require $rootDir.'api/functions.php';
	require $rootDir.'api/functionsForDB.php';
?>

<?php require $rootDir.'common/header.php'; ?>
<?php require $rootDir.'api/entryCard'.$requestPhase.'.php'; ?>
<?php require $rootDir.'common/footer.php'; ?>