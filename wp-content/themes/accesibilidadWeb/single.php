<?php get_header(); ?>
<!--h1>Noticias de Accesibilidad Web</h1-->
<div id="bodyContent">

	<div id="content" class="widecolumn">
	<!--h1>Noticias de Accesibilidad Web</h1-->
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="navigation">
			<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
			<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
		</div>

		<div class="post" id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>

			<div class="entry">
				<?php the_content('<p class="serif">Leer el resto de la entrada &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>P&aacute;ginas:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

				<p class="postmetadata alt">
					<small>
						Esta entrada fue publicada
						<?php /* This is commented, because it requires a little adjusting sometimes.
							You'll need to download this plugin, and follow the instructions:
							http://binarybonsai.com/archives/2004/08/17/time-since-plugin/ */
							/* $entry_datetime = abs(strtotime($post->post_date) - (60*120)); echo time_since($entry_datetime); echo ' ago'; */ ?>
						el <?php the_time('l, j \d\e F \d\e Y') ?> a las <?php the_time() ?>
						y est&aacute; clasificada bajo: <?php the_category(', ') ?>.
						Puede hacer un seguimiento de los comentarios de esta entrada gracias al feed <?php comments_rss_link('RSS 2.0'); ?>.

						<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							// Comentarios y Pings permitidos ?>
							Puede <a href="#respond">dejar un comentario</a>, o enviar un <a href="<?php trackback_url(true); ?>">trackback</a> desde su sitio.

						<?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							// Solo Pings permitidos ?>
							Los comentarios est&aacute;n cerrados, pero puede enviar un <a href="<?php trackback_url(true); ?> ">trackback</a> desde su sitio.

						<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							// Comentarios permitidos, Pings no ?>
							Puede dejar un comentario a continuaci&oacute;n. Los trackbacks est&aacute;n cerrados.

						<?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							// Ni comentarios, ni Pings permitidos ?>
							Tanto los comentarios como los trackbacks est&aacute;n cerrados.

						<?php } edit_post_link('Editar esta entrada.','',''); ?>

					</small>
				</p>
				<h2>Entradas relacionadas</h2>
				<ul>
				<?php related_posts($limit, $len, '$before_title', '$after_title', '$before_post', '$after_post', $show_pass_post, $show_excerpt); ?>
				</ul>

			</div>
		</div>

	<?php comments_template(); ?>

	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
