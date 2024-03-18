<?php require $rootDir.'api/functions.php'; ?>
<?php // 初期化
unset($_SESSION['fromEntryCardCheck']);
?>

<?php
// カード情報の照会(SQL処理)
require $rootDir.'api/functionsForDB.php';
$customerCards = getCardsInfoByCustomerId( $_SESSION['customer']['id'] );
// カードの選択状態
if( !isset($selectCardCompany) && count($customerCards) > 0 ) {
	$selectCardCompany = 0; //　カード登録があれば、1枚目を選択
}
// idで選択できる様に調整
$cardIdList = [];
foreach($customerCards as $row) {
	$cardIdList[] = [
		'card_id' => $row['id'],
		'company' => $row['company'],
	];
}

?>
<!-- <pre>
<?php //var_dump($customerCards); ?>
</pre> -->
<main>
	<section class="purchase">
		<div class="title">
			<h2>ご購入確認</h2>
		</div>
		<section class="PurchaseDetail">
			<h3 class="subTitle">ご購入商品</h3>
			<div class="cardsWrap">
				<?php
					$count = 0;
					foreach( $yourCart as $product ) {
						$count++;
						echo '<div class="purchaseCard No'.$count.'">';
						echo '    <div class="productNameLine">';
						echo '        <span>商品名</span>';
						echo '        <span class="productName">'.$product['name'].'</span>';
						echo '    </div>';
						echo '    <div class="productNumLine">';
						echo '        <span>数量</span>';
						echo '        <span class="productNum">'.$product['count'].'個</span>';
						echo '    </div>';
						echo '    <div class="productTotalPriceLine">';
						echo '        <span>金額</span>';
						echo '        <span class="productTotalPrice">税込 ￥'.priceFormChange($product['price']).'</span>';
						echo '    </div>';
						echo '</div>';
					}
				?>
				<!--div class="purchaseCard No1">
					<div class="productNameLine">
						<span>商品名</span>
						<span class="productName">CCドーナツ 当店オリジナル(5個入り)</span>
					</div>
					<div class="productNumLine">
						<span>数量</span>
						<span class="productNum">1個</span>
					</div>
					<div class="productTotalPriceLine">
						<span>金額</span>
						<span class="productTotalPrice">税込 ￥1,500</span>
					</div>
				</div>
				<div class="purchaseCard No2">
					<div class="productNameLine">
						<span>商品名</span>
						<span class="productName">CCドーナツ 当店オリジナル(5個入り)</span>
					</div>
					<div class="productNumLine">
						<span>数量</span>
						<span class="productNum">1個</span>
					</div>
					<div class="productTotalPriceLine">
						<span>金額</span>
						<span class="productTotalPrice">税込 ￥1,500</span>
					</div>
				</div-->
				<?php
					echo '<div class="purchaseCard total">';
					echo '    <div class="totalNumLine">';
					echo '        <span>合計数量</span>';
					echo '        <span class="totalNum">'.count($yourCart).'個</span>';
					echo '    </div>';
					echo '    <div class="totalPriceLine">';
					echo '        <span>合計金額</span>';
					echo '        <span class="totalPrice">税込 ￥ '.priceFormChange( priceSumAtCart($yourCart) ).'</span>';
					echo '    </div>';
					echo '</div>';
				?>
				<!--div class="totalCard">
					<div class="totalNumLine">
						<span>合計数量</span>
						<span class="totalNum">2個</span>
					</div>
					<div class="totalPriceLine">
						<span>合計金額</span>
						<span class="totalPriceLine">税込 ￥5,000</span>
					</div>
				</div-->
			</div>
		</section>
		<section class="sendInfo">
			<h3 class="subTitle">お届け先</h3>
			<div class="cardsWrap">
				<div class="purchaseCard">
					<div class="customerNameLine">
						<span>お名前</span>
						<span class="customerName"><?php echo $your['name']?></span>
					</div>
					<div class="zipCodeLine">
						<span>郵便番号</span>
						<span class="zipCode"><?php echo $your['postcode_a']?>-<?php echo $your['postcode_b']?></span>
					</div>
					<div class="customerAddressLine">
						<span>住所</span>
						<span class="customerAddress"><?php echo $your['address']?></span>
					</div>
				</div>
			</div>
		</section>
		<section class="cardInfo">
			<h3  class="subTitle">お支払い方法</h3>
			<div class="cardsWrap">
				<form id="purchaseForm" method="POST">
					<div class="purchaseCard">
						<?php
							if( empty($customerCards) ) {
								echo '<div class="toCardEntryWrap btnWrap">';
								echo '	<a class="commonBtnSetting" href="'.$rootDir.'entryCard.php?phase=Entry">カード情報登録する</a>';
								echo '</div>';
								echo '<p>';
								echo '	カード情報登録がまだのお客様はこちらへお進みください。';
								echo '</p>';
							}
							else {
								echo '<div class="payLine">';
								echo '	<span>お支払い</span>';
								echo '	<span class="pay">'.'クレジットカード'.'</span>';
								echo '</div>';
								echo '<div class="cardBrandLine">';
								echo '	<span>ブランド</span>';
								// カードの選択状態を反映
								echo '	<span><select name="card_id">';
								// for( $i=0; $i<count($customerCards); $i++ ) {
								// 	echo '<option value="'.$i.'"';
								// 		if( $selectCardCompany ==  $customerCards[$i]['company']) { echo 'selected'; }
								// 	echo '>'.CC_LIST[$customerCards[$i]['company']].'</option>';
								// }
								foreach($cardIdList as $row) {
									echo '<option value="'.$row['card_id'].'" ';
										if( $selectCardCompany == $row['company'] ) { echo 'selected'; }
									echo '>'.CC_LIST[$row['company']].'</option>';
								}
								echo '	</select></span>';
								echo '<input type="submit" formaction="'.$rootDir.'entryCard.php?phase=Entry&option=UPDATA" value="[更新]"/>';
								echo '<input type="submit" formaction="'.$rootDir.'entryCard.php?phase=Entry&option=ADD" value="[追加]"/>';
								echo '<input type="submit" formaction="'.$rootDir.'entryCard.php?phase=Check&option=DELETE" value="[削除]"/>';
								//echo '	<span class="cardBrand">'.CC_LIST[$customersCards[0]['card_company']].'</span>';
								echo '</div>';
								echo '<div class="cardCordLine">';
								echo '	<span>コード</span>';
								echo ' 	<span><input type="text" name="code" placeholder="セキュリティーコードを入力"></span>';
								echo '</div>';
							}
						?>
					</div>
				</form>
			</div>
		</section>
		<?php
			if( !empty($customerCards) ) {
				echo '<form class="btnWrap" method="POST">';
				echo '	<input class="commonBtnSetting" type="submit" 
				form="purchaseForm" formaction="'.$rootDir.'purchase.php?phase=Result" value="購入を確定する" >';
				echo '</form>';
			}
		?>
	<div class="linkWrap"></div>
	</section>
</main>