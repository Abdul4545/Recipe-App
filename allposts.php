<?php
include './pdodb.php';
include './cloudinary_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['name'];

$query = "SELECT p.*, u.name AS author_name, pr.profile_picture_url 
          FROM posts p 
          JOIN users u ON p.user_id = u.id 
          LEFT JOIN profile pr ON u.id = pr.user_id 
          ORDER BY p.createdAt DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Recipes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .content-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin-top: 20px;
        }

        .main-content {
            flex: 1;
            max-width: 700px;
            margin: 0 20px;
        }

        .card {
            background-color: lavender;
            margin-bottom: 1.5rem;
        }

        .carousel-item img,
        .card-img-top {
            width: 100%;
            height: 370px;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                flex-direction: column;
                align-items: center;
            }

            .ad-sidebar {
                margin-top: 20px;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="my-4 text-center">All Recipes</h2>

        <!-- Top Ad Section -->
        <div class="text-center mb-4">
            <!-- Google AdSense Placeholder -->
            <p>Top Ad Placeholder (e.g., Google AdSense)</p>
        </div>

        <div class="content-wrapper">
            <div class="main-content">
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $index => $post): ?>
                        <?php
                        $postId = $post['id'];
                        $foodName = htmlspecialchars($post['food_name']);
                        $description = htmlspecialchars($post['description']);
                        $ingredients = htmlspecialchars($post['ingredients']);
                        $foodType = htmlspecialchars($post['food_type']);

                        $userImage = !empty($post['profile_picture_url']) ? htmlspecialchars($post['profile_picture_url']) : 'default-user-image.jpg';
                        $authorName = htmlspecialchars($post['author_name']);
                        $images = array_filter([$post['image1_url'], $post['image2_url'], $post['image3_url']]);

                        $description_id = "description-" . $postId;
                        $ingredients_id = "ingredients-" . $postId;
                        ?>

                        <div class="card mb-4">
                            <div class="d-flex align-items-center p-3">
                                <img src="<?php echo $userImage; ?>" alt="User Image" class="rounded-circle"
                                    style="width: 80px; height: 80px; margin-right: 35px;">
                                <h5 class="m-0"><?php echo $authorName; ?></h5>
                            </div>

                            <?php if (count($images) > 1): ?>
                                <div id="carousel-<?php echo $postId; ?>" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($images as $index => $image): ?>
                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <img src="<?php echo htmlspecialchars($image); ?>" class="d-block w-100"
                                                    alt="Recipe Image">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <a class="carousel-control-prev" href="#carousel-<?php echo $postId; ?>" role="button"
                                        data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carousel-<?php echo $postId; ?>" role="button"
                                        data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            <?php elseif (count($images) === 1): ?>
                                <img src="<?php echo htmlspecialchars($images[0]); ?>" class="card-img-top" alt="Recipe Image">
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="card-title mb-2"><?php echo $foodName; ?></h5>
                                <p class="card-text mb-1"><strong>Food Type:</strong> <?php echo $foodType; ?></p>
                                <p class="card-text mb-1"><strong>Description:</strong>
                                    <span id="<?php echo $description_id; ?>">
                                        <?php echo strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description; ?>
                                    </span>
                                    <?php if (strlen($description) > 150): ?>
                                        <a href="javascript:void(0);"
                                            onclick="toggleContent('<?php echo $description_id; ?>', this)"
                                            data-full-content="<?php echo htmlspecialchars($description); ?>">See More</a>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text mb-1"><strong>Ingredients:</strong>
                                    <span id="<?php echo $ingredients_id; ?>">
                                        <?php echo strlen($ingredients) > 150 ? substr($ingredients, 0, 150) . '...' : $ingredients; ?>
                                    </span>
                                    <?php if (strlen($ingredients) > 150): ?>
                                        <a href="javascript:void(0);"
                                            onclick="toggleContent('<?php echo $ingredients_id; ?>', this)"
                                            data-full-content="<?php echo htmlspecialchars($ingredients); ?>">See More</a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Mid-page Ad Section -->
                        <?php if ($index % 2 === 1): ?>
                            <div class="text-center mb-4">
                                <!-- Google AdSense Placeholder -->
                                <p>Mid-page Ad Placeholder (e.g., Google AdSense)</p>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No recipes posted yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bottom Ad Section -->
        <div class="text-center mb-4">
            <!-- Google AdSense Placeholder -->
            <p>Bottom Ad Placeholder (e.g., Google AdSense)</p>
        </div>
    </div>

    <script>
        function toggleContent(id, link) {
            var contentElement = document.getElementById(id);
            var fullContent = link.getAttribute('data-full-content');

            if (link.innerHTML === "See More") {
                contentElement.innerHTML = fullContent;
                link.innerHTML = "See Less";
            } else {
                contentElement.innerHTML = fullContent.length > 150 ? fullContent.substring(0, 150) + '...' : fullContent;
                link.innerHTML = "See More";
            }
        }
    </script>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>