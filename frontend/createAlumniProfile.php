<?php  
session_start(); 

include '../connection/dbConnect.php';
include '../backend/login_process.php';
include '../backend/createAlumniProfile_process.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Alumni Profile</title>
  <link rel="stylesheet" href="style/navigation.css">

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f9;
    }
    .form-container {
      max-width: 800px;
      margin: 80px auto;
      padding: 20px;
      background: #ffffff;
      border: 1px solid #ddd;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .form-container img {
      border-radius: 50%;
      margin-bottom: 20px;
    }
    .form-container table {
      width: 100%;
    }
    .form-container input[type="text"],
    .form-container input[type="file"],
    .form-container textarea {
      width: calc(100% - 10px);
      padding: 5px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .form-container button {
      background-color: #007bff;
      color: #fff;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .form-container button:hover {
      background-color: #0056b3;
    }
    #mapCanvas {
      margin: 20px auto;
      border: 1px solid #ccc;
      border-radius: 5px;
      width: 100%;
      height: 350px;
    }
    .form-container .map-container {
      margin-top: 20px;
      text-align: center;
    }
    /* Autocomplete dropdown */
    #suggestions {
      position: absolute;
      background: white;
      border: 1px solid #ccc;
      max-height: 150px;
      overflow-y: auto;
      width: calc(100% - 22px);
      display: none;
      z-index: 1000;
    }
    #suggestions div {
      padding: 8px;
      cursor: pointer;
    }
    #suggestions div:hover {
      background: #f0f0f0;
    }
  </style>
</head>
<body>
<?php include 'nav.php';?>

<div class="form-container">
  <p align="center" style="font-size: 24px; font-weight: bold;">Edit Profile</p>
  <form action="../backend/saveProfile_process.php" method="POST" enctype="multipart/form-data">
    <input name="latitude" id="latitude" hidden>
    <input name="longitude" id="longitude" hidden>
    <input name="address" id="fullAddress" hidden>

    <table>
      <tr>
        <td>
          <img id="preview" src="<?php echo $profile_picture; ?>" alt="Profile Picture" width="150" height="150">
        </td>
        <td>
          <p>Name: <?php echo $_SESSION['fullName']; ?></p>
          <p>Course: <?php echo $_SESSION['course']; ?></p>
          <p>Batch: <?php echo $_SESSION['batch']; ?></p>
          <p>Current Job: <input type="text" name="current_job" placeholder="-Optional-" value="<?php echo htmlspecialchars($current_job); ?>"></p>
          <p>
            Location: 
            <input id="locationInput" type="text" name="location" placeholder="Search for location..." autocomplete="off" value="<?php echo htmlspecialchars($location); ?>">
            <div id="suggestions"></div>
          </p>
          <p><span id="address">Fetching location...</span></p>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <input type="file" name="profilePicture" accept="image/*" onchange="previewImage(event)">
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <p>Description:</p>
          <textarea name="description" rows="4" cols="50" placeholder="-Optional-"><?php echo htmlspecialchars($description); ?></textarea>
        </td>
      </tr>
      <tr>
        <td colspan="2" align="right">
          <button type="submit" name="submit">Save</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<div class="map-container">
  <div id="mapCanvas"></div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  let map = L.map('mapCanvas').setView([14.5995, 120.9842], 13); // Default Manila
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  let marker = L.marker([14.5995, 120.9842], { draggable: true }).addTo(map);

  marker.on('dragend', function(e) {
    let pos = marker.getLatLng();
    document.getElementById('latitude').value = pos.lat;
    document.getElementById('longitude').value = pos.lng;
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${pos.lat}&lon=${pos.lng}&format=json`)
      .then(res => res.json())
      .then(data => {
        document.getElementById('address').innerText = data.display_name || "Address not found";
        document.getElementById('fullAddress').value = data.display_name;
      });
  });

  // Location autocomplete
  const locationInput = document.getElementById("locationInput");
  const suggestions = document.getElementById("suggestions");

  locationInput.addEventListener("input", function() {
    let query = this.value;
    if (query.length < 2) {
      suggestions.style.display = "none";
      return;
    }
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
      .then(res => res.json())
      .then(data => {
        suggestions.innerHTML = "";
        if (data.length > 0) {
          data.forEach(place => {
            let div = document.createElement("div");
            div.textContent = place.display_name;
            div.addEventListener("click", function() {
              locationInput.value = place.display_name;
              document.getElementById('address').innerText = place.display_name;
              document.getElementById('latitude').value = place.lat;
              document.getElementById('longitude').value = place.lon;
              document.getElementById('fullAddress').value = place.display_name;

              map.setView([place.lat, place.lon], 15);
              marker.setLatLng([place.lat, place.lon]);

              suggestions.style.display = "none";
            });
            suggestions.appendChild(div);
          });
          suggestions.style.display = "block";
        } else {
          suggestions.style.display = "none";
        }
      });
  });

  function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
      document.getElementById('preview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
  }

  // Try to get user location
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(pos => {
      let lat = pos.coords.latitude;
      let lon = pos.coords.longitude;
      map.setView([lat, lon], 15);
      marker.setLatLng([lat, lon]);

      fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`)
        .then(res => res.json())
        .then(data => {
          document.getElementById('address').innerText = data.display_name || "Address not found";
          document.getElementById('latitude').value = lat;
          document.getElementById('longitude').value = lon;
          document.getElementById('fullAddress').value = data.display_name;
        });
    });
  }
</script>
</body>
</html>
