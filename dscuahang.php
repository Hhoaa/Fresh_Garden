<?php
// Dữ liệu cửa hàng
$stores = [
    "Hai Bà Trưng" => [
        [
            "name" => "Cửa hàng Vinmec Times City",
            "address" => "Tầng 1 Bệnh viện Vinmec Times City, số 458 Minh Khai, Hai Bà Trưng, Hà Nội",
            "phone" => "(024) 3856 3856 - 312",
            "lat" => 20.9945,
            "lng" => 105.8636
        ],
        [
            "name" => "Cửa hàng Vinschool T36",
            "address" => "KĐT Time City - Hai Bà Trưng - Hà Nội",
            "phone" => "(024) 3856 3856 - 310",
            "lat" => 20.9950,
            "lng" => 105.8650
        ]
    ],
    "Cầu Giấy" => [
        [
            "name" => "Cửa hàng Cầu Giấy",
            "address" => "Số 123, Đường Cầu Giấy, Hà Nội",
            "phone" => "(024) 1234 5678",
            "lat" => 21.0285,
            "lng" => 105.7821
        ]
    ]
];

// Lấy quận từ request, mặc định là Hai Bà Trưng
$district = $_GET['district'] ?? "Hai Bà Trưng";
$selectedStores = $stores[$district] ?? [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Cửa Hàng</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding-top: 50px;
        }
        h1 {
            text-align: center;
        }
        label {
            font-weight: bold;
        }
        select {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
        }
        .store-container {
            display: flex;
            gap: 20px;
        }
        .store-list {
            width: 40%;
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            background: #f9f9f9;
        }
        .store-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .store-item:hover {
            background-color: #e0e0e0;
        }
        .map {
            width: 60%;
            height: 400px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>  <!-- Nhúng file header -->

    <h1>Danh Sách Cửa Hàng</h1>

    <label for="district">QUẬN HUYỆN</label>
    <select id="district" onchange="updatePage()">
        <?php foreach ($stores as $key => $value): ?>
            <option value="<?= $key ?>" <?= $key === $district ? 'selected' : '' ?>>Quận <?= $key ?></option>
        <?php endforeach; ?>
    </select>

    <div class="store-container">
        <div id="store-list" class="store-list">
            <?php foreach ($selectedStores as $store): ?>
                <div class="store-item" onclick="getUserLocation(<?= $store['lat'] ?>, <?= $store['lng'] ?>)">
                    <strong><?= $store['name'] ?></strong><br>
                    <p><?= $store['address'] ?></p>
                    <p>☎ <?= $store['phone'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="map" class="map"></div>
    </div>

    <script>
        let map = L.map('map').setView([20.9945, 105.8636], 14);
        let routingControl;

        // Load OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        function getUserLocation(destLat, destLng) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    let userLat = position.coords.latitude;
                    let userLng = position.coords.longitude;
                    showRoute(userLat, userLng, destLat, destLng);
                }, () => {
                    alert("Không thể lấy vị trí của bạn");
                });
            } else {
                alert("Trình duyệt của bạn không hỗ trợ lấy vị trí.");
            }
        }

        function showRoute(userLat, userLng, destLat, destLng) {
            if (routingControl) {
                map.removeControl(routingControl);
            }
            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(userLat, userLng),
                    L.latLng(destLat, destLng)
                ],
                routeWhileDragging: true
            }).addTo(map);
            map.setView([destLat, destLng], 15);
        }

        function updatePage() {
            let district = document.getElementById("district").value;
            window.location.href = "?district=" + district;
        }
    </script>

</body>
</html>
