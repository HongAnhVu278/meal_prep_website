
<?php include("util.php"); ?>

<HTML>
    <HEAD>
        <TITLE></TITLE>
        <?php 
        include_once("bootstrap.php");
        $uid = $_SESSION['uid'];
        ?>
        
        <STYLE>
            .main {
                height: 100%;
                position: fixed;
                z-index: 2;

                display: flex;
                justify-content: center;
                align-items: center;

                top: 0;
                left: 250;
                right: 0;

                background-image: url('main_bg_2.jpg'); 
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;

                overflow-x: hidden;
                padding: 20px; 
            }

            .grid {
                position: fixed;
                display: grid;

                top: 0;
                left: 250;
                right: 0;
            }

            .sidenav {
                height: 100%;
                width: 250px; /* Adjust width as needed */
                position: fixed;
                z-index: 1;
                top: 0;
                left: 0;
                background-color: #243447; /* Dark blue background */
                color: white; /* White text color */
                box-shadow: 4px 0 10px 0 rgba(0, 0, 0, 0.25); /* Shadow for depth */
                overflow-x: hidden;
                padding-top: 20px; /* Spacing at the top */
                text-align: left; /* Align text to the left */
            }
            

            .user-icon {
                width: 100px; /* Larger icon */
                height: 100px;
                margin: 0 auto 20px auto; /* Centered icon */
                border-radius: 50;
                background-color: #808080; /* Lighter grey for the icon */
                display: flex; /* Use flexbox for centering */
                align-items: center;
                justify-content: center;
            }

            .user-icon::before {
                content: '\1F464'; /* Unicode for user icon */
                font-size: 50px; /* Larger icon size */
            }

            .menuItem {
                padding: 15px 20px; /* Larger click area */
                margin: 5px 10px; /* Spacing between items */
                border: none; /* No border */
                border-radius: 15px; /* Rounded corners for the items */
                text-align: left; /* Align text to the left */
                font-size: 18px; /* Larger font size */
                font-weight: bold; /* Bold font weight */
                background-color: #2C3E50; /* Subtle change in item background */
                color: #FFFFFF; /* White text color */
                display: block; /* Block display for the full-width effect */
                transition: background-color 0.3s ease; /* Transition for hover effect */
            }

            .menuItem:hover {
                background-color: #34495E; /* Darker shade on hover */
                color: #1ABC9C; /* Light blue color for the text on hover */
                text-decoration: none; /* No underline on hover */
            }

            A {
                text-decoration: none;
            }
        </STYLE>
    </HEAD>

    <BODY>
        <DIV class='container'>
            <DIV class='sidenav'>
            <DIV class='user-icon'></DIV>
                <DIV style='margin: auto; width: 50%; text-align: left;'>
                    Welcome
                    <P style='color: #0FAF80'><?php echo getUName($db, $uid); ?></P>
                </DIV>

                <DIV style='margin: 10px;'>
                    <A class='sidenav-links menuItem' href='landing_page.php?menu=logout'>Sign out</A>
                </DIV>

                <DIV style='margin: 10px;'>
                    <A class='sidenav-links menuItem' href='?menu=account'>Account</A>
                </DIV>
                
                <DIV style='margin: 10px;'>
                    <A class='sidenav-links menuItem' href='?menu=dashboard'>Dashboard</A>
                </DIV>

                <DIV style='margin: 10px;'>
                    <A class='sidenav-links menuItem' href='?menu=browse'>Browse Recipe</A>
                </DIV>

                <DIV style='margin: 10px;'>
                    <A class='sidenav-links menuItem' href='?menu=trendingRecipes'>Trending Recipe</A>
                </DIV>

            <DIV class='main'> 
                <?php
                $menu = $_GET['menu'];
                $uid = $_SESSION['uid'];
                if ($menu == 'dashboard') {
                    showProfile($db, $uid);
                } else if ($menu == 'browse') {
                    browseCatalog($db);
                } else if($menu == 'generateRecipe') {
                    genRecipe($db, $_POST);
                } else if ($menu == 'recipe') {
                    showRecipe($db, $_GET['mid']);
                } else if ($menu == 'makeRecipe') {
                    makeRecipe($db);
                } else if ($menu == 'trendingRecipes') {
                    getTrendingRecipes($db, $uid);
                } else if ($menu == 'addreview') {
                    addReview($db, $uid, $_POST);
                } else if ($menu == 'account'){
                    profileForm();
                } else if ($menu == 'saved_recipe') {
                    displaySavedRecipe($db, $uid);
                } else if ($menu == 'write') {
                    blogForm($db, $uid);
                } else if ($menu == 'post_content') {
                    updateDiary($db, $_POST);
                }else if ($menu == 'diary') {
                    showDiary($db, $uid);
                } else if ($menu == 'mealClicked') {
                    click($db, $_GET['mid']);
                }
                ?>
            </DIV>
        </DIV>
    </BODY>
</HTML>
