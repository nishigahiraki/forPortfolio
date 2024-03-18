<?php
	require $rootDir.'api/functions.php';
	//echo $_SERVER['REQUEST_METHOD'];
	if($_SERVER['REQUEST_METHOD']=='POST') { // リダイレクトされていると'GET'になる
		updateCart(); // カート処理を実行
		header('location: '.$rootDir.'viewCart.php'); // リダイレクト
		exit();
	}
?>

<?php
	require $rootDir.'api/functionsForDB.php';
	function updateCart() {
		// カート処理
		if( isset($_REQUEST['cartBehavior']) ) {
			switch($_REQUEST['cartBehavior']) {
				case 'insert' : // 追加
					addItemIntoCartByRequest($_REQUEST);
					// 追加後に$_REQUESTをクリア
					unset($_REQUEST);
				break;
				case 'recalc' : // 再計算
					recalcInCartById($_REQUEST['recalcId'], $_REQUEST['count']);
				break;
				case 'delete' : // 削除
					deleteItemInCartById($_REQUEST['deleteId']);
				break;
			}
		}
	}
?>

<?php // カートの内容情報の引継ぎ
	if(isset($_SESSION['cart'])) {
		$cartInProductsNum = count($_SESSION['cart']);
		$cartInPriceSumStr = priceFormChange( priceSumAtCart($_SESSION['cart']) );
	}
	else {
		$_SESSION['cart'] = [];
		$cartInProductsNum = 0;
		$cartInPriceSumStr = 0;
	}
?>

<main>
	<section class="yourCart">
		<?php require $rootDir.'api/confirmToPurchase.php'; ?>
		<!--section class="ConfirmToPurchase">
			<p>現在 商品 2点</p>
			<p>ご注文小計：税込 ￥5,000</p>
			<div class="gotoPurchase btnWrap">
				<input class="commonBtnSetting" type="submit" action="" value="購入確認へ進む">
			</div>
		</section -->
		<section class="cartInfo">
			<div class="cardsWrap">
				<?php
					$count = 0;
					foreach( $_SESSION['cart'] as $row ) {
						//print_r($_SESSION['cart']);
						$count++;
						$cardInfo = $row;
						$cardInfo['No'] = $count;
						// お気に入りを確認
						$favoriteIs = false;
						if( isset($_SESSION['customer']) ) {
							$favoriteIs = customerFavoriteIs($_SESSION['customer']['id'], $cardInfo['id']);
						}
						//
						require $rootDir.'api/productCardInCart.php';
					}
				?>
				<!-- div class="cartCard No1">
					<div class="productImage">
						<img src="../images/CART/PCCCドーナツの画像.png" alt="CCドーナツ 当店オリジナル(5個入り)">
					</div>
					<div class="infomation">
						<div class="productName"><h2>CCドーナツ 当店オリジナル(5個入り)</h2></div>
						<form>
							<div>
								<span class="productPrice">税込 ￥ 1,500</span>
								<label>
									<span>数量</span><input type="number" name="productNum"><span>個</span>
								</label>
							</div>
							<div>
								<input class="commonBtnSetting" type="submit" action="" value="再計算">
							</div>
						</form>
						<div class="toDelete"><a href="">削除する</a></div>
					</div>
				</div>
				<div class="cartCard No2">
					<div class="productImage">
						<img src="../images/CART/SPフルーツドーナツ詰め合わせ1.png" alt="CCドーナツ 当店オリジナル(5個入り)">
					</div>
					<div class="infomation">
						<div class="productName"><h2>CCドーナツ 当店オリジナル(5個入り)</h2></div>
						<form>
							<div>
								<span class="productPrice">税込 ￥ 1,500</span>
								<label>
									<span>数量</span><input type="number" name="productNum"><span>個</span>
								</label>
							</div>
							<div>
								<input class="commonBtnSetting" type="submit" action="" value="再計算">
							</div>
						</form>
						<div class="toDelete"><a href="">削除する</a></div>
					</div>
				</div-->
			</div>
		</section>
		<!--section class="ConfirmToPurchase">
			<p>現在 商品 2点</p>
			<p>ご注文小計：税込 ￥5,000</p>
			<div class="gotoPurchase btnWrap">
				<input class="commonBtnSetting" type="submit" action="" value="購入確認へ進む">
			</div>
		</section-->
		<?php
			if(count($_SESSION['cart'])>1) {
				require $rootDir.'api/confirmToPurchase.php';
			}
		?>
		<div class="continueToShopping btnWrap">
			<a class="commonBtnSetting" href="<?php echo $rootDir.'viewList.php?view=all'; ?>">買い物を続ける</a>
		</div>
	</section>
</main>