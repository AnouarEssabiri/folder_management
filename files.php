<?php
include 'db_connect.php';
$folder_parent = isset($_GET['fid']) ? $_GET['fid'] : 0;
$folders = $conn->query("SELECT * FROM folders where parent_id = $folder_parent and user_id = '" . $_SESSION['login_id'] . "'  order by name asc");


$files = $conn->query("SELECT * FROM files where folder_id = $folder_parent and user_id = '" . $_SESSION['login_id'] . "'  order by name asc");

?>
<style>
	/* General Styles */
	body {
		font-family: Arial, sans-serif;
		background-color: #f9f9f9;
		margin: 0;
		padding: 0;
	}

	/* Folder & File Cards */
	.folder-item,
	.file-item {
		border: 1px solid #ddd;
		border-radius: 8px;
		background: #fff;
		padding: 20px;
		text-align: center;
		transition: all 0.3s ease-in-out;
		box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
	}

	.folder-item:hover,
	.file-item:hover {
		transform: scale(1.05);
		background: #f0f8ff;
		cursor: pointer;
	}

	/* Breadcrumbs */
	.breadcrumb {
		background: transparent;
		font-size: 14px;
		padding: 10px 15px;
		border-radius: 8px;
		margin-bottom: 20px;
	}

	.breadcrumb a {
		text-decoration: none;
		color: #007bff;
	}

	.breadcrumb a:hover {
		text-decoration: underline;
	}

	/* Buttons */
	button {
		border: none;
		padding: 10px 15px;
		color: #fff;
		background: #007bff;
		border-radius: 5px;
		cursor: pointer;
		transition: background-color 0.3s;
	}

	button:hover {
		background: #0056b3;
	}

	#new_folder,
	#new_file {
		margin-right: 10px;
	}

	/* Search Bar */
	.input-group {
		margin: 20px 0;
	}

	.input-group .form-control {
		border-radius: 4px 0 0 4px;
		border: 1px solid #ddd;
	}

	.input-group .input-group-append .input-group-text {
		border-radius: 0 4px 4px 0;
		background: #007bff;
		color: #fff;
	}

	/* Table */
	table {
		width: 100%;
		border-collapse: collapse;
		margin-top: 20px;
		background: #fff;
		border-radius: 8px;
		overflow: hidden;
		box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
	}

	table th,
	table td {
		padding: 12px;
		text-align: left;
		border-bottom: 1px solid #ddd;
	}

	table th {
		background: #f7f7f7;
		font-weight: bold;
	}

	table tr:hover {
		background: #f0f8ff;
	}

	/* Custom Context Menu */
	.custom-menu {
		z-index: 1000;
		position: absolute;
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 5px;
		box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
		padding: 10px;
		min-width: 150px;
	}

	.custom-menu a {
		display: block;
		color: #333;
		padding: 8px 10px;
		text-decoration: none;
		border-radius: 4px;
		transition: background-color 0.2s;
	}

	.custom-menu a:hover {
		background: #007bff;
		color: #fff;
	}

	/* Responsive Design */
	@media screen and (max-width: 768px) {

		.folder-item,
		.file-item {
			margin: 10px 0;
			width: 100%;
		}

		table th,
		table td {
			font-size: 14px;
		}

		.breadcrumb {
			font-size: 12px;
		}
	}
</style>
<nav aria-label="breadcrumb ">
	<ol class="breadcrumb">

		<?php
		$id = $folder_parent;
		$breadcrumbs = [];

		while ($id > 0) {
			$path = $conn->query("SELECT * FROM folders WHERE id = $id")->fetch_array();
			$breadcrumbs[] = '<li class="breadcrumb-item dynamic-item text-info" style="text-transform:uppercase">
                              <a class="text-info" href="index.php?page=files&fid=' . $path['parent_id'] . '">' . $path['name'] . '</a>
                          </li>';
			$id = $path['parent_id'];
		}

		$breadcrumbs[] = '<li class="breadcrumb-item" style="text-transform:uppercase">
                          <a class="text-info" href="index.php?page=files">Files</a>
                      </li>';

		$breadcrumbs = array_reverse($breadcrumbs);

		foreach ($breadcrumbs as $breadcrumb) {
			echo $breadcrumb;
		}
		?>

	</ol>

</nav>
<div class="container-fluid">
	<div class="col-lg-12">

		<div class="row">
			<button class="btn btn-info btn-sm" id="new_folder"><i class="fa fa-plus"></i> New Folder</button>
			<button class="btn btn-info btn-sm ml-4" id="new_file"><i class="fa fa-upload"></i> Upload File</button>
		</div>
		<hr>
		<div class="row">
			<div class="col-lg-12">
				<div class="col-md-4 input-group offset-4">

					<input type="text" class="form-control" placeholder="search ..." id="search" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
					<div class="input-group-append">
						<span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-search"></i></span>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h4><b>Folders</b></h4>
			</div>
		</div>
		<hr>
		<div class="row" style="justify-content:space-evenly">
			<?php
			while ($row = $folders->fetch_assoc()):
			?>
				<div class="card col-md-3 mt-2 ml-2 mr-2 mb-2 folder-item" data-id="<?php echo $row['id'] ?>">
					<div class="card-body">
						<large><span><i class="fa fa-folder"></i></span><b class="to_folder"> <?php echo $row['name'] ?></b></large>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
		<hr>
		<div class="row">
			<div class="card col-md-12">
				<div class="card-body">
					<table width="100%">
						<tr>
							<th width="40%" class="">Filename</th>
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
										<large><span><i class="fa <?php echo $icon ?>"></i></span><b class="to_file"> <?php echo $name ?></b></large>
										<input type="text" class="rename_file" value="<?php echo $row['name'] ?>" data-id="<?php echo $row['id'] ?>" data-type="<?php echo $row['file_type'] ?>" style="display: none">
									</a>
								</td>
								<td><i class="to_file"><?php echo date('Y/m/d h:i A', strtotime($row['date_updated'])) ?></i></td>
								<td><i class="to_file"><?php echo $row['description'] ?></i></td>
							</tr>

						<?php endwhile; ?>
					</table>

				</div>
			</div>

		</div>
	</div>
</div>
<div id="menu-folder-clone" style="display: none;">
	<a href="javascript:void(0)" class="custom-menu-list file-option edit">Rename</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option delete">Delete</a>
</div>
<div id="menu-file-clone" style="display: none;">
	<a href="javascript:void(0)" class="custom-menu-list file-option edit"><span><i class="fa fa-edit"></i> </span>Rename</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option download"><span><i class="fa fa-download"></i> </span>Download</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option delete"><span><i class="fa fa-trash"></i> </span>Delete</a>
</div>

<script>
	$('#new_folder').click(function() {
		uni_modal('', 'manage_folder.php?fid=<?php echo $folder_parent ?>')
	})
	$('#new_file').click(function() {
		uni_modal('', 'manage_files.php?fid=<?php echo $folder_parent ?>')
	})
	$('.folder-item').click(function() {
		location.href = 'index.php?page=files&fid=' + $(this).attr('data-id')
	})
	$('.folder-item').bind("contextmenu", function(event) {
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

		$("div.custom-menu .edit").click(function(e) {
			e.preventDefault()
			uni_modal('Rename Folder', 'manage_folder.php?fid=<?php echo $folder_parent ?>&id=' + $(this).attr('data-id'))
		})
		$("div.custom-menu .delete").click(function(e) {
			e.preventDefault()
			_conf("Are you sure to delete this Folder?", 'delete_folder', [$(this).attr('data-id')])
		})
	})

	//FILE
	$('.file-item').bind("contextmenu", function(event) {
		event.preventDefault();

		$('.file-item').removeClass('active')
		$(this).addClass('active')
		$("div.custom-menu").hide();
		var custom = $("<div class='custom-menu file'></div>")
		custom.append($('#menu-file-clone').html())
		custom.find('.edit').attr('data-id', $(this).attr('data-id'))
		custom.find('.delete').attr('data-id', $(this).attr('data-id'))
		custom.find('.download').attr('data-id', $(this).attr('data-id'))
		custom.appendTo("body")
		custom.css({
			top: event.pageY + "px",
			left: event.pageX + "px"
		});

		$("div.file.custom-menu .edit").click(function(e) {
			e.preventDefault()
			$('.rename_file[data-id="' + $(this).attr('data-id') + '"]').siblings('large').hide();
			$('.rename_file[data-id="' + $(this).attr('data-id') + '"]').show();
		})
		$("div.file.custom-menu .delete").click(function(e) {
			e.preventDefault()
			_conf("Are you sure to delete this file?", 'delete_file', [$(this).attr('data-id')])
		})
		$("div.file.custom-menu .download").click(function(e) {
			e.preventDefault()
			window.open('download.php?id=' + $(this).attr('data-id'))
		})

		$('.rename_file').keypress(function(e) {
			var _this = $(this)
			if (e.which == 13) {
				start_load()
				$.ajax({
					url: 'ajax.php?action=file_rename',
					method: 'POST',
					data: {
						id: $(this).attr('data-id'),
						name: $(this).val(),
						type: $(this).attr('data-type'),
						folder_id: '<?php echo $folder_parent ?>'
					},
					success: function(resp) {
						if (typeof resp != undefined) {
							resp = JSON.parse(resp);
							if (resp.status == 1) {
								_this.siblings('large').find('b').html(resp.new_name);
								end_load();
								_this.hide()
								_this.siblings('large').show()
							}
						}
					}
				})
			}
		})

	})
	//FILE


	$('.file-item').click(function() {
		if ($(this).find('input.rename_file').is(':visible') == true)
			return false;
		uni_modal($(this).attr('data-name'), 'manage_files.php?<?php echo $folder_parent ?>&id=' + $(this).attr('data-id'))
	})
	$(document).bind("click", function(event) {
		$("div.custom-menu").hide();
		$('#file-item').removeClass('active')

	});
	$(document).keyup(function(e) {

		if (e.keyCode === 27) {
			$("div.custom-menu").hide();
			$('#file-item').removeClass('active')

		}

	});
	$(document).ready(function() {
		$('#search').keyup(function() {
			var _f = $(this).val().toLowerCase()
			$('.to_folder').each(function() {
				var val = $(this).text().toLowerCase()
				if (val.includes(_f))
					$(this).closest('.card').toggle(true);
				else
					$(this).closest('.card').toggle(false);


			})
			$('.to_file').each(function() {
				var val = $(this).text().toLowerCase()
				if (val.includes(_f))
					$(this).closest('tr').toggle(true);
				else
					$(this).closest('tr').toggle(false);


			})
		})
	})

	function delete_folder($id) {
		start_load();
		$.ajax({
			url: 'ajax.php?action=delete_folder',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Folder successfully deleted.", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)
				}
			}
		})
	}

	function delete_file($id) {
		start_load();
		$.ajax({
			url: 'ajax.php?action=delete_file',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Folder successfully deleted.", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)
				}
			}
		})
	}
	$('.dynamic-item').append(function() {
		return $(this).next().find('.dynamic-item')
	})
</script>