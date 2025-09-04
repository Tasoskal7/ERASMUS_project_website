<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "erasmapp");
if($conn->connect_error) 
{
  die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$name = $lname = $am = $pnumber = $email = $username = $pw = $cpw = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
  $name = trim($_POST['name']);
  $lname = trim($_POST['LName']);
  $am = trim($_POST['AM']);
  $pnumber = trim($_POST['pNumber']);
  $email = trim($_POST['email']);
  $username = trim($_POST['username']);
  $pw = $_POST['pw'];
  $cpw = $_POST['cpw'];

  if(preg_match('/\d/', $name)) 
  {
    $errors[] = "Το όνομα δεν πρέπει να περιέχει αριθμούς.";
  }
  if(empty($name)) 
  {
    $errors[] = "Το όνομα είναι υποχρεωτικό.";
  }

  if(preg_match('/\d/', $lname)) 
  {
    $errors[] = "Το επίθετο δεν πρέπει να περιέχει αριθμούς.";
  }
  if(empty($lname)) 
  {
    $errors[] = "Το επίθετο είναι υποχρεωτικό.";
  }

  if(!preg_match('/^2022\d{9}$/', $am)) 
  {
    $errors[] = "Ο Αριθμός Μητρώου πρέπει να έχει 13 ψηφία και να ξεκινά με 2022.";
  }

  if(!preg_match('/^\d{10}$/', $pnumber)) 
  {
    $errors[] = "Το τηλέφωνο πρέπει να αποτελείται από 10 ψηφία.";
  }

  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
  {
    $errors[] = "Το email δεν είναι έγκυρο.";
  }

  $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();
  if($stmt->num_rows > 0){
    $errors[] = "Το username χρησιμοποιείται ήδη.";
  }
  $stmt->close();

  if(strlen($pw) < 5){
    $errors[] = "Ο κωδικός πρέπει να έχει τουλάχιστον 5 χαρακτήρες.";
  }
  if(!preg_match('/[\W_]/', $pw)){
    $errors[] = "Ο κωδικός πρέπει να περιέχει τουλάχιστον ένα σύμβολο.";
  }

  if($pw !== $cpw) 
  {
    $errors[] = "Η επιβεβαίωση κωδικού δεν ταιριάζει.";
  }

  if(empty($errors)) 
  {
    $hashed_pw = password_hash($pw, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, LName, AM, pNumber, email, username, pw) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $lname, $am, $pnumber, $email, $username, $hashed_pw);
    if($stmt->execute()) 
    {
      header("Location: login.php");
      exit();
    } 
    else 
    {
      $errors[] = "Σφάλμα κατά την εγγραφή. Προσπαθήστε ξανά.";
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="el">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>UOP ERASMUS GUIDE - Εγγραφή</title>
    <link rel="stylesheet" href="styles/signup.css" />
    <style>
    .error-message {color: #fff; background: #e74c3c; padding: 10px 20px; border-radius: 5px; max-width: 350px; margin: 40px auto 15px auto; text-align: center; display: block;}
    </style>
  </head>

  <body>
    <section class="header">
      <nav>
        <a href="index.html"><img src="media/erasmuslogo.png" alt="Erasmus Logo"/></a>
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
        <h1>Δημιουργία Λογαριασμού</h1>
        <?php
        if(!empty($errors))
        {
          foreach($errors as $error) 
          {
            echo "<div class='error-message'>$error</div>";
          }
        }
        ?>

        <div class="form-container">
          <form action="signup.php" method="post">
          <div class="form-group">
            <label for="reg-firstname">Όνομα:</label>
            <input type="text" id="reg-firstname" name="name" required />
          </div>

          <div class="form-group">
            <label for="reg-lastname">Επίθετο:</label>
            <input type="text" id="reg-lastname" name="LName" required />
          </div>

          <div class="form-group">
            <label for="reg-student-id">Αριθμός Μητρώου:</label>
            <input type="text" id="reg-student-id" name="AM" required />
          </div>

          <div class="form-group">
            <label for="reg-email">Email:</label>
            <input type="email" id="reg-email" name="email" required />
          </div>

          <div class="form-group">
            <label for="reg-phone">Τηλέφωνο:</label>
            <input type="tel" id="reg-phone" name="pNumber" required />
          </div>

          <div class="form-group">
            <label for="reg-username">Όνομα Χρήστη:</label>
            <input type="text" id="reg-username" name="username" required />
          </div>

          <div class="form-group">
            <label for="reg-password">Κωδικός Πρόσβασης:</label>
            <input type="password" id="reg-password" name="pw" required />
          </div>

          <div class="form-group">
            <label for="reg-confirm-password">Επιβεβαίωση Κωδικού:</label>
            <input type="password" id="reg-confirm-password" name="cpw" required />
          </div>

          <button type="submit" class="btn">ΕΓΓΡΑΦΗ</button>
          </form>
          Έχετε ήδη λογαριασμό; <a href="login.php">Συνδεθείτε εδώ</a>
        </div>
      </div>
    </section>
  </body>
</html>