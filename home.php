<style>
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

	span.card-icon {
		position: absolute;
		font-size: 3em;
		bottom: .2em;
		color: #ffffff80;
	}

	.file-item {
		cursor: pointer;
	}

	a.custom-menu-list:hover,
	.file-item:hover,
	.file-item.active {
		background: #80808024;
	}

	a.custom-menu-list span.icon {
		width: 1em;
		margin-right: 5px
	}
</style>
<nav aria-label="fil d'Ariane">
	<ol class="breadcrumb">
		<li class="breadcrumb-item text-info">Accueil</li>
	</ol>
</nav>
<div class="containe-fluid">
	<?php include('db_connect.php');
	$files = $conn->query("SELECT * FROM files WHERE DATE(date_updated) = CURDATE() ORDER BY DATE(date_updated) DESC;");
	?>
	<div class="row">
		<div class="col-lg-12">
			<div class="card col-md-4 offset-2 bg-info float-left">
				<div class="card-body text-white">
					<h4><b>Utilisateurs</b></h4>
					<hr>
					<span class="card-icon"><i class="fa fa-users"></i></span>
					<h3 class="text-right"><b><?php echo $conn->query('SELECT * FROM users')->num_rows ?></b></h3>
				</div>
			</div>
			<div class="card col-md-4 offset-2 bg-info ml-4 float-left">
				<div class="card-body text-white">
					<h4><b>Fichiers</b></h4>
					<hr>
					<span class="card-icon"><i class="fa fa-file"></i></span>
					<h3 class="text-right"><b><?php echo $conn->query('SELECT * FROM files')->num_rows ?></b></h3>
				</div>
			</div>
		</div>
	</div>
	<div class="row mt-3 ml-3 mr-3">
		<h1 style="margin-block:20px;">Recent Fichiers</h1>
		<div class="card col-md-12">
			<div class="card-body">
				<table width="100%">
					<tr>
						<th width="40%" class="">Nom du fichier</th>
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
						$audio_arr = array('mp3', 'wav', 'aac', 'flac', 'ogg');
						$video_arr = array('mp4', 'avi', 'mkv', 'mov', 'flv', 'webm');
						$text_arr = array('txt', 'rtf', 'csv', 'md', 'log');
						$ppt_arr = array('ppt', 'pptx');  // Extensions PowerPoint
						$access_arr = array('mdb', 'accdb');  // Extensions Access
						$publisher_arr = array('pub');  // Extensions Publisher
						$icon = 'fa-file';

						if (in_array(strtolower($row['file_type']), $img_arr)) {
							$icon = 'fa-image';  // Fichiers image
						} elseif (in_array(strtolower($row['file_type']), $doc_arr)) {
							$icon = 'fa-file-word';  // Documents Word
						} elseif (in_array(strtolower($row['file_type']), $pdf_arr)) {
							$icon = 'fa-file-pdf';  // Fichiers PDF
						} elseif (in_array(strtolower($row['file_type']), ['xlsx', 'xls', 'xlsm', 'xlsb', 'xltm', 'xlt', 'xla', 'xlr'])) {
							$icon = 'fa-file-excel';  // Fichiers Excel
						} elseif (in_array(strtolower($row['file_type']), ['zip', 'rar', 'tar'])) {
							$icon = 'fa-file-archive';  // Fichiers archive
						} elseif (in_array(strtolower($row['file_type']), $audio_arr)) {
							$icon = 'fa-file-audio';  // Fichiers audio
						} elseif (in_array(strtolower($row['file_type']), $video_arr)) {
							$icon = 'fa-file-video';  // Fichiers vidéo
						} elseif (in_array(strtolower($row['file_type']), $text_arr)) {
							$icon = 'fa-file-alt';  // Fichiers texte
						} elseif (in_array(strtolower($row['file_type']), $ppt_arr)) {
							$icon = 'fa-file-powerpoint';  // Fichiers PowerPoint
						} elseif (in_array(strtolower($row['file_type']), $access_arr)) {
							$icon = 'fa-file-database';  // Fichiers Access
						} elseif (in_array(strtolower($row['file_type']), $publisher_arr)) {
							$icon = 'fa-file-pdf';  // Fichiers Publisher (peut utiliser l'icône PDF si vous préférez)
						}

						?>
						<tr class='file-item' data-id="<?php echo $row['id'] ?>" data-name="<?php echo $name ?>">
							<td>
								<a href="http://localhost/folder_management/assets/uploads/<?php echo $row['file_path'] ?>"
									target="_blank">
									<large><span><i class="fa <?php echo $icon ?>"></i></span><b class="to_file">
											<?php echo $name ?></b></large>
									<input type="text" class="rename_file" value="<?php echo $row['name'] ?>"
										data-id="<?php echo $row['id'] ?>" data-type="<?php echo $row['file_type'] ?>"
										style="display: none">
								</a>
							</td>
							<td><i class="to_file"><?php echo date('Y/m/d h:i A', strtotime($row['date_updated'])) ?></i>
							</td>
							<td><i class="to_file"><?php echo $row['description'] ?></i></td>
						</tr>

					<?php endwhile; ?>
				</table>

			</div>
		</div>

	</div>
</div>

</div>
<div id="menu-file-clone" style="display: none;">
	<a href="javascript:void(0)" class="custom-menu-list file-option download"><span><i class="fa fa-download"></i>
		</span>Télécharger</a>
	<!-- <a href="javascript:void(0)" class="custom-menu-list file-option delete"><span><i class="fa fa-trash"></i>
		</span>Supprimer</a> -->
</div>
<script>
	//FICHIER
	$('.file-item').bind("contextmenu", function (event) {
		event.preventDefault();

		$('.file-item').removeClass('active')
		$(this).addClass('active')
		$("div.custom-menu").hide();
		var custom = $("<div class='custom-menu file'></div>")
		custom.append($('#menu-file-clone').html())
		custom.find('.download').attr('data-id', $(this).attr('data-id'))
		custom.find('.delete').attr('data-id', $(this).attr('data-id'))
		custom.appendTo("body")
		custom.css({
			top: event.pageY + "px",
			left: event.pageX + "px"
		});

		$("div.custom-menu .delete").click(function (e) {
			e.preventDefault()
			_conf("Voulez-vous vraiment supprimer ce fichier?", "delete_file", [$(this).attr('data-id')])
		})

		$("div.file.custom-menu .download").click(function (e) {
			e.preventDefault()
			window.open('download.php?id=' + $(this).attr('data-id'))

		})

	})
	$(document).bind("click", function (event) {
		$("div.custom-menu").hide();
		$('#file-item').removeClass('active')

	});
	$(document).keyup(function (e) {

		if (e.keyCode === 27) {
			$("div.custom-menu").hide();
			$('#file-item').removeClass('active')

		}
	})
	function delete_file(id) {
		start_load()
		$.ajax({
			url: 'delete_file.php',
			method: 'POST',
			data: {
				id: id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Fichier supprimé avec succès", "success");
					setTimeout(function() {
						location.reload()
					}, 1500)
				}
			}
		})
	}
</script>