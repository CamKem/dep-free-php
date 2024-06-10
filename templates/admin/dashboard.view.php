<div class="dashboard">
    <div class="statistic">
        <h3>Users</h3>
        <p id="userCount"><?= $users->count() ?></p>
    </div>
    <div class="statistic">
        <h3>Orders</h3>
        <p id="orderCount"><?= $orders->count() ?></p>
    </div>
    <div class="statistic">
        <h3>Products</h3>
        <p id="productCount"><?= $products->count() ?></p>
    </div>
    <div class="statistic">
        <h3>Categories</h3>
        <p id="categoryCount"><?= session()->get('categories')->count() ?></p>
    </div>
    <!-- Add more statistics here -->
</div>