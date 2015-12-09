<?php 
  require_once 'config.php';

  if(!logged_in($conn)) {
    header("Location: index.php");
  }
  
  $category = NULL;
  $value = NULL;

  if(isset($_GET["clear"])) {
    header("Location: graduates.php");
  }

  if(isset($_GET["filter"])) {
    $category = $_GET["category"];
    $value = $_GET["value"];
  }
  
  # Build Query
  $sql = "SELECT * FROM graduate_students";

  if(isset($category) && isset($value) && !empty($value)) {
    $sql = "$sql WHERE $category LIKE '$value'";
  }

  if(isset($_GET["sort_column"]) && isset($_GET["direction"])) {
    $sort_column = $_GET["sort_column"];
    $direction = $_GET["direction"];

    $sql = "$sql ORDER BY $sort_column $direction";
  }

  $query = $conn->prepare($sql);
  $query->execute();
  
  # Create links to sort table
  function sortable_link($column_name) {
    $link = "graduates.php";
    $current_direction = $_GET["direction"];
    
    if($current_direction === "ASC" || empty($current_direction)) {
      $direction = "DESC";
    } else {
      $direction = "ASC";
    }

    $link = $link."?sort_column=$column_name&direction=$direction";
    return $link;
  }

  function select_filter_tag() {
    $tag = '<select name="category">';
    $categories = [
      "first_name"    => "First Name", 
      "last_name"     => "Last Name", 
      "term"          => "Term", 
      "academic_year" => "Academic Year", 
      "level_code"    => "Level Code", 
      "degree"        => "Degree", 
      "major"         => "Major" 
    ];
    foreach($categories as $key => $value) {
      $selected = $_GET["category"] === $key ? "selected" : " ";
      $tag = $tag."<option value=\"{$key}\" {$selected} \">$value</option>";
    }
    $tag = $tag."</select>";
    return $tag;
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title> Graduated Students | UTEP Alumni Store </title>
    <?php require_once '_stylesheets.php'; ?>
  </head>
  <body>
    <?php require_once '_navbar.php'; ?>
    <div class="container">
      <div class="twelve columns"> 
        <h1> Graduated Students </h1>
        <form action="graduates.php">
          <label for="category">Filter By: </label>
          <?= select_filter_tag() ?>
          <input type="text" name="value" />
          <input type="submit" name="filter" value="Search"/>
          <input type="submit" name="clear" value="Clear Filter"/>
        </form>
        <table id="graduates">
          <thead>
          <th><a href="<?= sortable_link("first_name") ?>">First Name</a></th>
            <th><a href="<?= sortable_link("last_name") ?>">Last Name</a></th>
            <th><a href="<?= sortable_link("academic_year") ?>">Academic Year</a></th>
            <th><a href="<?= sortable_link("term") ?>">Term</a></th>
            <th><a href="<?= sortable_link("level_code") ?>">Level Code</a></th>
            <th><a href="<?= sortable_link("degree") ?>">Degree</a></th>
            <th><a href="<?= sortable_link("major") ?>">Major</a></th>
            <th></th>
          </thead>
          <tbody> 
            <?php while($graduate = $query->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
              <td><?= $graduate["first_name"] ?></td> 
              <td><?= $graduate["last_name"] ?></td> 
              <td><?= $graduate["academic_year"] ?></td> 
              <td><?= $graduate["term"] ?></td> 
              <td><?= $graduate["level_code"] ?></td> 
              <td><?= $graduate["degree"] ?></td> 
              <td><?= $graduate["major"] ?></td>
              <td style="text-align: center">
                <a href="profile.php?id=<?= $graduate["id"] ?>">
                  View Profile
                </a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody> 
        </table>
      </div>
    </div>
  </body>
</html>
