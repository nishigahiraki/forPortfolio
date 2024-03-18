<pre>
<?php //var_dump($_GET); ?>
<?php //var_dump($_POST); ?>
<?php //echo $requestOption; ?>
</pre>
<?php // カード登録(入力確認で登録前のチェックは終わっているものとする)
	//echo $_SERVER['REQUEST_METHOD'];
	if($_SERVER['REQUEST_METHOD']=='POST') { // リダイレクトされていると'GET'になる
		//
		// optionで処理を分ける
		if( $requestOption == 'DELETE' ) {
			deleteCardInfo($_POST);
		}
		else {
			$_POST['customer_id'] = $_SESSION['customer']['id'];
			$registIs = registCardInfo($_POST, $requestOption); // 情報をDBに登録
			unset($_SESSION['fromEntryCardCheck']); // SESSION内の更新情報破棄
		}
		//
		header('location: '.$rootDir.'entryCard.php?phase=Result&option='.$requestOption); // リダイレクト
		exit();
	}
?>

<main>
	<section class="entry result">
		<div class="title">
			<h2>カード情報登録完了</h2>
		</div>
		<?php
			$messageSet =[
				'parentSection' => 'warning',
				'messages' => [
					'当サイトは模擬サイトですので、',
					'実際のクレジットカード情報は登録しないでください',
				],
				'forBtnWrap' => [],
				'forLinkWraps' => []
			];
			viewErrorMes($messageSet);
		?>
		<div class="messageWrap">
			<p>支払い情報登録が完了しました。</p>
			<p>続けて購入確認ページへお進みください。</p>
		</div>
		<div class="linkWrap">
			<div><a href=<?php echo $rootDir.'purchase.php?phase=Entry'; ?>>購入確認ページへすすむ</a></div>
		</div>
	</section>
</main>