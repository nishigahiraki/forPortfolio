<?php session_start(); ?>

<?php // 表示に使う値なので、処理
	$requestPhase = htmlspecialchars( $_REQUEST['phase'] ) ?? 'Entry';
?>

<?php // ページ情報
	$rootDir = './';
	$pageTitleList = [
		'Entry' => 'ログイン',
		'Result' => 'ログイン処理結果',
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
			// ログイン処理
			require $rootDir.'api/functionsForDB.php';
			excuteLogin([
				'mail' => $_REQUEST['email'],
				'password' => $_REQUEST['password'],
			]);
			array_unshift($breadInfo,
				['name' => 'ログイン処理結果', 'url' => $rootDir.'login.php?phase=Result']);
		case 'Entry' :
			array_unshift($breadInfo,
				['name' => 'ログイン', 'url' => $rootDir.'login.php?phase=Entry']);
		default :
			array_unshift($breadInfo,
				['name' => 'TOP', 'url' => $rootDir.'index.php']);
	}
?>

<?php require $rootDir.'common/header.php'; ?>
<?php require $rootDir.'api/login'.$requestPhase.'.php'; ?>
<?php require $rootDir.'common/footer.php'; ?>
