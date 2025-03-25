<?php
session_start();
include("connections.php");

// Redirect to login if not logged in or not a regular user
if (!isset($_SESSION["user_id"]) || $_SESSION["account_type"] != "2") {
    header("Location: login.php");
    exit();
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch products from the database
$query = "SELECT * FROM products";
$result = $connections->query($query);
if (!$result) {
    die("Query failed: " . $connections->error);
}
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Debug: Check if products are fetched
if (empty($products)) {
    echo "No products found in the database.";
    exit();
}

// Fetch hero images from the database
$query = "SELECT * FROM hero_images ORDER BY id ASC LIMIT 3";
$result = $connections->query($query);
if (!$result) {
    die("Query failed: " . $connections->error);
}
$hero_images = [];
while ($row = $result->fetch_assoc()) {
    $hero_images[] = $row;
}

// Debug: Check if hero images are fetched
if (empty($hero_images)) {
    echo "No hero images found in the database.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shop - Home</title>
    <!-- Import Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons (from nav.php) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f7f6; 
            margin: 0; 
            padding: 0; 
        }
        .container { 
            max-width: 1200px;
            margin: 20px auto; 
            padding: 20px; 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        }
        h1 { 
            text-align: center; 
            color: #333; 
            font-family: 'Playfair Display', serif;
            font-size: 2.5em; 
            font-weight: 700; 
            letter-spacing: 1px; 
            margin-bottom: 20px; 
        }

        /* Hero Carousel Styles */
        .hero-carousel-container {
            position: relative;
            width: 100%;
            overflow: hidden;
            margin-bottom: 20px; /* Space between carousels */
        }
        .hero-carousel {
            display: flex;
            width: 300%; /* 3 images × 100% */
            transition: transform 0.5s ease-in-out;
        }
        .hero-carousel-item {
            flex: 0 0 100%; /* Show 1 image at a time */
            box-sizing: border-box;
        }
        .hero-carousel-item img {
            width: auto;
            height: 750px; /* Fixed height for hero images */
            object-fit: cover; /* Crop to fit */
            border-radius: 8px;
            display: block;
        }
        .carousel-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }
        .carousel-indicators .dot {
            width: 12px;
            height: 12px;
            background-color: #bbb;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .carousel-indicators .dot.active {
            background-color: #333;
        }

        /* Product Carousel Styles */
        .carousel-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }
        .carousel {
            display: flex;
            width: 400%; /* 8 original + 8 duplicates = 16 items × 25% = 400% */
            transition: transform 0.5s ease-in-out;
        }
        .carousel-item {
            flex: 0 0 25%; /* Show 4 items (100% / 4) */
            box-sizing: border-box;
            padding: 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .carousel-item a {
            text-decoration: none; /* Remove underline from link */
        }
        .carousel-item img {
            width: 224px;
            height: 168px;
            object-fit: contain;
            border-radius: 8px;
            transition: transform 0.3s ease;
            margin: 0 auto;
        }
        .carousel-item img:hover {
            transform: scale(1.05);
        }
        .carousel-item h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1em;
            margin: 8px 0 5px;
            color: #333;
            line-height: 1.2;
            max-width: 224px;
        }
        .carousel-item p {
            font-size: 0.9em;
            color: #666;
            margin: 0;
        }
        .arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2em;
            color: #333;
            background: rgba(255, 255, 255, 0.7);
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 10;
            transition: background 0.3s ease;
        }
        .arrow:hover {
            background: rgba(255, 255, 255, 0.9);
        }
        .arrow-left {
            left: 10px;
        }
        .arrow-right {
            right: 10px;
        }

        /* Responsive Adjustments for Product Carousel */
        @media (max-width: 768px) {
            .carousel-item {
                flex: 0 0 50%;
            }
            .carousel {
                width: 800%;
            }
            .carousel-item img {
                width: 200px;
                height: 150px;
            }
            .carousel-item h3 {
                max-width: 200px;
                font-size: 1em;
            }
        }
        @media (max-width: 480px) {
            .carousel-item {
                flex: 0 0 100%;
            }
            .carousel {
                width: 1600%;
            }
            .carousel-item img {
                width: 180px;
                height: 135px;
            }
            .carousel-item h3 {
                max-width: 180px;
                font-size: 0.9em;
            }
            .carousel-item p {
                font-size: 0.8em;
            }
        }

        /* Responsive Adjustments for Hero Carousel */
        @media (max-width: 768px) {
            .hero-carousel-item {
                height: 600px;
            }
        }
        @media (max-width: 480px) {
            .hero-carousel-item {
                height: 400px;
            }
            .carousel-indicators .dot {
                width: 10px;
                height: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include("nav.php"); ?>
    <div class="hero-carousel-container">

        <div class="hero-carousel" id="heroCarousel">
            <?php foreach ($hero_images as $hero_image) { ?>
                <div class="hero-carousel-item">
                    <img src="get_image.php?id=<?php echo $hero_image['id']; ?>&type=hero" alt="<?php echo htmlspecialchars($hero_image['alt_text']); ?>">
                </div>
            <?php } ?>
        </div>
        <div class="carousel-indicators">
            <?php for ($i = 0; $i < count($hero_images); $i++) { ?>
                <span class="dot" onclick="goToHeroSlide(<?php echo $i; ?>)"></span>
            <?php } ?>
        </div>
    </div>
    <h1>Refined. Timeless. Yours, <?php echo $_SESSION["name"]; ?>.</h1>
    <div class="container">
        <!-- Product Carousel -->
        <div class="carousel-container">
            <button class="arrow arrow-left" onclick="moveCarousel(-1)">‹</button>
            <div class="carousel" id="carousel">
                <!-- 8 Original Products -->
                <?php foreach ($products as $index => $product) { ?>
                    <div class="carousel-item">
                        <a href="product.php?id=<?php echo $product['id']; ?>">
                            <img src="get_image.php?id=<?php echo $product['id']; ?>&type=product" alt="Fragrance <?php echo $index + 1; ?>">
                        </a>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>$<?php echo number_format($product['price'], 2); ?></p>
                    </div>
                <?php } ?>
                <!-- Duplicates for Infinite Loop -->
                <?php foreach ($products as $index => $product) { ?>
                    <div class="carousel-item">
                        <a href="product.php?id=<?php echo $product['id']; ?>">
                            <img src="get_image.php?id=<?php echo $product['id']; ?>&type=product" alt="Fragrance <?php echo $index + 1; ?>">
                        </a>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>$<?php echo number_format($product['price'], 2); ?></p>
                    </div>
                <?php } ?>
            </div>
            <button class="arrow arrow-right" onclick="moveCarousel(1)">›</button>
        </div>
    </div>

    <script>
        // Hero Carousel (Auto-Swiping with Dots)
        const heroCarousel = document.getElementById('heroCarousel');
        const dots = document.querySelectorAll('.carousel-indicators .dot');
        const totalHeroSlides = <?php echo count($hero_images); ?>;
        let currentHeroSlide = 0;

        function updateHeroSlide() {
            const offset = currentHeroSlide * -100;
            heroCarousel.style.transform = `translateX(${offset}%)`;
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentHeroSlide);
            });
        }

        function goToHeroSlide(index) {
            currentHeroSlide = index;
            updateHeroSlide();
        }

        function autoSwipeHero() {
            currentHeroSlide = (currentHeroSlide + 1) % totalHeroSlides;
            updateHeroSlide();
        }

        setInterval(autoSwipeHero, 3000);
        updateHeroSlide();

        // Product Carousel (Infinite Loop)
        const carousel = document.getElementById('carousel');
        const totalItems = 8;
        const visibleItems = 4;
        let currentIndex = totalItems;

        carousel.style.transform = `translateX(${-totalItems * 25}%)`;

        function moveCarousel(direction) {
            currentIndex += direction;

            const offset = currentIndex * -25;
            carousel.style.transition = 'transform 0.5s ease-in-out';
            carousel.style.transform = `translateX(${offset}%)`;

            if (currentIndex >= totalItems * 2) {
                setTimeout(() => {
                    carousel.style.transition = 'none';
                    currentIndex = totalItems;
                    carousel.style.transform = `translateX(${-totalItems * 25}%)`;
                }, 500);
            } else if (currentIndex < totalItems) {
                setTimeout(() => {
                    carousel.style.transition = 'none';
                    currentIndex = totalItems * 2 - 1;
                    carousel.style.transform = `translateX(${-(totalItems * 2 - 1) * 25}%)`;
                }, 500);
            }
        }
    </script>
</body>
</html>