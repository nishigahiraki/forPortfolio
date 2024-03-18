<?php // DBへ接続
function ccDBconectionPost() { // SQLデータベースへの接続
    $nowConection = $_SERVER['SERVER_NAME'];
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

<?php // DBへデータを書き込み
function inputPosts($csvURL){
	echo 'CSVよみこみ　はじめ';
    // KEN_ALL-utf8.CSV //
    $posts = [];
    $shichosonList = [];
    if (($handle = fopen($csvURL, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            $tmpAry=[];
            $tmpAry=[
                'postcode_a'=> $data[1],
                'postcode'=> $data[2],
                'ken'=> $data[6],
                'city'=> $data[7],
                'other'=> $data[8],
            ];
            array_push($shichosonList, $data[7]);
            array_push($posts,$tmpAry);
        }
        fclose($handle);

            $pdo = ccDBconectionPost(); // データベースへ接続
            $sql = $pdo->prepare('INSERT INTO posts VALUES(null, ?, ?, ?, ?, ?)');
            
            //
            foreach($posts as $p) {
                $sql->execute([
                    $p['postcode_a'],
                    $p['postcode'],
                    $p['ken'],
                    $p['city'],
                    $p['other']
                ]);
            }

    }
	echo 'CSVよみこみ　おわり';
}
?>

<?php // 住所で検索
function getPostcodeByAddress($ken, $city, $other){
    // 引継ぎ
    if(isset($ken)) { $thisKen = $ken; }
    else { $thiKen = ''; }
    if(isset($city)) { $thisCity = $city; }
    else { $thisCity = ''; }
    if(isset($other)) { $thisOther = $other; }
    else { $thisOther = ''; }
    //
    $pdo = ccDBconectionPost(); // データベースへ接続
    $sql = $pdo->prepare("SELECT * FROM posts WHERE ken LIKE ? AND city LIKE ? AND other LIKE ?"); // 住所取得
	$sql->execute([
		'%'.htmlspecialchars($thisKen).'%',
        '%'.htmlspecialchars($thisCity).'%',
        '%'.htmlspecialchars($thisOther).'%',
	]);
	$result = $sql->fetchAll(); // 取得データを変数に置き換え
	unset($pdo); // データベースからの切断
	//if(empty($ccdSearchItems)) { echo '登録がありません。'; }
	return $result;
}
?>

<?php // 郵便番号で検索
function getAddressByPostcode($postcode) {
    if(!empty($postcode)){
        $pdo = ccDBconectionPost(); // データベースへ接続
        $sql = $pdo->prepare("SELECT * FROM posts WHERE postcode LIKE ?"); // 住所取得
        $sql->execute([
            '%'.htmlspecialchars($postcode).'%',
        ]);
        $result = $sql->fetchAll(); // 取得データを変数に置き換え
        unset($pdo); // データベースからの切断
        //if(empty($ccdSearchItems)) { echo '登録がありません。'; }
        return $result;
    }
}
?>