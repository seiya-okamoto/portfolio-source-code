<?php
    //Modelはデータベースとのやり取り、処理などを行う
    //PHPのみで記述されたファイルには閉じタグを記述しないこと！！（終了タグの後に余分な空白や改行があると、予期せぬ挙動を引き起こす場合があるから）
    
    /**
     * DBに格納されているユーザー名とパスワード（管理者以外）を二次元配列で取得
     * 
     * @param object
     * @return array
     */
    function get_user_list($pdo) {
        $sql = 'SELECT user_name, password FROM ec_users WHERE user_admin = "user";';
        return get_sql_result($pdo, $sql);
    }

    /**
     * 入力されたユーザー名とパスワードがDBに格納されているものと一致するものがあれば、$loginUserJudgeに値を格納
     * 
     * @param object
     * @return string
     */
    function login_user_judge($pdo) {
        $userData = get_user_list($pdo); //DBに格納されているユーザー名とパスワード（管理者以外）を二次元配列で取得

        for ($i = 0; $i <= count($userData)-1; $i++) {
            if ($userData[$i]['user_name'] === h($_POST['user_name']) && $userData[$i]['password'] === h($_POST['password'])) {
                $loginUserJudge = "user-match";
            }  
        }

        return $loginUserJudge;
    }

    /**
     * DBに格納されているユーザー名とパスワード（管理者用）を二次元配列で取得
     * 
     * @param object
     * @return array
     */
    function get_admin_list($pdo) {
        $sql = 'SELECT user_name, password FROM ec_users WHERE user_admin = "admin";';
        return get_sql_result($pdo, $sql);
    }

    /**
     * 入力された管理者用のユーザー名とパスワードがDBに格納されているものと一致するものがあれば、$loginAdminJudgeに値を格納
     * 
     * @param array
     * @return string
     */
    function login_admin_judge($pdo) {
        $adminData = get_admin_list($pdo); //DBに格納されているユーザー名とパスワード（管理者）を二次元配列で取得

        for ($i = 0; $i <= count($adminData)-1; $i++) {
            if ($adminData[$i]['user_name'] === h($_POST['user_name']) && $adminData[$i]['password'] === h($_POST['password'])) {
                $loginAdminJudge = "admin-match";
            }  
        }

        return $loginAdminJudge;
    }

    /**
     * Cookieの保存処理
     * 
     */
    function cookie_save() {
        //cookie保持の有効期限を変数に格納
        $cookieExpiration = time() + 60 * 60 * 24 * EXPIRATION_PERIOD; //現在時刻から30日後までがcookie保持の有効期限

        //POSTされたフォームの値を変数（cookieに値を保存用）に格納
        $cookieConfirmation = $_POST['cookie_confirmation'];
        $userName = h($_POST['user_name']);
        $pass = h($_POST['password']);

        //Cookieを保存
        setcookie('cookie_confirmation', $cookieConfirmation, $cookieExpiration);
        setcookie('userName', $userName, $cookieExpiration);
        setcookie('pass', $pass, $cookieExpiration);
    }

    /**
     * Cookieの削除処理
     * 
     */
    function cookie_delete() {
        setcookie('cookie_confirmation', '', time() - 10); //Cookieは、保存期限を現在の時間よりも過去に設定をすることによって削除される
        setcookie('userName', '', time() - 10);
        setcookie('pass', '', time() - 10);
    }
