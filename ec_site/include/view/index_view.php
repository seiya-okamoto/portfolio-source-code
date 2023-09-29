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

            .user-name {
                margin-bottom: 5px;
            }

            .password {
                margin-bottom: 10px;
            }

            .checkbox {
                margin-bottom: 20px;
            }

            .link-to-user-registration {
                margin-top: 35px;
            }

            .login-btn {
                color: white;
                background-color: #2a55cc;
                border: none;
                display: inline-block;
                width: 200px;
                height: 25px;
                cursor: pointer;
                border-radius: 5px;
            }

            .login-btn:hover {
                text-decoration: underline;
                text-underline-offset: 3px;
            }

        </style>
    </head>
    <body>
        <!-- headerの読み込み -->
        <?php include_once '../../include/template/header.php'; ?>

        <div class="container">

            <h2>ログイン</h2>       
                             
            <?php if (isset($_POST['user_name']) && isset($_POST['password'])) { ?>  
                <p class="error-msg"><span id="error-msg">ユーザー名またはパスワードに誤りがあります。</span></p>
            <?php   } ?>       
                  
            <form method="post">
                <div class="user-name">
                    <label for="user-name">ユーザー名</label>
                    <input type="text" name="user_name" value="<?php echo h($_COOKIE['userName']); ?>">
                </div>
                <div class="password">
                    <label for="password">パスワード</label>
                    <input type="text" name="password" value="<?php echo h($_COOKIE['pass']); ?>">
                </div>
                <div class="checkbox">
                    <input type="checkbox" name="cookie_confirmation" value="checked" <?php echo $_COOKIE['cookie_confirmation']; ?>>次回からユーザー名、パスワードの入力を省略する
                </div>            
                <input type="submit" class="login-btn" value="ログイン">
            </form>
            
            <div class="link-to-user-registration">
                <a href="user-registration.php">ユーザー登録ページへ</a>
            </div>

        </div>

    </body>
</html>