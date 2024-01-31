<?php $__env->startSection('content'); ?>

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Product</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('products.index')); ?>">Products</a></li>
                        <li class="breadcrumb-item active">Edit Product</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Edit Product</h3>
                        </div>

                        <form action="<?php echo e(route('products.update', ['id' => $product->id])); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <?php echo $__env->make('qb-flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="name" class="col-sm-3 col-form-label">Item Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="name" id="name" value="<?php echo e($product->Name); ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="description" class="col-sm-3 col-form-label">Description</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="description" name="description"><?php echo e($product->Description); ?></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="unit_price" class="col-sm-3 col-form-label">Unit Price</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" name="unit_price" id="unit_price" value="<?php echo e($product->UnitPrice); ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="type" class="col-sm-3 col-form-label">Posting Type</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" required="" id="type" name="type">
                                            <option value="Service" <?php echo e($product->Type == 'Service' ? 'selected' : ''); ?>>Service</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Add other fields here based on your schema -->

                                <input type="hidden" value="<?php echo e($product->ItemId); ?>" id="ItemId" name="ItemId">
                            </div>

                            <div class="card-footer text-center">
                                <button type="submit" class="btn btn-info">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\git\quickBook_panelone\resources\views/products/edit.blade.php ENDPATH**/ ?>