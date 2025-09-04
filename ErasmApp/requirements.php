<?php
session_start();
if (
    !isset($_SESSION['username']) ||
    (
        $_SESSION['role'] !== 'registered' &&
        $_SESSION['role'] !== 'administrator'
    )
) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="el">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>UOP ERASMUS GUIDE - Απαιτήσεις</title>
    <link rel="stylesheet" href="styles/req.css" />
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
        <h1>Απαιτήσεις Συμμετοχής στο Erasmus</h1>
        <div>
          <img src="media/pen.jpg" class="penimg" />
        </div>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Απαίτηση</th>
                <th>Ελάχιστο Όριο</th>
                <th>Περιγραφή</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Έτος Σπουδών</td>
                <td>≥ 2ο έτος</td>
                <td>
                  Ο φοιτητής πρέπει να έχει ολοκληρώσει τουλάχιστον το 1ο έτος
                  σπουδών
                </td>
              </tr>
              <tr>
                <td>Ποσοστό Επιτυχίας</td>
                <td>≥ 70%</td>
                <td>Ποσοστό περασμένων μαθημάτων έως το προηγούμενο έτος</td>
              </tr>
              <tr>
                <td>Μέσος Όρος</td>
                <td>≥ 6.50</td>
                <td>Μέσος όρος βαθμολογίας περασμένων μαθημάτων</td>
              </tr>
              <tr>
                <td>Αγγλικά</td>
                <td>≥ B2</td>
                <td>Πιστοποιημένη γνώση Αγγλικής γλώσσας</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="text-align: center; margin: 30px 0">
          <a href="media/odigos-kinitikotitas-erasmus.pdf" class="btn" download>
            Οδηγός Κινητικότητας Erasmus (PDF)
          </a>
        </div>
        <div class="form-container">
          <h2>Έλεγχος Απαιτήσεων</h2>
          <form id="requirements-form">
            <div class="form-group">
              <label for="study-year">Τρέχον Έτος Σπουδών:</label>
              <select id="study-year" required>
                <option value="">-- Επιλέξτε --</option>
                <option value="1">1ο Έτος</option>
                <option value="2">2ο Έτος</option>
                <option value="3">3ο Έτος</option>
                <option value="4">4ο Έτος</option>
                <option value="5">>4ο Έτος</option>
              </select>
            </div>
            <div class="form-group">
              <label for="passed-percentage">Ποσοστό Περασμένων Μαθημάτων (%):</label>
              <input type="number" id="passed-percentage" min="0" max="100" required/>
            </div>
            <div class="form-group">
              <label for="average-grade">Μέσος Όρος Βαθμολογίας:</label>
              <input type="number" id="average-grade" step="0.01" min="0" max="10" required/>
            </div>
            <div class="form-group">
              <label>Επίπεδο Αγγλικών:</label>
              <label><input type="radio" name="english-level" value="A1" />A1</label>
              <label><input type="radio" name="english-level" value="A2" />A2</label>
              <label><input type="radio" name="english-level" value="B1" />B1</label>
              <label><input type="radio" name="english-level" value="B2" />B2</label>
              <label><input type="radio" name="english-level" value="C1" />C1</label>
              <label><input type="radio" name="english-level" value="C2" />C2</label>
            </div>
            <button type="submit" class="btn">ΕΛΕΓΧΟΣ ΑΠΑΙΤΗΣΕΩΝ</button>
          </form>
          <div id="form-error" class="error-message"></div>
          <div id="result"></div>
        </div>
      </div>
    </section>
    <script src="scripts/req.js"></script>
  </body>
</html>