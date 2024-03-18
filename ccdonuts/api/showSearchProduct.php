<?php require $rootDir.'api/functions.php'; ?>
<?php require $rootDir.'api/functionsForDB.php'; ?>
<?php
switch( $searchWord ) {
	case 'お気に入り' :
	case 'お気にいり' :
	case 'おきにいり' :
	case 'fav' :
	case 'favorite' :
		// お気に入り検索
		if( isset($_SESSION['customer']['id']) ) {
			$ccdSearchItems = customerFavoriteAll($_SESSION['customer']['id']);
		}
		else { $ccdSearchItems = []; }
	break;
	case '新商品' :
	case 'しんしょうひん':
	case '新作' :
	case 'しんさく' :
	case 'new' :
	case 'new item' :
	case 'new items' :
	case 'newitem' :
	case 'newitems' :
		// 新商品検索
		$ccdSearchItems = getNewProductInfo();
	break;
	case '購入履歴' :
	case 'こうにゅうりれき' :
	case '履歴' :
	case 'りれき' :
	case 'history' :
		// 購入履歴検索
		if( isset($_SESSION['customer']['id']) ) {
			$ccdSearchItems = getPurchaseHistory($_SESSION['customer']['id']);
		}
		else { $ccdSearchItems = []; }
		break;
	default :
		$ccdSearchItems = searchProductsByName($searchWord);
	break;
}
?>

<main>
	<section class="productsAll">
		<div class="title">
			<h2>商品検索結果</h2>
		</div>
		<section class="mainMenu">
			<h3 class="subTitle addbottom80px">『<?php echo htmlspecialchars($searchWord); ?>』での検索結果は、<?php echo count($ccdSearchItems); ?>点です。</h3>
			<div class="cardsWrap">
				<?php
					$count = 0;
					foreach( $ccdSearchItems as $row ) {
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