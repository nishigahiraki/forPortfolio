<?php session_start(); ?>

<?php // 表示に使う値なので、処理
	$requestPhase = htmlspecialchars( $_REQUEST['phase'] ) ?? 'Entry';
	if( isset($_REQUEST['option']) ) {
		$requestOption = htmlspecialchars( $_REQUEST['option'] ) ?? '';
	}
?>

<?php // ページ情報
	$rootDir = './';
	$pageTitleList = [
		'Entry' => '会員登録',
		'Check' => '入力確認',
		'Result' => '会員登録完了',
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
				['name' => '会員登録完了', 'url' => $rootDir.'entryStore.php?phase=Result']);
		case 'Check' :
			array_unshift($breadInfo,
				['name' => '入力確認', 'url' => $rootDir.'entryStore.php?phase=Check']);
		case 'Entry' :
			array_unshift($breadInfo,
				['name' => '会員登録', 'url' => $rootDir.'entryStore.php?phase=Entry']);
		default :
			array_unshift($breadInfo,
				['name' => 'ログイン', 'url' => $rootDir.'login.php?phase=Entry']);
			array_unshift($breadInfo,
				['name' => 'TOP', 'url' => $rootDir.'index.php']);
	}
?>

<?php require $rootDir.'common/header.php'; ?>
<?php require $rootDir.'api/entryStore'.$requestPhase.'.php'; ?>
<?php require $rootDir.'common/footer.php'; ?>