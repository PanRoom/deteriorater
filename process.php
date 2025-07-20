<?php
// process.php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK || !isset($_POST['level'])) {
    die('エラー: 不正なリクエストです。');
}

$tmpName = $_FILES['image']['tmp_name'];
$level = $_POST['level'];

// 劣化レベルに応じて、品質のパターンと回数を設定
$iterations = 0;
$qualities = [];

switch ($level) {
    case 'weak':
        $iterations = 5;
        $qualities = [51, 50, 49, 48, 47]; // 高品質で少しだけ変換
        break;
    case 'strong':
        $iterations = 12;
        $qualities = [27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16]; // 低品質で何度も変換
        break;
    case 'medium':
    default:
        $iterations = 8;
        $qualities = [31, 30, 29, 28, 27, 26, 25, 24]; // 標準的な劣化
        break;
}

$imageInfo = getimagesize($tmpName);
if ($imageInfo === false) { die('エラー: 有効な画像ファイルではありません。'); }

$image = imagecreatefromstring(file_get_contents($tmpName));
if ($image === false) { die('エラー: 画像を読み込めませんでした。'); }

// 設定された回数と品質でループ処理
for ($i = 0; $i < $iterations; $i++) {
    // qualities配列の要素数を超えた場合、配列をループさせる
    $quality = $qualities[$i % count($qualities)];

    ob_start();
    imagejpeg($image, null, $quality); // 可変の品質を適用
    $jpegData = ob_get_clean();
    
    imagedestroy($image);
    $image = imagecreatefromstring($jpegData);
}

ob_start();
imagejpeg($image);
$finalImageData = ob_get_clean();
imagedestroy($image);

$_SESSION['degraded_image'] = $finalImageData;

header("Location: result.php");
exit;