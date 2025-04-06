<style>
  nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 8vh;
    padding: 0 2%;
    background-color: #F3EEE5;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
  }

  .nav-left,
  .nav-center,
  .nav-right {
    display: flex;
    align-items: center;
  }

  .nav-left img,
  .nav-right img {
    width: 3.5vw;
    height: auto;
  }

  .nav-center {
    gap: 2vw;
  }

  .nav-center a,
  .nav-right a {
    text-decoration: none;
    color: #000;
    font-weight: bold;
    margin-left: 1vw;
  }

  .nav-right {
    gap: 1vw;
  }
</style>

<nav>
  <div class="nav-left">
  <a href="http://127.0.0.1/pfe/EASYLEGAL/index.php">
  <img src="http://127.0.0.1/pfe/EASYLEGAL/assets/logo.png" alt="Logo">
    </a>
  </div>

  <div class="nav-center">
    <a href="http://127.0.0.1/pfe/EASYLEGAL/search">Rechercher</a>
    <a href="http://127.0.0.1/pfe/EASYLEGAL/forum">Forum</a>
    <a href="http://127.0.0.1/pfe/EASYLEGAL/messaging">Discuter</a>
  </div>

  <div class="nav-right">
    <a href="http://127.0.0.1/pfe/EASYLEGAL/Profile">
      <img src="http://127.0.0.1/pfe/EASYLEGAL/assets/Male User.png" alt="Profil">
    </a>
    <?php if (isset($_SESSION['id'])): ?>
      <a href="http://127.0.0.1/pfe/EASYLEGAL/auth/Logout.php">Déconnexion</a>
    <?php else: ?>
      <a href="http://127.0.0.1/pfe/EASYLEGAL/auth/login.php">Connexion</a>
    <?php endif; ?>
  </div>
</nav>
