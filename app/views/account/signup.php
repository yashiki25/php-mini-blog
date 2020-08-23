<?php $this->setLayoutVar('title', 'アカウント登録') ?>

<h2>アカウント登録</h2>

<form action="<?php echo $base_url; ?>/account/register" method="post">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">

    <table>
        <tbody>
        <tr>
            <th>ユーザーID</th>
            <?php if (isset($errors['user_name'])) { ?>
            <p><?php echo $this->escape($errors['user_name']); ?></p>
            <?php } ?>
            <td>
                <input type="text" name="user_name" value="<?php echo $this->escape($user_name); ?>">
            </td>
        </tr>
        <tr>
            <th>パスワード</th>
            <td>
                <input type="password" name="password" value="<?php echo $this->escape($password); ?>">
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" value="登録">
    </p>
</form>