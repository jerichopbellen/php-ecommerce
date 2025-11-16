<?php
session_start();
include('./includes/header.php');
include('./includes/config.php');

// --- Search handling ---
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($keyword !== '') {
    ?>
    <div class="container my-5">
        <?php
        $like = "%{$keyword}%";
        $search_sql = "SELECT 
                            p.product_id,
                            p.name AS product_name,
                            p.description,
                            p.dimension,
                            b.name AS brand_name,
                            c.name AS category_name,
                            (
                                SELECT pi.img_path 
                                FROM product_images pi 
                                WHERE pi.product_id = p.product_id 
                                ORDER BY pi.image_id ASC
                                LIMIT 1
                            ) AS img_path,
                            (
                                SELECT ROUND(AVG(r.rating), 1)
                                FROM reviews r
                                WHERE r.product_id = p.product_id
                            ) AS average_rating,
                            (
                                SELECT SUM(s.quantity)
                                FROM product_variants v
                                JOIN stocks s ON v.variant_id = s.variant_id
                                WHERE v.product_id = p.product_id
                            ) AS total_stock
                        FROM products p
                        INNER JOIN product_variants v ON p.product_id = v.product_id
                        INNER JOIN brands b ON p.brand_id = b.brand_id
                        INNER JOIN categories c ON p.category_id = c.category_id
                        WHERE (
                            p.name LIKE ? OR 
                            p.description LIKE ? OR 
                            c.name LIKE ? OR 
                            b.name LIKE ?
                        )
                        GROUP BY p.product_id
                        ORDER BY p.product_id ASC";

        $stmt = $conn->prepare($search_sql);
        $stmt->bind_param("ssss", $like, $like, $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();

        $itemCount = $result ? mysqli_num_rows($result) : 0;
        ?>
        <h2 class="text-center mb-4">
            <i class="bi bi-search me-2"></i>Search results for '<?=htmlspecialchars($keyword) ?>' (<?=$itemCount ?>)
        </h2>

        <?php if ($result && $itemCount > 0): ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php
                $price_stmt = $conn->prepare("SELECT MIN(v.price) AS min_price, MAX(v.price) AS max_price FROM product_variants v WHERE v.product_id = ?");

                while ($row = $result->fetch_assoc()):
                    $product_id = intval($row['product_id']);
                    $total_stock = intval($row['total_stock']);
                    $is_out_of_stock = $total_stock <= 0;
                    $min = $max = null;

                    if ($price_stmt) {
                        $price_stmt->bind_param("i", $product_id);
                        $price_stmt->execute();
                        $price_res = $price_stmt->get_result();
                        if ($price_res && $pr = $price_res->fetch_assoc()) {
                            $min = $pr['min_price'] !== null ? (float)$pr['min_price'] : null;
                            $max = $pr['max_price'] !== null ? (float)$pr['max_price'] : null;
                        }
                    }

                    if ($min === null) {
                        $price_display = "Price not available";
                    } elseif ($min == $max) {
                        $price_display = "₱" . number_format($min, 2);
                    } else {
                        $price_display = "₱" . number_format($min, 2) . " - ₱" . number_format($max, 2);
                    }

                    $rating = isset($row['average_rating']) ? round($row['average_rating'], 1) : 0;
                    $img = htmlspecialchars($row['img_path'] ?: 'placeholder.png');
                    $dimension = htmlspecialchars($row['dimension'] ?: 'N/A');
                ?>

                <div class="col">
                    <div class="card h-100 shadow-sm <?=$is_out_of_stock ? 'opacity-50' : '' ?>">
                        <a href="product_details.php?product_id=<?= $product_id ?>" class="text-decoration-none text-dark">
                            <img src="<?= $img ?>" class="card-img-top" alt="" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?=htmlspecialchars($row['product_name']) ?></h5>
                                <p class="card-text text-muted"><?= htmlspecialchars($row['brand_name']) ?> • <?= htmlspecialchars($row['category_name']) ?></p>
                                <p class="card-text"><small class="text-muted"><i class="bi bi-rulers me-1"></i><?= $dimension ?></small></p>
                                <p class="card-text fw-bold"><?= $price_display ?></p>
                                <?php if ($is_out_of_stock): ?>
                                    <span class="badge bg-danger mb-2">Out of Stock</span>
                                <?php endif; ?>
                                <div class="rating mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($rating >= $i): ?>
                                            <i class="bi bi-star-fill text-warning"></i>
                                        <?php elseif ($rating >= $i - 0.5): ?>
                                            <i class="bi bi-star-half text-warning"></i>
                                        <?php else: ?>
                                            <i class="bi bi-star text-secondary"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span class="text-muted ms-1"><?=number_format($rating, 1) ?></span>
                                </div>
                            </div>
                        </a>
                        <div class="card-footer text-center">
                            <form method="POST" action="./cart/add_to_cart.php">
                                <input type="hidden" name="product_id" value="<?=$product_id ?>">
                                <button type="submit" name="submit" class="btn btn-outline-primary w-100" <?=$is_out_of_stock ? 'disabled' : '' ?>>
                                    <?=$is_out_of_stock ? 'Out of Stock' : 'Add to Cart' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <?php endwhile;
                if ($price_stmt) $price_stmt->close();
                ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No products found.</div>
        <?php endif;

        $stmt->close();
        ?>
    </div>
<?php
} else {
    // Get filter parameters
    $selected_category = isset($_GET['category']) && $_GET['category'] !== '' ? intval($_GET['category']) : 0;
    $selected_brand = isset($_GET['brand']) && $_GET['brand'] !== '' ? intval($_GET['brand']) : 0;
    $selected_material = isset($_GET['material']) ? trim($_GET['material']) : '';
    $selected_color = isset($_GET['color']) ? trim($_GET['color']) : '';
    $selected_dimension = isset($_GET['dimension']) ? trim($_GET['dimension']) : '';
    $selected_availability = isset($_GET['availability']) ? $_GET['availability'] : '';
    $min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
    $max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;
    ?>
    <div class="container my-5">
        <h2 class="text-center mb-4"><i class="bi bi-shop-window me-2"></i>Browse Products</h2>

        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="" id="filterForm">
                            <!-- Category Filter -->
                            <div class="mb-3">
                                <label for="category" class="form-label fw-bold"><i class="bi bi-tags me-1"></i>Category</label>
                                <select name="category" id="category" class="form-select form-select-sm">
                                    <option value="">All categories</option>
                                    <?php
                                    $category_sql = "SELECT category_id, name FROM categories ORDER BY name ASC";
                                    $category_result = mysqli_query($conn, $category_sql);
                                    if ($category_result && mysqli_num_rows($category_result) > 0) {
                                        while ($cat = mysqli_fetch_assoc($category_result)) {
                                            $cid = intval($cat['category_id']);
                                            $cname = htmlspecialchars($cat['name']);
                                            $sel = ($cid === $selected_category) ? " selected" : "";
                                            echo "<option value='{$cid}'{$sel}>{$cname}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Brand Filter -->
                            <div class="mb-3">
                                <label for="brand" class="form-label fw-bold"><i class="bi bi-award me-1"></i>Brand</label>
                                <select name="brand" id="brand" class="form-select form-select-sm">
                                    <option value="">All brands</option>
                                    <?php
                                    $brand_sql = "SELECT brand_id, name FROM brands ORDER BY name ASC";
                                    $brand_result = mysqli_query($conn, $brand_sql);
                                    if ($brand_result && mysqli_num_rows($brand_result) > 0) {
                                        while ($brand = mysqli_fetch_assoc($brand_result)) {
                                            $bid = intval($brand['brand_id']);
                                            $bname = htmlspecialchars($brand['name']);
                                            $sel = ($bid === $selected_brand) ? " selected" : "";
                                            echo "<option value='{$bid}'{$sel}>{$bname}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Material Filter -->
                            <div class="mb-3">
                                <label for="material" class="form-label fw-bold"><i class="bi bi-box-seam me-1"></i>Material</label>
                                <select name="material" id="material" class="form-select form-select-sm">
                                    <option value="">All materials</option>
                                    <?php
                                    $material_sql = "SELECT DISTINCT material FROM product_variants WHERE material IS NOT NULL AND material != '' ORDER BY material ASC";
                                    $material_result = mysqli_query($conn, $material_sql);
                                    if ($material_result && mysqli_num_rows($material_result) > 0) {
                                        while ($mat = mysqli_fetch_assoc($material_result)) {
                                            $mname = htmlspecialchars($mat['material']);
                                            $sel = ($mname === $selected_material) ? " selected" : "";
                                            echo "<option value='{$mname}'{$sel}>{$mname}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Color Filter -->
                            <div class="mb-3">
                                <label for="color" class="form-label fw-bold"><i class="bi bi-palette me-1"></i>Color</label>
                                <select name="color" id="color" class="form-select form-select-sm">
                                    <option value="">All colors</option>
                                    <?php
                                    $color_sql = "SELECT DISTINCT color FROM product_variants WHERE color IS NOT NULL AND color != '' ORDER BY color ASC";
                                    $color_result = mysqli_query($conn, $color_sql);
                                    if ($color_result && mysqli_num_rows($color_result) > 0) {
                                        while ($col = mysqli_fetch_assoc($color_result)) {
                                            $cname = htmlspecialchars($col['color']);
                                            $sel = ($cname === $selected_color) ? " selected" : "";
                                            echo "<option value='{$cname}'{$sel}>{$cname}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Dimension Filter -->
                            <div class="mb-3">
                                <label for="dimension" class="form-label fw-bold"><i class="bi bi-rulers me-1"></i>Size/Dimension</label>
                                <select name="dimension" id="dimension" class="form-select form-select-sm">
                                    <option value="">All dimensions</option>
                                    <?php
                                    $dimension_sql = "SELECT DISTINCT dimension FROM products WHERE dimension IS NOT NULL AND dimension != '' ORDER BY dimension ASC";
                                    $dimension_result = mysqli_query($conn, $dimension_sql);
                                    if ($dimension_result && mysqli_num_rows($dimension_result) > 0) {
                                        while ($dim = mysqli_fetch_assoc($dimension_result)) {
                                            $dname = htmlspecialchars($dim['dimension']);
                                            $sel = ($dname === $selected_dimension) ? " selected" : "";
                                            echo "<option value='{$dname}'{$sel}>{$dname}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Price Range Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-bold"><i class="bi bi-currency-dollar me-1"></i>Price Range</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="<?=$min_price !== null ? $min_price : '' ?>" step="0.01" min="0">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="<?=$max_price !== null ? $max_price : '' ?>" step="0.01" min="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Availability Filter -->
                            <div class="mb-3">
                                <label for="availability" class="form-label fw-bold"><i class="bi bi-check-circle me-1"></i>Availability</label>
                                <select name="availability" id="availability" class="form-select form-select-sm">
                                    <option value="">All products</option>
                                    <option value="in_stock" <?=$selected_availability === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
                                    <option value="out_of_stock" <?=$selected_availability === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-2"><i class="bi bi-funnel-fill me-1"></i>Apply Filters</button>
                            <a href="index.php" class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle me-1"></i>Clear Filters</a>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Products Grid -->
            <div class="col-lg-9">
                <?php
                // Build product query with filters
                $where_conditions = [];
                $params = [];
                $param_types = "";

                if ($selected_category > 0) {
                    $where_conditions[] = "p.category_id = ?";
                    $params[] = $selected_category;
                    $param_types .= "i";
                }

                if ($selected_brand > 0) {
                    $where_conditions[] = "p.brand_id = ?";
                    $params[] = $selected_brand;
                    $param_types .= "i";
                }

                if ($selected_material !== '') {
                    $where_conditions[] = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND pv.material = ?)";
                    $params[] = $selected_material;
                    $param_types .= "s";
                }

                if ($selected_color !== '') {
                    $where_conditions[] = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND pv.color = ?)";
                    $params[] = $selected_color;
                    $param_types .= "s";
                }

                if ($selected_dimension !== '') {
                    $where_conditions[] = "p.dimension = ?";
                    $params[] = $selected_dimension;
                    $param_types .= "s";
                }

                if ($min_price !== null || $max_price !== null) {
                    if ($min_price !== null && $max_price !== null) {
                        $where_conditions[] = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND pv.price BETWEEN ? AND ?)";
                        $params[] = $min_price;
                        $params[] = $max_price;
                        $param_types .= "dd";
                    } elseif ($min_price !== null) {
                        $where_conditions[] = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND pv.price >= ?)";
                        $params[] = $min_price;
                        $param_types .= "d";
                    } elseif ($max_price !== null) {
                        $where_conditions[] = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND pv.price <= ?)";
                        $params[] = $max_price;
                        $param_types .= "d";
                    }
                }

                if ($selected_availability === 'in_stock') {
                    $where_conditions[] = "EXISTS (SELECT 1 FROM product_variants v JOIN stocks s ON v.variant_id = s.variant_id WHERE v.product_id = p.product_id AND s.quantity > 0)";
                } elseif ($selected_availability === 'out_of_stock') {
                    $where_conditions[] = "NOT EXISTS (SELECT 1 FROM product_variants v JOIN stocks s ON v.variant_id = s.variant_id WHERE v.product_id = p.product_id AND s.quantity > 0)";
                }

                $where_clause = count($where_conditions) > 0 ? "WHERE " . implode(" AND ", $where_conditions) : "";

                $product_sql = "SELECT 
                                    p.product_id,
                                    p.name AS product_name,
                                    p.description,
                                    p.dimension,
                                    b.name AS brand_name,
                                    c.name AS category_name,
                                    (SELECT pi.img_path 
                                     FROM product_images pi 
                                     WHERE pi.product_id = p.product_id 
                                     ORDER BY pi.image_id ASC
                                     LIMIT 1) AS img_path,
                                    ROUND((SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.product_id), 1) AS average_rating,
                                    (SELECT SUM(s.quantity) FROM product_variants v JOIN stocks s ON v.variant_id = s.variant_id WHERE v.product_id = p.product_id) AS total_stock
                                FROM products p
                                INNER JOIN brands b ON p.brand_id = b.brand_id
                                INNER JOIN categories c ON p.category_id = c.category_id
                                {$where_clause}
                                GROUP BY p.product_id
                                ORDER BY p.product_id ASC";

                $stmt = $conn->prepare($product_sql);
                if (count($params) > 0) {
                    $stmt->bind_param($param_types, ...$params);
                }
                $stmt->execute();
                $product_result = $stmt->get_result();

                if ($product_result && mysqli_num_rows($product_result) > 0):
                    echo "<div class='row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4'>";
                    $price_stmt = $conn->prepare("SELECT MIN(v.price) AS min_price, MAX(v.price) AS max_price FROM product_variants v WHERE v.product_id = ?");

                    while ($row = $product_result->fetch_assoc()):
                        $product_id = intval($row['product_id']);
                        $total_stock = intval($row['total_stock']);
                        $is_out_of_stock = $total_stock <= 0;
                        $min = $max = null;

                        if ($price_stmt) {
                            $price_stmt->bind_param("i", $product_id);
                            $price_stmt->execute();
                            $price_res = $price_stmt->get_result();
                            if ($price_res && $pr = $price_res->fetch_assoc()) {
                                $min = $pr['min_price'] !== null ? (float)$pr['min_price'] : null;
                                $max = $pr['max_price'] !== null ? (float)$pr['max_price'] : null;
                            }
                        }

                        if ($min === null) {
                            $price_display = "Price not available";
                        } elseif ($min == $max) {
                            $price_display = "₱" . number_format($min, 2);
                        } else {
                            $price_display = "₱" . number_format($min, 2) . " - ₱" . number_format($max, 2);
                        }

                        $rating = isset($row['average_rating']) ? round($row['average_rating'], 1) : 0;
                        $img = htmlspecialchars($row['img_path'] ?: 'placeholder.png');
                        $dimension = htmlspecialchars($row['dimension'] ?: 'N/A');
                ?>

                <div class="col">
                    <div class="card h-100 shadow-sm <?=$is_out_of_stock ? 'opacity-50' : '' ?>">
                        <a href="product_details.php?product_id=<?= $product_id ?>" class="text-decoration-none text-dark">
                            <img src="<?=$img ?>" class="card-img-top" alt="" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?=htmlspecialchars($row['product_name']) ?></h5>
                                <p class="card-text text-muted"><?=htmlspecialchars($row['brand_name']) ?> • <?=htmlspecialchars($row['category_name']) ?></p>
                                <p class="card-text"><small class="text-muted"><i class="bi bi-rulers me-1"></i><?=$dimension ?></small></p>
                                <p class="card-text fw-bold"><?=$price_display ?></p>
                                <?php if ($is_out_of_stock): ?>
                                    <span class="badge bg-danger mb-2">Out of Stock</span>
                                <?php endif; ?>
                                <div class="rating mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($rating >= $i): ?>
                                            <i class="bi bi-star-fill text-warning"></i>
                                        <?php elseif ($rating >= $i - 0.5): ?>
                                            <i class="bi bi-star-half text-warning"></i>
                                        <?php else: ?>
                                            <i class="bi bi-star text-secondary"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span class="text-muted ms-1"><?=number_format($rating, 1) ?></span>
                                </div>
                            </div>
                        </a>
                        <div class="card-footer text-center">
                            <form method="POST" action="./cart/add_to_cart.php">
                                <input type="hidden" name="product_id" value="<?=$product_id ?>">   
                                <button type="submit" name="submit" class="btn btn-outline-primary w-100" <?=$is_out_of_stock ? 'disabled' : '' ?>>
                                    <?= $is_out_of_stock ? 'Out of Stock' : 'Add to Cart' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <?php endwhile;
                    if ($price_stmt) $price_stmt->close();
                    echo "</div>";
                else:
                    echo "<div class='alert alert-info text-center'>No products found matching your filters.</div>";
                endif;

                $stmt->close();
                ?>
            </div>
        </div>
    </div>
<?php
}
include('./includes/footer.php');
?>