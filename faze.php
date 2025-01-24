<?php
// Set the directory you want to access
$directory = './';

// Check if the user has submitted the form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file actions
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $filename = $_POST['filename'];
        $path = $directory . $filename;

        switch ($action) {
            case 'edit':
                if (isset($_POST['content'])) {
                    $content = $_POST['content'];
                    file_put_contents($path, $content);
                    echo "<div class='alert alert-success'>File '$filename' has been updated.</div>";
                }
                break;
            case 'delete':
                if (file_exists($path)) {
                    unlink($path);
                    echo "<div class='alert alert-danger'>File '$filename' has been deleted.</div>";
                }
                break;
            case 'download':
                if (file_exists($path)) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    readfile($path);
                    exit;
                }
                break;
            case 'upload':
                if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
                    $uploadedFile = $_FILES['file'];
                    $uploadedFilename = $uploadedFile['name'];
                    $uploadedFilePath = $directory . $uploadedFilename;
                    if (move_uploaded_file($uploadedFile['tmp_name'], $uploadedFilePath)) {
                        echo "<div class='alert alert-success'>File '$uploadedFilename' has been uploaded.</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error uploading file '$uploadedFilename'.</div>";
                    }
                }
                break;
        }
    }
}

// Get the list of files in the directory
$files = scandir($directory);
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Manager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">File Manager</h1>

        <h2 class="mb-3">Files in the directory:</h2>
        <div class="list-group">
            <?php foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    echo "<a href='?file=$file' class='list-group-item list-group-item-action'>" . $file . "</a>";
                }
            } ?>
        </div>

        <div class="mt-5">
            <h2 class="mb-3">Upload a File</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload">
                <div class="form-group">
                    <input type="file" name="file" class="form-control-file">
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>

        <?php if (isset($_GET['file'])) {
            $file = $_GET['file'];
            $path = $directory . $file;
            $content = file_get_contents($path);
        ?>
            <div class="mt-5">
                <h2 class="mb-3">Edit File: <?php echo $file; ?></h2>
                <form method="post">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="filename" value="<?php echo $file; ?>">
                    <div class="form-group">
                        <textarea name="content" rows="10" class="form-control"><?php echo $content; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="?file=<?php echo $file; ?>&action=download" class="btn btn-secondary">Download</a>
                    <button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
                </form>
            </div>
        <?php } ?>
    </div>
</body>
</html>
