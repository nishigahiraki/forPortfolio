<?php // カード情報の有無確認
	// 初期化
	$NGList = [
				'name' => 0,
			  'number' => 0,
			 'company' => 0,
		  'limit_year' => 0,
		 'limit_month' => 0,
				'code' => 0,
	];
	$keyList = array_keys($NGList);
	foreach($keyList as $key) {
		$cardInfo[$key] = null; // 入力値
		$NGList[$key] = 0; // NG判定
		$refDataset[$key] = null;
	};
?>
<?php // 各入力情報の更新
	if( $requestOption != 'ADD' ) { 
		// 追加(ADD)の時以外は、選択したカード情報を入れておく
		if(!empty($requestCardId)) {
			$refDataset = getCardInfoByCardId($requestCardId);
		}
	}
	if( !empty($_SESSION['fromEntryCardCheck']) ) { 
		$canUseCard = $_SESSION['fromEntryCardCheck'][0]; // カードが有効か
		$NGList = $_SESSION['fromEntryCardCheck'][1]; // 入力不備のリスト
		$refDataset = $_SESSION['fromEntryCardCheck'][2]; // データの引き継ぎ
	}
	//
	$cardInfo = $refDataset; // 入力値
?>

<!-- <pre>
<?php //var_dump($cardInfo); ?>
</pre> --> 

<main>
	<section class="entry">
		<div class="title">
			<h2>カード情報登録</h2>
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
		<?php
		if( (isset($NGList) && array_sum($NGList) > 0) ) {
			$codeIs = $_SESSION['fromEntryCardCheck'][0];
			$NGList = $_SESSION['fromEntryCardCheck'][1];

			$messages = [];
			if( $NGList['number'] != 0 ) {
				$messages[] = 'カード番号は数字で入力してください。';
			}
			if( $NGList['company'] != 0 ) {
				$messages[] = 'カード会社を選択してください。';
			}
			if( $NGList['limit_month'] != 0 || $NGList['limit_year'] != 0 ) {
				$messages[] = '有効期限に不備がございます。';
			}
			if( array_sum($NGList) > 0 ) {
				$messages[] = 'ご確認をお願いいたします。';
			}
			if( !$codeIs ) {
				if(array_sum($NGList) > 0) { $messages[] = '&nbsp;'; } // スペース
				$messages[] = 'カード情報に不備があります。';
				$messages[] = 'ご確認をお願いいたします。';

				// 印つけ
				$NGList['name'] = 1;
				$NGList['number'] = 1;
				$NGList['company'] = 1;
				$NGList['limit_month'] = 1;
				$NGList['limit_year'] = 1;
				$NGList['code'] = 1;
			}
			$messageSet =[
				'parentSection' => '',
				'messages' => $messages,
				'forBtnWrap' => [],
				'forLinkWraps' => []
			];
			viewErrorMes($messageSet);
		}
		?>
		<form method="post">
			<label>
				<span>お名前</span>
				<span>
				<input
					type="text"
					class = "<?php if($NGList['name'] == 1) {echo 'error';} ?>"
					name="name"
					value="<?php echo $cardInfo['name']; ?>"
					placeholder="ドーナツ太郎"
					required />
				</span>
			</label>
			<label>
				<span>カード番号</span>
				<span>
				<input
					type="text"
					class = "<?php if($NGList['number'] == 1) {echo 'error';} ?>"
					name="number"
					value="<?php echo $cardInfo['number'] ?? ''; ?>"
					placeholder="123456789123"
					required />
				</span>
			</label>
			<label>
				<span class="card_company entry <?php if($NGList['company'] == 1) {echo 'error';} ?>">カード会社</span>
				<div>
					<label for="cc1radio">
						<input
							type="radio" 
							id="cc1radio"
							name="company"
							value="0"
							<?php if($cardInfo['company'] == 0 && isset($cardInfo['company'])) { echo 'checked'; } ?>
							/>
						<span class="radio cc1"></span>
						<span>JCB</span>
					</label>
					<label for="cc2radio">
						<input
							type="radio" 
							id="cc2radio"
							name="company"
							value="1"
							<?php if($cardInfo['company'] == 1) { echo 'checked'; } ?>
							/>
						<span class="radio cc2"></span>
						<span>Visa</span>
					</label>
					<label for="cc3radio">
						<input
							type="radio" 
							id="cc3radio"
							name="company"
							value="2"
							<?php if($cardInfo['company'] == 2) { echo 'checked'; } ?>
							/>
						<span class="radio cc3"></span>
						<span>Mastercard</span>
					</label>
				</div>
			</label>
			<label>
				<span>有効期限</span>
				<label>
					<span>
					<input
						type="text"
						class = "<?php if($NGList['limit_month'] == 1) {echo 'error';} ?>"
						name="limit_month"
						value="<?php echo $cardInfo['limit_month']; ?>"
						placeholder="4"
						min="1" max="12"
						required />
					</span>
					<span>月</span>
				</label>
				<label>
					<span>
					<input
						type="text"
						class = "<?php if($NGList['limit_year'] == 1) {echo 'error';} ?>"
						name="limit_year"
						value="<?php echo $cardInfo['limit_year']; ?>"
						placeholder="25"
						min="24" size="2"
						required />
					</span>
					<span>年</span>
				</label>
			</label>
			<label>
				<span>セキュリティコード</span>
				<span>
				<input
					type="text"
					class = "<?php if($NGList['code'] == 1) {echo 'error';} ?>"
					name="code"
					value="<?php echo $cardInfo['code']; ?>"
					placeholder="123456"
					required />
				</span>
			</label>
			<div class="btnWrap">
				<input type="hidden" name="card_id" value="<?php echo $requestCardId; ?>">
				<input class="commonBtnSetting" type="submit"
					formaction="<?php 
						echo $rootDir.'entryCard.php?phase=Check';
						if( isset($requestOption) ) { echo '&option='.$requestOption; }
					?>" 
					value="<?php
						switch( $requestOption ) {
							case 'DELETE' :
								echo 'カードを削除する';
							break;
							case 'ADD' :
								echo 'カードを追加する';
							break;
							case 'UPDATA' :
								echo '更新確認する';
							break;
							default :
								echo '入力確認する';
							break;
						}
					?>">
			</div>
		</form>
		<div class="linkWrap">
			<a href="<?php echo $rootDir.'purchase.php?phase=Entry'; ?>">購入確認に戻る</a>
		</div>
	</section>
</main>