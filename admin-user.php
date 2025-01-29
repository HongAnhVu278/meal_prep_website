
<?php include_once("util.php"); ?>

<HTML>
    <HEAD>
        <TITLE>Admin Page</TITLE>
        <?php
        $uid = $_SESSION['uid'];
        ?>
    </HEAD>

    <STYLE>
        .sidenav {
            height: 100%;
            width: 250px;
            position: fixed;
            z-index: 1;

            top: 0;
            left: 0;

            background-color: #F8F8F8;

            overflow-x: hidden;
            padding-top: 10px;
            text-align: center;
        }
    </STYLE>

    <BODY>
        <DIV class='container'>
            <DIV class='sidenav'>
                <?php
                $sql = "SELECT aid, uName "
                    . "FROM admin JOIN user "
                    . "ON id=aid "
                    . "WHERE aid IN ("
                    . " SELECT uid "
                    . " FROM manages "
                    . " WHERE aid=$uid)";
                
                $res = $db->query($sql);
                if ($res) {
                    while ($row = $res->fetch()) {
                        $id = $row['aid'];
                        $uName = $row['uName'];
                        echo "<DIV class='admin-user'>";
                        echo "<A href='?menu=admin-user&uid=$id'>$uName</A>";
                        echo "</DIV>";
                    }
                } else {
                    echo "<SCRIPT>change_page('landing_page.php', 1000)</SCRIPT>";
                    echo "Could not find admin users.";
                }

                $sql = "SELECT uid, uName "
                    . "FROM end JOIN user "
                    . "ON id=uid "
                    . "WHERE uid IN ("
                    . " SELECT uid "
                    . " FROM manages "
                    . " WHERE aid=$uid)";

                $res = $db->query($sql);
                if ($res) {
                    while ($row = $res->fetch()) {
                        $id = $row['uid'];
                        $uName = $row['uName'];
                        echo "<DIV class='end-user'>";
                        echo "<A href='?menu=end-user&uid=$id'>$uName</A>";
                        echo "</DIV>";
                    }
                } else {
                    echo "<SCRIPT>change_page('landing_page.php', 1000)</SCRIPT>";
                    echo "Could not find end users.";
                }
                ?>
            </DIV>
            
            <?php
            $menu = $_GET['menu'];
            $id = $_GET['uid'];
            if ($menu == 'admin-user')
                echo "<DIV class=''>";            
            else if ($menu = 'end-user')
                echo "<DIV class=''>";
            
            displayUserInfo($db, $id);
            echo "</DIV>";
            ?>
        </DIV>
    </BODY>
</HTML>
