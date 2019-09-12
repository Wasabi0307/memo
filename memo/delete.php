<?php

// データベースの接続情報
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', 'root');
define( 'DB_NAME', 'board');
// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$message_id = null;
$mysqli = null;
$sql = null;
$res = null;
$error_message = array();
$message_data = array();

session_start();

if( !empty($_GET['message_id']) && empty($_POST['message_id']) ) {
	$message_id = (int)h($_GET['message_id'], ENT_QUOTES);
	// データベースに接続
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
	
	// 接続エラーの確認
	if( $mysqli->connect_errno ) {
		$error_message[] = 'データベースの接続に失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
	} else {
	
		// データの読み込み
		$sql = "SELECT * FROM message WHERE id = $message_id";
		$res = $mysqli->query($sql);
		
		if( $res ) {
			$message_data = $res->fetch_assoc();
		} else {
		
			// データが読み込めなかったら一覧に戻る
			header("Location: ./index.php");
		}
		
		$mysqli->close();
	}

} elseif( !empty($_POST['message_id']) ) {
	$message_id = (int)h( $_POST['message_id'], ENT_QUOTES);
	// データベースに接続
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
	
	// 接続エラーの確認
	if( $mysqli->connect_errno ) {
		$error_message[] = 'データベースの接続に失敗しました。 エラー番号 ' . $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
	} else {
		$sql = "DELETE FROM message WHERE id = $message_id";
		$res = $mysqli->query($sql);
	}
	
	$mysqli->close();
		
		// 更新に成功したら一覧に戻る
		if( $res ) {
           
			header("Location: ./index.php");
		}
}


//htmlspecialcharsのショートカット
function h($value){
    return htmlspecialchars($value, ENT_QUOTES);
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>

    <title>削除</title>
    <link rel="stylesheet" type=text/css href="style.css">
</head>

<body>

    <!--メインコンテンツ-->
    <h1>削除しますか？</h1>
    <?php if( !empty($error_message) ): ?>
    <ul class="error_message">
        <?php  foreach( $error_message as $value ): ?>
        <li>・<?php echo $value; ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <form method=post>
        <div>
            <textarea id="message" name="message" onkeyup="viewStrLen();" cols="50" rows="10" placeholder="ここに書いてね" disabled><?php if(!empty($message_data['message']) ){ echo $message_data['message']; }?></textarea>
        </div>
        <a class="btn-cancel" href="index.php">キャンセル</a>
        <input type="submit" name="btn_submit" class="btn-square" value="削除">
        <input type="hidden" name="message_id" value="<?php echo $message_data['id']; ?>">
    </form>

</body>

</html>
