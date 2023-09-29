<?php
    //modelファイル内の関数で共通して使用できるものはここにテンプレ化する

    /**
     * XSS対策のエスケープ処理（文字列へ変換）
     * 
     * @param string
     * @return string
     */
    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * DB接続を行いPDOインスタンスを返す
     *  
     * @return object $pdo
     */
    function get_connection() {
        try {
            // PDOインスタンスの生成
            $pdo=new PDO(DSN, LOGIN_USER, PASSWORD);
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        return $pdo;
    }

    /**
     * SQL文を実行し、結果を配列で取得し、返す
     * 
     * @param object $pdo
     * @param string $sql 実行されるSQL文章
     * @return array 結果セットを格納した配列
     */
    function get_sql_result($pdo, $sql) {
        $data = []; //配列を空にする
        if ($result = $pdo->query($sql)) {           
                while ($row = $result->fetch()) { // 結果セットから1行ずつ取得して連想配列に格納、これをレコードがなくなるまで繰り返す
                    $data[] = $row;
                }            
        }

        return $data;
    }

    /**
     * クエリを実行し、成功したら、$querySuccessFailure = "クエリ成功"を返す
     * SQLインジェクション対策済（プリペアドステートメント実装）
     * 
     * @param object $pdo
     * @param string $sql
     * @param array $bindArray
     * @return string 
     */
    function query($pdo, $sql ,$bindArray) {
        try{
            //PDOのエラー時にPDOExceptionが発生するように設定
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $pdo->beginTransaction(); //トランザクション開始

            //prepareメソッドによるクエリの実行準備をする
            $stmt = $pdo -> prepare($sql);

            //値をバインド（結びつける）
            //バインドされる値にSQLインジェクションに使われるような特殊文字が含まれていた場合でも、ただの文字列として扱われるようになる
            if (isset($bindArray)) {
                foreach ($bindArray as $keys => $values) {
                    $stmt -> bindValue($values[0], $values[1]);   
                }
            } 

            //クエリの実行
            $stmt->execute();
            $pdo->commit(); //正常に終了したらコミット
            $querySuccessFailure = "クエリ成功";
        } catch (PDOException $e){
            echo $e->getMessage();
            $pdo->rollBack(); //エラーが起きたらロールバック
            exit();
        }

        return $querySuccessFailure;
    }

    /**
     * 二次元配列に格納された要素の値の特殊文字をただの文字列に変換、引数$arrayは二次元配列
     * 
     * @param array
     * @return array
     */
    function h_array($array) {
        foreach ($array as $keys => $values) {
            foreach ($values as $key => $value) {
                //h関数で特殊文字をただの文字列に変換して二次元配列に格納し直す
                $array[$keys][$key] = h($value);
            }
        }

        //変換後の二次元配列を返す
        return $array;
    }

    /**
     * ec_cartテーブルとec_productsテーブルを結合して、当該ユーザーのカート内商品情報を二次元配列で取得
     * 
     * @param object
     * @return array
     */
    function get_cart_list($pdo) {
        $userName = h($_SESSION['user_name']);
        $sql = "SELECT *
        FROM ec_cart INNER JOIN ec_products ON ec_cart.product_id = ec_products.product_id
        WHERE user_name = '$userName';";

        return get_sql_result($pdo, $sql);
    }

    /**
     * カート内商品の合計金額を算出
     * 
     * @param array
     * @return int
     */
    function total_amount($cartList) {
        for ($i = 0; $i <= count($cartList) - 1; $i++){
            $subtotal = $cartList[$i]['price'] * $cartList[$i]['purchase_qty'];
            $totalAmount = $totalAmount + $subtotal;
        } 

        return $totalAmount;
    }