<!DOCTYPE HTML>

<?php
session_start();
include_once('util.php');
?>

<HTML>
    <HEAD>
        <TITLE>Landing Page</TITLE>

        <?php
        $menu = 'landing-page';
        if (isset($_GET['menu'])) {
            $menu = $_GET['menu'];
            if ($menu == 'login') {
                $uName = $_POST['uName'];
                $pWord = $_POST['pWord'];

                $sql = "SELECT pWord "
                    . "FROM user "
                    . "WHERE uName='$uName'";

                $res = $db->query($sql);
                if (!$res) {
                    header("refresh: 2;url=?logout");
                    echo "Either username or password is incorrect.";
                } else if (md5($pWord) == $res->fetch()['pWord']){
                    $_SESSION['uid'] = getUID($db, $uName);
                    $uid = $_SESSION['uid'];
                    if (isAdmin($db, $uid)) {
                        header("Location: admin-user.php?menu=main");
                    } else {
                        header("Location: end-user.php?menu=dashboard");
                    }
                }
            } else if ($menu == 'logout') {
                unset($_SESSION['uid']);
                $menu = 'landing-page';
            }
        }
        $uid = isset($_SESSION["uid"]) ? $_SESSION["uid"] : 0;
        ?>

        <STYLE>
            body, html {
                height: 100%;
                margin: 0;
            }

            .bg {
                /* The image used */
                background-image: url("landing_page_bg_1.jpg");

                /* Full height */
                height: 100%;

                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            }

            .login-container {
                position: absolute;
                /* height: 300px; */
                width: 300px;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%); 
                background: rgba(255, 255, 255, 0.5); /* semi-transparent white */
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            input[type=text], input[type=password] {
                width: 89%;
                padding: 15px;
                margin: 5px 0 22px 0;
                border: none;
                background: #f1f1f1;
            }

            input[type=text]:focus, input[type=password]:focus {
                background-color: #ddd;
                outline: none;
            }

            .login-btn {
                background-color: #4CAF50;
                color: white;
                padding: 14px 20px;
                margin: 8px 0;
                border: none;
                cursor: pointer;
                width: 100%;
                opacity: 0.9;
            }

            .login-btn:hover {
                opacity: 1;
            }

            .checkbox {
                margin-bottom: 15px;
            }

            /* Add padding to container elements */
            .container {
                padding: 16px;
            }

        </STYLE>
    </HEAD>

    <BODY>
        <DIV class="bg">
            <DIV class="login-container">
                 <DIV class="container">
                    <?php if ($menu == 'landing-page') { ?>
                        <FORM action='?menu=login' method='post'>
                            <LABEL for="uName"><b>Username</B></LABEL>
                            <INPUT type="text" placeholder="Enter Username" name="uName" required>

                            <LABEL for="pWord"><b>Password</B></LABEL>
                            <INPUT type="password" placeholder="Enter Password" name="pWord" required>

                            <INPUT class="login-btn" type='submit' value='Login'/>
                        </FORM>

                    <?php } else if ($menu == 'main') {  ?>
                        <FORM action='?menu=logout' method='post'>
                            <INPUT type='submit' value='Logout'/>
                        </FORM>
                    <?php }  ?>
                 </DIV> <!- close container  ->
            </DIV> <!- close login-container  ->
        </DIV> <!- close bg  ->
    </BODY>
</HTML>

