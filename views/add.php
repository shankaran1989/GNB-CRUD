<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Add Property</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Add Property</h2>
            <a href="../index.php" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="POST" action="../store.php" enctype="multipart/form-data" class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="pro_title" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Property Name</label>
                        <input type="text" name="propname" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Reference No</label>
                        <input type="text" name="ref_no" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="pro_add" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bedrooms</label>
                        <input type="number" name="bedroom_count" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bathrooms</label>
                        <input type="number" name="bath_room_count" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Residents</label>
                        <input type="number" name="resp_count" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1">LET</option>
                            <option value="2">SALE</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Photos (multiple allowed)</label>
                        <input type="file" name="property_photos[]" multiple accept="image/*" class="form-control">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="short_desc" rows="4" class="form-control"></textarea>
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary px-4">Save Property</button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>