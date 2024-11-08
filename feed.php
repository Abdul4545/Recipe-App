<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT id, food_name, description, ingredients, food_type, image1_url, image2_url, image3_url FROM posts WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - My Recipes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .carousel-item img {
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php' ?>
    <div class="container">
        <h2>My Recipes</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $description = htmlspecialchars($row['description']);
                $ingredients = htmlspecialchars($row['ingredients']);
                $food_type = htmlspecialchars($row['food_type']);
                $images = array_filter([$row['image1_url'], $row['image2_url'], $row['image3_url']]);
                $description_id = 'description_content_' . $row['id'];
                $ingredients_id = 'ingredients_content_' . $row['id'];
                ?>
                <div class="card mb-3" style="background-color: lavender;">
                    <div class="row no-gutters">
                        <div class="col-md-4">
                            <?php if (count($images) > 1): ?>
                                <div id="carousel-<?php echo $row['id']; ?>" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($images as $index => $image): ?>
                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <img src="<?php echo $image; ?>" class="d-block w-100"
                                                    style="width: 100%; height: 250px; object-fit: cover;" alt="Recipe Image">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <a class="carousel-control-prev" href="#carousel-<?php echo $row['id']; ?>" role="button"
                                        data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carousel-<?php echo $row['id']; ?>" role="button"
                                        data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            <?php elseif (count($images) === 1): ?>
                                <img src="<?php echo $images[0]; ?>" class="card-img" alt="Recipe Image"
                                    style="width: 100%; height: 250px; object-fit: cover;">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-2"><?php echo htmlspecialchars($row['food_name']); ?></h5>
                                    <div>
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="text-primary mr-2"
                                            title="Update">
                                            <i class="fas fa-edit fa-lg"></i>
                                        </a>
                                        <a href="#" class="text-warning" title="Delete"
                                            onclick="deleteRecipe(event, <?php echo $row['id']; ?>)">
                                            <i class="fas fa-trash fa-lg"></i>
                                        </a>
                                    </div>
                                </div>
                                <p class="card-text mb-1"><strong>Food Type:</strong>
                                    <?php echo htmlspecialchars($food_type); ?></p>

                                <p class="card-text mb-1">
                                    <strong>Description:</strong>
                                    <span id="<?php echo $description_id; ?>">
                                        <?php echo htmlspecialchars(strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description); ?>
                                    </span>
                                    <?php if (strlen($description) > 100): ?>
                                        <a href="javascript:void(0);"
                                            onclick="toggleContent('<?php echo $description_id; ?>', this)"
                                            data-full-content="<?php echo htmlspecialchars($description); ?>">See More</a>
                                    <?php endif; ?>
                                </p>

                                <p class="card-text mb-1">
                                    <strong>Ingredients:</strong>
                                    <span id="<?php echo $ingredients_id; ?>">
                                        <?php echo htmlspecialchars(strlen($ingredients) > 100 ? substr($ingredients, 0, 100) . '...' : $ingredients); ?>
                                    </span>
                                    <?php if (strlen($ingredients) > 100): ?>
                                        <a href="javascript:void(0);"
                                            onclick="toggleContent('<?php echo $ingredients_id; ?>', this)"
                                            data-full-content="<?php echo htmlspecialchars($ingredients); ?>">See More</a>
                                    <?php endif; ?>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            No recipes posted yet, Contribute in posting new Recipes.
        <?php endif; ?>

        <?php $stmt = null; $conn = null; ?>
    </div>

    <script>
        function deleteRecipe(event, recipeId) {
            event.preventDefault();
            if (confirm('Are you sure you want to delete this recipe?')) {
                fetch('delete_recipe.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: recipeId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Recipe deleted successfully.');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('There was a problem with the delete operation. Please try again later.');
                    });
            }
        }

        function toggleContent(contentId, linkElement) {
            const contentElement = document.getElementById(contentId);
            const fullContent = linkElement.getAttribute('data-full-content');

            if (linkElement.textContent === "See More") {
                contentElement.textContent = fullContent;
                linkElement.textContent = "See Less";
            } else {
                contentElement.textContent = fullContent.substring(0, 100) + "...";
                linkElement.textContent = "See More";
            }
        }


    </script>

    <script src="https://kit.fontawesome.com/208b63afba.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>