
<?php

session_start();

include_once('bootstrap.php');
include_once('db.php');

?>
<SCRIPT>
    function change_page(url, time) {
        setTimeout(function() {
            window.location.href = url;
        }, time);
    }
</SCRIPT>


<STYLE>
    .meal-box {
        border: solid 2px blue;
        margin: 3px;
        padding: 10px;
    }

    .scrollable-box {
        overflow: auto;
        max-height: 85%;
        grid-row: 0;
        display: grid;
    }

    .browse_tab {
        display: grid;
        position: fixed;

        margin: 10px;
        padding: 2px;

        top: 100;
        bottom: 0;
        left: 250;
        right: 0;
    }

    label, input {
        font-family: cursive, sans-serif;
    }

    .ing_list {
        margin-bottom: 10px;
        margin-top: 10px;
        margin-right: 20px;
        padding: 4px;

        font-family: cursive, sans-serif;
        outline: 0;
        background: #2ECC71;
        color: #FFF;
        border: 1px solid crimson;
        border-radius: 9px;
    }

    .submit-button {
        margin-top: 20px;
        font-family: cursive, sans-serif;
    }
</STYLE>

<?php

function getUID($db, $uName) {
    $sql = "SELECT id "
         . "FROM user "
         . "WHERE uName='$uName'";

    $res = $db->query($sql);

    if ($res) {
        $row = $res->fetch();
        return $row['id'];
    }

    header('refresh:2;landing_page.php');
    echo 'Failed to login. Either the Username or Password is incorrect.';
}

function getUName($db, $uid) {
    $sql = "SELECT uName "
        . "FROM user "
        . "WHERE id=$uid";

    $res = $db->query($sql);
    if ($res) {
        $row = $res->fetch();
        return $row['uName'];
    }

    header("refresh:2;landing_page.php");
    echo 'Failed to login. Either the Username or Password is incorrect.';
}

function isAdmin($db, $uid) {
    $sql = "SELECT aid "
        . "FROM admin "
        . "WHERE aid IN ($uid)";

    $res = $db->query($sql);
    if ($res) {
        while ($res->fetch())
            return TRUE;
        return FALSE;
    } else {
        ?>
        <SCRIPT>
            change_page("landing_page.php", 1000);
        </SCRIPT>
        <?php
    }
}

function showProfile($db, $uid) {
    $sql = "SELECT * "
        . "FROM meal "
        . "WHERE uid=$uid";

    $res = $db->query($sql);
    if (!$res) {
        header("refresh: 2;url=end-user.php?menu=dashboard");
        echo "Could not find your meals";
    } else {
        echo "<DIV class='browse_tab'>";
        while ($meal = $res->fetch()) {
            echo "<DIV class='meal-box'>";
            $mid = $meal['mid'];
            $meal['ings'] = getIngredients($db, $mid);
            
            displayMeal($db, $meal);
            echo "</DIV>";
        }
        echo "</DIV>";
    }
}

// Shows the recipe with all steps, ingredients, and picture
function showRecipe($db, $mid) {
    $sql = "SELECT mName, image "
        . "FROM meal "
        . "WHERE mid=$mid";

    $res = $db->query($sql);
    if (!$res) {
        header('refresh:2?menu=dashboard');
        echo 'Failed to find the recipe!';
    }

    $mealInfo = $res->fetch();
    $mName = $mealInfo['mName'];
    $image = $mealInfo['image'];
    ?>

    <STYLE>
        .recipe {
            color: black;
            display: grid;
        }

        .meal-name {
            text-align: center;
            grid-row: 0;
        }
        
        .recipe_image {
            grid-row: 1;
            grid-column: 1;
            justify-self: center;
            margin: 25px;
        }

        .wrapper {
            display: inline-block;
        }

        .wrapper * {
            float: right;
        }

        .rating-label {
            font-size: 30px;
            cursor: pointer;
        }

        .rating {
            display: none;
        }

        .rating:checked ~ .rating-label {
            color: red;
        }
    </STYLE>

    <DIV class='recipe'>
        <P class='meal-name'><?php echo $mName ?></P>
        <DIV class=''>
            <?php
            $ings = getIngredients($db, $mid);
            $ing_types = array(
                'Dairy'      => array(),
                'Fruit'      => array(),
                'Protein'    => array(),
                'Vegetables' => array(),
                'Grain'      => array(),
                
                0       => 'Dairy',
                1       => 'Fruit',
                2       => 'Protein',
                3       => 'Vegetables',
                4       => 'Grain'
            );

            foreach ($ings as $ing) {
                $iName = $ing[1];
                $type  = $ing[2];

                array_push($ing_types[$type], $iName);
            }

            for ($i = 0; $i < count($ing_types) / 2; ++$i) {
                $type_array = $ing_types[($type = $ing_types[$i])];

                if (($size = count($type_array)) > 0) {
                    echo "<SELECT class='ing_list' name='$type'>";
                    echo "<OPTION>$type</OPTION>";
                
                    for ($f = 0; $f < $size; ++$f) {
                        $iName = $type_array[$f];
                        echo "<OPTION disabled>$iName</OPTION>";
                    }

                    echo "</SELECT>";
                }
            }
            ?>
        </DIV>

        <DIV style='display: grid; margin: 5px;'>
            <?php
            echo "<IMG class='recipe_image' src='$image' alt='$mName' width='200' height='200'>";
            echo "<DIV style='grid-column: 0; grid-row: 2; margin: 3px'>";
            $sql = "SELECT step "
                . "FROM meal NATURAL JOIN recipe_step "
                . "WHERE mid=$mid";

            $res = $db->query($sql);
            if (!$res) {
                header('refresh:2?menu=dashboard');
                echo 'Failed to find the recipe!';
            }

            $i = 0;
            while ($step = $res->fetch())
                echo "<P>" . ++$i . "). " . $step[0] . "</P>";
            ?>
        </DIV>
        <DIV class='review-bar'>
            <DIV style='grid-row: 1;'>

            </DIV>
            <DIV style='grid-row: 0; display: grid;'>
                <?php
                echo "<FORM style='grid-column: 1 / 25; grid-row: 0; display: grid;' method='post' action='?menu=addreview'>";
                echo "<DIV class='wrapper'>";
                for ($i = 1; $i <= 5; ++$i) {
                    ?>
                        <INPUT class='rating' type='radio' name='rating' id='rating<?php echo $i; ?>' value='<?php echo $i; ?>' />
                    <LABEL class='rating-label' for='rating<?php echo $i; ?>'>&#10038</LABEL>
                    <?php
                }
                echo "</DIV>";
                echo "<LABEL style='text-align: center; grid-row: 1;' for='review'>Review:</LABEL>";
                echo "<TEXTAREA style='grid-row: 0;' name='review' rows='2' cols='64' maxlength='256'></TEXTAREA>";
                echo "<INPUT type='hidden' name='mid' value='$mid' />";
                echo "<INPUT type='submit' value='submit' />";
                echo "</FORM>";
                ?>
            </DIV>
        </DIV>
        <DIV>
            <?php
            $sql = "SELECT rating, review "
                . "FROM review "
                . "WHERE mid=$mid";

            $res = $db->query($sql);
            if (!$res) {
                header("refresh:2;url=end-user.php?menu=browse");
                echo "Could not load reviews";
            } else {
                while ($r = $res->fetch()) {
                    $rating = $r['rating'];
                    $review = $r['review'];
                
                    echo "<P>$rating, $review</P>";
                }
            }
            ?>
        </DIV>
    </DIV>
    <?php
}

// Shows the meals in the browse menu. Allows the user to click on the
// recipe's name to access the information of the recipe.
function displayMeal($db, $mealInfo) {
    $mid = $mealInfo['mid'];
    $name = $mealInfo['mName'];
    $ings = $mealInfo['ings'];
 
    echo "<A style='grid-columns: 1;' href='?menu=mealClicked&mid=$mid'>$name</A>";
    echo "<BR/><BR/>";

    $ing_types = array(
        'Dairy'      => array(),
        'Fruit'      => array(),
        'Protein'    => array(),
        'Vegetables' => array(),
        'Grain'      => array(),
        
        0       => 'Dairy',
        1       => 'Fruit',
        2       => 'Protein',
        3       => 'Vegetables',
        4       => 'Grain'
    );

    foreach ($ings as $ing) {
        $iName = $ing[1];
        $type  = $ing[2];

        array_push($ing_types[$type], $iName);
    }

    for ($i = 0; $i < count($ing_types) / 2; ++$i) {
        $type_array = $ing_types[($type = $ing_types[$i])];

        if (($size = count($type_array)) > 0) {
            echo "<SELECT class='ing_list' name='$type'>";
            echo "<OPTION>$type</OPTION>";
        
            for ($f = 0; $f < $size; ++$f) {
                $iName = $type_array[$f];
                echo "<OPTION disabled>$iName</OPTION>";
            }

            echo "</SELECT>";
        }
    }
}

function getIngredients($db, $mid) {
    $sql = "SELECT * "
        . "FROM meal_uses NATURAL JOIN ingredient "
        . "WHERE mid=$mid";
    
    $res = $db->query($sql);
    if (!$res) {
        header('refresh:2?menu=dashboard');
        echo 'Could not find the ingredients for a recipe.';
    } else {
        $ings = array();
        while ($row = $res->fetch())
            array_push($ings, array($row[0], $row[2], $row[3]));
        
        return $ings;
    }
}

// Allows an end user to browse meals by displaying meals
// in rows of two.
//
// Also, allows the user to add ingredients to their list.
function browseCatalog($db) {
    $sql = "SELECT mid, mName "
        . "FROM meal";

    $res = $db->query($sql);

    ?>
    <DIV class='browse_tab'>
        <?php
        recipeForm($db);

        echo "<DIV class='scrollable-box'>";
        if ($res) {
            while ($meal = $res->fetch()) {
                echo "<DIV class='meal-box'>";
                $mid = $meal['mid'];
                $meal['ings'] = getIngredients($db, $mid);
                
                displayMeal($db, $meal);
                echo "</DIV>";
            }
        }
        echo "</DIV>";
    echo "</DIV>";
}

function recipeForm($db) {
    $types = [ 'Dairy', 'Fruit', 'Protein', 'Vegetables', 'Grain' ];

    echo "<FORM style='grid-row: 1;' name='recipe' action ='end-user.php?menu=generateRecipe'  method='post'>\n";

    $i = 0;
    foreach ($types as $type) {
        echo "<DIV style='display: inline-grid;'>";
        echo "<LABEL style='grid-row: 1; text-align: center' for='$type'>$type:</LABEL>";
        echo "<SELECT style='grid-row: 0;' class='ing_list' name='$type"."[]' size='2'  multiple>";

        $sql = "SELECT iid, iName FROM ingredient WHERE type = '$type'";
        $res = $db->query($sql);
        if ($res) {
            while ($row = $res->fetch()) {
                $iid = $row['iid'];
                $iName = $row['iName'];

                echo "<OPTION value='$iid'>$iName</OPTION>\n";
            }
        }

        echo "</SELECT>";
        echo "</DIV>";
        ++$i;
    }

    echo "<DIV class='submit-button'>";
    echo "<INPUT type='submit' value='Cooking...'>";
    echo "</DIV>";

    echo"</FORM>";
}

function genRecipe($db, $selectedIngredients) {
print_r($selectedIngredients);
$flattenedArray = [];

foreach ($selectedIngredients as $type => $ids) {
    // Merging the IDs of each type into a single array
    $flattenedArray = array_merge($flattenedArray, $ids);
}

// Creating a comma-separated string of all ingredient IDs
$ingredientIds = implode(", ", $flattenedArray);
    
    // echo $ingredientIds;
      
    $recipeSql = "SELECT DISTINCT meal.mid, meal.mName "
                . "FROM meal "
                . "INNER JOIN meal_uses ON meal.mid = meal_uses.mid "
                . "WHERE meal_uses.iid IN ($ingredientIds)";
    
    $possibleRecipes = $db->query($recipeSql);

    echo "<DIV class='browse_tab'>";
    recipeForm($db);
    if ($possibleRecipes) {
        echo "<DIV class='scrollable-box'>";
        while ($meal = $possibleRecipes->fetch()) {
            echo "<DIV class='meal-box'>";
            $mid = $meal['mid'];
            $meal['ings'] = getIngredients($db, $mid);

            displayMeal($db, $meal);
            echo "</DIV>";
        }
        echo "</DIV>";
    } else {
        echo "No recipes found with the selected ingredients.";
    }
}

function addReview($db, $uid, $reviewInfo) {
    $mid = $reviewInfo['mid'];
    $review = $reviewInfo['review'];
    $rating = $reviewInfo['rating'];

    $sql = "INSERT INTO review "
        . "VALUE($uid, $mid, $rating, '$review')";

    $res = $db->query($sql);
    if (!$res)
        echo "Error adding review.";
    else
        echo "<H2>Thank you for the review.</H2>";

    ?>
    <SCRIPT>
        change_page("end-user.php?menu=dashboard", 1000);
    </SCRIPT>
    <?php    
}

// Creates a form for an end user to create a meal
function makeRecipe($db) {

}

function click($db, $mid) {
    $sql = "INSERT INTO click(mid, cdate) VALUE($mid, CURRENT_DATE()); ";

    $res = $db->query($sql);
    if (!$res) {
        echo "<SCRIPT>change_page('?menu=dashboard', 1000)</SCRIPT>";
        echo "Error adding click to database";
    } else {
        echo "<SCRIPT>change_page('?menu=recipe&mid=$mid', 0);</SCRIPT>";
    }
}

// Generates a trending recipe, which is the recipe with the most engagement (clicks) today. If there is a tie between multiple, a random one of the most
// highly engaged recipes is selected.
function getTrendingRecipes($db, $uid) {

    $sql = "SELECT meal.mid, mName, image
            FROM click RIGHT OUTER JOIN meal
            ON meal.mid = click.mid AND cdate = CURRENT_DATE
            GROUP BY meal.mid
            HAVING COUNT(cid) >= ALL (
                SELECT COUNT(cid)
                FROM click
                WHERE cdate = CURRENT_DATE
                GROUP BY click.mid
            );";

    $res = $db->query($sql);
    if ($res == FALSE) {
        echo "SQL query error: a trending recipe was not generated.";
    } else {
        $recipes = $res->fetchAll();
        $trendingNum = random_int(0, count($res) - 1);
        echo "<DIV class='col-5'></DIV><P>There were " . count($recipes) . " results.</P>";

        $trending = $recipes[$trendingNum];

        ?>
            <DIV class='col-12' style='text-size: 30px'>Trending recipe</DIV>


           
        <?php

       
    }
   
    //  style='width: 70%; height: 50%; text-align: center'
   
}

function displaySavedRecipe($db, $uid) {
    echo '<STYLE>
        .user-profile, .saved-recipes{
             color: black; /* Sets text color to black */
         }
    </STYLE>';
    echo '<DIV class="user-profile">';

    // SQL query to get the saved recipes for the user
    $recipeSql = "SELECT m.mid, m.mName, m.image "
               . "FROM meal m "
               . "INNER JOIN saved_recipes AS sr ON m.mid = sr.mid "
               . "WHERE sr.uid = $uid"; 

   
    $possibleRecipes = $db->query($recipeSql);

    if ($possibleRecipes) {
        echo "<DIV class='scrollable-box'>";
        while ($meal = $possibleRecipes->fetch()) {
            echo "<DIV class='meal-box'>";
            $mid = $meal['mid'];
            $meal['ings'] = getIngredients($db, $mid);

            displayMeal($db, $meal);
            echo "</DIV>";
        }
        echo "</DIV>";
    } else {
        echo "No recipes saved.";
    }
}



function profileForm(){
    ?>
        <STYLE>
            .account_btn {
                    padding: 15px 20px; 
                    margin: 5px 10px; 
                    border: none; 
                    border-radius: 15px; 
                    text-align: left; 
                    font-size: 18px; 
                    font-weight: bold; 
                    background-color: rgba(255, 255, 255, 0.7); 
                    color: #333333; 
                    display: block; 
                    transition: background-color 0.3s ease; 
                }
    
                .account_btn:hover {
                    background-color: #34495E; 
                    color: #1ABC9C; 
                    text-decoration: none; 
                }

                .culinary-note {
                     background-color: rgba(255, 255, 255, 0.7);
                     padding: 20px;
                     margin: 20px 0;
                     border-radius: 8px;
                     box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                     font-family: Arial, sans-serif;
                     color: #333;
                }  

        </STYLE>
    
        <DIV class='grid'>
        <A class='account_btn' style='grid-column: 1' href='?menu=saved_recipe'>Saved Recipes</A>
        <A class='account_btn' style='grid-column: 2' href='?menu=diary'>Diary</A>
        <A class='account_btn' style='grid-column: 3' href='?menu=write'>Write</A>
        </DIV>

        <DIV class='culinary-note'>
        <H2>Your Culinary Collection</H2>
        <P>Welcome to Your Culinary Collection!
        This is your go-to spot for all the recipes you've saved. More than just a list, it's a place to keep track of your cooking experiences.
        Made a tweak to a recipe that worked wonders? Jot it down. Want to remember something for next time? Add a note. Your thoughts and experiences give these recipes a personal touch. 
        Feel free to share your kitchen adventures – the ups and the downs. Your stories and tips could be just what another cook needs to hear!.</P>
        </DIV>
    
    <?php
    }

function blogForm($db, $uid){
?>
<style>
            form {
                background-color: rgba(255, 255, 255, 0.4); 
                padding: 30px; 
                border-radius: 8px; 
                box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
                margin: 20px auto; 
                width: 50%; 
                max-width: 600px; 
            }
            input[type='text'], textarea {
                width: calc(100% - 16px); 
                padding: 12px; 
                margin-bottom: 15px; 
                border: 1px solid #ccc; 
                border-radius: 4px; 
                background-color: rgba(255, 255, 255, 0.7); 
                box-sizing: border-box; 
            }
            input[type='submit'] {
                background-color: #337ab7; 
                color: white;
                padding: 15px 20px; 
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.2s; 
                font-weight: bold;
                width: 30%; 
            }
            input[type='submit']:hover {
                background-color: #286090; 
            }
          </style>

<FORM name='formPost' action='?menu=post_content' method='post'>
<?php echo"<P><INPUT type='hidden' name='uid' value=$uid /></p>"; ?>
<P><INPUT type='text' name='subject' placeholder='Type subject here' required /></P>
<P><TEXTAREA rows='5' cols='30' name='content' placeholder='Type content here' required></TEXTAREA></P>
<P><INPUT type='submit' value='Post!' /></P>
</FORM>

<?php
}

function updateDiary($db, $contentData){
    $uid     = $contentData['uid'];
    $subject = $contentData['subject'];
    $content = $contentData['content'];

    $sql = "INSERT INTO post(uid, subject, content, date)" 
         . "VALUE($uid, '$subject', '$content', NOW());";

    $res = $db->query($sql);

    if ($res != FALSE) {
        header("refresh:2;url=end-user.php?menu=dashboard");
        printf("<div style='color: black;'><H3>successfully post!</H3>\n");
    }
    else {
        header("refresh:2;url=end-user.php?menu=dashboard");
        printf("<div style='color: black;'><H3>failed to post</H3>\n");  
    }
} 

function showDiary($db, $uid) {
    $sql = "SELECT * FROM post WHERE uid=$uid ORDER BY date DESC";
    $res = $db->query($sql);

    // Define the style for the diary entries outside of the loop
    echo "<style>
            .diary-container {
                max-width: 800px; 
                margin: 20px auto; 
                padding: 20px;
                background-color:rgba(255, 255, 255, 0.4); 
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
            }
            .diary-entry {
                background-color: #f9f9f9; 
                color: #333; 
                border-left: 4px solid #34495E; 
                padding: 20px;
                margin-bottom: 20px; 
                border-radius: 8px; 
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            .diary-subject {
                font-weight: bold;
                font-size: 1.25em; 
                margin-bottom: 10px; 
            }
            .diary-date {
                font-size: 0.85em; 
                color: #555; 
                margin-bottom: 15px; 
            }
            .diary-content {
                white-space: pre-wrap; 
                line-height: 1.6; 
            }
        </style>";

    if ($res != FALSE) {
        // echo "<DIV class='browse_tab'>";
        echo "<DIV class='container diary-container'>";
        echo "<DIV class='scrollable-box'>";
        while ($row = $res->fetch()) {
            $subject = $row['subject'];
            $content = $row['content']; 
            $date = $row['date']; 

            // Echo the diary entry with subject and content
            echo "<DIV class='diary-entry'>";
            echo "<DIV class='diary-subject'>$subject</DIV>";
            echo "<DIV class='diary-date'>Posted on $date</DIV>"; // Display the date
            echo "<DIV class='diary-content'>$content</DIV>";
            echo "</DIV>";
        }
        echo "</DIV>";
        echo "</DIV>";
    } else {
        echo "<P>Error loading diary entries!</P>";
    }
}

?>
