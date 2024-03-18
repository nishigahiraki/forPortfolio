<?php require $rootDir.'api/functions.php'; ?>
<?php // 入力の不備をチェック
	// 値の抽出と合わせて整形
	$customer = [
				  'id' => htmlspecialchars($_REQUEST['id']),
				'name' => htmlspecialchars($_REQUEST['name']),
			'furigana' => htmlspecialchars(mb_convert_kana($_REQUEST['furigana'],'asKCV')),
		  'postcode_a' => htmlspecialchars(mb_convert_kana($_REQUEST['postcode_a'],'a')),
		  'postcode_b' => htmlspecialchars(mb_convert_kana($_REQUEST['postcode_b'],'a')),
			 'address' => htmlspecialchars(mb_convert_kana($_REQUEST['address'],'asKV')),
				'mail' => htmlspecialchars(($_REQUEST['mail'])),
		'mailForCheck' => htmlspecialchars(mb_convert_kana($_REQUEST['mailForCheck'],'as')),
			'password' => htmlspecialchars(mb_convert_kana($_REQUEST['password'],'as')),
	'passwordForCheck' => htmlspecialchars(mb_convert_kana($_REQUEST['passwordForCheck'],'as')),
	];
?>
<?php
	// 諸々不備をチェックする
	$nameList = [
				'name' => 'お名前',
			'furigana' => 'お名前（フリガナ）',
		  'postcode_a' => '郵便番号',
		  'postcode_b' => '郵便番号',
			 'address' => '住所',
				'mail' => 'メールアドレス',
		'mailForCheck' => 'メールアドレス確認用',
			'password' => 'パスワード',
	'passwordForCheck' => 'パスワード確認用',
	];
	$keyList = array_keys($nameList);
	//
	foreach( $keyList as $key ) {
		if( !isset($customer[$key]) ) {
			$NGList[$key] = 1;
		}
		else {
			$NGList[$key] = 0;
		}
	}
	//
	// 郵便番号のチェック
	if( !preg_match('/^[0-9]{3}$/', $customer['postcode_a']) ) {
		$NGList['postcode_a'] = 1;
	}
	if( !preg_match('/^[0-9]{4}$/', $customer['postcode_b']) ) {
		$NGList['postcode_b'] = 1;
	}
	// メールアドレスのチェック
	//echo $customer['mail'];
	if( !preg_match('/^[a-zA-Z0-9\-\_\.]+@[a-zA-Z0-9\-\_\.]+$/', $customer['mail']) ) {
		$NGList['mail'] = 1;
		echo 'is error';
	}
	// 確認用メールアドレスのチェック
	if( $customer['mail'] !== $customer['mailForCheck'] ) {
		$NGList['mail'] = 1;
		$NGList['mailForCheck'] = 1;
	}
	// パスワードのチェック
	if( !preg_match('/^[a-zA-Z0-9]{8,20}$/', $customer['password']) ) {
		$NGList['password'] = 1;
	}
	// 確認用パスワードのチェック
	if( $customer['password'] !== $customer['passwordForCheck'] ) {
		$NGList['password'] = 1;
		$NGList['passwordForCheck'] = 1;
	}
?>
<?php // 対象の情報が登録されているかチェック
	require $rootDir.'api/functionsForDB.php';
	if(isset($_SESSION['customer'])) {
		$existCustomerIs = false; // ログインしていれば、存在判定をしない
	}
	else {
		$existCustomerIs = existCustomerCheck( $customer );
	}
?>
<?php // 入力に不備があった場合リダイレクトする
	unset($_SESSION['fromEntryStoreCheck']);
	$_SESSION['fromEntryStoreCheck'] = [$existCustomerIs, $NGList, $customer];
	
	if($existCustomerIs || array_sum($NGList) > 0 ) {
		header('location: '.$rootDir.'entryStore.php?phase=Entry'); // リダイレクト
		exit();
	}
?>
<?php // 処理状態確認
$currentIs = 'ENTRY'; // 初期値
if(isset($requestOption) && $requestOption == 'OUT') {
	$currentIs = 'OUT'; // 退会処理中
}
?>
<!--?php // テスト用に強制的に判定を通過する設定
	$existCustomerIs = false;
	$NGList=[0,0];
?-->

<main>
	<section class="entry">
		<div class="title">
			<h2>入力確認</h2>
		</div>
		<?php
			if($currentIs == 'OUT') {
				$messageSet =[
					'parentSection' => 'warning',
					'messages' => [
						'退会処理を実行します。',
						'下記の内容とクレジットカードの登録を無効にします。',
					],
					'forBtnWrap' => [],
					'forLinkWraps' => []
				];
				viewErrorMes($messageSet);
			}
		?>
		<form method="post">
			<?php // 入力確認表示
				foreach($keyList as $key) {
					if($key != 'postcode_b') {
						echo '<label>';
						echo '	<span>'.$nameList[$key].'</span>';
					}
					if($NGList[$key]){
						echo '	<span class="fromInput '.$key.' error">';
					}
					else {
						echo '	<span class="fromInput '.$key.'">';
					}
						echo '<input type="hidden" name= "NGList['.$key.']" value="'.$NGList[$key].'">';
						echo '<input type="hidden" name="'.$key.'" value="'.$customer[$key].'">';
						echo $customer[$key];
						echo '</span>';
					if($key == 'postcode_a') { echo '<span>-</span>'; }
					if($key != 'postcode_a') {
						echo '</label>';
					}
				}
			?>
			<div class="btnWrap">
				<input type="hidden" name='id' value=<?php echo $customer['id']; ?>>
				<!--input type="hidden" name='backIs' value="true"-->
				<input class="commonBtnSetting <?php if($currentIs == 'OUT') { echo 'displayNone'; }?>" 
						type="submit"
						formaction="<?php echo $rootDir.'entryStore.php?phase=Entry'; ?>"
						value="入力内容を修正する">
				<input class="commonBtnSetting" 
						type="submit"
						formaction="<?php echo $rootDir.'entryStore.php?phase=Result&option='.$currentIs; ?>"
						value="<?php
							if(isset($_SESSION['customer'])) { 
								if($currentIs == 'OUT') { echo '退会する'; }
								else { echo '上記の内容で更新する'; }
							}
							else { echo '登録する'; }
						?>">
			</div>

		</form>
		<div class="linkWrap">
			<?php
			if($currentIs == 'OUT') {
				echo '<div><a href="'.$rootDir.'index.php">TOPへもどる</a></div>';
			}
			?>
		</div>
    </section>
</main>