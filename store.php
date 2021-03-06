<?php
  require_once 'config.php';
  
  $query = $conn->query("SELECT products.*, images.data, images.type 
                           FROM products
                      LEFT JOIN images
                             ON products.image_id = images.id");
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
      <div class="twelve columns">
        <h1> Alumni Store </h1>
        <hr/>
        <div class="products">
          <?php while($product = $query->fetch_assoc()): ?>
            <div class="product">
              <div class="product-image"> 
                <?php if(isset($product["data"])): ?> 
                  <?= base64_image($product["data"], $product["type"]); ?>
                <?php else: ?>
                  <image src="http://placehold.it/200x200"/>
                <?php endif; ?>
              </div>
              <div class="product-name">
                <a href="product.php?id=<?= $product["id"] ?>">
                  <?= $product["name"] ?>
                </a>
              </div>
              <div class="product-price">
                $<?= $product["price"] ?>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </body>
</html>
