<?php
    //Modelはデータベースとのやり取り、処理などを行う
    //PHPのみで記述されたファイルには閉じタグを記述しないこと！！（終了タグの後に余分な空白や改行があると、予期せぬ挙動を引き起こす場合があるから）

    /**
     * ec_productsテーブルからステータスが「public」の商品情報を二次元配列で取得
     * 
     * @param object
     * @return array
     */
    function get_public_product_list($pdo) {
        $sql = 'SELECT product_id, product_name, price, stock_qty, product_img, public_private FROM ec_products
        WHERE public_private = "public" ORDER BY product_id DESC;'; // idの値の降順で並べ替え（新しく登録したものを上、古いものを下に表示するため）
        return get_sql_result($pdo, $sql);
    }

    /**
     * ec_cartテーブルに格納されているuser_nameとproduct_idを二次元配列で取得
     * 
     * @param object
     * @return array
     */
    function get_products_in_cart($pdo) {
        $sql = 'SELECT user_name, product_id FROM ec_cart;';
        return get_sql_result($pdo, $sql);
    }

    /**
     * user_nameとproduct_idがec_cartテーブルに格納されているものと一致するものがあれば、$loginUserJudgeに値を格納
     * 
     * @param object
     * @return string
     */
    function duplication_in_cart_judge($pdo) {
        $cartProducts = get_products_in_cart($pdo); //ec_cartテーブルに格納されているuser_nameとproduct_idを二次元配列で取得

        for ($i = 0; $i <= count($cartProducts)-1; $i++) {
            if ($cartProducts[$i]['user_name'] === h($_SESSION['user_name']) && $cartProducts[$i]['product_id'] === h($_POST['add_to_cart_product_id'])) {
                $duplicationInCartJudge = "カートに当該商品あり";
            }  
        }

        return $duplicationInCartJudge;
    }

    /**
     *カートに追加する商品の商品名を二次元配列で取得
     * 
     * @param object
     * @return array
     */
    function product_name_add_cart($pdo) {
        $addProductId = h($_POST['add_to_cart_product_id']);
        $sql = "SELECT product_name FROM ec_products WHERE product_id = '$addProductId';";
        return get_sql_result($pdo, $sql);
    }

    /**
     * カートテーブルに購入商品情報を登録(当該商品が既にカートテーブルにある場合、数量のみを1増やす)し、成功したら、"クエリ成功"とカートに追加した商品名を返す
     * SQLインジェクション対策済（プリペアドステートメント実装）
     * 
     * @param object 
     * @return array 
     */
    function add_to_cart($pdo) {
        $duplicationInCartJudge = duplication_in_cart_judge($pdo);

        //sql文
        if ($duplicationInCartJudge === "カートに当該商品あり") {
            //数量のみを1増やす
            $sql = "UPDATE ec_cart SET purchase_qty = purchase_qty + 1 WHERE user_name = :userName AND product_id = :productId;";
        } else {
            //購入商品情報を登録
            $sql = "INSERT INTO ec_cart(user_name, product_id, purchase_qty) VALUES(:userName, :productId, 1);";
        }
        
        //バインドする値を多次元配列に格納
        $bindArray = [
            [":userName", $_SESSION['user_name']],
            [":productId", $_POST['add_to_cart_product_id']]
        ];

        //クエリを実行し、成功したら、$querySuccessFailure = "クエリ成功"を返す
        $querySuccessFailure = query($pdo, $sql ,$bindArray);

        //カートに追加した商品名を格納
        $productNameAddCart = product_name_add_cart($pdo);
        $displayProductNameAddCart = $productNameAddCart[0]["product_name"];

        return array($querySuccessFailure, $displayProductNameAddCart);
    }

