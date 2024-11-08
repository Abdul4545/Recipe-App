<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Recipe</title>

    <!-- Latest Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css">

    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            z-index: 1;
            position: relative;
        }
    </style>
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    include 'navbar.php';
    ?>

    <div class="container mt-5">
        <div class="form-container">
            <h2 class="text-center mb-4">Submit Your Recipe</h2>
            <form action="submit_post.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="food_name">Recipe Name</label>
                    <input type="text" class="form-control" name="food_name" required />
                </div>

                <div class="form-group">
                    <label for="description" style="display: flex; justify-content: space-between;">
                        Description
                        <span id="description-count" style="font-weight: normal; color: red; font-size: 0.9em; margin-left: auto;">
                            600 characters left
                        </span>
                    </label>
                    <textarea class="form-control" name="description" rows="3" maxlength="600" oninput="updateCharacterCount('description', 600)" required></textarea>
                </div>

                <div class="form-group">
                    <label for="ingredients" style="display: flex; justify-content: space-between;">
                        Ingredients
                        <span id="ingredients-count" style="font-weight: normal; color: red; font-size: 0.9em; margin-left: auto;">
                            250 characters left
                        </span>
                    </label>
                    <textarea class="form-control" name="ingredients" rows="3" maxlength="250" oninput="updateCharacterCount('ingredients', 250)" required></textarea>
                </div>

                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="food_type">Food Type</label>
                        <select class="form-control" name="food_type" required>
                            <option value="" disabled selected>Select food type</option>
                            <option value="vegetarian">Vegetarian</option>
                            <option value="non-vegetarian">Non-Vegetarian</option>
                            <option value="egg-based">Egg-Based</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="images">Upload Images (max 3):</label>
                        <input type="file" class="form-control-file" name="images[]" accept="image/*" multiple required onchange="showImageNames(this)">
                        <small class="form-text text-muted" id="imageNames"></small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Submit Recipe</button>
            </form>
        </div>
    </div>

    <!-- jQuery and Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>

    <script>
        function updateCharacterCount(field, maxChars) {
            const textarea = document.querySelector(`textarea[name="${field}"]`);
            const countSpan = document.getElementById(`${field}-count`);
            const remaining = maxChars - textarea.value.length;
            countSpan.textContent = `${remaining} characters left`;
        }

        function showImageNames(input) {
            const imageNamesElement = document.getElementById('imageNames');
            const files = input.files;
            let names = [];

            for (let i = 0; i < files.length; i++) {
                names.push(files[i].name);
            }

            if (names.length > 0) {
                imageNamesElement.textContent = 'Selected images: ' + names.join(', ');
            } else {
                imageNamesElement.textContent = '';
            }
        }
    </script>
</body>

</html>
