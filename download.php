<?php
// download.php
session_start();

// セッションに画像データがなければ何もしない
if (!isset($_SESSION['degraded_image'])) {
    exit;
}

// セッションから画像データを取得
$imageData = $_SESSION['degraded_image'];

// セッションデータを削除（再利用を防ぐため）
unset($_SESSION['degraded_image']);

// HTTPヘッダーを設定してダウンロードさせる
header('Content-Type: image/jpeg');
header('Content-Disposition: attachment; filename="degraded_image.jpg"');
header('Content-Length: ' . strlen($imageData));

// 画像データを出力
echo $imageData;

exit;