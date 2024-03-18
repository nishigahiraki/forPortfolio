<!-- 商品表示用カード　 -->
<div class="<?php echo 'card No'.numStr10($cardInfo['No']); ?>">
	<?php
		if($rankingIs) {
			echo '	<div class="rankNo">'.$cardInfo['No'].'</div>';
		}
	?>
	<a href="<?php echo $rootDir.'viewDetail.php?name=' .$cardInfo['name']; ?>">
		<div class="productImage <?php if($favoriteIs) { echo 'favorite'; } ?>">
			<!--img src="<?php echo $rootDir.'images/PRODUCTALL/ccd'.numStr10($cardInfo['id']).'PC.png'; ?>" alt="<?php echo $cardInfo['name']; ?>"-->
			<picture>
				<source srcset="<?php echo $rootDir.'images/PRODUCTALL/ccd'.numStr10($cardInfo['id']).'SP.png'; ?>"
					media="(max-width: 768px)">
				<img src="<?php echo $rootDir.'images/PRODUCTALL/ccd'.numStr10($cardInfo['id']).'PC.png'; ?>"
					alt="<?php echo $cardInfo['name']; ?>">
			</picture>
		</div>
	</a>
	<a href="<?php echo $rootDir.'viewDetail.php?name='.$cardInfo['name']; ?>">
		<div class="productName">
			<?php 
				if( $cardInfo['is_new'] == true ) { echo '【新作】'; }
				echo $cardInfo['name'];
			?>
		</div>
	</a>
	<div class="productPrice">税込 ￥ <?php echo priceFormChange($cardInfo['price']); ?></div>
	<form action="<?php echo $rootDir.'viewCart.php'; ?>" method="post">
		<input type="hidden" name="id" value="<?php echo $cardInfo['id']; ?>">
		<input type="hidden" name="name" value="<?php echo $cardInfo['name']; ?>">
		<input type="hidden" name="price" value="<?php echo $cardInfo['price']; ?>">
		<input type="hidden" name="count" value="1">
		<input type="hidden" name="cartBehavior" value="insert">
		<div class="intoCartBtnWrap">
			<?php
				$toCartMess = 'カートに入れる';
				if(isset($_SESSION['cart'])) { // カートに同じ物があるか判定
					foreach($_SESSION['cart'] as $row) {
						if($row['id'] == $cardInfo['id']) { $toCartMess = '更にカートへ追加する'; break; }
					}
				}
			?>
			<input class="commonBtnSetting" type="submit" value="<?php echo $toCartMess; ?>">
		</div>
	</form>
</div>
