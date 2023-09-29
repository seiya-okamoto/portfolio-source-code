<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EC SITE</title>
        <!-- テンプレ化したstyleの読み込み -->
        <?php include_once '../../include/template/style.php'; ?>
        <style type="text/css">
            h2 {
                font-size: 21px;
            }

            .product-name, .price, .stock-qty, .product-img {
                margin-bottom: 2px;
            }

            .input-item-name {
                display: inline-block;
                width: 240px;
            }

            .left-alignment {
                text-align: left;
                float: left;
            }

            .right-alignment {
                text-align: right;
            }

            .input-product-name, .input-price, .input-stock-qty {
                width: 300px;
            }

            .input-conditions {
                font-size: 13px;
            }

            .registration-form-note {
                color: red;
                margin: 10px 0 10px 0;
            }

            .registration-btn {
                color: white;
                background-color: #ff6347;
                border: none;
                display: inline-block;
                width: 200px;
                height: 25px;
                cursor: pointer;
                border-radius: 5px;
                margin-left: 100px;
            }

            .registration-btn:hover {
                text-decoration: underline;
                text-underline-offset: 3px;
            }

            .headline-products-list {
                margin-top: 60px;
            }

            .responsive-table {
                overflow-x:auto;
            }

            table {
                width: 700px;
            }

            table, td, th {
                border: solid black 1px;
                margin-bottom: 30px;
            }

            td {
                text-align: center;
            }

            .td-product-img {
                width: 150px;
                height: 150px;
                position: relative;
            }

            .td-product-name {
                width: 150px;
            }

            .td-price {
                width: 100px;
            }

            .td-stock-qty {
                width: 170px;
            }

            .td-public-private {
                width: 110px;
            }

            .td-delete {
                width: 50px;
            }

            img {
                max-width: 80%; /*imgを親要素の80%のサイズ以内で表示*/
                max-height: 80%; /*imgを親要素の80%のサイズ以内で表示*/
                width: auto; /*アスペクト比を固定*/
                height: auto; /*アスペクト比を固定*/
                position: absolute; /*縦横中央表示*/
                top: 50%; /*縦横中央表示*/
                left: 50%; /*縦横中央表示*/
                transform: translate(-50%, -50%); /*縦横中央表示*/
            }

            .input-change-stock-qty {
                width: 50px;
            }

            .products-list-btn {
                cursor: pointer;
            }

            .private-background {
                background-color: #979797;
            }

            @media all and (max-width: 768px) {
                h1 {
                    font-size: 18px;
                    margin-left: 20px;
                }

                .logout-btn {
                    margin-right: 5px;
                }

                .display-login-user-name {
                    font-size: 14px;
                }

                h2 {
                    font-size: 18px;
                }

                .right-alignment {
                    visibility: hidden;
                }

                .product-name, .price, .stock-qty, .product-img {
                    margin-bottom: 15px;
                }

                .input-product-name, .input-price, .input-stock-qty {   
                    display: block;
                }

                .product-img-block {
                    display: block;
                }

                select {
                    display: block;
                }

                .registration-btn {
                    margin-left: 50px;
                }

                .registration-form-note {
                    margin: 15px 0 15px 0;
                }

            }

        </style>
    </head>
    <body>
        <header style="background-color: #f27d4b;">
            <div class="header-flex">          
                <h1>商品管理（管理者用ページ）</h1>
                <form action="index.php" method="post">
                    <input type="hidden" name="logout" value="logout">
                    <input type="submit" class="logout-btn" value="ログアウト">
                </form>
            </div>
        </header>

        <?php if (isset($_SESSION['admin_name'])) { ?>
        <div class="display-login-user-name"><?php echo h($_SESSION['admin_name'])."さん：ログイン中です"; ?></div>
        <?php } ?>
        
        <div class="container">
            <h2>商品登録</h2>

            <!-- $registrationErrorMsgに値があれば、入力エラーメッセージを表示 -->       
            <?php if (isset($registrationErrorMsg)) { ?>
                <p class="error-msg"><span id="error-msg"><?php echo $registrationErrorMsg; ?></span></p>
            <?php   } ?>
            
            <!-- DBへ商品登録成功したら、メッセージを表示 -->
            <?php if ($registrationSuccessFailure === "登録成功") { ?>
                <p class="completion-msg"><span id="completion-msg">商品登録完了しました！</span></p>
            <?php } ?>
            
            <form method="post" enctype="multipart/form-data" class="registration-form">
                <div class="product-name">
                    <label for="product-name" class="input-item-name">
                        <div class="left-alignment">商品名<span class="input-conditions">(20字以内)</span></div>
                        <div class="right-alignment">：</div>
                    </label>
                    <input type="text" class="input-product-name" name="product_name" required maxlength="20" value= <?php echo h($enteredProductName); ?>>
                </div>

                <div class="price">
                    <label for="price" class="input-item-name">
                        <div class="left-alignment">価格<span class="input-conditions">(半角数字、0以上の整数のみ)</span></div>
                        <div class="right-alignment">：</div>
                    </label>
                    <input type="text" class="input-price" name="price" required pattern="^([1-9][0-9]*|0)$" value= <?php echo h($enteredPrice); ?>>
                </div>

                <div class="stock-qty">
                    <label for="stock-qty" class="input-item-name">
                        <div class="left-alignment">在庫数<span class="input-conditions">(半角数字、0以上の整数のみ)</span></div>
                        <div class="right-alignment">：</div>
                    </label>
                    <input type="text" class="input-stock-qty" name="stock_qty" required pattern="^([1-9][0-9]*|0)$" value= <?php echo h($enteredStockQty); ?>>
                </div>

                <div class="product-img">
                    <label for="product-img" class="input-item-name">
                        <div class="left-alignment">商品画像<span class="input-conditions">(jpg, jpeg, pngのみ)</span></div>
                        <div class="right-alignment">：</div>
                    </label>
                    <input type="file" class="product-img-block" name="product_img" required accept=".jpg, .jpeg, .png">
                </div>

                <div class="public-private">
                    <label for="public-private" class="input-item-name">
                        <div class="left-alignment">ステータス</div>
                        <div class="right-alignment">：</div>
                    </label>
                    <select name="public_private">
                        <option value="public">公開</option>
                        <option value="private">非公開</option>
                    </select>          
                </div>
                
                <div class="registration-form-note">※すべての項目を入力して下さい</div>
                
                <input type="submit" class="registration-btn" value="商品を登録">
            </form>         


            <h2 class="headline-products-list">商品一覧</h2>

            <!-- 在庫数の入力エラーがあれば、入力エラーメッセージを表示 -->       
            <?php if (isset($changeStockQtyErrorMsg)) { ?>
                <p class="error-msg"><span id="error-msg"><?php echo $changeStockQtyErrorMsg; ?></span></p>
            <?php   } ?>

            <!-- DBへ在庫数変更できたら、メッセージを表示 -->
            <?php if ($changeStockQtySuccessFailure === "クエリ成功") { ?>
                <p class="completion-msg"><span id="completion-msg"><?php echo h($_POST['change_target_product_name']); ?>の在庫数を変更しました！</span></p>
            <?php } ?>

            <!-- DBへ公開フラグ変更できたら、メッセージを表示 -->
            <?php if ($changePublicPrivateSuccessFailure === "クエリ成功") { ?>
                <?php if (isset($_POST['make_private'])) { ?>
                    <p class="completion-msg"><span id="completion-msg"><?php echo h($_POST['public_private_target_product_name']); ?>を非公開にしました！</span></p>
                <?php } elseif(isset($_POST['make_public'])) { ?>
                    <p class="completion-msg"><span id="completion-msg"><?php echo h($_POST['public_private_target_product_name']); ?>を公開にしました！</span></p>
                <?php } ?>     
            <?php } ?>

            <!-- DBから商品情報の削除ができたら、メッセージを表示 -->
            <?php if ($deleteProductSuccessFailure === "クエリ成功") { ?>
                <p class="completion-msg"><span id="completion-msg"><?php echo h($_POST['delete_target_product_name']); ?>を削除しました！</span></p>
            <?php } ?>

            <div class="responsive-table">
            <table>
                <tr>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>公開 &frasl; 非公開</th>
                    <th>削除</th>
                </tr>
            <?php foreach ($productData as $value) { ?>
                <?php if ($value['public_private'] === "private") { ?>
                    <tr class="private-background">
                <?php } else { ?>
                    <tr>
                <?php } ?>
                        <td class="td-product-img">
                            <img src="<?php echo $value['product_img']; ?>">
                        </td>
                        <td class="td-product-name">
                            <?php echo $value['product_name']; ?>
                        </td>
                        <td class="td-price">
                            &yen;<?php echo number_format($value['price']);?>
                        </td>
                        <td class="td-stock-qty">
                            <form method="post">
                                <input type="hidden" name="change_target_product_id" value=<?php echo $value['product_id']; ?>>
                                <input type="hidden" name="change_target_product_name" value=<?php echo $value['product_name']; ?>>
                                <input type="text" class="input-change-stock-qty" name="change_stock_qty" required pattern="^([1-9][0-9]*|0)$" value=<?php echo $value['stock_qty']; ?>>
                                <input type="submit" class="products-list-btn" value="変更">
                            </form>
                        </td>
                        <td class="td-public-private">
                            <form method="post">
                                <input type="hidden" name="public_private_target_product_id" value=<?php echo $value['product_id']; ?>>
                                <input type="hidden" name="public_private_target_product_name" value=<?php echo $value['product_name']; ?>>
                            <?php if ($value['public_private'] === "public") { ?>
                                <input type="submit" class="products-list-btn" name="make_private" value="非公開にする">
                            <?php } else { ?>
                                <input type="submit" class="products-list-btn" name="make_public" value="公開にする">
                            <?php } ?>
                            </form>
                        </td>
                        <td class="td-delete">
                            <form method="post">
                                <input type="hidden" name="delete_target_product_id" value=<?php echo $value['product_id']; ?>>
                                <input type="hidden" name="delete_target_product_img" value=<?php echo $value['product_img']; ?>>
                                <input type="hidden" name="delete_target_product_name" value=<?php echo $value['product_name']; ?>>
                                <input type="submit" class="products-list-btn" name="delete_product" value="削除">
                            </form>
                        </td>
                    </tr>    
            <?php } ?>
            </table>
            </div>
        </div>
    </body>
</html>