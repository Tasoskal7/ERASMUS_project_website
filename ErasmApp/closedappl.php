<?php
session_start();
if(
  !isset($_SESSION['username']) ||
  (
    $_SESSION['role'] !== 'registered' &&
    $_SESSION['role'] !== 'administrator'
  )
) 
{
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="el">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles/index.css" />
  </head>

  <body>
    <section class="header">
      <nav>
        <a href="index.php"><img src="media/erasmuslogo.png" width="250px"/></a>
        <div class="navlinks">
          <ul>
            <li><a href="index.php">ΑΡΧΙΚΗ</a></li>
            <li><a href="more.php">ΠΕΡΙΣΣΟΤΕΡΑ</a></li>
            <li><a href="requirements.php">ΑΠΑΙΤΗΣΕΙΣ</a></li>
            <li><a href="application.php">ΦΟΡΜΑ</a></li>
            <li><a href="profile.php">ΠΡΟΦΙΛ</a></li>
            <li><a href="logout.php">ΑΠΟΣΥΝΔΕΣΗ</a></li>
          </ul>
        </div>
      </nav>

      <div class="content">
        <p>Η περίοδος αιτήσεων δεν είναι ενεργή αυτή τη στιγμή.<br>
            Επικοινωνήστε με τη γραμματεία.
        </p>
      </div>
    </section>
  </body>
</html>