<?php if( me() ) { ?>

<div class="left">

<div class="title">Suggest a Store/Brand</div>
<?php echo suggest_store_form( array('intent' => 1) ); ?>

</div>

<div></div>

<?php } else { ?>

<div class="left">

<div class="title">Suggest a Store/Brand</div>
<?php echo suggest_store_form( array('intent' => 2) ); ?>

</div>

<div></div>

<?php } ?>