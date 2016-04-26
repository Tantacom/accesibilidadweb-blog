<?php get_header(); ?>
<h1>Noticias de Accesibilidad Web</h1>
<div id="bodyContent">
<?php get_sidebar(); ?>
	<div id="content" class="narrowcolumn">
	<!--h1>Noticias de Accesibilidad Web</h1-->
	<?php if (have_posts()) : ?>

		<h2 class="pagetitle">Resultados de la b&uacute;squeda</h2>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Entradas anteriores') ?></div>
			<div class="alignright"><?php previous_posts_link('Entradas siguientes &raquo;') ?></div>
		</div>


		<?php while (have_posts()) : the_post(); ?>

			<div class="post">
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
				<small><?php the_time('l, j \d\e F \d\e Y') ?></small>

				<p class="postmetadata">Clasificado bajo: <?php the_category(', ') ?> | <?php edit_post_link('Editar','',' | '); ?>  <?php comments_popup_link('Sin comentarios &#187;', '1 comentario &#187;', '% comentarios &#187;'); ?></p>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Entradas anteriores') ?></div>
			<div class="alignright"><?php previous_posts_link('Entradas siguientes &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">No se han encontrado resultados.</h2>
		<p>No existe ning&uacute;n art&iacute;culo que coincida con su criterio de b&uacute;squeda.</p>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>

	</div>
</div>
<?php get_footer(); ?>