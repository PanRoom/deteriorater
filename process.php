<?php
// process.php

session_start();

// POSTリクエストでない場合は、トップページにリダイレクト
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit;
}

// ファイルアップロードやレベル指定にエラーがある場合は処理を中断
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK || !isset($_POST['level'])) {
    die('エラー: 不正なリクエストです。');
}

// アップロードされた一時ファイル名と劣化レベルを取得
$tmpName = $_FILES['image']['tmp_name'];
$level = $_POST['level'];

// 劣化レベルに応じて、繰り返しの回数とJPEG品質のパターンを設定
$iterations = 0;
$qualities = [];

switch ($level) {
    case 'weak':
        $iterations = 5; // 劣化（弱）は5回繰り返す
        $qualities = [51, 50, 49, 48, 47]; // 比較的高品質で再圧縮
        break;
    case 'strong':
        $iterations = 12; // 劣化（強）は12回繰り返す
        $qualities = [27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16]; // 低品質で再圧縮
        break;
    case 'medium':
    default:
        $iterations = 8; // 劣化（中）は8回繰り返す
        $qualities = [31, 30, 29, 28, 27, 26, 25, 24]; // 標準的な品質で再圧縮
        break;
}

// 画像情報が取得できるかチェック
$imageInfo = getimagesize($tmpName);
if ($imageInfo === false) {
    die('エラー: 有効な画像ファイルではありません。');
}

// GDライブラリを使って画像リソースを作成
$image = imagecreatefromstring(file_get_contents($tmpName));
if ($image === false) {
    die('エラー: 画像を読み込めませんでした。');
}

// 設定された回数だけ、JPEGの再圧縮を繰り返す
for ($i = 0; $i < $iterations; $i++) {
    // $qualities配列の要素数を超えた場合、インデックスをループさせる
    $quality = $qualities[$i % count($qualities)];

    // バッファリングを開始し、指定された品質でJPEGデータを生成
    ob_start();
    imagejpeg($image, null, $quality);
    $jpegData = ob_get_clean();
    
    // 古い画像リソースを破棄し、新しいJPEGデータから再度画像リソースを作成
    imagedestroy($image);
    $image = imagecreatefromstring($jpegData);
}

// 最終的な画像データを生成
ob_start();
imagejpeg($image);
$finalImageData = ob_get_clean();
imagedestroy($image);

// 処理後の画像データをセッションに保存
$_SESSION['degraded_image'] = $finalImageData;

// 結果表示ページにリダイレクト
header("Location: result.php");
exit;