<?php
include 'db_connect.php';

$folder_parent = isset($_GET['fid']) ? $_GET['fid'] : 0;
$records_per_page = 10;  // Set the number of records per page
$records_per_page_files = 5;

// Pagination for folders
$page_folders = isset($_GET['p_folders']) ? (int) $_GET['p_folders'] : 1;  // Default to 1 if page is not set
if ($page_folders < 1) {
	$page_folders = 1;  // Ensure that page is at least 1
}
$offset_folders = ($page_folders - 1) * $records_per_page;  // Calculate the offset for folders

// Pagination for files
$page_files = isset($_GET['p_files']) ? (int) $_GET['p_files'] : 1;  // Default to 1 if page is not set
if ($page_files < 1) {
	$page_files = 1;  // Ensure that page is at least 1
}
$offset_files = ($page_files - 1) * $records_per_page_files;  // Calculate the offset for files

// Fetch the search term from input
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Modify the SQL queries to include the search condition
$search_condition = $search_term ? " AND (name LIKE '%$search_term%' OR description LIKE '%$search_term%')" : '';

// Fetch folders with pagination and search
$folders = $conn->query("SELECT * FROM folders WHERE user_id = '" . $_SESSION['login_id'] . "' $search_condition ORDER BY name ASC LIMIT $records_per_page OFFSET $offset_folders");

// Fetch files with pagination and search
$files = $conn->query("SELECT * FROM `files` WHERE user_id = '" . $_SESSION['login_id'] . "' $search_condition ORDER BY name ASC LIMIT $records_per_page_files OFFSET $offset_files");

// Get the total number of folders and files
$total_folders = $conn->query("SELECT COUNT(*) as count FROM folders WHERE user_id = '" . $_SESSION['login_id'] . "'")->fetch_assoc()['count'];
$total_files = $conn->query("SELECT COUNT(*) as count FROM files WHERE user_id = '" . $_SESSION['login_id'] . "'")->fetch_assoc()['count'];

// Calculate total pages for folders and files separately
$total_pages_folders = ceil($total_folders / $records_per_page);
$total_pages_files = ceil($total_files / $records_per_page_files);

?>

<style>
	html {
		scroll-behavior: smooth;
	}

	.folder-item {
		cursor: pointer;
	}

	.folder-item:hover {
		background: #eaeaea;
		color: black;
		box-shadow: 3px 3px #0000000f;
	}

	.custom-menu {
		z-index: 1000;
		position: absolute;
		background-color: #ffffff;
		border: 1px solid #0000001c;
		border-radius: 5px;
		padding: 8px;
		min-width: 13vw;
	}

	a.custom-menu-list {
		width: 100%;
		display: flex;
		color: #4c4b4b;
		font-weight: 600;
		font-size: 1em;
		padding: 1px 11px;
	}

	.file-item {
		cursor: pointer;
	}

	a.custom-menu-list:hover,
	.file-item:hover,
	.file-item.active {
		background: #80808024;
	}

	/* table th,
	td {
		border-left:1px solid gray;
	} */

	a.custom-menu-list span.icon {
		width: 1em;
		margin-right: 5px
	}

	.pagination {
		display: flex;
		justify-content: center;
		margin-top: 20px;
	}

	.pagination a {
		padding: 8px 16px;
		margin: 0 5px;
		text-decoration: none;
		color: #007bff;
		border: 1px solid #ddd;
		border-radius: 4px;
	}

	.pagination a:hover {
		background-color: #f0f0f0;
	}

	.pagination .active {
		background-color: #007bff;
		color: white;
		font-weight: bold;
	}

	.pagination .prev,
	.pagination .next {
		font-weight: bold;
	}
</style>
<nav aria-label="breadcrumb ">
	<ol class="breadcrumb">

		<?php
		$id = $folder_parent;
		while ($id > 0) {

			$path = $conn->query("SELECT * FROM folders where id = $id  order by name asc")->fetch_array();
			?>
			<li class="breadcrumb-item text-info"><?php echo $path['name']; ?></li>
			<?php
			$id = $path['parent_id'];
		}
		?>
		<li class="breadcrumb-item" id="top"><a class="text-info" href="index.php?page=files">CMC RSK</a></li>
	</ol>
</nav>
<div class="container-fluid">
	<div class="col-lg-12">

		<div class="row">
			<!-- <button class="btn btn-success btn-sm" id="new_folder"><i class="fa fa-plus"></i> Nouveau Dossier</button> -->
			<!-- <button class="btn btn-success btn-sm ml-4" id="new_file"><i class="fa fa-upload"></i> Télécharger un fichier</button> -->
		</div>
		<!-- <hr> -->
		<!-- <a href="#top" class="btn btn-outline-info" style="position:fixed;bottom:10px;right:20px;z-index:20;padding:10px;border-radius: 10px;">▲</a> -->
		<!-- <div class="row my-3">
			<div class="col-lg-12 d-flex justify-content-center">
				<div class="col-md-6 input-group">
					<input type="text" class="form-control search-input" placeholder="Tapez pour rechercher..."
						id="search" aria-label="Search">
					<div class="input-group-append">
						<button class="btn btn-info" id="search-btn">
							<i class="fa fa-search"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
		<div id="no-results" class="text-center text-danger" style="display: none;">Aucun résultat trouvé!</div> -->

		<div class="row my-3">
			<div class="col-lg-12 d-flex justify-content-center">
				<div class="col-md-6 input-group">
					<form action="index.php" method="get">
						<input type="text" class="form-control search-input" name="search"
							value="<?php echo $search_term; ?>" placeholder="Tapez pour rechercher..."
							aria-label="Search">
						<div class="input-group-append">
							<button type="submit" class="btn btn-info">
								<i class="fa fa-search"></i>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div id="no-results" class="text-center text-danger" style="display: none;">Aucun résultat trouvé!</div>


		<div class="row">
			<div class="col-md-12">
				<h4><b>Dossiers</b></h4>
			</div>
		</div>
		<hr>
		<div class="row" style="justify-content: space-evenly;">
			<?php
			while ($row = $folders->fetch_assoc()):
				?>
				<div class="card col-md-3 mt-2 ml-2 mr-2 mb-2 folder-item" data-id="<?php echo $row['id'] ?>">
					<div class="card-body">
						<large><span><i class="fa fa-folder"></i></span><b class="to_folder"
								style="text-transform:uppercase"> <?php echo $row['name'] ?></b></large>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
		<!-- Pagination Controls for Folders -->
		<div class="pagination">
			<?php if ($page_folders > 1): ?>
				<a href="index.php?page=quick_search&fid=<?php echo $folder_parent; ?>&p_folders=<?php echo $page_folders - 1; ?>&search=<?php echo $search_term; ?>"
					class="prev">Prev</a>
			<?php endif; ?>

			<?php for ($i = 1; $i <= $total_pages_folders; $i++): ?>
				<a href="index.php?page=quick_search&fid=<?php echo $folder_parent; ?>&p_folders=<?php echo $i; ?>&search=<?php echo $search_term; ?>"
					class="page-number <?php echo ($i == $page_folders) ? 'active' : ''; ?>">
					<?php echo $i; ?>
				</a>
			<?php endfor; ?>

			<?php if ($page_folders < $total_pages_folders): ?>
				<a href="index.php?page=quick_search&fid=<?php echo $folder_parent; ?>&p_folders=<?php echo $page_folders + 1; ?>&search=<?php echo $search_term; ?>"
					class="next">Next</a>
			<?php endif; ?>
		</div>

		<hr>
		<div class="row">
			<div class="card col-md-12">
				<div class="card-body">
					<table width="100%">
						<tr>
							<th width="40%" class="">Nom de fichier</th>
							<th width="20%" class="">Date</th>
							<th width="40%" class="">Description</th>
						</tr>
						<?php
						while ($row = $files->fetch_assoc()):
							$name = explode(' ||', $row['name']);
							$name = isset($name[1]) ? $name[0] . " (" . $name[1] . ")." . $row['file_type'] : $name[0] . "." . $row['file_type'];
							$img_arr = array('png', 'jpg', 'jpeg', 'gif', 'psd', 'tif');
							$doc_arr = array('doc', 'docx');
							$pdf_arr = array('pdf', 'ps', 'eps', 'prn');
							$icon = 'fa-file';
							if (in_array(strtolower($row['file_type']), $img_arr))
								$icon = 'fa-image';
							if (in_array(strtolower($row['file_type']), $doc_arr))
								$icon = 'fa-file-word';
							if (in_array(strtolower($row['file_type']), $pdf_arr))
								$icon = 'fa-file-pdf';
							if (in_array(strtolower($row['file_type']), ['xlsx', 'xls', 'xlsm', 'xlsb', 'xltm', 'xlt', 'xla', 'xlr']))
								$icon = 'fa-file-excel';
							if (in_array(strtolower($row['file_type']), ['zip', 'rar', 'tar']))
								$icon = 'fa-file-archive';
							?>
							<tr class='file-item' data-id="<?php echo $row['id'] ?>" data-name="<?php echo $name ?>">
								<td>
									<a href="./assets/uploads/<?php echo $row['file_path'] ?>" target="_blank">
										<large><span><i class="fa <?php echo $icon ?>"></i></span><b class="to_file">
												<?php echo $name ?></b></large>
										<input type="text" class="rename_file" value="<?php echo $row['name'] ?>"
											data-id="<?php echo $row['id'] ?>" data-type="<?php echo $row['file_type'] ?>"
											style="display: none">
									</a>
								</td>
								<td><i
										class="to_file"><?php echo date('Y/m/d h:i A', strtotime($row['date_updated'])) ?></i>
								</td>
								<td><i class="to_file"><?php echo $row['description'] ?></i></td>
							</tr>
						<?php endwhile; ?>
					</table>

				</div>
			</div>
			<!-- Pagination Controls for Files -->
			<div class="pagination">
				<?php if ($page_files > 1): ?>
					<a href="index.php?page=quick_search&fid=<?php echo $folder_parent; ?>&p_files=<?php echo $page_files - 1; ?>"
						class="prev">Prev</a>
				<?php endif; ?>

				<?php for ($i = 1; $i <= $total_pages_files; $i++): ?>
					<a href="index.php?page=quick_search&fid=<?php echo $folder_parent; ?>&p_files=<?php echo $i; ?>"
						class="page-number"><?php echo $i; ?></a>
				<?php endfor; ?>

				<?php if ($page_files < $total_pages_files): ?>
					<a href="index.php?page=quick_search&fid=<?php echo $folder_parent; ?>&p_files=<?php echo $page_files + 1; ?>"
						class="next">Next</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<div id="menu-folder-clone" style="display: none;">
	<a href="javascript:void(0)" class="custom-menu-list file-option edit">Renommer</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option delete">Supprimer</a>
</div>
<div id="menu-file-clone" style="display: none;">
	<a href="javascript:void(0)" class="custom-menu-list file-option edit"><span><i class="fa fa-edit"></i>
		</span>Renommer</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option download"><span><i class="fa fa-download"></i>
		</span>Télécharger</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option delete"><span><i class="fa fa-trash"></i>
		</span>Supprimer</a>
</div>

<script>
	$('#new_folder').click(function () {
		uni_modal('', 'manage_folder.php?fid=<?php echo $folder_parent ?>')
	})
	$('#new_file').click(function () {
		uni_modal('', 'manage_files.php?fid=<?php echo $folder_parent ?>')
	})
	$('.folder-item').click(function () {
		location.href = 'index.php?page=files&fid=' + $(this).attr('data-id')
	})
	$('.folder-item').bind("contextmenu", function (event) {
		event.preventDefault();
		$("div.custom-menu").hide();
		var custom = $("<div class='custom-menu'></div>")
		custom.append($('#menu-folder-clone').html())
		custom.find('.edit').attr('data-id', $(this).attr('data-id'))
		custom.find('.delete').attr('data-id', $(this).attr('data-id'))
		custom.appendTo("body")
		custom.css({
			top: event.pageY + "px",
			left: event.pageX + "px"
		});

		$("div.custom-menu .edit").click(function (e) {
			e.preventDefault()
			uni_modal('Renommer le dossier', 'manage_folder.php?fid=<?php echo $folder_parent ?>&id=' + $(this).attr('data-id'))
		})
		$("div.custom-menu .delete").click(function (e) {
			e.preventDefault()
			_conf("Voulez-vous vraiment supprimer ce dossier?", "delete_folder", [$(this).attr('data-id')])
		})

		$(document).bind("mousedown", function (e) {
			if (!$(e.target).closest("div.custom-menu").length) {
				$("div.custom-menu").hide();
			}
		});
	});

	$('.file-item').bind("contextmenu", function (event) {
		event.preventDefault();
		$("div.custom-menu").hide();
		var custom = $("<div class='custom-menu'></div>")
		custom.append($('#menu-file-clone').html())
		custom.find('.edit').attr('data-id', $(this).attr('data-id'))
		custom.find('.download').attr('data-id', $(this).attr('data-id'))
		custom.find('.delete').attr('data-id', $(this).attr('data-id'))
		custom.appendTo("body")
		custom.css({
			top: event.pageY + "px",
			left: event.pageX + "px"
		});

		$("div.custom-menu .edit").click(function (e) {
			e.preventDefault()
			uni_modal('Renommer le fichier', 'manage_files.php?fid=<?php echo $folder_parent ?>&id=' + $(this).attr('data-id'))
		})
		$("div.custom-menu .delete").click(function (e) {
			e.preventDefault()
			_conf("Voulez-vous vraiment supprimer ce fichier?", "delete_file", [$(this).attr('data-id')])
		})
		$("div.custom-menu .download").click(function (e) {
			e.preventDefault()
			window.location = './assets/uploads/' + $(this).attr('data-id')
		})

		$(document).bind("mousedown", function (e) {
			if (!$(e.target).closest("div.custom-menu").length) {
				$("div.custom-menu").hide();
			}
		});
	});

	function delete_folder(id) {
		start_load()
		$.ajax({
			url: 'delete_folder.php',
			method: 'POST',
			data: {
				id: id
			},
			success: function (resp) {
				if (resp == 1) {
					alert_toast("Dossier supprimé avec succès", "success");
					setTimeout(function () {
						location.reload()
					}, 1500)
				}
			}
		})
	}

	function delete_file(id) {
		start_load()
		$.ajax({
			url: 'delete_file.php',
			method: 'POST',
			data: {
				id: id
			},
			success: function (resp) {
				if (resp == 1) {
					alert_toast("Fichier supprimé avec succès", "success");
					setTimeout(function () {
						location.reload()
					}, 1500)
				}
			}
		})
	}

	$('.search-input').on('input', function () {
		var query = $(this).val().toLowerCase();
		var noResults = true;

		$('.folder-item').each(function () {
			var folderName = $(this).text().toLowerCase();
			if (folderName.indexOf(query) !== -1) {
				$(this).show();
				noResults = false;
			} else {
				$(this).hide();
			}
		});

		$('.file-item').each(function () {
			var fileName = $(this).text().toLowerCase();
			if (fileName.indexOf(query) !== -1) {
				$(this).show();
				noResults = false;
			} else {
				$(this).hide();
			}
		});

		if (noResults) {
			$('#no-results').show();
		} else {
			$('#no-results').hide();
		}
	});
	
</script>
