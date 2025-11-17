<?php
class PropertyController
{
    private $property;
    private $uploadDir;
    public function __construct($model, $uploadDir = 'uploads')
    {
        $this->property = $model;
        $this->uploadDir = rtrim($uploadDir, '/');
        if (!is_dir($this->uploadDir)) mkdir($this->uploadDir, 0755, true);
    }

    // handle file uploads: expects $_FILES['property_photos'] (multiple)

    private function handleUploads(array $files = null, string $existingJson = ''): array
    {
        $uploaded = [];

        // Preserve existing images if provided
        if (!empty($existingJson)) {
            $existing = json_decode($existingJson, true);
            if (is_array($existing)) $uploaded = $existing;
        }

        if (!$files || !isset($files['name']) || !is_array($files['name'])) {
            return $uploaded;
        }

        // Configuration
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        // Use finfo to verify MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        // Normalize arrays
        $names = $files['name'];
        $tmp   = $files['tmp_name'];
        $errs  = $files['error'];
        $sizes = $files['size'];

        for ($i = 0; $i < count($names); $i++) {
            if (!isset($names[$i]) || $errs[$i] !== UPLOAD_ERR_OK) {
                continue; // skip errors
            }

            // basic checks
            if ($sizes[$i] <= 0 || $sizes[$i] > $maxFileSize) {
                continue; // skip too large or empty
            }

            // extension check (lowercase)
            $orig = $names[$i];
            $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt, true)) {
                continue;
            }

            // MIME check
            $mime = finfo_file($finfo, $tmp[$i]);
            if (!in_array($mime, $allowedMime, true)) {
                continue;
            }

            // generate safe filename
            try {
                $newName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            } catch (Exception $e) {
                // fallback
                $newName = time() . '_' . bin2hex(openssl_random_pseudo_bytes(8)) . '.' . $ext;
            }

            $dst = $this->uploadDir . '/' . $newName;

            // move file
            if (move_uploaded_file($tmp[$i], $dst)) {
                // Optionally set file permissions
                @chmod($dst, 0644);
                $uploaded[] = $newName;
            }
        }

        finfo_close($finfo);
        return $uploaded;
    }

    public function store()
    {
        // Basic server-side validation + sanitization
        // Note: use Sanitizer::cleanString for text inputs.
        require_once __DIR__ . '/../helpers/Sanitizer.php'; // adjust path if needed

        $data = [];

        // sanitize text fields (limit lengths to avoid DB overflow)
        $data['pro_title'] = isset($_POST['pro_title']) ? Sanitizer::cleanString($_POST['pro_title'], 255) : '';
        $data['pro_add']   = isset($_POST['pro_add']) ? Sanitizer::cleanString($_POST['pro_add'], 500) : '';
        $data['short_desc'] = isset($_POST['short_desc']) ? Sanitizer::cleanString($_POST['short_desc'], 2000) : '';
        $data['propname']  = isset($_POST['propname']) ? Sanitizer::cleanString($_POST['propname'], 255) : '';
        $data['ref_no']    = isset($_POST['ref_no']) ? Sanitizer::cleanString($_POST['ref_no'], 100) : '';

        // numeric fields (cast safely)
        $data['bedroom_count']   = isset($_POST['bedroom_count']) ? Sanitizer::cleanNumber($_POST['bedroom_count']) : 0;
        $data['bath_room_count'] = isset($_POST['bath_room_count']) ? Sanitizer::cleanNumber($_POST['bath_room_count']) : 0;
        $data['resp_count']      = isset($_POST['resp_count']) ? Sanitizer::cleanNumber($_POST['resp_count']) : 0;
        $data['status']          = isset($_POST['status']) ? Sanitizer::cleanNumber($_POST['status']) : 1;

        // File uploads
        $files = isset($_FILES['property_photos']) ? $_FILES['property_photos'] : null;

        // Use secure uploader (this will return array of filenames)
        $uploaded = $this->handleUploads($files);

        // store JSON (even if empty array)
        $data['property_photos'] = json_encode(array_values($uploaded));

        // optional: simple server-side required checks
        if (empty($data['pro_title'])) {
            // handle error gracefully; for now redirect back with a message (you can improve)
            header('Location: views/add.php?error=' . urlencode('Title is required'));
            exit;
        }

        // Create the property (model uses prepared statements)
        $this->property->create($data);

        // redirect back to list
        header('Location: index.php');
        exit;
    }

    public function update()
    {
        require_once __DIR__ . '/../helpers/Sanitizer.php'; // load sanitizer

        // Must exist
        if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            header('Location: index.php?error=' . urlencode('Invalid property ID'));
            exit;
        }

        $id = (int) $_POST['id'];

        // Sanitize incoming input safely
        $data = [];

        // Text fields
        $data['pro_title'] = isset($_POST['pro_title']) ? Sanitizer::cleanString($_POST['pro_title'], 255) : '';
        $data['pro_add']   = isset($_POST['pro_add']) ? Sanitizer::cleanString($_POST['pro_add'], 500) : '';
        $data['short_desc'] = isset($_POST['short_desc']) ? Sanitizer::cleanString($_POST['short_desc'], 2000) : '';
        $data['propname']  = isset($_POST['propname']) ? Sanitizer::cleanString($_POST['propname'], 255) : '';
        $data['ref_no']    = isset($_POST['ref_no']) ? Sanitizer::cleanString($_POST['ref_no'], 100) : '';

        // Numeric fields
        $data['bedroom_count']   = isset($_POST['bedroom_count']) ? Sanitizer::cleanNumber($_POST['bedroom_count']) : 0;
        $data['bath_room_count'] = isset($_POST['bath_room_count']) ? Sanitizer::cleanNumber($_POST['bath_room_count']) : 0;
        $data['resp_count']      = isset($_POST['resp_count']) ? Sanitizer::cleanNumber($_POST['resp_count']) : 0;
        $data['status']          = isset($_POST['status']) ? Sanitizer::cleanNumber($_POST['status']) : 1;

        // Get existing property record
        $existing = $this->property->getById($id);
        $existing_photos = !empty($existing['property_photos']) ? $existing['property_photos'] : '';

        // New uploaded files
        $files = isset($_FILES['property_photos']) ? $_FILES['property_photos'] : null;

        // This combines old + newly uploaded images securely
        $uploaded = $this->handleUploads($files, $existing_photos);

        // Store JSON (secure array re-indexing)
        $data['property_photos'] = json_encode(array_values($uploaded));

        // Validate required fields
        if (empty($data['pro_title'])) {
            header('Location: views/edit.php?id=' . $id . '&error=' . urlencode('Title is required'));
            exit;
        }

        // Update the record (Model uses prepared statements → SQL injection safe)
        $this->property->update($id, $data);

        header('Location: index.php?updated=1');
        exit;
    }


    public function delete($id)
    {
        // Optionally remove files - we'll leave files for simplicity
        $this->property->delete($id);
        header('Location: index.php');
        exit;
    }
}
