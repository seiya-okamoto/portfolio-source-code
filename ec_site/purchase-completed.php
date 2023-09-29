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
    include_once '../../include/model/purchase-completed_model.php';


    $pdo = get_connection(); //DB接続


    //カート内商品の表示処理
    $cartList = [];
    $cartList = get_cart_list($pdo); //当該ユーザーのカート内商品情報を二次元配列で取得
    $cartList = h_array($cartList); //二次元配列に格納されたカート内商品情報をエスケープ処理
    
    //購入完了ページが表示された後に、ユーザーがページを更新したとき（カート内に商品がなくなった場合）
    if (empty($cartList)) {
        //catalog.phpにリダイレクト（転送）する
        header('Location: catalog.php');
        exit();
    }

    //カート内商品の合計金額を算出
    $totalAmount = total_amount($cartList); //カート内商品の合計金額を算出
    $totalAmount = number_format($totalAmount); //合計金額を3桁区切り表示にする


    //ec_productsテーブルから購入完了した商品の在庫数を購入数分減らし、ec_cartテーブルの当該ユーザーの商品情報をすべて削除
    $reduceStockQtyAndDeleteCartDataSuccessFailure = reduce_stock_qty_and_delete_cart_data($pdo, $cartList); //変更成功したら"クエリ成功"を返す



    //Viewファイルを読み込む（Controllerファイルに記述された変数をViewファイルで使用可能）
    include_once '../../include/view/purchase-completed_view.php';