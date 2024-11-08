<?php
include './config.php';
include './cloudinary_config.php';
include './pdodb.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $recipeId = $data['id'] ?? null;

    if ($recipeId) {
        // Fetch public IDs of images for deletion
        $stmt = $pdo->prepare("SELECT image1_public_id, image2_public_id, image3_public_id FROM posts WHERE id = ?");
        $stmt->execute([$recipeId]);
        $recipe = $stmt->fetch();

        if ($recipe) {
            // Delete recipe from database
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
            if ($stmt->execute([$recipeId])) {
                try {
                    // Initialize Cloudinary with constants from config.php
                    $cloudinary = new \Cloudinary\Cloudinary([
                        'cloud' => [
                            'cloud_name' => CLOUDINARY_CLOUD_NAME,
                            'api_key'    => CLOUDINARY_API_KEY,
                            'api_secret' => CLOUDINARY_API_SECRET,
                        ],
                        'url' => ['secure' => true]
                    ]);

                    // Delete images from Cloudinary if public IDs exist
                    if (!empty($recipe['image1_public_id'])) {
                        $cloudinary->uploadApi()->destroy($recipe['image1_public_id'], ["invalidate" => true]);
                    }
                    if (!empty($recipe['image2_public_id'])) {
                        $cloudinary->uploadApi()->destroy($recipe['image2_public_id'], ["invalidate" => true]);
                    }
                    if (!empty($recipe['image3_public_id'])) {
                        $cloudinary->uploadApi()->destroy($recipe['image3_public_id'], ["invalidate" => true]);
                    }

                    echo json_encode(['success' => true, 'message' => 'Recipe and images deleted successfully.']);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Images not deleted from Cloudinary: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete recipe from database.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Recipe not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid recipe ID.']);
    }
}
