<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <base href="{{ asset('assets') . '/' }}">
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Connexion</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="css/app.min.css">
    <link rel="stylesheet" href="bundles/bootstrap-social/bootstrap-social.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/components.css">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="css/custom.css">
    <link rel='shortcut icon' type='image/x-icon' href='img/favicon.ico' />
    <style>
        body, html {
            height: 100%;
        }
        #app {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .section {
            display: flex;
            flex: 1;
            align-items: center;
            justify-content: center;
        }
        .card {
            margin-top: -50px; /* Ajustement visuel optionnel */
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.1);
        }
        /* Styles pour le bouton de visibilité */
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6c757d;
        }
        .password-input-group {
            position: relative;
        }
    </style>
</head>

<body>
  <div class="loader"></div>
  <div id="app">
    <section class="section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
            <div class="card card-primary">
              <div class="card-header text-center">
                <h4>Connexion</h4>
              </div>
              <div class="card-body">
                <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
                  @csrf
                  <div class="form-group">
                    <label for="email">Adresse Email</label>
                    <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
                    <div class="invalid-feedback">
                      Veuillez saisir votre email
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="d-block">
                      <label for="password" class="control-label">Mot de passe</label>
                      <div class="float-right">
                        <a href="{{ route('password.request') }}" class="text-small">
                          Mot de passe oublié ?
                        </a>
                      </div>
                    </div>
                    <div class="password-input-group">
                      <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                      <button type="button" class="password-toggle" id="passwordToggle">
                        <i class="fas fa-eye"></i>
                      </button>
                      <div class="invalid-feedback">
                        Veuillez saisir votre mot de passe
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                      <label class="custom-control-label" for="remember-me">Se souvenir de moi</label>
                    </div>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Se connecter
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- General JS Scripts -->
  <script src="js/app.min.js"></script>
  <!-- JS Libraies -->
  <!-- Page Specific JS File -->
  <!-- Template JS File -->
  <script src="js/scripts.js"></script>
  <!-- Custom JS File -->
  <script src="js/custom.js"></script>
  
  <script>
    // Script pour la fonctionnalité de basculement de visibilité du mot de passe
    document.addEventListener('DOMContentLoaded', function() {
      const passwordInput = document.getElementById('password');
      const passwordToggle = document.getElementById('passwordToggle');
      
      passwordToggle.addEventListener('click', function() {
        // Basculer le type de l'input entre password et text
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Changer l'icône
        const icon = this.querySelector('i');
        if (type === 'password') {
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        } else {
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        }
      });
    });
  </script>
</body>
</html>