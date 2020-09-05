<?php $this->setLayoutVar('title', $status['user_name']) ?>

<?php echo $this->render('status_status', ['status' => $status]); ?>
