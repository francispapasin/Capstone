<?php  
    include '../connection/dbConnect.php';
    include '../backend/login_process.php';

    $profile_picture = (!empty($_SESSION['profilePicture']) && file_exists($_SESSION['profilePicture'])) 
        ? $_SESSION['profilePicture'] 
        : 'Image/Alumni.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Map</title>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>


        #mapCanvas {
            position: absolute;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            z-index: 0;
        }

        #filters-container {
            position: absolute;
            top: 100px; left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 8px;
            border-radius: 8px;
            display: flex;
            gap: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        #filters-container select, 
        #filters-container input {
            padding: 17px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-container { position: relative; }

        .suggestions-container {
            position: absolute;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
            width: 100%;
            display: none;
        }

        .suggestion-item {
            padding: 10px;
            cursor: pointer;
        }

        .suggestion-item:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>  

    <div id="filters-container">
        <p>Choose Course:</p>
        <select id="courseFilter" style="height: 60px">
            <option selected="selected">All</option>
            <option>Bachelor of Science in Accountancy</option>
            <option>Bachelor of Science in Business Administration</option>
            <option>Bachelor of Science in Criminology</option>
            <option>Bachelor of Science in Education</option>
            <option>Bachelor of Science in Hospitality Management</option>
            <option>Bachelor of Science in Information Technology</option>
            <option>Bachelor of Science in Radiologic Technology</option>      
            <option>Bachelor of Science in Tourism Management</option>                            
        </select>

        <p>Search Alumni:</p>
        <div class="search-container">
            <input type="text" id="searchAlumni" size="50" placeholder="Enter name">
            <div id="suggestions" class="suggestions-container"></div>
        </div>

        <p>Choose Continent:</p>
        <select id="continentFilter" style="height: 60px">
            <option value="All" selected="selected">All</option>
            <option value="Africa">Africa</option>
            <option value="Antarctica">Antarctica</option>
            <option value="Asia">Asia</option>
            <option value="Australia">Australia</option>
            <option value="Europe">Europe</option>
            <option value="North America">North America</option>
            <option value="South America">South America</option>
        </select>
    </div>

    <div id="mapCanvas"></div>

    <script>
        var map;
        var markers = [];
        var alumniData = [];

        function initialize() {
            // Init map
            map = L.map('mapCanvas').setView([10, 10], 2.5);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors &copy; <a href="https://www.carto.com/">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);


            loadMarkers();
            fetchAlumniData();
        }

        function fetchAlumniData() {
            fetch('../backend/getAlumniLocation_process.php')
                .then(response => response.json())
                .then(data => {
                    alumniData = data;
                })
                .catch(error => console.error("Error fetching alumni data:", error));
        }

        function loadMarkers(course = 'All', searchName = '') {
            // Clear markers
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            fetch(`../backend/getAlumniLocation_process.php?course=${encodeURIComponent(course)}&searchName=${encodeURIComponent(searchName)}`)
                .then(response => response.json())
                .then(data => {
                    console.log("Filtered Alumni Data:", data);

                    let locationGroups = {};

                    data.forEach(alumni => {
                        if (!isNaN(alumni.lat) && !isNaN(alumni.lng)) {
                            let key = `${alumni.lat},${alumni.lng}`;
                            if (!locationGroups[key]) {
                                locationGroups[key] = [];
                            }
                            locationGroups[key].push(alumni);
                        } else {
                            console.error("Invalid coordinates for:", alumni);
                        }
                    });

                    Object.keys(locationGroups).forEach(key => {
                        let [lat, lng] = key.split(',').map(Number);
                        let usersAtLocation = locationGroups[key];

                        let marker = L.marker([lat, lng]).addTo(map);

                        let content = `<div style="display: flex; flex-direction: column; align-items: flex-start;">`;
                        usersAtLocation.forEach(user => {
                            content += `
                                <div style="display: flex; align-items: center; padding: 5px; font-size: 14px;">
                                    <a href="profile.php?id=${user.id}" style="display: flex; align-items: center; text-decoration: none; color: black;">
                                        <img src="${user.profile_picture || 'Image/Alumni.png'}" alt="${user.name}'s profile" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                                        <span>${user.name}</span>
                                    </a>
                                </div>
                            `;
                        });
                        content += `</div>`;

                        marker.bindPopup(content);
                        markers.push(marker);
                    });

                    console.log("Markers Loaded:", markers.length);
                })
                .catch(error => console.error("Error loading markers:", error));
        }

        function moveToContinent(continent) {
            const continentCenters = {
                "All": { lat: 10, lng: 10, zoom: 2.5 },
                "Africa": { lat: 1.5, lng: 17.6, zoom: 3.8 },
                "Antarctica": { lat: -75.0, lng: 0.0, zoom: 3 },
                "Asia": { lat: 34.0479, lng: 100.6197, zoom: 4 },
                "Australia": { lat: -25.2744, lng: 133.7751, zoom: 5 },
                "Europe": { lat: 54.5260, lng: 15.2551, zoom: 4 },
                "North America": { lat: 54.5260, lng: -105.2551, zoom: 4 },
                "South America": { lat: -14.2350, lng: -51.9253, zoom: 4 }
            };

            var location = continentCenters[continent] || continentCenters["All"];
            map.setView([location.lat, location.lng], location.zoom);
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('continentFilter').addEventListener('change', function() {
                moveToContinent(this.value);
            });

            document.getElementById('courseFilter').addEventListener('change', function() {
                loadMarkers(this.value);
            });

            document.getElementById('searchAlumni').addEventListener('input', function() {
                const query = this.value.toLowerCase();
                const suggestionsContainer = document.getElementById('suggestions');
                suggestionsContainer.innerHTML = '';

                if (query) {
                    const filteredAlumni = alumniData.filter(alumni => 
                        alumni.name.toLowerCase().includes(query)
                    );

                    filteredAlumni.forEach(alumni => {
                        const suggestionItem = document.createElement('div');
                        suggestionItem.classList.add('suggestion-item');
                        suggestionItem.textContent = alumni.name;

                        suggestionItem.addEventListener('click', function() {
                            document.getElementById('searchAlumni').value = alumni.name;
                            suggestionsContainer.innerHTML = '';
                            suggestionsContainer.style.display = 'none';
                            loadMarkers(document.getElementById('courseFilter').value, alumni.name);
                        });

                        suggestionsContainer.appendChild(suggestionItem);
                    });

                    suggestionsContainer.style.display = filteredAlumni.length > 0 ? 'block' : 'none';
                } else {
                    suggestionsContainer.style.display = 'none';
                }
            });

            initialize();
        });
    </script>
</body>
</html>
