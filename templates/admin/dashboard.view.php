<div class="dashboard">
    <a href="<?= route('admin.users.index') ?>" class="dashboard-link">
        <div class="dashboard-card">
            <div>
                <i class="fas fa-users"></i>
                <h3>Users</h3>
            </div>
            <span><?= $users->count() ?></span>
        </div>
    </a>
    <a href="<?= route('admin.orders.index') ?>" class="dashboard-link">
        <div class="dashboard-card">
            <div>
                <i class="fas fa-shopping-cart"></i>
                <h3>Orders</h3>
            </div>
            <span><?= $orders->count() ?></span>
        </div>
    </a>
    <a href="<?= route('admin.products.index') ?>" class="dashboard-link">
        <div class="dashboard-card">
            <div>
                <i class="fas fa-boxes"></i>
                <h3>Products</h3>
            </div>
            <span><?= $products->count() ?></span>
        </div>
    </a>
    <a href="<?= route('admin.categories.index') ?>" class="dashboard-link">
        <div class="dashboard-card">
            <div>
                <i class="fas fa-tags"></i>
                <h3>Categories</h3>
            </div>
            <span><?= $categories->count() ?></span>
        </div>
    </a>
    <!-- Add more statistics here -->
</div>