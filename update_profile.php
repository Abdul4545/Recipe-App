<?php
session_start();
include 'db.php';
require 'vendor/autoload.php'; 
include 'cloudinary_config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$profile_picture_url = null;
$profile_picture_public_id = null;

// Prepare and execute query to fetch current profile picture details
$query = "SELECT profile_picture_url, profile_picture_public_id FROM profile WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_picture_url, $current_public_id);
$stmt->fetch();
$stmt->close();

if (!empty($_FILES['profile_picture']['name'])) {
    $file = $_FILES['profile_picture']['tmp_name'];


    if (!empty($current_public_id)) {
        try {
            $cloudinary->uploadApi()->destroy($current_public_id);
        } catch (Exception $e) {
            echo "Error deleting old profile picture: " . htmlspecialchars($e->getMessage());
            exit();
        }
    }

    try {
        $uploadResult = $cloudinary->uploadApi()->upload($file, [
            'folder' => 'profile_pictures',
            'public_id' => uniqid(), 
        ]);

        $profile_picture_url = $uploadResult['secure_url'];
        $profile_picture_public_id = $uploadResult['public_id'];
    } catch (Exception $e) {
        echo "Error uploading new profile picture: " . htmlspecialchars($e->getMessage());
        exit();
    }
} else {
    $profile_picture_url = $current_picture_url;
    $profile_picture_public_id = $current_public_id;
}

$preferences = isset($_POST['preferences']) ? htmlspecialchars(trim($_POST['preferences'])) : '';
$experience = isset($_POST['experience']) ? htmlspecialchars(trim($_POST['experience'])) : '';
$bio = isset($_POST['bio']) ? htmlspecialchars(trim($_POST['bio'])) : '';

$query = "SELECT user_id FROM profile WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {

    $stmt->close();
    $query = "UPDATE profile SET profile_picture_url = ?, profile_picture_public_id = ?, preferences = ?, experience = ?, bio = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $profile_picture_url, $profile_picture_public_id, $preferences, $experience, $bio, $user_id);
} else {

    $stmt->close();
    $query = "INSERT INTO profile (user_id, profile_picture_url, profile_picture_public_id, preferences, experience, bio) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $user_id, $profile_picture_url, $profile_picture_public_id, $preferences, $experience, $bio);
}

if ($stmt->execute()) {
    echo "Profile updated successfully!";
    header("Location: home.php");
    exit();
} else {
    echo "Error updating profile: " . htmlspecialchars($stmt->error); 
}

$stmt->close();
$conn->close();
?>

