<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EC SITE</title>
        <!-- テンプレ化したstyleの読み込み -->
        <?php include_once '../../include/template/style.php'; ?>
        <style type="text/css">
            .return-catalog {
                text-align: right;
            }

            .no-product-in-cart {
                font-size: 19px;
                margin-top: 30px;
            }

            .error-msg, .completion-msg {
                margin: 30px 0 30px 0;
                text-align: center;
            }

            .change_purchase_qty_note {
                margin: 20px 0 20px 0;
            }

            .display-products-area {
                width: 90%;
                margin: 0 auto 30px auto;
            }

            .flexbox {
                display: flex;
                border: 1px solid black;
                margin-bottom: 15px;
            }

            .display-img-size {
                width: 40%;
                height: 150px;
                position: relative;
                margin: 10px;
            }

            img {
                max-width: 100%;
                max-height: 100%;
                width: auto;
                height: auto;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }

            .product-info {
                margin-left: 20px;
                margin-bottom: 10px;
                width: 60%;
            }

            .product-name {
                font-size: 18px;
                margin: 5px 0 5px 0;
            }

            .price {
                color: #d30000;
            }

            .stock-qty {
                color: green;
            }

            .input-change-purchase-qty {
                width: 40px;
            }

            .products-change-btn {
                cursor: pointer;
            }

            .total-amount {
                text-align: right;
                font-size: 20px;
                font-weight: bold;
            }

            .form-purchase-btn {
                text-align: right;
                margin-top: 10px;
            }

            .purchase-btn {
                color: white;
                background-color: #2a55cc;
                border: none;
                display: inline-block;
                width: 160px;
                height: 35px;
                cursor: pointer;
                border-radius: 5px;
                font-size: 20px;
            }

            .purchase-btn:hover {
                text-decoration: underline;
                text-underline-offset: 3px;
            }

        </style>
    </head>
    <body>
        <!-- headerの読み込み -->
        <?php include_once '../../include/template/header_with_menu.php'; ?>
        
        <div class="container">
            <h2>ショッピングカート</h2>

            <div class="return-catalog">
                <a href="catalog.php">商品一覧に戻る</a>
            </div>

            <!-- カートが空ならば、表示 -->
            <?php if (empty($cartList)) { ?>
                <div class="no-product-in-cart">ショッピングカートに商品が入っていません。</div>
            <?php } ?>

            <!-- 他ユーザーの購入による在庫数減少に伴う、カート内商品削除や購入数変更があれば、そのメッセージを表示 -->
            <?php if ($deleteProductsMsg !== null) { ?>
                <?php for ($k = 0; $k <= count($deleteProductsMsg)-1;  $k++) { ?>
                    <p class="error-msg"><span id="error-msg"><?php echo $deleteProductsMsg[$k]; ?></span></p>
                <?php } ?>
            <?php } ?>
            <?php if ($changePurchaseQtyMsg !== null) { ?>
                <?php for ($l = 0; $l <= count($changePurchaseQtyMsg)-1;  $l++) { ?>
                    <p class="error-msg"><span id="error-msg"><?php echo $changePurchaseQtyMsg[$l]; ?></span></p>
                <?php } ?>
            <?php } ?>            

            <!-- 数量の入力エラーがあれば、入力エラーメッセージを表示 -->       
            <?php if (isset($changePurchaseQtyErrorMsg)) { ?>
                <p class="error-msg"><span id="error-msg"><?php echo $changePurchaseQtyErrorMsg; ?></span></p>
            <?php } ?>

            <!-- DBへ数量変更できたら、メッセージを表示 -->
            <?php if ($changePurchaseQtySuccessFailure === "クエリ成功") { ?>
                <p class="completion-msg"><span id="completion-msg"><?php echo h($_POST['change_target_product_name']); ?>の数量を変更しました！</span></p>
            <?php } ?>

            <!-- DBから商品情報の削除ができたら、メッセージを表示 -->
            <?php if ($deleteProductSuccessFailure === "クエリ成功") { ?>
                <p class="completion-msg"><span id="completion-msg"><?php echo h($_POST['delete_target_product_name']); ?>を削除しました！</span></p>
            <?php } ?>
            
            <!-- カートが空ではないならば、表示（カートが空ならば、表示しない） -->
            <?php if (!empty($cartList)) { ?>
                <div class="change_purchase_qty_note">※数量を変更する場合、1以上の整数(半角数字)を入力して下さい。</div>
            <?php } ?>        

            <div class="display-products-area">
                <?php foreach ($cartList as $value) { ?>
                    <div class="flexbox">
                        <div class="display-img-size">
                            <img src="<?php echo $value['product_img']; ?>" >
                        </div>
                        <div class="product-info">
                            <div class="product-name"><?php echo $value['product_name']; ?></div>
                            <div class="price">&yen;<?php echo number_format($value['price']);?></div>
                            <div class="stock-qty">在庫数：<?php echo $value['stock_qty'];?></div>
                            <form method="post">
                                <input type="hidden" name="change_target_product_id" value=<?php echo $value['product_id']; ?>>
                                <input type="hidden" name="change_target_product_name" value=<?php echo $value['product_name']; ?>>
                                <label for="qty">数量：</label>
                                <input type="text" class="input-change-purchase-qty" name="change_purchase_qty" required pattern="^([1-9][0-9]*)$" value=<?php echo $value['purchase_qty']; ?>>
                                <input type="submit" class="products-change-btn" value="変更">
                            </form>
                            <form method="post">
                                <input type="hidden" name="delete_target_product_id" value=<?php echo $value['product_id']; ?>>
                                <input type="hidden" name="delete_target_product_name" value=<?php echo $value['product_name']; ?>>
                                <input type="submit" class="products-change-btn" name="delete_product" value="削除">
                            </form>
                        </div>
                    </div>
                <?php } ?>
            
                <!-- カートが空ではないならば、合計金額と購入ボタンを表示（カートが空ならば、表示しない） -->
                <?php if (!empty($cartList)) { ?>
                    <div class="total-amount">合計：<?php echo h($totalAmount);?>円</div>
                    <form method="post" class="form-purchase-btn">
                        <input type="submit" class="purchase-btn" name="purchase_btn" value="購入する">
                    </form>
                <?php } ?>
            </div>
        </div>

    </body>
</html>