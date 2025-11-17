<?php
include '../db/Database.php';
include '../models/Property.php';
$db = new Database();
$prop = new Property($db->conn);
$edit = $prop->getById($_GET['id']);
$photos = [];
if (!empty($edit['property_photos'])) $photos = json_decode($edit['property_photos'], true) ?: [];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit Property</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .thumb {
            max-width: 120px;
            max-height: 80px;
            margin: 5px;
            object-fit: cover;
            border-radius: 6px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Edit Property</h2>
            <a href="../index.php" class="btn btn-secondary">Back to list</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="../update.php" enctype="multipart/form-data" class="row g-3">
                    <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">

                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input class="form-control" type="text" name="pro_title" required value="<?= htmlentities($edit['pro_title']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Property Name</label>
                        <input class="form-control" type="text" name="propname" value="<?= htmlentities($edit['propname']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Reference No</label>
                        <input class="form-control" type="text" name="ref_no" value="<?= htmlentities($edit['ref_no']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input class="form-control" type="text" name="pro_add" value="<?= htmlentities($edit['pro_add']) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bedrooms</label>
                        <input class="form-control" type="number" name="bedroom_count" value="<?= (int)$edit['bedroom_count'] ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bathrooms</label>
                        <input class="form-control" type="number" name="bath_room_count" value="<?= (int)$edit['bath_room_count'] ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Residents</label>
                        <input class="form-control" type="number" name="resp_count" value="<?= (int)$edit['resp_count'] ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Existing Photos</label>
                        <div class="d-flex flex-wrap align-items-start">
                            <?php if (count($photos)): foreach ($photos as $p): ?>
                                    <a href="../uploads/<?= rawurlencode($p) ?>" target="_blank" class="me-2">
                                        <img src="../uploads/<?= rawurlencode($p) ?>" alt="photo" class="thumb border">
                                    </a>
                                <?php endforeach;
                            else: ?>
                                <div class="text-muted">No photos</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Upload additional Photos (will be appended)</label>
                        <input class="form-control" type="file" name="property_photos[]" multiple accept="image/*">
                        <div class="form-text">Allowed: jpg, jpeg, png, gif, webp.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="short_desc" rows="4"><?= htmlentities($edit['short_desc']) ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" <?= ((int)$edit['status'] === 1) ? 'selected' : '' ?>>LET</option>
                            <option value="2" <?= ((int)$edit['status'] === 2) ? 'selected' : '' ?>>SALE</option>
                            <option value="0" <?= ((int)$edit['status'] !== 1 && (int)$edit['status'] !== 2) ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-primary px-4" type="submit">Update Property</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>