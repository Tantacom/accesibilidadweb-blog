<?php get_header(); ?>
<h1>Noticias de Accesibilidad Web</h1>
<div id="bodyContent">

	<div id="content" class="narrowcolumn">
	<!--h1>Noticias de Accesibilidad Web</h1-->
	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Enlace permanente a <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<small><?php the_time('l, j \d\e F \d\e Y') ?> por <?php the_author() ?></small>

				<div class="entry">
					<?php the_content('Leer el resto de la entrada &raquo;'); ?>
				</div>

				<p class="postmetadata">Clasificado bajo: <?php the_category(', ') ?> | <?php edit_post_link('Editar','',' | '); ?>  <?php comments_popup_link('Sin comentarios &#187;', '1 comentario &#187;', '% comentarios &#187;'); ?></p>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Entradas anteriores') ?></div>
			<div class="alignright"><?php previous_posts_link('Entradas siguientes &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h3>No encontrado</h3>
		<p>Disculpe, lo que busca no est&aacute; aqu&iacute;.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
