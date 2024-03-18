<main>
	<section class="login">
		<div class="title">
			<h2>ログイン
				<?php
					if(isset($_SESSION['customer'])) { echo '完了'; }
					else { echo '失敗'; }
				?>
			</h2>
		</div>
		<div class="messageWrap">
			<p>ログインが
				<?php
					if(isset($_SESSION['customer'])) { echo '完了'; }
					else { echo '失敗'; }
				?>
				しました。</p>
			<p>
				<?php
					if(isset($_SESSION['customer'])) { echo '引き続きお楽しみください。'; }
					else { echo 'ログインをやり直してください。'; }
				?>
			</p>
		</div>
		<div class="linkWrap">
			<?php
				if(isset($_SESSION['customer'])) {
					echo '<div><a href="'.$rootDir.'purchase.php?phase=Entry">購入確認ページへ進む</a></div>';
					echo '<div><a href="'.$rootDir.'index.php">TOPページへもどる</a></div>';
				}
				else {
					echo '<div><a href="'.$rootDir.'login.php?phase=Entry">ログインページへもどる</a></div>';
				}
			?>
		</div>
	</section>
</main>