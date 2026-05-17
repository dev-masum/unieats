<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/functions.php';
requireLogin();
requireStudent();

$db = createDb()->getConnection();
$menuModel = new Menu($db);
$cartModel = new Cart($db);
$userId = (int) $_SESSION['user_id'];

$category = trim($_GET['category'] ?? '');
$showAvailable = isset($_GET['available']) ? 1 : 0;
$items = [];
if ($category !== '') {
    $items = $menuModel->getItemsByCategory($category);
} else {
    $items = $showAvailable ? $menuModel->getAvailableItems() : $menuModel->getAllItems();
}
$categories = $menuModel->getCategories();
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menuId = (int) ($_POST['menu_id'] ?? 0);
    $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
    if ($menuId > 0) {
        $success = $cartModel->addItem($userId, $menuId, $quantity);
        $message = $success ? 'Item added to cart.' : 'Unable to add item to cart.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>UniEats Menu</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Menu</h1>
            <nav>
                <a href="menu.php">Menu</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">Orders</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        <main>
            <?php if ($message): ?>
                <div class="alert"><?= escape($message) ?></div>
            <?php endif; ?>
            <section class="filters">
                <form method="get" action="menu.php">
                    <label>Category</label>
                    <select name="category">
                        <option value="">All categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= escape($cat) ?>" <?= $cat === $category ? 'selected' : '' ?>><?= escape($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label>
                        <input type="checkbox" name="available" value="1" <?= $showAvailable ? 'checked' : '' ?>>
                        Only available
                    </label>
                    <button type="submit">Filter</button>
                </form>
            </section>
            <section class="menu-grid">
                <?php if (empty($items)): ?>
                    <p>No menu items found.</p>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <div class="menu-card">
                            <h3><?= escape($item['ItemName']) ?></h3>
                            <p class="meta"><?= escape($item['Category']) ?> • $<?= number_format($item['Price'], 2) ?></p>
                            <p><?= escape($item['Description']) ?></p>
                            <p>Status: <?= $item['Availability'] ? 'Available' : 'Unavailable' ?></p>
                            <?php if ($item['Availability']): ?>
                                <form method="post" action="menu.php">
                                    <input type="hidden" name="menu_id" value="<?= escape($item['MenuID']) ?>">
                                    <label for="quantity-<?= escape($item['MenuID']) ?>">Qty</label>
                                    <input type="number" id="quantity-<?= escape($item['MenuID']) ?>" name="quantity" value="1"
                                        min="1">
                                    <button type="submit">Add to Cart</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>

</html>