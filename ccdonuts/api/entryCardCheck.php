<?php // 入力の不備をチェック
	if( $requestOption == 'DELETE' ) {  // DELETEで来ている場合
		$cardInfo = getCardInfoByCardId($requestCardId); // 既存カードデータ
	}
	else{
		// 値の抽出と合わせて整形
		$cardInfo = [
			'customer_id' => htmlspecialchars($_SESSION['customer']['id']),
			'name' => htmlspecialchars($_REQUEST['name']),
			'number' => htmlspecialchars(mb_convert_kana($_REQUEST['number'],'a')),
			'company' => htmlspecialchars($_REQUEST['company']),
			'limit_year' => htmlspecialchars(mb_convert_kana($_REQUEST['limit_year'],'a')),
			'limit_month' => htmlspecialchars(mb_convert_kana($_REQUEST['limit_month'],'a')),
			'code' => htmlspecialchars($_REQUEST['code']),
		];
	}
?>
<?php // 諸々不備をチェックする
	// 初期化
	$nameList = [
				'name' => 'お名前',
			  'number' => 'カード番号',
			 'company' => 'カード会社',
		  'limit_year' => '月',
		 'limit_month' => '年',
			    'code' => 'セキュリティコード',
	];
	$keyList = array_keys($nameList);
	//
	// 入力がなかったら、NGとする
	foreach( $keyList as $key ) {
		if( !isset($cardInfo[$key]) ) {
			$NGList[$key] = 1;
		}
		else {
			$NGList[$key] = 0;
		}
	}
	//
	// カード番号
	if( !preg_match('/^[0-9]+$/', $cardInfo['number']) ) {
		$NGList['number'] = 1;
	}
	// 年月check
	// 2桁にする
	$cardInfo['limit_month'] = sprintf('%02d', $cardInfo['limit_month']);
	$cardInfo['limit_year'] = sprintf('%02d', $cardInfo['limit_year']);
	// 単純に数字の範囲チェックのみでOKかも...
	$NGList['limit_month'] = 1;
	if( 1 <= $cardInfo['limit_month'] && $cardInfo['limit_month'] <= 12 ) {
		$NGList['limit_month'] = 0;
	}
	else { echo 'monthError:::'.$cardInfo['limit_month']; }
	$NGList['limit_year'] = 1;
	if( $cardInfo['limit_year'] >= 24 ) { // とりあえず24年以降を対象に
		$NGList['limit_year'] = 0;
	}
	else { echo 'monthError:::'.$cardInfo['limit_year']; }

	// 有効期限のチェック
	date_default_timezone_set('Japan');
	$nowYM = date('ym');
	$cardYM = $cardInfo['limit_year'].$cardInfo['limit_month'];
	// echo 'nowYM:::'.$nowYM.'<br>';
	// echo 'cardYM:::'.$cardYM.'<br>';
	if( $cardYM < $nowYM ) {
		// 期限切れ
		$NGList['limit_year'] = 1;
		$NGList['limit_month'] = 1;
	}
?>
<?php // 期限以外のカードの有効チェック
	// セキュリティコードとかチェック
	$codeIs = true; // 一旦 OKにしとく
?>
<?php // 対象の情報が登録されているかcustomer_idでチェック
	$customersCards = existCardCheck( $cardInfo );
	if( empty($customersCards) ) {
		$existCardIs = false;
	}
	else {
		$existCardIs = true;
	}
?>
<?php // 入力に不備があった場合リダイレクトする
	unset($_SESSION['fromEntryCardCheck']);
	$_SESSION['fromEntryCardCheck'] = [$codeIs, $NGList, $cardInfo];
	if( !$codeIs || array_sum($NGList) > 0 ) { // エラーがあればリダイレクト
		header('location: '.$rootDir.'entryCard.php?phase=Entry&option='.$requestOption); // リダイレクト
		exit();
	}
?>

<main>
	<section class="entry">
		<div class='title'>
			<h2>入力情報確認</h2>
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
			$messageSet =[
				'parentSection' => 'caution',
				'messages' => [
					'同じブランドですでに登録されています。',
					'既存の情報を更新しますか？',
				],
				'forBtnWrap' => [],
				'forLinkWraps' => []
			];
			if($existCardIs && $requestOption == 'ADD') {
				// [追加]で来たが、既に同じブランドで登録がある場合
				viewErrorMes($messageSet);
				$requestOption = 'UPDATA';
			}
		?>
		<div>
			<form method="post">
				<?php // 入力確認表示
					foreach($keyList as $key) {
							echo '<label>';
						if( $key == 'limit_year') {
							echo '	<span>有効期限</span>';
						}
						else if($key == 'limit_month') {
							echo '	<span class="notVisible">有効期限</span>';
						}
						else {
							echo '	<span>'.$nameList[$key].'</span>';
						}
						if($NGList[$key]){
							echo '	<span class="fromInput '.$key.' error">';
						}
						else {
							echo '	<span class="fromInput '.$key.'">';
						}
						if( $key == 'company' ) {
							echo CC_LIST[$cardInfo['company']];
						}
						else{
							echo $cardInfo[$key];
						}
						if( $key == 'limit_year') { echo ' 年'; }
						if( $key == 'limit_month') { echo ' 月'; }
							echo '</span>';
							//echo '<input type="hidden" name= "NGList['.$key.']" value="'.$NGList[$key].'">'; // NGの時はリダイレクトするので不要
							echo '<input type="hidden" name="'.$key.'" value="'.$cardInfo[$key].'">';
							echo '</label>';
					}
				?>
				<!--label>
					<span>お名前</span>
					<span class="fromInput name"><?php echo $cardInfo['name']; ?></span>
				</label>
				<label>
					<span>カード番号</span>
					<span class="fromInput card_numbers"><?php echo $cardInfo['card_number']; ?></span>
				</label>
				<label>
					<span>カード会社</span>
					<span class="fromInput card_companyanys"><?php echo CC_LIST[$cardInfo['card_company']]; ?></span>
				</label>
				<label>
					<span>有効期限</span>
					<span class="fromInput limit_month"><?php echo $cardInfo['limit_month']; ?> 月</span>
				</label>
				<label>
					<span class="notVisible">有効期限</span>
					<span class="fromInput limit_year"><?php echo $cardInfo['limit_year']; ?> 年</span>
				</label>
				<label>
					<span>セキュリティコード</span>
					<span class="fromInput code"><?php echo $cardInfo['code']; ?></span>
				</label-->
				<div class="btnWrap">
					<input type="hidden" name="card_id" value="<?php echo $requestCardId; ?>">
					<input class="commonBtnSetting <?php echo 'displayNone'; ?>" 
							type="submit"
							formaction="<?php echo $rootDir.'entryCard.php?phase=Entry'; ?>"
							value="入力を修正する"
					/>
					<input class="commonBtnSetting" 
							type="submit"
							formaction="<?php 
								echo $rootDir.'entryCard.php?phase=Result';
								if( isset($requestOption) ) { echo '&option='.$requestOption; }
							?>"
							value="<?php
								switch( $requestOption ) {
									case 'DELETE' :
										echo '削除する';
									break;
									case 'ADD' :
										echo '追加する';
									break;
									case 'UPDATA' :
										echo '登録情報を更新する';
									break;
									default :
										echo '登録する';
									break;
								}
							?>"
					/>
				</div>
			</form>
		</div>
		<div class="linkWrap">
			<a href="<?php echo $rootDir.'purchase.php?phase=Entry'; ?>">購入確認に戻る</a>
		</div>
	</section>
</main>