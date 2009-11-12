<h2>Register</h2>
<?php
    $session->flash('auth');
    echo $form->create('User', array('action' => 'register'));
    echo $form->input('username');
    echo $form->input('password');
    echo $form->input('password2', array('type' => 'password', 'label' => 'Verify password'));
    echo $form->end('Create Account');
?>