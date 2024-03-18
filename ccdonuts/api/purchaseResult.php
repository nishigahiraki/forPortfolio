<?php require $rootDir.'api/functions.php'; ?>
<?php require $rootDir.'api/functionsForDB.php'; ?>
<?php
	//var_dump($_REQUEST);
	// セキュリティコードの確認
	if( isset($_REQUEST['card_id']) ){
		$requestCardId = $_REQUEST['card_id'];
	}
	else { $requestCardId = null; }
	if( isset($_REQUEST['code']) ) {
		$requestCode = $_REQUEST['code'];
	}
	else { $requestCode = null; }
	//
	if( codeCheck($requestCardId, $requestCode) ) {
		// 購入処理
		$purchaseInfo = [
			'customer_id' => $_SESSION['customer']['id'],
			'card_id' => $_REQUEST['card_id'],
			'cartInfo' => $_SESSION['cart'],
		];
		if( $flg = excutePurchase( $purchaseInfo ) ) {
			// 購入処理がうまくいっていれば...
			unset($_SESSION['cart']);
		}
	}
	else {
		// セキュリティコードの不整合
		//echo 'id code check';
		$flg = false;
	}
?>

<main>
	<section class="purchase result">
	<?php
	if( $flg ) {
		print<<<_HTML_
			<div class="title">
				<h2>ご購入完了</h2>
			</div>
			<!--div class="messageWrap">
				<p>ご購入いただきありがとうございます。</p>
				<p>今後ともご愛顧の程、宜しくお願いいたします。</p>
			</div>
			<div class="linkWrap">
				<div><a href="{$rootDir}index.php">TOPページへすすむ</a></div>
			</div-->
		_HTML_;
		$messageSet =[
			'parentSection' => 'infomation',
			'messages' => [
				'ご購入いただきありがとうございます。',
				'今後ともご愛顧の程、宜しくお願いいたします。',
			],
			'forBtnWrap' => [
				'TOPページへすすむ', $rootDir.'index.php"',
			],
			'forLinkWraps' => [ ['', ''] ],
		];
		viewErrorMes($messageSet);
	}
	else {
		print<<<_HTML_
			<div class="title">
				<h2>ご購入処理失敗</h2>
			</div>
		_HTML_;
		$messageSet =[
			'parentSection' => 'caution',
			'messages' => [
				'購入処理に失敗しました。',
				'お支払い方法をご確認ください。',
			],
			'forBtnWrap' => [
				'購入確認へもどる', $rootDir.'purchase.php?phase=Entry',
			],
			'forLinkWraps' => [ ['', ''] ],
		];
		viewErrorMes($messageSet);
	}
	?>
	</section>
</main>