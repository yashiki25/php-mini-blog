<?php $this->setLayoutVar('title', 'アカウント登録') ?>

<h2>アカウント登録</h2>

<form action="<?php echo $base_url; ?>/account/register" method="post">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">

    <?php if (isset($errors) && count($errors) > 0): ?>
        <?php echo $this->render('error', ['errors' => $errors]); ?>
    <?php endif; ?>

    <?php echo $this->render('account/inputs', ['user_name' => $user_name, 'password' => $password]); ?>

    <p>
        <input type="submit" value="登録">
    </p>
</form>