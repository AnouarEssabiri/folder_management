<?php // Function to get folders recursively
include 'db_connect.php';

function fetchFoldersAndFiles($parent_id = 0, $conn)
{
    $folders = [];
    $query = $conn->query("SELECT * FROM folders WHERE parent_id = $parent_id");
    while ($row = $query->fetch_assoc()) {
        $row['children'] = fetchFoldersAndFiles($row['id'], $conn); // Recursively fetch child folders
        $row['files'] = [];
        $fileQuery = $conn->query("SELECT * FROM files WHERE folder_id = {$row['id']}");
        while ($fileRow = $fileQuery->fetch_assoc()) {
            $row['files'][] = $fileRow; // Add files to the folder
        }
        $folders[] = $row;
    }
    return $folders;
}

// Fetch the root folders, their children, and associated files
$folderTree = fetchFoldersAndFiles(0, $conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draggable Folder Arborescence</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f7f7f7;
            color: #333;
        }

        .folder-list {
            list-style-type: none;
            padding-left: 20px;
        }

        .folder {
            display: flex;
            align-items: center;
            cursor: pointer;
            margin: 5px 0;
            padding: 5px;
            border-radius: 5px;
        }

        .nested {
            display: none;
            /* Hide nested folders by default */
            margin-left: 20px;
        }

        .open>.nested {
            display: block;
            /* Show nested folders when parent is open */
        }

        .folder-icon {
            margin-right: 10px;
            width: 16px;
            height: 16px;
            background-size: contain;
            background-repeat: no-repeat;
        }

        .folder-icon.closed {
            background-image: url('./assets/img/folder1.png');
            /* Closed folder icon */
        }

        .folder-icon.open {
            background-image: url('https://cdn-icons-png.flaticon.com/512/561/561128.png');
            /* Open folder icon */
        }
    </style>
</head>

<body>
    <!-- <a href="./assets/uploads/1733323140_CC_gestion_project.pdf" target="_blank">view document</a> -->
    <h1>Draggable Folder Arborescence</h1>
    <?php
    function renderFoldersAndFilesWithColors($folders)
    {
        echo '<ul class="folder-list">';
        foreach ($folders as $folder) {
            // Determine the folder color: red if it contains files, otherwise black
            $folderColor = !empty($folder['files']) ? 'red' : 'black';

            echo '<li draggable="true" data-folder-id="' . $folder['id'] . '" class="draggable-folder">';
            echo '<span class="folder" style="color:' . $folderColor . ';">
                <i class="fa fa-folder closed" style="margin-right: 5px;"></i>
                <span>' . htmlspecialchars($folder['name']) . '</span>
              </span>';

            // Render files in the current folder
            if (!empty($folder['files'])) {
                echo '<ul class="file-list">';
                foreach ($folder['files'] as $file) {
                    echo '<li draggable="true" style="list-style-type: none;" data-file-id="' . $file['id'] . '" class="draggable-file">
                        <span class="file" style="color: #2caaa6;">
                            <i class="fa fa-file"></i>
                            <span>' . htmlspecialchars($file['name'] . '.' . $file['file_type']) . '</span>
                        </span>
                      </li>';
                }
                echo '</ul>';
            }

            // Render child folders
            if (!empty($folder['children'])) {
                echo '<div class="nested">';
                renderFoldersAndFilesWithColors($folder['children']); // Recursively render children
                echo '</div>';
            }
            echo '</li>';
        }
        echo '</ul>';
    }


    // Render the folder and file structure
    renderFoldersAndFilesWithColors($folderTree);
    ?>



    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const folderElements = document.querySelectorAll('.folder');

            folderElements.forEach(folder => {
                folder.addEventListener('click', (e) => {
                    e.stopPropagation(); // Prevents triggering parent clicks
                    const parent = folder.closest('li');
                    const icon = folder.querySelector('.folder-icon');
                    parent.classList.toggle('open'); // Toggle open class
                    icon.classList.toggle('open');
                    icon.classList.toggle('closed');
                });
            });

            // Drag-and-Drop Logic
            let draggedElement = null;

            document.addEventListener('dragstart', (event) => {
                if (event.target.classList.contains('draggable-folder')) {
                    draggedElement = event.target;
                    event.target.classList.add('dragging');
                }
            });

            document.addEventListener('dragend', (event) => {
                if (event.target.classList.contains('draggable-folder')) {
                    event.target.classList.remove('dragging');
                    draggedElement = null;
                }
            });

            document.addEventListener('dragover', (event) => {
                event.preventDefault();
                const target = event.target.closest('.draggable-folder');
                if (target && target !== draggedElement) {
                    target.classList.add('drag-over');
                }
            });

            document.addEventListener('dragleave', (event) => {
                const target = event.target.closest('.draggable-folder');
                if (target) {
                    target.classList.remove('drag-over');
                }
            });

            document.addEventListener('drop', (event) => {
                event.preventDefault();
                const target = event.target.closest('.draggable-folder');
                if (target && target !== draggedElement) {
                    target.classList.remove('drag-over');
                    const draggedFolderId = draggedElement.getAttribute('data-folder-id');
                    const targetFolderId = target.getAttribute('data-folder-id');

                    // Update the DOM (move the dragged folder)
                    target.parentNode.insertBefore(draggedElement, target.nextSibling);

                    // TODO: Update the database with the new parent-child relationship
                    console.log(`Moved folder ${draggedFolderId} under folder ${targetFolderId}`);
                }
            });
        });
    </script>
</body>

</html>