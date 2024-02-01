<?php $__env->startSection('content'); ?>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Invoices</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Invoice List</li>
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mr-auto">Manage Invoice</h3>

                            <a href="/payment-requests" id="createUrl" class="btn btn-info ">Add Payment Request</a>
                        </div>
                        <?php if(session('success')): ?>
                            <div class="alert alert-success">
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <!-- Invoice table goes here -->
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Total Before Discount</th>
                                    <th>Total After Discount</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($invoice->customer->name); ?></td>
                                        <td><?php echo e($invoice->total_before_discount); ?></td>
                                        <td><?php echo e($invoice->total_after_discount); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('payment-requests.edit-invoice', $invoice->id)); ?>" class="btn btn-success"><i class="nav-icon fas fa-edit"></i></a>
                                            
                                            
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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\git\quickBook_panelone\resources\views/payment-requests/invoice-list.blade.php ENDPATH**/ ?>