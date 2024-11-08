<?php
session_start(); 
require 'vendor/autoload.php'; 
include 'cloudinary_config.php'; 
include 'pdodb.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$user_id = $_SESSION['user_id'];
$food_name = $_POST['food_name'] ?? '';
$description = $_POST['description'] ?? '';
$ingredients = $_POST['ingredients'] ?? '';
$food_type = $_POST['food_type'] ?? '';


$uploadedImages = [];
$publicIds = [];

// Check if the images are set in the request
if (!empty($_FILES['images']['name'][0])) {
    $fileCount = count($_FILES['images']['name']);
    
    for ($i = 0; $i < min(3, $fileCount); $i++) {
        $file = $_FILES['images']['tmp_name'][$i];
        
        try {
            // Upload image to Cloudinary
            $result = $cloudinary->uploadApi()->upload($file, [
                'folder' => 'recipe_images',
                'public_id' => uniqid(), // Generates a unique public ID
            ]);
            
            // Store the secure URL and public ID from Cloudinary
            $uploadedImages[] = $result['secure_url'];
            $publicIds[] = $result['public_id']; // Save the public ID for later use

        } catch (Exception $e) {
            echo "Error uploading image: " . $e->getMessage();
            exit();
        }
    }
}

// Set image variables, defaulting to null if fewer than 3 images are uploaded
$image1_url = $uploadedImages[0] ?? null;
$image2_url = $uploadedImages[1] ?? null;
$image3_url = $uploadedImages[2] ?? null;

$image1_public_id = $publicIds[0] ?? null;
$image2_public_id = $publicIds[1] ?? null;
$image3_public_id = $publicIds[2] ?? null;

try {
    $query = "INSERT INTO posts (user_id, food_name, description, ingredients, food_type, image1_url, image2_url, image3_url, createdAt, image1_public_id, image2_public_id, image3_public_id) 
              VALUES (:user_id, :food_name, :description, :ingredients, :food_type, :image1_url, :image2_url, :image3_url, NOW(), :image1_public_id, :image2_public_id, :image3_public_id)";
    
    $stmt = $pdo->prepare($query);
    
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':food_name', $food_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':ingredients', $ingredients);
    $stmt->bindParam(':food_type', $food_type);
    $stmt->bindParam(':image1_url', $image1_url);
    $stmt->bindParam(':image2_url', $image2_url);
    $stmt->bindParam(':image3_url', $image3_url);
    $stmt->bindParam(':image1_public_id', $image1_public_id);
    $stmt->bindParam(':image2_public_id', $image2_public_id);
    $stmt->bindParam(':image3_public_id', $image3_public_id);

    if ($stmt->execute()) {
        echo "Recipe posted successfully!";
        header("Location: index.php");
        exit();
    } else {
        echo "Error posting recipe: Could not execute query.";
    }
} catch (PDOException $e) {
    echo "Error posting recipe: " . $e->getMessage(); 
}
