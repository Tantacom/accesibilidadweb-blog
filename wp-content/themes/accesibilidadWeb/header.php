<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
<meta name="verify-v1" content="wGtOnqRKiqCMMzolI4LvmEeGpXbQLAwezPp8nfOvCdM=" />

<?	
	if ( is_category()  ){		
		global $post;
		$categories = get_the_category($post->ID);
	 	$des =  "Publicamos artículos en profundidad sobre ".$categories[0]->cat_name."...Consúltalos" ?>
	 	<meta name="description" content="<? echo $des; ?>" />
	<? 	
	}else if(is_date()){ ?>
		
	 	<meta name="description" content="Publicamos artículos en profundidad sobre temas de actualidad relacionados con la accesibilidad web, el desarrollo frontend y el desarrollo backend y cms, tendencias... Consúltalos." />
	
	<? } ?>

<!-- Metadatos de navegacion semantica -->
<link rel="start" href="/index.php" title="Página inicial" />
<link rel="index" href="/index.php" title="Página inicial" />
<link rel="author" href="/accesibilidad_web.php" title="La Accesibilidad" />
<link rel="author" href="/auditoria_accesibilidad_web.php" title="Auditoría" />
<link rel="author" href="/adecuacion_accesibilidad_web.php" title="Adecuación y desarrollo" />
<link rel="section" href="/formacion_accesibilidad_web.php" title="Formación" />
<link rel="section" href="/experiencia_accesibilidad_web.php" title="Experiencia" />
<link rel="section" href="blog/index.php" title="Blog" />
<link rel="section" href="/tanta_comunicacion.php" title="Contacto" />

<link href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/css/styles.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
  <!--[if lte IE 6]>
	<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/css/fixIE6.css" />
<![endif]-->
<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/css/fixIE7.css" />
<![endif]-->

<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/css/impresion.css" media="print" />

<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />


<?php wp_head(); ?>
</head>
<body id="blog">
<div id="wrapper">
		<div class="hide">
			<ul title="Enlaces de salto:">
				<li><a href="#navBar">Menú de navegación</a></li>
				<li><a href="#sideBar">Menú lateral</a></li>
				<li><a href="#content">Contenido</a></li>
			</ul>
		</div>
		<div id="header">
			<div>
				<ul>
					<li><a href="/index.php" accesskey="0">Inicio</a></li>
					<li class="sel"><a href="/blog/index.php" accesskey="6">Blog</a></li>
					<li class="reset"><a href="/tanta_comunicacion.php" accesskey="7">Contacto</a></li>
				</ul>
			</div>
			<a href="/index.php"><img src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/img/accesibilidadweb.gif" width="243" height="18" alt="Ir a inicio." id="logo" /></a>
		</div>

		<div id="navBar">
			<ul>
				<li><a href="/accesibilidad_web.php" accesskey="1">La Accesibilidad</a></li>
				<li><a href="/auditoria_accesibilidad_web.php" accesskey="2">Auditoría</a></li>
				<li><a href="/adecuacion_accesibilidad_web.php" accesskey="3">Adecuación y desarrollo</a></li>
				<li><a href="/formacion_accesibilidad_web.php" accesskey="4">Formación</a></li>
				<li class="reset"><a href="/experiencia_accesibilidad_web.php" accesskey="5">Experiencia</a></li>
			</ul>
		</div>



