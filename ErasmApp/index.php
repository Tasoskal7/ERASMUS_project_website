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
    <title>UOP ERASMUS GUIDE</title>
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
        <h1>Καλωσήρθατε στην Πύλη Erasmus+ για Φοιτητές!</h1>
        <p>
          Αγαπητοί φοιτητές,<br />
          Σας καλωσορίζουμε στην ψηφιακή πλατφόρμα που δημιουργήθηκε ειδικά για
          εσάς που ονειρεύεστε μια εκπαιδευτική εμπειρία πέρα από τα σύνορα της
          χώρας μας!<br />
          Το πρόγραμμα Erasmus+ αποτελεί μια μοναδική ευκαιρία να διευρύνετε
          τους ορίζοντές σας,<br />
          να αποκτήσετε πολύτιμες ακαδημαϊκές γνώσεις και να βιώσετε την
          καθημερινότητα μιας διαφορετικής κουλτούρας.<br />
          Κάντε το πρώτο βήμα προς μια αξέχαστη εμπειρία!<br />
          <b>Η ομάδα υποστήριξης Erasmus</b>
        </p>
        <a href="application.php" class="startbtn"><b>ΞΕΚΙΝΗΣΤΕ</b></a>
      </div>
      <div class="image-container">
        <img src="media/erasmusplus.jpg" class="erasmusimg" alt="Erasmus+ Program Image"/>
      </div>
    </section>
  </body>
</html>
