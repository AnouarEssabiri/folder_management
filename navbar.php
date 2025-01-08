<style>

</style>


<nav id="sidebar" class='mx-lt-5 bg-dark' >
		
		<div class="sidebar-list">
				<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-home"></i></span> Accueil</a>
				<a href="index.php?page=files" class="nav-item nav-files"><span class='icon-field'><i class="fa fa-archive"></i></span> Archives</a>
				<a href="index.php?page=quick_search" class="nav-item nav-quick_search"><span class='icon-field'><i class="fa fa-search"></i></span> Recherche avancée</a>
				<a href="index.php?page=arborescence" class="nav-item nav-arborescence"><span class='icon-field'><i class="fa fa-tree"></i></span> Arborescence</a>
				<?php if($_SESSION['login_type'] == 1): ?>
				<!-- <a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users"></i></span> Users</a> -->
			<?php endif; ?>
		</div>

</nav>
<script>
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
</script>