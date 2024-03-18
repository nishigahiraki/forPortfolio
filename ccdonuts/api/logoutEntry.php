<main>
	<section class="logout entry">
		<div class="title">
			<h2>ログアウト</h2>
		</div>
		<div class="messageWrap">
			<p>
				カートの内容がリセットされます。
			</p>
			<div class="btnWrap">
				<a class="commonBtnSetting" href=<?php echo $rootDir.'logout.php?phase=Result'; ?>>ログアウトする</a>
			</div>
		</div>
		<div class="linkWrap">
			<?php
				echo '<div><a href="'.$rootDir.'index.php">TOPページへもどる</a></div>';
				echo '<div><a href="'.$rootDir.'login.php?phase=Entry">ログインページへもどる</a></div>';
			?>
		</div>
	</section>
</main>