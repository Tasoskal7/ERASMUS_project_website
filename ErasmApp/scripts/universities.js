const apiUrl = "universities_api.php";

function fetchUniversities() 
{
  fetch(apiUrl)
    .then((res) => res.json())
    .then((data) => {
      const tbody = document.querySelector("#universities-table tbody");
      tbody.innerHTML = "";
      data.forEach((uni) => 
      {
        tbody.innerHTML += `
          <tr data-id="${uni.id}">
          <td>${uni.id}</td>
          <td><input type="text" value="${uni.uni_name}" class="edit-name"></td>
          <td><input type="text" value="${uni.country}" class="edit-country"></td>
          <td><input type="text" value="${uni.city}" class="edit-city"></td>
          <td>
            <select class="edit-active">
              <option value="1" ${uni.active == 1 ? "selected" : ""}>Ενεργό</option>
              <option value="0" ${uni.active == 0 ? "selected" : ""}>Ανενεργό</option>
            </select>
          </td>
          <td>
            <button type="button" onclick="updateUni(${uni.id}, this)">Αποθήκευση</button>
            <button type="button" onclick="deleteUni(${uni.id})" style="color:red;">Διαγραφή</button>
          </td> 
        </tr>
        `;
      });
    });
}

function addUni(e) 
{
  e.preventDefault();
  const name = document.getElementById("uni_name").value.trim();
  const country = document.getElementById("country").value.trim();
  const city = document.getElementById("city").value.trim();
  const active = parseInt(document.getElementById("active").value, 10);
  if(!name || !country || !city) return;
  fetch(apiUrl, 
    {method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      uni_name: name,
      country: country,
      city: city,
      active: active,
    }),
  })
    .then((res) => res.json())
    .then(() => {
      document.getElementById("add-uni-form").reset();
      fetchUniversities();
    });
}

function updateUni(id, btn) 
{
  const tr = btn.closest("tr");
  const name = tr.querySelector(".edit-name").value.trim();
  const country = tr.querySelector(".edit-country").value.trim();
  const city = tr.querySelector(".edit-city").value.trim();
  const active = parseInt(tr.querySelector(".edit-active").value, 10);
  if (!name || !country || !city) return;
  fetch(apiUrl + "?id=" + id, {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      uni_name: name,
      country: country,
      city: city,
      active: active,
    }),
  })
  .then((res) => res.json())
  .then(() => fetchUniversities());
}

function deleteUni(id) 
{
  if (!confirm("Σίγουρα διαγραφή;")) return;
  fetch(apiUrl + "?id=" + id, { method: "DELETE" })
    .then((res) => res.json())
    .then(() => fetchUniversities());
}

document.addEventListener("DOMContentLoaded", 
  function () 
  {
  document.getElementById("add-uni-form").addEventListener("submit", addUni);
  fetchUniversities();
});
