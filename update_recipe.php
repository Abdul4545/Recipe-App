<?php
include 'pdodb.php'; 

$recipe_id = $_POST['recipe_id'];
$food_name = $_POST['food_name'];
$description = $_POST['description'];
$ingredients = $_POST['ingredients'];
$food_type = $_POST['food_type'];

$query = "UPDATE posts SET food_name = ?, description = ?, ingredients = ?, food_type = ?, updatedAt = NOW() WHERE id = ?";
$stmt = $pdo->prepare($query);

if ($stmt->execute([$food_name, $description, $ingredients, $food_type, $recipe_id])) {
    echo "Recipe updated successfully!";
    header("Location: index.php");
    exit;
} else {
    echo "Error updating recipe.";
}

$stmt->closeCursor();

