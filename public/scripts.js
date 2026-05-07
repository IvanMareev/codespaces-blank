let currentPage = 0;
const limit = 20;

function sortImages(order) {
    const params = new URLSearchParams();
    params.set('sort', order);
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.pushState({}, '', newUrl);
    document.getElementById('sortAscBtn').disabled = order === 'asc';
    document.getElementById('sortDescBtn').disabled = order === 'desc';

    loadImages();
}

async function loadImages() {
    try {
        const queryParams = new URLSearchParams(window.location.search);
        const sortValue = queryParams.get('sort');
        const response = await fetch('/api/images.php?limit=' + limit + '&offset=' + (currentPage * limit) + (sortValue ? `&sort=${sortValue}` : ''));
        const images = await response.json();
        displayImages(images);
        console.log(images);

        updatePagination(images.length);
    } catch (error) {
        console.error('Error loading images:', error);
    }
}

function displayImages(images) {
    const gallery = document.getElementById('gallery');
    gallery.innerHTML = '';
    images.forEach(image => {
        const item = document.createElement('div');
        item.className = 'image-item';
        item.innerHTML = `
            <img src="${image.s3_url}" alt="${image.original_name}">
            <div class="image-details">
                <p>Дата загрузки:<br> ${image.uploaded_at}</p>
                <button class="delete-btn" onclick="deleteImage(${image.id})">Удалить</button>
            </div>
        `;
        gallery.appendChild(item);
    });
}

async function deleteImage(id) {
    if (!confirm('Удалить изображение?')) return;

    try {
        const response = await fetch('/api/images.php/' + id, { method: 'DELETE' });
        if (response.ok) {
            loadImages();
        } else {
            alert('Ошибка удаления');
        }
    } catch (error) {
        console.error('Error deleting image:', error);
    }
}

function updatePagination(imageCount) {
    document.getElementById('pageInfo').textContent = `Страница ${currentPage + 1}`;
    document.getElementById('prevBtn').disabled = currentPage === 0;
    document.getElementById('nextBtn').disabled = imageCount < limit;
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentPage > 0) {
            currentPage--;
            loadImages();
        }
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        currentPage++;
        loadImages();
    });

    document.getElementById('uploadForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const progress = document.getElementById('progress');
        const progressBar = progress.querySelector('progress');
        const progressText = document.getElementById('progressText');

        progress.style.display = 'block';

        try {
            const xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.value = percent;
                    progressText.textContent = percent + '%';
                }
            });

            xhr.addEventListener('load', () => {
                if (xhr.status === 201) {
                    loadImages();
                    e.target.reset();
                } else {
                    alert('Ошибка загрузки');
                }
                progress.style.display = 'none';
            });

            xhr.open('POST', '/api/images.php');
            xhr.send(formData);
        } catch (error) {
            console.error('Error uploading image:', error);
            progress.style.display = 'none';
        }
    });

    loadImages();
});