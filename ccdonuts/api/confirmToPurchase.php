<section class="ConfirmToPurchase">
	<p>現在 商品 <?php echo $cartInProductsNum; ?>点</p>
	<p>ご注文小計：税込 ￥<?php echo $cartInPriceSumStr ?></p>
	<div class="gotoPurchase btnWrap">
		<form action="<?php echo $rootDir.'purchase.php'; ?>" method="get">
			<input type="hidden" name="phase" value="Entry">
			<input class="commonBtnSetting"
				type="submit"
				value="購入確認へ進む"
				<?php if($cartInProductsNum<=0) { echo 'disabled=disabled'; } ?>
			/>
		</form>
	</div>
</section>