<?php
session_start();
if
(
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
$conn = new mysqli("localhost", "root", "", "erasmapp");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$today = date('Y-m-d');
$period = $conn->query("SELECT * FROM application_period WHERE active=1 ORDER BY id DESC LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="el">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Αποτελέσματα Erasmus</title>
    <link rel="stylesheet" href="styles/result.css" />
  </head>
  <body>
    <section class="header">
        <nav>
        <a href="index.html"><img src="media/erasmuslogo.png" width="250px"/></a>
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
      <div class="container">
        <h1>Αποτελέσματα Erasmus</h1>
        <?php
        if(!$period) 
        {
          echo '<p class="msg">Δεν έχει οριστεί περίοδος αιτήσεων.</p>';
        } 
        elseif($today >= $period['start_date'] && $today <= $period['end_date']) 
        {
          echo '<p class="msg">Η περίοδος αιτήσεων είναι ακόμα ανοιχτή. Τα αποτελέσματα θα ανακοινωθούν μετά τη λήξη της.</p>';
        } 
        elseif($today > $period['end_date'] && empty($period['results_announced'])) 
        {
          echo '<p class="msg">Τα αποτελέσματα δεν είναι ακόμα διαθέσιμα.</p>';
        }
        elseif($today > $period['end_date'] && !empty($period['results_announced'])) 
        {
          $sql = "SELECT a.name, a.lname, u.uni_name, a.accepted
                  FROM applications a
                  LEFT JOIN universities u ON a.uni1 = u.id
                  ORDER BY a.lname, a.name";
          $result = $conn->query($sql);

          if($result && $result->num_rows > 0) 
          {
            echo '<table>';
            echo '<tr>
                  <th>Όνομα</th>
                  <th>Επίθετο</th>
                  <th>Πανεπιστήμιο</th>
                  <th>Κατάσταση</th>
                  </tr>';
              while($row = $result->fetch_assoc()) 
              {
                $status = $row['accepted'] ? '<span class="approved">Εγκρίθηκε</span>' : '<span class="not-approved">Δεν εγκρίθηκε</span>';
                echo '<tr>
                      <td>'.htmlspecialchars($row['name']).'</td>
                      <td>'.htmlspecialchars($row['lname']).'</td>
                      <td>'.htmlspecialchars($row['uni_name']).'</td>
                      <td>'.$status.'</td>
                      </tr>';
              }
            echo '</table>';
          } 
          else 
          {
            echo '<p class="msg">Δεν υπάρχουν αιτήσεις.</p>';
          }
        }
        ?>
      </div>
    </section>
  </body>
</html>
