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
if ($conn->connect_error) 
{ 
    die("Connection failed: " . $conn->connect_error); 
}

$username = $_SESSION['username'];
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $name = trim($_POST['name']);
    $lname = trim($_POST['LName']);
    $am = trim($_POST['AM']);
    $pnumber = trim($_POST['pNumber']);
    $email = trim($_POST['email']);
    $pw = $_POST['pw'];
    $cpw = $_POST['cpw'];

    if(preg_match('/\d/', $name)) 
    { 
        $errors[] = "Το όνομα δεν πρέπει να περιέχει αριθμούς."; 
    }
    if(empty($name)) { 
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
    if(!empty($pw)) 
    {
        if(strlen($pw) < 5) 
        { 
            $errors[] = "Ο κωδικός πρέπει να έχει τουλάχιστον 5 χαρακτήρες."; 
        }
        if(!preg_match('/[\W_]/', $pw))
        { 
            $errors[] = "Ο κωδικός πρέπει να περιέχει τουλάχιστον ένα σύμβολο."; 
        }
        if($pw !== $cpw) 
        { 
            $errors[] = "Η επιβεβαίωση κωδικού δεν ταιριάζει."; 
        }
    }

    if(empty($errors)) 
    {
        if (!empty($pw)) 
        {
            $hashed_pw = password_hash($pw, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name=?, LName=?, AM=?, pNumber=?, email=?, pw=? WHERE username=?");
            $stmt->bind_param("sssssss", $name, $lname, $am, $pnumber, $email, $hashed_pw, $username);
        } 
        else 
        {
            $stmt = $conn->prepare("UPDATE users SET name=?, LName=?, AM=?, pNumber=?, email=? WHERE username=?");
            $stmt->bind_param("ssssss", $name, $lname, $am, $pnumber, $email, $username);
        }
        if($stmt->execute()) 
        {
            $success = "Το προφίλ ενημερώθηκε επιτυχώς!";
        }
        else 
        {
            $errors[] = "Σφάλμα κατά την ενημέρωση. Προσπαθήστε ξανά.";
        }
        $stmt->close();
    }
}
$stmt = $conn->prepare("SELECT name, LName, AM, pNumber, email FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($name, $lname, $am, $pnumber, $email);
$stmt->fetch();
$stmt->close();
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
        <a href="index.php"><img src="media/erasmuslogo.png" alt="Erasmus Logo"/></a>
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
        <div class="form-container">
            <h2>Το Προφίλ μου</h2>

            <?php
                foreach ($errors as $error) 
                { 
                    echo "<div class='error-message'>$error</div>"; 
                }
                if(!empty($success)) 
                { 
                    echo "<div class='success-message'>$success</div>"; 
                }
            ?>

            <form action="profile.php" method="post">
                <div class="form-group">
                    <label>Όνομα:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="form-group">
                    <label>Επίθετο:</label>
                    <input type="text" name="LName" value="<?php echo htmlspecialchars($lname); ?>" required>
                </div>
                <div class="form-group">
                    <label>Αριθμός Μητρώου:</label>
                    <input type="text" name="AM" value="<?php echo htmlspecialchars($am); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label>Τηλέφωνο:</label>
                    <input type="text" name="pNumber" value="<?php echo htmlspecialchars($pnumber); ?>" required>
                </div>
                <div class="form-group">
                    <label>Όνομα Χρήστη (δεν αλλάζει):</label>
                    <input type="text" value="<?php echo htmlspecialchars($username); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Νέος Κωδικός (προαιρετικά):</label>
                    <input type="password" name="pw">
                </div>
                <div class="form-group">
                    <label>Επιβεβαίωση Νέου Κωδικού:</label>
                    <input type="password" name="cpw">
                </div>
                <button type="submit" class="btn">Αποθήκευση Αλλαγών</button>
            </form>
            <a href="application.php" class="btn">Μετάβαση στη Φόρμα Αίτησης Erasmus</a>
            <a href="admin.php" class="btn">Πυλή administrator</a>
        </div>
    </div>
    </body>
</html>
