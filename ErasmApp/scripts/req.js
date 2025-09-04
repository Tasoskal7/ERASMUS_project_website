document.addEventListener("DOMContentLoaded", 
function() 
{
  const form = document.querySelector("#requirements-form");
  const errorDiv = document.getElementById("form-error");
  const resultDiv = document.getElementById("result");

  if(!form) 
  {
    alert("Form not found!");
    return;
  }

  form.addEventListener("submit", 
  function (event) 
  {
    event.preventDefault();
    errorDiv.innerHTML = "";
    resultDiv.innerHTML = "";

    const year = document.getElementById("study-year").value;
    const percentage = parseFloat(document.getElementById("passed-percentage").value);
    const average = parseFloat(document.getElementById("average-grade").value);
    const english = document.querySelector('input[name="english-level"]:checked')?.value;

    let error = "";

    if(!year || parseInt(year) < 2) 
    {
      error +="<span style='color:red;font-weight:bold;'>Πρέπει να είστε τουλάχιστον στο 2ο έτος σπουδών.<br></span>";
    }
    if(isNaN(percentage) || percentage < 70) 
    {
      error +="<span style='color:red;font-weight:bold;'>Το ποσοστό επιτυχίας πρέπει να είναι τουλάχιστον 70%.<br></span>";
    }
    if(isNaN(average) || average < 6.5) 
    {
      error +="<span style='color:red;font-weight:bold;'>Ο μέσος όρος πρέπει να είναι τουλάχιστον 6.50.<br></span>";
    }
    const levels = ["B2", "C1", "C2"];
    if (!levels.includes(english)) 
    {
      error +="<span style='color:red;font-weight:bold;'>Το επίπεδο αγγλικών πρέπει να είναι τουλάχιστον B2.<br></span>";
    }

    if(error)
    {
      errorDiv.innerHTML = error;
      resultDiv.innerHTML = "";
    } 
    else 
    {
      errorDiv.innerHTML = "";
      resultDiv.innerHTML ="<span style='color:green;font-weight:bold;'>Συγχαρητήρια! Πληροίτε όλες τις απαιτήσεις για το Erasmus.</span>";
    }
  });
});
