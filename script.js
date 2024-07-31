document.addEventListener('DOMContentLoaded', function() {
    const uploadBox = document.getElementById('uploadBox');
    const fileList = document.getElementById('fileList');
    const fileListContainer = document.getElementById('fileListContainer');
    const imagePreview = document.getElementById('imagePreview');
    const fileInput = document.getElementById('fileInput');
    const filePicker = document.getElementById('filePicker');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const notification = document.getElementById('notification');

    uploadBox.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadBox.classList.add('dragover');
    });

    uploadBox.addEventListener('dragleave', function(e) {
        uploadBox.classList.remove('dragover');
    });

    uploadBox.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadBox.classList.remove('dragover');

        const files = e.dataTransfer.files;
        for (let i = 0; i < files.length; i++) {
            uploadFile(files[i]);
        }
    });

    filePicker.addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', function() {
        const files = fileInput.files;
        for (let i = 0; i < files.length; i++) {
            uploadFile(files[i]);
        }
    });

    function showNotification(message, type) {
        notification.textContent = message;
        notification.className = 'notification ' + type;
        notification.style.display = 'block';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 5000);
    }

    function uploadFile(file) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();

        formData.append('file', file);

        xhr.open('POST', 'upload.php', true);

        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                progressContainer.style.display = 'block'; // Tampilkan progress bar
                const percentComplete = (event.loaded / event.total) * 100;
                progressBar.style.width = percentComplete + '%';
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                const li = document.createElement('li');
                const ext = file.name.split('.').pop().toLowerCase();
                let icon;
                switch (ext) {
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'gif':
                        icon = 'fa-file-image';
                        break;
                    case 'zip':
                    case 'rar':
                        icon = 'fa-file-archive';
                        break;
                    default:
                        icon = 'fa-file';
                }
                li.innerHTML = '<i class="fas ' + icon + '"></i> <a href="download.php?file=' + encodeURIComponent(file.name) + '" data-type="' + ext + '" data-path="uploads/' + file.name + '" download>' + file.name + '</a> <button class="delete-btn" data-file="' + encodeURIComponent(file.name) + '">Delete</button>';
                fileList.appendChild(li);
                progressBar.style.width = '0%';
                progressContainer.style.display = 'none'; // Sembunyikan progress bar
                showNotification('File uploaded successfully!', 'success');
            } else {
                showNotification('Error uploading file.', 'error');
            }
        };

        xhr.onerror = function() {
            showNotification('Error uploading file.', 'error');
        };

        xhr.send(formData);
    }

    fileListContainer.addEventListener('mouseover', function(e) {
        if (e.target.tagName === 'A' && e.target.dataset.type.match(/(jpg|jpeg|png|gif)/)) {
            const imgSrc = e.target.dataset.path;
            imagePreview.innerHTML = '<img src="' + imgSrc + '">';
            imagePreview.style.display = 'block';
        }
    });

    fileListContainer.addEventListener('mousemove', function(e) {
        if (imagePreview.style.display === 'block') {
            imagePreview.style.top = e.pageY + 15 + 'px';
            imagePreview.style.left = e.pageX + 15 + 'px';
        }
    });

    fileListContainer.addEventListener('mouseout', function(e) {
        if (e.target.tagName === 'A' && e.target.dataset.type.match(/(jpg|jpeg|png|gif)/)) {
            imagePreview.style.display = 'none';
            imagePreview.innerHTML = '';
        }
    });

    fileListContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-btn')) {
            const fileLink = e.target.previousElementSibling;
            const fileName = fileLink.textContent.trim();
            const encryptedFile = e.target.dataset.file;

            if (confirm('Are you sure you want to delete this file?')) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'delete.php?file=' + encodeURIComponent(encryptedFile), true);

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        showNotification('File deleted successfully!', 'success');
                        e.target.parentElement.remove();
                    } else {
                        showNotification('Error deleting file.', 'error');
                    }
                };

                xhr.onerror = function() {
                    showNotification('Error deleting file.', 'error');
                };

                xhr.send();
            }
        }
    });
});
