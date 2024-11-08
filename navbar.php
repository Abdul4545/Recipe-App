<?php
require '../src/config/pdodb.php'; 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit;
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['name']; 


$sql = "SELECT profile_picture_url FROM profile WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$userProfile = $stmt->fetch(PDO::FETCH_ASSOC);

$profilePictureUrl = !empty($userProfile['profile_picture_url']) ? $userProfile['profile_picture_url'] : 'default_profile_picture.jpg';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe App - Home</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    

    <style>
        .profile-pic {
            width: 45px;
            height: 45px;
            border-radius: 20%;
            object-fit: cover;
        }

        .nav-icon {
            font-size: 1.5rem;
            color: #555;
        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <a class="navbar-brand" href="home.php">Recipe App</a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse m-0" id="navbarNav">
            <ul class="navbar-nav ml-auto align-items-center">
                <li class="nav-item mx-2">
                    <a class="nav-link" href="post.php">
                        <i class="fas fa-plus-circle nav-icon"></i>
                    </a>
                </li>

                <li class="nav-item mx-2">
                    <a class="nav-link" href="#">
                        <i class="fas fa-bell nav-icon"></i>
                    </a>
                </li>

                <li class="nav-item dropdown mx-2">
                    <a class="nav-link dropdown-toggle p-0" href="#" id="profileDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="<?php echo htmlspecialchars($profilePictureUrl); ?>" alt="Profile" class="profile-pic">
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                        <a class="dropdown-item" href="profile.php">Profile</a>
                        <a class="dropdown-item" href="feed.php">My Posts</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>

            </ul>
        </div>
    </nav>

    <script src="https://kit.fontawesome.com/208b63afba.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> <!-- Use the full version -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</body>

</html>
