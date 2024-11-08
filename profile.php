<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT u.name, p.profile_picture_url, p.preferences, p.experience, p.bio
          FROM users u 
          LEFT JOIN profile p ON u.id = p.user_id 
          WHERE u.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php' ?>

    <div class="container mt-2">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5" style = "background-color: lavender;">
                <div class="text-center mb-2 mt-4">
                    <img src="<?php echo !empty($user['profile_picture_url']) ? htmlspecialchars($user['profile_picture_url']) : 'uploads/default.jpg'; ?>"
                        alt="<?php echo empty($user['profile_picture_url']) ? 'Profile' : 'Profile Picture'; ?>"
                        class="profile-image">
                </div>

                <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="profile_picture">Change Profile Picture:</label>
                        <input type="file" name="profile_picture" class="form-control" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control"
                            value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Food Preferences</label>
                        <input type="text" name="preferences" class="form-control"
                            value="<?php echo htmlspecialchars($user['preferences']); ?>"
                            placeholder="E.g., Chinese, Italian, Mexican">
                    </div>

                    <div class="form-group">
                        <label>Experience Level</label>
                        <input type="text" name="experience" class="form-control"
                            value="<?php echo htmlspecialchars($user['experience']); ?>"
                            placeholder="Beginner, Intermediate, Expert">
                    </div>

                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" class="form-control"
                            placeholder="Tell us something about yourself"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mb-4">Update Profile</button>
                </form>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> <!-- Use the full version -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>

</html>