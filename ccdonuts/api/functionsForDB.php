<?php // ログイン処理
function excuteLogin( $loginInfo ) {
	// 初期化
	unset($_SESSION['customer']);
	unset($_SESSION['fromEntryStoreCheck']); // 会員情報の入力を破棄
	unset($_SESSION['fromEntryCardCheck']); // カード情報の入力を破棄
	$loginIs = false;
	//
	$customerInfo = getCustomerInfo( $loginInfo ); // 会員情報の取得
	//
	if( isset($customerInfo) && $customerInfo != false ) {
		// 会員情報が取得できてれば、$_SESSIONに登録
		$_SESSION['customer'] = [
			'id' => htmlspecialchars($customerInfo['id']),
			'name' => htmlspecialchars($customerInfo['name']),
			'furigana' => htmlspecialchars($customerInfo['furigana']),
			'postcode_a' => sprintf('%03d',htmlspecialchars($customerInfo['postcode_a'])),
			'postcode_b' => sprintf('%04d',htmlspecialchars($customerInfo['postcode_b'])),
			'address' => htmlspecialchars($customerInfo['address']),
			'mail' => htmlspecialchars($customerInfo['mail']),
			'mailForCheck' => htmlspecialchars($customerInfo['mail']),
			'password' => htmlspecialchars($customerInfo['password']),
			'passwordForCheck' => htmlspecialchars($customerInfo['password']),
		];
		$loginIs = true;
	}
	//
	return $loginIs; // $_SESSIONに登録できたら、trueを返す
}
?>

<?php // ログアウト処理
function excuteLogout() {
	if(isset($_SESSION)){
		// $_SESSION['loginIs'] = false;
		if( isset($_SESSION['customer']) ) {
			unset($_SESSION['customer']); // SESSION内の会員情報破棄
			unset($_SESSION['fromEntryStoreCheck']); // SESSION内の更新情報破棄
			unset($_SESSION['fromEntryCardCheck']); // SESSION内の更新情報破棄
			unset($_SESSION['cart']); // SESSION内のcart情報破棄
			//echo 'ログアウトしました。';
		}
		else {
			//echo 'すでにログアウトしてました。';
		}
	}
}
?>

<?php // SQL DB:ccdonutsへ接続
function ccDBconection() { // SQLデータベースへの接続
	$nowConection=$_SERVER['SERVER_NAME'];
	try{
		$ccdHOST = 'localhost'; // サーバホスト
		switch( $nowConection ) {
			case 'localhost' :
				$ccdDBNAME = 'ccdonuts'; // データベース名
				$ccdUSER = 'ccStaff'; // ユーザ名
				$ccdPASS = 'ccDonuts'; // パスワード
			break;
			case 'cf670622.cloudfree.jp' :
				$ccdDBNAME = 'cf670622_ccdonuts'; // データベース名
				$ccdUSER = 'cf670622_nicky'; // ユーザ名
				$ccdPASS = 'nicky1976'; // パスワード
			break;
		}
	//
		$pdo = new PDO("mysql:host=$ccdHOST; dbname=$ccdDBNAME; charset=utf8", $ccdUSER , $ccdPASS); // DBへの接続
		return $pdo;
	}
	catch( PDOException $error ) {
        echo 'エラー';
    }
}
?>

<?php // DB:products 全商品の取得
function getAllProducts() {
	$pdo = ccDBconection(); // データベースへの接続
	$sql = $pdo->prepare("SELECT * FROM products"); // 全商品取得
	$sql->execute();
	$ccdAllProducts = $sql->fetchAll(); // 取得データを変数に置き換え
	unset($pdo); // データベースからの切断
	
	if(empty($ccdAllProducts)) { echo '登録がありません。'; }
	else {
		$ccdMainItems = [];
		$ccdVarietyItems = [];
		foreach( $ccdAllProducts as $row ) {
			if( preg_match( '/^.*セットです.*$/',$row['introduction']) ) {
				array_push( $ccdVarietyItems, $row );
			}
			else {
				array_push( $ccdMainItems, $row );
			}
		}
	}
	return [
		'allProducts' => $ccdAllProducts,
		'mainItems' => $ccdMainItems,
		'varietyItems' => $ccdVarietyItems,
	];
}
?>

<?php // DB:products 新商品の取得
function getNewProductInfo() {
	try{
		$pdo = ccDBconection(); // データベースへの接続
		$sql = $pdo->prepare("SELECT * FROM products WHERE is_new=?"); // 新商品取得
		$sql->execute([1]);
		$ccdNewProducts = $sql->fetchAll(); // 取得データを変数に置き換え
		unset($pdo); // データベースからの切断
	}
	catch( PDOException $error ) {
        $ccdNewProducts = [];
    }
	//
	return $ccdNewProducts;
	/*
	if(empty($ccdNewProducts)) { 
		echo '新商品がありません。';
		return null;
	}
	else {
		// 一旦 1つ目だけを採用しておく
		return [
				'id' => $ccdNewProducts[0]['id'],
				'name' => $ccdNewProducts[0]['name'],
			'price' => $ccdNewProducts[0]['price'],
		'introduction' => $ccdNewProducts[0]['introduction'],
		];
	}
	*/
}
?>

<?php // DB:products 商品検索(名称)
function searchProductsByName($searchWord) {
	$pdo = ccDBconection(); // データベースへの接続
	$sql = $pdo->prepare("SELECT * FROM products WHERE name like ?"); // 全商品取得
	$sql->execute([
		'%'.htmlspecialchars($searchWord).'%'
	]);
	$ccdSearchItems = $sql->fetchAll(); // 取得データを変数に置き換え
	unset($pdo); // データベースからの切断
	//if(empty($ccdSearchItems)) { echo '登録がありません。'; }
	return $ccdSearchItems;
}
?>

<?php // DB:products 1商品の取得(名称)
function getDetailByName( $name ) {
	$pdo = ccDBconection(); // データベースへの接続
	$sql = $pdo->prepare("SELECT * FROM products WHERE name=?"); // 商品取得
	$sql->execute([
		htmlspecialchars($name),
	]);
	$selectProducts = $sql->fetchAll(); // 取得データを変数に置き換え
	unset($pdo); // データベースからの切断
	/*foreach( $selectProduct as $row ) {
		print_r($row);
	}*/
	return $selectProducts[0]; // 1つ目をお目当ての商品としておく。
}
?>

<?php // DB:products 1商品の取得(id)
function getDetailById( $product_id ) {
	$pdo = ccDBconection(); // データベースへの接続
	$sql = $pdo->prepare("SELECT * FROM products WHERE id=?"); // 商品取得
	$sql->execute([
		htmlspecialchars($product_id),
	]);
	$selectProduct = $sql->fetch(PDO::FETCH_ASSOC); // 取得データを変数に置き換え。idはユニークでしょう。
	unset($pdo); // データベースからの切断
	/*foreach( $selectProduct as $row ) {
		print_r($row);
	}*/
	return $selectProduct;
}
?>

<?php // DB:customers ログイン情報から会員情報を取得
function getCustomerInfo( $loginInfo ) {
	$pdo = ccDBconection(); // データベースへの接続
	$sql = $pdo->prepare('SELECT * FROM customers WHERE mail=? and password=?'); // 登録情報の取得
	$sql->execute( [
		htmlspecialchars($loginInfo['mail']),
		htmlspecialchars($loginInfo['password']),
	] );
	$customerInfo = $sql->fetch(PDO::FETCH_ASSOC); // 名称keyで内容を取得 会員情報はユニーク
	unset($pdo); // データベースからの切断
	//
	return $customerInfo; // 名称keyの会員情報を返す
}
?>

<?php // DB:customers 既存ユーザーの存在チェック
function existCustomerCheck( $customer ) {
	// print_r($customer['id']);
	// print_r($customer['mail']);
	// print_r($customer['password']);
	//
	$pdo = ccDBconection(); // データベースへの接続
	$sql = $pdo->prepare("SELECT * FROM customers WHERE id!=? AND mail=? AND password=?"); // メールとパスワードでDB検索
	$sql->execute( [
		htmlspecialchars($customer['id']), 
		htmlspecialchars($customer['mail']), 
		htmlspecialchars($customer['password'])
		] );
	$existCustomers = $sql->fetchAll();
	unset($pdo); // データベースからの切断
	//print_r($existCustomers);
	//
	if( empty($existCustomers) ) {
		return false; // 既存の登録者がいなければfalse
	}
	else {
		return true; // 既存の登録者がいればtrue
	}
}
?>

<?php // DB:customers 会員登録(入力確認で登録前のチェックは終わっているものとする)
//  新規登録(true)/更新(false)を返す
function registCustomerInfo( $request ) {
	$pdo = ccDBconection(); //データベースへの接続
	if ( isset($_SESSION['customer']) ) { // ログインしているか否かのチェック
		// ログインしている(情報更新)時の処理
		$sql = $pdo->prepare('UPDATE customers 
								SET name=?, furigana=?, postcode_a=?, postcode_b=?, address=?, mail=?, password=?
								WHERE id=?');
		$sql->execute([
			htmlspecialchars($request['name']),
			htmlspecialchars($request['furigana']),
			htmlspecialchars($request['postcode_a']),
			htmlspecialchars($request['postcode_b']),
			htmlspecialchars($request['address']),
			htmlspecialchars($request['mail']),
			htmlspecialchars($request['password']),
			htmlspecialchars($_SESSION['customer']['id']),
		]);
		// ログイン中の情報を更新
		$temp_id = $_SESSION['customer']['id'];
		$_SESSION['customer'] = [
						  'id' => $temp_id,
						'name' => htmlspecialchars($request['name']),
					'furigana' => htmlspecialchars($request['furigana']),
				  'postcode_a' => sprintf('%03d',htmlspecialchars($request['postcode_a'])),
				  'postcode_b' => sprintf('%04d',htmlspecialchars($request['postcode_b'])),
					 'address' => htmlspecialchars($request['address']),
						'mail' => htmlspecialchars($request['mail']),
				'mailForCheck' => htmlspecialchars($request['mail']),
					'password' => htmlspecialchars($request['password']),
			'passwordForCheck' => htmlspecialchars($request['password']),
		];
		//
		$newRegistIs = false;
	}
	else {
		// ログインしていない(新規登録)時の処理
		$sql = $pdo->prepare('INSERT INTO customers VALUES(null, ?, ?, ?, ?, ?, ?, ?)');
		$sql->execute([
			htmlspecialchars($request['name']),
			htmlspecialchars($request['furigana']),
			htmlspecialchars($request['postcode_a']),
			htmlspecialchars($request['postcode_b']),
			htmlspecialchars($request['address']),
			htmlspecialchars($request['mail']),
			htmlspecialchars($request['password']),
		]);
		//
		$newRegistIs = true;
	}
	unset($pdo); // データベースからの切断
	//
	return $newRegistIs;
}
?>
<?php // 管理用のcustomerInfoのload
function loadAdminInfo() {
	$pdo = ccDBconection(); //データベースへの接続
	// 管理用の内容をロード
	$sql = $pdo->prepare("SELECT * FROM customers WHERE id=?");
	$sql->execute([0]);
	$adminINFO = $sql->fetch(PDO::FETCH_ASSOC); // 管理用データはユニーク
	unset($pdo); // データベースからの切断
	//
	if( empty($adminINFO) ) { $adminINFO = false; }
	//
	return $adminINFO;
}
?>
<?php 
function deleteCustomerInfo( $customer_id ) {
	// 管理用データのロード
	define( 'adminINFO',  loadAdminInfo() );
	if( !adminINFO ) {
		return false; // ロードに失敗
	}
	//var_dump(adminINFO);
	// 管理用のidで塗りつぶす
	$pdo = ccDBconection(); //データベースへの接続
	//
	// ログインしている(情報更新)時の処理
	try {
		$sql = $pdo->prepare('UPDATE customers 
								SET name=?, furigana=?, postcode_a=?, postcode_b=?, address=?, mail=?, password=?
								WHERE id=?');
		$sql->execute([
			htmlspecialchars(adminINFO['name']),
			htmlspecialchars(adminINFO['furigana']),
			htmlspecialchars(adminINFO['postcode_a']),
			htmlspecialchars(adminINFO['postcode_b']),
			htmlspecialchars(adminINFO['address']),
			htmlspecialchars(adminINFO['mail']),
			htmlspecialchars(adminINFO['password']),
			htmlspecialchars($customer_id),
		]);
		$flg = true;
	}
	catch(error) {
		$flg = false;
	}
	return $flg; // 処理の成否を返す
}
?>

<?php
function leaveStoreFunc($customer_id) {
	// お気に入りの登録削除
	$flg = deleteAllFavoriteByCutomerId( $customer_id );
	if( !$flg ) { return false; } // 失敗した時
	// カード登録の無効化
	// customer_idから登録カードを確認
	$tmpCards = getCardsInfoByCustomerId($customer_id);
	// 各カードのidで無効化処理
	foreach($tmpCards as $row) {
		$request['card_id'] = $row['id'];
		$flg = deleteCardInfo( $request );
	}
	if( !$flg ) { return false; } // 失敗した時
	// 会員登録の無効化
	$flg = deleteCustomerInfo( $customer_id );
	if( !$flg ) { return false; } // 失敗した時
	// 残るはログアウト
	return true;
}
?>
<?php // DB:favorite お気に入り取得
function getFavoriteListByCustomerId( $customer_id ) {
	try{
		$pdo = ccDBconection(); //データベースへの接続
		$sql = $pdo->prepare("SELECT * FROM favorite WHERE customer_id=?");
		$sql->execute( [
			htmlspecialchars( $customer_id ),
			] );
		$tmpList = $sql->fetchAll(PDO::FETCH_ASSOC);
		unset($pdo); // データベースからの切断
	}
	catch(PDOException $error) { $tmpList=[]; }
	return $tmpList;
}
?>
<?php // DB:favorite お気に入り取得 (商品情報を返す)
function customerFavoriteAll( $customer_id ) {
	// customer_idからお気に入りに登録されている内容を取得
	$temps = getFavoriteListByCustomerId( $customer_id );
	try{
		$pdo = ccDBconection(); //データベースへの接続
		// $sql = $pdo->prepare("SELECT * FROM favorite WHERE customer_id=?");
		// $sql->execute( [
		// 	htmlspecialchars( $customer_id ),
		// 	] );
		// $temps = $sql->fetchAll(PDO::FETCH_ASSOC);
		// unset($pdo); // データベースからの切断
		//
		// product_idを商品情報に変換
		$ccdSearchItems = [];
		foreach( $temps as $row ) {
			$favProductId = $row['product_id'];
			// 商品情報を検索
			$pdo = ccDBconection(); // データベースへの接続
			$sql = $pdo->prepare("SELECT * FROM products WHERE id=?"); // idで商品検索
			$sql->execute([
				htmlspecialchars($favProductId)
			]);
			$ccdSearchItems[] = $sql->fetch(PDO::FETCH_ASSOC); // 取得データを変数に置き換え(idと商品はユニーク)
			unset($pdo); // データベースからの切断
			//if(empty($ccdSearchItems)) { echo '登録がありません。'; }
		}
	}
	catch( PDOException $error ) { $ccdSearchItems = []; }
	//
	return $ccdSearchItems;
}
?>

<?php // DB:favorite お気に入り確認
function customerFavoriteIs($customer_id, $product_id) {
	// 商品がユーザーのお気に入りになっているか
	try{
		$pdo = ccDBconection(); //データベースへの接続
		$sql = $pdo->prepare("SELECT * FROM favorite WHERE customer_id=? AND product_id=?");
		$sql->execute( [
			htmlspecialchars($customer_id),
			htmlspecialchars($product_id)
			] );
		$existFavorite = $sql->fetchAll(PDO::FETCH_ASSOC);
	}
	catch( PDOException $error) { $existFavorite = []; }
	//
	if( count($existFavorite) > 0 ) {
		$favoriteIs = true;
	}
	else {
		$favoriteIs = false;
	}
	//
	return $favoriteIs;
}
?>
<?php // お気に入りの削除
function deleteAllFavoriteByCutomerId( $customer_id ) {
	try {
		$pdo = ccDBconection(); //データベースへの接続
		$sql = $pdo->prepare("DELETE FROM favorite WHERE customer_id=?");
		$sql->execute( [
			htmlspecialchars($customer_id)
		] );
		$flg = true;
	}
	catch( PDOException $error ) {
		$flg = false;
	}
	return $flg;
}
?>
<?php // お気に入り処理
function toggleFavorite($customer_id, $product_id) {
	try{
		// お気に入りの確認
		$favoriteIs = customerFavoriteIs($customer_id, $product_id);
		//echo 'pre:::'.$favoriteIs;
		//
		$pdo = ccDBconection(); //データベースへの接続
		if( $favoriteIs ) {
			// お気に入りなら...お気に入りから外す
			$sql = $pdo->prepare("DELETE FROM favorite WHERE customer_id=? AND product_id=?");
		}
		else {
			// お気に入りじゃないなら...お気に入りに登録
			$sql = $pdo->prepare('INSERT INTO favorite VALUES(?, ?)');
		}
		$sql->execute( [
			htmlspecialchars($customer_id),
			htmlspecialchars($product_id)
		] );
		$favoriteIs = !$favoriteIs;
	}
	catch( PDOException $error) { $favoriteIs = false; }
	//echo 'after:::'.$favoriteIs;
	//
	return $favoriteIs;
}
?>

<?php // カード情報の取得 (customer_id)
function getCardsInfoByCustomerId($customer_id) {
	$pdo = ccDBconection(); //データベースへの接続
	try {
		$sql = $pdo->prepare("SELECT * FROM cards WHERE customer_id=?"); // 新商品取得
		$sql->execute([ htmlspecialchars($customer_id) ]);
		$userCardInfos = $sql->fetchAll(PDO::FETCH_ASSOC); // 取得データを変数に置き換え
		unset($pdo); // データベースからの切断
	}
	catch( PDOException $error) {
		$userCardInfos[] = null;
	}
	//
	return $userCardInfos; // 1つ目をお目当てのカード情報としておく。
}
?>
<?php // カード情報の取得 (card_id)
function getCardInfoByCardId($card_id) {
	$pdo = ccDBconection(); //データベースへの接続
	try {
		$sql = $pdo->prepare("SELECT * FROM cards WHERE id=?"); // 新商品取得
		$sql->execute([ htmlspecialchars($card_id) ]);
		$userCardInfo = $sql->fetch(PDO::FETCH_ASSOC); // card_idはユニーク
		unset($pdo); // データベースからの切断
	}
	catch( PDOException $error) {
		$userCardInfo = null;
	}
	//
	return $userCardInfo; // 1つ目をお目当てのカード情報としておく。
}
?>

<?php // 登録カードの存在チェック => 検索結果を返す。
function existCardCheck( $cardInfo ) {
	//
	$pdo = ccDBconection(); // データベースへの接続
	try{
		$sql = $pdo->prepare("SELECT * FROM cards WHERE customer_id=? AND company=?"); // customer_idでDB検索
		$sql->execute( [
			htmlspecialchars($cardInfo['customer_id']),
			htmlspecialchars($cardInfo['company'])
			] );
		$existCards = $sql->fetchAll();
		unset($pdo); // データベースからの切断
	}
	catch( PDOException $error) {
		$existCards = [];
	}
	//print_r($existCustomers);
	//
	return $existCards;
}
?>

<?php // カード登録(入力確認で登録前のチェックは終わっているものとする)
//  新規登録(true)/更新(false)を返す
function registCardInfo( $request, $option='ADD' ) {
	// echo 'card_id:::'.$request['card_id'].'<br>';
	// echo 'option:::'.$option.'<br>';
	$pdo = ccDBconection(); //データベースへの接続
	$registIs = false;
	if( $option == 'UPDATA' ) {
		// 更新の時
		$sql = $pdo->prepare('UPDATE cards 
				SET name=?, number=?, company=?, limit_year=?, limit_month=?, code=?
				WHERE id=?');
		$sql->execute([
			htmlspecialchars($request['name']),
			htmlspecialchars($request['number']),
			htmlspecialchars($request['company']),
			htmlspecialchars($request['limit_year']),
			htmlspecialchars($request['limit_month']),
			htmlspecialchars($request['code']),
			htmlspecialchars($request['card_id']),
		]);
		//
		$registIs = true;
	}
	else if($option == 'ADD') {
		//echo 'tsuika';
		// 新規登録
		// 既存登録されているかチェック　 => 既存があれば、更新になる。なければ新規追加に。
		// 存在を判定して、自動で切り替える？ <= 入力内容チェックの段階で、登録の重複をチェック
		//
		$sql = $pdo->prepare('INSERT INTO cards VALUES(null, ?, ?, ?, ?, ?, ?, ?)');
		$sql->execute([
			htmlspecialchars($request['customer_id']),
			htmlspecialchars($request['name']),
			htmlspecialchars($request['number']),
			htmlspecialchars($request['company']),
			htmlspecialchars($request['limit_year']),
			htmlspecialchars($request['limit_month']),
			htmlspecialchars($request['code']),
		]);
		//
		$registIs = true;
	}
	else { $registIs = false; }
	unset($pdo); // データベースからの切断
	return $registIs;
}
?>
<?php
function deleteCardInfo( $request ) {
	$pdo = ccDBconection(); //データベースへの接続
	$registIs = false;
	try{
	// アクセスできない様に塗りつぶす
	$sql = $pdo->prepare("UPDATE cards 
			SET customer_id=?, name=?, number=?, company=?, limit_year=?, limit_month=?, code=?
			WHERE id=?");
	$sql->execute([
			0, 'xxxx', 0, 0, 0, 0, 'xxxx',
			htmlspecialchars($request['card_id']),
		]);
	unset($pdo); // データベースからの切断
	//
	$flg = true;
	}
	catch( PDOException $error) {
		echo $error;
		$flg = false;
	}
	return $flg;
}
?>
<?php // 購入処理
function excutePurchase( $purchaseInfo ) {
	// purchase_idの設定
	$purchase_id = getPurchaseIdMax() + 1;
	// DB:purchaseへの登録
	$flg = registPurchase($purchase_id, $purchaseInfo['customer_id'], $purchaseInfo['card_id']);
	if($flg) {
		// DB:purchase_datailへの登録
		$flg = registPurchase_detail($purchase_id, $purchaseInfo['cartInfo']);
	}
	return $flg;
}
?>
<?php // DB:purchaseからidの最大値を取得
function getPurchaseIdMax() {
	$pdo = ccDBconection(); //データベースへの接続
	$sql = $pdo->prepare('SELECT * FROM purchase');
	$sql->execute();
	$allPurchaseInfo = $sql->fetchAll();
	// 情報から最大値を検索
	$max = 0;
	foreach($allPurchaseInfo as $row){
		if($max < $row['id']) {
			$max = $row['id'];
		}
	}
	return $max;
}
?>
<?php // DB:purchaseへの登録
function registPurchase($purchase_id, $customer_id, $card_id) {
	// 購入日時チェック
	date_default_timezone_set('Japan');
	$nowYMD = date('ymd');
	$flg = false;
	//
	$pdo = ccDBconection(); //データベースへの接続
	$sql = $pdo->prepare('INSERT INTO purchase VALUES(?, ?, ?, ?)');
	$flg = $sql->execute([
		htmlspecialchars($purchase_id),
		htmlspecialchars($customer_id),
		htmlspecialchars($card_id),
		htmlspecialchars($nowYMD),
	]);
	unset($pdo); // データベースからの切断
	return $flg;
}
?>

<?php // DB:purchase_datailへの登録
function registPurchase_detail($purchase_id, $cartInfo) {
	foreach($cartInfo as $row) {
		$flg = false;
		$pdo = ccDBconection(); //データベースへの接続
		$sql = $pdo->prepare('INSERT INTO purchase_detail VALUES(?, ?, ?)');
		$flg = $sql->execute([
			htmlspecialchars($purchase_id),
			htmlspecialchars($row['id']),
			htmlspecialchars($row['count']),
		]);
		if(!$flg) { break; }
	}
	unset($pdo); // データベースからの切断
	return $flg;
}
?>

<?php // DB:product_id からランキング取得（購入総個数で降順）
function getPurchasedProducts( $duration='' ) {
	$pdo = ccDBconection(); //データベースへの接続
	//$sql = $pdo->prepare('SELECT * FROM purchase_detail ORDER BY count DESC'); // count降順で取得
	$sql = $pdo->prepare('SELECT product_id, SUM(count) AS sum FROM purchase_detail GROUP BY product_id ORDER BY sum DESC;');
	$sql->execute();
	$purchasedProducts = $sql->fetchAll(PDO::FETCH_ASSOC); // 名称keyで内容を取得
	unset($pdo); // データベースからの切断
	return $purchasedProducts;
}
?>

<?php // DB:purchase->purchase_detail から購入履歴を取得
function getPurchaseHistory($customer_id) {
	//echo 'getPurchaseHistory:::'.$customer_id.'<br>';
	$pdo = ccDBconection(); //データベースへの接続
	// 購入情報のID(purchase_id)から購入内容の取得
	$purchaseProductIdList = []; // 購入商品リストの初期化
	try {
		$sql = $pdo->prepare('
					SELECT DISTINCT product_id FROM purchase_detail 
					WHERE purchase_id IN (SELECT id FROM purchase WHERE customer_id=?)'); // 購入商品の取得(重複なし)
		$sql->execute([ htmlspecialchars($customer_id) ]);
		$tmpProductIdList = $sql->fetchAll(PDO::FETCH_ASSOC); // 名称keyで内容を取得
		unset($pdo); // データベースからの切断
	}
	catch( PDOException $error) {
		$tmpProductIdList[] = null;
	}
	//print_r($tmpProductIdList);
	/* $purchaseProductIdList 未登録ならpush
	foreach($tmpProductIdList as $row2) {
		if( !array_search( $row2['product_id'], $purchaseProductIdList ) ) {
			$purchaseProductIdList[] = $row2['product_id'];
		}
	}*/
	
	$purchaseProductInfos = [];
	foreach($tmpProductIdList as $row) { // 取得したidを商品情報に変換
		$purchaseProductInfos[] = getDetailById( $row['product_id'] ); 
	}
	return $purchaseProductInfos; // 購入商品情報を返す。
}
?>

<?php // 購入可能確認
function codeCheck($card_id, $code) {
	$sCode = getSCodeByCardId($card_id); // 指定されているコードの取得
	// echo 'card_ig : '.$card_id.'<br>';
	// echo 'sCode ::: '.$sCode.'<br>';
	// echo 'code :::: '.$code.'<br>';
	if($code == $sCode) { return true; }
	else { return false; }
}
?>
<?php // 指定されているコードの取得
function getSCodeByCardId($card_id) {
	$pdo = ccDBconection(); //データベースへの接続
	try {
		$sql = $pdo->prepare("
					SELECT code FROM cards WHERE id=? "); // セキュリティコードの取得
		$sql->execute([ htmlspecialchars($card_id) ]);
		$tmpCode = $sql->fetch(PDO::FETCH_ASSOC)['code']; // card_idはユニーク codeで返す
		unset($pdo); // データベースからの切断
	}
	catch( PDOException $error) {
		$tmpCode = false;
	}
	return $tmpCode;
}
?>