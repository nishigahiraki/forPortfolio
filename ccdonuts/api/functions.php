<?php // 数字の整形
function numStr10($num) {
	$str10 = $num;
	if( $num < 10 ) {
		$str10 = '0'.$str10;
	}
	return $str10;
}
?>
<?php // 金額整形
function priceFormChange($nowPrice) {
	$nowPriceAry = [];
	$priceLen = strlen($nowPrice);
	$count = 0;
	while( $priceLen-3*($count) > 0 ) {
		if( $priceLen-3*($count) < 3 ) {
			$tmp = substr($nowPrice, 0, $priceLen-3*($count));
		}
		else {
			$tmp = substr($nowPrice, $priceLen-3*($count+1), 3);
		}
		array_push($nowPriceAry,$tmp);
		$count++;
	}
	$nowPriceStr = array_pop($nowPriceAry);
	while( count($nowPriceAry) > 0 ) {
		$nowPriceStr = $nowPriceStr .','. array_pop($nowPriceAry);
	}
return $nowPriceStr;
}
?>

<?php // カート内合計 yourCart[price,count]
function priceSumAtCart($yourCart) {
	$cartInPriceSum = 0;
	foreach( $yourCart as $row ) {
		$cartInPriceSum += $row['price'] * $row['count'];
	}
	return $cartInPriceSum;
}
?>

<?php // カートへ追加($_SESSION['cart']をカートで決め打ちにしとく)
function addItemIntoCartByRequest($request) {
	if( isset($request['id']) &&
		isset($request['name']) &&
		isset($request['price']) &&
		isset($request['count']) ) {
		$requestPId = $request['id']; // リクエストの引継ぎ
		$requestPName = $request['name'];
		$requestPPrice = $request['price'];
		$requestPNum = $request['count'];
	}
	else {
		$requestPId = 3; // 仮入力
		$requestPName = 'てすと入力';
		$requestPPrice = 100;
		$requestPNum = 3;
	}
	//
	if(!isset($_SESSION['cart'])) { //　カートが設定されていなければ
		$_SESSION['cart'] = []; // 初期化
	}
	$productCount = 0;
	if(isset($_SESSION['cart'][$requestPId])) {
		// カートの中に同じ商品があるとき...
		$productCount = $_SESSION['cart'][$requestPId]['count']; // 既存の数を引継ぎ
	}
	// カートを更新
	$_SESSION['cart'][$requestPId] = [
		'id' => $requestPId,
		'name' => $requestPName,
		'price' => $requestPPrice,
		'count' => $productCount + $requestPNum,
	];
	//
	// 後始末 (リロード対策)
	$_REQUEST = null; // <=対策にならんかった
}
?>
<?php // カートから削除($_SESSION['cart']をカートで決め打ちにしとく)
function deleteItemInCartById($productId) {
	unset($_SESSION['cart'][$productId]);
}
?>
<?php // 再計算($_SESSION['cart']をカートで決め打ちにしとく)
function recalcInCartById($productId, $requestCount) {
	if(isset($_SESSION['cart'][$productId])) { // 再計算要求の商品があれば...
		$_SESSION['cart'][$productId]['count'] = $requestCount;
	}
}
?>
<?php // カードリスト
define('CC_LIST', ['JCB', 'Visa', 'Mastercard']);
?>
<?php // エラーメッセージ表示枠
function viewErrorMes($messageSet) {
	$pSection = $messageSet['parentSection'];
	$messages = $messageSet['messages'];
	$forBtnWrap = $messageSet['forBtnWrap'];
	$forLinkWraps = $messageSet['forLinkWraps'];

	if( !empty($pSection) ){
		echo '<section class="'.$pSection.'">';
	}
	echo '<section class="errorView">';
	foreach($messages as $row) {
		echo '<p>'.$row.'</p>';
	}
	if( !empty($forBtnWrap) ) {
		print <<< _BtnWrap_
		<div class="btnWrap">
			<a class="commonBtnSetting" href="$forBtnWrap[1]">$forBtnWrap[0]</a>
		</div>
		_BtnWrap_;
	}
	echo '</section>';
	if(!empty($forLinkWraps)) {
		echo '<div class="linkWrap">';
			foreach($forLinkWraps as $row) {
				echo '<div><a href="'.$row[1].'">'.$row[0].'</a></div>';
			}
		echo '</div>';
	}
	if( !empty($pSection) ) {
		echo '</section>';
	}
}