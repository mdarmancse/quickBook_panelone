<?php $__env->startSection('content'); ?>


    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Marchant</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Merchant List</li>
                    </ol>
                </div>

            </div>

        </div>

    </div>


    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Merchant List</h3>
                        </div>
                        <div class="card-body">
                            <!-- Marchant table goes here -->
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $marchants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $marchant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($marchant->name); ?></td>
                                        <td><?php echo e($marchant->email); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('marchants.edit',  $marchant->id)); ?>" class="btn btn-success"><i class="nav-icon fas fa-edit"></i></a>





                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\git\quickBook_panelone\resources\views/marchants/index.blade.php ENDPATH**/ ?>