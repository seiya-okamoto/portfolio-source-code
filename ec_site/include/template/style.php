<!-- 共通のstyleはここにまとめる -->
<style type="text/css">
    body {
        margin: 0;
        padding: 0;
        font-family: "Noto Sans JP", sans-serif;
    }

    header {
        width: 100%;
        height: 60px;
        background-color: #6696ff;
    }

    .header-flex {
        display: flex;
    }

    h1 {
        color: white;
        font-size: 25px;
        line-height: 60px;
        height: 60px;
        margin: 0 auto 0 30px
    }

    .cart-btn {
        color: white;
        text-decoration: none;
        display: block;
        line-height: 48px;
        height: 48px;
        cursor: pointer;
        font-weight: bold;
        font-size: 14px;
        margin: 6px 0 6px 0;
        padding: 0 10px 0 10px;
    }

    .logout-btn {
        color: white;
        background-color: transparent;
        border: none;
        display: block;
        line-height: 46px;
        margin: 6px 15px 6px 0;
        cursor: pointer;
        font-weight: bold;
        font-size: 14px;
    }

    .logout-btn:hover, .cart-btn:hover {
        border: 1px solid white;
    }

    .display-login-user-name {
        text-align: right;
        font-weight: bold;
    }

    .container {
        max-width: 1024px;
        width: 90%;
        margin: 0 auto;
    }

    .error-msg, .completion-msg {
        margin: 30px 0 30px 0;
    }

    #error-msg, #completion-msg {    
        font-weight: bold;
        padding: 8px 10px 8px 10px;                
        border-radius: 5px;
        display: inline-block;
        text-align: left;
    }

    #error-msg {
        color: red;
        background-color: #ffe2f0;
        border: 3px solid red;
    }

    #completion-msg {
        color: blue;
        background-color: #75cbf9;
        border: 3px solid blue;
    }

</style>