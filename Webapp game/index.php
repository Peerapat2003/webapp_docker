<?php
$host = getenv('MYSQL_HOST') ?: 'db';
$user = getenv('MYSQL_USER') ?: 'root';
$pass = getenv('MYSQL_PASSWORD') ?: 'secret';
$dbname = getenv('MYSQL_DATABASE') ?: 'game_store';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// สร้างตาราง games ถ้ายังไม่มี
$conn->query("
CREATE TABLE IF NOT EXISTS games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL UNIQUE,
    genre VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT ''
)
");

// ข้อมูลตัวอย่าง
$games = [
    ['Cyberpunk 2077', 'RPG', 59.99, 'images/cyberpunk_2077.jpg'],
    ['FIFA 23', 'Sports', 49.99, 'images/fifa_23.jpg'],
    ['Call of Duty: Modern Warfare II', 'Shooter', 69.99, 'images/call_of_duty_modern_warfare_ii.jpg'],
    ['Among Us', 'Party', 4.99, 'images/amongus.jpg'],
    ['Minecraft', 'Sandbox', 29.99, 'images/minecraft.jpg'],
    ['The Witcher 3: Wild Hunt', 'RPG', 39.99, 'images/witcher3.jpg'],
    ['Grand Theft Auto V', 'Action', 29.99, 'images/gta5.jpg'],
    ['Resident Evil 4 Remake', 'Horror', 59.99, 'images/re4_remake.jpg'],
    ['Elden Ring', 'RPG', 59.99, 'images/elden_ring.jpg'],
    ['Need for Speed: Heat', 'Racing', 49.99, 'images/nfs_heat.jpg'],
    ['Overwatch 2', 'Shooter', 0.00, 'images/overwatch2.jpg'],
    ['Tekken 7', 'Action', 39.99, 'images/tekken7.jpg'],
];

foreach ($games as $game) {
    [$title, $genre, $price, $image] = $game;
    $conn->query("
        INSERT INTO games (title, genre, price, image)
        VALUES ('$title','$genre',$price,'$image')
        ON DUPLICATE KEY UPDATE title=title
    ");
}

// Pagination
$per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;

// ดึงข้อมูลสำหรับหน้าปัจจุบัน
$result = $conn->query("SELECT * FROM games LIMIT $start, $per_page");

// หาจำนวนหน้า
$total_result = $conn->query("SELECT COUNT(*) as total FROM games")->fetch_assoc()['total'];
$total_page = ceil($total_result / $per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Game Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Montserrat', sans-serif;
    background-color: #1e1e2f;
    color: #fff;
    min-height: 100vh;
}
.container {
    max-width: 1100px;
    margin: 40px auto;
    padding: 20px;
}
.game-header {
    background: linear-gradient(135deg, #8c52ff, #5e3aff);
    padding: 30px 20px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(140,82,255,0.5);
    position: relative;
    overflow: hidden;
}
.game-header h1 {
    color: #fff;
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 10px;
    text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
}
.game-header .subtitle {
    color: #e0dfff;
    font-size: 1.1rem;
    font-weight: 500;
}
.game-header .header-icon {
    position: absolute;
    top: -10px;
    left: -10px;
    font-size: 4rem;
    opacity: 0.2;
    transform: rotate(-15deg);
}
.card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    background: linear-gradient(135deg, #2a2a3c, #1e1e2f);
    box-shadow: 0 8px 15px rgba(0,0,0,0.3);
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 25px rgba(140,82,255,0.6);
}
.card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}
.card-body {
    padding: 15px;
}
.card-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #fff;
}
.card-text {
    font-size: 0.95rem;
    color: #ccc;
    margin: 5px 0;
}
.card-text span {
    color: #8c52ff;
    font-weight: 600;
}
.pagination {
    justify-content: center;
    margin-top: 30px;
}
.page-item .page-link {
    color: #fff;
    background-color: #2a2a3c;
    border: none;
    margin: 0 5px;
    transition: all 0.3s;
}
.page-item.active .page-link {
    background-color: #8c52ff;
    color: #fff;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(140,82,255,0.5);
}
.page-item .page-link:hover {
    background-color: #5e3aff;
    color: #fff;
}
</style>
</head>
<body>
<div class="container">
    <header class="game-header text-center mb-5">
    <div class="header-icon">🎮</div>
    <h1>Game Store</h1>
    <p class="subtitle">Discover, Play & Enjoy Your Favorite Games</p>
    </header>

    <div class="row g-4">
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="col-md-4">
            <div class="card">
                <?php if($row['image']): ?>
                    <img src="<?= $row['image'] ?>" alt="<?= $row['title'] ?>">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?= $row['title'] ?></h5>
                    <p class="card-text">Genre: <span><?= $row['genre'] ?></span></p>
                    <p class="card-text">Price: <span>$<?= $row['price'] ?></span></p>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    </div>

    <!-- Pagination Links -->
    <nav>
        <ul class="pagination">
            <?php for($i=1; $i<=$total_page; $i++): ?>
                <li class="page-item <?= ($i==$page)?'active':'' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>
<?php $conn->close(); ?>
