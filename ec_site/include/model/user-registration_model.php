<?php
    //Modelはデータベースとのやり取り、処理などを行う
    //PHPのみで記述されたファイルには閉じタグを記述しないこと！！（終了タグの後に余分な空白や改行があると、予期せぬ挙動を引き起こす場合があるから）

    /**
     * DBに格納されているユーザー名を二次元配列で取得
     * 
     * @param object
     * @return array
     */
    function get_user_list($pdo) {
        $sql = 'SELECT user_name FROM ec_users;';
        return get_sql_result($pdo, $sql);
    }

    /**
     * 入力されたユーザー名がDBに格納されているものと一致するものがあれば、$userNameDuplicationに値を格納
     * 
     * @param array
     * @return string
     */
    function user_name_duplication($userData) {
        for ($i = 0; $i <= count($userData)-1; $i++) {
            if ($userData[$i]['user_name'] === h($_POST['user_name'])) {
                $userNameDuplication = "ユーザー名重複";
            }  
        }

        return $userNameDuplication;
    }
    
    /**
     * サーバー側でのバリデーション判定(入力NGの場合、$errorMsgにエラー内容の文字列を格納)
     * 
     * @param object
     * @return string
     */
    function server_validation($pdo) {
        //既に同じユーザー名が登録されているかチェック       
        $userData = get_user_list($pdo); //DBに格納されているユーザー名を二次元配列で取得
        $userNameDuplication = user_name_duplication($userData); //入力されたユーザー名がDBに格納されているものと一致するものがあれば、$userNameDuplicationに値を格納

        //バリデーション
        if (isset($_POST['user_name']) && isset($_POST['password'])) {
            if (h($_POST['user_name']) === "" && h($_POST['password']) === "") {
                $errorMsg = "ユーザー名、パスワードを入力して下さい。";
            } elseif (h($_POST['user_name']) === "") {
                $errorMsg = "ユーザー名を入力して下さい。";
            } elseif (h($_POST['password']) === "") {
                $errorMsg = "パスワードを入力して下さい。";
            } elseif (!preg_match('/^[a-zA-Z0-9]{5,20}$/', h($_POST['user_name'])) && !preg_match('/^[a-zA-Z0-9]{8,20}$/', h($_POST['password']))) {
                $errorMsg = "ユーザー名、パスワードを正しい形式で入力して下さい。";
            } elseif (!preg_match('/^[a-zA-Z0-9]{5,20}$/', h($_POST['user_name']))) {
                $errorMsg = "ユーザー名を正しい形式で入力して下さい。";
            } elseif (!preg_match('/^[a-zA-Z0-9]{8,20}$/', h($_POST['password']))) {
                $errorMsg = "パスワードを正しい形式で入力して下さい。";
            } elseif ($userNameDuplication === "ユーザー名重複") {
                $errorMsg = "このユーザー名は登録済です。別のユーザー名で登録して下さい。";
            }
        }

        return $errorMsg;
    }

    /**
     * ユーザー情報を登録し、成功したら、"クエリ成功"を返す
     * SQLインジェクション対策済（プリペアドステートメント実装）
     * 
     * @param object 
     * @return string 
     */
    function user_registration($pdo) {
        //INSERT文
        $sql = "INSERT INTO ec_users(user_name, password, user_admin) VALUES(:userName, :pass, 'user');";

        //バインドする値を多次元配列に格納
        $bindArray = [
            [":userName", $_POST['user_name']],
            [":pass", $_POST['password']]
        ];

        //クエリを実行し、成功したら、$querySuccessFailure = "クエリ成功"を返す
        $querySuccessFailure = query($pdo, $sql ,$bindArray);

        return $querySuccessFailure;
    }