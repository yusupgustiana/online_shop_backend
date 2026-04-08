<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">Dashboard</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">St</a>
        </div>
<ul class="sidebar-menu">

    <li class="nav-item {{ Request::is('dashboard-ecommerce-dashboard') ? 'active' : '' }}">
        <a href="{{ url('dashboard-ecommerce-dashboard') }}" class="nav-link">
            <i class="fas fa-fire"></i><span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item {{ Request::is('user*') ? 'active' : '' }}">
        <a href="{{ route('admin.user.index') }}" class="nav-link">
            <i class="fas fa-users"></i><span>Users</span>
        </a>
    </li>

    <li class="nav-item {{ Request::is('category*') ? 'active' : '' }}">
        <a href="{{ route('admin.category.index') }}" class="nav-link">
            <i class="fas fa-tags"></i><span>Category</span>
        </a>
    </li>
        <li class="nav-item {{ Request::is('product*') ? 'active' : '' }}">
        <a href="{{ route('admin.product.index') }}" class="nav-link">
            <i class="fas fa-tags"></i><span>Product</span>
        </a>
    </li>

</ul>
 </aside>
</div>
