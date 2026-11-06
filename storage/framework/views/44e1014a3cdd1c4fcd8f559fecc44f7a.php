<?php $__env->startSection('title', 'Log in'); ?>

<?php $__env->startSection('content'); ?>
<div class="card form-card" style="margin:0 auto">
    <h1>Log in</h1>
    <p class="muted">Use the account you registered with the market administration office.</p>

    <form method="POST" action="<?php echo e(route('login')); ?>">
        <?php echo csrf_field(); ?>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username" class="<?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>
        <div class="field">
            <label class="check"><input type="checkbox" name="remember" value="1"> Keep me logged in</label>
        </div>
        <button type="submit" class="btn primary block">Log in</button>
    </form>

    <p class="mt muted small">No account yet? <a href="<?php echo e(route('register')); ?>">Register as a shopper or vendor</a>.</p>

    <?php if(app()->environment('local')): ?>
        <div class="alert info small mt">
            <strong>Demo accounts</strong> (password: <code>password</code>)<br>
            admin@tindapresyo.test · inspector@tindapresyo.test<br>
            vendor1@tindapresyo.test · shopper1@tindapresyo.test
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\tindapresyo\resources\views/auth/login.blade.php ENDPATH**/ ?>