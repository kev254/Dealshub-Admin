<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

// Check if a branch ID is provided
if (isset($_GET['bid'])) {
    $branch_id = base64_decode($_GET['bid']); // Get the branch ID from URL

    // Fetch the branch details based on vendor_id and branch_id
    $stmt = $conn->prepare("SELECT * FROM branches WHERE id = ? AND vendor_id = ?");
    $stmt->bind_param("ii", $branch_id, $vid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error = "Branch not found or you're not authorized to edit this branch.";
    } else {
        $branch = $result->fetch_assoc(); // Fetch branch details

        // Set the initial values for the form
        $branch_name = $branch['branch_name'];
        $latitude = $branch['latitude'];
        $longitude = $branch['longitude'];
        $status = $branch['status'];
    }

    $stmt->close();
} else {
    // If no branch_id is provided, redirect to branch list page
    header("Location: branches");
    exit;
}

// Check if the form has been submitted for updating the branch
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $branch_name = trim($_POST['branch_name']);
    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);
    $status = intval($_POST['status']); // Active or Inactive

    // Server-side validation
    if (empty($branch_name)) {
        $error = "Branch name is required.";
    } elseif (empty($latitude) || empty($longitude)) {
        $error = "Latitude and Longitude are required.";
    } else {
        // Update the branch in the database
        $stmt = $conn->prepare("UPDATE branches SET branch_name = ?, latitude = ?, longitude = ?, status = ? WHERE id = ? AND vendor_id = ?");
        $stmt->bind_param("ssddii", $branch_name, $latitude, $longitude, $status, $branch_id, $vid);

        if ($stmt->execute()) {
            $success = "Branch updated successfully!";
            header("refresh:1; url=branches"); // Redirect to branches page after success
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <?php include("partials/head.php"); ?>

    <body>
        <div class="page-wrapper compact-wrapper" id="pageWrapper">
            <?php include("partials/header.php"); ?>
            <div class="page-body-wrapper">
                <?php include("partials/sidebar.php"); ?>
                <div class="page-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Edit Branch</h5>
                                        <?php if (isset($error)): ?>
                                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                        <?php endif; ?>
                                        <?php if (isset($success)): ?>
                                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                                        <?php endif; ?>
                                        <form class="theme-form" method="POST">
                                            <div class="row">
                                                <div class="col-md-12 mb-4">
                                                    <label class="form-label">Branch Name</label>
                                                    <input class="form-control" type="text" name="branch_name" value="<?= htmlspecialchars($branch_name) ?>" required>
                                                </div>
                                                
                                                <div class="col-md-12 mb-4">
                                                <label class="form-label">Search Location on map (By town name, street name e.tc. e.g Kimathi street)</label>
                                                <input id="autocomplete" class="form-control" type="text" placeholder="Enter city or place">
                                            </div>
                                                <div class="col-md-12 mb-4">
                                                    <div id="map" style="height: 400px; width: 100%;"></div>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <label class="form-label">Latitude</label>
                                                    <input class="form-control" type="text" name="latitude" id="latitude" value="<?= htmlspecialchars($latitude) ?>" required readonly>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <label class="form-label">Longitude</label>
                                                    <input class="form-control" type="text" name="longitude" id="longitude" value="<?= htmlspecialchars($longitude) ?>" required readonly>
                                                </div>
                                                
                                                <div class="col-md-6 mb-4">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-control" name="status">
                                                        <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Active</option>
                                                        <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 text-end">
                                                    <button class="btn btn-primary" type="submit">Update Branch</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include("partials/footer.php"); ?>
        </div>
    </div>

    

    <?php include("partials/js.php"); ?>
    <!-- Google Maps API -->
<script 
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBdY0xpkwh1pRqAdBBDjs6pbnfjjFTEK-M&callback=initMap&libraries=places" 
  async 
  defer>
</script>

<script>
  function initMap() {
    // Initialize the map with the user's current location or a default location
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (position) {
        const userLat = position.coords.latitude;
        const userLng = position.coords.longitude;

        const map = new google.maps.Map(document.getElementById("map"), {
          center: { lat: userLat, lng: userLng },
          zoom: 13,
          mapTypeControl: false,
        });

        const marker = new google.maps.Marker({
          map: map,
          position: { lat: userLat, lng: userLng },
          draggable: true,
        });

        // Update latitude and longitude when marker is dragged
        google.maps.event.addListener(marker, "dragend", function () {
          const position = marker.getPosition();
          document.getElementById("latitude").value = position.lat();
          document.getElementById("longitude").value = position.lng();
        });

        // Update latitude and longitude when map is clicked
        google.maps.event.addListener(map, "click", function (event) {
          marker.setPosition(event.latLng);
          document.getElementById("latitude").value = event.latLng.lat();
          document.getElementById("longitude").value = event.latLng.lng();
        });

        // Autocomplete search for place names
        const input = document.getElementById("autocomplete");
        const autocomplete = new google.maps.places.Autocomplete(input, {
          fields: ["formatted_address", "geometry", "name"],
          strictBounds: false,
        });

        // Bind the map's bounds to the autocomplete object
        autocomplete.bindTo("bounds", map);

        const infowindow = new google.maps.InfoWindow();
        const infowindowContent = document.getElementById("infowindow-content");

        infowindow.setContent(infowindowContent);

        autocomplete.addListener("place_changed", function () {
          infowindow.close();
          marker.setVisible(false);

          const place = autocomplete.getPlace();
          if (!place.geometry || !place.geometry.location) {
            window.alert("No details available for input: '" + place.name + "'");
            return;
          }

          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(30);
          }

          marker.setPosition(place.geometry.location);
          marker.setVisible(true);

          infowindowContent.children["place-name"].textContent = place.name;
          infowindowContent.children["place-address"].textContent = place.formatted_address;
          infowindow.open(map, marker);

          // Update latitude and longitude based on selected place
          document.getElementById("latitude").value = place.geometry.location.lat();
          document.getElementById("longitude").value = place.geometry.location.lng();
        });
      });
    } else {
      // Default to a generic location if geolocation is not available
      const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 40.749933, lng: -73.98633 },
        zoom: 30,
        mapTypeControl: false,
      });
    }
  }

  // Declare the initMap function for the global window object
  window.initMap = initMap;
</script>
</body>
</html>
