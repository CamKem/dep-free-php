<div id="menu-nav" class="hidden">
    <div class="admin-nav">
        <ul>
            <a href="<?= route('admin.users.index') ?>">
                <li class="nav-links">Users</li>
            </a>
            <a href="<?= route('admin.products.index') ?>">
                <li class="nav-links">Products</li>
            </a>
            <a href="<?= route('admin.orders.index') ?>">
                <li class="nav-links">Orders</li>
            </a>
            <a href="<?= route('admin.categories.index') ?>">
                <li class="nav-links">Categories</li>
            </a>
            <a href="<?= route('admin.roles.index') ?>">
                <li class="nav-links">Roles</li>
            </a>
            <a href="<?= route('logout') ?>">
                <li class="nav-links-logout">Logout</li>
            </a>
        </ul>
    </div>
</div>