<!doctype html>
<html>
<head><title>Properties</title></head>
<body>
<h2>Property List</h2>
<p><a href="views/add.php">Add New</a></p>

<form method="GET" action="index.php" style="margin-bottom:15px;">
    <input type="text" name="q" placeholder="Search title, propname or ref" value="<?= isset($_GET['q'])?htmlentities($_GET['q']):'' ?>">
    <button type="submit">Search</button>
</form>

<?php if($data->num_rows==0): ?>
    <p>No properties found.</p>
<?php else: ?>
    <?php while($r=$data->fetch_assoc()): ?>
        <div style="border:1px solid #ccc;padding:10px;margin-bottom:8px;">
            <strong><?= htmlentities($r['pro_title']) ?></strong> (<?= $r['propname'] ?>) - Ref: <?= $r['ref_no'] ?><br>
            <?= htmlentities($r['pro_add']) ?><br>
            Bedrooms: <?= $r['bedroom_count'] ?> | Baths: <?= $r['bath_room_count'] ?><br>
            <?php $photos = json_decode($r['property_photos'], true) ?: []; if(count($photos)): ?>
                <?php foreach($photos as $p): ?>
                    <img src="uploads/<?= $p ?>" style="max-width:100px;max-height:70px;margin:3px;" alt="photo">
                <?php endforeach; ?>
            <?php endif; ?>
            <br>
            <a href="views/edit.php?id=<?= $r['id'] ?>">Edit</a> | <a href="delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
        </div>
    <?php endwhile; ?>

    <!-- Pagination -->
    <div style="margin-top:15px;">
    <?php
        $pages = ceil($total/$limit);
        $cur = $page;
        $qparam = isset($_GET['q']) ? '&q='.urlencode($_GET['q']) : '';
        for($i=1;$i<=$pages;$i++){
            if($i==$cur) echo "<strong>$i</strong> ";
            else echo "<a href='index.php?page=$i$qparam'>$i</a> ";
        }
    ?>
    </div>
<?php endif; ?>

</body>
</html>