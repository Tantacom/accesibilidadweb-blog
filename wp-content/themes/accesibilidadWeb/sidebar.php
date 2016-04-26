	<div id="sidebar">
		<ul>

			<li>
				<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</li>
			<li><h2>Acerca del autor</h2>
				<span><a href="http://www.seraccesible.net/felix-zapata-berlinches/">Félix Zapata</a></span>
			</li>
			
			<li class="rss"><h2>Sindicaci&oacute;n</h2>
				<ul>
					<li><a href="<?php bloginfo('rss2_url'); ?>">Entradas (RSS)</a></li>
					<li><a href="<?php bloginfo('comments_rss2_url'); ?>">Comentarios (RSS)</a></li>
				</ul>
			</li>

			<li><h2>Lo más leído</h2>
			<ul>
				<?php get_most_viewed('post', 10); ?>
				</ul>
			</li>



			<?php wp_list_categories('show_count=1&title_li=<h2>Categorías</h2>'); ?>

			<li><h2>Archivos</h2>
				<ul>
				<?php wp_get_archives('type=monthly'); ?>
				</ul>
			</li>

			<?php /* If this is the frontpage */ if ( is_home() || is_page() ) { ?>
				<li><h2>Lectura recomendada</h2>
				<ul>
					<li><a href="http://olgacarreras.blogspot.com/2007/02/pdf-accesibles.html" rel='nofollow'>PDFs accesibles</a></li>
					<li><a href="http://olgacarreras.blogspot.com/2007/02/ajax-accesible-ii-wai-aria.html" rel='nofollow'>AJAX Accesible (II): WAI-ARIA</a></li>
					<li><a href="http://olgacarreras.blogspot.com/2007/04/metodologa-certificaciones-y-entidades.html" rel='nofollow' title="Metodolog&iacute;as, certificaciones y entidades certificadoras de la accesibilidad web en Espa&ntilde;a">Metodolog&iacute;as y certificaciones</a></li>
					<li><a href="http://accesibilidadweb.blogspot.com/2007/04/cmo-crear-un-documento-word-accesible.html" rel='nofollow'>Crear un documento Word accesible</a></li>
					<li><a href="http://www.alistapart.com/articles/tohellwithwcag2/" rel='nofollow' lang="en" hreflang="en" title="Art&iacute;culo de Joe Clark criticando la futura versi&oacute;n de las Directrices de Accesibilidad">To Hell with WCAG 2</a></li>
					<li><a href="http://juicystudio.com/article/eshop-accessibility.php" rel='nofollow' lang="en" hreflang="en" title="Revisi&oacute;n de Joe Clark de la versi&oacute;n 1 de las Directrices de Accesibilidad">WCAG Samurai Errata for WCAG 1.0</a></li>
				</ul>
			</li>

				<?php wp_list_bookmarks(); ?>


			<?php } ?>
		</ul>

	</div>

