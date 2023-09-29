<?php
    //Controllerファイル
    //PHPのみで記述されたファイルには閉じタグを記述しないこと！！（終了タグの後に余分な空白や改行があると、予期せぬ挙動を引き起こす場合があるから）

    //このページに初回アクセス時は、サーバーにてセッションIDが作成され、ユーザーのクッキーにも送信される
    //それ以降は、ユーザーのクッキー(セッションID)とサーバーのセッションIDが一致したら、
    //セッションを再開して、サーバーに保存されているセッションファイルの情報を取得できる
    session_start();

    //ログアウト処理がされた場合、
    if (isset($_POST["logout"])) {
        //セッション名を取得する("PHPSESSID"を取得)
        $session = session_name();

        //セッションの値を削除（配列を空にする）
        $_SESSION = [];
        
        if (isset($_COOKIE[$session])) { //cookieに保存されている情報（セッションID、つまりPHPSSIDのこと）があれば
            // cookieを削除
            //パス '/' をセットすると、クッキーは domain 配下の全てで有効となる。つまり、https://portfolio02.dc-itex.com配下のすべてにsetcookieが適用される。
            //例えば、'/foo/' をセットすると、/foo/ ディレクトリとそのサブディレクトリ配下 (例えば /foo/bar/) にsetcookieが適用される。
            setcookie($session, '', time() - 30, '/'); // https://portfolio02.dc-itex.com配下のクッキーを削除
        }
    }

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
    include_once '../../include/model/index_model.php';
    

    //読み込んだmodelの関数を用いてDBデータ等を変数に格納して、viewにて使えるようにする
    $pdo = get_connection(); //DB接続を行いPDOインスタンスを返す

    //ユーザー名、パスワードが一致判定
    $loginUserJudge = login_user_judge($pdo); //入力されたユーザー名とパスワードがDBに格納されているものと一致するものがあれば、$loginUserJudgeに値を格納
    $loginAdminJudge = login_admin_judge($pdo); //入力された管理者用のユーザー名とパスワードがDBに格納されているものと一致するものがあれば、$loginAdminJudgeに値を格納


    //セッションとcookieの処理（ユーザー）
    // 入力されたユーザー名とパスワードが合致する場合
    if ($loginUserJudge === "user-match") {
        //セッションに値を格納
        $_SESSION['user_name'] = $_POST['user_name']; //ユーザーがログインしているかどうかは$_SESSION['user_name']に値が入っているかどうか

        //Cookieの保存or削除処理
        if (isset($_POST['cookie_confirmation'])) { //入力省略にチェックがされている場合
            cookie_save(); //Cookieの保存処理
        } else { //入力省略にチェックされていない場合           
            cookie_delete(); //Cookieの削除処理
        }
    }

    //セッションとcookieの処理（管理者）
    // 入力された管理用のユーザー名とパスワードが合致する場合
    if ($loginAdminJudge === "admin-match") {
        //セッションに値を格納
        $_SESSION['admin_name'] = $_POST['user_name']; //管理者がログインしているかどうかは$_SESSION['admin_name']に値が入っているかどうか

        //Cookieの保存or削除処理
        if (isset($_POST['cookie_confirmation'])) { //入力省略にチェックがされている場合
            cookie_save(); //Cookieの保存処理
        } else { //入力省略にチェックされていない場合           
            cookie_delete(); //Cookieの削除処理
        }
    }
    
    //ユーザー名、パスワードが一致したとき、商品一覧ページに画面遷移
    if ($loginUserJudge === "user-match") {
        header('Location: catalog.php');
        exit();
    }
    
    //管理用のユーザー名、パスワードが一致したとき、商品管理ページに画面遷移
    if ($loginAdminJudge === "admin-match") {
        header('Location: product-control.php');
        exit();
    }


    //Viewファイルを読み込む（Controllerファイルに記述された変数をViewファイルで使用可能）
    include_once '../../include/view/index_view.php';