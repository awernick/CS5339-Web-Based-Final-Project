<?php
  require_once 'config.php';

  if(!isset($_GET["id"])){
    header("Location: index.php");
  }

  $id = $_GET["id"];

  $query = $conn->prepare("SELECT users.*, images.data, images.type FROM users 
                        LEFT JOIN images ON users.image_id = images.id  
                            WHERE users.id=:id");

  $query->bindValue(":id", $id);
  $query->execute();
  $user = $query->fetch(PDO::FETCH_ASSOC);

  # Update Profile Info
  if(isset($_POST["update_profile"])) {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];

    $query = $conn->prepare("UPDATE users 
                                SET first_name=:first_name, 
                                    last_name=:last_name, 
                                    email=:email
                              WHERE id=:id");
    $query->bindValue(":first_name", $first_name);
    $query->bindValue(":last_name", $last_name);
    $query->bindValue(":email", $email);
    $query->bindValue(":id", $user["id"]);
    $query->execute();
    header("Location: profile.php?id={$user["id"]}");
  }

  # Update Profile Picture
  if(isset($_POST["upload_pic"])) {
    if(empty($_FILES["profile_pic"]["tmp_name"])){
      header("Location: profile.php?id={$user["id"]}");
    }
    else {

    # Read file contents
    $tmp_file = $_FILES['profile_pic']['tmp_name'];
    $file = fopen($tmp_file, "r");
    $file_contents = fread($file, filesize($tmp_file));
    fclose($file);

    # Get Base64 data and MIME
    $file_contents = addslashes($file_contents);
    $info = getimagesize($_FILES['profile_pic']['tmp_name']);
    $mime = $info['mime'];

    # Prepare query for insertion
    $query = $conn->prepare("INSERT INTO images(data, type)
                    VALUES('$file_contents', :type)");

    $query->bindValue(":type", $mime);
    $query->execute();

    # Fetch image id and update the user profile
    $image_id = $conn->lastInsertId();

    # Update user with current image
    $query = $conn->prepare("UPDATE users 
                                SET image_id=:image_id 
                              WHERE id=:user_id");
    
    $query->bindValue(":image_id", $image_id);
    $query->bindValue(":user_id", $user["id"]);
    $query->execute();

    # Redirect to current profile to reload image
    header("Location: profile.php?id={$user['id']}");
    }
  }

  # Friends
  $friends = array();
  $query = $conn->prepare("SELECT * FROM friendships 
                            WHERE follower_id=:follower_id");

  $query->bindValue(":follower_id", $id);
  $query->execute();
  $friend_ids = array();

  while($friend = $query->fetch(PDO::FETCH_ASSOC)){
    array_push($friend_ids, $friend["followed_id"]);  
  }

  if(count($friend_ids) > 0) {
    $friend_ids = implode(',', $friend_ids);
    $sql = "SELECT users.*, images.data as image_data, images.type 
              FROM users
         LEFT JOIN images 
                ON users.image_id = images.id 
             WHERE users.id IN ($friend_ids)";
    $query = $conn->prepare($sql);
    $query->execute();

    while($friend = $query->fetch(PDO::FETCH_ASSOC)) {
      array_push($friends, $friend);
    }
  }

  function information_field_tag($field) {
    global $user, $current_user;

    $value = $user[$field];
    if(logged_in() && $current_user["id"] === $user["id"]){
      return "<input type=\"text\" name=\"$field\" value=\"$value\" form=\"update_prof\" \\>";
    } else {
      return $user["$field"];
    }
  }

  
?>
<!DOCTYPE html>
<html>
  <head>
  <title> <?= "{$user['first_name']} {$user['last_name']}'s Profile" ?>  | UTEP Alumni Store </title>
    <?php require_once '_stylesheets.php'; ?>
  </head>
  <body>
    <?php require_once '_navbar.php'; ?>
    <div class="container content">
      <div class="three columns sidebar">
        <div class="profile-picture">
          <?php if(!isset($user["image_id"])): ?>
            <img src="http://placehold.it/200x200">
          <?php else: ?>
            <?php base64_image($user["data"], $user["type"]) ?>
          <?php endif; ?>
        </div>
        <?php if(logged_in() && $current_user["id"] === $user["id"]): ?>
          <form action="profile.php?id=<?= $user["id"] ?>" method="post" enctype="multipart/form-data">
            <input type="file" name="profile_pic" value=""/>
            <input type="submit" name="upload_pic" value="Upload Picture"/>
          </form>
        <?php endif ?>
      </div>
      <div class="nine columns">
        <div class="user-profile">
          <div class="row">
            <div class="eight columns">
              <h1><?= "{$user["first_name"]} {$user["last_name"]}" ?>'s Profile</h1>
            </div>
            <div class="four columns">
              <?php if(logged_in() && $user["id"] != $current_user["id"]): ?>
                <form action="follow.php" method="post">
                  <input type="hidden" name="followed_id" value="<?= $user["id"] ?>"/>
                  <?php if(!is_following(intval($current_user["id"]), $user["id"])): ?>
                    <input type="submit" name="follow" value="Follow" class="follow_button"/>
                  <?php else: ?>
                    <input type="submit" name="unfollow" value="Unfollow" class="unfollow_button" />
                  <?php endif;?>
                </form>
              <?php endif; ?>
              <?php if(logged_in() && $user["id"] === $current_user["id"]):?>
                <form action="profile.php?id=<?= $user["id"] ?>" id="update_prof" method="post">
                  <input type="submit" name="update_profile" value="Update Profile" class="update-profile-button" />
                </form>
              <?php endif?>
            </div>
          </div>

          <div class="row general-info">
            <h3> General Information </h3>
            <div class="info"> 
              <span class="desc"> First Name: </span>
              <?= information_field_tag("first_name"); ?>
            </div>
            <div class="info"> 
              <span class="desc"> Last Name: </span>
              <?= information_field_tag("last_name"); ?>
            </div>
            <div class="info"> 
              <span class="desc"> E-Mail: </span>
              <?= information_field_tag("email"); ?>
            </div>
          </div>

          <div class="row graduation-info">
            <h3> Gradutation Information </h3>
            <div class="info"> 
              <span class="desc"> Major: </span>
              <?= information_field_tag("major"); ?>
            </div>
            <div class="info"> 
              <span class="desc"> Academic Year: </span>
              <?= information_field_tag("academic_year"); ?>
            </div>
            <div class="info"> 
              <span class="desc">Term: </span>
              <?= information_field_tag("term"); ?>
            </div>
          </div>

          <div class="row friend-info">
            <h3> Friends </h3>
            <?php foreach($friends as $friend): ?>
              <div class="friend"> 
                <div class="friend-icon">
                  <?php if(!isset($friend["image_data"])): ?>
                    <img src="http://placehold.it/50x50"/>
                  <?php else: ?>
                    <?php base64_image($friend["image_data"], $friend["type"]); ?>
                  <?php endif ?>
                </div>
                <div class="friend-name">
                  <a href="profile.php?id=<?= $friend["id"] ?>">
                    <?= $friend["first_name"] ?><br />
                    <?= $friend["last_name"] ?>
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
