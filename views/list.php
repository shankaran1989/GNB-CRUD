<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Properties</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS (Bootstrap 5 integration) -->
    <link href="https://cdn.jsdelivr.net/npm/datatables.net-dt@1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        .thumb {
            max-width: 100px;
            max-height: 70px;
            margin: 3px;
            object-fit: cover;
            border-radius: 4px;
        }

        td.nowrap {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="m-0">Property List</h2>
            <a class="btn btn-primary" href="views/add.php">Add New</a>
        </div>

        <!-- If you still want server-side search (q param) keep the form, otherwise DataTables has built-in search.
         We'll keep it hidden but available if you want to use it. -->
        <form method="GET" action="index.php" class="mb-3 d-none">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Server search (title, propname or ref)"
                    value="<?= isset($_GET['q']) ? htmlentities($_GET['q']) : '' ?>">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </form>

        <?php if ($data->num_rows == 0): ?>
            <div class="alert alert-warning">No properties found.</div>
        <?php else: ?>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Filter by Status</label>
                    <select id="statusFilter" class="form-select">
                        <option value="">All</option>
                        <option value="LET">LET</option>
                        <option value="Sale">Sale</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table id="propertiesTable" class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Prop Name</th>
                            <th>Ref No</th>
                            <th>Address</th>
                            <th>Bed</th>
                            <th>Bath</th>
                            <th>Photos</th>
                            <th>Desc</th>
                            <th>Status</th>
                            <th class="nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $serial = ($page - 1) * $limit + 1;
                        while ($r = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $serial++ ?></td>
                                <td><?= htmlentities($r['pro_title']) ?></td>
                                <td><?= htmlentities($r['propname']) ?></td>
                                <td><?= htmlentities($r['ref_no']) ?></td>
                                <td><?= htmlentities($r['pro_add']) ?></td>
                                <td><?= (int)$r['bedroom_count'] ?></td>
                                <td><?= (int)$r['bath_room_count'] ?></td>
                                <td>
                                    <?php
                                    $photos = json_decode($r['property_photos'], true) ?: [];
                                    if (count($photos)):
                                        foreach ($photos as $p):
                                    ?>
                                            <a href="uploads/<?= rawurlencode($p) ?>" target="_blank">
                                                <img src="uploads/<?= rawurlencode($p) ?>" class="thumb" alt="photo">
                                            </a>
                                    <?php
                                        endforeach;
                                    else:
                                        echo '<span class="text-muted small">No photos</span>';
                                    endif;
                                    ?>
                                </td>
                                <td><?= htmlentities($r['short_desc']) ?></td>
                                <td>
                                    <?php if ((int)$r['status'] === 1): ?>
                                        <span class="badge bg-success">LET</span>
                                    <?php elseif ((int)$r['status'] === 2): ?>
                                        <span class="badge bg-info">Sale</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Unknown</span>
                                    <?php endif; ?>
                                </td>
                                <td class="nowrap">
                                    <a class="btn btn-sm btn-outline-primary" href="views/edit.php?id=<?= $r['id'] ?>">Edit</a>
                                    <a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?= $r['id'] ?>"
                                        onclick="return confirm('Delete this property?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Prop Name</th>
                            <th>Ref No</th>
                            <th>Address</th>
                            <th>Bed</th>
                            <th>Bath</th>
                            <th>Photos</th>
                            <th>Desc</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Keep server-side pagination links for compatibility (DataTables will handle client side) -->
            <nav aria-label="Page navigation example" class="mt-3">
                <ul class="pagination">
                    <?php
                    $pages = max(1, ceil($total / $limit));
                    $cur = $page;
                    $qparam = isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '';
                    // show previous
                    $prevClass = $cur <= 1 ? 'disabled' : '';
                    ?>
                    <li class="page-item <?= $prevClass ?>">
                        <a class="page-link" href="<?= $cur > 1 ? "index.php?page=" . ($cur - 1) . $qparam : '#' ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item <?= $i == $cur ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?page=<?= $i ?><?= $qparam ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $cur >= $pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $cur < $pages ? "index.php?page=" . ($cur + 1) . $qparam : '#' ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

    </div>

    <!-- jQuery (DataTables depends on it) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#propertiesTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [5, 10, 25, 50, 100],
                "order": [
                    [1, "desc"]
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": [7, 9]
                }],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Quick search..."
                }
            });

            // Status Filter Dropdown
            $('#statusFilter').on('change', function() {
                var val = $(this).val();

                // Apply filter only on the status column (index 9)
                if (val === "") {
                    table.column(9).search("").draw();
                } else {
                    table.column(9).search(val, true, false).draw();
                }
            });
        });
    </script>

</body>

</html>