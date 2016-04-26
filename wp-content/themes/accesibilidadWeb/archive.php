<?php get_header(); ?>
<h1>Noticias de Accesibilidad Web</h1>
<div id="bodyContent">


	<div id="content" class="narrowcolumn">
		<!--h1>Noticias de Accesibilidad Web</h1-->
		<?php if (have_posts()) : ?>

		 <?php $post = $posts[0]; // Hack. Definir $post para que the_date() funcione. ?>
<?php /* Si es un archivo de categoria */ if (is_category()) { ?>
		<h2>Archivo de la categor&iacute;a "<?php echo single_cat_title(); ?>"</h2>

 	  <?php /* Si es un archivo diario */ } elseif (is_day()) { ?>
		<h2>Archivo del <?php the_time('j \d\e F \d\e Y'); ?></h2>

	 <?php /* Si es un archivo mensual */ } elseif (is_month()) { ?>
		<h2>Archivo de <?php the_time('F \d\e Y'); ?></h2>

		<?php /* Si es un archivo anual */ } elseif (is_year()) { ?>
		<h2>Archivo de <?php the_time('Y'); ?></h2>

	  <?php /* Si es una busqueda */ } elseif (is_search()) { ?>
		<h2>Resultados de la b&uacute;squeda</h2>

	  <?php /* Si es un archivo de autor */ } elseif (is_author()) { ?>
		<h2>Archivo de autor</h2>

		<?php /* Si es un archivo paginado */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2>Archivos del weblog</h2>

		<?php } ?>


		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>

		<?php while (have_posts()) : the_post(); ?>
		<div class="post">
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
				<small><?php the_time('l, j \d\e F \d\e Y') ?></small>

				<div class="entry">
					<?php the_content() ?>
				</div>

				<p class="postmetadata">Clasificado bajo: <?php the_category(', ') ?> | <?php edit_post_link('Editar','',' | '); ?>  <?php comments_popup_link('Sin comentarios &#187;', '1 comentario &#187;', '% comentarios &#187;'); ?></p>

			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">No encontrado</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>

	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
