<?php
    //Modelはデータベースとのやり取り、処理などを行う
    //PHPのみで記述されたファイルには閉じタグを記述しないこと！！（終了タグの後に余分な空白や改行があると、予期せぬ挙動を引き起こす場合があるから）

    /**
     * ec_productsテーブルから購入完了した商品の在庫数を購入数分減らし、ec_cartテーブルの当該ユーザーの商品情報をすべて削除できたら、"クエリ成功"を返す
     * SQLインジェクション対策済（プリペアドステートメント実装）
     * 
     * @param object
     * @param array
     * @return string 
     */
    function reduce_stock_qty_and_delete_cart_data($pdo, $cartList) {
        try{
            //PDOのエラー時にPDOExceptionが発生するように設定
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $pdo->beginTransaction(); //トランザクション開始

            //在庫数変更処理
            foreach ($cartList as $value) {
                $reduceStockQtyTargetProductId = $value['product_id'];
                $purchaseQty = $value['purchase_qty'];
                $stockQty = $value['stock_qty'];
                $updatedStockQty = $stockQty - $purchaseQty;        

                //UPDATE文
                $sql = "UPDATE ec_products SET stock_qty = :updatedStockQty
                WHERE product_id = :reduceStockQtyTargetProductId;";

                //prepareメソッドによるクエリの実行準備をする
                $stmt = $pdo -> prepare($sql);

                //値をバインドする
                $stmt -> bindValue(':updatedStockQty', $updatedStockQty);
                $stmt -> bindValue(':reduceStockQtyTargetProductId', $reduceStockQtyTargetProductId);

                //クエリの実行
                $stmt->execute();               
            }

            //カートテーブルの当該ユーザーの商品情報をすべて削除する処理
            foreach ($cartList as $value) {
                $deleteTargetProductId = $value['product_id'];   

                //DELETE文
                $sql = "DELETE FROM ec_cart WHERE product_id = :deleteTargetProductId AND user_name = :userName;";

                //prepareメソッドによるクエリの実行準備をする
                $stmt = $pdo -> prepare($sql);

                //値をバインドする
                $stmt -> bindValue(':deleteTargetProductId', $deleteTargetProductId);
                $stmt -> bindValue(':userName', $_SESSION['user_name']);

                //クエリの実行
                $stmt->execute();
            }

            $pdo->commit(); //正常に終了したらコミット
            $querySuccessFailure = "クエリ成功";
        } catch (PDOException $e){
            echo $e->getMessage();
            $pdo->rollBack(); //エラーが起きたらロールバック
            exit();
        }
        
        return $querySuccessFailure;
    }