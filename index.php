<?php
// index.php
function fetch_products($url = 'https://fakestoreapi.com/products') {
    $opts = ["http" => ["method" => "GET","timeout" => 5]];
    $context = stream_context_create($opts);
    $json = @file_get_contents($url, false, $context);
    if ($json === false && function_exists('curl_version')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $json = curl_exec($ch);
        curl_close($ch);
    }
    if ($json === false || !$json) return [];
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

$products = fetch_products();
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q !== '') {
    $qLower = mb_strtolower($q);
    $products = array_values(array_filter($products, function($p) use ($qLower) {
        return (strpos(mb_strtolower($p['title']), $qLower) !== false)
            || (strpos(mb_strtolower($p['category']), $qLower) !== false);
    }));
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Product Listing — Assignment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">
  <header class="py-3 bg-white shadow-sm">
    <div class="container d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <div class="logo fs-4 fw-bold">Logo</div>
        <nav class="d-none d-md-block">
          <a href="#" class="nav-link d-inline-block">Shop</a>
          <a href="#" class="nav-link d-inline-block">About</a>
          <a href="#" class="nav-link d-inline-block">Giving</a>
        </nav>
      </div>
      <form class="d-flex ms-3" method="get" role="search">
        <input class="form-control form-control-sm" type="search" placeholder="Search products..." name="q" value="<?php echo htmlspecialchars($q); ?>">
      </form>
      <div class="ms-3">Cart (0)</div>
    </div>
  </header>
  <main class="container my-4">
    <div class="hero p-5 rounded-3 mb-4">
      <h1 class="display-6 text-center mb-0">Shop environment<br>friendly quality goods</h1>
    </div>
    <div class="d-flex align-items-center justify-content-between mb-3">
      <ul class="nav nav-pills">
        <li class="nav-item"><a class="nav-link active" href="#">All Products</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Bamboo</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Plant-based</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Toothpastes</a></li>
      </ul>
      <div>
        <button class="btn btn-outline-secondary btn-sm me-2" disabled>Sort by</button>
        <button class="btn btn-outline-secondary btn-sm">Filters</button>
      </div>
    </div>
    <div class="row g-4">
      <?php if (empty($products)): ?>
        <div class="col-12"><div class="alert alert-warning">No products available.</div></div>
      <?php else: foreach ($products as $p): ?>
        <?php
          $title = htmlspecialchars($p['title'] ?? 'Untitled');
          $price = isset($p['price']) ? number_format((float)$p['price'], 2) : '0.00';
          $category = htmlspecialchars($p['category'] ?? '');
          $image = htmlspecialchars($p['image'] ?? '');
        ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <div class="card product-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <div class="image-wrap mb-2">
                  <?php if ($image): ?>
                    <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" class="product-img">
                  <?php else: ?><div class="placeholder-img">Product image</div><?php endif; ?>
                </div>
                <div class="badge bg-light text-dark small">Add to Cart</div>
              </div>
              <h6 class="product-title mt-auto"><?php echo $title; ?></h6>
              <div class="d-flex align-items-center justify-content-between mt-2">
                <small class="text-muted"><?php echo $category; ?></small>
                <div class="fs-6 fw-semibold">$<?php echo $price; ?></div>
              </div>
              <div class="mt-3"><button class="btn btn-success btn-sm w-100" disabled>+ Add to Cart</button></div>
            </div>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </main>
  <footer class="py-4 bg-white mt-5">
    <div class="container text-center text-muted small">
      Built for screening assignment • Demo layout using Fake Store API
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>