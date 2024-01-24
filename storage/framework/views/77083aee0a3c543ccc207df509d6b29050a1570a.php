

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

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Settings</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <a href="<?php echo e($qbauth); ?>" class="btn btn-info">Authenticate Quickbook</a>
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Sandbox/Production</th>
                    <th>Secret Id</th>
                    <th>Token</th>
                    <th>redirect url: </th>
                    <th>Others</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td><?php echo e($settings['baseUrl']); ?></td>
                    <td><?php echo e($settings['ClientID']); ?></td>
                    <td><?php echo e($settings['ClientSecret']); ?></td>
                    <td><?php echo e($settings['RedirectURI']); ?></td>
                    <td><?php echo e($settings['others']); ?></td>
                    <td><a href="<?php echo e(route('settings.edit',['id' => $settings['id'] ])); ?>" class="btn btn-success">Edit</a></td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\quickbooks_panelone\resources\views/settings/index.blade.php ENDPATH**/ ?>