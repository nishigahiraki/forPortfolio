<?php // 初期化
	$nameList = [
				  'id' => 'id',
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
	foreach($keyList as $key) {
		$customer[$key] = null; // 入力値
		$NGList[$key] = 0; // NG判定
	};
?>
<?php // 各入力情報の更新
	$refDataset = []; // 入力データの参照先
	if( isset($_SESSION['customer']) ) { 
		$refDataset = $_SESSION['customer'];
	}
	if( isset($_SESSION['fromEntryStoreCheck'][2]) ) {
		$existCustomerIs = $_SESSION['fromEntryStoreCheck'][0];
		$NGList = $_SESSION['fromEntryStoreCheck'][1];
		$refDataset = $_SESSION['fromEntryStoreCheck'][2];
	}
	//
	foreach($keyList as $key) {
		if( isset($refDataset[$key]) ) {
			$customer[$key] = $refDataset[$key]; // 入力値
		}
		else {
			$customer[$key] = $refDataset[$key] = null;
		}
	}
	// もどるボタン、住所検索からの繊維遷移
	//var_dump($_REQUEST);
	if(!empty($_REQUEST['name'])) { 
		$customer['name'] = htmlspecialchars($_REQUEST['name']); }
	if(!empty($_REQUEST['furigana'])) { 
		$customer['furigana'] = htmlspecialchars(mb_convert_kana($_REQUEST['furigana'],'asKCV')); }
	if(!empty($_REQUEST['postcode_a'])) { 
		$customer['postcode_a'] = htmlspecialchars(mb_convert_kana($_REQUEST['postcode_a'],'a')); }
	if(!empty($_REQUEST['postcode_b'])) { 
		$customer['postcode_b'] = htmlspecialchars(mb_convert_kana($_REQUEST['postcode_b'],'a')); }
	if(!empty($_REQUEST['address'])) { 
		$customer['address'] = htmlspecialchars(mb_convert_kana($_REQUEST['address'],'asKV')); }
	if(!empty($_REQUEST['mail'])) { 
		$customer['mail'] = htmlspecialchars(($_REQUEST['mail'])); }
	if(!empty($_REQUEST['mailForCheck'])) { 
		$customer['mailForCheck'] = htmlspecialchars(mb_convert_kana($_REQUEST['mailForCheck'],'as')); }
	if(!empty($_REQUEST['password'])) { 
		$customer['password'] = htmlspecialchars(mb_convert_kana($_REQUEST['password'],'as')); }
	if(!empty($_REQUEST['passwordForCheck'])) { 
		$customer['passwordForCheck'] = htmlspecialchars(mb_convert_kana($_REQUEST['passwordForCheck'],'as')); }
?>
<main>
	<section class="entry">
		<div class="title">
			<h2>会員登録</h2>
		</div>
		<?php
		if( (isset($existCustomerIs) && $existCustomerIs) ||
			(isset($NGList) && array_sum($NGList) > 0) ) {
			$messages = [];
			if( array_sum($NGList) > 0 ) {
				$messages[] = '入力データに不備がございます。';
				$messages[] = 'ご確認をお願いいたします。';
			}
			if($existCustomerIs) {
				if(array_sum($NGList) > 0) { $messages[] = '&nbsp;'; } // スペース
				$messages[] = 'メールアドレスとパスワードの組み合わせが既に使用されています。';
				$messages[] = '変更をお願いいたします。';

				// 印つけ
				$NGList['mail'] = 1;
				$NGList['mailForCheck'] = 1;
				$NGList['password'] = 1;
				$NGList['passwordForCheck'] = 1;
			}
			$messageSet =[
				'parentSection' => '',
				'messages' => $messages,
				'forBtnWrap' => [],
				'forLinkWraps' => []
			];
			require $rootDir.'api/functions.php';
			viewErrorMes($messageSet);
		}
		?>
		<!--?php
		if( isset($_SESSION['fromEntryStoreCheck']) ) {
			$existCustomerIs = $_SESSION['fromEntryStoreCheck'][0];
			$NGList = $_SESSION['fromEntryStoreCheck'][1];
			if($existCustomerIs || array_sum($NGList) > 0 ) {
				echo '<section class="errorView">';
				if($existCustomerIs) {
					echo '<p>メールアドレスとパスワードの組み合わせが既に使用されています。</p>';
					echo '<p>変更をお願いいたします。</p>';
				}
				if( array_sum($NGList) > 0 ) {
					echo '<p>入力データに不備がございます。</p>';
					echo '<p>ご確認をお願いいたします。</p>';
					// print_r($NGList);
				}
				echo '</section>';
			}
		}
		?-->
		<form method="post" novalidate>
			<label>
				<span>お名前</span>
				<span>
				<input
					type="text" 
					class = "<?php if($NGList['name'] == 1) {echo 'error';} ?>"
					name="name"
					value="<?php echo $customer['name']; ?>"
					placeholder="ドーナツ太郎"
					required />
				</span>
			</label>
			<label>
				<span>お名前（フリガナ）</span>
				<span>
				<input
					type="text" 
					class = "<?php if($NGList['furigana'] == 1) {echo 'error';} ?>"
					name="furigana"
					value="<?php echo $customer['furigana']; ?>"
					placeholder="ドーナツタロウ"
					required />
				</span>
			</label>
			<label>
				<span>郵便番号</span>
				<span>
					<input
						type="text" 
						class = "<?php if($NGList['postcode_a'] == 1) {echo 'error';} ?>"
						name="postcode_a"
						value="<?php echo $customer['postcode_a']; ?>"
						placeholder="123"
						required />
				</span>
				<span>-</span>
				<span>
					<input
						type="text" 
						class = "<?php if($NGList['postcode_b'] == 1) {echo 'error';} ?>"
						name="postcode_b"
						value="<?php echo $customer['postcode_b']; ?>"
						placeholder="4567"
						required />
				</span>
					<input
						type="submit"
						formaction="<?php echo $rootDir.'searchPostcode.php'; ?>"
						value="住所検索" />
			</label>
			<label>
				<span>住所</span>
				<span>
				<input
					type="text" 
					class = "<?php if($NGList['address'] == 1) {echo 'error';} ?>"
					name="address"
					value="<?php echo $customer['address']; ?>"
					placeholder="千葉県〇〇市中央1-1-1"
					required />
				</span>
			</label>
			<label>
				<span>メールアドレス</span>
				<span>
				<input
					type="text" 
					class = "<?php if($NGList['mail'] == 1) {echo 'error';} ?>"
					name="mail"
					value="<?php echo $customer['mail']; ?>"
					placeholder="123@gmail.com"
					required />
				</span>
			</label>
			<label>
				<span>メールアドレス確認用</span>
				<span>
				<input
					type="text" 
					class = "<?php if($NGList['mailForCheck'] == 1) {echo 'error';} ?>"
					name="mailForCheck"
					value="<?php echo $customer['mailForCheck']; ?>"
					placeholder="123@gmail.com"
					required />
				</span>
			</label>
			<label>
				<span>パスワード</span>
				<span class="caution">半角英数字8文字以上20文字以内で入力してください。※記号の使用はできません</span>
				<span>
				<input
					type="text" 
					class = "<?php if($NGList['password'] == 1) {echo 'error';} ?>"
					name="password"
					value="<?php echo $customer['password']; ?>"
					placeholder="123456abcd"
					required />
				</span>
			</label>
			<label>
				<span>パスワード確認用</span>
				<span>
				<input
					type="text" 
					class = "<?php if($NGList['passwordForCheck'] == 1) {echo 'error';} ?>"
					name="passwordForCheck"
					value="<?php echo $customer['passwordForCheck']; ?>"
					placeholder="123456abcd"
					required />
				</span>
			</label>
			<div class="btnWrap">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input class="commonBtnSetting out <?php if(!isset($_SESSION['customer'])) {echo 'displayNone';}?>" 
					formaction="<?php echo $rootDir.'entryStore.php?phase=Check&option=OUT'; ?>"
					type="submit" 
					value="退会する" />
				<input class="commonBtnSetting"
					formaction="<?php echo $rootDir.'entryStore.php?phase=Check'; ?>"
					type="submit"
					value="入力確認する" />
			</div>
		</form>
		<div class="linkWrap"></div>
	</section>
</main>