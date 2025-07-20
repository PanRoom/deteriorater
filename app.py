import io
from flask import Flask, render_template, request, send_file
from PIL import Image
from wsgiref.handlers import CGIHandler

app = Flask(__name__)

@app.route('/')
def index():
    """トップページを表示する"""
    return render_template('index.html')

@app.route('/process', methods=['POST'])
def process_image():
    """画像処理を実行する"""
    # フォームからファイルと変換回数を取得
    if 'image' not in request.files or 'iterations' not in request.form:
        return "必要なデータがありません", 400

    file = request.files['image']
    iterations = int(request.form['iterations'])
    
    if not file:
        return "ファイルが選択されていません", 400

    try:
        # Pillowで画像を開く
        img = Image.open(file.stream)
        
        # オリジナル画像のフォーマットを保持（PNGなどがアップロードされた場合のため）
        # ただし、最初の変換でJPGになる
        output_format = 'JPEG'

        # 指定された回数だけJPG変換を繰り返す
        for i in range(iterations):
            # メモリ上にJPGとして保存するためのバッファ
            buffer = io.BytesIO()
            # RGBに変換しないとJPEG保存でエラーになる場合がある
            img = img.convert('RGB')
            # 画質を指定して保存（この数値を下げると劣化が早まる）
            img.save(buffer, format=output_format, quality=80) 
            buffer.seek(0)
            # 保存したデータを再度Pillowで開く
            img = Image.open(buffer)

        # 最終的に劣化した画像をクライアントに送るためのバッファ
        final_buffer = io.BytesIO()
        img.save(final_buffer, format=output_format)
        final_buffer.seek(0)

        # ファイルとしてダウンロードさせる
        return send_file(
            final_buffer,
            mimetype=f'image/{output_format.lower()}',
            as_attachment=True,
            download_name='degraded_image.jpg'
        )

    except Exception as e:
        return f"エラーが発生しました: {e}", 500

if __name__ == '__main__':
    CGIHandler().run(app)