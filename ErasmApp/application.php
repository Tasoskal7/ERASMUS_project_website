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

$today = date('Y-m-d');
$period = $conn->query("SELECT * FROM application_period WHERE active=1 ORDER BY id DESC LIMIT 1")->fetch_assoc();
if (!$period || $today < $period['start_date'] || $today > $period['end_date']) 
{
  header("Location: closedappl.php");
  exit();
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT name, LName, AM FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($name, $lname, $am);
$stmt->fetch();
$stmt->close();


$universities = [];
$uni_query = $conn->query("SELECT id, uni_name FROM universities WHERE active=1");
while ($row = $uni_query->fetch_assoc()) 
{
  $universities[] = $row;
}

$errors = [];
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
  $passed_percent = isset($_POST['passed_percent']) ? floatval($_POST['passed_percent']) : null;
  $average = isset($_POST['average']) ? floatval($_POST['average']) : null;
  $english_level = $_POST['english_level'] ?? '';
  $other_langs = $_POST['other_langs'] ?? '';
  $uni1 = $_POST['uni1'] ?? '';
  $uni2 = $_POST['uni2'] ?? '';
  $uni3 = $_POST['uni3'] ?? '';
  $terms_accepted = isset($_POST['terms']) ? 1 : 0;

  if($passed_percent === null || $passed_percent < 0 || $passed_percent > 100) 
  {
    $errors[] = "Το ποσοστό περασμένων μαθημάτων πρέπει να είναι αριθμός από 0 έως 100.";
  }
  if($average === null || $average < 0 || $average > 10) 
  {
    $errors[] = "Ο μέσος όρος πρέπει να είναι αριθμός από 0 έως 10.";
  }
  if(empty($english_level)) 
  {
    $errors[] = "Επιλέξτε επίπεδο αγγλικών.";
  }
  if(empty($uni1)) 
  {
    $errors[] = "Επιλέξτε 1ο πανεπιστήμιο.";
  }
  if(empty($_FILES['grades_file']['name'])) 
  {
    $errors[] = "Ανεβάστε αναλυτική βαθμολογία.";
  }
  if(empty($_FILES['eng_cert']['name'])) 
  {
    $errors[] = "Ανεβάστε πτυχίο αγγλικής.";
  }
  if(!$terms_accepted) 
  {
    $errors[] = "Πρέπει να αποδεχτείτε τους όρους.";
  }

  $allowed = ['pdf'];
  $grades_ext = strtolower(pathinfo($_FILES['grades_file']['name'], PATHINFO_EXTENSION));
  $eng_ext = strtolower(pathinfo($_FILES['eng_cert']['name'], PATHINFO_EXTENSION));
  if(!in_array($grades_ext, $allowed)) 
  {
    $errors[] = "Η αναλυτική βαθμολογία πρέπει να είναι σε pdf.";
  }
  if(!in_array($eng_ext, $allowed)) 
  {
    $errors[] = "Το πτυχίο αγγλικής πρέπει να είναι σε pdf.";
  }

  $other_certs_names = [];
  if(!empty($_FILES['other_certs']['name'][0])) 
  {
    foreach ($_FILES['other_certs']['name'] as $idx => $fname) 
    {
      $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
      if(!in_array($ext, $allowed)) 
      {
        $errors[] = "Κάθε πτυχίο άλλης γλώσσας πρέπει να είναι pdf.";
      }
    }
  }

  if(empty($errors)) 
  {
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir);

    $grades_newname = uniqid("grades_") . "." . $grades_ext;
    $eng_newname = uniqid("eng_") . "." . $eng_ext;

    move_uploaded_file($_FILES['grades_file']['tmp_name'], $upload_dir . $grades_newname);
    move_uploaded_file($_FILES['eng_cert']['tmp_name'], $upload_dir . $eng_newname);

    $other_cert_paths = [];
    if(!empty($_FILES['other_certs']['name'][0])) 
    {
      foreach ($_FILES['other_certs']['name'] as $idx => $fname) 
      {
        $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
        $newname = uniqid("other_") . "." . $ext;
        move_uploaded_file($_FILES['other_certs']['tmp_name'][$idx], $upload_dir . $newname);
        $other_cert_paths[] = $newname;
      }
    }
    $other_cert_paths_str = implode(",", $other_cert_paths);

    $stmt = $conn->prepare("INSERT INTO applications 
      (username, name, lname, am, passed_percent, average, english_level, other_langs, uni1, uni2, uni3, grades_file, eng_cert, other_certs, terms_accepted, submitted_at) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssddssssssssi",
      $username, $name, $lname, $am, $passed_percent, $average,
      $english_level, $other_langs, $uni1, $uni2, $uni3,
      $grades_newname, $eng_newname, $other_cert_paths_str, $terms_accepted);
    
    if($stmt->execute()) 
    {
      header("Location: application.php?success=1");
      exit();
    } 
    else 
    {
      $errors[] = "Σφάλμα κατά την αποθήκευση της αίτησης.";
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
    <title>UOP ERASMUS GUIDE - Αίτηση</title>
    <link rel="stylesheet" href="styles/appl.css"/>
    <style>
      .error-message { color: #fff; background: #e74c3c; padding: 10px 20px; border-radius: 5px; margin-bottom: 15px; max-width: 400px; text-align: center; margin: 20px auto;}
      .success-message { color: #fff; background: #27ae60; padding: 10px 20px; border-radius: 5px; margin-bottom: 15px; max-width: 400px; text-align: center; margin: 20px auto;}
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
    </section>
      <div class="content">
        <h1>Αίτηση Συμμετοχής στο Erasmus</h1>
        <?php
        if(isset($_GET['success'])) 
        {
          echo "<div class='success-message'>Η αίτηση υποβλήθηκε επιτυχώς!</div>";
        }
        if(!empty($errors)) 
        {
          foreach($errors as $error) 
          {
            echo "<div class='error-message'>$error</div>";
          }
        }
        ?>

        <div class="form-container">
          <form method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label>Όνομα:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" readonly required>
          </div>

          <div class="form-group">
            <label>Επίθετο:</label>
            <input type="text" name="lname" value="<?php echo htmlspecialchars($lname); ?>" readonly required>
          </div>

          <div class="form-group">
            <label>Αριθμός Μητρώου:</label>
            <input type="text" name="am" value="<?php echo htmlspecialchars($am); ?>" readonly required>
          </div>

          <div class="form-group">
            <label for="passed_percent">Ποσοστό Περασμένων Μαθημάτων (%):</label>
            <input type="number" id="passed_percent" name="passed_percent" min="0" max="100" step="0.01" required> 
          </div>    

          <div class="form-group">
            <label for="average-grade">Μέσος Όρος Περασμένων Μαθημάτων:</label>
            <input type="number" name="average" step="0.01" min="0" max="10" required>
          </div>

          <div class="form-group">
            <label>Επίπεδο Αγγλικών:</label>
            <label><input type="radio" name="english_level" value="A1">A1</label>
            <label><input type="radio" name="english_level" value="A2">A2</label>
            <label><input type="radio" name="english_level" value="B1">B1</label>
            <label><input type="radio" name="english_level" value="B2">B2</label>
            <label><input type="radio" name="english_level" value="C1">C1</label>
            <label><input type="radio" name="english_level" value="C2">C2</label>
          </div>

          <div class="form-group">
            <label>Γνώση Επιπλέον Ξένων Γλωσσών:</label>
            <label><input type="radio" name="other_langs" value="ΝΑΙ">ΝΑΙ</label>
            <label><input type="radio" name="other_langs" value="ΟΧΙ">ΟΧΙ</label>
          </div>

          <div class="form-group">
            <label>Πανεπιστήμιο - 1η Επιλογή:</label>
            <select name="uni1" required>
              <option value="">-- Επιλέξτε Πανεπιστήμιο --</option>
              <?php 
              foreach($universities as $uni) 
              {
                echo "<option value=\"".htmlspecialchars($uni['id'])."\">".htmlspecialchars($uni['uni_name'])."</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Πανεπιστήμιο - 2η Επιλογή:</label>
            <select name="uni2">
              <option value="">-- Επιλέξτε Πανεπιστήμιο (Προαιρετικά) --</option>
              <?php 
              foreach($universities as $uni) 
              {
                echo "<option value=\"".htmlspecialchars($uni['id'])."\">".htmlspecialchars($uni['uni_name'])."</option>";
              } 
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Πανεπιστήμιο - 3η Επιλογή:</label>
            <select name="uni3">
              <option value="">-- Επιλέξτε Πανεπιστήμιο (Προαιρετικά) --</option>
              <?php 
              foreach($universities as $uni) 
              {
                echo "<option value=\"".htmlspecialchars($uni['id'])."\">".htmlspecialchars($uni['uni_name'])."</option>";
              } 
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Αναλυτική Βαθμολογία (PDF):</label>
            <input type="file" name="grades_file" accept=".pdf" required/>
          </div>

          <div class="form-group">
            <label>Πτυχίο Αγγλικής Γλώσσας (PDF):</label>
            <input type="file" name="eng_cert" accept=".pdf" required/>
          </div>

          <div class="form-group">
            <label>Πτυχία Άλλων Ξένων Γλωσσών (πολλαπλά PDF):</label>
            <input type="file" name="other_certs[]" accept=".pdf" multiple/>
          </div>

          <div class="form-group terms-group">
            <input type="checkbox" name="terms" value="1" required/>
            <label>Αποδέχομαι τους όρους και προϋποθέσεις συμμετοχής.</label>
          </div>  

          <div style="text-align:center;margin-top:20px;">
            <button type="submit" class="btn">ΥΠΟΒΟΛΗ ΑΙΤΗΣΗΣ</button>
          </div>
          </form>
        </div>
      </div>
  </body>
</html>
