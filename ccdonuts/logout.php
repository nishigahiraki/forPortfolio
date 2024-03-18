<?php session_start(); ?>

<?php // 表示に使う値なので、処理
	$requestPhase = htmlspecialchars( $_REQUEST['phase'] ) ?? 'Entry';
	if( isset($_REQUEST['option']) ) {
		$requestOption = htmlspecialchars( $_REQUEST['option'] );
	}
	else { $requestOption = null; }
?>

<?php // ページ情報
	$rootDir = './';
	$pageTitleList = [
		'Entry' => 'ログアウト',
		'Result' => 'ログアウト処理結果',
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
	switch( $requestPhase ) {
		case 'Result' :
			require $rootDir.'api/functionsForDB.php';
			excuteLogout(); // ログアウト処理
			array_unshift($breadInfo,
				['name' => 'ログアウト処理結果', 'url' => $rootDir.'logout.php?phase=Result']);
		case 'Entry' :
			array_unshift($breadInfo,
				['name' => 'ログアウト', 'url' => $rootDir.'logout.php?phase=Entry']);
		default :
			array_unshift($breadInfo,
				['name' => 'TOP', 'url' => $rootDir.'index.php']);
	}
?>

<?php require $rootDir.'common/header.php'; ?>
<?php require $rootDir.'api/logout'.$requestPhase.'.php'; ?>
<?php require $rootDir.'common/footer.php'; ?>