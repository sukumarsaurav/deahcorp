<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Digital Agency'; ?> - Your One-Stop Solution</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php if (isset($pageStyle)): ?>
    <link rel="stylesheet" href="/assets/css/<?php echo $pageStyle; ?>.css">
    <?php endif; ?>
</head>
<body>
    <header class="header">
        <div class="container nav-container">
            <a href="/" class="logo">DigitalAgency</a>
            <nav>
                <button class="menu-toggle" id="menuToggle">
                    <span class="hamburger"></span>
                </button>
                <ul class="nav-menu">
                    <li><a href="/">Home</a></li>
                    <li><a href="/pages/services.php">Services</a></li>
                    <li><a href="/pages/portfolio.php">Portfolio</a></li>
                    <li><a href="/pages/about.php">About</a></li>
                    <li><a href="/pages/blog.php">Blog</a></li>
                    <li><a href="/pages/contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>
</body>
</html> 