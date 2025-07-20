// script.js

// スライダー関連の変数を削除
// const slider = document.getElementById('iterations-slider');
// const sliderValue = document.getElementById('iterations-value');

// 新しくラジオボタンの要素を取得
const levelSelectors = document.querySelectorAll('.radio-label');
const form = document.getElementById('upload-form');
const submitButton = document.getElementById('submit-button');
const loader = document.getElementById('loader');
const uploadArea = document.getElementById('upload-area');
const fileInput = document.getElementById('file-input');
const imagePreview = document.getElementById('image-preview');

// --- Event Listeners ---

// ラジオボタンのクリックイベント
levelSelectors.forEach(label => {
    label.addEventListener('click', () => {
        // すべての選択スタイルを解除
        levelSelectors.forEach(l => l.classList.remove('selected'));
        // クリックされたものに選択スタイルを適用
        label.classList.add('selected');
    });
});

// 初期状態で選択されているものにスタイルを適用
document.querySelector('.radio-label input:checked').parentElement.classList.add('selected');

// 以下、ファイル選択やフォーム送信のロジックは変更なし
uploadArea.addEventListener('click', () => fileInput.click());

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});
uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});
uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        handleFileSelect(files[0]);
    }
});

fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
        handleFileSelect(fileInput.files[0]);
    }
});

form.addEventListener('submit', () => {
    submitButton.style.display = 'none';
    loader.style.display = 'block';
});

// --- Helper Function ---
function handleFileSelect(file) {
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
            submitButton.disabled = false;
        };
        reader.readAsDataURL(file);
    }
}