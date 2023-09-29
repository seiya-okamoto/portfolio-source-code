<?php
    //Controllerファイル
    //PHPのみで記述されたファイルには閉じタグを記述しないこと！！（終了タグの後に余分な空白や改行があると、予期せぬ挙動を引き起こす場合があるから）
    
    session_start();

    //管理者としてログイン中ではないとき
    if (!isset($_SESSION['admin_name'])) { //$_SESSION['admin_name']がnullのとき、つまり、管理者としてログイン中ではないとき
        //index.phpにリダイレクト（転送）する
        header('Location: index.php');
        exit();
    }

    //Constファイルを読み込む
    include_once '../../include/config/const.php';

    //テンプレ化されたmodelファイルを読み込む
    include_once '../../include/template/template_model.php';
    //Modelファイルを読み込む（Modelファイルに記述された関数を使用可能）
    include_once '../../include/model/product-control_model.php';


    $pdo = get_connection(); //DB接続


    //商品登録処理

    //商品登録のサーバー側でのバリデーション判定(入力NGの場合、$registrationErrorMsgにエラー内容の文字列を格納)
    if (isset($_POST['product_name'])) {
        $registrationErrorMsg = registration_server_validation();
    }

    //入力エラーがある場合、商品登録フォームに入力内容を残すために、postの値を変数に格納
    if (isset($registrationErrorMsg)) {
        $enteredProductName = $_POST['product_name'];
        $enteredPrice = $_POST['price'];
        $enteredStockQty = $_POST['stock_qty'];
    }

    //「登録ボタンが押された（postがnullではない）」 かつ「入力エラーなし（$registrationErrorMsgがnull）」の場合、DBへ商品登録処理
    if (isset($_POST['product_name']) && !isset($registrationErrorMsg)) {
        $registrationSuccessFailure = product_registration($pdo); //DBに商品情報を登録、商品画像はディレクトリに格納し、登録成功したら、$registrationSuccessFailure = "登録成功"を返す  
    }


    //商品一覧での商品情報変更処理

    //商品一覧での在庫数変更におけるサーバー側でのバリデーション判定(入力NGの場合、$changeStockQtyErrorMsgにエラー内容の文字列を格納)
    $changeStockQtyErrorMsg = change_stock_qty_server_validation();

    //入力エラーがなければ、在庫数の変更
    if (isset($_POST['change_stock_qty']) && !isset($changeStockQtyErrorMsg)) {
        $changeStockQtySuccessFailure = change_stock_qty($pdo); //DBの在庫数を変更し、変更成功したら"クエリ成功"を返す
    }

    //公開フラグの変更
    if (isset($_POST['make_private']) || isset($_POST['make_public'])) {
        $changePublicPrivateSuccessFailure = change_public_private($pdo); //DBのpublic_privateを変更し、変更成功したら、"クエリ成功"を返す
    }

    //商品情報の削除
    if (isset($_POST['delete_product'])) {
        $deleteProductSuccessFailure = delete_product($pdo); //DBから商品情報の削除が成功したら、"クエリ成功"を返す
    }
    

    //商品一覧の表示処理

    $productData = [];
    $productData = get_product_list($pdo); //全商品の情報を二次元配列で取得
    $productData = h_array($productData); //二次元配列に格納された全商品の情報をエスケープ処理
    



    //Viewファイルを読み込む（Controllerファイルに記述された変数をViewファイルで使用可能）
    include_once '../../include/view/product-control_view.php';