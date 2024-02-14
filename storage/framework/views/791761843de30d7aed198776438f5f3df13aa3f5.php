

<?php $__env->startSection('content'); ?>

    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Cardknox Settings</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
              <li class="breadcrumb-item active">Cardknox</li>
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
                                  <h3 class="card-title">Cardknox Settings</h3>
                              </div>
                              <!-- /.card-header -->
                              <div class="card-body">
                                  <?php if($settings): ?>
                                      <p><strong>Transaction Key:</strong> <?php echo e($settings['transaction_key']); ?></p>
                                      <p><strong>Ifields Key:</strong> <?php echo e($settings['ifield_key']); ?></p>

                                      <div class="text-center mt-4">
                                          <?php if(isset($settings['id'])): ?>
                                              <a href="<?php echo e(route('cardknox.edit',['id' => $settings['id'] ])); ?>" class="btn btn-success ml-2">Edit</a>
                                          <?php endif; ?>
                                      </div>
                                  <?php else: ?>


                                      <div class="text-center mt-4">
                                          <p>No cardknox settings data available.</p>
                                          <a href="<?php echo e(route('cardknox.edit')); ?>" class="btn btn-success ml-2">Add</a>


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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\git\quickBook_panelone\resources\views/cardknox/index.blade.php ENDPATH**/ ?>