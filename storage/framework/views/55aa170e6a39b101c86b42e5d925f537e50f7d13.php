

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
              <div class="container">
                  <div class="row justify-content-center">
                      <div class="col-md-6">
                          <div class="card">
                              <div class="card-header">
                                  <h3 class="card-title">Settings</h3>
                              </div>
                              <!-- /.card-header -->
                              <div class="card-body">
                                  <?php if($settings): ?>
                                      <p><strong>Sandbox/Production:</strong> <?php echo e($settings['baseUrl']); ?></p>
                                      <p><strong>Secret Id:</strong> <?php echo e($settings['ClientID']); ?></p>
                                      <p><strong>Token:</strong> <?php echo e($settings['ClientSecret']); ?></p>
                                      <p><strong>Redirect URL:</strong> <?php echo e($settings['RedirectURI']); ?></p>
                                      <p><strong>Others:</strong> <?php echo e($settings['others']); ?></p>
                                      <div class="text-center mt-4">
                                          <a href="<?php echo e($qbauth); ?>" class="btn btn-info" target="_blank">Authenticate Quickbook</a>
                                          <?php if(isset($settings['id'])): ?>
                                              <a href="<?php echo e(route('settings.edit',['id' => $settings['id'] ])); ?>" class="btn btn-success ml-2">Edit</a>
                                          <?php endif; ?>
                                      </div>
                                  <?php else: ?>


                                      <div class="text-center mt-4">
                                          <p>No settings data available.</p>
                                          <a href="<?php echo e($qbauth); ?>" class="btn btn-info" target="_blank">Authenticate Quickbook</a>
                                          <a href="<?php echo e(route('settings.edit')); ?>" class="btn btn-success ml-2">Add</a>


                                      </div>

                                  <?php endif; ?>
                              </div>
                              <!-- /.card-body -->
                          </div>
                      </div>
                  </div>
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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\git\quickBook_panelone\resources\views/settings/index.blade.php ENDPATH**/ ?>