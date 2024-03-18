<?php require $rootDir.'api/functions.php'; ?>
<?php require $rootDir.'api/functionsForDB.php'; ?>
<?php $allProducts = getAllproducts(); ?>

<main>
	<section class="productsAll">
		<div class="title addbottom80px">
			<h2>商品一覧</h2>
		</div>
		<section class="mainMenu">
			<h3 class="subTitle">メインメニュー</h3>
			<div class="cardsWrap">
				<?php
				$count = 0;
				foreach( $allProducts['mainItems'] as $row ) {
					$rankingIs = false;
					$count++;
					$cardInfo = $row;
					$cardInfo['No'] = $count;
					// お気に入りを確認
					$favoriteIs = false;
					if( isset($_SESSION['customer']) ) {
						$favoriteIs = customerFavoriteIs($_SESSION['customer']['id'], $cardInfo['id']);
					}
					//
					require './api/productCard.php';
				}
				?>
			</div>
		</section>
		<section class="varietySet">
			<h3 class="subTitle">バラエティセット</h3>
			<div class="cardsWrap">
				<?php
				$count = 0;
				foreach( $allProducts['varietyItems'] as $row ) {
					$rankingIs = false;
					$count++;
					$cardInfo = $row;
					$cardInfo['No'] = $count;
					// お気に入りを確認
					$favoriteIs = false;
					if( isset($_SESSION['customer']) ) {
						$favoriteIs = customerFavoriteIs($_SESSION['customer']['id'], $cardInfo['id']);
					}
					//
					require './api/productCard.php';
				}
				?>
			</div>
		</section>
	</section>
</main>