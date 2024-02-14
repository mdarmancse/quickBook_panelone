

<?php $__env->startSection('content'); ?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Cardknox Setting</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li class="breadcrumb-item active">Cardknox Setting</li>
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
            <div class="col-lg-8">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Cardknox Settings Update</h3>
                    </div>

                    <form class="form-horizontal" action="<?php echo e($settings ? route('settings.cardknoxStore', ['id' => $settings->id]) : route('settings.cardknoxStore')); ?>" method="POST">                        <?php echo csrf_field(); ?>
                        <?php echo method_field('POST'); ?>
                        <?php echo $__env->make('flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                        <div class="card-body">

                            <div class="form-group row">
                                <label for="transaction_key" class="col-sm-3 col-form-label">Transaction Key</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="transaction_key" name='transaction_key' value="<?php echo e($settings ? $settings->transaction_key : null); ?>" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ifield_key" class="col-sm-3 col-form-label">Ifield Key</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="ifield_key" name='ifield_key' value="<?php echo e($settings ? $settings->ifield_key : null); ?>" >
                                </div>
                            </div>

                            <!-- Add more fields as needed -->

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

<!-- /.content -->

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\git\quickBook_panelone\resources\views/cardknox/edit.blade.php ENDPATH**/ ?>