<?php // セッションデータの確認
	$email = '';
	$password = '';
	if(isset($_SESSION['customer'])) {
		$email = $_SESSION['customer']['mail'];
		$password = $_SESSION['customer']['password'];
	}
?>

<main>
	<section class="login">
		<div class="title">
			<h2>ログイン</h2>
		</div>
		<form action=<?php echo $rootDir.'login.php?phase=Result'; ?> method="post">
			<?php
				if(isset($_SESSION['customer'])) {
					echo '<p class="caution">'.$_SESSION['customer']['name'].' 様で、既にログインされています。</p>';
				}
			?>
			<label>
				<span>メールアドレス</span>
				<input type="email" name="email" value="<?php echo $email; ?>" required>
			</label>
			<label>
				<span>パスワード</span>
				<input type="password" name="password" value="<?php echo $password; ?>" required>
			</label>
			<div class="btnWrap">
				<input class="commonBtnSetting" type="submit" value="ログインする">
			</div>
		</form>
		<div class="linkWrap">
			<div>
				<a href="<?php echo $rootDir.'entryStore.php?phase=Entry'; ?>">会員登録はこちら</a>
			</div>
		</div>
	</section>
</main>