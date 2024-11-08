<?php
session_start();
include './db.php';


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get recipe ID from URL
if (!isset($_GET['id'])) {
    die("Recipe ID not specified.");
}
$recipe_id = $_GET['id'];

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM posts WHERE id = ? AND user_id = ?"; // Ensure the user is the owner of the recipe
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $recipe_id, $user_id); // Use user_id to check ownership
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();
$stmt->close();

if (!$recipe) {
    die("Recipe not found or you do not have permission to edit this recipe.");
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h3 {
            color: #007bff;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-5">

        <form action="update_recipe.php" method="POST" class="form-container">
            <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">

            <h3 class="mb-4 text-center">Edit Recipe</h3>

            <div class="form-group">
                <label for="food_name">Recipe Name</label>
                <input type="text" class="form-control" id="food_name" name="food_name"
                    value="<?php echo htmlspecialchars($recipe['food_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description" style="display: flex; justify-content: space-between;">
                    Description
                    <span id="description-count" style="font-weight: normal; color: red; font-size: 0.9em;">600
                        characters left</span>
                </label>
                <textarea class="form-control" id="description" name="description" rows="3" maxlength="600"
                    oninput="updateCharacterCount('description', 600)"
                    required><?php echo htmlspecialchars($recipe['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="ingredients" style="display: flex; justify-content: space-between;">
                    Ingredients
                    <span id="ingredients-count" style="font-weight: normal; color: red; font-size: 0.9em;">250
                        characters left</span>
                </label>
                <textarea class="form-control" id="ingredients" name="ingredients" rows="3" maxlength="250"
                    oninput="updateCharacterCount('ingredients', 250)"
                    required><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>
            </div>


            <div class="form-group">
                <label for="food_type" style="display: flex; justify-content: space-between;">Food Type <span id="ingredients-count" style="font-weight: normal; color: red; font-size: 0.9em;">Mandatory to choose</span></label>
                <select class="form-control" id="food_type" name="food_type" required>
                    <option value="Vegetarian" <?php echo ($recipe['food_type'] == 'Vegetarian') ? 'selected' : ''; ?>>
                        Vegetarian</option>
                    <option value="Non-Vegetarian" <?php echo ($recipe['food_type'] == 'Non-Vegetarian') ? 'selected' : ''; ?>>Non-Vegetarian</option>
                    <option value="Egg-Based" <?php echo ($recipe['food_type'] == 'Egg-Based') ? 'selected' : ''; ?>>
                        Egg-Based</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Update Recipe</button>
        </form>
    </div>


    <script>
        function updateCharacterCount(field, maxChars) {
            const textarea = document.getElementById(field);
            const countSpan = document.getElementById(`${field}-count`);
            const remaining = maxChars - textarea.value.length;
            countSpan.textContent = `${remaining} characters left`;
        }
        updateCharacterCount('description', 600);
        updateCharacterCount('ingredients', 250);
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>