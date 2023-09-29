<?php
    //Controllerファイル
    //PHPのみで記述されたファイルには閉じタグを記述しないこと！！（終了タグの後に余分な空白や改行があると、予期せぬ挙動を引き起こす場合があるから）
    
    session_start();

    //ユーザーとしてログイン中である場合は、catalog.phpにリダイレクト（転送）する
    if (isset($_SESSION['user_name'])) {       
        header('Location: catalog.php');
        exit();
    }

    //管理者としてログイン中である場合は、product-control.phpにリダイレクト（転送）する
    if (isset($_SESSION['admin_name'])) {       
        header('Location: product-control.php');
        exit();
    }

    //Constファイルを読み込む
    include_once '../../include/config/const.php';

    //テンプレ化されたmodelファイルを読み込む
    include_once '../../include/template/template_model.php';
    //Modelファイルを読み込む（Modelファイルに記述された関数を使用可能）
    include_once '../../include/model/user-registration_model.php';


    $pdo = get_connection(); //DB接続を行いPDOインスタンスを返す

    //サーバー側でのバリデーション判定(入力NGの場合、$errorMsgにエラー内容の文字列を格納)
    $errorMsg = server_validation($pdo);

    //「postがnullではない」 かつ「入力エラーなし（$errorMsgがnull）」の場合、DBへユーザー情報を登録
    if ((isset($_POST['user_name']) && isset($_POST['password'])) && !isset($errorMsg)) {
        $registrationSuccessFailure = user_registration($pdo); //DBにユーザー情報を登録し、登録成功したら、$registrationSuccessFailure = "クエリ成功"を返す
    }


    //Viewファイルを読み込む（Controllerファイルに記述された変数をViewファイルで使用可能）
    include_once '../../include/view/user-registration_view.php';
