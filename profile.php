<?php
  require_once 'config.php';

  if(!isset($_GET["id"])){
    header("Location: index.php");
  }

  $id = $_GET["id"];

  $query = $conn->prepare("SELECT users.*, images.data, images.type FROM users 
                             JOIN images ON users.image_id = images.id  
                            WHERE users.id=:id");

  $query->bindValue(":id", $id);
  $query->execute();
  $user = $query->fetch(PDO::FETCH_ASSOC);

  if(isset($_POST["upload_pic"])) {

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
    $sql = "SELECT * FROM users WHERE id IN ($friend_ids)";
    $query = $conn->prepare($sql);
    $query->execute();

    while($friend = $query->fetch(PDO::FETCH_ASSOC)) {
      array_push($friends, $friend);
    }
  }


  function base64_image($data, $mime) {
    $b64data = "data:".$mime.";base64," . base64_encode($data);
    #echo '<img src="'.$b64data.'" alt="" />'; 
    echo '<img src="data:image/jpeg;base64,'.base64_encode($data).'"/>';
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
    <div class="container">
      <div class="four columns">
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
            <input type="submit" name="upload_pic" />
          </form>
        <?php endif ?>
      </div>
      <div class="eight columns">
        <div class="user-profile">
          <div class="row">
            <div class="eight columns">
              <h1><?= "{$user["first_name"]} {$user["last_name"]}" ?>'s Profile</h1>
            </div>
            <div class="four columns">
              <?php if(logged_in($conn)): ?>
                <form action="follow.php" method="post">
                  <input type="hidden" name="followed_id" value="<?= $user["id"] ?>"/>
                  <?php if(!is_following(intval($current_user["id"]), $user["id"])): ?>
                    <input type="submit" name="follow" value="Follow" />
                  <?php else: ?>
                    <input type="submit" name="unfollow" value="Unfollow" />
                  <?php endif;?>
                </form>
              <?php endif; ?>
            </div>
          </div>

          <div class="row general-info">
            <h3> General Information </h3>
            <div class="info"> 
              <span class="desc"> First Name: </span>
              <?= $user["first_name"] ?>
            </div>
            <div class="info"> 
              <span class="desc"> Last Name: </span>
              <?= $user["last_name"] ?>
            </div>
            <div class="info"> 
              <span class="desc"> E-Mail: </span>
              <?= $user["email"] ?>
            </div>
          </div>

          <div class="row graduation-info">
            <h3> Gradutation Information </h3>
            <div class="info"> 
              <span class="desc"> Major: </span>
              <?= $user["major"] ?>
            </div>
            <div class="info"> 
              <span class="desc"> Academic Year: </span>
              <?= $user["academic_year"] ?>
            </div>
            <div class="info"> 
              <span class="desc">Term: </span>
              <?= $user["term"] ?>
            </div>
          </div>

          <div class="row friend-info">
            <h3> Friends </h3>
            <div> </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
