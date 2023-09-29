<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EC SITE</title>
        <!-- テンプレ化したstyleの読み込み -->
        <?php include_once '../../include/template/style.php'; ?>
        <style type="text/css">
            .completion-msg {
                text-align: center;
                margin-bottom: 50px;
            }       

            .flexbox {
                display: flex;
                flex-wrap: wrap;
            }

            .flex-item {
                width: 25%;
                margin-bottom: 20px;
            }

            .flex-item-width {
                width: 90%;
                margin: 0 auto;
                border: 1px solid black;
                height: 400px;
            }

            .product-name {
                margin: 10px 0 10px 10px;
                font-size: 18px;
                height: 60px;
            }

            .display-img-size {
                width: 80%;
                height: 170px;
                position: relative;
                margin: 0 auto;
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

            .price {
                text-align: center;
                color: #d30000;
                margin-top: 20px;
            }

            .stock-qty {
                text-align: center;
                color: green;
            }

            .form-add-to-cart {
                text-align: center;
                margin-top: 20px;
            }

            .sold-out-btn {
                display: inline-block;
                width: 120px;
                height: 30px;
                border-radius: 5px;
            }

            .add-to-cart-btn {
                color: white;
                background-color: #2a55cc;
                border: none;
                display: inline-block;
                width: 120px;
                height: 30px;
                cursor: pointer;
                border-radius: 5px;
            }

            .add-to-cart-btn:hover {
                text-decoration: underline;
                text-underline-offset: 3px;
            }

            @media all and (max-width: 768px) {
                .flex-item {
                    width: 50%;
                }
            }

        </style>
    </head>
    <body>
        <!-- headerの読み込み -->
        <?php include_once '../../include/template/header_with_menu.php'; ?>

        <div class="container">
            <h2>商品一覧</h2>

            <!-- カートテーブルに購入商品情報を登録できたら、メッセージを表示 -->
            <?php if ($addToCartSuccessFailureAndProductName[0] === "クエリ成功") { ?>
            <p class="completion-msg"><span id="completion-msg"><?php echo h($addToCartSuccessFailureAndProductName[1]); ?>をカートに追加しました！</span></p>
            <?php } ?>

            <div class="flexbox">

            <?php foreach ($publicProductData as $value) { ?>
                <div class="flex-item">
                    <div class="flex-item-width">
                        <p class="product-name"><?php echo $value['product_name']; ?></p>
                        <div class="display-img-size">
                            <img src="<?php echo $value['product_img']; ?>" >   
                        </div>
                        <div class="price">
                            &yen;<?php echo number_format($value['price']);?>
                        </div>
                        <div class="stock-qty">
                            在庫数：<?php echo $value['stock_qty'];?>
                        </div>
                        <form method="post" class="form-add-to-cart">
                            <input type="hidden" name="add_to_cart_product_id" value=<?php echo $value['product_id']; ?>>
                            <?php if ($value['stock_qty'] === "0") { ?>
                            <input type="submit" class="sold-out-btn" disabled value="売り切れ">
                            <?php } else { ?>
                            <input type="submit" class="add-to-cart-btn" value="カートに入れる">
                            <?php } ?>
                        </form>
                    </div>
                </div>
            <?php } ?>

            </div>
        </div>
        
    </body>
</html>