<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrator') 
{
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "erasmapp");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$success = "";
$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['start_date']) && isset($_POST['end_date'])) 
{
  $start = $_POST['start_date'];
  $end = $_POST['end_date'];
  if (!$start || !$end || $start > $end) 
  {
    $error = "Εισάγετε σωστές ημερομηνίες (η έναρξη πρέπει να προηγείται της λήξης).";
  } 
  else 
  {
    $conn->query("UPDATE application_period SET active=0");
    $stmt = $conn->prepare("INSERT INTO application_period (start_date, end_date, active) VALUES (?, ?, 1)");
    $stmt->bind_param("ss", $start, $end);
    if ($stmt->execute()) 
    {
      $success = "Η περίοδος αιτήσεων ορίστηκε επιτυχώς!";
    } 
    else 
    {
      $error = "Σφάλμα κατά την αποθήκευση.";
    }
    $stmt->close();
  }
}

$period = $conn->query("SELECT * FROM application_period WHERE active=1 ORDER BY id DESC LIMIT 1")->fetch_assoc();

$today = date('Y-m-d');
$period_ended = false;
if ($period && $today > $period['end_date']) 
{
  $period_ended = true;
}

if (isset($_POST['announce_results']) && $period_ended) 
{
  $conn->query("UPDATE application_period SET results_announced=1 WHERE id = " . intval($period['id']));
  $success = "Τα αποτελέσματα ανακοινώθηκαν και είναι πλέον διαθέσιμα σε όλους τους χρήστες.";
}

$results_announced = false;
if ($period) 
{
  $row = $conn->query("SELECT results_announced FROM application_period WHERE id = " . intval($period['id']))->fetch_assoc();
  $results_announced = !empty($row['results_announced']);
}

$universities = [];
$uni_query = $conn->query("SELECT id, uni_name FROM universities WHERE active=1");
while ($row = $uni_query->fetch_assoc())
{
  $universities[] = $row;
}

$order = $_GET['order'] ?? 'desc';
$min_percent = $_GET['min_percent'] ?? '';
$uni_filter = $_GET['uni_filter'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
  $all_ids = [];
  $res = $conn->query("SELECT id FROM applications");
  while ($row = $res->fetch_assoc()) 
  {
      $all_ids[] = $row['id'];
  }
  $accepted_ids = isset($_POST['accepted_ids']) ? array_map('intval', $_POST['accepted_ids']) : [];

  foreach ($all_ids as $id) 
  {
    $accepted = in_array($id, $accepted_ids) ? 1 : 0;
    $conn->query("UPDATE applications SET accepted=$accepted WHERE id=$id");
  }

  header("Location: admin.php?success=2");
  exit();
}
?>


<!DOCTYPE html>
<head>
  <script src="scripts/universities.js"></script>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Διαχείριση Περιόδου Αιτήσεων</title>
  <link rel="stylesheet" href="styles/admin.css" />


</head>

<body>
  <section class="header">
    <nav>
      <a href="index.html"><img src="media/erasmuslogo.png" alt="Erasmus Logo"/></a>
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
    <h1>Καθορισμός Περιόδου Αιτήσεων</h1>
    <?php
      if ($success) echo "<div style='color:green;'>$success</div>";
      if ($error) echo "<div style='color:red;'>$error</div>";
    ?>
    <div class="form-container">
      <p>Ορίστε την περίοδο αιτήσεων για το πρόγραμμα Erasmus:</p>
      <div class="form-group">
      <form method="post">
        <label>Έναρξη: <input type="date" name="start_date" value="<?php echo $period['start_date'] ?? ''; ?>" required></label><br>
        <label>Λήξη: <input type="date" name="end_date" value="<?php echo $period['end_date'] ?? ''; ?>" required></label><br>
      </div>
      <button type="submit" class="btn">Αποθήκευση</button>
      </form>
      <?php
      if ($period)
      {
        echo "<p>Τρέχουσα περίοδος: <b>{$period['start_date']}</b> έως <b>{$period['end_date']}</b></p>";
      }
      ?>
    </div>
    <?php
      $order = $_GET['order'] ?? 'desc';
      $min_percent = $_GET['min_percent'] ?? '';
      $uni_filter = $_GET['uni_filter'] ?? '';
    ?>
    <div class="form-container">
      <p>Λίστα Αιτήσεων</p>
      <div class="form-group">
      <form method="get" style="margin-bottom:20px;">
        <label>Ταξινόμηση κατά μέσο όρο:
          <select name="order">
            <option value="desc" <?= $order=='desc'?'selected':'' ?>>Φθίνουσα</option>
            <option value="asc" <?= $order=='asc'?'selected':'' ?>>Αύξουσα</option>
          </select>
        </label>
        <label>Ελάχιστο ποσοστό επιτυχίας:
          <input type="number" name="min_percent" min="0" max="100" step="0.01" value="<?= htmlspecialchars($min_percent) ?>">
        </label>
        <label>Πανεπιστήμιο:
          <select name="uni_filter">
            <option value="">-- Όλα --</option>
            <?php foreach($universities as $uni): ?>
            <option value="<?= $uni['id'] ?>" <?= $uni_filter==$uni['id']?'selected':'' ?>>
            <?= htmlspecialchars($uni['uni_name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>
      <button type="submit" class="btn">Φιλτράρισμα</button>
      </form>
    </div>
    <?php
      $sql = "SELECT a.*, 
        u1.uni_name as uni1_name, 
        u2.uni_name as uni2_name, 
        u3.uni_name as uni3_name 
        FROM applications a
        LEFT JOIN universities u1 ON a.uni1 = u1.id
        LEFT JOIN universities u2 ON a.uni2 = u2.id
        LEFT JOIN universities u3 ON a.uni3 = u3.id
        WHERE 1";
      if ($min_percent !== '' && is_numeric($min_percent)) 
      {
        $sql .= " AND a.passed_percent >= " . floatval($min_percent);
      }
      if ($uni_filter !== '' && is_numeric($uni_filter)) 
      {
        $sql .= " AND (a.uni1 = $uni_filter OR a.uni2 = $uni_filter OR a.uni3 = $uni_filter)";
      }
      $order_sql = ($order == 'asc') ? 'ASC' : 'DESC';
      $sql .= " ORDER BY a.average $order_sql, a.id DESC";

      $result = $conn->query($sql);

      echo '<form method="post">';
      echo '<div class="table-container">';
      echo '<table>';
      echo '<tr>
      <th>Όνομα</th>
      <th>Επίθετο</th>
      <th>Αριθμός Μητρώου</th>
      <th>Μέσος Όρος</th>
      <th>Ποσοστό Επιτυχίας</th>
      <th>Επίπεδο Αγγλικών</th>
      <th>Γν. Άλλων Γλωσσών</th>
      <th>1η Επιλογή</th>
      <th>2η Επιλογή</th>
      <th>3η Επιλογή</th>
      <th>Αρχεία</th>
      <th>Δεκτή</th>
      </tr>';

      while($row = $result->fetch_assoc()) 
      {
        echo '<tr>';
        echo '<td>'.htmlspecialchars($row['name']).'</td>';
        echo '<td>'.htmlspecialchars($row['lname']).'</td>';
        echo '<td>'.htmlspecialchars($row['am']).'</td>';
        echo '<td>'.htmlspecialchars($row['average']).'</td>';
        echo '<td>'.htmlspecialchars($row['passed_percent']).'%</td>';
        echo '<td>'.htmlspecialchars($row['english_level']).'</td>';
        echo '<td>'.htmlspecialchars($row['other_langs']).'</td>';
        echo '<td>'.htmlspecialchars($row['uni1_name']).'</td>';
        echo '<td>'.htmlspecialchars($row['uni2_name']).'</td>';
        echo '<td>'.htmlspecialchars($row['uni3_name']).'</td>';
        echo '<td>';
        if ($row['grades_file']) echo '<a href="uploads/'.$row['grades_file'].'" target="_blank">Βαθμολογία</a><br>';
        if ($row['eng_cert']) echo '<a href="uploads/'.$row['eng_cert'].'" target="_blank">Αγγλικά</a><br>';
        if ($row['other_certs']) 
        {
            $files = explode(',', $row['other_certs']);
            foreach($files as $f) 
            {
                if ($f) echo '<a href="uploads/'.$f.'" target="_blank">Άλλη Γλώσσα</a><br>';
            }
        }
        echo '</td>';
        $checked = $row['accepted'] ? 'checked' : '';
        echo '<td><input type="checkbox" name="accepted_ids[]" value="'.$row['id'].'" '.$checked.'></td>';
        echo '</tr>';
      }
      echo '</table>';
      echo '</div>'; 
      echo '<div class="form-container">';
      echo '<button type="submit" class="btn" style="margin-top:15px;">Αποθήκευση</button>';
      echo '</div>';
      echo '</form>';
    ?>

    <?php if ($period_ended && !$results_announced): ?>
      <form method="post" style="margin-top: 20px;">
        <button type="submit" name="announce_results" class="btn">Ανακοίνωση Αποτελεσμάτων</button>
      </form>
      <?php elseif ($results_announced): ?>
      <p style="color: lightgreen; font-weight: bold; margin-top: 20px;">
        Τα αποτελέσματα έχουν ανακοινωθεί και είναι διαθέσιμα σε όλους τους χρήστες.
      </p>
    <?php endif; ?>

    <div class="form-container">
    <h2>Διαχείριση Συνεργαζόμενων Πανεπιστημίων</h2>
    <div class="form-group">
    <form id="add-uni-form" style="margin-bottom:20px;">
      <input type="text" id="uni_name" placeholder="Όνομα πανεπιστημίου" required><br>
      <input type="text" id="country" placeholder="Χώρα" required>
      <input type="text" id="city" placeholder="Πόλη" required>
      <select id="active" required>
        <option value="1">Ενεργό</option>
        <option value="0">Ανενεργό</option>
      </select>
      <button type="submit" class="btn">Προσθήκη</button>
    </form>
    </div>
    </div>

    <div class="table-container">
    <div id="universities-app">
      <table id="universities-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Όνομα</th>
            <th>Χώρα</th>
            <th>Πόλη</th>
            <th>Ενεργό</th>
            <th>Ενέργειες</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    </div>

  </div>
</body>
</html>
