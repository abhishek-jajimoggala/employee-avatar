<?php

$message = "";
$originalImage = "";
$thumbnailImage = "";

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {

    // Check whether file was uploaded
    if (!isset($_FILES["avatar"]) || $_FILES["avatar"]["error"] !== UPLOAD_ERR_OK) {
        $message = "Please select a valid image.";
    } else {

        $employeeName = trim($_POST["employee_name"] ?? "");

        $file = $_FILES["avatar"];

        $fileName = $file["name"];
        $tmpName = $file["tmp_name"];
        $fileSize = $file["size"];

        // Allowed extensions
        $allowedExtensions = ["jpg", "jpeg", "png"];

        $extension = strtolower(
            pathinfo($fileName, PATHINFO_EXTENSION)
        );

        // Check extension
        if (!in_array($extension, $allowedExtensions)) {

            $message = "Only JPG, JPEG and PNG images are allowed.";

        // Check file size (2 MB)
        } elseif ($fileSize > 2 * 1024 * 1024) {

            $message = "Image size must be less than 2 MB.";

        } else {

            // Check whether the file is actually an image
            $imageInfo = getimagesize($tmpName);

            if ($imageInfo === false) {

                $message = "The uploaded file is not a valid image.";

            } else {

                // Create upload directories
                $uploadDir = __DIR__ . "/uploads/";
                $thumbnailDir = __DIR__ . "/uploads/thumbnails/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                if (!is_dir($thumbnailDir)) {
                    mkdir($thumbnailDir, 0777, true);
                }

                // Generate unique filename
                $newFileName = "employee_" . time() . "_" . uniqid() . "." . $extension;

                $originalPath = $uploadDir . $newFileName;
                $thumbnailPath = $thumbnailDir . $newFileName;

                // Move original image
                if (move_uploaded_file($tmpName, $originalPath)) {

                    $width = $imageInfo[0];
                    $height = $imageInfo[1];

                    // Create source image
                    if ($extension === "jpg" || $extension === "jpeg") {

                        $sourceImage = imagecreatefromjpeg($originalPath);

                    } elseif ($extension === "png") {

                        $sourceImage = imagecreatefrompng($originalPath);
                    }

                    if (!$sourceImage) {

                        unlink($originalPath);
                        $message = "Unable to process the image.";

                    } else {

                        // Thumbnail size
                        $thumbnailWidth = 150;
                        $thumbnailHeight = 150;

                        // Create thumbnail canvas
                        $thumbnail = imagecreatetruecolor(
                            $thumbnailWidth,
                            $thumbnailHeight
                        );

                        // PNG transparency
                        if ($extension === "png") {

                            imagealphablending($thumbnail, false);
                            imagesavealpha($thumbnail, true);

                            $transparent = imagecolorallocatealpha(
                                $thumbnail,
                                255,
                                255,
                                255,
                                127
                            );

                            imagefill(
                                $thumbnail,
                                0,
                                0,
                                $transparent
                            );
                        }

                        // Crop image into square
                        $squareSize = min($width, $height);

                        $sourceX = ($width - $squareSize) / 2;
                        $sourceY = ($height - $squareSize) / 2;

                        // Resize and crop
                        imagecopyresampled(
                            $thumbnail,
                            $sourceImage,
                            0,
                            0,
                            $sourceX,
                            $sourceY,
                            $thumbnailWidth,
                            $thumbnailHeight,
                            $squareSize,
                            $squareSize
                        );

                        // Save thumbnail
                        if ($extension === "jpg" || $extension === "jpeg") {

                            imagejpeg(
                                $thumbnail,
                                $thumbnailPath,
                                90
                            );

                        } elseif ($extension === "png") {

                            imagepng(
                                $thumbnail,
                                $thumbnailPath
                            );
                        }

                        // Free memory
                        imagedestroy($sourceImage);
                        imagedestroy($thumbnail);

                        // Browser paths
                        $originalImage = "uploads/" . $newFileName;
                        $thumbnailImage = "uploads/thumbnails/" . $newFileName;

                        $message = "Employee avatar uploaded successfully!";
                    }
                } else {

                    $message = "Failed to upload the image.";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Employee Avatar Uploader</title>

</head>

<body>

    <h2>Employee Avatar Uploader</h2>

    <?php if ($message !== ""): ?>

        <p>
            <strong>
                <?php echo htmlspecialchars($message); ?>
            </strong>
        </p>

    <?php endif; ?>


    <form method="POST"
          enctype="multipart/form-data">

        <label for="employee_name">
            Employee Name:
        </label>

        <input
            type="text"
            id="employee_name"
            name="employee_name"
            required
        >

        <br><br>


        <label for="avatar">
            Select Employee Avatar:
        </label>

        <input
            type="file"
            id="avatar"
            name="avatar"
            accept=".jpg,.jpeg,.png"
            required
        >

        <br><br>


        <button type="submit">
            Upload Avatar
        </button>

    </form>


    <?php if ($originalImage !== ""): ?>

        <hr>

        <h3>Original Image</h3>

        <img
            src="<?php echo htmlspecialchars($originalImage); ?>"
            alt="Employee Avatar"
            width="300"
        >


        <h3>Generated Thumbnail</h3>

        <img
            src="<?php echo htmlspecialchars($thumbnailImage); ?>"
            alt="Employee Thumbnail"
            width="150"
            height="150"
        >

    <?php endif; ?>

</body>

</html>