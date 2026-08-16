# Employee Avatar Uploader with Thumbnail Generation

## 📌 Project Description

Employee Avatar Uploader is a PHP-based web application that allows users to upload an employee profile/avatar image and automatically generate a 150 × 150 pixel thumbnail.

The application validates the uploaded image, stores the original image, and creates a cropped square thumbnail using PHP's GD image-processing extension.

## 🚀 Features

- Employee name input
- Employee avatar image upload
- Supports JPG, JPEG, and PNG images
- Maximum file size of 2 MB
- Image validation
- Automatic unique file name generation
- Original image storage
- Automatic thumbnail generation
- 150 × 150 pixel thumbnail
- Square image cropping
- PNG transparency support
- Simple PHP-based implementation

## 🛠️ Technologies Used

- PHP
- HTML
- XAMPP
- Apache
- PHP GD Extension

## 📂 Project Structure

```text
employee-avatar/
│
├── index.php
├── README.md
├── .gitignore
│
└── uploads/
    ├── .gitkeep
    └── thumbnails/
        └── .gitkeep
