<?php
session_start();
$conn = new mysqli("localhost", "root", "", "erasmapp");
if ($conn->connect_error) 
{
  die("Connection failed: " . $conn->connect_error);
}

if($_SERVER["REQUEST_METHOD"] == "POST") 
{
  $username = $_POST['username'];
  $password = $_POST['password'];

  $sql = "SELECT * FROM users WHERE username=? LIMIT 1";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();
  if($user = $result->fetch_assoc()) 
  {
    if (password_verify($password, $user['pw'])) 
    {
      $_SESSION['username'] = $user['username'];
      $_SESSION['role'] = $user['role'];
      header("Location: index.php");
      exit();
    } 
    else 
    {
      $error = "Λάθος κωδικός!";
    }
  } 
  else 
  {
    $error = "Ο χρήστης δεν βρέθηκε!";
  }
}
?>

<!DOCTYPE html>
<html lang="el">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>UOP ERASMUS GUIDE - Σύνδεση</title>
    <link rel="stylesheet" href="styles/login.css" />
    <style>
      .error-message {align-items: center; color: #fff; background: #e74c3c; padding: 10px 20px; border-radius: 5px; margin-bottom: 15px; max-width: 400px;}
    </style>
  </head>

  <body>
    <section class="header">
      <nav>
        <a href="index.html"><img src="media/erasmuslogo.png" width="250px"/></a>
        <div class="navlinks">
          <ul>
            <li><a href="index.html">ΑΡΧΙΚΗ</a></li>
            <li><a href="more.html">ΠΕΡΙΣΣΟΤΕΡΑ</a></li>
            <li><a href="requirements.html">ΑΠΑΙΤΗΣΕΙΣ</a></li>
            <li><a href="application.php">ΦΟΡΜΑ</a></li>
            <li><a href="login.php">ΣΥΝΔΕΣΗ</a></li>
            <li><a href="signup.php">ΕΓΓΡΑΦΗ</a></li>
          </ul>
        </div>
      </nav>

      <div class="content">
        <h1>Σύνδεση στην Πύλη Erasmus</h1>
        <div class="login-box">
          <div class="login-container">
            <h2 class="login-title">Είσοδος Χρήστη</h2>
            <form action="#" method="post">
              <div class="form-group">
                <label for="username">Όνομα Χρήστη</label>
                <input type="text" id="username" name="username" required />
              </div>

              <div class="form-group">
                <label for="password">Κωδικός Πρόσβασης</label>
                <input type="password" id="password" name="password" required />
              </div>
              <?php if (!empty($error)) { ?>
              <div class="error-message"><?php echo $error; ?></div>
              <?php } ?>

              <button type="submit" class="submit-btn"><b>ΣΥΝΔΕΣΗ</b></button>
              <p class="register-link">
                Δεν έχετε λογαριασμό; <a href="signup.php">Εγγραφείτε εδώ</a>
              </p>
            </form>
          </div>
        </div>
      </div>
    </section>
  </body>
</html>
