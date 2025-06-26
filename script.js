// Añade este console.log al PRINCIPIO de tu archivo script.js para verificar que el archivo se carga
console.log("script.js - Archivo cargado."); 

// TODO el código de tu script.js debe ir dentro de esta función DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    console.log("script.js - DOMContentLoaded disparado."); 

    // Menu toggle para móviles - Versión mejorada
    const menuToggle = document.querySelector('.menu-toggle');
    const navbar = document.querySelector('.navbar');

    if (menuToggle && navbar) {
        console.log("script.js - Menu toggle y navbar encontrados.");
        menuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            navbar.classList.toggle('active');
            console.log("script.js - Menu toggle clickeado.");
        });

        document.querySelectorAll('.navbar a').forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.classList.remove('active');
                navbar.classList.remove('active');
            });
        });
    } else {
        console.log("script.js - Menu toggle o navbar NO encontrados.");
    }

    // Scroll suave mejorado
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== 'javascript:void(0)') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    console.log("script.js - Scroll suave inicializado.");

    // Botón "Volver arriba"
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });
        console.log("script.js - Botón 'Volver arriba' inicializado.");
    } else {
        console.log("script.js - Botón 'Volver arriba' NO encontrado.");
    }

    // Filtrado de categorías
    const filterButtons = document.querySelectorAll('.filter-btn');
    if (filterButtons.length > 0) {
        console.log(`script.js - ${filterButtons.length} botones de filtro encontrados.`);
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const category = this.dataset.category;
                const posts = document.querySelectorAll('.post');
                posts.forEach(post => {
                    if (category === 'all' || post.dataset.category === category) {
                        post.style.display = 'block';
                    } else {
                        post.style.display = 'none';
                    }
                });
                console.log(`script.js - Filtrando por categoría: ${category}`);
            });
        });
    } else {
        console.log("script.js - Botones de filtro NO encontrados.");
    }

    // Formularios (ej: Contacto y Newsletter)
    const initForm = (formId, successMessage) => {
        const form = document.getElementById(formId);
        if (form) {
            console.log(`script.js - Formulario ${formId} inicializado.`);
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const messageElement = this.querySelector('.form-message') ||
                                     this.querySelector('.newsletter-message');
                let isValid = true;
                this.querySelectorAll('[required]').forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.classList.add('error');
                    } else {
                        input.classList.remove('error');
                    }
                });
                if (isValid) {
                    if (messageElement) {
                        messageElement.textContent = successMessage;
                        messageElement.style.color = '#2ecc71';
                        messageElement.classList.add('visible');
                    }
                    setTimeout(() => {
                        this.reset();
                        if (messageElement) {
                            messageElement.textContent = '';
                            messageElement.classList.remove('visible');
                        }
                    }, 3000);
                } else if (messageElement) {
                    messageElement.textContent = 'Por favor completa todos los campos requeridos';
                    messageElement.style.color = '#e74c3c';
                    messageElement.classList.add('visible');
                }
            });
        } else {
            console.log(`script.js - Formulario ${formId} NO encontrado.`);
        }
    };

    initForm('newsletterForm', '¡Gracias por suscribirte!');
    initForm('contactForm', '¡Mensaje enviado con éxito!');

    // Bloque duplicado para el formulario de newsletter, puedes eliminarlo si 'initForm' es suficiente
    // Si necesitas validación de email más específica fuera de 'required', entonces este bloque es útil.
    // Solo si no lo has eliminado y es necesario:
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm && !newsletterForm.dataset.initialized) {
        newsletterForm.dataset.initialized = 'true';
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = document.getElementById('newsletter-email');
            const email = emailInput ? emailInput.value.trim() : '';
            const messageElement = document.getElementById('newsletterMessage');
            if (!messageElement) return;
            if (!email) {
                messageElement.textContent = 'Por favor, ingresa tu email.';
                messageElement.style.color = '#e74c3c';
                messageElement.classList.add('visible');
                return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                messageElement.textContent = 'Por favor, ingresa un email válido.';
                messageElement.style.color = '#e74c3c';
                messageElement.classList.add('visible');
                return;
            }
            messageElement.textContent = `¡Gracias por suscribirte con el email ${email}!`;
            messageElement.style.color = '#2ecc71';
            messageElement.classList.add('visible');
            setTimeout(() => {
                this.reset();
                messageElement.textContent = '';
                messageElement.classList.remove('visible');
            }, 3000);
        });
        console.log("script.js - Validación de Newsletter (bloque adicional) inicializada.");
    }


    // === INICIO DE LA SECCIÓN CORREGIDA PARA EL MENÚ DESPLEGABLE DEL USUARIO ===
    // ESTO SE MUEVE DENTRO DE DOMContentLoaded
    const userMenu = document.querySelector('.user-menu');
    if (userMenu) {
        console.log("script.js - Elemento .user-menu encontrado para el dropdown.");
        const userLink = userMenu.querySelector('a:first-child');
        const dropdown = userMenu.querySelector('.dropdown');

        if (userLink && dropdown) {
            console.log("script.js - Enlace de usuario y dropdown encontrados.");
            userLink.addEventListener('click', (e) => {
                e.preventDefault();
                dropdown.classList.toggle('active');
                userMenu.classList.toggle('dropdown-open'); // Clase opcional para estilos adicionales
                console.log("script.js - Clic en enlace de usuario. Toggle 'active'/'dropdown-open'.");
            });

            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target) && dropdown.classList.contains('active')) {
                    dropdown.classList.remove('active');
                    userMenu.classList.remove('dropdown-open');
                    console.log("script.js - Clic fuera del menú, dropdown cerrado.");
                }
            });

            dropdown.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    dropdown.classList.remove('active');
                    userMenu.classList.remove('dropdown-open');
                    console.log("script.js - Clic en elemento del dropdown, cerrado.");
                });
            });
        } else {
            console.log("script.js - userLink o dropdown NO encontrados dentro de .user-menu.");
        }
    } else {
        console.log("script.js - Elemento .user-menu NO encontrado para el dropdown.");
    }
    // === FIN DE LA SECCIÓN CORREGIDA PARA EL MENÚ DESPLEGABLE DEL USUARIO ===


    // Cargar más posts (simulación)
    const loadMoreBtn = document.querySelector('.btn-outline');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = loadMoreBtn.getAttribute('href');
        });
        console.log("script.js - Botón 'Cargar más' inicializado.");
    } else {
        console.log("script.js - Botón 'Cargar más' NO encontrado.");
    }

    // Efectos de scroll para elementos (AOS y fallback)
    // Asegurarse de que AOS se inicializa una sola vez y dentro de DOMContentLoaded
    if (typeof AOS !== 'undefined' && !window.aosInitialized) {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 120
        });
        window.aosInitialized = true; // Marca AOS como inicializado
        console.log("script.js - AOS inicializado.");
    } else if (typeof AOS === 'undefined') { // Fallback si AOS no está presente
        const animateOnScroll = () => {
            const elements = document.querySelectorAll('[data-aos]');
            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.3;
                if (elementPosition < screenPosition) {
                    element.classList.add('aos-animate');
                }
            });
        };
        animateOnScroll();
        window.addEventListener('scroll', animateOnScroll);
        console.log("script.js - Fallback de animación por scroll inicializado (AOS no encontrado).");
    }

    // Precarga de imágenes cuando están en el viewport
    const lazyLoadImages = () => {
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src || img.src; // Usa dataset.src si existe, sino src
                        img.removeAttribute('loading');
                        observer.unobserve(img);
                    }
                });
            });
            lazyImages.forEach(img => imageObserver.observe(img));
            console.log("script.js - Lazy load de imágenes iniciado con IntersectionObserver.");
        } else {
            console.log("script.js - IntersectionObserver no soportado, lazy load de imágenes no activo.");
        }
    };
    window.addEventListener('load', lazyLoadImages);
}); // <-- CIERRE DE document.addEventListener('DOMContentLoaded', function() {