<?php
    ////Modelはデータベースとのやり取り、処理などを行う
    //PHPのみで記述されたファイルには閉じタグを記述しないこと！！（終了タグの後に余分な空白や改行があると、予期せぬ挙動を引き起こす場合があるから）

    /**
     * 商品登録のサーバー側でのバリデーション判定(入力NGの場合、$registrationErrorMsgにエラー内容の文字列を格納)
     * 
     * @return string
     */
    function registration_server_validation() {
        //商品名の文字数取得
        $productNameWordCount = mb_strlen($_POST['product_name']);

        //画像の拡張子チェック
        $approvedExt = array( 'jpg', 'jpeg', 'png' );
        $enteredExt = strtolower( pathinfo(h($_FILES['product_img']['name']), PATHINFO_EXTENSION) ); //「pathinfo( ファイルの名前, PATHINFO_EXTENSION )」でファイルの名前から拡張子のみを取得。拡張子が大文字の場合があるので、「strtolower」で小文字に変換。
        if( in_array( $enteredExt, $approvedExt ) ){ // 取り出した拡張子を「in_array」で許可された拡張子に該当があるかどうかを判定
            $checkExt = "許可された拡張子";
        }

        //バリデーション
        if (h($_POST['product_name']) === "" || h($_POST['price']) === "" || h($_POST['stock_qty']) === "" || ($_FILES['product_img']['error'] === 4)) { //画像を選択せずに登録ボタンを押すと、$_FILES['post-img']['error']にint型の4が格納される
            $registrationErrorMsg = "未入力項目があります。すべての項目を入力して下さい。";
        } elseif ($productNameWordCount > 20 ) {
            $registrationErrorMsg = "商品名は20字以内で入力して下さい。";
        } elseif (!preg_match('/^([1-9][0-9]*|0)$/', h($_POST['price']))) {
            $registrationErrorMsg = "価格を正しい形式で入力して下さい。";
        } elseif (!preg_match('/^([1-9][0-9]*|0)$/', h($_POST['stock_qty']))) {
            $registrationErrorMsg = "在庫数を正しい形式で入力して下さい。";
        } elseif (!in_array($_POST['public_private'], ['public','private'])) {
            $registrationErrorMsg = "ステータスを選択してください。";
        } elseif ($checkExt !== "許可された拡張子") {
            $registrationErrorMsg = "jpg,jpeg,png以外の商品画像が選択されています。";
        }

        return $registrationErrorMsg;
    }

    /**
     * DBのec_productsテーブルからproduct_id列の最大値を二次元配列で取得し、返す
     * 
     * @param object
     * @return array 
     */
    function get_max_product_id($pdo) {
        $sql = 'SELECT MAX(product_id) FROM ec_products;'; //product_id列の最大値を取得
        return get_sql_result($pdo, $sql);
    }

    /**
     * DBに商品情報を登録、商品画像はディレクトリに格納し、登録成功したら、$registrationSuccessFailure = "登録成功"を返す
     * SQLインジェクション対策済（プリペアドステートメント実装）
     * 
     * @param object 
     * @return string 
     */
    function product_registration($pdo) {
        $maxProductId = get_max_product_id($pdo); //登録されてあるproduct_idの最大値を取得
        $registerProductId = $maxProductId[0]["MAX(product_id)"] + 1; //新たに登録するproduct_id

        //DBに登録する入力情報を変数に格納
        $productName = $_POST['product_name'];
        $price = $_POST['price'];
        $stockQty = $_POST['stock_qty'];
        $publicPrivate = $_POST['public_private'];

        //商品画像をサーバーのimgディレクトリに格納する処理
        //画像格納ディレクトリの作成
        $imgDirectoryName = str_pad($registerProductId, 3, 0, STR_PAD_LEFT); //ディレクトリ名はproduct_idと同じ番号とし、0埋め処理（ディレクトリを番号順に並べるため）
        mkdir("img/".$imgDirectoryName, 0777); // imgディレクトリ直下に、画像格納ディレクトリを作る
        //作成したディレクトリに画像を保存する処理        
        $savePath = 'img/'.$imgDirectoryName.'/'.h(basename($_FILES['product_img']['name'])); //送信された画像ファイルの名前をファイル名として保存先のパスを作成
        move_uploaded_file($_FILES['product_img']['tmp_name'], $savePath); //画像ファイルを保存先ディレクトリに移動させる

        try{
            //PDOのエラー時にPDOExceptionが発生するように設定
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $pdo->beginTransaction(); //トランザクション開始

            //INSERT文
            $sql = "INSERT INTO ec_products(product_id, product_name, price, stock_qty, product_img, public_private)
            VALUES('$registerProductId', :productName, :price, :stockQty, '$savePath', :publicPrivate);";

            //prepareメソッドによるクエリの実行準備をする
            $stmt = $pdo -> prepare($sql);

            //値をバインド（結びつける）
            //バインドされる値にSQLインジェクションに使われるような特殊文字が含まれていた場合でも、ただの文字列として扱われるようになる
            $stmt -> bindValue(':productName', $productName);
            $stmt -> bindValue(':price', $price);
            $stmt -> bindValue(':stockQty', $stockQty);
            $stmt -> bindValue(':publicPrivate', $publicPrivate);

            //クエリの実行
            $stmt->execute();
            $pdo->commit(); //正常に終了したらコミット
            $registrationSuccessFailure = "登録成功";
        } catch (PDOException $e){
            echo $e->getMessage();
            $pdo->rollBack(); //エラーが起きたらロールバック
            //作成した画像格納ディレクトリを消去する処理
            unlink($savePath); // rmdirは中にファイルが存在すると削除出来ないので、先にディレクトリ内の画像を削除
            rmdir('img/'.$imgDirectoryName); //作成したディレクトリを消去
            exit();
        }

        return $registrationSuccessFailure;
    }

    /**
     * 商品一覧での在庫数変更におけるサーバー側でのバリデーション判定(入力NGの場合、$changeStockQtyErrorMsgにエラー内容の文字列を格納)
     * 
     * @return string
     */
    function change_stock_qty_server_validation() {
        //バリデーション
        if (isset($_POST['change_stock_qty'])) {
            if (!preg_match('/^([1-9][0-9]*|0)$/', h($_POST['change_stock_qty']))) { 
                $changeStockQtyErrorMsg = "在庫数は、「半角数字、0以上の整数」で入力して下さい。";
            }
        }

        return $changeStockQtyErrorMsg;
    }

    /**
     * DBの在庫数を変更し、変更成功したら、"クエリ成功"を返す
     * SQLインジェクション対策済（プリペアドステートメント実装）
     * 
     * @param object 
     * @return string 
     */
    function change_stock_qty($pdo) {
        //UPDATE文
        $sql = "UPDATE ec_products SET stock_qty = :changeStockQty WHERE product_id = :changeTargetProductId;";

        //バインドする値を多次元配列に格納
        $bindArray = [
            [":changeStockQty", $_POST['change_stock_qty']],
            [":changeTargetProductId", $_POST['change_target_product_id']]
        ];

        //クエリを実行し、成功したら、$querySuccessFailure = "クエリ成功"を返す
        $querySuccessFailure = query($pdo, $sql ,$bindArray);

        return $querySuccessFailure;
    }

    /**
     * DBのpublic_privateを変更し、変更成功したら、"クエリ成功"を返す
     * 
     * @param object 
     * @return string 
     */
    function change_public_private($pdo) {
        //DBのpublic_private列にて変更するものを変数に格納
        if (isset($_POST['make_private'])) {
            $changePublicPrivate = "private";
        } elseif (isset($_POST['make_public'])) {
            $changePublicPrivate = "public";
        }

        //UPDATE文
        $sql = "UPDATE ec_products SET public_private = '$changePublicPrivate' WHERE product_id = :changeTargetProductId;";

        //バインドする値を多次元配列に格納
        $bindArray = [
            [":changeTargetProductId", $_POST['public_private_target_product_id']]
        ];

        //クエリを実行し、成功したら、$querySuccessFailure = "クエリ成功"を返す
        $querySuccessFailure = query($pdo, $sql ,$bindArray);

        return $querySuccessFailure;
    }

    /**
     * DBから商品情報の削除が成功したら、"クエリ成功"を返す
     * 
     * @param object 
     * @return string 
     */
    function delete_product($pdo) {
        $deleteTargetProductImgPath = $_POST['delete_target_product_img'];

        //UPDATE文
        $sql = "DELETE FROM ec_products WHERE product_id = :deleteTargetProductId;";

        //バインドする値を多次元配列に格納
        $bindArray = [
            [":deleteTargetProductId", $_POST['delete_target_product_id']]
        ];

        //クエリを実行し、成功したら、$querySuccessFailure = "クエリ成功"を返す
        $querySuccessFailure = query($pdo, $sql ,$bindArray);

        if ($querySuccessFailure === "クエリ成功") {
            //商品画像と画像保存ディレクトリの削除
            $deleteDirectoryName = str_pad(h($_POST['delete_target_product_id']), 3, 0, STR_PAD_LEFT); //0埋め処理
            unlink($deleteTargetProductImgPath); // rmdirは中にファイルが存在すると削除出来ないので、先にディレクトリ内の画像を削除
            rmdir('img/'.$deleteDirectoryName); // ディレクトリを消去
        }

        return $querySuccessFailure;
    }

    /**
     * 全商品の情報を二次元配列で取得
     * 
     * @param object
     * @return array
     */
    function get_product_list($pdo) {
        $sql = 'SELECT product_id, product_name, price, stock_qty, product_img, public_private FROM ec_products
        ORDER BY product_id DESC;'; // idの値の降順で並べ替え（新しく登録したものを上、古いものを下に表示するため）
        return get_sql_result($pdo, $sql);
    }

