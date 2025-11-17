<?php
class PropertyController {
    private $property;
    private $uploadDir;
    public function __construct($model, $uploadDir='uploads') { $this->property = $model; $this->uploadDir = rtrim($uploadDir, '/'); if(!is_dir($this->uploadDir)) mkdir($this->uploadDir, 0755, true); }

    // handle file uploads: expects $_FILES['property_photos'] (multiple)
    private function handleUploads($files, $existingJson='') {
        $uploaded = array();
        // keep existing if provided
        if(!empty($existingJson)) {
            $existing = json_decode($existingJson, true);
            if(is_array($existing)) $uploaded = $existing;
        }
        if(!isset($files) || !isset($files['name'])) return $uploaded;
        $names = $files['name'];
        $tmp   = $files['tmp_name'];
        $errs  = $files['error'];
        $sizes = $files['size'];

        for($i=0;$i<count($names);$i++){
            if($errs[$i] !== UPLOAD_ERR_OK) continue;
            $orig = $names[$i];
            $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
            $allowed = array('jpg','jpeg','png','gif','webp');
            if(!in_array($ext, $allowed)) continue;
            $newName = time().'_'.bin2hex(random_bytes(6)).'.'.$ext;
            $dst = $this->uploadDir . '/' . $newName;
            if(move_uploaded_file($tmp[$i], $dst)) {
                $uploaded[] = $newName;
            }
        }
        return $uploaded;
    }

    public function store() {
        $data = $_POST;
        // handle uploads, returning JSON string of filenames
        $files = isset($_FILES['property_photos']) ? $_FILES['property_photos'] : null;
        $uploaded = $this->handleUploads($files);
        $data['property_photos'] = json_encode($uploaded);
        // set defaults
        $data['status'] = isset($data['status']) ? (int)$data['status'] : 1;
        $this->property->create($data);
        header('Location: index.php');
        exit;
    }

    public function update() {
        $data = $_POST;
        $id = (int)$data['id'];
        // get existing record to preserve existing photos
        $existing = $this->property->getById($id);
        $existing_photos = isset($existing['property_photos']) ? $existing['property_photos'] : '';
        $files = isset($_FILES['property_photos']) ? $_FILES['property_photos'] : null;
        $uploaded = $this->handleUploads($files, $existing_photos);
        $data['property_photos'] = json_encode($uploaded);
        $data['status'] = isset($data['status']) ? (int)$data['status'] : 1;
        $this->property->update($id, $data);
        header('Location: index.php');
        exit;
    }

    public function delete($id) {
        // Optionally remove files - we'll leave files for simplicity
        $this->property->delete($id);
        header('Location: index.php');
        exit;
    }
}
?>