

<?php $__env->startSection('content'); ?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">


                <!-- /.card-header -->
                <div class="card-body">
                    <!-- Horizontal Form -->
                    <div class="card card-info">
                        <div class="card-header" style="margin-bottom:5px">
                            <h3 class="card-title">Quickbooks Settings Update</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form class="form-horizontal" action="<?php echo e(route('settings.store', ['settings'=> $settings ])); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('POST'); ?>
                            <?php echo $__env->make('flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="ClientID" class="col-sm-2 col-form-label">ClientID</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="ClientID" name="ClientID" value="<?php echo e($settings->ClientID); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ClientSecret" class="col-sm-2 col-form-label">ClientSecret</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="ClientSecret" name="ClientSecret" value="<?php echo e($settings->ClientSecret); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="RedirectURI" class="col-sm-2 col-form-label">Redirect URL</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="RedirectURI" name='RedirectURI'  value="<?php echo e(route('qb.callback', ['user' => auth()->user()->id])); ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="phone2" class="col-sm-2 col-form-label">Others (Optional)</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="phone2" name="others"  value="<?php echo e($settings->others); ?>">
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer text-center">
                                <button type="submit" class="btn btn-info">Update</button>
                            </div>
                            <!-- /.card-footer -->
                        </form>
                    </div>
                    <!-- /.card -->

                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\git\quickBook_panelone\resources\views/settings/edit.blade.php ENDPATH**/ ?>