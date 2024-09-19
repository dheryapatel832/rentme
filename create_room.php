<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Room</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places" async defer></script>
    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function showPosition(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            var geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(lat, lng);

            geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        document.getElementById('address').value = results[0].formatted_address;
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                    } else {
                        alert("No results found");
                    }
                } else {
                    alert("Geocoder failed due to: " + status);
                }
            });
        }

        function showError(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    alert("User denied the request for Geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    alert("The request to get user location timed out.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("An unknown error occurred.");
                    break;
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Create Room</h1>
    </header>

    <main>
        <form action="submit_room.php" method="POST" enctype="multipart/form-data">
            <label for="room-title">Room Title:</label>
            <input type="text" id="room-title" name="title" required>

            <label for="room-description">Description:</label>
            <textarea id="room-description" name="description" required></textarea>

            <label for="room-price">Price per Night:</label>
            <input type="number" id="room-price" name="price" required>

            <label for="room-address">Address:</label>
            <input type="text" id="address" name="address" readonly>

            <label for="room-latitude">Latitude:</label>
            <input type="text" id="latitude" name="latitude" readonly>

            <label for="room-longitude">Longitude:</label>
            <input type="text" id="longitude" name="longitude" readonly>

            <button type="button" onclick="getLocation()">Get My Location</button>

            <label for="room-images">Upload Images:</label>
            <input type="file" id="room-images" name="images[]" multiple>

            <button type="submit">Add Room</button>
        </form>
    </main>
</body>
</html>
