<?php require $rootDir.'api/functionsForDB.php'; ?>

<?php // 処理状態確認
$currentIs = 'ENTRY'; // 初期値
if(isset($requestOption) && $requestOption == 'OUT') {
	$currentIs = 'OUT'; // 退会処理中
}
?>

<?php // 会員登録(入力確認で登録前のチェックは終わっているものとする)
	$newRegistIs = registCustomerInfo($_REQUEST); // 会員登録して、新規登録(true)/更新(false)を返す
	unset($_SESSION['fromEntryStoreCheck']); // SESSION内の更新情報破棄

	if($currentIs == 'OUT') {
		// 退会処理
		if( leaveStoreFunc($_SESSION['customer']['id']) ) {
			echo '処理完遂';
			// リダイレクト
			header('location: '.$rootDir.'logout.php?phase=Result&option=OUT');
		}
		else {
			echo 'エラー';
		}
		
	}
?>

<main>
	<section class="entry result">
		<div class="title">
			<h2>会員登録完了</h2>
		</div>
		<div class="messageWrap">
			<?php
				if($newRegistIs) {
					echo '<p>会員登録が完了しました。</p>';
					echo '<p>ログインページへお進みください。</p>';
					echo '<div class="btnWrap">';
					echo '    <a class="commonBtnSetting" href="'.$rootDir.'login.php?phase=Entry">ログインする</a>';
					echo '</div>';
				}
				else {
					echo '<p>会員登録を更新しました。</p>';
					echo '<p>引き続きお楽しみください。</p>';
				}
			?>
		</div>
		<div class="linkWrap">
			<div><a href="<?php echo $rootDir.'entryCard.php?phase=Entry'; ?>">クレジットカード登録へすすむ</a></div>
			<div><a href="<?php echo $rootDir.'viewCart.php'; ?>">購入確認ページへすすむ</a></div>
		</div>
	</section>
</main>