       </main>
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-logo">
                <img src="/libreria/img/logo2.jpg" alt="Logo Librería Online" class="footer-logo-img">
            </div>
            
            <div class="footer-right">
                <p class="footer-copyright">&copy; <?php echo date('Y'); ?> Librería Online. Todos los derechos reservados.</p>
                
                <nav class="footer-nav">
                    <ul class="footer-nav-list">
                        <li class="footer-nav-item"><a href="#" class="footer-link" id="footerTerms">Términos y condiciones</a></li>
                        <li class="footer-nav-item"><a href="#" class="footer-link" id="footerPrivacy">Política de privacidad</a></li>
                        <li class="footer-nav-item"><a href="mailto:contacto@libreriaonline.com" class="footer-link">Contacto</a></li>
                    </ul>
                </nav>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="footer-modal" id="footerModal">
            <div class="modal-container">
                <span class="modal-close">&times;</span>
                <h3 class="modal-title">Términos y Condiciones de Librería Online</h3>
                <div class="modal-body">
                    <h4>1. Aceptación de los Términos</h4>
                    <p>Al acceder y utilizar nuestro sitio web, usted acepta cumplir con estos términos y condiciones.</p>
                    
                    <h4>2. Propiedad Intelectual</h4>
                    <p>Todos los contenidos de este sitio, incluyendo textos, gráficos, logotipos, son propiedad exclusiva de Librería Online.</p>
                    
                    <h4>3. Compras y Pagos</h4>
                    <p>Los precios están sujetos a cambio sin previo aviso. Aceptamos todas las tarjetas de crédito principales.</p>
                    
                    <h4>4. Envíos y Devoluciones</h4>
                    <p>Los plazos de entrega son estimados. Las devoluciones deben realizarse dentro de los 30 días posteriores a la compra.</p>
                    
                    <h4>5. Limitación de Responsabilidad</h4>
                    <p>Librería Online no será responsable por daños indirectos o consecuenciales derivados del uso del sitio.</p>
                </div>
            </div>
        </div>
    </footer>

    <style>
        /* Footer Styles */
        .site-footer {
            background-color: #ffffff;
            color: #333333;
            padding: 30px 0;
            margin-top: 50px;
            border-top: 1px solid #eaeaea;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-logo-img {
            height: 100px;
            width: auto;
        }
        
        .footer-right {
            text-align: right;
        }
        
        .footer-copyright {
            margin: 10px 0;
            font-size: 0.9em;
            color: #666666;
        }
        
        .footer-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: flex-end;
            gap: 20px;
        }
        
        .footer-link {
            color: #444444;
            text-decoration: none;
            font-size: 0.9em;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .footer-link:hover {
            color: #007bff;
            text-decoration: underline;
        }
        
        /* Modal Styles */
        .footer-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .modal-container {
            background-color: #ffffff;
            margin: 50px auto;
            padding: 30px;
            border-radius: 8px;
            width: 85%;
            max-width: 700px;
            position: relative;
            color: #333333;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        }
        
        .modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #999999;
            transition: color 0.3s;
        }
        
        .modal-close:hover {
            color: #333333;
        }
        
        .modal-title {
            color: #2c3e50;
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #eeeeee;
        }
        
        .modal-body h4 {
            color: #2980b9;
            margin: 25px 0 10px 0;
        }
        
        .modal-body p {
            margin-bottom: 15px;
            line-height: 1.6;
            color: #555555;
        }
        
        @media (max-width: 768px) {
            .footer-container {
                flex-direction: column;
                text-align: center;
            }
            
            .footer-right {
                text-align: center;
                margin-top: 20px;
            }
            
            .footer-nav-list {
                justify-content: center;
            }
            
            .modal-container {
                width: 90%;
                padding: 20px;
                margin: 20px auto;
            }
        }
    </style>

    <script>
        // Footer Modal Functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Terms Modal
            const termsBtn = document.getElementById('footerTerms');
            const modal = document.getElementById('footerModal');
            const closeBtn = document.querySelector('.modal-close');
            
            if(termsBtn && modal) {
                termsBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    modal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                });
            }
            
            if(closeBtn && modal) {
                closeBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                });
            }
            
            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                if(e.target === modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
            
            // Privacy link (you can add similar functionality)
            const privacyBtn = document.getElementById('footerPrivacy');
            if(privacyBtn) {
                privacyBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // You can implement similar modal for privacy policy
                    alert("Aquí iría la política de privacidad");
                });
            }
        });
    </script>

    <script src="/libreria/assets/js/scripts.js"></script>
</body>
</html>