<?php
    //定数を格納したファイル
    //PHPのみで記述されたファイルには閉じタグを記述しないこと！！（終了タグの後に余分な空白や改行があると、予期せぬ挙動を引き起こす場合があるから）

    //DB接続に必要な情報
    define('DSN', 'mysql:host=localhost;dbname=xb513874_7lfo4');
    define('LOGIN_USER', 'xb513874_4cwi7');
    define('PASSWORD', 'nlm7legonh');

    //Cookie（ログイン情報）の保存期間           
    define('EXPIRATION_PERIOD', 30); //30日に設定