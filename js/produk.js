const categoryProducts = {
  'geomatic-applications': {
    title: 'Geomatic Applications',
    description: 'Solusi aplikasi geomatika untuk survei, pemetaan, dan analisis geospasial',
    products: [
      {
        id: 'enterprise-rack-server',
        name: 'Enterprise Rack Server',
        image: 'assets/remotesesing.jpg',
        description: 'Server rack enterprise dengan performa tinggi yang dirancang khusus untuk mendukung aplikasi geomatika skala besar. Menyediakan kapasitas penyimpanan besar, kecepatan pemrosesan data spasial yang optimal, serta keandalan 24/7 untuk mendukung kebutuhan survei, pemetaan, hingga analisis geospasial yang kompleks.',
        features: [],
        category: 'Geomatic Applications',
        detailUrl: 'produk-detail.php?id=enterprise-rack-server'
      },
      {
        id: 'geographic-information-system',
        name: 'Geographic Information System',
        image: 'assets/Geograpic-Information-System.jpg',
        icon: 'fas fa-database',
        description: 'Sistem informasi geografis komprehensif untuk analisis dan visualisasi data spasial.',
        features: [],
        category: 'Geomatic Applications',
        detailUrl: 'produk-detail.php?id=geographic-information-system'
      },
      {
        id: 'peta-rbi-tanjung-lesung',
        name: 'Peta RBI Skala 1:5000 Wilayah KEK Tanjung Lesung Menggunakan Data Foto Udara dan Lidar Tahun 2017',
        image: 'assets/Fotogrametri.jpg',
        icon: 'fas fa-map',
        description: 'Peta Rupa Bumi Indonesia skala 1:5000 untuk wilayah KEK Tanjung Lesung menggunakan teknologi fotogrametri dan LiDAR.',
        features: [],
        category: 'Geomatic Applications',
        detailUrl: 'produk-detail.php?id=peta-rbi-tanjung-lesung'
      },
      {
        id: 'lidar-survey',
        name: 'Lidar Survey',
        image: 'assets/lidar1.jpg',
        icon: 'fas fa-radar',
        description: 'Layanan survei LiDAR untuk pemetaan topografi dan analisis elevasi dengan akurasi tinggi.',
        features: [],
        category: 'Geomatic Applications',
        detailUrl: 'produk-detail.php?id=lidar-survey'
      },
      {
        id: 'topographic-survey',
        name: 'Topographic Survey',
        image: 'assets/topograpsurvey.jpg',
        icon: 'fas fa-mountain',
        description: 'Survei topografi untuk pemetaan kontur dan elevasi permukaan tanah.',
        features: [],
        category: 'Geomatic Applications',
        detailUrl: 'produk-detail.php?id=topographic-survey'
      },
      {
        id: 'studi-survey-alur',
        name: 'Studi Survey Alur',
        image: 'assets/Studi Survey Alur.jpg',
        icon: 'fas fa-route',
        description: 'Studi dan survei alur untuk perencanaan infrastruktur dan transportasi.',
        features: [],
        category: 'Geomatic Applications',
        detailUrl: 'produk-detail.php?id=studi-survey-alur'
      },
      {
        id: 'peta-sistem-lahan',
        name: 'Peta Sistem Lahan Skala 1:50.000 Provinsi Sumatera Selatan',
        image: 'assets/peta sistem lahan.jpg',
        icon: 'fas fa-seedling',
        description: 'Peta sistem lahan untuk perencanaan pertanian dan pengelolaan sumber daya alam.',
        features: [],
        category: 'Geomatic Applications',
        detailUrl: 'produk-detail.php?id=peta-sistem-lahan'
      },
      {
        id: 'peta-morfometri',
        name: 'Peta Morfometri Bentang Lahan 1:50.000 tahun 2018',
        image: 'assets/Peta Morfometri.jpg',
        icon: 'fas fa-mountain',
        description: 'Peta morfometri untuk analisis bentuk lahan dan karakteristik topografi.',
        features: [],
        category: 'Geomatic Applications',
        detailUrl: 'produk-detail.php?id=peta-morfometri'
      },
      {
        id: 'peta-penutup-lahan',
        name: 'Peta Penutup Lahan tahun 2016',
        image: 'assets/Peta Penutup Lahan.jpg',
        icon: 'fas fa-leaf',
        description: 'Peta penutup lahan untuk monitoring perubahan penggunaan lahan dan vegetasi.',
        features: [],
        category: 'Geomatic Applications',
        detailUrl: 'produk-detail.php?id=peta-penutup-lahan'
      },
      {
        id: 'peta-rbi-gunungmas',
        name: 'Peta RBI Skala 1:5000 Kab Gunungmas menggunakan data foto udara dan Lidar th 2019',
        image: 'assets/Fotogrametri2.jpg',
        icon: 'fas fa-map-marked-alt',
        description: 'Peta RBI skala 1:5000 untuk Kabupaten Gunungmas dengan teknologi fotogrametri dan LiDAR terbaru.',
        features: [],
        category: 'Geomatic Applications',
        detailUrl: 'produk-detail.php?id=peta-rbi-gunungmas'
      },
      {
        id: 'hidrographic-survey',
        name: 'Hidrographic Survey',
        image: 'assets/hidrograpsurvey1.jpg',
        icon: 'fas fa-water',
        description: 'Survei hidrografi untuk pemetaan dasar laut dan perairan.',
        features: [],
        category: 'Geomatic Applications',
        detailUrl: 'produk-detail.php?id=hidrographic-survey'
      }
    ]
  },
  'software-provider': {
    title: 'Software Provider',
    description: 'Software dan platform GIS terdepan untuk analisis geospasial dan pemetaan',
    products: [
      {
        id: 'arcgis-desktop',
        name: 'ArcGIS For Desktop',
        image: 'assets/arcgis_destop.jpg',
        icon: 'fas fa-desktop',
        description: 'Software GIS desktop terdepan untuk analisis geospasial, pemetaan, dan manajemen data.',
        features: [],
        category: 'Software Provider',
        detailUrl: 'produk-detail.php?id=arcgis-desktop'
      },
      {
        id: 'arcgis-enterprise',
        name: 'ArcGIS Enterprise',
        image: 'assets/arcgisportal.jpg',
        icon: 'fas fa-server',
        description: 'Platform GIS enterprise untuk organisasi besar dengan kemampuan sharing dan kolaborasi.',
        features: [],
        category: 'Software Provider',
        detailUrl: 'produk-detail.php?id=arcgis-enterprise'
      },
      {
        id: 'arcgis-mobile',
        name: 'ArcGIS Mobile',
        image: 'assets/arcgis_mobile.jpg',
        icon: 'fas fa-mobile-alt',
        description: 'Aplikasi GIS mobile untuk pengumpulan data lapangan dan navigasi.',
        features: [],
        category: 'Software Provider',
        detailUrl: 'produk-detail.php?id=arcgis-mobile'
      },
      {
        id: 'erdas-image',
        name: 'Erdas Image',
        image: 'assets/erdas image.jpg',
        icon: 'fas fa-satellite',
        description: 'Software pengolahan citra satelit dan remote sensing untuk analisis geospasial.',
        features: [],
        category: 'Software Provider',
        detailUrl: 'produk-detail.php?id=erdas-image'
      },
      {
        id: 'erdas-apollo',
        name: 'Erdas Apollo',
        image: 'assets/erdas apolo.jpg',
        icon: 'fas fa-rocket',
        description: 'Platform cloud untuk manajemen dan analisis citra satelit skala besar.',
        features: [],
        category: 'Software Provider',
        detailUrl: 'produk-detail.php?id=erdas-apollo'
      }
    ]
  },
  'enrm': {
    title: 'Environment & Natural Resources Management',
    description: 'Solusi manajemen lingkungan dan sumber daya alam untuk pembangunan berkelanjutan',
    products: [
      {
        id: 'coastal-zone-management',
        name: 'Coastal Zone Management',
        image: 'assets/coastal zone.jpg',
        icon: 'fas fa-water',
        description: 'Sistem manajemen zona pesisir untuk monitoring dan perlindungan lingkungan pantai.',
        features: [],
        category: 'Environment & Natural Resources Management',
        detailUrl: 'produk-detail.php?id=coastal-zone-management'
      },
      {
        id: 'forest-plantation-inventory',
        name: 'Forest & Plantation Inventory',
        image: 'assets/logo.png',
        icon: 'fas fa-tree',
        description: 'Sistem inventarisasi hutan dan perkebunan untuk pengelolaan sumber daya kehutanan.',
        features: [],
        category: 'Environment & Natural Resources Management',
        detailUrl: 'produk-detail.php?id=forest-plantation-inventory'
      },
      {
        id: 'natural-resources-accounting',
        name: 'Natural Resources Accounting',
        image: 'assets/logo.png',
        icon: 'fas fa-calculator',
        description: 'Sistem akuntansi sumber daya alam untuk valuasi ekonomi dan perencanaan pembangunan.',
        features: [],
        category: 'Environment & Natural Resources Management',
        detailUrl: 'produk-detail.php?id=natural-resources-accounting'
      },
      {
        id: 'environment-monitoring',
        name: 'Environment Monitoring',
        image: 'assets/logo.png',
        icon: 'fas fa-globe',
        description: 'Sistem monitoring lingkungan untuk pemantauan kualitas air, udara, dan tanah.',
        features: [],
        category: 'Environment & Natural Resources Management',
        detailUrl: 'produk-detail.php?id=environment-monitoring'
      }
    ]
  },
  'gis-data-provider': {
    title: 'GIS Data Provider',
    description: 'Penyedia data geospasial dan citra satelit untuk berbagai kebutuhan pemetaan',
    products: [
      {
        id: 'maxar',
        name: 'Maxar',
        image: 'assets/maxar.jpeg',
        icon: 'fas fa-satellite',
        description: 'Penyedia citra satelit resolusi tinggi untuk pemetaan dan monitoring global.',
        features: [],
        category: 'GIS Data Provider',
        detailUrl: 'produk-detail.php?id=maxar'
      },
      {
        id: 'planetscope',
        name: 'Planetscope',
        image: 'assets/logo.png',
        icon: 'fas fa-globe',
        description: 'Citra satelit harian untuk monitoring perubahan lahan dan pertanian.',
        features: [],
        category: 'GIS Data Provider',
        detailUrl: 'produk-detail.php?id=planetscope'
      },
      {
        id: 'radarsat',
        name: 'Radarsat',
        image: 'assets/radarsat.jpg',
        icon: 'fas fa-radar',
        description: 'Data radar satelit untuk monitoring cuaca dan analisis permukaan bumi.',
        features: [],
        category: 'GIS Data Provider',
        detailUrl: 'produk-detail.php?id=radarsat'
      },
      {
        id: 'scanned-map',
        name: 'Scanned Map',
        image: 'assets/scanned map.jpg',
        icon: 'fas fa-map',
        description: 'Peta digital hasil scanning untuk referensi historis dan analisis perubahan.',
        features: [],
        category: 'GIS Data Provider',
        detailUrl: 'produk-detail.php?id=scanned-map'
      },
      {
        id: 'vector-map',
        name: 'Vector Map',
        image: 'assets/vector map.jpg',
        icon: 'fas fa-vector-square',
        description: 'Data vektor untuk analisis spasial dan pembuatan peta tematik.',
        features: [],
        category: 'GIS Data Provider',
        detailUrl: 'produk-detail.php?id=vector-map'
      },
      {
        id: 'data-converter',
        name: 'Data Converter',
        image: 'assets/logo.png',
        icon: 'fas fa-exchange-alt',
        description: 'Tools konversi data geospasial antar format untuk kompatibilitas sistem.',
        features: [],
        category: 'GIS Data Provider',
        detailUrl: 'produk-detail.php?id=data-converter'
      },
      {
        id: 'poi-data',
        name: 'POI Data',
        image: 'assets/logo.png',
        icon: 'fas fa-map-marker-alt',
        description: 'Database Points of Interest untuk navigasi dan analisis lokasi.',
        features: [],
        category: 'GIS Data Provider',
        detailUrl: 'produk-detail.php?id=poi-data'
      }
    ]
  },
  'gis-information-technology': {
    title: 'GIS & Information Technology',
    description: 'Aplikasi dan sistem teknologi informasi geografis untuk berbagai platform',
    products: [
      {
        id: 'web-desktop-mobile-app',
        name: 'Web, Desktop, Mobile Application',
        image: 'assets/application.jpg',
        icon: 'fas fa-laptop-code',
        description: 'Pengembangan aplikasi GIS untuk platform web, desktop, dan mobile sesuai kebutuhan.',
        features: [],
        category: 'GIS & Information Technology',
        detailUrl: 'produk-detail.php?id=web-desktop-mobile-app'
      },
      {
        id: 'gps-tracking-system',
        name: 'GPS Tracking System',
        image: 'assets/logo.png',
        icon: 'fas fa-map-marker-alt',
        description: 'Sistem pelacakan GPS untuk monitoring kendaraan, aset, dan personel secara real-time.',
        features: [],
        category: 'GIS & Information Technology',
        detailUrl: 'produk-detail.php?id=gps-tracking-system'
      }
    ]
  },
};

// Function to get product by ID
function getProductById(productId) {
  for (const categoryKey in categoryProducts) {
    const category = categoryProducts[categoryKey];
    const product = category.products.find(p => p.id === productId);
    if (product) {
      return product;
    }
  }
  return null;
}

// Function to show category products
function showCategoryProducts(categoryKey) {
  const displayArea = document.getElementById('category-products-display');
  const titleElement = document.getElementById('category-title');
  const descriptionElement = document.getElementById('category-description');
  const productsGrid = document.getElementById('products-grid');

  const categoryData = categoryProducts[categoryKey];
  if (!categoryData) return;

  // Set category info
  titleElement.textContent = categoryData.title;
  descriptionElement.textContent = categoryData.description;

  // Clear existing products
  productsGrid.innerHTML = '';

  // Add products
  categoryData.products.forEach(product => {
    const productCard = document.createElement('div');
    productCard.className = 'individual-product clickable-product';
    productCard.onclick = () => window.location.href = product.detailUrl;
    productCard.innerHTML = `
      <div class="product-image">
        ${product.image ? 
          `<img src="${product.image}" alt="${product.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
           <i class="${product.icon}" style="display:none;"></i>` 
          : 
          `<i class="${product.icon}"></i>`
        }
      </div>
      <div class="product-info">
        <h4 class="product-name">${product.name}</h4>
        <p class="product-description">${product.description}</p>
        <div class="product-features">
          ${product.features.slice(0, 3).map(feature => `<span class="feature-tag">${feature}</span>`).join('')}
        </div>
        <div class="product-action">
          <button class="btn-detail">Lihat Detail</button>
        </div>
      </div>
    `;
    productsGrid.appendChild(productCard);
  });

  // Show the display area and scroll into view
  displayArea.classList.add('active');
  setTimeout(() => {
    displayArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }, 100);
}

// Function to close category display
function closeCategoryDisplay() {
  const displayArea = document.getElementById('category-products-display');
  displayArea.classList.remove('active');
}

// Initialize product page functionality
document.addEventListener('DOMContentLoaded', function() {
  // Add event listeners to category buttons
  document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const category = this.getAttribute('data-category');
      showCategoryProducts(category);
    });
  });

  // Category card interaction
  document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-10px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', function() {
      this.style.transform = 'translateY(0) scale(1)';
    });
  });

  // Add fade-in animation on scroll for product elements
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);

  // Observe product elements for animation
  document.querySelectorAll('.fade-in-up').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
  });
});

// Add CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
  .product-btn {
    position: relative;
    overflow: hidden;
  }
  
  .ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: scale(0);
    animation: rippleEffect 0.6s linear;
    pointer-events: none;
  }
  
  @keyframes rippleEffect {
    to {
      transform: scale(4);
      opacity: 0;
    }
  }
`;
document.head.appendChild(style);

// Parallax effect for page header
window.addEventListener('scroll', () => {
  const scrolled = window.pageYOffset;
  const header = document.querySelector('.page-header');
  const speed = scrolled * 0.5;
  
  if (header) {
    header.style.transform = `translateY(${speed}px)`;
  }
});

// Counter animation for statistics (utility function)
function animateCounter(element, target, duration = 2000) {
  let start = 0;
  const increment = target / (duration / 16);
  const timer = setInterval(() => {
    start += increment;
    element.textContent = Math.floor(start);
    if (start >= target) {
      element.textContent = target;
      clearInterval(timer);
    }
  }, 16);
}

// Loading animation
window.addEventListener('load', () => {
  document.body.classList.add('loaded');
  
  // Trigger animations for elements in viewport
  document.querySelectorAll('.fade-in-up').forEach((el, index) => {
    setTimeout(() => {
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    }, index * 100);
  });
});

// Search functionality (for future use)
function filterProducts(searchTerm) {
  const products = document.querySelectorAll('.individual-product');
  const categories = document.querySelectorAll('.category-card');
  
  products.forEach(product => {
    const name = product.querySelector('.product-name');
    if (name) {
      const productName = name.textContent.toLowerCase();
      if (productName.includes(searchTerm.toLowerCase())) {
        product.style.display = 'block';
      } else {
        product.style.display = 'none';
      }
    }
  });
  
  categories.forEach(category => {
    const title = category.querySelector('h3').textContent.toLowerCase();
    const description = category.querySelector('p').textContent.toLowerCase();
    const productItems = Array.from(category.querySelectorAll('.category-products li'))
      .map(li => li.textContent.toLowerCase()).join(' ');
    
    if (title.includes(searchTerm.toLowerCase()) || 
        description.includes(searchTerm.toLowerCase()) || 
        productItems.includes(searchTerm.toLowerCase())) {
      category.style.display = 'block';
    } else {
      category.style.display = 'none';
    }
  });
}

// Lazy loading for images (utility function)
const imageObserver = new IntersectionObserver((entries, observer) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const img = entry.target;
      if (img.dataset.src) {
        img.src = img.dataset.src;
        img.classList.remove('lazy');
        imageObserver.unobserve(img);
      }
    }
  });
});

// Initialize lazy loading for any images with data-src attribute
document.querySelectorAll('img[data-src]').forEach(img => {
  imageObserver.observe(img);
});

// Utility function to show notifications
function showNotification(message, type = 'success') {
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.style.cssText = `
    position: fixed;
    top: 100px;
    right: 20px;
    background: ${type === 'success' ? '#10b981' : '#ef4444'};
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
    notification.style.animation = 'slideOutRight 0.3s ease-in';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// Add CSS animations for notifications
const notificationStyle = document.createElement('style');
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
