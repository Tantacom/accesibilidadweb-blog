<form method="get" id="searchform" action="http://<?php echo $_SERVER['SERVER_NAME']; ?>/blog/index.php">
<div><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
<input type="submit" id="searchsubmit" value="Buscar" />
</div>
</form>
