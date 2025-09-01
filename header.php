<?php
/**
 * Header moderne - Design épuré inspiré de Chéri Bibi
 * @package Le Margo
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<!-- Preconnect pour optimiser les performances -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

	<!-- Balises meta essentielles -->
	<meta name="author" content="Le Margo">
	
	<!-- Open Graph -->
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?php echo home_url(); ?>">
	<meta property="og:site_name" content="<?php bloginfo('name'); ?>">

	<!-- Favicon -->
	<link rel="icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.ico">

	<!-- Debug Site Kit -->
	<?php 
	if (function_exists('googlesitekit_activation_notice')) {
		echo "<!-- Site Kit est activé -->\n";
	} else {
		echo "<!-- Site Kit n'est pas activé -->\n";
	}
	?>

	<!-- 
	CODE GA4 MANUEL DE TEST (à décommenter si nécessaire) :
	
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-VOTRE-ID-ICI"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', 'G-VOTRE-ID-ICI');
	</script>
	-->

	<?php wp_head(); ?>
	
	<!-- Test de présence de Google Analytics -->
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Vérifier si gtag est disponible
		if (typeof gtag === 'function') {
			console.log('✅ gtag est chargé et fonctionnel');
			
			// Envoyer un événement de test
			gtag('event', 'page_view_test', {
				'custom_parameter': 'test_from_lemargo'
			});
		} else {
			console.log('❌ gtag n\'est pas chargé');
		}
		
		// Vérifier si Google Analytics est détecté
		if (typeof ga === 'function') {
			console.log('✅ ga (Universal Analytics) détecté');
		}
		
		// Vérifier si dataLayer existe
		if (typeof dataLayer !== 'undefined') {
			console.log('✅ dataLayer existe:', dataLayer);
			
			// Chercher l'ID de mesure dans dataLayer
			dataLayer.forEach(function(item, index) {
				if (item && item[0] === 'config' && item[1] && item[1].startsWith('G-')) {
					console.log('🎯 ID de mesure GA4 trouvé:', item[1]);
				}
			});
		} else {
			console.log('❌ dataLayer n\'existe pas');
		}
		
		// Vérifier les scripts GA4 chargés
		const scripts = document.querySelectorAll('script[src*="googletagmanager.com"]');
		if (scripts.length > 0) {
			console.log('✅ Scripts GA4 détectés:', scripts.length);
			scripts.forEach(script => console.log('Script:', script.src));
		} else {
			console.log('❌ Aucun script GA4 détecté');
		}
		
		// Test de tracking manuel après 2 secondes
		setTimeout(function() {
			if (typeof gtag === 'function') {
				console.log('🧪 Test de tracking manuel...');
				gtag('event', 'manual_test', {
					'event_category': 'Debug',
					'event_label': 'Manual Test from Le Margo',
					'custom_parameter': 'test_' + Date.now()
				});
				console.log('✅ Événement de test envoyé');
				
				// Vérifier si les requêtes sont bloquées
				setTimeout(function() {
					fetch('https://www.google-analytics.com/g/collect?v=2&tid=G-TEST&cid=test', {
						method: 'POST',
						mode: 'no-cors'
					}).then(function() {
						console.log('✅ Requêtes GA4 non bloquées');
					}).catch(function(error) {
						console.log('❌ Requêtes GA4 bloquées:', error);
					});
				}, 1000);
			}
		}, 2000);
	});
	</script>
	
	<style>
		/* Header ultra-minimaliste */
		.site-header {
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			z-index: 1000;
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(10px);
			padding: var(--spacing-md) 0;
			transition: var(--transition);
		}
		
		.site-branding {
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		
		.site-logo a {
			font-size: 1.2rem;
			font-weight: var(--font-weight-light);
			letter-spacing: 2px;
			text-transform: uppercase;
		}
		
		.main-navigation ul {
			display: flex;
			list-style: none;
			gap: var(--spacing-lg);
			margin: 0;
			padding: 0;
		}
		
		.main-navigation a {
			font-size: 0.85rem;
			font-weight: var(--font-weight-normal);
			letter-spacing: 1px;
			text-transform: uppercase;
		}
		
		/* Hamburger menu */
		.mobile-menu-toggle {
			display: none;
			background: none;
			border: none;
			cursor: pointer;
			padding: var(--spacing-xs);
		}
		
		.hamburger-line {
			display: block;
			width: 18px;
			height: 1px;
			background: var(--color-black);
			margin: 4px 0;
			transition: var(--transition);
		}
		
		.mobile-menu-toggle.active .hamburger-line:nth-child(1) {
			transform: rotate(45deg) translate(4px, 4px);
		}
		
		.mobile-menu-toggle.active .hamburger-line:nth-child(2) {
			opacity: 0;
		}
		
		.mobile-menu-toggle.active .hamburger-line:nth-child(3) {
			transform: rotate(-45deg) translate(5px, -5px);
		}
		
		@media (max-width: 768px) {
			.mobile-menu-toggle {
				display: block;
			}
			
			.main-navigation {
				display: none;
				position: absolute;
				top: 100%;
				left: 0;
				right: 0;
				background: var(--color-white);
				padding: var(--spacing-md);
				box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
			}
			
			.main-navigation.active {
				display: block;
			}
			
			.main-navigation ul {
				flex-direction: column;
				gap: var(--spacing-md);
				text-align: center;
			}
		}
		
		/* Animation du header au scroll */
		.site-header.scrolled {
			background: rgba(255, 255, 255, 0.98);
			box-shadow: 0 1px 20px rgba(0, 0, 0, 0.05);
		}
        .language-switcher {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-right: var(--spacing-lg);
        }
        .language-switcher a {
            color: var(--color-text-light);
            text-decoration: none;
        }
        .language-switcher .current-lang {
            font-weight: var(--font-weight-bold);
            color: var(--color-text);
        }
        .language-switcher .lang-separator {
            display: none;
        }

        .flag-icon {
            width: 24px;
            height: 16px;
            display: inline-block;
            vertical-align: middle;
            transition: var(--transition);
            border-radius: 2px;
            box-shadow: 0 0 2px rgba(0,0,0,0.15);
        }

        .language-switcher a .flag-icon {
            filter: grayscale(1);
            opacity: 0.6;
        }

        .language-switcher a:hover .flag-icon {
            filter: grayscale(0);
            opacity: 1;
        }

        .current-lang .flag-icon {
            filter: grayscale(0);
        }

        .header-right-group {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
        }

        @media (max-width: 768px) {
            .header-right-group {
                gap: var(--spacing-md);
            }
            .main-navigation {
                display: none;
            }
            .language-switcher {
                margin-right: 32px;
            }
        }
	</style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">

	<header class="site-header">
		<div class="container">
			<div class="site-branding">
				<div class="site-logo">
					<?php
					if (has_custom_logo()) {
						the_custom_logo();
					} else {
						?>
						<a href="<?php echo esc_url(home_url('/')); ?>" rel="home" style="text-decoration: none; color: inherit; font-size: 1.2rem; font-weight: 300; letter-spacing: 2px; text-transform: uppercase;">
							<?php bloginfo('name'); ?>
						</a>
						<?php
					}
					?>
				</div>

                <div class="header-right-group">
                    <?php if (function_exists('the_language_switcher')) {
                        the_language_switcher();
                    } ?>

                    <nav class="main-navigation" role="navigation">
                        <ul id="primary-menu">
                            <li><a href="<?php echo home_url('/reserver/'); ?>"><?php _e('Réservation', 'le-margo'); ?></a></li>
                            <li><a href="https://instagram.com/lemargoeymet" target="_blank"><?php _e('Instagram', 'le-margo'); ?></a></li>
                            <li><a href="mailto:sasdamaeymet@gmail.com"><?php _e('Mail', 'le-margo'); ?></a></li>
                        </ul>
                    </nav>

                    <button class="mobile-menu-toggle" aria-label="<?php _e('Menu', 'le-margo'); ?>" aria-expanded="false">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </button>
                </div>
			</div>
		</div>
	</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle mobile menu
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const navigation = document.querySelector('.main-navigation');
    
    if (mobileToggle && navigation) {
        mobileToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            this.setAttribute('aria-expanded', !isExpanded);
            this.classList.toggle('active');
            navigation.classList.toggle('active');
        });
    }
    
    // Header scroll effect
    const header = document.querySelector('.site-header');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Fermer le menu mobile lors du clic sur un lien
    const mobileLinks = document.querySelectorAll('.main-navigation a');
    mobileLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                mobileToggle.classList.remove('active');
                navigation.classList.remove('active');
                mobileToggle.setAttribute('aria-expanded', 'false');
            }
        });
    });
});
</script> 