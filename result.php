<?php
// result.php
session_start();

// セッションに画像データがなければトップページに戻す
if (!isset($_SESSION['degraded_image'])) {
    header("Location: index.html");
    exit;
}

// セッションから画像データを取得し、Base64にエンコードしてimgタグで表示できるようにする
$imageData = $_SESSION['degraded_image'];
$imageBase64 = 'data:image/jpeg;base64,' . base64_encode($imageData);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>劣化処理の結果</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="upload-container">
        <div class="result-container">
            <h1>処理が完了しました</h1>
            <p>以下が劣化処理後の画像です。</p>

            <img src="<?php echo $imageBase64; ?>" alt="劣化した画像" class="result-image">

            <div class="button-group">
                <a href="download.php">ダウンロード</a>
                <a href="index.html" class="secondary">最初からやり直す</a>
            </div>
        </div>
    </div>
</body>
</html>