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
    include_once '../../include/model/cart_model.php';


    $pdo = get_connection(); //DB接続


    //購入ボタンを押した後のチェック
    //商品がカートに入っている間に、他のユーザーが同じ商品を購入し、在庫数が減った際に、
    //「在庫数0」となったならば、カート内の当該商品を削除し、Msg表示
    //「購入数 > 在庫数」となったならば、購入数に対して在庫数が足りないため、購入数を在庫数のMAX値に変更し、Msg表示
    //いずれの場合も購入完了ページには遷移させない
    if (isset($_POST['purchase_btn'])) {
        $cartList = [];
        $cartList = get_cart_list($pdo); //当該ユーザーのカート内商品情報を二次元配列で取得
        $cartList = h_array($cartList); //二次元配列に格納されたカート内商品情報をエスケープ処理

        //商品削除、購入数変更処理
        $deleteProductsChangePurchaseQtyMsg = stock_purchase_qty_comparison_and_delete_product_and_change_purchase_qty($pdo, $cartList);
        //二次元配列に格納されたMsgを分ける
        $deleteProductsMsg = $deleteProductsChangePurchaseQtyMsg[0];
        $changePurchaseQtyMsg = $deleteProductsChangePurchaseQtyMsg[1];

        //他ユーザーの購入による在庫数減少に伴う、カート内商品削除や購入数変更がないならば、購入完了ページに遷移させる
        if ($deleteProductsMsg === null && $changePurchaseQtyMsg === null) {
            header('Location: purchase-completed.php');
            exit();
        }
    }


    //カート内の商品情報変更処理

    //数量変更におけるサーバー側でのバリデーション判定(入力NGの場合、$changePurchaseQtyErrorMsgにエラー内容の文字列を格納)
    $changePurchaseQtyErrorMsg = change_purchase_qty_server_validation($pdo);

    //入力エラーがなければ、数量の変更
    if (isset($_POST['change_purchase_qty']) && !isset($changePurchaseQtyErrorMsg)) {
        $changePurchaseQtySuccessFailure = change_purchase_qty($pdo); //DBの数量を変更し、変更成功したら"クエリ成功"を返す
    }

    //カートから商品を削除
    if (isset($_POST['delete_product'])) {
        $deleteProductSuccessFailure = delete_product($pdo); //カートテーブルから商品情報の削除が成功したら、"クエリ成功"を返す
    }


    //カート内商品の表示処理
    $cartList = [];
    $cartList = get_cart_list($pdo); //当該ユーザーのカート内商品情報を二次元配列で取得
    $cartList = h_array($cartList); //二次元配列に格納されたカート内商品情報をエスケープ処理

    //カート内商品の合計金額を算出
    $totalAmount = total_amount($cartList); //カート内商品の合計金額を算出
    $totalAmount = number_format($totalAmount); //合計金額を3桁区切り表示にする


    //Viewファイルを読み込む（Controllerファイルに記述された変数をViewファイルで使用可能）
    include_once '../../include/view/cart_view.php';