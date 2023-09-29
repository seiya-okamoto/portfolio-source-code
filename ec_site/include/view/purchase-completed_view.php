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
                margin: 30px 0 40px 0;
            }


            #completion-msg {    
                font-weight: bold;
                padding: 8px 10px 8px 10px;
            }

            .display-products-area {
                width: 85%;
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
                width: 60%;
            }

            .product-name {
                font-size: 18px;
                margin: 10px 0 10px 0;
            }

            .price {
                color: #d30000;
            }

            .total-amount {
                text-align: right;
                font-size: 20px;
                font-weight: bold;
            }

        </style>
    </head>
    <body>
        <!-- headerの読み込み -->
        <?php include_once '../../include/template/header_with_menu.php'; ?>

        <div class="container">
            <h2>購入完了</h2>

            <p class="completion-msg"><span id="completion-msg">お買い上げ、ありがとうございました！</span></p>

            <div class="display-products-area">
                <?php foreach ($cartList as $value) { ?>
                    <div class="flexbox">
                        <div class="display-img-size">
                            <img src="<?php echo $value['product_img']; ?>" >
                        </div>
                        <div class="product-info">
                            <div class="product-name"><?php echo $value['product_name']; ?></div>
                            <div class="price">&yen;<?php echo number_format($value['price']);?></div>
                            <div class="purchase-qty">数量：<?php echo $value['purchase_qty']; ?></div>
                        </div>
                    </div>
                <?php } ?>
                
                <div class="total-amount">合計：<?php echo h($totalAmount);?>円</div>

                <a href="catalog.php">商品一覧に戻る</a>
            </div>
        </div>

    </body>
</html>