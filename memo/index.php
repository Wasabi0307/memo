<?php 
// データベースの接続情報
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', 'root');
define( 'DB_NAME', 'board');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message_array = array();
$error_message = array();
$clean = array();

session_start();

if( !empty($_POST['btn_submit']) ) {
	
	// メッセージの入力チェック
	if( empty($_POST['message']) ) {
		$error_message[] = 'なにか入力してください。';
	} else {
		$clean['message'] = h( $_POST['message'], ENT_QUOTES);
	}

	if( empty($error_message) ) {
		
		// データベースに接続
		$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
		// 接続エラーの確認
		if( $mysqli->connect_errno ) {
			$error_message[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
		} else {

			// 文字コード設定
			$mysqli->set_charset('utf8');
			
			// 書き込み日時を取得
			$now_date = date("Y-m-d H:i:s");
			
			// データを登録するSQL作成
			$sql = "INSERT INTO message (message, post_date) VALUES (  '$clean[message]', '$now_date')";
			
			// データを登録
			$res = $mysqli->query($sql);
		
			if( $res ) {
				$_SESSION['success_message'] = '書き込みに成功しました。';
			} else {
				$error_message[] = '書き込みに失敗しました。';
			}
		
			// データベースの接続を閉じる
			$mysqli->close();
		}

		header('Location: ./');
	}
}

// データベースに接続
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 接続エラーの確認
if( $mysqli->connect_errno ) {
	$error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {

	$sql = "SELECT id,message,post_date FROM message ORDER BY post_date DESC";
	$res = $mysqli->query($sql);

    if( $res ) {
		$message_array = $res->fetch_all(MYSQLI_ASSOC);
    }

    $mysqli->close();
}

//htmlspecialcharsのショートカット
function h($value){
    return htmlspecialchars($value, ENT_QUOTES);
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>

    <title>メモ</title>
    <script src="js/count.js"></script>
    <link rel="stylesheet" type=text/css href="style.css">
</head>

<body>

    <!--メインコンテンツ-->
    <h1>メモ</h1>
    <?php if( empty($_POST['btn_submit']) && !empty($_SESSION['success_message']) ): ?>
    <p class="success_message"><?php echo $_SESSION['success_message']; ?></p>
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if( !empty($error_message) ): ?>
    <ul class="error_message">
        <?php foreach( $error_message as $value ): ?>
        <li>・<?php echo $value; ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <form method=post>
        <div>
            <textarea id="area1" id="message" name="message" onkeyup="viewStrLen();" cols="50" rows="10" placeholder="ここに書いてね"></textarea>
        </div>
        <input type="submit" name="btn_submit" value="登録" class="btn-square">
        <button type="submit" name="btn_submit" value="delete" class="btn-square" class="disable" disabled href="delete.php">選択削除</button>
        <p id="strLen">文字</p>
    </form>
    <section>

        <?php if(!empty($message_array) ){ ?>
        <?php foreach($message_array as $value ){ ?>
        <article>
            <div class="info" method="post">
                <h2><?php echo $value['view_name']; ?></h2>
                <time><?php echo date('Y年m月d日 H:i',strtotime($value['post_date'])); ?></time>
                   <?php if($_SESSION['id']); ?>
                   <a class="btn-button" href="edit.php?message_id=<?php echo $value['id']; ?>">編集</a>  
                   <a class="btn-button" href="delete.php?message_id=<?php echo $value['id']; ?>">削除</a>
                   <input type="checkbox" name="submit">
            </div>
            <p><?php echo nl2br($value['message']) ?></p>
        </article>
        <?php } ?>
        <?php } ?>
    </section>
</body>

</html>
