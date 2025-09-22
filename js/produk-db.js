// produk-db.js - JavaScript untuk produk dari database

// Function to load products by category
async function loadCategoryProducts(categoryKey) {
    try {
        const response = await fetch(`ajax/get-products.php?category=${categoryKey}`);
        const data = await response.json();
        
        if (data.success) {
            showCategoryProducts(data.category, data.products);
        } else {
            console.error('Error loading products:', data.error);
            showEmptyCategory(categoryKey);
        }
    } catch (error) {
        console.error('Network error:', error);
        showEmptyCategory(categoryKey);
    }
}

// Function to show category products
function showCategoryProducts(category, products) {
    const displayArea = document.getElementById("category-products-display");
    const titleElement = document.getElementById("category-title");
    const descriptionElement = document.getElementById("category-description");
    const productsGrid = document.getElementById("products-grid");

    // Set category info
    titleElement.textContent = category.category_name;
    descriptionElement.textContent = category.category_description;

    // Clear existing products
    productsGrid.innerHTML = "";

    // Add products
    if (products && products.length > 0) {
        products.forEach((product) => {
            const productCard = document.createElement("div");
            productCard.className = "individual-product clickable-product";

            productCard.innerHTML = `
                <div class="product-image">
                    ${product.image ? 
                        `<img src="${product.image}" alt="${product.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                         <div class="image-placeholder" style="display:none;">
                            <i class="${product.icon || 'fas fa-box'}"></i>
                            <p>Gambar tidak tersedia</p>
                         </div>` 
                        : 
                        `<div class="image-placeholder">
                            <i class="${product.icon || 'fas fa-box'}"></i>
                            <p>Gambar tidak tersedia</p>
                         </div>`
                    }
                </div>
                <div class="product-info">
                    <h4 class="product-name">${product.name}</h4>
                    <p class="product-description">${product.description}</p>
                    <div class="product-action">
                        <button class="btn-detail">Lihat Detail</button>
                    </div>
                </div>
            `;

            // Add click event to detail button
            const btnDetail = productCard.querySelector(".btn-detail");
            btnDetail.addEventListener("click", () => {
                window.location.href = `produk-detail.php?id=${product.product_id}`;
            });

            productsGrid.appendChild(productCard);
        });
    } else {
        // Show empty state
        productsGrid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b;">
                <i class="fas fa-box" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <h4>Belum Ada Produk</h4>
                <p>Kategori ini belum memiliki produk.</p>
            </div>
        `;
    }

    // Show the display area and scroll into view
    displayArea.classList.add("active");
    setTimeout(() => {
        const yOffset = -80; // navbar height
        const y = displayArea.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({ top: y, behavior: "smooth" });
    }, 100);
}

// Function to show empty category
function showEmptyCategory(categoryKey) {
    const displayArea = document.getElementById("category-products-display");
    const titleElement = document.getElementById("category-title");
    const descriptionElement = document.getElementById("category-description");
    const productsGrid = document.getElementById("products-grid");

    titleElement.textContent = "Kategori Produk";
    descriptionElement.textContent = "Tidak dapat memuat produk kategori ini.";
    
    productsGrid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #ef4444;">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem;"></i>
            <h4>Gagal Memuat Produk</h4>
            <p>Terjadi kesalahan saat memuat produk. Silakan coba lagi.</p>
        </div>
    `;

    displayArea.classList.add("active");
}

// Function to close category display
function closeCategoryDisplay() {
    const displayArea = document.getElementById("category-products-display");
    displayArea.classList.remove("active");
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
}

// Function to get product by ID (for detail page)
async function getProductById(productKey) {
    try {
        const response = await fetch(`ajax/get-product-detail.php?key=${productKey}`);
        const data = await response.json();
        
        if (data.success) {
            return data.product;
        } else {
            return null;
        }
    } catch (error) {
        console.error('Error getting product:', error);
        return null;
    }
}

// Initialize product page functionality
document.addEventListener("DOMContentLoaded", function () {
    // Add event listeners to category buttons
    document.querySelectorAll(".category-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const category = this.getAttribute("data-category");
            loadCategoryProducts(category);
        });
    });

    // Category card hover effects
    document.querySelectorAll(".category-card").forEach((card) => {
        card.addEventListener("mouseenter", function () {
            this.style.transform = "translateY(-10px) scale(1.02)";
        });

        card.addEventListener("mouseleave", function () {
            this.style.transform = "translateY(0) scale(1)";
        });
    });

    // Add fade-in animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = "1";
                entry.target.style.transform = "translateY(0)";
            }
        });
    }, observerOptions);

    // Observe fade-in elements
    document.querySelectorAll(".fade-in-up").forEach((el) => {
        el.style.opacity = "0";
        el.style.transform = "translateY(30px)";
        el.style.transition = "opacity 0.6s ease, transform 0.6s ease";
        observer.observe(el);
    });
});

// Parallax effect for page header
window.addEventListener("scroll", () => {
    const scrolled = window.pageYOffset;
    const header = document.querySelector(".page-header");
    const speed = scrolled * 0.5;

    if (header) {
        header.style.transform = `translateY(${speed}px)`;
    }
});

// Loading animation
window.addEventListener("load", () => {
    document.body.classList.add("loaded");

    // Trigger animations for elements in viewport
    document.querySelectorAll(".fade-in-up").forEach((el, index) => {
        setTimeout(() => {
            el.style.opacity = "1";
            el.style.transform = "translateY(0)";
        }, index * 100);
    });
});

// Search functionality
function filterProducts(searchTerm) {
    const products = document.querySelectorAll(".individual-product");
    const categories = document.querySelectorAll(".category-card");

    products.forEach((product) => {
        const name = product.querySelector(".product-name");
        if (name) {
            const productName = name.textContent.toLowerCase();
            if (productName.includes(searchTerm.toLowerCase())) {
                product.style.display = "block";
            } else {
                product.style.display = "none";
            }
        }
    });
}

// Utility function to show notifications
function showNotification(message, type = "success") {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === "success" ? "#10b981" : "#ef4444"};
        color: white;
        padding: 1rem 2rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        animation: slideInRight 0.3s ease-out;
        max-width: 300px;
    `;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = "slideOutRight 0.3s ease-in";
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animations for notifications
const notificationStyle = document.createElement("style");
notificationStyle.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(notificationStyle);