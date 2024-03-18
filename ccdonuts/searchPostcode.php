<?php session_start(); ?>

<?php // ページ情報
	$pageTitle = 'C.C.Donuts | 住所検索';
	$rootDir = './';
	$breadInfo = [
		['name' => 'TOP', 'url' => $rootDir.'index.php'],
		['name' => '会員登録', 'url' => $rootDir.'entryStore.php?phase=Entry'],
		['name' => '住所検索', 'url' => $rootDir.'searchPostcode.php'],
	];
?>

<?php // 入力データの引き継ぎ
	$customer = [
				  'id' => htmlspecialchars($_REQUEST['id']),
				'name' => htmlspecialchars($_REQUEST['name']),
			'furigana' => htmlspecialchars(mb_convert_kana($_REQUEST['furigana'],'asKCV')),
		  'postcode_a' => htmlspecialchars(mb_convert_kana($_REQUEST['postcode_a'],'a')),
		  'postcode_b' => htmlspecialchars(mb_convert_kana($_REQUEST['postcode_b'],'a')),
			 'address' => htmlspecialchars(mb_convert_kana($_REQUEST['address'],'asKV')),
				'mail' => htmlspecialchars(($_REQUEST['mail'])),
		'mailForCheck' => htmlspecialchars(mb_convert_kana($_REQUEST['mailForCheck'],'as')),
			'password' => htmlspecialchars(mb_convert_kana($_REQUEST['password'],'as')),
	'passwordForCheck' => htmlspecialchars(mb_convert_kana($_REQUEST['passwordForCheck'],'as')),
	];
	$keyList = array_keys($customer);
?>

<?php 
require './api/functionsForPost.php';
if( !empty($_REQUEST['postcode']) ) {
    // 郵便番号で検索
    $postcode = $_REQUEST['postcode'];
    $results = getAddressByPostcode($postcode);
}
else if( !empty($_REQUEST['ken']) || !empty($_REQUEST['city']) || !empty($_REQUEST['other']) ) {
    // 住所で検索
    if( isset($_REQUEST['ken']) ){ $request['ken'] = $_REQUEST['ken']; }
    else { $request['city'] = ''; }
    if( isset($_REQUEST['city']) ){ $request['city'] = $_REQUEST['city']; }
    else { $request['city'] = ''; }
    if( isset($_REQUEST['other']) ){ $request['other'] = $_REQUEST['other']; }
    else { $request['other'] = ''; }
    //
    // DBから検索取得
    $results = getPostcodeByAddress($request['ken'], $request['city'], $request['other']);
}
if(!empty($results)) {
    // 結果があれば表示
    $formedResults = [];
    foreach( $results as $row) {
        /*
        $tmp = [
            'postcode' => $row['postcode'],
            'address'=> $row['ken'].$row['city'].$row['other'],
        ];
        array_push($formedResults, $tmp);
        */
        $formedResults[$row['ken']][$row['city']][] = [
            'postcode' => $row['postcode'],
            'ken' => $row['ken'],
            'city' => $row['city'],
            'other' => $row['other'],
        ];
    }
}
?>

<?php
if( isset($_REQUEST['load']) ) {
	$csvURL = $rootDir.'common/postcode/KEN_ALL-utf8-'.$_REQUEST['load'].'.CSV';
	inputPosts($csvURL);
}
?>

<?php require $rootDir.'common/header.php'; ?>
<main>
	<!--a href="<?php echo $rootDir.'searchPostcode.php?load=shizuoka'; ?>">取込[北海道〜静岡]</a><br>
	<a href="<?php echo $rootDir.'searchPostcode.php?load=aichi'; ?>">取込[愛知〜沖縄]</a-->
	<?php
	if( isset($_REQUEST['postcode']) ) {
		if(!empty($formedResults)){
			echo '<section class="searchPostcode result">';
			echo '    <div class="title">';
			echo '        <h2>検索結果</h2>';
			echo '    </div>';
			echo '    <p>'.count($results).'件ヒットしました。</p>';
			echo '    <div class="resultWrap">';  
				/*foreach( $formedResults as $row ) {
					echo '<form>';
					echo '<input type="submit" value="使用">';
					echo $row['postcode'].' : '.$row['address'];
					echo '</form>';
				}*/
			$kenKeys = array_keys($formedResults);
			$kenCount = 0;
			foreach( $kenKeys as $kenK ) {
				echo '<ul class="ken"><label>'.$kenK;
				$kenCount++;
				echo '<input type="checkbox" id="ken'.$kenCount.'"></label>';
				$cityKeys = array_keys($formedResults[$kenK]);
				//
				$cityCount = 0;
				foreach( $cityKeys as $cityK ) {
					echo '<li><ul class="city"><label>┣'.$cityK;
					$cityCount++;
					echo '<input type="checkbox" id="ken'.$kenCount.'city'.$cityCount.'"></label>';
					//
					$results = $formedResults[$kenK][$cityK];
					foreach($results as $row){
						echo '<li class="postcode">┃┗'.$row['postcode'].' : ';
						echo $row['other'];
						echo '<form action="'.$rootDir.'entryStore.php?phase=Entry" method="POST" >';
						foreach($keyList as $key) {
							echo '<input type="hidden" name="'.$key.'" value="'.$customer[$key].'">';
						}
						$postcodeA = substr($row['postcode'], 0, 3);
						$postcodeB = substr($row['postcode'], 3);
						print <<<_HTML_
							<input type="hidden" name="postcode_a" value="$postcodeA">
							<input type="hidden" name="postcode_b" value="$postcodeB">
							<input type="hidden" name="address" value="$row[ken] $row[city] $row[other]">
						_HTML_;
						echo ' <input type="submit" value="使用する"></form></li>';
					}
					echo '</ul></li>';
				}
				echo '</ul>';
			}
			echo '    </div>';     
			echo '</section>';
		}
		else {
			$messages = [];
			$messages[] = '検索結果は、0 件です。';
			//
			$messageSet =[
				'parentSection' => 'searchPostcode result',
				'messages' => $messages,
				'forBtnWrap' => [],
				'forLinkWraps' => []
			];
			require $rootDir.'api/functions.php';
			viewErrorMes($messageSet); // エラーメッセージとして表示
		}
	}
	?>
	<section class="login">
		<div class="title">
			<h2>住所検索</h2>
		</div>
		<form method="POST">
			<label><span>郵便番号：</span><input type="text" name="postcode" placeholder="1234567"></label>
			<label><span>都道府県：</span><input type="text" name="ken" placeholder="○○県（県不要）"></label>
			<label><span>市町村：</span><input type="text" name="city" placeholder="△△市（市不要）"></label>
			<label><span>その他：</span><input type="text" name="other" placeholder="□□（町目,番地の前まで）"></label>
			<?php
				foreach($keyList as $key) {
					echo '<input type="hidden" name="'.$key.'" value="'.$customer[$key].'">';
				}
			?>
			<div class="btnWrap">
				<input class="commonBtnSetting" type="submit" 
					formaction="./entryStore.php?phase=Entry"
					value="もどる">
				<input class="commonBtnSetting" type="submit" 
					formaction="./searchPostcode.php" value="検索する">
			</div>
		</form>
		<div class="linkWrap"></div>
	</section>
</main>
<?php require $rootDir.'common/footer.php'; ?>

