<?php session_start(); ?>

<?php // ページ情報
	$pageTitle = 'C.C.Donuts';
	$rootDir = './';
	$breadInfo = [];
?>

<?php require $rootDir.'api/functions.php'; ?>
<?php require $rootDir.'api/functionsForDB.php'; ?>

<?php  // 新商品検索
	$tmpNewProducts = getNewProductInfo();
	// 一旦 1つ目だけを採用しておく
	$ccdNewProduct = [
				  'id' => $tmpNewProducts[0]['id'],
				'name' => $tmpNewProducts[0]['name'],
			   'price' => $tmpNewProducts[0]['price'],
		'introduction' => $tmpNewProducts[0]['introduction'],
		]
?>
<?php
	// 商品名の()を分割する
	$ccdNewProductNameBody = strstr($ccdNewProduct['name'], '（', true);
	$ccdNewProductNameSub = strstr($ccdNewProduct['name'], '（');
?>
<?php $newItemThumbUrl = $rootDir.'images/PRODUCTALL/ccd'.numStr10($ccdNewProduct['id']); ?>
<?php // 全商品取得
	$ccdAllProduct = getAllProducts()['allProducts'];
?>

<?php // 人気ランキング
// 購入総個数で降順にリストを取得
$ppRankingList = getPurchasedProducts();
// リストに順位を追加
$ppMAX = count($ppRankingList);
$rankNo = 1;
// 1位を取り合えず登録
$ppRankingList[0]['rankNo'] = 1;
$ppRankingList[0]['productInfo'] = getDetailById($ppRankingList[0]['product_id']);
// 1番目以降を処理
for($i=1; $i<$ppMAX; $i++) {
	// １個前と比較して...
	if($ppRankingList[$i]['sum'] == $ppRankingList[$i-1]['sum']) {
		// 購入総個数が同じなら、同じ順位にする。
		$ppRankingList[$i]['rankNo'] = $ppRankingList[$i-1]['rankNo']; // 順位の登録
	}
	else {
		// 購入総個数が異なれば...
		$ppRankingList[$i]['rankNo'] = $i+1; // 累積順位の登録
	}
	// 商品情報を登録しておく
	$ppRankingList[$i]['productInfo'] = getDetailById($ppRankingList[$i]['product_id']);
}
?>

<?php require $rootDir.'common/header.php'; ?>
		<main>
			<section class="pageTop">
				<div class="heroImage">
					<picture>
						<source srcset="<?php echo $rootDir.'images/INDEX/heroImagePC.png'; ?>"
							media="(max-width: 768px)">
						<img src="<?php echo $rootDir.'images/INDEX/heroImagePC.png'; ?>"
							alt="ccdonutsヒーロー">
					</picture>
					<!--img src="./images/INDEX/heroImagePC.png" alt="ccdonutsヒーロー"-->
				</div>
			</section>
			<section class="products">
				<div class="panelWrap">
					<div class="panel1">
						<a href="<?php echo $rootDir.'viewDetail.php?name='.$ccdNewProduct['name'].'">'; ?>
							<span>新商品</span>
							<span>
								<span><?php echo $ccdNewProductNameBody.'</span>
								<span class="nosp">'.$ccdNewProductNameSub; ?></span>
							</span>
							<!--div><image src="./images/INDEX/newItemPC.png" alt="新商品"></div-->
						</a>
					</div>
					<div class="panel2">
						<a href="">
							<span>ドーナツのある生活</span>
							<!--div><image src="./images/INDEX/lifeWithDounutsPC.png" alt="ドーナツのある生活"></div-->
						</a>
					</div>
					<div class="panel3">
						<a href="./viewList.php?view=all">
							<span>商品一覧</span>
							<!--div><image src="./images/INDEX/productAllBannerPC.png" alt="商品一覧"></div-->
						</a>
					</div>
				</div>
			</section>
			<section class="philosophy">
				<div class="philosophyWrap">
					<h2>Philosophy</h2>
					<h3>私たちの信念</h3>
					<p>"Creationg Connections"</p>
					<p>「ドーナツでつながる」</p>
				</div>
				<!--div class="philosophyImg">
					<img src="./images/INDEX/philosophyImagePC.png" alt="CCドーナツ philosopyイメージ">
				</div-->
			</section>
			<section
				class="ranking <?php 
					$ppMax = count($ppRankingList);
					if( $ppMax < 1 ) { echo 'displayNone'; }
				?>">
				<div class="title">
					<h2>人気ランキング</h2>
				</div>
					<div class="cardsWrap">
						<?php
							if( $ppMax > 6 ) { $ppMax = 6; }
							for( $i=0; $i<$ppMax; $i++ ) {
								$rankingIs = true;
								if( $i > $ppMax ) { break; }
								$cardInfo = $ppRankingList[$i]['productInfo'];
								$cardInfo['No'] = $ppRankingList[$i]['rankNo'];
								//var_dump($cardInfo);
								// お気に入りを確認
								$favoriteIs = false;
								if( isset($_SESSION['customer']) ) {
									$favoriteIs = customerFavoriteIs($_SESSION['customer']['id'], $cardInfo['id']);
								}
								//
								//
								require './api/productCard.php';
							}
						?>
					<!--div class="card No01">
						<div class="rankNo">1</div>
						<div class="productImage">
							<img src="./images/PRODUCTALL/ccd01PC.png" alt="CCドーナツ 当店オリジナル(5個入り)">
						</div>
						<div class="productName">CCドーナツ 当店オリジナル(5個入り)</div>
						<div class="productPrice">税込 ￥ 1,500</div>
						<div class="intoCartBtnWrap">
								<input class="commonBtnSetting" type="submit" action="" value="カートに入れる">
						</div>
					</div>
					<div class="card No02">
						<div class="rankNo">2</div>
						<div class="productImage">
							<img src="./images/PRODUCTALL/ccd01PC.png" alt="CCドーナツ 当店オリジナル(5個入り)">
						</div>
						<div class="productName">CCドーナツ 当店オリジナル(5個入り)</div>
						<div class="productPrice">税込 ￥ 1,500</div>
						<div class="intoCartBtnWrap">
								<input class="commonBtnSetting" type="submit" action="" value="カートに入れる">
						</div>
					</div>
					<div class="card No03">
						<div class="rankNo">3</div>
						<div class="productImage">
							<img src="./images/PRODUCTALL/ccd01PC.png" alt="CCドーナツ 当店オリジナル(5個入り)">
						</div>
						<div class="productName">CCドーナツ 当店オリジナル(5個入り)</div>
						<div class="productPrice">税込 ￥ 1,500</div>
						<div class="intoCartBtnWrap">
								<input class="commonBtnSetting" type="submit" action="" value="カートに入れる">
						</div>
					</div>
					<div class="card No04">
						<div class="rankNo">4</div>
						<div class="productImage">
							<img src="./images/PRODUCTALL/ccd01PC.png" alt="CCドーナツ 当店オリジナル(5個入り)">
						</div>
						<div class="productName">CCドーナツ 当店オリジナル(5個入り)</div>
						<div class="productPrice">税込 ￥ 1,500</div>
						<div class="intoCartBtnWrap">
								<input class="commonBtnSetting" type="submit" action="" value="カートに入れる">
						</div>
					</div>
					<div class="card No05">
						<div class="rankNo">5</div>
						<div class="productImage">
							<img src="./images/PRODUCTALL/ccd01PC.png" alt="CCドーナツ 当店オリジナル(5個入り)">
						</div>
						<div class="productName">CCドーナツ 当店オリジナル(5個入り)</div>
						<div class="productPrice">税込 ￥ 1,500</div>
						<div class="intoCartBtnWrap">
								<input class="commonBtnSetting" type="submit" action="" value="カートに入れる">
						</div>
					</div>
					<div class="card No06">
						<div class="rankNo">6</div>
						<div class="productImage">
							<img src="./images/PRODUCTALL/ccd01PC.png" alt="CCドーナツ 当店オリジナル(5個入り)">
						</div>
						<div class="productName">CCドーナツ 当店オリジナル(5個入り)</div>
						<div class="productPrice">税込 ￥ 1,500</div>
						<div class="intoCartBtnWrap">
								<input class="commonBtnSetting" type="submit" action="" value="カートに入れる">
						</div-->
					</div>
				</div>
			</section>
		</main>
<?php require $rootDir.'common/footer.php'; ?>