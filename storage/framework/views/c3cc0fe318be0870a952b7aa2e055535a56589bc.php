<!-- Navbar -->
<nav class="main-header navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('home')); ?>" class="nav-link">Home</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    <?php echo e(Auth::user()->name); ?>

                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <?php echo e(__('Logout')); ?>

                    </a>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                        <?php echo csrf_field(); ?>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="container">
        <!-- Brand Logo -->
        <a href="<?php echo e(route('home')); ?>" class="brand-link">
            <img src="<?php echo e(asset('admin/dist/img/AdminLTELogo.png')); ?>" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Panelone QB</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="#" class="nav-link <?php echo e($menu == 'home' ? 'active' : ''); ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- QB Settings -->
                    <li class="nav-item has-treeview <?php echo e($menu == 'settings' ? 'menu-is-opening menu-open' : ''); ?>">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                QB Settings
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item ml-2">
                                <a href="<?php echo e(route('settings')); ?>" class="nav-link">
                                    <p>Quickbooks Settings</p>
                                </a>
                            </li>

                            <li class="nav-item ml-2">
                                <a href="<?php echo e(route('cardknox')); ?>" class="nav-link">
                                    <p>Cardknox Settings</p>
                                </a>
                            </li>
                            <li class="nav-item ml-2">
                                <a href="<?php echo e(route('change.password')); ?>" class="nav-link">
                                    <p>Change Password</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Customers Section -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Customers
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item ml-2">
                                <a href="<?php echo e(route('customers.index')); ?>" class="nav-link">
                                    <p>Customer List</p>
                                </a>
                            </li>
                            <li class="nav-item ml-2">
                                <a href="<?php echo e(route('customers.create')); ?>" class="nav-link">
                                    <p>Add Customer</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Products Section -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-box"></i>
                            <p>
                                Products
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item ml-2">
                                <a href="<?php echo e(route('products.index')); ?>" class="nav-link">
                                    <p>Product List</p>
                                </a>
                            </li>
                            <li class="nav-item ml-2">
                                <a href="<?php echo e(route('products.create')); ?>" class="nav-link">
                                    <p>Add Product</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Payment Section -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-money-check-alt"></i>
                            <p>
                                Invoices
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item ml-2">
                                <a href="<?php echo e(route('invoice.index')); ?>" class="nav-link">
                                    <p>Create Invoice</p>
                                </a>
                            </li>
                            <li class="nav-item ml-2">
                                <a href="<?php echo e(route('invoice.invoice-list')); ?>" class="nav-link">
                                    <p>Manage Invoice</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php if(Auth::user()->id == 1): ?>
                        <!-- Merchant Section -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-store"></i>
                                <p>
                                    Merchant
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item ml-2">
                                    <a href="<?php echo e(route('marchants.index')); ?>" class="nav-link">
                                        <p>Merchant List</p>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a href="<?php echo e(route('marchants.create')); ?>" class="nav-link">
                                        <p>Add Merchant</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </div>
</aside>
<?php /**PATH C:\laragon\www\git\quickBook_panelone\resources\views/layouts/left-nav.blade.php ENDPATH**/ ?>