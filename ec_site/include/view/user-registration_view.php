<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EC SITE</title>
        <!-- テンプレ化したstyleの読み込み -->
        <?php include_once '../../include/template/style.php'; ?>
        <style type="text/css">
            .container {
                text-align: center;
            }

            h2 {
                margin: 50px 0 60px 0;
            }

            .necessary {
                color: red;
                border: solid 1px red;
                border-radius: 4px;
                font-size: 12px;
                padding: 0 5px;
                margin-left: 5px;
            }

            .input-conditions {
                font-size: 13px;
            }

            .input-field {
                display: block;
                width: 300px;
                margin: 0 auto;
            }

            .password {
                margin-top: 15px;
            }

            .register-btn {
                margin: 20px 0 35px 0;
                color: white;
                background-color: #2a55cc;
                border: none;
                display: inline-block;
                width: 200px;
                height: 25px;
                cursor: pointer;
                border-radius: 5px;
            }

            .register-btn:hover {
                text-decoration: underline;
                text-underline-offset: 3px;
            }

        </style>
    </head>
    <body>
        <!-- headerの読み込み -->
        <?php include_once '../../include/template/header.php'; ?>

        <div class="container">

            <h2 class="user-registration-headline">ユーザー登録</h2>        
            
            
            <!-- $errorMsgに値があれば、入力エラーメッセージを表示 -->       
            <?php if (isset($errorMsg)) { ?>
                <p class="error-msg"><span id="error-msg"><?php  echo $errorMsg; ?></span></p>
            <?php   } ?>    
                    
            <!-- ユーザー登録完了したら、メッセージを表示 -->
            <?php if ($registrationSuccessFailure === "クエリ成功") { ?>        
                <p class="completion-msg"><span id="completion-msg">ユーザー登録完了しました！</span></p>
            <?php } ?>


            <?php if ($registrationSuccessFailure !== "クエリ成功") { ?>        
                <form method="post">
                    <div>
                        <label for="user-name">ユーザー名<span class="input-conditions">（半角英数字、5〜20文字）</span><span class="necessary">必須</span></label>
                        <input type="text" name="user_name" class="input-field" required pattern="^([a-zA-Z0-9]{5,20})$" value= <?php echo h($_POST['user_name']); ?>>                    
                    </div>

                    <div class="password">
                        <label for="password">パスワード<span class="input-conditions">（半角英数字、8〜20文字）</span><span class="necessary">必須</span></label>
                        <input type="text" name="password" class="input-field" required pattern="^([a-zA-Z0-9]{8,20})$" value= <?php echo h($_POST['password']); ?>>                    
                    </div>
                    
                    <input type="submit" value="登録" class="register-btn">
                </form>
            <?php } ?>    

            <a href="index.php">ログインページへ</a>

        </div>

    </body>
</html>