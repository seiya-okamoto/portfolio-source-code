<?php
    //Modelはデータベースとのやり取り、処理などを行う
    //PHPのみで記述されたファイルには閉じタグを記述しないこと！！（終了タグの後に余分な空白や改行があると、予期せぬ挙動を引き起こす場合があるから）

    /**
     * 購入ボタンを押した後の購入数と在庫数を比較し、
     * 「在庫数0」の商品があるならば、カート内の当該商品を削除
     * 「購入数 > 在庫数」であるならば、在庫が足りないため、購入数を在庫数のMAX値に変更
     * 削除と変更処理を行った旨を記載した文字列を配列に格納して返す
     * 
     * @param object
     * @param array
     * @return array
     */
    function stock_purchase_qty_comparison_and_delete_product_and_change_purchase_qty($pdo, $cartList) {
        try{
            //PDOのエラー時にPDOExceptionが発生するように設定
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $pdo->beginTransaction(); //トランザクション開始

            $deleteProducts = array(); //削除した商品名を格納する配列を宣言
            $changePurchaseQtyProducts = array(); //購入数を変更した商品名を格納する配列を宣言

            //
            //「購入数 > 在庫数」であるならば、在庫が足りないため、購入数を在庫数のMAX値に変更する処理
            foreach ($cartList as $value) {
                if ($value['stock_qty'] === "0") {
                    //カート内の在庫数0の商品を削除

                    //DELETE文
                    $sql = "DELETE FROM ec_cart WHERE product_id = :deleteTargetProductId AND user_name = :userName;";

                    //prepareメソッドによるクエリの実行準備をする
                    $stmt = $pdo -> prepare($sql);

                    //値をバインドする
                    $stmt -> bindValue(':deleteTargetProductId', $value['product_id']);
                    $stmt -> bindValue(':userName', $_SESSION['user_name']);

                    //クエリの実行
                    $stmt->execute();

                    //削除した商品名を配列に格納                        
                    array_push($deleteProducts, $value['product_name']);

                } elseif ($value['purchase_qty'] > $value['stock_qty']) {
                    //購入数の変更処理

                    //UPDATE文
                    $sql = "UPDATE ec_cart SET purchase_qty = :maxStockQty
                    WHERE product_id = :changeTargetProductId AND user_name = :userName;";

                    //prepareメソッドによるクエリの実行準備をする
                    $stmt = $pdo -> prepare($sql);

                    //値をバインドする
                    $stmt -> bindValue(':maxStockQty', $value['stock_qty']);
                    $stmt -> bindValue(':changeTargetProductId', $value['product_id']);
                    $stmt -> bindValue(':userName', $_SESSION['user_name']);

                    //クエリの実行
                    $stmt->execute();

                    //購入数を変更した商品名を配列に格納                        
                    array_push($changePurchaseQtyProducts, $value['product_name']);
                }  
            }

            $pdo->commit(); //正常に終了したらコミット
        } catch (PDOException $e){
            echo $e->getMessage();
            $pdo->rollBack(); //エラーが起きたらロールバック
            exit();
        }

        if (!empty($deleteProducts)) {
            $deleteProductsByStockOtyNoneMsg = array();
            for ($i = 0; $i <= count($deleteProducts)-1;  $i++) {
                array_push($deleteProductsByStockOtyNoneMsg, $deleteProducts[$i]."の在庫がなくなったため、カートから削除しました。");
            }
        }

        if (!empty($changePurchaseQtyProducts)) {
            $changePurchaseQtyByReduceStockOtyMsg = array();
            for ($j = 0; $j <= count($changePurchaseQtyProducts)-1;  $j++) {
                array_push($changePurchaseQtyByReduceStockOtyMsg, $changePurchaseQtyProducts[$j]."の在庫数が変更されたため、数量を変更しました。");
            }
        }

        return array($deleteProductsByStockOtyNoneMsg, $changePurchaseQtyByReduceStockOtyMsg);

    }

    /**
     * ec_productsテーブルから、数量変更しようとしている商品の在庫数を二次元配列で取得
     * 
     * @param object
     * @return array
     */
    function get_stock_qty($pdo) {
        $changeTargetProductId = h($_POST['change_target_product_id']);
        $sql = "SELECT stock_qty FROM ec_products WHERE product_id = '$changeTargetProductId';";
        return get_sql_result($pdo, $sql);
    }

    /**
     * カート内商品の数量変更におけるサーバー側でのバリデーション判定(入力NGの場合、$changePurchaseQtyErrorMsgにエラー内容の文字列を格納)
     * 
     * @return string
     */
    function change_purchase_qty_server_validation($pdo) {
        //バリデーション
        if (isset($_POST['change_purchase_qty'])) {
            $arrayStockQty = get_stock_qty($pdo);
            $stockQty = $arrayStockQty[0]['stock_qty'];

            if (!preg_match('/^([1-9][0-9]*)$/', h($_POST['change_purchase_qty']))) { 
                $changePurchaseQtyErrorMsg = "数量は、「1以上の整数(半角数字)」で入力して下さい。";
            } elseif (h($_POST['change_purchase_qty']) > $stockQty) { //「購入数 > 在庫数」ならば、在庫が足りないため、エラー
                $changePurchaseQtyErrorMsg = "数量は、在庫数以下の数字で入力して下さい。";
            }
        }

        return $changePurchaseQtyErrorMsg;
    }

    /**
     * DBの数量を変更し、変更成功したら、"クエリ成功"を返す
     * SQLインジェクション対策済（プリペアドステートメント実装）
     * 
     * @param object 
     * @return string 
     */
    function change_purchase_qty($pdo) {
        //UPDATE文
        $sql = "UPDATE ec_cart SET purchase_qty = :changePurchaseQty
        WHERE product_id = :changeTargetProductId AND user_name = :userName;";

        //バインドする値を多次元配列に格納
        $bindArray = [
            [":changePurchaseQty", $_POST['change_purchase_qty']],
            [":changeTargetProductId", $_POST['change_target_product_id']],
            [":userName", $_SESSION['user_name']]
        ];

        //クエリを実行し、成功したら、$querySuccessFailure = "クエリ成功"を返す
        $querySuccessFailure = query($pdo, $sql ,$bindArray);

        return $querySuccessFailure;
    }

    /**
     * カートテーブルから商品情報の削除が成功したら、"クエリ成功"を返す
     * 
     * @param object 
     * @return string 
     */
    function delete_product($pdo) {
        //DELETE文
        $sql = "DELETE FROM ec_cart WHERE product_id = :deleteTargetProductId AND user_name = :userName;";

        //バインドする値を多次元配列に格納
        $bindArray = [
            [":deleteTargetProductId", $_POST['delete_target_product_id']],
            [":userName", $_SESSION['user_name']]
        ];

        //クエリを実行し、成功したら、$querySuccessFailure = "クエリ成功"を返す
        $querySuccessFailure = query($pdo, $sql ,$bindArray);

        return $querySuccessFailure;
    }