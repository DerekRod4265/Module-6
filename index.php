<?php

$databaseConnection = mysqli_connect("localhost", "root", "root", "socialmedia");

if (mysqli_connect_error()) {
    exit("Database connection failed!");
}

$errors = [];
session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
} else {
    ?>
    <div class="logoutbtn">
    Welcome <?php echo ($_SESSION['nickname']); ?>
    <a href="logout.php">Logout</a>
    </div>
    <?php
    $userId = $_SESSION['userId'];
}   
?>

<?php
if (mysqli_connect_error()) {
    exit("Database connection failed!");
}


if (isset($_GET['postDeleteId'])) {
    $postDeleteId = mysqli_real_escape_string($databaseConnection, $_GET['postDeleteId']);

    $sql = "DELETE FROM posts WHERE id='" . $postDeleteId . "'";
    $postDeletionSuccessful = mysqli_query($databaseConnection, $sql);

    if ($postDeletionSuccessful) {
        header("Location: index.php");
        exit();
    } else {
        echo (mysqli_error($databaseConnection));
        mysqli_close($databaseConnection);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editPostClicked'])) {
    $postToEdit = mysqli_real_escape_string($databaseConnection, $_GET['postToEdit']);
    $updatedPost = mysqli_real_escape_string($databaseConnection, $_POST['updatedPost']);

    if (!isset($updatedPost) || trim($updatedPost) === "") {
        $errors[] = "Cannot leave the post empty!";
    }

    if (empty($errors)) {
        $sql = "UPDATE posts SET ";
        $sql .= "postContent='" . $updatedPost . "' ";
        $sql .= "WHERE id='" . $postToEdit . "'";
        $postUpdatedSuccessful = mysqli_query($databaseConnection, $sql);

        if ($postUpdatedSuccessful) {
            header("Location: index.php");
            exit();
        } else {
            echo (mysqli_error($databaseConnection));
            mysqli_close($databaseConnection);
            exit();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['postButton'])) {
    $postContent = mysqli_real_escape_string($databaseConnection, $_POST["postContent"]);

    if (!isset($postContent) || trim($postContent) === "") {
        $errors[] = "Cannot post an empty content!";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO posts (postContent, userId) VALUES ('" . $postContent . "', '" . $userId . "')";
        $postInsertionSuccessful = mysqli_query($databaseConnection, $sql);

        if (!$postInsertionSuccessful) {
            echo (mysqli_error($databaseConnection));
            mysqli_close($databaseConnection);
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <h1>Welcome to Social Media</h1>

    <span class="error">
        <?php
        foreach ($errors as $currentError) {
            echo ($currentError);
        }
        ?>
    </span>

    <form action="index.php" method="post">
        <textarea name="postContent"></textarea>
        <br>
        <br>
        <input type="submit" value="Post" name="postButton">
    </form>

    <?php
    $sql = "SELECT * FROM posts";
    $allPosts = mysqli_query($databaseConnection, $sql);

    while ($currentPost = mysqli_fetch_assoc($allPosts)) {
        ?>
        <article>
            <?php echo ($currentPost['date']); ?>:
            <?php echo (htmlspecialchars($currentPost['postContent'])); ?>: by
            <?php
            $sql = "SELECT * FROM users WHERE id ='" . $currentPost['userId'] . "'";
            $userOfPost = mysqli_query($databaseConnection, $sql);
            $userOfPost = mysqli_fetch_assoc($userOfPost);
            ?>
            <?php echo ($userOfPost['nickname']); ?>:
            <?php
            if ($userOfPost['id'] == $userId) {
                ?>
                <a href="<?php echo ("index.php?postDeleteId=" . urlencode($currentPost['id'])); ?>">Delete</a>
                <a href="<?php echo ("index.php?postEditId=" . urlencode($currentPost['id'])); ?>">Edit</a>
                <?php
            }
            ?>
        </article>

        <?php
        if (isset($_GET['postEditId']) && $currentPost['id'] == $_GET['postEditId']) {
            $postEditId = mysqli_real_escape_string($databaseConnection, $_GET['postEditId']);
            $sql = "SELECT * FROM posts WHERE id='" . $postEditId . "'";
            $postToEdit = mysqli_query($databaseConnection, $sql);
            $postToEdit = mysqli_fetch_assoc($postToEdit);
            ?>
            <form action="<?php echo ("index.php?postToEdit=" . urlencode($postToEdit['id'])); ?>" method="POST">
                <textarea name="updatedPost"><?php echo ($postToEdit['postContent']); ?></textarea>
                <input type="submit" value="Edit post" name="editPostClicked">
            </form>
            <?php
        }
    }
    ?>

    <?php
    mysqli_close($databaseConnection);
    ?>
    </div>

</body>
</html>