<?php
  require_once 'config.php';
  
  if(!isset($_GET["id"])) {
    header("Location: store.php");
  }

  $query = $conn->query("SELECT * 
                           FROM products
                      LEFT JOIN images
                             ON products.image_id = images.id
                          WHERE products.id='{$_GET["id"]}'");
  if($query->num_rows == 0) {
    header("Location: store.php");
  }

  $product = $query->fetch_assoc();
?>

<!DOCTYPE html>
<html>
  <head>
    <title> Store | UTEP Alumni Store </title>
    <?php require_once '_stylesheets.php'; ?>
  </head>
  <body>
    <?php require_once '_navbar.php'; ?>
    <div class="container content">
      <div class="back">
        <a href="store.php">Back to Store</a>
      </div>
      <div class="product">
        <div class="three columns"> 
          <div class="product-image">
            <? if(isset($product["data"])): ?>
              <?= base64_image($product["data"], $product["type"]); ?>
            <?php else: ?>
               <img src="http://placehold.it/200x200" />
            <?php endif; ?>
          </div>
        </div>
        <div class="nine columns">
          <h1> <?= $product["name"] ?> </h1>
          <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="<?= $product["sku"] ?>">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_cart_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
          </form>
          <hr/>
          <div class="product-info">
            <div class="product-sku">
              <div class="title"> SKU: </div>
              <div class="value"> <?= $product["sku"] ?></div>
            </div>
            <div class="product-price">
              <div class="title">Price:</div>
              <div class="value">$<?= $product["price"]; ?></div>
            </div>
            <div class="product-desc">
              <div class="title">Description:</div>
              <div class="value"><?= $product["description"] ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
