<?php $__env->startSection('title', 'Register'); ?>

<?php $__env->startSection('content'); ?>
<div class="card form-card" style="margin:0 auto">
    <h1>Create an account</h1>
    <p class="muted">Shoppers can report overpricing. Vendors can register a stall and post daily prices.</p>

    <form method="POST" action="<?php echo e(route('register')); ?>">
        <?php echo csrf_field(); ?>
        <div class="field">
            <label for="account_type">I am a</label>
            <select id="account_type" name="account_type" class="<?php $__errorArgs = ['account_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value="shopper" <?php if(old('account_type', 'shopper') === 'shopper'): echo 'selected'; endif; ?>>Shopper (I buy at the market)</option>
                <option value="vendor" <?php if(old('account_type') === 'vendor'): echo 'selected'; endif; ?>>Vendor (I rent a stall)</option>
            </select>
        </div>

        <div class="row">
            <div class="field">
                <label for="name">Full name</label>
                <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" required class="<?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            </div>
            <div class="field">
                <label for="phone">Mobile number <span class="muted">(optional)</span></label>
                <input type="text" id="phone" name="phone" value="<?php echo e(old('phone')); ?>" class="<?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            </div>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="username" class="<?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        </div>
        <div class="row">
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="new-password" class="<?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <div class="hint">At least 8 characters.</div>
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            </div>
        </div>

        
        <div id="stall-fields" style="display:none">
            <div class="alert info small">Your stall will be reviewed and approved by the market administration office before you can post prices.</div>
            <div class="row">
                <div class="field">
                    <label for="stall_number">Stall number</label>
                    <input type="text" id="stall_number" name="stall_number" value="<?php echo e(old('stall_number')); ?>" placeholder="e.g. V-12" class="<?php $__errorArgs = ['stall_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                </div>
                <div class="field">
                    <label for="section">Market section</label>
                    <select id="section" name="section" class="<?php $__errorArgs = ['section'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">Choose a section</option>
                        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s); ?>" <?php if(old('section') === $s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="field">
                <label for="stall_name">Stall name</label>
                <input type="text" id="stall_name" name="stall_name" value="<?php echo e(old('stall_name')); ?>" class="<?php $__errorArgs = ['stall_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            </div>
        </div>

        <button type="submit" class="btn primary block">Register</button>
    </form>
    <p class="mt muted small">Already registered? <a href="<?php echo e(route('login')); ?>">Log in</a>.</p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Documents\college\Fourth Year (irregular student era)\1st sem\Capstone 1\tindapresyo\resources\views/auth/register.blade.php ENDPATH**/ ?>