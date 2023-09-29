<?php
    //Controllerファイル
    //PHPのみで記述されたファイルには閉じタグを記述しないこと！！（終了タグの後に余分な空白や改行があると、予期せぬ挙動を引き起こす場合があるから）

    session_start();
    
    //ユーザーとしてログイン中ではないとき
    if (!isset($_SESSION['user_name'])) { //$_SESSION['user_name']がnullのとき、つまり、ユーザーとしてログイン中ではないとき
        //index.phpにリダイレクト（転送）する
        header('Location: index.php');
        exit();
    }

    //Constファイルを読み込む
    include_once '../../include/config/const.php';

    //テンプレ化されたmodelファイルを読み込む
    include_once '../../include/template/template_model.php';
    //Modelファイルを読み込む（Modelファイルに記述された関数を使用可能）
    include_once '../../include/model/catalog_model.php';


    $pdo = get_connection(); //DB接続

    //商品一覧の表示処理
    $publicProductData = [];
    $publicProductData = get_public_product_list($pdo); //ec_productsテーブルからステータスが「public」の商品情報を二次元配列で取得
    $publicProductData = h_array($publicProductData); //二次元配列に格納された商品情報をエスケープ処理

    //「カートに入れる」ボタンが押されたら、カートテーブルに購入商品情報を登録(当該商品が既にカートテーブルにある場合、数量のみを1増やす)
    if (isset($_POST['add_to_cart_product_id'])) {
        $addToCartSuccessFailureAndProductName = add_to_cart($pdo); //成功したら、"クエリ成功"とカートに追加した商品名を配列で返す
    }


    //Viewファイルを読み込む（Controllerファイルに記述された変数をViewファイルで使用可能）
    include_once '../../include/view/catalog_view.php';