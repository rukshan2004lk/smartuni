<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - Campus Map";
$currentPage = "map";
require_once "includes/header.php";

$user = $_SESSION['user'] ?? [
    'id' => 1,
    'fname' => 'Sandeesha',
    'lname' => 'Rukshan',
    'role_id' => 1
];

$userName = htmlspecialchars($user['fname'] ?? 'User');
?>

<!-- Page Header -->
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h1 class="fw-bold fs-3 mb-1">Campus Map & Floor Plans</h1>
        <p class="text-secondary small mb-0">Navigate through campus buildings and facilities</p>
    </div>
</div>

<!-- Step 1: Building Selection View -->
<div id="building-selection" class="row g-4">
    <div class="col-12 col-md-6">
        <div class="bg-white border rounded-3 p-4 shadow-sm text-center position-relative overflow-hidden" 
             style="cursor: pointer; transition: transform 0.2s;" 
             onclick="selectBuilding('Solertia')"
             onmouseover="this.style.transform='translateY(-5px)'"
             onmouseout="this.style.transform='translateY(0)'">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 80px; height: 80px;">
                    <i class="bi bi-building fs-1"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark">Solertia Building</h4>
            <p class="text-muted small mb-0">Engineering Technology & ICT Complex</p>
            <button class="btn btn-outline-primary mt-3 px-4 rounded-pill">View Plans</button>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="bg-white border rounded-3 p-4 shadow-sm text-center position-relative overflow-hidden" 
             style="cursor: pointer; transition: transform 0.2s;" 
             onclick="selectBuilding('Genesis')"
             onmouseover="this.style.transform='translateY(-5px)'"
             onmouseout="this.style.transform='translateY(0)'">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-info-subtle text-info rounded-circle" style="width: 80px; height: 80px;">
                    <i class="bi bi-buildings fs-1"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark">Genesis Building</h4>
            <p class="text-muted small mb-0">Biosystems Technology Building Complex</p>
            <button class="btn btn-outline-info mt-3 px-4 rounded-pill">View Plans</button>
        </div>
    </div>
</div>

<!-- Step 2: Floor Viewer (Hidden by default) -->
<div id="floor-viewer" class="d-none">
    <div class="bg-white border rounded-3 p-4 shadow-sm">
        
        <!-- Viewer Header -->
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">
                <button class="btn btn-light rounded-circle" onclick="backToSelection()" title="Back to Buildings">
                    <i class="bi bi-arrow-left fs-5"></i>
                </button>
                <div>
                    <h4 id="viewer-title" class="fw-bold mb-0 text-primary">Building Name</h4>
                    <span id="floor-name" class="badge bg-secondary">Ground Floor</span>
                </div>
            </div>

            <!-- Floor Navigation Controls -->
            <div class="d-flex align-items-center gap-2">
                <button id="prev-btn" class="btn btn-outline-primary d-flex align-items-center gap-2" onclick="prevFloor()">
                    <i class="bi bi-chevron-left"></i> Previous
                </button>
                
                <!-- Optional: Dropdown to jump directly to a floor -->
                <select id="floor-select" class="form-select form-select-sm w-auto fw-bold" onchange="jumpToFloor(this.value)">
                    <!-- Options populated via JS -->
                </select>

                <button id="next-btn" class="btn btn-outline-primary d-flex align-items-center gap-2" onclick="nextFloor()">
                    Next <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Image Container -->
        <div class="text-center bg-light rounded-3 p-3 position-relative min-vh-50 d-flex align-items-center justify-content-center" style="min-height: 500px;">
            <div id="loading-spinner" class="spinner-border text-primary position-absolute d-none" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <img id="floor-image" src="" alt="Floor Plan" class="img-fluid rounded shadow-sm border" style="max-height: 800px; object-fit: contain;">
        </div>

    </div>
</div>

<script>
    // Define the floor plans for each building. 
    // Update the 'img' paths to match your actual server directory structure.
    const mapData = {
        'Solertia': [
            { name: 'Ground Floor', img: 'images/maps/solertia/ground.jfif' },
            { name: 'Floor 1', img: 'images/maps/solertia/floor1.jfif' },
            { name: 'Floor 2', img: 'images/maps/solertia/floor2.jfif' },
            { name: 'Floor 3', img: 'images/maps/solertia/floor3.jfif' },
            { name: 'Floor 4', img: 'images/maps/solertia/floor4.jfif' },
            { name: 'Floor 5', img: 'images/maps/solertia/floor5.jfif' }
        ],
        'Genesis': [
            { name: 'Ground Floor', img: 'images/maps/gensis/ground.jfif' },
            { name: 'Floor 1', img: 'images/maps/gensis/floor1.jfif' },
            { name: 'Floor 2', img: 'images/maps/gensis/floor2.jfif' },
            { name: 'Floor 3', img: 'images/maps/gensis/floor3.jfif' },
            { name: 'Floor 4', img: 'images/maps/gensis/floor4.jfif' },
            { name: 'Floor 5', img: 'images/maps/gensis/floor5.jfif' }
        ]
    };

    let currentBuilding = '';
    let currentFloorIndex = 0;

    function selectBuilding(buildingName) {
        currentBuilding = buildingName;
        currentFloorIndex = 0; // Always start at ground floor (index 0)

        // Toggle visibility
        document.getElementById('building-selection').classList.add('d-none');
        document.getElementById('floor-viewer').classList.remove('d-none');
        
        // Set titles
        document.getElementById('viewer-title').innerText = buildingName + ' Building';

        // Populate dropdown menu
        const selectBox = document.getElementById('floor-select');
        selectBox.innerHTML = '';
        mapData[buildingName].forEach((floor, index) => {
            const option = document.createElement('option');
            option.value = index;
            option.text = floor.name;
            selectBox.appendChild(option);
        });

        renderFloor();
    }

    function renderFloor() {
        const floorData = mapData[currentBuilding][currentFloorIndex];
        const imgElement = document.getElementById('floor-image');
        const spinner = document.getElementById('loading-spinner');

        // Show spinner while loading new image
        spinner.classList.remove('d-none');
        imgElement.classList.add('opacity-50');

        imgElement.onload = function() {
            spinner.classList.add('d-none');
            imgElement.classList.remove('opacity-50');
        };

        // Update UI elements
        imgElement.src = floorData.img;
        document.getElementById('floor-name').innerText = floorData.name;
        document.getElementById('floor-select').value = currentFloorIndex;

        // Manage button states (disable Prev on Ground, disable Next on top floor)
        document.getElementById('prev-btn').disabled = (currentFloorIndex === 0);
        document.getElementById('next-btn').disabled = (currentFloorIndex === mapData[currentBuilding].length - 1);
    }

    function nextFloor() {
        if (currentFloorIndex < mapData[currentBuilding].length - 1) {
            currentFloorIndex++;
            renderFloor();
        }
    }

    function prevFloor() {
        if (currentFloorIndex > 0) {
            currentFloorIndex--;
            renderFloor();
        }
    }

    function jumpToFloor(index) {
        currentFloorIndex = parseInt(index);
        renderFloor();
    }

    function backToSelection() {
        document.getElementById('floor-viewer').classList.add('d-none');
        document.getElementById('building-selection').classList.remove('d-none');
        document.getElementById('floor-image').src = ''; // clear image to save memory
    }
</script>

<?php require_once "includes/footer.php"; ?>