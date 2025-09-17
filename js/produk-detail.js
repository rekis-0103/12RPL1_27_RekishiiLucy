// produk-detail.js - untuk menangani halaman detail produk

// Get product ID from URL
const urlParams = new URLSearchParams(window.location.search);
const productId = urlParams.get('id');

// Load product details when page loads
document.addEventListener('DOMContentLoaded', function() {
	loadProductDetails(productId);
});

function loadProductDetails(productId) {
	// Get product data from produk.js
	const product = getProductById(productId);
	
	if (!product) {
		showNotFoundMessage();
		return;
	}
	
	// Update page title
	document.title = `${product.name} - PT Waindo Specterra`;
	document.getElementById('product-title').textContent = product.name;
	
	// Create product detail HTML
	const productDetailHTML = `
		<div class="product-detail-content">
			<div class="product-detail-header">
				<div class="breadcrumb">
					<a href="index.php">Beranda</a>
					<span>/</span>
					<a href="produk.php">Produk</a>
					<span>/</span>
					<span>${product.category}</span>
					<span>/</span>
					<span>${product.name}</span>
				</div>
			</div>
			
			<div class="product-detail-main">
				<div class="product-image-section">
					<div class="product-main-image">
						${product.image ? 
							`<img src="${product.image}" alt="${product.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
							 <div class="image-placeholder" style="display:none;">
								<i class="fas fa-image"></i>
								<p>Gambar tidak tersedia</p>
							 </div>` 
							: 
							`<div class="image-placeholder">
								<i class="fas fa-image"></i>
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
							${product.category}
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
	
	document.getElementById('product-detail-container').innerHTML = productDetailHTML;
}

function showNotFoundMessage() {
	document.getElementById('product-not-found').style.display = 'block';
	document.getElementById('product-title').textContent = 'Produk Tidak Ditemukan';
}

function showTab(tabName) {
	// Hide all tab panes
	document.querySelectorAll('.tab-pane').forEach(pane => {
		pane.classList.remove('active');
	});
	
	// Remove active class from all tab buttons
	document.querySelectorAll('.tab-btn').forEach(btn => {
		btn.classList.remove('active');
	});
	
	// Show selected tab pane
	document.getElementById(tabName + '-tab').classList.add('active');
	
	// Add active class to clicked button
	event.target.classList.add('active');
}

function contactUs() {
	window.location.href = 'hubungi-kami.php';
}

function requestQuote() {
	window.location.href = 'hubungi-kami.php?request=quote&product=' + encodeURIComponent(document.getElementById('product-title').textContent);
}