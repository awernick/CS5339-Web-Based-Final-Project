<nav class="navbar">
  <div class="container">
    <div class="navbar-brand">
      <img src="./images/logo-utep.png" />
    </div>
    <ul class="navbar-list">
      <li><a href="index.php"> Home </a></li>
      <li><a href="graduates.php"> Graduated </a></li>
      <li><a href="store.php"> Store </a></li>
      <?php if(logged_in()): ?>
        <li><a href="message_board.php">Message Board</a></li>
        <li><a href="profile.php?id=<?= $current_user["id"] ?>">My Profile</a></li>
        <li><a href="logout.php"> Log Out </a></li>
      <?php else: ?>
        <li><a href="login.php"> Log In </a></li>
        <li><a href="register.php"> Register </a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
