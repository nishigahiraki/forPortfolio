<main>
<?php echo $requestOption; ?>
	<section class="logout result">
		<div class="title">
			<h2>ログアウト</h2>
		</div>
		<div class="messageWrap">
			<p class="<?php if($requestOption != 'OUT') { echo 'displayNone'; } ?>">
				退会処理が完了しました
			</p>
			<p class="<?php if($requestOption == 'OUT') { echo 'displayNone'; } ?>">
				ログアウトしました。
			</p>
		</div>
		<div class="linkWrap">
			<?php
				echo '<div><a href="'.$rootDir.'index.php">TOPページへもどる</a></div>';
				if($requestOption != 'OUT') {
					echo '<div><a href="'.$rootDir.'login.php?phase=Entry">ログインページへもどる</a></div>';
				}
			?>
		</div>
	</section>
</main>