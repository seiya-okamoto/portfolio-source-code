<header>
    <div class="header-flex">          
        <h1>EC SITE</h1>
        <a class="cart-btn" href="cart.php">カート</a>
        <form action="index.php" method="post">
            <input type="hidden" name="logout" value="logout">
            <input type="submit" class="logout-btn" value="ログアウト">
        </form>
    </div>
</header>

<?php if (isset($_SESSION['user_name'])) { ?>
    <div class="display-login-user-name"><?php echo h($_SESSION['user_name'])."さん：ログイン中です"; ?></div>
<?php } ?>