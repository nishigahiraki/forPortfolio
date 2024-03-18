<?php require $rootDir.'api/functions.php'; ?>
<?php require $rootDir.'api/functionsForDB.php'; ?>
<?php $selectItem = getDetailByName($searchName); ?>
<?php // 検索結果がなければ...
if( empty($selectItem) ){
	header('location: '.$rootDir.'viewList.php?view=all'); // リダイレクト
	exit();
}
?>

<?php
if(isset($_SESSION['customer'])){
	if( isset($_REQUEST['favoriteIs']) ) {
		$favoriteIs = toggleFavorite($_SESSION['customer']['id'], $selectItem['id']);
	}
	else {
		$favoriteIs = customerFavoriteIs($_SESSION['customer']['id'], $selectItem['id']);
	}
}
else { $favoriteIs = false; }
?>

<main>
<?php // アラート
if( empty($_SESSION['customer']) && !empty($_REQUEST['favoriteIs']) ) {
	$messages = [];
	$messages[] = 'お気に入り情報の操作は、';
	$messages[] = 'ログインをしてください。';
	//
	$messageSet =[
		'parentSection' => 'alert',
		'messages' => $messages,
		'forBtnWrap' => ['ログインする', $rootDir.'login.php?phase=Entry'],
		'forLinkWraps' => []
	];
	viewErrorMes($messageSet);
}
?>
<section class="productDetail">
	<div class="cardsWrap">
		<div class="productImage">
			<?php
			echo '<img src="'.$rootDir.'images/PRODUCTALL/ccd'.numStr10($selectItem['id']).'PC.png" alt="'.$selectItem['name'].'">';
			?>
		</div>
		<div class="detailCard">
			<div class="productName">
				<h2>
					<?php if( $selectItem['is_new'] == true ) { echo '【新作】'; } ?>
					<?php echo $selectItem['name']; ?>
				</h2>
			</div>
			<div class="productDetail">
				<?php echo $selectItem['introduction']; ?>
			</div>
			<div class="productPrice">
				税込 ￥ <?php echo priceFormChange($selectItem['price']); ?>
			</div>
			<form action="<?php echo $rootDir.'viewCart.php'; ?>" method="post">
				<label><input type="number" name="count" min="0" max="999" value="1"><span>個</span></label>
				<div class="intoCartBtnWrap">
					<input type="hidden" name="id" value="<?php echo $selectItem['id']; ?>">
					<input type="hidden" name="name" value="<?php echo $selectItem['name']; ?>">
					<input type="hidden" name="price" value="<?php echo $selectItem['price']; ?>">
					<input type="hidden" name="cartBehavior" value="insert">
					<?php
						$toCartMess = 'カートに入れる';
						if(isset($_SESSION['cart'])) { // カートに同じ物があるか判定
							foreach($_SESSION['cart'] as $row) {
								if($row['id'] == $selectItem['id']) { $toCartMess = '更にカートへ追加する'; break; }
							}
						}
					?>
					<input class="commonBtnSetting" type="submit" value="<?php echo $toCartMess; ?>">
				</div>
			</form>
			<div class="toggleFavoriteWrap">
				<?php
					if($favoriteIs) {
						// 既にお気に入りなので、クリックするとお気に入りから外れる
						echo '<a href="'.$rootDir.'viewDetail.php?name='.$searchName.'&favoriteIs=false">';
						echo '<img src="'.$rootDir.'images/COMMON/favoriteOnIcon.svg" alt="favorite">';
						echo '</a>';
					}
					else {
						// お気に入りじゃないので、クリックするとお気に入りになる
						echo '<a href="'.$rootDir.'viewDetail.php?name='.$searchName.'&favoriteIs=true">';
						echo '<img src="'.$rootDir.'images/COMMON/favoriteOffIcon.svg" alt="favorite">';
						echo '</a>';
					}
				?>
			</div>
		</div>
	</div>
</section>
</main>