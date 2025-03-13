<?php
if(isset($_SESSION['messagi_ni']))
{
    ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>HEY</strong> <?$_SESSION['message_ni']; ?>
        <button type=button class="btn-close" data-bs-toggle="alert" aria-label="Close"></button>
    </div>
    <?php
    unset($_SESSION['message_ni']);
}
?>