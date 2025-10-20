// script.js

// --- DOM Element Selection ---
const levelSelectors = document.querySelectorAll('.radio-label'); // 劣化レベル選択のラベル
const form = document.getElementById('upload-form'); // アップロードフォーム
const submitButton = document.getElementById('submit-button'); // 送信ボタン
const loader = document.getElementById('loader'); // ローディングスピナー
const uploadArea = document.getElementById('upload-area'); // ドラッグ＆ドロップエリア
const fileInput = document.getElementById('file-input'); // ファイル選択のinput要素
const imagePreview = document.getElementById('image-preview'); // 画像プレビュー表示エリア

// --- Event Listeners ---

// 劣化レベル選択のラジオボタンがクリックされたときの処理
levelSelectors.forEach(label => {
    label.addEventListener('click', () => {
        // すべての選択スタイルを一旦解除
        levelSelectors.forEach(l => l.classList.remove('selected'));
        // クリックされたラベルに選択スタイルを適用
        label.classList.add('selected');
    });
});

// ページ読み込み時に、デフォルトでチェックされているラジオボタンにスタイルを適用
document.querySelector('.radio-label input:checked').parentElement.classList.add('selected');

// ドラッグ＆ドロップエリアがクリックされたら、ファイル選択ダイアログを開く
uploadArea.addEventListener('click', () => fileInput.click());

// ファイルがドラッグオーバーされたときに、エリアのスタイルを変更
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

// ファイルがドラッグエリアから離れたときに、スタイルを元に戻す
uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

// ファイルがドロップされたときの処理
uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files; // input要素にファイルを設定
        handleFileSelect(files[0]); // ファイル選択時の共通処理を呼び出す
    }
});

// ファイル選択ダイアログでファイルが選ばれたときの処理
fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
        handleFileSelect(fileInput.files[0]);
    }
});

// フォームが送信されるときに、送信ボタンを隠してローディングスピナーを表示
form.addEventListener('submit', () => {
    submitButton.style.display = 'none';
    loader.style.display = 'block';
});

// --- Helper Function ---

/**
 * ファイルが選択されたときの共通処理
 * @param {File} file 選択されたファイルオブジェクト
 */
function handleFileSelect(file) {
    // ファイルが画像形式であるかを確認
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            // 画像のプレビューを表示
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
            // 送信ボタンを有効化
            submitButton.disabled = false;
        };
        // ファイルをData URLとして読み込む
        reader.readAsDataURL(file);
    }
}