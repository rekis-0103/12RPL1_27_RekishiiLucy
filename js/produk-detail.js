// produk-detail.js - menangani halaman detail produk dari database

// Ambil product ID dari URL
const urlParams = new URLSearchParams(window.location.search);
const productId = urlParams.get('id');

// Jalankan setelah DOM siap
document.addEventListener('DOMContentLoaded', function () {
    loadProductDetails(productId);
});

async function loadProductDetails(productId) {
    if (!productId) {
        showNotFoundMessage();
        return;
    }

    try {
        const response = await fetch(`ajax/get-product-detail.php?id=${productId}`);
        const data = await response.json();

        if (data.success && data.product) {
            const product = data.product;

            // Update judul halaman
            document.title = `${product.name} - PT Waindo Specterra`;

            const titleEl = document.getElementById('product-title');
            if (titleEl) titleEl.textContent = product.name;

            // Buat HTML detail produk
            const productDetailHTML = `
                <div class="product-detail-content">
                    <div class="product-detail-header">
                        <div class="breadcrumb">
                            <a href="index.php">Beranda</a>
                            <span>/</span>
                            <a href="produk.php">Produk</a>
                            <span>/</span>
                            <span>${product.category_name}</span>
                            <span>/</span>
                            <span>${product.name}</span>
                        </div>
                    </div>
                    
                    <div class="product-detail-main">
                        <div class="product-image-section">
                            <div class="product-main-image">
                                ${product.image ? 
                                    `<img src="${product.image}" alt="${product.name}" 
                                          onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                     <div class="image-placeholder" style="display:none;">
                                        <i class="fas fa-box"></i>
                                        <p>Gambar tidak tersedia</p>
                                     </div>` 
                                    : 
                                    `<div class="image-placeholder">
                                        <i class="fas fa-box"></i>
                                        <p>Gambar tidak tersedia</p>
                                     </div>`
                                }
                            </div>
                        </div>
                        
                        <div class="product-info-section">
                            <div class="product-header">
                                <h1>${product.name}</h1>
                                <div class="product-category-badge">
                                    <i class="fas fa-tag"></i>
                                    ${product.category_name}
                                </div>
                            </div>
                            
                            <div class="product-description">
                                <h3>Deskripsi Produk</h3>
                                <p>${product.description}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="back-to-products">
                        <a href="produk.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            Kembali ke Produk
                        </a>
                    </div>
                </div>
            `;

            const containerEl = document.getElementById('product-detail-container');
            if (containerEl) containerEl.innerHTML = productDetailHTML;

        } else {
            console.error('API Error:', data.error || 'Product not found');
            showNotFoundMessage();
        }
    } catch (error) {
        console.error('Error loading product details:', error);
        showNotFoundMessage();
    }
}

function showNotFoundMessage() {
    const notFoundEl = document.getElementById('product-not-found');
    const containerEl = document.getElementById('product-detail-container');
    const titleEl = document.getElementById('product-title');

    if (notFoundEl) notFoundEl.style.display = 'block';
    if (containerEl) containerEl.style.display = 'none';
    if (titleEl) titleEl.textContent = 'Produk Tidak Ditemukan';
}

function contactUs() {
    window.location.href = 'hubungi-kami.php';
}

function requestQuote() {
    const productName = document.getElementById('product-title')?.textContent || '';
    window.location.href = 'hubungi-kami.php?request=quote&product=' + encodeURIComponent(productName);
}