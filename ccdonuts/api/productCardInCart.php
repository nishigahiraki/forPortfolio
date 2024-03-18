<!-- カート用の商品表示カード -->
<div class="<?php echo 'cartCard No'.numStr10($cardInfo['No']); ?>">
	<a href="<?php echo $rootDir.'viewDetail.php?name='.$cardInfo['name']; ?>">
		<div class="productImage <?php if($favoriteIs) { echo 'favorite'; } ?>">
			<img src="<?php echo $rootDir.'images/PRODUCTALL/ccd'.numStr10($cardInfo['id']).'PC.png'; ?>" alt="<?php echo $cardInfo['name']; ?>">
		</div>
	</a>
	<div class="infomation">
		<div class="productName">
			<h2>
				<a href="<?php echo $rootDir.'viewDetail.php?name='.$cardInfo['name']; ?>">
					<!--? セッションからのデータだから、is_newが取れないなぁ　php if( $cardInfo['is_new'] == true ) { echo '【新作】'; } ?-->
					<?php echo $cardInfo['name']; ?>
				</a>
			</h2>
		</div>
		<form action="<?php echo $rootDir.'viewCart.php'; ?>" method="post">
			<div>
				<span class="productPrice">税込 ￥ <?php echo priceFormChange($cardInfo['price']); ?></span>
				<label>
					<span>数量</span><input type="number" name="count" min="1" max="999" value="<?php echo $cardInfo['count']; ?>"><span>個</span>
				</label>
			</div>
			<div>
				<input type="hidden" name="recalcId" value="<?php echo $cardInfo['id']; ?>">
				<input type="hidden" name="cartBehavior" value="recalc">
				<input class="commonBtnSetting" type="submit" value="再計算">
			</div>
		</form>
		<form action="<?php echo $rootDir.'viewCart.php'; ?>" method="post">
			<input type="hidden" name="deleteId" value="<?php echo $cardInfo['id']; ?>">
			<input type="hidden" name="cartBehavior" value="delete">
			<input class="toDelete" type="submit" value="削除する">
		</form>
	</div>
</div>