<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>

<div class="option" style="display: none"><img src="<?php echo $this->url (); ?>/images/progress.gif" alt="progress"/></div>

<img src="<?php echo $this->url (); ?>/images/page.png" alt="page"/>

<?php if ($nolink !== true) : ?>
	<a href="#<?php echo $type ?>"><strong><?php echo $name ?></strong></a>
<?php else : ?>
	<strong><?php echo $name ?></strong>
<?php endif; ?>

- <?php echo $desc ?>
